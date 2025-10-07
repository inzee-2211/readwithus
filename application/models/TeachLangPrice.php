<?php

/**
 * This class is used to handle Teach Lang Price
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class TeachLangPrice extends FatModel
{

    const DB_TBL = 'tbl_user_teach_lang_prices';
    const DB_TBL_PREFIX = 'ustelgpr_';

    protected $slot;
    protected $userTeachLangId;

    /**
     * Initialize Teach Lang Price
     * 
     * @param int $slot
     * @param int $userTeachLangId
     */
    public function __construct(int $slot = 0, int $userTeachLangId = 0)
    {
        parent::__construct();
        $this->slot = $slot;
        $this->userTeachLangId = $userTeachLangId;
    }

    /**
     * Save Teach Lang Price
     * 
     * @param int $minSlab
     * @param int $maxSlab
     * @param float $price
     * @return bool
     */
    public function saveTeachLangPrice(int $minSlab, int $maxSlab, float $price): bool
    {
        $data = [
            'ustelgpr_utlang_id' => $this->userTeachLangId,
            'ustelgpr_slot' => $this->slot,
            'ustelgpr_min_slab' => $minSlab,
            'ustelgpr_max_slab' => $maxSlab,
            'ustelgpr_price' => $price
        ];

        $record = new TableRecord(self::DB_TBL);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Delete Teach Slots
     * 
     * @param array $slots
     * @return bool
     */
    public function deleteTeachSlots(array $slots): bool
    {
        if (empty($slots)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
        }
        if (!FatApp::getDb()->query('DELETE  FROM ' . self::DB_TBL . ' WHERE ustelgpr_slot IN (' . implode(",", $slots) . ')')) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Delete All User Price
     * 
     * @param int $teacherId
     * @return bool
     */
    public function deleteAllUserPrice(int $teacherId): bool
    {
        $query = 'DELETE ' . TeachLangPrice::DB_TBL . ' FROM ' . TeachLangPrice::DB_TBL . ' INNER JOIN ' .
                UserTeachLanguage::DB_TBL . ' utl ON utl.utlang_id = ustelgpr_utlang_id and utl.utlang_user_id = ' . $teacherId;
        if (!FatApp::getDb()->query($query)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Get Teaching Slots
     * 
     * @param int $userId
     * @return array
     */
    public function getTeachingSlots(int $userId = 0): array
    {
        $srch = new SearchBase(static::DB_TBL, 'ustelgpr');
        $srch->joinTable(UserTeachLanguage::DB_TBL, 'INNER JOIN', 'utl.utlang_id = ustelgpr_utlang_id', 'utl');
        $srch->joinTable(TeachLanguage::DB_TBL, 'INNER JOIN', 'tlanguage.tlang_id = utl.utlang_tlang_id', 'tlanguage');
        $srch->addFld(['ustelgpr_slot as slot', 'ustelgpr_slot']);
        $srch->addCondition('ustelgpr_price', '>', 0);
        $srch->addCondition('utlang_tlang_id', '>', 0);
        $srch->addCondition('tlang_active', '=', AppConstant::YES);
        $srch->addCondition('utlang_user_id', '=', $userId);
        $srch->addGroupBy('ustelgpr_slot');
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Slap Collapse
     * 
     * @param int $teacherId
     * @param int $min
     * @param int $max
     * @param array $teachLangIds
     * @return bool
     */
    public static function isSlapCollapse(int $teacherId, int $min, int $max, array $teachLangIds = []): bool
    {
        $srch = new SearchBase(static::DB_TBL, 'ustelgpr');
        $srch->joinTable(UserTeachLanguage::DB_TBL, 'INNER JOIN', 'utl.utlang_id = ustelgpr_utlang_id', 'utl');
        $srch->addCondition('ustelgpr_max_slab', '>=', $min);
        $srch->addCondition('ustelgpr_min_slab', '<=', $max);
        $srch->addCondition('utl.utlang_user_id', '=', $teacherId);
        if (!empty($teachLangIds)) {
            $srch->addCondition('utl.utlang_tlang_id', 'IN', $teachLangIds);
        }
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $slabData = FatApp::getDb()->fetch($srch->getResultSet());
        return (!empty($slabData));
    }

}
