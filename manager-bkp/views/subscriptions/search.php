<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'ordsub_id' => Label::getLabel('LBL_SUB_ID'),
    'order_id' => Label::getLabel('LBL_ORDER_ID'),
    'ordsub_startdate' => Label::getLabel('LBL_START_DATE'),
    'ordsub_enddate' => Label::getLabel('LBL_END_DATE'),
    'learner_name' => Label::getLabel('LBL_LEARNER'),
    'teacher_name' => Label::getLabel('LBL_TEACHER'),
    'order_discount_value' => Label::getLabel('LBL_DISCOUNT'),
    'order_net_amount' => Label::getLabel('LBL_NET_TOTAL'),
    'order_payment_status' => Label::getLabel('LBL_PAYMENT'),
    'order_addedon' => Label::getLabel('LBL_DATETIME'),
    'ordsub_status' => Label::getLabel('LBL_STATUS')
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$statuses = Subscription::getStatuses();
$paymentMethod[null] = Label::getLabel('LBL_N/A');
foreach ($subscriptions as $row) {
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'order_id':
                $td->appendElement('plaintext', [], Order::formatOrderId(FatUtility::int($row[$key])), true);
                break;
            case 'ordsub_startdate':
            case 'ordsub_enddate':
                $td->appendElement('plaintext', [], date('Y-m-d', strtotime($row[$key])), true);
                break;
            case 'learner_name':
                $td->appendElement('plaintext', [], $row['learner_first_name'] . ' ' . $row['learner_last_name'], true);
                break;
            case 'teacher_name':
                $td->appendElement('plaintext', [], $row['teacher_first_name'] . ' ' . $row['teacher_last_name'], true);
                break;
            case 'order_type':
                $td->appendElement('plaintext', [], Order::getTypeArr($row[$key]), true);
                break;
            case 'order_net_amount':
            case 'ordles_discount':
            case 'order_discount_value':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row[$key]), true);
                break;
            case 'order_payment_status':
                $td->appendElement('plaintext', [], Order::getPaymentArr($row[$key]), true);
                break;
            case 'ordsub_status':
                $status = $statuses[$row[$key]];
                if ($row[$key] == Subscription::ACTIVE && strtotime($row['ordsub_enddate']) < $row['ordsub_currenttime_unix']) {
                    $status = Label::getLabel('LBL_EXPIRED');
                }
                $td->appendElement('plaintext', [], $status, true);
                break;
            case 'order_pmethod_id':
                $td->appendElement('plaintext', [], $paymentMethod[$row[$key]] ?? Label::getLabel('N/A'), true);
                break;
            case 'order_addedon':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]), true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($subscriptions) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSubsSearchPaging']);
$pagingArr = ['pageCount' => ceil($recordCount / $post['pagesize']), 'pageSize' => $post['pagesize'], 'page' => $post['pageno'], 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
