<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
if ($canEdit) {
    $arrFlds = ['dragdrop' => ''];
}
$arrFlds['listserial'] = Label::getLabel('LBL_SRNO');
$arrFlds['prefer_identifier'] = Label::getLabel('LBL_PREFERENCE_IDENTIFIER');
$arrFlds['prefer_title'] = Label::getLabel('LBL_PREFERENCE_TITLE');
if ($canEdit) {
    $arrFlds['action'] = Label::getLabel('LBL_ACTION');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'preferences']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = 0;
foreach ($arrListing as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr');
    $tr->setAttribute("id", $row['prefer_id']);
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                $td->setAttribute("class", 'dragHandle');
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions centered"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT'), "onclick" => "preferenceForm(" . $row['prefer_id'] . ")"], Label::getLabel('LBL_EDIT'), true);
                    $innerLiDelete = $innerUl->appendElement("li");
                    $innerLiDelete->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_DELETE'), "onclick" => "deleteRecord(" . $row['prefer_id'] . ")"], Label::getLabel('LBL_DELETE'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
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
        $('#preferences').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('Preferences', 'updateOrder'), order, function (res) {});
            },
            dragHandle: ".dragHandle",
        });
    });
</script>