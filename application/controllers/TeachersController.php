<?php

/**
 * Teachers Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class TeachersController extends MyAppController
{

    /**
     * Initialize Teachers
     * 
     * @param string $action
     */
    function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Teachers
     * 
     * @param string $slug
     */
    public function index(string $slug = '')
    {
        $postedData = FatApp::getPostedData();
        if (!empty($slug)) {
            $teachLangs = TeachLanguage::getTeachLanguages($this->siteLangId);
            $teachlangArr = array_column($teachLangs, 'tlang_slug', 'tlang_id');
            $postedData['teachs'] = [array_search($slug, $teachlangArr)];
        }
        $searchSession = $_SESSION[AppConstant::SEARCH_SESSION] ?? [];
        $srchFrm = TeacherSearch::getSearchForm($this->siteLangId);
        $srchFrm->fill($postedData + $searchSession);
        unset($_SESSION[AppConstant::SEARCH_SESSION]);
        $this->set('srchFrm', $srchFrm);
        $this->set('setMonthAndWeekNames', true);
        $this->set('languages', TeachLanguage::getAllLangs($this->siteLangId, true));
        $this->_template->addJs([
            'js/moment.min.js',
            'js/fullcalendar-luxon.min.js',
            'js/fullcalendar.min.js',
            'js/fullcalendar-luxon-global.min.js',
            'js/fateventcalendar.js',
        ]);
        $this->_template->render(true, true, 'teachers/index.php');
    }

    /**
     * Render Teachers based on Language
     * 
     * @param string $slug
     */
    public function languages(string $slug = '')
    {
        $this->index($slug);
    }

    /**
     * Find Teachers
     */
    public function search()
    {
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = TeacherSearch::getSearchForm($langId);
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new TeacherSearch($langId, $userId, $userType);
        $srch->addCondition('teacher.user_id', '!=', $userId);
        $srch->addSearchListingFields();
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->applyOrderBy($post['sorting']);
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $teachers = $srch->fetchAndFormat();
        $recordCount = $srch->recordCount();
        $this->set('post', $post);
        $this->set('teachers', $teachers);
        $this->set('recordCount', $recordCount);
        $this->set('pageCount', ceil($recordCount / $posts['pagesize']));
        $this->set('slots', MyUtility::timeSlotArr());
        $this->_template->render(false, false);
    }

    /**
     * Render Teacher Detail Page
     * 
     * @param string $username
     */
    public function view($username)
    {
        $srch = new TeacherSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'us.user_id = teacher.user_id', 'us');
        $srch->addCondition('teacher.user_username', '=', $username);
        $srch->addFld('us.user_trial_enabled');
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $teachers = $srch->fetchAndFormat(true);
        if (empty($teachers)) {
            FatUtility::exitWithErrorCode(404);
        }
        $teacher = current($teachers);
        $teachLangPrices = $this->teachLangPrices($teacher['user_id']);
        $teacher['teachLanguages'] = array_column($teachLangPrices, 'teachLangName', 'utlang_tlang_id');
        $teacher['proficiencyArr'] = SpeakLanguage::getProficiencies();
        if ($teacher['testat_reviewes'] > 0) {
            $reviewFrm = $this->getReviewForm();
            $reviewFrm->fill(['teacher_id' => $teacher['user_id']]);
            $this->set('reviewFrm', $reviewFrm);
        }
        $freeTrialConf = FatApp::getConfig('CONF_ENABLE_FREE_TRIAL', FatUtility::VAR_INT, 0);
        $freeTrialEnabled = ($teacher['user_trial_enabled'] && $freeTrialConf);
        $isFreeTrailAvailed = true;
        if ($freeTrialEnabled) {
            $isFreeTrailAvailed = Lesson::isTrailAvailed($this->siteUserId, $teacher['user_id']);
        }
        $userPreferences = Preference::getUserPreferences($teacher['user_id'], $this->siteLangId);
        $preferencesData = [];
        foreach ($userPreferences as $value) {
            $preferencesData[$value['prefer_type']][$value['uprefer_prefer_id']] = $value;
        }
        unset($userPreferences);
        $qualifications = (new UserQualification(0, $teacher['user_id']))->getUQualification();
        $userQualifications = [];
        foreach ($qualifications as $value) {
            $userQualifications[$value['uqualification_experience_type']][$value['uqualification_id']] = $value;
        }
        $class = new GroupClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $courseObj = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $moreCourses = $courseObj->getMoreCourses($teacher['user_id']);
        /* checkout form */
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $checkoutForm = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $checkoutForm->fill(['order_type' => Order::TYPE_COURSE]);

        $this->sets([
            'isFreeTrailAvailed' => $isFreeTrailAvailed,
            'userPreferences' => $preferencesData,
            'preferencesType' => Preference::getPreferenceTypeArr(),
            'userQualifications' => $userQualifications,
            'qualificationType' => UserQualification::getExperienceTypeArr(),
            'classes' => $class->getUpcomingClasses(['teacher_id' => $teacher['user_id']]),
            'bookingBefore' => FatApp::getConfig('CONF_CLASS_BOOKING_GAP'),
            'teacher' => $teacher,
            'userTeachLangs' => $teachLangPrices,
            'moreCourses' => $moreCourses,
            'checkoutForm' => $checkoutForm,
            'freeTrialEnabled' => $freeTrialEnabled,
            'setMonthAndWeekNames' => true,
        ]);
        $this->_template->addJs([
            'js/moment.min.js',
            'js/fullcalendar-luxon.min.js',
            'js/fullcalendar.min.js',
            'js/fullcalendar-luxon-global.min.js',
            'js/fateventcalendar.js'
        ]);
        $this->_template->render();
    }

    /**
     * Teach Lang Prices
     * 
     * @param int $teacherId
     * @return array
     */
    private function teachLangPrices(int $teacherId): array
    {
        $userTeachLang = new UserTeachLanguage($teacherId);
        $srchLang = $userTeachLang->getSrchObject($this->siteLangId, true);
        $srchLang->doNotCalculateRecords();
        $srchLang->addMultipleFields([
            'IFNULL(tlang_name, tlang_identifier) as teachLangName', 'utlang_id', 'utlang_tlang_id',
            'ustelgpr_slot', 'ustelgpr_price', 'ustelgpr_min_slab', 'ustelgpr_max_slab', 'ustelgpr_price'
        ]);
        $srchLang->addCondition('ustelgpr_price', '>', 0);
        $srchLang->addCondition('ustelgpr_min_slab', '>', 0);
        return FatApp::getDb()->fetchAll($srchLang->getResultSet());
    }

    /**
     * Render Teacher reviews
     */
    public function reviews()
    {
        $frm = $this->getReviewForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(Label::getLabel('LBL_CANNOT_LOAD_REVIEWS'));
        }
        $srch = new SearchBase(RatingReview::DB_TBL, 'ratrev');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id=ratrev.ratrev_user_id', 'learner');
        $srch->addCondition('ratrev.ratrev_status', '=', RatingReview::STATUS_APPROVED);
        $srch->addCondition('ratrev.ratrev_teacher_id', '=', $post['teacher_id']);
        $srch->addMultipleFields([
            'user_first_name', 'user_last_name', 'ratrev_id', 'ratrev_user_id',
            'ratrev_title', 'ratrev_detail', 'ratrev_overall', 'ratrev_created'
        ]);
        $sorting = FatApp::getPostedData('sorting', FatUtility::VAR_STRING, RatingReview::SORTBY_NEWEST);
        $srch->addOrder('ratrev.ratrev_id', $sorting);
        $srch->setPageSize(AppConstant::PAGESIZE);
        $srch->setPageNumber($post['pageno']);
        $this->set('reviews', FatApp::getDb()->fetchAll($srch->getResultSet()));
        $this->set('pageCount', $srch->pages());
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    /**
     * Get Review Form
     * 
     * @return Form
     */
    private function getReviewForm(): Form
    {
        $frm = new Form('reviewFrm');
        $fld = $frm->addHiddenField('', 'teacher_id');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setIntPositive();
        $frm->addHiddenField('', 'sorting', RatingReview::SORTBY_NEWEST);
        $frm->addHiddenField('', 'pageno', 1);
        return $frm;
    }

    /**
     * Render Calendar View
     */
    public function viewCalendar()
    {
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $user = new User($teacherId);
        if (!$teacher = $user->validateTeacher($this->siteLangId, $this->siteUserId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->sets(['teacher' => $teacher,
            'duration' => FatApp::getConfig('CONF_DEFAULT_PAID_LESSON_DURATION')]);
        $this->_template->render(false, false);
    }

    /**
     * Check Slot Availability
     * 
     * @param type $teacherId
     */
    public function checkSlotAvailability($teacherId = 0)
    {
        $teacherId = FatUtility::int($teacherId);
        $form = $this->getAvailabilityForm();
        if ($teacherId < 1 || !$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $startDateTime = MyDate::formatToSystemTimezone($post['start']);
        $endDateTime = MyDate::formatToSystemTimezone($post['end']);
        if (strtotime($startDateTime) < time()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_START_TIME_MUST_BE_GREATER_THEN_CURRENT_TIME'));
        }
        /** check teacher availability */
        $availability = new Availability($teacherId);
        if (!$availability->isAvailable($startDateTime, $endDateTime)) {
            FatUtility::dieJsonError($availability->getError());
        }
        /** check teacher slot availability */
        if (!$availability->isUserAvailable($startDateTime, $endDateTime)) {
            FatUtility::dieJsonError($availability->getError());
        }
        /** check Learner slot availability */
        if ($this->siteUserId > 0) {
            $availability = new Availability($this->siteUserId);
            if (!$availability->isUserAvailable($startDateTime, $endDateTime)) {
                FatUtility::dieJsonError($availability->getError());
            }
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SLOT_AVAILABLE'));
    }

    /**
     * Get Scheduled Sessions
     * 
     * @param int $userId
     */
    public function getScheduledSessions($userId)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $userType = Fatapp::getPostedData('user_type', FatUtility::VAR_INT, 0);
        $start = Fatapp::getPostedData('start', FatUtility::VAR_STRING, '');
        $end = Fatapp::getPostedData('end', FatUtility::VAR_STRING, '');
        if (empty($start) || empty($end)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $start = MyDate::formatToSystemTimezone($start);
        $end = MyDate::formatToSystemTimezone($end);
        $userIds = [$userId];
        if ($this->siteUserId > 0 && $this->siteUserId != $userId) {
            array_push($userIds, $this->siteUserId);
        }
        $groupClasses = [];
        $classes = [];
        $lessons = $this->getScheduledLessons($userIds, $start, $end, $userType);
        $classes = $this->getScheduledClasses($userIds, $start, $end, $userType);
        if ($userType != User::LEARNER) {
            $classIds = array_column($classes, 'classId', 'classId');
            $groupClasses = $this->getClasses($userIds, $start, $end, $classIds);
        }
        FatUtility::dieJsonSuccess(['data' => array_merge($lessons, $classes, $groupClasses)]);
    }

    /**
     * Get Scheduled Lessons
     * 
     * @param array $userIds
     * @param string $start
     * @param string $end
     * @param int $userType
     * @return array
     */
    private function getScheduledLessons(array $userIds, string $start, string $end, int $userType = 0)
    {
        $srch = new SearchBase(Lesson::DB_TBL, 'ordles');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = ordles.ordles_order_id ', 'orders');
        if ($userType == User::LEARNER) {
            $srch->addCondition('order_user_id', 'IN', $userIds);
        } elseif ($userType == User::TEACHER) {
            $srch->addCondition('ordles_teacher_id', 'IN', $userIds);
        } else {
            $cond = $srch->addCondition('ordles_teacher_id', 'IN', $userIds);
            $cond->attachCondition('order_user_id', 'IN', $userIds);
        }
        $srch->addCondition('ordles_status', '=', Lesson::SCHEDULED);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('orders.order_status', '=', Order::STATUS_COMPLETED);
        $srch->addCondition('ordles_lesson_starttime', '<', $end);
        $srch->addCondition('ordles_lesson_endtime', '>', $start);
        $srch->addMultipleFields(['ordles_lesson_starttime', 'ordles_lesson_endtime']);
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        $jsonArr = [];
        while ($record = FatApp::getDb()->fetch($resultSet)) {
            array_push($jsonArr, [
                "title" => "",
                "start" => MyDate::formatDate($record['ordles_lesson_starttime']),
                "end" => MyDate::formatDate($record['ordles_lesson_endtime']),
                "className" => "sch_data booked-slot"
            ]);
        }
        return $jsonArr;
    }

    /**
     * Get Scheduled Classes
     * 
     * @param array $userIds
     * @param string $start
     * @param string $end
     * @param int $userType
     * @return array
     */
    private function getScheduledClasses(array $userIds, string $start, string $end, int $userType = 0)
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(OrderClass::DB_TBL, 'INNER JOIN', 'ordcls.ordcls_order_id = orders.order_id', 'ordcls');
        $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id =  ordcls.ordcls_grpcls_id', 'grpcls');
        if ($userType == User::LEARNER) {
            $srch->addCondition('order_user_id', 'IN', $userIds);
        } elseif ($userType == User::TEACHER) {
            $srch->addCondition('grpcls_teacher_id', 'IN', $userIds);
        } else {
            $cond = $srch->addCondition('grpcls_teacher_id', 'IN', $userIds);
            $cond->attachCondition('order_user_id', 'IN', $userIds);
        }
        $srch->addCondition('grpcls_type', '=', GroupClass::TYPE_REGULAR);
        $srch->addCondition('orders.order_payment_status', '=', Order::ISPAID);
        $srch->addCondition('orders.order_status', '=', Order::STATUS_COMPLETED);
        $srch->addCondition('ordcls_status', '=', OrderClass::SCHEDULED);
        $srch->addCondition('grpcls_start_datetime', '<', $end);
        $srch->addCondition('grpcls_end_datetime', '>', $start);
        $srch->addMultipleFields(['grpcls_start_datetime', 'grpcls_end_datetime', 'grpcls_id']);
        $srch->addGroupBy('grpcls_id');
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        $jsonArr = [];
        while ($record = FatApp::getDb()->fetch($resultSet)) {
            array_push($jsonArr, [
                "title" => "",
                'classId' => $record['grpcls_id'],
                "start" => MyDate::formatDate($record['grpcls_start_datetime']),
                "end" => MyDate::formatDate($record['grpcls_end_datetime']),
                "className" => "sch_data booked-slot"
            ]);
        }
        return $jsonArr;
    }

    /**
     * Get Classes
     * 
     * @param array $userIds
     * @param string $start
     * @param string $end
     * @param array $classIds
     * @return array
     */
    private function getClasses(array $userIds, string $start, string $end, array $classIds)
    {
        $srch = new SearchBase(GroupClass::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls_teacher_id', 'IN', $userIds);
        $srch->addCondition('grpcls_status', '=', GroupClass::SCHEDULED);
        $srch->addCondition('grpcls_type', '=', GroupClass::TYPE_REGULAR);
        $srch->addCondition('grpcls_start_datetime', '< ', $end);
        $srch->addCondition('grpcls_end_datetime', ' > ', $start);
        $srch->addMultipleFields(['grpcls_start_datetime', 'grpcls_end_datetime', 'grpcls_id']);
        $resultSet = $srch->getResultSet();
        $jsonArr = [];
        while ($record = FatApp::getDb()->fetch($resultSet)) {
            if (array_key_exists($record['grpcls_id'], $classIds)) {
                continue;
            }
            array_push($jsonArr, [
                "title" => "",
                "start" => MyDate::formatDate($record['grpcls_start_datetime']),
                "end" => MyDate::formatDate($record['grpcls_end_datetime']),
                "className" => "sch_data booked-slot"
            ]);
        }
        return $jsonArr;
    }

    /**
     * Get Availability JSON Data
     * 
     * @param type $userId
     */
    public function getAvailabilityJsonData($userId)
    {
        $userId = FatUtility::int($userId);
        $start = FatApp::getPostedData('start', FatUtility::VAR_STRING, '');
        $end = FatApp::getPostedData('end', FatUtility::VAR_STRING, '');
        $sub = FatApp::getPostedData('sub', FatUtility::VAR_INT, 0);
        if (empty($start) || empty($end)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $start = MyDate::formatToSystemTimezone($start);
        $end = MyDate::formatToSystemTimezone($end);
        $availability = new Availability($userId);
        FatUtility::dieJsonSuccess(['data' => $availability->getAvailability($start, $end, $sub)]);
    }

    /**
     * Get Schedules
     * 
     * @param type $userId
     * @param type $start
     * @param type $end
     * @return type
     */
    public function getSchedules($userId = 0, $start, $end)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (empty($start) || empty($end)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $onlyTeacher = Fatapp::getPostedData('onlyTeacher', FatUtility::VAR_INT, 0);
        $includeGclass = Fatapp::getPostedData('includeGclass', FatUtility::VAR_INT, 1);
        $userTimezone = $this->siteTimezone;
        $systemTimeZone = MyUtility::getSystemTimezone();
        $start = MyDate::changeDateTimezone($start, $userTimezone, $systemTimeZone);
        $end = MyDate::changeDateTimezone($end, $userTimezone, $systemTimeZone);
        $userIds = [];
        $userIds[] = $userId;
        if ($this->siteUserId > 0 && !$onlyTeacher) {
            $userIds[] = $this->siteUserId;
        }
        $lessonData = $this->getScheduledLessons($userIds, $start, $end, $onlyTeacher);
        $classData = $this->getScheduledClasses($userIds, $start, $end, $onlyTeacher);
        $GClassData = [];
        if ($includeGclass) {
            $classIds = array_column($classData, 'classId', 'classId');
            $GClassData = $this->getClasses($userIds, $start, $end, $classIds);
        }
        return array_merge($lessonData, $classData, $GClassData);
    }

    /**
     * Get Availability Form
     * 
     * @return Form
     */
    private function getAvailabilityForm(): Form
    {
        $frm = new Form('availabilityForm');
        $startFld = $frm->addRequiredField(Label::getLabel('LBL_START_TIME'), 'start');
        $startFld->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
        $endFld = $frm->addRequiredField(Label::getLabel('LBL_END_TIME'), 'end');
        $endFld->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
        $endFld->requirements()->setCompareWith('start', 'gt', Label::getLabel('LBL_START_TIME'));
        return $frm;
    }

}
