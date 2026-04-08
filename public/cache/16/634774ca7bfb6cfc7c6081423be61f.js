/* global fcom, langLbl */
$(function () {
    var currentPage = 1;
    var dv = '#listItems';
    searchGroupClasses = function (frm) {
        fcom.ajax(fcom.makeUrl('GroupClasses', 'search'), fcom.frmData(frm), function (t) {
            $(dv).html(t);
        });
    };
    viewLearners = function (id) {
        fcom.ajax(fcom.makeUrl('GroupClasses', 'learners', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    form = function (id) {
        fcom.ajax(fcom.makeUrl('GroupClasses', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
            jQuery('#grpcls_start_datetime,#grpcls_end_datetime').each(function () {
                $(this).datetimepicker({format: 'Y-m-d H:i'});
            });
        });
    };
    removeClass = function (id) {
        if (!confirm(langLbl.confirmRemove)) {
            return;
        }
        fcom.ajax(fcom.makeUrl('GroupClasses', 'removeClass', [id]), '', function (t) {
            searchGroupClasses(document.frmSrch);
        });
    };
    cancelClass = function (id) {
        if (confirm(langLbl.confirmCancel)) {
            fcom.ajax(fcom.makeUrl('GroupClasses', 'cancelClass', [id]), '', function (t) {
                searchGroupClasses(document.frmSrch);
            });
        }
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.updateWithAjax(fcom.makeUrl('GroupClasses', 'setup'), fcom.frmData(frm), function (t) {
            searchGroupClasses(document.frmSrch);
            $(document).trigger('close.facebox');
        });
    };
    clearSearch = function () {
        document.frmSrch.reset();
        document.frmSrch.teacher_id.value = '';
        searchGroupClasses(document.frmSrch);
    };
    goToSearchPage = function (page) {
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        searchGroupClasses(frm);
    };
    $(document).on('click', function () {
        $('.autoSuggest').empty();
    });
    $('input[name=\'teacher\']').autocomplete({
        'source': function (request, response) {
            fcom.updateWithAjax(fcom.makeUrl('Users', 'AutoCompleteJson'), { keyword: request
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
            $("input[name='teacher_id']").val(item.value);
            $("input[name='teacher']").val(item.name);
        }
    });
    $('input[name=\'teacher\']').keyup(function () {
        $('input[name=\'teacher_id\']').val('');
    });
    searchGroupClasses(document.frmSrch);
});