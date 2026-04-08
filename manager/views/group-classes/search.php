<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    'grpcls_title' => Label::getLabel('LBL_Class_Title'),
    'grpcls_type' => Label::getLabel('LBL_Type'),
    'teacher_name' => Label::getLabel('LBL_Teacher'),
    'grpcls_total_seats' => Label::getLabel('LBL_MAX_LEARNERS'),
    'grpcls_entry_fee' => Label::getLabel('LBL_Entry_Fee'),
    'grpcls_start_datetime' => Label::getLabel('LBL_START_TIME'),
    'grpcls_end_datetime' => Label::getLabel('LBL_END_TIME'),
    'grpcls_added_on' => Label::getLabel('LBL_CREATED'),
    'grpcls_status' => Label::getLabel('LBL_STATUS'),
    'action' => Label::getLabel('LBL_Action'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table--hovered table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($classes as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'teacher_name':
                $td->appendElement('plaintext', [], $row['user_first_name'] . ' ' . $row['user_last_name']);
                break;
            case 'grpcls_type':
                $td->appendElement('plaintext', [], GroupClass::getClassTypes($row[$key]));
                break;
            case 'grpcls_entry_fee':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row[$key]), true);
                break;
            case 'grpcls_added_on':
            case 'grpcls_start_datetime':
            case 'grpcls_end_datetime':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]));
                break;
            case 'grpcls_status':
                $td->appendElement('plaintext', [], GroupClass::getStatuses($row[$key]), true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green'], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', ['href' => 'javascript:;', 'onclick' => 'viewLearners(' . $row['grpcls_id'] . ');', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View_Learners')], Label::getLabel('LBL_Learners'), true);
                if ($row['grpcls_type'] == GroupClass::TYPE_PACKAGE) {
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => MyUtility::makeUrl('PackageClasses') . '?grpcls_parent=' . $row['grpcls_id'], 'class' => 'button small green', 'title' => Label::getLabel('LBL_CLasses')], Label::getLabel('LBL_CLasses'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], html_entity_decode($row[$key]));
                break;
        }
    }
}
if (count($classes) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmSearchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
