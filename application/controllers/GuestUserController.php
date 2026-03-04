<?php

use Google\Service\Oauth2;

/**
 * Guest User Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */


// if (!function_exists('write_log')) {
//     /**
//      * Simple file logger.
//      *
//      * @param string $message  Short message (what happened)
//      * @param array  $context  Extra data (will be JSON encoded)
//      * @param string $channel  Optional “channel” name (redirect, auth, stripe, etc.)
//      */
//     function write_log(string $message, array $context = [], string $channel = 'app'): void
//     {
//         // Base logs directory: /application/logs
//         $baseDir = CONF_INSTALLATION_PATH . 'application' . DIRECTORY_SEPARATOR . 'logs';

//         // Ensure directory exists
//         if (!is_dir($baseDir)) {
//             @mkdir($baseDir, 0775, true);
//         }

//         // One file per day & channel, e.g. redirect-2025-12-12.log
//         $filePath = sprintf(
//             '%s%s%s-%s.log',
//             $baseDir,
//             DIRECTORY_SEPARATOR,
//             $channel,
//             date('Y-m-d')
//         );

//         // Build log line
//         $line = sprintf(
//             "[%s] %s %s\n",
//             date('Y-m-d H:i:s'),
//             $message,
//             $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''
//         );

//         // Append line (ignore errors; this is debugging helper)
//         @file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);
//     }
// }

class GuestUserController extends MyAppController
{

    //  private function getDashboardUrl(): string
//     {
//         // /dashboard/my-subscriptions
//         return MyUtility::makeUrl('MySubscriptions', '', [], CONF_WEBROOT_DASHBOARD);
//     }
    private function getDashboardUrl(): string
    {
        // If user is logged in and is a parent -> go to parent portal
        if (!empty($this->siteUserId)) {
            $isParent = !empty($this->siteUser['user_is_parent']);

            // Fallback if siteUser doesn't include it for any reason
            if (!$isParent) {
                $row = User::getAttributesById($this->siteUserId, ['user_is_parent']);
                $isParent = !empty($row['user_is_parent']);
            }

            if ($isParent) {
                return MyUtility::makeUrl('Parent', '', [], CONF_WEBROOT_DASHBOARD);
            }
        }

        // Default existing behaviour
        return MyUtility::makeUrl('MySubscriptions', '', [], CONF_WEBROOT_DASHBOARD);
    }

    /**
     * Initialize Guest User
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        // write_log('GuestUser::__construct', [
        //     'action'    => $action,
        //     'siteUserId'=> $this->siteUserId,
        //     'uri'       => $_SERVER['REQUEST_URI'] ?? '',
        // ], 'auth');

        $actions = ['verifyEmail', 'configureEmail', 'updateEmail'];
        if (!in_array($action, $actions) && $this->siteUserId > 0) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_USER_ALREADY_LOGGED_IN'));
            }
            // FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
            FatApp::redirectUser($this->getDashboardUrl());
        }
    }

    /**
     * Render Login|Signin Form
     * 
     * @return type
     */
    public function loginForm()
    {

        $this->set('frm', UserAuth::getSigninForm());
        if (FatApp::getPostedData('isPopUp', FatUtility::VAR_INT, 0)) {
            $this->_template->render(false, false, 'guest-user/login-form-popup.php');
            return;
        }
        $this->_template->render();
    }


    /**
     * Login|Signin Setup
     */
    public function signinSetup()
    {
        //print_r($_SESSION);
        $frm = UserAuth::getSigninForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $auth = new UserAuth();
        ///echo 'test';die;
        if (!$auth->login($post['username'], $post['password'], MyUtility::getUserIp())) {

            FatUtility::dieJsonError($auth->getError());
        }
        if (FatUtility::int($post['remember_me']) == AppConstant::YES) {
            UserAuth::setAuthTokenUser(UserAuth::getLoggedUserId());
        }

        //   print_r($_SESSION);
        $_SESSION[AppConstant::SEARCH_SESSION] = FatApp::getPostedData();
        //   print_r($_SESSION);
        FatUtility::dieJsonSuccess(Label::getLabel("MSG_LOGIN_SUCCESSFULL"));
        //echo 'test3311';die;
    }

