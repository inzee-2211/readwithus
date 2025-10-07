<?php

/**
 * Admin Controller Class is base controller for Backend controllers
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class AdminController extends FatController
{

    protected $siteAdmin;
    protected $siteAdminId;
    protected $siteLangId;
    protected $siteLanguage;
    protected $siteTimezone;
    protected $objPrivilege;
    protected $layoutDirection;

    /**
     * Initialize Admin Controller
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->setLoggedAdmin();
        $this->setSiteLanguage();
        $this->setAdminTimezone();
        $this->setSiteCurrency();
        $this->objPrivilege = new AdminPrivilege();
        $this->layoutDirection = MyUtility::getLayoutDirection();
        $siteLanguages = $this->getSiteLanguages();
        $this->sets([
            'favicon' => (new Afile(Afile::TYPE_FAVICON))->getFile(),
            'siteLangId' => $this->siteLangId,
            'siteLanguage' => $this->siteLanguage,
            'siteLanguages' => $siteLanguages,
            'siteAdminId' => $this->siteAdminId,
            'siteTimezone' => $this->siteTimezone,
            'actionName' => $this->_actionName,
            'layoutDirection' =>$this->layoutDirection,
            'controllerName' => str_replace('Controller', '', $this->_controllerName),
        ]);
        if (!FatUtility::isAjaxCall()) {
            $this->set('jsVariables', $this->getJsVariables($siteLanguages));
        }
        if ($this->layoutDirection == 'rtl') {
            $this->_template->addCss('css/style--arabic.css');
        }
    }

    /**
     * Set Logged Admin
     */
    private function setLoggedAdmin()
    {
        $this->siteAdminId = 0;
        $this->siteAdmin = [];
        if (AdminAuth::isAdminLogged()) {
            $this->siteAdminId = AdminAuth::getLoggedAdminId();
            $this->siteAdmin = Admin::getAttributesById($this->siteAdminId);
        }
    }

    /**
     * Set Site Language
     */
    private function setSiteLanguage()
    {
        MyUtility::setSystemLanguage();
        if (!empty($_COOKIE['CONF_SITE_LANGUAGE'])) {
            $this->siteLangId = FatUtility::int($_COOKIE['CONF_SITE_LANGUAGE']);
            $this->siteLanguage = Language::getData($this->siteLangId);
            MyUtility::setSiteLanguage($this->siteLanguage);
        } else {
            $this->siteLangId = FatApp::getConfig('CONF_SITE_LANGUAGE');
            $this->siteLanguage = Language::getData($this->siteLangId);
            MyUtility::setSiteLanguage($this->siteLanguage);
        }
    }

    /**
     * Set Site Timezone
     */
    private function setAdminTimezone()
    {
        MyUtility::setSystemTimezone();
        if (!empty($_COOKIE['CONF_ADMIN_TIMEZONE'])) {
            $this->siteTimezone = $_COOKIE['CONF_ADMIN_TIMEZONE'];
            MyUtility::setAdminTimezone($this->siteTimezone);
        } elseif (AdminAuth::isAdminLogged()) {
            $this->siteTimezone = $this->siteAdmin['admin_timezone'] ?? CONF_SERVER_TIMEZONE;
            MyUtility::setAdminTimezone($this->siteTimezone);
        } else {
            $this->siteTimezone = CONF_SERVER_TIMEZONE;
            MyUtility::setAdminTimezone($this->siteTimezone);
        }
    }

    /**
     * Set Site Currency
     */
    private function setSiteCurrency()
    {
        MyUtility::setSystemCurrency();
        if (!empty($_COOKIE['CONF_SITE_CURRENCY'])) {
            $currencyId = FatUtility::int($_COOKIE['CONF_SITE_CURRENCY']);
        } else {
            $currencyId = FatApp::getConfig('CONF_SITE_CURRENCY');
        }
        $this->siteCurrId = FatUtility::int($currencyId);
        $this->siteCurrency = Currency::getData($this->siteCurrId, $this->siteLangId);
        MyUtility::setSiteCurrency($this->siteCurrency);
    }

    /**
     * Get Site Languages
     * 
     * @return type
     */
    private function getSiteLanguages()
    {
        $srch = new SearchBase(Language::DB_TBL);
        $srch->addMultipleFields(['language_id', 'language_code', 'language_name', 'language_direction']);
        $srch->addCondition('language_active', '=', AppConstant::YES);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get JS Variables
     * 
     * @return array
     */
    private function getJsVariables(array $siteLanguages): array
    {
        $jsVariables  = [
            'confirmRemove' => Label::getLabel('LBL_Do_you_want_to_remove'),
            'confirmReset' => Label::getLabel('LBL_Do_you_want_to_reset_settings'),
            'confirmUpdate' => Label::getLabel('LBL_Do_you_want_to_update'),
            'confirmUpdateStatus' => Label::getLabel('LBL_Do_you_want_to_update'),
            'confirmDelete' => Label::getLabel('LBL_Do_you_want_to_delete'),
            'confirmDeleteImage' => Label::getLabel('LBL_Do_you_want_to_delete_image'),
            'layoutDirection' => MyUtility::getLayoutDirection(),
            'invalidRequest' => Label::getLabel('LBL_Invalid_Request!'),
            'DoYouWantTo' => Label::getLabel('LBL_Do_you_really_want_to'),
            'theRequest' => Label::getLabel('LBL_the_request'),
            'confirmReplaceCurrentToDefault' => Label::getLabel('LBL_Do_you_want_to_replace_current_content_to_default_content'),
            'processing' => Label::getLabel('LBL_Processing...'),
            'confirmRestore' => Label::getLabel('LBL_Do_you_want_to_restore'),
            'isMandatory' => Label::getLabel('VLBL_is_mandatory'),
            'pleaseEnterValidEmailId' => Label::getLabel('VLBL_Please_enter_valid_email_ID_for'),
            'charactersSupportedFor' => Label::getLabel('VLBL_Only_characters_are_supported_for'),
            'pleaseEnterIntegerValue' => Label::getLabel('VLBL_Please_enter_integer_value_for'),
            'pleaseEnterNumericValue' => Label::getLabel('VLBL_Please_enter_numeric_value_for'),
            'startWithLetterOnlyAlphanumeric' => Label::getLabel('VLBL_startWithLetterOnlyAlphanumeric'),
            'mustBeBetweenCharacters' => Label::getLabel('VLBL_Length_Must_be_between_6_to_20_characters'),
            'invalidValues' => Label::getLabel('VLBL_Length_Invalid_value_for'),
            'shouldNotBeSameAs' => Label::getLabel('VLBL_should_not_be_same_as'),
            'mustBeSameAs' => Label::getLabel('VLBL_must_be_same_as'),
            'mustBeGreaterOrEqual' => Label::getLabel('VLBL_must_be_greater_than_or_equal_to'),
            'mustBeGreaterThan' => Label::getLabel('VLBL_must_be_greater_than'),
            'mustBeLessOrEqual' => Label::getLabel('VLBL_must_be_less_than_or_equal_to'),
            'mustBeLessThan' => Label::getLabel('VLBL_must_be_less_than'),
            'lengthOf' => Label::getLabel('VLBL_Length_of'),
            'valueOf' => Label::getLabel('VLBL_Value_of'),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between'),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between'),
            'and' => Label::getLabel('VLBL_and'),
            'pleaseSelect' => Label::getLabel('VLBL_Please_select'),
            'to' => Label::getLabel('VLBL_to'),
            'options' => Label::getLabel('VLBL_options'),
            'confirmCancel' => Label::getLabel('LBL_DO_YOU_WANT_TO_CANCEL'),
            'today' => Label::getLabel('LBL_Today'),
            'prev' => Label::getLabel('LBL_Prev'),
            'next' => Label::getLabel('LBL_Next'),
            'done' => Label::getLabel('Done'),
            'confirmActivate' => Label::getLabel('LBL_ARE_YOU_SURE_YOU_WANT_TO_ACTIVATE?'),
            'teachLangUpdNotice' => Label::getLabel('LBL_Teaching_LANGUAGE_UPDATE_NOTICE'),
        ];
        foreach ($siteLanguages as $val) {
            $jsVariables['language' . $val['language_id']] = $val['language_direction'];
        }
        return $jsVariables;
    }
}
