<?php

/**
 * FAQ is used for FAQs handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class FaqController extends AdminBaseController
{

    /**
     * Initialize FAQ
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewFaq();
    }

    public function index($faq_catid = 0)
    {
        $this->set('includeEditor', true);
        $this->set('faq_catid', FatUtility::int($faq_catid));
        $this->set('canEdit', $this->objPrivilege->canEditFaq(true));
        $this->set('searchFrm', $this->getSearchForm());
        $this->_template->render();
    }

    /**
     * Search & List FAQs
     */
    public function search()
    {
        $srch = Faq::getSearchObject($this->siteLangId, false);
        $srch->addMultipleFields(['faq_identifier', 'faq_id', 'faq_category', 'faq_active', 'faq_title']);
        $srch->addOrder('faq_active', 'desc');
        $keyword = FatApp::getPostedData('keyword', FatUtility::VAR_STRING, '');
        if (!empty($keyword)) {
            $cnd = $srch->addCondition('faq_identifier', 'like', '%' . $keyword . '%', 'AND', true);
            $cnd->attachCondition('faq_title', 'like', '%' . $keyword . '%');
        }
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set("canEdit", $this->objPrivilege->canEditFaq(true));
        $this->set('recordCount', $srch->recordCount());
        $this->set("arr_listing", $records);
        $this->set('post', FatApp::getPostedData());
        $this->_template->render(false, false);
    }

    /**
     * Render FAQ Form
     * 
     * @param int $faqId
     */
    public function form($faqId)
    {
        $faqId = FatUtility::int($faqId);
        $frm = $this->getForm($faqId);
        if (0 < $faqId) {
            $data = Faq::getAttributesById($faqId, ['faq_id', 'faq_identifier', 'faq_category', 'faq_is_popular','faq_active']);
            if ($data === false) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('faqId', $faqId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    /**
     * Setup FAQ
     */
    public function setup()
    {
        $this->objPrivilege->canEditFaq();
        $frm = $this->getForm(0);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $faqId = $post['faq_id'];
        unset($post['faq_id']);
        if ($faqId == 0) {
            $post['faq_added_on'] = date('Y-m-d H:i:s');
        }
        $faq = new Faq($faqId);
        $faq->assignValues($post);
        if (!$faq->save()) {
            FatUtility::dieJsonError($faq->getError());
        }
        $newTabLangId = 0;
        if ($faqId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Faq::getAttributesByLangId($langId, $faqId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $faqId = $faq->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'faqId' => $faqId, 'langId' => $newTabLangId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Render FAQ Lang Form
     * 
     * @param int $faqId
     * @param int $lang_id
     */
    public function langForm($faqId = 0, $lang_id = 0)
    {
        $faqId = FatUtility::int($faqId);
        $lang_id = FatUtility::int($lang_id);
        if ($faqId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($faqId, $lang_id);
        $langData = Faq::getAttributesByLangId($lang_id, $faqId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('faqId', $faqId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    /**
     * Setup FAQ Language Data
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditFaq();
        $post = FatApp::getPostedData();
        $faqId = $post['faq_id'];
        $lang_id = $post['lang_id'];
        if ($faqId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($faqId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['faq_id']);
        unset($post['lang_id']);
        $data = [
            'faqlang_lang_id' => $lang_id,
            'faqlang_faq_id' => $faqId,
            'faq_title' => $post['faq_title'],
            'faq_description' => $post['faq_description']
        ];
        $faq = new Faq($faqId);
        if (!$faq->updateLangData($lang_id, $data)) {
            FatUtility::dieJsonError($faq->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Faq::getAttributesByLangId($langId, $faqId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'faqId' => $faqId, 'langId' => $newTabLangId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditFaq();
        $faqId = FatApp::getPostedData('faqId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $faq = new Faq($faqId);
        if (!$faq->loadFromDb()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!$faq->changeStatus($status)) {
            FatUtility::dieJsonError($faq->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Delete Record
     */
    public function deleteRecord()
    {
        $this->objPrivilege->canEditFaq();
        $faqId = FatApp::getPostedData('faqId', FatUtility::VAR_INT, 0);
        $faq = new Faq($faqId);
        if (!$faq->loadFromDb()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!$faq->deleteRecord($faqId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /**
     * Get FAQ Form
     * 
     * @param int $faqId
     * @return Form
     */
    private function getForm($faqId = 0): Form
    {
        $faqId = FatUtility::int($faqId);
        $frm = new Form('frmFaq');
        $frm->addHiddenField('', 'faq_id', $faqId);
        $frm->addRequiredField(Label::getLabel('LBL_Faq_Identifier'), 'faq_identifier');
        $fld = $frm->addSelectBox(Label::getLabel('LBL_faq_category'), 'faq_category', Faq::getFaqCategoryArr(MyUtility::getSiteLangId()));
        $fld->requirements()->setRequired(true);
        $frm->addSelectBox(Label::getLabel('LBL_Faq_is_popular'), 'faq_is_popular', AppConstant::getYesNoArr(), '', [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'faq_active', AppConstant::getActiveArr(), '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Language Form
     * 
     * @param int $faqId
     * @param int $lang_id
     * @return \Form
     */
    private function getLangForm($faqId = 0, $lang_id = 0)
    {
        $frm = new Form('frmFaqLang');
        $frm->addHiddenField('', 'faq_id', $faqId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Faq_Title'), 'faq_title');
        $frm->addHtmlEditor(Label::getLabel('LBL_Faq_Text'), 'faq_description');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmFaqearch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_CLEAR_SEARCH'), ['onclick' => 'clearSearch();']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
