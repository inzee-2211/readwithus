<?php

/**
 * This Controller is used for handling course learning process
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CertificatesController extends MyAppController
{
    /* Image Sizes */
    const SIZE_SMALL = 'SMALL';
    const SIZE_MEDIUM = 'MEDIUM';
    const SIZE_LARGE = 'LARGE';

    /**
     * Initialize Tutorials
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Index
     */
    public function index()
    {
        FatUtility::exitWithErrorCode(404);
    }

    /**
     * Render Certificate Detail Page
     *
     * @param int $ordcrsId
     */
    public function view(int $ordcrsId)
    {
        /* get course and user data */
        $srch = new OrderCourseSearch($this->siteLangId, 0, 0);
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addMultipleFields([
            'teacher.user_id AS teacher_id',
            'learner.user_country_id',
            'orders.order_user_id',
            'ordcrs_certificate_number'
        ]);
        $srch->addCondition('ordcrs_id', '=', $ordcrsId);
        if (!$order = FatApp::getDb()->fetch($srch->getResultSet())) {
            FatUtility::exitWithErrorCode(404);
        }
        if (empty($order['ordcrs_certificate_number'])) {
            FatUtility::exitWithErrorCode(404);
        }
        /* get country name */
        $srch = Country::getSearchObject(false, $this->siteLangId);
        $srch->addCondition('country_id', '=', $order['user_country_id']);
        $srch->addFld('IFNULL(c_l.country_name, c.country_identifier) AS country_name');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $order['country_name'] = '';
        if ($country = FatApp::getDb()->fetch($srch->getResultSet())) {
            $order['country_name'] = $country['country_name'];
        }
        /* get teacher stats */
        $srch = new SearchBase(TeacherStat::DB_TBL);
        $srch->addCondition('testat_user_id', '=', $order['teacher_id']);
        $srch->addMultipleFields(['testat_ratings', 'testat_reviewes']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $order['teacher_rating'] = $order['teacher_reviewes'] = 0;
        if ($stats = FatApp::getDb()->fetch($srch->getResultSet())) {
            $order['teacher_rating'] = $stats['testat_ratings'];
            $order['teacher_reviewes'] = $stats['testat_reviewes'];
        }
        $this->sets([
            'ordcrsId' => $ordcrsId,
            'order' => $order,
        ]);
        $this->_template->render();
    }
}
