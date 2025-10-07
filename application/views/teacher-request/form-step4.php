<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupStep4(this); return(false);');
?>
<?php $this->includeTemplate('teacher-request/_partial/leftPanel.php', ['step' => 4]); ?>
<div class="page-block__right">
    <div class="page-block__head">
        <div class="head__title">
            <h4><?php echo Label::getLabel('LBL_Tutor_registration'); ?></h4>
        </div>
    </div>
    <div class="page-block__body">
        <?php echo $frm->getFormTag() ?>
        <div class="row justify-content-center no-gutters">
            <div class="col-md-12 col-lg-10 col-xl-11">
                <div class="block-content">
                    <div class="block-content__head d-flex align-items-center justify-content-between">
                        <div>
                            <div class="info__content">
                                <h5><?php echo Label::getLabel('LBL_Resume_Section_Title'); ?></h5>
                                <p><?php echo Label::getLabel('LBL_Resume_section_desc'); ?></p>
                            </div>
                        </div>
                        <div>
                            <div class="info__action">
                                <a href="javascript:void(0);" onclick="teacherQualificationForm(0);" class="btn btn--bordered btn--small color-secondary qualification-add-js d-none"><?php echo Label::getLabel('LBL_ADD_RESUME'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="block-content__body">
                        <div class="form form--register">
                            <div class="form__body padding-0">
                                <div class="table-scroll" id="qualification-container"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="field-set margin-bottom-0 accept--field">
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <label>
                                                <span class="checkbox">
                                                    <input type="checkbox" data-field-caption="<?php echo Label::getLabel('LBL_TERMS_&_CONDITIONS'); ?>" name="tereq_terms" data-fatreq='{"required":true}' value="1" <?php echo $frm->getField('tereq_terms')->checked ? 'checked' : ''; ?> />
                                                    <i class="input-helper"></i>
                                                </span>
                                                <?php echo Label::getLabel('LBL_ACCEPT_TUTOR_APPROVAL'); ?>
                                                <a target="_blank" href="<?php echo MyUtility::makeUrl('cms', 'view', [2]) ?>"><?php echo Label::getLabel('LBL_TERMS_&_CONDITIONS'); ?></a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content__foot">
                        <div class="form__actions">
                            <div class="d-flex align-items-center justify-content-between">
                                <div><input type="button" name="back" onclick="getform(3);" value="<?php echo Label::getLabel('LBL_Back'); ?>"></div>
                                <div>
                                    <input type="submit" name="save" value="<?php echo Label::getLabel('LBL_SAVE'); ?>" />
                                    <input type="button" name="next" onclick="setupStep4(document.frmFormStep4, true)" value="<?php echo Label::getLabel('LBL_NEXT'); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJs(); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        searchTeacherQualification();
    });
</script>