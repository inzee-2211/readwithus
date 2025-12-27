<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php $headerClasses = strtolower($controllerName) . ' ' . strtolower($controllerName) . '-' . strtolower($actionName); ?>
<?php
// File: application/views/_partial/header.php
// Add this near other CSS/JS includes

$mathEditorEnabled = defined('CONF_ENABLE_MATH_EDITOR') && CONF_ENABLE_MATH_EDITOR;
?>


<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" class="<?php echo MyUtility::isDemoUrl() ? 'sticky-demo-header' : ''; ?>">

    <head>
        
        <meta charset="utf-8">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0,user-scalable=0" />
        <?php
// =========================
// Dynamic SEO defaults
// =========================
$metaTitle = $pageTitle ?? (FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId, FatUtility::VAR_STRING, '') ?: 'Read With Us');
$metaDesc  = $pageDescription ?? FatApp::getConfig('CONF_META_DESCRIPTION', FatUtility::VAR_STRING, '');
$canonical = $canonicalUrl ?? MyUtility::makeFullUrl();
$robots    = $metaRobots ?? 'index,follow';
$ogImg     = $ogImage ?? (CONF_WEBROOT_FRONT_URL . 'images/logo.png');
$twSite    = $twitterSite ?? '@read_withus';
?>

<title><?php echo htmlspecialchars($metaTitle); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($metaDesc); ?>">
<meta name="robots" content="<?php echo htmlspecialchars($robots); ?>">
<link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($ogImg); ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="<?php echo htmlspecialchars($twSite); ?>">
<meta name="twitter:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($ogImg); ?>">

<?php if (!empty($structuredData)) { ?>
<script type="application/ld+json"><?php echo $structuredData; ?></script>
<?php } ?>

        <?php if ($mathEditorEnabled): ?>
            <!-- Math Editor System -->
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/mathlive/dist/mathlive-static.css" />
<script src="https://cdn.jsdelivr.net/npm/mathlive/dist/mathlive.min.js"></script>

<!-- MathJax v3 for LaTeX rendering -->
<script>
window.MathJax = {
    tex: {
        inlineMath: [['\\(', '\\)']],
        displayMath: [['\\[', '\\]']],
        processEscapes: true,
        processEnvironments: true
    },
    options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
        ignoreHtmlClass: 'no-mathjax',
        renderActions: {
            addMenu: [],
            checkLoading: []
        }
    },
    startup: {
        pageReady: () => {
            return MathJax.startup.defaultPageReady().then(() => {
                console.log('MathJax initialized');
            });
        }
    }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<!-- Our Math Editor JS -->
<script src="<?php echo CONF_WEBROOT_FRONT_URL; ?>js/math-editor.js"></script>

<!-- Inline Styles for Math Editor -->
<style>
/* Math Editor Styles */
.rwu-math-wrapper {
    position: relative;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #f9fafb;
    padding: 12px;
}

.rwu-mathfield {
    min-height: 40px;
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    font-family: "Cambria Math", "Latin Modern Math", STIXGeneral, serif;
}

.rwu-mathfield:focus {
    outline: 2px solid #2DADFF;
    outline-offset: 2px;
}

.rwu-math-clear {
    position: absolute;
    right: 12px;
    top: 12px;
    border: none;
    background: #f3f4f6;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px;
    cursor: pointer;
    color: #6b7280;
}

.rwu-math-clear:hover {
    background: #e5e7eb;
    color: #374151;
}

.rwu-math-raw {
    font-size: 11px;
    color: #6b7280;
    margin-top: 8px;
    padding: 4px;
    background: #f8f9fa;
    border-radius: 4px;
    word-break: break-all;
    max-height: 60px;
    overflow-y: auto;
}

.math-field-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
}

/* Ensure virtual keyboard stays on top */
.ML__virtual-keyboard {
    z-index: 9999 !important;
}

/* LaTeX rendering styles */
.latex-render {
    display: inline-block;
    margin: 2px 0;
    padding: 2px 4px;
    vertical-align: middle;
}

