/* global fcom */
(function () {
    updatePayment = function (form) {
        if (!$(form).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl("Orders", "updatePayment"), fcom.frmData(form), function (t) {
            location.reload();
        });
    };
    updateStatus = function (payId, status) {
        fcom.updateWithAjax(fcom.makeUrl("Orders", "updateStatus"), {payId: payId, status: status}, function (t) {
            location.reload();
        });
    };
})();
