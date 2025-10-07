/* global langLbl, fcom, boolLoadComments */
$(document).ready(function () {
    $('.toggle-nav--vertical-js').click(function () {
        $(this).toggleClass("active");
        if ($(window).width() < 990) {
            $('.nav--vertical-js').slideToggle();
        }
    });
    /* blog slider */
    if (langLbl.layoutDirection == 'rtl') {
        $('.post__pic').slick({ dots: false, arrows: true, autoplay: true, rtl: true, pauseOnHover: false, });
    } else {
        $('.post__pic').slick({ dots: false, arrows: true, autoplay: true, pauseOnHover: false, });
    }
});
$(document).delegate(".link--post-comment-form", 'click', function () {
    $('html, body').animate({ scrollTop: $("#container--comment-form").offset().top - 150 }, 'slow');
    fcom.scrollToTop($("#container--comment-form"));
});
(function () {
    bannerAdds = function () {
        fcom.process();
        fcom.ajax(fcom.makeUrl('Banner', 'blogPage'), '', function (res) {
            $("#div--banners").html(res);
            if ($(window).width() < 990) {
                $('.grids').masonry({ itemSelector: '.grids__item', });
            }
        });
    };
    searchBlogss = function (frm) {
        fcom.updateWithAjax(fcom.makeUrl('Blog', 'search'), fcom.frmData(frm), function (ans) {
            $("#listItem").html(ans.html);
        });
    };
    setupPostComment = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Blog', 'setupPostComment'), fcom.frmData(frm), function (res) {
            frm.reset();
        });
    };
    searchComments = function (blogId, page) {
        page = parseInt(page);
        fcom.ajax(fcom.makeUrl('Blog', 'searchComments'), { blogId: blogId, page: page }, function (res) {
            let divElement = $("#comments--listing");
            if (page > 1) {
                divElement.find("#loadMoreBtn").remove();
            }
            divElement.append(res);
        });
    };
    goToSearchPage = function (page) {
        var frm = document.frmBlogSearchPaging;
        $(frm.page).val(page);
        searchBlogss(frm);
    };
})();