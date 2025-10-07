<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">  
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MY_PROFILE'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                    <div class="row" id="profileInfoFrmBlock">
                        <?php echo Label::getLabel('LBL_Loading..'); ?>
                    </div>
                </div>	              
            </div>     
        </div>
    </div>
</div>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
