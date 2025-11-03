<?php

/**
 * Teacher Request Controller is used for Teacher Request handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class TeacherRequestsController extends AdminBaseController
{

    /**
     * Initialize Teacher Request
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTeacherRequests();
    }

    /**
     * Render Teacher Request Search Form
     */
    public function index()
    {
        $this->set('form', $this->getSearchForm());
        $this->_template->render();
    }

    /**
     * Search & List Teacher Request
     */
    public function search()
    {
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srchForm = $this->getSearchForm();
        $post = $srchForm->getFormDataFromArray(FatApp::getPostedData());
        $srch = new SearchBase(TeacherRequest::DB_TBL, 'tereq');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'user.user_id = tereq.tereq_user_id', 'user');
        $srch->addMultipleFields(['tereq_comments', 'user_username', 'user_email', 'user_deleted',
            'user_first_name', 'user_last_name', 'tereq_id', 'tereq_user_id', 'tereq_status',
            'tereq_date', 'tereq_reference', 'tereq_first_name', 'tereq_last_name']);
        $srch->addCondition('tereq.tereq_step', '=', 5);
        $srch->addOrder('tereq_id', 'desc');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        if (!empty($post['keyword'])) {
            $cnd = $srch->addCondition('tereq_reference', '=', '%' . $post['keyword'] . '%', 'AND');
            $cnd->attachCondition('mysql_func_concat(`user_first_name`," ",`user_last_name`)', 'like', '%' . $post['keyword'] . '%', 'OR', true);
            $cnd->attachCondition('user_email', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cnd->attachCondition('user_username', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cnd->attachCondition('tereq_reference', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        if (!empty($post['date_from'])) {
            if ($post['status'] > -1) {
                $srch->addCondition('tereq.tereq_status', '=', $post['status']);
            }
            $post['date_from'] = MyDate::formatToSystemTimezone($post['date_from']);
            $srch->addCondition('tereq.tereq_date', '>=', $post['date_from'], 'AND', true);
        }
        if ($post['status'] > -1) {
            $srch->addCondition('tereq.tereq_status', '=', $post['status']);
        }
        if (!empty($post['date_to'])) {
            if ($post['status'] > -1 && empty($post['date_from'])) {
                $srch->addCondition('tereq.tereq_status', '=', $post['status']);
            }
            $post['date_to'] = MyDate::formatToSystemTimezone($post['date_to'] . " 23:59:59");
            $srch->addCondition('tereq.tereq_date', '<=', $post['date_to'], 'AND', true);
        }
        $this->sets([
            'arrListing' => FatApp::getDb()->fetchAll($srch->getResultSet()),
            'canEdit' => $this->objPrivilege->canEditTeacherRequests(true),
            'postedData' => $post, 'page' => $page, 'pageSize' => $pagesize,
            'pagingArr' => ['page' => $page, 'pageCount' => $srch->pages(), 'recordCount' => $srch->recordCount()
            ]
        ]);
        $this->_template->render(false, false);
    }

    public function view(int $requestId)
    {
        $srch = new SearchBase(TeacherRequest::DB_TBL, 'tereq');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'u.user_id = tereq.tereq_user_id', 'u');
        $srch->addDirectCondition('u.user_deleted IS NULL');
        $srch->addCondition('tereq_id', '=', $requestId);
        $srch->addCondition('tereq.tereq_step', '=', 5);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields([
            'tereq_id', 'tereq_user_id', 'tereq_reference',
            'tereq_date', 'tereq_attempts', 'tereq_comments',
            'tereq_status', 'tereq_first_name', 'tereq_last_name',
            'tereq_gender', 'tereq_phone_number', 'tereq_phone_code',
            'tereq_video_link', 'tereq_biography', 'tereq_teach_langs',
            'tereq_speak_langs', 'tereq_slang_proficiency'
        ]);
        $srch->addGroupBy('tereq_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_USER_OR_REQUEST_NOT_FOUND'));
        }
        if(!empty($row['tereq_phone_code'])){
            $row['tereq_phone_code'] = Country::getAttributesById($row['tereq_phone_code'], 'country_dial_code');
        }       
        $row['tereq_teach_langs'] = json_decode($row['tereq_teach_langs'], true);
        $row['tereq_speak_langs'] = json_decode($row['tereq_speak_langs'], true);
        $row['tereq_slang_proficiency'] = json_decode($row['tereq_slang_proficiency'], true);
        $userImage = (new Afile(Afile::TYPE_TEACHER_APPROVAL_IMAGE))->getFile($row['tereq_user_id']);
        if (empty($userImage)) {
            $userImage = (new Afile(Afile::TYPE_USER_PROFILE_IMAGE))->getFile($row['tereq_user_id']);
        }
        $this->sets([
            'row' => $row, 'userImage' => $userImage,
            'photoIdRow' => (new Afile(Afile::TYPE_TEACHER_APPROVAL_PROOF))->getFile($row['tereq_user_id']),
            'speakLanguagesArr' => SpeakLanguage::getNames($this->siteLangId, $row['tereq_speak_langs']),
            'teachLanguages' => TeachLanguage::getNames($this->siteLangId, $row['tereq_teach_langs']),
            'speakLanguageProfArr' => SpeakLanguage::getProficiencies()
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Change Status Form
     * 
     * @param type $requestId
     */
    public function changeStatusForm($requestId)
    {
        $requestId = FatUtility::int($requestId);
        $srch = new SearchBase(TeacherRequest::DB_TBL, 'tereq');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'u.user_id = tereq.tereq_user_id', 'u');
        $srch->addCondition('tereq_id', '=', $requestId);
        $srch->addCondition('tereq.tereq_step', '=', 5);
        $srch->addMultipleFields(['tereq_id']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $frm = $this->getUpdateForm();
        $frm->fill(['tereq_id' => $requestId]);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    /**
     * Update Status
     */
    public function updateStatus()
    {
        $this->objPrivilege->canEditTeacherRequests();
        $frm = $this->getUpdateForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $requestId = FatApp::getPostedData('tereq_id', FatUtility::VAR_INT, 0);
        $comment = FatApp::getPostedData('tereq_comments', FatUtility::VAR_STRING, '');
        $srch = new SearchBase(TeacherRequest::DB_TBL, 'tr');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'u.user_id = tr.tereq_user_id', 'u');
        $srch->joinTable(UserSetting::DB_TBL, 'LEFT JOIN', 'us.user_id = u.user_id', 'us');
        $srch->addCondition('tereq_id', '=', $requestId);
        $srch->addCondition('tereq_status', '=', TeacherRequest::STATUS_PENDING);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields([
            'user_lang_id', 'tereq_status', 'tereq_user_id', 'tereq_language_id', 'tereq_comments',
            'tereq_reference', 'user_first_name', 'user_last_name', 'user_email', 'tereq_first_name',
            'tereq_last_name', 'tereq_gender', 'tereq_phone_number', 'tereq_phone_code', 'tereq_biography',
            'tereq_video_link', 'tereq_teach_langs', 'tereq_speak_langs', 'tereq_slang_proficiency'
        ]);
        $requestRow = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($requestRow)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $requestRow['tereq_comments'] = $comment;
        $db = FatApp::getDb();
        $db->startTransaction();
        $post['tereq_status_updated'] = date('Y-m-d H:i:s');
        $teacherRequest = new TeacherRequest($requestId);
        $teacherRequest->assignValues($post);
        if (!$teacherRequest->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($teacherRequest->getError());
        }
        if ($post['tereq_status'] == TeacherRequest::STATUS_APPROVED) {
            $user = new User($requestRow['tereq_user_id']);
            $user->assignValues([
                'user_is_teacher' => AppConstant::YES,
                'user_dashboard' => User::TEACHER,
                'user_first_name' => $requestRow['tereq_first_name'],
                'user_last_name' => $requestRow['tereq_last_name'],
                'user_gender' => $requestRow['tereq_gender']
            ]);
            if (!$user->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($user->getError());
            }
             $registeredAs    = (int)($uRow['user_registered_as'] ?? 0) ?: User::TEACHER;
            $data = [
                'user_phone_number' => $requestRow['tereq_phone_number'],
                'user_phone_code' => $requestRow['tereq_phone_code'],
                'user_video_link' => $requestRow['tereq_video_link'],
                'user_dashboard'      => User::TEACHER,
                'user_registered_as' => $registeredAs,
                'teacher_wallet_balance' => 0,
                'user_wallet_balance' => 0,
            ];
            $userSetting = new UserSetting($requestRow['tereq_user_id']);
            if (!$userSetting->saveData($data)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($userSetting->getError());
            }
            $langData = [
                'user_biography' => $requestRow['tereq_biography'],
                'userlang_user_id' => $requestRow['tereq_user_id'],
                'userlang_lang_id' => $requestRow['tereq_language_id'],
            ];
            $userLang = new TableRecord(User::DB_TBL_LANG);
            $userLang->assignValues($langData);
            if (!$userLang->addNew([], $langData)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($userLang->getError());
            }
            $fileData = (new Afile(Afile::TYPE_TEACHER_APPROVAL_IMAGE))->getFile($requestRow['tereq_user_id']);
            if (!empty($fileData) && !empty($fileData['file_path'] != "")) {
                $fileData['file_type'] = Afile::TYPE_USER_PROFILE_IMAGE;
                unset($fileData['file_id']);
                if (!$db->insertFromArray(Afile::DB_TBL, $fileData, false, [], $fileData)) {
                    $db->rollbackTransaction();
                    FatUtility::dieJsonError($db->getError());
                }
            }
            $teachLangsArray = json_decode($requestRow['tereq_teach_langs'], true);
            if (!empty($teachLangsArray)) {
                foreach ($teachLangsArray as $langId) {
                    $userTeachLanguage = new UserTeachLanguage($requestRow['tereq_user_id']);
                    if (!$userTeachLanguage->saveTeachLang($langId)) {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError($userTeachLanguage->getError());
                    }
                }
            }
            $speakLangArr = json_decode($requestRow['tereq_speak_langs'], true);
            $profArr = json_decode($requestRow['tereq_slang_proficiency'], true);
            if (!empty($speakLangArr)) {
                $userSpeakLanguage = new UserSpeakLanguage($requestRow['tereq_user_id']);
                foreach ($speakLangArr as $key => $slangId) {
                    if (!$userSpeakLanguage->saveLang($slangId, $profArr[$key])) {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError($userSpeakLanguage->getError());
                    }
                }
            }
            $dataArr = ['uqualification_active' => AppConstant::YES];
            if (!$db->updateFromArray(UserQualification::DB_TBL, $dataArr, ['smt' => 'uqualification_user_id = ?', 'vals' => [$requestRow['tereq_user_id']]])) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($db->getError());
            }
            $stat = new TeacherStat($requestRow['tereq_user_id']);
            $stat->setTeachLangPrices();
            $stat->setSpeakLang();
            $stat->setQualification();
            $notifi = new Notification($requestRow['tereq_user_id'], Notification::TYPE_TEACHER_APPROVAL);
            $notifi->sendNotification();
        }
        $db->commitTransaction();
        $requestRow['tereq_status'] = $post['tereq_status'];
        $this->sendStatusUpdateEmail($this->siteLangId, $requestRow);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Status_Updated_Successfully'));
    }

    /**
     * Send Status Update Email
     * 
     * @param int $langId
     * @param array $data
     * @return bool
     */
    private function sendStatusUpdateEmail(int $langId, array $data): bool
    {
        $vars = [
            '{user_first_name}' => $data['tereq_first_name'],
            '{user_last_name}' => $data['tereq_last_name'],
            '{user_full_name}' => $data['tereq_first_name'] . ' ' . $data['tereq_last_name'],
            '{reference_number}' => $data['tereq_reference'],
            '{new_request_status}' => TeacherRequest::getStatuses($data['tereq_status']),
            '{request_comments}' => ($data['tereq_comments'] != '') ? nl2br($data['tereq_comments']) : Label::getLabel('LBL_N/A', $langId),
        ];
        $mail = new FatMailer($langId, 'teacher_request_status_change_learner');
        $mail->setVariables($vars);
        if ($mail->sendMail([$data['user_email']])) {
            return true;
        }
        return false;
    }

    /**
     * Search Qualifications
     * 
     * @param int $userId
     */
    public function searchQualifications($userId)
    {
        $qualification = new UserQualification(0, FatUtility::int($userId));
        $this->set('arrListing', $qualification->getUQualification(false, true));
        $this->_template->render(false, false);
    }

    /**
     * Get Update Form
     * 
     * @return Form
     */
    private function getUpdateForm(): Form
    {
        $frm = new Form('frmTeacherRequestUpdateForm');
        $frm->addHiddenField('', 'tereq_id', 0)->requirements()->setInt();
        $statusArr = TeacherRequest::getStatuses();
        unset($statusArr[TeacherRequest::STATUS_PENDING]);
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'tereq_status', $statusArr, '')->requirements()->setRequired();
        $frm->addTextArea(Label::getLabel('LBL_REASON_FOR_CANCELLATION'), 'tereq_comments', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update'));
        return $frm;
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmSrch');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '');
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'status', ['-1' => Label::getLabel('LBL_ALL')] + TeacherRequest::getStatuses(), '', [], '');
        $frm->addDateField(Label::getLabel('LBL_DATE_FROM'), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_DATE_TO'), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $fldSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEARCH'));
        $fldCancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_CLEAR'));
        $fldSubmit->attachField($fldCancel);
        return $frm;
    }

}
