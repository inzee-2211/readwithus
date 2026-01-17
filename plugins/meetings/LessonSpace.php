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
     // 1) Always generate a UNIQUE launch id (avoid collisions on live)
$launchId = $meeting['id'] . '_' . bin2hex(random_bytes(4));

// 2) Always send timeouts in UTC "Z" format (avoid timezone drift)
// Convert using SYSTEM timezone (not PHP default), then to UTC "Z"
$sysTz = new DateTimeZone(MyUtility::getSystemTimezone()); // or CONF_SERVER_TIMEZONE if you use that
$utcTz = new DateTimeZone('UTC');

$startDt = new DateTime($meeting['starttime'], $sysTz);
$endDt   = new DateTime($meeting['endtime'], $sysTz);

$startDt->setTimezone($utcTz);
$endDt->setTimezone($utcTz);

$startUtc = $startDt->format('Y-m-d\TH:i:s\Z');
$endUtc   = $endDt->format('Y-m-d\TH:i:s\Z');
if (strtotime($endUtc) <= strtotime($startUtc)) {
    $this->error = "LessonSpace invalid timeouts: start={$startUtc}, end={$endUtc}";
    return false;
}



$data = [
    "id" => $launchId,
    "user" => [
        'name' => trim($user['user_first_name'] . ' ' . $user['user_last_name']),
        'leader' => ($user['user_type'] == User::TEACHER),
        // 'profile_picture' => $user['user_image'],
    ],
    'timeouts' => [
        "not_before" => $startUtc,
        "not_after"  => $endUtc
    ],
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
        'Accept: application/json',
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postfields),
        'Authorization: Organisation ' . $this->settings['api_key'],
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);  // use true on live
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($curl, CURLOPT_TIMEOUT, 20);

    $curlResult = curl_exec($curl);
    $httpcode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($curlResult === false) {
        $this->error = 'LessonSpace cURL error: ' . curl_error($curl);
        curl_close($curl);
        return false;
    }
    curl_close($curl);

    // Log to PHP error log (check server error log)
    error_log("LessonSpace HTTP={$httpcode} URL={$url} PAYLOAD={$postfields} RESPONSE={$curlResult}");

    $response = json_decode($curlResult, true);

    if (!is_array($response)) {
        $this->error = "LessonSpace non-JSON HTTP {$httpcode}: " . substr((string)$curlResult, 0, 500);
        return false;
    }

    // Catch common LessonSpace error shapes
    if (!empty($response['detail'])) {
        $this->error = "LessonSpace HTTP {$httpcode} detail: " . (is_string($response['detail']) ? $response['detail'] : json_encode($response['detail']));
        return false;
    }
    if (!empty($response['non_field_errors'])) {
        $this->error = "LessonSpace HTTP {$httpcode} non_field_errors: " . json_encode($response['non_field_errors']);
        return false;
    }

    if ($httpcode >= 400) {
        $this->error = "LessonSpace HTTP {$httpcode}: " . json_encode($response);
        return false;
    }

    return $response;
}

}
