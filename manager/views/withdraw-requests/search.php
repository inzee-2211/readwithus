<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [
    'listserial' => Label::getLabel('LBL_ID'),
    'user_details' => Label::getLabel('LBL_USER'),
    'withdrawal_transaction_fee' => Label::getLabel('LBL_TXN_FEE'),
    'withdrawal_amount' => Label::getLabel('LBL_AMOUNT'),
    'account_details' => Label::getLabel('LBL_ACCOUNT'),
    'withdrawal_request_date' => Label::getLabel('LBL_DATE'),
    'withdrawal_status' => Label::getLabel('LBL_STATUS'),
];
if ($canEdit) {
    $arrFlds['action'] = Label::getLabel('LBL_ACTION');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table--hovered table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$labelN = Label::getLabel('LBL_N');
$labelE = Label::getLabel('LBL_E');
$labelBankName = Label::getLabel('LBL_BANK_NAME');
$labelAcName = Label::getLabel('LBL_AC_NAME');
$labelAcNumber = Label::getLabel('LBL_AC_NUMBER');
$labelIfsc = Label::getLabel('LBL_IFSC/SWIFT_CODE');
$labelBankAddress = Label::getLabel('LBL_BANK_ADDRESS');
$labelPaypalEmail = Label::getLabel('LBL_PAYPAL_EMAIL');
$srNo = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arrListing as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr');
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], WithdrawRequest::formatRequestNumber($row["withdrawal_id"]));
                break;
            case 'user_details':
                $txt = $row["user_first_name"] . ' ' . $row["user_last_name"] . '<br><small>(' . $row["user_email"] . ')</small>';
                $td->appendElement('plaintext', [], $txt, true);
                break;
            case 'user_balance':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row['user_balance']), true);
                break;
            case 'withdrawal_transaction_fee':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row['withdrawal_transaction_fee']), true);
                break;
            case 'withdrawal_amount':
                $td->appendElement('plaintext', [], MyUtility::formatMoney($row['withdrawal_amount']), true);
                break;
            case 'account_details':
                $txt = '';
                switch ($row["pmethod_code"]) {
                    case BankPayout::KEY:
                        $txt .= '<strong>' . $labelBankName . ': </strong>' . $row["withdrawal_bank"] . ' ';
                        $txt .= '<strong>' . $labelAcName . ': </strong>' . $row["withdrawal_account_holder_name"] . '<br>';
                        $txt .= '<strong>' . $labelAcNumber . ': </strong>' . $row["withdrawal_account_number"] . ' ';
                        $txt .= '<strong>' . $labelIfsc . ': </strong>' . $row["withdrawal_ifc_swift_code"] . '<br>';
                        $txt .= '<strong>' . $labelBankAddress . ': </strong>' . $row["withdrawal_bank_address"] . '<br>';
                        break;
                    case PaypalPayout::KEY:
                        $txt .= '<strong>' . $labelPaypalEmail . ': </strong>' . $row["withdrawal_paypal_email_id"] . '<br>';
                        break;
                }
                $txt .= '<strong>' . Label::getLabel('LBL_COMMENTS') . ': </strong>' . $row["withdrawal_comments"];
                $td->appendElement('plaintext', [], $txt, true);
                break;
            case 'withdrawal_request_date':
                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]), true);
                break;
            case 'withdrawal_status':
                $td->appendElement('plaintext', [], $statusArr[$row['withdrawal_status']], true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                if ($canEdit && empty($row['user_deleted']) && $row['withdrawal_status'] == WithdrawRequest::STATUS_PENDING) {
                    $li = $ul->appendElement("li", ['class' => 'droplink']);
                    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT')], '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                    $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_APPROVE'), "onclick" => "updateStatus(" . $row['withdrawal_id'] . "," . WithdrawRequest::STATUS_COMPLETED . " , '" . Label::getLabel('LBL_APPROVE') . "')"], Label::getLabel('LBL_APPROVE'), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_DECLINE'), "onclick" => "updateStatus(" . $row['withdrawal_id'] . "," . WithdrawRequest::STATUS_DECLINED . " ,'" . Label::getLabel('LBL_DECLINE') . "')"], Label::getLabel('LBL_DECLINE'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (empty($arrListing)) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmReqSearchPaging']);
$pagingArr = ['pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
