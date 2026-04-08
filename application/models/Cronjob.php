<?php

/**
 * This class is used to handle CronJob
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Cronjob
{

    /**
     * Send Archived Emails
     * 
     * @return string
     */
    public function sendArchivedEmails(): string
    {
        $srch = new SearchBase(FatMailer::DB_TBL_ARCHIVE);
        $srch->addCondition('earch_senton', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('earch_attempted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addOrder('earch_id', 'ASC');
        $srch->setPageSize(15);
        $srch->doNotCalculateRecords();
        $rs = $srch->getResultSet();
        while ($row = FatApp::getDb()->fetch($rs)) {
            $mail = new FatMailer(0, '');
            if (!$mail->sendArchivedMail($row)) {
                return $mail->getError();
            }
        }
        return 'Archived Emails sent successfully';
    }

    /**
     * Send Class Reminder
     * 
     * @param int $type
     * @return string
     */
    public function sendClassReminder(int $type): string
    {
        $reminder = new Reminder();
        if (!$reminder->sendClassReminder($type)) {
            return $reminder->getError();
        }
        return 'Classes reminder sent successfully';
    }

    /**
     * Send Lesson Reminder
     * 
     * @param int $type
     * @return string
     */
    public function sendLessonReminder(int $type): string
    {
        $reminder = new Reminder();
        if (!$reminder->sendLessonReminder($type)) {
            return $reminder->getError();
        }
        return 'lessons reminder sent successfully';
    }

    /**
     * Send Lesson Reminder
     * 
     * @param int $type
     * @return string
     */
    public function sendWalletBalanceReminder(int $type): string
    {
        $reminder = new Reminder();
        if (!$reminder->sendWalletBalanceReminder($type)) {
            return $reminder->getError();
        }
        return 'Wallet balance reminder sent successfully';
    }

    /**
     * Resolved Issue Settlement
     * 
     * @return string
     */
    public function resolvedIssueSettlement(): string
    {
        $issue = new Issue();
        if (!$issue->resolvedIssueSettlement()) {
            return $issue->getError();
        }
        return 'resolved issue Settlement successfully';
    }

    /**
     * Completed Lesson Settlement
     * 
     * @return string
     */
    public function completedLessonSettlement(): string
    {
        $issue = new Issue();
        if (!$issue->completedLessonSettlement()) {
            return $issue->getError();
        }
        return 'completed lessons settlement successfully';
    }

    /**
     * Completed Class Settlement
     * 
     * @return string
     */
    public function completedClassSettlement(): string
    {
        $issue = new Issue();
        if (!$issue->completedClassSettlement()) {
            return $issue->getError();
        }
        return 'completed Classes settlement successfully';
    }

    /**
     * Update Availability
     * 
     * @return string
     */
    public function updateAvailabililty(): string
    {
        $availability = new Availability(0);
        if (!$availability->updateBySystem()) {
            return $availability->getError();
        }
        return 'Update the users Availabililty successfully';
    }

    /**
     * Send Unread Messages Notifications
     * 
     * @return string
     */
    public function sendUnreadMsgsNotifications(): string
    {
        $thread = new Thread();
        return $thread->sendUnreadMsgsNotifications();
    }

    /**
     * Cancel Pending Orders
     * 
     * @return string
     */
    public function cancelPendingOrders(): string
    {
        $bankTransfer = PaymentMethod::getByCode(BankTransferPay::KEY);
        $duration = FatApp::getConfig('CONF_CANCEL_ORDER_DURATION');
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->addMultipleFields([
            'order_id', 'couhis_id', 'order_type', 'couhis_coupon_id',
            'order_pmethod_id', 'order_addedon', 'order_user_id',
            'user_first_name', 'user_last_name', 'user_lang_id', 'user_email'
        ]);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'orders.order_user_id = users.user_id', 'users');
        $srch->joinTable(Coupon::DB_TBL_HISTORY, 'LEFT JOIN', 'orders.order_id = couhis.couhis_order_id', 'couhis');
        $srch->addCondition('order_payment_status', '=', Order::UNPAID);
        $srch->addCondition('order_status', '!=', Order::STATUS_CANCELLED);
        if (!empty($bankTransfer['pmethod_id'])) {
            $srch->addCondition('order_pmethod_id', '!=', $bankTransfer['pmethod_id']);
        }
        $srch->addCondition('mysql_func_DATE_ADD(order_addedon, INTERVAL ' . $duration . ' MINUTE)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->setPageSize(10);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $order = new Order();
            if (!$order->cancelUnpaidOrder($row, true)) {
                return $order->getError();
            }
        }
        return 'Cancel pending orders successfully';
    }

    /**
     * Cancel Pending Orders
     * 
     * @return string
     */
    public function cancelBankTransPendingOrders(): string
    {

        $bankTransfer = PaymentMethod::getByCode(BankTransferPay::KEY);
        if (empty($bankTransfer['pmethod_id'])) {
            return 'Bank transfer payment gateway not active';
        }
        $duration = (new BankTransferPay([]))->getBookBeforeHours();
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->addMultipleFields([
            'order_id', 'couhis_id', 'order_type', 'couhis_coupon_id',
            'order_pmethod_id', 'order_addedon', 'order_user_id',
            'user_first_name', 'user_last_name', 'user_lang_id', 'user_email'
        ]);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'orders.order_user_id = users.user_id', 'users');
        $srch->joinTable(Coupon::DB_TBL_HISTORY, 'LEFT JOIN', 'orders.order_id = couhis.couhis_order_id', 'couhis');
        $srch->addCondition('order_payment_status', '=', Order::UNPAID);
        $srch->addCondition('order_status', '!=', Order::STATUS_CANCELLED);
        $srch->addCondition('order_pmethod_id', '=', $bankTransfer['pmethod_id']);
        $srch->addCondition('mysql_func_DATE_ADD(order_addedon, INTERVAL ' . $duration . ' HOUR)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->setPageSize(10);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $order = new Order();
            if (!$order->cancelUnpaidOrder($row, true)) {
                return $order->getError();
            }
        }
        return 'Cancel Bank transfer pending orders successfully';
    }

    /**
     * Recurring Subscription
     * 
     * @return string
     * @todo Need to discuss this method
     */
    public function recurringSubscription(): string
    {
        $walletPay = PaymentMethod::getByCode(WalletPay::KEY);
        if (empty($walletPay)) {
            return "WALLET PAY IS NOT ACTIVE";
        }
        $db = FatApp::getDb();
        $srch = new SearchBase(Subscription::DB_TBL, 'ordsub');
        $srch->addMultipleFields([
            'order_id', 'learner.user_id as learner_id', 'order_discount_value', 'order_net_amount', 'ordsub_id',
            'learner.user_timezone as learner_timezone', 'ordsub_teacher_id',
            'learner.user_lang_id as learner_lang_id', 'learner.user_first_name as learner_first_name',
            'learner.user_last_name as learner_last_name', 'learner.user_email as learner_email',
            'DATEDIFF(ordsub_enddate, ordsub_startdate) as subdays'
        ]);
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordsub.ordsub_order_id', 'orders');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = orders.order_user_id', 'learner');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordsub.ordsub_teacher_id', 'teacher');
        $srch->addDirectCondition('learner.user_deleted IS NULL');
        $srch->addDirectCondition('teacher.user_deleted IS NULL');
        $srch->addCondition('ordsub.ordsub_status', '=', Subscription::ACTIVE);
        $srch->addCondition('ordsub.ordsub_enddate', '<', date('Y-m-d H:i:s'));
        $srch->addCondition('mysql_func_DATE_ADD(ordsub_enddate, INTERVAL 1 HOUR)', '>', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('order_status', '=', Order::STATUS_COMPLETED);
        $srch->setPageSize(10);
        $srch->doNotCalculateRecords();
        $rows = $db->fetchAll($srch->getResultSet(), 'ordsub_id');
        $orderIds = array_column($rows, 'subdays', 'order_id');
        $orderLessons = $this->orderLessons($orderIds);

        foreach ($rows as $value) {
            if (empty($orderLessons[$value['order_id']])) {
                continue;
            }
            $userWalletBalance = User::getWalletBalance($value['learner_id']);
            $totalAmount = $value['order_net_amount'] + $value['order_discount_value'];
            if ($totalAmount > $userWalletBalance) {
                $tabelRecord = new TableRecord(Subscription::DB_TBL);
                $tabelRecord->assignValues(['ordsub_status' => Subscription::CANCELLED, 'ordsub_updated' => date('Y-m-d H:i:s')]);
                if (!$tabelRecord->update(['smt' => 'ordsub_id = ?', 'vals' => [$value['ordsub_id']]])) {
                    $db->rollbackTransaction();
                    return false;
                }
                $vars = [
                    '{learner_name}' => $value['learner_first_name'] . ' ' . $value['learner_last_name'],
                    '{current_balance}' => MyUtility::formatMoney($userWalletBalance),
                    '{subscription_amount}' => MyUtility::formatMoney($totalAmount),
                ];
                $mail = new FatMailer($value['learner_lang_id'], 'wallet_balance_low_for_subscription');
                $mail->setVariables($vars);
                $mail->sendMail([$value['learner_email']]);
                continue;
            }
            $lessons = $this->formatAndValidate($orderLessons[$value['order_id']], $value['learner_id']);
            $subDays = FatApp::getConfig('CONF_RECURRING_SUBSCRIPTION_WEEKS') * 7;
            $startEndDate = MyDate::getSubscriptionDates($subDays, $value['learner_timezone']);
            $subscription = [
                'order_item_count' => count($lessons['lessons']),
                'order_net_amount' => $totalAmount,
                'ordles_type' => Lesson::TYPE_SUBCRIP,
                'order_pmethod_id' => $walletPay['pmethod_id'],
                'ordsub_teacher_id' => $value['ordsub_teacher_id'],
                'ordsub_startdate' => MyDate::formatToSystemTimezone($startEndDate['startDate']),
                'ordsub_enddate' => MyDate::formatToSystemTimezone($startEndDate['endDate']),
                'lessons' => $lessons['lessons']
            ];
            if (!$db->startTransaction()) {
                return $db->getError();
            }
            $order = new Order(0, $value['learner_id']);
            if (!$order->recurringSubscription($subscription)) {
                $db->rollbackTransaction();
                return $order->getError();
            }
            $orderId = $order->getMainTableRecordId();
            $orderData = Order::getAttributesById($orderId, ['order_id', 'order_type', 'order_user_id', 'order_net_amount']);
            if ($orderData['order_net_amount'] == 0) {
                $payment = new OrderPayment($orderId);
                if (!$payment->paymentSettlements('NA', 0, [])) {
                    $db->rollbackTransaction();
                    return $payment->getError();
                }
            } else {
                $walletPayObj = new WalletPay($orderData);
                if (!$data = $walletPayObj->getChargeData()) {
                    $db->rollbackTransaction();
                    return $walletPayObj->getError();
                }
                $res = $walletPayObj->callbackHandler($data);
                if ($res['status'] == AppConstant::NO) {
                    $db->rollbackTransaction();
                    return $walletPayObj->getError();
                }
            }
            $tabelRecord = new TableRecord(Subscription::DB_TBL);
            $tabelRecord->assignValues(['ordsub_status' => Subscription::COMPLETED, 'ordsub_updated' => date('Y-m-d H:i:s')]);
            if (!$tabelRecord->update(['smt' => 'ordsub_id = ?', 'vals' => [$value['ordsub_id']]])) {
                $db->rollbackTransaction();
                return $tabelRecord->getError();
            }
            $db->commitTransaction();
            $vars = [
                '{learner_name}' => $value['learner_first_name'] . ' ' . $value['learner_last_name'],
                '{start_time}' => $startEndDate['startDate'],
                '{end_time}' => $startEndDate['endDate'],
                '{seheduled_lessons}' => $lessons['scheduledCount'],
                '{unscheduled_lessons}' => $lessons['unScheduledCount'],
            ];
            $mail = new FatMailer($value['learner_lang_id'], 'recurring_subscription');
            $mail->setVariables($vars);
            $mail->sendMail([$value['learner_email']]);
        }
        return 'recurring subscription successfully';
    }

    /**
     * Order Lessons
     * 
     * @param array $orderIds
     * @return array
     */
    public function orderLessons(array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }
        $srch = new SearchBase(Lesson::DB_TBL, 'ordles');
        $srch->addMultipleFields([
            'ordles.ordles_tlang_id', 'ordles.ordles_duration', 'ordles_type', 'ordles_teacher_id',
            'ordles_tlang_id', 'ordles_commission', 'ordles.ordles_order_id',
            'ordles_teacher_id', 'ordles_lesson_starttime', 'ordles_amount',
            'ordles_lesson_starttime', 'ordles_lesson_endtime'
        ]);
        $srch->addCondition('ordles_order_id', 'IN', array_keys($orderIds));
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $resultSet = $srch->getResultSet();
        $orderLessons = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            if (!empty($row['ordles_lesson_starttime'])) {
                $row['ordles_lesson_starttime'] = date("Y-m-d H:i:s", strtotime($row['ordles_lesson_starttime'] . " +" . $orderIds[$row['ordles_order_id']] . " days"));
                $row['ordles_lesson_endtime'] = date("Y-m-d H:i:s", strtotime($row['ordles_lesson_endtime'] . " +" . $orderIds[$row['ordles_order_id']] . " days"));
            }
            $orderLessons[$row['ordles_order_id']][] = $row;
        }
        return $orderLessons;
    }

    /**
     * Format And Validate
     * 
     * @param array $lessons
     * @param int $userId
     * @return array
     */
    private function formatAndValidate(array $lessons, int $userId): array
    {
        $subscription = ['scheduledCount' => 0, 'unScheduledCount' => 0, 'lessons' => []];
        foreach ($lessons as $key => $value) {
            if (empty($value['ordles_lesson_starttime'])) {
                $lessons[$key]['ordles_status'] = Lesson::UNSCHEDULED;
                $subscription['unScheduledCount'] += 1;
                continue;
            }
            $avail = new Availability($value['ordles_teacher_id']);
            if (!$avail->isAvailable($value['ordles_lesson_starttime'], $value['ordles_lesson_endtime'])) {
                $lessons[$key]['ordles_status'] = Lesson::UNSCHEDULED;
                $lessons[$key]['ordles_lesson_starttime'] = null;
                $lessons[$key]['ordles_lesson_endtime'] = null;
                $subscription['unScheduledCount'] += 1;
                continue;
            }
            if (!$avail->isUserAvailable($value['ordles_lesson_starttime'], $value['ordles_lesson_endtime'])) {
                $lessons[$key]['ordles_status'] = Lesson::UNSCHEDULED;
                $lessons[$key]['ordles_lesson_starttime'] = null;
                $lessons[$key]['ordles_lesson_endtime'] = null;
                $subscription['unScheduledCount'] += 1;
                continue;
            }
            $avail = new Availability($userId);
            if (!$avail->isUserAvailable($value['ordles_lesson_starttime'], $value['ordles_lesson_endtime'])) {
                $lessons[$key]['ordles_status'] = Lesson::UNSCHEDULED;
                $lessons[$key]['ordles_lesson_starttime'] = null;
                $lessons[$key]['ordles_lesson_endtime'] = null;
                $subscription['unScheduledCount'] += 1;
                continue;
            }
            $lessons[$key]['ordles_status'] = Lesson::SCHEDULED;
            $subscription['scheduledCount'] += 1;
        }
        $subscription['lessons'] = $lessons;
        return $subscription;
    }

    public function cancelNotBookedClasses(): string
    {
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = '
                . 'grpcls.grpcls_id and gclang.gclang_lang_id = teacher.user_lang_id', 'gclang');
        $srch->addMultipleFields([
            'user_first_name', 'user_last_name',
            'user_lang_id', 'grpcls_id', 'user_email',
            'IFNULL(gclang.grpcls_title,grpcls.grpcls_title) as grpcls_title'
        ]);
        $srch->addCondition('grpcls.grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_booked_seats', '=', 0);
        $srch->addCondition('grpcls.grpcls_start_datetime', '<', date('Y-m-d H:i:s'));
        $srch->addCondition('grpcls.grpcls_parent', '=', 0);
        $srch->setPageSize(10);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        $db = FatApp::getDb();
        while ($row = $db->fetch($resultSet)) {
            $record = new TableRecord(GroupClass::DB_TBL);
            $record->setFldValue('grpcls_status', GroupClass::CANCELLED);
            if (!$record->update(['smt' => 'grpcls_id = ? OR grpcls_parent = ?', 'vals' => [$row['grpcls_id'], $row['grpcls_id']]])) {
                return $record->getError();
            }
            $vars = ['{teacher_name}' => $row['user_first_name'] . ' ' . $row['user_last_name'], '{title}' => $row['grpcls_title'],];
            $mail = new FatMailer($row['user_lang_id'], 'no_booking_class_or_package_cancelled');
            $mail->setVariables($vars);
            $mail->sendMail([$row['user_email']]);
        }
        return 'cancel not booked classes successfully';
    }

    public function restoreDb()
    {
        $restore = new Restore();
        if (!$restore->restoreDb()) {
            return false;
        }
        return 'Demo Database Restored';
    }

    /**
     * Completed Course Settlement
     * 
     * @return string
     */
    public function completedCourseSettlement(): string
    {
        $course = new Course();
        if (!$course->completedCourseSettlement()) {
            return $course->getError();
        }
        return 'Courses settlement successful';
    }
    
    public function shuffleZoomLicense()
    {
        $meet = new ZoomMeeting();
        if (!$meet->initMeetingTool()) {
            return false;
        }
        $toolDetails = $meet->getToolDetails();
        $freeMeetingDuration = FatApp::getConfig('CONF_ZOOM_FREE_MEETING_DURATION', FatUtility::VAR_INT);
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->addMultipleFields(['zmusr_user_id', 'zmusr_zoom_id', 'zmusr_zoom_type']);
        $srch->joinTable(ZoomMeeting::DB_TBL_USERS, 'INNER JOIN', 'grpcls_teacher_id = zmusr_user_id', 'zmusr');
        $srch->addCondition('grpcls.grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('grpcls.grpcls_booked_seats', '>', 0);
        $srch->addCondition('grpcls.grpcls_metool_id', '=', $toolDetails['metool_id']);
        $srch->addCondition('grpcls.grpcls_duration', '>', $freeMeetingDuration);
        $srch->addDirectCondition('grpcls.grpcls_teacher_starttime IS NOT NULL');
        $srch->addDirectCondition("DATE_SUB(grpcls.grpcls_end_datetime, INTERVAL 5 MINUTE) < '" . date('Y-m-d H:i:s') . "'");
        $srch->addCondition('zmusr.zmusr_zoom_type', '=', ZoomMeeting::USER_TYPE_LICENSED);
        $srch->addGroupBy('grpcls_teacher_id');
        $srch->setPageSize(20);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $row['zmusr_zoom_type'] = ZoomMeeting::USER_TYPE_BASIC;
            if (!$meet->updateUser($row)) {
                return false;
            }
        }
        $srch = new SearchBase(Lesson::DB_TBL, 'ordles');
        $srch->addMultipleFields(['zmusr_user_id', 'zmusr_zoom_id', 'zmusr_zoom_type']);
        $srch->joinTable(ZoomMeeting::DB_TBL_USERS, 'INNER JOIN', 'ordles_teacher_id = zmusr_user_id', 'zmusr');
        $srch->addCondition('ordles.ordles_status', '=', Lesson::SCHEDULED);
        $srch->addCondition('ordles.ordles_duration', '>', $freeMeetingDuration);
        $srch->addCondition('ordles.ordles_metool_id', '=', $toolDetails['metool_id']);
        $srch->addDirectCondition('ordles.ordles_teacher_starttime IS NOT NULL');
        $srch->addDirectCondition("DATE_SUB(ordles.ordles_lesson_endtime, INTERVAL 5 MINUTE) < '" . date('Y-m-d H:i:s') . "'");
        $srch->addCondition('zmusr.zmusr_zoom_type', '=', ZoomMeeting::USER_TYPE_LICENSED);
        $srch->addGroupBy('ordles_teacher_id');
        $srch->setPageSize(20);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $row['zmusr_zoom_type'] = ZoomMeeting::USER_TYPE_BASIC;
            if (!$meet->updateUser($row)) {
                return false;
            }
        }
        return true;
    }

}
