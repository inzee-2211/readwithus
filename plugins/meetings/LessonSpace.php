<?php

class LessonSpace extends FatModel
{

    private $tool;
    private $settings;
    private $meeting;

    const BASE_URL = "https://api.thelessonspace.com/v2/";

    public function __construct()
    {
        $this->tool = [];
        $this->settings = [];
        $this->meeting = [];
        parent::__construct();
    }

    /**
     * Initialize Meeting Tool
     * 1. Load Meeting Tool
     * 2. Format Meeting Tool Settings
     * 3. Validate Meeting Tool Settings
     * 
     * @return bool
     */
    public function initMeetingTool(): bool
    {
        /* Load Meeting Tool */
        $this->tool = MeetingTool::getByCode(MeetingTool::LESSON_SPACE);
        if (empty($this->tool)) {
            $this->error = Label::getLabel('LBL_LESSON_SPACE_NOT_FOUND');
            return false;
        }
        /* Format Meeting Tool Settings */
        $settings = json_decode($this->tool['metool_settings'], 1) ?? [];
        $this->settings = array_column($settings, 'value', 'key');
        /* Validate Meeting Tool Settings */
        if (empty($this->settings['api_key'])) {
            $this->error = Label::getLabel("MSG_LESSON_SPACE_NOT_CONFIGURED");
            return false;
        }
        return true;
    }

    /**
     * Create Meeting on LessonSpace
     * 
     * @param array $user = []
     * @param array $meeting = ['title', 'duration', 'starttime', 'endtime', 'timezone']
     */
    public function createMeeting(array $user, array $meeting)
    {
        $userTimezoneOffset = MyDate::getOffset($user['user_timezone']);
        $starttime = strtotime(MyDate::formatDate($meeting['starttime'], 'Y-m-d H:i:s', $user['user_timezone']));
        $endtime = strtotime(MyDate::formatDate($meeting['endtime'], 'Y-m-d H:i:s', $user['user_timezone']));
        $unixStarttime = date('Y-m-d', $starttime) . 'T' . date('H:i:s', $starttime) . $userTimezoneOffset;
        $unixEndtime = date('Y-m-d', $endtime) . 'T' . date('H:i:s', $endtime) . $userTimezoneOffset;
        $data = [
            "id" => $meeting['id'],
            "user" => [
                'name' => $user['user_first_name'] . ' ' . $user['user_last_name'],
                'leader' => ($user['user_type'] == User::TEACHER),
                // 'profile_picture' => $user['user_image'],
            ],
            'timeouts' => ["not_before" => $unixStarttime, "not_after" => $unixEndtime],
            "features" => [
                'invite' => false,
                'fullscreen' => true,
                'endSession' => false,
                'whiteboard.equations' => true,
                'whiteboard.infiniteToggle' => true
            ]
        ];
        $url = static::BASE_URL . 'spaces/launch/';
        if (!$response = $this->exeCurlRequest($url, $data)) {
            return false;
        }
      if (empty($response['client_url'])) {
    // keep actual API error if we already captured it in exeCurlRequest()
    $this->error = $this->error ?: ('LessonSpace missing client_url. Raw: ' . json_encode($response));
    return false;
}

        return $this->meeting = $response;
    }

    public function getJoinUrl(): string
    {
        return $this->meeting['client_url'];
    }

    public function getAppUrl(): string
    {
        return $this->meeting['client_url'];
    }

    /**
     * End Meeting
     * 
     * @param array $meeting
     * @return bool
     */
    public function endMeeting(array $meeting): bool
    {
        return true;
    }


    public function getFreeMeetingDuration() : int
    {
        return -1;
    }

    public function getLicensedCount(): int
    {
        return -1;
    }

    /**
     * Execute Curl Request
     *
     * @param string $url
     * @param array $params
     * @return boolean
     */
    private function exeCurlRequest(string $url, array $params)
    {
        $postfields = json_encode($params);
        $headers = [
            'Accept', 'application/json',
            'Content-type: application/json',
            'Content-length: ' . strlen($postfields),
            'Authorization: Organisation ' . $this->settings['api_key']
        ];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        $curlResult = curl_exec($curl);
        if (curl_errno($curl)) {
            $this->error = 'Error:' . curl_error($curl);
            return false;
        }
        curl_close($curl);
        // $response = json_decode($curlResult, true) ?? [];
        // if (empty($response) || !empty($response['detail'])) {
        //     $this->error = Label::getLabel('LBL_CONTACT_WITH_ADMIN_ISSUE_WITH_MEETING_TOOL');
        //     return false;
        // }
        // return $response;
        $response = json_decode($curlResult, true);

if (!is_array($response)) {
    // Not JSON => show first part of raw response for debugging
    $this->error = 'LessonSpace raw response: ' . substr((string)$curlResult, 0, 300);
    return false;
}

if (!empty($response['detail'])) {
    // LessonSpace returns useful detail, show it
    $this->error = 'LessonSpace error: ' . (is_string($response['detail']) ? $response['detail'] : json_encode($response['detail']));
    return false;
}

if (empty($response)) {
    $this->error = 'LessonSpace empty response';
    return false;
}

return $response;

    }
}
