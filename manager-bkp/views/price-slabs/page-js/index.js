/* global fcom */
$(document).ready(function () {
    searchPriceSlabs(document.priceSlabSearchFrm);
});
(function () {
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.priceSlabPagingForm;
        $(frm.pageno).val(pageno);
        searchPriceSlabs(frm);
    }
    searchPriceSlabs = function (frm) {
        fcom.ajax(fcom.makeUrl('PriceSlabs', 'search'), fcom.frmData(frm), function (res) {
            $(dv).html(res);
        });
    };
    priceSlabForm = function (id) {
        fcom.ajax(fcom.makeUrl('PriceSlabs', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupPriceSlab = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('PriceSlabs', 'setup'), fcom.frmData(frm), function (t) {
            searchPriceSlabs(document.priceSlabPagingForm);
            $(document).trigger('close.facebox');
        });
    }
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var psId = parseInt(obj.id);
        var data = 'psId=' + psId + "&status=" + active;
        fcom.ajax(fcom.makeUrl('PriceSlabs', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + psId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var psId = parseInt(obj.id);
        var data = 'psId=' + psId + "&status=" + inActive;
        fcom.ajax(fcom.makeUrl('PriceSlabs', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + psId).attr('onclick', 'activeStatus(this)');
        });
    };
})();
