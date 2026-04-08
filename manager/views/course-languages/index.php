<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_COURSE_LANGUAGE'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <span class="-color-secondary span-right"><?php echo Label::getLabel('LBL_COURSE_LANGUAGE_UPDATE_NOTICE'); ?></span>
                                    <a href="javascript:void(0);" onclick="form(0, 0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="listing"> <?php echo Label::getLabel('LBL_PROCESSING'); ?></div>
                        </div> 
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>