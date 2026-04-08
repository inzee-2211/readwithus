/* global fcom, e, langLbl */
$(document).ready(function () {
    searchSocialPlatforms();
});
(function () {
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    reloadList = function () {
        searchSocialPlatforms();
    };
    searchSocialPlatforms = function (form) {
        fcom.ajax(fcom.makeUrl('SocialPlatform', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    addFormNew = function (id) {
        addForm(id);
    };
    addForm = function (id) {
        fcom.ajax(fcom.makeUrl('SocialPlatform', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('SocialPlatform', 'setup'), fcom.frmData(frm), function (t) {
            reloadList();
            $(document).trigger('close.facebox');
        });
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var splatformId = parseInt(obj.id);
        var data = 'splatformId=' + splatformId + '&status=' + active;
        fcom.ajax(fcom.makeUrl('SocialPlatform', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + splatformId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var splatformId = parseInt(obj.id);
        var data = 'splatformId=' + splatformId + '&status=' + inActive;
        fcom.ajax(fcom.makeUrl('SocialPlatform', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + splatformId).attr('onclick', 'activeStatus(this)');
        });
    };
})();