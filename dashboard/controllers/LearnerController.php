<?php
if (!function_exists('app_require')) {
    function app_require(string $relativePath): void
    {
        // Normalize incoming relative path and block traversal
        $rel = ltrim(str_replace(['\\', '//'], '/', $relativePath), '/');
        if (strpos($rel, '..') !== false) {
            throw new InvalidArgumentException('Invalid path: traversal not allowed.');
        }

        // Figure out project root and common bases
        $dashboardDir = realpath(__DIR__ . '/..');          // .../dashboard
        $projectRoot  = $dashboardDir ? realpath($dashboardDir . '/..') : null; // repo root
        $application  = $projectRoot ? $projectRoot . '/application' : null;

        // Build candidate bases to try (in order)
        $bases = [];
        if ($application && is_dir($application)) $bases[] = $application;
        if ($projectRoot && is_dir($projectRoot)) $bases[] = $projectRoot;

        // Consider CONF_APPLICATION_PATH only if it’s a dir
        if (defined('CONF_APPLICATION_PATH') && is_dir(CONF_APPLICATION_PATH)) {
            $bases[] = rtrim(CONF_APPLICATION_PATH, "/\\");
            // Also try the parent of CONF_APPLICATION_PATH (sometimes points to project root)
            $bases[] = rtrim(dirname(CONF_APPLICATION_PATH), "/\\");
        }

        // Try both the raw relative and an application-prefixed variant
        $relatives = [$rel];
        if (strpos($rel, 'application/') !== 0) {
            $relatives[] = 'application/' . $rel;
        }

        // Attempt in order; return on first hit
        $attempted = [];
        foreach ($bases as $base) {
            foreach ($relatives as $r) {
                $full = rtrim($base, "/\\") . '/' . $r;
                $attempted[] = $full;
                if (is_file($full)) {
                    require_once $full;
                    return;
                }
            }
        }

        // Final: also try relative to THIS file’s grandparent (as a hard fallback)
        $fallback = realpath(__DIR__ . '/../../') ?: null; // project root guess
        if ($fallback) {
            foreach ($relatives as $r) {
                $full = rtrim($fallback, "/\\") . '/' . $r;
                $attempted[] = $full;
                if (is_file($full)) {
                    require_once $full;
                    return;
                }
            }
        }

        // No luck – show all attempts for quick diagnosis
        throw new RuntimeException(
            "Required file not found. Tried:\n- " . implode("\n- ", $attempted)
        );
    }
}
app_require('library/services/SubscriptionEnrollment.php');

