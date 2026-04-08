<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form layout--');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = '12';
if (!$canEdit) {
    $submitBtn = $frm->getField('btn_submit');
    $frm->removeField($submitBtn);
}
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_ROBOTS_FILE'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <span class="-color-secondary span-right"><?php echo stripslashes(Label::getLabel('NOTE_Robots_File_Modification')); ?></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tabs_panel_wrap">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>