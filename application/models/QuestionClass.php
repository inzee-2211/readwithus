<?php

/**
 * This class is used to handle GroupClass
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class QuestionClass extends MyAppModel
{

    const DB_TBL = 'tbl_questions';
    const DB_TBL_PREFIX = 'question_';
    const DB_TBL_LANG = 'tbl_group_classes_lang';
    const DB_TBL_LANG_PREFIX = 'gclang_';
    /* Class Status column grpcls_status */
    const SCHEDULED = 1;
    const COMPLETED = 2;
    const CANCELLED = 3;
    /* Class Type */
    const TYPE_REGULAR = 1;
    const TYPE_PACKAGE = 2;

    private $userId;
    private $userType;

    /**
     * Initialize Group Class
     * 
     * @param int $id
     * @param int $userId
     * @param int $userType
     */
    public function __construct(int $id = 0, int $userId = 0, int $userType = 0)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        parent::__construct(static::DB_TBL, 'question_id', $id);
    }

    /**
     * Get Statuses
     * 
     * @param int $key
     * @return string|array
     */
    public static function getStatuses(int $key = null)
    {
        $arr = [
            static::SCHEDULED => Label::getLabel('LBL_SCHEDULED'),
            static::COMPLETED => Label::getLabel('LBL_COMPLETED'),
            static::CANCELLED => Label::getLabel('LBL_CANCELLED')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    public static function getClassTypes($key = null)
    {
        $arr = [
            static::TYPE_REGULAR => Label::getLabel('LBL_REGULAR'),
            static::TYPE_PACKAGE => Label::getLabel('LBL_PACKAGE')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Search Object
     * 
     * @return SearchBase
     */
    public function getSearchObject(): SearchBase
    {
        $srch = new SearchBase(static::DB_TBL, 'grpcls');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        $srch->addCondition('teacher.user_is_teacher', '=', AppConstant::YES);
        $srch->addDirectCondition('teacher.user_deleted IS NULL');
        if ($this->userId > 0 && $this->userType == User::TEACHER) {
            $srch->addCondition('grpcls.grpcls_teacher_id', '=', $this->userId);
        }
        if ($this->mainTableRecordId > 0) {
            $srch->addCondition('grpcls.grpcls_id', '=', $this->mainTableRecordId);
        }
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        return $srch;
    }

    /**
     * Save Group Class
     * 
     * Add new and update existing the group class 
     *
     * @param array $data = [grpcls_start_datetime, grpcls_entry_fee] must be in default configured timezone
     * @return bool
     */
    public function saveClass(array $data): bool
    {/*
        echo '<pre>';print_r($data);die;
        $recordId = $this->mainTableRecordId;
        $addEvent = true;
        if ($this->mainTableRecordId > 0) {
            if (!$classData = $this->getClassToSave(0)) {
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
            }
            if (!empty($classData['grpcls_booked_seats'])) {
                $addEvent = false;
                $data['grpcls_start_datetime'] = $classData['grpcls_start_datetime'];
                $data['grpcls_end_datetime'] = $classData['grpcls_end_datetime'];
                $data['grpcls_duration'] = $classData['grpcls_duration'];
                $data['grpcls_entry_fee'] = $classData['grpcls_entry_fee'];
                $data['grpcls_tlang_id'] = $classData['grpcls_tlang_id'];
                $data['grpcls_total_seats'] = max($data['grpcls_total_seats'], $classData['grpcls_booked_seats']);
            }
        }
        $startUnix = strtotime($data['grpcls_start_datetime']);
        if ($startUnix < time()) {
            $this->error = Label::getLabel('LBL_CAN_NOT_ADD_TIME_FOR_OLD_DATE');
            return false;
        }
        
        $avail = new Availability($this->userId);
        if (!$avail->isUserAvailable($data['grpcls_start_datetime'], $data['grpcls_end_datetime'], $this->mainTableRecordId)) {
            $this->error = $avail->getError();
            return false;
        }
        unset($data['grpcls_id']);
        $db = FatApp::getDb();
        $db->startTransaction();
        $this->setFldValue('grpcls_type', static::TYPE_REGULAR);
        $this->assignValues($data);
        if (!$this->save()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!empty($data['grpcls_banner']['name'])) {
            $file = new Afile(Afile::TYPE_GROUP_CLASS_BANNER);
            $classId = $this->getMainTableRecordId();
            if (!$file->saveFile($data['grpcls_banner'], $classId, true)) {
                $db->rollbackTransaction();
                $this->error = $file->getError();
                return false;
            }
        }
        if ($addEvent) {
            $this->addGoogleEvent($recordId, $data);
        }
        $meetingTool = new Meeting(0, 0);
        $meetingTool->checkLicense($data['grpcls_start_datetime'], $data['grpcls_end_datetime'], $data['grpcls_duration']);
        $db->commitTransaction();
        return true;*/


       
        /* validate data */
        // if (!$this->validate($data)) {
        //     return false;
        // }
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
 
        
        $this->setFldValue('question_title', $data['grpcls_title']);
        $this->setFldValue('question_desc', $data['grpcls_description']);
        $this->setFldValue('question_math_equation', $data['grpcls_description_math']);
        $this->setFldValue('question_type', $data['grpcls_tlang_id']);
        $this->setFldValue('question_added_by', $data['grpcls_teacher_id']);
        $this->setFldValue('question_cat', $data['course_cate_id']);
        $this->setFldValue('question_subcat', (string)($data['course_subcate_ida'] ?? '0')); //added by rehan
        // $this->setFldValue('question_subcat', $_POST['course_subcate_ida']);
        $this->setFldValue('question_hint', $data['grpcls_hint']);
        $this->setFldValue('question_image', $data['image']);
        $this->setFldValue('question_marks', $data['grpcls_total_marks']);
        $this->setFldValue('question_option_1', isset($data['question_option_1']) ? $data['question_option_1'] : '');
        $this->setFldValue('question_option_2', isset($data['question_option_2']) ?$data['question_option_2'] : '');
        $this->setFldValue('question_option_3', isset($data['question_option_3']) ? $data['question_option_3'] :'');
        // AFTER (correctly take option_4)
$this->setFldValue('question_option_4', isset($data['question_option_4']) ? $data['question_option_4'] : ''); //added by rehan
        // $this->setFldValue('question_option_4', isset($data['question_option_4']) ? $data['question_option_2'] :'');
        $this->setFldValue('question_answers', isset($data['question_answers']) ? $data['question_answers'] :'');
       
        $this->assignValues($data);
        if (!$this->save()) {
            $db->rollbackTransaction();
            $this->error = $this->getError();
            return false;
        }
         

        $db->commitTransaction();
        return true;
    }

    /**
     * Add Google Event
     * 
     * @param bool $recordId
     * @param array $data
     */
    public function addGoogleEvent(bool $recordId, array $data)
    {
        $token = (new UserSetting($this->userId))->getGoogleToken();
        if (!empty($token)) {
            $googleCalendar = new GoogleCalendarEvent($this->userId, $this->mainTableRecordId, AppConstant::GCLASS);
            if ($recordId > 0) {
                $event = $googleCalendar->getGroupClassEvent();
                if (!empty($event['gocaev_event_id'])) {
                    $googleCalendar->deletEvent($token, $event['gocaev_event_id']);
                }
            }
            $data['grpcls_id'] = $this->mainTableRecordId;
            $data['google_token'] = $token;
            $googleCalendar->addClassEvent($data, User::TEACHER);
        }
    }

    /**
     * Save Lang Data
     * 
     * @param array $data
     * @return bool
     */
    public function saveLangData(array $data): bool
    {
        if (!$this->getClassToSave()) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $record = new TableRecord(static::DB_TBL_LANG);
        $record->assignValues($data);
        if (!$record->addNew([], $record->getFlds())) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Start Class
     * 
     * @param array $class
     * @return boolean|array
     */
    public function start(array $class)
    {
        if ($this->userType == User::LEARNER && empty($class['grpcls_teacher_starttime'])) {
            $this->error = Label::getLabel('LBL_PLEASE_WAIT_LET_TEACHER_JOIN');
            return false;
        }
        if (empty($class['grpcls_teacher_starttime'])) {
            $this->assignValues([
                'grpcls_teacher_starttime' => date('Y-m-d H:i:s'),
                'ordcls_updated' => date('Y-m-d H:i:s')
            ]);
        }
        $this->setFldValue('grpcls_metool_id', $class['grpcls_metool_id']);
        if (!$this->save()) {
            return false;
        }
        return $class;
    }

    /**
     * Complete Class
     * 
     * @param array $class
     * @return bool
     */
    public function complete(array $class): bool
    {
        $db = FatApp::getDb();
        $db->startTransaction();
        $this->assignValues(['grpcls_teacher_endtime' => date('Y-m-d H:i:s'), 'grpcls_status' => static::COMPLETED]);
        if (!$this->save()) {
            return false;
        }
        if (!$this->markOrderClassCompleted()) {
            $db->rollbackTransaction();
            return false;
        }
        if ($class['grpcls_parent'] > 0) {
            $packageClsStats = static::getPackageClsStats($class['grpcls_parent']);
            if (0 >= $packageClsStats['schClassCount']) {
                $tableRecord = new TableRecord(static::DB_TBL);
                $tableRecord->assignValues(['grpcls_status' => static::COMPLETED]);
                if (!$tableRecord->update(['smt' => 'grpcls_id = ?', 'vals' => [$class['grpcls_parent']]])) {
                    $this->error = $tableRecord->getError();
                    $db->rollbackTransaction();
                    return false;
                }
                $tableRecord = new TableRecord(OrderPackage::DB_TBL);
                $tableRecord->assignValues(['ordpkg_status' => OrderPackage::COMPLETED]);
                if (!$tableRecord->update(['smt' => 'ordpkg_package_id = ? and ordpkg_status = ?', 'vals' => [$class['grpcls_parent'], OrderPackage::SCHEDULED]])) {
                    $this->error = $tableRecord->getError();
                    $db->rollbackTransaction();
                    return false;
                }
            }
        }
        $sessionLog = new SessionLog($this->getMainTableRecordId(), AppConstant::GCLASS);
        if (!$sessionLog->addCompletedClassLog($this->userId, User::TEACHER)) {
            $db->rollbackTransaction();
            $this->error = $sessionLog->getError();
            return false;
        }
        $db->commitTransaction();
        return true;
    }

    /**
     * Mark Order Class Completed
     * 
     * @return bool
     */
    private function markOrderClassCompleted(): bool
    {
        $time = date('Y-m-d H:i:s');
        $query = " UPDATE " . OrderClass::DB_TBL . " SET ordcls_endtime = '" . $time . "', ordcls_starttime = IF(ordcls_starttime IS NULL, '" . $time . "', `ordcls_starttime`), ordcls_ended_by = '" . User::TEACHER . "', ordcls_updated = '" . $time . "', ordcls_status = '" . OrderClass::COMPLETED . "' WHERE 
                    ordcls_status = " . OrderClass::SCHEDULED . " AND ordcls_grpcls_id = " . $this->mainTableRecordId . ";";
        if (!FatApp::getDb()->query($query)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Cancel Class
     * 
     * @param string $comment
     * @param int $langId
     * @return bool
     */
    public function cancel(string $comment = '', int $langId = 0): bool
    {
        if (!$class = $this->getClassToCancel($langId)) {
            return false;
        }
        $ordClses = OrderClass::getOrdClsByGroupId($this->mainTableRecordId, [], [OrderClass::SCHEDULED]);
        $db = FatApp::getDb();
        $db->startTransaction();
        $this->setFldValue('grpcls_status', static::CANCELLED);
        if (!$this->save()) {
            return false;
        }
        $tableRecord = new TableRecord(OrderClass::DB_TBL);
        $tableRecord->assignValues(['ordcls_status' => OrderClass::CANCELLED, 'ordcls_updated' => date('Y-m-d H:i:s')]);
        if (!$tableRecord->update(['smt' => 'ordcls_grpcls_id = ? ', 'vals' => [$this->mainTableRecordId]])) {
            $this->error = $tableRecord->getError();
            $db->rollbackTransaction();
            return false;
        }
        $orderClass = new OrderClass();
        if (!$orderClass->refundToLearner($ordClses, 100)) {
            $this->error = $orderClass->getError();
            $db->rollbackTransaction();
            return false;
        }
        $sessionLog = new SessionLog($this->mainTableRecordId, AppConstant::GCLASS);
        if (!$sessionLog->addCanceledClassLog($this->userId, User::TEACHER, $comment)) {
            $db->rollbackTransaction();
            $this->error = $sessionLog->getError();
            return false;
        }
        $count = (count($ordClses) * -1);
        if (!$this->updateBookedSeatsCount($count)) {
            $db->rollbackTransaction();
            return false;
        }
        $db->commitTransaction();
        $class['comment'] = $comment;
        $googleCalendar = new GoogleCalendarEvent($this->userId, $this->mainTableRecordId, AppConstant::GCLASS);
        $googleCalendar->removeClassEvents();
        $this->sendCancelClassNotification($ordClses, $class);
        return true;
    }

    /* Cancel Class
     * 
     * @param string $comment
     * @param int $langId
     * @return bool
     */

    public function cancelPackage(): bool
    {
        if (!$this->getPackageToCancel()) {
            return false;
        }
        $this->setFldValue('grpcls_status', static::CANCELLED);
        if (!$this->save()) {
            return false;
        }
        $record = new TableRecord(static::DB_TBL);
        $record->setFldValue('grpcls_status', static::CANCELLED, true);
        if (!$record->update(['smt' => 'grpcls_parent = ?', 'vals' => [$this->mainTableRecordId]])) {
            $this->error = $record->getError();
            return false;
        }
        $googleCalendar = new GoogleCalendarEvent($this->userId, $this->mainTableRecordId, AppConstant::GCLASS);
        $googleCalendar->removeTeacherPackEvents();
        return true;
    }

    /**
     * Send Cancel Class Notification
     * 
     * @param array $ordClses
     * @param array $class
     */
    private function sendCancelClassNotification(array $ordClses, array $class)
    {
        foreach ($ordClses as $value) {
            $url = MyUtility::makeUrl('Classes', 'view', [$value['ordcls_id']]);
            $noti = new Notification($value['user_id'], Notification::TYPE_CLASS_CANCELLED);
            $noti->sendNotification(['{link}' => $url, '{class_name}' => $class['grpcls_title']], User::LEARNER);
            $mail = new FatMailer($value['learner_lang_id'], 'teacher_class_cancelled_email');
            $vars = [
                '{class_name}' => $class['grpcls_title'],
                '{teacher_comment}' => $class['comment'],
                '{learner_name}' => $value['learner_first_name'] . ' ' . $value['learner_last_name'],
                '{teacher_name}' => $value['teacher_first_name'] . ' ' . $value['teacher_last_name'],
                '{class_url}' => $url,
            ];
            $mail->setVariables($vars);
            $mail->sendMail([$value['learner_email']]);
        }
    }

    /**
     * Get Class To Start
     * 
     * @param int $langId
     * @return bool|array
     */
    public function getClassToStart(int $langId)
    {
        $currentDate = date('Y-m-d H:i:s');
        $srch = $this->getSearchObject();
        $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id and gclang.gclang_lang_id = ' . $langId, 'gclang');
        $srch->addCondition('grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('grpcls_start_datetime', '<=', $currentDate);
        $srch->addCondition('grpcls_end_datetime', '>', $currentDate);
        $srch->addMultipleFields([
            'grpcls.grpcls_id', 'IFNULL(gclang.grpcls_title,grpcls.grpcls_title) as grpcls_title',
            'grpcls.grpcls_start_datetime', 'grpcls.grpcls_end_datetime', 'grpcls.grpcls_metool_id',
            'grpcls.grpcls_teacher_starttime', 'grpcls.grpcls_teacher_endtime',
            'teacher.user_id as teacher_id', 'grpcls_duration',
            'teacher.user_first_name as teacher_first_name', 'teacher.user_last_name as teacher_last_name',
            'teacher.user_email as teacher_email', 'teacher.user_timezone as teacher_timezone',
        ]);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            $this->error = Label::getLabel('LBL_CLASS_NOT_FOUND');
            return false;
        }
        return $row;
    }

    /**
     * Get Class To Save
     * 
     * @param int $langId
     * @return bool|null|array
     */
    public function getClassToSave(int $langId = 0)
    {
         
        if (0 >= $this->mainTableRecordId) {
            return false;
        }
       
        $srch = new SearchBase(QuestionClass::DB_TBL, 'question');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('question.*');
        // if ($langId > 0) {
        //     $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id and gclang.gclang_lang_id = ' . $langId, 'gclang');
        //     $srch->addMultipleFields([
        //         'IFNULL(gclang.grpcls_title,grpcls.grpcls_title) as grpcls_title',
        //         'IFNULL(gclang.grpcls_description,grpcls.grpcls_description) as grpcls_description',
        //     ]);
        // }
        // if ($this->userId > 0) {
        //     $srch->addCondition('grpcls.grpcls_teacher_id', '=', $this->userId);
        // }
        // $srch->addCondition('grpcls.grpcls_booked_seats', '=', 0);
        // $srch->addCondition('grpcls.grpcls_status', '=', GroupClass::SCHEDULED);
        // $srch->addCondition('grpcls.grpcls_start_datetime', '>', date('Y-m-d H:i:s'));
        // $srch->addCondition('grpcls.grpcls_parent', '=', 0);
          $srch->addCondition('question.question_id', '=', $this->mainTableRecordId);

     
        return FatApp::getDb()->fetch($srch->getResultSet());
 
     
    }

    public function getClassToSaveQuiz(int $langId = 0)
    {
         
        if (0 >= $this->mainTableRecordId) {
            return false;
        }



            $srch = new SearchBase('tbl_quiz_grading', 'qg');
            $srch->doNotCalculateRecords();
            $srch->setPageSize(1);
            $srch->addFld('qg.*, qd.quiz_title, qd.quiz_description,qd.quiz_pass_percentage,cc.course_title'); // Add fields from the joined table
            $srch->addCondition('qg.id', '=', $this->mainTableRecordId);

            // Add join
            $srch->joinTable(
            'tbl_quizzes',   // Table to join
            'INNER JOIN',         // Join type
            'qg.quiz_id = qd.quiz_id', // Join condition
            'qd'                  // Alias for the joined table
            );
            $srch->joinTable(
                'tbl_course_details',   // Table to join
                'INNER JOIN',         // Join type
                'qg.course_id = cc.course_id', // Join condition
                'cc'                  // Alias for the joined table
                );

        
        // $srch = new SearchBase('tbl_quiz_grading', 'qg');
        // $srch->doNotCalculateRecords();
        // $srch->setPageSize(1);
        // $srch->addFld('qg.*');
        // $srch->addCondition('qg.id', '=', $this->mainTableRecordId);

     
        return FatApp::getDb()->fetch($srch->getResultSet());
 
     
    }
    public function getQuizData($createdby_id)
    {
        $srch = new SearchBase('tbl_quizzes', 'quizzes');
        $srch->addMultipleFields(['*']);
        $srch->addCondition('quiz_user_id', '=', $createdby_id);
        $srch->doNotCalculateRecords();
       // echo $srch->getQuery();die;
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }
    /**
     * Get Package To Save
     * 
     * @param int $langId
     * @return bool|null|array
     */
    public function getPackageToSave(int $langId = 0)
    {
        if (0 >= $this->mainTableRecordId) {
            return false;
        }
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('grpcls.*');
        if ($langId > 0) {
            $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id and gclang.gclang_lang_id = ' . $langId, 'gclang');
            $srch->addMultipleFields([
                'IFNULL(gclang.grpcls_title,grpcls.grpcls_title) as grpcls_title',
                'IFNULL(gclang.grpcls_description,grpcls.grpcls_description) as grpcls_description',
            ]);
        }
        if ($this->userId > 0) {
            $srch->addCondition('grpcls.grpcls_teacher_id', '=', $this->userId);
        }
        $srch->addCondition('grpcls.grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_booked_seats', '=', 0);
        $srch->addCondition('grpcls.grpcls_parent', '=', 0);
        $srch->addCondition('grpcls.grpcls_type', '=', static::TYPE_PACKAGE);
        $srch->addCondition('grpcls.grpcls_id', '=', $this->mainTableRecordId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get Class To Complete
     * 
     * @return bool|array
     */
    public function getClassToComplete()
    {
        $srch = $this->getSearchObject();
        $srch->addCondition('grpcls.grpcls_status', '=', static::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_start_datetime', '<', date('Y-m-d H:i:s'));
        $srch->addDirectCondition('grpcls_teacher_starttime IS NOT NULL');
        $srch->addDirectCondition('grpcls_teacher_endtime IS NULL');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            $this->error = Label::getLabel('LBL_CLASS_NOT_FOUND');
            return false;
        }
        if (!empty($row['grpcls_teacher_starttime'])) {
            $endLessonWindow = FatApp::getConfig('CONF_ALLOW_TEACHER_END_CLASS', FatUtility::VAR_INT, 10);
            $toTime = strtotime('+' . $endLessonWindow . ' minutes', strtotime($row['grpcls_teacher_starttime']));
            $toTime = min($toTime, strtotime($row['grpcls_end_datetime']));
            if (time() < $toTime) {
                $this->error = Label::getLabel('LBL_CANNOT_END_CLASS_SO_EARLY!');
                return false;
            }
        }
        return $row;
    }

    /**
     * Get Class To Cancel
     * 
     * @param int $langId
     * @return bool|array
     */
    public function getClassToCancel(int $langId = 0)
    {
        $srch = $this->getSearchObject();
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = gclang.gclang_grpcls_id and gclang_lang_id = ' . $langId, 'gclang');
            $srch->addFld('IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title');
        }
        $srch->addCondition('grpcls.grpcls_status', '=', static::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_parent', '=', 0);
        $srch->addCondition('grpcls.grpcls_type', '=', static::TYPE_REGULAR);
        $srch->addMultipleFields(['grpcls.grpcls_id', 'grpcls_start_datetime', 'grpcls_booked_seats']);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            $this->error = Label::getLabel('LBL_CLASS_NOT_FOUND');
            return false;
        }
        $duration = FatApp::getConfig('CONF_CLASS_CANCEL_DURATION', FatUtility::VAR_INT, 24);
        $startTime = strtotime($row['grpcls_start_datetime'] . ' -' . $duration . ' hours');
        if (time() >= $startTime) {
            $this->error = Label::getLabel('LBL_TIME_TO_CANCEL_CLASS_PASSED');
            return false;
        }
        return $row;
    }

    /**
     * Get Class to Book
     *
     * @param int $langId       Language Id, if language based data required
     * @return array
     */
    public function getClassToBook(int $langId = 0)
    {
        $timeToBook = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $srch = new SearchBase(static::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_status', '=', static::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_type', '=', static::TYPE_REGULAR);
        $srch->addCondition('grpcls.grpcls_teacher_id', '!=', $this->userId);
        $srch->addCondition('grpcls.grpcls_id', '=', $this->mainTableRecordId);
        $srch->addCondition('mysql_func_grpcls.grpcls_total_seats', '>', 'mysql_func_grpcls.grpcls_booked_seats', 'AND', true);
        $srch->addCondition('mysql_func_DATE_SUB(grpcls.grpcls_start_datetime, INTERVAL ' . $timeToBook . ' MINUTE)', '>=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addMultipleFields([
            'grpcls.grpcls_id', 'grpcls.grpcls_title', 'grpcls.grpcls_teacher_id', 'grpcls.grpcls_entry_fee as ordcls_amount',
            'grpcls.grpcls_duration', 'grpcls_total_seats', 'grpcls.grpcls_start_datetime', 'grpcls.grpcls_end_datetime', 'grpcls_booked_seats'
        ]);
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id and gclang.gclang_lang_id = ' . $langId, 'gclang');
            $srch->addFld("IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title");
        }
        $srch->doNotCalculateRecords();
        $class = FatApp::getDb()->fetch($srch->getResultSet(), 'grpcls_id');
        if (empty($class)) {
            $this->error = Label::getLabel('LBL_CLASS_NOT_FOUND');
            return false;
        }
        return $class;
    }

    public function getPackageToBook(int $langId = 0)
    {
        $timeToBook = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $srch = new SearchBase(static::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_status', '=', static::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_type', '=', static::TYPE_PACKAGE);
        $srch->addCondition('grpcls.grpcls_teacher_id', '!=', $this->userId);
        $srch->addCondition('grpcls.grpcls_id', '=', $this->mainTableRecordId);
        $srch->addCondition('mysql_func_grpcls.grpcls_total_seats', '>', 'mysql_func_grpcls.grpcls_booked_seats', 'AND', true);
        $srch->addCondition('mysql_func_DATE_SUB(grpcls.grpcls_start_datetime, INTERVAL ' . $timeToBook . ' MINUTE)', '>=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addMultipleFields([
            'grpcls.grpcls_id', 'grpcls.grpcls_title', 'grpcls.grpcls_teacher_id', 'grpcls.grpcls_entry_fee as grpcls_amount',
            'grpcls.grpcls_duration', 'grpcls_total_seats', 'grpcls.grpcls_start_datetime', 'grpcls.grpcls_end_datetime', 'grpcls_booked_seats'
        ]);
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id and gclang.gclang_lang_id = ' . $langId, 'gclang');
            $srch->addFld("IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title");
        }
        $srch->doNotCalculateRecords();
        $package = FatApp::getDb()->fetch($srch->getResultSet(), 'grpcls_id');
        if (empty($package)) {
            $this->error = Label::getLabel('LBL_CLASS_PACKAGE_NOT_FOUND');
            return false;
        }
        return $package;
    }

    /**
     * Update Booked Seats Count
     * 
     * @param int $count
     * @return bool
     */
    public function updateBookedSeatsCount(int $count = 1): bool
    {
        $query = "UPDATE " . static::DB_TBL . ' SET grpcls_booked_seats = grpcls_booked_seats + ' . $count . ' WHERE grpcls_id = ' . $this->mainTableRecordId;
        if (!FatApp::getDb()->query($query)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Check Group Class Timing
     * 
     * @param array $userIds
     * @param string $startDateTime
     * @param string $endDateTime
     * @return SearchBase
     */
    public static function checkGroupClassTiming(array $userIds, string $startDateTime, string $endDateTime): SearchBase
    {
        $searchBase = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $searchBase->addMultipleFields(['grpcls_id']);
        $searchBase->addCondition('grpcls_teacher_id', 'IN', $userIds);
        $searchBase->addCondition('grpcls_start_datetime', '<', $endDateTime);
        $searchBase->addCondition('grpcls_end_datetime', '>', $startDateTime);
        return $searchBase;
    }

    /**
     * Get Names
     * 
     * @param array $classIds
     * @param array $langIds
     * @return array
     */
    public function getNames(array $classIds, array $langIds): array
    {
        $identifier = '';
        $searchBase = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $searchBase->addMultipleFields([
            "IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title",
            'gclang_lang_id', 'grpcls_id', ' grpcls.grpcls_title as identifier'
        ]);
        $searchBase->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id', 'gclang');
        $searchBase->addCondition('gclang_grpcls_id', 'IN', $classIds);
        $searchBase->addCondition('gclang_lang_id', 'IN', $langIds);
        $searchBase->doNotCalculateRecords();
        $searchBase->doNotLimitRecords();
        $resultSet = $searchBase->getResultSet();
        $names = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $names[$row['grpcls_id']][$row['gclang_lang_id']] = $row['grpcls_title'];
            $identifier = $row['identifier'];
        }
        foreach ($classIds as $classId) {
            if (!array_key_exists($classId, $names)) {
                $names[$classId] = [];
            }
            foreach ($langIds as $langId) {
                if (!array_key_exists($langId, $names[$classId])) {
                    $names[$classId][$langId] = $identifier;
                }
            }
        }
        return $names;
    }

    /**
     * Get Class By Slug
     * 
     * @param string $slug
     * @return type
     */
    public static function getClassBySlug(string $slug)
    {
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->addMultipleFields(['grpcls_id', 'grpcls.grpcls_slug', 'grpcls.grpcls_title']);
        $srch->addCondition('grpcls_slug', '=', $slug);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get Attributes By Lang Id
     * 
     * @param int $langId
     * @param int $recordId
     * @param type $attr
     * @return bool|string|array
     */
    public static function getAttrByLangId(int $langId, int $recordId, array $attr = null)
    {
        $srch = new SearchBase(static::DB_TBL_LANG, 'gclang');
        $srch->addCondition('gclang.gclang_lang_id', '=', $langId);
        $srch->addCondition('gclang.gclang_grpcls_id', '=', $recordId);
        $srch->doNotCalculateRecords();
        $srch->setPagesize(1);
        if (!is_null($attr)) {
            $srch->addMultipleFields($attr);
        }
        $record = FatApp::getDb()->fetch($srch->getResultSet());
        return empty($record) ? false : $record;
    }

    /**
     * Get package To Cancel
     * 
     * @param int $langId
     * @return bool|array
     */
    public function getPackageToCancel()
    {
        $srch = new SearchBase(static::DB_TBL, 'grpcls');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        $srch->addCondition('teacher.user_is_teacher', '=', AppConstant::YES);
        $srch->addCondition('grpcls.grpcls_teacher_id', '=', $this->userId);
        $srch->addCondition('grpcls.grpcls_id', '=', $this->mainTableRecordId);
        $srch->addCondition('grpcls.grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_type', '=', GroupClass::TYPE_PACKAGE);
        $srch->addCondition('grpcls.grpcls_booked_seats', '=', 0);
        $srch->addMultipleFields([
            'grpcls.grpcls_id', 'teacher.user_id as teacher_id',
            'grpcls_start_datetime', 'grpcls_booked_seats'
        ]);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            $this->error = Label::getLabel('LBL_PACKAGE_NOT_FOUND');
            return false;
        }
        $duration = FatApp::getConfig('CONF_PACKAGE_CANCEL_DURATION', FatUtility::VAR_INT, 24);
        $startTime = strtotime($row['grpcls_start_datetime'] . ' -' . $duration . ' hours');
        if (time() >= $startTime) {
            $this->error = Label::getLabel('LBL_TIME_TO_CANCEL_CLASS_PASSED');
            return false;
        }
        return $row;
    }

    public static function getPackageClsStats(int $packageId)
    {
        $srch = new SearchBase(static::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_parent', '=', $packageId);
        $srch->addMultipleFields([
            'count(grpcls_id) as totalClasses',
            'count(IF(grpcls.grpcls_status = ' . static::SCHEDULED . ', 1, null)) as schClassCount',
            'count(IF(grpcls.grpcls_status = ' . static::COMPLETED . ', 1, null)) as completedClass',
            'count(IF(grpcls.grpcls_status = ' . static::CANCELLED . ', 1, null)) as cancelledClass',
        ]);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    public function getSchedClassStats(): array
    {
        $srch = $this->getSearchObject();
        $srch->addCondition('grpcls_type', '=', static::TYPE_REGULAR);
        $srch->addMultipleFields([
            'count(grpcls_id) as totalClasses',
            'count(IF(grpcls.grpcls_status = ' . static::SCHEDULED . ', 1, null)) as schClassCount',
            'count(IF(grpcls.grpcls_status = ' . static::SCHEDULED . ' and grpcls.grpcls_start_datetime >= "' . date('Y-m-d H:i:s') . '", 1, null)) as upcomingClass'
        ]);
        $data = FatApp::getDb()->fetch($srch->getResultSet());
        return [
            'totalClasses' => FatUtility::int($data['totalClasses'] ?? 0),
            'schClassCount' => FatUtility::int($data['schClassCount'] ?? 0),
            'upcomingClass' => FatUtility::int($data['upcomingClass'] ?? 0)
        ];
    }

    public static function getScheduledClassesCount(string $startTime, string $endTime, int $duration): array
    {
        $srch = new SearchBase(static::DB_TBL, 'gclang');
        $srch->addMultipleFields(['count(*) totalCount', 'min(grpcls_start_datetime) as startTime', 'max(grpcls_end_datetime) as endTime']);
        $srch->addCondition('grpcls_start_datetime', '<', $endTime);
        $srch->addCondition('grpcls_end_datetime', '>', $startTime);
        $srch->addCondition('grpcls_duration', '>', $duration);
        $srch->addCondition('grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        return [
            'totalCount' => $row['totalCount'] ?? 0,
            'startTime' => $row['startTime'] ?? null,
            'endTime' => $row['endTime'] ?? null,
        ];
    }

}