/**
 * Learner Controller is used for handling Learners
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class LearnerController extends DashboardController
{

    /**
     * Initialize Learner
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        MyUtility::setUserType(User::LEARNER);
        parent::__construct($action);
    }

    /**
     * Render Learner's Dashboard Homepage
     */
   public function index()
{
    /* 🔹 Ensure subscription courses are enrolled into tbl_order_courses
       so that courseStats and My Courses work with subscriptions too. */
    if ($this->siteUserId > 0) {
        SubscriptionEnrollment::syncForUser($this->siteUserId);
    }

    $lessStatsCount = (new Lesson(0, $this->siteUserId, $this->siteUserType))->getLessStatsCount();
    $schClassStats = (new OrderClass(0, $this->siteUserId, $this->siteUserType))->getSchedClassStats();
    $courseStats = (new OrderCourse(0, $this->siteUserId, $this->siteUserType, $this->siteLangId))->getCourseStats();
    $frmSrch = static::getSearchForm();
    
    $this->sets([
        'frmSrch' => $frmSrch,
        'schLessonCount' => $lessStatsCount['schLessonCount'],
        'totalLesson' => $lessStatsCount['totalLesson'],
        'totalClasses' => $schClassStats['totalClasses'],
        'totalCourses' => $courseStats['totalCourses'],
        'walletBalance' => User::getWalletBalance($this->siteUserId),
        'setMonthAndWeekNames' => true,
    ]);
    $this->_template->addJs([
        'issues/page-js/common.js',
        'lessons/page-js/common.js',
        'plans/page-js/common.js',
        'js/moment.min.js',
        'js/jquery.cookie.js',
        'js/app.timer.js',
        'js/fullcalendar-luxon.min.js',
        'js/fullcalendar.min.js',
        'js/fullcalendar-luxon-global.min.js',
        'js/fateventcalendar.js'
    ]);

    $this->_template->render();
}

    /**
     * Toggle Teacher Favorite
     */
    public function toggleTeacherFavorite()
    {
        $teacherId = FatApp::getPostedData('teacher_id', FatUtility::VAR_INT, 0);
        if ($teacherId == $this->siteUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $db = FatApp::getDb();
        $srch = new TeacherSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->applyPrimaryConditions();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('teacher.user_id', '=', $teacherId);
        $srch->addMultipleFields(['teacher.user_id', 'teacher.user_first_name', 'teacher.user_last_name']);
        $teacher = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $message = '';
        $action = 'N';
        $srch = new SearchBase(User::DB_TBL_TEACHER_FAVORITE, 'uft');
        $srch->addCondition('uft_user_id', '=', $this->siteUserId);
        $srch->addCondition('uft_teacher_id', '=', $teacherId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if (!$db->fetch($srch->getResultSet())) {
            $userObj = new User($this->siteUserId);
            if (!$userObj->setupFavoriteTeacher($teacherId)) {
                $message = Label::getLabel('LBL_PLEASE_CONTACT_SUPPORT');
            }
            $action = 'A';
            $message = Label::getLabel('LBL_ADDED_TO_FAVOURITES');
        } else {
            if (!$db->deleteRecords(User::DB_TBL_TEACHER_FAVORITE, [
                        'smt' => 'uft_user_id = ? AND uft_teacher_id = ?',
                        'vals' => [$this->siteUserId, $teacherId]
                    ])) {
                $message = Label::getLabel('LBL_PLEASE_CONTACT_SUPPORT');
            }
            $action = 'R';
            $message = Label::getLabel('LBL_REMOVED_FROM_FAVOURITES');
        }
        FatUtility::dieJsonSuccess(['msg' => $message, 'action' => $action]);
    }

    /**
     * Render Favorite page and Favorite Search Form
     */
    public function favourites()
    {
        $frmFavSrch = $this->getFavouriteSearchForm();
        $this->set('frmFavSrch', $frmFavSrch);
        $this->_template->render();
    }

    /**
     * Get Favorite Search Form
     * 
     * @return Form
     */
    private function getFavouriteSearchForm(): Form
    {
        $frm = new Form('frmFavSrch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_KEYWORD')]);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'), ['class' => 'btn btn--primary']);
        $fld_cancel = $frm->addResetButton('', "btn_clear", Label::getLabel('LBL_Clear'), ['onclick' => 'clearSearch();', 'class' => 'btn--clear']);
        $fld_submit->attachField($fld_cancel);
        $frm->addHiddenField('', 'page', 1);
        return $frm;
    }

    /**
     * Get Favorites
     */
    public function getFavourites()
    {
        $frm = $this->getFavouriteSearchForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $userObj = new User($this->siteUserId);
        $favouritesData = $userObj->getFavourites($post, $this->siteLangId);
        $this->set('countriesArr', Country::getNames($this->siteLangId));
        $this->set('favouritesData', $favouritesData);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
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
        $frm->addHiddenField('', 'ordles_lesson_starttime', MyDate::formatDate(date('Y-m-d H:i:s'), 'Y-m-d'));
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE);
        $frm->addHiddenField('', 'pageno', 1);
        $frm->addHiddenField('', 'view', AppConstant::VIEW_DASHBOARD_LISTING);
        return $frm;
    }

}
