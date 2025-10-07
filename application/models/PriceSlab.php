<?php

/**
 * This class is used to handle Price Slab
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class PriceSlab extends MyAppModel
{

    const DB_TBL = 'tbl_pricing_slabs';
    const DB_TBL_PREFIX = 'prislab_';

    /**
     * Initialize Price Slab
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'prislab_id', $id);
    }

    /**
     * Save Slab
     * 
     * @param int $min
     * @param int $max
     * @return bool
     */
    public function saveSlab(int $min, int $max, int $status): bool
    {
        $this->assignValues([
            'prislab_min' => $min,
            'prislab_max' => $max,
            'prislab_active' => $status,
        ]);
        return $this->save([], $this->getFlds());
    }

    /**
     * Get Search Object
     * 
     * @param bool $activeOnly
     * @return SearchBase
     */
    public static function getSearchObject(bool $activeOnly = false): SearchBase
    {
        $searchBase = new SearchBase(self::DB_TBL, 'ps');
        if ($activeOnly) {
            $searchBase->addCondition('prislab_active', '=', AppConstant::ACTIVE);
        }
        return $searchBase;
    }

    /**
     * Get All Slabs
     * 
     * @param bool $activeOnly
     * @param array $attr
     * @return array
     */
    public function getAllSlabs(): array
    {
        $srch = self::getSearchObject(true);
        $srch->addMultipleFields(['prislab_min as minSlab', 'prislab_max as maxSlab', 'CONCAT(prislab_min,"-",prislab_max) as minMaxKey']);
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Slap Collapse
     * 
     * @param int $min
     * @param int $max
     * @return bool
     */
    public function isSlapCollapse(int $min, int $max): bool
    {
        $srch = PriceSlab::getSearchObject();
        $srch->doNotCalculateRecords();
        $srch->addCondition('prislab_max', '>=', $min);
        $srch->addCondition('prislab_min', '<=', $max);
        $srch->addCondition('prislab_id', '!=', $this->mainTableRecordId);
        $srch->setPageSize(1);
        $slabData = FatApp::getDb()->fetch($srch->getResultSet());
        return (!empty($slabData));
    }

    /**
     * Get Min-Max Slab
     * 
     * @return array
     */
    public static function getMinAndMaxSlab(): array
    {
        $srch = PriceSlab::getSearchObject();
        $srch->addMultipleFields(['min(prislab_min) as minSlab', 'max(prislab_max) as maxSlab']);
        $srch->addCondition('prislab_active', '=', AppConstant::ACTIVE);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $minAndMaxSlab = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($minAndMaxSlab)) {
            return $minAndMaxSlab;
        }
        return ['minSlab' => 0, 'maxSlab' => 0];
    }
}
