<?php
require_once CONF_APPLICATION_PATH . 'controllers/ParentBaseController.php';
/**
 * Parent Subscription Controller
 * 
 * @package ReadWithUs
 */
class ParentSubscriptionController extends ParentBaseController
{
    public function index()
    {
        // Mock Data
        $subscriptions = [
            [
                'id' => 101,
                'package_name' => 'Math Starter',
                'child_name' => 'John Doe',
                'start_date' => '2023-09-01',
                'end_date' => '2023-10-01',
                'status' => 'Active',
                'status_class' => 'badge--success'
            ],
            [
                'id' => 102,
                'package_name' => 'English Basic',
                'child_name' => 'Jane Doe',
                'start_date' => '2023-09-05',
                'end_date' => '2023-10-05',
                'status' => 'Active',
                'status_class' => 'badge--success'
            ]
        ];

        $this->set('subscriptions', $subscriptions);
        $this->set('pageTitle', Label::getLabel('LBL_MY_SUBSCRIPTIONS'));
        $this->_template->render();
    }
}
