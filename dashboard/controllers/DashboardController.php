<?php

/**
 * Dashboard Controller is used for handling Dashboard on Teacher
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class DashboardController extends MyAppController
{

    /**
     * Initialize Dashboard 
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
       
     
 
        parent::__construct($action);
        if (empty($this->siteUserId)) {
            
            if (FatUtility::isAjaxCall()) {
                http_response_code(401);
                FatUtility::dieJsonError(Label::getLabel('LBL_SESSION_EXPIRED'));
            }
            if ($action != 'logout') {
             
                FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm', [], CONF_WEBROOT_FRONT_URL));
            }
        }
        if ($this->siteUserType == User::TEACHER && $this->siteUser['user_is_teacher'] == AppConstant::NO) {
        
         
            MyUtility::setUserType(User::LEARNER);
            
            FatApp::redirectUser(MyUtility::makeUrl('TeacherRequest', 'form', [], CONF_WEBROOT_FRONTEND));
        }
    }

    /**
     * Get Badge Counts
     */
    public function getBadgeCounts()
    {
        $notiCount = (new Notification($this->siteUserId))->getUnreadCount($this->siteUserType);
        $messCount = (new Thread(0, $this->siteUserId))->getUnreadCount();
        FatUtility::dieJsonSuccess(['notifications' => $notiCount, 'messages' => $messCount]);
    }

    /**
     * Report Search Form
     * 
     * @param int $forGraph
     * @return Form
     */
    protected function reportSearchForm(): Form
    {
        $frm = new Form('reportSearchForm');
        $field = $frm->addSelectBox(Label::getLabel('LBL_DURATION_TYPE'), 'duration_type', MyDate::getDurationTypesArr(), MyDate::TYPE_TODAY);
        $field->requirements()->setInt();
        $field->requirements()->setRequired(true);
        return $frm;
    }

}
