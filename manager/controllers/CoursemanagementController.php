<?php
require_once CONF_APPLICATION_PATH . 'library/ZipImageBag.php';

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
    $frm  = $this->getSearchForm();
    $post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_subcateid']);

    $srch = new SearchBase('tbl_quiz_management', 'qm');          // subtopics
    $srch->joinTable('tbl_quiz_setup', 'INNER JOIN', 'qs.id = qm.quiz_setup_id', 'qs'); // topics
    $srch->joinTable('course_subjects', 'LEFT JOIN', 'cs.id = qs.subject_id', 'cs');    // subjects

    $srch->addMultipleFields([
        'qm.id AS qid',
        'IFNULL(cs.subject, "") AS subject',
        'IFNULL(qs.topic_name, "") AS topic',
        'qm.subtopic_name',
        'qm.video_url',
        'qm.pdf_path',
        'qm.answer_pdf_path',
        'qm.created_at',
        'qm.updated_at',
    ]);

    // Filters: subject / topic / subtopic (case-insensitive)
    if (!empty($post['subject'])) {
        $srch->addCondition('cs.subject', 'LIKE', '%' . trim($post['subject']) . '%');
    }
    if (!empty($post['topic'])) {
        $srch->addCondition('qs.topic_name', 'LIKE', '%' . trim($post['topic']) . '%');
    }
    if (!empty($post['subtopic'])) {
        $srch->addCondition('qm.subtopic_name', 'LIKE', '%' . trim($post['subtopic']) . '%');
    }

    $srch->setPageSize((int)$post['pagesize']);
    $srch->setPageNumber((int)$post['page']);
    $srch->addOrder('qm.id', 'DESC');

    $rs     = $srch->getResultSet();
    $rows   = FatApp::getDb()->fetchAll($rs);

    $this->sets([
        'arrListing'  => $rows,
        'page'        => $post['page'],
        'post'        => $post,
        'pageSize'    => $post['pagesize'],
        'pageCount'   => $srch->pages(),
        'recordCount' => $srch->recordCount(),
        'canEdit'     => $this->objPrivilege->canEditCourses(true),
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

private function getForm(int $id = 0): Form
{
    $frm = new Form('frmQuizManagement');
    $db  = FatApp::getDb();

    // SUBJECTS (build options WITHOUT an empty '' key)
    $subjectRows = $db->fetchAll($db->query("SELECT id, subject FROM course_subjects ORDER BY subject ASC"));
    $subjectList = [];
    if ($subjectRows) {
        foreach ($subjectRows as $s) {
            $subjectList[$s['id']] = $s['subject'];
        }
    }

    $posted      = FatApp::getPostedData();
    if ($id > 0 && empty($posted)) {
    $row = $db->fetch("SELECT qm.quiz_setup_id, qs.subject_id, qs.examboard_id, qs.tier_id
                         FROM tbl_quiz_management qm
                   INNER JOIN tbl_quiz_setup qs ON qs.id = qm.quiz_setup_id
                        WHERE qm.id = ?", [$id]);
    if ($row) {
        $subjectId   = (int)$row['subject_id'];
        $examboardId = (int)$row['examboard_id'];
        $tierId      = (int)$row['tier_id'];
    }
}

    $subjectId   = FatUtility::int($posted['subject_id']   ?? ($subjectId   ?? 0));
$examboardId = FatUtility::int($posted['examboard_id'] ?? ($examboardId ?? 0));
$tierId      = FatUtility::int($posted['tier_id']      ?? ($tierId      ?? 0));


    // Subject (pass placeholder in the LAST param, not inside options)
    $fldSubj = $frm->addSelectBox('Subject', 'subject_id', $subjectList, $subjectId, [], Label::getLabel('LBL_SELECT_SUBJECT'));
    $fldSubj->setFieldTagAttribute('id', 'subject_id');
    $fldSubj->requirements()->setRequired();

    // Examboard (empty options here; client fills via AJAX; placeholder in LAST param)
    $fldBoard = $frm->addSelectBox('Examboard', 'examboard_id', [], $examboardId, [], Label::getLabel('LBL_SELECT_EXAMBOARD'));
    $fldBoard->setFieldTagAttribute('id', 'examboard_id');

    // Tier (same)
    $fldTier = $frm->addSelectBox('Tier', 'tier_id', [], $tierId, [], Label::getLabel('LBL_SELECT_TIER'));
    $fldTier->setFieldTagAttribute('id', 'tier_id');

    // Topics: keep server fallback by subject (no '' in options; pass placeholder last)
    $topicList = [];
    if ($subjectId > 0) {
        $qSid = $db->quoteVariable($subjectId);
        $rows = $db->fetchAll($db->query(
            "SELECT id, topic_name FROM tbl_quiz_setup WHERE subject_id = $qSid ORDER BY topic_name ASC"
        ));
        if ($rows) {
            foreach ($rows as $t) { $topicList[$t['id']] = $t['topic_name']; }
        }
    }
    $fldTopic = $frm->addSelectBox('Topic', 'quiz_setup_id', $topicList, '', [], Label::getLabel('LBL_SELECT_TOPIC'));
    $fldTopic->setFieldTagAttribute('id', 'quiz_setup_id');
    $fldTopic->requirements()->setRequired();

    // Subtopic + uploads
    $frm->addTextBox('Subtopic Name', 'subtopic_name', '')->requirements()->setRequired();
    $frm->addTextBox('Video URL (optional)', 'video_url', '');
    $frm->addFileUpload('Upload Past Paper PDF', 'pdf_path', ['accept' => '.pdf']);
    $frm->addFileUpload('Upload Answer Paper PDF', 'answer_pdf_path', ['accept' => '.pdf']);
    $frm->addFileUpload('Upload Quiz CSV', 'quiz_csv', ['accept' => '.csv']);
    $frm->addFileUpload('Upload Images ZIP (optional)', 'quiz_images_zip', ['accept' => '.zip']);


    // Hidden fields
    $frm->addHiddenField('', 'id', $id);
    $frm->addHiddenField('', 'existing_pdf', '');
    $frm->addHiddenField('', 'existing_answer_pdf', '');

    $frm->addSubmitButton('', 'btn_submit', $id > 0 ? 'Update Subtopic' : 'Save Subtopic');
    return $frm;
}

public function downloadSampleCsv()
{
    $this->objPrivilege->canViewCourses();

    // Force download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="quiz_questions_sample.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');

    // UTF-8 BOM for Excel compatibility
    fwrite($out, "\xEF\xBB\xBF");

    // This matches your setup() CSV import (11 columns)
    $headers = [
        'question_text',
        'answer_a',
        'answer_b',
        'answer_c',
        'answer_d',
        'correct_answer',   // A/B/C/D
        'difficulty',       // Easy/Medium/Hard
        'question_type',    // Multiple-Choice / Story-Based / Short (recommend Multiple-Choice here)
        'hint',
        'explanation',
        'image_url',
          'option_mode',        // text|image
    'answer_a_image',     // URL or ZIP filename
    'answer_b_image',
    'answer_c_image',
    'answer_d_image',
    ];
    fputcsv($out, $headers);

    // Sample rows
    fputcsv($out, [
        'What is 2 + 2?',
        '3', '4', '5', '6',
        'B',
        'Easy',
        'Multiple-Choice',
        'Think about pairs.',
        '2 + 2 equals 4.',
        'https://example.com/sample-q1.png',
          'text', '', '', '', ''
    ]);

    fputcsv($out, [
        'Read: "The cat sat on the mat." Which word is a noun?',
        'cat', 'sat', 'on', 'mat',
        'A',
        'Medium',
        'Multiple-Choice',
        'A noun is a person/place/thing.',
        '"Cat" is a noun (a thing/animal).',
        ''
    ]);

    fclose($out);
    exit; // IMPORTANT: stop template output
}

public function topicsBySubject()
{
    $db  = FatApp::getDb();

    $sid = FatUtility::int(
        FatApp::getPostedData('subject_id', FatUtility::VAR_INT,
            FatApp::getPostedData('subjectId', FatUtility::VAR_INT, 0)
        )
    );
    $examboardId = FatUtility::int(FatApp::getPostedData('examboard_id', FatUtility::VAR_INT, 0));
    $tierId      = FatUtility::int(FatApp::getPostedData('tier_id', FatUtility::VAR_INT, 0));

    if ($sid <= 0) {
        FatUtility::dieJsonSuccess(['status' => 1, 'data' => [], 'count' => 0]);
    }

    // Build WHERE with optional filters (preserve subject-only as fallback)
    $where = ["subject_id = " . $db->quoteVariable($sid)];
    if ($examboardId > 0) $where[] = "examboard_id = " . $db->quoteVariable($examboardId);
    if ($tierId      > 0) $where[] = "tier_id = "      . $db->quoteVariable($tierId);

    $sql  = "SELECT id, topic_name
               FROM tbl_quiz_setup
              WHERE " . implode(' AND ', $where) . "
           ORDER BY topic_name ASC";

    $rs   = $db->query($sql);
    if (!$rs) {
        FatUtility::dieJsonSuccess(['status' => 1, 'data' => [], 'count' => 0]);
    }

    $rows  = $db->fetchAll($rs);
    $pairs = $rows ? array_column($rows, 'topic_name', 'id') : [];

    FatUtility::dieJsonSuccess(['status' => 1, 'data' => $pairs, 'count' => count($pairs)]);
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
    // accept either subject_id or subjectId (some callers use one or the other)
    $subjectId = FatUtility::int(
        FatApp::getPostedData('subject_id', FatUtility::VAR_INT,
            FatApp::getPostedData('subjectId', FatUtility::VAR_INT, 0)
        )
    );

    if ($subjectId <= 0) {
        echo json_encode(['status' => 0 , 'msg' => 'Invalid subject ID']);
        return;
    }

    $examboard = $this->getExamboardBySubject($subjectId);

    if (empty($examboard)) {
        // optional fallback: return all boards (or keep it empty if you prefer)
        // $examboard = $this->pair("SELECT id, name FROM course_examboards ORDER BY name");
        echo json_encode(['status' => 0, 'msg' => 'No exam boards found for this subject']);
        return;
    }

    // keep the “Add New” option if you use it elsewhere
    $examboard['add_new'] = ['id' => 'add_new', 'name' => '➕ Add New'];

    echo json_encode(['status' => 1, 'data' => $examboard]);
}


    private function getExamboardBySubject(int $subjectId): array
{
    $db  = FatApp::getDb();
    $qid = $db->quoteVariable($subjectId);

    // Pull distinct boards that are actually used by topics for the selected subject
    $sql = "SELECT eb.id, eb.name
              FROM tbl_quiz_setup qs
        INNER JOIN course_examboards eb ON eb.id = qs.examboard_id
             WHERE qs.subject_id = $qid
          GROUP BY eb.id, eb.name
          ORDER BY eb.name ASC";

    $rs   = $db->query($sql);
    if (!$rs) { return []; }

    $rows = $db->fetchAll($rs);
    return $rows ? array_column($rows, 'name', 'id') : [];
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
    $courseId   = FatUtility::int($post['course_id'] ?? 0);

    if ($subtopicId <= 0) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_SUBTOPIC_OR_COURSE'));
    }

    if (empty($_FILES['question_csv']['tmp_name']) || $_FILES['question_csv']['error'] !== UPLOAD_ERR_OK) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_OR_MISSING_CSV_FILE'));
    }

    $file = $_FILES['question_csv'];

    // Extension check (mime is unreliable on many servers)
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($fileExtension !== 'csv') {
        FatUtility::dieJsonError(Label::getLabel('LBL_ONLY_CSV_FILES_ARE_ALLOWED'));
    }

    $db = FatApp::getDb();
    $zipBag = null;

    try {
        // Optional zip (supports either field name)
        $zipBag = $this->initZipBagFromUpload('question_images_zip')
            ?: $this->initZipBagFromUpload('quiz_images_zip');

        $lines = $this->readCsvRows($file['tmp_name']);
        if (count($lines) < 2) {
            throw new Exception(Label::getLabel('LBL_CSV_FILE_HAS_NO_VALID_DATA'));
        }

        $db->startTransaction();

        $sql = "DELETE FROM tbl_quaestion_bank WHERE subtopic_id = " . (int)$subtopicId;
        if (!$db->query($sql)) {
            throw new Exception(Label::getLabel('LBL_FAILED_TO_CLEAR_OLD_QUESTIONS'));
        }

        unset($lines[0]); // header
        $insertedCount = 0;

        $uploadDir = 'uploads/question_images/';
        if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
foreach ($lines as $rowIndex => $line) {
      // ✅ Support old 16-col CSV + new 21-col CSV
$line = array_pad($line, 21, '');

[
  $question_text, $answer_a, $answer_b, $answer_c, $answer_d,
  $correct_answer, $difficulty, $examboardName, $topic, $subtopicName,
  $levelName, $question_type, $tier, $hint, $explanation, $image_url,

  // ✅ NEW
  $option_mode, $a_img, $b_img, $c_img, $d_img
] = $line;

$option_mode = strtolower(trim((string)$option_mode));
$option_mode = ($option_mode === 'image') ? 'image' : 'text';


            if (trim($question_text) === '' || trim($correct_answer) === '') {
                continue;
            }

            // Keep newlines consistent
            $explanation = str_replace(["\r\n", "\r"], "\n", (string)$explanation);

            $imagePath = $this->resolveAndSaveQuestionImage((string)$image_url, $zipBag, $uploadDir);
            $optAPath = $optBPath = $optCPath = $optDPath = '';
if ($option_mode === 'image') {
    $optAPath = $this->resolveAndSaveQuestionImage((string)$a_img, $zipBag, $uploadDir);
    $optBPath = $this->resolveAndSaveQuestionImage((string)$b_img, $zipBag, $uploadDir);
    $optCPath = $this->resolveAndSaveQuestionImage((string)$c_img, $zipBag, $uploadDir);
    $optDPath = $this->resolveAndSaveQuestionImage((string)$d_img, $zipBag, $uploadDir);
}


            $examboardRow = $db->fetch("SELECT id FROM course_examboards WHERE name = ?", [$examboardName]);
            $examboardId  = $examboardRow ? (int)$examboardRow['id'] : 0;

            $levelRow = $db->fetch("SELECT id FROM course_levels WHERE level_name = ?", [$levelName]);
            $levelId  = $levelRow ? (int)$levelRow['id'] : 0;

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
                'category'          => '',
                'subcategory'       => '',
                'explanation'       => trim($explanation),
                'image'             => $imagePath,
                'question_added_on' => date('Y-m-d H:i:s'),
                'option_mode'    => $option_mode,
'answer_a_image' => $optAPath,
'answer_b_image' => $optBPath,
'answer_c_image' => $optCPath,
'answer_d_image' => $optDPath,
            ];

            if (!$db->insertFromArray('tbl_quaestion_bank', $questionData)) {
               throw new Exception("Row #" . ($rowIndex + 1) . " insert failed: " . $db->getError());

            }

            $insertedCount++;
        }

        $db->commitTransaction();

        FatUtility::dieJsonSuccess([
            'msg'    => "✅ {$insertedCount} questions uploaded successfully!",
            'status' => 1
        ]);

    } catch (Exception $e) {
        // Some Fatbit DB wrappers don’t have inTransaction(); rollback is safe anyway
        $db->rollbackTransaction();
        FatUtility::dieJsonError($e->getMessage());
    } finally {
        if ($zipBag) { $zipBag->cleanup(); }
    }
}

