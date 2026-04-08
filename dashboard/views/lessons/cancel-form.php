<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'cancelSetup(this); return(false);');
if ($lesson['order_discount_value'] > 0) {
    $noteFld = $frm->getField('note_text');
    $noteFld->htmlAfterField = '<br><spam class="-color-primary color-secondary">' . Label::getLabel('LBL_NOTE_CANCEL_LESSON_DISCOUNT_ORDER_TEXT') . '</spam>';
}
?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_CANCEL_LESSON'); ?></h4>
    <?php echo $frm->getFormHtml(); ?>
</div>