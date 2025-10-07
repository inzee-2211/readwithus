<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('onsubmit', 'search(this, 1); return(false);');
$frm->setFormTagAttribute('class', 'web_form');
$fld = $frm->getField('btn_clear');
$fld->addFieldTagAttribute('onclick', 'clearSearch()');
$submitBtn = $frm->getField('BTN_SUBMIT');
$submitBtn->developerTags['col'] = 6;
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon">
                                <i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_ALL_Reported_Issues'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php echo $frm->getFormHtml(); ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="issueListing">
                                <?php echo Label::getLabel('LBL_Processing...'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>