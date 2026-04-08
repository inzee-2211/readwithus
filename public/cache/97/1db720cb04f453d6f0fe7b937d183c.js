/* global fcom */

$(document).ready(function () {
    searchGiftcards(document.frmGiftcardSearch);
});
(function () {
    goToSearchPage = function (pageno) {
        var frm = document.frmGiftcardSearchPaging;
        $(frm.pageno).val(pageno);
        searchGiftcards(frm);
    }
    searchGiftcards = function (form) {
        var dv = $('#ordersListing');
        fcom.ajax(fcom.makeUrl('Giftcards', 'search'), fcom.frmData(form), function (res) {
            dv.html(res);
        });
    };
    viewGiftCard = function (ordgiftId) {
        fcom.ajax(fcom.makeUrl('Giftcards', 'view'), {ordgiftId: ordgiftId}, function (response) {
            $.facebox(response);
        });
    };
    reloadOrderList = function () {
        searchGiftcards(document.frmGiftcardSearchPaging);
    };
    clearSearch = function () {
        document.frmGiftcardSearch.reset();
        searchGiftcards(document.frmGiftcardSearch);
    };
})();
