var lectureId;
$(function () {
    generalForm = function () {
       
        fcom.ajax(fcom.makeUrl('Quizzes', 'generalForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            var id = $('textarea[name="course_details"]').attr('id');
            window["oEdit_" + id].disableFocusOnLoad = true;
            $('#pageContentJs input[name="course_title"]:first').focus();
           getCourseEligibility();
          //  $('.general-info-js').removeClass('is-progress').addClass('is-completed');
            fcom.setEditorLayout(siteLangId);
        });
    };
    
    mediaForm = function (process = true) {
        //alert('sd');
        fcom.ajax(fcom.makeUrl('Quizzes', 'mediaForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            getCourseEligibility();
        }, {process: process});
    };
    // intendedLearnersForm = function () {
    //     fcom.ajax(fcom.makeUrl('Quizzes', 'intendedLearnersForm', [courseId]), '', function (res) {
    //         $('#pageContentJs').html(res);
    //         getCourseEligibility();
    //     });
    // };
    intendedLearnersForm = function (page = 1) {
  var frm = document.frmSearchPaging;
  if (frm && frm.pageno) {
    $(frm.pageno).val(page);
  }

  fcom.ajax(
    fcom.makeUrl('Quizzes', 'intendedLearnersForm', [courseId]),
    frm ? fcom.frmData(frm) : { pageno: page },
    function (res) {
      $('#pageContentJs').html(res);
      getCourseEligibility();
    }
  );
};

goToSearchPage = function (page) {
  intendedLearnersForm(page);
};

    settingsForm = function () {
        fcom.ajax(fcom.makeUrl('Quizzes', 'settingsForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            getCourseEligibility();
        });
    };
    addFld = function (type) {
        $('.typesAreaJs' + type + " .typesListJs").append($('.typesAreaJs' + type + " .typeFieldsJs:last").clone().find("input:text, input:hidden").val("").end());
        var obj = $('.typesAreaJs' + type + " .typeFieldsJs:last");
        $(obj).find("a.sortHandlerJs").removeClass('sortHandlerJs');
        $(obj).find("a.removeRespJs").attr('onclick', "removeIntendedLearner(this, 0);").show();
        $(obj).find(".field-count").attr('field-count', $(obj).find(".field-count").data('length'));
    };

    updateIntendedOrder = function() {
        var order = [];
        $('.sortable_ids').each(function () {
            order.push($(this).val());
        });
        fcom.ajax(fcom.makeUrl('Quizzes', 'updateIntendedOrder'), {
            'order': order
        }, function (res) {
            intendedLearnersForm();
        });
    }
    removeIntendedLearner = function (obj, id) {
        if (id > 0) {
            if (confirm(langLbl.confirmRemove)) {
                fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'deleteIntendedLearner', [id]), '', function (res) {
                    $(obj).parents('.typeFieldsJs').remove();
                });
            }
            getCourseEligibility();
        } else {
            $(obj).parents('.typeFieldsJs').remove();
        }
    };
    priceForm = function () {
        fcom.ajax(fcom.makeUrl('Quizzes', 'priceForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            updatePriceForm($('input[name="course_type"]:checked').val());
            getCourseEligibility();
        });
    };
