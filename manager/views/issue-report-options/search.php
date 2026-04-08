<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [];
if ($canEdit) {
    $arrFlds['dragdrop'] = '';
}
$arrFlds['listserial'] = Label::getLabel('LBL_SRNO');
$arrFlds['tissueoptlang_title'] = Label::getLabel('LBL_TITLE');
$arrFlds['tissueopt_identifier'] = Label::getLabel('LBL_IDENTIFIER');
$arrFlds['tissueopt_active'] = Label::getLabel('LBL_STATUS');
if ($canEdit) {
    $arrFlds['action'] = Label::getLabel('LBL_ACTION');
}

$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered table-dragable', 'id' => 'IssueReportOptions']);
$th = $tbl->appendElement('thead')->appendElement('tr');
$activeLabel = Label::getLabel('LBL_ACTIVE');
$inactiveLabel = Label::getLabel('LBL_INACTIVE');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = 0;
foreach ($arrListing as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr');
    $tr->setAttribute("id", $row['tissueopt_id']);
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['tissueopt_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'tissueopt_active':
                $active = "";
                $statusAct = 'activeStatus(this)';
                if ($row['tissueopt_active'] == AppConstant::YES) {
                    $active = 'active';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['tissueopt_active'] == AppConstant::NO) {
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = '';
                if ($canEdit === false) {
                    $statusClass = "disabled";
                    $statusAct = '';
                }
                $str = '<label id="' . $row['tissueopt_id'] . '" class="statustab status_' . $row['tissueopt_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
                        <span data-off="' . $activeLabel . '" data-on="' . $inactiveLabel . '" class="switch-labels"></span>
                        <span class="switch-handles ' . $statusClass . '"></span>
                    </label>';

                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions centered"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green',
                        'title' => Label::getLabel('LBL_EDIT'), "onclick" => "form(" . $row['tissueopt_id'] . ")"
                            ], Label::getLabel('LBL_EDIT'), true);
                    $innerLiDelete = $innerUl->appendElement("li");
                    $innerLiDelete->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green',
                        'title' => Label::getLabel('LBL_DELETE'), "onclick" => "deleteRecord(" . $row['tissueopt_id'] . ")"
                            ], Label::getLabel('LBL_DELETE'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key] ?? Label::getLabel('LBL_NA'), true);
                break;
        }
    }
}
if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
?>
<script>
    $(document).ready(function () {
        $('#IssueReportOptions').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('IssueReportOptions', 'updateOrder'), order, function (res) { });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>