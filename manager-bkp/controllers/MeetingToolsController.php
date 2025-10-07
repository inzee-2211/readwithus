<?php

/**
 * Meeting Tools Controller is used for Meeting Tools handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class MeetingToolsController extends AdminBaseController
{

    /**
     * Initialize Meeting Tools
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewMeetingTool();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $this->set("search", $this->getSearchForm());
        $this->set("canEdit", $this->objPrivilege->canEditMeetingTool(true));
        $this->_template->render();
    }

    /**
     * Search & List Lesson Order
     */
    public function search()
    {
        $posts = FatApp::getPostedData();
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new SearchBase(MeetingTool::DB_TBL);
        if ($post['metool_status'] != '') {
            $srch->addCondition('metool_status', '=', $post['metool_status']);
        }
        if ($post['metool_code'] != '') {
            $srch->addCondition('metool_code', 'LIKE', '%' . $post['metool_code'] . '%');
        }
        $srch->addOrder('metool_code', 'ASC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $rs = $srch->getResultSet();
        $this->sets([
            'post' => $post,
            'recordCount' => $srch->recordCount(),
            'records' => FatApp::getDb()->fetchAll($rs),
            'canEdit' => $this->objPrivilege->canEditMeetingTool(true)
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Meeting Tool Form
     */
    public function form()
    {
        $this->objPrivilege->canEditMeetingTool();
        $id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        $meetingTool = MeetingTool::getAttributesById($id);
        if (empty($meetingTool)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $form = $this->getForm($meetingTool);
        if(MyUtility::isDemoUrl()){
            MyUtility::maskAndDisableFormFields($form, ['metool_id', 'metool_code']);
        }
        $this->sets([
            'frm' => $form,
            'metoolInfo' => $meetingTool['metool_info']
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Meeting Tool
     */
    public function setup()
    {
        if (MyUtility::isDemoUrl()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_SETTINGS'));
        }
        $this->objPrivilege->canEditMeetingTool();
        $id = FatApp::getPostedData('metool_id', FatUtility::VAR_INT, 0);
        $meetingTool = MeetingTool::getAttributesById($id);
        if (empty($meetingTool)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getForm($meetingTool);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $tool = new MeetingTool($post['metool_id']);
        if (!$tool->setup($post)) {
            FatUtility::dieJsonError($tool->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SETUP_SUCCESSFUL'));
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditMeetingTool();
        $id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if ($status == AppConstant::INACTIVE) {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $tool = new MeetingTool($id);
        if (!$tool->updateStatus($status)) {
            FatUtility::dieJsonError($tool->getError());
        }
        if (!FatApp::getDb()->updateFromArray(
            MeetingTool::DB_TBL,
            ['metool_status' => AppConstant::INACTIVE],
            ['smt' => 'metool_id != ?', 'vals' => [$id]]
        )) {
            FatUtility::dieJsonError(FatApp::getDb()->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmMeetingToolSearch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'metool_code');
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'metool_status', MeetingTool::getStatues());
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnClear = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear'));
        $btnSubmit->attachField($btnClear);
        return $frm;
    }

    /**
     * Get Meeting Tool Form
     * 
     * @param array $tool
     * @return Form
     */
    private function getForm(array $tool): Form
    {
        $frm = new Form('frmMeetingTool');
        $frm->addHiddenField('', 'metool_id', $tool['metool_id']);
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Code'), 'metool_code', $tool['metool_code'], ['disabled' => 'disabled']);
        $fld->requirements()->setRequired();
        $settings = json_decode($tool['metool_settings'], true);
        foreach ($settings as $row) {
            $frm->addRequiredField(Label::getLabel('MTL_' . $row['key']), 'metool_settings[' . $row['key'] . ']', $row['value']);
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }
}
