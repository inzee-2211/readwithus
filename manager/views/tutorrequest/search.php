<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

// Define table headers
$arr_flds = [
    // 'attempt_id'      => Label::getLabel('LBL_ID'),
    'name'       => Label::getLabel('LBL_NAME'),
    'email'      => Label::getLabel('LBL_USER_EMAIL'),
    'parent_email'      => Label::getLabel('LBL_PARENT_EMAIL'),
    'phone'    => Label::getLabel('LBL_PHONE'),
    'subject'   => Label::getLabel('LBL_SUBJECT'),
    'preferred_time' => Label::getLabel('LBL_PREFERRED_TIME'),
     
    'created_at'      => Label::getLabel('LBL_DATE'),
    'action'          => Label::getLabel('LBL_ACTION'),
];
?>

<div class="buttons-group" style="padding: 4px; text-align: right;">
    <!-- Optional: You can remove this if no 'Add' functionality -->
    <!-- <a href="javascript:void(0);" onclick="categoryForm();" class="btn-primary">Add New</a> -->
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

                // View action
                // $innerLiView = $innerUl->appendElement('li');
                // $innerLiView->appendElement('a', [
                //     'href' => 'javascript:void(0);',
                //     'onclick' => 'view("' . $row['attempt_id'] . '")',
                //     'class' => 'button small green',
                //     'title' => Label::getLabel('LBL_VIEW')
                // ], Label::getLabel('LBL_VIEW'), true);

                // Delete action
                $innerLiDel = $innerUl->appendElement('li');
                $innerLiDel->appendElement('a', [
                    'href' => 'javascript:void(0);',
                    'onclick' => 'deleted("' . $row['id'] . '")',
                    'class' => 'button small green',
                    'title' => Label::getLabel('LBL_DELETE')
                ], Label::getLabel('LBL_DELETE'), true);
                break;

            default:
                $text = $row[$key] ?? '-';
                if ($key === 'created_at') {
                    $text = date('d M, Y H:i', strtotime($row[$key]));
                }
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
    'pageCount'    => ceil($recordCount / $post['pagesize']),
    'pageSize'     => $post['pagesize'],
    'page'         => $post['page'],
    'recordCount'  => $recordCount
];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
