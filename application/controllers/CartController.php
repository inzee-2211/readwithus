<?php

/**
 * Cart Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class CartController extends LoggedUserController
{

    /**
     * Initialize Cart
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Language and Duration Slots
     */
    public function langSlots()
    {
        $teacherId = FatApp::getPostedData('ordles_teacher_id', FatUtility::VAR_INT, 0);
        $duration = FatApp::getPostedData('ordles_duration', FatUtility::VAR_INT, 0);
        $tlangId = FatApp::getPostedData('ordles_tlang_id', FatUtility::VAR_INT, 0);
        if ($teacherId < 1 || $teacherId == $this->siteUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $user = new User($teacherId);
        if (!$teacher = $user->validateTeacher($this->siteLangId, $this->siteUserId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $utl = new UserTeachLanguage($teacherId);
        $langslots = $utl->getLangSlots($this->siteLangId);
        $tlangs = array_keys($langslots);
        $tlangId = (!in_array($tlangId, $tlangs)) ? current($tlangs) : $tlangId;
        $slots = $langslots[$tlangId]['slots'] ?? [];
        $duration = (!in_array($duration, $slots)) ? current($slots) : $duration;
        $this->sets([
            'teacher' => $teacher, 'langslots' => $langslots,
            'tlangId' => $tlangId, 'duration' => $duration,
            'stepCompleted' => [], 'stepProcessing' => [1],
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Price Slabs and lesson Quantity 
     */
    public function priceSlabs()
    {
        $teacherId = FatApp::getPostedData('ordles_teacher_id', FatUtility::VAR_INT, 0);
        $tlangId = FatApp::getPostedData('ordles_tlang_id', FatUtility::VAR_INT, 0);
        $duration = FatApp::getPostedData('ordles_duration', FatUtility::VAR_INT, 0);
        $quantity = FatApp::getPostedData('ordles_quantity', FatUtility::VAR_INT, 0);
        $ordlesType = FatApp::getPostedData('ordles_type', FatUtility::VAR_INT, 0);
        $ordlesType = ($ordlesType < 1) ? Lesson::TYPE_REGULAR : $ordlesType;
        if ($teacherId < 1 || $tlangId < 1 || $duration < 1 || $teacherId == $this->siteUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $user = new User($teacherId);
        if (!$teacher = $user->validateTeacher($this->siteLangId, $this->siteUserId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $utl = new UserTeachLanguage($teacherId);
        if (!$slabs = $utl->getPriceSlabs($this->siteLangId, $tlangId, $duration)) {
            FatUtility::dieJsonError($utl->getError());
        }
        $minValue = min(array_column($slabs, 'ustelgpr_min_slab', 'ustelgpr_min_slab'));
        $maxValue = max(array_column($slabs, 'ustelgpr_max_slab', 'ustelgpr_max_slab'));
        $quantity = empty($quantity) ? $minValue : $quantity;
        $discount = 0;
        $offer = OfferPrice::getLessonOffer($this->siteUserId, $teacherId);
        if (!empty($offer['offpri_lesson_price'])) {
            $offers = json_decode($offer['offpri_lesson_price'], 1);
            $offers = array_column($offers, 'offer', 'duration');
            $discount = Fatutility::float(($offers[$duration] ?? 0));
        }
        $this->sets([
            'subWeek' => FatApp::getConfig('CONF_RECURRING_SUBSCRIPTION_WEEKS'),
            'teacher' => $teacher, 'tlangId' => $tlangId, 'duration' => $duration,
            'quantity' => $quantity, 'discount' => $discount, 'slabs' => $slabs,
            'offer' => $offer, 'minValue' => $minValue, 'maxValue' => $maxValue,
            'ordlesType' => $ordlesType, 'tlangName' => $slabs[key($slabs)]['tlang_name'],
            'postedData' => FatApp::getPostedData(), 'stepCompleted' => [1], 'stepProcessing' => [2],
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Render Booking Calendar for selecting slots
     */
    public function viewCalendar()
    {
        $teacherId = FatApp::getPostedData('ordles_teacher_id', FatUtility::VAR_INT, 0);
        $tlangId = FatApp::getPostedData('ordles_tlang_id', FatUtility::VAR_INT, 0);
        $duration = FatApp::getPostedData('ordles_duration', FatUtility::VAR_INT, 0);
        $quantity = FatApp::getPostedData('ordles_quantity', FatUtility::VAR_INT, 0);
        $ordlesType = FatApp::getPostedData('ordles_type', FatUtility::VAR_INT, 0);
        if (
                $teacherId < 1 || $tlangId < 1 || $duration < 1 || $quantity < 1 ||
                $ordlesType < 1 || $teacherId == $this->siteUserId
        ) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $user = new User($teacherId);
        if (!$teacher = $user->validateTeacher($this->siteLangId, $this->siteUserId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $tlangName = TeachLanguage::getAttributesByLangId($this->siteLangId, $tlangId, 'tlang_name');
        $calendarDays = 490; // 70 weeks
        if ($ordlesType == Lesson::TYPE_SUBCRIP) {
            $calendarDays = (FatApp::getConfig('CONF_RECURRING_SUBSCRIPTION_WEEKS') * 7 ) + 1;
        }
        $this->sets([
            'teacher' => $teacher, 'tlangId' => $tlangId,
            'duration' => $duration, 'quantity' => $quantity,
            'ordlesType' => $ordlesType,
            'tlangName' => $tlangName,
            'calendarDays' => $calendarDays,
            'nowDate' => MyDate::formatDate(date('Y-m-d H:i:s')),
            'nowDate' => MyDate::formatDate(date('Y-m-d H:i:s')),
            'stepCompleted' => [1, 2], 'stepProcessing' => [3]
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Add Lesson(s) to Cart
     */
    public function addLesson()
    {
        $quantity = FatApp::getPostedData('ordles_quantity', FatUtility::VAR_INT, 0);
        $post = FatApp::getPostedData();
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $frm = $cart->getLessonForm($quantity);
        if (!$post = $frm->getFormDataFromArray($post)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!empty($post['ordles_starttime']) && !empty($post['ordles_endtime'])) {
            $post['ordles_starttime'] = MyDate::formatToSystemTimezone($post['ordles_starttime']);
            $post['ordles_endtime'] = MyDate::formatToSystemTimezone($post['ordles_endtime']);
        }
        if ($post['ordles_type'] == Lesson::TYPE_REGULAR) {
            $post['lessons'] = $this->formatLessonData($post);
            unset($post['startTime'], $post['endTime']);
        }
        if (!$cart->addLesson($post)) {
            FatUtility::dieJsonError($cart->getError());
        }
        if ($post['ordles_type'] == Lesson::TYPE_FTRAIL) {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_ITEM_ADDED_SUCCESSFULLY'));
        }
        $this->set('post', $post);
        $this->paymentSummary(Order::TYPE_LESSON);
    }

    /**
     * Add Subscription to cart
     */
    public function addSubscription()
    {
        $quantity = FatApp::getPostedData('ordles_quantity', FatUtility::VAR_INT, 0);
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $frm = $cart->getSubscriptionForm($quantity);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['lessons'] = $this->formatLessonData($post);
        unset($post['startTime'], $post['endTime']);
        if (!$cart->addSubscription($post)) {
            FatUtility::dieJsonError($cart->getError());
        }
        $this->set('post', $post);
        $this->paymentSummary(Order::TYPE_SUBSCR);
    }

    /**
     * Add Class to cart
     */
    public function addClass()
    {
        $grpclsId = FatApp::getPostedData('grpcls_id', FatUtility::VAR_INT, 0);
        if (empty($grpclsId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if (!$cart->addClass($grpclsId)) {
            FatUtility::dieJsonError($cart->getError());
        }
        $this->set('post', ['grpcls_id' => $grpclsId]);
        $this->paymentSummary(Order::TYPE_GCLASS);
    }

    /**
     * Add Package to cart
     */
    public function addPackage()
    {
        $packageId = FatApp::getPostedData('packageId', FatUtility::VAR_INT, 0);
        if (empty($packageId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if (!$cart->addPackage($packageId)) {
            FatUtility::dieJsonError($cart->getError());
        }
        $this->set('post', ['package_id' => $packageId]);
        $this->paymentSummary(Order::TYPE_PACKGE);
    }

    /**
     * Add Course to cart
     */
    public function addCourse()
    {
        $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
        if (empty($courseId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* check course already booked */
        $course = CourseSearch::getPurchasedCourses($this->siteUserId, [$courseId]);
        if (!empty($course)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_HAVE_ALREADY_PURCHASED_THIS_COURSE'));
        }
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if (!$cart->addCourse($courseId)) {
            FatUtility::dieJsonError($cart->getError());
        }
        $this->set('post', ['course_id' => $courseId]);
        $this->paymentSummary(Order::TYPE_COURSE);
    }

    /**
     * Apply Coupon
     */
    public function applyCoupon()
    {
        $code = FatApp::getPostedData('coupon_code', FatUtility::VAR_STRING, '');
        $orderType = FatApp::getPostedData('order_type', FatUtility::VAR_INT, 0);
        if (empty($code) || empty($orderType)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if (!$cart->applyCoupon($code)) {
            FatUtility::dieJsonError($cart->getError());
        }
        $this->paymentSummary($orderType);
    }

    /**
     * Remove Coupon
     */
    public function removeCoupon()
    {
        $orderType = FatApp::getPostedData('order_type', FatUtility::VAR_INT, 0);
        if (empty($orderType)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if (!$cart->removeCoupon()) {
            FatUtility::dieJsonError(Label::getLabel("LBL_INVALID_ACTION"));
        }
        $this->paymentSummary($orderType);
    }

    /**
     * Render Payment Summary
     * 
     * @param int $orderType
     */
    public function paymentSummary(int $orderType)
    {
        $addAndPay = FatApp::getPostedData('add_and_pay', FatUtility::VAR_INT, 0);
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if (!$cartItems = $cart->getItems()) {
            FatUtility::dieJsonError($cart->getError());
        }
        $couponCode = $cart->getCoupon()['coupon_code'] ?? '';
        $checkoutFormData = ['order_type' => $orderType, 'coupon_code' => $couponCode];
        $pmethodId = FatApp::getPostedData('order_pmethod_id', FatUtility::VAR_STRING, '');
        if (!empty($pmethodId)) {
            $checkoutFormData['order_pmethod_id'] = $pmethodId;
        }
        $checkoutForm = $cart->getCheckoutForm();
        $checkoutForm->fill($checkoutFormData);
        $coupon = new Coupon(0, $this->siteLangId);
        $this->sets([
            'addAndPay' => $addAndPay,
            'cartItems' => $cartItems,
            'checkoutForm' => $checkoutForm,
            'cartTotal' => $cart->getTotal(),
            'cartDiscount' => $cart->getDiscount(),
            'appliedCoupon' => $cart->getCoupon(),
            'availableCoupons' => $coupon->getCouponList(),
            'currencyData' => MyUtility::getSystemCurrency(),
            'walletBalance' => User::getWalletBalance($this->siteUserId),
            'walletPayId' => PaymentMethod::getByCode(WalletPay::KEY)['pmethod_id'],
            'stepCompleted' => [1, 2, 3], 'stepProcessing' => [4],
        ]);
        $this->_template->render(false, false, 'cart/payment-summary.php');
    }

    /**
     * Confirm Order to place
     */
    public function confirmOrder()
    {
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        if ($cart->getCount() < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_CART_IS_EMPTY'));
        }
        $cartNetAmount = $cart->getTotal() - $cart->getDiscount();
        $frm = $cart->getCheckoutForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $order = new Order(0, $this->siteUserId);
        if (!$order->addItems($post['order_type'], $cart->getItems())) {
            FatUtility::dieJsonError($order->getError());
        }
        if (!$order->applyCoupon($cart->getCoupon())) {
            FatUtility::dieJsonError($order->getError());
        }
        if (!$order->placeOrder($post['order_type'], $post['order_pmethod_id'], $post['add_and_pay'])) {
            FatUtility::dieJsonError($order->getError());
        }
        $orderId = $order->getMainTableRecordId();
        if ($cartNetAmount == 0) {
            $payment = new OrderPayment($orderId);
            if (!$payment->paymentSettlements('NA', 0, [])) {
                FatUtility::dieJsonError($payment->getError());
            }
            FatUtility::dieJsonSuccess(['redirectUrl' => MyUtility::makeUrl('Payment', 'success', [$orderId])]);
        }
        $redirectUrl = MyUtility::makeUrl('Payment', 'charge', [$orderId], CONF_WEBROOT_FRONTEND);
        FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => Label::getLabel('LBL_PROCESSING')]);
    }

    /**
     * Render Trail Calendar
     */
    public function trailCalendar()
    {
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        if (FatApp::getConfig('CONF_ENABLE_FREE_TRIAL', FatUtility::VAR_INT, 0) != 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $user = new User($teacherId);
        if (!$teacher = $user->validateTeacher($this->siteLangId, $this->siteUserId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($teacher['user_trial_enabled'] == AppConstant::NO) {
            FatUtility::dieJsonError(Label::getLabel('LBL_FREE_TRIAL_IS_DISABLED_BY_TEACHER'));
        }
        if (Lesson::isTrailAvailed($this->siteUserId, $teacher['user_id'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_ALLREADY_AVAILED_FREE_TRIAL_LESSON'));
        }
        $teacher['user_country_code'] = Country::getAttributesById($teacher['user_country_id'], 'country_code');
        $duration = FatApp::getConfig('CONF_TRIAL_LESSON_DURATION');
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $form = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $form->fill(['order_type' => Order::TYPE_LESSON]);
        $this->set('form', $form);
        $this->sets(['teacher' => $teacher, 'duration' => $duration]);
        $this->_template->render(false, false);
    }

    /**
     * Format Lessons Data
     * 
     * @param array $data
     * @return array
     */
    private function formatLessonData(array $data): array
    {
        $lessonData = [];
        foreach ($data['startTime'] as $key => $value) {
            $lesson = ['ordles_starttime' => null, 'ordles_endtime' => null];
            if (!empty($value) && !empty($data['endTime'][$key])) {
                $lesson['ordles_starttime'] = MyDate::formatToSystemTimezone($value);
                $lesson['ordles_endtime'] = MyDate::formatToSystemTimezone($data['endTime'][$key]);
            }
            array_push($lessonData, $lesson);
        }
        return $lessonData;
    }

}
