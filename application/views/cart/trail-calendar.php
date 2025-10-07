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
                <div class="cal-status"><span class="ml-0 box-hint disabled-box">&nbsp;</span><p><?php echo Label::getLabel('LBL_NOT_AVAILABLE'); ?></p></div>
                <div class="cal-status"><span class="box-hint available-box">&nbsp;</span><p><?php echo Label::getLabel('LBL_AVAILABLE'); ?></p></div>
                <div class="cal-status"><span class="box-hint booked-box">&nbsp;</span><p><?php echo Label::getLabel('LBL_BOOKED'); ?></p></div>
            </div>
        </div>
    </div>
    <div id='calendar-container'>
        <div id='d_calendarfree_trial'></div>
    </div>
</div>
<div class="tooltipevent-wrapper-js d-none">
    <div class="tooltipevent">
        <div class="booking-view -align-center">
            <div class="booking__head align-center">
                <h3 class="-display-inline"><?php echo ucwords($teacher['user_first_name'] . " " . $teacher['user_last_name']); ?></h3>
                <span class="flag -display-inline"><img src="<?php echo CONF_WEBROOT_FRONTEND . 'flags/' . strtolower($teacher['user_country_code']) . '.svg'; ?>" style="height: 22px;border: 1px solid #000;" /></span>
            </div>
            <div class="booking__body">
                <div class="inline-list">
                    <div class="inline-list__value highlight tooltipevent-time-js">
                        <div>
                            <strong><?php echo Label::getLabel("LBL_DATE") . ' : '; ?></strong>
                            <span class="displayEventDate"></span>
                        </div>
                        <div>
                            <strong><?php echo Label::getLabel("LBL_TIME") . ' : '; ?></strong>
                            <span class="displayEventTime"></span>
                        </div>
                    </div>
                </div>
                <div class="-gap-10"></div>
                <div class="-align-center">
                    <?php
                    $form->addFormTagAttribute('class', 'd-none');
                    echo $form->getFormHtml();
                    ?>
                    <a href="javascript:void(0);" onClick="cart.addLesson();" class="btn btn--secondary btn--small book-freetrial-js btn--wide"><?php echo Label::getLabel('LBL_BOOK_LESSON!'); ?></a>
                </div>
                <a onclick="$('.tooltipevent-wrapper-js').addClass('d-none');" href="javascript:;" class="-link-close"></a>
            </div>
        </div>
    </div>
</div>
<script>
    cart.prop.ordles_duration = '<?php echo $duration; ?>';
    cart.prop.ordles_teacher_id = '<?php echo $teacher['user_id']; ?>';
    cart.prop.ordles_tlang_id = -1;
    var fecal = new FatEventCalendar('<?php echo $teacher['user_id']; ?>', '<?php echo MyDate::getOffset($siteTimezone); ?>');
    fecal.AvailaibilityCalendar('<?php echo MyDate::formatDate(date('Y-m-d H:i:s')); ?>', '<?php echo $duration; ?>', '<?php echo $teacher['user_book_before']; ?>', true);
</script>