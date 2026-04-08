<?php

/**
 * Group Classes Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class GroupClassesController extends MyAppController
{

    /**
     * Initialize Group Classes
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Group Classes|Packages
     */
    public function index()
    {
        $searchSession = $_SESSION[AppConstant::SEARCH_SESSION] ?? [];
        $frm = GroupClassSearch::getSearchForm($this->siteLangId);
        $frm->fill(FatApp::getPostedData() + $searchSession);
        unset($_SESSION[AppConstant::SEARCH_SESSION]);
        $this->set('srchFrm', $frm);
        $this->_template->render();
    }

    /**
     * Search Group Classes|Packages
     */
    public function search()
    {
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = GroupClassSearch::getSearchForm($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new GroupClassSearch($langId, $userId, $userType);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('grpcls_start_datetime');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $rows = $srch->fetchAndFormat();
        $this->set('classes', $rows);
        $this->set('post', $post);
        $this->set('recordCount', $srch->recordCount());
        $this->set('bookingBefore', FatApp::getConfig('CONF_CLASS_BOOKING_GAP'));
        $this->_template->render(false, false);
    }

    /**
     * Render Group Classes|Packages Detail
     * 
     * @param string $slug
     */
    public function view(string $slug)
    {
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $srch = new GroupClassSearch($langId, $userId, $userType);
        $srch->addCondition('grpcls.grpcls_slug', '=', $slug);
        $srch->applyOrderBy('grpcls_start_datetime');
        $srch->applyPrimaryConditions();
        $srch->addSearchDetailFields();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $rows = $srch->fetchAndFormat();
        if (count($rows) < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $class = current($rows);
        $this->set('class', $class);
        $srch = new GroupClassSearch($langId, $userId, $userType);
        $this->set('moreClasses', $srch->getMoreClasses($class['grpcls_teacher_id'], $class['grpcls_id']));
        $this->set('pkgclses', PackageSearch::getClasses($class['grpcls_id'], $langId));
        $this->set('bookingBefore', FatApp::getConfig('CONF_CLASS_BOOKING_GAP'));
        $this->_template->render();
    }

}
