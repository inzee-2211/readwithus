<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    'title' => Label::getLabel('LBL_Title'),
    'prislab_min' => Label::getLabel('LBL_Min_Slabs'),
    'prislab_max' => Label::getLabel('LBL_Max_Slabs'),
    'prislab_active' => Label::getLabel('LBL_Status'),
];
if ($canEdit) {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = ($post['pageno'] == 1) ? 0 : $post['pagesize'] * ($post['pageno'] - 1);
$activeLabel = Label::getLabel('LBL_ACTIVE');
$inactiveLabel = Label::getLabel('LBL_INACTIVE');
foreach ($records as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    $tr->setAttribute("id", $row['prislab_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'title':
                $title = Label::getLabel('LBL_{min}_to_{max}_Lesson(s)'); //5 to 9 hrs
                $title = str_replace(['{min}', '{max}'], [$row['prislab_min'], $row['prislab_max']], $title);
                $td->appendElement('plaintext', [], $title);
                break;
            case 'prislab_active':
                $active = "";
                $statusAct = 'activeStatus(this)';
                if ($row['prislab_active'] == AppConstant::ACTIVE) {
                    $active = 'active';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['prislab_active'] == AppConstant::INACTIVE) {
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = '';
                if ($canEdit === false) {
                    $statusClass = "disabled";
                    $statusAct = '';
                }
                $str = '<label id="' . $row['prislab_id'] . '" class="statustab status_' . $row['prislab_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
                        <span data-off="' . $activeLabel . '" data-on="' . $inactiveLabel . '" class="switch-labels"></span>
                        <span class="switch-handles ' . $statusClass . '"></span>
                    </label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions centered"]);
                if ($canEdit) {
                    //$li = $ul->appendElement("li");
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement(
                            'a',
                            [
                                'href' => 'javascript:void(0)', 'class' => 'button small green',
                                'title' => Label::getLabel('LBL_Edit'), "onclick" => "priceSlabForm(" . $row['prislab_id'] . ");"
                            ],
                            Label::getLabel('LBL_Edit'),
                            true
                    );
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (empty($records)) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'priceSlabPagingForm']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $post['pageno'], 'pageSize' => $post['pagesize'], 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
