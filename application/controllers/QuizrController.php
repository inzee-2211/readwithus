<?php

class QuizrController extends MyAppController
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
     * Course list
     *
     * @return void
     */
 public function index()
{
    /* -------------------------------------------------------------
       1. Popular courses (fallback if no recommended courses)
    -------------------------------------------------------------- */
    $course         = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
    $popularCourses = $course->getPopularCourses();
    $popularCourses = array_slice($popularCourses, 0, 4);

    /* -------------------------------------------------------------
       2. Get attempt id from query string
    -------------------------------------------------------------- */
    $attemptId = isset($_GET['attempt']) ? (int) $_GET['attempt'] : 0;

    $db                = FatApp::getDb();
    $attemptresult     = [];
    $attemptquestions  = [];
    $resultText        = '';
    $currentSubtopicId = null;
    $nextSubtopicId    = null;
    
    // For video + recommended courses
    $recommendedVideoUrl = '';
    $recommendedCourses  = [];

    // Tutor request mapping
    $subtopicName      = '';
    $tutreqLevelId     = 0;
    $tutreqSubjectId   = 0;
    $tutreqExamboardId = 0;
    $tutreqTierId      = 0;

    /* -------------------------------------------------------------
       3. Load attempt row (header) and get video URL
    -------------------------------------------------------------- */
    if ($attemptId > 0) {
        $sqlAttempt = "SELECT * FROM tbl_quiz_attempts WHERE id = " . (int)$attemptId . " LIMIT 1";
        $rsAttempt  = $db->query($sqlAttempt);

        if ($rsAttempt) {
            $row = $db->fetch($rsAttempt);
            if ($row) {
                $attemptresult[]   = $row;
                $resultText        = strtolower($row['result'] ?? '');
                $currentSubtopicId = $row['subtopic_id'] ?? null;
                
                // Get video URL from tbl_quiz_management
                if (!empty($currentSubtopicId)) {
                    $sqlVideo = "SELECT video_url FROM tbl_quiz_management WHERE id = " . (int)$currentSubtopicId . " LIMIT 1";
                    $rsVideo  = $db->query($sqlVideo);
                    if ($rsVideo) {
                        $videoRow            = $db->fetch($rsVideo);
                        $recommendedVideoUrl = $videoRow['video_url'] ?? '';
                    }
                }
            }
        }
    }

    /* -------------------------------------------------------------
       3.1 Compute next subtopic
    -------------------------------------------------------------- */
    if (!empty($currentSubtopicId)) {
        $nextSubtopicId = $this->getNextSubtopicId((int)$currentSubtopicId);
    }

    /* -------------------------------------------------------------
       3.5 Derive Level / Subject / Exam Board / Tier 
           for TutorRequest AND Courses, via tbl_quiz_setup
    -------------------------------------------------------------- */
    if (!empty($currentSubtopicId)) {
        // Get quiz_setup_id from tbl_quiz_management
        $setupQuery  = "SELECT quiz_setup_id FROM tbl_quiz_management WHERE id = " . (int)$currentSubtopicId . " LIMIT 1";
        $setupResult = $db->query($setupQuery);
        $quizSetupId = 0;
        
        if ($setupResult) {
            $setupRow    = $db->fetch($setupResult);
            $quizSetupId = $setupRow['quiz_setup_id'] ?? 0;
        }

        if ($quizSetupId > 0) {
            // Get level, subject, etc from tbl_quiz_setup
            $sqlMap = "
                SELECT 
                    qs.topic_name,
                    qs.level_id,
                    qs.subject_id,
                    qs.examboard_id,
                    qs.tier_id,
                    lvl.level_name,
                    subj.subject AS subject_name,
                    eb.name AS examboard_name,
                    tr.name AS tier_name
                FROM tbl_quiz_setup qs
                LEFT JOIN course_levels lvl     ON lvl.id = qs.level_id
                LEFT JOIN course_subjects subj  ON subj.id = qs.subject_id
                LEFT JOIN course_examboards eb ON eb.id = qs.examboard_id
                LEFT JOIN course_tier tr       ON tr.id = qs.tier_id
                WHERE qs.id = " . (int)$quizSetupId . "
                LIMIT 1
            ";

            $rsMap = $db->query($sqlMap);
            if ($rsMap) {
                $mapRow = $db->fetch($rsMap);
                if ($mapRow) {
                    $subtopicName      = $mapRow['topic_name']   ?? '';
                    $tutreqLevelId     = (int)($mapRow['level_id']     ?? 0);
                    $tutreqSubjectId   = (int)($mapRow['subject_id']   ?? 0);
                    $tutreqExamboardId = (int)($mapRow['examboard_id'] ?? 0);
                    $tutreqTierId      = (int)($mapRow['tier_id']      ?? 0);

                    // Courses whose level + subject match quiz setup
                    $recommendedCourses = $this->getRecommendedCourses($tutreqLevelId, $tutreqSubjectId);
                }
            }
        }

        // If we couldn't get courses from quiz setup, try alternative method
        if (empty($recommendedCourses)) {
            $recommendedCourses = $this->getRecommendedCoursesAlternative($currentSubtopicId);
        }

        // Optional: keep this in session if used elsewhere
        if (!empty($subtopicName)) {
            $_SESSION['subtopicName'] = $subtopicName;
        }
    }

    /* -------------------------------------------------------------
       4. Load attempt answers + question_title + explanation
    -------------------------------------------------------------- */
    if ($attemptId > 0) {
        $sqlAnswers = "
            SELECT
                ans.*,
                qb.question_title,
                qb.explanation,
                qb.correct_answer
            FROM tbl_quiz_attempt_answers AS ans
            LEFT JOIN tbl_quaestion_bank AS qb ON qb.id = ans.question_id
            WHERE ans.attempt_id = " . (int)$attemptId . "
            ORDER BY ans.id ASC
        ";

        $rsAnswers = $db->query($sqlAnswers);
        if ($rsAnswers) {
            $attemptquestions = $db->fetchAll($rsAnswers);
        } else {
            error_log('Quiz answers query failed: ' . $db->getError());
        }
    }

    /* -------------------------------------------------------------
       5. Email parent if result = fail
    -------------------------------------------------------------- */
     if (!empty($resultText)) {
        $parentEmail = $_SESSION['quiz_user']['parent_email'] ?? '';
        $studentName = $_SESSION['quiz_user']['full_name'] ?? 'Your child';
        $subjectName = $_SESSION['subjectName'] ?? ($subtopicName ?? 'this subject');

        // Debug log so we know what’s going on
        error_log("QuizrController index: resultText={$resultText}, parentEmail={$parentEmail}");

        if ($resultText === 'fail' && !empty($parentEmail)) {

            $subject = "Your child's quiz result on Read With Us";

            $message = "Dear Parent,

{$studentName} recently attempted a quiz in {$subjectName} and did not achieve a passing grade this time.

We understand this can be worrying, but this result does NOT define their potential. At Read With Us, every setback is a chance to rebuild confidence with the right support.

We offer:
• Interactive topic quizzes
• Revision modules focused on weaker areas
• Full course access guided by expert educators

\"It's not about being the best, it's about being better than you were yesterday.\"

We recommend revisiting the topic and retrying the quiz to strengthen understanding.

Warm regards,
The Read With Us Team
info@readwithus.org.uk
www.readwithus.org.uk";

            // Convert newlines to <br> so it looks nice in HTML
            $bodyHtml = nl2br($message);

            if (!FatMailer::sendRaw([$parentEmail], $subject, $bodyHtml)) {
                error_log("QuizrController: failed to send parent email to {$parentEmail}");
            } else {
                error_log("QuizrController: parent email sent to {$parentEmail}");
            }
        }
    }

    /* -------------------------------------------------------------
       6. Prepare data for the view
    -------------------------------------------------------------- */
    $srchFrm = CourseSearch::getSearchForm($this->siteLangId);
    unset($_SESSION[AppConstant::SEARCH_SESSION]);

    // 🔸 Behaviour: use recommended if present, otherwise fallback to popular
    $displayCourses = !empty($recommendedCourses) ? $recommendedCourses : $popularCourses;

    $this->set('srchFrm', $srchFrm);
    $this->set('attemptresult', $attemptresult);
    $this->set('attemptquestions', $attemptquestions);
    $this->set('coursesslider', $displayCourses);
    $this->set('filterTypes', Course::getFilterTypes());
    $this->set('currentSubtopicId', $currentSubtopicId);
    $this->set('resultText', $resultText);
    $this->set('nextSubtopicId', $nextSubtopicId); 

    // For tutor modal + video
    $this->set('subtopicName',        $subtopicName);
    $this->set('tutreqLevelId',       $tutreqLevelId);
    $this->set('tutreqSubjectId',     $tutreqSubjectId);
    $this->set('tutreqExamboardId',   $tutreqExamboardId);
    $this->set('tutreqTierId',        $tutreqTierId);
    $this->set('recommendedVideoUrl', $recommendedVideoUrl);

    $this->_template->render();
}



