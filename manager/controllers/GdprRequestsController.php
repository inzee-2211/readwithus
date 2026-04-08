<?php

/**
 * GDPR Requests is used for GDPR Requests handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class GdprRequestsController extends AdminBaseController
{

    /**
     * Initialize GDPR Request
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewGdprRequests();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $this->set('frmSrch', $this->getSearchForm());
        $this->_template->render();
    }

    /**
     * Search & List GDPR Requests
     */
    public function search()
    {
        $frmSrch = $this->getSearchForm();
        if (!$post = $frmSrch->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError($frmSrch->getValidationErrors());
        }
        $srch = new SearchBase(GdprRequest::DB_TBL, 'gdpr');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'gdpr.gdpreq_user_id = user.user_id', 'user');
        $srch->addCondition('gdpreq_status', '!=', GdprRequest::STATUS_DELETED_REQUEST);
        if (!empty($post['keyword'])) {
            $fullName = 'mysql_func_CONCAT(user.user_first_name, " ", user.user_last_name)';
            $cond = $srch->addCondition($fullName, 'LIKE', '%' . trim($post['keyword']) . '%', 'AND', true);
            $cond->attachCondition('gdpreq_reason', 'LIKE', '%' . trim($post['keyword']) . '%');
        }
        if (!empty($post['status'])) {
            $srch->addCondition('gdpreq_status', '=', $post['status']);
        }
        if (!empty($post['added_on'])) {
            $srch->addCondition('gdpreq_added_on', '>=', MyDate::formatToSystemTimezone($post['added_on'] . ' 00:00:00'), 'AND', true);
            $srch->addCondition('gdpreq_added_on', '<=', MyDate::formatToSystemTimezone($post['added_on'] . ' 23:59:59'), 'AND', true);
        }
        $srch->addOrder('gdpreq_added_on', 'DESC');
        $srch->setPageNumber($post['page']);
        $srch->setPageSize($post['pageSize']);
        $gdprRequests = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('postedData', $post);
        $this->set('gdprRequests', $gdprRequests);
        $this->set('page', $post['page']);
        $this->set('pageSize', $post['pageSize']);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('gdprStatus', GdprRequest::getStatusArr());
        $this->set('canEdit', $this->objPrivilege->canEditGdprRequests(true));
        $this->_template->render(false, false);
    }

    /**
     * View GDPR request
     */
    public function view()
    {
        $requestId = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($requestId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $srch = new SearchBase(GdprRequest::DB_TBL, 'gdpr');
        $srch->joinTable(User::DB_TBL, 'LEFT JOIN', 'gdpr.gdpreq_user_id = u.user_id', 'u');
        $srch->addCondition('gdpreq_id', '=', $requestId);
        $srch->doNotCalculateRecords();
        $reqDetail = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($reqDetail)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($reqDetail['gdpreq_status'] == GdprRequest::STATUS_PENDING) {
            $frm = $this->getChangeStatusForm();
            $frm->fill($reqDetail);
            $this->set("frm", $frm);
        }
        $this->set("data", $reqDetail);

        $this->_template->render(false, false);
    }

    /**
     * Get Search Form

     * @return Form
     */
    protected function getSearchForm(): Form
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $frm->addHiddenField('', 'pageSize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
        $statuses = GdprRequest::getStatusArr();
        unset($statuses[GdprRequest::STATUS_DELETED_REQUEST]);
        $frm->addSelectBox(Label::getLabel('LBl_Status'), 'status', $statuses);
        $frm->addDateField(Label::getLabel('LBl_Added_On'), 'added_on');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addButton('', 'btn_reset', Label::getLabel('LBL_CLEAR'));
        return $frm;
    }

    /**
     * Get Status Form
     * 
     * @return Form
     */
    private function getChangeStatusForm(): Form
    {
        $frm = new Form('changeStatusForm');
        $status = [
            GdprRequest::STATUS_COMPLETED => Label::getLabel('LBL_COMPLETED'),
            GdprRequest::STATUS_DELETED_DATA => Label::getLabel('LBL_DELETE_DATA'),
            GdprRequest::STATUS_DELETED_REQUEST => Label::getLabel('LBL_DELETE_REQUEST'),
        ];
        $frm->addSelectBox(Label::getLabel('LBL_REQUEST_STATUS'), 'gdpreq_status', $status, '')
                ->requirements()->setRequired();
        $frm->addHiddenField('', 'gdpreq_id', 0);
        $frm->addSubmitButton('', 'btn_submit', 'Update');
        return $frm;
    }

    /**
     * Update GDPR Request Status
     */
    public function updateStatus()
    {
        $form = $this->getChangeStatusForm();
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $gdpr = new GdprRequest($post['gdpreq_id']);
        if (!$gdpr->updateStatus($post['gdpreq_status'])) {
            FatUtility::dieJsonError($gdpr->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_UPDATED_SUCCESSFULLY'));
    }

}
