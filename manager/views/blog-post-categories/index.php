<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$searchFrm->setFormTagAttribute('onsubmit', 'searchBlogPostCategories(this); return(false);');
$searchFrm->setFormTagAttribute('class', 'web_form');
$btn_clear = $searchFrm->getField('btn_clear');
$btn_clear->addFieldTagAttribute('onclick', 'clearSearch()');
$submitBtn = $searchFrm->getField('btn_submit');
$submitBtn->developerTags['col'] = 6;
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Blog_Post_Categories'); ?> <?php echo $bpCatData['bpcategory_identifier'] ?? ''; ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <a href="javascript:void(0);" onclick="addCategoryForm(0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <section class="section searchform_filter" style="display:none;">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Search'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" >
                        <?php echo $searchFrm->getFormHtml(); ?>    
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="listing"><?php echo Label::getLabel('LBL_processing'); ?></div>
                        </div> 
                    </div>
                </section>
            </div>		
        </div>
    </div>
</div>
