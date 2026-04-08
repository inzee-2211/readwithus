<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($allLessons)) {
    $variables['btn'] = '<a href="' . MyUtility::makeFullUrl('Lessons') . '" class="btn btn--primary">' . Label::getLabel('LBL_VIEW_ALL_LESSONS') . '</a>';
    $variables['msgHeading'] = Label::getLabel('LBL_NO_UPCOMING_LESSON');
    $this->includeTemplate('_partial/no-record-found.php', $variables, false);
    return;
}
foreach ($allLessons as $key => $lessons) {
    ?>
    <div class="lesson-list-container">
        <div class="lesson-list_head">
            <div class="date">
                <p><?php echo $key; ?></p>
            </div>
            <?php foreach ($lessons as $lesson) { ?>
                <div class="lesson-list short-details">
                    <div class="lesson-list__left">
                        <div class="avtar avtar--small avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($lesson['first_name']); ?>">
                            <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $lesson['user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg'); ?>" />
                        </div>
                    </div>
                    <div class="lesson-list__right">
                        <p><?php echo ucwords(implode(" ", [$lesson['first_name'], $lesson['last_name']])); ?></p>
                        <p class="lesson-time">
                            <span><?php echo date('H:i', $lesson['ordles_starttime_unix']); ?></span><?php echo $lesson['lessonTitle']; ?>
                        </p>
                    </div>
                    <a href="<?php echo MyUtility::makeUrl('Lessons', 'view', [$lesson['ordles_id']]); ?>" class="lesson-list__action"></a>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>