/* global fcom */
$(document).ready(function () {
    search(document.frmSearch);
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.frmCourseSearchPaging;
        $(frm.page).val(pageno);
        search(frm);
    };
    search = function (form) {
        var data = data = fcom.frmData(form);
        fcom.ajax(fcom.makeUrl('CourseOrders', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        search(document.frmSearch);
    };
})();
