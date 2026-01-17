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
     */
    public function createMeeting(array $user, array $meeting)
    {
        // 1) Generate unique launch ID
        $launchId = $meeting['id'] . '_' . bin2hex(random_bytes(8));
        
        // 2) Convert times to ISO 8601 format with proper timezone
        // Use the system timezone as reference
        $systemTimezone = MyUtility::getSystemTimezone();
        
        try {
            $sysTz = new DateTimeZone($systemTimezone);
            $startDt = new DateTime($meeting['starttime'], $sysTz);
            $endDt = new DateTime($meeting['endtime'], $sysTz);
            
            // Format as ISO 8601 with timezone offset (e.g., 2024-01-15T10:30:00+05:30)
            $startUtc = $startDt->format('c'); // 'c' = ISO 8601 date
            $endUtc = $endDt->format('c');
            
            // Validate times
            if ($endDt <= $startDt) {
                $this->error = "Invalid lesson times: End time must be after start time";
                return false;
            }
        } catch (Exception $e) {
            $this->error = "Invalid date format: " . $e->getMessage();
            return false;
        }

        // 3) Prepare user data - email is often required
        $userName = trim($user['user_first_name'] . ' ' . $user['user_last_name']);
        $userEmail = $user['user_email'] ?? '';
        
        // 4) Build payload according to LessonSpace API requirements
        $data = [
            "id" => $launchId,
            "user" => [
                "name" => $userName,
                "email" => $userEmail, // Often required by LessonSpace
                "leader" => ($user['user_type'] == User::TEACHER),
                "external_id" => (string)($user['user_id'] ?? ''),
            ],
            "timeouts" => [
                "not_before" => $startUtc,
                "not_after" => $endUtc,
            ],
            "features" => [
                'invite' => false,
                'fullscreen' => true,
                'endSession' => false,
                'whiteboard.equations' => true,
                'whiteboard.infiniteToggle' => true,
                'chat.private' => true,
                'chat.public' => true
            ],
            "metadata" => [
                "lesson_id" => $meeting['id'],
                "user_id" => $user['user_id'] ?? '',
                "user_type" => $user['user_type'] ?? '',
            ]
        ];

        $url = static::BASE_URL . 'spaces/launch/';
        if (!$response = $this->exeCurlRequest($url, $data)) {
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
        return $this->meeting['client_url'] ?? '';
    }

    public function getAppUrl(): string
    {
        return $this->meeting['client_url'] ?? '';
    }

    /**
     * End Meeting
     */
    public function endMeeting(array $meeting): bool
    {
        // Implement if LessonSpace API supports ending meetings
        return true;
    }

    public function getFreeMeetingDuration(): int
    {
        return -1;
    }

    public function getLicensedCount(): int
    {
        return -1;
    }

    /**
     * Execute Curl Request with detailed debugging
     */
    private function exeCurlRequest(string $url, array $params)
    {
        $postfields = json_encode($params, JSON_PRETTY_PRINT);
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Organisation ' . $this->settings['api_key'],
        ];

        // Log request for debugging
        error_log("=== LessonSpace API Request ===");
        error_log("URL: " . $url);
        error_log("Headers: " . json_encode($headers));
        error_log("Payload: " . $postfields);
        
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FAILONERROR => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);

        $curlResult = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        error_log("=== LessonSpace API Response ===");
        error_log("HTTP Code: " . $httpcode);
        error_log("Response: " . $curlResult);
        error_log("cURL Error: " . $error);
        
        curl_close($curl);

        if ($curlResult === false) {
            $this->error = 'LessonSpace cURL error: ' . $error;
            return false;
        }

        $response = json_decode($curlResult, true);
        
        if (!is_array($response)) {
            $this->error = "LessonSpace invalid JSON response. HTTP {$httpcode}: " . substr((string)$curlResult, 0, 500);
            return false;
        }

        // Check for API-specific error formats
        if (!empty($response['detail'])) {
            $this->error = "LessonSpace HTTP {$httpcode} - " . (is_string($response['detail']) ? $response['detail'] : json_encode($response['detail']));
            return false;
        }
        
        if (!empty($response['non_field_errors'])) {
            $this->error = "LessonSpace HTTP {$httpcode} - " . (is_array($response['non_field_errors']) ? implode(', ', $response['non_field_errors']) : $response['non_field_errors']);
            return false;
        }
        
        if (!empty($response['error'])) {
            $this->error = "LessonSpace HTTP {$httpcode} - " . (is_string($response['error']) ? $response['error'] : json_encode($response['error']));
            return false;
        }

        if ($httpcode >= 400) {
            $this->error = "LessonSpace HTTP {$httpcode}: " . json_encode($response);
            return false;
        }

        // Success - ensure we have required fields
        if (empty($response['client_url']) && !empty($response['url'])) {
            $response['client_url'] = $response['url'];
        }

        return $response;
    }
}