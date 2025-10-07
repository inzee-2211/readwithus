<?php

/**
 * Configurations Controller is used for Configurations handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class ConfigurationsController extends AdminBaseController
{
    /* these variables must be only those which will store array type data and will saved as serialized array [ */

    private $serializeArrayValues = [];

    /* ] */

    /**
     * Initialize Configurations
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->set("includeEditor", true);
        $this->objPrivilege->canViewGeneralSettings();
    }

    public function index()
    {
        $activeTab = FatApp::getQueryStringData('tab', FatUtility::VAR_INT, Configurations::FORM_GENERAL);
        $tabs = Configurations::getTabs();
        $activeTab = (!empty($tabs[$activeTab])) ? $activeTab : Configurations::FORM_GENERAL;
        $this->sets(['activeTab' => $activeTab, 'tabs' => $tabs]);
        $this->_template->render();
    }

    /**
     * Render Configuration Form
     * 
     * @param type $frmType
     */
    public function form($frmType)
    {
        $frmType = FatUtility::int($frmType);
        if (in_array($frmType, Configurations::getLangTypeForms())) {
            $this->set('languages', Language::getAllNames());
        }
        $record = Configurations::getConfigurations();
        if (($frmType == Configurations::FORM_OPTIONS)) {
            $record['CONF_PAID_LESSON_DURATION'] = explode(',', $record['CONF_PAID_LESSON_DURATION']);
            $record['CONF_GROUP_CLASS_DURATION'] = explode(',', $record['CONF_GROUP_CLASS_DURATION']);
        }
        $frm = $this->getForm($frmType);
        $frm->fill($record);
        if ($frmType == Configurations::FORM_THIRD_PARTY) {
            $google = new Google();
            $this->sets([
                'accessToken' => $google->getAnalyticsToken(),
                'isGoogleAuthSet' => ($google->getClient() !== false)
            ]);
        }
        $disableFormType = [Configurations::FORM_THIRD_PARTY, Configurations::FORM_SEO, Configurations::FORM_LIVE_CHAT];
        if (in_array($frmType, $disableFormType) && MyUtility::isDemoUrl()) {
            MyUtility::maskAndDisableFormFields($frm, ['CONF_SITE_TRACKER_CODE', 'CONF_LIVE_CHAT_CODE']);
        }
        $this->sets([
            'frm' => $frm,
            'canEdit' => $this->objPrivilege->canEditGeneralSettings(true),
            'frmType' => $frmType,
            'lang_id' => 0,
            'formLayout' => '',
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Configuration Lang Form
     * 
     * @param int $frmType
     * @param int $langId
     * @param int $tabId
     */
    public function langForm($frmType, $langId, $tabId = null)
    {

        $frmType = FatUtility::int($frmType);
        $langId = FatUtility::int($langId);
        $frm = $this->getLangForm($frmType, $langId);
        if (in_array($frmType, Configurations::getLangTypeForms())) {
            $this->set('languages', Language::getAllNames());
        }
        if ($frmType == Configurations::FORM_MEDIA) {
            $getFiles = $this->getFiles($langId);
            $this->set('mediaData', $getFiles);
        }
        $record = Configurations::getConfigurations();
        $frm->fill($record);
        if ($tabId) {
            $this->set('tabId', $tabId);
        }
        $this->set('frm', $frm);
        $this->set('lang_id', $langId);
        $this->set('frmType', $frmType);
        $this->set('languages', Language::getAllNames());
        $this->set('formLayout', Language::getLayoutDirection($langId));
        $this->set('canEdit', $this->objPrivilege->canEditGeneralSettings(true));
        $this->_template->render(false, false, 'configurations/form.php');
    }

    /**
     * Get Media Files
     * 
     * @param int $langId
     * @return array
     */
    private function getFiles(int $langId): array
    {
        $searchBase = new SearchBase(Afile::DB_TBL);
        $searchBase->doNotCalculateRecords();
        $searchBase->addMultipleFields(['file_type', 'file_lang_id', 'file_id']);
        $searchBase->addCondition('file_type', 'IN', $this->getConfMediaType());
        $searchBase->addCondition('file_lang_id', '=', $langId);
        $searchBase->addCondition('file_path', '!=', '');
        return FatApp::getDb()->fetchAll($searchBase->getResultSet(), 'file_type');
    }

    /**
     * Setup Configuration
     */
    public function setup()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $post = FatApp::getPostedData();
        $frmType = FatUtility::int($post['form_type']);
        if (1 > $frmType) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getForm($frmType);
        $post = $frm->getFormDataFromArray($post);
        $disableFormType = [Configurations::FORM_THIRD_PARTY, Configurations::FORM_LIVE_CHAT];
        if (MyUtility::isDemoUrl() && in_array($frmType, $disableFormType)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_SETTINGS'));
        }

        if ($frmType == Configurations::FORM_SEO && MyUtility::isDemoUrl()) {
            unset($post['CONF_SITE_TRACKER_CODE']);
        }
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        unset($post['form_type']);
        unset($post['btn_submit']);
        foreach ($this->serializeArrayValues as $val) {
            if (array_key_exists($val, $post)) {
                if (is_array($post[$val])) {
                    $post[$val] = serialize($post[$val]);
                }
            } else {
                if (isset($post[$val])) {
                    $post[$val] = 0;
                }
            }
        }
        if ($frmType == Configurations::FORM_OPTIONS) {
            if ($post['CONF_EMAIL_VERIFICATION_REGISTRATION'] || $post['CONF_ADMIN_APPROVAL_REGISTRATION']) {
                $post['CONF_AUTO_LOGIN_REGISTRATION'] = 0;
            }
        }
        $msg = '';
        $record = new Configurations();
        if (
                isset($post["CONF_SEND_SMTP_EMAIL"]) &&
                $post["CONF_SEND_EMAIL"] && $post["CONF_SEND_SMTP_EMAIL"] &&
                (
                ($post["CONF_SEND_SMTP_EMAIL"] != FatApp::getConfig("CONF_SEND_SMTP_EMAIL")) ||
                ($post["CONF_SMTP_HOST"] != FatApp::getConfig("CONF_SMTP_HOST")) ||
                ($post["CONF_SMTP_PORT"] != FatApp::getConfig("CONF_SMTP_PORT")) ||
                ($post["CONF_SMTP_USERNAME"] != FatApp::getConfig("CONF_SMTP_USERNAME")) ||
                ($post["CONF_SMTP_SECURE"] != FatApp::getConfig("CONF_SMTP_SECURE")) ||
                ($post["CONF_SMTP_PASSWORD"] != FatApp::getConfig("CONF_SMTP_PASSWORD"))
                )
        ) {
            $smtp_arr = ["host" => $post["CONF_SMTP_HOST"], "port" => $post["CONF_SMTP_PORT"], "username" => $post["CONF_SMTP_USERNAME"], "password" => $post["CONF_SMTP_PASSWORD"], "secure" => $post["CONF_SMTP_SECURE"]];
            $mail = new FatMailer($this->siteLangId, 'test_email');
            $saved = $mail->sendSmtpTestEmail($post);
            if ($saved) {
                if (!$record->update($post)) {
                    FatUtility::dieJsonError($record->getError());
                }
                FatUtility::dieJsonSuccess(Label::getLabel('LBL_WE_HAVE_SENT_A_TEST_EMAIL_TO_ADMINISTRATOR_ACCOUNT_' . FatApp::getConfig("CONF_SITE_OWNER_EMAIL")));
            } else {
                FatUtility::dieJsonError(Label::getLabel("LBL_SMTP_settings_provided_is_invalid_or_unable_to_send_email_so_we_have_not_saved_SMTP_settings"));
            }
        }
        if (isset($post['CONF_USE_SSL']) && $post['CONF_USE_SSL'] == 1) {
            if (!$this->is_ssl_enabled()) {
                if ($post['CONF_USE_SSL'] != FatApp::getConfig('CONF_USE_SSL')) {
                    FatUtility::dieJsonError(Label::getLabel('MSG_SSL_NOT_INSTALLED_FOR_WEBSITE_Try_to_Save_data_without_Enabling_ssl'));
                }
                unset($post['CONF_USE_SSL']);
            }
        }
        $unselectedSlot = [];
        if (array_key_exists('CONF_PAID_LESSON_DURATION', $post)) {
            if (!in_array($post['CONF_DEFAULT_PAID_LESSON_DURATION'], $post['CONF_PAID_LESSON_DURATION'])) {
                FatUtility::dieJsonError(Label::getLabel('MSG_PLEASE_SELECT_DEFAULT_DURATION_FROM_SELECTED_DURATIONS'));
            }
            $bookingSlots = FatApp::getConfig('CONF_PAID_LESSON_DURATION');
            $bookingSlots = explode(',', $bookingSlots);
            $unselectedSlot = array_diff($bookingSlots, $post['CONF_PAID_LESSON_DURATION']);
            $post['CONF_PAID_LESSON_DURATION'] = implode(',', $post['CONF_PAID_LESSON_DURATION']);
        }

        if (array_key_exists('CONF_GROUP_CLASS_DURATION', $post)) {
            $post['CONF_GROUP_CLASS_DURATION'] = implode(',', $post['CONF_GROUP_CLASS_DURATION']);
        }
        if (!$record->update($post)) {
            FatUtility::dieJsonError($record->getError());
        }
        if (!empty($unselectedSlot)) {
            $teachLangPrice = new TeachLangPrice();
            $teachLangPrice->deleteTeachSlots($unselectedSlot);
            $teacherStat = new TeacherStat(0);
            $teacherStat->setTeachLangPricesBulk();
        }
        if (array_key_exists('CONF_SITE_CURRENCY', $post)) {
            $siteCurrency = Currency::getData($post['CONF_SITE_CURRENCY'], $this->siteLangId);
            MyUtility::setSiteCurrency($siteCurrency, true);
        }
        $data = [
            'msg' => $msg ?: Label::getLabel('MSG_Setup_Successful'),
            'frmType' => $frmType,
            'langId' => 0
        ];
        Fatutility::dieJsonSuccess($data);
    }

    /**
     * Is SSL Enabled
     * 
     * @return boolean
     */
    public function is_ssl_enabled()
    {
        // url connection
        $url = "https://" . $_SERVER["HTTP_HOST"];
        // Initiate connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); // set browser/user agent
        // Set cURL and other options
        curl_setopt($ch, CURLOPT_URL, $url); // set url
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // allow https verification if true
        curl_setopt($ch, CURLOPT_NOBODY, true);
        // grab URL and pass it to the browser
        $res = curl_exec($ch);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * Setup Language
     */
    public function setupLang()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $post = FatApp::getPostedData();
        $frmType = FatUtility::int($post['form_type']);
        $langId = FatUtility::int($post['lang_id']);
        if (1 > $frmType || 1 > $langId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($frmType, $langId);
        $post = $frm->getFormDataFromArray($post);
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        unset($post['form_type']);
        unset($post['lang_id']);
        unset($post['btn_submit']);
        $config = new Configurations();
        if (!$config->update($post)) {
            FatUtility::dieJsonError($config->getError());
        }
        $data = [
            'msg' => Label::getLabel('MSG_Setup_Successful'),
            'frmType' => $frmType,
            'langId' => $langId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Upload Media
     */
    public function uploadMedia()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST_OR_FILE_NOT_SUPPORTED'));
        }
        $fileType = FatApp::getPostedData('file_type', FatUtility::VAR_INT, 0);
        $lang_id = FatApp::getPostedData('lang_id', FatUtility::VAR_INT, 0);
        $allowedFileTypeArr = $this->getConfMediaType();
        if (MyUtility::isDemoUrl()) {
            $logoTypes = [
                Afile::TYPE_FRONT_LOGO,
                Afile::TYPE_PAYMENT_PAGE_LOGO
            ];
            if (in_array($fileType, $logoTypes)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO'));
            }
        }
        if (!in_array($fileType, $allowedFileTypeArr)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_PLEASE_SELECT_A_FILE'));
        }
        $file = new Afile($fileType, $lang_id);
        if (!$file->saveFile($_FILES['file'], 0, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        $data = [
            'file' => $_FILES['file']['name'],
            'frmType' => Configurations::FORM_GENERAL,
            'msg' => $_FILES['file']['name'] . Label::getLabel('MSG_UPLOADED_SUCCESSFULLY')
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Google Authorize
     */
    public function googleAuthorize()
    {
        $code = FatApp::getQueryStringData('code', FatUtility::VAR_STRING, NULL);
        $error = FatApp::getQueryStringData('error', FatUtility::VAR_STRING, NULL);
        if (!empty($error)) {
            FatApp::redirectUser(MyUtility::makeUrl('Configurations') . '?tab=' . Configurations::FORM_THIRD_PARTY);
        }
        $googleAnalytics = new GoogleAnalytics();
        $authorize = $googleAnalytics->authorize($code);
        $redirectUrl = $googleAnalytics->getRedirectUrl();
        $msg = $googleAnalytics->getError();
        if (!$authorize) {
            if (empty($code)) {
                FatUtility::dieJsonError(['msg' => $msg, 'redirectUrl' => $redirectUrl]);
            }
            Message::addErrorMessage($msg);
            FatApp::redirectUser($redirectUrl);
        }
        if (empty($code)) {
            FatUtility::dieJsonSuccess(['redirectUrl' => $googleAnalytics->getRedirectUrl()]);
        }
        Message::addMessage(Label::getLabel('LBL_GOOGLE_AUTHORIZE_SCCESSFULY'));
        FatApp::redirectUser($redirectUrl);
    }

    /**
     * Remove Media
     */
    public function removeMedia()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $type = FatApp::getPostedData('type', FatUtility::VAR_INT, 0);
        $langId = FatApp::getPostedData('langId', FatUtility::VAR_INT, 0);
        if (MyUtility::isDemoUrl()) {
            $logoTypes = [
                Afile::TYPE_FRONT_LOGO,
                Afile::TYPE_PAYMENT_PAGE_LOGO
            ];
            if (in_array($type, $logoTypes)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO'));
            }
        }
        $file = new Afile($type, $langId);
        if (!$file->removeFile(0, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_Deleted_Successfully'));
    }

    /**
     * Get Configuration Form
     * 
     * @param int $type
     * @param array $arrValues
     * @return Form
     */
    private function getForm(int $type, array $arrValues = []): Form
    {
        $frm = new Form('frmConfiguration');
        switch ($type) {
            case Configurations::FORM_GENERAL:
                $frm->addEmailField(Label::getLabel('LBL_Store_Owner_Email'), 'CONF_SITE_OWNER_EMAIL');
                $frm->addTextBox(Label::getLabel('LBL_Telephone'), 'CONF_SITE_PHONE');
                $cpagesArr = ContentPage::getPagesForSelectBox($this->siteLangId);
                $frm->addSelectBox(Label::getLabel('LBL_Privacy_Policy_Page'), 'CONF_PRIVACY_POLICY_PAGE', $cpagesArr);
                $frm->addSelectBox(Label::getLabel('LBL_Terms_and_Conditions_Page'), 'CONF_TERMS_AND_CONDITIONS_PAGE', $cpagesArr);
                $frm->addSelectBox(Label::getLabel('LBL_Cookies_Policies_Page'), 'CONF_COOKIES_BUTTON_LINK', $cpagesArr);
                $fld1 = $frm->addCheckBox(Label::getLabel('LBL_Cookies_Policies'), 'CONF_ENABLE_COOKIES', 1, [], false, 0);
                $fld1->htmlAfterField = "<br><small>" . Label::getLabel("LBL_cookies_policies_section_will_be_shown_on_frontend") . "</small>";
                break;
            case Configurations::FORM_LOCAL:
                $frm->addSelectBox(Label::getLabel('LBL_Default_Site_Laguage'), 'CONF_DEFAULT_LANG', Language::getAllNames(), false, [], '');
                $frm->addSelectBox(Label::getLabel('LBL_Country'), 'CONF_COUNTRY', Country::getNames($this->siteLangId));
                $currencyArr = Currency::getCurrencyNameWithCode($this->siteLangId);
                $frm->addSelectBox(Label::getLabel('LBL_Default_Site_Currency'), 'CONF_SITE_CURRENCY', $currencyArr, false, [], '');
                break;
            case Configurations::FORM_SEO:
                $fld = $frm->addCheckBox(Label::getLabel('LBL_ENABLE_LANGUAGE_CODE_TO_SITE_URLS'), 'CONF_LANGCODE_URL', 1, [], false, 0);
                $fld->htmlAfterField = '<small>' . Label::getLabel("LBL_LANGUAGE_CODE_TO_SITE_URLS_EXAMPLES") . '</small>';
                $fld2 = $frm->addTextarea(Label::getLabel('LBL_Site_Tracker_Code'), 'CONF_SITE_TRACKER_CODE');
                $fld2->htmlAfterField = '<small>' . Label::getLabel("LBL_This_is_the_site_tracker_script,_used_to_track_and_analyze_data_about_how_people_are_getting_to_your_website._e.g.,_Google_Analytics.") . ' http://www.google.com/analytics/</small>';
                break;
            case Configurations::FORM_OPTIONS:
                $frm->addHtml('', 'Admin', '<h3>' . Label::getLabel('LBL_Admin') . '</h3>');
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_Default_Items_Per_Page"), "CONF_ADMIN_PAGESIZE");
                $fld3->requirements()->setRange(1, 500);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Set_number_of_records_shown_per_page_(Users,_orders,_etc)") . "</small>";
                $frm->addHtml('', 'FlashCard', '<h3>' . Label::getLabel('LBL_FlashCards') . '</h3>');
                $frm->addCheckBox(Label::getLabel("CONF_ENABLE_FLASHCARD"), 'CONF_ENABLE_FLASHCARD', 1, [], false, 0);
                $frm->addHtml('', 'NEWSLETTER_SUBSCRIPTION', '<h3>' . Label::getLabel('LBL_NEWSLETTER_SUBSCRIPTION') . '</h3>');
                $frm->addCheckBox(Label::getLabel("CONF_ENABLE_NEWSLETTER_SUBSCRIPTION"), 'CONF_ENABLE_NEWSLETTER_SUBSCRIPTION', 1, [], false, 0);
                $frm->addHtml('', 'Free_Trial', '<h3>' . Label::getLabel('LBL_Free_Trial') . '</h3>');
                $frm->addCheckBox(Label::getLabel("CONF_ENABLE_FREE_TRIAL"), 'CONF_ENABLE_FREE_TRIAL', AppConstant::YES, [], false, AppConstant::NO);
                $frm->addHtml('', 'report_issue', '<h3>' . Label::getLabel('LBL_REPORT/ESCALATE_ISSUE_TIME') . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("MINIMUM_GIFT_CARD_AMOUNT"), "MINIMUM_GIFT_CARD_AMOUNT");
                $fld->requirements()->setIntPositive();
                $fld->requirements()->setRange(1, 99999);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_MINIMUM_GIFT_CARD_AMOUNT_TEXT") . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION"), "CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION");
                $fld->requirements()->setIntPositive();
                $fld->requirements()->setRange(0, 168);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_REPORT_ISSUE_HOURS_AFTER_COMPLETION_TEXT") . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("CONF_ESCALATED_ISSUE_HOURS_AFTER_RESOLUTION"), "CONF_ESCALATED_ISSUE_HOURS_AFTER_RESOLUTION");
                $fld->requirements()->setIntPositive();
                $fld->requirements()->setRange(0, 168);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_ESCALATED_ISSUE_HOURS_AFTER_RESOLUTION_TEXT") . "</small>";
                $frm->addHtml('', 'Grpcls', '<h3>' . Label::getLabel('LBL_GROUP_CLASS') . '</h3>');
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_PACKAGE_CANCEL_DURATION"), "CONF_PACKAGE_CANCEL_DURATION");
                $fld3->requirements()->setRange(0, 50);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_PACKAGE_CANCEL_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_CLASS_CANCEL_DURATION"), "CONF_CLASS_CANCEL_DURATION");
                $fld3->requirements()->setRange(0, 50);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_CLASS_CANCEL_DURATION_TXT") . "</small>";
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_CLASS_REFUND_DURATION"), "CONF_CLASS_REFUND_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_CLASS_REFUND_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addFloatField(Label::getLabel("LBL_CLASS_REFUND_PERCENTAGE_AFTER_DURATION"), "CONF_CLASS_REFUND_PERCENTAGE_AFTER_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_CLASS_REFUND_PERCENTAGE_AFTER_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addFloatField(Label::getLabel("LBL_CLASS_REFUND_PERCENTAGE_BEFORE_DURATION"), "CONF_CLASS_REFUND_PERCENTAGE_BEFORE_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_CLASS_REFUND_PERCENTAGE_BEFORE_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_END_CLASS_DURATION"), "CONF_ALLOW_TEACHER_END_CLASS");
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_DURATION_AFTER_TEACHER_CAN_END_CLASS_(In_Minutes)") . "</small>";
                $fld3 = $frm->addTextBox(Label::getLabel("LBL_Class_Booking_Time_Span(Minutes)"), "CONF_CLASS_BOOKING_GAP");
                $fld3->requirements()->setIntPositive();
                $fld3->requirements()->setRange(0, 1000);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_CLASS_BOOKING_GAP_TEXT") . "</small>";
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_Class_Max_learners"), "CONF_GROUP_CLASS_MAX_LEARNERS");
                $fld3->requirements()->setRange(1, 99999);
                $slots = AppConstant::getGroupClassSlots();
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_GROUP_CLASS_MAX_LEARNERS_TEXT") . "</small>";
                $fld = $frm->addCheckBoxes(Label::getLabel("LBL_LESSON_DURATION_FOR_GROUP_CLASSES"), "CONF_GROUP_CLASS_DURATION", $slots, [], ['class' => 'list-inline']);
                $fld->requirements()->setSelectionRange(1, count($slots));
                $fld->htmlAfterField = "<small>" . Label::getLabel("htmlAfterField_GROUP_CLASS_DURATIONS_TEXT") . "</small>";
                $frm->addHtml('', 'Admin', '<h3>' . Label::getLabel('LBL_Teacher_Dashboard') . '</h3>');
                $bookingSlots = AppConstant::getBookingSlots();
                $fld = $frm->addCheckBoxes(Label::getLabel("LBL_Lesson_durations"), "CONF_PAID_LESSON_DURATION", $bookingSlots, [], ['class' => 'list-inline']);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_DURATIONS_TEXT") . "</small>";
                $frm->addRadioButtons(Label::getLabel("LBL_Default_Lesson_duration"), "CONF_DEFAULT_PAID_LESSON_DURATION", $bookingSlots, '', ['class' => 'list-inline'])->requirements()->setRequired();
                $frm->addRadioButtons(Label::getLabel("LBL_Trial_Lesson_duration"), "CONF_TRIAL_LESSON_DURATION", $bookingSlots, '', ['class' => 'list-inline'])->requirements()->setRequired();
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_END_LESSON_DURATION"), "CONF_ALLOW_TEACHER_END_LESSON");
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Duration_After_Teacher_Can_End_Lesson_(In_Minutes)") . "</small>";
                $maxAttemptFld = $frm->addIntegerField(Label::getLabel("LBL_MAX_TEACHER_REQUEST_ATTEMPT"), "CONF_MAX_TEACHER_REQUEST_ATTEMPT");
                $maxAttemptFld->requirements()->setRange(0, 10);
                $maxAttemptFld->htmlAfterField = "<br><small>" . Label::getLabel("htmlafterfield_MAX_TEACHER_REQUEST_ATTEMPT_TEXT") . ".</small>";
                $fld = $frm->addIntegerField(Label::getLabel("LBL_LESSON_RESCHEDULE_DURATION"), "CONF_LESSON_RESCHEDULE_DURATION");
                $fld->requirements()->setRange(0, 50);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_RESCHEDULE_DURATION_TEXT") . "</small>";
                $fld = $frm->addIntegerField(Label::getLabel("LBL_LESSON_CANCEL_DURATION"), "CONF_LESSON_CANCEL_DURATION");
                $fld->requirements()->setRange(0, 50);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_CANCEL_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_LESSON_REFUND_DURATION"), "CONF_LESSON_REFUND_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_REFUND_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addFloatField(Label::getLabel("LBL_LESSON_REFUND_PERCENTAGE_AFTER_DURATION"), "CONF_LESSON_REFUND_PERCENTAGE_AFTER_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_REFUND_PERCENTAGE_AFTER_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addFloatField(Label::getLabel("LBL_LESSON_REFUND_PERCENTAGE_BEFORE_DURATION"), "CONF_LESSON_REFUND_PERCENTAGE_BEFORE_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_REFUND_PERCENTAGE_BEFORE_DURATION_TEXT") . "</small>";
                $fld3 = $frm->addFloatField(Label::getLabel("LBL_UNSCHEDULE_LESSON_REFUND_PERCENTAGE"), "CONF_UNSCHEDULE_LESSON_REFUND_PERCENTAGE");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_UNSCHEDULE_LESSON_REFUND_PERCENTAGE_TEXT") . "</small>";
                /* Course Configurations[ */
                $frm->addHtml('', 'Course', '<h3>' . Label::getLabel("LBL_COURSE") . '</h3>');
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_COURSE_CANCELLATION_DURATION(DAYS)"), "CONF_COURSE_CANCEL_DURATION");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_COURSE_CANCELLATION_DURATION_TEXT") . ".</small>";
                $reviewStatus = [
                    Course::REFUND_PENDING => Label::getLabel('STATUS_PENDING'),
                    Course::REFUND_APPROVED => Label::getLabel('STATUS_APPROVED')
                ];
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_COURSE_DEFAULT_CANCELLATION_STATUS"), 'CONF_COURSE_DEFAULT_CANCELLATION_STATUS', $reviewStatus, '', ['class' => 'list-inline']);
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_SET_THE_DEFAULT_STATUS_WHEN_A_COURSE_CANCELLATION_REQUEST_IS_PLACED") . "</small>";
                /* ] */
                $frm->addHtml('', 'Account', '<h3>' . Label::getLabel("LBL_Account") . '</h3>');
                $fld5 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Admin_Approval_After_Registration_(Sign_Up)"), 'CONF_ADMIN_APPROVAL_REGISTRATION', 1, [], false, 0);
                $fld5->htmlAfterField = '<br><small>' . Label::getLabel("LBL_On_enabling_this_feature,_admin_need_to_approve_each_learner_after_registration_(Learner_cannot_login_until_admin_approves)") . '</small>';
                $fld7 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Email_Verification_After_Registration"), 'CONF_EMAIL_VERIFICATION_REGISTRATION', 1, [], false, 0);
                $fld7->htmlAfterField = "<br><small>" . Label::getLabel("LBL_user_need_to_verify_their_email_address_provided_during_registration") . " </small>";
                $fld9 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Auto_Login_After_Registration"), 'CONF_AUTO_LOGIN_REGISTRATION', 1, [], false, 0);
                $fld9->htmlAfterField = "<br><small>" . Label::getLabel("LBL_On_enabling_this_feature,_users_will_be_automatically_logged-in_after_registration") . "</small>";
                $fld10 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Sending_Welcome_Mail_After_Registration"), 'CONF_WELCOME_EMAIL_REGISTRATION', 1, [], false, 0);
                $fld10->htmlAfterField = "<br><small>" . Label::getLabel("LBL_On_enabling_this_feature,_users_will_receive_a_welcome_mail_after_registration.") . "</small>";
                $frm->addHtml('', 'Withdrawal', '<h3>' . Label::getLabel("LBL_Withdrawal") . '</h3>');
                $fld = $frm->addIntegerField(Label::getLabel("LBL_Minimum_Withdrawal_Amount") . ' [' . $this->siteCurrency['currency_code'] . ']', 'CONF_MIN_WITHDRAW_LIMIT', '');
                $fld->htmlAfterField = "<small> " . Label::getLabel("LBL_This_is_the_minimum_withdrawable_amount.") . "</small>";
                $fld->requirements()->setRange(1, 999999);
                $fld = $frm->addIntegerField(Label::getLabel("LBL_Minimum_Interval_[Days]"), 'CONF_MIN_INTERVAL_WITHDRAW_REQUESTS', '');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_minimum_interval_in_days_between_two_withdrawal_requests.") . "</small>";
                $fld->requirements()->setRange(0, 999999);
                $reviewStatus = [
                    RatingReview::STATUS_PENDING => Label::getLabel('STATUS_PENDING'),
                    RatingReview::STATUS_APPROVED => Label::getLabel('STATUS_APPROVED')
                ];
                $frm->addHtml('', 'reviews', '<h3>' . Label::getLabel("LBL_REVIEWS") . '</h3>');
                $frm->addRadioButtons(Label::getLabel("LBL_ALLOW_REVIEWS"), 'CONF_ALLOW_REVIEWS', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_DEFAULT_REVIEW_STATUS"), 'CONF_DEFAULT_REVIEW_STATUS', $reviewStatus, '', ['class' => 'list-inline']);
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_SET_THE_DEFAULT_REVIEW_ORDER_STATUS_WHEN_A_NEW_REVIEW_IS_PLACED") . "</small>";
                $frm->addHtml('', 'checkout', '<h3>' . Label::getLabel("LBL_Checkout") . '</h3>');
                $fld1 = $frm->addCheckBox(Label::getLabel('LBL_Activate_Live_Payment_Transaction_Mode'), 'CONF_TRANSACTION_MODE', 1, [], false, 0);
                $fld1->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Set_Transaction_Mode_To_Live_Environment") . "</small>";

                /* Notifications Settings */
                $frm->addHtml('', 'notification', '<h3>' . Label::getLabel("LBL_Notifications") . '</h3>');

                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Enable_Unread_Messages_Notifications"), 'CONF_ENABLE_UNREAD_MSG_NOTIFICATION', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_Enable_Email_Notifications_For_Unread_Messages.") . "</small>";

                $fld = $frm->addIntegerField(Label::getLabel("LBL_Unread_Messages_Notify_Duration[mins]"), 'CONF_UNREAD_MSG_NOTIFICATION_DURATION', '');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_Is_The_Messages_Unread_Duration_After_Which_Users_Will_Get_Notification._Recommended_Duration:_10_Mins") . "</small>";
                $fld->requirements()->setRange(0, 999999);

                /* Attachments settings */
                $frm->addHtml('', 'Attachments', '<h3>' . Label::getLabel("LBL_ATTACHMENTS") . '</h3>');
                $fld = $frm->addIntegerField(Label::getLabel("LBL_DELETE_ATTACHMENT_DURATION[MINS]"), 'CONF_DELETE_ATTACHMENT_ALLOWED_DURATION', '');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_THIS_IS_THE_DURATION_UNTIL_THE_USERS_ARE_ALLOWED_TO_DELETE_SENT_ATTACHMENTS_IN_MESSAGES") . "</small>";
                break;
            case Configurations::FORM_EMAIL:
                $frm->addEmailField(Label::getLabel("LBL_From_Email"), 'CONF_FROM_EMAIL');
                $frm->addEmailField(Label::getLabel("LBL_Reply_to_Email_Address"), 'CONF_REPLY_TO_EMAIL');
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Send_Email"), 'CONF_SEND_EMAIL', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                if (FatApp::getConfig('CONF_SEND_EMAIL', FatUtility::VAR_INT, 1)) {
                    $fld->htmlAfterField = '<a href="javascript:void(0)" id="testMail-js">' . Label::getLabel("LBL_Click_Here") . '</a> to test email. ' . Label::getLabel("LBL_This_will_send_Test_Email_to_Site_Owner_Email") . ' - ' . FatApp::getConfig("CONF_SITE_OWNER_EMAIL");
                }
                $frm->addEmailField(Label::getLabel("LBL_Contact_Email_Address"), 'CONF_CONTACT_EMAIL');
                $frm->addRadioButtons(Label::getLabel("LBL_Send_SMTP_Email"), 'CONF_SEND_SMTP_EMAIL', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                $fld = $frm->addTextBox(Label::getLabel("LBL_SMTP_Host"), 'CONF_SMTP_HOST');
                $fld = $frm->addTextBox(Label::getLabel("LBL_SMTP_Port"), 'CONF_SMTP_PORT');
                $fld = $frm->addTextBox(Label::getLabel("LBL_SMTP_Username"), 'CONF_SMTP_USERNAME');
                $fld = $frm->addPasswordField(Label::getLabel("LBL_SMTP_Password"), 'CONF_SMTP_PASSWORD');
                $frm->addRadioButtons(Label::getLabel("LBL_SMTP_Secure"), 'CONF_SMTP_SECURE', AppConstant::getSmtpSecureArr(), '', ['class' => 'list-inline']);
                break;
            case Configurations::FORM_LIVE_CHAT:
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Activate_Live_Chat"), 'CONF_ENABLE_LIVECHAT', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Activate_3rd_Party_Live_Chat.") . "</small>";
                $fld = $frm->addTextarea(Label::getLabel("LBL_Live_Chat_Code"), 'CONF_LIVE_CHAT_CODE');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_live_chat_script/code_provided_by_the_3rd_party_API_for_integration.") . "</small>";
                break;
            case Configurations::FORM_THIRD_PARTY:
                $frm->addHtml('', 'Newsletter', '<h3>' . Label::getLabel("LBL_FACEBOOK_LOGIN") . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Facebook_APP_ID"), 'CONF_FACEBOOK_APP_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_ID_used_in_login_and_post.") . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Facebook_App_Secret"), 'CONF_FACEBOOK_APP_SECRET');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Facebook_secret_key_used_for_authentication_and_other_Facebook_related_plugins_support.") . "</small>";
                $frm->addHtml('', 'Newsletter', '<h3>' . Label::getLabel("LBL_NEWSLETTER_SUBSCRIPTION") . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Mailchimp_Key"), 'CONF_MAILCHIMP_KEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Mailchimp_application_key_used_in_subscribe_and_send_newsletters.") . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Mailchimp_List_ID"), 'CONF_MAILCHIMP_LIST_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Mailchimp_subscribers_List_ID") . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_MAILCHIMP_SERVER_PREFIX"), 'CONF_MAILCHIMP_SERVER_PREFIX');
                $fld->htmlAfterField = "<small>" . Label::getLabel("htmlAfterField_MAILCHIMP_SERVER_PREFIX_TEXT") . "</small>";
                $frm->addHtml('', 'Analytics', '<h3>' . Label::getLabel("LBL_Google_Analytics") . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_GOOGLE_ANALYTICS_TABLE_ID"), 'CONF_ANALYTICS_TABLE_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_google_analytics_table_id_example") . "</small>";
                $frm->addHtml('', 'Analytics', '<h3>' . Label::getLabel("LBL_Google_Recaptcha") . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Site_Key"), 'CONF_RECAPTCHA_SITEKEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_Site_key_used_for_Google_Recaptcha.") . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Secret_Key"), 'CONF_RECAPTCHA_SECRETKEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_Secret_key_used_for_Google_Recaptcha.") . "</small>";
                $frm->addHtml('', '', '<h3>' . Label::getLabel("LBL_YOUTUBE_DATA_API") . '</h3>');
                $fld2 = $frm->addTextBox(Label::getLabel('LBL_YOUTUBE_DATA_API_KEY'), 'CONF_YOUTUBE_DATA_API_KEY');
                $fld2->htmlAfterField = '<small>' . Label::getLabel("LBL_YOUTUBE_API_KEY_MESSAGE") . '</small>';
                $frm->addHtml('', '', '<h3>' . Label::getLabel("LBL_Google_Client_Json") . '</h3>');
                $fld2 = $frm->addTextarea(Label::getLabel('LBL_Google_Client_Json'), 'CONF_GOOGLE_CLIENT_JSON');
                $fld2->htmlAfterField = '<small>' . Label::getLabel("LBL_GOOGLE_JSON_MESSAGE") . '</small>';
                break;
            case Configurations::FORM_SERVER:
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Use_SSL"), 'CONF_USE_SSL', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                $fld->htmlAfterField = '<small>' . Label::getLabel("LBL_NOTE:_To_use_SSL,_check_with_your_host") . '.</small>';
                if (!MyUtility::isDemoUrl()) {
                    $fld = $frm->addRadioButtons(Label::getLabel("LBL_Maintenance_Mode"), 'CONF_MAINTENANCE', AppConstant::getYesNoArr(), '', ['class' => 'list-inline']);
                    $fld->htmlAfterField = '<small>' . Label::getLabel("LBL_Enable_Maintenance_Mode_Text") . '.</small>';
                }
                break;
            case Configurations::FORM_SECURITY:
                $fld = $frm->addIntegerField(Label::getLabel("LBL_REMEMBER_ME_DAYS_FOR_ADMIN"), 'CONF_ADMIN_REMEMBER_ME_DAYS');
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_ADMIN_REMEMBER_ME_DAYS") . "</small>";
                $fld->requirements()->setRange(1, 365);
                $fld = $frm->addIntegerField(Label::getLabel("LBL_REMEMBER_ME__DAYS_FOR_USER"), 'CONF_USER_REMEMBER_ME_DAYS');
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_USER_REMEMBER_ME_DAYS") . "</small>";
                $fld->requirements()->setRange(1, 365);
                $fld = $frm->addSelectBox(Label::getLabel("LBL_REMEMBER_ME_SECURITY_FOR_ADMIN"), 'CONF_ADMIN_REMEMBER_ME_IP_ENABLE', Configurations::getSecuritySettings(), '', [], '');
                $fld->requirements()->setRequired();
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_ADMIN_REMEMBER_ME_IP_ENABLE") . "</small>";
                $fld = $frm->addSelectBox(Label::getLabel("LBL_REMEMBER_ME_SECURITY_FOR_USER"), 'CONF_USER_REMEMBER_ME_IP_ENABLE', Configurations::getSecuritySettings(), '', [], '');
                $fld->requirements()->setRequired();
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_USER_REMEMBER_ME_IP_ENABLE") . "</small>";
                break;
        }
        $frm->addHiddenField('', 'form_type', $type);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel("LBL_Save_Changes"));
        return $frm;
    }

    /**
     * Get Lang Form
     * 
     * @param int $type
     * @param int $langId
     * @return Form
     */
    private function getLangForm(int $type, int $langId): Form
    {
        $frm = new Form('frmConfiguration');
        switch ($type) {
            case Configurations::FORM_GENERAL:
                $frm->addTextBox(Label::getLabel("LBL_Site_Name"), 'CONF_WEBSITE_NAME_' . $langId);
                $frm->addTextBox(Label::getLabel("LBL_EMAIL_FROM_NAME"), 'CONF_FROM_NAME_' . $langId);
                $fld = $frm->addTextarea(Label::getLabel("LBL_ADDRESS"), 'CONF_ADDRESS_' . $langId);
                $fld->requirements()->setLength(20, 130);
                $fld = $frm->addTextarea(Label::getLabel('LBL_Cookies_Policies_Text'), 'CONF_COOKIES_TEXT_' . $langId);
                $fld->requirements()->setLength(50, 200);
                break;
            case Configurations::FORM_MEDIA:
                $frm->addButton(Label::getLabel("LBL_ADMIN_LOGO"), 'admin_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'admin_logo', 'data-file_type' => Afile::TYPE_ADMIN_LOGO]);
                $frm->addButton(Label::getLabel("LBL_DESKTOP_LOGO"), 'front_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'front_logo', 'data-file_type' => Afile::TYPE_FRONT_LOGO]);
                $frm->addButton(Label::getLabel("LBL_EMAIL_TEMPLATE_LOGO"), 'email_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'email_logo', 'data-file_type' => Afile::TYPE_EMAIL_LOGO]);
                $frm->addButton(Label::getLabel("LBL_WEBSITE_FAVICON"), 'favicon', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'favicon', 'data-file_type' => Afile::TYPE_FAVICON]);
                $frm->addButton(Label::getLabel('LBL_Payment_Page_Logo'), 'payment_page_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'payment_page_logo', 'data-file_type' => Afile::TYPE_PAYMENT_PAGE_LOGO]);
                $frm->addButton(Label::getLabel('LBL_Apple_Touch_Icon'), 'apple_touch_icon', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'apple_touch_icon', 'data-file_type' => Afile::TYPE_APPLE_TOUCH_ICON]);
                $frm->addButton(Label::getLabel('LBL_Blog_Image'), 'blog_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'blog_img', 'data-file_type' => Afile::TYPE_BLOG_PAGE_IMAGE]);
                $frm->addButton(Label::getLabel('LBL_Lesson_Image'), 'lesson_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'lesson_img', 'data-file_type' => Afile::TYPE_LESSON_PAGE_IMAGE]);
                $frm->addButton(Label::getLabel('LBL_Apply_To_Teach_Banner'), 'apply_to_teach_banner', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'apply_to_teach_banner', 'data-file_type' => Afile::TYPE_APPLY_TO_TEACH_BANNER]);
                $frm->addButton(Label::getLabel('LBL_CERTIFICATE_LOGO'), 'certificate_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'certificate_logo', 'data-file_type' => Afile::TYPE_CERTIFICATE_LOGO]);
                break;
            case Configurations::FORM_SERVER:
                $fld = $frm->addHtmlEditor(Label::getLabel('LBL_Maintenance_Text'), 'CONF_MAINTENANCE_TEXT_' . $langId);
                $fld->requirements()->setRequired(true);
                break;
        }
        $frm->addHiddenField('', 'lang_id', $langId);
        $frm->addHiddenField('', 'form_type', $type);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel("LBL_Save_Changes"));
        return $frm;
    }

    /**
     * Test Email
     */
    public function testEmail()
    {
        try {
            $mail = new FatMailer($this->siteLangId, 'test_email');
            if (!$mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')])) {
                FatUtility::dieJsonError($mail->getError());
            }
            FatUtility::dieJsonSuccess("Mail sent to - " . FatApp::getConfig('CONF_SITE_OWNER_EMAIL'));
        } catch (Exception $e) {
            FatUtility::dieJsonError($e->getMessage());
        }
    }

    /**
     * Conf Media Types
     * 
     * @return array
     */
    private function getConfMediaType(): array
    {
        return [
            Afile::TYPE_ADMIN_LOGO,
            Afile::TYPE_FRONT_LOGO,
            Afile::TYPE_PAYMENT_PAGE_LOGO,
            Afile::TYPE_EMAIL_LOGO,
            Afile::TYPE_FAVICON,
            Afile::TYPE_APPLE_TOUCH_ICON,
            Afile::TYPE_BLOG_PAGE_IMAGE,
            Afile::TYPE_LESSON_PAGE_IMAGE,
            Afile::TYPE_APPLY_TO_TEACH_BANNER,
            Afile::TYPE_CERTIFICATE_LOGO
        ];
    }

}
