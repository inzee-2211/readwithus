<?php

/**
 * Messages Controller is used for handling Messages
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class MessagesController extends DashboardController
{

    /**
     * Initialize Messages
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Search Form
     */
    public function index($threadId = 0)
    {
        $this->set('frmSrch', $this->getSearchForm());
        $this->set('threadId', $threadId);
        $this->_template->render();
    }

    /**
     * Search & List Messages
     */
    public function search()
    {
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $srch = Thread::getThreads($this->siteUserId);
        $srch->addFld('(CASE WHEN tfr.user_id = ' . $this->siteUserId . ' THEN  CONCAT(tfto.user_first_name, " ", tfto.user_last_name) ELSE CONCAT(tfr.user_first_name, " ", tfr.user_last_name) END) AS otherUser');
        if (isset(FatApp::getPostedData()['is_unread']) and (FatApp::getPostedData()['is_unread'] != '' || FatApp::getPostedData()['is_unread'] == 1)) {
            $srch->addHaving('message_is_unread', '=', FatApp::getPostedData()['is_unread']);
        }
        if ($post['keyword'] != '') {
            $srch->addHaving('otherUser', 'like', '%' . $post['keyword'] . '%');
        }
        $srch->joinTable(Afile::DB_TBL, 'LEFT JOIN', 'file.file_record_id = tfr.user_id and file.file_type = ' . Afile::TYPE_USER_PROFILE_IMAGE, 'file');
        $srch->addFld('file.file_id');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet(), 'thread_id');
        $this->set('postedData', $post);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('isActive', FatApp::getPostedData('isactive', FatUtility::VAR_INT, 0));
        $this->_template->render(false, false);
    }

    /**
     * Initiate Conversation
     * 
     * @param int $userId
     */
    public function initiate($userId)
    {
        
        if ($userId < 1 || $userId == $this->siteUserId) {
            FatUtility::dieJsonError([
                'msg' => Label::getLabel('LBL_INVALID_REQUEST'),
                'redirectUrl' => CommonHelper::redirectUserReferer(true)
            ]);
        }
        if (User::isUserDelete($userId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $receiverIsTeacher = User::getAttributesById($userId, 'user_is_teacher');
        if (empty($this->siteUser['user_is_teacher']) && empty($receiverIsTeacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $userArr = [$userId, $this->siteUserId];
        $threadobj = new Thread();
        $threadId = $threadobj->getThreadId($userArr);
        $srch = new MessageSearch();
        $srch->joinThreadMessage();
        $srch->joinMessagePostedFromUser();
        $srch->joinMessagePostedToUser();
        $srch->addMultipleFields(['tth.*', 'ttm.message_id', 'ttm.message_text', 'ttm.message_date', 'ttm.message_is_unread']);
        $srch->addCondition('ttm.message_deleted', '=', 0);
        $srch->addCondition('tth.thread_id', '=', $threadId);
        $cnd = $srch->addCondition('ttm.message_from', '=', $userId);
        $cnd->attachCondition('ttm.message_to', '=', $userId, 'OR');
        $srch->addOrder('message_id', 'DESC');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet(), 'message_id');
        if ($srch->recordCount() > 0) {
            $json['threadId'] = $threadId;
            $json['redirectUrl'] = MyUtility::makeUrl('Messages');
            FatUtility::dieJsonSuccess($json);
        }
        $frm = $this->sendMessageForm($this->siteLangId);
        $frm->fill(array('message_thread_id' => $threadId));
        $this->set('frm', $frm);

        $this->sets([
            'allowedExtensions' => Afile::getAllowedExts(Afile::TYPE_MESSAGE_ATTACHMENT),
            'fileSize' => Afile::getAllowedUploadSize(Afile::TYPE_MESSAGE_ATTACHMENT)
        ]);
        $json['html'] = $this->_template->render(false, false, 'messages/generate-thread-pop-up.php', true, false);
        FatUtility::dieJsonSuccess($json);
        if ($threadId) {
            FatApp::redirectUser(MyUtility::makeUrl('Messages', 'thread', array($threadId)));
        }
        Message::addErrorMessage($threadobj->getError());
        CommonHelper::redirectUserReferer();
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmMessageSrch');
        $frm->addTextBox(Label::getLabel('LBL_From'), 'keyword');
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'is_unread', [0 => Label::getLabel('LBL_Read'), 1 => Label::getLabel('LBL_Unread')], [], [], Label::getLabel('LBL_Select'));
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $fldSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Submit'));
        $fldCancel = $frm->addResetButton("", "btn_clear", Label::getLabel('LBL_Clear'), ['onclick' => 'clearSearch();', 'class' => 'btn--clear']);
        $fldSubmit->attachField($fldCancel);
        return $frm;
    }

    /**
     * Send Message Form
     * 
     * @param int  $langId
     * @param bool $isMsgRequired
     * @return Form
     */
    private function sendMessageForm(int $langId, bool $isMsgRequired = true): Form
    {
        $frm = new Form('frmSendMessage');
        $fld = $frm->addTextarea(Label::getLabel('LBL_MESSAGE', $langId), 'message_text', '');
        $fld->requirements()->setRequired($isMsgRequired);
        $fld->requirements()->setLength(0, 1000);
        $frm->addFileUpload('', 'message_file');
        $frm->addHiddenField('', 'message_thread_id');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEND', $langId));
        return $frm;
    }

    /**
     * Message Search
     */
    public function messageSearch()
    {
        $userId = $this->siteUserId;
        $post = FatApp::getPostedData();
        $threadId = FatUtility::int($post['thread_id']);
        $threadUsers = Thread::getThreadUsers($threadId);
        if (1 > $threadId || !in_array($userId, $threadUsers)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_ACCESS'));
        }
        $otherUserId = ($threadUsers[0] == $userId) ? $threadUsers[1] : $threadUsers[0];
        $otherUserDetail = User::getAttributesById($otherUserId, ['user_id', 'user_first_name', 'user_last_name', 'user_deleted', 'user_is_teacher', 'user_username']);
        $post['page'] = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $srch = new MessageSearch();
        $srch->joinThreadMessage();
        $srch->joinMessagePostedFromUser();
        $srch->joinMessagePostedToUser();
        $srch->addMultipleFields(['tth.*', 'ttm.message_id', 'ttm.message_text', 'ttm.message_date', 'ttm.message_is_unread']);
        $srch->addCondition('ttm.message_deleted', '=', 0);
        $srch->addCondition('tth.thread_id', '=', $threadId);
        $cnd = $srch->addCondition('ttm.message_from', '=', $userId);
        $cnd->attachCondition('ttm.message_to', '=', $userId, 'OR');
        $srch->addOrder('message_id', 'DESC');
        $srch->setPageNumber($post['page']);
        $srch->setPageSize(AppConstant::PAGESIZE);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet(), 'message_id');
        ksort($records);
        /* fetch attachments */
        $messageIds = array_keys($records);
        $attachmentsList = [];
        if (count($messageIds) > 0) {
            $fileSrch = new SearchBase(Afile::DB_TBL, 'af');
            $fileSrch->addMultipleFields(['file_id', 'file_record_id', 'file_name']);
            $fileSrch->addCondition('file_type', '=', Afile::TYPE_MESSAGE_ATTACHMENT);
            $fileSrch->addCondition('file_record_id', 'IN', $messageIds);
            $fileSrch->doNotCalculateRecords();
            $fileSrch->doNotLimitRecords();
            $attachmentsList = FatApp::getDb()->fetchAll($fileSrch->getResultSet(), 'file_record_id');
        }
        $this->set('attachmentsList', $attachmentsList);
        $threadObj = new Thread($threadId);
        if (!$threadObj->markUserMessageRead($threadId, $userId)) {
            Message::addErrorMessage($threadObj->getError());
        }
        $this->set('allowedExtensions', Afile::getAllowedExts(Afile::TYPE_MESSAGE_ATTACHMENT));
        $file = new Afile(Afile::TYPE_USER_PROFILE_IMAGE);
        $userImage = $file->getFile($otherUserId);
        $frm = $this->sendMessageForm($this->siteLangId);
        $frm->fill(['message_thread_id' => $threadId]);
        $this->sets([
            'frm' => $frm,
            'allowedExtensions' => Afile::getAllowedExts(Afile::TYPE_MESSAGE_ATTACHMENT),
            'fileSize' => Afile::getAllowedUploadSize(Afile::TYPE_MESSAGE_ATTACHMENT),
            'arrListing' => $records,
            'threadId' => $threadId,
            'userImage' => $userImage,
            'otherUserDetail' => $otherUserDetail,
            'pageCount' => $srch->pages(),
            'page' => $post['page'],
        ]);
        $json['html'] = $this->_template->render(false, false, 'messages/message-search.php', true);
        $json['msg'] = '';
        FatUtility::dieJsonSuccess($json);
    }

    /**
     * Send Message
     */
    public function sendMessage()
    {
        $userId = $this->siteUserId;
        /* if file is uploaded then set msg fields as non required */
        $isFileUploadRequest = false;
        if (isset($_FILES['message_file']['name']) && !empty($_FILES['message_file']['name'])) {
            if (!CommonHelper::validateMaxUploadSize($_FILES['message_file']['size'])) {
                $label = Label::getLabel('LBL_YOUR_FILE_SIZE_HAS_EXCEEDED_THE_ALLOWED_SIZE_OF_{size}');
                $label = str_replace('{size}', Afile::getAllowedUploadSize(Afile::TYPE_MESSAGE_ATTACHMENT), $label);
                FatUtility::dieJsonError($label);
            }
            $isFileUploadRequest = true;
        }
        $frm = $this->sendMessageForm($this->siteLangId, !$isFileUploadRequest);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $threadId = FatUtility::int($post['message_thread_id']);
        $threadUsers = Thread::getThreadUsers($threadId);
        if (empty($threadUsers) || 1 > $threadId || !in_array($this->siteUserId, $threadUsers)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_ACCESS'));
        }
        $messageSendTo = ($threadUsers[0] == $userId) ? $threadUsers[1] : $threadUsers[0];
        if (User::isUserDelete($messageSendTo)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_ACCESS'));
        }
        $receiverIsTeacher = User::getAttributesById($messageSendTo, 'user_is_teacher');
        if (empty($this->siteUser['user_is_teacher']) && empty($receiverIsTeacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $srch = new MessageSearch();
        $srch->addMultipleFields(['tth.*']);
        $srch->addCondition('tth.thread_id', '=', $threadId);
        $rs = $srch->getResultSet();
        $threadDetails = [];
        if ($rs) {
            $threadDetails = FatApp::getDb()->fetch($rs);
        }
        if (empty($threadDetails)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_ACCESS'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $data = [
            'message_thread_id' => $threadId,
            'message_from' => $userId,
            'message_to' => $messageSendTo,
            'message_text' => trim($post['message_text']),
            'message_date' => date('Y-m-d H:i:s'),
            'message_is_unread' => Thread::MESSAGE_IS_UNREAD
        ];
        $tObj = new Thread();
        if (!$insertId = $tObj->addThreadMessages($data)) {
            FatUtility::dieJsonError(Label::getLabel($tObj->getError(), $this->siteLangId));
        }
        if (isset($_FILES['message_file']['name']) && !empty($_FILES['message_file']['name'])) {
            $fileHandlerObj = new Afile(Afile::TYPE_MESSAGE_ATTACHMENT);
            if (!$fileHandlerObj->saveFile($_FILES['message_file'], $insertId)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($fileHandlerObj->getError());
            }
        }
        $db->commitTransaction();
        $json['threadId'] = $threadId;
        $json['messageId'] = $insertId;
        $json['msg'] = Label::getLabel('MSG_MESSAGE_SUBMITTED_SUCCESSFULLY!');
        FatUtility::dieJsonSuccess($json);
    }

    /**
     * Get Unread Count
     */
    public function getUnreadCount()
    {
        $messCount = (new Thread(0, $this->siteUserId))->getUnreadCount();
        FatUtility::dieJsonSuccess(['messCount' => $messCount]);
    }

    /**
     * Function to delete attachments and its physical file
     *
     */
    public function deleteAttachment(): json
    {
        $userId = $this->siteUserId;
        $msgId = FatApp::getPostedData('msg_id', FatUtility::VAR_INT, 0);
        /* validate msg id */
        $srch = new MessageSearch();
        $srch->joinThreadMessage();
        $srch->addFld('tth.thread_id');
        $srch->addFld('message_date');
        $srch->addCondition('ttm.message_deleted', '=', 0);
        $srch->addCondition('ttm.message_from', '=', $userId);
        $srch->addCondition('message_id', '=', $msgId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $msgData = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($msgData)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Invalid_Request', $this->siteLangId));
        }
        $msgTime = new DateTime($msgData['message_date']);
        $currentTime = new DateTime();
        $difference = $msgTime->diff($currentTime);
        $duration = FatApp::getConfig('CONF_DELETE_ATTACHMENT_ALLOWED_DURATION');
        if ($difference->format('%i') >= $duration) {
            $msg = Label::getLabel('MSG_MESSAGES_OLDER_THAN_{msg-duration}_MINS_CANNOT_BE_REMOVED');
            FatUtility::dieJsonError(str_replace('{msg-duration}', $duration, $msg));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        /* delete message */
        if (!$db->deleteRecords(Thread::DB_TBL_THREAD_MESSAGES, ['smt' => 'message_id = ?', 'vals' => array($msgId)])) {
            FatUtility::dieJsonError($db->getError());
        }
        /* delete attachment */
        $fileHandlerObj = new Afile(Afile::TYPE_MESSAGE_ATTACHMENT);
        if (!$fileHandlerObj->removeFile($msgId, true)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($fileHandlerObj->getError());
        }
        $db->commitTransaction();
        $response = [
            'threadId' => $msgData['thread_id'],
            'msg' => Label::getLabel('MSG_Attachment_Removed_Successfully', $this->siteLangId)
        ];
        FatUtility::dieJsonSuccess($response);
    }

    /**
     * Function to download messages attachments
     *
     * @param int $fileId
     * 
     */
    public function downloadAttachment(int $fileId)
    {
        if ($fileId < 1) {
            FatUtility::dieWithError(Label::getLabel('MSG_Invalid_Request', $this->siteLangId));
        }
        $userId = $this->siteUserId;
        $srch = new MessageSearch();
        $srch->joinThreadMessage();
        $srch->addFld('ttm.message_id');
        $srch->addCondition('ttm.message_deleted', '=', 0);
        $cnd = $srch->addCondition('ttm.message_from', '=', $userId);
        $cnd->attachCondition('ttm.message_to', '=', $userId, 'OR');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (!FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::dieWithError(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
        }
        $fileObj = new Afile(Afile::TYPE_MESSAGE_ATTACHMENT);
        $fileObj->downloadById($fileId);
    }
}
