/* global fcom, moment, props, langLbl, slabs, parseFloat, minValue, LESSON_TYPE_SUBCRIP, LESSON_TYPE_REGULAR, maxValue, FTRAIL_TYPE, frm */
var cart = {
    prop: {
        add_and_pay: 0,
    },
    addCourse: function (courseId) {
        fcom.ajax(fcom.makeUrl("Cart", "addCourse", '', confFrontEndUrl), { course_id: courseId }, function (response) {
            $.facebox(response, "facebox-large");
        });
    },
    addFreeCourse: function (courseId) {
        fcom.process();
        fcom.ajax(fcom.makeUrl("Cart", "addCourse", '', confFrontEndUrl), { course_id: courseId }, function (response) {
            cart.confirmOrder(document.frmCheckout);
        });
    },
    selectWallet: function (checked) {
        document.checkoutForm.add_and_pay.value = checked ? 1 : 0;
        if (!$(document.checkoutForm).validate()) {
            return;
        }
        fcom.process();
        var orderType = document.checkoutForm.order_type.value;
        fcom.ajax(fcom.makeUrl("Cart", "paymentSummary", [orderType], confFrontEndUrl), fcom.frmData(document.checkoutForm), function (response) {
            $.facebox(response);
        });
    },
    confirmOrder: function (form) {
        if (!$(form).validate()) {
            return;
        }
        fcom.process();
        form.submit.disabled = true;
        fcom.updateWithAjax(fcom.makeUrl("Cart", "confirmOrder", '', confFrontEndUrl), fcom.frmData(form), function (response) {
            setTimeout(function () {
                form.submit.disabled = false;
            }, 1000);
            if (response.redirectUrl) {
                window.location.href = response.redirectUrl;
            }
            if (response.status != 1) {
                form.submit.disabled = false;
            }
        }, { failed: true });
    },
    applyCoupon: function (code) {
        if (code) {
            document.checkoutForm.coupon_code.value = code;
        }
        if (!$(document.checkoutForm).validate()) {
            return;
        }
        fcom.process();
        fcom.ajax(fcom.makeUrl("Cart", "applyCoupon", '', confFrontEndUrl), fcom.frmData(document.checkoutForm), function (response) {
            $.facebox(response);
        });
    },
    removeCoupon: function () {
        fcom.process();
        fcom.ajax(fcom.makeUrl("Cart", "removeCoupon", '', confFrontEndUrl), fcom.frmData(document.checkoutForm), function (response) {
            $.facebox(response);
        });
    },
    disableEnter: function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            if (document.checkoutForm.coupon_code.value != '') {
                cart.applyCoupon(document.checkoutForm.coupon_code.value);
                return;
            }
        }
    },
};
$(document).bind("afterClose.facebox", function () {
    cart.prop.add_and_pay = 0;
});
