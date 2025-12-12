<?php
require_once CONF_APPLICATION_PATH . 'controllers/ParentBaseController.php';
/**
 * Parent Wallet Controller
 * 
 * @package ReadWithUs
 */
class ParentWalletController extends ParentBaseController
{
    public function index()
    {
        // Mock Data
        $balance = 50.00;
        $transactions = [
            [
                'id' => 'TXN7890',
                'date' => '2023-09-28',
                'amount' => '-$25.00',
                'description' => 'Subscription Purchase: Math Starter (John Doe)',
                'status' => 'Completed'
            ],
            [
                'id' => 'TXN7891',
                'date' => '2023-09-15',
                'amount' => '+$75.00',
                'description' => 'Wallet Top-up',
                'status' => 'Completed'
            ]
        ];

        $this->set('balance', $balance);
        $this->set('transactions', $transactions);
        $this->set('pageTitle', Label::getLabel('LBL_WALLET'));
        $this->_template->render();
    }
}
