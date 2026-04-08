<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->addFormTagAttribute('onsubmit', 'scheduleSetup(this); return false;');
$startTime = $form->getField('ordles_lesson_starttime');
$startTime->addFieldTagAttribute('id', 'lesson_starttime');
$endTime = $form->getField('ordles_lesson_endtime');
$endTime->addFieldTagAttribute('id', 'lesson_endtime');
$submit = $form->getField('submit');
$submit->addFieldTagAttribute('class', 'btn btn--secondary btn--small btn--wide');
?>
<div id="loaderCalendar" class="calendar-loader" style="display: none;">
    <div class="loader"></div>
</div>
<div class="calendar-view scheduled-lesson-popup">
    <div class="calendar-view__head">
        <div class="row ">
            <div class="col-sm-5">
                <h4><?php echo $lesson['teacher_first_name'] . ' ' . $lesson['teacher_last_name'] . " " . Label::getLabel('LBL_CALENDAR'); ?></h4>
            </div>
            <div class="col-sm-7 d-flex align-items-center justify-content-sm-end justify-content-start">
                <div class="cal-status">
                    <span class="ml-0 box-hint disabled-box">&nbsp;</span>
                    <p><?php echo Label::getLabel('LBL_NOT_AVAILABLE'); ?></p>
                </div>
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
    <div id='calendar-container'>
        <div id='d_calendar'></div>
    </div>
</div>
<div class="tooltipevent-wrapper-js d-none">
    <div class="tooltipevent">
        <div class="booking-view -align-center">
            <h3 class="-display-inline"><?php echo ucwords($lesson['teacher_first_name'] . ' ' . $lesson['teacher_last_name']); ?></h3>
            <span class="flag -display-inline"><img style="height: 22px;border: 1px solid #000;" src="<?php echo CONF_WEBROOT_FRONTEND . 'flags/' . strtolower($lesson['teacher_country_code']) . '.svg'; ?>" /></span>
            <?php
            echo $form->getFormTag();
            echo $form->getFieldHTML('ordles_id');
            echo $startTime->getHTML();
            echo $endTime->getHTML();
            ?>
            <div class="inline-list">
                <div class="inline-list__value highlight tooltipevent-time-js">
                    <div>
                        <strong><?php echo Label::getLabel("LBL_Date") . ' : '; ?></strong>
                        <span class="displayEventDate">{{displayEventDate}}</span>
                    </div>
                    <div>
                        <strong><?php echo Label::getLabel("LBL_Time") . ' : '; ?></strong>
                        <span class="displayEventTime">{{displayEventTime}}</span>
                    </div>
                </div>
            </div>
            <div class="-align-center">
                <?php echo $submit->getHTML(); ?>
            </div>
            </form>
            <a onclick="$('.tooltipevent-wrapper-js').addClass('d-none');" href="javascript:;" class="-link-close"></a>
        </div>
    </div>
</div>
<?php echo $form->getExternalJS(); ?>
<script>
    var checkSlotAvailabiltAjaxRun = false;
    var fecal = new FatEventCalendar(<?php echo $lesson['ordles_teacher_id']; ?>, '<?php echo MyDate::getOffset($siteTimezone); ?>');
    fecal.WeeklyBookingCalendar('<?php echo MyDate::formatDate(date('Y-m-d H:i:s')); ?>', '<?php echo $lesson['ordles_duration']; ?>', <?php echo $teacherBookingBefore; ?>, '<?php echo $subStartDate; ?>', '<?php echo $subdays; ?>');
</script>