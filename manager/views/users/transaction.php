<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_USER_TRANSACTIONS'); ?></h4>
    </div>
    <div class="tabs_nav_container responsive flat">
        <ul class="tabs_nav">
            <li><a class="active" href="javascript:void(0);"><?php echo Label::getLabel('LBL_TRANSACTIONS'); ?></a></li>
            <?php if ($canEdit) { ?>
                <li><a href="javascript:void(0);" onclick="transactionForm(<?php echo $userId ?>);"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a></li>
            <?php } ?>
        </ul>
        <div class="tabs_panel_wrap">
            <div class="tabs_panel">
                <?php
                $arrFlds = [
                    'usrtxn_id' => Label::getLabel('LBL_TRANSACTION_ID'),
                    'usrtxn_datetime' => Label::getLabel('LBL_DATE'),
                    'usrtxn_amount' => Label::getLabel('LBL_CREDIT'),
                    'usrtxn_comment' => Label::getLabel('LBL_DESCRIPTION')
                ];
                $tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive fixed-layout']);
                $th = $tbl->appendElement('thead')->appendElement('tr');
                foreach ($arrFlds as $key => $val) {
                    $e = $th->appendElement('th', [], $val, true);
                }
                $srNo = 0;
                foreach ($arrListing as $sn => $row) {
                    $srNo++;
                    $tr = $tbl->appendElement('tr');
                    foreach ($arrFlds as $key => $val) {
                        $td = $tr->appendElement('td');
                        switch ($key) {
                            case 'usrtxn_id':
                                $td->appendElement('plaintext', [], Transaction::formatTxnId($row[$key]));
                                break;
                            case 'usrtxn_datetime':
                                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]));
                                break;
                            case 'usrtxn_amount':
                                $td->appendElement('plaintext', [], MyUtility::formatMoney($row[$key]), true);
                                break;
                            case 'usrtxn_comment':
                                $td->appendElement('plaintext', [], $row[$key], true);
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
                echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'transactionPaging']);
                $pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'callBackJsFunc' => 'goToTransactionPage'];
                $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
                ?>
            </div>
        </div>
    </div>
</section>
<script>
    var userId = '<?php echo $userId; ?>';
</script>