/* global confFrontEndUrl, fcom, searchfavorites, confWebRootUrl, langLbl, weekDayNames, monthNames, layoutDirection, moment, jstz */
/**
 * Check JSON String
 * @returns {Boolean}
 */
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
/**
 * 
 * @param {number} time must be an integer
 */
function reloadPage(time) {
    if (time && time > 0) {
        setTimeout(() => {
            window.location.reload();
        }, time);
    } else {
        window.location.reload();
    }
}
cancel = function () {
    $.facebox.close();
};
generateThread = function (id) {
 
    fcom.updateWithAjax(fcom.makeUrl('Messages', 'initiate', [id], confWebRootUrl), '', function (ans) {
        if (ans.redirectUrl) {
            if (ans.threadId) {
                sessionStorage.setItem('threadId', ans.threadId);
            }
            window.location.href = ans.redirectUrl;
            return;
        }
        $.facebox(ans.html, '');
    });
};
sendMessage = function (frm) {
    if (!$(frm).validate()) {
        return;
    }
    var formData = new FormData(frm);
    fcom.ajaxMultipart(fcom.makeUrl('Messages', 'sendMessage', [], confWebRootUrl), formData, function (data) {
        window.location.href = fcom.makeUrl('Messages', '', [], confWebRootUrl);
    }, {fOutMode: 'json'});
    return false;
};

