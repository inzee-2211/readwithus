<?php

/**
 * This class is used to filter abusive content from blog posts
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Abusive extends MyAppModel
{

    const DB_TBL = 'tbl_abusive_words';
    const DB_TBL_PREFIX = 'abusive_';

    /**
     * Initialize Abusive Class
     * 
     * @param int $abusiveId
     */
    public function __construct(int $abusiveId = 0)
    {
        parent::__construct(static::DB_TBL, 'abusive_id', $abusiveId);
    }

    /**
     * Validate Content
     * 
     * @param string $textToBeCheck     Text to checked for abusive content
     * @param array $abusiveTxtArr      Abusive content array
     * @return bool                     It will return false if text has one or more abusive words
     */
    public static function validateContent(string $textToBeCheck, array &$abusiveTxtArr = []): bool
    {
        $srch = new SearchBase(static::DB_TBL, 'abusive');
        $srch->joinTable(Language::DB_TBL, 'INNER JOIN', 'abusive_lang_id = language_id AND language_active = ' . AppConstant::YES);
        $srch->addMultipleFields(['abusive_id', 'abusive_keyword']);
        $srch->addOrder('abusive.abusive_lang_id', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $abusiveArr = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        $abusiveTxtArr = [];
        if ($abusiveArr) {
            $abusiveArr = array_map("strtolower", $abusiveArr);
            $textToBeCheckArr = explode(" ", $textToBeCheck);
            foreach ($textToBeCheckArr as $postedWord) {
                if (in_array(strtolower($postedWord), $abusiveArr)) {
                    array_push($abusiveTxtArr, $postedWord);
                }
            }
        }
        if (!empty($abusiveTxtArr)) {
            return false;
        }
        return true;
    }

}