public function setup()
{
    $db   = FatApp::getDb();
    $post = FatApp::getPostedData();
    $id   = FatUtility::int($post['id'] ?? 0);

    $quizSetupId  = FatUtility::int($post['quiz_setup_id']);
    $subtopicName = trim($post['subtopic_name'] ?? '');
    $videoUrl     = trim($post['video_url'] ?? '');

    if ($quizSetupId <= 0 || $subtopicName === '') {
        FatUtility::dieJsonError('Topic and Subtopic name are required.');
    }

    // PDF upload logic remains the same...
    $pdfPath = '';
    if (!empty($_FILES['pdf_path']['tmp_name']) && $_FILES['pdf_path']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['pdf_path']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') FatUtility::dieJsonError('Only PDF allowed');
        $uploadDir = 'uploads/past_papers/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $pdfPath = $uploadDir . uniqid('paper_') . '.' . $ext;
        move_uploaded_file($_FILES['pdf_path']['tmp_name'], $pdfPath);
    }
// Answer paper PDF
$answerPdfPath = '';
if (!empty($_FILES['answer_pdf_path']['tmp_name']) && $_FILES['answer_pdf_path']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['answer_pdf_path']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') FatUtility::dieJsonError('Only PDF allowed');
    $uploadDir = 'uploads/past_papers/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $answerPdfPath = $uploadDir . uniqid('answer_') . '.' . $ext;
    move_uploaded_file($_FILES['answer_pdf_path']['tmp_name'], $answerPdfPath);
}

    $db->startTransaction();

    $payload = [
        'quiz_setup_id' => $quizSetupId,
        'subtopic_name' => $subtopicName,
        'video_url'     => $videoUrl,
        'updated_at'    => date('Y-m-d H:i:s'),
    ];
    if ($pdfPath) { $payload['pdf_path'] = $pdfPath; }
    if ($answerPdfPath)  { $payload['answer_pdf_path'] = $answerPdfPath; }

    // Insert/Update subtopic logic remains the same...
    if ($id > 0) {
        if (!$db->updateFromArray('tbl_quiz_management', $payload, ['smt' => 'id = ?', 'vals' => [$id]])) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError('Failed to update subtopic: ' . $db->getError());
        }
        $subtopicId = $id;

        // Clear old questions if new CSV uploaded
        if (!empty($_FILES['quiz_csv']['tmp_name']) && $_FILES['quiz_csv']['error'] === UPLOAD_ERR_OK) {
            if (!$db->deleteRecords('tbl_quaestion_bank', ['smt' => 'subtopic_id = ?', 'vals' => [$subtopicId]])) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError('Failed to clear old questions: ' . $db->getError());
            }
        }
    } else {
        $payload['created_at'] = date('Y-m-d H:i:s');
        if (!$db->insertFromArray('tbl_quiz_management', $payload)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError('Failed to insert subtopic: ' . $db->getError());
        }
        $subtopicId = $db->getInsertId();
    }

    // UPDATED CSV PROCESSING WITH EXPLANATION AND IMAGE SUPPORT
    // if (!empty($_FILES['quiz_csv']['tmp_name']) && $_FILES['quiz_csv']['error'] === UPLOAD_ERR_OK) {
    //     $csvData = array_map('str_getcsv', file($_FILES['quiz_csv']['tmp_name']));
    //     $uploadDir = 'uploads/question_images/';
    //     if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
    //     foreach ($csvData as $i => $row) {
    //         if ($i === 0 || count($row) < 11) continue; // Skip header and incomplete rows
            
    //         // Pad to at least 11 fields to handle optional fields
    //         $row = array_pad($row, 11, '');
            
    //         list($question_text, $a, $b, $c, $d, $correct, $difficulty, $question_type, $hint, $explanation, $image_url) = $row;

    //         // Handle image upload from URL
    //         $imagePath = '';
    //         if (!empty($image_url)) {
    //             $imageContent = @file_get_contents(trim($image_url));
    //             if ($imageContent !== false) {
    //                 $ext = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION);
    //                 if (empty($ext)) {
    //                     $ext = 'jpg'; // Default extension
    //                 }
    //                 $imageName = uniqid('qimg_') . '.' . $ext;
    //                 $imagePath = $uploadDir . $imageName;
    //                 file_put_contents($imagePath, $imageContent);
    //             }
    //         }

    //         $qData = [
    //             'subtopic_id'       => $subtopicId,
    //             'question_title'    => trim($question_text),
    //             'answer_a'          => trim($a),
    //             'answer_b'          => trim($b),
    //             'answer_c'          => trim($c),
    //             'answer_d'          => trim($d),
    //             'correct_answer'    => trim($correct),
    //             'difficult_level'   => $difficulty !== '' ? trim($difficulty) : 'Medium',
    //             'question_type'     => $question_type !== '' ? trim($question_type) : 'MCQ',
    //             'hint'              => trim($hint),
    //             'explanation'       => trim($explanation), // NEW FIELD
    //             'image'             => $imagePath,        // NEW FIELD
    //             'question_added_on' => date('Y-m-d H:i:s'),
    //         ];
            
    //         if (!$db->insertFromArray('tbl_quaestion_bank', $qData)) {
    //             $db->rollbackTransaction();
    //             FatUtility::dieJsonError('Error inserting question: ' . $db->getError());
    //         }
    //     }
    // }
    // UPDATED CSV PROCESSING WITH EXPLANATION + IMAGE (URL OR ZIP) SUPPORT
