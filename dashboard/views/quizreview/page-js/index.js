/* global monthNames, langLbl, fcom, VIEW_CALENDAR, VIEW_LISTING, VIEW_LISTING */
(function () {
    goToSearchPage = function (pageno) {
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    searchListing = function (frm) {
        console.log(fcom.frmData(frm));
        fcom.ajax(fcom.makeUrl('Questions', 'searchquiz'), fcom.frmData(frm), function (response) {
            $("#listing").html(response);
        });
    };
    search = function (form) {
      //  var view = (form && form.view.value) ? parseInt(form.view.value) : VIEW_LISTING;
       // alert(view);
        var view=1;
        switch (view) {
            case VIEW_CALENDAR:
                getCalendarView();
                break;
            case VIEW_LISTING:
            default:
               searchListing(form);
                break;
        }
    };

  
    getCalendarView = function () {
        fcom.ajax(fcom.makeUrl('Questions', 'calendarView'), '', function (response) {
            $("#listing").html(response);
        });
    };

  /*  addgrade = function () {
      // Get all grade inputs
      const gradeInputs = document.querySelectorAll(
        '#grades-form input[type="number"]'
      );
      const gradesData = {}; // Object to store question ID and grades
      let isValid = true;
      let errorMessage = "";

      // Validate and collect grades
      gradeInputs.forEach((input) => {
        const questionId = input.name.match(/\d+/)[0]; // Extract question ID from name attribute

        const gradeValue = parseFloat(input.value);
        const maxMarks = parseFloat(input.getAttribute("data-max-marks"));

        if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > maxMarks) {
          isValid = false;
          errorMessage += `Grade for Question ${questionId} must be between 0 and ${maxMarks}.\n`;
        } else {
          alert(questionId);
          alert(gradesData[questionId]);
          gradesData[questionId] = gradeValue; // Store the question ID and grade
        }
        alert("Grades Data:", gradesData);
      });

      if (!isValid) {
        alert(errorMessage);
      } else {
        // If all validations pass, proceed with grades data
        console.log("Grades Data:", gradesData);
        alert("Grades validated successfully! Check console for details.");
      }
    };*/

    addgrade = function () {
        const gradeInputs = document.querySelectorAll(
          '#grades-form input[type="number"]'
        );
      
        const gradesData = {};
        let isValid = true;
        let errorMessage = "";

        const hiddenInput = document.querySelector('#grades-form input[name="rade_id"]');
        const radeId = hiddenInput ? hiddenInput.value : null;
        const score = document.querySelector('#grades-form input[name="score"]');
        const totalscore = score ? score.value : null;

         const  marks = document.querySelector('#grades-form input[name="totalmarks"]');
         const totalMarks = marks ? marks.value : null;

         const lecture_id = document.querySelector('#grades-form input[name="quizlectureid"]');
          const quiz_lecture_id = lecture_id ? lecture_id.value : null;

         const learner_id = document.querySelector('#grades-form input[name="quiz_learner_id"]');
         const quiz_learner_id = learner_id ? learner_id.value : null;
         const quiz_pass_percentage = document.querySelector('#grades-form input[name="quiz_pass_percentage"]');
         const Pass_percentage = quiz_pass_percentage ? quiz_pass_percentage.value : null;
          
        gradeInputs.forEach((input) => {
          // Debug: Log the name attribute
         // alert("Input Name Attribute:", input.name);
      
          // Extract question ID from the name attribute
          const questionIdMatch = input.name.match(/\d+/);
        //  alert("Question ID Match:", questionIdMatch); // Log questionIdMatch to check the regex output
      
          const questionId = questionIdMatch ? questionIdMatch[0] : null;
         // alert("Extracted Question ID:", questionId); // Debug: Log the extracted question ID
      
          if (!questionId) {
          //  console.error("Failed to extract a question ID. Skipping this input.");
            isValid = false;
            return; // Skip further processing for this input
          }
      
          const gradeValue = parseFloat(input.value);
          const maxMarks = parseFloat(input.getAttribute("data-max-marks"));
      
          if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > maxMarks) {
            isValid = false;
            errorMessage += `Grade for Question ${questionId} must be between 0 and ${maxMarks}.\n`;
          } else {
            gradesData[questionId] = gradeValue; // Store the question ID and grade
            
          }
        });
      
        if (!isValid) {
          alert(errorMessage);
        } else {
            
            fcom.ajax(fcom.makeUrl('Questions', 'submitTeacherResult'), {radeId: radeId,gradesData:gradesData,totalscore:totalscore,totalMarks:totalMarks,quiz_lecture_id:quiz_lecture_id,quiz_learner_id:quiz_learner_id,Pass_percentage:Pass_percentage}, function (response) {
                  
                setTimeout(function(){
                    location.reload();
                },1000)
                 
              });
        //     alert(`Grades Data Updated: ${JSON.stringify(gradesData)}`);
        //   alert("Grades validated successfully! Check console for details.");
        }
      };
      
    
    clearSearch = function () {
        document.frmClassSearch.reset();
        search(document.frmClassSearch);
    };
    addForm = function (classId) {
  
        fcom.ajax(fcom.makeUrl('Questions', 'addFormQuiz'), {classId: classId}, function (response) {
          //  alert(response);
            $.facebox(response, 'facebox-medium');
            bindDatetimePicker("#grpcls_start_datetime");
        });
    };
    setupClass = function (form, goToLangForm) {
        
        if (!$(form).validate()) {
            return;
        }
        var data = new FormData(form);
        fcom.ajaxMultipart(fcom.makeUrl('Questions', 'setupQuestions'), data, function (res) {
            search(document.frmClassSearch);
            if (goToLangForm && $('.lang-li').length > 0) {
                langId = $('.lang-li').first().attr('data-id');
                langForm(res.classId, langId);
                return;
            }
            $.facebox.close();
        }, {fOutMode: 'json'});
    };
    langForm = function (classId, langId) {
        fcom.ajax(fcom.makeUrl('Questions', 'langForm'), {classId: classId, langId: langId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    setupLangData = function (form, goToNext) {
        if (!$(form).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Questions', 'setupLang'), fcom.frmData(form), function (res) {
            search(document.frmClassSearch);
            if (goToNext && $('.lang-list .is-active').next('li').length > 0) {
                $('.lang-list .is-active').next('li').find('a').trigger('click');
                return;
            }
            $.facebox.close();
        });
    };
    formatSlug = function (fld) {
        fcom.updateWithAjax(fcom.makeUrl('Home', 'slug'), {slug: $(fld).val()}, function (res) {
            $(fld).val(res.slug);
            if (res.slug != '') {
                checkUnique($(fld), 'tbl_group_classes', 'grpcls_slug', 'grpcls_id', $('#grpcls_id'), []);
            }
        });
    };

    getSubCategories = function (id) {
      
        id = (id == '') ? 0 : id;
        fcom.ajax(fcom.makeUrl('Courses', 'getSubcategories', [id]), '', function (res) {
            $("#subCategories").html(res);
             
        }, {process: false});
    };

    getSubCategoriessearch = function (id) {
      
        id = (id == '') ? 0 : id;
        fcom.ajax(fcom.makeUrl('Courses', 'getSubcategories', [id]), '', function (res) {
            $("#subCategoriesSearch").html(res);
             
        }, {process: false});
    };
})();