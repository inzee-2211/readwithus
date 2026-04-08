<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SRNO'),
    'post_title' => Label::getLabel('LBL_Post_Title'),
    'categories' => Label::getLabel('LBL_Category'),
    'post_published_on' => Label::getLabel('LBL_Published_Date'),
    'post_published' => Label::getLabel('LBL_Post_Status'),
];
if ($canEdit) {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'post']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if ($row['post_published'] == 1) {
        $tr->setAttribute("id", $row['post_id']);
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'post_published_on':
                $td->appendElement('plaintext', [], MyDate::formatDate($row['post_published_on']));
                break;
            case 'post_added_on':
                $td->appendElement('plaintext', [], MyDate::formatDate($row['post_added_on']));
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'post_title':
                if ($row['post_title'] != '') {
                    $td->appendElement('plaintext', [], $row['post_title'], true);
                    $td->appendElement('br', []);
                    $td->appendElement('plaintext', [], '(' . $row['post_identifier'] . ')', true);
                } else {
                    $td->appendElement('plaintext', [], $row[$key], true);
                }
                break;
            case 'post_published':
                $td->appendElement('plaintext', [], BlogPost::getStatuses($row[$key]), true);
                break;
            case 'child_count':
                if ($row[$key] == 0) {
                    $td->appendElement('plaintext', [], $row[$key], true);
                } else {
                    $td->appendElement('a', ['href' => MyUtility::makeUrl('BlogPostCategories', 'index', [$row['post_id']]), 'title' => Label::getLabel('LBL_View_Categories')], $row[$key]);
                }
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "addBlogPostForm(" . $row['post_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                    $innerLiDelete = $innerUl->appendElement('li');
                    $innerLiDelete->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteRecord(" . $row['post_id'] . ")"], Label::getLabel('LBL_Delete'), true);
                }
                break;
            case 'categories':
                $td->appendElement('plaintext', [], implode(", ", explode(",", $row['categories'])), true);
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
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmSearchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