if (!empty($_FILES['quiz_csv']['tmp_name']) && $_FILES['quiz_csv']['error'] === UPLOAD_ERR_OK) {

    // optional ZIP for images (NEW - backward compatible)
    $zipBag = null;
    try {
        $zipBag = $this->initZipBagFromUpload('quiz_images_zip'); // field added in getForm()
    } catch (Exception $e) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError($e->getMessage());
    }

    $uploadDir = 'uploads/question_images/';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

    // robust CSV read
    $csvData = $this->readCsvRows($_FILES['quiz_csv']['tmp_name']);

    foreach ($csvData as $i => $row) {
        if ($i === 0) continue; // header row

        // Keep your current 11-col format fully supported
    // ✅ Support old 11-col CSV + new 16-col CSV
$row = array_pad($row, 16, '');

[
  $question_text, $a, $b, $c, $d,
  $correct, $difficulty, $question_type, $hint, $explanation, $image_url,
  $option_mode, $a_img, $b_img, $c_img, $d_img
] = $row;

$option_mode = strtolower(trim((string)$option_mode));
$option_mode = ($option_mode === 'image') ? 'image' : 'text';

        if (trim($question_text) === '' || trim($correct) === '') {
            continue;
        }

        // NEW: image from URL OR ZIP filename/path
        $imagePath = $this->resolveAndSaveQuestionImage((string)$image_url, $zipBag, $uploadDir);
// ✅ Option images (URL OR ZIP filename) - only if option_mode=image
$optAPath = $optBPath = $optCPath = $optDPath = '';
if ($option_mode === 'image') {
    $optAPath = $this->resolveAndSaveQuestionImage((string)$a_img, $zipBag, $uploadDir);
    $optBPath = $this->resolveAndSaveQuestionImage((string)$b_img, $zipBag, $uploadDir);
    $optCPath = $this->resolveAndSaveQuestionImage((string)$c_img, $zipBag, $uploadDir);
    $optDPath = $this->resolveAndSaveQuestionImage((string)$d_img, $zipBag, $uploadDir);
}

        $qData = [
            'subtopic_id'       => $subtopicId,
            'question_title'    => trim($question_text),
            'answer_a'          => trim($a),
            'answer_b'          => trim($b),
            'answer_c'          => trim($c),
            'answer_d'          => trim($d),
            'correct_answer'    => trim($correct),
            'difficult_level'   => $difficulty !== '' ? trim($difficulty) : 'Medium',
            'question_type'     => $question_type !== '' ? trim($question_type) : 'MCQ',
            'hint'              => trim($hint),
            'explanation'       => trim($explanation),
            'image'             => $imagePath,
            'question_added_on' => date('Y-m-d H:i:s'),
            'option_mode'    => $option_mode,
'answer_a_image' => $optAPath,
'answer_b_image' => $optBPath,
'answer_c_image' => $optCPath,
'answer_d_image' => $optDPath,
        ];

        if (!$db->insertFromArray('tbl_quaestion_bank', $qData)) {
            if ($zipBag) $zipBag->cleanup();
            $db->rollbackTransaction();
            FatUtility::dieJsonError('Error inserting question: ' . $db->getError());
        }
    }

    if ($zipBag) $zipBag->cleanup();
}


    $db->commitTransaction();
    FatUtility::dieJsonSuccess(['msg' => '✅ Subtopic and questions saved successfully!']);
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
            'option_mode',
