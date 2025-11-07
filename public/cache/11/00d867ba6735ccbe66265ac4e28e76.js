/* global fcom, bpCategoryId */
$(document).ready(function () {
    searchBlogs(document.frmBlogSearch);
    $('.toggle-nav--vertical-js').click(function () {
        $(this).toggleClass("active");
        if ($(window).width() < 990) {
            $('.nav--vertical-js').slideToggle();
        }
    });
});
(function () {
    bannerAdds = function () {
        fcom.process();
        fcom.ajax(fcom.makeUrl('Banner', 'blogPage'), '', function (res) {
            $("#div--banners").html(res);
        });
    };
    var dv = '#listing';
    reloadListing = function () {
        searchBlogs(document.frmBlogSearch);
    };
    searchBlogs = function (frm, append) {
        if (typeof append == undefined || append == null) {
            append = 0;
        }
        var data = fcom.frmData(frm);
        if (bpCategoryId) {
            data += '&categoryId=' + bpCategoryId;
        }
        fcom.updateWithAjax(fcom.makeUrl('Blog', 'search'), data, function (ans) {
            if (append == 1) {
                $(dv).append(ans.html);
            } else {
                $(dv).html(ans.html);
            }
            if ($("#loadMoreBtnDiv").length) {
                $("#loadMoreBtnDiv").html(ans.loadMoreBtnHtml);
            }
        });
    };
    goToSearchPage = function (page) {
        var frm = document.frmBlogSearchPaging;
        $(frm.page).val(page);
        searchBlogs(frm);
    };
})();