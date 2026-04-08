<?php

/**
 * Teachers Controller is used for handling Teachers
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class TeachersController extends DashboardController
{

    /**
     * Initialize Teachers
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        MyUtility::setUserType(User::LEARNER);
        parent::__construct($action);
    }

    public function index()
    {
        $this->set('frm', $this->getSearchForm());
        $this->_template->render();
    }

    public function search()
    {
        $frm = $this->getSearchForm();
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new SearchBase(OfferPrice::DB_TBL, 'teofpr');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = teofpr.offpri_teacher_id', 'teacher');
        $srch->addMultipleFields([
            'CONCAT(teacher.user_first_name, " ", teacher.user_last_name) AS teacher_full_name',
            'user_username AS teacher_username',
            'teofpr.offpri_id AS offpri_id', 'teofpr.offpri_classes AS offpri_classes',
            'teofpr.offpri_lessons AS offpri_lessons', 'teofpr.offpri_learner_id AS offpri_learner_id',
            'teofpr.offpri_class_price AS offpri_class_price',
            'teofpr.offpri_lesson_price AS offpri_lesson_price',
            'teofpr.offpri_package_price AS offpri_package_price',
            'teofpr.offpri_teacher_id AS offpri_teacher_id',
        ]);
        $keyword = trim($post['keyword']);
        if (!empty($keyword)) {
            $fullName = 'mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)';
            $srch->addCondition($fullName, 'LIKE', '%' . $keyword . '%', 'AND', true);
        }
        $srch->addCondition('teofpr.offpri_learner_id', '=', $this->siteUserId);
        $srch->addOrder('teofpr.offpri_lessons', 'DESC');
        $srch->addOrder('teofpr.offpri_classes', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $teachers = [];
        $db = FatApp::getDb();
        $rs = $srch->getResultSet();
        while ($record = $db->fetch($rs)) {
            $record['offpri_class_price'] = (!empty($record['offpri_class_price'])) ? json_decode($record['offpri_class_price'], true) : [];
            $record['offpri_lesson_price'] = (!empty($record['offpri_lesson_price'])) ? json_decode($record['offpri_lesson_price'], true) : [];
            $teachers[$record['offpri_id']] = $record;
        }
        $this->set('post', $post);
        $this->set('teachers', $teachers);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Teacher')]);
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'view', 1)->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear'));
        return $frm;
    }

}
