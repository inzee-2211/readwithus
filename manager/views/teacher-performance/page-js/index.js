/* global fcom, SITE_ROOT_URL */
$(document).ready(function () {
    search(document.frmTeacherPerformance);
    $("input[name='keyword']").autocomplete({
        'source': function (request, response) {
            fcom.updateWithAjax(fcom.makeUrl('Users', 'AutoCompleteJson'), {
                keyword: request
            }, function (result) {
                response($.map(result.data, function (item) {
                    return {
                        label: escapeHtml(item['full_name'] + ' (' + item['user_email'] + ')'),
                        value: item['user_id'], name: item['full_name']
                    };
                }));
            });
        },
        'select': function (item) {
            $("input[name='user_id']").val(item.value);
            $("input[name='keyword']").val(item.name);
        }
    });
    $("input[name='keyword']").keyup(function () {
        $("input[name='user_id']").val('');
    });
});
(function () {
    search = function (frm) {
        fcom.ajax(fcom.makeUrl('TeacherPerformance', 'search'), fcom.frmData(frm), function (res) {
            $('#listing').html(res);
        });
    };
    clearSearch = function () {
        document.frmTeacherPerformance.reset();
        document.frmTeacherPerformance.user_id.value = '';
        search(document.frmTeacherPerformance);
    };
    goToSearchPage = function (pageno) {
        var frm = document.frmTeacherPerformancePaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
})();	