'answer_a_image',
'answer_b_image',
'answer_c_image',
'answer_d_image',

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
            $sql1 = "DELETE FROM tbl_quaestion_bank WHERE subtopic_id = $quotedValue";
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

    $frm->addTextBox(Label::getLabel('LBL_SUBJECT'),  'subject',  '', ['placeholder' => Label::getLabel('LBL_SEARCH_BY_SUBJECT')]);
    $frm->addTextBox(Label::getLabel('LBL_TOPIC'),    'topic',    '', ['placeholder' => Label::getLabel('LBL_SEARCH_BY_TOPIC')]);
    $frm->addTextBox(Label::getLabel('LBL_SUBTOPIC'), 'subtopic', '', ['placeholder' => Label::getLabel('LBL_SEARCH_BY_SUBTOPIC')]);

    $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
    $frm->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
    $frm->addHiddenField('', 'order_id');

    $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
    $btnSubmit->attachField($frm->addButton('', 'btn_reset', Label::getLabel('LBL_Clear')));
    return $frm;
}
public function questionBank(int $subtopicId)
{
    $subtopicId = FatUtility::int($subtopicId);
    if ($subtopicId <= 0) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    $db = FatApp::getDb();

    // Get subtopic details - FIXED: Use quoteVariable instead of parameter
    $qid = $db->quoteVariable($subtopicId);
    $sql = "SELECT qm.*, qs.topic_name, cs.subject
              FROM tbl_quiz_management qm
        INNER JOIN tbl_quiz_setup qs ON qs.id = qm.quiz_setup_id
        INNER JOIN course_subjects cs ON cs.id = qs.subject_id
             WHERE qm.id = $qid";

    $rs = $db->query($sql);
    $subtopic = $db->fetch($rs);
    
    if (!$subtopic) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    // Rest of your code remains the same...
   $keyword = FatApp::getQueryStringData('keyword', FatUtility::VAR_STRING, '');

/* questions search */
$q = new SearchBase('tbl_quaestion_bank', 'qb');
$q->addCondition('qb.subtopic_id', '=', $subtopicId);

if ($keyword !== '') {
    $q->addCondition('qb.question_title', 'LIKE', '%' . trim($keyword) . '%');
}

$q->addMultipleFields([
  'qb.id','qb.question_title',
  'qb.option_mode',
  'qb.answer_a','qb.answer_b','qb.answer_c','qb.answer_d',
  'qb.answer_a_image','qb.answer_b_image','qb.answer_c_image','qb.answer_d_image',
  'qb.correct_answer','qb.difficult_level','qb.question_type','qb.hint','qb.question_added_on',
  'qb.image','qb.explanation'
]);


    $rs = $q->getResultSet();
    $questions = FatApp::getDb()->fetchAll($rs);
    $srchFrm = $this->getQuestionSearchForm($subtopicId);
$srchFrm->fill(FatApp::getQueryStringData());

    $this->sets([
        'subtopic'  => $subtopic,
        'questions' => $questions,
        'srchFrm'   => $srchFrm,
        'canEdit'   => $this->objPrivilege->canEditCourses(true),
    ]);
    $this->_template->render(true, true, 'coursemanagement/questionbank.php');
}

