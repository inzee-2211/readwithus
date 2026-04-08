/* global fcom, langLbl */
(function () {
  
    cancelSetup = function (form) {
        if (!$(form).validate()) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Classes', 'cancelSetup'), fcom.frmData(form), function (response) {
            reloadPage(3000);
        });
    };
    feedbackForm = function (classId) {
        fcom.ajax(fcom.makeUrl('Classes', 'feedbackForm'), {classId: classId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    feedbackSetup = function (frm) {
        fcom.ajax(fcom.makeUrl('Classes', 'feedbackSetup'), fcom.frmData(frm), function (response) {
            reloadPage(3000);
        });
    };
})();