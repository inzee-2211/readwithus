/* global fcom, langLbl, weekDayNames, monthNames, layoutDirection */
var dayShortNames = weekDayNames.shortName.slice(0);
var lastValue = dayShortNames[6];
dayShortNames.pop();
dayShortNames.unshift(lastValue);
defaultsValue = {
    monthNames: monthNames.longName,
    monthNamesShort: monthNames.shortName,
    dayNamesMin: dayShortNames,
    dayNamesShort: dayShortNames,
    currentText: langLbl.today,
    closeText: langLbl.done,
    prevText: langLbl.prev,
    nextText: langLbl.next,
    isRTL: (layoutDirection == 'rtl')
};
$.datepicker.regional[''] = $.extend(true, {}, defaultsValue);
$.datepicker.setDefaults($.datepicker.regional['']);
(function () {
    goToSearchPage = function (pageno) {
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    search = function (frm) {
        fcom.ajax(fcom.makeUrl('Wallet', 'search'), fcom.frmData(frm), function (response) {
            $("#listing").html(response);
        });
    };
    clearSearch = function () {
        document.txnSrchFrm.reset();
        search(document.txnSrchFrm);
    };
    closeForm = function () {
        $(document).trigger('close.facebox');
    }
    redeemGiftcardForm = function () {
        fcom.ajax(fcom.makeUrl('Wallet', 'giftcard-redeem-form'), '', function (res) {
            $.facebox(res, 'facebox-small');
        });
    };
    addMoney = function () {
        fcom.ajax(fcom.makeUrl('Wallet', 'addMoney'), '', function (response) {
            $.facebox(response, 'facebox-small');
        });
    };
    setupAddMoney = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.process();
        frm.submit.disabled = true;
        fcom.updateWithAjax(fcom.makeUrl('Wallet', 'setupAddMoney'), fcom.frmData(frm), function (response) {
            if (response.redirectUrl) {
                setTimeout(function () {
                    window.location.href = response.redirectUrl
                }, 1000);
                return;
            }
            if (response.status != 1) {
                frm.submit.disabled = false;
            }
        }, { failed: true });
    };
    giftcardRedeem = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Wallet', 'reedemGiftcard'), fcom.frmData(frm), function (res) {
            $(document).trigger('close.facebox');
            document.location.reload();
        });
    };
})();