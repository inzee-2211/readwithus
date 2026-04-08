<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (count($allLessons) == 0) {
    $link = MyUtility::makeFullUrl('Teachers', '', [], CONF_WEBROOT_FRONTEND);
    $variables = ['msgHeading' => Label::getLabel('LBL_NO_LESSON!')];
    if ($siteUserType == User::LEARNER) {
        $variables['btn'] = '<a href="' . MyUtility::makeFullUrl('Teachers', '', [], CONF_WEBROOT_FRONTEND) . '" class="btn btn--primary">' . Label::getLabel('LBL_FIND_TEACHER') . '</a>';
    }
    $this->includeTemplate('_partial/no-record-found.php', $variables, false);
    return;
}
$subscriptionLabel = Order::getTypeArr(Order::TYPE_SUBSCR);
?>
<div class="results">
    <?php foreach ($allLessons as $key => $lessons) { ?>
        <div class="lessons-group margin-top-10">
            <date class="date uppercase small bold-600"><?php echo $key; ?></date>
            <?php foreach ($lessons as $lesson) { ?>
                <!-- [ LESSON CARD ========= -->
                <div class="card-landscape">
                    <div class="card-landscape__colum card-landscape__colum--first">
                        <?php if (!empty($lesson['ordles_starttime_unix']) || !empty($lesson['ordles_lesson_time_info'])) { ?>
                            <div class="card-landscape__head">
                                <?php if (!empty($lesson['ordles_starttime_unix'])) { ?>
                                    <time class="card-landscape__time"><?php echo date('H:i', $lesson['ordles_starttime_unix']) . ' - ' . date('H:i', $lesson['ordles_endtime_unix']); ?></time>
                                    <date class="card-landscape__date"><?php echo $myDate->convertToLocal(date('l, F d, Y', $lesson['ordles_starttime_unix'])); ?></date>
                                <?php } ?>
                                <?php if ($lesson['canScheduleLesson']) { ?>
                                    <a href="javascript:void();" onclick="scheduleForm('<?php echo $lesson['ordles_id']; ?>', '');" class="card-landscape__time"><?php echo Label::getLabel('LBL_SCHEDULE_NOW'); ?></a>
                                <?php } ?>
                            </div>
                            <div class="timer">
                                <?php if ($lesson['ordles_status'] == Lesson::SCHEDULED || !empty($lesson['ordles_lesson_time_info'])) { ?>
                                    <div class="timer__media">
                                        <span><svg class="icon icon--clock icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use></svg></span>
                                    </div>
                                    <div class="timer__content">
                                        <?php if ($lesson['ordles_starttime_unix'] > $lesson['ordles_currenttime_unix']) { ?>
                                            <div class="timer__controls yocaoch-timer" id="<?php echo $siteUserType . '_' . $lesson['ordles_id']; ?>" timestamp="<?php echo $lesson['ordles_lesson_starttime_utc']; ?>">00:00:00:00</div>
                                        <?php } if (!empty($lesson['ordles_lesson_time_info'])) { ?>
                                            <span class="color-red"><?php echo Label::getLabel($lesson['ordles_lesson_time_info']); ?></span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--second">
                        <div class="card-landscape__head">
                            <span class="card-landscape__title"><?php echo $lesson['lessonTitle']; ?></span>
                            <span class="card-landscape__status badge color-secondary badge--curve badge--small margin-left-0"><?php echo Lesson::getStatuses($lesson['ordles_status']); ?></span>
                            <?php if ($lesson['order_type'] == Order::TYPE_SUBSCR) { ?>
                                <span class="card-landscape__status badge color-secondary badge--curve badge--small margin-left-0"><?php echo $subscriptionLabel; ?></span>
                            <?php } if ($lesson['repiss_id'] > 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_ISSUE_REPORTED'); ?></span>
                            <?php } ?>
                        </div>
                        <?php if ($lesson['ordles_status'] != Lesson::CANCELLED) { ?>
                            <div class="card-landscape__docs">
                                <?php if ($lesson['plan_id'] > 0) { ?>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" onclick="viewAssignedPlan('<?php echo $lesson['plan_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>');" class="attachment-file">
                                            <svg class="icon icon--issue icon--attachement icon--xsmall color-black"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#attach'; ?>"></use></svg>
                                            <?php echo $lesson['plan_title'] ?>
                                        </a>
                                        <?php if ($siteUserType == User::TEACHER) { ?>
                                            <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $lesson['ordles_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>');" class="underline color-black  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_CHANGE'); ?></a>
                                            <a href="javascript:void(0);" onclick="removeAssignedPlan('<?php echo $lesson['ordles_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>');" class="underline color-black  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_REMOVE'); ?></a>
                                        <?php } ?>
                                    </div>
                                    <?php
                                } else {
                                    if ($siteUserType == User::TEACHER) {
                                        ?>
                                        <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $lesson['ordles_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>')" class="btn btn--transparent btn--addition color-black btn--small"><?php echo Label::getLabel('LBL_ATTACH_LESSON_PLAN'); ?></a>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--third">
                        <div class="card-landscape__actions">
                            <div class="profile-meta">
                                <div class="profile-meta__media">
                                    <span class="avtar" data-title="<?php echo CommonHelper::getFirstChar($lesson['first_name']); ?>">
                                        <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $lesson['user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '?t=' . time(); ?>" />
                                    </span>
                                </div>
                                <div class="profile-meta__details">
                                    <p class="bold-600 color-black"><?php echo implode(" ", [$lesson['first_name'], $lesson['last_name']]); ?></p>
                                    <p class="small"><?php echo $lesson['country_name']; ?></p>
                                </div>
                            </div>
                            <div class="actions-group">
                                <?php if ($lesson['ordles_status'] != Lesson::CANCELLED) { ?>
                                    <a href="<?php echo MyUtility::makeFullUrl('Lessons', 'view', [$lesson['ordles_id']]); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--enter icon--18"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#enter'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Enter_Classroom'); ?></div>
                                    </a>
                                <?php } if ($lesson['canCancelLesson']) { ?>
                                    <a href="javascript:void(0);" onclick="cancelForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--cancel icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#cancel'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Cancel'); ?></div>
                                    </a>
                                <?php } if ($lesson['canRescheduleLesson']) { ?>
                                    <a href="javascript:void(0);" onclick="rescheduleForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#reschedule'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Reschedule'); ?></div>
                                    </a>
                                <?php } if ($lesson['canScheduleLesson']) { ?>
                                    <a href="javascript:void(0);" onclick="scheduleForm('<?php echo $lesson['ordles_id']; ?>', '');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#reschedule'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Schedule'); ?></div>
                                    </a>
                                <?php } if ($lesson['repiss_id'] > 0) { ?>
                                    <a href="javascript:void(0);" onclick="viewIssue('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--issue-details icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#view-report'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Issue'); ?></div>
                                    </a>
                                <?php } if ($lesson['canReportIssue']) { ?>
                                    <a href="javascript:void(0);" onclick="issueForm('<?php echo $lesson['ordles_id']; ?>', '<?php echo AppConstant::LESSON; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--issue-reported icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#report-issue'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Report'); ?></div>
                                    </a>
                                <?php } if ($lesson['canRateLesson']) { ?>
                                    <a href="javascript:void(0);" onclick="feedbackForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#lesson-view'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_RATE'); ?></div>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] ========= -->
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php
if ($post['view'] != AppConstant::VIEW_DASHBOARD_LISTING) {
    $pagingArr = [
        'pageSize' => $post['pagesize'],
        'page' => $post['pageno'], $post['pageno'],
        'recordCount' => $recordCount,
        'pageCount' => ceil($recordCount / $post['pagesize']),
    ];
    echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
}
?>
<script>
    $(document).ready(function () {
        $('.yocaoch-timer').each(function (i) {
            var recordId = $(this).attr('id');
            $('#' + recordId).yocoachTimer({
                recordId: recordId,
                recordType: 'LESSON'
            });
        })
    });
</script>