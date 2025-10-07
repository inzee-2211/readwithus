/* global fcom */

$(document).ready(function () {
    searchThreadMessages(document.frmMessageSrch);
});
(function () {
    var dv = '#messageListing';
    searchThreadMessages = function (frm, append) {
        if (typeof append == undefined || append == null) {
            append = 0;
        }
        fcom.updateWithAjax(fcom.makeUrl('Messages', 'messageSearch'), fcom.frmData(frm), function (ans) {
            if (append == 1) {
                $(dv).prepend(ans.html);
            } else {
                $(dv).html(ans.html);
            }
            $("#loadMoreBtnDiv").html(ans.loadMoreBtnHtml);
        });
    };
    goToLoadPrevious = function (page) {
        var frm = document.frmMessageSrch;
        $(frm.page).val(page);
        searchThreadMessages(frm, 1);
    };
    sendMessage = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Messages', 'sendMessage'), fcom.frmData(frm), function (t) {
            window.location.href = fcom.makeUrl('Messages');
        });
    };
})();