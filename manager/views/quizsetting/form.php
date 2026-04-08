<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><?= $formTitle ?> - <?= Label::getLabel('LBL_FORM'); ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <?php echo $frm->getFormTag(); ?>
                <div class="form__body">
                    <?php echo $frm->getFieldHtml('id'); ?>
                    <?php echo $frm->getFieldHtml('entity_type'); ?>
                    <?php 
                    foreach ($frm->getAllFields() as $field) {
                        if (in_array($field->getName(), ['id', 'entity_type', 'btn_submit'])) continue;
                        echo $frm->getFieldHtml($field->getName());
                    }
                    ?>
                </div>
                <div class="form__footer">
                    <?php echo $frm->getFieldHtml('btn_submit'); ?>
                </div>
            </form>
            <?php echo $frm->getExternalJs(); ?>
        </div>
    </div>
</div>