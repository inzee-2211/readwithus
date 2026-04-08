<?php

/**
 * This class is used to handle Currency
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Currency extends MyAppModel
{

    const DB_TBL = 'tbl_currencies';
    const DB_TBL_PREFIX = 'currency_';
    const DB_TBL_LANG = 'tbl_currencies_lang';
    const DB_TBL_LANG_PREFIX = 'currencylang_';

    /**
     * Initialize Currency
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'currency_id', $id);
        $this->objMainTableRecord->setSensitiveFields(['currency_is_default']);
    }

    /**
     * Get Search Object
     * 
     * @param int $langId
     * @param bool $isActive
     * @return SearchBase
     */
    public static function getSearchObject(int $langId = 0, bool $isActive = true): SearchBase
    {
        $srch = new SearchBase(static::DB_TBL, 'curr');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'curr_l.currencylang_currency_id '
                    . ' = curr.currency_id and curr_l.currencylang_lang_id = ' . $langId, 'curr_l');
        }
        if ($isActive) {
            $srch->addCondition('curr.currency_active', '=', 1);
        }
        return $srch;
    }

    /**
     * Get Currency Name With Code
     * 
     * @param int $langId
     * @return bool|array
     */
    public static function getCurrencyNameWithCode(int $langId)
    {
        $srch = self::getSearchObject($langId);
        $srch->addMultipleFields(['currency_id', 'CONCAT(IFNULL(curr_l.currency_name,curr.currency_code)," (",currency_code ,")") as currency_name_code']);
        $srch->addOrder('currency_order');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $row = FatApp::getDb()->fetchAllAssoc($srch->getResultSet(), 'currency_id');
        if (!is_array($row)) {
            return false;
        }
        return $row;
    }

    /**
     * Get Data
     * 
     * @param int  $currencyId
     * @param int  $langId
     * @param bool $active
     * @return null|array
     */
    public static function getData(int $currencyId, int $langId, $active = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'currency');
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'curlang.currencylang_currency_id = '
                . 'currency.currency_id AND curlang.currencylang_lang_id = ' . $langId, 'curlang');
        if ($active == true) {
            $srch->addCondition('currency.currency_active', '=', AppConstant::YES);
        }
        $srch->addCondition('currency.currency_id', '=', $currencyId);
        $srch->addMultipleFields([
            'currency.currency_id AS currency_id',
            'curlang.currency_name AS currency_name',
            'currency.currency_code AS currency_code',
            'currency.currency_value AS currency_value',
            'currency.currency_symbol_left AS currency_symbol_left',
            'currency.currency_symbol_right AS currency_symbol_right',
        ]);
        $srch->addOrder('currency_order', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get System

     * @param int $langId
     * @return null|array
     */
    public static function getSystemCurrency(int $langId)
    {
        $srch = new SearchBase(static::DB_TBL, 'currency');
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'curlang.currencylang_currency_id = '
                . 'currency.currency_id AND curlang.currencylang_lang_id = ' . $langId, 'curlang');
        $srch->addCondition('currency.currency_active', '=', AppConstant::YES);
        $srch->addCondition('currency.currency_is_default', '=', AppConstant::YES);
        $srch->addCondition('currency.currency_value', '=', 1);
        $srch->addMultipleFields([
            'currency.currency_id AS currency_id',
            'curlang.currency_name AS currency_name',
            'currency.currency_code AS currency_code',
            'currency.currency_value AS currency_value',
            'currency.currency_symbol_left AS currency_symbol_left',
            'currency.currency_symbol_right AS currency_symbol_right',
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

}
