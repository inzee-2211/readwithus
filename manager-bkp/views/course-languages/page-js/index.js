$(document).ready(function () {
    search();
});
(function () {
    var dv = '#listing';
    search = function (process = true) {
        fcom.ajax(fcom.makeUrl('CourseLanguages', 'search'), '', function (res) {
            $(dv).html(res);
        }, {process: process});
    };
    changeStatus = function (obj, cLangId) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            return;
        }
        var status = parseInt($(obj).data('status'));
        var data = 'cLangId=' + cLangId + "&status=" + status;
        fcom.updateWithAjax(fcom.makeUrl('CourseLanguages', 'changeStatus'), data, function (res) {
            if (status == 1) {
                $(obj).removeClass("inactive").addClass("active").data("status", 0);
            } else {
                $(obj).removeClass("active").addClass("inactive").data("status", 1);
            }
        });
    };
    form = function (id) {
        fcom.ajax(fcom.makeUrl('CourseLanguages', 'form', [id]), '', function (response) {
            $.facebox(response);
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CourseLanguages', 'setup'), fcom.frmData(frm), function (res) {
            search(false);
            let element = $('.tabs_nav a.active').parent().next('li');
            if (element.length > 0) {
                let langId = element.find('a').attr('data-id');
                langForm(res.cLangId, langId, false);
                return;
            }
            $(document).trigger('close.facebox');
        });
    }
    langForm = function (cLangId, langId, process = true) {
        fcom.ajax(fcom.makeUrl('CourseLanguages', 'langForm', [cLangId, langId]), '', function (response) {
            $.facebox(response);
        }, {process: process});
    };
    langSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CourseLanguages', 'langSetup'), fcom.frmData(frm), function (res) {
            search(false);
            let element = $('.tabs_nav a.active').parent().next('li');
            if (element.length > 0) {
                let langId = element.find('a').attr('data-id');
                langForm(res.cLangId, langId, false);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CourseLanguages', 'deleteRecord'), { 'cLangId': id }, function (res) {
            search();
        });
    };
})();