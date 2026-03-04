<?php
/**
 * Parent Controller - Basic Parent Portal
 */
class ParentController extends DashboardController
{
    const DB_TBL_PARENT_STUDENTS = 'tbl_parent_students';
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    public function __construct(string $action)
    {
        // Parent should behave like Learner session-type
        MyUtility::setUserType(User::LEARNER);
        parent::__construct($action);
        $_SESSION['RWU_DASHBOARD_ROLE'] = 'parent';

        // Guard: only parents can access (except for returning from impersonation)
        if ($action != 'backToParent') {
            if (empty($this->siteUser['user_is_parent']) || $this->siteUser['user_is_parent'] != AppConstant::YES) {
                Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
                FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
            }
        }
    }

    /**
     * Parent dashboard home -> Summary dashboard
     */
    public function index()
    {
        $children = $this->getApprovedChildren();
        $pendingRequests = $this->getPendingRequestsCount();
        $upcomingLessons = $this->getUpcomingLessonsForChildren();

        $this->sets([
            'children' => $children,
            'childrenCount' => count($children),
            'pendingRequests' => $pendingRequests,
            'upcomingLessons' => $upcomingLessons,
        ]);
        $this->_template->render();
    }

    /**
     * List children linked to this parent (approved only)
     */
    public function children()
    {
        $children = $this->getApprovedChildren();
        $pendingRequests = $this->getPendingRequests();

        $this->sets([
            'children' => $children,
            'pendingRequests' => $pendingRequests,
        ]);
        $this->_template->render();
    }

