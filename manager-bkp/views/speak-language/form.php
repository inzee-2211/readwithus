<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_SPOKEN_LANGUAGE_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                        <?php
                        $inactive = ($sLangId == 0) ? 'fat-inactive' : '';
                        $mediaForm = ($sLangId > 0) ? 'onclick="mediaForm(' . $sLangId . ');"' : '';
                        foreach ($languages as $langId => $langName) {
                            $langForm = (intval($sLangId) > 0) ? 'onclick="langForm(' . $sLangId . ',' . $langId . ');"' : '';
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" data-id="<?php echo $langId; ?>" <?php echo $langForm; ?>><?php echo $langName; ?></a></li>
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