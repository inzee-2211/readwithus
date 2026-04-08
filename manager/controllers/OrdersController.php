<?php

/**
 * Orders Controller is used for Orders handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class OrdersController extends AdminBaseController
{

    /**
     * Order Initialize
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewOrders();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $frm = $this->getSearchForm();
        $frm->fill(FatApp::getPostedData());
        $this->set('srchFrm', $frm);
        $this->_template->render();
    }

    /**
     * Search & List Orders
     */
    public function search()
    {
        $posts = FatApp::getPostedData();
        $posts['pagesize'] = FatApp::getConfig('CONF_ADMIN_PAGESIZE');
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new OrderSearch($this->siteLangId, $this->siteAdminId, User::SUPPORT);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('order_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $this->sets([
            'orders' => $srch->fetchAndFormat(),
            'post' => $post,
            'recordCount' => $srch->recordCount(),
            'canEdit' => $this->objPrivilege->canEditOrders(true)
        ]);
        $this->_template->render(false, false);
    }

    /**
     * View Order Detail
     * 
     * @param int $orderId
     */
    public function view($orderId = 0)
    {
        $srch = new OrderSearch($this->siteLangId, $this->siteAdminId, User::SUPPORT);
        $srch->addCondition('orders.order_id', '=', FatUtility::int($orderId));
        $srch->applyPrimaryConditions();
        $srch->doNotCalculateRecords();
        $srch->addSearchDetailFields();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $orders = $srch->fetchAndFormat();
        if (empty($orders)) {
            FatUtility::exitWithErrorCode(404);
        }
        $order = current($orders);
        $order['orderPayments'] = OrderPayment::getPaymentsByOrderId($orderId);
        $pendingAmount = 0;
        $totalPaidAmount = array_sum(array_column($order['orderPayments'], 'ordpay_amount'));
        if ($totalPaidAmount < $order["order_net_amount"]) {
            $pendingAmount = $order["order_net_amount"] - $totalPaidAmount;
        }
        $orderObj = new Order($order["order_id"]);
        $childeOrders = current($orderObj->getSubOrders($order["order_type"], $this->siteLangId));
        $form = $this->getPaymentForm($orderId, $pendingAmount);
        $form->fill(['ordpay_order_id' => $orderId, 'ordpay_pmethod_id' => $order['order_pmethod_id']]);
        $this->sets([
            'form' => $form,
            'order' => $order,
            'pendingAmount' => $pendingAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'childeOrderDetails' => $childeOrders,
            'canEdit' => $this->objPrivilege->canEditOrders(true),
            'payins' => array_column(PaymentMethod::getAll(), 'pmethod_code', 'pmethod_id'),
        ]);
        if (class_exists('BankTransferPay')) {
            $this->sets([
                'bankTransfers' => BankTransferPay::getPayments($orderId),
                'bankTransferPay' => PaymentMethod::getByCode(BankTransferPay::KEY),
            ]);
        }
        $this->_template->render();
    }

    /**
     * Update Payment
     */
    public function updatePayment()
    {
        $this->objPrivilege->canEditOrders();
        $orderId = FatApp::getPostedData('ordpay_order_id', FatUtility::VAR_INT, 0);
        $srch = new OrderSearch($this->siteLangId, $this->siteAdminId, User::SUPPORT);
        $srch->addCondition('orders.order_id', '=', FatUtility::int($orderId));
        $srch->addCondition('order_payment_status', '=', Order::UNPAID);
        $srch->addCondition('order_status', '=', Order::STATUS_INPROCESS);
        $srch->addCondition('order_net_amount', '>', 0);
        $srch->applyPrimaryConditions();
        $srch->addSearchDetailFields();
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $orders = $srch->fetchAndFormat();
        if (count($orders) < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $order = current($orders);
        $orderPayments = OrderPayment::getPaymentsByOrderId($orderId);
        $pendingAmount = 0;
        $totalPaidAmount = array_sum(array_column($orderPayments, 'ordpay_amount'));
        if ($totalPaidAmount < $order["order_net_amount"]) {
            $pendingAmount = $order["order_net_amount"] - $totalPaidAmount;
        }
        if (empty($pendingAmount)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $form = $this->getPaymentForm($orderId, $pendingAmount);
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $post['ordpay_amount'] = FatUtility::float($post['ordpay_amount']);
        $orderPayment = new OrderPayment($orderId, $this->siteLangId);
        if (!$orderPayment->paymentSettlements($post['ordpay_txn_id'], $post['ordpay_amount'], $post)) {
            FatUtility::dieJsonError($orderPayment->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_PAYMENT_DETAILS_ADDED_SUCCESSFULLY'));
    }

    /**
     * Cancel Order
     */
    public function cancelOrder()
    {
        $this->objPrivilege->canEditOrders();
        $orderId = FatApp::getPostedData('orderId', FatUtility::VAR_INT, 0);
        $order = new Order($orderId);
        if (!$order->cancelOrder()) {
            FatUtility::dieJsonError($order->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ORDER_CANCELLED_SUCCESSFULLY'));
    }

    public function updateStatus()
    {
        $payId = FatApp::getPostedData('payId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (empty($payId) || empty($status)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $payment = BankTransferPay::getById($payId);
        if (empty($payment)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($status == BankTransferPay::APPROVED) {
            $orderPayment = new OrderPayment($payment['bnktras_order_id'], $this->siteLangId);
            if (!$orderPayment->paymentSettlements($payment['bnktras_txn_id'], $payment['bnktras_amount'], $payment)) {
                FatUtility::dieJsonError($orderPayment->getError());
            }
        } elseif ($status == BankTransferPay::DECLINED) {
            $orderObj = new Order($payment['bnktras_order_id']);
            $order = $orderObj->getOrderToPay();
            $mail = new FatMailer($order['user_lang_id'], 'bank_transfer_payment_declined');
            $mail->setVariables(['{user_name}' => $order['user_name'],
                '{order_id}' => Order::formatOrderId($order['order_id'])]);
            $mail->sendMail([$order['user_email']]);
        }
        $record = new TableRecord(BankTransferPay::DB_TBL);
        $record->setFldValue('bnktras_status', $status);
        if (!$record->update(['smt' => 'bnktras_id = ?', 'vals' => [$payId]])) {
            FatUtility::dieJsonError($record->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ORDER_PAYMENT_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Get Order Purchased Lessons Form
     * 
     * @return Form
     */
    protected function getOrderPurchasedLessonsForm(): Form
    {
        $frm = new Form('orderPurchasedLessonsSearchForm');
        $arr_options = ['-1' => Label::getLabel('LBL_Does_Not_Matter')] + AppConstant::getYesNoArr();
        $frm->addTextBox(Label::getLabel('LBL_Teacher'), 'teacher', '', ['id' => 'teacher', 'autocomplete' => 'off']);
        $frm->addTextBox(Label::getLabel('LBL_Learner'), 'learner', '', ['id' => 'learner', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_Free_Trial'), 'op_lpackage_is_free_trial', $arr_options, -1, [], '');
        $frm->addSelectBox(Label::getLabel('Payment Status'), 'order_payment_status', Order::getPaymentArr(), -2, [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Class_Type'), 'class_type', AppConstant::getClassTypes());
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'order_user_id', '');
        $frm->addHiddenField('', 'op_teacher_id', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search'));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $orderType = Order::getTypeArr();
        $frm = new Form('frmOrderSearch');
        $frm->addHiddenField('', 'order_user_id', '', ['id' => 'order_user_id', 'autocomplete' => 'off']);
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $frm->addTextBox(Label::getLabel('LBL_USER'), 'order_user', '', ['id' => 'order_user', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_ORDER_TYPE'), 'order_type', $orderType)->requirements()->setIntPositive();
        $frm->addSelectBox(Label::getLabel('LBL_PAYMENT'), 'order_payment_status', Order::getPaymentArr());
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'order_status', Order::getStatusArr());
        $frm->addDateField(Label::getLabel('LBL_DATE_FROM'), 'date_from', '', ['readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_DATE_TO'), 'date_to', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEARCH'));
        $btnSubmit->attachField($frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_CLEAR')));
        return $frm;
    }

    /**
     * Get Payment Form
     * 
     * @param int $orderId
     * @param float $netAmount
     * @return Form
     */
    private function getPaymentForm(int $orderId, float $netAmount): Form
    {
        $form = new Form('frmPayment');
        $form->addHiddenField('', 'ordpay_order_id', $orderId);
        $form->addSelectBox(Label::getLabel('LBL_PAYMENT_METHOD'), 'ordpay_pmethod_id', PaymentMethod::getPayins())
                ->requirements()->setRequired(true);
        $form->addRequiredField(Label::getLabel('LBL_TXN_ID'), 'ordpay_txn_id');
        $amountFld = $form->addRequiredField(Label::getLabel('LBL_AMOUNT'), 'ordpay_amount');
        $amountFld->requirements()->setFloatPositive(true);
        $amountFld->requirements()->setRange($netAmount, $netAmount);
        $form->addTextArea(Label::getLabel('LBL_COMMENTS'), 'ordpay_response', '')->requirements()->setRequired();
        $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $form;
    }

}