    /**
     * Register|Signup Form
     */
    public function registerForm()
    {
        $policyPageId = FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0);
        $termPageId = FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0);
        $privacyPolicyLink = $termsConditionsLink = '';
        if ($policyPageId > 0) {
            $privacyPolicyLink = MyUtility::makeUrl('Cms', 'view', [$policyPageId]);
        }
        if ($termPageId > 0) {
            $termsConditionsLink = MyUtility::makeUrl('Cms', 'view', [$policyPageId]);
        }
        $this->sets([
            'frm' => $this->getSignupForm(),
            'privacyPolicyLink' => $privacyPolicyLink,
            'termsConditionsLink' => $termsConditionsLink,
        ]);
        $this->_template->render(true, true, 'guest-user/registration-form.php');
    }

    /**
     * Render Register|Signup Form
     */
    public function signupForm()
    {
        $termPageId = FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0);
        $policyPageId = FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0);
        $privacyPolicyLink = $termsConditionsLink = '';
        if ($policyPageId > 0) {
            $privacyPolicyLink = MyUtility::makeUrl('Cms', 'view', [$policyPageId]);
        }
        if ($termPageId > 0) {
            $termsConditionsLink = MyUtility::makeUrl('Cms', 'view', [$termPageId]);
        }
        $this->sets([
            'frm' => $this->getSignupForm(),
            'privacyPolicyLink' => $privacyPolicyLink,
            'termsConditionsLink' => $termsConditionsLink,
        ]);
        $this->set('frm', $this->getSignupForm());
        $this->_template->render(false, false);
    }

    /**
     * Register|Signup Setup
     */
    public function signupSetup()
    {
        $frm = $this->getSignupForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!MyUtility::validatePassword($post['user_password'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_ALPHANUMERIC'));
        }
        $auth = new UserAuth();
        if (!$auth->signup($post)) {
            FatUtility::dieJsonError($auth->getError());
        }
        $user = User::getByEmail($post['user_email']);
        $response = $auth->sendSignupEmails($user);
        if (!empty($response)) {
            FatUtility::dieJsonSuccess(['msg' => $response['msg'], 'redirectUrl' => $response['url']]);
        }
        // $redirectUrl = MyUtility::makeUrl();
        $redirectUrl = $this->getDashboardUrl();
        if (
            FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION') == AppConstant::NO &&
            FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION') == AppConstant::NO &&
            FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION') == AppConstant::YES
        ) {
            $auth = new UserAuth();
            if (!$auth->login($post['user_email'], $post['user_password'], MyUtility::getUserIp())) {
                FatUtility::dieJsonError($auth->getError());
            }
            // $redirectUrl = MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD);
            $redirectUrl = $this->getDashboardUrl();
        }
        FatUtility::dieJsonSuccess([
            'redirectUrl' => $redirectUrl,
            'msg' => Label::getLabel('LBL_REGISTERATION_SUCCESSFULL')
        ]);
    }

    /**
     * Verify User Email Id
     * 
     * @param string $code
     */
    public function verifyEmail(string $code)
    {
        $verification = new Verification();
        if (!$verification->verify($code)) {
            Message::addErrorMessage($verification->getError());
            FatUtility::exitWithErrorCode(404);
        }
        $verification->removeExpiredToken();
        Message::addMessage(Label::getLabel("MSG_EMAIL_VERIFIED_SUCCESFULLY"));
        FatApp::redirectUser(MyUtility::makeUrl('Home'));
    }

    /**
     * Render Forgot Password Form
     */
    public function forgotPassword()
    {
        $this->sets([
            'siteKey' => FatApp::getConfig('CONF_RECAPTCHA_SITEKEY'),
            'secretKey' => FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY'),
            'frm' => $this->getForgotPasswordForm()
        ]);
        $this->_template->render();
    }

    /**
     * Setup Forgot Password Request
     */
    public function forgotPasswordSetup()
    {
        $frm = $this->getForgotPasswordForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $userAuth = new UserAuth();
        $captcha = FatApp::getPostedData('g-recaptcha-response', FatUtility::VAR_STRING, '');
        if (!$userAuth->setupResetPasswordRequest($post['user_email'], $captcha)) {
            FatUtility::dieJsonError($userAuth->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel("MSG_SENT_RESET_PASSWORD_INSTRUCTIONS_ON_YOUR_EMAIL"));
    }

    /**
     * Render Reset Password Form
     * 
     * @param int $userId
     * @param string $token
     */
    public function resetPassword($userId, $token)
    {
        $userId = FatUtility::int($userId);
        $userAuth = new UserAuth();
        if (!$userAuth->validateResetPasswordLink($userId, $token)) {
            Message::addErrorMessage($userAuth->getError());
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
        }
        $this->set('frm', $this->getResetPasswordForm($userId, $token));
        $this->_template->render();
    }

    /**
     * Setup Reset Password
     */
    public function resetPasswordSetup()
    {
        $userId = FatApp::getPostedData('user_id', FatUtility::VAR_INT, 0);
        $token = FatApp::getPostedData('token', FatUtility::VAR_STRING, '');
        $frm = $this->getResetPasswordForm($userId, $token);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!MyUtility::validatePassword($post['new_password'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_ALPHANUMERIC'));
        }
        $userAuth = new UserAuth();
        if (!$userAuth->validateResetPasswordLink($userId, $token)) {
            FatUtility::dieJsonError($userAuth->getError());
        }
        if (!$userAuth->setupResetPassword($userId, $post['new_password'])) {
            FatUtility::dieJsonError($userAuth->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_PASSWORD_CHANGED_SUCCESSFULLY'));
    }

    /**
     * Resend Signup Verify Email
     * 
     * @param string $email
     */
    public function resendSignupVerifyEmail(string $email)
    {
        $user = User::getByEmail($email);
        if (empty($user)) {
            FatUtility::dieWithError(Label::getLabel('ERR_INVALID_REQUEST'));
        }
        $auth = new UserAuth();
        if (!$auth->sendVerifyEmail($user)) {
            FatUtility::dieJsonError($auth->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_VERIFICATION_EMAIL_HAS_BEEN_SENT'));
    }

    /**
     * Get Signup Form
     * 
     * @return Form
     */
    private function getSignupForm(): Form
    {
        $frm = new Form('signupFrm');
        $frm->addHiddenField('', 'user_id', 0, ['id' => 'user_id']);
        $frm->addRequiredField(Label::getLabel('LBL_FIRST_NAME'), 'user_first_name');
        $frm->addTextBox(Label::getLabel('LBL_LAST_NAME'), 'user_last_name');
        $fld = $frm->addEmailField(Label::getLabel('LBL_EMAIL_ID'), 'user_email', '', ['autocomplete="off"']);
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
        $countries = Country::getAll($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_PHONE_CODE'), 'user_phone_code', array_column($countries, 'phone_code', 'country_id'), '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired(true);
        $fld = $frm->addTextBox(Label::getLabel('LBL_PHONE'), 'user_phone_number');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PHONE_NO_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('LBL_PHONE_NO_VALIDATION_MSG'));
        $fld = $frm->addPasswordField(Label::getLabel('LBL_PASSWORD'), 'user_password');
        $fld->requirements()->setRequired();
        $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel(AppConstant::PASSWORD_CUSTOM_ERROR_MSG));
        $fld = $frm->addCheckBox(Label::getLabel('LBL_I_ACCEPT_TO_THE'), 'agree', AppConstant::NO);
        $fld->requirements()->setRequired();
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_TERMS_AND_CONDITION_ARE_MANDATORY'));
        $frm->addCheckbox(Label::getLabel('LBL_REGISTER_AS_PARENT'), 'user_is_parent', 1, [], false, 0);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Register'));
        return $frm;
    }

    /**
     * Get Forgot Password Form
     * 
     * @return Form
     */
    private function getForgotPasswordForm(): Form
    {
        $frm = new Form('forgotPasswordFrm');
        $frm->addEmailField(Label::getLabel('LBL_PLEASE_ENTER_REGISTERED_EMAIL'), 'user_email')->requirements()->setRequired();
        $frm->addHtml('', 'htmlNote', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT'));
        return $frm;
    }

    /**
     * Get Reset Password Form
     * 
     * @param int $userId
     * @param string $token
     * @return Form
     */
    private function getResetPasswordForm($userId, $token): Form
    {
        $frm = new Form('frmResetPwd');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_NEW_PASSWORD'), 'new_password');
        $fld->requirements()->setRequired();
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel(AppConstant::PASSWORD_CUSTOM_ERROR_MSG));
        $fld_cp = $frm->addPasswordField(Label::getLabel('LBL_CONFIRM_NEW_PASSWORD'), 'confirm_password');
        $fld_cp->requirements()->setRequired();
        $fld_cp->requirements()->setCompareWith('new_password', 'eq', Label::getLabel('LBL_NEW_PASSWORD'));
        $frm->addHiddenField('', 'user_id', $userId, ['id' => 'user_id']);
        $frm->addHiddenField('', 'token', $token, ['id' => 'token']);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_RESET_PASSWORD'));
        return $frm;
    }

    /**
     * Google(Social) Login
     */
    public function googleLogin()
    {
        if (!empty($error)) {
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
        }
        $code = $_GET['code'] ?? null;
        $google = new Google();
        if (!$client = $google->getClient()) {
            Message::addErrorMessage($google->getError());
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
        }
        $client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . MyUtility::getSiteLangId()));
        $client->setScopes([Oauth2::USERINFO_EMAIL, Oauth2::USERINFO_PROFILE]);
        $client->setRedirectUri(MyUtility::makeFullUrl('GuestUser', 'googleLogin'));
        if (!empty($code)) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($code);
            if (array_key_exists('error', $accessToken)) {
                Message::addErrorMessage(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN_LATER'));
                FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
            }
            $client->setAccessToken($accessToken);
            $oauth2 = new Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            $auth = new UserAuth();
            if (!$auth->updateGoogleLogin($userInfo['id'], $userInfo['email'], $userInfo['name'])) {
                Message::addErrorMessage($auth->getError());
                FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
            }
            $user = User::getByEmail($userInfo['email']);
            if (!$auth->login($user['user_email'], $user['user_password'], MyUtility::getUserIp(), false)) {
                Message::addErrorMessage(Label::getLabel($auth->getError()));
                FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
            }
            Message::addMessage(Label::getLabel("LBL_LOG_IN_SUCCESSFULL"));
            // FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
            FatApp::redirectUser($this->getDashboardUrl());
        }
        FatApp::redirectUser($client->createAuthUrl());
    }

    /**
     * Facebook(Social) Login
     */
    public function facebookLogin()
    {
        try {
            $code = $_GET['code'] ?? null;
            $fb = new Facebook\Facebook([
                'app_id' => FatApp::getConfig('CONF_FACEBOOK_APP_ID', FatUtility::VAR_STRING, ''),
                'app_secret' => FatApp::getConfig('CONF_FACEBOOK_APP_SECRET', FatUtility::VAR_STRING, ''),
                'default_graph_version' => 'v2.10'
            ]);
            $helper = $fb->getRedirectLoginHelper();
            if (!empty($code)) {
                $accessToken = $helper->getAccessToken();
                if (empty($accessToken)) {
                    Message::addErrorMessage($helper->getError());
                    FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'signinForm'));
                }
                if (!$accessToken->isLongLived()) {
                    $oAuth2Client = $fb->getOAuth2Client();
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                }
                $profileRequest = $fb->get('/me?fields=id,name,email,first_name,last_name', $accessToken->getValue());
                $userInfo = $profileRequest->getDecodedBody();
                $auth = new UserAuth();
                $userInfo['email'] = (!empty($userInfo['email'])) ? $userInfo['email'] : '';
                if (!$user = $auth->facebookLogin($userInfo['id'], $userInfo['name'], $userInfo['email'])) {
                    Message::addErrorMessage($auth->getError());
                    FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
                }
                // $redirectUrl = MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD);
                $redirectUrl = $this->getDashboardUrl();
                $successMsgLabel = 'LBL_LOG_IN_SUCCESSFULL';
                if (empty($user['user_email'])) {
                    if (!$auth->setUserSession($user['user_email'], MyUtility::getUserIp(), $user)) {
                        Message::addErrorMessage(Label::getLabel($auth->getError()));
                        FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
                    }
                    $redirectUrl = MyUtility::makeUrl('GuestUser', 'configureEmail');
                    $successMsgLabel = 'LBL_LOG_IN_SUCCESSFULL_PLEASE_UPDATE_YOUR_EMAIL';
                } elseif (!$auth->login($user['user_email'], $user['user_password'], MyUtility::getUserIp(), false)) {
                    Message::addErrorMessage(Label::getLabel($auth->getError()));
                    FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
                }
                Message::addMessage(Label::getLabel($successMsgLabel));
                FatApp::redirectUser($redirectUrl);
            }
            FatApp::redirectUser($helper->getLoginUrl(MyUtility::makeFullUrl('GuestUser', 'facebookLogin'), ['email', 'public_profile']));
        } catch (\Throwable $th) {
            Message::addErrorMessage(Label::getLabel('LBL_FACEBOOK_LOGIN_IS_NOT_AVAILABLE'));
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
        }
    }

    /**
     * Render Configure Email Form
     */
    public function configureEmail()
    {
        if (empty($this->siteUserId)) {
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm'));
        }
        if (!empty($this->siteUser['user_email'])) {
            // FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
            FatApp::redirectUser($this->getDashboardUrl());
        }
        $this->set('frm', $this->getConfigureEmailForm());
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    /**
     * Update Email Address
     */
    public function updateEmail()
    {
        $emailFrm = $this->getConfigureEmailForm();
        if (!$post = $emailFrm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($emailFrm->getValidationErrors()));
        }
        $fields = ['user_id', 'user_email', 'user_verified', 'user_lang_id', 'user_first_name', 'user_last_name'];
        $user = User::getAttributesById($this->siteUserId, $fields);
        if (!empty($user['user_email'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $token = $this->siteUserId . '_' . FatUtility::getRandomString(15);
        $verification = new Verification($this->siteUserId);
        if (!$verification->removeToken($this->siteUserId)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($verification->getError());
        }
        if (!$verification->addToken($token, $this->siteUserId, $post['new_email'], Verification::TYPE_EMAIL_CHANGE)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($verification->getError());
        }
        $user['user_email'] = $post['new_email'];
        if (!$this->sendEmailChangeVerificationLink($token, $user)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel('MSG_UNABLE_TO_PROCESS_YOUR_REQUSET'));
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_PLEASE_VERIFY_YOUR_EMAIL'));
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_UPDATE_EMAIL_REQUEST_SENT_PLEASE_VERIFY_YOUR_NEW_EMAIL'));
    }

    /**
     * Resend Verification Link
     * 
     * @param string $email
     */
    public function resendVerificationLink(string $email)
    {
        $user = User::getByEmail($email);
        if (empty($user)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_ERROR_INVALID_REQUEST'));
        }
        if (!empty($user['user_verified'])) {
            FatUtility::dieWithError(Label::getLabel("MSG_ALREADY_VERIFIED_PLEASE_LOGIN."));
        }
        $userAuth = new UserAuth();
        if (!$userAuth->sendVerifyEmail($user)) {
            FatUtility::dieWithError($userAuth->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_VERIFICATION_EMAIL_SENT_AGAIN'));
    }

    /**
     * Get Configure Email Form
     * 
     * @return Form
     */
    private function getConfigureEmailForm(): Form
    {
        $frm = new Form('changeEmailFrm');
        $frm->addHiddenField('', 'user_id', $this->siteUserId);
        $newEmail = $frm->addEmailField(Label::getLabel('LBL_NEW_EMAIL'), 'new_email');
        $newEmail->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
        $newEmail->requirements()->setRequired();
        $conNewEmail = $frm->addEmailField(Label::getLabel('LBL_CONFIRM_NEW_EMAIL'), 'conf_new_email');
        $conNewEmailReq = $conNewEmail->requirements();
        $conNewEmailReq->setRequired();
        $conNewEmailReq->setCompareWith('new_email', 'eq');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE'));
        return $frm;
    }

    /**
     * Get Email Verification Form
     * 
     * @param string $token
     * @param array $data
     * @return boolean
     */
    private function sendEmailChangeVerificationLink(string $token, array $data): bool
    {
        $vars = [
            '{user_first_name}' => $data['user_first_name'],
            '{user_last_name}' => $data['user_last_name'],
            '{user_full_name}' => $data['user_first_name'] . ' ' . $data['user_last_name'],
            '{verification_url}' => MyUtility::makeFullUrl('GuestUser', 'verifyEmail', [$token], CONF_WEBROOT_FRONT_URL),
        ];
        $mail = new FatMailer($this->siteLangId, 'user_email_change_verification');
        $mail->setVariables($vars);
        if (!$mail->sendMail([$data['user_email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

}
