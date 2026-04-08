<?php

/**
 * This class is used to handle lectures
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class Lecture extends MyAppModel
{
    const DB_TBL = 'tbl_lectures';
    const DB_TBL_PREFIX = 'lecture_';
    const DB_TBL_LECTURE_RESOURCE = 'tbl_lectures_resources';
    const DB_TBL_LECTURE_RESOURCE_PREFIX = 'lecsrc_';

    const TYPE_RESOURCE_EXTERNAL_URL = 1;
    const TYPE_RESOURCE_UPLOAD_FILE = 2;
    const TYPE_RESOURCE_LIBRARY = 3;
    const TYPE_RESOURCE_QUIZ = 4;
    
    private $userId;

    /**
     * Initialize Lecture
     *
     * @param int $id
     */
    public function __construct(int $id = 0, $userId = 0)
    {
        parent::__construct(static::DB_TBL, 'lecture_id', $id);
        $this->userId = $userId;
    }

    /**
     * Get Resource Types List
     *
     * @param int $key
     * @return string|array
     */
    public static function getTypes(int $key = null)
    {
        $arr = [
            static::TYPE_RESOURCE_EXTERNAL_URL => Label::getLabel('LBL_EXTERNAL_URL'),
            static::TYPE_RESOURCE_UPLOAD_FILE => Label::getLabel('LBL_UPLOAD_FILE'),
            static::TYPE_RESOURCE_LIBRARY => Label::getLabel('LBL_RESOURCE_LIBRARY')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Setup data
     *
     * @param array $data
     * @return bool
     */
    public function setup(array $data)
    {
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        $this->assignValues([
            'lecture_section_id' => $data['lecture_section_id'],
            'lecture_course_id' => $data['lecture_course_id'],
            'lecture_is_trial' => $data['lecture_is_trial'],
            'lecture_title' => $data['lecture_title'],
            'lecture_details' => $data['lecture_details'],
            'lecture_updated' => date('Y-m-d H:i:s'),
        ]);
        if ($data['lecture_id'] < 1) {
            $this->setFldValue('lecture_created', date('Y-m-d H:i:s'));
        }
        if (!$this->save($data)) {
            $db->rollbackTransaction();
            $this->error = $this->getError();
            return false;
        }
        /* update lecture duration */
        if (!$this->setDuration()) {
            return false;
        }
        /* update section duration */
        $section = new Section($data['lecture_section_id']);
        if (!$section->setDuration()) {
            $db->rollbackTransaction();
            $this->error = $section->getError();
            return false;
        }
        /* update course duration */
        $course = new Course($data['lecture_course_id']);
        if (!$course->setDuration()) {
            $this->error = $course->getError();
            return false;
        }
        /* reset section order */
        if (!$this->resetOrder($data['lecture_course_id'])) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->setLectureCount($data['lecture_section_id'])) {
            $db->rollbackTransaction();
            return false;
        }
        $db->commitTransaction();
        return true;
    }

    /**
     * Function to setup resources
     *
     * @param int $lecSrcId
     * @param int $type
     * @param int $srcId
     * @param int $courseId
     * @return bool
     */
    public function setupResources(int $lecSrcId, int $type, int $srcId, int $courseId)
    {
        /* bind added resource with lecture */
        $obj = new TableRecord(static::DB_TBL_LECTURE_RESOURCE);
        $obj->assignValues([
            'lecsrc_id' => $lecSrcId,
            'lecsrc_type' => $type,
            'lecsrc_resrc_id' => $srcId,
            'lecsrc_lecture_id' => $this->getMainTableRecordId(),
            'lecsrc_course_id' => $courseId,
            'lecsrc_link'       => '',
               'lecsrc_duration'   => 0,       
            'lecsrc_created' => date('Y-m-d H:i:s')
        ]);
        if (!$obj->addNew()) {
            $this->error = $obj->getError();
            return false;
        }
        return true;
    }

    /**
     * Function to remove course
     *
     * @return bool
     */
    public function delete()
    {
        $lectureId = $this->getMainTableRecordId();
        if (!$data = static::getAttributesById($lectureId, ['lecture_section_id', 'lecture_course_id'])) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        if (Course::getAttributesById($data['lecture_course_id'], 'course_user_id') != $this->userId) {
            $this->error = Label::getLabel('LBL_UNAUTHORIZED_ACCESS');
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        $this->setFldValue('lecture_deleted', date('Y-m-d H:i:s'));
        if (!$this->save()) {
            $db->rollbackTransaction();
            $this->error = $this->getError();
            return false;
        }
        /* update lecture duration */
        if (!$this->setDuration()) {
            $db->rollbackTransaction();
            return false;
        }
        /* update section duration */
        $section = new Section($data['lecture_section_id']);
        if (!$section->setDuration()) {
            $db->rollbackTransaction();
            $this->error = $section->getError();
            return false;
        }
        /* update course duration */
        $course = new Course($data['lecture_course_id']);
        if (!$course->setDuration()) {
            $db->rollbackTransaction();
            $this->error = $course->getError();
            return false;
        }
        /* reset lectures order */
        if (!$this->resetOrder($data['lecture_course_id'])) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->setLectureCount($data['lecture_section_id'])) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    //lines added by rehan start here
/**
 * Get quiz with randomized questions
 */
/**
 * Get quiz with randomized questions - IMPROVED VERSION
 */
private function getQuizWithRandomizedQuestions($quizId, $langId = 0)
{
    $db = FatApp::getDb();
    
    // Fetch quiz details - more comprehensive query
    $quizSrch = new SearchBase('tbl_quizzes', 'qz');
    $quizSrch->addCondition('qz.quiz_id', '=', $quizId);
    
    // Add language join if needed
    if ($langId > 0) {
        $quizSrch->joinTable('tbl_quiz_langs', 'LEFT JOIN', 
            'quelang.quelang_quiz_id = qz.quiz_id AND quelang.quelang_lang_id = ' . $langId, 
            'quelang');
        $quizSrch->addMultipleFields([
            'qz.*',
            'quelang.quiz_title',
            'quelang.quiz_description'
        ]);
    } else {
        $quizSrch->addMultipleFields(['qz.*']);
    }
    
    $quizSrch->doNotCalculateRecords();
    $quizDetails = $db->fetch($quizSrch->getResultSet());
    
    if (!$quizDetails) {
        return false;
    }

    // Fetch questions with randomization
    $questionSrch = new SearchBase('tbl_quiz_questions', 'qq');
    $questionSrch->joinTable('tbl_questions', 'INNER JOIN', 'qq.question_id = q.question_id', 'q');
    $questionSrch->addCondition('qq.quiz_id', '=', $quizId);
    $questionSrch->addCondition('q.question_active', '=', 1);
    $questionSrch->addOrder('RAND()'); // Randomize questions
    
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
    
    // Debug: Check if questions were found
    if (empty($questions)) {
        // Try alternative table structure
        $altQuestionSrch = new SearchBase('tbl_questions', 'q');
        $altQuestionSrch->addCondition('q.quiz_id', '=', $quizId);
        $altQuestionSrch->addCondition('q.question_active', '=', 1);
        $altQuestionSrch->addOrder('RAND()');
        
        $altQuestionSrch->addMultipleFields([
            'q.question_id',
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
//lines added by rehan end here
    /**
     * Reset Order
     *
     * @param int $courseId
     * @return bool
     */
    public function resetOrder(int $courseId)
    {
        /* reset section order */
        $srch = new SearchBase(static::DB_TBL);
        $srch->joinTable(
            Section::DB_TBL,
            'INNER JOIN',
            'section_id = lecture_section_id'
        );
        $srch->addFld('lecture_id');
        $srch->addCondition('lecture_course_id', '=', $courseId);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('section_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addOrder('section_order', 'ASC');
        $srch->addOrder('lecture_order = 0', 'ASC');
        $srch->addOrder('lecture_order', 'ASC');
        $lectureIds = FatApp::getDb()->fetchAll($srch->getResultSet(), 'lecture_id');
        $lectureIds = array_keys($lectureIds);
        array_unshift($lectureIds, "");
        unset($lectureIds[0]);
        /* return if no record avaiable for ordering */
        if (count($lectureIds) < 1) {
            return true;
        }
        if (!$this->updateOrder($lectureIds)) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * Function to get lecture media by id and type
     *
     * @param int $type
     * @return array
     */
    public function getMedia(int $type)
    {
        $srch = new SearchBase(static::DB_TBL_LECTURE_RESOURCE);
        $srch->addCondition('lecsrc_lecture_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('lecsrc_type', '=', $type);
        $srch->addMultipleFields([
            'lecsrc_id',
            'lecsrc_link',
            'lecsrc_lecture_id'
        ]);
        $srch->doNotCalculateRecords();

        if ($type == Lecture::TYPE_RESOURCE_EXTERNAL_URL) {
            $srch->setPageSize(1);
            return FatApp::getDb()->fetch($srch->getResultSet());
        } else {
            $srch->doNotLimitRecords();
            return FatApp::getDb()->fetchAll($srch->getResultSet());
        }
    }
    

    /**
     * Get lecture resources
     *
     * @return array|bool
     */
    public function getResources()
    {
        $srch = new SearchBase(static::DB_TBL_LECTURE_RESOURCE, 'lecsrc');
        $srch->addCondition('lecsrc.lecsrc_lecture_id', '=', $this->mainTableRecordId);
        // $srch->joinTable(
        //     Resource::DB_TBL,
        //     'INNER JOIN',
        //     'resrc.resrc_id = lecsrc.lecsrc_resrc_id',
        //     'resrc'
        // );
        // Change to LEFT JOIN so we get records even if lecsrc_resrc_id = 0
    $srch->joinTable(
        Resource::DB_TBL,
        'LEFT JOIN',  // CHANGED FROM INNER JOIN TO LEFT JOIN
        'resrc.resrc_id = lecsrc.lecsrc_resrc_id',
        'resrc'
    );
    
        $srch->addMultipleFields([
            'resrc_name',
            'resrc_size',
            'resrc_type',
            'lecsrc_id',
            'lecsrc_lecture_id',
            'lecsrc_created',
            'resrc_type', //lines added by rehan
             'lecsrc_type',      // ADD THIS - from lecture_resources table
        'lecsrc_link',      // ADD THIS - for external URLs
        'lecsrc_meta',      // ADD THIS - for quiz metadata
        'lecsrc_duration',  // ADD THIS - for video duration
            'resrc_id',
             'lecsrc_course_id' //lines added by rehan
        ]);
            $srch->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
    $srch->addCondition('resrc.resrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true, true); // Optional condition
        // $srch->addCondition('resrc.resrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        // $srch->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addOrder('lecsrc_id', 'DESC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Function to set lectures count in sections
     *
     * @param int $sectionId
     * @return bool
     */
    public function setLectureCount(int $sectionId)
    {
        /* get count*/
        $srch = new SearchBase(static::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('COUNT(lecture_id) AS section_lectures');
        $srch->addFld('lecture_course_id');
        $srch->addCondition('lecture_section_id', '=', $sectionId);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        /* update lectures count */
        $section = new Section($sectionId);
        $section->setFldValue('section_lectures', $row['section_lectures']);
        if (!$section->save()) {
            $this->error = $section->getError();
            return false;
        }
        /* update course lectures */
        if (!$this->setCourseLectureCount($sectionId)) {
            $this->error = $section->getError();
            return false;
        }
        return true;
    }

    /**
     * Function to set course lectures count in sections
     *
     * @param int $sectionId
     * @return bool
     */
    private function setCourseLectureCount(int $sectionId)
    {
        /* get course id to update course lectures count */
        $courseId = Section::getAttributesById($sectionId, 'section_course_id');
        $srch = new SearchBase(static::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('COUNT(lecture_id) AS course_lectures');
        $srch->addCondition('lecture_course_id', '=', $courseId);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        $course = new Course($courseId);
        $course->assignValues($row);
        if (!$course->save()) {
            $this->error = $course->getError();
            return false;
        }
        return true;
    }

    /**
     * Setup External Url Media
     *
     * @param array $post
     * @return bool
     */
    public function setupMedia(array $post)
    {
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        if (!$duration = YouTube::getYoutubeVideoDuration($post['lecsrc_link'])) {
            $this->error = Label::getLabel('LBL_UNABLE_TO_GET_VIDEO_DETAILS');
            return false;
        }
        $data = [
            'lecsrc_id' => $post['lecsrc_id'],
            'lecsrc_type' => static::TYPE_RESOURCE_EXTERNAL_URL,
            'lecsrc_duration' => $duration,
            'lecsrc_link' => $post['lecsrc_link'],
            'lecsrc_lecture_id' => $this->getMainTableRecordId(),
            'lecsrc_course_id' => $post['lecsrc_course_id'],
            'lecsrc_resrc_id'   => (int)($post['lecsrc_resrc_id'] ?? 0)
            
        ];
        if ($post['lecsrc_id'] < 1) {
            $data['lecsrc_created'] = date('Y-m-d H:i:s');
        } else {
            $data['lecsrc_updated'] = date('Y-m-d H:i:s');
        }
        /* save external url media */
        if (!$db->insertFromArray(static::DB_TBL_LECTURE_RESOURCE, $data, true, [], $data)) {
            $this->error = $db->getError();
            return false;
        }
        /* update lecture duration */
        if (!$this->setDuration()) {
            return false;
        }
        /* update section duration */
        $sectionId = Lecture::getAttributesById($this->getMainTableRecordId(), 'lecture_section_id');
        $section = new Section($sectionId);
        if (!$section->setDuration()) {
            $this->error = $section->getError();
            return false;
        }
        /* update course duration */
        $course = new Course($post['lecsrc_course_id']);
        if (!$course->setDuration()) {
            $this->error = $course->getError();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Set lecture video duration
     *
     * @return bool
     */
    public function setDuration()
    {
        $srch = new SearchBase(static::DB_TBL_LECTURE_RESOURCE);
        $srch->addCondition('lecsrc_lecture_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('lecsrc_type', '=', static::TYPE_RESOURCE_EXTERNAL_URL);
        $srch->addCondition('lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('SUM(lecsrc_duration) AS lecture_duration');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        $duration = 0;
        if ($row) {
            $duration = $row['lecture_duration'];
        }
        /* get lecture content duration */
        $content = static::getAttributesById($this->getMainTableRecordId(), 'lecture_details');
        $content = strip_tags($content);
        $content = count(explode(' ', $content));
        $row['lecture_duration'] = (ceil($content / 100) * 60) + $duration;
        /* update lecture duration */
        $this->assignValues($row);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * Get lecture videos
     *
     * @param array $lectureIds
     * @return array
     */
    public static function getVideos(array $lectureIds)
    {
        $srch = new SearchBase(Lecture::DB_TBL_LECTURE_RESOURCE, 'lecsrc');
        $srch->addDirectCondition('lecsrc.lecsrc_lecture_id IN (' . implode(',', $lectureIds) . ')');
        $srch->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('lecsrc.lecsrc_type', '=', Lecture::TYPE_RESOURCE_EXTERNAL_URL);
        $srch->addMultipleFields(['lecsrc_id', 'lecsrc_lecture_id']);
        $videos = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        if (!empty($videos)) {
            return $videos;
        }
        return [];
    }

    /**
     * Extract lectures ids and free trials data from sections array
     *
     * @param array $sections
     * @return array
     */
    public static function getIds(array $sections)
    {
        $lectureIds = [];
        foreach ($sections as $val) {
            if (isset($val['lectures']) && count($val['lectures']) > 0) {
                foreach ($val['lectures'] as $lecture) {
                    if ($lecture['lecture_is_trial'] == AppConstant::NO) {
                        continue;
                    }
                    $lectureIds[] = $lecture['lecture_id'];
                }
            }
        }
        return $lectureIds;
    }

    /**
     * Get lecture by id
     *
     * @return array
     */
    public function getByCourseId(int $courseId)
    {
        $srch = new SearchBase(static::DB_TBL, 'lecture');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('lecture_course_id', '=', $courseId);
        $srch->addCondition('lecture_id', '=', $this->getMainTableRecordId());
        $srch->addMultipleFields([
            'lecture.lecture_id',
            'lecture.lecture_course_id',
            'lecture.lecture_section_id',
        ]); 
        return FatApp::getDb()->fetch($srch->getResultSet());
    }
}
