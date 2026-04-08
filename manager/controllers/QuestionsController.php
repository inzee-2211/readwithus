<?php

/**
 * Courses Controller is used for course handling
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class QuestionsController extends AdminBaseController
{

    /**
     * Initialize Courses
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCourses();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
       
        $cateId = FatApp::getQueryStringData('course_cateid') ?? 0;
        $frm = $this->getSearchForm($cateId);
        $frm->fill(FatApp::getQueryStringData());
        $this->set('srchFrm', $frm);
        $this->set('params', FatApp::getQueryStringData());
        $this->_template->render();
    }

    /**
     * Search & List
     */
    public function search()
    {

         
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_subcateid']);
        
        $srch = new QuestionSearch($this->siteLangId, 0, User::SUPPORT);
        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'subcate.cate_id = question  .question_subcat', 'subcate');
        $srch->joinTable(Category::DB_TBL, 'LEFT JOIN', 'cate.cate_id = question  .question_cat', 'cate');
        $srch->joinTable('tbl_users', 'INNER JOIN', 'tutor.user_id = question.question_added_by', 'tutor');
       
        $srch->addMultipleFields([
        '*',           // Select all fields from questions table
        'cate.cate_identifier as  catname',
        'subcate.cate_identifier as subcatname',
        'tutor.user_first_name as fname',
        'tutor.user_last_name as lname'
        ]);
        $srch->applySearchConditionsadmin($post);
      
        $srch->addCondition('question.question_deleted', '=', 0);
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['page']);
       
        $srch->addOrder('question.question_id', 'DESC');
        $orders = $srch->fetchAndFormat();
 


        $this->sets([
            'arrListing' => $orders,
            'page' => $post['page'],
            'post' => $post,
            'pageSize' => $post['pagesize'],
            'pageCount' => $srch->pages(),
            'recordCount' => $srch->recordCount(),
            'canEdit' => $this->objPrivilege->canEditCourses(true),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Course View
     *
     * @param int $courseId
     * return html
     */
    public function view(int $courseId)
    {
        $srch = new QuestionSearch($this->siteLangId, 0, User::SUPPORT);

        $srch->addCondition('question.question_id', '=', $courseId);
        $srch->applyPrimaryConditions();
 
        $courses = $srch->fetchAndFormat();
       
        if (empty($courses)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
 
                $db = FatApp::getDb(); // Get the database connection instance
                $data = ['question_deleted' => 1]; // Column and new value
                
                // Specify the conditions for updating
                $where = ['smt' => 'question_id = ?', 'vals' => [$courseId]];
                
                // Perform the update
                if (!$db->updateFromArray('tbl_questions', $data, $where)) {
                    FatUtility::dieJsonError($db->getError());
                }
                
                // Return success response
                FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Updated_Successfully'));
                

        // $course = current($courses);
        // $this->sets([
        //     'courseData' => $course,
        //     'canEdit' => $this->objPrivilege->canEditCourses(true),
        // ]);
        // $this->_template->render(false, false);
    }

    /**
     * Fetch sub categories for selected category
     *
     * @param int $catgId
     * @param int $subCatgId
     * @return html
     */
    public function getSubcategories(int $catgId, int $subCatgId = 0)
    {
        $catgId = FatUtility::int($catgId);
        $subcategories = [];
        if ($catgId > 0) {
            $subcategories = Category::getCategoriesByParentId($this->siteLangId, $catgId);
        }
        $this->set('subCatgId', $subCatgId);
        $this->set('subcategories', $subcategories);
        $this->_template->render(false, false);
    }

    /**
     * Auto Complete JSON
     */
    public function autoCompleteJson()
    {
        $keyword = FatApp::getPostedData('keyword', FatUtility::VAR_STRING, '');
        if (empty($keyword)) {
            FatUtility::dieJsonSuccess(['data' => []]);
        }
        $srch = new SearchBase(CourseLanguage::DB_TBL, 'clang');
        $srch->joinTable(CourseLanguage::DB_TBL_LANG, 'LEFT JOIN', 'clanglang.clanglang_clang_id = clang.clang_id AND clanglang.clanglang_lang_id = ' . $this->siteLangId, 'clanglang');
        $srch->addMultiplefields(['clang_id', 'IFNULL(clanglang.clang_name, clang.clang_identifier) as clang_name']);
        if (!empty($keyword)) {
            $cond = $srch->addCondition('clanglang.clang_name', 'LIKE', '%' . $keyword . '%');
            $cond->attachCondition('clang.clang_identifier', 'LIKE', '%' . $keyword . '%', 'OR');
        }
        $srch->addCondition('clang.clang_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('clang.clang_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addOrder('clang_name', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(20);
        $data = FatApp::getDb()->fetchAll($srch->getResultSet(), 'clang_id');
        FatUtility::dieJsonSuccess(['data' => $data]);
    }

    /**
     * Update status
     *
     * @param int $courseId
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $courseId, int $status)
    {
        $this->objPrivilege->canEditCourses();
        $courseId = FatUtility::int($courseId);
        $status = FatUtility::int($status);
        $status = ($status == AppConstant::YES) ? AppConstant::NO : AppConstant::YES;
        $course = new Course($courseId);
        $course->setFldValue('course_active', $status);
        if (!$course->save()) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_STATUS_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Get Search Form
     *
     * @param int $cateId
     * @return \Form
     */
    private function getSearchForm(int $cateId = 0): Form
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(
            Label::getLabel('LBL_TITLE'),
            'keyword',
            '',
            ['placeholder' => Label::getLabel('LBL_SEARCH_BY_QUESTION_TITLE')]
        );
        $frm->addTextBox(
            Label::getLabel('LBL_TUTOR'),
            'tutor',
            '',
         //   ['id' => 'course_clang_id', 'autocomplete' => 'off']
         ['placeholder' => Label::getLabel('LBL_SEARCH_BY_TEACHER')]
        );
        $categoryList = Category::getCategoriesByParentId($this->siteLangId, 0, Category::TYPE_COURSE, true);
        $frm->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'course_cateid', $categoryList, '', [], Label::getLabel('LBL_SELECT'));
        $subcategories = [];
        if ($cateId > 0) {
            $subcategories = Category::getCategoriesByParentId($this->siteLangId, $cateId);
        }
        $frm->addSelectBox(Label::getLabel('LBL_SUBCATEGORY'), 'course_subcateid', $subcategories, '', [], Label::getLabel('LBL_SELECT'));
        $frm->addHiddenField('', 'course_clang_id', '', ['id' => 'course_clang_id', 'autocomplete' => 'off']);
       // $frm->addDateField(Label::getLabel('LBL_DATE_FROM'), 'course_addedon_from', '', ['readonly' => 'readonly']);
       // $frm->addDateField(Label::getLabel('LBL_DATE_TO'), 'course_addedon_till', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'order_id');

        $questionTypes = [
             
            '1' => Label::getLabel('LBL_SINGLE_CHOICE'),
            '2' => Label::getLabel('LBL_MULTIPLE_CHOICE'),
            '3' => Label::getLabel('LBL_TEXT_BASED')
        ];
        $frm->addSelectBox(Label::getLabel('LBL_QUESTION_TYPE'), 'grpcls_tlang_id', $questionTypes, '', [], Label::getLabel('LBL_SELECT'));
        
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnSubmit->attachField($frm->addButton('', 'btn_reset', Label::getLabel('LBL_Clear')));
        return $frm;
    }
}
