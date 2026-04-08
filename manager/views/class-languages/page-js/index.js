/* global fcom, SITE_ROOT_URL */
$(document).ready(function () {
    search(document.frmClassLanguages);
    $("input[name='grpcls_tlang']").autocomplete({
        'source': function (request, response) {
            fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'autoCompleteJson'), { keyword: request
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
            $("input[name='grpcls_tlang_id']").val(item.value);
            $("input[name='grpcls_tlang']").val(item.name);
        }
    });
    $("input[name='grpcls_tlang']").keyup(function () {
        $("input[name='grpcls_tlang_id']").val('');
    });
});
(function () {
    search = function (frm) {
        fcom.ajax(fcom.makeUrl('ClassLanguages', 'search'), fcom.frmData(frm), function (res) {
            $('#listing').html(res);
        });
    };
    clearSearch = function () {
        document.frmClassLanguages.reset();
        document.frmClassLanguages.grpcls_tlang_id.value = '';
        search(document.frmClassLanguages);
    };
    goToSearchPage = function (pageno) {
        var frm = document.frmClassLanguagesPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    viewAll = function (teachlangId) {
        var newForm = $('<form>', {'method': 'POST', 'action': fcom.makeUrl('Classes'), 'target': '_top'});
        newForm.append($('<input>', {'name': 'ordcls_tlang_id', 'value': teachlangId, 'type': 'hidden'}));
        newForm.append($('<input>', {'name': 'order_payment_status', 'value': 1, 'type': 'hidden'}));
        newForm.appendTo('body');
        newForm.submit();
    };
})();	