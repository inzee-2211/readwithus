<?php

/**
 * Admin Class is used to handle Admin Statistic
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class AdminStatistic
{

    /**
     * Get Dashboard Stats
     * 
     * @param bool $recalculate
     * @return boolean
     */
    public static function getDashboardStats(bool $recalculate = false)
    {
        $stats = json_decode(FatApp::getConfig('CONF_ADMIN_DASHBOARD_STATS'), true);
        // if (!$recalculate) {
        //     return $stats;
        // }
            if (!$recalculate && is_array($stats)) {
                $stats['TM_ADMIN_EARNINGS']  = static::getAdminEarnings(false);
        $stats['ALL_ADMIN_EARNINGS'] = static::getAdminEarnings(true);
                 $stats['ALL_COURSES_TOTAL']  = static::getCoursesTotal(true);
        $stats['TM_COURSES_TOTAL']   = static::getCoursesTotal(false);
                 $stats['ALL_USERS_TOTAL']    = static::getUsersTotal(true);
                     $stats['ALL_TEACHERS_TOTAL'] = static::getTeachersTotal(true);
        $stats['TM_TEACHERS_TOTAL']  = static::getTeachersTotal(false);
        // merge subscription stats live even when using cached dashboard
        return array_merge($stats, static::getSubscriptionStats());
    }
        $stats = [
            'TM_LESSONS_REVENUE' => static::getLessonsRevenue(),
            'ALL_LESSONS_REVENUE' => static::getLessonsRevenue(true),
            'TM_CLASSES_REVENUE' => static::getClassesRevenue(),
            'ALL_CLASSES_REVENUE' => static::getClassesRevenue(true),
            'TM_COURSES_REVENUE' => static::getCoursesRevenue(),
            'ALL_COURSES_REVENUE' => static::getCoursesRevenue(true),
            'TM_ADMIN_EARNINGS' => static::getAdminEarnings(),
            'ALL_ADMIN_EARNINGS' => static::getAdminEarnings(true),
            'TM_LESSONS_TOTAL' => static::getLessonsTotal(),
            'ALL_LESSONS_TOTAL' => static::getLessonsTotal(true),
            'TM_CLASSES_TOTAL' => static::getClassesTotal(),
            'ALL_CLASSES_TOTAL' => static::getClassesTotal(true),
            'TM_COURSES_TOTAL' => static::getCoursesTotal(),
            'ALL_COURSES_TOTAL' => static::getCoursesTotal(true),
            'TM_COMPLETED_LESSONS' => static::getCompletedLessons(),
            'ALL_COMPLETED_LESSONS' => static::getCompletedLessons(true),
            'TM_COMPLETED_CLASSES' => static::getCompletedClasses(),
            'ALL_COMPLETED_CLASSES' => static::getCompletedClasses(true),
            'TM_COMPLETED_COURSES' => static::getCompletedCourses(),
            'ALL_COMPLETED_COURSES' => static::getCompletedCourses(true),
            'TM_CANCELLED_LESSONS' => static::getCancelledLessons(),
            'ALL_CANCELLED_LESSONS' => static::getCancelledLessons(true),
            'TM_CANCELLED_CLASSES' => static::getCancelledClasses(),
            'ALL_CANCELLED_CLASSES' => static::getCancelledClasses(true),
            'TM_CANCELLED_COURSES' => static::getCancelledCourses(),
            'ALL_CANCELLED_COURSES' => static::getCancelledCourses(true),
            'TM_UNSCHEDULE_LESSONS' => static::getUnscheduleLessons(),
            'ALL_UNSCHEDULE_LESSONS' => static::getUnscheduleLessons(true),
            'TM_USERS_TOTAL' => static::getUsersTotal(),
            'ALL_USERS_TOTAL' => static::getUsersTotal(true),
            'TM_TEACHERS_TOTAL' => static::getTeachersTotal(),
    'ALL_TEACHERS_TOTAL' => static::getTeachersTotal(true),
        ];
            $stats = array_merge($stats, static::getSubscriptionStats());
            
        if ($recalculate) {
            $assignValues = ['conf_name' => 'CONF_ADMIN_DASHBOARD_STATS', 'conf_val' => json_encode($stats)];
            if (!FatApp::getDb()->insertFromArray(Configurations::DB_TBL, $assignValues, false, [], $assignValues)) {
                return false;
            }
        }
        return $stats;
    }

    /**
     * Get Lessons Revenue
     * 
     * @param bool $all
     * @return float
     */
    private static function getLessonsRevenue(bool $all = false): float
    {
        $srch = new SearchBase('tbl_sales_stats', 'slstat');
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, CONF_SERVER_TIMEZONE, false, 'Y-m-d');
            $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
            $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['SUM(IFNULL(slstat_les_sales,0)) as les_sales']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['les_sales'] ?? 0.00;
    }

    /**
     * Get Classes Revenue
     * 
     * @param bool $all
     * @return float
     */
    private static function getClassesRevenue(bool $all = false): float
    {
        $srch = new SearchBase('tbl_sales_stats', 'slstat');
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, CONF_SERVER_TIMEZONE, false, 'Y-m-d');
            $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
            $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['SUM(IFNULL(slstat_cls_sales,0)) as cls_sales']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['cls_sales'] ?? 0.00;
    }

    /**
     * Get Lessons Revenue
     * 
     * @param bool $all
     * @return float
     */
    private static function getCoursesRevenue(bool $all = false): float
    {
        $srch = new SearchBase('tbl_sales_stats', 'slstat');
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, CONF_SERVER_TIMEZONE, false, 'Y-m-d');
            $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
            $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['SUM(IFNULL(slstat_crs_sales,0)) as crs_sales']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['crs_sales'] ?? 0.00;
    }

    /**
     * Get Admin Earnings
     * 
     * @param bool $all
     * @return float
     */
    /**
 * Get Admin Earnings
 *
 * - From tbl_sales_stats:
 *      slstat_les_earnings + slstat_cls_earnings + slstat_crs_earnings
 * - PLUS subscription revenue (non-trial subs) from tbl_user_subscriptions
 *
 * @param bool $all
 * @return float
 */
