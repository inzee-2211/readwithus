<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
define('SYSTEM_FRONT', true);
$url = MyUtility::makeFullUrl('', '', [], CONF_WEBROOT_FRONTEND) . ltrim(MyUtility::makeUrl('Blog', 'postDetail', [$post_id], CONF_WEBROOT_FRONT_URL), '/');
$frm->setFormTagAttribute('id', 'bpCat');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$identiFierFld = $frm->getField('post_identifier');
$identiFierFld->setFieldTagAttribute('onkeyup', "Slugify(this.value,'seourl_custom','post_id');getSlugUrl($(\"#seourl_custom\"),$(\"#seourl_custom\").val())");
$IDFld = $frm->getField('post_id');
$IDFld->setFieldTagAttribute('id', "post_id");
$urlFld = $frm->getField('seourl_custom');
$urlFld->setFieldTagAttribute('id', "seourl_custom");
$urlFld->htmlAfterField = "<small class='text--small'>" .  $url . '</small>';
$urlFld->setFieldTagAttribute('onkeyup', "getSlugUrl(this,this.value)");
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
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
                        <li><a class="active" href="javascript:void(0);" onclick="blogPostForm(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($post_id > 0) { ?> onclick="linksForm(<?php echo $post_id ?>);" <?php } ?>><?php echo Label::getLabel('LBL_Link_Category'); ?></a></li>
                        <?php foreach ($languages as $langId => $langName) { ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($post_id > 0) { ?> onclick="langForm(<?php echo $post_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($post_id > 0) { ?> onclick="postImages(<?php echo $post_id ?>);" <?php } ?>><?php echo Label::getLabel('LBL_Post_Images'); ?></a></li>
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