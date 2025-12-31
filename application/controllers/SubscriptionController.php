<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
require_once CONF_APPLICATION_PATH . 'library/services/StripeClientFactory.php';
require_once CONF_APPLICATION_PATH . 'library/services/SubscriptionEnrollment.php';
require_once CONF_APPLICATION_PATH . 'library/services/SubscriptionTrialService.php';

class SubscriptionController extends MyAppController
{
    /* -----------------------------
     * Public: Pricing page (list of plans)
     * ----------------------------- */
    public function pricing()
    {
        $plans = SubscriptionPackage::getActiveAll();
        foreach ($plans as &$p) {
            $p['id']          = (int)$p['spackage_id']; // ensure you have 'id' for URLs
            $p['name']        = $p['spackage_name'];
            $p['price_month'] = (float)$p['spackage_price_monthly'];
            $p['price_year']  = (float)$p['spackage_price_yearly'];
            $p['is_quiz_only'] = !empty($p['spackage_is_quiz_only']);

        }
        $this->set('plans', $plans);
        $this->set('siteCurrency', $this->siteCurrency);
        $this->_template->render(true, true, 'pricing/index.php');
    }
public function activateFreeQuizPlan($spackageId)
{
    // must be logged in
    if (!UserAuth::isUserLogged()) {
        Message::addErrorMessage(Label::getLabel('LBL_PLEASE_LOGIN'));
        // optionally store redirect
        $_SESSION['redirect_url'] = MyUtility::makeUrl('Subscription', 'activateFreeQuizPlan', [$spackageId]);
        FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
    }

    $spackageId = FatUtility::int($spackageId);

    $pkg = SubscriptionPackage::getById($spackageId);
    if (!$pkg) {
        FatUtility::exitWithErrorCode(404);
    }

    // Ensure this is the quiz-only package
    $isQuizOnly = !empty($pkg['spackage_is_quiz_only']);
    if (!$isQuizOnly) {
        Message::addErrorMessage('Invalid free plan.');
        FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
    }

    // If user already has a paid active/trialing subscription, no need for free plan
    $paid = UserSubscription::getActiveByUser($this->siteUserId);
    if ($paid) {
        Message::addMessage('You already have an active subscription. Quizzes are already unlocked.');
        FatApp::redirectUser(MyUtility::makeUrl('Courses'));
    }

    // If user already has quiz-access subscription (including free), do nothing
    $quizSub = UserSubscription::getQuizAccessByUser($this->siteUserId);
    if ($quizSub) {
        Message::addMessage('Free quiz access is already active.');
        FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
    }

    // Create a free subscription row (no Stripe)
    $data = [
        'usubs_user_id'          => $this->siteUserId,
        'usubs_spackage_id'      => $spackageId,
        'usubs_billing_interval' => 'free',
        'usubs_subject_ids'      => '',
        'stripe_subscription_id' => null,
        'stripe_customer_id'     => null,
        'usubs_status'           => 'free',
        'usubs_is_trial'         => 0,
        'usubs_start_date'       => date('Y-m-d H:i:s'),
        'usubs_end_date'         => null,
    ];

    UserSubscription::createOrActivate($data);

    Message::addMessage('Free Quiz Plan activated! You now have unlimited quiz access.');
    FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
}

