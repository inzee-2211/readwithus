<?php

/**
 * Blog Post Controller is used for Blog Post handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class BlogPostsController extends AdminBaseController
{

    /**
     * Initialize Blog Post 
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewBlogPosts();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $search = $this->getSearchForm();
        $canEdit = $this->objPrivilege->canEditBlogPosts(true);
        $this->set("search", $search);
        $this->set('includeEditor', true);
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    /**
     * Search & List Posts
     */
    public function search()
    {
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = BlogPost::getSearchObject($this->siteLangId);
        $srch->joinTable(BlogPost::DB_LANG_TBL, 'LEFT JOIN', 'bp_l.postlang_post_id = bp.post_id and bp_l.postlang_lang_id = ' . $this->siteLangId, 'bp_l');
        if (!empty($post['keyword'])) {
            $keywordCond = $srch->addCondition('bp.post_identifier', 'like', '%' . $post['keyword'] . '%');
            $keywordCond->attachCondition('bp_l.post_title', 'like', '%' . $post['keyword'] . '%');
        }
        if (isset($post['post_published']) && $post['post_published'] != '') {
            $srch->addCondition('bp.post_published', '=', $post['post_published']);
        }
        $srch->addMultipleFields(['*,ifnull(post_title,post_identifier) post_title , group_concat(ifnull(bpcategory_name ,bpcategory_identifier)) categories']);
        $srch->addGroupby('post_id');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("canEdit", $this->objPrivilege->canEditBlogPosts(true));
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    /**
     * Render Post Form
     * 
     * @param int $postId
     */
    public function form($postId = 0)
    {
        $this->objPrivilege->canEditBlogPosts();
        $postId = FatUtility::int($postId);
        $frm = $this->getForm($postId);
        if (0 < $postId) {
            $data = BlogPost::getAttributesById($postId);
            if (empty($data)) {
                FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
            }
            /* url data[ */
            $urlSrch = new SearchBase(SeoUrl::DB_TBL, 'ur');
            $urlSrch->doNotCalculateRecords();
            $urlSrch->doNotLimitRecords();
            $urlSrch->addFld('seourl_custom');
            $urlSrch->addCondition('seourl_original', '=', 'blog/post-detail/' . $postId);
            $urlRow = FatApp::getDb()->fetch($urlSrch->getResultSet());
            if ($urlRow) {
                $data['seourl_custom'] = $urlRow['seourl_custom'];
            }
            /* ] */
            $frm->fill($data);
        }
        $this->set('frm', $frm);
        $this->set('post_id', $postId);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    /**
     * Render Links Form
     * 
     * @param int $postId
     */
    public function linksForm(int $postId)
    {
        $postId = FatUtility::int($postId);
        $this->set('frmLinks', $this->getLinksForm($postId));
        $this->set('post_id', $postId);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    /**
     * Render Lang Form
     * 
     * @param type $postId
     * @param type $lang_id
     */
    public function langForm($postId = 0, $lang_id = 0)
    {
        $this->objPrivilege->canEditBlogPosts();
        $postId = FatUtility::int($postId);
        $lang_id = FatUtility::int($lang_id);
        if ($postId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($postId, $lang_id);
        $langData = BlogPost::getAttributesByLangId($lang_id, $postId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('post_id', $postId);
        $this->set('post_lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    /**
     * Post Setup
     */
    public function setup()
    {
        $this->objPrivilege->canEditBlogPosts();
        $frm = $this->getForm(0);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $postId = FatUtility::int($post['post_id']);
        $post['post_published_on'] = ($post['post_published']) ? date('Y-m-d H:i:s') : null;
        if ($postId == 0) {
            $post['post_added_on'] = date('Y-m-d H:i:s');
        }
        if ($postId > 0) {
            $blogPost = BlogPost::getAttributesById($postId, ['post_id', 'post_published', 'post_published_on']);
            if (empty($blogPost)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            if ($blogPost['post_published'] == $post['post_published']) {
                $post['post_published_on'] = $blogPost['post_published_on'];
            }
        }
        unset($post['post_id']);


        $post['post_updated_on'] = date('Y-m-d H:i:s');
        $blogPost = new BlogPost($postId);
        $blogPost->assignValues($post);
        if (!$blogPost->save()) {
            FatUtility::dieJsonError($blogPost->getError());
        }
        $postId = $blogPost->getMainTableRecordId();
        /* url data[ */
        $blogOriginalUrl = 'blog/post-detail/' . $postId;
        $blogCustomUrl = CommonHelper::seoUrl($post['seourl_custom']);
        if ($post['seourl_custom'] == '') {
            FatApp::getDb()->deleteRecords(SeoUrl::DB_TBL, ['smt' => 'seourl_original = ?', 'vals' => [$blogOriginalUrl]]);
        } else {
            $urlSrch = new SearchBase(SeoUrl::DB_TBL, 'ur');
            $urlSrch->doNotCalculateRecords();
            $urlSrch->doNotLimitRecords();
            $urlSrch->addFld('seourl_custom');
            $urlSrch->addCondition('seourl_original', '=', $blogOriginalUrl);
            $rs = $urlSrch->getResultSet();
            $urlRow = FatApp::getDb()->fetch($rs);
            $record = new TableRecord(SeoUrl::DB_TBL);
            if ($urlRow) {
                $record->assignValues(['seourl_custom' => $blogCustomUrl]);
                if (!$record->update(['smt' => 'seourl_original = ?', 'vals' => [$blogOriginalUrl]])) {
                    FatUtility::dieJsonError(Label::getLabel("LBL_PLEASE_TRY_DIFFERENT_URL,_URL_ALREADY_USED_FOR_ANOTHER_RECORD."));
                }
            } else {
                $langs = Language::getAllNames();
                foreach ($langs as $langId => $langName) {
                    $record->assignValues([
                        'seourl_original' => $blogOriginalUrl,
                        'seourl_custom' => $blogCustomUrl,
                        'seourl_lang_id' => $langId,
                        'seourl_httpcode' => SeoUrl::HTTP_CODE_301
                    ]);
                    if (!$record->addNew()) {
                        FatUtility::dieJsonError(Label::getLabel("LBL_PLEASE_TRY_DIFFERENT_URL,_URL_ALREADY_USED_FOR_ANOTHER_RECORD."));
                    }
                }
            }
        }
        /* ] */
        $newTabLangId = 0;
        if ($postId > 0) {
            $postId = $postId;
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = BlogPost::getAttributesByLangId($langId, $postId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $postId = $blogPost->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $postObj = new BlogPost();
        $postCategories = $postObj->getPostCategories($postId);
        $data = [
            'msg' => Label::getLabel('MSG_BLOG_POST_SETUP_SUCCESSFUL'),
            'postId' => $postId,
            'langId' => $newTabLangId,
        ];
        if (empty($postCategories)) {
            $data['openLinksForm'] = true;
        }
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Post Lang Setup
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditBlogPosts();
        $post = FatApp::getPostedData();
        $postId = $post['post_id'];
        $lang_id = $post['lang_id'];
        if ($postId == 0 || $lang_id == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($postId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['post_id']);
        unset($post['lang_id']);
        $data = [
            'postlang_lang_id' => $lang_id,
            'postlang_post_id' => $postId,
            'post_title' => $post['post_title'],
            'post_author_name' => $post['post_author_name'],
            'post_short_description' => $post['post_short_description'],
            'post_description' => $post['post_description'],
        ];
        $blogPost = new BlogPost($postId);
        if (!$blogPost->updateLangData($lang_id, $data)) {
            FatUtility::dieJsonError($blogPost->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = BlogPost::getAttributesByLangId($langId, $postId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'msg' => Label::getLabel('MSG_BLOG_POST_SETUP_SUCCESSFUL'),
            'postId' => $postId,
            'langId' => $newTabLangId,
            'openImagesTab' => ($newTabLangId === false) ? true : false,
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Setup Post Categories
     */
    public function setupCategories()
    {
        $this->objPrivilege->canEditBlogPosts();
        $post = FatApp::getPostedData();
        $frm = $this->getLinksForm($post['post_id']);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $postId = $post['post_id'];
        unset($post['post_id']);
        if ($postId <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (empty($post['categories'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_CATEGORIES'));
        }
        $categories = $post['categories'];
        $blogPost = new BlogPost($postId);
        /* link blog post to blog post categories[ */
        if (!$blogPost->addUpdateCategories($postId, $categories)) {
            FatUtility::dieJsonError($blogPost->getError());
        }
        /* ] */
        $newTabLangId = 0;
        if ($postId > 0) {
            $postId = $postId;
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = BlogPost::getAttributesByLangId($langId, $postId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $postId = $blogPost->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $data = [
            'postId' => $postId,
            'langId' => $newTabLangId,
            'msg' => Label::getLabel('MSG_RECORD_UPDATED_SUCCESSFULLY')
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Delete Record
     */
    public function deleteRecord()
    {
        $this->objPrivilege->canEditBlogPosts();
        $postId = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($postId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $blogPost = new BlogPost($postId);
        if (!$blogPost->canMarkRecordDelete()) {
            FatUtility::dieJsonError(Label::getLabel('MSG_UNAUTHORIZED_ACCESS'));
        }
        $blogPost->assignValues([BlogPost::tblFld('deleted') => 1]);
        if (!$blogPost->save()) {
            FatUtility::dieJsonError($blogPost->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /**
     * Render Images Form
     * 
     * @param int $postId
     */
    public function imagesForm($postId)
    {
        $postId = FatUtility::int($postId);
        if (!$postId) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        if (!BlogPost::getAttributesById($postId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_RECORD'));
        }
        $imagesFrm = $this->getImagesFrm($postId);
        $this->set('languages', Language::getAllNames());
        $this->set('post_id', $postId);
        $this->set('imagesFrm', $imagesFrm);
        $this->_template->render(false, false);
    }

    /**
     * Render Post Images 
     * 
     * @param int $postId
     * @param int $langId
     */
    public function images($postId, $langId = 0)
    {
        $postId = FatUtility::int($postId);
        $langId = FatUtility::int($langId);
        if (!$postId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (!BlogPost::getAttributesById($postId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_RECORD'));
        }
        $file = new Afile(Afile::TYPE_BLOG_POST_IMAGE, $langId);
        $post_images = $file->getFiles($postId, false);
        $this->set('languages', Language::getAllNames());
        $this->set("canEdit", $this->objPrivilege->canEditBlogPosts(true));
        $this->set('images', $post_images);
        $this->set('post_id', $postId);
        $this->_template->render(false, false);
    }

    /**
     * Upload Blog Post Images
     * 
     * @param int $postId
     * @param int $langId
     */
    public function uploadBlogPostImages($postId, $langId = 0)
    {
        $this->objPrivilege->canEditBlogPosts();
        $postId = FatUtility::int($postId);
        $langId = FatUtility::int($langId);
        if ($postId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        if (empty($post)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST_OR_FILE_NOT_SUPPORTED'));
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_A_FILE'));
        }
        $file = new Afile(Afile::TYPE_BLOG_POST_IMAGE, $langId);
        if (!$file->saveFile($_FILES['file'], $postId)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_IMAGE_UPLOADED_SUCCESSFULLY'));
    }

    /**
     * Delete Post Image
     * 
     * @param int $postId
     * @param int $fileId
     * @param int $langId
     */
    public function deleteImage($postId = 0, $fileId = 0, $langId = 0)
    {
        $postId = FatUtility::int($postId);
        $fileId = FatUtility::int($fileId);
        $langId = FatUtility::int($langId);
        if (!$postId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $file = new Afile(Afile::TYPE_BLOG_POST_IMAGE, $langId);
        if (!$file->removeById($fileId, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_DELETED_SUCCESSFULLY'));
    }

    /**
     * Get Images Form
     * 
     * @param int $postId
     * @return Form
     */
    private function getImagesFrm($postId = 0): Form
    {
        $frm = new Form('frmBlogPostImage', ['id' => 'imageFrm']);
        $frm->addHiddenField('', 'post_id', $postId);
        $frm->addSelectBox(Label::getLabel('LBL_Language'), 'lang_id', AppConstant::bannerTypeArr(), '', [], '');
        $fld = $frm->addButton(
            Label::getLabel('LBL_Photo(s)'),
            'post_image',
            Label::getLabel('LBL_Upload_Image'),
            ['class' => 'blogFile-Js', 'id' => 'post_image', 'data-file_type' => Afile::TYPE_BLOG_POST_IMAGE, 'data-frm' => 'frmBlogPostImage']
        );
        return $frm;
    }

    /**
     * Get Form
     * 
     * @param int $postId
     * @return Form
     */
    private function getForm($postId = 0): Form
    {
        $postId = FatUtility::int($postId);
        $frm = new Form('frmBlogPost', ['id' => 'frmBlogPost']);
        $frm->addHiddenField('', 'post_id', 0);
        $frm->addRequiredField(Label::getLabel('LBL_Post_Identifier'), 'post_identifier');
        $fld = $frm->addTextBox(Label::getLabel('LBL_SEO_Friendly_URL'), 'seourl_custom');
        $fld->requirements()->setRequired();
        $frm->addSelectBox(Label::getLabel('LBL_Post_Status'), 'post_published', BlogPost::getStatuses(), '', ['class' => 'small'], '');
        $frm->addCheckBox(Label::getLabel('LBL_Comment_Open'), 'post_comment_opened', 1, [], false, 0);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Lang Form
     * 
     * @param int $postId
     * @param int $lang_id
     * @return Form
     */
    private function getLangForm($postId = 0, $lang_id = 0): Form
    {
        $postId = FatUtility::int($postId);
        $frm = new Form('frmBlogPostCatLang', ['id' => 'frmBlogPostCatLang']);
        $frm->addHiddenField('', 'post_id', $postId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Title'), 'post_title');
        $frm->addRequiredField(Label::getLabel('LBL_Post_Author_Name'), 'post_author_name');
        $frm->addTextarea(Label::getLabel('LBL_Short_Description'), 'post_short_description')->requirements()->setRequired(true);
        $frm->addHtmlEditor(Label::getLabel('LBL_Description'), 'post_description')->requirements()->setRequired(true);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update'));
        return $frm;
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmSearch', ['id' => 'frmSearch']);
        $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword', '', ['class' => 'search-input']);
        $frm->addSelectBox(Label::getLabel('LBL_Post_Status'), 'post_published', BlogPost::getStatuses(), '', ['class' => 'small'], 'Select');
        $frm->addHiddenField('', 'page', 1);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear'));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    /**
     * Get Links Form
     * 
     * @param int $postId
     * @return Form
     */
    private function getLinksForm(int $postId): Form
    {
        $postObj = new BlogPost();
        $postCategories = $postObj->getPostCategories($postId);
        $selectedCats = [];
        foreach ($postCategories as $cat) {
            $selectedCats[] = $cat['bpcategory_id'];
        }
        $frm = new Form('frmLinks', ['id' => 'frmLinks']);
        $prodCatObj = new BlogPostCategory();
        $arrOptions = $prodCatObj->getBlogPostCatTreeStructure();
        $fld = $frm->addCheckBoxes(Label::getLabel('LBL_Category'), 'categories', $arrOptions, $selectedCats);
        $frm->addHiddenField('', 'post_id', $postId);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }
}
