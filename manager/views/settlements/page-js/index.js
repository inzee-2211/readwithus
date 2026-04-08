/* global fcom, langLbl */
$(document).ready(function () {
    search(document.frmSaleReport);
});
(function () {
    search = function (form) {
        fcom.ajax(fcom.makeUrl('Settlements', 'search'), fcom.frmData(form), function (res) {
            $('#listing').html(res);
        });
    };
    goToSearchPage = function (page) {
        var form = document.frmSaleReportPaging;
        $(form.pageno).val(page);
        search(form);
    };
    clearSearch = function () {
        document.frmSaleReport.reset();
        $("input[name='slstat_teacher_id']").val('');
        search(document.frmSaleReport);
    };
    regenerate = function () {
        fcom.updateWithAjax(fcom.makeUrl('SalesReport', 'regenerate'), '', function (res) {
            $("#regendatedtime").text(res.regendatedtime);
            search(document.frmSaleReport);
        });
    };
})();