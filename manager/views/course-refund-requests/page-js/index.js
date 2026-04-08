/* global fcom, langLbl */
$(document).ready(function () {
    search(document.requestSearch);
    $('input[name=\'learner\']').autocomplete({
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
            }, {process: false});
        },
        'select': function (item) {
            $("input[name='learner_id']").val(item.value);
            $("input[name='learner']").val(item.name);
        }
    });
    $('input[name=\'learner\']').keyup(function () {
        $('input[name=\'learner_id\']').val('');
    });
});
(function () {
    goToSearchPage = function (page) {
        var frm = document.frmPaging;
        $(frm.page).val(page);
        search(frm);
    };
    search = function (form) {
        fcom.ajax(fcom.makeUrl('CourseRefundRequests', 'search'), fcom.frmData(form), function (response) {
            $('#listing').html(response);
        });
    };
    clearSearch = function () {
        document.requestSearch.reset();
        $('input[name="learner_id"]').val('');
        search(document.requestSearch);
    };
    view = function (reqId) {
        fcom.ajax(fcom.makeUrl('CourseRefundRequests', 'view', [reqId]), '', function (response) {
            $.facebox(response, 'faceboxWidth');
        });
    };
    updateStatus = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CourseRefundRequests', 'updateStatus'), fcom.frmData(frm), function (res) {
            $(document).trigger('close.facebox');
            search(document.requestSearch);
        });
    };
    showHideCommentBox = function (val) {
        if (val == REFUND_DECLINED) {
            $('#remarkField').show();
        } else {
            $('#remarkField').hide();
        }
    };
    changeStatusForm = function (reqId) {
        fcom.ajax(fcom.makeUrl('CourseRefundRequests', 'form', [reqId]), '', function (response) {
            $.facebox(response, 'faceboxWidth');
            showHideCommentBox();
        });
    };
})();	