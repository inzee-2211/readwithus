$("document").ready(function () {
    var key = $('.card-listing').attr('id');
    if (getCookie(key) == "true") {
        changeTz($('.card-listing').find('.statustab'));
    }
    $('.slider-onethird-js').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: false,
        arrows: true,
        adaptiveHeight: true,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        dots: false,
        prevArrow: '<button class="slick-prev cursor-hide" aria-label="Previous" type="button">Previous</button>',
        nextArrow: '<button class="slick-next cursor-hide" aria-label="Next" type="button">Next</button>',
        responsive: [
            {breakpoint: 1199, settings: {slidesToShow: 2, arrows: false,dots: true }},
            {breakpoint: 1023, settings: {slidesToShow: 2, arrows: false,dots: true}},
            {breakpoint: 767, settings: {slidesToShow: 2, arrows: false,dots: true}},
            {breakpoint: 576, settings: {slidesToShow: 1, arrows: false,dots: true }}
        ]
    });
});
