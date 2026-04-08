/* global fcom, langLbl */

$(document).ready(function () {
    searchEtpls(document.frmEtplsSearch);
});
(function () {
    var preview = 0;
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    goToSearchPage = function (page) {
        var frm = document.frmEtplsSrchPaging;
        $(frm.page).val(page);
        searchEtpls(frm);
    };
    reloadList = function () {
        searchEtpls(document.frmEtplsSrchPaging);
    };
    searchEtpls = function (form) {
        fcom.ajax(fcom.makeUrl('EmailTemplates', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    editEtplLangForm = function (etplCode, langId) {
        fcom.resetEditorInstance();
        editLangForm(etplCode, langId);
    };
    editLangForm = function (etplCode, langId) {
        fcom.resetEditorInstance();
        fcom.ajax(fcom.makeUrl('EmailTemplates', 'langForm', [etplCode, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
            fcom.setEditorLayout(langId);
            fcom.resetFaceboxHeight();
            var frm = $('#facebox form')[0];
            var validator = $(frm).validation({errordisplay: 3});
            $(frm).submit(function (e) {
                e.preventDefault();
                validator.validate();
                if (!validator.isValid()) {
                    return;
                }
                var data = fcom.frmData(frm);
                fcom.updateWithAjax(fcom.makeUrl('EmailTemplates', 'langSetup'), data, function (t) {
                    fcom.resetEditorInstance();
                    reloadList();
                    if (t.lang_id > 0) {
                        editLangForm(t.etplCode, t.lang_id);
                        return;
                    }
                    $(document).trigger('close.facebox');
                });
            });
        });
    };
    setupEtplLang = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('EmailTemplates', 'langSetup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (preview == 1) {
                $('#previewTpl')[0].click();
            } else {
                $(document).trigger('close.facebox');
            }
        });
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            return;
        }
        var etplCode = obj.id;
        if (etplCode == '') {
            fcom.error(langLbl.invalidRequest);
            return false;
        }
        var data = 'etplCode=' + etplCode + '&status=' + active;
        fcom.ajax(fcom.makeUrl('EmailTemplates', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + etplCode).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            return;
        }
        var etplCode = obj.id;
        if (etplCode == '') {
            fcom.error(langLbl.invalidRequest);
            return false;
        }
        var data = 'etplCode=' + etplCode + '&status=' + inActive;
        fcom.ajax(fcom.makeUrl('EmailTemplates', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + etplCode).attr('onclick', 'activeStatus(this)');
        });
    };
    clearSearch = function () {
        document.frmEtplsSearch.reset();
        searchEtpls(document.frmEtplsSearch);
    };
    setupAndPreview = function () {
        preview = 1;
        setupEtplLang(document.frmEtplLang);
    };
})();