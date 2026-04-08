<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$techOrders = [Order::TYPE_LESSON, Order::TYPE_SUBSCR, Order::TYPE_GCLASS, Order::TYPE_PACKGE];
$subOrder = current($subOrders);
?>
<td class="-no-padding" colspan="11">
    <div class="close margin-left-auto -hide-desktop" onclick="view('<?php echo $order['order_id']; ?>')"></div>
    <div class="target-data__group">
        <div class="detail-list">
            <div class="detail-list__item">
                <div class="detail-info">
                    <div class="detail-info__title detail-title">
                        <h6 class=""><?php echo Label::getLabel('LBL_ORDER_DETAIL'); ?></h6>
                    </div>
                    <div class="detail-info__listing">
                        <div class="detail-info__row"><?php echo Label::getLabel('LBL_ORDER_ID'); ?>: <?php echo Order::formatOrderId($order['order_id']); ?></div>
                        <div class="detail-info__row"><?php echo Label::getLabel('LBL_DATE'); ?>: <?php echo MyDate::formatDate($order['order_addedon']); ?></div>
                        <div class="detail-info__row"><?php echo Label::getLabel('LBL_DISCOUNT'); ?>: <?php echo MyUtility::formatMoney($order["order_discount_value"]); ?></div>
                        <div class="detail-info__row"><?php echo Label::getLabel('LBL_TOTAL'); ?>: <?php echo MyUtility::formatMoney($order['order_net_amount']); ?></div>
                    </div>
                </div>
            </div>
            <?php
            $orderTypes = array_merge($techOrders, [Order::TYPE_COURSE]);
            if (in_array($order['order_type'], $orderTypes)) { ?>
                <div class="detail-list__item">
                    <div class="detail-info">
                        <div class="detail-info__title detail-title">
                            <h6 class=""><?php echo Label::getLabel('LBL_TEACHER_DETAIL'); ?></h6>
                        </div>
                        <div class="detail-info__listing">
                            <div class="detail-info__row"><?php echo Label::getLabel('LBL_TEACHER'); ?>: <?php echo implode(" ", [$subOrder['user_first_name'], $subOrder['user_last_name']]); ?></div>
                            <div class="detail-info__row"><?php echo Label::getLabel('LBL_FROM'); ?>: <?php echo $countries[$subOrder['user_country_id']] ?? Label::getLabel('LBL_NA'); ?></div>
                            <div class="detail-info__row"><?php echo Label::getLabel('LBL_TIMEZONE'); ?>: <?php echo $subOrder['user_timezone']; ?></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="detail-list__item">
                <div class="detail-info">
                    <div class="detail-info__title detail-title">
                        <h6 class=""><?php echo Label::getLabel('LBL_ORDER_TYPE'); ?></h6>
                    </div>
                    <div class="detail-info__listing">
                        <div class="detail-info__row"><?php echo Label::getLabel('LBL_TYPE'); ?>: <?php echo Order::getTypeArr($order['order_type']); ?></div>
                        <?php
                        switch ($order['order_type']) {
                            case Order::TYPE_LESSON:
                            case Order::TYPE_SUBSCR:
                                ?>
                                <div class="detail-info__row">
                                    <?php echo is_null($subOrder['tlang_name']) ? Label::getLabel('LBL_FREE_TRAIL') : $subOrder['tlang_name']; ?>,
                                    <?php echo $subOrder['ordles_duration']; ?>
                                    <?php echo Label::getLabel('LBL_MINUTES'); ?>
                                </div>
                                <div class="detail-info__row">
                                    <?php echo Label::getLabel('LBL_QUANTITY'); ?>:
                                    <?php echo count($subOrders) . ' ' . Label::getLabel('LBL_Lesson'); ?>
                                </div>
                                <div class="detail-info__row">
                                    <?php echo Label::getLabel('LBL_PRICE'); ?>:
                                    <?php echo MyUtility::formatMoney($subOrder['ordles_amount']); ?>/
                                    <?php echo Label::getLabel('LBL_Lesson'); ?>
                                </div>
                                <?php
                                break;
                            case Order::TYPE_GCLASS:
                            case Order::TYPE_PACKGE:
                                $classTitle = ($order['order_type'] == Order::TYPE_GCLASS) ? $subOrder['grpcls_title'] : $subOrder['package_title'];
                                ?>
                                <div class="detail-info__row"><?php echo $classTitle; ?></div>
                                <div class="detail-info__row">
                                    <?php echo $subOrder['tlang_name']; ?>,
                                    <?php echo $subOrder['grpcls_duration']; ?>
                                    <?php echo Label::getLabel('LBL_MINUTES'); ?>
                                </div>
                                <div class="detail-info__row">
                                    <?php echo Label::getLabel('LBL_PRICE'); ?>:
                                    <?php echo MyUtility::formatMoney($subOrder['ordcls_amount']); ?>/
                                    <?php echo Label::getLabel('LBL_CLASS'); ?>
                                </div>
                                <?php
                                break;
                            case Order::TYPE_COURSE:
                                ?>
                                <div class="detail-info__row"><?php echo $subOrder['course_title']; ?></div>
                                <div class="detail-info__row">
                                    <?php echo $subOrder['clang_name']; ?>,
                                    <?php echo YouTube::convertDuration($subOrder['course_duration']); ?>
                                </div>
                                <div class="detail-info__row">
                                    <?php echo Label::getLabel('LBL_PRICE'); ?>:
                                    <?php echo MyUtility::formatMoney($subOrder['ordcrs_amount']); ?>/
                                    <?php echo Label::getLabel('LBL_COURSE'); ?>
                                </div>
                                <?php
                                break;
                            case Order::TYPE_WALLET:
                                ?>
                                <div class="detail-info__row"><?php echo Label::getLabel('LBL_AMOUNT_ADDED'); ?>: <?php echo MyUtility::formatMoney($order['order_net_amount']); ?></div>
                                <?php
                                break;
                            case Order::TYPE_GFTCRD:
                                ?>
                                <div class="detail-info__row"><?php echo Label::getLabel('LBL_RECIPIENT_NAME'); ?>: <?php echo $subOrder['ordgift_receiver_name']; ?></div>
                                <div class="detail-info__row"><?php echo Label::getLabel('LBL_RECIPIENT_EMAIL'); ?>: <?php echo $subOrder['ordgift_receiver_email']; ?></div>
                                <div class="detail-info__row"><?php echo Label::getLabel('LBL_GIFTCARD_STATUS'); ?>: <?php echo Giftcard::getStatuses($subOrder['ordgift_status']); ?></div>
                                <?php
                                break;
                        }
                        ?>
                        <?php if ($order['order_related_order_id'] > 0) { ?>
                            <div class="detail-info__row"><?php echo Label::getLabel('LBL_RELATED_ORDER'); ?>: <?php echo Order::formatOrderId($order['order_related_order_id']); ?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (in_array($order['order_type'], $techOrders) && count($subOrders)) { ?>
        <div class="target-data__group">
            <div class="table-panel">
                <div class="table-panel__head panel__head-trigger panel__head-trigger-js">
                    <div class="detail-title">
                        <h6><?php echo Label::getLabel('LBL_ITEMS_IN_ORDER'); ?></h6>
                    </div>
                </div>
                <div class="table-panel__body panel__body-target panel__body-target-js">
                    <table class="table table--responsive">
                        <thead>
                            <tr class="row-trigger title-row">
                                <?php if (in_array($order['order_type'], [Order::TYPE_LESSON, Order::TYPE_SUBSCR])) { ?>
                                    <th><?php echo Label::getLabel('LBL_LESSON_ID'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_ORDER_DATE'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_LESSON_STARTTIME'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_LESSON_ENDTIME'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                <?php } elseif (in_array($order['order_type'], [Order::TYPE_GCLASS, Order::TYPE_PACKGE])) { ?>
                                    <th><?php echo Label::getLabel('LBL_CLASS_ID'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_ORDER_DATE'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_CLASS_STARTTIME'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_CLASS_ENDTIME'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subOrders as $subOrder) { ?>
                                <?php if (in_array($order['order_type'], [Order::TYPE_LESSON, Order::TYPE_SUBSCR])) { ?>
                                    <tr>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_CLASS_ID'); ?></div>
                                                <div class="flex-cell__content"><?php echo $subOrder['ordles_id']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_ORDER_DATE'); ?></div>
                                                <div class="flex-cell__content"><?php echo MyDate::formatDate($order['order_addedon']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_CLASS_STARTTIME'); ?></div>
                                                <div class="flex-cell__content"><?php echo MyDate::formatDate($subOrder['ordles_lesson_starttime']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_CLASS_ENDTIME'); ?></div>
                                                <div class="flex-cell__content"><?php echo MyDate::formatDate($subOrder['ordles_lesson_endtime']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_STATUS'); ?></div>
                                                <div class="flex-cell__content"><span class="badge color-primary badge--curve"><?php echo Lesson::getStatuses($subOrder['ordles_status']) ?></span></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                                if (in_array($order['order_type'], [Order::TYPE_GCLASS, Order::TYPE_PACKGE])) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_CLASS_ID'); ?></div>
                                                <div class="flex-cell__content"><?php echo $subOrder['ordcls_id']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_ORDER_DATE'); ?></div>
                                                <div class="flex-cell__content"><?php echo MyDate::formatDate($order['order_addedon']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_CLASS_STARTTIME'); ?></div>
                                                <div class="flex-cell__content"><?php echo MyDate::formatDate($subOrder['grpcls_start_datetime']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_CLASS_ENDTIME'); ?></div>
                                                <div class="flex-cell__content"><?php echo MyDate::formatDate($subOrder['grpcls_end_datetime']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-cell">
                                                <div class="flex-cell__label"><?php echo Label::getLabel('LBL_STATUS'); ?></div>
                                                <div class="flex-cell__content"><span class="badge color-primary badge--curve"><?php echo OrderClass::getStatuses($subOrder['ordcls_status']) ?></span></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
    <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php } ?>
    <div class="target-data__group">
        <div class="table-panel">
            <div class="table-panel__head panel__head-trigger panel__head-trigger-js">
                <div class="detail-title">
                    <h6><?php echo Label::getLabel('LBL_PAYMENT_HISTORY') ?></h6>
                </div>
            </div>
            <div class="table-panel__body panel__body-target panel__body-target-js">
                <table class="table  table--responsive">
                    <thead>
                        <tr class="row-trigger title-row">
                            <th><?php echo Label::getLabel('LBL_DATE'); ?></th>
                            <th><?php echo Label::getLabel('LBL_TXN_ID'); ?></th>
                            <th><?php echo Label::getLabel('LBL_PAYMENT_METHOD'); ?></th>
                            <th><?php echo Label::getLabel('LBL_AMOUNT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($order['orderPayments'])) { ?>
    <?php foreach ($order['orderPayments'] as $payment) { ?>
                                <tr>
                                    <td>
                                        <div class="flex-cell">
                                            <div class="flex-cell__label"><?php echo Label::getLabel('LBL_DATE'); ?></div>
                                            <div class="flex-cell__content"><?php echo MyDate::formatDate($payment['ordpay_datetime']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex-cell">
                                            <div class="flex-cell__label"><?php echo Label::getLabel('LBL_TXN_ID'); ?></div>
                                            <div class="flex-cell__content">
                                                <div style="word-break: break-all;">
        <?php echo $payment['ordpay_txn_id']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex-cell">
                                            <div class="flex-cell__label"><?php echo Label::getLabel('LBL_PAYMENT_METHOD'); ?></div>
                                            <div class="flex-cell__content"><?php echo $pmethods[$payment['ordpay_pmethod_id']] ?? 'NA'; ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex-cell">
                                            <div class="flex-cell__label"><?php echo Label::getLabel('LBL_AMOUNT'); ?></div>
                                            <div class="flex-cell__content"><?php echo MyUtility::formatMoney($payment['ordpay_amount']); ?></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
<?php } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="flex-cell">
                                        <div><?php echo Label::getLabel('LBL_NO_DATA_AVAILABLE'); ?></div>
                                    </div>
                                </td>
                            </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</td>

<script>
    $(document).ready(function () {
        /* FUNCTION FOR LEFT COLLAPSEABLE LINKS */
        if ($(window).width() < 767) {
            $('.panel__head-trigger-js').click(function () {
                if ($(this).hasClass('is-active')) {
                    $(this).removeClass('is-active');
                    $(this).siblings('.panel__body-target-js').slideUp();
                    return false;
                }
                $('.panel__head-trigger-js').removeClass('is-active');
                $(this).addClass("is-active");
                $('.panel__body-target-js').slideUp();
                $(this).siblings('.panel__body-target-js').slideDown();
            });
        }
    })
</script>