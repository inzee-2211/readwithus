/* global fcom */

$(document).ready(function () {
    search(document.frmSrch);
});
(function () {
    search = function (form) {
        fcom.ajax(fcom.makeUrl('TeacherRequests', 'search'), fcom.frmData(form), function (res) {
            $("#listing").html(res);
        });
    };
    clearSearch = function () {
        document.frmSrch.reset();
        search(document.frmSrch);
    };
    view = function (utrequestId) {
        fcom.ajax(fcom.makeUrl('TeacherRequests', 'view', [utrequestId]), '', function (response) {
            $.facebox(response);
        });
    };
    changeStatusForm = function (utrequestId) {
        fcom.ajax(fcom.makeUrl('TeacherRequests', 'changeStatusForm', [utrequestId]), '', function (response) {
            $.facebox(response);
        });
    };
    showHideCommentBox = function (val) {
        if (val == STATUS_CANCELLED) {
            $('#comments').parents('.row').removeClass('hide');
        } else {
            $('#comments').parents('.row').addClass('hide');
        }
    };
    updateStatus = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('TeacherRequests', 'updateStatus'), fcom.frmData(frm), function (res) {
            search(document.frmSrch);
            $(document).trigger('close.facebox');
        });
    };
    searchQualifications = function (userId) {
        fcom.ajax(fcom.makeUrl('TeacherRequests', 'searchQualifications', [userId]), '', function (response) {
            $.facebox(response);
        });
    };
    goToSearchPage = function (page) {
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        search(frm);
    };
})();