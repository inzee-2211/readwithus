<?php
require_once __DIR__ . '/CourseAccessService.php';
defined('SYSTEM_INIT') or die('Invalid Usage');

class SubscriptionEnrollment
{
    /**
     * Ensure user has access to course via subscription
     */
public static function ensureForCourse(int $userId, int $courseId): int
{
    $userId = FatUtility::int($userId);
    $courseId = FatUtility::int($courseId);
    
    $debugLog = "=== ensureForCourse Debug ===\n";
    $debugLog .= "User ID: $userId, Course ID: $courseId\n";
    
    if ($userId < 1 || $courseId < 1) { 
        $debugLog .= "❌ Invalid user or course ID\n";
        self::writeDebugLog($debugLog);
        return 0; 
    }

    $db = FatApp::getDb();

    // Check subscription access
    $hasAccess = CourseAccessService::hasSubscriptionAccess($userId, $courseId);
    $debugLog .= "hasSubscriptionAccess returned: " . ($hasAccess ? "TRUE" : "FALSE") . "\n";
    
    if (!$hasAccess) {
        $debugLog .= "❌ Subscription access denied\n";
        self::writeDebugLog($debugLog);
        return 0;
    }

    $debugLog .= "✅ Subscription access granted\n";

    // Ensure access record exists - this is the main record now
    $ensureAccess = CourseAccessService::ensureSubscriptionAccess($userId, $courseId);
    $debugLog .= "ensureSubscriptionAccess returned: " . ($ensureAccess ? "TRUE" : "FALSE") . "\n";

    // For subscription access, we return a fake ordcrs_id that represents the access record
    // We'll use negative numbers to distinguish from real order course IDs
    $accessId = self::getSubscriptionAccessId($userId, $courseId);
    $debugLog .= "✅ Using subscription access ID: $accessId\n";
    
    // Ensure progress record exists using our access ID
    $progressResult = self::ensureProgressRecord($accessId);
    $debugLog .= "ensureProgressRecord returned: " . ($progressResult ? "TRUE" : "FALSE") . "\n";
    
    $debugLog .= "✅ Returning access ID: $accessId\n";
    self::writeDebugLog($debugLog);
    
    return $accessId;
}

/**
 * Generate a unique access ID for subscription-based access
 * We use negative numbers to avoid conflicts with real order course IDs
 */
private static function getSubscriptionAccessId(int $userId, int $courseId): int
{
    // Create a unique negative ID based on user and course
    // This ensures the same user+course always gets the same access ID
    $uniqueId = -1 * (($userId * 100000) + $courseId);
    
    // Make sure it's within integer range
    if ($uniqueId < -2147483647) {
        $uniqueId = -2147483647;
    }
    
    return $uniqueId;
}

/**
 * Write debug log to file
 */
private static function writeDebugLog(string $message): void
{
    $logFile = CONF_INSTALLATION_PATH . 'application/logs/subscription_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

    /**
     * Get the active status value for order course
     */
    private static function getOrderCourseActiveStatus(): int
    {
        // Check if constant exists, otherwise use default value
        if (defined('OrderCourse::STATUS_ACTIVE')) {
            return OrderCourse::STATUS_ACTIVE;
        }
        
        // Try other common constant names
        if (defined('OrderCourse::ACTIVE')) {
            return OrderCourse::ACTIVE;
        }
        
        // Default to 1 (most common value for active status)
        return 1;
    }

    /**
     * Get the cancelled status value for order course
     */
    private static function getOrderCourseCancelledStatus(): int
    {
        // Check if constant exists, otherwise use default value
        if (defined('OrderCourse::STATUS_CANCELLED')) {
            return OrderCourse::STATUS_CANCELLED;
        }
        
        // Try other common constant names
        if (defined('OrderCourse::CANCELLED')) {
            return OrderCourse::CANCELLED;
        }
        
        // Default to 3 (common value for cancelled status)
        return 3;
    }

    /**
     * Get the completed status value for order course
     */
    private static function getOrderCourseCompletedStatus(): int
    {
        // Check if constant exists, otherwise use default value
        if (defined('OrderCourse::STATUS_COMPLETED')) {
            return OrderCourse::STATUS_COMPLETED;
        }
        
        // Try other common constant names
        if (defined('OrderCourse::COMPLETED')) {
            return OrderCourse::COMPLETED;
        }
        
        // Default to 2 (common value for completed status)
        return 2;
    }

    /**
     * Ensure course progress record exists
     */
    private static function ensureProgressRecord(int $ordcrsId): bool
    {
        $db = FatApp::getDb();
        
        $srch = new SearchBase(CourseProgress::DB_TBL, 'cp');
        $srch->addCondition('cp.crspro_ordcrs_id', '=', $ordcrsId);
        $srch->setPageSize(1);
        
        if ($db->fetch($srch->getResultSet())) {
            return true;
        }

        // Get the actual progress status values
        $inProgressStatus = self::getCourseProgressInProgressStatus();

        $cp = new TableRecord(CourseProgress::DB_TBL);
        $cp->assignValues([
            'crspro_ordcrs_id' => $ordcrsId,
            'crspro_started'   => date('Y-m-d H:i:s'),
            'crspro_status'    => $inProgressStatus,
            'crspro_progress'  => 0,
        ]);
        
        return $cp->addNew();
    }

    /**
     * Get the in-progress status value for course progress
     */
    private static function getCourseProgressInProgressStatus(): int
    {
        // Check if constant exists, otherwise use default value
        if (defined('CourseProgress::IN_PROGRESS')) {
            return CourseProgress::IN_PROGRESS;
        }
        
        // Try other common constant names
        if (defined('CourseProgress::STATUS_IN_PROGRESS')) {
            return CourseProgress::STATUS_IN_PROGRESS;
        }
        
        // Default to 1 (common value for in-progress status)
        return 1;
    }

    /**
     * Sync all courses for user based on subscription
     */
    public static function syncForUser(int $userId): void
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) { 
            return; 
        }

