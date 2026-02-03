var tutor = false;
var notes = false;
var lecture = false;
var reviews = false;

$(function () {

    /* ===============================
     *  SHARED HELPERS FOR AVA CHAT
     * =============================== */

      function simpleMarkdownToHtml(input) {
    let text = String(input ?? '');

    // ✅ 0) Normalize FIRST (before escapeHtml)
    // Remove any number of backslashes before math delimiters:
    // \(...\), \\(...\\), \[...\], etc.
    text = text
        .replace(/\\+\(/g, '(')
        .replace(/\\+\)/g, ')')
        .replace(/\\+\[/g, '[')
        .replace(/\\+\]/g, ']')

        // Optional: remove escaping of markdown tokens like \*\*bold\*\*
        .replace(/\\([*_`~])/g, '$1')

        // Optional: common latex token
        .replace(/\\times/g, '×');

    // ✅ 1) Escape HTML (safe)
    text = escapeHtml(text);

    // 2) Extract fenced code blocks first (``` ... ```)
    const codeBlocks = [];
    text = text.replace(/```([\s\S]*?)```/g, function (_, code) {
        const idx = codeBlocks.length;
        codeBlocks.push(code);
        return `@@CODEBLOCK_${idx}@@`;
    });

    // 3) Inline code `code`
    text = text.replace(/`([^`\n]+)`/g, '<code>$1</code>');

    // 4) Bold **text**
    text = text.replace(/\*\*([^\n*][\s\S]*?)\*\*/g, '<strong>$1</strong>');

    // 5) Italic *text*
    text = text.replace(/(^|[^*])\*([^*\n]+)\*(?!\*)/g, '$1<em>$2</em>');

    // 6) Links [label](url)
    text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, function (_, label, url) {
        url = url.trim();
        if (!/^https?:\/\//i.test(url)) return label;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer">${label}</a>`;
    });

    // 7) Unordered lists
    const lines = text.split('\n');
    let out = [];
    let inUl = false;

    function closeUl() {
        if (inUl) { out.push('</ul>'); inUl = false; }
    }

    lines.forEach(line => {
        const m = line.match(/^\s*[-*]\s+(.*)$/);
        if (m) {
            if (!inUl) { out.push('<ul>'); inUl = true; }
            out.push('<li>' + m[1] + '</li>');
        } else {
            closeUl();
            out.push(line);
        }
    });
    closeUl();

    text = out.join('\n');

    // 8) Newlines -> <br>
    text = text.replace(/\n/g, '<br>');

    // 9) Restore code blocks
    text = text.replace(/@@CODEBLOCK_(\d+)@@/g, function (_, n) {
        const code = codeBlocks[Number(n)] ?? '';
        const escapedCode = escapeHtml(code);
        return `<pre><code>${escapedCode}</code></pre>`;
    });

    return text;
}

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[m];
        });
    }

    function renderUser(text) {
        return (
            '<div class="ai-msg ai-msg--user">' +
            '  <div class="ai-msg__avatar">You</div>' +
            '  <div class="ai-msg__bubble">' + escapeHtml(text) + '</div>' +
            '</div>'
        );
    }
 

function renderBot(text) {
    const html = simpleMarkdownToHtml(text);

    return (
        '<div class="ai-msg ai-msg--bot">' +
        '  <div class="ai-msg__avatar">AI</div>' +
        '  <div class="ai-msg__bubble ai-md">' + html + '</div>' +
        '</div>'
    );
}



    // Build a unique key per (learner + course + lecture)
    function aiStorageKey() {
        if (typeof learnerId === 'undefined') return null;
        if (typeof courseId === 'undefined') return null;
        if (typeof currentLectureId === 'undefined') return null;
        return 'avaChat_' + learnerId + '_' + courseId + '_' + currentLectureId;
    }

    // Load history from localStorage and render it (if present)
    function loadChatFromStorage($messages) {
        var history = [];
        var key = aiStorageKey();
        if (!key || !window.localStorage) {
            return history;
        }

        var raw = localStorage.getItem(key);
        if (!raw) {
            // No saved history → keep whatever HTML backend rendered
            return history;
        }

        try {
            history = JSON.parse(raw) || [];
        } catch (e) {
            history = [];
        }

        if (history.length > 0) {
            // Replace default welcome with actual history
            $messages.empty();
            history.forEach(function (m) {
                if (m.role === 'user') {
                    $messages.append(renderUser(m.content));
                } else {
                    $messages.append(renderBot(m.content));
                }
            });
            $messages.scrollTop($messages[0].scrollHeight);
        }

        return history;
    }

    function saveChatToStorage(history) {
        var key = aiStorageKey();
        if (!key || !window.localStorage) return;
        try {
            localStorage.setItem(key, JSON.stringify(history));
        } catch (e) {
            // ignore storage errors
        }
    }

    function clearChatStorage() {
        var key = aiStorageKey();
        if (!key || !window.localStorage) return;
        localStorage.removeItem(key);
    }

    /* ===============================
     *  EXISTING LECTURE / NOTES / QUIZ LOGIC
     *  (UNCHANGED)
     * =============================== */

    loadLectureShowmsg = function (lectureId) {
        alert('You need to attempt and qualify this lecture quiz before proceeding to the next lecture.');
    };

    loadLecture = function (lectureId) {
        if (lecture == false || lectureId > 0) {
            var progressId = $('#progressId').val();
            fcom.ajax(
                fcom.makeUrl('Tutorials', 'getLectureData', [lectureId, progressId]),
                '',
                function (res) {
                    $('.lectureDetailJs').html(res);
                    setCurrentLecture(lectureId);
                    getVideo(lectureId);
                    lecture = true;
                }
            );
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
        $('.sidebarPanelJs').css({ 'display': '' });
        $('.tutorialTabsJs ul li').removeClass('is-active');
        $('.crsDetailTabJs').parent().addClass('is-active');
        $('.lectureDetailJs, .tabsPanelJs').show();
    };

    getVideo = function (lectureId) {
        var progressId = $('#progressId').val();
        fcom.ajax(
            fcom.makeUrl('Tutorials', 'getVideo', [lectureId, progressId]),
            '',
            function (res) {
                $('.videoContentJs').html(res);
                // after video loads, maybe show Ava intro for this lecture
                maybeShowAvaIntro(lectureId);
            },
            { 'process': false }
        );
    };

    /**
     * Show the Ava onboarding dialog only once per (user + lecture).
     */
    function maybeShowAvaIntro(lectureId) {
        // safety checks
        if (typeof learnerId === 'undefined' || !window.localStorage) {
            return;
        }
        if (!lectureId) {
            return;
        }

        var key = 'avaIntroSeen_' + learnerId + '_' + lectureId;

        // If user already saw this intro for this lecture, do nothing
        if (localStorage.getItem(key) === '1') {
            return;
        }

        showAvaIntroModal(lectureId, key);
    }

    function showAvaIntroModal(lectureId, storageKey) {
        var $overlay = $('#avaIntroOverlay');
        var $textEl = $('#avaIntroText');

        if ($overlay.length === 0 || $textEl.length === 0) {
            return; // HTML not present
        }

        // Block background
        $('body').addClass('avaIntro-open');
        $overlay.show();

        // --- Typewriter text content ---
        var lines = [
            "Hi, I’m Ava, your AI study tutor. 👋",
            "",
            "Here’s how this lecture works:",
            "1) First, watch the lecture videos carefully.",
            "2) Check the relevent resources and take notes.",
            "3) Then attempt the short quiz for this lecture.",
            "4) If you get stuck or something feels confusing, ask me anything about this lecture.",
            "If you still struggle even after my help, I can recommend a human tutor for extra support.",
            "",
            "Do you understand this learning flow?"
        ];
        var fullText = lines.join('\n');
        var idx = 0;
        var speed = 28; // ms per character

        $textEl.text('');

        function typeNextChar() {
            if (idx > fullText.length) return;
            $textEl.text(fullText.slice(0, idx));
            idx++;
            if (idx <= fullText.length) {
                setTimeout(typeNextChar, speed);
            }
        }
        typeNextChar();

        // Helper to close dialog
        function closeIntro() {
            $overlay.fadeOut(180, function () {
                $('body').removeClass('avaIntro-open');
            });
        }

        // Button: YES (understood)
        $('#avaIntroYes').off('click').on('click', function () {
            if (storageKey) {
                localStorage.setItem(storageKey, '1');
            }
            closeIntro();
        });

        // Button: NO (go to Ava)
        $('#avaIntroNo').off('click').on('click', function () {
            if (storageKey) {
                localStorage.setItem(storageKey, '1');
            }
            closeIntro();

            // Switch to AI tab after a tiny delay (to avoid race)
            setTimeout(function () {
                // Activate AVA tab in UI
                $('.tutorialTabsJs ul li').removeClass('is-active');
                $('.tutorialTabsJs ul li:has(a[onclick*="getAI"])').addClass('is-active');

                // Load AI panel
                getAI();
            }, 220);
        });
    }

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
        fcom.updateWithAjax(
            fcom.makeUrl('Tutorials', 'getLecture', [next]),
            { 'progress_id': progressId },
            function (res) {
                lecture = false;
                if (lectureCompleted == 1) {
                    markComplete(res.previous_lecture_id, 1);
                }
                loadLecture(res.next_lecture_id);
                setProgress();
            }
        );
    };
// called by feedback-form.php onsubmit="feedbackSetup(this); return(false);"
feedbackSetup = function (frm) {
    if (!$(frm).validate()) return;

    fcom.updateWithAjax(
        fcom.makeUrl('Tutorials', 'feedbackSetup'),
        fcom.frmData(frm),
        function (res) {
            $.facebox.close();
            // refresh reviews tab content
            if (typeof getReviews === 'function') {
                getReviews();
            }
        }
    );
};

window.searchReviews = function (page) {
    page = page || 1;

    var $frm = $('#reviewFrm');
    if (!$frm.length) return;

    $frm.find('input[name="pageno"]').val(page);

    fcom.ajax(fcom.makeUrl('Tutorials', 'searchReviews'), $frm.serialize(), function (res) {
        $('.reviewsListingJs').html(res);
    });
};


    markComplete = function (lectureId, status) {
        fcom.updateWithAjax(
            fcom.makeUrl('Tutorials', 'markComplete'),
            {
                'status': status,
                'lecture_id': lectureId,
                'progress_id': $('#progressId').val()
            },
            function () {
                var obj = $('#lectureJs' + lectureId).find('input[type="checkbox"]');
                if (status == 1) {
                    $(obj).prop('checked', true);
                    $('#btnComplete' + lectureId).addClass('btn--disabled');
                } else {
                    $('#btnComplete' + lectureId).removeClass('btn--disabled');
                }
                var sectionId = $(obj).data('section');
                $('.completedLecture' + sectionId).text(
                    $(obj).parents('.lecturesListJs').find('input[type="checkbox"]:checked').length
                );
                setProgress();
            },
            { 'process': false }
        );
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
        fcom.updateWithAjax(
            fcom.makeUrl('Tutorials', 'setProgress'),
            { 'progress_id': progressId },
            function (res) {
                var lbl = langLbl.courseProgressPercent;
                lbl = lbl.replace("{percent}", res.progress);
                $('.progressPercent').html(lbl);
                $('#progressBarJs').prop('style', "--percent:" + parseInt(res.progress));
                if (res.is_completed == true) {
                    window.location = fcom.makeUrl('Tutorials', 'completed', [progressId]);
                }
            },
            { 'process': false }
        );
    };

    if (currentLectureId > 0) {
        loadLecture(currentLectureId);
    } else {
        getLecture();
    }

    getTutorInfo = function () {
        if (tutor == false) {
            fcom.ajax(
                fcom.makeUrl('Tutorials', 'getTeacherDetail'),
                { 'course_id': courseId },
                function (res) {
                    $('.tutorInfoJs').html(res);
                    tutor = true;
                }
            );
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs , .quizJs').hide();
        $('.sidebarPanelJs').css({ 'display': '' });
        $('.tutorInfoJs, .tabsPanelJs').show();
    };

    AttampQuiz = function (quiz_id, courseId, lectureId) {
        fcom.ajax(
            fcom.makeUrl('Tutorials', 'getQuizinfo'),
            { 'quiz_id': quiz_id, 'courseId': courseId, 'lectureId': lectureId },
            function (res) {
                $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs , .quizJs').hide();
                $('.sidebarPanelJs').css({ 'display': '' });
                $('.quizJs').html(res).show();
                $('.tabsPanelJs').show();
                // searchReviews();
            }
        );
    };

    getReviews = function () {
        var progressId = $('#progressId').val();
        fcom.ajax(
            fcom.makeUrl('Tutorials', 'getReviews'),
            { 'course_id': courseId, 'progress_id': progressId },
            function (res) {
                $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
                $('.sidebarPanelJs').css({ 'display': '' });
                $('.reviewsJs').html(res).show();
                $('.tabsPanelJs').show();
                searchReviews();
            }
        );
    };

    searchReviews = function () {
        var data = fcom.frmData(document.reviewFrm);
        fcom.ajax(
            fcom.makeUrl('Tutorials', 'searchReviews'),
            data,
            function (res) {
                $('.reviewSrchListJs').remove();
                $('.reviewsListJs').after(res);
            }
        );
    };

    goToReviewsSearchPage = function (page) {
        var frm = document.reviewFrm;
        $(frm.pageno).val(page);
        searchReviews(frm);
    };

 feedbackForm = function (progressId) {
    fcom.ajax(
        fcom.makeUrl('Tutorials', 'feedbackForm', [progressId]),
        {}, // no need to send ordcrs_id anymore
        function (res) {
            $.facebox(res, 'facebox-medium');
        }
    );
};


    // feedbackSetup = function (frm) {
    //     if (!$(frm).validate()) {
    //         return;
    //     }
    //     fcom.updateWithAjax(
    //         fcom.makeUrl('Tutorials', 'feedbackSetup'),
    //         fcom.frmData(frm),
    //         function (res) {
    //             $.facebox.close();
    //             $('.reviewFrmJs').removeAttr('onclick').addClass('btn--disabled');
    //         }
    //     );
    // };

    getNotes = function (id) {
        if (notes == false) {
            fcom.ajax(
                fcom.makeUrl('LectureNotes', 'index'),
                { 'course_id': courseId, 'ordcrs_id': id },
                function (res) {
                    $('.notesJs').html(res);
                    notesSearch(document.frmNotesSearch);
                    notes = true;
                }
            );
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
        $('.sidebarPanelJs').css({ 'display': '' });
        $('.notesJs, .tabsPanelJs').show();
    };

    notesSearch = function (frm, process = true) {
        var data = fcom.frmData(frm);
        fcom.ajax(
            fcom.makeUrl('LectureNotes', 'search'),
            data,
            function (res) {
                $('.notesListingJs').html(res);
            },
            { process: process }
        );
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
        fcom.ajax(
            fcom.makeUrl('LectureNotes', 'form', [id]),
            {
                'lecnote_lecture_id': currentLectureId,
                'lecnote_course_id': courseId,
                'lecnote_ordcrs_id': ordcrsId
            },
            function (res) {
                $.facebox(res);
            }
        );
    };

    $('body').on('input', '#notesKeywordJs', function () {
        var val = $(this).val();
        if (val != '') {
            $('.notesHeadJs .form-search__action--reset').show();
        } else {
            $('.notesHeadJs .form-search__action--reset').hide();
        }
    });

    // lines added by rehan for quiz-lecture starts here
    getQuiz = function () {
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
    // lines added by rehan end here for quiz-lecture

    setupNotes = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(
            fcom.makeUrl('LectureNotes', 'setup'),
            data,
            function (res) {
                clearNotesSearch();
                notesSearch(document.frmNotesSearch, false);
                $.facebox.close();
            }
        );
    };

    removeNotes = function (id) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(
                fcom.makeUrl('LectureNotes', 'delete'),
                { 'lecnote_id': id },
                function (res) {
                    notesSearch(document.frmNotesSearch);
                }
            );
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

    /* ===============================
     *  AVA TUTOR TAB + PERSISTENT CHAT
     * =============================== */

    getAI = function () {
        fcom.ajax(
            fcom.makeUrl('Tutorials', 'getAI'),
            {},
            function (res) {
                $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs, .quizJs').hide();
                $('.sidebarPanelJs').css({ 'display': '' });
                $('.quizJs').html(res).show();
                $('.tabsPanelJs').show();

                // highlight AVA tab
                $('.tutorialTabsJs ul li').removeClass('is-active');
                $('.tutorialTabsJs ul li:has(a[onclick*="getAI"])').addClass('is-active');

                bindAiTutorUi();

                // Load dynamic intro only if there is no prior history
                loadAvaDynamicIntro();
            }
        );
    };

    function loadAvaDynamicIntro() {
        var progressId = $('#progressId').val();
        const $messages = $('#aiMessages');

        // If user already has chat restored or more than one message,
        // or welcome bubble is gone, do not override.
        var hasWelcome = $messages.find('.ai-welcome').length > 0;
        if ($messages.children().length > 1 || !hasWelcome) {
            return;
        }

        fcom.updateWithAjax(
            fcom.makeUrl('Tutorials', 'getAIIntro'),
            {
                lecture_id: currentLectureId,
                progress_id: progressId
            },
            function (res) {
                if (res && res.intro) {
                    // Append intro below the welcome message
                    $messages.append(renderBot(res.intro));
                }
            },
            { process: false }
        );
    }

    function bindAiTutorUi() {
        const $box = $('.quizJs');
        const $messages = $box.find('#aiMessages');
        const $input = $box.find('#aiInput');
        const $send = $box.find('#aiSendBtn');

        // Load stored history (if any) and render it
        let history = loadChatFromStorage($messages); // [{role, content}]

        $box.find('.ai-suggestion').off('click').on('click', function () {
            $input.val($(this).text());
            $input.focus();
        });

        $box.find('#aiClearBtn').off('click').on('click', function () {
            $messages.html('');
            history = [];
            clearChatStorage();
        });

        // If you ever add #aiConnectBtn in markup, this remains harmless
        $box.find('#aiConnectBtn').off('click').on('click', function () {
            alert('AI is already wired to your course context.');
        });

        function postUserMessage(txt) {
            if (!txt.trim()) return;
            appendUser(txt);
            $input.val('');

            // Compose a brief “recent history” string (not stored server-side)
            const lastTurns = history
                .slice(-6)
                .map(m => (m.role.toUpperCase() + ': ' + m.content))
                .join('\n');

            const messageToSend =
                (lastTurns ? lastTurns + '\n' : '') +
                'USER: ' + txt;

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
                { 'process': false }
            );
        }

        function appendUser(text) {
            history.push({ role: 'user', content: text });
            $messages.append(renderUser(text));
            $messages.scrollTop($messages[0].scrollHeight);
            saveChatToStorage(history);
        }

        function appendBot(text) {
            history.push({ role: 'assistant', content: text });
            $messages.append(renderBot(text));
            $messages.scrollTop($messages[0].scrollHeight);
            saveChatToStorage(history);
        }

        $send.off('click').on('click', function () {
            postUserMessage($input.val());
        });

        $input.off('keydown').on('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                postUserMessage($input.val());
            }
        });
    }

});
