<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$slideFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$slideFrm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$slideFrm->developerTags['colClassPrefix'] = 'col-md-';
$slideFrm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_SLIDE_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="slideForm(<?php echo $slide_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <li class="<?php echo ($slide_id == 0) ? 'fat-inactive' : ''; ?>"><a href="javascript:void(0)" <?php if ($slide_id > 0) { ?> onclick="slideMediaForm(<?php echo $slide_id ?>);" <?php } ?>><?php echo Label::getLabel('LBL_Media'); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $slideFrm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>