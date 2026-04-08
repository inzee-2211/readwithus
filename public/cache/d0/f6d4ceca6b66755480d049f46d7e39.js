/* global fcom, langLbl */
(function () {
    scheduleForm = function (lessonId) {
        fcom.ajax(fcom.makeUrl('Lessons', 'scheduleForm'), {lessonId: lessonId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    scheduleSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Lessons', 'scheduleSetup'), fcom.frmData(frm), function (response) {
            reloadPage(3000);
        });
    };
    rescheduleForm = function (lessonId) {
        fcom.ajax(fcom.makeUrl('Lessons', 'rescheduleForm'), {lessonId: lessonId}, function (response) {
            $.facebox(response, 'facebox-medium booking-calendar-pop-js');
        });
    };
    rescheduleSetup = function (frm) {
        var rescheduleReason = $('#reschedule-reason-js').val();
        if ($.trim(rescheduleReason) == "") {
            $('.booking-calendar-pop-js').animate({scrollTop: 0}, 500);
            return false;
        }
        if (!$(frm).validate()) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Lessons', 'rescheduleSetup'), fcom.frmData(frm), function (response) {
            reloadPage(3000);
        });
    };
    cancelForm = function (lessonId) {
        fcom.ajax(fcom.makeUrl('Lessons', 'cancelForm'), {lessonId: lessonId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    cancelSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Lessons', 'cancelSetup'), fcom.frmData(frm), function (response) {
            reloadPage(3000);
        });
    };
    feedbackForm = function (lessonId) {
        fcom.ajax(fcom.makeUrl('Lessons', 'feedbackForm'), {lessonId: lessonId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    feedbackSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Lessons', 'feedbackSetup'), fcom.frmData(frm), function (response) {
            reloadPage(300);
        });
    };
    planListing = function (attachedPlanId) {
        fcom.ajax(fcom.makeUrl('Plans', 'search'), {attachedPlanId: attachedPlanId, listing_type: 1}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
})();