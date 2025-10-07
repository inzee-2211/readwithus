<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_TEACHER_REQUEST_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tablewrap">
            <div id="listing">
                <?php
                $arrFlds = [
                    'listserial' => Label::getLabel('LBL_srNo'),
                    'uqualification_experience_type' => Label::getLabel('LBL_TYPE'),
                    'uqualification_title' => Label::getLabel('LBL_TITLE'),
                    'certificate_file' => Label::getLabel('LBL_UPLOADED_CERTIFICATE'),
                    'uqualification_description' => Label::getLabel('LBL_DESCRIPTION'),
                    'uqualification_institute_name' => Label::getLabel('LBL_INSTITUTE'),
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
                            case 'uqualification_experience_type':
                                $td->appendElement('plaintext', [], UserQualification::getExperienceTypeArr()[$row['uqualification_experience_type']] . '<br/>' . $row['uqualification_start_year'] . '-' . $row['uqualification_end_year'], true);
                                break;
                            case 'certificate_file':
                                $td->appendElement('span', ['class' => 'td__caption -hide-desktop -show-mobile'], Label::getLabel('LBL_CERTIFICATE'));
                                $span = $td->appendElement('span', ['class' => 'td__data'], '');
                                if (!empty($row['file_id'])) {
                                    $a = $span->appendElement('a', ["target" => "_blank", 'href' => MyUtility::makeFullUrl('Image', 'download', [Afile::TYPE_USER_QUALIFICATION_FILE, $row['uqualification_id']])], '');
                                    $divInsideSpan = $a->appendElement('div', ['class' => 'attachment-file'], '');
                                    $spanInside_DivInsideSpan = $divInsideSpan->appendElement('div', ['class' => 'inline-icon -display-inline -color-fill']);
                                    $svgSpan = $spanInside_DivInsideSpan->appendElement('span', ['class' => 'svg-icon']);
                                    $svgSpan->appendElement('plaintext', [], '<img src="' . CONF_WEBROOT_BACKEND . 'images/attach.svg" class="">', true);
                                    $divInsideSpan->appendElement('plaintext', [], $row['file_name']);
                                }
                                break;
                            case 'uqualification_institute_name':
                                $td->appendElement('plaintext', [], $row['uqualification_institute_name'] . '<br/>' . $row['uqualification_institute_address'], true);
                                break;
                            case 'uqualification_description':
                                $td->appendElement('plaintext', [], nl2br($row['uqualification_description']), true);
                                break;
                            default:
                                $td->appendElement('plaintext', [], $row[$key], true);
                                break;
                        }
                    }
                }
                if (count($arrListing) == 0) {
                    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_No_Records_Found'));
                }
                echo $tbl->getHtml();
                ?>
            </div>
        </div>
    </div>
</section>