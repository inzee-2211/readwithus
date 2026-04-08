/* global fcom, langLbl */
$(document).ready(function () {
    searchBlogContributions(document.frmSearch);
});
(function () {
    goToSearchPage = function (page) {
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        searchBlogContributions(frm);
    }
    reloadList = function () {
        searchBlogContributions(document.frmSearchPaging);
    }
    view = function (id) {
        fcom.ajax(fcom.makeUrl('BlogContributions', 'view', [id]), '', function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    updateStatus = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogContributions', 'updateStatus'), fcom.frmData(frm), function (res) {
            $(document).trigger('close.facebox');
            reloadList();
        });
    };
    searchBlogContributions = function (form) {
        fcom.ajax(fcom.makeUrl('BlogContributions', 'search'), fcom.frmData(form), function (res) {
            $("#listing").html(res);
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogContributions', 'deleteRecord'), {id: id}, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        searchBlogContributions(document.frmSearch);
    };
})();