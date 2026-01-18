<?php

/**
 * This class is used to handle Cart|Checkout
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Cart extends FatModel
{

    const DB_TBL = 'tbl_user_cart';
    const DB_TBL_PREFIX = 'cart_';
    /* Item Types */
    const LESSON = 'LESSON';
    const SUBSCR = 'SUBSCR';
    const GCLASS = 'GCLASS';
    const PACKGE = 'PACKGE';
    const COURSE = 'COURSE';

    private $userId;
    private $langId;
    private $coupon;
    private $items;

    /**
     * Initialize Cart
     * 
     * @param int $userId
     * @param int $langId
     */
    public function __construct(int $userId, int $langId)
    {
        parent::__construct();
        $this->userId = $userId;
        $this->langId = $langId;
        $row = $this->getData();
        $this->coupon = $row['cart_coupon'] ?? [];
        $this->items = [
            static::LESSON => $row['cart_items'][static::LESSON] ?? [],
            static::SUBSCR => $row['cart_items'][static::SUBSCR] ?? [],
            static::GCLASS => $row['cart_items'][static::GCLASS] ?? [],
            static::PACKGE => $row['cart_items'][static::PACKGE] ?? [],
            static::COURSE => $row['cart_items'][static::COURSE] ?? [],
        ];
    }
