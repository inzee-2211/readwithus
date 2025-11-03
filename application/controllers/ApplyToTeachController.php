<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class ApplyToTeachController extends MyAppController
{
    public function index()
    {
    // Hero art (replace with Image controller later if you store it there)
        $hero = [
            'title1' => 'Become a Tutor',
            'title2' => 'Empower Students Globally',
            'subtitle' => 'Teach what you love. Set your own hours, earn confidently, and grow your teaching career online.',
            'cta_text' => 'Apply to Teach Now',
            'cta_url'  => MyUtility::makeUrl('ApplyToTeach', 'start'), // stub for your future form
            'bg_url'   => CONF_WEBROOT_URL . 'images/hero/teacher.png', // put an image here
        ];

        // Fun facts (numbers you can later fetch from DB)
        $stats = [
            ['icon' => '👩‍🎓', 'label' => 'Students',            'value' => '67.1k'],
            ['icon' => '🎓',   'label' => 'Certified Instructors','value' => '26k'],
            ['icon' => '🌍',   'label' => 'Country Language',     'value' => '72'],
            ['icon' => '✅',   'label' => 'Success Rate',         'value' => '99.9%'],
            ['icon' => '🏢',   'label' => 'Trusted Companies',     'value' => '57'],
        ];

        // Steps (how it works)
        $steps = [
            ['title' => 'Apply to become instructor.', 'desc' => 'Fill a short application and tell us about your expertise.'],
            ['title' => 'Setup & edit your profile.',  'desc' => 'Add subjects, availability, intro video, and pricing.'],
            ['title' => 'Create your course.',         'desc' => 'Use our builder to add lessons, quizzes, and projects.'],
            ['title' => 'Start teaching & earning.',   'desc' => 'Get booked by students worldwide and track earnings.'],
        ];

        // Requirements & benefits (simple arrays you can localize later)
        $requirements = [
            'Strong command over the subject area',
            'Clear communication in English (or selected language)',
            'Stable internet & camera for live sessions',
            'Ability to create engaging learning materials',
            'Compliance with our tutor code of conduct',
        ];

        $benefits = [
            ['title'=>'Flexible schedule','desc'=>'Teach when it suits you.'],
            ['title'=>'Global reach','desc'=>'Connect with learners across countries.'],
            ['title'=>'Built-in tools','desc'=>'AI quizzes, assignments, analytics.'],
            ['title'=>'Secure payouts','desc'=>'Timely payments in your currency.'],
            ['title'=>'Growth support','desc'=>'Guides, feedback, and resources.'],
            ['title'=>'Community','desc'=>'Share tips with fellow instructors.'],
        ];

        $faqs = [
            ['q'=>'Do I need prior teaching experience?', 'a'=>'It’s preferred but not required. We value subject mastery and clarity.'],
            ['q'=>'How are tutors paid?', 'a'=>'We support secure payouts to your region. Earnings are tracked in your dashboard.'],
            ['q'=>'Can I set my own rates?', 'a'=>'Yes, you control pricing and discounts for lessons and courses.'],
            ['q'=>'What subjects can I teach?', 'a'=>'Anything approved by our quality team—STEM, languages, exam prep, and more.'],
        ];

        // Pass data to view
        $this->set('hero', $hero);
        $this->set('stats', $stats);
        $this->set('steps', $steps);
        $this->set('requirements', $requirements);
        $this->set('benefits', $benefits);
        $this->set('faqs', $faqs);

        // Page title for meta
        $this->set('title', Label::getLabel('LBL_APPLY_AS_TUTOR', $this->siteLangId));

        $this->_template->render(true, true, 'apply_to_teach/index.php');
    }

    

    /**
     * Start (pre-signup gate). If logged-in learner (not teacher) => jump to the old multi-step form.
     * If guest => show compact email+password form with live email check.
     */
    public function start()
    {
        $siteUserId = FatUtility::int($this->siteUserId);
        if ($siteUserId > 0) {
            $user = User::getAttributesById($siteUserId, ['user_id','user_is_teacher']);
            // If already a teacher, go to dashboard (or wherever you like)
            if (!empty($user['user_is_teacher'])) {
                FatApp::redirectUser(MyUtility::makeUrl('Teacher', 'dashboard'));
            }
            // Not a teacher yet -> reuse EXISTING multi-step flow
            FatApp::redirectUser(MyUtility::makeUrl('TeacherRequest', 'form'));
        }

        // Guest: render a modern signup card (email/password agree)
        $applyTeachFrm = $this->getApplyTeachFrm();
        $this->set('applyTeachFrm', $applyTeachFrm);
        $this->set('title', Label::getLabel('LBL_APPLY_AS_TUTOR', $this->siteLangId));
        $this->_template->render(true, true, 'apply_to_teach/start.php');
    }

    /**
     * AJAX: validate email quickly before submit
     */
    public function checkEmail()
    {
        $email = FatApp::getPostedData('email', FatUtility::VAR_STRING, '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_EMAIL'));
        }
        // Check uniqueness
        $srch = new SearchBase('tbl_users', 'u');
        $srch->addCondition('u.user_email', '=', $email);
        $srch->addFld('u.user_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_EMAIL_ALREADY_REGISTERED'));
        }
        FatUtility::dieJsonSuccess(['ok' => 1]);
    }

    /**
     * Handle pre-signup then handoff to TeacherRequest steps
     * (Stricter checks than before: email unique, password policy, terms)
     */
  public function teacherSetup()
{
    $frm  = $this->getTeacherSignupForm();
    $post = FatApp::getPostedData();
    $defaultCountryId = 0;

    /* -------- Normalize & pre-checks -------- */
    $post['user_email'] = isset($post['user_email']) ? trim(mb_strtolower($post['user_email'])) : '';
    if (!isset($post['user_first_name']) || trim($post['user_first_name']) === '') {
        $post['user_first_name'] = strstr($post['user_email'], '@', true) ?: 'Tutor';
    }

    if (!$post = $frm->getFormDataFromArray($post)) {
        FatUtility::dieJsonError('Form validation failed: ' . current($frm->getValidationErrors()));
    }

    // Email format
    if (!filter_var($post['user_email'], FILTER_VALIDATE_EMAIL)) {
        FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_EMAIL'));
    }

    // Email uniqueness (server-side)
    $srch = new SearchBase('tbl_users', 'u');
    $srch->addCondition('u.user_email', '=', $post['user_email']);
    $srch->addFld('u.user_id');
    if (FatApp::getDb()->fetch($srch->getResultSet())) {
        FatUtility::dieJsonError(Label::getLabel('MSG_EMAIL_ALREADY_REGISTERED'));
    }

    // Password policy
    if (!MyUtility::validatePassword($post['user_password'])) {
        FatUtility::dieJsonError(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
    }

    // Terms agreed
    if (empty($post['agree'])) {
        FatUtility::dieJsonError(Label::getLabel('MSG_Terms_and_Condition_and_Privacy_Policy_are_mandatory.'));
    }

    /* -------- Transaction & user creation -------- */
    $db = FatApp::getDb();
    $db->startTransaction();

    // Ensure teacher role (we override whatever came from the form)
    $userData = array_merge($post, [
        'user_dashboard'     => User::TEACHER,
        'user_registered_as' => User::TEACHER,
        'user_lang_id'       => MyUtility::getSiteLangId(),
        'user_timezone'      => MyUtility::getSiteTimezone(),
        'user_country_id'     => $defaultCountryId,
        'user_wallet_balance' => 0,

    ]);

    $user = new User();
    $user->assignValues($userData);

    if (FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION') == AppConstant::NO) {
        $user->setFldValue('user_verified', date('Y-m-d H:i:s'));
    }
    if (empty(FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION'))) {
        $user->setFldValue('user_active', AppConstant::YES);
    }

    // Step 1: save()
    if (!$user->save()) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError('Save failed: ' . $user->getError());
    }

    // Step 2: setSettings()
    if (!$user->setSettings($userData)) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError('setSettings failed: ' . $user->getError());
    }

    // Step 3: setPassword()
    if (!$user->setPassword($post['user_password'])) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError('setPassword failed: ' . $user->getError());
    }

    if (!$db->commitTransaction()) {
        FatUtility::dieJsonError('Commit failed: ' . $db->getError());
    }

    /* -------- Post-create: email & handoff to steps -------- */
    $userData['user_id'] = $user->getMainTableRecordId();

    $auth = new UserAuth();
    $res  = $auth->sendSignupEmails($userData);

    if (
        FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION') == AppConstant::NO &&
        FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION') == AppConstant::NO &&
        FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION') == AppConstant::YES
    ) {
        if (!$auth->login($userData['user_email'], $post['user_password'], MyUtility::getUserIp())) {
            FatUtility::dieJsonError('Auto-login failed: ' . $auth->getError());
        }
    } else {
        // Needed so TeacherRequest/form knows the user context
        TeacherRequest::startSession($userData);
    }

    FatUtility::dieJsonSuccess([
        'msg' => $res['msg'] ?? Label::getLabel('LBL_REGISTERATION_SUCCESSFULL'),
        'redirectUrl' => MyUtility::makeUrl('TeacherRequest', 'form'),
    ]);
}


    /* ---------- Forms used on this page ---------- */

    private function getApplyTeachFrm(): Form
    {
        // compact guest form (email/pass/agree) — endpoint points to THIS controller
        $frm = new Form('frmApplyTeachFrm');
        $frm->addHiddenField('', 'user_id', 0);
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email_ID'), 'user_email', '', ['autocomplete' => 'off', 'data-check-url' => MyUtility::makeUrl('ApplyToTeach','checkEmail')]);
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id'); // keeps server validation too
        $fld = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'user_password');
        $fld->requirements()->setRequired();
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel(AppConstant::PASSWORD_CUSTOM_ERROR_MSG));

        $frm->addHiddenField('', 'user_dashboard', User::TEACHER);
        $frm->addHiddenField('', 'agree', 1)->requirements()->setRequired();

        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_REGISTER_WITH_EMAIL'));
        $frm->setFormTagAttribute('action', MyUtility::makeUrl('ApplyToTeach','teacherSetup'));
        return $frm;
    }

    private function getTeacherSignupForm(): Form
    {
        // same as your older one, only we set dashboard=LEARNER there; we’ll keep it consistent but point form->action to this controller
        $frm = new Form('signupForm');
        $frm->addRequiredField(Label::getLabel('LBL_FIRST_NAME'), 'user_first_name');
        $frm->addTextBox(Label::getLabel('LBL_LAST_NAME'), 'user_last_name');

        $fld = $frm->addEmailField(Label::getLabel('LBL_EMAIL_ID'), 'user_email', '', ['autocomplete="off"']);
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');

        $fld = $frm->addPasswordField(Label::getLabel('LBL_PASSWORD'), 'user_password');
        $fld->requirements()->setRequired();
        $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
        $fld->requirements()->setRegularExpressionToValidate(AppConstant::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_8_Digit_AlphaNumeric_Password'));

        $fld = $frm->addCheckBox(Label::getLabel('LBL_I_accept_to_the'), 'agree', 1);
        $fld->requirements()->setRequired();
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Terms_and_Condition_and_Privacy_Policy_are_mandatory.'));

        // We’ll set TEACHER inside teacherSetup() to avoid any mismatch.
        $frm->addHiddenField('', 'user_dashboard', User::LEARNER);

        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Register'));
        $frm->setFormTagAttribute('action', MyUtility::makeUrl('ApplyToTeach','teacherSetup'));
        return $frm;
    }
}
