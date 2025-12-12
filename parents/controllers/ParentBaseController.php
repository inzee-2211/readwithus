<?php

/**
 * Parent Base Controller
 * 
 * @package ReadWithUs
 */
class ParentBaseController extends MyAppController
{
    public function __construct(string $action)
    {
        parent::__construct($action);

        // TODO: Implement Parent Authentication
        // For now, allow access without strict login check to facilitate development and testing.

        $this->set('siteLangId', $this->siteLangId);
        // User::PARENT is not yet defined in the core User model. 
        // We use ID 3 for Parent for now.
        $this->set('siteUserType', 3); // Mocking Parent Context
    }

    protected function getParentSidebar()
    {
        // return $this->_template->
    }
}
