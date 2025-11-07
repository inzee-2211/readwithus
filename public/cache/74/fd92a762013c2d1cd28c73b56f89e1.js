var tutor = false;
var notes = false;
var lecture = false;
var reviews = false;
$(function () {

    loadLectureShowmsg = function (lectureId) {
      alert('You need to attempt and qualify this lecture quiz before proceeding to the next lecture.');
    };
    loadLecture = function (lectureId) {
        if (lecture == false || lectureId > 0) {
            var progressId = $('#progressId').val();
            fcom.ajax(fcom.makeUrl('Tutorials', 'getLectureData', [lectureId, progressId]), '', function (res) {
                $('.lectureDetailJs').html(res);
                setCurrentLecture(lectureId);
                getVideo(lectureId);
                lecture = true;
            });
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
        $('.sidebarPanelJs').css({'display': ''});
        $('.tutorialTabsJs ul li').removeClass('is-active');
        $('.crsDetailTabJs').parent().addClass('is-active');
        $('.lectureDetailJs, .tabsPanelJs').show();
    };
    getVideo = function (lectureId) {
        var progressId = $('#progressId').val();
        fcom.ajax(fcom.makeUrl('Tutorials', 'getVideo', [lectureId, progressId]), '', function (res) {
            $('.videoContentJs').html(res);
        }, { 'process': false });
    };
    setCurrentLecture = function (lectureId) {
        $('.lecturesListJs .lecture, .sectionListJs').removeClass('is-active');
        $('.sectionListJs .control-target-js').hide();
        $('#lectureJs' + lectureId).addClass('is-active');
        $('#lectureJs' + lectureId).parents('.sectionListJs').addClass('is-active');
        $('#lectureJs' + lectureId).parents('.control-target-js').show();
        $('.lectureTitleJs').text($('#lectureJs' + lectureId + ' .lectureName').text());
        currentLectureId = lectureId;
    };
    getLecture = function (lectureCompleted = 0, next = 1) {
        var progressId = $('#progressId').val();
        fcom.updateWithAjax(fcom.makeUrl('Tutorials', 'getLecture', [next]), {
            'progress_id': progressId,
        }, function (res) {
            lecture = false;
            if (lectureCompleted == 1) {
                markComplete(res.previous_lecture_id, 1);
            }
            loadLecture(res.next_lecture_id);
            setProgress();
        });
    };
    markComplete = function (lectureId, status) {
        fcom.updateWithAjax(fcom.makeUrl('Tutorials', 'markComplete'), {
            'status': status,
            'lecture_id': lectureId,
            'progress_id': $('#progressId').val()
        }, function () {
            var obj = $('#lectureJs' + lectureId).find('input[type="checkbox"]');
            if (status == 1) {
                $(obj).prop('checked', true);
                $('#btnComplete' + lectureId).addClass('btn--disabled');
            } else {
                $('#btnComplete' + lectureId).removeClass('btn--disabled');
            }
            var sectionId = $(obj).data('section');
            $('.completedLecture' + sectionId).text($(obj).parents('.lecturesListJs').find('input[type="checkbox"]:checked').length);
            setProgress();
        }, { 'process': false });
    };
    $('.lecturesListJs input[type="checkbox"]').change(function () {
        var _obj = $(this);
        var checked = ($(_obj).is(":checked")) ? 1 : 0;
        markComplete($(_obj).val(), checked);
    });
    $('body').on('click', '.getNextJs', function () {
        if ($(this).attr('last-record') == 1) {
            return;
        }
        getLecture();
    });
    $('body').on('click', '.getPrevJs', function () {
        if ($(this).attr('last-record') == 1) {
            return;
        }
        getLecture(0, 0);
    });
    setProgress = function () {
        var progressId = $('#progressId').val();
        fcom.updateWithAjax(fcom.makeUrl('Tutorials', 'setProgress'), {
            'progress_id': progressId
        }, function (res) {
            var lbl = langLbl.courseProgressPercent;
            lbl = lbl.replace("{percent}", res.progress);
            $('.progressPercent').html(lbl);
            $('#progressBarJs').prop('style', "--percent:" + parseInt(res.progress));
            if (res.is_completed == true) {
                window.location = fcom.makeUrl('Tutorials', 'completed', [progressId]);
            }
        }, {'process': false});
    };
    if (currentLectureId > 0) {
        loadLecture(currentLectureId);
    } else {
        getLecture();
    }
    getTutorInfo = function () {
        if (tutor == false) {
            fcom.ajax(fcom.makeUrl('Tutorials', 'getTeacherDetail'), { 'course_id': courseId }, function (res) {
                $('.tutorInfoJs').html(res);
                tutor = true;
            });
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs , .quizJs').hide();
        $('.sidebarPanelJs').css({ 'display': '' });
        $('.tutorInfoJs, .tabsPanelJs').show();
    };
    AttampQuiz = function (quiz_id, courseId,  lectureId) {

        fcom.ajax(fcom.makeUrl('Tutorials', 'getQuizinfo'), { 'quiz_id': quiz_id,'courseId': courseId,'lectureId': lectureId }, function (res) {
             $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs , .quizJs').hide();
              $('.sidebarPanelJs').css({ 'display': '' });
           $('.quizJs').html(res).show();
             $('.tabsPanelJs').show();
            // searchReviews();
        });
 
    };
    getReviews = function () {
        var progressId = $('#progressId').val();
        fcom.ajax(fcom.makeUrl('Tutorials', 'getReviews'), { 'course_id': courseId, 'progress_id': progressId }, function (res) {
            $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
            $('.sidebarPanelJs').css({ 'display': '' });
            $('.reviewsJs').html(res).show();
            $('.tabsPanelJs').show();
            searchReviews();
        });
    };
    searchReviews = function () {
        var data = fcom.frmData(document.reviewFrm);
        fcom.ajax(fcom.makeUrl('Tutorials', 'searchReviews'), data, function (res) {
            $('.reviewSrchListJs').remove();
            $('.reviewsListJs').after(res);
        });
    };
    goToReviewsSearchPage = function (page) {
        var frm = document.reviewFrm;
        $(frm.pageno).val(page);
        searchReviews(frm);
    };
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
            $('.reviewFrmJs').removeAttr('onclick').addClass('btn--disabled');
        });
    };
    getNotes = function (id) {
        if (notes == false) {
            fcom.ajax(fcom.makeUrl('LectureNotes', 'index'), {'course_id' : courseId, 'ordcrs_id': id}, function (res) {
                $('.notesJs').html(res);
                notesSearch(document.frmNotesSearch);
                notes = true;
            });
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
        $('.sidebarPanelJs').css({ 'display': '' });
        $('.notesJs, .tabsPanelJs').show();
    };
    notesSearch = function (frm, process = true) {
        var data = fcom.frmData(frm);
        fcom.ajax(fcom.makeUrl('LectureNotes', 'search'), data, function (res) {
            $('.notesListingJs').html(res);
        }, {process: process});
    };
    clearNotesSearch = function () {
        document.frmNotesSearch.reset();
        $('.notesHeadJs .form-search__action--reset').hide();
        notesSearch(document.frmNotesSearch);
    };
    goToNotesSearchPage = function (page) {
        var frm = document.frmNotesPaging;
        $(frm.page).val(page);
        notesSearch(frm);
    };
    notesForm = function (id, ordcrsId) {
        fcom.ajax(fcom.makeUrl('LectureNotes', 'form', [id]), {
            'lecnote_lecture_id' : currentLectureId,
            'lecnote_course_id' : courseId,
            'lecnote_ordcrs_id': ordcrsId,
        }, function (res) {
            $.facebox(res);
        });
    };
    $('body').on('input', '#notesKeywordJs', function () {
        var val = $(this).val();
        if (val != '') {
            $('.notesHeadJs .form-search__action--reset').show();
        } else {
            $('.notesHeadJs .form-search__action--reset').hide();
        }
    });
    //lines added by rehan for quiz-lecture starts here
 getQuiz = function() {
  if (currentLectureId > 0) {
    fcom.ajax(
      fcom.makeUrl('Tutorials', 'getQuizStart'),
      {
        'lecture_id': currentLectureId,
        'course_id': courseId,
        'progress_id': $('#progressId').val()
      },
      function (res) {
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
        $('.sidebarPanelJs').css({ 'display': '' });
        $('.quizJs').html(res).show();
        $('.tabsPanelJs').show();
      }
    );
  } else {
    alert('Please select a lecture first');
  }
};
//lines added by rehan end here for quiz-lecture
    setupNotes = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('LectureNotes', 'setup'), data, function (res) {
            clearNotesSearch();
            notesSearch(document.frmNotesSearch, false);
            $.facebox.close();
        });
    };
    removeNotes = function (id) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(fcom.makeUrl('LectureNotes', 'delete'), { 'lecnote_id' : id }, function (res) {
                notesSearch(document.frmNotesSearch);
            });
        }
    };
    goToPendingLecture = function () {
        var lectureId = 0;
        $('.sectionListJs').each(function () {
            if (lectureId < 1) {
                if ($(this).find('input[type="checkbox"]:not(:checked)').length > 0) {
                    lectureId = $(this).find('input[type="checkbox"]:not(:checked):first').val();
                }
            }
        });
        loadLecture(lectureId);
    };
    getAI = function () {
  fcom.ajax(
    fcom.makeUrl('Tutorials', 'getAI'),
    {},
    function (res) {
      $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
      $('.sidebarPanelJs').css({ 'display': '' });
      $('.quizJs').html(res).show();   // reuse quiz pane area for AI UI
      $('.tabsPanelJs').show();
      bindAiTutorUi();
    }
  );
};

