<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="col-sm-12">
    <h1><?php echo Label::getLabel('LBL_COMMISSION_HISTORY'); ?></h1>
    <div class="tabs_nav_container responsive flat">
        <div class="tabs_panel_wrap">
            <div class="tabs_panel">
                <?php
                $arrFlds = [
                    'listserial' => Label::getLabel('LBL_srNo'),
                    'user_id' => Label::getLabel('LBL_USER'),
                    'comhis_lessons' => Label::getLabel('LBL_LESSON_FEES_[%]'),
                    'comhis_classes' => Label::getLabel('LBL_CLASS_FEES_[%]'),
                    'comhis_courses' => Label::getLabel('LBL_COURSE_FEES_[%]'),
                    'comhis_created' => Label::getLabel('LBL_ADDED_ON')
                ];
                $tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
                $th = $tbl->appendElement('thead')->appendElement('tr');
                foreach ($arrFlds as $val) {
                    $e = $th->appendElement('th', [], $val);
                }
                $srNo = 0;
                foreach ($arrListing as $sn => $row) {
                    $srNo++;
                    $tr = $tbl->appendElement('tr');
                    foreach ($arrFlds as $key => $val) {
                        $td = $tr->appendElement('td');
                        switch ($key) {
                            case 'listserial':
                                $td->appendElement('plaintext', [], $srNo);
                                break;
                            case 'user_id':
                                $str = "<span class='label label-success'>" . Label::getLabel('LBL_GLOBAL_COMMISSION') . "</span>";
                                if (!empty($row['user_id'])) {
                                    $str = $row['user_first_name'] . ' ' . $row['user_last_name'];
                                }
                                $td->appendElement('plaintext', [], $str, true);
                                break;
                            case 'comhis_created':
                                $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]));
                                break;
                            default:
                                $td->appendElement('plaintext', [], $row[$key]);
                                break;
                        }
                    }
                }
                if (count($arrListing) == 0) {
                    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_NO_RECORD_FOUND'));
                }
                echo $tbl->getHtml();
                ?>
            </div>
        </div>
    </div>
</div>