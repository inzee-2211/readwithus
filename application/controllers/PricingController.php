<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class PricingController extends MyAppController
{
    public function index()
    {
        $db = FatApp::getDb();

        /* -----------------------------
         * 1) Load levels that have at least one active package
         * ----------------------------- */
        $srch = new SearchBase('course_levels', 'cl');
        $srch->joinTable(
            SubscriptionPackage::DB_TBL,
            'INNER JOIN',
            'p.spackage_level_id = cl.id AND p.spackage_status = 1',
            'p'
        );
        $srch->addMultipleFields(['cl.id', 'cl.level_name']);
        $srch->addGroupBy('cl.id');
        $srch->addOrder('cl.level_name', 'ASC');

        $levels = $db->fetchAll($srch->getResultSet()) ?: [];

        /* -----------------------------
         * 2) Determine selected level
         * ----------------------------- */
        $selectedLevelId = 0;
        if (!empty($levels)) {
            $selectedLevelId = isset($_GET['level_id']) ? FatUtility::int($_GET['level_id']) : 0;
            if ($selectedLevelId <= 0) {
                $selectedLevelId = (int)$levels[0]['id'];
            }
        }

        /* -----------------------------
         * 3) Fetch plans only for this level
         * ----------------------------- */
        $plans = SubscriptionPackage::getActiveAll($selectedLevelId);

        /* -----------------------------
         * 4) Subscription + quiz access context
         * ----------------------------- */
        $hasActiveSubscription = false;
        $currentPackageId      = 0;
        $currentMonthlyPrice   = 0.0;

        $hasQuizAccess         = false;
        $currentQuizPackageId  = 0;
        $quizAccessStatus      = '';

        if (UserAuth::isUserLogged()) {

            // ✅ quiz access (free OR paid) — must run even if no paid subscription
            $quizSub = UserSubscription::getQuizAccessByUser($this->siteUserId);
            if (!empty($quizSub)) {
                $hasQuizAccess        = true;
                $currentQuizPackageId = (int)$quizSub['usubs_spackage_id'];
                $quizAccessStatus     = (string)$quizSub['usubs_status'];
            }

            // ✅ paid subscription
            $activeSub = UserSubscription::getActiveByUser($this->siteUserId);
            if (!empty($activeSub)) {
                $hasActiveSubscription = true;
                $currentPackageId      = (int)$activeSub['usubs_spackage_id'];

                // Try to get current plan price from the plans we already fetched
                foreach ($plans as $row) {
                    if ((int)$row['spackage_id'] === $currentPackageId) {
                        $currentMonthlyPrice = (float)$row['spackage_price_monthly'];
                        break;
                    }
                }

                // Fallback: fetch directly if not present in this level’s list
                if ($currentMonthlyPrice <= 0 && $currentPackageId > 0) {
                    $currentPkgRow = SubscriptionPackage::getById($currentPackageId);
                    if (!empty($currentPkgRow)) {
                        $currentMonthlyPrice = (float)$currentPkgRow['spackage_price_monthly'];
                    }
                }
            }
        }

        /* -----------------------------
         * 5) Normalize each plan + mark "current" or "upgrade"
         * ----------------------------- */
        foreach ($plans as &$p) {
            // these are useful in the view (even if you re-map there too)
            $p['name']        = $p['spackage_name'];
            $p['price_month'] = (float)$p['spackage_price_monthly'];
            $p['price_year']  = (float)$p['spackage_price_yearly'];
            $p['trial_days']  = (int)($p['spackage_trial_days'] ?? 0);

            $isCurrent = ($hasActiveSubscription && (int)$p['spackage_id'] === $currentPackageId);

            $isUpgrade = (
                $hasActiveSubscription
                && !$isCurrent
                && $currentMonthlyPrice > 0
                && (float)$p['spackage_price_monthly'] > $currentMonthlyPrice
            );

            $p['is_current'] = $isCurrent;
            $p['is_upgrade'] = $isUpgrade;
        }
        unset($p);

        /* -----------------------------
         * 6) User detail (trial eligibility etc.)
         * ----------------------------- */
        $userDetail = [];
        if (UserAuth::isUserLogged()) {
            $userDetail = User::getDetail($this->siteUserId);
        }

        /* -----------------------------
         * 7) Pass data to view
         * ----------------------------- */
        $this->set('plans', $plans);
        $this->set('levels', $levels);
        $this->set('selectedLevelId', $selectedLevelId);
        $this->set('siteCurrency', $this->siteCurrency);

        $this->set('hasActiveSubscription', $hasActiveSubscription);
        $this->set('currentPackageId', $currentPackageId);
        $this->set('userDetail', $userDetail);

        $this->set('hasQuizAccess', $hasQuizAccess);
        $this->set('currentQuizPackageId', $currentQuizPackageId);
        $this->set('quizAccessStatus', $quizAccessStatus);

        $this->_template->render(true, true, 'pricing/index.php');
    }
}
