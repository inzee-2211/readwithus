<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
$recaptchaKey = FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '');
$contactFrm->setFormTagAttribute('class', 'form form--normal rwu-contact-clean__form');
if (!empty($recaptchaKey)) {
    $captchaFld = $contactFrm->getField('htmlNote');
    $captchaFld->htmlBeforeField = '<div class="field-set"><div class="caption-wraper"><label class="field_label"></label></div><div class="field-wraper"><div class="field_cover">';
    $captchaFld->htmlAfterField = '</div></div></div>';
}
$contactFrm->setFormTagAttribute('onsubmit', 'contactSetup(this); return(false);');
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;
$nameFld    = $contactFrm->getField('name');
$phoneFld   = $contactFrm->getField('phone');
$emailFld   = $contactFrm->getField('email');
$messageFld = $contactFrm->getField('message');
?>

<section class="rwu-contact-clean">
  <div class="container">
    <div class="rwu-contact-clean__grid">
      <!-- Left: Info -->
      <div class="rwu-contact-clean__info">
        <h2 class="rwu-contact-clean__title"><?php echo Label::getLabel('LBL_Need_a_Direct_Line?'); ?></h2>
        <p class="rwu-contact-clean__desc">
          <?php echo Label::getLabel('LBL_Contact_us_anytime_for_questions_or_support'); ?>
        </p>
        <div class="rwu-contact-clean__item">
          <div class="rwu-icon"><i class="fa fa-phone"></i></div>
          <div>
            <div class="rwu-item__label"><?php echo Label::getLabel('LBL_Phone'); ?></div>
            <div class="rwu-item__value">+447961990404</div>
          </div>
        </div>
        <div class="rwu-contact-clean__item">
          <div class="rwu-icon"><i class="fa fa-envelope"></i></div>
          <div>
            <div class="rwu-item__label"><?php echo Label::getLabel('LBL_Email'); ?></div>
            <div class="rwu-item__value">info@readwithus.com</div>
            
          </div>
        </div>
         <div class="rwu-contact-clean__item">
          <div class="rwu-icon"><i class="fa fa-envelope"></i></div>
          <div>
            <div class="rwu-item__label"><?php echo Label::getLabel('LBL_ADDRESS'); ?></div>
            <div class="rwu-item__value">14 Thirlmere Close, Wainscott, Rochester, Kent, ME2 4PA</div>
            
          </div>
        </div>
         <div class="rwu-contact-clean__item">
          <div class="rwu-icon"><i class="fa fa-envelope"></i></div>
          <div>
            <div class="rwu-item__label"><?php echo Label::getLabel('LBL_SUPPORT'); ?></div>
            <div class="rwu-item__value">SUPPORT</div>
            
          </div>
        </div>
      </div>

      <!-- Right: Form -->
      <div class="rwu-contact-clean__formbox">
        <h3 class="rwu-contact-clean__subtitle"><?php echo Label::getLabel('LBL_Contact_Us'); ?></h3>
        <p class="rwu-contact-clean__subdesc"><?php echo Label::getLabel('LBL_We_usually_reply_within_a_few_hours'); ?></p>
        <?php echo $contactFrm->getFormTag(); ?>

          <div class="form-row">
            <div class="form-group">
              <label><?php echo Label::getLabel('LBL_Name'); ?><?php if ($nameFld->requirement->isRequired()) { ?><span>*</span><?php } ?></label>
              <?php echo $contactFrm->getFieldHTML('name'); ?>
            </div>
            <div class="form-group">
              <label><?php echo Label::getLabel('LBL_Phone'); ?><?php if ($phoneFld->requirement->isRequired()) { ?><span>*</span><?php } ?></label>
              <?php echo $contactFrm->getFieldHTML('phone'); ?>
            </div>
          </div>

          <div class="form-group full">
            <label><?php echo Label::getLabel('LBL_Email'); ?><?php if ($emailFld->requirement->isRequired()) { ?><span>*</span><?php } ?></label>
            <?php echo $contactFrm->getFieldHTML('email'); ?>
          </div>

          <div class="form-group full">
            <label><?php echo Label::getLabel('LBL_Message'); ?><?php if ($messageFld->requirement->isRequired()) { ?><span>*</span><?php } ?></label>
            <?php echo $contactFrm->getFieldHTML('message'); ?>
          </div>

          <?php if (!empty($siteKey) && !empty($secretKey)) { ?>
          <div class="form-group full">
            <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
          </div>
          <?php } ?>

          <div class="form-group full">
            <?php echo $contactFrm->getFieldHTML('btn_submit'); ?>
          </div>
        </form>
        <?php echo $contactFrm->getExternalJS(); ?>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($siteKey) && !empty($secretKey)) { ?>
  <script src='//www.google.com/recaptcha/api.js'></script>
<?php } ?>

