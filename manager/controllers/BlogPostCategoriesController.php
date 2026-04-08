<?php

/**
 * Blog Post Category Controller is used for Blog Post Category handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class BlogPostCategoriesController extends AdminBaseController
{

    /**
     * Initialize Blog Post Category
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewBlogPostCategories();
    }

    /**
     * Render Blog Post Categories Search Form
     * 
     * @param int $parent
     */
    public function index($parent = 0)
    {
        $canEdit = $this->objPrivilege->canEditBlogPostCategories(true);
        $parent = FatUtility::int($parent);
        $bpCatData = BlogPostCategory::getAttributesById($parent);
        $searchFrm = $this->getSearchForm();
        $data = ['bpcategory_parent' => $parent];
        $searchFrm->fill($data);
        $this->set('searchFrm', $searchFrm);
        $this->set('canEdit', $canEdit);
        $this->set('bpcategory_parent', $parent);
        $this->set('bpCatData', $bpCatData);
        $this->_template->render();
    }

    /**
     * Search & List Category
     */
    public function search()
    {
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $parent = FatApp::getPostedData('bpcategory_parent', FatUtility::VAR_INT, 0);
        $srch = BlogPostCategory::getSearchObject(true, $this->siteLangId, false);
        $srch->addCondition('bpc.bpcategory_parent', '=', $parent);
        $srch->addOrder('bpc.bpcategory_order', 'asc');
        $srch->addFld('bpc.*');
        if (!empty($post['keyword'])) {
            $keywordCond = $srch->addCondition('bpc.bpcategory_identifier', 'like', '%' . $post['keyword'] . '%');
            $keywordCond->attachCondition('bpc_l.bpcategory_name', 'like', '%' . $post['keyword'] . '%');
        }
        $parentCatData = BlogPostCategory::getAttributesById($parent);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addMultipleFields(["bpcategory_name"]);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $pageCount = $srch->pages();
        $this->set("arr_listing", $records);
        $this->set('pageCount', $pageCount);
        $this->set('parentData', $parentCatData);
        $this->set('postedData', $post);
        $this->set('canEdit', $this->objPrivilege->canEditBlogPostCategories(true));
        $this->_template->render(false, false);
    }

    /**
     * Render Category Form
     * 
     * @param int $bpcategory_id
     * @param int $bpcategory_parent
     */
    public function form($bpcategory_id = 0, $bpcategory_parent = 0)
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $bpcategory_id = FatUtility::int($bpcategory_id);
        $bpcategory_parent = FatUtility::int($bpcategory_parent);
        $frm = $this->getForm($bpcategory_id, $this->siteLangId);
        if (0 < $bpcategory_id) {
            $data = BlogPostCategory::getAttributesById($bpcategory_id);
            if ($data === false) {
                FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
            }
            /* url data[ */
            $urlSrch = new SearchBase(SeoUrl::DB_TBL, 'ur');
            $urlSrch->doNotCalculateRecords();
            $urlSrch->doNotLimitRecords();
            $urlSrch->addFld('seourl_custom');
            $urlSrch->addCondition('seourl_original', '=', 'blog/category/' . $bpcategory_id);
            $rs = $urlSrch->getResultSet();
            $urlRow = FatApp::getDb()->fetch($rs);
            if ($urlRow) {
                $data['seourl_custom'] = $urlRow['seourl_custom'];
            }
            /* ] */
            $frm->fill($data);
        } else {
            $data = ['bpcategory_parent' => $bpcategory_parent];
            $frm->fill($data);
        }
        $this->set('frm', $frm);
        $this->set('bpcategory_id', $bpcategory_id);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    /**
     * Render Category Lang Form
     * 
     * @param int $catId
     * @param int $lang_id
     */
    public function langForm($catId = 0, $lang_id = 0)
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $bpcategory_id = FatUtility::int($catId);
        $lang_id = FatUtility::int($lang_id);
        if ($bpcategory_id == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($bpcategory_id, $lang_id);
        $langData = BlogPostCategory::getAttributesByLangId($lang_id, $bpcategory_id);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('bpcategory_id', $bpcategory_id);
        $this->set('bpcategory_lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    /**
     * Setup Category
     */
    public function setup()
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $frm = $this->getForm(0, $this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $bpcategory_id = FatUtility::int($post['bpcategory_id']);
        $bpcategory_parent = FatUtility::int($post['bpcategory_parent']);
        unset($post['bpcategory_id']);
        $record = new BlogPostCategory($bpcategory_id);
        if ($bpcategory_id == 0) {
            $display_order = $record->getMaxOrder($bpcategory_parent);
            $post['bpcategory_order'] = $display_order;
        }
        $record->assignValues($post);
        if (!$record->save()) {
            FatUtility::dieJsonError($record->getError());
        }
        $bpcategory_id = $record->getMainTableRecordId();
        $newTabLangId = 0;
        if ($bpcategory_id > 0) {
            $catId = $bpcategory_id;
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = BlogPostCategory::getAttributesByLangId($langId, $bpcategory_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $catId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $data = [
            'msg' => Label::getLabel('MSG_Category_Setup_Successful'),
            'catId' => $catId,
            'langId' => $newTabLangId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Category Lang Setup
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $post = FatApp::getPostedData();
        $bpcategory_id = $post['bpcategory_id'];
        $lang_id = $post['lang_id'];
        if ($bpcategory_id == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($bpcategory_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['bpcategory_id']);
        unset($post['lang_id']);
        $data = [
            'bpcategorylang_lang_id' => $lang_id,
            'bpcategorylang_bpcategory_id' => $bpcategory_id,
            'bpcategory_name' => $post['bpcategory_name'],
        ];
        $bpCat = new BlogPostCategory($bpcategory_id);
        if (!$bpCat->updateLangData($lang_id, $data)) {
            FatUtility::dieJsonError($bpCat->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = BlogPostCategory::getAttributesByLangId($langId, $bpcategory_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'msg' => Label::getLabel('MSG_Category_Setup_Successful'),
            'catId' => $bpcategory_id,
            'langId' => $newTabLangId,
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Delete Record
     */
    public function deleteRecord()
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $bpcategory_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($bpcategory_id < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $bpCat = new BlogPostCategory($bpcategory_id);
        if (!$bpCat->canMarkRecordDelete($bpcategory_id)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $bpCat->assignValues([BlogPostCategory::tblFld('deleted') => 1]);
        if (!$bpCat->save()) {
            FatUtility::dieJsonError($bpCat->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /**
     * Update Order
     */
    public function updateOrder()
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $bpCat = new BlogPostCategory();
            if (!$bpCat->updateOrder($post['bpcategory'])) {
                FatUtility::dieJsonError($bpCat->getError());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('MSG_Order_Updated_Successfully'));
        }
    }

    /**
     * Get Bread Crumb Nodes
     * 
     * @param string $action
     * @return type
     */
    public function getBreadcrumbNodes(string $action)
    {
        $nodes = [];
        $parameters = FatApp::getParameters();
        switch ($action) {
            case 'index':
                $nodeData = ['title' => Label::getLabel('LBL_ROOT_CATEGORIES')];
                if (!empty($parameters[0])) {
                    $nodeData['href'] = MyUtility::makeUrl('BlogPostCategories');
                }
                $nodes[] = $nodeData;
                if (isset($parameters[0]) && $parameters[0] > 0) {
                    $parent = FatUtility::int($parameters[0]);
                    if ($parent > 0) {
                        $cntInc = 1;
                        $bpCatObj = new BlogPostCategory();
                        $category_structure = $bpCatObj->getCategoryStructure($parent);
                        foreach ($category_structure as $catKey => $catVal) {
                            if ($cntInc < count($category_structure)) {
                                $nodes[] = ['title' => $catVal["bpcategory_identifier"], 'href' => MyUtility::makeUrl('BlogPostCategories', 'index', [$catVal['bpcategory_id']])];
                            } else {
                                $nodes[] = ['title' => $catVal["bpcategory_identifier"]];
                            }
                            $cntInc++;
                        }
                    }
                }
                break;
            case 'form':
                break;
        }
        return $nodes;
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditBlogPostCategories();
        $bpcategoryId = FatApp::getPostedData('bpcategoryId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $blogPostCategory = new BlogPostCategory($bpcategoryId);
        if (!$blogPostCategory->loadFromDb()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $data = $blogPostCategory->getFlds();
        if (!$blogPostCategory->changeStatus($status)) {
            FatUtility::dieJsonError($blogPostCategory->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Category Form
     * 
     * @param int $bpcategory_id
     * @param int $langId
     * @return Form
     */
    private function getForm(int $bpcategory_id = 0, int $langId): Form
    {
        $bpCatObj = new BlogPostCategory();
        $arrCategories = $bpCatObj->getCategoriesForSelectBox($langId, $bpcategory_id);
        $categories = $bpCatObj->makeAssociativeArray($arrCategories);
        $frm = new Form('frmBlogPostCategory', ['id' => 'frmBlogPostCategory']);
        $frm->addHiddenField('', 'bpcategory_id', 0);
        $frm->addRequiredField(Label::getLabel('LBL_Category_Identifier'), 'bpcategory_identifier');
        $frm->addSelectBox(Label::getLabel('LBL_Category_Parent'), 'bpcategory_parent', [0 => 'Root Category'] + $categories, '', ['class' => 'small'], '');
        $frm->addSelectBox(Label::getLabel('LBL_Category_Status'), 'bpcategory_active', AppConstant::getActiveArr(), '', ['class' => 'small'], '');
        $frm->addCheckBox(Label::getLabel('LBL_Featured'), 'bpcategory_featured', 1, [], false, 0);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Lang Form
     * 
     * @param int $bpcategory_id
     * @param int $lang_id
     * @return Form
     */
    private function getLangForm(int $bpcategory_id = 0, int $lang_id = 0): Form
    {
        $bpcategory_id = FatUtility::int($bpcategory_id);
        $frm = new Form('frmBlogPostCatLang', ['id' => 'frmBlogPostCatLang']);
        $frm->addHiddenField('', 'bpcategory_id', $bpcategory_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Category_Name'), 'bpcategory_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update'));
        return $frm;
    }

    /**
     * Get Search Form
     * 
     * @return \Form
     */
    private function getSearchForm()
    {
        $frm = new Form('frmSearch', ['id' => 'frmSearch']);
        $frm->addHiddenField('', 'bpcategory_parent', 0, ['id' => 'bpcategory_parent']);
        $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword', '', ['class' => 'search-input']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear'));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
