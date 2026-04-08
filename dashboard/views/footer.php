<div class="page__footer">
    <div class="container container--fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="footer-nav">
                    <ul>
                        <li><a href="<?php echo MyUtility::makeUrl('', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_HOME'); ?></a></li>
                        <li><a href="<?php echo MyUtility::makeUrl('Teachers', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_FIND_A_TUTOR'); ?></a></li>
                        <?php if ($siteUser['user_is_teacher'] == AppConstant::NO) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('TeacherRequest', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_APPLY_TO_TEACH'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <p class="small align-right">
                    <?php
                    if (MyUtility::isDemoUrl()) {
                        echo CommonHelper::replaceStringData(Label::getLabel('LBL_COPYRIGHT_TEXT'), ['{YEAR}' => '&copy; ' . date("Y"), '{PRODUCT}' => '<a target="_blank"  href="https://yo-coach.com">Yo!Coach</a>', '{OWNER}' => '<a target="_blank"  class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>']);
                    } else {
                        echo Label::getLabel('LBL_COPYRIGHT') . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . $siteLangId, FatUtility::VAR_STRING);
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
</main>
<!-- ] -->
</div>
<?php if (FatApp::getConfig('CONF_ENABLE_COOKIES') && empty($cookieConsent)) { ?>
    <div class="cc-window cc-banner cc-type-info cc-theme-block cc-bottom cookie-alert no-print">
        <?php if (FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')) { ?>
            <div class="box-cookies">
                <span id="cookieconsent:desc" class="cc-message">
                    <?php echo FatUtility::decodeHtmlEntities(FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')); ?>
                    <a href="<?php echo MyUtility::makeUrl('cms', 'view', [FatApp::getConfig('CONF_COOKIES_BUTTON_LINK')], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_READ_MORE'); ?></a></span>
                </span>
                <a href="javascript:void(0)" class="cc-close" onClick="acceptAllCookies();"><?php echo Label::getLabel('LBL_ACCEPT_COOKIES'); ?></a>
                <a href="javascript:void(0)" class="cc-close" onClick="cookieConsentForm(true);"><?php echo Label::getLabel('LBL_CHOOSE_COOKIES'); ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<script>
    $(".expand-js").click(function() {
        $(".expand-target-js").slideToggle();
    });
    $(".slide-toggle-js").click(function() {
        $(".slide-target-js").slideToggle();
    });
    /******** TABS SCROLL FUNCTION  ****************/
    moveToTargetDiv('.tabs-scrollable-js li.is-active', '.tabs-scrollable-js ul');
    $('.tabs-scrollable-js li').click(function() {
        $('.tabs-scrollable-js li').removeClass('is-active');
        $(this).addClass('is-active');
        moveToTargetDiv('.tabs-scrollable-js li.is-active', '.tabs-scrollable-js ul');
    });

    function moveToTargetDiv(target, outer) {
        var out = $(outer);
        var tar = $(target);
        var x = out.width();
        var y = tar.outerWidth(true);
        var z = tar.index();
        var q = 0;
        var m = out.find('li');
        for (var i = 0; i < z; i++) {
            q += $(m[i]).outerWidth(true);
        }
        out.animate({
            scrollLeft: Math.max(0, q)
        }, 800);
        return false;
    }
    $('.list-inline li').click(function() {
        $('.list-inline li').removeClass('is-active');
        $(this).addClass('is-active');
    });
    $(document).ready(function() {
        /* SIDE BAR SCROLL DYNAMIC HEIGHT */
        $('.sidebar__body').css('height', 'calc(100% - ' + $('.sidebar__head').innerHeight() + 'px');
        $(window).resize(function() {
            $('.sidebar__body').css('height', 'calc(100% - ' + $('.sidebar__head').innerHeight() + 'px');
        });
        /* COMMON TOGGLES */
        var _body = $('html');
        var _toggle = $('.trigger-js');
        _toggle.each(function() {
            var _this = $(this),
                _target = $(_this.attr('href'));
            _this.on('click', function(e) {
                e.preventDefault();
                _target.toggleClass('is-visible');
                _this.toggleClass('is-active');
                _body.toggleClass('is-toggle');
            });
        });
        /* FOR FULL SCREEN TOGGLE */
        var _body = $('html');
        var _toggle = $('.fullview-js');
        _toggle.each(function() {
            var _this = $(this),
                _target = $(_this.attr('href'));
            _this.on('click', function(e) {
                e.preventDefault();
                _target.toggleClass('is-visible');
                _this.toggleClass('is-active');
                _body.toggleClass('is-fullview');
            });
        });
    });
</script>
<?php
if (FatApp::getConfig('CONF_ENABLE_LIVECHAT', FatUtility::VAR_STRING, '')) {
    echo FatApp::getConfig('CONF_LIVE_CHAT_CODE', FatUtility::VAR_STRING, '');
}
if (FatApp::getConfig('CONF_SITE_TRACKER_CODE', FatUtility::VAR_STRING, '') && !empty($cookieConsent[CookieConsent::STATISTICS])) {
    echo FatApp::getConfig('CONF_SITE_TRACKER_CODE', FatUtility::VAR_STRING, '');
}
?>
</body>
<!-- Custom Loader -->
<div id="app-alert" class="alert-position alert-position--top-right fadeInDown animated"></div>
<script>
    <?php if ($siteUserId > 0) { ?>
        setTimeout(getBadgeCount(), 1000);
    <?php }
    if (Message::getMessageCount() > 0) { ?>
        fcom.success('<?php echo Message::getData()['msgs'][0]; ?>');
    <?php }
    if (Message::getDialogCount() > 0) { ?>
        fcom.warning('<?php echo Message::getData()['dialog'][0]; ?>');
    <?php }
    if (Message::getErrorCount() > 0) { ?>
        fcom.error('<?php echo Message::getData()['errs'][0]; ?>');
    <?php } ?>
</script>
</script>

</html>