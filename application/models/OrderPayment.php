<?php

/**
 * This class is used to handle Order Payment
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class OrderPayment extends FatModel
{

    private $order;
    private $orderId;

    /**
     * Initialize Order Payment
     * 
     * @param int $orderId
     */
    public function __construct(int $orderId)
    {
        parent::__construct();
        $this->orderId = $orderId;
        $this->order = Order::getAttributesById($orderId);
    }

    /**
     * getById
     * 
     * @param int $payId
     * @return null|array
     */
    public static function getById(int $payId)
    {
        $src = new SearchBase(Order::DB_TBL_PAYMENT, 'ordpay');
        $src->addCondition('ordpay_id', '=', $payId);
        $src->doNotCalculateRecords();
        $src->setPageSize(1);
        return FatApp::getDb()->fetch($src->getResultSet());
    }

    /**
     * Get Methods
     * 
     * @param int $key
     * @return string|array
     */
    public static function getMethods(int $key = null)
    {
        $srch = PaymentMethod::getSearchObject(false);
        $srch->addMultipleFields(['pmethod_id', 'pmethod_code']);
        $arr = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        foreach ($arr as $ke => $value) {
            $arr[$ke] = Label::getLabel('LBL_' . $value);
        }
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Payments By Order Id
     * 
     * @param int $orderId
     * @return array
     */
    public static function getPaymentsByOrderId(int $orderId): array
    {
        $src = new SearchBase(Order::DB_TBL_PAYMENT, 'ordpay');
        $src->addCondition('ordpay_order_id', '=', $orderId);
        $src->doNotCalculateRecords();
        $src->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($src->getResultSet());
    }

    /**
     * Payment Settlements
     * 
     * @param string $txnId
     * @param float $amount
     * @param array $res
     * @return bool
     */
    public function paymentSettlements(string $txnId, float $amount, array $res): bool
    {
        if (empty($this->order)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        if (Order::ISPAID == $this->order['order_payment_status']) {
            $this->error = Label::getLabel('LBL_ORDER_ALREADY_PAID');
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        if (!$this->addOrderPayment($txnId, $amount, $res)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->sendEmailNotification()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->sendSystemNotification()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->updatePaymentStatus()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->relatedOrderPayments()) {
            $db->rollbackTransaction();
            return false;
        }
        $db->commitTransaction();
        return true;
    }

    /**
     * Related Order Payments
     * 
     * @return bool
     */
    private function relatedOrderPayments(): bool
    {
        $userId = $this->order['order_user_id'];
        $orderId = $this->order['order_related_order_id'];
        if (empty($orderId)) {
            return true;
        }
        /* Get Releated Order to Pay */
        $orderObj = new Order($orderId, $userId);
        if (!$order = $orderObj->getOrderToPay()) {
            $this->error = $orderObj->getError();
            return true;
        }
        /* Initialize Payment Method */
        $pay = new WalletPay($order);
        if (!$pay->initPayemtMethod()) {
            $this->error = $pay->getError();
            return false;
        }
        /* Check user wallet balance */
        $amount = FatUtility::int($order['order_net_amount']);
        if ($amount > User::getWalletBalance($order['order_user_id'])) {
            $this->error = Label::getLabel('MSG_INSUFFICIENT_WALLET_BALANCE');
            return false;
        }
        /* Debit user wallet balance */
        $vars = [Transaction::getTypes($order['order_type']), Order::formatOrderId($orderId)];
        $comment = str_replace(['{ordertype}', '{orderid}'], $vars, Label::getLabel('LBL_{ordertype}:_ID_{orderid}'));
        $txn = new Transaction($order['order_user_id'], $order['order_type']);
        if (!$txn->debit($amount, $comment)) {
            $this->error = Label::getLabel('MSG_SOMETHING_WENT_WRONG_TRY_AGAIN');
            return false;
        }
        $res = Transaction::getAttributesById($txn->getMainTableRecordId());
        /* Order Payment & Settlements */
        $payment = new OrderPayment($orderId);
        if (!$payment->paymentSettlements($res['usrtxn_id'], $amount, $res)) {
            $this->error = $payment->getError();
            return false;
        }
        return true;
    }

    /**
     * Add Order Payment
     * 
     * @param string $txnId
     * @param float $amount
     * @param array $res
     * @return bool
     */
    private function addOrderPayment(string $txnId, float $amount, array $res): bool
    {
        $pmethodId = $res['ordpay_pmethod_id'] ?? $this->order['order_pmethod_id'];
        $paymentData = [
            'ordpay_txn_id' => $txnId,
            'ordpay_amount' => $amount,
            'ordpay_pmethod_id' => $pmethodId,
            'ordpay_order_id' => $this->orderId,
            'ordpay_response' => json_encode($res),
            'ordpay_datetime' => date('Y-m-d H:i:s')
        ];
        $payment = new TableRecord(Order::DB_TBL_PAYMENT);
        $payment->assignValues($paymentData);
        if (!$payment->addNew(['HIGH_PRIORITY'])) {
            $this->error = $payment->getError();
            return false;
        }
        return true;
    }

    /**
     * Update Payment Status
     * 
     * Mark order payment status paid
     * Update Suborder's required tables
     * 
     * @return bool
     */
    private function updatePaymentStatus(): bool
    {
        $orderType = FatUtility::int($this->order['order_type']);
        $order = new TableRecord(Order::DB_TBL);
        $order->setFldValue('order_payment_status', Order::ISPAID);
        $order->setFldValue('order_status', Order::STATUS_COMPLETED);
        if (!$order->update(['smt' => 'order_id = ?', 'vals' => [$this->orderId]])) {
            $this->error = $order->getError();
            return false;
        }
        $this->order['order_payment_status'] = Order::ISPAID;
        $this->order['order_status'] = Order::STATUS_COMPLETED;
        switch ($orderType) {
            case Order::TYPE_LESSON:
                return $this->updateLessonData();
            case Order::TYPE_SUBSCR:
                return $this->updateSubscriptionData();
            case Order::TYPE_GCLASS:
                return $this->updateGclassData();
            case Order::TYPE_PACKGE:
                return $this->updatePackageData();
            case Order::TYPE_COURSE:
                return $this->updateCourseData();
            case Order::TYPE_WALLET:
                return $this->updateWalletData();
            case Order::TYPE_GFTCRD:
                return $this->updateGiftcardData();
        }
        $this->error = Label::getLabel('LBL_INVALID_REQUEST');
        return false;
    }

    /**
     * Send Email Notification
     * 
     * @return bool
     */
    private function sendEmailNotification(): bool
{
    // If you have a global toggle, short-circuit when emails are off
    if (defined('ALLOW_EMAILS') && !ALLOW_EMAILS) {
        return true;
    }
    // Or, if your config key is different, use that instead:
    // if (!FatApp::getConfig('CONF_ALLOW_EMAILS', FatUtility::VAR_INT, 0)) {
    //     return true;
    // }

    $user = User::getAttributesById($this->order['order_user_id']);
    $variables = [
        '{orderid}'    => Order::formatOrderId($this->order['order_id']),
        '{order_link}' => MyUtility::makeFullUrl('Orders', 'view', [$this->order['order_id']], CONF_WEBROOT_BACKEND),
        '{payment}'    => MyUtility::formatMoney($this->order['order_net_amount']),
        '{customer}'   => $user['user_first_name'] . ' ' . $user['user_last_name'],
    ];

    // ------- Mail to customer -------
    $mail = new FatMailer($user['user_lang_id'], 'order_paid_to_customer');
    $mail->setVariables($variables);
    if (!$mail->sendMail([$user['user_email']])) {
        // Log but DO NOT fail payment
        error_log('OrderPayment email (customer) failed: ' . $mail->getError());
        // DO NOT set $this->error and DO NOT return false
    }

    // ------- Mail to admin -------
    $langId = MyUtility::getSystemLanguage()['language_id'] ?? 1;
    $mail   = new FatMailer($langId, 'order_paid_to_admin');
    $mail->setVariables($variables);
    if (!$mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')])) {
        // Log but DO NOT fail payment
        error_log('OrderPayment email (admin) failed: ' . $mail->getError());
    }

    // Always return true so payment flow continues
    return true;
}


    /**
     * Send System Notification
     * 
     * @return bool
     */
    private function sendSystemNotification(): bool
    {
        $userId = FatUtility::int($this->order['order_user_id']);
        $noti = new Notification($userId, Notification::TYPE_ORDER_PAID);
        $variables = [
            '{orderid}' => Order::formatOrderId($this->order['order_id']),
            '{payment}' => MyUtility::formatMoney($this->order['order_net_amount']),
        ];
        if (!$noti->sendNotification($variables, User::LEARNER)) {
            $this->error = $noti->getError();
            return false;
        }
        return true;
    }

    /**
     * Update Lesson Order:
     * 
     * 1. Update Lesson counts in (tbl_offer_prices)
     * 2. Update Lesson counts in (tbl_teacher_stats)
     * 3. Update Student counts in (tbl_teacher_stats)
     * 
     * @return bool
     */
    private function updateLessonData(): bool
    {
        $subOrderObj = new SearchBase(Order::DB_TBL_LESSON);
        $subOrderObj->addMultipleFields([
            'ordles_teacher_id', 'ordles_tlang_id', 'ordles_id', 'ordles_type',
            'IFNULL(lsetting.user_google_token,"") as learner_google_token',
            'IFNULL(tsetting.user_google_token,"") as teacher_google_token',
            'ordles_status',
            'learner.user_lang_id as learner_lang_id',
            'learner.user_first_name as learner_first_name',
            'learner.user_last_name as learner_last_name',
            'learner.user_email as learner_email',
            'teacher.user_lang_id as teacher_lang_id',
            'teacher.user_first_name as teacher_first_name',
            'teacher.user_last_name as teacher_last_name',
            'teacher.user_email as teacher_email',
            'ordles_duration', 'ordles_lesson_starttime',
            'ordles_lesson_endtime'
        ]);
        $subOrderObj->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = ' . $this->order['order_user_id'], 'learner');
        $subOrderObj->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordles_teacher_id', 'teacher');
        $subOrderObj->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'lsetting.user_id = learner.user_id', 'lsetting');
        $subOrderObj->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'tsetting.user_id = teacher.user_id', 'tsetting');
        $subOrderObj->addCondition('ordles_order_id', '=', $this->orderId);
        $subOrderObj->doNotCalculateRecords();
        $subOrders = FatApp::getDb()->fetchAll($subOrderObj->getResultSet());
        if (empty($subOrders)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $subOrder = current($subOrders);
        $teacherId = FatUtility::int($subOrder['ordles_teacher_id']);
        $lessons = FatUtility::int($this->order['order_item_count']);
        $offerPrice = new OfferPrice($this->order['order_user_id']);
        if (!$offerPrice->increaseLesson($teacherId, $lessons)) {
            $this->error = $offerPrice->getError();
            return false;
        }
        if (!$offerPrice->updateTeacherStats($teacherId)) {
            $this->error = $offerPrice->getError();
            return false;
        }
        $tlangName = Label::getLabel('LBL_FREE_TRIAL', $subOrder['learner_lang_id']);
        if ($subOrder['ordles_type'] != Lesson::TYPE_FTRAIL) {
            $tlanguageNames = TeachLanguage::getNames($subOrder['learner_lang_id'], [$subOrder['ordles_tlang_id']]);
            $tlangName = $tlanguageNames[$subOrder['ordles_tlang_id']] ?? '';
        }
        $vars = [
            '{learner_name}' => $subOrder['learner_first_name'] . ' ' . $subOrder['learner_last_name'],
            '{teacher_name}' => $subOrder['teacher_first_name'] . ' ' . $subOrder['teacher_last_name'],
            '{tlang_name}' => $tlangName,
            '{lesson_url}' => MyUtility::makeFullUrl('Lessons', 'index', [], CONF_WEBROOT_DASHBOARD) . '?order_id=' . $this->orderId,
        ];
        $mail = new FatMailer($subOrder['teacher_lang_id'], 'teacher_lesson_book_email');
        $mail->setVariables($vars);
        $mail->sendMail([$subOrder['teacher_email']]);
        $mail = new FatMailer($subOrder['learner_lang_id'], 'learner_lesson_book_email');
        $mail->setVariables($vars);
        $mail->sendMail([$subOrder['learner_email']]);
        $this->addLessonEvent($subOrders, $tlangName);
        $this->checkMeetingLicense($subOrders);
        return true;
    }

    /**
     * Update Lesson Order:
     * 
     * 1. Update Lesson counts in (tbl_offer_prices)
     * 2. Update Lesson counts in (tbl_teacher_stats)
     * 3. Update Student counts in (tbl_teacher_stats)
     * 
     * @return bool
     */
    private function updateSubscriptionData(): bool
    {
        $subOrderObj = new SearchBase(Subscription::DB_TBL);
        $subOrderObj->joinTable(Lesson::DB_TBL, 'INNER JOIN', 'ordsub_order_id = ordles_order_id', 'ordles');
        $subOrderObj->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = ' . $this->order['order_user_id'], 'learner');
        $subOrderObj->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordles_teacher_id', 'teacher');
        $subOrderObj->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'lsetting.user_id = learner.user_id', 'lsetting');
        $subOrderObj->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'tsetting.user_id = teacher.user_id', 'tsetting');
        $subOrderObj->addCondition('ordsub_order_id', '=', $this->orderId);
        $subOrderObj->addMultipleFields([
            'ordles_teacher_id', 'ordles_tlang_id', 'ordles_id',
            'IFNULL(lsetting.user_google_token,"") as learner_google_token',
            'IFNULL(tsetting.user_google_token,"") as teacher_google_token',
            'ordles_status',
            'learner.user_lang_id as learner_lang_id',
            'learner.user_first_name as learner_first_name',
            'learner.user_last_name as learner_last_name',
            'learner.user_email as learner_email',
            'teacher.user_lang_id as teacher_lang_id',
            'teacher.user_first_name as teacher_first_name',
            'teacher.user_last_name as teacher_last_name',
            'teacher.user_email as teacher_email',
            'ordles_duration', 'ordles_lesson_starttime', 'ordles_lesson_endtime'
        ]);
        $subOrderObj->doNotCalculateRecords();
        $subOrders = FatApp::getDb()->fetchAll($subOrderObj->getResultSet());
        if (empty($subOrders)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $subOrder = current($subOrders);
        $teacherId = FatUtility::int($subOrder['ordles_teacher_id']);
        $lessons = FatUtility::int($this->order['order_item_count']);
        $offerPrice = new OfferPrice($this->order['order_user_id']);

        if (!$offerPrice->increaseLesson($teacherId, $lessons)) {
            $this->error = $offerPrice->getError();
            return false;
        }
        $teacherStat = new TeacherStat($teacherId);
        if (!$teacherStat->setLessonAndClassCount()) {
            $this->error = $teacherStat->getError();
            return false;
        }
        $tlanguageNames = TeachLanguage::getNames($subOrder['learner_lang_id'], [$subOrder['ordles_tlang_id']]);
        $tlangName = $tlanguageNames[$subOrder['ordles_tlang_id']] ?? '';
        $vars = [
            '{learner_name}' => $subOrder['learner_first_name'] . ' ' . $subOrder['learner_last_name'],
            '{teacher_name}' => $subOrder['teacher_first_name'] . ' ' . $subOrder['teacher_last_name'],
            '{tlang_name}' => $tlangName,
            '{lesson_url}' => MyUtility::makeFullUrl('Lessons', 'index', [], CONF_WEBROOT_DASHBOARD) . '?order_id=' . $this->orderId,
        ];
        $mail = new FatMailer($subOrder['teacher_lang_id'], 'teacher_lesson_book_email');
        $mail->setVariables($vars);
        $mail->sendMail([$subOrder['teacher_email']]);
        $mail = new FatMailer($subOrder['learner_lang_id'], 'learner_lesson_book_email');
        $mail->setVariables($vars);
        $mail->sendMail([$subOrder['learner_email']]);
        $this->addLessonEvent($subOrders, $tlangName);
        $this->checkMeetingLicense($subOrders);
        return true;
    }

    /**
     * Update Group Class Order:
     * 
     * 1. Update Class counts in (tbl_offer_prices)
     * 2. Update Class counts in (tbl_teacher_stats)
     * 3. Update Student counts in (tbl_teacher_stats)
     * 4. Update Group class booked counts (tbl_group_classes)
     * 5. Add Event on google calendar (tbl_google_calendar_events)
     * 
     * @return bool
     */
    private function updateGclassData(): bool
    {
        $learner = User::getAttributesById($this->order['order_user_id'], ['user_lang_id', 'user_first_name', 'user_last_name', 'user_email']);
        $srch = new SearchBase(OrderClass::DB_TBL, 'ordcls');
        $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id = ordcls.ordcls_grpcls_id', 'grpcls');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_teacher_id = teacher.user_id', 'teacher');
        $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = grpclslang.gclang_grpcls_id and grpclslang.gclang_lang_id = ' . $learner['user_lang_id'], 'grpclslang');
        $srch->addCondition('ordcls_order_id', '=', $this->orderId);
        $srch->addMultipleFields([
            'grpcls_id', 'ordcls_id', 'grpcls_start_datetime', 'teacher.user_email as teacher_email', 'teacher.user_lang_id as teacher_lang_id',
            'grpcls_end_datetime', 'grpcls_teacher_id', 'teacher.user_first_name as teacher_first_name', 'teacher.user_last_name as teacher_last_name',
            'IFNULL(grpclslang.grpcls_description, grpcls.grpcls_description) as grpcls_description',
            'IFNULL(grpclslang.grpcls_title, grpcls.grpcls_title) as grpcls_title'
        ]);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $subOrder = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($subOrder)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $teacherId = FatUtility::int($subOrder['grpcls_teacher_id']);
        $classes = FatUtility::int($this->order['order_item_count']);
        $offerPrice = new OfferPrice($this->order['order_user_id']);
        if (!$offerPrice->increaseClass($teacherId, $classes)) {
            $this->error = $offerPrice->getError();
            return false;
        }
        $teacherStat = new TeacherStat($teacherId);
        if (!$teacherStat->setLessonAndClassCount()) {
            $this->error = $teacherStat->getError();
            return false;
        }
        $class = new GroupClass($subOrder['grpcls_id']);
        if (!$class->updateBookedSeatsCount()) {
            $this->error = $class->getError();
            return false;
        }
        $vars = [
            '{learner_name}' => $learner['user_first_name'] . ' ' . $learner['user_last_name'],
            '{teacher_name}' => $subOrder['teacher_first_name'] . ' ' . $subOrder['teacher_last_name'],
            '{class_name}' => $subOrder['grpcls_title']
        ];
        $mail = new FatMailer($subOrder['teacher_lang_id'], 'learner_class_book_email');
        $mail->setVariables($vars);
        $mail->sendMail([$subOrder['teacher_email']]);
        $mail = new FatMailer($learner['user_lang_id'], 'teacher_class_book_email');
        $mail->setVariables($vars);
        $mail->sendMail([$learner['user_email']]);
        $token = (new UserSetting($this->order['order_user_id']))->getGoogleToken();
        if (!empty($token)) {
            $subOrder['google_token'] = $token;
            $googleCalendar = new GoogleCalendarEvent($this->order['order_user_id'], $subOrder['grpcls_id'], AppConstant::GCLASS);
            $googleCalendar->addClassEvent($subOrder, User::LEARNER);
        }
        return true;
    }

    /**
     * Update Package Data
     * 
     * @return bool
     */
    private function updatePackageData(): bool
    {
        $learner = User::getAttributesById($this->order['order_user_id'], ['user_lang_id', 'user_first_name', 'user_last_name', 'user_email']);
        $srch = new SearchBase(OrderClass::DB_TBL, 'ordcls');
        $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id = ordcls.ordcls_grpcls_id', 'grpcls');
        $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = grpclslang.gclang_grpcls_id and grpclslang.gclang_lang_id = ' . $learner['user_lang_id'], 'grpclslang');
        $srch->addCondition('ordcls_order_id', '=', $this->orderId);
        $srch->addMultipleFields([
            'grpcls_id', 'ordcls_id', 'grpcls_start_datetime', 'grpcls_end_datetime', 'grpcls_teacher_id',
            'IFNULL(grpclslang.grpcls_description, grpcls.grpcls_description) as grpcls_description',
            'IFNULL(grpclslang.grpcls_title, grpcls.grpcls_title) as grpcls_title'
        ]);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $subOrders = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (empty($subOrders)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $subOrder = current($subOrders);
        $teacherId = FatUtility::int($subOrder['grpcls_teacher_id']);
        $teacher = User::getAttributesById($teacherId, ['user_lang_id', 'user_first_name', 'user_last_name', 'user_email']);
        $offerPrice = new OfferPrice($this->order['order_user_id']);
        if (!$offerPrice->increaseClass($teacherId, count($subOrders))) {
            $this->error = $offerPrice->getError();
            return false;
        }
        $teacherStat = new TeacherStat($teacherId);
        if (!$teacherStat->setLessonAndClassCount()) {
            $this->error = $teacherStat->getError();
            return false;
        }
        $package = OrderPackage::getByOrderId($this->orderId, $teacher['user_lang_id']);
        $class = new GroupClass($package['ordpkg_package_id']);
        if (!$class->updateBookedSeatsCount()) {
            $this->error = $class->getError();
            return false;
        }
        foreach ($subOrders as $subOrder) {
            $class = new GroupClass($subOrder['grpcls_id']);
            if (!$class->updateBookedSeatsCount()) {
                $this->error = $class->getError();
                return false;
            }
        }
        /* Send Email Notifications */
        $vars = [
            '{learner_name}' => $learner['user_first_name'] . ' ' . $learner['user_last_name'],
            '{teacher_name}' => $teacher['user_first_name'] . ' ' . $teacher['user_last_name'],
            '{class_name}' => $package['package_name']
        ];
        $mail = new FatMailer($teacher['user_lang_id'], 'teacher_package_purchased');
        $mail->setVariables($vars);
        $mail->sendMail([$teacher['user_email']]);
        $mail = new FatMailer($learner['user_lang_id'], 'learner_package_purchased');
        $mail->setVariables($vars);
        $mail->sendMail([$learner['user_email']]);
        $token = (new UserSetting($this->order['order_user_id']))->getGoogleToken();
        if (!empty($token)) {
            foreach ($subOrders as $subOrder) {
                $subOrder['google_token'] = $token;
                $googleCalendar = new GoogleCalendarEvent($this->order['order_user_id'], $subOrder['grpcls_id'], AppConstant::GCLASS);
                $googleCalendar->addClassEvent($subOrder, User::LEARNER);
            }
        }
        return true;
    }

    /**
     * Update Course Order:
     * 
     * 1. Update Course students counts in (tbl_courses)
     * 
     * @return bool
     */
    private function updateCourseData(): bool
    {
        $srch = new SearchBase(OrderCourse::DB_TBL, 'ordcrs');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'ordcrs.ordcrs_course_id = course.course_id', 'course');
        $srch->joinTable(Course::DB_TBL_LANG, 'INNER JOIN', 'course.course_id = crsdetail.course_id', 'crsdetail');
        $srch->addCondition('ordcrs_order_id', '=', $this->order['order_id']);
        $srch->addMultipleFields([
            'ordcrs_id',
            'ordcrs_course_id',
            'course_user_id',
            'course_title',
            'course_price'
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('ordcrs_course_id');
        if (!$orderCourse = FatApp::getDb()->fetch($srch->getResultSet())) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $record = new OrderCourse($orderCourse['ordcrs_id']);
        $record->setFldValue('ordcrs_status', OrderCourse::COMPLETED);
        if (!$record->save()) {
            $this->error = $record->getError();
            return false;
        }
        $courseId = $orderCourse['ordcrs_course_id'];
        $course = new Course($courseId);
        if (!$course->setStudentCount()) {
            $this->error = $course->getError();
            return false;
        }
        /* update count in teachers stats */
        $teacherStats = new TeacherStat($orderCourse['course_user_id']);
        if (!$teacherStats->setCoursesCount()) {
            $this->error = $teacherStats->getError();
            return false;
        }
        $learner = User::getAttributesById($this->order['order_user_id'], ['user_lang_id', 'user_first_name', 'user_last_name', 'user_email']);
        $teacher = User::getAttributesById($orderCourse['course_user_id'], ['user_first_name', 'user_last_name']);
        $vars = [
            '{learner_name}' => ucwords($learner['user_first_name'] . ' ' . $learner['user_last_name']),
            '{teacher_name}' => ucwords($teacher['user_first_name'] . ' ' . $teacher['user_last_name']),
            '{course_title}' => ucfirst($orderCourse['course_title']),
            '{course_price}' => MyUtility::formatMoney($orderCourse['course_price']),
            '{course_link}' => MyUtility::generateFullUrl('Tutorials', 'start', [$orderCourse['ordcrs_id']], CONF_WEBROOT_DASHBOARD)
        ];
        $mail = new FatMailer($learner['user_lang_id'], 'course_booking_email_to_learner');
        $mail->setVariables($vars);
        $mail->sendMail([$learner['user_email']]);
        $mail = new FatMailer(MyUtility::getSiteLangId(), 'course_booking_email_to_admin');
        $mail->setVariables($vars);
        $mail->sendMail([FatApp::getConfig('CONF_SITE_OWNER_EMAIL')]);
        return true;
    }

    /**
     * Update Wallet Order:
     * 
     * 1. Add Credit TXN entry in (tbl_user_transactions) 
     * 2. Update User wallet balance (tbl_user_settings)
     * 
     * @return bool
     */
    private function updateWalletData(): bool
    {
        $reason = Label::getLabel('LBL_WALLET_MONEY_ADDED');
        $txn = new Transaction($this->order['order_user_id'], Transaction::TYPE_MONEY_DEPOSIT);
        if (!$txn->credit($this->order['order_net_amount'], $reason)) {
            $this->error = $txn->getError();
            return false;
        }
        $notifiVar = ['{reason}' => $reason, '{amount}' => MyUtility::formatMoney($this->order['order_net_amount'])];
        $notifi = new Notification($this->order['order_user_id'], Notification::TYPE_WALLET_CREDIT);
        $notifi->sendNotification($notifiVar);
        return true;
    }

    /**
     * Update Gift Card Order
     * 
     * @return bool
     */
    private function updateGiftcardData(): bool
    {
        $giftcard = new Giftcard();
        $giftcard->sendMailToAdminAndRecipient($this->orderId);
        return true;
    }

    /**
     * Get Payments By Order Id
     * @param int $orderId
     * @param bool $joinPaymentMethod
     * @param int $langId
     * @return array
     */
    public static function getPaymentByTxnId(string $txnId): array
    {
        $src = new SearchBase(Order::DB_TBL_PAYMENT, 'ordpay');
        $src->addMultipleFields(['ordpay.*']);
        $src->addCondition('ordpay_txn_id', '=', $txnId);
        $src->doNotCalculateRecords();
        $src->doNotLimitRecords();
        return FatApp::getDb()->fetch($src->getResultSet());
    }

    /**
     * Add Lesson Event
     * 
     * @param array $subOrders
     * @param string $tlangName
     * @return bool
     */
    private function addLessonEvent(array $subOrders, string $tlangName = ''): bool
    {
        foreach ($subOrders as $lesson) {
            if ($lesson['ordles_status'] != Lesson::SCHEDULED) {
                continue;
            }
            $lesson['tlang_name'] = $tlangName;
            if (!empty($lesson['learner_google_token'])) {
                $lesson['google_token'] = $lesson['learner_google_token'];
                $lesson['lang_id'] = $lesson['learner_lang_id'];
                $googleCalendar = new GoogleCalendarEvent($this->order['order_user_id'], $lesson['ordles_id'], AppConstant::LESSON);
                $googleCalendar->addLessonEvent($lesson);
            }
            if (!empty($lesson['teacher_google_token'])) {
                $lesson['google_token'] = $lesson['teacher_google_token'];
                $lesson['lang_id'] = $lesson['teacher_lang_id'];
                $googleCalendar = new GoogleCalendarEvent($lesson['ordles_teacher_id'], $lesson['ordles_id'], AppConstant::LESSON);
                $googleCalendar->addLessonEvent($lesson);
            }
        }
        return true;
    }

    /**
     * Check Meeting License
     * 
     * @param array $subOrders
     * @return bool
     */
    private function checkMeetingLicense(array $subOrders): bool
    {
        $meetingTool = new Meeting(0, 0);
        foreach ($subOrders as $lesson) {
            if ($lesson['ordles_status'] != Lesson::SCHEDULED) {
                continue;
            }
            $meetingTool->checkLicense($lesson['ordles_lesson_starttime'], $lesson['ordles_lesson_endtime'], $lesson['ordles_duration']);
        }
        return true;
    }
}
