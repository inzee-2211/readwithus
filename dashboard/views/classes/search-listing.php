<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (count($allClasses) == 0) {
    $link = MyUtility::makeFullUrl('Teachers', '', [], CONF_WEBROOT_FRONTEND);
    $variables = ['msgHeading' => Label::getLabel('LBL_NO_GROUP_CLASS')];
    if ($siteUserType == User::LEARNER) {
        $variables['btn'] = '<a href="' . $link . '" class="btn btn--primary">' . Label::getLabel('LBL_FIND_TEACHER') . '</a>';
    }
    $this->includeTemplate('_partial/no-record-found.php', $variables, false);
    return;
}
?>
<div class="results">
    <?php foreach ($allClasses as $key => $classes) { ?>
        <div class="lessons-group margin-top-10">
            <date class="date uppercase small bold-600"><?php echo $key; ?></date>
            <?php
            foreach ($classes as $class) {
                $classId = ($siteUserType == User::LEARNER) ? $class['ordcls_id'] : $class['grpcls_id'];
                $classScheduled = (($siteUserType == User::TEACHER && $class['grpcls_status'] == GroupClass::SCHEDULED) ||
                        ($siteUserType == User::LEARNER && $class['ordcls_status'] == OrderClass::SCHEDULED));
                ?>
                <!-- [ LESSON CARD ========= -->
                <div class="card-landscape">
                    <div class="card-landscape__colum card-landscape__colum--first">
                        <div class="card-landscape__head">
                            <time class="card-landscape__time"><?php echo date('H:i', $class['grpcls_starttime_unix']) . ' - ' . date('H:i', $class['grpcls_endtime_unix']); ?></time>
                            <date class="card-landscape__date"><?php echo $myDate->convertToLocal(date('l, F d, Y', $class['grpcls_starttime_unix'])); ?></date>
                        </div>
                        <?php if ($classScheduled) { ?>
                            <div class="timer">
                                <div class="timer__media"><span><svg class="icon icon--clock icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use></svg></span></div>
                                <div class="timer__content">
                                    <?php if ($class['grpcls_starttime_unix'] > $class['grpcls_currenttime_unix']) { ?>
                                        <div class="timer__controls yocaoch-timer" id="<?php echo $siteUserType . '_' . $classId; ?>" timestamp="<?php echo $class['grpcls_start_datetime_utc']; ?>">00:00:00:00</div>
                                    <?php } if (empty($class['grpcls_booked_seats']) && $class['grpcls_starttime_unix'] < $class['grpcls_currenttime_unix']) { ?>
                                        <span class="color-red"><?php echo Label::getLabel('LBL_NO_ONE_HAS_BOOKED'); ?></span>
                                    <?php } elseif (!empty($class['class_time_info'])) { ?>
                                        <span class="color-red"><?php echo Label::getLabel($class['class_time_info']); ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--second">
                        <div class="card-landscape__head">
                            <span class="card-landscape__title"><?php echo $class['grpcls_title']; ?></span>
                            <span class="card-landscape__status badge color-secondary badge--curve badge--small margin-left-0">
                                <?php echo $class['statusText']; ?>
                            </span>
                            <?php if ($siteUserType == User::TEACHER) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_ENTRY_FEE') . ': ' . MyUtility::formatMoney($class['grpcls_entry_fee']); ?></span>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_BOOKED_SEATS') . ': ' . $class['grpcls_booked_seats'] . '/' . $class['grpcls_total_seats']; ?></span>
                            <?php } ?>
                            <?php if ($class['repiss_id'] > 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0">
                                    <?php echo Label::getLabel('LBL_ISSUE_REPORTED'); ?>
                                </span>
                            <?php } ?>
                            <?php if ($class['grpcls_parent'] > 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_PACKAGE_CLASS'); ?></span>
                            <?php } ?>
                        </div>
                        <?php if (!$class['isClassCanceled']) { ?>
                            <div class="card-landscape__docs">
                                <?php if ($class['plan_id'] > 0) { ?>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" onclick="viewAssignedPlan('<?php echo $class['plan_id']; ?>');" class="attachment-file">
                                            <svg class="icon icon--issue icon--attachement icon--xsmall color-black">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#attach'; ?>"></use>
                                            </svg>
                                            <?php echo $class['plan_title'] ?>
                                        </a>
                                        <?php if ($siteUserType == User::TEACHER) { ?>
                                            <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $class['grpcls_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>');" class="underline color-black  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_CHANGE'); ?></a>
                                            <a href="javascript:void(0);" onclick="removeAssignedPlan('<?php echo $class['grpcls_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>');" class="underline color-black  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_REMOVE'); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php } elseif ($siteUserType == User::TEACHER) { ?>
                                    <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $class['grpcls_id']; ?>', '<?php echo Plan::PLAN_TYPE_CLASSES; ?>');" class="btn btn--transparent btn--addition color-black btn--small"><?php echo Label::getLabel('LBL_ATTACH_LESSON_PLAN'); ?></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--third">
                        <div class="card-landscape__actions">
                            <div class="profile-meta">
                                <?php if ($siteUserType == User::LEARNER) { ?>
                                    <div class="profile-meta__media">
                                        <span class="avtar" data-title="<?php echo CommonHelper::getFirstChar($class['teacher_first_name']); ?>">
                                            <?php echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $class['grpcls_teacher_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '?t=' . time() . '" alt="' . $class['teacher_first_name'] . '" />'; ?>
                                        </span>
                                    </div>
                                    <div class="profile-meta__details">
                                        <p class="bold-600 color-black"><?php echo $class['teacher_first_name'] . ' ' . $class['teacher_last_name']; ?></p>
                                        <p class="small"> <?php echo $class['teacher_country']; ?> </p>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="actions-group">
                                <?php if ($class['grpcls_booked_seats'] > 0) { ?>
                                    <a href="<?php echo MyUtility::makeUrl('Classes', 'view', [$classId]); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--enter icon--18"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#enter'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_ENTER_CLASSROOM'); ?></div>
                                    </a>
                                <?php } if ($class['canEdit']) { ?>
                                    <a href="javascript:void(0);" onclick="addForm('<?php echo $class['grpcls_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--edit icon--small">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#edit'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_EDIT'); ?></div>
                                    </a>
                                <?php } if ($class['canCancelClass']) { ?>
                                    <a href="javascript:void(0);" onclick="cancelForm('<?php echo $classId; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--cancel icon--small">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#cancel'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_CANCEL'); ?></div>
                                    </a>
                                <?php } ?>
                                <?php
                                if ($class['repiss_id'] > 0) {
                                    $issueReportBtn = '<a href="javascript:void(0);" onclick="viewIssue(' . $class['repiss_id'] . ');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">';
                                    if ($siteUserType == User::TEACHER) {
                                        $issueReportBtn = '<a href="' . MyUtility::makeUrl('issues', 'index', [$class['grpcls_id']]) . '" target="_blank" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">';
                                    }
                                    echo $issueReportBtn;
                                    ?>
                                    <svg class="icon icon--issue-details icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#view-report'; ?>"></use></svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_VIEW_ISSUE_DETAIL'); ?></div>
                                    </a>
                                    <?php
                                }
                                if ($class['canReportClass']) {
                                    ?>
                                    <a href="javascript:void(0);" onclick="issueForm('<?php echo $class['ordcls_id']; ?>', '<?php echo AppConstant::GCLASS; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--issue-reported icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#report-issue'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_REPORT_ISSUE'); ?></div>
                                    </a>
                                <?php } ?>
                                <?php if ($class['canRateClass']) { ?>
                                    <a href="javascript:void(0);" onclick="feedbackForm('<?php echo $classId; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#lesson-view'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Rate'); ?></div>
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
$pagingArr = [
    'pageSize' => $post['pagesize'],
    'page' => $post['pageno'],
    'recordCount' => $recordCount,
    'pageCount' => ceil($recordCount / $post['pagesize']),
];
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
<script>
    $(document).ready(function () {
        $('.yocaoch-timer').each(function (i) {
            var recordId = $(this).attr('id');
            $('#' + recordId).yocoachTimer({
                recordId: recordId,
                recordType: 'CLASS'
            });
        })
    });
</script>