<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4><?php echo Label::getLabel('LBL_ISSUE_DETAIL'); ?></h4>
        <?php if ($issue['repiss_record_type'] == AppConstant::LESSON && $order['order_discount_value'] > 0) { ?>
            <span class="-color-primary color-secondary"><?php echo Label::getLabel('LBL_NOTE_REFUND_LESSON_DISCOUNT_ORDER_TEXT'); ?></span>
        <?php } ?>


    </div>
    <div class="facebox-panel__body padding-0">
        <div class="detail-group-row">
            <div class="detail-row">
                <div class="detail-row__primary">
                    <span class="card-landscape__status badge color-red badge--curve margin-left-0 margin-right-5">
                        <?php echo Issue::getStatusArr($issue['repiss_status']); ?>
                    </span>
                    <?php echo Label::getLabel('LBL_ISSUE'); ?> <span class="tag"><?php echo $issue['repiss_title']; ?></span>
                    <?php echo Label::getLabel('LBL_WAS_POSTED_BY'); ?> <span class="tag"><?php echo $issue['learner_full_name']; ?></span>
                </div>
                <div class="detail-row__secondary">
                    <div class="date">
                        <?php echo MyDate::formatDate($issue['repiss_reported_on'], 'H:i:a'); ?>
                        <span><?php echo MyDate::formatDate($issue['repiss_reported_on'], 'M d,Y'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($issue['canEscalateIssue'] || $issue['repiss_status'] == Issue::STATUS_ESCALATED) { ?>
            <div class="detail-group-row">
                <div class="detail-row">
                    <div class="detail-row__primary">
                        <span class="card-landscape__status badge badge--curve margin-left-0 margin-right-5">
                            <?php echo Label::getLabel('LBL_NOT_HAPPY_WITH_SOLUTION?'); ?>
                        </span>
                    </div>
                    <div class="detail-row__secondary">
                        <?php if ($issue['canEscalateIssue']) { ?>
                            <button onclick="escalate('<?php echo $issue['repiss_id']; ?>')" class="btn btn-small btn--primary">
                                <?php echo Label::getLabel('LBL_ESCALATE_TO_SUPPORT_TEAM'); ?>
                            </button>
                        <?php } elseif ($issue['repiss_status'] == Issue::STATUS_ESCALATED) { ?>
                            <?php echo Label::getLabel('LBL_ESCALATED_TO_SUPPORT_TEAM'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="detail-group-row background-gray">
            <div class="detail-group-title">
                <h4><?php echo Label::getLabel('LBL_ISSUE_LOGS'); ?></h4>
            </div>
            <div class="issue-log">
                <div class="issue-log__item">
                    <div class="detail-row">
                        <div class="detail-row__primary">
                            <span class="bold-600 color-black"><?php echo $issue['learner_full_name'] . ' [' . Issue::getUserTypeArr(User::LEARNER) . ']'; ?></span>
                            <?php echo Label::getLabel('LBL_TAKE_ACTION'); ?><span class="tag"><?php echo $issue['repiss_title']; ?></span>
                            <div class="comment">
                                <span class="bold-600 margin-right-2">
                                    <?php echo Label::getLabel('LBL_Comment'); ?>:
                                </span>
                                <?php echo nl2br($issue['repiss_comment']); ?>
                            </div>
                        </div>
                        <div class="detail-row__secondary">
                            <div class="date">
                                <?php echo MyDate::formatDate($issue['repiss_reported_on'], 'H:i:a'); ?>
                                <span><?php echo MyDate::formatDate($issue['repiss_reported_on'], 'M d,Y'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php foreach ($logs as $log) { ?>
                    <div class="issue-log__item">
                        <div class="detail-row">
                            <div class="detail-row__primary">
                                <span class="bold-600 color-black"><?php echo $log['user_fullname'] . ' [' . Issue::getUserTypeArr($log['reislo_added_by_type']) . ']'; ?></span>
                                <?php echo Label::getLabel('LBL_TAKE_ACTION'); ?><span class="tag"><?php echo Issue::getActionsArr($log['reislo_action']); ?></span>
                                <div class="comment">
                                    <span class="bold-600 margin-right-2">
                                        <?php echo Label::getLabel('LBL_Comment'); ?>:
                                    </span>
                                    <?php echo nl2br($log['reislo_comment']); ?>
                                </div>
                            </div>
                            <div class="detail-row__secondary">
                                <div class="date">
                                    <?php echo MyDate::formatDate($log['reislo_added_on'], 'H:i:a'); ?>
                                    <span><?php echo MyDate::formatDate($log['reislo_added_on'], 'M d,Y'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="detail-group-row">
            <div class="detail-group-title">
                <?php if (AppConstant::GCLASS == $issue['repiss_record_type']) { ?>
                    <h4><?php echo Label::getLabel('LBL_CLASS_DETAILS'); ?></h4>
                <?php } else { ?>
                    <h4><?php echo Label::getLabel('LBL_LESSON_DETAILS'); ?></h4>
                <?php } ?>
            </div>
            <div class="info-panel">
                <div class="info-panel__cover">
                    <?php if (AppConstant::GCLASS == $issue['repiss_record_type']) { ?>
                        <div class="info-panel__title">
                            <h4><?php echo Label::getLabel('LBL_CLASS'); ?></h4>
                        </div>
                        <div class="info-panel__body">
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_ORDER_ID') ?></div>
                                <div><?php echo Order::formatOrderId($issue['ordles_order_id']); ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_CLASS_ID') ?></div>
                                <div><?php echo $issue['ordles_id']; ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_TEACHER_CLASS_ID') ?></div>
                                <div><?php echo $issue['grpcls_id']; ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_CLASS_PRICE'); ?></div>
                                <div><?php echo MyUtility::formatMoney($issue['ordles_amount']); ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_ENDED_BY'); ?></div>
                                <div>
                                    <?php
                                    if (!empty($issue['ordles_ended_by'])) {
                                        echo ($issue['ordles_ended_by'] == User::TEACHER)  ? $issue['teacher_full_name'] : $issue['learner_full_name'];
                                    } else {
                                        echo Label::getLabel('LBL_N/A');
                                    }; ?>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="info-panel__title">
                            <h4><?php echo Label::getLabel('LBL_LESSON'); ?></h4>
                        </div>
                        <div class="info-panel__body">
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_ORDER_ID') ?></div>
                                <div><?php echo Order::formatOrderId($issue['ordles_order_id']); ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_LESSON_ID') ?></div>
                                <div><?php echo $issue['ordles_id']; ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_LESSON_PRICE'); ?></div>
                                <div><?php echo  MyUtility::formatMoney($issue['ordles_amount']); ?></div>
                            </div>
                            <div class="info-panel__item">
                                <div><?php echo Label::getLabel('LBL_ENDED_BY'); ?></div>
                                <div>
                                    <?php
                                    if (!empty($issue['ordles_ended_by'])) {
                                        echo ($issue['ordles_ended_by'] == User::TEACHER)  ? $issue['teacher_full_name'] : $issue['learner_full_name'];
                                    } else {
                                        echo Label::getLabel('LBL_N/A');
                                    }; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="info-panel__cover">
                    <div class="info-panel__title">
                        <h4><?php echo $issue['teacher_full_name'] . ' [' . Label::getLabel('LBL_TEACHER') . ']'; ?> </h4>
                    </div>
                    <div class="info-panel__body">
                        <div class="info-panel__item">
                            <div><?php echo Label::getLabel('LBL_JOIN_TIME'); ?></div>
                            <div>
                                <?php echo MyDate::formatDate($issue['ordles_teacher_starttime'], 'H:i:a'); ?>
                                <span><?php echo MyDate::formatDate($issue['ordles_teacher_starttime'], 'M d,Y'); ?></span>
                            </div>
                        </div>
                        <div class="info-panel__item">
                            <div><?php echo Label::getLabel('LBL_END_TIME'); ?></div>
                            <div>
                                <?php echo MyDate::formatDate($issue['ordles_teacher_endtime'], 'H:i:a'); ?>
                                <span><?php echo MyDate::formatDate($issue['ordles_teacher_endtime'], 'M d,Y'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-panel__cover">
                    <div class="info-panel__title">
                        <h4><?php echo $issue['learner_full_name'] . ' [' . Label::getLabel('LBL_LEARNER') . ']'; ?> </h4>
                    </div>
                    <div class="info-panel__body">
                        <div class="info-panel__item">
                            <div><?php echo Label::getLabel('LBL_JOIN_TIME'); ?></div>
                            <div>
                                <?php echo MyDate::formatDate($issue['ordles_student_starttime'], 'H:i:a'); ?>
                                <span><?php echo MyDate::formatDate($issue['ordles_student_starttime'], 'M d,Y'); ?></span>
                            </div>
                        </div>
                        <div class="info-panel__item">
                            <div><?php echo Label::getLabel('LBL_END_TIME'); ?></div>
                            <div>
                                <?php echo MyDate::formatDate($issue['ordles_student_endtime'], 'H:i:a'); ?>
                                <span><?php echo MyDate::formatDate($issue['ordles_student_endtime'], 'M d,Y'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>