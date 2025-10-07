(function () {
    var preview = 0;
    setupMedia = function () {
        var frm = document.frmMedia;
        if (!$(frm).validate()) {
            return;
        }
        var data = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('Certificates', 'setupMedia'), data, function (response) {
            $(frm)[0].reset();
            $('.certificateMediaJs img').attr('src', response.imgUrl);
        }, { fOutMode: 'json' });
    };
    setup = function () {
        var frm = (document.frmCertificate);
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        data += "&heading=" + $.trim($('.contentHeadingJs').text());
        data += "&content_part_1=" + $.trim($('.contentPart1Js').text());
        data += "&learner=" + $.trim($('.contentLearnerJs').text());
        data += "&content_part_2=" + $.trim($('.contentPart2Js').text());
        data += "&trainer=" + $.trim($('.contentTrainerJs').text());
        data += "&certificate_number=" + $.trim($('.contentCertNoJs').text());
        fcom.updateWithAjax(fcom.makeUrl('Certificates', 'setup'), data, function (t) {
            if (preview == 1) {
                preview = 0;
                window.open(fcom.makeUrl('Certificates', 'generate', [$('select[name="certpl_lang_id"]').val()]), '_blank');
                // $('#previewCertificateJs')[0].click();
            }
        });
        return false;
    };
    edit = function (certTplCode, langId) {
        window.location = fcom.makeUrl('Certificates', 'form', [certTplCode, langId]);
    };
    setupAndPreview = function () {
        preview = 1;
        setup();
    };
    resetToDefault = function () {
        var data = fcom.frmData(document.frmCertificate);
        fcom.ajax(fcom.makeUrl('Certificates', 'getDefaultContent'), data, function(response) {
            if (response.data) {
                $('.contentHeadingJs').text(response.data.heading);
                $('.contentPart1Js').text(response.data.content_part_1);
                $('.contentLearnerJs').text(response.data.learner);
                $('.contentPart2Js').text(response.data.content_part_2);
                $('.contentTrainerJs').text(response.data.trainer);
                $('.contentCertNoJs').text(response.data.certificate_number);
            }
        }, { fOutMode: 'json' });
    }
})();
