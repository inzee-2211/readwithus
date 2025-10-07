<?php if (isset($includeEditor) && $includeEditor) { ?>
    <script   src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
    <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js" ></script>	
<?php } ?>
<script>
    var ALERT_CLOSE_TIME = <?php echo FatApp::getConfig("CONF_AUTO_CLOSE_ALERT_TIME"); ?>;
</script>
</head>
<?php $isPreviewOn = MyUtility::isDemoUrl() ? 'is-preview-on' : ''; ?>
<body class="<?php echo $bodyClass . ' ' . $isPreviewOn; ?>" dir="<?php echo $siteLanguage['language_direction']; ?>">
    <?php
    if (MyUtility::isDemoUrl()) {
        include(CONF_INSTALLATION_PATH . 'restore/view/header-bar.php');
    }
    ?>
    <div class="page-container"></div>
