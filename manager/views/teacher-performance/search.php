<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [
    'srno' => Label::getLabel('LBL_SRNO'),
    'teacher_name' => Label::getLabel('LBL_TEACHER'),
    'testat_lessons' => Label::getLabel('LBL_LESSONS'),
    'testat_classes' => Label::getLabel('LBL_CLASSES'),
    'testat_courses' => Label::getLabel('LBL_COURSES'),
    'testat_students' => Label::getLabel('LBL_STUDENTS'),
    'testat_reviewes' => Label::getLabel('LBL_REVIEWES'),
    'testat_ratings' => Label::getLabel('LBL_RATINGS')
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srno = $page == 1 ? 0 : $postedData['pagesize'] * ($page - 1);
foreach ($records as $sn => $row) {
    $srno++;
    $tr = $tbl->appendElement('tr');
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'srno':
                $td->appendElement('plaintext', [], $srno);
                break;
            case 'testat_ratings':
                $rating = '<ul class="rating list-inline">';
                for ($j = 1; $j <= 5; $j++) {
                    $class = ($j <= round($row[$key])) ? "active" : "in-active";
                    $fillColor = ($j <= round($row[$key])) ? "#ff3a59" : "#474747";
                    $rating .= '<li class="' . $class . '">
                                    <svg xml:space="preserve" enable-background="new 0 0 70 70" viewBox="0 0 70 70" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
                                    <g><path d="M51,42l5.6,24.6L35,53.6l-21.6,13L19,42L0,25.4l25.1-2.2L35,0l9.9,23.2L70,25.4L51,42z M51,42" fill="' . $fillColor . '" /></g></svg>
				  </li>';
                }
                $rating .= '</ul>';
                $td->appendElement('plaintext', [], $rating, true);
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($records) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmTeacherPerformancePaging']);
$this->includeTemplate('_partial/pagination.php', ['pageCount' => $pageCount, 'recordCount' => $recordCount, 'page' => $page], false);
