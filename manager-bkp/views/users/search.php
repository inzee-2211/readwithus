<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    // 'user_image' => Label::getLabel('LBL_IMAGE'),
    'user_full_name' => Label::getLabel('LBL_NAME/ID'),
    'user_email' => Label::getLabel('LBL_EMAIL_ID'),
    'type' => Label::getLabel('LBL_TYPE'),
    'user_created' => Label::getLabel('LBL_REGISTERED'),
    'user_verified' => Label::getLabel('LBL_VERIFIED'),
    'user_active' => Label::getLabel('LBL_STATUS'),
    'action' => Label::getLabel('LBL_ACTION'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = $page == 1 ? 0 : $pageSize * ($page - 1);
$userTypeArray = User::getUserTypes();
$activeLabel = Label::getLabel('LBL_ACTIVE');
$inactiveLabel = Label::getLabel('LBL_INACTIVE');
$signUpForStr = Label::getLabel('LBL_SIGNING_UP_FOR_TEACHER');
$yesNoArr = AppConstant::getYesNoArr();
foreach ($arrListing as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr', []);
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'user_full_name':
                $td->appendElement('plaintext', [], $row[$key] . '<br/>' . Label::getLabel('LBL_USER_ID') . ': ' . $row['user_id'], true);
                break;
            case 'user_image':
                $td->appendElement('img', ['style' => 'width:40px;', 'src' => MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $row['user_id']], CONF_WEBROOT_FRONT_URL)], '', true);
                break;
            case 'user_active':
                $active = "";
                $statusAct = 'changeStatus(this,1)';
                if ($row['user_active'] == AppConstant::ACTIVE) {
                    $active = 'active';
                    $statusAct = 'changeStatus(this,0)';
                }
                $statusClass = '';
                if ($canEdit === false) {
                    $statusClass = "disabled";
                    $statusAct = '';
                }
                $str = '<label id="' . $row['user_id'] . '" class="statustab status_' . $row['user_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
				  <span data-off="' . $activeLabel . '" data-on="' . $inactiveLabel . '" class="switch-labels"></span>
				  <span class="switch-handles ' . $statusClass . '"></span>
				</label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'user_created':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]));
                break;
            case 'type':
                $str = $userTypeArray[User::LEARNER];
                if ($row['user_is_teacher']) {
                    $str .= '<br/>' . $userTypeArray[User::TEACHER];
                } elseif ($row['user_registered_as'] == User::TEACHER) {
                    $str .= '<br/><small class="label-danger">' . $signUpForStr . '</small>';
                }
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'user_verified':
                $verified = is_null($row[$key] ?? null) ? 0 : 1;
                $td->appendElement('plaintext', [], $yesNoArr[$verified], true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW'), "onclick" => "view(" . $row['user_id'] . ");"], Label::getLabel('LBL_VIEW'), true);
                $innerLi = $innerUl->appendElement('li');
                if ($canEdit) {
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT'), "onclick" => "userForm(" . $row['user_id'] . ");"], Label::getLabel('LBL_EDIT'), true);
                    $innerLi = $innerUl->appendElement("li");
                }
                $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_TRANSACTIONS'), "onclick" => "transactions(" . $row['user_id'] . ");"], Label::getLabel('LBL_TRANSACTIONS'), true);
                $innerLi = $innerUl->appendElement('li');
                if ($canEdit) {
                    $innerLi->appendElement('a', ['href' => "javascript:void(0)", 'onClick' => "changePassword(" . $row['user_id'] . ");", 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_CHANGE_PASSWORD')], Label::getLabel('LBL_CHANGE_PASSWORD'), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => "javascript:void(0)", 'onClick' => "userLogin(" . $row['user_id'] . ");", 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_LOG_INTO_STORE')], Label::getLabel('LBL_LOG_INTO_PROFILE'), true);
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
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmUserSearchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
