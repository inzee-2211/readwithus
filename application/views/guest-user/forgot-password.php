<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('autocomplete', 'off');
$frm->setFormTagAttribute('id', 'forgotPasswordFrma');
$frm->setFormTagAttribute('onsubmit', 'forgotPasswordSetup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frmFld = $frm->getField('user_email');
if (!empty($siteKey) && !empty($secretKey)) {
    $htmlNote = $frm->getField('htmlNote');
    $htmlNote->value = '<div class="field-set"><div class="caption-wraper"><label class="field_label"></label></div><div class="field-wraper"><div class="field_cover">
    <div class="g-recaptcha" data-sitekey="' . $siteKey . '" data-callback="captchaValidate" data-expired-callback="captchaValidate"></div></div></div></div>';
}
?>
<section class="section section--gray section--page">
    <div class="container container--fixed">
        <a href="<?php echo MyUtility::makeUrl('GuestUser', 'loginForm'); ?>" class="-link-underline -color-secondary -style-bold"><?php echo Label::getLabel('LBL_BACK_TO_LOGIN'); ?></a>
        <span class="-gap"></span>
        <div class="row justify-content-center">
            <div class="col-sm-9 col-lg-5 col-xl-5">
                <div class="box -skin">
                    <div class="box__head -align-center">
                        <h4 class="-border-title"><?php echo Label::getLabel('LBL_FORGOT_PASSWORD'); ?></h4>
                    </div>
                    <div class="box__body -padding-40">
                        <?php echo $frm->getFormHtml(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if (!empty($siteKey) && !empty($secretKey)) { ?>
    <script src='//www.google.com/recaptcha/api.js'></script>
<?php } ?>