/* global fcom, langLbl, range, LABELS */
$(document).ready(function () {

    $('input[name="keyword"]').on('keyup', function (event) {
        $(".filter-item__search-submit").show();
        $(".filter-item__search-reset").hide();
        if (event.keyCode == 13 && $(this).val() != '') {
            search(document.frmSearch);
            $(".filter-item__search-submit").hide();
            $(".filter-item__search-reset").show();
        }

    });

    searchKeyword = function () {
        var keyword = document.frmSearch.keyword.value;
        if (keyword.trim() != '') {
            $(".filter-item__search-submit").hide();
            $(".filter-item__search-reset").show();
        }
        search(document.frmSearch);
    };

    clearKeyword = function () {
        document.frmSearch.keyword.value = '';
        $(".filter-item__search-submit").show();
        $(".filter-item__search-reset").hide();
        search(document.frmSearch);
    };


    onkeyupLanguage = function () {
        $('.select-teachlang-js').parent().parent().hide();
        var keyword = ($('input[name="teach_language"]').val()).toLowerCase();
        $('.select-teachlang-js:contains("' + keyword + '")').parent().parent().show();
    };

    searchLanguage = function (reset = false) {
        var language = [];
        $('input[name="language[]"]:checked').each(function () {
            language.push($(this).parent().find('.select-option__item').text());
        });
        if (language.length > 0) {
            var placeholder = '<div class="selected-filters"><span class="selected-filters__item">' + language.join(', ') +
                    '</span><span class="selected-filters__action" onclick="clearLanguage();"></span></div>';
            $('input[name="teach_language"]').val('').trigger('keyup');
            $('.teachlang-placeholder-js').html(placeholder);
        } else {
            $('.teachlang-placeholder-js').html(LABELS.allLanguages);
        }
        if (reset === true) {
            return;
        }
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearLanguage = function () {
        $('input[name="teach_language"]').val('').trigger('keyup');
        $('.teachlang-placeholder-js').html(LABELS.allLanguages);
        $('input[name="language[]"]').prop('checked', false);
        search(document.frmSearch);
        $("body").trigger('click');
    };

    searchClasstype = function (reset = false) {
        var classtype = [];
        $('input[name="classtype[]"]:checked').each(function () {
            classtype.push($(this).parent().find('.select-option__item').text());
        });
        if (classtype.length > 0) {
            var placeholder = '<div class="selected-filters"><span class="selected-filters__item">' + classtype.join(', ') +
                    '</span><span class="selected-filters__action" onclick="clearClasstype();"></span></div>';
            $('.classtype-placeholder-js').html(placeholder);
        } else {
            $('.classtype-placeholder-js').html(LABELS.allClassTypes);
        }
        if (reset === true) {
            return;
        }
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearClasstype = function () {
        $('.classtype-placeholder-js').html(LABELS.allClassTypes);
        $('input[name="classtype[]"]').prop('checked', false);
        search(document.frmSearch);
        $("body").trigger('click');
    };


    searchDuration = function (reset = false) {
        var duration = [];
        $('input[name="duration[]"]:checked').each(function () {
            duration.push($(this).parent().find('.select-option__item').text());
        });
        if (duration.length > 0) {
            var placeholder = '<div class="selected-filters"><span class="selected-filters__item">' + duration.join(', ') +
                    '</span><span class="selected-filters__action" onclick="clearDuration();"></span></div>';
            $('.duration-placeholder-js').html(placeholder);
        } else {
            $('.duration-placeholder-js').html(LABELS.allDurations);
        }
        if (reset === true) {
            return;
        }
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearDuration = function () {
        $('.duration-placeholder-js').html(LABELS.allDurations);
        $('input[name="duration[]"]').prop('checked', false);
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearMore = function (field) {
        $('input[name="' + field + '"]').prop('checked', false);
    };

    clearAllMobile = function () {
        $(".filter-item__search-submit").show();
        $(".filter-item__search-reset").hide();
        $('.teachlang-placeholder-js').html(LABELS.allLanguages);
        $('.classtype-placeholder-js').html(LABELS.allClassTypes);
        $('.duration-placeholder-js').html(LABELS.allDurations);
        $('input[name="keyword"]').prop('value', '');
        $('input[name="language[]"]').prop('checked', false);
        $('input[name="classtype[]"]').prop('checked', false);
        $('input[name="duration[]"]').prop('checked', false);
        search(document.frmSearch);
        $("body").trigger('click');
    };

    search = function (frmSearch) {
        closeFilter();
        fcom.process();
        var data = fcom.frmData(frmSearch);
        fcom.ajax(fcom.makeUrl('GroupClasses', 'search'), data, function (response) {
            $('#listing').html(response);
            $(".gototop").trigger('click');
        });
    };

    goToSearchPage = function (pageno) {
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };

    openFilter = function () {
        $("body").addClass('is-filter-show');
        $("#filter-panel").addClass('is-filter-visible');
        setTimeout(function () {
            $('.filters-layout__item-second .filter-item__target').show();
            $('.filters-layout__item-second .filter-item__trigger').addClass('is-active');
        }, 500);
    };

    closeFilter = function () {
        $("body").removeClass('is-filter-show');
        $("#filter-panel").removeClass('is-filter-visible');
    };

    $('.filter-item__trigger-js').click(function (event) {
        if ($(event.target).hasClass('selected-filters__action')) {
            return;
        }
        let isFilterMore = $(this).hasClass('filter-more-js');
        let magaFilter = $('.filters-more');
        let isParMegaBody = $(this).parents('.maga-body-js').length;
        if ($(this).hasClass("is-active")) {
            if (isParMegaBody == 0) {
                $(this).removeClass("is-active").siblings('.filter-item__target-js').slideUp();
                $('body').removeClass('filter-active');
            }
            if (isFilterMore) {
                $('.filters-more .filter-item__trigger-js').removeClass('is-active');
                $('.filters-more .filter-item__target-js').hide();
            }
            return;
        }
        if (isParMegaBody) {
            $('.filters-more .filter-item__trigger-js').removeClass('is-active');
            $('.filters-more .filter-item__target-js').hide();
            $(this).addClass("is-active").siblings('.filter-item__target-js').show();
            if ($(document).width() <= 767) {
                $('.filter-item__trigger-js').removeClass('is-active');
                $('.filter-item__target-js').hide();
                $(this).addClass("is-active").siblings('.filter-item__target-js').slideDown();
            }
        } else {
            $('.filter-item__trigger-js').removeClass('is-active');
            $('.filter-item__target-js').hide();
            $(this).addClass("is-active").siblings('.filter-item__target-js').slideDown();
        }
        $('body').addClass('filter-active');
        if (isFilterMore) {
            let megaBodyItem = magaFilter.find('.filter-item__trigger-js:first');
            megaBodyItem.addClass('is-active').siblings('.filter-item__target-js').show();
        }
    });
    $('body').click(function (e) {
        if ($(e.target).parents('.filter-item').length == 0) {
            $('.filter-item__trigger-js').siblings('.filter-item__target-js').slideUp();
            $('.filter-item__trigger-js').removeClass('is-active');
            $('body').removeClass('filter-active');
        }
    });


    showOffers = function (obj) {
        $(obj).parent('.toggle-dropdown').addClass("is-active");
    };

    hideOffers = function (obj) {
        $(obj).parent('.toggle-dropdown').removeClass("is-active");
    };

    toggleShare = function (element) {
        $(element).parent('.toggle-dropdown').toggleClass("is-active");
    };

    searchLanguage(true);
    searchClasstype(true);
    searchDuration(true);
    search(document.frmSearch);
});

$(window).scroll(function () {
    var body_height = $(".body").position();
    if (typeof body_height !== typeof undefined && body_height.top < $(window).scrollTop()) {
        $("body").addClass("is-filter-fixed");
    } else {
        $("body").removeClass("is-filter-fixed");
    }
});
