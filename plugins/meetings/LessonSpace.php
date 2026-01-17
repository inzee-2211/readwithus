<?php

class LessonSpace extends FatModel
{
    private $tool;
    private $settings;
    private $meeting;

    const BASE_URL = "https://api.thelessonspace.com/v2/";

    // How many seconds before/after lesson to allow joining (safety buffer)
    // Helps when server time is slightly drifted or user joins a bit early/late.
    const JOIN_BUFFER_BEFORE = 900; // 15 minutes
    const JOIN_BUFFER_AFTER  = 1800; // 30 minutes

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

        // Resolve a safe timezone to use (user tz preferred; fallback to system)
        $userTz = (string)($user['user_timezone'] ?? '');
        $sysTz  = (string)MyUtility::getSystemTimezone();
        $tzName = $this->pickValidTimezone($userTz, $sysTz);

        // Compute timestamps from meeting start/end
        $startRaw = (string)($meeting['starttime'] ?? '');
        $endRaw   = (string)($meeting['endtime'] ?? '');
        if ($startRaw === '' || $endRaw === '') {
            $this->error = "LessonSpace missing meeting start/end times";
            return false;
        }

        // Build user name (must not be empty)
        $fullName = trim((string)($user['user_first_name'] ?? '') . ' ' . (string)($user['user_last_name'] ?? ''));
        if ($fullName === '') {
            $fullName = 'User';
        }

        // Keep only fields that existed in the ORIGINAL payload by default.
        $profilePic = (string)($user['user_image'] ?? '');

        // Try payload variants in this order:
        // 1) ORIGINAL STYLE (offset format using timezone offset) + buffer
        // 2) ISO8601 "c" (system/user tz) + buffer
        // 3) UTC "Z" + buffer
        //
        // Reason: local worked with original offset style; live may need different normalization.
        $variants = ['offset', 'iso_c', 'utc_z'];

        $lastError = '';
        foreach ($variants as $idx => $variant) {
            $attemptId = $this->makeAttemptId($baseId, $idx);

            $timeouts = $this->buildTimeouts($startRaw, $endRaw, $tzName, $variant);
            if ($timeouts === false) {
                // buildTimeouts sets $this->error
                $lastError = $this->error;
                continue;
            }

            $payload = [
                'id' => $attemptId,
                'user' => [
                    'name' => $fullName,
                    'leader' => ((int)($user['user_type'] ?? 0) === User::TEACHER),
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

            // Only include profile_picture if we have a non-empty URL/string (avoid null)
            if ($profilePic !== '') {
                $payload['user']['profile_picture'] = $profilePic;
            }

            // OPTIONAL (safe): include email ONLY if it exists and is not empty.
            // Some LessonSpace accounts enforce email, but we never send null.
            $email = trim((string)($user['user_email'] ?? ''));
            if ($email !== '') {
                $payload['user']['email'] = $email;
            }

            $url = static::BASE_URL . 'spaces/launch/';
            $response = $this->exeCurlRequest($url, $payload, [
                'variant' => $variant,
                'tz_used' => $tzName,
                'start_raw' => $startRaw,
                'end_raw' => $endRaw,
                'server_now_unix' => time(),
                'server_now_iso' => gmdate('c'),
            ]);

            if ($response === false) {
                $lastError = $this->error;
                continue;
            }

            // Success requires client_url
            if (!empty($response['client_url'])) {
                $this->meeting = $response;
                return $this->meeting;
            }

            // Some APIs might return "url" instead of "client_url"
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
     * Build timeouts with buffers and different formats.
     *
     * Variants:
     * - offset : 2026-01-17T19:30:00+05:00   (closest to original)
     * - iso_c  : 2026-01-17T19:30:00+05:00   via DateTime::format('c')
     * - utc_z  : 2026-01-17T14:30:00Z        (UTC Z)
     */
    private function buildTimeouts(string $startRaw, string $endRaw, string $tzName, string $variant)
    {
        try {
            $tz = new DateTimeZone($tzName);

            // Interpret raw DB times as being in $tzName (this matches original intent most closely)
            $startDt = new DateTime($startRaw, $tz);
            $endDt   = new DateTime($endRaw, $tz);

            // Apply buffer to tolerate minor drift and allow early join
            $startDt->modify('-' . self::JOIN_BUFFER_BEFORE . ' seconds');
            $endDt->modify('+' . self::JOIN_BUFFER_AFTER . ' seconds');

            if ($endDt->getTimestamp() <= $startDt->getTimestamp()) {
                $this->error = "LessonSpace invalid timeouts: end <= start after buffers";
                return false;
            }

            if ($variant === 'utc_z') {
                $utc = new DateTimeZone('UTC');
                $startDt->setTimezone($utc);
                $endDt->setTimezone($utc);

                return [
                    'not_before' => $startDt->format('Y-m-d\TH:i:s\Z'),
                    'not_after'  => $endDt->format('Y-m-d\TH:i:s\Z'),
                ];
            }

            if ($variant === 'iso_c') {
                return [
                    'not_before' => $startDt->format('c'),
                    'not_after'  => $endDt->format('c'),
                ];
            }

            // default: offset (explicit like original)
            return [
                'not_before' => $startDt->format('Y-m-d\TH:i:sP'),
                'not_after'  => $endDt->format('Y-m-d\TH:i:sP'),
            ];

        } catch (Exception $e) {
            $this->error = "LessonSpace DateTime error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Pick a valid timezone string.
     */
    private function pickValidTimezone(string $preferred, string $fallback): string
    {
        $preferred = trim($preferred);
        $fallback  = trim($fallback);

        if ($preferred !== '' && $this->isValidTimezone($preferred)) {
            return $preferred;
        }
        if ($fallback !== '' && $this->isValidTimezone($fallback)) {
            return $fallback;
        }
        // Safe final fallback
        return 'UTC';
    }

    private function isValidTimezone(string $tz): bool
    {
        try {
            new DateTimeZone($tz);
            return true;
        } catch (Exception $e) {
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

        // Keep it safely short (some APIs enforce max lengths)
        if (strlen($id) > 64) {
            $id = substr(hash('sha256', $id), 0, 64);
        }
        return $id;
    }

    /**
     * Execute Curl Request with strict headers + rich logs.
     *
     * @param string $url
     * @param array $params
     * @param array $meta
     * @return array|false
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

        // On LIVE: should be true. If live server lacks CA bundle, you'd get cURL SSL error (not HTTP 400).
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        $curlResult = curl_exec($curl);
        $httpcode   = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($curlResult === false) {
            $this->error = 'LessonSpace cURL error: ' . curl_error($curl);
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        // Log full request/response (helps diagnose LIVE-only issues)
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

        // Success
        return $response;
    }
}
