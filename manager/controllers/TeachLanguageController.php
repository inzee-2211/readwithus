<?php

/**
 * Teach Language Controller is used for TeachLanguage handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class TeachLanguageController extends AdminBaseController
{

    /**
     * Initialize Teach Language
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTeachLanguage();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $this->set('frmSearch', $this->getSearchForm());
        $this->set('canEdit', $this->objPrivilege->canEditTeachLanguage(true));
        $this->_template->render();
    }

    /**
     * Search & List Teach Languages
     */
    public function search()
    {
        $data = FatApp::getPostedData();
        $searchForm = $this->getSearchForm();
        $post = $searchForm->getFormDataFromArray($data);
        $srch = TeachLanguage::getSearchObject($this->siteLangId, false);
        if (!empty($post['keyword'])) {
            $cond = $srch->addCondition('tlang_identifier', 'like', '%' . $post['keyword'] . '%');
            $cond->attachCondition('tlang_name', 'like', '%' . $post['keyword'] . '%');
        }
        $srch->addOrder('tlang_order', 'asc');
        $srch->addOrder('tlang_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('arrListing', $records);
        $this->set('canEdit', $this->objPrivilege->canEditTeachLanguage(true));
        $this->_template->render(false, false);
    }

    /**
     * Teach Language Form
     * 
     * @param int $tLangId
     */
    public function form($tLangId = 0)
    {
        $this->objPrivilege->canEditTeachLanguage();
        $tLangId = FatUtility::int($tLangId);
        $frm = $this->getForm();
        $frm->getField('tlang_id')->value = $tLangId;
        if ($tLangId > 0) {
            $data = TeachLanguage::getAttributesById($tLangId);
            if (empty($data)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $frm->fill($data);
        }
        $this->sets([
            'languages' => Language::getAllNames(),
            'tLangId' => $tLangId, 'frm' => $frm,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Teach Language
     */
    public function setup()
    {
        $this->objPrivilege->canEditTeachLanguage();
        $post = FatApp::getPostedData();
        if (isset($post['tlang_slug'])) {
            $post['tlang_slug'] = CommonHelper::seoUrl($post['tlang_slug']);
        }
        $frm = $this->getForm(true);
        if (!$post = $frm->getFormDataFromArray($post)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $tLangId = $post['tlang_id'];
        unset($post['tlang_id']);
        $record = new TeachLanguage($tLangId);
        $record->assignValues($post);
        if (!$record->save()) {
            FatUtility::dieJsonError($record->getError());
        }
        if ($post['tlang_active'] == AppConstant::NO) {
            (new UserTeachLanguage())->removeTeachLang([$tLangId]);
            (new TeacherStat(0))->setTeachLangPricesBulk();
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'tLangId' => $record->getMainTableRecordId()
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Teach Lang Language Form
     * 
     * @param int $tLangId
     * @param int $langId
     */
    public function langForm($tLangId = 0, $langId = 0)
    {
        $tLangId = FatUtility::int($tLangId);
        $langId = FatUtility::int($langId);
        if (empty(TeachLanguage::getAttributesById($tLangId, 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($langId);
        $languages = $langFrm->getField('tlanglang_lang_id')->options;
        if (!array_key_exists($langId, $languages)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langData = TeachLanguage::getAttributesByLangId($langId, $tLangId);
        if (empty($langData)) {
            $langData = ['tlanglang_lang_id' => $langId, 'tlanglang_tlang_id' => $tLangId];
        }
        $langFrm->fill($langData);
        $this->sets([
            'tLangId' => $tLangId,
            'languages' => $languages,
            'lang_id' => $langId,
            'langFrm' => $langFrm,
            'formLayout' => Language::getLayoutDirection($langId),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Teach Lang Language Data
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditTeachLanguage();
        $frm = $this->getLangForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (empty(TeachLanguage::getAttributesById($post['tlanglang_tlang_id'], 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $data = [
            'tlanglang_lang_id' => $post['tlanglang_lang_id'],
            'tlanglang_tlang_id' => $post['tlanglang_tlang_id'],
            'tlang_name' => $post['tlang_name']
        ];
        $teachLanguage = new TeachLanguage($post['tlanglang_tlang_id']);
        if (!$teachLanguage->updateLangData($post['tlanglang_lang_id'], $data)) {
            FatUtility::dieJsonError($teachLanguage->getError());
        }
        $data = [
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'tLangId' => $post['tlanglang_tlang_id']
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditTeachLanguage();
        $tLangId = FatApp::getPostedData('tLangId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (empty(TeachLanguage::getAttributesById($tLangId, 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $teachLanguage = new TeachLanguage($tLangId);
        if (!$teachLanguage->changeStatus($status)) {
            FatUtility::dieJsonError($teachLanguage->getError());
        }
        if ($status == AppConstant::NO) {
            (new UserTeachLanguage())->removeTeachLang([$tLangId]);
            (new TeacherStat(0))->setTeachLangPricesBulk();
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Delete Record
     */
    public function deleteRecord()
    {
        $this->objPrivilege->canEditTeachLanguage();
        $tLangId = FatApp::getPostedData('tLangId', FatUtility::VAR_INT, 0);
        if (empty(TeachLanguage::getAttributesById($tLangId, 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $teachLanguage = new TeachLanguage($tLangId);
        if (!$teachLanguage->deleteRecord($tLangId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        (new UserTeachLanguage())->removeTeachLang([$tLangId]);
        (new TeacherStat(0))->setTeachLangPricesBulk();
        (new Afile(Afile::TYPE_TEACHING_LANGUAGES))->removeFile($tLangId, true);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /**
     * Get Teach Lang Form
     * 
     * @param bool $setUnique
     * @return Form
     */
    private function getForm(bool $setUnique = false): Form
    {
        $frm = new Form('frmLessonPackage');
        $fld = $frm->addHiddenField('', 'tlang_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive();
        $frm->addRequiredField(Label::getLabel('LBL_LANGUAGE_IDENTIFIER'), 'tlang_identifier');
        $fld = $frm->addTextBox(Label::getLabel('LBL_LANGUAGE_SLUG'), 'tlang_slug');
        $fld->requirements()->setRequired();
        $fld->requirements()->setRequired();
        $fld->requirements()->setLength(3, 100);
        if ($setUnique) {
            $fld->setUnique(TeachLanguage::DB_TBL, 'tlang_slug', 'tlang_id', 'tlang_id', 'tlang_id');
        }
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'tlang_active', AppConstant::getActiveArr(), '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    /**
     * Get TeachLang Language Form
     * @param int $langId
     * @return Form
     */
    private function getLangForm(int $langId = 0): Form
    {
        $frm = new Form('frmTeachLang');
        $frm->addHiddenField('', 'tlanglang_tlang_id');
        $frm->addSelectBox('', 'tlanglang_lang_id', Language::getAllNames(), '', [], '');
        $frm->addRequiredField(Label::getLabel('LBL_LANGUAGE_NAME', $langId), 'tlang_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES', $langId));
        return $frm;
    }

    /**
     * Render Media Form
     * 
     * @param int $tLangId
     */
    public function mediaForm($tLangId = 0)
    {
        if (empty(TeachLanguage::getAttributesById($tLangId, 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $this->sets([
            "tLangId" => $tLangId,
            "canEdit" => $this->objPrivilege->canEditTeachLanguage(true),
            "mediaFrm" => $this->getMediaForm($tLangId),
            "languages" => Language::getAllNames(),
            "image" => (new Afile(Afile::TYPE_TEACHING_LANGUAGES))->getFile($tLangId),
            "teachLangExt" => implode(', ', Afile::getAllowedExts(Afile::TYPE_TEACHING_LANGUAGES)),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Get Media Form
     * 
     * @param int $tlang_id
     * @return Form
     */
    private function getMediaForm($tlang_id): Form
    {
        $frm = new Form('frmTeachLanguageMedia');
        $frm->addHiddenField('', 'tlang_id', $tlang_id);
        $frm->addFileUpload('', 'tlang_image_file');
        $frm->addButton(Label::getLabel('LBL_LANGUAGE_IMAGE'), 'tlang_image', Label::getLabel('LBL_UPLOAD_FILE'),
                ['class' => 'tlanguageFile-Js', 'id' => 'tlang_image', 'data-tlang_id' => $tlang_id]);
        return $frm;
    }

    /**
     * Upload File
     * 
     * @param int $tlanguageId
     */
    public function uploadFile($tlanguageId)
    {
        $this->objPrivilege->canEditTeachLanguage();
        if (empty(TeachLanguage::getAttributesById($tlanguageId, 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $type = FatApp::getPostedData('imageType', FatUtility::VAR_INT, Afile::TYPE_TEACHING_LANGUAGES);
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Please_Select_A_File'));
        }
        $file = new Afile($type);
        if (!$file->saveFile($_FILES['file'], $tlanguageId, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess([
            'tlang_id' => $tlanguageId,
            'msg' => $_FILES['file']['name'] . Label::getLabel('MSG_FILE_UPLOADED_SUCCESSFULLY')
        ]);
    }

    /**
     * Remove File
     * 
     * @param int $tlanguageId
     * @param int $fileType
     */
    public function removeFile($tlanguageId, $fileType)
    {
        $this->objPrivilege->canEditTeachLanguage();
        $tlanguageId = FatUtility::int($tlanguageId);
        $fileType = FatUtility::int($fileType);
        if (1 > $fileType) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (empty(TeachLanguage::getAttributesById($tlanguageId, 'tlang_id'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $file = new Afile($fileType);
        if (!$file->removeFile($tlanguageId, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_DELETED_SUCCESSFULLY'));
    }

    /**
     * Update Order
     */
    public function updateOrder()
    {
        $this->objPrivilege->canEditTeachLanguage();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $teachLangObj = new TeachLanguage();
            if (!$teachLangObj->updateOrder($post['teachingLangages'])) {
                FatUtility::dieJsonError($teachLangObj->getError());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_ORDER_UPDATED_SUCCESSFULLY'));
        }
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmTeachLanguageSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_LANGUAGE_IDENTIFIER'), 'keyword', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEARCH'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_CLEAR'));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    /**
     * Auto Complete JSON
     */
    public function autoCompleteJson()
    {
        $keyword = FatApp::getPostedData('keyword', FatUtility::VAR_STRING, '');
        if (empty($keyword)) {
            FatUtility::dieJsonSuccess(['data' => []]);
        }
        $langId = MyUtility::getSiteLangId();
        $srch = new SearchBase(TeachLanguage::DB_TBL, 'tlang');
        $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id AND tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
        $srch->addMultiplefields(['tlang_id', 'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name']);
        if (!empty($keyword)) {
            $cond = $srch->addCondition('tlanglang.tlang_name', 'LIKE', '%' . $keyword . '%');
            $cond->attachCondition('tlang.tlang_identifier', 'LIKE', '%' . $keyword . '%', 'OR');
        }
        $srch->addOrder('tlang_name', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(20);
        $data = FatApp::getDb()->fetchAll($srch->getResultSet(), 'tlang_id');
        FatUtility::dieJsonSuccess(['data' => $data]);
    }

}
