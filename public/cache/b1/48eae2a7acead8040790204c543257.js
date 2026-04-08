/* global fcom, moment, moreLinkTextLabel, FullCalendar, i, confFrontEndUrl, langLbl, userType */
var timeInterval;
var FatEventCalendar = function (teacherId, offset) {
    this.teacherId = teacherId;
    this.offset = offset;
    var seconds = 2;
    teacherId = teacherId;
    this.calDefaultConf = {
        initialView: 'timeGridWeek',
        headerToolbar: {left: 'time', center: 'title', right: 'prev,next today'},
        slotDuration: '00:05',
        buttonText: {today: langLbl.today},
        direction: layoutDirection,
        nowIndicator: true,
        navLinks: false,
        eventOverlap: false,
        slotEventOverlap: false,
        selectable: false,
        editable: false,
        selectLongPressDelay: 50,
        eventLongPressDelay: 50,
        longPressDelay: 50,
        allDaySlot: false,
        eventTimeFormat: '{hh:mm {a}}',
        slotLabelFormat: '{hh:mm {a}}',
        loading: function (isLoading) {
            if (isLoading == true) {
                jQuery("#loaderCalendar").show();
            } else {
                jQuery("#loaderCalendar").hide();
            }
        }
    };
    updateTime = function (time, calendarObj) {
        currentTimeStr = moment(time).add(seconds, 'seconds').format('YYYY-MM-DD HH:mm:ss');
        currentTimeStr = calendarObj.formatDate(currentTimeStr, 'hh:mm:ss a')
        jQuery('body').find(".fc-toolbar-ltr h6 span.timer").html(currentTimeStr);
    };
    this.setLocale = function (locale) {
        this.calDefaultConf.locale = locale;
    };
    this.startTimer = function (currentTime, calendarObj) {
        clearInterval(timeInterval);
        timeInterval = setInterval(function () {
            this.updateTime(currentTime, calendarObj);
            seconds++;
        }, 1000);
    };
    getSlotBookingConfirmationBox = function (calEvent, calendar) {
        var startDateTime = calendar.formatDate(calEvent.start, 'LLLL, d, yyyy');
        var start = calendar.formatDate(calEvent.start, 'hh:mm {a}');
        var end = calendar.formatDate(calEvent.end, 'hh:mm {a}');
        var selectedStartDateTime = moment(calEvent.start).format('YYYY-MM-DD HH:mm:ss');
        var selectedEndDateTime = moment(calEvent.end).format('YYYY-MM-DD HH:mm:ss');
        let tooltip = jQuery('.tooltipevent-wrapper-js')
        let tooltipevent = jQuery('.tooltipevent-wrapper-js')
        tooltip.find('#lesson_starttime').val(selectedStartDateTime);
        tooltip.find('#lesson_endtime').val(selectedEndDateTime);
        tooltip.find('.displayEventDate').html(startDateTime);
        tooltip.find('.displayEventTime').html(start + ' - ' + end);
        tooltipevent.css({'position': 'absolute', 'top': '50%', 'left': '50%'});
        tooltip.css('z-index', 10000);
        tooltip.removeClass('d-none');
    };
    validateStartEnd = function (info, calendar) {
        let calendarStartDateTime = calendar.view.currentStart;
        let calendarEndDateTime = calendar.view.currentEnd;
        var start = info.event.start;
        var end = info.event.end;
        if (moment(calendarStartDateTime) > moment(end) || moment(calendarEndDateTime) < moment(start)) {
            info.event.remove();
        }
        if (moment(calendarStartDateTime) > moment(start)) {
            info.event.setStart(calendarStartDateTime);
        }
        if (moment(calendarEndDateTime) < moment(info.event.end)) {
            info.event.setEnd(calendarEndDateTime);
        }
    };
    eventMerging = function (info, events, calendar) {
        validateStartEnd(info, calendar);
        var start = info.event.start;
        var end = info.event.end;
        for (i in events) {
            if (events[i]._instance.instanceId == info.oldEvent._instance.instanceId && events[i]._instance.defId == info.oldEvent._instance.defId) {
                continue;
            }
            if (moment(events[i].start) < moment(calendar.view.currentStart) || moment(events[i].end) > calendar.view.currentEnd) {
                continue;
            }
            if (moment(end) >= moment(events[i].start) && moment(start) <= moment(events[i].end)) {
                if (moment(start) > moment(events[i].start)) {
                    start = events[i].start;
                    info.event.setStart(events[i].start);
                }
                if (moment(end) < moment(events[i].end)) {
                    end = events[i].end;
                    info.event.setEnd(events[i].end);
                }
                events[i].remove();
            }
        }
    };
    removeCloseIcon = function () {
        $('.fc-timegrid-event').each(function () {
            if (!$(this).hasClass('fc-event-start')) {
                $(this).find(".closeon").remove();
            }
        });
    };
};
FatEventCalendar.prototype.WeeklyBookingCalendar = function (currentTime, duration, bookingBefore, subStartDate, days) {
    let calStartDate = moment(currentTime).format('YYYY-MM-DD');
    let calEndDate = moment(currentTime).add(days, 'days').format('YYYY-MM-DD');
    let bookingBeforeDate = moment(currentTime).add(bookingBefore, 'hours');
    if (subStartDate != '') {
        calStartDate = moment(subStartDate).format('YYYY-MM-DD');
    }
    var fecal = this;
    var calConf = {
        now: currentTime,
        selectable: true,
        validRange: {
            start: calStartDate,
            end: calEndDate
        },
        views: {timeGridWeek: {titleFormat: '{LLL {d}}, yyyy', duration: {days: 7}}},
        dayHeaderFormat: '{EEE {L/d}}',
        eventSources: [{
                events: function (fetchInfo, successCallback, failureCallback) {
                    postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss') + "&bookingBefore=" + bookingBefore;
                    fcom.updateWithAjax(fcom.makeUrl('Teachers', 'getAvailabilityJsonData', [fecal.teacherId], confFrontEndUrl), postData, function (res) {
                        let events = [];
                        let response = res.data;
                        for (i in response) {
                            if (bookingBeforeDate >= moment(response[i].end)) {
                                continue;
                            }
                            if (moment(response[i].start) < bookingBeforeDate && moment(response[i].end) > bookingBeforeDate) {
                                response[i].start = moment(bookingBeforeDate).format('YYYY-MM-DD HH:mm:ss');
                            }
                            response[i].display = 'background';
                            response[i].selectable = true;
                            response[i].editable = false;
                            events.push(response[i]);
                        }
                        successCallback(events);
                    });
                }
            },
            {
                events: function (fetchInfo, successCallback, failureCallback) {
                    postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
                    fcom.updateWithAjax(fcom.makeUrl('Teachers', 'getScheduledSessions', [fecal.teacherId], confFrontEndUrl), postData, function (events) {
                        successCallback(events.data);
                    }, {process: false});
                }
            },
        ],
        select: function (arg) {
            if (checkSlotAvailabiltAjaxRun) {
                calendar.unselect();
                return false;
            }
            let slotAvailableEl = $(arg.jsEvent.target).parents('.fc-timegrid-col-frame').find('.slot_available');
            if (slotAvailableEl.length == 0) {
                calendar.unselect();
                return false;
            }
            jQuery('body #d_calendar .closeon').click();
            jQuery("#loaderCalendar").show();
            let start = moment(arg.start);
            let end = moment(arg.start).add(duration, 'minutes');
            if (start < bookingBeforeDate || end > moment(calEndDate)) {
                jQuery("#loaderCalendar").hide();
                jQuery("body").css({"cursor": "default", "pointer-events": "initial"});
                calendar.unselect();
                return false;
            }
            checkSlotAvailabiltAjaxRun = true;
            var event = {start: moment(start).format('YYYY-MM-DD HH:mm:ss'), end: moment(end).format('YYYY-MM-DD HH:mm:ss')};
            fcom.updateWithAjax(fcom.makeUrl('Teachers', 'checkSlotAvailability', [fecal.teacherId], confFrontEndUrl), event, function (response) {
                checkSlotAvailabiltAjaxRun = false;
                jQuery("#loaderCalendar").hide();
                jQuery("body").css({"cursor": "default", "pointer-events": "initial"});
                if (response.status == 0) {
                    jQuery('body > .tooltipevent').remove();
                    calendar.unselect();
                    return false;
                }
                this.getSlotBookingConfirmationBox(event, calendar);
            }, {failed: true});
        }
    }
    var defaultConf = this.calDefaultConf;
    var conf = {...defaultConf, ...calConf};
    var calendarEl = document.getElementById('d_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    calendar.render();
    jQuery('body').find(".fc-time-button").parent().html("<h6><span>" + langLbl.myTimeZoneLabel + " :-</span> <span class='timer'>" + calendar.formatDate(currentTime, 'hh:mm:ss {a}') + "</span><span class='timezoneoffset'>(" + langLbl.timezoneString + " " + this.offset + ")</span></h6>");
    seconds = 2;
    this.startTimer(currentTime, calendar);
    jQuery(".fc-today-button,button.fc-prev-button,button.fc-next-button").click(function () {
        jQuery('body > .tooltipevent').remove();
    });
    jQuery(document).bind('close.facebox', function () {
        jQuery('body > .tooltipevent').remove();
    });
};
FatEventCalendar.prototype.TeacherDashboardCalendar = function (currentTime, userId) {
    var calConf = {
        initialView: 'dayGridMonth',
        now: currentTime,
        headerToolbar: {left: 'time', center: 'title', right: 'prev,next'},
        views: {dayGridMonth: {titleFormat: '{LLL}, yyyy'}},
        dayHeaderFormat: '{EEE}',
        moreLinkText: moreLinkTextLabel,
        eventColor: 'green',
        events: function (fetchInfo, successCallback, failureCallback) {
            var postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
            postData += "&user_type=" + userType;
            fcom.updateWithAjax(fcom.makeUrl('Teachers', 'getScheduledSessions', [userId], confFrontEndUrl), postData, function (res) {
                successCallback(res.data);
            }, {process: false});
        },
        dayMaxEvents: 1
    }
    var defaultConf = this.calDefaultConf;
    var conf = {...defaultConf, ...calConf};
    var calendarEl = document.getElementById('d_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    calendar.render();
};
FatEventCalendar.prototype.LessonMonthlyCalendar = function (currentTime) {
    var calConf = {
        initialView: 'dayGridMonth',
        now: currentTime,
        headerToolbar: {left: 'time', center: 'title', right: 'prev,next'},
        views: {dayGridMonth: {titleFormat: '{LLL}, yyyy'}},
        dayHeaderFormat: '{EEE}',
        moreLinkText: moreLinkTextLabel,
        eventColor: 'green',
        eventTimeFormat: '{HH:mm}',
        events: function (fetchInfo, successCallback, failureCallback) {
            postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
            if (document.frmLessonSearch) {
                postData = postData + "&" + fcom.frmData(document.frmLessonSearch);
            }
            fcom.updateWithAjax(fcom.makeUrl('Lessons', 'calendarJson'), postData, function (res) {
                successCallback(res.data);
                setTimeout(function () {
                    $.appalert.close();
                }, 0);
            });
        },
        dayMaxEvents: 3
    };
    var defaultConf = this.calDefaultConf;
    var conf = {...defaultConf, ...calConf};
    var calendarEl = document.getElementById('d_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    calendar.render();
};
FatEventCalendar.prototype.ClassesMonthlyCalendar = function (currentTime) {
    var calConf = {
        initialView: 'dayGridMonth',
        now: currentTime,
        headerToolbar: {left: 'time', center: 'title', right: 'prev,next'},
        views: {dayGridMonth: {titleFormat: '{LLL}, yyyy'}},
        dayHeaderFormat: '{EEE}',
        moreLinkText: moreLinkTextLabel,
        eventColor: 'green',
        eventTimeFormat: '{HH:mm}',
        events: function (fetchInfo, successCallback, failureCallback) {
            postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
            if (document.frmClassSearch) {
                postData = postData + "&" + fcom.frmData(document.frmClassSearch);
            }
            fcom.updateWithAjax(fcom.makeUrl('Classes', 'calendarJson'), postData, function (res) {
                successCallback(res.data);
                setTimeout(function () {
                    $.appalert.close();
                }, 0);
            });
        },
        dayMaxEvents: 3
    };
    var defaultConf = this.calDefaultConf;
    var conf = {...defaultConf, ...calConf};
    var calendarEl = document.getElementById('d_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    calendar.render();
};
FatEventCalendar.prototype.generalAvailaibility = function (currentTime) {
    var calConf = {
        selectable: true,
        editable: true,
        initialDate: '2018-01-21',
        slotEventOverlap: false,
        now: currentTime,
        headerToolbar: {left: 'time', center: '', right: ''},
        firstDay: 0,
        dayHeaderFormat: '{EEE}',
        eventResizableFromStart: true,
        eventSources: [{
                events: function (fetchInfo, successCallback, failureCallback) {
                    var postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
                    fcom.updateWithAjax(fcom.makeUrl('Teacher', 'generalAvblJson'), postData, function (res) {
                        successCallback(res.data);
                    }, {process: false});
                }
            }],
        select: function (arg) {
            var start = arg.start;
            var end = arg.end;
            if (moment(start).format('d') != moment(end).format('d') && moment(end).format('YYYY-MM-DD HH:mm') != moment(start).add(1, 'days').format('YYYY-MM-DD 00:00')) {
                calendar.unselect();
                return false;
            }
            var events = calendar.getEvents();
            for (i in events) {
                if (moment(end) >= moment(events[i].start) && moment(start) <= moment(events[i].end)) {
                    if (moment(start) > moment(events[i].start)) {
                        start = moment(events[i].start).format('YYYY-MM-DD') + "T" + moment(events[i].start).format('HH:mm:ss');
                    }
                    if (moment(end) < moment(events[i].end)) {
                        end = moment(events[i].end).format('YYYY-MM-DD') + "T" + moment(events[i].end).format('HH:mm:ss');
                    }
                    events[i].remove();
                }
            }
            calendar.addEvent({title: '', start: start, end: end, className: 'slot_available', allDay: false});
        },
        eventDrop: function (info) {
            eventMerging(info, calendar.getEvents(), calendar);
        },
        eventResize: function (info) {
            eventMerging(info, calendar.getEvents(), calendar);
        },
        eventDidMount: function (arg) {
            let event = arg.event;
            validateStartEnd(arg, calendar);
            element = arg.el;
            $(element).find(".fc-event-main-frame").prepend("<span class='closeon'>X</span>");
            $(element).find(".closeon").click(function () {
                if (confirm(langLbl.confirmRemove)) {
                    event.remove();
                }
            });
            removeCloseIcon();
        }
    };
    var defaultConf = this.calDefaultConf;
    var conf = {...defaultConf, ...calConf};
    var calendarEl = document.getElementById('ga_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    calendar.render();
    jQuery('body').find(".fc-time-button").parent().html("<h6><span>" + langLbl.myTimeZoneLabel +
            " :-</span> <span class='timer'>" + calendar.formatDate(currentTime, 'hh:mm:ss {a}') +
            "</span><span class='timezoneoffset'>(" + langLbl.timezoneString + " " + this.offset + ")</span></h6>");
    seconds = 2;
    this.startTimer(currentTime, calendar);
    return calendar;
};
FatEventCalendar.prototype.weeklyAvailaibility = function (currentTime, initialDate) {
    var calConf = {
        selectable: true,
        editable: true,
        now: currentTime,
        dayHeaderFormat: '{EEE {L/d}}',
        views: {timeGridWeek: {titleFormat: '{LLL {d}}, yyyy'}},
        eventResizableFromStart: true,
        events: function (fetchInfo, successCallback, failureCallback) {
            if (calendar) {
                calendar.removeAllEvents()
            }
            var postData = "start=" + moment(fetchInfo.start).subtract(1, "weeks").format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).add(1, "weeks").format('YYYY-MM-DD HH:mm:ss');
            fcom.updateWithAjax(fcom.makeUrl('Teacher', 'avalabilityJson'), postData, function (response) {
                let events = response.data;
                for (i in events) {
                    if (moment(fetchInfo.start) > moment(events[i].start) && moment(fetchInfo.end) < moment(events[i].end)) {
                        let newEvent = {
                            start: moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss'),
                            end: moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss'),
                            className: 'slot_available'
                        }
                        events.push(newEvent);
                        newEvent = {
                            start: moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss'),
                            end: events[i].end,
                            className: 'slot_available'
                        }
                        events.push(newEvent);
                        events[i].end = moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss');
                    } else if (moment(fetchInfo.start) > moment(events[i].start) && moment(fetchInfo.start) < moment(events[i].end)) {
                        let newEvent = {
                            start: moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss'),
                            end: events[i].end,
                            className: 'slot_available'
                        }
                        events[i].end = moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss');
                        events.push(newEvent);
                    } else if (moment(fetchInfo.end) > moment(events[i].start) && moment(fetchInfo.end) < moment(events[i].end)) {
                        let newEvent = {
                            start: events[i].start,
                            end: moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss'),
                            className: 'slot_available'
                        }
                        events[i].start = moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
                        events.push(newEvent);
                    }
                }
                successCallback(events);
            });
        },
        select: function (arg) {
            var start = arg.start;
            var end = arg.end;
            if (start < moment(calendar.view.currentStart)) {
                start = calendar.view.currentStart;
            }
            if (end > calendar.view.currentEnd) {
                end = calendar.view.currentEnd;
            }
            var events = calendar.getEvents();
            for (i in events) {
                if (moment(events[i].start) < moment(calendar.view.currentStart) || moment(events[i].end) > calendar.view.currentEnd) {
                    continue;
                }
                if (moment(end) >= moment(events[i].start) && moment(start) <= moment(events[i].end)) {
                    if (moment(start) > moment(events[i].start)) {
                        start = events[i].start;
                    }
                    if (moment(end) < moment(events[i].end)) {
                        end = events[i].end;
                    }
                    events[i].remove();
                }
            }
            calendar.addEvent({end: end, start: start, className: 'slot_available'});
        },
        eventDrop: function (info) {
            eventMerging(info, calendar.getEvents(), calendar);
        },
        eventResize: function (info) {
            eventMerging(info, calendar.getEvents(), calendar);
        },
        eventDidMount: function (arg) {
            let event = arg.event;
            let element = arg.el;
            $(element).find(".fc-event-main-frame").prepend("<span class='closeon'>X</span>");
            $(element).find(".closeon").click(function () {
                if (confirm(langLbl.confirmRemove)) {
                    event.remove();
                }
            });
            removeCloseIcon();
        }
    };
    var defaultConf = this.calDefaultConf;
    var conf = {...defaultConf, ...calConf};
    if (initialDate && initialDate != '') {
        conf.initialDate = initialDate;
    }
    var calendarEl = document.getElementById('w_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    calendar.render();
    jQuery('body').find(".fc-time-button").parent().html("<h6><span>" + langLbl.myTimeZoneLabel + " :-</span> <span class='timer'>" + calendar.formatDate(currentTime, 'hh:mm:ss {a}') + "</span><span class='timezoneoffset'>(" + langLbl.timezoneString + " " + this.offset + ")</span></h6>");
    seconds = 2;
    this.startTimer(currentTime, calendar);
    return calendar;
};