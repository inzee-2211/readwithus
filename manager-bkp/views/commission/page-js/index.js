/* global fcom, langLbl */
$(document).ready(function () {
    search(document.commSearch);
});
(function () {
    goToSearchPage = function (page) {
        var frm = document.frmCommPaging;
        $(frm.page).val(page);
        search(frm);
    };
    search = function (form) {
        fcom.ajax(fcom.makeUrl('Commission', 'search'), fcom.frmData(form), function (response) {
            $('#listing').html(response);
        });
    };
    commissionForm = function (commissionId) {
        fcom.ajax(fcom.makeUrl('Commission', 'form', [commissionId]), '', function (response) {
            $.facebox(response, 'faceboxWidth');
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Commission', 'setup'), fcom.frmData(frm), function (res) {
            $(document).trigger('close.facebox');
            search(document.commSearch);
        });
    };
    viewHistory = function (userId) {
        fcom.ajax(fcom.makeUrl('Commission', 'viewHistory', [userId]), '', function (response) {
            $.facebox(response, 'faceboxWidth');
        });
    };
    clearSearch = function () {
        document.commSearch.reset();
        search(document.commSearch);
    };
})();	