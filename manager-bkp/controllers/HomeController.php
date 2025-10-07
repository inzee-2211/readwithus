<?php

/**
 * Home Controller is used for handling Basic actions
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class HomeController extends AdminBaseController
{

    /**
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewAdminDashboard();
    }

    /**
     * Render Dashboard 
     */
    public function index()
    {
        $this->_template->addJs(['js/chartist.min.js', 'js/jquery.counterup.js', 'js/slick.min.js']);
        $this->_template->addCss(['css/chartist.css']);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) {
            $this->_template->addCss('css/ie.css');
        }
        $datetime = FatApp::getConfig('CONF_SALES_REPORT_GENERATED_DATE');
        $regendatedtime = str_replace('{datetime}', $datetime . ' (UTC)', Label::getLabel('LBL_REPORT_GENERATED_ON_{datetime}'));
        $this->sets([
            'objPrivilege' => $this->objPrivilege,
            'stats' => AdminStatistic::getDashboardStats(),
            'regendatedtime' => $regendatedtime,
            'analyticsToken' => (new GoogleAnalytics())->getAnalyticsToken(),
        ]);
        $this->_template->render();
    }

    /**
     * Dashboard Stat Chart
     */
    public function dashboardStatChart()
    {
        $userData = array_column(AdminStatistic::getUsersStat(MyDate::TYPE_LAST_12_MONTH), 'totalUser', 'groupDate');
        $lessonData = array_column(AdminStatistic::getAdminLessonEarningStats(MyDate::TYPE_LAST_12_MONTH), 'les_earnings', 'groupDate');
        $classData = array_column(AdminStatistic::getAdminClassEarningStats(MyDate::TYPE_LAST_12_MONTH), 'cls_earnings', 'groupDate');
        $courseData = array_column(AdminStatistic::getAdminCourseEarningStats(MyDate::TYPE_LAST_12_MONTH), 'crs_earnings', 'groupDate');
        FatUtility::dieJsonSuccess([
            'userData' => $userData,
            'lessonData' => $lessonData,
            'classData' => $classData,
            'courseData' => $courseData
        ]);
    }

    /**
     * Dashboard Stats
     */
    public function topClassLanguages()
    {
        $interval = FatApp::getPostedData('interval', FatUtility::VAR_INT, MyDate::TYPE_ALL);
        $interval = (!array_key_exists($interval, MyDate::getDurationTypesArr())) ? MyDate::TYPE_ALL : $interval;
        $this->set('statsInfo', AdminStatistic::classTopLanguage($this->siteLangId, $interval, 50));
        $this->_template->render(false, false);
    }

    /**
     * Dashboard Stats
     */
    public function topLessonLanguages()
    {
        $interval = FatApp::getPostedData('interval', FatUtility::VAR_INT, MyDate::TYPE_ALL);
        $interval = (!array_key_exists($interval, MyDate::getDurationTypesArr())) ? MyDate::TYPE_ALL : $interval;
        $this->set('statsInfo', AdminStatistic::lessonTopLanguage($this->siteLangId, $interval, 50));
        $this->_template->render(false, false);
    }

    /**
     * Dashboard Stats
     */
    public function topCourseCategories()
    {
        $interval = FatApp::getPostedData('interval', FatUtility::VAR_INT, MyDate::TYPE_ALL);
        $interval = (!array_key_exists($interval, MyDate::getDurationTypesArr())) ? MyDate::TYPE_ALL : $interval;
        $this->set('statsInfo', AdminStatistic::courseTopCategories($this->siteLangId, $interval, 50));
        $this->_template->render(false, false);
    }

    /**
     * Get Google Analytic
     */
    public function getGoogleAnalytics()
    {
        $analytics = new GoogleAnalytics();
        $sessionData = $analytics->getSessions();
        $response = ['session' => [], 'organicSearches' => [], 'sessionErrorMsg' => '', 'organicSearchesErrorMsg' => ''];
        if ($sessionData === false) {
            $response['sessionErrorMsg'] = Label::getLabel('LBL_GOOGLE_ANALYTICS_CONFIGURATION_ERROR_MSG');
        } else {
            $data = [[Label::getLabel('LBL_DATE'), Label::getLabel('LBL_SESSION')]];
            $response['session'] = array_merge($data, $sessionData ?? []);
        }
        $organicSearches = $analytics->getOrganicSearches();
        if ($organicSearches === false) {
            $response['organicSearchesErrorMsg'] = Label::getLabel('LBL_GOOGLE_ANALYTICS_CONFIGURATION_ERROR_MSG');
        } else {
            $data = [[Label::getLabel('LBL_SOURCE'), Label::getLabel('LBL_VISTORS')]];
            $response['organicSearches'] = array_merge($data, $organicSearches ?? []);
        }
        FatUtility::dieJsonSuccess($response);
    }

    /**
     * Clear Cache
     */
    public function clearCache()
    {
        FatCache::clearAll();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CACHE_HAS_BEEN_CLEARED'));
    }

    /**
     * Set Admin Language
     * 
     * @param int $langId
     */
    public function setLanguage($langId = 0)
    {
        FatCache::clearAll();
        $langId = FatUtility::int($langId);
        if ($langId > 0) {
            $language = Language::getData($langId);
            if (empty($language)) {
                FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
            }
            MyUtility::setCookie('CONF_SITE_LANGUAGE', $langId);
            FatUtility::dieJsonSuccess(Label::getLabel('MSG_LANGUAGE_UPDATE_SUCCESSFULLY'));
        }
        FatUtility::dieJsonError(Label::getLabel('MSG_PLEASE_SELECT_ANY_LANGUAGE'));
    }

}
