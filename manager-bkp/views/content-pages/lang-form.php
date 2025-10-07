<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$blockLangFrm->setFormTagAttribute('class', 'web_form layout--' . $formLayout);
$blockLangFrm->developerTags['colClassPrefix'] = 'col-md-';
$blockLangFrm->developerTags['fld_default_col'] = 12;
if ($cpage_layout == ContentPage::CONTENT_PAGE_LAYOUT1_TYPE) {
    $fld = $blockLangFrm->getField('cpage_bg_image');
    $fld->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
    $preferredDimensionsStr = '<small class="text--small"> ' . Label::getLabel('LBL_This_will_be_displayed_on_your_cms_Page') . '</small>';
    $htmlAfterField = $preferredDimensionsStr;
    if (!empty($bgImage)) {
        $htmlAfterField .= '<div class="image-div-js"><ul class="image-listing grids--onethird">';
        $htmlAfterField .= '<li><div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('image', 'show', [Afile::TYPE_CPAGE_BACKGROUND_IMAGE, $cpage_id, Afile::SIZE_SMALL, $cpage_lang_id]) . '" class="bg-image-js"> <a href="javascript:void(0);" onClick="removeBgImage(' . $bgImage['file_record_id'] . ',' . $bgImage['file_lang_id'] . ',' . $cpage_layout . ')" class="remove--img"><i class="ion-close-round"></i></a></div>';
        $htmlAfterField .= '</li></ul></div>';
    } else {
        $htmlAfterField .= '<div class="hide image-div-js"><ul class="image-listing grids--onethird"><li><div class="uploaded--image"></div></li></ul></div>';
    }
    $fld->htmlAfterField = $htmlAfterField;
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Content_Pages_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="addForm(<?php echo $cpage_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        if ($cpage_id > 0) {
                            foreach ($languages as $langId => $langName) {
                        ?>
                                <li><a class="<?php echo ($cpage_lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" onclick="addLangForm(<?php echo $cpage_id ?>, <?php echo $langId; ?>, <?php echo $cpage_layout; ?>);"><?php echo $langName; ?></a></li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php
                            echo $blockLangFrm->getFormTag();
                            echo $blockLangFrm->getFormHtml(false);
                            echo '</form>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>