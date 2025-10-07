<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [
    'srno' => Label::getLabel('LBL_SRNO'),
    'language' => Label::getLabel('LBL_LANGUAGE'),
    'unscheduled' => Label::getLabel('LBL_UNSCHEDULED'),
    'scheduled' => Label::getLabel('LBL_SCHEDULED'),
    'completed' => Label::getLabel('LBL_COMPLETED'),
    'cancelled' => Label::getLabel('LBL_CANCELLED'),
    'totalsold' => Label::getLabel('LBL_TOTAL_SOLD'),
    'action' => Label::getLabel('LBL_ACTION')
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srno = $page == 1 ? 0 : $postedData['pagesize'] * ($page - 1);
foreach ($records as $sn => $row) {
    $srno++;
    $tr = $tbl->appendElement('tr');
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'srno':
                $td->appendElement('plaintext', [], $srno);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions"]);
                $li = $ul->appendElement("li");
                $li->appendElement('a', [
                    'href' => 'javascript:void(0)',
                    'class' => 'button small green',
                    'title' => Label::getLabel('LBL_View'),
                    "onclick" => "viewAll(" . $row['ordles_tlang_id'] . ")"
                        ], '<i class="ion-eye icon"></i>', true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($records) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmLessonLanguagesPaging']);
$this->includeTemplate('_partial/pagination.php', ['pageCount' => $pageCount, 'recordCount' => $recordCount, 'page' => $page], false);
