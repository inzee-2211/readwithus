<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$srchFrm->setFormTagAttribute('onsubmit', 'searchPages(this); return(false);');
$srchFrm->setFormTagAttribute('class', 'web_form');
$submitBtn = $srchFrm->getField('btn_submit');
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
                            <h5><?php echo Label::getLabel('LBL_MANAGE_BIBLE_CONTENT'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <a href="javascript:void(0);" onclick="addForm(0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <section class="section searchform_filter" style="display:none;">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" >
                        <?php echo $srchFrm->getFormHtml(); ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="pageListing">
                                <?php echo Label::getLabel('LBL_Processing...'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>