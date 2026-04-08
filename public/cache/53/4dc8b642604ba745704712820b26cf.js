/* global fcom, confWebRootUrl, SslUsed, jstz, langLbl, searchfavorites, confWebDashUrl, ALERT_CLOSE_TIME */
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
var newsletterAjaxRuning = false;
$(document).ready(function () {
    setUpJsTabs();
    setUpGoToTop();
    toggleNavDropDownForDevices();
    toggleHeaderCurrencyLanguageForDevices();
    toggleFooterCurrencyLanguage();
    if ($.datepicker) {
        var old_goToToday = $.datepicker._gotoToday
        $.datepicker._gotoToday = function (id) {
            old_goToToday.call(this, id);
            this._selectDate(id);
            $(id).blur();
            return;
        }
    }
});
(function ($) {
    screenHeight = $(window).height() - 100;
    window.onresize = function (event) {
        screenHeight = $(window).height() - 100;
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
    getBadgeCount = function () {
        setTimeout(function () {
            fcom.ajax(fcom.makeUrl('Dashboard', 'getBadgeCounts', [], confWebDashUrl), '', function (response) {
                if (response.notifications > 0) {
                    let notifications = (response.notifications >= 100) ? '100+' : response.notifications;
                    $('.notification-count-js').addClass('head-count').text(notifications);
                }
                if (response.messages > 0) {
                    let messages = (response.messages >= 100) ? '100+' : response.messages;
                    $('.message-count-js').addClass('head-count').text(messages);
                }
            }, {fOutMode: 'json'});
        }, ALERT_CLOSE_TIME * 1000);
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
                body_height = $(".body").position();
                scroll_position = $(window).scrollTop();
                if (body_height.top < scroll_position) {
                    $(".header").addClass("is-fixed");
                } else {
                    $(".header").removeClass("is-fixed");
                }
            });
        }
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
    jQuery(document).ready(function (e) {
        function t(t) {
            e(t).bind("click", function (t) {
                t.preventDefault();
                e(this).parent().fadeOut()
            })
        }
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
    signinForm = function () {
        fcom.process();
        fcom.ajax(fcom.makeUrl('GuestUser', 'loginForm'), 'isPopUp=' + 1, function (response) {
            $.facebox(response);
        });
    };



    // var isPopupOpen = false; // Flag to track if the popup is open

    // signinForm = function () {
    //     console.log('1');
    //     if (!isPopupOpen) { // Only call signinForm if popup isn't open
    //         console.log('2');
    //         fcom.process();
    //         fcom.ajax(fcom.makeUrl('GuestUser', 'loginForm'), 'isPopUp=' + 1, function (response) {
    //             $.facebox(response);
    //             isPopupOpen = true; // Set the flag to true once the popup is shown
    //         });
    //     }
    // };
    
    
    // $(document).on('close.facebox', function() {
    //     isPopupOpen = false; // Reset flag when facebox is closed
    // });
    
    
    //setInterval(signinForm, 9000);
    




     
    signinSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        if (document.frmSearchPaging) {
            data += '&' + fcom.frmData(document.frmSearchPaging);
        }
        fcom.updateWithAjax(fcom.makeUrl('GuestUser', 'signinSetup'), data, function (res) {
            window.location.reload();
        });
    };
    signupForm = function () {
        fcom.process();
        fcom.ajax(fcom.makeUrl('GuestUser', 'signupForm'), '', function (response) {
            $.facebox(response);
        });
    };
    signupSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('GuestUser', 'signupSetup'), fcom.frmData(frm), function (res) {
            $.facebox.close();
            frm.reset();
            setTimeout(function () {
                window.location.reload();
            }, 1000);
        });
    };
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
        fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'setSiteLanguage'), data, function (res) {
            window.location.href = res.url;
        });
    };
    setSiteCurrency = function (currencyId) {
        fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'setSiteCurrency', [currencyId]), '', function (res) {
            document.location.reload();
        });
    };
    resendSignupVerifyEmail = function (email) {
        fcom.updateWithAjax(fcom.makeUrl('GuestUser', 'resendSignupVerifyEmail', [email]), {});
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
        fcom.updateWithAjax(fcom.makeUrl('Learner', 'toggleTeacherFavorite', [], confWebDashUrl), data, function (ans) {
            if (ans.action == 'A') {
                $(el).addClass("is--active");
            } else if (ans.action == 'R') {
                $(el).removeClass("is--active");
            }
            if (typeof searchfavorites != 'undefined') {
                searchfavorites(document.frmFavSrch);
            }
        });
        $(el).blur();
    };
    generateThread = function (id) {
       
        fcom.updateWithAjax(fcom.makeUrl('Messages', 'initiate', [id], confWebDashUrl), '', function (ans) {
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
        $.loader.show();
        var formData = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('Messages', 'sendMessage', [], confWebDashUrl), formData, function (data) {
            $.loader.hide();
            window.location.href = fcom.makeUrl('Messages', '', [], confWebDashUrl);
        }, {fOutMode: 'json'});
        return false;
    };
    closeNavigation = function () {
        $('.subheader .nav__dropdown a').removeClass('is-active');
        $('.subheader .nav__dropdown-target').fadeOut();
    };
    acceptAllCookies = function () {
        fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'acceptAll'), '', function (res) {
            $(".cookie-alert").remove();
            $(".cookie-alert").hide('slow');
        });
    };
    cookieConsentForm = function () {
        fcom.process();
        fcom.ajax(fcom.makeUrl('CookieConsent', 'form'), '', function (res) {
            $.facebox(res, 'facebox-medium cookies-popup');
        });
    };
    cookieConsentSetup = function (form) {
        if (!$(form).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('CookieConsent', 'setup'), fcom.frmData(form), function (res) {
            $('.cookie-alert').remove();
            $.facebox.close();
        });
    };
    teacherSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('TeacherRequest', 'teacherSetup'), fcom.frmData(frm), function (res) {
            setTimeout(() => {
                window.location.href = res.redirectUrl;
            }, 1000)
        });
    };
    resendVerificationLink = function (username) {
        if (username == "undefined" || typeof username === "undefined") {
            username = '';
        }
        fcom.updateWithAjax(fcom.makeUrl('GuestUser', 'resendVerificationLink', [username]));
    };
    submitNewsletterForm = function (form) {
        if (newsletterAjaxRuning) {
            return false
        }
        if (!$(form).validate()) {
            return;
        }
        newsletterAjaxRuning = true;
        $.loader.show();
        var data = fcom.frmData(form);
        fcom.ajax(fcom.makeUrl('Home', 'setUpNewsLetter'), data, function (response) {
            if (response.status == 1) {
                form.reset();
            }
            $.loader.hide();
            newsletterAjaxRuning = false;
        }, {fOutMode: 'json', failed: true});
    }

})(jQuery);
function toggleOffers(element) {
    $(element).toggleClass("is-active");
    $('html').toggleClass("show-offers-js");
    $(".offers-target-js").toggle();
}

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

$(document).ready(function () {
    var userTimezone = getCookie('CONF_SITE_TIMEZONE');
    var tz = jstz.determine();
    var timezone = tz.name();
    if (userTimezone == '' || userTimezone == null) {
        setCookie('CONF_SITE_TIMEZONE', timezone);
    }



    /* FOR BACK TO TOP */
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 500) {
                $('.gototop').addClass("isvisible");
            } else {
                $('.gototop').removeClass("isvisible");
            }
        });
        // scroll body to 0px on click
        $('.gototop').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
    $(document).on('click', '.play-video', function () {
        $.facebox('<iframe id="ytplayer" type="text/html" width="1000" height="460" src="' + $(this).attr('data-src') + '" frameborder="2"></iframe>');
    });
});

function resetEditorHeight() {
    setTimeout(function () {
        $('.editor-content').each(function (i, div) {
            var height = $(div).children('iframe').contents().height() + 20;
            $(div).css('height', height + 'px');
        });
    }, 100);
}