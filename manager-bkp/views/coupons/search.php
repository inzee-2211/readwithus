<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'srno' => Label::getLabel('LBL_SRNO'),
    'coupon_title' => Label::getLabel('LBL_Title'),
    'coupon_code' => Label::getLabel('LBL_Code'),
    'coupon_discount' => Label::getLabel('LBL_Discount'),
    'coupon_datetime' => Label::getLabel('LBL_Available'),
    'coupon_active' => Label::getLabel('LBL_Status'),
    'action' => Label::getLabel('LBL_Action')
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        if (date('Y-m-d H:i:s') > $row['coupon_end_date']) {
            $td->setAttribute('class', 'c-red');
        }
        switch ($key) {
            case 'srno':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'coupon_discount':
                $discountValue = '0.0';
                if ($row['coupon_discount_type'] == AppConstant::PERCENTAGE) {
                    $discountValue = MyUtility::formatPercent($row['coupon_discount_value']);
                } elseif ($row['coupon_discount_type'] == AppConstant::FLAT_VALUE) {
                    $discountValue = MyUtility::formatMoney($row['coupon_discount_value']);
                }
                $td->appendElement('plaintext', [], $discountValue, true);
                break;
            case 'coupon_datetime':
                $dispDate = MyDate::formatDate($row['coupon_start_date'], 'Y-m-d') . ' - ' . MyDate::formatDate($row['coupon_end_date'], 'Y-m-d');
                $td->appendElement('plaintext', [], $dispDate, true);
                break;
            case 'coupon_active':
                $td->appendElement('plaintext', [], AppConstant::getActiveArr($row[$key]));
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                if ($canEdit) {
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "form(" . $row['coupon_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "remove(" . $row['coupon_id'] . ")"], Label::getLabel('LBL_Delete'), true);
                }
                $innerLiHistory = $innerUl->appendElement('li');
                $innerLiHistory->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_History'), "onclick" => "couponHistory(" . $row['coupon_id'] . ")"], Label::getLabel('LBL_History'), true);
                break;
            case 'coupon_title':
                $td->appendElement('plaintext', [], $row[$key], true);
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
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmCouponSearchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
<script >
    var DISCOUNT_IN_PERCENTAGE = '<?php echo AppConstant::PERCENTAGE; ?>';
    var DISCOUNT_IN_FLAT_VALUE = '<?php echo AppConstant::FLAT_VALUE; ?>';
    function callCouponDiscountIn(val) {
        if (val == DISCOUNT_IN_PERCENTAGE) {
            $("#coupon_max_discount_value_div").show();
        }
        if (val == DISCOUNT_IN_FLAT_VALUE) {
            $("#coupon_max_discount_value_div").hide();
        }
    }
</script>