private static function getAdminEarnings(bool $all = false): float
{
    $db   = FatApp::getDb();

    // 1) Earnings from lessons / classes / courses (aggregated sales stats)
    $srch = new SearchBase('tbl_sales_stats', 'slstat');

    if (!$all) {
        $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, CONF_SERVER_TIMEZONE, false, 'Y-m-d');
        $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
        $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
    }

    $srch->addFld(
        'SUM(
            IFNULL(slstat_les_earnings, 0)
          + IFNULL(slstat_cls_earnings, 0)
          + IFNULL(slstat_crs_earnings, 0)
        ) AS earnings'
    );
    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $row          = $db->fetch($srch->getResultSet());
    $orderEarning = FatUtility::float($row['earnings'] ?? 0.0);

    // 2) Earnings from subscriptions
    // Uses tbl_user_subscriptions + tbl_subscription_packages
    $subscriptionEarning = static::getSubscriptionRevenue($all);

    // Total admin earnings = orders + subscriptions
    return $orderEarning + $subscriptionEarning;
}



//STATS ADDED BY REHAN

/**
 * Get Courses Total
 *
 * All = all approved + published courses (not deleted)
 * This month = approved + published courses created this month
 */
// private static function getCoursesTotal(bool $all = false): int
// {
//     $srch = new CourseSearch(0, 0, User::SUPPORT);
//     $srch->applyPrimaryConditions(); // Yo! – usually already filters out deleted etc.

//     // Extra safety: only non-deleted, published courses
//     $srch->addDirectCondition('course.course_deleted IS NULL');
//     $srch->addCondition('course.course_status', '=', Course::PUBLISHED);

//     // Join approval table and keep ONLY approved requests
//     $srch->joinTable(
//         Course::DB_TBL_APPROVAL_REQUEST,
//         'INNER JOIN',
//         'course.course_id = coapre.coapre_course_id',
//         'coapre'
//     );
//     $srch->addCondition('coapre.coapre_status', '=', Course::REQUEST_APPROVED);

//     // Limit to "this month" when $all = false
//     if (!$all) {
//         $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, null, true);
//         $srch->addCondition('course.course_created', '>=', $datetime['startDate']);
//         $srch->addCondition('course.course_created', '<=', $datetime['endDate']);
//     }

//     // DISTINCT in case a course ever has multiple approval rows
//     $srch->addMultipleFields(['COUNT(DISTINCT course.course_id) AS totalCourses']);
//     $srch->doNotCalculateRecords();
//     $srch->setPageSize(1);

