<?php

/**
 * Blog Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class BlogController extends MyAppController
{

    /**
     * Initialize Blog
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->set('blogPage', true);
        $this->set('bodyClass', 'is--blog');
    }

    public function getBreadcrumbNodes($action)
    {
        $nodes = [];
        $className = get_class($this);
        $arr = explode('-', FatUtility::camel2dashed($className));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $className = ucwords(implode(' ', $arr));
        if ($action == 'index') {
            $nodes[] = ['title' => $className];
        } else {
            $nodes[] = ['title' => $className, 'href' => MyUtility::makeUrl($urlController)];
        }
        $parameters = FatApp::getParameters();
        if (!empty($parameters)) {
            if ($action == 'category') {
                $id = reset($parameters);
                $id = FatUtility::int($id);
                $data = BlogPostCategory::getAttributesByLangId($this->siteLangId, $id);
                $title = $data['bpcategory_name'];
                $nodes[] = ['title' => $title];
            } elseif ($action == 'postDetail') {
                $id = reset($parameters);
                $id = FatUtility::int($id);
                $data = BlogPost::getAttributesByLangId($this->siteLangId, $id);
                $title = CommonHelper::truncateCharacters($data['post_title'], 40);
                $nodes[] = ['title' => $title];
            }
        } elseif ($action == 'contributionForm' || $action == 'setupContribution') {
            $nodes[] = ['title' => Label::getLabel('LBL_CONTRIBUTION')];
        }
        return $nodes;
    }

    public function index()
    {
        $this->_template->render();
    }

    public function category($categoryId)
    {
        $categoryId = FatUtility::int($categoryId);
        if ($categoryId < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $this->set('bpCategoryId', $categoryId);
        $this->_template->render(true, true, 'blog/index.php');
    }

public function search()
{
    $post = FatApp::getPostedData();
    $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : FatUtility::int($post['page']);
    $pageSize = AppConstant::PAGESIZE;

    $srch = BlogPost::getSearchObject($this->siteLangId, true, false, true);
    $srch->joinTable(
        BlogPost::DB_LANG_TBL,
        'INNER JOIN',
        'bp_l.postlang_post_id = bp.post_id and bp_l.postlang_lang_id = ' . $this->siteLangId,
        'bp_l'
    );

    $srch->addMultipleFields([
        'bp.*',
        'IFNULL(bp_l.post_title,post_identifier) AS post_title',
        'bp_l.post_author_name',
        'bp_l.post_short_description',
        'GROUP_CONCAT(bpcategory_id) AS categoryIds',
        'GROUP_CONCAT(IFNULL(bpcategory_name, bpcategory_identifier) SEPARATOR "~") AS categoryNames',
        // REPLACEMENT for GETBLOGCATCODE:
        'GROUP_CONCAT(LPAD(bpcategory_id, 6, "0")) AS categoryCodes',
    ]);

    $srch->addCondition('postlang_post_id', 'is not', 'mysql_func_null', 'and', true);

    if ($categoryId = FatApp::getPostedData('categoryId', FatUtility::VAR_INT, 0)) {
        $srch->addCondition('ptc_bpcategory_id', '=', $categoryId);
    }

    $keyword = trim(FatApp::getPostedData('keyword', FatUtility::VAR_STRING, ''));
    if (!empty($keyword)) {
        $keywordCond = $srch->addCondition('post_title', 'like', "%$keyword%");
        $keywordCond->attachCondition('post_short_description', 'like', "%$keyword%");
        $keywordCond->attachCondition('post_description', 'like', "%$keyword%");
    }

    $srch->addCondition('post_published', '=', AppConstant::YES);
    $srch->addOrder('post_added_on', 'desc');
    $srch->setPageSize($pageSize);
    $srch->setPageNumber($page);
    $srch->addGroupby('post_id');

    $rs = $srch->getResultSet();
    if ($rs === false) {
        // extra safety – prevents "fetchAll on bool" and gives a clean JSON error
        FatUtility::dieJsonError(Label::getLabel('MSG_UNABLE_TO_LOAD_BLOG_POSTS'));
    }

    $records = FatApp::getDb()->fetchAll($rs);

    $this->set('page', $page);
    $this->set('pageCount', $srch->pages());
    $this->set("postList", $records);
    $this->set('recordCount', $srch->recordCount());
    $this->set('postedData', $post);

    $json['html'] = $this->_template->render(false, false, 'blog/search.php', true);
    $json['loadMoreBtnHtml'] = $this->_template->render(false, false, 'blog/load-more-btn.php', true, false);

    FatUtility::dieJsonSuccess($json);
}

    public function postDetail($blogPostId)
    {
        $blogPostId = FatUtility::int($blogPostId);
        if ($blogPostId <= 0) {
            FatUtility::exitWithErrorCode(404);
        }
        $file = new Afile(Afile::TYPE_BLOG_POST_IMAGE, $this->siteLangId);
        $this->set('post_images', $file->getFiles($blogPostId));
        $srch = BlogPost::getSearchObject($this->siteLangId, true, true);
        $srch->joinTable(BlogPost::DB_LANG_TBL, 'INNER JOIN', 'bp_l.postlang_post_id = bp.post_id and bp_l.postlang_lang_id = ' . $this->siteLangId, 'bp_l');
        $srch->addCondition('post_id', '=', $blogPostId);
        $srch->addMultipleFields([
            'bp.*', 'IFNULL(bp_l.post_title,post_identifier) as post_title',
            'bp_l.post_author_name', 'bp_l.post_description', 'group_concat(bpcategory_id) categoryIds',
            'group_concat(IFNULL(bpcategory_name, bpcategory_identifier) SEPARATOR "~") categoryNames'
        ]);
        $srchComment = clone $srch;
        $srch->addGroupby('post_id');
        $blogPostData = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($blogPostData)) {
            FatUtility::exitWithErrorCode(404);
        }
        $this->set('blogPostData', $blogPostData);
        $srchComment->addGroupby('bpcomment_id');
        $srchComment->joinTable(
            BlogComment::DB_TBL,
            'inner join',
            'bpcomment.bpcomment_post_id  = post_id and bpcomment.bpcomment_deleted=0',
            'bpcomment'
        );
        $srchComment->addMultipleFields(['bpcomment.*']);
        $srchComment->addCondition('bpcomment_approved', '=', BlogComment::STATUS_APPROVED);
        $commentsResultSet = $srchComment->getResultSet();
        $this->set('commentsCount', $srchComment->recordCount());
        $this->set('blogPostComments', FatApp::getDb()->fetchAll($commentsResultSet));
        if ($blogPostData['post_comment_opened'] && UserAuth::isUserLogged()) {
            $frm = $this->getPostCommentForm($blogPostId);
            $userInfo = User::getDetail($this->siteUserId);
            if (!empty($userInfo)) {
                $frm->getField('bpcomment_author_name')->value = $userInfo['user_first_name'] . ' ' . $userInfo['user_last_name'];
                $frm->getField('bpcomment_author_email')->value = $userInfo['user_email'];
            }
            $this->set('postCommentFrm', $frm);
        }
        $srchCommentsFrm = $this->getCommentSearchForm($blogPostId);
        $this->set('srchCommentsFrm', $srchCommentsFrm);
        $this->_template->addJs(['js/slick.js']);
        $this->_template->render();
    }

    public function setupPostComment()
    {
        if (1 > $this->siteUserId) {
            FatUtility::dieJsonError(Label::getLabel('MSG_USER_NOT_LOGGED'));
        }
        $blogPostId = FatApp::getPostedData('bpcomment_post_id', FatUtility::VAR_INT, 0);
        if ($blogPostId <= 0) {
            FatUtility::dieWithError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getPostCommentForm($blogPostId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
        $enteredAbusiveWordsArr = [];
        if (!Abusive::validateContent($post['bpcomment_content'], $enteredAbusiveWordsArr)) {
            if (!empty($enteredAbusiveWordsArr)) {
                $errStr = Label::getLabel("LBL_Word_{abusiveword}_is/are_not_allowed_to_post");
                $errStr = str_replace("{abusiveword}", '"' . implode(", ", $enteredAbusiveWordsArr) . '"', $errStr);
                FatUtility::dieJsonError($errStr);
            }
        }
        $post['bpcomment_user_id'] = $this->siteUserId;
        $post['bpcomment_added_on'] = date('Y-m-d H:i:s');
        $post['bpcomment_user_ip'] = $_SERVER['REMOTE_ADDR'];
        $post['bpcomment_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $blogComment = new BlogComment();
        $blogComment->assignValues($post);
        if (!$blogComment->save()) {
            FatUtility::dieJsonError($blogComment->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_BLOG_COMMENT_POSTED_AND_AWAITING_ADMIN_APPROVAL.'));
    }

    public function searchComments()
    {
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $blogId = FatApp::getPostedData('blogId', FatUtility::VAR_INT, 0);
        $srch = new SearchBase(BlogPost::DB_TBL, 'bp');
        $srch->joinTable(BlogComment::DB_TBL, 'INNER JOIN', 'bpcomment.bpcomment_post_id = post_id', 'bpcomment');
        $srch->joinTable(Afile::DB_TBL, 'LEFT JOIN', 'file.file_record_id = bpcomment_user_id and file.file_type = ' . Afile::TYPE_USER_PROFILE_IMAGE, 'file');
        $srch->addMultipleFields(['bpcomment.*', 'file_id']);
        $srch->addCondition('bpcomment_approved', '=', BlogComment::STATUS_APPROVED);
        $srch->addCondition(' bpcomment.bpcomment_deleted', '=', AppConstant::NO);
        $srch->addCondition('bp.post_published', '=', AppConstant::ACTIVE);
        $srch->addCondition('post_id', '=', $blogId);
        $srch->setPageSize(AppConstant::PAGESIZE);
        $srch->setPageNumber($page);
        $srch->addOrder('bpcomment_added_on', 'desc');
        $this->sets([
            'blogPostComments' => FatApp::getDb()->fetchAll($srch->getResultSet()),
            'commentsCount' => $srch->recordCount(),
            'page' => $page,
            'blogId' => $blogId,
            'pageCount' => $srch->pages(),
        ]);
        $this->_template->render(false, false);
    }

    public function contributionForm()
    {
        $frm = $this->getContributionForm();
        $userInfo = User::getDetail($this->siteUserId);
        if (!empty($userInfo)) {
            $frm->getField('bcontributions_author_first_name')->value = $userInfo['user_first_name'];
            $frm->getField('bcontributions_author_last_name')->value = $userInfo['user_last_name'];
            $frm->getField('bcontributions_author_email')->value = $userInfo['user_email'];
            $frm->getField('bcontributions_author_phone')->value = $userInfo['user_phone_number'];
        }
        if ($post = FatApp::getPostedData()) {
            $frm->fill($post);
        }
        $this->set('frm', $frm);
        $this->_template->render(true, true, 'blog/contribution-form.php');
    }

    public function setupContribution()
    {
        $frm = $this->getContributionForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData() + $_FILES)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (
            FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' &&
            FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != ''
        ) {
            $recaptcha = FatApp::getPostedData('g-recaptcha-response', FatUtility::VAR_STRING, '');
            if (!CommonHelper::verifyCaptcha($recaptcha)) {
                FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_CAPTCHA'));
            }
        }
        $post['bcontributions_added_on'] = date('Y-m-d H:i:s');
        $post['bcontributions_user_id'] = $this->siteUserId;
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_PLEASE_SELECT_A_FILE'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $contribution = new BlogContribution();
        $contribution->assignValues($post);
        if (!$contribution->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($contribution->getError());
        }
        $contributionId = $contribution->getMainTableRecordId();
        $file = new Afile(Afile::TYPE_BLOG_CONTRIBUTION);
        if (!$file->saveFile($_FILES['file'], $contributionId, true)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($file->getError());
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CONTRIBUTED_SUCCESSFULLY'));
    }

    private function getContributionForm()
    {
        $frm = new Form('frmBlogContribution');
        $frm->addRequiredField(Label::getLabel('LBL_First_Name'), 'bcontributions_author_first_name', '');
        $frm->addRequiredField(Label::getLabel('LBL_Last_Name'), 'bcontributions_author_last_name', '');
        $frm->addEmailField(Label::getLabel('LBL_Email_Address'), 'bcontributions_author_email', '');
        $fld_phn = $frm->addRequiredField(Label::getLabel('LBL_Phone'), 'bcontributions_author_phone');
        $fld_phn->requirements()->setRegularExpressionToValidate('^[\s()+-]*([0-9][\s()+-]*){5,20}$');
        $frm->addFileUpload(Label::getLabel('LBL_Upload_File'), 'file')->requirements()->setRequired(true);
        if (
            FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' &&
            FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != ''
        ) {
            $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' .
                FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT'));
        return $frm;
    }

    private function getPostCommentForm($postId)
    {
        $frm = new Form('frmBlogPostComment');
        $frm->addTextarea(Label::getLabel('LBL_Message'), 'bpcomment_content')->requirements()->setRequired(true);
        $frm->addRequiredField(Label::getLabel('LBL_Name'), 'bpcomment_author_name');
        $frm->addEmailField(Label::getLabel('LBL_Email_Address'), 'bpcomment_author_email', '');
        $frm->addHiddenField('', 'bpcomment_post_id', $postId);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('Btn_Post_Comment'));
        return $frm;
    }

    private function getCommentSearchForm($postId)
    {
        $frm = new Form('frmSearchComments');
        $frm->addHiddenField('', 'page');
        $frm->addHiddenField('', 'post_id', $postId);
        return $frm;
    }
}
