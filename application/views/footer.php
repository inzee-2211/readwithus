<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$newsletter = false;
$apikey = FatApp::getConfig("CONF_MAILCHIMP_KEY");
$listId = FatApp::getConfig("CONF_MAILCHIMP_LIST_ID");
$prefix = FatApp::getConfig("CONF_MAILCHIMP_SERVER_PREFIX");
if (!empty($apikey) && !empty($listId) && !empty($prefix) && FatApp::getConfig('CONF_ENABLE_NEWSLETTER_SUBSCRIPTION')) {
    $newsletter = true;
}
if ($newsletter) {
    $form = MyUtility::getNewsLetterForm();
    $form->developerTags['colClassPrefix'] = 'col-sm-';
    $form->developerTags['fld_default_col'] = 12;
    $form->setFormTagAttribute('onsubmit', 'submitNewsletterForm(this); return false;');
    $emailFld = $form->getField('email');
    $emailFld->developerTags['noCaptionTag'] = true;
    $emailFld->addFieldTagAttribute('placeholder', Label::getLabel('LBL_ENTER_EMAIL'));
    $submitBtn = $form->getField('btnSubmit');
    $submitBtn->developerTags['noCaptionTag'] = true;
    $submitBtn->addFieldTagAttribute('class', 'btn btn--secondary col-12 no-gutter');
}
$sitePhone = FatApp::getConfig('CONF_SITE_PHONE');
$siteEmail = FatApp::getConfig('CONF_CONTACT_EMAIL');
$address = FatApp::getConfig('CONF_ADDRESS_' . $siteLangId, FatUtility::VAR_STRING, '');
?>
</div>
<footer class="footer">
    <section class="section section--footer">
        <div class="container container--narrow">
            <div class="row footer--row">
                <div class="col-md-6 col-lg-3">
                    <div class="footer-group toggle-group">
                        <div class="footer__group-title toggle-trigger-js">
                            <h5 class=""><?php echo Label::getLabel('LBL_SUPPORT') ?></h5>
                        </div>
                        <div class="footer__group-content toggle-target-js">
                            <div class="bullet-list">
                                <ul class="footer_contact_details">
                                    <li>
                                        <svg class="icon icon--email">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#email'; ?>"></use>
                                        </svg>
                                        <span><a href="mailto:<?php echo $siteEmail; ?>"> : <?php echo $siteEmail; ?></a></span>
                                    </li>
                                    <?php if (!empty($sitePhone)) { ?>
                                        <li>
                                            <svg class="icon icon--phone">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#phone'; ?>"></use>
                                            </svg>
                                            <span><a href="tel:<?php echo $sitePhone; ?>"> : <?php echo $sitePhone; ?></a></span>
                                        </li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($address)) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-group toggle-group">
                            <div class="footer__group-title toggle-trigger-js">
                                <h5><?php echo Label::getLabel('LBL_ADDRESS'); ?></h5>
                            </div>
                            <div class="footer__group-content toggle-target-js">
                                <div class="bullet-list">
                                    <ul class="footer_contact_details">
                                        <li>
                                            <svg class="icon icon--pin">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#pin'; ?>"></use>
                                            </svg>
                                            <span><?php echo FatApp::getConfig('CONF_ADDRESS_' . $siteLangId, FatUtility::VAR_STRING, ''); ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($socialPlatforms)) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-group toggle-group">
                            <div class="footer__group-title toggle-trigger-js">
                                <h5><?php echo Label::getLabel('LBL_SOCIAL'); ?></h5>
                            </div>
                            <div class="footer__group-content toggle-target-js">
                                <div class="bullet-list">
                                    <ul class="footer_social-links">
                                        <?php foreach ($socialPlatforms as $name => $link) { ?>
                                            <li>
                                                <a href="<?php echo $link; ?>" target="_blank">
                                                    <svg class="icon icon--email">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#' . strtolower($name); ?>"></use>
                                                    </svg>
                                                    <span><?php echo Label::getLabel('LBL_' . $name); ?></span>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-md-6 col-lg-3">
                    <div class="footer-group toggle-group">
                        <div class="footer__group-title toggle-trigger-js">
                            <h5 class=""><?php echo Label::getLabel('LBL_LANGUAGE_&_CURRENCY'); ?></h5>
                        </div>
                        <div class="footer__group-content toggle-target-js">
                            <div class="bullet-list">
                                <div class="settings-group">
                                    <div class="settings toggle-group">
                                        <a class="btn btn--bordered btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $siteLanguage['language_name']; ?></a>
                                        <div class="settings__target settings__target-js" style="display: none;">
                                            <ul>
                                                <?php foreach ($siteLanguages as $language) { ?>
                                                    <li <?php echo ($siteLangId == $language['language_id']) ? 'class="is--active"' : ''; ?>>
                                                        <a <?php echo ($siteLangId != $language['language_id']) ? 'onclick="setSiteLanguage(' . $language['language_id'] . ')"' : ''; ?> href="javascript:void(0)"><?php echo $language['language_name'] ?></a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="settings toggle-group">
                                        <a class="btn btn--bordered btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $siteCurrency['currency_code']; ?></a>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row footer--row">
                <?php if (!empty($footerOneNav)) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-group toggle-group">
                            <div class="footer__group-title toggle-trigger-js">
                                <h5 class=""><?php echo current($footerOneNav)['parent']; ?></h5>
                            </div>
                            <div class="footer__group-content toggle-target-js">
                                <div class="bullet-list">
                                    <ul>
                                        <?php
                                        foreach ($footerOneNav as $nav) {
                                            if ($nav['pages']) {
                                                foreach ($nav['pages'] as $link) {
                                                    $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']);
                                        ?>
                                                    <li>
                                                        <a target="<?php echo $link['nlink_target']; ?>" href="<?php echo $navUrl; ?>" class="bullet-list__action"><?php echo $link['nlink_caption']; ?></a>
                                                    </li>
                                        <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($footerTwoNav)) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-group toggle-group">
                            <div class="footer__group-title toggle-trigger-js">
                                <h5 class=""><?php echo current($footerTwoNav)['parent']; ?></h5>
                            </div>
                            <div class="footer__group-content toggle-target-js">
                                <div class="bullet-list">
                                    <ul>
                                        <?php
                                        foreach ($footerTwoNav as $nav) {
                                            if ($nav['pages']) {
                                                foreach ($nav['pages'] as $link) {
                                                    $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']);
                                        ?>
                                                    <li>
                                                        <a target="<?php echo $link['nlink_target']; ?>" href="<?php echo $navUrl; ?>" class="bullet-list__action"><?php echo $link['nlink_caption']; ?></a>
                                                    </li>
                                        <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($footerThreeNav)) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-group toggle-group">
                            <div class="footer__group-title toggle-trigger-js">
                                <h5 class=""><?php echo current($footerThreeNav)['parent']; ?></h5>
                            </div>
                            <div class="footer__group-content toggle-target-js">
                                <div class="bullet-list">
                                    <ul>
                                        <?php
                                        foreach ($footerThreeNav as $nav) {
                                            if ($nav['pages']) {
                                                foreach ($nav['pages'] as $link) {
                                                    $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']);
                                        ?>
                                                    <li>
                                                        <a target="<?php echo $link['nlink_target']; ?>" href="<?php echo $navUrl; ?>" class="bullet-list__action"><?php echo $link['nlink_caption']; ?></a>
                                                    </li>
                                        <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($newsletter) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-group toggle-group">
                            <div class="footer__group-title toggle-trigger-js">
                                <h5 class=""><?php echo Label::getLabel('LBL_SIGNUP_TO_NEWSLETTER'); ?></h5>
                            </div>
                            <div class="footer__group-content toggle-target-js">
                                <p><?php echo Label::getLabel('LBL_NEWSLETTER_DESCRITPTION'); ?></p>
                                <?php echo $form->getFormHtml(); ?>
                                <div class="email-field">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="row footer--row">
                <div class="col-md-12">
                    <div class="footer-group toggle-group">
                        <div class="footer__group-title toggle-trigger-js">
                            <h5><?php echo Label::getLabel('LBL_LANGUAGES'); ?></h5>
                        </div>
                        <div class="footer__group-content toggle-target-js">
                            <div class="footer__group-tag">
                                <?php foreach ($teachLangs as $teachLangId => $langName) { ?>
                                    <div class="tags-inline__item"><a href="<?php echo MyUtility::makeUrl('teachers', 'index', [$langName['tlang_slug']]); ?>"><?php echo $langName['tlang_name']; ?></a></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="section-copyright">
        <div class="container container--narrow">
            <div class="copyright">
                <div class="footer__logo">
                    <a href="<?php echo MyUtility::makeUrl(); ?>">
                        <?php if (MyUtility::isDemoUrl()) { ?>
                            <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/readwithus.png'; ?>" alt="" />
                        <?php } else { ?>
                            <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_FRONT_LOGO, 0, Afile::SIZE_MEDIUM]); ?>" alt="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId, FatUtility::VAR_STRING, ''); ?>">
                        <?php } ?>
                    </a>
                </div>
                 <!-- <div class="trustpilot-widget" style="width:100%" data-locale="en-GB" data-template-id="5419b6a8b0d04a076446a9ad" data-businessunit-id="64f2d20fd79983d2c4f6d24a" data-style-height="24px" data-style-width="100%" data-theme="light" data-min-review-count="10" data-without-reviews-preferred-string-id="1" data-style-alignment="center">
                        <a href="https://uk.trustpilot.com/review/readwithus.org.uk" target="_blank" rel="noopener">Trustpilot</a>
                </div>  -->

                <div class="trustpilot-widget"   data-locale="en-GB" data-template-id="5419b6a8b0d04a076446a9ad" data-businessunit-id="64f2d20fd79983d2c4f6d24a" data-style-height="24px" data-style-width="100%" data-theme="light" data-min-review-count="10" data-without-reviews-preferred-string-id="1" data-style-alignment="center">
                        <a href="https://uk.trustpilot.com/review/readwithus.org.uk" target="_blank" rel="noopener">Trustpilot</a>
                </div> 
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
</footer>

<a href="#top" class="gototop" title="Back to Top"></a>


<?php if (FatApp::getConfig('CONF_ENABLE_COOKIES', FatUtility::VAR_INT, 1) && empty($cookieConsent)) { ?>
    <div class="cc-window cc-banner cc-type-info cc-theme-block cc-bottom cookie-alert no-print">
        <?php if (FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')) { ?>
            <div class="box-cookies">
                <span id="cookieconsent:desc" class="cc-message">
                    <?php echo FatUtility::decodeHtmlEntities(FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')); ?>
                    <?php
                    $readMorePage = FatApp::getConfig('CONF_COOKIES_BUTTON_LINK', FatUtility::VAR_INT);
                    if ($readMorePage) {
                    ?>
                        <a href="<?php echo MyUtility::makeUrl('cms', 'view', [$readMorePage]); ?>"><?php echo Label::getLabel('LBL_READ_MORE'); ?></a></span>
            <?php } ?>
            </span>
            <a href="javascript:void(0)" class="cc-close" onClick="acceptAllCookies();"><?php echo Label::getLabel('LBL_ACCEPT_COOKIES'); ?></a>
            <a href="javascript:void(0)" class="cc-close" onClick="cookieConsentForm();"><?php echo Label::getLabel('LBL_CHOOSE_COOKIES'); ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php
if (FatApp::getConfig('CONF_ENABLE_LIVECHAT', FatUtility::VAR_STRING, '')) {
    echo FatApp::getConfig('CONF_LIVE_CHAT_CODE', FatUtility::VAR_STRING, '');
}
if (FatApp::getConfig('CONF_SITE_TRACKER_CODE', FatUtility::VAR_STRING, '') && !empty($cookieConsent[CookieConsent::STATISTICS])) {
    echo FatApp::getConfig('CONF_SITE_TRACKER_CODE', FatUtility::VAR_STRING, '');
}
?>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  -->

<!-- Then Owl Carousel -->
 <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script> 
<script src="<?php echo getBaseUrl(); ?>/assets/js/custom-web.js"></script>    -->


</body>

</html>