<?php

/**
 * Categories Controller
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CategoriesController extends AdminBaseController
{

    /**
     * Initialize Categories
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCategories();
    }

    /**
     * Render Search Form
     *
     * @param int $cateId
     */
    public function index($cateId = 0)
    {
        $frm = $this->getSearchForm();
        $frm->fill(['parent_id' => $cateId]);
        $this->sets([
            "frmSearch" => $frm,
            "canEdit" => $this->objPrivilege->canEditCategories(true),
            "parentId" => $cateId
        ]);
        $this->_template->render();
    }

    /**
     * Search & List Categories
     */
    public function search()
    {
        $form = $this->getSearchForm();
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }

        $srch = Category::getSearchObject();
        $srch->joinTable(
            Category::DB_LANG_TBL,
            'LEFT OUTER JOIN',
            'catg.cate_id = catg_l.catelang_cate_id AND catg_l.catelang_lang_id = ' . $this->siteLangId,
            'catg_l'
        );
        $srch->addCondition('cate_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        if (isset($post['parent_id'])) {
            $srch->addCondition('cate_parent', '=', $post['parent_id']);
        }
        if (isset($post['cate_type']) && $post['cate_type'] > 0) {
            $srch->addCondition('catg.cate_type', '=', $post['cate_type']);
        }
        $srch->addMultipleFields(
            [
                'catg.cate_id',
                'catg.cate_type',
                'catg.cate_parent',
                'catg.cate_subcategories',
                'catg.cate_records',
                'catg.cate_status',
                'catg.cate_created',
                'IFNULL(catg_l.cate_name, catg.cate_identifier) AS cate_name',
                'catg.cate_identifier',
                'catg_l.catelang_lang_id',
            ]
        );
        $srch->doNotCalculateRecords();
        $srch->addOrder('cate_status', 'DESC');
        $srch->addOrder('cate_order');
        $data = FatApp::getDb()->fetchAll($srch->getResultSet(), 'cate_id');
        $this->sets([
            'arrListing' => $data,
            'postedData' => $post,
            'canEdit' => $this->objPrivilege->canEditCategories(true),
            'canViewCourses' => $this->objPrivilege->canViewCourses(true)
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Categories Form
     *
     * @param int $categoryId
     * @param int $langId
     */
    public function form(int $categoryId)
    {
        $this->objPrivilege->canEditCategories();
        $categoryId = FatUtility::int($categoryId);
        $data = [];
        if ($categoryId > 0) {
            $category = new Category($categoryId);
            $data = $category->getDataById();
            if (count($data) < 1) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
        }
        $frm = $this->getForm($categoryId);
        $frm->fill($data);
        $this->sets([
            'frm' => $frm,
            'data' => $data,
            'categoryId' => $categoryId,
            'languages' => Language::getAllNames(),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Categories
     */
    public function setup()
    {
        $this->objPrivilege->canEditCategories();
        $frm = $this->getForm($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['cate_parent'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $category = new Category($post['cate_id']);
        if ($post['cate_id'] > 0) {
            if (!$data = $category->getDataById()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_CATEGORY_NOT_FOUND'));
            }
            if ($post['cate_parent'] > 0 && $data['cate_subcategories'] > 0) {
                FatUtility::dieJsonError(Label::getLabel('LBL_CANNOT_ASSIGN_PARENT_AS_THIS_CATEGORY_HAS_ITS_OWN_SUBCATEGORIES'));
            }
        }
        if (!$category->setup($post)) {
            FatUtility::dieJsonError($category->getError());
        }
        FatUtility::dieJsonSuccess([
            'cateId' => $category->getMainTableRecordId(),
            'msg' => Label::getLabel('MSG_SETUP_SUCCESSFUL')
        ]);
    }

    /**
     * Language Form
     * 
     * @param int $categoryId
     * @param int $langId
     */
    public function langForm(int $categoryId = 0, int $langId = 0)
    {
        $this->objPrivilege->canEditCategories();
        if (!Category::getAttributesById($categoryId, ['cate_id'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* get lang data */
        $srch = new SearchBase(Category::DB_LANG_TBL);
        $srch->addCondition('catelang_lang_id', '=', $langId);
        $srch->addCondition('catelang_cate_id', '=', $categoryId);
        $srch->addMultipleFields(['cate_name', 'cate_details', 'catelang_id']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $data = FatApp::getDb()->fetch($srch->getResultSet());
        /* fill form data */
        $frm = $this->getLangForm($langId);
        $frm->fill($data);
        $frm->fill(['catelang_lang_id' => $langId, 'catelang_cate_id' => $categoryId]);
        $this->sets([
            'categoryId' => $categoryId,
            'frm' => $frm,
            'formLayout' => Language::getLayoutDirection($langId),
            'languages' => Language::getAllNames(),
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Lang Data
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditCategories();
        $frm = $this->getLangForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!Category::getAttributesById($post['catelang_cate_id'], 'cate_id')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $category = new Category($post['catelang_cate_id']);
        if (!$category->addUpdateLangData($post)) {
            FatUtility::dieJsonError($category->getError());
        }
        FatUtility::dieJsonSuccess([
            'cateId' => $post['catelang_cate_id'],
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL')
        ]);
    }

    /**
     * Delete category
     *
     * @param int $cateId
     */
    public function delete(int $cateId)
    {
        $this->objPrivilege->canEditCategories();
        $category = new Category($cateId);
        if (!$category->delete()) {
            FatUtility::dieJsonError($category->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /**
     * Get Form
     *
     * @return Form
     */
    private function getForm(int $catgId = 0): Form
    {
        $frm = new Form('frmCategory');
        $fld = $frm->addHiddenField('', 'cate_id');
        $fld->requirements()->setIntPositive();
        $fld = $frm->addTextBox(Label::getLabel('LBL_IDENTIFIER'), 'cate_identifier')->requirements()->setRequired();
        $fld = $frm->addHiddenField('', 'cate_type', Category::TYPE_COURSE);
        $fld->requirements()->setIntPositive();
        $parentCategories = Category::getCategoriesByParentId(
            $this->siteLangId, 0, Category::TYPE_COURSE, false, false
        );
        if ($catgId > 0) {
            unset($parentCategories[$catgId]);
        }
        $fld = $frm->addSelectBox(Label::getLabel('LBL_PARENT'), 'cate_parent', $parentCategories, '', [], Label::getLabel('LBL_ROOT_CATEGORY'));
        $fld->requirements()->setInt();
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'cate_status', AppConstant::getActiveArr(), '', [], '')
        ->requirements()
        ->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    /**
     * Get Language form
     *
     * @param int $langId
     */
    private function getLangForm(int $langId = 0)
    {
        $frm = new Form('frmLang');
        $frm->addHiddenField('', 'catelang_id');
        $frm->addHiddenField('', 'catelang_cate_id');
        $frm->addHiddenField('', 'catelang_lang_id');
        $frm->addTextBox(Label::getLabel('LBL_NAME', $langId), 'cate_name')->requirements()->setRequired();
        $frm->addTextarea(Label::getLabel('LBL_DESCRIPTION', $langId), 'cate_details')->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES', $langId));
        return $frm;
    }

    /**
     * Get Search Form
     *
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('categorySearch');
        $frm->addHiddenField('', 'parent_id', '');
        $frm->addHiddenField(Label::getLabel('LBL_TYPE'), 'cate_type', Category::TYPE_COURSE);
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'));
        return $frm;
    }

    /**
     * Update status
     *
     * @param int $cateId
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $cateId, int $status)
    {
        $this->objPrivilege->canEditCategories();
        $cateId = FatUtility::int($cateId);
        $status = FatUtility::int($status);
        $status = ($status == AppConstant::YES) ? AppConstant::NO : AppConstant::YES;

        if ($cateId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $category = new Category($cateId);
        $category->setFldValue('cate_status', $status);
        if (!$category->updateStatus()) {
            FatUtility::dieJsonError($category->getError());
        }
        
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_STATUS_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Update Sort Order
     *
     * @param int $onDrag
     * @return json
     */
    public function updateOrder(int $onDrag = 1)
    {
        $this->objPrivilege->canEditCategories();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $cateObj = new Category();
            if (!$cateObj->updateOrder($post['categoriesList'])) {
                FatUtility::dieJsonError($cateObj->getError());
            }
            if ($onDrag == 0) {
                FatUtility::dieJsonSuccess('');
            } else {
                FatUtility::dieJsonSuccess(Label::getLabel('LBL_Order_Updated_Successfully'));
            }
        }
    }

    public function getBreadcrumbNodes($action)
    {
        $nodes = [];
        $parameters = FatApp::getParameters();
        if (isset($parameters[0]) && $parameters[0] > 0) {
            $row = Category::getNames([$parameters[0]], $this->siteLangId);
            $nodes = [
                [
                    'title' => Label::getLabel('LBL_ROOT_CATEGORIES'),
                    'href' => MyUtility::generateUrl('categories', 'index')
                ],
                [
                    'title' => $row[$parameters[0]],
                ]
            ];
        } else {
            $nodes = [['title' => Label::getLabel('LBL_ROOT_CATEGORIES')]];
        }
        return $nodes;
    }
}
