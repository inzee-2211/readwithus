<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_SRNO'),
    'currency_code' => Label::getLabel('LBL_Currency'),
    'currency_symbol_left' => Label::getLabel('LBL_Symbol_Left'),
    'currency_symbol_right' => Label::getLabel('LBL_Symbol_Right'),
    'currency_active' => Label::getLabel('LBL_Status'),
];
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
} else {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table--hovered table-responsive', 'id' => 'currencyList']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr', []);
    $tr->setAttribute("id", $row['currency_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['currency_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'currency_active':
                $active = "active";
                $statucAct = '';
                $strTxt = Label::getLabel('LBL_Active');
                if ($row['currency_active'] == AppConstant::YES) {
                    $active = 'active';
                    $statucAct = 'inactiveStatus(this)';
                }
                if ($row['currency_active'] == AppConstant::NO) {
                    $strTxt = Label::getLabel('LBL_Inactive');
                    $active = 'inactive';
                    $statucAct = 'activeStatus(this)';
                }
                $disabledClass = "";
                if ($canEdit == false || $row['currency_is_default'] == AppConstant::YES) {
                    $disabledClass = "disabled-switch";
                    $statucAct = "";
                }
                $str = '<label id="' . $row['currency_id'] . '" class="statustab ' . $active . ' ' . $disabledClass . '" onclick="' . $statucAct . '">
					<span data-off="' . Label::getLabel('LBL_Active') . '" data-on="' . Label::getLabel('LBL_Inactive') . '" class="switch-labels status_' . $row['currency_id'] . '"></span>
					<span class="switch-handles"></span>
					</label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'currency_code':
                if ($row['currency_name'] != '') {
                    $td->appendElement('plaintext', [], $row['currency_name'], true);
                    $td->appendElement('br', []);
                    $td->appendElement('plaintext', [], '(' . $row[$key] . ')', true);
                } else {
                    $td->appendElement('plaintext', [], $row[$key], true);
                }
                if ($row['currency_is_default'] == AppConstant::YES) {
                    $td->appendElement('br', []);
                    $td->appendElement('plaintext', [], '<small>[' . Label::getLabel('LBL_THIS_IS_YOUR_DEFAULT_CURRENCY') . ']</small>', true);
                }

                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "editCurrencyForm(" . $row['currency_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                }
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
?>
<script>
    $(document).ready(function () {
        $('#currencyList').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('CurrencyManagement', 'updateOrder'), order, function (res) { });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>