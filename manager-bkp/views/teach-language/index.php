<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_TEACHING_LANGUAGE'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <span class="-color-secondary span-right"><?php echo Label::getLabel('LBL_TEACHING_LANGUAGE_UPDATE_NOTICE'); ?></span>
                                    <a href="javascript:void(0);" onclick="form(0, 0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <section>
                    <div style="display:none;">
                        <?php
                        $frmSearch->setFormTagAttribute('onsubmit', 'search(this); return(false);');
                        $frmSearch->setFormTagAttribute('class', 'web_form');
                        $frmSearch->developerTags['colClassPrefix'] = 'col-md-';
                        $frmSearch->developerTags['fld_default_col'] = 4;
                        $btn_clear = $frmSearch->getField('btn_clear');
                        $btn_clear->addFieldTagAttribute('onclick', 'clearSearch()');
                        echo $frmSearch->getFormHtml();
                        ?>
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
</div>