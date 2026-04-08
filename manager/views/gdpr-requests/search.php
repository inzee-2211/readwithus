<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'gdpreq_id' => Label::getLabel('LBL_REQ_ID'),
    'gdpreq_user_name' => Label::getLabel('LBL_USER_NAME'),
    'user_email' => Label::getLabel('LBL_USER_EMAIL'),
    'gdpreq_reason' => Label::getLabel('LBL_REASON'),
    'gdpreq_added_on' => Label::getLabel('LBL_REQUESTED_ON'),
    'gdpreq_updated_on' => Label::getLabel('LBL_UPDATED_ON'),
    'gdpreq_status' => Label::getLabel('LBL_STATUS'),
];
if ($canEdit) {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table--hovered table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $th->appendElement('th', [], $val);
}
foreach ($gdprRequests as $sn => $row) {
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'gdpreq_reason':
                $td->appendElement('plaintext', [], CommonHelper::truncateCharacters($row['gdpreq_reason'], 50));
                break;
            case 'gdpreq_user_name':
                $td->appendElement('plaintext', [], implode(" ", [$row['user_first_name'], $row['user_last_name']]));
                break;
            case 'gdpreq_added_on':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]));
                break;
            case 'gdpreq_updated_on':
                $text = MyDate::formatDate($row[$key]);
                if ($row['gdpreq_status'] == GdprRequest::STATUS_PENDING) {
                    $text = Label::getLabel('LBL_N/A');
                }
                $td->appendElement('plaintext', [], $text, true);
                break;
            case 'gdpreq_status':
                $td->appendElement('plaintext', [], $gdprStatus[$row[$key]] ?? '', true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "view(" . $row['gdpreq_id'] . ")"], '<i class="ion-eye icon"></i>', true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key]);
                break;
        }
    }
}
if (count($gdprRequests) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmSearchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
