/* global fcom */
$(document).ready(function () {
    searchUrls(document.frmSearch);
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.frmUrlSearchPaging;
        $(frm.pageno).val(pageno);
        searchUrls(frm);
    };
    reloadList = function () {
        searchUrls(document.frmUrlSearchPaging);
    };
    searchUrls = function (form) {
        fcom.ajax(fcom.makeUrl('UrlRewriting', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    urlForm = function (seourlId) {
        fcom.ajax(fcom.makeUrl('UrlRewriting', 'form'), {seourlId: seourlId}, function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('UrlRewriting', 'setup'), fcom.frmData(frm), function (t) {
            reloadList();
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        var data = 'id=' + id;
        fcom.updateWithAjax(fcom.makeUrl('UrlRewriting', 'deleteRecord'), data, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        searchUrls(document.frmSearch);
    };
})();	