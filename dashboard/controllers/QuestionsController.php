<?php

/**
 * Classes Controller is used for handling Classes on Teacher and Learner Dashboards
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class QuestionsController extends DashboardController
{

    /**
     * Initialize ClassesController 
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $userId = $this->siteUserId;
        // if($userId!=449)
        // {
        //  FatUtility::exitWithErrorCode(404);
        // }
         
        $this->_template->addJs([
            'js/teacherLessonCommon.js',
            'js/jquery.datetimepicker.js',
            'issues/page-js/common.js',
            'classes/page-js/common.js',
            'js/jquery.cookie.js',
            'js/app.timer.js',
            'plans/page-js/common.js',
            'js/jquery.barrating.min.js',
            'js/moment.min.js',
            'js/fullcalendar-luxon.min.js',
            'js/fullcalendar.min.js',
            'js/fullcalendar-luxon-global.min.js',
            'js/fateventcalendar.js'
        ]);
        $frm = QuestionSearch::getSearchForm($this->siteUserType);
        $postData = FatApp::getQueryStringData();
        if (!empty($postData['package_id'])) {
            $postData = array_merge($postData, [
                'ordcls_status' => '',
                'grpcls_status' => '',
                'grpcls_start_datetime' => ''
            ]);
        }
        $frm->fill($postData);
        $this->sets([
            'frm' => $frm, 'setMonthAndWeekNames' => true,
            'upcomingClass' => $this->getUpcomingClass()
        ]);
        $this->_template->render();
    }

    /**
     * Search & List Classes
     */
    public function search()
    {
   
      
        if (isset($_POST['grpcls_subcate_id']) && !empty($_POST['grpcls_subcate_id'])) {
            $grpcls_subcate_id=$_POST['grpcls_subcate_id']; 
        }
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = QuestionSearch::getSearchForm($userType);
         if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new QuestionSearch($langId, $userId, $userType);
        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'subcate.cate_id = question  .question_subcat', 'subcate');
       $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'cate.cate_id = question  .question_cat', 'cate');
 
            $srch->addMultipleFields([
            '*',           // Select all fields from questions table
             'cate.cate_identifier as  catname',
           'subcate.cate_identifier as subcatname'
            ]);
 
            if (isset($post['title']) && !empty($post['title'])) {
            $srch->addCondition('question_title', 'LIKE', '%' . $post['title'] . '%');
            }
            if (isset($post['grpcls_tlang_id']) && !empty($post['grpcls_tlang_id'])) {
            $srch->addCondition('question_type', 'LIKE', '%' . $post['grpcls_tlang_id'] . '%');
            }

            if (isset($post['grpcls_q_cat']) && !empty($post['grpcls_q_cat'])) {
            $srch->addCondition('question_cat', '=', $post['grpcls_q_cat']);
            }
            if (isset($grpcls_subcate_id) && !empty($grpcls_subcate_id)) {
            $srch->addCondition('question_subcat', '=', $grpcls_subcate_id);
            }
            if (isset($userId) && !empty($userId)) {
            $srch->addCondition('question_added_by', '=', $userId);
            }

        $srch->addCondition('question.question_deleted', '=', 0);
    
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
       
        $rows = $srch->fetchAndFormat();
  
        $myDate = new MyDate();
        $myDate->setMonthAndWeekNames();
        $this->sets([
            'post' => $post,
            'myDate' => $myDate,
            'recordCount' => $srch->recordCount(),
            'planType' => Plan::PLAN_TYPE_CLASSES,
            'allClasses' => $srch->groupDates($rows),
        ]);
        $this->_template->render(false, false, 'questions/search-listing.php');
    }

    public function searchquiz()
    {
         
        $db = FatApp::getDb();
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        
        // Get posted data
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
     //echo '<pre>';print_r($posts); 
     $userId = $this->siteUserId;
     
        $frm = QuestionSearch::getSearchForm($userType);
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        // Initialize search base
        $srch = new SearchBase('tbl_quiz_grading', 'qg');
         $srch->joinTable('tbl_users', 'INNER JOIN', 'u.user_id = qg.quiz_learner_id', 'u');
         $srch->joinTable('tbl_course_details', 'INNER JOIN', 'qg.course_id = c.course_id', 'c');
       $srch->addMultipleFields([
            'qg.*',                         
            'u.user_first_name',            
            'c.course_title',               
        ]);
        
        if (isset($userId) && !empty($userId)) {
            $srch->addCondition('qg.quiz_tutor_id', '=', $userId);
            }
        // Set pagination
      $srch->addOrder('qg.id', 'DESC');
        $srch->setPageSize($posts['pagesize']);
        $srch->setPageNumber($posts['pageno']);
 
         
        $resultSet = $srch->getResultSet();
     
        if ($resultSet) {
            $rows = $db->fetchAll($resultSet);
            
        }  
        
        $this->sets([
            'post' => $post,
           // 'recordCount' => count($rows),
            'recordCount' => $srch->recordCount(),
             'allClasses' =>  $rows,
        ]);
        $this->_template->render(false, false, 'quizreview/search-listing.php');
    }


    /**
     * Render Class Detail View
     * 
     * @param type $classId
     */
    public function view($classId)
    {
        $classId = FatUtility::int($classId);
        $condition = ['grpcls_id' => $classId];
        if ($this->siteUserType == User::LEARNER) {
            $condition = ['ordcls_id' => $classId];
        }
        $srch = new ClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($condition);
        $srch->addSearchListingFields();
        $srch->setPageSize(1);
        $classes = $srch->fetchAndFormat(true);
        if (empty($classes)) {
            FatUtility::exitWithErrorCode(404);
        }
        $class = current($classes);
        $learners = [];
        if ($this->siteUserType == User::TEACHER) {
            if (empty($class['grpcls_booked_seats'])) {
                FatUtility::exitWithErrorCode(404);
            }
            $learners = OrderClass::getOrdClsByGroupId($classId, [], [OrderClass::SCHEDULED, OrderClass::COMPLETED]);
        }
        $flashcardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD');
        if ($flashcardEnabled) {
            $flashcardSrchFrm = Flashcard::getSearchForm($this->siteLangId);
            $flashcardSrchFrm->fill(['flashcard_type_id' => $classId]);
            $this->set('flashcardSrchFrm', $flashcardSrchFrm);
        }

        if (!empty($class['grpcls_metool_id'])) {
            $mettingTool = (new MeetingTool($class['grpcls_metool_id']))->getDetail();
        } else {
            $mettingTool = MeetingTool::getActiveTool();
        }
        $this->sets([
            'class' => $class,
            'classId' => $classId,
            'learners' => $learners,
            'mettingTool' => $mettingTool,
            'flashcardEnabled' => $flashcardEnabled
        ]);
        $this->_template->addJs([
            'js/jquery.cookie.js',
            'js/app.timer.js',
            'issues/page-js/common.js',
            'js/jquery.barrating.min.js',
            'classes/page-js/common.js',
            'plans/page-js/common.js'
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
     * Render Calendar View
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
        $form = ClassSearch::getSearchForm($this->siteUserType, true);
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }

        $post['start'] = MyDate::changeDateTimezone($post['start'], $this->siteTimezone, MyUtility::getSystemTimezone());
        $post['end'] = MyDate::changeDateTimezone($post['end'], $this->siteTimezone, MyUtility::getSystemTimezone());
        $srch = new ClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->doNotCalculateRecords();
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->applyCalendarConditions($post);
        $srch->addMultipleFields([
            'IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as title',
            'grpcls.grpcls_start_datetime as start',
            'grpcls.grpcls_end_datetime as end'
        ]);
        $db = FatApp::getDb();
        $resultSet = $srch->getResultSet();
        $response = [];
        while ($row = $db->fetch($resultSet)) {
            $row['start'] = MyDate::formatDate($row['start']);
            $row['end'] = MyDate::formatDate($row['end']);
            $response[] = $row;
        }
        FatUtility::dieJsonSuccess(['data' => $response]);
    }

    /**
     * Join Meeting
     * 
     * 1. Get Class to join
     * 2. Initialize Meeting
     * 3. Join on Meeting Tool
     * 4. Add Join Datetime
     */
    public function joinMeeting()
    {
        /* Get Class to join */
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
        if ($this->siteUserType == User::LEARNER) {
            $classObj = new OrderClass($classId, $this->siteUserId, $this->siteUserType);
        } else {
            $classObj = new GroupClass($classId, $this->siteUserId, $this->siteUserType);
        }
        if (!$class = $classObj->getClassToStart($this->siteLangId)) {
            FatUtility::dieJsonError($classObj->getError());
        }
        if ($this->siteUserType == User::LEARNER && is_null($class['grpcls_teacher_starttime'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_LET_THE_TEACHER_START_CLASS'));
        }
        /* Initialize Meeting */
        $meetingObj = new Meeting($this->siteUserId, $this->siteUserType);
        if (!$meetingObj->initMeeting($class['grpcls_metool_id'])) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* Join on Meeting Tool */
        if (!$meeting = $meetingObj->joinClass($class)) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        $class['grpcls_metool_id'] = $meeting['meet_metool_id'];
        /* Add join datetime */
        if (!$classObj->start($class)) {
            FatUtility::dieJsonError($classObj->getError());
        }
        FatUtility::dieJsonSuccess(['meeting' => $meeting, 'msg' => Label::getLabel('LBL_JOINING_PLEASE_WAIT')]);
    }

    /**
     * End Meeting
     * 
     * 1. Get Class to Complete
     * 2. Initialize Meeting Tool
     * 3. End on Meeting Tool
     * 4. Mark Meeting Complete
     */
    public function endMeeting()
    {
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
        if ($this->siteUserType == User::LEARNER) {
            $classObj = new OrderClass($classId, $this->siteUserId, $this->siteUserType);
        } else {
            $classObj = new GroupClass($classId, $this->siteUserId, $this->siteUserType);
        }
        /* Get Class To Complete */
        if (!$class = $classObj->getClassToComplete()) {
            FatUtility::dieJsonError($classObj->getError());
        }
        /* Initialize Meeting Tool */
        $meetingObj = new Meeting($this->siteUserId, $this->siteUserType);
        if (!$meetingObj->initMeeting($class['grpcls_metool_id'])) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* End on Meeting Tool */
        if (!$meetingObj->endMeeting($classId, AppConstant::GCLASS)) {
            FatUtility::dieJsonError($meetingObj->getError());
        }
        /* Mark Meeting Complete */
        if (!$classObj->complete($class)) {
            FatUtility::dieJsonError($classObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Render Cancel Class Form
     */
   public function cancelForm()
{
    $courseId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
    $srch = new QuestionSearch($this->siteLangId, 0, User::SUPPORT);

    $srch->addCondition('question.question_id', '=', $courseId);
    $srch->applyPrimaryConditions();

    $courses = $srch->fetchAndFormat();

    if (empty($courses)) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    $db = FatApp::getDb();

    // 1) Soft delete the question
    $data  = ['question_deleted' => 1];
    $where = ['smt' => 'question_id = ?', 'vals' => [$courseId]];

    if (!$db->updateFromArray('tbl_questions', $data, $where)) {
        FatUtility::dieJsonError($db->getError());
    }

    // 2) Remove mappings from quizzes
    $db->deleteRecords('tbl_quiz_questions', [
        'smt'  => 'question_id = ?',
        'vals' => [$courseId],
    ]);

    FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Updated_Successfully'));
}

public function bulkDelete()
{
    // Get raw posted array (no type filter here!)
    $questionIds = FatApp::getPostedData('question_ids', null, []);

    if (!is_array($questionIds) || empty($questionIds)) {
        FatUtility::dieJsonError(Label::getLabel('LBL_NO_QUESTION_SELECTED'));
    }

    // Normalize IDs (int-cast + unique + non-empty)
    $questionIds = array_unique(
        array_filter(
            array_map('intval', $questionIds)
        )
    );

    if (empty($questionIds)) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    $db     = FatApp::getDb();
    $userId = $this->siteUserId;
    $idList = implode(',', $questionIds); // safe – all ints

    $db->startTransaction();

    /* 1) Remove mappings from quizzes */
    if (
        !$db->deleteRecords(
            'tbl_quiz_questions',
            ['smt' => "question_id IN ($idList)", 'vals' => []]
        )
    ) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError($db->getError());
    }

    /* 2) Remove uploaded files for these questions (if any) */
    if (class_exists('Afile')) {
        $file = new Afile(Afile::TYPE_LESSON_QUESTIONS_FILE);
        foreach ($questionIds as $qid) {
            if (!$file->removeFile($qid, 0, true) && $file->getError()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($file->getError());
            }
        }
    }

    /* 3) Hard delete questions (but only those created by this teacher) */
    if (
        !$db->deleteRecords(
            'tbl_questions',
            [
                'smt'  => "question_id IN ($idList) AND question_added_by = ?",
                'vals' => [$userId],
            ]
        )
    ) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError($db->getError());
    }

    $db->commitTransaction();

    FatUtility::dieJsonSuccess(Label::getLabel('LBL_QUESTIONS_DELETED_SUCCESSFULLY'));
}


    /**
     * Cancel Class
     */
    public function cancelSetup()
    {
        $frm = $this->getCancelForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $classId = $post['classId'];
        if ($this->siteUserType == User::LEARNER) {
            $class = new OrderClass($classId, $this->siteUserId, $this->siteUserType);
        } else {
            $class = new GroupClass($classId, $this->siteUserId, $this->siteUserType);
        }
        if (!$class->cancel($post['comment'], $this->siteLangId)) {
            FatUtility::dieJsonError($class->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Render Feedback Form
     */
    public function feedbackForm()
    {
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
        $class = new OrderClass($classId, $this->siteUserId, $this->siteUserType);
        if (!$record = $class->getClassToFeedback()) {
            FatUtility::dieJsonError($class->getError());
        }
        $frm = RatingReview::getFeedbackForm();
        $record['ratrev_type_id'] = $classId;
        $frm->fill($record);
        $this->sets(['frm' => $frm, 'class' => $record]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Feedback
     */
    public function feedbackSetup()
    {
        $posts = FatApp::getPostedData();
        $frm = RatingReview::getFeedbackForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $classId = FatApp::getPostedData('ratrev_type_id', FatUtility::VAR_INT, 0);
        $class = new OrderClass($classId, $this->siteUserId, $this->siteUserType);
        $post['ratrev_lang_id'] = $this->siteLangId;
        if (!$class->feedback($post)) {
            FatUtility::dieJsonError($class->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Cancel Form
     * 
     * @return Form
     */
    private function getCancelForm(): Form
    {
        $frm = new Form('cancelFrm');
        $comment = $frm->addTextArea(Label::getLabel('LBL_COMMENTS'), 'comment');
        $comment->requirements()->setLength(10, 300);
        $comment->requirements()->setRequired();
        $frm->addHiddenField('', 'classId')->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }

    //bulk upload form
    public function bulkForm()
{
    $this->_template->render(false, false, 'questions/bulk-form.php');
}

public function downloadSample()
{
    $headers = ['title','type','marks','category_id','subcategory_id','description','math_equation','hint','option_1','option_2','option_3','option_4','correct_answers','image'];
    $rows = [
      ['Area of circle?',1,2,3,7,'Find area of circle','\\pi r^2','Use π≈3.14','πr^2','2πr','r^2','π/2','1','circle.png'],
      ['Multiple correct demo',2,3,3,7,'Pick valid primes','','','2','3','4','6','1,2','https://example.com/q2.png'],
      ['Short text Q',3,5,3,7,'Write formula','','', '','','','','πr^2','']
    ];
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=questions_sample.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    foreach ($rows as $row) fputcsv($out, $row);
    fclose($out); exit;
}

public function bulkSetup()
{
    $db = FatApp::getDb();
    $db->startTransaction();

    try {
        if (empty($_FILES['bulk_csv']['tmp_name'])) {
            FatUtility::dieJsonError('Please upload a CSV file.');
        }
        $importer = new BulkQuestionImporter($this->siteUserId, $this->siteUserType, $this->siteLangId);

        $stats = $importer->import($_FILES['bulk_csv'], $_FILES['bulk_images'] ?? null);
        $db->commitTransaction();

        $msg = "Imported {$stats['success']} of {$stats['total']} row(s).";
        if (!empty($stats['errors'])) {
            $msg .= " Issues:\n- " . implode("\n- ", $stats['errors']);
        }
        FatUtility::dieJsonSuccess(['msg' => nl2br($msg)]);

    } catch (Exception $e) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError($e->getMessage());
    }
}
//ends bulk upload form
    /**
     * Render Add|Edit Question Form
     */
    public function addForm()
    {
        
         
        $form = $this->getAddForm();
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
         
        $isClassBooked = false;
        $classData='';
        if ($classId > 0) {
           
            $groupClass = new QuestionClass($classId, $this->siteUserId, $this->siteUserType);
            
            if (!$classData = $groupClass->getClassToSave()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
             
             $classData['grpcls_title'] = $classData['question_title'];
             $classData['grpcls_tlang_id'] = $classData['question_type'];
             $classData['grpcls_description'] = $classData['question_desc'];
             $classData['image'] = $classData['question_image'];
             $classData['grpcls_description_math'] = $classData['question_math_equation'];
             $classData['grpcls_total_marks'] = $classData['question_marks'];
             $classData['grpcls_hint'] = $classData['question_hint'];
             $classData['course_cate_id'] = $classData['question_cat'];
             $classData['course_subcate_ida'] = $classData['question_subcat'];
            $form->fill($classData);
            //$min = max($classData['grpcls_booked_seats'], 1);
           // $form->getField('grpcls_total_seats')->requirements()->setRange($min, FatApp::getConfig('CONF_GROUP_CLASS_MAX_LEARNERS'));
        }
       // echo '<pre>';print_r($classData);die;
        $this->set('frm', $form);
        $this->set('data', $classData);
        $this->set('classId', $classId);
        $this->set('isClassBooked', $isClassBooked);
        $this->set('languages', Language::getAllNames(false));
        $this->_template->render(false, false);
    }
    public function submitTeacherResult()
    {
           $totalGrades = array_sum($_POST['gradesData']);
            $gradesJson = json_encode($_POST['gradesData']);
            $totalscore=$_POST['totalscore']+$totalGrades;

            $TotalMArks=$_POST['totalMarks'];
            $Pass_percentage=$_POST['Pass_percentage'];
            $quiz_learner_id=$_POST['quiz_learner_id'];
            $quiz_lecture_id=$_POST['quiz_lecture_id'];

           $percentage = ($totalscore / $TotalMArks) * 100;

            if ($percentage >= $Pass_percentage) {
                $status = 2;
            } else {
                $status = 1;
            }
 
            $db = FatApp::getDb(); // Get the database connection instance
            $data = ['score' => $totalscore,'manual_check'=>$gradesJson,'status'=>1]; // Column and new value
  
            $where = ['smt' => 'id = ?', 'vals' => [$_POST['radeId']]];

            // Perform the update
            if (!$db->updateFromArray('tbl_quiz_grading', $data, $where)) {
            FatUtility::dieJsonError($db->getError());
            }




            $db = FatApp::getDb(); // Get the database connection instance
            $data = ['status' => $status]; // Columns and new values

            // Multiple conditions in WHERE clause
            $where = [
            'smt' => 'quiz_learner_id = ? AND quiz_lecture_id = ?', // Add multiple conditions
            'vals' => [$quiz_learner_id, $quiz_lecture_id] // Provide corresponding values
            ];

            // Perform the update
            if (!$db->updateFromArray('tbl_quiz_attempt', $data, $where)) {
            FatUtility::dieJsonError($db->getError());
            }





         FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Updated_Successfully'));
         
    }

    public function addFormQuiz()
    {

        
        $form = $this->getAddForm();
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
        
        $isClassBooked = false;
         
        if ($classId > 0) {
           
            $groupClass = new QuestionClass($classId, $this->siteUserId, $this->siteUserType);
            
            if (!$classData = $groupClass->getClassToSaveQuiz()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }

 
            $form->fill($classData);
       }
       
  
       $quiz_submit_data = $classData['quiz_submit_data'];
       $quiz_autoresult_data = $classData['quiz_autoresult_data'];
    
        $submit_data = json_decode($quiz_submit_data, true);
        
     // echo '<pre>';print_r($quiz_autoresult_data);die;
        $submitted_answers = $submit_data['answers'];
// Array to hold question and answer details
$questionsDetails = [];

// Loop through submitted answers
 $Questionmarks=0;
foreach ($submitted_answers as $questionId => $submittedAnswer) {
    // Fetch question details
    $srch = new SearchBase('tbl_questions', 'q');
    $srch->addCondition('q.question_id', '=', $questionId);
    $srch->addFld('q.question_id, q.question_title,q.question_marks, q.question_type, 
                   q.question_option_1, q.question_option_2, q.question_option_3, q.question_option_4, 
                   q.question_answers');
    $rs = $srch->getResultSet();
    $questionData = FatApp::getDb()->fetch($rs);
 
    if ($questionData) {
        // Format question options
        $options = [
            "1" => $questionData['question_option_1'],
            "2" => $questionData['question_option_2'],
            "3" => $questionData['question_option_3'],
            "4" => $questionData['question_option_4']
        ];
       
        
        // Resolve text for correct answer and submitted answer
         
        $correctAnswerText = is_array(json_decode($questionData['question_answers'], true)) 
            ? implode(", ", array_map(fn($id) => $options[$id], json_decode($questionData['question_answers'], true))) 
            : $options[$questionData['question_answers']] ?? $questionData['question_answers'];

        $submittedAnswerText = is_array($submittedAnswer) 
            ? implode(", ", array_map(fn($id) => $options[$id] ?? $id, $submittedAnswer)) 
            : $options[$submittedAnswer] ?? $submittedAnswer;

            $decodedquiz_autoresult_data = json_decode($quiz_autoresult_data, true);
            $questionId=$questionData['question_id'];
            // echo '<pre>';print_r($decodedquiz_autoresult_data);die;
            // echo  $questionId; 
            if (isset($decodedquiz_autoresult_data['autoCheckedQuestions'][$questionId])) {
                $status =$decodedquiz_autoresult_data['autoCheckedQuestions'][$questionId]['status'];
                $correctanswer =$decodedquiz_autoresult_data['autoCheckedQuestions'][$questionId]['correctanswer'];
                $marksobtained =$decodedquiz_autoresult_data['autoCheckedQuestions'][$questionId]['marks'];
            } else {
                $status ='';
                $marksobtained =0;
            }
            
        // Add question details and answers to the results
        $questionsDetails[] = [
            'question_id' =>     $questionData['question_id'],
            'question_title' =>  $questionData['question_title'],
            'question_marks' =>  $questionData['question_marks'],
            'options' =>         $options,
            'correct_answer' =>  $correctAnswerText,
            'submitted_answer' => $submittedAnswerText,
            'question_type' =>    $questionData['question_type'],
            'status' =>    $status,
            'explanation' =>    $correctanswer,
            'marksobtained' =>    $marksobtained,
            'is_correct' =>      $correctAnswerText == $submittedAnswerText
        ];
    }
}
 
 
        $this->set('frm', $form);
        $this->set('classId', $classId);
        $this->set('questionsDetails', $questionsDetails);
        $this->set('formdata', $classData);
        $this->set('isClassBooked', $isClassBooked);
        $this->set('languages', Language::getAllNames(false));
        //$this->_template->render(false, false);
        $this->_template->render(false, false, 'quizreview/add-form.php');
    }

    public function quizForm()
    {
        // echo '<pre>';print_r($_POST);die;
        $form = $this->getAddForm();
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
         
        $isClassBooked = false;
         
        $groupClass = new QuestionClass($classId, $this->siteUserId, $this->siteUserType);
        $allClasses = $groupClass->getQuizData($this->siteUserId);
        if ($classId > 0) {
           
           
           // echo '<pre>';print_r($allClasses);die;
            if (!$classData = $groupClass->getClassToSave()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
             }
       
        $this->set('frm', $form);
        $this->set('classId', $classId);
        $this->set('formid', $_POST['formid']);
        $this->set('isClassBooked', $isClassBooked);
        $this->set('languages', Language::getAllNames(false));
        $this->set('allClasses', $allClasses);
        $this->_template->render(false, false);
    }

    /**
     * Setup Class
     */
    public function setupQuestions()
    {
 

       
        $post = FatApp::getPostedData();
         $db = FatApp::getDb();
         $db->startTransaction();
       
        if(isset($post['options'][0]) && !empty($post['options'][0]))
        {
        $question_option_1 =$post['options'][0];
        }
        if(isset($post['options'][1]) && !empty($post['options'][1]))
        {
        $question_option_2 =$post['options'][1];
        }
        if(isset($post['options'][2]) && !empty($post['options'][2]))
        {
        $question_option_3 =$post['options'][2];
        }
        if(isset($post['options'][3]) && !empty($post['options'][3]))
        {
        $question_option_4 =$post['options'][3];
        }
        if(isset($post['correct_answer']) && !empty($post['correct_answer']))
        {
             $correct_answer=implode(',',$post['correct_answer']);
        }
      
        $form = $this->getAddForm(true);
        if (!$post = $form->getFormDataFromArray($post)) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        
        $post['grpcls_teacher_id'] = $this->siteUserId;
        $post['image']='';
        if (!empty($_FILES['grpcls_banner']['tmp_name'])) {
            $post['image']=1;
        }
        else
        {
            $post['image']=0;
        }
      


        $post['question_option_1'] = (isset($question_option_1) && !empty($question_option_1)) ? $question_option_1 : null;
        $post['question_option_2']  =(isset($question_option_2) && !empty($question_option_2)) ?$question_option_2 : null;
        $post['question_option_3']  =(isset($question_option_3) && !empty($question_option_3)) ? $question_option_3 : null;     
        $post['question_option_4']  = (isset($question_option_4) && !empty($question_option_4)) ? $question_option_4 : null;
        $post['question_answers']  = (isset($correct_answer) && !empty($correct_answer)) ? $correct_answer : null;
       
      
        $class = new QuestionClass($post['question_id'], $this->siteUserId, $this->siteUserType);
         
        if (!$class->saveClass($post)) {
            FatUtility::dieJsonError($class->getError());
        }
        $questionaddedid = $class->getMainTableRecordId();



        if (!empty($_FILES['grpcls_banner']['tmp_name'])) {
            $fileData = [
                'name' => $_FILES['grpcls_banner']['name'],
                'type' => $_FILES['grpcls_banner']['type'],
                'tmp_name' => $_FILES['grpcls_banner']['tmp_name'],
                'error' => $_FILES['grpcls_banner']['error'],
                'size' => $_FILES['grpcls_banner']['size']
            ];

            $file = new Afile(Afile::TYPE_LESSON_QUESTIONS_FILE);
            if (!$file->saveFile($fileData, $questionaddedid)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($fileData['name'] . ' - ' . $file->getError());
            }
            


            //   $uploadPath = CONF_UPLOADS_PATH;
        
            // $filePath = date('Y') . '/' . date('m') . '/';
            // if (!file_exists($uploadPath . $filePath)) {
            //     mkdir($uploadPath . $filePath, 0777, true);
            // }
           
            // $fileName = preg_replace('/[^a-zA-Z0-9.]/', '', $_FILES['grpcls_banner']['name']);
            // while (file_exists($uploadPath . $filePath . $fileName)) {
            //     $fileName = time() . '-' . $fileName;
            // }
            // $filePath = $filePath . $fileName;
     
            // if (!move_uploaded_file($_FILES['grpcls_banner']['tmp_name'], $uploadPath . $filePath)) {
               
            //     $this->error = Label::getLabel('FILE_COULD_NOT_SAVE_FILE');
            //     return false;
            // }

            // $post['image']=$filePath;
        }
        // $db = FatApp::getDb();
        $db->startTransaction();
        
         

        FatUtility::dieJsonSuccess([
            'classId' => $class->getMainTableRecordId(),
            'msg' => Label::getLabel('LBL_QUESTION_ADDED_SUCCESSFULLY')
        ]);
        
    }

    /**
     * Render Class Language Form
     */
    public function langForm()
    {
        $classId = FatApp::getPostedData('classId', FatUtility::VAR_INT, 0);
        $langId = FatApp::getPostedData('langId', FatUtility::VAR_INT, 0);
        if (empty($classId) || empty($langId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $groupClass = new GroupClass($classId, $this->siteUserId, $this->siteUserType);
        $classData = $groupClass->getClassToSave($langId);
        if (empty($classData)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $classData['gclang_grpcls_id'] = $classId;
        $classData['gclang_lang_id'] = $langId;
        $form = $this->getLangForm($langId);
        $form->fill($classData);
        $this->set('frm', $form);
        $this->set('langId', $langId);
        $this->set('classId', $classId);
        $this->set('languages', Language::getAllNames(false));
        $this->_template->render(false, false);
    }

    /**
     * Setup Class Languages
     */
    public function setupLang()
    {
        $form = $this->getLangForm();
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $groupClass = new GroupClass($post['gclang_grpcls_id'], $this->siteUserId, $this->siteUserType);
        if (!$groupClass->saveLangData($post)) {
            FatUtility::dieJsonError($groupClass->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Add Class Form
     * 
     * @param bool $setUnique
     * @return Form
     */
    private function getAddForm(bool $setUnique = false): Form
    {
        $userTeachLangs = new UserTeachLanguage($this->siteUserId);
        $srch = $userTeachLangs->getSrchObject($this->siteLangId);
        $srch->doNotCalculateRecords();
        $srch->addMultiplefields(['tlang_id', 'IFNULL(tlang_name, tlang_identifier) as tlang_name']);
        $teachLangData = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        $form = new Form('classesForm');
        $fld = $form->addHiddenField('', 'question_id');
        $fld->requirements()->setIntPositive(true);
        $fld = $form->addRequiredField(Label::getLabel('LBL_Title'), 'grpcls_title');
        $fld1 = $form->addTextBox(Label::getLabel('LBL_HINT'), 'grpcls_hint');
       // $fld->requirements()->setLength(10, 100);
        
        $fld->requirements()->setRequired();
        $fld->requirements()->setRequired();
        //$fld->requirements()->setLength(10, 100);
       
        $form->addFileUpload(Label::getLabel('LBL_QUESTION_IMAGE'), 'grpcls_banner');
        $fld = $form->addTextArea(Label::getLabel('LBL_DESCRIPTION'), 'grpcls_description');
        
       // $fld->requirements()->setLength(10, 500);

        $fld = $form->addTextArea('Enter Math Equation (LaTeX format):', 'grpcls_description_math');
       
        $fld = $form->addIntegerField(Label::getLabel('LBL_MARKS'), 'grpcls_total_marks', '', ['id' => 'grpcls_total_marks']);
        $fld->requirements()->setRequired(true);
      
        $categories = Category::getCategoriesByParentId($this->siteLangId);
        $fld = $form->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'course_cate_id', $categories, '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
        $fld = $form->addSelectBox(Label::getLabel('LBL_SUBCATEGORY'), 'course_subcate_ida', [], '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setInt();
      
        $fld->requirements()->setRange(1, FatApp::getConfig('CONF_GROUP_CLASS_MAX_LEARNERS', FatUtility::VAR_INT, 9999));
        
        $manualOptions = [
       // Default empty option
            '1' => Label::getLabel('LBL_SINGLE_CHOICE'), // Option 1
            '2' => Label::getLabel('LBL_MULTIPLE_CHOICE'), // Option 2
            '3' => Label::getLabel('LBL_TEXT')  // Option 3
        ];
        $form->addSelectBox(Label::getLabel('LBL_TYPE'), 'grpcls_tlang_id', $manualOptions, '', ['id' => 'grpcls_tlang_id'], Label::getLabel('LBL_SELECT'))->requirements()->setRequired(true);
        $currencyCode = MyUtility::getSystemCurrency()['currency_code'];
  
        $fld->requirements()->setRange(1, 9999);
       $form->addSubmitButton('', 'btn_next', Label::getLabel('LBL_SAVE'));
        return $form;
    }

    /**
     * Get Language Form
     * 
     * @param int $langId
     * @return Form
     */
    private function getLangForm(int $langId = 0): Form
    {
        $frm = new Form('classLangForm');
        $fld = $frm->addHiddenField('', 'gclang_grpcls_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive(true);
        $fld = $frm->addHiddenField('', 'gclang_lang_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive(true);
        $fld = $frm->addRequiredField(Label::getLabel('LBL_TITLE', $langId), 'grpcls_title');
        $fld->requirements()->setLength(10, 100);
        $fld = $frm->addTextArea(Label::getLabel('LBL_DESCRIPTION', $langId), 'grpcls_description');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setLength(10, 1000);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT', $langId));
        return $frm;
    }

    /**
     * Check Class Status
     * 
     * @param int $classId
     */
    public function checkClassStatus($classId = 0)
    {
        $fields = ['ordcls_status', 'grpcls_status', 'grpcls_end_datetime', 'grpcls_teacher_starttime', 'ordcls_starttime'];
        $srch = new ClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->addCondition('ordcls_id', '=', $classId);
        $srch->applyPrimaryConditions();
        $srch->addMultipleFields($fields);
        $srch->setPageSize(1);
        $srch->doNotCalculateRecords();
        $class = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($class)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST_PLEASE_REFRESH_PAGE'));
        }
        $status = (User::TEACHER == $this->siteUserType) ? $class['grpcls_status'] : $class['ordcls_status'];
        if (User::TEACHER == $this->siteUserType && GroupClass::SCHEDULED == $class['grpcls_status']) {
            if (empty($class['grpcls_teacher_starttime']) && strtotime($class['grpcls_end_datetime']) > time()) {
                FatUtility::dieJsonSuccess(['classStatus' => $status, 'msg' => Label::getLabel('LBL_PLEASE_JOIN_CLASS_AND_START_CLASS')]);
            } elseif (!empty($class['grpcls_teacher_starttime']) && strtotime($class['grpcls_end_datetime']) < time()) {
                FatUtility::dieJsonError(['classStatus' => $status, 'msg' => Label::getLabel('LBL_TIME_IS_OVER_PLEASE_END_THE_CLASS')]);
            }
        }
        if (
                User::LEARNER == $this->siteUserType && !empty($class['grpcls_teacher_starttime']) &&
                GroupClass::SCHEDULED == $class['grpcls_status'] && OrderClass::SCHEDULED == $class['ordcls_status']
        ) {
            if (empty($class['ordcls_starttime']) && strtotime($class['grpcls_end_datetime']) > time()) {
                FatUtility::dieJsonSuccess(['classStatus' => $status, 'msg' => Label::getLabel('LBL_TEACHER_HAS_JOINED_PLEASE_JOIN_CLASS')]);
            } elseif (!empty($class['ordcls_starttime']) && strtotime($class['grpcls_end_datetime']) < time())
                FatUtility::dieJsonError(['classStatus' => $status, 'msg' => Label::getLabel('LBL_TIME_IS_OVER_CLASS_WILL_BE_ENDED_SOON')]);
        }
        FatUtility::dieJsonSuccess(['msg' => '', 'classStatus' => $status]);
    }

    /**
     * Get Upcoming Class
     * 
     * @return array
     */
    private function getUpcomingClass()
    {
        $srch = new ClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->addCondition('grpcls_start_datetime', '=', MyDate::formatDate(date('Y-m-d H:i:s')));
        $srch->addCondition('ordcls_status', '=', OrderClass::SCHEDULED);
        $srch->addCondition('grpcls_booked_seats', '>', 0);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('grpcls_start_datetime');
        $srch->setPageSize(1);
        $classe = $srch->fetchAndFormat(true);
        if (!empty($classe)) {
            $classe = current($classe);
        }
        return $classe;
    }

        /**
     * Fetch sub categories for selected category
     *
     * @param int $catgId
     * @param int $subCatgId
     * @return html
     */
    public function getSubcategories(int $catgId, int $subCatgId = 0)
    {
        $catgId = FatUtility::int($catgId);
        $subcategories = [];
        if ($catgId > 0) {
            $subcategories = Category::getCategoriesByParentId($this->siteLangId, $catgId);
        }
        $this->set('subCatgId', $subCatgId);
        $this->set('subcategories', $subcategories);
        $this->_template->render(false, false);
    }

}