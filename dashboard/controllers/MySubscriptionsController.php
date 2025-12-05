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

    /**
     * My Subscription page for learner
     */
    public function index()
    {
        $subscription = $this->getActiveSubscription($this->userId);
        $subjects     = $this->getSubscriptionSubjects($subscription);

        $this->set('subscription', $subscription);
        $this->set('subjects', $subjects);

        // Pricing page URL (for upgrade / browse plans)
        $pricingUrl = MyUtility::makeUrl('Pricing', 'index', [], CONF_WEBROOT_FRONT_URL);
        $this->set('pricingUrl', $pricingUrl);

        $this->_template->render(true,true);
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

        // Ensure this subscription belongs to logged in user and is active
        $srch = new SearchBase('tbl_user_subscriptions', 'us');
        $srch->addMultipleFields([
            'us.usubs_id AS user_sub_id',
            'us.usubs_user_id AS user_id',
            'us.usubs_status AS status',
            'us.stripe_subscription_id AS gateway_sub_id',
        ]);
        $srch->addCondition('us.usubs_id', '=', $userSubId);
        $srch->addCondition('us.usubs_user_id', '=', $this->userId);
        // $srch->addCondition('us.usubs_status', '=', 'active');
        $srch->addCondition('us.usubs_status', 'IN', ['active', 'trialing']);


        $rs  = $srch->getResultSet();
        $row = $db->fetch($rs);

        if (empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_OR_EXPIRED_SUBSCRIPTION'));
        }

        $db->startTransaction();

        // If you integrate Stripe / gateway:
        // SubscriptionBilling::cancelAtPeriodEnd($row['gateway_sub_id']);

        // Your enum: 'active','trialing','past_due','canceled',...
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

        // Access is revoked because status != active
        $db->commitTransaction();

        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SUBSCRIPTION_CANCELLED_SUCCESSFULLY'));
    }

    /**
     * Fetch most recent active subscription for learner
     */
    private function getActiveSubscription(int $userId): array
    {
        $db   = FatApp::getDb();
        $srch = new SearchBase('tbl_user_subscriptions', 'us');

        // Join with packages; adjust if your table / column names differ
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
            'us.usubs_subject_ids',            // comma-separated IDs
            'sp.*',                            // all package fields
        ]);

        $srch->addCondition('us.usubs_user_id', '=', $userId);
   $srch->addCondition('us.usubs_status', 'IN', ['active', 'trialing']);
$srch->addDirectCondition('us.usubs_end_date >= NOW()');


        $srch->addOrder('us.usubs_start_date', 'DESC');
        $srch->setPageSize(1);

        $rs  = $srch->getResultSet();
        $row = $db->fetch($rs);

        return $row ?: [];
    }

    /**
     * Get subjects from usubs_subject_ids (comma-separated IDs)
     * and resolve titles from course_subjects table:
     *   id, subject, level_id, created_at
     */
    private function getSubscriptionSubjects(array $subscription): array
    {
        if (empty($subscription) || empty($subscription['usubs_subject_ids'])) {
            return [];
        }

        $subjectIds = array_filter(
            array_map('intval', explode(',', $subscription['usubs_subject_ids']))
        );
        if (empty($subjectIds)) {
            return [];
        }

        $db   = FatApp::getDb();
        $srch = new SearchBase('course_subjects', 'cs');

        // match IDs to course_subjects.id
        $srch->addMultipleFields([
            'cs.id',
            'cs.subject',
            'cs.level_id',
        ]);
        $srch->addCondition('cs.id', 'IN', $subjectIds);

        $rs = $srch->getResultSet();
        if (!$rs) {
            return [];
        }

        $rows = $db->fetchAll($rs);
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
