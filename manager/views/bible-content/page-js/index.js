/* global fcom */
$(document).ready(function () {
    searchPages(document.frmPagesSearch);
});
(function () {
    goToSearchPage = function (page) {
        var frm = document.frmPagesSearchPaging;
        $(frm.page).val(page);
        searchPages(frm);
    };
    reloadList = function () {
        searchPages(document.frmPagesSearchPaging);
    };
    searchPages = function (form) {
        var dv = '#pageListing';
        fcom.ajax(fcom.makeUrl('BibleContent', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    addForm = function (id) {
        fcom.resetEditorInstance();
        fcom.ajax(fcom.makeUrl('BibleContent', 'form', [id]), '', function (t) {
            $.facebox(t, 'faceboxWidth');
            var frm = $('#facebox form')[0];
            var validator = $(frm).validation({errordisplay: 3});
            $(frm).submit(function (e) {
                e.preventDefault();
                setup(frm, validator);
            });
        });
    };
    setup = function (frm, validator) {
        validateYoutubelink(frm.biblecontent_url);
        validator.validate();
        if (!validator.isValid()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('BibleContent', 'setup'), data, function (res) {
            fcom.success(res.msg);
            $(document).trigger('close.facebox');
            reloadList();
        });
        return false;
    };
    addLangForm = function (pageId, langId) {
        fcom.resetEditorInstance();
        fcom.ajax(fcom.makeUrl('BibleContent', 'langForm', [pageId, langId]), '', function (t) {
            $.facebox(t);
            var frm = $('#facebox form')[0];
            var validator = $(frm).validation({errordisplay: 3});
            $(frm).submit(function (e) {
                e.preventDefault();
                validator.validate();
                if (!validator.isValid()) {
                    return;
                }
                var data = fcom.frmData(frm);
                fcom.updateWithAjax(fcom.makeUrl('BibleContent', 'langSetup'), data, function (res) {
                    reloadList();
                    if (res.langId > 0) {
                        addLangForm(res.biblecontent_id, res.langId);
                        return;
                    }
                    $(document).trigger('close.facebox');
                });
            });
        });
    };
    setupLang = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('BibleContent', 'langSetup'), fcom.frmData(frm), function (res) {
            reloadList();
            if (res.langId > 0) {
                addLangForm(res.biblecontent_id, res.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    deleteRecord = function (id) {
        if (!confirm("Do you really want to delete this record?")) {
            return;
        }
        fcom.ajax(fcom.makeUrl('BibleContent', 'deleteRecord'), {id: id}, function (res) {
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmPagesSearch.reset();
        searchPages(document.frmPagesSearch);
    };
    toggleStatus = function (obj) {
        if (!confirm("Do you really want to update status?")) {
            return;
        }
        var biblecontentId = parseInt(obj.id);
        if (biblecontentId < 1) {
            fcom.error('Invalid Request!');
            return false;
        }
        var statusStr = '';
        if ($(obj).hasClass('active')) {
            statusStr = 'biblecontent_active=0';
        } else {
            statusStr = 'biblecontent_active=1';
        }
        var data = 'biblecontent_id=' + biblecontentId + '&' + statusStr;
        fcom.ajax(fcom.makeUrl('BibleContent', 'changeStatus'), data, function (res) {
            $(obj).toggleClass("active");
            setTimeout(function () {
                reloadList();
            }, 1000);
        });
    };
})();
function showMarketingMediaType(val) {
    var selectedMediaType = parseInt(val);
    if (isNaN(selectedMediaType)) {
        selectedMediaType = 0;
    }
    $('.media-types').parents('.col-3').hide();
    switch (selectedMediaType) {
        case 1:
            $('#ImageId').parents('.col-3').show();
            break;
        case 2:
            $('#videoId').parents('.col-3').show();
            break;
    }
    return true;
}
