
<?php

class TutorRequestController extends MyAppController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Return active courses as JSON for the multiselect.
     * Frontend can call: /TutorRequest/courses
     */
    public function courses()
    {
        $langId = MyUtility::getSiteLangId();

        // Fetch active/approved, language-aware course names
        // If you have a CourseSearch util, use it; below is a safe direct pull:
        $srch = new SearchBase('tbl_courses', 'c');
        $srch->joinTable('tbl_courses_lang', 'LEFT JOIN', 'cl.courselang_course_id = c.course_id AND cl.courselang_lang_id = ' . $langId, 'cl');
        $srch->addCondition('c.course_active', '=', AppConstant::YES);
        $srch->addMultipleFields(['c.course_id', 'IFNULL(cl.course_title, c.course_slug) AS course_title']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1000);
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);

        FatUtility::dieJsonSuccess(['courses' => $rows]);
    }

    /**
     * Create a tutor request (AJAX or POST)
     */
    public function create()
    {
        $frm = $this->getRequestForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $courseIds = FatApp::getPostedData('course_ids', null, []);   // get raw
$courseIds = array_values(array_unique(array_filter(array_map('intval', (array)$courseIds)))) ;

if (empty($courseIds)) {
    FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_AT_LEAST_ONE_COURSE'));
}
if ($courseIds) {
    $srch = new SearchBase('tbl_courses', 'c');
    $srch->addCondition('c.course_id', 'IN', $courseIds);
    $srch->addCondition('c.course_active', '=', AppConstant::YES);
    $srch->addMultipleFields(['c.course_id']);
    $rs = $srch->getResultSet();
    $validIds = array_map('intval', array_column(FatApp::getDb()->fetchAll($rs), 'course_id'));
    $courseIds = array_values(array_intersect($courseIds, $validIds));
    if (empty($courseIds)) {
        FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_COURSE_SELECTION'));
    }
}
$userId = 0;
if (class_exists('UserAuthentication') && method_exists('UserAuthentication', 'getLoggedUserId')) {
    $userId = FatUtility::int(UserAuthentication::getLoggedUserId());
}

        $data = [
            'tutreq_user_id'       => $userId,
         
            'tutreq_first_name'    => $post['tutreq_first_name'],
            'tutreq_last_name'     => $post['tutreq_last_name'] ?? '',
            'tutreq_email'         => $post['tutreq_email'],
            'tutreq_phone_code'    => FatUtility::int($post['tutreq_phone_code']),
            'tutreq_phone_number'  => $post['tutreq_phone_number'],
            'tutreq_preferred_time'=> $post['tutreq_preferred_time'] ?? '',
            'tutreq_status'        => 0,
            'tutreq_added_on'      => date('Y-m-d H:i:s'),
        ];

        if (!TutorRequest::saveRequest($data, $courseIds)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_UNABLE_TO_SAVE_REQUEST'));
        }

        // $this->notifyAdmin($data, $courseIds);
 // ✅ Always return a canonical success payload (no extra output before/after).
FatUtility::dieJsonSuccess(Label::getLabel('LBL_REQUEST_SUBMITTED_SUCCESSFULLY'));
// This yields: {"status":1,"msg":"Request Submitted Successfully"}


    }
// private function notifyAdmin(array $data, array $courseIds): void
// {
//     $adminEmail = TutorRequest::getAdminEmail();
//     if (empty($adminEmail)) { return; }

//     $titles = [];
//     if (!empty($courseIds)) {
//         $langId = (int) MyUtility::getSiteLangId();

//         $srch = new SearchBase('tbl_courses', 'c');
//         // alias must be provided separately for YoCoach SearchBase
//         $srch->joinTable(
//             'tbl_courses_lang',
//             'LEFT JOIN',
//             'cl.courselang_course_id = c.course_id AND cl.courselang_lang_id = ' . $langId,
//             'cl'
//         );
//         $srch->addCondition('c.course_id', 'IN', array_map('intval', $courseIds));
//         $srch->addMultipleFields(['IFNULL(cl.course_title, c.course_slug) AS title']);
//         $srch->doNotCalculateRecords();
//         $srch->setPageSize(1000);

//         $rs = $srch->getResultSet();

//         if ($rs) {
//             $rows = FatApp::getDb()->fetchAll($rs) ?: [];
//             foreach ($rows as $r) {
//                 if (!empty($r['title'])) { $titles[] = $r['title']; }
//             }
//         } else {
//             // Optional: inspect SQL error during dev
//             // error_log('notifyAdmin course title query failed: ' . FatApp::getDb()->getError());
//         }
//     }

//     $vars = [
//         '{first_name}'     => $data['tutreq_first_name'] ?? '',
//         '{last_name}'      => $data['tutreq_last_name'] ?? '',
//         '{email}'          => $data['tutreq_email'] ?? '',
//         '{phone}'          => '+' . ($data['tutreq_phone_code'] ?? '') . '-' . ($data['tutreq_phone_number'] ?? ''),
//         '{preferred_time}' => $data['tutreq_preferred_time'] ?? '',
//         '{courses}'        => implode(', ', $titles ?: array_map('intval', $courseIds)),
//     ];

//     // Use your email template (Admin -> Email Templates) with identifier: tutor_request_admin
//     $mail = new FatMailer(MyUtility::getSiteLangId(), 'tutor_request_admin');
//     $mail->setVariables($vars);
//     $mail->sendMail([$adminEmail]);
// }

    private function getRequestForm(): Form
    {
        $frm = new Form('tutorReqFrm');

        $frm->addRequiredField(Label::getLabel('LBL_FIRST_NAME'), 'tutreq_first_name');
        $frm->addTextBox(Label::getLabel('LBL_LAST_NAME'), 'tutreq_last_name');
        $frm->addEmailField(Label::getLabel('LBL_EMAIL'), 'tutreq_email')->requirements()->setRequired();

        $countries = Country::getAll(MyUtility::getSiteLangId());
        $frm->addSelectBox(Label::getLabel('LBL_PHONE_CODE'), 'tutreq_phone_code', array_column($countries, 'phone_code', 'country_id'))->requirements()->setRequired();
        $phoneFld = $frm->addTextBox(Label::getLabel('LBL_PHONE'), 'tutreq_phone_number');
        $phoneFld->requirements()->setRequired();
        $phoneFld->requirements()->setRegularExpressionToValidate(AppConstant::PHONE_NO_REGEX);
        $phoneFld->requirements()->setCustomErrorMessage(Label::getLabel('LBL_PHONE_NO_VALIDATION_MSG'));

        $frm->addTextArea(Label::getLabel('LBL_PREFERRED_TIME'), 'tutreq_preferred_time');

        // courses handled separately as 'course_ids[]' in the view
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }
    
}
