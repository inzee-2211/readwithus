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
// if (!function_exists('app_debug_log')) {
//     /**
//      * Simple JSON line logger into application/logs/<channel>-YYYY-MM-DD.log
//      */
//     function app_debug_log(string $channel, string $message, array $context = []): void
//     {
//         // Try to resolve project root in a robust way
//         // dashboard/controllers/CoursesController.php -> __DIR__ = .../dashboard/controllers
//         // dirname(__DIR__, 2) -> ... (project root)
//         $projectRoot = realpath(dirname(__DIR__, 2));
//         if ($projectRoot === false) {
//             // Fallback: rely on CONF_APPLICATION_PATH if it exists
//             if (defined('CONF_APPLICATION_PATH')) {
//                 $projectRoot = rtrim(dirname(CONF_APPLICATION_PATH), "/\\");
//             } else {
//                 // Last resort: current directory
//                 $projectRoot = realpath('.') ?: '.';
//             }
//         }

//         // application/logs
//         $base = $projectRoot . '/application/logs';

//         // Ensure dir exists
//         if (!is_dir($base)) {
//             @mkdir($base, 0775, true);
//         }

//         $file = $base . '/' . $channel . '-' . date('Y-m-d') . '.log';

//         $payload = [
//             'time'    => date('Y-m-d H:i:s'),
//             'uri'     => $_SERVER['REQUEST_URI'] ?? '',
//             'message' => $message,
//             'context' => $context,
//         ];

//         $line = json_encode($payload) . PHP_EOL;

//         // Try to write. If it fails, log to Apache/PHP error.log
//         $ok = @file_put_contents($file, $line, FILE_APPEND);
//         if ($ok === false) {
//             error_log(
//                 'app_debug_log: FAILED to write log file: ' . $file .
//                 ' | payload=' . $line
//             );
//         }
//     }
// }


app_require('library/services/UnifiedCourseAccess.php');
/**
 * This Controller is used for handling courses
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CoursesController extends DashboardController
{

    /**
     * Initialize Courses
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Search Form
     *
     */
    public function index()
    {
        $frm = $this->getSearchForm();
        $this->set('frm', $frm);
        $this->_template->addJs('js/jquery.barrating.min.js');
        $this->_template->render();
    }

    /**
     * Search & List Plans
     */
   /**
 * Search & List Courses - UPDATED FOR SUBSCRIPTION MODEL
 */
/**
 * Search & List Courses - UPDATED FOR SUBSCRIPTION MODEL
 */
/**
 * Search & List Courses - SUBSCRIPTION-AWARE (subjects → courses)
 */
/**
 * Search & List Courses - UPDATED FOR SUBSCRIPTION MODEL
 */
