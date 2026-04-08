/* global fcom */
$(document).ready(function () {
    searchClass(document.frmClassSearch);
    $("input[name='ordcls_tlang']").autocomplete({
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
            $("input[name='ordcls_tlang_id']").val(item.value);
            $("input[name='ordcls_tlang']").val(item.name);
        }
    });
    $("input[name='ordcls_tlang']").keyup(function () {
        $("input[name='ordcls_tlang_id']").val('');
    });
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.frmClassSearchPaging;
        $(frm.pageno).val(pageno);
        searchClass(frm);
    };
    reloadList = function () {
        searchClass(document.frmClassSearchPaging);
    };
    searchClass = function (form) {
        var data = data = fcom.frmData(form);
        fcom.ajax(fcom.makeUrl('Classes', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };
    viewClass = function (ordclsId) {
        fcom.ajax(fcom.makeUrl('Classes', 'view'), {ordclsId: ordclsId}, function (res) {
            $.facebox(res, 'facebox-medium');
        });
    };
    clearSearch = function () {
        document.frmClassSearch.reset();
        $("input[name='ordcls_tlang_id']").val('');
        searchClass(document.frmClassSearch);
    };
})();
