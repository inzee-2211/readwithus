<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$yesNoArr = AppConstant::getYesNoArr();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_VIEW_LESSON_DETAIL'); ?></h4>
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
                                        <?php echo Label::getLabel('LBL_LANGUAGE'); ?>
                                    </label>
                                    : <strong><?php echo $order['ordles_tlang_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_STATUS'); ?>
                                    </label>
                                    : <strong><?php echo Lesson::getStatuses($order['ordles_status']); ?></strong>
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
                                    : <strong><?php echo $order['ordles_lesson_starttime']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ENDS'); ?>
                                    </label>
                                    : <strong><?php echo $order['ordles_lesson_endtime']; ?></strong>
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
                                    : <strong><?php echo MyDate::formatDate($order['ordles_teacher_starttime']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TEACHER_END_TIME'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($order['ordles_teacher_endtime']); ?></strong>
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
                                    : <strong><?php echo MyDate::formatDate($order['ordles_student_starttime']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LEARNER_END_TIME'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($order['ordles_student_endtime']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LESSON_PRICE'); ?>
                                    </label>
                                    : <strong><?php echo MyUtility::formatMoney($order['ordles_amount']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ADMIN_COMMISSION_(%)'); ?>
                                    </label>
                                    : <strong><?php echo $order['ordles_commission'] . '%'; ?></strong>
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
                                    : <strong><?php echo (is_null($order['ordles_teacher_paid'])) ? Label::getLabel('LBL_NO') : Label::getLabel('LBL_YES'); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_REVIEWED_ON_LESSON'); ?>
                                    </label>
                                    : <strong><?php echo $yesNoArr[$order['ordles_reviewed']]; ?></strong>
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
                                    : <strong><?php echo ($order['ordles_refund'] > 0) ? MyUtility::formatMoney($order['ordles_refund']) : Label::getLabel('LBL_N/A'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_DURATION'); ?>
                                    </label>
                                    : <strong><?php echo $order['ordles_duration'] . ' ' . Label::getLabel('LBL_MINS'); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ORDER_ID'); ?>
                                    </label>
                                    : <strong><a target="_blank" href="<?php echo MyUtility::makeUrl('Orders', 'view', [$order['ordles_order_id']]); ?>"><?php echo Label::getLabel('LBL_VIEW') . ' ' . Order::formatOrderId($order['ordles_order_id']); ?> </a></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LESSON_ENDED_BY'); ?>
                                    </label>
                                    : <strong><?php if (!empty($order['ordles_ended_by'])) {
                                                    echo ($order['ordles_ended_by'] == User::TEACHER)  ? $order['teacher_first_name'] . ' ' . $order['teacher_last_name'] : $order['learner_first_name'] . ' ' . $order['learner_last_name'];
                                                } else {
                                                    echo Label::getLabel('LBL_N/A');
                                                }; ?>
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