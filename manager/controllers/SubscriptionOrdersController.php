<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
if (!function_exists('app_require')) {
    function app_require(string $relativePath): void
    {
        // Normalize incoming relative path and block traversal
        $rel = ltrim(str_replace(['\\', '//'], '/', $relativePath), '/');
        if (strpos($rel, '..') !== false) {
            throw new InvalidArgumentException('Invalid path: traversal not allowed.');
        }

        // Figure out project root and common bases
        $currentDir  = realpath(__DIR__ . '/..');              // .../dashboard OR .../manager
        $projectRoot = $currentDir ? realpath($currentDir . '/..') : null; // repo root
        $application = $projectRoot ? $projectRoot . '/application' : null;

        // Build candidate bases to try (in order)
        $bases = [];
        if ($application && is_dir($application)) $bases[] = $application;
        if ($projectRoot && is_dir($projectRoot)) $bases[] = $projectRoot;

        // Consider CONF_APPLICATION_PATH only if it’s a dir
        if (defined('CONF_APPLICATION_PATH') && is_dir(CONF_APPLICATION_PATH)) {
            $bases[] = rtrim(CONF_APPLICATION_PATH, "/\\");
            // Also try the parent of CONF_APPLICATION_PATH (sometimes points to project root)
            $bases[] = rtrim(dirname(CONF_APPLICATION_PATH), "/\\");
        }

        // Try both the raw relative and an application-prefixed variant
        $relatives = [$rel];
        if (strpos($rel, 'application/') !== 0) {
            $relatives[] = 'application/' . $rel;
        }

        // Attempt in order; return on first hit
        $attempted = [];
        foreach ($bases as $base) {
            foreach ($relatives as $r) {
                $full = rtrim($base, "/\\") . '/' . $r;
                $attempted[] = $full;
                if (is_file($full)) {
                    require_once $full;
                    return;
                }
            }
        }

        // Final: also try relative to THIS file’s grandparent (as a hard fallback)
        $fallback = realpath(__DIR__ . '/../../') ?: null; // project root guess
        if ($fallback) {
            foreach ($relatives as $r) {
                $full = rtrim($fallback, "/\\") . '/' . $r;
                $attempted[] = $full;
                if (is_file($full)) {
                    require_once $full;
                    return;
                }
            }
        }

        // No luck – show all attempts for quick diagnosis
        throw new RuntimeException(
            "Required file not found. Tried:\n- " . implode("\n- ", $attempted)
        );
    }
}

