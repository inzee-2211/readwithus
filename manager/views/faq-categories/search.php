<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [];
if ($canEdit) {
    $arr_flds['dragdrop'] = '';
}
$arr_flds['listserial'] = Label::getLabel('LBL_SRNO');
$arr_flds['faqcat_identifier'] = Label::getLabel('LBL_category_Name');
$arr_flds['faqcat_active'] = Label::getLabel('LBL_Status');

if ($canEdit) {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive', 'id' => 'faqcat']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $key => $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if ($row['faqcat_active'] == AppConstant::ACTIVE) {
        $tr->setAttribute("id", $row['faqcat_id']);
    }
    if ($row['faqcat_active'] != AppConstant::ACTIVE) {
        $tr->setAttribute("class", "nodrag nodrop");
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['faqcat_active'] == AppConstant::ACTIVE) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'select_all':
                $td->appendElement('plaintext', [], '<label class="checkbox"><input class="selectItem--js" type="checkbox" name="faqcat_ids[]" value=' . $row['faqcat_id'] . '><i class="input-helper"></i></label>', true);
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'faqcat_identifier':
                if ($row['faqcat_name'] != '') {
                    $td->appendElement('plaintext', [], $row['faqcat_name'], true);
                    $td->appendElement('br', []);
                    $td->appendElement('plaintext', [], '(' . $row[$key] . ')', true);
                } else {
                    $td->appendElement('plaintext', [], $row[$key], true);
                }
                break;
            case 'faqcat_active':
                $active = "";
                if ($row['faqcat_active']) {
                    $active = 'checked';
                }
                $statusAct = ($canEdit === true) ? 'toggleStatus(event,this,' . AppConstant::YES . ')' : 'toggleStatus(event,this,' . AppConstant::NO . ')';
                $statusClass = ($canEdit === false) ? 'disabled' : '';
                $str = '<label class="statustab -txt-uppercase">
                     <input ' . $active . ' type="checkbox" id="switch' . $row['faqcat_id'] . '" value="' . $row['faqcat_id'] . '" onclick="' . $statusAct . '" class="switch-labels"/>
                    <i class="switch-handles ' . $statusClass . '"></i></label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $innerLiEdit = $innerUl->appendElement('li');
                if ($canEdit) {
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "addFaqCatForm(" . $row['faqcat_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                }
                if ($canEdit) {
                    $innerLiDelete = $innerUl->appendElement('li');
                    $innerLiDelete->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteRecord(" . $row['faqcat_id'] . ")"], Label::getLabel('LBL_Delete'), true);
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
$frm = new Form('frmFaqCatListing', ['id' => 'frmFaqCatListing']);
$frm->setFormTagAttribute('class', 'web_form last_td_nowrap');
$frm->setFormTagAttribute('onsubmit', 'formAction(this, reloadList ); return(false);');
$frm->setFormTagAttribute('action', MyUtility::makeUrl('FaqCategories', 'toggleBulkStatuses'));
$frm->addHiddenField('', 'status');
echo $frm->getFormTag();
echo $frm->getFieldHtml('status');
echo $tbl->getHtml();
?>
</form>
<?php echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmFaqCatSearchPaging']); ?>
<script>
    $(document).ready(function () {
        $('#faqcat').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('FaqCategories', 'updateOrder'), order, function (res) { });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>