private function getQuestionSearchForm(int $subtopicId): Form
{
    $frm = new Form('frmQuestionSearch');
    $frm->addHiddenField('', 'subtopic_id', $subtopicId);
    $frm->addTextBox(Label::getLabel('LBL_SEARCH_BY_QUESTION_TITLE'), 'keyword', '');
    $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
    $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear'));
    return $frm;
}

public function subtopicForm(int $id = 0)
{
    $this->objPrivilege->canEditCourses();

    $db = FatApp::getDb();
    $row = null;
    if ($id > 0) {
        $qid = $db->quoteVariable($id);
       $rs  = $db->query("SELECT qm.*, qs.subject_id, qs.examboard_id, qs.tier_id
                     FROM tbl_quiz_management qm
               INNER JOIN tbl_quiz_setup qs ON qs.id = qm.quiz_setup_id
                    WHERE qm.id = $qid"); // rehan
        $row = $db->fetch($rs);
        if (!$row) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
    }

    $frm = $this->getForm($id);

    if ($row) {
        $frm->fill([
            'id'            => $row['id'],
            'subject_id'    => $row['subject_id'],
            'examboard_id'  => $row['examboard_id'] ?? 0, // NEW
        'tier_id'       => $row['tier_id'] ?? 0,      // NEW
            'quiz_setup_id' => $row['quiz_setup_id'],
            'subtopic_name' => $row['subtopic_name'],
            'video_url'     => $row['video_url'],
            // Note: PDF path cannot be pre-filled in file upload field
        ]);
    }

    $this->sets(['frm' => $frm, 'data' => ($row ?? []), 'categoryId' => $id]);
    $this->_template->render(false, false, 'coursemanagement/form.php');
}

public function deleteSubtopic(int $id)
{
    $this->objPrivilege->canEditCourses();
    $db = FatApp::getDb();
    $id = FatUtility::int($id);
    if ($id <= 0) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    }

    $db->startTransaction();
    try {
        // remove questions first
        if (!$db->deleteRecords('tbl_quaestion_bank', ['smt' => 'subtopic_id = ?', 'vals' => [$id]])) {
            throw new Exception($db->getError());
        }
        // then remove subtopic row
        if (!$db->deleteRecords('tbl_quiz_management', ['smt' => 'id = ?', 'vals' => [$id]])) {
            throw new Exception($db->getError());
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    } catch (Exception $e) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError(Label::getLabel('LBL_ERROR_DELETING_RECORD') . ': ' . $e->getMessage());
    }
}
/* ---------- QUESTION FORM (add/edit) ---------- */
public function questionForm(int $subtopicId, int $id = 0)
{
    $this->objPrivilege->canEditCourses();
    $db = FatApp::getDb();

    $subtopicId = FatUtility::int($subtopicId);
    if ($subtopicId <= 0) FatUtility::dieJsonError('Invalid subtopic.');

    $row = [];
    if ($id > 0) {
         $qid = $db->quoteVariable($id);
    $rs  = $db->query("SELECT * FROM tbl_quaestion_bank WHERE id = $qid");
    $row = $db->fetch($rs);

    if (!$row) FatUtility::dieJsonError('Invalid question.');
    }

    // Build form
 // ---- Build form (safe defaults) ----
$frm = new Form('frmQuestion');
$frm->addHiddenField('', 'id', $id);
   $frm->setFormTagAttribute('id', 'frmQuestion');// rehan
$frm->addHiddenField('', 'subtopic_id', $subtopicId);

// ---- Normalize legacy question types (story -> Story-Based etc.) ----
$rawType = strtolower(trim((string)($row['question_type'] ?? '')));

if ($rawType === '' ) {
    $currType = 'Multiple-Choice';
} elseif ($rawType === 'story' || $rawType === 'story-based' || $rawType === 'story_based') {
    $currType = 'Story-Based';
} elseif ($rawType === 'short' || $rawType === 'short-answer' || $rawType === 'short_answer') {
    $currType = 'Short';
} elseif ($rawType === 'mcq' || $rawType === 'multiple-choice' || $rawType === 'multiple choice') {
    $currType = 'Multiple-Choice';
} else {
    // fallback: keep as-is if it already matches one of our options
    $currType = $row['question_type'] ?? 'Multiple-Choice';
}
 // safe default

$frm->addTextBox('Question Title', 'question_title', $row['question_title'] ?? '')->requirements()->setRequired();

/* Use values that we also display elsewhere */
$typeOptions = [
  'Multiple-Choice' => 'Multiple-Choice',
  'Story-Based'     => 'Story-Based',
  'Short'           => 'Short',
];
$frm->addSelectBox('Type', 'question_type', $typeOptions, $currType, [], '')->setFieldTagAttribute('id','question_type');

/* MCQ group */
$frm->addHtml('', '', '<div class="mcq-fields">');
$frm->addTextBox('Option A', 'answer_a', $row['answer_a'] ?? '');
$frm->addTextBox('Option B', 'answer_b', $row['answer_b'] ?? '');
$frm->addTextBox('Option C', 'answer_c', $row['answer_c'] ?? '');
$frm->addTextBox('Option D', 'answer_d', $row['answer_d'] ?? '');
$frm->addSelectBox(
  'Correct Answer (MCQ)',
  'correct_answer',
  ['A'=>'A','B'=>'B','C'=>'C','D'=>'D'],
  $row['correct_answer'] ?? ''
);
$frm->addFileUpload('Option A Image', 'answer_a_image_file', ['accept'=>'.jpg,.jpeg,.png,.webp,.gif']);
$frm->addFileUpload('Option B Image', 'answer_b_image_file', ['accept'=>'.jpg,.jpeg,.png,.webp,.gif']);
$frm->addFileUpload('Option C Image', 'answer_c_image_file', ['accept'=>'.jpg,.jpeg,.png,.webp,.gif']);
$frm->addFileUpload('Option D Image', 'answer_d_image_file', ['accept'=>'.jpg,.jpeg,.png,.webp,.gif']);

$frm->addHiddenField('', 'existing_answer_a_image', $row['answer_a_image'] ?? '');
$frm->addHiddenField('', 'existing_answer_b_image', $row['answer_b_image'] ?? '');
$frm->addHiddenField('', 'existing_answer_c_image', $row['answer_c_image'] ?? '');
$frm->addHiddenField('', 'existing_answer_d_image', $row['answer_d_image'] ?? '');

$frm->addHtml('', '', '</div>');
// ✅ Options type selector (only for MCQ UI; we’ll hide via JS when not MCQ)
$mode = $row['option_mode'] ?? 'text';
$frm->addSelectBox(
  'Options Type',
  'option_mode',
  ['text' => 'Text', 'image' => 'Images'],
  $mode
)->setFieldTagAttribute('id', 'option_mode');

$frm->addTextArea(
  'Correct Answer (Story/Short)',
  'correct_answer_text',
  ($row['question_type'] ?? '') === 'Multiple-Choice' ? '' : ($row['correct_answer'] ?? ''),
  ['rows' => 3]
);
/* Text answer for Story/Short */
// $frm->addHtml('', '', '<div class="text-answer-field">');
// $frm->addTextArea('Answer (text)', 'answer_text', ($currType === 'Multiple-Choice') ? '' : ($row['answer_a'] ?? ''), ['rows'=>3]);
// $frm->addHtml('', '', '</div>');

$frm->addSelectBox('Difficulty', 'difficult_level', ['Easy'=>'Easy','Medium'=>'Medium','Hard'=>'Hard'], $row['difficult_level'] ?? 'Medium');
$frm->addTextBox('Hint (optional)', 'hint', $row['hint'] ?? '');
$frm->addTextArea('Explanation (optional)', 'explanation', $row['explanation'] ?? '', ['rows'=>3]);
$frm->addFileUpload('Image (optional)', 'image', ['accept'=>'.jpg,.jpeg,.png,.gif']);
$frm->addHiddenField('', 'existing_image', $row['image'] ?? '');
$frm->addSubmitButton('', 'btn_submit', ($id>0?'Update':'Add') . ' Question');

/* Render without layout/header so no RWU logo appears inside the modal */
$this->sets(['frm'=>$frm, 'q'=>$row, 'subtopicId'=>$subtopicId]);
$this->_template->render(false, false, 'coursemanagement/question_form.php');

}

