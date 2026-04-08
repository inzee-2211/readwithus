<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$flds = array(
    'listserial' => Label::getLabel('LBL_SR_NO.'),
    'taecherName' => Label::getLabel('LBL_TEACHER_NAME'),
    'learnerName' => Label::getLabel('LBL_LEARNER_NAME'),
    'order_id' => Label::getLabel('LBL_ORDER_DETAILS'),
    'prevTimings' => Label::getLabel('LBL_PREV_TIMINGS'),
    'sesslog_prev_status' => Label::getLabel('LBL_PREV_STATUS'),
    'sesslog_changed_status' => Label::getLabel('LBL_ACTION_PERFORMED'),
    'sesslog_created' => Label::getLabel('LBL_ADDED_ON'),
    'sesslog_comment' => Label::getLabel('LBL_REASON')
);
if(SessionLog::LESSON_CANCELLED_LOG == $post['reportType']){
    unset($flds['prevTimings']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$srNo = 0;
$statusArr = Lesson::getStatuses();
foreach ($logs as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr');
    foreach ($flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->setAttribute('width', '2%');
                $td->appendElement('plaintext', array(), $srNo);
                break;
            case 'taecherName':
                $td->setAttribute('width', '15%');
                $td->appendElement('span', [], $row['teacher_first_name'] . ' ' . $row['teacher_last_name'], true);
                break;
            case 'learnerName':
                $td->appendElement('span', [], $row['learner_first_name'] . ' ' . $row['learner_last_name'], true);
                break;
            case 'order_id':
                $td->setAttribute('width', '15%');
                $td->appendElement('plaintext', array(), Label::getLabel('LBL_O-ID') . ': ' . Order::formatOrderId(FatUtility::int($row['order_id'])) . '<br> ' . Label::getLabel('LBL_LESSON_ID') . ': ' . $row['ordles_id'], true);
                break;
            case 'prevTimings':
                $td->setAttribute('width', '20%');
                $timings = Label::getLabel('LBL_N/A');
                if (!empty($row['sesslog_prev_starttime']) && !empty($row['sesslog_prev_endtime'])) {
                    $timings = Label::getLabel('LBL_ST') . ': ' . MyDate::formatDate($row['sesslog_prev_starttime']) . '<br> ' . Label::getLabel('LBL_ET') . ': ' . MyDate::formatDate($row['sesslog_prev_endtime']);
                }
                $td->appendElement('plaintext', array(), $timings, true);
                break;
            case 'sesslog_prev_status':
                $td->appendElement('plaintext', array(), $statusArr[$row['sesslog_prev_status']], true);
                break;
            case 'sesslog_changed_status':
                $td->appendElement('plaintext', array(), $statusArr[$row['sesslog_changed_status']], true);
                break;
            case 'sesslog_created':
                $td->appendElement('plaintext', array(), MyDate::formatDate($row['sesslog_created']), true);
                break;
            case 'sesslog_comment':
                $td->appendElement('span', array('title' => nl2br($row[$key])), CommonHelper::truncateCharacters($row['sesslog_comment'], 20, '', '...', true), true);
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key]);
                break;
        }
    }
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo $logTypeLabel . ' - <span class="label--info">' . $user['user_first_name'] . ' ' . $user['user_last_name'] . '</span>'; ?></h4>
        <a onClick="exportReport(<?php echo $post['user_id']; ?>, <?php echo $post['reportType']; ?>)" class='btn btn-primary export-btn btn-sm'><?php echo Label::getLabel('LBL_EXPORT_CSV') ?></a>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <div class="row table-responsive">
                        <?php
                        if (empty($logs)) {
                            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($flds)), Label::getLabel('LBL_NO_RECORDS_FOUND'));
                        }
                        echo $tbl->getHtml();
                        echo FatUtility::createHiddenFormFromData($post, array('name' => 'viewLogPaging'));
                        $pagingArr = array('callBackJsFunc' => 'goToViewNextPage', 'pageCount' => $pageCount, 'page' => $post['pageno'], 'recordCount' => $recordCount);
                        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>