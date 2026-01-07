<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');

class MySubscriptionsController extends DashboardController
{
    protected $userId;

    public function __construct($action)
    {
        parent::__construct($action);

        $this->userId     = UserAuth::getLoggedUserId();
        $this->siteLangId = MyUtility::getSiteLangId();

        if ($this->userId < 1) {
            FatUtility::dieWithError(Label::getLabel('MSG_SESSION_EXPIRED'));
        }
    }

    public function index()
    {
        // 1) Paid subscription (active/trialing + not expired)
        $subscription = $this->getActiveSubscription($this->userId);

        // 2) If no paid subscription, try quiz-only plan (free/active/trialing)
        if (empty($subscription)) {
            $subscription = $this->getQuizAccessSubscription($this->userId);
        }

        // Detect plan type
        $hasSubscription = !empty($subscription);
        $isQuizOnlyPlan  = $hasSubscription && (int)($subscription['spackage_is_quiz_only'] ?? 0) === 1;

        // Subjects ONLY for paid subscriptions
        $subjects = [];
        if ($hasSubscription && !$isQuizOnlyPlan) {
            $subjects = $this->getSubscriptionSubjects($subscription);
        }

        // Pricing page URL
        $pricingUrl = MyUtility::makeUrl('Pricing', 'index', [], CONF_WEBROOT_FRONT_URL);

        // Free-plan detection (your "free quiz plan")
        $status = $subscription['status'] ?? '';
        $isFreePlan = $hasSubscription && (
            $isQuizOnlyPlan ||
            $status === 'free' ||
            (int)($subscription['spackage_is_free'] ?? 0) === 1
        );

        $this->set('subscription', $subscription);
        $this->set('subjects', $subjects);
        $this->set('pricingUrl', $pricingUrl);
        $this->set('isFreePlan', $isFreePlan);
        $this->set('isQuizOnlyPlan', $isQuizOnlyPlan);

        $this->_template->render(true, true);
    }

    /**
     * Cancel subscription (AJAX / POST)
     */
    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }

        $userSubId = FatApp::getPostedData('user_sub_id', FatUtility::VAR_INT, 0);
        if ($userSubId < 1) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }

        $db = FatApp::getDb();

        $srch = new SearchBase('tbl_user_subscriptions', 'us');
        $srch->addMultipleFields([
            'us.usubs_id AS user_sub_id',
            'us.usubs_user_id AS user_id',
            'us.usubs_status AS status',
            'us.stripe_subscription_id AS gateway_sub_id',
        ]);
        $srch->addCondition('us.usubs_id', '=', $userSubId);
        $srch->addCondition('us.usubs_user_id', '=', $this->userId);
        $srch->addCondition('us.usubs_status', 'IN', ['active', 'trialing', 'free']);

        $row = $db->fetch($srch->getResultSet());
        if (empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_OR_EXPIRED_SUBSCRIPTION'));
        }

        $db->startTransaction();

        $updateArr = [
            'usubs_status'   => 'canceled',
            'usubs_end_date' => date('Y-m-d H:i:s'),
        ];

        if (!$db->updateFromArray(
            'tbl_user_subscriptions',
            $updateArr,
            [
                'smt'  => 'usubs_id = ? AND usubs_user_id = ?',
                'vals' => [$userSubId, $this->userId],
            ]
        )) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel('MSG_SOMETHING_WENT_WRONG'));
        }

        $db->commitTransaction();

        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SUBSCRIPTION_CANCELLED_SUCCESSFULLY'));
    }

    /**
     * Paid subscription: active/trialing + not expired
     */
    private function getActiveSubscription(int $userId): array
    {
        $db   = FatApp::getDb();
        $srch = new SearchBase('tbl_user_subscriptions', 'us');

        $srch->joinTable(
            'tbl_subscription_packages',
            'LEFT JOIN',
            'sp.spackage_id = us.usubs_spackage_id',
            'sp'
        );

        $srch->addMultipleFields([
            'us.usubs_id AS user_sub_id',
            'us.usubs_user_id AS user_id',
            'us.usubs_spackage_id AS package_id',
            'us.usubs_start_date AS start_date',
            'us.usubs_end_date AS end_date',
            'us.usubs_status AS status',
            'us.usubs_subject_ids',
            'sp.*',
        ]);

        $srch->addCondition('us.usubs_user_id', '=', $userId);
        $srch->addCondition('us.usubs_status', 'IN', ['active', 'trialing']);
        $srch->addDirectCondition('us.usubs_end_date >= NOW()');

        $srch->addOrder('us.usubs_start_date', 'DESC');
        $srch->setPageSize(1);

        $row = $db->fetch($srch->getResultSet());
        return $row ?: [];
    }

    /**
     * Quiz-only plan (free OR active/trialing) fetched with SAME JOIN + SAME aliases
     * so your view gets spackage_name/title and start_date/end_date/status.
     */
    private function getQuizAccessSubscription(int $userId): array
    {
        $db   = FatApp::getDb();
        $srch = new SearchBase('tbl_user_subscriptions', 'us');

        $srch->joinTable(
            'tbl_subscription_packages',
            'LEFT JOIN',
            'sp.spackage_id = us.usubs_spackage_id',
            'sp'
        );

        $srch->addMultipleFields([
            'us.usubs_id AS user_sub_id',
            'us.usubs_user_id AS user_id',
            'us.usubs_spackage_id AS package_id',
            'us.usubs_start_date AS start_date',
            'us.usubs_end_date AS end_date',
            'us.usubs_status AS status',
            'us.usubs_subject_ids',
            'sp.*',
        ]);

        $srch->addCondition('us.usubs_user_id', '=', $userId);

        // quiz-only package
        $srch->addCondition('sp.spackage_is_quiz_only', '=', 1);

        // statuses that represent quiz access
        $srch->addCondition('us.usubs_status', 'IN', ['free', 'active', 'trialing']);

        // If your free plan has NULL end_date, allow it
        $srch->addDirectCondition('(us.usubs_end_date IS NULL OR us.usubs_end_date = "0000-00-00 00:00:00" OR us.usubs_end_date >= NOW())');

        $srch->addOrder('us.usubs_start_date', 'DESC');
        $srch->setPageSize(1);

        $row = $db->fetch($srch->getResultSet());
        return $row ?: [];
    }

    private function getSubscriptionSubjects(array $subscription): array
    {
        if (empty($subscription) || empty($subscription['usubs_subject_ids'])) {
            return [];
        }

        $subjectIds = array_filter(array_map('intval', explode(',', $subscription['usubs_subject_ids'])));
        if (empty($subjectIds)) {
            return [];
        }

        $db   = FatApp::getDb();
        $srch = new SearchBase('course_subjects', 'cs');
        $srch->addMultipleFields(['cs.id', 'cs.subject', 'cs.level_id']);
        $srch->addCondition('cs.id', 'IN', $subjectIds);

        $rows = $db->fetchAll($srch->getResultSet());
        if (!$rows) {
            return [];
        }

        $subjects = [];
        foreach ($rows as $row) {
            $id    = (int)$row['id'];
            $title = $row['subject'] ?? ('Subject #' . $id);

            $subjects[] = [
                'subject_id'    => $id,
                'subject_title' => $title,
            ];
        }

        return $subjects;
    }
}
