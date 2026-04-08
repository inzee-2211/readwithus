<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<script>
<?php if ($flashcardEnabled) { ?>
        const FLASHCARD_VIEW = '<?php echo Flashcard::VIEW_SHORT; ?>';
        const FLASHCARD_TYPE = '<?php echo Flashcard::TYPE_GCLASS; ?>';
        const FLASHCARD_TYPE_ID = '<?php echo $class['ordcls_id']; ?>';
        const FLASHCARD_TLANG_ID = '<?php echo $class['grpcls_tlang_id']; ?>';
<?php } ?>
    const ACTIVE_MEETING_TOOL = '<?php echo $mettingTool['metool_code']; ?>';
    const ATOM_CHAT = '<?php echo MeetingTool::ATOM_CHAT; ?>';
    const PUBLISHED = <?php echo GroupClass::SCHEDULED; ?>;
    const SCHEDULED = <?php echo OrderClass::SCHEDULED; ?>;
    const CANCELLED = <?php echo OrderClass::CANCELLED; ?>;
    const COMPLETED = <?php echo OrderClass::COMPLETED; ?>;
    var grpcls_currenttime_unix = <?php echo FatUtility::int($class['grpcls_currenttime_unix']); ?>;
    var grpcls_starttime_unix = <?php echo FatUtility::int($class['grpcls_starttime_unix']); ?>;
    var grpClsStatus = <?php echo FatUtility::int($class['grpcls_status']); ?>;
    var ordClsStatus = <?php echo FatUtility::int($class['ordcls_status']); ?>;
    var grpcls_endtime_unix = <?php echo FatUtility::int($class['grpcls_endtime_unix']); ?>;
    var joinTime = "<?php echo $class['joinTime']; ?>";
    var userType = <?php echo FatUtility::int($siteUserType); ?>;
    var classId = <?php echo FatUtility::int($classId); ?>;
    var grpclsId = <?php echo FatUtility::int($class['grpcls_id']); ?>;
    var ordclsId = <?php echo FatUtility::int($class['ordcls_id']); ?>;
    var canJoin = <?php echo FatUtility::int($class['canJoin']); ?>;
    var eneTimeMsg = "<?php echo CommonHelper::htmlEntitiesDecode(Label::getLabel('LBL_LESSON_ENDTIME_MSG')); ?>";
    var endClassConfirmMsg = "<?php echo CommonHelper::htmlEntitiesDecode(Label::getLabel('LBL_END_CLASS_CONFIRM_MSG')); ?>";
