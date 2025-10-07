<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_PREFERENCES'); ?> > <?php echo Preference::getPreferenceTypeArr()[$type]; ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <span class="-color-secondary span-right"><?php echo Label::getLabel('LBL_PREFERENCES_UPDATE_NOTICE'); ?></span>
                                    <a href="javascript:void(0);" onclick="preferenceForm(0, '<?php echo $type; ?>');" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <section class="section searchform_filter" style="display:none;">
                <div class="sectionhead">
                    <h4> <?php echo Label::getLabel('LBL_SEARCH'); ?></h4>
                </div>
                <div class="sectionbody space togglewrap">
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