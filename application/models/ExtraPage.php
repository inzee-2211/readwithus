<?php

/**
 * This class is used to handle Extra Page
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class ExtraPage extends MyAppModel
{

    const DB_TBL = 'tbl_extra_pages';
    const DB_TBL_PREFIX = 'epage_';
    const DB_TBL_LANG = 'tbl_extra_pages_lang';
    const DB_TBL_LANG_PREFIX = 'epagelang_';
    const BLOCK_PROFILE_INFO_BAR = 1;
    const BLOCK_WHY_US = 2;
    const BLOCK_BROWSE_TUTOR = 3;
    const BLOCK_CONTACT_BANNER_SECTION = 4;
    const BLOCK_CONTACT_LEFT_SECTION = 5;
    const BLOCK_APPLY_TO_TEACH_BENEFITS_SECTION = 6;
    const BLOCK_APPLY_TO_TEACH_FEATURES_SECTION = 7;
    const BLOCK_APPLY_TO_TEACH_BECOME_A_TUTOR_SECTION = 8;
    const BLOCK_APPLY_TO_TEACH_STATIC_BANNER = 9;
    const BLOCK_HOW_TO_START_LEARNING = 10;
    const BLOCK_WHY_WE_ARE_EFFECTIVE = 11;

    private $pageType;

    /**
     * Initialize Extra Page
     * 
     * @param int $epageId
     * @param type $pageType
     */
    public function __construct(int $epageId = 0, $pageType = '')
    {
        $this->pageType = $pageType;
        parent::__construct(static::DB_TBL, 'epage_id', $epageId);
    }

    public static function getSearchObject($langId = 0, $isActive = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'ep');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'ep_l.epagelang_epage_id = ep.epage_id and ep_l.epagelang_lang_id = ' . $langId, 'ep_l');
        }
        if ($isActive) {
            $srch->addCondition('epage_active', '=', AppConstant::ACTIVE);
        }
        return $srch;
    }

    /**
     * Get Block Content
     * 
     * @param int $pageType
     * @param int $langId
     * @return string
     */
    public static function getBlockContent(int $pageType, int $langId): string
    {
        $srch = self::getSearchObject($langId);
        $srch->addCondition('ep.epage_type', '=', $pageType);
        $srch->addMultipleFields([
            'epage_id', 'IFNULL(epage_label, epage_identifier) as epage_label',
            'epage_type', 'IFNULL(epage_content,"") as epage_content', 'epage_default_content'
        ]);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $resultData = FatApp::getDb()->fetch($rs);
        if (empty($resultData['epage_content'])) {
            return "";
        }
        return $resultData['epage_content'];
    }

}
