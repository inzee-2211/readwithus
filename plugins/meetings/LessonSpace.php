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
    // --- 1) Build a SAFE unique launch id (avoid collisions + avoid overlong ids) ---
    $baseId = (string)($meeting['id'] ?? '');
    if ($baseId === '') {
        $this->error = "LessonSpace missing meeting[id]";
        return false;
    }

    $launchId = $baseId . '_' . bin2hex(random_bytes(4));

    // If LessonSpace has an internal max length, keep it safe.
    // (64 is a common limit; if your logs show otherwise you can adjust)
    if (strlen($launchId) > 64) {
        $launchId = substr(hash('sha256', $launchId), 0, 64);
    }

    // --- 2) Format timeouts in ISO8601 WITH OFFSET (closest to your original working code) ---
    // Use the user's timezone (like before) because LessonSpace used to receive that.
    $userTz = !empty($user['user_timezone']) ? $user['user_timezone'] : MyUtility::getSystemTimezone();

    try {
        $tz = new DateTimeZone($userTz);

        // meeting['starttime'] / ['endtime'] are strings like "Y-m-d H:i:s"
        // Treat them as already in USER timezone (this matches your original intent).
        $startDt = new DateTime($meeting['starttime'], $tz);
        $endDt   = new DateTime($meeting['endtime'], $tz);

        // ISO8601 with offset: 2026-01-17T19:30:00+05:00
        $notBefore = $startDt->format('Y-m-d\TH:i:sP');
        $notAfter  = $endDt->format('Y-m-d\TH:i:sP');

        if ($endDt->getTimestamp() <= $startDt->getTimestamp()) {
            $this->error = "LessonSpace invalid timeouts (end <= start): not_before={$notBefore}, not_after={$notAfter}";
            return false;
        }
    } catch (Exception $e) {
        $this->error = "LessonSpace timezone/DateTime error: " . $e->getMessage();
        return false;
    }

    // --- 3) Restore payload structure close to original (features + profile_picture) ---
    $fullName = trim(($user['user_first_name'] ?? '') . ' ' . ($user['user_last_name'] ?? ''));
    if ($fullName === '') {
        $fullName = 'User';
    }

    $data = [
        "id" => $launchId,
        "user" => [
            "name" => $fullName,
            "leader" => ((int)($user['user_type'] ?? 0) === User::TEACHER),
            "profile_picture" => $user['user_image'] ?? null,

            // These two are harmless if LessonSpace ignores them, helpful if required:
            "email" => $user['user_email'] ?? null,
            "id"    => (string)($user['user_id'] ?? ''),
        ],
        "timeouts" => [
            "not_before" => $notBefore,
            "not_after"  => $notAfter,
        ],
        "features" => [
            'invite' => false,
            'fullscreen' => true,
            'endSession' => false,
            'whiteboard.equations' => true,
            'whiteboard.infiniteToggle' => true
        ],
    ];

    $url = static::BASE_URL . 'spaces/launch/';
    $response = $this->exeCurlRequest($url, $data);
    if (!$response) {
        return false;
    }

    if (empty($response['client_url'])) {
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
        $response = json_decode($curlResult, true) ?? [];
        if (empty($response) || !empty($response['detail'])) {
            $this->error = Label::getLabel('LBL_CONTACT_WITH_ADMIN_ISSUE_WITH_MEETING_TOOL');
            return false;
        }
        return $response;
    }
}
