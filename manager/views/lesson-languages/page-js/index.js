/* global fcom, SITE_ROOT_URL */
$(document).ready(function () {
    search(document.frmLessonLanguages);
    $("input[name='ordles_tlang']").autocomplete({
        'source': function (request, response) {
            fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'autoCompleteJson'), {
                keyword: request
            }, function (result) {
                response($.map(result.data, function (item) {
                    return {
                        label: escapeHtml(item['tlang_name']),
                        value: item['tlang_id'], name: item['tlang_name']
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
    search = function (frm) {
        fcom.ajax(fcom.makeUrl('LessonLanguages', 'search'), fcom.frmData(frm), function (res) {
            $('#listing').html(res);
        });
    };
    clearSearch = function () {
        document.frmLessonLanguages.reset();
        document.frmLessonLanguages.ordles_tlang_id.value = '';
        search(document.frmLessonLanguages);
    };
    goToSearchPage = function (pageno) {
        var frm = document.frmLessonLanguagesPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    viewAll = function (teachlangId) {
        var newForm = $('<form>', {'method': 'POST', 'action': fcom.makeUrl('Lessons'), 'target': '_top'});
        newForm.append($('<input>', {'name': 'ordles_tlang_id', 'value': teachlangId, 'type': 'hidden'}));
        newForm.append($('<input>', {'name': 'order_payment_status', 'value': 1, 'type': 'hidden'}));
        newForm.appendTo('body');
        newForm.submit();
    };
})();	