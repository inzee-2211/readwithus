<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

class FreeQuizzesController extends MyAppController
{
    protected $userId;
    protected $siteLangId;

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
        // Only allow if user has free quiz plan access
        $subscription = $this->getQuizAccessSubscription($this->userId);

        if (empty($subscription)) {
            // if no free quiz access -> send to pricing (or show error)
            FatApp::redirectUser(MyUtility::makeUrl('MySubscriptions'));
        }

        // IMPORTANT: front base URL (visitor side), used by home.js for api.php + quizizz redirect
        $frontBase = defined('CONF_WEBROOT_FRONT_URL') && CONF_WEBROOT_FRONT_URL
            ? CONF_WEBROOT_FRONT_URL
            : CONF_WEBROOT_URL;

        // quizizz URL must be front-side, not dashboard
        $quizizzUrl = MyUtility::makeUrl('Quizizz', 'index', [], $frontBase);

        $this->set('frontBase', $frontBase);
        $this->set('quizizzUrl', $quizizzUrl);
        $this->set('subscription', $subscription);

        $this->_template->render(true, true);
    }

    /**
     * Quiz-only plan access (free OR active/trialing), same idea as your MySubscriptionsController method.
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
            'sp.*',
        ]);

        $srch->addCondition('us.usubs_user_id', '=', $userId);
$srch->addDirectCondition('(sp.spackage_is_quiz_only = 1 OR sp.spackage_is_quiz_only IS NULL OR sp.spackage_is_quiz_only = 0)');
        $srch->addCondition('us.usubs_status', 'IN', ['free', 'active', 'trialing']);
        $srch->addDirectCondition('(us.usubs_end_date IS NULL OR us.usubs_end_date = "0000-00-00 00:00:00" OR us.usubs_end_date >= NOW())');

        $srch->addOrder('us.usubs_start_date', 'DESC');
        $srch->setPageSize(1);

        $row = $db->fetch($srch->getResultSet());
        return $row ?: [];
    }
}