/* ---------- QUESTION SAVE ---------- */
public function saveQuestion()
{
    $this->objPrivilege->canEditCourses();
    $db   = FatApp::getDb();
    $post = FatApp::getPostedData();

    $id         = FatUtility::int($post['id'] ?? 0);
    $subtopicId = FatUtility::int($post['subtopic_id'] ?? 0);
    if ($subtopicId <= 0) FatUtility::dieJsonError('Invalid subtopic.');

    // base payload first
    $data = [
        'subtopic_id'     => $subtopicId,
        'question_title'  => trim($post['question_title'] ?? ''),
        'answer_a'        => trim($post['answer_a'] ?? ''),
        'answer_b'        => trim($post['answer_b'] ?? ''),
        'answer_c'        => trim($post['answer_c'] ?? ''),
        'answer_d'        => trim($post['answer_d'] ?? ''),
        'correct_answer'  => trim($post['correct_answer'] ?? ''),
        'difficult_level' => trim($post['difficult_level'] ?? 'Medium'),
        'question_type'   => trim($post['question_type'] ?? 'Multiple-Choice'),
        'hint'            => trim($post['hint'] ?? ''),
        'explanation'     => trim($post['explanation'] ?? ''),
        'option_mode' => trim($post['option_mode'] ?? 'text'),

    ];
    $data['option_mode'] = strtolower(trim((string)$data['option_mode']));
$data['option_mode'] = ($data['option_mode'] === 'image') ? 'image' : 'text';

    if ($data['question_title'] === '') {
        FatUtility::dieJsonError('Question is required.');
    }

    // normalize type and map text answers for non-MCQ
    $type = $data['question_type'];
    if (stripos($type, 'multiple') !== false || stripos($type, 'mcq') !== false) {
        // MCQ – all four options + correct are required
     if ($data['correct_answer'] === '') {
    FatUtility::dieJsonError('Please select the correct answer for Multiple-Choice.');
}

if ($data['option_mode'] === 'text') {
    if ($data['answer_a']==='' || $data['answer_b']==='' || $data['answer_c']==='' || $data['answer_d']==='') {
        FatUtility::dieJsonError('Please fill all options for Multiple-Choice (text mode).');
    }
} else {
    // image mode: require images (upload OR existing)
    $hasA = !empty($_FILES['answer_a_image_file']['tmp_name']) || !empty($post['existing_answer_a_image']);
    $hasB = !empty($_FILES['answer_b_image_file']['tmp_name']) || !empty($post['existing_answer_b_image']);
    $hasC = !empty($_FILES['answer_c_image_file']['tmp_name']) || !empty($post['existing_answer_c_image']);
    $hasD = !empty($_FILES['answer_d_image_file']['tmp_name']) || !empty($post['existing_answer_d_image']);

    if (!$hasA || !$hasB || !$hasC || !$hasD) {
        FatUtility::dieJsonError('Please upload all 4 option images for Multiple-Choice (image mode).');
    }

    // optional: clear text options to avoid confusion
    $data['answer_a'] = $data['answer_b'] = $data['answer_c'] = $data['answer_d'] = '';
}

        // ensure correct answer is one of A-D
        if (!in_array($data['correct_answer'], ['A','B','C','D'], true)) {
            FatUtility::dieJsonError('Correct answer must be A, B, C or D.');
        }
        $data['question_type'] = 'Multiple-Choice';
   } else {
    // Story/Short – ONLY keep correct answer text, clear options
    $textAns = trim($post['correct_answer_text'] ?? '');
    if ($textAns === '') {
        FatUtility::dieJsonError('Please provide the correct answer for Story/Short.');
    }

    $data['answer_a'] = '';
    $data['answer_b'] = '';
    $data['answer_c'] = '';
    $data['answer_d'] = '';

    $data['correct_answer'] = $textAns; // ✅ store ONLY here (matches CSV behavior)

    if (stripos($type, 'story') !== false) $data['question_type'] = 'Story-Based';
    elseif (stripos($type, 'short') !== false) $data['question_type'] = 'Short';
}


    // optional image
    $imagePath = $post['existing_image'] ?? '';
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
            FatUtility::dieJsonError('Only JPG/PNG/GIF allowed.');
        }
        $uploadDir = 'uploads/question_images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $imagePath = $uploadDir . uniqid('qimg_') . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            FatUtility::dieJsonError('Image upload failed.');
        }
    }
    if ($imagePath) $data['image'] = $imagePath;
    // ===== Option images upload (MCQ image mode) =====
