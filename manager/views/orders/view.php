<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
switch ($order['order_type']) {
    case Order::TYPE_LESSON:
        $oderTypeLabel = 'LBL_LESSONS_DETAILS';
        break;
    case Order::TYPE_SUBSCR:
        $oderTypeLabel = 'LBL_SUBSCRIPTION_DETAILS';
        break;
    case Order::TYPE_GCLASS:
        $oderTypeLabel = 'LBL_GROUP_CLASS_DETAILS';
        break;
    case Order::TYPE_COURSE:
        $oderTypeLabel = 'LBL_COURSE_DETAILS';
        break;
    case Order::TYPE_PACKGE:
        $oderTypeLabel = 'LBL_PACKAGE_CLASS_DETAILS';
        break;
    case Order::TYPE_WALLET:
        $oderTypeLabel = 'LBL_WALLET_DETAILS';
        break;
    case Order::TYPE_GFTCRD:
        $oderTypeLabel = 'LBL_GIFT_CARD_DETAILS';
        break;
}
$payins[0] = Label::getLabel('LBL_N/A');
?>
<script>
    var order_id = '<?php echo $order["order_id"] ?>';
</script>
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_ORDER_DETAIL'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <?php
                        $ul = new HtmlElement("ul", ["class" => "actions actions--centered"]);
                        $li = $ul->appendElement("li", ['class' => 'droplink']);
                        $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT')], '<i class="ion-android-more-horizontal icon"></i>', true);
                        $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                        $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                        $innerLi = $innerUl->appendElement('li');
                        $innerLi->appendElement('a', ['href' => MyUtility::makeUrl('Orders'), 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_BACK_TO_ORDER')], Label::getLabel('LBL_BACK_TO_ORDER'), true);
                        echo $ul->getHtml();
                        ?>
                        <h4><?php echo Label::getLabel('LBL_CUSTOMER_ORDER_DETAIL'); ?></h4>
                    </div>
                    <div class="sectionbody">
                        <table class="table table--details">
                            <tr>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER/INVOICE_ID'); ?>:</strong> <?php echo Order::formatOrderId($order["order_id"]); ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_DATE'); ?>: </strong> <?php echo MyDate::formatDate($order['order_addedon']); ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_PAYMENT_STATUS'); ?>:</strong> <?php echo Order::getPaymentArr($order['order_payment_status']); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_TOTAL_AMOUNT'); ?>: </strong> <?php echo MyUtility::formatMoney($order["order_total_amount"]); ?> </td>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_NET_AMOUNT'); ?>: </strong> <?php echo MyUtility::formatMoney($order["order_net_amount"]); ?> </td>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_DISCOUNT'); ?>: </strong> <?php echo MyUtility::formatMoney($order["order_discount_value"]); ?> </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_AMOUNT_PAID'); ?>: </strong><?php echo MyUtility::formatMoney($totalPaidAmount); ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_AMOUNT_PENDING'); ?>: </strong><?php echo MyUtility::formatMoney($pendingAmount); ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_STATUS'); ?>: </strong><?php echo Order::getStatusArr($order["order_status"]); ?></td>
                            </tr>
                        </table>
                    </div>
                </section>
                <div class="row row--cols-group">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <section class="section">
                            <div class="sectionhead">
                                <h4><?php echo Label::getLabel('LBL_USER_DETAILS'); ?></h4>
                            </div>
                            <div class="row space">
                                <div class="addresas-group">
                                    <p>
                                        <strong><?php echo Label::getLabel('LBL_NAME'); ?> : </strong><?php echo $order['learner_full_name']; ?><br>
                                        <strong><?php echo Label::getLabel('LBL_EMAIL'); ?> : </strong><?php echo $order['learner_email']; ?><br>
                                        <strong><?php echo Label::getLabel('LBL_USER_ID'); ?> : </strong><?php echo $order['order_user_id']; ?><br>
                                        <strong><?php echo Label::getLabel('LBL_USER_TIMEZONE'); ?> : </strong><?php echo MyDate::formatTimeZoneLabel($order['user_timezone']); ?><br>
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <section class="section">
                            <div class="sectionhead">
                                <h4><?php echo Label::getLabel('LBL_ORDER_DETAILS'); ?></h4>
                            </div>
                            <div class="row space">
                                <div class="addresas-group ">
                                    <p>
                                        <strong><?php echo Label::getLabel('LBL_ORDER_TYPE'); ?> : </strong><?php echo Order::getTypeArr($order['order_type']); ?><br>
                                        <strong><?php echo Label::getLabel('LBL_ORDER/INVOICE_ID'); ?> : </strong><?php echo Order::formatOrderId($order["order_id"]); ?><br>
                                        <strong><?php echo Label::getLabel('LBL_ORDER_AMOUNT_PAID'); ?> : </strong> <?php echo MyUtility::formatMoney($totalPaidAmount); ?><br>
                                        <strong><?php echo Label::getLabel('LBL_ORDER_DATE'); ?> : </strong> <?php echo MyDate::formatDate($order['order_addedon']); ?><br>
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <section class="section">
                            <div class="sectionhead">
                                <h4><?php echo Label::getLabel($oderTypeLabel); ?></h4>
                            </div>
                            <div class="row space">
                                <div class="addresas-group ">
                                    <p>
                                        <?php
                                        switch ($order['order_type']) {
                                            case Order::TYPE_LESSON:
                                            case Order::TYPE_SUBSCR:
                                                ?>
                                                <?php if ($order['order_type'] == Order::TYPE_SUBSCR) { ?>
                                                    <strong><?php echo Label::getLabel('LBL_SUBSCRIPTION_START_DATE'); ?> : </strong><?php echo MyDate::formatDate($childeOrderDetails['ordsub_startdate']); ?><br>
                                                    <strong><?php echo Label::getLabel('LBL_SUBSCRIPTION_END_DATE'); ?> : </strong><?php echo MyDate::formatDate($childeOrderDetails['ordsub_enddate']); ?><br>
                                                <?php } ?>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_NAME'); ?> : </strong><?php echo $childeOrderDetails['user_first_name'] . ' ' . $childeOrderDetails['user_last_name']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_EMAIL'); ?> : </strong><?php echo $childeOrderDetails['user_email']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_ID'); ?> : </strong><?php echo $childeOrderDetails['ordles_teacher_id']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_TIMEZONE'); ?> : </strong><?php echo MyDate::formatTimeZoneLabel($childeOrderDetails['user_timezone']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_LESSON_TYPE'); ?> : </strong><?php echo Lesson::getTypes($childeOrderDetails['ordles_type']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_NO._OF_LESSONS'); ?> : </strong><?php echo $childeOrderDetails['order_item_count']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_LESSON_DURATION'); ?> : </strong><?php echo $childeOrderDetails['ordles_duration'] . ' ' . Label::getLabel('LBL_MINS') . '/' . Label::getLabel('LBL_PER_LESSON'); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_LESSON_PRICE'); ?> : </strong><?php echo MyUtility::formatMoney($childeOrderDetails['ordles_amount']) . '/' . Label::getLabel('LBL_PER_LESSON'); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_ADMIN_COMMISSION_(%)'); ?> : </strong><?php echo $childeOrderDetails['ordles_commission'] . '%'; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACH_LANGUAGE'); ?> : </strong><?php echo $childeOrderDetails['tlang_name']; ?><br>
                                                <strong><a href="<?php echo MyUtility::makeUrl('Lessons', 'index') . '?order_id=' . $order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_LESSON_ORDER'); ?></a></strong><br>
                                                <?php if ($order['order_type'] == Order::TYPE_SUBSCR) { ?>
                                                    <strong><a href="<?php echo MyUtility::makeUrl('Subscriptions') . '?order_id=' . $order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_SUBSCRIPTION_ORDER'); ?></a></strong><br>
                                                <?php } ?>
                                                <?php
                                                break;
                                            case Order::TYPE_GCLASS:
                                                ?>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_NAME'); ?> : </strong><?php echo $childeOrderDetails['user_first_name'] . ' ' . $childeOrderDetails['user_last_name']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_EMAIL'); ?> : </strong><?php echo $childeOrderDetails['user_email']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_ID'); ?> : </strong><?php echo $childeOrderDetails['grpcls_teacher_id']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_TIMEZONE'); ?> : </strong><?php echo MyDate::formatTimeZoneLabel($childeOrderDetails['user_timezone']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_CLASS_NAME'); ?> : </strong><?php echo $childeOrderDetails['grpcls_title']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_START_DATE_TIME'); ?> : </strong><?php echo MyDate::formatDate($childeOrderDetails['grpcls_start_datetime']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_END_DATE_TIME'); ?> : </strong> <?php echo MyDate::formatDate($childeOrderDetails['grpcls_end_datetime']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TOTAL_SEATS'); ?> : </strong><?php echo $childeOrderDetails['grpcls_total_seats']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_CLASS_PRICE'); ?> : </strong><?php echo MyUtility::formatMoney($childeOrderDetails['ordcls_amount']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_ADMIN_COMMISSION_(%)'); ?> : </strong><?php echo $childeOrderDetails['ordcls_commission'] . '%'; ?><br>
                                                <strong><a href="<?php echo MyUtility::makeUrl('Classes', 'index') . '?order_id=' . $order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_CLASS_ORDER'); ?></a></strong><br>
                                                <?php
                                                break;
                                            case Order::TYPE_PACKGE:
                                                ?>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_NAME'); ?> : </strong><?php echo $childeOrderDetails['user_first_name'] . ' ' . $childeOrderDetails['user_last_name']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_EMAIL'); ?> : </strong><?php echo $childeOrderDetails['user_email']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_ID'); ?> : </strong><?php echo $childeOrderDetails['grpcls_teacher_id']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_TIMEZONE'); ?> : </strong><?php echo MyDate::formatTimeZoneLabel($childeOrderDetails['user_timezone']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_PACKAGE_NAME'); ?> : </strong><?php echo $childeOrderDetails['package_title']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_START_DATE_TIME'); ?> : </strong><?php echo MyDate::formatDate($childeOrderDetails['package_start']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_END_DATE_TIME'); ?> : </strong> <?php echo MyDate::formatDate($childeOrderDetails['package_end']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TOTAL_SEATS'); ?> : </strong><?php echo $childeOrderDetails['grpcls_total_seats']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_PACKAGE_PRICE'); ?> : </strong><?php echo MyUtility::formatMoney($childeOrderDetails['ordpkg_amount']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_ADMIN_COMMISSION_(%)'); ?> : </strong><?php echo $childeOrderDetails['ordcls_commission'] . '%/' . Label::getLabel('LBL_PER_CLASS'); ?><br>
                                                <strong><a href="<?php echo MyUtility::makeUrl('Packages') . '?order_id=' . $order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_PACKAGES_ORDER'); ?></a></strong><br>
                                                <strong><a href="<?php echo MyUtility::makeUrl('Classes', 'index') . '?order_id=' . $order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_CLASS_ORDER'); ?></a></strong><br>
                                                <?php
                                                break;
                                            case Order::TYPE_WALLET:
                                                ?>
                                                <strong><?php echo Label::getLabel('LBL_AMOUNT_ADDED'); ?> : </strong><?php echo MyUtility::formatMoney($order['order_net_amount']); ?><br>

                                                <?php if (!empty($order['order_related_order_id'])) { ?>
                                                    <strong><?php echo Label::getLabel('LBL_RELATED_ORDER'); ?> : </strong><a target="_blank" href="<?php echo MyUtility::makeUrl('Orders', 'view', [$order['order_related_order_id']]); ?>"><?php echo Label::getLabel('LBL_VIEW') . ' ' . Order::formatOrderId($order['order_related_order_id']); ?> </a><br>
                                                    <?php
                                                }
                                                break;
                                            case Order::TYPE_GFTCRD:
                                                ?>
                                                <strong><?php echo Label::getLabel('LBL_GIFTCARD_CODE'); ?> : </strong><?php echo $childeOrderDetails['ordgift_code']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_RECIPIENT_NAME'); ?> : </strong><?php echo $childeOrderDetails['ordgift_receiver_name']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_RECIPIENT_EMAIL'); ?> : </strong><?php echo $childeOrderDetails['ordgift_receiver_email']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_GIFTCARD_STATUS'); ?> : </strong><?php echo Giftcard::getStatuses($childeOrderDetails['ordgift_status']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_AMOUNT'); ?> : </strong><?php echo MyUtility::formatMoney($order['order_net_amount']); ?><br>
                                                <strong><a href="<?php echo MyUtility::makeUrl('Giftcards', 'index') . '?order_id=' . $order['order_id']; ?>"><?php echo Label::getLabel('LBL_VIEW_GIFTCARD_ORDER'); ?></a></strong><br>
                                                <?php
                                                break;
                                            case Order::TYPE_COURSE:
                                                ?>
                                                <strong><?php echo Label::getLabel('LBL_COURSE_TITLE'); ?> : </strong><?php echo $childeOrderDetails['course_title']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_NAME'); ?> : </strong><?php echo ucwords($childeOrderDetails['user_first_name']. ' ' .$childeOrderDetails['user_last_name']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_TEACHER_EMAIL'); ?> : </strong><?php echo $childeOrderDetails['user_email']; ?><br>
                                                <strong><?php echo Label::getLabel('LBL_AMOUNT'); ?> : </strong><?php echo MyUtility::formatMoney($order['ordcrs_amount']); ?><br>
                                                <strong><?php echo Label::getLabel('LBL_ADMIN_COMMISSION_(%)'); ?> : </strong><?php echo $childeOrderDetails['ordcrs_commission'] . '%'; ?><br>
                                                <?php
                                                break;
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_ORDER_PAYMENT_HISTORY'); ?></h4>
                    </div>
                    <div class="sectionbody">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th width="15%"><?php echo Label::getLabel('LBL_DATE_ADDED'); ?></th>
                                    <th width="15%"><?php echo Label::getLabel('LBL_TXN_ID'); ?></th>
                                    <th width="15%"><?php echo Label::getLabel('LBL_PAYMENT_METHOD'); ?></th>
                                    <th width="15%"><?php echo Label::getLabel('LBL_AMOUNT'); ?></th>
                                    <th width="40%"><?php echo Label::getLabel('LBL_GATEWAY_RESPONSE'); ?></th>
                                </tr>
                                <?php if (!empty($bankTransfers)) { ?>
                                    <?php foreach ($bankTransfers as $row) { ?>
                                        <tr>
                                            <td><?php echo MyDate::formatDate($row['bnktras_datetime']); ?></td>
                                            <td><?php echo $row['bnktras_txn_id']; ?></td>
                                            <td>
                                                <?php echo Label::getLabel('LBL_' . $payins[$bankTransferPay['pmethod_id']]); ?>
                                                <?php if ($row['bnktras_status'] == BankTransferPay::PENDING) { ?>
                                                    <div>
                                                        <a href="javascript:updateStatus('<?php echo $row['bnktras_id']; ?>','<?php echo BankTransferPay::APPROVED; ?>')"><?php echo Label::getLabel('LBL_APPROVE'); ?></a> |
                                                        <a href="javascript:updateStatus('<?php echo $row['bnktras_id']; ?>','<?php echo BankTransferPay::DECLINED; ?>')"><?php echo Label::getLabel('LBL_DECLINE'); ?></a>
                                                    </div>
                                                <?php } else { ?>
                                                    (<?php echo BankTransferPay::getStatuses($row['bnktras_status']); ?>)<br />
                                                <?php } ?>
                                                <?php if (FatUtility::int($row['file_id']) > 0) { ?>
                                                    <a href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_ORDER_PAY_RECEIPT, $row['file_record_id']]); ?>"><?php echo Label::getLabel('LBL_VIEW_PAYMENT_RECEIPT'); ?></a>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo MyUtility::formatMoney($row['bnktras_amount']); ?></td>
                                            <td>
                                                <div class="break-me collapse-text"><?php echo nl2br($row['bnktras_response']); ?></div>
                                                <a class="collapse-btn" href="javascript:void(0)"><?php echo Label::getLabel('LBL_SHOW_MORE'); ?></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                <?php foreach ($order['orderPayments'] as $row) { ?>
                                    <tr>
                                        <td><?php echo MyDate::formatDate($row['ordpay_datetime']); ?></td>
                                        <td><?php echo $row['ordpay_txn_id']; ?></td>
                                        <td><?php echo Label::getLabel('LBL_' . $payins[$row['ordpay_pmethod_id']]); ?></td>
                                        <td><?php echo MyUtility::formatMoney($row['ordpay_amount']); ?></td>
                                        <td>
                                            <div class="break-me collapse-text" style="overflow: auto;"><?php echo $row['ordpay_response']; ?></div>
                                            <a class="collapse-btn" href="javascript:void(0)"><?php echo Label::getLabel('LBL_SHOW_MORE'); ?></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                <?php if ($canEdit && $order['order_payment_status'] == Order::UNPAID && $order['order_status'] == Order::STATUS_INPROCESS) { ?>
                    <section class="section">
                        <div class="sectionhead">
                            <h4><?php echo Label::getLabel('LBL_ORDER_PAYMENTS'); ?></h4>
                        </div>
                        <div class="sectionbody space">
                            <?php
                            $form->setFormTagAttribute('onsubmit', 'updatePayment(this); return(false);');
                            $form->setFormTagAttribute('class', 'web_form');
                            $form->developerTags['colClassPrefix'] = 'col-md-';
                            $form->developerTags['fld_default_col'] = 4;
                            $paymentFld = $form->getField('ordpay_response');
                            $paymentFld->developerTags['col'] = 12;
                            echo $form->getFormHtml();
                            ?>
                        </div>
                    </section>
                <?php } ?>
            </div>
        </div>
    </div>
</div>