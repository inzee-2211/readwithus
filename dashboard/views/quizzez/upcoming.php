<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($allLessons)) {
    return;
}
$upcomingLesson = current($allLessons);
?>
<div class="infobar infobar--primary">
    <div class="row justify-content-between align-items-center">
        <div class="col-lg-8 col-sm-6">
            <div class="d-flex align-items-lg-center">
                <div class="infobar__media margin-right-5">
                    <div class="infobar__media-icon infobar__media-icon--vcamera ">
                        <svg class="icon icon--vcamera">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#video-camera'; ?>"></use>
                        </svg>
                    </div>
                </div>
                <div class="infobar__content">
                    <div class="upcoming-lesson display-inline">
                        <?php echo Label::getLabel('LBL_NEXT_LESSON:'); ?> <date class=" bold-600"> <?php echo date('Y-m-d', $upcomingLesson['ordles_starttime_unix']); ?></date> <?php echo Label::getLabel('LBL_AT'); ?> <time class=". bold-600"><?php echo date('H:i', $upcomingLesson['ordles_starttime_unix']); ?></time>
                        <?php echo Label::getLabel('LBL_WITH'); ?>
                        <div class="avtar-meta display-inline">
                            <span class="avtar avtar--xsmall display-inline margin-right-2" data-title="<?php echo CommonHelper::getFirstChar($upcomingLesson['first_name']); ?>">
                                <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $upcomingLesson['user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg'); ?>" />
                            </span>
                            <?php echo $upcomingLesson['first_name'] . ' ' . $upcomingLesson['last_name']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="upcoming-lesson-action d-flex align-items-center justify-content-between justify-content-sm-end">
                <div class="timer margin-right-4">
                    <div class="timer__media">
                        <span><svg class="icon icon--clock icon--small">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use>
                            </svg></span>
                    </div>
                    <div class="timer__content">
                        <div class="timer__controls timer-js style colorDefinition size_sm" id="lessonStartTimer" timestamp="<?php echo $upcomingLesson['ordles_lesson_starttime_utc']; ?>">00:00:00:00</div>
                    </div>
                </div>
                <a href="<?php echo MyUtility::makeUrl('Lessons', 'view', [$upcomingLesson['ordles_id']]); ?>" class="btn bg-secondary"><?php echo Label::getLabel('LBL_ENTER_CLASSROOM') ?></a>
            </div>
        </div>
    </div>
</div>
<script>
    $("#lessonStartTimer").yocoachTimer({
        recordType: 'LESSON',
        recordId: '<?php echo $upcomingLesson['ordles_id']; ?>'
    });
</script>