<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [
    'listserial' => Label::getLabel('LBL_srNo'),
    'tereq_reference' => Label::getLabel('LBL_REFERENCE_NUMBER'),
    'user_full_name' => Label::getLabel('LBL_NAME'),
    'user_email' => Label::getLabel('LBL_EMAIL'),
    'tereq_comments' => Label::getLabel('LBL_COMMENTS'),
    'tereq_date' => Label::getLabel('LBL_REQUESTED_ON'),
    'status' => Label::getLabel('LBL_STATUS'),
    'action' => Label::getLabel('LBL_ACTION'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arrListing as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr');
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'user_full_name':
                $td->appendElement('plaintext', [], CommonHelper::htmlEntitiesDecode(implode(" ", [$row['tereq_first_name'], $row['tereq_last_name']])));
                break;
            case 'status':
                $td->appendElement('plaintext', [], TeacherRequest::getStatuses($row['tereq_status']), true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW'), "onclick" => "view(" . $row['tereq_id'] . ");"], Label::getLabel('LBL_VIEW'), true);
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_QUALIFICATIONS'), "onclick" => "searchQualifications(" . $row['tereq_user_id'] . ");"], Label::getLabel('LBL_QUALIFICATIONS'), true);
                if ($canEdit && empty($row['user_deleted']) && $row['tereq_status'] == TeacherRequest::STATUS_PENDING) {
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_CHANGE_STATUS'), "onclick" => "changeStatusForm(" . $row['tereq_id'] . ");"], Label::getLabel('LBL_CHANGE_STATUS'), true);
                }
                /* ] */
                break;
            case 'tereq_date':
                $td->appendElement('plaintext', [], MyDate::formatDate($row['tereq_date']));
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
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmSearchPaging']);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