getBadgeCount = function () {
    fcom.updateWithAjax(fcom.makeUrl('Dashboard', 'getBadgeCounts'), '', function (response) {
        if (response.notifications > 0) {
            let notifications = (response.notifications >= 100) ? '100+' : response.notifications;
            $('.notification-badge').attr('data-count', notifications);
        }
        if (response.messages > 0) {
            let messages = (response.messages >= 100) ? '100+' : response.messages;
            $('.message-badge').attr('data-count', messages);
        }
    }, {process: false});
};
acceptAllCookies = function () {
    fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'acceptAll', [], confFrontEndUrl), '', function (res) {
        if (res.status == 1) {
            $(".cookie-alert").hide('slow');
            $(".cookie-alert").remove();
        }
    });
};
cookieConsentForm = function (inFacebox) {
    inFacebox = (inFacebox) ? inFacebox : false;
    fcom.ajax(fcom.makeUrl('CookieConsent', 'form', [], confFrontEndUrl), '', function (res) {
        if (inFacebox) {
            $.facebox(res, 'facebox-medium cookies-popup');
            return;
        }
        $('#formBlock-js').html(res);
    });
};
cookieConsentSetup = function (form) {
    if (!$(form).validate()) {
        return;
    }
    fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'setup', [], confFrontEndUrl), fcom.frmData(form), function (res) {
        $('.cookie-alert').remove();
        $.facebox.close();
    });
};
$(document).ready(function () {
    setUpJsTabs();
    setUpGoToTop();
    toggleNavDropDownForDevices();
    toggleHeaderNavigationForDevices();
    toggleHeaderCurrencyLanguageForDevices();
    toggleFooterCurrencyLanguage();
    if ($.datepicker) {
        var old_goToToday = $.datepicker._gotoToday
        $.datepicker._gotoToday = function (id) {
            old_goToToday.call(this, id);
            this._selectDate(id);
            $(id).blur();
            return;
        };
    }
});
(function ($) {
    var screenHeight = $(window).height() - 100;
    window.onresize = function (event) {
        var screenHeight = $(window).height() - 100;
    };
    $.extend(fcom, {
        resetFaceboxHeight: function () {
            $('html').addClass('show-facebox');
            facebocxHeight = screenHeight;
            $('#facebox .content').css('max-height', facebocxHeight - 50 + 'px');
            if ($('#facebox .content').height() + 100 >= screenHeight) {
                $('#facebox .content').css('display', 'block');
            } else {
                $('#facebox .content').css('max-height', '');
                $('#facebox .content').css('overflow', '');
            }
        },
        updateFaceboxContent: function (t, cls) {
            if (typeof cls == 'undefined' || cls == 'undefined') {
                cls = '';
            }
            $.facebox(t, cls);
            fcom.resetFaceboxHeight();
        },
        waitAndRedirect: function (redirectUrl) {
            setTimeout(function () {
                window.location.href = redirectUrl;
            }, 3000);
        },
        setEditorLayout: function (lang_id) {
            setTimeout(function () {
                var editors = oUtil.arrEditor;
                layout = langLbl['language' + lang_id];
                for (x in editors) {
                    $('#idContent' + editors[x]).contents().find("body").css('direction', layout);
                    $('#' + editors[x] + "grp").parent().parent().attr('dir', layout);
                }
            }, 100);
        },
    });
    $(document).bind('reveal.facebox', function () {
        fcom.resetFaceboxHeight();
    });
    $(window).on("orientationchange", function () {
        facebocxHeight = screenHeight;
        $('#facebox .content').css('max-height', facebocxHeight - 50 + 'px');
        if ($('#facebox .content').height() + 100 >= screenHeight) {
            $('#facebox .content').css('display', 'block');
        } else {
            $('#facebox .content').css('max-height', '');
            $('#facebox .content').css('overflow', '');
        }
    });
    $(document).bind('loading.facebox', function () { });
    $(document).bind('beforeReveal.facebox', function () { });
    $(document).bind('afterClose.facebox', function () {
        $('html').removeClass('show-facebox');
    });
    setUpJsTabs = function () {
        $(".tabs-content-js").hide();
        $(".tabs-js li:first").addClass("is-active").show();
        $(".tabs-content-js:first").show();
    };
    setUpGoToTop = function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scroll-top-js').addClass("isvisible");
            } else {
                $('.scroll-top-js').removeClass("isvisible");
            }
        });
        $(".scroll-top-js").click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    };
    setUpStickyHeader = function () {
        if ($(window).width() > 767) {
            $(window).scroll(function () {
                if ($(".body").length > 0) {
                    scroll_position = $(window).scrollTop();
                    if (body_height.top < scroll_position) {
                        $(".header").addClass("is-fixed");
                    } else {
                        $(".header").removeClass("is-fixed");
                    }
                }
            });
        }
    };
    signinForm = function () {
        fcom.ajax(fcom.makeUrl('GuestUser', 'loginForm', [], confFrontEndUrl), 'isPopUp=' + 1, function (response) {
            $.facebox(response);
        });
    };

     

    signinSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('GuestUser', 'signinSetup', [], confFrontEndUrl), fcom.frmData(frm), function (res) {
            window.location.reload();
        });
    };
    toggleNavDropDownForDevices = function () {
        if ($(window).width() < 1200) {
            $('.nav__dropdown-trigger-js').click(function () {
                if ($(this).hasClass('is-active')) {
                    $('html').removeClass('show-dashboard-js');
                    $(this).removeClass('is-active');
                    $(this).siblings('.nav__dropdown-target-js').slideUp();
                    return false;
                }
                $('.nav__dropdown-trigger-js').removeClass('is-active');
                $('html').addClass('show-dashboard-js');
                $(this).addClass("is-active");
                $('.nav__dropdown-target-js').slideUp();
                $(this).siblings('.nav__dropdown-target-js').slideDown();
            });
        }
    };
    toggleHeaderNavigationForDevices = function () {
        $('.toggle--nav-js').click(function () {
            $(this).toggleClass("is-active");
            $('html').toggleClass("show-nav-js");
            $('html').removeClass("show-dashboard-js");
        });
    };
    jQuery(document).ready(function (e) {
        function t(t) {
            e(t).bind("click", function (t) {
                t.preventDefault();
                e(this).parent().fadeOut()
            });
        }
        $(".cc-cookie-accept-js").click(function () {
            fcom.ajax(fcom.makeUrl('Custom', 'updateUserCookies', [], confFrontEndUrl), '', function (t) {
                $(".cookie-alert").hide('slow');
                $(".cookie-alert").remove();
                $.facebox.close();
            });
        });
        $(".tabs-content-js").hide();
        $(".tabs-js li:first").addClass("is-active").show();
        $(".tabs-content-js:first").show();
        $(".tabs-js li").click(function () {
            $(".tabs-js li").removeClass("is-active");
            $(this).addClass("is-active");
            $(".tabs-content-js").hide();
            var activeTab = $(this).data("href");
            $(activeTab).fadeIn();
            return true;
        });
        e(".toggle__trigger-js").click(function () {
            var t = e(this).parents(".toggle-group").children(".toggle__target-js").is(":hidden");
            e(".toggle-group .toggle__target-js").hide();
            e(".toggle-group .toggle__trigger-js").removeClass("is-active");
            if (t) {
                e(this).parents(".toggle-group").children(".toggle__target-js").toggle().parents(".toggle-group").children(".toggle__trigger-js").addClass("is-active")
            }
        });
        $(document.body).on('click', ".toggle__trigger-js", function () {
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
        $(".tab-swticher-small a").click(function () {
            $(".tab-swticher-small a").removeClass("is-active");
            $(this).addClass("is-active");
        });
    });
    toggleHeaderCurrencyLanguageForDevices = function () {
        $('.nav__item-settings-js').click(function () {
            $(this).toggleClass("is-active");
            $('html').toggleClass("show-setting-js");
        });
    };
    toggleFooterCurrencyLanguage = function () {
        $(".toggle-footer-lang-currency-js").click(function () {
            var clickedSectionClass = $(this).siblings(".listing-div-js").attr("div-for");
            $(".toggle-footer-lang-currency-js").each(function () {
                if ($(this).siblings(".listing-div-js").attr("div-for") != clickedSectionClass) {
                    $(this).siblings(".listing-div-js").hide();
                }
            });
            $(this).siblings(".listing-div-js").slideToggle();
        });
    };
    setSiteLanguage = function (langId) {
        var data = {langId: langId, url: window.location.pathname};
        fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'setSiteLanguage', [], confFrontEndUrl), data, function (res) {
            window.location.href = res.url;
        });
    };
    setSiteCurrency = function (currencyId) {
        fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'setSiteCurrency', [currencyId], confFrontEndUrl), '', function (res) {
            document.location.reload();
        });
    };
    togglePassword = function (e) {
        var passType = $("input[name='user_password']").attr("type");
        if (passType == "text") {
            $("input[name='user_password']").attr("type", "password");
            $(e).html($(e).attr("data-show-caption"));
        } else {
            $("input[name='user_password']").attr("type", "text");
            $(e).html($(e).attr("data-hide-caption"));
        }
    };
    toggleLoginPassword = function (e) {
        var passType = $("input[name='password']").attr("type");
        if (passType == "text") {
            $("input[name='password']").attr("type", "password");
            $(e).html($(e).attr("data-show-caption"));
        } else {
            $("input[name='password']").attr("type", "text");
            $(e).html($(e).attr("data-hide-caption"));
        }
    };
    toggleTeacherFavorite = function (teacherId, el) {
        var data = 'teacher_id= ' + teacherId;
        fcom.updateWithAjax(fcom.makeUrl('Learner', 'toggleTeacherFavorite'), data, function (ans) {
            if (ans.status) {
                if (ans.action == 'A') {
                    $(el).addClass("is--active");
                } else if (ans.action == 'R') {
                    $(el).removeClass("is--active");
                }
                if (typeof searchfavorites != 'undefined') {
                    searchfavorites(document.frmFavSrch);
                }
            }
        });
        $(el).blur();
    };
    closeNavigation = function () {
        $('.subheader .nav__dropdown a').removeClass('is-active');
        $('.subheader .nav__dropdown-target').fadeOut();
    };
})(jQuery);
function setCookie(key, value) {
    var expires = new Date();
    expires.setTime(expires.getTime() + 604800);
    secure = (SslUsed == 1) ? ' secure;' : '';
    samesite = "";
    if (secure) {
        samesite = " samesite=none;";
    }
    document.cookie = key + '=' + value + '; ' + secure + samesite + ' expires=' + expires.toUTCString() + ';  domain=.' + window.location.hostname + '; path=' + confWebRootUrl;
}
function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}
function bindDatetimePicker(selector) {
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
    };
    $.datepicker.regional[''] = $.extend(true, {}, defaultsValue);
    $.datepicker.setDefaults($.datepicker.regional['']);
    var dayNames = weekDayNames.shortName.slice(0);
    var lastValue = dayNames[6];
    dayNames.pop();
    dayNames.unshift(lastValue);
    $.fn.datetimepicker.defaults.i18n = {'': {months: monthNames.longName, dayOfWeek: dayNames}};
    $(selector).datetimepicker({
        step: 5, lang: '',
        format: 'Y-m-d H:i:00',
        minDate: new Date(),
        closeOnDateSelect: false,
        closeOnInputClick: false,
        onChangeDateTime: function (currentDateTime, $input) {
            let selectedDateTime = $input.val();
            let selectedTime = selectedDateTime.split(" ")[1];
            let minutes = parseInt(selectedTime.split(":")[1]);
            let minutesToAdd = 0;
            const validTime = [15, 30, 45, 0];
           /* if (!validTime.includes(minutes)) {
                if (minutes < 15) {
                    minutesToAdd = 15 - minutes;
                } else if (minutes < 30) {
                    minutesToAdd = 30 - minutes;
                } else if (minutes < 45) {
                    minutesToAdd = 45 - minutes;
                } else if (minutes > 45) {
                    minutesToAdd = 60 - minutes;
                }
            } */
            this.setOptions({
                value: moment(selectedDateTime).add(minutesToAdd, 'minutes').format("YYYY-MM-DD HH:mm:ss")
            });
        },
    });
}
$(document).ready(function () {
    var userTimezone = getCookie('CONF_SITE_TIMEZONE');
    var tz = jstz.determine();
    var timezone = tz.name();
    if (userTimezone == '' || userTimezone == null) {
        setCookie('CONF_SITE_TIMEZONE', timezone);
    }
});

function resetEditorHeight() {
    setTimeout(function () {
        $('.editor-content').each(function (i, div) {
            var height = $(div).children('iframe').contents().height() + 20;
            $(div).css('height', height + 'px');
        });
    }, 200);
}