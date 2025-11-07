/* global fcom, langLbl */
$(document).ready(function () {
    search(document.frmReqSearch);
});
(function () {
    goToSearchPage = function (page) {
        var frm = document.frmReqSearchPaging;
        $(frm.page).val(page);
        search(frm);
    };

    searchRequest = function (form) {
        if (!$(form).validate()) {
            return;
        }
        search(form);
    };
    search = function (form) {
        fcom.ajax(fcom.makeUrl('WithdrawRequests', 'search'), fcom.frmData(form), function (res) {
            $('#listing').html(res);
        });
    };
    updateStatus = function (id, status, statusName) {
        var data = 'id=' + id + '&status=' + status;
        if (confirm(langLbl.DoYouWantTo + ' ' + statusName + ' ' + langLbl.theRequest)) {
            fcom.updateWithAjax(fcom.makeUrl('WithdrawRequests', 'updateStatus'), data, function (t) {
                document.frmReqSearch.page.value = document.frmReqSearchPaging.page.value;
                search(document.frmReqSearch);
            });
        }
    };
    clearSearch = function () {
        document.frmReqSearch.reset();
        searchRequest(document.frmReqSearch);
    };
})();