//     $records = FatApp::getDb()->fetch($srch->getResultSet());
//     return (int)($records['totalCourses'] ?? 0);
// }

/**
 * Get Teachers Total
 *
 * @param bool $all
 * @return int
 */
private static function getTeachersTotal(bool $all = false): int
{
    $srch = new SearchBase(User::DB_TBL, 'user');
    $srch->addDirectCondition('user_deleted IS NULL');
    // Marked as teacher
    $srch->addCondition('user_is_teacher', '=', 1);

    if (!$all) {
        $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
        $srch->addCondition('user_created', '>=', $datetime['startDate']);
        $srch->addCondition('user_created', '<=', $datetime['endDate']);
    }

    $srch->addMultipleFields(['COUNT(user_id) AS totalTeacher']);
    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $records = FatApp::getDb()->fetch($srch->getResultSet());
    return $records['totalTeacher'] ?? 0;
}

/* ---------- Subscription / payment stats (new) ---------- */

public static function getSubscriptionStats(): array
{
    return [
        'SUB_ACTIVE_COUNT'      => static::getActiveSubscriptionsCount(),
        'SUB_EXPIRED_COUNT'     => static::getExpiredSubscriptionsCount(),
        'SUB_REVENUE_ALL'       => static::getSubscriptionRevenue(true),
        'SUB_REVENUE_THISMONTH' => static::getSubscriptionRevenue(false),
    ];
}

/**
 * Number of currently active (paid or trialing) subscriptions.
 */
private static function getActiveSubscriptionsCount(): int
{
    $db   = FatApp::getDb();
    $srch = new SearchBase('tbl_user_subscriptions', 'usubs');

    // Count "active" + "trialing" as active
    $srch->addCondition('usubs_status', 'IN', ['active', 'trialing']);

    $srch->addMultipleFields(['COUNT(*) AS total']);
    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $row = $db->fetch($srch->getResultSet());
    return FatUtility::int($row['total'] ?? 0);
}

/**
 * Number of expired / cancelled subscriptions.
 */
private static function getExpiredSubscriptionsCount(): int
{
    $db   = FatApp::getDb();
    $srch = new SearchBase('tbl_user_subscriptions', 'usubs');

    // Canceled or past_due
    $srch->addCondition('usubs_status', 'IN', ['canceled', 'past_due']);

    $srch->addMultipleFields(['COUNT(*) AS total']);
    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $row = $db->fetch($srch->getResultSet());
    return FatUtility::int($row['total'] ?? 0);
}

/**
 * Total subscription revenue.
 * Uses tbl_user_subscriptions + tbl_subscription_packages ONLY.
 * - Ignores pure free trials (usubs_is_trial = 1)
 * - "All" = all time
 * - "This month" = by usubs_created date
 */
