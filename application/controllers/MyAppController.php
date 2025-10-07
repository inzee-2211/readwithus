<?php

/**
 * MyApp Controller is a Base Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class MyAppController extends FatController
{

    protected $siteUser;
    protected $siteUserId;
    protected $siteUserType;
    protected $siteLangId;
    protected $siteLanguage;
    protected $siteCurrId;
    protected $siteCurrency;
    protected $siteTimezone;
    protected $cookieConsent;

    /**
     * Initialize Application
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->checkMaintenance();
        $this->setLoggedUser();
        $this->setSiteLanguage();
        $this->setSiteCurrency();
        $this->setSiteTimezone();
        $this->setCookieConsent();
        $siteLanguages = $this->getSiteLanguages();
        $this->sets([
            'siteUser' => $this->siteUser,
            'siteUserId' => $this->siteUserId,
            'siteUserType' => $this->siteUserType,
            'siteLangId' => $this->siteLangId,
            'siteLanguage' => $this->siteLanguage,
            'siteCurrId' => $this->siteCurrId,
            'siteCurrency' => $this->siteCurrency,
            'siteTimezone' => $this->siteTimezone,
            'siteLanguages' => $siteLanguages,
            'siteCurrencies' => $this->getSiteCurrencies(),
            'cookieConsent' => $this->cookieConsent,
            'messageData' => Message::getData(),
        ]);
        $this->set('actionName', $this->_actionName);
        $controllerName = str_replace('Controller', '', $this->_controllerName);
        $this->set('controllerName', $controllerName);
        $this->set('teachLangs', TeachLanguage::getTeachLanguages($this->siteLangId));
        if (!FatUtility::isAjaxCall()) {
            $this->set('canonicalUrl', SeoUrl::getCanonicalUrl());
            $this->set('headerNav', Navigation::getHeaderNav());
            $this->set('footerOneNav', Navigation::footerOneNav());
            $this->set('footerTwoNav', Navigation::footerTwoNav());
            $this->set('footerThreeNav', Navigation::footerThreeNav());
            $this->set('socialPlatforms', SocialPlatform::getAll());
            $this->set('jsVariables', MyUtility::getCommonLabels($siteLanguages));

            $viewType = 'frontend';
            if (CONF_APPLICATION_PATH == CONF_INSTALLATION_PATH . 'dashboard/') {
                $viewType = 'dashboard';
                if (strtolower($controllerName) == 'tutorials' || strtolower($controllerName) == 'coursepreview') {
                    $viewType = 'course-personal';
                }
            }
            $this->_template->addCss([
                'css/common-' . $this->siteLanguage['language_direction'] . '.css',
                'css/' . $viewType . '-' . $this->siteLanguage['language_direction'] . '.css'
            ]);
        }
    }

    /**
     * Check System Maintenance Mode
     * 
     * @return boolean
     */
    private function checkMaintenance()
    {
        if (FatApp::getConfig("CONF_MAINTENANCE") == AppConstant::NO) {
            return true;
        }
        if (
                ($this->_controllerName == "MaintenanceController") ||
                ($this->_controllerName == 'CookieConsentController' &&
                in_array($this->_actionName, ['form', 'acceptAll', 'setup', 'setSiteLanguage']))
        ) {
            return true;
        }
        UserAuth::logout();
        if (FatUtility::isAjaxCall()) {
            FatUtility::dieJsonError(Label::getLabel('MSG_MAINTENANCE_MODE_TEXT'));
        }
        FatApp::redirectUser(MyUtility::makeUrl('maintenance'));
    }

    /**
     * Set Site Logged User
     * 
     * @return bool
     */
    private function setLoggedUser()
    {
        $this->siteUserType = User::LEARNER;
        $this->siteUser = User::getDetail(UserAuth::getLoggedUserId());
        if (empty($this->siteUser)) {
            UserAuth::logout();
            $this->siteUser = [];
            $this->siteUserId = 0;
            return true;
        }
        $this->siteUserId = FatUtility::int($this->siteUser['user_id']);
        if ($this->siteUser['user_is_teacher'] == AppConstant::YES) {
            $this->siteUserType = User::TEACHER;
            $this->siteUser['profile_progress'] = User::getProfileProgress($this->siteUserId);
        }
        if (!empty(MyUtility::getUserType())) {
            $this->siteUserType = MyUtility::getUserType();
        }
        MyUtility::setUserType($this->siteUserType);
        if (empty($this->siteUser['user_email']) && !in_array($this->_actionName,
                        ['configureEmail', 'updateEmail', 'verifyEmail', 'logout', 'setSiteLanguage'])) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError(Label::getLabel('MSG_PLEASE_CONFIGURE_YOUR_EMAIL'));
            }
            Message::addErrorMessage(Label::getLabel('MSG_PLEASE_CONFIGURE_YOUR_EMAIL'));
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'configureEmail', [], CONF_WEBROOT_FRONT_URL));
        }
        return true;
    }

    /**
     * Set Site Language
     * 
     * @return bool
     */
    private function setSiteLanguage()
    {
        MyUtility::setSystemLanguage();
        if (defined('CONF_SITE_LANGUAGE')) {
            $this->siteLangId = CONF_SITE_LANGUAGE;
            $this->siteLanguage = Language::getData($this->siteLangId);
            MyUtility::setSiteLanguage($this->siteLanguage, true);
            return true;
        }
        if (!empty($_COOKIE['CONF_SITE_LANGUAGE'])) {
            $this->siteLangId = FatUtility::int($_COOKIE['CONF_SITE_LANGUAGE']);
            $this->siteLanguage = Language::getData($this->siteLangId);
            MyUtility::setSiteLanguage($this->siteLanguage);
            return true;
        }
        $langId = User::getAttributesById($this->siteUserId, 'user_lang_id');
        if (!empty($langId)) {
            $this->siteLangId = FatUtility::int($langId);
            $this->siteLanguage = Language::getData($this->siteLangId);
            MyUtility::setSiteLanguage($this->siteLanguage);
            return true;
        }
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langCode = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            $langId = array_search($langCode, Language::getCodes());
            if (!empty($langId)) {
                $this->siteLangId = FatUtility::int($langId);
                $this->siteLanguage = Language::getData($this->siteLangId);
                MyUtility::setSiteLanguage($this->siteLanguage);
                return true;
            }
        }
        $this->siteLangId = FatApp::getConfig('CONF_SITE_LANGUAGE');
        $this->siteLanguage = Language::getData($this->siteLangId);
        MyUtility::setSiteLanguage($this->siteLanguage);
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
     * Set Site Timezone
     * 
     * @return boolean
     */
    private function setSiteTimezone()
    {
        MyUtility::setSystemTimezone();
        if (!empty($_COOKIE['CONF_SITE_TIMEZONE'])) {
            $this->siteTimezone = $_COOKIE['CONF_SITE_TIMEZONE'];
            MyUtility::setSiteTimezone($this->siteTimezone);
            return true;
        }
        if (!empty($this->siteUser['user_timezone'])) {
            $this->siteTimezone = $this->siteUser['user_timezone'];
            MyUtility::setSiteTimezone($this->siteTimezone);
            return true;
        }
        $this->siteTimezone = MyUtility::getSystemTimezone();
    }

    /**
     * Set Cookie Consent
     * 
     * @return boolean
     */
    private function setCookieConsent()
    {
        if (!empty($_COOKIE['CONF_SITE_CONSENTS'])) {
            $this->cookieConsent = json_decode($_COOKIE['CONF_SITE_CONSENTS'], true);
            MyUtility::setCookieConsents($this->cookieConsent);
            return true;
        }
        if (!empty($this->siteUserId)) {
            $cookieConsent = CookieConsent::getSettings($this->siteUserId);
            if (!empty($cookieConsent)) {
                $this->cookieConsent = json_decode($cookieConsent, true);
                MyUtility::setCookieConsents($this->cookieConsent);
                return true;
            }
        }
        return true;
    }

    /**
     * Get Site Languages
     * 
     * @return array
     */
    private function getSiteLanguages(): array
    {
        $srch = new SearchBase(Language::DB_TBL);
        $srch->addMultipleFields(['language_id', 'language_code', 'language_direction', 'language_name']);
        $srch->addCondition('language_active', '=', AppConstant::YES);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get Site Currencies
     * 
     * @return array
     */
    private function getSiteCurrencies(): array
    {
        $srch = new SearchBase(Currency::DB_TBL, 'currency');
        $srch->joinTable(Currency::DB_TBL_LANG, 'LEFT JOIN', 'curlang.currencylang_currency_id = '
                . 'currency.currency_id AND curlang.currencylang_lang_id = ' . $this->siteLangId, 'curlang');
        $srch->addCondition('currency.currency_active', '=', AppConstant::YES);
        $srch->addMultipleFields(['currency_id', 'currency_code', 'currency_name']);
        $srch->addOrder('currency_order');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Catch All undefined actions
     * 
     * @param string $action
     */
    public function fatActionCatchAll(string $action)
    {
        $this->_template->render(false, false, 'error-pages/404.php');
    }

}
