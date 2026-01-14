<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$joinTime = ($siteUserType == User::LEARNER) ? $lesson['ordles_student_starttime'] : $lesson['ordles_teacher_starttime'];
$startTimer = false;
$endTimer = false;
if ($lesson['ordles_type'] == Lesson::TYPE_FTRAIL) {
    $lesson['ordles_tlang_id'] = '-1';
}

// Get current time in seconds
$currentTime = time();

// Check if we have UTC timestamps and convert them to local timestamps for display
if (isset($lesson['ordles_lesson_starttime_utc'])) {
    // If we have UTC timestamp, convert to local Unix timestamp
    $startTimeLocal = strtotime($lesson['ordles_lesson_starttime']);
    $endTimeLocal = strtotime($lesson['ordles_lesson_endtime']);
} else {
    // Use the Unix timestamps directly
    $startTimeLocal = $lesson['ordles_starttime_unix'];
    $endTimeLocal = $lesson['ordles_endtime_unix'];
}

// Calculate if lesson is in the future
$isFutureLesson = ($startTimeLocal > $currentTime);
$isLiveLesson = (
    $lesson['ordles_status'] == Lesson::SCHEDULED
    && $endTimeLocal > $currentTime
    && $startTimeLocal <= $currentTime
);

// For timer display, we need the actual start/end times in milliseconds
$startTimeForTimer = $startTimeLocal * 1000;
$endTimeForTimer = $endTimeLocal * 1000;
$currentTimeForTimer = $currentTime * 1000;
?>
<script>
<?php if ($flashcardEnabled) { ?>
        const FLASHCARD_VIEW = '<?php echo Flashcard::VIEW_SHORT; ?>';
        const FLASHCARD_TYPE = '<?php echo Flashcard::TYPE_LESSON; ?>';
        const FLASHCARD_TYPE_ID = '<?php echo $lesson['ordles_id']; ?>';
        const FLASHCARD_TLANG_ID = '<?php echo $lesson['ordles_tlang_id']; ?>';
<?php } ?>
    const CONF_ACTIVE_MEETING_TOOL = '<?php echo $activeMettingTool['metool_code']; ?>';
    const ATOM_CHAT = '<?php echo MeetingTool::ATOM_CHAT; ?>';
    const SCHEDULED = <?php echo Lesson::SCHEDULED ?>;
    const CANCELLED = <?php echo Lesson::CANCELLED ?>;
    const COMPLETED = <?php echo Lesson::COMPLETED ?>;
    const USER_TYPE = <?php echo FatUtility::int($siteUserType); ?>;
    var lessonStatus = <?php echo FatUtility::int($lesson['ordles_status']); ?>;
    var lessonId = <?php echo FatUtility::int($lesson['ordles_id']); ?>;
    
    // Use the calculated local timestamps in milliseconds
    var ordles_starttime_unix = <?php echo $startTimeForTimer; ?>;
    var ordles_endtime_unix = <?php echo $endTimeForTimer; ?>;
    var ordles_currenttime_unix = <?php echo $currentTimeForTimer; ?>;
    
    // Debug logs
    console.log('Start time (local):', new Date(<?php echo $startTimeForTimer; ?>));
    console.log('End time (local):', new Date(<?php echo $endTimeForTimer; ?>));
    console.log('Current time:', new Date(<?php echo $currentTimeForTimer; ?>));
    console.log('Is future lesson:', <?php echo $isFutureLesson ? 'true' : 'false'; ?>);

    var joinTime = '<?php echo $joinTime; ?>';
    var canJoin = <?php echo FatUtility::int($lesson['canJoin']); ?>;
    var endLessonConfirmMsg = "<?php echo CommonHelper::htmlEntitiesDecode(Label::getLabel('LBL_END_LESSON_CONFIRM_MSG')); ?>";
