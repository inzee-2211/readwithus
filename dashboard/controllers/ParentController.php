<?php

/**
 * Parent Controller - Basic Parent Portal
 */
class ParentController extends DashboardController
{
    const DB_TBL_PARENT_STUDENTS = 'tbl_parent_students';

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
     * Parent dashboard home -> Children list
     */
    public function index()
    {
        FatApp::redirectUser(MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD));
    }

    /**
     * List children linked to this parent (approved only)
     */
    public function children()
    {
        $children = $this->getApprovedChildren();
        $this->sets([
            'children' => $children,
        ]);
        $this->_template->render();
    }

    /**
     * View a single child (basic page for now)
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

        $this->sets([
            'child' => $child,
        ]);
        $this->_template->render();
    }

    /**
     * Fetch approved children for logged-in parent
     */
    private function getApprovedChildren(): array
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'stu.user_id = ps.parstd_student_user_id', 'stu');
        $srch->addCondition('ps.parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('ps.parstd_status', '=', 1);
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
     * Fetch one approved child for this parent
     */
    private function getApprovedChild(int $studentId): array
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'stu.user_id = ps.parstd_student_user_id', 'stu');
        $srch->addCondition('ps.parstd_parent_user_id', '=', $this->siteUserId);
        $srch->addCondition('ps.parstd_student_user_id', '=', $studentId);
        $srch->addCondition('ps.parstd_status', '=', 1);
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
}
