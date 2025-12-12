<?php
require_once CONF_APPLICATION_PATH . 'controllers/ParentBaseController.php';
/**
 * Parent Dashboard Controller
 * 
 * @package ReadWithUs
 */
class ParentDashboardController extends ParentBaseController
{
    public function index()
    {
        $stats = [
            'childrenCount' => 2,
            'upcomingLessons' => 3,
            'completedQuizzes' => 12,
            'activeSubscriptions' => 1,
            'walletBalance' => 50.00,
        ];

        $this->set('stats', $stats);
        $this->set('pageTitle', Label::getLabel('LBL_PARENT_DASHBOARD'));

        $this->_template->render();
    }
}
