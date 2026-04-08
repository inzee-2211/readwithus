<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_SRNO'),
    'biblecontent_title' => Label::getLabel('LBL_TITLE'),
    'biblecontent_url' => Label::getLabel('LBLVIDEO_LINK'),
    'biblecontent_active' => Label::getLabel('LBL_Status'),
    'action' => Label::getLabel('LBL_ACTION'),
];
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
    unset($arr_flds['action']);
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive', 'id' => 'bibleList']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $inActive = empty($row['biblecontent_active']) ? 'inactive' : '';
    $tr = $tbl->appendElement('tr', ['class' => $inActive]);
    $tr->setAttribute("id", $row['biblecontent_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['biblecontent_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'biblecontent_title':
                $heading = substr($row[$key], 0, 30);
                $td->appendElement('plaintext', [], $heading);
                break;
            case 'biblecontent_active':
                $active = "";
                if ($row['biblecontent_active']) {
                    $active = 'active';
                }
                $statucAct = ($canEdit === true) ? 'toggleStatus(this)' : '';
                $str = '<label id="' . $row['biblecontent_id'] . '" class="statustab ' . $active . '" onclick="' . $statucAct . '">
                      <span data-off="Inactive" data-on="Active" class="switch-labels"></span>
                      <span class="switch-handles"></span>
                    </label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                if ($canEdit) {
                    $ul = $td->appendElement("ul", ["class" => "actions"]);
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'Edit', "onclick" => "addForm(" . $row['biblecontent_id'] . ")"], '<i class="ion-edit icon"></i>', true);
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => 'Delete', "onclick" => "deleteRecord(" . $row['biblecontent_id'] . ")"], '<i class="ion-android-delete icon"></i>', true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], 'No records found');
}
echo $tbl->getHtml();
?>
<script>
    $(document).ready(function () {
        $('#bibleList').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('BibleContent', 'updateOrder'), order, function (res) { });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>