    /* -----------------------------
     * Step 1: Select subjects (after package click)
     * URL: /subscription/select-subjects/{spackageId}/{billing}
     * ----------------------------- */

    
   public function selectSubjects($spackageId, $billing = 'monthly')
{
    if (!UserAuth::isUserLogged()) {
        Message::addErrorMessage(Label::getLabel('LBL_PLEASE_LOGIN'));
        FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
    }

    $spackageId = FatUtility::int($spackageId);

    // 🔒 NEW: Prevent buying the exact same package again
    $activeSub = UserSubscription::getActiveByUser($this->siteUserId);
    if ($activeSub && (int)$activeSub['usubs_spackage_id'] === $spackageId) {
        // Change 'Courses' to your actual dashboard route if different
        Message::addMessage('You already have this subscription. We\'ve taken you to your courses.');
        FatApp::redirectUser(MyUtility::makeUrl('Courses'));
    }

    $pkg = SubscriptionPackage::getById($spackageId);
    if (!$pkg) {
        FatUtility::exitWithErrorCode(404);
    }

    $limit   = (int)$pkg['spackage_subject_limit'];
    $levelId = (int)$pkg['spackage_level_id']; // 🔹 this is the key

    if ($levelId <= 0) {
        Message::addErrorMessage('This package is not linked to any level. Please contact support.');
        FatApp::redirectUser(MyUtility::makeUrl('Pricing'));
    }

    // Store subscription intent in session
    $_SESSION['subscription_intent'] = [
        'spackage_id' => $spackageId,
        'billing'     => (string)$billing,
        'limit'       => $limit,
        'level_id'    => $levelId,
    ];

    // Fetch subjects for this level only
    $srch = new SearchBase('course_subjects', 's');
    $srch->addMultipleFields([
        's.id AS subject_id',
        's.subject AS subject_name',
        's.level_id AS level_id',
    ]);
    $srch->addCondition('s.level_id', '=', $levelId);
    $srch->addOrder('s.subject', 'ASC');

    $db       = FatApp::getDb();
    $rs       = $srch->getResultSet();
    $subjects = $db->fetchAll($rs) ?: [];

    $selected = [];

    $this->sets(compact('subjects', 'selected', 'limit', 'pkg'));
    $this->_template->render(true, false, 'subscription/subject-select.php');
}

    /* -----------------------------
     * Step 2: AJAX – save subjects & redirect to checkout
     * URL: POST /subscription/process-subscription
     * ----------------------------- */
    public function processSubscription()
    {
        if (!UserAuth::isUserLogged()) {
            $this->jsonError('Not logged in');
        }

        $subIntent = $_SESSION['subscription_intent'] ?? null;
        if (!$subIntent || empty($subIntent['spackage_id'])) {
            $this->jsonError('Subscription session expired. Please start over.');
        }

        // Read posted IDs safely (no VAR_ARRAY in your build)
        $subjectIds = FatApp::getPostedData('subject_ids', null, []);
        if (!is_array($subjectIds)) { $subjectIds = []; }
        $subjectIds = array_values(array_unique(array_map('intval', $subjectIds)));

        if (count($subjectIds) > (int)$subIntent['limit']) {
            $this->jsonError('You can select up to ' . (int)$subIntent['limit'] . ' subjects.');
        }

        // Save selected subjects for checkout
        $_SESSION['subscription_intent']['selected_subjects'] = $subjectIds;

        // Return a FLAT JSON with redirectUrl
        $redirect = MyUtility::makeUrl('Subscription', 'checkout', [
            (int)$subIntent['spackage_id'],
            (string)$subIntent['billing'],
        ]);
        $this->jsonOk('Subjects selected.', ['redirectUrl' => $redirect]);
    }

