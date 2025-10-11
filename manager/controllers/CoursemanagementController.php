<?php

/**
 * Courses Controller is used for course handling
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CoursemanagementController extends AdminBaseController
{

    /**
     * Initialize Courses
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCourses();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $cateId = FatApp::getQueryStringData('course_cateid') ?? 0;
        $frm = $this->getSearchForm($cateId);
        $frm->fill(FatApp::getQueryStringData());
        $this->set('srchFrm', $frm);
        $this->set('params', FatApp::getQueryStringData());
        $this->_template->render();
    }

    /**
     * Search & List
     */
    public function search()
    {
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_subcateid']);
        $srch = new CourseManagement($this->siteLangId, 0, User::SUPPORT);

        $srch->addMultipleFields([
            '*',
            'question.id as qid',
            'course_examboards.name as examboard_name',
            'course_year.name as year_name',
            'course_type.name as type_name',
            'course_tier.name as tier_name',
        ]);
        $srch->addCondition('question.deleted', '=', 0);
        $srch->applySearchConditionsadmin($post);
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['page']);

        // $srch->addOrder('question.id', 'DESC');
        $orders = $srch->fetchAndFormat();


        $this->sets([
            'arrListing' => $orders,
            'page' => $post['page'],
            'post' => $post,
            'pageSize' => $post['pagesize'],
            'pageCount' => $srch->pages(),
            'recordCount' => $srch->recordCount(),
            'canEdit' => $this->objPrivilege->canEditCourses(true),
        ]);
        $this->_template->render(false, false);
    }

    public function form(int $categoryId)
    {
        $this->objPrivilege->canEditCategories();
        $categoryId = FatUtility::int($categoryId);
        $data = [];
        if ($categoryId > 0) {
            $category = new Category($categoryId);
            $data = $category->getDataById();
            if (count($data) < 1) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
        }
        $frm = $this->getForm($categoryId);
        $frm->fill($data);
        $this->sets([
            'frm' => $frm,
            'data' => $data,
            'categoryId' => $categoryId,
            'languages' => Language::getAllNames(),
        ]);
        $this->_template->render(false, false);
    }


    private function getForm(): Form
    {
        $frm = new Form('frmCourse');
        $examboards = $this->getExamboardsFromDB();
        $examboards = [
            '' => 'Select Exam Board',
            'add_new' => '➕ Add New'
        ] + $examboards;

        // Add select box for Exam Board
        $fldExamBoard = $frm->addSelectBox(Label::getLabel('LBL_EXAM_BOARD'), 'examboard', $examboards, '', [], '');
        $fldExamBoard->setFieldTagAttribute('id', 'examboard');
       $fldExamBoard->setFieldTagAttribute('class', 'examboard-select form-control');

        $fldExamBoard->setFieldTagAttribute('onchange', 'handleExamBoardChange(this.value)');
        $fldExamBoard->requirements()->setRequired(false);
        // Add a hidden input field for adding a new exam board
        $fldNewExamBoard = $frm->addTextBox('', 'new_examboard', '');
        $fldNewExamBoard->setFieldTagAttribute('id', 'new_examboard');
        $fldNewExamBoard->setFieldTagAttribute('style', 'display:none;');
        $fldNewExamBoard->requirements()->setRequired(false);

        // Tier options
        $tiers = $this->getTierFromDB();
        $tiers = [
            '' => 'Select Tier',
            'add_new' => '➕ Add New'
        ] + $tiers;

        // Add select box for Tier
        $fldTier = $frm->addSelectBox(Label::getLabel('LBL_TIER'), 'tier', $tiers, '', [], '');
        $fldTier->setFieldTagAttribute('id', 'tier');
        $fldTier->setFieldTagAttribute('class', 'tier-select form-control');
        $fldTier->setFieldTagAttribute('onchange', 'handleTierChange(this.value)');
        $fldTier->requirements()->setRequired(false);
        // Hidden input for adding a new Tier
        $fldNewTier = $frm->addTextBox('', 'new_tier', '');
        $fldNewTier->setFieldTagAttribute('id', 'new_tier');
        $fldNewTier->setFieldTagAttribute('style', 'display:none;');
        $fldNewTier->requirements()->setRequired(false);


        // Type options
        $types = $this->getTypeFromDB();
        $types = [
            '' => 'Select Type',
            'add_new' => '➕ Add New'
        ] + $types;

        // Add select box for Type
        $fldType = $frm->addSelectBox(Label::getLabel('LBL_TYPE'), 'type', $types, '', [], '');
        $fldType->setFieldTagAttribute('id', 'type');
        $fldType->setFieldTagAttribute('onchange', 'handleTypeChange(this.value)');
        $fldType->requirements()->setRequired();
        // Hidden input for adding a new Type
        $fldNewType = $frm->addTextBox('', 'new_type', '');
        $fldNewType->setFieldTagAttribute('id', 'new_type');
        $fldNewType->setFieldTagAttribute('style', 'display:none;');
        $fldNewType->requirements()->setRequired(false);


        // Year options
        $years = $this->getYearsFromDB();
        $years = [
            '' => 'Select Year',
            'add_new' => '➕ Add New'
        ] + $years;

        // Add select box for Year
        $fldYear = $frm->addSelectBox(Label::getLabel('LBL_YEAR'), 'year_id', $years, '', [], '');
        $fldYear->setFieldTagAttribute('id', 'year');
        $fldYear->setFieldTagAttribute('onchange', 'handleYearChange(this.value)');
        $fldYear->requirements()->setRequired(false);
        // Hidden input for adding a new Year
        $fldNewYear = $frm->addTextBox('', 'new_year', '');
        $fldNewYear->setFieldTagAttribute('id', 'new_year');
        $fldNewYear->setFieldTagAttribute('style', 'display:none;');
        $fldNewYear->requirements()->setRequired(false);




        //level field for form
        $levels = $this->getLevelsFromDB();
        $levels = [
            '' => 'Select Level', // Empty value for the placeholder
            'add_new' => '➕ Add New'
        ] + $levels;

        $fld = $frm->addSelectBox(Label::getLabel('LBL_LEVEL'), 'level', $levels, '', [], '');
        $fld->setFieldTagAttribute('id', 'level');
        $fld->setFieldTagAttribute('onchange', 'handleLevelChange(this.value)');
        $fld->requirements()->setRequired();

        // Add a hidden input field for adding a new level
        $fldNewLevel = $frm->addTextBox('', 'new_level', '');
        $fldNewLevel->setFieldTagAttribute('id', 'new_level');
        $fldNewLevel->setFieldTagAttribute('style', 'display:none;');
        $fldNewLevel->requirements()->setRequired(false);


        // $subjects = $this->getSubjectsFromDB(); // Assuming a method to fetch subjects
        $subjects = [];
        $subjects = [
            '' => 'Select Subject', // Empty value for the placeholder
            'add_new' => '➕ Add New'
        ] + $subjects;

        $fld = $frm->addSelectBox(Label::getLabel('LBL_SUBJECT'), 'subject', $subjects, '', [], '');
        $fld->setFieldTagAttribute('id', 'subject'); // Ensure ID is set
        $fld->setFieldTagAttribute('onchange', 'handleSubjectChange(this.value)');
        $fld->requirements()->setRequired();

        // Add a hidden input field for adding a new subject
        $fldNewSubject = $frm->addTextBox('', 'new_subject', '');
        $fldNewSubject->setFieldTagAttribute('id', 'new_subject');
        $fldNewSubject->setFieldTagAttribute('style', 'display:none;');
        $fldNewSubject->requirements()->setRequired(false);

        // Topic Selection (Initially empty, dynamically loaded)
        $topics = $this->getTopicsFromDB();

        // safety check agar null/false mila
        if (!is_array($topics)) {
            $topics = [];
        }

        $topics = [
            ''        => 'Select Topics',
            'add_new' => '➕ Add New'
        ] + $topics;

        $fld = $frm->addSelectBox(Label::getLabel('LBL_TOPIC'), 'topic', $topics, '', [], '');
        $fld->setFieldTagAttribute('id', 'topic');
        $fld->setFieldTagAttribute('onchange', 'handleTopicChange(this.value)');
        $fld->requirements()->setRequired();

        // Hidden input for new Topic
        $fldNewTopic = $frm->addTextBox('', 'new_topic', '');
        $fldNewTopic->setFieldTagAttribute('id', 'new_topic');
        $fldNewTopic->setFieldTagAttribute('style', 'display:none;');



        // **Dynamic Sub-Topic Selection**
        $frm->addHtml('', 'subtopic_container', '<div id="subtopic-container"></div>');

        // "Add More" Button
        $frm->addButton('', 'btn_add_subtopic', Label::getLabel('LBL_ADD_SUBTOPIC'))
            ->setFieldTagAttribute('onclick', 'addSubTopicFields();');
        // Submit Button
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SUBMIT'));

        return $frm;
    }


    public function getsubtopicsbytopic()
    {
        // Assuming the topic ID is passed via POST
        $topicId = FatApp::getPostedData('topicId', FatUtility::VAR_INT);

        // Fetch sub-topics for the given topic
        $subTopics = $this->getSubTopicsByTopicId($topicId);

        // Respond with sub-topics
        echo json_encode([
            'status' => 1,
            'data' => $subTopics
        ]);
        exit;
    }

    private function getSubTopicsByTopicId($topicId)
    {

        $db = FatApp::getDb();
        $query = "SELECT id, topic FROM course_topics WHERE parent_id = $topicId"; // Direct insertion
        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        // Fetch all results
        $subtopics = $db->fetchAll($result);

        // If no records found, return an empty array
        if (empty($subtopics)) {
            return [];
        }

        // Return the subjects as an associative array
        return array_column($subtopics, 'topic', 'id');
    }

    public function deleteExamBoard()
    {
        $examboardId = FatApp::getPostedData('examboard_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Exam_Board_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_examboards WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete_Exam_Board'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Exam_Board_Deleted_Successfully'));
    }

        public function deleteTier()
    {
        $examboardId = FatApp::getPostedData('tier_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_TIer_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_tier WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete_Tier'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Tier_Deleted_Successfully'));
    }

          public function deleteType()
    {
        $examboardId = FatApp::getPostedData('type_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Type_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_type WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Type_Deleted_Successfully'));
    }

          public function deleteYear()
    {
        $examboardId = FatApp::getPostedData('year_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Year_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_year WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Year_Deleted_Successfully'));
    }

         public function deleteLevel()
    {
        $examboardId = FatApp::getPostedData('level_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Level_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_levels WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Level_Deleted_Successfully'));
    }

         public function deleteSubject()
    {
        $examboardId = FatApp::getPostedData('subject_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Level_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_subjects WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Subject_Deleted_Successfully'));
    }

        public function deleteTopic()
    {
        $examboardId = FatApp::getPostedData('topic_id', FatUtility::VAR_INT, 0);
        if ($examboardId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Topic_ID'));
        }
        $db = FatApp::getDb();
        $sql = 'DELETE FROM course_topics WHERE id = ' . $db->quoteVariable($examboardId);

        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Could_Not_Delete'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Topic_Deleted_Successfully'));
    }

    private function getLevelsFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, level_name FROM course_levels"; // Replace with actual table name
        $result = $db->query($query);
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }
        $levels = $db->fetchAll($result);
        if (empty($levels)) {
            return []; // Return an empty array if no records found
        }

        return array_column($levels, 'level_name', 'id');
    }

    private function getExamboardsFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, name FROM course_examboards ORDER BY name ASC";
        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $examBoards = $db->fetchAll($result);
        return empty($examBoards) ? [] : array_column($examBoards, 'name', 'id');
    }

    private function getTierFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, name FROM course_tier ORDER BY name ASC";
        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $tiers = $db->fetchAll($result);
        return empty($tiers) ? [] : array_column($tiers, 'name', 'id');
    }

    private function getTypeFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, name FROM course_type"; // Replace with actual table name
        $result = $db->query($query);
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }
        $levels = $db->fetchAll($result);
        if (empty($levels)) {
            return []; // Return an empty array if no records found
        }

        return array_column($levels, 'name', 'id');
    }

    private function getYearsFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, name FROM course_year"; // Replace with actual table name
        $result = $db->query($query);
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }
        $levels = $db->fetchAll($result);
        if (empty($levels)) {
            return []; // Return an empty array if no records found
        }

        return array_column($levels, 'name', 'id');
    }



    public function gettopicforsubject()
{
    $subjectId = FatApp::getPostedData('subjectId', FatUtility::VAR_INT, 0);
    $levelId = FatApp::getPostedData('levelId', FatUtility::VAR_INT, 0);
    $examboardId = FatApp::getPostedData('examboardId', FatUtility::VAR_INT, 0);
    $yearId = FatApp::getPostedData('yearId', FatUtility::VAR_INT, 0);

    if ($subjectId <= 0) {
        echo json_encode(['status' => 0, 'msg' => 'Invalid subjectId']);
        return;
    }

    $topics = $this->getTopicBySubjectId($subjectId, $levelId, $examboardId, $yearId);

    if (empty($topics)) {
        echo json_encode(['status' => 0, 'msg' => 'No topics found']);
        return;
    }

    echo json_encode(['status' => 1, 'data' => $topics]);
}


   private function getTopicBySubjectId($subjectId, $levelId = 0, $examboardId = 0, $yearId = 0)
{
    $db = FatApp::getDb();

    if ($subjectId <= 0) {
        return [];
    }

    $params = [$subjectId];
    $sql = "SELECT id, topic 
            FROM course_topics 
            WHERE subject_id = ?";

    if ($levelId > 0) {
        if ($this->isGcseLevel($levelId)) {
            // ✅ GCSE case → examboard_id required
            if ($examboardId > 0) {
                $sql .= " AND examboard_id = ?";
                $params[] = $examboardId;
            } else {
                // agar examboardId missing hai to koi data na do
                return [];
            }
        } else {
            // ✅ Non-GCSE case → year_id required
            if ($yearId > 0) {
                $sql .= " AND year_id = ?";
                $params[] = $yearId;
            } else {
                return [];
            }
        }
    }

    $sql .= " ORDER BY topic ASC";
    $topics = $db->fetchAll($sql, $params);

    if (empty($topics)) {
        return [];
    }

    return array_column($topics, 'topic', 'id');
}



    public function getsubjectsforlevel()
    {
        // Assuming the 'level_id' is passed in the request
        $levelId = FatApp::getPostedData('levelId', FatUtility::VAR_INT, 0);

        // Check if level ID is valid
        if ($levelId <= 0) {
            echo json_encode(['status' => 0 , 'msg' => 'Invalid level ID']);
            return;
        }

        // Fetch subjects for the given level from the database
        $subjects = $this->getSubjectsByLevel($levelId); // Your method to get subjects by level

        if (empty($subjects)) {
            echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
            return;
        }

        // Add the "Add New" option
        $subjects['add_new'] = ['id' => 'add_new', 'name' => '➕ Add New'];

        // Return subjects as JSON response
        echo json_encode(['status' => 1, 'data' => $subjects]);
    }

    private function getSubjectsByLevel($levelId)
    {
        // Get the database instance
        $db = FatApp::getDb();

        // Directly insert the levelId into the query (ensure proper escaping to prevent SQL injection)
        $query = "SELECT id, subject FROM course_subjects WHERE level_id = $levelId"; // Direct insertion

        // Execute the query
        $result = $db->query($query);

        // Check if the query was successful
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        // Fetch all results
        $subjects = $db->fetchAll($result);

        // If no records found, return an empty array
        if (empty($subjects)) {
            return [];
        }

        // Return the subjects as an associative array
        return array_column($subjects, 'subject', 'id');
    }


    public function getexamboardforsubject()
    {
        // Assuming the 'level_id' is passed in the request
        $subjectId = FatApp::getPostedData('subjectId', FatUtility::VAR_INT, 0);

        // Check if level ID is valid
        if ($subjectId <= 0) {
            echo json_encode(['status' => 0 , 'msg' => 'Invalid level ID']);
            return;
        }

        // Fetch subjects for the given level from the database
        $examboard = $this->getExamboardBySubject($subjectId); // Your method to get subjects by level

        if (empty($examboard)) {
            echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
            return;
        }

        // Add the "Add New" option
        $examboard['add_new'] = ['id' => 'add_new', 'name' => '➕ Add New'];

        // Return subjects as JSON response
        echo json_encode(['status' => 1, 'data' => $examboard]);
    }

    private function getExamboardBySubject($subjectId)
    {
        // Get the database instance
        $db = FatApp::getDb();

        // Directly insert the levelId into the query (ensure proper escaping to prevent SQL injection)
        $query = "SELECT id, name FROM course_examboards WHERE 	subject_id = $subjectId"; // Direct insertion

        // Execute the query
        $result = $db->query($query);

        // Check if the query was successful
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        // Fetch all results
        $examboard = $db->fetchAll($result);

        // If no records found, return an empty array
        if (empty($examboard)) {
            return [];
        }

        // Return the subjects as an associative array
        return array_column($examboard, 'name', 'id');
    }

    public function getTierforExamboard()
    {
        // Assuming the 'level_id' is passed in the request
        $examboardId = FatApp::getPostedData('examboardId', FatUtility::VAR_INT, 0);

        // Check if level ID is valid
        if ($examboardId <= 0) {
            echo json_encode(['status' => 0 , 'msg' => 'Invalid level ID']);
            return;
        }

        // Fetch subjects for the given level from the database
        $tier = $this->getTierByExamboard($examboardId); // Your method to get subjects by level

        if (empty($tier)) {
            echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
            return;
        }

        // Add the "Add New" option
        $tier['add_new'] = ['id' => 'add_new', 'name' => '➕ Add New'];

        // Return subjects as JSON response
        echo json_encode(['status' => 1, 'data' => $tier]);
    }

    private function getTierByExamboard($examboardId)
    {
        // Get the database instance
        $db = FatApp::getDb();

        // Directly insert the levelId into the query (ensure proper escaping to prevent SQL injection)
        $query = "SELECT id, name FROM course_tier WHERE 	examboard_id = $examboardId"; // Direct insertion

        // Execute the query
        $result = $db->query($query);

        // Check if the query was successful
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        // Fetch all results
        $tier = $db->fetchAll($result);

        // If no records found, return an empty array
        if (empty($tier)) {
            return [];
        }

        // Return the subjects as an associative array
        return array_column($tier, 'name', 'id');
    }


    private function getSubjectsFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, subject FROM course_subjects ORDER BY subject ASC";
        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $subjects = $db->fetchAll($result);
        return empty($subjects) ? [] : array_column($subjects, 'subject', 'id');
    }

    private function getTopicsFromDB()
    {
        $db = FatApp::getDb();

        $query = "SELECT id, topic FROM course_topics ORDER BY topic ASC";
        $result = $db->query($query);

        if (!$result) {
            return [];
        }

        $rows = $db->fetchAll($result);

        return empty($rows) ? [] : array_column($rows, 'topic', 'id');
    }



    private function isGcseLevel(int $levelId): bool
    {
        if ($levelId <= 0) {
            return false;
        }
        $db = FatApp::getDb();
        $row = $db->fetch("SELECT level_name FROM course_levels WHERE id = ?", [$levelId]);
        if (!$row) {
            return false;
        }
        return strcasecmp(trim($row['level_name']), 'gcse') === 0;
    }


