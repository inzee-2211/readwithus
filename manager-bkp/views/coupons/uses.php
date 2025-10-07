<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_COUPON_HISTORY'); ?> (<?php echo $coupon['coupon_code']; ?>)</h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class=" sectionbody space">
                    <div class="border-box border-box--space">
                        <?php
                        $arr_flds = [
                            'order_id' => Label::getLabel('LBL_Order_Id'),
                            'user_username' => Label::getLabel('LBL_Customer'),
                            'order_total_amount' => Label::getLabel('LBL_Amount'),
                            'order_addedon' => Label::getLabel('LBL_Date'),
                        ];
                        $tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
                        $th = $tbl->appendElement('thead')->appendElement('tr');
                        foreach ($arr_flds as $key => $val) {
                            $e = $th->appendElement('th', [], $val, true);
                        }
                        $sr_no = 0;
                        foreach ($records as $sn => $row) {
                            $sr_no++;
                            if (!empty($row['couhis_released'])) {
                                $tr = $tbl->appendElement('tr', ['class' => 'label-danger']);
                            } else {
                                $tr = $tbl->appendElement('tr');
                            }
                            foreach ($arr_flds as $key => $val) {
                                $td = $tr->appendElement('td');
                                switch ($key) {
                                    case 'order_id':
                                        $td->appendElement('plaintext', [], Order::formatOrderId(FatUtility::int($row[$key])), true);
                                        break;
                                    case 'user_username':
                                        $td->appendElement('plaintext', [], $row['user_first_name'] . ' ' . $row['user_last_name'], true);
                                        break;
                                    case 'order_total_amount':
                                        $td->appendElement('plaintext', [], MyUtility::formatMoney($row[$key]), true);
                                        break;
                                    case 'couhis_added_on':
                                    case 'order_addedon':
                                        $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]), true);
                                        break;
                                    default:
                                        $td->appendElement('plaintext', [], $row[$key], true);
                                        break;
                                }
                            }
                        }
                        if (count($records) == 0) {
                            $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
                        }
                        echo $tbl->getHtml();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>