<style>
/* ==============================
   ReadWithUs — Contact (Figma Style)
   ============================== */
.rwu-contact-clean {
  --accent: #ff782d;
  --ink: #0a033c;
  --muted: #555;
  padding: 60px 0;
  background: linear-gradient(180deg, #ffffff 0%, #f9faff 100%);
  font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}
.rwu-contact-clean__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: start;
}
.rwu-contact-clean__info {
  max-width: 500px;
}
.rwu-contact-clean__title {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 10px;
  color: var(--ink);
}
.rwu-contact-clean__desc {
  color: var(--muted);
  margin-bottom: 25px;
  line-height: 1.6;
}
.rwu-contact-clean__item {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}
.rwu-contact-clean__item .rwu-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: #fff0e9;
  color: var(--accent);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
  font-size: 18px;
}
.rwu-item__label {
  font-weight: 600;
  font-size: 14px;
  color: var(--muted);
}
.rwu-item__value {
  font-weight: 600;
  font-size: 16px;
  color: var(--ink);
}

.rwu-contact-clean__formbox {
  background: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  padding: 30px;
}
.rwu-contact-clean__subtitle {
  font-weight: 700;
  font-size: 22px;
  color: var(--ink);
  margin-bottom: 5px;
}
.rwu-contact-clean__subdesc {
  font-size: 15px;
  color: var(--muted);
  margin-bottom: 25px;
}
.rwu-contact-clean__form input,
.rwu-contact-clean__form textarea {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  outline: none;
  transition: all 0.2s;
    margin-bottom: 15px;
}
.rwu-contact-clean__form input:focus,
.rwu-contact-clean__form textarea:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(255, 120, 45, 0.15);
}
.rwu-contact-clean__form textarea {
  resize: vertical;
  min-height: 120px;

}
.form-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}
.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.form-group.full {
  flex: 1 1 100%;
}
.form-group label {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 6px;
  color: var(--ink);
}
.form-group label span {
  color: var(--accent);
}
.form-group input[type="submit"],
.form-group button[type="submit"] {
  background: #2DADFF;
  color: #fff;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  padding: 2px 18px;
  margin-top: 10px;
  transition: all 0.2s;
  cursor: pointer;
}
.form-group input[type="submit"]:hover {
  transform: translateY(-1px);
  background: #ff8a4b;
  box-shadow: 0 8px 18px rgba(255, 120, 45, 0.25);
}

/* Responsive */
@media (max-width: 992px) {
  .rwu-contact-clean__grid {
    grid-template-columns: 1fr;
  }
  .rwu-contact-clean__formbox {
    margin-top: 30px;
  }
}
</style>

<script>
/* global fcom, grecaptcha */
(function () {
  contactSetup = function (frm) {
    if (!$(frm).validate()) return;
    var $btn = $(frm).find('input[type="submit"], button[type="submit"]').first();
    var original = $btn.val();
    $btn.prop('disabled', true).val('Sending...');

    fcom.process();
    fcom.updateWithAjax(
      fcom.makeUrl('Contact', 'contactSubmit'),
      fcom.frmData(frm),
      function (res) {
        frm.reset();
        if (typeof grecaptcha !== 'undefined' && grecaptcha.reset) {
          grecaptcha.reset();
        }
        $btn.prop('disabled', false).val(original);
        alert(res.msg || 'Thanks for contacting us!');
      },
      function () {
        $btn.prop('disabled', false).val(original);
        alert('Something went wrong. Please try again.');
      }
    );
  };
})();
</script>
