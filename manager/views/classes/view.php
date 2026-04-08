<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$yesNoArr = AppConstant::getYesNoArr();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_CLASS_DETAIL'); ?></h4>
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
                                        <?php echo Label::getLabel('LBL_CLASS_NAME'); ?>
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
                                        <?php echo Label::getLabel('LBL_CLASS_STATUS'); ?>
                                    </label>
                                    : <strong><?php echo OrderClass::getStatuses($order['ordcls_status']); ?></strong>
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
                                        <?php echo Label::getLabel('LBL_START_TIME'); ?>
                                    </label>
                                    : <strong><?php echo $order['grpcls_start_datetime']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_END_TIME'); ?>
                                    </label>
                                    : <strong><?php echo $order['grpcls_end_datetime']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TEACHER_START_TIME'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($order['grpcls_teacher_starttime']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TEACHER_END_TIME'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($order['grpcls_teacher_endtime']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LEARNER_START_TIME'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($order['ordcls_starttime']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LEARNER_END_TIME'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($order['ordcls_endtime']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_CLASS_PRICE'); ?>
                                    </label>
                                    : <strong><?php echo MyUtility::formatMoney($order['ordcls_amount']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ADMIN_COMMISSION_(%)'); ?>
                                    </label>
                                    : <strong><?php echo $order['ordcls_commission'] . '%'; ?></strong>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TEACHER_PAID'); ?>
                                    </label>
                                    : <strong><?php echo (is_null($order['ordcls_teacher_paid'])) ? Label::getLabel('LBL_NO') : Label::getLabel('LBL_YES'); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_REVIEWED_ON_CLASS'); ?>
                                    </label>
                                    : <strong><?php echo $yesNoArr[$order['ordcls_reviewed']]; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ISSUE_REPORTED'); ?>
                                    </label>
                                    : <strong><?php echo ($order['repiss_id'] > 0) ? $yesNoArr[AppConstant::YES] : $yesNoArr[AppConstant::NO]; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_REFUND'); ?>
                                    </label>
                                    : <strong><?php echo ($order['ordcls_refund'] > 0) ? MyUtility::formatMoney($order['ordcls_refund']) : Label::getLabel('LBL_N/A'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ORDER_ID'); ?>
                                    </label>
                                    : <strong><a target="_blank" href="<?php echo MyUtility::makeUrl('Orders', 'view', [$order['order_id']]); ?>"><?php echo Label::getLabel('LBL_VIEW') . ' ' . Order::formatOrderId(FatUtility::int($order['order_id'])); ?> </a></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ENDED_BY'); ?>
                                    </label>
                                    : <strong>
                                        <?php
                                            if (!empty($order['ordcls_ended_by'])) {
                                                echo ($order['ordcls_ended_by'] == User::TEACHER)  ? $order['teacher_first_name'] . ' ' . $order['teacher_last_name'] : $order['learner_first_name'] . ' ' . $order['learner_last_name'];
                                            } else {
                                                echo Label::getLabel('LBL_N/A');
                                            };
                                        ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>