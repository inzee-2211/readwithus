<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'teacherPreferencesFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupTeacherLanguages(this, false); return(false);');
$teachLangField = $frm->getField('teach_lang_id');
$teachLangFieldValue = $teachLangField->value;
$backBtn = $frm->getField('back_btn');
$backBtn->addFieldTagAttribute("onClick", "$('.profile-Info-js').trigger('click');");
$nextBtn = $frm->getField('next_btn');
$nextBtn->addFieldTagAttribute("onClick", "setupTeacherLanguages(this.form, true); return(false);");
$saveBtn = $frm->getField('submit');
?>
<div class="content-panel__head">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5><?php echo Label::getLabel('LBL_MANAGE_LANGUAGES'); ?></h5>
        </div>
        <div></div>
    </div>
</div>
<div class="content-panel__body">
    <div class="form">
        <?php echo $frm->getFormTag(); ?>
        <div class="form__body">
            <div class="colum-layout">
                <div class="colum-layout__cell">
                    <div class="colum-layout__head">
                        <span class="bold-600"><?php echo $teachLangField->getCaption(); ?></span>
                        <?php if ($teachLangField->requirement->isRequired()) { ?>
                            <span class="spn_must_field">*</span>
                        <?php } ?>
                    </div>
                    <div class="colum-layout__body">
                        <div class="colum-layout__scroll scrollbar">
                            <?php foreach ($teachLangField->options as $key => $value) { ?>
                                <div class="selection">
                                    <label class="selection__trigger">
                                        <input name="<?php echo $teachLangField->getName() . '[]'; ?>" value="<?php echo $key; ?>" class="selection__trigger-input" type="checkbox" <?php echo (in_array($key, $teachLangFieldValue)) ? 'checked' : ''; ?>>
                                        <span class="selection__trigger-action">
                                            <span class="selection__trigger-label"><?php echo $value; ?></span>
                                            <span class="selection__trigger-icon"></span>
                                        </span>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="colum-layout__cell">
                    <div class="colum-layout__head">
                        <span class="bold-600"><?php echo Label::getLabel('LBL_LANGUAGE_I_SPEAK'); ?></span>
                        <span class="spn_must_field">*</span>
                    </div>
                    <div class="colum-layout__body">
                        <div class="colum-layout__scroll scrollbar">
                            <?php
                            foreach ($speakLangs as $key => $value) {
                                $speakLangField = $frm->getField('uslang_slang_id[' . $key . ']');
                                $proficiencyField = $frm->getField('uslang_proficiency[' . $key . ']');
                                $proficiencyField->addFieldTagAttribute('onchange', 'changeProficiency(this,' . $key . ');');
                                $proficiencyField->addFieldTagAttribute('data-lang-id', $key);
                                $isLangSpeak = $speakLangField->checked;
                            ?>
                                <div class="selection selection--select slanguage-<?php echo $key; ?> <?php echo ($isLangSpeak) ? 'is-selected' : ''; ?>">
                                    <label class="selection__trigger ">
                                        <input type="checkbox" value="<?php echo $key; ?>" class="slanguage-checkbox-js slanguage-checkbox-<?php echo $key; ?>" onchange="changeSpeakLang(this, <?php echo $key; ?>);" name="<?php echo $speakLangField->getName(); ?>" <?php echo ($isLangSpeak) ? 'checked' : ''; ?>>
                                        <span class="selection__trigger-action">
                                            <span class="selection__trigger-label">
                                                <?php echo $value; ?>
                                                <?php if (array_key_exists($proficiencyField->value, $profArr)) { ?>
                                                    <span class="badge color-secondary badge-js  badge--round badge--small margin-0">
                                                        <?php echo $profArr[$proficiencyField->value]; ?>
                                                    </span>
                                                <?php } ?>
                                            </span>
                                            <span class="selection__trigger-icon"></span>
                                        </span>
                                    </label>
                                    <div class="selection__target">
                                        <?php echo $proficiencyField->getHTML(); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form__actions">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <?php echo $backBtn->getHTML(); ?>
                </div>
                <div>
                    <?php
                    echo $saveBtn->getHTML();
                    echo $nextBtn->getHTML();
                    ?>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJS(); ?>
    </div>
</div>