<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    'label_key' => Label::getLabel('LBL_Key'),
    'label_caption' => Label::getLabel('LBL_Caption')
];
if ($canEdit) {
    $arr_flds += ['action' => Label::getLabel('LBL_Action'),];
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        if ($key == 'label_key') {
            $td->setAttribute('class', 'word-break');
        }
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no, true);
                break;
            case 'label_caption':
                $td->appendElement('plaintext', [], nl2br($row[$key]), true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $innerLiEdit = $innerUl->appendElement('li');
                $innerLiEdit->appendElement('a', [
                    'href' => 'javascript:void(0)',
                    'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'),
                    "onclick" => "labelsForm('" . $row['label_id'] . "')"],
                        Label::getLabel('LBL_Edit'), true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmLabelsSrchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
