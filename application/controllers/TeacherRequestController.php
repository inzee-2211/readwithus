<?php

/**
 * Teacher Request Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class TeacherRequestController extends MyAppController
{

    private $userId;
    private $requestCount;

    /**
     * Initialize Teacher Request
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        MyUtility::setUserType(User::LEARNER);
        parent::__construct($action);
        $this->userId = 0;
        if ($this->siteUserId > 0) {
            $this->userId = $this->siteUserId;
        } elseif (TeacherRequest::getSession('user_id')) {
            $this->userId = TeacherRequest::getSession('user_id');
        }
        $this->requestCount = 0;
    }

    /**
     * Render Apply to Teach Form
     */
    public function index()
    {
        if (TeacherRequest::getSession('user_id') > 0) {
            FatApp::redirectUser(MyUtility::makeUrl('TeacherRequest', 'form', [], CONF_WEBROOT_FRONTEND));
        }
        $this->set('faqs', $this->getApplyToTeachFaqs());
        $this->set('applyTeachFrm', $this->getApplyTeachFrm($this->siteLangId));
        $this->set('sectionAfterBanner', ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_BENEFITS_SECTION, $this->siteLangId));
        $this->set('featuresSection', ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_FEATURES_SECTION, $this->siteLangId));
        $this->set('staticBannerSection', ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_STATIC_BANNER, $this->siteLangId));
        $this->set('becometutorSection', ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_BECOME_A_TUTOR_SECTION, $this->siteLangId));
        $this->_template->render();
    }
