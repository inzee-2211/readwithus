<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$mediaFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$mediaFrm->developerTags['colClassPrefix'] = 'col-md-';
$mediaFrm->developerTags['fld_default_col'] = 12;
$imageFile = $mediaFrm->getField('tlang_image_file');
$imageFile->addFieldTagAttribute('class', 'hide tlang_image_file');
$imageFile->addFieldTagAttribute('onChange', 'uploadImage(this, ' . $tLangId . ', ' . Afile::TYPE_TEACHING_LANGUAGES . ')');
$fld1 = $mediaFrm->getField('tlang_image');
$fld1->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$extensionLabel = Label::getLabel('LBL_ALLOWED_FILE_EXTS_{extension}');
$demensionLabel = Label::getLabel('LBL_PREFERRED_DIMENSIONS_ARE_WIDTH_{width}_&_HEIGHT_{height}');
$preferredDimensionsStr = '<span class="uploadimage--info" >' . str_replace(['{width}', '{height}'], ['240px', '240px'], $demensionLabel) . '</span>';
$preferredDimensionsStr .= '<span class="uploadimage--info" >' . str_replace('{extension}', $teachLangExt, $extensionLabel) . '</span>';
$htmlAfterField = $preferredDimensionsStr;
$htmlAfterField .= '<div id="flag-image-listing">';
if (!empty($image)) {
    $htmlAfterField .= '<ul class="grids--onethird"><li><div class="logoWrap"><div class="logothumb"> <img src="' . MyUtility::makeUrl('image', 'show', [Afile::TYPE_TEACHING_LANGUAGES, $image['file_record_id'], Afile::SIZE_SMALL]) . '?' . time() . '" title="' . $image['file_name'] . '" alt="' . $image['file_name'] . '">';
    if ($canEdit) {
        $htmlAfterField .= '<a class="deleteLink white" href="javascript:void(0);" onclick="removeFile(' . $tLangId . ', ' . Afile::TYPE_TEACHING_LANGUAGES . ');" class="delete"><i class="ion-close-round"></i></a>';
    }
    $htmlAfterField .= '</div></div></li></ul>';
}
$htmlAfterField .= '</div>';
$fld1->htmlAfterField = $htmlAfterField;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_LANGUAGE_IMAGE'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="form(<?php echo $tLangId; ?>);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                        <?php foreach ($languages as $langId => $langName) { ?>
                            <li><a href="javascript:void(0);" onclick="langForm(<?php echo $tLangId ?>, <?php echo $langId; ?>);"><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li><a class="active" href="javascript:void(0)" onclick="mediaForm(<?php echo $tLangId ?>);"><?php echo Label::getLabel('LBL_MEDIA'); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $mediaFrm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>