<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class PricingController extends MyAppController
{
   public function index()
    {
        $db = FatApp::getDb();

        // 1. Load levels that actually have at least one active package
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

        // 2. Determine selected level (from ?level_id, else default to first level)
        $selectedLevelId = 0;
        if (!empty($levels)) {
            $selectedLevelId = isset($_GET['level_id']) ? FatUtility::int($_GET['level_id']) : 0;
            if ($selectedLevelId <= 0) {
                $selectedLevelId = (int)$levels[0]['id']; // default: first available level
            }
        }

        // 3. Fetch plans only for this level
        $plans = SubscriptionPackage::getActiveAll($selectedLevelId);

        // 4. Detect active subscription for this user (if logged in)
        $hasActiveSubscription = false;
        $currentPackageId      = 0;
        $currentMonthlyPrice   = 0.0;

        if (UserAuth::isUserLogged()) {
            $activeSub = UserSubscription::getActiveByUser($this->siteUserId);
            if ($activeSub) {
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
                    if ($currentPkgRow) {
                        $currentMonthlyPrice = (float)$currentPkgRow['spackage_price_monthly'];
                    }
                }
            }
        }

        // 5. Normalize each plan + mark "current" or "upgrade"
        foreach ($plans as &$p) {
            $p['name']        = $p['spackage_name'];
            $p['price_month'] = (float)$p['spackage_price_monthly'];
            $p['price_year']  = (float)$p['spackage_price_yearly'];
             $p['trial_days']  = (int)$p['spackage_trial_days']; 

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
 $userDetail = [];
        if (UserAuth::isUserLogged()) {
            $userDetail = User::getDetail($this->siteUserId);
        }

        $this->set('plans', $plans);
        $this->set('levels', $levels);
        $this->set('selectedLevelId', $selectedLevelId);
        $this->set('siteCurrency', $this->siteCurrency);

        // Pass subscription context to the view
        $this->set('hasActiveSubscription', $hasActiveSubscription);
        $this->set('currentPackageId', $currentPackageId);
        $this->set('userDetail', $userDetail);

        $this->_template->render(true, true, 'pricing/index.php');
    }
}
