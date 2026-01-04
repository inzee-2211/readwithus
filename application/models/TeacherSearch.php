<?php

/**
 * This class is used to handle Teacher Search
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class TeacherSearch extends YocoachSearch
{

    /**
     * Initialize Teacher Search
     * 
     * @param int $langId
     * @param int $userId
     * @param int $userType
     */
    public function __construct(int $langId, int $userId, int $userType)
    {
        $this->table = 'tbl_users';
        $this->alias = 'teacher';
        parent::__construct($langId, $userId, $userType);
        $this->joinTable('tbl_teacher_stats', 'LEFT JOIN', 'testat.testat_user_id = teacher.user_id', 'testat');
    }

    /**
     * Get Listing Fields
     * 
     * @return array
     */
    public static function getListingFields(): array
    {
        return [
            'teacher.user_id' => 'user_id',
            'teacher.user_username' => 'user_username',
            'teacher.user_first_name' => 'user_first_name',
            'teacher.user_last_name' => 'user_last_name',
            'teacher.user_country_id' => 'user_country_id',
            'testat.testat_students' => 'testat_students',
            'testat.testat_lessons' => 'testat_lessons',
            'testat.testat_classes' => 'testat_classes',
            'testat.testat_ratings' => 'testat_ratings',
            'testat.testat_reviewes' => 'testat_reviewes',
            'testat.testat_minprice' => 'testat_minprice',
            'testat.testat_maxprice' => 'testat_maxprice',
        ];
    }

    /**
     * Apply Primary Conditions
     * 
     * @return void
     */
