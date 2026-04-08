<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$stickyDemoHeader = '';
if (FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1) && MyUtility::isDemoUrl()) {
    $stickyDemoHeader = 'sticky-demo-header';
}
?>
<!doctype html>
<html lang="en" dir="<?php echo $siteLanguage['language_direction']; ?>" class="<?php echo $stickyDemoHeader; ?>">

<head>
    <!-- Basic Page Needs ======================== -->
    <meta charset="utf-8">
    <?php echo $this->writeMetaTags(); ?>
    <!-- MOBILE SPECIFIC METAS ===================== -->
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0,user-scalable=0" />
    <!-- FONTS ================================================== -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>" />
    <!-- FAVICON ================================================== -->
    <link rel="shortcut icon" href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_FAVICON, 0, Afile::SIZE_ORIGINAL], CONF_WEBROOT_FRONTEND); ?>">
    <link rel="apple-touch-icon" href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_APPLE_TOUCH_ICON, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONTEND); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/apple-touch-icon-114x114.png">
    <!-- CSS/JS ================================================== -->
    <?php
    $jsVariables = CommonHelper::htmlEntitiesDecode($jsVariables);
    $sslUsed = (FatApp::getConfig('CONF_USE_SSL', FatUtility::VAR_BOOLEAN, false)) ? 1 : 0;
    $websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId, FatUtility::VAR_STRING, '');
    $mainDashboardClass = (($controllerName == 'Teacher' || $controllerName == 'Learner') && $actionName == "index") ? "main-dashboard" : '';
    ?>
    <script type="text/javascript">
        var langLbl = <?php echo json_encode(CommonHelper::htmlEntitiesDecode($jsVariables)) ?>;
        var layoutDirection = '<?php echo MyUtility::getLayoutDirection(); ?>';
        var SslUsed = '<?php echo $sslUsed; ?>';
        var userTimeZone = '<?php echo MyUtility::getSiteTimezone(); ?>';
        var timeZoneOffset = '<?php echo MyDate::getOffset(MyUtility::getSiteTimezone()); ?>';
        var cookieConsent = <?php echo json_encode($cookieConsent); ?>;
        var userType = <?php echo FatUtility::int($siteUserType); ?>;
        const LEARNER = <?php echo User::LEARNER; ?>;
        const TEACHER = <?php echo User::TEACHER; ?>;
        const ALERT_CLOSE_TIME = '<?php echo FatApp::getConfig("CONF_AUTO_CLOSE_ALERT_TIME", FatUtility::VAR_INT, 0); ?>';
        const confWebRootUrl = '<?php echo CONF_WEBROOT_URL; ?>';
        const confFrontEndUrl = '<?php echo CONF_WEBROOT_FRONTEND; ?>';
        <?php if (isset($setMonthAndWeekNames) && $setMonthAndWeekNames) { ?>
            var monthNames = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::getAllMonthName(false, $siteLangId))); ?>;
            var weekDayNames = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::dayNames(false, $siteLangId))); ?>;
            var meridiems = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::meridiems(false, $siteLangId))); ?>;
        <?php } ?>
    </script>
    <?php
    echo $this->getJsCssIncludeHtml(!CONF_DEVELOPMENT_MODE);
    if (FatApp::getConfig('CONF_ENABLE_PWA', FatUtility::VAR_BOOLEAN, false)) {
    ?>
        <link rel="manifest" href="<?php echo MyUtility::makeUrl('MyApp', 'PwaManifest'); ?>">
        <script>
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker.register("<?php echo CONF_WEBROOT_FRONTEND; ?>sw.js");
            }
        </script>
    <?php } ?>
    <?php echo Common::setThemeColorStyle(true); ?>
</head>
<body class="course-leaner">
    <page class="page">
        <main class="page-container">