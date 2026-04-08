/* global fcom, langLbl, e, oUtil */
$(document).ready(function () {
    searchBlocks(document.frmBlockSearch);
});
(function () {
    var active = 1;
    var inActive = 0;
    reloadList = function () {
        searchBlocks(document.frmBlockSearch);
    };
    searchBlocks = function (form) {
        var dv = '#blockListing';
        fcom.ajax(fcom.makeUrl('ContentBlock', 'search'), fcom.frmData(form), function (res) {
            $(dv).html(res);
        });
    };
    addBlockFormNew = function (id) {
        addBlockForm(id);
    };
    addBlockForm = function (id) {
        fcom.ajax(fcom.makeUrl('ContentBlock', 'form', [id]), '', function (t) {
            fcom.updateFaceboxContent(t);
        });
    };
    setupBlock = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('ContentBlock', 'setup'), data, function (res) {
            if (res.langId > 0) {
                addBlockLangForm(res.epageId, res.langId);
                return;
            }
            reloadList();
            $(document).trigger('close.facebox');
        });
    };
    addBlockLangForm = function (epageId, langId) {
        fcom.resetEditorInstance();
        fcom.ajax(fcom.makeUrl('ContentBlock', 'langForm', [epageId, langId]), '', function (t) {
            fcom.updateFaceboxContent(t);
            fcom.setEditorLayout(langId);
            var frm = $('#facebox form')[0];
            var validator = $(frm).validation({errordisplay: 3});
            $(frm).submit(function (e) {
                e.preventDefault();
                validator.validate();
                if (!validator.isValid()) {
                    return;
                }
                var data = fcom.frmData(frm);
                fcom.updateWithAjax(fcom.makeUrl('ContentBlock', 'langSetup'), data, function (res) {
                    fcom.resetEditorInstance();
                    reloadList();
                    if (res.langId > 0) {
                        addBlockLangForm(res.epageId, res.langId);
                        return;
                    }
                    $(document).trigger('close.facebox');
                });
            });
        });
    };
    setupBlockLang = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('ContentBlock', 'langSetup'), fcom.frmData(frm), function (res) {
            reloadList();
            if (t.langId > 0) {
                addBlockLangForm(res.epageId, res.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };
    resetToDefaultContent = function () {
        var agree = confirm(langLbl.confirmReplaceCurrentToDefault);
        if (!agree) {
            return false;
        }
        oUtil.obj.putHTML($("#editor_default_content").html());
    };
    activeStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var epageId = parseInt(obj.id);
        data = 'epageId=' + epageId + '&status=' + active;
        fcom.ajax(fcom.makeUrl('ContentBlock', 'changeStatus'), data, function (res) {
            $(obj).removeClass("inactive");
            $(obj).addClass("active");
            $(".status_" + epageId).attr('onclick', 'inactiveStatus(this)');
        });
    };
    inactiveStatus = function (obj) {
        if (!confirm(langLbl.confirmUpdateStatus)) {
            e.preventDefault();
            return;
        }
        var epageId = parseInt(obj.id);
        var data = 'epageId=' + epageId + '&status=' + inActive;
        fcom.ajax(fcom.makeUrl('ContentBlock', 'changeStatus'), data, function (res) {
            $(obj).removeClass("active");
            $(obj).addClass("inactive");
            $(".status_" + epageId).attr('onclick', 'activeStatus(this)');
        });
    };
    removeBgImage = function (epage_id, langId, file_type) {
        if (!confirm(langLbl.confirmDeleteImage)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('ContentBlock', 'removeBgImage', [epage_id, langId, file_type]), '', function (res) {
            addBlockLangForm(epage_id, langId);
        });
    };
})();
