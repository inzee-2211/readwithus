<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupNotes(this); return(false);');
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_NOTES'); ?></h4>
    </div>
    <div class="facebox-panel__body">
        <?php echo $frm->getFormTag(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php
                            $fld = $frm->getField('lecnote_notes');
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
                            <?php
                            $fld = $frm->getField('btn_cancel');
                            $fld->setFieldTagAttribute('onclick', "$.facebox.close();");
                            echo $fld->getHtml();
                            echo $frm->getFieldHtml('btn_submit');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo $frm->getFieldHtml('lecnote_course_id');
        echo $frm->getFieldHtml('lecnote_lecture_id');
        echo $frm->getFieldHtml('lecnote_ordcrs_id');
        echo $frm->getFieldHtml('lecnote_id');
        ?>
        </form>
        <?php echo $frm->getExternalJS(); ?>
    </div>
</div>