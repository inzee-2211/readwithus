<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSearch->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$frmSearch->setFormTagAttribute('id', 'search');
$frmSearch->setFormTagAttribute('style', 'display:none');
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_CATEGORIES'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <?php if ($canEdit || $parentId > 0) { ?>
                            <div class="col-lg-auto">
                                <div class="buttons-group">
                                    <?php if ($canEdit) { ?>
                                        <a href="javascript:void(0);" onclick="categoryForm(0, '<?php echo $siteLangId; ?>');" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                    <?php } ?>
                                    <?php if ($parentId > 0) { ?>
                                        <a href="<?php echo MyUtility::makeUrl('Categories'); ?>" class="btn-primary"><?php echo Label::getLabel('LBL_BACK'); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php echo $frmSearch->getFormHtml(); ?>
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