public function applyPrimaryConditions(): void
{
    // keep only safety/validity filters
    $this->addDirectCondition('teacher.user_deleted IS NULL');
    $this->addCondition('teacher.user_active', '=', AppConstant::ACTIVE);
    $this->addCondition('teacher.user_is_teacher', '=', AppConstant::YES);

    // keep country requirement if you need flags/locations
    $this->addCondition('teacher.user_country_id', '>', AppConstant::NO);

    // OPTIONAL: if you want to show unverified teachers too, remove this
    $this->addDirectCondition('teacher.user_verified IS NOT NULL');

    // ❌ remove these “profile completion” restrictions
    // $this->addCondition('testat.testat_teachlang', '=', AppConstant::YES);
    // $this->addCondition('testat.testat_speaklang', '=', AppConstant::YES);
    // $this->addCondition('testat.testat_preference', '=', AppConstant::YES);
    // $this->addCondition('testat.testat_availability', '=', AppConstant::YES);
    // $this->addCondition('testat.testat_qualification', '=', AppConstant::YES);

    // ❌ remove username requirement (we’ll handle link fallback)
    $this->addCondition('teacher.user_username', '!=', "");
}


    /**
     * Apply Search Conditions
     * 
     * @param array $post
     * @return void
     */
    public function applySearchConditions(array $post): void
    {
        /* Keyword */
        if (!empty(trim($post['keyword']))) {
            $fullName = 'mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)';
            $this->addCondition($fullName, 'LIKE', '%' . trim($post['keyword']) . '%', 'AND', true);
        }
        /* Teach Language */
        if (count($post['teachs'])) {
            $srch = new SearchBase(UserTeachLanguage::DB_TBL, 'utlang');
            $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', ' ustelgpr.ustelgpr_utlang_id = utlang.utlang_id', 'ustelgpr');
            $srch->addDirectCondition('utlang_tlang_id IN(' . implode(",", FatUtility::int($post['teachs'])) . ')');
            $srch->addFld('DISTINCT utlang_user_id as utlang_user_id');
            $srch->addCondition('ustelgpr_price', '>', 0);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'utlang.utlang_user_id = teacher.user_id', 'utlang');
        }
        /* Speak Language */
        if (count($post['speaks'])) {
            $srch = new SearchBase(UserSpeakLanguage::DB_TBL);
            $srch->addFld('DISTINCT uslang_user_id as uslang_user_id');
            $srch->addDirectCondition('uslang_slang_id IN (' . implode(',', FatUtility::int($post['speaks'])) . ')');
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'utsl.uslang_user_id = teacher.user_id', 'utsl');
        }
        /* Price */
        if (count($post['price'])) {
            $pricesql = [];
            foreach ($post['price'] as $price) {
                $range = AppConstant::getPriceRange(FatUtility::int($price));
                array_push($pricesql, '(testat.testat_maxprice >= ' . MyUtility::convertToSystemCurrency($range[0]) .
                        ' AND testat.testat_minprice <= ' . MyUtility::convertToSystemCurrency($range[1]) . ')');
            }
            $this->addDirectCondition(' ( ' . implode(' OR ', $pricesql) . ' ) ');
        }
        if (!empty($post['price_from']) & !empty($post['price_till'])) {
            $this->addCondition('testat.testat_maxprice', '>=', $post['price_from']);
            $this->addCondition('testat.testat_minprice', '<=', $post['price_till']);
        } elseif (!empty($post['price_from'])) {
            $this->addCondition('testat.testat_minprice', '>=', $post['price_from']);
        } elseif (!empty($post['price_till'])) {
            $this->addCondition('testat.testat_maxprice', '<=', $post['price_till']);
        }
        /* Week Day and Time Slot */
        $weekDays = (array) ($post['days'] ?? []);
        $timeSlots = (array) ($post['slots'] ?? []);
        if (count($weekDays) > 0 || count($timeSlots) > 0) {
            $weekDays = (empty($weekDays)) ? [0, 1, 2, 3, 4, 5, 6] : $weekDays;
            $timeSlotArr = (!empty($timeSlots)) ? MyUtility::formatTimeSlotArr($timeSlots) : [];
            $srch = new SearchBase(Availability::DB_TBL_GENERAL);
            $srch->addFld('DISTINCT gavail_user_id as gavail_user_id');
            $weekDates = MyDate::changeWeekDaysToDate($weekDays, $timeSlotArr);
            $condition = ' ( ';
            foreach ($weekDates as $weekDayKey => $date) {
                $condition .= ($weekDayKey == 0) ? '' : ' OR ';
                $condition .= ' ( `gavail_starttime` < "' . $date['endDate'] . '" and `gavail_endtime` > "' . $date['startDate'] . '" ) ';
            }
            $condition .= ' ) ';
            $srch->addDirectCondition($condition);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'gavail.gavail_user_id = teacher.user_id', 'gavail');
        }
        /* Location Country */
        if (!empty($post['locations'])) {
            $this->addDirectCondition('teacher.user_country_id IN (' . implode(',', $post['locations']) . ')');
        }
        /* Preferences Filter (Teacher’s accent, Teaches level, Subjects, Test preparations, Lesson includes, Learner’s age group) */
        $preferences = array_merge($post['accents'], $post['levels'], $post['subjects'], $post['lesson_type'], $post['tests'], $post['age_group']);
        if (count($preferences) > 0) {
            $srch = new SearchBase('tbl_user_preferences');
            $srch->addFld('DISTINCT uprefer_user_id as uprefer_user_id');
            $srch->addCondition('uprefer_prefer_id', 'IN', $preferences);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'utpref.uprefer_user_id = teacher.user_id', 'utpref');
        }
        /* Tutor Gender */
        if (count($post['gender']) == 1) {
            $this->addCondition('teacher.user_gender', '=', current($post['gender']));
        }
    }

    /**
     * Apply Order By
     * 
     * @param int $sorting
     * @return void
     */
    public function applyOrderBy($sorting): void
    {
        switch ($sorting) {
            case AppConstant::SORT_PRICE_ASC:
                $this->addOrder('testat.testat_minprice', 'ASC');
                break;
            case AppConstant::SORT_PRICE_DESC:
                $this->addOrder('testat.testat_minprice', 'DESC');
                break;
            default:
                $this->addOrder('testat.testat_students', 'DESC');
                $this->addOrder('testat.testat_lessons', 'DESC');
                $this->addOrder('testat.testat_reviewes', 'DESC');
                $this->addOrder('testat.testat_ratings', 'DESC');
                break;
        }
    }

    /**
     * Fetch And Format
     * 
     * @param bool $viewPage
     * @return array
     */
    public function fetchAndFormat(bool $viewPage = false): array
    {
        $records = FatApp::getDb()->fetchAll($this->getResultSet());
        if (count($records) == 0) {
            return [];
        }
        $teacherIds = array_column($records, 'user_id');
        $countryIds = array_column($records, 'user_country_id');
        $countries = static::getCountryNames($this->langId, $countryIds);
        $favorites = static::getFavoriteTeachers($this->userId, $teacherIds);
        $langData = static::getTeachersLangData($this->langId, $teacherIds);
        $teachLangs = static::getTeachLangs($this->langId, $teacherIds);
        $speakLangs = static::getSpeakLangs($this->langId, $teacherIds);
        $timeslots = static::getTimeslots($this->userId, $teacherIds);
        $photos = static::getProfilePhotos($teacherIds);
        $videos = static::getYouTubeVideos($teacherIds);
        $userProfileImages = static::getUserProfileImages($teacherIds);
        $offers = OfferPrice::getOffers($this->userId, $teacherIds);
        $courses = static::getCourses($teacherIds);
        foreach ($records as $key => $record) {
            $record['uft_id'] = $favorites[$record['user_id']] ?? 0;
            $record['user_photo'] = $photos[$record['user_id']] ?? '';
            $record['courses'] = $courses[$record['user_id']] ?? 0;
            $record['user_biography'] = $langData[$record['user_id']] ?? '';
            $record['user_country_name'] = $countries[$record['user_country_id']]['name'] ?? '';
            $record['user_country_code'] = $countries[$record['user_country_id']]['code'] ?? '';
            $record['teacherTeachLanguageName'] = $teachLangs[$record['user_id']] ?? '';
            $record['spoken_language_names'] = $speakLangs[$record['user_id']]['slang_name'] ?? '';
            $record['spoken_languages_proficiency'] = $speakLangs[$record['user_id']]['uslang_proficiency'] ?? '';
            $record['offers'] = $offers[$record['user_id']]['lesson'] ?? [];
            $record['testat_timeslots'] = [];
            if (!$viewPage) {
                $record['testat_timeslots'] = $timeslots[$record['user_id']] ?? AppConstant::getEmptyDaySlots();
            }
            $record['user_video_link'] = MyUtility::validateYoutubeUrl($videos[$record['user_id']]);
            $record['userProfileImageId'] = $userProfileImages[$record['user_id']]['file_id'] ?? 0;
            $records[$key] = $record;
        }
        return $records;
    }

    public static function getCourses(array $teacherIds)
    {
        if (empty($teacherIds)) {
            return [];
        }
        $srch = new CourseSearch(0, 0, 0);
        $srch->addMultipleFields(['course_user_id', 'COUNT(course.course_id) AS courses']);
        $srch->applyPrimaryConditions();
        $srch->addCondition('course_user_id', 'IN', $teacherIds);
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $srch->addGroupBy('course_user_id');
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get Profile Photos
     * 
     * @param array $teacherIds
     * @return array
     */
    public static function getProfilePhotos(array $teacherIds): array
    {
        if (count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(Afile::DB_TBL);
        $srch->addMultipleFields(['file_record_id', 'file_name']);
        $srch->addCondition('file_type', '=', Afile::TYPE_USER_PROFILE_IMAGE);
        $srch->addCondition('file_record_id', 'IN', $teacherIds);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get YouTube Videos
     * 
     * @param array $teacherIds
     * @return array
     */
    public static function getYouTubeVideos(array $teacherIds): array
    {
        if (count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(UserSetting::DB_TBL);
        $srch->addCondition('user_id', 'In', $teacherIds);
        $srch->addMultipleFields(['user_id', 'user_video_link']);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get Countries Names
     * 
     * @param int $langId
     * @param array $countryIds
     * @return array
     */
    public static function getCountryNames(int $langId, array $countryIds): array
    {
        if ($langId == 0 || count($countryIds) == 0) {
            return [];
        }
        $srch = new SearchBase(Country::DB_TBL, 'country');
        $on = 'clang.countrylang_country_id = country.country_id AND clang.countrylang_lang_id = ' . $langId;
        $srch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', $on, 'clang');
        $srch->addMultipleFields(['country_id', 'country_code',
            'IFNULL(country_name, country_identifier) AS country_name']);
        $srch->addCondition('country.country_id', 'IN', $countryIds);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($result);
        $countries = [];
        foreach ($rows as $row) {
            $countries[$row['country_id']] = ['code' => $row['country_code'], 'name' => $row['country_name']];
        }
        return $countries;
    }

    /**
     * Get Teachers LangData
     * 
     * @param int $langId
     * @param array $teacherIds
     * @return array
     */
    public static function getTeachersLangData(int $langId, array $teacherIds): array
    {
        if ($langId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_users_lang', 'userlang');
        $srch->addCondition('userlang.userlang_lang_id', '=', $langId);
        $srch->addCondition('userlang.userlang_user_id', 'IN', $teacherIds);
        $srch->addMultipleFields(['userlang_user_id', 'user_biography']);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    /**
     * Get Favorite Teachers
     * 
     * @param int $userId
     * @param array $teacherIds
     * @return array
     */
    public static function getFavoriteTeachers(int $userId, array $teacherIds): array
    {
        if ($userId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_user_favourite_teachers', 'uft');
        $srch->addCondition('uft.uft_teacher_id', 'IN', $teacherIds);
        $srch->addCondition('uft.uft_user_id', '=', $userId);
        $srch->addMultipleFields(['uft_teacher_id', 'uft_id']);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    /**
     * Get Teachers Teach Lang
     * 
     * @param int $langId
     * @param array $teacherIds
     * @return array
     */
    public static function getTeachLangs(int $langId, array $teacherIds): array
    {
        if ($langId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(UserTeachLanguage::DB_TBL, 'utlang');
        $srch->joinTable(TeachLanguage::DB_TBL, 'INNER JOIN', 'tlang.tlang_id = utlang.utlang_tlang_id', 'tlang');
        $srch->joinTable(TeachLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanglang.tlanglang_tlang_id = tlang.tlang_id and tlanglang.tlanglang_lang_id = ' . $langId, 'tlanglang');
        $srch->addMultipleFields(['utlang.utlang_user_id', 'GROUP_CONCAT(IFNULL(tlang_name, tlang_identifier) SEPARATOR ", ") as tlang_name']);
        $srch->addCondition('utlang.utlang_user_id', 'IN', $teacherIds);
        $srch->addGroupBy('utlang.utlang_user_id');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get Teachers Speak Lang
     * 
     * @param int $langId
     * @param array $teacherIds
     * @return array
     */
    public static function getSpeakLangs(int $langId, array $teacherIds): array
    {
        if ($langId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(UserSpeakLanguage::DB_TBL, 'uslang');
        $srch->joinTable(SpeakLanguage::DB_TBL, 'INNER JOIN', 'slang.slang_id = uslang.uslang_slang_id', 'slang');
        $srch->joinTable(SpeakLanguage::DB_TBL_LANG, 'LEFT JOIN', 'slanglang.slanglang_slang_id = slang.slang_id and slanglang.slanglang_lang_id = ' . $langId, 'slanglang');
        $srch->addMultipleFields(['uslang.uslang_user_id', 'GROUP_CONCAT(uslang.uslang_proficiency) as uslang_proficiency', 'GROUP_CONCAT(IFNULL(slanglang.slang_name, slang_identifier) SEPARATOR ", ") as slang_name']);
        $srch->addCondition('uslang.uslang_user_id', 'IN', $teacherIds);
        $srch->addGroupBy('uslang.uslang_user_id');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'uslang_user_id');
    }

    /**
     * Get User Profile Images
     *
     * @param array $teacherIds
     * @return array
     */
    public static function getUserProfileImages(array $teacherIds): array
    {
        if (count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(Afile::DB_TBL, 'file');
        $srch->addCondition('file.file_type', '=', Afile::TYPE_USER_PROFILE_IMAGE);
        $srch->addCondition('file.file_path', '!=', '');
        $srch->addCondition('file.file_record_id', 'IN', $teacherIds);
        $srch->addMultipleFields(['file.file_id', 'file.file_record_id']);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($result, 'file_record_id');
    }

    /**
     * Get Available Time Slots
     * 
     * @param array $teacherIds
     * @return array
     */
    public static function getTimeslots(int $userId, array $teacherIds): array
    {
        if (count($teacherIds) == 0) {
            return [];
        }
        $startAndEndDate = MyDate::getStartEndDate(MyDate::TYPE_THIS_WEEK);
        $weekDiff = MyDate::weekDiff(Availability::GENERAL_WEEKSTART, $startAndEndDate['startDate']);
        $srch = new SearchBase(Availability::DB_TBL_GENERAL, 'gavail');
        $srch->addMultipleFields(['gavail.*', 'user_timezone']);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'user.user_id = gavail_user_id', 'user');
        $srch->addCondition('gavail_user_id', 'IN', $teacherIds);
        $srch->addOrder('gavail_starttime', 'ASC');
        $resultSet = $srch->getResultSet();
        $users = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $teacherTimeZone = (empty($user[0]['user_timezone'])) ? MyUtility::getSystemTimezone() : $user[0]['user_timezone'];
            $startDate = strtotime($row['gavail_starttime'] . ' + ' . $weekDiff . ' weeks');
            $endDate = strtotime($row['gavail_endtime'] . ' + ' . $weekDiff . ' weeks');
            if (MyDate::isDST(date('Y-m-d H:i:s', $startDate), $teacherTimeZone)) {
                $startDate = strtotime('-1 hours', $endDate);
            }
            if (MyDate::isDST(date('Y-m-d H:i:s', $endDate), $teacherTimeZone)) {
                $endDate = strtotime('-1 hours', $endDate);
            }
            $row['gavail_starttime'] = MyDate::formatDate(date('Y-m-d H:i:s', $startDate));
            $row['gavail_endtime'] = MyDate::formatDate(date('Y-m-d H:i:s', $endDate));
            $users[$row['gavail_user_id']][] = $row;
        }
        $timeSlots = [
            ['00:00:00', '04:00:00'], ['04:00:00', '08:00:00'],
            ['08:00:00', '12:00:00'], ['12:00:00', '16:00:00'],
            ['16:00:00', '20:00:00'], ['20:00:00', '00:00:00'],
        ];
        $userTimeslots = [];
        $emptySlots = AppConstant::getEmptyDaySlots();
        foreach ($users as $id => $user) {
            $userTimeslots[$id] = $emptySlots;
            foreach ($user as $availability) {
                $startTime = strtotime($availability['gavail_starttime']);
                $endTime = strtotime($availability['gavail_endtime']);
                for ($i = 0; $i <= 6; $i++) {
                    $dayStartTime = strtotime($startAndEndDate['startDate'] . " +" . $i . " days");
                    $dayEndTime = strtotime($startAndEndDate['startDate'] . " +" . ($i + 1) . " days");
                    if ($startTime >= $dayEndTime || $endTime <= $dayStartTime) {
                        continue;
                    }
                    foreach ($timeSlots as $index => $slot) {
                        $slotStartTime = strtotime(date('Y-m-d', $dayStartTime) . ' ' . $slot[0]);
                        $slotEndTime = strtotime(date('Y-m-d', $dayStartTime) . ' ' . $slot[1]);
                        if ($slot[1] == "00:00:00") {
                            $slotEndTime = $dayEndTime;
                        }
                        if ($startTime >= $slotEndTime || $endTime <= $slotStartTime) {
                            continue;
                        }
                        $startDateTime = max($slotStartTime, $startTime);
                        $endDateTime = min($slotEndTime, $endTime);
                        $userTimeslots[$id][$i][$index] += ($endDateTime - $startDateTime);
                    }
                }
            }
        }
        return $userTimeslots;
    }

    /**
     * Get Record Count
     * 
     * @return int
     */
    public function getRecordCount(): int
    {
        $db = FatApp::getDb();
        $order = $this->order;
        $page = $this->page;
        $pageSize = $this->pageSize;
        $this->limitRecords = false;
        $this->order = [];
        $qry = $this->getQuery() . ' LIMIT ' . SEARCH_MAX_COUNT . ', 1';
        if ($db->totalRecords($db->query($qry)) > 0) {
            $recordCount = SEARCH_MAX_COUNT;
        } else {
            if (empty($this->groupby) && empty($this->havings)) {
                $this->addFld('COUNT(user_id) AS total');
                $rs = $db->query($this->getQuery());
            } else {
                $this->addFld('user_id as user_id');
                $rs = $db->query('SELECT COUNT(user_id) AS total FROM (' . $this->getQuery() . ') t');
            }
            $recordCount = FatUtility::int($db->fetch($rs)['total'] ?? 0);
        }
        $this->order = $order;
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->limitRecords = true;
        return $recordCount;
    }

    /**
     * Remove All Conditions
     * 
     * @return void
     */
    public function removeAllConditions(): void
    {
        $this->conditions = [];
    }

    /**
     * Join setting Table
     * 
     * @return void
     */
    public function joinSettingTabel(): void
    {
        $this->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'us.user_id = teacher.user_id', 'us');
    }

    /**
     * Get Search Form
     * 
     * @param int $langId
     * @return Form
     */
    public static function getSearchForm(int $langId): Form
    {
        $preferences = Preference::getOptions($langId);
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_SEARCH_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_BY_TEACHER_NAME')]);
        $frm->addCheckBoxes(Label::getLabel('LBL_LANGUAGES'), 'teachs', TeachLanguage::getAllLangs($langId, true));
        $frm->addCheckBoxes(Label::getLabel('LBL_PRICE'), 'price', AppConstant::getPriceRangeOptions());
        $frm->addTextBox(Label::getLabel('LBL_PRICE_FROM'), 'price_from', '', ['placeholder' => Label::getLabel('LBL_FROM')]);
        $frm->addTextBox(Label::getLabel('LBL_PRICE_TILL'), 'price_till', '', ['placeholder' => Label::getLabel('LBL_TILL')]);
        $frm->addCheckBoxes(Label::getLabel('LBL_WEEK_DAYS'), 'days', AppConstant::getWeekDays());
        $frm->addCheckBoxes(Label::getLabel('LBL_DAY_SLOTS'), 'slots', MyUtility::timeSlotArr());
        $frm->addCheckBoxes(Label::getLabel('LBL_LOCATION'), 'locations', Country::getNames($langId));
        $frm->addCheckBoxes(Label::getLabel('LBL_SPEAKS'), 'speaks', SpeakLanguage::getOptions($langId));
        $frm->addCheckBoxes(Label::getLabel('LBL_GENDER'), 'gender', AppConstant::getGenders());
        $frm->addCheckBoxes(Label::getLabel('LBL_ACCENTS'), 'accents', $preferences[Preference::TYPE_ACCENTS]);
        $frm->addCheckBoxes(Label::getLabel('LBL_LEVELS'), 'levels', $preferences[Preference::TYPE_TEACHES_LEVEL]);
        $frm->addCheckBoxes(Label::getLabel('LBL_SUBJECTS'), 'subjects', $preferences[Preference::TYPE_SUBJECTS]);
        $frm->addCheckBoxes(Label::getLabel('LBL_LESSON_TYPE'), 'lesson_type', $preferences[Preference::TYPE_LESSONS]);
        $frm->addCheckBoxes(Label::getLabel('LBL_TESTS'), 'tests', $preferences[Preference::TYPE_TEST_PREPARATIONS]);
        $frm->addCheckBoxes(Label::getLabel('LBL_AGE_GROUP'), 'age_group', $preferences[Preference::TYPE_LEARNER_AGES]);
        $frm->addRadioButtons(Label::getLabel('LBL_SORT_BY'), 'sorting', AppConstant::getSortbyArr(), AppConstant::SORT_POPULARITY);
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        $frm->addHiddenField(Label::getLabel('LBL_LANG_SLUG'), 'langslug', '');
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnSubmit->attachField($frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear')));
        return $frm;
    }
}