function bindAiTutorUi(){
  const $box = $('.quizJs');
  const $messages = $box.find('#aiMessages');
  const $input = $box.find('#aiInput');
  const $send = $box.find('#aiSendBtn');

  // simple client-side history (not saved to DB)
  const history = []; // {role:'user'|'assistant', content:string}

  $box.find('.ai-suggestion').off('click').on('click', function(){
    $input.val($(this).text());
    $input.focus();
  });

  $box.find('#aiClearBtn').off('click').on('click', function(){
    $messages.html('');
    history.length = 0;
  });

  $box.find('#aiConnectBtn').off('click').on('click', function(){
    alert('AI is already wired to your course context.');
  });

  function postUserMessage(txt){
    if (!txt.trim()) return;
    appendUser(txt);
    $input.val('');
    // Compose a brief “recent history” string (not stored server-side)
    const lastTurns = history.slice(-6).map(m => `${m.role.toUpperCase()}: ${m.content}`).join('\n');
    const messageToSend = (lastTurns ? lastTurns + '\n' : '') + `USER: ${txt}`;

    fcom.updateWithAjax(
      fcom.makeUrl('Tutorials', 'aiChat'),
      {
        'lecture_id': currentLectureId,
        'progress_id': $('#progressId').val(),
        'message': messageToSend
      },
      function (res) {
        if (res && res.reply) {
          appendBot(res.reply);
        } else {
          appendBot('Hmm, I could not generate a response.');
        }
      },
      {'process': false}
    );
  }

  function appendUser(text){
    history.push({role:'user', content:text});
    $messages.append(renderUser(text));
    $messages.scrollTop($messages[0].scrollHeight);
  }
  function appendBot(text){
    history.push({role:'assistant', content:text});
    $messages.append(renderBot(text));
    $messages.scrollTop($messages[0].scrollHeight);
  }

  function renderUser(text){
    return `
      <div class="ai-msg ai-msg--user">
        <div class="ai-msg__avatar">You</div>
        <div class="ai-msg__bubble">${escapeHtml(text)}</div>
      </div>`;
  }
  function renderBot(text){
    return `
      <div class="ai-msg ai-msg--bot">
        <div class="ai-msg__avatar">AI</div>
        <div class="ai-msg__bubble">${escapeHtml(text).replace(/\n/g,'<br>')}</div>
      </div>`;
  }
  function escapeHtml(s){
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  $send.off('click').on('click', function(){ postUserMessage($input.val()); });
  $input.off('keydown').on('keydown', function(e){
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      postUserMessage($input.val());
    }
  });
}

});