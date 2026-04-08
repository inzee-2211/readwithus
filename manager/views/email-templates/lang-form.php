<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('class', 'web_form layout--' . $formLayout);
$langFrm->setFormTagAttribute('onsubmit', 'setupEtplLang(this); return(false);');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 12;

$fld = $langFrm->getField('btn_preview');
if ($etplCode == 'emails_header_footer_layout') {
    $fld->setFieldTagAttribute('style', 'display:none;');
} else {
    $fld->setFieldTagAttribute('onclick', 'setupAndPreview();');
}

$fld = $langFrm->getField('etpl_lang_id');
$fld->setFieldTagAttribute('onchange', 'editLangForm("'.$etplCode.'",this.value)');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Email_Template_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">      
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $langFrm->getFormHtml(); ?>
                    <a style="display:none;" id="previewTpl" target="_blank" href="<?php echo MyUtility::makeUrl('EmailTemplates', 'preview', [$etplCode, $langId]); ?>"></a>
                </div>
            </div>						
        </div>
    </div>						
</section>