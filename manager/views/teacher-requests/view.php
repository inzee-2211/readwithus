<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_TEACHER_REQUEST_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="add border-box border-box--space">
            <div class="repeatedrow">
                <form class="web_form form_horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <h3><i class="ion-person icon"></i> <?php echo Label::getLabel('LBL_REQUEST_INFORMATION'); ?></h3>
                        </div>
                    </div>
                    <div class="rowbody">
                        <div class="listview">
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_REFERENCE_NUMBER'); ?></dt>
                                <dd><?php echo $row['tereq_reference']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_REQUESTED_ON'); ?></dt>
                                <dd><?php echo MyDate::formatDate($row['tereq_date']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_STATUS'); ?></dt>
                                <dd><?php echo TeacherRequest::getStatuses($row['tereq_status']); ?></dd>
                            </dl>
                            <?php if ($row['tereq_comments'] != '') { ?>
                                <dl class="list">
                                    <dt><?php echo Label::getLabel('LBL_COMMENTS/REASON'); ?></dt>
                                    <dd><?php echo nl2br($row['tereq_comments']); ?></dd>
                                </dl>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="repeatedrow">
                <form class="web_form form_horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <h3><i class="ion-person icon"></i> <?php echo Label::getLabel('LBL_PROFILE_INFORMATION'); ?></h3>
                        </div>
                    </div>
                    <div class="rowbody">
                        <div class="listview">
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_PROFILE_PICTURE'); ?></dt>
                                <dd><img src="<?php echo MyUtility::makeUrl('Image', 'show', [$userImage['file_type'], $row['tereq_user_id'], Afile::SIZE_SMALL]); ?>" /></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_PHOTO_ID'); ?></dt>
                                <dd>
                                    <?php
                                    if (!empty($photoIdRow['file_record_id'])) {
                                        echo '<a target="_blank" href="' . MyUtility::makeFullUrl('Image', 'download', [Afile::TYPE_TEACHER_APPROVAL_PROOF, $photoIdRow['file_record_id']]) . '" download>' . $photoIdRow['file_name'] . '</a>';
                                    } else {
                                        echo "-";
                                    }
                                    ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_FIRST_NAME'); ?></dt>
                                <dd><?php echo $row['tereq_first_name']; ?>&nbsp;</dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_LAST_NAME'); ?></dt>
                                <dd><?php echo $row['tereq_last_name']; ?>&nbsp;</dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_GENDER'); ?></dt>
                                <dd><?php echo User::getGenderTypes()[$row['tereq_gender']]; ?>&nbsp;</dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_PHONE_NUMBER'); ?></dt>
                                <dd><?php echo $row['tereq_phone_code'] . ' ' . $row['tereq_phone_number']; ?>&nbsp;</dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_YOU_TUBE_VIDEO_LINK'); ?></dt>
                                <dd><?php echo $row['tereq_video_link']; ?> &nbsp;</dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_PROFILE_INFO'); ?></dt>
                                <dd><?php echo $row['tereq_biography']; ?> &nbsp; </dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_TEACHING_LANGUAGE'); ?></dt>
                                <dd> <?php echo implode(', ', $teachLanguages) ?>
                                </dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_SPOKEN_LANGUAGE'); ?></dt>
                                <dd>
                                    <?php foreach ($row['tereq_speak_langs'] as $key => $val) { ?>
                                        <?php echo $speakLanguagesArr[$val] . ' : ' . $speakLanguageProfArr[$row['tereq_slang_proficiency'][$key]] . '<br/>'; ?>
                                    <?php } ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>