public function uploadQuestionBank()
{
    $post = FatApp::getPostedData();
    $subtopicId = FatUtility::int($post['subtopic_id'] ?? 0);
    $courseId = FatUtility::int($post['course_id'] ?? 0);

    if ($subtopicId <= 0) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_SUBTOPIC_OR_COURSE'));
    }

    if (!isset($_FILES['question_csv']) || $_FILES['question_csv']['error'] !== UPLOAD_ERR_OK) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_OR_MISSING_CSV_FILE'));
    }

    $file = $_FILES['question_csv'];
    $allowedMimeTypes = ['text/csv', 'application/vnd.ms-excel', 'text/plain'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (!in_array($fileMimeType, $allowedMimeTypes) || strtolower($fileExtension) !== 'csv') {
        FatUtility::dieJsonError(Label::getLabel('LBL_ONLY_CSV_FILES_ARE_ALLOWED'));
    }

    $csvData = file_get_contents($file['tmp_name']);
    $lines = array_map('str_getcsv', explode(PHP_EOL, $csvData));
    $lines = array_filter($lines); // Remove empty rows

    if (count($lines) < 2) {
        FatUtility::dieJsonError(Label::getLabel('LBL_CSV_FILE_HAS_NO_VALID_DATA'));
    }

    $db = FatApp::getDb();
    $db->startTransaction();

    // Delete old questions for this subtopic
    $sql = "DELETE FROM tbl_quaestion_bank WHERE subtopic_id = $subtopicId";
    if (!$db->query($sql)) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError(Label::getLabel('LBL_FAILED_TO_CLEAR_OLD_QUESTIONS'));
    }

    $header = $lines[0];
    unset($lines[0]); // Remove header row

    $insertedCount = 0;

    foreach ($lines as $line) {
        // Pad to at least 14 fields to prevent undefined offset errors
        $line = array_pad($line, 14, '');

        [
            $question_text,
            $answer_a,
            $answer_b,
            $answer_c,
            $answer_d,
            $correct_answer,
            $difficulty,
            $examboardName,
            $topic,
            $subtopicName,
            $levelName,
            $question_type,
            $tier,
            $hint
        ] = $line;

        if (empty($question_text) || empty($correct_answer)) {
            continue; // Skip incomplete rows
        }

        // Resolve Examboard ID from course_examboards table
        $examboardRow = $db->fetch("SELECT id FROM course_examboards WHERE name = ?", [$examboardName]);
        $examboardId = $examboardRow ? $examboardRow['id'] : 0;

        // Resolve Level ID from tbl_courses (or wherever levels are stored)
        $levelRow = $db->fetch("SELECT id FROM tbl_courses WHERE name = ?", [$levelName]);
        $levelId = $levelRow ? $levelRow['id'] : 0;

        $questionData = [
            'question_title'    => trim($question_text),
            'answer_a'          => trim($answer_a),
            'answer_b'          => trim($answer_b),
            'answer_c'          => trim($answer_c),
            'answer_d'          => trim($answer_d),
            'correct_answer'    => trim($correct_answer),
            'difficult_level'   => trim($difficulty),
            'examboard_id'      => $examboardId,
            'subtopic_id'       => $subtopicId,
            'course_id'         => $courseId,
            'level_id'          => $levelId,
            'topic'             => trim($topic),
            'subtopic'          => trim($subtopicName),
            'tier'              => trim($tier),
            'hint'              => trim($hint),
            'question_type'     => trim($question_type),
             'category'          => '',                  // <-- add this line
            'subcategory'       => '', // optional
            'question_added_on' => date('Y-m-d H:i:s'),
        ];

        if (!$db->insertFromArray('tbl_quaestion_bank', $questionData)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError('❌ Error inserting question: ' . $db->getError());
        }

        $insertedCount++;
    }

    $db->commitTransaction();

    FatUtility::dieJsonSuccess([
        'msg' => "✅ {$insertedCount} questions uploaded successfully!",
        'status' => '1'
    ]);
}


    public function setup()
    {
        $this->objPrivilege->canEditCategories();

    $post = FatApp::getPostedData();
    $db   = FatApp::getDb();

    // 🔴 Validation
    if (empty($post['level']) || empty($post['subject'])) {
        FatUtility::dieJsonError('❌ Level and Subject are required.');
    }

    // ✅ Handle Level
    if ($post['level'] == 'add_new') {
        if (empty($post['new_level'])) {
            FatUtility::dieJsonError('❌ New Level is required.');
        }
        $levelRow = $db->fetch($db->query(
            'SELECT id FROM course_levels WHERE level_name = ' . $db->quoteVariable($post['new_level'])
        ));
        if (!$levelRow) {
            $db->insertFromArray('course_levels', [
                'level_name' => $post['new_level'],
                'created_at' => date('Y-m-d H:i:s')
            ]) or FatUtility::dieJsonError('❌ Failed to insert new level: ' . $db->getError());
            $levelId = $db->getInsertId();
        } else {
            $levelId = $levelRow['id'];
        }
    } else {
        $levelId = (int)$post['level'];
    }

    // ✅ Handle Subject
    if ($post['subject'] == 'add_new') {
        if (empty($post['new_subject'])) {
            FatUtility::dieJsonError('❌ New Subject is required.');
        }
        $db->insertFromArray('course_subjects', [
            'subject'   => $post['new_subject'],
            'level_id'  => $levelId,
            'created_at'=> date('Y-m-d H:i:s')
        ]) or FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
        $subjectId = $db->getInsertId();
    } else {
        $subjectId = (int)$post['subject'];
    }

    // ✅ Handle Examboard
    if ($post['examboard'] == 'add_new') {
        if (empty($post['new_examboard'])) {
            FatUtility::dieJsonError('❌ New Examboard is required.');
        }
        $db->insertFromArray('course_examboards', [
            'name'       => $post['new_examboard'],
            'subject_id' => $subjectId,
            'created_at' => date('Y-m-d H:i:s')
        ]) or FatUtility::dieJsonError('❌ Failed to insert new examboard: ' . $db->getError());
        $examboardId = $db->getInsertId();
    } else {
        $examboardId = (int)$post['examboard'];
    }

    // ✅ Handle Tier
    if ($post['tier'] == 'add_new') {
        if (empty($post['new_tier'])) {
            FatUtility::dieJsonError('❌ New Tier is required.');
        }
        $db->insertFromArray('course_tier', [
            'name'         => $post['new_tier'],
            'examboard_id' => $examboardId,
            'created_at'   => date('Y-m-d H:i:s')
        ]) or FatUtility::dieJsonError('❌ Failed to insert new tier: ' . $db->getError());
        $tierId = $db->getInsertId();
    } else {
        $tierId = (int)$post['tier'];
    }

    // ✅ Handle Type
    if ($post['type'] == 'add_new') {
        if (empty($post['new_type'])) {
            FatUtility::dieJsonError('❌ New Type is required.');
        }
        $db->insertFromArray('course_type', [
            'name'       => $post['new_type'],
            'created_at' => date('Y-m-d H:i:s')
        ]) or FatUtility::dieJsonError('❌ Failed to insert new type: ' . $db->getError());
        $typeId = $db->getInsertId();
    } else {
        $typeId = (int)$post['type'];
    }

    // ✅ Handle Year
    if ($post['year_id'] == 'add_new') {
        if (empty($post['new_year'])) {
            FatUtility::dieJsonError('❌ New Year is required.');
        }
        $db->insertFromArray('course_year', [
            'name'       => $post['new_year'],
            'subject_id' => $subjectId,
            'created_at' => date('Y-m-d H:i:s')
        ]) or FatUtility::dieJsonError('❌ Failed to insert new year: ' . $db->getError());
        $yearId = $db->getInsertId();
    } else {
        $yearId = (int)$post['year_id'];
    }

    // ✅ Handle Topic (fixed with examboard/year)
    if ($post['topic'] == 'add_new') {
        if (empty($post['new_topic'])) {
            FatUtility::dieJsonError('❌ New Topic is required.');
        }
        $db->insertFromArray('course_topics', [
            'topic'        => $post['new_topic'],
            'subject_id'   => $subjectId,
            'examboard_id' => $examboardId,
            'year_id'      => $yearId,
            'created_at'   => date('Y-m-d H:i:s')
        ]) or FatUtility::dieJsonError('❌ Failed to insert new topic: ' . $db->getError());
        $topicId = $db->getInsertId();
    } else {
        $topicId = (int)$post['topic'];
    }

    // ✅ Insert into main course management
    $insertData = [
        'topic'       => $topicId,
        'subject'     => $subjectId,
        'level'       => $levelId,
        'tier'        => $tierId,
        'year_id'     => $yearId,
        'type'        => $typeId,
        'examBoards'  => $examboardId,
        'created_on'  => date('Y-m-d H:i:s')
    ];
    if (!$db->insertFromArray('tbl_course_management', $insertData)) {
        FatUtility::dieJsonError('❌ Error inserting course: ' . $db->getError());
    }
    $courseId = $db->getInsertId();

        if (!empty($post['subtopics']) && is_array($post['subtopics'])) {
            foreach ($post['subtopics'] as $index =>  $subtopic) {


                if ($subtopic == 'add_new') {

                    $insertData = [
                        'topic' => $post['new_subtopics'][$index],
                        'subject_id' => 0,
                        'parent_id' => $topicId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    if (!$db->insertFromArray('course_topics', $insertData)) {
                        FatUtility::dieJsonError('❌ Failed to insert new subtopic: ' . $db->getError());
                    }
                    $subtopic = $db->getInsertId();
                }



                $pdfPath = '';
                if (!empty($_FILES['past_exams']['tmp_name'][$index]) && $_FILES['past_exams']['error'][$index] === UPLOAD_ERR_OK) {
                    $pdfFile = $_FILES['past_exams'];
                    $pdfExtension = pathinfo($pdfFile['name'][$index], PATHINFO_EXTENSION);
                    if (strtolower($pdfExtension) === 'pdf') {
                        $uploadDir = 'uploads/previous_papers/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $pdfPath = $uploadDir . uniqid('paper_') . '.' . $pdfExtension;
                        move_uploaded_file($pdfFile['tmp_name'][$index], $pdfPath);
                    } else {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError('❌ Invalid PDF format. Only .pdf files are allowed.');
                    }
                }

                $lessonData = [
                    'course_id'   => $courseId,
                    'subtopic'       => $subtopic,
                    'video_url' => isset($post['video_urls'][$index]) ? $post['video_urls'][$index] : '',
                    'previous_paper_pdf' => $pdfPath,
                    'created_at'  => date('Y-m-d H:i:s')
                ];
                if (!$db->insertFromArray('course_subtopics', $lessonData)) {
                    $db->rollbackTransaction();
                    FatUtility::dieJsonError('❌ Error inserting lesson: ' . $db->getError());
                }
                $subtopicId = $db->getInsertId();
                $subtopicId=$subtopic;


                // ✅ Handle CSV file for this subtopic
                if (!empty($_FILES['quiz_csvs']['tmp_name'][$index]) && $_FILES['quiz_csvs']['error'][$index] === UPLOAD_ERR_OK) {
                    $file = $_FILES['quiz_csvs']['tmp_name'][$index];
                    $csvData = array_map('str_getcsv', file($file));

                    if (!empty($csvData)) {
                        foreach ($csvData as $rowIndex => $row) {
                            if ($rowIndex === 0) continue; // Skip header row

                            if (count($row) < 14) continue; // Ensure valid row format

                            list(
                                $question_text,
                                $answer_a,
                                $answer_b,
                                $answer_c,
                                $answer_d,
                                $correct_answer,
                                $difficulty,
                                $examboardName,
                                $topic,
                                $subtopicName,
                                $levelName,
                                $question_type,
                                $tier,
                                $hint
                            ) = $row;

                            // Insert question into question bank
                

                                $questionData = [
                                    'question_title'    => trim($question_text),
                                    'answer_a'          => trim($answer_a),
                                    'answer_b'          => trim($answer_b),
                                    'answer_c'          => trim($answer_c),
                                    'answer_d'          => trim($answer_d),
                                    'correct_answer'    => trim($correct_answer),
                                    'difficult_level'   => trim($difficulty),
                                    'examboard_id'      => $examboardId,
                                    'level_id'          => $levelId,
                                    'subtopic_id'       => $subtopicId, // ✅ Link question to the inserted subtopic
                                    'course_id'         => $courseId,
                                    'topic'             => trim($topic),
                                    'subtopic'          => trim($subtopicName),
                                    'tier'              => trim($tier),
                                    'hint'              => trim($hint),
                                    'question_type'     => trim($question_type),
                                     'category'          => '',
                                    'subcategory'       => '', // optional
                                    'question_added_on' => date('Y-m-d H:i:s')
                                ];
                            if (!$db->insertFromArray('tbl_quaestion_bank', $questionData)) {
                                $db->rollbackTransaction();
                                FatUtility::dieJsonError('❌ Error inserting question: ' . $db->getError());
                            }
                        }
                    }
                }
            }
        }


        $db->commitTransaction();
    FatUtility::dieJsonSuccess(['msg' => '✅ Course added successfully!']);
    }




    /**
     * Render Course View
     *
     * @param int $courseId
     * return html
     */

    public function view(int $courseId)
{
    $course = '';
    $srch = new CourseManagement($this->siteLangId, 0, User::SUPPORT);

    $srch->addMultipleFields([
        '*',
        'question.id as qid',
        'course_examboards.name as examboard_name',
        'course_year.name as year_name',
        'course_type.name as type_name',
        'course_tier.name as tier_name',
    ]);

    $srch->addCondition('question.deleted', '=', 0);
    $srch->addCondition('question.id', '=', $courseId);

    $srch->setPageSize(1);
    $srch->setPageNumber(1);

    $result = $srch->fetchAndFormat();

    // Step 1: Fetch course materials
    $materialSrch = new SearchBase('course_subtopics'); 
    $materialSrch->addCondition('course_id', '=', $courseId);
    $materialSrch->addMultipleFields([
        'id',           // use as subtopic_id
        'subtopic',
        'video_url',
        'previous_paper_pdf',
        'course_id',
        'created_at'
    ]);

    $materialRs = $materialSrch->getResultSet();
    if (!$materialRs) {
        FatUtility::dieJsonError("Failed to fetch course materials.");
    }

    $courseMaterials = FatApp::getDb()->fetchAll($materialRs);
    $finalData = [];

    foreach ($courseMaterials as $material) {
        $subtopicId = $material['id']; // correct ID

        $questionSrch = new SearchBase('tbl_quaestion_bank');
        $questionSrch->addCondition('subtopic_id', '=', $subtopicId);

        // Optional: filter by examboard_id or level_id if needed
        if (isset($_GET['examboard_id']) && intval($_GET['examboard_id']) > 0) {
            $questionSrch->addCondition('examboard_id', '=', intval($_GET['examboard_id']));
        }
        if (isset($_GET['level_id']) && intval($_GET['level_id']) > 0) {
            $questionSrch->addCondition('level_id', '=', intval($_GET['level_id']));
        }

        $questionSrch->addMultipleFields([
            'id',
            'question_title',
            'answer_a',
            'answer_b',
            'answer_c',
            'answer_d',
            'correct_answer',
            'difficult_level',
            'category',
            'subcategory',
            'topic',
            'subtopic',
            'subtopic_id',
            'course_id',
            'image',
            'question_type',
            'hint',
            'explanation',
            'question_added_on',
            'examboard_id',
            'level_id',
            'tier',
        ]);

        $questionRs = $questionSrch->getResultSet();
        if (!$questionRs) {
            $material['questions'] = [];
            $finalData[] = $material;
            continue;
        }

        $questions = FatApp::getDb()->fetchAll($questionRs);
        $material['questions'] = $questions;
        $finalData[] = $material;
    }

    $this->sets([
        'courseData' => $course,
        'finalData' => $finalData,
        'quizdata' => $result,
        'canEdit' => $this->objPrivilege->canEditCourses(true),
    ]);

    $this->_template->render(false, false);
}


        public function deleteMaterial(int $id)
        {
        $db = FatApp::getDb();
        $id = (int)$id;

            $quotedValue = $db->quoteVariable($id);

            $db->startTransaction();

        try {
            // Delete from tbl_question_bank
            $sql1 = "DELETE FROM tbl_question_bank WHERE subtopic_id = $quotedValue";
            $db->query($sql1);

                $sql2 = "DELETE FROM course_subtopics WHERE id = $quotedValue";
            $db->query($sql2);

            $db->commitTransaction();
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
        } catch (Exception $e) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel('LBL_ERROR_DELETING_RECORD') . ': ' . $e->getMessage());
        }
        }



    public function deleted(int $courseId)
    {
        $db = FatApp::getDb();

        // Step 1: Fetch course details
        $courseRow = $db->fetch(FatApp::getDb()->query("SELECT * FROM tbl_course_management WHERE id = " . $db->quoteVariable($courseId)));

        if (!$courseRow) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        // Define foreign key fields and their corresponding parent tables and field names
        $foreignKeys = [
            'tier'       => ['table' => 'course_tier', 'field' => 'id'],
            'year'       => ['table' => 'course_year', 'field' => 'id'],
            'type'       => ['table' => 'course_type', 'field' => 'id'],
            'examBoards'       => ['table' => 'course_examboards', 'field' => 'id'],
            'level'       => ['table' => 'course_levels', 'field' => 'id'],
            // Add more foreign key mappings as needed
        ];

        foreach ($foreignKeys as $fkField => $ref) {
            if (!array_key_exists($fkField, $courseRow)) {
                continue;
            }

            $fkValue = $courseRow[$fkField];
            if (empty($fkValue)) {
                continue;
            }

            $qry = "SELECT COUNT(*) as total FROM tbl_course_management
            WHERE $fkField = " . $db->quoteVariable($fkValue) . "
            AND id != " . $db->quoteVariable($courseId);
            $countRow = $db->fetch($db->query($qry));

            if ((int)$countRow['total'] === 0) {
                $where = [
                    'smt' => "{$ref['field']} = ?",
                    'vals' => [$fkValue],
                ];

                // Use soft-delete
                // if (!$db->updateFromArray($ref['table'], ['deleted' => 1], $where)) {
                //     error_log("Failed to soft-delete from {$ref['table']} where {$ref['field']} = $fkValue");
                // }
                //    if (!$db->deleteRecords($ref['table'], [$ref['field'] => $fkValue])) {
                //     error_log("Failed to delete from {$ref['table']} where {$ref['field']} = $fkValue");
                // }

                $quotedValue = $db->quoteVariable($fkValue); // Safely escape the value
                $sql = "DELETE FROM {$ref['table']} WHERE {$ref['field']} = $quotedValue";

                if (!$db->query($sql)) {
                    error_log("Manual DELETE failed for {$ref['table']} WHERE {$ref['field']} = $fkValue");
                }
            }
        }




        // Step 3: Soft-delete the course itself
        $data = ['deleted' => 1];
        $where = ['smt' => 'id = ?', 'vals' => [$courseId]];

        if (!$db->updateFromArray('tbl_course_management', $data, $where)) {
            FatUtility::dieJsonError($db->getError());
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Updated_Successfully'));
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
     * Auto Complete JSON
     */
    public function autoCompleteJson()
    {
        $keyword = FatApp::getPostedData('keyword', FatUtility::VAR_STRING, '');
        if (empty($keyword)) {
            FatUtility::dieJsonSuccess(['data' => []]);
        }
        $srch = new SearchBase(CourseLanguage::DB_TBL, 'clang');
        $srch->joinTable(CourseLanguage::DB_TBL_LANG, 'LEFT JOIN', 'clanglang.clanglang_clang_id = clang.clang_id AND clanglang.clanglang_lang_id = ' . $this->siteLangId, 'clanglang');
        $srch->addMultiplefields(['clang_id', 'IFNULL(clanglang.clang_name, clang.clang_identifier) as clang_name']);
        if (!empty($keyword)) {
            $cond = $srch->addCondition('clanglang.clang_name', 'LIKE', '%' . $keyword . '%');
            $cond->attachCondition('clang.clang_identifier', 'LIKE', '%' . $keyword . '%', 'OR');
        }
        $srch->addCondition('clang.clang_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('clang.clang_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addOrder('clang_name', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(20);
        $data = FatApp::getDb()->fetchAll($srch->getResultSet(), 'clang_id');
        FatUtility::dieJsonSuccess(['data' => $data]);
    }

    /**
     * Update status
     *
     * @param int $courseId
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $courseId, int $status)
    {
        $this->objPrivilege->canEditCourses();
        $courseId = FatUtility::int($courseId);
        $status = FatUtility::int($status);
        $status = ($status == AppConstant::YES) ? AppConstant::NO : AppConstant::YES;
        $course = new Course($courseId);
        $course->setFldValue('course_active', $status);
        if (!$course->save()) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_STATUS_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Get Search Form
     *
     * @param int $cateId
     * @return \Form
     */
    private function getSearchForm(int $cateId = 0): Form
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(
            Label::getLabel('LBL_LEVEL'),
            'keyword',
            '',
            ['placeholder' => Label::getLabel('LBL_SEARCH_BY_LEVEL')]
        );
        $frm->addTextBox(
            Label::getLabel('LBL_SUBJECT'),
            'subject',
            '',
            //   ['id' => 'course_clang_id', 'autocomplete' => 'off']
            ['placeholder' => Label::getLabel('LBL_SEARCH_BY_SUBJECT')]
        );
        $frm->addTextBox(
            Label::getLabel('LBL_TOPIC'),
            'topic',
            '',
            //   ['id' => 'course_clang_id', 'autocomplete' => 'off']
            ['placeholder' => Label::getLabel('LBL_SEARCH_BY_TOPIC')]
        );
        $frm->addTextBox(
            Label::getLabel('LBL_SUBTOPIC'),
            'subtopic',
            '',
            //   ['id' => 'course_clang_id', 'autocomplete' => 'off']
            ['placeholder' => Label::getLabel('LBL_SEARCH_BY_SUBTOPIC')]
        );
        // $categoryList = Category::getCategoriesByParentId($this->siteLangId, 0, Category::TYPE_COURSE, true);
        // $frm->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'course_cateid', $categoryList, '', [], Label::getLabel('LBL_SELECT'));
        // $subcategories = [];
        // if ($cateId > 0) {
        //     $subcategories = Category::getCategoriesByParentId($this->siteLangId, $cateId);
        // }
        // $frm->addSelectBox(Label::getLabel('LBL_SUBCATEGORY'), 'course_subcateid', $subcategories, '', [], Label::getLabel('LBL_SELECT'));
        //$frm->addHiddenField('', 'course_clang_id', '', ['id' => 'course_clang_id', 'autocomplete' => 'off']);
        // $frm->addDateField(Label::getLabel('LBL_DATE_FROM'), 'course_addedon_from', '', ['readonly' => 'readonly']);
        // $frm->addDateField(Label::getLabel('LBL_DATE_TO'), 'course_addedon_till', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'order_id');

        // $questionTypes = [
        //     '1' => Label::getLabel('LBL_SINGLE_CHOICE'),
        //     '2' => Label::getLabel('LBL_MULTIPLE_CHOICE'),
        //     '3' => Label::getLabel('LBL_TEXT_BASED')
        // ];
        // $frm->addSelectBox(Label::getLabel('LBL_QUESTION_TYPE'), 'grpcls_tlang_id', $questionTypes, '', [], Label::getLabel('LBL_SELECT'));
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnSubmit->attachField($frm->addButton('', 'btn_reset', Label::getLabel('LBL_Clear')));
        return $frm;
    }
}

