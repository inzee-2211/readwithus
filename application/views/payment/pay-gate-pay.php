<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php $currency = MyUtility::getSystemCurrency(); ?>
<section class="section section--grey section--page -pattern">
    <div class="container container--fixed">
        <div class="page-panel -clearfix">
            <div class="page__panel-narrow">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="box -padding-30 -skin">
                            <div class="box__data">
                                <div class="loader"></div>
                                <div class="-align-center">
                                    <h1 class="-color-secondary"><?php echo Label::getLabel('LBL_WE_ARE_REDIRECTING_YOU'); ?></h1>
                                    <h4><?php echo Label::getLabel('LBL_PLEASE_WAIT..'); ?></h4>
                                </div>
                            </div>
                            <div class="-align-center">
                                <div class="-gap"></div>
                                <p><?php echo Label::getLabel('LBL_Payable_Amount'); ?> : <strong><?php echo MyUtility::formatMoney($order['order_net_amount']); ?></strong> </p>
                                <p><?php echo Label::getLabel('LBL_Order_Invoice'); ?>: <strong><?php echo Order::formatOrderId($order["order_id"]); ?></strong></p>
                                <?php if ($order['order_currency_code'] != $currency['currency_code']) { ?>
                                    <p class="-color-secondary"><?php echo MyUtility::getCurrencyDisclaimer($order['order_net_amount']); ?></p>
                                <?php } ?>
                                <div class="-gap"></div>
                            </div>
                            <div class="-align-center">
                                <div class="payment-from">
                                    <?php echo $frm->getFormHtml(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(function () {
        setTimeout(function () {
            $('form[name="payGatePayForm"]').submit();
        }, 5000);
    });
</script>
