/* global fcom, langLbl, confWebDashUrl, SCHEDULED */
(function () {
    scheduleForm = function (lessonId) {
        fcom.process();
        fcom.ajax(fcom.makeUrl('Lessons', 'scheduleForm', '', confWebDashUrl), { lessonId: lessonId }, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    scheduleSetup = function (frm) {
        fcom.process();
        fcom.ajax(fcom.makeUrl('Lessons', 'scheduleSetup', '', confWebDashUrl), fcom.frmData(frm), function (response) {
            window.location.reload();
        });
    };
})();