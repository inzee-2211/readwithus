$(function () {
    goToSearchPage = function (page) {
        alert('dsdsds');
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(page);
         search(frm);
    };
    search = function (frm) {
        console.log(fcom.frmData(frm));
        fcom.ajax(fcom.makeUrl('Quizzes', 'search'), fcom.frmData(frm), function (res) {
            $("#listing").html(res);
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        getSubCategories(0);
        search(document.frmSearch);
    };
    form = function () {
        fcom.ajax(fcom.makeUrl('Quizzes', 'form'), '', function (res) {
            $.facebox(res, 'facebox-medium');
        });
    };
    setup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('Quizzes', 'setup'), data, function (res) {
            $.facebox.close();
            search(document.frmSearch);
        }, { fOutMode: 'json' });
    };
    remove = function (courseId) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('Quizzes', 'remove', [courseId]), '', function (res) {
                search(document.frmSearch);
            });
        }
    };
    cancelForm = function (ordcrsId) {
      
        
        fcom.ajax(fcom.makeUrl('Quizzes', 'cancelForm'), { 'ordcrs_id': ordcrsId }, function (res) {
          //  $.facebox(res);
          location.reload();
        });
    };

    addForm = function (courseId) {
         
           fcom.ajax(fcom.makeUrl('Quizzes', 'addForm'), {courseId: courseId}, function (response) {
               $.facebox(response, 'facebox-medium');
             //  bindDatetimePicker("#grpcls_start_datetime");
           });
       };
    cancelSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'cancelSetup'), fcom.frmData(frm), function (res) {
            $.facebox.close();
            search(document.frmSearch);
        });
    };
    search(document.frmSearch);
    feedbackForm = function (ordcrsId) {
        fcom.ajax(fcom.makeUrl('Tutorials', 'feedbackForm'), { 'ordcrs_id': ordcrsId }, function (res) {
            $.facebox(res, 'facebox-medium');
        });
    };
    feedbackSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Tutorials', 'feedbackSetup'), fcom.frmData(frm), function (res) {
            $.facebox.close();
            search(document.frmSearch);
        });
    };
    retake = function (id) {
        if (confirm(langLbl.confirmRetake)) {
            fcom.updateWithAjax(fcom.makeUrl('Tutorials', 'retake'), { 'progress_id': id }, function (res) {
                window.location = fcom.makeUrl('Tutorials', 'index', [id]);
            });
        }
    };
    getSubCategories = function (id) {
        id = (id == '') ? 0 : id;
        fcom.ajax(fcom.makeUrl('Quizzes', 'getSubcategories', [id]), '', function (res) {
            $("#subCategories").html(res);
        }, {process: false});
    };
});
