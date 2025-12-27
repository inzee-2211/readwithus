<?php

use MailchimpMarketing\ApiClient;

/**
 * Home Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class HomeController extends MyAppController
{

    /**
     * Initialize Home
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Website Homepage
     */
  public function index()
{
   $levels = $this->getLevelsFromDB(); // returns [id => level_name]

    $selectedLevelId = 0;
    if (!empty($levels)) {
        // read from ?level_id, else default to first level
        $selectedLevelId = FatApp::getQueryStringData('level_id', FatUtility::VAR_INT, 0);
        if ($selectedLevelId <= 0) {
            $firstKey = array_key_first($levels);
            $selectedLevelId = (int)$firstKey;
        }
    }

    $slides = Slide::getSlides();
    $this->sets([
        'slides' => $slides,
        'slideImages' => Slide::getSlideImages(array_keys($slides), $this->siteLangId),
        'whyUsBlock' => ExtraPage::getBlockContent(ExtraPage::BLOCK_WHY_US, $this->siteLangId),
        'browseTutorPage' => ExtraPage::getBlockContent(ExtraPage::BLOCK_BROWSE_TUTOR, $this->siteLangId),
        'startLearning' => ExtraPage::getBlockContent(ExtraPage::BLOCK_HOW_TO_START_LEARNING, $this->siteLangId),
        'bookingBefore' => FatApp::getConfig('CONF_CLASS_BOOKING_GAP'),
        'popularLanguages' => TeachLanguage::getPopularLangs($this->siteLangId),
        'testmonialList' => Testimonial::getTestimonials($this->siteLangId),
        'blogPostsList' => BlogPost::getBlogsForGrids($this->siteLangId),
        'topRatedTeachers' => $this->getTopRatedTeachers(),
         'levels' => $levels,
        'popularFaqList' => $this->getPopularFaqs(),
        'whyWeEffectiveBlock' => ExtraPage::getBlockContent(ExtraPage::BLOCK_WHY_WE_ARE_EFFECTIVE, $this->siteLangId),
         'examBoards'          => $this->getExamBoardsFromDB(),
        'tiers'               => $this->getTiersFromDB(),
    ]);

    $db     = FatApp::getDb();
    $langId = $this->siteLangId;

    // ========= SUBSCRIPTION PLANS FOR HOME PRICING SECTION =========

    // 1) Find a default level that has at least one active package
    $defaultLevelId = 0;
    $sqlLevel = "
        SELECT cl.id
        FROM course_levels cl
        JOIN " . SubscriptionPackage::DB_TBL . " p
          ON p.spackage_level_id = cl.id
         AND p.spackage_status = 1
        ORDER BY cl.level_name ASC
        LIMIT 1
    ";
    $rowLevel = $db->fetch($db->query($sqlLevel));
    if ($rowLevel) {
        $defaultLevelId = (int)$rowLevel['id'];
    }

    // 2) Fetch active packages for that level (same service as PricingController)
    $plans = [];
    if ($selectedLevelId > 0) {
        // same service the Pricing controller uses
        $plans = SubscriptionPackage::getActiveAll($selectedLevelId);
    }

    // 3) Detect if this user already has an active subscription
    $hasActiveSubscription = false;
    $currentPackageId      = 0;

    if (UserAuth::isUserLogged()) {
        $activeSub = UserSubscription::getActiveByUser($this->siteUserId);
        if ($activeSub) {
            $hasActiveSubscription = true;
            $currentPackageId      = (int)$activeSub['usubs_spackage_id'];
        }
    }
 $userDetail = [];
    if (UserAuth::isUserLogged()) {
        $userDetail = User::getAttributesById(
            $this->siteUserId,
            ['user_id', 'user_trial_eligible']
        ) ?: [];
    }

    // 4) Expose to home view (our home rwu-pricing section uses these)
    $this->set('plans', $plans);
    $this->set('selectedLevelId', $selectedLevelId);
    $this->set('hasActiveSubscription', $hasActiveSubscription);
    $this->set('currentPackageId', $currentPackageId);
     $this->set('userDetail', $userDetail);

    // ========= END SUBSCRIPTION PLANS BLOCK =========

    // ✅ Home testimonials (your existing block)
    $srch = Testimonial::getSearchObject($langId, false);
    $srch->addCondition('testimonial_active', '=', AppConstant::YES);
    $srch->addMultipleFields([
        't.testimonial_id',
        't.testimonial_user_name',
        't.testimonial_identifier',
        't_l.testimonial_text',
        't.testimonial_added_on',
    ]);
    $srch->addOrder('testimonial_added_on', 'DESC');
    $srch->setPageSize(2); // only 2 for homepage

    $homeTestimonials = $db->fetchAll($srch->getResultSet(), 'testimonial_id');
    $this->set('homeTestimonials', $homeTestimonials);

    $class  = new GroupClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
    $course = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
   $this->set('classes', $class->getUpcomingClasses());

$popularCourses = $course->getPopularCourses();
$popularCourses = $this->attachCourseCardMeta($popularCourses);
$this->set('courses', $popularCourses);

    /* =========================
     * HOME PAGE SEO (Title/Desc/OG/Twitter/Schema)
     * ========================= */
    $pageTitle = 'GCSE Online Tutors & Courses | ReadWithUs';
    $pageDescription = 'GCSE online learning platform with expert tutors, interactive video lessons and AI-powered quizzes. Study Maths, English & Science anywhere in the UK.';

    // Canonical: homepage absolute URL
    $canonicalUrl = MyUtility::makeFullUrl();

    // OG/Twitter image (absolute)
    $ogImage = CONF_WEBROOT_URL . 'images/hero/hero.png';

    // Robots
    $metaRobots = 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';

    // Basic Schema (Organization + WebSite SearchAction)
    $schema = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Read With Us',
            'url' => $canonicalUrl,
            'logo' => CONF_WEBROOT_FRONT_URL . 'images/logo.png',
            'sameAs' => [
                'https://www.facebook.com/readwithusofficial',
                'https://www.instagram.com/readwithusuk',
                'https://www.linkedin.com/company/read-with-us-uk'
            ],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Read With Us',
            'url' => $canonicalUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => MyUtility::makeFullUrl('Courses') . '?keyword={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ];

    $this->set('pageTitle', $pageTitle);
    $this->set('pageDescription', $pageDescription);
    $this->set('canonicalUrl', $canonicalUrl);
    $this->set('ogImage', $ogImage);
    $this->set('metaRobots', $metaRobots);
    $this->set('twitterSite', '@read_withus');
    $this->set('structuredData', json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    $this->_template->render();
}
/**
 * Add course_subtitle + course_duration to the courses used on home page cards.
 */
private function attachCourseCardMeta(array $courses): array
{
    if (empty($courses)) {
        return $courses;
    }

    $ids = [];
    foreach ($courses as $c) {
        $id = (int)($c['course_id'] ?? 0);
        if ($id > 0) $ids[] = $id;
    }

    $ids = array_values(array_unique($ids));
    if (empty($ids)) {
        return $courses;
    }

    $db = FatApp::getDb();
    $langId = (int)$this->siteLangId;
    $in = implode(',', array_map('intval', $ids));

    // NOTE: if your lang table uses different column names, adjust them here.
    $sql = "
        SELECT 
            c.course_id,
            c.course_duration,
            cl.course_subtitle
        FROM " . Course::DB_TBL . " c
        LEFT JOIN " . Course::DB_TBL_LANG . " cl
            ON cl.courselang_course_id = c.course_id
           AND cl.courselang_lang_id = {$langId}
        WHERE c.course_id IN ({$in})
    ";

    $rs = $db->query($sql);
    $rows = $rs ? $db->fetchAll($rs, 'course_id') : [];

    foreach ($courses as &$c) {
        $id = (int)($c['course_id'] ?? 0);
        if ($id > 0 && isset($rows[$id])) {
            if (!isset($c['course_subtitle']) || $c['course_subtitle'] === '') {
                $c['course_subtitle'] = $rows[$id]['course_subtitle'] ?? '';
            }
            if (!isset($c['course_duration']) || $c['course_duration'] === '' || $c['course_duration'] === null) {
                $c['course_duration'] = $rows[$id]['course_duration'] ?? '';
            }
        }
    }
    unset($c);

    return $courses;
}

private function getExamBoardsFromDB(): array
{
    $db = FatApp::getDb();

    // Simple: fetch all exam boards, ordered by name
    $sql = "SELECT id, name 
              FROM course_examboards
          ORDER BY name ASC";

    $rs = $db->query($sql);
    if (!$rs) {
        return [];
    }

    return $db->fetchAll($rs); // rows: ['id' => .., 'name' => ..]
}

private function getTiersFromDB(): array
{
    $db = FatApp::getDb();

    // Only non-deleted tiers, ordered by name
    $sql = "SELECT id, name, examboard_id
              FROM course_tier
             WHERE deleted = 0
          ORDER BY name ASC";

    $rs = $db->query($sql);
    if (!$rs) {
        return [];
    }

    return $db->fetchAll($rs); // rows: ['id' => .., 'name' => .., 'examboard_id' => ..]
}



    /**
     * Setup News Letter
     */
    public function setUpNewsLetter()
    {
        $post = FatApp::getPostedData();
        $apikey = FatApp::getConfig("CONF_MAILCHIMP_KEY");
        $listId = FatApp::getConfig("CONF_MAILCHIMP_LIST_ID");
        $prefix = FatApp::getConfig("CONF_MAILCHIMP_SERVER_PREFIX");
        if (empty($apikey) || empty($listId) || empty($prefix)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NOT_CONFIGURED_PLEASE_CONTACT_SUPPORT'));
        }
        try {
            $mailchimp = new ApiClient();
            $mailchimp->setConfig(['apiKey' => $apikey, 'server' => $prefix]);
            $response = $mailchimp->ping->get();
            if (!isset($response->health_status)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_CONFIGURED_ERROR_MESSAGE'));
            }
            $subscriber = $mailchimp->lists->addListMember($listId, ['email_address' => $post['email'], 'status' => 'subscribed'], true);
            if ($subscriber->status != 'subscribed') {
                FatUtility::dieJsonError(Label::getLabel('MSG_NEWSLETTER_SUBSCRIPTION_VALID_EMAIL'));
            }
        } catch (Exception $e) {
            $error = strtolower($e->getMessage());
            if (strpos($error, 'member exists') > -1) {
                FatUtility::dieJsonSuccess(Label::getLabel('MSG_YOU_ARE_ALREADY_SUBSCRIBER'));
            } else {
                FatUtility::dieJsonError($error);
            }
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_SUCCESSFULLY_SUBSCRIBED'));
    }

    private function getLevelsFromDB()
    {
        $db = FatApp::getDb();
        
        $query = "SELECT id, level_name FROM course_levels"; // Replace with actual table name
        
        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $levels = $db->fetchAll($result);

        if (empty($levels)) {
            return []; // Return an empty array if no records found
        }

        return array_column($levels, 'level_name', 'id');
    }
/*
    public function getsubjectsforlevel()
{
       $levelId = FatApp::getPostedData('levelId', FatUtility::VAR_INT, 0);

        if ($levelId <= 0) {
           echo json_encode(['status' => 0, 'msg' => 'Invalid level ID']);
           return;
       }
   
       $subjects = $this->getSubjectsByLevel($levelId); // Your method to get subjects by level
        
       if (empty($subjects)) {
           echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
           return;
       }
   
     echo json_encode(['status' => 1, 'data' => $subjects]);
}
*/
private function getSubjectsByLevel(int $levelId): array
{
    $db = FatApp::getDb();

    $levelId = (int)$levelId;
    if ($levelId <= 0) {
        return [];
    }

    $sql = "SELECT id, subject 
              FROM course_subjects 
             WHERE level_id = {$levelId}
          ORDER BY subject ASC";
    $rs  = $db->query($sql);
    if (!$rs) {
        return [];
    }

    return $db->fetchAll($rs); // rows: ['id' => .., 'subject' => ..]
}


  public function getsubjectsforlevel()
{
    $levelId = FatApp::getPostedData('levelId', FatUtility::VAR_INT, 0);

    if ($levelId <= 0) {
        echo json_encode(['status' => 0, 'msg' => 'Invalid level ID']);
        return;
    }

    $rows = $this->getSubjectsByLevel($levelId);

    if (empty($rows)) {
        echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
        return;
    }

    $subjects = [];
    foreach ($rows as $row) {
        $subjects[] = [
            'id'   => (int)$row['id'],
            'name' => $row['subject'],
        ];
    }

    echo json_encode(['status' => 1, 'data' => $subjects]);
}

    private function getTopicsBySubject($subjectId)
    {
        $db = FatApp::getDb();

        $query = "SELECT id, topic FROM course_topics WHERE subject_id = $subjectId"; // Query topics by subject

        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $topics = $db->fetchAll($result);

        if (empty($topics)) {
            return [];
        }

        return array_column($topics, 'topic', 'id'); // Returning topic name keyed by ID
    }


    private function getSubtopicsByTopic($topicId)
    {
        $db = FatApp::getDb();

        $query = "SELECT id, topic as subtopic FROM course_topics WHERE parent_id = $topicId"; // Query subtopics by topic

        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $subtopics = $db->fetchAll($result);

        if (empty($subtopics)) {
            return [];
        }

        return array_column($subtopics, 'subtopic', 'id'); // Returning subtopic name keyed by ID
    }


    /**
     * Get Top Rated Teachers
     * 
     * @return array
     */
    private function getTopRatedTeachers(): array
    {
        $srch = new TeacherSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->addMultipleFields([
            'teacher.user_first_name', 'teacher.user_last_name',
            'teacher.user_id', 'user_username', 'testat.testat_ratings',
            'teacher.user_country_id',
            'testat.testat_reviewes'
        ]);
        $srch->applyPrimaryConditions();
        $srch->addCondition('testat_ratings', '>', 0);
        $srch->addOrder('testat_ratings', 'DESC');
        $srch->setPageSize(8);
        $srch->doNotCalculateRecords();
        $records = FatApp::getDb()->fetchAll($srch->getResultSet(), 'user_id');
        $countryIds = array_column($records, 'user_country_id');
        $countries = TeacherSearch::getCountryNames($this->siteLangId, $countryIds);
        foreach ($records as $key => $record) {
            $records[$key]['country_name'] = $countries[$record['user_country_id']] ?? '';
            $records[$key]['full_name'] = $record['user_first_name'] . ' ' . $record['user_last_name'];
        }
        return $records;
    }

     /**
     * Get Popular FAQ
     * 
     * @return array
     */
    private function getPopularFaqs(): array
    {
        $srch = Faq::getSearchObject($this->siteLangId);
        $srch->addMultipleFields(['faq_id', 'IFNULL(faq_title, faq_identifier) as faq_title',
       'faq_description']);
       $srch->joinTable(FaqCategory::DB_TBL, 'INNER JOIN', 'faqcat_id = faq_category');
    $srch->addCondition('faqcat_active', '=', AppConstant::YES);
    $srch->addCondition('faq_active', '=', AppConstant::YES);
    $srch->addCondition('faq_is_popular', '=', AppConstant::YES);
    $srch->addOrder('faq_id','DESC');
    $srch->doNotCalculateRecords();
    $pageSize = AppConstant::PAGESIZE;
    $srch->setPageSize($pageSize);
    return FatApp::getDb()->fetchAll($srch->getResultSet());    
    }

}
