<?php

class LessonSpace extends FatModel
{
    private $tool;
    private $settings;
    private $meeting;

    const BASE_URL = "https://api.thelessonspace.com/v2/";

    // allow join a bit earlier/later
    const JOIN_BUFFER_BEFORE = 900;  // 15 min
    const JOIN_BUFFER_AFTER  = 1800; // 30 min

    public function __construct()
    {
        $this->tool = [];
        $this->settings = [];
        $this->meeting = [];
        parent::__construct();
    }

    public function initMeetingTool(): bool
    {
        $this->tool = MeetingTool::getByCode(MeetingTool::LESSON_SPACE);
        if (empty($this->tool)) {
            $this->error = Label::getLabel('LBL_LESSON_SPACE_NOT_FOUND');
            return false;
        }

        $settings = json_decode($this->tool['metool_settings'], true) ?? [];
        $this->settings = array_column($settings, 'value', 'key');

        if (empty($this->settings['api_key'])) {
            $this->error = Label::getLabel("MSG_LESSON_SPACE_NOT_CONFIGURED");
            return false;
        }

        return true;
    }

    public function createMeeting(array $user, array $meeting)
    {
        // IMPORTANT: stable space id => everyone joins the SAME meeting
        $spaceId = (string)($meeting['id'] ?? '');
        if ($spaceId === '') {
            $this->error = "LessonSpace missing meeting[id]";
            return false;
        }

        $fullName = trim((string)($user['user_first_name'] ?? '') . ' ' . (string)($user['user_last_name'] ?? ''));
        if ($fullName === '') {
            $fullName = 'User';
        }

        $isTeacher = ((int)($user['user_type'] ?? 0) === User::TEACHER);

        $email      = trim((string)($user['user_email'] ?? ''));
        $profilePic = trim((string)($user['user_image'] ?? ''));

        // Lesson start/end are in system timezone in YoCoach
        $sysTz = (string)MyUtility::getSystemTimezone();
        try {
            $start = new DateTime((string)$meeting['starttime'], new DateTimeZone($sysTz));
            $end   = new DateTime((string)$meeting['endtime'], new DateTimeZone($sysTz));
        } catch (Exception $e) {
            $this->error = "LessonSpace bad start/end time: " . $e->getMessage();
            return false;
        }

        // Buffer join window
        $notBeforeUnix = $start->getTimestamp() - self::JOIN_BUFFER_BEFORE;
        $notAfterUnix  = $end->getTimestamp() + self::JOIN_BUFFER_AFTER;

        // Convert to UTC Z format (safe)
        $notBefore = gmdate('Y-m-d\TH:i:s\Z', $notBeforeUnix);
        $notAfter  = gmdate('Y-m-d\TH:i:s\Z', $notAfterUnix);

        $payload = [
            "id" => $spaceId,
            "user" => [
                "name" => $fullName,
                "leader" => $isTeacher,
            ],
            "timeouts" => [
                "not_before" => $notBefore,
                "not_after" => $notAfter,
            ],
            "features" => [
                "invite" => false,
                "fullscreen" => true,
                "endSession" => false,
                "whiteboard.equations" => true,
                "whiteboard.infiniteToggle" => true,
            ],
        ];

        if ($email !== '') {
            $payload['user']['email'] = $email;
        }

        // only include profile picture if it's public http(s)
        if ($profilePic !== '' && preg_match('~^https?://~i', $profilePic)) {
            $payload['user']['profile_picture'] = $profilePic;
        }

        $url = static::BASE_URL . 'spaces/launch/';
        $response = $this->exeCurlRequest($url, $payload);

        if ($response === false) {
            return false;
        }

        if (empty($response['client_url'])) {
            $this->error = 'LessonSpace missing client_url. Raw: ' . json_encode($response);
            return false;
        }

        return $this->meeting = $response;
    }

    public function getJoinUrl(): string
    {
        return (string)($this->meeting['client_url'] ?? '');
    }

    public function getAppUrl(): string
    {
        return (string)($this->meeting['client_url'] ?? '');
    }

    public function endMeeting(array $meeting): bool
    {
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

    private function exeCurlRequest(string $url, array $params)
    {
        $postfields = json_encode($params);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postfields),
            // IMPORTANT: LessonSpace expects "Organisation" (UK spelling)
            'Authorization: Organisation ' . $this->settings['api_key'],
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        $curlResult = curl_exec($curl);
        $httpcode   = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($curlResult === false) {
            $this->error = 'LessonSpace cURL error: ' . curl_error($curl);
            curl_close($curl);
            return false;
        }
        curl_close($curl);

        $response = json_decode($curlResult, true);

        if (!is_array($response)) {
            $this->error = "LessonSpace non-JSON HTTP {$httpcode}: " . substr((string)$curlResult, 0, 500);
            return false;
        }

        if ($httpcode >= 400) {
            if (!empty($response['non_field_errors'])) {
                $this->error = "LessonSpace HTTP {$httpcode} non_field_errors: " . json_encode($response['non_field_errors']);
            } elseif (!empty($response['detail'])) {
                $this->error = "LessonSpace HTTP {$httpcode} detail: " . (is_string($response['detail']) ? $response['detail'] : json_encode($response['detail']));
            } else {
                $this->error = "LessonSpace HTTP {$httpcode}: " . json_encode($response);
            }
            return false;
        }

        return $response;
    }
}
