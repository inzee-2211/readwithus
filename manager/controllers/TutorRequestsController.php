<?php

/**
 * Admin Tutor Requests Controller
 */
class TutorRequestsController extends AdminBaseController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
        // Use an existing privilege method that fits your app
        $this->objPrivilege->canViewUsers();
    }

    /**
     * List all tutor requests
     */
    public function index()
    {
        $page = FatApp::getQueryStringData('page', FatUtility::VAR_INT, 1);
        $pageSize = 20;

        $db = FatApp::getDb();

        // Base table: tutor requests
        $srch = new SearchBase('tbl_tutor_requests', 'tr');

        // Join mapping table (tutor_request_courses) – for older flow / course-based requests
        $srch->joinTable(
            'tbl_tutor_request_courses',
            'LEFT JOIN',
            'trc.trc_tutreq_id = tr.tutreq_id',
            'trc'
        );

        // Join courses table so we can show course titles instead of IDs (fallback)
        $srch->joinTable(
            'tbl_courses',
            'LEFT JOIN',
            'c.course_id = trc.trc_course_id',
            'c'
        );

        /*
         * NOTE:
         * We now primarily use tutreq_level_id, tutreq_subject_id, tutreq_examboard_id, tutreq_tier_id
         * to understand what kind of "package" / setup user is asking for.
         * course_titles is kept as a fallback for older data.
         */
        $srch->addMultipleFields([
            'tr.tutreq_id',
            'tr.tutreq_user_id',
            'tr.tutreq_first_name',
            'tr.tutreq_last_name',
            'tr.tutreq_email',
            'tr.tutreq_phone_code',
            'tr.tutreq_phone_number',
            'tr.tutreq_preferred_time',
            'tr.tutreq_status',
            'tr.tutreq_added_on',

            // NEW: expose level/subject/exam board / tier ids on listing
            'tr.tutreq_level_id',
            'tr.tutreq_subject_id',
            'tr.tutreq_examboard_id',
            'tr.tutreq_tier_id',

            // Fallback: comma-separated list of *course slugs* instead of IDs
            'GROUP_CONCAT(DISTINCT IFNULL(c.course_slug, c.course_slug) SEPARATOR ", ") AS course_titles',
        ]);

        // Group by request so GROUP_CONCAT works
        $srch->addGroupBy('tr.tutreq_id');

        // Newest first
        $srch->addOrder('tutreq_added_on', 'DESC');

        // Pagination
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);

        // Run query
        $rs = $srch->getResultSet();

        if ($rs === false) {
            die('DB Error in TutorRequestsController::index => ' . $db->getError());
        }

        $rows        = $db->fetchAll($rs);
        $recordCount = $srch->recordCount();

        $this->set('list', $rows);
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('recordCount', $recordCount);
        $this->set('statusArr', $this->getStatusArr());

        $this->_template->render();
    }

    /**
     * View single request details (simple page)
     */
    public function view($requestId)
    {
        $requestId = FatUtility::int($requestId);
        if ($requestId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
        }

        $db = FatApp::getDb();

        $srch = new SearchBase('tbl_tutor_requests', 'tr');
        $srch->joinTable(
            'tbl_tutor_request_courses',
            'LEFT JOIN',
            'trc.trc_tutreq_id = tr.tutreq_id',
            'trc'
        );
        $srch->joinTable(
            'tbl_courses',
            'LEFT JOIN',
            'c.course_id = trc.trc_course_id',
            'c'
        );

        $srch->addCondition('tr.tutreq_id', '=', $requestId);

        $srch->addMultipleFields([
            'tr.*',
            // Keep course titles for legacy data
            'GROUP_CONCAT(DISTINCT IFNULL(c.course_slug, c.course_slug) SEPARATOR ", ") AS course_titles',
        ]);

        $srch->addGroupBy('tr.tutreq_id');
        $srch->setPageSize(1);

        $rs = $srch->getResultSet();
        if ($rs === false) {
            Message::addErrorMessage('DB Error: ' . $db->getError());
            FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
        }

        $row = $db->fetch($rs);
        if (empty($row)) {
            Message::addErrorMessage(Label::getLabel('LBL_REQUEST_NOT_FOUND'));
            FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
        }

        // Simple debug-style detail view; can be replaced with a template later
        echo '<div class="page"><div class="fixed_container"><div class="row"><div class="space">';
        echo '<h2>' . Label::getLabel('LBL_TUTOR_REQUEST') . ' #' . (int)$row['tutreq_id'] . '</h2>';

        $fullName = trim(($row['tutreq_first_name'] ?? '') . ' ' . ($row['tutreq_last_name'] ?? ''));

        echo '<p><strong>' . Label::getLabel('LBL_NAME') . ':</strong> ' . htmlspecialchars($fullName) . '</p>';
        echo '<p><strong>' . Label::getLabel('LBL_EMAIL') . ':</strong> ' . htmlspecialchars($row['tutreq_email']) . '</p>';
        echo '<p><strong>' . Label::getLabel('LBL_PHONE') . ':</strong> +' .
             htmlspecialchars($row['tutreq_phone_code']) . ' ' . htmlspecialchars($row['tutreq_phone_number']) . '</p>';
        echo '<p><strong>' . Label::getLabel('LBL_PREFERRED_SCENARIO') . ':</strong><br>' .
             nl2br(htmlspecialchars($row['tutreq_preferred_time'])) . '</p>';

        // Show grouped package info (IDs, similar to listing)
        echo '<h4 style="margin-top:15px;">' . Label::getLabel('LBL_PACKAGES') . '</h4>';
        echo '<ul>';
        if (!empty($row['tutreq_level_id'])) {
            echo '<li><strong>' . Label::getLabel('LBL_LEVEL') . ':</strong> #' . (int)$row['tutreq_level_id'] . '</li>';
        }
        if (!empty($row['tutreq_subject_id'])) {
            echo '<li><strong>' . Label::getLabel('LBL_SUBJECT') . ':</strong> #' . (int)$row['tutreq_subject_id'] . '</li>';
        }
        if (!empty($row['tutreq_examboard_id'])) {
            echo '<li><strong>' . Label::getLabel('LBL_EXAM_BOARD') . ':</strong> #' . (int)$row['tutreq_examboard_id'] . '</li>';
        }
        if (!empty($row['tutreq_tier_id'])) {
            echo '<li><strong>' . Label::getLabel('LBL_TIER') . ':</strong> #' . (int)$row['tutreq_tier_id'] . '</li>';
        }
        if (!empty($row['course_titles'])) {
            echo '<li><strong>' . Label::getLabel('LBL_COURSES') . ':</strong> ' .
                 htmlspecialchars($row['course_titles']) . '</li>';
        }
        echo '</ul>';

        echo '<p><a class="btn btn--primary btn--sm" href="' . MyUtility::makeUrl('TutorRequests') . '">'
             . Label::getLabel('LBL_BACK_TO_LIST') . '</a></p>';

        echo '</div></div></div></div>';
        exit;
    }

    /**
     * Update request status (simple POST)
     */
    public function updateStatus()
    {
        $requestId = FatApp::getPostedData('requestId', FatUtility::VAR_INT, 0);
        $status    = FatApp::getPostedData('status',   FatUtility::VAR_INT, 0);

        if ($requestId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
        }

        $statusArr = $this->getStatusArr();
        if (!array_key_exists($status, $statusArr)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_STATUS'));
            FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
        }

        $db = FatApp::getDb();
        if (!$db->updateFromArray(
            'tbl_tutor_requests',
            ['tutreq_status' => $status],
            ['smt' => 'tutreq_id = ?', 'vals' => [$requestId]]
        )) {
            Message::addErrorMessage($db->getError());
            FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
        }

        Message::addMessage(Label::getLabel('LBL_STATUS_UPDATED_SUCCESSFULLY'));
        FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
    }

    /**
     * Delete request (simple POST)
     */
    public function delete()
{
    $requestId = FatApp::getPostedData('requestId', FatUtility::VAR_INT, 0);

    if ($requestId < 1) {
        Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
        FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
    }

    $db = FatApp::getDb();
    $db->startTransaction();

    try {
        // Delete associated courses first (legacy mapping table)
        // FIX: correct column name is trc_tutreq_id (based on your JOIN)
        $db->deleteRecords('tbl_tutor_request_courses', [
            'smt'  => 'trc_tutreq_id = ?',
            'vals' => [$requestId],
        ]);

        // Delete main request
        if (!$db->deleteRecords('tbl_tutor_requests', [
            'smt'  => 'tutreq_id = ?',
            'vals' => [$requestId],
        ])) {
            throw new Exception($db->getError());
        }

        $db->commitTransaction();
        Message::addMessage(Label::getLabel('LBL_REQUEST_DELETED_SUCCESSFULLY'));
        FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
    } catch (Exception $e) {
        $db->rollbackTransaction();
        Message::addErrorMessage($e->getMessage());
        FatApp::redirectUser(MyUtility::makeUrl('TutorRequests'));
    }
}
    /**
     * Status labels
     */
    private function getStatusArr()
    {
        return [
            0 => Label::getLabel('LBL_PENDING'),
            1 => Label::getLabel('LBL_PROCESSED'),
            2 => Label::getLabel('LBL_REJECTED'),
        ];
    }
}
