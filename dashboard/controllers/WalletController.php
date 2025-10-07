<?php

/**
 * Wallet Controller is used for handling Wallet
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class WalletController extends DashboardController
{

    /**
     * Initialize Wallet
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $this->set('setMonthAndWeekNames', true);
        $this->set('frm', $this->getSearchForm());
        $this->set('balance', User::getWalletBalance($this->siteUserId));
        $this->_template->render();
    }

    /**
     * Search Wallet Txns
     */
    public function search()
    {
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new SearchBase(Transaction::DB_TBL);
        $srch->addCondition('usrtxn_user_id', '=', $this->siteUserId);
        $srch->addOrder('usrtxn_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        if (!empty($post['keyword'])) {
            $keyword = strtoupper(substr($post['keyword'], 0, 2));
            if ($keyword == 'TN') {
                $cond = $srch->addCondition('usrtxn_id', '=', ltrim(str_replace('TN-', '', $post['keyword']), '0'));
            } else {
                $cond = $srch->addCondition('usrtxn_id', 'LIKE', '%' . $post['keyword'] . '%');
            }
            $cond->attachCondition('usrtxn_comment', 'LIKE', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['date_from'])) {
            $srch->addCondition('usrtxn_datetime', '>=', MyDate::formatDate($post['date_from'] . ' 00:00:00'));
        }
        if (!empty($post['date_to'])) {
            $srch->addCondition('usrtxn_datetime', '<=', MyDate::formatDate($post['date_to'] . ' 23:59:59'));
        }
        $txns = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('recordCount', $srch->recordCount());
        $this->set('post', $post);
        $this->set('txns', $txns);
        $this->_template->render(false, false);
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('txnSrchFrm');
        $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword', '');
        $frm->addDateField(Label::getLabel('LBL_Date_From'), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To'), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear'));
        return $frm;
    }

    /**
     * Render Add Money Form
     */
    public function addMoney()
    {
        $this->set('form', $this->getAddMoneyForm());
        $this->set('currency', MyUtility::getSystemCurrency());
        $this->_template->render(false, false);
    }

    /**
     * Setup Add Money
     */
    public function setupAddMoney()
    {
        $frm = $this->getAddMoneyForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $minamount = FatApp::getConfig('MINIMUM_WALLET_RECHARGE_AMOUNT');
        if ($post['amount'] < $minamount) {
            $label = Label::getLabel("LBL_MINIMUM_WALLET_RECHARGE_{minamount}");
            FatUtility::dieJsonError(str_replace("{minamount}", $minamount, $label));
        }
        $order = new Order(0, $this->siteUserId);
        if (!$order->placeWalletOrder($post['amount'], $post['pmethod_id'])) {
            FatUtility::dieJsonError($order->getError());
        }
        $url = MyUtility::makeUrl('Payment', 'charge', [$order->getMainTableRecordId()], CONF_WEBROOT_FRONT_URL);
        FatUtility::dieJsonSuccess(['msg' => Label::getLabel('MSG_REDIRECTING_TO_PAYMENT_GATEWAY'), 'redirectUrl' => $url]);
    }

    /**
     * Get Add Money Form
     * 
     * @return Form
     */
    private function getAddMoneyForm(): Form
    {
        $currency = MyUtility::getSystemCurrency();
        $placeHolder = $currency['currency_symbol_left'] . '0.00' . $currency['currency_symbol_right'];
        $payins = PaymentMethod::getPayins(false);
        $str = Label::getLabel('LBL_ENTER_AMOUNT_TO_BE_ADDED_[{currencycode}]');
        $label = str_replace("{currencycode}", $currency['currency_code'], $str);
        $frm = new Form('frmAddMoney');
        $fld = $frm->addSelectBox(Label::getLabel('LBL_METHODS'), 'pmethod_id', $payins, array_key_first($payins), [], '');
        $fld->requirements()->setRequired();
        $fld = $frm->addRequiredField($label, 'amount', '', ['placeholder' => $placeHolder]);
        $fld->requirements()->setFloatPositive();
        $fld->requirements()->setRange(FatApp::getConfig('MINIMUM_WALLET_RECHARGE_AMOUNT'), 999999);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }

    /**
     * Reedem Giftcard
     */
    public function reedemGiftcard()
    {
        $frm = $this->getGiftcardRedeemForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $giftcard = new Giftcard();
        if (!$giftcard->redeem($post['giftcard_code'], $this->siteUserId)) {
            FatUtility::dieJsonError($giftcard->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_GIFTCARD_REDEEM_SUCCESSFULLY'));
    }

    /**
     * Render Giftcard Redeem Form
     */
    public function giftcardRedeemForm()
    {
        $this->set('frm', $this->getGiftcardRedeemForm());
        $this->_template->render(false, false);
    }

    /**
     * Get Giftcard Redeem Form
     * 
     * @return Form
     */
    private function getGiftcardRedeemForm(): Form
    {
        $frm = new Form('giftCardReeedem');
        $frm->addFormTagAttribute('class', 'form');
        $frm->addFormTagAttribute('onsubmit', 'giftcardRedeem(this); return(false);');
        $giftcard = $frm->addTextBox(Label::getLabel('LBL_GiftCard_Code'), 'giftcard_code', '', ['placeholder' => Label::getLabel('LBL_Enter_Gift_Card_Code')]);
        $giftcard->requirements()->setRequired();
        $frm->addButton("", "btn_cancel", Label::getLabel('LBL_Cancel'));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Redeem'), ['class' => 'btn btn--primary block-on-mobile']);
        return $frm;
    }

    /**
     * Request Withdrawal
     */
    public function requestWithdrawal()
    {
        $lastWithdrawal = User::getLastWithdrawal($this->siteUserId);
        $balance = User::getWalletBalance($this->siteUserId);
        if ($lastWithdrawal && (strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS", FatUtility::VAR_INT, 0) . " days") - time()) > 0) {
            $nextWithdrawalDate = date('Y-m-d', strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS", FatUtility::VAR_INT, 0) . " days"));
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_WITHDRAWAL_REQUEST_DATE'), MyDate::formatDate($lastWithdrawal["withdrawal_request_date"]), MyDate::formatDate($nextWithdrawalDate), FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS")));
        }
        $minimumWithdrawLimit = FatApp::getConfig("CONF_MIN_WITHDRAW_LIMIT", FatUtility::VAR_INT, 0);
        if ($balance < $minimumWithdrawLimit) {
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_%s_Minimum_Balance_Required'), MyUtility::formatMoney($minimumWithdrawLimit)));
        }
        $payoutMethodId = FatApp::getPostedData('methodId', FatUtility::VAR_INT, 0);
        $frm = $this->getWithdrawalForm($payoutMethodId, $balance);
        $data = (new User($this->siteUserId))->getUserBankInfo();
        $frm->fill($data);
        $this->sets(['frm' => $frm]);
        $this->_template->render(false, false);
    }

    /**
     *
     * @param int $payoutMethodId
     * @param int $langId
     * @return void
     */
    private function getWithdrawalForm(int $payoutMethodId, $balance = 0)
    {
        $withdrawlMethodArray = PaymentMethod::getPayouts();
        if (empty($withdrawlMethodArray)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_NO_PAYMENT_METHOD_ACTIVE_YET'));
        }
        $txnFeeArr = array_column($withdrawlMethodArray, 'pmethod_fees', 'pmethod_id');
        $paymentMethod = array_column($withdrawlMethodArray, 'pmethod_code', 'pmethod_id');
        if (!empty($payoutMethodId) && !array_key_exists($payoutMethodId, $paymentMethod)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        if (empty($payoutMethodId)) {
            $payoutMethodId = array_key_first($paymentMethod);
        }
        $txnFee = json_decode($txnFeeArr[$payoutMethodId], true);
        $pmethodModel = $paymentMethod[$payoutMethodId];
        foreach ($paymentMethod as $key => $value) {
            $paymentMethod[$key] = Label::getLabel('LBL_' . $value);
        }
        $form = $pmethodModel::getWithdrawalForm($paymentMethod);
        $form->getField('withdrawal_payment_method_id')->value = $payoutMethodId;
        $withdrawalAmountFld = $form->getField('withdrawal_amount');
        $withdrawalAmountAfterHTML = "<small>" . Label::getLabel('LBL_CURRENT_WALLET_BALANCE') . ' ' . MyUtility::formatMoney($balance) . "</small>";
        $payoutFee = MyUtility::formatMoney($txnFee['fee']);
        if ($txnFee['type'] == AppConstant::PERCENTAGE) {
            $payoutFee = MyUtility::formatPercent($txnFee['fee']);
        }
        if ($txnFee['fee'] > 0) {
            $withdrawalAmountAfterHTML .= "<small class='-color-secondary transaction-fee'>" . Label::getLabel('LBL_Transaction_Fee') . ' ' . $payoutFee . '</small>';
        }
        $withdrawalAmountFld->htmlAfterField = $withdrawalAmountAfterHTML;
        return $form;
    }

    /**
     * Setup Request Withdrawal
     */
    public function setupRequestWithdrawal()
    {
        $userId = $this->siteUserId;
        $balance = User::getWalletBalance($userId);
        $lastWithdrawal = User::getLastWithdrawal($userId);
        if ($lastWithdrawal && (strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS") . " days") - time()) > 0) {
            $nextWithdrawalDate = date('Y-d-m', strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS") . " days"));
            FatUtility::dieJsonError(sprintf(
                            Label::getLabel('MSG_Withdrawal_Request_Date'),
                            MyDate::formatDate($lastWithdrawal["withdrawal_request_date"]),
                            MyDate::formatDate($nextWithdrawalDate),
                            FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS")
            ));
        }
        $minimumWithdrawLimit = FatApp::getConfig("CONF_MIN_WITHDRAW_LIMIT");
        if ($balance < $minimumWithdrawLimit) {
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_WITHDRAWAL_REQUEST_MINIMUM_BALANCE_LESS_%s'), MyUtility::formatMoney($minimumWithdrawLimit)));
        }
        $payoutMethodId = FatApp::getPostedData('withdrawal_payment_method_id', FatUtility::VAR_INT, 0);
        $frm = $this->getWithdrawalForm($payoutMethodId, $this->siteLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $pmethodCodeFld = $frm->getField('pmethod_code');
        $post['pmethod_code'] = $pmethodCode = $pmethodCodeFld->value;
        if (($minimumWithdrawLimit > $post["withdrawal_amount"])) {
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_WITHDRAWAL_REQUEST_LESS_%s'), MyUtility::formatMoney($minimumWithdrawLimit)));
        }
        if (($post["withdrawal_amount"] > $balance)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_WITHDRAWAL_REQUEST_GREATER_THAN_BALANCE'));
        }
        $txnfee = (new PaymentMethod($payoutMethodId))->getTransactionFee();
        $fee = FatUtility::float($txnfee['fee'] ?? 0);
        if (!empty($txnfee) && $txnfee['fee'] > 0 && $txnfee['type'] == AppConstant::PERCENTAGE) {
            $fee = FatUtility::float(($txnfee['fee'] / 100) * $post["withdrawal_amount"]);
        }
        $amount = $post['withdrawal_amount'] - $fee;
        if (0 >= $amount) {
            FatUtility::dieJsonError(Label::getLabel('MSG_AMOUNT_MUST_BE_GREATER_THEN_GATEWAY_FEE'));
        }
        $post['withdrawal_transaction_fee'] = $fee;
        $userObj = new User($userId);
        $saveInfoFunction = ($pmethodCode == BankPayout::KEY) ? 'updateBankInfo' : 'updatePaypalInfo';
        if (!$userObj->$saveInfoFunction($post)) {
            Label::getLabel($userObj->getError());
        }
        if (!$withdrawRequestId = $userObj->addWithdrawalRequest(array_merge($post, ["ub_user_id" => $userId]), $this->siteLangId)) {
            FatUtility::dieJsonError($userObj->getError());
        }
        $withdrawRequestData = $post;
        $withdrawRequestData['txn_id'] = WithdrawRequest::formatRequestNumber($withdrawRequestId);
        $withdrawRequestData['user_first_name'] = $this->siteUser['user_first_name'];
        $withdrawRequestData['user_last_name'] = $this->siteUser['user_last_name'];
        $withdrawRequestData['user_email'] = $this->siteUser['user_email'];
        $withdrawRequestData['payout_type'] = ($pmethodCode == BankPayout::KEY) ? Label::getLabel('Lbl_Bank_Payout') : Label::getLabel('Lbl_Paypal_Payout');
        $fatTemplate = new FatTemplate(' ', ' ');
        $fatTemplate->set('data', $withdrawRequestData);
        $fatTemplate->set('pmethodCode', $pmethodCode);
        $withdrawRequestData['other_details'] = $fatTemplate->render(false, false, 'wallet/withdrawal-request-mail.php', true);
        $this->sendWithdrawRequestEmail($withdrawRequestData);
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_WITHDRAW_REQUEST_PLACED_SUCCESSFULLY'));
    }

    /**
     * Send Withdraw Request Email
     * 
     * @param array $data
     */
    private function sendWithdrawRequestEmail(array $data)
    {
        $data['withdrawal_request_date'] = date('Y-m-d');
        $vars = [
            '{txn_id}' => $data['txn_id'],
            '{user_first_name}' => $data['user_first_name'],
            '{user_last_name}' => $data['user_last_name'],
            '{payout_type}' => $data['payout_type'],
            '{request_date}' => $data['withdrawal_request_date'],
            '{withdrawal_amount}' => MyUtility::formatMoney($data['withdrawal_amount']),
            '{other_details}' => $data['other_details'],
            '{time_offset}' => "(" . CONF_SERVER_TIMEZONE . " " . MyDate::getOffset(CONF_SERVER_TIMEZONE) . ")",
            '{withdrawal_comment}' => $data['withdrawal_comments']
        ];
        $to = FatApp::getConfig('CONF_SITE_OWNER_EMAIL');
        if (!empty($to)) {
            $mail = new FatMailer($this->siteLangId, 'new_withdrawal_request_mail_to_admin');
            $mail->setVariables($vars);
            $mail->sendMail([$to]);
        }
        $vars['request_date'] = MyDate::formatDate($data['withdrawal_request_date'], 'Y-m-d');
        $mail = new FatMailer($this->siteLangId, 'new_withdrawal_request_mail_to_user');
        $mail->setVariables($vars);
        $mail->sendMail([$data['user_email']]);
    }

    public function withdrawRequests()
    {
        $this->set('setMonthAndWeekNames', true);
        $this->set('frm', $this->getSearchWithdrawRequestsForm());
        $this->set('canWithdraw', User::getWalletBalance($this->siteUserId) >= FatApp::getConfig("CONF_MIN_WITHDRAW_LIMIT"));
        $this->_template->render();
    }

    public function searchWithdrawRequests()
    {
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = $this->getSearchWithdrawRequestsForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new SearchBase(WithdrawRequest::DB_TBL);
        $srch->addCondition('withdrawal_user_id', '=', $this->siteUserId);
        $srch->addOrder('withdrawal_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        if (!empty($post['keyword'])) {
            $keyword = strtoupper(substr($post['keyword'], 0, 1));
            if ($keyword == '#') {
                $cond = $srch->addCondition('withdrawal_id', '=', ltrim(str_replace('#', '', $post['keyword']), '0'));
            } else {
                $cond = $srch->addCondition('withdrawal_id', 'LIKE', '%' . $post['keyword'] . '%');
            }
            $cond->attachCondition('withdrawal_comments', 'LIKE', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['date_from'])) {
            $srch->addCondition('withdrawal_request_date', '>=', MyDate::formatDate($post['date_from'] . ' 00:00:00'));
        }
        if (!empty($post['date_to'])) {
            $srch->addCondition('withdrawal_request_date', '<=', MyDate::formatDate($post['date_to'] . ' 23:59:59'));
        }
        $requests = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('post', $post);
        $this->set('requests', $requests);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    /**
     * Get Search Withdraw Requests Form
     * 
     * @return Form
     */
    private function getSearchWithdrawRequestsForm(): Form
    {
        $frm = new Form('withdrawSrchFrm');
        $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword', '');
        $frm->addDateField(Label::getLabel('LBL_Date_From'), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To'), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear'));
        return $frm;
    }

}
