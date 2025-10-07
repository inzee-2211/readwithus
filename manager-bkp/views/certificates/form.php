<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form layout--'.$layoutDir);
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'setup(); return false;');

$fld = $frm->getField('certpl_lang_id');
$fld->setFieldTagAttribute('onchange', 'edit("' . $data['certpl_code'] . '", this.value); return false;');

$mediaFrm->setFormTagAttribute('class', 'web_form layout--'.$layoutDir);
$mediaFrm->developerTags['colClassPrefix'] = 'col-md-';
$mediaFrm->developerTags['fld_default_col'] = 12;
$fld = $mediaFrm->getField('certpl_image');
$fld->setFieldTagAttribute('onchange', 'setupMedia();');
$fld->htmlAfterField .= '<small>' . str_replace('{dimensions}', implode('x', $dimensions), Label::getLabel('LBL_PREFERRED_DIMENSIONS_{dimensions}', $data['certpl_lang_id'])) .  ' & ' . str_replace('{ext}', $imageExts, Label::getLabel('LBL_ALLOWED_FILE_EXTS_{ext}', $data['certpl_lang_id'])) . '</small>';
$fld = $frm->getField('btn_preview');
$fld->setFieldTagAttribute('onclick', 'setupAndPreview();');
$fld = $frm->getField('btn_reset');
$fld->setFieldTagAttribute('onclick', 'resetToDefault()');
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5> <?php echo Label::getLabel('LBL_EDIT_CERTIFICATE'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_CERTIFICATE_SETUP', $data['certpl_lang_id']); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $mediaFrm->getFormTag(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label">
                                                    <?php echo Label::getLabel('LBL_BACKGROUND_IMAGE', $data['certpl_lang_id']); ?>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $mediaFrm->getFieldHtml('certpl_image'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php echo $frm->getFieldHtml('certpl_id'); ?>
                                </form>
                                <?php echo $mediaFrm->getExternalJs(); ?>
                                <?php echo $frm->getFormTag(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label">
                                                    <?php echo Label::getLabel('LBL_LANGUAGE', $data['certpl_lang_id']); ?>
                                                    <span class="spn_must_field">*</span>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $frm->getFieldHtml('certpl_lang_id'); ?>
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
                                                    <?php echo Label::getLabel('LBL_NAME', $data['certpl_lang_id']); ?>
                                                    <span class="spn_must_field">*</span>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $frm->getFieldHtml('certpl_name'); ?>
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
                                                    <?php echo Label::getLabel('LBL_BODY', $data['certpl_lang_id']); ?>
                                                    <span class="spn_must_field">*</span>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="layout--<?php echo $layoutDir; ?>">
                                                    <div class="certificate certificateJs">
                                                        <div class="certificate-media certificateMediaJs">
                                                            <img src="<?php echo MyUtility::makeUrl('image', 'show', [Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE, 0, Afile::SIZE_LARGE]) . '?time=' . time() ?>">
                                                        </div>
                                                        <div class="certificate-content">
                                                            <h1 class="certificate-title contentHeadingJs" contenteditable="true">
                                                                <?php echo $content['heading'] ?>
                                                            </h1>
                                                            <br>
                                                            <div class="certificate-subtitle contentPart1Js" contenteditable="true">
                                                                <?php echo $content['content_part_1'] ?>
                                                            </div>
                                                            <div class="certificate-author contentLearnerJs" contenteditable="true">
                                                                <?php echo $content['learner'] ?>
                                                            </div>
                                                            <div class="certificate-meta contentPart2Js" contenteditable="true">
                                                                <?php echo $content['content_part_2'] ?>
                                                            </div>
                                                            <div class="certificate-signs">
                                                                <div class="certificate-signs__left">
                                                                    <div class="style-bold contentTrainerJs" contenteditable="true">
                                                                        <?php echo $content['trainer'] ?>
                                                                    </div>
                                                                </div>
                                                                <div class="certificate-signs__middle">
                                                                    <div class="certificate-logo">
                                                                        <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_CERTIFICATE_LOGO, 0, Afile::SIZE_MEDIUM, $data['certpl_lang_id']]); ?>" alt="">
                                                                    </div>
                                                                </div>
                                                                <div class="certificate-signs__right">
                                                                    <div class="style-bold contentCertNoJs" contenteditable="true">
                                                                        <?php echo $content['certificate_number'] ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3><?php echo $frm->getFieldHtml('replacement_caption'); ?></h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php echo $frm->getFieldHtml('certpl_vars'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label">
                                                    <?php echo Label::getLabel('LBL_STATUS', $data['certpl_lang_id']); ?>
                                                    <span class="spn_must_field">*</span>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $frm->getFieldHtml('certpl_status'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper"><label class="field_label"></label></div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $frm->getFieldHtml('btn_submit'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php echo $frm->getFieldHtml('certpl_code'); ?>
                                    <?php echo $frm->getFieldHtml('certpl_id'); ?>
                                    <?php echo $frm->getFieldHtml('catelang_id'); ?>
                                    </form>
                                    <?php echo $frm->getExternalJs(); ?>
                                </div>
                            </div>
                        </div>
                </section>
            </div>
        </div>
    </div>
</div>
<a target="_blank" id="previewCertificateJs" style="display:none;" href="<?php echo MyUtility::makeUrl('Certificates', 'generate'); ?>"></a>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    .layout--rtl .certificate {
        direction: rtl;
    }

    .certificate {
        width: 100%;
        position: relative;
        font-family: 'Open Sans', sans-serif;
    }

    .certificate::before {
        padding-bottom: 81.15%;
        content: "";
        display: block;
    }

    .certificate-media,
    .certificate-media img {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: 0 auto;
        width: 100%;
    }


    .certificate-content {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 1;
        text-align: center;
        padding: 12% 12% 4%;
        display: flex;
        flex-direction: column;
        font-style: italic;
        font-family: inherit;
    }

    .certificate-title,
    .certificate-subtitle,
    .certificate-author,
    .certificate-meta {
        font-style: inherit;
    }

    .certificate-title {
        font-size: 2.4vw;
        font-weight: 700;
        margin-bottom: 10%;
    }

    .certificate-subtitle {
        font-size: 1.4vw;
        margin-bottom: 2%;
        font-weight: normal; line-height: 1.4;
    }

    .certificate-author {
        font-size: 2vw;
        margin-bottom: 10%;
        font-style: normal;
        font-weight: 700; line-height: 1.2;
    }

    .certificate-meta {
        font-size: 2.2vw;
        font-weight: normal;
        line-height: 1.4;
        max-width: 90%;
        margin: 0 auto 5%;
    }

    .certificate-signs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        width: 100%;
        font-size: 14px;
        font-style: normal;
    }

    .certificate-signs__left {
        display: flex;
        align-items: center;
        width: 33.3%;
    }

    .certificate-signs__right {
        display: flex;
        align-items: center;
        width: 33.3%;
        justify-content: flex-end;
    }

    .certificate-signs__middle {
        width: 33.3%;
        display: flex;
        justify-content: center;
    }

    .certificate-logo {
        max-width: 160px;
    }

    .style-bold {
        font-weight: 700 !important;
    }
</style>