<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'order_id' => Label::getLabel('LBL_Order_ID'),
    'user_full_name' => Label::getLabel('LBL_user_name'),
    'order_total_amount' => Label::getLabel('LBL_Total'),
    'ordgift_status' => Label::getLabel('LBL_STATUS'),
    'order_payment_status' => Label::getLabel('LBL_PAYMENT'),
    'order_pmethod_id' => Label::getLabel('LBL_PAY_METHOD'),
    'order_addedon' => Label::getLabel('LBL_DATETIME'),
    'action' => Label::getLabel('LBL_ACTION'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table--hovered table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = $post['pageno'] == 1 ? 0 : $post['pagesize'] * ($post['pageno'] - 1);
foreach ($orders as $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'buyer_user_name':
                $td->appendElement('plaintext', [], $row[$key] . '<br/>' . $row['buyer_email'], true);
                break;
            case 'order_id':
                $td->appendElement('plaintext', [], Order::formatOrderId($row[$key]));
                break;
            case 'order_total_amount':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row['order_total_amount']), true);
                break;
            case 'order_addedon':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]));
                break;
            case 'order_payment_status':
                $td->appendElement('span', [], Order::getPaymentArr($row[$key]));
                break;
            case 'order_pmethod_id':
                $td->appendElement('plaintext', [], $paymentMethod[$row[$key]] ?? Label::getLabel('N/A'), true);
                break;
            case 'ordgift_status':
                $td->appendElement('plaintext', [], Giftcard::getStatuses($row[$key]), true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions"]);
                $li = $ul->appendElement("li");
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View'), "onclick" => "viewGiftCard(" . $row['ordgift_id'] . ")"], '<i class="ion-eye icon"></i>', true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key]);
                break;
        }
    }
}
if (count($orders) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmGiftcardSearchPaging']);
$pagingArr = ['pageCount' => ceil($recordCount / $post['pagesize']), 'page' => $post['pageno'], 'pageSize' => $post['pagesize'], 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
