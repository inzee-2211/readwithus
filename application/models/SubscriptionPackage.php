<?php
class SubscriptionPackage
{
    public const DB_TBL = 'tbl_subscription_packages';

     public static function getActiveAll(int $levelId = 0): array
    {
        $srch = new SearchBase(self::DB_TBL, 'p');
        $srch->addCondition('p.spackage_status', '=', 1);

        if ($levelId > 0) {
            $srch->addCondition('p.spackage_level_id', '=', $levelId);
        }

        $srch->addOrder('p.spackage_price_monthly', 'ASC');
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs) ?: [];
    }

    public static function getById(int $id)
    {
        $srch = new SearchBase(self::DB_TBL, 'p');
        $srch->addCondition('p.spackage_id', '=', FatUtility::int($id));
        $srch->addCondition('p.spackage_status', '=', 1);
        $srch->setPageSize(1);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }
}
