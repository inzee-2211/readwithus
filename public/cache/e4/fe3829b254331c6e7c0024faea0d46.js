$(document).ready(function () {
    /* SIDE BAR SCROLL DYNAMIC HEIGHT */
    $('.sidebar__body').css('height', 'calc(100% - ' + $('.sidebar__head').innerHeight() + 'px');
    $(window).resize(function () {
        $('.sidebar__body').css('height', 'calc(100% - ' + $('.sidebar__head').innerHeight() + 'px');
    });
    /* COMMON TOGGLES */
    var _body = $('html');
    var _toggle = $('.trigger-js');
    _toggle.each(function () {
        var _this = $(this), _target = $(_this.attr('href'));
        _this.on('click', function (e) {
            e.preventDefault();
            _target.toggleClass('is-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-toggle');
        });
    });
    /* FOR FULL SCREEN TOGGLE */
    var _body = $('html');
    var _toggle = $('.fullview-js');
    _toggle.each(function () {
        var _this = $(this),
                _target = $(_this.attr('href'));
        _this.on('click', function (e) {
            e.preventDefault();
            _target.toggleClass('is-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-fullview');
        });
    });
    /* FOR FOOTER */
    if ($(window).width() < 767) {
        /* FOR FOOTER TOGGLES */
        $('.toggle-trigger-js').click(function () {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active');
                $(this).siblings('.toggle-target-js').slideUp();
                return false;
            }
            $('.toggle-trigger-js').removeClass('is-active');
            $(this).addClass("is-active");
            $('.toggle-target-js').slideUp();
            $(this).siblings('.toggle-target-js').slideDown();
        });
    }
    /* FOR STICKY HEADER */
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = $('.header').outerHeight();
    $(window).scroll(function (event) {
        didScroll = true;
    });
    setInterval(function () {
        if (didScroll) {
            hasScrolled();
            didScroll = false;
        }
    }, 250);
    function hasScrolled() {
        var st = $(this).scrollTop();
        if (Math.abs(lastScrollTop - st) <= delta)
            return;
        if (st > lastScrollTop && st > navbarHeight) {
            $('.header').removeClass('nav-down').addClass('nav-up');
        } else {
            if (st + $(window).height() < $(document).height()) {
                $('.header').removeClass('nav-up').addClass('nav-down');
            }
        }
        lastScrollTop = st;
    }
    $(".settings__trigger-js").click(function () {
        var t = $(this).parents(".toggle-group").children(".settings__target-js").is(":hidden");
        $(".toggle-group .settings__target-js").hide();
        $(".toggle-group .settings__trigger-js").removeClass("is--active");
        if (t) {
            $(this).parents(".toggle-group").children(".settings__target-js").toggle().parents(".toggle-group").children(".settings__trigger-js").addClass("is--active")
        }
    });
    $(".toggle--nav-js").click(function () {
        $(this).toggleClass("is-active");
        $('html').toggleClass("show-nav-js");
        $('html').removeClass("show-dashboard-js");
    });

});

/* FOR HEADER TOGGLES */
if ($(window).width() < 1200) {
    $('.nav__dropdown-trigger-js').click(function () {
        $('html').toggleClass("show-dropdown-js");
        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            $(this).siblings('.nav__dropdown-target-js').slideUp();
            return false;
        }
        $('.nav__dropdown-trigger-js').removeClass('is-active');
        $(this).addClass("is-active");
        $('.nav__dropdown-target-js').slideUp();
        $(this).siblings('.nav__dropdown-target-js').slideDown();
    });
}
/* FOR COMMON DROPDOWN */
$('.nav__dropdown-js').each(function () {
    $(this).click(function () {
        $(this).parent('.nav__item').toggleClass("is-active");
        $("html").toggleClass("toggled-user");
        return false;
    });
})
$('html').click(function () {
    if ($('.nav__item').hasClass('is-active')) {
        $('.nav__item').removeClass('is-active');
    }
});
$('.nav__item').click(function (e) {
    e.stopPropagation();
});
/* FOR MOBILE CANVAS MENU */
$(".nav__toggle-js").click(function () {
    $("html").toggleClass("show--mobile-nav");
    return false;
});
/* FOR DESKTOP NAVIGATION */
if ($(window).width() > 1200) {
    var elBody = $("html");
    $('.nav--primary > ul > li.nav__has-child').mouseenter(function () {
        $(this).toggleClass("is-active");
        elBody.toggleClass("show--main-nav");
        return false;
    }).mouseleave(function () {
        $(this).toggleClass("is-active");
        elBody.toggleClass("show--main-nav");
        return false;
    });
}
/* FUNCTION FOR COMMON DROPDOWN */
jQuery(document).ready(function (e) {
    function t(t) {
        e(t).bind("click", function (t) {
            t.preventDefault();
            e(this).parent().fadeOut()
        })
    }
    e(".toggle__trigger-js").click(function () {
        var t = e(this).parents(".toggle-group").children(".toggle__target-js").is(":hidden");
        e(".toggle-group .toggle__target-js").hide();
        e(".toggle-group .toggle__trigger-js").removeClass("is-active");
        if (t) {
            e(this).parents(".toggle-group").children(".toggle__target-js").toggle().parents(".toggle-group").children(".toggle__trigger-js").addClass("is-active")
        }
    });
    e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("toggle-group"))
            e(".toggle-group .toggle__target-js").hide();
    });
    e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("toggle-group"))
            e(".toggle-group .toggle__trigger-js").removeClass("is-active");
    })
});
var _carousel = $('.js-carousel');
_carousel.each(function () {
    var _this = $(this),
            _slidesToShow = (_this.data("slides")).toString().split(',');
    _this.slick({
        slidesToShow: parseInt(_slidesToShow.length > 0 ? _slidesToShow[0] : "3"),
        slidesToScroll: 1,
        arrows: _this.data("arrows"),
        dots: _this.data("dots"),
        infinite: true,
        autoplay: true,
        pauseOnHover: true,
        responsive: [
            {breakpoint: 1200, settings: {slidesToShow: parseInt(parseInt(_slidesToShow.length > 1 ? _slidesToShow[1] : "2"))}},
            {breakpoint: 768, settings: {slidesToShow: parseInt(parseInt(_slidesToShow.length > 2 ? _slidesToShow[2] : "1"))}},
            {breakpoint: 576, settings: {slidesToShow: parseInt(parseInt(_slidesToShow.length > 3 ? _slidesToShow[3] : "1"))}}
        ]
    });
});
$('.vert-carousel').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    arrow: true,
    vertical: true
});
$.loader = {
    selector: '.loading-wrapper',
    show: function () {
        $(this.selector).show();
    },
    hide: function () {
        $(this.selector).hide();
    }
};