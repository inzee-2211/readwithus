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

        // Guard: only parents can access
        if (empty($this->siteUser['user_is_parent']) || $this->siteUser['user_is_parent'] != AppConstant::YES) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
            FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
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
        
        // Get child's quiz attempts
        $quizAttempts = $this->getChildQuizAttempts($studentId);
        
        // Get child's tutors
        $tutors = $this->getChildTutors($studentId);

        $this->sets([
            'child' => $child,
            'courses' => $courses,
            'upcomingLessons' => $upcomingLessons,
            'quizAttempts' => $quizAttempts,
            'tutors' => $tutors,
        ]);
        $this->_template->render();
    }

    /**
     * Add child form
     */
    public function addChildForm()
    {
        $frm = $this->getAddChildForm();
        $this->set('frm', $frm);
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

        // Check if child user exists
        $childUser = User::getByEmail($childEmail);
        if (empty($childUser)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_USER_WITH_THIS_EMAIL_NOT_FOUND'));
        }

        // Check if already linked
        if ($this->isAlreadyLinked($childUser['user_id'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_CHILD_ALREADY_LINKED'));
        }

        // Create pending request
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

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REQUEST_SENT_SUCCESSFULLY'));
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
        if (!$db->deleteRecords(self::DB_TBL_PARENT_STUDENTS, [
            'smt' => 'parstd_parent_user_id = ? AND parstd_student_user_id = ? AND parstd_status = ?',
            'vals' => [$this->siteUserId, $studentId, self::STATUS_APPROVED]
        ])) {
            FatUtility::dieJsonError($db->getError());
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CHILD_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Get add child form
     */
    private function getAddChildForm(): Form
    {
        $frm = new Form('addChildForm');
        $frm->addRequiredField(Label::getLabel('LBL_CHILD_EMAIL'), 'child_email', '', ['placeholder' => 'email@example.com']);
        $frm->addRequiredField(Label::getLabel('LBL_RELATION'), 'relation', '', ['placeholder' => Label::getLabel('LBL_E_G_PARENT_GUARDIAN')]);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SEND_REQUEST'));
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

    $srch = new SearchBase(OrderCourse::DB_TBL, 'oc');
    $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'c.course_id = oc.ordcrs_course_id', 'c');
    $srch->addCondition('oc.ordcrs_user_id', '=', $childId);

    $srch->addMultipleFields([
        'oc.ordcrs_id',
        'oc.ordcrs_course_id',
        'oc.ordcrs_progress',
        'oc.ordcrs_status',
        'c.course_title',
    ]);

    $srch->addOrder('oc.ordcrs_id', 'DESC');

    $rs = $srch->getResultSet();
    if ($rs === false) { return []; }   // << prevents fatal
    return $db->fetchAll($rs);
}

    /**
     * Get child's upcoming lessons
     */
   private function getChildUpcomingLessons(int $childId): array
{
    $db = FatApp::getDb();

    $srch = new SearchBase('tbl_order_lessons', 'ol');
    $srch->joinTable('tbl_orders', 'INNER JOIN', 'o.order_id = ol.ordles_order_id', 'o');
    $srch->joinTable(User::DB_TBL, 'INNER JOIN', 't.user_id = ol.ordles_teacher_id', 't');

    $srch->addCondition('o.order_user_id', '=', $childId);
    $srch->addCondition('ol.ordles_lesson_starttime', '>=', date('Y-m-d H:i:s'));

    $srch->addMultipleFields([
        'ol.ordles_id',
        'ol.ordles_lesson_starttime',
        'ol.ordles_lesson_endtime',
        't.user_first_name as teacher_first_name',
        't.user_last_name as teacher_last_name',
    ]);

    $srch->addOrder('ol.ordles_lesson_starttime', 'ASC');
    $srch->setPageSize(10);

    $rs = $srch->getResultSet();
    if ($rs === false) { return []; } // prevents fetchAssoc(bool) crash
    return $db->fetchAll($rs);
}

    /**
     * Get child's quiz attempts
     */
    /**
 * Get child's quiz attempts (safe: no fatals)
 */
private function getChildQuizAttempts(int $childId): array
{
    $db = FatApp::getDb();

    // If these classes exist, keep using them; otherwise fallback to table strings
    $attemptTbl = defined('QuizAttempt::DB_TBL') ? QuizAttempt::DB_TBL : 'tbl_quiz_attempts';
    $quizTbl    = defined('Quiz::DB_TBL') ? Quiz::DB_TBL : 'tbl_quizzes';

    $srch = new SearchBase($attemptTbl, 'qa');
    $srch->joinTable($quizTbl, 'INNER JOIN', 'q.quiz_id = qa.quatt_quiz_id', 'q');
    $srch->addCondition('qa.quatt_user_id', '=', $childId);

    $srch->addMultipleFields([
        'qa.quatt_id',
        'qa.quatt_score',
        'qa.quatt_attempted_on',
        'q.quiz_title',
    ]);

    $srch->addOrder('qa.quatt_attempted_on', 'DESC');
    $srch->setPageSize(10);

    $rs = $srch->getResultSet();
    if ($rs === false) {
        // Optional: log DB error if you want
        // FatUtility::createLog($db->getError(), 'parent-portal-errors.log');
        return [];
    }

    $rows = $db->fetchAll($rs);
    return is_array($rows) ? $rows : [];
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
    if (empty($children)) return [];

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
    if ($rs === false) { return []; }
    return FatApp::getDb()->fetchAll($rs);
}

}