$(document).ready(function() {

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
$(function () {
    getLecture = function (next = 1, lectureCompleted = 0) {
        
        fcom.updateWithAjax(fcom.makeUrl('CoursePreview', 'getLecture', [next]), {
            'course_id': courseId,
            'lecture_id': currentLectureId,
        }, function (res) {
            if (lectureCompleted == 1) {
                var sectionId = $('.lecturesListJs input[type="checkbox"][value="' + currentLectureId +'"]').data('section');
                markComplete(sectionId, currentLectureId);
            }
            loadLecture(res.lecture_id);
           
        });
    };
    loadLecture = function (lectureId) {
        if (lectureId > 0) {
            fcom.ajax(fcom.makeUrl('CoursePreview', 'getLectureData', [courseId, lectureId]), '', function (res) {
                $('.lectureDetailJs').html(res);
                getVideo(lectureId);
                if ($('.lecturesListJs input[type="checkbox"][value="' + lectureId + '"]').is(':checked') == true) {
                    console.log('in');
                    $('#btnComplete' + lectureId).addClass('btn--disabled');
                }
            });
            currentLectureId = lectureId;
            $('.lecturesListJs .lecture, .sectionListJs').removeClass('is-active');
            $('#lectureJs' + lectureId).addClass('is-active');
            $('#lectureJs' + lectureId).parents('.sectionListJs').addClass('is-active');
            $('.lectureTitleJs').text($('#lectureJs' + lectureId + ' .lectureName').text());
            $('.sectionListJs .control-target-js').hide();
            $('#lectureJs' + lectureId).parents('.control-target-js').show();
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs').hide();
        $('.sidebarJs').css({ 'display': '' });
        $('.lectureDetailJs, .tabsPanelJs').show();
        $('.tabs-scrollable-js li').removeClass('is-active');
        $('.crsDetailTabJs').parent().addClass('is-active');
    };
    getVideo = function (lectureId) {
        fcom.ajax(fcom.makeUrl('CoursePreview', 'getVideo', [courseId, lectureId]), '', function (res) {
            $('.videoContentJs').html(res);
        });
    };
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
        getLecture(0);
    });
    $('.lecturesListJs input[type="checkbox"]').change(function () {
        setCompleteCount($(this).data('section'), $(this));
        if ($(this).is(':checked')) {
            $('#btnComplete' + $(this).val()).addClass('btn--disabled');
        } else{
            $('#btnComplete' + $(this).val()).removeClass('btn--disabled');
        }
        setProgress();
    });
    setCompleteCount = function (sectionId, obj) {
        $('.completedLecture' + sectionId).text($(obj).parents('.lecturesListJs').find('input[type="checkbox"]:checked').length);

        var totalLectures = parseInt($('.sidebarJs').find('input[type = "checkbox"]').length);
        var completedLectures = parseInt($('.sidebarJs').find('input[type = "checkbox"]:checked').length);
        var lbl = langLbl.courseProgressPercent;
        var percent = (completedLectures * 100) / totalLectures;
        lbl = lbl.replace("{percent}", percent.toFixed(2));
        $('.progressPercent').html(lbl);
    };
    markComplete = function (sectionId, lectureId) {
        var obj = $('.lecturesListJs input[type="checkbox"][value="' + lectureId +'"]');
        $(obj).prop('checked', true);
        setCompleteCount(sectionId, obj);
        $('#btnComplete' + lectureId).addClass('btn--disabled');
        setProgress();
    };
    setProgress = function () {
        var totalLectures = parseInt($('.sidebarJs').find('input[type="checkbox"]').length);
        var completedLectures = parseInt($('.sidebarJs').find('input[type="checkbox"]:checked').length);
        var percent = (completedLectures * 100) / totalLectures;
        $('#progressBarJs').prop('style', "--percent:" + percent.toFixed(2));
    };
    getTutorInfo = function () {
        if (tutor == false) {
            fcom.ajax(fcom.makeUrl('CoursePreview', 'getTeacherDetail'), { 'course_id': courseId }, function (res) {
                $('.tutorInfoJs').html(res);
                tutor = true;
            });
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs').hide();
        $('.sidebarJs').css({ 'display': '' });
        $('.tutorInfoJs, .tabsPanelJs').show();
    };
    getReviews = function () {
        fcom.ajax(fcom.makeUrl('CoursePreview', 'getReviews'), { 'course_id': courseId }, function (res) {
            $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs').hide();
            $('.sidebarJs').css({ 'display': '' });
            $('.reviewsJs').html(res).show();
            $('.tabsPanelJs').show();
            searchReviews();
        });
    };
    searchReviews = function () {
        var data = fcom.frmData(document.reviewFrm);
        fcom.ajax(fcom.makeUrl('CoursePreview', 'searchReviews'), data, function (res) {
            $('.reviewSrchListJs').remove();
            $('.reviewsListJs').after(res);
        });
    };
    goToReviewsSearchPage = function (page) {
        var frm = document.reviewFrm;
        $(frm.pageno).val(page);
        searchReviews(frm);
    };
    getNotes = function () {
        if (notes == false) {
            fcom.ajax(fcom.makeUrl('LectureNotes', 'index'), { 'course_id': courseId, 'is_preview' : 1 }, function (res) {
                $('.notesJs').html(res);
                notesSearch(document.frmNotesSearch);
                notes = true;
            });
        }
        $('.lectureDetailJs, .notesJs, .reviewsJs, .tutorInfoJs').hide();
        $('.sidebarJs').css({ 'display': '' });
        $('.notesJs, .tabsPanelJs').show();
    };
    notesSearch = function (frm) {
        var data = fcom.frmData(frm);
        data += '&is_preview=1';
        fcom.ajax(fcom.makeUrl('LectureNotes', 'search'), data, function (res) {
            $('.notesListingJs').html(res);
        });
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
    $('body').on('input', '#notesKeywordJs', function () {
        var val = $(this).val();
        if (val != '') {
            $('.notesHeadJs .form-search__action--reset').show();
        } else {
            $('.notesHeadJs .form-search__action--reset').hide();
        }
    });
    getLecture(1);
});