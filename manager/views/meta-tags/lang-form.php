<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$langFrm->setFormTagAttribute('onsubmit', 'setupLangMetaTag(this,"' . $metaType . '"); return(false);');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 12;
$otherMetatagsFld = $langFrm->getField('meta_other_meta_tags');
$otherMetatagsFld->htmlAfterField = '<small>' . htmlentities(stripslashes(Label::getLabel('LBL_OTHER_META_TAG_EXAMPLE', $langId))) . '</small>';
$fld1 = $langFrm->getField('open_graph_image');
$fld1->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$htmlAfterField = '<div style="margin-top:15px;" class="preferredDimensions-js">' . sprintf(Label::getLabel('LBL_Preferred_Dimensions_%s'), '1200 x 627') . '</div>';
$htmlAfterField .= '<div id="image-listing"></div>';
$fld1->htmlAfterField = $htmlAfterField;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Meta_Tag_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="editMetaTagForm('<?php echo $metaId; ?>','<?php echo $metaType; ?>','<?php echo $recordId; ?>');"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        if ($metaId > 0) {
                            foreach ($languages as $lang_Id => $langName) {
                        ?>
                                <li><a class="<?php echo ($lang_Id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" onclick="editMetaTagLangForm('<?php echo $metaId ?>','<?php echo $lang_Id; ?>','<?php echo $metaType; ?>');"><?php echo $langName; ?></a></li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $langFrm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>