<?php

/**
 * This class is used to handle Order
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Order extends MyAppModel
{

    const DB_TBL = 'tbl_orders';
    const DB_TBL_PREFIX = 'order_';
    const DB_TBL_LESSON = 'tbl_order_lessons';
    const DB_TBL_SUBSCR = 'tbl_order_subscriptions';
    const DB_TBL_GCLASS = 'tbl_order_classes';
    const DB_TBL_PACKAG = 'tbl_order_packages';
    const DB_TBL_COURSE = 'tbl_order_courses';
    const DB_TBL_PAYMENT = 'tbl_order_payments';
    /* Order Types */
    const TYPE_LESSON = 1;
    const TYPE_SUBSCR = 2;
    const TYPE_GCLASS = 3;
    const TYPE_PACKGE = 4;
    const TYPE_COURSE = 5;
    const TYPE_WALLET = 6;
    const TYPE_GFTCRD = 7;
    /* Order Status */
    const STATUS_INPROCESS = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 3;
    /* Payment Status */
    const UNPAID = 0;
    const ISPAID = 1;

    private $userId;
    private $orderId;
    private $coupon = [];
    private $lessons = [];
    private $subscrp = [];
    private $classes = [];
    private $packags = [];
    private $courses = [];

    /**
     * Initialize Order
     * @param int $orderId
     * @param int $userId
     */
    public function __construct(int $orderId = 0, int $userId = 0)
    {
        $this->userId = $userId;
        $this->orderId = $orderId;
        parent::__construct(static::DB_TBL, 'order_id', $orderId);
    }

    /**
     * Add Lessons
     * 
     * @param array $lessons = [
     *          ordles_teacher_id, ordles_tlang_id, ordles_duration, ordles_type,
     *          ordles_quantity, ordles_starttime, ordles_endtime
     *          ];
     * @return bool
     */
    private function addLessons(array $lessons): bool
    {
        if (empty($lessons)) {
            return true;
        }
        foreach ($lessons as $lesson) {
            // $lessonObj = new Lesson(0, $this->userId, User::LEARNER);
            // if (!$lesson = $lessonObj->getLessonPrice($lesson)) {
            //     $this->error = Label::getLabel('LBL_LESSON_NOT_AVAILABLE');
            //     return false;
            // }
            $lessonObj = new Lesson(0, $this->userId, User::LEARNER);

/* First try strict pricing */
$pricedLesson = $lessonObj->getLessonPrice($lesson);

if (!$pricedLesson) {
    /* Fallback: allow incomplete profile tutors (same as Cart.php behavior) */
    $pricedLesson = $this->getLessonPriceLenient($lesson);

    if (!$pricedLesson) {
        $this->error = $lessonObj->getError();
        if (empty($this->error)) {
            $this->error = Label::getLabel('LBL_LESSON_NOT_AVAILABLE');
        }
        return false;
    }
}

$lesson = $pricedLesson;

            $comm = Commission::getCommission($lesson['ordles_teacher_id']);
            $lessonData = [
                'ordles_type' => $lesson['ordles_type'],
                'ordles_amount' => OfferPrice::applyLessonOffer($this->userId, $lesson),
                'ordles_teacher_id' => $lesson['ordles_teacher_id'],
                'ordles_tlang_id' => $lesson['ordles_tlang_id'],
                'ordles_commission' => $comm['comm_lessons'],
                'ordles_lesson_starttime' => null,
                'ordles_lesson_endtime' => null,
                'ordles_duration' => $lesson['ordles_duration'],
                'ordles_status' => Lesson::UNSCHEDULED,
                'ordles_reviewed' => 0,
                'ordles_metool_id' => 0,
            
            ];
            foreach ($lesson['lessons'] as $value) {
                $value = array_merge($value, $lessonData);
                $value['ordles_lesson_starttime'] = $value['ordles_starttime'];
                $value['ordles_lesson_endtime'] = $value['ordles_endtime'];
                if (!empty($value['ordles_lesson_starttime']) && !empty($value['ordles_lesson_endtime'])) {
                    $value['ordles_status'] = Lesson::SCHEDULED;
                }
                array_push($this->lessons, $value);
            }
        }
        return true;
    }

    /**
     * Add Subscription
     * 
     * @param array $subscription
     * @return bool
     */
    private function addSubscriptions(array $subscription)
    {
        if (empty($subscription)) {
            return true;
        }
        $lesson = new Lesson(0, $this->userId, User::LEARNER);
        if (!$subscription = $lesson->getLessonPrice($subscription)) {
            $this->error = $lesson->getError();
            return false;
        }
        $lessonAmount = OfferPrice::applyLessonOffer($this->userId, $subscription);
        $comm = Commission::getCommission($subscription['ordles_teacher_id']);
        $subscription['total_amount'] = $lessonAmount * $subscription['ordles_quantity'];
        $subscription['ordsub_teacher_id'] = $subscription['ordles_teacher_id'];
        $lessonData = [
            'ordles_type' => Lesson::TYPE_SUBCRIP,
            'ordles_amount' => $lessonAmount,
            'ordles_teacher_id' => $subscription['ordles_teacher_id'],
            'ordles_tlang_id' => $subscription['ordles_tlang_id'],
            'ordles_commission' => $comm['comm_lessons'],
            'ordles_duration' => $subscription['ordles_duration'],
            'ordles_status' => Lesson::UNSCHEDULED,
            'ordles_reviewed' => 0,
        ];
        foreach ($subscription['lessons'] as $key => $value) {
            $value = array_merge($value, $lessonData);
            $value['ordles_lesson_starttime'] = $value['ordles_starttime'];
            $value['ordles_lesson_endtime'] = $value['ordles_endtime'];
            if (!empty($value['ordles_lesson_starttime']) && !empty($value['ordles_lesson_endtime'])) {
                $value['ordles_status'] = Lesson::SCHEDULED;
            }
            $subscription['lessons'][$key] = $value;
        }
        $this->subscrp = $subscription;
        return true;
    }

    /**
     * Add Classes
     * 
     * @param array $classes
     * @return bool
     */
    private function addClasses(array $classes): bool
    {
        if (empty($classes)) {
            return true;
        }
        foreach ($classes as $classId => $value) {
            $classObj = new GroupClass($classId, $this->userId, User::LEARNER);
            if (!$class = $classObj->getClassToBook()) {
                $this->error = Label::getLabel('LBL_CLASS_NOT_AVAILABLE');
                return false;
            }
            $unpaidSeats = OrderClass::getUnpaidSeats([$classId]);
            $unpaidSeatCount = $unpaidSeats[$classId] ?? 0;
            if (!empty($unpaidSeatCount) && $class['grpcls_total_seats'] <= ($unpaidSeatCount + $class['grpcls_booked_seats'])) {
                $this->error = Label::getLabel('LBL_PROCESSING_CLASS_ORDER_TEXT');
                return false;
            }
            $amount = OfferPrice::applyClassOffer($this->userId, $class);
            $comm = Commission::getCommission($class['grpcls_teacher_id']);
            array_push($this->classes, [
                'ordcls_starttime' => null,
                'ordcls_endtime' => null,
                'ordcls_amount' => $amount,
                'ordcls_grpcls_id' => $classId,
                'ordcls_status' => OrderClass::SCHEDULED,
                'ordcls_type' => GroupClass::TYPE_REGULAR,
                'ordcls_commission' => $comm['comm_classes'],
            ]);
        }
        return true;
    }

    /**
     * Add packages
     * 
     * @param array $packages
     * @return bool
     */
    private function addPackages(array $packages): bool
    {
        if (empty($packages)) {
            return true;
        }
        foreach ($packages as $packageId => $value) {
            $classObj = new GroupClass($packageId, $this->userId, User::LEARNER);
            if (!$package = $classObj->getPackageToBook()) {
                $this->error = Label::getLabel('LBL_CLASS_PACKAGE_NOT_AVAILABLE');
                return false;
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
            $amount = OfferPrice::applyPackageOffer($this->userId, $package);
            $comm = Commission::getCommission($package['grpcls_teacher_id']);
            $record = [
                'classes' => $classes,
                'ordpkg_amount' => $amount,
                'ordpkg_package_id' => $packageId,
                'ordpkg_commission' => $comm['comm_classes'],
                'ordpkg_status' => OrderPackage::SCHEDULED,
            ];
            array_push($this->packags, $record);
        }
        return true;
    }

    /**
     * Add Courses
     * 
     * @param array $courseIds
     * @return bool
     */
    private function addCourses(array $courses): bool
    {
        if (empty($courses)) {
            return true;
        }

        $courseIds = array_keys($courses);
        $srch = new SearchBase(Course::DB_TBL, 'course');
        $srch->addCondition('course.course_id', 'IN', $courseIds);
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $srch->addMultipleFields(['course_id', 'course_price', 'course_user_id']);
        $rows = FatApp::getDb()->fetchAll($srch->getResultSet(), 'course_id');
        if (count($rows) < count($courseIds)) {
            $this->error = Label::getLabel('LBL_COURSE_NOT_AVAILABLE');
            return false;
        }
        foreach ($courseIds as $courseId) {
            $commission = Commission::getCommission($rows[$courseId]['course_user_id']);
            array_push($this->courses, [
                'ordcrs_course_id' => $courseId,
                'ordcrs_amount' => $rows[$courseId]['course_price'],
                'ordcrs_commission' => $commission['comm_courses'],
                'ordcrs_status' => OrderCourse::IN_PROGRESS,
                'ordcrs_certificate_number' => '',
                'ordcrs_reviewed' => 0,
                'ordcrs_payment' => AppConstant::UNPAID,
            ]);
        }
        return true;
    }

    /**
     * Place Order
     * 
     * Prepare Order Data
     * Insert Order Record
     * Insert Order Coupon
     * Insert Order Lessons
     * Insert Order Classes
     * Insert Order Courses
     * Add Order Payments
     * 
     * @param int $type
     * @param int $pmethodId
     * @param int $addPay
     * @return bool
     */
    public function placeOrder(int $type, int $pmethodId, int $addPay): bool
    {
        if ($addPay == AppConstant::YES) {
            $methodId = $pmethodId;
            $balance = User::getWalletBalance($this->userId);
            $remaining = $this->getNetAmount() - $balance;
            if ($remaining <= 0) {
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
            }
            $wallet = PaymentMethod::getByCode(WalletPay::KEY);
            $pmethodId = FatUtility::int($wallet['pmethod_id']);
        }
        $currency = MyUtility::getSystemCurrency();
        $orderData = [
            'order_type' => $type,
            'order_user_id' => $this->userId,
            'order_addedon' => date('Y-m-d H:i:s'),
            'order_total_amount' => $this->getTotal(),
            'order_net_amount' => $this->getNetAmount(),
            'order_item_count' => $this->getItemCount(),
            'order_payment_status' => AppConstant::UNPAID,
            'order_discount_value' => $this->getDiscount(),
            'order_pmethod_id' => FatUtility::int($pmethodId),
            'order_currency_code' => $currency['currency_code'],
            'order_currency_value' => $currency['currency_value'],
            'order_status' => static::STATUS_INPROCESS,
        ];
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        /* Insert Order Record */
        $this->assignValues($orderData);
        if (!$this->addNew(['HIGH_PRIORITY'])) {
            $db->rollbackTransaction();
            return false;
        }
        $this->orderId = $this->getMainTableRecordId();
        $this->mainTableRecordId = $this->orderId;
        if (!$this->insertCoupon()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->insertLessons()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->insertSubscriptions()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->insertClasses()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->insertPackages()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->insertCourses()) {
            $db->rollbackTransaction();
            return false;
        }
        if ($addPay && !$this->placeWalletOrder($remaining, $methodId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Place Wallet Order
     * 
     * @param float $amount
     * @param int $pmethodId
     * @return bool
     */
    public function placeWalletOrder(float $amount, int $pmethodId): bool
    {
        $currency = MyUtility::getSystemCurrency();
        $this->assignValues([
            'order_type' => static::TYPE_WALLET,
            'order_user_id' => $this->userId,
            'order_addedon' => date('Y-m-d H:i:s'),
            'order_item_count' => 1,
            'order_discount_value' => 0,
            'order_net_amount' => $amount,
            'order_total_amount' => $amount,
            'order_pmethod_id' => $pmethodId,
            'order_status' => static::STATUS_INPROCESS,
            'order_payment_status' => AppConstant::UNPAID,
            'order_currency_code' => $currency['currency_code'],
            'order_currency_value' => $currency['currency_value'],
            'order_related_order_id' => $this->getMainTableRecordId(),
        ]);
        return $this->addNew(['HIGH_PRIORITY']);
    }

    /**
     * Place recurring Subscription Order
     * 
     * @param float $amount
     * @param int $pmethodId
     * @return bool
     */
    public function recurringSubscription(array $subscrp): bool
    {

        $currency = MyUtility::getSystemCurrency();
        $this->assignValues([
            'order_type' => static::TYPE_SUBSCR,
            'order_user_id' => $this->userId,
            'order_addedon' => date('Y-m-d H:i:s'),
            'order_item_count' => $subscrp['order_item_count'],
            'order_net_amount' => $subscrp['order_net_amount'],
            'order_total_amount' => $subscrp['order_net_amount'],
            'order_pmethod_id' => $subscrp['order_pmethod_id'],
            'order_status' => static::STATUS_INPROCESS,
            'order_payment_status' => AppConstant::UNPAID,
            'order_currency_code' => $currency['currency_code'],
            'order_currency_value' => $currency['currency_value'],
        ]);
        if (!$this->addNew(['HIGH_PRIORITY'])) {
            return false;
        }
        $tabelRecord = new TableRecord(Subscription::DB_TBL);
        $tabelRecord->assignValues([
            'ordsub_order_id' => $this->getMainTableRecordId(),
            'ordsub_teacher_id' => $subscrp['ordsub_teacher_id'],
            'ordsub_startdate' => $subscrp['ordsub_startdate'],
            'ordsub_enddate' => $subscrp['ordsub_enddate'],
            'ordsub_recurring' => AppConstant::YES,
            'ordsub_status' => Subscription::ACTIVE,
            'ordsub_created' => date('Y-m-d H:i:s'),
            'ordsub_updated' => date('Y-m-d H:i:s')
        ]);
        if (!$tabelRecord->addNew(['HIGH_PRIORITY'])) {
            $this->error = $tabelRecord->getError();
            return false;
        }
        $data = [
            'ordles_order_id' => $this->getMainTableRecordId(),
            'ordles_updated' => date('Y-m-d H:i:s'),
        ];
        foreach ($subscrp['lessons'] as $record) {
            $lesson = new TableRecord(static::DB_TBL_LESSON);
            $lesson->assignValues(array_merge($record, $data));
            if (!$lesson->addNew(['HIGH_PRIORITY'])) {
                $this->error = $lesson->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Place GiftCard Order
     * 
     * @param array $data
     * @return bool
     */
    public function placeGiftcardOrder(array $data): bool
    {
        $amount = FatUtility::float($data['order_total_amount']);
        $minamount = FatApp::getConfig('MINIMUM_GIFT_CARD_AMOUNT');
        if ($amount < $minamount) {
            $minamount = MyUtility::formatMoney($minamount);
            $label = Label::getLabel("LBL_MINIMUM_GIFT_CARD_{minamount}");
            $this->error = str_replace("{minamount}", $minamount, $label);
            return false;
        }
        $pmethodId = $data['order_pmethod_id'];
        $addpay = FatUtility::int($data['add_and_pay']);
        if ($addpay == AppConstant::YES) {
            $balance = User::getWalletBalance($this->userId);
            $remaining = FatUtility::float($amount - $balance);
            if ($remaining <= 0) {
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
            }
            $methodId = $pmethodId;
            $wallet = PaymentMethod::getByCode(WalletPay::KEY);
            $pmethodId = FatUtility::int($wallet['pmethod_id']);
        }
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        $currency = MyUtility::getSystemCurrency();
        $this->assignValues([
            'order_type' => static::TYPE_GFTCRD,
            'order_user_id' => $this->userId,
            'order_addedon' => date('Y-m-d H:i:s'),
            'order_item_count' => 1,
            'order_discount_value' => 0,
            'order_net_amount' => $amount,
            'order_total_amount' => $amount,
            'order_payment_status' => AppConstant::UNPAID,
            'order_pmethod_id' => FatUtility::int($pmethodId),
            'order_currency_code' => $currency['currency_code'],
            'order_currency_value' => $currency['currency_value'],
            'order_status' => static::STATUS_INPROCESS,
        ]);
        if (!$this->addNew(['HIGH_PRIORITY'])) {
            $db->rollbackTransaction();
            $this->error = $db->getError();
            return false;
        }
        $cardData = [
            'ordgift_code' => uniqid(),
            'ordgift_expiry' => date('Y-m-d H:i:s', strtotime('+2 months')),
            'ordgift_status' => Giftcard::STATUS_UNUSED,
            'ordgift_order_id' => $this->getMainTableRecordId(),
            'ordgift_receiver_name' => $data['ordgift_receiver_name'],
            'ordgift_receiver_email' => $data['ordgift_receiver_email'],
        ];
        $receiver = User::getByEmail($data['ordgift_receiver_email']);
        if (!empty($receiver['user_email'])) {
            $cardData['ordgift_receiver_id'] = $receiver['user_id'];
        }
        $card = new Giftcard(0);
        $card->assignValues($cardData);
        if (!$card->addNew(['HIGH_PRIORITY'])) {
            $this->error = $card->getError();
            $db->rollbackTransaction();
            return false;
        }
        if ($addpay > 0 && !$this->placeWalletOrder($remaining, $methodId)) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Insert Coupon
     * 
     * @return bool
     */
    private function insertCoupon(): bool
    {
        $couponId = $this->coupon['coupon_id'] ?? 0;
        if ($couponId < 1) {
            return true;
        }
        $coupon = new Coupon($couponId);
        if (!$coupon->increaseUsedCount()) {
            $this->error = $coupon->getError();
            return false;
        }
        $coupon = new TableRecord(Coupon::DB_TBL_HISTORY);
        $coupon->assignValues([
            'couhis_coupon_id' => $couponId,
            'couhis_order_id' => $this->orderId,
            'couhis_created' => date('Y-m-d H:m:i'),
            'couhis_coupon' => json_encode($this->coupon),
        ]);
        if (!$coupon->addNew(['HIGH_PRIORITY'])) {
            $this->error = $coupon->getError();
            return false;
        }
        return true;
    }

    /**
     * Insert Lessons
     * 
     * @return bool
     */
    private function insertLessons(): bool
    {
        if (count($this->lessons) < 1) {
            return true;
        }
        $data = [
            'ordles_order_id' => $this->orderId,
            'ordles_discount' => $this->getLessonDiscount(),
            'ordles_updated' => date('Y-m-d H:i:s'),
        ];
        foreach ($this->lessons as $record) {
            $lesson = new TableRecord(static::DB_TBL_LESSON);
            $lesson->assignValues(array_merge($record, $data));
            if (!$lesson->addNew(['HIGH_PRIORITY'])) {
                $this->error = $lesson->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Insert Subscriptions
     * 
     * @return bool
     */
    private function insertSubscriptions(): bool
    {
        if (count($this->subscrp) < 1) {
            return true;
        }
        $subscription = new TableRecord(Subscription::DB_TBL);
        $subscription->assignValues([
            'ordsub_order_id' => $this->orderId,
            'ordsub_teacher_id' => $this->subscrp['ordsub_teacher_id'],
            'ordsub_startdate' => $this->subscrp['ordsub_startdate'],
            'ordsub_enddate' => $this->subscrp['ordsub_enddate'],
            'ordsub_recurring' => AppConstant::YES,
            'ordsub_status' => Subscription::ACTIVE,
            'ordsub_created' => date('Y-m-d H:i:s'),
            'ordsub_updated' => date('Y-m-d H:i:s')
        ]);
        if (!$subscription->addNew(['HIGH_PRIORITY'])) {
            $this->error = $subscription->getError();
            return false;
        }
        $data = [
            'ordles_order_id' => $this->orderId,
            'ordles_discount' => $this->getSubscriptionDiscount(),
            'ordles_updated' => date('Y-m-d H:i:s'),
        ];
        foreach ($this->subscrp['lessons'] as $record) {
            $lesson = new TableRecord(static::DB_TBL_LESSON);
            $lesson->assignValues(array_merge($record, $data));
            if (!$lesson->addNew(['HIGH_PRIORITY'])) {
                $this->error = $lesson->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Insert Classes
     * 
     * @return bool
     */
    private function insertClasses(): bool
    {
        if (count($this->classes) < 1) {
            return true;
        }
        $data = [
            'ordcls_order_id' => $this->orderId,
            'ordcls_discount' => $this->getClassDiscount(),
            'ordcls_updated' => date('Y-m-d H:i:s')
        ];
        foreach ($this->classes as $record) {
            $class = new TableRecord(static::DB_TBL_GCLASS);
            $class->assignValues(array_merge($record, $data));
            if (!$class->addNew(['HIGH_PRIORITY'])) {
                $this->error = $class->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Insert Packages
     * 
     * @return bool
     */
    private function insertPackages(): bool
    {
        if (count($this->packags) < 1) {
            return true;
        }
        $discount = FatUtility::float($this->getPackageDiscount() / count($this->packags));
        $data = ['ordpkg_order_id' => $this->orderId, 'ordpkg_discount' => $discount];
        foreach ($this->packags as $record) {
            $package = new TableRecord(static::DB_TBL_PACKAG);
            $package->assignValues(array_merge($record, $data));
            if (!$package->addNew(['HIGH_PRIORITY'])) {
                $this->error = $package->getError();
                return false;
            }
            $classCount = count($record['classes']);
            foreach ($record['classes'] as $row) {
                $classData = [
                    'ordcls_order_id' => $this->orderId,
                    'ordcls_type' => GroupClass::TYPE_PACKAGE,
                    'ordcls_grpcls_id' => $row['grpcls_id'],
                    'ordcls_commission' => $record['ordpkg_commission'],
                    'ordcls_amount' => $record['ordpkg_amount'] / $classCount,
                    'ordcls_discount' => $data['ordpkg_discount'] / $classCount,
                    'ordcls_status' => OrderClass::SCHEDULED,
                ];
                $class = new TableRecord(static::DB_TBL_GCLASS);
                $class->assignValues(array_merge($row, $classData));
                if (!$class->addNew(['HIGH_PRIORITY'])) {
                    $this->error = $class->getError();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Insert Courses
     * 
     * @return bool
     */
    private function insertCourses(): bool
    {
        if (count($this->courses) < 1) {
            return true;
        }
        $data = [
            'ordcrs_order_id' => $this->orderId,
            'ordcrs_discount' => $this->getCourseDiscount()
        ];
        foreach ($this->courses as $record) {
            $course = new TableRecord(static::DB_TBL_COURSE);
            $course->assignValues(array_merge($record, $data));
            if (!$course->addNew(['HIGH_PRIORITY'])) {
                $this->error = $course->getError();
                return false;
            }
            $progress = new CourseProgress();
            if (!$progress->setup($course->getId())) {
                $this->error = $progress->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * Apply Discount Coupon
     * 
     * @param array $coupon
     * @return bool
     */
    public function applyCoupon(array $coupon): bool
    {
        if (empty($coupon)) {
            return true;
        }
        $code = $coupon['coupon_code'] ?? '';
        $couponObj = new Coupon(0, MyUtility::getSiteLangId());
        if (!$row = $couponObj->validateCoupon($code, $this->getTotal(), $this->userId)) {
            $this->error = $couponObj->getError();
            return false;
        }
        $this->coupon = $row;
        return true;
    }

    /**
     * Get Coupon
     * 
     * @return type
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Get Discount
     * 
     * @return float
     */
    public function getDiscount(): float
    {
        return FatUtility::float($this->coupon['coupon_discount'] ?? 0);
    }

    /**
     * Get Lesson Discount
     * 
     * @return float
     */
    public function getLessonDiscount(): float
    {
        $discount = $this->getDiscount();
        if (count($this->lessons) > 0 && $discount > 0) {
            return FatUtility::float($discount / count($this->lessons));
        }
        return 0;
    }

    /**
     * Get Subscription Discount
     * 
     * @return float
     */
    public function getSubscriptionDiscount(): float
    {
        $discount = $this->getDiscount();
        if (count($this->subscrp) > 0 && $discount > 0) {
            return FatUtility::float($discount / count($this->subscrp['lessons']));
        }
        return 0;
    }

    /**
     * Get Class Discount

     * @return float
     */
    public function getClassDiscount(): float
    {
        $discount = $this->getDiscount();
        if (count($this->classes) > 0 && $discount > 0) {
            return FatUtility::float($discount / count($this->classes));
        }
        return 0;
    }

    /**
     * Get Package Discount

     * @return float
     */
    public function getPackageDiscount(): float
    {
        $discount = $this->getDiscount();
        if (count($this->packags) > 0 && $discount > 0) {
            return FatUtility::float($discount / count($this->packags));
        }
        return 0;
    }

    /**
     * Get Course Discount
     * 
     * @return float
     */
    public function getCourseDiscount(): float
    {
        $discount = $this->getDiscount();
        if (count($this->courses) > 0 && $discount > 0) {
            return FatUtility::float($discount / count($this->courses));
        }
        return 0;
    }

    /**
     * Get Total Amount
     * 
     * @return float
     */
    private function getTotal(): float
    {
        $lessonsAmount = array_sum(array_column($this->lessons, 'ordles_amount'));
        $subscrpAmount = $this->subscrp['total_amount'] ?? 0;
        $classesAmount = array_sum(array_column($this->classes, 'ordcls_amount'));
        $packagsAmount = array_sum(array_column($this->packags, 'ordpkg_amount'));
        $coursesAmount = array_sum(array_column($this->courses, 'ordcrs_amount'));
        return FatUtility::float($lessonsAmount + $subscrpAmount + $classesAmount + $packagsAmount + $coursesAmount);
    }

    /**
     * Get Net Amount
     * 
     * @return float
     */
    private function getNetAmount(): float
    {
        return FatUtility::float($this->getTotal() - $this->getDiscount());
    }

    /**
     * Get Item Count
     * 
     * @return int
     */
    private function getItemCount(): int
    {
        return array_sum([
            static::TYPE_LESSON => count($this->lessons),
            static::TYPE_SUBSCR => count($this->subscrp['lessons'] ?? []),
            static::TYPE_GCLASS => count($this->classes),
            static::TYPE_PACKGE => count($this->packags),
            static::TYPE_COURSE => count($this->courses)
        ]);
    }

    /**
     * Get Order Type Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getTypeArr(int $key = null)
    {
        $arr = [
            static::TYPE_LESSON => Label::getLabel('LBL_LESSON'),
            static::TYPE_SUBSCR => Label::getLabel('LBL_SUBSCRIPTIONS'),
            static::TYPE_GCLASS => Label::getLabel('LBL_GROUP_CLASSES'),
            static::TYPE_PACKGE => Label::getLabel('LBL_CLASS_PACKAGES'),
            static::TYPE_COURSE => Label::getLabel('LBL_COURSE_PURCHASED'),
            static::TYPE_WALLET => Label::getLabel('LBL_WALLET_RECHARGE'),
            static::TYPE_GFTCRD => Label::getLabel('LBL_GIFTCARD_PURCHASED'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Order Status Array
     *
     * @param int $key
     * @return string|array
     */
    public static function getStatusArr(int $key = null)
    {
        $arr = [
            static::STATUS_INPROCESS => Label::getLabel('LBL_INPROCESS'),
            static::STATUS_COMPLETED => Label::getLabel('LBL_COMPLETED'),
            static::STATUS_CANCELLED => Label::getLabel('LBL_CANCELLED'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Payment Status Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getPaymentArr(int $key = null)
    {
        $arr = [
            static::UNPAID => Label::getLabel('LBL_UNPAID'),
            static::ISPAID => Label::getLabel('LBL_IS_PAID'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Format Order id
     * 
     * @param int $orderId
     * @return string
     */
    public static function formatOrderId(int $orderId): string
    {
        return 'O' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get Order FInfo
     * 
     * @return type
     */
    public function getOrderInfo()
    {
        $src = new SearchBase(Order::DB_TBL);
        $src->addMultipleFields([
            'order_id', 'order_discount_value', 'order_payment_status', 'order_type', 'order_net_amount',
            'order_user_id', 'order_total_amount', 'order_related_order_id', 'order_item_count',
        ]);
        $src->addCondition('order_id', '=', $this->orderId);
        $src->doNotCalculateRecords();
        return FatApp::getDb()->fetch($src->getResultSet());
    }

    /**
     * Add Order Items
     * 
     * @param int $orderType
     * @param array $items
     * @return bool
     */
    public function addItems(int $orderType, array $items): bool
    {
        switch ($orderType) {
            case static::TYPE_LESSON:
                return $this->addLessons($items[Cart::LESSON]);
            case static::TYPE_SUBSCR:
                return $this->addSubscriptions($items[Cart::SUBSCR]);
            case static::TYPE_GCLASS:
                return $this->addClasses($items[Cart::GCLASS]);
            case static::TYPE_PACKGE:
                return $this->addPackages($items[Cart::PACKGE]);
            case static::TYPE_COURSE:
                return $this->addCourses($items[Cart::COURSE]);
            default:
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
        }
        return true;
    }

    /**
     * Get Valid Order to Pay
     * 
     * @return bool|array
     *
     */
    public function getOrderToPay()
    {
        $srch = new SearchBase(static::DB_TBL, 'orders');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'users.user_id=orders.order_user_id', 'users');
        $srch->joinTable(PaymentMethod::DB_TBL, 'LEFT JOIN', 'pmethod.pmethod_id=orders.order_pmethod_id', 'pmethod');
        $srch->addMultipleFields([
            'orders.order_id', 'orders.order_type', 'orders.order_user_id', 'orders.order_pmethod_id', 'orders.order_related_order_id',
            'orders.order_currency_code', 'orders.order_net_amount', 'orders.order_total_amount', 'orders.order_payment_status',
            'users.user_id', 'users.user_first_name', 'users.user_last_name', 'users.user_email', 'pmethod.pmethod_code',
            'CONCAT(users.user_first_name, " ", users.user_last_name) AS user_name', 'users.user_country_id', 'users.user_lang_id'
        ]);
        $srch->addDirectCondition('users.user_deleted IS NULL');
        $srch->addDirectCondition('users.user_verified IS NOT NULL');
        $srch->addCondition('orders.order_id', '=', $this->orderId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $order = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($order)) {
            $this->error = Label::getLabel('LBL_ORDER_NOT_FOUND');
            return false;
        }
        return $order;
    }

    /**
     * Cancel Order
     * 
     * @return boolean
     */
    public function cancelOrder()
    {
        $src = new SearchBase(Order::DB_TBL, 'orders');
        $src->joinTable(Coupon::DB_TBL_HISTORY, 'LEFT JOIN', 'orders.order_id = couhis.couhis_order_id', 'couhis');
        $src->joinTable(User::DB_TBL, 'INNER JOIN', 'user.user_id = orders.order_user_id', 'user');
        $src->addMultipleFields([
            'order_status', 'order_id', 'order_status',
            'order_user_id', 'order_type', 'couhis_id', 'couhis_coupon_id',
            'user.user_first_name', 'user.user_last_name', 'user_email', 'user_lang_id'
        ]);
        $src->addCondition('order_id', '=', $this->orderId);
        $src->addCondition('order_payment_status', '=', Order::UNPAID);
        $src->addCondition('order_status', '!=', Order::STATUS_CANCELLED);
        $src->doNotCalculateRecords();
        $src->setPageSize(1);
        $data = FatApp::getDb()->fetch($src->getResultSet());
        if (empty($data)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        return $this->cancelUnpaidOrder($data);
    }

    /**
     * Get Child Order Details 
     * 
     * @param integer $orderType
     * @param integer $langId
     * @return null|array
     */
    public function getSubOrders(int $orderType, int $langId)
    {
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->addCondition('order_type', '=', $orderType);
        $srch->addCondition('order_id', '=', $this->orderId);
        switch ($orderType) {
            case Order::TYPE_LESSON:
                $srch->joinTable(Lesson::DB_TBL, 'INNER JOIN', 'ordles.ordles_order_id = orders.order_id', 'ordles');
                $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordles.ordles_teacher_id', 'teacher');
                $srch->joinTable(TeachLanguage::DB_TBL, 'LEFT JOIN', 'tlang.tlang_id = ordles.ordles_tlang_id', 'tlang');
                $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
                $srch->addMultipleFields([
                    'teacher.user_timezone', 'teacher.user_country_id', 'teacher.user_first_name', 'teacher.user_last_name', 'teacher.user_email',
                    'ordles_id', 'ordles_order_id', 'ordles_type', 'ordles_teacher_id', 'ordles_amount', 'ordles_tlang_id', 'order_item_count', 'ordles_lesson_starttime', 'ordles_lesson_endtime',
                    'ordles_status', 'ordles_duration', 'ordles_commission', 'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name',
                ]);
                break;
            case Order::TYPE_SUBSCR:
                $srch->joinTable(Subscription::DB_TBL, 'INNER JOIN', 'ordsub.ordsub_order_id = orders.order_id', 'ordsub');
                $srch->joinTable(Lesson::DB_TBL, 'INNER JOIN', 'ordles.ordles_order_id = orders.order_id', 'ordles');
                $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = ordles.ordles_teacher_id', 'teacher');
                $srch->joinTable(TeachLanguage::DB_TBL, 'INNER JOIN', 'tlang.tlang_id = ordles.ordles_tlang_id', 'tlang');
                $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
                $srch->addMultipleFields([
                    'teacher.user_timezone', 'teacher.user_country_id', 'teacher.user_first_name', 'teacher.user_last_name', 'teacher.user_email',
                    'ordles_id', 'ordles_order_id', 'ordles_type', 'ordles_teacher_id', 'ordles_amount', 'ordles_tlang_id', 'order_item_count', 'ordles_lesson_starttime', 'ordles_lesson_endtime',
                    'ordles_status', 'ordles_duration', 'ordles_commission', 'teacher.user_timezone', 'ordsub_startdate', 'ordsub_enddate', 'ordsub_status', 'ordsub_created',
                    'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name',
                ]);
                break;
            case Order::TYPE_GCLASS:
                $srch->joinTable(OrderClass::DB_TBL, 'INNER JOIN', 'ordcls.ordcls_order_id = orders.order_id', 'ordcls');
                $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id = ordcls.ordcls_grpcls_id', 'grpcls');
                $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
                $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id', 'gclang');
                $srch->joinTable(TeachLanguage::DB_TBL, 'LEFT JOIN', 'tlang.tlang_id = grpcls.grpcls_tlang_id', 'tlang');
                $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
                $srch->addCondition('ordcls_type', '=', GroupClass::TYPE_REGULAR);
                $srch->addGroupBy('ordcls_id');
                $srch->addMultipleFields([
                    'teacher.user_timezone', 'teacher.user_country_id', 'teacher.user_first_name', 'teacher.user_last_name', 'teacher.user_email',
                    'ordcls_id', 'ordcls_status', 'ordcls_order_id', 'grpcls_teacher_id', 'ordcls_amount', 'grpcls_tlang_id', 'grpcls_total_seats',
                    'grpcls_start_datetime', 'grpcls_end_datetime', 'ordcls_commission', 'grpcls_duration', 'ordcls_amount',
                    'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name', 'IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title',
                ]);
                break;
            case Order::TYPE_PACKGE:
                $srch->joinTable(OrderPackage::DB_TBL, 'INNER JOIN', 'ordpkg.ordpkg_order_id = orders.order_id', 'ordpkg');
                $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'package.grpcls_id = ordpkg.ordpkg_package_id', 'package');
                $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'packlang.gclang_grpcls_id = package.grpcls_id', 'packlang');
                $srch->joinTable(OrderClass::DB_TBL, 'INNER JOIN', 'ordcls.ordcls_order_id = orders.order_id', 'ordcls');
                $srch->joinTable(GroupClass::DB_TBL, 'INNER JOIN', 'grpcls.grpcls_id = ordcls.ordcls_grpcls_id', 'grpcls');
                $srch->joinTable(GroupClass::DB_TBL_LANG, 'LEFT JOIN', 'gclang.gclang_grpcls_id = grpcls.grpcls_id', 'gclang');
                $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'teacher.user_id = grpcls.grpcls_teacher_id', 'teacher');
                $srch->joinTable(TeachLanguage::DB_TBL, 'LEFT JOIN', 'tlang.tlang_id = grpcls.grpcls_tlang_id', 'tlang');
                $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
                $srch->addCondition('ordcls_type', '=', GroupClass::TYPE_PACKAGE);
                $srch->addGroupBy('ordcls_id');
                $srch->addMultipleFields([
                    'teacher.user_timezone', 'teacher.user_country_id', 'teacher.user_first_name', 'teacher.user_last_name', 'teacher.user_email',
                    'ordcls_id', 'ordcls_status', 'ordcls_order_id', 'grpcls.grpcls_teacher_id', 'ordcls_amount', 'grpcls.grpcls_tlang_id', 'grpcls.grpcls_total_seats',
                    'grpcls.grpcls_start_datetime', 'grpcls.grpcls_end_datetime', 'ordcls_commission', 'grpcls.grpcls_duration', 'ordcls_amount', 'ordpkg_commission', 'ordpkg_amount',
                    'ordpkg_discount', 'IFNULL(packlang.grpcls_title, package.grpcls_title) as package_title', 'package.grpcls_start_datetime as package_start',
                    'package.grpcls_end_datetime as package_end',
                    'IFNULL(tlanglang.tlang_name, tlang.tlang_identifier) as tlang_name',
                    'IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title',
                ]);
                break;
            case Order::TYPE_COURSE:
                $srch->joinTable(OrderCourse::DB_TBL, 'LEFT JOIN', 'orders.order_type = ' . Order::TYPE_COURSE . ' AND orders.order_id = ordcrs.ordcrs_order_id', 'ordcrs');
                $srch->joinTable(Course::DB_TBL, 'LEFT JOIN', 'course.course_id = ordcrs.ordcrs_course_id', 'course');
                $srch->joinTable(Course::DB_TBL_LANG, 'LEFT JOIN', 'course.course_id = crsdetail.course_id', 'crsdetail');
                $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'course.course_user_id = teacher.user_id', 'teacher');
                $srch->joinTable(CourseLanguage::DB_TBL, 'INNER JOIN', 'clang.clang_id = course.course_clang_id', 'clang');
                $srch->joinTable(
                    CourseLanguage::DB_TBL_LANG,
                    'LEFT JOIN',
                    'clang.clang_id = clanglang.clanglang_clang_id AND clanglang.clanglang_lang_id = ' . $langId,
                    'clanglang'
                );
                $srch->addMultipleFields([
                    'crsdetail.course_title',
                    'teacher.user_first_name',
                    'teacher.user_last_name',
                    'teacher.user_timezone',
                    'teacher.user_country_id',
                    'IFNULL(clanglang.clang_name, clang.clang_identifier) AS clang_name',
                    'course.course_duration',
                    'ordcrs.ordcrs_amount',
                    'teacher.user_email',
                    'ordcrs.ordcrs_status',
                    'ordcrs.ordcrs_commission'
                ]);
                break;
            case Order::TYPE_WALLET:
                break;
            case Order::TYPE_GFTCRD:
                $srch->addMultipleFields(['ordgift.*']);
                $srch->joinTable(Giftcard::DB_TBL, 'INNER JOIN', 'ordgift.ordgift_order_id = orders.order_id', 'ordgift');
                break;
        }
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Cancel Unpaid Order
     * 
     * @param array $order
     * @param bool $notify
     * @return bool
     * 
     */
    public function cancelUnpaidOrder(array $order, bool $notify = false): bool
    {
        $db = FatApp::getDb();
        $db->startTransaction();
        $record = new TableRecord(static::DB_TBL);
        $record->assignValues(['order_status' => Order::STATUS_CANCELLED]);
        if (!$record->update(['smt' => 'order_id = ?', 'vals' => [$order['order_id']]])) {
            $db->rollbackTransaction();
            $this->error = $record->getError();
            return false;
        }
        if (!empty($order['couhis_id'])) {
            $coupon = new Coupon($order['couhis_coupon_id']);
            if (!$coupon->decreaseUsedCount($order['couhis_id'])) {
                $db->rollbackTransaction();
                $this->error = $coupon->getError();
                return false;
            }
        }
        switch ($order['order_type']) {
            case Order::TYPE_LESSON:
                $table = Lesson::DB_TBL;
                $updateArray = ['ordles_status' => Lesson::CANCELLED];
                $whereArray = ['smt' => 'ordles_order_id = ?', 'vals' => [$order['order_id']]];
                break;
            case Order::TYPE_SUBSCR:
                if (!$db->updateFromArray(Lesson::DB_TBL, ['ordles_status' => Lesson::CANCELLED],
                                ['smt' => 'ordles_order_id = ?', 'vals' => [$order['order_id']]])) {
                    $db->rollbackTransaction();
                    $this->error = $db->getError();
                    return false;
                }
                $table = Subscription::DB_TBL;
                $updateArray = ['ordsub_status' => Subscription::CANCELLED];
                $whereArray = ['smt' => 'ordsub_order_id = ?', 'vals' => [$order['order_id']]];
                break;
            case Order::TYPE_GCLASS:
                $table = OrderClass::DB_TBL;
                $updateArray = ['ordcls_status' => OrderClass::CANCELLED];
                $whereArray = ['smt' => 'ordcls_order_id = ?', 'vals' => [$order['order_id']]];
                break;
            case Order::TYPE_PACKGE:
                if (!$db->updateFromArray(OrderClass::DB_TBL, ['ordcls_status' => OrderClass::CANCELLED],
                                ['smt' => 'ordcls_order_id = ?', 'vals' => [$order['order_id']]])) {
                    $db->rollbackTransaction();
                    $this->error = $db->getError();
                    return false;
                }
                $table = OrderPackage::DB_TBL;
                $updateArray = ['ordpkg_status' => OrderPackage::CANCELLED];
                $whereArray = ['smt' => 'ordpkg_order_id = ?', 'vals' => [$order['order_id']]];
                break;
            case Order::TYPE_GFTCRD:
                $table = Giftcard::DB_TBL;
                $updateArray = ['ordgift_status' => Giftcard::STATUS_CANCELLED];
                $whereArray = ['smt' => 'ordgift_order_id = ?', 'vals' => [$order['order_id']]];
                break;
            case Order::TYPE_COURSE:
                $table = OrderCourse::DB_TBL;
                $updateArray = ['ordcrs_status' => OrderCourse::CANCELLED];
                $whereArray = ['smt' => 'ordcrs_order_id = ?', 'vals' => [$order['order_id']]];
                break;
        }
        if (!empty($table) && !$db->updateFromArray($table, $updateArray, $whereArray)) {
            $db->rollbackTransaction();
            $this->error = $db->getError();
            return false;
        }
        if ($notify == false) {
            $db->commitTransaction();
            return true;
        }
        $notifi = new Notification($order['order_user_id'], Notification::TYPE_ORDER_CANCELLED);
        $notifi->sendNotification(['{orderid}' => Order::formatOrderId($order['order_id'])]);
        $db->commitTransaction();
        $mail = new FatMailer($order['user_lang_id'], 'order_cancelled_by_admin');
        $vars = [
            '{order_id}' => Order::formatOrderId($order['order_id']),
            '{user_name}' => $order['user_first_name'] . ' ' . $order['user_last_name'],
            '{link}' => MyUtility::makeFullUrl('orders', '', [], CONF_WEBROOT_DASHBOARD),
        ];
        $mail->setVariables($vars);
        $mail->sendMail([$order['user_email']]);
        return true;
    }
    
/**
 * Lenient lesson pricing (fallback)
 * Allows order placement even if teacher profile flags are incomplete.
 * Used ONLY when strict Lesson::getLessonPrice() fails.
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
    $srch->joinTable(UserTeachLanguage::DB_TBL, 'INNER JOIN', 'utlang.utlang_user_id = teacher.user_id', 'utlang');
    $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'ustelgpr.ustelgpr_utlang_id = utlang.utlang_id', 'ustelgpr');

    $srch->addCondition('teacher.user_id', '=', $teacherId);
    $srch->addDirectCondition('teacher.user_deleted IS NULL');
    $srch->addCondition('teacher.user_active', '=', AppConstant::ACTIVE);
    $srch->addCondition('teacher.user_is_teacher', '=', AppConstant::YES);

    // keep consistent with your platform filters (optional but recommended)
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

    // normalize fields expected by OfferPrice::applyLessonOffer + order pipeline
    $lesson['ordles_teacher_id'] = $teacherId;
    $lesson['ordles_tlang_id']   = FatUtility::int($row['ordles_tlang_id'] ?? $tlangId);
    $lesson['ordles_duration']   = $duration;
    $lesson['ordles_type']       = $type;
    $lesson['ordles_amount']     = FatUtility::float($row['ordles_amount']);

    return $lesson;
}

}