/**
 * Get recommended courses based on level and subject.
 * Uses tbl_courses + tbl_course_details only (matches your schema).
 */
private function getRecommendedCourses($levelId, $subjectId)
{
    $db      = FatApp::getDb();
    $courses = [];

    // Must have both filters; otherwise, no recommendation.
    if ($levelId <= 0 || $subjectId <= 0) {
        return [];
    }

    try {
        $sql = "
            SELECT 
                c.course_id,
                d.course_title,
                c.course_price,
                c.course_ratings,
                c.course_reviews,
                c.course_slug,
                c.course_level,
                c.course_subject_id,
                NULL AS subcate_name,
                d.course_details
            FROM tbl_courses c
            LEFT JOIN tbl_course_details d 
                ON d.course_id = c.course_id
            WHERE c.course_active = " . (int) AppConstant::ACTIVE . "
              AND c.course_status = " . (int) Course::PUBLISHED . "
              AND c.course_deleted IS NULL
              AND c.course_level = " . (int) $levelId . "
              AND c.course_subject_id = " . (int) $subjectId . "
            ORDER BY 
                c.course_ratings DESC, 
                c.course_reviews DESC,
                c.course_id DESC
            LIMIT 4
        ";

        $result = $db->query($sql);
        if ($result) {
            $courses = $db->fetchAll($result) ?: [];
        }

    } catch (Exception $e) {
        error_log("Error getting recommended courses: " . $e->getMessage());
    }

    return $courses;
}
/**
 * Fallback: recommended courses without level/subject filter.
 * Still uses only tbl_courses + tbl_course_details.
 */