removeQuizQuestion = function (questionId) {
  if (!confirm(langLbl.confirmRemove || 'Remove this question?')) return;

  const data = { quiz_id: courseId, question_id: questionId };

  fcom.updateWithAjax(
    fcom.makeUrl('Quizzes', 'removeQuestion'), // backend action
    data,
    function () {
      // reload current page of the questions list
      const frm = document.frmSearchPaging;
      const page = frm && frm.pageno ? parseInt($(frm.pageno).val() || 1, 10) : 1;
      intendedLearnersForm(page);
    }
  );
};

    // goToSearchPage = function (page) {
       
    //      var frm = document.frmSearchPaging;
    //       $(frm.pageno).val(page);
    //       // search(frm);
    // };
    search = function (frm) {
        console.log(fcom.frmData(frm));
        // fcom.ajax(fcom.makeUrl('Quizzes', 'priceForm'), fcom.frmData(frm), function (res) {
        //     $("#listing").html(res);
        // });
        fcom.ajax(fcom.makeUrl('Quizzes', 'priceForm', [courseId]), '', function (res) {
         //   $('#pageContentJs').html(res);
         //   updatePriceForm($('input[name="course_type"]:checked').val());
         //   getCourseEligibility();
        });
    };

    selectAllCheckboxes= function (courseId) {
        const checkedCheckboxes = document.querySelectorAll('input[name="questionIds[]"]:checked');
        const selectedValues = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);
        var data = {
            courseId: courseId,
            QId: selectedValues
        };
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'setupQuestions'), data, function (res) {
            intendedLearnersForm();
         //   curriculumForm();
           // getCourseEligibility();
        });
     

    };

    updatePriceForm = function (type) {
        if (type == TYPE_FREE) {
            $('select[name="course_currency_id"], input[name="course_price"]').attr("data-fatreq", '{"required":false}').val('');
            $('.reqFldsJs').hide();

        } else {
            $('select[name="course_currency_id"], input[name="course_price"]').attr("data-fatreq", '{"required":true}');
            $('.reqFldsJs').show();
        }
    };
    setupPrice = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'setupPrice'), data, function (res) {
            curriculumForm();
            getCourseEligibility();
        });
    };
    curriculumForm = function () {
        fcom.ajax(fcom.makeUrl('Quizzes', 'curriculumForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            searchSections();
            getCourseEligibility();
        });
    };
   
    setupSettings = function (frm) {
      if (!$(frm).validate()) {
            return;
        }
         var data = fcom.frmData(frm);
          
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'setupSettings'), data, function (res) {
            console.log(res);
           // settingsForm();
           setTimeout(function() {
            window.location.href = 'http://readwithus.org.uk/dashboard/quizzes';
        }, 0);
           return;
        });
    };
    setup = function () {
        var frm = $('#frmCourses');
        if (!$(frm).validate()) {
            return;
        }
 
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'setup'), data, function (res) {
            $('#mainHeadingJs').text(res.title);
            window.history.pushState('page', document.title, fcom.makeUrl('Quizzes', 'form', [res.courseId]));
            courseId = res.courseId;
           
         //   alert(JSON.stringify(res.courseId));
             intendedLearnersForm(courseId);
            // getCourseEligibility();
          //updatePriceForm
        });
    };

    setupIntendedLearners = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
      
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'setupIntendedLearners'), data, function (res) {
            // priceForm();
            // getCourseEligibility();
           // alert(JSON.stringify(res.courseId));
          // alert(JSON.stringify(res.courseId));
            courseId = res.courseId;
            settingsForm();
        });
    };
    setupMedia = function () {
        var frm = $('#frmCourses')[0];
        var data = new FormData(frm);
        frm.reset();
        fcom.ajaxMultipart(fcom.makeUrl('Quizzes', 'setupMedia'), data, function (res) {
            if (res.status == 1) {
                mediaForm(false);
                getCourseEligibility();
            }
        }, { fOutMode: 'json' });
    };
   
    removeMedia = function(type) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'removeMedia', [courseId]), {type}, function (res) {
                mediaForm();
                getCourseEligibility();
            });
        }
    };
    generalForm();
    getCourseEligibility = function () {
      
        fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'getEligibilityStatus', [courseId]), '', function (res) {
            setCompletedStatus(res.criteria);
        }, {'process' : false});
    };
    setCompletedStatus = function (criteria) {
       //  alert(criteria);
      ///alert(JSON.stringify(criteria, null, 2));
        $('.general-info-js, .intended-learner-js, .course-price-js, .curriculum-js, .course-setting-js').removeClass('is-completed').addClass('is-progress');
        $('.btnApprovalJs').addClass('d-none');
        if (criteria.course_lang == 1 && criteria.course_image == 1 && criteria.course_preview_video == 1 && criteria.course_cate == 1 && criteria.course_subcate == 1 && criteria.course_clang == 1) {
            $('.general-info-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.general_info_js == 1) {
            $('.general-info-js').removeClass('is-progress').addClass('is-completed');
        }
        // if (criteria.courses_intended_learners == 1) {
        //     $('.intended-learner-js').removeClass('is-progress').addClass('is-completed');
        // }

          if (criteria.courses_intended_learners1 == 1) {
            $('.intended-learner-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_price == 1 && criteria.course_currency_id == 1) {
            $('.course-price-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_sections == 1 && criteria.course_lectures == 1) {
            $('.curriculum-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_tags == 1) {
            $('.course-setting-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_is_eligible == true) {
            $('.btnApprovalJs').removeClass('d-none');
        }
    };
    getCourseEligibility();

    /* Sections [ */

    addForm = function (classId) {
     alert('ds');
        // fcom.ajax(fcom.makeUrl('Questions', 'addForm'), {classId: classId}, function (response) {
        //     $.facebox(response, 'facebox-medium');
        //   //  bindDatetimePicker("#grpcls_start_datetime");
        // });
    };
    sectionForm = function (id) {
        
      
         var section_order = $('#courseSectionOrderJs').val();
        fcom.ajax(fcom.makeUrl('Sections', 'form', [courseId]), { section_order, id }, function (res) {
            $('.message-display').remove();
            if (id > 0) {
                $('#sectionId' + id + " .sectionCardJs").hide();
                $('#sectionId' + id + " .sectionEditCardJs").html(res);
            } else {
                $('#courseSectionOrderJs').val(parseInt(section_order) + 1);
                $('#sectionFormAreaJs').append(res);
                $('body, html').animate({ scrollTop: $("#sectionForm" + section_order + '1').offset().top }, 1000);
            }
        }); 
    };
    setupSection = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Sections', 'setup'), data, function (res) {
            $(frm).parents('.card-panel').remove();
            searchSections();
            getCourseEligibility();
        });
    };
    updateSectionOrder = function () {
        var order = [''];
        $('#sectionAreaJs .card-panel').each(function () {
            order.push($(this).data('id'));
        });
        fcom.ajax(fcom.makeUrl('Sections', 'updateOrder', [courseId]), {
            'order': order
        }, function (res) {
            searchSections();
        });
    };
    removeSection = function (id) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('Sections', 'delete', [id]), '', function (res) {
                searchSections();
                getCourseEligibility();
            });
        }
    };
    cancelSection = function (id) {
        $("#sectionForm" + id).remove();
        if ($('#sectionAreaJs .card-panel').length < 1) {
            searchSections();
        }
    };
    searchSections = function (sectionId) {
        fcom.ajax(fcom.makeUrl('Sections', 'search', [courseId]), { 'section_id': sectionId }, function (res) {
            if (sectionId > 0) {
                $('#sectionId' + sectionId).html(res);
                return;
            } else {
                $('#sectionAreaJs').html(res);
            }
        });
    };
    /* ] */

    /* Lectures [ */
    lectureForm = function (sectionId, lectureId = 0) {
        var lectureOrder = $('#lectureOrderJs').val();
        fcom.ajax(fcom.makeUrl('Lectures', 'form', [sectionId]), { 'lecture_id': lectureId, 'lecture_order': lectureOrder, 'course_id': courseId }, function (res) {
            /* for edit, append form to the current lecture area */
            if ($('#sectionLectures' + lectureId).length > 0) {
                $('#sectionLectures' + lectureId).replaceWith(res).show();
            } else {
                /* if new form added, append it to the last */
                $('#sectionId' + sectionId + ' .lecturesListJs').append(res).show();
                $('#lectureOrderJs').val(parseInt(lectureOrder) + 1);
            }
            var id = $(res).find('textarea[name="lecture_details"]').attr('id');
            window["oEdit_" + id].disableFocusOnLoad = true;
            fcom.setEditorLayout(siteLangId);
        });
    };
    $(document).on('submit', 'form[name=frmLecture]', function (event) {
        var frm = $(this);
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Lectures', 'setup'), fcom.frmData(frm), function (res) {
            $(frm).parents('.card-group-js').attr('id', 'sectionLectures' + res.lectureId);
            lectureMediaForm(res.lectureId);
            getCourseEligibility();
        });
        return false;
    });
    updateLectureOrder = function () {
        var order = [''];
        $('#sectionAreaJs .lecturePanelJs').each(function () {
            order.push($(this).data('id'));
        });
        fcom.ajax(fcom.makeUrl('Lectures', 'updateOrder'), {
            'order': order
        }, function (res) {
            searchSections();
        });
    };
    removeLecture = function (sectionId, id) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('Lectures', 'delete', [id]), '', function (res) {
                searchSections(sectionId);
                getCourseEligibility();
            });
        }
    };
    cancelLecture = function (lectureId) {
        fcom.ajax(fcom.makeUrl('Lectures', 'search', [lectureId]), '', function (res) {
            var ele = $('#sectionLectures' + lectureId);
            $('#sectionLectures' + lectureId).before(res);
            $(ele).remove();
        });
    };
    removeLectureForm = function (sectionId, id) {
        $(id).remove();
        if ($('#sectionId' + sectionId + ' .lecturesListJs .card-group-js').length == 0) {
            $('#sectionId' + sectionId + ' .lecturesListJs').hide();
        }
    };
    lectureMediaForm = function (lectureId) {
        fcom.ajax(fcom.makeUrl('Lectures', 'mediaForm', [lectureId]), '', function (res) {
            $('#sectionLectures' + lectureId).html(res);
        });
    };
    validateVideolink = function (field) {
        let frm = field.form;
        let url = field.value.trim();
        if (url == '') {
            return false;
        }
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
        let matches = url.match(regExp);
        if (matches && matches[2].length == 11) {
            let validUrl = "https://www.youtube.com/embed/";
            validUrl += matches[2];
            $(field).val(validUrl);
        } else {
            $(field).val('');
        }
        $(frm).validate();
    };
    setupLectureMedia = function (frm) {
        if (!$("#" + frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Lectures', 'setupMedia'), fcom.frmData($("#" + frm)), function (res) {
            lectureResourceForm(res.lectureId);
        });
    };
    lectureResourceForm = function (lectureId, process = true) {
        fcom.ajax(fcom.makeUrl('LectureResources', 'index', [lectureId]), '', function (res) {
            $('#sectionLectures' + lectureId).html(res);
        }, { process: process });
    };
    setupLectureResrc = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('LectureResources', 'setup'), data, function (res) {
            lectureResourceForm(res.lectureId, false);
            $.facebox.close();
        }, { fOutMode: 'json' });
    };
    uploadResource = function (id) {
        var frm = $('#' + id)[0];
        setupLectureResrc(frm);
    };
    removeLectureResrc = function (id, lectureId) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('LectureResources', 'delete', [id]), '', function (res) {
                lectureResourceForm(lectureId);
            });
        }
    };
    getResources = function (lecId) {
        fcom.ajax(fcom.makeUrl('LectureResources', 'resources', [lecId]), '', function (res) {
            $.facebox(res, 'facebox-medium padding-0');
            lectureId = lecId;
            searchResources(document.frmResourceSearch);
        });
    };
    searchResources = function (frm, page = 1) {
        document.frmResourceSearch.page.value = page;
        fcom.updateWithAjax(fcom.makeUrl('LectureResources', 'search', [lectureId]), fcom.frmData(frm), function (res) {
            if (page > 1) {
                $('#listingJs').append(res.html);
            } else {
                $('#listingJs').html(res.html);
            }
            if (res.loadMore == 1) {
                $('.rvwLoadMoreJs a').data('page', res.nextPage);
                $('.rvwLoadMoreJs').show();
            } else {
                $('.rvwLoadMoreJs').hide();
            }
        });
    };
    resourcePaging = function (_obj) {
        searchResources(document.frmResourceSearch, $(_obj).data('page'));
    };
    /* ] */
    submitForReview = function () {
        if (confirm(langLbl.confirmCourseSubmission)) {
            fcom.updateWithAjax(fcom.makeUrl('Quizzes', 'submitForApproval', [courseId]), '', function (res) {
                window.location = fcom.makeUrl('Quizzes');
            });
        }
    }
});
$(document).ready(function(){
    $('body').on('input', 'input[type="text"], textarea', function () {
        var ele = $(this).parent();
        if ($(ele).hasClass('field-count')) {
            var max = parseInt($(ele).data('length'));
            var strLen = parseInt($(this).val().length);
            var limit = max - strLen;
            if (limit < 0) {
                $(this).val($(this).val().substring(0, max));
                $(ele).attr('field-count', 0);
                return;
            }
            $(ele).attr('field-count', limit);
        }
    });
});
getSubCategories = function (id, selectedId = 0) {
    fcom.ajax(fcom.makeUrl('Quizzes', 'getSubcategories', [id, selectedId]), '', function (res) {
        $("#subCategories").html(res);
    });
};