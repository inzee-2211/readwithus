<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupCurrency(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
if ($defaultCurrency) {
    $fld = $frm->getField('currency_value');
    $fld->setFieldTagAttribute('disabled', true);
    $fld->htmlAfterField = '<small>' . Label::getLabel('LBL_THIS_IS_YOUR_DEFAULT_CURRENCY') . '</small>';
    $frm->getField('currency_code')->setFieldTagAttribute('disabled', true);
    $frm->getField('currency_active')->setFieldTagAttribute('disabled', true);
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Currency_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">      
        <div class="tabs_nav_container responsive flat">
            <ul class="tabs_nav">
                <li><a class="active" href="javascript:void(0)" onclick="currencyForm(<?php echo $currency_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                <?php
                $inactive = ($currency_id == 0) ? 'fat-inactive' : '';
                foreach ($languages as $langId => $langName) {
                    ?>
                    <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($currency_id > 0) { ?> onclick="editCurrencyLangForm(<?php echo $currency_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                <?php } ?>
            </ul>
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>						
        </div>
    </div>						
</section>