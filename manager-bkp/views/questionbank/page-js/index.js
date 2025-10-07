/* global fcom */
$(document).ready(function () {
    search(document.frmSearch);

    $("input[name='course_clang']").autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: fcom.makeUrl('Courses', 'autoCompleteJson'),
                data: { keyword: request, fIsAjax: 1 },
                dataType: 'json',
                type: 'post',
                success: function (result) {
                    response($.map(result.data, function (item) {
                        return { label: escapeHtml(item['clang_name']), value: item['clang_id'], name: item['clang_name'] };
                    }));
                },
            });
        },
        'select': function (item) {
            $("input[name='course_clang_id']").val(item.value);
            $("input[name='course_clang']").val(item.name);
        }
    });
    $("input[name='course_clang']").keyup(function () {
        $("input[name='course_clang_id']").val('');
    });
});
(function () {
    var dv = '#listing';
    goToSearchPage = function (pageno) {
        var frm = document.frmPaging;
        $(frm.page).val(pageno);
        search(frm);
    };
    search = function (form) {
        var data = data = fcom.frmData(form);
        fcom.ajax(fcom.makeUrl('Questionbank', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        $("input[name='course_clang_id'], select[name='course_cateid'], select[name='course_subcateid']").val('');
        getSubcategories(0);
        search(document.frmSearch);
    };


    categoryForm = function () {
        categoryId=1;
        fcom.ajax(fcom.makeUrl('Questionbank', 'form', [categoryId]), '', function (response) {
            $.facebox(response, 'faceboxWidth');
           
        });
    };

//    setup = function (frm) {
//         if (!$(frm).validate()) {
//             return;
//         }
//         fcom.updateWithAjax(fcom.makeUrl('Questionbank', 'setup'), fcom.frmData(frm), function (res) {
//             search(document.categorySearch, false);
//             let element = $('.tabs_nav a.active').parent().next('li');
//             if (element.length > 0) {
//                 let langId = element.find('a').attr('data-id');
//                 langForm(res.cateId, langId, false);
//                 return;
//             }
//             $(document).trigger('close.facebox');
//         });
//     }; 

  
setup = function (frm) {
    if (!$(frm).validate()) {
        return;
    }

    let formData = new FormData(frm); // Ensure file upload works

    $.ajax({
        url: fcom.makeUrl('Questionbank', 'setup'),
        type: 'POST',
        data: formData,
        contentType: false, 
        processData: false, 
        dataType: 'json',
        success: function (res) {
            if (res.status === 1) {
              
                location.reload(); 
                // $(document).trigger('close.facebox');  
                // search(document.frmSearch);
            } else {
                alert(res.msg); // Show error message
            }
        },
        error: function (xhr) {
            console.log(xhr.responseText); // Debugging
        }
    });
};



    

    view = function (courseId) {
        fcom.ajax(fcom.makeUrl('Questionbank', 'view', [courseId]), '', function (res) {
           // $.facebox(res, 'faceboxWidth');
           location.reload(); 
        });
    };
    userLogin = function (userId, courseId, action = 'edit') {
        fcom.updateWithAjax(fcom.makeUrl('Users', 'login', [userId]), '', function (res) {
            if (action == 'edit') {
                window.open(fcom.makeUrl('Courses', 'form', [courseId], SITE_ROOT_DASHBOARD_URL), "_blank");
            } else if(action == 'preview') {
                window.open(fcom.makeUrl('CoursePreview', 'index', [courseId], SITE_ROOT_DASHBOARD_URL), "_blank");
            }
        });
    };
    getSubcategories = function (id, selectedId = 0) {
        fcom.ajax(fcom.makeUrl('Courses', 'getSubcategories', [id, selectedId]), '', function (res) {
            $("#subCategories").html(res);
        }, { async : false });
    };
    updateStatus = function (id, status) {
        if (confirm(langLbl.confirmUpdateStatus)) {
            fcom.updateWithAjax(fcom.makeUrl('Courses', 'updateStatus', [id, status]), '', function (res) {
                if ($('form[name="frmPaging"] input[name="page"]').length > 0) {
                    search(document.frmPaging);
                } else {
                    search(document.frmSearch);
                }
            });
        }
    };
})();
