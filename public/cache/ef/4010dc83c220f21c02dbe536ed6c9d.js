/* global fcom, langLbl, e */
$(document).ready(function () {
    searchCountry(document.frmCountrySearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    goToSearchPage = function (page) {
        var frm = document.frmCountrySearchPaging;
        $(frm.page).val(page);
        searchCountry(frm);
    };
    reloadList = function () {
        searchCountry(document.frmCountrySearchPaging);
    };
    searchCountry = function (form) {
        fcom.ajax(fcom.makeUrl('Countries', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    addCountryForm = function (id) {
        countryForm(id);
    };
    countryForm = function (id) {
        fcom.ajax(fcom.makeUrl('Countries', 'form', [id]), '', function (t) {
            $.facebox(t, 'faceboxWidth');
            fcom.updateFaceboxContent(t);
        });
    };
    editCountryFormNew = function (countryId) {
        editCountryForm(countryId);
    };
    editCountryForm = function (countryId) {
        fcom.ajax(fcom.makeUrl('Countries', 'form', [countryId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupCountry = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Countries', 'setup'), data, function (t) {
            reloadList();
            if (t.langId > 0) {
                editCountryLangForm(t.countryId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    editCountryLangForm = function (countryId, langId) {
        fcom.ajax(fcom.makeUrl('Countries', 'langForm', [countryId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupLangCountry = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Countries', 'langSetup'), fcom.frmData(frm), function (t) {
            reloadList();
            if (t.langId > 0) {
                editCountryLangForm(t.countryId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var countryId = parseInt(obj.id);
        var data = 'countryId=' + countryId + "&status=" + active;
        fcom.ajax(fcom.makeUrl('Countries', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + countryId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var countryId = parseInt(obj.id);
        var data = 'countryId=' + countryId + "&status=" + inActive;
        fcom.ajax(fcom.makeUrl('Countries', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + countryId).attr('onclick', 'activeStatus(this)');
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        searchCountry(document.frmSearch);
    };
})();