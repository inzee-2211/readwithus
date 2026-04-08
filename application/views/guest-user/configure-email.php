<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('autocomplete', 'off');
$frm->setFormTagAttribute('onsubmit', 'updateEmail(this); return(false);');
$fld = $frm->getField('new_email');
$fld->developerTags['col'] = 12;
$fld = $frm->getField('conf_new_email');
$fld->developerTags['col'] = 12;
$fld = $frm->getField('btn_submit');
$fld->developerTags['col'] = 12;
?>
<section class="section section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-sm-9 col-lg-5 col-xl-5">
                <?php echo Label::getLabel('LBL_PLEASE_CONTACT_WEMASTER'); ?> <a href="mailto:<?php echo FatApp::getConfig('conf_site_owner_email') ?>"><?php echo FatApp::getConfig('conf_site_owner_email') ?></a>
            </div>
        </div>
    </div>
</section>
<section class="section section--gray section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-lg-9 col-xl-8">
                <div class="box -skin">
                    <div class="box__head -align-center">
                        <h1><?php echo Label::getLabel('LBL_UPDATE_EMAIL'); ?></h1>
                    </div>
                    <div class="box__body -padding-40"><?php echo $frm->getFormHtml(); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