$uploadDir = 'uploads/question_images/';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }

$allowed = ['jpg','jpeg','png','webp','gif'];

function rwuUploadOpt(string $fileKey, string $existingKey, string $uploadDir, array $allowed): string {
    $existing = FatApp::getPostedData($existingKey, FatUtility::VAR_STRING, '');
    if (!empty($_FILES[$fileKey]['tmp_name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            FatUtility::dieJsonError('Only JPG/JPEG/PNG/WEBP/GIF allowed for option images.');
        }
        $path = $uploadDir . uniqid('opt_') . '.' . $ext;
        if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $path)) {
            FatUtility::dieJsonError('Option image upload failed.');
        }
        return $path;
    }
    return $existing;
}

if (($data['question_type'] ?? '') === 'Multiple-Choice' && ($data['option_mode'] ?? 'text') === 'image') {
    $data['answer_a_image'] = rwuUploadOpt('answer_a_image_file', 'existing_answer_a_image', $uploadDir, $allowed);
    $data['answer_b_image'] = rwuUploadOpt('answer_b_image_file', 'existing_answer_b_image', $uploadDir, $allowed);
    $data['answer_c_image'] = rwuUploadOpt('answer_c_image_file', 'existing_answer_c_image', $uploadDir, $allowed);
    $data['answer_d_image'] = rwuUploadOpt('answer_d_image_file', 'existing_answer_d_image', $uploadDir, $allowed);
} else {
    // if switching back to text mode, keep DB clean
    $data['answer_a_image'] = $post['existing_answer_a_image'] ?? '';
    $data['answer_b_image'] = $post['existing_answer_b_image'] ?? '';
    $data['answer_c_image'] = $post['existing_answer_c_image'] ?? '';
    $data['answer_d_image'] = $post['existing_answer_d_image'] ?? '';
}


    if ($id > 0) {
        if (!$db->updateFromArray('tbl_quaestion_bank', $data, ['smt'=>'id = ?', 'vals'=>[$id]])) {
            FatUtility::dieJsonError($db->getError());
        }
    } else {
        $data['question_added_on'] = date('Y-m-d H:i:s');
        if (!$db->insertFromArray('tbl_quaestion_bank', $data)) {
            FatUtility::dieJsonError($db->getError());
        }
    }
    FatUtility::dieJsonSuccess(['status'=>1,'msg'=>'Saved']);
}


