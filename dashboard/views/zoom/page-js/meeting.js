/* global ZoomMtg, CONF_ZOOM_VERSION */

(function () {
    var testTool = window.testTool;
    var tmpArgs = testTool.parseQuery();
    var meetingConfig = {
        apiKey: tmpArgs.apiKey,
        meetingNumber: tmpArgs.mn,
        userName: (function () {
            if (tmpArgs.name) {
                try {
                    return testTool.b64DecodeUnicode(tmpArgs.name);
                } catch (e) {
                    return tmpArgs.name;
                }
            }
            return ("CDN#" + tmpArgs.version + "#" + testTool.detectOS() + "#" + testTool.getBrowserInfo());
        })(),
        passWord: tmpArgs.pwd,
        leaveUrl: tmpArgs.leaveUrl,
        role: parseInt(tmpArgs.role, 10),
        userEmail: (function () {
            try {
                return testTool.b64DecodeUnicode(tmpArgs.email);
            } catch (e) {
                return tmpArgs.email;
            }
        })(),
        lang: tmpArgs.lang,
        signature: tmpArgs.signature || "",
        china: tmpArgs.china === "1",
    };
    if (testTool.isMobileDevice()) {
        vConsole = new VConsole();
    }
    ZoomMtg.setZoomJSLib('https://source.zoom.us/' + CONF_ZOOM_VERSION + '/lib', '/av');
    ZoomMtg.preLoadWasm();
    ZoomMtg.prepareJssdk();
    ZoomMtg.i18n.load(meetingConfig.lang);
    function beginJoin(signature) {
        ZoomMtg.init({
            leaveUrl: meetingConfig.leaveUrl,
            webEndpoint: meetingConfig.webEndpoint,
            success: function () {
                ZoomMtg.i18n.reload(meetingConfig.lang);
                ZoomMtg.join({
                    meetingNumber: meetingConfig.meetingNumber,
                    userName: meetingConfig.userName,
                    signature: signature,
                    apiKey: meetingConfig.apiKey,
                    userEmail: meetingConfig.userEmail,
                    passWord: meetingConfig.passWord,
                    success: function (res) { },
                    error: function (res) {
                        console.log(res);
                    },
                });
            },
            error: function (res) {
                console.log(res);
            },
        });
    }
    beginJoin(meetingConfig.signature);
})();