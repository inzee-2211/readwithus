<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="results page-panel montly-lesson-calendar margin-top-6">
    <div id='calendar-container'>
        <div id='d_calendar' class="calendar-view"></div>
    </div>
</div>
<script>
    moreLinkTextLabel = '<?php echo Label::getLabel('LBL_VIEW_MORE'); ?>';
    var fecal = new FatEventCalendar(0, '<?php echo MyDate::getOffset($siteTimezone); ?>');
    fecal.ClassesMonthlyCalendar('<?php echo $nowDate; ?>');
</script>