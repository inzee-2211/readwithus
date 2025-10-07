<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_SrNo'),
    'user_full_name' => Label::getLabel('LBL_Full_Name'),
    'user_email' => Label::getLabel('LBL_Email'),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table--hovered table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$sr_no = 0;
foreach ($learners as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'user_full_name':
                $td->appendElement('plaintext', [], $row['user_first_name'] . ' ' . $row['user_last_name'], true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key]);
                break;
        }
    }
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_STUDENTS'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <div class="row">
                        <?php
                        if (count($learners) == 0) {
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