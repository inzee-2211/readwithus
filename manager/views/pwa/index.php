<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($iconData)) {
    $frm->getField('icon')->requirement->setRequired();
} else {
    $icon_img_fld = $frm->getField('icon_img');
    $icon_img_fld->value = '<img src="' . MyUtility::makeUrl('Image', 'show', [Afile::TYPE_PWA_APP_ICON, 0, Afile::SIZE_SMALL]) . '" alt="' . Label::getLabel('LBL_App Icon') . '">';
}
if (empty($splashIconData)) {
    $frm->getField('splash_icon')->requirement->setRequired();
} else {
    $splash_icon_img_fld = $frm->getField('splash_icon_img');
    $splash_icon_img_fld->value = '<img src="' . MyUtility::makeUrl('Image', 'show', [Afile::TYPE_PWA_SPLASH_ICON, 0, Afile::SIZE_SMALL]) . '" alt="' . Label::getLabel('LBL_PWA_Splash_Icon') . '">';
}
if (!$canEdit) {
    $submitBtn = $frm->getField('btn_submit');
    $frm->removeField($submitBtn);
}
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->developerTags = ['colClassPrefix' => 'col-md-', 'fld_default_col' => 12];
$frm->getField('pwa_settings[background_color]')->overrideFldType('color');
$frm->getField('pwa_settings[theme_color]')->overrideFldType('color');
$frm->setFormTagAttribute('onsubmit', 'pwaSetup(this); return(false);');
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon">
                                <i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_PWA_SETTINGS'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionbody space">
                        <div class="box -padding-20">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>