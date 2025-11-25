<?php

class TutorRequestController extends MyAppController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * (Optional) still available if some old UI uses it,
     * but NOT used by the new tutor request form anymore.
     */
    public function courses()
    {
        $langId = MyUtility::getSiteLangId();

        $srch = new SearchBase('tbl_courses', 'c');
        $srch->joinTable(
            'tbl_courses_lang',
            'LEFT JOIN',
            'cl.courselang_course_id = c.course_id AND cl.courselang_lang_id = ' . $langId,
            'cl'
        );
        $srch->addCondition('c.course_active', '=', AppConstant::YES);
        $srch->addMultipleFields([
            'c.course_id',
            'IFNULL(cl.course_title, c.course_slug) AS course_title'
        ]);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1000);

        $rs   = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);

        FatUtility::dieJsonSuccess(['courses' => $rows]);
    }

    /**
     * Create a tutor request (AJAX / POST)
     * NEW FLOW: Level + Subject + Exam Board + Tier (no courses)
     */
    public function create()
    {
        $frm     = $this->getRequestForm();
        $rawPost = FatApp::getPostedData();

        // Basic field validation via Form (name, email, phone, preferred time)
        if (!$post = $frm->getFormDataFromArray($rawPost)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        // Extra fields from new flow (we keep them as ints)
        $levelId   = FatUtility::int($post['tutreq_level_id'] ?? 0);
        $subjectId = FatUtility::int($post['tutreq_subject_id'] ?? 0);
        $examId    = FatUtility::int($post['tutreq_examboard_id'] ?? 0);
        $tierId    = FatUtility::int($post['tutreq_tier_id'] ?? 0);

        // Level & subject MUST be chosen
        // if ($levelId <= 0 || $subjectId <= 0) {
        //     FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_LEVEL_AND_SUBJECT'));
        // }

        // Exam board & tier are optional – no hard validation for now
        // (you can enforce them later if you like)

        // Identify logged user (if any)
        $userId = 0;
        if (class_exists('UserAuthentication') && method_exists('UserAuthentication', 'getLoggedUserId')) {
            $userId = FatUtility::int(UserAuthentication::getLoggedUserId());
        }

        // Build data for tbl_tutor_requests
        $data = [
            'tutreq_user_id'        => $userId,
            'tutreq_first_name'     => $post['tutreq_first_name'],
            'tutreq_last_name'      => $post['tutreq_last_name'] ?? '',
            'tutreq_email'          => $post['tutreq_email'],
            'tutreq_phone_code'     => FatUtility::int($post['tutreq_phone_code']),
            'tutreq_phone_number'   => $post['tutreq_phone_number'],
            'tutreq_preferred_time' => $post['tutreq_preferred_time'] ?? '',
            'tutreq_status'         => 0,
            'tutreq_added_on'       => date('Y-m-d H:i:s'),

            // 🔹 NEW COLUMNS (must exist in tbl_tutor_requests)
            'tutreq_level_id'       => $levelId,
            'tutreq_subject_id'     => $subjectId,
            'tutreq_examboard_id'   => $examId,
            'tutreq_tier_id'        => $tierId,
        ];

        // We no longer use courseIds; pass an empty array.
        if (!TutorRequest::saveRequest($data, [])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_UNABLE_TO_SAVE_REQUEST'));
        }

        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REQUEST_SUBMITTED_SUCCESSFULLY'));
    }

    /**
     * Form definition used only for basic validation.
     * No course_ids field anymore. We add hidden fields for the new IDs
     * so they flow through $frm->getFormDataFromArray().
     */
    private function getRequestForm(): Form
    {
        $frm = new Form('tutorReqFrm');

        // Name / email
        $frm->addRequiredField(Label::getLabel('LBL_FIRST_NAME'), 'tutreq_first_name');
        $frm->addTextBox(Label::getLabel('LBL_LAST_NAME'), 'tutreq_last_name');
        $frm->addEmailField(Label::getLabel('LBL_EMAIL'), 'tutreq_email')
            ->requirements()->setRequired();

        // Phone
        $countries = Country::getAll(MyUtility::getSiteLangId());
        $phoneCodes = array_column($countries, 'phone_code', 'country_id');

        $frm->addSelectBox(
            Label::getLabel('LBL_PHONE_CODE'),
            'tutreq_phone_code',
            $phoneCodes
        )->requirements()->setRequired();

        $phoneFld = $frm->addTextBox(Label::getLabel('LBL_PHONE'), 'tutreq_phone_number');
        $phoneFld->requirements()->setRequired();
        $phoneFld->requirements()->setRegularExpressionToValidate(AppConstant::PHONE_NO_REGEX);
        $phoneFld->requirements()->setCustomErrorMessage(Label::getLabel('LBL_PHONE_NO_VALIDATION_MSG'));

        // Preferred time (we store "9:00 AM - 11:00 AM" from hidden field)
        $frm->addTextArea(Label::getLabel('LBL_PREFERRED_TIME'), 'tutreq_preferred_time');

        // 🌟 NEW HIDDEN FIELDS – IDs posted from the Level/Subject/Exam/Tier selects
        $frm->addHiddenField('', 'tutreq_level_id');
        $frm->addHiddenField('', 'tutreq_subject_id');
        $frm->addHiddenField('', 'tutreq_examboard_id');
        $frm->addHiddenField('', 'tutreq_tier_id');

        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SUBMIT'));

        return $frm;
    }
}
