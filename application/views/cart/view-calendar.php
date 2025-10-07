<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
/* conver hours to minutes */
$bookBefore = $teacher['user_book_before'] * 60;
$steps = Cart::getSteps();
?>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="cart.priceSlabs('<?php echo $teacher['user_id']; ?>', '<?php echo $tlangId; ?>', '<?php echo $duration; ?>', '<?php echo $quantity; ?>', '<?php echo $ordlesType; ?>'); $('body > .tooltipevent').remove();" class=" btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <h4><?php echo Label::getLabel('LBL_SCHEDULE_YOUR_LESSONS'); ?></h4>
        <div class="step-nav">
            <ul>
                <?php foreach ($steps as $key => $step) { ?>
                    <li class="step-nav_item <?php echo in_array($key, $stepProcessing) ? 'is-process' : ''; ?> <?php echo in_array($key, $stepCompleted) ? 'is-completed' : ''; ?> ">
                        <a href="javascript:void(0);"><?php echo $step; ?></a>
                        <?php if (in_array($key, $stepCompleted)) { ?><span class="step-icon"></span><?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div id="loaderCalendar" class="calendar-loader" style="display: none;">
            <div class="loader"></div>
        </div>
        <div class="calendar-view scheduled-lesson-popup">
            <div class="calendar-view__head">
                <div class="row ">
                    <div class="col-sm-7 d-flex align-items-center justify-content-sm-start justify-content-start">
                        <div class="cal-status"><span class="ml-0 box-hint disabled-box">&nbsp;</span><p><?php echo Label::getLabel('LBL_NOT_AVAILABLE'); ?></p></div>
                        <div class="cal-status"><span class="box-hint available-box">&nbsp;</span><p><?php echo Label::getLabel('LBL_AVAILABLE'); ?></p></div>
                        <div class="cal-status"><span class="box-hint booked-box">&nbsp;</span><p><?php echo Label::getLabel('LBL_BOOKED'); ?></p></div>
                    </div>
                    <?php if ($quantity > 1) { ?>
                        <div class="col-sm-5 justify-content-sm-end d-flex align-items-center">
                            <div class="drop-action">
                                <div class="drop-action__trigger drop-trigger-js" id="lesson-drop-action">
                                    <span class="drop-action__label unscheduled-lessson-js"><?php echo $quantity; ?></span>
                                    <span class="drop-action__value"><?php echo Label::getLabel('LBL_LESSONS_TO_SCHEDULE'); ?></span>
                                </div>
                                <div class="drop-action__target drop-target-js" style="display: none;">
                                    <div class="drop-action__target-box">
                                        <div class="numbers-list" id="cal-lesson-list">
                                            <?php for ($i = 1; $i <= $quantity; $i++) { ?>
                                                <span class="numbers-list__item">
                                                    <span class="number-list__value"><?php echo Label::getLabel('LBL_TO_SCHEDULE'); ?></span>
                                                    <span class="is-delete d-none" data-id=""></span>
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div id='calendar-container'>
                <div id='booking-calendar' class="learner_subscription_calendar"></div>
            </div>
        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__left">
            <div class="teacher-profile">
                <div class="teacher__media">
                    <div class="avtar avtar-md">
                        <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_SMALL]) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] ?>">
                    </div>
                </div>
                <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
            </div>
            <div class="step-breadcrumb">
                <ul>
                    <li><a href="javascript:void(0);"><?php echo $tlangName; ?>, <?php echo sprintf(Label::getLabel('LBL_%s_Mins'), $duration); ?></a></li>
                    <li><a href="javascript:void(0);"><?php echo sprintf(Label::getLabel('LBL_%s'), ($ordlesType == Lesson::TYPE_REGULAR) ? Label::getLabel('LBL_REGULAR_LESSONS') : Label::getLabel('LBL_SUBSCRIPTION_LESSONS')); ?>(<?php echo $quantity; ?>)</a></li>
                </ul>
            </div>
        </div>
        <div class="box-foot__right">
            <?php if ($ordlesType == Lesson::TYPE_REGULAR) { ?>
                <a href="javascript:void(0);" onclick="cart.addLesson();" class="btn btn--primary color-white" id="lesson-checkout-btn-js"><?php echo LabeL::getLabel('LBL_NEXT'); ?></a>
            <?php } else { ?>
                <a href="javascript:void(0);" class="btn btn--primary color-white btn--disabled " id="subcrip-checkout-btn-js"><?php echo LabeL::getLabel('LBL_NEXT'); ?></a>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    var labelToSchedule = '<?php echo CommonHelper::htmlEntitiesDecode(Label::getLabel('LBL_TO_SCHEDULE')); ?>';
    var labelAllScheduled = '<?php echo CommonHelper::htmlEntitiesDecode(Label::getLabel('LBL_ALL_SCHEDULED')); ?>';
    var TYPE_SUBCRIP = '<?php echo Lesson::TYPE_SUBCRIP ?>';
    var sub = (TYPE_SUBCRIP == cart.prop.ordles_type) ? 1 : 0;
    var fecal = new FatEventCalendar(<?php echo $teacher['user_id']; ?>, '<?php echo MyDate::getOffset($siteTimezone); ?>');
    fecal.bookingCalendar('<?php echo MyDate::formatDate(date('Y-m-d H:i:s')); ?>', '<?php echo $duration; ?>', '<?php echo $bookBefore; ?>', '<?php echo $calendarDays; ?>', sub);
    if (cart.prop.ordles_type == TYPE_SUBCRIP && Object.keys(cart.prop.slots).length == cart.prop.ordles_quantity) {
        $('#subcrip-checkout-btn-js').removeClass('btn--disabled').attr('onclick', ' cart.addSubscription();');
    }
    $(".drop-trigger-js").click(function () {
        $(".drop-target-js").slideToggle();
    });
    $('.is-delete').click(function () {
        let eventId = $(this).attr('data-id');
        if (cart.prop.slots[eventId]) {
            calendarEvent = calendar.getEventById(eventId);
            if (calendarEvent) {
                calendarEvent.remove();
                delete cart.prop.slots[eventId];
                if (cart.prop.ordles_type == TYPE_SUBCRIP) {
                    $('#subcrip-checkout-btn-js').addClass('btn--disabled').removeAttr('onclick');
                }
                updateLessonList(calendar);
            }

        }
    });
</script>