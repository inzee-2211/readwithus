<?php
require_once CONF_APPLICATION_PATH . 'controllers/ParentBaseController.php';
/**
 * Parent Children Controller
 * 
 * @package ReadWithUs
 */
class ParentChildrenController extends ParentBaseController
{
    public function index()
    {
        $children = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'grade' => 'Year 7',
                'activeSubscriptions' => 1,
                'totalQuizzes' => 5,
                'averageScore' => 82,
                'teachers' => ['Mr. Smith', 'Ms. Taylor'],
                'avatar_letter' => 'J'
            ],
            [
                'id' => 2,
                'name' => 'Jane Doe',
                'grade' => 'Year 5',
                'activeSubscriptions' => 1,
                'totalQuizzes' => 3,
                'averageScore' => 90,
                'teachers' => ['Ms. Brown'],
                'avatar_letter' => 'J'
            ]
        ];

        $this->set('children', $children);
        $this->set('pageTitle', Label::getLabel('LBL_MY_CHILDREN'));
        $this->_template->render();
    }

    public function view($childId)
    {
        $childId = FatUtility::int($childId);
        if ($childId < 1) {
            FatUtility::exitWithErrorCode(404);
        }

        // Mock Data
        $child = [
            'id' => $childId,
            'name' => ($childId == 1) ? 'John Doe' : 'Jane Doe',
            'grade' => ($childId == 1) ? 'Year 7' : 'Year 5',
            'courses' => [
                ['name' => 'Mathematics 101', 'progress' => '60%'],
                ['name' => 'English Literature', 'progress' => '40%']
            ],
            'quizzes' => [
                ['name' => 'Math Quiz 1', 'score' => '85/100', 'date' => '2023-10-01'],
                ['name' => 'English Grammer', 'score' => '90/100', 'date' => '2023-10-05']
            ],
            'upcoming_lessons' => [
                ['subject' => 'Math', 'teacher' => 'Mr. Smith', 'date' => '2023-10-20 10:00:00']
            ]
        ];

        $this->set('child', $child);
        $this->set('pageTitle', $child['name'] . ' - ' . Label::getLabel('LBL_PROGRESS'));
        $this->_template->render();
    }
}
