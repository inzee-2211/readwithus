/* global fcom */
$(document).ready(function () {
    searchGdprRequests(document.frmSrch);
});
(function () {
    searchGdprRequests = function (frm) {
        fcom.ajax(fcom.makeUrl('GdprRequests', 'search'), fcom.frmData(frm), function (t) {
            $('#listItems').html(t);
        });
    };
    reloadList = function () {
        searchGdprRequests(document.frmSrch);
    };
    view = function (requestId) {
        fcom.ajax(fcom.makeUrl('GdprRequests', 'view'), {id: requestId}, function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    updateStatus = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('GdprRequests', 'updateStatus'), fcom.frmData(frm), function (t) {
            $.facebox.close();
            searchGdprRequests(document.frmSrch);
        });
    };
    clearSearch = function () {
        document.frmSrch.reset();
        searchGdprRequests(document.frmSrch);
    };
    goToSearchPage = function (page) {
        var frm = document.frmSrch;
        $(frm.page).val(page);
        searchGdprRequests(frm);
    };
})();