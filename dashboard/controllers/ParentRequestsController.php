<?php

/**
 * Parent Requests Controller - For Learners to manage parent links
 */
class ParentRequestsController extends DashboardController
{
    const DB_TBL_PARENT_STUDENTS = 'tbl_parent_students';
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    public function __construct(string $action)
    {
        parent::__construct($action);
        // Only learners should access this
        if ($this->siteUserType != User::LEARNER) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
            FatApp::redirectUser(MyUtility::makeUrl('Account', '', [], CONF_WEBROOT_DASHBOARD));
        }
    }

    public function index()
    {
        $requests = $this->getRequests();
        $this->set('requests', $requests);
        $this->_template->render();
    }

    public function acceptRequest(int $parstdId)
    {
        $parstdId = FatUtility::int($parstdId);
        if ($parstdId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $db = FatApp::getDb();
        $where = [
            'smt' => 'parstd_id = ? AND parstd_student_user_id = ? AND parstd_status = ?',
            'vals' => [$parstdId, $this->siteUserId, self::STATUS_PENDING]
        ];

        if (!$db->updateFromArray(self::DB_TBL_PARENT_STUDENTS, ['parstd_status' => self::STATUS_APPROVED], $where)) {
            FatUtility::dieJsonError($db->getError());
        }

        if ($db->getAffectedRows() < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_REQUEST_NOT_FOUND_OR_ALREADY_PROCESSED'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REQUEST_ACCEPTED_SUCCESSFULLY'));
    }

    public function rejectRequest(int $parstdId)
    {
        $parstdId = FatUtility::int($parstdId);
        if ($parstdId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $db = FatApp::getDb();
        $where = [
            'smt' => 'parstd_id = ? AND parstd_student_user_id = ? AND parstd_status = ?',
            'vals' => [$parstdId, $this->siteUserId, self::STATUS_PENDING]
        ];

        if (!$db->updateFromArray(self::DB_TBL_PARENT_STUDENTS, ['parstd_status' => self::STATUS_REJECTED], $where)) {
            FatUtility::dieJsonError($db->getError());
        }

        if ($db->getAffectedRows() < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_REQUEST_NOT_FOUND_OR_ALREADY_PROCESSED'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REQUEST_REJECTED_SUCCESSFULLY'));
    }

    public function removeLink(int $parstdId)
    {
        $parstdId = FatUtility::int($parstdId);
        if ($parstdId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $db = FatApp::getDb();
        $where = [
            'smt' => 'parstd_id = ? AND parstd_student_user_id = ?',
            'vals' => [$parstdId, $this->siteUserId]
        ];

        if (!$db->deleteRecords(self::DB_TBL_PARENT_STUDENTS, $where)) {
            FatUtility::dieJsonError($db->getError());
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_LINK_REMOVED_SUCCESSFULLY'));
    }

    private function getRequests(): array
    {
        $srch = new SearchBase(self::DB_TBL_PARENT_STUDENTS, 'ps');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'p.user_id = ps.parstd_parent_user_id', 'p');
        $srch->addCondition('ps.parstd_student_user_id', '=', $this->siteUserId);
        $srch->addMultipleFields([
            'ps.*',
            'p.user_first_name as parent_first_name',
            'p.user_last_name as parent_last_name',
            'p.user_email as parent_email',
        ]);
        $srch->addOrder('ps.parstd_id', 'DESC');
        return FatApp::getDb()->fetchAll($srch->getResultSet()) ?: [];
    }
}
