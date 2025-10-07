<?php

/**
 * This class is used to handle Teach Languages
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class TeachLanguage extends MyAppModel
{

    const DB_TBL = 'tbl_teach_languages';
    const DB_TBL_LANG = 'tbl_teach_languages_lang';
    const DB_TBL_PREFIX = 'tlang_';
    const PROFICIENCY_TOTAL_BEGINNER = 1;
    const PROFICIENCY_BEGINNER = 2;
    const PROFICIENCY_UPPER_BEGINNER = 3;
    const PROFICIENCY_INTERMEDIATE = 4;
    const PROFICIENCY_UPPER_INTERMEDIATE = 5;
    const PROFICIENCY_ADVANCED = 6;
    const PROFICIENCY_UPPER_ADVANCED = 7;
    const PROFICIENCY_NATIVE = 8;

    /**
     * Initialize Teach Language
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'tlang_id', $id);
    }

    /**
     * Get Search Object
     * 
     * @param int $langId
     * @param bool $active
     * @return SearchBase
     */
    public static function getSearchObject(int $langId = 0, bool $active = true): SearchBase
    {
        $srch = new SearchBase(static::DB_TBL, 'tlang');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id AND tlanglang_lang_id = ' . $langId, 'tlanglang');
        }
        if ($active == true) {
            $srch->addCondition('tlang.tlang_active', '=', AppConstant::ACTIVE);
        }
        return $srch;
    }

    /**
     * Get All Languages
     * 
     * @param int $langId
     * @param bool $active
     * @return array
     */
    public static function getAllLangs(int $langId, bool $active = false)
    {
        $srch = new SearchBase(static::DB_TBL, 'tlang');
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id AND tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
        $srch->addMultiplefields(['tlang_id', 'IFNULL(tlang_name, tlang_identifier) as tlang_name']);
        if ($active) {
            $srch->addCondition('tlang_active', '=', AppConstant::YES);
        }
        $srch->addOrder('tlang_order', 'ASC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get Lang By Id
     * 
     * @param int $tLangId
     * @param int $langId
     * @return string
     */
    public static function getLangById(int $tLangId, int $langId): string
    {
        $langId = ($langId > 0) ? $langId : MyUtility::getSiteLangId();
        $srch = new SearchBase(static::DB_TBL, 'tlang');
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id =' . $langId, 'tlanglang');
        $srch->addMultipleFields(['IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name']);
        $srch->addCondition('tlang_id', '=', $tLangId);
        $srch->doNotCalculateRecords();
        $teachLangs = FatApp::getDb()->fetch($srch->getResultSet());
        return $teachLangs['tlang_name'] ?? '';
    }

    /**
     * Get Names
     * 
     * @param int $langId
     * @param array $teachLangIds
     * @return array
     */
    public static function getNames(int $langId, array $teachLangIds): array
    {
        $teachLangIds = array_filter(array_unique($teachLangIds));
        if ($langId == 0 || empty($teachLangIds)) {
            return [];
        }
        $srch = new SearchBase(static::DB_TBL, 'tlang');
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id =' . $langId, 'tlanglang');
        $srch->addMultipleFields(['tlang.tlang_id', 'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name']);
        $srch->addDirectCondition('tlang.tlang_id IN (' . implode(',', FatUtility::int($teachLangIds)) . ')');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get Teach Languages
     * 
     * @param int $langId
     * @return array
     */
    public static function getTeachLanguages(int $langId): array
    {
        $srch = new SearchBase(TeachLanguage::DB_TBL, 'tlang');
        $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id AND tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
        $srch->addMultiplefields(['tlang_id', 'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name', 'tlang_slug']);
        $srch->addCondition('tlang_active', '=', AppConstant::YES);
        $srch->addDirectCondition('tlang_slug IS NOT NULL');
        $srch->addOrder('tlang_order', 'ASC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get Popular Languages
     * 
     * @param int $langId
     * @return array
     */
    public static function getPopularLangs(int $langId): array
    {
        $srch = new SearchBase(TeachLanguage::DB_TBL, 'tlang');
        $srch->joinTable(Order::DB_TBL_LESSON, 'INNER JOIN', 'ordles.ordles_tlang_id = tlang.tlang_id', 'ordles');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordles.ordles_id', 'orders');
        $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id AND tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
        $srch->addMultiplefields(['tlang_id', 'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name', 'tlang_slug']);
        $srch->addCondition('tlang.tlang_active', '=', AppConstant::YES);
        $srch->addOrder('count(orders.order_id)', 'DESC');
        $srch->addOrder('tlang_name', 'ASC');
        $srch->addGroupBy('tlang_id');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(6);
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'tlang_id');
    }

}
