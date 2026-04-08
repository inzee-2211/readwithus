/* global oUtil, langLbl, x, fcom, weekDayNames, monthNames, layoutDirection, SITE_ROOT_URL, siteConstants */
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
(function ($) {
    var screenHeight = $(window).height() - 100;
    window.onresize = function (event) {
        var screenHeight = $(window).height() - 100;
    };
    var dayShortNames = weekDayNames.shortName.slice(0);
    var lastValue = dayShortNames[6];
    dayShortNames.pop();
    dayShortNames.unshift(lastValue);
    defaultsValue = {
        monthNames: monthNames.longName,
        monthNamesShort: monthNames.shortName,
        dayNamesMin: dayShortNames,
        dayNamesShort: dayShortNames,
        currentText: langLbl.today,
        closeText: langLbl.done,
        prevText: langLbl.prev,
        nextText: langLbl.next,
        isRTL: (layoutDirection == 'rtl')
    }
    $.datepicker.regional[''] = $.extend(true, {}, defaultsValue);
    $.datepicker.setDefaults($.datepicker.regional['']);
    $.extend(fcom, {
        resetEditorInstance: function () {
            if (typeof oUtil != 'undefined') {
                var editors = oUtil.arrEditor;
                for (x in editors) {
                    eval('delete window.' + editors[x]);
                }
                oUtil.arrEditor = [];
            }
        },
        setEditorLayout: function (lang_id) {
            setTimeout(function () {
                var editors = oUtil.arrEditor;
                layout = langLbl['language' + lang_id];
                for (x in editors) {
                    $('#idContent' + editors[x]).contents().find("body").css('direction', layout);
                }
            }, 100);
        },
        resetFaceboxHeight: function () {
            $('html').css('overflow', 'hidden');
            facebocxHeight = screenHeight;
            $('#facebox .content').css('max-height', facebocxHeight - 50 + 'px');
            if ($('#facebox .content').height() + 100 >= screenHeight) {
                $('#facebox .content').css('overflow-y', 'scroll');
                $('#facebox .content').css('display', 'block');
            } else {
                $('#facebox .content').css('max-height', '');
                $('#facebox .content').css('overflow', '');
            }
        },
        getLoader: function () {
            return '<div class="circularLoader"><svg class="circular" height="30" width="30"><circle class="path" cx="25" cy="25.2" r="19.9" fill="none" stroke-width="6" stroke-miterlimit="10"></circle> </svg> </div>';
        },
        updateFaceboxContent: function (t, cls) {
            if (typeof cls == 'undefined' || cls == 'undefined') {
                cls = '';
            }
            $.facebox(t, cls);
            fcom.resetFaceboxHeight();
        }
    });
    $(document).bind('reveal.facebox', function () {
        fcom.resetFaceboxHeight();
    });
    $(window).on("orientationchange", function () {
        fcom.resetFaceboxHeight();
    });
    $(document).bind('loading.facebox', function () {
        $('#facebox .content').addClass('fbminwidth');
    });
    $(document).bind('afterClose.facebox', fcom.resetEditorInstance);
    $(document).bind('afterClose.facebox', function () {
        $('html').css('overflow', '')
    });
    $(document).bind('close.sysmsgcontent', function () {
        $('.alert').fadeOut();
    });
    $.facebox.settings.loadingImage = SITE_ROOT_URL + 'images/loading.gif';
    $.facebox.settings.closeImage = SITE_ROOT_URL + 'images/close.png';
    if ($.datepicker) {
        var old_goToToday = $.datepicker._gotoToday
        $.datepicker._gotoToday = function (id) {
            old_goToToday.call(this, id);
            this._selectDate(id);
            $(id).blur();
            return;
        }
    }
    ;
    refreshCaptcha = function (elem) {
        $(elem).attr('src', siteConstants.webroot + 'helper/captcha?sid=' + Math.random());
    };
    clearCache = function () {
        fcom.ajax(fcom.makeUrl('Home', 'clearCache'), '', function (t) {
            window.location.reload();
        });
    };
    SelectText = function (element) {
        var doc = document
            , text = doc.getElementById(element)
            , range, selection
            ;
        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    };
    getSlugUrl = function (obj, str, extra, pos) {
        if (pos == undefined)
            pos = 'pre';
        var str = str.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-\/]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');
        if (extra && pos == 'pre') {
            str = extra + '/' + str;
        }
        if (extra && pos == 'post') {
            str = str + '/' + extra;
        }
        $(obj).next().html(SITE_URL + str);
    };
    redirectfunc = function (url, id, nid) {
        if (nid > 0) {
            markRead(nid, url, id);
        } else {
            var form = '<input type="hidden" name="id" value="' + id + '">';
            $('<form action="' + url + '" method="POST">' + form + '</form>').appendTo($(document.body)).submit();
        }
    };
    markRead = function (nid, url, id) {
        if (nid.length < 1) {
            return false;
        }
        var data = 'record_ids=' + nid + '&status=' + 1 + '&markread=1';
        fcom.updateWithAjax(fcom.makeUrl('Notifications', 'changeStatus'), data, function (res) {
            var form = '<input type="hidden" name="id" value="' + id + '">';
            $('<form action="' + url + '" method="POST">' + form + '</form>').appendTo($(document.body)).submit();
        });
    };
    generateSitemap = function () {
        fcom.updateWithAjax(fcom.makeUrl('Sitemap', 'generate'), '', function (res) { });
    };
    logout = function () {
        fcom.updateWithAjax(fcom.makeUrl('Profile', 'logout'), '', function (res) {
            setTimeout(function () {
                window.location.href = fcom.makeUrl('AdminGuest', 'loginForm');
            }, 1000);
        });
    };

})(jQuery);
function getSlickSliderSettings(slidesToShow, slidesToScroll, layoutDirection) {
    slidesToShow = (typeof slidesToShow != "undefined") ? parseInt(slidesToShow) : 4;
    slidesToScroll = (typeof slidesToScroll != "undefined") ? parseInt(slidesToScroll) : 1;
    layoutDirection = (typeof layoutDirection != "undefined") ? layoutDirection : 'ltr';
    if (layoutDirection == 'rtl') {
        return {
            slidesToShow: slidesToShow,
            slidesToScroll: slidesToScroll,
            infinite: false,
            arrows: true,
            rtl: true,
            prevArrow: '<a data-role="none" class="slick-prev" aria-label="previous"></a>',
            nextArrow: '<a data-role="none" class="slick-next" aria-label="next"></a>',
            responsive: [
                { breakpoint: 1050, settings: { slidesToShow: slidesToShow - 1 } },
                { breakpoint: 990, settings: { slidesToShow: 3 } },
                { breakpoint: 767, settings: { slidesToShow: 2 } },
                { breakpoint: 400, settings: { slidesToShow: 1 } }
            ]
        }
    } else {
        return {
            slidesToShow: slidesToShow,
            slidesToScroll: slidesToScroll,
            infinite: false,
            arrows: true,
            prevArrow: '<a data-role="none" class="slick-prev" aria-label="previous"></a>',
            nextArrow: '<a data-role="none" class="slick-next" aria-label="next"></a>',
            responsive: [
                { breakpoint: 1050, settings: { slidesToShow: slidesToShow - 1 } },
                { breakpoint: 990, settings: { slidesToShow: 3 } },
                { breakpoint: 767, settings: { slidesToShow: 2 } },
                { breakpoint: 400, settings: { slidesToShow: 1 } }
            ]
        }
    }
}
(function () {
    Slugify = function (str, str_val_id, is_slugify) {
        var str = str.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');
        if ($("#" + is_slugify).val() == 0)
            $("#" + str_val_id).val(str);
    };
    callChart = function (dv, $labels, $series, $position) {
        new Chartist.Bar('#' + dv, {
            labels: $labels,
            series: [$series],
        }, {
            stackBars: false,
            axisY: {
                position: $position,
                labelInterpolationFnc: function (value) {
                    return value;
                }
            }
        }).on('draw', function (data) {
            if (data.type === 'bar') {
                data.element.attr({
                    style: 'stroke-width: 25px'
                });
            }
        });
    };
    escapeHtml = function (text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function (m) {
            return map[m];
        });
    };
    validateYoutubelink = function (field) {
        let frm = field.form;
        let url = field.value.trim();
        if (url == '') {
            return false;
        }
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
        let matches = url.match(regExp);
        if (matches && matches[2].length == 11) {
            let validUrl = "https://www.youtube.com/embed/";
            validUrl += matches[2];
            $(field).val(validUrl);
        } else {
            $(field).val('');
        }
        $(frm).validate();
    };

    validateLink = function (field) {
        let url = field.value.trim();
        if (url == '') {
            return false;
        }
        let regExp = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/;
        let matches = url.match(regExp);
        if (matches) {
            $(field).val(url);
        } else {
            $(field).val('');
        }
    };
})();

function resetEditorHeight() {
    setTimeout(function () {
        $('.editor-content').each(function (i, div) {
            var height = $(div).children('iframe').contents().height() + 20;
            $(div).css('height', height + 'px');
        });
    }, 200);
}