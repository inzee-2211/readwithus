<?php

/**
 * Order Subscription Controller is used for Order Subscription handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class SubscriptionsController extends AdminBaseController
{

    /**
     * Initialize Order Lesson
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewSubscriptionOrders();
    }

    /**
     * Render Subscriptions Order Search Form
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
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = FatApp::getConfig('CONF_ADMIN_PAGESIZE');
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new SubscriptionSearch($this->siteLangId, $this->siteAdminId, User::SUPPORT);
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->addSearchListingFields();
        $srch->addOrder('ordsub_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $this->set('post', $post);
        $this->set('subscriptions',  $srch->fetchAndFormat());
        $this->set('paymentMethod', OrderPayment::getMethods());
        $this->set('recordCount', $srch->recordCount());
        $this->set('canEdit', $this->objPrivilege->canEditSubscriptionOrders(true));
        $this->_template->render(false, false);
    }

    /**
     * View Lesson Order Detail
     */
    public function view()
    {
        $ordsubId = FatApp::getPostedData('ordsubId');
        if (empty($ordsubId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langId = $this->siteLangId;
        $adminId = $this->siteAdminId;
        $srch = new LessonSearch($langId, $adminId, User::SUPPORT);
        $srch->addCondition('ordsub.ordsub_id', '=', $ordsubId);
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
        $frm = new Form('frmSubsSearch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_SEARCH_BY_KEYWORD')]);
        $frm->addSelectBox(Label::getLabel('LBL_PAYMENT'), 'order_payment_status', Order::getPaymentArr());
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'ordsub_status', Subscription::getStatuses())->requirements();
        $frm->addDateField(Label::getLabel('LBL_START_DATE'), 'ordsub_startdate', '', ['readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_END_DATE'), 'ordsub_enddate', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'order_id');
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnSubmit->attachField($frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear')));
        return $frm;
    }
}
