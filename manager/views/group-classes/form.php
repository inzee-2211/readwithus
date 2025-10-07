<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'groupClassesFrm');
$frm->setFormTagAttribute('enctype', 'multipart/form-data');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$dateformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT', FatUtility::VAR_STRING, 'Y-m-d');
$timeformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT_TIME', FatUtility::VAR_STRING, 'H:i');
$frm->getField('grpcls_start_datetime')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
$frm->getField('grpcls_end_datetime')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
?>
<div class="box -padding-20">
    <!--page-head start here-->
    <div class="d-flex justify-content-between align-items-center">
        <div><h4><?php echo Label::getLabel('LBL_GROUP_CLASS_SETUP'); ?></h4></div>
    </div>
    <span class="-gap"></span>
    <!--page-head end here-->
    <?php echo $frm->getFormHtml(); ?>
</div>
