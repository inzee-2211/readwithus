<?php

/**
 * This Controller is used for handling course learning process
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class TutorialsController extends DashboardController
{

    /**
     * Initialize Tutorials
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Start Course
     *
     * @param int $ordcrsId
     */
    public function start(int $ordcrsId)
    {
        $order = new OrderCourse($ordcrsId, $this->siteUserId);
        if (!$order->getOrderCourseById()) {
            FatUtility::exitWithErrorCode(404);
        }
        /* check if already started */
        $srch = new SearchBase(CourseProgress::DB_TBL);
        $srch->addCondition('crspro_ordcrs_id', '=', $ordcrsId);
        $srch->addMultipleFields(['crspro_id', 'crspro_started']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (!$data = FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::exitWithErrorCode(404);
        }
        if (empty($data['crspro_started'])) {
            $progress = new CourseProgress($data['crspro_id']);
            $progress->assignValues(['crspro_started' => date('Y-m-d'), 'crspro_status' => CourseProgress::IN_PROGRESS]);
            if (!$progress->save()) {
                FatUtility::exitWithErrorCode(404);
            }
        }
        FatApp::redirectUser(MyUtility::generateUrl('Tutorials', 'index', [$data['crspro_id']]));
    }

    /**
     * Render Study Page with course progress details
     *
     * @param int $progressId
     */
    public function index(int $progressId)
    {
        $progressData = CourseProgress::getAttributesById($progressId, [
            'crspro_ordcrs_id',
            'crspro_lecture_id',
            'crspro_progress',
            'crspro_completed',
        ]);
        $order = new OrderCourse($progressData['crspro_ordcrs_id'], $this->siteUserId);
        if (!$data = $order->getOrderCourseById()) {
            FatUtility::exitWithErrorCode(404);
        }
        $courseId = $data['ordcrs_course_id'];
         
        /* fetch course details */
        $courseObj = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course = $courseObj->get()) {
            FatUtility::exitWithErrorCode(404);
        }
        /* fetch section and lectures list */
        $srch = new SectionSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->applyPrimaryConditions();
        $srch->addCondition('section.section_course_id', '=', $courseId);
        $srch->addSearchListingFields();
        $srch->addOrder('section.section_order', 'ASC');
  
        if (!$sections = $srch->fetchAndFormat()) {
            FatUtility::exitWithErrorCode(404);
        }


      /*  $db = FatApp::getDb();
        foreach ($sections as $sectionKey => $section) {
            // Check if this section has a quiz
            if ($section['section_quiz_id'] != 0) {
                // Get the quiz attempt data for this section
                $srch = new SearchBase('tbl_quiz_attempt', 'qa');
                $srch->addCondition('qa.quiz_learner_id', '=', $this->siteUserId);
                $srch->addCondition('qa.quiz_lecture_id', '=', $section['section_quiz_id']);
                $srch->addCondition('qa.status', 'IN', [0, 1]);  // Status could be: 0 = submitted, 1 = failed, 2 = passed
                $srch->addFld('qa.status'); // Fetching only the status
                $rs = $srch->getResultSet();
        
                $quizData = $db->fetch($rs);
        
                // If quiz data exists, add the quiz attempt status under lectures
                if ($quizData) {
                    // Iterate over the lectures and add the quiz attempt status
                    foreach ($section['lectures'] as $lectureKey => $lecture) {
                        $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = $quizData['status'];
                    }
                } else {
                    // If no attempt made, set 'not attempted'
                    foreach ($section['lectures'] as $lectureKey => $lecture) {
                        $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = '0';
                    }
                }
            } else {
                
                // foreach ($section['lectures'] as $lectureKey => $lecture) {
                //     $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = '2';
                // }
            }
        }*/
        $db = FatApp::getDb();
        $lockLectures = false; // Flag to lock subsequent sections if a quiz is unattempted or failed
 
foreach ($sections as $sectionKey => $section) {
    if ($lockLectures) {
        // Lock all lectures in this section
        foreach ($section['lectures'] as $lectureKey => $lecture) {
            $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = 'locked';
        }
        continue; // Skip further processing for this section
    }

    // Check if this section has a quiz
    if ($section['section_quiz_id'] != 0) {
        // Get the quiz attempt data for this section
        $srch = new SearchBase('tbl_quiz_attempt', 'qa');
        $srch->addCondition('qa.quiz_learner_id', '=', $this->siteUserId);
        $srch->addCondition('qa.quiz_lecture_id', '=', $section['section_quiz_id']);
        $srch->addCondition('qa.status', 'IN', [0, 1]); // Status: 0 = submitted, 1 = failed
        $srch->addFld('qa.status');
        $rs = $srch->getResultSet();

        $quizData = $db->fetch($rs);

        // If no quiz attempt or failed, mark current lectures as accessible, lock subsequent sections
        if (!$quizData || $quizData['status'] == 1) {
            foreach ($section['lectures'] as $lectureKey => $lecture) {
                $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = 'accessible'; // Keep current section open
            }
            $lockLectures = true; // Lock subsequent sections
        } else {
            // Quiz passed or in evaluation, mark appropriately
            foreach ($section['lectures'] as $lectureKey => $lecture) {
                $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = $quizData['status'];
            }
        }
    } else {
        // No quiz in this section, mark lectures as accessible
        foreach ($section['lectures'] as $lectureKey => $lecture) {
            $sections[$sectionKey]['lectures'][$lectureKey]['quiz_attempt_status'] = 'accessible';
        }
    }
}

        







        /* format lectures stats */
        $progress = new CourseProgress($progressId);
        $lectureStats = $progress->getLectureStats($sections);
        
        //  echo '<pre>';print_R($sections);die;
        $this->sets([
            'course' => $course,
            'sections' => $sections,
            'progress' => $progressData,
            'progressId' => $progressId,
            'lectureStats' => $lectureStats,
        ]);
        $this->_template->addJs(['js/jquery.barrating.min.js', 'js/common_ui_functions.js']);
        $this->_template->render();
    }

    /**
     * Find next & previous lecture
     *
     * @param int $next
     * @return json
     */
    public function getLecture(int $next = AppConstant::YES)
    {
       
        $progressId = FatApp::getPostedData('progress_id', FAtUtility::VAR_INT, 0);
        $data = CourseProgress::getAttributesById($progressId, [
            'crspro_lecture_id',
            'crspro_ordcrs_id',
            'crspro_progress'
        ]);
        $ordcrs = new OrderCourse($data['crspro_ordcrs_id'], $this->siteUserId);
        if (!$order = $ordcrs->getOrderCourseById()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_LECTURE_NOT_FOUND'));
        }
        $data['crspro_course_id'] = $order['ordcrs_course_id'];
        $progress = new CourseProgress($progressId);
        $lectureId = $progress->getLecture($data, $next);
        FatUtility::dieJsonSuccess([
            'previous_lecture_id' => $data['crspro_lecture_id'],
            'next_lecture_id' => $lectureId,
        ]);
    }

    /**
     * Set current active lecture
     * Get lecture data
     *
     * @param int $lectureId
     * @param int $progressId
     */
    public function getLectureData(int $lectureId, int $progressId)
    {
    
        $progress = new CourseProgress($progressId);
        if (!$progress->isLectureValid($lectureId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!$progress->setCurrentLecture($lectureId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_UNABLE_TO_RENDER_NEXT_LECTURE._PLEASE_TRY_AGAIN'));
        }
         
        /* get previous and next lectures */
        $lectureIds = $progress->getNextPrevLectures();
        /* get lecture content */
        $srch = new LectureSearch($this->siteLangId);
        $srch->joinTable('tbl_sections', 'INNER JOIN', 'section.section_id = lecture.lecture_section_id', 'section');

        

        $srch->applyPrimaryConditions();
        $srch->addMultipleFields([
            'lecture.lecture_id AS lecture_id',
            'lecture.lecture_order AS lecture_order',
            'lecture.lecture_section_id AS lecture_section_id',
            'lecture.lecture_course_id AS lecture_course_id',
            'lecture.lecture_is_trial AS lecture_is_trial',
            'lecture.lecture_duration AS lecture_duration',
            'lecture.lecture_title AS lecture_title',
            'lecture.lecture_details AS lecture_details',
            'section.section_quiz_id AS section_quiz_id',
          
            
        ]);
        $srch->addSearchListingFields();
        $srch->addCondition('lecture.lecture_id', 'IN', [$lectureId, $lectureIds['next'], $lectureIds['previous']]);

       // $srch->addCondition('qa.quiz_learner_id', 'IN', [820]);
        //echo $srch->getQuery();die;
        $lectures = $srch->fetchAndFormat();
       // echo '<pre>';print_r($lectures);die;
        $lecture = isset($lectures[$lectureId]) ? $lectures[$lectureId] : [];
        /* get lecture resources */
        $resources = [];
        if (!empty($lecture)) {
            $lectureObj = new Lecture($lecture['lecture_id']);
            $resources = $lectureObj->getResources();
        }
        /* get lecture video */
        $resource = new Lecture($lectureId);
        $video = $resource->getMedia(Lecture::TYPE_RESOURCE_EXTERNAL_URL);



 
    $db = FatApp::getDb();
$srch = new SearchBase('tbl_quiz_attempt', 'qa');

// Add join with the quiz table
$srch->joinTable('tbl_quizzes', 'INNER JOIN', 'qa.quiz_id = q.quiz_id', 'q');

// Add conditions
$srch->addCondition('qa.quiz_learner_id', '=', $this->siteUserId);
$srch->addCondition('qa.quiz_lecture_id', '=', $lectureId);

// Fetch specific columns from both tables
$srch->addMultipleFields([
    'qa.attempt', 
    'qa.quiz_id', 
    'qa.id', 
    'qa.status',
    'q.quiz_title',  // Example column from tbl_quiz
    'q.quiz_description',  // Example column from tbl_quiz
    'q.quiz_pass_message',
    'q.quiz_fail_message',
]);

//lines added by rehan
// find quiz attached to this lecture (per-lecture attachment)
// $attachedQuizId = 0;
// foreach ($resources as $r) {
//     if ((int)$r['lecsrc_type'] === (int)Lecture::TYPE_RESOURCE_QUIZ) {
//         if (!empty($r['lecsrc_meta'])) {
//             $meta = @json_decode($r['lecsrc_meta'], true);
//             if (is_array($meta) && !empty($meta['quizId'])) {
//                 $attachedQuizId = (int)$meta['quizId'];
//                 break;
//             }
//         }
//     }
// }
// end lines added by rehan

$rs = $srch->getResultSet();
$QuizAttemptData = $db->fetchAll($rs);
 


        /* get progress data */
        $progData = CourseProgress::getAttributesById($progressId, ['crspro_covered', 'crspro_progress']);
        $this->sets([
            'lecture' => $lecture,
            'previousLecture' => isset($lectures[$lectureIds['previous']]) ? $lectures[$lectureIds['previous']] : [],
            'nextLecture' => isset($lectures[$lectureIds['next']]) ? $lectures[$lectureIds['next']] : [],
            'resources' => $resources,
            'progressId' => $progressId,
            'progData' => $progData,
            'video' => $video,
            'QuizAttemptData'=>$QuizAttemptData,
          //  'attachedQuizId' => $attachedQuizId // passing the attached quiz id to the template added by rehan for per-lecture quiz attachment
        ]);
        $this->_template->render(false, false, 'tutorials/get-lecture.php');
    }

    /**
     * Get lecture data
     *
     * @param int $lectureId
     * @param int $progressId
     */
    public function getVideo(int $lectureId, int $progressId)
    {
    
        $srch = new SearchBase(CourseProgress::DB_TBL);
        $srch->addCondition('crspro_id', '=', $progressId);
        $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'ordcrs_id = crspro_ordcrs_id');
        $srch->addMultipleFields([
            'crspro_lecture_id',
            'ordcrs_course_id AS crspro_course_id',
            'crspro_progress'
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
       
        if (empty(FatApp::getDb()->fetch($srch->getResultSet()))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* get previous and next lectures */
        $progress = new CourseProgress($progressId);
        $lectureIds = $progress->getNextPrevLectures();

        /* get lecture content */
        $srch = new LectureSearch($this->siteLangId);
        $srch->applyPrimaryConditions();
        $srch->joinTable('tbl_sections', 'INNER JOIN', 'section.section_id = lecture.lecture_section_id', 'section');
        $srch->addMultipleFields([
            'lecture_title',
            'lecture_id',
            'lecture_order',
            'lecture_section_id',
            'section.section_quiz_id'
        ]);
        $srch->addCondition('lecture.lecture_id', 'IN', [$lectureId, $lectureIds['next'], $lectureIds['previous']]);
       // echo $srch->getQuery();die;
        $lectures = $srch->fetchAndFormat();
        $this->sets([
            'lecture' => isset($lectures[$lectureId]) ? $lectures[$lectureId]: [],
            'previousLecture' => isset($lectures[$lectureIds['previous']]) ? $lectures[$lectureIds['previous']] : [],
            'nextLecture' => isset($lectures[$lectureIds['next']]) ? $lectures[$lectureIds['next']] : [],
        ]);
        /* get lecture video */
        $resource = new Lecture($lectureId);
        $this->set('video', $resource->getMedia(Lecture::TYPE_RESOURCE_EXTERNAL_URL));
        $this->_template->render(false, false, 'tutorials/get-video.php');
    }

    /**
     * Render teacher details
     */
    public function getTeacherDetail()
    {
        $courseId = FatApp::getPostedData('course_id');
        $teacherId = Course::getAttributesById($courseId, 'course_user_id');
        /* get teacher details */
        $srch = new TeacherSearch($this->siteLangId, 0, 0);
        $srch->addCondition('teacher.user_id', '=', $teacherId);
        $srch->applyPrimaryConditions();
        $srch->addMultipleFields([
            'user_username',
            'user_id',
            'user_last_name',
            'user_first_name',
            'testat_ratings',
            'testat_reviewes',
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (!$teacher = FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langData = TeacherSearch::getTeachersLangData($this->siteLangId, [$teacherId]);
        $teacher['user_biography'] = $langData[$teacherId] ?? '';
        $teachLangs = TeacherSearch::getTeachLangs($this->siteLangId, [$teacherId]);
        $teacher['teacherTeachLanguageName'] = $teachLangs[$teacherId] ?? '';
        $teacherCourses = TeacherSearch::getCourses([$teacherId]);
        $teacher['courses'] = $teacherCourses[$teacherId] ?? 0;
        $this->set('teacher', $teacher);
        $this->_template->render(false, false);
    }

    /**
     * Mark lecture as Convered/Uncovered
     *
     * @return json
     */
    public function markComplete()
    {
        $lectureId = FatApp::getPostedData('lecture_id', FAtUtility::VAR_INT, 0);
        $status = (int)FatApp::getPostedData('status');
        $progressId = FatApp::getPostedData('progress_id', FAtUtility::VAR_INT, 0);
        if ($lectureId < 1 || $progressId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $progress = new CourseProgress($progressId);
        if (!$progress->setCompletedLectures($lectureId, $status)) {
            FatUtility::dieJsonError($progress->getError());
        }
        if (!empty($status)) {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_LECTURE_MARKED_COVERED'));
        } else {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_LECTURE_MARKED_UNCOVERED'));
        }
    }

    /**
     * Update Course Progress & Completed Status
     *
     * @return json
     */
    public function setProgress()
    {
        $progressId = FatApp::getPostedData('progress_id', FAtUtility::VAR_INT, 0);
        if ($progressId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $progressData = CourseProgress::getAttributesById($progressId, ['crspro_progress', 'crspro_ordcrs_id']);
        $ordcrs = new OrderCourse($progressData['crspro_ordcrs_id'], $this->siteUserId);
        if (!$order = $ordcrs->getOrderCourseById()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* update course progress */
        $progress = new CourseProgress($progressId);
        if (!$progress->updateProgress($order['ordcrs_course_id'])) {
            FatUtility::dieJsonError($progress->getError());
        }
        $progress = CourseProgress::getAttributesById($progressId, ['crspro_progress', 'crspro_completed']);
        $response = ['progress' => $progress['crspro_progress']];
        if (
            $progressData['crspro_progress'] != $progress['crspro_progress'] && 
            (int)$progress['crspro_progress'] == 100
        ) {
            $response['is_completed'] = ($progress['crspro_completed']) ? true : false;
        }
        FatUtility::dieJsonSuccess($response);
    }

    /**
     * Render Course Completed Page With Certificate Download Link
     *
     * @param int $progressId
     */
    public function completed(int $progressId)
    {
        if ($progressId < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $data = CourseProgress::getAttributesById($progressId, [
            'crspro_completed',
            'crspro_ordcrs_id',
            'crspro_progress'
        ]);
        if (!$data['crspro_completed']) {
            FatUtility::exitWithErrorCode(404);
        }
        $ordcrs = new OrderCourse($data['crspro_ordcrs_id'], $this->siteUserId);
        if (!$order = $ordcrs->getOrderCourseById()) {
            FatUtility::exitWithErrorCode(404);
        }
        /* fetch course details */
        $courseObj = new Course($order['ordcrs_course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course = $courseObj->get()) {
            FatUtility::exitWithErrorCode(404);
        }
        $this->sets([
            'progressId' => $progressId,
            'progress' => $data,
            'course' => $course,
            'user' => User::getAttributesById($this->siteUserId, ['user_first_name', 'user_last_name'])
        ]);
        $this->_template->addJs('js/jquery.barrating.min.js');
        $this->_template->render();
    }

    /**
     * Download resources
     *
     * @param int $progressId
     * @param int $resourceId
     *
     */
    public function downloadResource(int $progressId, int $resourceId)
    {
        if ($progressId < 1 || $resourceId < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $ordcrsId = CourseProgress::getAttributesById($progressId, 'crspro_ordcrs_id');
        if ($ordcrsId < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $ordcrs = new OrderCourse($ordcrsId, $this->siteUserId);
        if (!$ordcrs->getOrderCourseById()) {
            FatUtility::exitWithErrorCode(404);
        }
        $srch = new SearchBase(Lecture::DB_TBL_LECTURE_RESOURCE, 'lecsrc');
        $srch->addCondition('lecsrc.lecsrc_id', '=', $resourceId);
        $srch->joinTable(Resource::DB_TBL, 'INNER JOIN', 'resrc.resrc_id = lecsrc.lecsrc_resrc_id', 'resrc');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields([
            'resrc_path',
            'resrc_name',
        ]);
        $srch->addCondition('resrc.resrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        if (!$resource = FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::exitWithErrorCode(404);
        }
        if (!file_exists(CONF_UPLOADS_PATH . $resource['resrc_path'])) {
            FatUtility::exitWithErrorCode(404);
        }
        $filePath = CONF_UPLOADS_PATH . $resource['resrc_path'];
        if (!$contentType = mime_content_type($filePath)) {
            FatUtility::exitWithErrorCode(500);
        }
        ob_end_clean();
        header('Expires: 0');
        header('Pragma: public');
        header("Content-Type: " . $contentType);
        header('Content-Description: File Transfer');
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: attachment; filename="' . $resource['resrc_name'] . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        echo file_get_contents($filePath);
    }

    /**
     * Reset progress for course retake
     *
     * @return json
     */
    public function retake()
    {
        $progressId = FatApp::getPostedData('progress_id', FatUtility::VAR_INT, 0);
        $ordcrsId = CourseProgress::getAttributesById($progressId, 'crspro_ordcrs_id');
        if ($ordcrsId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $ordcrs = new OrderCourse($ordcrsId, $this->siteUserId);
        if (!$ordcrs->getOrderCourseById()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_UNAUTHORIZED_ACCESS'));
        }
        $progress = new CourseProgress($progressId);
        if (!$progress->retake()) {
            FatUtility::dieJsonError($progress->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_COURSE_PROGRESS_RESET_SUCCESSFULLY'));
    }

    /**
     * Render Feedback Form
     *
     */
    public function feedbackForm()
    {
        $ordcrsId = FatApp::getPostedData('ordcrs_id', FatUtility::VAR_INT, 0);
        $ordcrs = new OrderCourse($ordcrsId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$order = $ordcrs->getCourseToFeedback()) {
            FatUtility::dieJsonError($ordcrs->getError());
        }
        $frm = CourseRatingReview::getFeedbackForm();
        $frm->fill(['ratrev_type_id' => $order['ordcrs_course_id'], 'ordcrs_id' => $ordcrsId]);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    /**
     * Feedback submission
     *
     * @return json
     */
    public function feedbackSetup()
    {
        $post = FatApp::getPostedData();
        $frm = CourseRatingReview::getFeedbackForm();
        if (!$post = $frm->getFormDataFromArray($post)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $course = new Course($post['ratrev_type_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->feedback($post)) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REVIEW_SUBMITTED_SUCCESSFULLY'));
    }

    /**
     * Get reviews form and overall stats
     */
    public function getReviews()
    {
        $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
        $progressId = FatApp::getPostedData('progress_id', FatUtility::VAR_INT, 0);
        /* check order course details */
        $srch = new SearchBase(CourseProgress::DB_TBL, 'ratrev');
        $srch->addCondition('crspro_id', '=', $progressId);
        $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'ordcrs_id = crspro_ordcrs_id', 'ordcrs');
        $srch->addMultipleFields([
            'crspro_ordcrs_id',
            'ordcrs_reviewed',
            'ordcrs_status'
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (!$orderCourse = FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        /* fetch course details */
        $courseObj = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course = $courseObj->get()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        /* fetch rating data */
        $revObj = new CourseRatingReview();
        $this->set('reviews', $revObj->getRatingStats($courseId));
        /* get sorting form */
        $frm = $this->getReviewForm();
        $frm->fill(['course_id' => $courseId]);
        $this->sets([
            'frm' => $frm,
            'courseId' => $courseId,
            'course' => $course,
            'ordcrsId' => $orderCourse['crspro_ordcrs_id'],
            'canRate' => OrderCourseSearch::canRate($orderCourse, $this->siteUserType),
        ]);
        $this->_template->render(false, false);
    }
//lines added by rehan for quiz-lecture starts here
/**
 * Get quiz with randomized questions
 */
/**
 * Get quiz with randomized questions - IMPROVED VERSION
 */
/**
 * Get quiz questions by subtopic ID
 */
private function getQuizQuestionsBySubtopic($subtopicId, $langId = 0)
{
    $db = FatApp::getDb();
    
    // First, get the subtopic name from course_topics
    $topicSrch = new SearchBase('course_topics', 'ct');
    $topicSrch->addCondition('ct.id', '=', $subtopicId);
    $topicSrch->addMultipleFields(['topic']);
    $topicSrch->doNotCalculateRecords();
    $subtopic = $db->fetch($topicSrch->getResultSet());
    
    if (!$subtopic) {
        return false;
    }
    
    $subtopicName = $subtopic['topic'];
    
    // Create quiz details structure
    $quizDetails = [
        'quiz_id' => $subtopicId,
        'quiz_title' => $subtopicName . ' Quiz',
        'quiz_description' => 'Quiz for: ' . $subtopicName,
        'quiz_pass_percentage' => 60, // Default pass percentage
        'quiz_duration' => 0, // No time limit by default
        'quiz_user_id' => 0
    ];
    
    // Fetch questions from tbl_quaestion_bank (note the table name spelling)
   // Fetch questions related to the quiz
        $questionSrch = new SearchBase('tbl_quiz_questions', 'qq');
        $questionSrch->joinTable('tbl_questions', 'INNER JOIN', 'qq.question_id = q.question_id', 'q');
        // $quizID->joinTable('tbl_lecture_resources', 'INNER JOIN', 'qq.quiz_id = quiz.quiz_id', 'quiz');
        // $questionSrch->addCondition('qq.quiz_id', '=', $quizId);
        $questionSrch->addMultipleFields([
            // 'qq.quiz_id',
            'qq.question_id',
            'q.question_title',
            'q.question_math_equation',
            'q.question_type',
            'q.question_desc',
            'q.question_cat',
            'q.question_subcat',
            'q.question_marks',
            'q.question_hint',
            'q.question_option_1',
            'q.question_option_2',
            'q.question_option_3',
            'q.question_option_4',
            'q.question_other',
            'q.question_answers',
            'q.question_image',
        ]);
        $questionSrch->doNotCalculateRecords();
        $questions = $db->fetchAll($questionSrch->getResultSet());
    
    // If no questions found in tbl_quaestion_bank, try alternative table names
    if (empty($questions)) {
        // Try tbl_question_bank (correct spelling)
        $altQuestionSrch = new SearchBase('tbl_question_bank', 'qb');
        $altQuestionSrch->addCondition('qb.subtopic', '=', $subtopicName);
        $altQuestionSrch->addCondition('qb.question_active', '=', 1);
        $altQuestionSrch->addOrder('RAND()');
        $altQuestionSrch->addMultipleFields([
            'qb.id as question_id',
            'qb.question_title',
            'qb.question_math_equation',
            'qb.question_type',
            'qb.question_desc',
            'qb.question_cat',
            'qb.question_subcat',
            'qb.question_marks',
            'qb.question_hint',
            'qb.question_option_1',
            'qb.question_option_2',
            'qb.question_option_3',
            'qb.question_option_4',
            'qb.question_other',
            'qb.question_answers',
            'qb.question_image',
        ]);
        $questions = $db->fetchAll($altQuestionSrch->getResultSet());
    }
    
    // Randomize options for each question
    foreach ($questions as &$question) {
        if (in_array($question['question_type'], ['1', '2'])) { // MCQ or Checkbox
            $options = [];
            for ($i = 1; $i <= 4; $i++) {
                $optionKey = "question_option_$i";
                if (!empty($question[$optionKey])) {
                    $options[] = [
                        'id' => $i,
                        'text' => $question[$optionKey]
                    ];
                }
            }
            shuffle($options); // Randomize options
            $question['randomized_options'] = $options;
        }
    }
    
    $quizDetails['questions'] = $questions;
    return $quizDetails;
}
/**
 * Get user quiz attempt - UPDATED VERSION
 */
private function getUserQuizAttemptLecture($userId, $subtopicId, $lectureId)
{
    $srch = new SearchBase('tbl_quiz_attempt', 'qa');
    $srch->addCondition('qa.quiz_learner_id', '=', $userId);
    $srch->addCondition('qa.quiz_lecture_id', '=', $lectureId);
    // We use subtopic ID as the identifier since we don't have quiz_id in tbl_quizzes
    $srch->addCondition('qa.quiz_id', '=', $subtopicId);
    $srch->addOrder('qa.attempt', 'DESC');
    $srch->setPageSize(1);
    $srch->doNotCalculateRecords();
    
    return FatApp::getDb()->fetch($srch->getResultSet());
}
//lines end here
/**
 * Get user quiz attempt
 */
private function getUserQuizAttempt($userId, $quizId, $lectureId)
{
    $srch = new SearchBase('tbl_quiz_attempt', 'qa');
    $srch->addCondition('qa.quiz_learner_id', '=', $userId);
    $srch->addCondition('qa.quiz_id', '=', $quizId);
    $srch->addCondition('qa.quiz_lecture_id', '=', $lectureId);
    $srch->addOrder('qa.attempt', 'DESC');
    $srch->setPageSize(1);
    $srch->doNotCalculateRecords();
    
    return FatApp::getDb()->fetch($srch->getResultSet());
}

    /**
 * Get quiz for lecture
 */
/**
 * Get quiz for lecture
 */
/**
 * Get quiz for lecture - UPDATED VERSION
 */
public function getQuiz()
{
    $lectureId = FatApp::getPostedData('lecture_id', FatUtility::VAR_INT, 0);
    $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
    $progressId = FatApp::getPostedData('progress_id', FatUtility::VAR_INT, 0);
    
    if ($lectureId < 1) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    // Get lecture details
    $lectureData = Lecture::getAttributesById($lectureId);
    
    if (!$lectureData) {
        FatUtility::dieJsonError(Label::getLabel('LBL_LECTURE_NOT_FOUND'));
    }


    // Get quiz resources for this lecture
    $lecture = new Lecture($lectureId);
    $resources = $lecture->getResources();
    // DEBUG: Let's see what's in the resources array
echo "<pre>Resources for lecture $lectureId: ";
print_r($resources);
echo "</pre>";
    $quizResource = null;
    
    foreach ($resources as $resource) {
        if ($resource['lecsrc_type'] == Lecture::TYPE_RESOURCE_QUIZ) {
            $quizResource = $resource;
            break;
        }
    }
    
    if (!$quizResource) {
        // No quiz found for this lecture
        $this->set('msg', Label::getLabel('LBL_NO_QUIZ_AVAILABLE_FOR_THIS_LECTURE'));
        $this->_template->render(false, false, 'tutorials/no-quiz.php');
        return;
    }
    
    // Parse quiz metadata
    $quizMeta = json_decode($quizResource['lecsrc_meta'], true);
    $subtopicId = $quizMeta['subtopic'] ?? 0;
    
    if ($subtopicId < 1) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_QUIZ_SUBTOPIC'));
    }
    
    // Get quiz details using subtopic ID
    $quizDetails = $this->getQuizQuestionsBySubtopic($subtopicId, $this->siteLangId);
    
    if (!$quizDetails || empty($quizDetails['questions'])) {
        FatUtility::dieJsonError(Label::getLabel('LBL_NO_QUESTIONS_FOUND_FOR_THIS_QUIZ'));
    }
    
    // Check if user has already attempted this quiz
    $previousAttempt = $this->getUserQuizAttemptLecture($this->siteUserId, $subtopicId, $lectureId);
    
    $this->sets([
        'quizDetails' => $quizDetails,
        'quizResource' => $quizResource,
        'previousAttempt' => $previousAttempt,
        'courseId' => $courseId,
        'lectureId' => $lectureId,
        'progressId' => $progressId,
        'siteLangId' => $this->siteLangId
    ]);
    
    $this->_template->render(false, false, 'tutorials/get-quiz-new.php');
}
//lines added by rehan for quiz-lecture ends here

    public function getQuizinfo()
    {
        
       // $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
        $quizId = FatApp::getPostedData('quiz_id', FatUtility::VAR_INT, 0);
        $courseId = FatApp::getPostedData('courseId', FatUtility::VAR_INT, 0);
        $lectureId = FatApp::getPostedData('lectureId', FatUtility::VAR_INT, 0);
        $db = FatApp::getDb();
    
        // Fetch quiz details
        $quizSrch = new SearchBase('tbl_quizzes', 'qz');
        $quizSrch->addCondition('qz.quiz_id', '=', $quizId);
        $quizSrch->doNotCalculateRecords();
        $quizDetails = $db->fetch($quizSrch->getResultSet());
        
        if (!$quizDetails) {
            FatUtility::dieJsonError(Label::getLabel('LBL_QUIZ_NOT_FOUND'));
        }
    
        // Fetch questions related to the quiz
        $questionSrch = new SearchBase('tbl_quiz_questions', 'qq');
        $questionSrch->joinTable('tbl_questions', 'INNER JOIN', 'qq.question_id = q.question_id', 'q');
        $questionSrch->addCondition('qq.quiz_id', '=', $quizId);
        $questionSrch->addMultipleFields([
            'qq.quiz_id',
            'qq.question_id',
            'q.question_title',
            'q.question_math_equation',
            'q.question_type',
            'q.question_desc',
            'q.question_cat',
            'q.question_subcat',
            'q.question_marks',
            'q.question_hint',
            'q.question_option_1',
            'q.question_option_2',
            'q.question_option_3',
            'q.question_option_4',
            'q.question_other',
            'q.question_answers',
            'q.question_image',
        ]);
        $questionSrch->doNotCalculateRecords();
        $questions = $db->fetchAll($questionSrch->getResultSet());
        
        // Combine quiz details with questions
        $quizDetails['questions'] = $questions;
     

  
        $frm = $this->getReviewForm();
        $frm->fill(['quizId' => $quizId]);
        $this->sets([
            'frm' => $frm,
            'quizId' => $quizId,
            'quizDetails' => $quizDetails,
             'lectureId' => $lectureId,
             'courseId' => $courseId,
            // 'canRate' => OrderCourseSearch::canRate($orderCourse, $this->siteUserType),
        ]);
        //$this->_template->render(false, false);
        $this->_template->render(false, false, 'tutorials/get-quiz.php');
    }

    /**
     * Get reviews list
     */
    public function searchReviews()
    {
        $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
        $post = FatApp::getPostedData();
        /* get reviews list */
        $srch = new SearchBase(RatingReview::DB_TBL, 'ratrev');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'course.course_id = ratrev.ratrev_type_id', 'course');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = ratrev.ratrev_user_id', 'learner');
        $srch->addCondition('ratrev.ratrev_status', '=', RatingReview::STATUS_APPROVED);
        $srch->addCondition('ratrev.ratrev_type', '=', AppConstant::COURSE);
        $srch->addCondition('ratrev.ratrev_type_id', '=', $courseId);
        $srch->addMultipleFields([
            'user_first_name', 'user_last_name', 'ratrev_id', 'ratrev_user_id',
            'ratrev_title', 'ratrev_detail', 'ratrev_overall', 'ratrev_created'
        ]);
        $sorting = FatApp::getPostedData('sorting', FatUtility::VAR_STRING, RatingReview::SORTBY_NEWEST);
        $srch->addOrder('ratrev.ratrev_id', $sorting);
        $pagesize = AppConstant::PAGESIZE;
        $srch->setPageSize($pagesize);
        $post['pageno'] = FatApp::getPostedData('pageno', FatUtility::VAR_INT, 1);
        $srch->setPageNumber($post['pageno']);
        $reviews = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->sets([
            'reviews' => $reviews,
            'pageCount' => $srch->pages(),
            'pagesize' => $pagesize,
            'recordCount' => $srch->recordCount(),
            'post' => $post,
            'courseId' => $courseId,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Get Review Form
     * 
     * @return Form
     */
    private function getReviewForm(): Form
    {
        $frm = new Form('reviewFrm');
        $fld = $frm->addHiddenField('', 'course_id');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setIntPositive();
        $frm->addSelectBox('', 'sorting', RatingReview::getSortTypes(), '', [], '');
        $frm->addHiddenField('', 'pageno', 1);
        return $frm;
    }
     //lines added by rehan for quiz-lecture starts here
/**
 * Submit lecture quiz
 */
/**
 * Submit lecture quiz - UPDATED VERSION
 */
public function submitLectureQuiz()
{
    $post = FatApp::getPostedData();
    $subtopicId = $post['quiz_id'] ?? 0; // This is actually the subtopic ID
    $lectureId = $post['lecture_id'] ?? 0;
    $answers = $post['answers'] ?? [];
    $progressId = $post['progress_id'] ?? 0;

    if ($subtopicId < 1 || $lectureId < 1) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    // Get quiz questions to evaluate
    $quizDetails = $this->getQuizQuestionsBySubtopic($subtopicId, $this->siteLangId);
    
    if (!$quizDetails || empty($quizDetails['questions'])) {
        FatUtility::dieJsonError(Label::getLabel('LBL_QUIZ_NOT_FOUND'));
    }

    // Evaluate the quiz (you can reuse your existing evaluation logic)
    $result = $this->evaluateQuizAnswers($answers, $quizDetails['questions']);
    
    if ($result) {
        // Mark lecture as completed if quiz passed
        if ($result['passed']) {
            $progress = new CourseProgress($progressId);
            $progress->setCompletedLectures($lectureId, 1);
        }

        // Save attempt record
        $this->saveQuizAttempt($subtopicId, $lectureId, $progressId, $result);

        FatUtility::dieJsonSuccess([
            'status' => 'success',
            'message' => Label::getLabel('LBL_QUIZ_SUBMITTED_SUCCESSFULLY'),
            'data' => $result
        ]);
    }

    FatUtility::dieJsonError(Label::getLabel('LBL_ERROR_SUBMITTING_QUIZ'));
}

/**
 * Evaluate quiz answers
 */
private function evaluateQuizAnswers($answers, $questions)
{
    $score = 0;
    $totalMarks = 0;
    $autoCheckedQuestions = [];

    foreach ($questions as $question) {
        $questionId = $question['question_id'];
        $marks = $question['question_marks'];
        $totalMarks += $marks;

        $submittedAnswer = $answers[$questionId] ?? '';
        $correctAnswer = $question['question_answers'];
        
        // Simple evaluation logic - you can enhance this
        if ($question['question_type'] == '1') { // Single choice
            if ($submittedAnswer == $correctAnswer) {
                $score += $marks;
                $status = 'correct';
            } else {
                $status = 'incorrect';
            }
        } elseif ($question['question_type'] == '2') { // Multiple choice
            // You'll need more complex logic for multiple choice
            $submittedArray = is_array($submittedAnswer) ? $submittedAnswer : [$submittedAnswer];
            $correctArray = explode(',', $correctAnswer);
            sort($submittedArray);
            sort($correctArray);
            
            if ($submittedArray == $correctArray) {
                $score += $marks;
                $status = 'correct';
            } else {
                $status = 'incorrect';
            }
        } else { // Text answer
            // For text answers, you might want to always mark as correct or implement AI grading
            $score += $marks;
            $status = 'correct';
        }

        $autoCheckedQuestions[$questionId] = [
            'status' => $status,
            'marks' => $status == 'correct' ? $marks : 0,
            'question_title' => $question['question_title'],
            'submitted_answer' => $submittedAnswer,
            'correctanswer' => $correctAnswer,
        ];
    }

    $percentage = ($score / $totalMarks) * 100;
    $passed = $percentage >= 60; // Using default 60% pass rate

    return [
        'score' => $score,
        'totalMarks' => $totalMarks,
        'percentage' => $percentage,
        'passed' => $passed,
        'autoCheckedQuestions' => $autoCheckedQuestions
    ];
}

/**
 * Save quiz attempt
 */
private function saveQuizAttempt($subtopicId, $lectureId, $progressId, $result)
{
    $db = FatApp::getDb();
    
    // Check for existing attempt
    $srch = new SearchBase('tbl_quiz_attempt', 'qa');
    $srch->addCondition('qa.quiz_id', '=', $subtopicId);
    $srch->addCondition('qa.quiz_learner_id', '=', $this->siteUserId);
    $srch->addCondition('qa.quiz_lecture_id', '=', $lectureId);
    $srch->addMultipleFields(['qa.attempt', 'qa.id']);
    $existingData = $db->fetchAll($srch->getResultSet());

    $attempt = empty($existingData) ? 1 : ($existingData[0]['attempt'] + 1);
    $status = $result['passed'] ? 2 : 1; // 2 = passed, 1 = failed

    $data = [
        'quiz_id' => $subtopicId,
        'quiz_learner_id' => $this->siteUserId,
        'quiz_lecture_id' => $lectureId,
        'attempt' => $attempt,
        'status' => $status,
        'score' => $result['score'],
        'total_marks' => $result['totalMarks'],
        'percentage' => $result['percentage'],
        'created_on' => date('Y-m-d H:i:s'),
    ];

    if (empty($existingData)) {
        $db->insertFromArray('tbl_quiz_attempt', $data);
    } else {
        $db->updateFromArray('tbl_quiz_attempt', $data, [
            'smt' => 'id = ?', 
            'vals' => [$existingData[0]['id']]
        ]);
    }
}
//lines added by rehan end here

public function submitQuiz()
{
   
    $db = FatApp::getDb();
    $quizId = $_POST['quiz_id'];
    $questions = $this->getCorrectAnswersForQuiz($_POST['quiz_id']);

    $result = [
        'autoCheckedQuestions' => [],
        'manualCheckQuestions' => [],
        'score' => 0,
        'totalMarks' => 0,
    ];

     $submittedAnswers = array_map(function ($answer) {
        return is_array($answer) ? implode(',', $answer) : $answer;
    }, $_POST['answers']);
 
    foreach ($questions as $questionDetails) {

    
        $correctAnswers = explode(',', trim($questionDetails['question_answers'])); // Split numeric answers
        $questionType = $questionDetails['question_type'];
        $marks = $questionDetails['question_marks'];
        $questionId = $questionDetails['question_id'];
        $question_title = html_entity_decode($questionDetails['question_title']);
        $result['totalMarks'] += $marks;

        // Map correct numeric answers to their corresponding options
        $mappedCorrectAnswers = array_map(function ($answer) use ($questionDetails) {
            return $questionDetails["question_answers"] ?? null;
        }, $correctAnswers);

        // Process single-choice and multiple-choice questions
        if (in_array($questionType, ['1', '2'])) {
            $submittedAnswer = $submittedAnswers[$questionId] ?? '';
         $submittedAnswerArray = explode(',', $submittedAnswer);

            // Map submitted answers to their options
            $mappedSubmittedAnswers = array_map(function ($answer) {
                return $answer ?? null;
            }, $submittedAnswerArray);

            // Sort for accurate comparison
            sort($mappedSubmittedAnswers);
            sort($mappedCorrectAnswers);

            if ($submittedAnswerArray == $correctAnswers) {
                $result['autoCheckedQuestions'][$questionId] = [
                    'status' => 'correct',
                    'marks' => $marks,
                    'question_title' => $question_title,
                    'submitted_answer' => $submittedAnswerArray,
                    'correctanswer' => $mappedCorrectAnswers,
                ];
                $result['score'] += $marks;
            } else {
                $result['autoCheckedQuestions'][$questionId] = [
                    'status' => 'incorrect',
                    'marks' => 0,
                    'question_title' => $question_title,
                    'submitted_answer' => $submittedAnswerArray,
                    'correctanswer' => $mappedCorrectAnswers,
                ];
            }
        } elseif ($questionType == '3') { // Text-based questions

            $api_key = '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';  
           
            $submitans=$_POST['answers'][$questionId] ?? ''; 
            $question =$questionDetails['question_title']; // Question asked
            $student_answer = $submitans; // Student's answer

            $question_type = 'Physics';   
            $question_marks = $marks; 

            $prompt = "You are a teacher grading a student's answer. Grade the answer based on the following criteria:

            1. **Evaluation Based on Marks**:
            - If the question is worth **1-2 marks**, evaluate the answer as simply **right or wrong**. Award full marks for a correct answer and zero marks for an incorrect answer.
            - If the question is worth **3 marks or more**, evaluate the answer based on the following detailed criteria:

            a. **Correctness** (" . (0.5 * $question_marks) . " marks): Does the answer correctly address the question?
            - For **factual/ numerical questions**, award full marks if the answer:
            1. Matches the correct value exactly for integer or fractional results.
            2. **Is accurate within reasonable precision** (e.g., rounded to 2-4 decimal places) for decimal answers.
            3. Use a tolerance of **±0.0001** for comparing numerical values. For example, if the correct answer is 1.31429, accept any value within the range 1.31419 to 1.31439.
            - For **theoretical questions**, assess whether the student has provided the correct concept, reasoning, or explanation, ensuring it aligns with logical, scientific, and proven principles.
            - **Exact matches within tolerance should be treated as correct**. If the student's answer falls within the acceptable tolerance range, award full marks. **Partial marks should not be given if the value is correct within the tolerance**.

            b. **Clarity** (" . (0.3 * $question_marks) . " marks): Is the answer clear, concise, and well-written? 
            - Award full marks if the answer is easy to understand, concise, and free from grammatical or spelling errors.
            - Deduct marks if the answer has unclear language, confusing structure, or frequent grammar/spelling errors. 
            - Consider if the student has made their reasoning process clear for complex or theoretical answers.

            c. **Relevance** (" . (0.2 * $question_marks) . " marks): Does the answer stay focused on the question and avoid unnecessary details?
            - Award full marks if the answer directly addresses the question, without veering off-topic.
            - Deduct marks for unnecessary tangents or unrelated information. 
            - For **theoretical questions**, ensure the answer stays on-topic with the key points that were asked.

            2. **Special Considerations for Subjects**:
            - For questions in **English**, place special emphasis on:
            - **Grammar and Vocabulary**: Evaluate if the student uses appropriate grammar, correct spelling, and a suitable vocabulary level.
            - **Relevance and Clarity**: Ensure the answer is directly addressing the question, well-structured, and free from unnecessary or irrelevant content.

            - For questions in **geography, biology, EVS, science, civics, or economics**, ensure the response is based on **logical, scientific, and proven information**.

            **Special Instructions**:
            - This question is worth " . $question_marks . " marks.
            - For **numerical/ factual answers**, check if the calculations and results are accurate:
            - **Exact matches** within the tolerance (e.g., if the correct answer is 1.31429, check that the student's answer is between 1.31419 and 1.31439) should be treated as a **correct answer** and awarded full marks.
            - If the student's answer is **exact** or within the acceptable range, award **full marks**. 
            - Award **zero marks** for answers that are **incorrect** and do not match the correct value within the acceptable tolerance.
            - For **theoretical or conceptual questions**, evaluate how well the student explains the concept, ensuring the response is logical, scientifically accurate, and aligned with established facts.
            - For **longer responses**, assess if the student is succinct without losing essential details, while evaluating correctness, clarity, relevance, and overall quality.

            **Input**:
            Question: \"$question\"  
            Student's Answer: \"$student_answer\"  

            **Output**:
            Marks: [X/" . $question_marks . "]  
            Explanation: 
            - **Correctness**: [Explain the reasoning for awarding the correctness score. Indicate whether the answer was completely correct, partially correct, or incorrect.]
            - **Clarity**: [Explain the reasoning for the clarity score. Was the answer clear, concise, and well-written? Did the student use appropriate terminology and sentence structure?]
            - **Relevance**: [Explain the reasoning for the relevance score. Did the answer stay focused on the question or contain irrelevant details?]
            - **Grammar and Vocabulary (for English)**: [For English questions, provide additional feedback on grammar, vocabulary, and sentence structure.]
            ";
            
       $data = [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    ["role" => "system", "content" => "You are a helpful assistant and an expert teacher."],
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.0,  
                "max_tokens" => 100
            ];
            
            // cURL setup to call OpenAI API
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            $response_data = json_decode($response, true);
           
             
            if (isset($response_data['choices'][0]['message']['content'])) {
            $gpt_response = $response_data['choices'][0]['message']['content'];
            
            preg_match('/\b([\d\.]+)\s*\/\s*' . preg_quote($question_marks, '/') . '\b/', $gpt_response, $matches);
            $marks = isset($matches[1]) ? floatval($matches[1]) : 0;  
            $question_marks_obtained = $marks;

            preg_match('/Explanation:\s*(.*)/s', $gpt_response, $explanation_matches);
            $explanation = isset($explanation_matches[1]) ? trim($explanation_matches[1]) : 'No explanation provided';

 
            } else {
                echo "Error: Unable to get a response. Check API key or request formatting.";
            }
            
          if($question_marks_obtained > 0)
          {
            $status='correct';
          }
          else
          {
            $status='incorrect';
          }
     
            $result['autoCheckedQuestions'][$questionId] = [
               
                'status' => $status,
                'question_title' => $question_title,
                'marks' => $question_marks_obtained,
                'submitted_answer' => $submitans,
                'correctanswer' => $explanation,
            ];

            $result['score'] += $question_marks_obtained;
        }
    }

     $percentage = ($result['score'] / $result['totalMarks']) * 100;
      
    if ($percentage >= $_POST['quiz_pass_percentage']) {
        $status = 2;
    } else {
        $status = 1;
    } 

    $db = FatApp::getDb();
    $srch = new SearchBase('tbl_quiz_attempt', 'qa');
    $srch->addCondition('qa.quiz_id', '=', $quizId);
    $srch->addCondition('qa.quiz_learner_id', '=', $this->siteUserId);
    $srch->addCondition('qa.quiz_lecture_id', '=', $_POST['lectureId']);
    // Fetch specific columns (optional)
    $srch->addMultipleFields(['qa.attempt', 'qa.quiz_id', 'qa.id']);
    $rs = $srch->getResultSet();
    $existingData = $db->fetchAll($rs);

    if (empty($existingData)) {
        $attempt=1;
        $data = [
          
            'quiz_id' => $quizId,
            'quiz_learner_id' =>  $this->siteUserId,
            'quiz_lecture_id' => $_POST['lectureId'],
            'attempt' => $attempt,
            'status' => $status,
            'created_on' => date('Y-m-d H:i:s'), // Use current timestamp
        ];
        if (!$db->insertFromArray('tbl_quiz_attempt', $data)) {
            return $db->getError(); // Return error if insert fails
        }
        
    } else {
        $attempt=$existingData[0]['attempt']+1;
        $data = [
            'quiz_id' => $quizId,
            'quiz_learner_id' =>  $this->siteUserId,
            'quiz_lecture_id' => $_POST['lectureId'],
            'attempt' => $attempt,
            'status' => $status,
            'created_on' => date('Y-m-d H:i:s'), // Use current timestamp
        ];
       
    
        if (!$db->updateFromArray('tbl_quiz_attempt', $data, ['smt' => 'id = ?', 'vals' => [$existingData[0]['id']]])) {
            return $db->getError(); // Return error if update fails
        }
        
    }

    $dataToInsert = [
        'quiz_id' => $quizId,
        'quiz_learner_id' => $this->siteUserId,
        'course_id' => $_POST['courseId'],
        'quiz_tutor_id' => $_POST['quiz_teacher_id'],
        'quiz_lecture_id' => $_POST['lectureId'],
        'quiz_submit_data' => json_encode($_POST),
        'quiz_autoresult_data' => json_encode($result),
        'score' => $result['score'],
        'attempt' => $attempt,
        'status' => 1,
        'percentage' => $percentage,
        'total_marks' => $result['totalMarks'],
        'quiz_added_on' => date('Y-m-d H:i:s'),
    ];
    if (!$db->insertFromArray('tbl_quiz_grading', $dataToInsert)) {
        FatUtility::dieJsonError($db->getError()); // Handle any insertion errors
    }
   
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you for your responses, Your test is being evaluated',
        'Quizdata' => $result
    ]);
    //  FatUtility::dieJsonSuccess(json_encode($result));
  die;
}


    function getCorrectAnswersForQuiz($quizId) {
        $db = FatApp::getDb();
        $quizSrch = new SearchBase('tbl_quizzes', 'qz');
        $quizSrch->addCondition('qz.quiz_id', '=', $quizId);
        $quizSrch->doNotCalculateRecords();
        $quizDetails = $db->fetch($quizSrch->getResultSet());
        
        if (!$quizDetails) {
            FatUtility::dieJsonError(Label::getLabel('LBL_QUIZ_NOT_FOUND'));
        }
    
        // Fetch questions related to the quiz
        $questionSrch = new SearchBase('tbl_quiz_questions', 'qq');
        $questionSrch->joinTable('tbl_questions', 'INNER JOIN', 'qq.question_id = q.question_id', 'q');
        $questionSrch->addCondition('qq.quiz_id', '=', $quizId);
        $questionSrch->addMultipleFields([
            'qq.quiz_id',
            'qq.question_id',
            'q.question_title',
            'q.question_type',
            'q.question_desc',
            'q.question_cat',
            'q.question_subcat',
            'q.question_marks',
            'q.question_hint',
            'q.question_option_1',
            'q.question_option_2',
            'q.question_option_3',
            'q.question_option_4',
            'q.question_other',
            'q.question_answers',
        ]);
        $questionSrch->doNotCalculateRecords();
        $questions = $db->fetchAll($questionSrch->getResultSet());
        return $questions;

    }
}