</script>
<!-- [ PAGE ========= -->
<div class="session">
    <div class="session__head">
        <div class="session-infobar">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-8 col-lg-8 col-sm-12">
                    <div class="session-infobar__top">
                        <h4><?php echo $lesson['lessonTitle'] . ' ' . '<span class="color-primary">' . Lesson::getStatuses($lesson['ordles_status']) . '</span>' . ' ' . Label::getLabel('LBL_WITH'); ?></h4>
                        <div class="profile-meta">
                            <div class="profile-meta__media">
                                <span class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar($lesson['first_name']); ?>">
                                    <?php echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $lesson['user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '" />'; ?>
                                </span>
                            </div>
                            <div class="profile-meta__details"><h4 class="bold-600"><?php echo $lesson['first_name'] . ' ' . $lesson['last_name']; ?></h4></div>
                        </div>
                    </div>
                    <div class="session-infobar__bottom">
                        <?php if (!empty($lesson['ordles_starttime_unix'])) { ?>
                            <div class="session-time">
                                <p>
                                    <span><?php echo date('H:i', $startTimeLocal) . ' - ' . date('H:i', $endTimeLocal); ?>,</span>
                                    <?php echo date('Y-m-d', $startTimeLocal); ?>
                                </p>
                            </div>
                        <?php } ?>
                        <?php if ($lesson['ordles_status'] != Lesson::CANCELLED && $lesson['plan_id'] > 0) { ?>
                            <div class="session-resource">
                                <a href="javascript:void(0);" onclick="viewAssignedPlan('<?php echo $lesson['plan_id']; ?>')" class="attachment-file padding-2">
                                    <svg class="icon icon--issue icon--attachement icon--xsmall color-black"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#attach'; ?>"></use></svg>
                                    <?php echo $lesson['plan_title'] ?>
                                </a>
                                <?php if ($siteUserType == User::TEACHER && ($startTimeLocal - $currentTime) > 0) { ?>
                                    <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $lesson['ordles_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>');" class="underline attachment-file padding-2"><?php echo Label::getLabel('LBL_CHANGE'); ?></a>
                                    <a href="javascript:void(0);" onclick="removeAssignedPlan('<?php echo $lesson['ordles_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>');" class="underline attachment-file padding-2"><?php echo Label::getLabel('LBL_REMOVE'); ?></a>
                                <?php } ?>
                            </div>
                        <?php } else if ($siteUserType == User::TEACHER && $lesson['ordles_status'] != Lesson::CANCELLED && ($startTimeLocal - $currentTime) > 0) { ?>
                            <div class="session-resource">
                                <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $lesson['ordles_id']; ?>', '<?php echo Plan::PLAN_TYPE_LESSONS; ?>');" class="btn btn--transparent btn--addition color-black padding-2"><?php echo Label::getLabel('LBL_ATTACH_LESSON_PLAN'); ?></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-12">
                    <div class="session-infobar__action">
                        <?php if ($isLiveLesson) { ?>
                            <?php $endTimer = true; ?>
                            <span class="btn btn--live" id="lessonEndTimer" timestamp="<?php echo $endTimeForTimer; ?>"> 00:00:00:00 </span>
                        <?php } ?>
                        <button class="btn bg-red end_lesson_now <?php echo (!$lesson['canEnd']) ? 'd-none' : ''; ?> " id="endL" onclick="endLesson(<?php echo $lesson['ordles_id']; ?>);"><?php echo Label::getLabel('LBL_End_Lesson'); ?></button>
                        <?php if ($lesson['canCancelLesson']) { ?>
                            <button onclick="cancelForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--bordered color-third cancel-lesson--js"><?php echo Label::getLabel('LBL_Cancel'); ?></button>
                        <?php } ?>
                        <?php if ($lesson['canRescheduleLesson']) { ?>
                            <button onclick="rescheduleForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--third reschedule-lesson--js"><?php echo Label::getLabel('LBL_RESCHEDULE'); ?></button>
                        <?php } ?>
                        <?php if ($lesson['canScheduleLesson']) { ?>
                            <button onclick="scheduleForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--third"><?php echo Label::getLabel('LBL_SCHEDULE'); ?></button>
                        <?php } ?>
                        <?php if ($lesson['repiss_id'] > 0) { ?>
                            <button onclick="viewIssue('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered color-third"> <?php echo Label::getLabel('LBL_Issue'); ?> </button>
                        <?php } if ($lesson['canReportIssue']) { ?>
                            <button onclick="issueForm('<?php echo $lesson['ordles_id']; ?>', '<?php echo AppConstant::LESSON; ?>');" class="btn btn--third"> <?php echo Label::getLabel('LBL_REPORT'); ?> </button>
                        <?php } ?>
                        <?php if ($lesson['canRateLesson']) { ?>
                            <button onclick="feedbackForm('<?php echo $lesson['ordles_id']; ?>');" class="btn btn--third"> <?php echo Label::getLabel('LBL_Rate'); ?> </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="session__body">
        <div class="sesson-window" style="background-image:url(<?php echo FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_LESSON_PAGE_IMAGE, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') ?>)">
            <div class="sesson-window__content lessonBox" id="lessonBox">
                <!-- session-window__frame -->
                <div class="session-status">
                    <?php if (!is_null($lesson['user_deleted'])) { ?>
                        <?php $link = MyUtility::makeFullUrl('Contact', 'index', [], CONF_WEBROOT_FRONTEND); ?>
                        <p><?php echo Label::getLabel('LBL_USER_NO_MORE_EXISTS'); ?></p>
                        <a class="btn btn--secondary" href="<?php echo $link; ?>"><?php echo Label::getLabel('LBL_CONTACT_US'); ?></a>
                    <?php } elseif ($lesson['ordles_status'] != Lesson::SCHEDULED || $endTimeLocal < $currentTime) { ?>
                        <div class="status_media">
                            <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use></svg>
                        </div>
                        <?php echo empty($lesson['statusInfoLabel']) ? '' : '<p>' . $lesson['statusInfoLabel'] . '</p>'; ?>
                        <a href="<?php echo MyUtility::makeFullUrl('Lessons'); ?>" class="btn btn--primary btn--large"><?php echo Label::getLabel('LBL_GO_TO_LESSONS'); ?></a>
                    <?php } elseif ($lesson['canJoin']) { ?>
                        <div class="join-btns join_lesson_now">
                            <?php if ($activeMettingTool['metool_code'] == MeetingTool::ZOOM_MEETING) { ?>
                                <a href="javascript:void(0);" class="btn btn--primary btn--large" onclick="joinLesson('<?php echo $lesson['ordles_id']; ?>', false);"><?php echo Label::getLabel('LBL_JOIN_FROM_BROWSER'); ?></a>
                                <div class="-gap-10"></div>
                                <a href="javascript:void(0);" class="btn btn--secondary btn--large" onclick="joinLesson('<?php echo $lesson['ordles_id']; ?>', true);"><?php echo Label::getLabel('LBL_JOIN_FROM_ZOOM_APP'); ?></a>
                            <?php } else { ?>
                                <a href="javascript:void(0);" class="btn btn--secondary btn--large" onclick="joinLesson('<?php echo $lesson['ordles_id']; ?>', false);"><?php echo Label::getLabel('LBL_JOIN_LESSON'); ?></a>
                            <?php } ?>
                        </div>
                    <?php } elseif ($lesson['ordles_status'] == Lesson::SCHEDULED && $isFutureLesson) { ?>
                        <?php $startTimer = true; ?>
                        <div class="start-lesson-timer timer">
                            <h5 class="timer-title"><?php echo Label::getLabel('LBL_STARTS_IN'); ?></h5>
                            <div class="countdown-timer size_lg" id="lessonStartTimer" timestamp="<?php echo $startTimeForTimer; ?>">00:00:00:00</div>
                        </div>
                    <?php } elseif ($lesson['ordles_status'] == Lesson::SCHEDULED && !$isFutureLesson) { ?>
                        <!-- Lesson is scheduled but in the past -->
                        <div class="status_media">
                            <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use></svg>
                        </div>
                        <p><?php echo Label::getLabel('LBL_LESSON_TIME_HAS_PASSED'); ?></p>
                        <a href="<?php echo MyUtility::makeFullUrl('Lessons'); ?>" class="btn btn--primary btn--large"><?php echo Label::getLabel('LBL_GO_TO_LESSONS'); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ] -->
<script>
    $(document).ready(function () {
        <?php if ($startTimer) { ?>
            console.log('Initializing start timer for lesson:', lessonId);
            $("#lessonStartTimer").yocoachTimer({
                recordId: lessonId,
                recordType: 'LESSON',
                callback: function () {
                    console.log('Start timer finished, reloading page');
                    window.location.reload();
                }
            });
        <?php } ?>
        
        <?php if ($endTimer) { ?>
            console.log('Initializing end timer for lesson:', lessonId);
            $("#lessonEndTimer").yocoachTimer({
                recordId: lessonId,
                recordType: 'LESSON',
                callback: function () {
                    console.log('End timer finished, hiding join buttons');
                    $(".join-btns").addClass('d-none');
                }
            });
            checkLessonStatus(lessonId, lessonStatus);
        <?php } ?>
    });
</script>