<div class="section-layout__footer">
    <div class="container">
        <p>
            <?php
            if (MyUtility::isDemoUrl()) {
                echo CommonHelper::replaceStringData(Label::getLabel('LBL_COPYRIGHT_TEXT'), ['{YEAR}' => '&copy; ' . date("Y"), '{PRODUCT}' => '<a target="_blank"  href="https://yo-coach.com">Yo!Coach</a>', '{OWNER}' => '<a target="_blank"  class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>']);
            } else {
                echo Label::getLabel('LBL_COPYRIGHT') . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . MyUtility::getSiteLangId(), FatUtility::VAR_STRING);
            }
            ?>
        </p>
    </div>
</div>
</div>
</div>
<!-- ] -->
</div>
<!-- ] -->
</main>
</page>
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
    $('.responsive-toggle-js').click(function() {
        let index = $(this).index();
        $('.responsive-toggle-js').removeClass('is-active');
        $(this).addClass('is-active');
        $('.responsive-target-js').hide();
        $('.responsive-target-js').eq(index).show();
    });



    $(".control-target-js").hide();
    $(".control-target-js:first").show();
    $(".control-trigger-js").click(function(e) {
        e.preventDefault();
        if ($(this).parents('.control-group-js').hasClass('is-active')) {
            $(this).siblings('.control-target-js').slideUp();
            $('.control-group-js').removeClass('is-active');
        } else {
            $('.control-group-js').removeClass('is-active');
            $(this).parents('.control-group-js').addClass('is-active');
            $('.control-target-js').slideUp();
            $(this).siblings('.control-target-js').slideDown();
        }
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
</script>
</body>
<!-- Custom Loader -->
<div id="app-alert" class="alert-position alert-position--top-right">
    <alert role="alert" class="alert">
        <alert-icon class="alert__icon"></alert-icon>
        <alert-message class="alert__message">
            <p></p>
        </alert-message>
        <alert-close class="alert__close" onclick="$.appalert.close();"></alert-close>
    </alert>
</div>
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

</html>