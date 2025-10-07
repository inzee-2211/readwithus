<?php

/**
 * Currency Management is used for Currency handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class CurrencyManagementController extends AdminBaseController
{

    /**
     * Initialize Currency
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCurrencyManagement();
    }

    public function index()
    {
        $this->set("canEdit", $this->objPrivilege->canEditCurrencyManagement(true));
        $this->_template->render();
    }

    /**
     * Search & List Currencies
     */
    public function search()
    {
        $srch = Currency::getSearchObject($this->siteLangId, false);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addOrder('currency_order', 'ASC');
        $this->set("arr_listing", FatApp::getDb()->fetchAll($srch->getResultSet()));
        $this->set("canEdit", $this->objPrivilege->canEditCurrencyManagement(true));
        $this->set('activeInactiveArr', AppConstant::getActiveArr());
        $this->_template->render(false, false);
    }

    /**
     * Render Currency Form
     * 
     * @param int  $currencyId
     */
    public function form($currencyId = 0)
    {
        $this->objPrivilege->canEditCurrencyManagement();
        $currencyId = FatUtility::int($currencyId);
        $frm = $this->getForm($currencyId);
        if (0 > $currencyId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $defaultCurrency = 0;
        if ($currencyId > 0) {
            $data = Currency::getAttributesById($currencyId, ['currency_id', 'currency_code', 'currency_active',
                        'currency_symbol_left', 'currency_symbol_right', 'currency_value', 'currency_is_default']);
            if ($data === false) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $defaultCurrency = $data['currency_is_default'];
            $frm->fill($data);
        }
        $this->set('frm', $frm);
        $this->set('currency_id', $currencyId);
        $this->set('languages', Language::getAllNames());
        $this->set('defaultCurrency', $defaultCurrency);
        $this->_template->render(false, false);
    }

    /**
     * Setup Currency 
     */
    public function setup()
    {
        $this->objPrivilege->canEditCurrencyManagement();
        $frm = $this->getForm(0);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $currencyId = FatUtility::int($post['currency_id']);
        unset($post['currency_id']);
        if ($currencyId > 0) {
            $data = Currency::getAttributesById($currencyId, ['currency_id', 'currency_is_default']);
            if ($data === false) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            if ($data['currency_is_default'] == AppConstant::YES) {
                unset($post['currency_value'], $post['currency_code'], $post['currency_active']);
            }
        }
        $currency = new Currency($currencyId);
        $post['currency_date_modified'] = date('Y-m-d H:i:s');
        $currency->assignValues($post);
        if (!$currency->save()) {
            FatUtility::dieJsonError($currency->getError());
        }
        $newTabLangId = 0;
        if ($currencyId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Currency::getAttributesByLangId($langId, $currencyId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $currencyId = $currency->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'currencyId' => $currencyId, 'langId' => $newTabLangId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Render Currency Lang Form
     * 
     * @param int $currencyId
     * @param int $lang_id
     */
    public function langForm($currencyId = 0, $lang_id = 0)
    {
        $this->objPrivilege->canEditCurrencyManagement();
        $currencyId = FatUtility::int($currencyId);
        $lang_id = FatUtility::int($lang_id);
        if ($currencyId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($currencyId, $lang_id);
        $langData = Currency::getAttributesByLangId($lang_id, $currencyId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('currencyId', $currencyId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    /**
     * Setup Currency Language Data
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditCurrencyManagement();
        $post = FatApp::getPostedData();
        $currencyId = $post['currency_id'];
        $lang_id = $post['lang_id'];
        if ($currencyId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($currencyId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['currency_id']);
        unset($post['lang_id']);
        $data = [
            'currencylang_lang_id' => $lang_id,
            'currencylang_currency_id' => $currencyId,
            'currency_name' => $post['currency_name']
        ];
        $currency = new Currency($currencyId);
        if (!$currency->updateLangData($lang_id, $data)) {
            FatUtility::dieJsonError($currency->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Currency::getAttributesByLangId($langId, $currencyId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'currencyId' => $currencyId,
            'langId' => $newTabLangId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Update Sort Order
     */
    public function updateOrder()
    {
        $this->objPrivilege->canEditCurrencyManagement();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $currency = new Currency();
            if (!$currency->updateOrder($post['currencyList'])) {
                FatUtility::dieJsonError($currency->getError());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_Order_Updated_Successfully'));
        }
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditCurrencyManagement();
        $currencyId = FatApp::getPostedData('currencyId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $currency = new Currency($currencyId);
        if (!$currency->loadFromDb()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!$currency->changeStatus($status)) {
            FatUtility::dieJsonError($currency->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Form
     * 
     * @param int $currencyId
     * @return Form
     */
    private function getForm($currencyId = 0): Form
    {
        $frm = new Form('frmCurrency');
        $frm->addHiddenField('', 'currency_id', FatUtility::int($currencyId));
        $frm->addRequiredField(Label::getLabel('LBL_Currency_code'), 'currency_code');
        $frm->addTextBox(Label::getLabel('LBL_Currency_Symbol_Left'), 'currency_symbol_left');
        $frm->addTextBox(Label::getLabel('LBL_Currency_Symbol_Right'), 'currency_symbol_right');
        $frm->addFloatField(Label::getLabel('LBL_Currency_Conversion_Value'), 'currency_value');
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'currency_active', AppConstant::getActiveArr(), '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Lang Form
     * 
     * @param int $currencyId
     * @param int $lang_id
     * @return Form
     */
    private function getLangForm($currencyId = 0, $lang_id = 0): Form
    {
        $frm = new Form('frmCurrencyLang');
        $frm->addHiddenField('', 'currency_id', $currencyId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Currency_Name'), 'currency_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

}
