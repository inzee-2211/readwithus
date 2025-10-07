<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('id', 'bpCat');
$langFrm->setFormTagAttribute('class', 'web_form layout--' . $formLayout);
$langFrm->setFormTagAttribute('onsubmit', 'langSetup(this); return(false);');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 12;
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
                        <li><a href="javascript:void(0);" onclick="blogPostForm(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <li><a href="javascript:void(0);" onclick="linksForm(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_Link_Category'); ?></a></li>
                        <?php
                        if ($post_id > 0) {
                            foreach ($languages as $langId => $langName) {
                                ?>
                                <li><a class="<?php echo ($post_lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" onclick="langForm(<?php echo $post_id ?>, <?php echo $langId; ?>);"><?php echo $langName; ?></a></li>
                                <?php
                            }
                        }
                        ?>
                        <li><a href="javascript:void(0);" onclick="postImages(<?php echo $post_id ?>);"><?php echo Label::getLabel('LBL_Post_Images'); ?></a></li>
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