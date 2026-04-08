$(document).ready(function () {
    showPreviewVideo = function (courseId) {
        fcom.ajax(fcom.makeUrl('Courses', 'previewVideo', [courseId]), '', function (resp) {
            $.facebox(resp);
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
    $(document).bind('close.facebox', function () {
        $('#facebox .content').empty();
    });
    sortReviews = function (val) {
        document.reviewFrm.sorting.value = val;
        reviews();
    };
    reviews = function () {
        var data = fcom.frmData(document.reviewFrm);
        fcom.ajax(fcom.makeUrl('Courses', 'reviews'), data, function (resp) {
            $('#reviewsListingJs').html(resp);
        });
    };
    reviews();
    gotoPage = function (pageNo) {
        document.reviewFrm.pageno.value = pageNo;
        reviews();
    };
    openMedia = function (rsrcId) {
        fcom.ajax(fcom.makeUrl('Courses', 'resource', [rsrcId]), '', function (resp) {
            if ($('#facebox').length > 0 && $('#facebox').is(':visible')) {
                $('#facebox .content').html(resp);
            } else {
                $.facebox(resp);
            }
        });
    };
    $('.slider-onethird-js').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: false,
        arrows: true,
        adaptiveHeight: true,
        dots: false,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        prevArrow: '<button class="slick-prev cursor-hide" aria-label="Previous" type="button">Previous</button>',
        nextArrow: '<button class="slick-next cursor-hide" aria-label="Next" type="button">Next</button>',
        responsive: [{
                breakpoint: 1199,
                settings: {slidesToShow: 3, dots: true, arrows: false, }
            },
            {
                breakpoint: 1023,
                settings: {slidesToShow: 2, dots: true, arrows: false, }
            },
            {
                breakpoint: 767,
                settings: {slidesToShow: 2, dots: true, arrows: false, }
            },

            {
                breakpoint: 576,
                settings: {slidesToShow: 1, dots: true, arrows: false, }
            }

        ]
    });

    jQuery(window).scroll(checkActiveSection);
    jQuery(document).ready(checkActiveSection);
    jQuery('.page-nav-js li a').click(function (e) {
        var idSectionGoto = jQuery(this).closest('li').data('id');
        $('html, body').stop().animate({
            scrollTop: jQuery('.panels-container-js .panel-content-js[data-id="' + idSectionGoto + '"]').offset().top - 100
        }, 300);
        jQuery('.page-nav-js li').removeClass('is-active');
        jQuery('.page-nav-js li[data-id="' + idSectionGoto + '"]').addClass('is-active');
        e.preventDefault();
    });


    if ($(window).width() < 576) {
        $(".panel-target-js").hide();
        $(".panel-trigger-js").click(function (e) {
            e.preventDefault();
            if ($(this).parents('.panel-content-js').hasClass('is-active')) {
                $(this).siblings('.panel-target-js').slideUp();
                $('.panel-content-js').removeClass('is-active');
            } else {
                $('.panel-content-js').removeClass('is-active');
                $(this).parents('.panel-content-js').addClass('is-active');
                $('.panel-target-js').slideUp();
                $(this).siblings('.panel-target-js').slideDown();
            }
        });
    }
    showLectures = function (obj, id) {
        if ($(obj).hasClass('is-active')) {
            $(obj).removeClass('is-active');
            $('.lecturesListJs' + id).slideUp();
        } else {
            $(obj).addClass('is-active');
            $('.lecturesListJs' + id).slideDown();
        }
    };
    resetEditorHeight();
});

function checkActiveSection() {
    var fromTop = jQuery(window).scrollTop();
    jQuery('.panels-container-js .panel-content-js').each(function () {
        var sectionOffset = jQuery(this).offset();
        let OffsetTop = sectionOffset.top - 120;
        if (OffsetTop <= fromTop) {
            jQuery('.page-nav-js li').removeClass('is-active');
            jQuery('.page-nav-js li[data-id="' + jQuery(this).data('id') + '"]').addClass('is-active');
        }
    });
}