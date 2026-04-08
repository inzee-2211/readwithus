<?php

/**
 * This class is used to handle Class Search  Listing
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class QuestionSearch extends YocoachSearch
{

    /**
     * Initialize Class Search
     * 
     * @param int $langId
     * @param int $userId
     * @param int $userType
     */
    public function __construct(int $langId, int $userId, int $userType)
    {
        $this->table = QuestionClass::DB_TBL;
        $this->alias = 'question';
        parent::__construct($langId, $userId, $userType);
    /*    $this->addCondition('grpcls.grpcls_type', '=', GroupClass::TYPE_REGULAR);
        $joinType = ($userType == User::LEARNER) ? 'INNER JOIN' : 'LEFT JOIN';
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
        $this->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = gclang.gclang_grpcls_id and gclang.gclang_lang_id = ' . $this->langId, 'gclang');
        $this->joinTable(OrderClass::DB_TBL, $joinType, 'ordcls.ordcls_grpcls_id = grpcls.grpcls_id', 'ordcls');
        $this->joinTable(Order::DB_TBL, $joinType, 'orders.order_id = ordcls.ordcls_order_id', 'orders');
        $this->joinTable(User::DB_TBL, $joinType, 'learner.user_id = orders.order_user_id', 'learner');*/
    }

    /**
     * Apply Primary Conditions
     * 
     * @return void
     */
    public function applyPrimaryConditions(): void
    {
        if ($this->userType === User::LEARNER) {
            $this->addCondition('orders.order_user_id', '=', $this->userId);
            $this->addDirectCondition('learner.user_deleted IS NULL');
            $this->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        } elseif ($this->userType === User::TEACHER) {
            $this->addGroupBy('grpcls.grpcls_id');
            $this->addCondition('grpcls.grpcls_teacher_id', '=', $this->userId);
            $this->addDirectCondition('teacher.user_deleted IS NULL');
            $this->addCondition('teacher.user_is_teacher', '=', AppConstant::YES);
        }
    }

    /**
     * Apply Calendar Conditions
     * 
     * @param array $post
     * @return void
     */
    public function applyCalendarConditions(array $post): void
    {
        $this->addCondition('grpcls.grpcls_start_datetime', '< ', $post['end']);
        $this->addCondition('grpcls.grpcls_end_datetime', ' > ', $post['start']);
    }

    /**
     * Apply Search Conditions
     * 
     * @param array $post
     * @return void
     */
    public function applySearchConditionsadmin(array $post): void
    {
 
        if (!empty($post['keyword'])) {
            $keyword = trim($post['keyword']);
            $cnd = $this->addCondition('question.question_title', 'LIKE', '%' . $keyword . '%');
          }
          if (!empty($post['tutor'])) {
            $tutor = trim($post['tutor']);
            $cnd = $this->addCondition('tutor.user_first_name', 'LIKE', '%' . $tutor . '%');
          }

          if (isset($post['course_cateid']) && !empty($post['course_cateid'])) {
            $this->addCondition('cate.cate_id', '=', $post['course_cateid']);
        }
        if (isset($post['course_subcateid']) && !empty($post['course_subcateid'])) {
            $this->addCondition('subcate.cate_id', '=', $post['course_subcateid']);
        }
 
        if (!empty($post['grpcls_tlang_id'])) {
            $this->addCondition('question.question_type', '=', $post['grpcls_tlang_id']);
        }
       
    }

    public function applySearchConditions(array $post): void
    {
 
   //  echo '<pre>';print_r($post);die;
       /* if (!empty($post['keyword'])) {
             
            $keyword = trim($post['keyword']);
            $cond = $this->addCondition('gclang.grpcls_title', 'LIKE', '%' . $keyword . '%');
            $cond->attachCondition('grpcls.grpcls_title', 'LIKE', '%' . $keyword . '%');
            $fullName = 'mysql_func_CONCAT(learner.user_first_name, " ", learner.user_last_name)';
            if ($this->userType === User::LEARNER) {
                $fullName = 'mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)';
            }
            $cond->attachCondition($fullName, 'LIKE', '%' . $keyword . '%', 'OR', true);
            $orderId = FatUtility::int(str_replace("O", '', $keyword));
            $cond->attachCondition('grpcls.grpcls_id', '=', $orderId);
            $cond->attachCondition('ordcls.ordcls_id', '=', $orderId);
            $cond->attachCondition('ordcls.ordcls_order_id', '=', $orderId);
        }*/
        if (!empty($post['grpcls_tlang_id'])) {
            $this->addCondition('question.question_type', '=', $post['grpcls_tlang_id']);
        }
        // if (!empty($post['grpcls_id'])) {
        //     $this->addCondition('grpcls.grpcls_id', '=', $post['grpcls_id']);
        // }
        // if (!empty($post['package_id'])) {
        //     $this->addCondition('grpcls.grpcls_parent', '=', $post['package_id']);
        // }
        // if (!empty($post['order_id'])) {
        //     $this->addCondition('orders.order_id', '=', $post['order_id']);
        // }
        // if (!empty($post['grpcls_type'])) {
        //     $this->addCondition('grpcls.grpcls_type', '=', $post['grpcls_type']);
        // }
        // if (!empty($post['ordcls_status']) && $post['ordcls_status'] != -1) {
        //     $this->addCondition('ordcls.ordcls_status', '=', $post['ordcls_status']);
        // }
        // if (!empty($post['grpcls_status']) && $post['grpcls_status'] != -1) {
        //     $this->addCondition('grpcls.grpcls_status', '=', $post['grpcls_status']);
        // }
        // if (!empty($post['grpcls_duration'])) {
        //     $this->addCondition('grpcls.grpcls_duration', '=', $post['grpcls_duration']);
        // }
        // if (!empty($post['ordcls_tlang_id'])) {
        //     $this->addCondition('grpcls.grpcls_tlang_id', '=', $post['ordcls_tlang_id']);
        // } elseif (!empty($post['ordcls_tlang'])) {
        //     $this->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = '
        //         . ' grpcls.grpcls_tlang_id AND tlanglang.tlanglang_lang_id = ' . $this->langId, 'tlanglang');
        //     $this->addCondition('tlanglang.tlang_name', 'LIKE', '%' . trim($post['ordcls_tlang']) . '%');
        // }
        // if (!empty($post['grpcls_tlang_id'])) {
        //     $this->addCondition('grpcls.grpcls_tlang_id', '=', $post['grpcls_tlang_id']);
        // }
        // if (isset($post['order_payment_status']) && $post['order_payment_status'] !== '') {
        //     $this->addCondition('orders.order_payment_status', '=', $post['order_payment_status']);
        // }
        // if (!empty($post['grpcls_start_datetime'])) {
        //     $post['grpcls_start_datetime'] = MyDate::formatToSystemTimezone($post['grpcls_start_datetime']);
        //     $this->addCondition('grpcls.grpcls_start_datetime', '>=', $post['grpcls_start_datetime']);
        // }
        // if (!empty($post['grpcls_end_datetime'])) {
        //     $post['grpcls_end_datetime'] = MyDate::formatToSystemTimezone($post['grpcls_end_datetime'] . ' 23:59:59');
        //     $this->addCondition('grpcls.grpcls_end_datetime', '<=', $post['grpcls_end_datetime']);
        // }
        // if (!empty($post['order_addedon_from'])) {
        //     $start = $post['order_addedon_from'] . ' 00:00:00';
        //     $this->addCondition('orders.order_addedon', '>=', MyDate::formatToSystemTimezone($start));
        // }
        // if (!empty($post['order_addedon_till'])) {
        //     $end = $post['order_addedon_till'] . ' 23:59:59';
        //     $this->addCondition('orders.order_addedon', '<=', MyDate::formatToSystemTimezone($end));
        // }
    }

    /**
     * Fetch & Format classes
     * 
     * @param bool $single
     * @return array
     */
    public function fetchAndFormat(bool $single = false): array
    {

      
        $rows = FatApp::getDb()->fetchAll($this->getResultSet());
        
        if (count($rows) == 0) {
            return [];
        }
        $classIds = array_column($rows, 'grpcls_id');
        $recordIds = array_column($rows, 'ordcls_id');
        if ($this->userType == User::TEACHER) {
            $recordIds = $classIds;
        }
        $currentTimeUnix = strtotime(MyDate::formatDate(date('Y-m-d H:i:s')));
        $teachLangIds = array_column($rows, 'grpcls_tlang_id');
        $classPlans = Plan::getGclassPlans($classIds);
        $classIssues = Issue::getClassIssueIds($recordIds, $this->userType);
        $teachLangs = TeachLanguage::getNames($this->langId, $teachLangIds);
        $countries = Country::getNames($this->langId, array_column($rows, 'teacher_country_id'));
        $statuses = ($this->userType == User::TEACHER) ? GroupClass::getStatuses() : OrderClass::getStatuses();
        $ongoingLabel = Label::getLabel('LBL_CLASS_IS_ONGOING');
        $passedLabel = Label::getLabel('LBL_CLASS_TIME_HAS_PASSED');
       // echo '<pre>';print_r($rows);die;
       /* foreach ($rows as $key => $row) {
            $row['teacher_country'] = $countries[$row['teacher_country_id']] ?? '';
            $row['grpcls_currenttime_unix'] = $currentTimeUnix;
            $row['grpcls_start_datetime_utc'] = strtotime($row['grpcls_start_datetime']);
            $row['grpcls_end_datetime_utc'] = strtotime($row['grpcls_end_datetime']);
            $row['grpcls_start_datetime'] = MyDate::formatDate($row['grpcls_start_datetime']);
            $row['grpcls_end_datetime'] = MyDate::formatDate($row['grpcls_end_datetime']);
            $row['grpcls_starttime_unix'] = strtotime($row['grpcls_start_datetime']);
            $row['grpcls_endtime_unix'] = strtotime($row['grpcls_end_datetime']);
            $row['grpcls_remaining_unix'] = $row['grpcls_starttime_unix'] - $row['grpcls_currenttime_unix'];
            $recordId = ($this->userType == User::TEACHER) ? $row['grpcls_id'] : $row['ordcls_id'];
            $row['repiss_id'] = $classIssues[$recordId] ?? 0;
            $status = ($this->userType == User::TEACHER) ? $row['grpcls_status'] : $row['ordcls_status'];
            $row['statusText'] = $statuses[$status] ?? Label::getLabel('LBL_N/A');
            $row['class_time_info'] = '';
            if ($row['grpcls_currenttime_unix'] > $row['grpcls_endtime_unix']) {
                $row['class_time_info'] = $passedLabel;
            } elseif ($row['grpcls_currenttime_unix'] > $row['grpcls_starttime_unix']) {
                $row['class_time_info'] = $ongoingLabel;
            }
            $row['grpcls_tlang_name'] = $teachLangs[$row['grpcls_tlang_id']] ?? '';
            $row = array_merge($row, $classPlans[$row['grpcls_id']] ?? ['plan_id' => 0]);
            $row = $this->addUserDetails($row);
            $row['isClassCanceled'] = $this->isClassCanceled($row);
            $row['canRateClass'] = $this->canRateClass($row);
            $row['canReportClass'] = $this->canReportClass($row);
            $row['canCancelClass'] = $this->canCancelClass($row);
            $row['canEdit'] = $this->canEdit($row);
            $row['showTimer'] = $this->showStartTimer($row);
            if ($single) {
                $row['canEnd'] = $this->canEnd($row);
                $row['grpcls_endtime_remaining_unix'] = $row['grpcls_endtime_unix'] - $currentTimeUnix;
                $row['showEndTimer'] = $this->showEndTimer($row);
                $row['canJoin'] = $this->canJoin($row);
                $row['statusInfoLabel'] = $this->statusInfoLabel($row);
            }
            $rows[$key] = $row;
        }*/
        return $rows;
    }

    /**
     * Add User Details
     * 
     * @param array $row
     * @return array
     */
    private function addUserDetails(array $row): array
    {
        $row['first_name'] = $row['teacher_first_name'];
        $row['last_name'] = $row['teacher_last_name'];
        $row['user_id'] = $row['grpcls_teacher_id'];
        $row['joinTime'] = $row['ordcls_starttime'];
        if ($this->userType == User::TEACHER) {
            $row['first_name'] = $row['learner_first_name'];
            $row['last_name'] = $row['learner_last_name'];
            $row['user_id'] = $row['order_user_id'];
            $row['joinTime'] = $row['grpcls_teacher_starttime'];
        }
        return $row;
    }

    /**
     * Check Class Canceled
     * 
     * @param array $class
     * @return bool
     */
    private function isClassCanceled(array $class): bool
    {
        return ($class['grpcls_status'] == GroupClass::CANCELLED || ($this->userType == User::LEARNER && $class['ordcls_status'] == OrderClass::CANCELLED));
    }

    /**
     * Can Rate Class
     * 
     * @param array $class
     * @return bool
     */
    private function canRateClass(array $class): bool
    {
        return ($this->userType == User::LEARNER &&
            OrderClass::COMPLETED == $class['ordcls_status'] &&
            $class['ordcls_reviewed'] == AppConstant::NO && FatApp::getConfig('CONF_ALLOW_REVIEWS'));
    }

    /**
     * Can Report Class
     * 
     * @param array $class
     * @return bool
     */
    private function canReportClass(array $class): bool
    {
        $reportHours = FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION', FatUtility::VAR_INT, 0);
        if ($reportHours <= 0 || !is_null($class['ordcls_teacher_paid']) || $this->userType != User::LEARNER || $class['repiss_id'] > 0) {
            return false;
        }
        $reportTime = strtotime(" +" . $reportHours . " hour", $class['grpcls_endtime_unix']);
        return (
            ($class['ordcls_status'] == OrderClass::COMPLETED ||
                ($class['ordcls_status'] == OrderClass::SCHEDULED &&
                    empty($class['grpcls_teacher_starttime']) && $class['grpcls_currenttime_unix'] > $class['grpcls_endtime_unix'])) &&
            $reportTime > $class['grpcls_currenttime_unix']);
    }

    /**
     * Can Cancel Class
     * 
     * @param array $class
     * @return bool
     */
    private function canCancelClass(array $class): bool
    {
        $cancelDurtaion = FatApp::getConfig('CONF_CLASS_CANCEL_DURATION', FatUtility::VAR_INT, 24);
        $startTime = strtotime(' - ' . $cancelDurtaion . ' hours', $class['grpcls_starttime_unix']);
        return (
            (($this->userType == User::TEACHER && $class['grpcls_status'] == GroupClass::SCHEDULED) ||
                ($this->userType == User::LEARNER && $class['ordcls_status'] == OrderClass::SCHEDULED)) &&
            $startTime > $class['grpcls_currenttime_unix'] && $class['grpcls_parent'] == 0 && $class['grpcls_type'] == GroupClass::TYPE_REGULAR);
    }

    /**
     * Can Edit|Update Class
     * 
     * @param array $class
     * @return bool
     */
    private function canEdit(array $class): bool
    {
        return ($class['grpcls_status'] == GroupClass::SCHEDULED && $class['grpcls_booked_seats'] == 0 &&
            $class['grpcls_parent'] == 0 && $this->userType == User::TEACHER &&
            $class['grpcls_starttime_unix'] > $class['grpcls_currenttime_unix']);
    }

    /**
     * Show Start Timer
     * 
     * @param array $class
     * @return bool
     */
    private function showStartTimer(array $class): bool
    {
        return ((($this->userType == User::TEACHER && $class['grpcls_status'] == GroupClass::SCHEDULED) ||
            ($this->userType == User::LEARNER && $class['ordcls_status'] == OrderClass::SCHEDULED)) &&
            $class['grpcls_starttime_unix'] >= $class['grpcls_currenttime_unix']);
    }

    /**
     * Show End Timer
     * 
     * @param array $class
     * @return bool
     */
    private function showEndTimer(array $class): bool
    {
        return ((($this->userType == User::TEACHER && $class['grpcls_status'] == GroupClass::SCHEDULED) ||
            ($this->userType == User::LEARNER && $class['ordcls_status'] == OrderClass::SCHEDULED)) &&
            $class['grpcls_starttime_unix'] <= $class['grpcls_currenttime_unix'] &&
            $class['grpcls_endtime_unix'] >= $class['grpcls_currenttime_unix']);
    }

    /**
     * Can End Class
     * 
     * @param array $class
     * @return bool
     */
    private function canEnd(array $class): bool
    {
        if (
            ($class['grpcls_status'] != GroupClass::SCHEDULED) ||
            ($this->userType == User::LEARNER && $class['ordcls_status'] != OrderClass::SCHEDULED)
        ) {
            return false;
        }
        if ($this->userType == User::LEARNER) {
            return (!empty($class['ordcls_starttime']) && empty($class['ordcls_endtime']));
        }
        return (!empty($class['grpcls_teacher_starttime']) && empty($class['grpcls_teacher_endtime']));
    }

    /**
     * Can Join Class
     * 
     * @param array $class
     * @return bool
     */
    private function canJoin(array $class): bool
    {
        if (
            ($class['grpcls_status'] != GroupClass::SCHEDULED) ||
            ($this->userType == User::LEARNER && $class['ordcls_status'] != OrderClass::SCHEDULED)
        ) {
            return false;
        }
        return ($class['grpcls_starttime_unix'] <= $class['grpcls_currenttime_unix'] &&
            $class['grpcls_currenttime_unix'] < $class['grpcls_endtime_unix']);
    }

    /**
     * Status Info Label
     * 
     * @param array $class
     * @return string
     */
    private function statusInfoLabel(array $class): string
    {
        $label = '';
        if ($this->userType == User::LEARNER) {
            switch ($class['ordcls_status']) {
                case OrderClass::COMPLETED:
                    $label = 'LBL_NOTE_THIS_CLASS_IS_COMPLETED';
                    if ($class['canRateClass']) {
                        $label .= '_RATE_IT.';
                    }
                    break;
                case OrderClass::CANCELLED:
                    $label = 'LBL_NOTE_THIS_CLASS_HAS_BEEN_CANCELLED._SCHEDULE_MORE_CLASS.';
                    break;
                case OrderClass::SCHEDULED:
                    if ($class['grpcls_currenttime_unix'] > $class['grpcls_endtime_unix']) {
                        $label = 'LBL_NOTE_END_TIME_FOR_THIS_CLASS_IS_PASSED._SCHEDULE_MORE_CLASS.';
                    }
                    break;
            }
        } else {
            switch ($class['grpcls_status']) {
                case GroupClass::SCHEDULED:
                    if ($class['grpcls_currenttime_unix'] > $class['grpcls_endtime_unix']) {
                        $label = 'LBL_NOTE_END_TIME_FOR_THIS_CLASS_IS_PASSED';
                    }
                    break;
                case GroupClass::COMPLETED:
                    $label = 'LBL_NOTE_THIS_CLASS_IS_COMPLETED_ENCOURAGE_YOUR_STUDENT_TO_RATE_IT.';
                    break;
                case GroupClass::CANCELLED:
                    $label = 'LBL_NOTE_THIS_CLASS_HAS_BEEN_CANCELLED/DELETED';
                    break;
            }
        }
        if ($class['repiss_id'] > 0) {
            $label = 'LBL_NOTE_AN_ISSUE_IS_REPORTED';
        }
        return empty($label) ? '' : Label::getLabel($label);
    }

    /**
     * Get Search Form
     * 
     * @param int $usertype
     * @return Form
     */
    public static function getSearchForm(int $usertype, $forCalendarView = false): Form
    {
        $frm = new Form('frmClassSearch');
        $frm->addTextBox(Label::getLabel('LBL_TITLE'), 'title');
        $teachLangs = TeachLanguage::getAllLangs(MyUtility::getSiteLangId(), true);
        $questionTypes = [
            1 => Label::getLabel('LBL_SINGLE_CHOICE'),   // Single Choice Question
            2 => Label::getLabel('LBL_MULTIPLE_CHOICE'), // Multiple Choice Question
            3 => Label::getLabel('LBL_TEXT_BASED')       // Text-based Question
        ];
        $frm->addSelectBox(Label::getLabel('LBL_QUESTION_TYPE'), 'grpcls_tlang_id', $questionTypes, '', [], Label::getLabel('LBL_SELECT'));

        $categories = Category::getCategoriesByParentId(1);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'grpcls_q_cat', $categories, '', [], Label::getLabel('LBL_SELECT'));
 
        $fld = $frm->addSelectBox(Label::getLabel('LBL_SUBCATEGORY'), 'grpcls_subcate_id', [], '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setInt();
      if ($usertype == User::TEACHER) {
            $status = ['-1' => Label::getLabel('LBL_ALL_CLASSSES')] + GroupClass::getStatuses();
            $frm->addRadioButtons(Label::getLabel('LBL_STATUS'), 'grpcls_status', $status, GroupClass::SCHEDULED);
        } elseif ($usertype == User::LEARNER) {
            $status = ['-1' => Label::getLabel('LBL_ALL_CLASSSES')] + OrderClass::getStatuses();
            $frm->addRadioButtons(Label::getLabel('LBL_STATUS'), 'ordcls_status', $status, GroupClass::SCHEDULED);
        }
        if ($forCalendarView) {
            $frm->addRequiredField(Label::getLabel('LBL_START'), 'start');
            $frm->addRequiredField(Label::getLabel('LBL_END'), 'end');
        }
        $frm->addHiddenField(Label::getLabel('LBL_PAGESIZE'), 'pagesize', 10)->requirements()->setInt();
        $frm->addHiddenField(Label::getLabel('LBL_PAGENO'), 'pageno', 1)->requirements()->setInt();
        $frm->addHiddenField('', 'package_id')->requirements()->setInt();
        $frm->addHiddenField('', 'ordcls_id')->requirements()->setInt();
        $frm->addHiddenField('', 'grpcls_id')->requirements()->setInt();
        $frm->addHiddenField('', 'order_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEARCH'));
        $frm->addResetButton('', 'btn_clear', Label::getLabel('LBL_CLEAR'));
        return $frm;
    }

    /**
     * Get Detail Fields
     * 
     * @return array
     */
    public static function getDetailFields(): array
    {
        return static::getListingFields();
    }

    /**
     * Get Listing Fields
     * 
     * @return array
     */
    public static function getListingFields(): array
    {
        return [
            'orders.order_id' => 'order_id',
            'orders.order_type' => 'order_type',
            'orders.order_user_id' => 'order_user_id',
            'orders.order_pmethod_id' => 'order_pmethod_id',
            'orders.order_discount_value' => 'order_discount_value',
            'orders.order_currency_code' => 'order_currency_code',
            'orders.order_currency_value' => 'order_currency_value',
            'orders.order_payment_status' => 'order_payment_status',
            'orders.order_total_amount' => 'order_total_amount',
            'orders.order_addedon' => 'order_addedon',
            'ordcls.ordcls_id' => 'ordcls_id',
            'ordcls.ordcls_starttime' => 'ordcls_starttime',
            'ordcls.ordcls_endtime' => 'ordcls_endtime',
            'ordcls.ordcls_starttime' => 'ordcls_starttime',
            'ordcls.ordcls_endtime' => 'ordcls_endtime',
            'ordcls.ordcls_commission' => 'ordcls_commission',
            'ordcls.ordcls_amount' => 'ordcls_amount',
            'ordcls.ordcls_discount' => 'ordcls_discount',
            'ordcls.ordcls_refund' => 'ordcls_refund',
            'ordcls.ordcls_status' => 'ordcls_status',
            'ordcls.ordcls_updated' => 'ordcls_updated',
            'ordcls.ordcls_reviewed' => 'ordcls_reviewed',
            'ordcls.ordcls_teacher_paid' => 'ordcls_teacher_paid',
            'ordcls_ended_by' => 'ordcls_ended_by',
            'grpcls.grpcls_id' => 'grpcls_id',
            'grpcls.grpcls_type' => 'grpcls_type',
            'grpcls.grpcls_parent' => 'grpcls_parent',
            'grpcls.grpcls_teacher_id' => 'grpcls_teacher_id',
            'grpcls.grpcls_tlang_id' => 'grpcls_tlang_id',
            'grpcls.grpcls_start_datetime' => 'grpcls_start_datetime',
            'grpcls.grpcls_end_datetime' => 'grpcls_end_datetime',
            'grpcls.grpcls_booked_seats' => 'grpcls_booked_seats',
            'grpcls.grpcls_total_seats' => 'grpcls_total_seats',
            'grpcls.grpcls_entry_fee' => 'grpcls_entry_fee',
            'grpcls.grpcls_added_on' => 'grpcls_added_on',
            'grpcls.grpcls_status' => 'grpcls_status',
            'grpcls.grpcls_metool_id' => 'grpcls_metool_id',
            'grpcls.grpcls_teacher_starttime' => 'grpcls_teacher_starttime',
            'grpcls.grpcls_teacher_endtime' => 'grpcls_teacher_endtime',
            'teacher.user_country_id' => 'teacher_country_id',
            'teacher.user_username' => 'teacher_username',
            'teacher.user_first_name' => 'teacher_first_name',
            'teacher.user_last_name' => 'teacher_last_name',
            'learner.user_country_id' => 'learner_country_id',
            'learner.user_username' => 'learner_username',
            'learner.user_first_name' => 'learner_first_name',
            'learner.user_last_name' => 'learner_last_name',
            'learner.user_deleted' => 'learner_deleted',
            'teacher.user_deleted' => 'teacher_deleted',
            'IFNULL(gclang.grpcls_title, grpcls.grpcls_title)' => 'grpcls_title',
            'IFNULL(gclang.grpcls_description, grpcls.grpcls_description)' => 'grpcls_description',
        ];
    }

    /**
     * Upcoming Classes
     * 
     * @return array
     */
    public function upcomingClasses(): array
    {
        $this->applyPrimaryConditions();
        $this->addSearchListingFields();
        $this->addCondition('grpcls_start_datetime', '>', date('Y-m-d H:i:s'));
        $this->addOrder('grpcls_start_datetime', 'Asc');
        $this->setPageSize(3);
        return $this->fetchAndFormat();
    }

    /**
     * Group Dates
     * 
     * @param array $rows
     * @return array
     */
    public function groupDates(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }
        $classes = [];
        /*foreach ($rows as $row) {
            $key = date('Y-m-d', $row['grpcls_starttime_unix']);
            if (isset($classes[$key])) {
                array_push($classes[$key], $row);
            } else {
                $classes[$key] = [$row];
            }
        }*/
        return $rows;
    }
}
