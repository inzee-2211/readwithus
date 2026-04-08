<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$form->setFormTagAttribute('class', 'web_form');
$form->developerTags['colClassPrefix'] = 'col-md-';
$form->developerTags['fld_default_col'] = 3;
$submitBtn = $form->getField('btn_submit');
$submitBtn->developerTags['col'] = 6;
$fld = $form->getField('btn_clear');
$fld->addFieldTagAttribute('onclick', 'clearSearch()');
?>
<script>
    var STATUS_APPROVED = <?php echo TeacherRequest::STATUS_APPROVED; ?>;
    var STATUS_CANCELLED = <?php echo TeacherRequest::STATUS_CANCELLED; ?>;
</script>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon">
                                <i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_TEACHER_REQUESTS'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_SEARCH'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php echo $form->getFormHtml(); ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing">
                                <?php echo Label::getLabel('LBL_PROCESSING'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>