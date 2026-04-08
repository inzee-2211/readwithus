<?php

use Google\Service\Calendar;
use Google\Service\Calendar\Event;

/**
 * A Common Google Calendar Utility  
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class GoogleCalendar extends Google
{

    private $redirect = null;

    /**
     * Initialize Google Calendar
     * 
     * @param int $userId
     */
    public function __construct(int $userId = 0)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    /**
     * Authorize
     * 
     * @param string $code
     * @return bool
     */
    public function authorize(string $code = null): bool
    {
        try {
            if (!$this->getClient()) {
                return false;
            }
            $this->client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . MyUtility::getSiteLangId()));
            $this->client->setScopes([Calendar::CALENDAR, Calendar::CALENDAR_EVENTS]);
            $this->client->setAccessType("offline");
            $this->client->setApprovalPrompt("force");
            $this->client->setRedirectUri(MyUtility::makeFullUrl('Account', 'GoogleCalendarAuthorize', [], CONF_WEBROOT_DASHBOARD));
            if (empty($code)) {
                $this->redirect = $this->client->createAuthUrl();
                return true;
            }
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            if (array_key_exists('error', $accessToken)) {
                $this->error = Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN_LATER');
                $this->redirect = MyUtility::makeUrl('Account', 'ProfileInfo', [], CONF_WEBROOT_DASHBOARD);
                return false;
            }
            $this->client->setAccessToken($accessToken);
        } catch (Exception $exc) {
            $this->error = $exc->getMessage();
            return false;
        }
        $userSetting = new UserSetting($this->userId);
        if (!$userSetting->saveData(['user_google_token' => json_encode($this->client->getAccessToken())])) {
            $this->error = $userSetting->getError();
            return false;
        }
        $this->redirect = MyUtility::makeUrl('Account', 'ProfileInfo', [], CONF_WEBROOT_DASHBOARD);
        return true;
    }

    /**
     * Get Redirect URL
     * 
     * @return type
     */
    public function getRedirectUrl()
    {
        return $this->redirect;
    }

    /**
     * Add Event
     * 
     * @param array $data
     * @return bool
     */
    public function addEvent(array $data)
    {
        if (!$token = $this->getUserToken($data['google_token'] ?? '')) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        unset($data['google_token']);
        try {
            if (!$client = $this->getClient()) {
                return false;
            }
            $client->refreshToken($token);
            $service = new Calendar($client);
            $event = $service->events->insert('primary', new Event($data));
        } catch (\Throwable $th) {
            $this->error = $th->getMessage();
            return false;
        }
        return $event->id;
    }

    /**
     * Update Event
     * 
     * @param string $eventId
     * @param array $data
     * @return bool
     */
    public function updateEvent(string $eventId, array $data): bool
    {
        if (!$token = $this->getUserToken($data['google_token'] ?? '')) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        unset($data['google_token']);
        try {
            if (!$client = $this->getClient()) {
                return false;
            }
            $client->refreshToken($token);
            $service = new Calendar($client);
            $event = $service->events->update('primary', $eventId, new Event($data));
        } catch (\Throwable $th) {
            $this->error = $th->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Delete Event
     * 
     * @param string $eventId
     * @param string $token
     * @return bool
     */
    public function deleteEvent(string $eventId, string $token): bool
    {
        if (!$token = $this->getUserToken($token ?? '')) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        try {
            if (!$client = $this->getClient()) {
                return false;
            }
            $client->refreshToken($token);
            $service = new Calendar($client);
            $event = $service->events->delete('primary', $eventId);
        } catch (\Throwable $th) {
            $this->error = $th->getMessage();
            return false;
        }
        return true;
    }

}