.latex-render-error {
    color: #dc3545;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 6px;
    border-radius: 4px;
    font-family: monospace;
}
</style>
<?php endif; ?>
        <?php
// Keep YoCoach meta tags only if controller didn't provide custom SEO
if (empty($pageTitle) && empty($pageDescription)) {
    echo $this->writeMetaTags();
}
?>

        <link rel="shortcut icon" href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_FAVICON, 0, Afile::SIZE_ORIGINAL]); ?>">
        <link rel="apple-touch-icon" href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_APPLE_TOUCH_ICON, 0, Afile::SIZE_LARGE]); ?>">
        <?php if (!empty($canonicalUrl)) { ?>
            <link rel="canonical" href="<?php echo $canonicalUrl; ?>" />
        <?php } ?>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
        <script type="text/javascript">
            var langLbl = <?php echo json_encode(CommonHelper::htmlEntitiesDecode($jsVariables)); ?>;
            var timeZoneOffset = '<?php echo MyDate::getOffset($siteTimezone); ?>';
            var layoutDirection = '<?php echo $siteLanguage['language_direction']; ?>';
            var currencySymbolLeft = '<?php echo $siteCurrency['currency_symbol_left']; ?>';
            var currencySymbolRight = '<?php echo $siteCurrency['currency_symbol_right']; ?>';
            var SslUsed = '<?php echo FatApp::getConfig('CONF_USE_SSL'); ?>';
            var cookieConsent = <?php echo json_encode($cookieConsent); ?>;
            const confWebRootUrl = '<?php echo CONF_WEBROOT_URL; ?>';
            const confFrontEndUrl = '<?php echo CONF_WEBROOT_URL; ?>';
            const confWebDashUrl = '<?php echo CONF_WEBROOT_DASHBOARD; ?>';
            const FTRAIL_TYPE = '<?php echo Lesson::TYPE_FTRAIL; ?>';
            var ALERT_CLOSE_TIME = <?php echo FatApp::getConfig("CONF_AUTO_CLOSE_ALERT_TIME"); ?>;

            
<?php

 

    function getBaseUrl(): string
    {
        // Prefer configured constant if present
        if (defined('CONF_WEBROOT_FRONTEND') && CONF_WEBROOT_FRONTEND) {
            return rtrim(CONF_WEBROOT_FRONTEND, '/') . '/';
        }

        // Detect scheme safely (supports proxies)
        $scheme = 'http';
        if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) {
            $scheme = 'https';
        }

        // Host
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Path to the running script (eg. /SDH/readwithus/public)
        $script = $_SERVER['SCRIPT_NAME'] ?? '/';
        $dir    = rtrim(str_replace('\\', '/', dirname($script)), '/');

        // Normalize: root dir should be empty (not '/')
        $path = ($dir === '' || $dir === '/') ? '/' : $dir . '/';

        return $scheme . '://' . $host . $path;
    }

/**
 * Note var names of monthNames, weekDayNames and meridiems must not be changed
 */

 
if (isset($setMonthAndWeekNames) && $setMonthAndWeekNames) {
    ?>
                var monthNames = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::getAllMonthName(false, $siteLangId))); ?>;
                var weekDayNames = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::dayNames(false, $siteLangId))); ?>;
                var meridiems = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::meridiems(false, $siteLangId))); ?>;
