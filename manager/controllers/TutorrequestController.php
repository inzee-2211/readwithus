<?php

/**
 * Courses Controller is used for course handling
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class TutorrequestController extends AdminBaseController
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



   public function search()
{
    $frm  = $this->getSearchForm();
    $post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['user_id', 'subtopic_id']);

    $srch = new SearchBase('course_findatutor', 'qa');

    // Fields to fetch
    $srch->addMultipleFields(['qa.*']);

    // Pagination defaults
    $page     = (int)($post['page'] ?? 1);
    $pageSize = (int)($post['pagesize'] ?? 10);

    $srch->addOrder('qa.id', 'DESC');
    $srch->setPageSize($pageSize);
    $srch->setPageNumber($page);

    $db = FatApp::getDb();
    $rs = $srch->getResultSet();

    // 🔍 IMPORTANT: guard + debug the DB error
    if ($rs === false) {
        die('DB Error in TutorrequestController::search => ' . $db->getError());
    }

    $records = $db->fetchAll($rs);

    $this->sets([
        'arrListing'  => $records,
        'page'        => $page,
        'post'        => $post,
        'pageSize'    => $pageSize,
        'pageCount'   => $srch->pages(),
        'recordCount' => $srch->recordCount(),
        'canEdit'     => false,
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
        // Exam Board options array
        $examboards = $this->getExamboardsFromDB();
        $examboards = [
            '' => 'Select Exam Board', // Empty value for the placeholder
            'add_new' => '➕ Add New'
        ] + $examboards;

        // Add select box for Exam Board
        $fldExamBoard = $frm->addSelectBox(Label::getLabel('LBL_EXAM_BOARD'), 'examboard', $examboards, '', [], '');
        $fldExamBoard->setFieldTagAttribute('id', 'examboard');
        $fldExamBoard->setFieldTagAttribute('onchange', 'handleExamBoardChange(this.value)');
        $fldExamBoard->requirements()->setRequired();
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
        $fldTier->setFieldTagAttribute('onchange', 'handleTierChange(this.value)');
        $fldTier->requirements()->setRequired();
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
        $fldYear = $frm->addSelectBox(Label::getLabel('LBL_YEAR'), 'year', $years, '', [], '');
        $fldYear->setFieldTagAttribute('id', 'year');
        $fldYear->setFieldTagAttribute('onchange', 'handleYearChange(this.value)');
        $fldYear->requirements()->setRequired();
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



        // examBoards Selection
        //$examBoards  = ['Option 1', 'Option 2', 'Skip'];
        // $frm->addRadioButtons(Label::getLabel('LBL_EXAM_BOARD'), 'examBoards', array_combine($examBoards, $examBoards));
        // Tier Selection
        // $tiers = ['Option 1', 'Option 2', 'Skip'];
        // $frm->addRadioButtons(Label::getLabel('LBL_TIER'), 'tier', array_combine($tiers, $tiers));

        // Type Selection
        // $types = ['Option 1', 'Option 2', 'Skip'];
        // $frm->addRadioButtons(Label::getLabel('LBL_TYPE'), 'type', array_combine($types, $types));

        // Topic Selection (Initially empty, dynamically loaded)
        $topics = []; // Fetch topics from DB
        // $topics['add_new'] = '➕ Add New';
        $topics = [
            '' => 'Select Topics', // Empty value for the placeholder
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
        $query = "SELECT id, name FROM course_examboards"; // Replace with actual table name
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

    private function getTierFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, name FROM course_tier"; // Replace with actual table name
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
        // Assuming the 'level_id' is passed in the request
        $subjectId = FatApp::getPostedData('subjectId', FatUtility::VAR_INT, 0);

        // Check if level ID is valid
        if ($subjectId <= 0) {
            echo json_encode(['status' => 0, 'msg' => 'Invalid subjectId ID']);
            return;
        }

        // Fetch subjects for the given level from the database
        $topics = $this->getTopicBySubjectId($subjectId); // Your method to get subjects by level

        if (empty($topics)) {
            echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
            return;
        }

        // Add the "Add New" option
        //   $topics['add_new'] = ['id' => 'add_new', 'name' => '➕ Add New'];

        // Return subjects as JSON response
        echo json_encode(['status' => 1, 'data' => $topics]);
    }
    private function getTopicBySubjectId($subjectId)
    {
        // Get the database instance
        $db = FatApp::getDb();

        // Directly insert the levelId into the query (ensure proper escaping to prevent SQL injection)
        $query = "SELECT id, topic FROM course_topics WHERE subject_id = $subjectId"; // Direct insertion

        // Execute the query
        $result = $db->query($query);

        // Check if the query was successful
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        // Fetch all results
        $topics = $db->fetchAll($result);

        // If no records found, return an empty array
        if (empty($topics)) {
            return [];
        }

        // Return the subjects as an associative array
        return array_column($topics, 'topic', 'id');
    }


    public function getsubjectsforlevel()
    {
        // Assuming the 'level_id' is passed in the request
        $levelId = FatApp::getPostedData('levelId', FatUtility::VAR_INT, 0);

        // Check if level ID is valid
        if ($levelId <= 0) {
            echo json_encode(['status' => 0, 'msg' => 'Invalid level ID']);
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





    private function getSubjectsFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, subject FROM course_subjects"; // Replace with actual table name
        $result = $db->query($query);
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $levels = $db->fetchAll($result);

        if (empty($levels)) {
            return []; // Return an empty array if no records found
        }

        return array_column($levels, 'subject', 'id');
    }

    private function getTopicsFromDB()
    {
        $db = FatApp::getDb();
        $query = "SELECT id, topic FROM course_topics"; // Replace with actual table name
        $result = $db->query($query);
        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $levels = $db->fetchAll($result);

        if (empty($levels)) {
            return []; // Return an empty array if no records found
        }

        return array_column($levels, 'topic', 'id');
    }


    public function setup()
    {
        $this->objPrivilege->canEditCategories();

        $post = FatApp::getPostedData();


        if (empty($post['level']) || empty($post['subject'])) {
            FatUtility::dieJsonError('❌ Level and Subject are required.');
        }

        if ($post['level'] == 'add_new' && $post['new_level'] == '') {
            FatUtility::dieJsonError('❌ New level required.');
        }

        if ($post['subject'] == 'add_new' && $post['new_subject'] == '') {
            FatUtility::dieJsonError('❌ New Subject required.');
        }
        $db = FatApp::getDb();

        if ($post['level'] == 'add_new') {
            if (empty($post['new_level'])) {
                FatUtility::dieJsonError('❌ New Level is required.');
            }

            // Check if the new level already exists
            $levelQuery = 'SELECT id FROM course_levels WHERE level_name = ' . $db->quoteVariable($post['new_level']);
            $levelRow = $db->fetch($db->query($levelQuery));

            if (!$levelRow) {
                $insertData = [
                    'level_name'  => $post['new_level'],
                    'created_at'  => date('Y-m-d H:i:s')
                ];
                if (!$db->insertFromArray('course_levels', $insertData)) {
                    FatUtility::dieJsonError('❌ Failed to insert new level: ' . $db->getError());
                }
                $levelId = $db->getInsertId();
            } else {
                $levelId = $levelRow['id'];
            }
        } else {
            $levelId = $post['level'];
        }

        // Handle Subject
        if ($post['subject'] == 'add_new') {
            if (empty($post['new_subject'])) {
                FatUtility::dieJsonError('❌ New Subject is required.');
            }

            $insertData = [
                'subject' => $post['new_subject'],
                'level_id' => $levelId,
                'created_at'   => date('Y-m-d H:i:s')
            ];
            if (!$db->insertFromArray('course_subjects', $insertData)) {
                FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
            }
            $subjectId = $db->getInsertId();
        } else {
            $subjectId = $post['subject'];
        }

        if ($post['examboard'] == 'add_new') {
            if (empty($post['new_examboard'])) {
                FatUtility::dieJsonError('❌ New Examboard is required.');
            }

            $insertData = [
                'name' => $post['new_examboard'],
                'created_at'   => date('Y-m-d H:i:s')
            ];
            if (!$db->insertFromArray('course_examboards', $insertData)) {
                FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
            }
            $examboardId = $db->getInsertId();
        } else {
            $examboardId = $post['examboard'];
        }

        if ($post['tier'] == 'add_new') {
            if (empty($post['new_tier'])) {
                FatUtility::dieJsonError('❌ New Tier is required.');
            }

            $insertData = [
                'name' => $post['new_tier'],
                'created_at'   => date('Y-m-d H:i:s')
            ];
            if (!$db->insertFromArray('course_tier', $insertData)) {
                FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
            }
            $tierId = $db->getInsertId();
        } else {
            $tierId = $post['tier'];
        }

        if ($post['type'] == 'add_new') {
            if (empty($post['new_type'])) {
                FatUtility::dieJsonError('❌ New Type is required.');
            }

            $insertData = [
                'name' => $post['new_type'],
                'created_at'   => date('Y-m-d H:i:s')
            ];
            if (!$db->insertFromArray('course_type', $insertData)) {
                FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
            }
            $typeId = $db->getInsertId();
        } else {
            $typeId = $post['type'];
        }


        if ($post['year'] == 'add_new') {
            if (empty($post['new_type'])) {
                FatUtility::dieJsonError('❌ New Year is required.');
            }

            $insertData = [
                'name' => $post['new_year'],
                'created_at'   => date('Y-m-d H:i:s')
            ];
            if (!$db->insertFromArray('course_year', $insertData)) {
                FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
            }
            $yearId = $db->getInsertId();
        } else {
            $yearId = $post['year'];
        }

        // Handle Topic
        if ($post['topic'] == 'add_new') {
            if (empty($post['new_topic'])) {
                FatUtility::dieJsonError('❌ New Topic is required.');
            }

            $insertData = [
                'topic' => $post['new_topic'],
                'subject_id' => $subjectId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            if (!$db->insertFromArray('course_topics', $insertData)) {
                FatUtility::dieJsonError('❌ Failed to insert new topic: ' . $db->getError());
            }
            $topicId = $db->getInsertId();
        } else {
            $topicId = $post['topic'];
        }

        $insertData = [
            'topic'       => $topicId,
            'subject'       => $subjectId,
            'level'       => $levelId,
            'tier'       => $tierId,
            'year'       => $yearId,
            'type'       => $typeId,
            'examBoards'       => $examboardId,
            'created_on'         => date('Y-m-d H:i:s')
        ];

        if (!$db->insertFromArray('tbl_course_management', $insertData)) {
            $db->rollbackTransaction(); // ❌ Rollback if failed
            FatUtility::dieJsonError('❌ Error inserting course: ' . $db->getError());
        }

        // ✅ Get the inserted Course ID
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
                $subtopicId = $subtopic;


                // ✅ Handle CSV file for this subtopic
                if (!empty($_FILES['quiz_csvs']['tmp_name'][$index]) && $_FILES['quiz_csvs']['error'][$index] === UPLOAD_ERR_OK) {
                    $file = $_FILES['quiz_csvs']['tmp_name'][$index];
                    $csvData = array_map('str_getcsv', file($file));

                    if (!empty($csvData)) {
                        foreach ($csvData as $rowIndex => $row) {
                            if ($rowIndex === 0) continue; // Skip header row

                            if (count($row) < 12) continue; // Ensure valid row format

                            list(
                                $question_text,
                                $answer_a,
                                $answer_b,
                                $answer_c,
                                $answer_d,
                                $correct_answer,
                                $difficulty,
                                $category,
                                $topic,
                                $subtopic,
                                $grade,
                                $question_type
                            ) = $row;

                            // Insert question into question bank
                            $questionData = [
                                'question_title' => trim($question_text),
                                'answer_a' => trim($answer_a),
                                'answer_b' => trim($answer_b),
                                'answer_c' => trim($answer_c),
                                'answer_d' => trim($answer_d),
                                'correct_answer' => trim($correct_answer),
                                'difficult_level' => trim($difficulty),
                                'subtopic_id' => $subtopicId, // ✅ Link question to the inserted subtopic
                                'course_id' => $courseId,
                                'category' => $category,
                                'topic' => $topic,
                                'subtopic' => $subtopic,
                                'grade' => $grade,
                                'question_type' => $question_type,
                                'subcategory' => '',
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

        // Insert Quizzes
        /*   if (!empty($post['quiz']) && is_array($post['quiz'])) {
            foreach ($post['quiz'] as $quiz) {
                $quizData = [
                    'course_id' => $courseId,
                    'quiz'      => $quiz['quiz'],
                    'created_on' => date('Y-m-d H:i:s')
                ];
                if (!$db->insertFromArray('tbl_course_quizzes', $quizData)) {
                    $db->rollbackTransaction();
                    FatUtility::dieJsonError('❌ Error inserting quiz: ' . $db->getError());
                }
            }
        }
    */
        $db->commitTransaction();

        FatUtility::dieJsonSuccess([
            'msg' => '✅ Course added successfully!'
        ]);
    }




    /**
     * Render Course View
     *
     * @param int $courseId
     * return html
     */

    public function view(int $courseId)
    {
        $srch = new CourseSearch($this->siteLangId, 0, User::SUPPORT);

        $srch->addCondition('course.course_id', '=', $courseId);
        $srch->applyPrimaryConditions();

        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'subcate.cate_id = course.course_subcate_id', 'subcate');
        $srch->joinTable(
            Category::DB_LANG_TBL,
            'LEFT JOIN',
            'subcate.cate_id = subcatelang.catelang_cate_id AND subcatelang.catelang_lang_id = ' . $this->siteLangId,
            'subcatelang'
        );

        $srch->addSearchListingFields();
        $srch->addFld('subcatelang.cate_name AS subcate_name');
        $courses = $srch->fetchAndFormat();
        if (empty($courses)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = current($courses);


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
        $srch->addCondition('question.id', '=', $courseId); // 🔑 Filter by Course ID

        $srch->setPageSize(1); // Since you're expecting a single course
        $srch->setPageNumber(1);

        $result = $srch->fetchAndFormat();




        // $srch = new SearchBase('course_subtopics'); // Replace with actual table name if different
        // $srch->addCondition('course_id', '=', $courseId);
        // $srch->addMultipleFields([
        //     'id',
        //     'subtopic',
        //     'video_url',
        //     'previous_paper_pdf',
        //     'course_id',
        //     'created_at'
        // ]);

        // $rs = $srch->getResultSet();
        // $data = FatApp::getDb()->fetchAll($rs, 'id');

        // echo '<pre>'; print_r($data); die;





        // Step 1: Fetch course materials
        $materialSrch = new SearchBase('course_subtopics'); // Replace with actual table name
        $materialSrch->addCondition('course_id', '=', $courseId);
        $materialSrch->addMultipleFields([
            'id',           // We'll use this as subtopic_id
            'subtopic',
            'video_url',
            'previous_paper_pdf',
            'course_id',
            'created_at'
        ]);

        $materialRs = $materialSrch->getResultSet();
        $courseMaterials = FatApp::getDb()->fetchAll($materialRs);

        // Step 2: Loop through each course material and fetch questions
        $finalData = [];

        foreach ($courseMaterials as $material) {
            $subtopicId = $material['id']; // Assuming this ID is used as subtopic_id in tbl_question_bank

            $questionSrch = new SearchBase('tbl_quaestion_bank');
            $questionSrch->addCondition('subtopic_id', '=', $subtopicId);
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
                'grade',
                'question_type',
                'hint',
                'explanation',
                'question_added_on',
            ]);

            $questionRs = $questionSrch->getResultSet();
            $questions = FatApp::getDb()->fetchAll($questionRs);
            // $materialRs = $materialSrch->getResultSet();
            // $courseMaterials = FatApp::getDb()->fetchAll($materialRs);
            //     // Merge course material with its questions
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

 

    public function deleted(int $attemptId)
    {
        $db = FatApp::getDb();
 
        // Step 1: Fetch quiz attempt row
         $sql = "DELETE FROM course_findatutor WHERE id = $attemptId";
        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_FAILED_TO_DELETE_ATTEMPT'));
        }

        // if (!$attemptRow) {
        //     FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        // }

       /* $subtopicId = $attemptRow['subtopic_id'];
        $userId = $attemptRow['user_id'];

        $sql = "DELETE FROM tbl_quiz_attempts WHERE id = $attemptId";
        if (!$db->query($sql)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_FAILED_TO_DELETE_ATTEMPT'));
        }


        if ($userId > 0) {
            $checkUser = $db->fetch($db->query(
                "SELECT COUNT(*) as total FROM tbl_quiz_attempts WHERE user_id = " . $db->quoteVariable($userId)
            ));

            if ((int)$checkUser['total'] === 0) {
               
                $sql = "DELETE FROM course_attempt_userdetails WHERE user_id = $user_id";
                if (!$db->query($sql)) {
                    FatUtility::dieJsonError(Label::getLabel('LBL_FAILED_TO_DELETE_ATTEMPT'));
                }
            }
        }*/



        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Deleted_Successfully'));
    }



    /**
     * Fetch sub categories for selected category
     *
     * @param int $catgId
     * @param int $subCatgId
     * @return html
     */
   
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
