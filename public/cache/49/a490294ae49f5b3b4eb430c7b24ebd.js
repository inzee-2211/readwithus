/* global fcom */
$(document).ready(function () {
    searchLesson(document.frmLessonSearch);
    $("input[name='ordles_tlang']").autocomplete({
        'source': function (request, response) {
            fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'autoCompleteJson'), {
                keyword: request
            }, function (result) {
                response($.map(result.data, function (item) {
                    return {
                        label: escapeHtml(item['tlang_name']),
                        value: item['tlang_id'],
                        name: item['tlang_name']
                    };
                }));
            });
        },
        'select': function (item) {
            $("input[name='ordles_tlang_id']").val(item.value);
            $("input[name='ordles_tlang']").val(item.name);
        }
    });
    $("input[name='ordles_tlang']").keyup(function () {
        $("input[name='ordles_tlang_id']").val('');
    });
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.frmLessonSearchPaging;
        $(frm.pageno).val(pageno);
        searchLesson(frm);
    };
    reloadList = function () {
        searchLesson(document.frmLessonSearchPaging);
    };
    searchLesson = function (form) {
        fcom.ajax(fcom.makeUrl('Lessons', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    viewLesson = function (ordlesId) {
        fcom.ajax(fcom.makeUrl('Lessons', 'view'), {ordlesId: ordlesId}, function (res) {
            $.facebox(res, 'facebox-medium');
        });
    };
    clearSearch = function () {
        document.frmLessonSearch.reset();
        $("input[name='ordles_tlang_id']").val('');
        searchLesson(document.frmLessonSearch);
    };
})();
