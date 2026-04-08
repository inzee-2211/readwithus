/* global fcom, dv, google, google, layoutDirection, ans, dataCurrencyRight, dataCurrencyLeft */
var chartData = {};
var position = (layoutDirection != 'rtl') ? 'start' : 'end';
(function () {
    getTopClassLanguage = function (interval, intervalText) {
        $('.topClassLanguage').html('<li>' + fcom.getLoader() + '</li>');
        fcom.ajax(fcom.makeUrl('Home', 'topClassLanguages'), {interval: interval}, function (response) {
            $('.topClassLanguage').html(response);
            $('.languageDurationType-js').text(intervalText);
        }, {process: false});
    };
    getTopLessonLanguage = function (interval, intervalText) {
        $('.topLessonLanguage').html('<li>' + fcom.getLoader() + '</li>');
        fcom.ajax(fcom.makeUrl('Home', 'topLessonLanguages'), {interval: interval}, function (response) {
            $('.topLessonLanguage').html(response);
            $('.languageDurationType-js2').text(intervalText);
        }, {process: false});
    };
    getTopCourseCategories = function (interval, intervalText) {
        $('.topCourseCategories').html('<li>' + fcom.getLoader() + '</li>');
        fcom.ajax(fcom.makeUrl('Home', 'topCourseCategories'), {interval: interval}, function (response) {
            $('.topCourseCategories').html(response);
            $('.languageDurationType-js2').text(intervalText);
        }, {process: false});
    };
    getStatisticsData = function () {
        $("#lessonEarning--js").html(fcom.getLoader());
        fcom.updateWithAjax(fcom.makeUrl('Home', 'dashboardStatChart'), '', function (response) {
            chartData = response;
            $("#lessonEarning--js").html('');
            callChart('lessonEarning--js', Object.keys(chartData.lessonData), Object.values(chartData.lessonData), position);
        }, {process: false});
    };
    getGoogleAnalytics = function () {
        $("#visitsGraph").html(fcom.getLoader());
        $("#piechart").html(fcom.getLoader());
        fcom.updateWithAjax(fcom.makeUrl('Home', 'getGoogleAnalytics'), '', function (response) {
            if (response.sessionErrorMsg != '') {
                $("#visitsGraph").html(response.sessionErrorMsg);
            } else {
                var dataVisits = google.visualization.arrayToDataTable(response.session);
                var optionVisits = {
                    title: '', width: $('#visitsGraph').width(),
                    height: 240,
                    curveType: 'function',
                    legend: {position: 'bottom'},
                };
                var visits = new google.visualization.LineChart(document.getElementById('visitsGraph'));
                visits.draw(dataVisits, optionVisits);
            }
            if (response.organicSearchesErrorMsg != '') {
                $("#piechart").html(response.organicSearchesErrorMsg);
                return;
            }
            var dataVisits = google.visualization.arrayToDataTable(response.organicSearches);
            var optionVisits = {
                title: '', width: $('#piechart').width(),
                height: 360,
                pieHole: 0.4,
                pieStartAngle: 100,
                legend: {position: 'bottom', textStyle: {fontSize: 12, alignment: 'center'}}
            };
            var trafic = new google.visualization.PieChart(document.getElementById('piechart'));
            trafic.draw(dataVisits, optionVisits);
        }, {process: false});
    };
    regenerateStat = function () {
        fcom.updateWithAjax(fcom.makeUrl('salesReport', 'regenerate'), '', function (t) {
            location.reload();
        });
    };
})();
$(document).ready(function () {
    $position = (layoutDirection != 'rtl') ? 'start' : 'end';
});
$(document).ready(function () {
    getStatisticsData();
    getGoogleAnalytics();
    getTopClassLanguage();
    getTopLessonLanguage();
    getTopCourseCategories();
    $('.carousel--oneforth-js').slick(getSlickSliderSettings(4));
    $('.statistics-nav-js li a').click(function () {
        $('.statistics-tab-js .tabs_panel').hide();
        $('.statistics-nav-js li a').removeClass('active');
        var activeTab = $(this).attr('rel');
        $(this).addClass('active');
        $("#" + activeTab).show();
        if ($(this).attr('data-chart')) {
            if (activeTab == 'tabs_1') {
                callChart('lessonEarning--js', Object.keys(chartData.lessonData), Object.values(chartData.lessonData), position);
            } else if (activeTab == 'tabs_2') {
                callChart('classEarning--js', Object.keys(chartData.classData), Object.values(chartData.classData), position);
            } else if (activeTab == 'tabs_3') {
                callChart('courseEarning--js', Object.keys(chartData.courseData), Object.values(chartData.courseData), position);
            } else if (activeTab == 'tabs_4') {
                callChart('userSignups--js', Object.keys(chartData.userData), Object.values(chartData.userData), position);
            }
        }
    });
});