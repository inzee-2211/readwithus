<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'qid' => Label::getLabel('LBL_ID'),
    'level_name' => Label::getLabel('LBL_LEVEL'),
    'subject' => Label::getLabel('LBL_SUBJECT'),
    'topic' => Label::getLabel('LBL_TOPIC'),
    'tier_name' => Label::getLabel('LBL_TIER'),
    'type_name' => Label::getLabel('LBL_TYPE'),
    'year_name' => Label::getLabel('LBL_YEAR'),
    'examboard_name' => Label::getLabel('LBL_EXAMBOARDS'),
    'action' => Label::getLabel('LBL_ACTION'),
];
?>

<div class="buttons-group" style="padding: 4px;align-content: right;text-align: right;">
    <a href="javascript:void(0);" onclick="categoryForm();" class="btn-primary">Add New</a>
</div>
<?php

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
                $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'onclick' => 'view("'.$row['qid'].'")', 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW')], Label::getLabel('LBL_VIEW'), true);
               
               
                $innerLiEdit = $innerUl->appendElement('li');
                $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'onclick' => 'deleted("' . $row['qid'] . '")', 'class' => 'button small green', 'title' => Label::getLabel('LBL_DELETE')], Label::getLabel('LBL_DELETE'), true);
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