private static function getSubscriptionRevenue(bool $all = false): float
{
    $db   = FatApp::getDb();
    $srch = new SearchBase('tbl_user_subscriptions', 'usubs');

    // Join package to get plan prices
    $srch->joinTable(
        'tbl_subscription_packages',
        'INNER JOIN',
        'spkg.spackage_id = usubs.usubs_spackage_id',
        'spkg'
    );

    // Only paid subs (skip free trials)
    $srch->addCondition('usubs_is_trial', '=', 0);

    // Include all finished/active subs in revenue
    $srch->addCondition('usubs_status', 'IN', ['active', 'trialing', 'past_due', 'canceled']);

    if (!$all) {
        // Revenue counted in month the subscription row was created
        $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, null, true);
        $srch->addCondition('usubs_created', '>=', $datetime['startDate']);
        $srch->addCondition('usubs_created', '<=', $datetime['endDate']);
    }

    $srch->addMultipleFields([
        "SUM(
            CASE
                WHEN usubs_billing_interval = 'month'
                    THEN IFNULL(spkg.spackage_price_monthly, 0)
                WHEN usubs_billing_interval = 'year'
                    THEN IFNULL(spkg.spackage_price_yearly, 0)
                ELSE 0
            END
        ) AS revenue"
    ]);

    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $row = $db->fetch($srch->getResultSet());
    return FatUtility::float($row['revenue'] ?? 0.0);
}


    /**
     * Get Lessons Total
     * 
     * @param bool $all
     * @return int
     */
    private static function getLessonsTotal(bool $all = false): int
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(Order::DB_TBL_LESSON, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id', 'ordles');
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_LESSON, Order::TYPE_SUBSCR]);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('order_addedon', '>=', $datetime['startDate']);
            $srch->addCondition('order_addedon', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(ordles_id) AS totalLesson']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalLesson'] ?? 0;
    }

    /**
     * Get Classes Total
     * 
     * @param bool $all
     * @return int
     */
    private static function getClassesTotal(bool $all = false): int
    {
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('grpcls_added_on', '>=', $datetime['startDate']);
            $srch->addCondition('grpcls_added_on', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(grpcls_id) AS totalClasses']);
        $srch->addCondition('grpcls_parent', '=', 0);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalClasses'] ?? 0;
    }

    /**
     * Get Courses Total
     * 
     * @param bool $all
     * @return int
     */
    private static function getCoursesTotal(bool $all = false): int
    {
        $srch = new CourseSearch(0, 0, User::SUPPORT);
        $srch->applyPrimaryConditions();
        $srch->joinTable(
            Course::DB_TBL_APPROVAL_REQUEST,
            'INNER JOIN',
            'course.course_id = coapre.coapre_course_id',
            'coapre'
        );
        $srch->addCondition('coapre.coapre_status', '=', Course::REQUEST_APPROVED);
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('course.course_created', '>=', $datetime['startDate']);
            $srch->addCondition('course.course_created', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(course.course_id) AS totalCourses']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalCourses'] ?? 0;
    }

    /**
     * Get Completed Lessons
     * 
     * @param bool $all
     * @return int
     */
    private static function getCompletedLessons(bool $all = false): int
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(Order::DB_TBL_LESSON, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id', 'ordles');
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_LESSON, Order::TYPE_SUBSCR]);
        $srch->addCondition('orders.order_status', '=', Order::STATUS_COMPLETED);
        $srch->addCondition('ordles.ordles_status', '=', Lesson::COMPLETED);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('ordles_updated', '>=', $datetime['startDate']);
            $srch->addCondition('ordles_updated', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(ordles_id) AS totalLesson']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalLesson'] ?? 0;
    }

    /**
     * Get Completed Classes
     * 
     * @param bool $all
     * @return int
     */
    private static function getCompletedClasses(bool $all = false): int
    {
        $srch = new SearchBase(OrderClass::DB_TBL, 'ordcls');
        $srch->addCondition('ordcls_status', '=', OrderClass::COMPLETED);
        if (!$all) {
            $srch->addDirectCondition('ordcls.ordcls_teacher_paid IS NOT NULL');
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('ordcls_updated', '>=', $datetime['startDate']);
            $srch->addCondition('ordcls_updated', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(distinct ordcls_grpcls_id) AS totalClasses']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalClasses'] ?? 0;
    }

    /**
     * Get Completed Courses
     * 
     * @param bool $all
     * @return int
     */
    private static function getCompletedCourses(bool $all = false): int
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'orders.order_id = ordcrs.ordcrs_order_id', 'ordcrs');
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_COURSE]);
        $srch->addCondition('orders.order_status', '=', Order::STATUS_COMPLETED);
        $srch->addCondition('ordcrs.ordcrs_status', '=', OrderCourse::COMPLETED);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('ordcrs_updated', '>=', $datetime['startDate']);
            $srch->addCondition('ordcrs_updated', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(ordcrs_id) AS totalCourses']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalCourses'] ?? 0;
    }

    /**
     * Get Cancelled Lessons
     * 
     * @param bool $all
     * @return int
     */
    private static function getCancelledLessons(bool $all = false): int
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(Order::DB_TBL_LESSON, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id', 'ordles');
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_LESSON, Order::TYPE_SUBSCR]);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('ordles.ordles_status', '=', Lesson::CANCELLED);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('ordles_updated', '>=', $datetime['startDate']);
            $srch->addCondition('ordles_updated', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(ordles_id) AS totalLesson']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalLesson'] ?? 0;
    }

    /**
     * Get Cancelled Classes
     * 
     * @param bool $all
     * @return int
     */
    private static function getCancelledClasses(bool $all = false): int
    {
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls_status', '=', GroupClass::CANCELLED);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('grpcls_added_on', '>=', $datetime['startDate']);
            $srch->addCondition('grpcls_added_on', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(grpcls_id) AS totalClasses']);
        $srch->addCondition('grpcls_parent', '=', 0);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalClasses'] ?? 0;
    }

    /**
     * Get Cancelled Courses
     * 
     * @param bool $all
     * @return int
     */
    private static function getCancelledCourses(bool $all = false): int
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'orders.order_id = ordcrs.ordcrs_order_id', 'ordcrs');
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_COURSE]);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('ordcrs.ordcrs_status', '=', OrderCourse::CANCELLED);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('ordcrs_updated', '>=', $datetime['startDate']);
            $srch->addCondition('ordcrs_updated', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(ordcrs_id) AS totalCourses']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalCourses'] ?? 0;
    }

    /**
     * Get Unscheduled Lessons
     * 
     * @param bool $all
     * @return int
     */
    private static function getUnscheduleLessons(bool $all = false): int
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(Order::DB_TBL_LESSON, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id', 'ordles');
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_LESSON, Order::TYPE_SUBSCR]);
        $srch->addCondition('orders.order_status', '=', Order::STATUS_COMPLETED);
        $srch->addCondition('ordles.ordles_status', '=', Lesson::UNSCHEDULED);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        if (!$all) {
            $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
            $srch->addCondition('order_addedon', '>=', $datetime['startDate']);
            $srch->addCondition('order_addedon', '<=', $datetime['endDate']);
        }
        $srch->addMultipleFields(['COUNT(ordles_id) AS totalLesson']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records['totalLesson'] ?? 0;
    }

    /**
     * Get Users Total
     * 
     * @param bool $all
     * @return int
     */
    // private static function getUsersTotal(bool $all = false): int
    // {
    //     $srch = new SearchBase(User::DB_TBL, 'user');
    //     $srch->addDirectCondition('user_deleted IS NULL');
    //     if (!$all) {
    //         $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
    //         $srch->addCondition('user_created', '>=', $datetime['startDate']);
    //         $srch->addCondition('user_created', '<=', $datetime['endDate']);
    //     }
    //     $srch->addMultipleFields(['COUNT(user_id) AS totalUser']);
    //     $srch->doNotCalculateRecords();
    //     $srch->setPageSize(1);
    //     $records = FatApp::getDb()->fetch($srch->getResultSet());
    //     return $records['totalUser'] ?? 0;
    // }
