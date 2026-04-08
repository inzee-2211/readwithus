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
        $('input[name="teachs[]"]:checked').each(function () {
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
        $('input[name="teachs[]"]').prop('checked', false);
        search(document.frmSearch);
        $("body").trigger('click');
    };

    searchPrice = function (reset = false) {
        var price = [];
        if (!isNaN(parseInt($('input[name="price_from"]').val()))) {
            price.push($('input[name="price_from"]').val());
        }
        if (!isNaN(parseInt($('input[name="price_till"]').val()))) {
            price.push($('input[name="price_till"]').val());
        }
        $('input[name="price[]"]:checked').each(function () {
            price.push($(this).parent().find('.select-option__item').text());
        });
        if (price.length > 0) {
            var placeholder = '<div class="selected-filters"><span class="selected-filters__item">' + price.join(', ') +
                    '</span><span class="selected-filters__action" onclick="clearPrice();"></span></div>';
            $('.price-placeholder-js').html(placeholder);
        } else {
            $('.price-placeholder-js').html(LABELS.allPrices);
        }
        if (reset === true) {
            return;
        }
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearPrice = function () {
        $('.price-placeholder-js').html(LABELS.allPrices);
        $('input[name="price[]"]').prop('checked', false);
        $('input[name="price_from"]').val('');
        $('input[name="price_till"]').val('');
        search(document.frmSearch);
        $("body").trigger('click');
    };

    searchAvailbility = function (reset = false) {
        var avaialbility = [];
        $('input[name="days[]"]:checked, input[name="slots[]"]:checked').each(function () {
            avaialbility.push($(this).parent().find('.select-option__item').text());
        });
        if (avaialbility.length > 0) {
            var placeholder = '<div class="selected-filters"><span class="selected-filters__item">' + avaialbility.join(', ') +
                    '</span><span class="selected-filters__action" onclick="clearAvailbility();"></span></div>';
            $('.availbility-placeholder-js').html(placeholder);
        } else {
            $('.availbility-placeholder-js').html(LABELS.selectTiming);
        }
        if (reset === true) {
            return;
        }
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearAvailbility = function () {
        $('.availbility-placeholder-js').html(LABELS.selectTiming);
        $('input[name="days[]"]').prop('checked', false);
        $('input[name="slots[]"]').prop('checked', false);
        search(document.frmSearch);
        $("body").trigger('click');
    };

    onkeyupLocation = function () {
        $('.select-location-js').parent().parent().hide();
        var keyword = ($('input[name="location_search"]').val()).toLowerCase();
        $('.select-location-js:contains("' + keyword + '")').parent().parent().show();
    };

    searchMore = function () {
        $('input[name="location_search"]').val('').trigger('onkeyup');
        search(document.frmSearch);
        $("body").trigger('click');
        countSelectedFilters();
    };

    clearMore = function (field) {
        $('input[name="' + field + '"]').prop('checked', false);
        $('input[name="location_search"]').val('').trigger('onkeyup');
        countSelectedFilters();
    };

    clearAllDesktop = function () {
        $('input[name="locations[]"]').prop('checked', false);
        $('input[name="gender[]"]').prop('checked', false);
        $('input[name="speaks[]"]').prop('checked', false);
        $('input[name="accents[]"]').prop('checked', false);
        $('input[name="levels[]"]').prop('checked', false);
        $('input[name="subjects[]"]').prop('checked', false);
        $('input[name="lesson_type[]"]').prop('checked', false);
        $('input[name="tests[]"]').prop('checked', false);
        $('input[name="age_group[]"]').prop('checked', false);
        countSelectedFilters();
        search(document.frmSearch);
        $("body").trigger('click');
    };

    clearAllMobile = function () {
        $(".filter-item__search-submit").show();
        $(".filter-item__search-reset").hide();
        $('.teachlang-placeholder-js').html(LABELS.allLanguages);
        $('.price-placeholder-js').html(LABELS.allPrices);
        $('.availbility-placeholder-js').html(LABELS.selectTiming);
        $('input[name="keyword"]').prop('value', '');
        $('input[name="teachs[]"]').prop('checked', false);
        $('input[name="price[]"]').prop('checked', false);
        $('input[name="days[]"]').prop('checked', false);
        $('input[name="slots[]"]').prop('checked', false);
        clearAllDesktop();
    };

    toggleSort = function () {
        $('body').toggleClass('sort-active');
        $('.sort-trigger-js').toggleClass('is-active');
        $('.sort-trigger-js').siblings('.sort-target-js').slideToggle();
    };

    sortsearch = function (sorting) {
        document.frmSearch.sorting.value = sorting;
        $("body").removeClass('sort-active');
        search(document.frmSearch);
    };

    search = function (frmSearch) {
        closeFilter();
        fcom.process();
        var data = fcom.frmData(frmSearch);
        fcom.ajax(fcom.makeUrl('Teachers', 'search'), data, function (response) {
            $('#listing').html(response);
            $(".gototop").trigger('click');
        });
    };

    gotoPage = function (pageno) {
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };

    activeFilter = function () {
        $("body").addClass('filter-active');
    };

    inactiveFilter = function () {
        $("body").removeClass('filter-active');
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

    countSelectedFilters = function () {
        var language = $(".language-filter-js:checked").length;
        var price = $(".price-filter-js:checked").length;
        var availbility = $(".availbility-filter-js:checked").length;
        var country = $(".country-filter-js:checked").length;
        var gender = $(".gender-filter-js:checked").length;
        var speak = $(".speak-filter-js:checked").length;
        var accent = $(".accent-filter-js:checked").length;
        var level = $(".level-filter-js:checked").length;
        var subject = $(".subject-filter-js:checked").length;
        var include = $(".include-filter-js:checked").length;
        var test = $(".test-filter-js:checked").length;
        var agegroup = $(".age-group-filter-js:checked").length;
        var morecount = country + gender + speak + accent + level + subject + include + test + agegroup;
        (language > 0) ? $(".language-count-js").text(language).show() : $(".language-count-js").text('').hide();
        (price > 0) ? $(".price-count-js").text(price).show() : $(".price-count-js").text('').hide();
        (availbility > 0) ? $(".availbility-count-js").text(availbility).show() : $(".availbility-count-js").text('').hide();
        (country > 0) ? $(".country-count-js").text(country).show() : $(".country-count-js").text('').hide();
        (gender > 0) ? $(".gender-count-js").text(gender).show() : $(".gender-count-js").text('').hide();
        (speak > 0) ? $(".speak-count-js").text(speak).show() : $(".speak-count-js").text('').hide();
        (accent > 0) ? $(".accent-count-js").text(accent).show() : $(".accent-count-js").text('').hide();
        (level > 0) ? $(".level-count-js").text(level).show() : $(".level-count-js").text('').hide();
        (subject > 0) ? $(".subject-count-js").text(subject).show() : $(".subject-count-js").text('').hide();
        (include > 0) ? $(".include-count-js").text(include).show() : $(".include-count-js").text('').hide();
        (test > 0) ? $(".test-count-js").text(test).show() : $(".test-count-js").text('').hide();
        (agegroup > 0) ? $(".age-group-count-js").text(agegroup).show() : $(".age-group-count-js").text('').hide();
        (morecount > 0) ? $(".more-count-js").text(morecount).show() : $(".more-count-js").text('').hide();
    }

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

    $(document).on('click', '.panel-action', function () {
        $(this).parents('.panel-box').find('.panel-content').hide();
        var section = $(this).attr('content');
        if (section === 'video') {
            var iframe = $(this).parents('.panel-box').find('.' + section).find('iframe');
            if (typeof iframe.attr('src') == 'undefined') {
                iframe.attr('src', iframe.parent().attr('data-src'));
            }
        }
        $(this).parents('.panel-box').find('.' + section).show();
        $(this).parent().siblings().removeClass('is--active');
        $(this).parent().addClass('is--active');

    });


    showOffers = function (obj) {
        $(obj).parent('.toggle-dropdown').addClass("is-active");
    };

    hideOffers = function (obj) {
        $(obj).parent('.toggle-dropdown').removeClass("is-active");
    };

    viewCalendar = function (teacherId) {
        fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar'), {teacherId: teacherId}, function (response) {
            $.facebox(response, 'facebox-large');
        });
    };
    searchLanguage(true);
    searchPrice(true);
    searchAvailbility(true);
    countSelectedFilters();
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
