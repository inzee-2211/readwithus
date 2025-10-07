<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='container container-fluid'>
        <div class="page__title">
            <div class="row">
                <div class="col--first col-lg-auto">
                    <span class="page__icon"><i class="ion-android-star"></i></span>
                    <h5><?php echo Label::getLabel('LBL_Manage_Coupons'); ?> </h5>
                    <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                </div>
                <?php if ($canEdit) { ?>
                    <div class="col-lg-auto">
                        <div class="buttons-group">
                            <a href="javascript:void(0);" onclick="form(0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div> 
        <section class="section searchform_filter">
            <div class="sectionhead">
                <h4> <?php echo Label::getLabel('LBL_Search'); ?></h4>
            </div>
            <div class="sectionbody space togglewrap" style="display:none;">
                <?php
                $frmSearch->setFormTagAttribute('onsubmit', 'search(this); return(false);');
                $frmSearch->setFormTagAttribute('class', 'web_form');
                echo $frmSearch->getFormHtml();
                ?>    
            </div>
        </section> 
        <section class="section">
            <div class="sectionbody">
                <div class="tablewrap" >
                    <div id="listing"> <?php echo Label::getLabel('LBL_Processing...'); ?></div>
                </div> 
            </div>
        </section>
    </div>
</div>