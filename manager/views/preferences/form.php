<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$typeFld = $frm->getField('prefer_type');
$typeFld->addFieldTagAttribute('class', 'hide');
$typeFld->setWrapperAttribute('class', 'hide');
$preferenceId = $frm->getField('prefer_id')->value;
$preferenceType = $frm->getField('prefer_type')->value;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_PREFERENCE_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class = "active" href = "javascript:void(0)" onclick = "preferenceForm(<?php echo $preferenceId ?>,<?php echo $preferenceType ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        $inactive = ($preferId == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            $langForm = (intval($preferenceId) > 0) ? 'onclick="langForm(' . $preferenceId . ',' . $langId . ');"' : '';
                            ?>
                            <li class=" lang-li-js <?php echo $inactive; ?>"><a href="javascript:void(0);" data-id="<?php echo $langId; ?>" <?php echo $langForm; ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>