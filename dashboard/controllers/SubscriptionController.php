<?php
class DashboardSubscriptionController extends DashboardController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Show current subscription status in dashboard
     */
    public function index()
    {
        $subscription = UserSubscription::getActiveByUser($this->siteUserId);
        
        if ($subscription) {
            // Get package details
            $package = SubscriptionPackage::getById($subscription['usubs_spackage_id']);
            
            // Get subscribed subjects with names
            $subjectIds = array_filter(array_map('intval', 
                explode(',', $subscription['usubs_subject_ids'])));
            
            $subjects = [];
            if (!empty($subjectIds)) {
                $srch = new SearchBase('course_subjects', 's');
                $srch->addCondition('s.id', 'IN', $subjectIds);
                $srch->addMultipleFields(['id', 'subject']);
                $subjects = FatApp::getDb()->fetchAll($srch->getResultSet());
            }
            
            // Get accessible course count
            $courseCount = $this->getAccessibleCourseCount($subjectIds);
            
            $this->sets([
                'subjects' => $subjects,
                'package' => $package,
                'courseCount' => $courseCount,
            ]);
        }
        
        $this->set('subscription', $subscription);
        $this->_template->render();
    }

    /**
     * Manage subjects from dashboard
     */
    public function manageSubjects()
    {
        $subscription = UserSubscription::getActiveByUser($this->siteUserId);
        
        if (!$subscription) {
            Message::addErrorMessage(Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'));
            FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing', [], CONF_WEBROOT_FRONT_URL));
        }

        $package = SubscriptionPackage::getById($subscription['usubs_spackage_id']);
        $limit = (int)$package['spackage_subject_limit'];

        // Get all available subjects
        $srch = new SearchBase('course_subjects', 's');
        $srch->addMultipleFields(['id AS subject_id', 'subject AS subject_name']);
        $srch->addOrder('subject', 'ASC');
        $allSubjects = FatApp::getDb()->fetchAll($srch->getResultSet());

        $currentSubjects = array_filter(array_map('intval',
            explode(',', $subscription['usubs_subject_ids'])));

        $frm = $this->getSubjectSelectionForm($allSubjects, $limit);
        $frm->fill(['subject_ids' => $currentSubjects]);

        $this->sets([
            'frm' => $frm,
            'subscription' => $subscription,
            'package' => $package,
            'limit' => $limit,
        ]);
        
        $this->_template->render();
    }

    /**
     * Save subject selection from dashboard
     */
    public function setupSubjects()
    {
        $subscription = UserSubscription::getActiveByUser($this->siteUserId);
        
        if (!$subscription) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'));
        }

        $package = SubscriptionPackage::getById($subscription['usubs_spackage_id']);
        $limit = (int)$package['spackage_subject_limit'];

        $frm = $this->getSubjectSelectionForm([], $limit);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $subjectIds = $post['subject_ids'] ?? [];
        if (count($subjectIds) > $limit) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CAN_SELECT_UP_TO_{limit}_SUBJECTS', ['{limit}' => $limit]));
        }

        $csv = implode(',', $subjectIds);
        
        if (UserSubscription::updateSubjects($subscription['usubs_id'], $csv)) {
            // Sync course access with new subjects
            SubscriptionEnrollment::syncForUser($this->siteUserId);
            
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_SUBJECTS_UPDATED_SUCCESSFULLY'));
        }

        FatUtility::dieJsonError(Label::getLabel('LBL_SUBJECT_UPDATE_FAILED'));
    }

    /**
     * Cancel subscription from dashboard
     */
    public function cancel()
    {
        $subscription = UserSubscription::getActiveByUser($this->siteUserId);
        
        if (!$subscription) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'));
        }

        // If using Stripe, we'd typically redirect to frontend for cancellation
        // or handle via webhook. For now, mark as canceled in database.
        
        $db = FatApp::getDb();
        $db->startTransaction();
        
        try {
            // Mark subscription as canceled
            if (!$db->updateFromArray(
                UserSubscription::DB_TBL,
                [
                    'usubs_status' => 'canceled',
                    'usubs_canceled_at' => date('Y-m-d H:i:s')
                ],
                ['smt' => 'usubs_id = ?', 'vals' => [$subscription['usubs_id']]]
            )) {
                throw new Exception('Failed to update subscription status');
            }

            // Remove subscription access
            SubscriptionEnrollment::removeSubscriptionAccess($this->siteUserId);
            
            $db->commitTransaction();
            
            Message::addMessage(Label::getLabel('LBL_SUBSCRIPTION_CANCELLED_SUCCESSFULLY'));
            FatUtility::dieJsonSuccess([
                'msg' => Label::getLabel('LBL_SUBSCRIPTION_CANCELLED_SUCCESSFULLY'),
                'redirectUrl' => MyUtility::makeUrl('DashboardSubscription')
            ]);
            
        } catch (Exception $e) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError(Label::getLabel('LBL_SUBSCRIPTION_CANCEL_FAILED'));
        }
    }

    /**
     * Upgrade subscription - redirect to frontend pricing page
     */
    public function upgrade()
    {
        FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing', [], CONF_WEBROOT_FRONT_URL));
    }

    /**
     * Get count of accessible courses for subjects
     */
    private function getAccessibleCourseCount(array $subjectIds): int
    {
        if (empty($subjectIds)) {
            return 0;
        }

        $srch = new SearchBase(Course::DB_TBL, 'c');
        $srch->addCondition('c.course_subject_id', 'IN', $subjectIds);
        $srch->addCondition('c.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('c.course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('COUNT(*) as course_count');
        
        $result = FatApp::getDb()->fetch($srch->getResultSet());
        return (int)($result['course_count'] ?? 0);
    }

    /**
     * Get subject selection form for dashboard
     */
    private function getSubjectSelectionForm(array $subjects = [], int $limit = 0): Form
    {
        $frm = new Form('frmSubjects');
        
        $options = [];
        foreach ($subjects as $subject) {
            $options[$subject['subject_id']] = $subject['subject_name'];
        }
        
        $fld = $frm->addCheckBoxes(
            Label::getLabel('LBL_SELECT_SUBJECTS'), 
            'subject_ids', 
            $options
        );
        $fld->requirements()->setRequired();
        
        if ($limit > 0) {
            $fld->requirements()->setCustomErrorMessage(
                Label::getLabel('LBL_YOU_CAN_SELECT_UP_TO_{limit}_SUBJECTS', ['{limit}' => $limit])
            );
        }
        
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_UPDATE_SUBJECTS'));
        $frm->addButton('', 'btn_cancel', Label::getLabel('LBL_CANCEL'), [
            'onclick' => 'goToSubscription();'
        ]);
        
        return $frm;
    }
}