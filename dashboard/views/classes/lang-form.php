<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$languageIds = array_column($languages, 'language_direction', 'language_id');
$class = ($languageIds[$langId] == 'rtl') ? 'form form--rtl' : 'form';
$frm->setFormTagAttribute('id', 'classLangForm');
$frm->setFormTagAttribute('class', $class);
$frm->setFormTagAttribute('onsubmit', 'setupLangData(this, true); return(false);');
$titleFld = $frm->getField('grpcls_title');
$descFld = $frm->getField('grpcls_description');
$submitBtn = $frm->getField('btn_submit');
$lastlangId = array_key_last($languageIds);
if ($lastlangId == $langId) {
    $submitBtn->value = Label::getLabel('LBL_SAVE', $langId);
}
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel("LBL_ADD_GROUP_CLASS"); ?></h4>
        <div class="tabs tabs--line border-bottom-0">
            <ul class="lang-list">
                <li><a href="javascript:void(0);" onclick="addForm('<?php echo $classId ?>');"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                <?php foreach ($languages as $language) { ?>
                    <li class="<?php echo ($language['language_id'] == $langId) ? 'is-active' : '' ?>"><a href="javascript:void(0)" onclick="langForm('<?php echo $classId ?>', '<?php echo $language['language_id']; ?>')"><?php echo $language['language_name']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="facebox-panel__body">
        <?php echo $frm->getFormTag(); ?>
        <?php echo $frm->getFieldHTML('gclang_grpcls_id'); ?>
        <?php echo $frm->getFieldHTML('gclang_lang_id'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $titleFld->getCaption(); ?>
                            <?php if ($titleFld->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $titleFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $descFld->getCaption(); ?>
                            <?php if ($descFld->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $descFld->getHtml(); ?>
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
                            <?php echo $submitBtn->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJS(); ?>
    </div>
</div>