<?php

/**
 * Logged User Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class LoggedUserController extends MyAppController
{

    /**
     * Initialize Logged User
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
            Message::addErrorMessage(Label::getLabel('LBL_SESSION_EXPIRED'));
            FatApp::redirectUser(MyUtility::makeUrl('GuestUser', 'loginForm', [], CONF_WEBROOT_FRONT_URL));
        }
    }

}
