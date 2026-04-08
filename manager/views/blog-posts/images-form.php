<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$imagesFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$imagesFrm->developerTags['colClassPrefix'] = 'col-md-';
$imagesFrm->developerTags['fld_default_col'] = 12;
$img_fld = $imagesFrm->getField('post_image');
$img_fld->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$langFld = $imagesFrm->getField('lang_id');
$langFld->addFieldTagAttribute('class', 'language-js');
$preferredDimensionsStr = '<small class="text--small">' . sprintf(Label::getLabel('LBL_Preferred_Dimensions_%s'), '945*710') . '</small>';
$htmlAfterField = $preferredDimensionsStr;
$img_fld->htmlAfterField = $htmlAfterField;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Blog_Post_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">		
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <?php $inactive = ($post_id == 0) ? 'fat-inactive' : ''; ?>
                        <li><a href="javascript:void(0);" onclick="blogPostForm(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <li><a href="javascript:void(0);" onclick="linksForm(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_Link_Category'); ?></a></li>
                        <?php foreach ($languages as $langId => $langName) { ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($post_id > 0) { ?> onclick="langForm(<?php echo $post_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li><a class="active" href="javascript:void(0);" onclick="postImages(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_Post_Images'); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <div class="col-sm-12">
                                <h4><?php echo Label::getLabel('LBL_Post_Images'); ?></h4>
                                <?php echo $imagesFrm->getFormHtml(); ?>
                                <div id="image-listing"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>