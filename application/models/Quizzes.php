<?php

/**
 * This class is used to handle Course
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class Quizzes extends MyAppModel
{

    const DB_TBL = 'tbl_quizzes';
    const DB_TBL_PREFIX = 'quiz_';
    const DB_TBL_LANG = 'tbl_course_details';
    const DB_TBL_APPROVAL_REQUEST = 'tbl_course_approval_requests';
    const DB_TBL_REFUND_REQUEST = 'tbl_course_refund_requests';
    const DB_TBL_INTENDED_LEARNERS = 'tbl_courses_intended_learners';

    /* Course Status */
    const DRAFTED = 1;
    const SUBMITTED = 2;
    const PUBLISHED = 3;

    /* Course Request Status */
    const REQUEST_PENDING = 0;
    const REQUEST_APPROVED = 1;
    const REQUEST_DECLINED = 2;

    /* Course Refund Status */
    const REFUND_PENDING = 0;
    const REFUND_APPROVED = 1;
    const REFUND_DECLINED = 2;

    /* Course Price Type */
    const TYPE_FREE = 1;
    const TYPE_PAID = 2;

    /* Filter Types */
    const FILTER_COURSE = 1;
    const FILTER_TEACHER = 2;
    const FILTER_TAGS = 3;

    private $userId;
    private $userType;
    private $langId;

    /**
     * Initialize Course
     *
     * @param int $id
     * @param int $userId
     * @param int $userType
     */
    public function __construct(int $id = 0, int $userId = 0, int $userType = 0, int $langId = 0)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->langId = $langId;
        parent::__construct(static::DB_TBL, 'quiz_id', $id);
    }

    /**
     * Function to check if course is not sent for the approval
     *
     * @return boolean
     */
    public function canEditCourse()
    {
        if ($this->getMainTableRecordId() > 0) {
            $course = static::getAttributesById($this->getMainTableRecordId(), [
                        'quiz_status',
                        'quiz_active',
                        'quiz_user_id'
            ]);
            if (!$course) {
                $this->error = Label::getLabel('LBL_COURSE_NOT_FOUND');
                return false;
            }
            if ($course['quiz_active'] == AppConstant::INACTIVE) {
                $this->error = Label::getLabel('LBL_QUIZ_IS_IN_INACTIVE_STATE');
                return false;
            }
            if ($course['quiz_user_id'] != $this->userId) {
                $this->error = Label::getLabel('LBL_UNAUTHORIZED_ACCESS');
                return false;
            }
            if (!$course['quiz_status']) {
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
            }
            if (static::SUBMITTED == $course['quiz_status']) {
                $this->error = Label::getLabel('LBL_ACTION_NOT_ALLOWED._COURSE_APPROVAL_REQUEST_IS_IN_PROCESS');
                return false;
            }
            if (static::PUBLISHED == $course['quiz_status']) {
                $this->error = Label::getLabel('LBL_ACTION_NOT_ALLOWED._COURSE_IS_ALREADY_PUBLISHED');
                return false;
            }
        }
        return true;
    }

    /**
     * GetCourse Status List
     *
     * @param integer $key
     * @return string|array
     */
    public static function getStatuses(int $key = null)
    {
        $arr = [
            static::DRAFTED => Label::getLabel('LBL_DRAFTED'),
            static::SUBMITTED => Label::getLabel('LBL_SUBMITTED_FOR_APPROVAL'),
            static::PUBLISHED => Label::getLabel('LBL_PUBLISHED')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Approval Requests Status List
     *
     * @param integer $key
     * @return string|array
     */
    public static function getRequestStatuses(int $key = null)
    {
        $arr = [
            static::REQUEST_PENDING => Label::getLabel('LBL_PENDING'),
            static::REQUEST_APPROVED => Label::getLabel('LBL_APPROVED'),
            static::REQUEST_DECLINED => Label::getLabel('LBL_DECLINED')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Refund Requests Status List
     *
     * @param integer $key
     * @return string|array
     */
    public static function getRefundStatuses(int $key = null)
    {
        $arr = [
            static::REFUND_PENDING => Label::getLabel('LBL_REFUND_PENDING'),
            static::REFUND_APPROVED => Label::getLabel('LBL_REFUND_APPROVED'),
            static::REFUND_DECLINED => Label::getLabel('LBL_REFUND_DECLINED')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Types List
     *
     * @param integer $key
     * @return string|array
     */
    public static function getTypes(int $key = null)
    {
        $arr = [
            static::TYPE_FREE => Label::getLabel('LBL_FREE'),
            static::TYPE_PAID => Label::getLabel('LBL_PAID')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get course levels list or value
     *
     * @param integer $key
     * @return string|array
     */
    public static function getCourseLevels(int $key = null)
    {
        $levelList = json_decode(FatApp::getConfig('CONF_COURSE_LEVELS'));
        $arr = [];
        foreach ($levelList as $level) {
            $arr[$level->id] = Label::getLabel('LBL_' . $level->name);
        }
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Course Filter Types
     *
     * @param integer $key
     * @return string|array
     */
    public static function getFilterTypes(int $key = null)
    {
        $arr = [
            static::FILTER_COURSE => Label::getLabel('LBL_COURSES'),
            static::FILTER_TEACHER => Label::getLabel('LBL_TEACHERS'),
            static::FILTER_TAGS => Label::getLabel('LBL_TAGS')
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Course Rating Filters
     *
     * @return array
     */
    public static function getRatingFilters()
    {
        return [
            '4.5' => Label::getLabel('LBL_4.5_&_UP'),
            '4.0' => Label::getLabel('LBL_4.0_&_UP'),
            '3.5' => Label::getLabel('LBL_3.5_&_UP'),
            '3.0' => Label::getLabel('LBL_3.0_&_UP'),
        ];
    }

    /**
     * Update course requests status
     *
     * @param array $data
     * @return bool
     */
    public function updateRequestStatus(array $data)
    {
        $requestId = FatUtility::int($data['coapre_id']);
        $status = FatUtility::int($data['coapre_status']);
        if ($requestId < 1 || $status < 1) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $eligibility = $this->isEligibleForApproval();
        if ($eligibility['course_is_eligible'] == false && $status != static::REQUEST_DECLINED) {
            $this->error = Label::getLabel('LBL_COURSE_DETAILS_ARE_INCOMPLETE._CATEGORY,_SUBCATEGORY_OR_LANGUAGE_NOT_AVAILABLE.');
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        if (!$this->setupRequest($requestId, $data)) {
            $this->error = $this->getError();
            return false;
        }
        /* update course status */
        $courseStatus = ($status == static::REQUEST_APPROVED) ? static::PUBLISHED : static::DRAFTED;
        if (!$this->updateStatus($courseStatus)) {
            $db->rollbackTransaction();
            return false;
        }
        if ($courseStatus == static::PUBLISHED && !$this->setStatsCount()) {
            $db->rollbackTransaction();
            $this->error = $this->getError();
            return false;
        }
        if (!$this->sendRequestUpdateMailToTeacher($data)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    private function updateStatus($status)
    {
        $this->setFldValue('course_status', $status);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * function to send request status update email to teacher
     *
     * @param array $data
     * @return bool
     */
    private function sendRequestUpdateMailToTeacher(array $data)
    {
        $mail = new FatMailer($this->langId, 'course_request_update_email_to_teacher');
        $vars = [
            '{username}' => ucwords($data['user_first_name'] . ' ' . $data['user_last_name']),
            '{course_title}' => ucwords($data['coapre_title']),
            '{request_status}' => static::getRequestStatuses($data['coapre_status']),
            '{admin_comment}' => empty($data['coapre_remark']) ? Label::getLabel('LBL_NA') : $data['coapre_remark'],
        ];
        $mail->setVariables($vars);
        if (!$mail->sendMail([$data['user_email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Update course refund requests status
     *
     * @param array $data
     * @return bool
     */
    public function updateRefundRequestStatus(array $data)
    {
        $requestId = FatUtility::int($data['corere_id']);
        $status = FatUtility::int($data['corere_status']);
        if ($requestId < 1 || $status < 1) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $srch = new CourseRefundRequestSearch($this->langId, 0, 0);
        $srch->addSearchListingFields();
        $srch->applySearchConditions(['corere_id' => $requestId]);
        $srch->joinUser();
        $srch->addFld('user_lang_id');
        if (!$request = FatApp::getDb()->fetch($srch->getResultSet())) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $order = new OrderCourse($request['ordcrs_id'], $request['user_id'], User::SUPPORT, $request['user_lang_id']);
        if (!$orderData = $order->getCourseToCancel()) {
            $this->error = $order->getError();
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $refundData = [
            'corere_status' => $status,
            'corere_comment' => $data['corere_comment'],
            'corere_updated' => date('Y-m-d H:i:s')
        ];
        $where = ['smt' => 'corere_id = ?', 'vals' => [$requestId]];
        if (!$db->updateFromArray(self::DB_TBL_REFUND_REQUEST, $refundData, $where)) {
            $this->error = $db->getError();
            return false;
        }
        if ($status == static::REFUND_APPROVED) {
            $order->setFldValue('ordcrs_status', OrderCourse::CANCELLED);
            $order->setFldValue('ordcrs_updated', date('Y-m-d H:i:s'));
            if (!$order->save()) {
                $db->rollbackTransaction();
                $order->error = $order->getError();
                return false;
            }
            /* update course progress status */
            if (!FatApp::getDb()->updateFromArray(
                            CourseProgress::DB_TBL,
                            ['crspro_status' => CourseProgress::CANCELLED],
                            ['smt' => 'crspro_ordcrs_id = ?', 'vals' => [$request['ordcrs_id']]]
                    )) {
                $this->error = Label::getLabel('LBL_AN_ERROR_HAS_OCCURRED');
                $db->rollbackTransaction();
                return false;
            }
            if (!$order->refundToLearner($orderData)) {
                $db->rollbackTransaction();
                return false;
            }
            $course = new Course($orderData['course_id']);
            if (!$course->setStudentCount()) {
                $this->error = $course->getError();
                $db->rollbackTransaction();
                return false;
            }
        }
        $request['corere_remark'] = $data['corere_comment'];
        $request = array_merge($request, $refundData);
        static::sendRefundStatusMailToLearner($request);
        $db->commitTransaction();
        return true;
    }

    /**
     * function to send refund request status update email to learner
     *
     * @param array $data
     * @return void
     */
    public static function sendRefundStatusMailToLearner(array $data)
    {
        $mail = new FatMailer($data['user_lang_id'], 'course_refund_update_email_to_learner');
        $vars = [
            '{username}' => ucwords($data['user_first_name'] . ' ' . $data['user_last_name']),
            '{course_title}' => ucwords($data['course_title']),
            '{request_status}' => static::getRefundStatuses($data['corere_status']),
            '{admin_comment}' => empty($data['corere_remark']) ? Label::getLabel('LBL_NA') : $data['corere_remark'],
        ];
        $mail->setVariables($vars);
        $mail->sendMail([$data['user_email']]);
    }

    /**
     * Setup basic details
     *
     * @param array $data
     * @return bool
     */
    public function setupQuestions(array $data)
    {
       // $commaSeparatedQuestions = implode(',', $data['QId']);
        $db = FatApp::getDb();  // Initialize the database object

        // Start the transaction or any other database operation
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        $eligibilityTableName = 'tbl_quiz_questions'; // Different table name
      //  echo '<pre>';print_r($data);die;
      foreach ($data['QId'] as $value) {
        $eligibilityData = [
            'quiz_id' => $data['courseId'],
            'status' => 1, // Assuming the quiz ID is available
            'question_id' => $value,  // Example data, adjust according to your needs
            'created' => date('Y-m-d H:i:s'),
        ];

        // Insert into the second table using a different table name
        if (!$db->insertFromArray($eligibilityTableName, $eligibilityData)) {
            $db->rollbackTransaction(); // Rollback if the second insert fails
            $this->error = $db->getError();
            return false;
        }

        // If everything is successful, commit the transaction
        if (!$db->commitTransaction()) {
            $db->rollbackTransaction();
            $this->error = $db->getError();
            return false;
        }
    }

        return true;

    }
    public function setupGeneralData(array $data)
    {
         
        // if (!$this->canEditCourse()) {
        //     return false;
        // }
        
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        if ($data['quiz_id'] < 1) {
            $this->setFldValue('quiz_created', date('Y-m-d H:i:s'));
        }
         $this->setFldValue('quiz_user_id', $this->userId);
         $this->setFldValue('quiz_updated', date('Y-m-d H:i:s'));
         $this->setFldValue('quiz_status', 0);
         $this->setFldValue('quiz_active', AppConstant::ACTIVE);
         $this->setFldValue('quiz_title', $data['quiz_title']);
         $this->setFldValue('quiz_steps', 1);
         $this->setFldValue('quiz_description', $data['quiz_description']);
         $this->assignValues($data);
            if (!$this->save()) {
                $db->rollbackTransaction();
                $this->error = $this->getError();
                return false;
            }
   
        $db->commitTransaction();
        return true;
    }

    public function setupIntendData(array $data)
    {
         
        // if (!$this->canEditCourse()) {
        //     return false;
        // }
        
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
      
        if ($data['course_id'] < 1) {
            $this->setFldValue('quiz_created', date('Y-m-d H:i:s'));
        }
        //  $this->setFldValue('quiz_user_id', $this->userId);
        //  $this->setFldValue('quiz_updated', date('Y-m-d H:i:s'));
        //  $this->setFldValue('quiz_status', Course::DRAFTED);
        //  $this->setFldValue('quiz_active', AppConstant::ACTIVE);
        //  $this->setFldValue('quiz_title', $data['course_title']);
         $this->setFldValue('quiz_steps', 2);
         //$this->setFldValue('quiz_description', $data['course_details']);
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
     * Function to validate & create unique slug
     *
     * @param string $title
     * @return string
     */
    private function getSlug(string $title)
    {
        $title = MyUtility::createSlug($title);
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('course_slug', '=', $title);
        $srch->doNotCalculateRecords();
        $srch->addFld('course_slug');
        $srch->setPageSize(1);
        if (FatApp::getDb()->fetch($srch->getResultSet())) {
            return CommonHelper::seoUrl($title) . '-' . $this->getMainTableRecordId();
        }
        return CommonHelper::seoUrl($title);
    }

    /**
     * Validate form values
     *
     * @param array $data
     * @return bool
     */
    private function validate(array $data)
    {
        /* validate categories */
        $categories = [$data['course_cate_id'], $data['course_subcate_id']];
        $srch = Category::getSearchObject();
        $srch->doNotCalculateRecords();
        $srch->addFld('cate_id');
        $srch->addCondition('cate_id', 'IN', $categories);
        $srch->addCondition('cate_status', '=', AppConstant::ACTIVE);
        $srch->addCondition('cate_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $categories = FatApp::getDb()->fetchAll($srch->getResultSet(), 'cate_id');
        if (!array_key_exists($data['course_cate_id'], $categories)) {
            $this->error = Label::getLabel('LBL_CATEGORY_NOT_AVAILABLE');
            return false;
        }
        if ($data['course_subcate_id'] > 0 && !array_key_exists($data['course_subcate_id'], $categories)) {
            $this->error = Label::getLabel('LBL_SUBCATEGORY_NOT_AVAILABLE');
            return false;
        }
        $courseLang = CourseLanguage::getAttributesById($data['course_clang_id'], ['clang_active', 'clang_deleted']);
        if ($courseLang['clang_active'] == AppConstant::INACTIVE || !empty($courseLang['clang_deleted'])) {
            $this->error = Label::getLabel('LBL_LANGUAGE_NOT_AVAILABLE');
            return false;
        }
        return true;
    }

    /**
     * Setup Course Basic Lang Data
     *
     * @param array $data
     * @return bool
     */
    private function setupLangData(array $data)
    {
        if (!FatApp::getDb()->insertFromArray(static::DB_TBL_LANG, $data, false, [], $data)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Setup basic details
     *
     * @param array $data
     * @return bool
     */
    public function setupSettings(array $data)
    {
       
        // if (!$this->canEditCourse()) {
        //     return false;
        // }
         //echo '<pre>';print_r($data);die;
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
         $this->setFldValue('quiz_updated', date('Y-m-d H:i:s'));
       $this->setFldValue('quiz_duration', $data['quiz_duration']);
       $this->setFldValue('quiz_pass_percentage', $data['quiz_pass_percentage']);
       $this->setFldValue('quiz_validity', $data['quiz_validity']);
       $this->setFldValue('quiz_fail_message', $data['quiz_fail_message']);
       $this->setFldValue('quiz_pass_message', $data['quiz_pass_message']);
       $this->setFldValue('quiz_offer_certificate', $data['quiz_offer_certificate']);
       $this->setFldValue('quiz_status', 1);
       $this->setFldValue('quiz_steps', 3);
       
        if (!$this->save()) {
            $db->rollbackTransaction();
            $this->error = $this->getError();
            return false;
        }
       
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
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
        if ($this->getMainTableRecordId() < 1) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        if (!$this->canEditCourse() || !$this->canDeleteCourse()) {
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        /* mark course deleted */
        $this->setFldValue('course_deleted', date('Y-m-d H:i:s'));
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        if (!$this->deleteMedia()) {
            $db->rollbackTransaction();
            return false;
        }
        $db->commitTransaction();
        return true;
    }

    private function canDeleteCourse()
    {
        $courseId = $this->getMainTableRecordId();
        $courseDeleted = static::getAttributesById($courseId, ['course_deleted']);
        if ((int) $courseDeleted['course_deleted'] > 0) {
            $this->error = Label::getLabel('LBL_COURSE_ALREADY_DELETED');
            return false;
        }
        /* check if course order is in progress */
        $srch = new SearchBase(OrderCourse::DB_TBL);
        $srch->addCondition('ordcrs_course_id', '=', $courseId);
        $srch->addCondition('ordcrs_status', '!=', OrderCourse::COMPLETED);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (FatApp::getDb()->fetch($srch->getResultSet())) {
            $this->error = Label::getLabel('LBL_CANNOT_DELETE_AS_THE_COURSE_IS_ASSOCIATED_WITH_ORDERS');
            return false;
        }
        return true;
    }

    private function deleteMedia()
    {
        $courseId = $this->getMainTableRecordId();
        /* delete course images */
        $files = new Afile(Afile::TYPE_COURSE_IMAGE);
        $filesList = $files->getFilesByType($courseId);
        if ($filesList) {
            foreach ($filesList as $file) {
                if (!$files->removeById($file['file_id'], true)) {
                    $this->error = $files->getError();
                    return false;
                }
            }
        }
        /* delete course preview video */
        $files = new Afile(Afile::TYPE_COURSE_PREVIEW_VIDEO);
        $filesList = $files->getFilesByType($courseId);
        if ($filesList) {
            foreach ($filesList as $file) {
                if (!$files->removeById($file['file_id'], true)) {
                    $this->error = $files->getError();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check and send eligibility status for approval
     *
     * @return array
     */
    public function isEligibleForApproval()
    {
        $courseId = $this->getMainTableRecordId();
        $criteria['course_is_eligible'] = false;

        $srch = new QuizSearch(0, $this->userId, $this->userType);
       
        $srch->addCondition('quiz.quiz_id', '=', $courseId);
        $srch->setPageSize(1);
        $courseData = FatApp::getDb()->fetch($srch->getResultSet());
        
        if(isset($courseData) && !empty($courseData))
        {
            if($courseData['quiz_steps']==1)
            {
                $criteria['general_info_js']=1;
            }
            if($courseData['quiz_steps']==2)
            {
                $criteria['general_info_js']=1;
                $criteria['courses_intended_learners1']=1;
            }
            
            $criteria['course_is_eligible'] = true;
        }
        else
        {
           // $criteria['general_info_js']=1;
            $criteria['course_is_eligible'] = false;
        }

         
         return $criteria;
        // $courseId = $this->getMainTableRecordId();
        // $criteria = json_decode(FatApp::getConfig('CONF_COURSE_APPROVAL_ELIGIBILITY_CRITERIA'));
        // $criteria = array_fill_keys($criteria, 0);
        // /* get course curriculum and price tabs data */
        // $srch = new CourseSearch(0, $this->userId, $this->userType);
        // $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'subcate.cate_id = course.course_subcate_id', 'subcate');
        // $srch->applyPrimaryConditions();
        // $srch->addMultipleFields([
        //     'IF(course_sections > 0, 1, 0) as course_sections',
        //     'IF(course_lectures > 0, 1, 0) as course_lectures',
        //     'IF(course_type = ' . Course::TYPE_FREE . ' OR course_currency_id > 0, 1, 0) as course_currency_id',
        //     'IF(course_type = ' . Course::TYPE_FREE . ' OR course_price > 0, 1, 0) as course_price',
        //     'IF(cate.cate_deleted IS NULL AND cate.cate_status = ' . AppConstant::ACTIVE . ', 1, 0) course_cate',
        //     'IF(course.course_subcate_id > 0 AND (subcate.cate_deleted IS NOT NULL OR subcate.cate_status = ' . AppConstant::INACTIVE . '), 0, 1) course_subcate',
        //     'IF(clang.clang_deleted IS NULL AND clang.clang_active = ' . AppConstant::ACTIVE . ', 1, 0) course_clang'
        // ]);
        // $srch->addCondition('course.course_id', '=', $courseId);
        // $srch->setPageSize(1);
        // $courseData = FatApp::getDb()->fetch($srch->getResultSet());

        // if ($courseData) {
        //     $criteria = array_merge($criteria, $courseData);
        // }

        // /* check sections without lectures */
        // $srch = new SearchBase(Section::DB_TBL);
        // $srch->doNotCalculateRecords();
        // $srch->setPageSize(1);
        // $srch->addCondition('section_course_id', '=', $courseId);
        // $srch->addCondition('section_lectures', '=', 0);
        // $srch->addDirectCondition('section_deleted IS NULL');
        // $srch->addFld('COUNT(section_id) as sections_count');
        // $sections = FatApp::getDb()->fetch($srch->getResultSet());
        // if (($courseData && $courseData['course_sections'] > 0) && ($sections && $sections['sections_count'] > 0)) {
        //     $criteria['course_sections'] = 0;
        // }
        // /* get course lang data */
        // $srch = new SearchBase(Course::DB_TBL_LANG);
        // $srch->doNotCalculateRecords();
        // $srch->setPageSize(1);
        // $srch->addCondition('course_id', '=', $courseId);
        // $srch->addMultipleFields([
        //     'IF(course_id IS NULL, 0, 1) as course_lang',
        //     /* 'IF(course_welcome IS NULL, 0, 1) as course_welcome',
        //       'IF(course_congrats IS NULL, 0, 1) as course_congrats', */
        //     '1 as course_welcome',
        //     '1 as course_congrats',
        //     'IF(course_srchtags IS NULL, 0, 1) as course_tags',
        // ]);
        // if ($courseLang = FatApp::getDb()->fetch($srch->getResultSet())) {
        //     $criteria = array_merge($criteria, $courseLang);
        // }
        // /* get course intended learners data */
        // $srch = new SearchBase(IntendedLearner::DB_TBL);
        // $srch->doNotCalculateRecords();
        // $srch->setPageSize(1);
        // $srch->addCondition('coinle_course_id', '=', $courseId);
        // $srch->addFld('coinle_id');
        // $criteria['courses_intended_learners'] = (FatApp::getDb()->fetch($srch->getResultSet())) ? 1 : 0;
        // /* get course image and video */
        // $afile = new Afile(Afile::TYPE_COURSE_IMAGE);
        // $criteria['course_image'] = ($afile->getFilesByType($courseId)) ? 1 : 0;
        // $afile = new Afile(Afile::TYPE_COURSE_PREVIEW_VIDEO);
        // $criteria['course_preview_video'] = ($afile->getFilesByType($courseId)) ? 1 : 0;
        // $criteria['course_is_eligible'] = true;

        // if (!empty(array_search(0, $criteria))) {
        //     $criteria['course_is_eligible'] = false;
        // }
        // return $criteria;
    }

    /**
     * Submit course for approval from admin
     *
     * @return bool
     */
    public function submitApprovalRequest()
    {
        if (!$this->canEditCourse()) {
            return false;
        }
        $eligibility = $this->isEligibleForApproval();
        if ($eligibility['course_is_eligible'] == false) {
            $this->error = Label::getLabel('LBL_COURSE_IS_NOT_ELIGIBILE_FOR_REVIEW._PLEASE_COMPLETE_THE_DETAILS');
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        if (!$this->setupRequest(0)) {
            $this->error = $this->getError();
            return false;
        }
        /* update course status */
        if (!$this->updateStatus(static::SUBMITTED)) {
            $db->rollbackTransaction();
            $this->error = $this->getError();
            return false;
        }
        if (!$this->sendApprovalRequestEmailToAdmin()) {
            $db->rollbackTransaction();
            $this->error = $db->getError();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Add/Edit Approval Request
     *
     * @param int   $requestId
     * @param array $data
     * @return bool
     */
    private function setupRequest(int $requestId, array $data = [])
    {
        $db = FatApp::getDb();
        if ($requestId < 1) {
            $course = $this->get();
            $intendedLearner = new IntendedLearner();
            $intendedLearnerData = $intendedLearner->get($this->getMainTableRecordId());
            $price = 0;
            if ($course['course_price'] > 0) {
                $baseCurrency = MyUtility::getSiteCurrency();
                $price = CourseUtility::convertToSystemCurrency($course['course_price'], $baseCurrency['currency_id']);
            }
            $data = [
                'coapre_course_id' => $this->getMainTableRecordId(),
                'coapre_cate_id' => $course['course_cate_id'],
                'coapre_subcate_id' => $course['course_subcate_id'],
                'coapre_clang_id' => $course['course_clang_id'],
                'coapre_level' => $course['course_level'],
                'coapre_certificate' => $course['course_certificate'],
                'coapre_status' => static::REQUEST_PENDING,
                'coapre_created' => date('Y-m-d H:i:s'),
                'coapre_title' => $course['course_title'],
                'coapre_subtitle' => $course['course_subtitle'],
                'coapre_details' => $course['course_details'],
                'coapre_price' => $price,
                'coapre_duration' => $course['course_duration'],
                'coapre_srchtags' => $course['course_srchtags'],
                'coapre_learners' => json_encode($intendedLearnerData[IntendedLearner::TYPE_LEARNERS]),
                'coapre_learnings' => json_encode($intendedLearnerData[IntendedLearner::TYPE_LEARNING]),
                'coapre_requirements' => json_encode($intendedLearnerData[IntendedLearner::TYPE_REQUIREMENTS]),
            ];
        } else {
            $data = [
                'coapre_id' => $requestId,
                'coapre_status' => $data['coapre_status'],
                'coapre_remark' => $data['coapre_remark'],
                'coapre_updated' => date('Y-m-d H:i:s')
            ];
        }
        if (!$db->insertFromArray(static::DB_TBL_APPROVAL_REQUEST, $data, false, [], $data)) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * function to send approval request email to admin
     *
     * @return bool
     */
    private function sendApprovalRequestEmailToAdmin()
    {
        $srch = new SearchBase(static::DB_TBL, 'course');
        $srch->addCondition('course.course_id', '=', $this->getMainTableRecordId());
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'crsdetail.course_id = course.course_id', 'crsdetail');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'course.course_user_id = teacher.user_id', 'teacher');
        $srch->addCondition('course.course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('course.course_user_id', '=', $this->userId);
        $srch->addMultipleFields([
            'teacher.user_first_name AS teacher_first_name',
            'teacher.user_last_name AS teacher_last_name',
            'crsdetail.course_title AS course_title',
        ]);
        $data = FatApp::getDb()->fetch($srch->getResultSet());
        $mail = new FatMailer($this->langId, 'course_approval_request_email_to_admin');
        $vars = [
            '{username}' => ucwords($data['teacher_first_name'] . ' ' . $data['teacher_last_name']),
            '{course_title}' => ucwords($data['course_title']),
            '{course_link}' => MyUtility::makeFullUrl('CourseRequests', '', [], CONF_WEBROOT_BACKEND),
        ];
        $mail->setVariables($vars);
        if (!$mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Update duration
     *
     * @return bool
     */
    public function setDuration()
    {
        $srch = new SearchBase(Section::DB_TBL);
        $srch->addCondition('section_course_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('section_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('IFNULL(SUM(section_duration), 0) AS course_duration');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        /* update duration */
        $this->assignValues($row);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * Function to set students count in courses
     *
     * @param int $courseId
     * @return bool
     */
    public function setStudentCount()
    {
        /* get count */
        $srch = new SearchBase(OrderCourse::DB_TBL, 'ordcrs');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordcrs.ordcrs_order_id', 'orders');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('COUNT(ordcrs_order_id) AS course_students');
        $srch->addCondition('ordcrs_course_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('ordcrs_status', '!=', OrderCourse::CANCELLED);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        /* update student count */
        $this->assignValues($row);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * Get course data by id
     *
     * @return array|false
     */
    public function get()
    {
        $srch = new SearchBase(static::DB_TBL, 'quiz');
      //  $srch->joinTable(static::DB_TBL_LANG, 'LEFT JOIN', 'crsdetail.course_id = course.course_id', 'crsdetail');
       // $srch->addCondition('course.course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('quiz.quiz_id', '=', $this->getMainTableRecordId());
        $srch->addMultipleFields([
            'quiz_title',
            // 'crsdetail.course_subtitle',
            // 'crsdetail.course_details',
            // 'course.course_id',
            // 'course.course_cate_id',
            // 'course.course_subcate_id',
            // 'course.course_level',
            // 'course.course_certificate',
            // 'course.course_clang_id',
            // 'course.course_lectures',
            // 'course.course_user_id',
            // 'course.course_reviews',
            // 'course.course_ratings',
            // 'course.course_certificate',
            // 'course.course_currency_id',
            // 'course.course_price',
            // 'course_duration',
            // 'course_srchtags'
        ]);
        $srch->setPageSize(1);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Feedback Course
     * 
     * @param array $post
     * @return bool
     */
    public function feedback(array $post): bool
    {
        $ordcrs = new OrderCourse($post['ordcrs_id'], $this->userId, $this->userType, $this->langId);
        if (!$course = $ordcrs->getCourseToFeedback()) {
            $this->error = $ordcrs->getError();
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $ratingReview = new CourseRatingReview($course['course_user_id'], $this->userId);
        $post['ratrev_lang_id'] = $this->langId;
        if (!$ratingReview->addReview(AppConstant::COURSE, $this->getMainTableRecordId(), $post)) {
            $db->rollbackTransaction();
            $this->error = $ratingReview->getError();
            return false;
        }
        $record = new OrderCourse($course['ordcrs_id']);
        $record->assignValues(['ordcrs_reviewed' => AppConstant::YES, 'ordcrs_updated' => date('Y-m-d H:i:s')]);
        if (!$record->save()) {
            $this->error = $record->getError();
            $db->rollbackTransaction();
            return false;
        }
        $db->commitTransaction();
        if (FatApp::getConfig('CONF_DEFAULT_REVIEW_STATUS') == CourseRatingReview::STATUS_APPROVED) {
            $ratingReview->sendMailToTeacher($course);
        } else {
            $ratingReview->sendMailToAdmin($course);
        }
        return true;
    }

    /**
     * Set rating & reviews count
     *
     * @return bool
     */
    public function setRatingReviewCount()
    {
        $srch = new SearchBase(CourseRatingReview::DB_TBL, 'ratrev');
        $srch->addMultipleFields([
            'COUNT(*) as course_reviews',
            'IFNULL(ROUND(AVG(ratrev.ratrev_overall), 2), 0) as course_ratings'
        ]);
        $srch->addCondition('ratrev.ratrev_status', '=', CourseRatingReview::STATUS_APPROVED);
        $srch->addCondition('ratrev.ratrev_type_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('ratrev.ratrev_type', '=', AppConstant::COURSE);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (!$data = FatApp::getDb()->fetch($srch->getResultSet())) {
            $data = ['course_ratings' => 0, 'course_reviews' => 0];
        }
        $this->assignValues($data);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * Update stats for category and students
     *
     * @return bool
     */
    private function setStatsCount()
    {
        /* update category course count */
        $courseData = static::getAttributesById($this->getMainTableRecordId(), ['course_cate_id', 'course_subcate_id']);
        $srch = new SearchBase(Course::DB_TBL);
        $srch->joinTable(
                Category::DB_TBL,
                'INNER JOIN',
                'c.cate_id = course_cate_id OR c.cate_id = course_subcate_id',
                'c'
        );
        $srch->addCondition('course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('course_status', '=', static::PUBLISHED);
        $srch->addCondition('c.cate_id', 'IN', $courseData);
        $srch->doNotCalculateRecords();
        $srch->addMultipleFields(['c.cate_id, COUNT(*)']);
        $srch->addGroupBy('cate_id');
        $rows = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        if (count($rows) > 0) {
            foreach ($rows as $id => $count) {
                $category = new Category($id);
                $category->assignValues(['cate_records' => $count]);
                if (!$category->save()) {
                    $this->error = $category->getError();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Settle payment for completed courses
     *
     * @return void
     */
    public function completedCourseSettlement()
    {
        $days = FatApp::getConfig('CONF_COURSE_CANCEL_DURATION');
        $srch = new SearchBase(OrderCourse::DB_TBL, 'ordcrs');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'ordcrs.ordcrs_order_id = orders.order_id', 'orders');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'ordcrs.ordcrs_course_id = course.course_id', 'course');
        $srch->joinTable(Course::DB_TBL_LANG, 'LEFT JOIN', 'crsdetail.course_id = course.course_id', 'crsdetail');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'course.course_user_id = teacher.user_id', 'teacher');
        $srch->joinTable(
                Course::DB_TBL_REFUND_REQUEST, 'LEFT JOIN', 'corere.corere_ordcrs_id = ordcrs.ordcrs_id', 'corere'
        );
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('ordcrs.ordcrs_status', '=', OrderCourse::COMPLETED);
        $srch->addDirectCondition('DATE_ADD(orders.order_addedon, INTERVAL ' . $days . ' DAY) < "' . date('Y-m-d H:i:s') . '"', 'AND');
        $srch->addDirectCondition('ordcrs.ordcrs_teacher_paid IS NULL');
        $cnd = $srch->addCondition('corere.corere_id', 'IS', 'mysql_func_NULL', 'AND', true);
        $cnd->attachCondition('corere.corere_status', '=', static::REFUND_DECLINED);
        $srch->addOrder('ordcrs.ordcrs_id', 'ASC');
        $srch->addMultipleFields([
            'ordcrs_commission',
            'ordcrs_amount',
            'ordcrs_discount',
            'ordcrs_id',
            'order_id',
            'teacher.user_id as teacher_id',
            'teacher.user_email as teacher_email',
            'teacher.user_first_name',
            'teacher.user_last_name',
            'crsdetail.course_title',
            'teacher.user_lang_id'
        ]);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        $db = FatApp::getDb();
        while ($course = $db->fetch($resultSet)) {
            $db->startTransaction();
            $commission = ($course['ordcrs_commission'] / 100) * $course['ordcrs_amount'];
            $teacherAmount = round(($course['ordcrs_amount'] - $commission), 2);
            if ($teacherAmount > 0) {
                $comment = Label::getLabel('LBL_PAYMENT_ON_COURSE_"{course-name}"');
                $comment = str_replace('{course-name}', $course['course_title'], $comment);
                $txn = new Transaction($course['teacher_id'], Transaction::TYPE_TEACHER_PAYMENT);
                if (!$txn->credit($teacherAmount, $comment)) {
                    $this->error = $txn->getError();
                    $db->rollbackTransaction();
                    return false;
                }
                $txn->sendEmail();
                $course['amount'] = $teacherAmount;
                $this->sendPaymentMailToTeacher($course);
            }
            $orderCourse = new OrderCourse($course['ordcrs_id']);
            $earnings = $course['ordcrs_amount'] - ($course['ordcrs_discount'] + $teacherAmount);
            $orderCourse->assignValues([
                'ordcrs_teacher_paid' => $teacherAmount,
                'ordcrs_earnings' => FatUtility::float($earnings),
                'ordcrs_updated' => date('Y-m-d H:i:s')
            ]);
            if (!$orderCourse->save()) {
                $this->error = $orderCourse->getError();
                $db->rollbackTransaction();
                return false;
            }
            $db->commitTransaction();
        }
        return true;
    }

    /**
     * function to send course payment settlement email to teacher
     *
     * @param array $data
     * @return void
     */
    public function sendPaymentMailToTeacher(array $data)
    {
        $mail = new FatMailer($data['user_lang_id'], 'completed_course_settlement_email_to_teacher');

        $vars = [
            '{username}' => ucwords($data['user_first_name'] . ' ' . $data['user_last_name']),
            '{course_title}' => ucwords($data['course_title']),
            '{amount}' => MyUtility::formatMoney($data['amount']),
        ];
        $mail->setVariables($vars);
        $mail->sendMail([$data['teacher_email']]);
    }

    /**
     * Get course data by slug
     *
     * @param string $slug
     * @return array|null
     */
    public static function getCourseBySlug(string $slug)
    {
        $srch = new SearchBase(Course::DB_TBL, 'crs');
        $srch->addMultipleFields(['course_id', 'crs.course_slug']);
        $srch->addCondition('course_slug', '=', $slug);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

}
