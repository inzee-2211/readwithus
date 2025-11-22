<?php
class UserSubscription
{
    public const DB_TBL = 'tbl_user_subscriptions';

public static function getActiveByUser(int $userId)
{
    $srch = new SearchBase(self::DB_TBL, 'u');
    $srch->addCondition('u.usubs_user_id', '=', FatUtility::int($userId));
    $srch->addCondition('u.usubs_status', '=', 'active');
    $srch->addCondition('u.usubs_end_date', '>=', date('Y-m-d H:i:s'));
    $srch->addOrder('u.usubs_id', 'DESC');
    $srch->setPageSize(1);
    return FatApp::getDb()->fetch($srch->getResultSet());
}




    /**
     * Check if user subscription allows access to this course
     * Returns: ['allowed' => bool, 'reason' => string, 'message' => string, 'subscription' => array|null]
     */
    public static function canAccessCourse(int $userId, int $courseId): array
    {
        $db = FatApp::getDb();

        // 1) Active subscription?
        $sub = self::getActiveByUser($userId);
        if (!$sub) {
            return [
                'allowed'     => false,
                'reason'      => 'NO_ACTIVE_SUB',
                'message'     => Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'),
                'subscription'=> null,
            ];
        }

        // 2) Fetch course + subject_id
        $course = $db->fetch($db->query("
            SELECT c.course_id, c.course_title, c.course_subject_id
            FROM tbl_courses c
            WHERE c.course_id = " . (int)$courseId . " 
              AND c.course_deleted = 0
            LIMIT 1
        "));

        if (!$course) {
            return [
                'allowed'     => false,
                'reason'      => 'COURSE_NOT_FOUND',
                'message'     => Label::getLabel('LBL_COURSE_NOT_FOUND'),
                'subscription'=> $sub,
            ];
        }

        $subjectId = (int)$course['course_subject_id'];

        // 3) Check if subject in subscription's selected subjects
        $selectedCsv = (string)$sub['usubs_subject_ids']; // comma-separated
        $selected    = array_filter(array_map('intval', explode(',', $selectedCsv)));

        if (!in_array($subjectId, $selected, true)) {
            return [
                'allowed'     => false,
                'reason'      => 'SUBJECT_NOT_SELECTED',
                'message'     => Label::getLabel('LBL_SUBJECT_NOT_INCLUDED_IN_YOUR_SUBSCRIPTION'),
                'subscription'=> $sub,
            ];
        }

        return [
            'allowed'     => true,
            'reason'      => 'OK',
            'message'     => Label::getLabel('LBL_ACCESS_GRANTED'),
            'subscription'=> $sub,
        ];
    }
    // public static function getActiveByUser(int $userId)
    // {
    //     $srch = new SearchBase(self::DB_TBL, 'u');
    //     $srch->addCondition('u.usubs_user_id', '=', FatUtility::int($userId));
    //     $srch->addCondition('u.usubs_status', '=', 'active');
    //     $srch->addCondition('u.usubs_end_date', '>=', date('Y-m-d H:i:s'));
    //     $srch->addOrder('u.usubs_id', 'DESC');
    //     $srch->setPageSize(1);
    //     return FatApp::getDb()->fetch($srch->getResultSet());
    // }
    // public static function canAccessCourse(int $userId, int $courseId): array
    // {
    //     $db = FatApp::getDb();

    //     // 1) Find active sub
    //     $sub = self::getActiveByUser($userId);
    //     if (!$sub) {
    //         return ['allowed' => false, 'reason' => 'NO_SUB', 'redirect' => MyUtility::makeUrl('Subscription', 'pricing')];
    //     }

    //     // 2) Get this course's subject_id (adjust table/columns if different)
    //     $course = $db->fetch(FatApp::getDb()->query("
    //         SELECT c.course_id, c.subject_id
    //         FROM courses c
    //         WHERE c.course_id = " . (int)$courseId . " LIMIT 1
    //     "));
    //     if (!$course) {
    //         return ['allowed' => false, 'reason' => 'COURSE_NOT_FOUND', 'redirect' => MyUtility::makeUrl('Courses')];
    //     }

    //     $subjectId = (int)$course['subject_id'];

    //     // 3) Is the course subject in user's selected subjects?
    //     $selectedCsv = (string)$sub['usubs_subject_ids'];
    //     $selected    = array_filter(array_map('intval', explode(',', $selectedCsv)));

    //     if (in_array($subjectId, $selected, true)) {
    //         return ['allowed' => true, 'reason' => 'OK'];
    //     }

    //     // Not included: send to manage subjects
    //     return [
    //         'allowed'  => false,
    //         'reason'   => 'SUBJECT_NOT_SELECTED',
    //         'redirect' => MyUtility::makeUrl('Subscription', 'manageSubjects'),
    //     ];
    // }
    public static function createOrActivate(array $data): bool
    {
        $rec = new TableRecord(self::DB_TBL);
        foreach ($data as $k => $v) { $rec->setFldValue($k, $v); }
        return $rec->addNew();
    }

    public static function updateSubjects(int $usubsId, string $csv): bool
    {
        return FatApp::getDb()->updateFromArray(
            self::DB_TBL, ['usubs_subject_ids'=>$csv],
            ['smt'=>'usubs_id = ?', 'vals'=>[$usubsId]]
        );
    }

    public static function markCanceledByStripeSub(string $stripeSubId): bool
    {
        return FatApp::getDb()->updateFromArray(
            self::DB_TBL, ['usubs_status'=>'canceled'],
            ['smt'=>'stripe_subscription_id = ?', 'vals'=>[$stripeSubId]]
        );
    }

    public static function markExpiredByStripeSub(string $stripeSubId): bool
    {
        return FatApp::getDb()->updateFromArray(
            self::DB_TBL, ['usubs_status'=>'expired'],
            ['smt'=>'stripe_subscription_id = ?', 'vals'=>[$stripeSubId]]
        );
    }
}
