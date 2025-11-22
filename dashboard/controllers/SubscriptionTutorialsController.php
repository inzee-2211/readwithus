<?php
// dashboard/controllers/SubscriptionTutorialsController.php

class SubscriptionTutorialsController extends DashboardController
{
    /**
     * Start subscription course - IMPROVED with better error handling
     */
    public function start(int $courseId)
    {
        $userId = $this->siteUserId;
        
        try {
            // Check access first
            if (!UnifiedCourseAccess::canAccessCourse($userId, $courseId)) {
                Message::addErrorMessage(Label::getLabel('LBL_SUBSCRIPTION_REQUIRED_FOR_ACCESS'));
                FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
            }

            $accessData = UnifiedCourseAccess::startCourse($userId, $courseId);
            
            FatApp::redirectUser(MyUtility::generateUrl('SubscriptionTutorials', 'index', [
                $accessData['progress_id']
            ]));
            
        } catch (Exception $e) {
            error_log("Subscription course start failed: " . $e->getMessage());
            Message::addErrorMessage($e->getMessage());
            FatApp::redirectUser(MyUtility::makeUrl('Courses', 'view', [$courseId]));
        }
    }

    /**
     * Mark lecture as complete - IMPROVED with concurrency handling
     */
    public function markComplete()
    {
        $lectureId = FatApp::getPostedData('lecture_id', FatUtility::VAR_INT, 0);
        $status = (int)FatApp::getPostedData('status');
        $progressId = FatApp::getPostedData('progress_id', FatUtility::VAR_INT, 0);
        
        if ($lectureId < 1 || $progressId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        try {
            $success = UnifiedCourseAccess::markLectureComplete($progressId, $lectureId, (bool)$status);
            
            if ($success) {
                if ($status) {
                    FatUtility::dieJsonSuccess(Label::getLabel('LBL_LECTURE_MARKED_COVERED'));
                } else {
                    FatUtility::dieJsonSuccess(Label::getLabel('LBL_LECTURE_MARKED_UNCOVERED'));
                }
            } else {
                FatUtility::dieJsonError(Label::getLabel('LBL_OPERATION_FAILED'));
            }
            
        } catch (Exception $e) {
            FatUtility::dieJsonError($e->getMessage());
        }
    }

    /**
     * Get course analytics for admin
     */
    public function getCourseAnalytics(int $courseId)
    {
        if ($this->siteUserType != User::ADMIN && $this->siteUserType != User::TEACHER) {
            FatUtility::exitWithErrorCode(403);
        }

        $db = FatApp::getDb();
        
        $analytics = $db->fetchAll($db->query("
            SELECT 
                sp.subpro_progress as progress_basis_points,
                COUNT(*) as user_count
            FROM tbl_subscription_progress sp
            WHERE sp.subpro_course_id = ?
            GROUP BY sp.subpro_progress
            ORDER BY sp.subpro_progress
        ", [$courseId]));

        $this->set('analytics', $analytics);
        $this->_template->render(false, false);
    }
}