
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
