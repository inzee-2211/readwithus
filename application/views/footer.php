<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$newsletter = false;
$apikey  = FatApp::getConfig("CONF_MAILCHIMP_KEY");
$listId  = FatApp::getConfig("CONF_MAILCHIMP_LIST_ID");
$prefix  = FatApp::getConfig("CONF_MAILCHIMP_SERVER_PREFIX");
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
$address   = FatApp::getConfig('CONF_ADDRESS_' . $siteLangId, FatUtility::VAR_STRING, '');
?>
</div>

<footer class="footer rwu-footer">
  <style>
    /* ===== RWU Footer (Figma) ===== */
    .rwu-footer, .rwu-footer .section--footer, .rwu-footer .section-copyright { background:#0E2F47 !important; }
    .rwu-footer *{ box-sizing:border-box; }
    .rwu-footer h5, .rwu-footer h4, .rwu-footer p, .rwu-footer li, .rwu-footer a, .rwu-footer span { color:#D6E8F5; }
    .rwu-footer h5{ color:#ffffff; font-weight:700; letter-spacing:-.2px; }
    .rwu-footer a:hover{ color:#2DADFF; }

    /* Band 1: Join Us + Newsletter */
    .rwu-footer__cta{ padding:50px 0 24px; border-bottom:1px solid #374151; }
    .rwu-footer__cta .cta-grid{ display:grid; grid-template-columns:1fr 420px; gap:24px; align-items:center; }
    .rwu-footer__cta h4{ margin:0 0 8px; color:#fff; font:700 20px/24px Inter,system-ui,sans-serif; }
    .rwu-footer__cta p{ margin:0; color:#D6E8F5; font-size:13px; line-height:20px; letter-spacing:-.32px; }
    .rwu-footer input[type="email"],
    .rwu-footer input[type="text"]{
      height:48px;border-radius:8px;background:#fff;border:1px solid #D1D5DB;box-shadow:0 1px 2px rgba(0,0,0,.047);color:#0A033C;
    }
    .rwu-footer .btn.btn--secondary{ height:50px;border-radius:0 8px 8px 0;background:#419BD5;color:#66C0FF;border:none; }

    /* Band 2: 4 Columns */
    .rwu-footer__grid{ display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:30px; padding:50px 0 50px; border-top:1px solid #374151; }
    .rwu-foot-col h5{ margin:0 0 14px; font-size:14px; line-height:17px; }
    .rwu-foot-col p{ margin:0 0 16px; }
    .rwu-foot-col ul{ list-style:none; margin:0; padding:0; }
    .rwu-foot-col li{ margin:0 0 8px; }
    .rwu-foot-col .bullet-list__action{ color:#E5E7EB; text-decoration:none; }
    .rwu-foot-col .footer_contact_details li span,
    .rwu-foot-col .footer_contact_details a{ color:#D6E8F5; }

    .rwu-contact-list li{ display:flex; gap:10px; align-items:flex-start; }
    .rwu-contact-list svg{ width:18px;height:18px; flex:0 0 18px; }

    /* App badges + social */
    .rwu-badges{ display:flex; gap:15px; margin:6px 0 14px; }
    .rwu-badge{ width:120px; height:38px; background:#0b2232; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#D6E8F5; font-size:10px; }
    .rwu-social{ display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
    .rwu-social a{ display:flex; justify-content:center; align-items:center; width:36px; height:36px;  border-radius:6px; }
    .rwu-social svg{ width:15px;height:15px; }

    /* Language & Currency */
    .rwu-settings { margin-top:14px; }
    .rwu-settings .btn--bordered.btn--dropdown{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.25); color:#fff; height:40px; }
    .rwu-settings .settings__target{ background:#0C2A40; border:1px solid rgba(255,255,255,.15); }

    /* Band 3: bottom bar */
    .rwu-footer__bottom{ padding:16px 0 24px; border-top:1px solid #374151; }
    .rwu-bottom-grid{ display:grid; grid-template-columns:1fr auto; gap:16px; align-items:center; position:relative; }
    .rwu-bottom-left{ color:#9CA3AF; font-size:12px; }
    .rwu-bottom-links{ display:flex; gap:10px; align-items:center; justify-content:flex-end; }
    .rwu-bottom-links a{ color:#F9FAFB; font-size:12px; text-decoration:underline; }

    /* Payment logos row */
    .rwu-pay{ margin-top:8px; display:flex; gap:10px; align-items:center; }
    .rwu-pay img{ height:18px; display:block; }
    .rwu-pay .slot{ width:36px; height:13px; background:#fff; border-radius:2px; opacity:.9; }

    @media (max-width: 992px){
      .rwu-footer__cta .cta-grid{ grid-template-columns:1fr; }
      .rwu-footer__grid{ grid-template-columns:1fr 1fr; }
      .rwu-bottom-grid{ grid-template-columns:1fr; }
      .rwu-bottom-links{ justify-content:flex-start; }
    }
    @media (max-width: 576px){
      .rwu-footer__grid{ grid-template-columns:1fr; }
    }
  </style>

  <section class="section section--footer">
    <div class="container container--narrow ">

      <!-- ===== Band 1: Join Us Now + Newsletter ===== -->
      <div class="rwu-footer__cta">
        <div class="cta-grid">
          <div>
            <h4><?= Label::getLabel('LBL_JOIN_US_NOW'); ?></h4>
            <p><?= Label::getLabel('LBL_STAY_UPDATED_WITH_TUTORING_AND_RESOURCES'); ?></p>
          </div>
          <div>
            <?php if ($newsletter) { echo $form->getFormHtml(); } ?>
          </div>
        </div>
      </div>

      <!-- ===== Band 2: Support / Explore / Help / Download app ===== -->
      <div class="rwu-footer__grid">

        <!-- Col 1: Support + Address -->
        <div class="rwu-foot-col">
          <h5><?= Label::getLabel('LBL_DO_YOU_NEED_HELP_?'); ?></h5>
          <p>Our support team is always ready to assist you with tutoring, account, or course-related questions.</p>

          <ul class="footer_contact_details rwu-contact-list">
            <?php if (!empty($sitePhone)) { ?>
              <li>
                <svg class="icon icon--phone"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#phone'; ?>"></use></svg>
                <span><strong><?php echo $sitePhone; ?></strong></span>
              </li>
            <?php } ?>
            <li>
              <svg class="icon icon--email"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#email'; ?>"></use></svg>
              <span><a href="mailto:<?php echo $siteEmail; ?>"><?php echo $siteEmail; ?></a></span>
            </li>
          </ul>

          <!-- <?php if (!empty($address)) { ?>
            <h5 style="margin-top:18px;"><?= Label::getLabel('LBL_ADDRESS'); ?></h5>
            <p><?php echo $address; ?></p>
          <?php } ?> -->
        </div>

        <!-- Col 2: Explore (footerOneNav) -->
        <div class="rwu-foot-col">
          <h5>Explore Read With Us</h5>
          <ul>
            <?php if (!empty($footerOneNav)) {
              foreach ($footerOneNav as $nav) {
                if (!empty($nav['pages'])) {
                  foreach ($nav['pages'] as $link) {
                    $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']); ?>
                    <li><a class="bullet-list__action" target="<?= $link['nlink_target']; ?>" href="<?= $navUrl; ?>"><?= $link['nlink_caption']; ?></a></li>
            <?php }}}} ?>
          </ul>
        </div>

        <!-- Col 3: Help & Resources (footerTwoNav) -->
        <div class="rwu-foot-col">
          <h5><?php echo !empty($footerTwoNav) ? current($footerTwoNav)['parent'] : Label::getLabel('LBL_HELP_&_RESOURCES'); ?></h5>
          <ul>
            <?php if (!empty($footerTwoNav)) {
              foreach ($footerTwoNav as $nav) {
                if (!empty($nav['pages'])) {
                  foreach ($nav['pages'] as $link) {
                    $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']); ?>
                    <li><a class="bullet-list__action" target="<?= $link['nlink_target']; ?>" href="<?= $navUrl; ?>"><?= $link['nlink_caption']; ?></a></li>
            <?php }}}} ?>
          </ul>
        </div>

        <!-- Col 4: Download app + Social + Language & Currency -->
        <div class="rwu-foot-col">
          <h5><?= Label::getLabel('LBL_DOWNLOAD_OUR_APP'); ?></h5>
          <!-- Replace placeholders with your real badge images if available -->
          <div class="rwu-badges">
            <?php // sample placeholders, swap with real images if you have them ?>
            <div class="rwu-badge">Google Play</div>
            <div class="rwu-badge">App Store</div>
          </div>

          <?php if (!empty($socialPlatforms)) { ?>
            <p style="margin-top:12px; color:#fff;"><?= Label::getLabel('LBL_FOLLOW_US_ON_SOCIAL_MEDIA'); ?></p>
            <div class="rwu-social">
              <?php foreach ($socialPlatforms as $name => $link) { ?>
                <a href="<?= $link; ?>" target="_blank" aria-label="<?= $name; ?>">
                  <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#' . strtolower($name); ?>"></use></svg>
                </a>
              <?php } ?>
            </div>
          <?php } ?>

          <!-- Language and Currency compact controls -->
          <div class="rwu-settings">
            

            
          </div>
        </div>

      </div><!-- /grid -->


  
            <div>
              <?php
              if (MyUtility::isDemoUrl()) {
                  echo CommonHelper::replaceStringData(
                      Label::getLabel('LBL_COPYRIGHT_TEXT'),
                      [
                          '{YEAR}'   => '&copy; ' . date("Y"),
                          '{PRODUCT}' => '<a target="_blank" href="https://yo-coach.com">Yo!Coach</a>',
                          '{OWNER}'   => '<a target="_blank" class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>'
                      ]
                  );
              } else {
                  echo Label::getLabel('LBL_COPYRIGHT') . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . MyUtility::getSiteLangId(), FatUtility::VAR_STRING);
              }
              ?>
            </div>

            <!-- Payment logos (swap placeholders with your real images if you have them) -->
            <div class="rwu-pay">
              <!-- examples:
              <img src="<?= CONF_WEBROOT_URL ?>images/payments/visa.svg" alt="Visa">
              <img src="<?= CONF_WEBROOT_URL ?>images/payments/mastercard.svg" alt="Mastercard">
              <img src="<?= CONF_WEBROOT_URL ?>images/payments/skrill.svg" alt="Skrill">
              <img src="<?= CONF_WEBROOT_URL ?>images/payments/klrna.svg" alt="Klarna">
              -->
              <span class="slot" title="Visa"></span>
              <span class="slot" title="PayPal"></span>
              <span class="slot" style="width:53px;" title="Skrill"></span>
              <span class="slot" style="width:41px;" title="Klarna"></span>
              <span class="slot" style="width:64px;" title="Stripe"></span>
            </div>
          
          </div>
          </div>

      </div>

    </div>
  </section>

 
</footer>

<a href="#top" class="gototop" title="Back to Top"></a>

<?php if (FatApp::getConfig('CONF_ENABLE_COOKIES', FatUtility::VAR_INT, 1) && empty($cookieConsent)) { ?>
  <div class="cc-window cc-banner cc-type-info cc-theme-block cc-bottom cookie-alert no-print">
    <?php if (FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')) { ?>
      <div class="box-cookies">
        <span id="cookieconsent:desc" class="cc-message">
          <?php echo FatUtility::decodeHtmlEntities(FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')); ?>
          <?php $readMorePage = FatApp::getConfig('CONF_COOKIES_BUTTON_LINK', FatUtility::VAR_INT);
          if ($readMorePage) { ?>
            <a href="<?php echo MyUtility::makeUrl('cms', 'view', [$readMorePage]); ?>"><?php echo Label::getLabel('LBL_READ_MORE'); ?></a>
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
</body>
</html>
    