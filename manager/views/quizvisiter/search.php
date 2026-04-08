<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

// Define table headers
$arr_flds = [
    'user_name' => Label::getLabel('LBL_USER_NAME'),
    'user_email' => Label::getLabel('LBL_USER_EMAIL'),
    'parent_email' => Label::getLabel('LBL_PARENT_EMAIL'),
    'real_attempt_count' => Label::getLabel('LBL_ATTEMPTS'),
    // 'user_created_at' => Label::getLabel('LBL_CREATED_ON'),
    'action' => Label::getLabel('LBL_ACTION'),
];
?>

<div class="buttons-group" style="padding: 4px; text-align: right;">
</div>

<?php
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');

foreach ($arr_flds as $val) {
    $th->appendElement('th', [], $val);
}

// Render table rows
foreach ($arrListing as $row) {
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');

        switch ($key) {
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', [
                    'href' => 'javascript:void(0)',
                    'class' => 'button small green',
                    'title' => Label::getLabel('LBL_OPTIONS')
                ], '<i class="ion-android-more-horizontal icon"></i>', true);

                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);

                // View action (Pass user_id)
                $innerLiView = $innerUl->appendElement('li');
                $innerLiView->appendElement('a', [
                    'href' => 'javascript:void(0);',
                    'onclick' => 'view("' . $row['user_id'] . '")', // Passing user_id
                    'class' => 'button small green',
                    'title' => Label::getLabel('LBL_VIEW')
                ], Label::getLabel('LBL_VIEW'), true);

                // Delete action (Pass user_id)
                $innerLiDel = $innerUl->appendElement('li');
                $innerLiDel->appendElement('a', [
                    'href' => 'javascript:void(0);',
                    'onclick' => 'deleted("' . $row['user_id'] . '")', // Passing user_id
                    'class' => 'button small green',
                    'title' => Label::getLabel('LBL_DELETE')
                ], Label::getLabel('LBL_DELETE'), true);
                break;

            // case 'user_created_at':
            //     $td->appendElement('plaintext', [], date('d M, Y H:i', strtotime($row[$key])), true);
            //     break;

            default:
                $text = $row[$key] ?? '-';
                $td->appendElement('plaintext', [], $text, true);
                break;
        }
    }
}

// No records
if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', [
        'colspan' => count($arr_flds)
    ], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}

// Output table
echo $tbl->getHtml();

// Pagination
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmPaging']);

$pagingArr = [
    'pageCount' => ceil($recordCount / $post['pagesize']),
    'pageSize' => $post['pagesize'],
    'page' => $post['page'],
    'recordCount' => $recordCount
];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>