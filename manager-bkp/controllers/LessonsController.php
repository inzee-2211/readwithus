<?php

/**
 * Order Lesson Controller is used for Order Lesson handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class LessonsController extends AdminBaseController
{

    /**
     * Initialize Order Lesson
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewLessonsOrders();
    }

    /**
     * Render Lesson Order Search Form
     */
    public function index()
    {
        $frm = $this->getSearchForm();
        $frm->fill(FatApp::getPostedData() + FatApp::getQueryStringData());
        $this->set('srchFrm', $frm);
        $this->_template->render();
    }

    /**
     * Search & List Lesson Orders
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
        $srch = new LessonSearch($langId, $adminId, User::SUPPORT);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('ordles_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $orders = $srch->fetchAndFormat();
        $this->set('post', $post);
        $this->set('orders', $orders);
        $this->set('paymentMethod', OrderPayment::getMethods());
        $this->set('recordCount', $srch->recordCount());
        $this->set('canEdit', $this->objPrivilege->canEditLessonsOrders(true));
        $this->_template->render(false, false);
    }

    /**
     * View Lesson Order Detail
     */
    public function view()
    {
        $ordlesId = FatApp::getPostedData('ordlesId');
        if (empty($ordlesId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langId = $this->siteLangId;
        $adminId = $this->siteAdminId;
        $srch = new LessonSearch($langId, $adminId, User::SUPPORT);
        $srch->addCondition('ordles.ordles_id', '=', $ordlesId);
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
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmLessonSearch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_SEARCH_BY_KEYWORD')]);
        $frm->addTextBox(Label::getLabel('LBL_LANGUAGE'), 'ordles_tlang', '', ['id' => 'ordles_tlang', 'autocomplete' => 'off']);
        $frm->addHiddenField('', 'ordles_tlang_id', '', ['id' => 'ordles_tlang_id', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_PAYMENT'), 'order_payment_status', Order::getPaymentArr());
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'ordles_status', Lesson::getStatuses())->requirements()->setIntPositive();
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
