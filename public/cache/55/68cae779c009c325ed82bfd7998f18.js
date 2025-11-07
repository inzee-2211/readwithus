/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    var pluses = /\+/g;
    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }
    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }
    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }
    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }
        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch (e) {
        }
    }
    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }
    var config = $.cookie = function (key, value, options) {
        // Write
        if (value !== undefined && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);
            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setTime(+t + days * 864e+5);
            }
            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }
        // Read
        var result = key ? undefined : {};
        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];
        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');
            if (key && key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }
            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }
        return result;
    };
    config.defaults = {path: '/'};
    $.removeCookie = function (key, options) {
        if ($.cookie(key) === undefined) {
            return false;
        }
        // Must not alter options, thus extending a fresh object...
        $.cookie(key, '', $.extend({}, options, {expires: -1}));
        return !$.cookie(key);
    };
}));
/* global moment */
(function ($) {
    $.fn.yocoachTimer = function (options) {
        var timer = this;
        timer.init = function () {
            timer.settings = $.extend({}, {
                recordId: options.recordId,
                recordType: options.recordType,
                starttime: $(timer).attr('timestamp'),
                callback: false
            }, options);
            $.cookie(timer.getKey(), timer.settings.starttime);
        };
        timer.start = function () {
            timer.interval = setInterval(function () {
                var startTime = parseInt($.cookie(timer.getKey()));
                var currentTime = parseInt((new Date()).getTime() / 1000);
                var remainingTime = startTime - currentTime;
                if (remainingTime < 1) {
                    clearInterval(timer.interval);
                    $(timer).text('00:00:00:00');
                    $.cookie(timer.getKey(), 0);
                    if (timer.settings.callback) {
                        timer.settings.callback();
                    }
                    return;
                }
                var days = Math.floor(remainingTime / (60 * 60 * 24));
                var divisor_for_hours = remainingTime % (60 * 60 * 24);
                var hours = Math.floor(divisor_for_hours / (60 * 60));
                var divisor_for_minutes = remainingTime % (60 * 60);
                var minutes = Math.floor(divisor_for_minutes / 60);
                var divisor_for_seconds = divisor_for_minutes % 60;
                var seconds = Math.ceil(divisor_for_seconds);
                seconds = (seconds < 10) ? '0' + seconds : seconds;
                minutes = (minutes < 10) ? '0' + minutes : minutes;
                hours = (hours < 10) ? '0' + hours : hours;
                days = (days < 10) ? '0' + days : days;
                $(timer).text(days + ':' + hours + ':' + minutes + ':' + seconds);
            }, 1000);
        };
        timer.getKey = function () {
            return timer.settings.recordType + timer.settings.recordId;
        };
        timer.init();
        timer.start();
    };
}(jQuery));/* global fcom, langLbl */
$(function () {
    viewIssue = function (issueId) {
        fcom.ajax(fcom.makeUrl('Issues', 'view'), {issueId: issueId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    issueForm = function (recordId, recordType) {
        fcom.ajax(fcom.makeUrl('Issues', 'form'), {recordId: recordId, recordType: recordType, }, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    issueSetup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.ajax(fcom.makeUrl('Issues', 'setup'), fcom.frmData(frm), function (response) {
            $.facebox.close();
            reloadPage(3000);
        });
    };
    resolveForm = function (issueId) {
        fcom.ajax(fcom.makeUrl('Issues', 'resolve'), {issueId: issueId}, function (response) {
            $.facebox(response, 'facebox-medium issueDetailPopup');
        });
    };
    resolveSetup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        var action = fcom.makeUrl('Issues', 'resolveSetup');
        fcom.updateWithAjax(action, fcom.frmData(frm), function (response) {
            $.facebox.close();
            reloadPage(3000);
        });
    };
    escalate = function (issueId) {
        fcom.ajax(fcom.makeUrl('Issues', 'escalate'), {issueId: issueId}, function (response) {
            $.facebox(response, 'facebox-small');
        });
    };
    escalateSetup = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var action = fcom.makeUrl('Issues', 'escalateSetup');
        fcom.updateWithAjax(action, fcom.frmData(frm), function (response) {
            $.facebox.close();
            reloadPage(3000);
        });
    };
});
(function(c){var b,a;a=typeof window!=="undefined"&&window!==null?window:global;a.BarRating=b=(function(){function d(){this.show=function(){var g=c(this.elem),j,f,h=this.options,e,i;if(!g.data("barrating")){if(h.initialRating){i=c('option[value="'+h.initialRating+'"]',g)}else{i=c("option:selected",g)}g.data("barrating",{currentRatingValue:i.val(),currentRatingText:i.text(),originalRatingValue:i.val(),originalRatingText:i.text()});j=c("<div />",{"class":"br-widget"}).insertAfter(g);g.find("option").each(function(){var n,m,l,k;n=c(this).val();if(n){m=c(this).text();l=c("<a />",{href:"#","data-rating-value":n,"data-rating-text":m});k=c("<span />",{text:(h.showValues)?m:""});j.append(l.append(k))}});if(h.showSelectedRating){j.append(c("<div />",{text:"","class":"br-current-rating"}))}g.data("barrating").deselectable=(!g.find("option:first").val())?true:false;if(h.reverse){e="nextAll"}else{e="prevAll"}if(h.reverse){j.addClass("br-reverse")}if(h.readonly){j.addClass("br-readonly")}j.on("ratingchange",function(k,l,m){l=l?l:g.data("barrating").currentRatingValue;m=m?m:g.data("barrating").currentRatingText;g.find('option[value="'+l+'"]').prop("selected",true);if(h.showSelectedRating){c(this).find(".br-current-rating").text(m)}}).trigger("ratingchange");j.on("updaterating",function(k){c(this).find('a[data-rating-value="'+g.data("barrating").currentRatingValue+'"]').addClass("br-selected br-current")[e]().addClass("br-selected")}).trigger("updaterating");f=j.find("a");f.on("touchstart",function(k){k.preventDefault();k.stopPropagation();c(this).click()});if(h.readonly){f.on("click",function(k){k.preventDefault()})}if(!h.readonly){f.on("click",function(k){var m=c(this),l,n;k.preventDefault();f.removeClass("br-active br-selected");m.addClass("br-selected")[e]().addClass("br-selected");l=m.attr("data-rating-value");n=m.attr("data-rating-text");if(m.hasClass("br-current")&&g.data("barrating").deselectable){m.removeClass("br-selected br-current")[e]().removeClass("br-selected br-current");l="",n=""}else{f.removeClass("br-current");m.addClass("br-current")}g.data("barrating").currentRatingValue=l;g.data("barrating").currentRatingText=n;j.trigger("ratingchange");h.onSelect.call(this,g.data("barrating").currentRatingValue,g.data("barrating").currentRatingText);return false});f.on({mouseenter:function(){var k=c(this);f.removeClass("br-active").removeClass("br-selected");k.addClass("br-active")[e]().addClass("br-active");j.trigger("ratingchange",[k.attr("data-rating-value"),k.attr("data-rating-text")])}});j.on({mouseleave:function(){f.removeClass("br-active");j.trigger("ratingchange").trigger("updaterating")}})}g.hide()}};this.clear=function(){var e=c(this.elem);var f=e.next(".br-widget");if(f&&e.data("barrating")){f.find("a").removeClass("br-selected br-current");e.data("barrating").currentRatingValue=e.data("barrating").originalRatingValue;e.data("barrating").currentRatingText=e.data("barrating").originalRatingText;f.trigger("ratingchange").trigger("updaterating");this.options.onClear.call(this,e.data("barrating").currentRatingValue,e.data("barrating").currentRatingText)}};this.destroy=function(){var f=c(this.elem);var h=f.next(".br-widget");if(h&&f.data("barrating")){var e=f.data("barrating").currentRatingValue;var g=f.data("barrating").currentRatingText;f.removeData("barrating");h.off().remove();f.show();this.options.onDestroy.call(this,e,g)}}}d.prototype.init=function(f,g){var e;e=this;e.elem=g;return e.options=c.extend({},c.fn.barrating.defaults,f)};return d})();c.fn.barrating=function(e,d){return this.each(function(){var f=new b();if(!c(this).is("select")){c.error("Sorry, this plugin only works with select fields.")}if(f.hasOwnProperty(e)){f.init(d,this);return f[e]()}else{if(typeof e==="object"||!e){d=e;f.init(d,this);return f.show()}else{c.error("Method "+e+" does not exist on jQuery.barrating")}}})};return c.fn.barrating.defaults={initialRating:null,showValues:false,showSelectedRating:true,reverse:false,readonly:false,onSelect:function(d,e){},onClear:function(d,e){},onDestroy:function(d,e){}}})(jQuery);/* global fcom, langLbl */
(function () {
    cancelForm = function (classId) {
        fcom.ajax(fcom.makeUrl('Classes', 'cancelForm'), {classId: classId}, function (response) {
            $.facebox(response, 'facebox-small');
        });
    };
    cancelSetup = function (form) {
        if (!$(form).validate()) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Classes', 'cancelSetup'), fcom.frmData(form), function (response) {
            reloadPage(3000);
        });
    };
    feedbackForm = function (classId) {
        fcom.ajax(fcom.makeUrl('Classes', 'feedbackForm'), {classId: classId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    feedbackSetup = function (frm) {
        fcom.ajax(fcom.makeUrl('Classes', 'feedbackSetup'), fcom.frmData(frm), function (response) {
            reloadPage(3000);
        });
    };
})();/* global fcom, langLbl */
(function () {
    assignPlanToClasses = function (recordId, planId, planType) {
        var data = 'recordId=' + recordId + '&planId=' + planId + '&planType=' + planType;
        fcom.updateWithAjax(fcom.makeUrl('Plans', 'assignPlanToClasses'), data, function (t) {
            $.facebox.close();
            if (document.frmSearchPaging) {
                search(document.frmSearchPaging);
                return;
            }
            window.location.reload();
        });
    };
    removeAssignedPlan = function (recordId, planType) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(fcom.makeUrl('Plans', 'removeAssignedPlan'), 'recordId=' + recordId + '&planType=' + planType, function (t) {
                $.facebox.close();
                if (document.frmSearchPaging) {
                    search(document.frmSearchPaging);
                    return;
                }
                window.location.reload();
            });
        }
    };
    listLessonPlans = function (id, type) {
        fcom.ajax(fcom.makeUrl('plans', 'index', [id, type]), '', function (t) {
            $.facebox('<div class="facebox-panel"><div class="facebox-panel__body">' + t + '</div></div>', 'facebox-medium');
            fcom.ajax(fcom.makeUrl('Plans', 'search'), fcom.frmData(document.planSearchFrm), function (res) {
                $(".plan-listing#listing").html(res);
            });
        });
    };
    viewAssignedPlan = function (recordId, type) {
        fcom.ajax(fcom.makeUrl('Plans', 'viewAssignedPlan', [recordId, type]), '', function (t) {
            $.facebox(t, 'facebox-medium');
        });
    };
    searchPlans = function (frm) {
        fcom.ajax(fcom.makeUrl('Plans', 'search'), fcom.frmData(frm), function (res) {
            $(".plan-listing#listing").html(res);
        });
    };
    clearPlanSearch = function () {
        document.getElementById('planKeyword').value = '';
        document.getElementById('planLevel').value = '';
        searchPlans($('form#planSearchFrm'));
    };
    form = function (planId) {
        fcom.ajax(fcom.makeUrl('Plans', 'form'), {planId: planId}, function (res) {
            $.facebox(res, 'facebox-medium');
        });
    };
    goToPlanSearchPage = function (pageno) {
        var frm = document.frmPlanSearchPaging;
        $(frm.pageno).val(pageno);
        searchPlans(frm);
    };
})();
/* global fcom, langLbl, FLASHCARD_VIEW, FLASHCARD_TYPE, FLASHCARD_TYPE_ID, FLASHCARD_TLANG_ID */
$(function () {
    searchFlashcards = function (frm) {
        var data = {
            view: FLASHCARD_VIEW,
            keyword: frm.keyword.value,
            flashcard_type: FLASHCARD_TYPE,
            flashcard_type_id: FLASHCARD_TYPE_ID
        };
        fcom.ajax(fcom.makeUrl('Flashcards', 'search'), data, function (res) {
            $('#flashcard').html(res);
        });
    };

    clearSearch = function () {
        document.searchFlashcardFrm.reset();
        searchFlashcards(document.searchFlashcardFrm);
    };

    flashcardForm = function (id) {
        var frmData = {flashcardId: id, view: FLASHCARD_VIEW};
        fcom.ajax(fcom.makeUrl('Flashcards', 'form'), frmData, function (res) {
            $('#flashcard').html(res);
        });
    };
    
    flashcardSetup = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        var data = {
            flashcard_type: FLASHCARD_TYPE,
            flashcard_type_id: FLASHCARD_TYPE_ID,
            flashcard_tlang_id: FLASHCARD_TLANG_ID,
            flashcard_id: frm.flashcard_id.value,
            flashcard_title: frm.flashcard_title.value,
            flashcard_detail: frm.flashcard_detail.value
        };
        fcom.updateWithAjax(fcom.makeUrl('Flashcards', 'setup'), data, function (res) {
            searchFlashcards(document.searchFlashcardFrm);
            $.facebox.close();
        });
    };
    flashcardCancel = function () {
        searchFlashcards(document.searchFlashcardFrm);
    }
    flashcardRemove = function (id) {
        if (!confirm(langLbl.confirmRemove)) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Flashcards', 'remove'), {cardId: id}, function (res) {
            searchFlashcards(document.searchFlashcardFrm);
        });
    };
    searchFlashcards(document.searchFlashcardFrm);
});

