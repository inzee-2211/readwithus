/* global fcom */
$(function () {
    var dv = '#listItems';
    search = function (frm) {
        fcom.ajax(fcom.makeUrl('Students', 'search'), fcom.frmData(frm), function (t) {
            $(dv).html(t);
        });
    };
    clearSearch = function () {
        document.frmSrch.reset();
        search(document.frmSrch);
    };
    goToSearchPage = function (page) {
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(page);
        search(frm);
    };
    offerForm = function (learnerId) {
        fcom.ajax(fcom.makeUrl('Students', 'offerForm'), {learnerId: learnerId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    setupOfferPrice = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.updateWithAjax(fcom.makeUrl('Students', 'setupOfferPrice'), fcom.frmData(frm), function (res) {
            search(document.frmSearchPaging);
            $.facebox.close();
        });
    };
    messageForm = function (id) {
        fcom.ajax(fcom.makeUrl('Students', 'messageForm', [id]), '', function (t) {
            $.facebox(t, 'facebox-medium');
        });
    };
    messageSetup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.updateWithAjax(fcom.makeUrl('Students', 'messageSetup'), fcom.frmData(frm), function (t) {
            search(document.frmSrch);
            $.facebox.close();
        });
    };
    search(document.frmSrch);
});