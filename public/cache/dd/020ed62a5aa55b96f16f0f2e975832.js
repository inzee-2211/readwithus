/* global fcom, langLbl */
$(document).ready(function () {
    search(document.frmTeachLanguageSearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    var dv = '#listing';
    search = function (form) {
        fcom.ajax(fcom.makeUrl('TeachLanguage', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    clearSearch = function () {
        document.frmTeachLanguageSearch.reset();
        search(document.frmTeachLanguageSearch);
    };
    form = function (id) {
        fcom.ajax(fcom.makeUrl('TeachLanguage', 'form', [id]), '', function (response) {
            $.facebox(response);
        });
    };
    langForm = function (tLangId, langId) {
        fcom.ajax(fcom.makeUrl('TeachLanguage', 'langForm', [tLangId, langId]), '', function (response) {
            $.facebox(response);
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'setup'), fcom.frmData(frm), function (res) {
            search(document.frmTeachLanguageSearch);
            let element = $('.tabs_nav a.active').parent().next('li');
            if (element.length > 0) {
                let langId = element.find('a').attr('data-id');
                langForm(res.tLangId, langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    langSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'langSetup'), fcom.frmData(frm), function (res) {
            search(document.frmTeachLanguageSearch);
            let element = $('.tabs_nav a.active').parent().next('li');
            if (element.length > 0) {
                if (!element.find('a').hasClass('media-js')) {
                    let langId = element.find('a').attr('data-id');
                    langForm(res.tLangId, langId);
                } else {
                    mediaForm(res.tLangId);
                }
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        var data = 'tLangId=' + id;
        fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'deleteRecord'), data, function (res) {
            search(document.frmTeachLanguageSearch);
        });
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var tLangId = parseInt(obj.id);
        var data = 'tLangId=' + tLangId + "&status=" + active;
        fcom.ajax(fcom.makeUrl('TeachLanguage', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + tLangId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var tLangId = parseInt(obj.id);
        var data = 'tLangId=' + tLangId + "&status=" + inActive;
        fcom.ajax(fcom.makeUrl('TeachLanguage', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + tLangId).attr('onclick', 'activeStatus(this)');
        });
    };

    mediaForm = function (tLangId) {
        fcom.ajax(fcom.makeUrl('TeachLanguage', 'mediaForm', [tLangId]), '', function (response) {
            $.facebox(response);
        });
    };
    removeFile = function (tLangId, fileType) {
        if (!confirm(langLbl.confirmDeleteImage)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('TeachLanguage', 'removeFile', [tLangId, fileType]), '', function (t) {
            mediaForm(tLangId);
        });
    };
    uploadImage = function (input, tlanguageId, type) {
        if (input.files[0]) {
            uploadFile(input.files[0], tlanguageId, type);
        }
    };
    uploadFile = function (file, tlanguageId, type) {
        let formData = new FormData();
        formData.append('file', file);
        formData.append('imageType', type);
        fcom.ajaxMultipart(fcom.makeUrl('TeachLanguage', 'uploadFile', [tlanguageId]), formData, function (res) {
            search(document.frmSpokenLanguageSearch);
            mediaForm(tlanguageId);
        }, {fOutMode: 'json'});
    }
})();
$(document).on('click', '.tlanguageFile-Js', function () {
    $('.tlang_image_file').trigger('click');
});
$(document).on('click', '.tlanguageFlagFile-Js', function () {
    $('.tlang_flag_file').trigger('click');
});
