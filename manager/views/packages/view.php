<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$yesNoArr = AppConstant::getYesNoArr();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_PACKAGE_DETAIL'); ?></h4>
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
                                        <?php echo Label::getLabel('LBL_LEARNER_NAME'); ?>
                                    </label>
                                    : <strong><?php echo $order['learner_first_name'] . ' ' . $order['learner_last_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TEACHER_NAME'); ?>
                                    </label>
                                    : <strong><?php echo $order['teacher_first_name'] . ' ' . $order['teacher_last_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_PACKAGE_NAME'); ?>
                                    </label>
                                    : <strong><?php echo $order['grpcls_title']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LANGUAGE'); ?>
                                    </label>
                                    : <strong><?php echo $order['grpcls_tlang_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_PACKAGE_STATUS'); ?>
                                    </label>
                                    : <strong><?php echo OrderPackage::getStatuses($order['ordpkg_status']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ORDER_PAYMENT_STATUS'); ?>
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
                                        <?php echo Label::getLabel('LBL_PACKAGE_START_TIME'); ?>
                                    </label>
                                    : <strong><?php echo $order['grpcls_start_datetime']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_PACKAGE_END_TIME'); ?>
                                    </label>
                                    : <strong><?php echo $order['grpcls_end_datetime']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_PACKAGE_PRICE'); ?>
                                    </label>
                                    : <strong><?php echo MyUtility::formatMoney($order['ordpkg_amount']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ORDER_ID'); ?>
                                    </label>
                                    : <strong><a target="_blank" href="<?php echo MyUtility::makeUrl('Orders', 'view', [$order['order_id']]); ?>"><?php echo Label::getLabel('LBL_VIEW') . ' ' . Order::formatOrderId(FatUtility::int($order['order_id'])); ?> </a></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_VIEW_CLASSES'); ?>
                                    </label>
                                    : <strong><a target="_blank" href="<?php echo MyUtility::makeUrl('Classes').'?order_id='.$order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_CLASSES'); ?> </a></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>