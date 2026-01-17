<?php

class AtomChat extends FatModel
{

    private $tool;
    private $settings;
    private $meeting;

    const BASE_URL = 'https://api.cometondemand.net/api/v2/';

    const ROLE_TEACHER = 'TEACHER';
    const ROLE_LEARNER = 'LEARNER';
    const GROUP_TYPE_PRIVATE = 4;


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
        $this->tool = MeetingTool::getByCode(MeetingTool::ATOM_CHAT);
        if (empty($this->tool)) {
            $this->error = Label::getLabel('LBL_ATOM_CHAT_NOT_FOUND');
            return false;
        }
        /* Format Meeting Tool Settings */
        $settings = json_decode($this->tool['metool_settings'], 1) ?? [];
        foreach ($settings as $row) {
            $this->settings[$row['key']] = $row['value'];
        }
        /* Validate Meeting Tool Settings */
        if (
            empty($this->settings['api_key']) ||
            empty($this->settings['api_id']) ||
            empty($this->settings['chat_auth'])
        ) {
            $this->error = Label::getLabel("MSG_ATOM_CHAT_NOT_CONFIGURED");
            return false;
        }
        return true;
    }

    /**
     * Create Meeting on Atom Chat
     * 
     * @param array $user = []
     * @param array $meeting = ['title', 'detail', 'duration', 'timezone', 'recordType', 'otherDetails', 'starttime']
     * @return bool
     */
    public function createMeeting(array $user, array $meeting)
    {
        if ($meeting['recordType'] == AppConstant::GCLASS && !$this->createGroup($user, $meeting)) {
            return false;
        }
        return $this->meeting = $this->getMeetingConfig($user, $meeting);
    }

    public function getMeetingConfig(array $user, array $meeting): array
    {
        $meeting['chat_role'] = static::ROLE_LEARNER;
        if ($user['user_type'] == User::TEACHER) {
            $meeting['chat_role'] = static::ROLE_TEACHER;
        }
        $chatName = $user['user_first_name'] . ' ' . $user['user_last_name'];
        $meetingConfig = [
            "chat_appid" => $this->settings['api_id'],
            "chat_auth" => $this->settings['chat_auth'],
            "chat_id" => $user['user_id'],
            "chat_name" => $chatName,
            "chat_avatar" =>  $user['user_image'],
            "chat_role" =>  $meeting['chat_role'],
            "chat_friends" => '',
            'chat_js' => "https://fast.cometondemand.net/" . $this->settings['api_id'] . "x_xchatx_xcorex_xembedcode.js",
            "chat_signature" => $this->generateSignature($user['user_id'], $chatName),
            'chat_iframe' => "https://" . $this->settings['api_id'] . ".cometondemand.net/cometchat_embedded.php",
        ];
        if ($meeting['recordType'] == AppConstant::GCLASS) {
            $meetingConfig['chat_iframe'] = $meetingConfig['chat_iframe'] . '?guid=' . $meeting['id'];
        } elseif ($meeting['recordType'] == AppConstant::LESSON) {
            $meetingConfig['chat_friends'] = ($user['user_id'] == $meeting['otherDetails']['teacher_id']) ? $meeting['otherDetails']['learner_id'] : $meeting['otherDetails']['teacher_id'];
        }
        return $meetingConfig;
    }

    /**
     * get join url function
     *
     * @param array $meeting 
     * @return string
     */
    public function getJoinUrl(): string
    {
        return $this->meeting['chat_iframe'];
    }

    public function getAppUrl(): string
    {
        return $this->meeting['chat_iframe'];
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
     * End Atom chat Meeting
     * 
     * @param array $meeting
     * @return bool
     */
    public function endMeeting(array $meeting): bool
    {
        return true;
    }

    /**
     * Generate signature
     *
     * @param integer $meetingId
     * @param integer $role
     * @return string
     */
    private function generateSignature($chatId, $chatName): string
    {
        return  md5($chatId . $chatName . $this->settings['api_key']);
    }

    /**
     * create Group in comet chat function
     *
     * @param array $meeting
     * @return boolean
     */
    private function createGroup(array $user, array $meeting): bool
    {
        /** for learner check group is created or not the create by teacher details*/
        if ($user['user_type'] == User::LEARNER) {
            $meetingObj = new Meeting($meeting['otherDetails']['teacher_id'], User::TEACHER);
            $meetingDetails = $meetingObj->getMeeting($meeting['recordId'], $meeting['recordType']);
            if (empty($meetingDetails)) {
                $meetingObj->initMeeting($meeting['meetingToolId']);
                if (!$meetingObj->createMeeting($meeting['recordId'], $meeting['recordType'], $meeting)) {
                    return false;
                }
            }
            return true;
        }
        $params = [
            'GUID' => $meeting['id'],
            'name' => $meeting['otherDetails']['grpcls_title'],
            'type' => static::GROUP_TYPE_PRIVATE
        ];
        $url = static::BASE_URL . 'createGroup';
        if (!$response = $this->exeCurlRequest($url, $params)) {
            return false;
        }
        /* if failed status is equal to 2001 => Class already cretaed or GUID must be unique*/
        if (empty($response['success']) && (empty($response['failed']['status']) || $response['failed']['status'] != 2001)) {
            $this->error = Label::getLabel('LBL_SOMETHING_WENT_WRONG');
            return false;
        }
        return true;
    }

    /**
     * Execute Curl Request
     *
     * @param string $url
     * @param array $params
     * @return boolean
     */
    private function exeCurlRequest(string $url, array $params, string $method = 'POST')
    {
        $headers = [
            'Content-type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'api-key: ' . $this->settings['api_key']
        ];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $curlResult = curl_exec($curl);
        if (curl_errno($curl)) {
            $this->error = 'Error:' . curl_error($curl);
            return false;
        }
        curl_close($curl);
        $response = json_decode($curlResult, true) ?? [];
        if (empty($response)) {
            $this->error = Label::getLabel('LBL_CONTACTATOM_WITH_ADMIN_ISSUE_WITH_MEETING_TOOL');
            return false;
        }
        return $response;
    }
}
