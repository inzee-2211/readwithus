<?php

/**
 * Group Class is used for Group Classes handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class GroupClassesController extends AdminBaseController
{

    /**
     * Initialize Group Class
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewGroupClasses();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $frm = $this->getSearchForm();
        $frm->fill(FatApp::getQueryStringData());
        $this->set('frmSrch', $frm);
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addCss('css/jquery.datetimepicker.css');
        $this->_template->render();
    }

    /**
     * Search & List Group Classes
     */
    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $frmSrch = $this->getSearchForm();
        if (!$post = $frmSrch->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frmSrch->getValidationErrors()));
        }
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = gclang.gclang_grpcls_id AND '
                . '  gclang.gclang_lang_id = ' . $this->siteLangId, 'gclang');
        $srch->addMultipleFields(['teacher.user_first_name', 'teacher.user_last_name', 'teacher.user_email',
            'grpcls_type', 'grpcls_id', 'grpcls_slug', 'grpcls_teacher_id', 'grpcls_tlang_id', 'grpcls_start_datetime',
            'grpcls_end_datetime', 'grpcls_teacher_starttime', 'grpcls_teacher_endtime', 'grpcls_total_seats',
            'grpcls_entry_fee', 'grpcls_added_on', 'grpcls_status', 'IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title',
            'IFNULL(gclang.grpcls_description, grpcls.grpcls_description) as grpcls_description']);
        $keyword = trim(FatApp::getPostedData('keyword', null, ''));
        if (!empty($keyword)) {
            $fullname = 'mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)';
            $cnd = $srch->addCondition($fullname, 'like', '%' . $keyword . '%', 'AND', true);
            $cnd->attachCondition('teacher.user_username', 'like', '%' . $keyword . '%');
            $cnd->attachCondition('teacher.user_email', 'like', '%' . $keyword . '%');
            $cnd->attachCondition('grpcls.grpcls_title', 'like', '%' . $keyword . '%');
        }
        if (!empty($post['teacher_id'])) {
            $srch->addCondition('teacher.user_id', '=', $post['teacher_id']);
        } elseif (!empty($post['teacher'])) {
            $fullname = 'mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)';
            $cnd = $srch->addCondition($fullname, 'like', '%' . trim($post['teacher']) . '%', 'AND', true);
            $cnd->attachCondition('teacher.user_username', 'like', '%' . trim($post['teacher']) . '%');
            $cnd->attachCondition('teacher.user_email', 'like', '%' . trim($post['teacher']) . '%');
        }
        if (!empty($post['grpcls_start_datetime'])) {
            $srch->addCondition('grpcls.grpcls_start_datetime', ">=", MyDate::formatToSystemTimezone($post['grpcls_start_datetime'] . ' 00:00:00'), 'AND', true);
        }
        if (!empty($post['grpcls_end_datetime'])) {
            $srch->addCondition('grpcls.grpcls_end_datetime', "<=", MyDate::formatToSystemTimezone($post['grpcls_end_datetime'] . ' 23:59:59'), 'AND', true);
        }
        if (!empty($post['grpcls_type'])) {
            $srch->addCondition('grpcls.grpcls_type', '=', $post['grpcls_type']);
        }
        if (!empty($post['grpcls_status'])) {
            $srch->addCondition('grpcls.grpcls_status', '=', $post['grpcls_status']);
        }
        $page = $post['page'];
        $srch->addCondition('grpcls_parent', '=', 0);
        $srch->addOrder('grpcls.grpcls_start_datetime', 'DESC');
        $srch->setPageSize($pagesize);
        $srch->setPageNumber($page);
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('classes', $classes);
        $this->set('postedData', $post);
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('canEdit', $this->objPrivilege->canEditGroupClasses(true));
        $this->_template->render(false, false);
    }

    /**
     * View Class learners
     * 
     * @param int $classId
     */
    public function learners($classId)
    {
        $classType = GroupClass::getAttributesById($classId, 'grpcls_type');
        if ($classType == GroupClass::TYPE_PACKAGE) {
            $learners = OrderPackage::getLearners($classId);
        } else {
            $learners = OrderClass::getLearners($classId);
        }
        $this->set('learners', $learners);
        $this->_template->render(false, false);
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    protected function getSearchForm(): Form
    {
        $frm = new Form('frmSrch');
        $frm->addHiddenField('', 'teacher_id');
        $frm->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
        $frm->addTextBox(Label::getLabel('LBL_SEARCH_BY_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $frm->addTextBox(Label::getLabel('LBL_TEACHER'), 'teacher');
        $frm->addDateField(Label::getLabel('LBL_START_TIME'), 'grpcls_start_datetime', '', ['readonly' => 'readonly', 'autocomplete' => 'off']);
        $frm->addDateField(Label::getLabel('LBL_END_TIME'), 'grpcls_end_datetime', '', ['readonly' => 'readonly', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBl_CLASS_TYPE'), 'grpcls_type', GroupClass::getClassTypes());
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'grpcls_status', GroupClass::getStatuses());
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnReset = $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear'));
        $btnSubmit->attachField($btnReset);
        return $frm;
    }

}
