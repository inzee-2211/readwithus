<?php

/**
 * This Controller is used to preview course learning page
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CoursePreviewController extends DashboardController
{

    /**
     * Initialize Tutorials
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        if ($this->siteUserType == User::LEARNER) {
            FatUtility::exitWithErrorCode(404);
        }
    }

    /**
     * Render Study Page
     *
     * @param int $courseId
     */
    public function index(int $courseId)
    {
        /* fetch course details */
        $courseObj = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course = $courseObj->get()) {
            FatUtility::exitWithErrorCode(404);
        }
        if ($this->siteUserId != $course['course_user_id']) {
            FatUtility::exitWithErrorCode(404);
        }
        /* fetch section and lectures list */
        $srch = new SectionSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->applyPrimaryConditions();
        $srch->addCondition('section.section_course_id', '=', $courseId);
        $srch->addOrder('section.section_order', 'ASC');
        $srch->addSearchListingFields();
        $srch->doNotCalculateRecords();
        if (!$sections = $srch->fetchAndFormat()) {
            FatUtility::exitWithErrorCode(404);
        }
        if ($course['course_lectures'] < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $this->sets([
            'course' => $course,
            'sections' => $sections,
        ]);
        $this->_template->addJs('js/common_ui_functions.js');
        $this->_template->render();
    }

    /**
     * Get lecture to be displayed
     *
     * @param int $next
     * @return json
     */
    public function getLecture(int $next)
    {
        $courseId = FatApp::getPostedData('course_id', FAtUtility::VAR_INT, 0);
        $lectureId = FatApp::getPostedData('lecture_id', FAtUtility::VAR_INT, 0);
        
        /* get next lecture */
        $lectureId = $this->getNextPrevLecture($courseId, $lectureId, $next);
        FatUtility::dieJsonSuccess(['lecture_id' => $lectureId]);
    }

    /**
     * Find next/previous lecture id
     *
     * @param int $courseId
     * @param int $lectureId
     * @param int $getNext
     * @return int
     */
    private function getNextPrevLecture(int $courseId, int $lectureId, int $getNext = AppConstant::YES)
    {
        /* get lecture order */
        $lectureOrder = Lecture::getAttributesById($lectureId, 'lecture_order');
        $lectureOrder = empty($lectureOrder) ? 0 : $lectureOrder;
        /* get lecture */
        $srch = new SearchBase(Lecture::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('lecture_id');
        $srch->addCondition('lecture_course_id', '=', $courseId);
        if ($getNext == AppConstant::YES) {
            $srch->addCondition('lecture_order', '>', $lectureOrder);
            $srch->addOrder('lecture_order', 'ASC');
        } else {
            $srch->addCondition('lecture_order', '<', $lectureOrder);
            $srch->addOrder('lecture_order', 'DESC');
        }
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $lecture = FatApp::getDb()->fetch($srch->getResultSet());
        $lectureId = 0;
        if (!empty($lecture)) {
            $lectureId = $lecture['lecture_id'];
        }
        return $lectureId;
    }

    /**
     * Get data for next, previous & current lecture
     *
     * @param int $courseId
     * @param int $lectureId
     */
    public function getLectureData(int $courseId, int $lectureId)
    {

       
        $lectureIds = [$lectureId];
        $lectureIds[] = $nextLecture = $this->getNextPrevLecture($courseId, $lectureId, AppConstant::YES);
        $lectureIds[] = $prevLecture = $this->getNextPrevLecture($courseId, $lectureId, AppConstant::NO);
        $srch = new LectureSearch($this->siteLangId);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addCondition('lecture.lecture_id', 'IN', $lectureIds);
        $lectures = $srch->fetchAndFormat();
        $resources = $lecture = [];
        if (isset($lectures[$lectureId])) {
            $lecture = isset($lectures[$lectureId]) ? $lectures[$lectureId] : [];
            $lectureObj = new Lecture($lecture['lecture_id']);
            $resources = $lectureObj->getResources();
        }
        /* get lecture video */
        $resource = new Lecture($lectureId);
        $video = $resource->getMedia(Lecture::TYPE_RESOURCE_EXTERNAL_URL);
        $this->sets([
            'lecture' => $lecture,
            'previousLecture' => isset($lectures[$prevLecture]) ? $lectures[$prevLecture] : [],
            'nextLecture' => isset($lectures[$nextLecture]) ? $lectures[$nextLecture] : [],
            'resources' => $resources,
            'video' => $video,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Get lecture data
     *
     * @param int $courseId
     * @param int $lectureId
     */
    public function getVideo(int $courseId, int $lectureId)
    {
        $lectureIds = [$lectureId];
        $lectureIds[] = $nextLecture = $this->getNextPrevLecture($courseId, $lectureId, AppConstant::YES);
        $lectureIds[] = $prevLecture = $this->getNextPrevLecture($courseId, $lectureId, AppConstant::NO);
        $srch = new LectureSearch($this->siteLangId);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addCondition('lecture.lecture_id', 'IN', $lectureIds);
        $lectures = $srch->fetchAndFormat();
        $this->sets([
            'lecture' => isset($lectures[$lectureId]) ? $lectures[$lectureId] : [],
            'previousLecture' => isset($lectures[$prevLecture]) ? $lectures[$prevLecture] : [],
            'nextLecture' => isset($lectures[$nextLecture]) ? $lectures[$nextLecture] : [],
        ]);
        /* get lecture video */
        $resource = new Lecture($lectureId);
        $this->set('video', $resource->getMedia(Lecture::TYPE_RESOURCE_EXTERNAL_URL));
        $this->_template->render(false, false);
    }

    /**
     * Download resources
     *
     * @param int $resourceId
     *
     */
    public function downloadResource(int $resourceId)
    {
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
        $resource = FatApp::getDb()->fetch($srch->getResultSet());
        // pr($resource);
        if (empty($resource)) {
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
        $srch->setPageSize(1);;
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
     * Get reviews form and overall stats
     */
    public function getReviews()
    {
        $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
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
            'course' => $course
        ]);
        $this->_template->render(false, false);
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
}
