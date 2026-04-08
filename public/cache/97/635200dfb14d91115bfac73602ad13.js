/* global fcom */
$("document").ready(function () {
    var frm = document.frmBibleSrch;
    searchBible(frm);
});
(function () {
    searchBible = function (frm) {
        fcom.updateWithAjax(fcom.makeUrl('Bible', 'search'), fcom.frmData(frm), function (ans) {
            if ($('#total_records').length > 0) {
                $('#total_records').html(ans.totalRecords);
            }
            if ($('#start_record').length > 0) {
                $('#start_record').html(ans.startRecord);
            }
            if ($('#end_record').length > 0) {
                $('#end_record').html(ans.endRecord);
            }
            if (ans.totalRecords == 0) {
                $('.pagCount').html('');
            }
            $("#bibleListingContainer").html(ans.html);
        });
    };
    goToSearchPage = function (page) {
        var frm = document.frmBibleSearchPaging;
        $(frm.page).val(page);
        searchBible(frm);
    };
})();