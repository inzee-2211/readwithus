/* global fcom */
 
 
function addLessonFields() {
    let lessonContainer = document.getElementById("lesson-container");
    if (!lessonContainer) {
        console.error("Error: lessonContainer not found!");
        return;
    }

   // let lessonIndex = lessonContainer.children.length;

   let existingLessonFields = lessonContainer.querySelectorAll(".lesson-block");

    let lessonIndex = existingLessonFields.length; // Ensure new fields start at the correct index


    let newLesson = document.createElement("div");
    newLesson.classList.add("lesson-block"); // Ensures correct removal
    newLesson.innerHTML = `
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">Lesson Title</label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <input placeholder="Enter Lesson Title" type="text" name="lesson[${lessonIndex + 1}][title]" value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">Lesson Description</label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <textarea placeholder="Enter Lesson Description" name="lesson[${lessonIndex + 1}][description]"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                <div class="caption-wraper"><label class="field_label"></label></div>
                    <div class="field-wraper">
                        <div class="field_cover">
                        <input onclick="removeLessonField(this)" data-field-caption="" data-fatreq="{&quot;required&quot;:false}" type="button" name="btn_add_lesson" value="Remove Lesson">
                             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    lessonContainer.appendChild(newLesson);
}

function removeLessonField(button) {
    let lessonBlock = button.closest(".lesson-block"); // Selects the whole lesson div
    if (lessonBlock) {
        lessonBlock.remove();
    }
}



function addQuizFields() {
    let quizContainer = document.getElementById("quiz-container");
    if (!quizContainer) {
        console.error("Error: quiz-container not found!");
        return;
    }

    // Find the highest index in the current quiz fields
    let existingQuizFields = quizContainer.querySelectorAll(".quiz-block");
    let quizIndex = existingQuizFields.length; // Ensure new fields start at the correct index

    let newQuiz = document.createElement("div");
    newQuiz.classList.add("quiz-block"); // Assign a class for easy removal
    newQuiz.innerHTML = `
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">Quiz</label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <input placeholder="Enter Quiz" type="text" name="quiz[${quizIndex + 1}][quiz]" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                <div class="caption-wraper"><label class="field_label"></label></div>
                    <div class="field-wraper">
                        <div class="field_cover">
                          <input onclick="removeQuizField(this)" data-field-caption="" data-fatreq="{&quot;required&quot;:false}" type="button" name="btn_add_lesson" value="Remove Quiz">
 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    quizContainer.appendChild(newQuiz);
}

