<?php
// application/library/services/UnifiedCourseAccess.php

class UnifiedCourseAccess
{
    /**
     * Start or resume a course - IMPROVED VERSION
     */
    public static function startCourse(int $userId, int $courseId): array
    {
        $db = FatApp::getDb();
        
        // 1. Validate user has access via subscription
        if (!self::hasSubscriptionAccess($userId, $courseId)) {
            throw new Exception(Label::getLabel('LBL_COURSE_ACCESS_DENIED_NO_SUBSCRIPTION'));
        }

        // 2. Get active subscription
        $subscription = UserSubscription::getActiveByUser($userId);
        if (!$subscription) {
            throw new Exception(Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'));
        }

        // 3. Track access
        self::trackCourseAccess($userId, $courseId, $subscription['usubs_id']);

        // 4. Get or create progress record (now unique per user/course)
        $progress = self::getOrCreateProgress($userId, $courseId, $subscription['usubs_id']);
        
        return [
            'progress_id' => $progress['subpro_id'],
            'progress_type' => 'subscription',
            'course_id' => $courseId,
            'user_id' => $userId,
            'subscription_id' => $subscription['usubs_id'],
            'progress_percent' => $progress['subpro_progress'] / 100, // Convert basis points to percentage
            'covered_lectures' => $progress['subpro_covered_count']
        ];
    }

    /**
     * Check subscription access to course - IMPROVED with JOIN
     */
    public static function hasSubscriptionAccess(int $userId, int $courseId): bool
    {
        $db = FatApp::getDb();
        
        // Get course subject
        $course = $db->fetch($db->query(
            "SELECT course_subject_id FROM tbl_courses WHERE course_id = ? AND course_deleted IS NULL", 
            [$courseId]
        ));
        
        if (!$course || empty($course['course_subject_id'])) {
            return false;
        }

        $subjectId = (int)$course['course_subject_id'];

        // IMPROVED: Use junction table instead of FIND_IN_SET
        $query = "
            SELECT 1
            FROM tbl_user_subscriptions us
            INNER JOIN tbl_user_subscription_subjects uss ON us.usubs_id = uss.usubs_id
            WHERE us.usubs_user_id = ?
            AND us.usubs_status = 'active'
            AND us.usubs_end_date >= NOW()
            AND uss.subject_id = ?
            LIMIT 1
        ";
        
        $result = $db->fetch($db->query($query, [$userId, $subjectId]));
        return !empty($result);
    }

    /**
     * Track course access for analytics - IMPROVED with revocation support
     */
    private static function trackCourseAccess(int $userId, int $courseId, int $subscriptionId): void
    {
        $db = FatApp::getDb();
        
        $existing = $db->fetch($db->query("
            SELECT subacc_id, subacc_access_count, subacc_revoked_at
            FROM tbl_subscription_course_access 
            WHERE subacc_user_id = ? AND subacc_course_id = ? AND subacc_subscription_id = ?
        ", [$userId, $courseId, $subscriptionId]));

        $now = date('Y-m-d H:i:s');
        
        if ($existing) {
            if ($existing['subacc_revoked_at']) {
                // Reactivate revoked access
                $db->updateFromArray('tbl_subscription_course_access', [
                    'subacc_last_accessed' => $now,
                    'subacc_access_count' => $existing['subacc_access_count'] + 1,
                    'subacc_revoked_at' => null
                ], [
                    'smt' => 'subacc_id = ?',
                    'vals' => [$existing['subacc_id']]
                ]);
            } else {
                // Update existing active access
                $db->updateFromArray('tbl_subscription_course_access', [
                    'subacc_last_accessed' => $now,
                    'subacc_access_count' => $existing['subacc_access_count'] + 1
                ], [
                    'smt' => 'subacc_id = ?',
                    'vals' => [$existing['subacc_id']]
                ]);
            }
        } else {
            // Create new access record
            $db->insertFromArray('tbl_subscription_course_access', [
                'subacc_user_id' => $userId,
                'subacc_subscription_id' => $subscriptionId,
                'subacc_course_id' => $courseId,
                'subacc_accessed' => $now,
                'subacc_last_accessed' => $now,
                'subacc_access_count' => 1
            ]);
        }
    }

    /**
     * Get or create progress record - IMPROVED with unique user/course and optimistic locking
     */
    private static function getOrCreateProgress(int $userId, int $courseId, ?int $subscriptionId): array
    {
        $db = FatApp::getDb();
        
        // Check existing progress (now unique per user/course)
        $progress = $db->fetch($db->query("
            SELECT * FROM tbl_subscription_progress 
            WHERE subpro_user_id = ? AND subpro_course_id = ?
        ", [$userId, $courseId]));

        $now = date('Y-m-d H:i:s');
        
        if ($progress) {
            // Update subscription ID if different and refresh timestamp
            if ($subscriptionId && (int)$progress['subpro_subscription_id'] !== $subscriptionId) {
                $db->updateFromArray('tbl_subscription_progress', [
                    'subpro_subscription_id' => $subscriptionId,
                    'subpro_updated' => $now,
                    'subpro_version' => $progress['subpro_version'] + 1
                ], [
                    'smt' => 'subpro_id = ? AND subpro_version = ?',
                    'vals' => [$progress['subpro_id'], $progress['subpro_version']]
                ]);
                
                // Reload progress data
                $progress = $db->fetch($db->query("
                    SELECT * FROM tbl_subscription_progress 
                    WHERE subpro_id = ?
                ", [$progress['subpro_id']]));
            }
            
            return $progress;
        }

        // Create new progress record
        $progressData = [
            'subpro_user_id' => $userId,
            'subpro_course_id' => $courseId,
            'subpro_subscription_id' => $subscriptionId,
            'subpro_started' => $now,
            'subpro_status' => 1, // in progress
            'subpro_progress' => 0, // basis points (0.00%)
            'subpro_current_lecture' => 0,
            'subpro_covered_lectures' => '',
            'subpro_covered_count' => 0,
            'subpro_created' => $now,
            'subpro_updated' => $now,
            'subpro_version' => 1
        ];

        if ($db->insertFromArray('tbl_subscription_progress', $progressData)) {
            $progressData['subpro_id'] = $db->getInsertId();
            return $progressData;
        }

        throw new Exception('Failed to create progress record');
    }

    /**
     * Update course progress - IMPROVED with optimistic locking
     */
    public static function updateProgress(int $progressId, array $data, int $currentVersion): bool
    {
        $db = FatApp::getDb();
        
        $data['subpro_updated'] = date('Y-m-d H:i:s');
        $data['subpro_version'] = $currentVersion + 1;
        
        $result = $db->updateFromArray('tbl_subscription_progress', $data, [
            'smt' => 'subpro_id = ? AND subpro_version = ?',
            'vals' => [$progressId, $currentVersion]
        ]);
        
        if ($result && $db->getAffectedRows() === 0) {
            throw new Exception('Progress was updated by another process. Please refresh.');
        }
        
        return $result;
    }

    /**
     * Mark lecture as completed - IMPROVED with concurrency control
     */
    public static function markLectureComplete(int $progressId, int $lectureId, bool $complete = true): bool
    {
        $db = FatApp::getDb();
        
        // Get current progress with version for optimistic locking
        $progress = self::getProgress($progressId);
        if (!$progress) {
            return false;
        }

        $coveredLectures = [];
        if (!empty($progress['subpro_covered_lectures'])) {
            $coveredLectures = explode(',', $progress['subpro_covered_lectures']);
            $coveredLectures = array_map('intval', $coveredLectures);
        }

        $coveredCount = $progress['subpro_covered_count'];
        
        if ($complete) {
            // Add to covered if not already
            if (!in_array($lectureId, $coveredLectures)) {
                $coveredLectures[] = $lectureId;
                $coveredCount++;
            }
        } else {
            // Remove from covered
            $key = array_search($lectureId, $coveredLectures);
            if ($key !== false) {
                unset($coveredLectures[$key]);
                $coveredCount = max(0, $coveredCount - 1);
            }
        }

        $updateData = [
            'subpro_covered_lectures' => implode(',', $coveredLectures),
            'subpro_covered_count' => $coveredCount,
            'subpro_current_lecture' => $complete ? $lectureId : $progress['subpro_current_lecture']
        ];

        // Calculate progress percentage (in basis points)
        $totalLectures = self::getCourseLectureCount($progress['subpro_course_id']);
        if ($totalLectures > 0) {
            $progressPercent = (int)round(($coveredCount / $totalLectures) * 10000);
            $updateData['subpro_progress'] = min(10000, $progressPercent);
            
            // Auto-complete if 100%
            if ($progressPercent >= 10000) {
                $updateData['subpro_status'] = 2; // completed
                $updateData['subpro_completed'] = date('Y-m-d H:i:s');
            }
        }

        return self::updateProgress($progressId, $updateData, $progress['subpro_version']);
    }

    /**
     * Get course lecture count for progress calculation
     */
    private static function getCourseLectureCount(int $courseId): int
    {
        $db = FatApp::getDb();
        $result = $db->fetch($db->query("
            SELECT COUNT(*) as lecture_count 
            FROM tbl_lectures 
            WHERE lecture_course_id = ? AND lecture_deleted IS NULL
        ", [$courseId]));
        
        return $result ? (int)$result['lecture_count'] : 0;
    }

    /**
     * Revoke course access when subscription ends
     */
    public static function revokeAccess(int $userId, int $subscriptionId): void
    {
        $db = FatApp::getDb();
        $now = date('Y-m-d H:i:s');
        
        // Mark access records as revoked
        $db->updateFromArray('tbl_subscription_course_access', [
            'subacc_revoked_at' => $now
        ], [
            'smt' => 'subacc_user_id = ? AND subacc_subscription_id = ? AND subacc_revoked_at IS NULL',
            'vals' => [$userId, $subscriptionId]
        ]);
        
        // Note: Progress records are kept for continuity if user resubscribes
    }

    /**
     * Get user's enrolled courses with progress
     */
    public static function getUserCourses(int $userId): array
    {
        $db = FatApp::getDb();
        
        $query = "
            SELECT 
                c.course_id,
                c.course_title,
                c.course_subtitle,
                c.course_image,
                c.course_ratings,
                c.course_students,
                c.course_lectures,
                c.course_duration,
                sp.subpro_id as progress_id,
                ROUND(sp.subpro_progress / 100, 2) as progress_percent, -- Convert basis points to percentage
                sp.subpro_status as progress_status,
                sp.subpro_started as started_date,
                sp.subpro_covered_count as completed_lectures,
                us.usubs_id as subscription_id,
                us.usubs_end_date as subscription_ends
            FROM tbl_subscription_progress sp
            JOIN tbl_courses c ON sp.subpro_course_id = c.course_id
            LEFT JOIN tbl_user_subscriptions us ON sp.subpro_subscription_id = us.usubs_id
            WHERE sp.subpro_user_id = ?
            ORDER BY sp.subpro_updated DESC
        ";
        
        return $db->fetchAll($db->query($query, [$userId]));
    }

    /**
     * Get progress data
     */
    public static function getProgress(int $progressId): ?array
    {
        $db = FatApp::getDb();
        $progress = $db->fetch($db->query("
            SELECT * FROM tbl_subscription_progress WHERE subpro_id = ?
        ", [$progressId]));
        
        if ($progress) {
            // Convert basis points to percentage for external use
            $progress['progress_percent'] = $progress['subpro_progress'] / 100;
        }
        
        return $progress;
    }

    /**
     * Check if user can currently access a course (active subscription required)
     */
    public static function canAccessCourse(int $userId, int $courseId): bool
    {
        return self::hasSubscriptionAccess($userId, $courseId);
    }
}