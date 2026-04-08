<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');

$primaryFld = $frm->getField('theme_primary_color');
$primaryFld->addFieldTagAttribute('class', 'jscolor');

$primaryFld = $frm->getField('theme_primary_inverse_color');
$primaryFld->addFieldTagAttribute('class', 'jscolor');

$primaryFld = $frm->getField('theme_secondary_color');
$primaryFld->addFieldTagAttribute('class', 'jscolor');

$primaryFld = $frm->getField('theme_secondary_inverse_color');
$primaryFld->addFieldTagAttribute('class', 'jscolor');

$primaryFld = $frm->getField('theme_footer_color');
$primaryFld->addFieldTagAttribute('class', 'jscolor');

$primaryFld = $frm->getField('theme_footer_inverse_color');
$primaryFld->addFieldTagAttribute('class', 'jscolor');

$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
?>

<section class="section">
	<div class="sectionhead">
		<h4><?php echo Label::getLabel('LBL_Theme_Setup'); ?></h4>
	</div>
	<div class="sectionbody space">
		<div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
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
