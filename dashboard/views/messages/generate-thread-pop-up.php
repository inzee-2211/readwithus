<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('onSubmit', 'sendMessage(this); return false;');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;

$sizeLabel = Label::getLabel('LBL_FILE_SIZE_SHOULD_BE_LESS_THAN_{FILE-SIZE}_MB');
$sizeLabel = str_replace('{file-size}',  MyUtility::convertBitesToMb($fileSize), $sizeLabel);
$formatsLabel = Label::getLabel('LBL_SUPPORTED_FILE_FORMATS_ARE_{file-formats}');
$formatsLabel = str_replace('{file-formats}', implode(', ', $allowedExtensions), $formatsLabel);

$fld = $frm->getField('message_file');
$fld->htmlAfterField = "<small>" . $sizeLabel . ' & ' . $formatsLabel . '</small>';

?>

<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_START_CONVERSATION'); ?></h4>
    </div>
    <div class="facebox-panel__body padding-bottom-0">
        <?php echo $frm->getFormHtml(); ?>
    </div>
</div>
