<?php
class SubscriptionGate
{
    public static function assertCourseAccess(int $userId, int $courseId): void
    {
        $course = Course::getAttributesById($courseId, ['course_id','course_subject_id']);
        $subjectId = FatUtility::int($course['course_subject_id']);
        if ($subjectId < 1) {
            Message::addErrorMessage('Course is not linked to any subject.');
            FatApp::redirectUser(MyUtility::makeUrl('Courses'));
        }

        $sub = UserSubscription::getActiveByUser($userId);
        if (!$sub) {
            Message::addErrorMessage('Please subscribe to access courses.');
            FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'pricing'));
        }
        $allowed = array_filter(array_map('trim', explode(',', (string)$sub['usubs_subject_ids'])));
        if (!in_array((string)$subjectId, $allowed, true)) {
            Message::addErrorMessage('This subject is not in your plan. Upgrade or change your selected subjects.');
            FatApp::redirectUser(MyUtility::makeUrl('Subscription', 'manageSubjects'));
        }
    }
}
