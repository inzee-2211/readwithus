<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'order_id' => Label::getLabel('LBL_ORDER_ID'),
    'learner_full_name' => Label::getLabel('LBL_USER_NAME'),
    'order_type' => Label::getLabel('LBL_ORDER_TYPE'),
    'order_item_count' => Label::getLabel('LBL_ITEMS'),
    'order_total_amount' => Label::getLabel('LBL_TOTAL'),
    'order_discount_value' => Label::getLabel('LBL_DISCOUNT'),
    'order_net_amount' => Label::getLabel('LBL_NET_TOTAL'),
    'order_payment_status' => Label::getLabel('LBL_PAYMENT'),
    'order_status' => Label::getLabel('LBL_STATUS'),
    'order_pmethod_id' => Label::getLabel('LBL_PAY_METHOD'),
    'order_addedon' => Label::getLabel('LBL_DATETIME'),
    'action' => Label::getLabel('LBL_ACTION'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$paymentMethod = OrderPayment::getMethods();
foreach ($orders as $row) {
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_OPTIONS')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $innerLiEdit = $innerUl->appendElement('li');
                $innerLiEdit->appendElement('a', ['href' => MyUtility::makeUrl('Orders', 'View', [$row['order_id']]), 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW')], Label::getLabel('LBL_VIEW'), true);
                if ($canEdit && $row['order_payment_status'] == Order::UNPAID && $row['order_status'] != Order::STATUS_CANCELLED) {
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'class' => 'button small green', 'title' => Label::getLabel('LBL_CANCEL_ORDER'), "onclick" => "cancelOrder(" . $row['order_id'] . ")"], Label::getLabel('LBL_CANCEL_ORDER'), true);
                }
                break;
            case 'order_id':
                $td->appendElement('plaintext', [], Order::formatOrderId($row[$key]), true);
                break;
            case 'order_type':
                $td->appendElement('plaintext', [], Order::getTypeArr($row[$key]), true);
                break;
            case 'order_discount_value':
            case 'order_total_amount':
            case 'order_net_amount':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row[$key]), true);
                break;
            case 'order_payment_status':
                $td->appendElement('plaintext', [], Order::getPaymentArr($row[$key]), true);
                break;
            case 'order_status':
                $td->appendElement('plaintext', [], Order::getStatusArr($row[$key]), true);
                break;
            case 'order_pmethod_id':
                $td->appendElement('plaintext', [], $paymentMethod[$row[$key]] ?? Label::getLabel('LBL_N/A'), true);
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
if (count($orders) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmOrderSearchPaging']);
$pagingArr = ['pageCount' => ceil($recordCount / $post['pagesize']), 'pageSize' => $post['pagesize'], 'page' => $post['pageno'], 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
