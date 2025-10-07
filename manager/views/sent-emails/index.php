<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Sent_Emails_List'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>


                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Sent_Emails_List'); ?> </h4>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="emails-list"> <?php echo Label::getLabel('LBL_Processing...'); ?></div>
                        </div> 
                    </div>
                </section>
            </div>		
        </div>
    </div>
</div>



