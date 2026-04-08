/* global fcom, langLbl, SITE_ROOT_URL */
$(document).ready(function () {
    searchSlides(document.frmSlideSearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    reloadList = function () {
        var frm = document.frmSlideSearch;
        searchSlides(frm);
    }
    searchSlides = function (form) {
        var dv = '#listing';
        fcom.ajax(fcom.makeUrl('Slides', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    addSlideForm = function (id) {
        slideForm(id);
    };
    slideForm = function (id) {
        fcom.ajax(fcom.makeUrl('Slides', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setup = function (frm) {
        validateLink(frm.slide_url);
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Slides', 'setup'), fcom.frmData(frm), function (t) {
            reloadList();
            slideMediaForm(t.slideId, 0);
        });
    };
    slideMediaForm = function (slideId, langId) {
        fcom.ajax(fcom.makeUrl('Slides', 'mediaForm'), { langId: langId, slideId: slideId }, function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    deleteRecord = function (id) {
        if (!confirm(langLbl.confirmDelete)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Slides', 'deleteRecord'), { id: id }, function (res) {
            reloadList();
        });
    };

    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var slideId = parseInt(obj.id);
        var data = 'slideId=' + slideId + '&status=' + active;
        fcom.ajax(fcom.makeUrl('Slides', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + slideId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var slideId = parseInt(obj.id);
        var data = 'slideId=' + slideId + '&status=' + inActive;
        fcom.ajax(fcom.makeUrl('Slides', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + slideId).attr('onclick', 'activeStatus(this)');
        });
    };
    setupMedia = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.process();
        var data = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('Slides', 'setupMedia'), data, function (response) {
            slideMediaForm(response.slideId, response.langId);
        }, { fOutMode: 'json' });
    };
})();