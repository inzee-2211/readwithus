<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'txnfeeSetup(this); return(false);');
$frm->setFormTagAttribute('id', 'gatewayFeeForm');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
?>

<section class="section">
    <div class="sectionhead" id="pmFeesectionhead-js">
        <h4><?php echo Label::getLabel('LBL_Method_Fee_Setups'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel" id="pmFeeForm-js">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
