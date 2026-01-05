<?php

/**
 * Contact Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class ContactController extends MyAppController
{

    /**
     * Initialize Contact
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Contact Us
     */
    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $contactBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_CONTACT_BANNER_SECTION, $this->siteLangId);
        $contactLeftSection = ExtraPage::getBlockContent(ExtraPage::BLOCK_CONTACT_LEFT_SECTION, $this->siteLangId);
        $this->sets([
            'contactFrm' => $contactFrm,
            'siteLangId' => $this->siteLangId,
            'contactBanner' => $contactBanner,
            'contactLeftSection' => $contactLeftSection,
            'siteKey' => FatApp::getConfig('CONF_RECAPTCHA_SITEKEY'),
            'secretKey' => FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY'),
        ]);
        $this->_template->render();
    }

    /**
     * Submit Contact Us
     */
    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        
        $captcha = FatApp::getPostedData('g-recaptcha-response', FatUtility::VAR_STRING, '');
        if (!CommonHelper::verifyCaptcha($captcha)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_CAPTCHA'));
        }
        $vars = ['{name}' => $post['name'], '{email_address}' => $post['email'],
            '{phone_number}' => $post['phone'], '{message}' => nl2br($post['message'])];
        $contactEmails = explode(',', FatApp::getConfig("CONF_CONTACT_EMAIL"));
        $mail = new FatMailer($this->siteLangId, 'contact_us');
        $mail->setVariables($vars);
      if (!$mail->sendMail($contactEmails)) {
    // show detailed message only on localhost (safe)
    if ($isLocalhost) {
        FatUtility::dieJsonError('Email failed: ' . $mail->getError());
    }
    FatUtility::dieJsonError(Label::getLabel('MSG_EMAIL_NOT_SENT_SERVER_ISSUE'));
}
    }


    /**
     * Get Contact Us Form
     * 
     * @return Form
     */
    private function contactUsForm(): Form
    {
        $frm = new Form('frmContact');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Name'), 'name', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email'), 'email', '');
        $fld_phn = $frm->addRequiredField(Label::getLabel('LBL_Your_Phone'), 'phone');
        $fld_phn->requirements()->setRegularExpressionToValidate('^[\s()+-]*([0-9][\s()+-]*){5,20}$');
        $frm->addTextArea(Label::getLabel('LBL_Your_Message'), 'message')->requirements()->setRequired();
        $recaptchaKey = FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '');
        if (!empty($recaptchaKey)) {
            $fld = $frm->addHiddenField('', 'g-recaptcha-response');
            $fld->requirements()->setRequired();
            $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT'));
        return $frm;
    }

}
