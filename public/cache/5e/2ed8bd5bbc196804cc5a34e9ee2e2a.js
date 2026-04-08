/* global fcom, langLbl */
$(function () {
    viewIssue = function (issueId) {
        fcom.ajax(fcom.makeUrl('Issues', 'view'), {issueId: issueId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    issueForm = function (recordId, recordType) {
        fcom.ajax(fcom.makeUrl('Issues', 'form'), {recordId: recordId, recordType: recordType, }, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    issueSetup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.ajax(fcom.makeUrl('Issues', 'setup'), fcom.frmData(frm), function (response) {
            $.facebox.close();
            reloadPage(3000);
        });
    };
    resolveForm = function (issueId) {
        fcom.ajax(fcom.makeUrl('Issues', 'resolve'), {issueId: issueId}, function (response) {
            $.facebox(response, 'facebox-medium issueDetailPopup');
        });
    };
    resolveSetup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        var action = fcom.makeUrl('Issues', 'resolveSetup');
        fcom.updateWithAjax(action, fcom.frmData(frm), function (response) {
            $.facebox.close();
            reloadPage(3000);
        });
    };
    escalate = function (issueId) {
        fcom.ajax(fcom.makeUrl('Issues', 'escalate'), {issueId: issueId}, function (response) {
            $.facebox(response, 'facebox-small');
        });
    };
    escalateSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var action = fcom.makeUrl('Issues', 'escalateSetup');
        fcom.updateWithAjax(action, fcom.frmData(frm), function (response) {
            $.facebox.close();
            reloadPage(3000);
        });
    };
});
/* global fcom */

$(function () {
    var dv = '#listItems';
    search = function (frm) {
        fcom.ajax(fcom.makeUrl('Issues', 'search'), fcom.frmData(frm), function (t) {
            $(dv).html(t);
        });
    };
    clearSearch = function () {
        document.frmSrch.reset();
        search(document.frmSrch);
    }
    goToSearchPage = function (pageno) {
        var frm = document.frmSearchPaging
        frm.pageno.value = pageno;
        search(frm);
    };
    search(document.frmSrch);
});
