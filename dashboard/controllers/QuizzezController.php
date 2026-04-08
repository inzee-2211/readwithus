<?php

/**
 * Lessons Controller is used for handling Lessons
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class QuizzezController extends DashboardController
{

    /**
     * Initialize Lessons
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Lessons Search Form
     */
    public function index()
    {
        $frm = LessonSearch::getSearchForm();
        $data = FatApp::getQueryStringData();
        $data['ordles_status'] = $data['ordles_status'] ?? -1;
        if (!empty($data['order_id'])) {
            $data['ordles_lesson_starttime'] = '';
        }
        $frm->fill($data);
        $this->sets([
            'frm' => $frm,
            'setMonthAndWeekNames' => true,
            'userType', $this->siteUserType
        ]);
        $this->_template->addJs([
            'js/jquery.datetimepicker.js',
            'js/jquery.barrating.min.js',
            'js/jquery.cookie.js',
            'js/app.timer.js',
            'js/moment.min.js',
            'js/fullcalendar-luxon.min.js',
            'js/fullcalendar.min.js',
            'js/fullcalendar-luxon-global.min.js',
            'js/fateventcalendar.js',
            'issues/page-js/common.js',
            'plans/page-js/common.js',
            'lessons/page-js/common.js'
        ]);
        $this->_template->render();
    }

    /**
     * Search & List Lessons
     */
    // public function search()
    // {
      /*  $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = LessonSearch::getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new LessonSearch($langId, $userId, $userType);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->applyOrderBy($post);
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $rows = $srch->fetchAndFormat();
        $myDate = new MyDate();
        $myDate->setMonthAndWeekNames();
        $this->sets([
            'post' => $post,
            'myDate' => $myDate,
            'planType' => Plan::PLAN_TYPE_LESSONS,
            'recordCount' => $srch->recordCount(),
            'allLessons' => $srch->groupDates($rows),
        ]);
        $this->_template->render(false, false, 'lessons/search-listing.php');*/

 
/*
$langId = $this->siteLangId;
$userId = $this->siteUserId;
$posts = FatApp::getPostedData();
$posts['pageno'] = $posts['pageno'] ?? 1;
$posts['pagesize'] = $posts['pagesize'] ?? AppConstant::PAGESIZE;

 
$page = $posts['pageno'];
$pageSize = $posts['pagesize'];
$offset = ($page - 1) * $pageSize;

$db = FatApp::getDb();

// Main Query
$query = "
    SELECT qa.id, qa.subtopic_id, ct.topic AS subtopic_name,
           qa.total_questions, qa.total_correct, qa.total_marks,
           qa.marks_obtained, qa.result, qa.attempt_date
    FROM tbl_quiz_attempts qa
    LEFT JOIN course_topics ct ON ct.id = qa.subtopic_id
    WHERE qa.user_id = ".$userId."
    ORDER BY qa.attempt_date DESC
    LIMIT $offset, $pageSize
";

$result = $db->query($query, [$userId]);
 


$quizAttempts = [];
if ($result) {
    $quizAttempts = $db->fetchAll($result);
}
 
// Count Query for Pagination
$countQuery = "
    SELECT COUNT(*) AS total
    FROM tbl_quiz_attempts
    WHERE user_id = $userId
";

$countResult = $db->query($countQuery); // No second param needed here

$totalCount = 0;
if ($countResult) {
    $countRow = $db->fetch($countResult);
    $totalCount = $countRow['total'] ?? 0;
}
  
// Pass to Template
$this->sets([
    'post' => $posts,
    'quizAttempts' => $quizAttempts,
    'recordCount' => $totalCount,
    'pageCount' => ceil($totalCount / $pageSize),
    'allLessons' => $quizAttempts,
]);

$this->_template->render(false, false, 'quizzez/search-listing.php');
*/
 

// $langId = $this->siteLangId;
// $userId = $this->siteUserId;
// $posts = FatApp::getPostedData();
// $posts['pageno'] = $posts['pageno'] ?? 1;
// $posts['pagesize'] = $posts['pagesize'] ?? AppConstant::PAGESIZE;

