<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<div class="container container--fixed">
    <div class="page__head">
        <a href="<?php echo MyUtility::makeUrl('Courses') ?>" class="page-back">
            <svg class="icon icon--back margin-right-3">
                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#arrow-back"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK_TO_COURSES'); ?>
        </a>
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-8">
                <h1 id="mainHeadingJs">
                <?php
                if ($courseId > 0) {
                    echo $courseTitle;
                } else {
                    echo Label::getLabel('LBL_MANAGE_COURSE_DETAILS');
                }
                ?>
                </h1>
                <p class="margin-0"><?php echo Label::getLabel('LBL_MANAGE_COURSE_SUB_HEADING'); ?></p>
            </div>
            <div class="col-sm-auto"></div>
        </div>
    </div>
    <div class="page__body" id="pageContentJs"></div>
    <script>
        var courseId = "<?php echo $courseId ?>";
        var siteLangId = "<?php echo $siteLangId; ?>";
    </script>