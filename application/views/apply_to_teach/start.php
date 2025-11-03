<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$applyTeachFrm->developerTags['colClassPrefix'] = 'col-md-';
$applyTeachFrm->developerTags['fld_default_col'] = 12;
$applyTeachFrm->setFormTagAttribute('class', 'form');
$applyTeachFrm->setFormTagAttribute('id', 'applyTeachStartForm');
$emailFld = $applyTeachFrm->getField('user_email');
$passFld  = $applyTeachFrm->getField('user_password');
$submit   = $applyTeachFrm->getField('btn_submit');
$emailFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_EMAIL'));
$passFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_PASSWORD'));
$submit->setFieldTagAttribute('class', 'btn btn--secondary btn--large btn--block');
?>
<section class="section section--page">
  <div class="container container--narrow">
    <div class="box box--narrow">
      <h2 class="-align-center"><?php echo Label::getLabel('LBL_APPLY_TO_TEACH'); ?></h2>
      <p class="-align-center"><?php echo Label::getLabel('LBL_CREATE_YOUR_ACCOUNT_AND_START_TEACHING'); ?></p>

      <?php echo $applyTeachFrm->getFormTag(); ?>
        <div class="field-set">
          <div class="field-wraper">
            <div class="field_cover">
              <?php echo $emailFld->getHTML(); ?>
            </div>
          </div>
        </div>
        <div class="field-set">
          <div class="field-wraper">
            <div class="field_cover">
              <?php echo $passFld->getHTML(); ?>
              <?php echo $applyTeachFrm->getFieldHTML('agree'); ?>
              <?php echo $applyTeachFrm->getFieldHTML('user_dashboard'); ?>
              <?php echo $applyTeachFrm->getFieldHTML('user_id'); ?>
            </div>
          </div>
        </div>
        <button class="btn btn--secondary btn--large btn--block" name="btn_submit"
          value="<?php echo $submit->value; ?>"><?php echo $submit->value; ?></button>
      </form>

      <?php echo $applyTeachFrm->getExternalJs(); ?>

      <p class="-align-center margin-top-4">
        <?php
          $termsPage = FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0);
          $privacyPage = FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0);
          echo sprintf(
            Label::getLabel('LBL_BY_SIGNING_UP_YOU_AGREE_TO_TERMS'),
            '<a href="' . MyUtility::makeUrl('Cms','view',[$termsPage]) . '" class="color-primary">' . Label::getLabel('LBL_Terms_&_Conditions') . '</a>',
            '<a href="' . MyUtility::makeUrl('Cms','view',[$privacyPage]) . '" class="color-primary">' . Label::getLabel('LBL_Privacy_Policy') . '</a>'
          );
        ?>
      </p>
    </div>
  </div>
</section>

<script>
(function(){
  const form = document.getElementById('applyTeachStartForm');
  const email = form.querySelector('input[name="user_email"]');
  let debounce;
  email.addEventListener('input', function(){
    clearTimeout(debounce);
    const val = email.value.trim();
    if (!val) return;
    debounce = setTimeout(() => {
      const url = email.getAttribute('data-check-url') || '<?= MyUtility::makeUrl('ApplyToTeach','checkEmail'); ?>';
      $.ajax({
        url, type: 'POST', data: {email: val}, dataType: 'json',
        success: function(res){
          // ok -> do nothing
        },
        error: function(xhr){
          let msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Email not available';
          fcom.error(msg);
        }
      });
    }, 350);
  });

  // AJAX submit (uses action from form tag -> ApplyToTeach/teacherSetup)
  form.addEventListener('submit', function(e){
    e.preventDefault();
    fcom.updateWithAjax($(form).attr('action'), $(form).serialize(), function(json){
      if (json.redirectUrl) { window.location = json.redirectUrl; }
    });
  });
})();
</script>
