<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'cancelSetup(this); return(false);');
// if ($refundPercentage > 0) {
//     $frm->getField('comment')->htmlAfterField = '<spam class="-color-primary">' . sprintf(Label::getLabel('LBL_NOTE:_REFUND_WOULD_BE_%s_PERCENT'), $refundPercentage) . '</spam>';
// }
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_CANCEL_COURSE'); ?></h4>
    </div>
    <div class="facebox-panel__body padding-bottom-0">
        <?php echo $frm->getFormHtml(); ?>
    </div>
</div>