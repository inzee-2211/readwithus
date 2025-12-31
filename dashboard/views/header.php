<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$stickyDemoHeader = MyUtility::isDemoUrl() ? 'sticky-demo-header' : '';
?>
<!doctype html>
<html lang="en" dir="<?php echo $siteLanguage['language_direction']; ?>" class="<?php echo $stickyDemoHeader; ?>">

<head>
    <!-- Basic Page Needs ======================== -->
    <meta charset="utf-8">
  <?php
// Keep YoCoach meta tags only if controller didn't provide custom SEO
if (empty($pageTitle) && empty($pageDescription)) {
    echo $this->writeMetaTags();
}
?>

    <!-- MOBILE SPECIFIC METAS ===================== -->
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0,user-scalable=0" />
    <!-- FONTS ================================================== -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>" />
    <!-- FAVICON ================================================== -->
    <link rel="shortcut icon"
        href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_FAVICON, 0, Afile::SIZE_ORIGINAL], CONF_WEBROOT_FRONTEND); ?>">
    <link rel="apple-touch-icon"
        href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_APPLE_TOUCH_ICON, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONTEND); ?>">
    <link rel="apple-touch-icon" sizes="72x72"
        href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114"
        href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/apple-touch-icon-114x114.png">
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
    if (isset($includeEditor) && $includeEditor) {
        ?>
        <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
        <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js"></script>
    <?php }
    if (FatApp::getConfig('CONF_ENABLE_PWA', FatUtility::VAR_BOOLEAN, false)) { ?>
        <link rel="manifest" href="<?php echo MyUtility::makeUrl('Pwa', '', [], CONF_WEBROOT_FRONTEND); ?>">
        <script>
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker.register("<?php echo CONF_WEBROOT_FRONTEND; ?>sw.js");
            }
        </script>
    <?php } ?>

    <?php echo Common::setThemeColorStyle(true); ?>
    <script>
        $(document).ready(function () {
            <?php if ($siteUserId > 0) { ?>
                setTimeout(getBadgeCount(), 1000);
            <?php }
            if (!empty($messageData['msgs'][0] ?? '')) { ?>
                fcom.success('<?php echo $messageData['msgs'][0]; ?>');
            <?php }
            if (!empty($messageData['dialog'][0] ?? '')) { ?>
                fcom.warning('<?php echo $messageData['dialog'][0]; ?>');
            <?php }
            if (!empty($messageData['errs'][0] ?? '')) { ?>
                fcom.error('<?php echo $messageData['errs'][0]; ?>');
            <?php } ?>
        });
    </script>
</head>
<?php $isPreviewOn = MyUtility::isDemoUrl() ? 'is-preview-on' : ''; ?>
<?php
$dashboardRole = $_SESSION['RWU_DASHBOARD_ROLE'] ?? '';
$isParentMode  = ($dashboardRole === 'parent');
$isParentPage = (strtolower($controllerName) === 'parent');

$isUserParent = (!empty($siteUser['user_is_parent']) && $siteUser['user_is_parent'] == AppConstant::YES);
$parentModeClass = ($isParentMode && $isUserParent && $isParentPage) ? 'is-parent-mode' : '';
?>