</script>
<!-- [ PAGE ========= -->
<div class="session">
    <div class="session__head">
        <div class="session-infobar">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-8 col-lg-8 col-sm-12">
                    <div class="session-infobar__top">
                        <h4>
                            <?php
                            echo $class['grpcls_title'];
                            echo ' <span class="color-primary">' . $class['statusText'] . '</span> ';
                            echo (!empty($class['user_id'])) ? Label::getLabel('LBL_WITH') : ' ';
                            ?>
                        </h4>
                        <div class="profile-meta">
                            <div class="profile-meta__media">
                                <span class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar($class['first_name']); ?>">
                                    <?php echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $class['user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '" />'; ?>
                                </span>
                            </div>
                            <div class="profile-meta__details">
                                <h4 class="bold-600"><?php echo $class['first_name'] . ' ' . $class['last_name']; ?></h4>
                            </div>
                        </div>
                        <?php if (count($learners) > 1) { ?>
                            <div class="more-dropdown">
                                <a class="menu__item-trigger trigger-js color-secondary" href="#more-stud">&nbsp;<?php echo ' +' . (count($learners) - 1) . ' ' . Label::getLabel('LBL_More'); ?></a>
                                <ul class="menu__dropdown more--dropdown" id="more-stud">
                                    <?php
                                    foreach ($learners as $learner) {
                                        if ($learner['user_id'] == $class['order_user_id']) {
                                            continue;
                                        }
                                        ?>
                                        <li>
                                            <div class="profile-meta">
                                                <div class="profile-meta__media">
                                                    <span class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar($learner['learner_first_name']); ?>">
                                                        <?php echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $learner['order_user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '" />'; ?>
                                                    </span>
                                                </div>
                                                <div class="profile-meta__details">
                                                    <h4 class="bold-600"><?php echo $learner['learner_first_name'] . ' ' . $learner['learner_last_name']; ?></h4>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="session-infobar__bottom">
                        <?php if (!empty($class['grpcls_starttime_unix'])) { ?>
                            <div class="session-time">
                                <p>
                                    <span><?php echo date('H:i', $class['grpcls_starttime_unix']) . ' - ' . date('H:i', $class['grpcls_endtime_unix']); ?>,</span>
                                    <?php echo date('Y-m-d', $class['grpcls_starttime_unix']); ?>
                                </p>
                            </div>
                        <?php } ?>
                        <?php if (!$class['isClassCanceled'] && $class['plan_id'] > 0) { ?>
                            <div class="session-resource">
                                <a href="javascript:void(0);" onclick="viewAssignedPlan('<?php echo $class['plan_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>')" class="attachment-file padding-2">
                                    <svg class="icon icon--issue icon--attachement icon--xsmall color-black">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#attach'; ?>"></use>
                                    </svg>
                                    <?php echo $class['plan_title']; ?>
                                </a>
                            </div>
                            <?php if ($siteUserType == User::TEACHER && ($class['grpcls_starttime_unix'] - $class['grpcls_currenttime_unix']) > 0) { ?>
                                <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $class['grpcls_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>');" class="underline color-black  padding-2"><?php echo Label::getLabel('LBL_CHANGE'); ?></a>
                                <a href="javascript:void(0);" onclick="removeAssignedPlan('<?php echo $class['grpcls_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>');" class="underline color-black  padding-2"><?php echo Label::getLabel('LBL_REMOVE'); ?></a>
                            <?php } ?>
                            <?php
                        } else {
                            if ($siteUserType == User::TEACHER && ($class['grpcls_starttime_unix'] - $class['grpcls_currenttime_unix']) > 0) {
                                ?>
                                <div class="session-resource">
                                    <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $class['grpcls_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>');" class="btn btn--transparent btn--addition color-black padding-2"><?php echo Label::getLabel('LBL_ATTACH_LESSON_PLAN'); ?></a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-12">
                    <div class="session-infobar__action">
                        <?php if ($class['showEndTimer']) { ?>
                            <span class="btn btn--live" id="classEndTimer" timestamp="<?php echo $class['grpcls_end_datetime_utc']; ?>"> 00:00:00:00 </span>
                        <?php } ?>
                        <button class="btn bg-red end_lesson_now <?php echo (!$class['canEnd']) ? 'd-none' : ''; ?> " id="endClass" onclick="endMeeting(<?php echo $classId; ?>);"><?php echo Label::getLabel('LBL_END_LESSON'); ?></button>
                        <?php if ($class['canCancelClass']) { ?>
                            <button onclick="cancelForm('<?php echo $classId; ?>');" class="btn btn--bordered color-third cancel-lesson--js"><?php echo Label::getLabel('LBL_CANCEL'); ?></button>
                        <?php } ?>
                        <?php
                        if ($class['repiss_id'] > 0) {
                            $issueReportBtn = '<a href="javascript:void(0);" onclick="viewIssue(' . $class['repiss_id'] . ');" class="btn btn--bordered color-third ">' . Label::getLabel('LBL_VIEW_ISSUE_DETAIL') . '</a>';
                            if ($siteUserType == User::TEACHER) {
                                $issueReportBtn = '<a href="' . MyUtility::makeUrl('issues', 'index', [$class['grpcls_id']]) . '" target="_blank" class="btn btn--bordered color-third ">' . Label::getLabel('LBL_VIEW_ISSUE_DETAIL') . '</a>';
                            }
                            echo $issueReportBtn;
                            ?>
                            <?php
                        }
                        if ($class['canReportClass']) {
                            ?>
                            <button onclick="issueForm('<?php echo $classId; ?>', '<?php echo AppConstant::GCLASS; ?>');" class="btn btn--third"><?php echo Label::getLabel('LBL_REPORT_ISSUE'); ?></button>
                        <?php } ?>
                        <?php if ($class['canRateClass']) { ?>
                            <button onclick="feedbackForm('<?php echo $classId; ?>');" class="btn btn--bordered color-third "><?php echo Label::getLabel('LBL_RATE'); ?></button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="session__body">
        <div class="sesson-window" style="background-image:url(<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_LESSON_PAGE_IMAGE, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') ?>);">
            <div class="sesson-window__content classBox" id="classBox">
                <!-- session-window__frame -->
                <div class="session-status">
                    <?php if (!is_null($class['teacher_deleted'])) { ?>
                        <?php $link = MyUtility::makeUrl('Contact', 'index', [], CONF_WEBROOT_FRONTEND); ?>
                        <p><?php echo Label::getLabel('LBL_USER_NO_MORE_EXISTS'); ?></p>
                        <a class="btn btn--secondary" href="<?php echo $link; ?>"><?php echo Label::getLabel('LBL_CONTACT_US'); ?></a>
                    <?php } elseif ((($siteUserType == User::TEACHER && $class['grpcls_status'] != GroupClass::SCHEDULED) || ($siteUserType == User::LEARNER && $class['ordcls_status'] != OrderClass::SCHEDULED)) || $class['grpcls_endtime_unix'] < $class['grpcls_currenttime_unix']) {
                        ?>
                        <div class="status_media">
                            <svg class="icon">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use>
                            </svg>
                        </div>
                        <?php echo empty($class['statusInfoLabel']) ? '' : '<p>' . $class['statusInfoLabel'] . '</p>'; ?>
                        <a href="<?php echo MyUtility::makeUrl('Classes'); ?>" class="btn btn--primary btn--large"><?php echo Label::getLabel('LBL_GO_TO_CLASSES'); ?></a>
                    <?php } elseif ($class['canJoin']) { ?>
                        <div class="join-btns">
                            <?php if ($mettingTool['metool_code'] == MeetingTool::ZOOM_MEETING) { ?>
                                <a href="javascript:void(0);" class="btn btn--primary btn--large" onclick="joinMeeting('<?php echo $classId; ?>', false);"><?php echo Label::getLabel('LBL_JOIN_FROM_BROWSER'); ?></a>
                                <div class="-gap-10"></div>
                                <a href="javascript:void(0);" class="btn btn--secondary btn--large" onclick="joinMeeting('<?php echo $classId; ?>', true);"><?php echo Label::getLabel('LBL_JOIN_FROM_ZOOM_APP'); ?></a>
                            <?php } else { ?>
                                <a href="javascript:void(0);" class="btn btn--secondary btn--large" onclick="joinMeeting('<?php echo $classId; ?>', false);"><?php echo Label::getLabel('LBL_JOIN_CLASS'); ?></a>
                            <?php } ?>
                        </div>
                    <?php } elseif ($class['showTimer']) { ?>
                        <div class="start-lesson-timer timer">
                            <h5 class="timer-title"><?php echo Label::getLabel('LBL_STARTS_IN'); ?></h5>
                            <div class="countdown-timer size_lg" id="classStartTimer" timestamp="<?php echo $class['grpcls_start_datetime_utc']; ?>">00:00:00:00</div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
<?php if ($class['showTimer']) { ?>
            $("#classStartTimer").yocoachTimer({
                recordId: ordclsId,
                recordType: 'CLASS',
                callback: function () {
                    window.location.reload();
                }
            });
<?php } ?>
<?php if ($class['showEndTimer']) { ?>
            $("#classEndTimer").yocoachTimer({
                recordId: ordclsId,
                recordType: 'CLASS',
                callback: function () {
                    $(".join-btns").addClass('d-none');
                }
            });
    <?php if ($siteUserType == User::LEARNER) { ?>
                checkClassStatus(ordclsId, ordClsStatus);
    <?php } if ($siteUserType == User::TEACHER) { ?>
                console.log('hre');
                checkClassStatus(ordclsId, grpClsStatus);
    <?php } ?>
<?php } ?>
    });
</script>