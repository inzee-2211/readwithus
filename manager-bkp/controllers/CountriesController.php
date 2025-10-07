<?php

/**
 * Countries Controller is used for Countries handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class CountriesController extends AdminBaseController
{

    /**
     * Initialize Countries
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCountries();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $this->set("search", $this->getSearchForm($this->siteLangId));
        $this->_template->render();
    }

    /**
     * Search & List Countries
     */
    public function search()
    {
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $post = $searchForm->getFormDataFromArray(FatApp::getPostedData());
        $srch = new SearchBase(Country::DB_TBL, 'c');
        $srch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'c_l.countrylang_country_id = c.country_id and c_l.countrylang_lang_id = ' . $this->siteLangId, 'c_l');
        $srch->addFld('c.* , IFNULL(c_l.country_name, c.country_identifier) as country_name');
        if (!empty($post['keyword'])) {
            $condition = $srch->addCondition('c.country_code', 'like', '%' . $post['keyword'] . '%');
            $condition->attachCondition('c_l.country_name', 'like', '%' . $post['keyword'] . '%', 'OR');
            $condition->attachCondition('c.country_identifier', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $srch->addOrder('country_name');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("canEdit", $this->objPrivilege->canEditCountries(true));
        $this->set('activeInactiveArr', AppConstant::getActiveArr());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    /**
     * Render Country Form
     * 
     * @param int $countryId
     */
    public function form(int $countryId)
    {
        $this->objPrivilege->canEditCountries();
        $data = Country::getAttributesById($countryId);
        if (empty($data)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getForm();
        $frm->fill($data);
        $this->sets([
            'languages' => Language::getAllNames(),
            'country_id' => $countryId, 'frm' => $frm
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Country
     */
    public function setup()
    {
        $this->objPrivilege->canEditCountries();
        $frm = $this->getForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $countryId = FatUtility::int($post['country_id']);
        $data = Country::getAttributesById($countryId);
        if (empty($data)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $country = new Country($countryId);
        $country->assignValues($post);
        if (!$country->save()) {
            FatUtility::dieJsonError($country->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Country::getAttributesByLangId($langId, $countryId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'countryId' => $countryId, 'langId' => $newTabLangId,
            'msg' => Label::getLabel('LBL_UPDATED_SUCCESSFULLY')
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Render Country Lang Data
     * @param int $countryId
     * @param int $lang_id
     */
    public function langForm($countryId = 0, $lang_id = 0)
    {
        $countryId = FatUtility::int($countryId);
        $lang_id = FatUtility::int($lang_id);
        if ($countryId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $country = Country::getAttributesById($countryId, ['country_identifier']);
        if (empty($country)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $data = ['countrylang_country_id' => $countryId, 'countrylang_lang_id' => $lang_id, 'country_name' => $country['country_identifier']];
        $langFrm = $this->getLangForm($countryId, $lang_id);
        $langData = Country::getAttributesByLangId($lang_id, $countryId);
        if (!empty($langData)) {
            $data = $langData;
        }
        $langFrm->fill($data);
        $this->set('languages', Language::getAllNames());
        $this->set('countryId', $countryId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    /**
     * Language Setup
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditCountries();
        $frm = $this->getLangForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $country = Country::getAttributesById($post['countrylang_country_id'], ['country_identifier']);
        if (empty($country)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $country = new Country($post['countrylang_country_id']);
        if (!$country->updateLangData($post['countrylang_lang_id'], $post)) {
            FatUtility::dieJsonError($country->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!Country::getAttributesByLangId($langId, $post['countrylang_country_id'])) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'countryId' => $post['countrylang_country_id'], 'langId' => $newTabLangId,
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditCountries();
        $countryId = FatApp::getPostedData('countryId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $country = new Country($countryId);
        if (!$country->loadFromDb()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!$country->changeStatus($status)) {
            FatUtility::dieJsonError($country->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search'));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    /**
     * Get Form
     * 
     * @param int $countryId
     * @return Form
     */
    private function getForm(): Form
    {
        $frm = new Form('frmCountry');
        $frm->addRequiredField(Label::getLabel('LBL_IDENTIFIER'), 'country_identifier');
        $frm->addHiddenField('', 'country_id');
        $frm->addTextBox(Label::getLabel('LBL_COUNTRY_CODE'), 'country_code', '', ['disabled' => 'disabled']);
        $frm->addTextBox(Label::getLabel('LBL_DIAL_CODE'), 'country_dial_code', '', ['disabled' => 'disabled']);
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'country_active', AppConstant::getActiveArr(), '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Language Form
     * 
     * @param int $countryId
     * @param int $lang_id
     * @return Form
     */
    private function getLangForm(): Form
    {
        $frm = new Form('frmCountryLang');
        $fld = $frm->addHiddenField('', 'countrylang_country_id');
        $fld->requirements()->setRequired();
        $fld = $frm->addHiddenField('', 'countrylang_lang_id');
        $fld->requirements()->setRequired();
        $frm->addRequiredField(Label::getLabel('LBL_COUNTRY_NAME'), 'country_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

}
