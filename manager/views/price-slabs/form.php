<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->setFormTagAttribute('class', 'web_form form_horizontal');
$form->setFormTagAttribute('onsubmit', 'setupPriceSlab(this); return(false);');
$form->developerTags['colClassPrefix'] = 'col-md-';
$form->developerTags['fld_default_col'] = 12;
$minField = $form->getField('prislab_min');
$maxField = $form->getField('prislab_max');
?>
<section class="section">
    <div class="sectionhead">
        <h5>
            <?php echo Label::getLabel('LBL_Price_Slab_Setup'); ?><br/>
            <small class="-color-secondary"><?php echo Label::getLabel('LBL_MIN_AND_MAX_FORM_NOTE'); ?></small>
        </h5>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <h1></h1>
                <div class="tabs_nav_container responsive flat">
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $form->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>