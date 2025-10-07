<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$fld = $frm->getField('coupon_discount_type');
$fld->setFieldTagAttribute('onchange', 'toggleMaxDiscount(this.value);');

$fld = $frm->getField('coupon_max_discount');
$fld->addFieldTagAttribute('id', 'coupon_max_discount');
$fld->setWrapperAttribute('id', 'coupon_max_discount_div');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_COUPON_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="form(<?php echo $couponId; ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        $inactive = ($couponId == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($couponId > 0) { ?> onclick="langForm(<?php echo $couponId ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>