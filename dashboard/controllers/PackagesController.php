<?php

/**
 * Packages Controller is used for handling Packages
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class PackagesController extends DashboardController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {

        $frm = PackageSearch::getSearchForm();
        $frm->fill(FatApp::getQueryStringData());
        $this->set('setMonthAndWeekNames', true);
        $this->set('frm', $frm);
        $this->_template->addJs(['js/jquery.datetimepicker.js', 'js/moment.min.js']);
        $this->_template->render();
    }

    public function search()
    {
        $langId = $this->siteLangId;
        $userId = $this->siteUserId;
        $userType = $this->siteUserType;
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $frm = PackageSearch::getSearchForm();
        if (!$post = $frm->getFormDataFromArray($posts)) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $srch = new PackageSearch($langId, $userId, $userType);
        $srch->applySearchConditions($post);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addOrder('grpcls_id', 'DESC');
        $srch->setPageSize($post['pagesize']);
        $srch->setPageNumber($post['pageno']);
        $rows = $srch->fetchAndFormat();
        $this->sets([
            'post' => $post, 'packages' => $rows,
            'recordCount' => $srch->recordCount(),
            'myDate' => (new MyDate())->setMonthAndWeekNames(),
        ]);
        $this->_template->render(false, false);
    }

    public function form()
    {
        $packageId = FatApp::getPostedData('packageId', FatUtility::VAR_INT, 0);
        $frm = $this->getForm();
        if ($packageId > 0) {
            $groupClass = new GroupClass($packageId, $this->siteUserId, $this->siteUserType);
            if (!$record = $groupClass->getPackageToSave()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $file = new Afile(Afile::TYPE_GROUP_CLASS_BANNER);
            $this->set('banner', $file->getFile($packageId));
            $frm->fill($record);
        }
        $this->set('frm', $frm);
        $this->set('packageId', $packageId);
        $this->set('classes', PackageSearch::getClasses($packageId));
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $form = $this->getForm(true);
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData() + $_FILES)) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $packageId = FatUtility::int($post['grpcls_id']);
        if ($packageId > 0) {
            $groupClass = new GroupClass($packageId, $this->siteUserId, $this->siteUserType);
            if (!$record = $groupClass->getPackageToSave()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
        }
        $mindate = MyDate::formatToSystemTimezone(min($post['starttime']));
        if (time() > strtotime($mindate)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_START_TIME_MUST_WE_GRATHER_CURRENT_TIME'));
        }
        $maxdate = MyDate::formatToSystemTimezone(max($post['starttime']));
        $maxdate = strtotime($maxdate . " +" . $post['grpcls_duration'] . ' minutes');
        $package = array_merge($post, [
            'grpcls_parent' => 0,
            'grpcls_end_datetime' => date("Y-m-d H:i:s", $maxdate),
            'grpcls_start_datetime' => $mindate,
            'grpcls_duration' => $post['grpcls_duration'],
            'grpcls_type' => GroupClass::TYPE_PACKAGE,
            'grpcls_status' => GroupClass::SCHEDULED,
            'grpcls_teacher_id' => $this->siteUserId,
        ]);
        /* Insert|Updated Package Data */
        $db = FatApp::getDb();
        $db->startTransaction();
        $record = new GroupClass($packageId, $this->siteUserId, User::TEACHER);
        $record->assignValues($package);
        if (!$record->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($record->getError());
        }
        $packageNewId = $record->getMainTableRecordId();
        if (!empty($post['grpcls_banner']['name'])) {
            $file = new Afile(Afile::TYPE_GROUP_CLASS_BANNER);
            if (!$file->saveFile($post['grpcls_banner'], $packageNewId, true)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($file->getError());
            }
        }
        /* Insert|Update Package Classes Data  */
        $classes = [];
        $classCount = count($post['title']);
        $avail = new Availability($this->siteUserId);
        foreach ($post['title'] as $key => $title) {
            $slug = $post['grpcls_slug'] . '-' . $key;
            $starttime = $post['starttime'][$key];
            $starttime = MyDate::formatToSystemTimezone($post['starttime'][$key]);
            $endtime = date("Y-m-d H:i:s", strtotime($starttime . ' +' . $post['grpcls_duration'] . ' minutes'));
            if (!$avail->isUserAvailable($starttime, $endtime, $key)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($avail->getError());
            }
            $class = [
                'grpcls_title' => $title,
                'grpcls_slug' => $slug,
                'grpcls_parent' => $packageNewId,
                'grpcls_end_datetime' => $endtime,
                'grpcls_start_datetime' => $starttime,
                'grpcls_teacher_id' => $this->siteUserId,
                'grpcls_status' => GroupClass::SCHEDULED,
                'grpcls_type' => GroupClass::TYPE_REGULAR,
                'grpcls_tlang_id' => $post['grpcls_tlang_id'],
                'grpcls_duration' => $post['grpcls_duration'],
                'grpcls_total_seats' => $post['grpcls_total_seats'],
                'grpcls_entry_fee' => $post['grpcls_entry_fee'] / $classCount,
                'grpcls_added_on' => date('Y-m-d H:i:s'),
            ];
            if ($packageId > 0) {
                $class['grpcls_id'] = $key;
            }
            $record = new TableRecord(GroupClass::DB_TBL);
            $record->assignValues($class);
            if (!$record->addNew([], $class)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($record->getError());
            }
            if (0 >= $packageId) {
                $class['grpcls_id'] = $record->getId();
            }
            $classes[] = $class;
            $meetingTool = new Meeting(0, 0);
            $meetingTool->checkLicense($class['grpcls_start_datetime'], $class['grpcls_end_datetime'], $class['grpcls_duration']);
        }
        $db->commitTransaction();
        $languages = Language::getCodes();
        $newTabLangId = 0;
        foreach ($languages as $id => $code) {
            if (empty(GroupClass::getAttrByLangId($id, $packageNewId))) {
                $newTabLangId = $id;
                break;
            }
        }
        $this->addGoogleEvents($classes, $packageId);
        FatUtility::dieJsonSuccess([
            'packageId' => $packageNewId, 'langId' => $newTabLangId,
            'msg' => Label::getLabel('LBL_PACKAGE_SETUP_SUCCESSFULLY')
        ]);
    }

    public function langForm()
    {
        $langId = FatApp::getPostedData('langId', FatUtility::VAR_INT, 0);
        $packageId = FatApp::getPostedData('packageId', FatUtility::VAR_INT, 0);
        if (empty($langId) || empty($packageId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $groupClassObj = new GroupClass($packageId, $this->siteUserId, $this->siteUserType);
        if (!$groupClass = $groupClassObj->getPackageToSave($langId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm();
        if ($langData = GroupClass::getAttrByLangId($langId, $packageId)) {
            $langFrm->fill($langData);
        } else {
            $groupClass['gclang_lang_id'] = $langId;
            $groupClass['gclang_grpcls_id'] = $packageId;
            $langFrm->fill($groupClass);
        }
        $this->set('frm', $langFrm);
        $this->set('tabLangId', $langId);
        $this->set('packageId', $packageId);
        $this->set('classes', PackageSearch::getClasses($packageId, $langId));
        $this->set('languages', Language::getAllNames(false));
        $this->_template->render(false, false);
    }

    public function setupLang()
    {
        $form = $this->getLangForm();
        if (!$post = $form->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        /* Insert|Update Package Language Data */
        $db = FatApp::getDb();
        $db->startTransaction();
        $packageId = FatUtility::int($post['gclang_grpcls_id']);
        $record = new GroupClass($packageId, $this->siteUserId, $this->siteUserType);
        if (!$record->saveLangData($post)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($record->getError());
        }
        /* Insert|Update Package Classes Language Data */
        foreach ($post['title'] as $key => $title) {
            $data = [
                'gclang_lang_id' => $post['gclang_lang_id'],
                'grpcls_title' => $title, 'gclang_grpcls_id' => $key
            ];
            $record = new TableRecord(GroupClass::DB_TBL_LANG);
            $record->assignValues($data);
            if (!$record->addNew([], $data)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($record->getError());
            }
        }
        $db->commitTransaction();
        $languages = Language::getCodes();
        $newTabLangId = 0;
        foreach ($languages as $id => $code) {
            if (empty(GroupClass::getAttrByLangId($id, $packageId))) {
                $newTabLangId = $id;
                break;
            }
        }
        FatUtility::dieJsonSuccess([
            'packageId' => $packageId, 'langId' => $newTabLangId,
            'msg' => Label::getLabel('LBL_PACKAGE_SETUP_SUCCESSFULLY')
        ]);
    }

    public function cancelSetup()
    {
        $packageId = FatApp::getPostedData('packageId', FatUtility::VAR_INT, 0);
        if ($this->siteUserType == User::LEARNER) {
            $class = new OrderPackage(0, $this->siteUserId, $this->siteUserType);
            $record = $class->cancelPackage($packageId, $this->siteLangId);
        } else {
            $class = new GroupClass($packageId, $this->siteUserId, $this->siteUserType);
            $record = $class->cancelPackage();
        }
        if (!$record) {
            FatUtility::dieJsonError($class->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel("LBL_PACKAGE_CANCELLED_SUCCESSFULLY!"));
    }

    private function getForm()
    {
        $userTeachLangs = new UserTeachLanguage($this->siteUserId);
        $srch = $userTeachLangs->getSrchObject($this->siteLangId);
        $srch->doNotCalculateRecords();
        $srch->addMultiplefields(['tlang_id', 'IFNULL(tlang_name, tlang_identifier) as tlang_name']);
        $teachLangData = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        $form = new Form('frmPackage');
        $form->addHiddenField('', 'grpcls_id')->requirements()->setIntPositive();
        $fld = $form->addRequiredField(Label::getLabel('LBL_TITLE'), 'grpcls_title');
        $fld->requirements()->setLength(10, 100);
        $fld = $form->addTextBox(Label::getLabel('LBL_SLUG'), 'grpcls_slug');
        $fld->setUnique(GroupClass::DB_TBL, 'grpcls_slug', 'grpcls_id', 'grpcls_id', 'grpcls_id');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setLength(10, 100);
        $form->addFileUpload(Label::getLabel('LBL_CLASS_BANNER'), 'grpcls_banner');
        $fld = $form->addTextArea(Label::getLabel('LBL_DESCRIPTION'), 'grpcls_description');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setLength(10, 1000);
        $fld = $form->addSelectBox(Label::getLabel('LBL_LANGUAGE'), 'grpcls_tlang_id', $teachLangData);
        $fld->requirements()->setRequired(true);
        $fld = $form->addIntegerField(Label::getLabel('LBL_MAX_LEARNERS'), 'grpcls_total_seats');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setRange(1, FatApp::getConfig('CONF_GROUP_CLASS_MAX_LEARNERS'));
        $currencyCode = MyUtility::getSystemCurrency()['currency_code'];
        $fld = $form->addFloatField(str_replace('{currency}', $currencyCode, Label::getLabel('LBL_ENTRY_FEE_[{currency}]')), 'grpcls_entry_fee');
        $fld->requirements()->setPositive(true);
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setRange(1, 9999);
        $fld = $form->addSelectBox(Label::getLabel('LBL_EACH_CLASS_(MINUTES)'), 'grpcls_duration', AppConstant::fromatClassSlots());
        $fld->requirements()->setRequired(true);
        $form->addRequiredField(Label::getLabel('LBL_CLASS_TITLE'), 'title[]')->requirements()->setLength(10, 100);
        $starttime = $form->addRequiredField(Label::getLabel('LBL_START_TIME'), 'starttime[]', '', ['class' => 'datetime', 'autocomplete' => 'off', 'readonly' => 'readonly']);
        $starttime->requirements()->setRegularExpressionToValidate(AppConstant::DATE_TIME_REGEX);
        $form->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $form;
    }

    private function getLangForm()
    {
        $frm = new Form('frmPackageLang');
        $fld = $frm->addHiddenField('', 'gclang_grpcls_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive(true);
        $fld = $frm->addHiddenField('', 'gclang_lang_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive(true);
        $fld = $frm->addRequiredField(Label::getLabel('LBL_TITLE'), 'grpcls_title');
        $fld->requirements()->setLength(10, 100);
        $fld = $frm->addTextArea(Label::getLabel('LBL_DESCRIPTION'), 'grpcls_description');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setLength(10, 1000);
        $frm->addRequiredField(Label::getLabel('LBL_CLASS_TITLE'), 'title[]')->requirements()->setLength(10, 100);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    private function addGoogleEvents(array $classes, int $packageId)
    {
        $googleEvents = [];
        if ($packageId > 0) {
            $classIds = array_column($classes, 'grpcls_id');
            $googleEvents = $this->getEvents($classIds);
        }
        $token = (new UserSetting($this->siteUserId))->getGoogleToken();
        foreach ($classes as $key => $class) {
            $googleCalendar = new GoogleCalendarEvent($this->siteUserId, $class['grpcls_id'], AppConstant::GCLASS);
            if (!empty($googleEvents[$class['grpcls_id']])) {
                $googleCalendar->deletEvent($token, $googleEvents[$class['grpcls_id']]['gocaev_event_id']);
            }
            $class['google_token'] = $token;
            $class['grpcls_description'] = '';
            $googleCalendar->addClassEvent($class, User::TEACHER);
        }
    }

    private function getEvents(array $recordIds)
    {
        if (empty($recordIds)) {
            return [];
        }
        $srch = new SearchBase(GoogleCalendarEvent::DB_TBL, 'gocaev');
        $srch->addMultipleFields(['gocaev.*']);
        $srch->addCondition('gocaev.gocaev_record_id', 'IN', $recordIds);
        $srch->addCondition('gocaev.gocaev_record_type', '=', AppConstant::GCLASS);
        $srch->addCondition('gocaev.gocaev_user_id', '=', $this->siteUserId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'gocaev_record_id');
    }
}
