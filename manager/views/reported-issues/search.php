<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    'repiss_record_type' => Label::getLabel('LBL_TYPE'),
    'repiss_record_id' => Label::getLabel('LBL_CLASS/LESSON_ID'),
    'order_id' => Label::getLabel('LBL_ORDER_ID'),
    'repiss_title' => Label::getLabel('LBL_Issue'),
    'repiss_reported_by' => Label::getLabel('LBL_Reported_By'),
    'repiss_reported_on' => Label::getLabel('LBL_Reported_On'),
    'repiss_status' => Label::getLabel('LBL_Status'),
    'action' => Label::getLabel('LBL_Action'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = $post['page'] == 1 ? 0 : $post['pageSize'] * ($post['page'] - 1);
$classType = AppConstant::getClassTypes();
foreach ($records as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr', []);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'repiss_record_type':
                $td->appendElement('plaintext', [], $classType[$row['repiss_record_type']]);
                break;
            case 'repiss_reported_by':
                $td->appendElement('plaintext', [], $row['learner_first_name'] . ' ' . $row['learner_last_name']);
                break;
            case 'order_id':
                $orderId = ($row['repiss_record_type'] == AppConstant::GCLASS) ? $row['ordcls_order_id'] : $row['ordles_order_id'];
                $td->appendElement('plaintext', [], Order::formatOrderId(FatUtility::int($orderId)));
                break;
            case 'repiss_status':
                $td->appendElement('plaintext', [], Issue::getStatusArr($row[$key]), true);
                break;
            case 'repiss_reported_on':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]), true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $viewLi = $innerUl->appendElement('li');
                $viewLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View'), "onclick" => "view(" . $row['repiss_id'] . ")"], Label::getLabel('LBL_View'), true);
                if ($canEdit && $row['repiss_status'] == Issue::STATUS_ESCALATED) {
                    $actionLi = $innerUl->appendElement("li");
                    $actionLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Action'), "onclick" => "actionForm(" . $row['repiss_id'] . ")"], Label::getLabel('LBL_Action'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key] ?? 'NA', true);
                break;
        }
    }
}
if (count($records) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmUserSearchPaging']);
$this->includeTemplate('_partial/pagination.php', ['pageCount' => ceil($recordCount / $post['pageSize']), 'page' => $post['page'], 'pageSize' => $post['pageSize'], 'recordCount' => $recordCount], false);
