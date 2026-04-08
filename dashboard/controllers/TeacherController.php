<?php

/**
 * Teacher Controller is used for handling Teachers
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class TeacherController extends DashboardController
{

    /**
     * Initialize Teacher
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        MyUtility::setUserType(User::TEACHER);
        parent::__construct($action);
        $_SESSION['RWU_DASHBOARD_ROLE'] = 'teacher';

    }

    /**
     * Teacher Search Form
     */
    public function index()
    {
        $statistics = new Statistics($this->siteUserId);
        $earningData = $statistics->getEarning(MyDate::TYPE_ALL);
        $sessionStats = $statistics->getSessionStats();
        $courseStats = (new OrderCourse(0, $this->siteUserId, $this->siteUserType, $this->siteLangId))->getCourseStats();
        $this->sets([
            'earnings' => $earningData['earning'] ?? 0,
            'schLessonCount' => $sessionStats['lessStats']['schLessonCount'],
            'schClassCount' => $sessionStats['classStats']['schClassCount'],
            'viewProfile' => $this->siteUser['profileProgress']['isProfileCompleted'] ?? 0,
            'durationType' => MyDate::getDurationTypesArr(),
            'userTimezone' => $this->siteTimezone,
            'walletBalance' => User::getWalletBalance($this->siteUserId),
            'setMonthAndWeekNames' => true,
            'courseCount' => $courseStats['totalCourses']
        ]);
        $this->_template->addJs([
            'js/moment.min.js', 'js/fullcalendar-luxon.min.js',
            'js/fateventcalendar.js', 'js/jquery.cookie.js', 'js/app.timer.js',
            'js/fullcalendar.min.js', 'js/fullcalendar-luxon-global.min.js',
        ]);
        $this->_template->render();
    }

    /**
     * Render Tech Lang Price Form
     */
    public function techLangPriceForm()
    {
        $showAdminSlab = FatApp::getPostedData('showAdminSlab', FatUtility::VAR_BOOLEAN, false);
        $userTeachingLang = $this->getUserTeachLangData();
        $slabData = (new PriceSlab())->getAllSlabs();
        $teacherAddedSlabs = array_column($userTeachingLang, 'minMaxKey', 'minMaxKey');
        unset($teacherAddedSlabs['0-0']);
        $slabDifference = [];
        if (!empty($userTeachingLang)) {
            $adminAddedSlabs = array_column($slabData, 'minMaxKey', 'minMaxKey');
            $slabDifference = array_merge(array_diff($adminAddedSlabs, $teacherAddedSlabs), array_diff($teacherAddedSlabs, $adminAddedSlabs));
        }
        $priceSum = array_sum(array_column($userTeachingLang, 'ustelgpr_price'));
        $slabs = $slabData;
        if ($priceSum > 0 && !$showAdminSlab) {
            $slabs = $this->formatTeacherSlabs($userTeachingLang);
        }
        $this->sets([
            'frm' => $this->getTechLangPriceForm($userTeachingLang, $slabs, $showAdminSlab),
            'userToTeachLangRows' => $userTeachingLang,
            'slabDifference' => $slabDifference,
            'showAdminSlab' => $showAdminSlab,
            'defaultCurrency' => MyUtility::getSystemCurrency(),
            'slabs' => $slabs,
            'priceSum' => $priceSum
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Teach Lang Price
     */
    public function setupLangPrice()
    {
        $showAdminSlab = FatApp::getPostedData('showAdminSlab', FatUtility::VAR_BOOLEAN, false);
        $form = $this->getTechLangPriceForm(null, null, $showAdminSlab);
        $post = FatApp::getPostedData();
        $postData = $form->getFormDataFromArray($post);
        if (false === $postData) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        if (empty($post['duration'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_DURATION_IS_REQURIED'));
        }
        if (empty($post['ustelgpr_price'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PRICE_IS_REQURIED'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $teachLangPrice = new TeachLangPrice();
        if (!$teachLangPrice->deleteAllUserPrice($this->siteUserId)) {
            FatUtility::dieJsonError($teachLangPrice);
        }
        foreach ($post['duration'] as $durationKey => $duration) {
            if (empty($duration) || $durationKey != $duration) {
                continue;
            }
            $slabs = $post['ustelgpr_price'][$duration];
            foreach ($slabs as $slabKey => $languages) {
                if (empty($languages)) {
                    continue;
                }
                $slabAarray = explode('-', $slabKey);
                foreach ($languages as $userTeachLangang => $price) {
                    $teachLangPrice = new TeachLangPrice($duration, $userTeachLangang);
                    if (!$teachLangPrice->saveTeachLangPrice($slabAarray[0], $slabAarray[1], $price)) {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError($teachLangPrice->getError());
                    }
                }
            }
        }
        $stat = new TeacherStat($this->siteUserId);
        if (!$stat->setTeachLangPrices()) {
            FatUtility::dieJsonError($stat->getError());
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SETUP_SUCCESSFUL'));
    }

    /**
     * Render Teacher Languages Form
     */
    public function teacherLanguagesForm()
    {
        $speakLangs = SpeakLanguage::getAllLangs($this->siteLangId, true);
        $this->sets([
            'speakLangs' => $speakLangs,
            'profArr' => SpeakLanguage::getProficiencies(),
            'frm' => $this->getTeacherLanguagesForm($this->siteLangId, $speakLangs)
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Get Teacher Profile Progress
     */
    public function profileProgress()
    {
        FatUtility::dieJsonSuccess(['PrfProg' => $this->siteUser['profile_progress']]);
    }

    /**
     * Setup Teacher Languages
     */
    public function setupTeacherLanguages()
    {
        $frm = $this->getTeacherLanguagesForm($this->siteLangId, []);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $speakLangs = array_filter(FatUtility::int($post['uslang_slang_id']));
        if (empty($speakLangs)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SPEAK_LANGUAGE_IS_REQURIED'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $error = '';
        if (!$this->deleteUserTeachLang($post['teach_lang_id'], $error)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($error);
        }
        foreach ($post['teach_lang_id'] as $tlang) {
            if (empty($tlang)) {
                continue;
            }
            $userTeachLanguage = new UserTeachLanguage($this->siteUserId);
            if (!$userTeachLanguage->saveTeachLang($tlang)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($userTeachLanguage->getError());
            }
        }
        if (!$this->deleteUserSpeakLang($speakLangs, $error)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($error);
        }
        foreach ($speakLangs as $key => $lang) {
            if (empty($lang)) {
                continue;
            }
            $insertArr = ['uslang_slang_id' => $lang, 'uslang_proficiency' => $post['uslang_proficiency'][$key], 'uslang_user_id' => $this->siteUserId];
            if (!$db->insertFromArray(UserSpeakLanguage::DB_TBL, $insertArr, false, [], $insertArr)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($db->getError());
            }
        }
        (new TeacherStat($this->siteUserId))->setTeachLangPrices();
        (new TeacherStat($this->siteUserId))->setSpeakLang();
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SETUP_SUCCESSFUL'));
    }

    /**
     * Render Teacher Qualification Form
     * 
     * @param int $qualifId
     */
    public function teacherQualificationForm($qualifId = 0)
    {
        $qualifId = FatUtility::int($qualifId);
        $frm = UserQualification::getForm();
        if ($qualifId > 0) {
            $uQuali = new UserQualification($qualifId, $this->siteUserId);
            if (!$row = $uQuali->getQualiForUpdate()) {
                FatUtility::dieJsonError($uQuali->getError());
            }
            $frm->fill($row);
        }
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    /**
     * Setup Teacher Qualification
     */
    public function setupTeacherQualification()
    {
        $frm = UserQualification::getForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $id = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        $qualification = new UserQualification($id, $this->siteUserId);
        if ($id > 0 && !$qualification->getQualiForUpdate()) {
            FatUtility::dieJsonError($qualification->getError());
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $post['uqualification_active'] = AppConstant::YES;
        $post['uqualification_user_id'] = $this->siteUserId;
        $qualification->assignValues($post);
        if (!$qualification->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($qualification->getError());
        }
        if (!empty($_FILES['certificate']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['certificate']['tmp_name'])) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_A_FILE'));
            }
            $file = new Afile(Afile::TYPE_USER_QUALIFICATION_FILE);
            if (!$file->saveFile($_FILES['certificate'], $qualification->getMainTableRecordId(), true)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($file->getError());
            }
        }
        (new TeacherStat($this->siteUserId))->setQualification();
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_QUALIFICATION_SETUP_SUCCESSFUL'));
    }

    /**
     * Delete Teacher Qualification
     * 
     * @param int $id
     */
    public function deleteTeacherQualification($id = 0)
    {
        $id = FatUtility::int($id);
        if ($id < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $qualification = new UserQualification($id, $this->siteUserId);
        if (!$qualification->getQualiForUpdate()) {
            FatUtility::dieJsonError($qualification->getError());
        }
        if (!$qualification->deleteRecord()) {
            FatUtility::dieJsonError($qualification->getError());
        }
        $file = new Afile(Afile::TYPE_USER_QUALIFICATION_FILE);
        $file->removeFile($id, true);
        (new TeacherStat($this->siteUserId))->setQualification();
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_QUALIFICATION_REMOVED_SUCCESSFULY'));
    }

    /**
     * Teacher Qualification
     */
    public function teacherQualification()
    {
        $qualification = new UserQualification(0, $this->siteUserId);
        $this->set('qualificationData', $qualification->getUQualification(false, true));
        $this->_template->render(false, false);
    }

    /**
     * RenderTeacher Preferences Form
     */
    public function teacherPreferencesForm()
    {
        $teacherPrefArr = Preference::getUserPreferences($this->siteUserId);
        $arrOptions = [];
        foreach ($teacherPrefArr as $val) {
            $arrOptions['pref_' . $val['prefer_type']][] = $val['uprefer_prefer_id'];
        }
        $frm = $this->getTeacherPreferencesForm();
        $frm->fill($arrOptions);
        $this->set('preferencesFrm', $frm);
        $this->_template->render(false, false);
    }

    /**
     * Setup Teacher Preferences
     */
    public function setupTeacherPreferences()
    {
        $frm = $this->getTeacherPreferencesForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        if (!$db->deleteRecords(Preference::DB_TBL_USER_PREF, ['smt' => 'uprefer_user_id = ?', 'vals' => [$this->siteUserId]])) {
            FatUtility::dieJsonError($db->getError());
        }
        $preference = 0;
        foreach ($post as $val) {
            if (empty($val)) {
                continue;
            }
            foreach ($val as $innerVal) {
                if (!$db->insertFromArray(Preference::DB_TBL_USER_PREF, ['uprefer_prefer_id' => $innerVal, 'uprefer_user_id' => $this->siteUserId])) {
                    $db->rollbackTransaction();
                    FatUtility::dieJsonError($db->getError());
                }
                $preference = 1;
            }
        }
        $db->commitTransaction();
        (new TeacherStat($this->siteUserId))->setPreference($preference);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_PREFERENCES_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Render Availability Page
     */
    public function availability()
    {
        $this->set('setMonthAndWeekNames', true);
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
     * Render General Availability page
     */
    public function generalAvailability()
    {
        $this->_template->render(false, false);
    }

    /**
     * Render Weekly Availability page
     */
    public function weeklyAvailability()
    {
        $this->_template->render(false, false);
    }

    /**
     * Setup General Availability
     */
    public function setupGeneralAvailability()
    {
        $post = FatApp::getPostedData();
        $availabilityData = !empty($post['data']) ? json_decode($post['data'], true) : [];
        $availability = new Availability($this->siteUserId);
        if (!$availability->setGeneral($availabilityData)) {
            FatUtility::dieJsonError($availability->getError());
        }
        $available = empty($availabilityData) ? 0 : 1;
        (new TeacherStat($this->siteUserId))->setAvailability($available);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_AVAILABILITY_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Setup Availability
     */
    public function setupAvailability()
    {
        $start = FatApp::getPostedData('start', FatUtility::VAR_STRING, '');
        $end = FatApp::getPostedData('end', FatUtility::VAR_STRING, '');
        $availability = FatApp::getPostedData('availability', FatUtility::VAR_STRING, '');
        if (empty($start) || empty($end) || empty($availability)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $start = MyDate::formatToSystemTimezone($start);
        $end = MyDate::formatToSystemTimezone($end);
        $availability = json_decode($availability, true);
        $avail = new Availability($this->siteUserId);
        if (!$avail->setAvailability($start, $end, $availability)) {
            FatUtility::dieWithError($avail->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_AVAILABILITY_UPDATED_SUCCESSFULLY'));
    }

    /**
     * Send Message
     * 
     * @param int $userId
     */
    public function message(int $userId)
    {
        $userDetails = User::getDetail($userId);
        if (empty($userDetails) || $userId == $this->siteUserId) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
            CommonHelper::redirectUserReferer();
        }
        $teacherDetails = User::getDetail($this->siteUserId);
        $this->set('teacherDetails', $teacherDetails);
        $this->set('userDetails', $userDetails);
        $this->_template->render();
    }

    /**
     * Get Tech Lang Price Form
     * 
     * @param array $userTeachingLang
     * @param array $slabs
     * @param bool $showAdminSlab
     * @return Form
     */
    private function getTechLangPriceForm(array $userTeachingLang = null, array $slabs = null, bool $showAdminSlab = false): Form
    {
        $frm = new Form('frmSettings');
        $lessonDurations = MyUtility::getActiveSlots();
        if (null === $userTeachingLang) {
            $userTeachingLang = $this->getUserTeachLangData();
        }
        if (null === $slabs) {
            $priceSum = array_sum(array_column($userTeachingLang, 'ustelgpr_price'));
            if ($priceSum > 0 && !$showAdminSlab) {
                $slabs = $this->formatTeacherSlabs($userTeachingLang);
            } else {
                $priceSlab = new PriceSlab();
                $slabs = $priceSlab->getAllSlabs();
            }
        }
        $userTeachLangData = array_column($userTeachingLang, 'teachLangName', 'utlang_id');
        $teacherLessonDuration = array_column($userTeachingLang, 'ustelgpr_slot', 'ustelgpr_slot');
        $updatePrice = $frm->addFloatField(Label::getLabel('LBL_ADD_PRICE'), 'price_update');
        $updatePrice->requirements()->setRange(1, 99999);
        $updatePrice->requirements()->setRequired(false);
        $showAdminSlabField = $frm->addHiddenField('', 'showAdminSlab', ($showAdminSlab) ? AppConstant::YES : AppConstant::NO);
        $showAdminSlabField->requirements()->setRange(0, 1);
        $showAdminSlabField->requirements()->setRequired(true);
        $defaultSlot = FatApp::getConfig('conf_default_paid_lesson_duration', FatUtility::VAR_STRING, 60);
        $durationLabel = Label::getLabel('LBL_{duration}_MINS');
        foreach ($lessonDurations as $lessonDuration) {
            $durationLabel = str_replace('{duration}', $lessonDuration, $durationLabel);
            $durationFld = $frm->addCheckBox($durationLabel, 'duration[' . $lessonDuration . ']', $lessonDuration, [], false, 0);
            if ($lessonDuration == $defaultSlot) {
                $durationFld->requirements()->setRequired(true);
            }
            if (array_key_exists($lessonDuration, $teacherLessonDuration) || $lessonDuration == $defaultSlot) {
                $durationFld->checked = true;
            }
            foreach ($slabs as $slab) {
                foreach ($userTeachLangData as $uTeachLangId => $uTeachLang) {
                    $filedName = 'ustelgpr_price[' . $lessonDuration . '][' . $slab['minMaxKey'] . '][' . $uTeachLangId . ']';
                    $label = $filedName;
                    $fld = $frm->addFloatField($uTeachLang . ' ' . Label::getLabel('LBL_PRICE'), $filedName);
                    $fld->requirements()->setRange(1, 99999);
                    $fld->requirements()->setRequired(true);
                    $keyField = $uTeachLangId . '-' . $slab['minMaxKey'] . '-' . $lessonDuration;
                    if (!empty($userTeachingLang[$keyField]['ustelgpr_price']) && !$showAdminSlab) {
                        $fld->value = $userTeachingLang[$keyField]['ustelgpr_price'];
                    }
                    $durationFld->requirements()->addOnChangerequirementUpdate($lessonDuration, 'eq', $filedName, $fld->requirements());
                    $fieldRequirement = new FormFieldRequirement($filedName, $label);
                    $fieldRequirement->setRequired(false);
                    $fieldRequirement->setRange(0, 99999);
                    $durationFld->requirements()->addOnChangerequirementUpdate($lessonDuration, 'ne', $filedName, $fieldRequirement);
                }
            }
        }
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE'));
        $frm->addButton('', 'nextBtn', Label::getLabel('LBL_Next'));
        $frm->addButton('', 'backBtn', Label::getLabel('LBL_Back'));
        return $frm;
    }

    /**
     * Get Teacher Languages Form
     * 
     * @param int $langId
     * @param array $spokenLangs
     * @return Form
     */
    private function getTeacherLanguagesForm(int $langId, array $spokenLangs = []): Form
    {
        $teachLangs = TeachLanguage::getAllLangs($langId, true);
        $langArr = $spokenLangs ?: SpeakLanguage::getAllLangs($langId, true);
        $profArr = SpeakLanguage::getProficiencies();
        $db = FatApp::getDb();
        $userTeachLanguage = new UserTeachLanguage($this->siteUserId);
        $userTeachlangs = $userTeachLanguage->getSrchObject($this->siteLangId);
        $userTeachlangs->addCondition('utlang_user_id', '=', $this->siteUserId);
        $userTeachlangs->addMultiplefields(['utlang_tlang_id']);
        $userToTeachLangRows = $db->fetchAll($userTeachlangs->getResultSet(), 'utlang_tlang_id');
        $userToTeachLangRows = array_flip(array_keys($userToTeachLangRows));
        $userToLangSrch = new SearchBase('tbl_user_speak_languages');
        $userToLangSrch->addMultiplefields(['uslang_slang_id', 'uslang_proficiency']);
        $userToLangSrch->addCondition('uslang_user_id', '=', $this->siteUserId);
        $userToLangRs = $userToLangSrch->getResultSet();
        $spokenLangRows = $db->fetchAllAssoc($userToLangRs);
        $frm = new Form('frmTeacherLanguages');
        $frm->addCheckBoxes(Label::getLabel('LBL_LANGUAGE_TO_TEACH'), 'teach_lang_id', $teachLangs, array_keys($userToTeachLangRows))->requirements()->setRequired();
        $proficiencyLabel = stripslashes(Label::getLabel('LBL_I_DO_SPEAK_THIS_LANGUAGE'));
        $speekLangFieldLabel = Label::getLabel('LBL_LANGUAGE_I_SPEAK');
        $proficiencyFieldLabel = Label::getLabel('LBL_LANGUAGE_PROFICIENCY');
        foreach ($langArr as $key => $lang) {
            $speekLangField = $frm->addCheckBox($speekLangFieldLabel, 'uslang_slang_id[' . $key . ']', $key, ['class' => 'uslang_slang_id'], false, 0);
            $proficiencyField = $frm->addSelectBox($proficiencyFieldLabel, 'uslang_proficiency[' . $key . ']', $profArr, '', ['class' => 'uslang_proficiency select__dropdown'], $proficiencyLabel);
            if (array_key_exists($key, $spokenLangRows)) {
                $proficiencyField->value = $spokenLangRows[$key];
                $speekLangField->checked = true;
                $speekLangField->value = $key;
            }
            $proficiencyRequired = new FormFieldRequirement($proficiencyField->getName(), $proficiencyField->getCaption());
            $proficiencyRequired->setRequired(true);
            $proficiencyOptional = new FormFieldRequirement($proficiencyField->getName(), $proficiencyField->getCaption());
            $proficiencyOptional->setRequired(false);
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'gt', $proficiencyField->getName(), $proficiencyRequired);
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'le', $proficiencyField->getName(), $proficiencyOptional);
        }
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE'));
        $frm->addButton('', 'next_btn', Label::getLabel('LBL_Next'));
        $frm->addButton('', 'back_btn', Label::getLabel('LBL_Back'));
        return $frm;
    }

    /**
     * Delete User Teach Lang
     * 
     * @param array $langIds
     * @param type $error
     * @return bool
     */
    private function deleteUserTeachLang(array $langIds = [], &$error = ''): bool
    {
        $teacherId = $this->siteUserId;
        $teachLangPriceQuery = 'DELETE ' . UserTeachLanguage::DB_TBL . ', ustelgpr FROM ' .
                UserTeachLanguage::DB_TBL . ' LEFT JOIN ' . TeachLangPrice::DB_TBL .
                ' ustelgpr ON ustelgpr.ustelgpr_utlang_id = utlang_id WHERE utlang_user_id = ' . $teacherId;
        if (!empty($langIds)) {
            $langIds = implode(',', $langIds);
            $teachLangPriceQuery .= ' and utlang_tlang_id NOT IN (' . $langIds . ')';
        }
        $db = FatApp::getDb();
        $db->query($teachLangPriceQuery);
        if ($db->getError()) {
            $error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Delete User Speak Lang
     * 
     * @param array $langIds
     * @param type $error
     * @return bool
     */
    private function deleteUserSpeakLang(array $langIds = [], &$error = ''): bool
    {
        $teacherId = $this->siteUserId;
        $query = 'DELETE  FROM ' . UserSpeakLanguage::DB_TBL . ' WHERE uslang_user_id = ' . $teacherId;
        if (!empty($langIds)) {
            $langIds = implode(',', $langIds);
            $query .= ' and uslang_slang_id NOT IN (' . $langIds . ')';
        }
        $db = FatApp::getDb();
        $db->query($query);
        if ($db->getError()) {
            $error = $db->getError();
            return false;
        }
        return true;
    }

    /**
     * Get Teacher Preferences Form
     * 
     * @return Form
     */
    private function getTeacherPreferencesForm(): Form
    {
        $frm = new Form('teacherPreferencesFrm');
        $preferencesArr = Preference::getPreferencesArr($this->siteLangId);
        $titleArr = Preference::getPreferenceTypeArr($this->siteLangId);
        foreach ($preferencesArr as $key => $val) {
            if (empty($preferencesArr[$key])) {
                continue;
            }
            $optionsArr = array_column($preferencesArr[$key], 'prefer_title', 'prefer_id');
            if (isset($titleArr[$key])) {
                $frm->addCheckBoxes($titleArr[$key], 'pref_' . $key, $optionsArr, [], ['class' => 'list-onethird list-onethird--bg']);
            }
        }
        $frm->addButton('', 'btn_back', Label::getLabel('LBL_BACK'));
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE'));
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_NEXT'));
        return $frm;
    }

    /**
     * Get User Teach Lang Data
     * 
     * @return type
     */
    private function getUserTeachLangData()
    {
        $userTeachLanguage = new UserTeachLanguage($this->siteUserId);
        $userTeachlangs = $userTeachLanguage->getSrchObject($this->siteLangId, true);
        $userTeachlangs->doNotCalculateRecords();
        $userTeachlangs->addMultiplefields([
            'IFNULL(tlang_name, tlang_identifier) as teachLangName',
            'IFNULL(`ustelgpr_slot`, 0) as ustelgpr_slot', 'utlang_tlang_id',
            'ustelgpr_price', 'utlang_id', 'ustelgpr_min_slab', 'ustelgpr_max_slab',
            'CONCAT(IFNULL(ustelgpr_min_slab,0),"-",IFNULL(ustelgpr_max_slab,0)) as minMaxKey',
            'CONCAT(`utlang_id`, "-", IFNULL(`ustelgpr_min_slab`,0),"-", IFNULL(`ustelgpr_max_slab`,0), "-", IFNULL(`ustelgpr_slot`, 0)) as keyField',
        ]);
        return FatApp::getDb()->fetchAll($userTeachlangs->getResultSet(), 'keyField');
    }

    /**
     * Format Teacher Slabs
     * 
     * @param array $slabData
     * @return array
     */
    private function formatTeacherSlabs(array $slabData): array
    {
        $returnArray = [];
        foreach ($slabData as $key => $value) {
            if ($value['ustelgpr_min_slab'] > 0 && $value['ustelgpr_max_slab'] > $value['ustelgpr_min_slab']) {
                $returnArray[$value['minMaxKey']] = [
                    'minSlab' => $value['ustelgpr_min_slab'],
                    'maxSlab' => $value['ustelgpr_max_slab'],
                    'minMaxKey' => $value['minMaxKey'],
                ];
            }
        }
        return $returnArray;
    }

    /**
     * Get Search Form
     * 
     * @return Form
     */
    public static function getSearchForm(): Form
    {
        $frm = new Form('frmSrch');
        $frm->addHiddenField('', 'ordles_status', Lesson::SCHEDULED);
        $frm->addHiddenField('', 'ordles_lesson_starttime', date('Y-m-d H:i:s'));
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE);
        $frm->addHiddenField('', 'pageno', 1);
        $frm->addHiddenField('', 'view', AppConstant::VIEW_SHORT);
        return $frm;
    }

    /**
     * Get General Availability JSON Data
     *
     * @return void
     */
    public function generalAvblJson()
    {
        $availability = new Availability($this->siteUserId);
        FatUtility::dieJsonSuccess(['data' => $availability->getGeneral()]);
    }

    /**
     * Get General Availability JSON Data
     *
     * @return void
     */
    public function avalabilityJson()
    {
        $start = FatApp::getPostedData('start', FatUtility::VAR_STRING, '');
        $end = FatApp::getPostedData('end', FatUtility::VAR_STRING, '');
        if (empty($start) || empty($end)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $start = MyDate::formatToSystemTimezone($start);
        $end = MyDate::formatToSystemTimezone($end);
        $availability = new Availability($this->siteUserId);
        FatUtility::dieJsonSuccess(['data' => $availability->getAvailability($start, $end)]);
    }

}
