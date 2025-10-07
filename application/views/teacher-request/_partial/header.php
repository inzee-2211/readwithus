<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<script>
    var ALERT_CLOSE_TIME = <?php echo FatApp::getConfig("CONF_AUTO_CLOSE_ALERT_TIME"); ?>;
</script>
<header class="header">
    <div class="container container--narrow">
        <div class="header-primary">
            <div class="d-flex justify-content-between">
                <div class="header__left">
                    <div class="header__logo">
                        <a href="<?php echo MyUtility::makeUrl(); ?>">
                            <?php if (MyUtility::isDemoUrl()) { ?>
                                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                            <?php } else { ?>
                                <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_FRONT_LOGO, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONT_URL); ?>" alt="">
                            <?php } ?>
                        </a>
                    </div>
                </div>
                <div class="header__right">
                    <div class="head__action">
                        <a class="" href="<?php echo MyUtility::makeUrl('TeacherRequest', 'logoutGuestUser'); ?>">
                            <svg class="icon icon--logout">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#logout'; ?>"></use>
                            </svg>
                            <span><?php echo Label::getLabel('LBL_LOGOUT'); ?></span>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ] -->