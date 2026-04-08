/* global fcom */

$(document).ready(function () {
    search(document.frmLessonStatus);
    $('input[name="user"]').autocomplete({
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
            $("input[name='user']").val(item.name);
        }
    });
    $("input[name='user']").keyup(function () {
        $("input[name='user_id']").val('');
    });
});
(function () {
    search = function (form) {
        fcom.ajax(fcom.makeUrl('LessonStats', 'search'), fcom.frmData(form), function (response) {
            $("#listing").html(response);
        });
    };
    clearSearch = function () {
        document.frmLessonStatus.user_id.value = '';
        document.frmLessonStatus.reset();
        search(document.frmLessonStatus);
    };
    goToSearchPage = function (pageno) {
        var frm = document.logPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    goToViewNextPage = function (pageno) {
        var frm = document.viewLogPaging;
        $(frm.pageno).val(pageno);
        getLogData(fcom.frmData(frm));
    };
    viewLogs = function (userId, reportType, pageno) {
        let data = fcom.frmData(document.logPaging);
        data += '&user_id=' + userId + "&reportType=" + reportType + '&pageno=' + pageno;
        getLogData(data);
    };
    getLogData = function (data) {
        fcom.ajax(fcom.makeUrl('LessonStats', 'viewLogs'), data, function (response) {
            $.facebox(response, 'xlargebox');
        });
    };
    exportReport = function () {
        document.viewLogPaging.action = fcom.makeUrl('LessonStats', 'export');
        document.viewLogPaging.method = "post";
        document.viewLogPaging.submit();
    };
})();