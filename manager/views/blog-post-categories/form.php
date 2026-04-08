<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'bpCat');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupCategory(this); return(false);');
$identiFierFld = $frm->getField('bpcategory_identifier');
$identiFierFld->setFieldTagAttribute('onkeyup', "Slugify(this.value,'seourl_custom','bpcategory_id');getSlugUrl($(\"#seourl_custom\"),$(\"#seourl_custom\").val(),'','pre',true)");
$IDFld = $frm->getField('bpcategory_id');
$IDFld->setFieldTagAttribute('id', "bpcategory_id");
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_BLOG_POST_CATEGORY_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">		
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="categoryForm(<?php echo $bpcategory_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        $inactive = ($bpcategory_id == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($bpcategory_id > 0) { ?> onclick="categoryLangForm(<?php echo $bpcategory_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>