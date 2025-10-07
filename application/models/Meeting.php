<?php

/**
 * This class is used to handle Meeting
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Meeting extends FatModel
{

    private $tool;
    private $userId;
    private $userType;

    const DB_TBL = 'tbl_meetings';

    /**
     * Initialize Meeting Model
     * 
     * @param int $userId
     * @param int $userType
     */
    public function __construct(int $userId, int $userType)
    {
        $this->userId = $userId;
        $this->userType = $userType;
    }

    /**
     * Initialize Meeting
     * 
     * @return bool
     */
    public function initMeeting(int $meetingToolId = 0): bool
    {
        if (!empty($meetingToolId)) {
            $this->tool = (new MeetingTool($meetingToolId))->getDetail();
        } else {
            $this->tool = MeetingTool::getActiveTool();
        }
        if (empty($this->tool) || !class_exists($this->tool['metool_code'])) {
            $this->error = Label::getLabel('LBL_MEETING_TOOL_NOT_FOUND');
            return false;
        }
        return true;
    }

    /**
     * Join Lesson Meeting
     * 
     * @param array $lesson
     * @return bool|array
     */
    public function joinLesson(array $lesson)
    {
        $meeting = $this->getMeeting($lesson['ordles_id'], AppConstant::LESSON);
        if (!empty($meeting)) {
            return $meeting;
        }
        $data = [
            'id' => $lesson['ordles_id'] . '_' . AppConstant::LESSON,
            'title' => str_replace(
                    ['{teachlang}', '{duration}'],
                    [$lesson['ordles_tlang_name'], $lesson['ordles_duration']],
                    Label::getLabel('LBL_{teachlang},_{duration}_MINUTES_OF_LESSON')
            ),
            'duration' => $lesson['ordles_duration'],
            'starttime' => $lesson['ordles_lesson_starttime'],
            'endtime' => $lesson['ordles_lesson_endtime'],
            'timezone' => MyUtility::getSystemTimezone(),
            'recordType' => AppConstant::LESSON,
            'recordId' => $lesson['ordles_id'],
            'recordId' => $lesson['ordles_id'],
            'meetingToolId' => $this->tool['metool_id'],
            'otherDetails' => $lesson,
        ];
        $meeting = $this->createMeeting($lesson['ordles_id'], AppConstant::LESSON, $data);
        if (!empty($meeting)) {
            return $meeting;
        }
        return false;
    }

    /**
     * Join Class Meeting
     * 
     * @param array $class
     * @return bool|array
     */
    public function joinClass(array $class)
    {
        $classId = ($this->userType == User::LEARNER) ? $class['ordcls_id'] : $class['grpcls_id'];
        $meeting = $this->getMeeting($classId, AppConstant::GCLASS);
        if (!empty($meeting)) {
            return $meeting;
        }
        $data = [
            'id' => $class['grpcls_id'] . '_' . AppConstant::GCLASS,
            'title' => $class['grpcls_title'],
            'duration' => $class['grpcls_duration'],
            'starttime' => $class['grpcls_start_datetime'],
            'endtime' => $class['grpcls_end_datetime'],
            'timezone' => MyUtility::getSystemTimezone(),
            'recordType' => AppConstant::GCLASS,
            'recordId' => $class['grpcls_id'],
            'meetingToolId' => $this->tool['metool_id'],
            'otherDetails' => $class,
        ];
        $meeting = $this->createMeeting($classId, AppConstant::GCLASS, $data);
        if (!empty($meeting)) {
            return $meeting;
        }
        return false;
    }

    /**
     * Get Meeting
     * 
     * @param int $recordId
     * @param int $recordType
     * @return bool|array
     */
    public function getMeeting(int $recordId, int $recordType)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->joinTable(MeetingTool::DB_TBL, 'INNER JOIN', 'mt.metool_id=meet_metool_id', 'mt');
        $srch->addCondition('meet_record_id', '=', $recordId);
        $srch->addCondition('meet_record_type', '=', $recordType);
        $srch->addCondition('meet_user_id', '=', $this->userId);
        $srch->doNotCalculateRecords();
        $meeting = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($meeting)) {
            $this->error = Label::getLabel('LBL_MEETING_NOT_FOUND');
            return false;
        }
        return $meeting;
    }

    /**
     * Create Meeting
     * 
     * @param int $recordId
     * @param int $recordType
     * @param array $meeting
     * @return type
     */
    public function createMeeting(int $recordId, int $recordType, array $meeting)
    {
        $meet = new $this->tool['metool_code']();
        if (!$meet->initMeetingTool()) {
            $this->error = $meet->getError();
            return false;
        }
        $user = $this->getUserDetails($this->userType, $meeting['otherDetails']);
        if (!$res = $meet->createMeeting($user, $meeting)) {
            $this->error = $meet->getError();
            return false;
        }
        $res['joinUrl'] = $meet->getJoinUrl($res);
        $res['appUrl'] = $meet->getAppUrl($res);
        $record = new TableRecord(static::DB_TBL);
        $meetingRecord = [
            'meet_user_id' => $this->userId,
            'meet_metool_id' => $this->tool['metool_id'],
            'meet_record_id' => $recordId,
            'meet_record_type' => $recordType,
            'meet_details' => json_encode($res),
            'meet_created' => date('Y-m-d H:m:s'),
        ];
        $record->assignValues($meetingRecord);
        if (!$record->addNew()) {
            $this->error = $record->getError();
            return false;
        }
        return $meetingRecord;
    }

    /**
     * End Meeting (class|lesson)
     * 
     * @param int $recordId
     * @param int $recordType
     * @return bool
     */
    public function endMeeting(int $recordId, int $recordType): bool
    {
        if (!$meeting = $this->getMeeting($recordId, $recordType)) {
            return false;
        }
        $meet = new $this->tool['metool_code']($this->userId, $this->userType);
        if (!$meet->initMeetingTool()) {
            $this->error = $meet->getError();
            return false;
        }
        if (!$meet->endMeeting($meeting)) {
            $this->error = $meet->getError();
            return false;
        }
        return true;
    }

    /**
     * Checked the License in meeting tool
     *
     * @param string $startTime
     * @param string $endTime
     * @param integer $duration
     * @return boolean
     */
    public function checkLicense(string $startTime, string $endTime, int $duration): bool
    {
        if (!$this->initMeeting()) {
            return false;
        }
        $meet = new $this->tool['metool_code']($this->userId, $this->userType);
        if (!$meet->initMeetingTool()) {
            $this->error = $meet->getError();
            return false;
        }
        $meetingDuration = $meet->getFreeMeetingDuration($startTime, $endTime, $duration);
        $licenseCount = $meet->getLicensedCount($startTime, $endTime, $duration);
        if ($meetingDuration == -1 || $meetingDuration > $duration) {
            return true;
        }
        $lessons = Lesson::getScheduledLessonCount($startTime, $endTime, $meetingDuration);
        $groupClass = GroupClass::getScheduledClassesCount($startTime, $endTime, $meetingDuration);
        $totalSession = $lessons['totalCount'] + $groupClass['totalCount'];
        if ($totalSession > $licenseCount) {
            $startTime = min($lessons['startTime'] ?? $groupClass['startTime'], $groupClass['startTime'] ?? $lessons['startTime']);
            $endTime = max($lessons['endTime'] ?? $groupClass['endTime'], $groupClass['endTime'] ?? $lessons['endTime']);
            $language = MyUtility::getSystemLanguage();
            $mail = new FatMailer($language['language_id'], 'license_alert');
            $vars = [
                '{session_count}' => $totalSession,
                '{start_time}' => $startTime . "(" . CONF_SERVER_TIMEZONE . " " . MyDate::getOffset(CONF_SERVER_TIMEZONE) . ")",
                '{end_time}' => $endTime . "(" . CONF_SERVER_TIMEZONE . " " . MyDate::getOffset(CONF_SERVER_TIMEZONE) . ")",
                '{meeting_tool}' => $this->tool['metool_code'],
            ];
            $mail->setVariables($vars);
            if (!$mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')])) {
                $this->error = $mail->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Get User FDetails
     * 
     * @param int $userType
     * @param array $data
     * @return array
     */
    private function getUserDetails(int $userType, array $data): array
    {
        if ($userType == User::LEARNER) {
            return [
                'user_id' => $data['learner_id'],
                'user_first_name' => $data['learner_first_name'],
                'user_last_name' => $data['learner_last_name'],
                'user_email' => $data['learner_email'],
                'user_timezone' => $data['learner_timezone'],
                'user_type' => User::LEARNER,
                'user_image' => MyUtility::makeFullUrl(
                        'Image',
                        'show',
                        [
                            Afile::TYPE_USER_PROFILE_IMAGE, $data['learner_id'],
                            Afile::SIZE_MEDIUM
                        ],
                        CONF_WEBROOT_FRONT_URL
                ),
            ];
        } else {
            return [
                'user_id' => $data['teacher_id'],
                'user_first_name' => $data['teacher_first_name'],
                'user_last_name' => $data['teacher_last_name'],
                'user_email' => $data['teacher_email'],
                'user_timezone' => $data['teacher_timezone'],
                'user_type' => User::TEACHER,
                'user_image' => MyUtility::makeFullUrl(
                        'Image',
                        'show',
                        [
                            Afile::TYPE_USER_PROFILE_IMAGE, $data['teacher_id'],
                            Afile::SIZE_MEDIUM
                        ],
                        CONF_WEBROOT_FRONT_URL
                ),
            ];
        }
    }

}
