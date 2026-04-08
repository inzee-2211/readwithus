<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'certpl_id' => Label::getLabel('LBL_ID'),
    'certpl_name' => Label::getLabel('LBL_NAME'),
    'certpl_status' => Label::getLabel('LBL_STATUS'),
];
if ($canEdit) {
    $arr_flds['action'] = Label::getLabel('LBL_ACTION');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$paymentMethod = OrderPayment::getMethods();
foreach ($arrListing as $row) {
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'action':
                if ($canEdit) {
                    $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_OPTIONS')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);

                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => MyUtility::makeUrl('Certificates', 'form', [$row['certpl_code'], $row['certpl_lang_id']]), 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW')], Label::getLabel('LBL_Edit'), true);
                }
                break;
            case 'certpl_status':
                $active = "active";
                if ($row['certpl_status'] == AppConstant::NO) {
                    $active = 'inactive';
                }
                $str = '<label class="statustab ' . $active . '" '. (($canEdit) ? 'onclick="updateStatus(\'' . $row['certpl_code'] . '\', \''.$row['certpl_status'].'\')"':""). '>
				  <span data-off="' . Label::getLabel('LBL_Active') . '" data-on="' . Label::getLabel('LBL_Inactive') . '" class="switch-labels "></span>
				  <span class="switch-handles"></span>
				</label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}

echo $tbl->getHtml();
