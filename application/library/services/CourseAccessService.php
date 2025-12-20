<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class CourseAccessService
{
    /**
     * Check if user can access course (both order-based and subscription-based)
     */
    public static function canAccessCourse(int $userId, int $courseId): bool
    {
        // 1. Check traditional order-based access
        $orderAccess = self::hasOrderAccess($userId, $courseId);
        if ($orderAccess) {
            return true;
        }

        // 2. Check subscription-based access
        return self::hasSubscriptionAccess($userId, $courseId);
    }

    /**
     * Traditional order-based access check
     */
    private static function hasOrderAccess(int $userId, int $courseId): bool
    {
        $srch = new SearchBase(OrderCourse::DB_TBL, 'oc');
        $srch->addCondition('oc.ordcrs_user_id', '=', $userId);
        $srch->addCondition('oc.ordcrs_course_id', '=', $courseId);
        $srch->addCondition('oc.ordcrs_status', 'IN', [
            OrderCourse::STATUS_ACTIVE, 
            OrderCourse::STATUS_COMPLETED
        ]);
        $srch->setPageSize(1);
        $srch->doNotCalculateRecords();
        
        return (bool)FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Subscription-based access check - FIXED VERSION
     */
    public static function hasSubscriptionAccess(int $userId, int $courseId): bool
{
    $db = FatApp::getDb();
    
    // ADD DEBUG LOGGING
    // $debugLog = "=== hasSubscriptionAccess Debug ===\n";
    // $debugLog .= "User ID: $userId, Course ID: $courseId\n";
    
    // Method 1: Using SearchBase (most reliable in YoCoach)
    $courseSrch = new SearchBase(Course::DB_TBL, 'c');
    $courseSrch->addCondition('c.course_id', '=', $courseId);
    $courseSrch->addMultipleFields(['c.course_subject_id']);
    $courseSrch->setPageSize(1);
    $courseSrch->doNotCalculateRecords();
    
    $courseData = $db->fetch($courseSrch->getResultSet());
    
    $debugLog .= "Course Data: " . print_r($courseData, true) . "\n";
    
    if (!$courseData || empty($courseData['course_subject_id'])) {
        $debugLog .= "❌ Course not found or no subject_id\n";
        return false;
    }
    
    $subjectId = (int)$courseData['course_subject_id'];
    $debugLog .= "Course Subject ID: $subjectId\n";
    
    // Check if user has active subscription for this subject
    $subSrch = new SearchBase('tbl_user_subscriptions', 'us');
    $subSrch->addCondition('us.usubs_user_id', '=', $userId);
    $subSrch->addCondition('us.usubs_status', '=', 'active');
    $subSrch->addCondition('us.usubs_end_date', '>=', date('Y-m-d H:i:s'));
    $subSrch->addDirectCondition("FIND_IN_SET('{$subjectId}', us.usubs_subject_ids)");
    $subSrch->setPageSize(1);
    $subSrch->doNotCalculateRecords();
    
    $subscriptionData = $db->fetch($subSrch->getResultSet());
    $debugLog .= "Subscription Data: " . print_r($subscriptionData, true) . "\n";
    
    $result = (bool)$subscriptionData;
    $debugLog .= "Final Result: " . ($result ? "TRUE" : "FALSE") . "\n";
    
    return $result;
}

/**
 * Write debug log to file
 */

    /**
     * Alternative method using SearchBase only (more reliable)
     */
    public static function hasSubscriptionAccessAlternative(int $userId, int $courseId): bool
    {
        $db = FatApp::getDb();
        
        // Get course subject
        $courseSubject = $db->fetch($db->query(
            "SELECT course_subject_id FROM tbl_courses WHERE course_id = ?", 
            [$courseId]
        ));
        
        if (!$courseSubject) {
            return false;
        }
        
        $subjectId = (int)$courseSubject['course_subject_id'];
        
        // Get user's active subscription
        $sub = UserSubscription::getActiveByUser($userId);
        if (!$sub) {
            return false;
        }
        
        // Check if subject is in subscription's selected subjects
        $selectedSubjects = array_filter(array_map('intval', 
            explode(',', (string)$sub['usubs_subject_ids'])));
        
        return in_array($subjectId, $selectedSubjects, true);
    }

    /**
     * Get access method for user course (for display purposes)
     */
    public static function getAccessMethod(int $userId, int $courseId): string
    {
        if (self::hasOrderAccess($userId, $courseId)) {
            return 'purchase';
        }
        if (self::hasSubscriptionAccess($userId, $courseId)) {
            return 'subscription';
        }
        return 'none';
    }

    /**
     * Ensure subscription access is recorded
     */
    public static function ensureSubscriptionAccess(int $userId, int $courseId): bool
    {
        if (!self::hasSubscriptionAccess($userId, $courseId)) {
            return false;
        }

        $db = FatApp::getDb();
        
        // Check if access record already exists
        $srch = new SearchBase('tbl_user_course_access');
        $srch->addCondition('uca_user_id', '=', $userId);
        $srch->addCondition('uca_course_id', '=', $courseId);
        $srch->setPageSize(1);
        
        if ($db->fetch($srch->getResultSet())) {
            return true; // Already exists
        }

        // Get active subscription
        $sub = UserSubscription::getActiveByUser($userId);
        if (!$sub) {
            return false;
        }

        // Create access record
        $data = [
            'uca_user_id' => $userId,
            'uca_course_id' => $courseId,
            'uca_subscription_id' => $sub['usubs_id'],
            'uca_created' => date('Y-m-d H:i:s')
        ];
        
        return $db->insertFromArray('tbl_user_course_access', $data);
    }
}