    /* -----------------------------
     * Step 3: Create Stripe Session & render stripe-pay.php (auto-redirect)
     * URL: /subscription/checkout/{spackageId}/{billing}
     * ----------------------------- */
    public function checkout($spackageId, $billing = 'monthly')
{
    if (!UserAuth::isUserLogged()) {
        Message::addErrorMessage(Label::getLabel('LBL_PLEASE_LOGIN'));
        FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
    }

    $subIntent = $_SESSION['subscription_intent'] ?? null;
    if (!$subIntent || (int) $subIntent['spackage_id'] !== (int) $spackageId) {
        Message::addErrorMessage('Please select subjects first.');
        FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'selectSubjects', [$spackageId, $billing]));
    }

    $pkg = SubscriptionPackage::getById(FatUtility::int($spackageId));
    if (!$pkg) {
        FatUtility::exitWithErrorCode(404);
    }

    $priceId = ($billing === 'yearly')
        ? ($pkg['stripe_price_id_yearly'] ?? '')
        : ($pkg['stripe_price_id_monthly'] ?? '');

    if (!$priceId) {
        Message::addErrorMessage('Stripe price ID missing for selected billing period.');
        FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
    }

    // 🔹 Compute dynamic trial days for THIS user + package
    $trialDays = SubscriptionTrialService::computeTrialDays($this->siteUserId, $pkg);
    $isTrial   = ($trialDays > 0);

    $subjectIdsCsv = implode(',', $subIntent['selected_subjects'] ?? []);

    $stripe  = StripeClientFactory::client();

    // Subscription metadata (for Stripe subscription object)
    $subscriptionData = [
        'metadata' => [
            'user_id'     => (string) $this->siteUserId,
            'spackage_id' => (string) $pkg['spackage_id'],
            'billing'     => (string) $billing,
            'subject_ids' => $subjectIdsCsv,
            'is_trial'    => $isTrial ? '1' : '0',
        ],
    ];
    if ($trialDays > 0) {
        $subscriptionData['trial_period_days'] = $trialDays;
    }

    $session = $stripe->checkout->sessions->create([
        'mode'       => 'subscription',
        'line_items' => [
            [
                'price'    => $priceId,
                'quantity' => 1,
            ],
        ],
        // You can still let Stripe create a customer automatically via email
        'customer_email' => $this->siteUser['user_email'] ?? null,

        'success_url' => MyUtility::makeFullUrl('Subscription', 'success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => MyUtility::makeFullUrl('Subscription', 'cancel'),

        // Metadata on Checkout Session (for checkout.session.completed event)
        'metadata'    => [
            'user_id'     => (string) $this->siteUserId,
            'spackage_id' => (string) $pkg['spackage_id'],
            'billing'     => (string) $billing,
            'subject_ids' => $subjectIdsCsv,
            'is_trial'    => $isTrial ? '1' : '0',
        ],

        // Subscription-level data (trial + subscription metadata)
        'subscription_data' => $subscriptionData,
    ]);

    // Used by payment/stripe-pay.php
    $this->set('stripe', ['publishable_key' => StripeClientFactory::publishableKey()]);
    $this->set('sessionId', $session->id);

    // Keep the existing "order" structure for template compatibility
    $this->set('order', [
        'order_net_amount'    => 0,
        'order_id'            => 0,
        'order_currency_code' => $this->siteCurrency['currency_code'],
    ]);

    $this->_template->render(true, false, 'payment/stripe-pay.php');
}

    /* -----------------------------
     * Step 4: Success – activate in DB
     * ----------------------------- */
    public function success()
{
    if (!UserAuth::isUserLogged()) {
        FatUtility::exitWithErrorCode(403);
    }

    $sessionId = $_GET['session_id'] ?? '';
    if (!$sessionId) {
        FatUtility::exitWithErrorCode(404);
    }

    // Optionally just fetch session for reassurance/logging
    $stripe  = StripeClientFactory::client();
    try {
        $session = $stripe->checkout->sessions->retrieve($sessionId);
    } catch (\Exception $e) {
        // Do not fail hard; just show a generic message
    }

    /**
     * At this point, our StripeWebhookController should already have
     * created/updated tbl_user_subscriptions and called SubscriptionEnrollment::syncForUser().
     *
     * So here we only show a friendly message and redirect.
     */
    Message::addMessage('Thank you! If your payment was successful, your subscription (or trial) is now active.');
    FatApp::redirectUser(MyUtility::makeUrl('Courses'));
}

//    public function success()
// {
//     if (!UserAuth::isUserLogged()) {
//         FatUtility::exitWithErrorCode(403);
//     }

//     $sessionId = $_GET['session_id'] ?? '';
//     if (!$sessionId) {
//         FatUtility::exitWithErrorCode(404);
//     }

