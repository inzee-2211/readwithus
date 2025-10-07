<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'setupOfferPrice(this); return(false);');
?>
<div class="box -padding-20">
    <?php
    echo $frm->getFormTag();
    echo $frm->getFieldHtml('offpri_learner_id');
    echo $frm->getFieldHtml('offpri_id');
    ?>
    <h3 class="page-heading" style="text-align: center;"><?php echo sprintf(Label::getLabel("LBL_OFFER_PERCENTAGE_FOR_%s"), ucfirst($offers['user_first_name'] . ' ' . $offers['user_last_name'])) ?></h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-box-bordered box-signle-price">
                <h5 class="margin-bottom-0 margin-top-6"><?php echo Label::getLabel("LBL_LESSON_OFFER") ?></h5>
                <table class="table-pricing">
                    <tbody>
                        <?php foreach ($userSlots as $userSlot) { ?>
                            <tr>
                                <td width="50%"><?php echo $frm->getField('offpri_lesson_price[' . $userSlot . ']')->getCaption(); ?></td>
                                <td>
                                    <?php echo $frm->getFieldHtml('offpri_lesson_price[' . $userSlot . ']') ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-box-bordered box-signle-price">
                <h5 class="margin-bottom-0 margin-top-6"><?php echo Label::getLabel("LBL_CLASS_OFFER") ?></h5>
                <table class="table-pricing">
                    <tbody>
                        <?php foreach ($classSlots as $classSlot) { ?>
                            <tr>
                                <td width="50%"><?php echo $frm->getField('offpri_class_price[' . $classSlot . ']')->getCaption(); ?></td>
                                <td>
                                    <?php echo $frm->getFieldHtml('offpri_class_price[' . $classSlot . ']') ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-box-bordered box-signle-price">
                <h5 class="margin-bottom-0 margin-top-6"><?php echo Label::getLabel("LBL_CLASS_PACKAGE_OFFER") ?></h5>
                <table class="table-pricing">
                    <tbody>
                        <tr>
                            <td width="50%"><?php echo $frm->getField('offpri_package_price')->getCaption(); ?></td>
                            <td>
                                <?php echo $frm->getFieldHtml('offpri_package_price'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="fld_wrapper-js col-md-2">
            <div class="field-set">
                <div class="caption-wraper"><label class="field_label">&nbsp;</label></div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frm->getFieldHtml('btn_submit'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <?php echo $frm->getExternalJs(); ?>
</div>