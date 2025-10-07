<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$arrFlds = [];
if ($canEdit) {
    $arrFlds['dragdrop'] = '';
}
$arrFlds['listserial'] = Label::getLabel('LBL_SRNO');
$arrFlds['tlang_identifier'] = Label::getLabel('LBL_LANGUAGE_IDENTIFIER');
$arrFlds['tlang_name'] = Label::getLabel('LBL_LANGUAGE_NAME');
$arrFlds['tlang_active'] = Label::getLabel('LBL_STATUS');
if ($canEdit) {
    $arrFlds['action'] = Label::getLabel('LBL_ACTION');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered table-dragable', 'id' => 'teachingLangages']);
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
    $tr->setAttribute("id", $row['tlang_id']);
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['tlang_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'tlang_active':
                $active = "";
                $statusAct = 'activeStatus(this)';
                if ($row['tlang_active'] == AppConstant::YES) {
                    $active = 'active';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['tlang_active'] == AppConstant::NO) {
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = '';
                if ($canEdit === false) {
                    $statusClass = "disabled";
                    $statusAct = '';
                }
                $str = '<label id="' . $row['tlang_id'] . '" class="statustab status_' . $row['tlang_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
                        <span data-off="' . $activeLabel . '" data-on="' . $inactiveLabel . '" class="switch-labels"></span>
                        <span class="switch-handles ' . $statusClass . '"></span>
                    </label>';

                // $active = "";
                // $changeStatus = AppConstant::YES;
                // if ($row['tlang_active'] == AppConstant::YES) {
                //     $changeStatus = AppConstant::NO;
                //     $active = 'checked';
                // }
                // $statusAct = 'changeStatus(this,' . $changeStatus . ')';
                // $statusClass = '';
                // if ($canEdit === false) {
                //     $statusClass = "disabled";
                //     $statusAct = '';
                // }
                // $str = '<label class="statustab -txt-uppercase">                 
                //      <input ' . $active . ' type="checkbox" id="switch' . $row['tlang_id'] . '" value="' . $row['tlang_id'] . '" onclick="' . $statusAct . '" class="switch-labels status_' . $row['tlang_id'] . '"/>
                //     <i class="switch-handles ' . $statusClass . '"></i></label>';
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
                    $innerLiEdit->appendElement(
                            'a',
                            [
                                'href' => 'javascript:void(0)', 'class' => 'button small green',
                                'title' => Label::getLabel('LBL_EDIT'), "onclick" => "form(" . $row['tlang_id'] . ")"
                            ],
                            Label::getLabel('LBL_EDIT'),
                            true
                    );
                    $innerLiDelete = $innerUl->appendElement("li");
                    $innerLiDelete->appendElement(
                            'a',
                            [
                                'href' => 'javascript:void(0)', 'class' => 'button small green',
                                'title' => Label::getLabel('LBL_DELETE'), "onclick" => "deleteRecord(" . $row['tlang_id'] . ")"
                            ],
                            Label::getLabel('LBL_DELETE'),
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
if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
?>
<script>
    $(document).ready(function () {
        $('#teachingLangages').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('TeachLanguage', 'updateOrder'), order, function (res) { });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>