public function form()
{
    // More flexible user ID detection
    $userId = FatUtility::int($this->userId);
    
    // If no user ID found, try to get from session directly
    if ($userId < 1) {
        $sessionUserId = TeacherRequest::getSession('user_id');
        $userId = FatUtility::int($sessionUserId);
        
        if ($userId > 0) {
            $this->userId = $userId;
        }
    }
    
    // Final check - if still no user ID, redirect
    if ($userId < 1) {
        error_log("TeacherRequestController: No user ID found, redirecting to index");
        TeacherRequest::closeSession();
        FatApp::redirectUser(MyUtility::makeUrl('TeacherRequest', '', [], CONF_WEBROOT_FRONTEND));
    }

    $this->requestCount = TeacherRequest::getRequestCount($userId);

    $step = 1;
    if ($this->requestCount == FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT')) {
        $step = 5;
    } else {
        $req  = TeacherRequest::getRequestByUserId($userId);
        $step = (int)($req['tereq_step'] ?? 1);
        if ($step < 1 || $step > 5) { $step = 1; }
    }

    // Pass the userId to the view for debugging
    $this->set('step', $step);
    $this->set('userId', $userId);
    $this->set('exculdeMainHeaderDiv', false);
    
    $this->_template->addJs('js/jquery.form.js');
    $this->_template->addJs('js/cropper.js');
    $this->_template->addJs('teacher-request/page-js/form.js');
    
    $this->_template->render(true, false);
}
    private function attemptReachedCheck()
    {
        $this->requestCount = TeacherRequest::getRequestCount($this->userId);
        if ($this->requestCount <= FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT')) {
            return true;
        }
        FatUtility::dieJsonError(Label::getLabel('LBL_YOU_HAVE_REACH_MAX_ATTEMPTS_TO_SUBMIT_REQUEST'));
    }

    /**
     * Render Form Step1
     */
    public function formStep1()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $frm = $this->getFormStep1($this->siteLangId);
        $request = TeacherRequest::getRequestByUserId($userId);
        if (empty($request)) {
            $user = User::getDetail($this->userId);
            $request = [
                'tereq_first_name' => $user['user_first_name'],
                'tereq_last_name' => $user['user_last_name'],
                'tereq_gender' => $user['user_gender'],
                'tereq_phone_code' => $user['user_phone_code'],
                'tereq_phone_number' => $user['user_phone_number'],
                'tereq_user_id' => $user['user_id']
            ];
        }
        if (!empty($request)) {
            $frm->fill($request);
        }
        $file = new Afile(Afile::TYPE_TEACHER_APPROVAL_PROOF);
        $this->sets([
            'frm' => $frm, 'request' => $request,
            'user' => User::getAttributesById($userId),
            'photoId' => $file->getFile($this->userId),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Form Step2
     */
    public function formStep2()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $request = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_PENDING);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFormStep2($this->siteLangId);
        $frm->fill($request);
        if ($this->requestCount > 1 && $request['tereq_step'] == 2 && (empty($request['tereq_video_link']) || empty($request['tereq_biography']))) {
            $cancelledReq = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_CANCELLED);
            unset($cancelledReq['tereq_id']);
            $frm->fill($cancelledReq);
        }
        $file = new Afile(Afile::TYPE_TEACHER_APPROVAL_IMAGE);
        $teacherApprovalImage = $file->getFile($this->userId);
        if (!$teacherApprovalImage) {
            $file = new Afile(Afile::TYPE_USER_PROFILE_IMAGE);
            $teacherApprovalImage = $file->getFile($this->userId);
        }
        $this->sets([
            'teacherApprovalImage' => $teacherApprovalImage,
            'frm' => $frm, 'userId' => $userId, 'request' => $request,
            'imageExt' => Afile::getAllowedExts(Afile::TYPE_TEACHER_APPROVAL_IMAGE),
            'fileSize' => Afile::getAllowedUploadSize(Afile::TYPE_TEACHER_APPROVAL_IMAGE),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Form Step3
     */
    public function formStep3()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $request = TeacherRequest::getRequestByUserId($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($this->requestCount > 1 && $request['tereq_step'] == 3) {
            $lastRequest = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_CANCELLED);
            if (!empty($lastRequest)) {
                $request['tereq_teach_langs'] = $lastRequest['tereq_teach_langs'];
                $request['tereq_speak_langs'] = $lastRequest['tereq_speak_langs'];
                $request['tereq_slang_proficiency'] = $lastRequest['tereq_slang_proficiency'];
            }
        }
        $request['tereq_teach_langs'] = empty($request['tereq_teach_langs']) ? '[]' : $request['tereq_teach_langs'];
        $request['tereq_speak_langs'] = empty($request['tereq_speak_langs']) ? '[]' : $request['tereq_speak_langs'];
        $request['tereq_slang_proficiency'] = empty($request['tereq_slang_proficiency']) ? '[]' : $request['tereq_slang_proficiency'];
        $request['tereq_teach_langs'] = json_decode($request['tereq_teach_langs'], true);
        $request['tereq_speak_langs'] = json_decode($request['tereq_speak_langs'], true);
        $request['tereq_slang_proficiency'] = json_decode($request['tereq_slang_proficiency'], true);
        $spokenLangs = SpeakLanguage::getAllLangs($this->siteLangId, true);
        $frm = $this->getFormStep3($this->siteLangId, $spokenLangs);
        $frm->fill($request);
        $this->set('frm', $frm);
        $this->set('request', $request);
        $this->set('spokenLangs', $spokenLangs);
        $this->set('user', User::getAttributesById($userId));
        $this->_template->render(false, false);
    }

    /**
     * Render Form Step4
     */
    public function formStep4()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $request = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_PENDING);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFormStep4();
        $frm->fill($request);
        $this->set('frm', $frm);
        $this->set('request', $request);
        $this->set('user', User::getAttributesById($userId));
        $this->_template->render(false, false);
    }

    /**
     * Render Form Step5
     */
    public function formStep5()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $request = TeacherRequest::getRequestByUserId($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->set('request', $request);
        $this->set('user', User::getAttributesById($userId));
        $this->set('requestCount', TeacherRequest::getRequestCount($userId));
        $this->set('allowedCount', FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT'));
        $this->_template->render(false, false);
    }

    /**
     * Setup Step1 Form
     */
  public function setupStep1()
{
    $userId = FatUtility::int($this->userId);
    if ($userId < 1) {
        FatUtility::dieJsonError('INVALID_REQUEST: userId missing');
    }

    $frm = $this->getFormStep1($this->siteLangId);
    if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
        $ve = current($frm->getValidationErrors());
        error_log('[TR][setupStep1] validation error: ' . print_r($frm->getValidationErrors(), true));
        FatUtility::dieJsonError('VALIDATION_FAIL: ' . $ve);
    }

    // Optional file upload
    if (!empty($_FILES['user_photo_id']['tmp_name'])) {
        $file = new Afile(Afile::TYPE_TEACHER_APPROVAL_PROOF);
        if (!$file->saveFile($_FILES['user_photo_id'], $userId, true)) {
            $fErr = $file->getError() ?: 'Unknown file error';
            error_log('[TR][setupStep1] file upload error: ' . $fErr);
            FatUtility::dieJsonError('FILE_UPLOAD_ERROR: ' . $fErr);
        }
    }

   $data = [
    'tereq_step'         => 2,
    'tereq_user_id'      => $userId,
    'tereq_language_id'  => $this->siteLangId,
    'tereq_reference'    => $userId . '-' . time(),
    'tereq_date'         => date('Y-m-d H:i:s'),

    'tereq_first_name'   => $post['tereq_first_name'],
    'tereq_last_name'    => $post['tereq_last_name'],
    'tereq_gender'       => $post['tereq_gender'],
    'tereq_phone_code'   => $post['tereq_phone_code'],
    'tereq_phone_number' => $post['tereq_phone_number'],

    'tereq_status'         => TeacherRequest::STATUS_PENDING,
    'tereq_status_updated' => date('Y-m-d H:i:s'),
    'tereq_comments'       => '',

    // 🔹 numeric NOT NULL columns – give real integers
    'tereq_attempts' => 1,   // or 0, depending on how you want to count attempts
    'tereq_terms'    => 0,   // not accepted yet on step 1

    // 🔹 text/JSON columns – empty string / empty JSON is fine
    'tereq_video_link'        => '',
    'tereq_biography'         => '',
    'tereq_teach_langs'       => '[]',
    'tereq_speak_langs'       => '[]',
    'tereq_slang_proficiency' => '[]',
];


    // If there’s already a pending request, update it instead of insert
    $request = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_PENDING);
    if (!empty($request)) {
        $data['tereq_id'] = $request['tereq_id'];
    }

    // Log the payload so we can compare with table schema
    error_log('[TR][setupStep1] about to save: ' . json_encode($data));

    $db = FatApp::getDb();
    $record = new TableRecord(TeacherRequest::DB_TBL);
    $record->assignValues($data);

    if (!$record->addNew([], $data)) {
        $recErr = $record->getError();
        $dbErr  = method_exists($db, 'getError') ? $db->getError() : '';
        error_log('[TR][setupStep1] addNew FAILED. recordError=' . $recErr . ' dbError=' . $dbErr);

        // Return a developer-friendly message TEMPORARILY
        FatUtility::dieJsonError('DB_SAVE_FAILED: ' . ($recErr ?: $dbErr ?: 'unknown'));
    }

    FatUtility::dieJsonSuccess(['step' => 2, 'msg' => 'OK']);
}

    /**
     * Setup Profile Image
     */
   public function setupProfileImage()
{
    if ($this->userId < 1) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }
    if (empty($_FILES['user_profile_image'])) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    $request = TeacherRequest::getRequestByUserId($this->userId, TeacherRequest::STATUS_PENDING);
    if (empty($request)) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    $this->attemptReachedCheck();

    if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
        FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_A_FILE'));
    }

    // 🔹 1) Check upload directory is writable (for debugging AWS)
    $uploadRoot = CONF_UPLOADS_PATH; // e.g. /var/www/html/readwithus/user-uploads/
    if (!is_writable($uploadRoot)) {
        error_log('[TR][setupProfileImage] Upload folder NOT writable: ' . $uploadRoot);
        FatUtility::dieJsonError(
            Label::getLabel('LBL_UPLOAD_FOLDER_NOT_WRITABLE_CONTACT_ADMIN')
        );
    }

    // 🔹 2) Try to save and surface any error from Afile
    try {
        // Teacher approval image
        $file = new Afile(Afile::TYPE_TEACHER_APPROVAL_IMAGE);
        if (!$file->saveFile($_FILES['user_profile_image'], $this->userId, true)) {
            $fErr = $file->getError() ?: 'Unknown upload error';
            error_log('[TR][setupProfileImage] saveFile error: ' . $fErr);
            FatUtility::dieJsonError($fErr);
        }

        // (Optional) also update normal profile image
        /*
        $file = new Afile(Afile::TYPE_USER_PROFILE_IMAGE);
        if (!$file->saveFile($_FILES['user_profile_image'], $this->userId, true)) {
            $fErr = $file->getError() ?: 'Unknown upload error (profile image)';
            error_log('[TR][setupProfileImage] saveFile profile error: ' . $fErr);
            FatUtility::dieJsonError($fErr);
        }
        */

    } catch (Throwable $e) {
        error_log('[TR][setupProfileImage] EXCEPTION: ' . $e->getMessage());
        FatUtility::dieJsonError('ERR_PROFILE_IMAGE_UPLOAD_FAILED');
    }

    $fileUrl = MyUtility::makeFullUrl(
        'Image',
        'show',
        [Afile::TYPE_TEACHER_APPROVAL_IMAGE, $this->userId, Afile::SIZE_LARGE]
    ) . '?' . time();

    FatUtility::dieJsonSuccess([
        'msg'  => Label::getLabel('MSG_File_uploaded_successfully'),
        'file' => $fileUrl,
    ]);
}


    /**
     * Setup Step2 Form
     */
  public function setupStep2()
{
    $userId = FatUtility::int($this->userId);
    if ($userId < 1) {
        FatUtility::dieJsonError('INVALID_REQUEST: userId missing');
    }

    $frm = $this->getFormStep2($this->siteLangId);
    if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
        $ve = current($frm->getValidationErrors());
        error_log('[TR][setupStep2] validation error: ' . print_r($frm->getValidationErrors(), true));
        FatUtility::dieJsonError('VALIDATION_FAIL: ' . $ve);
    }

    $request = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_PENDING);
    if (empty($request)) {
        error_log('[TR][setupStep2] no pending request for userId=' . $userId);
        FatUtility::dieJsonError('INVALID_REQUEST: pending record missing');
    }

    // Ensure profile image exists (approval image or fallback to profile)
    $userImage = (new Afile(Afile::TYPE_TEACHER_APPROVAL_IMAGE))->getFile($this->userId);
    if (empty($userImage)) {
        $userImage = (new Afile(Afile::TYPE_USER_PROFILE_IMAGE))->getFile($this->userId);
        if (empty($userImage)) {
            error_log('[TR][setupStep2] profile picture missing for userId=' . $userId);
            FatUtility::dieJsonError(Label::getLabel('LBL_PROFILE_PICTURE_REQURED'));
        }
    }

    $data = [
        'tereq_step'       => 3,
        'tereq_video_link' => $post['tereq_video_link'],
        'tereq_biography'  => $post['tereq_biography'],
    ];
    error_log('[TR][setupStep2] updating tereq_id=' . $request['tereq_id'] . ' with: ' . json_encode($data));

    $db = FatApp::getDb();
    $record = new TableRecord(TeacherRequest::DB_TBL);
    $record->assignValues($data);

    if (!$record->update(['smt' => 'tereq_id = ?', 'vals' => [$request['tereq_id']]])) {
        $recErr = $record->getError();
        $dbErr  = method_exists($db, 'getError') ? $db->getError() : '';
        error_log('[TR][setupStep2] UPDATE FAILED. recordError=' . $recErr . ' dbError=' . $dbErr);
        FatUtility::dieJsonError('DB_UPDATE_FAILED: ' . ($recErr ?: $dbErr ?: 'unknown'));
    }

    FatUtility::dieJsonSuccess(['step' => 3, 'msg' => 'OK']);
}


    /**
     * Setup Step3 Form
     */
   public function setupStep3()
{
    $userId = FatUtility::int($this->userId);
    if ($userId < 1) {
        FatUtility::dieJsonError('INVALID_REQUEST: userId missing');
    }

    $spokenLangs = SpeakLanguage::getAllLangs($this->siteLangId, true);
    $frm = $this->getFormStep3($this->siteLangId, $spokenLangs);
    if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
        $ve = current($frm->getValidationErrors());
        error_log('[TR][setupStep3] validation error: ' . print_r($frm->getValidationErrors(), true));
        FatUtility::dieJsonError('VALIDATION_FAIL: ' . $ve);
    }

    $request = TeacherRequest::getRequestByUserId($userId, TeacherRequest::STATUS_PENDING);
    if (empty($request)) {
        error_log('[TR][setupStep3] no pending request for userId=' . $userId);
        FatUtility::dieJsonError('INVALID_REQUEST: pending record missing');
    }

    $teachLangs = json_encode(array_filter(FatUtility::int($post['tereq_teach_langs'])));
    $speakLangs = [];
    $speakLangArr = array_filter(FatUtility::int(array_values($post['tereq_speak_langs'])));
    foreach ($speakLangArr as $v) { $speakLangs[] = $v; }

    $speakLangsProf = [];
    $speakLangsProfArr = array_filter(FatUtility::int(array_values($post['tereq_slang_proficiency'])));
    foreach ($speakLangsProfArr as $v) { $speakLangsProf[] = $v; }

    if (empty($speakLangs) || empty($speakLangsProf)) {
        error_log('[TR][setupStep3] missing speak langs or proficiency');
        FatUtility::dieJsonError(Label::getLabel('LBL_SPEAK_LANGUAGE_AND_PROFICIENCY_REQUIRED'));
    }

    $data = [
        'tereq_step'              => 4,
        'tereq_teach_langs'       => $teachLangs,
        'tereq_speak_langs'       => json_encode($speakLangs),
        'tereq_slang_proficiency' => json_encode($speakLangsProf),
    ];
    error_log('[TR][setupStep3] updating tereq_id=' . $request['tereq_id'] . ' with: ' . json_encode($data));

    $db = FatApp::getDb();
    $record = new TableRecord(TeacherRequest::DB_TBL);
    $record->assignValues($data);

    if (!$record->update(['smt' => 'tereq_id = ?', 'vals' => [$request['tereq_id']]])) {
        $recErr = $record->getError();
        $dbErr  = method_exists($db, 'getError') ? $db->getError() : '';
        error_log('[TR][setupStep3] UPDATE FAILED. recordError=' . $recErr . ' dbError=' . $dbErr);
        FatUtility::dieJsonError('DB_UPDATE_FAILED: ' . ($recErr ?: $dbErr ?: 'unknown'));
    }

    FatUtility::dieJsonSuccess(['step' => 4, 'msg' => 'OK']);
}


    /**
     * Setup Step4 Form
     */
   public function setupStep4()
{
    $userId = FatUtility::int($this->userId);
    if ($userId < 1) {
        FatUtility::dieJsonError('INVALID_REQUEST: userId missing');
    }

    $frm = $this->getFormStep4();
    if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
        $ve = current($frm->getValidationErrors());
        error_log('[TR][setupStep4] validation error: ' . print_r($frm->getValidationErrors(), true));
        FatUtility::dieJsonError('VALIDATION_FAIL: ' . $ve);
    }

    $request = TeacherRequest::getRequestByUserId($userId);
    if (empty($request)) {
        error_log('[TR][setupStep4] no request found for userId=' . $userId);
        FatUtility::dieJsonError('INVALID_REQUEST: record missing');
    }

    $qualification = new UserQualification(0, $this->userId);
    $rows = $qualification->getUQualification(false, true);
    if (empty($rows)) {
        error_log('[TR][setupStep4] qualification missing for userId=' . $userId);
        FatUtility::dieJsonError(Label::getLabel('LBL_TEACHER_QUALIFICATION_REQUIRED'));
    }

    $data = ['tereq_step' => 5, 'tereq_terms' => $post['tereq_terms']];
    error_log('[TR][setupStep4] updating tereq_id=' . $request['tereq_id'] . ' with: ' . json_encode($data));

    $db = FatApp::getDb();
    $record = new TableRecord(TeacherRequest::DB_TBL);
    $record->assignValues($data);

    if (!$record->update(['smt' => 'tereq_id = ?', 'vals' => [$request['tereq_id']]])) {
        $recErr = $record->getError();
        $dbErr  = method_exists($db, 'getError') ? $db->getError() : '';
        error_log('[TR][setupStep4] UPDATE FAILED. recordError=' . $recErr . ' dbError=' . $dbErr);
        FatUtility::dieJsonError('DB_UPDATE_FAILED: ' . ($recErr ?: $dbErr ?: 'unknown'));
    }

    // email
    $mail = new FatMailer($this->siteLangId, 'teacher_request_received');
    $vars = [
        '{refnum}'       => $request['tereq_reference'],
        '{name}'         => $request['tereq_first_name'] . ' ' . $request['tereq_last_name'],
        '{phone}'        => $request['tereq_phone_code'] . ' ' . $request['tereq_phone_number'],
        '{request_date}' => $request['tereq_date']
    ];
    $mail->setVariables($vars);
    $mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')]);

    FatUtility::dieJsonSuccess(['step' => 5, 'msg' => 'OK']);
}

    /**
     * Get Step1 Form
     * 
     * @return Form
     */
    private function getFormStep1(): Form
    {
        $frm = new Form('frmFormStep1', ['id' => 'frmFormStep1']);
        $frm->addRequiredField(Label::getLabel('LBL_First_Name'), 'tereq_first_name')->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_Last_Name'), 'tereq_last_name');
        $frm->addRadioButtons(Label::getLabel('LBL_Gender'), 'tereq_gender', User::getGenderTypes(), User::GENDER_MALE)->requirements()->setRequired();
        $countries = Country::getAll($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_PHONE_CODE'), 'tereq_phone_code', array_column($countries, 'phone_code', 'country_id'), '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired(true);
        $fld = $frm->addTextBox(Label::getLabel('LBL_PHONE_NUMBER'), 'tereq_phone_number');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PHONE_NO_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('LBL_PHONE_NO_VALIDATION_MSG'));
        $frm->addHiddenField('', 'resubmit', 0);
        $frm->addFileUpload(Label::getLabel('LBL_Photo_Id'), 'user_photo_id');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Step2 Form
     * 
     * @return Form
     */
    private function getFormStep2(): Form
    {
        $frm = new Form('frmFormStep2', ['id' => 'frmFormStep2']);
        $frm->addFileUpload(Label::getLabel('LBL_Profile_Picture'), 'user_profile_image', ['onchange' => 'popupImage(this)', 'accept' => 'image/*']);
        $frm->addTextArea(Label::getLabel('LBL_Biography'), 'tereq_biography')->requirements()->setLength(1, 500);
        $fld = $frm->addTextBox(Label::getLabel('LBL_Introduction_video'), 'tereq_video_link');
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::INTRODUCTION_VIDEO_LINK_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_Valid_Video_Link'));
        $frm->addHiddenField('', 'update_profile_img', Label::getLabel('LBL_Update_Profile_Picture'), ['id' => 'update_profile_img']);
        $frm->addHiddenField('', 'rotate_left', Label::getLabel('LBL_Rotate_Left'), ['id' => 'rotate_left']);
        $frm->addHiddenField('', 'rotate_right', Label::getLabel('LBL_Rotate_Right'), ['id' => 'rotate_right']);
        $frm->addHiddenField('', 'img_data', '', ['id' => 'img_data']);
        $frm->addHiddenField('', 'action', 'avatar', ['id' => 'avatar-action']);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Step3 Form
     * 
     * @param int $langId
     * @param type $spokenLangs
     * @return Form
     */
    private function getFormStep3($langId, $spokenLangs): Form
    {
        $frm = new Form('frmFormStep3', ['id' => 'frmFormStep3']);
        $profArr = SpeakLanguage::getProficiencies();
        $teachLanguages = TeachLanguage::getAllLangs($langId, true);
        $fld = $frm->addCheckBoxes(Label::getLabel('LBL_LANGUAGE_TO_TEACH'), 'tereq_teach_langs', $teachLanguages);
        $fld->requirements()->setSelectionRange(1, count($teachLanguages));
        $fld->requirements()->setRequired();
        $langArr = $spokenLangs ?: SpeakLanguage::getAllLangs($langId, true);
        $proficiencyLabel = stripslashes(Label::getLabel("LBL_I_DO_NOT_SPEAK_THIS_LANGUAGE"));
        foreach ($langArr as $key => $lang) {
            $speekLangField = $frm->addCheckBox(Label::getLabel('LBL_LANGUAGE_I_SPEAK'), 'tereq_speak_langs[' . $key . ']', $key, ['class' => 'uslang_slang_id'], false, '0');
            $proficiencyField = $frm->addSelectBox(Label::getLabel('LBL_LANGUAGE_PROFICIENCY'), 'tereq_slang_proficiency[' . $key . ']', $profArr, '', ['class' => 'uslang_proficiency select__dropdown'], $proficiencyLabel);
            $proficiencyField->requirements()->setRequired();
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'gt', $proficiencyField->getName(), $proficiencyField->requirements());
            $proficiencyField->requirements()->setRequired(false);
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'le', $proficiencyField->getName(), $proficiencyField->requirements());
            $speekLangField->requirements()->setRequired();
            $proficiencyField->requirements()->addOnChangerequirementUpdate(0, 'gt', $proficiencyField->getName(), $speekLangField->requirements());
            $speekLangField->requirements()->setRequired(false);
            $proficiencyField->requirements()->addOnChangerequirementUpdate(0, 'le', $proficiencyField->getName(), $speekLangField->requirements());
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    /**
     * Get Step4 Form
     * 
     * @return Form
     */
    private function getFormStep4(): Form
    {
        $frm = new Form('frmFormStep4', ['id' => 'frmFormStep4']);
        $frm->addCheckBox(Label::getLabel('LBL_ACCEPT_TEACHER_APPROVAL_TERMS_&_CONDITION'), 'tereq_terms', 1, [], false, 0)->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Search Teacher Qualification
     */
    public function searchTeacherQualification()
    {
        $qualification = new UserQualification(0, $this->userId);
        $this->set("rows", $qualification->getUQualification(false, true));
        $this->set("userId", $this->userId);
        $this->_template->render(false, false);
    }

    /**
     * Render Teacher Qualification Form
     */
    public function teacherQualificationForm()
    {
        $qualificationId = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        $frm = UserQualification::getForm();
        if ($qualificationId > 0) {
            $qualification = new UserQualification($qualificationId, $this->userId);
            if (!$row = $qualification->getQualiForUpdate()) {
                FatUtility::dieJsonError($qualification->getError());
            }
            $frm->fill($row);
            $field = $frm->getField('certificate');
            $field->requirements()->setRequired(false);
        }
        $this->set('frm', $frm);
        $this->set('qualificationId', $qualificationId);
        $this->_template->render(false, false);
    }

    /**
     * Setup Teacher Qualification
     */
  public function setupTeacherQualification()
{
    $frm = UserQualification::getForm();

    // Validate incoming fields against the form
    if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
        FatUtility::dieJsonError(current($frm->getValidationErrors()));
    }

    // Always attach the current user
    $post['uqualification_user_id'] = $this->userId;

    // ---- Hardening: provide defaults for NOT NULL columns ----
    // (adjust or remove any that don't exist in your schema)
    if (!isset($post['uqualification_active']))   { $post['uqualification_active']   = 1; }
    if (!isset($post['uqualification_verified'])) { $post['uqualification_verified'] = 0; }
    if (!isset($post['uqualification_order']))    { $post['uqualification_order']    = 0; }

    // Optional: make sure id is int
    $qualificationId = FatUtility::int($post['uqualification_id'] ?? 0);

    $db = FatApp::getDb();
    $db->startTransaction();

    try {
        // If updating, you can optionally verify the row belongs to this user here.
        $qualification = new UserQualification($qualificationId, $this->userId);
        $qualification->assignValues($post);

        if (!$qualification->save()) {
            // Surface the model error (and last query if needed)
            $db->rollbackTransaction();
            $err = $qualification->getError() ?: Label::getLabel('MSG_SETUP_FAILED');
            FatUtility::dieJsonError($err);
        }

        // Handle certificate upload if provided
        if (!empty($_FILES['certificate']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['certificate']['tmp_name'])) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_A_FILE'));
            }
            $savedId = $qualification->getMainTableRecordId();
            $file    = new Afile(Afile::TYPE_USER_QUALIFICATION_FILE);

            if (!$file->saveFile($_FILES['certificate'], $savedId, true)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($file->getError());
            }
        }

        if (!$db->commitTransaction()) {
            FatUtility::dieJsonError(Label::getLabel('MSG_SOMETHING_WENT_WRONG'));
        }

        // Success
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETUP_SUCCESSFUL'));

    } catch (Throwable $e) {
        $db->rollbackTransaction();
        // Return a concise, useful error
        FatUtility::dieJsonError('ERR_QUALIFICATION_SAVE: ' . $e->getMessage());
    }
}

    /**
     * Delete Teacher Qualification
     */
    public function deleteTeacherQualification()
    {
        $qualificationId = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        if ($qualificationId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $qualification = new UserQualification($qualificationId, $this->userId);
        if (!$row = $qualification->getQualiForUpdate()) {
            FatUtility::dieJsonError($qualification->getError());
        }
        $userQualification = new UserQualification($qualificationId);
        if (true !== $userQualification->deleteRecord()) {
            FatUtility::dieJsonError($userQualification->getError());
        }
        $file = new Afile(Afile::TYPE_USER_QUALIFICATION_FILE);
        $file->removeFile($qualificationId, true);
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_QUALIFICATION_REMOVED_SUCCESSFULY'));
    }

    /**
     * Logout Guest User
     */
    public function logoutGuestUser()
    {
        TeacherRequest::closeSession();
        FatApp::redirectUser(MyUtility::makeUrl());
    }

    /**
     * Get Apply Teach Form
     * 
     * @return Form
     */
    private function getApplyTeachFrm(): Form
    {
        $frm = new Form('frmApplyTeachFrm');
        $frm->addHiddenField('', 'user_id', 0);
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email_ID'), 'user_email', '', ['autocomplete' => 'off']);
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'user_password');
        $fld->requirements()->setRequired();
        $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel(AppConstant::PASSWORD_CUSTOM_ERROR_MSG));
        $frm->addHiddenField('', 'user_dashboard', User::TEACHER);
        $frm->addHiddenField('', 'agree', 1)->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_REGISTER_WITH_EMAIL'));
        return $frm;
    }

    private function getApplyToTeachFaqs()
    {
        $srch = Faq::getSearchObject($this->siteLangId, false);
        $srch->addMultipleFields(['faq_identifier', 'faq_id', 'faq_category', 'faq_active', 'faq_title', 'faq_description']);
        $srch->addCondition('faq_category', '=', Faq::CATEGORY_APPLY_TO_TEACH);
        $srch->addOrder('faq_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        return $records;
    }

    /**
     * Teacher Setup
     */
    public function teacherSetup()
    {
        $frm = $this->getTeacherSignupForm();
        $post = FatApp::getPostedData();
        if (!isset($post['user_first_name'])) {
            $post['user_first_name'] = strstr($post['user_email'], '@', true);
        }
        if (!$post = $frm->getFormDataFromArray($post)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!MyUtility::validatePassword($post['user_password'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
        }
        $db = FatApp::getDb();
// 1) Email format
if (!filter_var($post['user_email'], FILTER_VALIDATE_EMAIL)) {
    FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_EMAIL'));
}

// 2) Email uniqueness (server-side hard stop)
$srch = new SearchBase('tbl_users', 'u');
$srch->addCondition('u.user_email', '=', $post['user_email']);
$srch->addFld('u.user_id');
if (FatApp::getDb()->fetch($srch->getResultSet())) {
    FatUtility::dieJsonError(Label::getLabel('MSG_EMAIL_ALREADY_REGISTERED'));
}

// 3) Password policy (defense-in-depth)
if (!MyUtility::validatePassword($post['user_password'])) {
    FatUtility::dieJsonError(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
}

// 4) Terms agreed
if (empty($post['agree'])) {
    FatUtility::dieJsonError(Label::getLabel('MSG_Terms_and_Condition_and_Privacy_Policy_are_mandatory.'));
}


        $db->startTransaction();
        $userData = array_merge($post, [
            'user_dashboard' => User::TEACHER,
            'user_registered_as' => User::TEACHER,
            'user_lang_id' => MyUtility::getSiteLangId(),
            'user_timezone' => MyUtility::getSiteTimezone(),
        ]);
        $user = new User();
        $user->assignValues($userData);
        if (FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION') == AppConstant::NO) {
            $user->setFldValue('user_verified', date('Y-m-d H:i:s'));
        }
        if (empty(FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION'))) {
            $user->setFldValue('user_active', AppConstant::YES);
        }
        if (!$user->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel("MSG_USER_COULDDDD_NOT_BE_SET"));
        }
        if (!$user->setSettings($userData)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel("MSG_USER_COULD_NOT_BE_SET"));
        }
        if (!$user->setPassword($post['user_password'])) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel("MSG_USER_COULD_NOT_BE_SET"));
        }
        if (!$db->commitTransaction()) {
            FatUtility::dieJsonError(Label::getLabel("MSG_USER_COULD_NOT_BE_SET"));
        }
        $userData['user_id'] = $user->getMainTableRecordId();
          error_log("TeacherRequestController: Created user with ID: " . $userData['user_id']);
    
        $auth = new UserAuth();
        $res = $auth->sendSignupEmails($userData);
       $autoLogin = (
        FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION') == AppConstant::NO &&
        FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION') == AppConstant::NO &&
        FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION') == AppConstant::YES
    );

    if ($autoLogin) {
        if (!$auth->login($userData['user_email'], $userData['user_password'], MyUtility::getUserIp())) {
            FatUtility::dieJsonError($auth->getError());
        }
        // logged-in users will have $this->siteUserId
    } else {
        // make 100% sure the guest session carries the user id we just created
         $sessionCheck = TeacherRequest::getSession('user_id');
        TeacherRequest::startSession(['user_id' => $userId, 'user_email' => $userData['user_email']]);
    }

    FatUtility::dieJsonSuccess([
        'msg'         => $res['msg'] ?? Label::getLabel('LBL_REGISTERATION_SUCCESSFULL'),
        'redirectUrl' => MyUtility::makeUrl('TeacherRequest', 'form')
    ]);
    }

    /**
     * Get Teacher Signup Form
     * 
     * @return Form
     */
    private function getTeacherSignupForm(): Form
    {
        $frm = new Form('signupForm');
        $frm->addRequiredField(Label::getLabel('LBL_FIRST_NAME'), 'user_first_name');
        $frm->addTextBox(Label::getLabel('LBL_LAST_NAME'), 'user_last_name');
        $fld = $frm->addEmailField(Label::getLabel('LBL_EMAIL_ID'), 'user_email', '', ['autocomplete="off"']);
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_PASSWORD'), 'user_password');
        $fld->requirements()->setRequired();
        $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_8_Digit_AlphaNumeric_Password'));
        $termsConditionLabel = Label::getLabel('LBL_I_accept_to_the');
        $fld = $frm->addCheckBox($termsConditionLabel, 'agree', 1);
        $fld->requirements()->setRequired();
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Terms_and_Condition_and_Privacy_Policy_are_mandatory.'));
        $frm->addHiddenField('', 'user_dashboard', User::LEARNER);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Register'));
        return $frm;
    }
}
