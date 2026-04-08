<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'course_id' => Label::getLabel('LBL_ID'),
    'course_title' => Label::getLabel('LBL_TITLE'),
    'teacher_name' => Label::getLabel('LBL_TEACHER'),
    'cate_name' => Label::getLabel('LBL_CATEGORY'),
    'subcate_name' => Label::getLabel('LBL_SUBCATEGORY'),
    'coapre_updated' => Label::getLabel('LBL_PUBLISHED_ON'),
    'course_active' => Label::getLabel('LBL_ACTIVE'),
    'action' => Label::getLabel('LBL_ACTION'),
];
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
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_OPTIONS')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);

                $innerLiEdit = $innerUl->appendElement('li');
                $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'onclick' => 'view("'.$row['course_id'].'")', 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW')], Label::getLabel('LBL_VIEW'), true);
                $innerLiEdit = $innerUl->appendElement('li');
                $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'onclick' => 'userLogin("' . $row['course_teacher_id'] . '", "' . $row['course_id'] . '", "preview")', 'class' => 'button small green', 'title' => Label::getLabel('LBL_PREVIEW')], Label::getLabel('LBL_PREVIEW'), true);
                /* if ($canEdit && $row['course_status'] != Course::PUBLISHED) {
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW'), 'onclick' => 'userLogin("'.$row['course_teacher_id'].'", "'.$row['course_id'].'", "edit")'], Label::getLabel('LBL_Edit'), true);
                } */
                break;
            case 'teacher_name':
                $td->appendElement('plaintext', [], $row['teacher_first_name'] . ' ' . $row['teacher_last_name'], true);
                break;
            case 'coapre_updated':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]), true);
                break;
            case 'course_active':
                $active = "active";
                if ($row['course_active'] == AppConstant::NO) {
                    $active = 'inactive';
                }
                $str = '<label class="statustab ' . $active . '" ' . (($canEdit) ? 'onclick="updateStatus(\'' . $row['course_id'] . '\', \'' . $row['course_active'] . '\')"' : "") . '>
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
if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmPaging']);
$pagingArr = ['pageCount' => ceil($recordCount / $post['pagesize']), 'pageSize' => $post['pagesize'], 'page' => $post['page'], 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
