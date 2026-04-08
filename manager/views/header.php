<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
if (isset($includeEditor) && true === $includeEditor) {
    $extendEditorJs = 'true';
} else {
    $extendEditorJs = 'false';
    $includeEditor = false;
}
$commonHeadData = [
    'siteLangId' => $siteLangId,
    'jsVariables' => $jsVariables,
    'extendEditorJs' => $extendEditorJs,
    'includeEditor' => $includeEditor,
    'layoutDirection' => MyUtility::getLayoutDirection()
];
if (!empty($favIconFile)) {
    $commonHeadData['favIconFile'] = $favIconFile;
}
$this->includeTemplate('_partial/header/common-head.php', $commonHeadData, false);
echo $this->writeMetaTags();
echo $this->getJsCssIncludeHtml(false);
$commonHeadHtmlData = ['bodyClass' => $bodyClass, 'includeEditor' => $includeEditor, 'siteLanguage' => $siteLanguage];
$this->includeTemplate('_partial/header/common-header-html.php', $commonHeadHtmlData, false);
if (AdminAuth::isAdminLogged()) {
    $name = Admin::getAttributesById(AdminAuth::getLoggedAdminId(), 'admin_name');
    $this->includeTemplate('_partial/header/logged-user-header.php', [
        'adminName' => $name,
        'siteLangId' => $siteLangId,
        'siteLanguages' => $siteLanguages,
        'controllerName' => $controllerName,
        'adminLoggedId' => AdminAuth::getLoggedAdminId(),
        'actionName' => $actionName,
        'regendatedtime' => $regendatedtime ?? '',
    ]);
}
