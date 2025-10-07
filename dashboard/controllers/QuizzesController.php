<?php

/**
 * This Controller is used for handling courses
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class QuizzesController extends DashboardController
{

    /**
     * Initialize Courses
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Search Form
     *
     */
    public function index()
    {
        $userId = $this->siteUserId;
        // if($userId!=449)
        // {
        //  FatUtility::exitWithErrorCode(404);
        // }
        $frm = $this->getSearchForm();
        $this->set('frm', $frm);
        $this->_template->addJs('js/jquery.barrating.min.js');
        $this->_template->render();
    }

    /**
     * Search & List Plans
     */
    public function search()
    {
       
        $posts = FatApp::getPostedData();
        $userId = $this->siteUserId;
      
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_subcateid'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if($post['pageno']==0)
        {
            $post['pageno']=1;
        }
        
      
      $srch = new QuizSearch($this->siteLangId, 0, User::SUPPORT);

      // Join the tbl_quiz_questions to count the number of questions per quiz
      $srch->joinTable(
          'tbl_quiz_questions',       // The table to join
          'LEFT JOIN',                // Join type
          'quiz.quiz_id = quiz_questions.quiz_id',  // Join condition
          'quiz_questions'            // Alias for tbl_quiz_questions
      );
       $srch->joinTable('tbl_users', 'INNER JOIN', 'tutor.user_id = quiz.quiz_user_id', 'tutor');
       $srch->addCondition('quiz.quiz_delete', '=', 0);
      // Add a COUNT aggregate field for the number of questions per quiz
        $srch->addFld('COUNT(quiz_questions.id) as question_count');
        $srch->addFld('tutor.user_first_name as fname');
        $srch->addFld('tutor.user_last_name as lname');
      // Add any other fields from tbl_quizzes you need
      $srch->addFld('quiz.*');
      if (isset($userId) && !empty($userId)) {
        $srch->addCondition('quiz.quiz_user_id', '=', $userId);
        }
      // Ensure that grouping is applied for the COUNT function to work properly
      $srch->addGroupBy('quiz.quiz_id');
      
      // Set pagination
      $srch->setPageSize($post['pagesize']);
      $srch->setPageNumber($post['pageno']);
       
      $srch->addOrder('quiz.quiz_id', 'DESC');
       
      $orders = $srch->fetchAndFormat();
      
        $this->sets([
            'courses' => $orders,
            'post' => $post,
            'recordCount' => $srch->recordCount(),
            'courseStatuses' => Course::getStatuses(),
            'courseTypes' => Course::getTypes(),
            'orderStatuses' => CourseProgress::getStatuses(),
        ]);
        $this->_template->render(false, false);
    }
    /**
     * Render Course Manage Page
     *
     * @param mixed $courseId
     */

  
    public function form($courseId = 0)
    {
        if ($this->siteUserType == User::LEARNER) {
            FatUtility::exitWithErrorCode(404);
        }
        if (!empty($courseId) && FatUtility::int($courseId) < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $courseId = FatUtility::int($courseId);
         
        $courseTitle = '';
        if ($courseId > 0) {
            $srch = new QuizSearch($this->siteLangId, $this->siteUserId, User::TEACHER);
          //  $srch->applyPrimaryConditions();
            $srch->addMultipleFields(['quiz.quiz_id', 'quiz_title']);
            $srch->addCondition('quiz.quiz_id', '=', $courseId);
          //  $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
            $srch->setPageSize(1);
            if (!$course = FatApp::getDb()->fetch($srch->getResultSet())) {
                Message::addErrorMessage(Label::getLabel('LBL_QUIZ_NOT_FOUND'));
                FatApp::redirectUser(MyUtility::generateUrl('Quizzes'));
            }
            $courseTitle = $course['quiz_title'];
            
        }
  
        $this->set('courseTitle', $courseTitle);
        $this->set('courseId', $courseId);
       // $this->set('course_idaaa', $courseId);
        $this->set('siteLangId', $this->siteLangId);
        $this->set("includeEditor", true);
        $this->_template->addJs(['js/jquery.tagit.js', 'js/jquery.ui.touch-punch.min.js']);
        
        $this->_template->render();
    }

    /**
     * Render Basic Details Page
     *
     * @param int $courseId
     */
    public function generalForm(int $courseId = 0)
    {
     
        $course = [];
        if ($courseId > 0) {
            $srch = new QuizSearch($this->siteLangId, $this->siteUserId, User::TEACHER);
           // $srch->applyPrimaryConditions();
            $srch->addMultipleFields([
                'quiz_title',
                'quiz_description',
                'quiz_id',
             ]);
            $srch->addCondition('quiz.quiz_id', '=', $courseId);
            $srch->setPageSize(1);
           
            if (!$course = FatApp::getDb()->fetch($srch->getResultSet())) {
                FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
            }
        }
         
        $frm = $this->getGeneralForm();
        $frm->fill($course);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function addForm(int $courseId = 0)
    {
        
        $course = [];
        if ($courseId > 0) {
            $srch = new QuizSearch($this->siteLangId, $this->siteUserId, User::TEACHER);
           // $srch->applyPrimaryConditions();
            $srch->addMultipleFields([
                'quiz_title',
                'quiz_description',
             ]);
            $srch->addCondition('quiz.quiz_id', '=', $courseId);
           // $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
            $srch->setPageSize(1);
            //echo $srch->getQuery();die;
            if (!$course = FatApp::getDb()->fetch($srch->getResultSet())) {
                FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
            }
        }
         
        $frm = $this->getGeneralForm();
        $frm->fill($course);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
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

   

    /**
     * Render Media Page
     *
     * @param int $courseId
     */
    public function mediaForm(int $courseId)
    {
       // echo 'fsd';die;
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* validate course id */
        if (!Quizzes::getAttributesById($courseId, 'quiz_id')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
         
        /* get form and fill */
        $frm = $this->getMediaForm();
        $frm->fill(['course_id' => $courseId]);
        /* get course image required dimensions */
        $file = new Afile(Afile::TYPE_COURSE_IMAGE);
        $image = $file->getFile($courseId);
        $dimensions = $file->getImageSizes(Afile::SIZE_LARGE);
        /* get video url */
        $file = new Afile(Afile::TYPE_COURSE_PREVIEW_VIDEO);
        $previewVideo = $file->getFile($courseId);
        $this->sets([
            'frm' => $frm,
            'courseId' => $courseId,
            'extensions' => Afile::getAllowedExts(Afile::TYPE_COURSE_IMAGE),
            'videoFormats' => Afile::getAllowedExts(Afile::TYPE_COURSE_PREVIEW_VIDEO),
            'dimensions' => $dimensions,
            'filesize' => MyUtility::convertBitesToMb(Afile::getAllowedUploadSize()),
            'previewVideo' => $previewVideo,
            'image' => $image,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup course media
     *
     * @return json
     */
    public function setupMedia()
    {
        $frm = $this->getMediaForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieWithError($course->getError());
        }
        if (empty($_FILES['course_image']['name']) && empty($_FILES['course_preview_video']['name'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_MEDIA_SELECTED'));
        }
        $type = '';
        $files = [];
        if (!empty($_FILES['course_image']['name'])) {
            $type = Afile::TYPE_COURSE_IMAGE;
            $files = $_FILES['course_image'];
        }
        if (!empty($_FILES['course_preview_video']['name'])) {
            $type = Afile::TYPE_COURSE_PREVIEW_VIDEO;
            $files = $_FILES['course_preview_video'];
        }
        $file = new Afile($type);
        if (!$file->saveFile($files, $post['course_id'], true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_FILE_UPLOADED_SUCCESSFULLY'));
    }

    /**
     * Remove course media files
     *
     * @param int $courseId
     */
    public function removeMedia(int $courseId)
    {
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieWithError($course->getError());
        }
        $type = FatApp::getPostedData('type');
        $file = new Afile($type);
        if (!$file->removeFile($courseId, 0, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_FILE_REMOVED_SUCCESSFULLY'));
    }

  


    
    /**
     * Setup Intended Learners Data
     *
     */
    public function setupIntendedLearners()
    {
        
        $frm = $this->getIntendedLearnersForm();
        $post = FatApp::getPostedData();
        $course_id = intval($post['course_id']);
         
         
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
       

        $course = new Quizzes($course_id, $this->siteUserId, $this->siteUserType);
        if (!$course->setupIntendData($post)) {
            FatUtility::dieJsonError($course->getError());
        }
         
         
       FatUtility::dieJsonSuccess([
        'msg' => Label::getLabel('LBL_QUESTION_SETUP_SUCCESSFUL'),
        'courseId' => $course->getMainTableRecordId(),
         
    ]);
    }

     /**
     * Setup basic details
     *
     * @return json
     */
    public function setup()
    {
        $frm = $this->getGeneralForm();
        $post = FatApp::getPostedData();
         
       
        $course_id = intval($post['quiz_id']);
        $_SESSION['quiz_title']=$_POST['quiz_title'];
        $_SESSION['quiz_description']=$_POST['quiz_description'];
        
        $course = new Quizzes($course_id, $this->siteUserId, $this->siteUserType);
        if (!$course->setupGeneralData($post)) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess([
            'msg' => Label::getLabel('LBL_GENERAL_DETAILS_ADDED_SUCCESSFUL'),
            'courseId' => $course->getMainTableRecordId(),
            'title' => $post['quiz_title'],
        ]);
    }

    /**
     * Updating Intended Learner Records sort order
     *
     * @return json
     */
    public function updateIntendedOrder()
    {
        $ids = FatApp::getPostedData('order');
        $intended = new IntendedLearner();
        if (!$intended->updateOrder($ids)) {
            FatUtility::dieJsonError($intended->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_ORDER_SETUP_SUCCESSFUL'));
    }

    /**
     * function to delete course intended learner
     *
     * @param int $indLearnerId
     * @return json
     */
    public function deleteIntendedLearner(int $indLearnerId)
    {
        if ($indLearnerId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /*  check if record exists */
        if (!$courseId = IntendedLearner::getAttributesById($indLearnerId, 'coinle_course_id')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }
        $intended = new IntendedLearner($indLearnerId);
        if (!$intended->delete()) {
            FatUtility::dieJsonError($intended->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Render Pricing Page
     *
     * @param int $courseId
     */
    public function priceForm(int $courseId)
    {
         
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        
        $courseObj = new Quizzes($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
      
        // if (!$courseObj->canEditCourse()) {
        //     FatUtility::dieJsonError($courseObj->getError());
        // }
        
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $posts['pagesize'] = 1000;
        $frm = QuestionSearch::getSearchForm($userType);
         if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        
        $srch = new QuestionSearch($langId, $userId, $userType);
        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'subcate.cate_id = question  .question_subcat', 'subcate');
       $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'cate.cate_id = question  .question_cat', 'cate');
          $srch->addMultipleFields([
            '*',           
             'cate.cate_identifier as  catname',
           'subcate.cate_identifier as subcatname'
            ]);
 
            
            if (isset($userId) && !empty($userId)) {
            $srch->addCondition('question_added_by', '=', $userId);
            }

 
        $srch->addCondition('question_status', '=', 1);
        $srch->addOrder('question_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
         $srch->setPageNumber($post['pageno']);
   
        $rows = $srch->fetchAndFormat();
        $frm = $this->getPriceForm();
       $myDate = new MyDate();
        $myDate->setMonthAndWeekNames();
        $this->sets([
            
            'frm' => $frm,
            'post' => $post,
            'courseId' => $courseId,
            'myDate' => $myDate,
            'recordCount' => $srch->recordCount(),
            'planType' => Plan::PLAN_TYPE_CLASSES,
            'allClasses' => $srch->groupDates($rows),
        ]);
        $this->_template->render(false, false);
    }


    public function setupQuestions()
    {
        if ($_POST['courseId'] < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }  
        else
        {
            $course = new Quizzes($_POST['courseId'], $this->siteUserId, $this->siteUserType);
             if (!$course->setupQuestions($_POST)) {
                FatUtility::dieJsonError($course->getError());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('QUESTIONS_ADDED_SUCCESSFUL'));
        }

    }
    /**
     * Get Prices Data
     *
     * @return json
     */
    public function setupPrice()
    {
        $frm = $this->getPriceForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_currency_id'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }

        $price = 0;
        if ($post['course_type'] == Course::TYPE_PAID) {
            /* validate currency */
            $currencyId = FatUtility::int($post['course_currency_id']);
            if ($currencyId > 0 && Currency::getAttributesById($currencyId, 'currency_active') == AppConstant::INACTIVE) {
                FatUtility::dieJsonError(Label::getLabel('LBL_CURRENCY_NOT_AVAILABLE'));
            }
            if ($post['course_price'] > 0) {
                $price = CourseUtility::convertToSystemCurrency($post['course_price'], $post['course_currency_id']);
            }
            if ($price < 1) {
                $label = Label::getLabel('LBL_COURSE_PRICE_CANNOT_BE_LESS_THAN_1_{currency}');
                $label = str_replace('{currency}', MyUtility::getSystemCurrency()['currency_code'], $label);
                FatUtility::dieJsonError($label);
            }
        }
        $course->assignValues([
            'course_type' => $post['course_type'],
            'course_currency_id' => $post['course_currency_id'],
            'course_price' => $price,
        ]);
        if (!$course->save()) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETUP_SUCCESSFUL'));
    }

    /**
     * Render Curriculum Page
     *
     * @param int $courseId
     */
    public function curriculumForm(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        /* validate course id */
        if (!$course->get()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }
        /* get form and fill */
        $frm = $this->getCurriculumForm();
        $frm->fill(['course_id' => $courseId]);
        $this->set('frm', $frm);
        $this->set('courseId', $courseId);
        $this->_template->render(false, false);
    }


      /**
     * Render Intended Learners Page
     *
     * @param int $courseId
     */
    public function intendedLearnersForm(int $courseId)
    {
       
        $post = FatApp::getPostedData();
         
        $course_id = $courseId;
      
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
       

        $frm = $this->getIntendedLearnersForm();
        $frm->fill(['course_id' => $courseId]);

 
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm1 = QuestionSearch::getSearchForm($userType);
        if (!$post = $frm1->getFormDataFromArray($posts)) {
           FatUtility::dieJsonError(current($frm1->getValidationErrors()));
       }
       
       
        $srch = new QuestionSearch($langId, $userId, $userType);
         $srch->joinTable('tbl_quiz_questions', 'INNER JOIN', 'quizquestion.question_id = question .question_id', 'quizquestion');
        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'subcate.cate_id = question  .question_subcat', 'subcate');
        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'cate.cate_id = question  .question_cat', 'cate');
 
            $srch->addMultipleFields([
            '*',           // Select all fields from questions table
             'cate.cate_identifier as  catname',
           'subcate.cate_identifier as subcatname'
            ]);
  
            if (isset($userId) && !empty($userId)) {
            $srch->addCondition('quizquestion.quiz_id', '=', $courseId);
            }

         $srch->addCondition('question_status', '=', 1);
         $srch->setPageSize($post['pagesize']);
         $srch->setPageNumber($post['pageno']);
         $query = $srch->getQuery(); 
 
         $rows = $srch->fetchAndFormat();
           
         $frm1 = $this->getPriceForm();
        

        $myDate = new MyDate();
        $myDate->setMonthAndWeekNames();
    
 
        $this->sets([
            
            'frm' => $frm,
            'post' => $post,
            'courseId' => $courseId,
            'recordCount' => $srch->recordCount(),
            'planType' => Plan::PLAN_TYPE_CLASSES,
            'allClasses' => $srch->groupDates($rows),
        ]);
        $this->_template->render(false, false);



    }
    /**
     * Render Settings Page
     *
     * @param int $courseId
     */
    public function settingsForm(int $courseId)
    {
      
         
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
      
        /* validate course id */
        if (!$courseData = Quizzes::getAttributesById($courseId, ['quiz_id', 'quiz_title','quiz_pass_percentage','quiz_duration','quiz_validity'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        
         
        $course = new Quizzes($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        // if (!$course->canEditCourse()) {
        //     FatUtility::dieJsonError($course->getError());
        // }
        /* create form data */
        $frm = $this->getSettingForm();
       // echo '<pre>';print_r($frm);die;
      
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm1 = QuestionSearch::getSearchForm($userType);
        if (!$post = $frm1->getFormDataFromArray($posts)) {
           FatUtility::dieJsonError(current($frm1->getValidationErrors()));
       }
        
        $data = [
            'quiz_id' => $courseId,
            'quiz_pass_percentage' => $courseData['quiz_pass_percentage'],
            'quiz_duration' => $courseData['quiz_duration'],
           // 'course_price' => $courseData['quiz_validity']
        ];






        if ($courseId > 0) {
            $srch = new QuizSearch($this->siteLangId, $this->siteUserId, User::TEACHER);
           // $srch->applyPrimaryConditions();
            $srch->addMultipleFields([
                'quiz_pass_percentage',
                'quiz_duration',
                'quiz_validity',
                'quiz_pass_percentage',
                'quiz_fail_message',
                'quiz_pass_message',
                'quiz_offer_certificate',
                'quiz_id',
             ]);
            $srch->addCondition('quiz.quiz_id', '=', $courseId);
            $srch->setPageSize(1);

            if (!$course = FatApp::getDb()->fetch($srch->getResultSet())) {
                FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
            }
           
            
        }
         
        $frm = $this->getSettingForm();
        $frm->fill(['course_id' => $courseId]);
 
        $frm->fill($course);
        /* get form data from lang table */
 
       
        $this->sets([
            
            'frm' => $frm,
            'post' => $post,
            'courseId' => $courseId,
            'recordCount' => $srch->recordCount(),
            'planType' => Plan::PLAN_TYPE_CLASSES,
          
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Course Settings Data
     *
     * @return json
     */
    public function setupSettings()
    {
      
        $frm = $this->getSettingForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        
        $post['quiz_id']=$post['course_id'];
        unset($post['course_id']);
        $course = new Quizzes($post['quiz_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->setupSettings($post)) {
            FatUtility::dieJsonError($course->getError());
        }
      
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETTING_UPDATED_SUCCESSFUL'));
    }

    /**
     * function to delete course
     *
     * @param int $courseId
     * @return json
     */
    public function remove(int $courseId)
    {
        $courseId = FatUtility::int($courseId);
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->delete()) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Function to get eligibility status for all course steps.
     *
     * @param int $courseId
     * @return json
     */
    public function getEligibilityStatus(int $courseId)
    {
        $course = new Quizzes($courseId, $this->siteUserId, $this->siteUserType);
        $criteria = $course->isEligibleForApproval();
        FatUtility::dieJsonSuccess(['criteria' => $criteria]);
    }

    /**
     * Submitting course for approval from admin
     *
     * @param int $courseId
     * @return bool
     */
    public function submitForApproval(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->submitApprovalRequest()) {
            FatUtility::dieJsonError($course->getError());
        }
        Message::addMessage(Label::getLabel('LBL_APPROVAL_REQUESTED_SUCCESSFULLY'));
        FatUtility::dieJsonSuccess('');
    }

    /**
     * Add/Remove Course from user favorites list
     *
     * @return json
     */
    public function toggleFavorite()
    {
        $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $db = FatApp::getDb();
        /* validate course id */
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$data = $course->get()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        if ($data['course_user_id'] == $this->siteUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_MARK_YOUR_OWN_COURSE_AS_FAVORITE'));
        }
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, AppConstant::NO);
        if ($status == AppConstant::NO) {
            /* check course already marked favorite */
            $srch = new SearchBase(User::DB_TBL_COURSE_FAVORITE);
            $srch->addCondition('ufc_user_id', '=', $this->siteUserId);
            $srch->addCondition('ufc_course_id', '=', $courseId);
            $srch->doNotCalculateRecords();
            $srch->setPageSize(1);
            if (FatApp::getDb()->fetch($srch->getResultSet())) {
                FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_IS_ALREADY_IN_YOUR_FAVORITES_LIST'));
            }
            /* add to favorites */
            $user = new User($this->siteUserId);
            if (!$user->setupFavoriteCourse($courseId)) {
                FatUtility::dieJsonError($user->getError());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_COURSE_ADDED_TO_FAVORITES'));
        }
        /* remove from favorites */
        $where = [
            'smt' => 'ufc_user_id = ? AND ufc_course_id = ?',
            'vals' => [$this->siteUserId, $courseId]
        ];
        if (!$db->deleteRecords(User::DB_TBL_COURSE_FAVORITE, $where)) {
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_COURSE_REMOVED_FROM_FAVORITES'));
    }

    /**
     * Render cancellation popup
     */
    public function cancelForm()
    {
        $ordcrsId = FatApp::getPostedData('ordcrs_id', FatUtility::VAR_INT, 0);
         

        $db = FatApp::getDb();

        $updateData = [
            'quiz_delete' => 1
            
        ];
    
        if (!$db->updateFromArray('tbl_quizzes', $updateData, ['smt' => 'quiz_id = ?', 'vals' => [$ordcrsId]])) {
            $this->error = $db->getError();
             
        }
    
        return true;
        // $order = new Quizzes($ordcrsId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        // if (!$order->getCourseToCancel()) {
        //     FatUtility::dieJsonError($order->getError());
        // }
        // $frm = $this->getCancelForm();
        // $frm->fill(['ordcrs_id' => $ordcrsId]);
        // $this->sets(['frm' => $frm]);
        // $this->_template->render(false, false);
    }

    /**
     * Setup cancellation request
     *
     * @return bool
     */
    public function cancelSetup()
    {
        $frm = $this->getCancelForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $order = new OrderCourse($post['ordcrs_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$order->cancel($post['comment'])) {
            FatUtility::dieJsonError($order->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CANCELLATION_REQUEST_SUBMITTED_SUCCESSFULLY'));
    }

    /**
     * Get Cancel Form
     *
     */
    private function getCancelForm(): Form
    {
        $frm = new Form('cancelFrm');
        $comment = $frm->addTextArea(Label::getLabel('LBL_COMMENTS'), 'comment');
        $comment->requirements()->setLength(10, 300);
        $comment->requirements()->setRequired();
        $frm->addHiddenField('', 'ordcrs_id')->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }

    /**
     * Get Search Form
     *
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword');
        if ($this->siteUserType == User::TEACHER) {
            $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'course_status', Course::getStatuses(), '', [], Label::getLabel('LBL_SELECT'));
        } else {
            $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'crspro_status', OrderCourse::getStatuses(), '', [], Label::getLabel('LBL_SELECT'));
        }
        $frm->addSelectBox(Label::getLabel('LBL_TYPE'), 'course_type', Course::getTypes(), '', [], Label::getLabel('LBL_SELECT'));
        $categoryList = Category::getCategoriesByParentId($this->siteLangId);
        $frm->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'course_cateid', $categoryList, '', [], Label::getLabel('LBL_SELECT'));
        $frm->addSelectBox(Label::getLabel('LBL_SUB_CATEGORY'), 'course_subcateid', [], '', [], Label::getLabel('LBL_SELECT'));
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setInt();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEARCH'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_RESET'));
        return $frm;
    }

    /**
     * Basic Details Form
     *
     */
    private function getGeneralForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addTextBox(Label::getLabel('LBL_TITLE'), 'quiz_title')->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_COURSE_SUBTITLE'), 'course_subtitle')->requirements()->setRequired();
        $categories = Category::getCategoriesByParentId($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'course_cate_id', $categories, '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
        $fld = $frm->addSelectBox(Label::getLabel('LBL_SUBCATEGORY'), 'course_subcate_id', [], '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setInt();
        $langsList = (new CourseLanguage())->getAllLangs($this->siteLangId, true);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_TEACHING_LANGUAGE'), 'course_clang_id', $langsList, '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
        $fld = $frm->addSelectBox(Label::getLabel('LBL_LEVEL'), 'course_level', Course::getCourseLevels(), '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
        $frm->addHtmlEditor(Label::getLabel('LBL_DESCRIPTION'), 'quiz_description')->requirements()->setRequired();
        $frm->addHiddenField('', 'quiz_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Course Media Form
     *
     */
    private function getMediaForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addFileUpload(Label::getLabel('LBl_COURSE_IMAGE'), 'course_image');
        $frm->addFileUpload(Label::getLabel('LBl_PREVIEW_VIDEO'), 'course_preview_video');
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Intended Learners Form
     *
     */
    private function getIntendedLearnersForm(): Form
    {
        $frm = new Form('frmCourses');
        // $frm->addTextBox('', 'type_learnings[]')->requirements()->setRequired();
        // $frm->addTextBox('', 'type_requirements[]')->requirements()->setRequired();
        // $frm->addTextBox('', 'type_learners[]')->requirements()->setRequired();
        // $frm->addHiddenField('', 'type_learnings_ids[]');
        // $frm->addHiddenField('', 'type_requirements_ids[]');
        // $frm->addHiddenField('', 'type_learners_ids[]');
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Prices Form
     *
     */
    private function getPriceForm(): Form
    {
        $frm = new Form('frmCourses');
        $fld = $frm->addRadioButtons(Label::getLabel('LBL_TYPE'), 'course_type', Course::getTypes());
        $fld->requirements()->setRequired();
        $frm->addSelectBox(
            Label::getLabel('LBL_CURRENCY'),
            'course_currency_id',
            Currency::getCurrencyNameWithCode($this->siteLangId), 
            '', 
            [], 
            Label::getLabel('LBL_SELECT')
        );
        $frm->addTextBox(Label::getLabel('LBL_PRICE'), 'course_price')->requirements()->setFloat();
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Curriculum Form
     *
     */
    private function getCurriculumForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Setting Form
     *
     * @param bool $offerCetificate
     */
    private function getSettingForm(bool $offerCetificate = true): Form
    {
        $frm = new Form('frmCourses');
        if ($offerCetificate == true) {
            $fld = $frm->addRadioButtons(Label::getLabel('LBL_OFFER_CERTIFICATE'), 'quiz_offer_certificate', AppConstant::getYesNoArr(), AppConstant::NO);
            $fld->requirements()->setRequired();
        } else {
            $frm->addHiddenField('', 'quiz_offer_certificate', AppConstant::NO);
        }

        
          $frm->addTextArea(Label::getLabel('LBL_FAIL_MESSAGE'), 'quiz_fail_message')->requirements()->setRequired();
        $fld = $frm->addTextArea(Label::getLabel('LBL_PASS_MESSAGE'), 'quiz_pass_message');
        $fld->requirements()->setRequired();  
        
        $fld = $frm->addIntegerField(Label::getLabel('LBL_DURATIONS'), 'quiz_duration', '', ['id' => 'course_price']);
        $fld->requirements()->setRequired(true);
        $fld = $frm->addIntegerField(Label::getLabel('LBL_PASS_PERCENTAGE'), 'quiz_pass_percentage', '', ['id' => 'course_pass']);
        $fld->requirements()->setRequired(true);
 
        $fld = $frm->addIntegerField(Label::getLabel('LBL_VALIDITY'), 'quiz_validity', '', ['id' => 'course_validity']);
        $fld->requirements()->setRequired(true);
 
        
        //$frm->addTextBox(Label::getLabel('LBL_PASS'), 'course_tags')->requirements()->setRequired();
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_save', Label::getLabel('LBL_SAVE'));
        $frm->addButton('', 'btn_approval', Label::getLabel('LBL_SUBMIT_FOR_APPROVAL'));
        return $frm;
    }
}
