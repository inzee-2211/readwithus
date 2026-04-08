<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->setFormTagAttribute('class', 'web_form form_horizontal');
$form->setFormTagAttribute('onsubmit', 'setupMedia(this); return false;');
$form->developerTags['colClassPrefix'] = 'col-md-';
$form->developerTags['fld_default_col'] = 12;
$langIdFld = $form->getField('lang_id');
$langIdFld->addFieldTagAttribute('class', 'language-js');
$langIdFld->addFieldTagAttribute('onChange', 'slideMediaForm(' . $slideId . ', this.value);');
$extensionLabel = Label::getLabel('LBL_ALLOWED_FILE_EXTS_{ext}');
$dimensionsLabel = Label::getLabel('LBL_PREFERRED_DIMENSIONS_{dimensions}');
foreach ($displayTypes as $type => $display) {
    $field = $form->getField('slide_image_' . $type);
    $field->htmlAfterField = '<div id="image-listing">';
    if (!empty($images[$type])) {
        $field->htmlAfterField .= '<ul class="grids--onethird"><li><div class="logothumb"> <img src="' . MyUtility::makeUrl('image', 'show', [$type, $slideId, Afile::SIZE_SMALL, $langId]) . '?' . time() . '"> </div></li></ul>';
    }
    $field->htmlAfterField .= '</div>';
    $field->htmlAfterField .= '<div style="margin-top:15px;" >' . str_replace('{dimensions}', $dimensions[$type], $dimensionsLabel) . '</div>';
    $field->htmlAfterField .= '<div style="margin-top:15px;">' . str_replace('{ext}', $imageExts, $extensionLabel) . '</div>';
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_SLIDE_IMAGE_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="slideForm(<?php echo $slideId; ?>);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                        <li><a class="active" href="javascript:void(0);" onclick="slideMediaForm(<?php echo $slideId; ?>, 0);"><?php echo Label::getLabel('LBL_MEDIA'); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $form->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>