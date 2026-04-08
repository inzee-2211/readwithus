/* global fcom, langLbl */
$(document).ready(function () {
    searchCurrency(document.frmCurrencySearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    reloadList = function () {
        searchCurrency(document.frmCurrencySearch);
    };
    searchCurrency = function (form) {
        fcom.ajax(fcom.makeUrl('CurrencyManagement', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    editCurrencyForm = function (currencyId) {
        currencyForm(currencyId);
    };
    currencyForm = function (currencyId) {
        fcom.ajax(fcom.makeUrl('CurrencyManagement', 'form', [currencyId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupCurrency = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CurrencyManagement', 'setup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                editCurrencyLangForm(t.currencyId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    }
    editCurrencyLangForm = function (currencyId, langId) {
        fcom.ajax(fcom.makeUrl('CurrencyManagement', 'langForm', [currencyId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupLangCurrency = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CurrencyManagement', 'langSetup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                editCurrencyLangForm(t.currencyId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            return;
        }
        var currencyId = parseInt(obj.id);
        var data = 'currencyId=' + currencyId + '&status=' + active;
        fcom.ajax(fcom.makeUrl('CurrencyManagement', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + currencyId).attr('onclick', 'inactiveStatus(this)');
            setTimeout(function () {
                reloadList();
            }, 1000);
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            return;
        }
        var currencyId = parseInt(obj.id);
        var data = 'currencyId=' + currencyId + '&status=' + inActive;
        fcom.ajax(fcom.makeUrl('CurrencyManagement', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + currencyId).attr('onclick', 'activeStatus(this)');
            setTimeout(function () {
                reloadList();
            }, 1000);
        });
    };
})();	