function removeQuizField(button) {
    let quizBlock = button.closest(".quiz-block"); // Get the nearest quiz block
    if (quizBlock) {
        quizBlock.remove();
    }
}




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
        fcom.ajax(fcom.makeUrl('Coursemanagement', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };
    clearSearch = function () {
        document.frmSearch.reset();
        $("input[name='course_clang_id'], select[name='course_cateid'], select[name='course_subcateid']").val('');
        getSubcategories(0);
        search(document.frmSearch);
    };


    // categoryForm = function () {
    //     categoryId=1;
    //     fcom.ajax(fcom.makeUrl('Coursemanagement', 'form', [categoryId]), '', function (response) {
    //         $.facebox(response, 'faceboxWidth');
           
    //     });
    // };


    /*
    categoryForm = function () {
    categoryId = 1;
    fcom.ajax(fcom.makeUrl('Coursemanagement', 'form', [categoryId]), '', function (response) {
        $.facebox(response, 'faceboxWidth');

        // Wait for the form to be injected
        setTimeout(function () {
            const select = document.querySelector('select.examboard-select');
            if (!select) return;

            if (document.querySelector('#deleteExamBoardBtn')) return; // avoid duplicate

            // Create delete button
            const deleteBtn = document.createElement('button');
            deleteBtn.id = 'deleteExamBoardBtn';
            deleteBtn.type = 'button';
            deleteBtn.innerText = '🗑️ Delete Selected Exam Board';
            deleteBtn.className = 'btn btn-danger btn-sm';
            deleteBtn.style.marginTop = '10px';
            deleteBtn.style.marginLeft = '10px';
 

                deleteBtn.onclick = function () {
                const selectedValue = select.value;

                if (!selectedValue || selectedValue === 'add_new') {
                    alert('Cannot delete this option.');
                    return;
                }

                if (!confirm('Are you sure you want to delete this exam board from the database?')) return;

                 fcom.updateWithAjax(fcom.makeUrl('Coursemanagement', 'deleteExamBoard'), { examboard_id: selectedValue }, function (res) {
                // On success, remove from select
                 const option = select.querySelector(`option[value="${selectedValue}"]`);
                if (option) {
                option.remove();
                select.selectedIndex = 0;
                select.dispatchEvent(new Event('change'));
                }
                });
                };

          select.parentNode.insertBefore(deleteBtn, select.nextSibling);

          
        }, 300);  
    });
};*/



categoryForm = function () {
    categoryId = 1;
    fcom.ajax(fcom.makeUrl('Coursemanagement', 'form', [categoryId]), '', function (response) {
        $.facebox(response, 'faceboxWidth');

        setTimeout(function () {
            const dropdownsToEnableDelete = [
                { id: 'examboard', label: 'Exam Board', action: 'deleteExamBoard', param: 'examboard_id' },
                { id: 'tier', label: 'Tier', action: 'deleteTier', param: 'tier_id' },
                { id: 'type', label: 'Type', action: 'deleteType', param: 'type_id' },
                { id: 'year', label: 'Year', action: 'deleteYear', param: 'year_id' },
                { id: 'level', label: 'Level', action: 'deleteLevel', param: 'level_id' },
                { id: 'subject', label: 'Subject', action: 'deleteSubject', param: 'subject_id' },
                { id: 'topic', label: 'Topic', action: 'deleteTopic', param: 'topic_id' },
            ];

            dropdownsToEnableDelete.forEach(({ id, label, action, param }) => {
                attachDeleteButtonAfterFacebox(id, label, 'Coursemanagement', action, param);
            });
        }, 300);
    });
};

function attachDeleteButtonAfterFacebox(selectId, labelText, controller, action, paramKey) {
    const select = document.getElementById(selectId);
    if (!select || document.getElementById(`deleteBtn_${selectId}`)) return;

    const deleteBtn = document.createElement('button');
    deleteBtn.id = `deleteBtn_${selectId}`;
    deleteBtn.type = 'button';
    deleteBtn.innerText = `🗑️ Delete Selected ${labelText}`;
    deleteBtn.className = 'btn btn-danger btn-sm';
    deleteBtn.style.marginTop = '10px';
    deleteBtn.style.marginLeft = '10px';

    deleteBtn.onclick = function () {
        const selectedValue = select.value;

        if (!selectedValue || selectedValue === 'add_new') {
            alert(`Cannot delete this option.`);
            return;
        }

        if (!confirm(`Are you sure you want to delete this ${labelText} from the database?`)) return;

        const data = {};
        data[paramKey] = selectedValue;

        fcom.updateWithAjax(fcom.makeUrl(controller, action), data, function () {
            const option = select.querySelector(`option[value="${selectedValue}"]`);
            if (option) {
                option.remove();
                select.selectedIndex = 0;
                select.dispatchEvent(new Event('change'));
            }
        });
    };

    select.parentNode.insertBefore(deleteBtn, select.nextSibling);
}


 

  
setup = function (frm) {
    if (!$(frm).validate()) {
        return;
    }

    let formData = new FormData(frm); // Ensure file upload works

    $.ajax({
        url: fcom.makeUrl('Coursemanagement', 'setup'),
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


uploadQuestionBank = function (frm) {
    if (!$(frm).validate()) {
        return;
    }

    let formData = new FormData(frm); // Enables file upload

    $.ajax({
        url: fcom.makeUrl('Coursemanagement', 'uploadQuestionBank'), // Replace with your controller/action
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (res) {
            if (res.status === 1) {
                $.mbsmessage(res.msg, true, 'alert--success');
                location.reload(); // Reload or dynamically update if needed
            } else {
                $.mbsmessage(res.msg || 'Upload failed', false, 'alert--danger');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            $.mbsmessage('Server error during upload.', false, 'alert--danger');
        }
    });
};




    

    deleted = function (courseId) {
      fcom.ajax(fcom.makeUrl('Coursemanagement', 'deleted', [courseId]), '', function (res) {
            // $.facebox(res, 'faceboxWidth');
           location.reload(); 
        });
    };

     deleteMaterial = function (courseId) {
      fcom.ajax(fcom.makeUrl('Coursemanagement', 'deleteMaterial', [courseId]), '', function (res) {
            // $.facebox(res, 'faceboxWidth');
           location.reload(); 
        });
    };


      view = function (courseId) {
      fcom.ajax(fcom.makeUrl('Coursemanagement', 'view', [courseId]), '', function (res) {
             $.facebox(res, 'faceboxWidth');
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
