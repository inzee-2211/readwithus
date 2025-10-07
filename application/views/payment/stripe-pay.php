<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php $currency = MyUtility::getSystemCurrency(); ?>

<!-- ✅ Load Stripe JS BEFORE using it -->
<script src="https://js.stripe.com/v3/"></script>

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
                                    <h1 class="-color-secondary">We're redirecting you!!</h1>
                                    <h4>Please wait...</h4>
                                </div>
                            </div>
                            <div class="message-display">
                                <p><?php echo Label::getLabel('LBL_Payable_Amount'); ?> :
                                    <strong><?php echo MyUtility::formatMoney($order['order_net_amount']) ?></strong>
                                </p>
                                <p><?php echo Label::getLabel('LBL_Order_Invoice'); ?>:
                                    <strong><?php echo Order::formatOrderId($order["order_id"]); ?></strong>
                                </p>
                                <?php if ($order['order_currency_code'] != $currency['currency_code']) { ?>
                                    <p class="-color-secondary">
                                        <?php echo MyUtility::getCurrencyDisclaimer($order['order_net_amount']); ?>
                                    </p>
                                <?php } ?>
                            </div>
                            <div class="-align-center">
                                <div class="payment-from">
                                    <?php if (isset($error)) { ?>
                                        <div class="alert alert--danger"><?php echo $error ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ✅ Stripe Redirection Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var stripe = Stripe("<?php echo $stripe['publishable_key']; ?>");

        stripe.redirectToCheckout({
            sessionId: '<?php echo $sessionId ?>'
        }).then(function (result) {
            if (result.error) {
                console.error(result.error.message);
                alert(result.error.message);
            }
        });
    });
</script>
