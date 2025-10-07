<?php

/**
 * This class is used to handle GDPR requests
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class GdprRequest extends MyAppModel
{

    const DB_TBL = 'tbl_gdpr_requests';
    const DB_TBL_PREFIX = 'gdpreq_';
    const TRUNCATE_DATA = 1;
    const ANONYMIZE_DATA = 2;
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_DELETED_DATA = 3;
    const STATUS_DELETED_REQUEST = 4;

    /**
     * Initialize GDPR Request
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'gdpreq_id', $id);
    }

    /**
     * Get Request From User Id
     * 
     * @param int $userId
     * @return null|array
     */
    public static function getRequestFromUserId(int $userId)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('gdpreq_user_id', '=', $userId);
        $srch->addCondition('gdpreq_status', '=', static::STATUS_PENDING);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get Status Array
     * 
     * @return array
     */
    public static function getStatusArr(): array
    {
        return [
            static::STATUS_PENDING => Label::getLabel('LBL_PENDING'),
            static::STATUS_COMPLETED => Label::getLabel('LBL_COMPLETED'),
            static::STATUS_DELETED_DATA => Label::getLabel('LBL_DELETE_DATA'),
            static::STATUS_DELETED_REQUEST => Label::getLabel('LBL_DELETE_REQUEST'),
        ];
    }

    /**
     * Remove User Data
     * 
     * 1. Remove Attachments
     * 2. Remove Basic Info
     * 3. Remove Lang Info
     * 4. Remove Address
     * 5. Remove Bank Info
     * 6. Remove Settings
     * 7. Remove Qualifications
     * 8. Remove Qualifications Files
     * 9. Remove Verification Info
     * 10. Remove Withdraw Requests
     * 11. Remove Email Change Requests
     * 
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $status): bool
    {
        $requestId = $this->getMainTableRecordId();
        $request = static::getAttributesById($requestId);
        if (empty($request)) {
            $this->error = Label::getLabel('LBL_REQUEST_NOT_FOUND');
            return false;
        }
        if ($request['gdpreq_status'] != static::STATUS_PENDING) {
            $this->error = Label::getLabel('LBL_REQUEST_NOT_FOUND');
            return false;
        }
        $userId = FatUtility::int($request['gdpreq_user_id']);

        /* get user details */
        $userData = User::getAttributesById($userId, ['user_first_name', 'user_last_name', 'user_email', 'user_lang_id']);

        $db = FatApp::getDb();
        $db->startTransaction();
        $this->setFldValue('gdpreq_status', $status);
        $this->setFldValue('gdpreq_updated_on', date('Y-m-d H:i:s'));
        if (!$this->save()) {
            $db->rollbackTransaction();
            return false;
        }
        if (static::STATUS_DELETED_DATA !== $status) {
            $this->sendStatusUpdateMailToUser($userData, $status);
            $db->commitTransaction();
            return true;
        }
        if (!$this->removeAttachments($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeBasicInfo($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeLangInfo($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeBankInfo($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeSettings($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeQualifications($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeVerificationInfo($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeWithdrawRequests($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeEmailChangeRequests($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeTeacherRequest($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeBlogCommentInfo($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->removeBlogContribution($userId)) {
            $db->rollbackTransaction();
            return false;
        }
        $this->sendStatusUpdateMailToUser($userData, $status);
        $db->commitTransaction();
        return true;
    }

    /**
     * Remove Attachments
     * 
     * @param int $userId
     * @return bool
     */
    private function removeAttachments(int $userId): bool
    {
        $db = FatApp::getDb();
        $fileTypes = [Afile::TYPE_USER_PROFILE_IMAGE, Afile::TYPE_TEACHER_APPROVAL_IMAGE, Afile::TYPE_TEACHER_APPROVAL_PROOF];
        foreach ($fileTypes as $file) {
            $afile = new Afile($file);
            $file = $afile->getFile($userId);
            if (empty($file)) {
                continue;
            }
            if (!$db->deleteRecords(Afile::DB_TBL, ['smt' => 'file_id = ?', 'vals' => [$file['file_id']]])) {
                $this->error = $db->getError();
                return false;
            }
            if (file_exists(CONF_UPLOADS_PATH . $file['file_path'])) {
                unlink(CONF_UPLOADS_PATH . $file['file_path']);
            }
        }
        return true;
    }

    /**
     * Remove Basic Info
     * 
     * @param int $userId
     * @return bool
     */
    private function removeBasicInfo(int $userId): bool
    {
        $data = [
            'user_first_name' => Label::getLabel('LBL_Deleted'),
            'user_last_name' => Label::getLabel('LBL_User'),
            'user_email' => null,
            'user_username' => null,
            'user_password' => null,
            'user_gender' => null,
            'user_active' => 0,
            'user_country_id' => 0,
            'user_deleted' => date('Y-m-d H:i:s'),
        ];
        if (!FatApp::getDb()->updateFromArray(User::DB_TBL, $data,
                        ['smt' => 'user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Settings
     * 
     * @param int $userId
     * @return bool
     */
    private function removeSettings(int $userId): bool
    {
        $data = [
            'user_trial_enabled' => null,
            'user_book_before' => null,
            'user_phone_code' => null,
            'user_phone_number' => null,
            'user_wallet_balance' => 0,
            'user_video_link' => null,
            'user_google_id' => null,
            'user_facebook_id' => null,
            'user_google_token' => null,
            'user_facebook_token' => null
        ];
        if (!FatApp::getDb()->updateFromArray(User::DB_TBL_SETTING, $data,
                        ['smt' => 'user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Lang Info
     * 
     * @param int $userId
     * @return bool
     */
    private function removeLangInfo(int $userId): bool
    {
        if (!FatApp::getDb()->deleteRecords(User::DB_TBL_LANG,
                        ['smt' => 'userlang_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Bank Info
     * 
     * @param int $userId
     * @return bool
     */
    private function removeBankInfo(int $userId): bool
    {
        if (!FatApp::getDb()->deleteRecords(User::DB_TBL_USR_BANK_INFO,
                        ['smt' => 'ub_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Verification Info
     * 
     * @param int $userId
     * @return bool
     */
    private function removeVerificationInfo(int $userId): bool
    {
        $verification = new Verification();
        if (!$verification->removeToken($userId)) {
            $this->error = $verification->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Withdraw Requests
     * 
     * @param int $userId
     * @return bool
     */
    private function removeWithdrawRequests(int $userId): bool
    {
        $data = [
            'withdrawal_bank' => '',
            'withdrawal_account_holder_name' => '',
            'withdrawal_account_number' => '',
            'withdrawal_ifc_swift_code' => '',
            'withdrawal_bank_address' => '',
            'withdrawal_comments' => '',
            'withdrawal_paypal_email_id' => ''
        ];
        if (!FatApp::getDb()->updateFromArray(User::DB_TBL_USR_WITHDRAWAL_REQ, $data,
                        ['smt' => 'withdrawal_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Qualifications
     * 
     * @param int $userId
     * @return bool
     */
    private function removeQualifications(int $userId): bool
    {
        if (!FatApp::getDb()->deleteRecords(UserQualification::DB_TBL,
                        ['smt' => 'uqualification_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        $qualification = new UserQualification(0, $userId);
        $qualifications = $qualification->getUQualification(false);
        $file = new Afile(Afile::TYPE_USER_QUALIFICATION_FILE);
        foreach ($qualifications as $key => $value) {
            if (!$file->removeFile($value['uqualification_id'])) {
                $this->error = $file->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Remove Email Change Requests
     * 
     * @param int $userId
     * @return bool
     */
    private function removeEmailChangeRequests(int $userId): bool
    {
        if (!FatApp::getDb()->deleteRecords(UserEmailChangeRequest::DB_TBL,
                        ['smt' => 'uecreq_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove Teacher Request
     * 
     * @param int $userId
     * @return bool
     */
    private function removeTeacherRequest(int $userId): bool
    {
        $data = [
            'tereq_first_name' => Label::getLabel('LBL_DELETE'),
            'tereq_last_name' => Label::getLabel('LBL_USER'),
            'tereq_phone_code' => null,
            'tereq_phone_number' => null,
        ];
        if (!FatApp::getDb()->updateFromArray(TeacherRequest::DB_TBL, $data,
                        ['smt' => 'tereq_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove blog comment Info
     * 
     * @param int $userId
     * @return bool
     */
    private function removeBlogCommentInfo(int $userId): bool
    {
        $data = [
            'bpcomment_author_name' => Label::getLabel('LBL_DELETE_USER'),
            'bpcomment_author_email' => null,
        ];
        if (!FatApp::getDb()->updateFromArray(BlogComment::DB_TBL, $data,
                        ['smt' => 'bpcomment_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Remove blog Contribution
     * 
     * @param int $userId
     * @return bool
     */
    private function removeBlogContribution(int $userId): bool
    {
        $data = [
            'bcontributions_author_first_name' => Label::getLabel('LBL_DELETE_USER'),
            'bcontributions_author_last_name' => '',
            'bcontributions_author_email' => null,
            'bcontributions_author_phone' => null,
        ];
        if (!FatApp::getDb()->updateFromArray(BlogContribution::DB_TBL, $data,
                        ['smt' => 'bcontributions_user_id = ?', 'vals' => [$userId]])) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Send GDPR request notification to admin
     * 
     * @param int   $userId
     * @param array $data
     * @return bool
     */
    public function sendGdprRequestMailToAdmin(int $userId, array $data): bool
    {
        /* get user details */
        $userData = User::getAttributesById($userId, ['user_first_name', 'user_last_name', 'user_email']);
        $settingData = UserSetting::getSettings($userId, ['user_registered_as']);
        $userType = User::getUserTypes($settingData['user_registered_as']);

        $vars = [
            '{username}' => $userData['user_first_name'] . ' ' . $userData['user_last_name'],
            '{email}' => $userData['user_email'],
            '{account_type}' => $userType,
            '{deletion_reason}' => $data['gdpreq_reason']
        ];
        $langId = MyUtility::getSystemLanguage()['language_id'];
        $mail = new FatMailer($langId, 'delete_account_request_email');
        $mail->setVariables($vars);
        if (!$mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Send GDPR request status update notification to user
     * 
     * @param array $userData
     * @param int   $status
     * @return bool
     */
    private function sendStatusUpdateMailToUser(array $userData, int $status): bool
    {
        $isDataDeleted = AppConstant::getYesNoArr(AppConstant::YES);
        if (static::STATUS_DELETED_DATA !== $status) {
            $isDataDeleted = AppConstant::getYesNoArr(AppConstant::NO);
        }

        $vars = [
            '{username}' => $userData['user_first_name'] . ' ' . $userData['user_last_name'],
            '{email}' => $userData['user_email'],
            '{request_status}' => self::getStatusArr()[$status],
            '{data_removal_status}' => $isDataDeleted
        ];

        $mail = new FatMailer($userData['user_lang_id'], 'delete_account_request_status_update_email');
        $mail->setVariables($vars);
        if (!$mail->sendMail([$userData['user_email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

}
