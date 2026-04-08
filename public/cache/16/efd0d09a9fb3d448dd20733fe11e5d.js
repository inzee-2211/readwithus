/* global fcom */

$(document).ready(function () {
    searchLabels(document.frmLabelsSearch);
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (page) {
        var frm = document.frmLabelsSrchPaging;
        $(frm.page).val(page);
        searchLabels(frm);
    };
    reloadList = function () {
        searchLabels(document.frmLabelsSrchPaging);
    };
    searchLabels = function (frm) {
        fcom.ajax(fcom.makeUrl('Label', 'search'), fcom.frmData(frm), function (res) {
            $(dv).html(res);
        });
    };
    labelsForm = function (labelId) {
        fcom.ajax(fcom.makeUrl('Label', 'form', [labelId]), '', function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    setupLabels = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Label', 'setup'), fcom.frmData(frm), function (t) {
            $(document).trigger('close.facebox');
            reloadList();
        });
    };
    clearSearch = function () {
        document.frmLabelsSearch.reset();
        searchLabels(document.frmLabelsSearch);
    };
    exportLabels = function () {
        document.frmLabelsSearch.action = fcom.makeUrl('Label', 'export');
        document.frmLabelsSearch.submit();
    };
    importLabels = function () {
        fcom.ajax(fcom.makeUrl('Label', 'importLabelsForm'), '', function (t) {
            $.facebox(t, 'faceboxWidth');
        });
    };
    submitImportLaeblsUploadForm = function ()
    {
        var data = new FormData(  );
        $inputs = $('#frmImportLabels input[type=text],#frmImportLabels select,#frmImportLabels input[type=hidden]');
        $inputs.each(function () {
            data.append(this.name, $(this).val());
        });
        $.each($('#import_file')[0].files, function (i, file) {
            data.append('import_file', file);
            fcom.ajaxMultipart(fcom.makeUrl('Label', 'uploadLabelsImportedFile'), data, function (res) {
                $('#fileupload_div').html();
                reloadList();
                $(document).trigger('close.facebox');
            }, { fOutMode: 'json' });
        });
    };
})();