//     $stripe       = StripeClientFactory::client();
//     $session      = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['subscription']]);
//     $subscription = $session->subscription;

//     $spackageId = (int)($session->metadata->spackage_id ?? 0);
//     $subjectIds = (string)($session->metadata->subject_ids ?? '');
//     $start      = date('Y-m-d H:i:s', $subscription->current_period_start);
//     $end        = date('Y-m-d H:i:s', $subscription->current_period_end);

//     // Create/update user subscription
//     UserSubscription::createOrActivate([
//         'usubs_user_id'           => $this->siteUserId,
//         'usubs_spackage_id'       => $spackageId,
//         'usubs_subject_ids'       => $subjectIds,
//         'stripe_subscription_id'  => $subscription->id,
//         'stripe_customer_id'      => (string)$session->customer,
//         'usubs_status'            => 'active',
//         'usubs_start_date'        => $start,
//         'usubs_end_date'          => $end,
//     ]);

//     // 🔥 CRITICAL FIX: Sync all subscription courses to order_courses
//     SubscriptionEnrollment::syncForUser($this->siteUserId);

//     unset($_SESSION['subscription_intent']);

//     Message::addMessage('Subscription activated successfully! All courses are now available.');
//     FatApp::redirectUser(MyUtility::makeUrl('Courses'));
// }

    /* -----------------------------
     * Manage subjects (after purchase)
     * ----------------------------- */
    public function manageSubjects()
    {
        if (!UserAuth::isUserLogged()) {
            FatUtility::exitWithErrorCode(403);
        }

        $sub = UserSubscription::getActiveByUser($this->siteUserId);
        if (!$sub) {
            Message::addErrorMessage('No active subscription found.');
            FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
        }

        $pkg   = SubscriptionPackage::getById((int)$sub['usubs_spackage_id']);
        $limit = (int)$pkg['spackage_subject_limit'];

        $srch = new SearchBase('course_subjects', 's');
        $srch->addMultipleFields(['s.id AS subject_id', 's.subject AS subject_name']);
        $srch->addOrder('s.subject', 'ASC');

        $db = FatApp::getDb();
        $rs = $srch->getResultSet();
        if ($rs === false) {
            Message::addErrorMessage('Failed to fetch subjects: ' . $db->getError());
            FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
            return;
        }
        $subjects = $db->fetchAll($rs) ?: [];

        $selected = array_filter(array_map('trim', explode(',', (string)$sub['usubs_subject_ids'])));

        $this->sets(compact('subjects', 'selected', 'limit', 'sub'));
        $this->_template->render(true, false, 'subscription/subject-select.php');
    }

    public function saveSubjects()
    {
        if (!UserAuth::isUserLogged()) { $this->jsonError('Not logged in'); }

        $sub = UserSubscription::getActiveByUser($this->siteUserId);
        if (!$sub) { $this->jsonError('No active subscription'); }

        $pkg   = SubscriptionPackage::getById((int)$sub['usubs_spackage_id']);
        $limit = (int)$pkg['spackage_subject_limit'];

        $ids = FatApp::getPostedData('subject_ids', null, []);
        if (!is_array($ids)) { $ids = []; }
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if (count($ids) > $limit) {
            $this->jsonError('You can select up to ' . $limit . ' subjects.');
        }

        $csv = implode(',', $ids);
        if (!UserSubscription::updateSubjects((int)$sub['usubs_id'], $csv)) {
            $this->jsonError('Failed to save subjects.');
        }

        $this->jsonOk('Subjects saved.');
    }

    /* -----------------------------
     * Helpers: flat JSON
     * ----------------------------- */
    private function jsonOk(string $msg = 'OK', array $extra = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 1, 'msg' => $msg] + $extra, JSON_UNESCAPED_SLASHES);
        die;
    }

    private function jsonError(string $msg = 'Error', array $extra = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 0, 'msg' => $msg] + $extra, JSON_UNESCAPED_SLASHES);
        die;
    }
}
