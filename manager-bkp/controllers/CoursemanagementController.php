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
                '*',  'question.id as qid',
             
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

        // Add a hidden input field for adding a new subject
        $fldNewSubject = $frm->addTextBox('', 'new_subject', '');
        $fldNewSubject->setFieldTagAttribute('id', 'new_subject');
        $fldNewSubject->setFieldTagAttribute('style', 'display:none;');
        $fldNewSubject->requirements()->setRequired(false);
    
       
    
        // examBoards Selection
        $examBoards  = ['Option 1', 'Option 2', 'Skip'];
        $frm->addRadioButtons(Label::getLabel('LBL_EXAM_BOARD'), 'examBoards', array_combine($examBoards , $examBoards ));
        // Tier Selection
        $tiers = ['Option 1', 'Option 2', 'Skip'];
        $frm->addRadioButtons(Label::getLabel('LBL_TIER'), 'tier', array_combine($tiers, $tiers));
    
        // Type Selection
        $types = ['Option 1', 'Option 2', 'Skip'];
        $frm->addRadioButtons(Label::getLabel('LBL_TYPE'), 'type', array_combine($types, $types));
    
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

        if($post['level']=='add_new' && $post['new_level']=='')
        {
            FatUtility::dieJsonError('❌ New level required.');
        }

        if($post['subject']=='add_new' && $post['new_subject']=='')
        {
            FatUtility::dieJsonError('❌ New Subject required.');
        }
    
        // if (empty($post['course_title'])) {
        //     FatUtility::dieJsonError('❌ Course title is required.');
        // }
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

    // $subjectQuery = 'SELECT id FROM course_subjects WHERE subject = ' . $db->quoteVariable($post['new_subject']);
    // $subjectRow = $db->fetch($db->query($subjectQuery));

    // if (!$subjectRow) {
        $insertData = [
            'subject' => $post['new_subject'],
            'level_id' => $levelId,
            'created_at'   => date('Y-m-d H:i:s')
        ];
        if (!$db->insertFromArray('course_subjects', $insertData)) {
            FatUtility::dieJsonError('❌ Failed to insert new subject: ' . $db->getError());
        }
        $subjectId = $db->getInsertId();
    // } else {
    //     $subjectId = $subjectRow['id'];
    // }
} else {
    $subjectId = $post['subject'];
}

// Handle Topic
if ($post['topic'] == 'add_new') {
    if (empty($post['new_topic'])) {
        FatUtility::dieJsonError('❌ New Topic is required.');
    }

    // $topicQuery = 'SELECT id FROM course_topics WHERE topic = ' . $db->quoteVariable($post['new_topic']);
    // $topicRow = $db->fetch($db->query($topicQuery));

    // if (!$topicRow) {
        $insertData = [
            'topic' => $post['new_topic'],
            'subject_id' => $subjectId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (!$db->insertFromArray('course_topics', $insertData)) {
            FatUtility::dieJsonError('❌ Failed to insert new topic: ' . $db->getError());
        }
        $topicId = $db->getInsertId();
    // } else {
    //     $topicId = $topicRow['id'];
    // }
} else {
    $topicId = $post['topic'];
}


if(isset($post['tier']) && !empty($post['tier']))
{
    $tier=$post['tier'];
}
else
{
    $tier='';
}

if(isset($post['type']) && !empty($post['type']))
{
    $type=$post['type'];
}
else
{
    $type='';
}

if(isset($post['examBoards']) && !empty($post['examBoards']))
{
    $examBoards=$post['examBoards'];
}
else
{
    $examBoards='';
}
 
        $insertData = [
             'topic'       => $topicId,
             'subject'       => $subjectId,
             'level'       => $levelId,
             'tier'       => $tier,
             'type'       => $type,
             'examBoards'       => $examBoards,
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

        
                if($subtopic=='add_new'){

                    $insertData = [
                        'topic' =>$post['new_subtopics'][$index],
                        'subject_id' => 0,
                        'parent_id' =>$topicId,
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


                        // ✅ Handle CSV file for this subtopic
                if (!empty($_FILES['quiz_csvs']['tmp_name'][$index]) && $_FILES['quiz_csvs']['error'][$index] === UPLOAD_ERR_OK) {
                    $file = $_FILES['quiz_csvs']['tmp_name'][$index];
                    $csvData = array_map('str_getcsv', file($file));
 
                    if (!empty($csvData)) {
                        foreach ($csvData as $rowIndex => $row) {
                            if ($rowIndex === 0) continue; // Skip header row
 
                            if (count($row) < 12) continue; // Ensure valid row format

                            list(
                                $question_text, $answer_a, $answer_b, $answer_c, $answer_d,
                                $correct_answer, $difficulty, $category, $topic, $subtopic,
                                $grade, $question_type 
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
 
        $srch = new CourseManagement($this->siteLangId, 0, User::SUPPORT);

        $srch->addCondition('question.id', '=', $courseId);
       // $srch->applyPrimaryConditions();
 
        $courses = $srch->fetchAndFormat();
       
        if (empty($courses)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
 
                $db = FatApp::getDb(); // Get the database connection instance
                $data = ['deleted' => 1]; // Column and new value
                
                // Specify the conditions for updating
                $where = ['smt' => 'id = ?', 'vals' => [$courseId]];
                
                // Perform the update
                if (!$db->updateFromArray('tbl_course_management', $data, $where)) {
                    FatUtility::dieJsonError($db->getError());
                }
                
                // Return success response
                FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Updated_Successfully'));
                

        // $course = current($courses);
        // $this->sets([
        //     'courseData' => $course,
        //     'canEdit' => $this->objPrivilege->canEditCourses(true),
        // ]);
        // $this->_template->render(false, false);
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
