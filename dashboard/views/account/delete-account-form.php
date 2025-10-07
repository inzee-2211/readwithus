<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'delFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('autocomplete', 'off');
$frm->setFormTagAttribute('onsubmit', 'setUpGdprDelAcc(this); return(false);');
$reasonFld = $frm->getField('gdpreq_reason');
$submitBtn = $frm->getField('btn_submit');
$submitBtn->setFieldTagAttribute('form', $frm->getFormTagAttribute('id'));
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_DELETE_ACCOUNT_FORM'); ?></h4>
    </div>
    <div class="facebox-panel__body">
        <?php echo $frm->getFormTag(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $reasonFld->getCaption(); ?>
                            <?php if ($reasonFld->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $reasonFld->getHtml(); ?>
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
                            <?php echo $frm->getFieldHTML('btn_submit'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJS(); ?>
    </div>
</div>