private function getRecommendedCoursesAlternative($subtopicId)
{
    $db      = FatApp::getDb();
    $courses = [];

    try {
        $sql = "
            SELECT 
                c.course_id,
                d.course_title,
                c.course_price,
                c.course_ratings,
                c.course_reviews,
                c.course_slug,
                c.course_level,
                c.course_subject_id,
                NULL AS subcate_name,
                d.course_details
            FROM tbl_courses c
            LEFT JOIN tbl_course_details d 
                ON d.course_id = c.course_id
            WHERE c.course_active = " . (int) AppConstant::ACTIVE . "
              AND c.course_status = " . (int) Course::PUBLISHED . "
              AND c.course_deleted IS NULL
            ORDER BY 
                c.course_ratings DESC, 
                c.course_reviews DESC,
                c.course_id DESC
            LIMIT 4
        ";

        $result = $db->query($sql);
        if ($result) {
            $courses = $db->fetchAll($result) ?: [];
        }

    } catch (Exception $e) {
        error_log("Error in alternative course method: " . $e->getMessage());
    }

    return $courses;
}


private function getNextSubtopicId(int $currentSubtopicId): ?int
{
    $db = FatApp::getDb();

    $sql = "
        SELECT id
        FROM tbl_quiz_management
        WHERE id > " . (int)$currentSubtopicId . "
        ORDER BY id ASC
        LIMIT 1
    ";

    $res = $db->query($sql);
    if (!$res) {
        return null;
    }

    $row = $db->fetch($res);
    if (!empty($row) && !empty($row['id'])) {
        return (int)$row['id'];
    }
    return null; // no next quiz
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

        $subtopicId = trim($subtopicId);  // Trim any extra spaces
        $subtopicId = addslashes($subtopicId);  // Escape special characters (optional)


        $query = "SELECT id,subject FROM course_subjects WHERE   id = $subtopicId LIMIT 1";

        $result = $db->query($query);

        if (!$result) {
            echo "Error executing query: " . $db->errorInfo();
            die();
        }
        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetch($result);  // Assuming fetch returns a single row
        }
        return !empty($subtopic) ? $subtopic['subject'] : null;
    }


    public function getTopicnames($subjectid)
    {
        $db = FatApp::getDb();
        $subjectId = 0;


        $query = "SELECT id,topic FROM course_topics WHERE   subject_id = $subjectid";

        $result = $db->query($query);
        if (!$result) {
            echo "Error executing query: " . $db->errorInfo();
            die();
        }
        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetchAll($result);  // Assuming fetch returns a single row
        }

        return $subtopic;
    }







    /**
     * Find Teachers
     */
    public function search()
    {
        if (isset($_GET['attempt'])) {
            $attempt = $_GET['attempt'];
        } else {
            $attempt = null; // Set default if 'subtopic' is not in the query string
        }
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
