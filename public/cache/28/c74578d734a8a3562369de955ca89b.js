(function(c){var b,a;a=typeof window!=="undefined"&&window!==null?window:global;a.BarRating=b=(function(){function d(){this.show=function(){var g=c(this.elem),j,f,h=this.options,e,i;if(!g.data("barrating")){if(h.initialRating){i=c('option[value="'+h.initialRating+'"]',g)}else{i=c("option:selected",g)}g.data("barrating",{currentRatingValue:i.val(),currentRatingText:i.text(),originalRatingValue:i.val(),originalRatingText:i.text()});j=c("<div />",{"class":"br-widget"}).insertAfter(g);g.find("option").each(function(){var n,m,l,k;n=c(this).val();if(n){m=c(this).text();l=c("<a />",{href:"#","data-rating-value":n,"data-rating-text":m});k=c("<span />",{text:(h.showValues)?m:""});j.append(l.append(k))}});if(h.showSelectedRating){j.append(c("<div />",{text:"","class":"br-current-rating"}))}g.data("barrating").deselectable=(!g.find("option:first").val())?true:false;if(h.reverse){e="nextAll"}else{e="prevAll"}if(h.reverse){j.addClass("br-reverse")}if(h.readonly){j.addClass("br-readonly")}j.on("ratingchange",function(k,l,m){l=l?l:g.data("barrating").currentRatingValue;m=m?m:g.data("barrating").currentRatingText;g.find('option[value="'+l+'"]').prop("selected",true);if(h.showSelectedRating){c(this).find(".br-current-rating").text(m)}}).trigger("ratingchange");j.on("updaterating",function(k){c(this).find('a[data-rating-value="'+g.data("barrating").currentRatingValue+'"]').addClass("br-selected br-current")[e]().addClass("br-selected")}).trigger("updaterating");f=j.find("a");f.on("touchstart",function(k){k.preventDefault();k.stopPropagation();c(this).click()});if(h.readonly){f.on("click",function(k){k.preventDefault()})}if(!h.readonly){f.on("click",function(k){var m=c(this),l,n;k.preventDefault();f.removeClass("br-active br-selected");m.addClass("br-selected")[e]().addClass("br-selected");l=m.attr("data-rating-value");n=m.attr("data-rating-text");if(m.hasClass("br-current")&&g.data("barrating").deselectable){m.removeClass("br-selected br-current")[e]().removeClass("br-selected br-current");l="",n=""}else{f.removeClass("br-current");m.addClass("br-current")}g.data("barrating").currentRatingValue=l;g.data("barrating").currentRatingText=n;j.trigger("ratingchange");h.onSelect.call(this,g.data("barrating").currentRatingValue,g.data("barrating").currentRatingText);return false});f.on({mouseenter:function(){var k=c(this);f.removeClass("br-active").removeClass("br-selected");k.addClass("br-active")[e]().addClass("br-active");j.trigger("ratingchange",[k.attr("data-rating-value"),k.attr("data-rating-text")])}});j.on({mouseleave:function(){f.removeClass("br-active");j.trigger("ratingchange").trigger("updaterating")}})}g.hide()}};this.clear=function(){var e=c(this.elem);var f=e.next(".br-widget");if(f&&e.data("barrating")){f.find("a").removeClass("br-selected br-current");e.data("barrating").currentRatingValue=e.data("barrating").originalRatingValue;e.data("barrating").currentRatingText=e.data("barrating").originalRatingText;f.trigger("ratingchange").trigger("updaterating");this.options.onClear.call(this,e.data("barrating").currentRatingValue,e.data("barrating").currentRatingText)}};this.destroy=function(){var f=c(this.elem);var h=f.next(".br-widget");if(h&&f.data("barrating")){var e=f.data("barrating").currentRatingValue;var g=f.data("barrating").currentRatingText;f.removeData("barrating");h.off().remove();f.show();this.options.onDestroy.call(this,e,g)}}}d.prototype.init=function(f,g){var e;e=this;e.elem=g;return e.options=c.extend({},c.fn.barrating.defaults,f)};return d})();c.fn.barrating=function(e,d){return this.each(function(){var f=new b();if(!c(this).is("select")){c.error("Sorry, this plugin only works with select fields.")}if(f.hasOwnProperty(e)){f.init(d,this);return f[e]()}else{if(typeof e==="object"||!e){d=e;f.init(d,this);return f.show()}else{c.error("Method "+e+" does not exist on jQuery.barrating")}}})};return c.fn.barrating.defaults={initialRating:null,showValues:false,showSelectedRating:true,reverse:false,readonly:false,onSelect:function(d,e){},onClear:function(d,e){},onDestroy:function(d,e){}}})(jQuery);$(document).ready(function() {

    /* SIDE BAR SCROLL DYNAMIC HEIGHT */ 
    $('.sidebar__body').css('height', 'calc(100% - ' +$('.sidebar__head').innerHeight()+'px');

    $(window).resize(function(){
        $('.sidebar__body').css('height', 'calc(100% - ' +$('.sidebar__head').innerHeight()+'px');
    });



    /* COMMON TOGGLES */ 
    var _body = $('html');
    var _toggle = $('.trigger-js');
    _toggle.each(function(){
    var _this = $(this),
        _target = $(_this.attr('href'));

        _this.on('click', function(e){
            e.preventDefault();
            _target.toggleClass('is-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-toggle');
        });
    });


    /* FOR FULL SCREEN TOGGLE */
    var _body = $('html');
    var _toggle = $('.fullview-js');
    _toggle.each(function(){
    var _this = $(this),
        _target = $(_this.attr('href'));

        _this.on('click', function(e){
            e.preventDefault();
            _target.toggleClass('is-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-fullview');
        });
    });
    

    /* FOR FULL FILTER TOGGLE */
    var _body = $('html');
    var _toggle = $('.fullview-js');
    _toggle.each(function(){
    var _this = $(this),
        _target = $(_this.attr('href'));

        _this.on('click', function(e){
            e.preventDefault();
            _target.toggleClass('is-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-fullview');
        });
    });

    /* FOR FOOTER */
    if( $(window).width() < 767 ){
        /* FOR FOOTER TOGGLES */
        $('.toggle-trigger-js').click(function(){
        if($(this).hasClass('is-active')){
            $(this).removeClass('is-active');
            $(this).siblings('.toggle-target-js').slideUp();return false;
        }
        $('.toggle-trigger-js').removeClass('is-active');
        $(this).addClass("is-active");
            $('.toggle-target-js').slideUp();
            $(this).siblings('.toggle-target-js').slideDown();
        });
    }

    /* FOR STICKY HEADER */    
    // Hide Header on on scroll down
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = $('.header').outerHeight();

    $(window).scroll(function(event){
        didScroll = true;
    });

    setInterval(function() {
        if (didScroll) {
            hasScrolled();
            didScroll = false;
        }
    }, 250);

    function hasScrolled() {
        var st = $(this).scrollTop();
        
        // Make sure they scroll more than delta
        if(Math.abs(lastScrollTop - st) <= delta)
            return;
        
        // If they scrolled down and are past the navbar, add class .nav-up.
        // This is necessary so you never see what is "behind" the navbar.
        if (st > lastScrollTop && st > navbarHeight){
            // Scroll Down
            $('.header').removeClass('nav-down').addClass('nav-up');
        } else {
            // Scroll Up
            if(st + $(window).height() < $(document).height()) {
                $('.header').removeClass('nav-up').addClass('nav-down');
            }
        }
        
        lastScrollTop = st;
    }

    $(".toggle--nav-js").click(function () {
        $(this).toggleClass("is-active");
        $('html').toggleClass("show-nav-js");
        $('html').removeClass("show-dashboard-js");
      });
     
});var tutor = false;
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
});