class SubscriptionOrdersController extends AdminBaseController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
        // Use whatever your project already has, these are usually available:
        $this->objPrivilege->canViewOrders();
    }

    /**
     * List all subscriptions (existing functionality)
     */
    public function index()
    {
        $db   = FatApp::getDb();
        $post = FatApp::getPostedData();

        $srch = new SearchBase(UserSubscription::DB_TBL, 'us');

        $srch->joinTable(User::DB_TBL, 'LEFT JOIN', 'u.user_id = us.usubs_user_id', 'u');
        $srch->joinTable(SubscriptionPackage::DB_TBL, 'LEFT JOIN', 'p.spackage_id = us.usubs_spackage_id', 'p');

        $srch->addMultipleFields([
            'us.*',
            'u.user_first_name',
            'u.user_last_name',
            'u.user_email',
            'p.spackage_name',
            'p.spackage_level_id',
        ]);

        $status = FatApp::getPostedData('status', FatUtility::VAR_STRING, '');
        if ($status !== '') {
            $srch->addCondition('us.usubs_status', '=', $status);
        }

        $email = FatApp::getPostedData('email', FatUtility::VAR_STRING, '');
        if ($email !== '') {
            $srch->addDirectCondition(
                'u.user_email LIKE ' . $db->quoteVariable('%' . $email . '%')
            );
        }

        $srch->addOrder('us.usubs_id', 'DESC');

        $rows = $db->fetchAll($srch->getResultSet()) ?: [];

        $this->set('rows', $rows);
        $this->_template->render();
    }

    /**
     * Detailed subscription view
     * - package + level
     * - selected subjects
     * - unlocked courses
     * - Stripe info
     */
   public function view($id)
{
    $this->objPrivilege->canViewOrders();

    $id = FatUtility::int($id);
    if ($id <= 0) {
        FatUtility::exitWithErrorCode(404);
    }

    $db   = FatApp::getDb();
    $srch = new SearchBase(UserSubscription::DB_TBL, 'us');
    $srch->joinTable(User::DB_TBL, 'LEFT JOIN', 'u.user_id = us.usubs_user_id', 'u');
    $srch->joinTable(SubscriptionPackage::DB_TBL, 'LEFT JOIN', 'p.spackage_id = us.usubs_spackage_id', 'p');
    $srch->joinTable('course_levels', 'LEFT JOIN', 'cl.id = p.spackage_level_id', 'cl');

    $srch->addCondition('us.usubs_id', '=', $id);
    $srch->addMultipleFields([
        'us.*',
        'u.user_first_name',
        'u.user_last_name',
        'u.user_email',
        'p.spackage_name',
        'p.spackage_subject_limit',
        'p.spackage_level_id',
        'p.spackage_price_monthly',
        'p.spackage_price_yearly',
        'cl.level_name',
    ]);
    $srch->setPageSize(1);

    $row = $db->fetch($srch->getResultSet());
    if (!$row) {
        FatUtility::exitWithErrorCode(404);
    }

    /* ---------- Selected subjects ---------- */
    $subjectIds = [];
    $subjects   = [];

    if (!empty($row['usubs_subject_ids'])) {
        $subjectIds = array_filter(array_map('intval', explode(',', $row['usubs_subject_ids'])));
    }

    if (!empty($subjectIds)) {
        $subSrch = new SearchBase('course_subjects', 'cs');
        $subSrch->addCondition('cs.id', 'IN', $subjectIds);
        $subSrch->addMultipleFields(['cs.id', 'cs.subject', 'cs.level_id']);
        $subSrch->addOrder('cs.subject', 'ASC');

        $rs = $subSrch->getResultSet();
        if ($rs === false) {
            // optional: log or show the error
            // Message::addErrorMessage('Failed to fetch subjects: ' . $db->getError());
            $subjects = [];
        } else {
            $subjects = $db->fetchAll($rs) ?: [];
        }
    }

    /* ---------- Unlocked courses (simple rule: any course in selected subjects) ---------- */
       /* ---------- Unlocked courses (simple rule: any course in selected subjects) ---------- */
     /* ---------- Unlocked courses using CourseSearch (same logic as dashboard) ---------- */
    $courses = [];
    if (!empty($subjectIds)) {
        // 1) Derive allowedLevels from the same subjects (we already have $subjects above)
        $allowedLevels = [];
        if (!empty($subjects)) {
            foreach ($subjects as $s) {
                if (!empty($s['level_id'])) {
                    $allowedLevels[] = (int)$s['level_id'];
                }
            }
        }
        $allowedLevels = array_unique(array_filter($allowedLevels));

        // 2) Build CourseSearch exactly like dashboard/CoursesController::search(),
        //    but we only care about this one subscription + its user.
        $learnerId = (int)$row['usubs_user_id'];

        $srch = new CourseSearch($this->siteLangId, $learnerId, User::LEARNER);
        $srch->applyPrimaryConditions();

        // Filter by subscription subjects
        $srch->addCondition('course.course_subject_id', 'IN', $subjectIds);

        // Join course_subjects to enforce level consistency
        $srch->joinTable('course_subjects', 'LEFT JOIN', 'sub.id = course.course_subject_id', 'sub');

        if (!empty($allowedLevels)) {
            $srch->addCondition('sub.level_id', 'IN', $allowedLevels);
        }

        // We don't have extra filters here (keyword/status), so we skip applySearchConditions().
        $srch->addSearchListingFields();

        // Admin view doesn’t need progress/order joins – just the unlocked courses list
        $srch->addOrder('course.course_id', 'DESC');

        // Fetch a reasonably large page
        $srch->setPageSize(500);
        $srch->setPageNumber(1);

        $courses = $srch->fetchAndFormat();
    }



    /* ---------- Stripe info ---------- */
    $stripeData = [];
    if (!empty($row['stripe_subscription_id'])) {
      app_require('library/services/StripeClientFactory.php');
$stripe = StripeClientFactory::client();
        try {
            // Expand latest_invoice to get last amount paid
            $subscription = $stripe->subscriptions->retrieve(
                $row['stripe_subscription_id'],
                ['expand' => ['latest_invoice']]
            );

            $latestInvoice = $subscription->latest_invoice ?? null;

            $stripeData = [
                'status'                => $subscription->status,
                'current_period_start'  => !empty($subscription->current_period_start)
                    ? date('Y-m-d H:i:s', $subscription->current_period_start) : null,
                'current_period_end'    => !empty($subscription->current_period_end)
                    ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                'next_billing_at'       => !empty($subscription->current_period_end)
                    ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                'latest_amount'         => $latestInvoice ? $latestInvoice->amount_paid / 100 : null,
                'latest_currency'       => $latestInvoice ? strtoupper($latestInvoice->currency) : null,
                'stripe_customer_id'    => $row['stripe_customer_id'],
                'stripe_customer_url'   => !empty($row['stripe_customer_id'])
                    ? 'https://dashboard.stripe.com/customers/' . $row['stripe_customer_id'] : '',
                'stripe_subscription_url' => 'https://dashboard.stripe.com/subscriptions/' .
                    $row['stripe_subscription_id'],
            ];
        } catch (Exception $e) {
            $stripeData = [
                'error' => $e->getMessage(),
            ];
        }
    }

    /* ---------- Packages for change-plan dropdown ---------- */
   /* ---------- Packages for change-plan dropdown (with level) ---------- */
$pkgSrch = new SearchBase(SubscriptionPackage::DB_TBL, 'p');
$pkgSrch->joinTable('course_levels', 'LEFT JOIN', 'cl.id = p.spackage_level_id', 'cl');
$pkgSrch->addCondition('p.spackage_status', '=', 1);
$pkgSrch->addMultipleFields([
    'p.*',
    'cl.level_name',
]);
$pkgSrch->addOrder('cl.level_name', 'ASC');
$pkgSrch->addOrder('p.spackage_price_monthly', 'ASC');

$rs = $pkgSrch->getResultSet();
$allPackages = $rs ? $db->fetchAll($rs) : [];


    $this->set('row', $row);
    $this->set('subjects', $subjects);
    $this->set('courses', $courses);
    $this->set('stripeData', $stripeData);
    $this->set('allPackages', $allPackages);

    $this->_template->render();
}


    /**
     * Cancel subscription (existing functionality, unchanged)
     */
    public function cancel($id)
    {
        $this->objPrivilege->canEditOrders();

        $id = FatUtility::int($id);
        if ($id <= 0) {
            FatUtility::dieJsonError('Invalid request.');
        }

        $db  = FatApp::getDb();
        $sub = $db->fetch($db->query(
            'SELECT * FROM ' . UserSubscription::DB_TBL . ' WHERE usubs_id = ' . $id . ' LIMIT 1'
        ));

        if (!$sub) {
            FatUtility::dieJsonError('Subscription not found.');
        }

        if (!empty($sub['stripe_subscription_id'])) {
           app_require('library/services/StripeClientFactory.php');
$stripe = StripeClientFactory::client();
            try {
                $stripe->subscriptions->cancel($sub['stripe_subscription_id'], []);
            } catch (\Exception $e) {
                // You can log this, but don't block admin:
                // error_log($e->getMessage());
            }
        }

        $updated = $db->updateFromArray(
            UserSubscription::DB_TBL,
            [
                'usubs_status'   => 'canceled',
                'usubs_end_date' => date('Y-m-d H:i:s'),
            ],
            ['smt' => 'usubs_id = ?', 'vals' => [$id]]
        );

        if (!$updated) {
            FatUtility::dieJsonError($db->getError());
        }

        FatUtility::dieJsonSuccess('Subscription cancelled successfully.');
    }

    /**
     * Change plan (upgrade / downgrade) from admin.
     * POST: usubs_id, new_spackage_id
     */
    public function changePlan()
    {
        $this->objPrivilege->canEditOrders();

        $usubsId      = FatApp::getPostedData('usubs_id', FatUtility::VAR_INT, 0);
        $newPackageId = FatApp::getPostedData('new_spackage_id', FatUtility::VAR_INT, 0);

        if ($usubsId <= 0 || $newPackageId <= 0) {
            FatUtility::dieJsonError('Invalid request.');
        }

        $db = FatApp::getDb();

        // Current subscription
        $sub = $db->fetch($db->query(
            'SELECT * FROM ' . UserSubscription::DB_TBL . ' WHERE usubs_id = ' . (int)$usubsId . ' LIMIT 1'
        ));
        if (!$sub) {
            FatUtility::dieJsonError('Subscription not found.');
        }
        if (empty($sub['stripe_subscription_id'])) {
            FatUtility::dieJsonError('Stripe subscription ID missing.');
        }

        // New package
        $newPkg = SubscriptionPackage::getById((int)$newPackageId);
        if (!$newPkg) {
            FatUtility::dieJsonError('Target package not found.');
        }

        // Decide which Stripe price to use (monthly vs yearly) by checking current Stripe subscription interval
      app_require('library/services/StripeClientFactory.php');
$stripe = StripeClientFactory::client();

        try {
            $stripeSub = $stripe->subscriptions->retrieve(
                $sub['stripe_subscription_id'],
                ['expand' => ['items.data.price']]
            );
        } catch (Exception $e) {
            FatUtility::dieJsonError('Stripe error: ' . $e->getMessage());
        }

        if (empty($stripeSub->items->data[0])) {
            FatUtility::dieJsonError('No items found on Stripe subscription.');
        }

        $currentItem  = $stripeSub->items->data[0];
        $interval     = $currentItem->price->recurring->interval ?? 'month';

        // choose correct price ID from package
        if ($interval === 'year') {
            $newPriceId = $newPkg['stripe_price_id_yearly'] ?? '';
        } else {
            $newPriceId = $newPkg['stripe_price_id_monthly'] ?? '';
        }

        if (empty($newPriceId)) {
            FatUtility::dieJsonError('Stripe price ID missing on target package.');
        }

        // Update subscription on Stripe (prorated)
        try {
            $updatedStripeSub = $stripe->subscriptions->update(
                $sub['stripe_subscription_id'],
                [
                    'items' => [
                        [
                            'id'    => $currentItem->id,
                            'price' => $newPriceId,
                        ],
                    ],
                    'proration_behavior' => 'create_prorations',
                ]
            );
        } catch (Exception $e) {
            FatUtility::dieJsonError('Stripe error: ' . $e->getMessage());
        }

        // Keep DB basic info in sync (webhooks will handle deeper stuff if you added them)
        $updateArr = [
            'usubs_spackage_id' => $newPackageId,
        ];

        if (!empty($updatedStripeSub->current_period_end)) {
            $updateArr['usubs_end_date'] = date('Y-m-d H:i:s', $updatedStripeSub->current_period_end);
        }

        $db->updateFromArray(
            UserSubscription::DB_TBL,
            $updateArr,
            ['smt' => 'usubs_id = ?', 'vals' => [$usubsId]]
        );

        FatUtility::dieJsonSuccess('Plan updated successfully. Stripe subscription has been changed.');
    }

    /**
     * Simple subscription analytics dashboard
     */
    public function stats()
    {
        $this->objPrivilege->canViewOrders();

        $db = FatApp::getDb();

        // 1) Active subscriptions by plan
        $sqlActiveByPlan = "
            SELECT p.spackage_name, COUNT(*) AS total
            FROM " . UserSubscription::DB_TBL . " us
            JOIN " . SubscriptionPackage::DB_TBL . " p ON p.spackage_id = us.usubs_spackage_id
            WHERE us.usubs_status = 'active'
              AND us.usubs_end_date >= NOW()
            GROUP BY p.spackage_id, p.spackage_name
            ORDER BY total DESC
        ";
        $activeByPlan = $db->fetchAll($db->query($sqlActiveByPlan)) ?: [];

        // 2) MRR estimate – sum monthly price for active subs
        $sqlMrr = "
            SELECT SUM(p.spackage_price_monthly) AS mrr
            FROM " . UserSubscription::DB_TBL . " us
            JOIN " . SubscriptionPackage::DB_TBL . " p ON p.spackage_id = us.usubs_spackage_id
            WHERE us.usubs_status = 'active'
              AND us.usubs_end_date >= NOW()
        ";
        $mrrRow = $db->fetch($db->query($sqlMrr)) ?: ['mrr' => 0];
        $mrr    = (float)$mrrRow['mrr'];

        // Date ranges for "this month" and "last month"
        $today        = date('Y-m-d');
        $firstThis    = date('Y-m-01');
        $firstLast    = date('Y-m-01', strtotime('-1 month', strtotime($firstThis)));
        $firstPrev2   = date('Y-m-01', strtotime('-2 month', strtotime($firstThis)));
        $endLast      = date('Y-m-t', strtotime('-1 month'));

        // 3) New subs this month vs last month
        $sqlThisMonth = "
            SELECT COUNT(*) AS cnt
            FROM " . UserSubscription::DB_TBL . "
            WHERE usubs_start_date >= '" . $db->quoteVariable($firstThis) . "'
              AND usubs_start_date <= '" . $db->quoteVariable($today) . "'
        ";
        $rowThis = $db->fetch($db->query(str_replace("'", "", $sqlThisMonth))) ?: ['cnt' => 0];
        $newThis = (int)$rowThis['cnt'];

        $sqlLastMonth = "
            SELECT COUNT(*) AS cnt
            FROM " . UserSubscription::DB_TBL . "
            WHERE usubs_start_date >= '" . $firstLast . "'
              AND usubs_start_date <= '" . $endLast . "'
        ";
        $rowLast = $db->fetch($db->query($sqlLastMonth)) ?: ['cnt' => 0];
        $newLast = (int)$rowLast['cnt'];

        // 4) Cancellations (last 30 days)
        $date30 = date('Y-m-d H:i:s', strtotime('-30 days'));
        $sqlCanceled30 = "
            SELECT COUNT(*) AS cnt
            FROM " . UserSubscription::DB_TBL . "
            WHERE usubs_status = 'canceled'
              AND usubs_end_date >= '" . $date30 . "'
        ";
        $rowCanceled = $db->fetch($db->query($sqlCanceled30)) ?: ['cnt' => 0];
        $canceled30  = (int)$rowCanceled['cnt'];

        // A very rough churn estimate: cancelled last 30 / active now
        $sqlActiveCount = "
            SELECT COUNT(*) AS cnt
            FROM " . UserSubscription::DB_TBL . "
            WHERE usubs_status = 'active'
              AND usubs_end_date >= NOW()
        ";
        $rowActive = $db->fetch($db->query($sqlActiveCount)) ?: ['cnt' => 0];
        $activeNow = (int)$rowActive['cnt'];

        $churnRate = 0;
        if ($activeNow > 0) {
            $churnRate = round(($canceled30 / $activeNow) * 100, 1);
        }

        $this->set('activeByPlan', $activeByPlan);
        $this->set('mrr', $mrr);
        $this->set('newThisMonth', $newThis);
        $this->set('newLastMonth', $newLast);
        $this->set('canceled30', $canceled30);
        $this->set('churnRate', $churnRate);

        $this->_template->render();
    }
}
