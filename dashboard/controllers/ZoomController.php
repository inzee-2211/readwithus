<?php

/**
 * Zoom Controller is used for handling Zoom Meetings
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class ZoomController extends DashboardController
{

    /**
     * Initialize Zoom
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    public function meeting()
    {
        $this->_template->render();
    }

    public function leave()
    {
        $this->_template->render();
    }

}
