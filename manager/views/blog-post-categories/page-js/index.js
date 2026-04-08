
/* global fcom, langLbl */
$(document).ready(function () {
    searchBlogPostCategories(document.frmSearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    goToSearchPage = function (page) {
        var frm = document.frmCatSearchPaging;
        $(frm.page).val(page);
        searchBlogPostCategories(frm);
    }
    reloadList = function () {
        searchBlogPostCategories(document.frmCatSearchPaging);
    }
    addCategoryForm = function (id) {
        categoryForm(id);
    };
    categoryForm = function (id) {
        var frm = document.frmCatSearchPaging;
        var parent = $(frm.bpcategory_parent).val();
        if (typeof parent == undefined || parent == null) {
            parent = 0;
        }
        fcom.ajax(fcom.makeUrl('BlogPostCategories', 'form', [id, parent]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupCategory = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogPostCategories', 'setup'), fcom.frmData(frm), function (res) {
            reloadList();
            if (res.langId > 0) {
                categoryLangForm(res.catId, res.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    categoryLangForm = function (catId, langId) {
        fcom.ajax(fcom.makeUrl('BlogPostCategories', 'langForm', [catId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupCategoryLang = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogPostCategories', 'langSetup'), fcom.frmData(frm), function (res) {
            reloadList();
            if (res.langId > 0) {
                categoryLangForm(res.catId, res.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    searchBlogPostCategories = function (form) {
        fcom.ajax(fcom.makeUrl('BlogPostCategories', 'search'), fcom.frmData(form), function (res) {
            $("#listing").html(res);
        });
    };
    subcat_list = function (parent) {
        var frm = document.frmCatSearchPaging;
        $(frm.bpcategory_parent).val(parent);
        reloadList();
    };
    categoryMediaForm = function (prodCatId) {
        fcom.ajax(fcom.makeUrl('BlogPostCategories', 'mediaForm', [prodCatId]), '', function (t) {
            $.facebox(t);
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BlogPostCategories', 'deleteRecord'), {id: id}, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        searchBlogPostCategories(document.frmSearch);
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var bpcategoryId = parseInt(obj.id);
        var data = 'bpcategoryId=' + bpcategoryId + "&status=" + active;
        fcom.ajax(fcom.makeUrl('BlogPostCategories', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + bpcategoryId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var bpcategoryId = parseInt(obj.id);
        var data = 'bpcategoryId=' + bpcategoryId + "&status=" + inActive;
        fcom.ajax(fcom.makeUrl('BlogPostCategories', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + bpcategoryId).attr('onclick', 'activeStatus(this)');
        });
    };
})();