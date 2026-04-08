/* global fcom, langLbl, dv */
$(document).ready(function () {
    search(document.frmCouponSearch);
});
$(document).delegate('.language-js', 'change', function () {
    var lang_id = $(this).val();
    var coupon_id = $("input[name='coupon_id']").val();
    couponImages(coupon_id, lang_id);
});
(function () {
    search = function (form) {
        fcom.ajax(fcom.makeUrl('Coupons', 'search'), fcom.frmData(form), function (res) {
            $('#listing').html(res);
        });
    };
    goToSearchPage = function (pageno) {
        var frm = document.frmCouponSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    reloadList = function () {
        search(document.frmCouponSearchPaging);
    }
    form = function (id) {
        fcom.ajax(fcom.makeUrl('Coupons', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Coupons', 'setup'), data, function (t) {
            reloadList();
            if (t.langId > 0) {
                langForm(t.couponId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    langForm = function (couponId, langId) {
        fcom.ajax(fcom.makeUrl('Coupons', 'langForm', [couponId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    langSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Coupons', 'langSetup'), fcom.frmData(frm), function (res) {
            reloadList();
            if (res.langId > 0) {
                langForm(res.couponId, res.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    remove = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Coupons', 'remove'), 'id=' + id, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmCouponSearch.reset();
        search(document.frmCouponSearch);
    };

    couponHistory = function (couponId) {
        fcom.ajax(fcom.makeUrl('Coupons', 'uses', [couponId]), '', function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    toggleMaxDiscount = function (val) {
        if (val == 2) {
            $("#coupon_max_discount_div").hide();
        } else {
            $("#coupon_max_discount_div").show();
        }
    };
})();