<?php } ?>
        </script>
          <link rel="stylesheet" type="text/css" href="<?php echo getBaseUrl(); ?>assets/css/bootstrap.min.css" />
                  
       
       <link rel="stylesheet" type="text/css" href="<?php echo getBaseUrl(); ?>assets/css/style.css" />


        <link rel="stylesheet" type="text/css" href="<?php echo getBaseUrl(); ?>assets/css/responsive.css" />
       <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />  -->
        <?php if (!empty($includeEditor)) { ?>
            <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
            <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js"></script>
        <?php } ?>
        <?php
        
        
        if (FatApp::getConfig('CONF_ENABLE_PWA')) { ?>
            <link rel="manifest" href="<?php echo MyUtility::makeUrl('Pwa'); ?>">




            <script>
                if ("serviceWorker" in navigator) {
                    navigator.serviceWorker.register("<?php echo CONF_WEBROOT_FRONTEND; ?>sw.js");
                }
            </script>
        <?php } ?>
        <?php
         
        echo $this->getJsCssIncludeHtml(!CONF_DEVELOPMENT_MODE);
        echo Common::setThemeColorStyle();
        ?>
        <script>
             
            $(document).ready(function () {
<?php if ($siteUserId > 0) { ?>
                    setTimeout(getBadgeCount(), 1000);
<?php }if (!empty($messageData['msgs'][0] ?? '')) { ?>
                    fcom.success('<?php echo $messageData['msgs'][0]; ?>');
<?php }if (!empty($messageData['dialog'][0] ?? '')) { ?>
                    fcom.warning('<?php echo $messageData['dialog'][0]; ?>');
<?php }if (!empty($messageData['errs'][0] ?? '')) { ?>
                    fcom.error('<?php echo $messageData['errs'][0]; ?>');
<?php } ?>
            });
        </script>
        <script src="<?php echo getBaseUrl(); ?>assets/js/bootstrap.bundle.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script> -->

