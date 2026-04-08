<?php

class AdminBaseController extends AdminController
{

    /**
     * Initialize Admin Base
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        if (empty($this->siteAdminId)) {
            if (FatUtility::isAjaxCall()) {
                http_response_code(401);
                FatUtility::dieJsonError(Label::getLabel('LBL_YOUR_SESSION_SEEMS_TO_BE_EXPIRED'));
            }
            FatApp::redirectUser(MyUtility::makeUrl('AdminGuest', 'loginForm'));
        }
        $this->set("bodyClass", '');
    }

    public function getBreadcrumbNodes(string $action)
    {
        $nodes = [];
        $className = get_class($this);
        $arr = explode('-', FatUtility::camel2dashed($className));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $className = ucwords(implode(' ', $arr));
        if ($action == 'index') {
            $nodes[] = ['title' => $className];
        } else {
            $arr = explode('-', FatUtility::camel2dashed($action));
            $action = ucwords(implode(' ', $arr));
            $nodes[] = ['title' => $className, 'href' => MyUtility::makeUrl($urlController)];
            $nodes[] = ['title' => $action];
        }
        return $nodes;
    }
}
