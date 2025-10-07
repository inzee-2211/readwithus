<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-lg-12 col-md-12 col-sm-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('id', 'paystackPayFrm');
$frm->setFormTagAttribute('class', 'form form--normal');
$frm->setFormTagAttribute('action', $response['data']['authorization_url']);
$btn = $frm->getField('btn_submit');
$btn->setFieldTagAttribute('class', "d-none");
?>
<div class="payment-page">
    <div class="cc-payment">
        <div class="logo-payment">
            <?php if (MyUtility::isDemoUrl()) { ?>
                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
            <?php } else { ?>
                <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_PAYMENT_PAGE_LOGO, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONT_URL); ?>" alt="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId) ?>" title="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId) ?>" />
            <?php } ?>
        </div>
        <div class="reff row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class=""><?php echo Label::getLabel('LBL_Payable_Amount'); ?> : <strong><?php echo MyUtility::formatMoney($order['order_net_amount']) ?></strong> </p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class=""><?php echo Label::getLabel('LBL_Order_Invoice'); ?>: <strong><?php echo Order::formatOrderId($order["order_id"]); ?></strong></p>
            </div>
        </div>
        <div class="payment-from">
            <div class="payable-form__body" id="paymentFormElement-js">
                <h6><?php echo Label::getLabel('LBL_REDIRECTING_TO_PAYMENT_PAGE...'); ?></h6>
                <?php echo $frm->getFormHtml(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("form#paystackPayFrm").submit();
</script>
