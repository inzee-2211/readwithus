<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSearch->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$frmSearch->setFormTagAttribute('id', 'search');
$frmSearch->setFormTagAttribute('class', 'web_form');
$btn = $frmSearch->getField('btn_clear');
$btn->setFieldTagAttribute('onClick', 'clearSearch()');
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_COMMISSION_SETTINGS'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <a href="javascript:void(0);" onclick="commissionForm(0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <section class="section searchform_filter">
                <div class="sectionhead">
                    <h4> <?php echo Label::getLabel('LBL_Search'); ?></h4>
                </div>
                <div class="sectionbody space togglewrap" style="display:none;">
                    <?php echo $frmSearch->getFormHtml(); ?>
                </div>
            </section>
            <section class="section">
                <div class="sectionbody">
                    <div class="tablewrap">
                        <div id="listing"> <?php echo Label::getLabel('LBL_PROCESSING'); ?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>