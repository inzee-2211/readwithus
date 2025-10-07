<?php

/**
 * This class is used to handle User Teach Language
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class UserTeachLanguage extends MyAppModel
{

    const DB_TBL = 'tbl_user_teach_languages';
    const DB_TBL_PREFIX = 'utlang_';

    protected $userId;
    protected $slot;

    /**
     * Initialize User Teach Language
     * 
     * @param int $userId
     * @param int $id
     */
    public function __construct(int $userId = 0, int $id = 0)
    {
        $this->userId = $userId;
        parent::__construct(static::DB_TBL, 'utlang_id', $id);
    }

    /**
     * Save Teach Lang
     * 
     * @param int $teachLangId
     * @return bool
     */
    public function saveTeachLang(int $teachLangId): bool
    {
        if (empty($this->userId)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $data = [
            'utlang_tlang_id' => $teachLangId,
            'utlang_user_id' => $this->userId
        ];
        $this->assignValues($data);
        if (!$this->addNew([], $data)) {
            return false;
        }
        return true;
    }

    /**
     * Get Search Object
     * 
     * @param int $langId
     * @return SearchBase
     */
    public static function getSearchObject(int $langId): SearchBase
    {
        $srch = new SearchBase(static::DB_TBL, 'utlang');
        $srch->joinTable(TeachLanguage::DB_TBL, 'INNER JOIN', 'tlang.tlang_id = utlang.utlang_tlang_id', 'tlang');
        $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'ustelgpr.ustelgpr_utlang_id = utlang.utlang_id', 'ustelgpr');
        $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id =' . $langId, 'tlanglang');
        return $srch;
    }

    /**
     * Get Teach Languages
     * 
     * @param int $langId
     * @return bool|array
     */
    public function getTeachLangs(int $langId)
    {
        $srch = static::getSearchObject($langId);
        $srch->addMultipleFields(['IFNULL(tlang_name, tlang_identifier) as tlang_name', 'tlang_id']);
        $srch->addCondition('utlang.utlang_user_id', '=', $this->userId);
        $srch->addCondition('ustelgpr.ustelgpr_price', '>', 0);
        $srch->addGroupBy('tlang.tlang_id');
        $srch->doNotCalculateRecords();
        $langs = FatApp::getDb()->fetchAll($srch->getResultSet(), 'tlang_id');
        if (empty($langs)) {
            $this->error = Label::getLabel('LBL_TEACHER_DOES_NOT_HAVE_LANGUAGE');
            return false;
        }
        return $langs;
    }

    /**
     * Get Language Slots

     * @param int $langId
     * @return array
     */
    public function getLangSlots(int $langId)
    {
        $srch = new SearchBase(static::DB_TBL, 'utlang');
        $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'ustelgpr.ustelgpr_utlang_id = utlang.utlang_id', 'ustelgpr');
        $srch->addMultipleFields(['utlang_tlang_id', 'GROUP_CONCAT(DISTINCT ustelgpr_slot ORDER BY ustelgpr_slot ASC)']);
        $srch->addCondition('utlang_user_id', '=', $this->userId);
        $srch->addCondition('ustelgpr.ustelgpr_price', '>', 0);
        $srch->addGroupBy('utlang_tlang_id');
        $srch->doNotCalculateRecords();
        $slots = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        $langs = TeachLanguage::getNames($langId, array_keys($slots));
        $langslots = [];
        foreach ($slots as $key => $value) {
            $langslots[$key] = ['name' => $langs[$key] ?? '', 'slots' => explode(',', $value)];
        }
        return $langslots;
    }

    /**
     * Get Price Slabs
     * 
     * @param int $langId
     * @param int $tlangId
     * @param int $slot
     * @return bool|array
     */
    public function getPriceSlabs(int $langId, int $tlangId, int $slot)
    {
        $srch = static::getSearchObject($langId);
        $srch->addCondition('tlang.tlang_id', '=', $tlangId);
        $srch->addCondition('ustelgpr.ustelgpr_price', '>', 0);
        $srch->addCondition('ustelgpr.ustelgpr_slot', '=', $slot);
        $srch->addCondition('utlang.utlang_user_id', '=', $this->userId);
        $srch->addMultipleFields([
            'IFNULL(tlang_name, tlang_identifier) as tlang_name',
            'CONCAT(ustelgpr_min_slab,"-", ustelgpr_max_slab) as minMaxKey',
            'ustelgpr_min_slab', 'ustelgpr_max_slab', 'ustelgpr_price',
        ]);
        $srch->addGroupBy('minMaxKey');
        $srch->doNotCalculateRecords();
        $slabs = FatApp::getDb()->fetchAll($srch->getResultSet(), 'minMaxKey');
        if (empty($slabs)) {
            $this->error = Label::getLabel('LBL_TEACHER_DOES_NOT_HAVE_PRICE_SLABS');
            return false;
        }
        ksort($slabs, SORT_NUMERIC);
        return $slabs;
    }

    /**
     * Get Quantity Price
     * 
     * @param int $langId
     * @param int $teachLangId
     * @param int $slot
     * @param int $qty
     * @return bool|array
     */
    public function getQuantityPrice(int $langId, int $teachLangId, int $slot, int $qty)
    {
        $srch = static::getSearchObject($langId);
        $srch->addCondition('tlang.tlang_id', '=', $teachLangId);
        $srch->addCondition('ustelgpr.ustelgpr_price', '>', 0);
        $srch->addCondition('ustelgpr.ustelgpr_slot', '=', $slot);
        $srch->addCondition('ustelgpr.ustelgpr_max_slab', '>=', $qty);
        $srch->addCondition('ustelgpr.ustelgpr_min_slab', '<=', $qty);
        $srch->addCondition('utlang.utlang_user_id', '=', $this->userId);
        $srch->addFld('ustelgpr.ustelgpr_price');
        $srch->doNotCalculateRecords();
        $price = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($price)) {
            $this->error = Label::getLabel('LBL_TEACHER_DOES_NOT_HAVE_PRICE_SLABS');
            return false;
        }
        return $price;
    }

    /**
     * Get Lesson Types
     * 
     * @param int $langId
     * @return array
     */
    public function getLessonTypes()
    {
        $types = [];
        $settings = UserSetting::getSettings($this->userId, ['user_trial_enabled']);
        if ($settings['user_trial_enabled'] == AppConstant::YES) {
            $types[Lesson::TYPE_FTRAIL] = Label::getLabel('TYPE_FREE_TRAIL');
        }
        $types[Lesson::TYPE_REGULAR] = Label::getLabel('TYPE_REGULAR_LESSON');
        $types[Lesson::TYPE_SUBCRIP] = Label::getLabel('TYPE_SUBSCRIPTION');
        return $types;
    }

    public function getSrchObject(int $langId = 0, $withPrice = false, $priceTablejoinType = 'LEFT JOIN')
    {
        $searchBase = new SearchBase(static::DB_TBL, 'utlang');
        $searchBase->addCondition('utlang.utlang_user_id', '=', $this->userId);
        $searchBase->joinTable(TeachLanguage::DB_TBL, 'INNER JOIN', 'tlang.tlang_id = utlang.utlang_tlang_id', 'tlang');
        if ($langId > 0) {
            $searchBase->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id =' . $langId, 'tlanglang');
        }
        if ($withPrice) {
            $searchBase->joinTable(TeachLangPrice::DB_TBL, $priceTablejoinType, 'ustelgpr.ustelgpr_utlang_id = utlang.utlang_id', 'ustelgpr');
        }
        return $searchBase;
    }

    /**
     * Remove Teach Languages
     * 
     * @param array $langIds
     * @return bool
     */
    public function removeTeachLang(array $langIds = []): bool
    {
        $query = 'DELETE ' . UserTeachLanguage::DB_TBL . ', ustelgpr FROM ' . UserTeachLanguage::DB_TBL .
                ' LEFT JOIN ' . TeachLangPrice::DB_TBL . ' ustelgpr ON ustelgpr.ustelgpr_utlang_id = utlang_id WHERE 1 = 1';
        if (!empty($this->userId)) {
            $query .= ' and utlang_user_id = ' . $this->userId;
        }
        if (!empty($langIds)) {
            $langIds = implode(",", $langIds);
            $query .= ' and utlang_tlang_id IN (' . $langIds . ')';
        }
        $db = FatApp::getDb();
        $db->query($query);
        if ($db->getError()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

}
