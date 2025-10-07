/* global fcom, langLbl */
$(document).ready(function () {
    searchBlogComments(document.frmSearch);
});
(function () {
    goToSearchPage = function (page) {
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        searchBlogComments(frm);
    }
    reloadList = function () {
        searchBlogComments(document.frmSearchPaging);
    }
    view = function (id) {
        fcom.ajax(fcom.makeUrl('BlogComments', 'view', [id]), '', function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    updateStatus = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogComments', 'updateStatus'), fcom.frmData(frm), function (res) {
            reloadList();
            $(document).trigger('close.facebox');
        });
    };
    searchBlogComments = function (form) {
        fcom.ajax(fcom.makeUrl('BlogComments', 'search'), fcom.frmData(form), function (res) {
            $("#listing").html(res);
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogComments', 'deleteRecord'), {id: id}, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        searchBlogComments(document.frmSearch);
    };
})();