<?php

class LessonSpace extends FatModel
{
    private $tool;
    private $settings;
    private $meeting;

    const BASE_URL = "https://api.thelessonspace.com/v2/";

    // Buffers for join window (seconds)
    const JOIN_BUFFER_BEFORE = 900;  // 15 min
    const JOIN_BUFFER_AFTER  = 1800; // 30 min

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

    /**
     * Create Meeting on LessonSpace
     *
     * @param array $user
     * @param array $meeting
     * @return array|false
     */
    public function createMeeting(array $user, array $meeting)
    {
        $baseId = (string)($meeting['id'] ?? '');
        if ($baseId === '') {
            $this->error = "LessonSpace missing meeting[id]";
            return false;
        }

        // Build user name (must not be empty)
        $fullName = trim((string)($user['user_first_name'] ?? '') . ' ' . (string)($user['user_last_name'] ?? ''));
        if ($fullName === '') {
            $fullName = 'User';
        }

        $isTeacher = ((int)($user['user_type'] ?? 0) === User::TEACHER);

        // Keep original fields (backward compatible)
        $profilePic = trim((string)($user['user_image'] ?? ''));
        $email      = trim((string)($user['user_email'] ?? ''));

        // Lesson raw times (only for logging/diagnostics)
        $startRaw = (string)($meeting['starttime'] ?? '');
        $endRaw   = (string)($meeting['endtime'] ?? '');

        // Duration in seconds (fallback 30 mins)
        $durationMin = (int)($meeting['duration'] ?? 30);
        if ($durationMin <= 0) {
            $durationMin = 30;
        }
        $durationSec = $durationMin * 60;

        // --- The KEY FIX for LIVE: compute a safe "join window" based on SERVER NOW ---
        // This avoids DB timezone drift / server clock issues breaking the API validation.
        $now = time();
        $notBeforeUnix = $now - self::JOIN_BUFFER_BEFORE;
        $notAfterUnix  = $now + $durationSec + self::JOIN_BUFFER_AFTER;

        // Some providers require a minimum future window; ensure at least +30 min
        if ($notAfterUnix <= $now + 1800) {
            $notAfterUnix = $now + 1800;
        }

        // We'll attempt 2 payload styles:
        // 1) UTC Z (most robust across servers)
        // 2) ISO8601 with system timezone offset (fallback if their API expects offset)
        $variants = ['utc_z', 'iso_c'];

        $lastError = '';
        foreach ($variants as $idx => $variant) {
            $attemptId = $this->makeAttemptId($baseId, $idx);

            $timeouts = $this->formatTimeouts($notBeforeUnix, $notAfterUnix, $variant);
            if ($timeouts === false) {
                $lastError = $this->error;
                continue;
            }

            $payload = [
                'id' => $attemptId,
                'user' => [
                    'name' => $fullName,
                    'leader' => $isTeacher,
                ],
                'timeouts' => $timeouts,
                'features' => [
                    'invite' => false,
                    'fullscreen' => true,
                    'endSession' => false,
                    'whiteboard.equations' => true,
                    'whiteboard.infiniteToggle' => true,
                ],
            ];

            // Only include these if non-empty (avoid nulls)
            if ($email !== '') {
                $payload['user']['email'] = $email;
            }

            // IMPORTANT: profile_picture can cause live-only issues if the URL is blocked/403/private.
            // So include ONLY if it's a public http(s) URL.
            if ($profilePic !== '' && preg_match('~^https?://~i', $profilePic)) {
                $payload['user']['profile_picture'] = $profilePic;
            }

            $meta = [
                'variant' => $variant,
                'server_now_unix' => $now,
                'server_now_utc'  => gmdate('c', $now),
                'join_not_before_unix' => $notBeforeUnix,
                'join_not_after_unix'  => $notAfterUnix,
                'join_not_before_utc'  => gmdate('c', $notBeforeUnix),
                'join_not_after_utc'   => gmdate('c', $notAfterUnix),
                'lesson_start_raw' => $startRaw,
                'lesson_end_raw'   => $endRaw,
                'duration_min'     => $durationMin,
                'user_tz'          => (string)($user['user_timezone'] ?? ''),
                'system_tz'        => (string)MyUtility::getSystemTimezone(),
            ];

            $url = static::BASE_URL . 'spaces/launch/';
            $response = $this->exeCurlRequest($url, $payload, $meta);

            if ($response === false) {
                $lastError = $this->error;
                continue;
            }

            if (!empty($response['client_url'])) {
                $this->meeting = $response;
                return $this->meeting;
            }

            if (!empty($response['url'])) {
                $response['client_url'] = $response['url'];
                $this->meeting = $response;
                return $this->meeting;
            }

            $lastError = 'LessonSpace missing client_url. Raw: ' . json_encode($response);
        }

        $this->error = $lastError ?: 'LessonSpace failed to launch space.';
        return false;
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

    /**
     * Format timeouts based on unix timestamps.
     */
    private function formatTimeouts(int $notBeforeUnix, int $notAfterUnix, string $variant)
    {
        if ($notAfterUnix <= $notBeforeUnix) {
            $this->error = "LessonSpace invalid timeouts (unix): not_after <= not_before";
            return false;
        }

        if ($variant === 'utc_z') {
            return [
                'not_before' => gmdate('Y-m-d\TH:i:s\Z', $notBeforeUnix),
                'not_after'  => gmdate('Y-m-d\TH:i:s\Z', $notAfterUnix),
            ];
        }

        // iso_c (system timezone offset)
        try {
            $sysTz = new DateTimeZone((string)MyUtility::getSystemTimezone());
            $before = (new DateTime('@' . $notBeforeUnix))->setTimezone($sysTz)->format('c');
            $after  = (new DateTime('@' . $notAfterUnix))->setTimezone($sysTz)->format('c');

            return [
                'not_before' => $before,
                'not_after'  => $after,
            ];
        } catch (Exception $e) {
            $this->error = "LessonSpace timezone error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Make unique ID per attempt (safe length).
     */
    private function makeAttemptId(string $baseId, int $attemptIndex): string
    {
        $suffix = bin2hex(random_bytes(4)) . '_' . $attemptIndex;
        $id = $baseId . '_' . $suffix;

        // Keep safe length
        if (strlen($id) > 64) {
            $id = substr(hash('sha256', $id), 0, 64);
        }
        return $id;
    }

    /**
     * Execute Curl Request with strict headers + rich logs.
     */
    private function exeCurlRequest(string $url, array $params, array $meta = [])
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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        // Live should be TRUE. If CA bundle missing, you'd get curl error (not HTTP 400).
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        $curlResult = curl_exec($curl);
        $httpcode   = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($curlResult === false) {
            $this->error = 'LessonSpace cURL error: ' . curl_error($curl);
            curl_close($curl);
            return false;
        }
        curl_close($curl);

        // Logging (this is your proof for vendor support too)
        error_log("LessonSpace META=" . json_encode($meta));
        error_log("LessonSpace HTTP={$httpcode} URL={$url}");
        error_log("LessonSpace PAYLOAD={$postfields}");
        error_log("LessonSpace RESPONSE={$curlResult}");

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
