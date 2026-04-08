/* global fcom */
$(document).ready(function () {
    search(document.frmSubsSearch);
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.frmSubsSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    reloadList = function () {
        search(document.frmSubsSearchPaging);
    };
    search = function (form) {
        fcom.ajax(fcom.makeUrl('Subscriptions', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    viewLesson = function (ordlesId) {
        fcom.ajax(fcom.makeUrl('Subscriptions', 'view'), {ordlesId: ordlesId}, function (res) {
            $.facebox(res, 'facebox-medium');
        });
    };
    clearSearch = function () {
        document.frmSubsSearch.reset();
        $("input[name='ordles_tlang_id']").val('');
        search(document.frmSubsSearch);
    };
})();
