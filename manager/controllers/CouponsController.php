<?php

/**
 * Coupons Controller is used for Coupons handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class CouponsController extends AdminBaseController
{

    /**
     * Initialize Coupon
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewDiscountCoupons();
    }

    /**
     * Render Search Form
     */
    public function index()
    {
        $this->set("frmSearch", $this->getSearchForm());
        $this->set("canEdit", $this->objPrivilege->canEditDiscountCoupons(true));
        $this->_template->render();
    }

    /**
     * Search & List Coupons
     */
    public function search()
    {
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new SearchBase(Coupon::DB_TBL, 'coupon');
        $srch->joinTable(Coupon::DB_TBL_LANG, 'LEFT JOIN', 'couponlang.couponlang_coupon_id = coupon.coupon_id AND couponlang.couponlang_lang_id = ' . $this->siteLangId, 'couponlang');
        $srch->addMultipleFields(['coupon_id', 'coupon_code', 'coupon_active', 'coupon_discount_type', 'coupon_discount_value', 'coupon_start_date', 'coupon_end_date', 'IFNULL(couponlang.coupon_title, coupon_identifier) as coupon_title']);
        if (!empty($post['keyword'])) {
            $cnd = $srch->addCondition('coupon.coupon_code', 'like', '%' . $post['keyword'] . '%');
            $cnd->attachCondition('couponlang.coupon_title', 'like', '%' . $post['keyword'] . '%');
        }
        if ($post['coupon_active'] != '') {
            $srch->addCondition('coupon.coupon_active', '=', $post['coupon_active']);
        }
        if ($post['coupon_expire'] == AppConstant::YES) {
            $srch->addCondition('coupon.coupon_end_date', '<', date('Y-m-d H:i:s'));
        } elseif ($post['coupon_expire'] != '' && $post['coupon_expire'] == AppConstant::NO) {
            $srch->addCondition('coupon.coupon_end_date', '>', date('Y-m-d H:i:s'));
        }

        $srch->addOrder('coupon_id', 'DESC');
        $srch->setPageNumber($post['pageno']);
        $srch->setPageSize($post['pagesize']);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('postedData', $post);
        $this->set("arr_listing", $records);
        $this->set('page', $post['pageno']);
        $this->set('pageSize', $post['pagesize']);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set("canEdit", $this->objPrivilege->canEditDiscountCoupons(true));
        $this->_template->render(false, false);
    }

    /**
     * Render Coupon Form
     * 
     * @param int $couponId
     */
    public function form($couponId)
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $couponId = FatUtility::int($couponId);
        $data = ['coupon_id' => $couponId];
        $frm = $this->getForm();
        if (0 < $couponId) {
            $data = Coupon::getAttributesById($couponId);
            if (empty($data)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $data['coupon_start_date'] = MyDate::formatDate($data['coupon_start_date']);
            $data['coupon_end_date'] = MyDate::formatDate($data['coupon_end_date']);
        }
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('couponId', $couponId);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    /**
     * Setup Coupon
     */
    public function setup()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $frm = $this->getForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['coupon_start_date'] = MyDate::formatToSystemTimezone($post['coupon_start_date']);
        $post['coupon_end_date'] = MyDate::formatToSystemTimezone($post['coupon_end_date'] . ' 23:59:59');
        $couponId = FatUtility::int($post['coupon_id']);
        $coupon = new Coupon($couponId);
        $coupon->assignValues($post);
        if (!$coupon->save()) {
            FatUtility::dieJsonError($coupon->getError());
        }
        $newTabLangId = 0;
        if ($couponId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Coupon::getAttributesByLangId($langId, $couponId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $couponId = $coupon->getMainTableRecordId();
            $newTabLangId = $this->siteLangId;
        }
        $data = [
            'couponId' => $couponId, 'langId' => $newTabLangId,
            'msg' => Label::getLabel('MSG_Coupon_Setup_Successful.'),
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Render Coupon Lang Form
     * 
     * @param int $couponId
     * @param int $langId
     */
    public function langForm($couponId = 0, $langId = 0)
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $couponId = FatUtility::int($couponId);
        $langId = FatUtility::int($langId);
        if ($couponId == 0 || $langId == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($couponId, $langId);
        $langData = Coupon::getAttributesByLangId($langId, $couponId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('couponId', $couponId);
        $this->set('coupon_lang_id', $langId);
        $this->set('langFrm', $langFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('formLayout', Language::getLayoutDirection($langId));
        $this->_template->render(false, false);
    }

    /**
     * Setup Coupon Lang Data
     */
    public function langSetup()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $langId = FatApp::getPostedData('couponlang_lang_id', FatUtility::VAR_INT, 0);
        $couponId = FatApp::getPostedData('couponlang_coupon_id', FatUtility::VAR_INT, 0);
        if ($couponId == 0 || $langId == 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($couponId, $langId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $coupon = new Coupon($couponId);
        if (!$coupon->updateLangData($langId, $post)) {
            FatUtility::dieJsonError($coupon->getError());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Coupon::getAttributesByLangId($langId, $couponId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $data = [
            'msg' => Label::getLabel('MSG_SETUP_SUCCESSFUL'),
            'couponId' => $couponId, 'langId' => $newTabLangId
        ];
        FatUtility::dieJsonSuccess($data);
    }

    /**
     * Remove Coupon
     */
    public function remove()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $couponId = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        $coupon = Coupon::getAttributesById($couponId);
        if (empty($coupon)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $couponObj = new Coupon($couponId);
        if (!$couponObj->deleteRecord(false)) {
            FatUtility::dieJsonError($couponObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /**
     * Get Coupon Uses History
     * 
     * @param int $couponId
     */
    public function uses($couponId)
    {
        $couponId = FatUtility::int($couponId);
        if (empty($couponId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $srch = Coupon::getHistorySearchObject();
        $srch->addCondition('couhis_coupon_id', '=', $couponId);
        $srch->addMultipleFields(['order_id', 'order_total_amount', 'couhis_released',
            'order_addedon', 'user_first_name', 'user_last_name']);
        $srch->addOrder('couhis_id', 'DESC');
        $srch->doNotCalculateRecords();
        $this->set("coupon", Coupon::getAttributesById($couponId));
        $this->set("records", FatApp::getDb()->fetchAll($srch->getResultSet()));
        $this->_template->render(false, false);
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmCouponSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword', '');
        $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'coupon_active', AppConstant::getActiveArr());
        $frm->addSelectBox(Label::getLabel('LBL_EXPIRE'), 'coupon_expire', AppConstant::getYesNoArr());
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setIntPositive();
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnCancel = $frm->addButton("", "btn_clear", Label::getLabel('MSG_Clear'), ['onclick' => 'clearSearch()']);
        $btnSubmit->attachField($btnCancel);
        return $frm;
    }

    /**
     * Get Form
     * 
     * @return Form
     */
    private function getForm(): Form
    {
        $frm = new Form('frmCoupon');
        $frm->addHiddenField('', 'coupon_id', '', ['id' => 'coupon_id'])->requirements()->setIntPositive();
        $frm->addRequiredField(Label::getLabel('LBL_Coupon_Identifier'), 'coupon_identifier');
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Coupon_Code'), 'coupon_code');
        $fld->setUnique(Coupon::DB_TBL, 'coupon_code', 'coupon_id', 'coupon_id', 'coupon_id');
        $couponType = $frm->addSelectBox(Label::getLabel('LBL_DISCOUNT_TYPE'), 'coupon_discount_type', AppConstant::getPercentageFlatArr(), AppConstant::FLAT_VALUE, [], '');
        $couponType->requirements()->setRequired();
        $fld = $frm->addFloatField(Label::getLabel('LBL_DISCOUNT_VALUE'), 'coupon_discount_value');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setFloatPositive();
        $fld->requirements()->setRange(1, 9999999);
        $percentageRequirement = new FormFieldRequirement('coupon_discount_value', Label::getLabel('LBL_DISCOUNT_VALUE'));
        $percentageRequirement->setRequired(true);
        $percentageRequirement->setFloatPositive();
        $percentageRequirement->setRange(0.001, 9999999);
        $flatRequirement = new FormFieldRequirement('coupon_discount_value', Label::getLabel('LBL_DISCOUNT_VALUE'));
        $flatRequirement->setRequired(true);
        $flatRequirement->setFloatPositive();
        $flatRequirement->setRange(1, 9999999);
        $couponType->requirements()->addOnChangerequirementUpdate(AppConstant::PERCENTAGE, 'eq', 'coupon_discount_value', $percentageRequirement);
        $couponType->requirements()->addOnChangerequirementUpdate(AppConstant::FLAT_VALUE, 'eq', 'coupon_discount_value', $flatRequirement);

        $fld = $frm->addFloatField(Label::getLabel('LBL_MAX_DISCOUNT'), 'coupon_max_discount');
        $maxDiscountRequired = new FormFieldRequirement('coupon_max_discount', Label::getLabel('LBL_Max_Discount'));
        $maxDiscountRequired->setRequired(true);
        $maxDiscountRequired->setFloatPositive();
        $maxDiscountRequired->setRange(1, 9999999);
        $maxDiscountOptional = new FormFieldRequirement('coupon_max_discount', Label::getLabel('LBL_Max_Discount'));
        $maxDiscountOptional->setRequired(false);
        $couponType->requirements()->addOnChangerequirementUpdate(AppConstant::PERCENTAGE, 'eq', 'coupon_max_discount', $maxDiscountRequired);
        $couponType->requirements()->addOnChangerequirementUpdate(AppConstant::FLAT_VALUE, 'eq', 'coupon_max_discount', $maxDiscountOptional);
        $frm->addFloatField(Label::getLabel('LBL_MIN_ORDER'), 'coupon_min_order')->requirements()->setFloatPositive();
        $frm->addIntegerField(Label::getLabel('LBL_Max_uses'), 'coupon_max_uses', 1)->requirements()->setIntPositive();
        $frm->addIntegerField(Label::getLabel('LBL_Uses/User'), 'coupon_user_uses', 1)->requirements()->setIntPositive();
        $frm->addDateField(Label::getLabel('LBL_Date_From'), 'coupon_start_date', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender'])->requirements()->setRequired();
        $frm->addDateField(Label::getLabel('LBL_Date_Till'), 'coupon_end_date', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender'])->requirements()->setRequired();
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'coupon_active', AppConstant::getActiveArr())->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /**
     * Get Lang Form
     * 
     * @param int $couponId
     * @param int $langId
     * @return Form
     */
    private function getLangForm($couponId = 0, $langId = 0): Form
    {
        $frm = new Form('frmCouponLang');
        $frm->addHiddenField('', 'couponlang_lang_id', FatUtility::int($langId));
        $frm->addHiddenField('', 'couponlang_coupon_id', FatUtility::int($couponId));
        $frm->addRequiredField(Label::getLabel('LBL_Coupon_title'), 'coupon_title');
        $frm->addTextArea(Label::getLabel('LBL_Description'), 'coupon_description')->requirements()->setLength(0, 250);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

}