<script src="<?php echo getBaseUrl(); ?>assets/js/custom-web.js"></script>
         <!-- OG Product Facebook Meta [ -->
    <!-- ]   -->
    <!--Here is the Twitter Card code for this product  -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@read_withus">
    <meta name="twitter:title" content="Online Reading Tutors Platform | Tutors for PreK, Kindergarten, & Other Age Kids.">
    <meta name="twitter:description" content="Read With US, online reading tutors platform for Preschool kids, kindergarten, & Year 4-Year 9 students to increase their reading fluency, vocabulary & comprehension. Explore 1-to-1 live sessions on English & other languages with top-class tutors.">
    <meta name="twitter:image" content="http://readwithus.org.uk/images/online-reading-tutors-for-your-child.png">
    <!-- End Here is the Twitter Card code for this product  -->
    <script type='application/ld+json' defer>
        {
            "@context": "http://schema.org/",
            "@type": "Organization",
            "aggregateRating": {
                "@type": "AggregateRating",
                "reviewCount": "30",
                "ratingValue": "4.9",
                "worstRating": "1",
                "bestRating": "5"
            },
            "description": "Read with Us is an online tutoring platforms for students under 18 Age.",
            "url": "http://readwithus.org.uk",
            "telephone": "01634936520",
            "logo": {
                "@type": "ImageObject",
                "caption": "Read With Us",
                "contentUrl": ""
            },
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "14 Thirlmere Close Wainscott",
                "addressLocality": "Rochester",
                "postalCode": "ME2 4PA",
                "addressRegion": "United Kingdom",
                "name": "Read With Us"
            },
            "sameAs": ["https://www.facebook.com/readwithusofficial", "https://www.instagram.com/readwithusuk",
                "https://www.linkedin.com/company/read-with-us-uk", "https://www.youtube.com/channel/UCrI2mrU0_Znh2xTmBt22Zbg"
            ],
            "name": "Read With Us"
        }
    </script>
    <meta name="google-site-verification" content="M7iSjIdNqfAi4D3JntWPkrV_C4of_m8UanLTR1ObFU0" />
    </head>
    <?php $isPreviewOn = MyUtility::isDemoUrl() ? 'is-preview-on' : ''; ?>

    <body class="<?php echo $headerClasses . ' ' . $isPreviewOn; ?>" dir="<?php echo $siteLanguage['language_direction']; ?>">
        <!-- Custom Loader -->
        <div id="app-alert" class="alert-position alert-position--top-right fadeInDown animated"></div>
        <?php
        if (MyUtility::isDemoUrl()) {
            include(CONF_INSTALLATION_PATH . 'restore/view/header-bar.php');
        }
        if (isset($_SESSION['preview_theme'])) {
            $this->includeTemplate('_partial/preview.php', array(), false);
        }
        $websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId, FatUtility::VAR_STRING, '');
        if (!isset($exculdeMainHeaderDiv)) {
            ?>
            <header class="header">
                <div class="header-primary">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="header__left">
                                <a href="javascript:void(0)" class="toggle toggle--nav toggle--nav-js">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 515.555 515.555">
                                    <path d="m303.347 18.875c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" />
                                    <path d="m303.347 212.209c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" />
                                    <path d="m303.347 405.541c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" />
                                    </svg>
                                </a>
                                <div class="header__logo">
                                   <a href="<?php echo MyUtility::makeUrl(); ?>" title="<?php echo $websiteName; ?>">
        <img src="<?php echo CONF_WEBROOT_FRONT_URL . 'images/logo.png'; ?>"
             alt="<?php echo $websiteName; ?>">
    </a>
                                </div>

                                
                                <div class="header-dropdown header-dropdown--explore">
                                    
                                    <div id="explore" class="header-dropdown__target">
                                        <div class="dropdown__cover">
                                            <nav class="menu--inline">
                                                <ul>
                                                    <?php foreach ($teachLangs as $teachLangId => $teachlang) { ?>
                                                        <li class="menu__item">
                                                            <a href="<?php echo MyUtility::makeUrl('Teachers', 'languages', [$teachlang['tlang_slug']], CONF_WEBROOT_FRONTEND); ?>"><?php echo $teachlang['tlang_name']; ?></a>
                                                        </li>
                                                        <li class="menu__item">
                                                            <a href="<?php echo MyUtility::makeUrl('Teachers', 'languages', [$teachlang['tlang_slug']], CONF_WEBROOT_FRONTEND); ?>"><?php echo $teachlang['tlang_name']; ?></a>
                                                        </li>
                                                    <?php } ?>
                                                        <li class="menu__item">
                                                            <a href="//apply-to-teach">Apply to Teach</a>
                                                        </li>
                                                        <li class="menu__item">
                                                            <a href="/partner">Partner</a>
                                                        </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="header__middle">
                                <?php if (!empty($headerNav)) { ?>
                                    <span class="overlay overlay--nav toggle--nav-js is-active"></span>
                                <nav class="menu nav--primary-offset">
  <ul>
      <li class="menu__item dropdown">
        <a href="javascript:void(0)" class="dropdown-toggle" id="openSelectorNav">Revise Your Topic </a>
        <ul class="dropdown-menu" id="dropDownOptionNav" style="display:none; position:absolute; background:#fff; color:#000; padding:10px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,.1);">
          <li style="padding:6px 10px; font-size:14px; color:#555;">Loading topics...</li>
        </ul>
      </li>
    <li class="menu__item"><a href="<?php echo MyUtility::makeUrl('courses'); ?>">Courses</a></li>
    <li class="menu__item"><a href="<?php echo MyUtility::makeUrl('pricing'); ?>">Pricing</a></li>
    <li class="menu__item"><a href="<?php echo MyUtility::makeUrl('teachers'); ?>">Find a Tutor</a></li>
    <li class="menu__item"><a href="<?php echo MyUtility::makeUrl('apply-to-teach'); ?>">Apply as Instructor</a></li>

    <!-- Revise Topic dropdown -->
  </ul>
</nav>
                                <?php } ?>
                            </div>


                            
                            <div class="header__right">
                                <div class="header-controls">
                                    <div class="header-controls__item">
                                        <a href="<?php echo MyUtility::makeUrl('', '', [], CONF_WEBROOT_FRONTEND); ?>" class="header-controls__action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16.076" viewBox="0 0 18 16.076">
                                            <path d="M15.727,17.428H4.273a.818.818,0,0,1-.818-.818V9.246H1L9.449,1.565a.818.818,0,0,1,1.1,0L19,9.246H16.545v7.364A.818.818,0,0,1,15.727,17.428Zm-4.909-1.636h4.091V7.738L10,3.275,5.091,7.738v8.053H9.182V10.882h1.636Z" transform="translate(-1 -1.352)" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="header-controls__item header-dropdown header-dropdown--arrow">
                                        <a class="header-controls__action header-dropdown__trigger trigger-js" href="#languages-nav">
                                            <svg class="icon icon--globe">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#globe'; ?>"></use>
                                            </svg>
                                            <span class="lang"><?php echo $siteLanguage['language_code'] . ' - ' . $siteCurrency['currency_code']; ?></span>
                                            <svg class="icon icon--arrow">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#arrow-black' ?>"></use>
                                            </svg>
                                        </a>
                                        <div id="languages-nav" class="header-dropdown__target">
                                            <div class="dropdown__cover">
                                                <div class="settings-group">
                                                    <?php if (count($siteLanguages) > 1) { ?>
                                                        <div class="settings toggle-group">
                                                            <div class="dropdaown__title"><?php echo Label::getLabel('LBL_SITE_LANGUAGE') ?></div>
                                                            <a class="btn btn--bordered color-black btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $siteLanguage['language_name']; ?></a>
                                                            <div class="settings__target settings__target-js" style="display: none;">
                                                                <ul>
                                                                    <?php foreach ($siteLanguages as $language) { ?>
                                                                        <li <?php echo ($siteLangId == $language['language_id']) ? 'class="is--active"' : ''; ?>>
                                                                            <a <?php echo ($siteLangId != $language['language_id']) ? 'onclick="setSiteLanguage(' . $language['language_id'] . ')"' : ''; ?> href="javascript:void(0)"><?php echo $language['language_name']; ?></a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (count($siteCurrencies) > 1) { ?>
                                                        <div class="settings toggle-group">
                                                            <div class="dropdaown__title"><?php echo Label::getLabel('LBL_SITE_CURRENCY'); ?></div>
                                                            <a class="btn btn--bordered color-black btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $siteCurrency['currency_name']; ?></a>
                                                            <div class="settings__target settings__target-js" style="display: none;">
                                                                <ul>
                                                                    <?php foreach ($siteCurrencies as $currency) { ?>
                                                                        <li <?php echo ($siteCurrency['currency_id'] == $currency['currency_id']) ? 'class="is--active"' : ''; ?>>
                                                                            <a <?php echo ($siteCurrency['currency_id'] != $currency['currency_id']) ? 'onclick="setSiteCurrency(' . $currency['currency_id'] . ')"' : ''; ?> href="javascript:void(0);"><?php echo $currency['currency_code']; ?></a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($siteUserId > 0) { ?>
                                        <div class="header-controls__item header--notification">
                                            <a href="<?php echo MyUtility::makeUrl('Notifications', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="header-controls__action" title="<?php echo Label::getLabel('LBL_NOTIFICATIONS'); ?>">
                                                <span class="notification-count-js"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.8" viewBox="0 0 16 16.8">
                                                <path d="M16.4,14H18v1.6H2V14H3.6V8.4a6.4,6.4,0,0,1,12.8,0Zm-1.6,0V8.4a4.8,4.8,0,0,0-9.6,0V14ZM7.6,17.2h4.8v1.6H7.6Z" transform="translate(-2 -2)" />
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="header-controls__item header--message">
                                            <a href="<?php echo MyUtility::makeUrl('Messages', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="header-controls__action" title="<?php echo Label::getLabel('LBL_MESSAGES'); ?>">
                                                <span class="message-count-js"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.4" viewBox="0 0 16 14.4">
                                                <path d="M2.8,3H17.2a.8.8,0,0,1,.8.8V16.6a.8.8,0,0,1-.8.8H2.8a.8.8,0,0,1-.8-.8V3.8A.8.8,0,0,1,2.8,3ZM16.4,6.39l-6.342,5.68L3.6,6.373V15.8H16.4ZM4.009,4.6l6.04,5.33L16,4.6Z" transform="translate(-2 -3)" />
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="header-dropdown header-dropwown--profile">
                                            <a class="header-dropdown__trigger trigger-js" href="#profile-nav">
                                                <div class="teacher-profile">
                                                    <div class="teacher__media">
                                                        <div class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar($siteUser['user_first_name']); ?>">
                                                            <?php echo '<img src="' . MyUtility::makeUrl('Image', 'show', array(Afile::TYPE_USER_PROFILE_IMAGE, $siteUserId, Afile::SIZE_SMALL)) . '?' . time() . '" alt="" />'; ?>
                                                        </div>
                                                    </div>
                                                    <div class="teacher__name"><?php echo $siteUser['user_first_name']; ?></div>
                                                    <svg class="icon icon--arrow">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#arrow-black' ?>"></use>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div id="profile-nav" class="header-dropdown__target">
                                                <div class="dropdown__cover">
                                                    <nav class="menu--inline">
                                                        <ul>
                                                            <?php if ($siteUserType == User::TEACHER) { ?>
                                                                <li class="menu__item <?php echo ("Teacher" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Teacher', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Dashboard'); ?></a></li>
                                                                <li class="menu__item <?php echo ("Students" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Students', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_My_Students'); ?></a></li>
                                                                <li class="menu__item <?php echo ("Lessons" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Lessons', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Lessons'); ?></a></li>
                                                                <?php
                                                            }
                                                            if ($siteUserType == User::LEARNER) {
                                                                ?>
                                                                <li class="menu__item <?php echo ("Learner" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Learner', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Dashboard'); ?></a></li>
                                                                <li class="menu__item <?php echo ("Teachers" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Teachers', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_My_Teachers'); ?></a></li>
                                                                <li class="menu__item <?php echo ("Lessons" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Lessons', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Lessons'); ?></a></li>
                                                            <?php }
                                                            ?>
                                                            <li class="menu__item <?php echo ("Classes" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Classes', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Classes'); ?></a></li>
                                                            <li class="menu__item <?php echo ("Courses" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Courses', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Courses'); ?></a></li>
                                                            <li class="menu__item <?php echo ("Account" == $controllerName && "profileInfo" == $action) ? 'is-active' : ''; ?>"><a href="<?php echo MyUtility::makeUrl('Account', 'ProfileInfo', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Settings'); ?></a></li>
                                                            <li class="menu__item"><a href="<?php echo MyUtility::makeUrl('Account', 'logout', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Logout'); ?></a></li>
                                                        </ul>
                                                    </nav>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="header-controls__item header-action">
    <div class="header__action">
        <!-- Log in button -->
        <a href="javascript:void(0)" onClick="signinForm();"
           class="header-controls__action btn btn--bordered user-click"
           style="background-color: #ffffff; color: #2DADFF; border: 2px solid #2DADFF;">
            <?php echo Label::getLabel('LBL_Login'); ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18.375" viewBox="0 0 18.331 15.331" id="enter">
                <path d="M17.692 0h-11.5a.639.639 0 00-.639.639V5.11a.639.639 0 101.278 0V1.278h10.222v12.775H6.833v-3.832a.639.639 0 00-1.278 0v4.472a.639.639 0 00.639.639h11.5a.639.639 0 00.639-.639V.639A.639.639 0 0017.692 0z"></path>
                <path d="M9.936 9.769a.639.639 0 00.9.9l2.555-2.555q.022-.022.042-.046l.017-.023.02-.027.017-.028.015-.026.014-.029.013-.028c0-.009.007-.019.01-.029l.011-.03c.004-.01.005-.019.007-.029l.008-.032c.002-.011 0-.023.005-.034s0-.018 0-.028a.643.643 0 000-.126v-.028c0-.009 0-.023-.005-.034s-.005-.021-.008-.032 0-.019-.007-.029-.007-.02-.011-.03l-.01-.029c-.003-.01-.009-.018-.013-.028l-.014-.029-.015-.026-.017-.028-.02-.027-.017-.023q-.02-.024-.042-.046L10.84 4.659a.639.639 0 00-.9.9l1.46 1.468H.639A.639.639 0 000 7.666a.639.639 0 00.639.639H11.4z"></path>
            </svg>
        </a>

        <!-- Register button -->
        <a href="javascript:void(0)" onClick="signupForm();"
           class="btn btn--primary user-click"
           style="background-color:#2DADFF; color: #fff; border: 2px solid #6cb6e0;">
            <?php echo Label::getLabel('LBL_SIGN_UP'); ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18.375" viewBox="0 0 14 18.375">
                <path d="M18,19.375H16.25v-1.75A2.625,2.625,0,0,0,13.625,15H8.375A2.625,2.625,0,0,0,5.75,17.625v1.75H4v-1.75A4.375,4.375,0,0,1,8.375,13.25h5.25A4.375,4.375,0,0,1,18,17.625ZM11,11.5a5.25,5.25,0,1,1,5.25-5.25A5.25,5.25,0,0,1,11,11.5Zm0-1.75a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,11,9.75Z" transform="translate(-4 -1)" />
            </svg>
        </a>
    </div>
</div>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div id="body" class="body">
            <?php } ?>
<style>
/* anchor dropdown to its <li> */
.menu.nav--primary-offset .dropdown { position: relative; }

/* Revise button — brand blue and tighter near logo */
#openSelectorNav{
  display:inline-block;
  background:#2DADFF;
  color:#fff!important;
  font-weight:600;
  border-radius:6px;
  margin-top: 3%;
  padding:13px 18px;
  margin-left:-10%;   /* pulls closer to logo */
  margin-right:12px;
  border:2px solid transparent;
  letter-spacing:.2px;
  transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
}
#openSelectorNav:hover{
  background:#37b6ff;
  box-shadow:0 6px 16px rgba(45,173,255,.25);
  transform:translateY(-2px);
}

/* Dropdown container (CORRECT ID) */
#dropDownOptionNav{
  display:none;            /* shown/hidden by JS */
  position:absolute;
  top:44px; left:0;
  margin-top: 3%;
  margin-left:-12%;
  min-width:260px; max-height:420px; overflow-y:auto;
  background:rgba(255,255,255,.98);
  backdrop-filter:blur(6px);
  border-radius:12px;
  box-shadow:0 10px 24px rgba(0,0,0,.08);
  padding:10px 12px;
  z-index:9999;
  animation:navFadeIn .22s ease;
}

/* optional header/breadcrumb inside the menu */
#dropDownOptionNav .breadcrumb-nav span{
  font-size:13px; color:#1D9CFD; cursor:pointer;
}
#dropDownOptionNav .breadcrumb-nav span:hover{ color:#0A033C; }

/* reset default LI appearance */
#dropDownOptionNav li{ list-style:none; padding:0; margin:0; border:0; background:none; }

/* the clickable options rendered by JS */
#dropDownOptionNav .rtm-item{
  display:block; width:100%;
  appearance:none; -webkit-appearance:none; -moz-appearance:none; /* kill grey block */
  background:#F6FAFF;
  border:1px solid #E0F0FF;
  color:#0A033C;
  border-radius:8px;
  padding:10px 12px;
  margin:6px 0;
  font-size:14px; font-weight:600; text-align:left;
  cursor:pointer;
  transition:transform .15s ease, background .15s ease, border-color .15s ease;
}
#dropDownOptionNav .rtm-item:hover{
  background:#EAF5FF; border-color:#B3E0FF; transform:translateX(3px);
}
#dropDownOptionNav .rtm-item:focus{
  outline:0; box-shadow:0 0 0 3px rgba(45,173,255,.25);
}

/* little fade */
@keyframes navFadeIn{
  from{ opacity:0; transform:translateY(-6px); }
  to  { opacity:1; transform:translateY(0); }
}

/* mobile tweaks */
@media (max-width:768px){
  #openSelectorNav{ margin-left:0; margin-top:6px; }
  #dropDownOptionNav{ left:0; right:auto; width:calc(100vw - 32px); }
}
</style>

<script>
  window.RWU_CONFIG = window.RWU_CONFIG || {};
  window.RWU_CONFIG.baseUrl = '<?= getBaseUrl(); ?>';
</script>
<script src="<?= CONF_WEBROOT_URL ?>js/nav-revise.js"></script>
<style>
    /* Hide "Revise Your Topic" on mobile/tablet */
@media (max-width: 991.98px){
  /* hide the whole menu item (safest) */
  .menu.nav--primary-offset li.dropdown { 
    display: none !important; 
  }

  /* extra safety (in case markup changes) */
  #openSelectorNav,
  #dropDownOptionNav{
    display: none !important;
  }
}

</style>