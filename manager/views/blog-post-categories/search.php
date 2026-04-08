<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_SRNO'),
    'bpcategory_identifier' => Label::getLabel('LBL_Category_Name'),
    'child_count' => Label::getLabel('LBL_Subcategories'),
    'bpcategory_active' => Label::getLabel('LBL_Status'),
];

if (!$canEdit) {
    unset($arr_flds['dragdrop']);
} else {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'bpcategory']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
$activeLabel = Label::getLabel('LBL_ACTIVE');
$inactiveLabel = Label::getLabel('LBL_INACTIVE');
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if ($row['bpcategory_active'] == AppConstant::ACTIVE) {
        $tr->setAttribute("id", $row['bpcategory_id']);
    }
    if ($row['bpcategory_active'] != AppConstant::ACTIVE) {
        $tr->setAttribute("class", " nodrag nodrop");
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['bpcategory_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'bpcategory_identifier':
                if ($row['bpcategory_name'] != '') {
                    $td->appendElement('plaintext', [], $row['bpcategory_name'], true);
                    $td->appendElement('br', []);
                    $td->appendElement('plaintext', [], '(' . $row[$key] . ')', true);
                } else {
                    $td->appendElement('plaintext', [], $row[$key], true);
                }
                break;
            case 'child_count':
                if ($row[$key] == 0) {
                    $td->appendElement('plaintext', [], $row[$key], true);
                } else {
                    $td->appendElement('a', ['href' => MyUtility::makeUrl('BlogPostCategories', 'index', [$row['bpcategory_id']]), 'title' => Label::getLabel('LBL_View_Categories')], $row[$key]);
                }
                break;
            case 'bpcategory_active':
                $active = "";
                $statusAct = 'activeStatus(this)';
                if ($row['bpcategory_active'] == AppConstant::ACTIVE) {
                    $active = 'active';
                    $statusAct = 'inactiveStatus(this)';
                } else {
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = '';
                if ($canEdit === false) {
                    $statusClass = "disabled";
                    $statusAct = '';
                }
                $str = '<label id="' . $row['bpcategory_id'] . '" class="statustab status_' . $row['bpcategory_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
                <span data-off="' . $activeLabel . '" data-on="' . $inactiveLabel . '" class="switch-labels"></span>
                <span class="switch-handles ' . $statusClass . '"></span>
              </label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                if ($canEdit) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "addCategoryForm(" . $row['bpcategory_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                    $innerLiDelete = $innerUl->appendElement('li');
                    $innerLiDelete->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteRecord(" . $row['bpcategory_id'] . ")"], Label::getLabel('LBL_Delete'), true);
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
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmCatSearchPaging']);
?>
<script>
    $(document).ready(function() {
        var pcat_id = $('#bpcategory_parent').val();
        $('#bpcategory').tableDnD({
            onDrop: function(table, row) {
                var order = $.tableDnD.serialize('id');
                order += '&pcat_id=' + pcat_id;
                fcom.ajax(fcom.makeUrl('BlogPostCategories', 'updateOrder'), order, function(res) {});
            },
            dragHandle: ".dragHandle",
        });
    });
</script>