/* global fcom, currentPage */
$(document).ready(function () {
    search(document.frmWalletSearch);
});
(function () {
    goToSearchPage = function (pageno) {
        var frm = document.frmWalletSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    search = function (form) {
        var dv = $('#ordersListing');
        fcom.ajax(fcom.makeUrl('Wallet', 'search'), fcom.frmData(form), function (res) {
            dv.html(res);
        });
    };
    reloadOrderList = function () {
        search(document.frmWalletSearchPaging);
    };
    clearSearch = function () {
        document.frmWalletSearch.reset();
        search(document.frmWalletSearch);
    };
})();
