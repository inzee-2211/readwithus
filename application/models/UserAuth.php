<?php

/**
 * This class is used to handle User Auth
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class UserAuth extends FatModel
{

    const SESSION_ELEMENT = 'APP_SESSION';
    const COOKIES_ELEMENT = 'APP_COOKIES';
    const DB_TBL_USER_AUTH = 'tbl_user_auth_token';
    const DB_TBL_USER_PRR = 'tbl_user_password_reset_requests';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  User Login
     * 
     * @param string $username
     * @param string $password
     * @param string $userip
     * @return bool
     */
    public function login(string $username, string $password = null, string $userip = null, bool $enypass = true): bool
    {
        if (empty($username) || ($enypass && empty($password))) {
            $this->error = Label::getLabel('ERR_INVALID_CERDENTIALS');
            return false;
        }
        $user = User::getByEmail($username);
        if (empty($user)) {
            $this->logFailedLoginAttempt($username, $userip);
            $this->error = Label::getLabel('ERR_INVALID_CERDENTIALS');
            return false;
        }
        if ($this->isBruteForcing($username, $userip)) {
            $this->sendFailedLoginEmail($user);
            $this->error = Label::getLabel('ERR_YOUR_ATTEMPT_LIMIT_EXCEEDED');
            return false;
        }
        $password = $enypass ? static::encryptPassword($password) : $password;
        if ($user['user_password'] != $password) {
            $this->logFailedLoginAttempt($username, $userip);
            $this->error = Label::getLabel('ERR_INVALID_CERDENTIALS');
            return false;
        }
        if (empty($user['user_active'])) {
            $this->error = Label::getLabel('ERR_YOUR_ACCOUNT_IS_INACTIVE');
            return false;
        }
        if (empty($user['user_verified'])) {
            $this->error = Label::getLabel('ERR_YOUR_VERIFICATION_PENDING_{link}');
            $this->error = str_replace("{link}", '<a href="javascript:void(0)" onclick="resendVerificationLink(' . "'" . $username . "'" . ')">' . Label::getLabel('LBL_CLICK_HERE') . '</a>', $this->error);
            return false;
        }
        if (!$this->setUserSession($username, $userip, $user)) {
            $this->error = Label::getLabel('ERR_SOMETHING_WENT_WRONT_TRY_AGAIN');
            return false;
        }
        return true;
    }

    /**
     * Brute Force Check
     * 
     * @param string $username
     * @param string $userip
     * @return bool
     */
    public function isBruteForcing(string $username, string $userip): bool
    {
        $srch = new SearchBase('tbl_failed_login_attempts');
        $cond = $srch->addCondition('attempt_ip', '=', $userip);
        $cond->attachCondition('attempt_username', '=', $username);
        $srch->addCondition('attempt_time', '>=', date('Y-m-d H:i:s', strtotime("-5 minutes")));
        $srch->addFld('COUNT(*) AS total');
        $srch->doNotCalculateRecords();
        $attempts = FatApp::getDb()->fetch($srch->getResultSet());
        return ($attempts['total'] > 3);
    }

    /**
     * Send Failed Login Email
     * 
     * @param array $user
     * @return bool
     */
    private function sendFailedLoginEmail(array $user): bool
    {
        $mail = new FatMailer($user['user_lang_id'], 'failed_login_attempt');
        $mail->setVariables(['{user_full_name}' => $user['user_first_name'] . ' ' . $user['user_last_name']]);
        if (!$mail->sendMail([$user['user_email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Log Failed Login Attempt
     * 
     * @param string $username
     * @param string $userip
     */
    public function logFailedLoginAttempt(string $username, string $userip)
    {
        FatApp::getDb()->deleteRecords('tbl_failed_login_attempts', ['smt' => 'attempt_time < ?', 'vals' => [date('Y-m-d H:i:s', strtotime("-7 Day"))]]);
        FatApp::getDb()->insertFromArray('tbl_failed_login_attempts', ['attempt_username' => $username, 'attempt_ip' => $userip, 'attempt_time' => date('Y-m-d H:i:s')]);
    }

    /**
     * Set User Session
     * 
     * @param string $username
     * @param string $userip
     * @param array $user
     * @return bool
     */
    public function setUserSession(string $username, string $userip, array $user): bool
    {
        $cookieSettings = CookieConsent::getSettings($user['user_id']);
        if (empty($cookieSettings) && !empty($_COOKIE[CookieConsent::COOKIE_NAME])) {
            $cookieConsent = new CookieConsent($user['user_id']);
            $cookieConsent->updateSetting(json_decode($_COOKIE[CookieConsent::COOKIE_NAME], true));
        }
        if (!empty($cookieSettings)) {
            MyUtility::setCookieConsents(json_decode($cookieSettings, true), true);
        }
        if (!empty($user['user_timezone'])) {
            MyUtility::setSiteTimezone($user['user_timezone'], true);
        }
        $stmt = ['smt' => 'attempt_username = ? and attempt_ip = ?', 'vals' => [$username, $userip]];
        if (!FatApp::getDb()->deleteRecords('tbl_failed_login_attempts', $stmt)) {
            return false;
        }
        $userType = ($user['user_is_teacher'] == AppConstant::YES) ? User::TEACHER : User::LEARNER;
        MyUtility::setUserType($userType);
        $_SESSION[UserAuth::SESSION_ELEMENT] = [
            'user_ip' => $userip,
            'user_id' => $user['user_id'],
            'user_email' => $user['user_email'],
            'user_username' => $user['user_username'],
            'user_first_name' => $user['user_first_name'],
            'user_last_name' => $user['user_last_name'],
            'user_is_parent' => $user['user_is_parent'] ?? 0
        ];
        if (!empty($user['user_is_parent']) || FatApp::getPostedData('login_as_parent', FatUtility::VAR_INT, 0)) {
            $_SESSION['RWU_DASHBOARD_ROLE'] = 'parent';
        }
        return true;
    }

    /**
     * Is User Logged
     * 
     * @return bool
     */
    public static function isUserLogged(): bool
    {
        if (!empty($_SESSION[static::SESSION_ELEMENT] ?? '')) {
            return true;
        }
        if (empty($_COOKIE[static::COOKIES_ELEMENT] ?? '')) {
            static::clearAuthTokenUser();
            return false;
        }
        if (!$userId = static::getAuthTokenUserId()) {
            static::clearAuthTokenUser();
            return false;
        }
        $user = User::getAttributesById($userId);
        if (empty($user['user_active'] ?? '') || empty($user['user_verified'] ?? '') || !empty($user['user_deleted'])) {
            static::clearAuthTokenUser();
            return false;
        }
        $userAuth = new UserAuth();
        if (!$userAuth->setUserSession($user['user_email'], MyUtility::getUserIp(), $user)) {
            static::clearAuthTokenUser();
            return false;
        }
        return true;
    }

    /**
     * Get Logged User Id
     * 
     * @return int
     */
    public static function getLoggedUserId()
    {
        return FatUtility::int($_SESSION[static::SESSION_ELEMENT]['user_id'] ?? 0);
    }

    /**
     * Get Auth Token User
     * 
     * @return int|bool
     */
    private static function getAuthTokenUserId()
    {
        $token = $_COOKIE[static::COOKIES_ELEMENT] ?? '';
        $srch = new SearchBase(static::DB_TBL_USER_AUTH);
        $srch->addCondition('usrtok_token', '=', $token);
        $srch->addCondition('usrtok_expiry', '>=', date('Y-m-d H:i:s'));
        if (FatApp::getConfig('CONF_USER_REMEMBER_ME_IP_ENABLE', FatUtility::VAR_INT)) {
            $srch->addCondition('usrtok_last_ip', '=', MyUtility::getUserIp());
        }
        $srch->addCondition('usrtok_browser', '=', MyUtility::getUserAgent());
        $srch->addFld('usrtok_user_id as user_id');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetch($srch->getResultSet())['user_id'] ?? false;
    }

    /**
     * Set Auth Token User
     * 
     * @param int $userId
     * @return boolean
     */
    public static function setAuthTokenUser(int $userId)
    {
        $authToken = substr(md5(microtime()), 0, 32);
        $rememberMeDays = FatApp::getConfig('CONF_USER_REMEMBER_ME_DAYS', FatUtility::VAR_INT);
        $values = [
            'usrtok_user_id' => $userId,
            'usrtok_token' => $authToken,
            'usrtok_expiry' => date('Y-m-d H:i:s', strtotime('+' . $rememberMeDays . ' days')),
            'usrtok_last_ip' => MyUtility::getUserIp(),
            'usrtok_last_access' => date('Y-m-d H:i:s'),
            'usrtok_browser' => MyUtility::getUserAgent()
        ];
        if (!FatApp::getDb()->insertFromArray(static::DB_TBL_USER_AUTH, $values)) {
            return false;
        }
        // MyUtility::setCookie(static::COOKIES_ELEMENT, $authToken, $rememberMeDays * 86400, CONF_WEBROOT_FRONTEND);
        MyUtility::setCookie(static::COOKIES_ELEMENT, $authToken, $rememberMeDays * 86400, '/');
        return true;
    }

    private static function clearAuthTokenUser()
    {
        FatApp::getDb()->deleteRecords(static::DB_TBL_USER_AUTH, [
            'smt' => 'usrtok_token = ?',
            'vals' => [$_COOKIE[static::COOKIES_ELEMENT] ?? '']
        ]);
        // MyUtility::setCookie(static::COOKIES_ELEMENT, '', time() - 3600, CONF_WEBROOT_FRONTEND);
        MyUtility::setCookie(static::COOKIES_ELEMENT, '', time() - 3600, '/');
    }

    public function signup(array $data): bool
    {
        if (
            empty($data['user_email']) || empty($data['user_password']) ||
            empty($data['user_first_name']) || !isset($data['user_last_name']) ||
            empty($data['user_phone_code']) || empty($data['user_phone_number'])
        ) {
            $this->error = Label::getLabel('MSG_USER_COULD_NOT_BE_SET');
            return false;
        }
        $userData = [
            'user_dashboard' => User::LEARNER,
            'user_registered_as' => User::LEARNER,
            'user_email' => $data['user_email'],
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_lang_id' => MyUtility::getSiteLangId(),
            'user_timezone' => MyUtility::getSiteTimezone(),
            'user_phone_code' => $data['user_phone_code'],
            'user_country_id' => '1',
            'user_phone_number' => $data['user_phone_number'],
            'user_wallet_balance' => '0',
            'user_wallet_currency' => 'USD',
        ];
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = Label::getLabel('ERR_SOMETHING_WENT_WRONG');
            return false;
        }
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
            $this->error = Label::getLabel('MSG_USER_COULD_NOT_BE_SET');
            return false;
        }
        if (!$user->setSettings($userData)) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel('MSG_USER_COULD_NOT_BE_SET');
            return false;
        }
        if (!$user->setPassword($data['user_password'])) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel('MSG_USER_COULD_NOT_BE_SET');
            return false;
        }
        if (!$user->assignGiftCard($data['user_email'])) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel('MSG_USER_COULD_NOT_BE_SET');
            return false;
        }

        /* [PARENT-PORTAL] Handle Parent Registration & Default Student */
        if (!empty($data['user_is_parent'])) {
            $userId = $user->getMainTableRecordId();
            $userObj = new User($userId);
            $userObj->setFldValue('user_is_parent', AppConstant::YES);
            if (!$userObj->save()) {
                $db->rollbackTransaction();
                $this->error = $userObj->getError();
                return false;
            }
        }
        /* [/PARENT-PORTAL] */

        if (!$db->commitTransaction()) {
            $this->error = Label::getLabel('ERR_SOMETHING_WENT_WRONG');
            return false;
        }
        return true;
    }

    /**
     * Logout
     */
    public static function logout()
    {
        UserAuth::clearAuthTokenUser();
        unset($_SESSION[UserAuth::SESSION_ELEMENT]);
        unset($_SESSION[UserAuth::COOKIES_ELEMENT]);
        unset($_SESSION['SITE_USER_TYPE']);
    }

    /**
     * Setup Reset Password Request
     * 
     * @param string $email
     * @param string $captch
     * @return bool
     */
    public function setupResetPasswordRequest(string $email, string $captch = ''): bool
    {
        if (empty($email)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST');
            return false;
        }
        if (!CommonHelper::verifyCaptcha($captch)) {
            $this->error = Label::getLabel('MSG_INVALID_CAPTCHA');
            return false;
        }
        $user = User::getByEmail($email);
        if (empty($user)) {
            $this->error = Label::getLabel('LBL_PLEASE_ENTER_REGISTERED_EMAIL');
            return false;
        }
        if (empty($user['user_verified'])) {
            $link = '<a href="javascript:void(0)" onclick="resendSignupVerifyEmail(' . "'" .
                $user['user_email'] . "'" . ')">' . Label::getLabel('LBL_CLICK_HERE') . '</a>';
            $this->error = str_replace("{clickhere}", $link, Label::getLabel('MSG_VERIFICATION_PENDING_{clickhere}_TO_VERIFY'));
            return false;
        }
        if ($this->checkPasswordResetRequest($user['user_id']) > 0) {
            $this->error = Label::getLabel('ERR_RESET_PASSWORD_REQUEST_ALREADY_PLACED');
            return false;
        }
        $token = uniqid('Y', true);
        $db = FatApp::getDb();
        $db->startTransaction();
        $record = new TableRecord(static::DB_TBL_USER_PRR);
        $record->assignValues([
            'uprr_token' => $token,
            'uprr_user_id' => $user['user_id'],
            'uprr_expiry' => date('Y-m-d H:i:s', strtotime("+1 DAY"))
        ]);
        if (!$record->addNew(['HIGH_PRIORITY'])) {
            $this->error = $record->getError();
            $db->rollbackTransaction();
            return false;
        }
        $stmt = ['smt' => 'usrtok_user_id = ?', 'vals' => [$user['user_id']]];
        if (!FatApp::getDb()->deleteRecords(static::DB_TBL_USER_AUTH, $stmt)) {
            $this->error = FatApp::getDb()->getError();
            $db->rollbackTransaction();
            return false;
        }
        $mail = new FatMailer(MyUtility::getSiteLangId(), 'forgot_password');
        $mail->setVariables([
            '{user_full_name}' => $user['user_first_name'] . ' ' . $user['user_last_name'],
            '{reset_url}' => MyUtility::makeFullUrl('GuestUser', 'resetPassword', [$user['user_id'], $token])
        ]);
        if (!$mail->sendMail([$user['user_email']])) {
            $this->error = Label::getLabel('MSG_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN');
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Check Password Reset Request
     * 
     * @param int $userId
     * @return int
     */
    private function checkPasswordResetRequest(int $userId): int
    {
        $srch = new SearchBase(static::DB_TBL_USER_PRR);
        $srch->addCondition('uprr_user_id', '=', $userId);
        $srch->addCondition('uprr_expiry', '>', date('Y-m-d H:i:s'));
        $srch->addFld('uprr_user_id');
        $srch->getResultSet();
        return $srch->recordCount();
    }

    /**
     * Validate Reset Password Link
     * 
     * @param int $userId
     * @param string $token
     * @return bool
     */
    public function validateResetPasswordLink(int $userId, string $token): bool
    {
        if (empty($userId) || empty($token)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST');
            return false;
        }
        $srch = new SearchBase(static::DB_TBL_USER_PRR);
        $srch->addCondition('uprr_token', '=', $token);
        $srch->addCondition('uprr_user_id', '=', $userId);
        $srch->addCondition('uprr_expiry', '>', date('Y-m-d H:i:s'));
        $srch->getResultSet();
        if ($srch->recordCount() < 1) {
            $this->error = Label::getLabel('ERR_INVALID_LINK');
            return false;
        }
        return true;
    }

    /**
     * Setup Reset Password
     * 
     * @param int $userId
     * @param string $password
     * @return bool
     */
    public function setupResetPassword(int $userId, string $password): bool
    {
        if (empty($userId) || empty($password)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST');
            return false;
        }
        $userObj = new User($userId);
        $hashPassword = static::encryptPassword($password);
        $userObj->setFldValue('user_password', $hashPassword);
        if (!$userObj->save()) {
            $this->error = $userObj->getError();
            return false;
        }
        $stmt = ['smt' => 'uprr_user_id = ?', 'vals' => [$userId]];
        if (!FatApp::getDb()->deleteRecords(static::DB_TBL_USER_PRR, $stmt)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        $user = User::getAttributesById($userId, ['user_first_name', 'user_last_name', 'user_email', 'user_lang_id']);
        $mail = new FatMailer($user['user_lang_id'], 'password_changed_successfully');
        $mail->setVariables([
            '{user_full_name}' => $user['user_first_name'] . ' ' . $user['user_last_name'],
            '{login_link}' => MyUtility::makeFullUrl('GuestUser', 'loginForm')
        ]);
        $mail->sendMail([$user['user_email']]);
        return true;
    }

    /**
     * Update Google Login
     * 
     * @param string $id
     * @param string $email
     * @param string $name
     * @return boolean
     */
    public function updateGoogleLogin(string $id, string $email, string $name)
    {
        if (empty($id) || empty($email) || empty($name)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST');
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $user = User::getByEmail($email);
        if (!empty($user)) {
            if ($user['user_active'] != AppConstant::ACTIVE) {
                $this->error = Label::getLabel('ERR_YOUR_ACCOUNT_IS_INACTIVE');
                return false;
            }
            if (!empty($user['user_deleted'])) {
                $this->error = Label::getLabel('ERR_YOUR_ACCOUNT_ID_DELETED');
                return false;
            }
            $userObj = new User($user['user_id']);
            if (empty($user['user_verified'])) {
                $userObj->setFldValue('user_verified', date('Y-m-d H:i:s'));
                if (!$userObj->save()) {
                    $db->rollbackTransaction();
                    $this->error = Label::getLabel('ERR_ACCOUNT_VERIFICATION_IS_PENDING');
                    return false;
                }
            }
            if (!$userObj->setSettings(['user_google_id' => $id])) {
                $db->rollbackTransaction();
                $this->error = Label::getLabel('LBL_ERROR_TO_UPDATE_USER_DATA');
                return false;
            }
            $db->commitTransaction();
            return $user;
        }
        $names = explode(' ', $name);
        $userData = [
            'user_email' => $email,
            'user_username' => $id,
            'user_google_id' => $id,
            'user_first_name' => $names[0],
            'user_last_name' => $names[1] ?? '',
            'user_dashboard' => User::LEARNER,
            'user_registered_as' => User::LEARNER,
            'user_lang_id' => MyUtility::getSiteLangId(),
            'user_timezone' => MyUtility::getSiteTimezone()
        ];
        $userObj = new User();
        $userObj->assignValues($userData);
        $userObj->setFldValue('user_active', AppConstant::YES);
        $userObj->setFldValue('user_verified', date('Y-m-d H:i:s'));
        $userObj->setFldValue('user_created', date('Y-m-d H:i:s'));
        if (!$userObj->save()) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel("MSG_USER_COULD_NOT_BE_SET");
            return false;
        }
        if (!$userObj->setSettings($userData)) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel("MSG_USER_SETTING_COULD_NOT_BE_SET");
            return false;
        }
        $db->commitTransaction();
        if (!empty(FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION'))) {
            $this->sendSignupWelcomeEmail($userData);
        }
        if (!empty(FatApp::getConfig('CONF_NOTIFY_ADMIN_REGISTRATION'))) {
            $this->sendSignupAdminNotifion($userData);
        }
        $userData['user_password'] = NULL;
        return $userData;
    }

    /**
     * Facebook Login
     * 
     * @param string $id
     * @param string $name
     * @param string $email
     * @return bool|array
     */
    public function facebookLogin(string $id, string $name, string $email = '')
    {
        if (empty($id)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST');
            return false;
        }
        $db = FatApp::getDb();
        $srch = new SearchBase(User::DB_TBL, 'user');
        $srch->joinTable(User::DB_TBL_SETTING, 'INNER JOIN', 'uset.user_id = user.user_id', 'uset');
        $srch->addMultipleFields([
            'user.user_id',
            'user_email',
            'user_username',
            'user_password',
            'user_is_teacher',
            'user_first_name',
            'user_last_name',
            'user_dashboard',
            'user_facebook_id',
            'user_verified',
            'user_active',
            'user_deleted'
        ]);
        if (!empty($email)) {
            $srch->addCondition('user_email', '=', $email);
        } else {
            $srch->addCondition('user_facebook_id', '=', $id);
        }
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $user = $db->fetch($srch->getResultSet());
        $db->startTransaction();
        if (!empty($user)) {
            if (!empty($user['user_deleted'])) {
                $this->error = Label::getLabel('ERR_YOUR_ACCOUNT_ID_DELETED');
                return false;
            }
            if ($user['user_active'] != AppConstant::ACTIVE) {
                $this->error = Label::getLabel('ERR_YOUR_ACCOUNT_IS_INACTIVE');
                return false;
            }
            $userObj = new User($user['user_id']);
            if (empty($user['user_verified']) && !empty($email)) {
                $userObj->setFldValue('user_verified', date('Y-m-d H:i:s'));
                if (!$userObj->save()) {
                    $db->rollbackTransaction();
                    $this->error = Label::getLabel('ERR_ACCOUNT_VERIFICATION_IS_PENDING');
                    return false;
                }
            }
            if (!$userObj->setSettings(['user_facebook_id' => $id])) {
                $db->rollbackTransaction();
                $this->error = Label::getLabel('LBL_ERROR_TO_UPDATE_USER_DATA');
                return false;
            }
            $db->commitTransaction();
            return $user;
        }
        $names = explode(" ", $name);
        $userData = [
            'user_email' => $email,
            'user_username' => $id,
            'user_facebook_id' => $id,
            'user_first_name' => $names[0],
            'user_last_name' => $names[1] ?? '',
            'user_dashboard' => User::LEARNER,
            'user_registered_as' => User::LEARNER,
            'user_lang_id' => MyUtility::getSiteLangId(),
            'user_timezone' => MyUtility::getSiteTimezone(),
        ];
        $userObj = new User();
        $userObj->assignValues($userData);
        $userObj->setFldValue('user_active', AppConstant::YES);
        $userObj->setFldValue('user_created', date('Y-m-d H:i:s'));
        $userObj->setFldValue('user_verified', date('Y-m-d H:i:s'));
        if (!$userObj->save()) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel("MSG_USER_COULD_NOT_BE_SET");
            return false;
        }
        if (!$userObj->setSettings($userData)) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel("MSG_USER_COULD_NOT_BE_SET");
            return false;
        }
        $db->commitTransaction();
        if (!empty(FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION'))) {
            $this->sendSignupWelcomeEmail($userData);
        }
        if (!empty(FatApp::getConfig('CONF_NOTIFY_ADMIN_REGISTRATION'))) {
            $this->sendSignupAdminNotifion($userData);
        }
        $userData['user_id'] = $userObj->getMainTableRecordId();
        $userData['user_password'] = NULL;
        return $userData;
    }

    /**
     * Send Signup Emails
     * 
     * @param array $user
     * @return array
     */
    public function sendSignupEmails(array $user): array
    {
        $response = [];
        if (!empty(FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION'))) {
            $this->sendSignupWelcomeEmail($user);
        }
        if (!empty(FatApp::getConfig('CONF_NOTIFY_ADMIN_REGISTRATION'))) {
            $this->sendSignupAdminNotifion($user);
        }
        if (!empty(FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION'))) {
            $this->sendVerifyEmail($user);
            $response = [
                'url' => MyUtility::makeUrl(),
                'msg' => Label::getLabel('MSG_VERIFICATION_EMAIL_SENT')
            ];
        }
        return $response;
    }

    /**
     * Send Signup Welcome Email
     * 
     * @param array $user
     * @return bool
     */
    public function sendSignupWelcomeEmail(array $user): bool
    {
        $mail = new FatMailer($user['user_lang_id'], 'welcome_registration');
        $mail->setVariables([
            '{user_full_name}' => $user['user_first_name'] . ' ' . ($user['user_last_name'] ?? ''),
            '{contact_us_email}' => FatApp::getConfig('CONF_CONTACT_EMAIL'),
        ]);
        if (!$mail->sendMail([$user['user_email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Send Signup Admin Notification
     * 
     * @param array $user
     * @return bool
     */
    public function sendSignupAdminNotifion(array $user): bool
    {
        $mail = new FatMailer($user['user_lang_id'], 'new_registration_admin');
        $mail->setVariables([
            '{user_full_name}' => $user['user_first_name'] . ' ' . ($user['user_last_name'] ?? ''),
            '{user_email}' => $user['user_email'],
        ]);
        if (!$mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Send Verify Email
     * 
     * @param array $user
     * @return bool
     */
    public function sendVerifyEmail(array $user): bool
    {
        if (empty($user)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST');
            return false;
        }
        if (!empty($user['user_verified'])) {
            $this->error = Label::getLabel('LBL_ACCOUNT_ALREADY_VERIFIED');
            return false;
        }
        $token = $user['user_id'] . '-' . FatUtility::getRandomString(15);
        $verification = new Verification($user['user_id']);
        if (!$verification->addToken($token, $user['user_id'], $user['user_email'], Verification::TYPE_EMAIL_VERIFICATION)) {
            $this->error = $verification->getError();
            return false;
        }
        $mail = new FatMailer($user['user_lang_id'], 'user_email_verification');
        $mail->setVariables([
            '{user_full_name}' => $user['user_first_name'] . ' ' . ($user['user_last_name'] ?? ''),
            '{verification_url}' => MyUtility::makeFullUrl('GuestUser', 'verifyEmail', ['verify' => $token])
        ]);
        if (!$mail->sendMail([$user['user_email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Encrypt Password
     * 
     * @param string $pass
     * @return string
     */
    public static function encryptPassword(string $pass): string
    {
        return md5(PASSWORD_SALT . $pass . PASSWORD_SALT);
    }

    /**
     * Get Signin Form
     * 
     * @return Form
     */
    public static function getSigninForm(): Form
    {
        $frm = new Form('signinFrm');
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email'), 'username', (MyUtility::isDemoUrl() ? 'john.hettinger@dummyid.com' : ''), ['placeholder' => Label::getLabel('LBL_EMAIL_ADDRESS')]);
        $fld->requirements()->setRequired();
        $pwd = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'password', (MyUtility::isDemoUrl() ? 'John@123' : ''), ['placeholder' => Label::getLabel('LBL_PASSWORD')]);
        $pwd->requirements()->setRequired();
        $frm->addCheckbox(Label::getLabel('LBL_Remember_Me'), 'remember_me', 1, [], false, 0);
        $frm->addCheckbox(Label::getLabel('LBL_LOGIN_AS_PARENT'), 'login_as_parent', 1, [], false, 0);
        $frm->addHtml('', 'forgot', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_LOGIN'));
        return $frm;
    }

}
