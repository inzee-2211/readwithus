<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_ISSUE_LOGS'); ?>
            <?php if ($issue['repiss_record_type'] == AppConstant::LESSON && $order['order_discount_value'] > 0) { ?>
                <span class="-color-primary color-secondary"><?php echo Label::getLabel('LBL_NOTE_REFUND_LESSON_DISCOUNT_ORDER_TEXT'); ?></span>
            <?php } ?>
        </h4>

        <div>
            <h4>
                <?php echo Label::getLabel('LBL_ISSUE_STATUS'); ?>:</strong>
                <?php echo Issue::getStatusArr($issue['repiss_status']); ?>
            </h4>
        </div>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <thead>
                <tr>
                    <th><?php echo Label::getLabel('LBL_ACTION_BY'); ?></th>
                    <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
                    <th><?php echo Label::getLabel('LBL_COMMENT'); ?></th>
                    <th><?php echo Label::getLabel('LBL_ACTION_ON'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php echo $issue['learner_full_name']; ?>
                        <?php echo '(' . Issue::getUserTypeArr(User::LEARNER) . ')'; ?>
                    </td>
                    <td><?php echo $issue['repiss_title']; ?></td>
                    <td><?php echo nl2br($issue['repiss_comment']); ?></td>
                    <td><?php echo MyDate::formatDate($issue['repiss_reported_on']); ?></td>
                </tr>
                <?php foreach ($logs as $log) { ?>
                    <tr>
                        <td>
                            <?php echo $log['user_fullname']; ?>
                            <?php echo '(' . Issue::getUserTypeArr($log['reislo_added_by_type']) . ')'; ?>
                        </td>
                        <td><?php echo $actionArr[$log['reislo_action']]; ?></td>
                        <td><?php echo nl2br($log['reislo_comment']); ?></td>
                        <td><?php echo MyDate::formatDate($log['reislo_added_on']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_RECORD_DETAILS'); ?></h4>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_LANGUAGE'); ?>:</strong> <?php echo $issue['ordles_tlang_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_FREE_TRAIL'); ?>:</strong> <?php echo is_null($issue['ordles_tlang_id']) ? Label::getLabel('LBL_YES') : Label::getLabel('LBL_NO'); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_ORDER_ID'); ?>:</strong> <?php echo Order::formatOrderId(FatUtility::int($order['order_id'])); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_RECORD_ID'); ?>:</strong> <?php echo $issue['repiss_record_id']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_TOTAL_ITEM'); ?>:</strong> <?php echo $order['order_item_count']; ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_PRICE'); ?>:</strong> <?php echo MyUtility::formatMoney($issue['ordles_amount']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_ORDER_NET_AMOUNT'); ?>:</strong> <?php echo MyUtility::formatMoney($order['order_net_amount']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_ORDER_DISCOUNT_TOTAL'); ?>:</strong> <?php echo  MyUtility::formatMoney($order['order_discount_value']); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_Name'); ?>:</strong> <?php echo $issue['teacher_full_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_Join_Time'); ?>:</strong> <?php echo MyDate::formatDate($issue['ordles_teacher_starttime']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_End_Time'); ?>:</strong> <?php echo MyDate::formatDate($issue['ordles_teacher_endtime']); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_Name'); ?>:</strong> <?php echo $issue['learner_full_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_Join_Time'); ?>:</strong> <?php echo MyDate::formatDate($issue['ordles_student_starttime']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_end_Time'); ?>:</strong> <?php echo MyDate::formatDate($issue['ordles_student_endtime']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_ENDED_BY'); ?>:</strong>
                        <?php if (!empty($issue['ordles_ended_by'])) {
                            echo ($issue['ordles_ended_by'] == User::TEACHER)  ? $issue['teacher_full_name']: $issue['learner_full_name'];
                        } else {
                            echo Label::getLabel('LBL_N/A');
                        }; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>