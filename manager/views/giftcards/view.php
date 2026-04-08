<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$yesNoArr = AppConstant::getYesNoArr();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_VIEW_GIFTCARDS_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_USER_NAME'); ?>
                                    </label>
                                    : <strong><?php echo $order['user_full_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_PAYMENT_STATUS'); ?>
                                    </label>
                                    : <strong><?php echo Order::getPaymentArr($order['order_payment_status']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_GIFTCARD_CODE'); ?>
                                    </label>
                                    : <strong><?php echo $order['code']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_RECIPIENT_NAME'); ?>
                                    </label>
                                    : <strong><?php echo $order['receiver_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_RECIPIENT_EMAIL'); ?>
                                    </label>
                                    : <strong><?php echo $order['receiver_email']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_GIFTCARD_STATUS'); ?>
                                    </label>
                                    : <strong><?php echo Giftcard::getStatuses($order['ordgift_status']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_AMOUNT'); ?>
                                    </label>
                                    : <strong><?php echo MyUtility::formatMoney($order['order_net_amount']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>