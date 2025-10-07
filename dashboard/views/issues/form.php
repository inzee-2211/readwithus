<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'reportIssueForm');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'issueSetup(this); return(false);');
?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_ISSUE_REPORTED'); ?></h4>
    <?php echo $frm->getFormHtml(); ?>
</div>
