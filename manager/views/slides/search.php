<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_SRNO'),
    'slide_identifier' => Label::getLabel('LBL_Title'),
    'slide_url' => Label::getLabel('LBL_URL'),
    'slide_active' => Label::getLabel('LBL_Status'),
];
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
} else {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'slideList']);
$th = $tbl->appendElement('thead')->appendElement('tr');
$activeLabel = Label::getLabel('LBL_ACTIVE');
$inactiveLabel = Label::getLabel('LBL_INACTIVE');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
foreach ($arrListing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr', []);
    $tr->setAttribute("id", $row['slide_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['slide_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'slide_identifier':
                $td->appendElement('plaintext', [], $row['slide_identifier'], true);
                break;
            case 'slide_url':
                $url = CommonHelper::processURLString($row['slide_url']);
                $td->appendElement('plaintext', [], CommonHelper::truncateCharacters($url, 85), true);
                break;
            case 'slide_active':
                $active = "";
                $statusAct = 'activeStatus(this)';
                if ($row['slide_active'] == AppConstant::YES) {
                    $active = 'active';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['slide_active'] == AppConstant::NO) {
                    $active = 'unchecked';
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = '';
                if ($canEdit === false) {
                    $statusClass = "disabled";
                    $statusAct = '';
                }
                $str = '<label id="' . $row['slide_id'] . '" class="statustab status_' . $row['slide_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
                        <span data-off="' . $activeLabel . '" data-on="' . $inactiveLabel . '" class="switch-labels"></span>
                        <span class="switch-handles ' . $statusClass . '"></span>
                    </label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "addSlideForm(" . $row['slide_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                    $innerLiDelete = $innerUl->appendElement('li');
                    $innerLiDelete->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteRecord(" . $row['slide_id'] . ")"], Label::getLabel('LBL_Delete'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
?>
<script>
    $(document).ready(function() {
        $('#slideList').tableDnD({
            onDrop: function(table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('Slides', 'updateOrder'), order, function(res) {});
            },
            dragHandle: ".dragHandle",
        });
    });
</script>