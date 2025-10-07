<?php

/**
 * Classes Controller is used for Order Classes handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class ClassesController extends AdminBaseController
{

    /**
     * Initialize Classes
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewClassesOrders();
    }

    /**
     * Render Order Class Search Form
     */
    public function index()
    {
        $frm = $this->getSearchForm();
        $frm->fill(FatApp::getPostedData() + FatApp::getQueryStringData());
        $this->set('srchFrm', $frm);
        $this->_template->render();
    }

    /**
     * Search & List Order Class
     */
    public function search()
    {
        $langId = $this->siteLangId;
        $adminId = $this->siteAdminId;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = FatApp::getConfig('CONF_ADMIN_PAGESIZE');
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new ClassSearch($langId, $adminId, User::SUPPORT);
        $srch->addCondition('ordcls.ordcls_id', '>', 0);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('ordcls_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $orders = $srch->fetchAndFormat();
        $this->set('post', $post);
        $this->set('orders', $orders);
        $this->set('recordCount', $srch->recordCount());
        $this->set('canEdit', $this->objPrivilege->canEditClassesOrders(true));
        $this->_template->render(false, false);
    }

    /**
     * Render Order Class View
     */
    public function view()
    {
        $ordclsId = FatApp::getPostedData('ordclsId');
        if (empty($ordclsId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langId = $this->siteLangId;
        $adminId = $this->siteAdminId;
        $srch = new ClassSearch($langId, $adminId, User::SUPPORT);
        $srch->addCondition('ordcls.ordcls_id', '=', $ordclsId);
        $srch->applyPrimaryConditions();
        $srch->addSearchDetailFields();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $orders = $srch->fetchAndFormat();
        if (count($orders) < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->set('order', current($orders));
        $this->_template->render(false, false);
    }

    /**
     * Get Search Form
     * 
     * @return \Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmClassSearch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_SEARCH_BY_KEYWORD')]);
        $frm->addTextBox(Label::getLabel('LBL_LANGUAGE'), 'ordcls_tlang', '', ['id' => 'ordcls_tlang', 'autocomplete' => 'off']);
        $frm->addHiddenField('', 'ordcls_tlang_id', '', ['id' => 'ordcls_tlang_id', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_PAYMENT'), 'order_payment_status', Order::getPaymentArr());
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'ordcls_status', OrderClass::getStatuses())->requirements()->setIntPositive();
        $frm->addDateField(Label::getLabel('LBL_DATE_FROM'), 'order_addedon_from', '', ['readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_DATE_TO'), 'order_addedon_till', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'order_id');
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnSubmit->attachField($frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear')));
        return $frm;
    }
}
