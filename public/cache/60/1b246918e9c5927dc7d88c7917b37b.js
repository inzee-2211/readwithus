/* global fcom, langLbl, e */
$(document).ready(function () {
    searchFaq(document.frmFaqearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    goToSearchPage = function (page) {
        var frm = document.frmFaqearchPaging;
        $(frm.page).val(page);
        searchFaq(frm);
    }
    reloadList = function () {
        searchFaq(document.frmFaqearchPaging);
    };
    searchFaq = function (form) {
        fcom.ajax(fcom.makeUrl('Faq', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    addFaqForm = function (id) {
        FaqForm(id);
    };
    FaqForm = function (id) {
        fcom.ajax(fcom.makeUrl('Faq', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    editFaqFormNew = function (faqId) {
        editFaqForm(faqId);
    };
    editFaqForm = function (faqId) {
        fcom.ajax(fcom.makeUrl('Faq', 'form', [faqId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupFaq = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Faq', 'setup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                editFaqLangForm(t.faqId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    }
    editFaqLangForm = function (faqId, langId) {
        fcom.ajax(fcom.makeUrl('Faq', 'langForm', [faqId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
             fcom.setEditorLayout(langId);
        });
    };
    setupLangFaq = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Faq', 'langSetup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                editFaqLangForm(t.faqId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        var data = 'faqId=' + id;
        fcom.updateWithAjax(fcom.makeUrl('Faq', 'deleteRecord'), data, function (res) {
            reloadList();
        });
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var faqId = parseInt(obj.id);
        var data = 'faqId=' + faqId + "&status=" + active;
        fcom.ajax(fcom.makeUrl('Faq', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + faqId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var faqId = parseInt(obj.id);
        var data = 'faqId=' + faqId + "&status=" + inActive;
        fcom.ajax(fcom.makeUrl('Faq', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + faqId).attr('onclick', 'activeStatus(this)');
        });
    };
    clearSearch = function () {
        document.frmFaqearch.reset();
        searchFaq(document.frmFaqearch);
    };
})();
