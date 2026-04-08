<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='container container-fluid'>
        <div class="row">
            <div class="col-lg-12 col-md-12 space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Faq_Categories'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <a href="javascript:void(0);" onclick="addFaqCatForm(0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <section class="section searchform_filter" style="display:none;">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap">
                        <?php
                        $searchFrm->setFormTagAttribute('onsubmit', 'searchFaqCategories(this); return(false);');
                        $searchFrm->setFormTagAttribute('class', 'web_form');
                        $searchFrm->developerTags['colClassPrefix'] = 'col-md-';
                        $searchFrm->developerTags['fld_default_col'] = 4;
                        echo $searchFrm->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing"> <?php echo Label::getLabel('LBL_Processing'); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
