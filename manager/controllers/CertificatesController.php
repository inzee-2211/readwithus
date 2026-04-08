<?php

/**
 * Certificates Controller
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CertificatesController extends AdminBaseController
{

    /**
     * Initialize Certificates
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCertificates();
        $this->set("includeEditor", true);
    }

    /**
     * Render listing page
     */
    public function index()
    {
        $this->set("canEdit", $this->objPrivilege->canEditCertificates(true));
        $this->_template->render();
    }

    /**
     * List certificates
     */
    public function search()
    {
        $srch = CertificateTemplate::getSearchObject($this->siteLangId);
        $srch->addGroupBy('certpl_code');
        $srch->addCondition('certpl_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $data = FatApp::getDb()->fetchAll($srch->getResultSet());
        
        $this->sets([
            'arrListing' => $data,
            'canEdit' => $this->objPrivilege->canEditCertificates(true),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Update status
     *
     * @param string $certTplCpde
     * @param int    $status
     * @return bool
     */
    public function updateStatus(string $certTplCpde, int $status)
    {
        $this->objPrivilege->canEditCertificates();

        $certTplCpde = FatUtility::convertToType($certTplCpde, FatUtility::VAR_STRING);
        $status = FatUtility::int($status);
        $status = ($status == AppConstant::YES) ? AppConstant::NO : AppConstant::YES;

        $srch = CertificateTemplate::getSearchObject($this->siteLangId);
        $srch->addCondition('certpl_code', '=', $certTplCpde);
        if (!FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $template = new CertificateTemplate();
        if (!$template->updateStatus($certTplCpde, $status)) {
            FatUtility::dieJsonError($template->getError());
        }
        
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_STATUS_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Render Certificate Form
     *
     * @param string  $certTplCode
     * @param int     $langId
     */
    public function form(string $certTplCode, int $langId)
    {
        $this->objPrivilege->canEditCertificates();
        $certTplId = FatUtility::convertToType($certTplCode, FatUtility::VAR_STRING, '');
        $langId = FatUtility::int($langId);
        $langId = ($langId < 1) ? $this->siteLangId : $langId;

        $srch = CertificateTemplate::getSearchObject($langId);
        $srch->addCondition('certpl_code', '=', $certTplCode);
        if (!$data = FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::dieWithError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $data['certpl_lang_id'] = $langId;
        /* get template form */
        $frm = $this->getForm($langId);
        $frm->fill($data);

        /* get certficate image form */
        $mediaFrm = $this->getMediaForm($langId);
        $mediaFrm->fill(['certpl_id' => $certTplId]);

        /* get image dimensions */
        $dimensions = (new Afile(Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE))->getImageSizes();

        /* get lang data */
        $srch = new SearchBase(CertificateTemplate::DB_TBL);
        $srch->addCondition('certpl_code', '=', $data['certpl_code']);
        $srch->addMultipleFields(['certpl_lang_id', 'certpl_id']);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $langs = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        $this->sets([
            'frm' => $frm,
            'mediaFrm' => $mediaFrm,
            'data' => $data,
            'langs' => $langs,
            'content' => json_decode($data['certpl_body'], true),
            'formLayout' => Language::getLayoutDirection($data['certpl_lang_id']),
            'dimensions' => $dimensions['LARGE'],
            'imageExts' => implode(', ', Afile::getAllowedExts(Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE)),
            'layoutDir' => Language::getAttributesById($langId, 'language_direction')
        ]);
        $this->_template->render();
    }

    /**
     * Setup Certificate
     */
    public function setup()
    {
        $this->objPrivilege->canEditCertificates();
        $frm = $this->getForm();
        $post = FatApp::getPostedData();
        $post['certpl_body'] = json_encode([
            'heading' => $post['heading'],
            'content_part_1' => $post['content_part_1'],
            'learner' => $post['learner'],
            'content_part_2' => $post['content_part_2'],
            'trainer' => $post['trainer'],
            'certificate_number' => $post['certificate_number'],
        ]);
        if (!$post = $frm->getFormDataFromArray($post)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $template = new CertificateTemplate($post['certpl_id']);
        if (!$template->setup($post)) {
            FatUtility::dieJsonError($template->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETUP_SUCCESSFUL'));
    }

    /**
     * Setup Certificate Media
     */
    public function setupMedia()
    {
        $this->objPrivilege->canEditCertificates();
        $frm = $this->getMediaForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        
        if (!$code = CertificateTemplate::getAttributesById($post['certpl_id'], 'certpl_code')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $file = new Afile(Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE);
        if (!$file->saveFile($_FILES['certpl_image'], 0, true)) {
            FatUtility::dieJsonError($file->getError());
        }

        FatUtility::dieJsonSuccess([
            'imgUrl' => MyUtility::makeUrl('image', 'show', [
                    Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE, 0,
                    Afile::SIZE_LARGE
                ]) . '?time=' . time(),
            'msg' => Label::getLabel('MSG_FILES_UPLOADED_SUCCESSFULLY')
        ]);
    }

    public function generate($langId)
    {
        $langId = ($langId < 1) ? $this->siteLangId : $langId;
        /* Create dummy data */
        $data = [
            'learner_first_name' => 'Martha',
            'learner_last_name' => 'Christopher',
            'teacher_first_name' => 'John',
            'teacher_last_name' => 'Doe',
            'course_title' => 'English Language Learning - Beginners',
            'course_clang_name' => 'English',
            'lang_id' => $langId,
            'cert_number' => 'YC_h34uwh9e72w',
            'crspro_completed' => date('Y-m-d'),
            'course_duration' => '11265'
        ];
        $cert = new Certificate(0, 0, $langId);
        if (!$content = $cert->getFormattedContent($data)) {
            FatUtility::dieWithError(Label::getLabel('LBL_CONTENT_NOT_FOUND'));
        }
        /* get background and logo images */
        $afile = new Afile(Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE, 0);
        $backgroundImg = $afile->getFile(0, false);
        if (!isset($backgroundImg['file_path']) || !file_exists(CONF_UPLOADS_PATH . $backgroundImg['file_path'])) {
            $backgroundImg = CONF_INSTALLATION_PATH . 'public/images/noimage.jpg';
        } else {
            $backgroundImg = CONF_UPLOADS_PATH . $backgroundImg['file_path'];
        }
        $afile = new Afile(Afile::TYPE_CERTIFICATE_LOGO, $langId);
        $logoImg = $afile->getFile(0, false);
        if (!isset($logoImg['file_path']) || !file_exists(CONF_UPLOADS_PATH . $logoImg['file_path'])) {
            $logoImg = CONF_INSTALLATION_PATH . 'public/images/noimage.jpg';
        } else {
            $logoImg = CONF_UPLOADS_PATH . $logoImg['file_path'];
        }
        $this->sets([
            'content' => $content,
            'layoutDir' => Language::getAttributesById($langId, 'language_direction'),
            'langId' => $langId,
            'backgroundImg' => $backgroundImg,
            'logoImg' => $logoImg,
        ]);
        $content = $this->_template->render(false, false, 'certificates/generate.php', true);
        $filename = 'certificate.pdf';
        /* generate certificate */
        if (!$cert->generateCertificate($content, $filename, true)) {
            FatUtility::dieWithError(Label::getLabel('LBL_AN_ERROR_HAS_OCCURRED_WHILE_GENERATING_CERTIFICATE!'));
        }
    }

    /**
     * Get Default Certificate Content
     */
    public function getDefaultContent()
    {
        $post = FatApp::getPostedData();
        if (empty($post['certpl_code'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $certData = [];
        if ($post['certpl_code'] == Certificate::TYPE_COURSE) {
            $certData = json_decode(FatApp::getConfig('CONF_COURSE_CERTIFICATE_DEFAULT_CONTENT'), true);
        }
        return FatUtility::dieJsonSuccess([
            'data' => $certData,
            'msg' => '',
        ]);
    }



    /**
     * Get Form
     *
     * @return Form
     */
    private function getForm(int $langId = 0): Form
    {
        $frm = new Form('frmCertificate');
        $frm->addHiddenField('', 'certpl_code')->requirements()->setRequired();
        $fld = $frm->addHiddenField('', 'certpl_id');
        $fld->requirements()->setIntPositive();
        $fld = $frm->addHiddenField('', 'catelang_id');
        $fld->requirements()->setIntPositive();

        $fld = $frm->addSelectBox(
            Label::getLabel('LBL_LANGUAGE', $langId),
            'certpl_lang_id',
            Language::getAllNames(),
            '',
            [],
            ''
        );
        $fld->requirements()->setRequired();

        $frm->addTextBox(Label::getLabel('LBL_NAME', $langId), 'certpl_name')->requirements()->setRequired();
        $fld = $frm->addTextArea(Label::getLabel('LBL_Body', $langId), 'certpl_body');
        $fld->requirements()->setRequired(true);
        $frm->addHtml(
            Label::getLabel('LBL_Replacement_Caption', $langId),
            'replacement_caption',
            '<h3>' . Label::getLabel('LBL_CERTIFICATE_REPLACEMENT_VARS', $langId) . '</h3>'
        );
        $frm->addHtml(Label::getLabel('LBL_Replacement_Vars', $langId), 'certpl_vars', '');

        $frm->addSelectBox(Label::getLabel('LBL_STATUS', $langId), 'certpl_status', AppConstant::getActiveArr(null, $langId), '', [], '')
        ->requirements()
        ->setRequired();

        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES', $langId));
        $fld_button = $frm->addButton('', 'btn_preview', Label::getLabel('LBL_Save_&_Preview', $langId));
        $fld_reset = $frm->addButton('', 'btn_reset', Label::getLabel('LBL_RESET_TO_DEFAULT', $langId));
        $fld_submit->attachField($fld_button);
        $fld_submit->attachField($fld_reset);
        return $frm;
    }

    /**
     * Get Media Form
     *
     * @return Form
     */
    private function getMediaForm(int $langId = 0): Form
    {
        $frm = new Form('frmMedia');
        $frm->addHiddenField('', 'certpl_id');
        $frm->addFileUpload(Label::getLabel('LBL_BACKGROUND_IMAGE', $langId), 'certpl_image');
        return $frm;
    }
}
