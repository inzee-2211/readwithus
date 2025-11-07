/* global fcom, langLbl */
$(document).ready(function () {
    search(document.frmPreferenceSearch);
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (page) {
        var frm = document.frmPreferenceSearchPaging;
        $(frm.page).val(page);
        search(frm);
    }
    search = function (form) {
        fcom.ajax(fcom.makeUrl('Preferences', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    preferenceForm = function (id, type) {
        fcom.ajax(fcom.makeUrl('Preferences', 'form', [id, type]), '', function (response) {
            $.facebox(response);
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Preferences', 'setup'), fcom.frmData(frm), function (res) {
            search(document.frmPreferenceSearch);
            let element = $('.tabs_nav a.active').parent().next('li');
            if (element.length > 0) {
                let langId = element.find('a').attr('data-id');
                langForm(res.preferenceId, langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    }
    langForm = function (preferenceId, langId) {
        fcom.ajax(fcom.makeUrl('Preferences', 'langForm', [preferenceId, langId]), '', function (response) {
            $.facebox(response);
        });
    };
    langSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Preferences', 'langSetup'), fcom.frmData(frm), function (res) {
            search(document.frmPreferenceSearch);
            let element = $('.tabs_nav a.active').parent().next('li');
            if (element.length > 0) {
                let langId = element.find('a').attr('data-id');
                langForm(res.preferenceId, langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        var data = 'preferenceId=' + id;
        fcom.updateWithAjax(fcom.makeUrl('Preferences', 'deleteRecord'), data, function (res) {
            search(document.frmPreferenceSearch);
        });
    };
    clearSearch = function () {
        document.frmPreferenceSearch.reset();
        search(document.frmPreferenceSearch);
    };
})();