public function search()
{
    $frm = $this->getSearchForm();
    if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_subcateid'])) {
        FatUtility::dieJsonError(current($frm->getValidationErrors()));
    }

    if ($this->siteUserType == User::LEARNER) {

        $db = FatApp::getDb();

        // 1) Get active user subscriptions with subject IDs (CSV)
        $subscriptionSrch = new SearchBase('tbl_user_subscriptions', 'usubs');
        $subscriptionSrch->addCondition('usubs_user_id', '=', $this->siteUserId);
        // $subscriptionSrch->addCondition('usubs_status', '=', 'active');
$subscriptionSrch->addCondition('usubs_status', 'IN', ['active', 'trialing']);
$subscriptionSrch->addCondition('usubs_end_date', '>=', date('Y-m-d H:i:s'));


        $subscriptionSrch->addMultipleFields(['usubs_subject_ids']);
        $subscriptionRs = $subscriptionSrch->getResultSet();
        $subscriptions = $db->fetchAll($subscriptionRs);

        // Collect all subject IDs from all active subscriptions
        $allSubjectIds = [];
        foreach ($subscriptions as $subscription) {
            if (!empty($subscription['usubs_subject_ids'])) {
                $subjectIds = array_filter(
                    array_map('intval', explode(',', $subscription['usubs_subject_ids']))
                );
                $allSubjectIds = array_merge($allSubjectIds, $subjectIds);
            }
        }

        // Remove duplicates and ensure we have valid IDs
        $allSubjectIds = array_unique(array_filter($allSubjectIds));

        if (empty($allSubjectIds)) {
            // No subjects in subscriptions - return empty results
            $this->sets([
                'courses'        => [],
                'post'           => $post,
                'recordCount'    => 0,
                'courseStatuses' => Course::getStatuses(),
                'courseTypes'    => Course::getTypes(),
                'orderStatuses'  => CourseProgress::getStatuses(),
            ]);
            $this->_template->render(false, false);
            return;
        }

        // 2) Derive allowed level_ids from these subjects
        $allowedLevels = [];
        $subjSrch = new SearchBase('course_subjects', 'sub');
        $subjSrch->addCondition('sub.id', 'IN', $allSubjectIds);
        $subjSrch->addMultipleFields(['sub.id', 'sub.level_id']);
        $subjRs = $subjSrch->getResultSet();
        $subjRows = $db->fetchAll($subjRs) ?: [];

        foreach ($subjRows as $row) {
            if (!empty($row['level_id'])) {
                $allowedLevels[] = (int)$row['level_id'];
            }
        }
        $allowedLevels = array_unique(array_filter($allowedLevels));

        // 3) Build course search (subject + level filtered)
        $srch = new CourseSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->applyPrimaryConditions();

        // Filter by subjects from subscription
        $srch->addCondition('course.course_subject_id', 'IN', $allSubjectIds);

        // Join course_subjects to enforce level consistency
        $srch->joinTable('course_subjects', 'LEFT JOIN', 'sub.id = course.course_subject_id', 'sub');

        if (!empty($allowedLevels)) {
            // Filter by level as well (KCSE vs GCSE etc.)
            $srch->addCondition('sub.level_id', 'IN', $allowedLevels);
        }

        // Apply existing search filters (keyword, status, etc.)
        $srch->applySearchConditions($post);
        $srch->addSearchListingFields();

        // Legacy progress joins (if you still need them – kept as-is)
        $srch->joinTable(
            OrderCourse::DB_TBL,
            'LEFT JOIN',
            'ordcrs.ordcrs_course_id = course.course_id AND ordcrs.ordcrs_user_id = ' . $this->siteUserId,
            'ordcrs'
        );
        $srch->joinTable(
            CourseProgress::DB_TBL,
            'LEFT JOIN',
            'crspro.crspro_ordcrs_id = ordcrs.ordcrs_id',
            'crspro'
        );

        $srch->addMultipleFields([
            'ordcrs.ordcrs_id',
            'ordcrs.ordcrs_status',
            'crspro.crspro_progress',
            'crspro.crspro_status',
            'crspro.crspro_completed',
            'crspro.crspro_id'
        ]);

        $srch->addOrder('crspro_status', 'ASC');
        $srch->addOrder('course.course_id', 'DESC');

    } else {
        // Existing logic for teachers/admins
        $srch = new CourseSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
        $srch->addOrder('course_id', 'DESC');
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->applySearchConditions($post);
    }

    $srch->setPageSize($post['pagesize']);
    $srch->setPageNumber($post['page']);

    $courses = $srch->fetchAndFormat();

    $this->sets([
        'courses'        => $courses,
        'post'           => $post,
        'recordCount'    => $srch->recordCount(),
        'courseStatuses' => Course::getStatuses(),
        'courseTypes'    => Course::getTypes(),
        'orderStatuses'  => CourseProgress::getStatuses(),
    ]);

    $this->_template->render(false, false);
}


    /**
     * Render Course Manage Page
     *
     * @param mixed $courseId
     */
    public function form($courseId = 0)
    {
        if ($this->siteUserType == User::LEARNER) {
            FatUtility::exitWithErrorCode(404);
        }
        if (!empty($courseId) && FatUtility::int($courseId) < 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $courseId = FatUtility::int($courseId);
        $courseTitle = '';
        if ($courseId > 0) {
            $srch = new CourseSearch($this->siteLangId, $this->siteUserId, User::TEACHER);
            $srch->applyPrimaryConditions();
            $srch->addMultipleFields(['course.course_id', 'course_title']);
            $srch->addCondition('course.course_id', '=', $courseId);
            $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
            $srch->setPageSize(1);
            if (!$course = FatApp::getDb()->fetch($srch->getResultSet())) {
                Message::addErrorMessage(Label::getLabel('LBL_COURSE_NOT_FOUND'));
                FatApp::redirectUser(MyUtility::makeUrl('Courses'));
            }
            $courseTitle = $course['course_title'];
            $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
            if (!$course->canEditCourse()) {
                Message::addErrorMessage(Label::getLabel('LBL_UNAUTHORIZED_ACCESS'));
                FatApp::redirectUser(MyUtility::makeUrl('Courses'));
            }
        }

        $this->set('courseTitle', $courseTitle);
        $this->set('courseId', $courseId);
        $this->set('siteLangId', $this->siteLangId);
        $this->set("includeEditor", true);
        $this->_template->addJs(['js/jquery.tagit.js', 'js/jquery.ui.touch-punch.min.js']);
        $this->_template->render();
    }

    /**
     * Render Basic Details Page
     *
     * @param int $courseId
     */
    public function generalForm(int $courseId = 0)
    {
        $course = [];
        if ($courseId > 0) {
            $srch = new CourseSearch($this->siteLangId, $this->siteUserId, User::TEACHER);
            $srch->applyPrimaryConditions();
            $srch->addMultipleFields([
                'course_title',
                'course_subtitle',
                'course_cate_id',
                'course_subcate_id',
                'course_clang_id',
                'course_level',
                'course_details',
                  'course_subject_id',
                'course.course_id',
            ]);
            $srch->addCondition('course.course_id', '=', $courseId);
            $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
            $srch->setPageSize(1);
            if (!$course = FatApp::getDb()->fetch($srch->getResultSet())) {
                FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
            }
        }
        $frm = $this->getGeneralForm();
        $frm->fill($course);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    /**
     * Fetch sub categories for selected category
     *
     * @param int $catgId
     * @param int $subCatgId
     * @return html
     */
    public function getSubcategories(int $catgId, int $subCatgId = 0)
    {
        $catgId = FatUtility::int($catgId);
        $subcategories = [];
        if ($catgId > 0) {
            $subcategories = Category::getCategoriesByParentId($this->siteLangId, $catgId);
        }
        $this->set('subCatgId', $subCatgId);
        $this->set('subcategories', $subcategories);
        $this->_template->render(false, false);
    }

    /**
     * Setup basic details
     *
     * @return json
     */
    public function setup()
    {
        $frm = $this->getGeneralForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData(), [
            'course_cate_id', 'course_subcate_id', 'course_clang_id'
        ])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->setupGeneralData($post)) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess([
            'msg' => Label::getLabel('LBL_SETUP_SUCCESSFUL'),
            'courseId' => $course->getMainTableRecordId(),
            'title' => $post['course_title'],
        ]);
    }

    /**
     * Render Media Page
     *
     * @param int $courseId
     */
    public function mediaForm(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* validate course id */
        if (!Course::getAttributesById($courseId, 'course_id')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* get form and fill */
        $frm = $this->getMediaForm();
        $frm->fill(['course_id' => $courseId]);
        /* get course image required dimensions */
        $file = new Afile(Afile::TYPE_COURSE_IMAGE);
        $image = $file->getFile($courseId);
        $dimensions = $file->getImageSizes(Afile::SIZE_LARGE);
        /* get video url */
        $file = new Afile(Afile::TYPE_COURSE_PREVIEW_VIDEO);
        $previewVideo = $file->getFile($courseId);
        $this->sets([
            'frm' => $frm,
            'courseId' => $courseId,
            'extensions' => Afile::getAllowedExts(Afile::TYPE_COURSE_IMAGE),
            'videoFormats' => Afile::getAllowedExts(Afile::TYPE_COURSE_PREVIEW_VIDEO),
            'dimensions' => $dimensions,
            'filesize' => MyUtility::convertBitesToMb(Afile::getAllowedUploadSize()),
            'previewVideo' => $previewVideo,
            'image' => $image,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup course media
     *
     * @return json
     */
    public function setupMedia()
    {
        $frm = $this->getMediaForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieWithError($course->getError());
        }
        if (empty($_FILES['course_image']['name']) && empty($_FILES['course_preview_video']['name'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_MEDIA_SELECTED'));
        }
        $type = '';
        $files = [];
        if (!empty($_FILES['course_image']['name'])) {
            $type = Afile::TYPE_COURSE_IMAGE;
            $files = $_FILES['course_image'];
        }
        if (!empty($_FILES['course_preview_video']['name'])) {
            $type = Afile::TYPE_COURSE_PREVIEW_VIDEO;
            $files = $_FILES['course_preview_video'];
        }
        $file = new Afile($type);
        if (!$file->saveFile($files, $post['course_id'], true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_FILE_UPLOADED_SUCCESSFULLY'));
    }

    /**
     * Remove course media files
     *
     * @param int $courseId
     */
    public function removeMedia(int $courseId)
    {
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieWithError($course->getError());
        }
        $type = FatApp::getPostedData('type');
        $file = new Afile($type);
        if (!$file->removeFile($courseId, 0, true)) {
            FatUtility::dieJsonError($file->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_FILE_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Render Intended Learners Page
     *
     * @param int $courseId
     */
    public function intendedLearnersForm(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->get()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }
        /* get form and fill */
        $frm = $this->getIntendedLearnersForm();
        $frm->fill(['course_id' => $courseId]);
        /* get saved responses */
        $learner = new IntendedLearner();
        $responses = $learner->get($courseId);
        $this->sets([
            'frm' => $frm,
            'courseId' => $courseId,
            'responses' => $responses,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Setup Intended Learners Data
     *
     */
    /**
 * Setup Intended Learners Data with enhanced error handling
 *
 */
public function setupIntendedLearners()
{
    try {
        $frm = $this->getIntendedLearnersForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        // Validate course ownership and existence
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }

        // Clean and validate the data before processing
        $cleanedData = $this->cleanIntendedLearnersData($post);
        
        $intended = new IntendedLearner();
        if (!$intended->setup($cleanedData)) {
            FatUtility::dieJsonError($intended->getError());
        }
        
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETUP_SUCCESSFUL'));
        
    } catch (Exception $e) {
        error_log("CoursesController::setupIntendedLearners error: " . $e->getMessage());
        FatUtility::dieJsonError(Label::getLabel('LBL_SYSTEM_ERROR_OCCURRED'));
    }
}

/**
 * Clean and validate intended learners data
 *
 * @param array $post
 * @return array
 */
private function cleanIntendedLearnersData(array $post): array
{
    $cleaned = [
        'course_id' => (int)$post['course_id'],
        'type_learnings' => [],
        'type_requirements' => [],
        'type_learners' => [],
        'type_learnings_ids' => [],
        'type_requirements_ids' => [],
        'type_learners_ids' => []
    ];

    // Clean learning data
    if (isset($post['type_learnings']) && is_array($post['type_learnings'])) {
        foreach ($post['type_learnings'] as $index => $learning) {
            $cleanedLearning = trim(strip_tags($learning));
            if (!empty($cleanedLearning)) {
                $cleaned['type_learnings'][] = $cleanedLearning;
                // Handle corresponding ID
                if (isset($post['type_learnings_ids'][$index])) {
                    $id = trim($post['type_learnings_ids'][$index]);
                    $cleaned['type_learnings_ids'][] = (is_numeric($id) && $id > 0) ? (int)$id : '';
                } else {
                    $cleaned['type_learnings_ids'][] = '';
                }
            }
        }
    }

    // Clean requirements data
    if (isset($post['type_requirements']) && is_array($post['type_requirements'])) {
        foreach ($post['type_requirements'] as $index => $requirement) {
            $cleanedRequirement = trim(strip_tags($requirement));
            if (!empty($cleanedRequirement)) {
                $cleaned['type_requirements'][] = $cleanedRequirement;
                // Handle corresponding ID
                if (isset($post['type_requirements_ids'][$index])) {
                    $id = trim($post['type_requirements_ids'][$index]);
                    $cleaned['type_requirements_ids'][] = (is_numeric($id) && $id > 0) ? (int)$id : '';
                } else {
                    $cleaned['type_requirements_ids'][] = '';
                }
            }
        }
    }

    // Clean learners data
    if (isset($post['type_learners']) && is_array($post['type_learners'])) {
        foreach ($post['type_learners'] as $index => $learner) {
            $cleanedLearner = trim(strip_tags($learner));
            if (!empty($cleanedLearner)) {
                $cleaned['type_learners'][] = $cleanedLearner;
                // Handle corresponding ID
                if (isset($post['type_learners_ids'][$index])) {
                    $id = trim($post['type_learners_ids'][$index]);
                    $cleaned['type_learners_ids'][] = (is_numeric($id) && $id > 0) ? (int)$id : '';
                } else {
                    $cleaned['type_learners_ids'][] = '';
                }
            }
        }
    }

    return $cleaned;
}
    /**
     * Updating Intended Learner Records sort order
     *
     * @return json
     */
    public function updateIntendedOrder()
    {
        $ids = FatApp::getPostedData('order');
        $intended = new IntendedLearner();
        if (!$intended->updateOrder($ids)) {
            FatUtility::dieJsonError($intended->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_ORDER_SETUP_SUCCESSFUL'));
    }

    /**
     * function to delete course intended learner
     *
     * @param int $indLearnerId
     * @return json
     */
    public function deleteIntendedLearner(int $indLearnerId)
    {
        if ($indLearnerId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /*  check if record exists */
        if (!$courseId = IntendedLearner::getAttributesById($indLearnerId, 'coinle_course_id')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }
        $intended = new IntendedLearner($indLearnerId);
        if (!$intended->delete()) {
            FatUtility::dieJsonError($intended->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Render Pricing Page
     *
     * @param int $courseId
     */
    public function priceForm(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* validate course id */
        $data = ['course_id' => $courseId];
        if (!$course = Course::getAttributesById($courseId, ['course_type', 'course_currency_id', 'course_price'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        $courseObj = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$courseObj->canEditCourse()) {
            FatUtility::dieJsonError($courseObj->getError());
        }
        $data = array_merge($data, $course);

        /* get form and fill */
        $frm = $this->getPriceForm();
        $data['course_price'] = round(CourseUtility::convertToCurrency($data['course_price'], $data['course_currency_id']), 2);
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('courseId', $courseId);
        $this->_template->render(false, false);
    }

    /**
     * Get Prices Data
     *
     * @return json
     */
    public function setupPrice()
    {
        $frm = $this->getPriceForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData(), ['course_currency_id'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }

        $price = 0;
        if ($post['course_type'] == Course::TYPE_PAID) {
            /* validate currency */
            $currencyId = FatUtility::int($post['course_currency_id']);
            if ($currencyId > 0 && Currency::getAttributesById($currencyId, 'currency_active') == AppConstant::INACTIVE) {
                FatUtility::dieJsonError(Label::getLabel('LBL_CURRENCY_NOT_AVAILABLE'));
            }
            if ($post['course_price'] > 0) {
                $price = CourseUtility::convertToSystemCurrency($post['course_price'], $post['course_currency_id']);
            }
            if ($price < 1) {
                $label = Label::getLabel('LBL_COURSE_PRICE_CANNOT_BE_LESS_THAN_1_{currency}');
                $label = str_replace('{currency}', MyUtility::getSystemCurrency()['currency_code'], $label);
                FatUtility::dieJsonError($label);
            }
        }
        $course->assignValues([
            'course_type' => $post['course_type'],
            'course_currency_id' => $post['course_currency_id'],
            'course_price' => $price,
        ]);
        if (!$course->save()) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETUP_SUCCESSFUL'));
    }

    /**
     * Render Curriculum Page
     *
     * @param int $courseId
     */
    public function curriculumForm(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        /* validate course id */
        if (!$course->get()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }
        /* get form and fill */
        $frm = $this->getCurriculumForm();
        $frm->fill(['course_id' => $courseId]);
        $this->set('frm', $frm);
        $this->set('courseId', $courseId);
        $this->_template->render(false, false);
    }

    /**
     * Render Settings Page
     *
     * @param int $courseId
     */
    public function settingsForm(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* validate course id */
        if (!$courseData = Course::getAttributesById($courseId, ['course_id', 'course_certificate'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->canEditCourse()) {
            FatUtility::dieJsonError($course->getError());
        }
        /* create form data */
        $data = [
            'course_id' => $courseId,
            'course_certificate' => $courseData['course_certificate']
        ];
        /* get form data from lang table */
        $srch = new SearchBase(Course::DB_TBL_LANG);
        $srch->addCondition('course_id', '=', $courseId);
        $srch->addMultipleFields(['course_welcome', 'course_congrats', 'course_srchtags']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        if ($course = FatApp::getDb()->fetch($srch->getResultSet())) {
            $crsTags = [];
            if (!empty($course['course_srchtags'])) {
                $crsTags = json_decode($course['course_srchtags']);
            }
            $course['course_tags'] = implode(',', $crsTags);
            $data = array_merge($data, $course);
        }
        /* check certificate available or not */
        $srch = CertificateTemplate::getSearchObject($this->siteLangId);
        $srch->addCondition('certpl_code', '=', 'course_completion_certificate');
        $srch->addCondition('certpl_status', '=', AppConstant::ACTIVE);
        $offerCetificate = false;
        if (FatApp::getDb()->fetch($srch->getResultSet())) {
            $offerCetificate = true;
        }
        /* get form and fill */
        $frm = $this->getSettingForm($offerCetificate);
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('offerCetificate', $offerCetificate);
        $this->set('courseId', $courseId);
        $this->_template->render(false, false);
    }

    /**
     * Setup Course Settings Data
     *
     * @return json
     */
    public function setupSettings()
    {
 
        $frm = $this->getSettingForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        
        $course = new Course($post['course_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->setupSettings($post)) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SETUP_SUCCESSFUL'));
    }

    /**
     * function to delete course
     *
     * @param int $courseId
     * @return json
     */
    public function remove(int $courseId)
    {
        $courseId = FatUtility::int($courseId);
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->delete()) {
            FatUtility::dieJsonError($course->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_REMOVED_SUCCESSFULLY'));
    }

    /**
     * Function to get eligibility status for all course steps.
     *
     * @param int $courseId
     * @return json
     */
    public function getEligibilityStatus(int $courseId)
    {
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType);
        $criteria = $course->isEligibleForApproval();
        FatUtility::dieJsonSuccess(['criteria' => $criteria]);
    }

    /**
     * Submitting course for approval from admin
     *
     * @param int $courseId
     * @return bool
     */
    public function submitForApproval(int $courseId)
    {
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$course->submitApprovalRequest()) {
            FatUtility::dieJsonError($course->getError());
        }
        Message::addMessage(Label::getLabel('LBL_APPROVAL_REQUESTED_SUCCESSFULLY'));
        FatUtility::dieJsonSuccess('');
    }


    /**
     * Add/Remove Course from user favorites list
     *
     * @return json
     */
    public function toggleFavorite()
    {
        $courseId = FatApp::getPostedData('course_id', FatUtility::VAR_INT, 0);
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $db = FatApp::getDb();
        /* validate course id */
        $course = new Course($courseId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$data = $course->get()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_NOT_FOUND'));
        }
        if ($data['course_user_id'] == $this->siteUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_MARK_YOUR_OWN_COURSE_AS_FAVORITE'));
        }
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, AppConstant::NO);
        if ($status == AppConstant::NO) {
            /* check course already marked favorite */
            $srch = new SearchBase(User::DB_TBL_COURSE_FAVORITE);
            $srch->addCondition('ufc_user_id', '=', $this->siteUserId);
            $srch->addCondition('ufc_course_id', '=', $courseId);
            $srch->doNotCalculateRecords();
            $srch->setPageSize(1);
            if (FatApp::getDb()->fetch($srch->getResultSet())) {
                FatUtility::dieJsonError(Label::getLabel('LBL_COURSE_IS_ALREADY_IN_YOUR_FAVORITES_LIST'));
            }
            /* add to favorites */
            $user = new User($this->siteUserId);
            if (!$user->setupFavoriteCourse($courseId)) {
                FatUtility::dieJsonError($user->getError());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_COURSE_ADDED_TO_FAVORITES'));
        }
        /* remove from favorites */
        $where = [
            'smt' => 'ufc_user_id = ? AND ufc_course_id = ?',
            'vals' => [$this->siteUserId, $courseId]
        ];
        if (!$db->deleteRecords(User::DB_TBL_COURSE_FAVORITE, $where)) {
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_COURSE_REMOVED_FROM_FAVORITES'));
    }

    /**
     * Render cancellation popup
     */
    public function cancelForm()
    {
        $ordcrsId = FatApp::getPostedData('ordcrs_id', FatUtility::VAR_INT, 0);
        $order = new OrderCourse($ordcrsId, $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$order->getCourseToCancel()) {
            FatUtility::dieJsonError($order->getError());
        }
        $frm = $this->getCancelForm();
        $frm->fill(['ordcrs_id' => $ordcrsId]);
        $this->sets(['frm' => $frm]);
        $this->_template->render(false, false);
    }

    /**
     * Setup cancellation request
     *
     * @return bool
     */
    public function cancelSetup()
    {
        $frm = $this->getCancelForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $order = new OrderCourse($post['ordcrs_id'], $this->siteUserId, $this->siteUserType, $this->siteLangId);
        if (!$order->cancel($post['comment'])) {
            FatUtility::dieJsonError($order->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_CANCELLATION_REQUEST_SUBMITTED_SUCCESSFULLY'));
    }

    /**
     * Get Cancel Form
     *
     */
    private function getCancelForm(): Form
    {
        $frm = new Form('cancelFrm');
        $comment = $frm->addTextArea(Label::getLabel('LBL_COMMENTS'), 'comment');
        $comment->requirements()->setLength(10, 300);
        $comment->requirements()->setRequired();
        $frm->addHiddenField('', 'ordcrs_id')->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }

    /**
     * Get Search Form
     *
     */
    private function getSearchForm(): Form
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword');
        if ($this->siteUserType == User::TEACHER) {
            $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'course_status', Course::getStatuses(), '', [], Label::getLabel('LBL_SELECT'));
        } else {
            $frm->addSelectBox(Label::getLabel('LBL_STATUS'), 'crspro_status', OrderCourse::getStatuses(), '', [], Label::getLabel('LBL_SELECT'));
        }
        $frm->addSelectBox(Label::getLabel('LBL_TYPE'), 'course_type', Course::getTypes(), '', [], Label::getLabel('LBL_SELECT'));
        $categoryList = Category::getCategoriesByParentId($this->siteLangId);
        $frm->addSelectBox(Label::getLabel('LBL_CATEGORY'), 'course_cateid', $categoryList, '', [], Label::getLabel('LBL_SELECT'));
        $frm->addSelectBox(Label::getLabel('LBL_SUB_CATEGORY'), 'course_subcateid', [], '', [], Label::getLabel('LBL_SELECT'));
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setInt();
        $frm->addHiddenField('', 'page', 1)->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SEARCH'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_RESET'));
        return $frm;
    }
/**
 * Get list of subjects for course subject dropdown
 *
 * @return array [id => label]
 */
private function getSubjectOptions(): array
{
    $db = FatApp::getDb();

    // Adjust table name if needed (e.g. 'course_subjecs' or 'tbl_subjects')
    $srch = new SearchBase('course_subjects', 's');
    $srch->addMultipleFields(['s.id', 's.subject']);
    $srch->addOrder('s.subject', 'ASC');
    $srch->doNotCalculateRecords();
    $srch->setPageSize(1000);

    $rs = $srch->getResultSet();
    $rows = $db->fetchAll($rs) ?: [];

    $out = [];
    foreach ($rows as $row) {
        $out[(int)$row['id']] = $row['subject'];
    }
    return $out;
}

    /**
     * Basic Details Form
     *
     */
    private function getGeneralForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addTextBox(Label::getLabel('LBL_COURSE_TITLE'), 'course_title')->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_COURSE_SUBTITLE'), 'course_subtitle')->requirements()->setRequired();
        $categories = Category::getCategoriesByParentId($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_EXAMBOARD'), 'course_cate_id', $categories, '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
        $fld = $frm->addSelectBox(Label::getLabel('LBL_TIER'), 'course_subcate_id', [], '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setInt();
        $langsList = (new CourseLanguage())->getAllLangs($this->siteLangId, true);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_TEACHING_LANGUAGE'), 'course_clang_id', $langsList, '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
        $fld = $frm->addSelectBox(Label::getLabel('LBL_LEVEL'), 'course_level', Course::getCourseLevels(), '', [], Label::getLabel('LBL_SELECT'));
        $fld->requirements()->setRequired();
         $subjectOptions = $this->getSubjectOptions();
    $fld = $frm->addSelectBox(
        Label::getLabel('LBL_SUBJECT'),
        'course_subject_id',
        $subjectOptions,
        '',
        [],
        Label::getLabel('LBL_SELECT')
    );
    $fld->requirements()->setRequired();

        $frm->addHtmlEditor(Label::getLabel('LBL_DESCRIPTION'), 'course_details')->requirements()->setRequired();
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Course Media Form
     *
     */
    private function getMediaForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addFileUpload(Label::getLabel('LBl_COURSE_IMAGE'), 'course_image');
        $frm->addFileUpload(Label::getLabel('LBl_PREVIEW_VIDEO'), 'course_preview_video');
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Intended Learners Form
     *
     */
    private function getIntendedLearnersForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addTextBox('', 'type_learnings[]')->requirements()->setRequired();
        $frm->addTextBox('', 'type_requirements[]')->requirements()->setRequired();
        $frm->addTextBox('', 'type_learners[]')->requirements()->setRequired();
        $frm->addHiddenField('', 'type_learnings_ids[]');
        $frm->addHiddenField('', 'type_requirements_ids[]');
        $frm->addHiddenField('', 'type_learners_ids[]');
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Prices Form
     *
     */
    private function getPriceForm(): Form
    {
        $frm = new Form('frmCourses');
        $fld = $frm->addRadioButtons(Label::getLabel('LBL_TYPE'), 'course_type', Course::getTypes());
        $fld->requirements()->setRequired();
        $frm->addSelectBox(
            Label::getLabel('LBL_CURRENCY'),
            'course_currency_id',
            Currency::getCurrencyNameWithCode($this->siteLangId), 
            '', 
            [], 
            Label::getLabel('LBL_SELECT')
        );
        $frm->addTextBox(Label::getLabel('LBL_PRICE'), 'course_price')->requirements()->setFloat();
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Curriculum Form
     *
     */
    private function getCurriculumForm(): Form
    {
        $frm = new Form('frmCourses');
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_SAVE_&_NEXT'));
        return $frm;
    }

    /**
     * Get Setting Form
     *
     * @param bool $offerCetificate
     */
    private function getSettingForm(bool $offerCetificate = true): Form
    {
        $frm = new Form('frmCourses');
        if ($offerCetificate == true) {
            $fld = $frm->addRadioButtons(Label::getLabel('LBL_OFFER_CERTIFICATE'), 'course_certificate', AppConstant::getYesNoArr(), AppConstant::NO);
            $fld->requirements()->setRequired();
        } else {
            $frm->addHiddenField('', 'course_certificate', AppConstant::NO);
        }
        /* $frm->addTextArea(Label::getLabel('LBL_WELCOME_MESSAGE'), 'course_welcome')->requirements()->setRequired();
        $fld = $frm->addTextArea(Label::getLabel('LBL_CONGRATULATIONS_MESSAGE'), 'course_congrats');
        $fld->requirements()->setRequired(); */
        $frm->addTextBox(Label::getLabel('LBL_COURSE_TAGS'), 'course_tags')->requirements()->setRequired();
        $frm->addHiddenField('', 'course_id')->requirements()->setInt();
        $frm->addSubmitButton('', 'btn_save', Label::getLabel('LBL_SAVE'));
        $frm->addButton('', 'btn_approval', Label::getLabel('LBL_SUBMIT_FOR_APPROVAL'));
        return $frm;
    }
}