/* ---------- QUESTION DELETE ---------- */
public function deleteQuestion(int $id)
{
    $this->objPrivilege->canEditCourses();
    $db = FatApp::getDb();
    $id = FatUtility::int($id);
    if ($id <= 0) FatUtility::dieJsonError('Invalid request');

    if(!$db->deleteRecords('tbl_quaestion_bank', ['smt'=>'id = ?', 'vals'=>[$id]])) {
        FatUtility::dieJsonError($db->getError());
    }
    FatUtility::dieJsonSuccess(['status'=>1,'msg'=>'Deleted']);
}

private function initZipBagFromUpload(string $fieldName): ?ZipImageBag
{
    if (empty($_FILES[$fieldName]['tmp_name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if ($ext !== 'zip') {
        throw new Exception('Invalid ZIP file. Please upload a .zip.');
    }

    // adjust path to wherever you added ZipImageBag.php
    require_once CONF_APPLICATION_PATH . 'library/ZipImageBag.php';

    return new ZipImageBag($_FILES[$fieldName]['tmp_name']);
}

/**
 * Backwards compatible image handling:
 * - If imageRef is URL => download (your existing behavior)
 * - Else if zipBag exists => resolve file from zip and save
 * - Else => empty
 */
private function resolveAndSaveQuestionImage(string $imageRef, ?ZipImageBag $zipBag, string $uploadDir): string
{
    $imageRef = trim($imageRef);
    if ($imageRef === '') return '';

    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }

    // 1) URL (existing behavior)
    if (preg_match('~^https?://~i', $imageRef)) {
        $imageContent = @file_get_contents($imageRef);
        if ($imageContent === false) return '';

        $ext = strtolower(pathinfo(parse_url($imageRef, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg');
        if (!in_array($ext, ['jpg','jpeg','png','webp','gif'], true)) $ext = 'jpg';

        $imageName = uniqid('qimg_') . '.' . $ext;
        $imagePath = rtrim($uploadDir, '/\\') . '/' . $imageName;
        @file_put_contents($imagePath, $imageContent);
        return $imagePath;
    }

    // 2) ZIP filename/path
    if ($zipBag) {
        try {
            $extracted = $zipBag->resolve($imageRef);
        } catch (Exception $e) {
            // Make it a proper user-facing validation error
            throw new Exception($e->getMessage());
        }

        if ($extracted) {
            $ext = strtolower(pathinfo($extracted, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp','gif'], true)) return '';

            $imageName = uniqid('qimg_') . '.' . $ext;
            $imagePath = rtrim($uploadDir, '/\\') . '/' . $imageName;

            if (!@copy($extracted, $imagePath)) return '';
            return $imagePath;
        }
    }

    return '';
}


/**
 * More robust CSV reader than file()+str_getcsv:
 * supports quoted newlines and BOM.
 */
private function readCsvRows(string $tmpPath): array
{
    $rows = [];
    $fh = fopen($tmpPath, 'rb');
    if (!$fh) return $rows;

    $first = true;
    while (($row = fgetcsv($fh)) !== false) {
        if ($first && isset($row[0])) {
            // strip UTF-8 BOM if present
            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            $first = false;
        }
        // skip empty lines
        if (count($row) === 1 && trim((string)$row[0]) === '') continue;
        $rows[] = $row;
    }
    fclose($fh);
    return $rows;
}


}

