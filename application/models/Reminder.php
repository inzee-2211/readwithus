<?php

/**
 * This class is used to handle Reminders
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Reminder extends FatModel
{

    const DB_TBL = 'tbl_reminders';
    const DB_TBL_PREFIX = 'rem_';
    const ONE_HOUR = 1;
    const ONE_DAY = 2;
    const THREE_DAY = 3;
    const SEVEN_DAY = 4;
    const TYPE_LESSSON = 1;
    const TYPE_GCLASS = 2;
    const TYPE_SUBSCRIPTION = 3;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Setup Reminder
     * 
     * @param int $recordType
     * @param int $recordId
     * @param int $userId
     * @param int $minutes
     * @return bool
     */
    public function setup(int $recordType, int $recordId, int $userId, int $minutes): bool
    {
        $tableRecord = new TableRecord(static::DB_TBL);
        $tableRecord->assignValues([
            "rem_record_type" => $recordType,
            "rem_record_id" => $recordId,
            "rem_user_id" => $userId,
            "rem_minutes" => $minutes,
            "rem_senton" => date('Y-m-d H:i:s'),
        ]);
        if (!$tableRecord->addNew([], $tableRecord->getFlds())) {
            $this->error = $tableRecord->getError();
            return false;
        }
        return true;
    }

    /**
     * Send Lesson Reminder
     * 
     * @param int $type
     * @return bool
     */
    public function sendLessonReminder(int $type): bool
    {
        $type = (!in_array($type, [static::ONE_DAY, static::ONE_HOUR])) ? static::ONE_HOUR : $type;
        $minutes = $this->getMinutes($type);
        $srch = $this->getLessonSearchObject($type);
        $resultSet = $srch->getResultSet();
        $db = FatApp::getDb();
        while ($row = $db->fetch($resultSet)) {
            /* Send mail to teacher */
            $teacherData = $this->formatData($row, User::TEACHER);
            if (!$this->sendMail($teacherData, 'coming_up_lesson_reminder')) {
                return false;
            }
            if (!$this->setup(static::TYPE_LESSSON, $row['ordles_id'], $row['teacher_id'], $minutes)) {
                return false;
            }
            /* Send mail to learner */
            $learnerData = $this->formatData($row, User::LEARNER);
            if (!$this->sendMail($learnerData, 'coming_up_lesson_reminder')) {
                return false;
            }
            if (!$this->setup(static::TYPE_LESSSON, $row['ordles_id'], $row['learner_id'], $minutes)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Send Class Reminder
     * 
     * @param int $type
     * @return bool
     */
    public function sendWalletBalanceReminder(int $type): bool
    {

        $type = (!in_array($type, [static::ONE_DAY, static::THREE_DAY, static::SEVEN_DAY])) ? static::ONE_DAY : $type;
        $minutes = $this->getMinutes($type);
        $subscriptions = $this->getSubscriptions($minutes);
        if (empty($subscriptions)) {
            return true;
        }
        foreach ($subscriptions as $subscription) {
            $totalAmount = $subscription['order_net_amount'] + $subscription['order_discount_value'];
            $vars = [
                '{learner_name}' => $subscription['learner_first_name'] . ' ' . $subscription['learner_last_name'],
                '{current_balance}' => MyUtility::formatMoney($subscription['user_wallet_balance']),
                '{subscription_amount}' => MyUtility::formatMoney($totalAmount),
                '{sub_recurring_date}' => MyDate::formatDate($subscription['ordsub_enddate'], 'Y-m-d H:i:s', $subscription['learner_timezone']),
            ];
            $mail = new FatMailer($subscription['learner_lang_id'], 'wallet_balance_maintain_for_subscription');
            $mail->setVariables($vars);
            $mail->sendMail([$subscription['learner_email']]);
            if (!$this->setup(static::TYPE_SUBSCRIPTION, $subscription['ordsub_id'], $subscription['learner_id'], $minutes)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Send Class Reminder
     * 
     * @param int $type
     * @return bool
     */
    public function sendClassReminder(int $type): bool
    {
        $type = (!in_array($type, [static::ONE_DAY, static::ONE_HOUR])) ? static::ONE_HOUR : $type;
        $minutes = $this->getMinutes($type);
        $classes = $this->getClasses($type);
        if (empty($classes)) {
            return true;
        }
        $langIds = array_column($classes, 'teacher_lang_id', 'teacher_lang_id');
        $langIds += array_column($classes, 'learner_lang_id', 'learner_lang_id');
        $classIds = array_column($classes, 'grpcls_id', 'grpcls_id');
        $titles = $this->getClassTitles($classIds, $langIds);
        $teacherCls = [];
        foreach ($classes as $class) {
            /* Send mail to learner */
            $class['title'] = $titles[$class['grpcls_id'] . '-' . $class['learner_lang_id']] ?? $class['grpcls_title'];
            $learnerData = $this->formatData($class, User::LEARNER);
            if (!$this->sendMail($learnerData, 'coming_up_class_reminder')) {
                return false;
            }
            if (!$this->setup(static::TYPE_GCLASS, $class['grpcls_id'], $class['learner_id'], $minutes)) {
                return false;
            }
            /**
             * Added the below condition to prevent sending multiple emails to 
             * teacher's because multiple learners can join the one group class
             */
            $teacherClasses = $teacherCls[$class['teacher_id']] ?? [];
            if (in_array($class['grpcls_id'], $teacherClasses)) {
                continue;
            }
            /* Send mail to teacher */
            $class['title'] = $titles[$class['grpcls_id'] . '-' . $class['teacher_lang_id']] ?? $class['grpcls_title'];
            $teacherData = $this->formatData($class, User::TEACHER);
            if (!$this->sendMail($teacherData, 'coming_up_class_reminder')) {
                return false;
            }
            if (!$this->setup(static::TYPE_GCLASS, $class['grpcls_id'], $class['teacher_id'], $minutes)) {
                return false;
            }
            $teacherCls[$class['teacher_id']][] = $class['grpcls_id'];
        }
        return true;
    }

    /**
     * Get Lesson Search Object
     * 
     * @param int $type
     * @return SearchBase
     */
    private function getLessonSearchObject(int $type): SearchBase
    {
        $duration = $this->getMinutes($type);
        $srch = new SearchBase(Lesson::DB_TBL, 'ordles');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id', 'orders');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = orders.order_user_id', 'learner');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordles.ordles_teacher_id', 'teacher');
        $srch->joinTable(static::DB_TBL, 'LEFT JOIN', 'rem.rem_record_id = ordles.ordles_id and rem.rem_minutes = ' . $duration . ' and rem.rem_record_type = ' . static::TYPE_LESSSON, 'rem');
        $srch->addDirectCondition('learner.user_deleted IS NULL');
        $srch->addDirectCondition('teacher.user_deleted IS NULL');
        $srch->addDirectCondition('rem.rem_id IS NULL');
        $srch->addCondition('ordles.ordles_lesson_starttime', '>', date('Y-m-d H:i:s'));
        $srch->addCondition('ordles.ordles_status', '=', Lesson::SCHEDULED);
        if ($type == self::ONE_DAY) {
            $srch->addCondition('ordles.ordles_lesson_starttime', 'BETWEEN', [date('Y-m-d H:i:s', strtotime('+23 hours 30 minutes')), date('Y-m-d H:i:s', strtotime('+' . $duration . ' minutes'))]);
        } else {
            $srch->addCondition('ordles.ordles_lesson_starttime', 'BETWEEN', [date('Y-m-d H:i:s', strtotime('+30 minutes')), date('Y-m-d H:i:s', strtotime('+' . $duration . ' minutes'))]);
        }
        $srch->addOrder('ordles_lesson_starttime');
        $srch->setPageSize(15);
        $srch->doNotCalculateRecords();
        $srch->addMultipleFields([
            'teacher.user_email as teacher_email', 'teacher.user_first_name as teacher_first_name',
            'teacher.user_last_name as teacher_last_name', 'teacher.user_timezone as teacher_timezone',
            'teacher.user_lang_id as teacher_lang_id', 'learner.user_email as learner_email',
            'learner.user_first_name as learner_first_name', 'learner.user_last_name as learner_last_name',
            'learner.user_timezone as learner_timezone', 'learner.user_lang_id as learner_lang_id',
            'ordles_teacher_id as teacher_id', 'ordles_id', 'order_user_id as learner_id',
            'ordles_lesson_starttime as start', 'ordles_lesson_endtime as end', 'ordles_id as record_id',
        ]);
        return $srch;
    }

    /**
     * Get Classes
     * 
     * @param int $type
     * @return array
     */
    private function getClasses(int $type): array
    {
        $duration = $this->getMinutes($type);
        $srch = new SearchBase(OrderClass::DB_TBL, 'ordcls');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordcls.ordcls_order_id', 'orders');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = orders.order_user_id', 'learner');
        $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id = ordcls.ordcls_grpcls_id', 'grpcls');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        $srch->joinTable(static::DB_TBL, 'LEFT JOIN', 'rem.rem_record_id = grpcls.grpcls_id and rem.rem_minutes = ' . $duration . ' and rem.rem_record_type = ' . static::TYPE_GCLASS, 'rem');
        $srch->addDirectCondition('learner.user_deleted IS NULL');
        $srch->addDirectCondition('teacher.user_deleted IS NULL');
        $srch->addDirectCondition('rem.rem_id IS NULL');
        $srch->addCondition('grpcls.grpcls_start_datetime', '>', date('Y-m-d H:i:s'));
        $srch->addCondition('grpcls.grpcls_booked_seats', '>', 0);
        $srch->addCondition('grpcls.grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('ordcls.ordcls_status', '=', OrderClass::SCHEDULED);
        if ($type == self::ONE_DAY) {
            $srch->addCondition('grpcls.grpcls_start_datetime', 'BETWEEN', [date('Y-m-d H:i:s', strtotime('+23 hours 30 minutes')), date('Y-m-d H:i:s', strtotime('+' . $duration . ' minutes'))]);
        } else {
            $srch->addCondition('grpcls.grpcls_start_datetime', 'BETWEEN', [date('Y-m-d H:i:s', strtotime('+30 minutes')), date('Y-m-d H:i:s', strtotime('+' . $duration . ' minutes'))]);
        }
        $srch->addOrder('grpcls_start_datetime');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(15);
        $srch->addMultipleFields([
            'teacher.user_email as teacher_email', 'teacher.user_first_name as teacher_first_name',
            'teacher.user_last_name as teacher_last_name', 'teacher.user_timezone as teacher_timezone',
            'teacher.user_lang_id as teacher_lang_id', 'learner.user_email as learner_email',
            'learner.user_first_name as learner_first_name', 'learner.user_last_name as learner_last_name',
            'learner.user_timezone as learner_timezone', 'learner.user_lang_id as learner_lang_id',
            'grpcls.grpcls_teacher_id as teacher_id', 'grpcls_id', 'grpcls_id as record_id', 'ordcls_id',
            'order_user_id as learner_id', 'grpcls_start_datetime as start', 'grpcls_end_datetime as end', 'grpcls.grpcls_title'
        ]);
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get Class Titles
     * 
     * @param array $classIds
     * @param array $langIds
     * @return array
     */
    private function getClassTitles(array $classIds, array $langIds): array
    {
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = gclang.gclang_grpcls_id', 'gclang');
        $srch->doNotCalculateRecords();
        $srch->addCondition('grpcls.grpcls_id', 'IN', $classIds);
        $srch->addCondition('gclang.gclang_lang_id', 'IN', $langIds);
        $srch->addMultipleFields(['CONCAT(grpcls.grpcls_id,"-", gclang_lang_id) as classkey', 'IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title']);
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Format Data
     * 
     * @param array $row
     * @param int $userType
     * @return array
     */
    private function formatData(array $row, int $userType): array
    {
        $controller = (!empty($row['grpcls_id'])) ? 'Classes' : 'Lessons';
        if ($userType == User::TEACHER) {
            $vars = [
                '{name}' => $row['learner_first_name'] . ' ' . $row['learner_last_name'],
                '{start}' => MyDate::formatDate($row['start'], 'Y-m-d H:i:s', $row['teacher_timezone']),
                '{end}' => MyDate::formatDate($row['end'], 'Y-m-d H:i:s', $row['teacher_timezone']),
                '{link}' => MyUtility::makeFullUrl($controller, 'view', [$row['record_id']], CONF_WEBROOT_DASHBOARD)
            ];
            if (!empty($row['grpcls_id'])) {
                $vars['{name}'] = $row['title'];
            }
            return [
                'user_full_name' => $row['teacher_first_name'] . ' ' . $row['teacher_last_name'],
                'email' => $row['teacher_email'],
                'lang_id' => $row['teacher_lang_id'],
                'details' => str_replace(array_keys($vars), array_values($vars), $this->getTabelHTML()),
            ];
        } else {
            $recordId = $row['record_id'];
            $name = $row['teacher_first_name'] . ' ' . $row['teacher_last_name'];
            if (!empty($row['grpcls_id'])) {
                $recordId = (!empty($row['grpcls_id'])) ? $row['ordcls_id'] : $row['record_id'];
                $name = $row['title'];
            }

            $vars = [
                '{name}' => $name,
                '{start}' => MyDate::formatDate($row['start'], 'Y-m-d H:i:s', $row['learner_timezone']),
                '{end}' => MyDate::formatDate($row['end'], 'Y-m-d H:i:s', $row['learner_timezone']),
                '{link}' => MyUtility::makeFullUrl($controller, 'view', [$recordId], CONF_WEBROOT_DASHBOARD)
            ];
            return [
                'user_full_name' => $row['learner_first_name'] . ' ' . $row['learner_last_name'],
                'email' => $row['learner_email'],
                'lang_id' => $row['learner_lang_id'],
                'details' => str_replace(array_keys($vars), array_values($vars), $this->getTabelHTML()),
            ];
        }
    }

    /**
     * Send Email
     * 
     * @param array $row
     * @param string $template
     * @return bool
     */
    private function sendMail(array $row, string $template): bool
    {
        $mail = new FatMailer($row['lang_id'], $template);
        $vars = [
            '{user_full_name}' => $row['user_full_name'],
            '{details}' => $row['details'],
        ];
        $mail->setVariables($vars);
        if (!$mail->sendMail([$row['email']])) {
            $this->error = $mail->getError();
            return false;
        }
        return true;
    }

    /**
     * Get Subscriptions
     * 
     * @param int $type
     * @return array
     */
    private function getSubscriptions(int $duration): array
    {
        $srch = new SearchBase(Subscription::DB_TBL, 'ordsub');
        $srch->addMultipleFields([
            'order_id', 'learner.user_id as learner_id', 'order_discount_value', 'order_net_amount',
            'learnerSett.user_wallet_balance', 'ordsub_teacher_id', 'ordsub_id', 'ordsub.ordsub_enddate',
            'learner.user_lang_id as learner_lang_id', 'learner.user_first_name as learner_first_name',
            'learner.user_last_name as learner_last_name', 'learner.user_email as learner_email', 'learner.user_timezone as learner_timezone',
        ]);
        $srch->joinTable(static::DB_TBL, 'LEFT JOIN', 'rem.rem_record_id = ordsub.ordsub_id and rem.rem_minutes = ' . $duration . ' and rem.rem_record_type = ' . static::TYPE_SUBSCRIPTION, 'rem');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordsub.ordsub_order_id', 'orders');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = orders.order_user_id', 'learner');
        $srch->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'learnerSett.user_id = learner.user_id', 'learnerSett');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordsub.ordsub_teacher_id', 'teacher');
        $srch->addDirectCondition('learner.user_deleted IS NULL');
        $srch->addDirectCondition('teacher.user_deleted IS NULL');
        $srch->addDirectCondition('rem.rem_id IS NULL');
        $srch->addCondition('ordsub.ordsub_status', '=', Subscription::ACTIVE);
        $srch->addCondition('ordsub.ordsub_enddate', '<=', date('Y-m-d H:i:s', strtotime('+' . $duration . ' minutes')));
        $srch->addCondition('order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('order_status', '=', Order::STATUS_COMPLETED);
        $srch->setPageSize(10);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'ordsub_id');
    }

    /**
     * Get Table HTML
     * 
     * @return string
     */
    private function getTabelHTML(): string
    {
        return '<table style="border:1px solid #ddd; border-collapse:collapse;" cellspacing="0" cellpadding="0" border="0">
                <thead>
                    <tr>
                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> ' . Label::getLabel('LBL_NAME') . ' </th>
                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> ' . Label::getLabel('LBL_START') . ' </th>
                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> ' . Label::getLabel('LBL_END') . ' </th>
                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> </th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">{name}</td>
                    <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">{start}</td>
                    <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">{end}</td>
                    <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153"><a href="{link}" style="background:{secondary-color}; color:{secondary-inverse-color}; text-decoration:none;font-size:16px; font-weight:500;padding:10px 30px;display:inline-block;border-radius:3px;">' . Label::getLabel('LBL_VIEW') . '</a></td>
                 </tr>
                </tbody>
            </table>';
    }

    private function getMinutes($duration)
    {
        $minutes = [
            static::ONE_HOUR => 60,
            static::ONE_DAY => 1440,
            static::THREE_DAY => 4320,
            static::SEVEN_DAY => 10080
        ];
        return $minutes[$duration];
    }

}
