<?php

use Google\Service\Analytics;

/**
 * A Common Google Analytic Utility  
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class GoogleAnalytics extends Google
{

    private $redirect = null;

    /**
     * Authorize Google
     * 
     * @param string $code
     * @return boolean
     */
    public function authorize(string $code = null)
    {
        try {
            if (!$this->getClient()) {
                return false;
            }
            $this->client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . MyUtility::getSiteLangId()));
            $this->client->setScopes([Analytics::ANALYTICS_READONLY]);
            $this->client->setAccessType("offline");
            $this->client->setApprovalPrompt("force");
            $this->client->setRedirectUri(MyUtility::makeFullUrl('Configurations', 'googleAuthorize', [], CONF_WEBROOT_BACKEND));
            if (empty($code)) {
                $this->redirect = $this->client->createAuthUrl();
                return true;
            }
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            if (array_key_exists('error', $accessToken)) {
                $this->error = Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN_LATER');
                $this->redirect = MyUtility::makeUrl('Configurations', 'index', [], CONF_WEBROOT_BACKEND) . '?tab=' . Configurations::FORM_THIRD_PARTY;
                return false;
            }
            $this->client->setAccessToken($accessToken);
        } catch (Exception $exc) {
            $this->error = $exc->getMessage();
            return false;
        }
        $configurations = new Configurations();
        if (!$configurations->updateConf('ANALYTICS_GOOGLE_TOKEN', json_encode($this->client->getAccessToken()))) {
            $this->error = $configurations->getError();
            return false;
        }
        $this->redirect = MyUtility::makeUrl('Configurations', 'index', [], CONF_WEBROOT_BACKEND) . '?tab=' . Configurations::FORM_THIRD_PARTY;
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->redirect;
    }

    /**
     * Undocumented function
     *
     * 
     * Relative dates are always relative to the current date at the time of the query and 
     * are based on the timezone of the view (profile) specified in the query.
     * Ref : https://developers.google.com/analytics/devguides/reporting/core/v3/reference#ids
     * 
     * https://analytics.google.com/analytics/web/#/{SomeGoogleId}/admin/view/settings
     * 
     * @return void
     */
    public function getSessions(string $startDate = '', string $endDate = '')
    {
        $startDate = (empty($startDate)) ? '7daysAgo' : $startDate;
        $endDate = (empty($endDate)) ? 'today' : $endDate;
        $tableId = FatApp::getConfig('CONF_ANALYTICS_TABLE_ID', FatUtility::VAR_STRING, '');
        if (empty($tableId)) {
            return false;
        }
        if (empty($accessToken = $this->getAnalyticsToken())) {
            return false;
        }
        try {
            $this->client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . MyUtility::getSiteLangId(), FatUtility::VAR_STRING, ''));
            $this->client->setScopes([Analytics::ANALYTICS_READONLY]);
            $this->client->setAccessToken($accessToken);
            $analytics = new Analytics($this->client);
            $data = $analytics->data_ga->get('ga:' . $tableId, $startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:date']);
            $result = $data->getRows();

            $data = [];
            foreach ($result as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $dateString = substr($value[0], 0, 4) . '-' . substr($value[0], 4, 2) . '-' . substr($value[0], 6, 2);
                $data[] = [
                    $dateString, $value[1]
                ];
            }
            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get Organic Searches
     * 
     * @param string $startDate
     * @param string $endDate
     * @return boolean
     */
    public function getOrganicSearches(string $startDate = '', string $endDate = '')
    {
        $startDate = (empty($startDate)) ? '7daysAgo' : $startDate;
        $endDate = (empty($endDate)) ? 'today' : $endDate;
        $tableId = FatApp::getConfig('CONF_ANALYTICS_TABLE_ID', FatUtility::VAR_STRING, '');
        if (empty($tableId)) {
            return false;
        }
        if (empty($accessToken = $this->getAnalyticsToken())) {
            return false;
        }
        try {
            $this->client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . MyUtility::getSiteLangId(), FatUtility::VAR_STRING, ''));
            $this->client->setScopes([Analytics::ANALYTICS_READONLY]);
            $this->client->setAccessToken($accessToken);
            $analytics = new Analytics($this->client);
            $data = $analytics->data_ga->get('ga:' . $tableId, $startDate, $endDate, 'ga:organicSearches', ['dimensions' => 'ga:source']);
            return $data->getRows() ?? [];
        } catch (\Throwable $th) {
            return false;
        }
    }

}
