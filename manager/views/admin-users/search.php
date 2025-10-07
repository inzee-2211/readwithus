<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    'admin_name' => Label::getLabel('LBL_FULL_NAME'),
    'admin_username' => Label::getLabel('LBL_USERNAME'),
    'admin_email' => Label::getLabel('LBL_EMAIL'),
    'admin_active' => Label::getLabel('LBL_STATUS'),
];
if ($canEdit || $canViewAdminPermissions) {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'action':
                if ($row['admin_id'] == $adminLoggedInId || $row['admin_id'] == 1) {
                    break;
                }
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                if ($canEdit) {
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "editForm(" . $row['admin_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Change_Password'), "onclick" => "changePasswordForm(" . $row['admin_id'] . ")"], Label::getLabel('LBL_Change_Password'), true);
                }

                if ($row['admin_id'] > 1 && $row['admin_id'] != $adminLoggedInId && $canViewAdminPermissions) {
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => MyUtility::makeUrl('AdminUsers', 'permissions', [$row['admin_id']]), 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_Permissions')], Label::getLabel('LBL_Permissions'), true);
                }
                break;
            case 'admin_active':
                if ($row['admin_id'] > 1 && $row['admin_id'] != $adminLoggedInId) {
                    $active = "active";
                    $statucAct = '';
                    if ($row['admin_active'] == AppConstant::YES) {
                        $active = 'active';
                        if ($canEdit) {
                            $statucAct = 'inactiveStatus(this)';
                        }
                    }
                    if ($row['admin_active'] == AppConstant::NO) {
                        $active = '';
                        if ($canEdit) {
                            $statucAct = 'activeStatus(this)';
                        }
                    }
                    $str = '<label id="' . $row['admin_id'] . '" class="statustab ' . $active . ' status_' . $row['admin_id'] . '" onclick="' . $statucAct . '">
                                <span data-off="' . Label::getLabel('LBL_Active') . '" data-on="' . Label::getLabel('LBL_Inactive') . '" class="switch-labels "></span>
                                <span class="switch-handles"></span>
                            </label>';
                    $td->appendElement('plaintext', [], $str, true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
