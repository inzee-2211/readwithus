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
    $ll= $this->getLevelsFromDB();
       
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
        'levels' => $this->getLevelsFromDB(),
        'popularFaqList' => $this->getPopularFaqs(),
        'whyWeEffectiveBlock' => ExtraPage::getBlockContent(ExtraPage::BLOCK_WHY_WE_ARE_EFFECTIVE, $this->siteLangId),
    ]);

    // ✅ ADD THIS BLOCK
    $langId = $this->siteLangId;

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

    $db = FatApp::getDb();
    $homeTestimonials = $db->fetchAll($srch->getResultSet(), 'testimonial_id');
    $this->set('homeTestimonials', $homeTestimonials);
    // ✅ END OF ADD

    $class = new GroupClassSearch($this->siteLangId, $this->siteUserId, $this->siteUserType);
    $course = new CourseSearch($this->siteLangId, $this->siteUserId, 0);        
    $this->set('classes', $class->getUpcomingClasses());
    $this->set('courses', $course->getPopularCourses());
    $this->_template->render();
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
    private function getSubjectsByLevel($levelId)
    {
        $db = FatApp::getDb();

        $query = "SELECT id, subject FROM course_subjects WHERE level_id = $levelId"; // Direct insertion

        $result = $db->query($query);

        if (!$result) {
            die('Database Query Failed: ' . $db->getError());
        }

        $subjects = $db->fetchAll($result);

        if (empty($subjects)) {
            return [];
        }
        return array_column($subjects, 'subject', 'id');
    }



    public function getsubjectsforlevel()
    {
        $levelId = FatApp::getPostedData('levelId', FatUtility::VAR_INT, 0);
 
        if ($levelId <= 0) {
            echo json_encode(['status' => 0, 'msg' => 'Invalid level ID']);
            return;
        }

        $subjects = $this->getSubjectsByLevel($levelId); // Get subjects by level
        
        if (empty($subjects)) {
            echo json_encode(['status' => 0, 'msg' => 'No subjects found for the selected level']);
            return;
        }

        $subjectsWithDetails = [];

        foreach ($subjects as $subjectId => $subjectName) {
            $topics = $this->getTopicsBySubject($subjectId);
            
            $topicsWithSubtopics = [];

            foreach ($topics as $topicId => $topicName) {
                $subtopics = $this->getSubtopicsByTopic($topicId);

                $topicsWithSubtopics[$topicName] = $subtopics;
            }

            $subjectsWithDetails[$subjectName] = $topicsWithSubtopics;
        }
       
        echo json_encode(['status' => 1, 'data' => $subjectsWithDetails]);
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