private function liveLessonsUnavailableMsg(): string
{
    return "Live lesson booking is temporarily unavailable.\n\n"
        . "You can still access Quizzes, Courses, Flashcards and other learning resources.\n\n"
        . "We are working to restore this feature soon. Thank you for your patience.";
}

    /**
     * Add Lesson to Cart
     * 
     * 1. Get Valid Lesson Price
     * 2. Create Item Unique Key
     * 3. Apply Offer & Set into Cart
     * 
     * @param array $lesson = [
     *          ordles_teacher_id, ordles_tlang_id, ordles_duration, ordles_type,
     *          ordles_quantity, ordles_starttime, ordles_endtime 
     *          ];
     * @return bool
     */
    public function addLesson(array $lesson): bool
    {
$this->error = $this->liveLessonsUnavailableMsg();
return false;

        $this->clear();
        /* Validate Lesson Data */
        // $lessonObj = new Lesson(0, $this->userId, User::LEARNER);
        // if (!$lesson = $lessonObj->getLessonPrice($lesson)) {
        //     $this->error = $lessonObj->getError();
        //     return false;
        // }
        $lessonObj = new Lesson(0, $this->userId, User::LEARNER);

/* First try the standard (strict) pricing */
$pricedLesson = $lessonObj->getLessonPrice($lesson);

if (!$pricedLesson) {
    /* Fallback: allow incomplete profile tutors to be added to cart */
    $pricedLesson = $this->getLessonPriceLenient($lesson);

    if (!$pricedLesson) {
        $this->error = $lessonObj->getError(); // keep original error if possible
        if (empty($this->error)) {
            $this->error = Label::getLabel('LBL_LESSON_NOT_AVAILABLE');
        }
        return false;
    }
}

$lesson = $pricedLesson;

        /* Create Item Unique Key */
        $key = implode('_', [
            $lesson['ordles_teacher_id'], $lesson['ordles_tlang_id'], $lesson['ordles_duration'],
            $lesson['ordles_type'], $lesson['ordles_starttime'], $lesson['ordles_endtime']
        ]);
        /* Apply Offer & Set into Cart */
        $langName = TeachLanguage::getNames($this->langId, [$lesson['ordles_tlang_id']]);
        $lesson['ordles_tlang'] = $langName[$lesson['ordles_tlang_id']] ?? '';
        $lesson['ordles_amount'] = OfferPrice::applyLessonOffer($this->userId, $lesson);
        $lesson['total_amount'] = $lesson['ordles_amount'] * $lesson['ordles_quantity'];
        $this->items[static::LESSON][$key] = $lesson;
        return $this->refresh();
    }

    /**
     * Add Lesson Subscription to Cart
     * 
     * @param array $subscription
     * @return bool
     */
    public function addSubscription(array $subscription): bool
    {
        $this->clear();
        $subDays = FatApp::getConfig('CONF_RECURRING_SUBSCRIPTION_WEEKS') * 7;
        $dateRange = MyDate::getSubscriptionDates($subDays);
        $subscription['ordsub_startdate'] = MyDate::formatToSystemTimezone($dateRange['startDate']);
        $subscription['ordsub_enddate'] = MyDate::formatToSystemTimezone($dateRange['endDate']);
        $subscription['ordles_type'] = Lesson::TYPE_SUBCRIP;
        $lessonObj = new Lesson(0, $this->userId, User::LEARNER);
        if (!$subscription = $lessonObj->getLessonPrice($subscription)) {
            $this->error = $lessonObj->getError();
            return false;
        }
        $langName = TeachLanguage::getNames($this->langId, [$subscription['ordles_tlang_id']]);
        $subscription['ordles_tlang'] = $langName[$subscription['ordles_tlang_id']] ?? '';
        $subscription['ordles_amount'] = OfferPrice::applyLessonOffer($this->userId, $subscription);
        $subscription['total_amount'] = $subscription['ordles_amount'] * $subscription['ordles_quantity'];
        $this->items[static::SUBSCR] = $subscription;
        return $this->refresh();
    }

    /**
     * Add Group Class
     * 
     * 1. Get Group Class To Book
     * 2. Check Learner Availability
     * 3. Apply Offer & Set into Cart
     * 
     * @param int $classId
     * @return bool
     */
    public function addClass(int $classId): bool
    {
        $this->clear();
        $classObj = new GroupClass($classId, $this->userId, User::LEARNER);
        if (!$class = $classObj->getClassToBook($this->langId)) {
            $this->error = $classObj->getError();
            return false;
        }
        /* Get Group Class To Book */
        $unpaidOrder = OrderClass::getUnpaidClass($this->userId, $classId);
        if (!empty($unpaidOrder)) {
            $order = new Order($unpaidOrder['order_id'], $this->userId);
            if (!$order->cancelUnpaidOrder($unpaidOrder)) {
                $this->error = $order->getError();
                return false;
            }
        }
        /* Check In Process Orders */
        $unpaidSeats = OrderClass::getUnpaidSeats([$classId]);
        $unpaidSeatCount = $unpaidSeats[$classId] ?? 0;
        if (!empty($unpaidSeatCount) && $class['grpcls_total_seats'] <= ($unpaidSeatCount + $class['grpcls_booked_seats'])) {
            $this->error = Label::getLabel('LBL_PROCESSING_CLASS_ORDER_TEXT');
            return false;
        }
        /* Check Learner Availability */
        $avail = new Availability($this->userId);
        if (!$avail->isUserAvailable($class['grpcls_start_datetime'], $class['grpcls_end_datetime'])) {
            $this->error = $avail->getError();
            return false;
        }
        /* Apply Offer & Set into Cart */
        $class['ordcls_amount'] = OfferPrice::applyClassOffer($this->userId, $class);
        $class['total_amount'] = $class['ordcls_amount'];
        $this->items[static::GCLASS][$class['grpcls_id']] = $class;
        return $this->refresh();
    }

    /**
     * Add Class Package
     * 
     * 1. Get Group Class Package To Book
     * 2. Check In Process Orders 
     * 3. Check Learner Availability
     * 4. Apply Offer & Set into Cart
     * 
     * @param int $packageId
     * @return bool
     */
    public function addPackage(int $packageId): bool
    {
        $this->clear();
        $packageObj = new GroupClass($packageId, $this->userId, User::LEARNER);
        if (!$package = $packageObj->getPackageToBook($this->langId)) {
            $this->error = $packageObj->getError();
            return false;
        }
        /* Get Group Class Package To Book */
        $unpaidOrder = OrderPackage::getUnpaidPackage($this->userId, $packageId);
        if (!empty($unpaidOrder)) {
            $order = new Order($unpaidOrder['order_id'], $this->userId);
            if (!$order->cancelUnpaidOrder($unpaidOrder)) {
                $this->error = $order->getError();
                return false;
            }
        }
        /* Check In Process Orders */
        $unpaidSeats = OrderPackage::getUnpaidSeats([$packageId])[$packageId] ?? 0;
        if ($package['grpcls_total_seats'] <= ($package['grpcls_booked_seats'] + $unpaidSeats)) {
            $this->error = Label::getLabel('LBL_PROCESSING_PACKAGE_ORDER_TEXT');
            return false;
        }
        /* Check Learner Availability */
        $avail = new Availability($this->userId);
        $classes = PackageSearch::getClasses($packageId);
        foreach ($classes as $class) {
            $starttime = MyDate::formatToSystemTimezone($class['grpcls_start_datetime']);
            $endtime = MyDate::formatToSystemTimezone($class['grpcls_end_datetime']);
            if (!$avail->isUserAvailable($starttime, $endtime)) {
                $this->error = $avail->getError();
                return false;
            }
        }
        /* Apply Offer & Set into Cart */
        $package['classes'] = $classes;
        $package['grpcls_amount'] = OfferPrice::applyPackageOffer($this->userId, $package);
        $package['total_amount'] = $package['grpcls_amount'];
        $this->items[static::PACKGE][$package['grpcls_id']] = $package;
        return $this->refresh();
    }

    /**
     * Add Course to Cart
     * 
     * @param int $courseId
     * @return bool
     */
    public function addCourse(int $courseId): bool
    {
        $this->clear();
        $srch = new CourseSearch($this->langId, $this->userId, 0);
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions([
            'course_status' => Course::PUBLISHED,
            'course_id' => $courseId
        ]);
        $srch->addMultipleFields([
            'crsdetail.course_title AS course_title',
            'course_clang_id',
            'clang_identifier',
            'course_cate_id',
            'course_subcate_id',
            'course.course_price AS course_price',
            'course.course_currency_id AS course_currency_id',
            'course.course_id',
            'course.course_user_id',
            'crsdetail.course_srchtags',
        ]);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $courses = $srch->fetchAndFormat();
        $course = current($courses);
        if (empty($course)) {
            $this->error = Label::getLabel('LBL_COURSE_NOT_AVAILABLE');
            return false;
        }
        if ($course['course_user_id'] == $this->userId) {
            $this->error = Label::getLabel('LBL_YOU_ARE_NOT_ALLOWED_TO_ENROLL_TO_YOUR_OWN_COURSE');
            return false;
        }
        $unpaidOrders= OrderCourse::getUnpaidCourses($this->userId, $courseId);
        if (!empty($unpaidOrders)) {
            foreach ($unpaidOrders as $unpaidOrder) {
                $order = new Order($unpaidOrder['order_id'], $this->userId);
                if (!$order->cancelUnpaidOrder($unpaidOrder)) {
                    $this->error = $order->getError();
                    return false;
                }
            }
        }
        $course['total_amount'] = $course['course_price'];
        $this->items[static::COURSE][$courseId] = $course;
        return $this->refresh();
    }

    /**
     * Remove Lesson
     * 
     * @param string $key
     * @return bool
     */
    public function removeLesson(string $key): bool
    {
        unset($this->items[static::LESSON][$key]);
        return $this->refresh();
    }

    /**
     * Remove Subscription
     * 
     * @param int $classId
     * @return bool
     */
    public function removeSubscription(int $classId): bool
    {
        $this->items[static::SUBSCR] = [];
        return $this->refresh();
    }

    /**
     * Remove Group Class
     * 
     * @param int $classId
     * @return bool
     */
    public function removeClass(int $classId): bool
    {
        unset($this->items[static::GCLASS][$classId]);
        return $this->refresh();
    }

    /**
     * Remove Group Class
     * 
     * @param int $classId
     * @return bool
     */
    public function removePackage(int $classId): bool
    {
        unset($this->items[static::PACKGE][$classId]);
        return $this->refresh();
    }

    /**
     * Remove Course
     * 
     * @param int $courseId
     * @return bool
     */
    public function removeCourse(int $courseId): bool
    {
        unset($this->items[static::COURSE][$courseId]);
        return $this->refresh();
    }

    /**
     * Get Cart Items
     *
     * @return bool|array
     */
    public function getItems()
    {
        if ($this->getCount() < 1) {
            $this->error = Label::getLabel('LBL_CART_IS_EMPTY');
            return false;
        }
        return $this->items;
    }

    /**
     * get Coupon 
     *
     * @return array
     */
    public function getCoupon(): array
    {
        return $this->coupon;
    }

    /**
     * Get Discount
     *
     * @return float $discount
     */
    public function getDiscount(): float
    {
        return FatUtility::float($this->coupon['coupon_discount'] ?? '');
    }

    /**
     * Apply Coupon
     * 
     * @param string $code
     * @return bool
     */
    public function applyCoupon(string $code): bool
    {
        $coupon = new Coupon();
        $record = $coupon->validateCoupon($code, $this->getTotal(), $this->userId);
        if (empty($record)) {
            $this->error = $coupon->getError();
            return false;
        }
        $this->coupon = $record;
        return $this->refresh();
    }

    /**
     * Remove Coupon
     * 
     * @return bool
     */
    public function removeCoupon(): bool
    {
        $this->coupon = [];
        return $this->refresh();
    }

    /**
     * Clear Cart
     * 
     * @return bool
     */
    public function clear(): bool
    {
        $this->coupon = [];
        $this->items = [
            static::LESSON => [],
            static::SUBSCR => [],
            static::GCLASS => [],
            static::PACKGE => [],
            static::COURSE => [],
        ];
        return $this->refresh();
    }

    /**
     * Refresh Cart
     * 
     * @return bool
     */
    private function refresh(): bool
    {
        $cartData = [
            'cart_user_id' => $this->userId,
            'cart_items' => json_encode($this->items),
            'cart_coupon' => json_encode($this->coupon),
            'cart_updated' => date('Y-m-d H:i:s')
        ];
        $record = new TableRecord(static::DB_TBL);
        $record->assignValues($cartData);
        if (!$record->addNew([], $cartData)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Get Cart Data
     * 
     * @return bool|array
     */
    public function getData()
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->addCondition('cart_user_id', '=', $this->userId);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            $this->error = Label::getLabel('LBL_CART_IS_EMPTY');
            return false;
        }
        $row['cart_items'] = json_decode($row['cart_items'], true);
        $row['cart_coupon'] = json_decode($row['cart_coupon'], true);
        return $row;
    }

    /**
     * Item Count
     * 
     * @return int
     */
    public function getCount(): int
    {
        return array_sum([
            static::LESSON => count($this->items[static::LESSON]),
            static::SUBSCR => count($this->items[static::SUBSCR]['lessons'] ?? []),
            static::GCLASS => count($this->items[static::GCLASS]),
            static::PACKGE => count($this->items[static::PACKGE]),
            static::COURSE => count($this->items[static::COURSE]),
        ]);
    }

    /**
     * Get Order Total
     * 
     * @return float
     */
    public function getTotal(): float
    {
        $amount = array_sum(array_column($this->items[static::LESSON], 'total_amount'));
        $amount += $this->items[static::SUBSCR]['total_amount'] ?? 0;
        $amount += array_sum(array_column($this->items[static::GCLASS], 'total_amount'));
        $amount += array_sum(array_column($this->items[static::PACKGE], 'total_amount'));
        $amount += array_sum(array_column($this->items[static::COURSE], 'total_amount'));
        return FatUtility::float($amount);
    }

    /**
     * Get Item Types
     * 
     * @return array
     */
    public static function getItemTypes(): array
    {
        return [
            static::LESSON => static::LESSON,
            static::SUBSCR => static::SUBSCR,
            static::GCLASS => static::GCLASS,
            static::PACKGE => static::PACKGE,
            static::COURSE => static::COURSE,
        ];
    }

    /**
     * Get Lesson Form
     * 
     * @param int $quantity
     * @return Form
     */
    public function getLessonForm(int $quantity): Form
    {
        $frm = new Form('lessonForm');
        /* ordles_teacher_id */
        $teacherId = $frm->addHiddenField(Label::getLabel('LBL_TEACHER_ID'), 'ordles_teacher_id');
        $teacherId->requirements()->setRequired(true);
        $teacherId->requirements()->setIntPositive();
        /* ordles_tlang_id */
        $teachLang = $frm->addHiddenField(Label::getLabel('LBL_LANGUAGE_ID'), 'ordles_tlang_id');
        /* ordles_duration */
        $teachSlot = $frm->addHiddenField(Label::getLabel('LBL_LESSON_DURATION'), 'ordles_duration');
        $teachSlot->requirements()->setRequired(true);
        $teachSlot->requirements()->setIntPositive();
        /* ordles_type */
        $lessonType = $frm->addSelectBox(Label::getLabel('LBL_LESSON_TYPE'), 'ordles_type', Lesson::getLessonTypes());
        $lessonType->requirements()->setRequired(true);
        $lessonType->requirements()->setIntPositive();
        /* set teach Lang requried */
        /* order_item_count */
        $lessonQty = $frm->addHiddenField(Label::getLabel('LBL_LESSON_QUANTITY'), 'ordles_quantity', 1);
        $lessonQty->requirements()->setRange(1, 999999999999);
        $lessonType->requirements()->setIntPositive();
        $lessonQty->requirements()->setRequired(true);
        for ($i = 0; $i < $quantity; $i++) {
            /* ordles_starttime */
            $starttime = $frm->addHiddenField(Label::getLabel('LBL_START_TIME'), 'startTime[' . $i . ']');
            $starttime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
            /* ordles_endtime */
            $endtime = $frm->addHiddenField(Label::getLabel('LBL_END_TIME'), 'endTime[' . $i . ']');
            $endtime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
            /* set start time not requried */
            $endtime->requirements()->addOnChangerequirementUpdate('', 'eq', 'startTime[' . $i . ']', $starttime->requirements());
            /* set start time requried */
            $requirement = new FormFieldRequirement('startTime[' . $i . ']', Label::getLabel('LBL_START_TIME'));
            $requirement->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
            $requirement->setRequired(true);
            $endtime->requirements()->addOnChangerequirementUpdate('', 'ne', 'startTime[' . $i . ']', $requirement);
            /* set end time not requried */
            $starttime->requirements()->addOnChangerequirementUpdate('', 'eq', 'endTime[' . $i . ']', $endtime->requirements());
            /* set end time requried */
            $requirement = new FormFieldRequirement('endTime[' . $i . ']', Label::getLabel('LBL_END_TIME'));
            $requirement->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
            $requirement->setRequired(true);
            $starttime->requirements()->addOnChangerequirementUpdate('', 'ne', 'endTime[' . $i . ']', $requirement);
            /* ordsub_recurring */
        }
        /* ordles_starttime */
        $starttime = $frm->addHiddenField(Label::getLabel('LBL_START_TIME'), 'ordles_starttime');
        $starttime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
        /* ordles_endtime */
        $endtime = $frm->addHiddenField(Label::getLabel('LBL_END_TIME'), 'ordles_endtime');
        $endtime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
        return $frm;
    }

    /**
     * Get subscription Form
     * 
     * @param array $quantity
     * @return Form
     */
    public function getSubscriptionForm(int $quantity): Form
    {
        $frm = new Form('subcripForm');
        /* ordles_teacher_id */
        $teacherId = $frm->addHiddenField(Label::getLabel('LBL_TEACHER_ID'), 'ordles_teacher_id');
        $teacherId->requirements()->setRequired(true);
        $teacherId->requirements()->setIntPositive();
        /* ordles_tlang_id */
        $teachLang = $frm->addHiddenField(Label::getLabel('LBL_LANGUAGE_ID'), 'ordles_tlang_id');
        $teachLang->requirements()->setRequired(true);
        $teachLang->requirements()->setIntPositive();
        /* ordles_duration */
        $duration = $frm->addHiddenField(Label::getLabel('LBL_LESSON_DURATION'), 'ordles_duration');
        $duration->requirements()->setRequired(true);
        $duration->requirements()->setIntPositive();
        /* set teach Lang requried */
        /* order_item_count */
        $lessonQty = $frm->addHiddenField(Label::getLabel('LBL_LESSON_QUANTITY'), 'ordles_quantity', 1);
        $lessonQty->requirements()->setRange(1, 999999999999);
        $lessonQty->requirements()->setRequired(true);
        $subDays = FatApp::getConfig('CONF_RECURRING_SUBSCRIPTION_WEEKS') * 7 + 1;
        $dateRange = MyDate::getSubscriptionDates($subDays);
        $dateRange['startDate'] = MyDate::formatDate(date('Y-m-d H:i:s'));
        for ($i = 0; $i < $quantity; $i++) {
            $starttime = $frm->addRequiredField(Label::getLabel('LBL_START_TIME'), 'startTime[' . $i . ']');
            $starttime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
            $starttime->requirements()->setRange($dateRange['startDate'], $dateRange['endDate']);
            $endtime = $frm->addRequiredField(Label::getLabel('LBL_END_TIME'), 'endTime[' . $i . ']');
            $endtime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
            $endtime->requirements()->setRange($dateRange['startDate'], $dateRange['endDate']);
        }
        return $frm;
    }

    /**
     * Allow Bank Transfer Payment
     * 
     * @return bool
     */
    private function allowBankTransferPayment(): bool
    {
        $starttimes = [];
        foreach ($this->items[static::LESSON] as $lesson) {
            foreach ($lesson['lessons'] as $schedule) {
                array_push($starttimes, $schedule['ordles_starttime']);
            }
        }
        foreach ($this->items[static::SUBSCR]['lessons'] ?? [] as $schedule) {
            array_push($starttimes, $schedule['ordles_starttime']);
        }
        foreach ($this->items[static::GCLASS] as $class) {
            array_push($starttimes, $class['grpcls_start_datetime']);
        }
        foreach ($this->items[static::PACKGE] as $package) {
            foreach ($package['classes'] as $class) {
                array_push($starttimes, MyDate::formatToSystemTimezone($class['grpcls_start_datetime']));
            }
        }
        $starttimes = array_filter($starttimes);
        if (empty($starttimes)) {
            return true;
        }
        $hours = (new BankTransferPay([]))->getBookBeforeHours();
        return (date('Y-m-d H:i:s', strtotime('+' . $hours . ' hours')) <= min($starttimes));
    }

    /**
     * Get Payment Methods
     * 
     * @return array
     */
    private function getPaymentMethods(): array
    {
        $srch = new SearchBase(PaymentMethod::DB_TBL, 'pmethod');
        $srch->addMultipleFields(['pmethod_id', 'pmethod_code']);
        $srch->addCondition('pmethod_active', '=', AppConstant::YES);
        $srch->addCondition('pmethod_type', '=', PaymentMethod::TYPE_PAYIN);
        $cartNetAmount = $this->getTotal() - $this->getDiscount();
        if (User::getWalletBalance($this->userId) < $cartNetAmount) {
            $srch->addCondition('pmethod_code', '!=', WalletPay::KEY);
        }
        if (!$this->allowBankTransferPayment()) {
            $srch->addCondition('pmethod_code', '!=', BankTransferPay::KEY);
        }
        $srch->addOrder('pmethod_order', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $arr = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        foreach ($arr as $key => $value) {
            $arr[$key] = Label::getLabel('LBL_' . $value);
        }
        return $arr;
    }

    /**
     * Get Checkout Form
     * 
     * @param array $methods
     * @return Form
     */
    public function getCheckoutForm(array $methods = null): Form
    {
        $payins = is_null($methods) ? $this->getPaymentMethods() : $methods;
        $frm = new Form('checkoutForm', ['id' => 'checkoutForm']);
        $orderType = $frm->addSelectBox(Label::getLabel('LBL_ORDER_TYPE'), 'order_type', Order::getTypeArr());
        $orderType->requirements()->setRequired(true);
        $orderType->requirements()->setIntPositive();
        $pmethod = $frm->addSelectBox(Label::getLabel('LBL_PAYMENT_METHOD'), 'order_pmethod_id', $payins, array_key_first($payins));
        $pmethod->requirements()->setRequired(true);
        $pmethod->requirements()->setIntPositive();
        $frm->addTextBox(Label::getLabel('LBL_COUPON_CODE'), 'coupon_code');
        $fld = $frm->addTextBox(Label::getLabel('LBL_ADD_&_PAY'), 'add_and_pay', '');
        $fld->requirements()->setIntPositive();
        $frm->addButton(Label::getLabel('LBL_CONFIRM_PAYMENT'), 'submit', Label::getLabel('LBL_CONFIRM_PAYMENT'));
        return $frm;
    }
/**
 * Lenient lesson pricing (fallback)
 * Allows adding lesson to cart even if teacher profile flags are incomplete.
 * This is ONLY used when strict Lesson::getLessonPrice() fails.
 */
private function getLessonPriceLenient(array $lesson)
{
    $teacherId = FatUtility::int($lesson['ordles_teacher_id'] ?? 0);
    $tlangId   = FatUtility::int($lesson['ordles_tlang_id'] ?? 0);
    $duration  = FatUtility::int($lesson['ordles_duration'] ?? 0);
    $type      = FatUtility::int($lesson['ordles_type'] ?? 0);

    if ($teacherId < 1 || $duration < 1 || $type < 1) {
        return false;
    }

    $srch = new SearchBase(User::DB_TBL, 'teacher');
    $srch->joinTable('tbl_user_settings', 'LEFT JOIN', 'us.user_id = teacher.user_id', 'us');
    $srch->joinTable(UserTeachLanguage::DB_TBL, 'INNER JOIN', 'utlang.utlang_user_id = teacher.user_id', 'utlang');
    $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'ustelgpr.ustelgpr_utlang_id = utlang.utlang_id', 'ustelgpr');

    $srch->addCondition('teacher.user_id', '=', $teacherId);
    $srch->addCondition('teacher.user_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
    $srch->addCondition('teacher.user_active', '=', AppConstant::ACTIVE);
    $srch->addCondition('teacher.user_is_teacher', '=', AppConstant::YES);

    // optional, keep if your platform requires verified teachers
    $srch->addDirectCondition('teacher.user_verified IS NOT NULL');

    if ($tlangId > 0) {
        $srch->addCondition('utlang.utlang_tlang_id', '=', $tlangId);
    }

    $srch->addCondition('ustelgpr.ustelgpr_price', '>', 0);

    $srch->addMultipleFields([
        'teacher.user_id AS ordles_teacher_id',
        (($tlangId > 0) ? $tlangId : 'utlang.utlang_tlang_id') . ' AS ordles_tlang_id',
        $duration . ' AS ordles_duration',
        $type . ' AS ordles_type',
        'MIN(ustelgpr.ustelgpr_price) AS ordles_amount'
    ]);

    $srch->doNotCalculateRecords();
    $srch->setPageSize(1);

    $row = FatApp::getDb()->fetch($srch->getResultSet());

    if (empty($row) || FatUtility::float($row['ordles_amount'] ?? 0) <= 0) {
        return false;
    }

    $lesson['ordles_teacher_id'] = $teacherId;
    $lesson['ordles_tlang_id']   = FatUtility::int($row['ordles_tlang_id'] ?? $tlangId);
    $lesson['ordles_duration']   = $duration;
    $lesson['ordles_type']       = $type;
    $lesson['ordles_amount']     = FatUtility::float($row['ordles_amount']);

    $lesson['lessons'] = $lesson['lessons'] ?? [[
        'ordles_starttime' => $lesson['ordles_starttime'] ?? null,
        'ordles_endtime'   => $lesson['ordles_endtime'] ?? null,
    ]];

    return $lesson;
}

    /**
     * Get Checkout Steps
     * 
     * @return type
     */
    public static function getSteps()
    {
        return [
            1 => Label::getLabel('LBL_1'),
            2 => Label::getLabel('LBL_2'),
            3 => Label::getLabel('LBL_3'),
            4 => Label::getLabel('LBL_4')
        ];
    }
    

}
