<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$allAccessfrm->developerTags['colClassPrefix'] = 'col-md-';
$allAccessfrm->developerTags['fld_default_col'] = 12;
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon">
                                <i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage'); ?> <?php echo $data['admin_username']; ?> <?php echo Label::getLabel('LBL_User_Permission'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Admin_User_Listing'); ?> : <?php echo $data['admin_username']; ?></h4>	
                    </div>
                    <?php if($canEdit){ ?>
                    <div class="sectionbody space">      
                        <div class="tabs_nav_container responsive flat">
                            <div class="tabs_panel_wrap">
                                <div class="tabs_panel">
                                    <?php echo $allAccessfrm->getFormHtml(); ?>
                                </div>
                            </div>						
                        </div>
                    </div>		
                    <?php } ?>				
                </section>					                   
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing">
                                <?php echo Label::getLabel('LBL_Processing...'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>