/* global fcom, langLbl, addEmbedIframe, COMETCHAT_APP_ID, chat_height, chat_width, testTool, COMET_CHAT_APP, LESSON_SPACE, ZOOM_APP, joinFromApp, ACTIVE_MEETING_TOOL, COMPLETED, LEARNER, CANCELLED, userType, PUBLISHED, worker, SCHEDULED, ATOM_CHAT */
(function () {
    joinMeeting = function (classId, joinFromApp) {
        fcom.ajax(fcom.makeUrl('Classes', 'joinMeeting'), {classId: classId}, function (response) {
            var res = JSON.parse(response);
            var meToolCode = res.meeting.metool_code;
            var meet = JSON.parse(res.meeting.meet_details);
            $("#endClass").removeClass('d-none');
            $('#endL').removeClass('d-none');
            if (meToolCode != ATOM_CHAT) {
                if (joinFromApp) {
                    window.open(meet.appUrl, "_blank");
                } else {
                    loadIframe(meet.joinUrl);
                }
            } else {
                createCometChatBox(meet, "#classBox");
            }
        });
    };
    endMeeting = function (classId) {
        if (confirm(endClassConfirmMsg)) {
            fcom.ajax(fcom.makeUrl('Classes', 'endMeeting'), {classId: classId}, function (response) {
                reloadPage(3000);
            });
        }
    };
    checkClassStatus = function (classId, status) {
        if (typeof statusInterval != "undefined") {
            return;
        }
        statusInterval = setInterval(function () {
            fcom.updateWithAjax(fcom.makeUrl('Classes', 'checkClassStatus', [classId]), '', function (res) {
                if (status == SCHEDULED && res.classStatus == COMPLETED) {
                    clearInterval(statusInterval);
                    reloadPage(5000);
                }
            }, {process: false});
        }, 8000);
    };
    loadIframe = function (url) {
        $('.classBox').removeClass('sesson-window__content').addClass('session-window__frame').show();
        let html = '<div id="chat_box_div" style="width:100%;height:100%;max-width:100%;border:1px solid #CCCCCC;border-radius:5px;overflow:hidden;">';
        html += '<iframe  style="width:100%;height:100%;" src="' + url + '" allow="camera; microphone; fullscreen;display-capture" frameborder="0"></iframe>';
        html += '</div>';
        $("#classBox").html(html);
    };
})();
