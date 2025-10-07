<?php

/**
 * Orders Controller is used for handling Orders
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class OrdersController extends DashboardController
{

    /**
     * Initialize Orders
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Order Search Form
     */
    public function index()
    {
        $this->set('frm', OrderSearch::getSearchForm());
        $this->set('setMonthAndWeekNames', true);
        $this->_template->render();
    }

    /**
     * Search & List Orders
     */
    public function search()
    {
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = OrderSearch::getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new OrderSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('order_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $orders = $srch->fetchAndFormat();
        $this->set('post', $post);
        $this->set('orders', $orders);
        $this->set('recordCount', $srch->recordCount());
        $this->set('pmethods', PaymentMethod::getAll());
        $this->_template->render(false, false);
    }

    /**
     * View Order Detail
     */
    public function view()
    {
        $orderId = FatApp::getPostedData('orderId', FatUtility::VAR_INT, 0);
        $srch = new OrderSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->addCondition('orders.order_id', '=', FatUtility::int($orderId));
        $srch->doNotCalculateRecords();
        $srch->applyPrimaryConditions();
        $srch->addSearchDetailFields();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $orders = $srch->fetchAndFormat();
        if (empty($orders)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $order = current($orders);
        $order['orderPayments'] = OrderPayment::getPaymentsByOrderId($orderId);
        $pendingAmount = 0;
        $totalPaidAmount = array_sum(array_column($order['orderPayments'], 'ordpay_amount'));
        if ($totalPaidAmount < $order["order_net_amount"]) {
            $pendingAmount = $order["order_net_amount"] - $totalPaidAmount;
        }
        $orderObj = new Order($order["order_id"]);
        $subOrders = $orderObj->getSubOrders($order["order_type"], $this->siteLangId);
        $this->sets([
            'order' => $order,
            'subOrders' => $subOrders,
            'pendingAmount' => $pendingAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'pmethods' => PaymentMethod::getPayins(),
            'countries' => Country::getNames($this->siteLangId),
        ]);
        $this->_template->render(false, false);
    }

}