private static function getUsersTotal(bool $all = false): int
{
    $srch = new SearchBase(User::DB_TBL, 'user');

    // only real users
    $srch->addDirectCondition('user_deleted IS NULL');
    $srch->addCondition('user_active', '=', 1);
    $srch->addDirectCondition('user_verified IS NOT NULL');

    if (!$all) {
        $datetime = MyDate::getStartEndDate(MyDate::TYPE_THIS_MONTH, NULL, true);
        $srch->addCondition('user_created', '>=', $datetime['startDate']);
        $srch->addCondition('user_created', '<=', $datetime['endDate']);
    }

    $srch->addMultipleFields(['COUNT(user_id) AS totalUser']);
    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $records = FatApp::getDb()->fetch($srch->getResultSet());
    return $records['totalUser'] ?? 0;
}


    /**
     * Lesson Top Language
     * 
     * @param int $siteLangId
     * @param int $interval
     * @param int $limit
     * @return array
     */
    public static function lessonTopLanguage($siteLangId, int $interval, int $limit = 10): array
    {

        $datetime = MyDate::getStartEndDate($interval, NULL, true);
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(Order::DB_TBL_LESSON, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id', 'ordles');
        $srch->addMultipleFields(['COUNT(ordles_tlang_id) AS totalsold', 'ordles_tlang_id']);
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_LESSON, Order::TYPE_SUBSCR]);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('orders.order_addedon', '>=', $datetime['startDate']);
        $srch->addCondition('orders.order_addedon', '<=', $datetime['endDate']);
        $srch->addCondition('ordles.ordles_tlang_id', '>', 0);
        $srch->addGroupBy('ordles_tlang_id');
        $srch->addOrder('totalsold', 'DESC');
        $srch->doNotCalculateRecords();
        $srch->setPageSize($limit);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $langData = [];
        if (!empty($records)) {
            $teachLangIds = array_column($records, 'ordles_tlang_id');
            $teachLangs = TeachLanguage::getNames($siteLangId, $teachLangIds);
            foreach ($records as &$record) {
                if (!array_key_exists($record['ordles_tlang_id'], $teachLangs)) {
                    continue;
                }
                $record['language'] = $teachLangs[$record['ordles_tlang_id']];
                $langData[] = $record;
            }
        }
        return $langData;
    }

    /**
     * Course Top Categories
     * 
     * @param int $siteLangId
     * @param int $interval
     * @param int $limit
     * @return array
     */
    public static function courseTopCategories($siteLangId, int $interval, int $limit = 10): array
    {
        $datetime = MyDate::getStartEndDate($interval, NULL, true);
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'orders.order_id = ordcrs.ordcrs_order_id', 'ordcrs');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'course.course_id = ordcrs.ordcrs_course_id', 'course');
        $srch->addMultipleFields(['COUNT(course_cate_id) AS totalsold', 'course_cate_id']);
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_COURSE]);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('orders.order_addedon', '>=', $datetime['startDate']);
        $srch->addCondition('orders.order_addedon', '<=', $datetime['endDate']);
        $srch->addCondition('course.course_cate_id', '>', 0);
        $srch->addGroupBy('course_cate_id');
        $srch->addOrder('totalsold', 'DESC');
        $srch->doNotCalculateRecords();
        $srch->setPageSize($limit);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $catgData = [];
        if (!empty($records)) {
            $catgIds = array_column($records, 'course_cate_id');
            $categories = Category::getNames($catgIds, $siteLangId);
            foreach ($records as &$record) {
                if (!array_key_exists($record['course_cate_id'], $categories)) {
                    continue;
                }
                $record['category'] = $categories[$record['course_cate_id']];
                $catgData[] = $record;
            }
        }
        return $catgData;
    }

    /**
     * Classes Top Language
     * 
     * @param int $siteLangId
     * @param int $interval
     * @param int $limit
     * @return array
     */
    public static function classTopLanguage($siteLangId, int $interval, int $limit = 10): array
    {

        $datetime = MyDate::getStartEndDate($interval, NULL, true);
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(OrderClass::DB_TBL, 'INNER JOIN', 'orders.order_id = ordcls.ordcls_order_id', 'ordcls');
        $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id = ordcls.ordcls_grpcls_id', 'grpcls');
        $srch->addMultipleFields(['COUNT(grpcls_tlang_id) AS totalsold', 'grpcls_tlang_id']);
        $srch->addCondition('orders.order_type', 'IN', [Order::TYPE_GCLASS, Order::TYPE_PACKGE]);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('orders.order_addedon', '>=', $datetime['startDate']);
        $srch->addCondition('orders.order_addedon', '<=', $datetime['endDate']);
        $srch->addCondition('grpcls.grpcls_tlang_id', '>', 0);
        $srch->addGroupBy('grpcls_tlang_id');
        $srch->addOrder('totalsold', 'DESC');
        $srch->doNotCalculateRecords();
        $srch->setPageSize($limit);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $langData = [];
        if (!empty($records)) {
            $teachLangIds = array_column($records, 'grpcls_tlang_id');
            $teachLangs = TeachLanguage::getNames($siteLangId, $teachLangIds);
            foreach ($records as &$record) {
                if (!array_key_exists($record['grpcls_tlang_id'], $teachLangs)) {
                    continue;
                }
                $record['language'] = $teachLangs[$record['grpcls_tlang_id']];
                $langData[] = $record;
            }
        }
        return $langData;
    }

    /**
     * Get Users Stat
     * 
     * @param int $durationType
     * @return array
     */
    public static function getUsersStat(int $durationType): array
    {
        $datetime = MyDate::getStartEndDate($durationType, NULL, true);
        $srch = new SearchBase(User::DB_TBL, 'user');
        $srch->addDirectCondition('user_deleted IS NULL');
        $srch->addCondition('user_created', '>=', $datetime['startDate']);
        $srch->addCondition('user_created', '<=', $datetime['endDate']);
        switch ($durationType) {
            case MyDate::TYPE_TODAY:
                $srch->addFld("DATE_FORMAT(user_created, '%H:%i') as groupDate");
                break;
            case MyDate::TYPE_THIS_YEAR:
            case MyDate::TYPE_LAST_YEAR:
            case MyDate::TYPE_LAST_12_MONTH:
                $srch->addFld("DATE_FORMAT(user_created, '%m-%Y') as groupDate");
                break;
            default:
                $srch->addFld("DATE_FORMAT(user_created, '%Y-%m-%d') as groupDate");
                break;
        }
        $srch->addMultipleFields(['COUNT(user_id) AS totalUser']);
        $srch->addGroupBy("groupDate");
        $srch->addOrder("YEAR(user_created)", 'ASC');
        $srch->addOrder("MONTH(user_created)", 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'groupDate');
    }

    /**
     * Get Admin Lesson Earning Stats
     * 
     * @param int $durationType
     * @return array
     */
    public static function getAdminLessonEarningStats(int $durationType): array
    {
        $datetime = MyDate::getStartEndDate($durationType, NULL, true, 'Y-m-d');
        $srch = new SearchBase('tbl_sales_stats', 'slstat');
        $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
        $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
        switch ($durationType) {
            case MyDate::TYPE_THIS_YEAR:
            case MyDate::TYPE_LAST_YEAR:
            case MyDate::TYPE_LAST_12_MONTH:
                $srch->addFld("DATE_FORMAT(slstat_date, '%m-%Y') as groupDate");
                break;
            default:
                $srch->addFld("DATE_FORMAT(slstat_date, '%Y-%m-%d') as groupDate");
                break;
        }
        $srch->addMultipleFields(['sum(IFNULL(slstat_les_earnings,0)) as les_earnings']);
        $srch->addGroupBy("groupDate");
        $srch->addOrder("YEAR(slstat_date)", 'ASC');
        $srch->addOrder("MONTH(slstat_date)", 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'groupDate');
    }

    /**
     * Get Admin Class Earning Stats
     * 
     * @param int $durationType
     * @return array
     */
    public static function getAdminClassEarningStats(int $durationType): array
    {
        $datetime = MyDate::getStartEndDate($durationType, NULL, true, 'Y-m-d');
        $srch = new SearchBase('tbl_sales_stats', 'slstat');
        $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
        $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
        switch ($durationType) {
            case MyDate::TYPE_THIS_YEAR:
            case MyDate::TYPE_LAST_YEAR:
            case MyDate::TYPE_LAST_12_MONTH:
                $srch->addFld("DATE_FORMAT(slstat_date, '%m-%Y') as groupDate");
                break;
            default:
                $srch->addFld("DATE_FORMAT(slstat_date, '%Y-%m-%d') as groupDate");
                break;
        }
        $srch->addMultipleFields(['sum(IFNULL(slstat_cls_earnings,0)) as cls_earnings']);
        $srch->addGroupBy("groupDate");
        $srch->addOrder("YEAR(slstat_date)", 'ASC');
        $srch->addOrder("MONTH(slstat_date)", 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'groupDate');
    }

    /**
     * Get Admin Course Earning Stats
     * 
     * @param int $durationType
     * @return array
     */
    public static function getAdminCourseEarningStats(int $durationType): array
    {
        $datetime = MyDate::getStartEndDate($durationType, NULL, true, 'Y-m-d');
        
        $srch = new SearchBase('tbl_sales_stats', 'slstat');
        $srch->addCondition('slstat_date', '>=', $datetime['startDate']);
        $srch->addCondition('slstat_date', '<=', $datetime['endDate']);
        switch ($durationType) {
            case MyDate::TYPE_THIS_YEAR:
            case MyDate::TYPE_LAST_YEAR:
            case MyDate::TYPE_LAST_12_MONTH:
                $srch->addFld("DATE_FORMAT(slstat_date, '%m-%Y') as groupDate");
                break;
            default:
                $srch->addFld("DATE_FORMAT(slstat_date, '%Y-%m-%d') as groupDate");
                break;
        }
        $srch->addMultipleFields(['sum(IFNULL(slstat_crs_earnings,0)) as crs_earnings']);
        $srch->addGroupBy("groupDate");
        $srch->addOrder("YEAR(slstat_date)", 'ASC');
        $srch->addOrder("MONTH(slstat_date)", 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'groupDate');
    }
}