// $page = $posts['pageno'];
// $pageSize = $posts['pagesize'];
// $offset = ($page - 1) * $pageSize;

// $db = FatApp::getDb();

// // Initialize conditions
// $conditions = " WHERE qa.user_id = $userId ";
// $dateFilter = "";

// // Add date filters if available
// // Add date filters if available
// $dateFilter = '';
// if (!empty($posts['ordles_lesson_starttime']) && !empty($posts['ordles_lesson_endtime'])) {
//     $startDate = date('Y-m-d', strtotime($posts['ordles_lesson_starttime']));
//     $endDate = date('Y-m-d', strtotime($posts['ordles_lesson_endtime']));
//     $dateFilter = " AND qa.attempt_date BETWEEN '" . $startDate . "' AND '" . $endDate . "'";
// }



// // Main Query
// $query = "
//     SELECT qa.id, qa.subtopic_id, ct.topic AS subtopic_name,
//            qa.total_questions, qa.total_correct, qa.total_marks,
//            qa.marks_obtained, qa.result, qa.attempt_date
//     FROM tbl_quiz_attempts qa
//     LEFT JOIN course_topics ct ON ct.id = qa.subtopic_id
//     $conditions
//     $dateFilter
//     ORDER BY qa.attempt_date DESC
//     LIMIT $offset, $pageSize
// ";

// $result = $db->query($query);

// $quizAttempts = [];
// if ($result) {
//     $quizAttempts = $db->fetchAll($result);
// }

// // Count Query for Pagination
// $countQuery = "
//     SELECT COUNT(*) AS total
//     FROM tbl_quiz_attempts qa
//     $conditions
//     $dateFilter
// ";

// $countResult = $db->query($countQuery);
// $totalCount = 0;
// if ($countResult) {
//     $countRow = $db->fetch($countResult);
//     $totalCount = $countRow['total'] ?? 0;
// }

// // Pass to Template
// $this->sets([
//     'post' => $posts,
//     'quizAttempts' => $quizAttempts,
//     'recordCount' => $totalCount,
//     'pageCount' => ceil($totalCount / $pageSize),
//     'allLessons' => $quizAttempts, // For view compatibility
// ]);

// $this->_template->render(false, false, 'quizzez/search-listing.php');

