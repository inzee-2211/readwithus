<?php

class QuizizzController extends MyAppController
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

 public function index()
{
    $db = FatApp::getDb();

    /**
     * 1) Read setup IDs from querystring
     *    - preferred: setup_ids=30,31,32
     *    - fallback:  setup_id=30 (old behaviour)
     */
    $setupIdsParam = FatApp::getQueryStringData('setup_ids', FatUtility::VAR_STRING, '');
    $setupIds      = array_filter(array_map('intval', explode(',', (string)$setupIdsParam)));

    if (empty($setupIds)) {
        $singleSetupId = FatApp::getQueryStringData('setup_id', FatUtility::VAR_INT, 0);
        if ($singleSetupId > 0) {
            $setupIds = [(int)$singleSetupId];
        }
    }

    if (empty($setupIds)) {
        // nothing to resolve → 404
        FatUtility::exitWithErrorCode(404);
    }

    /**
     * 2) Fetch ALL matching setup rows (topics)
     *    We also join levels/subjects/etc to get display names.
     */
    $idsSql = implode(',', array_map('intval', $setupIds));

    $setupSql = "
        SELECT 
            qs.id,
            qs.topic_name,
            qs.level_id,
            qs.subject_id,
            qs.examboard_id,
            qs.tier_id,
            qs.year_id,

            lvl.level_name      AS level_name,
            subj.subject        AS subject_name,
            eb.name             AS examboard_name,
            tr.name             AS tier_name,
            yr.name             AS year_name
        FROM tbl_quiz_setup qs
        LEFT JOIN course_levels     lvl   ON lvl.id  = qs.level_id
        LEFT JOIN course_subjects   subj  ON subj.id = qs.subject_id
        LEFT JOIN course_examboards eb    ON eb.id   = qs.examboard_id
        LEFT JOIN course_tier       tr    ON tr.id   = qs.tier_id
        LEFT JOIN course_year       yr    ON yr.id   = qs.year_id
        WHERE qs.id IN ($idsSql)
        ORDER BY qs.topic_name ASC
    ";

    $setupRs  = $db->query($setupSql);
    $setups   = $db->fetchAll($setupRs);

    if (empty($setups)) {
        FatUtility::exitWithErrorCode(404);
    }

    // Use the FIRST setup row as the “anchor” for header + session
    $firstSetup = reset($setups);

    $subjectId   = (int)$firstSetup['subject_id'];
    $examboardId = (int)$firstSetup['examboard_id'];
    $yearId      = (int)$firstSetup['year_id'];

    $subjectName   = (string)($firstSetup['subject_name']   ?? 'Subject');
    $levelName     = (string)($firstSetup['level_name']     ?? '');
    $examboardName = (string)($firstSetup['examboard_name'] ?? '');
    $tierName      = (string)($firstSetup['tier_name']      ?? '');
    $yearName      = (string)($firstSetup['year_name']      ?? '');
    $topicTitle    = (string)($firstSetup['topic_name']     ?? 'Topic'); // kept for backwards compatibility

    /**
     * 3) Session values used by the header / other places
     */
    $_SESSION['setupId']     = (int)$firstSetup['id'];   // keep old behaviour
    $_SESSION['subjectId']   = $subjectId;
    $_SESSION['subjectName'] = $subjectName;

    /**
     * 4) Search form (unchanged)
     */
    $srchFrm = CourseSearch::getSearchForm($this->siteLangId);
    $srchFrm->fill([]);
    unset($_SESSION[AppConstant::SEARCH_SESSION]);

    /**
     * 5) Fetch all subtopics for ALL these setups in one go
     *    from tbl_quiz_management.
     */
    $mgmtSql = "
        SELECT 
            quiz_setup_id,
            id,
            subtopic_name,
            video_url,
            pdf_path,
            answer_pdf_path
        FROM tbl_quiz_management
        WHERE quiz_setup_id IN ($idsSql)
        ORDER BY quiz_setup_id ASC, position ASC, id ASC
    ";
    $mgmtRs   = $db->query($mgmtSql);
    $mgmtRows = $mgmtRs ? $db->fetchAll($mgmtRs) : [];

    // Group subtopics by quiz_setup_id
    $subtopicsBySetup = [];
    foreach ($mgmtRows as $row) {
        $sid = (int)$row['quiz_setup_id'];
        if (!isset($subtopicsBySetup[$sid])) {
            $subtopicsBySetup[$sid] = [];
        }
        $subtopicsBySetup[$sid][] = [
            'id'                 => (int)$row['id'],
            'name'               => $row['subtopic_name'],
            'video_url'          => $row['video_url'],
            'previous_paper_pdf' => $row['pdf_path'],                 // question paper
            'answer_pdf_path'    => $row['answer_pdf_path'] ?? '',    // answer paper
        ];
    }

    /**
     * 6) Build topics array for the view
     *    Each "topic" = one row in tbl_quiz_setup with its own subtopics.
     */
    $topics = [];
    foreach ($setups as $sRow) {
        $sid = (int)$sRow['id'];
        $topics[] = [
            'setup_id'   => $sid,
            'topic_name' => $sRow['topic_name'],
            'subtopics'  => $subtopicsBySetup[$sid] ?? [],
        ];
    }

    // For backwards compatibility with your existing view which expects $subtopics:
    $currentSubtopics = [];
    if (!empty($topics)) {
        $currentSubtopics = $topics[0]['subtopics'];  // first topic’s subtopics
    }

    /**
     * 7) Pass everything to the template
     */
    $this->set('srchFrm',        $srchFrm);
    $this->set('setupId',        (int)$firstSetup['id']); // primary id
    $this->set('topics',         $topics);    
    $this->set('setupIdsParam',  $setupIdsParam);             // NEW: all topics + subtopics
    $this->set('subtopics',      $currentSubtopics);      // legacy single-topic data
    $this->set('topicTitle',     $topicTitle);
    $this->set('examboardId',    $examboardId);
    $this->set('yearId',         $yearId);

    $this->set('levelName',      $levelName);
    $this->set('examboardName',  $examboardName);
    $this->set('tierName',       $tierName);
    $this->set('yearName',       $yearName);

    $this->set('filterTypes',    Course::getFilterTypes());

    $this->_template->render();
}


    private function getCourseIdBySubtopic($subtopicId) {
    $db = FatApp::getDb();
    $query = "SELECT course_id FROM course_subtopics WHERE id = " . (int)$subtopicId;
    $result = $db->fetch($db->query($query));
    return $result['course_id'] ?? 0;
    }


    public function submitSignup()
    {

        $post = FatApp::getPostedData();

        $name = trim($post['full_name'] ?? '');
        $email = trim($post['email'] ?? '');
        $parentEmail = trim($post['parent_email'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $subtopic_id = trim($post['subtopic_id'] ?? '');
        if (!$name || !$email || !$parentEmail || !$phone) {
            FatUtility::dieJsonError('All fields are required.');
        }

        


        $db = FatApp::getDb();

        // Check if email OR parent_email OR phone already exists
        $srch = new SearchBase('course_attempt_userdetails');
        $cnd = $srch->addCondition('email', '=', $email);
        $cnd->attachCondition('parent_email', '=', $parentEmail, 'OR');
        $cnd->attachCondition('phone', '=', $phone, 'OR');
        $srch->addFld('id'); // Or any existing field
        $rs = $srch->getResultSet();

        if ($row = FatApp::getDb()->fetch($rs)) {


            $_SESSION['quiz_user'] = [
                'id' => $row['id'],
                'name' => $name,
                'email' => $email,
                'parent_email' => $parentEmail,
                'phone' => $phone
            ];

            //FatUtility::dieJsonError('A user with the same email, parent email, or phone already exists.');
            echo json_encode(['status' => 1, 'subtopicid' => $subtopic_id, 'msg' => 'Success']);
            exit;
        }



        // Insert new record
        $dataToInsert = [
            'name'    => $name,
            'email'        => $email,
            'parent_email' => $parentEmail,
            'phone'        => $phone,
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $success = $db->insertFromArray('course_attempt_userdetails', $dataToInsert);

        $newId = $db->getInsertId();

        // Store in session
        $_SESSION['quiz_user'] = [
            'id' => $newId,
            'name' => $name,
            'email' => $email,
            'parent_email' => $parentEmail,
            'phone' => $phone
        ];

        echo json_encode(['status' => 1, 'subtopicid' => $subtopic_id, 'msg' => 'Success']);
        exit;
        // FatUtility::dieJsonSuccess('Form submitted successfully.');
    }


public function submitfindatutor()
{
    // Disable error display so notices/warnings don't break JSON
    ini_set('display_errors', 0);
    error_reporting(0);

    $post = FatApp::getPostedData();

    $name = trim($post['full_name'] ?? '');
    $email = trim($post['email'] ?? '');
    $parentEmail = trim($post['parent_email'] ?? '');
    $phone = trim($post['phone'] ?? '');
    $subject = trim($post['subject'] ?? '');
    $preferred_time = trim($post['preferred_time'] ?? '');
    $subtopic_id = trim($post['subtopic_id'] ?? '');

    if (!$name || !$email || !$parentEmail || !$phone || !$subject || !$preferred_time) {
        FatUtility::dieJsonError('All fields are required.');
    }

    $db = FatApp::getDb();

    $dataToInsert = [
        'name'           => $name,
        'email'          => $email,
        'parent_email'   => $parentEmail,
        'phone'          => $phone,
        'subject'        => $subject,
        'preferred_time' => $preferred_time,
        'created_at'     => date('Y-m-d H:i:s'),
    ];

    $success = $db->insertFromArray('course_findatutor', $dataToInsert);

    $newId = $db->getInsertId();

    // Set proper JSON header
    header('Content-Type: application/json');

    echo json_encode([
        'status'     => 1,
        'insertedid' => $newId,
        'msg'        => 'Thank you! We’ve received your form and will get back to you shortly.',
        'subtopicid' => $subtopic_id, // if needed
    ]);
    exit;
}

    public function getSubtopicIdByName($subtopicName)
    {
        $db = FatApp::getDb();
        $subjectId = 0;

        $subtopicName = trim($subtopicName);  // Trim any extra spaces
        $subtopicName = addslashes($subtopicName);  // Escape special characters (optional)


        $query = "SELECT id FROM course_topics WHERE topic = '$subtopicName' AND subject_id = $subjectId LIMIT 1";

        $result = $db->query($query);

        if (!$result) {
            echo "Error executing query: " . $db->errorInfo();
            die();
        }
        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetch($result);  // Assuming fetch returns a single row
        }

        return !empty($subtopic) ? $subtopic['id'] : null;
    }

    public function getSubjectNameById($subtopicId)
    {
        $db = FatApp::getDb();
        $subjectId = 0;

        $subtopicId = trim($subtopicId);  
        $subtopicId = addslashes($subtopicId);  


        $query = "SELECT id,subject FROM course_subjects WHERE   id = $subtopicId LIMIT 1";

        $result = $db->query($query);

        if (!$result) {
            echo "Query failed in getSubjectNameById(), possible invalid ID = " . $subtopicId;
        die();
        }
        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetch($result);  
        }
        return !empty($subtopic) ? $subtopic['subject'] : null;
    }


  private function getTopicnames($examboardId = 0, $yearId = 0)
{
    $db = FatApp::getDb();

    if ($examboardId <= 0 && $yearId <= 0) return [];

    $params = [];
    $sql = "SELECT id, topic FROM course_topics WHERE 1=1";
    if ($examboardId > 0) { $sql .= " AND examboard_id = ?"; $params[] = $examboardId; }
    elseif ($yearId > 0) { $sql .= " AND year_id = ?";      $params[] = $yearId; }

    $sql .= " ORDER BY topic ASC";

    $res = $db->query($sql, $params);
    if (!$res) return [];

    $topics = [];
    foreach ($db->fetchAll($res) as $row) {
        $topics[(int)$row['id']] = $row['topic'];
    }
    return $topics;
}




    public function getPreviouspapers($courseId)
    {
        $db = FatApp::getDb();
        // echo "Running getPreviouspapers for courseId: " . $courseId . "<br>";

        $query = "SELECT id, previous_paper_pdf FROM course_subtopics WHERE course_id = " . (int)$courseId;
        // echo "Query: $query <br>";

        $result = $db->query($query);

        if (!$result) {
            echo "Query execution failed.<br>";
            die();
        }

        $papers = $db->fetchAll($result);
        // echo "<pre>";
        // print_r($papers);
        // echo "</pre>";
        return $papers;
    }








    /**
     * Find Teachers
     */
    public function search()
    {

        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $posts['price_sorting'] = FatApp::getPostedData('price_sorting', FatUtility::VAR_INT, AppConstant::SORT_PRICE_ASC);
        $frm = CourseSearch::getSearchForm($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray($posts, ['course_cate_id'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $post['course_status'] = Course::PUBLISHED;

        $offset = ($posts['pageno'] - 1) * $posts['pagesize'];
        $db = FatApp::getDb();
        $query = "SELECT q.video_url, q.previous_paper_pdf,t.tier,t.type,t.examBoards
                  FROM course_subtopics q 
                  INNER JOIN tbl_course_management t ON t.id = q.course_id ";


        $query .= " LIMIT $offset, {$posts['pagesize']}";
        $result = $db->query($query, [AppConstant::ACTIVE]);
        $quizzes = [];
        if ($result) {
            $quizzes = $db->fetchAll($result); // Get quizzes as an array
        }
        $countQuery = "SELECT COUNT(*) AS total FROM course_subtopics";
        $countResult = $db->query($countQuery, [AppConstant::ACTIVE]);
        $totalCount = 0;
        if ($countResult) {
            $countRow = $db->fetch($countResult);
            $totalCount = $countRow['total'] ?? 0;
        }
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $checkoutForm = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $checkoutForm->fill(['order_type' => Order::TYPE_COURSE]);

        $this->sets([
            'post' => $post,
            'courses' => $quizzes,
            'recordCount' => $totalCount,
            'pageCount' => ceil($totalCount / $posts['pagesize']),
            'levels' => Course::getCourseLevels(),
            'types' => Course::getTypes(),
            'checkoutForm' => $checkoutForm
        ]);

        $this->_template->render(false, false);
    }

    public function submitAnswers()
    {
        $answersJson = FatApp::getPostedData('answers');
        $subtopicId = FatApp::getPostedData('subtopicid');

        // Convert JSON string to PHP array
        $answers = json_decode($answersJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            FatUtility::dieJsonError("Invalid answer data.");
        }

      


        $api_key = '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';


        foreach ($answers as $item) {
            $questionId = $item['questionId'];
            $userAnswer = $item['answer'];

            // Fetch question data including type and marks
            $srch = new SearchBase('tbl_quaestion_bank');
            $srch->addCondition('id', '=', $questionId);
            $srch->addMultipleFields(['question_title', 'correct_answer', 'question_type']);
            $rs = $srch->getResultSet();
            $question = FatApp::getDb()->fetch($rs);

            if (!$question) continue;

            $questionType = $question['question_type'];
            // $questionMarks = (float) $question['marks'];
            $questionMarks = 2;
            $questionTitle = $question['question_title'];
            $correctAnswer = $question['correct_answer'];

            if ($questionType === 'Story-Based') {
                // === ChatGPT Grading ===
                $prompt = "You are a teacher grading a student's answer. ...";
                $prompt = str_replace(
                    ['{question}', '{student_answer}', '{marks}'],
                    [$questionTitle, $userAnswer, $questionMarks],
                    $prompt
                );

                $data = [
                    "model" => "gpt-3.5-turbo",
                    "messages" => [
                        ["role" => "system", "content" => "You are a helpful assistant and an expert teacher."],
                        ["role" => "user", "content" => $prompt]
                    ],
                    "temperature" => 0.0,
                    "max_tokens" => 300
                ];

                $ch = curl_init('https://api.openai.com/v1/chat/completions');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $api_key
                    ],
                ]);

                $response = curl_exec($ch);
                curl_close($ch);

                $responseData = json_decode($response, true);

                $obtainedMarks = 0;
                $explanation = 'No explanation provided';
                $isCorrect = false;

                if (isset($responseData['choices'][0]['message']['content'])) {
                    $gptResponse = $responseData['choices'][0]['message']['content'];

                    // Extract marks
                    preg_match('/\b([\d\.]+)\s*\/\s*' . preg_quote($questionMarks, '/') . '\b/', $gptResponse, $matches);
                    $obtainedMarks = isset($matches[1]) ? floatval($matches[1]) : 0;

                    // Explanation
                    preg_match('/Explanation:\s*(.*)/s', $gptResponse, $explanationMatches);
                    $explanation = isset($explanationMatches[1]) ? trim($explanationMatches[1]) : $explanation;

                    $isCorrect = $obtainedMarks > 0;
                }

                $results[] = [
                    'questionId' => $questionId,
                    'userAnswer' => $userAnswer,
                    'correctAnswer' => null,
                    'isCorrect' => $isCorrect,
                    'marksObtained' => $obtainedMarks,
                    'explanation' => $explanation,
                ];
            } else {
                // === MCQ Logic ===
                $correctArray = explode(',', $correctAnswer);
                $userArray = is_array($userAnswer) ? $userAnswer : [$userAnswer];

                sort($correctArray);
                sort($userArray);

                $isCorrect = ($correctArray === $userArray);
                $marksObtained = $isCorrect ? $questionMarks : 0;

                $results[] = [
                    'questionId' => $questionId,
                    'userAnswer' => $userArray,
                    'correctAnswer' => $correctArray,
                    'isCorrect' => $isCorrect,
                    'marksObtained' => $marksObtained,
                    'explanation' => '',
                ];
            }
        }


        $totalCorrect = 0;
        $totalMarks = 0;
        $marksObtained = 0;

        // Store results after loop


        if (is_array($results) && count($results) > 0) {
            foreach ($results as $res) {
                $totalMarks += isset($res['marksObtained']) ? $res['marksObtained'] : 0;
                if ($res['isCorrect']) {
                    $totalCorrect++;
                }
            }
        }
        $totalQuestions = count($results);

        $passingPercentage = 40;
        $tm = $totalQuestions * $questionMarks;
        $percentage = ($totalMarks / $tm) * 100;

        $resultStatus = $percentage >= $passingPercentage ? 'pass' : 'fail';
        $db = FatApp::getDb();

        $quizAttemptData = [
            'user_id' => $this->siteUserId,
            'subtopic_id' => $subtopicId,
            'total_questions' => $totalQuestions,
            'total_correct' => $totalCorrect,
            'total_marks' => $totalQuestions * $questionMarks, // if uniform
            'marks_obtained' => $totalMarks,
            'result' => $resultStatus
        ];

        if (!$db->insertFromArray('tbl_quiz_attempts', $quizAttemptData)) {
            dieWithError('Failed to insert quiz attempt');
        }

        $attemptId = $db->getInsertId(); // You’ll need this to link answers

        foreach ($results as $res) {
            $answerData = [
                'attempt_id' => $attemptId,
                'question_id' => $res['questionId'],
                'user_answer' => is_array($res['userAnswer']) ? implode(',', $res['userAnswer']) : $res['userAnswer'],
                'correct_answer' => is_array($res['correctAnswer']) ? implode(',', $res['correctAnswer']) : (string)$res['correctAnswer'],
                'marks_obtained' => $res['marksObtained'],
                'is_correct' => $res['isCorrect'] ? 1 : 0,
            ];

            if (!$db->insertFromArray('tbl_quiz_attempt_answers', $answerData)) {
                dieWithError('Failed to insert answer for question ID: ' . $res['questionId']);
            }
        }


        FatUtility::dieJsonSuccess([
            'message' => 'Quiz submitted successfully!',
            'success' => 123, // if you have results to show
            'status' => $resultStatus,
            'marksObtained' => $totalMarks,
            'totalMarks' => $totalQuestions * $questionMarks,
        ]);
    }

    public function getQuestions()
    {
        $posts = FatApp::getPostedData(); // Fetch input data from AJAX request
        $subtopic = isset($_GET['subtopic']) ? $_GET['subtopic'] : '';

        $subtopicId = isset($posts['subtopicid']) ? (int)$posts['subtopicid'] : 0;

        $db = FatApp::getDb();
        $query = "SELECT * FROM tbl_quaestion_bank WHERE subtopic_id = " . $subtopicId . " ORDER BY RAND() LIMIT 15";
        echo $query;

        //  $query = "SELECT * FROM tbl_quaestion_bank  ORDER BY id desc LIMIT 5";

        $result = $db->query($query);

        if ($result) {
            $quizzes = $db->fetchAll($result);


            $formattedQuestions = [];
            foreach ($quizzes as $quiz) {
                $formattedQuestions[] = [
                    "id" => $quiz['id'],
                    "text" => $quiz['question_title'],
                    "type" => $quiz['question_type'],
                    "options" => array_values(array_filter([
                        $quiz['answer_a'],
                        $quiz['answer_b'],
                        $quiz['answer_c'],
                        $quiz['answer_d']
                    ])),
                    "answer" => explode(",", $quiz['correct_answer']), // Convert CSV string to array
                    "hint" => $quiz['hint'],
                    "explanation" => $quiz['explanation']
                ];
            }
            FatUtility::dieJsonSuccess([
                'success' => true,
                'data' => $formattedQuestions
            ]);
        } else {
            FatUtility::dieJsonError("No questions found.");
        }
    }

    public function getQuizizzList()
    {
        // Fetch posted data
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1; // Default to page 1 if not provided
        $posts['pagesize'] = AppConstant::PAGESIZE; // Default page size from constant
        $posts['price_sorting'] = FatApp::getPostedData('price_sorting', FatUtility::VAR_INT, AppConstant::SORT_PRICE_ASC);

        // Set default condition for quiz status
        $post['quiz_status'] = Quiz::PUBLISHED;

        // Prepare pagination variables
        $offset = ($posts['pageno'] - 1) * $posts['pagesize'];

        $db = FatApp::getDb();
        $query = "SELECT q.quiz_id, q.quiz_name, q.quiz_price, q.quiz_level, q.quiz_type, t.user_username 
              FROM tbl_quizzes q 
              LEFT JOIN tbl_teachers t ON t.user_id = q.quiz_teacher_id 
              WHERE q.quiz_status = ?";

        // Add price sorting if provided
        if ($posts['price_sorting'] == AppConstant::SORT_PRICE_DESC) {
            $query .= " ORDER BY q.quiz_price DESC";
        } else {
            $query .= " ORDER BY q.quiz_price ASC";
        }

        // Add pagination
        $query .= " LIMIT $offset, {$posts['pagesize']}";

        // Prepare and execute the query
        $result = $db->query($query, [AppConstant::ACTIVE]);

        // Fetch quizzes and process them
        $quizzes = [];
        if ($result) {
            $quizzes = $db->fetchAll($result); // Get quizzes as an array

            // Now, for each quiz, get the count of associated questions
            foreach ($quizzes as &$quiz) {
                $quizId = $quiz['quiz_id'] ?? null;

                if (!empty($quizId)) {
                    // Query to count questions for each quiz
                    $questionQuery = "SELECT COUNT(*) AS question_count FROM tbl_questions WHERE question_quiz_id = ?";
                    $questionResult = $db->query($questionQuery, [$quizId]);

                    if ($questionResult) {
                        $questionRow = $db->fetch($questionResult);
                        $quiz['question_count'] = $questionRow['question_count'] ?? 0;
                    } else {
                        $quiz['question_count'] = 0;
                    }
                } else {
                    $quiz['question_count'] = 0;
                }
            }
        }

        // Get the total record count (for pagination)
        $countQuery = "SELECT COUNT(*) AS total FROM tbl_quizzes WHERE quiz_status = ?";
        $countResult = $db->query($countQuery, [AppConstant::ACTIVE]);
        $totalCount = 0;
        if ($countResult) {
            $countRow = $db->fetch($countResult);
            $totalCount = $countRow['total'] ?? 0;
        }

        // Return the response in the required format
        $this->sets([
            'quizzes' => $quizzes,
            'recordCount' => $totalCount,
            'pageCount' => ceil($totalCount / $posts['pagesize']),
        ]);

        // Render the response without using a specific template
        $this->_template->render(false, false);
    }


    /**
     * View course detail
     *
     * @param string $slug
     * @return void
     */
    public function view(string $slug)
    {
        if (empty($slug)) {
            FatUtility::exitWithErrorCode(404);
        }
        /* get course details */
        $srch = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $srch->addSearchDetailFields();
        $srch->applyPrimaryConditions();
        $srch->addCondition('course_slug', '=', $slug);
        $srch->joinTable(TeacherStat::DB_TBL, 'INNER JOIN', 'testat.testat_user_id = teacher.user_id', 'testat');
        $srch->joinTable(
            User::DB_TBL_LANG,
            'LEFT JOIN',
            'userlang.userlang_user_id = teacher.user_id AND userlang.userlang_lang_id = ' . $this->siteLangId,
            'userlang'
        );
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('teacher.user_username', '!=', "");
        $srch->setPageSize(1);
        $courses = $srch->fetchAndFormat(true);
        if (empty($courses)) {
            FatUtility::exitWithErrorCode(404);
        }
        $course = current($courses);
        $teacherCourses = TeacherSearch::getCourses([$course['course_teacher_id']]);
        $course['teacher_courses'] = $teacherCourses[$course['course_teacher_id']] ?? 0;
        /* get more course by the same teacher */
        $courseObj = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $moreCourses = $courseObj->getMoreCourses($course['course_teacher_id'], $course['course_id']);
        /* get intended learner section details */
        $intended = new IntendedLearner();
        $intendedLearners = $intended->get($course['course_id'], $this->siteLangId);
        /* get curriculum */
        $curriculum = $this->curriculum($course['course_id']);
        /* fetch rating data */
        $revObj = new CourseRatingReview();
        $reviews = $revObj->getRatingStats($course['course_id']);
        /* Get order course data */
        $orderCourse = OrderCourse::getAttributesById($course['ordcrs_id'], ['ordcrs_status', 'ordcrs_reviewed']);
        $canRate = false;
        if ($orderCourse) {
            $canRate = OrderCourseSearch::canRate($orderCourse, $this->siteUserType);
        }
        /* Get and fill form data */
        $frm = $this->getReviewSrchForm();
        $frm->fill(['course_id' => $course['course_id']]);
        /* checkout form */
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $checkoutForm = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $checkoutForm->fill(['order_type' => Order::TYPE_COURSE]);


        $db = FatApp::getDb();

        $courseId = $course['course_id'] ?? null; // Safely get the course ID

        if (!empty($courseId)) {

            $query = "SELECT COUNT(*) AS section_count FROM tbl_sections WHERE section_course_id = " . $courseId . " AND section_quiz_id != 0";

            $result = $db->query($query); // Pass courseId as parameter

            if ($result) {
                $row = $db->fetch($result); // Fetch the result as an associative array
                $course['section_count'] = $row['section_count'] ?? 0;
            } else {
                $course['section_count'] = 0; // Default if query fails
            }
        } else {
            $course['section_count'] = 0; // Default if course ID is missing
        }


        $this->sets([
            'course' => $course,
            'moreCourses' => $moreCourses,
            'frm' => $frm,
            'intendedLearners' => $intendedLearners,
            'sections' => $curriculum['sections'],
            'videos' => $curriculum['videos'],
            'totalResources' => $curriculum['totalResources'],
            'reviews' => $reviews,
            'canRate' => $canRate,
            'checkoutForm' => $checkoutForm,
        ]);
        $this->_template->render();
    }

    /**
     * Preview video in popoup
     *
     * @param int $courseId
     * @return void
     */
    public function previewVideo(int $courseId)
    {
        $courseId = FatUtility::int($courseId);
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->set('courseId', $courseId);
        /* get course details */
        $srch = new SearchBase(Course::DB_TBL, 'course');
        $srch->joinTable(
            Course::DB_TBL_LANG,
            'LEFT JOIN',
            'crsdetail.course_id = course.course_id',
            'crsdetail'
        );
        $srch->addFld('crsdetail.course_title');
        $srch->addCondition('course.course_id', '=', $courseId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $this->set('course', FatApp::getDb()->fetch($srch->getResultSet()));
        $this->_template->render(false, false);
    }

    /**
     * Get curriculum list
     *
     * @param int $courseId
     * @return array
     */
    private function curriculum(int $courseId)
    {
        $srch = new SectionSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->addSearchListingFields();
        $srch->addCondition('section.section_course_id', '=', $courseId);
        $srch->applyPrimaryConditions();
        $srch->addOrder('section.section_order');
        $sections = $srch->fetchAndFormat();
        /* get list of lecture ids */
        $lectureIds = Lecture::getIds($sections);
        $videos = (count($lectureIds) > 0) ? Lecture::getVideos($lectureIds) : [];
        return [
            'videos' => $videos,
            'sections' => $sections,
            'totalResources' => array_sum(array_column($sections, 'total_resources'))
        ];
    }

    /**
     * Render course reviews
     *
     */
    public function reviews()
    {
        $frm = $this->getReviewSrchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $srch = new SearchBase(RatingReview::DB_TBL, 'ratrev');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'course.course_id = ratrev.ratrev_type_id', 'course');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = ratrev.ratrev_user_id', 'learner');
        $srch->addCondition('ratrev.ratrev_status', '=', RatingReview::STATUS_APPROVED);
        $srch->addCondition('ratrev.ratrev_type', '=', AppConstant::COURSE);
        $srch->addCondition('ratrev.ratrev_type_id', '=', $post['course_id']);
        $srch->addMultipleFields([
            'user_first_name',
            'user_last_name',
            'ratrev_id',
            'ratrev_user_id',
            'ratrev_title',
            'ratrev_detail',
            'ratrev_overall',
            'ratrev_created',
            'course_reviews'
        ]);
        $srch->addOrder('ratrev.ratrev_id', $post['sorting']);
        $pagesize = AppConstant::PAGESIZE;
        $srch->setPageSize($pagesize);
        $srch->setPageNumber($post['pageno']);
        $this->sets([
            'reviews' => FatApp::getDb()->fetchAll($srch->getResultSet()),
            'pageCount' => $srch->pages(),
            'post' => $post,
            'pagesize' => $pagesize,
            'recordCount' => $srch->recordCount(),
            'frm' => $frm,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Get Review Form
     * 
     * @return Form
     */
    private function getReviewSrchForm(): Form
    {
        $frm = new Form('reviewFrm');
        $fld = $frm->addHiddenField('', 'course_id');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setIntPositive();
        $frm->addHiddenField('', 'sorting', RatingReview::SORTBY_NEWEST);
        $frm->addHiddenField('', 'pageno', 1);
        return $frm;
    }

    /**
     * Get video content for preview
     *
     * @param int $resourceId
     */
    public function resource(int $resourceId)
    {
        $resourceId = FatUtility::int($resourceId);
        if ($resourceId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $srch = new SearchBase(Lecture::DB_TBL_LECTURE_RESOURCE, 'lecsrc');
        $srch->joinTable(
            Lecture::DB_TBL,
            'INNER JOIN',
            'lecture.lecture_id = lecsrc.lecsrc_lecture_id',
            'lecture'
        );
        $srch->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addMultipleFields(['lecsrc_link', 'lecture.lecture_title', 'lecsrc_course_id', 'lecsrc_id', 'lecsrc_lecture_id']);
        $srch->doNotCalculateRecords();
        $srch1 = clone $srch;

        $srch->addCondition('lecsrc.lecsrc_id', '=', $resourceId);
        $srch->setPageSize(1);
        $resource = FatApp::getDb()->fetch($srch->getResultSet());
        $this->set('resource', $resource);
        /* get free lectures */
        $srch1->joinTable(
            Lecture::DB_TBL,
            'INNER JOIN',
            'lecture.lecture_id = lecsrc.lecsrc_lecture_id',
            'lecture'
        );
        $srch1->addFld('lecture_duration');
        $srch1->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch1->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch1->addCondition('lecsrc_course_id', '=', $resource['lecsrc_course_id']);
        $srch1->addCondition('lecture_is_trial', '=', AppConstant::YES);
        $srch1->addCondition('lecsrc_type', '=', Lecture::TYPE_RESOURCE_EXTERNAL_URL);
        $this->set('lectures', FatApp::getDb()->fetchAll($srch1->getResultSet()));
        $this->_template->render(false, false);
    }

    /**
     * Auto Complete JSON
     */
    public function autoComplete()
    {
        $keyword = FatApp::getPostedData('term', FatUtility::VAR_STRING, '');
        if (empty($keyword)) {
            FatUtility::dieJsonSuccess(['data' => []]);
        }
        $filterTypes = Course::getFilterTypes();

        $courses = $this->getCourses($keyword);
        $data = [];
        if ($courses) {
            $data[] = $this->formatFiltersData($courses, Course::FILTER_COURSE);
        }
        /* find teachers */
        $teachers = $this->getTeachers($keyword);
        if (count($teachers) > 0) {
            $data[] = $this->formatFiltersData($teachers, Course::FILTER_TEACHER);
        }
        /* find tags */
        $tagsList = $this->getTags($keyword);
        $keyword = strtolower($keyword);
        if (count($tagsList)) {
            $list = [];
            foreach ($tagsList as $tags) {
                $tags = json_decode($tags['course_srchtags']);
                if (count($tags) > 0) {
                    foreach ($tags as $tag) {
                        if (stripos(strtolower($tag), $keyword) !== FALSE) {
                            $list[] = $tag;
                        }
                    }
                }
            }
            $child = [];
            if (count($list) > 0) {
                $list = array_unique($list);
                foreach ($list as $tag) {
                    $child[] = [
                        "id" => $tag,
                        "text" => $tag
                    ];
                }
            }

            $data[] = [
                'text' => $filterTypes[Course::FILTER_TAGS],
                'type' => Course::FILTER_TAGS,
                'children' => $child
            ];
        }
        echo json_encode($data);
        die;
    }

    /**
     * Function to format autocomplete filter data
     *
     * @param array $filtersData
     * @param int   $type
     * @return array
     */
    private function formatFiltersData(array $filtersData, int $type)
    {
        $filterTypes = Course::getFilterTypes();
        $child = [];
        foreach ($filtersData as $data) {
            $child[] = [
                "id" => $data['id'],
                "text" => $data['name']
            ];
        }
        return [
            'text' => $filterTypes[$type],
            'type' => $type,
            'children' => $child
        ];
    }

    /**
     * Function to get courses for autocomplete filter
     *
     * @param string $keyword
     * @return array
     */
    private function getCourses($keyword = '')
    {
        $srch = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $srch->applyPrimaryConditions();
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('teacher.user_username', '!=', "");
        $srch->addMultiplefields(['course.course_id as id', 'crsdetail.course_title as name']);
        if (!empty($keyword)) {
            $srch->addCondition('crsdetail.course_title', 'LIKE', '%' . $keyword . '%');
        }
        $srch->setPageSize(5);
        $srch->doNotCalculateRecords();
        $courses = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($courses)) {
            return $courses;
        }
        return [];
    }

    /**
     * Function to get teachers for autocomplete filter
     *
     * @param string $keyword
     * @return array
     */
    private function getTeachers($keyword = '')
    {
        $srch = new TeacherSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->applyPrimaryConditions();
        $cnd = $srch->addCondition('teacher.user_first_name', 'LIKE', '%' . $keyword . '%');
        $cnd->attachCondition('teacher.user_last_name', 'LIKE', '%' . $keyword . '%', 'OR');
        $cnd->attachCondition('mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)', 'LIKE', '%' . $keyword . '%', 'OR', true);
        $srch->addOrder('teacher.user_first_name', 'ASC');
        $srch->addMultiplefields(['teacher.user_id as id', 'CONCAT(teacher.user_first_name, " ", teacher.user_last_name) as name']);
        $srch->setPageSize(5);
        $srch->doNotCalculateRecords();
        $teachers = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($teachers)) {
            return $teachers;
        }
        return [];
    }

    /**
     * Function to get tags for autocomplete filter
     *
     * @param string $keyword
     * @return array
     */
    private function getTags($keyword = '')
    {
        $srch = new SearchBase(Course::DB_TBL_LANG, 'crsdetail');
        $srch->joinTable(
            Course::DB_TBL,
            'INNER JOIN',
            'crsdetail.course_id = course.course_id',
            'course'
        );
        $srch->doNotCalculateRecords();
        $srch->setPageSize(5);
        $srch->addCondition('mysql_func_LOWER(course_srchtags)', 'LIKE', '%' . strtolower($keyword) . '%', 'AND', true);
        $srch->addFld('course_srchtags');
        $srch->addCondition('course.course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $tagsList = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($tagsList)) {
            return $tagsList;
        }
        return [];
    }
    

}

