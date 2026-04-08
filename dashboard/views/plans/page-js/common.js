/* global fcom, langLbl */
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