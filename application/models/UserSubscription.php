<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class UserSubscription
{
    public const DB_TBL = 'tbl_user_subscriptions';

    /**
     * Get current active subscription for user.
     * Now treats both 'active' AND 'trialing' as "active", and requires end_date >= NOW().
     */
    public static function getActiveByUser(int $userId)
    {
        $db     = FatApp::getDb();
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            return null;
        }

        $srch = new SearchBase(self::DB_TBL, 'u');
        $srch->addCondition('u.usubs_user_id', '=', $userId);
        // 👇 include trialing as current subscription
        $srch->addCondition('u.usubs_status', 'IN', ['active', 'trialing']);
        $srch->addCondition('u.usubs_end_date', '>=', date('Y-m-d H:i:s'));
        $srch->addOrder('u.usubs_id', 'DESC');
        $srch->setPageSize(1);

        $rs = $srch->getResultSet();
        return $db->fetch($rs);
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
                'allowed'      => false,
                'reason'       => 'NO_ACTIVE_SUB',
                'message'      => Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'),
                'subscription' => null,
            ];
        }

        // 2) Fetch course + subject_id
        $course = $db->fetch($db->query("
            SELECT c.course_id, c.course_title, c.course_subject_id
            FROM tbl_courses c
            WHERE c.course_id = " . (int) $courseId . " 
              AND c.course_deleted = 0
            LIMIT 1
        "));

        if (!$course) {
            return [
                'allowed'      => false,
                'reason'       => 'COURSE_NOT_FOUND',
                'message'      => Label::getLabel('LBL_COURSE_NOT_FOUND'),
                'subscription' => $sub,
            ];
        }

        $subjectId = (int) $course['course_subject_id'];

        // 3) Check if subject in subscription's selected subjects
        $selectedCsv = (string) $sub['usubs_subject_ids']; // comma-separated
        $selected    = array_filter(array_map('intval', explode(',', $selectedCsv)));

        if (!in_array($subjectId, $selected, true)) {
            return [
                'allowed'      => false,
                'reason'       => 'SUBJECT_NOT_SELECTED',
                'message'      => Label::getLabel('LBL_SUBJECT_NOT_INCLUDED_IN_YOUR_SUBSCRIPTION'),
                'subscription' => $sub,
            ];
        }

        return [
            'allowed'      => true,
            'reason'       => 'OK',
            'message'      => Label::getLabel('LBL_ACCESS_GRANTED'),
            'subscription' => $sub,
        ];
    }

    /**
     * Legacy helper used by SubscriptionController::success()
     * (You can keep this for backward compatibility while webhooks take over.)
     */
    public static function createOrActivate(array $data): bool
    {
        $rec = new TableRecord(self::DB_TBL);
        foreach ($data as $k => $v) {
            $rec->setFldValue($k, $v);
        }
        return $rec->addNew();
    }

    /**
     * Update subjects CSV for a subscription.
     */
    public static function updateSubjects(int $usubsId, string $csv): bool
    {
        return FatApp::getDb()->updateFromArray(
            self::DB_TBL,
            ['usubs_subject_ids' => $csv],
            ['smt' => 'usubs_id = ?', 'vals' => [$usubsId]]
        );
    }

    /**
     * Mark a subscription canceled by its Stripe subscription ID.
     * Used from Stripe webhooks (customer.subscription.deleted).
     */
    public static function markCanceledByStripeSub(string $stripeSubId): bool
    {
        return FatApp::getDb()->updateFromArray(
            self::DB_TBL,
            ['usubs_status' => 'canceled'],
            ['smt' => 'stripe_subscription_id = ?', 'vals' => [$stripeSubId]]
        );
    }

    /**
     * Mark a subscription expired by its Stripe subscription ID.
     * Used from Stripe webhooks (invoice.payment_failed, etc.).
     */
    public static function markExpiredByStripeSub(string $stripeSubId): bool
    {
        return FatApp::getDb()->updateFromArray(
            self::DB_TBL,
            ['usubs_status' => 'expired'],
            ['smt' => 'stripe_subscription_id = ?', 'vals' => [$stripeSubId]]
        );
    }

    /**
     * 🔥 NEW:
     * Upsert (insert or update) subscription from Stripe data.
     * Used when checkout.session.completed fires.
     *
     * @return int usubs_id (0 on failure)
     */
    public static function upsertFromStripe(
        int $userId,
        int $spackageId,
        string $subjectIdsCsv,
        string $billing,
        \Stripe\Subscription $subscription,
        bool $isTrial,
        ?string $stripeCustomerId
    ): int {
        $db = FatApp::getDb();

        $userId     = FatUtility::int($userId);
        $spackageId = FatUtility::int($spackageId);

        if ($userId < 1 || $spackageId < 1) {
            return 0;
        }

        $stripeSubId = (string) $subscription->id;
        $status      = (string) $subscription->status;

        $trialStart = $subscription->trial_start ?: null;
        $trialEnd   = $subscription->trial_end ?: null;

        $currentStart = $subscription->current_period_start ?: null;
        $currentEnd   = $subscription->current_period_end ?: null;

        // Decide our start/end
        if ($isTrial && $trialStart) {
            $startTs = $trialStart;
        } else {
            $startTs = $currentStart ?: time();
        }

        if ($isTrial && $trialEnd) {
            $endTs = $trialEnd;
        } else {
            $endTs = $currentEnd ?: null;
        }

        $data = [
            'usubs_user_id'          => $userId,
            'usubs_spackage_id'      => $spackageId,
            'usubs_subject_ids'      => $subjectIdsCsv,
            'stripe_subscription_id' => $stripeSubId,
            'stripe_customer_id'     => $stripeCustomerId ?: null,
            'usubs_status'           => $status,
            'usubs_is_trial'         => $isTrial ? 1 : 0,
            'usubs_start_date'       => date('Y-m-d H:i:s', $startTs),
            'usubs_end_date'         => $endTs ? date('Y-m-d H:i:s', $endTs) : null,
        ];

        // Check if we already have a row for this Stripe subscription
        $row = $db->fetch($db->query(
            'SELECT usubs_id FROM ' . self::DB_TBL .
            ' WHERE stripe_subscription_id = ' . $db->quoteVariable($stripeSubId) .
            ' LIMIT 1'
        ));

        if ($row) {
            $usubsId = (int) $row['usubs_id'];
            $db->updateFromArray(
                self::DB_TBL,
                $data,
                ['smt' => 'usubs_id = ?', 'vals' => [$usubsId]]
            );
            return $usubsId;
        }

        // Insert new
        $rec = new TableRecord(self::DB_TBL);
        $rec->assignValues($data);
        if (!$rec->addNew()) {
            // optional: log $rec->getError()
            return 0;
        }

        return (int) $rec->getId();
    }

    /**
     * 🔥 NEW:
     * Sync status + dates from Stripe subscription updates.
     * Called on customer.subscription.updated.
     */
    public static function syncStatusFromStripe(\Stripe\Subscription $subscription): void
    {
        $db          = FatApp::getDb();
        $stripeSubId = (string) $subscription->id;

        $row = $db->fetch($db->query(
            'SELECT usubs_id, usubs_user_id FROM ' . self::DB_TBL .
            ' WHERE stripe_subscription_id = ' . $db->quoteVariable($stripeSubId) .
            ' LIMIT 1'
        ));
        if (!$row) {
            return; // no local subscription, nothing to sync
        }

        $status       = (string) $subscription->status;
        $currentStart = $subscription->current_period_start ?: null;
        $currentEnd   = $subscription->current_period_end ?: null;

        $update = [
            'usubs_status' => $status,
        ];
        if ($currentStart) {
            $update['usubs_start_date'] = date('Y-m-d H:i:s', $currentStart);
        }
        if ($currentEnd) {
            $update['usubs_end_date'] = date('Y-m-d H:i:s', $currentEnd);
        }

        $db->updateFromArray(
            self::DB_TBL,
            $update,
            [
                'smt'  => 'usubs_id = ?',
                'vals' => [(int) $row['usubs_id']],
            ]
        );

        // Keep course access in sync whenever subscription changes (trial → active, active → canceled, etc.)
        SubscriptionEnrollment::syncForUser((int) $row['usubs_user_id']);
    }
}