    /**
     * View a single child (detailed view)
     */
    public function child(int $studentId)
    {
        $studentId = FatUtility::int($studentId);
        if ($studentId < 1) {
            FatUtility::exitWithErrorCode(404);
        }

        $child = $this->getApprovedChild($studentId);
        if (empty($child)) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
            FatApp::redirectUser(MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD));
        }

        // Get child's courses progress
        $courses = $this->getChildCourses($studentId);

        // Get child's upcoming lessons
        $upcomingLessons = $this->getChildUpcomingLessons($studentId);

        // Get child's quiz attempts/available
        $quizAttempts = $this->getChildQuizzes($studentId);

        // Get child's tutors
        $tutors = $this->getChildTutors($studentId);

        // Calculate counts
        $attemptedQuizzesCount = 0;
        foreach ($quizAttempts as $q) {
            if (empty($q['not_attempted'])) {
                $attemptedQuizzesCount++;
            }
        }
        $unlockedCoursesCount = count($courses);

        $this->sets([
            'child' => $child,
            'courses' => $courses,
            'upcomingLessons' => $upcomingLessons,
            'quizAttempts' => $quizAttempts,
            'tutors' => $tutors,
            'attemptedQuizzesCount' => $attemptedQuizzesCount,
            'unlockedCoursesCount' => $unlockedCoursesCount,
        ]);
        $this->_template->render();
    }

    /**
     * Add child form
     */
    public function addChildForm()
    {
        $this->set('linkFrm', $this->getAddChildForm());
        $this->set('signupFrm', $this->getChildSignupForm());
        $this->_template->render();
    }

    /**
     * Setup add child request
     */
    public function setupAddChild()
    {
        $frm = $this->getAddChildForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $childEmail = $post['child_email'];
        $relation = $post['relation'];

        $childUser = User::getByEmail($childEmail);
        if (empty($childUser)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_USER_WITH_THIS_EMAIL_NOT_FOUND'));
        }

        if ($this->isAlreadyLinked($childUser['user_id'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_CHILD_ALREADY_LINKED'));
        }

        $data = [
            'parstd_parent_user_id' => $this->siteUserId,
            'parstd_student_user_id' => $childUser['user_id'],
            'parstd_relation' => $relation,
            'parstd_status' => self::STATUS_PENDING,
            'parstd_added_on' => date('Y-m-d H:i:s'),
        ];

        if (!FatApp::getDb()->insertFromArray(self::DB_TBL_PARENT_STUDENTS, $data)) {
            FatUtility::dieJsonError(FatApp::getDb()->getError());
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_LINK_REQUEST_SENT_SUCCESSFULLY'));
    }

    /**
     * Setup child signup and auto-link
     */
    public function setupChildSignup()
    {
        $frm = $this->getChildSignupForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        // Prepare data for UserAuth::signup
        $signupData = [
            'user_first_name' => $post['user_first_name'],
            'user_last_name' => $post['user_last_name'],
            'user_email' => $post['user_email'],
            'user_password' => $post['user_password'],
            'user_phone_code' => $this->siteUser['user_phone_code'] ?? '1', // Default to parent's or 1
            'user_phone_number' => $this->siteUser['user_phone_number'] ?? '0000000000', // Placeholder
        ];

        $auth = new UserAuth();
        if (!$auth->signup($signupData)) {
            FatUtility::dieJsonError($auth->getError());
        }

        $childUser = User::getByEmail($post['user_email']);
        if (empty($childUser)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_ERROR_IN_CREATING_CHILD_ACCOUNT'));
        }

        // Auto-link with APPROVED status
        $linkData = [
            'parstd_parent_user_id' => $this->siteUserId,
            'parstd_student_user_id' => $childUser['user_id'],
            'parstd_relation' => $post['relation'],
            'parstd_status' => self::STATUS_APPROVED,
            'parstd_added_on' => date('Y-m-d H:i:s'),
        ];

        if (!FatApp::getDb()->insertFromArray(self::DB_TBL_PARENT_STUDENTS, $linkData)) {
            FatUtility::dieJsonError(FatApp::getDb()->getError());
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CHILD_ACCOUNT_CREATED_AND_LINKED_SUCCESSFULLY'));
    }

    /**
     * Remove child link
     */
    public function removeChild(int $studentId)
    {
        $studentId = FatUtility::int($studentId);
        if ($studentId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $db = FatApp::getDb();
        if (
            !$db->deleteRecords(self::DB_TBL_PARENT_STUDENTS, [
                'smt' => 'parstd_parent_user_id = ? AND parstd_student_user_id = ? AND parstd_status = ?',
                'vals' => [$this->siteUserId, $studentId, self::STATUS_APPROVED]
            ])
        ) {
            FatUtility::dieJsonError($db->getError());
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CHILD_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Get add child form (Link Existing)
     */
    private function getAddChildForm(): Form
    {
        $frm = new Form('addChildForm');
        $frm->addRequiredField(Label::getLabel('LBL_CHILD_EMAIL'), 'child_email', '', ['placeholder' => 'email@example.com']);
        $frm->addRequiredField(Label::getLabel('LBL_RELATION'), 'relation', '', ['placeholder' => Label::getLabel('LBL_E_G_PARENT_GUARDIAN')]);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SEND_LINK_REQUEST'));
        return $frm;
    }

    /**
     * Get child signup form
     */
    private function getChildSignupForm(): Form
    {
        $frm = new Form('childSignupForm');
        $frm->addRequiredField(Label::getLabel('LBL_FIRST_NAME'), 'user_first_name');
        $frm->addRequiredField(Label::getLabel('LBL_LAST_NAME'), 'user_last_name');
        $frm->addEmailField(Label::getLabel('LBL_EMAIL'), 'user_email');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_PASSWORD'), 'user_password');
        $fld->requirements()->setRequired();
        $frm->addRequiredField(Label::getLabel('LBL_RELATION'), 'relation', '', ['placeholder' => Label::getLabel('LBL_E_G_PARENT_GUARDIAN')]);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_CREATE_ACCOUNT'));
        return $frm;
    }

    /**
     * Fetch approved children for logged-in parent
     */
    private function getApprovedChildren(): array
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'stu.user_id = ps.parstd_student_user_id', 'stu');
        $srch->addCondition('ps.parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('ps.parstd_status', '=', self::STATUS_APPROVED);
        $srch->addDirectCondition('stu.user_deleted IS NULL');
        $srch->addMultipleFields([
            'ps.parstd_id',
            'ps.parstd_relation',
            'stu.user_id AS student_id',
            'stu.user_first_name',
            'stu.user_last_name',
            'stu.user_email',
        ]);
        $srch->addOrder('ps.parstd_id', 'DESC');
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Fetch pending requests sent by parent
     */
    private function getPendingRequests(): array
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'stu.user_id = ps.parstd_student_user_id', 'stu');
        $srch->addCondition('ps.parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('ps.parstd_status', '=', self::STATUS_PENDING);
        $srch->addDirectCondition('stu.user_deleted IS NULL');
        $srch->addMultipleFields([
            'ps.parstd_id',
            'ps.parstd_relation',
            'ps.parstd_added_on',
            'stu.user_id AS student_id',
            'stu.user_first_name',
            'stu.user_last_name',
            'stu.user_email',
        ]);
        $srch->addOrder('ps.parstd_id', 'DESC');
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get count of pending requests
     */
    private function getPendingRequestsCount(): int
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->addCondition('parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('parstd_status', '=', self::STATUS_PENDING);
        $srch->addFld('COUNT(*) as total');
        $result = FatApp::getDb()->fetch($srch->getResultSet());
        return $result['total'] ?? 0;
    }

    /**
     * Check if already linked to parent
     */
    private function isAlreadyLinked(int $childUserId): bool
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->addCondition('parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('parstd_student_user_id', '=', $childUserId);
        $srch->addFld('COUNT(*) as total');
        $result = FatApp::getDb()->fetch($srch->getResultSet());
        return ($result['total'] ?? 0) > 0;
    }

    /**
     * Fetch one approved child for this parent
     */
    private function getApprovedChild(int $studentId): array
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'stu.user_id = ps.parstd_student_user_id', 'stu');
        $srch->addCondition('ps.parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('ps.parstd_student_user_id', '=', $studentId);
        $srch->addCondition('ps.parstd_status', '=', self::STATUS_APPROVED);
        $srch->addDirectCondition('stu.user_deleted IS NULL');
        $srch->addMultipleFields([
            'ps.parstd_id',
            'ps.parstd_relation',
            'stu.user_id AS student_id',
            'stu.user_first_name',
            'stu.user_last_name',
            'stu.user_email',
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        return FatApp::getDb()->fetch($srch->getResultSet()) ?: [];
    }

    /**
     * Get child's courses
     */
    private function getChildCourses(int $childId): array
    {
        $db = FatApp::getDb();

        // 1. Get Direct Order Courses
        $srch = new SearchBase(OrderCourse::DB_TBL, 'oc');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'c.course_id = oc.ordcrs_course_id', 'c');

        // Join actual progress table
        $srch->joinTable(
            CourseProgress::DB_TBL,
            'LEFT JOIN',
            'crspro.crspro_ordcrs_id = oc.ordcrs_id OR (crspro.crspro_user_id = oc.ordcrs_user_id AND crspro.crspro_course_id = oc.ordcrs_course_id)',
            'crspro'
        );

        $srch->addCondition('oc.ordcrs_user_id', '=', $childId);

        $srch->addMultipleFields([
            'oc.ordcrs_id',
            'oc.ordcrs_course_id',
            'IFNULL(crspro.crspro_progress, 0) as ordcrs_progress',
            'IFNULL(crspro.crspro_status, oc.ordcrs_status) as ordcrs_status',
            'c.course_title',
        ]);

        $srch->addOrder('oc.ordcrs_id', 'DESC');

        $rs = $srch->getResultSet();
        $directCourses = $rs ? ($db->fetchAll($rs) ?: []) : [];

        // 2. Get Subscription Courses (if applicable)
        $subCourses = [];
        if (class_exists('UnifiedCourseAccess')) {
            $subData = UnifiedCourseAccess::getUserCourses($childId);
            foreach ($subData as $row) {
                $subCourses[] = [
                    'ordcrs_id' => 0,
                    'ordcrs_course_id' => $row['course_id'],
                    'ordcrs_progress' => (int) ($row['progress_percent'] ?? 0),
                    'ordcrs_status' => (int) ($row['progress_status'] ?? 0),
                    'course_title' => $row['course_title'],
                ];
            }
        }

        // 3. Merge and deduplicate by course_id
        $allCourses = [];
        foreach ($directCourses as $c) {
            $allCourses[$c['ordcrs_course_id']] = $c;
        }

        foreach ($subCourses as $c) {
            if (!isset($allCourses[$c['ordcrs_course_id']])) {
                $allCourses[$c['ordcrs_course_id']] = $c;
            } else {
                // If sub progress is higher, maybe prefer it?
                if ($c['ordcrs_progress'] > $allCourses[$c['ordcrs_course_id']]['ordcrs_progress']) {
                    $allCourses[$c['ordcrs_course_id']]['ordcrs_progress'] = $c['ordcrs_progress'];
                    $allCourses[$c['ordcrs_course_id']]['ordcrs_status'] = $c['ordcrs_status'];
                }
            }
        }

        return array_values($allCourses);
    }

    /**
     * Get child's upcoming lessons
     */
    private function getChildUpcomingLessons(int $childId): array
    {
        $srch = new LessonSearch($this->siteLangId, $childId, User::LEARNER);
        $srch->addCondition('ordles_lesson_starttime', '>=', date('Y-m-d H:i:s'));
        $srch->addCondition('ordles_status', '=', Lesson::SCHEDULED);
        $srch->addOrder('ordles_lesson_starttime', 'ASC');
        $srch->applyPrimaryConditions();

        $srch->addMultipleFields([
            'ordles.ordles_id',
            'ordles.ordles_lesson_starttime',
            'ordles.ordles_lesson_endtime',
            'teacher.user_first_name as teacher_first_name',
            'teacher.user_last_name as teacher_last_name',
        ]);

        $srch->setPageSize(10);
        $rows = $srch->fetchAndFormat();
        return is_array($rows) ? $rows : [];
    }

    /**
     * Get child's quiz attempts
     */
    private function getChildQuizzes(int $childId): array
    {
        $db = FatApp::getDb();

        // 1. Get attempts
        $attemptTbl = 'tbl_quiz_attempts';
        $quizTbl = 'tbl_quizzes';

        $srch = new SearchBase($attemptTbl, 'qa');
        $srch->joinTable($quizTbl, 'INNER JOIN', 'q.quiz_id = qa.subtopic_id', 'q');
        $srch->addCondition('qa.user_id', '=', $childId);
        $srch->addMultipleFields([
            'qa.id as quatt_id',
            'IF(qa.total_marks > 0, (qa.marks_obtained / qa.total_marks) * 100, 0) as quatt_score',
            'qa.created_at as quatt_attempted_on',
            'qa.result as quatt_status',
            'q.quiz_id',
            'q.quiz_title',
        ]);
        $srch->addOrder('qa.created_at', 'DESC');
        $attempts = $db->fetchAll($srch->getResultSet()) ?: [];

        // 2. Get all courses the child is in
        $courses = $this->getChildCourses($childId);
        if (empty($courses)) {
            return $attempts;
        }
        $courseIds = array_column($courses, 'ordcrs_course_id');

        // 3. Get quizzes from these courses
        $srch = new SearchBase('tbl_lectures_resources', 'lr');
        $srch->joinTable('tbl_quizzes', 'INNER JOIN', 'q.quiz_id = lr.lecsrc_resrc_id', 'q');
        $srch->addCondition('lr.lecsrc_course_id', 'IN', $courseIds);
        $srch->addCondition('lr.lecsrc_type', '=', Lecture::TYPE_RESOURCE_QUIZ);
        $srch->addCondition('lr.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addMultipleFields(['q.quiz_id', 'q.quiz_title']);
        $availableQuizzes = $db->fetchAll($srch->getResultSet()) ?: [];

        // 4. Merge: For each available quiz, find the latest attempt or add as "Not Attempted"
        $quizMap = [];
        foreach ($attempts as $att) {
            if (!isset($quizMap[$att['quiz_id']])) {
                $quizMap[$att['quiz_id']] = $att;
            }
        }

        foreach ($availableQuizzes as $aq) {
            if (!isset($quizMap[$aq['quiz_id']])) {
                $quizMap[$aq['quiz_id']] = [
                    'quatt_id' => 0,
                    'quatt_score' => 0,
                    'quatt_attempted_on' => null,
                    'quatt_status' => 0,
                    'quiz_id' => $aq['quiz_id'],
                    'quiz_title' => $aq['quiz_title'],
                    'not_attempted' => true
                ];
            }
        }

        return array_values($quizMap);
    }

    /**
     * Get child's tutors
     */
    /**
     * Get child's tutors (safe: no fatals, no OrderLesson dependency, filtered by child)
     */
    private function getChildTutors(int $childId): array
    {
        $db = FatApp::getDb();

        // Use raw table names to avoid missing class fatals
        $srch = new SearchBase('tbl_order_lessons', 'ol');
        $srch->joinTable('tbl_orders', 'INNER JOIN', 'o.order_id = ol.ordles_order_id', 'o');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 't.user_id = ol.ordles_teacher_id', 't');

        // ONLY this child's lessons
        $srch->addCondition('o.order_user_id', '=', $childId);

        // Only valid teacher lessons
        $srch->addCondition('ol.ordles_teacher_id', '>', 0);

        $srch->addGroupBy('ol.ordles_teacher_id');

        $srch->addMultipleFields([
            't.user_id',
            't.user_first_name',
            't.user_last_name',
            'COUNT(ol.ordles_id) as total_lessons',
        ]);

        $rs = $srch->getResultSet();
        if ($rs === false) {
            // Optional log
            // FatUtility::createLog($db->getError(), 'parent-portal-errors.log');
            return [];
        }

        $rows = $db->fetchAll($rs);
        return is_array($rows) ? $rows : [];
    }


    /**
     * Get upcoming lessons for all children
     */
    private function getUpcomingLessonsForChildren(): array
    {
        $children = $this->getApprovedChildren();
        if (empty($children))
            return [];

        $childIds = array_column($children, 'student_id');

        $db = FatApp::getDb();
        $srch = new SearchBase('tbl_order_lessons', 'ol');
        $srch->joinTable('tbl_orders', 'INNER JOIN', 'o.order_id = ol.ordles_order_id', 'o');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 't.user_id = ol.ordles_teacher_id', 't');

        $srch->addCondition('o.order_user_id', 'IN', $childIds);
        $srch->addCondition('ol.ordles_lesson_starttime', '>=', date('Y-m-d H:i:s'));

        $srch->addMultipleFields([
            'ol.ordles_id',
            'ol.ordles_lesson_starttime',
            'ol.ordles_lesson_endtime',
            'o.order_user_id as child_id',
            't.user_first_name as teacher_first_name',
            't.user_last_name as teacher_last_name',
        ]);

        $srch->addOrder('ol.ordles_lesson_starttime', 'ASC');
        $srch->setPageSize(5);

        $rs = $srch->getResultSet();
        if ($rs === false) {
            return [];
        }
        return FatApp::getDb()->fetchAll($rs);
    }

    /**
     * Login as child
     * 
     * @param int $studentId
     */
    public function loginAsChild(int $studentId)
    {
        $studentId = FatUtility::int($studentId);
        if ($studentId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatApp::redirectUser(MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD));
        }

        // Security Check: Verify this student is an approved child of the requester
        $child = $this->getApprovedChild($studentId);
        if (empty($child)) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
            FatApp::redirectUser(MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD));
        }

        // Get child user details
        $data = User::getAttributesById($studentId, ['user_email', 'user_password']);
        if (empty($data)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatApp::redirectUser(MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD));
        }

        $parentUserId = $this->siteUserId;
        $userAuth = new UserAuth();

        // Logout current Parent session
        UserAuth::logout();
        TeacherRequest::closeSession();

        // Login as Child (using hashed password)
        if (!$userAuth->login($data['user_email'], $data['user_password'], MyUtility::getUserIp(), false)) {
            Message::addErrorMessage($userAuth->getError());
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm', [], CONF_WEBROOT_FRONTEND));
        }

        // Store parent ID in the child's session for "Back to Parent" feature
        $_SESSION['RWU_PARENT_IMPERSONATOR_ID'] = $parentUserId;

        // Redirect to child's dashboard
        FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
    }

    /**
     * Back to parent profile
     */
    public function backToParent()
    {
        $parentUserId = $_SESSION['RWU_PARENT_IMPERSONATOR_ID'] ?? 0;
        if ($parentUserId < 1) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
            FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
        }

        $data = User::getAttributesById($parentUserId, ['user_email', 'user_password']);
        if (empty($data)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm', [], CONF_WEBROOT_FRONTEND));
        }

        $userAuth = new UserAuth();

        // Logout current Child session
        UserAuth::logout();
        TeacherRequest::closeSession();

        // Login back as Parent
        if (!$userAuth->login($data['user_email'], $data['user_password'], MyUtility::getUserIp(), false)) {
            Message::addErrorMessage($userAuth->getError());
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm', [], CONF_WEBROOT_FRONTEND));
        }

        // Switch role back to parent
        $_SESSION['RWU_DASHBOARD_ROLE'] = 'parent';
        unset($_SESSION['RWU_PARENT_IMPERSONATOR_ID']);

        // Redirect to parent portal
        FatApp::redirectUser(MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD));
    }

}