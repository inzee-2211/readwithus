<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class SubscriptionTrialService
{
    /**
     * Check if user has already used a trial.
     *
     * Default: GLOBAL trial (any package).
     * To switch to "per package", see commented line below.
     */
    public static function hasUsedTrial(int $userId, int $packageId = 0): bool
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            return false;
        }

        $db = FatApp::getDb();

        // Fast path: check user table flag
        $user = User::getAttributesById($userId, ['user_trial_used_at']);
        if (!empty($user['user_trial_used_at'])) {
            return true;
        }

        // Fallback for legacy data: check any trial subscription
        $srch = new SearchBase(UserSubscription::DB_TBL, 'us');
        $srch->addCondition('us.usubs_user_id', '=', $userId);
        $srch->addCondition('us.usubs_is_trial', '=', 1);

        // 👉 To switch to "per-package trial" in future, uncomment:
        // if ($packageId > 0) {
        //     $srch->addCondition('us.usubs_spackage_id', '=', $packageId);
        // }

        $srch->setPageSize(1);
        return (bool) FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Whether user is allowed to have a trial for this package.
     * - Package must have trial_days > 0
     * - user_trial_eligible = 1
     * - user must not have used trial before (global check)
     */
    public static function isUserEligibleForTrial(int $userId, array $package): bool
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            return false;
        }

        $trialDays = (int) ($package['spackage_trial_days'] ?? 0);
        if ($trialDays <= 0) {
            return false;
        }

        $user = User::getAttributesById($userId, ['user_trial_eligible', 'user_trial_used_at']);
        if (!$user) {
            return false;
        }

        if ((int) $user['user_trial_eligible'] !== 1) {
            return false;
        }

        if (self::hasUsedTrial($userId /*, (int)$package['spackage_id']*/)) {
            return false;
        }

        return true;
    }

    /**
     * Compute effective trial days to use.
     * 0 means "no trial".
     */
    public static function computeTrialDays(int $userId, array $package): int
    {
        $trialDays = (int) ($package['spackage_trial_days'] ?? 0);
        if ($trialDays <= 0) {
            return 0;
        }

        if (!self::isUserEligibleForTrial($userId, $package)) {
            return 0;
        }

        return $trialDays;
    }

    /**
     * Mark that user has now used their trial (first time).
     * Global "one trial per user".
     */
    public static function markTrialUsed(int $userId): void
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            return;
        }

        $db = FatApp::getDb();

        // Only set once, keep earliest time
        $user = User::getAttributesById($userId, ['user_trial_used_at']);
        if (!empty($user['user_trial_used_at'])) {
            return;
        }

        $db->updateFromArray(
            User::DB_TBL,
            [
                'user_trial_used_at' => date('Y-m-d H:i:s'),
                // optional: auto-disable further trial eligibility
                // 'user_trial_eligible' => 0,
            ],
            [
                'smt'  => 'user_id = ?',
                'vals' => [$userId],
            ]
        );
    }

    /**
     * Helper for UI (pricing page): 
     * returns array with info per package for current user (if logged in).
     */
    public static function getTrialInfoForPackage(int $userId, array $package): array
    {
        $trialDays          = (int) ($package['spackage_trial_days'] ?? 0);
        $userCanGetTrial    = self::isUserEligibleForTrial($userId, $package);
        $effectiveTrialDays = $userCanGetTrial ? $trialDays : 0;

        return [
            'package_trial_days'     => $trialDays,
            'user_can_trial'         => $userCanGetTrial,
            'effective_trial_days'   => $effectiveTrialDays,
            'trial_label'            => ($userCanGetTrial && $trialDays > 0)
                ? sprintf('%d-day free trial', $trialDays)
                : null,
        ];
    }
}