//     }
public function search()
{
    $userId = $this->siteUserId;
    $posts  = FatApp::getPostedData();

    $posts['pageno']   = $posts['pageno']   ?? 1;
    $posts['pagesize'] = $posts['pagesize'] ?? AppConstant::PAGESIZE;

    $page     = (int)$posts['pageno'];
    $pageSize = (int)$posts['pagesize'];

    if ($page < 1)     { $page = 1; }
    if ($pageSize < 1) { $pageSize = AppConstant::PAGESIZE; }

    $offset = ($page - 1) * $pageSize;

    $db = FatApp::getDb();

    // Base: this learner + completed quizzes
    $conditions    = " WHERE qg.quiz_learner_id = " . (int)$userId . " AND qg.status = 1 ";
    $dateFilter    = '';
    $keywordFilter = '';
    $statusFilter  = '';

    // Date filter (quiz_added_on)
    if (!empty($posts['ordles_lesson_starttime']) && !empty($posts['ordles_lesson_endtime'])) {
        $startDate = date('Y-m-d', strtotime($posts['ordles_lesson_starttime']));
        $endDate   = date('Y-m-d', strtotime($posts['ordles_lesson_endtime']));
        $dateFilter = " AND DATE(qg.quiz_added_on) BETWEEN '" . $startDate . "' AND '" . $endDate . "'";
    }

    // Keyword filter (course title / slug)
    if (!empty($posts['keyword'])) {
        $keyword = trim($posts['keyword']);
        // very basic escaping to avoid breaking the SQL string
        $keyword = str_replace("'", "\\'", $keyword);
        $keyword = '%' . $keyword . '%';
        $keywordFilter = " AND (cd.course_title LIKE '" . $keyword . "' OR c.course_slug LIKE '" . $keyword . "')";
    }

    // Quiz status filter
    if (!empty($posts['quiz_status'])) {
        if ($posts['quiz_status'] === 'pass') {
            $statusFilter = " AND qg.percentage >= 50 ";
        } elseif ($posts['quiz_status'] === 'fail') {
            $statusFilter = " AND qg.percentage < 50 ";
        }
    }

    // MAIN QUERY
    $query = "
        SELECT 
            qg.id,
            qg.quiz_id,
            qg.course_id,
            qg.score,
            qg.total_marks,
            qg.percentage,
            qg.quiz_added_on,
            cd.course_title,
            c.course_slug,
            CASE 
                WHEN qg.percentage >= 50 THEN 'Pass'
                ELSE 'Fail'
            END AS result_status
        FROM tbl_quiz_grading qg
        LEFT JOIN tbl_courses c 
            ON c.course_id = qg.course_id
        LEFT JOIN tbl_course_details cd 
            ON cd.course_id = qg.course_id
        $conditions
        $dateFilter
        $keywordFilter
        $statusFilter
        ORDER BY qg.quiz_added_on DESC
        LIMIT $offset, $pageSize
    ";

    $result = $db->query($query);
    $quizResults = [];
    if ($result) {
        $quizResults = $db->fetchAll($result);
    }

    // COUNT QUERY
    $countQuery = "
        SELECT COUNT(*) AS total
        FROM tbl_quiz_grading qg
        LEFT JOIN tbl_courses c 
            ON c.course_id = qg.course_id
        LEFT JOIN tbl_course_details cd 
            ON cd.course_id = qg.course_id
        $conditions
        $dateFilter
        $keywordFilter
        $statusFilter
    ";

    $countResult = $db->query($countQuery);
    $totalCount = 0;
    if ($countResult) {
        $countRow = $db->fetch($countResult);
        $totalCount = (int)($countRow['total'] ?? 0);
    }

    $this->sets([
        'post'        => $posts,
        'quizResults' => $quizResults,
        'recordCount' => $totalCount,
        'pageCount'   => ($pageSize > 0) ? ceil($totalCount / $pageSize) : 0,
    ]);

    $this->_template->render(false, false, 'quizzez/search-listing.php');
}


    /**
     * Render Calendar View Page
     */
    public function calendarView()
    {
        $this->set('nowDate', MyDate::formatDate(date('Y-m-d H:i:s')));
        $this->_template->render(false, false);
    }

    /**
     * Calendar JSON
     */
    public function calendarJson()
    {

        $form = LessonSearch::getSearchForm(true);
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $post['start'] = MyDate::changeDateTimezone($post['start'], $this->siteTimezone, MyUtility::getSystemTimezone());
        $post['end'] = MyDate::changeDateTimezone($post['end'], $this->siteTimezone, MyUtility::getSystemTimezone());
        $srch = new LessonSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->doNotCalculateRecords();
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->addCalendarConditions($post);
        $srch->addMultipleFields([
            'ordles.ordles_lesson_starttime',
            'ordles.ordles_lesson_endtime',
            'ordles.ordles_duration',
            'ordles.ordles_tlang_id',
            'ordles_type'
        ]);
        FatUtility::dieJsonSuccess(['data' => $srch->fetchAndFormatCalendarData()]);
    }

    /**
     * View Lesson Detail
     * 
     * @param int $lessonId
     */
    public function view($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        $srch = new LessonSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $conditions = ['ordles_id' => $lessonId];
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($conditions);
        $srch->addSearchDetailFields();
        $lessons = $srch->fetchAndFormat(true);
        if (count($lessons) < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $lesson = current($lessons);
        $this->set('lesson', $lesson);
        $this->set('setMonthAndWeekNames', true);
        $this->set('userId', $this->siteUserId);
        $this->set('userType', $this->siteUserType);
        $flashcardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD');
        if (!empty($lesson['ordles_metool_id'])) {
            $mettingTool = (new MeetingTool($lesson['ordles_metool_id']))->getDetail();
        } else {
            $mettingTool = MeetingTool::getActiveTool();
        }

        $this->set('activeMettingTool', $mettingTool);
        $this->set('flashcardEnabled', $flashcardEnabled);
        if ($flashcardEnabled) {
            $flashcardSrchFrm = Flashcard::getSearchForm($this->siteLangId);
            $flashcardSrchFrm->fill(['flashcard_type_id' => $lessonId]);
            $this->set('flashcardSrchFrm', $flashcardSrchFrm);
        }

        $this->_template->addJs([
            'issues/page-js/common.js',
            'lessons/page-js/common.js',
            'js/jquery.barrating.min.js',
            'js/jquery.cookie.js',
            'js/app.timer.js',
            'js/moment.min.js',
            'js/fullcalendar-luxon.min.js',
            'js/fullcalendar.min.js',
            'js/fullcalendar-luxon-global.min.js',
            'js/fateventcalendar.js',
            'plans/page-js/common.js',
        ]);
        if ($flashcardEnabled) {
            $this->_template->addJs('js/flashcards.js');
        }
        if ($mettingTool['metool_code'] == MeetingTool::ATOM_CHAT) {
            $this->_template->addJs('js/atom-chat.js');
        }
        $this->_template->render();
    }

    /**
     * Join Meeting
     * 
     * 1. Get Lesson to join
     * 2. Initialize Meeting
     * 3. Join on Meeting Tool
     * 4. Update join Datetime
     */
    public function joinMeeting()
    {
        /* Get Lesson to join */
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        $lessonObj = new Lesson($lessonId, $this->siteUserId, $this->siteUserType);
        if (!$lesson = $lessonObj->getLessonToStart()) {
            FatUtility::dieJsonError($lessonObj->getError());
        }
        if ($this->siteUserType == User::LEARNER && is_null($lesson['ordles_teacher_starttime'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_LET_THE_TEACHER_START_LESSON'));
        }
        $tlangId = FatUtility::int($lesson['ordles_tlang_id']);
        $lesson['ordles_tlang_name'] = TeachLanguage::getLangById($tlangId, $this->siteLangId);
        /* Initialize Meeting */
        $meetingObj = new Meeting($this->siteUserId, $this->siteUserType);
        if (!$meetingObj->initMeeting($lesson['ordles_metool_id'])) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* Join on Meeting Tool */
        if (!$meeting = $meetingObj->joinLesson($lesson)) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* Update join datetime */
        $lesson['ordles_metool_id'] = $meeting['meet_metool_id'];
        if (!$lessonObj->start($lesson)) {
            FatUtility::dieJsonError($lessonObj->getError());
        }
        FatUtility::dieJsonSuccess(['meeting' => $meeting, 'msg' => Label::getLabel('LBL_JOINING_PLEASE_WAIT')]);
    }

    /**
     * End Meeting
     * 
     * 1. Get Lesson To Complete
     * 2. Initialize Meeting Tool
     * 3. End on Meeting Tool
     * 4. Mark Meeting Complete
     */
    public function endMeeting()
    {
        /* Get Lesson To Complete */
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        $lessonObj = new Lesson($lessonId, $this->siteUserId, $this->siteUserType);
        if (!$lesson = $lessonObj->getLessonToComplete()) {
            FatUtility::dieJsonError($lessonObj->getError());
        }
        /* Initialize Meeting Tool */
        $meetingObj = new Meeting($this->siteUserId, $this->siteUserType);
        if (!$meetingObj->initMeeting($lesson['ordles_metool_id'])) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* End on Meeting Tool */
        if (!$meetingObj->endMeeting($lessonId, AppConstant::LESSON)) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* Mark Meeting Complete */
        if (!$lessonObj->complete($lesson)) {
            FatUtility::dieJsonError($lessonObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Render Schedule Form
     */
    public function scheduleForm()
    {
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        $lesson = new Lesson($lessonId, $this->siteUserId, $this->siteUserType);
        if (!$record = $lesson->getLessonToSchedule()) {
            FatUtility::dieJsonError($lesson->getError());
        }
        $subStartDate = '';
        $subdays = 490; // 70 weeks;
        if ($record['ordles_type'] == LESSON::TYPE_SUBCRIP) {
            $subscription = Subscription::getSubsByOrderId($record['ordles_order_id'],
                            ['ordsub_startdate', 'DATEDIFF(ordsub_enddate, ordsub_startdate) as subdays']);
            if (!empty($subscription['ordsub_startdate'])) {
                $subStartDate = MyDate::formatDate($subscription['ordsub_startdate']);
                $subdays = $subscription['subdays'];
            }
        }
        $record['teacher_country_code'] = Country::getAttributesById($record['teacher_country_id'], 'country_code');
        $teacherSetting = UserSetting::getSettings($record['ordles_teacher_id']);
        $form = $this->getScheduleForm();
        $form->fill($record);
        $this->sets([
            'form' => $form,
            'subStartDate' => $subStartDate,
            'subdays' => $subdays,
            'teacherBookingBefore' => FatUtility::int($teacherSetting['user_book_before'] ?? 0),
            'lesson' => $record
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Schedule Setup
     */
    public function scheduleSetup()
    {
        $posts = FatApp::getPostedData();
        $frm = $this->getScheduleForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $lesson = new Lesson($post['ordles_id'], $this->siteUserId, $this->siteUserType);
        if (!$lesson->schedule($post, $this->siteLangId)) {
            FatUtility::dieJsonError($lesson->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Render Reschedule Form
     */
    public function rescheduleForm()
    {
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        $lesson = new Lesson($lessonId, $this->siteUserId, $this->siteUserType);
        if (!$record = $lesson->getLessonToReschedule($this->siteUserType)) {
            FatUtility::dieJsonError($lesson->getError());
        }
        $subStartDate = '';
        $subdays = 490; // 70 weeks;
        if ($record['ordles_type'] == LESSON::TYPE_SUBCRIP) {
            $subscription = Subscription::getSubsByOrderId($record['ordles_order_id'],
                            ['ordsub_startdate', 'DATEDIFF(ordsub_enddate, ordsub_startdate) as subdays']);
            if (!empty($subscription['ordsub_startdate'])) {
                $subStartDate = MyDate::formatDate($subscription['ordsub_startdate']);
                $subdays = $subscription['subdays'];
            }
        }
        $teacherSetting = UserSetting::getSettings($record['ordles_teacher_id']);
        $teacherBookingBefore = FatUtility::int($teacherSetting['user_book_before'] ?? 0);
        $record['teacher_country_code'] = Country::getAttributesById($record['teacher_country_id'], 'country_code');
        $form = $this->getRescheduleForm();
        $form->fill($record);
        $this->sets([
            'form' => $form,
            'subdays' => $subdays,
            'subStartDate' => $subStartDate,
            'teacherBookingBefore' => $teacherBookingBefore,
            'lesson' => $record
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Reschedule Setup
     */
    public function rescheduleSetup()
    {
        $posts = FatApp::getPostedData();
        $frm = $this->getRescheduleForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $lesson = new Lesson($post['ordles_id'], $this->siteUserId, $this->siteUserType);
        if (!$lesson->reschedule($post, $this->siteLangId)) {
            FatUtility::dieJsonError($lesson->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Render Cancel Form
     */
    public function cancelForm()
    {
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        $lesson = new Lesson($lessonId, $this->siteUserId, $this->siteUserType);
        if (!$record = $lesson->getLessonToCancel(false)) {
            FatUtility::dieJsonError($lesson->getError());
        }
        $refundPercentage = 0;
        if (FatUtility::FLOAT($record['order_total_amount']) > 0) {
            $refundPercentage = $lesson->getRefundPercentage($record['ordles_status'], $record['ordles_lesson_starttime']);
        }
        $frm = $this->getCancelForm($refundPercentage);
        $frm->fill($record);
        $this->set('frm', $frm);
        $this->set('lesson', $record);
        $this->_template->render(false, false);
    }

    /**
     * Cancel Setup
     */
    public function cancelSetup()
    {
        $posts = FatApp::getPostedData();
        $frm = $this->getCancelForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $lesson = new Lesson($post['ordles_id'], $this->siteUserId, $this->siteUserType);
        if (!$lesson->cancel($post, $this->siteLangId)) {
            FatUtility::dieJsonError($lesson->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_LESSON_CANCELLED_SUCCESSFULLY'));
    }

    /**
     * Render Feedback Form
     */
    public function feedbackForm()
    {
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        $lesson = new Lesson($lessonId, $this->siteUserId, $this->siteUserType);
        if (!$record = $lesson->getLessonToFeedback()) {
            FatUtility::dieJsonError($lesson->getError());
        }
        $frm = RatingReview::getFeedbackForm();
        $record['ratrev_type_id'] = $lessonId;
        $frm->fill($record);
        $this->set('frm', $frm);
        $this->set('lesson', $record);
        $this->_template->render(false, false);
    }

    /**
     * Feedback Setup
     */
    public function feedbackSetup()
    {
        $posts = FatApp::getPostedData();
        $frm = RatingReview::getFeedbackForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $lesson = new Lesson($post['ratrev_type_id'], $this->siteUserId, $this->siteUserType);
        $post['ratrev_lang_id'] = $this->siteLangId;
        if (!$lesson->feedback($post)) {
            FatUtility::dieJsonError($lesson->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Schedule Form
     * 
     * @return Form
     */
    private function getScheduleForm(): Form
    {
        $frm = new Form('scheduleFrm');
        $frm->addHiddenField('', 'ordles_id')->requirements()->setRequired();
        $startTime = $frm->addHiddenField('', 'ordles_lesson_starttime');
        $startTime->requirements()->setRequired();
        $endTime = $frm->addHiddenField('', 'ordles_lesson_endtime');
        $endTime->requirements()->setRequired();
        $endTime->requirements()->setCompareWith('ordles_lesson_starttime', 'gt');
        $startTime->requirements()->setCompareWith('ordles_lesson_endtime', 'lt');
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Confirm_It!'));
        return $frm;
    }

    /**
     * Get Reschedule Form
     * 
     * @return Form
     */
    private function getRescheduleForm(): Form
    {
        $frm = new Form('rescheduleFrm');
        $frm->addHiddenField('', 'ordles_id')->requirements()->setRequired();
        $startTime = $frm->addHiddenField('', 'ordles_lesson_starttime');
        $endTime = $frm->addHiddenField('', 'ordles_lesson_endtime');
        if ($this->siteUserType == User::LEARNER) {
            $startTime->requirements()->setRequired();
            $endTime->requirements()->setRequired();
            $endTime->requirements()->setCompareWith('ordles_lesson_starttime', 'gt');
            $startTime->requirements()->setCompareWith('ordles_lesson_endtime', 'lt');
        }
        $frm->addTextArea(Label::getLabel('LBL_RESCHEDULE_REASON'), 'comment')->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_CONFIRM_IT!'));
        return $frm;
    }

    /**
     * Get Cancel Form
     * 
     * @param float $refundPercentage
     * @return Form
     */
    private function getCancelForm(float $refundPercentage = 0): Form
    {
        $frm = new Form('cancelFrm');
        $comment = $frm->addTextArea(Label::getLabel('LBL_COMMENTS'), 'comment');
        $comment->requirements()->setLength(10, 200);
        $comment->requirements()->setRequired();
        $frm->addHiddenField('', 'ordles_id')->requirements()->setRequired();
        if ($refundPercentage > 0) {
            $frm->addHtml('', 'note_text', '<spam class="-color-primary">' . sprintf(Label::getLabel('LBL_Refund_Would_Be_%s_Percent.'), $refundPercentage) . '</spam>');
        }
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }

    /**
     * Check Lesson Status
     * 
     * @param int $lessonId
     */
    public function checkLessonStatus($lessonId = 0)
    {
        $fields = ['ordles_teacher_starttime', 'ordles_lesson_endtime', 'ordles_status', 'ordles_student_starttime'];
        $srch = new LessonSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->addCondition('ordles_id', '=', FatUtility::int($lessonId));
        $srch->applyPrimaryConditions();
        $srch->addSearchDetailFields();
        $srch->addMultipleFields($fields);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $lesson = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($lesson)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST_PLEASE_REFRESH_PAGE'));
        }
        $status = $lesson['ordles_status'];
        if (Lesson::SCHEDULED == $status && User::TEACHER == $this->siteUserType) {
            if (empty($lesson['ordles_teacher_starttime']) && strtotime($lesson['ordles_lesson_endtime']) > time()) {
                FatUtility::dieJsonSuccess(['lessonStatus' => $status, 'msg' => Label::getLabel('LBL_PLEASE_JOIN_LESSON_AND_START_LESSON')]);
            } elseif (!empty($lesson['ordles_teacher_starttime']) && strtotime($lesson['ordles_lesson_endtime']) < time()) {
                FatUtility::dieJsonError(['lessonStatus' => $status, 'msg' => Label::getLabel('LBL_TIME_IS_OVER_PLEASE_END_THE_LESSON')]);
            }
        } elseif (Lesson::SCHEDULED == $status && User::LEARNER == $this->siteUserType) {
            if (empty($lesson['ordles_student_starttime']) && !empty($lesson['ordles_teacher_starttime']) && strtotime($lesson['ordles_lesson_endtime']) > time()) {
                FatUtility::dieJsonSuccess(['lessonStatus' => $status, 'msg' => Label::getLabel('LBL_TEACHER_HAS_JOINED_PLEASE_JOIN_LESSON')]);
            } elseif (!empty($lesson['ordles_student_starttime']) && empty($lesson['ordles_student_endtime']) && strtotime($lesson['ordles_lesson_endtime']) < time()) {
                FatUtility::dieJsonError(['lessonStatus' => $status, 'msg' => Label::getLabel('LBL_TIME_IS_OVER_LESSON_WILL_BE_ENDED_SOON')]);
            }
        } elseif (Lesson::COMPLETED == $status) {
            $msg = (User::LEARNER == $this->siteUserType) ? 'LBL_TEACHER_HAS_ENDED_THE_LESSON' : 'LBL_LEARNER_HAS_ENDED_THE_LESSON';
            FatUtility::dieJsonError(['msg' => Label::getLabel($msg), 'lessonStatus' => $status]);
        } elseif (Lesson::CANCELLED == $status) {
            $msg = (User::LEARNER == $this->siteUserType) ? 'LBL_TEACHER_HAS_CANCELLED_THE_LESSON' : 'LBL_LEARNER_HAS_CANCELLED_THE_LESSON';
            FatUtility::dieJsonError(['msg' => Label::getLabel($msg), 'lessonStatus' => $status]);
        } elseif (Lesson::UNSCHEDULED == $status) {
            $msg = (User::LEARNER == $this->siteUserType) ? 'LBL_TEACHER_HAS_UNSCHEDULED_THE_LESSON' : 'LBL_LEARNER_HAS_UNSCHEDULED_THE_LESSON';
            FatUtility::dieJsonError(['msg' => Label::getLabel($msg), 'lessonStatus' => $status]);
        }
        FatUtility::dieJsonSuccess(['msg' => '', 'lessonStatus' => $status]);
    }

    /**
     * Get Upcoming Lesson
     * 
     * @return array
     */
    public function upcoming()
    {
        $pageSize = FatApp::getPostedData('pagesize', FatUtility::VAR_INT, AppConstant::PAGESIZE);
        $viewType = FatApp::getPostedData('view', FatUtility::VAR_INT, AppConstant::VIEW_LISTING);
        $srch = new LessonSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->addCondition('ordles_lesson_starttime', '>=', date('Y-m-d H:i:s'));
        $srch->addCondition('ordles_status', '=', Lesson::SCHEDULED);
        $srch->addOrder('ordles_lesson_starttime');
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->setPageSize($pageSize);
        $allLessons = $srch->fetchAndFormat();
        $view = 'lessons/upcoming.php';
        if ($viewType == AppConstant::VIEW_SHORT) {
            $view = 'lessons/short-detail-listing.php';
            $allLessons = $srch->groupDates($srch->fetchAndFormat());
        }
        $this->set('allLessons', $allLessons);
        $this->_template->render(false, false, $view);
    }

}
