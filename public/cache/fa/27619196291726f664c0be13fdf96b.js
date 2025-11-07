/* global fcom, langLbl */
$("document").ready(function () {
    loadOneThirdSlick();
    $('.toggle-dropdown__link-js').each(function () {
        $(this).click(function () {
            $(this).parent('.toggle-dropdown').toggleClass("is-active");
        });
    });
    $('html').click(function () {
        if ($('.toggle-dropdown').hasClass('is-active')) {
            $('.toggle-dropdown').removeClass('is-active');
        }
    });
    $('.toggle-dropdown').click(function (e) {
        e.stopPropagation();
    });
    $('.tab-a').click(function () {
        $(".tab").removeClass('tab-active');
        $(".tab[data-id='" + $(this).attr('data-id') + "']").addClass("tab-active");
        $(".tab-a").parent('li').removeClass('is-active');
        $(this).parent().addClass('is-active');
    });
    /* FUNCTION FOR LEFT COLLAPSEABLE LINKS */
    if ($(window).width() < 767) {
        $('.box__head-trigger-js').click(function () {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active');
                $(this).siblings('.box__body-target-js').slideUp();
                return false;
            }
            $('.box__head-trigger-js').removeClass('is-active');
            $(this).addClass("is-active");
            $('.box__body-target-js').slideUp();
            $(this).siblings('.box__body-target-js').slideDown();
        });
    }
    $(document).on('change', '#teachLang', function () {
        $(this).parents('.teach-langbody-js').find(".slider--onethird").hide();
        $('div[data-lang-id="' + $(this).val() + '"]').show();
        $($('div[data-lang-id="' + $(this).val() + '"]')).slick('setPosition');
    });
    $('select[name="orderBy"]').change(function () {
        var frm = document.frmReviewSearch;
        $(frm.page).val(1);
        var dv = '#itemRatings';
        $(dv).html('');
        reviews(document.frmReviewSearch);
    });
    loadReviews = function (teacherId, pageno) {
        var data = fcom.frmData(document.reviewFrm);
        data += '&sorting=' + $('select[name="sorting"]').val() + '&pageno=' + pageno + '&teacher_id=' + teacherId;
        fcom.ajax(fcom.makeUrl('Teachers', 'reviews'), data, function (response) {
            if (pageno > 1) {
                $(".show-more-container").remove();
                $('#listing-reviews').append(response);
            } else {
                $('#listing-reviews').html(response);
            }
        });
    };

    toggleCourseFavorite = function (courseId, el) {
        var status = $(el).data('status');
        var data = 'course_id= ' + courseId + '&status=' + status;
        fcom.updateWithAjax(fcom.makeUrl('Courses', 'toggleFavorite', [], confWebDashUrl), data, function (resp) {
            if (status == 0) {
                $(el).data("status", 1).addClass("is-active");
            } else {
                $(el).data("status", 0).removeClass("is-active");
            }
        });
    };
});
function viewCalendar(teacherId) {
    fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar'), {teacherId: teacherId}, function (response) {
        $('#availbility').html(response);
    });
}
loadOneThirdSlick = function () {
    $('.slider--onethird.slider-onethird-js').slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: false,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        arrows: true,
        adaptiveHeight: true,
        dots: false,
        prevArrow: '<button class="slick-prev cursor-hide" aria-label="Previous" type="button">Previous</button>',
        nextArrow: '<button class="slick-next cursor-hide" aria-label="Next" type="button">Next</button>',
        responsive: [
            {breakpoint: 1199, settings: {slidesToShow: 2, arrows: false, dots: true}},
            {breakpoint: 1023, settings: {slidesToShow: 2, arrows: false, dots: true}},
            {breakpoint: 767, settings: {slidesToShow: 1, arrows: false, dots: true}},
            {breakpoint: 576, settings: {slidesToShow: 1, arrows: false, dots: true}}
        ]
    });
};
