<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (count($logs)) { ?>
    <section class="section">
        <div class="sectionhead">
            <h4><?php echo Label::getLabel('LBL_Issue_Log'); ?></h4>
            <?php if ($issue['repiss_record_type'] == AppConstant::LESSON && $order['order_discount_value'] > 0) { ?>
                <span class="-color-primary color-secondary"><?php echo Label::getLabel('LBL_NOTE_REFUND_LESSON_DISCOUNT_ORDER_TEXT'); ?></span>
            <?php } ?>
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
<?php } ?>
<?php
if ($issue['repiss_status'] == Issue::STATUS_ESCALATED) {
    $frm->developerTags['colClassPrefix'] = 'col-md-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('id', 'actionForm');
    $frm->setFormTagAttribute('class', 'web_form');
    $frm->setFormTagAttribute('onsubmit', 'setupAction(this); return(false);');
    ?>
    <span class="gap"></span>
    <span class="gap"></span>
    <section class="section">
        <div class="sectionhead">
            <h4><?php echo Label::getLabel('LBL_ACTION_FORM'); ?></h4>
        </div>
        <div class="sectionbody">
            <?php echo $frm->getFormHtml(); ?>
        </div>
    </section>
<?php } ?>