        $db = FatApp::getDb();
        $sub = UserSubscription::getActiveByUser($userId);
        
        if (!$sub) { 
            // Remove subscription access if no active subscription
            self::removeSubscriptionAccess($userId);
            return; 
        }

        $allowed = array_filter(array_map('intval', explode(',', (string)$sub['usubs_subject_ids'])));
        if (empty($allowed)) { 
            self::removeSubscriptionAccess($userId);
            return; 
        }

        // Get all courses in allowed subjects
        $srch = new SearchBase(Course::DB_TBL, 'c');
        $srch->addMultipleFields(['c.course_id', 'c.course_subject_id']);
        $srch->addCondition('c.course_subject_id', 'IN', $allowed);
        $srch->addCondition('c.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('c.course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);

        $rs = $srch->getResultSet();
        if (!$rs) { 
            return; 
        }

        $accessibleCourses = [];
        while ($row = $db->fetch($rs)) {
            $accessibleCourses[] = (int)$row['course_id'];
            self::ensureForCourse($userId, (int)$row['course_id']);
        }

        // Remove access to courses no longer in subscription
        self::cleanupAccess($userId, $accessibleCourses);
    }

    /**
     * Remove subscription access for user
     */
    private static function removeSubscriptionAccess(int $userId): void
    {
        $db = FatApp::getDb();
        
        // Remove from access table
        $db->deleteRecords('tbl_user_course_access', [
            'smt' => 'uca_user_id = ?',
            'vals' => [$userId]
        ]);

        // Get cancelled status
        $cancelledStatus = self::getOrderCourseCancelledStatus();

        // Update order course status
        $db->updateFromArray(
            OrderCourse::DB_TBL, 
            ['ordcrs_status' => $cancelledStatus],
            [
                'smt' => 'ordcrs_user_id = ? AND ordcrs_source = ?',
                'vals' => [$userId, 1]
            ]
        );
    }

    /**
     * Clean up access to courses no longer in subscription
     */
    private static function cleanupAccess(int $userId, array $accessibleCourses): void
    {
        if (empty($accessibleCourses)) {
            return;
        }

        $db = FatApp::getDb();
        
        // Remove from access table
        $db->deleteRecords('tbl_user_course_access', [
            'smt' => 'uca_user_id = ? AND uca_course_id NOT IN (' . implode(',', $accessibleCourses) . ')',
            'vals' => [$userId]
        ]);

        // Get cancelled status
        $cancelledStatus = self::getOrderCourseCancelledStatus();

        // Update order course status for inaccessible courses
        $db->updateFromArray(
            OrderCourse::DB_TBL,
            ['ordcrs_status' => $cancelledStatus],
            [
                'smt' => 'ordcrs_user_id = ? AND ordcrs_source = ? AND ordcrs_course_id NOT IN (' . implode(',', $accessibleCourses) . ')',
                'vals' => [$userId, 1]
            ]
        );
    }

    /**
     * Debug method to check what status constants are available
     */
    public static function debugStatusConstants(): array
    {
        $constants = [];
        
        // Check OrderCourse constants
        $orderCourseConstants = [
            'STATUS_ACTIVE', 'ACTIVE',
            'STATUS_COMPLETED', 'COMPLETED', 
            'STATUS_CANCELLED', 'CANCELLED'
        ];
        
        foreach ($orderCourseConstants as $constant) {
            if (defined("OrderCourse::$constant")) {
                $constants["OrderCourse::$constant"] = constant("OrderCourse::$constant");
            }
        }
        
        // Check CourseProgress constants
        $courseProgressConstants = [
            'IN_PROGRESS', 'STATUS_IN_PROGRESS',
            'COMPLETED', 'STATUS_COMPLETED',
            'PENDING', 'STATUS_PENDING'
        ];
        
        foreach ($courseProgressConstants as $constant) {
            if (defined("CourseProgress::$constant")) {
                $constants["CourseProgress::$constant"] = constant("CourseProgress::$constant");
            }
        }
        
        return $constants;
    }
}