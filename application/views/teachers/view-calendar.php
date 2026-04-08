<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div id="loaderCalendar" class="calendar-loader" style="display: none;">
    <div class="loader"></div>
</div>
<div class="calendar-view">
    <div class="calendar-view__head">
        <div class="row">
            <div class="col-sm-5">
                <h4><?php echo $teacher['user_first_name'] . " " . $teacher['user_last_name'] . " " . Label::getLabel('LBL_CALENDAR'); ?></h4>
            </div>
            <div class="col-sm-7">
                <div class="cal-status">
                    <span class="box-hint available-box">&nbsp;</span>
                    <p><?php echo Label::getLabel('LBL_AVAILABLE'); ?></p>
                </div>
                <div class="cal-status">
                    <span class="box-hint booked-box">&nbsp;</span>
                    <p><?php echo Label::getLabel('LBL_BOOKED'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="note note--secondary mb-5"> <svg class="icon icon--explanation">
            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#explanation'; ?> "></use>
        </svg>
        <p><b><?php echo Label::getLabel('LBL_NOTE:') ?></b><?php echo Label::getLabel('THIS_CALENDAR_IS_TO_ONLY_CHECK_AVAILABILITY'); ?></p>
    </div>
    <div id='calendar-container'>
        <div id='d_calendar'></div>
    </div>
</div>
<script>
    var fecal = new FatEventCalendar('<?php echo $teacher['user_id']; ?>', '<?php echo MyDate::getOffset($siteTimezone); ?>');
    fecal.AvailaibilityCalendar('<?php echo MyDate::formatDate(date('Y-m-d H:i:s')); ?>', '<?php echo $duration; ?>', '<?php echo $teacher['user_book_before']; ?>', false);
</script>