<body
      class="dashboard-<?php echo (($siteUserType == User::TEACHER) ? 'teacher' : 'learner') . ' ' . $parentModeClass . ' ' . strtolower($controllerName) . ' ' . strtolower($actionName) . ' ' . $mainDashboardClass . ' ' . $isPreviewOn; ?>">
    <?php
    if (MyUtility::isDemoUrl()) {
        include(CONF_INSTALLATION_PATH . 'restore/view/header-bar.php');
    }
    if (isset($_SESSION['preview_theme'])) {
        $this->includeTemplate('header/preview.php', array(), false);
    }
    ?>
    <div class="site">
        <!-- [ SIDE BAR ========= -->
        <aside class="sidebar">
            <!-- [ SIDE BAR SECONDARY ========= -->
            <div class="sidebar__secondary">
                <nav class="menu menu--secondary">
                    <ul>
                        <li class="menu__item menu__item-toggle">
                            <a href="#primary-nav" class="menu__item-trigger trigger-js for-responsive"
                                title="<?php echo Label::getLabel('LBL_MENU'); ?>">
                                <span class="icon icon--menu">
                                    <span class="toggle"><span></span></span>
                                </span>
                                <span class="sr-only"><?php echo Label::getLabel('LBL_MENU'); ?></span>
                            </a>
                            <a href="#sidebar__primary" class="menu__item-trigger fullview-js for-desktop"
                                title="<?php echo Label::getLabel('LBL_MENU'); ?>">
                                <span class="icon icon--menu"><span class="toggle"><span></span></span></span>
                                <span class="sr-only"><?php echo Label::getLabel('LBL_MENU'); ?></span>
                            </a>
                        </li>
                        <li class="menu__item menu__item-home">
                            <a href="<?php echo MyUtility::makeUrl('Account'); ?>" class="menu__item-trigger"
                                title="<?php echo Label::getLabel('LBL_HOME'); ?>">
                                <svg class="icon icon--home">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#home'; ?>"></use>
                                </svg>
                                <span class="sr-only"><?php echo Label::getLabel('LBL_HOME'); ?></span>
                            </a>
                        </li>
                        <li
                            class="menu__item menu__item-messaging  <?php echo ($controllerName == 'Messages') ? 'is-active' : ''; ?>">
                            <a href="<?php echo MyUtility::makeUrl('Messages'); ?>"
                                class="menu__item-trigger message-badge"
                                title="<?php echo Label::getLabel('LBL_MESSAGING'); ?>">
                                <!-- add  data-count="{count}" if any unread message -->
                                <svg class="icon icon--messaging">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#message'; ?>">
                                    </use>
                                </svg>
                                <span class="sr-only"><?php echo Label::getLabel('LBL_MESSAGING'); ?></span>
                            </a>
                        </li>
                        <li
                            class="menu__item menu__item-notifications <?php echo ($controllerName == 'Notifications') ? 'is-active' : ''; ?> ">
                            <a href="<?php echo MyUtility::makeUrl('Notifications'); ?>"
                                class="menu__item-trigger notification-badge"
                                title="<?php echo Label::getLabel('LBL_NOTIFICATONS'); ?>">
                                <!-- add  data-count="{count}" if any unread Notificatons -->
                                <svg class="icon icon--notificatons">
                                    <use
                                        xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#notification'; ?>">
                                    </use>
                                </svg>
                                <span class="sr-only"><?php echo Label::getLabel('LBL_NOTIFICATONS'); ?></span>
                            </a>
                        </li>
                        <?php if (!empty($siteLanguages) || !empty($siteCurrencies)) { ?>
                            <li class="menu__item menu__item-languages">
                                <a href="#languages-nav" class="menu__item-trigger trigger-js"
                                    title="<?php echo Label::getLabel('LBL_LANGUAGES/CURRENCIES'); ?>">
                                    <svg class="icon icon--lang">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#language'; ?>">
                                        </use>
                                    </svg>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_LANGUAGES/CURRENCIES'); ?></span>
                                </a>
                                <div id="languages-nav" class="menu__dropdown">
                                    <div class="menu__dropdown-head">
                                        <span
                                            class="uppercase small bold-600"><?php echo Label::getLabel('LBL_CHANGE_LANGUAGES'); ?></span>
                                    </div>
                                    <div class="menu__dropdown-body">
                                        <nav class="menu menu--inline">
                                            <ul>
                                                <?php foreach ($siteLanguages as $language) { ?>
                                                    <li
                                                        class="menu__item <?php echo ($siteLangId == $language['language_id']) ? 'is-active' : ''; ?>">
                                                        <a href="javascript:void(0)" <?php echo ($siteLangId != $language['language_id']) ? 'onclick="setSiteLanguage(' . $language['language_id'] . ')"' : ''; ?>><?php echo $language['language_name']; ?></a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <hr />
                                            <ul>
                                                <?php foreach ($siteCurrencies as $currency) { ?>
                                                    <li
                                                        class="menu__item <?php echo ($siteCurrency['currency_id'] == $currency['currency_id']) ? 'is-active' : ''; ?>">
                                                        <a <?php echo ($siteCurrency['currency_id'] != $currency['currency_id']) ? 'onclick="setSiteCurrency(' . $currency['currency_id'] . ')"' : ''; ?>
                                                            href="javascript:void(0);"><?php echo $currency['currency_code']; ?></a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ($siteUserType == User::LEARNER) { ?>
                            <li
                                class="menu__item menu__item-favorites <?php echo ($controllerName == 'Learner' && $actionName == 'favourites') ? 'is-active' : ''; ?>">
                                <a href="<?php echo MyUtility::makeUrl('Learner', 'favourites'); ?>"
                                    class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_FAVORITES'); ?>">
                                    <svg class="icon icon--favorites">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#favorite'; ?>">
                                        </use>
                                    </svg>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_FAVORITES'); ?></span>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="menu__item menu__item-logout">
                            <a href="<?php echo MyUtility::makeUrl('Account', 'logout', [], CONF_WEBROOT_DASHBOARD); ?>"
                                class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_LOGOUT'); ?>">
                                <svg class="icon icon--logout">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#logout'; ?>">
                                    </use>
                                </svg>
                                <span class="sr-only"><?php echo Label::getLabel('LBL_LOGOUT'); ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- ] -->
            <!-- [ SIDE BAR PRIMARY ========= -->
            <div id="sidebar__primary" class="sidebar__primary">
                <div class="sidebar__head">
                    <figure class="logo">
                        <a href="<?php echo MyUtility::makeUrl('', '', [], CONF_WEBROOT_FRONT_URL); ?>">
                            <?php if (MyUtility::isDemoUrl()) { ?>
                                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                            <?php } else { ?>
                                <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', array(Afile::TYPE_FRONT_LOGO, 0, Afile::SIZE_LARGE), CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg'); ?>"
                                    alt="<?php echo $websiteName; ?>">
                            <?php } ?>
                        </a>
                    </figure>
                    <?php if (!isset($flashcardSrchFrm)) { ?>
                        <!-- [ PROFILE ========= -->
                        <div class="profile">
                            <a href="#profile-target" class="trigger-js profile__trigger">
                                <div class="profile__meta d-flex align-items-center">
                                    <div class="profile__media margin-right-4">
                                        <div class="avtar"
                                            data-title="<?php echo CommonHelper::getFirstChar($siteUser['user_first_name']); ?>">
                                            <?php echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $siteUserId, Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '?t=' . time() . '" alt="' . $siteUser['user_first_name'] . '" />'; ?>
                                        </div>
                                    </div>
                                    <div class="profile__details">
                                        <h6 class="profile__title">
                                            <?php echo $siteUser['user_first_name'] . ' ' . $siteUser['user_last_name']; ?>
                                        </h6>
                                        <!-- <?php $loggedAs = ($siteUserType == User::TEACHER) ? 'LBL_LOGGED_IN_AS_A_TEACHER' : 'LBL_LOGGED_IN_AS_A_LEARNER'; ?>
                                        <small class="color-black"><?php echo Label::getLabel($loggedAs); ?></small> -->
<?php
// use the variables already defined above the <body> (do NOT redefine them here)
if ($isParentMode && $isUserParent && strtolower($controllerName) === 'parent') {
    $loggedAsText = 'Logged In As A Parent';
} else {
    $loggedAsKey = ($siteUserType == User::TEACHER) ? 'LBL_LOGGED_IN_AS_A_TEACHER' : 'LBL_LOGGED_IN_AS_A_LEARNER';
    $loggedAsText = Label::getLabel($loggedAsKey);
}
?>
<small class="color-black"><?php echo $loggedAsText; ?></small>


                                    </div>
                                </div>
                            </a>
                            <div id="profile-target" class="profile__target">
                                <div class="profile__target-details">
                                    <table>
                                        <?php if (!empty($siteUser['country_name'])) { ?>
                                            <tr>
                                                <th><?php echo label::getLabel('LBL_LOCATION'); ?></th>
                                                <td><?php echo $siteUser['country_name']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th><?php echo label::getLabel('LBL_TIME_ZONE'); ?></th>
                                            <td>
                                                <?php echo MyDate::formatDate(date('Y-m-d H:i:s'), 'H:i', $siteUser['user_timezone']); ?>
                                                (<?php echo Label::getLabel('LBL_UTC') . " " . (new DateTime("now", new DateTimeZone($siteUser['user_timezone'])))->format('P'); ?>)
                                            </td>
                                        </tr>
                                    </table>
                                    <span class="-gap-10"></span>
                                    <div class="btns-group">
                                        <?php if ($siteUserType == User::TEACHER) { ?>
                                            <?php if (!empty($profileProgress['isProfileCompleted'])) { ?>
                                                <a href="<?php echo MyUtility::makeFullUrl('teachers', 'view', [$siteUser['user_username']], CONF_WEBROOT_FRONTEND); ?>"
                                                    class="btn btn--bordered color-third btn--block margin-top-2"><?php echo label::getLabel('LBL_View_Public_Profile'); ?></a>
                                            <?php } ?>
                                            <!-- <a href="<?php echo MyUtility::makeUrl('Learner'); ?>"
                                                class="btn btn--third btn--block margin-top-4"><?php echo label::getLabel('LBL_Switch_to_Learner_Profile'); ?></a> -->
                                                <a href="<?php echo MyUtility::makeUrl('Account', 'switchToLearnerProfile', [], CONF_WEBROOT_DASHBOARD); ?>"
   class="btn btn--third btn--block margin-top-4">
   <?php echo label::getLabel('LBL_Switch_to_Learner_Profile'); ?>
</a>

                                            <?php
                                        }
                                        if ($siteUserType == User::LEARNER && ($siteUser['user_is_teacher'] == AppConstant::YES || $siteUser['user_registered_as'] == User::TEACHER)) {
                                            ?>
                                            <!-- <a href="<?php echo MyUtility::makeUrl('Teacher'); ?>"
                                                class="btn btn--third btn--block margin-top-4"><?php echo label::getLabel('LBL_Switch_to_Teacher_Profile'); ?></a> -->
                                                <a href="<?php echo MyUtility::makeUrl('Account', 'switchToTeacherProfile', [], CONF_WEBROOT_DASHBOARD); ?>"
   class="btn btn--third btn--block margin-top-4">
   <?php echo label::getLabel('LBL_Switch_to_Teacher_Profile'); ?>
</a>

                                        <?php }
                                        ?>
                                        <!-- <?php
$isParentMode = (!empty($_SESSION['RWU_DASHBOARD_ROLE']) && $_SESSION['RWU_DASHBOARD_ROLE'] === 'parent');
?> -->

<?php if (!empty($siteUser['user_is_parent']) && $siteUser['user_is_parent'] == AppConstant::YES) { ?>
    <?php if (!$isParentMode) { ?>
        <a href="<?php echo MyUtility::makeUrl('Account', 'switchToParentProfile', [], CONF_WEBROOT_DASHBOARD); ?>"
           class="btn btn--third btn--block margin-top-4">
            Switch to Parent Profile
        </a>
    <?php } else { ?>
        <a href="<?php echo MyUtility::makeUrl('Account', 'switchToLearnerProfile', [], CONF_WEBROOT_DASHBOARD); ?>"
           class="btn btn--third btn--block margin-top-4">
            Switch to Learner Profile
        </a>
    <?php } ?>
<?php } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                    <?php }
                    ?>
                </div>
                <div class="sidebar__body">
                    <div class="sidebar__scroll">
                        <div id="primary-nav" class="menu-offset">
                            <!-- Display flashcard list on left sidebar in lesson view page  -->
                            <!-- <?php
                            $templateVariable = ['controllerName' => $controllerName, 'action' => $actionName, 'siteUser' => $siteUser, 'siteUserType' => $siteUserType];
                            $sidebarMenuLayout = '_partial/learner-sidebar.php';
                            if ($siteUserType == User::TEACHER) {
                                $templateVariable['tpp'] = $siteUser['profile_progress'];
                                $sidebarMenuLayout = '_partial/teacher-sidebar.php';
                            }
                            if (isset($flashcardSrchFrm)) {
                                $templateVariable['flashcardSrchFrm'] = $flashcardSrchFrm;
                                $sidebarMenuLayout = '_partial/flashcard-sidebar.php';
                            }
                            $this->includeTemplate($sidebarMenuLayout, $templateVariable);
                            ?> -->
                            <?php 
                            $templateVariable = ['controllerName' => $controllerName, 'action' => $actionName, 'siteUser' => $siteUser, 'siteUserType' => $siteUserType];

// $isParentMode = (!empty($_SESSION['RWU_DASHBOARD_ROLE']) && $_SESSION['RWU_DASHBOARD_ROLE'] === 'parent');

$sidebarMenuLayout = '_partial/learner-sidebar.php';

if ($siteUserType == User::TEACHER) {
    $templateVariable['tpp'] = $siteUser['profile_progress'];
    $sidebarMenuLayout = '_partial/teacher-sidebar.php';
}

// ONLY show parent sidebar when you are in Parent controller
if (strtolower($controllerName) === 'parent' && $isUserParent) {
    $sidebarMenuLayout = '_partial/parent-sidebar.php';
}


if (isset($flashcardSrchFrm)) {
    $templateVariable['flashcardSrchFrm'] = $flashcardSrchFrm;
    $sidebarMenuLayout = '_partial/flashcard-sidebar.php';
}

$this->includeTemplate($sidebarMenuLayout, $templateVariable);
 ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ] -->
        </aside>
        <!-- ] -->
        <main class="page">
            <?php $this->includeTemplate('_partial/subscription-alert.php'); ?>