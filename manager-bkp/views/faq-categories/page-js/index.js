/* global SITE_ROOT_URL, fcom, langLbl */
$(document).ready(function () {
    searchFaqCategories(document.frmSearch);
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (page) {
        var frm = document.frmFaqCatSearchPaging;
        $(frm.page).val(page);
        searchFaqCategories(frm);
    };
    redirectUrl = function (redirecrt) {
        window.location = SITE_ROOT_URL + '' + redirecrt;
    }
    reloadList = function () {
        searchFaqCategories(document.frmFaqCatSearchPaging);
    };
    searchFaqCategories = function (form) {
        fcom.ajax(fcom.makeUrl('FaqCategories', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    faqToCmsForm = function () {
        fcom.ajax(fcom.makeUrl('FaqCategories', 'faqToCmsForm'), '', function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    setupFaqToCms = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('FaqCategories', 'setupFaqToCms'), fcom.frmData(frm), function (t) {
            $(document).trigger('close.facebox');
        });
    };
    addFaqCatForm = function (id) {
        faqCatForm(id);
    };
    faqCatForm = function (id) {
        fcom.ajax(fcom.makeUrl('FaqCategories', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('FaqCategories', 'setup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                faqCatLangForm(t.catId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    faqCatLangForm = function (faqcatId, langId) {
        fcom.ajax(fcom.makeUrl('FaqCategories', 'langForm', [faqcatId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupLang = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('FaqCategories', 'langSetup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                faqCatLangForm(t.catId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('FaqCategories', 'deleteRecord'), {id: id}, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        searchFaqCategories(document.frmSearch);
    };
    toggleStatus = function (e, obj, canEdit) {
        if (canEdit == 0) {
            e.preventDefault();
            return;
        }
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var faqcatId = parseInt(obj.value);
        var data = 'faqcatId=' + faqcatId;
        fcom.ajax(fcom.makeUrl('FaqCategories', 'changeStatus'), data, function (res) {
            $(obj).toggleClass("active");
            setTimeout(function () {
                reloadList();
            }, 1000);
        });
    };
})();
