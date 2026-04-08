<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');

$fld = $frm->getField('resource_files[]');
$fld->htmlAfterField = "<small>" . str_replace(['{filesize}', '{extension}'], [$filesize, $allowedExtensions], Label::getLabel('LBL_NOTE:_ALLOWED_SIZE_{filesize}_MB._SUPPORTED_FILE_FORMATS_{extension}')) . "</small>";
$cancelBtn = $frm->getField('btn_cancel');
$cancelBtn->addFieldTagAttribute('onClick', 'cancel();');
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_UPLOAD_RESOURCES'); ?></h4>
    </div>
    <div class="facebox-panel__body">
        <?php echo $frm->getFormTag(); ?>
        <div class="row">
            <div class="col-md-8">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php
                            $fld = $frm->getField('resource_files[]');
                            echo $fld->getCaption();
                            ?>
                            <span class="spn_must_field">*</span>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $fld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-action-sticky">
            <div class="col-sm-12">
                <div class="field-set margin-bottom-0">
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $cancelBtn->getHtml(); ?>
                            <?php echo $frm->getFieldHtml('btn_submit'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJS(); ?>
    </div>
</div>
