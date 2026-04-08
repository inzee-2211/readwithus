/* global fcom, langLbl */

searchLessons = null;
var isLessonCancelAjaxRun = false;
requestReschedule = function (id) {
    fcom.ajax(fcom.makeUrl('Lessons', 'requestReschedule', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

requestRescheduleSetup = function (frm) {
    if (!$(frm).validate())
        return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('Lessons', 'requestRescheduleSetup'), data, function (t) {
        $.facebox.close();
        location.reload();
    });
};

cancelLesson = function (id) {
    fcom.ajax(fcom.makeUrl('Lessons', 'cancelLesson', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

cancelLessonSetup = function (frm) {
    if (isLessonCancelAjaxRun) {
        return false;
    }
    isLessonCancelAjaxRun = true;
    if (!$(frm).validate())
        return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('Lessons', 'cancelLessonSetup'), data, function (t) {
        $.facebox.close();
        location.reload();
    });
};

viewBookingCalendar = function (id) {
    fcom.ajax(fcom.makeUrl('Lessons', 'viewBookingCalendar', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

goToPlanSearchPage = function (pageno) {
    var frm = document.planSearchFrm;
    $(frm.pageno).val(pageno);
    fcom.ajax(fcom.makeUrl('Plans', 'search'), fcom.frmData(document.planSearchFrm), function (res) {
        $(".plan-listing#listing").html(res);
    });
};

scheduleLessonSetup = function (lessonId, startTime, endTime, date) {
    fcom.ajax(fcom.makeUrl('Lessons', 'scheduleLessonSetup'), 'lessonId=' + lessonId + '&startTime=' + startTime + '&endTime=' + endTime + '&date=' + date, function (doc) {
        $.facebox.close();
        location.reload();
    });
};


$(document).ready(function () {
    $(document).on("click", '.iss_accordion', function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
});/**
 * @preserve jQuery DateTimePicker plugin v2.4.1
 * @homepage http://xdsoft.net/jqplugins/datetimepicker/
 * (c) 2014, Chupurnov Valeriy.
 */
/*global document,window,jQuery,setTimeout,clearTimeout*/
(function ($) {
	'use strict';
	var default_options  = {
		i18n: {
			ar: { // Arabic
				months: [
					"كانون الثاني", "شباط", "آذار", "نيسان", "مايو", "حزيران", "تموز", "آب", "أيلول", "تشرين الأول", "تشرين الثاني", "كانون الأول"
				],
				dayOfWeek: [
					"ن", "ث", "ع", "خ", "ج", "س", "ح"
				]
			},
			ro: { // Romanian
				months: [
					"ianuarie", "februarie", "martie", "aprilie", "mai", "iunie", "iulie", "august", "septembrie", "octombrie", "noiembrie", "decembrie"
				],
				dayOfWeek: [
					"l", "ma", "mi", "j", "v", "s", "d"
				]
			},
			id: { // Indonesian
				months: [
					"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"
				],
				dayOfWeek: [
					"Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"
				]
			},
			bg: { // Bulgarian
				months: [
					"Януари", "Февруари", "Март", "Април", "Май", "Юни", "Юли", "Август", "Септември", "Октомври", "Ноември", "Декември"
				],
				dayOfWeek: [
					"Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"
				]
			},
			fa: { // Persian/Farsi
				months: [
					'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
				],
				dayOfWeek: [
					'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'
				]
			},
			ru: { // Russian
				months: [
					'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
				],
				dayOfWeek: [
					"Вск", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"
				]
			},
			uk: { // Ukrainian
				months: [
					'Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'
				],
				dayOfWeek: [
					"Ндл", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Сбт"
				]
			},
			en: { // English
				months: [
					"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
				],
				dayOfWeek: [
					"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
				]
			},
			el: { // Ελληνικά
				months: [
					"Ιανουάριος", "Φεβρουάριος", "Μάρτιος", "Απρίλιος", "Μάιος", "Ιούνιος", "Ιούλιος", "Αύγουστος", "Σεπτέμβριος", "Οκτώβριος", "Νοέμβριος", "Δεκέμβριος"
				],
				dayOfWeek: [
					"Κυρ", "Δευ", "Τρι", "Τετ", "Πεμ", "Παρ", "Σαβ"
				]
			},
			de: { // German
				months: [
					'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
				],
				dayOfWeek: [
					"So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"
				]
			},
			nl: { // Dutch
				months: [
					"januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"
				],
				dayOfWeek: [
					"zo", "ma", "di", "wo", "do", "vr", "za"
				]
			},
			tr: { // Turkish
				months: [
					"Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"
				],
				dayOfWeek: [
					"Paz", "Pts", "Sal", "Çar", "Per", "Cum", "Cts"
				]
			},
			fr: { //French
				months: [
					"Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
				],
				dayOfWeek: [
					"Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"
				]
			},
			es: { // Spanish
				months: [
					"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
				],
				dayOfWeek: [
					"Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"
				]
			},
			th: { // Thai
				months: [
					'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
				],
				dayOfWeek: [
					'อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'
				]
			},
			pl: { // Polish
				months: [
					"styczeń", "luty", "marzec", "kwiecień", "maj", "czerwiec", "lipiec", "sierpień", "wrzesień", "październik", "listopad", "grudzień"
				],
				dayOfWeek: [
					"nd", "pn", "wt", "śr", "cz", "pt", "sb"
				]
			},
			pt: { // Portuguese
				months: [
					"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
				],
				dayOfWeek: [
					"Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
				]
			},
			ch: { // Simplified Chinese
				months: [
					"一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"
				],
				dayOfWeek: [
					"日", "一", "二", "三", "四", "五", "六"
				]
			},
			se: { // Swedish
				months: [
					"Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September",  "Oktober", "November", "December"
				],
				dayOfWeek: [
					"Sön", "Mån", "Tis", "Ons", "Tor", "Fre", "Lör"
				]
			},
			kr: { // Korean
				months: [
					"1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"
				],
				dayOfWeek: [
					"일", "월", "화", "수", "목", "금", "토"
				]
			},
			it: { // Italian
				months: [
					"Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"
				],
				dayOfWeek: [
					"Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab"
				]
			},
			da: { // Dansk
				months: [
					"January", "Februar", "Marts", "April", "Maj", "Juni", "July", "August", "September", "Oktober", "November", "December"
				],
				dayOfWeek: [
					"Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"
				]
			},
			no: { // Norwegian
				months: [
					"Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"
				],
				dayOfWeek: [
					"Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"
				]
			},
			ja: { // Japanese
				months: [
					"1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"
				],
				dayOfWeek: [
					"日", "月", "火", "水", "木", "金", "土"
				]
			},
			vi: { // Vietnamese
				months: [
					"Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
				],
				dayOfWeek: [
					"CN", "T2", "T3", "T4", "T5", "T6", "T7"
				]
			},
			sl: { // Slovenščina
				months: [
					"Januar", "Februar", "Marec", "April", "Maj", "Junij", "Julij", "Avgust", "September", "Oktober", "November", "December"
				],
				dayOfWeek: [
					"Ned", "Pon", "Tor", "Sre", "Čet", "Pet", "Sob"
				]
			},
			cs: { // Čeština
				months: [
					"Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"
				],
				dayOfWeek: [
					"Ne", "Po", "Út", "St", "Čt", "Pá", "So"
				]
			},
			hu: { // Hungarian
				months: [
					"Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December"
				],
				dayOfWeek: [
					"Va", "Hé", "Ke", "Sze", "Cs", "Pé", "Szo"
				]
			},
			az: { //Azerbaijanian (Azeri)
				months: [
					"Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avqust", "Sentyabr", "Oktyabr", "Noyabr", "Dekabr"
				],
				dayOfWeek: [
					"B", "Be", "Ça", "Ç", "Ca", "C", "Ş"
				]
			},
			bs: { //Bosanski
				months: [
					"Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"
				],
				dayOfWeek: [
					"Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub"
				]
			},
			ca: { //Català
				months: [
					"Gener", "Febrer", "Març", "Abril", "Maig", "Juny", "Juliol", "Agost", "Setembre", "Octubre", "Novembre", "Desembre"
				],
				dayOfWeek: [
					"Dg", "Dl", "Dt", "Dc", "Dj", "Dv", "Ds"
				]
			},
			'en-GB': { //English (British)
				months: [
					"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
				],
				dayOfWeek: [
					"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
				]
			},
			et: { //"Eesti"
				months: [
					"Jaanuar", "Veebruar", "Märts", "Aprill", "Mai", "Juuni", "Juuli", "August", "September", "Oktoober", "November", "Detsember"
				],
				dayOfWeek: [
					"P", "E", "T", "K", "N", "R", "L"
				]
			},
			eu: { //Euskara
				months: [
					"Urtarrila", "Otsaila", "Martxoa", "Apirila", "Maiatza", "Ekaina", "Uztaila", "Abuztua", "Iraila", "Urria", "Azaroa", "Abendua"
				],
				dayOfWeek: [
					"Ig.", "Al.", "Ar.", "Az.", "Og.", "Or.", "La."
				]
			},
			fi: { //Finnish (Suomi)
				months: [
					"Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kesäkuu", "Heinäkuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu"
				],
				dayOfWeek: [
					"Su", "Ma", "Ti", "Ke", "To", "Pe", "La"
				]
			},
			gl: { //Galego
				months: [
					"Xan", "Feb", "Maz", "Abr", "Mai", "Xun", "Xul", "Ago", "Set", "Out", "Nov", "Dec"
				],
				dayOfWeek: [
					"Dom", "Lun", "Mar", "Mer", "Xov", "Ven", "Sab"
				]
			},
			hr: { //Hrvatski
				months: [
					"Siječanj", "Veljača", "Ožujak", "Travanj", "Svibanj", "Lipanj", "Srpanj", "Kolovoz", "Rujan", "Listopad", "Studeni", "Prosinac"
				],
				dayOfWeek: [
					"Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub"
				]
			},
			ko: { //Korean (한국어)
				months: [
					"1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"
				],
				dayOfWeek: [
					"일", "월", "화", "수", "목", "금", "토"
				]
			},
			lt: { //Lithuanian (lietuvių)
				months: [
					"Sausio", "Vasario", "Kovo", "Balandžio", "Gegužės", "Birželio", "Liepos", "Rugpjūčio", "Rugsėjo", "Spalio", "Lapkričio", "Gruodžio"
				],
				dayOfWeek: [
					"Sek", "Pir", "Ant", "Tre", "Ket", "Pen", "Šeš"
				]
			},
			lv: { //Latvian (Latviešu)
				months: [
					"Janvāris", "Februāris", "Marts", "Aprīlis ", "Maijs", "Jūnijs", "Jūlijs", "Augusts", "Septembris", "Oktobris", "Novembris", "Decembris"
				],
				dayOfWeek: [
					"Sv", "Pr", "Ot", "Tr", "Ct", "Pk", "St"
				]
			},
			mk: { //Macedonian (Македонски)
				months: [
					"јануари", "февруари", "март", "април", "мај", "јуни", "јули", "август", "септември", "октомври", "ноември", "декември"
				],
				dayOfWeek: [
					"нед", "пон", "вто", "сре", "чет", "пет", "саб"
				]
			},
			mn: { //Mongolian (Монгол)
				months: [
					"1-р сар", "2-р сар", "3-р сар", "4-р сар", "5-р сар", "6-р сар", "7-р сар", "8-р сар", "9-р сар", "10-р сар", "11-р сар", "12-р сар"
				],
				dayOfWeek: [
					"Дав", "Мяг", "Лха", "Пүр", "Бсн", "Бям", "Ням"
				]
			},
			'pt-BR': { //Português(Brasil)
				months: [
					"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
				],
				dayOfWeek: [
					"Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"
				]
			},
			sk: { //Slovenčina
				months: [
					"Január", "Február", "Marec", "Apríl", "Máj", "Jún", "Júl", "August", "September", "Október", "November", "December"
				],
				dayOfWeek: [
					"Ne", "Po", "Ut", "St", "Št", "Pi", "So"
				]
			},
			sq: { //Albanian (Shqip)
				months: [
					"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
				],
				dayOfWeek: [
					"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
				]
			},
			'sr-YU': { //Serbian (Srpski)
				months: [
					"Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"
				],
				dayOfWeek: [
					"Ned", "Pon", "Uto", "Sre", "čet", "Pet", "Sub"
				]
			},
			sr: { //Serbian Cyrillic (Српски)
				months: [
					"јануар", "фебруар", "март", "април", "мај", "јун", "јул", "август", "септембар", "октобар", "новембар", "децембар"
				],
				dayOfWeek: [
					"нед", "пон", "уто", "сре", "чет", "пет", "суб"
				]
			},
			sv: { //Svenska
				months: [
					"Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"
				],
				dayOfWeek: [
					"Sön", "Mån", "Tis", "Ons", "Tor", "Fre", "Lör"
				]
			},
			'zh-TW': { //Traditional Chinese (繁體中文)
				months: [
					"一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"
				],
				dayOfWeek: [
					"日", "一", "二", "三", "四", "五", "六"
				]
			},
			zh: { //Simplified Chinese (简体中文)
				months: [
					"一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"
				],
				dayOfWeek: [
					"日", "一", "二", "三", "四", "五", "六"
				]
			},
			he: { //Hebrew (עברית)
				months: [
					'ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'
				],
				dayOfWeek: [
					'א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'
				]
			},
			hy: { // Armenian
				months: [
					"Հունվար", "Փետրվար", "Մարտ", "Ապրիլ", "Մայիս", "Հունիս", "Հուլիս", "Օգոստոս", "Սեպտեմբեր", "Հոկտեմբեր", "Նոյեմբեր", "Դեկտեմբեր"
				],
				dayOfWeek: [
					"Կի", "Երկ", "Երք", "Չոր", "Հնգ", "Ուրբ", "Շբթ"
				]
			}
		},
		value: '',
		lang: 'en',

		format:	'Y/m/d H:i',
		formatTime:	'H:i',
		formatDate:	'Y/m/d',

		startDate:	false, // new Date(), '1986/12/08', '-1970/01/05','-1970/01/05',
		step: 60,
		monthChangeSpinner: true,

		closeOnDateSelect: true,
		closeOnWithoutClick: true,
		closeOnInputClick: true,

		timepicker: true,
		datepicker: true,
		weeks: false,

		defaultTime: false,	// use formatTime format (ex. '10:00' for formatTime:	'H:i')
		defaultDate: false,	// use formatDate format (ex new Date() or '1986/12/08' or '-1970/01/05' or '-1970/01/05')

		minDate: false,
		maxDate: false,
		minTime: false,
		maxTime: false,

		allowTimes: [],
		opened: false,
		initTime: true,
		inline: false,
		theme: '',

		onSelectDate: function () {},
		onSelectTime: function () {},
		onChangeMonth: function () {},
		onChangeYear: function () {},
		onChangeDateTime: function () {},
		onShow: function () {},
		onClose: function () {},
		onGenerate: function () {},

		withoutCopyright: true,
		inverseButton: false,
		hours12: false,
		next:	'xdsoft_next',
		prev : 'xdsoft_prev',
		dayOfWeekStart: 0,
		parentID: 'body',
		timeHeightInTimePicker: 25,
		timepickerScrollbar: true,
		todayButton: true,
		defaultSelect: true,

		scrollMonth: true,
		scrollTime: true,
		scrollInput: true,

		lazyInit: false,
		mask: false,
		validateOnBlur: true,
		allowBlank: true,
		yearStart: 1950,
		yearEnd: 2050,
		style: '',
		id: '',
		fixed: false,
		roundTime: 'round', // ceil, floor
		className: '',
		weekends: [],
		disabledDates : [],
		yearOffset: 0,
		beforeShowDay: null,

		enterLikeTab: true
	};
	// fix for ie8
	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function (obj, start) {
			var i, j;
			for (i = (start || 0), j = this.length; i < j; i += 1) {
				if (this[i] === obj) { return i; }
			}
			return -1;
		};
	}
	Date.prototype.countDaysInMonth = function () {
		return new Date(this.getFullYear(), this.getMonth() + 1, 0).getDate();
	};
	$.fn.xdsoftScroller = function (percent) {
		return this.each(function () {
			var timeboxparent = $(this),
				pointerEventToXY = function (e) {
					var out = {x: 0, y: 0},
						touch;
					if (e.type === 'touchstart' || e.type === 'touchmove' || e.type === 'touchend' || e.type === 'touchcancel') {
						touch  = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
						out.x = touch.clientX;
						out.y = touch.clientY;
					} else if (e.type === 'mousedown' || e.type === 'mouseup' || e.type === 'mousemove' || e.type === 'mouseover' || e.type === 'mouseout' || e.type === 'mouseenter' || e.type === 'mouseleave') {
						out.x = e.clientX;
						out.y = e.clientY;
					}
					return out;
				},
				move = 0,
				timebox,
				parentHeight,
				height,
				scrollbar,
				scroller,
				maximumOffset = 100,
				start = false,
				startY = 0,
				startTop = 0,
				h1 = 0,
				touchStart = false,
				startTopScroll = 0,
				calcOffset = function () {};
			if (percent === 'hide') {
				timeboxparent.find('.xdsoft_scrollbar').hide();
				return;
			}
			if (!$(this).hasClass('xdsoft_scroller_box')) {
				timebox = timeboxparent.children().eq(0);
				parentHeight = timeboxparent[0].clientHeight;
				height = timebox[0].offsetHeight;
				scrollbar = $('<div class="xdsoft_scrollbar"></div>');
				scroller = $('<div class="xdsoft_scroller"></div>');
				scrollbar.append(scroller);

				timeboxparent.addClass('xdsoft_scroller_box').append(scrollbar);
				calcOffset = function calcOffset(event) {
					var offset = pointerEventToXY(event).y - startY + startTopScroll;
					if (offset < 0) {
						offset = 0;
					}
					if (offset + scroller[0].offsetHeight > h1) {
						offset = h1 - scroller[0].offsetHeight;
					}
					timeboxparent.trigger('scroll_element.xdsoft_scroller', [maximumOffset ? offset / maximumOffset : 0]);
				};

				scroller
					.on('touchstart.xdsoft_scroller mousedown.xdsoft_scroller', function (event) {
						if (!parentHeight) {
							timeboxparent.trigger('resize_scroll.xdsoft_scroller', [percent]);
						}

						startY = pointerEventToXY(event).y;
						startTopScroll = parseInt(scroller.css('margin-top'), 10);
						h1 = scrollbar[0].offsetHeight;

						if (event.type === 'mousedown') {
							if (document) {
								$(document.body).addClass('xdsoft_noselect');
							}
							$([document.body, window]).on('mouseup.xdsoft_scroller', function arguments_callee() {
								$([document.body, window]).off('mouseup.xdsoft_scroller', arguments_callee)
									.off('mousemove.xdsoft_scroller', calcOffset)
									.removeClass('xdsoft_noselect');
							});
							$(document.body).on('mousemove.xdsoft_scroller', calcOffset);
						} else {
							touchStart = true;
							event.stopPropagation();
							event.preventDefault();
						}
					})
					.on('touchmove', function (event) {
						if (touchStart) {
							event.preventDefault();
							calcOffset(event);
						}
					})
					.on('touchend touchcancel', function (event) {
						touchStart =  false;
						startTopScroll = 0;
					});

				timeboxparent
					.on('scroll_element.xdsoft_scroller', function (event, percentage) {
						if (!parentHeight) {
							timeboxparent.trigger('resize_scroll.xdsoft_scroller', [percentage, true]);
						}
						percentage = percentage > 1 ? 1 : (percentage < 0 || isNaN(percentage)) ? 0 : percentage;

						scroller.css('margin-top', maximumOffset * percentage);

						setTimeout(function () {
							timebox.css('marginTop', -parseInt((timebox[0].offsetHeight - parentHeight) * percentage, 10));
						}, 10);
					})
					.on('resize_scroll.xdsoft_scroller', function (event, percentage, noTriggerScroll) {
						var percent, sh;
						parentHeight = timeboxparent[0].clientHeight;
						height = timebox[0].offsetHeight;
						percent = parentHeight / height;
						sh = percent * scrollbar[0].offsetHeight;
						if (percent > 1) {
							scroller.hide();
						} else {
							scroller.show();
							scroller.css('height', parseInt(sh > 10 ? sh : 10, 10));
							maximumOffset = scrollbar[0].offsetHeight - scroller[0].offsetHeight;
							if (noTriggerScroll !== true) {
								timeboxparent.trigger('scroll_element.xdsoft_scroller', [percentage || Math.abs(parseInt(timebox.css('marginTop'), 10)) / (height - parentHeight)]);
							}
						}
					});

				timeboxparent.on('mousewheel', function (event) {
					var top = Math.abs(parseInt(timebox.css('marginTop'), 10));

					top = top - (event.deltaY * 20);
					if (top < 0) {
						top = 0;
					}

					timeboxparent.trigger('scroll_element.xdsoft_scroller', [top / (height - parentHeight)]);
					event.stopPropagation();
					return false;
				});

				timeboxparent.on('touchstart', function (event) {
					start = pointerEventToXY(event);
					startTop = Math.abs(parseInt(timebox.css('marginTop'), 10));
				});

				timeboxparent.on('touchmove', function (event) {
					if (start) {
						event.preventDefault();
						var coord = pointerEventToXY(event);
						timeboxparent.trigger('scroll_element.xdsoft_scroller', [(startTop - (coord.y - start.y)) / (height - parentHeight)]);
					}
				});

				timeboxparent.on('touchend touchcancel', function (event) {
					start = false;
					startTop = 0;
				});
			}
			timeboxparent.trigger('resize_scroll.xdsoft_scroller', [percent]);
		});
	};

	$.fn.datetimepicker = function (opt) {
		var KEY0 = 48,
			KEY9 = 57,
			_KEY0 = 96,
			_KEY9 = 105,
			CTRLKEY = 17,
			DEL = 46,
			ENTER = 13,
			ESC = 27,
			BACKSPACE = 8,
			ARROWLEFT = 37,
			ARROWUP = 38,
			ARROWRIGHT = 39,
			ARROWDOWN = 40,
			TAB = 9,
			F5 = 116,
			AKEY = 65,
			CKEY = 67,
			VKEY = 86,
			ZKEY = 90,
			YKEY = 89,
			ctrlDown	=	false,
			options = ($.isPlainObject(opt) || !opt) ? $.extend(true, {}, default_options, opt) : $.extend(true, {}, default_options),

			lazyInitTimer = 0,
			createDateTimePicker,
			destroyDateTimePicker,

			lazyInit = function (input) {
				input
					.on('open.xdsoft focusin.xdsoft mousedown.xdsoft', function initOnActionCallback(event) {
						if (input.is(':disabled') || input.data('xdsoft_datetimepicker')) {
							return;
						}
						clearTimeout(lazyInitTimer);
						lazyInitTimer = setTimeout(function () {

							if (!input.data('xdsoft_datetimepicker')) {
								createDateTimePicker(input);
							}
							input
								.off('open.xdsoft focusin.xdsoft mousedown.xdsoft', initOnActionCallback)
								.trigger('open.xdsoft');
						}, 100);
					});
			};

		createDateTimePicker = function (input) {
			var datetimepicker = $('<div ' + (options.id ? 'id="' + options.id + '"' : '') + ' ' + (options.style ? 'style="' + options.style + '"' : '') + ' class="xdsoft_datetimepicker xdsoft_' + options.theme + ' xdsoft_noselect ' + (options.weeks ? ' xdsoft_showweeks' : '') + options.className + '"></div>'),
				xdsoft_copyright = $('<div class="xdsoft_copyright"><a target="_blank" href="http://xdsoft.net/jqplugins/datetimepicker/">xdsoft.net</a></div>'),
				datepicker = $('<div class="xdsoft_datepicker active"></div>'),
				mounth_picker = $('<div class="xdsoft_mounthpicker"><button type="button" class="xdsoft_prev"></button><button type="button" class="xdsoft_today_button"></button>' +
					'<div class="xdsoft_label xdsoft_month"><span></span><i></i></div>' +
					'<div class="xdsoft_label xdsoft_year"><span></span><i></i></div>' +
					'<button type="button" class="xdsoft_next"></button></div>'),
				calendar = $('<div class="xdsoft_calendar"></div>'),
				timepicker = $('<div class="xdsoft_timepicker active"><button type="button" class="xdsoft_prev"></button><div class="xdsoft_time_box"></div><button type="button" class="xdsoft_next"></button></div>'),
				timeboxparent = timepicker.find('.xdsoft_time_box').eq(0),
				timebox = $('<div class="xdsoft_time_variant"></div>'),
				/*scrollbar = $('<div class="xdsoft_scrollbar"></div>'),
				scroller = $('<div class="xdsoft_scroller"></div>'),*/
				monthselect = $('<div class="xdsoft_select xdsoft_monthselect"><div></div></div>'),
				yearselect = $('<div class="xdsoft_select xdsoft_yearselect"><div></div></div>'),
				triggerAfterOpen = false,
				XDSoft_datetime,
				//scroll_element,
				xchangeTimer,
				timerclick,
				current_time_index,
				setPos,
				timer = 0,
				timer1 = 0,
				_xdsoft_datetime;

			mounth_picker
				.find('.xdsoft_month span')
					.after(monthselect);
			mounth_picker
				.find('.xdsoft_year span')
					.after(yearselect);

			mounth_picker
				.find('.xdsoft_month,.xdsoft_year')
					.on('mousedown.xdsoft', function (event) {
					var select = $(this).find('.xdsoft_select').eq(0),
						val = 0,
						top = 0,
						visible = select.is(':visible'),
						items,
						i;

					mounth_picker
						.find('.xdsoft_select')
							.hide();
					if (_xdsoft_datetime.currentTime) {
						val = _xdsoft_datetime.currentTime[$(this).hasClass('xdsoft_month') ? 'getMonth' : 'getFullYear']();
					}

					select[visible ? 'hide' : 'show']();
					for (items = select.find('div.xdsoft_option'), i = 0; i < items.length; i += 1) {
						if (items.eq(i).data('value') === val) {
							break;
						} else {
							top += items[0].offsetHeight;
						}
					}

					select.xdsoftScroller(top / (select.children()[0].offsetHeight - (select[0].clientHeight)));
					event.stopPropagation();
					return false;
				});

			mounth_picker
				.find('.xdsoft_select')
					.xdsoftScroller()
				.on('mousedown.xdsoft', function (event) {
					event.stopPropagation();
					event.preventDefault();
				})
				.on('mousedown.xdsoft', '.xdsoft_option', function (event) {

					if (_xdsoft_datetime.currentTime === undefined || _xdsoft_datetime.currentTime === null) {
						_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
					}

					var year = _xdsoft_datetime.currentTime.getFullYear();
					if (_xdsoft_datetime && _xdsoft_datetime.currentTime) {
						_xdsoft_datetime.currentTime[$(this).parent().parent().hasClass('xdsoft_monthselect') ? 'setMonth' : 'setFullYear']($(this).data('value'));
					}

					$(this).parent().parent().hide();

					datetimepicker.trigger('xchange.xdsoft');
					if (options.onChangeMonth && $.isFunction(options.onChangeMonth)) {
						options.onChangeMonth.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
					}

					if (year !== _xdsoft_datetime.currentTime.getFullYear() && $.isFunction(options.onChangeYear)) {
						options.onChangeYear.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
					}
				});

			datetimepicker.setOptions = function (_options) {
				options = $.extend(true, {}, options, _options);

				if (_options.allowTimes && $.isArray(_options.allowTimes) && _options.allowTimes.length) {
					options.allowTimes = $.extend(true, [], _options.allowTimes);
				}

				if (_options.weekends && $.isArray(_options.weekends) && _options.weekends.length) {
					options.weekends = $.extend(true, [], _options.weekends);
				}

				if (_options.disabledDates && $.isArray(_options.disabledDates) && _options.disabledDates.length) {
                    options.disabledDates = $.extend(true, [], _options.disabledDates);
                }

				if ((options.open || options.opened) && (!options.inline)) {
					input.trigger('open.xdsoft');
				}

				if (options.inline) {
					triggerAfterOpen = true;
					datetimepicker.addClass('xdsoft_inline');
					input.after(datetimepicker).hide();
				}

				if (options.inverseButton) {
					options.next = 'xdsoft_prev';
					options.prev = 'xdsoft_next';
				}

				if (options.datepicker) {
					datepicker.addClass('active');
				} else {
					datepicker.removeClass('active');
				}

				if (options.timepicker) {
					timepicker.addClass('active');
				} else {
					timepicker.removeClass('active');
				}

				if (options.value) {
					if (input && input.val) {
						input.val(options.value);
					}
					_xdsoft_datetime.setCurrentTime(options.value);
				}

				if (isNaN(options.dayOfWeekStart)) {
					options.dayOfWeekStart = 0;
				} else {
					options.dayOfWeekStart = parseInt(options.dayOfWeekStart, 10) % 7;
				}

				if (!options.timepickerScrollbar) {
					timeboxparent.xdsoftScroller('hide');
				}

				if (options.minDate && /^-(.*)$/.test(options.minDate)) {
					options.minDate = _xdsoft_datetime.strToDateTime(options.minDate).dateFormat(options.formatDate);
				}

				if (options.maxDate &&  /^\+(.*)$/.test(options.maxDate)) {
					options.maxDate = _xdsoft_datetime.strToDateTime(options.maxDate).dateFormat(options.formatDate);
				}

				mounth_picker
					.find('.xdsoft_today_button')
						.css('visibility', !options.todayButton ? 'hidden' : 'visible');

				if (options.mask) {
					var e,
						getCaretPos = function (input) {
							try {
								if (document.selection && document.selection.createRange) {
									var range = document.selection.createRange();
									return range.getBookmark().charCodeAt(2) - 2;
								}
								if (input.setSelectionRange) {
									return input.selectionStart;
								}
							} catch (e) {
								return 0;
							}
						},
						setCaretPos = function (node, pos) {
							node = (typeof node === "string" || node instanceof String) ? document.getElementById(node) : node;
							if (!node) {
								return false;
							}
							if (node.createTextRange) {
								var textRange = node.createTextRange();
								textRange.collapse(true);
								textRange.moveEnd('character', pos);
								textRange.moveStart('character', pos);
								textRange.select();
								return true;
							}
							if (node.setSelectionRange) {
								node.setSelectionRange(pos, pos);
								return true;
							}
							return false;
						},
						isValidValue = function (mask, value) {
							var reg = mask
								.replace(/([\[\]\/\{\}\(\)\-\.\+]{1})/g, '\\$1')
								.replace(/_/g, '{digit+}')
								.replace(/([0-9]{1})/g, '{digit$1}')
								.replace(/\{digit([0-9]{1})\}/g, '[0-$1_]{1}')
								.replace(/\{digit[\+]\}/g, '[0-9_]{1}');
							return (new RegExp(reg)).test(value);
						};
					input.off('keydown.xdsoft');

					if (options.mask === true) {
						options.mask = options.format
							.replace(/Y/g, '9999')
							.replace(/F/g, '9999')
							.replace(/m/g, '19')
							.replace(/d/g, '39')
							.replace(/H/g, '29')
							.replace(/i/g, '59')
							.replace(/s/g, '59');
					}

					if ($.type(options.mask) === 'string') {
						if (!isValidValue(options.mask, input.val())) {
							input.val(options.mask.replace(/[0-9]/g, '_'));
						}

						input.on('keydown.xdsoft', function (event) {
							var val = this.value,
								key = event.which,
								pos,
								digit;

							if (((key >= KEY0 && key <= KEY9) || (key >= _KEY0 && key <= _KEY9)) || (key === BACKSPACE || key === DEL)) {
								pos = getCaretPos(this);
								digit = (key !== BACKSPACE && key !== DEL) ? String.fromCharCode((_KEY0 <= key && key <= _KEY9) ? key - KEY0 : key) : '_';

								if ((key === BACKSPACE || key === DEL) && pos) {
									pos -= 1;
									digit = '_';
								}

								while (/[^0-9_]/.test(options.mask.substr(pos, 1)) && pos < options.mask.length && pos > 0) {
									pos += (key === BACKSPACE || key === DEL) ? -1 : 1;
								}

								val = val.substr(0, pos) + digit + val.substr(pos + 1);
								if ($.trim(val) === '') {
									val = options.mask.replace(/[0-9]/g, '_');
								} else {
									if (pos === options.mask.length) {
										event.preventDefault();
										return false;
									}
								}

								pos += (key === BACKSPACE || key === DEL) ? 0 : 1;
								while (/[^0-9_]/.test(options.mask.substr(pos, 1)) && pos < options.mask.length && pos > 0) {
									pos += (key === BACKSPACE || key === DEL) ? -1 : 1;
								}

								if (isValidValue(options.mask, val)) {
									this.value = val;
									setCaretPos(this, pos);
								} else if ($.trim(val) === '') {
									this.value = options.mask.replace(/[0-9]/g, '_');
								} else {
									input.trigger('error_input.xdsoft');
								}
							} else {
								if (([AKEY, CKEY, VKEY, ZKEY, YKEY].indexOf(key) !== -1 && ctrlDown) || [ESC, ARROWUP, ARROWDOWN, ARROWLEFT, ARROWRIGHT, F5, CTRLKEY, TAB, ENTER].indexOf(key) !== -1) {
									return true;
								}
							}

							event.preventDefault();
							return false;
						});
					}
				}
				if (options.validateOnBlur) {
					input
						.off('blur.xdsoft')
						.on('blur.xdsoft', function () {
						  if (options.allowBlank && !$.trim($(this).val()).length) {
						    $(this).val(null);
						    datetimepicker.data('xdsoft_datetime').empty();
						  } else if (!Date.parseDate($(this).val(), options.format)) {
						    var splittedHours   = +([$(this).val()[0], $(this).val()[1]].join('')),
						        splittedMinutes = +([$(this).val()[2], $(this).val()[3]].join(''));
						    
						    // parse the numbers as 0312 => 03:12
						    if(!options.datepicker && options.timepicker && splittedHours >= 0 && splittedHours < 24 && splittedMinutes >= 0 && splittedMinutes < 60) {
						      $(this).val([splittedHours, splittedMinutes].map(function(item) {
						        return item > 9 ? item : '0' + item
						      }).join(':'));
						    } else {
						      $(this).val((_xdsoft_datetime.now()).dateFormat(options.format));
						    }
						    
						    datetimepicker.data('xdsoft_datetime').setCurrentTime($(this).val());
						  } else {
						    datetimepicker.data('xdsoft_datetime').setCurrentTime($(this).val());
						  }
						  
						  datetimepicker.trigger('changedatetime.xdsoft');
						});
				}
				options.dayOfWeekStartPrev = (options.dayOfWeekStart === 0) ? 6 : options.dayOfWeekStart - 1;

				datetimepicker
					.trigger('xchange.xdsoft')
					.trigger('afterOpen.xdsoft');
			};

			datetimepicker
				.data('options', options)
				.on('mousedown.xdsoft', function (event) {
					event.stopPropagation();
					event.preventDefault();
					yearselect.hide();
					monthselect.hide();
					return false;
				});

			//scroll_element = timepicker.find('.xdsoft_time_box');
			timeboxparent.append(timebox);
			timeboxparent.xdsoftScroller();

			datetimepicker.on('afterOpen.xdsoft', function () {
				timeboxparent.xdsoftScroller();
			});

			datetimepicker
				.append(datepicker)
				.append(timepicker);

			if (options.withoutCopyright !== true) {
				datetimepicker
					.append(xdsoft_copyright);
			}

			datepicker
				.append(mounth_picker)
				.append(calendar);

			$(options.parentID)
				.append(datetimepicker);

			XDSoft_datetime = function () {
				var _this = this;
				_this.now = function (norecursion) {
					var d = new Date(),
						date,
						time;

					if (!norecursion && options.defaultDate) {
						date = _this.strToDate(options.defaultDate);
						d.setFullYear(date.getFullYear());
						d.setMonth(date.getMonth());
						d.setDate(date.getDate());
					}

					if (options.yearOffset) {
						d.setFullYear(d.getFullYear() + options.yearOffset);
					}

					if (!norecursion && options.defaultTime) {
						time = _this.strtotime(options.defaultTime);
						d.setHours(time.getHours());
						d.setMinutes(time.getMinutes());
					}

					return d;
				};

				_this.isValidDate = function (d) {
					if (Object.prototype.toString.call(d) !== "[object Date]") {
						return false;
					}
					return !isNaN(d.getTime());
				};

				_this.setCurrentTime = function (dTime) {
					_this.currentTime = (typeof dTime === 'string') ? _this.strToDateTime(dTime) : _this.isValidDate(dTime) ? dTime : _this.now();
					datetimepicker.trigger('xchange.xdsoft');
				};

				_this.empty = function () {
					_this.currentTime = null;
				};

				_this.getCurrentTime = function (dTime) {
					return _this.currentTime;
				};

				_this.nextMonth = function () {

					if (_this.currentTime === undefined || _this.currentTime === null) {
						_this.currentTime = _this.now();
					}

					var month = _this.currentTime.getMonth() + 1,
						year;
					if (month === 12) {
						_this.currentTime.setFullYear(_this.currentTime.getFullYear() + 1);
						month = 0;
					}

					year = _this.currentTime.getFullYear();

					_this.currentTime.setDate(
						Math.min(
							new Date(_this.currentTime.getFullYear(), month + 1, 0).getDate(),
							_this.currentTime.getDate()
						)
					);
					_this.currentTime.setMonth(month);

					if (options.onChangeMonth && $.isFunction(options.onChangeMonth)) {
						options.onChangeMonth.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
					}

					if (year !== _this.currentTime.getFullYear() && $.isFunction(options.onChangeYear)) {
						options.onChangeYear.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
					}

					datetimepicker.trigger('xchange.xdsoft');
					return month;
				};

				_this.prevMonth = function () {

					if (_this.currentTime === undefined || _this.currentTime === null) {
						_this.currentTime = _this.now();
					}

					var month = _this.currentTime.getMonth() - 1;
					if (month === -1) {
						_this.currentTime.setFullYear(_this.currentTime.getFullYear() - 1);
						month = 11;
					}
					_this.currentTime.setDate(
						Math.min(
							new Date(_this.currentTime.getFullYear(), month + 1, 0).getDate(),
							_this.currentTime.getDate()
						)
					);
					_this.currentTime.setMonth(month);
					if (options.onChangeMonth && $.isFunction(options.onChangeMonth)) {
						options.onChangeMonth.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
					}
					datetimepicker.trigger('xchange.xdsoft');
					return month;
				};

				_this.getWeekOfYear = function (datetime) {
					var onejan = new Date(datetime.getFullYear(), 0, 1);
					return Math.ceil((((datetime - onejan) / 86400000) + onejan.getDay() + 1) / 7);
				};

				_this.strToDateTime = function (sDateTime) {
					var tmpDate = [], timeOffset, currentTime;

					if (sDateTime && sDateTime instanceof Date && _this.isValidDate(sDateTime)) {
						return sDateTime;
					}

					tmpDate = /^(\+|\-)(.*)$/.exec(sDateTime);
					if (tmpDate) {
						tmpDate[2] = Date.parseDate(tmpDate[2], options.formatDate);
					}
					if (tmpDate  && tmpDate[2]) {
						timeOffset = tmpDate[2].getTime() - (tmpDate[2].getTimezoneOffset()) * 60000;
						currentTime = new Date((_xdsoft_datetime.now()).getTime() + parseInt(tmpDate[1] + '1', 10) * timeOffset);
					} else {
						currentTime = sDateTime ? Date.parseDate(sDateTime, options.format) : _this.now();
					}

					if (!_this.isValidDate(currentTime)) {
						currentTime = _this.now();
					}

					return currentTime;
				};

				_this.strToDate = function (sDate) {
					if (sDate && sDate instanceof Date && _this.isValidDate(sDate)) {
						return sDate;
					}

					var currentTime = sDate ? Date.parseDate(sDate, options.formatDate) : _this.now(true);
					if (!_this.isValidDate(currentTime)) {
						currentTime = _this.now(true);
					}
					return currentTime;
				};

				_this.strtotime = function (sTime) {
					if (sTime && sTime instanceof Date && _this.isValidDate(sTime)) {
						return sTime;
					}
					var currentTime = sTime ? Date.parseDate(sTime, options.formatTime) : _this.now(true);
					if (!_this.isValidDate(currentTime)) {
						currentTime = _this.now(true);
					}
					return currentTime;
				};

				_this.str = function () {
					return _this.currentTime.dateFormat(options.format);
				};
				_this.currentTime = this.now();
			};

			_xdsoft_datetime = new XDSoft_datetime();

			mounth_picker
				.find('.xdsoft_today_button')
				.on('mousedown.xdsoft', function () {
					datetimepicker.data('changed', true);
					_xdsoft_datetime.setCurrentTime(0);
					datetimepicker.trigger('afterOpen.xdsoft');
				}).on('dblclick.xdsoft', function () {
					input.val(_xdsoft_datetime.str());
					datetimepicker.trigger('close.xdsoft');
				});
			mounth_picker
				.find('.xdsoft_prev,.xdsoft_next')
				.on('mousedown.xdsoft', function () {
					var $this = $(this),
						timer = 0,
						stop = false;

					(function arguments_callee1(v) {
						if ($this.hasClass(options.next)) {
							_xdsoft_datetime.nextMonth();
						} else if ($this.hasClass(options.prev)) {
							_xdsoft_datetime.prevMonth();
						}
						if (options.monthChangeSpinner) {
							if (!stop) {
								timer = setTimeout(arguments_callee1, v || 100);
							}
						}
					}(500));

					$([document.body, window]).on('mouseup.xdsoft', function arguments_callee2() {
						clearTimeout(timer);
						stop = true;
						$([document.body, window]).off('mouseup.xdsoft', arguments_callee2);
					});
				});

			timepicker
				.find('.xdsoft_prev,.xdsoft_next')
				.on('mousedown.xdsoft', function () {
					var $this = $(this),
						timer = 0,
						stop = false,
						period = 110;
					(function arguments_callee4(v) {
						var pheight = timeboxparent[0].clientHeight,
							height = timebox[0].offsetHeight,
							top = Math.abs(parseInt(timebox.css('marginTop'), 10));
						if ($this.hasClass(options.next) && (height - pheight) - options.timeHeightInTimePicker >= top) {
							timebox.css('marginTop', '-' + (top + options.timeHeightInTimePicker) + 'px');
						} else if ($this.hasClass(options.prev) && top - options.timeHeightInTimePicker >= 0) {
							timebox.css('marginTop', '-' + (top - options.timeHeightInTimePicker) + 'px');
						}
						timeboxparent.trigger('scroll_element.xdsoft_scroller', [Math.abs(parseInt(timebox.css('marginTop'), 10) / (height - pheight))]);
						period = (period > 10) ? 10 : period - 10;
						if (!stop) {
							timer = setTimeout(arguments_callee4, v || period);
						}
					}(500));
					$([document.body, window]).on('mouseup.xdsoft', function arguments_callee5() {
						clearTimeout(timer);
						stop = true;
						$([document.body, window])
							.off('mouseup.xdsoft', arguments_callee5);
					});
				});

			xchangeTimer = 0;
			// base handler - generating a calendar and timepicker
			datetimepicker
				.on('xchange.xdsoft', function (event) {
					clearTimeout(xchangeTimer);
					xchangeTimer = setTimeout(function () {

						if (_xdsoft_datetime.currentTime === undefined || _xdsoft_datetime.currentTime === null) {
							_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
						}

						var table =	'',
							start = new Date(_xdsoft_datetime.currentTime.getFullYear(), _xdsoft_datetime.currentTime.getMonth(), 1, 12, 0, 0),
							i = 0,
							j,
							today = _xdsoft_datetime.now(),
							maxDate = false,
							minDate = false,
							d,
							y,
							m,
							w,
							classes = [],
							customDateSettings,
							newRow = true,
							time = '',
							h = '',
							line_time;

						while (start.getDay() !== options.dayOfWeekStart) {
							start.setDate(start.getDate() - 1);
						}

						table += '<table><thead><tr>';

						if (options.weeks) {
							table += '<th></th>';
						}

						for (j = 0; j < 7; j += 1) {
							table += '<th>' + options.i18n[options.lang].dayOfWeek[(j + options.dayOfWeekStart) % 7] + '</th>';
						}

						table += '</tr></thead>';
						table += '<tbody>';

						if (options.maxDate !== false) {
							maxDate = _xdsoft_datetime.strToDate(options.maxDate);
							maxDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate(), 23, 59, 59, 999);
						}

						if (options.minDate !== false) {
							minDate = _xdsoft_datetime.strToDate(options.minDate);
							minDate = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
						}

						while (i < _xdsoft_datetime.currentTime.countDaysInMonth() || start.getDay() !== options.dayOfWeekStart || _xdsoft_datetime.currentTime.getMonth() === start.getMonth()) {
							classes = [];
							i += 1;

							d = start.getDate();
							y = start.getFullYear();
							m = start.getMonth();
							w = _xdsoft_datetime.getWeekOfYear(start);

							classes.push('xdsoft_date');

							if (options.beforeShowDay && $.isFunction(options.beforeShowDay.call)) {
								customDateSettings = options.beforeShowDay.call(datetimepicker, start);
							} else {
								customDateSettings = null;
							}

							if ((maxDate !== false && start > maxDate) || (minDate !== false && start < minDate) || (customDateSettings && customDateSettings[0] === false)) {
								classes.push('xdsoft_disabled');
							} else if (options.disabledDates.indexOf(start.dateFormat(options.formatDate)) !== -1) {
								classes.push('xdsoft_disabled');
							}

							if (customDateSettings && customDateSettings[1] !== "") {
								classes.push(customDateSettings[1]);
							}

							if (_xdsoft_datetime.currentTime.getMonth() !== m) {
								classes.push('xdsoft_other_month');
							}

							if ((options.defaultSelect || datetimepicker.data('changed')) && _xdsoft_datetime.currentTime.dateFormat(options.formatDate) === start.dateFormat(options.formatDate)) {
								classes.push('xdsoft_current');
							}

							if (today.dateFormat(options.formatDate) === start.dateFormat(options.formatDate)) {
								classes.push('xdsoft_today');
							}

							if (start.getDay() === 0 || start.getDay() === 6 || ~options.weekends.indexOf(start.dateFormat(options.formatDate))) {
								classes.push('xdsoft_weekend');
							}

							if (options.beforeShowDay && $.isFunction(options.beforeShowDay)) {
								classes.push(options.beforeShowDay(start));
							}

							if (newRow) {
								table += '<tr>';
								newRow = false;
								if (options.weeks) {
									table += '<th>' + w + '</th>';
								}
							}

							table += '<td data-date="' + d + '" data-month="' + m + '" data-year="' + y + '"' + ' class="xdsoft_date xdsoft_day_of_week' + start.getDay() + ' ' + classes.join(' ') + '">' +
										'<div>' + d + '</div>' +
									'</td>';

							if (start.getDay() === options.dayOfWeekStartPrev) {
								table += '</tr>';
								newRow = true;
							}

							start.setDate(d + 1);
						}
						table += '</tbody></table>';

						calendar.html(table);

						mounth_picker.find('.xdsoft_label span').eq(0).text(options.i18n[options.lang].months[_xdsoft_datetime.currentTime.getMonth()]);
						mounth_picker.find('.xdsoft_label span').eq(1).text(_xdsoft_datetime.currentTime.getFullYear());

						// generate timebox
						time = '';
						h = '';
						m = '';
						line_time = function line_time(h, m) {
							var now = _xdsoft_datetime.now();
							now.setHours(h);
							h = parseInt(now.getHours(), 10);
							now.setMinutes(m);
							m = parseInt(now.getMinutes(), 10);
							var optionDateTime = new Date(_xdsoft_datetime.currentTime);
							optionDateTime.setHours(h);
							optionDateTime.setMinutes(m);
							classes = [];
							if((options.minDateTime !== false && options.minDateTime > optionDateTime) || (options.maxTime !== false && _xdsoft_datetime.strtotime(options.maxTime).getTime() < now.getTime()) || (options.minTime !== false && _xdsoft_datetime.strtotime(options.minTime).getTime() > now.getTime())) {
								classes.push('xdsoft_disabled');
							}
							if ((options.initTime || options.defaultSelect || datetimepicker.data('changed')) && parseInt(_xdsoft_datetime.currentTime.getHours(), 10) === parseInt(h, 10) && (options.step > 59 || Math[options.roundTime](_xdsoft_datetime.currentTime.getMinutes() / options.step) * options.step === parseInt(m, 10))) {
								if (options.defaultSelect || datetimepicker.data('changed')) {
									classes.push('xdsoft_current');
								} else if (options.initTime) {
									classes.push('xdsoft_init_time');
								}
							}
							if (parseInt(today.getHours(), 10) === parseInt(h, 10) && parseInt(today.getMinutes(), 10) === parseInt(m, 10)) {
								classes.push('xdsoft_today');
							}
							time += '<div class="xdsoft_time ' + classes.join(' ') + '" data-hour="' + h + '" data-minute="' + m + '">' + now.dateFormat(options.formatTime) + '</div>';
						};

						if (!options.allowTimes || !$.isArray(options.allowTimes) || !options.allowTimes.length) {
							for (i = 0, j = 0; i < (options.hours12 ? 12 : 24); i += 1) {
								for (j = 0; j < 60; j += options.step) {
									h = (i < 10 ? '0' : '') + i;
									m = (j < 10 ? '0' : '') + j;
									line_time(h, m);
								}
							}
						} else {
							for (i = 0; i < options.allowTimes.length; i += 1) {
								h = _xdsoft_datetime.strtotime(options.allowTimes[i]).getHours();
								m = _xdsoft_datetime.strtotime(options.allowTimes[i]).getMinutes();
								line_time(h, m);
							}
						}

						timebox.html(time);

						opt = '';
						i = 0;

						for (i = parseInt(options.yearStart, 10) + options.yearOffset; i <= parseInt(options.yearEnd, 10) + options.yearOffset; i += 1) {
							opt += '<div class="xdsoft_option ' + (_xdsoft_datetime.currentTime.getFullYear() === i ? 'xdsoft_current' : '') + '" data-value="' + i + '">' + i + '</div>';
						}
						yearselect.children().eq(0)
												.html(opt);

						for (i = 0, opt = ''; i <= 11; i += 1) {
							opt += '<div class="xdsoft_option ' + (_xdsoft_datetime.currentTime.getMonth() === i ? 'xdsoft_current' : '') + '" data-value="' + i + '">' + options.i18n[options.lang].months[i] + '</div>';
						}
						monthselect.children().eq(0).html(opt);
						$(datetimepicker)
							.trigger('generate.xdsoft');
					}, 10);
					event.stopPropagation();
				})
				.on('afterOpen.xdsoft', function () {
					if (options.timepicker) {
						var classType, pheight, height, top;
						if (timebox.find('.xdsoft_current').length) {
							classType = '.xdsoft_current';
						} else if (timebox.find('.xdsoft_init_time').length) {
							classType = '.xdsoft_init_time';
						}
						if (classType) {
							pheight = timeboxparent[0].clientHeight;
							height = timebox[0].offsetHeight;
							top = timebox.find(classType).index() * options.timeHeightInTimePicker + 1;
							if ((height - pheight) < top) {
								top = height - pheight;
							}
							timeboxparent.trigger('scroll_element.xdsoft_scroller', [parseInt(top, 10) / (height - pheight)]);
						} else {
							timeboxparent.trigger('scroll_element.xdsoft_scroller', [0]);
						}
					}
				});

			timerclick = 0;
			calendar
				.on('click.xdsoft', 'td', function (xdevent) {
					xdevent.stopPropagation();  // Prevents closing of Pop-ups, Modals and Flyouts in Bootstrap
					timerclick += 1;
					var $this = $(this),
						currentTime = _xdsoft_datetime.currentTime;

					if (currentTime === undefined || currentTime === null) {
						_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
						currentTime = _xdsoft_datetime.currentTime;
					}

					if ($this.hasClass('xdsoft_disabled')) {
						return false;
					}

					currentTime.setDate(1);
					currentTime.setFullYear($this.data('year'));
					currentTime.setMonth($this.data('month'));
					currentTime.setDate($this.data('date'));

					datetimepicker.trigger('select.xdsoft', [currentTime]);

					input.val(_xdsoft_datetime.str());
					if ((timerclick > 1 || (options.closeOnDateSelect === true || (options.closeOnDateSelect === 0 && !options.timepicker))) && !options.inline) {
						datetimepicker.trigger('close.xdsoft');
					}

					if (options.onSelectDate &&	$.isFunction(options.onSelectDate)) {
						options.onSelectDate.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), xdevent);
					}

					datetimepicker.data('changed', true);
					datetimepicker.trigger('xchange.xdsoft');
					datetimepicker.trigger('changedatetime.xdsoft');
					setTimeout(function () {
						timerclick = 0;
					}, 200);
				});

			timebox
				.on('click.xdsoft', 'div', function (xdevent) {
					xdevent.stopPropagation();
					var $this = $(this),
						currentTime = _xdsoft_datetime.currentTime;

					if (currentTime === undefined || currentTime === null) {
						_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
						currentTime = _xdsoft_datetime.currentTime;
					}

					if ($this.hasClass('xdsoft_disabled')) {
						return false;
					}
					currentTime.setHours($this.data('hour'));
					currentTime.setMinutes($this.data('minute'));
					datetimepicker.trigger('select.xdsoft', [currentTime]);

					datetimepicker.data('input').val(_xdsoft_datetime.str());
					if (!options.inline) {
						datetimepicker.trigger('close.xdsoft');
					}

					if (options.onSelectTime && $.isFunction(options.onSelectTime)) {
						options.onSelectTime.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), xdevent);
					}
					datetimepicker.data('changed', true);
					datetimepicker.trigger('xchange.xdsoft');
					datetimepicker.trigger('changedatetime.xdsoft');
				});


			datepicker
				.on('mousewheel.xdsoft', function (event) {
					if (!options.scrollMonth) {
						return true;
					}
					if (event.deltaY < 0) {
						_xdsoft_datetime.nextMonth();
					} else {
						_xdsoft_datetime.prevMonth();
					}
					return false;
				});

			input
				.on('mousewheel.xdsoft', function (event) {
					if (!options.scrollInput) {
						return true;
					}
					if (!options.datepicker && options.timepicker) {
						current_time_index = timebox.find('.xdsoft_current').length ? timebox.find('.xdsoft_current').eq(0).index() : 0;
						if (current_time_index + event.deltaY >= 0 && current_time_index + event.deltaY < timebox.children().length) {
							current_time_index += event.deltaY;
						}
						if (timebox.children().eq(current_time_index).length) {
							timebox.children().eq(current_time_index).trigger('mousedown');
						}
						return false;
					}
					if (options.datepicker && !options.timepicker) {
						datepicker.trigger(event, [event.deltaY, event.deltaX, event.deltaY]);
						if (input.val) {
							input.val(_xdsoft_datetime.str());
						}
						datetimepicker.trigger('changedatetime.xdsoft');
						return false;
					}
				});

			datetimepicker
				.on('changedatetime.xdsoft', function (event) {
					if (options.onChangeDateTime && $.isFunction(options.onChangeDateTime)) {
						var $input = datetimepicker.data('input');
						options.onChangeDateTime.call(datetimepicker, _xdsoft_datetime.currentTime, $input, event);
						delete options.value;
						$input.trigger('change');
					}
				})
				.on('generate.xdsoft', function () {
					if (options.onGenerate && $.isFunction(options.onGenerate)) {
						options.onGenerate.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
					}
					if (triggerAfterOpen) {
						datetimepicker.trigger('afterOpen.xdsoft');
						triggerAfterOpen = false;
					}
				})
				.on('click.xdsoft', function (xdevent) {
					xdevent.stopPropagation();
				});

			current_time_index = 0;

			setPos = function () {
				var offset = datetimepicker.data('input').offset(), top = offset.top + datetimepicker.data('input')[0].offsetHeight - 1, left = offset.left, position = "absolute";
				if (options.fixed) {
					top -= $(window).scrollTop();
					left -= $(window).scrollLeft();
					position = "fixed";
				} else {
					if (top + datetimepicker[0].offsetHeight > $(window).height() + $(window).scrollTop()) {
						top = offset.top - datetimepicker[0].offsetHeight + 1;
					}
					if (top < 0) {
						top = 0;
					}
					if (left + datetimepicker[0].offsetWidth > $(window).width()) {
						left = $(window).width() - datetimepicker[0].offsetWidth;
					}
				}
				datetimepicker.css({
					left: left,
					top: top,
					position: position
				});
			};
			datetimepicker
				.on('open.xdsoft', function (event) {
					var onShow = true;
					if (options.onShow && $.isFunction(options.onShow)) {
						onShow = options.onShow.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), event);
					}
					if (onShow !== false) {
						datetimepicker.show();
						setPos();
						$(window)
							.off('resize.xdsoft', setPos)
							.on('resize.xdsoft', setPos);

						if (options.closeOnWithoutClick) {
							$([document.body, window]).on('mousedown.xdsoft', function arguments_callee6() {
								datetimepicker.trigger('close.xdsoft');
								$([document.body, window]).off('mousedown.xdsoft', arguments_callee6);
							});
						}
					}
				})
				.on('close.xdsoft', function (event) {
					var onClose = true;
					mounth_picker
						.find('.xdsoft_month,.xdsoft_year')
							.find('.xdsoft_select')
								.hide();
					if (options.onClose && $.isFunction(options.onClose)) {
						onClose = options.onClose.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), event);
					}
					if (onClose !== false && !options.opened && !options.inline) {
						datetimepicker.hide();
					}
					event.stopPropagation();
				})
				.on('toggle.xdsoft', function (event) {
					if (datetimepicker.is(':visible')) {
						datetimepicker.trigger('close.xdsoft');
					} else {
						datetimepicker.trigger('open.xdsoft');
					}
				})
				.data('input', input);

			timer = 0;
			timer1 = 0;

			datetimepicker.data('xdsoft_datetime', _xdsoft_datetime);
			datetimepicker.setOptions(options);

			function getCurrentValue() {

				var ct = false, time;

				if (options.startDate) {
					ct = _xdsoft_datetime.strToDate(options.startDate);
				} else {
					ct = options.value || ((input && input.val && input.val()) ? input.val() : '');
					if (ct) {
						ct = _xdsoft_datetime.strToDateTime(ct);
					} else if (options.defaultDate) {
						ct = _xdsoft_datetime.strToDate(options.defaultDate);
						if (options.defaultTime) {
							time = _xdsoft_datetime.strtotime(options.defaultTime);
							ct.setHours(time.getHours());
							ct.setMinutes(time.getMinutes());
						}
					}
				}

				if (ct && _xdsoft_datetime.isValidDate(ct)) {
					datetimepicker.data('changed', true);
				} else {
					ct = '';
				}

				return ct || 0;
			}

			_xdsoft_datetime.setCurrentTime(getCurrentValue());

			input
				.data('xdsoft_datetimepicker', datetimepicker)
				.on('open.xdsoft focusin.xdsoft mousedown.xdsoft', function (event) {
					if (input.is(':disabled') || (input.data('xdsoft_datetimepicker').is(':visible') && options.closeOnInputClick)) {
						return;
					}
					clearTimeout(timer);
					timer = setTimeout(function () {
						if (input.is(':disabled')) {
							return;
						}

						triggerAfterOpen = true;
						_xdsoft_datetime.setCurrentTime(getCurrentValue());

						datetimepicker.trigger('open.xdsoft');
					}, 100);
				})
				.on('keydown.xdsoft', function (event) {
					var val = this.value, elementSelector,
						key = event.which;
					if ([ENTER].indexOf(key) !== -1 && options.enterLikeTab) {
						elementSelector = $("input:visible,textarea:visible");
						datetimepicker.trigger('close.xdsoft');
						elementSelector.eq(elementSelector.index(this) + 1).focus();
						return false;
					}
					if ([TAB].indexOf(key) !== -1) {
						datetimepicker.trigger('close.xdsoft');
						return true;
					}
				});
		};
		destroyDateTimePicker = function (input) {
			var datetimepicker = input.data('xdsoft_datetimepicker');
			if (datetimepicker) {
				datetimepicker.data('xdsoft_datetime', null);
				datetimepicker.remove();
				input
					.data('xdsoft_datetimepicker', null)
					.off('.xdsoft');
				$(window).off('resize.xdsoft');
				$([window, document.body]).off('mousedown.xdsoft');
				if (input.unmousewheel) {
					input.unmousewheel();
				}
			}
		};
		$(document)
			.off('keydown.xdsoftctrl keyup.xdsoftctrl')
			.on('keydown.xdsoftctrl', function (e) {
				if (e.keyCode === CTRLKEY) {
					ctrlDown = true;
				}
			})
			.on('keyup.xdsoftctrl', function (e) {
				if (e.keyCode === CTRLKEY) {
					ctrlDown = false;
				}
			});
		return this.each(function () {
			var datetimepicker = $(this).data('xdsoft_datetimepicker');
			if (datetimepicker) {
				if ($.type(opt) === 'string') {
					switch (opt) {
					case 'show':
						$(this).select().focus();
						datetimepicker.trigger('open.xdsoft');
						break;
					case 'hide':
						datetimepicker.trigger('close.xdsoft');
						break;
					case 'toggle':
						datetimepicker.trigger('toggle.xdsoft');
						break;
					case 'destroy':
						destroyDateTimePicker($(this));
						break;
					case 'reset':
						this.value = this.defaultValue;
						if (!this.value || !datetimepicker.data('xdsoft_datetime').isValidDate(Date.parseDate(this.value, options.format))) {
							datetimepicker.data('changed', false);
						}
						datetimepicker.data('xdsoft_datetime').setCurrentTime(this.value);
						break;
					}
				} else {
					datetimepicker
						.setOptions(opt);
				}
				return 0;
			}
			if ($.type(opt) !== 'string') {
				if (!options.lazyInit || options.open || options.inline) {
					createDateTimePicker($(this));
				} else {
					lazyInit($(this));
				}
			}
		});
	};
	$.fn.datetimepicker.defaults = default_options;
}(jQuery));
(function () {

/*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 3.1.12
 *
 * Requires: jQuery 1.2.2+
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});

// Parse and Format Library
//http://www.xaprb.com/blog/2005/12/12/javascript-closures-for-runtime-efficiency/
/*
 * Copyright (C) 2004 Baron Schwartz <baron at sequent dot org>
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, version 2.1.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for more
 * details.
 */
Date.parseFunctions={count:0};Date.parseRegexes=[];Date.formatFunctions={count:0};Date.prototype.dateFormat=function(b){if(b=="unixtime"){return parseInt(this.getTime()/1000);}if(Date.formatFunctions[b]==null){Date.createNewFormat(b);}var a=Date.formatFunctions[b];return this[a]();};Date.createNewFormat=function(format){var funcName="format"+Date.formatFunctions.count++;Date.formatFunctions[format]=funcName;var codePrefix="Date.prototype."+funcName+" = function() {return ";var code="";var special=false;var ch="";for(var i=0;i<format.length;++i){ch=format.charAt(i);if(!special&&ch=="\\"){special=true;}else{if(special){special=false;code+="'"+String.escape(ch)+"' + ";}else{code+=Date.getFormatCode(ch);}}}if(code.length==0){code="\"\"";}else{code=code.substring(0,code.length-3);}eval(codePrefix+code+";}");};Date.getFormatCode=function(a){switch(a){case"d":return"String.leftPad(this.getDate(), 2, '0') + ";case"D":return"Date.dayNames[this.getDay()].substring(0, 3) + ";case"j":return"this.getDate() + ";case"l":return"Date.dayNames[this.getDay()] + ";case"S":return"this.getSuffix() + ";case"w":return"this.getDay() + ";case"z":return"this.getDayOfYear() + ";case"W":return"this.getWeekOfYear() + ";case"F":return"Date.monthNames[this.getMonth()] + ";case"m":return"String.leftPad(this.getMonth() + 1, 2, '0') + ";case"M":return"Date.monthNames[this.getMonth()].substring(0, 3) + ";case"n":return"(this.getMonth() + 1) + ";case"t":return"this.getDaysInMonth() + ";case"L":return"(this.isLeapYear() ? 1 : 0) + ";case"Y":return"this.getFullYear() + ";case"y":return"('' + this.getFullYear()).substring(2, 4) + ";case"a":return"(this.getHours() < 12 ? 'am' : 'pm') + ";case"A":return"(this.getHours() < 12 ? 'AM' : 'PM') + ";case"g":return"((this.getHours() %12) ? this.getHours() % 12 : 12) + ";case"G":return"this.getHours() + ";case"h":return"String.leftPad((this.getHours() %12) ? this.getHours() % 12 : 12, 2, '0') + ";case"H":return"String.leftPad(this.getHours(), 2, '0') + ";case"i":return"String.leftPad(this.getMinutes(), 2, '0') + ";case"s":return"String.leftPad(this.getSeconds(), 2, '0') + ";case"O":return"this.getGMTOffset() + ";case"T":return"this.getTimezone() + ";case"Z":return"(this.getTimezoneOffset() * -60) + ";default:return"'"+String.escape(a)+"' + ";}};Date.parseDate=function(a,c){if(c=="unixtime"){return new Date(!isNaN(parseInt(a))?parseInt(a)*1000:0);}if(Date.parseFunctions[c]==null){Date.createParser(c);}var b=Date.parseFunctions[c];return Date[b](a);};Date.createParser=function(format){var funcName="parse"+Date.parseFunctions.count++;var regexNum=Date.parseRegexes.length;var currentGroup=1;Date.parseFunctions[format]=funcName;var code="Date."+funcName+" = function(input) {\nvar y = -1, m = -1, d = -1, h = -1, i = -1, s = -1, z = -1;\nvar d = new Date();\ny = d.getFullYear();\nm = d.getMonth();\nd = d.getDate();\nvar results = input.match(Date.parseRegexes["+regexNum+"]);\nif (results && results.length > 0) {";var regex="";var special=false;var ch="";for(var i=0;i<format.length;++i){ch=format.charAt(i);if(!special&&ch=="\\"){special=true;}else{if(special){special=false;regex+=String.escape(ch);}else{obj=Date.formatCodeToRegex(ch,currentGroup);currentGroup+=obj.g;regex+=obj.s;if(obj.g&&obj.c){code+=obj.c;}}}}code+="if (y > 0 && z > 0){\nvar doyDate = new Date(y,0);\ndoyDate.setDate(z);\nm = doyDate.getMonth();\nd = doyDate.getDate();\n}";code+="if (y > 0 && m >= 0 && d > 0 && h >= 0 && i >= 0 && s >= 0)\n{return new Date(y, m, d, h, i, s);}\nelse if (y > 0 && m >= 0 && d > 0 && h >= 0 && i >= 0)\n{return new Date(y, m, d, h, i);}\nelse if (y > 0 && m >= 0 && d > 0 && h >= 0)\n{return new Date(y, m, d, h);}\nelse if (y > 0 && m >= 0 && d > 0)\n{return new Date(y, m, d);}\nelse if (y > 0 && m >= 0)\n{return new Date(y, m);}\nelse if (y > 0)\n{return new Date(y);}\n}return null;}";Date.parseRegexes[regexNum]=new RegExp("^"+regex+"$");eval(code);};Date.formatCodeToRegex=function(b,a){switch(b){case"D":return{g:0,c:null,s:"(?:Sun|Mon|Tue|Wed|Thu|Fri|Sat)"};case"j":case"d":return{g:1,c:"d = parseInt(results["+a+"], 10);\n",s:"(\\d{1,2})"};case"l":return{g:0,c:null,s:"(?:"+Date.dayNames.join("|")+")"};case"S":return{g:0,c:null,s:"(?:st|nd|rd|th)"};case"w":return{g:0,c:null,s:"\\d"};case"z":return{g:1,c:"z = parseInt(results["+a+"], 10);\n",s:"(\\d{1,3})"};case"W":return{g:0,c:null,s:"(?:\\d{2})"};case"F":return{g:1,c:"m = parseInt(Date.monthNumbers[results["+a+"].substring(0, 3)], 10);\n",s:"("+Date.monthNames.join("|")+")"};case"M":return{g:1,c:"m = parseInt(Date.monthNumbers[results["+a+"]], 10);\n",s:"(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)"};case"n":case"m":return{g:1,c:"m = parseInt(results["+a+"], 10) - 1;\n",s:"(\\d{1,2})"};case"t":return{g:0,c:null,s:"\\d{1,2}"};case"L":return{g:0,c:null,s:"(?:1|0)"};case"Y":return{g:1,c:"y = parseInt(results["+a+"], 10);\n",s:"(\\d{4})"};case"y":return{g:1,c:"var ty = parseInt(results["+a+"], 10);\ny = ty > Date.y2kYear ? 1900 + ty : 2000 + ty;\n",s:"(\\d{1,2})"};case"a":return{g:1,c:"if (results["+a+"] == 'am') {\nif (h == 12) { h = 0; }\n} else { if (h < 12) { h += 12; }}",s:"(am|pm)"};case"A":return{g:1,c:"if (results["+a+"] == 'AM') {\nif (h == 12) { h = 0; }\n} else { if (h < 12) { h += 12; }}",s:"(AM|PM)"};case"g":case"G":case"h":case"H":return{g:1,c:"h = parseInt(results["+a+"], 10);\n",s:"(\\d{1,2})"};case"i":return{g:1,c:"i = parseInt(results["+a+"], 10);\n",s:"(\\d{2})"};case"s":return{g:1,c:"s = parseInt(results["+a+"], 10);\n",s:"(\\d{2})"};case"O":return{g:0,c:null,s:"[+-]\\d{4}"};case"T":return{g:0,c:null,s:"[A-Z]{3}"};case"Z":return{g:0,c:null,s:"[+-]\\d{1,5}"};default:return{g:0,c:null,s:String.escape(b)};}};Date.prototype.getTimezone=function(){return this.toString().replace(/^.*? ([A-Z]{3}) [0-9]{4}.*$/,"$1").replace(/^.*?\(([A-Z])[a-z]+ ([A-Z])[a-z]+ ([A-Z])[a-z]+\)$/,"$1$2$3");};Date.prototype.getGMTOffset=function(){return(this.getTimezoneOffset()>0?"-":"+")+String.leftPad(Math.floor(Math.abs(this.getTimezoneOffset())/60),2,"0")+String.leftPad(Math.abs(this.getTimezoneOffset())%60,2,"0");};Date.prototype.getDayOfYear=function(){var a=0;Date.daysInMonth[1]=this.isLeapYear()?29:28;for(var b=0;b<this.getMonth();++b){a+=Date.daysInMonth[b];}return a+this.getDate();};Date.prototype.getWeekOfYear=function(){var b=this.getDayOfYear()+(4-this.getDay());var a=new Date(this.getFullYear(),0,1);var c=(7-a.getDay()+4);return String.leftPad(Math.ceil((b-c)/7)+1,2,"0");};Date.prototype.isLeapYear=function(){var a=this.getFullYear();return((a&3)==0&&(a%100||(a%400==0&&a)));};Date.prototype.getFirstDayOfMonth=function(){var a=(this.getDay()-(this.getDate()-1))%7;return(a<0)?(a+7):a;};Date.prototype.getLastDayOfMonth=function(){var a=(this.getDay()+(Date.daysInMonth[this.getMonth()]-this.getDate()))%7;return(a<0)?(a+7):a;};Date.prototype.getDaysInMonth=function(){Date.daysInMonth[1]=this.isLeapYear()?29:28;return Date.daysInMonth[this.getMonth()];};Date.prototype.getSuffix=function(){switch(this.getDate()){case 1:case 21:case 31:return"st";case 2:case 22:return"nd";case 3:case 23:return"rd";default:return"th";}};String.escape=function(a){return a.replace(/('|\\)/g,"\\$1");};String.leftPad=function(d,b,c){var a=new String(d);if(c==null){c=" ";}while(a.length<b){a=c+a;}return a;};Date.daysInMonth=[31,28,31,30,31,30,31,31,30,31,30,31];Date.monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];Date.dayNames=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];Date.y2kYear=50;Date.monthNumbers={Jan:0,Feb:1,Mar:2,Apr:3,May:4,Jun:5,Jul:6,Aug:7,Sep:8,Oct:9,Nov:10,Dec:11};Date.patterns={ISO8601LongPattern:"Y-m-d H:i:s",ISO8601ShortPattern:"Y-m-d",ShortDatePattern:"n/j/Y",LongDatePattern:"l, F d, Y",FullDateTimePattern:"l, F d, Y g:i:s A",MonthDayPattern:"F d",ShortTimePattern:"g:i A",LongTimePattern:"g:i:s A",SortableDateTimePattern:"Y-m-d\\TH:i:s",UniversalSortableDateTimePattern:"Y-m-d H:i:sO",YearMonthPattern:"F, Y"};
}());
/* global fcom, langLbl */
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
/* global fcom, langLbl */
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
})();/*!
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
})();(function(c){var b,a;a=typeof window!=="undefined"&&window!==null?window:global;a.BarRating=b=(function(){function d(){this.show=function(){var g=c(this.elem),j,f,h=this.options,e,i;if(!g.data("barrating")){if(h.initialRating){i=c('option[value="'+h.initialRating+'"]',g)}else{i=c("option:selected",g)}g.data("barrating",{currentRatingValue:i.val(),currentRatingText:i.text(),originalRatingValue:i.val(),originalRatingText:i.text()});j=c("<div />",{"class":"br-widget"}).insertAfter(g);g.find("option").each(function(){var n,m,l,k;n=c(this).val();if(n){m=c(this).text();l=c("<a />",{href:"#","data-rating-value":n,"data-rating-text":m});k=c("<span />",{text:(h.showValues)?m:""});j.append(l.append(k))}});if(h.showSelectedRating){j.append(c("<div />",{text:"","class":"br-current-rating"}))}g.data("barrating").deselectable=(!g.find("option:first").val())?true:false;if(h.reverse){e="nextAll"}else{e="prevAll"}if(h.reverse){j.addClass("br-reverse")}if(h.readonly){j.addClass("br-readonly")}j.on("ratingchange",function(k,l,m){l=l?l:g.data("barrating").currentRatingValue;m=m?m:g.data("barrating").currentRatingText;g.find('option[value="'+l+'"]').prop("selected",true);if(h.showSelectedRating){c(this).find(".br-current-rating").text(m)}}).trigger("ratingchange");j.on("updaterating",function(k){c(this).find('a[data-rating-value="'+g.data("barrating").currentRatingValue+'"]').addClass("br-selected br-current")[e]().addClass("br-selected")}).trigger("updaterating");f=j.find("a");f.on("touchstart",function(k){k.preventDefault();k.stopPropagation();c(this).click()});if(h.readonly){f.on("click",function(k){k.preventDefault()})}if(!h.readonly){f.on("click",function(k){var m=c(this),l,n;k.preventDefault();f.removeClass("br-active br-selected");m.addClass("br-selected")[e]().addClass("br-selected");l=m.attr("data-rating-value");n=m.attr("data-rating-text");if(m.hasClass("br-current")&&g.data("barrating").deselectable){m.removeClass("br-selected br-current")[e]().removeClass("br-selected br-current");l="",n=""}else{f.removeClass("br-current");m.addClass("br-current")}g.data("barrating").currentRatingValue=l;g.data("barrating").currentRatingText=n;j.trigger("ratingchange");h.onSelect.call(this,g.data("barrating").currentRatingValue,g.data("barrating").currentRatingText);return false});f.on({mouseenter:function(){var k=c(this);f.removeClass("br-active").removeClass("br-selected");k.addClass("br-active")[e]().addClass("br-active");j.trigger("ratingchange",[k.attr("data-rating-value"),k.attr("data-rating-text")])}});j.on({mouseleave:function(){f.removeClass("br-active");j.trigger("ratingchange").trigger("updaterating")}})}g.hide()}};this.clear=function(){var e=c(this.elem);var f=e.next(".br-widget");if(f&&e.data("barrating")){f.find("a").removeClass("br-selected br-current");e.data("barrating").currentRatingValue=e.data("barrating").originalRatingValue;e.data("barrating").currentRatingText=e.data("barrating").originalRatingText;f.trigger("ratingchange").trigger("updaterating");this.options.onClear.call(this,e.data("barrating").currentRatingValue,e.data("barrating").currentRatingText)}};this.destroy=function(){var f=c(this.elem);var h=f.next(".br-widget");if(h&&f.data("barrating")){var e=f.data("barrating").currentRatingValue;var g=f.data("barrating").currentRatingText;f.removeData("barrating");h.off().remove();f.show();this.options.onDestroy.call(this,e,g)}}}d.prototype.init=function(f,g){var e;e=this;e.elem=g;return e.options=c.extend({},c.fn.barrating.defaults,f)};return d})();c.fn.barrating=function(e,d){return this.each(function(){var f=new b();if(!c(this).is("select")){c.error("Sorry, this plugin only works with select fields.")}if(f.hasOwnProperty(e)){f.init(d,this);return f[e]()}else{if(typeof e==="object"||!e){d=e;f.init(d,this);return f.show()}else{c.error("Method "+e+" does not exist on jQuery.barrating")}}})};return c.fn.barrating.defaults={initialRating:null,showValues:false,showSelectedRating:true,reverse:false,readonly:false,onSelect:function(d,e){},onClear:function(d,e){},onDestroy:function(d,e){}}})(jQuery);!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):e.moment=t()}(this,function(){"use strict";var e,t;function n(){return e.apply(null,arguments)}function s(e){return e instanceof Array||"[object Array]"===Object.prototype.toString.call(e)}function i(e){return null!=e&&"[object Object]"===Object.prototype.toString.call(e)}function r(e){return void 0===e}function a(e){return"number"==typeof e||"[object Number]"===Object.prototype.toString.call(e)}function o(e){return e instanceof Date||"[object Date]"===Object.prototype.toString.call(e)}function u(e,t){var n,s=[];for(n=0;n<e.length;++n)s.push(t(e[n],n));return s}function l(e,t){return Object.prototype.hasOwnProperty.call(e,t)}function d(e,t){for(var n in t)l(t,n)&&(e[n]=t[n]);return l(t,"toString")&&(e.toString=t.toString),l(t,"valueOf")&&(e.valueOf=t.valueOf),e}function h(e,t,n,s){return Ot(e,t,n,s,!0).utc()}function c(e){return null==e._pf&&(e._pf={empty:!1,unusedTokens:[],unusedInput:[],overflow:-2,charsLeftOver:0,nullInput:!1,invalidMonth:null,invalidFormat:!1,userInvalidated:!1,iso:!1,parsedDateParts:[],meridiem:null,rfc2822:!1,weekdayMismatch:!1}),e._pf}function f(e){if(null==e._isValid){var n=c(e),s=t.call(n.parsedDateParts,function(e){return null!=e}),i=!isNaN(e._d.getTime())&&n.overflow<0&&!n.empty&&!n.invalidMonth&&!n.invalidWeekday&&!n.weekdayMismatch&&!n.nullInput&&!n.invalidFormat&&!n.userInvalidated&&(!n.meridiem||n.meridiem&&s);if(e._strict&&(i=i&&0===n.charsLeftOver&&0===n.unusedTokens.length&&void 0===n.bigHour),null!=Object.isFrozen&&Object.isFrozen(e))return i;e._isValid=i}return e._isValid}function m(e){var t=h(NaN);return null!=e?d(c(t),e):c(t).userInvalidated=!0,t}t=Array.prototype.some?Array.prototype.some:function(e){for(var t=Object(this),n=t.length>>>0,s=0;s<n;s++)if(s in t&&e.call(this,t[s],s,t))return!0;return!1};var _=n.momentProperties=[];function y(e,t){var n,s,i;if(r(t._isAMomentObject)||(e._isAMomentObject=t._isAMomentObject),r(t._i)||(e._i=t._i),r(t._f)||(e._f=t._f),r(t._l)||(e._l=t._l),r(t._strict)||(e._strict=t._strict),r(t._tzm)||(e._tzm=t._tzm),r(t._isUTC)||(e._isUTC=t._isUTC),r(t._offset)||(e._offset=t._offset),r(t._pf)||(e._pf=c(t)),r(t._locale)||(e._locale=t._locale),_.length>0)for(n=0;n<_.length;n++)r(i=t[s=_[n]])||(e[s]=i);return e}var g=!1;function p(e){y(this,e),this._d=new Date(null!=e._d?e._d.getTime():NaN),this.isValid()||(this._d=new Date(NaN)),!1===g&&(g=!0,n.updateOffset(this),g=!1)}function v(e){return e instanceof p||null!=e&&null!=e._isAMomentObject}function w(e){return e<0?Math.ceil(e)||0:Math.floor(e)}function M(e){var t=+e,n=0;return 0!==t&&isFinite(t)&&(n=w(t)),n}function S(e,t,n){var s,i=Math.min(e.length,t.length),r=Math.abs(e.length-t.length),a=0;for(s=0;s<i;s++)(n&&e[s]!==t[s]||!n&&M(e[s])!==M(t[s]))&&a++;return a+r}function D(e){!1===n.suppressDeprecationWarnings&&"undefined"!=typeof console&&console.warn&&console.warn("Deprecation warning: "+e)}function k(e,t){var s=!0;return d(function(){if(null!=n.deprecationHandler&&n.deprecationHandler(null,e),s){for(var i,r=[],a=0;a<arguments.length;a++){if(i="","object"==typeof arguments[a]){for(var o in i+="\n["+a+"] ",arguments[0])i+=o+": "+arguments[0][o]+", ";i=i.slice(0,-2)}else i=arguments[a];r.push(i)}D(e+"\nArguments: "+Array.prototype.slice.call(r).join("")+"\n"+(new Error).stack),s=!1}return t.apply(this,arguments)},t)}var Y,O={};function T(e,t){null!=n.deprecationHandler&&n.deprecationHandler(e,t),O[e]||(D(t),O[e]=!0)}function x(e){return e instanceof Function||"[object Function]"===Object.prototype.toString.call(e)}function b(e,t){var n,s=d({},e);for(n in t)l(t,n)&&(i(e[n])&&i(t[n])?(s[n]={},d(s[n],e[n]),d(s[n],t[n])):null!=t[n]?s[n]=t[n]:delete s[n]);for(n in e)l(e,n)&&!l(t,n)&&i(e[n])&&(s[n]=d({},s[n]));return s}function P(e){null!=e&&this.set(e)}n.suppressDeprecationWarnings=!1,n.deprecationHandler=null,Y=Object.keys?Object.keys:function(e){var t,n=[];for(t in e)l(e,t)&&n.push(t);return n};var W={};function H(e,t){var n=e.toLowerCase();W[n]=W[n+"s"]=W[t]=e}function R(e){return"string"==typeof e?W[e]||W[e.toLowerCase()]:void 0}function C(e){var t,n,s={};for(n in e)l(e,n)&&(t=R(n))&&(s[t]=e[n]);return s}var F={};function L(e,t){F[e]=t}function U(e,t,n){var s=""+Math.abs(e),i=t-s.length;return(e>=0?n?"+":"":"-")+Math.pow(10,Math.max(0,i)).toString().substr(1)+s}var N=/(\[[^\[]*\])|(\\)?([Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|YYYYYY|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?|.)/g,G=/(\[[^\[]*\])|(\\)?(LTS|LT|LL?L?L?|l{1,4})/g,V={},E={};function I(e,t,n,s){var i=s;"string"==typeof s&&(i=function(){return this[s]()}),e&&(E[e]=i),t&&(E[t[0]]=function(){return U(i.apply(this,arguments),t[1],t[2])}),n&&(E[n]=function(){return this.localeData().ordinal(i.apply(this,arguments),e)})}function A(e,t){return e.isValid()?(t=j(t,e.localeData()),V[t]=V[t]||function(e){var t,n,s,i=e.match(N);for(t=0,n=i.length;t<n;t++)E[i[t]]?i[t]=E[i[t]]:i[t]=(s=i[t]).match(/\[[\s\S]/)?s.replace(/^\[|\]$/g,""):s.replace(/\\/g,"");return function(t){var s,r="";for(s=0;s<n;s++)r+=x(i[s])?i[s].call(t,e):i[s];return r}}(t),V[t](e)):e.localeData().invalidDate()}function j(e,t){var n=5;function s(e){return t.longDateFormat(e)||e}for(G.lastIndex=0;n>=0&&G.test(e);)e=e.replace(G,s),G.lastIndex=0,n-=1;return e}var Z=/\d/,z=/\d\d/,$=/\d{3}/,q=/\d{4}/,J=/[+-]?\d{6}/,B=/\d\d?/,Q=/\d\d\d\d?/,X=/\d\d\d\d\d\d?/,K=/\d{1,3}/,ee=/\d{1,4}/,te=/[+-]?\d{1,6}/,ne=/\d+/,se=/[+-]?\d+/,ie=/Z|[+-]\d\d:?\d\d/gi,re=/Z|[+-]\d\d(?::?\d\d)?/gi,ae=/[0-9]{0,256}['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFF07\uFF10-\uFFEF]{1,256}|[\u0600-\u06FF\/]{1,256}(\s*?[\u0600-\u06FF]{1,256}){1,2}/i,oe={};function ue(e,t,n){oe[e]=x(t)?t:function(e,s){return e&&n?n:t}}function le(e,t){return l(oe,e)?oe[e](t._strict,t._locale):new RegExp(de(e.replace("\\","").replace(/\\(\[)|\\(\])|\[([^\]\[]*)\]|\\(.)/g,function(e,t,n,s,i){return t||n||s||i})))}function de(e){return e.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")}var he={};function ce(e,t){var n,s=t;for("string"==typeof e&&(e=[e]),a(t)&&(s=function(e,n){n[t]=M(e)}),n=0;n<e.length;n++)he[e[n]]=s}function fe(e,t){ce(e,function(e,n,s,i){s._w=s._w||{},t(e,s._w,s,i)})}var me=0,_e=1,ye=2,ge=3,pe=4,ve=5,we=6,Me=7,Se=8;function De(e){return ke(e)?366:365}function ke(e){return e%4==0&&e%100!=0||e%400==0}I("Y",0,0,function(){var e=this.year();return e<=9999?""+e:"+"+e}),I(0,["YY",2],0,function(){return this.year()%100}),I(0,["YYYY",4],0,"year"),I(0,["YYYYY",5],0,"year"),I(0,["YYYYYY",6,!0],0,"year"),H("year","y"),L("year",1),ue("Y",se),ue("YY",B,z),ue("YYYY",ee,q),ue("YYYYY",te,J),ue("YYYYYY",te,J),ce(["YYYYY","YYYYYY"],me),ce("YYYY",function(e,t){t[me]=2===e.length?n.parseTwoDigitYear(e):M(e)}),ce("YY",function(e,t){t[me]=n.parseTwoDigitYear(e)}),ce("Y",function(e,t){t[me]=parseInt(e,10)}),n.parseTwoDigitYear=function(e){return M(e)+(M(e)>68?1900:2e3)};var Ye,Oe=Te("FullYear",!0);function Te(e,t){return function(s){return null!=s?(be(this,e,s),n.updateOffset(this,t),this):xe(this,e)}}function xe(e,t){return e.isValid()?e._d["get"+(e._isUTC?"UTC":"")+t]():NaN}function be(e,t,n){e.isValid()&&!isNaN(n)&&("FullYear"===t&&ke(e.year())&&1===e.month()&&29===e.date()?e._d["set"+(e._isUTC?"UTC":"")+t](n,e.month(),Pe(n,e.month())):e._d["set"+(e._isUTC?"UTC":"")+t](n))}function Pe(e,t){if(isNaN(e)||isNaN(t))return NaN;var n,s=(t%(n=12)+n)%n;return e+=(t-s)/12,1===s?ke(e)?29:28:31-s%7%2}Ye=Array.prototype.indexOf?Array.prototype.indexOf:function(e){var t;for(t=0;t<this.length;++t)if(this[t]===e)return t;return-1},I("M",["MM",2],"Mo",function(){return this.month()+1}),I("MMM",0,0,function(e){return this.localeData().monthsShort(this,e)}),I("MMMM",0,0,function(e){return this.localeData().months(this,e)}),H("month","M"),L("month",8),ue("M",B),ue("MM",B,z),ue("MMM",function(e,t){return t.monthsShortRegex(e)}),ue("MMMM",function(e,t){return t.monthsRegex(e)}),ce(["M","MM"],function(e,t){t[_e]=M(e)-1}),ce(["MMM","MMMM"],function(e,t,n,s){var i=n._locale.monthsParse(e,s,n._strict);null!=i?t[_e]=i:c(n).invalidMonth=e});var We=/D[oD]?(\[[^\[\]]*\]|\s)+MMMM?/,He="January_February_March_April_May_June_July_August_September_October_November_December".split("_");var Re="Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_");function Ce(e,t){var n;if(!e.isValid())return e;if("string"==typeof t)if(/^\d+$/.test(t))t=M(t);else if(!a(t=e.localeData().monthsParse(t)))return e;return n=Math.min(e.date(),Pe(e.year(),t)),e._d["set"+(e._isUTC?"UTC":"")+"Month"](t,n),e}function Fe(e){return null!=e?(Ce(this,e),n.updateOffset(this,!0),this):xe(this,"Month")}var Le=ae;var Ue=ae;function Ne(){function e(e,t){return t.length-e.length}var t,n,s=[],i=[],r=[];for(t=0;t<12;t++)n=h([2e3,t]),s.push(this.monthsShort(n,"")),i.push(this.months(n,"")),r.push(this.months(n,"")),r.push(this.monthsShort(n,""));for(s.sort(e),i.sort(e),r.sort(e),t=0;t<12;t++)s[t]=de(s[t]),i[t]=de(i[t]);for(t=0;t<24;t++)r[t]=de(r[t]);this._monthsRegex=new RegExp("^("+r.join("|")+")","i"),this._monthsShortRegex=this._monthsRegex,this._monthsStrictRegex=new RegExp("^("+i.join("|")+")","i"),this._monthsShortStrictRegex=new RegExp("^("+s.join("|")+")","i")}function Ge(e){var t=new Date(Date.UTC.apply(null,arguments));return e<100&&e>=0&&isFinite(t.getUTCFullYear())&&t.setUTCFullYear(e),t}function Ve(e,t,n){var s=7+t-n;return-((7+Ge(e,0,s).getUTCDay()-t)%7)+s-1}function Ee(e,t,n,s,i){var r,a,o=1+7*(t-1)+(7+n-s)%7+Ve(e,s,i);return o<=0?a=De(r=e-1)+o:o>De(e)?(r=e+1,a=o-De(e)):(r=e,a=o),{year:r,dayOfYear:a}}function Ie(e,t,n){var s,i,r=Ve(e.year(),t,n),a=Math.floor((e.dayOfYear()-r-1)/7)+1;return a<1?s=a+Ae(i=e.year()-1,t,n):a>Ae(e.year(),t,n)?(s=a-Ae(e.year(),t,n),i=e.year()+1):(i=e.year(),s=a),{week:s,year:i}}function Ae(e,t,n){var s=Ve(e,t,n),i=Ve(e+1,t,n);return(De(e)-s+i)/7}I("w",["ww",2],"wo","week"),I("W",["WW",2],"Wo","isoWeek"),H("week","w"),H("isoWeek","W"),L("week",5),L("isoWeek",5),ue("w",B),ue("ww",B,z),ue("W",B),ue("WW",B,z),fe(["w","ww","W","WW"],function(e,t,n,s){t[s.substr(0,1)]=M(e)});I("d",0,"do","day"),I("dd",0,0,function(e){return this.localeData().weekdaysMin(this,e)}),I("ddd",0,0,function(e){return this.localeData().weekdaysShort(this,e)}),I("dddd",0,0,function(e){return this.localeData().weekdays(this,e)}),I("e",0,0,"weekday"),I("E",0,0,"isoWeekday"),H("day","d"),H("weekday","e"),H("isoWeekday","E"),L("day",11),L("weekday",11),L("isoWeekday",11),ue("d",B),ue("e",B),ue("E",B),ue("dd",function(e,t){return t.weekdaysMinRegex(e)}),ue("ddd",function(e,t){return t.weekdaysShortRegex(e)}),ue("dddd",function(e,t){return t.weekdaysRegex(e)}),fe(["dd","ddd","dddd"],function(e,t,n,s){var i=n._locale.weekdaysParse(e,s,n._strict);null!=i?t.d=i:c(n).invalidWeekday=e}),fe(["d","e","E"],function(e,t,n,s){t[s]=M(e)});var je="Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_");var Ze="Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_");var ze="Su_Mo_Tu_We_Th_Fr_Sa".split("_");var $e=ae;var qe=ae;var Je=ae;function Be(){function e(e,t){return t.length-e.length}var t,n,s,i,r,a=[],o=[],u=[],l=[];for(t=0;t<7;t++)n=h([2e3,1]).day(t),s=this.weekdaysMin(n,""),i=this.weekdaysShort(n,""),r=this.weekdays(n,""),a.push(s),o.push(i),u.push(r),l.push(s),l.push(i),l.push(r);for(a.sort(e),o.sort(e),u.sort(e),l.sort(e),t=0;t<7;t++)o[t]=de(o[t]),u[t]=de(u[t]),l[t]=de(l[t]);this._weekdaysRegex=new RegExp("^("+l.join("|")+")","i"),this._weekdaysShortRegex=this._weekdaysRegex,this._weekdaysMinRegex=this._weekdaysRegex,this._weekdaysStrictRegex=new RegExp("^("+u.join("|")+")","i"),this._weekdaysShortStrictRegex=new RegExp("^("+o.join("|")+")","i"),this._weekdaysMinStrictRegex=new RegExp("^("+a.join("|")+")","i")}function Qe(){return this.hours()%12||12}function Xe(e,t){I(e,0,0,function(){return this.localeData().meridiem(this.hours(),this.minutes(),t)})}function Ke(e,t){return t._meridiemParse}I("H",["HH",2],0,"hour"),I("h",["hh",2],0,Qe),I("k",["kk",2],0,function(){return this.hours()||24}),I("hmm",0,0,function(){return""+Qe.apply(this)+U(this.minutes(),2)}),I("hmmss",0,0,function(){return""+Qe.apply(this)+U(this.minutes(),2)+U(this.seconds(),2)}),I("Hmm",0,0,function(){return""+this.hours()+U(this.minutes(),2)}),I("Hmmss",0,0,function(){return""+this.hours()+U(this.minutes(),2)+U(this.seconds(),2)}),Xe("a",!0),Xe("A",!1),H("hour","h"),L("hour",13),ue("a",Ke),ue("A",Ke),ue("H",B),ue("h",B),ue("k",B),ue("HH",B,z),ue("hh",B,z),ue("kk",B,z),ue("hmm",Q),ue("hmmss",X),ue("Hmm",Q),ue("Hmmss",X),ce(["H","HH"],ge),ce(["k","kk"],function(e,t,n){var s=M(e);t[ge]=24===s?0:s}),ce(["a","A"],function(e,t,n){n._isPm=n._locale.isPM(e),n._meridiem=e}),ce(["h","hh"],function(e,t,n){t[ge]=M(e),c(n).bigHour=!0}),ce("hmm",function(e,t,n){var s=e.length-2;t[ge]=M(e.substr(0,s)),t[pe]=M(e.substr(s)),c(n).bigHour=!0}),ce("hmmss",function(e,t,n){var s=e.length-4,i=e.length-2;t[ge]=M(e.substr(0,s)),t[pe]=M(e.substr(s,2)),t[ve]=M(e.substr(i)),c(n).bigHour=!0}),ce("Hmm",function(e,t,n){var s=e.length-2;t[ge]=M(e.substr(0,s)),t[pe]=M(e.substr(s))}),ce("Hmmss",function(e,t,n){var s=e.length-4,i=e.length-2;t[ge]=M(e.substr(0,s)),t[pe]=M(e.substr(s,2)),t[ve]=M(e.substr(i))});var et,tt=Te("Hours",!0),nt={calendar:{sameDay:"[Today at] LT",nextDay:"[Tomorrow at] LT",nextWeek:"dddd [at] LT",lastDay:"[Yesterday at] LT",lastWeek:"[Last] dddd [at] LT",sameElse:"L"},longDateFormat:{LTS:"h:mm:ss A",LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D, YYYY",LLL:"MMMM D, YYYY h:mm A",LLLL:"dddd, MMMM D, YYYY h:mm A"},invalidDate:"Invalid date",ordinal:"%d",dayOfMonthOrdinalParse:/\d{1,2}/,relativeTime:{future:"in %s",past:"%s ago",s:"a few seconds",ss:"%d seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",M:"a month",MM:"%d months",y:"a year",yy:"%d years"},months:He,monthsShort:Re,week:{dow:0,doy:6},weekdays:je,weekdaysMin:ze,weekdaysShort:Ze,meridiemParse:/[ap]\.?m?\.?/i},st={},it={};function rt(e){return e?e.toLowerCase().replace("_","-"):e}function at(e){var t=null;if(!st[e]&&"undefined"!=typeof module&&module&&module.exports)try{t=et._abbr,require("./locale/"+e),ot(t)}catch(e){}return st[e]}function ot(e,t){var n;return e&&((n=r(t)?lt(e):ut(e,t))?et=n:"undefined"!=typeof console&&console.warn&&console.warn("Locale "+e+" not found. Did you forget to load it?")),et._abbr}function ut(e,t){if(null!==t){var n,s=nt;if(t.abbr=e,null!=st[e])T("defineLocaleOverride","use moment.updateLocale(localeName, config) to change an existing locale. moment.defineLocale(localeName, config) should only be used for creating a new locale See http://momentjs.com/guides/#/warnings/define-locale/ for more info."),s=st[e]._config;else if(null!=t.parentLocale)if(null!=st[t.parentLocale])s=st[t.parentLocale]._config;else{if(null==(n=at(t.parentLocale)))return it[t.parentLocale]||(it[t.parentLocale]=[]),it[t.parentLocale].push({name:e,config:t}),null;s=n._config}return st[e]=new P(b(s,t)),it[e]&&it[e].forEach(function(e){ut(e.name,e.config)}),ot(e),st[e]}return delete st[e],null}function lt(e){var t;if(e&&e._locale&&e._locale._abbr&&(e=e._locale._abbr),!e)return et;if(!s(e)){if(t=at(e))return t;e=[e]}return function(e){for(var t,n,s,i,r=0;r<e.length;){for(t=(i=rt(e[r]).split("-")).length,n=(n=rt(e[r+1]))?n.split("-"):null;t>0;){if(s=at(i.slice(0,t).join("-")))return s;if(n&&n.length>=t&&S(i,n,!0)>=t-1)break;t--}r++}return et}(e)}function dt(e){var t,n=e._a;return n&&-2===c(e).overflow&&(t=n[_e]<0||n[_e]>11?_e:n[ye]<1||n[ye]>Pe(n[me],n[_e])?ye:n[ge]<0||n[ge]>24||24===n[ge]&&(0!==n[pe]||0!==n[ve]||0!==n[we])?ge:n[pe]<0||n[pe]>59?pe:n[ve]<0||n[ve]>59?ve:n[we]<0||n[we]>999?we:-1,c(e)._overflowDayOfYear&&(t<me||t>ye)&&(t=ye),c(e)._overflowWeeks&&-1===t&&(t=Me),c(e)._overflowWeekday&&-1===t&&(t=Se),c(e).overflow=t),e}function ht(e,t,n){return null!=e?e:null!=t?t:n}function ct(e){var t,s,i,r,a,o=[];if(!e._d){var u,l;for(u=e,l=new Date(n.now()),i=u._useUTC?[l.getUTCFullYear(),l.getUTCMonth(),l.getUTCDate()]:[l.getFullYear(),l.getMonth(),l.getDate()],e._w&&null==e._a[ye]&&null==e._a[_e]&&function(e){var t,n,s,i,r,a,o,u;if(null!=(t=e._w).GG||null!=t.W||null!=t.E)r=1,a=4,n=ht(t.GG,e._a[me],Ie(Tt(),1,4).year),s=ht(t.W,1),((i=ht(t.E,1))<1||i>7)&&(u=!0);else{r=e._locale._week.dow,a=e._locale._week.doy;var l=Ie(Tt(),r,a);n=ht(t.gg,e._a[me],l.year),s=ht(t.w,l.week),null!=t.d?((i=t.d)<0||i>6)&&(u=!0):null!=t.e?(i=t.e+r,(t.e<0||t.e>6)&&(u=!0)):i=r}s<1||s>Ae(n,r,a)?c(e)._overflowWeeks=!0:null!=u?c(e)._overflowWeekday=!0:(o=Ee(n,s,i,r,a),e._a[me]=o.year,e._dayOfYear=o.dayOfYear)}(e),null!=e._dayOfYear&&(a=ht(e._a[me],i[me]),(e._dayOfYear>De(a)||0===e._dayOfYear)&&(c(e)._overflowDayOfYear=!0),s=Ge(a,0,e._dayOfYear),e._a[_e]=s.getUTCMonth(),e._a[ye]=s.getUTCDate()),t=0;t<3&&null==e._a[t];++t)e._a[t]=o[t]=i[t];for(;t<7;t++)e._a[t]=o[t]=null==e._a[t]?2===t?1:0:e._a[t];24===e._a[ge]&&0===e._a[pe]&&0===e._a[ve]&&0===e._a[we]&&(e._nextDay=!0,e._a[ge]=0),e._d=(e._useUTC?Ge:function(e,t,n,s,i,r,a){var o=new Date(e,t,n,s,i,r,a);return e<100&&e>=0&&isFinite(o.getFullYear())&&o.setFullYear(e),o}).apply(null,o),r=e._useUTC?e._d.getUTCDay():e._d.getDay(),null!=e._tzm&&e._d.setUTCMinutes(e._d.getUTCMinutes()-e._tzm),e._nextDay&&(e._a[ge]=24),e._w&&void 0!==e._w.d&&e._w.d!==r&&(c(e).weekdayMismatch=!0)}}var ft=/^\s*((?:[+-]\d{6}|\d{4})-(?:\d\d-\d\d|W\d\d-\d|W\d\d|\d\d\d|\d\d))(?:(T| )(\d\d(?::\d\d(?::\d\d(?:[.,]\d+)?)?)?)([\+\-]\d\d(?::?\d\d)?|\s*Z)?)?$/,mt=/^\s*((?:[+-]\d{6}|\d{4})(?:\d\d\d\d|W\d\d\d|W\d\d|\d\d\d|\d\d))(?:(T| )(\d\d(?:\d\d(?:\d\d(?:[.,]\d+)?)?)?)([\+\-]\d\d(?::?\d\d)?|\s*Z)?)?$/,_t=/Z|[+-]\d\d(?::?\d\d)?/,yt=[["YYYYYY-MM-DD",/[+-]\d{6}-\d\d-\d\d/],["YYYY-MM-DD",/\d{4}-\d\d-\d\d/],["GGGG-[W]WW-E",/\d{4}-W\d\d-\d/],["GGGG-[W]WW",/\d{4}-W\d\d/,!1],["YYYY-DDD",/\d{4}-\d{3}/],["YYYY-MM",/\d{4}-\d\d/,!1],["YYYYYYMMDD",/[+-]\d{10}/],["YYYYMMDD",/\d{8}/],["GGGG[W]WWE",/\d{4}W\d{3}/],["GGGG[W]WW",/\d{4}W\d{2}/,!1],["YYYYDDD",/\d{7}/]],gt=[["HH:mm:ss.SSSS",/\d\d:\d\d:\d\d\.\d+/],["HH:mm:ss,SSSS",/\d\d:\d\d:\d\d,\d+/],["HH:mm:ss",/\d\d:\d\d:\d\d/],["HH:mm",/\d\d:\d\d/],["HHmmss.SSSS",/\d\d\d\d\d\d\.\d+/],["HHmmss,SSSS",/\d\d\d\d\d\d,\d+/],["HHmmss",/\d\d\d\d\d\d/],["HHmm",/\d\d\d\d/],["HH",/\d\d/]],pt=/^\/?Date\((\-?\d+)/i;function vt(e){var t,n,s,i,r,a,o=e._i,u=ft.exec(o)||mt.exec(o);if(u){for(c(e).iso=!0,t=0,n=yt.length;t<n;t++)if(yt[t][1].exec(u[1])){i=yt[t][0],s=!1!==yt[t][2];break}if(null==i)return void(e._isValid=!1);if(u[3]){for(t=0,n=gt.length;t<n;t++)if(gt[t][1].exec(u[3])){r=(u[2]||" ")+gt[t][0];break}if(null==r)return void(e._isValid=!1)}if(!s&&null!=r)return void(e._isValid=!1);if(u[4]){if(!_t.exec(u[4]))return void(e._isValid=!1);a="Z"}e._f=i+(r||"")+(a||""),kt(e)}else e._isValid=!1}var wt=/^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),?\s)?(\d{1,2})\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{2,4})\s(\d\d):(\d\d)(?::(\d\d))?\s(?:(UT|GMT|[ECMP][SD]T)|([Zz])|([+-]\d{4}))$/;function Mt(e,t,n,s,i,r){var a=[function(e){var t=parseInt(e,10);{if(t<=49)return 2e3+t;if(t<=999)return 1900+t}return t}(e),Re.indexOf(t),parseInt(n,10),parseInt(s,10),parseInt(i,10)];return r&&a.push(parseInt(r,10)),a}var St={UT:0,GMT:0,EDT:-240,EST:-300,CDT:-300,CST:-360,MDT:-360,MST:-420,PDT:-420,PST:-480};function Dt(e){var t,n,s,i=wt.exec(e._i.replace(/\([^)]*\)|[\n\t]/g," ").replace(/(\s\s+)/g," ").trim());if(i){var r=Mt(i[4],i[3],i[2],i[5],i[6],i[7]);if(t=i[1],n=r,s=e,t&&Ze.indexOf(t)!==new Date(n[0],n[1],n[2]).getDay()&&(c(s).weekdayMismatch=!0,s._isValid=!1,1))return;e._a=r,e._tzm=function(e,t,n){if(e)return St[e];if(t)return 0;var s=parseInt(n,10),i=s%100;return(s-i)/100*60+i}(i[8],i[9],i[10]),e._d=Ge.apply(null,e._a),e._d.setUTCMinutes(e._d.getUTCMinutes()-e._tzm),c(e).rfc2822=!0}else e._isValid=!1}function kt(e){if(e._f!==n.ISO_8601)if(e._f!==n.RFC_2822){e._a=[],c(e).empty=!0;var t,s,i,r,a,o,u,d,h=""+e._i,f=h.length,m=0;for(i=j(e._f,e._locale).match(N)||[],t=0;t<i.length;t++)r=i[t],(s=(h.match(le(r,e))||[])[0])&&((a=h.substr(0,h.indexOf(s))).length>0&&c(e).unusedInput.push(a),h=h.slice(h.indexOf(s)+s.length),m+=s.length),E[r]?(s?c(e).empty=!1:c(e).unusedTokens.push(r),o=r,d=e,null!=(u=s)&&l(he,o)&&he[o](u,d._a,d,o)):e._strict&&!s&&c(e).unusedTokens.push(r);c(e).charsLeftOver=f-m,h.length>0&&c(e).unusedInput.push(h),e._a[ge]<=12&&!0===c(e).bigHour&&e._a[ge]>0&&(c(e).bigHour=void 0),c(e).parsedDateParts=e._a.slice(0),c(e).meridiem=e._meridiem,e._a[ge]=function(e,t,n){var s;if(null==n)return t;return null!=e.meridiemHour?e.meridiemHour(t,n):null!=e.isPM?((s=e.isPM(n))&&t<12&&(t+=12),s||12!==t||(t=0),t):t}(e._locale,e._a[ge],e._meridiem),ct(e),dt(e)}else Dt(e);else vt(e)}function Yt(e){var t,l,h,_,g=e._i,w=e._f;return e._locale=e._locale||lt(e._l),null===g||void 0===w&&""===g?m({nullInput:!0}):("string"==typeof g&&(e._i=g=e._locale.preparse(g)),v(g)?new p(dt(g)):(o(g)?e._d=g:s(w)?function(e){var t,n,s,i,r;if(0===e._f.length)return c(e).invalidFormat=!0,void(e._d=new Date(NaN));for(i=0;i<e._f.length;i++)r=0,t=y({},e),null!=e._useUTC&&(t._useUTC=e._useUTC),t._f=e._f[i],kt(t),f(t)&&(r+=c(t).charsLeftOver,r+=10*c(t).unusedTokens.length,c(t).score=r,(null==s||r<s)&&(s=r,n=t));d(e,n||t)}(e):w?kt(e):r(l=(t=e)._i)?t._d=new Date(n.now()):o(l)?t._d=new Date(l.valueOf()):"string"==typeof l?(h=t,null===(_=pt.exec(h._i))?(vt(h),!1===h._isValid&&(delete h._isValid,Dt(h),!1===h._isValid&&(delete h._isValid,n.createFromInputFallback(h)))):h._d=new Date(+_[1])):s(l)?(t._a=u(l.slice(0),function(e){return parseInt(e,10)}),ct(t)):i(l)?function(e){if(!e._d){var t=C(e._i);e._a=u([t.year,t.month,t.day||t.date,t.hour,t.minute,t.second,t.millisecond],function(e){return e&&parseInt(e,10)}),ct(e)}}(t):a(l)?t._d=new Date(l):n.createFromInputFallback(t),f(e)||(e._d=null),e))}function Ot(e,t,n,r,a){var o,u={};return!0!==n&&!1!==n||(r=n,n=void 0),(i(e)&&function(e){if(Object.getOwnPropertyNames)return 0===Object.getOwnPropertyNames(e).length;var t;for(t in e)if(e.hasOwnProperty(t))return!1;return!0}(e)||s(e)&&0===e.length)&&(e=void 0),u._isAMomentObject=!0,u._useUTC=u._isUTC=a,u._l=n,u._i=e,u._f=t,u._strict=r,(o=new p(dt(Yt(u))))._nextDay&&(o.add(1,"d"),o._nextDay=void 0),o}function Tt(e,t,n,s){return Ot(e,t,n,s,!1)}n.createFromInputFallback=k("value provided is not in a recognized RFC2822 or ISO format. moment construction falls back to js Date(), which is not reliable across all browsers and versions. Non RFC2822/ISO date formats are discouraged and will be removed in an upcoming major release. Please refer to http://momentjs.com/guides/#/warnings/js-date/ for more info.",function(e){e._d=new Date(e._i+(e._useUTC?" UTC":""))}),n.ISO_8601=function(){},n.RFC_2822=function(){};var xt=k("moment().min is deprecated, use moment.max instead. http://momentjs.com/guides/#/warnings/min-max/",function(){var e=Tt.apply(null,arguments);return this.isValid()&&e.isValid()?e<this?this:e:m()}),bt=k("moment().max is deprecated, use moment.min instead. http://momentjs.com/guides/#/warnings/min-max/",function(){var e=Tt.apply(null,arguments);return this.isValid()&&e.isValid()?e>this?this:e:m()});function Pt(e,t){var n,i;if(1===t.length&&s(t[0])&&(t=t[0]),!t.length)return Tt();for(n=t[0],i=1;i<t.length;++i)t[i].isValid()&&!t[i][e](n)||(n=t[i]);return n}var Wt=["year","quarter","month","week","day","hour","minute","second","millisecond"];function Ht(e){var t=C(e),n=t.year||0,s=t.quarter||0,i=t.month||0,r=t.week||0,a=t.day||0,o=t.hour||0,u=t.minute||0,l=t.second||0,d=t.millisecond||0;this._isValid=function(e){for(var t in e)if(-1===Ye.call(Wt,t)||null!=e[t]&&isNaN(e[t]))return!1;for(var n=!1,s=0;s<Wt.length;++s)if(e[Wt[s]]){if(n)return!1;parseFloat(e[Wt[s]])!==M(e[Wt[s]])&&(n=!0)}return!0}(t),this._milliseconds=+d+1e3*l+6e4*u+1e3*o*60*60,this._days=+a+7*r,this._months=+i+3*s+12*n,this._data={},this._locale=lt(),this._bubble()}function Rt(e){return e instanceof Ht}function Ct(e){return e<0?-1*Math.round(-1*e):Math.round(e)}function Ft(e,t){I(e,0,0,function(){var e=this.utcOffset(),n="+";return e<0&&(e=-e,n="-"),n+U(~~(e/60),2)+t+U(~~e%60,2)})}Ft("Z",":"),Ft("ZZ",""),ue("Z",re),ue("ZZ",re),ce(["Z","ZZ"],function(e,t,n){n._useUTC=!0,n._tzm=Ut(re,e)});var Lt=/([\+\-]|\d\d)/gi;function Ut(e,t){var n=(t||"").match(e);if(null===n)return null;var s=((n[n.length-1]||[])+"").match(Lt)||["-",0,0],i=60*s[1]+M(s[2]);return 0===i?0:"+"===s[0]?i:-i}function Nt(e,t){var s,i;return t._isUTC?(s=t.clone(),i=(v(e)||o(e)?e.valueOf():Tt(e).valueOf())-s.valueOf(),s._d.setTime(s._d.valueOf()+i),n.updateOffset(s,!1),s):Tt(e).local()}function Gt(e){return 15*-Math.round(e._d.getTimezoneOffset()/15)}function Vt(){return!!this.isValid()&&(this._isUTC&&0===this._offset)}n.updateOffset=function(){};var Et=/^(\-|\+)?(?:(\d*)[. ])?(\d+)\:(\d+)(?:\:(\d+)(\.\d*)?)?$/,It=/^(-|\+)?P(?:([-+]?[0-9,.]*)Y)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)W)?(?:([-+]?[0-9,.]*)D)?(?:T(?:([-+]?[0-9,.]*)H)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)S)?)?$/;function At(e,t){var n,s,i,r=e,o=null;return Rt(e)?r={ms:e._milliseconds,d:e._days,M:e._months}:a(e)?(r={},t?r[t]=e:r.milliseconds=e):(o=Et.exec(e))?(n="-"===o[1]?-1:1,r={y:0,d:M(o[ye])*n,h:M(o[ge])*n,m:M(o[pe])*n,s:M(o[ve])*n,ms:M(Ct(1e3*o[we]))*n}):(o=It.exec(e))?(n="-"===o[1]?-1:(o[1],1),r={y:jt(o[2],n),M:jt(o[3],n),w:jt(o[4],n),d:jt(o[5],n),h:jt(o[6],n),m:jt(o[7],n),s:jt(o[8],n)}):null==r?r={}:"object"==typeof r&&("from"in r||"to"in r)&&(i=function(e,t){var n;if(!e.isValid()||!t.isValid())return{milliseconds:0,months:0};t=Nt(t,e),e.isBefore(t)?n=Zt(e,t):((n=Zt(t,e)).milliseconds=-n.milliseconds,n.months=-n.months);return n}(Tt(r.from),Tt(r.to)),(r={}).ms=i.milliseconds,r.M=i.months),s=new Ht(r),Rt(e)&&l(e,"_locale")&&(s._locale=e._locale),s}function jt(e,t){var n=e&&parseFloat(e.replace(",","."));return(isNaN(n)?0:n)*t}function Zt(e,t){var n={milliseconds:0,months:0};return n.months=t.month()-e.month()+12*(t.year()-e.year()),e.clone().add(n.months,"M").isAfter(t)&&--n.months,n.milliseconds=+t-+e.clone().add(n.months,"M"),n}function zt(e,t){return function(n,s){var i;return null===s||isNaN(+s)||(T(t,"moment()."+t+"(period, number) is deprecated. Please use moment()."+t+"(number, period). See http://momentjs.com/guides/#/warnings/add-inverted-param/ for more info."),i=n,n=s,s=i),$t(this,At(n="string"==typeof n?+n:n,s),e),this}}function $t(e,t,s,i){var r=t._milliseconds,a=Ct(t._days),o=Ct(t._months);e.isValid()&&(i=null==i||i,o&&Ce(e,xe(e,"Month")+o*s),a&&be(e,"Date",xe(e,"Date")+a*s),r&&e._d.setTime(e._d.valueOf()+r*s),i&&n.updateOffset(e,a||o))}At.fn=Ht.prototype,At.invalid=function(){return At(NaN)};var qt=zt(1,"add"),Jt=zt(-1,"subtract");function Bt(e,t){var n=12*(t.year()-e.year())+(t.month()-e.month()),s=e.clone().add(n,"months");return-(n+(t-s<0?(t-s)/(s-e.clone().add(n-1,"months")):(t-s)/(e.clone().add(n+1,"months")-s)))||0}function Qt(e){var t;return void 0===e?this._locale._abbr:(null!=(t=lt(e))&&(this._locale=t),this)}n.defaultFormat="YYYY-MM-DDTHH:mm:ssZ",n.defaultFormatUtc="YYYY-MM-DDTHH:mm:ss[Z]";var Xt=k("moment().lang() is deprecated. Instead, use moment().localeData() to get the language configuration. Use moment().locale() to change languages.",function(e){return void 0===e?this.localeData():this.locale(e)});function Kt(){return this._locale}function en(e,t){I(0,[e,e.length],0,t)}function tn(e,t,n,s,i){var r;return null==e?Ie(this,s,i).year:(t>(r=Ae(e,s,i))&&(t=r),function(e,t,n,s,i){var r=Ee(e,t,n,s,i),a=Ge(r.year,0,r.dayOfYear);return this.year(a.getUTCFullYear()),this.month(a.getUTCMonth()),this.date(a.getUTCDate()),this}.call(this,e,t,n,s,i))}I(0,["gg",2],0,function(){return this.weekYear()%100}),I(0,["GG",2],0,function(){return this.isoWeekYear()%100}),en("gggg","weekYear"),en("ggggg","weekYear"),en("GGGG","isoWeekYear"),en("GGGGG","isoWeekYear"),H("weekYear","gg"),H("isoWeekYear","GG"),L("weekYear",1),L("isoWeekYear",1),ue("G",se),ue("g",se),ue("GG",B,z),ue("gg",B,z),ue("GGGG",ee,q),ue("gggg",ee,q),ue("GGGGG",te,J),ue("ggggg",te,J),fe(["gggg","ggggg","GGGG","GGGGG"],function(e,t,n,s){t[s.substr(0,2)]=M(e)}),fe(["gg","GG"],function(e,t,s,i){t[i]=n.parseTwoDigitYear(e)}),I("Q",0,"Qo","quarter"),H("quarter","Q"),L("quarter",7),ue("Q",Z),ce("Q",function(e,t){t[_e]=3*(M(e)-1)}),I("D",["DD",2],"Do","date"),H("date","D"),L("date",9),ue("D",B),ue("DD",B,z),ue("Do",function(e,t){return e?t._dayOfMonthOrdinalParse||t._ordinalParse:t._dayOfMonthOrdinalParseLenient}),ce(["D","DD"],ye),ce("Do",function(e,t){t[ye]=M(e.match(B)[0])});var nn=Te("Date",!0);I("DDD",["DDDD",3],"DDDo","dayOfYear"),H("dayOfYear","DDD"),L("dayOfYear",4),ue("DDD",K),ue("DDDD",$),ce(["DDD","DDDD"],function(e,t,n){n._dayOfYear=M(e)}),I("m",["mm",2],0,"minute"),H("minute","m"),L("minute",14),ue("m",B),ue("mm",B,z),ce(["m","mm"],pe);var sn=Te("Minutes",!1);I("s",["ss",2],0,"second"),H("second","s"),L("second",15),ue("s",B),ue("ss",B,z),ce(["s","ss"],ve);var rn,an=Te("Seconds",!1);for(I("S",0,0,function(){return~~(this.millisecond()/100)}),I(0,["SS",2],0,function(){return~~(this.millisecond()/10)}),I(0,["SSS",3],0,"millisecond"),I(0,["SSSS",4],0,function(){return 10*this.millisecond()}),I(0,["SSSSS",5],0,function(){return 100*this.millisecond()}),I(0,["SSSSSS",6],0,function(){return 1e3*this.millisecond()}),I(0,["SSSSSSS",7],0,function(){return 1e4*this.millisecond()}),I(0,["SSSSSSSS",8],0,function(){return 1e5*this.millisecond()}),I(0,["SSSSSSSSS",9],0,function(){return 1e6*this.millisecond()}),H("millisecond","ms"),L("millisecond",16),ue("S",K,Z),ue("SS",K,z),ue("SSS",K,$),rn="SSSS";rn.length<=9;rn+="S")ue(rn,ne);function on(e,t){t[we]=M(1e3*("0."+e))}for(rn="S";rn.length<=9;rn+="S")ce(rn,on);var un=Te("Milliseconds",!1);I("z",0,0,"zoneAbbr"),I("zz",0,0,"zoneName");var ln=p.prototype;function dn(e){return e}ln.add=qt,ln.calendar=function(e,t){var s=e||Tt(),i=Nt(s,this).startOf("day"),r=n.calendarFormat(this,i)||"sameElse",a=t&&(x(t[r])?t[r].call(this,s):t[r]);return this.format(a||this.localeData().calendar(r,this,Tt(s)))},ln.clone=function(){return new p(this)},ln.diff=function(e,t,n){var s,i,r;if(!this.isValid())return NaN;if(!(s=Nt(e,this)).isValid())return NaN;switch(i=6e4*(s.utcOffset()-this.utcOffset()),t=R(t)){case"year":r=Bt(this,s)/12;break;case"month":r=Bt(this,s);break;case"quarter":r=Bt(this,s)/3;break;case"second":r=(this-s)/1e3;break;case"minute":r=(this-s)/6e4;break;case"hour":r=(this-s)/36e5;break;case"day":r=(this-s-i)/864e5;break;case"week":r=(this-s-i)/6048e5;break;default:r=this-s}return n?r:w(r)},ln.endOf=function(e){return void 0===(e=R(e))||"millisecond"===e?this:("date"===e&&(e="day"),this.startOf(e).add(1,"isoWeek"===e?"week":e).subtract(1,"ms"))},ln.format=function(e){e||(e=this.isUtc()?n.defaultFormatUtc:n.defaultFormat);var t=A(this,e);return this.localeData().postformat(t)},ln.from=function(e,t){return this.isValid()&&(v(e)&&e.isValid()||Tt(e).isValid())?At({to:this,from:e}).locale(this.locale()).humanize(!t):this.localeData().invalidDate()},ln.fromNow=function(e){return this.from(Tt(),e)},ln.to=function(e,t){return this.isValid()&&(v(e)&&e.isValid()||Tt(e).isValid())?At({from:this,to:e}).locale(this.locale()).humanize(!t):this.localeData().invalidDate()},ln.toNow=function(e){return this.to(Tt(),e)},ln.get=function(e){return x(this[e=R(e)])?this[e]():this},ln.invalidAt=function(){return c(this).overflow},ln.isAfter=function(e,t){var n=v(e)?e:Tt(e);return!(!this.isValid()||!n.isValid())&&("millisecond"===(t=R(r(t)?"millisecond":t))?this.valueOf()>n.valueOf():n.valueOf()<this.clone().startOf(t).valueOf())},ln.isBefore=function(e,t){var n=v(e)?e:Tt(e);return!(!this.isValid()||!n.isValid())&&("millisecond"===(t=R(r(t)?"millisecond":t))?this.valueOf()<n.valueOf():this.clone().endOf(t).valueOf()<n.valueOf())},ln.isBetween=function(e,t,n,s){return("("===(s=s||"()")[0]?this.isAfter(e,n):!this.isBefore(e,n))&&(")"===s[1]?this.isBefore(t,n):!this.isAfter(t,n))},ln.isSame=function(e,t){var n,s=v(e)?e:Tt(e);return!(!this.isValid()||!s.isValid())&&("millisecond"===(t=R(t||"millisecond"))?this.valueOf()===s.valueOf():(n=s.valueOf(),this.clone().startOf(t).valueOf()<=n&&n<=this.clone().endOf(t).valueOf()))},ln.isSameOrAfter=function(e,t){return this.isSame(e,t)||this.isAfter(e,t)},ln.isSameOrBefore=function(e,t){return this.isSame(e,t)||this.isBefore(e,t)},ln.isValid=function(){return f(this)},ln.lang=Xt,ln.locale=Qt,ln.localeData=Kt,ln.max=bt,ln.min=xt,ln.parsingFlags=function(){return d({},c(this))},ln.set=function(e,t){if("object"==typeof e)for(var n=function(e){var t=[];for(var n in e)t.push({unit:n,priority:F[n]});return t.sort(function(e,t){return e.priority-t.priority}),t}(e=C(e)),s=0;s<n.length;s++)this[n[s].unit](e[n[s].unit]);else if(x(this[e=R(e)]))return this[e](t);return this},ln.startOf=function(e){switch(e=R(e)){case"year":this.month(0);case"quarter":case"month":this.date(1);case"week":case"isoWeek":case"day":case"date":this.hours(0);case"hour":this.minutes(0);case"minute":this.seconds(0);case"second":this.milliseconds(0)}return"week"===e&&this.weekday(0),"isoWeek"===e&&this.isoWeekday(1),"quarter"===e&&this.month(3*Math.floor(this.month()/3)),this},ln.subtract=Jt,ln.toArray=function(){var e=this;return[e.year(),e.month(),e.date(),e.hour(),e.minute(),e.second(),e.millisecond()]},ln.toObject=function(){var e=this;return{years:e.year(),months:e.month(),date:e.date(),hours:e.hours(),minutes:e.minutes(),seconds:e.seconds(),milliseconds:e.milliseconds()}},ln.toDate=function(){return new Date(this.valueOf())},ln.toISOString=function(e){if(!this.isValid())return null;var t=!0!==e,n=t?this.clone().utc():this;return n.year()<0||n.year()>9999?A(n,t?"YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]":"YYYYYY-MM-DD[T]HH:mm:ss.SSSZ"):x(Date.prototype.toISOString)?t?this.toDate().toISOString():new Date(this.valueOf()+60*this.utcOffset()*1e3).toISOString().replace("Z",A(n,"Z")):A(n,t?"YYYY-MM-DD[T]HH:mm:ss.SSS[Z]":"YYYY-MM-DD[T]HH:mm:ss.SSSZ")},ln.inspect=function(){if(!this.isValid())return"moment.invalid(/* "+this._i+" */)";var e="moment",t="";this.isLocal()||(e=0===this.utcOffset()?"moment.utc":"moment.parseZone",t="Z");var n="["+e+'("]',s=0<=this.year()&&this.year()<=9999?"YYYY":"YYYYYY",i=t+'[")]';return this.format(n+s+"-MM-DD[T]HH:mm:ss.SSS"+i)},ln.toJSON=function(){return this.isValid()?this.toISOString():null},ln.toString=function(){return this.clone().locale("en").format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ")},ln.unix=function(){return Math.floor(this.valueOf()/1e3)},ln.valueOf=function(){return this._d.valueOf()-6e4*(this._offset||0)},ln.creationData=function(){return{input:this._i,format:this._f,locale:this._locale,isUTC:this._isUTC,strict:this._strict}},ln.year=Oe,ln.isLeapYear=function(){return ke(this.year())},ln.weekYear=function(e){return tn.call(this,e,this.week(),this.weekday(),this.localeData()._week.dow,this.localeData()._week.doy)},ln.isoWeekYear=function(e){return tn.call(this,e,this.isoWeek(),this.isoWeekday(),1,4)},ln.quarter=ln.quarters=function(e){return null==e?Math.ceil((this.month()+1)/3):this.month(3*(e-1)+this.month()%3)},ln.month=Fe,ln.daysInMonth=function(){return Pe(this.year(),this.month())},ln.week=ln.weeks=function(e){var t=this.localeData().week(this);return null==e?t:this.add(7*(e-t),"d")},ln.isoWeek=ln.isoWeeks=function(e){var t=Ie(this,1,4).week;return null==e?t:this.add(7*(e-t),"d")},ln.weeksInYear=function(){var e=this.localeData()._week;return Ae(this.year(),e.dow,e.doy)},ln.isoWeeksInYear=function(){return Ae(this.year(),1,4)},ln.date=nn,ln.day=ln.days=function(e){if(!this.isValid())return null!=e?this:NaN;var t,n,s=this._isUTC?this._d.getUTCDay():this._d.getDay();return null!=e?(t=e,n=this.localeData(),e="string"!=typeof t?t:isNaN(t)?"number"==typeof(t=n.weekdaysParse(t))?t:null:parseInt(t,10),this.add(e-s,"d")):s},ln.weekday=function(e){if(!this.isValid())return null!=e?this:NaN;var t=(this.day()+7-this.localeData()._week.dow)%7;return null==e?t:this.add(e-t,"d")},ln.isoWeekday=function(e){if(!this.isValid())return null!=e?this:NaN;if(null!=e){var t=(n=e,s=this.localeData(),"string"==typeof n?s.weekdaysParse(n)%7||7:isNaN(n)?null:n);return this.day(this.day()%7?t:t-7)}return this.day()||7;var n,s},ln.dayOfYear=function(e){var t=Math.round((this.clone().startOf("day")-this.clone().startOf("year"))/864e5)+1;return null==e?t:this.add(e-t,"d")},ln.hour=ln.hours=tt,ln.minute=ln.minutes=sn,ln.second=ln.seconds=an,ln.millisecond=ln.milliseconds=un,ln.utcOffset=function(e,t,s){var i,r=this._offset||0;if(!this.isValid())return null!=e?this:NaN;if(null!=e){if("string"==typeof e){if(null===(e=Ut(re,e)))return this}else Math.abs(e)<16&&!s&&(e*=60);return!this._isUTC&&t&&(i=Gt(this)),this._offset=e,this._isUTC=!0,null!=i&&this.add(i,"m"),r!==e&&(!t||this._changeInProgress?$t(this,At(e-r,"m"),1,!1):this._changeInProgress||(this._changeInProgress=!0,n.updateOffset(this,!0),this._changeInProgress=null)),this}return this._isUTC?r:Gt(this)},ln.utc=function(e){return this.utcOffset(0,e)},ln.local=function(e){return this._isUTC&&(this.utcOffset(0,e),this._isUTC=!1,e&&this.subtract(Gt(this),"m")),this},ln.parseZone=function(){if(null!=this._tzm)this.utcOffset(this._tzm,!1,!0);else if("string"==typeof this._i){var e=Ut(ie,this._i);null!=e?this.utcOffset(e):this.utcOffset(0,!0)}return this},ln.hasAlignedHourOffset=function(e){return!!this.isValid()&&(e=e?Tt(e).utcOffset():0,(this.utcOffset()-e)%60==0)},ln.isDST=function(){return this.utcOffset()>this.clone().month(0).utcOffset()||this.utcOffset()>this.clone().month(5).utcOffset()},ln.isLocal=function(){return!!this.isValid()&&!this._isUTC},ln.isUtcOffset=function(){return!!this.isValid()&&this._isUTC},ln.isUtc=Vt,ln.isUTC=Vt,ln.zoneAbbr=function(){return this._isUTC?"UTC":""},ln.zoneName=function(){return this._isUTC?"Coordinated Universal Time":""},ln.dates=k("dates accessor is deprecated. Use date instead.",nn),ln.months=k("months accessor is deprecated. Use month instead",Fe),ln.years=k("years accessor is deprecated. Use year instead",Oe),ln.zone=k("moment().zone is deprecated, use moment().utcOffset instead. http://momentjs.com/guides/#/warnings/zone/",function(e,t){return null!=e?("string"!=typeof e&&(e=-e),this.utcOffset(e,t),this):-this.utcOffset()}),ln.isDSTShifted=k("isDSTShifted is deprecated. See http://momentjs.com/guides/#/warnings/dst-shifted/ for more information",function(){if(!r(this._isDSTShifted))return this._isDSTShifted;var e={};if(y(e,this),(e=Yt(e))._a){var t=e._isUTC?h(e._a):Tt(e._a);this._isDSTShifted=this.isValid()&&S(e._a,t.toArray())>0}else this._isDSTShifted=!1;return this._isDSTShifted});var hn=P.prototype;function cn(e,t,n,s){var i=lt(),r=h().set(s,t);return i[n](r,e)}function fn(e,t,n){if(a(e)&&(t=e,e=void 0),e=e||"",null!=t)return cn(e,t,n,"month");var s,i=[];for(s=0;s<12;s++)i[s]=cn(e,s,n,"month");return i}function mn(e,t,n,s){"boolean"==typeof e?(a(t)&&(n=t,t=void 0),t=t||""):(n=t=e,e=!1,a(t)&&(n=t,t=void 0),t=t||"");var i,r=lt(),o=e?r._week.dow:0;if(null!=n)return cn(t,(n+o)%7,s,"day");var u=[];for(i=0;i<7;i++)u[i]=cn(t,(i+o)%7,s,"day");return u}hn.calendar=function(e,t,n){var s=this._calendar[e]||this._calendar.sameElse;return x(s)?s.call(t,n):s},hn.longDateFormat=function(e){var t=this._longDateFormat[e],n=this._longDateFormat[e.toUpperCase()];return t||!n?t:(this._longDateFormat[e]=n.replace(/MMMM|MM|DD|dddd/g,function(e){return e.slice(1)}),this._longDateFormat[e])},hn.invalidDate=function(){return this._invalidDate},hn.ordinal=function(e){return this._ordinal.replace("%d",e)},hn.preparse=dn,hn.postformat=dn,hn.relativeTime=function(e,t,n,s){var i=this._relativeTime[n];return x(i)?i(e,t,n,s):i.replace(/%d/i,e)},hn.pastFuture=function(e,t){var n=this._relativeTime[e>0?"future":"past"];return x(n)?n(t):n.replace(/%s/i,t)},hn.set=function(e){var t,n;for(n in e)x(t=e[n])?this[n]=t:this["_"+n]=t;this._config=e,this._dayOfMonthOrdinalParseLenient=new RegExp((this._dayOfMonthOrdinalParse.source||this._ordinalParse.source)+"|"+/\d{1,2}/.source)},hn.months=function(e,t){return e?s(this._months)?this._months[e.month()]:this._months[(this._months.isFormat||We).test(t)?"format":"standalone"][e.month()]:s(this._months)?this._months:this._months.standalone},hn.monthsShort=function(e,t){return e?s(this._monthsShort)?this._monthsShort[e.month()]:this._monthsShort[We.test(t)?"format":"standalone"][e.month()]:s(this._monthsShort)?this._monthsShort:this._monthsShort.standalone},hn.monthsParse=function(e,t,n){var s,i,r;if(this._monthsParseExact)return function(e,t,n){var s,i,r,a=e.toLocaleLowerCase();if(!this._monthsParse)for(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[],s=0;s<12;++s)r=h([2e3,s]),this._shortMonthsParse[s]=this.monthsShort(r,"").toLocaleLowerCase(),this._longMonthsParse[s]=this.months(r,"").toLocaleLowerCase();return n?"MMM"===t?-1!==(i=Ye.call(this._shortMonthsParse,a))?i:null:-1!==(i=Ye.call(this._longMonthsParse,a))?i:null:"MMM"===t?-1!==(i=Ye.call(this._shortMonthsParse,a))?i:-1!==(i=Ye.call(this._longMonthsParse,a))?i:null:-1!==(i=Ye.call(this._longMonthsParse,a))?i:-1!==(i=Ye.call(this._shortMonthsParse,a))?i:null}.call(this,e,t,n);for(this._monthsParse||(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[]),s=0;s<12;s++){if(i=h([2e3,s]),n&&!this._longMonthsParse[s]&&(this._longMonthsParse[s]=new RegExp("^"+this.months(i,"").replace(".","")+"$","i"),this._shortMonthsParse[s]=new RegExp("^"+this.monthsShort(i,"").replace(".","")+"$","i")),n||this._monthsParse[s]||(r="^"+this.months(i,"")+"|^"+this.monthsShort(i,""),this._monthsParse[s]=new RegExp(r.replace(".",""),"i")),n&&"MMMM"===t&&this._longMonthsParse[s].test(e))return s;if(n&&"MMM"===t&&this._shortMonthsParse[s].test(e))return s;if(!n&&this._monthsParse[s].test(e))return s}},hn.monthsRegex=function(e){return this._monthsParseExact?(l(this,"_monthsRegex")||Ne.call(this),e?this._monthsStrictRegex:this._monthsRegex):(l(this,"_monthsRegex")||(this._monthsRegex=Ue),this._monthsStrictRegex&&e?this._monthsStrictRegex:this._monthsRegex)},hn.monthsShortRegex=function(e){return this._monthsParseExact?(l(this,"_monthsRegex")||Ne.call(this),e?this._monthsShortStrictRegex:this._monthsShortRegex):(l(this,"_monthsShortRegex")||(this._monthsShortRegex=Le),this._monthsShortStrictRegex&&e?this._monthsShortStrictRegex:this._monthsShortRegex)},hn.week=function(e){return Ie(e,this._week.dow,this._week.doy).week},hn.firstDayOfYear=function(){return this._week.doy},hn.firstDayOfWeek=function(){return this._week.dow},hn.weekdays=function(e,t){return e?s(this._weekdays)?this._weekdays[e.day()]:this._weekdays[this._weekdays.isFormat.test(t)?"format":"standalone"][e.day()]:s(this._weekdays)?this._weekdays:this._weekdays.standalone},hn.weekdaysMin=function(e){return e?this._weekdaysMin[e.day()]:this._weekdaysMin},hn.weekdaysShort=function(e){return e?this._weekdaysShort[e.day()]:this._weekdaysShort},hn.weekdaysParse=function(e,t,n){var s,i,r;if(this._weekdaysParseExact)return function(e,t,n){var s,i,r,a=e.toLocaleLowerCase();if(!this._weekdaysParse)for(this._weekdaysParse=[],this._shortWeekdaysParse=[],this._minWeekdaysParse=[],s=0;s<7;++s)r=h([2e3,1]).day(s),this._minWeekdaysParse[s]=this.weekdaysMin(r,"").toLocaleLowerCase(),this._shortWeekdaysParse[s]=this.weekdaysShort(r,"").toLocaleLowerCase(),this._weekdaysParse[s]=this.weekdays(r,"").toLocaleLowerCase();return n?"dddd"===t?-1!==(i=Ye.call(this._weekdaysParse,a))?i:null:"ddd"===t?-1!==(i=Ye.call(this._shortWeekdaysParse,a))?i:null:-1!==(i=Ye.call(this._minWeekdaysParse,a))?i:null:"dddd"===t?-1!==(i=Ye.call(this._weekdaysParse,a))?i:-1!==(i=Ye.call(this._shortWeekdaysParse,a))?i:-1!==(i=Ye.call(this._minWeekdaysParse,a))?i:null:"ddd"===t?-1!==(i=Ye.call(this._shortWeekdaysParse,a))?i:-1!==(i=Ye.call(this._weekdaysParse,a))?i:-1!==(i=Ye.call(this._minWeekdaysParse,a))?i:null:-1!==(i=Ye.call(this._minWeekdaysParse,a))?i:-1!==(i=Ye.call(this._weekdaysParse,a))?i:-1!==(i=Ye.call(this._shortWeekdaysParse,a))?i:null}.call(this,e,t,n);for(this._weekdaysParse||(this._weekdaysParse=[],this._minWeekdaysParse=[],this._shortWeekdaysParse=[],this._fullWeekdaysParse=[]),s=0;s<7;s++){if(i=h([2e3,1]).day(s),n&&!this._fullWeekdaysParse[s]&&(this._fullWeekdaysParse[s]=new RegExp("^"+this.weekdays(i,"").replace(".",".?")+"$","i"),this._shortWeekdaysParse[s]=new RegExp("^"+this.weekdaysShort(i,"").replace(".",".?")+"$","i"),this._minWeekdaysParse[s]=new RegExp("^"+this.weekdaysMin(i,"").replace(".",".?")+"$","i")),this._weekdaysParse[s]||(r="^"+this.weekdays(i,"")+"|^"+this.weekdaysShort(i,"")+"|^"+this.weekdaysMin(i,""),this._weekdaysParse[s]=new RegExp(r.replace(".",""),"i")),n&&"dddd"===t&&this._fullWeekdaysParse[s].test(e))return s;if(n&&"ddd"===t&&this._shortWeekdaysParse[s].test(e))return s;if(n&&"dd"===t&&this._minWeekdaysParse[s].test(e))return s;if(!n&&this._weekdaysParse[s].test(e))return s}},hn.weekdaysRegex=function(e){return this._weekdaysParseExact?(l(this,"_weekdaysRegex")||Be.call(this),e?this._weekdaysStrictRegex:this._weekdaysRegex):(l(this,"_weekdaysRegex")||(this._weekdaysRegex=$e),this._weekdaysStrictRegex&&e?this._weekdaysStrictRegex:this._weekdaysRegex)},hn.weekdaysShortRegex=function(e){return this._weekdaysParseExact?(l(this,"_weekdaysRegex")||Be.call(this),e?this._weekdaysShortStrictRegex:this._weekdaysShortRegex):(l(this,"_weekdaysShortRegex")||(this._weekdaysShortRegex=qe),this._weekdaysShortStrictRegex&&e?this._weekdaysShortStrictRegex:this._weekdaysShortRegex)},hn.weekdaysMinRegex=function(e){return this._weekdaysParseExact?(l(this,"_weekdaysRegex")||Be.call(this),e?this._weekdaysMinStrictRegex:this._weekdaysMinRegex):(l(this,"_weekdaysMinRegex")||(this._weekdaysMinRegex=Je),this._weekdaysMinStrictRegex&&e?this._weekdaysMinStrictRegex:this._weekdaysMinRegex)},hn.isPM=function(e){return"p"===(e+"").toLowerCase().charAt(0)},hn.meridiem=function(e,t,n){return e>11?n?"pm":"PM":n?"am":"AM"},ot("en",{dayOfMonthOrdinalParse:/\d{1,2}(th|st|nd|rd)/,ordinal:function(e){var t=e%10;return e+(1===M(e%100/10)?"th":1===t?"st":2===t?"nd":3===t?"rd":"th")}}),n.lang=k("moment.lang is deprecated. Use moment.locale instead.",ot),n.langData=k("moment.langData is deprecated. Use moment.localeData instead.",lt);var _n=Math.abs;function yn(e,t,n,s){var i=At(t,n);return e._milliseconds+=s*i._milliseconds,e._days+=s*i._days,e._months+=s*i._months,e._bubble()}function gn(e){return e<0?Math.floor(e):Math.ceil(e)}function pn(e){return 4800*e/146097}function vn(e){return 146097*e/4800}function wn(e){return function(){return this.as(e)}}var Mn=wn("ms"),Sn=wn("s"),Dn=wn("m"),kn=wn("h"),Yn=wn("d"),On=wn("w"),Tn=wn("M"),xn=wn("y");function bn(e){return function(){return this.isValid()?this._data[e]:NaN}}var Pn=bn("milliseconds"),Wn=bn("seconds"),Hn=bn("minutes"),Rn=bn("hours"),Cn=bn("days"),Fn=bn("months"),Ln=bn("years");var Un=Math.round,Nn={ss:44,s:45,m:45,h:22,d:26,M:11};var Gn=Math.abs;function Vn(e){return(e>0)-(e<0)||+e}function En(){if(!this.isValid())return this.localeData().invalidDate();var e,t,n=Gn(this._milliseconds)/1e3,s=Gn(this._days),i=Gn(this._months);t=w((e=w(n/60))/60),n%=60,e%=60;var r=w(i/12),a=i%=12,o=s,u=t,l=e,d=n?n.toFixed(3).replace(/\.?0+$/,""):"",h=this.asSeconds();if(!h)return"P0D";var c=h<0?"-":"",f=Vn(this._months)!==Vn(h)?"-":"",m=Vn(this._days)!==Vn(h)?"-":"",_=Vn(this._milliseconds)!==Vn(h)?"-":"";return c+"P"+(r?f+r+"Y":"")+(a?f+a+"M":"")+(o?m+o+"D":"")+(u||l||d?"T":"")+(u?_+u+"H":"")+(l?_+l+"M":"")+(d?_+d+"S":"")}var In=Ht.prototype;return In.isValid=function(){return this._isValid},In.abs=function(){var e=this._data;return this._milliseconds=_n(this._milliseconds),this._days=_n(this._days),this._months=_n(this._months),e.milliseconds=_n(e.milliseconds),e.seconds=_n(e.seconds),e.minutes=_n(e.minutes),e.hours=_n(e.hours),e.months=_n(e.months),e.years=_n(e.years),this},In.add=function(e,t){return yn(this,e,t,1)},In.subtract=function(e,t){return yn(this,e,t,-1)},In.as=function(e){if(!this.isValid())return NaN;var t,n,s=this._milliseconds;if("month"===(e=R(e))||"year"===e)return t=this._days+s/864e5,n=this._months+pn(t),"month"===e?n:n/12;switch(t=this._days+Math.round(vn(this._months)),e){case"week":return t/7+s/6048e5;case"day":return t+s/864e5;case"hour":return 24*t+s/36e5;case"minute":return 1440*t+s/6e4;case"second":return 86400*t+s/1e3;case"millisecond":return Math.floor(864e5*t)+s;default:throw new Error("Unknown unit "+e)}},In.asMilliseconds=Mn,In.asSeconds=Sn,In.asMinutes=Dn,In.asHours=kn,In.asDays=Yn,In.asWeeks=On,In.asMonths=Tn,In.asYears=xn,In.valueOf=function(){return this.isValid()?this._milliseconds+864e5*this._days+this._months%12*2592e6+31536e6*M(this._months/12):NaN},In._bubble=function(){var e,t,n,s,i,r=this._milliseconds,a=this._days,o=this._months,u=this._data;return r>=0&&a>=0&&o>=0||r<=0&&a<=0&&o<=0||(r+=864e5*gn(vn(o)+a),a=0,o=0),u.milliseconds=r%1e3,e=w(r/1e3),u.seconds=e%60,t=w(e/60),u.minutes=t%60,n=w(t/60),u.hours=n%24,o+=i=w(pn(a+=w(n/24))),a-=gn(vn(i)),s=w(o/12),o%=12,u.days=a,u.months=o,u.years=s,this},In.clone=function(){return At(this)},In.get=function(e){return e=R(e),this.isValid()?this[e+"s"]():NaN},In.milliseconds=Pn,In.seconds=Wn,In.minutes=Hn,In.hours=Rn,In.days=Cn,In.weeks=function(){return w(this.days()/7)},In.months=Fn,In.years=Ln,In.humanize=function(e){if(!this.isValid())return this.localeData().invalidDate();var t,n,s,i,r,a,o,u,l,d,h,c=this.localeData(),f=(n=!e,s=c,i=At(t=this).abs(),r=Un(i.as("s")),a=Un(i.as("m")),o=Un(i.as("h")),u=Un(i.as("d")),l=Un(i.as("M")),d=Un(i.as("y")),(h=r<=Nn.ss&&["s",r]||r<Nn.s&&["ss",r]||a<=1&&["m"]||a<Nn.m&&["mm",a]||o<=1&&["h"]||o<Nn.h&&["hh",o]||u<=1&&["d"]||u<Nn.d&&["dd",u]||l<=1&&["M"]||l<Nn.M&&["MM",l]||d<=1&&["y"]||["yy",d])[2]=n,h[3]=+t>0,h[4]=s,function(e,t,n,s,i){return i.relativeTime(t||1,!!n,e,s)}.apply(null,h));return e&&(f=c.pastFuture(+this,f)),c.postformat(f)},In.toISOString=En,In.toString=En,In.toJSON=En,In.locale=Qt,In.localeData=Kt,In.toIsoString=k("toIsoString() is deprecated. Please use toISOString() instead (notice the capitals)",En),In.lang=Xt,I("X",0,0,"unix"),I("x",0,0,"valueOf"),ue("x",se),ue("X",/[+-]?\d+(\.\d{1,3})?/),ce("X",function(e,t,n){n._d=new Date(1e3*parseFloat(e,10))}),ce("x",function(e,t,n){n._d=new Date(M(e))}),n.version="2.21.0",e=Tt,n.fn=ln,n.min=function(){return Pt("isBefore",[].slice.call(arguments,0))},n.max=function(){return Pt("isAfter",[].slice.call(arguments,0))},n.now=function(){return Date.now?Date.now():+new Date},n.utc=h,n.unix=function(e){return Tt(1e3*e)},n.months=function(e,t){return fn(e,t,"months")},n.isDate=o,n.locale=ot,n.invalid=m,n.duration=At,n.isMoment=v,n.weekdays=function(e,t,n){return mn(e,t,n,"weekdays")},n.parseZone=function(){return Tt.apply(null,arguments).parseZone()},n.localeData=lt,n.isDuration=Rt,n.monthsShort=function(e,t){return fn(e,t,"monthsShort")},n.weekdaysMin=function(e,t,n){return mn(e,t,n,"weekdaysMin")},n.defineLocale=ut,n.updateLocale=function(e,t){if(null!=t){var n,s,i=nt;null!=(s=at(e))&&(i=s._config),(n=new P(t=b(i,t))).parentLocale=st[e],st[e]=n,ot(e)}else null!=st[e]&&(null!=st[e].parentLocale?st[e]=st[e].parentLocale:null!=st[e]&&delete st[e]);return st[e]},n.locales=function(){return Y(st)},n.weekdaysShort=function(e,t,n){return mn(e,t,n,"weekdaysShort")},n.normalizeUnits=R,n.relativeTimeRounding=function(e){return void 0===e?Un:"function"==typeof e&&(Un=e,!0)},n.relativeTimeThreshold=function(e,t){return void 0!==Nn[e]&&(void 0===t?Nn[e]:(Nn[e]=t,"s"===e&&(Nn.ss=t-1),!0))},n.calendarFormat=function(e,t){var n=e.diff(t,"days",!0);return n<-6?"sameElse":n<-1?"lastWeek":n<0?"lastDay":n<1?"sameDay":n<2?"nextDay":n<7?"nextWeek":"sameElse"},n.prototype=ln,n.HTML5_FMT={DATETIME_LOCAL:"YYYY-MM-DDTHH:mm",DATETIME_LOCAL_SECONDS:"YYYY-MM-DDTHH:mm:ss",DATETIME_LOCAL_MS:"YYYY-MM-DDTHH:mm:ss.SSS",DATE:"YYYY-MM-DD",TIME:"HH:mm",TIME_SECONDS:"HH:mm:ss",TIME_MS:"HH:mm:ss.SSS",WEEK:"YYYY-[W]WW",MONTH:"YYYY-MM"},n});var luxon = function (e) { "use strict"; function r(e, t) { for (var n = 0; n < t.length; n++) { var r = t[n]; r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r) } } function i(e, t, n) { return t && r(e.prototype, t), n && r(e, n), e } function o(e, t) { e.prototype = Object.create(t.prototype), (e.prototype.constructor = e).__proto__ = t } function a(e) { return (a = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) { return e.__proto__ || Object.getPrototypeOf(e) })(e) } function u(e, t) { return (u = Object.setPrototypeOf || function (e, t) { return e.__proto__ = t, e })(e, t) } function s(e, t, n) { return (s = function () { if ("undefined" != typeof Reflect && Reflect.construct && !Reflect.construct.sham) { if ("function" == typeof Proxy) return 1; try { return Date.prototype.toString.call(Reflect.construct(Date, [], function () { })), 1 } catch (e) { return } } }() ? Reflect.construct : function (e, t, n) { var r = [null]; r.push.apply(r, t); var i = new (Function.bind.apply(e, r)); return n && u(i, n.prototype), i }).apply(null, arguments) } function t(e) { var r = "function" == typeof Map ? new Map : void 0; return (t = function (e) { if (null === e || (t = e, -1 === Function.toString.call(t).indexOf("[native code]"))) return e; var t; if ("function" != typeof e) throw new TypeError("Super expression must either be null or a function"); if (void 0 !== r) { if (r.has(e)) return r.get(e); r.set(e, n) } function n() { return s(e, arguments, a(this).constructor) } return n.prototype = Object.create(e.prototype, { constructor: { value: n, enumerable: !1, writable: !0, configurable: !0 } }), u(n, e) })(e) } function c(e, t) { (null == t || t > e.length) && (t = e.length); for (var n = 0, r = new Array(t); n < t; n++)r[n] = e[n]; return r } function V(e) { var t = 0; if ("undefined" != typeof Symbol && null != e[Symbol.iterator]) return (t = e[Symbol.iterator]()).next.bind(t); if (Array.isArray(e) || (e = function (e, t) { if (e) { if ("string" == typeof e) return c(e, t); var n = Object.prototype.toString.call(e).slice(8, -1); return "Object" === n && e.constructor && (n = e.constructor.name), "Map" === n || "Set" === n ? Array.from(e) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? c(e, t) : void 0 } }(e))) return function () { return t >= e.length ? { done: !0 } : { done: !1, value: e[t++] } }; throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.") } var n = function (e) { function t() { return e.apply(this, arguments) || this } return o(t, e), t }(t(Error)), l = function (t) { function e(e) { return t.call(this, "Invalid DateTime: " + e.toMessage()) || this } return o(e, t), e }(n), f = function (t) { function e(e) { return t.call(this, "Invalid Interval: " + e.toMessage()) || this } return o(e, t), e }(n), d = function (t) { function e(e) { return t.call(this, "Invalid Duration: " + e.toMessage()) || this } return o(e, t), e }(n), L = function (e) { function t() { return e.apply(this, arguments) || this } return o(t, e), t }(n), h = function (t) { function e(e) { return t.call(this, "Invalid unit " + e) || this } return o(e, t), e }(n), m = function (e) { function t() { return e.apply(this, arguments) || this } return o(t, e), t }(n), y = function (e) { function t() { return e.call(this, "Zone is an abstract class") || this } return o(t, e), t }(n), v = "numeric", g = "short", p = "long", w = { year: v, month: v, day: v }, k = { year: v, month: g, day: v }, b = { year: v, month: p, day: v }, O = { year: v, month: p, day: v, weekday: p }, S = { hour: v, minute: v }, T = { hour: v, minute: v, second: v }, M = { hour: v, minute: v, second: v, timeZoneName: g }, N = { hour: v, minute: v, second: v, timeZoneName: p }, D = { hour: v, minute: v, hour12: !1 }, E = { hour: v, minute: v, second: v, hour12: !1 }, x = { hour: v, minute: v, second: v, hour12: !1, timeZoneName: g }, F = { hour: v, minute: v, second: v, hour12: !1, timeZoneName: p }, Z = { year: v, month: v, day: v, hour: v, minute: v }, C = { year: v, month: v, day: v, hour: v, minute: v, second: v }, j = { year: v, month: g, day: v, hour: v, minute: v }, A = { year: v, month: g, day: v, hour: v, minute: v, second: v }, z = { year: v, month: g, day: v, weekday: g, hour: v, minute: v }, _ = { year: v, month: p, day: v, hour: v, minute: v, timeZoneName: g }, q = { year: v, month: p, day: v, hour: v, minute: v, second: v, timeZoneName: g }, H = { year: v, month: p, day: v, weekday: p, hour: v, minute: v, timeZoneName: p }, U = { year: v, month: p, day: v, weekday: p, hour: v, minute: v, second: v, timeZoneName: p }; function R(e) { return void 0 === e } function W(e) { return "number" == typeof e } function P(e) { return "number" == typeof e && e % 1 == 0 } function I() { try { return "undefined" != typeof Intl && Intl.DateTimeFormat } catch (e) { return !1 } } function J() { return !R(Intl.DateTimeFormat.prototype.formatToParts) } function Y() { try { return "undefined" != typeof Intl && !!Intl.RelativeTimeFormat } catch (e) { return !1 } } function G(e, r, i) { if (0 !== e.length) return e.reduce(function (e, t) { var n = [r(t), t]; return e && i(e[0], n[0]) === e[0] ? e : n }, null)[1] } function $(n, e) { return e.reduce(function (e, t) { return e[t] = n[t], e }, {}) } function B(e, t) { return Object.prototype.hasOwnProperty.call(e, t) } function Q(e, t, n) { return P(e) && t <= e && e <= n } function K(e, t) { return void 0 === t && (t = 2), e.toString().length < t ? ("0".repeat(t) + e).slice(-t) : e.toString() } function X(e) { return R(e) || null === e || "" === e ? void 0 : parseInt(e, 10) } function ee(e) { if (!R(e) && null !== e && "" !== e) { var t = 1e3 * parseFloat("0." + e); return Math.floor(t) } } function te(e, t, n) { void 0 === n && (n = !1); var r = Math.pow(10, t); return (n ? Math.trunc : Math.round)(e * r) / r } function ne(e) { return e % 4 == 0 && (e % 100 != 0 || e % 400 == 0) } function re(e) { return ne(e) ? 366 : 365 } function ie(e, t) { var n, r, i = (n = t - 1) - (r = 12) * Math.floor(n / r) + 1; return 2 == i ? ne(e + (t - i) / 12) ? 29 : 28 : [31, null, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][i - 1] } function oe(e) { var t = Date.UTC(e.year, e.month - 1, e.day, e.hour, e.minute, e.second, e.millisecond); return e.year < 100 && 0 <= e.year && (t = new Date(t)).setUTCFullYear(t.getUTCFullYear() - 1900), +t } function ae(e) { var t = (e + Math.floor(e / 4) - Math.floor(e / 100) + Math.floor(e / 400)) % 7, n = e - 1, r = (n + Math.floor(n / 4) - Math.floor(n / 100) + Math.floor(n / 400)) % 7; return 4 == t || 3 == r ? 53 : 52 } function ue(e) { return 99 < e ? e : 60 < e ? 1900 + e : 2e3 + e } function se(e, t, n, r) { void 0 === r && (r = null); var i = new Date(e), o = { hour12: !1, year: "numeric", month: "2-digit", day: "2-digit", hour: "2-digit", minute: "2-digit" }; r && (o.timeZone = r); var a = Object.assign({ timeZoneName: t }, o), u = I(); if (u && J()) { var s = new Intl.DateTimeFormat(n, a).formatToParts(i).find(function (e) { return "timezonename" === e.type.toLowerCase() }); return s ? s.value : null } if (u) { var c = new Intl.DateTimeFormat(n, o).format(i); return new Intl.DateTimeFormat(n, a).format(i).substring(c.length).replace(/^[, \u200e]+/, "") } return null } function ce(e, t) { var n = parseInt(e, 10); Number.isNaN(n) && (n = 0); var r = parseInt(t, 10) || 0; return 60 * n + (n < 0 || Object.is(n, -0) ? -r : r) } function le(e) { var t = Number(e); if ("boolean" == typeof e || "" === e || Number.isNaN(t)) throw new m("Invalid unit value " + e); return t } function fe(e, t, n) { var r = {}; for (var i in e) if (B(e, i)) { if (0 <= n.indexOf(i)) continue; var o = e[i]; if (null == o) continue; r[t(i)] = le(o) } return r } function de(e, t) { var n = Math.trunc(e / 60), r = Math.abs(e % 60), i = 0 <= n && !Object.is(n, -0) ? "+" : "-", o = i + Math.abs(n); switch (t) { case "short": return i + K(Math.abs(n), 2) + ":" + K(r, 2); case "narrow": return 0 < r ? o + ":" + r : o; case "techie": return i + K(Math.abs(n), 2) + K(r, 2); default: throw new RangeError("Value format " + t + " is out of range for property format") } } function he(e) { return $(e, ["hour", "minute", "second", "millisecond"]) } var me = /[A-Za-z_+-]{1,256}(:?\/[A-Za-z_+-]{1,256}(\/[A-Za-z_+-]{1,256})?)?/; function ye(e) { return JSON.stringify(e, Object.keys(e).sort()) } var ve = monthNames.longName, ge = monthNames.shortName, pe = ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"]; function we(e) { switch (e) { case "narrow": return pe; case "short": return ge; case "long": return ve; case "numeric": return ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"]; case "2-digit": return ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"]; default: return null } } var ke = weekDayNames.longName, be = weekDayNames.shortName, Oe = ["M", "T", "W", "T", "F", "S", "S"]; function Se(e) { switch (e) { case "narrow": return Oe; case "short": return be; case "long": return ke; case "numeric": return ["1", "2", "3", "4", "5", "6", "7"]; default: return null } } var Te = meridiems, Me = ["Before Christ", "Anno Domini"], Ne = ["BC", "AD"], De = ["B", "A"]; function Ee(e) { switch (e) { case "narrow": return De; case "short": return Ne; case "long": return Me; default: return null } } function Ie(e, t) { for (var n, r = "", i = V(e); !(n = i()).done;) { var o = n.value; o.literal ? r += o.val : r += t(o.val) } return r } var Ve = { D: w, DD: k, DDD: b, DDDD: O, t: S, tt: T, ttt: M, tttt: N, T: D, TT: E, TTT: x, TTTT: F, f: Z, ff: j, fff: _, ffff: H, F: C, FF: A, FFF: q, FFFF: U }, Le = function () { function h(e, t) { this.opts = t, this.loc = e, this.systemLoc = null } h.create = function (e, t) { return void 0 === t && (t = {}), new h(e, t) }, h.parseFormat = function (e) { for (var t = null, n = "", r = !1, i = [], o = 0; o < e.length; o++) { var a = e.charAt(o); "'" === a ? (0 < n.length && i.push({ literal: r, val: n }), t = null, n = "", r = !r) : r || a === t ? n += a : (0 < n.length && i.push({ literal: !1, val: n }), t = n = a) } return 0 < n.length && i.push({ literal: r, val: n }), i }, h.macroTokenToFormatOpts = function (e) { return Ve[e] }; var e = h.prototype; return e.formatWithSystemDefault = function (e, t) { return null === this.systemLoc && (this.systemLoc = this.loc.redefaultToSystem()), this.systemLoc.dtFormatter(e, Object.assign({}, this.opts, t)).format() }, e.formatDateTime = function (e, t) { return void 0 === t && (t = {}), this.loc.dtFormatter(e, Object.assign({}, this.opts, t)).format() }, e.formatDateTimeParts = function (e, t) { return void 0 === t && (t = {}), this.loc.dtFormatter(e, Object.assign({}, this.opts, t)).formatToParts() }, e.resolvedOptions = function (e, t) { return void 0 === t && (t = {}), this.loc.dtFormatter(e, Object.assign({}, this.opts, t)).resolvedOptions() }, e.num = function (e, t) { if (void 0 === t && (t = 0), this.opts.forceSimple) return K(e, t); var n = Object.assign({}, this.opts); return 0 < t && (n.padTo = t), this.loc.numberFormatter(n).format(e) }, e.formatDateTimeFromString = function (r, e) { function i(e, t) { return l.loc.extract(r, e, t) } function o(e) { return r.isOffsetFixed && 0 === r.offset && e.allowZ ? "Z" : r.isValid ? r.zone.formatOffset(r.ts, e.format) : "" } function a() { return f ? Te[r.hour < 12 ? 0 : 1] : i({ hour: "numeric", hour12: !0 }, "dayperiod") } function u(e, t) { return f ? (n = r, we(e)[n.month - 1]) : i(t ? { month: e } : { month: e, day: "numeric" }, "month"); var n } function s(e, t) { return f ? (n = r, Se(e)[n.weekday - 1]) : i(t ? { weekday: e } : { weekday: e, month: "long", day: "numeric" }, "weekday"); var n } function c(e) { return f ? (t = r, Ee(e)[t.year < 0 ? 0 : 1]) : i({ era: e }, "era"); var t } var l = this, f = "en" === this.loc.listingMode(), d = this.loc.outputCalendar && "gregory" !== this.loc.outputCalendar && J(); return Ie(h.parseFormat(e), function (e) { switch (e) { case "S": return l.num(r.millisecond); case "u": case "SSS": return l.num(r.millisecond, 3); case "s": return l.num(r.second); case "ss": return l.num(r.second, 2); case "m": return l.num(r.minute); case "mm": return l.num(r.minute, 2); case "h": return l.num(r.hour % 12 == 0 ? 12 : r.hour % 12); case "hh": return l.num(r.hour % 12 == 0 ? 12 : r.hour % 12, 2); case "H": return l.num(r.hour); case "HH": return l.num(r.hour, 2); case "Z": return o({ format: "narrow", allowZ: l.opts.allowZ }); case "ZZ": return o({ format: "short", allowZ: l.opts.allowZ }); case "ZZZ": return o({ format: "techie", allowZ: l.opts.allowZ }); case "ZZZZ": return r.zone.offsetName(r.ts, { format: "short", locale: l.loc.locale }); case "ZZZZZ": return r.zone.offsetName(r.ts, { format: "long", locale: l.loc.locale }); case "z": return r.zoneName; case "a": return a(); case "d": return d ? i({ day: "numeric" }, "day") : l.num(r.day); case "dd": return d ? i({ day: "2-digit" }, "day") : l.num(r.day, 2); case "c": return l.num(r.weekday); case "ccc": return s("short", !0); case "cccc": return s("long", !0); case "ccccc": return s("narrow", !0); case "E": return l.num(r.weekday); case "EEE": return s("short", !1); case "EEEE": return s("long", !1); case "EEEEE": return s("narrow", !1); case "L": return d ? i({ month: "numeric", day: "numeric" }, "month") : l.num(r.month); case "LL": return d ? i({ month: "2-digit", day: "numeric" }, "month") : l.num(r.month, 2); case "LLL": return u("short", !0); case "LLLL": return u("long", !0); case "LLLLL": return u("narrow", !0); case "M": return d ? i({ month: "numeric" }, "month") : l.num(r.month); case "MM": return d ? i({ month: "2-digit" }, "month") : l.num(r.month, 2); case "MMM": return u("short", !1); case "MMMM": return u("long", !1); case "MMMMM": return u("narrow", !1); case "y": return d ? i({ year: "numeric" }, "year") : l.num(r.year); case "yy": return d ? i({ year: "2-digit" }, "year") : l.num(r.year.toString().slice(-2), 2); case "yyyy": return d ? i({ year: "numeric" }, "year") : l.num(r.year, 4); case "yyyyyy": return d ? i({ year: "numeric" }, "year") : l.num(r.year, 6); case "G": return c("short"); case "GG": return c("long"); case "GGGGG": return c("narrow"); case "kk": return l.num(r.weekYear.toString().slice(-2), 2); case "kkkk": return l.num(r.weekYear, 4); case "W": return l.num(r.weekNumber); case "WW": return l.num(r.weekNumber, 2); case "o": return l.num(r.ordinal); case "ooo": return l.num(r.ordinal, 3); case "q": return l.num(r.quarter); case "qq": return l.num(r.quarter, 2); case "X": return l.num(Math.floor(r.ts / 1e3)); case "x": return l.num(r.ts); default: return (n = h.macroTokenToFormatOpts(t = e)) ? l.formatWithSystemDefault(r, n) : t }var t, n }) }, e.formatDurationFromString = function (e, t) { function n(e) { switch (e[0]) { case "S": return "millisecond"; case "s": return "second"; case "m": return "minute"; case "h": return "hour"; case "d": return "day"; case "M": return "month"; case "y": return "year"; default: return null } } var r, i = this, o = h.parseFormat(t), a = o.reduce(function (e, t) { var n = t.literal, r = t.val; return n ? e : e.concat(r) }, []), u = e.shiftTo.apply(e, a.map(n).filter(function (e) { return e })); return Ie(o, (r = u, function (e) { var t = n(e); return t ? i.num(r.get(t), e.length) : e })) }, h }(), xe = function () { function e(e, t) { this.reason = e, this.explanation = t } return e.prototype.toMessage = function () { return this.explanation ? this.reason + ": " + this.explanation : this.reason }, e }(), Fe = function () { function e() { } var t = e.prototype; return t.offsetName = function () { throw new y }, t.formatOffset = function () { throw new y }, t.offset = function () { throw new y }, t.equals = function () { throw new y }, i(e, [{ key: "type", get: function () { throw new y } }, { key: "name", get: function () { throw new y } }, { key: "universal", get: function () { throw new y } }, { key: "isValid", get: function () { throw new y } }]), e }(), Ze = null, Ce = function (e) { function t() { return e.apply(this, arguments) || this } o(t, e); var n = t.prototype; return n.offsetName = function (e, t) { return se(e, t.format, t.locale) }, n.formatOffset = function (e, t) { return de(this.offset(e), t) }, n.offset = function (e) { return -new Date(e).getTimezoneOffset() }, n.equals = function (e) { return "local" === e.type }, i(t, [{ key: "type", get: function () { return "local" } }, { key: "name", get: function () { return I() ? (new Intl.DateTimeFormat).resolvedOptions().timeZone : "local" } }, { key: "universal", get: function () { return !1 } }, { key: "isValid", get: function () { return !0 } }], [{ key: "instance", get: function () { return null === Ze && (Ze = new t), Ze } }]), t }(Fe), je = RegExp("^" + me.source + "$"), Ae = {}; var ze = { year: 0, month: 1, day: 2, hour: 3, minute: 4, second: 5 }; var _e = {}, qe = function (n) { function r(e) { var t = n.call(this) || this; return t.zoneName = e, t.valid = r.isValidZone(e), t } o(r, n), r.create = function (e) { return _e[e] || (_e[e] = new r(e)), _e[e] }, r.resetCache = function () { _e = {}, Ae = {} }, r.isValidSpecifier = function (e) { return !(!e || !e.match(je)) }, r.isValidZone = function (e) { try { return new Intl.DateTimeFormat("en-US", { timeZone: e }).format(), !0 } catch (e) { return !1 } }, r.parseGMTOffset = function (e) { if (e) { var t = e.match(/^Etc\/GMT([+-]\d{1,2})$/i); if (t) return -60 * parseInt(t[1]) } return null }; var e = r.prototype; return e.offsetName = function (e, t) { return se(e, t.format, t.locale, this.name) }, e.formatOffset = function (e, t) { return de(this.offset(e), t) }, e.offset = function (e) { var t, n, r, i, o, a, u = new Date(e), s = (a = this.name, Ae[a] || (Ae[a] = new Intl.DateTimeFormat("en-US", { hour12: !1, timeZone: a, year: "numeric", month: "2-digit", day: "2-digit", hour: "2-digit", minute: "2-digit", second: "2-digit" })), Ae[a]), c = s.formatToParts ? function (e, t) { for (var n = e.formatToParts(t), r = [], i = 0; i < n.length; i++) { var o = n[i], a = o.type, u = o.value, s = ze[a]; R(s) || (r[s] = parseInt(u, 10)) } return r }(s, u) : (t = u, n = s.format(t).replace(/\u200E/g, ""), r = /(\d+)\/(\d+)\/(\d+),? (\d+):(\d+):(\d+)/.exec(n), i = r[1], o = r[2], [r[3], i, o, r[4], r[5], r[6]]), l = c[0], f = c[1], d = c[2], h = c[3], m = +u, y = m % 1e3; return (oe({ year: l, month: f, day: d, hour: 24 === h ? 0 : h, minute: c[4], second: c[5], millisecond: 0 }) - (m -= 0 <= y ? y : 1e3 + y)) / 6e4 }, e.equals = function (e) { return "iana" === e.type && e.name === this.name }, i(r, [{ key: "type", get: function () { return "iana" } }, { key: "name", get: function () { return this.zoneName } }, { key: "universal", get: function () { return !1 } }, { key: "isValid", get: function () { return this.valid } }]), r }(Fe), He = null, Ue = function (n) { function r(e) { var t = n.call(this) || this; return t.fixed = e, t } o(r, n), r.instance = function (e) { return 0 === e ? r.utcInstance : new r(e) }, r.parseSpecifier = function (e) { if (e) { var t = e.match(/^utc(?:([+-]\d{1,2})(?::(\d{2}))?)?$/i); if (t) return new r(ce(t[1], t[2])) } return null }, i(r, null, [{ key: "utcInstance", get: function () { return null === He && (He = new r(0)), He } }]); var e = r.prototype; return e.offsetName = function () { return this.name }, e.formatOffset = function (e, t) { return de(this.fixed, t) }, e.offset = function () { return this.fixed }, e.equals = function (e) { return "fixed" === e.type && e.fixed === this.fixed }, i(r, [{ key: "type", get: function () { return "fixed" } }, { key: "name", get: function () { return 0 === this.fixed ? "UTC" : "UTC" + de(this.fixed, "narrow") } }, { key: "universal", get: function () { return !0 } }, { key: "isValid", get: function () { return !0 } }]), r }(Fe), Re = function (n) { function e(e) { var t = n.call(this) || this; return t.zoneName = e, t } o(e, n); var t = e.prototype; return t.offsetName = function () { return null }, t.formatOffset = function () { return "" }, t.offset = function () { return NaN }, t.equals = function () { return !1 }, i(e, [{ key: "type", get: function () { return "invalid" } }, { key: "name", get: function () { return this.zoneName } }, { key: "universal", get: function () { return !1 } }, { key: "isValid", get: function () { return !1 } }]), e }(Fe); function We(e, t) { var n; if (R(e) || null === e) return t; if (e instanceof Fe) return e; if ("string" != typeof e) return W(e) ? Ue.instance(e) : "object" == typeof e && e.offset && "number" == typeof e.offset ? e : new Re(e); var r = e.toLowerCase(); return "local" === r ? t : "utc" === r || "gmt" === r ? Ue.utcInstance : null != (n = qe.parseGMTOffset(e)) ? Ue.instance(n) : qe.isValidSpecifier(r) ? qe.create(e) : Ue.parseSpecifier(r) || new Re(e) } var Pe = function () { return Date.now() }, Je = null, Ye = null, Ge = null, $e = null, Be = !1, Qe = function () { function e() { } return e.resetCaches = function () { st.resetCache(), qe.resetCache() }, i(e, null, [{ key: "now", get: function () { return Pe }, set: function (e) { Pe = e } }, { key: "defaultZoneName", get: function () { return e.defaultZone.name }, set: function (e) { Je = e ? We(e) : null } }, { key: "defaultZone", get: function () { return Je || Ce.instance } }, { key: "defaultLocale", get: function () { return Ye }, set: function (e) { Ye = e } }, { key: "defaultNumberingSystem", get: function () { return Ge }, set: function (e) { Ge = e } }, { key: "defaultOutputCalendar", get: function () { return $e }, set: function (e) { $e = e } }, { key: "throwOnInvalid", get: function () { return Be }, set: function (e) { Be = e } }]), e }(), Ke = {}; function Xe(e, t) { void 0 === t && (t = {}); var n = JSON.stringify([e, t]), r = Ke[n]; return r || (r = new Intl.DateTimeFormat(e, t), Ke[n] = r), r } var et = {}; var tt = {}; function nt(e, t) { void 0 === t && (t = {}); t.base; var n = function (e, t) { if (null == e) return {}; for (var n, r = {}, i = Object.keys(e), o = 0; o < i.length; o++)n = i[o], 0 <= t.indexOf(n) || (r[n] = e[n]); return r }(t, ["base"]), r = JSON.stringify([e, n]), i = tt[r]; return i || (i = new Intl.RelativeTimeFormat(e, t), tt[r] = i), i } var rt = null; function it(e, t, n, r, i) { var o = e.listingMode(n); return "error" === o ? null : ("en" === o ? r : i)(t) } var ot = function () { function e(e, t, n) { var r; this.padTo = n.padTo || 0, this.floor = n.floor || !1, !t && I() && (r = { useGrouping: !1 }, 0 < n.padTo && (r.minimumIntegerDigits = n.padTo), this.inf = function (e, t) { void 0 === t && (t = {}); var n = JSON.stringify([e, t]), r = et[n]; return r || (r = new Intl.NumberFormat(e, t), et[n] = r), r }(e, r)) } return e.prototype.format = function (e) { if (this.inf) { var t = this.floor ? Math.floor(e) : e; return this.inf.format(t) } return K(this.floor ? Math.floor(e) : te(e, 3), this.padTo) }, e }(), at = function () { function e(e, t, n) { var r, i; this.opts = n, this.hasIntl = I(), e.zone.universal && this.hasIntl ? (r = "UTC", n.timeZoneName ? this.dt = e : this.dt = 0 === e.offset ? e : ur.fromMillis(e.ts + 60 * e.offset * 1e3)) : "local" === e.zone.type ? this.dt = e : r = (this.dt = e).zone.name, this.hasIntl && (i = Object.assign({}, this.opts), r && (i.timeZone = r), this.dtf = Xe(t, i)) } var t = e.prototype; return t.format = function () { if (this.hasIntl) return this.dtf.format(this.dt.toJSDate()); var e = function (e) { var t = "EEEE, LLLL d, yyyy, h:mm a"; switch (ye($(e, ["weekday", "era", "year", "month", "day", "hour", "minute", "second", "timeZoneName", "hour12"]))) { case ye(w): return "M/d/yyyy"; case ye(k): return "LLL d, yyyy"; case ye(b): return "LLLL d, yyyy"; case ye(O): return "EEEE, LLLL d, yyyy"; case ye(S): return "h:mm a"; case ye(T): return "h:mm:ss a"; case ye(M): case ye(N): return "h:mm a"; case ye(D): return "HH:mm"; case ye(E): return "HH:mm:ss"; case ye(x): case ye(F): return "HH:mm"; case ye(Z): return "M/d/yyyy, h:mm a"; case ye(j): return "LLL d, yyyy, h:mm a"; case ye(_): return "LLLL d, yyyy, h:mm a"; case ye(H): return t; case ye(C): return "M/d/yyyy, h:mm:ss a"; case ye(A): return "LLL d, yyyy, h:mm:ss a"; case ye(z): return "EEE, d LLL yyyy, h:mm a"; case ye(q): return "LLLL d, yyyy, h:mm:ss a"; case ye(U): return "EEEE, LLLL d, yyyy, h:mm:ss a"; default: return t } }(this.opts), t = st.create("en-US"); return Le.create(t).formatDateTimeFromString(this.dt, e) }, t.formatToParts = function () { return this.hasIntl && J() ? this.dtf.formatToParts(this.dt.toJSDate()) : [] }, t.resolvedOptions = function () { return this.hasIntl ? this.dtf.resolvedOptions() : { locale: "en-US", numberingSystem: "latn", outputCalendar: "gregory" } }, e }(), ut = function () { function e(e, t, n) { this.opts = Object.assign({ style: "long" }, n), !t && Y() && (this.rtf = nt(e, n)) } var t = e.prototype; return t.format = function (e, t) { return this.rtf ? this.rtf.format(e, t) : function (e, t, n, r) { void 0 === n && (n = "always"), void 0 === r && (r = !1); var i = { years: ["year", "yr."], quarters: ["quarter", "qtr."], months: ["month", "mo."], weeks: ["week", "wk."], days: ["day", "day", "days"], hours: ["hour", "hr."], minutes: ["minute", "min."], seconds: ["second", "sec."] }, o = -1 === ["hours", "minutes", "seconds"].indexOf(e); if ("auto" === n && o) { var a = "days" === e; switch (t) { case 1: return a ? "tomorrow" : "next " + i[e][0]; case -1: return a ? "yesterday" : "last " + i[e][0]; case 0: return a ? "today" : "this " + i[e][0] } } var u = Object.is(t, -0) || t < 0, s = Math.abs(t), c = 1 === s, l = i[e], f = r ? !c && l[2] || l[1] : c ? i[e][0] : e; return u ? s + " " + f + " ago" : "in " + s + " " + f }(t, e, this.opts.numeric, "long" !== this.opts.style) }, t.formatToParts = function (e, t) { return this.rtf ? this.rtf.formatToParts(e, t) : [] }, e }(), st = function () { function o(e, t, n, r) { var i, o, a, u = function (e) { var t = e.indexOf("-u-"); if (-1 === t) return [e]; var n, r = e.substring(0, t); try { n = Xe(e).resolvedOptions() } catch (e) { n = Xe(r).resolvedOptions() } return [r, n.numberingSystem, n.calendar] }(e), s = u[0], c = u[1], l = u[2]; this.locale = s, this.numberingSystem = t || c || null, this.outputCalendar = n || l || null, this.intl = (i = this.locale, o = this.numberingSystem, a = this.outputCalendar, I() ? ((a || o) && (i += "-u", a && (i += "-ca-" + a), o && (i += "-nu-" + o)), i) : []), this.weekdaysCache = { format: {}, standalone: {} }, this.monthsCache = { format: {}, standalone: {} }, this.meridiemCache = null, this.eraCache = {}, this.specifiedLocale = r, this.fastNumbersCached = null } o.fromOpts = function (e) { return o.create(e.locale, e.numberingSystem, e.outputCalendar, e.defaultToEN) }, o.create = function (e, t, n, r) { void 0 === r && (r = !1); var i = e || Qe.defaultLocale; return new o(i || (r ? "en-US" : function () { if (rt) return rt; if (I()) { var e = (new Intl.DateTimeFormat).resolvedOptions().locale; return rt = e && "und" !== e ? e : "en-US" } return rt = "en-US" }()), t || Qe.defaultNumberingSystem, n || Qe.defaultOutputCalendar, i) }, o.resetCache = function () { rt = null, Ke = {}, et = {}, tt = {} }, o.fromObject = function (e) { var t = void 0 === e ? {} : e, n = t.locale, r = t.numberingSystem, i = t.outputCalendar; return o.create(n, r, i) }; var e = o.prototype; return e.listingMode = function (e) { void 0 === e && (e = !0); var t = I() && J(), n = this.isEnglish(), r = !(null !== this.numberingSystem && "latn" !== this.numberingSystem || null !== this.outputCalendar && "gregory" !== this.outputCalendar); return t || n && r || e ? !t || n && r ? "en" : "intl" : "error" }, e.clone = function (e) { return e && 0 !== Object.getOwnPropertyNames(e).length ? o.create(e.locale || this.specifiedLocale, e.numberingSystem || this.numberingSystem, e.outputCalendar || this.outputCalendar, e.defaultToEN || !1) : this }, e.redefaultToEN = function (e) { return void 0 === e && (e = {}), this.clone(Object.assign({}, e, { defaultToEN: !0 })) }, e.redefaultToSystem = function (e) { return void 0 === e && (e = {}), this.clone(Object.assign({}, e, { defaultToEN: !1 })) }, e.months = function (n, r, e) { var i = this; return void 0 === r && (r = !1), void 0 === e && (e = !0), it(this, n, e, we, function () { var t = r ? { month: n, day: "numeric" } : { month: n }, e = r ? "format" : "standalone"; return i.monthsCache[e][n] || (i.monthsCache[e][n] = function (e) { for (var t = [], n = 1; n <= 12; n++) { var r = ur.utc(2016, n, 1); t.push(e(r)) } return t }(function (e) { return i.extract(e, t, "month") })), i.monthsCache[e][n] }) }, e.weekdays = function (n, r, e) { var i = this; return void 0 === r && (r = !1), void 0 === e && (e = !0), it(this, n, e, Se, function () { var t = r ? { weekday: n, year: "numeric", month: "long", day: "numeric" } : { weekday: n }, e = r ? "format" : "standalone"; return i.weekdaysCache[e][n] || (i.weekdaysCache[e][n] = function (e) { for (var t = [], n = 1; n <= 7; n++) { var r = ur.utc(2016, 11, 13 + n); t.push(e(r)) } return t }(function (e) { return i.extract(e, t, "weekday") })), i.weekdaysCache[e][n] }) }, e.meridiems = function (e) { var n = this; return void 0 === e && (e = !0), it(this, void 0, e, function () { return Te }, function () { var t; return n.meridiemCache || (t = { hour: "numeric", hour12: !0 }, n.meridiemCache = [ur.utc(2016, 11, 13, 9), ur.utc(2016, 11, 13, 19)].map(function (e) { return n.extract(e, t, "dayperiod") })), n.meridiemCache }) }, e.eras = function (e, t) { var n = this; return void 0 === t && (t = !0), it(this, e, t, Ee, function () { var t = { era: e }; return n.eraCache[e] || (n.eraCache[e] = [ur.utc(-40, 1, 1), ur.utc(2017, 1, 1)].map(function (e) { return n.extract(e, t, "era") })), n.eraCache[e] }) }, e.extract = function (e, t, n) { var r = this.dtFormatter(e, t).formatToParts().find(function (e) { return e.type.toLowerCase() === n }); return r ? r.value : null }, e.numberFormatter = function (e) { return void 0 === e && (e = {}), new ot(this.intl, e.forceSimple || this.fastNumbers, e) }, e.dtFormatter = function (e, t) { return void 0 === t && (t = {}), new at(e, this.intl, t) }, e.relFormatter = function (e) { return void 0 === e && (e = {}), new ut(this.intl, this.isEnglish(), e) }, e.isEnglish = function () { return "en" === this.locale || "en-us" === this.locale.toLowerCase() || I() && new Intl.DateTimeFormat(this.intl).resolvedOptions().locale.startsWith("en-us") }, e.equals = function (e) { return this.locale === e.locale && this.numberingSystem === e.numberingSystem && this.outputCalendar === e.outputCalendar }, i(o, [{ key: "fastNumbers", get: function () { var e; return null == this.fastNumbersCached && (this.fastNumbersCached = (!(e = this).numberingSystem || "latn" === e.numberingSystem) && ("latn" === e.numberingSystem || !e.locale || e.locale.startsWith("en") || I() && "latn" === new Intl.DateTimeFormat(e.intl).resolvedOptions().numberingSystem)), this.fastNumbersCached } }]), o }(); function ct() { for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++)t[n] = arguments[n]; var r = t.reduce(function (e, t) { return e + t.source }, ""); return RegExp("^" + r + "$") } function lt() { for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++)t[n] = arguments[n]; return function (c) { return t.reduce(function (e, t) { var n = e[0], r = e[1], i = e[2], o = t(c, i), a = o[0], u = o[1], s = o[2]; return [Object.assign(n, a), r || u, s] }, [{}, null, 1]).slice(0, 2) } } function ft(e) { if (null == e) return [null, null]; for (var t = arguments.length, n = new Array(1 < t ? t - 1 : 0), r = 1; r < t; r++)n[r - 1] = arguments[r]; for (var i = 0, o = n; i < o.length; i++) { var a = o[i], u = a[0], s = a[1], c = u.exec(e); if (c) return s(c) } return [null, null] } function dt() { for (var e = arguments.length, i = new Array(e), t = 0; t < e; t++)i[t] = arguments[t]; return function (e, t) { for (var n = {}, r = 0; r < i.length; r++)n[i[r]] = X(e[t + r]); return [n, null, t + r] } } var ht = /(?:(Z)|([+-]\d\d)(?::?(\d\d))?)/, mt = /(\d\d)(?::?(\d\d)(?::?(\d\d)(?:[.,](\d{1,9}))?)?)?/, yt = RegExp("" + mt.source + ht.source + "?"), vt = RegExp("(?:T" + yt.source + ")?"), gt = dt("weekYear", "weekNumber", "weekDay"), pt = dt("year", "ordinal"), wt = RegExp(mt.source + " ?(?:" + ht.source + "|(" + me.source + "))?"), kt = RegExp("(?: " + wt.source + ")?"); function bt(e, t, n) { var r = e[t]; return R(r) ? n : X(r) } function Ot(e, t) { return [{ year: bt(e, t), month: bt(e, t + 1, 1), day: bt(e, t + 2, 1) }, null, t + 3] } function St(e, t) { return [{ hour: bt(e, t, 0), minute: bt(e, t + 1, 0), second: bt(e, t + 2, 0), millisecond: ee(e[t + 3]) }, null, t + 4] } function Tt(e, t) { var n = !e[t] && !e[t + 1], r = ce(e[t + 1], e[t + 2]); return [{}, n ? null : Ue.instance(r), t + 3] } function Mt(e, t) { return [{}, e[t] ? qe.create(e[t]) : null, t + 1] } var Nt = /^-?P(?:(?:(-?\d{1,9})Y)?(?:(-?\d{1,9})M)?(?:(-?\d{1,9})W)?(?:(-?\d{1,9})D)?(?:T(?:(-?\d{1,9})H)?(?:(-?\d{1,9})M)?(?:(-?\d{1,9})(?:[.,](-?\d{1,9}))?S)?)?)$/; function Dt(e) { function t(e) { return e && f ? -e : e } var n = e[0], r = e[1], i = e[2], o = e[3], a = e[4], u = e[5], s = e[6], c = e[7], l = e[8], f = "-" === n[0]; return [{ years: t(X(r)), months: t(X(i)), weeks: t(X(o)), days: t(X(a)), hours: t(X(u)), minutes: t(X(s)), seconds: t(X(c)), milliseconds: t(ee(l)) }] } var Et = { GMT: 0, EDT: -240, EST: -300, CDT: -300, CST: -360, MDT: -360, MST: -420, PDT: -420, PST: -480 }; function It(e, t, n, r, i, o, a) { var u = { year: 2 === t.length ? ue(X(t)) : X(t), month: ge.indexOf(n) + 1, day: X(r), hour: X(i), minute: X(o) }; return a && (u.second = X(a)), e && (u.weekday = 3 < e.length ? ke.indexOf(e) + 1 : be.indexOf(e) + 1), u } var Vt = /^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),\s)?(\d{1,2})\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{2,4})\s(\d\d):(\d\d)(?::(\d\d))?\s(?:(UT|GMT|[ECMP][SD]T)|([Zz])|(?:([+-]\d\d)(\d\d)))$/; function Lt(e) { var t = e[1], n = e[2], r = e[3], i = e[4], o = e[5], a = e[6], u = e[7], s = e[8], c = e[9], l = e[10], f = e[11], d = It(t, i, r, n, o, a, u), h = s ? Et[s] : c ? 0 : ce(l, f); return [d, new Ue(h)] } var xt = /^(Mon|Tue|Wed|Thu|Fri|Sat|Sun), (\d\d) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (\d{4}) (\d\d):(\d\d):(\d\d) GMT$/, Ft = /^(Monday|Tuesday|Wedsday|Thursday|Friday|Saturday|Sunday), (\d\d)-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-(\d\d) (\d\d):(\d\d):(\d\d) GMT$/, Zt = /^(Mon|Tue|Wed|Thu|Fri|Sat|Sun) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ( \d|\d\d) (\d\d):(\d\d):(\d\d) (\d{4})$/; function Ct(e) { var t = e[1], n = e[2], r = e[3]; return [It(t, e[4], r, n, e[5], e[6], e[7]), Ue.utcInstance] } function jt(e) { var t = e[1], n = e[2], r = e[3], i = e[4], o = e[5], a = e[6]; return [It(t, e[7], n, r, i, o, a), Ue.utcInstance] } var At = ct(/([+-]\d{6}|\d{4})(?:-?(\d\d)(?:-?(\d\d))?)?/, vt), zt = ct(/(\d{4})-?W(\d\d)(?:-?(\d))?/, vt), _t = ct(/(\d{4})-?(\d{3})/, vt), qt = ct(yt), Ht = lt(Ot, St, Tt), Ut = lt(gt, St, Tt), Rt = lt(pt, St), Wt = lt(St, Tt); var Pt = ct(/(\d{4})-(\d\d)-(\d\d)/, kt), Jt = ct(wt), Yt = lt(Ot, St, Tt, Mt), Gt = lt(St, Tt, Mt); var $t = { weeks: { days: 7, hours: 168, minutes: 10080, seconds: 604800, milliseconds: 6048e5 }, days: { hours: 24, minutes: 1440, seconds: 86400, milliseconds: 864e5 }, hours: { minutes: 60, seconds: 3600, milliseconds: 36e5 }, minutes: { seconds: 60, milliseconds: 6e4 }, seconds: { milliseconds: 1e3 } }, Bt = Object.assign({ years: { months: 12, weeks: 52, days: 365, hours: 8760, minutes: 525600, seconds: 31536e3, milliseconds: 31536e6 }, quarters: { months: 3, weeks: 13, days: 91, hours: 2184, minutes: 131040, milliseconds: 78624e5 }, months: { weeks: 4, days: 30, hours: 720, minutes: 43200, seconds: 2592e3, milliseconds: 2592e6 } }, $t), Qt = 365.2425, Kt = 30.436875, Xt = Object.assign({ years: { months: 12, weeks: Qt / 7, days: Qt, hours: 24 * Qt, minutes: 525949.2, seconds: 525949.2 * 60, milliseconds: 525949.2 * 60 * 1e3 }, quarters: { months: 3, weeks: Qt / 28, days: Qt / 4, hours: 24 * Qt / 4, minutes: 131487.3, seconds: 525949.2 * 60 / 4, milliseconds: 7889237999.999999 }, months: { weeks: Kt / 7, days: Kt, hours: 24 * Kt, minutes: 43829.1, seconds: 2629746, milliseconds: 2629746e3 } }, $t), en = ["years", "quarters", "months", "weeks", "days", "hours", "minutes", "seconds", "milliseconds"], tn = en.slice(0).reverse(); function nn(e, t, n) { void 0 === n && (n = !1); var r = { values: n ? t.values : Object.assign({}, e.values, t.values || {}), loc: e.loc.clone(t.loc), conversionAccuracy: t.conversionAccuracy || e.conversionAccuracy }; return new an(r) } function rn(e, t, n, r, i) { var o, a = e[i][n], u = t[n] / a, s = !(Math.sign(u) === Math.sign(r[i])) && 0 !== r[i] && Math.abs(u) <= 1 ? (o = u) < 0 ? Math.floor(o) : Math.ceil(o) : Math.trunc(u); r[i] += s, t[n] -= s * a } function on(n, r) { tn.reduce(function (e, t) { return R(r[t]) ? e : (e && rn(n, r, e, r, t), t) }, null) } var an = function () { function y(e) { var t = "longterm" === e.conversionAccuracy || !1; this.values = e.values, this.loc = e.loc || st.create(), this.conversionAccuracy = t ? "longterm" : "casual", this.invalid = e.invalid || null, this.matrix = t ? Xt : Bt, this.isLuxonDuration = !0 } y.fromMillis = function (e, t) { return y.fromObject(Object.assign({ milliseconds: e }, t)) }, y.fromObject = function (e) { if (null == e || "object" != typeof e) throw new m("Duration.fromObject: argument expected to be an object, got " + (null === e ? "null" : typeof e)); return new y({ values: fe(e, y.normalizeUnit, ["locale", "numberingSystem", "conversionAccuracy", "zone"]), loc: st.fromObject(e), conversionAccuracy: e.conversionAccuracy }) }, y.fromISO = function (e, t) { var n = ft(e, [Nt, Dt])[0]; if (n) { var r = Object.assign(n, t); return y.fromObject(r) } return y.invalid("unparsable", 'the input "' + e + "\" can't be parsed as ISO 8601") }, y.invalid = function (e, t) { if (void 0 === t && (t = null), !e) throw new m("need to specify a reason the Duration is invalid"); var n = e instanceof xe ? e : new xe(e, t); if (Qe.throwOnInvalid) throw new d(n); return new y({ invalid: n }) }, y.normalizeUnit = function (e) { var t = { year: "years", years: "years", quarter: "quarters", quarters: "quarters", month: "months", months: "months", week: "weeks", weeks: "weeks", day: "days", days: "days", hour: "hours", hours: "hours", minute: "minutes", minutes: "minutes", second: "seconds", seconds: "seconds", millisecond: "milliseconds", milliseconds: "milliseconds" }[e ? e.toLowerCase() : e]; if (!t) throw new h(e); return t }, y.isDuration = function (e) { return e && e.isLuxonDuration || !1 }; var e = y.prototype; return e.toFormat = function (e, t) { void 0 === t && (t = {}); var n = Object.assign({}, t, { floor: !1 !== t.round && !1 !== t.floor }); return this.isValid ? Le.create(this.loc, n).formatDurationFromString(this, e) : "Invalid Duration" }, e.toObject = function (e) { if (void 0 === e && (e = {}), !this.isValid) return {}; var t = Object.assign({}, this.values); return e.includeConfig && (t.conversionAccuracy = this.conversionAccuracy, t.numberingSystem = this.loc.numberingSystem, t.locale = this.loc.locale), t }, e.toISO = function () { if (!this.isValid) return null; var e = "P"; return 0 !== this.years && (e += this.years + "Y"), 0 === this.months && 0 === this.quarters || (e += this.months + 3 * this.quarters + "M"), 0 !== this.weeks && (e += this.weeks + "W"), 0 !== this.days && (e += this.days + "D"), 0 === this.hours && 0 === this.minutes && 0 === this.seconds && 0 === this.milliseconds || (e += "T"), 0 !== this.hours && (e += this.hours + "H"), 0 !== this.minutes && (e += this.minutes + "M"), 0 === this.seconds && 0 === this.milliseconds || (e += te(this.seconds + this.milliseconds / 1e3, 3) + "S"), "P" === e && (e += "T0S"), e }, e.toJSON = function () { return this.toISO() }, e.toString = function () { return this.toISO() }, e.valueOf = function () { return this.as("milliseconds") }, e.plus = function (e) { if (!this.isValid) return this; for (var t, n = un(e), r = {}, i = V(en); !(t = i()).done;) { var o = t.value; (B(n.values, o) || B(this.values, o)) && (r[o] = n.get(o) + this.get(o)) } return nn(this, { values: r }, !0) }, e.minus = function (e) { if (!this.isValid) return this; var t = un(e); return this.plus(t.negate()) }, e.mapUnits = function (e) { if (!this.isValid) return this; for (var t = {}, n = 0, r = Object.keys(this.values); n < r.length; n++) { var i = r[n]; t[i] = le(e(this.values[i], i)) } return nn(this, { values: t }, !0) }, e.get = function (e) { return this[y.normalizeUnit(e)] }, e.set = function (e) { return this.isValid ? nn(this, { values: Object.assign(this.values, fe(e, y.normalizeUnit, [])) }) : this }, e.reconfigure = function (e) { var t = void 0 === e ? {} : e, n = t.locale, r = t.numberingSystem, i = t.conversionAccuracy, o = { loc: this.loc.clone({ locale: n, numberingSystem: r }) }; return i && (o.conversionAccuracy = i), nn(this, o) }, e.as = function (e) { return this.isValid ? this.shiftTo(e).get(e) : NaN }, e.normalize = function () { if (!this.isValid) return this; var e = this.toObject(); return on(this.matrix, e), nn(this, { values: e }, !0) }, e.shiftTo = function () { for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++)t[n] = arguments[n]; if (!this.isValid) return this; if (0 === t.length) return this; t = t.map(function (e) { return y.normalizeUnit(e) }); var r, i = {}, o = {}, a = this.toObject(); on(this.matrix, a); for (var u, s = V(en); !(u = s()).done;) { var c = u.value; if (0 <= t.indexOf(c)) { r = c; var l = 0; for (var f in o) l += this.matrix[f][c] * o[f], o[f] = 0; W(a[c]) && (l += a[c]); var d = Math.trunc(l); for (var h in i[c] = d, o[c] = l - d, a) en.indexOf(h) > en.indexOf(c) && rn(this.matrix, a, h, i, c) } else W(a[c]) && (o[c] = a[c]) } for (var m in o) 0 !== o[m] && (i[r] += m === r ? o[m] : o[m] / this.matrix[r][m]); return nn(this, { values: i }, !0).normalize() }, e.negate = function () { if (!this.isValid) return this; for (var e = {}, t = 0, n = Object.keys(this.values); t < n.length; t++) { var r = n[t]; e[r] = -this.values[r] } return nn(this, { values: e }, !0) }, e.equals = function (e) { if (!this.isValid || !e.isValid) return !1; if (!this.loc.equals(e.loc)) return !1; for (var t, n = V(en); !(t = n()).done;) { var r = t.value; if (this.values[r] !== e.values[r]) return !1 } return !0 }, i(y, [{ key: "locale", get: function () { return this.isValid ? this.loc.locale : null } }, { key: "numberingSystem", get: function () { return this.isValid ? this.loc.numberingSystem : null } }, { key: "years", get: function () { return this.isValid ? this.values.years || 0 : NaN } }, { key: "quarters", get: function () { return this.isValid ? this.values.quarters || 0 : NaN } }, { key: "months", get: function () { return this.isValid ? this.values.months || 0 : NaN } }, { key: "weeks", get: function () { return this.isValid ? this.values.weeks || 0 : NaN } }, { key: "days", get: function () { return this.isValid ? this.values.days || 0 : NaN } }, { key: "hours", get: function () { return this.isValid ? this.values.hours || 0 : NaN } }, { key: "minutes", get: function () { return this.isValid ? this.values.minutes || 0 : NaN } }, { key: "seconds", get: function () { return this.isValid ? this.values.seconds || 0 : NaN } }, { key: "milliseconds", get: function () { return this.isValid ? this.values.milliseconds || 0 : NaN } }, { key: "isValid", get: function () { return null === this.invalid } }, { key: "invalidReason", get: function () { return this.invalid ? this.invalid.reason : null } }, { key: "invalidExplanation", get: function () { return this.invalid ? this.invalid.explanation : null } }]), y }(); function un(e) { if (W(e)) return an.fromMillis(e); if (an.isDuration(e)) return e; if ("object" == typeof e) return an.fromObject(e); throw new m("Unknown duration argument " + e + " of type " + typeof e) } var sn = "Invalid Interval"; var cn = function () { function l(e) { this.s = e.start, this.e = e.end, this.invalid = e.invalid || null, this.isLuxonInterval = !0 } l.invalid = function (e, t) { if (void 0 === t && (t = null), !e) throw new m("need to specify a reason the Interval is invalid"); var n = e instanceof xe ? e : new xe(e, t); if (Qe.throwOnInvalid) throw new f(n); return new l({ invalid: n }) }, l.fromDateTimes = function (e, t) { var n, r, i = sr(e), o = sr(t), a = (r = o, (n = i) && n.isValid ? r && r.isValid ? r < n ? cn.invalid("end before start", "The end of an interval must be after its start, but you had start=" + n.toISO() + " and end=" + r.toISO()) : null : cn.invalid("missing or invalid end") : cn.invalid("missing or invalid start")); return null == a ? new l({ start: i, end: o }) : a }, l.after = function (e, t) { var n = un(t), r = sr(e); return l.fromDateTimes(r, r.plus(n)) }, l.before = function (e, t) { var n = un(t), r = sr(e); return l.fromDateTimes(r.minus(n), r) }, l.fromISO = function (e, t) { var n = (e || "").split("/", 2), r = n[0], i = n[1]; if (r && i) { var o = ur.fromISO(r, t), a = ur.fromISO(i, t); if (o.isValid && a.isValid) return l.fromDateTimes(o, a); if (o.isValid) { var u = an.fromISO(i, t); if (u.isValid) return l.after(o, u) } else if (a.isValid) { var s = an.fromISO(r, t); if (s.isValid) return l.before(a, s) } } return l.invalid("unparsable", 'the input "' + e + "\" can't be parsed as ISO 8601") }, l.isInterval = function (e) { return e && e.isLuxonInterval || !1 }; var e = l.prototype; return e.length = function (e) { return void 0 === e && (e = "milliseconds"), this.isValid ? this.toDuration.apply(this, [e]).get(e) : NaN }, e.count = function (e) { if (void 0 === e && (e = "milliseconds"), !this.isValid) return NaN; var t = this.start.startOf(e), n = this.end.startOf(e); return Math.floor(n.diff(t, e).get(e)) + 1 }, e.hasSame = function (e) { return !!this.isValid && this.e.minus(1).hasSame(this.s, e) }, e.isEmpty = function () { return this.s.valueOf() === this.e.valueOf() }, e.isAfter = function (e) { return !!this.isValid && this.s > e }, e.isBefore = function (e) { return !!this.isValid && this.e <= e }, e.contains = function (e) { return !!this.isValid && (this.s <= e && this.e > e) }, e.set = function (e) { var t = void 0 === e ? {} : e, n = t.start, r = t.end; return this.isValid ? l.fromDateTimes(n || this.s, r || this.e) : this }, e.splitAt = function () { var t = this; if (!this.isValid) return []; for (var e = arguments.length, n = new Array(e), r = 0; r < e; r++)n[r] = arguments[r]; for (var i = n.map(sr).filter(function (e) { return t.contains(e) }).sort(), o = [], a = this.s, u = 0; a < this.e;) { var s = i[u] || this.e, c = +s > +this.e ? this.e : s; o.push(l.fromDateTimes(a, c)), a = c, u += 1 } return o }, e.splitBy = function (e) { var t = un(e); if (!this.isValid || !t.isValid || 0 === t.as("milliseconds")) return []; for (var n, r, i = this.s, o = []; i < this.e;)r = +(n = i.plus(t)) > +this.e ? this.e : n, o.push(l.fromDateTimes(i, r)), i = r; return o }, e.divideEqually = function (e) { return this.isValid ? this.splitBy(this.length() / e).slice(0, e) : [] }, e.overlaps = function (e) { return this.e > e.s && this.s < e.e }, e.abutsStart = function (e) { return !!this.isValid && +this.e == +e.s }, e.abutsEnd = function (e) { return !!this.isValid && +e.e == +this.s }, e.engulfs = function (e) { return !!this.isValid && (this.s <= e.s && this.e >= e.e) }, e.equals = function (e) { return !(!this.isValid || !e.isValid) && (this.s.equals(e.s) && this.e.equals(e.e)) }, e.intersection = function (e) { if (!this.isValid) return this; var t = this.s > e.s ? this.s : e.s, n = this.e < e.e ? this.e : e.e; return n < t ? null : l.fromDateTimes(t, n) }, e.union = function (e) { if (!this.isValid) return this; var t = this.s < e.s ? this.s : e.s, n = this.e > e.e ? this.e : e.e; return l.fromDateTimes(t, n) }, l.merge = function (e) { var t = e.sort(function (e, t) { return e.s - t.s }).reduce(function (e, t) { var n = e[0], r = e[1]; return r ? r.overlaps(t) || r.abutsStart(t) ? [n, r.union(t)] : [n.concat([r]), t] : [n, t] }, [[], null]), n = t[0], r = t[1]; return r && n.push(r), n }, l.xor = function (e) { for (var t, n, r = null, i = 0, o = [], a = e.map(function (e) { return [{ time: e.s, type: "s" }, { time: e.e, type: "e" }] }), u = V((t = Array.prototype).concat.apply(t, a).sort(function (e, t) { return e.time - t.time })); !(n = u()).done;)var s = n.value, r = 1 === (i += "s" === s.type ? 1 : -1) ? s.time : (r && +r != +s.time && o.push(l.fromDateTimes(r, s.time)), null); return l.merge(o) }, e.difference = function () { for (var t = this, e = arguments.length, n = new Array(e), r = 0; r < e; r++)n[r] = arguments[r]; return l.xor([this].concat(n)).map(function (e) { return t.intersection(e) }).filter(function (e) { return e && !e.isEmpty() }) }, e.toString = function () { return this.isValid ? "[" + this.s.toISO() + " – " + this.e.toISO() + ")" : sn }, e.toISO = function (e) { return this.isValid ? this.s.toISO(e) + "/" + this.e.toISO(e) : sn }, e.toISODate = function () { return this.isValid ? this.s.toISODate() + "/" + this.e.toISODate() : sn }, e.toISOTime = function (e) { return this.isValid ? this.s.toISOTime(e) + "/" + this.e.toISOTime(e) : sn }, e.toFormat = function (e, t) { var n = (void 0 === t ? {} : t).separator, r = void 0 === n ? " – " : n; return this.isValid ? "" + this.s.toFormat(e) + r + this.e.toFormat(e) : sn }, e.toDuration = function (e, t) { return this.isValid ? this.e.diff(this.s, e, t) : an.invalid(this.invalidReason) }, e.mapEndpoints = function (e) { return l.fromDateTimes(e(this.s), e(this.e)) }, i(l, [{ key: "start", get: function () { return this.isValid ? this.s : null } }, { key: "end", get: function () { return this.isValid ? this.e : null } }, { key: "isValid", get: function () { return null === this.invalidReason } }, { key: "invalidReason", get: function () { return this.invalid ? this.invalid.reason : null } }, { key: "invalidExplanation", get: function () { return this.invalid ? this.invalid.explanation : null } }]), l }(), ln = function () { function e() { } return e.hasDST = function (e) { void 0 === e && (e = Qe.defaultZone); var t = ur.local().setZone(e).set({ month: 12 }); return !e.universal && t.offset !== t.set({ month: 6 }).offset }, e.isValidIANAZone = function (e) { return qe.isValidSpecifier(e) && qe.isValidZone(e) }, e.normalizeZone = function (e) { return We(e, Qe.defaultZone) }, e.months = function (e, t) { void 0 === e && (e = "long"); var n = void 0 === t ? {} : t, r = n.locale, i = void 0 === r ? null : r, o = n.numberingSystem, a = void 0 === o ? null : o, u = n.outputCalendar, s = void 0 === u ? "gregory" : u; return st.create(i, a, s).months(e) }, e.monthsFormat = function (e, t) { void 0 === e && (e = "long"); var n = void 0 === t ? {} : t, r = n.locale, i = void 0 === r ? null : r, o = n.numberingSystem, a = void 0 === o ? null : o, u = n.outputCalendar, s = void 0 === u ? "gregory" : u; return st.create(i, a, s).months(e, !0) }, e.weekdays = function (e, t) { void 0 === e && (e = "long"); var n = void 0 === t ? {} : t, r = n.locale, i = void 0 === r ? null : r, o = n.numberingSystem, a = void 0 === o ? null : o; return st.create(i, a, null).weekdays(e) }, e.weekdaysFormat = function (e, t) { void 0 === e && (e = "long"); var n = void 0 === t ? {} : t, r = n.locale, i = void 0 === r ? null : r, o = n.numberingSystem, a = void 0 === o ? null : o; return st.create(i, a, null).weekdays(e, !0) }, e.meridiems = function (e) { var t = (void 0 === e ? {} : e).locale, n = void 0 === t ? null : t; return st.create(n).meridiems() }, e.eras = function (e, t) { void 0 === e && (e = "short"); var n = (void 0 === t ? {} : t).locale, r = void 0 === n ? null : n; return st.create(r, null, "gregory").eras(e) }, e.features = function () { var e = !1, t = !1, n = !1, r = !1; if (I()) { e = !0, t = J(), r = Y(); try { n = "America/New_York" === new Intl.DateTimeFormat("en", { timeZone: "America/New_York" }).resolvedOptions().timeZone } catch (e) { n = !1 } } return { intl: e, intlTokens: t, zones: n, relative: r } }, e }(); function fn(e, t) { function n(e) { return e.toUTC(0, { keepLocalTime: !0 }).startOf("day").valueOf() } var r = n(t) - n(e); return Math.floor(an.fromMillis(r).as("days")) } function dn(e, t, n, r) { var i, o = function (e, t, n) { for (var r = {}, i = 0, o = [["years", function (e, t) { return t.year - e.year }], ["months", function (e, t) { return t.month - e.month + 12 * (t.year - e.year) }], ["weeks", function (e, t) { var n = fn(e, t); return (n - n % 7) / 7 }], ["days", fn]]; i < o.length; i++) { var a, u, s, c, l, f = o[i], d = f[0], h = f[1]; 0 <= n.indexOf(d) && (u = d, s = h(e, t), t < (l = e.plus(((a = {})[d] = s, a))) ? (e = e.plus(((c = {})[d] = s - 1, c)), --s) : e = l, r[d] = s) } return [e, r, l, u] }(e, t, n), a = o[0], u = o[1], s = o[2], c = o[3], l = t - a, f = n.filter(function (e) { return 0 <= ["hours", "minutes", "seconds", "milliseconds"].indexOf(e) }); 0 === f.length && (s < t && (s = a.plus(((i = {})[c] = 1, i))), s !== a && (u[c] = (u[c] || 0) + l / (s - a))); var d, h = an.fromObject(Object.assign(u, r)); return 0 < f.length ? (d = an.fromMillis(l, r)).shiftTo.apply(d, f).plus(h) : h } var hn = { arab: "[٠-٩]", arabext: "[۰-۹]", bali: "[᭐-᭙]", beng: "[০-৯]", deva: "[०-९]", fullwide: "[０-９]", gujr: "[૦-૯]", hanidec: "[〇|一|二|三|四|五|六|七|八|九]", khmr: "[០-៩]", knda: "[೦-೯]", laoo: "[໐-໙]", limb: "[᥆-᥏]", mlym: "[൦-൯]", mong: "[᠐-᠙]", mymr: "[၀-၉]", orya: "[୦-୯]", tamldec: "[௦-௯]", telu: "[౦-౯]", thai: "[๐-๙]", tibt: "[༠-༩]", latn: "\\d" }, mn = { arab: [1632, 1641], arabext: [1776, 1785], bali: [6992, 7001], beng: [2534, 2543], deva: [2406, 2415], fullwide: [65296, 65303], gujr: [2790, 2799], khmr: [6112, 6121], knda: [3302, 3311], laoo: [3792, 3801], limb: [6470, 6479], mlym: [3430, 3439], mong: [6160, 6169], mymr: [4160, 4169], orya: [2918, 2927], tamldec: [3046, 3055], telu: [3174, 3183], thai: [3664, 3673], tibt: [3872, 3881] }, yn = hn.hanidec.replace(/[\[|\]]/g, "").split(""); function vn(e, t) { var n = e.numberingSystem; return void 0 === t && (t = ""), new RegExp("" + hn[n || "latn"] + t) } var gn = "missing Intl.DateTimeFormat.formatToParts support"; function pn(e, n) { return void 0 === n && (n = function (e) { return e }), { regex: e, deser: function (e) { var t = e[0]; return n(function (e) { var t = parseInt(e, 10); if (isNaN(t)) { t = ""; for (var n = 0; n < e.length; n++) { var r = e.charCodeAt(n); if (-1 !== e[n].search(hn.hanidec)) t += yn.indexOf(e[n]); else for (var i in mn) { var o = mn[i], a = o[0], u = o[1]; a <= r && r <= u && (t += r - a) } } return parseInt(t, 10) } return t }(t)) } } } function wn(e) { return e.replace(/\./, "\\.?") } function kn(e) { return e.replace(/\./, "").toLowerCase() } function bn(n, r) { return null === n ? null : { regex: RegExp(n.map(wn).join("|")), deser: function (e) { var t = e[0]; return n.findIndex(function (e) { return kn(t) === kn(e) }) + r } } } function On(e, t) { return { regex: e, deser: function (e) { return ce(e[1], e[2]) }, groups: t } } function Sn(e) { return { regex: e, deser: function (e) { return e[0] } } } function Tn(t, n) { function r(e) { return { regex: RegExp(e.val.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")), deser: function (e) { return e[0] }, literal: !0 } } var i = vn(n), o = vn(n, "{2}"), a = vn(n, "{3}"), u = vn(n, "{4}"), s = vn(n, "{6}"), c = vn(n, "{1,2}"), l = vn(n, "{1,3}"), f = vn(n, "{1,6}"), d = vn(n, "{1,9}"), h = vn(n, "{2,4}"), m = vn(n, "{4,6}"), e = function (e) { if (t.literal) return r(e); switch (e.val) { case "G": return bn(n.eras("short", !1), 0); case "GG": return bn(n.eras("long", !1), 0); case "y": return pn(f); case "yy": return pn(h, ue); case "yyyy": return pn(u); case "yyyyy": return pn(m); case "yyyyyy": return pn(s); case "M": return pn(c); case "MM": return pn(o); case "MMM": return bn(n.months("short", !0, !1), 1); case "MMMM": return bn(n.months("long", !0, !1), 1); case "L": return pn(c); case "LL": return pn(o); case "LLL": return bn(n.months("short", !1, !1), 1); case "LLLL": return bn(n.months("long", !1, !1), 1); case "d": return pn(c); case "dd": return pn(o); case "o": return pn(l); case "ooo": return pn(a); case "HH": return pn(o); case "H": return pn(c); case "hh": return pn(o); case "h": return pn(c); case "mm": return pn(o); case "m": case "q": return pn(c); case "qq": return pn(o); case "s": return pn(c); case "ss": return pn(o); case "S": return pn(l); case "SSS": return pn(a); case "u": return Sn(d); case "a": return bn(n.meridiems(), 0); case "kkkk": return pn(u); case "kk": return pn(h, ue); case "W": return pn(c); case "WW": return pn(o); case "E": case "c": return pn(i); case "EEE": return bn(n.weekdays("short", !1, !1), 1); case "EEEE": return bn(n.weekdays("long", !1, !1), 1); case "ccc": return bn(n.weekdays("short", !0, !1), 1); case "cccc": return bn(n.weekdays("long", !0, !1), 1); case "Z": case "ZZ": return On(new RegExp("([+-]" + c.source + ")(?::(" + o.source + "))?"), 2); case "ZZZ": return On(new RegExp("([+-]" + c.source + ")(" + o.source + ")?"), 2); case "z": return Sn(/[a-z_+-/]{1,256}?/i); default: return r(e) } }(t) || { invalidReason: gn }; return e.token = t, e } var Mn = { year: { "2-digit": "yy", numeric: "yyyyy" }, month: { numeric: "M", "2-digit": "MM", short: "MMM", long: "MMMM" }, day: { numeric: "d", "2-digit": "dd" }, weekday: { short: "EEE", long: "EEEE" }, dayperiod: "a", dayPeriod: "a", hour: { numeric: "h", "2-digit": "hh" }, minute: { numeric: "m", "2-digit": "mm" }, second: { numeric: "s", "2-digit": "ss" } }; var Nn = null; function Dn(e, t) { if (e.literal) return e; var n = Le.macroTokenToFormatOpts(e.val); if (!n) return e; var r = Le.create(t, n).formatDateTimeParts(Nn = Nn || ur.fromMillis(1555555555555)).map(function (e) { return function (e, t) { var n = e.type, r = e.value; if ("literal" === n) return { literal: !0, val: r }; var i = t[n], o = Mn[n]; return "object" == typeof o && (o = o[i]), o ? { literal: !1, val: o } : void 0 }(e, n) }); return r.includes(void 0) ? e : r } function En(t, e, n) { var r, i, o, a = (r = Le.parseFormat(n), i = t, (o = Array.prototype).concat.apply(o, r.map(function (e) { return Dn(e, i) }))), u = a.map(function (e) { return Tn(e, t) }), s = u.find(function (e) { return e.invalidReason }); if (s) return { input: e, tokens: a, invalidReason: s.invalidReason }; var c, l, f, d = ["^" + (f = u).map(function (e) { return e.regex }).reduce(function (e, t) { return e + "(" + t.source + ")" }, "") + "$", f], h = d[1], m = RegExp(d[0], "i"), y = function (e, t, n) { var r = e.match(t); if (r) { var i, o, a = {}, u = 1; for (var s in n) { B(n, s) && (o = (i = n[s]).groups ? i.groups + 1 : 1, !i.literal && i.token && (a[i.token.val[0]] = i.deser(r.slice(u, u + o))), u += o) } return [r, a] } return [r, {}] }(e, m, h), v = y[0], g = y[1], p = g ? (l = R((c = g).Z) ? R(c.z) ? null : qe.create(c.z) : new Ue(c.Z), R(c.q) || (c.M = 3 * (c.q - 1) + 1), R(c.h) || (c.h < 12 && 1 === c.a ? c.h += 12 : 12 === c.h && 0 === c.a && (c.h = 0)), 0 === c.G && c.y && (c.y = -c.y), R(c.u) || (c.S = ee(c.u)), [Object.keys(c).reduce(function (e, t) { var n = function (e) { switch (e) { case "S": return "millisecond"; case "s": return "second"; case "m": return "minute"; case "h": case "H": return "hour"; case "d": return "day"; case "o": return "ordinal"; case "L": case "M": return "month"; case "y": return "year"; case "E": case "c": return "weekday"; case "W": return "weekNumber"; case "k": return "weekYear"; case "q": return "quarter"; default: return null } }(t); return n && (e[n] = c[t]), e }, {}), l]) : [null, null], w = p[0], k = p[1]; if (B(g, "a") && B(g, "H")) throw new L("Can't include meridiem when specifying 24-hour format"); return { input: e, tokens: a, regex: m, rawMatches: v, matches: g, result: w, zone: k } } var In = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334], Vn = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335]; function Ln(e, t) { return new xe("unit out of range", "you specified " + t + " (of type " + typeof t + ") as a " + e + ", which is invalid") } function xn(e, t, n) { var r = new Date(Date.UTC(e, t - 1, n)).getUTCDay(); return 0 === r ? 7 : r } function Fn(e, t, n) { return n + (ne(e) ? Vn : In)[t - 1] } function Zn(e, t) { var n = ne(e) ? Vn : In, r = n.findIndex(function (e) { return e < t }); return { month: r + 1, day: t - n[r] } } function Cn(e) { var t, n = e.year, r = e.month, i = e.day, o = Fn(n, r, i), a = xn(n, r, i), u = Math.floor((o - a + 10) / 7); return u < 1 ? u = ae(t = n - 1) : u > ae(n) ? (t = n + 1, u = 1) : t = n, Object.assign({ weekYear: t, weekNumber: u, weekday: a }, he(e)) } function jn(e) { var t, n = e.weekYear, r = e.weekNumber, i = e.weekday, o = xn(n, 1, 4), a = re(n), u = 7 * r + i - o - 3; u < 1 ? u += re(t = n - 1) : a < u ? (t = n + 1, u -= re(n)) : t = n; var s = Zn(t, u), c = s.month, l = s.day; return Object.assign({ year: t, month: c, day: l }, he(e)) } function An(e) { var t = e.year, n = Fn(t, e.month, e.day); return Object.assign({ year: t, ordinal: n }, he(e)) } function zn(e) { var t = e.year, n = Zn(t, e.ordinal), r = n.month, i = n.day; return Object.assign({ year: t, month: r, day: i }, he(e)) } function _n(e) { var t = P(e.year), n = Q(e.month, 1, 12), r = Q(e.day, 1, ie(e.year, e.month)); return t ? n ? !r && Ln("day", e.day) : Ln("month", e.month) : Ln("year", e.year) } function qn(e) { var t = e.hour, n = e.minute, r = e.second, i = e.millisecond, o = Q(t, 0, 23) || 24 === t && 0 === n && 0 === r && 0 === i, a = Q(n, 0, 59), u = Q(r, 0, 59), s = Q(i, 0, 999); return o ? a ? u ? !s && Ln("millisecond", i) : Ln("second", r) : Ln("minute", n) : Ln("hour", t) } var Hn = "Invalid DateTime"; function Un(e) { return new xe("unsupported zone", 'the zone "' + e.name + '" is not supported') } function Rn(e) { return null === e.weekData && (e.weekData = Cn(e.c)), e.weekData } function Wn(e, t) { var n = { ts: e.ts, zone: e.zone, c: e.c, o: e.o, loc: e.loc, invalid: e.invalid }; return new ur(Object.assign({}, n, t, { old: n })) } function Pn(e, t, n) { var r = e - 60 * t * 1e3, i = n.offset(r); if (t === i) return [r, t]; r -= 60 * (i - t) * 1e3; var o = n.offset(r); return i === o ? [r, i] : [e - 60 * Math.min(i, o) * 1e3, Math.max(i, o)] } function Jn(e, t) { e += 60 * t * 1e3; var n = new Date(e); return { year: n.getUTCFullYear(), month: n.getUTCMonth() + 1, day: n.getUTCDate(), hour: n.getUTCHours(), minute: n.getUTCMinutes(), second: n.getUTCSeconds(), millisecond: n.getUTCMilliseconds() } } function Yn(e, t, n) { return Pn(oe(e), t, n) } function Gn(e, t) { var n = Object.keys(t.values); -1 === n.indexOf("milliseconds") && n.push("milliseconds"), t = t.shiftTo.apply(t, n); var r = e.o, i = e.c.year + t.years, o = e.c.month + t.months + 3 * t.quarters, a = Object.assign({}, e.c, { year: i, month: o, day: Math.min(e.c.day, ie(i, o)) + t.days + 7 * t.weeks }), u = an.fromObject({ hours: t.hours, minutes: t.minutes, seconds: t.seconds, milliseconds: t.milliseconds }).as("milliseconds"), s = Pn(oe(a), r, e.zone), c = s[0], l = s[1]; return 0 !== u && (c += u, l = e.zone.offset(c)), { ts: c, o: l } } function $n(e, t, n, r, i) { var o = n.setZone, a = n.zone; if (e && 0 !== Object.keys(e).length) { var u = t || a, s = ur.fromObject(Object.assign(e, n, { zone: u, setZone: void 0 })); return o ? s : s.setZone(a) } return ur.invalid(new xe("unparsable", 'the input "' + i + "\" can't be parsed as " + r)) } function Bn(e, t, n) { return void 0 === n && (n = !0), e.isValid ? Le.create(st.create("en-US"), { allowZ: n, forceSimple: !0 }).formatDateTimeFromString(e, t) : null } function Qn(e, t) { var n = t.suppressSeconds, r = void 0 !== n && n, i = t.suppressMilliseconds, o = void 0 !== i && i, a = t.includeOffset, u = t.includeZone, s = void 0 !== u && u, c = t.spaceZone, l = void 0 !== c && c, f = t.format, d = void 0 === f ? "extended" : f, h = "basic" === d ? "HHmm" : "HH:mm"; return r && 0 === e.second && 0 === e.millisecond || (h += "basic" === d ? "ss" : ":ss", o && 0 === e.millisecond || (h += ".SSS")), (s || a) && l && (h += " "), s ? h += "z" : a && (h += "basic" === d ? "ZZZ" : "ZZ"), Bn(e, h) } var Kn = { month: 1, day: 1, hour: 0, minute: 0, second: 0, millisecond: 0 }, Xn = { weekNumber: 1, weekday: 1, hour: 0, minute: 0, second: 0, millisecond: 0 }, er = { ordinal: 1, hour: 0, minute: 0, second: 0, millisecond: 0 }, tr = ["year", "month", "day", "hour", "minute", "second", "millisecond"], nr = ["weekYear", "weekNumber", "weekday", "hour", "minute", "second", "millisecond"], rr = ["year", "ordinal", "hour", "minute", "second", "millisecond"]; function ir(e) { var t = { year: "year", years: "year", month: "month", months: "month", day: "day", days: "day", hour: "hour", hours: "hour", minute: "minute", minutes: "minute", quarter: "quarter", quarters: "quarter", second: "second", seconds: "second", millisecond: "millisecond", milliseconds: "millisecond", weekday: "weekday", weekdays: "weekday", weeknumber: "weekNumber", weeksnumber: "weekNumber", weeknumbers: "weekNumber", weekyear: "weekYear", weekyears: "weekYear", ordinal: "ordinal" }[e.toLowerCase()]; if (!t) throw new h(e); return t } function or(e, t) { for (var n, r = V(tr); !(n = r()).done;) { var i = n.value; R(e[i]) && (e[i] = Kn[i]) } var o = _n(e) || qn(e); if (o) return ur.invalid(o); var a = Qe.now(), u = Yn(e, t.offset(a), t), s = u[0], c = u[1]; return new ur({ ts: s, zone: t, o: c }) } function ar(t, n, r) { function e(e, t) { return e = te(e, o || r.calendary ? 0 : 2, !0), n.loc.clone(r).relFormatter(r).format(e, t) } function i(e) { return r.calendary ? n.hasSame(t, e) ? 0 : n.startOf(e).diff(t.startOf(e), e).get(e) : n.diff(t, e).get(e) } var o = !!R(r.round) || r.round; if (r.unit) return e(i(r.unit), r.unit); for (var a, u = V(r.units); !(a = u()).done;) { var s = a.value, c = i(s); if (1 <= Math.abs(c)) return e(c, s) } return e(0, r.units[r.units.length - 1]) } var ur = function () { function I(e) { var t = e.zone || Qe.defaultZone, n = e.invalid || (Number.isNaN(e.ts) ? new xe("invalid input") : null) || (t.isValid ? null : Un(t)); this.ts = R(e.ts) ? Qe.now() : e.ts; var r, i, o = null, a = null; n || (a = e.old && e.old.ts === this.ts && e.old.zone.equals(t) ? (o = (r = [e.old.c, e.old.o])[0], r[1]) : (i = t.offset(this.ts), o = Jn(this.ts, i), o = (n = Number.isNaN(o.year) ? new xe("invalid input") : null) ? null : o, n ? null : i)), this._zone = t, this.loc = e.loc || st.create(), this.invalid = n, this.weekData = null, this.c = o, this.o = a, this.isLuxonDateTime = !0 } I.local = function (e, t, n, r, i, o, a) { return R(e) ? new I({ ts: Qe.now() }) : or({ year: e, month: t, day: n, hour: r, minute: i, second: o, millisecond: a }, Qe.defaultZone) }, I.utc = function (e, t, n, r, i, o, a) { return R(e) ? new I({ ts: Qe.now(), zone: Ue.utcInstance }) : or({ year: e, month: t, day: n, hour: r, minute: i, second: o, millisecond: a }, Ue.utcInstance) }, I.fromJSDate = function (e, t) { void 0 === t && (t = {}); var n, r = (n = e, "[object Date]" === Object.prototype.toString.call(n) ? e.valueOf() : NaN); if (Number.isNaN(r)) return I.invalid("invalid input"); var i = We(t.zone, Qe.defaultZone); return i.isValid ? new I({ ts: r, zone: i, loc: st.fromObject(t) }) : I.invalid(Un(i)) }, I.fromMillis = function (e, t) { if (void 0 === t && (t = {}), W(e)) return e < -864e13 || 864e13 < e ? I.invalid("Timestamp out of range") : new I({ ts: e, zone: We(t.zone, Qe.defaultZone), loc: st.fromObject(t) }); throw new m("fromMillis requires a numerical input, but received a " + typeof e + " with value " + e) }, I.fromSeconds = function (e, t) { if (void 0 === t && (t = {}), W(e)) return new I({ ts: 1e3 * e, zone: We(t.zone, Qe.defaultZone), loc: st.fromObject(t) }); throw new m("fromSeconds requires a numerical input") }, I.fromObject = function (e) { var t = We(e.zone, Qe.defaultZone); if (!t.isValid) return I.invalid(Un(t)); var n = Qe.now(), r = t.offset(n), i = fe(e, ir, ["zone", "locale", "outputCalendar", "numberingSystem"]), o = !R(i.ordinal), a = !R(i.year), u = !R(i.month) || !R(i.day), s = a || u, c = i.weekYear || i.weekNumber, l = st.fromObject(e); if ((s || o) && c) throw new L("Can't mix weekYear/weekNumber units with year/month/day or ordinals"); if (u && o) throw new L("Can't mix ordinal dates with month/day"); var f, d, h = c || i.weekday && !s, m = Jn(n, r); h ? (f = nr, d = Xn, m = Cn(m)) : o ? (f = rr, d = er, m = An(m)) : (f = tr, d = Kn); for (var y, v = !1, g = V(f); !(y = g()).done;) { var p = y.value; R(i[p]) ? i[p] = v ? d[p] : m[p] : v = !0 } var w, k, b, O, S, T, M, N = (h ? (S = P((O = i).weekYear), T = Q(O.weekNumber, 1, ae(O.weekYear)), M = Q(O.weekday, 1, 7), S ? T ? !M && Ln("weekday", O.weekday) : Ln("week", O.week) : Ln("weekYear", O.weekYear)) : o ? (k = P((w = i).year), b = Q(w.ordinal, 1, re(w.year)), k ? !b && Ln("ordinal", w.ordinal) : Ln("year", w.year)) : _n(i)) || qn(i); if (N) return I.invalid(N); var D = Yn(h ? jn(i) : o ? zn(i) : i, r, t), E = new I({ ts: D[0], zone: t, o: D[1], loc: l }); return i.weekday && s && e.weekday !== E.weekday ? I.invalid("mismatched weekday", "you can't specify both a weekday of " + i.weekday + " and a date of " + E.toISO()) : E }, I.fromISO = function (e, t) { void 0 === t && (t = {}); var n = ft(e, [At, Ht], [zt, Ut], [_t, Rt], [qt, Wt]); return $n(n[0], n[1], t, "ISO 8601", e) }, I.fromRFC2822 = function (e, t) { void 0 === t && (t = {}); var n = ft(e.replace(/\([^)]*\)|[\n\t]/g, " ").replace(/(\s\s+)/g, " ").trim(), [Vt, Lt]); return $n(n[0], n[1], t, "RFC 2822", e) }, I.fromHTTP = function (e, t) { void 0 === t && (t = {}); var n = ft(e, [xt, Ct], [Ft, Ct], [Zt, jt]); return $n(n[0], n[1], t, "HTTP", t) }, I.fromFormat = function (e, t, n) { if (void 0 === n && (n = {}), R(e) || R(t)) throw new m("fromFormat requires an input string and a format"); var r, i = n.locale, o = void 0 === i ? null : i, a = n.numberingSystem, u = void 0 === a ? null : a, s = st.fromOpts({ locale: o, numberingSystem: u, defaultToEN: !0 }), c = [(r = En(s, e, t)).result, r.zone, r.invalidReason], l = c[0], f = c[1], d = c[2]; return d ? I.invalid(d) : $n(l, f, n, "format " + t, e) }, I.fromString = function (e, t, n) { return void 0 === n && (n = {}), I.fromFormat(e, t, n) }, I.fromSQL = function (e, t) { void 0 === t && (t = {}); var n = ft(e, [Pt, Yt], [Jt, Gt]); return $n(n[0], n[1], t, "SQL", e) }, I.invalid = function (e, t) { if (void 0 === t && (t = null), !e) throw new m("need to specify a reason the DateTime is invalid"); var n = e instanceof xe ? e : new xe(e, t); if (Qe.throwOnInvalid) throw new l(n); return new I({ invalid: n }) }, I.isDateTime = function (e) { return e && e.isLuxonDateTime || !1 }; var e = I.prototype; return e.get = function (e) { return this[e] }, e.resolvedLocaleOpts = function (e) { void 0 === e && (e = {}); var t = Le.create(this.loc.clone(e), e).resolvedOptions(this); return { locale: t.locale, numberingSystem: t.numberingSystem, outputCalendar: t.calendar } }, e.toUTC = function (e, t) { return void 0 === e && (e = 0), void 0 === t && (t = {}), this.setZone(Ue.instance(e), t) }, e.toLocal = function () { return this.setZone(Qe.defaultZone) }, e.setZone = function (e, t) { var n = void 0 === t ? {} : t, r = n.keepLocalTime, i = void 0 !== r && r, o = n.keepCalendarTime, a = void 0 !== o && o; if ((e = We(e, Qe.defaultZone)).equals(this.zone)) return this; if (e.isValid) { var u, s = this.ts; return (i || a) && (u = e.offset(this.ts), s = Yn(this.toObject(), u, e)[0]), Wn(this, { ts: s, zone: e }) } return I.invalid(Un(e)) }, e.reconfigure = function (e) { var t = void 0 === e ? {} : e, n = t.locale, r = t.numberingSystem, i = t.outputCalendar, o = this.loc.clone({ locale: n, numberingSystem: r, outputCalendar: i }); return Wn(this, { loc: o }) }, e.setLocale = function (e) { return this.reconfigure({ locale: e }) }, e.set = function (e) { if (!this.isValid) return this; var t, n = fe(e, ir, []); !R(n.weekYear) || !R(n.weekNumber) || !R(n.weekday) ? t = jn(Object.assign(Cn(this.c), n)) : R(n.ordinal) ? (t = Object.assign(this.toObject(), n), R(n.day) && (t.day = Math.min(ie(t.year, t.month), t.day))) : t = zn(Object.assign(An(this.c), n)); var r = Yn(t, this.o, this.zone); return Wn(this, { ts: r[0], o: r[1] }) }, e.plus = function (e) { return this.isValid ? Wn(this, Gn(this, un(e))) : this }, e.minus = function (e) { return this.isValid ? Wn(this, Gn(this, un(e).negate())) : this }, e.startOf = function (e) { if (!this.isValid) return this; var t, n = {}, r = an.normalizeUnit(e); switch (r) { case "years": n.month = 1; case "quarters": case "months": n.day = 1; case "weeks": case "days": n.hour = 0; case "hours": n.minute = 0; case "minutes": n.second = 0; case "seconds": n.millisecond = 0 }return "weeks" === r && (n.weekday = 1), "quarters" === r && (t = Math.ceil(this.month / 3), n.month = 3 * (t - 1) + 1), this.set(n) }, e.endOf = function (e) { var t; return this.isValid ? this.plus(((t = {})[e] = 1, t)).startOf(e).minus(1) : this }, e.toFormat = function (e, t) { return void 0 === t && (t = {}), this.isValid ? Le.create(this.loc.redefaultToEN(t)).formatDateTimeFromString(this, e) : Hn }, e.toLocaleString = function (e) { return void 0 === e && (e = w), this.isValid ? Le.create(this.loc.clone(e), e).formatDateTime(this) : Hn }, e.toLocaleParts = function (e) { return void 0 === e && (e = {}), this.isValid ? Le.create(this.loc.clone(e), e).formatDateTimeParts(this) : [] }, e.toISO = function (e) { return void 0 === e && (e = {}), this.isValid ? this.toISODate(e) + "T" + this.toISOTime(e) : null }, e.toISODate = function (e) { var t = (void 0 === e ? {} : e).format, n = "basic" === (void 0 === t ? "extended" : t) ? "yyyyMMdd" : "yyyy-MM-dd"; return 9999 < this.year && (n = "+" + n), Bn(this, n) }, e.toISOWeekDate = function () { return Bn(this, "kkkk-'W'WW-c") }, e.toISOTime = function (e) { var t = void 0 === e ? {} : e, n = t.suppressMilliseconds, r = void 0 !== n && n, i = t.suppressSeconds, o = void 0 !== i && i, a = t.includeOffset, u = void 0 === a || a, s = t.format; return Qn(this, { suppressSeconds: o, suppressMilliseconds: r, includeOffset: u, format: void 0 === s ? "extended" : s }) }, e.toRFC2822 = function () { return Bn(this, "EEE, dd LLL yyyy HH:mm:ss ZZZ", !1) }, e.toHTTP = function () { return Bn(this.toUTC(), "EEE, dd LLL yyyy HH:mm:ss 'GMT'") }, e.toSQLDate = function () { return Bn(this, "yyyy-MM-dd") }, e.toSQLTime = function (e) { var t = void 0 === e ? {} : e, n = t.includeOffset, r = void 0 === n || n, i = t.includeZone; return Qn(this, { includeOffset: r, includeZone: void 0 !== i && i, spaceZone: !0 }) }, e.toSQL = function (e) { return void 0 === e && (e = {}), this.isValid ? this.toSQLDate() + " " + this.toSQLTime(e) : null }, e.toString = function () { return this.isValid ? this.toISO() : Hn }, e.valueOf = function () { return this.toMillis() }, e.toMillis = function () { return this.isValid ? this.ts : NaN }, e.toSeconds = function () { return this.isValid ? this.ts / 1e3 : NaN }, e.toJSON = function () { return this.toISO() }, e.toBSON = function () { return this.toJSDate() }, e.toObject = function (e) { if (void 0 === e && (e = {}), !this.isValid) return {}; var t = Object.assign({}, this.c); return e.includeConfig && (t.outputCalendar = this.outputCalendar, t.numberingSystem = this.loc.numberingSystem, t.locale = this.loc.locale), t }, e.toJSDate = function () { return new Date(this.isValid ? this.ts : NaN) }, e.diff = function (e, t, n) { if (void 0 === t && (t = "milliseconds"), void 0 === n && (n = {}), !this.isValid || !e.isValid) return an.invalid(this.invalid || e.invalid, "created by diffing an invalid DateTime"); var r, i = Object.assign({ locale: this.locale, numberingSystem: this.numberingSystem }, n), o = (r = t, (Array.isArray(r) ? r : [r]).map(an.normalizeUnit)), a = e.valueOf() > this.valueOf(), u = dn(a ? this : e, a ? e : this, o, i); return a ? u.negate() : u }, e.diffNow = function (e, t) { return void 0 === e && (e = "milliseconds"), void 0 === t && (t = {}), this.diff(I.local(), e, t) }, e.until = function (e) { return this.isValid ? cn.fromDateTimes(this, e) : this }, e.hasSame = function (e, t) { if (!this.isValid) return !1; if ("millisecond" === t) return this.valueOf() === e.valueOf(); var n = e.valueOf(); return this.startOf(t) <= n && n <= this.endOf(t) }, e.equals = function (e) { return this.isValid && e.isValid && this.valueOf() === e.valueOf() && this.zone.equals(e.zone) && this.loc.equals(e.loc) }, e.toRelative = function (e) { if (void 0 === e && (e = {}), !this.isValid) return null; var t = e.base || I.fromObject({ zone: this.zone }), n = e.padding ? this < t ? -e.padding : e.padding : 0; return ar(t, this.plus(n), Object.assign(e, { numeric: "always", units: ["years", "months", "days", "hours", "minutes", "seconds"] })) }, e.toRelativeCalendar = function (e) { return void 0 === e && (e = {}), this.isValid ? ar(e.base || I.fromObject({ zone: this.zone }), this, Object.assign(e, { numeric: "auto", units: ["years", "months", "days"], calendary: !0 })) : null }, I.min = function () { for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++)t[n] = arguments[n]; if (!t.every(I.isDateTime)) throw new m("min requires all arguments be DateTimes"); return G(t, function (e) { return e.valueOf() }, Math.min) }, I.max = function () { for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++)t[n] = arguments[n]; if (!t.every(I.isDateTime)) throw new m("max requires all arguments be DateTimes"); return G(t, function (e) { return e.valueOf() }, Math.max) }, I.fromFormatExplain = function (e, t, n) { void 0 === n && (n = {}); var r = n.locale, i = void 0 === r ? null : r, o = n.numberingSystem, a = void 0 === o ? null : o; return En(st.fromOpts({ locale: i, numberingSystem: a, defaultToEN: !0 }), e, t) }, I.fromStringExplain = function (e, t, n) { return void 0 === n && (n = {}), I.fromFormatExplain(e, t, n) }, i(I, [{ key: "isValid", get: function () { return null === this.invalid } }, { key: "invalidReason", get: function () { return this.invalid ? this.invalid.reason : null } }, { key: "invalidExplanation", get: function () { return this.invalid ? this.invalid.explanation : null } }, { key: "locale", get: function () { return this.isValid ? this.loc.locale : null } }, { key: "numberingSystem", get: function () { return this.isValid ? this.loc.numberingSystem : null } }, { key: "outputCalendar", get: function () { return this.isValid ? this.loc.outputCalendar : null } }, { key: "zone", get: function () { return this._zone } }, { key: "zoneName", get: function () { return this.isValid ? this.zone.name : null } }, { key: "year", get: function () { return this.isValid ? this.c.year : NaN } }, { key: "quarter", get: function () { return this.isValid ? Math.ceil(this.c.month / 3) : NaN } }, { key: "month", get: function () { return this.isValid ? this.c.month : NaN } }, { key: "day", get: function () { return this.isValid ? this.c.day : NaN } }, { key: "hour", get: function () { return this.isValid ? this.c.hour : NaN } }, { key: "minute", get: function () { return this.isValid ? this.c.minute : NaN } }, { key: "second", get: function () { return this.isValid ? this.c.second : NaN } }, { key: "millisecond", get: function () { return this.isValid ? this.c.millisecond : NaN } }, { key: "weekYear", get: function () { return this.isValid ? Rn(this).weekYear : NaN } }, { key: "weekNumber", get: function () { return this.isValid ? Rn(this).weekNumber : NaN } }, { key: "weekday", get: function () { return this.isValid ? Rn(this).weekday : NaN } }, { key: "ordinal", get: function () { return this.isValid ? An(this.c).ordinal : NaN } }, { key: "monthShort", get: function () { return this.isValid ? ln.months("short", { locale: this.locale })[this.month - 1] : null } }, { key: "monthLong", get: function () { return this.isValid ? ln.months("long", { locale: this.locale })[this.month - 1] : null } }, { key: "weekdayShort", get: function () { return this.isValid ? ln.weekdays("short", { locale: this.locale })[this.weekday - 1] : null } }, { key: "weekdayLong", get: function () { return this.isValid ? ln.weekdays("long", { locale: this.locale })[this.weekday - 1] : null } }, { key: "offset", get: function () { return this.isValid ? +this.o : NaN } }, { key: "offsetNameShort", get: function () { return this.isValid ? this.zone.offsetName(this.ts, { format: "short", locale: this.locale }) : null } }, { key: "offsetNameLong", get: function () { return this.isValid ? this.zone.offsetName(this.ts, { format: "long", locale: this.locale }) : null } }, { key: "isOffsetFixed", get: function () { return this.isValid ? this.zone.universal : null } }, { key: "isInDST", get: function () { return !this.isOffsetFixed && (this.offset > this.set({ month: 1 }).offset || this.offset > this.set({ month: 5 }).offset) } }, { key: "isInLeapYear", get: function () { return ne(this.year) } }, { key: "daysInMonth", get: function () { return ie(this.year, this.month) } }, { key: "daysInYear", get: function () { return this.isValid ? re(this.year) : NaN } }, { key: "weeksInWeekYear", get: function () { return this.isValid ? ae(this.weekYear) : NaN } }], [{ key: "DATE_SHORT", get: function () { return w } }, { key: "DATE_MED", get: function () { return k } }, { key: "DATE_FULL", get: function () { return b } }, { key: "DATE_HUGE", get: function () { return O } }, { key: "TIME_SIMPLE", get: function () { return S } }, { key: "TIME_WITH_SECONDS", get: function () { return T } }, { key: "TIME_WITH_SHORT_OFFSET", get: function () { return M } }, { key: "TIME_WITH_LONG_OFFSET", get: function () { return N } }, { key: "TIME_24_SIMPLE", get: function () { return D } }, { key: "TIME_24_WITH_SECONDS", get: function () { return E } }, { key: "TIME_24_WITH_SHORT_OFFSET", get: function () { return x } }, { key: "TIME_24_WITH_LONG_OFFSET", get: function () { return F } }, { key: "DATETIME_SHORT", get: function () { return Z } }, { key: "DATETIME_SHORT_WITH_SECONDS", get: function () { return C } }, { key: "DATETIME_MED", get: function () { return j } }, { key: "DATETIME_MED_WITH_SECONDS", get: function () { return A } }, { key: "DATETIME_MED_WITH_WEEKDAY", get: function () { return z } }, { key: "DATETIME_FULL", get: function () { return _ } }, { key: "DATETIME_FULL_WITH_SECONDS", get: function () { return q } }, { key: "DATETIME_HUGE", get: function () { return H } }, { key: "DATETIME_HUGE_WITH_SECONDS", get: function () { return U } }]), I }(); function sr(e) { if (ur.isDateTime(e)) return e; if (e && e.valueOf && W(e.valueOf())) return ur.fromJSDate(e); if (e && "object" == typeof e) return ur.fromObject(e); throw new m("Unknown datetime argument: " + e + ", of type " + typeof e) } return e.DateTime = ur, e.Duration = an, e.FixedOffsetZone = Ue, e.IANAZone = qe, e.Info = ln, e.Interval = cn, e.InvalidZone = Re, e.LocalZone = Ce, e.Settings = Qe, e.Zone = Fe, e }({});/*!
FullCalendar v5.5.1
Docs & License: https://fullcalendar.io/
(c) 2020 Adam Shaw
*/
var FullCalendar=function(e){"use strict";var t=function(e,n){return(t=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(e,t){e.__proto__=t}||function(e,t){for(var n in t)Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n])})(e,n)};function n(e,n){function r(){this.constructor=e}t(e,n),e.prototype=null===n?Object.create(n):(r.prototype=n.prototype,new r)}var r=function(){return(r=Object.assign||function(e){for(var t,n=1,r=arguments.length;n<r;n++)for(var o in t=arguments[n])Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e}).apply(this,arguments)};function o(){for(var e=0,t=0,n=arguments.length;t<n;t++)e+=arguments[t].length;var r=Array(e),o=0;for(t=0;t<n;t++)for(var i=arguments[t],a=0,s=i.length;a<s;a++,o++)r[o]=i[a];return r}var i,a,s,l,u,c,d={},p=[],f=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i;function h(e,t){for(var n in t)e[n]=t[n];return e}function v(e){var t=e.parentNode;t&&t.removeChild(e)}function g(e,t,n){var r,o,i,a=arguments,s={};for(i in t)"key"==i?r=t[i]:"ref"==i?o=t[i]:s[i]=t[i];if(arguments.length>3)for(n=[n],i=3;i<arguments.length;i++)n.push(a[i]);if(null!=n&&(s.children=n),"function"==typeof e&&null!=e.defaultProps)for(i in e.defaultProps)void 0===s[i]&&(s[i]=e.defaultProps[i]);return m(e,s,r,o,null)}function m(e,t,n,r,o){var a={type:e,props:t,key:n,ref:r,__k:null,__:null,__b:0,__e:null,__d:void 0,__c:null,__h:null,constructor:void 0,__v:null==o?++i.__v:o};return null!=i.vnode&&i.vnode(a),a}function y(e){return e.children}function E(e,t){this.props=e,this.context=t}function S(e,t){if(null==t)return e.__?S(e.__,e.__.__k.indexOf(e)+1):null;for(var n;t<e.__k.length;t++)if(null!=(n=e.__k[t])&&null!=n.__e)return n.__e;return"function"==typeof e.type?S(e):null}function D(e){var t,n;if(null!=(e=e.__)&&null!=e.__c){for(e.__e=e.__c.base=null,t=0;t<e.__k.length;t++)if(null!=(n=e.__k[t])&&null!=n.__e){e.__e=e.__c.base=n.__e;break}return D(e)}}function b(e){(!e.__d&&(e.__d=!0)&&a.push(e)&&!C.__r++||l!==i.debounceRendering)&&((l=i.debounceRendering)||s)(C)}function C(){for(var e;C.__r=a.length;)e=a.sort((function(e,t){return e.__v.__b-t.__v.__b})),a=[],e.some((function(e){var t,n,r,o,i,a,s;e.__d&&(a=(i=(t=e).__v).__e,(s=t.__P)&&(n=[],(r=h({},i)).__v=i.__v+1,o=I(s,i,r,t.__n,void 0!==s.ownerSVGElement,null!=i.__h?[a]:null,n,null==a?S(i):a,i.__h),P(n,i),o!=a&&D(i)))}))}function w(e,t,n,r,o,i,a,s,l,u){var c,f,h,g,E,D,b,C=r&&r.__k||p,w=C.length;for(l==d&&(l=null!=a?a[0]:w?S(r,0):null),n.__k=[],c=0;c<t.length;c++)if(null!=(g=n.__k[c]=null==(g=t[c])||"boolean"==typeof g?null:"string"==typeof g||"number"==typeof g?m(null,g,null,null,g):Array.isArray(g)?m(y,{children:g},null,null,null):null!=g.__e||null!=g.__c?m(g.type,g.props,g.key,null,g.__v):g)){if(g.__=n,g.__b=n.__b+1,null===(h=C[c])||h&&g.key==h.key&&g.type===h.type)C[c]=void 0;else for(f=0;f<w;f++){if((h=C[f])&&g.key==h.key&&g.type===h.type){C[f]=void 0;break}h=null}E=I(e,g,h=h||d,o,i,a,s,l,u),(f=g.ref)&&h.ref!=f&&(b||(b=[]),h.ref&&b.push(h.ref,null,g),b.push(f,g.__c||E,g)),null!=E?(null==D&&(D=E),l=R(e,g,h,C,a,E,l),u||"option"!=n.type?"function"==typeof n.type&&(n.__d=l):e.value=""):l&&h.__e==l&&l.parentNode!=e&&(l=S(h))}if(n.__e=D,null!=a&&"function"!=typeof n.type)for(c=a.length;c--;)null!=a[c]&&v(a[c]);for(c=w;c--;)null!=C[c]&&O(C[c],C[c]);if(b)for(c=0;c<b.length;c++)H(b[c],b[++c],b[++c])}function R(e,t,n,r,o,i,a){var s,l,u;if(void 0!==t.__d)s=t.__d,t.__d=void 0;else if(o==n||i!=a||null==i.parentNode)e:if(null==a||a.parentNode!==e)e.appendChild(i),s=null;else{for(l=a,u=0;(l=l.nextSibling)&&u<r.length;u+=2)if(l==i)break e;e.insertBefore(i,a),s=a}return void 0!==s?s:i.nextSibling}function T(e,t,n){"-"===t[0]?e.setProperty(t,n):e[t]=null==n?"":"number"!=typeof n||f.test(t)?n:n+"px"}function k(e,t,n,r,o){var i,a,s;if(o&&"className"==t&&(t="class"),"style"===t)if("string"==typeof n)e.style.cssText=n;else{if("string"==typeof r&&(e.style.cssText=r=""),r)for(t in r)n&&t in n||T(e.style,t,"");if(n)for(t in n)r&&n[t]===r[t]||T(e.style,t,n[t])}else"o"===t[0]&&"n"===t[1]?(i=t!==(t=t.replace(/Capture$/,"")),(a=t.toLowerCase())in e&&(t=a),t=t.slice(2),e.l||(e.l={}),e.l[t+i]=n,s=i?x:M,n?r||e.addEventListener(t,s,i):e.removeEventListener(t,s,i)):"list"!==t&&"tagName"!==t&&"form"!==t&&"type"!==t&&"size"!==t&&"download"!==t&&"href"!==t&&!o&&t in e?e[t]=null==n?"":n:"function"!=typeof n&&"dangerouslySetInnerHTML"!==t&&(t!==(t=t.replace(/xlink:?/,""))?null==n||!1===n?e.removeAttributeNS("http://www.w3.org/1999/xlink",t.toLowerCase()):e.setAttributeNS("http://www.w3.org/1999/xlink",t.toLowerCase(),n):null==n||!1===n&&!/^ar/.test(t)?e.removeAttribute(t):e.setAttribute(t,n))}function M(e){this.l[e.type+!1](i.event?i.event(e):e)}function x(e){this.l[e.type+!0](i.event?i.event(e):e)}function _(e,t,n){var r,o;for(r=0;r<e.__k.length;r++)(o=e.__k[r])&&(o.__=e,o.__e&&("function"==typeof o.type&&o.__k.length>1&&_(o,t,n),t=R(n,o,o,e.__k,null,o.__e,t),"function"==typeof e.type&&(e.__d=t)))}function I(e,t,n,r,o,a,s,l,u){var c,d,p,f,v,g,m,S,D,b,C,R=t.type;if(void 0!==t.constructor)return null;null!=n.__h&&(u=n.__h,l=t.__e=n.__e,t.__h=null,a=[l]),(c=i.__b)&&c(t);try{e:if("function"==typeof R){if(S=t.props,D=(c=R.contextType)&&r[c.__c],b=c?D?D.props.value:c.__:r,n.__c?m=(d=t.__c=n.__c).__=d.__E:("prototype"in R&&R.prototype.render?t.__c=d=new R(S,b):(t.__c=d=new E(S,b),d.constructor=R,d.render=A),D&&D.sub(d),d.props=S,d.state||(d.state={}),d.context=b,d.__n=r,p=d.__d=!0,d.__h=[]),null==d.__s&&(d.__s=d.state),null!=R.getDerivedStateFromProps&&(d.__s==d.state&&(d.__s=h({},d.__s)),h(d.__s,R.getDerivedStateFromProps(S,d.__s))),f=d.props,v=d.state,p)null==R.getDerivedStateFromProps&&null!=d.componentWillMount&&d.componentWillMount(),null!=d.componentDidMount&&d.__h.push(d.componentDidMount);else{if(null==R.getDerivedStateFromProps&&S!==f&&null!=d.componentWillReceiveProps&&d.componentWillReceiveProps(S,b),!d.__e&&null!=d.shouldComponentUpdate&&!1===d.shouldComponentUpdate(S,d.__s,b)||t.__v===n.__v){d.props=S,d.state=d.__s,t.__v!==n.__v&&(d.__d=!1),d.__v=t,t.__e=n.__e,t.__k=n.__k,d.__h.length&&s.push(d),_(t,l,e);break e}null!=d.componentWillUpdate&&d.componentWillUpdate(S,d.__s,b),null!=d.componentDidUpdate&&d.__h.push((function(){d.componentDidUpdate(f,v,g)}))}d.context=b,d.props=S,d.state=d.__s,(c=i.__r)&&c(t),d.__d=!1,d.__v=t,d.__P=e,c=d.render(d.props,d.state,d.context),d.state=d.__s,null!=d.getChildContext&&(r=h(h({},r),d.getChildContext())),p||null==d.getSnapshotBeforeUpdate||(g=d.getSnapshotBeforeUpdate(f,v)),C=null!=c&&c.type==y&&null==c.key?c.props.children:c,w(e,Array.isArray(C)?C:[C],t,n,r,o,a,s,l,u),d.base=t.__e,t.__h=null,d.__h.length&&s.push(d),m&&(d.__E=d.__=null),d.__e=!1}else null==a&&t.__v===n.__v?(t.__k=n.__k,t.__e=n.__e):t.__e=N(n.__e,t,n,r,o,a,s,u);(c=i.diffed)&&c(t)}catch(e){t.__v=null,(u||null!=a)&&(t.__e=l,t.__h=!!u,a[a.indexOf(l)]=null),i.__e(e,t,n)}return t.__e}function P(e,t){i.__c&&i.__c(t,e),e.some((function(t){try{e=t.__h,t.__h=[],e.some((function(e){e.call(t)}))}catch(e){i.__e(e,t.__v)}}))}function N(e,t,n,r,o,i,a,s){var l,u,c,f,h,v=n.props,g=t.props;if(o="svg"===t.type||o,null!=i)for(l=0;l<i.length;l++)if(null!=(u=i[l])&&((null===t.type?3===u.nodeType:u.localName===t.type)||e==u)){e=u,i[l]=null;break}if(null==e){if(null===t.type)return document.createTextNode(g);e=o?document.createElementNS("http://www.w3.org/2000/svg",t.type):document.createElement(t.type,g.is&&{is:g.is}),i=null,s=!1}if(null===t.type)v===g||s&&e.data===g||(e.data=g);else{if(null!=i&&(i=p.slice.call(e.childNodes)),c=(v=n.props||d).dangerouslySetInnerHTML,f=g.dangerouslySetInnerHTML,!s){if(null!=i)for(v={},h=0;h<e.attributes.length;h++)v[e.attributes[h].name]=e.attributes[h].value;(f||c)&&(f&&(c&&f.__html==c.__html||f.__html===e.innerHTML)||(e.innerHTML=f&&f.__html||""))}(function(e,t,n,r,o){var i;for(i in n)"children"===i||"key"===i||i in t||k(e,i,null,n[i],r);for(i in t)o&&"function"!=typeof t[i]||"children"===i||"key"===i||"value"===i||"checked"===i||n[i]===t[i]||k(e,i,t[i],n[i],r)})(e,g,v,o,s),f?t.__k=[]:(l=t.props.children,w(e,Array.isArray(l)?l:[l],t,n,r,"foreignObject"!==t.type&&o,i,a,d,s)),s||("value"in g&&void 0!==(l=g.value)&&(l!==e.value||"progress"===t.type&&!l)&&k(e,"value",l,v.value,!1),"checked"in g&&void 0!==(l=g.checked)&&l!==e.checked&&k(e,"checked",l,v.checked,!1))}return e}function H(e,t,n){try{"function"==typeof e?e(t):e.current=t}catch(e){i.__e(e,n)}}function O(e,t,n){var r,o,a;if(i.unmount&&i.unmount(e),(r=e.ref)&&(r.current&&r.current!==e.__e||H(r,null,t)),n||"function"==typeof e.type||(n=null!=(o=e.__e)),e.__e=e.__d=void 0,null!=(r=e.__c)){if(r.componentWillUnmount)try{r.componentWillUnmount()}catch(e){i.__e(e,t)}r.base=r.__P=null}if(r=e.__k)for(a=0;a<r.length;a++)r[a]&&O(r[a],t,n);null!=o&&v(o)}function A(e,t,n){return this.constructor(e,n)}function U(e,t,n){var r,o,a;i.__&&i.__(e,t),o=(r=n===u)?null:n&&n.__k||t.__k,e=g(y,null,[e]),a=[],I(t,(r?t:n||t).__k=e,o||d,d,void 0!==t.ownerSVGElement,n&&!r?[n]:o?null:t.childNodes.length?p.slice.call(t.childNodes):null,a,n||d,r),P(a,e)}i={__e:function(e,t){for(var n,r,o,i=t.__h;t=t.__;)if((n=t.__c)&&!n.__)try{if((r=n.constructor)&&null!=r.getDerivedStateFromError&&(n.setState(r.getDerivedStateFromError(e)),o=n.__d),null!=n.componentDidCatch&&(n.componentDidCatch(e),o=n.__d),o)return t.__h=i,n.__E=n}catch(t){e=t}throw e},__v:0},E.prototype.setState=function(e,t){var n;n=null!=this.__s&&this.__s!==this.state?this.__s:this.__s=h({},this.state),"function"==typeof e&&(e=e(h({},n),this.props)),e&&h(n,e),null!=e&&this.__v&&(t&&this.__h.push(t),b(this))},E.prototype.forceUpdate=function(e){this.__v&&(this.__e=!0,e&&this.__h.push(e),b(this))},E.prototype.render=y,a=[],s="function"==typeof Promise?Promise.prototype.then.bind(Promise.resolve()):setTimeout,C.__r=0,u=d,c=0;var L="undefined"!=typeof globalThis?globalThis:window;L.FullCalendarVDom?console.warn("FullCalendar VDOM already loaded"):L.FullCalendarVDom={Component:E,createElement:g,render:U,createRef:function(){return{current:null}},Fragment:y,createContext:function(e){var t=function(e,t){var n={__c:t="__cC"+c++,__:e,Consumer:function(e,t){return e.children(t)},Provider:function(e,n,r){return this.getChildContext||(n=[],(r={})[t]=this,this.getChildContext=function(){return r},this.shouldComponentUpdate=function(e){this.props.value!==e.value&&n.some(b)},this.sub=function(e){n.push(e);var t=e.componentWillUnmount;e.componentWillUnmount=function(){n.splice(n.indexOf(e),1),t&&t.call(e)}}),e.children}};return n.Provider.__=n.Consumer.contextType=n}(e),n=t.Provider;return t.Provider=function(){var e=this,t=!this.getChildContext,r=n.apply(this,arguments);if(t){var o=[];this.shouldComponentUpdate=function(t){e.props.value!==t.value&&o.forEach((function(e){e.context=t.value,e.forceUpdate()}))},this.sub=function(e){o.push(e);var t=e.componentWillUnmount;e.componentWillUnmount=function(){o.splice(o.indexOf(e),1),t&&t.call(e)}}}return r},t},flushToDom:function(){var e=i.debounceRendering,t=[];i.debounceRendering=function(e){t.push(e)},U(g(W,{}),document.createElement("div"));for(;t.length;)t.shift()();i.debounceRendering=e},unmountComponentAtNode:function(e){U(null,e)}};var W=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){return g("div",{})},t.prototype.componentDidMount=function(){this.setState({})},t}(E);var V=function(){function e(e,t){this.context=e,this.internalEventSource=t}return e.prototype.remove=function(){this.context.dispatch({type:"REMOVE_EVENT_SOURCE",sourceId:this.internalEventSource.sourceId})},e.prototype.refetch=function(){this.context.dispatch({type:"FETCH_EVENT_SOURCES",sourceIds:[this.internalEventSource.sourceId]})},Object.defineProperty(e.prototype,"id",{get:function(){return this.internalEventSource.publicId},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"url",{get:function(){return this.internalEventSource.meta.url},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"format",{get:function(){return this.internalEventSource.meta.format},enumerable:!1,configurable:!0}),e}();function F(e){e.parentNode&&e.parentNode.removeChild(e)}function z(e,t){if(e.closest)return e.closest(t);if(!document.documentElement.contains(e))return null;do{if(B(e,t))return e;e=e.parentElement||e.parentNode}while(null!==e&&1===e.nodeType);return null}function B(e,t){return(e.matches||e.matchesSelector||e.msMatchesSelector).call(e,t)}function j(e,t){for(var n=e instanceof HTMLElement?[e]:e,r=[],o=0;o<n.length;o+=1)for(var i=n[o].querySelectorAll(t),a=0;a<i.length;a+=1)r.push(i[a]);return r}var G=/(top|left|right|bottom|width|height)$/i;function q(e,t){for(var n in t)Y(e,n,t[n])}function Y(e,t,n){null==n?e.style[t]="":"number"==typeof n&&G.test(t)?e.style[t]=n+"px":e.style[t]=n}function Z(e){e.preventDefault()}function X(e,t){return function(n){var r=z(n.target,e);r&&t.call(r,n,r)}}function K(e,t,n,r){var o=X(n,r);return e.addEventListener(t,o),function(){e.removeEventListener(t,o)}}var J=["webkitTransitionEnd","otransitionend","oTransitionEnd","msTransitionEnd","transitionend"];function $(e,t){var n=function(r){t(r),J.forEach((function(t){e.removeEventListener(t,n)}))};J.forEach((function(t){e.addEventListener(t,n)}))}var Q=0;function ee(){return String(Q+=1)}function te(){document.body.classList.add("fc-not-allowed")}function ne(){document.body.classList.remove("fc-not-allowed")}function re(e){e.classList.add("fc-unselectable"),e.addEventListener("selectstart",Z)}function oe(e){e.classList.remove("fc-unselectable"),e.removeEventListener("selectstart",Z)}function ie(e){e.addEventListener("contextmenu",Z)}function ae(e){e.removeEventListener("contextmenu",Z)}function se(e){var t,n,r=[],o=[];for("string"==typeof e?o=e.split(/\s*,\s*/):"function"==typeof e?o=[e]:Array.isArray(e)&&(o=e),t=0;t<o.length;t+=1)"string"==typeof(n=o[t])?r.push("-"===n.charAt(0)?{field:n.substring(1),order:-1}:{field:n,order:1}):"function"==typeof n&&r.push({func:n});return r}function le(e,t,n){var r,o;for(r=0;r<n.length;r+=1)if(o=ue(e,t,n[r]))return o;return 0}function ue(e,t,n){return n.func?n.func(e,t):ce(e[n.field],t[n.field])*(n.order||1)}function ce(e,t){return e||t?null==t?-1:null==e?1:"string"==typeof e||"string"==typeof t?String(e).localeCompare(String(t)):e-t:0}function de(e,t){var n=String(e);return"000".substr(0,t-n.length)+n}function pe(e,t){return e-t}function fe(e){return e%1==0}function he(e){var t=e.querySelector(".fc-scrollgrid-shrink-frame"),n=e.querySelector(".fc-scrollgrid-shrink-cushion");if(!t)throw new Error("needs fc-scrollgrid-shrink-frame className");if(!n)throw new Error("needs fc-scrollgrid-shrink-cushion className");return e.getBoundingClientRect().width-t.getBoundingClientRect().width+n.getBoundingClientRect().width}var ve=["sun","mon","tue","wed","thu","fri","sat"];function ge(e,t){var n=xe(e);return n[2]+=7*t,_e(n)}function me(e,t){var n=xe(e);return n[2]+=t,_e(n)}function ye(e,t){var n=xe(e);return n[6]+=t,_e(n)}function Ee(e,t){return Se(e,t)/7}function Se(e,t){return(t.valueOf()-e.valueOf())/864e5}function De(e,t){var n=we(e),r=we(t);return{years:0,months:0,days:Math.round(Se(n,r)),milliseconds:t.valueOf()-r.valueOf()-(e.valueOf()-n.valueOf())}}function be(e,t){var n=Ce(e,t);return null!==n&&n%7==0?n/7:null}function Ce(e,t){return Pe(e)===Pe(t)?Math.round(Se(e,t)):null}function we(e){return _e([e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate()])}function Re(e,t,n,r){var o=_e([t,0,1+Te(t,n,r)]),i=we(e),a=Math.round(Se(o,i));return Math.floor(a/7)+1}function Te(e,t,n){var r=7+t-n;return-((7+_e([e,0,r]).getUTCDay()-t)%7)+r-1}function ke(e){return[e.getFullYear(),e.getMonth(),e.getDate(),e.getHours(),e.getMinutes(),e.getSeconds(),e.getMilliseconds()]}function Me(e){return new Date(e[0],e[1]||0,null==e[2]?1:e[2],e[3]||0,e[4]||0,e[5]||0)}function xe(e){return[e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate(),e.getUTCHours(),e.getUTCMinutes(),e.getUTCSeconds(),e.getUTCMilliseconds()]}function _e(e){return 1===e.length&&(e=e.concat([0])),new Date(Date.UTC.apply(Date,e))}function Ie(e){return!isNaN(e.valueOf())}function Pe(e){return 1e3*e.getUTCHours()*60*60+1e3*e.getUTCMinutes()*60+1e3*e.getUTCSeconds()+e.getUTCMilliseconds()}function Ne(e,t,n,r){return{instanceId:ee(),defId:e,range:t,forcedStartTzo:null==n?null:n,forcedEndTzo:null==r?null:r}}var He=Object.prototype.hasOwnProperty;function Oe(e,t){var n={};if(t)for(var r in t){for(var o=[],i=e.length-1;i>=0;i-=1){var a=e[i][r];if("object"==typeof a&&a)o.unshift(a);else if(void 0!==a){n[r]=a;break}}o.length&&(n[r]=Oe(o))}for(i=e.length-1;i>=0;i-=1){var s=e[i];for(var l in s)l in n||(n[l]=s[l])}return n}function Ae(e,t){var n={};for(var r in e)t(e[r],r)&&(n[r]=e[r]);return n}function Ue(e,t){var n={};for(var r in e)n[r]=t(e[r],r);return n}function Le(e){for(var t={},n=0,r=e;n<r.length;n++){t[r[n]]=!0}return t}function We(e){var t=[];for(var n in e)t.push(e[n]);return t}function Ve(e,t){if(e===t)return!0;for(var n in e)if(He.call(e,n)&&!(n in t))return!1;for(var n in t)if(He.call(t,n)&&e[n]!==t[n])return!1;return!0}function Fe(e,t){var n=[];for(var r in e)He.call(e,r)&&(r in t||n.push(r));for(var r in t)He.call(t,r)&&e[r]!==t[r]&&n.push(r);return n}function ze(e,t,n){if(void 0===n&&(n={}),e===t)return!0;for(var r in t)if(!(r in e)||!Be(e[r],t[r],n[r]))return!1;for(var r in e)if(!(r in t))return!1;return!0}function Be(e,t,n){return e===t||!0===n||!!n&&n(e,t)}function je(e,t,n,r){void 0===t&&(t=0),void 0===r&&(r=1);var o=[];null==n&&(n=Object.keys(e).length);for(var i=t;i<n;i+=r){var a=e[i];void 0!==a&&o.push(a)}return o}function Ge(e,t,n){var r=n.dateEnv,o=n.pluginHooks,i=n.options,a=e.defs,s=e.instances;for(var l in s=Ae(s,(function(e){return!a[e.defId].recurringDef})),a){var u=a[l];if(u.recurringDef){var c=u.recurringDef.duration;c||(c=u.allDay?i.defaultAllDayEventDuration:i.defaultTimedEventDuration);for(var d=0,p=qe(u,c,t,r,o.recurringTypes);d<p.length;d++){var f=p[d],h=Ne(l,{start:f,end:r.add(f,c)});s[h.instanceId]=h}}}return{defs:a,instances:s}}function qe(e,t,n,r,o){var i=o[e.recurringDef.typeId].expand(e.recurringDef.typeData,{start:r.subtract(n.start,t),end:n.end},r);return e.allDay&&(i=i.map(we)),i}var Ye=["years","months","days","milliseconds"],Ze=/^(-?)(?:(\d+)\.)?(\d+):(\d\d)(?::(\d\d)(?:\.(\d\d\d))?)?/;function Xe(e,t){var n;return"string"==typeof e?function(e){var t=Ze.exec(e);if(t){var n=t[1]?-1:1;return{years:0,months:0,days:n*(t[2]?parseInt(t[2],10):0),milliseconds:n*(60*(t[3]?parseInt(t[3],10):0)*60*1e3+60*(t[4]?parseInt(t[4],10):0)*1e3+1e3*(t[5]?parseInt(t[5],10):0)+(t[6]?parseInt(t[6],10):0))}}return null}(e):"object"==typeof e&&e?Ke(e):"number"==typeof e?Ke(((n={})[t||"milliseconds"]=e,n)):null}function Ke(e){var t={years:e.years||e.year||0,months:e.months||e.month||0,days:e.days||e.day||0,milliseconds:60*(e.hours||e.hour||0)*60*1e3+60*(e.minutes||e.minute||0)*1e3+1e3*(e.seconds||e.second||0)+(e.milliseconds||e.millisecond||e.ms||0)},n=e.weeks||e.week;return n&&(t.days+=7*n,t.specifiedWeeks=!0),t}function Je(e,t){return{years:e.years+t.years,months:e.months+t.months,days:e.days+t.days,milliseconds:e.milliseconds+t.milliseconds}}function $e(e,t){return{years:e.years*t,months:e.months*t,days:e.days*t,milliseconds:e.milliseconds*t}}function Qe(e){return et(e)/864e5}function et(e){return 31536e6*e.years+2592e6*e.months+864e5*e.days+e.milliseconds}function tt(e,t){for(var n=null,r=0;r<Ye.length;r+=1){var o=Ye[r];if(t[o]){var i=e[o]/t[o];if(!fe(i)||null!==n&&n!==i)return null;n=i}else if(e[o])return null}return n}function nt(e){var t=e.milliseconds;if(t){if(t%1e3!=0)return{unit:"millisecond",value:t};if(t%6e4!=0)return{unit:"second",value:t/1e3};if(t%36e5!=0)return{unit:"minute",value:t/6e4};if(t)return{unit:"hour",value:t/36e5}}return e.days?e.specifiedWeeks&&e.days%7==0?{unit:"week",value:e.days/7}:{unit:"day",value:e.days}:e.months?{unit:"month",value:e.months}:e.years?{unit:"year",value:e.years}:{unit:"millisecond",value:0}}function rt(e){return e.toISOString().replace(/T.*$/,"")}function ot(e){return de(e.getUTCHours(),2)+":"+de(e.getUTCMinutes(),2)+":"+de(e.getUTCSeconds(),2)}function it(e,t){void 0===t&&(t=!1);var n=e<0?"-":"+",r=Math.abs(e),o=Math.floor(r/60),i=Math.round(r%60);return t?n+de(o,2)+":"+de(i,2):"GMT"+n+o+(i?":"+de(i,2):"")}function at(e,t,n){if(e===t)return!0;var r,o=e.length;if(o!==t.length)return!1;for(r=0;r<o;r+=1)if(!(n?n(e[r],t[r]):e[r]===t[r]))return!1;return!0}function st(e,t,n){var r,o;return function(){for(var i=[],a=0;a<arguments.length;a++)i[a]=arguments[a];if(r){if(!at(r,i)){n&&n(o);var s=e.apply(this,i);t&&t(s,o)||(o=s)}}else o=e.apply(this,i);return r=i,o}}function lt(e,t,n){var r,o,i=this;return function(a){if(r){if(!Ve(r,a)){n&&n(o);var s=e.call(i,a);t&&t(s,o)||(o=s)}}else o=e.call(i,a);return r=a,o}}var ut={week:3,separator:0,omitZeroMinute:0,meridiem:0,omitCommas:0},ct={timeZoneName:7,era:6,year:5,month:4,day:2,weekday:2,hour:1,minute:1,second:1},dt=/\s*([ap])\.?m\.?/i,pt=/,/g,ft=/\s+/g,ht=/\u200e/g,vt=/UTC|GMT/,gt=function(){function e(e){var t={},n={},r=0;for(var o in e)o in ut?(n[o]=e[o],r=Math.max(ut[o],r)):(t[o]=e[o],o in ct&&(r=Math.max(ct[o],r)));this.standardDateProps=t,this.extendedSettings=n,this.severity=r,this.buildFormattingFunc=st(mt)}return e.prototype.format=function(e,t){return this.buildFormattingFunc(this.standardDateProps,this.extendedSettings,t)(e)},e.prototype.formatRange=function(e,t,n,r){var o=this.standardDateProps,i=this.extendedSettings,a=function(e,t,n){if(n.getMarkerYear(e)!==n.getMarkerYear(t))return 5;if(n.getMarkerMonth(e)!==n.getMarkerMonth(t))return 4;if(n.getMarkerDay(e)!==n.getMarkerDay(t))return 2;if(Pe(e)!==Pe(t))return 1;return 0}(e.marker,t.marker,n.calendarSystem);if(!a)return this.format(e,n);var s=a;!(s>1)||"numeric"!==o.year&&"2-digit"!==o.year||"numeric"!==o.month&&"2-digit"!==o.month||"numeric"!==o.day&&"2-digit"!==o.day||(s=1);var l=this.format(e,n),u=this.format(t,n);if(l===u)return l;var c=mt(function(e,t){var n={};for(var r in e)(!(r in ct)||ct[r]<=t)&&(n[r]=e[r]);return n}(o,s),i,n),d=c(e),p=c(t),f=function(e,t,n,r){var o=0;for(;o<e.length;){var i=e.indexOf(t,o);if(-1===i)break;var a=e.substr(0,i);o=i+t.length;for(var s=e.substr(o),l=0;l<n.length;){var u=n.indexOf(r,l);if(-1===u)break;var c=n.substr(0,u);l=u+r.length;var d=n.substr(l);if(a===c&&s===d)return{before:a,after:s}}}return null}(l,d,u,p),h=i.separator||r||n.defaultSeparator||"";return f?f.before+d+h+p+f.after:l+h+u},e.prototype.getLargestUnit=function(){switch(this.severity){case 7:case 6:case 5:return"year";case 4:return"month";case 3:return"week";case 2:return"day";default:return"time"}},e}();function mt(e,t,n){var o=Object.keys(e).length;return 1===o&&"short"===e.timeZoneName?function(e){return it(e.timeZoneOffset)}:0===o&&t.week?function(e){return function(e,t,n,r){var o=[];"narrow"===r?o.push(t):"short"===r&&o.push(t," ");o.push(n.simpleNumberFormat.format(e)),"rtl"===n.options.direction&&o.reverse();return o.join("")}(n.computeWeekNumber(e.marker),n.weekText,n.locale,t.week)}:function(e,t,n){e=r({},e),t=r({},t),function(e,t){e.timeZoneName&&(e.hour||(e.hour="2-digit"),e.minute||(e.minute="2-digit"));"long"===e.timeZoneName&&(e.timeZoneName="short");t.omitZeroMinute&&(e.second||e.millisecond)&&delete t.omitZeroMinute}(e,t),e.timeZone="UTC";var o,i=new Intl.DateTimeFormat(n.locale.codes,e);if(t.omitZeroMinute){var a=r({},e);delete a.minute,o=new Intl.DateTimeFormat(n.locale.codes,a)}return function(r){var a=r.marker;return function(e,t,n,r,o){e=e.replace(ht,""),"short"===n.timeZoneName&&(e=function(e,t){var n=!1;e=e.replace(vt,(function(){return n=!0,t})),n||(e+=" "+t);return e}(e,"UTC"===o.timeZone||null==t.timeZoneOffset?"UTC":it(t.timeZoneOffset)));r.omitCommas&&(e=e.replace(pt,"").trim());r.omitZeroMinute&&(e=e.replace(":00",""));!1===r.meridiem?e=e.replace(dt,"").trim():"narrow"===r.meridiem?e=e.replace(dt,(function(e,t){return t.toLocaleLowerCase()})):"short"===r.meridiem?e=e.replace(dt,(function(e,t){return t.toLocaleLowerCase()+"m"})):"lowercase"===r.meridiem&&(e=e.replace(dt,(function(e){return e.toLocaleLowerCase()})));return e=(e=e.replace(ft," ")).trim()}((o&&!a.getUTCMinutes()?o:i).format(a),r,e,t,n)}}(e,t,n)}function yt(e,t){var n=t.markerToArray(e.marker);return{marker:e.marker,timeZoneOffset:e.timeZoneOffset,array:n,year:n[0],month:n[1],day:n[2],hour:n[3],minute:n[4],second:n[5],millisecond:n[6]}}function Et(e,t,n,r){var o=yt(e,n.calendarSystem);return{date:o,start:o,end:t?yt(t,n.calendarSystem):null,timeZone:n.timeZone,localeCodes:n.locale.codes,defaultSeparator:r||n.defaultSeparator}}var St=function(){function e(e){this.cmdStr=e}return e.prototype.format=function(e,t,n){return t.cmdFormatter(this.cmdStr,Et(e,null,t,n))},e.prototype.formatRange=function(e,t,n,r){return n.cmdFormatter(this.cmdStr,Et(e,t,n,r))},e}(),Dt=function(){function e(e){this.func=e}return e.prototype.format=function(e,t,n){return this.func(Et(e,null,t,n))},e.prototype.formatRange=function(e,t,n,r){return this.func(Et(e,t,n,r))},e}();function bt(e){return"object"==typeof e&&e?new gt(e):"string"==typeof e?new St(e):"function"==typeof e?new Dt(e):null}var Ct={navLinkDayClick:Pt,navLinkWeekClick:Pt,duration:Xe,bootstrapFontAwesome:Pt,buttonIcons:Pt,customButtons:Pt,defaultAllDayEventDuration:Xe,defaultTimedEventDuration:Xe,nextDayThreshold:Xe,scrollTime:Xe,slotMinTime:Xe,slotMaxTime:Xe,dayPopoverFormat:bt,slotDuration:Xe,snapDuration:Xe,headerToolbar:Pt,footerToolbar:Pt,defaultRangeSeparator:String,titleRangeSeparator:String,forceEventDuration:Boolean,dayHeaders:Boolean,dayHeaderFormat:bt,dayHeaderClassNames:Pt,dayHeaderContent:Pt,dayHeaderDidMount:Pt,dayHeaderWillUnmount:Pt,dayCellClassNames:Pt,dayCellContent:Pt,dayCellDidMount:Pt,dayCellWillUnmount:Pt,initialView:String,aspectRatio:Number,weekends:Boolean,weekNumberCalculation:Pt,weekNumbers:Boolean,weekNumberClassNames:Pt,weekNumberContent:Pt,weekNumberDidMount:Pt,weekNumberWillUnmount:Pt,editable:Boolean,viewClassNames:Pt,viewDidMount:Pt,viewWillUnmount:Pt,nowIndicator:Boolean,nowIndicatorClassNames:Pt,nowIndicatorContent:Pt,nowIndicatorDidMount:Pt,nowIndicatorWillUnmount:Pt,showNonCurrentDates:Boolean,lazyFetching:Boolean,startParam:String,endParam:String,timeZoneParam:String,timeZone:String,locales:Pt,locale:Pt,themeSystem:String,dragRevertDuration:Number,dragScroll:Boolean,allDayMaintainDuration:Boolean,unselectAuto:Boolean,dropAccept:Pt,eventOrder:se,handleWindowResize:Boolean,windowResizeDelay:Number,longPressDelay:Number,eventDragMinDistance:Number,expandRows:Boolean,height:Pt,contentHeight:Pt,direction:String,weekNumberFormat:bt,eventResizableFromStart:Boolean,displayEventTime:Boolean,displayEventEnd:Boolean,weekText:String,progressiveEventRendering:Boolean,businessHours:Pt,initialDate:Pt,now:Pt,eventDataTransform:Pt,stickyHeaderDates:Pt,stickyFooterScrollbar:Pt,viewHeight:Pt,defaultAllDay:Boolean,eventSourceFailure:Pt,eventSourceSuccess:Pt,eventDisplay:String,eventStartEditable:Boolean,eventDurationEditable:Boolean,eventOverlap:Pt,eventConstraint:Pt,eventAllow:Pt,eventBackgroundColor:String,eventBorderColor:String,eventTextColor:String,eventColor:String,eventClassNames:Pt,eventContent:Pt,eventDidMount:Pt,eventWillUnmount:Pt,selectConstraint:Pt,selectOverlap:Pt,selectAllow:Pt,droppable:Boolean,unselectCancel:String,slotLabelFormat:Pt,slotLaneClassNames:Pt,slotLaneContent:Pt,slotLaneDidMount:Pt,slotLaneWillUnmount:Pt,slotLabelClassNames:Pt,slotLabelContent:Pt,slotLabelDidMount:Pt,slotLabelWillUnmount:Pt,dayMaxEvents:Pt,dayMaxEventRows:Pt,dayMinWidth:Number,slotLabelInterval:Xe,allDayText:String,allDayClassNames:Pt,allDayContent:Pt,allDayDidMount:Pt,allDayWillUnmount:Pt,slotMinWidth:Number,navLinks:Boolean,eventTimeFormat:bt,rerenderDelay:Number,moreLinkText:Pt,selectMinDistance:Number,selectable:Boolean,selectLongPressDelay:Number,eventLongPressDelay:Number,selectMirror:Boolean,eventMinHeight:Number,slotEventOverlap:Boolean,plugins:Pt,firstDay:Number,dayCount:Number,dateAlignment:String,dateIncrement:Xe,hiddenDays:Pt,monthMode:Boolean,fixedWeekCount:Boolean,validRange:Pt,visibleRange:Pt,titleFormat:Pt,noEventsText:String},wt={eventDisplay:"auto",defaultRangeSeparator:" - ",titleRangeSeparator:" – ",defaultTimedEventDuration:"01:00:00",defaultAllDayEventDuration:{day:1},forceEventDuration:!1,nextDayThreshold:"00:00:00",dayHeaders:!0,initialView:"",aspectRatio:1.35,headerToolbar:{start:"title",center:"",end:"today prev,next"},weekends:!0,weekNumbers:!1,weekNumberCalculation:"local",editable:!1,nowIndicator:!1,scrollTime:"06:00:00",slotMinTime:"00:00:00",slotMaxTime:"24:00:00",showNonCurrentDates:!0,lazyFetching:!0,startParam:"start",endParam:"end",timeZoneParam:"timeZone",timeZone:"local",locales:[],locale:"",themeSystem:"standard",dragRevertDuration:500,dragScroll:!0,allDayMaintainDuration:!1,unselectAuto:!0,dropAccept:"*",eventOrder:"start,-duration,allDay,title",dayPopoverFormat:{month:"long",day:"numeric",year:"numeric"},handleWindowResize:!0,windowResizeDelay:100,longPressDelay:1e3,eventDragMinDistance:5,expandRows:!1,navLinks:!1,selectable:!1},Rt={datesSet:Pt,eventsSet:Pt,eventAdd:Pt,eventChange:Pt,eventRemove:Pt,windowResize:Pt,eventClick:Pt,eventMouseEnter:Pt,eventMouseLeave:Pt,select:Pt,unselect:Pt,loading:Pt,_unmount:Pt,_beforeprint:Pt,_afterprint:Pt,_noEventDrop:Pt,_noEventResize:Pt,_resize:Pt,_scrollRequest:Pt},Tt={buttonText:Pt,views:Pt,plugins:Pt,initialEvents:Pt,events:Pt,eventSources:Pt},kt={headerToolbar:Mt,footerToolbar:Mt,buttonText:Mt,buttonIcons:Mt};function Mt(e,t){return"object"==typeof e&&"object"==typeof t&&e&&t?Ve(e,t):e===t}var xt={type:String,component:Pt,buttonText:String,buttonTextKey:String,dateProfileGeneratorClass:Pt,usesMinMaxTime:Boolean,classNames:Pt,content:Pt,didMount:Pt,willUnmount:Pt};function _t(e){return Oe(e,kt)}function It(e,t){var n={},r={};for(var o in t)o in e&&(n[o]=t[o](e[o]));for(var o in e)o in t||(r[o]=e[o]);return{refined:n,extra:r}}function Pt(e){return e}function Nt(e,t,n,r){for(var o={defs:{},instances:{}},i=Kt(n),a=0,s=e;a<s.length;a++){var l=Zt(s[a],t,n,r,i);l&&Ht(l,o)}return o}function Ht(e,t){return void 0===t&&(t={defs:{},instances:{}}),t.defs[e.def.defId]=e.def,e.instance&&(t.instances[e.instance.instanceId]=e.instance),t}function Ot(e,t){var n=e.instances[t];if(n){var r=e.defs[n.defId],o=Lt(e,(function(e){return t=r,n=e,Boolean(t.groupId&&t.groupId===n.groupId);var t,n}));return o.defs[r.defId]=r,o.instances[n.instanceId]=n,o}return{defs:{},instances:{}}}function At(){return{defs:{},instances:{}}}function Ut(e,t){return{defs:r(r({},e.defs),t.defs),instances:r(r({},e.instances),t.instances)}}function Lt(e,t){var n=Ae(e.defs,t),r=Ae(e.instances,(function(e){return n[e.defId]}));return{defs:n,instances:r}}function Wt(e){return Array.isArray(e)?e:"string"==typeof e?e.split(/\s+/):[]}var Vt={display:String,editable:Boolean,startEditable:Boolean,durationEditable:Boolean,constraint:Pt,overlap:Pt,allow:Pt,className:Wt,classNames:Wt,color:String,backgroundColor:String,borderColor:String,textColor:String},Ft={display:null,startEditable:null,durationEditable:null,constraints:[],overlap:null,allows:[],backgroundColor:"",borderColor:"",textColor:"",classNames:[]};function zt(e,t){var n=function(e,t){return Array.isArray(e)?Nt(e,null,t,!0):"object"==typeof e&&e?Nt([e],null,t,!0):null!=e?String(e):null}(e.constraint,t);return{display:e.display||null,startEditable:null!=e.startEditable?e.startEditable:e.editable,durationEditable:null!=e.durationEditable?e.durationEditable:e.editable,constraints:null!=n?[n]:[],overlap:null!=e.overlap?e.overlap:null,allows:null!=e.allow?[e.allow]:[],backgroundColor:e.backgroundColor||e.color||"",borderColor:e.borderColor||e.color||"",textColor:e.textColor||"",classNames:(e.className||[]).concat(e.classNames||[])}}function Bt(e){return e.reduce(jt,Ft)}function jt(e,t){return{display:null!=t.display?t.display:e.display,startEditable:null!=t.startEditable?t.startEditable:e.startEditable,durationEditable:null!=t.durationEditable?t.durationEditable:e.durationEditable,constraints:e.constraints.concat(t.constraints),overlap:"boolean"==typeof t.overlap?t.overlap:e.overlap,allows:e.allows.concat(t.allows),backgroundColor:t.backgroundColor||e.backgroundColor,borderColor:t.borderColor||e.borderColor,textColor:t.textColor||e.textColor,classNames:e.classNames.concat(t.classNames)}}var Gt={id:String,groupId:String,title:String,url:String},qt={start:Pt,end:Pt,date:Pt,allDay:Boolean},Yt=r(r(r({},Gt),qt),{extendedProps:Pt});function Zt(e,t,n,r,o){void 0===o&&(o=Kt(n));var i=Xt(e,n,o),a=i.refined,s=i.extra,l=function(e,t){var n=null;e&&(n=e.defaultAllDay);null==n&&(n=t.options.defaultAllDay);return n}(t,n),u=function(e,t,n,r){for(var o=0;o<r.length;o+=1){var i=r[o].parse(e,n);if(i){var a=e.allDay;return null==a&&null==(a=t)&&null==(a=i.allDayGuess)&&(a=!1),{allDay:a,duration:i.duration,typeData:i.typeData,typeId:o}}}return null}(a,l,n.dateEnv,n.pluginHooks.recurringTypes);if(u)return(c=Jt(a,s,t?t.sourceId:"",u.allDay,Boolean(u.duration),n)).recurringDef={typeId:u.typeId,typeData:u.typeData,duration:u.duration},{def:c,instance:null};var c,d=function(e,t,n,r){var o,i,a=e.allDay,s=null,l=!1,u=null,c=null!=e.start?e.start:e.date;if(o=n.dateEnv.createMarkerMeta(c))s=o.marker;else if(!r)return null;null!=e.end&&(i=n.dateEnv.createMarkerMeta(e.end));null==a&&(a=null!=t?t:(!o||o.isTimeUnspecified)&&(!i||i.isTimeUnspecified));a&&s&&(s=we(s));i&&(u=i.marker,a&&(u=we(u)),s&&u<=s&&(u=null));u?l=!0:r||(l=n.options.forceEventDuration||!1,u=n.dateEnv.add(s,a?n.options.defaultAllDayEventDuration:n.options.defaultTimedEventDuration));return{allDay:a,hasEnd:l,range:{start:s,end:u},forcedStartTzo:o?o.forcedTzo:null,forcedEndTzo:i?i.forcedTzo:null}}(a,l,n,r);return d?{def:c=Jt(a,s,t?t.sourceId:"",d.allDay,d.hasEnd,n),instance:Ne(c.defId,d.range,d.forcedStartTzo,d.forcedEndTzo)}:null}function Xt(e,t,n){return void 0===n&&(n=Kt(t)),It(e,n)}function Kt(e){return r(r(r({},Vt),Yt),e.pluginHooks.eventRefiners)}function Jt(e,t,n,o,i,a){for(var s={title:e.title||"",groupId:e.groupId||"",publicId:e.id||"",url:e.url||"",recurringDef:null,defId:ee(),sourceId:n,allDay:o,hasEnd:i,ui:zt(e,a),extendedProps:r(r({},e.extendedProps||{}),t)},l=0,u=a.pluginHooks.eventDefMemberAdders;l<u.length;l++){var c=u[l];r(s,c(e))}return Object.freeze(s.ui.classNames),Object.freeze(s.extendedProps),s}function $t(e){var t=Math.floor(Se(e.start,e.end))||1,n=we(e.start);return{start:n,end:me(n,t)}}function Qt(e,t){void 0===t&&(t=Xe(0));var n=null,r=null;if(e.end){r=we(e.end);var o=e.end.valueOf()-r.valueOf();o&&o>=et(t)&&(r=me(r,1))}return e.start&&(n=we(e.start),r&&r<=n&&(r=me(n,1))),{start:n,end:r}}function en(e){var t=Qt(e);return Se(t.start,t.end)>1}function tn(e,t,n,r){return"year"===r?Xe(n.diffWholeYears(e,t),"year"):"month"===r?Xe(n.diffWholeMonths(e,t),"month"):De(e,t)}function nn(e,t){var n,r,o=[],i=t.start;for(e.sort(rn),n=0;n<e.length;n+=1)(r=e[n]).start>i&&o.push({start:i,end:r.start}),r.end>i&&(i=r.end);return i<t.end&&o.push({start:i,end:t.end}),o}function rn(e,t){return e.start.valueOf()-t.start.valueOf()}function on(e,t){var n=e.start,r=e.end,o=null;return null!==t.start&&(n=null===n?t.start:new Date(Math.max(n.valueOf(),t.start.valueOf()))),null!=t.end&&(r=null===r?t.end:new Date(Math.min(r.valueOf(),t.end.valueOf()))),(null===n||null===r||n<r)&&(o={start:n,end:r}),o}function an(e,t){return(null===e.start?null:e.start.valueOf())===(null===t.start?null:t.start.valueOf())&&(null===e.end?null:e.end.valueOf())===(null===t.end?null:t.end.valueOf())}function sn(e,t){return(null===e.end||null===t.start||e.end>t.start)&&(null===e.start||null===t.end||e.start<t.end)}function ln(e,t){return(null===e.start||null!==t.start&&t.start>=e.start)&&(null===e.end||null!==t.end&&t.end<=e.end)}function un(e,t){return(null===e.start||t>=e.start)&&(null===e.end||t<e.end)}function cn(e,t,n,r){var o={},i={},a={},s=[],l=[],u=hn(e.defs,t);for(var c in e.defs){"inverse-background"===(f=u[(S=e.defs[c]).defId]).display&&(S.groupId?(o[S.groupId]=[],a[S.groupId]||(a[S.groupId]=S)):i[c]=[])}for(var d in e.instances){var p=e.instances[d],f=u[(S=e.defs[p.defId]).defId],h=p.range,v=!S.allDay&&r?Qt(h,r):h,g=on(v,n);g&&("inverse-background"===f.display?S.groupId?o[S.groupId].push(g):i[p.defId].push(g):"none"!==f.display&&("background"===f.display?s:l).push({def:S,ui:f,instance:p,range:g,isStart:v.start&&v.start.valueOf()===g.start.valueOf(),isEnd:v.end&&v.end.valueOf()===g.end.valueOf()}))}for(var m in o)for(var y=0,E=nn(o[m],n);y<E.length;y++){var S,D=E[y];f=u[(S=a[m]).defId];s.push({def:S,ui:f,instance:null,range:D,isStart:!1,isEnd:!1})}for(var c in i)for(var b=0,C=nn(i[c],n);b<C.length;b++){D=C[b];s.push({def:e.defs[c],ui:u[c],instance:null,range:D,isStart:!1,isEnd:!1})}return{bg:s,fg:l}}function dn(e){return"background"===e.ui.display||"inverse-background"===e.ui.display}function pn(e,t){e.fcSeg=t}function fn(e){return e.fcSeg||e.parentNode.fcSeg||null}function hn(e,t){return Ue(e,(function(e){return vn(e,t)}))}function vn(e,t){var n=[];return t[""]&&n.push(t[""]),t[e.defId]&&n.push(t[e.defId]),n.push(e.ui),Bt(n)}function gn(e,t){var n=e.map(mn);return n.sort((function(e,n){return le(e,n,t)})),n.map((function(e){return e._seg}))}function mn(e){var t=e.eventRange,n=t.def,o=t.instance?t.instance.range:t.range,i=o.start?o.start.valueOf():0,a=o.end?o.end.valueOf():0;return r(r(r({},n.extendedProps),n),{id:n.publicId,start:i,end:a,duration:a-i,allDay:Number(n.allDay),_seg:e})}function yn(e,t){for(var n=t.pluginHooks.isDraggableTransformers,r=e.eventRange,o=r.def,i=r.ui,a=i.startEditable,s=0,l=n;s<l.length;s++){a=(0,l[s])(a,o,i,t)}return a}function En(e,t){return e.isStart&&e.eventRange.ui.durationEditable&&t.options.eventResizableFromStart}function Sn(e,t){return e.isEnd&&e.eventRange.ui.durationEditable}function Dn(e,t,n,r,o,i,a){var s=n.dateEnv,l=n.options,u=l.displayEventTime,c=l.displayEventEnd,d=e.eventRange.def,p=e.eventRange.instance;if(null==u&&(u=!1!==r),null==c&&(c=!1!==o),u&&!d.allDay&&(e.isStart||e.isEnd)){var f=i||(e.isStart?p.range.start:e.start||e.eventRange.range.start),h=a||(e.isEnd?p.range.end:e.end||e.eventRange.range.end);return c&&d.hasEnd?s.formatRange(f,h,t,{forcedStartTzo:i?null:p.forcedStartTzo,forcedEndTzo:a?null:p.forcedEndTzo}):s.format(f,t,{forcedTzo:i?null:p.forcedStartTzo})}return""}function bn(e,t,n){var r=e.eventRange.range;return{isPast:r.end<(n||t.start),isFuture:r.start>=(n||t.end),isToday:t&&un(t,r.start)}}function Cn(e){var t=["fc-event"];return e.isMirror&&t.push("fc-event-mirror"),e.isDraggable&&t.push("fc-event-draggable"),(e.isStartResizable||e.isEndResizable)&&t.push("fc-event-resizable"),e.isDragging&&t.push("fc-event-dragging"),e.isResizing&&t.push("fc-event-resizing"),e.isSelected&&t.push("fc-event-selected"),e.isStart&&t.push("fc-event-start"),e.isEnd&&t.push("fc-event-end"),e.isPast&&t.push("fc-event-past"),e.isToday&&t.push("fc-event-today"),e.isFuture&&t.push("fc-event-future"),t}function wn(e){return e.instance?e.instance.instanceId:e.def.defId+":"+e.range.start.toISOString()}var Rn={start:Pt,end:Pt,allDay:Boolean};function Tn(e,t,n){var o=function(e,t){var n=It(e,Rn),o=n.refined,i=n.extra,a=o.start?t.createMarkerMeta(o.start):null,s=o.end?t.createMarkerMeta(o.end):null,l=o.allDay;null==l&&(l=a&&a.isTimeUnspecified&&(!s||s.isTimeUnspecified));return r({range:{start:a?a.marker:null,end:s?s.marker:null},allDay:l},i)}(e,t),i=o.range;if(!i.start)return null;if(!i.end){if(null==n)return null;i.end=t.add(i.start,n)}return o}function kn(e,t){return an(e.range,t.range)&&e.allDay===t.allDay&&function(e,t){for(var n in t)if("range"!==n&&"allDay"!==n&&e[n]!==t[n])return!1;for(var n in e)if(!(n in t))return!1;return!0}(e,t)}function Mn(e,t,n){return r(r({},xn(e,t,n)),{timeZone:t.timeZone})}function xn(e,t,n){return{start:t.toDate(e.start),end:t.toDate(e.end),startStr:t.formatIso(e.start,{omitTime:n}),endStr:t.formatIso(e.end,{omitTime:n})}}function _n(e,t,n){var r=Xt({editable:!1},n),o=Jt(r.refined,r.extra,"",e.allDay,!0,n);return{def:o,ui:vn(o,t),instance:Ne(o.defId,e.range),range:e.range,isStart:!0,isEnd:!0}}function In(e,t,n){n.emitter.trigger("select",r(r({},Pn(e,n)),{jsEvent:t?t.origEvent:null,view:n.viewApi||n.calendarApi.view}))}function Pn(e,t){for(var n,o,i={},a=0,s=t.pluginHooks.dateSpanTransforms;a<s.length;a++){var l=s[a];r(i,l(e,t))}return r(i,(n=e,o=t.dateEnv,r(r({},xn(n.range,o,n.allDay)),{allDay:n.allDay}))),i}function Nn(e,t,n){var r=n.dateEnv,o=n.options,i=t;return e?(i=we(i),i=r.add(i,o.defaultAllDayEventDuration)):i=r.add(i,o.defaultTimedEventDuration),i}function Hn(e,t,n,r){var o=hn(e.defs,t),i={defs:{},instances:{}};for(var a in e.defs){var s=e.defs[a];i.defs[a]=On(s,o[a],n,r)}for(var l in e.instances){var u=e.instances[l];s=i.defs[u.defId];i.instances[l]=An(u,s,o[u.defId],n,r)}return i}function On(e,t,n,o){var i=n.standardProps||{};null==i.hasEnd&&t.durationEditable&&(n.startDelta||n.endDelta)&&(i.hasEnd=!0);var a=r(r(r({},e),i),{ui:r(r({},e.ui),i.ui)});n.extendedProps&&(a.extendedProps=r(r({},a.extendedProps),n.extendedProps));for(var s=0,l=o.pluginHooks.eventDefMutationAppliers;s<l.length;s++){(0,l[s])(a,n,o)}return!a.hasEnd&&o.options.forceEventDuration&&(a.hasEnd=!0),a}function An(e,t,n,o,i){var a=i.dateEnv,s=o.standardProps&&!0===o.standardProps.allDay,l=o.standardProps&&!1===o.standardProps.hasEnd,u=r({},e);return s&&(u.range=$t(u.range)),o.datesDelta&&n.startEditable&&(u.range={start:a.add(u.range.start,o.datesDelta),end:a.add(u.range.end,o.datesDelta)}),o.startDelta&&n.durationEditable&&(u.range={start:a.add(u.range.start,o.startDelta),end:u.range.end}),o.endDelta&&n.durationEditable&&(u.range={start:u.range.start,end:a.add(u.range.end,o.endDelta)}),l&&(u.range={start:u.range.start,end:Nn(t.allDay,u.range.start,i)}),t.allDay&&(u.range={start:we(u.range.start),end:we(u.range.end)}),u.range.end<u.range.start&&(u.range.end=Nn(t.allDay,u.range.start,i)),u}var Un=function(){function e(e,t,n){this.type=e,this.getCurrentData=t,this.dateEnv=n}return Object.defineProperty(e.prototype,"calendar",{get:function(){return this.getCurrentData().calendarApi},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"title",{get:function(){return this.getCurrentData().viewTitle},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"activeStart",{get:function(){return this.dateEnv.toDate(this.getCurrentData().dateProfile.activeRange.start)},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"activeEnd",{get:function(){return this.dateEnv.toDate(this.getCurrentData().dateProfile.activeRange.end)},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"currentStart",{get:function(){return this.dateEnv.toDate(this.getCurrentData().dateProfile.currentRange.start)},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"currentEnd",{get:function(){return this.dateEnv.toDate(this.getCurrentData().dateProfile.currentRange.end)},enumerable:!1,configurable:!0}),e.prototype.getOption=function(e){return this.getCurrentData().options[e]},e}(),Ln={id:String,defaultAllDay:Boolean,url:String,format:String,events:Pt,eventDataTransform:Pt,success:Pt,failure:Pt};function Wn(e,t,n){var r;if(void 0===n&&(n=Vn(t)),"string"==typeof e?r={url:e}:"function"==typeof e||Array.isArray(e)?r={events:e}:"object"==typeof e&&e&&(r=e),r){var o=It(r,n),i=o.refined,a=o.extra,s=function(e,t){for(var n=t.pluginHooks.eventSourceDefs,r=n.length-1;r>=0;r-=1){var o=n[r].parseMeta(e);if(o)return{sourceDefId:r,meta:o}}return null}(i,t);if(s)return{_raw:e,isFetching:!1,latestFetchId:"",fetchRange:null,defaultAllDay:i.defaultAllDay,eventDataTransform:i.eventDataTransform,success:i.success,failure:i.failure,publicId:i.id||"",sourceId:ee(),sourceDefId:s.sourceDefId,meta:s.meta,ui:zt(i,t),extendedProps:a}}return null}function Vn(e){return r(r(r({},Vt),Ln),e.pluginHooks.eventSourceRefiners)}function Fn(e,t){return"function"==typeof e&&(e=e()),null==e?t.createNowMarker():t.createMarker(e)}var zn=function(){function e(){}return e.prototype.getCurrentData=function(){return this.currentDataManager.getCurrentData()},e.prototype.dispatch=function(e){return this.currentDataManager.dispatch(e)},Object.defineProperty(e.prototype,"view",{get:function(){return this.getCurrentData().viewApi},enumerable:!1,configurable:!0}),e.prototype.batchRendering=function(e){e()},e.prototype.updateSize=function(){this.trigger("_resize",!0)},e.prototype.setOption=function(e,t){this.dispatch({type:"SET_OPTION",optionName:e,rawOptionValue:t})},e.prototype.getOption=function(e){return this.currentDataManager.currentCalendarOptionsInput[e]},e.prototype.getAvailableLocaleCodes=function(){return Object.keys(this.getCurrentData().availableRawLocales)},e.prototype.on=function(e,t){var n=this.currentDataManager;n.currentCalendarOptionsRefiners[e]?n.emitter.on(e,t):console.warn("Unknown listener name '"+e+"'")},e.prototype.off=function(e,t){this.currentDataManager.emitter.off(e,t)},e.prototype.trigger=function(e){for(var t,n=[],r=1;r<arguments.length;r++)n[r-1]=arguments[r];(t=this.currentDataManager.emitter).trigger.apply(t,o([e],n))},e.prototype.changeView=function(e,t){var n=this;this.batchRendering((function(){if(n.unselect(),t)if(t.start&&t.end)n.dispatch({type:"CHANGE_VIEW_TYPE",viewType:e}),n.dispatch({type:"SET_OPTION",optionName:"visibleRange",rawOptionValue:t});else{var r=n.getCurrentData().dateEnv;n.dispatch({type:"CHANGE_VIEW_TYPE",viewType:e,dateMarker:r.createMarker(t)})}else n.dispatch({type:"CHANGE_VIEW_TYPE",viewType:e})}))},e.prototype.zoomTo=function(e,t){var n;t=t||"day",n=this.getCurrentData().viewSpecs[t]||this.getUnitViewSpec(t),this.unselect(),n?this.dispatch({type:"CHANGE_VIEW_TYPE",viewType:n.type,dateMarker:e}):this.dispatch({type:"CHANGE_DATE",dateMarker:e})},e.prototype.getUnitViewSpec=function(e){var t,n,r=this.getCurrentData(),o=r.viewSpecs,i=r.toolbarConfig,a=[].concat(i.viewsWithButtons);for(var s in o)a.push(s);for(t=0;t<a.length;t+=1)if((n=o[a[t]])&&n.singleUnit===e)return n;return null},e.prototype.prev=function(){this.unselect(),this.dispatch({type:"PREV"})},e.prototype.next=function(){this.unselect(),this.dispatch({type:"NEXT"})},e.prototype.prevYear=function(){var e=this.getCurrentData();this.unselect(),this.dispatch({type:"CHANGE_DATE",dateMarker:e.dateEnv.addYears(e.currentDate,-1)})},e.prototype.nextYear=function(){var e=this.getCurrentData();this.unselect(),this.dispatch({type:"CHANGE_DATE",dateMarker:e.dateEnv.addYears(e.currentDate,1)})},e.prototype.today=function(){var e=this.getCurrentData();this.unselect(),this.dispatch({type:"CHANGE_DATE",dateMarker:Fn(e.calendarOptions.now,e.dateEnv)})},e.prototype.gotoDate=function(e){var t=this.getCurrentData();this.unselect(),this.dispatch({type:"CHANGE_DATE",dateMarker:t.dateEnv.createMarker(e)})},e.prototype.incrementDate=function(e){var t=this.getCurrentData(),n=Xe(e);n&&(this.unselect(),this.dispatch({type:"CHANGE_DATE",dateMarker:t.dateEnv.add(t.currentDate,n)}))},e.prototype.getDate=function(){var e=this.getCurrentData();return e.dateEnv.toDate(e.currentDate)},e.prototype.formatDate=function(e,t){var n=this.getCurrentData().dateEnv;return n.format(n.createMarker(e),bt(t))},e.prototype.formatRange=function(e,t,n){var r=this.getCurrentData().dateEnv;return r.formatRange(r.createMarker(e),r.createMarker(t),bt(n),n)},e.prototype.formatIso=function(e,t){var n=this.getCurrentData().dateEnv;return n.formatIso(n.createMarker(e),{omitTime:t})},e.prototype.select=function(e,t){var n;n=null==t?null!=e.start?e:{start:e,end:null}:{start:e,end:t};var r=this.getCurrentData(),o=Tn(n,r.dateEnv,Xe({days:1}));o&&(this.dispatch({type:"SELECT_DATES",selection:o}),In(o,null,r))},e.prototype.unselect=function(e){var t=this.getCurrentData();t.dateSelection&&(this.dispatch({type:"UNSELECT_DATES"}),function(e,t){t.emitter.trigger("unselect",{jsEvent:e?e.origEvent:null,view:t.viewApi||t.calendarApi.view})}(e,t))},e.prototype.addEvent=function(e,t){if(e instanceof Bn){var n=e._def,r=e._instance;return this.getCurrentData().eventStore.defs[n.defId]||(this.dispatch({type:"ADD_EVENTS",eventStore:Ht({def:n,instance:r})}),this.triggerEventAdd(e)),e}var o,i=this.getCurrentData();if(t instanceof V)o=t.internalEventSource;else if("boolean"==typeof t)t&&(o=We(i.eventSources)[0]);else if(null!=t){var a=this.getEventSourceById(t);if(!a)return console.warn('Could not find an event source with ID "'+t+'"'),null;o=a.internalEventSource}var s=Zt(e,o,i,!1);if(s){var l=new Bn(i,s.def,s.def.recurringDef?null:s.instance);return this.dispatch({type:"ADD_EVENTS",eventStore:Ht(s)}),this.triggerEventAdd(l),l}return null},e.prototype.triggerEventAdd=function(e){var t=this;this.getCurrentData().emitter.trigger("eventAdd",{event:e,relatedEvents:[],revert:function(){t.dispatch({type:"REMOVE_EVENTS",eventStore:jn(e)})}})},e.prototype.getEventById=function(e){var t=this.getCurrentData(),n=t.eventStore,r=n.defs,o=n.instances;for(var i in e=String(e),r){var a=r[i];if(a.publicId===e){if(a.recurringDef)return new Bn(t,a,null);for(var s in o){var l=o[s];if(l.defId===a.defId)return new Bn(t,a,l)}}}return null},e.prototype.getEvents=function(){var e=this.getCurrentData();return Gn(e.eventStore,e)},e.prototype.removeAllEvents=function(){this.dispatch({type:"REMOVE_ALL_EVENTS"})},e.prototype.getEventSources=function(){var e=this.getCurrentData(),t=e.eventSources,n=[];for(var r in t)n.push(new V(e,t[r]));return n},e.prototype.getEventSourceById=function(e){var t=this.getCurrentData(),n=t.eventSources;for(var r in e=String(e),n)if(n[r].publicId===e)return new V(t,n[r]);return null},e.prototype.addEventSource=function(e){var t=this.getCurrentData();if(e instanceof V)return t.eventSources[e.internalEventSource.sourceId]||this.dispatch({type:"ADD_EVENT_SOURCES",sources:[e.internalEventSource]}),e;var n=Wn(e,t);return n?(this.dispatch({type:"ADD_EVENT_SOURCES",sources:[n]}),new V(t,n)):null},e.prototype.removeAllEventSources=function(){this.dispatch({type:"REMOVE_ALL_EVENT_SOURCES"})},e.prototype.refetchEvents=function(){this.dispatch({type:"FETCH_EVENT_SOURCES"})},e.prototype.scrollToTime=function(e){var t=Xe(e);t&&this.trigger("_scrollRequest",{time:t})},e}(),Bn=function(){function e(e,t,n){this._context=e,this._def=t,this._instance=n||null}return e.prototype.setProp=function(e,t){var n,r;if(e in qt)console.warn("Could not set date-related prop 'name'. Use one of the date-related methods instead.");else if(e in Gt)t=Gt[e](t),this.mutate({standardProps:(n={},n[e]=t,n)});else if(e in Vt){var o=Vt[e](t);"color"===e?o={backgroundColor:t,borderColor:t}:"editable"===e?o={startEditable:t,durationEditable:t}:((r={})[e]=t,o=r),this.mutate({standardProps:{ui:o}})}else console.warn("Could not set prop '"+e+"'. Use setExtendedProp instead.")},e.prototype.setExtendedProp=function(e,t){var n;this.mutate({extendedProps:(n={},n[e]=t,n)})},e.prototype.setStart=function(e,t){void 0===t&&(t={});var n=this._context.dateEnv,r=n.createMarker(e);if(r&&this._instance){var o=tn(this._instance.range.start,r,n,t.granularity);t.maintainDuration?this.mutate({datesDelta:o}):this.mutate({startDelta:o})}},e.prototype.setEnd=function(e,t){void 0===t&&(t={});var n,r=this._context.dateEnv;if((null==e||(n=r.createMarker(e)))&&this._instance)if(n){var o=tn(this._instance.range.end,n,r,t.granularity);this.mutate({endDelta:o})}else this.mutate({standardProps:{hasEnd:!1}})},e.prototype.setDates=function(e,t,n){void 0===n&&(n={});var r,o,i,a=this._context.dateEnv,s={allDay:n.allDay},l=a.createMarker(e);if(l&&((null==t||(r=a.createMarker(t)))&&this._instance)){var u=this._instance.range;!0===n.allDay&&(u=$t(u));var c=tn(u.start,l,a,n.granularity);if(r){var d=tn(u.end,r,a,n.granularity);i=d,(o=c).years===i.years&&o.months===i.months&&o.days===i.days&&o.milliseconds===i.milliseconds?this.mutate({datesDelta:c,standardProps:s}):this.mutate({startDelta:c,endDelta:d,standardProps:s})}else s.hasEnd=!1,this.mutate({datesDelta:c,standardProps:s})}},e.prototype.moveStart=function(e){var t=Xe(e);t&&this.mutate({startDelta:t})},e.prototype.moveEnd=function(e){var t=Xe(e);t&&this.mutate({endDelta:t})},e.prototype.moveDates=function(e){var t=Xe(e);t&&this.mutate({datesDelta:t})},e.prototype.setAllDay=function(e,t){void 0===t&&(t={});var n={allDay:e},r=t.maintainDuration;null==r&&(r=this._context.options.allDayMaintainDuration),this._def.allDay!==e&&(n.hasEnd=r),this.mutate({standardProps:n})},e.prototype.formatRange=function(e){var t=this._context.dateEnv,n=this._instance,r=bt(e);return this._def.hasEnd?t.formatRange(n.range.start,n.range.end,r,{forcedStartTzo:n.forcedStartTzo,forcedEndTzo:n.forcedEndTzo}):t.format(n.range.start,r,{forcedTzo:n.forcedStartTzo})},e.prototype.mutate=function(t){var n=this._instance;if(n){var r=this._def,o=this._context,i=o.getCurrentData().eventStore,a=Ot(i,n.instanceId);a=Hn(a,{"":{display:"",startEditable:!0,durationEditable:!0,constraints:[],overlap:null,allows:[],backgroundColor:"",borderColor:"",textColor:"",classNames:[]}},t,o);var s=new e(o,r,n);this._def=a.defs[r.defId],this._instance=a.instances[n.instanceId],o.dispatch({type:"MERGE_EVENTS",eventStore:a}),o.emitter.trigger("eventChange",{oldEvent:s,event:this,relatedEvents:Gn(a,o,n),revert:function(){o.dispatch({type:"RESET_EVENTS",eventStore:i})}})}},e.prototype.remove=function(){var e=this._context,t=jn(this);e.dispatch({type:"REMOVE_EVENTS",eventStore:t}),e.emitter.trigger("eventRemove",{event:this,relatedEvents:[],revert:function(){e.dispatch({type:"MERGE_EVENTS",eventStore:t})}})},Object.defineProperty(e.prototype,"source",{get:function(){var e=this._def.sourceId;return e?new V(this._context,this._context.getCurrentData().eventSources[e]):null},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"start",{get:function(){return this._instance?this._context.dateEnv.toDate(this._instance.range.start):null},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"end",{get:function(){return this._instance&&this._def.hasEnd?this._context.dateEnv.toDate(this._instance.range.end):null},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"startStr",{get:function(){var e=this._instance;return e?this._context.dateEnv.formatIso(e.range.start,{omitTime:this._def.allDay,forcedTzo:e.forcedStartTzo}):""},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"endStr",{get:function(){var e=this._instance;return e&&this._def.hasEnd?this._context.dateEnv.formatIso(e.range.end,{omitTime:this._def.allDay,forcedTzo:e.forcedEndTzo}):""},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"id",{get:function(){return this._def.publicId},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"groupId",{get:function(){return this._def.groupId},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"allDay",{get:function(){return this._def.allDay},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"title",{get:function(){return this._def.title},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"url",{get:function(){return this._def.url},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"display",{get:function(){return this._def.ui.display||"auto"},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"startEditable",{get:function(){return this._def.ui.startEditable},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"durationEditable",{get:function(){return this._def.ui.durationEditable},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"constraint",{get:function(){return this._def.ui.constraints[0]||null},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"overlap",{get:function(){return this._def.ui.overlap},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"allow",{get:function(){return this._def.ui.allows[0]||null},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"backgroundColor",{get:function(){return this._def.ui.backgroundColor},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"borderColor",{get:function(){return this._def.ui.borderColor},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"textColor",{get:function(){return this._def.ui.textColor},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"classNames",{get:function(){return this._def.ui.classNames},enumerable:!1,configurable:!0}),Object.defineProperty(e.prototype,"extendedProps",{get:function(){return this._def.extendedProps},enumerable:!1,configurable:!0}),e.prototype.toPlainObject=function(e){void 0===e&&(e={});var t=this._def,n=t.ui,o=this.startStr,i=this.endStr,a={};return t.title&&(a.title=t.title),o&&(a.start=o),i&&(a.end=i),t.publicId&&(a.id=t.publicId),t.groupId&&(a.groupId=t.groupId),t.url&&(a.url=t.url),n.display&&"auto"!==n.display&&(a.display=n.display),e.collapseColor&&n.backgroundColor&&n.backgroundColor===n.borderColor?a.color=n.backgroundColor:(n.backgroundColor&&(a.backgroundColor=n.backgroundColor),n.borderColor&&(a.borderColor=n.borderColor)),n.textColor&&(a.textColor=n.textColor),n.classNames.length&&(a.classNames=n.classNames),Object.keys(t.extendedProps).length&&(e.collapseExtendedProps?r(a,t.extendedProps):a.extendedProps=t.extendedProps),a},e.prototype.toJSON=function(){return this.toPlainObject()},e}();function jn(e){var t,n,r=e._def,o=e._instance;return{defs:(t={},t[r.defId]=r,t),instances:o?(n={},n[o.instanceId]=o,n):{}}}function Gn(e,t,n){var r=e.defs,o=e.instances,i=[],a=n?n.instanceId:"";for(var s in o){var l=o[s],u=r[l.defId];l.instanceId!==a&&i.push(new Bn(t,u,l))}return i}var qn={};var Yn,Zn=function(){function e(){}return e.prototype.getMarkerYear=function(e){return e.getUTCFullYear()},e.prototype.getMarkerMonth=function(e){return e.getUTCMonth()},e.prototype.getMarkerDay=function(e){return e.getUTCDate()},e.prototype.arrayToMarker=function(e){return _e(e)},e.prototype.markerToArray=function(e){return xe(e)},e}();Yn=Zn,qn["gregory"]=Yn;var Xn=/^\s*(\d{4})(-?(\d{2})(-?(\d{2})([T ](\d{2}):?(\d{2})(:?(\d{2})(\.(\d+))?)?(Z|(([-+])(\d{2})(:?(\d{2}))?))?)?)?)?$/;function Kn(e){var t=Xn.exec(e);if(t){var n=new Date(Date.UTC(Number(t[1]),t[3]?Number(t[3])-1:0,Number(t[5]||1),Number(t[7]||0),Number(t[8]||0),Number(t[10]||0),t[12]?1e3*Number("0."+t[12]):0));if(Ie(n)){var r=null;return t[13]&&(r=("-"===t[15]?-1:1)*(60*Number(t[16]||0)+Number(t[18]||0))),{marker:n,isTimeUnspecified:!t[6],timeZoneOffset:r}}}return null}var Jn=function(){function e(e){var t=this.timeZone=e.timeZone,n="local"!==t&&"UTC"!==t;e.namedTimeZoneImpl&&n&&(this.namedTimeZoneImpl=new e.namedTimeZoneImpl(t)),this.canComputeOffset=Boolean(!n||this.namedTimeZoneImpl),this.calendarSystem=function(e){return new qn[e]}(e.calendarSystem),this.locale=e.locale,this.weekDow=e.locale.week.dow,this.weekDoy=e.locale.week.doy,"ISO"===e.weekNumberCalculation&&(this.weekDow=1,this.weekDoy=4),"number"==typeof e.firstDay&&(this.weekDow=e.firstDay),"function"==typeof e.weekNumberCalculation&&(this.weekNumberFunc=e.weekNumberCalculation),this.weekText=null!=e.weekText?e.weekText:e.locale.options.weekText,this.cmdFormatter=e.cmdFormatter,this.defaultSeparator=e.defaultSeparator}return e.prototype.createMarker=function(e){var t=this.createMarkerMeta(e);return null===t?null:t.marker},e.prototype.createNowMarker=function(){return this.canComputeOffset?this.timestampToMarker((new Date).valueOf()):_e(ke(new Date))},e.prototype.createMarkerMeta=function(e){if("string"==typeof e)return this.parse(e);var t=null;return"number"==typeof e?t=this.timestampToMarker(e):e instanceof Date?(e=e.valueOf(),isNaN(e)||(t=this.timestampToMarker(e))):Array.isArray(e)&&(t=_e(e)),null!==t&&Ie(t)?{marker:t,isTimeUnspecified:!1,forcedTzo:null}:null},e.prototype.parse=function(e){var t=Kn(e);if(null===t)return null;var n=t.marker,r=null;return null!==t.timeZoneOffset&&(this.canComputeOffset?n=this.timestampToMarker(n.valueOf()-60*t.timeZoneOffset*1e3):r=t.timeZoneOffset),{marker:n,isTimeUnspecified:t.isTimeUnspecified,forcedTzo:r}},e.prototype.getYear=function(e){return this.calendarSystem.getMarkerYear(e)},e.prototype.getMonth=function(e){return this.calendarSystem.getMarkerMonth(e)},e.prototype.add=function(e,t){var n=this.calendarSystem.markerToArray(e);return n[0]+=t.years,n[1]+=t.months,n[2]+=t.days,n[6]+=t.milliseconds,this.calendarSystem.arrayToMarker(n)},e.prototype.subtract=function(e,t){var n=this.calendarSystem.markerToArray(e);return n[0]-=t.years,n[1]-=t.months,n[2]-=t.days,n[6]-=t.milliseconds,this.calendarSystem.arrayToMarker(n)},e.prototype.addYears=function(e,t){var n=this.calendarSystem.markerToArray(e);return n[0]+=t,this.calendarSystem.arrayToMarker(n)},e.prototype.addMonths=function(e,t){var n=this.calendarSystem.markerToArray(e);return n[1]+=t,this.calendarSystem.arrayToMarker(n)},e.prototype.diffWholeYears=function(e,t){var n=this.calendarSystem;return Pe(e)===Pe(t)&&n.getMarkerDay(e)===n.getMarkerDay(t)&&n.getMarkerMonth(e)===n.getMarkerMonth(t)?n.getMarkerYear(t)-n.getMarkerYear(e):null},e.prototype.diffWholeMonths=function(e,t){var n=this.calendarSystem;return Pe(e)===Pe(t)&&n.getMarkerDay(e)===n.getMarkerDay(t)?n.getMarkerMonth(t)-n.getMarkerMonth(e)+12*(n.getMarkerYear(t)-n.getMarkerYear(e)):null},e.prototype.greatestWholeUnit=function(e,t){var n=this.diffWholeYears(e,t);return null!==n?{unit:"year",value:n}:null!==(n=this.diffWholeMonths(e,t))?{unit:"month",value:n}:null!==(n=be(e,t))?{unit:"week",value:n}:null!==(n=Ce(e,t))?{unit:"day",value:n}:fe(n=function(e,t){return(t.valueOf()-e.valueOf())/36e5}(e,t))?{unit:"hour",value:n}:fe(n=function(e,t){return(t.valueOf()-e.valueOf())/6e4}(e,t))?{unit:"minute",value:n}:fe(n=function(e,t){return(t.valueOf()-e.valueOf())/1e3}(e,t))?{unit:"second",value:n}:{unit:"millisecond",value:t.valueOf()-e.valueOf()}},e.prototype.countDurationsBetween=function(e,t,n){var r;return n.years&&null!==(r=this.diffWholeYears(e,t))?r/(Qe(n)/365):n.months&&null!==(r=this.diffWholeMonths(e,t))?r/function(e){return Qe(e)/30}(n):n.days&&null!==(r=Ce(e,t))?r/Qe(n):(t.valueOf()-e.valueOf())/et(n)},e.prototype.startOf=function(e,t){return"year"===t?this.startOfYear(e):"month"===t?this.startOfMonth(e):"week"===t?this.startOfWeek(e):"day"===t?we(e):"hour"===t?function(e){return _e([e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate(),e.getUTCHours()])}(e):"minute"===t?function(e){return _e([e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate(),e.getUTCHours(),e.getUTCMinutes()])}(e):"second"===t?function(e){return _e([e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate(),e.getUTCHours(),e.getUTCMinutes(),e.getUTCSeconds()])}(e):null},e.prototype.startOfYear=function(e){return this.calendarSystem.arrayToMarker([this.calendarSystem.getMarkerYear(e)])},e.prototype.startOfMonth=function(e){return this.calendarSystem.arrayToMarker([this.calendarSystem.getMarkerYear(e),this.calendarSystem.getMarkerMonth(e)])},e.prototype.startOfWeek=function(e){return this.calendarSystem.arrayToMarker([this.calendarSystem.getMarkerYear(e),this.calendarSystem.getMarkerMonth(e),e.getUTCDate()-(e.getUTCDay()-this.weekDow+7)%7])},e.prototype.computeWeekNumber=function(e){return this.weekNumberFunc?this.weekNumberFunc(this.toDate(e)):function(e,t,n){var r=e.getUTCFullYear(),o=Re(e,r,t,n);if(o<1)return Re(e,r-1,t,n);var i=Re(e,r+1,t,n);return i>=1?Math.min(o,i):o}(e,this.weekDow,this.weekDoy)},e.prototype.format=function(e,t,n){return void 0===n&&(n={}),t.format({marker:e,timeZoneOffset:null!=n.forcedTzo?n.forcedTzo:this.offsetForMarker(e)},this)},e.prototype.formatRange=function(e,t,n,r){return void 0===r&&(r={}),r.isEndExclusive&&(t=ye(t,-1)),n.formatRange({marker:e,timeZoneOffset:null!=r.forcedStartTzo?r.forcedStartTzo:this.offsetForMarker(e)},{marker:t,timeZoneOffset:null!=r.forcedEndTzo?r.forcedEndTzo:this.offsetForMarker(t)},this,r.defaultSeparator)},e.prototype.formatIso=function(e,t){void 0===t&&(t={});var n=null;return t.omitTimeZoneOffset||(n=null!=t.forcedTzo?t.forcedTzo:this.offsetForMarker(e)),function(e,t,n){void 0===n&&(n=!1);var r=e.toISOString();return r=r.replace(".000",""),n&&(r=r.replace("T00:00:00Z","")),r.length>10&&(null==t?r=r.replace("Z",""):0!==t&&(r=r.replace("Z",it(t,!0)))),r}(e,n,t.omitTime)},e.prototype.timestampToMarker=function(e){return"local"===this.timeZone?_e(ke(new Date(e))):"UTC"!==this.timeZone&&this.namedTimeZoneImpl?_e(this.namedTimeZoneImpl.timestampToArray(e)):new Date(e)},e.prototype.offsetForMarker=function(e){return"local"===this.timeZone?-Me(xe(e)).getTimezoneOffset():"UTC"===this.timeZone?0:this.namedTimeZoneImpl?this.namedTimeZoneImpl.offsetForArray(xe(e)):null},e.prototype.toDate=function(e,t){return"local"===this.timeZone?Me(xe(e)):"UTC"===this.timeZone?new Date(e.valueOf()):this.namedTimeZoneImpl?new Date(e.valueOf()-1e3*this.namedTimeZoneImpl.offsetForArray(xe(e))*60):new Date(e.valueOf()-(t||0))},e}(),$n=[],Qn={code:"en",week:{dow:0,doy:4},direction:"ltr",buttonText:{prev:"prev",next:"next",prevYear:"prev year",nextYear:"next year",year:"year",today:"today",month:"month",week:"week",day:"day",list:"list"},weekText:"W",allDayText:"all-day",moreLinkText:"more",noEventsText:"No events to display"};function er(e){for(var t=e.length>0?e[0].code:"en",n=$n.concat(e),r={en:Qn},o=0,i=n;o<i.length;o++){var a=i[o];r[a.code]=a}return{map:r,defaultCode:t}}function tr(e,t){return"object"!=typeof e||Array.isArray(e)?function(e,t){var n=[].concat(e||[]),r=function(e,t){for(var n=0;n<e.length;n+=1)for(var r=e[n].toLocaleLowerCase().split("-"),o=r.length;o>0;o-=1){var i=r.slice(0,o).join("-");if(t[i])return t[i]}return null}(n,t)||Qn;return nr(e,n,r)}(e,t):nr(e.code,[e.code],e)}function nr(e,t,n){var r=Oe([Qn,n],["buttonText"]);delete r.code;var o=r.week;return delete r.week,{codeArg:e,codes:t,week:o,simpleNumberFormat:new Intl.NumberFormat(e),options:r}}function rr(e){var t=tr(e.locale||"en",er([]).map);return new Jn(r(r({timeZone:wt.timeZone,calendarSystem:"gregory"},e),{locale:t}))}var or,ir={startTime:"09:00",endTime:"17:00",daysOfWeek:[1,2,3,4,5],display:"inverse-background",classNames:"fc-non-business",groupId:"_businessHours"};function ar(e,t){return Nt(function(e){var t;t=!0===e?[{}]:Array.isArray(e)?e.filter((function(e){return e.daysOfWeek})):"object"==typeof e&&e?[e]:[];return t=t.map((function(e){return r(r({},ir),e)}))}(e),null,t)}function sr(e,t){return e.left>=t.left&&e.left<t.right&&e.top>=t.top&&e.top<t.bottom}function lr(e,t){var n={left:Math.max(e.left,t.left),right:Math.min(e.right,t.right),top:Math.max(e.top,t.top),bottom:Math.min(e.bottom,t.bottom)};return n.left<n.right&&n.top<n.bottom&&n}function ur(e,t){return{left:Math.min(Math.max(e.left,t.left),t.right),top:Math.min(Math.max(e.top,t.top),t.bottom)}}function cr(e){return{left:(e.left+e.right)/2,top:(e.top+e.bottom)/2}}function dr(e,t){return{left:e.left-t.left,top:e.top-t.top}}function pr(){return null==or&&(or=function(){if("undefined"==typeof document)return!0;var e=document.createElement("div");e.style.position="absolute",e.style.top="0px",e.style.left="0px",e.innerHTML="<table><tr><td><div></div></td></tr></table>",e.querySelector("table").style.height="100px",e.querySelector("div").style.height="100%",document.body.appendChild(e);var t=e.querySelector("div").offsetHeight>0;return document.body.removeChild(e),t}()),or}var fr={defs:{},instances:{}},hr=function(){function e(){this.getKeysForEventDefs=st(this._getKeysForEventDefs),this.splitDateSelection=st(this._splitDateSpan),this.splitEventStore=st(this._splitEventStore),this.splitIndividualUi=st(this._splitIndividualUi),this.splitEventDrag=st(this._splitInteraction),this.splitEventResize=st(this._splitInteraction),this.eventUiBuilders={}}return e.prototype.splitProps=function(e){var t=this,n=this.getKeyInfo(e),r=this.getKeysForEventDefs(e.eventStore),o=this.splitDateSelection(e.dateSelection),i=this.splitIndividualUi(e.eventUiBases,r),a=this.splitEventStore(e.eventStore,r),s=this.splitEventDrag(e.eventDrag),l=this.splitEventResize(e.eventResize),u={};for(var c in this.eventUiBuilders=Ue(n,(function(e,n){return t.eventUiBuilders[n]||st(vr)})),n){var d=n[c],p=a[c]||fr,f=this.eventUiBuilders[c];u[c]={businessHours:d.businessHours||e.businessHours,dateSelection:o[c]||null,eventStore:p,eventUiBases:f(e.eventUiBases[""],d.ui,i[c]),eventSelection:p.instances[e.eventSelection]?e.eventSelection:"",eventDrag:s[c]||null,eventResize:l[c]||null}}return u},e.prototype._splitDateSpan=function(e){var t={};if(e)for(var n=0,r=this.getKeysForDateSpan(e);n<r.length;n++){t[r[n]]=e}return t},e.prototype._getKeysForEventDefs=function(e){var t=this;return Ue(e.defs,(function(e){return t.getKeysForEventDef(e)}))},e.prototype._splitEventStore=function(e,t){var n=e.defs,r=e.instances,o={};for(var i in n)for(var a=0,s=t[i];a<s.length;a++){o[p=s[a]]||(o[p]={defs:{},instances:{}}),o[p].defs[i]=n[i]}for(var l in r)for(var u=r[l],c=0,d=t[u.defId];c<d.length;c++){var p;o[p=d[c]]&&(o[p].instances[l]=u)}return o},e.prototype._splitIndividualUi=function(e,t){var n={};for(var r in e)if(r)for(var o=0,i=t[r];o<i.length;o++){var a=i[o];n[a]||(n[a]={}),n[a][r]=e[r]}return n},e.prototype._splitInteraction=function(e){var t={};if(e){var n=this._splitEventStore(e.affectedEvents,this._getKeysForEventDefs(e.affectedEvents)),r=this._getKeysForEventDefs(e.mutatedEvents),o=this._splitEventStore(e.mutatedEvents,r),i=function(r){t[r]||(t[r]={affectedEvents:n[r]||fr,mutatedEvents:o[r]||fr,isEvent:e.isEvent})};for(var a in n)i(a);for(var a in o)i(a)}return t},e}();function vr(e,t,n){var o=[];e&&o.push(e),t&&o.push(t);var i={"":Bt(o)};return n&&r(i,n),i}function gr(e,t,n,r){return{dow:e.getUTCDay(),isDisabled:Boolean(r&&!un(r.activeRange,e)),isOther:Boolean(r&&!un(r.currentRange,e)),isToday:Boolean(t&&un(t,e)),isPast:Boolean(n?e<n:!!t&&e<t.start),isFuture:Boolean(n?e>n:!!t&&e>=t.end)}}function mr(e,t){var n=["fc-day","fc-day-"+ve[e.dow]];return e.isDisabled?n.push("fc-day-disabled"):(e.isToday&&(n.push("fc-day-today"),n.push(t.getClass("today"))),e.isPast&&n.push("fc-day-past"),e.isFuture&&n.push("fc-day-future"),e.isOther&&n.push("fc-day-other")),n}function yr(e,t){return void 0===t&&(t="day"),JSON.stringify({date:rt(e),type:t})}var Er,Sr=null;function Dr(){return null===Sr&&(Sr=function(){var e=document.createElement("div");q(e,{position:"absolute",top:-1e3,left:0,border:0,padding:0,overflow:"scroll",direction:"rtl"}),e.innerHTML="<div></div>",document.body.appendChild(e);var t=e.firstChild.getBoundingClientRect().left>e.getBoundingClientRect().left;return F(e),t}()),Sr}function br(){return Er||(Er=function(){var e=document.createElement("div");e.style.overflow="scroll",e.style.position="absolute",e.style.top="-9999px",e.style.left="-9999px",document.body.appendChild(e);var t=Cr(e);return document.body.removeChild(e),t}()),Er}function Cr(e){return{x:e.offsetHeight-e.clientHeight,y:e.offsetWidth-e.clientWidth}}function wr(e,t){void 0===t&&(t=!1);var n=window.getComputedStyle(e),r=parseInt(n.borderLeftWidth,10)||0,o=parseInt(n.borderRightWidth,10)||0,i=parseInt(n.borderTopWidth,10)||0,a=parseInt(n.borderBottomWidth,10)||0,s=Cr(e),l=s.y-r-o,u={borderLeft:r,borderRight:o,borderTop:i,borderBottom:a,scrollbarBottom:s.x-i-a,scrollbarLeft:0,scrollbarRight:0};return Dr()&&"rtl"===n.direction?u.scrollbarLeft=l:u.scrollbarRight=l,t&&(u.paddingLeft=parseInt(n.paddingLeft,10)||0,u.paddingRight=parseInt(n.paddingRight,10)||0,u.paddingTop=parseInt(n.paddingTop,10)||0,u.paddingBottom=parseInt(n.paddingBottom,10)||0),u}function Rr(e,t,n){void 0===t&&(t=!1);var r=n?e.getBoundingClientRect():Tr(e),o=wr(e,t),i={left:r.left+o.borderLeft+o.scrollbarLeft,right:r.right-o.borderRight-o.scrollbarRight,top:r.top+o.borderTop,bottom:r.bottom-o.borderBottom-o.scrollbarBottom};return t&&(i.left+=o.paddingLeft,i.right-=o.paddingRight,i.top+=o.paddingTop,i.bottom-=o.paddingBottom),i}function Tr(e){var t=e.getBoundingClientRect();return{left:t.left+window.pageXOffset,top:t.top+window.pageYOffset,right:t.right+window.pageXOffset,bottom:t.bottom+window.pageYOffset}}function kr(e){for(var t=[];e instanceof HTMLElement;){var n=window.getComputedStyle(e);if("fixed"===n.position)break;/(auto|scroll)/.test(n.overflow+n.overflowY+n.overflowX)&&t.push(e),e=e.parentNode}return t}function Mr(e,t,n){var r=!1,o=function(){r||(r=!0,t.apply(this,arguments))},i=function(){r||(r=!0,n&&n.apply(this,arguments))},a=e(o,i);a&&"function"==typeof a.then&&a.then(o,i)}var xr=function(){function e(){this.handlers={},this.thisContext=null}return e.prototype.setThisContext=function(e){this.thisContext=e},e.prototype.setOptions=function(e){this.options=e},e.prototype.on=function(e,t){!function(e,t,n){(e[t]||(e[t]=[])).push(n)}(this.handlers,e,t)},e.prototype.off=function(e,t){!function(e,t,n){n?e[t]&&(e[t]=e[t].filter((function(e){return e!==n}))):delete e[t]}(this.handlers,e,t)},e.prototype.trigger=function(e){for(var t=[],n=1;n<arguments.length;n++)t[n-1]=arguments[n];for(var r=this.handlers[e]||[],o=this.options&&this.options[e],i=[].concat(o||[],r),a=0,s=i;a<s.length;a++){var l=s[a];l.apply(this.thisContext,t)}},e.prototype.hasHandlers=function(e){return this.handlers[e]&&this.handlers[e].length||this.options&&this.options[e]},e}();var _r=function(){function e(e,t,n,r){this.els=t;var o=this.originClientRect=e.getBoundingClientRect();n&&this.buildElHorizontals(o.left),r&&this.buildElVerticals(o.top)}return e.prototype.buildElHorizontals=function(e){for(var t=[],n=[],r=0,o=this.els;r<o.length;r++){var i=o[r].getBoundingClientRect();t.push(i.left-e),n.push(i.right-e)}this.lefts=t,this.rights=n},e.prototype.buildElVerticals=function(e){for(var t=[],n=[],r=0,o=this.els;r<o.length;r++){var i=o[r].getBoundingClientRect();t.push(i.top-e),n.push(i.bottom-e)}this.tops=t,this.bottoms=n},e.prototype.leftToIndex=function(e){var t,n=this.lefts,r=this.rights,o=n.length;for(t=0;t<o;t+=1)if(e>=n[t]&&e<r[t])return t},e.prototype.topToIndex=function(e){var t,n=this.tops,r=this.bottoms,o=n.length;for(t=0;t<o;t+=1)if(e>=n[t]&&e<r[t])return t},e.prototype.getWidth=function(e){return this.rights[e]-this.lefts[e]},e.prototype.getHeight=function(e){return this.bottoms[e]-this.tops[e]},e}(),Ir=function(){function e(){}return e.prototype.getMaxScrollTop=function(){return this.getScrollHeight()-this.getClientHeight()},e.prototype.getMaxScrollLeft=function(){return this.getScrollWidth()-this.getClientWidth()},e.prototype.canScrollVertically=function(){return this.getMaxScrollTop()>0},e.prototype.canScrollHorizontally=function(){return this.getMaxScrollLeft()>0},e.prototype.canScrollUp=function(){return this.getScrollTop()>0},e.prototype.canScrollDown=function(){return this.getScrollTop()<this.getMaxScrollTop()},e.prototype.canScrollLeft=function(){return this.getScrollLeft()>0},e.prototype.canScrollRight=function(){return this.getScrollLeft()<this.getMaxScrollLeft()},e}(),Pr=function(e){function t(t){var n=e.call(this)||this;return n.el=t,n}return n(t,e),t.prototype.getScrollTop=function(){return this.el.scrollTop},t.prototype.getScrollLeft=function(){return this.el.scrollLeft},t.prototype.setScrollTop=function(e){this.el.scrollTop=e},t.prototype.setScrollLeft=function(e){this.el.scrollLeft=e},t.prototype.getScrollWidth=function(){return this.el.scrollWidth},t.prototype.getScrollHeight=function(){return this.el.scrollHeight},t.prototype.getClientHeight=function(){return this.el.clientHeight},t.prototype.getClientWidth=function(){return this.el.clientWidth},t}(Ir),Nr=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.getScrollTop=function(){return window.pageYOffset},t.prototype.getScrollLeft=function(){return window.pageXOffset},t.prototype.setScrollTop=function(e){window.scroll(window.pageXOffset,e)},t.prototype.setScrollLeft=function(e){window.scroll(e,window.pageYOffset)},t.prototype.getScrollWidth=function(){return document.documentElement.scrollWidth},t.prototype.getScrollHeight=function(){return document.documentElement.scrollHeight},t.prototype.getClientHeight=function(){return document.documentElement.clientHeight},t.prototype.getClientWidth=function(){return document.documentElement.clientWidth},t}(Ir),Hr=function(){function e(e){this.iconOverrideOption&&this.setIconOverride(e[this.iconOverrideOption])}return e.prototype.setIconOverride=function(e){var t,n;if("object"==typeof e&&e){for(n in t=r({},this.iconClasses),e)t[n]=this.applyIconOverridePrefix(e[n]);this.iconClasses=t}else!1===e&&(this.iconClasses={})},e.prototype.applyIconOverridePrefix=function(e){var t=this.iconOverridePrefix;return t&&0!==e.indexOf(t)&&(e=t+e),e},e.prototype.getClass=function(e){return this.classes[e]||""},e.prototype.getIconClass=function(e,t){var n;return(n=t&&this.rtlIconClasses&&this.rtlIconClasses[e]||this.iconClasses[e])?this.baseIconClass+" "+n:""},e.prototype.getCustomButtonIconClass=function(e){var t;return this.iconOverrideCustomButtonOption&&(t=e[this.iconOverrideCustomButtonOption])?this.baseIconClass+" "+this.applyIconOverridePrefix(t):""},e}();if(Hr.prototype.classes={},Hr.prototype.iconClasses={},Hr.prototype.baseIconClass="",Hr.prototype.iconOverridePrefix="","undefined"==typeof FullCalendarVDom)throw new Error("Please import the top-level fullcalendar lib before attempting to import a plugin.");var Or=FullCalendarVDom.Component,Ar=FullCalendarVDom.createElement,Ur=FullCalendarVDom.render,Lr=FullCalendarVDom.createRef,Wr=FullCalendarVDom.Fragment,Vr=FullCalendarVDom.createContext,Fr=FullCalendarVDom.flushToDom,zr=FullCalendarVDom.unmountComponentAtNode,Br=function(){function e(e,t,n){var o=this;this.execFunc=e,this.emitter=t,this.scrollTime=n,this.handleScrollRequest=function(e){o.queuedRequest=r({},o.queuedRequest||{},e),o.drain()},t.on("_scrollRequest",this.handleScrollRequest),this.fireInitialScroll()}return e.prototype.detach=function(){this.emitter.off("_scrollRequest",this.handleScrollRequest)},e.prototype.update=function(e){e?this.fireInitialScroll():this.drain()},e.prototype.fireInitialScroll=function(){this.handleScrollRequest({time:this.scrollTime})},e.prototype.drain=function(){this.queuedRequest&&this.execFunc(this.queuedRequest)&&(this.queuedRequest=null)},e}(),jr=Vr({});function Gr(e,t,n,r,o,i,a,s,l,u,c,d,p){return{dateEnv:o,options:n,pluginHooks:a,emitter:u,dispatch:s,getCurrentData:l,calendarApi:c,viewSpec:e,viewApi:t,dateProfileGenerator:r,theme:i,isRtl:"rtl"===n.direction,addResizeHandler:function(e){u.on("_resize",e)},removeResizeHandler:function(e){u.off("_resize",e)},createScrollResponder:function(e){return new Br(e,u,Xe(n.scrollTime))},registerInteractiveComponent:d,unregisterInteractiveComponent:p}}var qr=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.shouldComponentUpdate=function(e,t){return this.debug&&console.log(Fe(e,this.props),Fe(t,this.state)),!ze(this.props,e,this.propEquality)||!ze(this.state,t,this.stateEquality)},t.addPropsEquality=Zr,t.addStateEquality=Xr,t.contextType=jr,t}(Or);qr.prototype.propEquality={},qr.prototype.stateEquality={};var Yr=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.contextType=jr,t}(qr);function Zr(e){var t=Object.create(this.prototype.propEquality);r(t,e),this.prototype.propEquality=t}function Xr(e){var t=Object.create(this.prototype.stateEquality);r(t,e),this.prototype.stateEquality=t}function Kr(e,t){"function"==typeof e?e(t):e&&(e.current=t)}function Jr(e,t,n,r,o){switch(t.type){case"RECEIVE_EVENTS":return function(e,t,n,r,o,i){if(t&&n===t.latestFetchId){var a=Nt(function(e,t,n){var r=n.options.eventDataTransform,o=t?t.eventDataTransform:null;o&&(e=$r(e,o));r&&(e=$r(e,r));return e}(o,t,i),t,i);return r&&(a=Ge(a,r,i)),Ut(Qr(e,t.sourceId),a)}return e}(e,n[t.sourceId],t.fetchId,t.fetchRange,t.rawEvents,o);case"ADD_EVENTS":return function(e,t,n,r){n&&(t=Ge(t,n,r));return Ut(e,t)}(e,t.eventStore,r?r.activeRange:null,o);case"RESET_EVENTS":return t.eventStore;case"MERGE_EVENTS":return Ut(e,t.eventStore);case"PREV":case"NEXT":case"CHANGE_DATE":case"CHANGE_VIEW_TYPE":return r?Ge(e,r.activeRange,o):e;case"REMOVE_EVENTS":return function(e,t){var n=e.defs,r=e.instances,o={},i={};for(var a in n)t.defs[a]||(o[a]=n[a]);for(var s in r)!t.instances[s]&&o[r[s].defId]&&(i[s]=r[s]);return{defs:o,instances:i}}(e,t.eventStore);case"REMOVE_EVENT_SOURCE":return Qr(e,t.sourceId);case"REMOVE_ALL_EVENT_SOURCES":return Lt(e,(function(e){return!e.sourceId}));case"REMOVE_ALL_EVENTS":return{defs:{},instances:{}};default:return e}}function $r(e,t){var n;if(t){n=[];for(var r=0,o=e;r<o.length;r++){var i=o[r],a=t(i);a?n.push(a):null==a&&n.push(i)}}else n=e;return n}function Qr(e,t){return Lt(e,(function(e){return e.sourceId!==t}))}function eo(e,t){return to({eventDrag:e},t)}function to(e,t){var n=t.getCurrentData(),o=r({businessHours:n.businessHours,dateSelection:"",eventStore:n.eventStore,eventUiBases:n.eventUiBases,eventSelection:"",eventDrag:null,eventResize:null},e);return(t.pluginHooks.isPropsValid||no)(o,t)}function no(e,t,n,o){return void 0===n&&(n={}),!(e.eventDrag&&!function(e,t,n,o){var i=t.getCurrentData(),a=e.eventDrag,s=a.mutatedEvents,l=s.defs,u=s.instances,c=hn(l,a.isEvent?e.eventUiBases:{"":i.selectionConfig});o&&(c=Ue(c,o));var d=(v=e.eventStore,g=a.affectedEvents.instances,{defs:v.defs,instances:Ae(v.instances,(function(e){return!g[e.instanceId]}))}),p=d.defs,f=d.instances,h=hn(p,e.eventUiBases);var v,g;for(var m in u){var y=u[m],E=y.range,S=c[y.defId],D=l[y.defId];if(!ro(S.constraints,E,d,e.businessHours,t))return!1;var b=t.options.eventOverlap,C="function"==typeof b?b:null;for(var w in f){var R=f[w];if(sn(E,R.range)){if(!1===h[R.defId].overlap&&a.isEvent)return!1;if(!1===S.overlap)return!1;if(C&&!C(new Bn(t,p[R.defId],R),new Bn(t,D,y)))return!1}}for(var T=i.eventStore,k=0,M=S.allows;k<M.length;k++){var x=M[k],_=r(r({},n),{range:y.range,allDay:D.allDay}),I=T.defs[D.defId],P=T.instances[m],N=void 0;if(N=I?new Bn(t,I,P):new Bn(t,D),!x(Pn(_,t),N))return!1}}return!0}(e,t,n,o))&&!(e.dateSelection&&!function(e,t,n,o){var i=e.eventStore,a=i.defs,s=i.instances,l=e.dateSelection,u=l.range,c=t.getCurrentData().selectionConfig;o&&(c=o(c));if(!ro(c.constraints,u,i,e.businessHours,t))return!1;var d=t.options.selectOverlap,p="function"==typeof d?d:null;for(var f in s){var h=s[f];if(sn(u,h.range)){if(!1===c.overlap)return!1;if(p&&!p(new Bn(t,a[h.defId],h),null))return!1}}for(var v=0,g=c.allows;v<g.length;v++){var m=g[v],y=r(r({},n),l);if(!m(Pn(y,t),null))return!1}return!0}(e,t,n,o))}function ro(e,t,n,r,o){for(var i=0,a=e;i<a.length;i++){if(!ao(oo(a[i],t,n,r,o),t))return!1}return!0}function oo(e,t,n,r,o){return"businessHours"===e?io(Ge(r,t,o)):"string"==typeof e?io(Lt(n,(function(t){return t.groupId===e}))):"object"==typeof e&&e?io(Ge(e,t,o)):[]}function io(e){var t=e.instances,n=[];for(var r in t)n.push(t[r].range);return n}function ao(e,t){for(var n=0,r=e;n<r.length;n++){if(ln(r[n],t))return!0}return!1}var so=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.uid=ee(),t}return n(t,e),t.prototype.prepareHits=function(){},t.prototype.queryHit=function(e,t,n,r){return null},t.prototype.isInteractionValid=function(e){var t=this.props.dateProfile,n=e.mutatedEvents.instances;if(t)for(var r in n)if(!ln(t.validRange,n[r].range))return!1;return eo(e,this.context)},t.prototype.isDateSelectionValid=function(e){var t,n,r=this.props.dateProfile;return!(r&&!ln(r.validRange,e.range))&&(t=e,n=this.context,to({dateSelection:t},n))},t.prototype.isValidSegDownEl=function(e){return!this.props.eventDrag&&!this.props.eventResize&&!z(e,".fc-event-mirror")},t.prototype.isValidDateDownEl=function(e){return!(z(e,".fc-event:not(.fc-bg-event)")||z(e,".fc-daygrid-more-link")||z(e,"a[data-navlink]")||z(e,".fc-popover"))},t}(Yr);function lo(e){return{id:ee(),deps:e.deps||[],reducers:e.reducers||[],isLoadingFuncs:e.isLoadingFuncs||[],contextInit:[].concat(e.contextInit||[]),eventRefiners:e.eventRefiners||{},eventDefMemberAdders:e.eventDefMemberAdders||[],eventSourceRefiners:e.eventSourceRefiners||{},isDraggableTransformers:e.isDraggableTransformers||[],eventDragMutationMassagers:e.eventDragMutationMassagers||[],eventDefMutationAppliers:e.eventDefMutationAppliers||[],dateSelectionTransformers:e.dateSelectionTransformers||[],datePointTransforms:e.datePointTransforms||[],dateSpanTransforms:e.dateSpanTransforms||[],views:e.views||{},viewPropsTransformers:e.viewPropsTransformers||[],isPropsValid:e.isPropsValid||null,externalDefTransforms:e.externalDefTransforms||[],eventResizeJoinTransforms:e.eventResizeJoinTransforms||[],viewContainerAppends:e.viewContainerAppends||[],eventDropTransformers:e.eventDropTransformers||[],componentInteractions:e.componentInteractions||[],calendarInteractions:e.calendarInteractions||[],themeClasses:e.themeClasses||{},eventSourceDefs:e.eventSourceDefs||[],cmdFormatter:e.cmdFormatter,recurringTypes:e.recurringTypes||[],namedTimeZonedImpl:e.namedTimeZonedImpl,initialView:e.initialView||"",elementDraggingImpl:e.elementDraggingImpl,optionChangeHandlers:e.optionChangeHandlers||{},scrollGridImpl:e.scrollGridImpl||null,contentTypeHandlers:e.contentTypeHandlers||{},listenerRefiners:e.listenerRefiners||{},optionRefiners:e.optionRefiners||{},propSetHandlers:e.propSetHandlers||{}}}function uo(){var e,t=[],n=[];return function(o,i){return e&&at(o,t)&&at(i,n)||(e=function(e,t){var n={},o={reducers:[],isLoadingFuncs:[],contextInit:[],eventRefiners:{},eventDefMemberAdders:[],eventSourceRefiners:{},isDraggableTransformers:[],eventDragMutationMassagers:[],eventDefMutationAppliers:[],dateSelectionTransformers:[],datePointTransforms:[],dateSpanTransforms:[],views:{},viewPropsTransformers:[],isPropsValid:null,externalDefTransforms:[],eventResizeJoinTransforms:[],viewContainerAppends:[],eventDropTransformers:[],componentInteractions:[],calendarInteractions:[],themeClasses:{},eventSourceDefs:[],cmdFormatter:null,recurringTypes:[],namedTimeZonedImpl:null,initialView:"",elementDraggingImpl:null,optionChangeHandlers:{},scrollGridImpl:null,contentTypeHandlers:{},listenerRefiners:{},optionRefiners:{},propSetHandlers:{}};function i(e){for(var t=0,a=e;t<a.length;t++){var s=a[t];n[s.id]||(n[s.id]=!0,i(s.deps),u=s,o={reducers:(l=o).reducers.concat(u.reducers),isLoadingFuncs:l.isLoadingFuncs.concat(u.isLoadingFuncs),contextInit:l.contextInit.concat(u.contextInit),eventRefiners:r(r({},l.eventRefiners),u.eventRefiners),eventDefMemberAdders:l.eventDefMemberAdders.concat(u.eventDefMemberAdders),eventSourceRefiners:r(r({},l.eventSourceRefiners),u.eventSourceRefiners),isDraggableTransformers:l.isDraggableTransformers.concat(u.isDraggableTransformers),eventDragMutationMassagers:l.eventDragMutationMassagers.concat(u.eventDragMutationMassagers),eventDefMutationAppliers:l.eventDefMutationAppliers.concat(u.eventDefMutationAppliers),dateSelectionTransformers:l.dateSelectionTransformers.concat(u.dateSelectionTransformers),datePointTransforms:l.datePointTransforms.concat(u.datePointTransforms),dateSpanTransforms:l.dateSpanTransforms.concat(u.dateSpanTransforms),views:r(r({},l.views),u.views),viewPropsTransformers:l.viewPropsTransformers.concat(u.viewPropsTransformers),isPropsValid:u.isPropsValid||l.isPropsValid,externalDefTransforms:l.externalDefTransforms.concat(u.externalDefTransforms),eventResizeJoinTransforms:l.eventResizeJoinTransforms.concat(u.eventResizeJoinTransforms),viewContainerAppends:l.viewContainerAppends.concat(u.viewContainerAppends),eventDropTransformers:l.eventDropTransformers.concat(u.eventDropTransformers),calendarInteractions:l.calendarInteractions.concat(u.calendarInteractions),componentInteractions:l.componentInteractions.concat(u.componentInteractions),themeClasses:r(r({},l.themeClasses),u.themeClasses),eventSourceDefs:l.eventSourceDefs.concat(u.eventSourceDefs),cmdFormatter:u.cmdFormatter||l.cmdFormatter,recurringTypes:l.recurringTypes.concat(u.recurringTypes),namedTimeZonedImpl:u.namedTimeZonedImpl||l.namedTimeZonedImpl,initialView:l.initialView||u.initialView,elementDraggingImpl:l.elementDraggingImpl||u.elementDraggingImpl,optionChangeHandlers:r(r({},l.optionChangeHandlers),u.optionChangeHandlers),scrollGridImpl:u.scrollGridImpl||l.scrollGridImpl,contentTypeHandlers:r(r({},l.contentTypeHandlers),u.contentTypeHandlers),listenerRefiners:r(r({},l.listenerRefiners),u.listenerRefiners),optionRefiners:r(r({},l.optionRefiners),u.optionRefiners),propSetHandlers:r(r({},l.propSetHandlers),u.propSetHandlers)})}var l,u}return e&&i(e),i(t),o}(o,i)),t=o,n=i,e}}var co=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t}(Hr);function po(e,t,n,o){if(t[e])return t[e];var i=function(e,t,n,o){var i=n[e],a=o[e],s=function(e){return i&&null!==i[e]?i[e]:a&&null!==a[e]?a[e]:null},l=s("component"),u=s("superType"),c=null;if(u){if(u===e)throw new Error("Can't have a custom view type that references itself");c=po(u,t,n,o)}!l&&c&&(l=c.component);if(!l)return null;return{type:e,component:l,defaults:r(r({},c?c.defaults:{}),i?i.rawOptions:{}),overrides:r(r({},c?c.overrides:{}),a?a.rawOptions:{})}}(e,t,n,o);return i&&(t[e]=i),i}co.prototype.classes={root:"fc-theme-standard",tableCellShaded:"fc-cell-shaded",buttonGroup:"fc-button-group",button:"fc-button fc-button-primary",buttonActive:"fc-button-active"},co.prototype.baseIconClass="fc-icon",co.prototype.iconClasses={close:"fc-icon-x",prev:"fc-icon-chevron-left",next:"fc-icon-chevron-right",prevYear:"fc-icon-chevrons-left",nextYear:"fc-icon-chevrons-right"},co.prototype.rtlIconClasses={prev:"fc-icon-chevron-right",next:"fc-icon-chevron-left",prevYear:"fc-icon-chevrons-right",nextYear:"fc-icon-chevrons-left"},co.prototype.iconOverrideOption="buttonIcons",co.prototype.iconOverrideCustomButtonOption="icon",co.prototype.iconOverridePrefix="fc-icon-";var fo=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.rootElRef=Lr(),t.handleRootEl=function(e){Kr(t.rootElRef,e),t.props.elRef&&Kr(t.props.elRef,e)},t}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=t.hookProps;return Ar(mo,{hookProps:n,didMount:t.didMount,willUnmount:t.willUnmount,elRef:this.handleRootEl},(function(r){return Ar(vo,{hookProps:n,content:t.content,defaultContent:t.defaultContent,backupElRef:e.rootElRef},(function(e,o){return t.children(r,Eo(t.classNames,n),e,o)}))}))},t}(Yr),ho=Vr(0);function vo(e){return Ar(ho.Consumer,null,(function(t){return Ar(go,r({renderId:t},e))}))}var go=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.innerElRef=Lr(),t}return n(t,e),t.prototype.render=function(){return this.props.children(this.innerElRef,this.renderInnerContent())},t.prototype.componentDidMount=function(){this.updateCustomContent()},t.prototype.componentDidUpdate=function(){this.updateCustomContent()},t.prototype.componentWillUnmount=function(){this.customContentInfo&&this.customContentInfo.destroy&&this.customContentInfo.destroy()},t.prototype.renderInnerContent=function(){var e=this.context.pluginHooks.contentTypeHandlers,t=this.props,n=this.customContentInfo,o=So(t.content,t.hookProps),i=null;if(void 0===o&&(o=So(t.defaultContent,t.hookProps)),void 0!==o){if(n)n.contentVal=o[n.contentKey];else if("object"==typeof o)for(var a in e)if(void 0!==o[a]){var s=e[a]();n=this.customContentInfo=r({contentKey:a,contentVal:o[a]},s);break}i=n?[]:o}return i},t.prototype.updateCustomContent=function(){this.customContentInfo&&this.customContentInfo.render(this.innerElRef.current||this.props.backupElRef.current,this.customContentInfo.contentVal)},t}(Yr),mo=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.handleRootEl=function(e){t.rootEl=e,t.props.elRef&&Kr(t.props.elRef,e)},t}return n(t,e),t.prototype.render=function(){return this.props.children(this.handleRootEl)},t.prototype.componentDidMount=function(){var e=this.props.didMount;e&&e(r(r({},this.props.hookProps),{el:this.rootEl}))},t.prototype.componentWillUnmount=function(){var e=this.props.willUnmount;e&&e(r(r({},this.props.hookProps),{el:this.rootEl}))},t}(Yr);function yo(){var e,t,n=[];return function(r,o){return t&&Ve(t,o)&&r===e||(e=r,t=o,n=Eo(r,o)),n}}function Eo(e,t){return"function"==typeof e&&(e=e(t)),Wt(e)}function So(e,t){return"function"==typeof e?e(t,Ar):e}var Do=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.normalizeClassNames=yo(),t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.options,r={view:t.viewApi},o=this.normalizeClassNames(n.viewClassNames,r);return Ar(mo,{hookProps:r,didMount:n.viewDidMount,willUnmount:n.viewWillUnmount,elRef:e.elRef},(function(t){return e.children(t,["fc-"+e.viewSpec.type+"-view","fc-view"].concat(o))}))},t}(Yr);function bo(e){return Ue(e,Co)}function Co(e){var t,n="function"==typeof e?{component:e}:e,o=n.component;return n.content&&(t=n,o=function(e){return Ar(jr.Consumer,null,(function(n){return Ar(Do,{viewSpec:n.viewSpec},(function(o,i){var a=r(r({},e),{nextDayThreshold:n.options.nextDayThreshold});return Ar(fo,{hookProps:a,classNames:t.classNames,content:t.content,didMount:t.didMount,willUnmount:t.willUnmount,elRef:o},(function(e,t,n,r){return Ar("div",{className:i.concat(t).join(" "),ref:e},r)}))}))}))}),{superType:n.type,component:o,rawOptions:n}}function wo(e,t,n,o){var i=bo(e),a=bo(t.views);return Ue(function(e,t){var n,r={};for(n in e)po(n,r,e,t);for(n in t)po(n,r,e,t);return r}(i,a),(function(e){return function(e,t,n,o,i){var a=e.overrides.duration||e.defaults.duration||o.duration||n.duration,s=null,l="",u="",c={};if(a&&(s=function(e){var t=JSON.stringify(e),n=Ro[t];void 0===n&&(n=Xe(e),Ro[t]=n);return n}(a))){var d=nt(s);l=d.unit,1===d.value&&(u=l,c=t[l]?t[l].rawOptions:{})}var p=function(t){var n=t.buttonText||{},r=e.defaults.buttonTextKey;return null!=r&&null!=n[r]?n[r]:null!=n[e.type]?n[e.type]:null!=n[u]?n[u]:null};return{type:e.type,component:e.component,duration:s,durationUnit:l,singleUnit:u,optionDefaults:e.defaults,optionOverrides:r(r({},c),e.overrides),buttonTextOverride:p(o)||p(n)||e.overrides.buttonText,buttonTextDefault:p(i)||e.defaults.buttonText||p(wt)||e.type}}(e,a,t,n,o)}))}var Ro={};var To=function(){function e(e){this.props=e,this.nowDate=Fn(e.nowInput,e.dateEnv),this.initHiddenDays()}return e.prototype.buildPrev=function(e,t,n){var r=this.props.dateEnv,o=r.subtract(r.startOf(t,e.currentRangeUnit),e.dateIncrement);return this.build(o,-1,n)},e.prototype.buildNext=function(e,t,n){var r=this.props.dateEnv,o=r.add(r.startOf(t,e.currentRangeUnit),e.dateIncrement);return this.build(o,1,n)},e.prototype.build=function(e,t,n){void 0===n&&(n=!0);var r,o,i,a,s,l,u,c,d=this.props;return r=this.buildValidRange(),r=this.trimHiddenDays(r),n&&(u=e,e=null!=(c=r).start&&u<c.start?c.start:null!=c.end&&u>=c.end?new Date(c.end.valueOf()-1):u),o=this.buildCurrentRangeInfo(e,t),i=/^(year|month|week|day)$/.test(o.unit),a=this.buildRenderRange(this.trimHiddenDays(o.range),o.unit,i),s=a=this.trimHiddenDays(a),d.showNonCurrentDates||(s=on(s,o.range)),s=on(s=this.adjustActiveRange(s),r),l=sn(o.range,r),{validRange:r,currentRange:o.range,currentRangeUnit:o.unit,isRangeAllDay:i,activeRange:s,renderRange:a,slotMinTime:d.slotMinTime,slotMaxTime:d.slotMaxTime,isValid:l,dateIncrement:this.buildDateIncrement(o.duration)}},e.prototype.buildValidRange=function(){var e=this.props.validRangeInput,t="function"==typeof e?e.call(this.props.calendarApi,this.nowDate):e;return this.refineRange(t)||{start:null,end:null}},e.prototype.buildCurrentRangeInfo=function(e,t){var n,r=this.props,o=null,i=null,a=null;return r.duration?(o=r.duration,i=r.durationUnit,a=this.buildRangeFromDuration(e,t,o,i)):(n=this.props.dayCount)?(i="day",a=this.buildRangeFromDayCount(e,t,n)):(a=this.buildCustomVisibleRange(e))?i=r.dateEnv.greatestWholeUnit(a.start,a.end).unit:(i=nt(o=this.getFallbackDuration()).unit,a=this.buildRangeFromDuration(e,t,o,i)),{duration:o,unit:i,range:a}},e.prototype.getFallbackDuration=function(){return Xe({day:1})},e.prototype.adjustActiveRange=function(e){var t=this.props,n=t.dateEnv,r=t.usesMinMaxTime,o=t.slotMinTime,i=t.slotMaxTime,a=e.start,s=e.end;return r&&(Qe(o)<0&&(a=we(a),a=n.add(a,o)),Qe(i)>1&&(s=me(s=we(s),-1),s=n.add(s,i))),{start:a,end:s}},e.prototype.buildRangeFromDuration=function(e,t,n,r){var o,i,a,s=this.props,l=s.dateEnv,u=s.dateAlignment;if(!u){var c=this.props.dateIncrement;u=c&&et(c)<et(n)?nt(c).unit:r}function d(){o=l.startOf(e,u),i=l.add(o,n),a={start:o,end:i}}return Qe(n)<=1&&this.isHiddenDay(o)&&(o=we(o=this.skipHiddenDays(o,t))),d(),this.trimHiddenDays(a)||(e=this.skipHiddenDays(e,t),d()),a},e.prototype.buildRangeFromDayCount=function(e,t,n){var r,o=this.props,i=o.dateEnv,a=o.dateAlignment,s=0,l=e;a&&(l=i.startOf(l,a)),l=we(l),r=l=this.skipHiddenDays(l,t);do{r=me(r,1),this.isHiddenDay(r)||(s+=1)}while(s<n);return{start:l,end:r}},e.prototype.buildCustomVisibleRange=function(e){var t=this.props,n=t.visibleRangeInput,r="function"==typeof n?n.call(t.calendarApi,t.dateEnv.toDate(e)):n,o=this.refineRange(r);return!o||null!=o.start&&null!=o.end?o:null},e.prototype.buildRenderRange=function(e,t,n){return e},e.prototype.buildDateIncrement=function(e){var t,n=this.props.dateIncrement;return n||((t=this.props.dateAlignment)?Xe(1,t):e||Xe({days:1}))},e.prototype.refineRange=function(e){if(e){var t=(n=e,r=this.props.dateEnv,o=null,i=null,n.start&&(o=r.createMarker(n.start)),n.end&&(i=r.createMarker(n.end)),o||i?o&&i&&i<o?null:{start:o,end:i}:null);return t&&(t=Qt(t)),t}var n,r,o,i;return null},e.prototype.initHiddenDays=function(){var e,t=this.props.hiddenDays||[],n=[],r=0;for(!1===this.props.weekends&&t.push(0,6),e=0;e<7;e+=1)(n[e]=-1!==t.indexOf(e))||(r+=1);if(!r)throw new Error("invalid hiddenDays");this.isHiddenDayHash=n},e.prototype.trimHiddenDays=function(e){var t=e.start,n=e.end;return t&&(t=this.skipHiddenDays(t)),n&&(n=this.skipHiddenDays(n,-1,!0)),null==t||null==n||t<n?{start:t,end:n}:null},e.prototype.isHiddenDay=function(e){return e instanceof Date&&(e=e.getUTCDay()),this.isHiddenDayHash[e]},e.prototype.skipHiddenDays=function(e,t,n){for(void 0===t&&(t=1),void 0===n&&(n=!1);this.isHiddenDayHash[(e.getUTCDay()+(n?t:0)+7)%7];)e=me(e,t);return e},e}();function ko(e,t,n){var r=t?t.activeRange:null;return _o({},function(e,t){var n=Vn(t),r=[].concat(e.eventSources||[]),o=[];e.initialEvents&&r.unshift(e.initialEvents);e.events&&r.unshift(e.events);for(var i=0,a=r;i<a.length;i++){var s=Wn(a[i],t,n);s&&o.push(s)}return o}(e,n),r,n)}function Mo(e,t,n,o){var i,a,s=n?n.activeRange:null;switch(t.type){case"ADD_EVENT_SOURCES":return _o(e,t.sources,s,o);case"REMOVE_EVENT_SOURCE":return i=e,a=t.sourceId,Ae(i,(function(e){return e.sourceId!==a}));case"PREV":case"NEXT":case"CHANGE_DATE":case"CHANGE_VIEW_TYPE":return n?Io(e,s,o):e;case"FETCH_EVENT_SOURCES":return Po(e,t.sourceIds?Le(t.sourceIds):Ho(e,o),s,o);case"RECEIVE_EVENTS":case"RECEIVE_EVENT_ERROR":return function(e,t,n,o){var i,a=e[t];if(a&&n===a.latestFetchId)return r(r({},e),((i={})[t]=r(r({},a),{isFetching:!1,fetchRange:o}),i));return e}(e,t.sourceId,t.fetchId,t.fetchRange);case"REMOVE_ALL_EVENT_SOURCES":return{};default:return e}}function xo(e){for(var t in e)if(e[t].isFetching)return!0;return!1}function _o(e,t,n,o){for(var i={},a=0,s=t;a<s.length;a++){var l=s[a];i[l.sourceId]=l}return n&&(i=Io(i,n,o)),r(r({},e),i)}function Io(e,t,n){return Po(e,Ae(e,(function(e){return function(e,t,n){if(!Oo(e,n))return!e.latestFetchId;return!n.options.lazyFetching||!e.fetchRange||e.isFetching||t.start<e.fetchRange.start||t.end>e.fetchRange.end}(e,t,n)})),t,n)}function Po(e,t,n,r){var o={};for(var i in e){var a=e[i];t[i]?o[i]=No(a,n,r):o[i]=a}return o}function No(e,t,n){var o=n.options,i=n.calendarApi,a=n.pluginHooks.eventSourceDefs[e.sourceDefId],s=ee();return a.fetch({eventSource:e,range:t,context:n},(function(r){var a=r.rawEvents;o.eventSourceSuccess&&(a=o.eventSourceSuccess.call(i,a,r.xhr)||a),e.success&&(a=e.success.call(i,a,r.xhr)||a),n.dispatch({type:"RECEIVE_EVENTS",sourceId:e.sourceId,fetchId:s,fetchRange:t,rawEvents:a})}),(function(r){console.warn(r.message,r),o.eventSourceFailure&&o.eventSourceFailure.call(i,r),e.failure&&e.failure(r),n.dispatch({type:"RECEIVE_EVENT_ERROR",sourceId:e.sourceId,fetchId:s,fetchRange:t,error:r})})),r(r({},e),{isFetching:!0,latestFetchId:s})}function Ho(e,t){return Ae(e,(function(e){return Oo(e,t)}))}function Oo(e,t){return!t.pluginHooks.eventSourceDefs[e.sourceDefId].ignoreRange}function Ao(e,t){switch(t.type){case"UNSELECT_DATES":return null;case"SELECT_DATES":return t.selection;default:return e}}function Uo(e,t){switch(t.type){case"UNSELECT_EVENT":return"";case"SELECT_EVENT":return t.eventInstanceId;default:return e}}function Lo(e,t){var n;switch(t.type){case"UNSET_EVENT_DRAG":return null;case"SET_EVENT_DRAG":return{affectedEvents:(n=t.state).affectedEvents,mutatedEvents:n.mutatedEvents,isEvent:n.isEvent};default:return e}}function Wo(e,t){var n;switch(t.type){case"UNSET_EVENT_RESIZE":return null;case"SET_EVENT_RESIZE":return{affectedEvents:(n=t.state).affectedEvents,mutatedEvents:n.mutatedEvents,isEvent:n.isEvent};default:return e}}function Vo(e,t,n,r,o){var i=[];return{headerToolbar:e.headerToolbar?Fo(e.headerToolbar,e,t,n,r,o,i):null,footerToolbar:e.footerToolbar?Fo(e.footerToolbar,e,t,n,r,o,i):null,viewsWithButtons:i}}function Fo(e,t,n,r,o,i,a){return Ue(e,(function(e){return function(e,t,n,r,o,i,a){var s="rtl"===t.direction,l=t.customButtons||{},u=n.buttonText||{},c=t.buttonText||{};return(e?e.split(" "):[]).map((function(e){return e.split(",").map((function(e){return"title"===e?{buttonName:e}:((t=l[e])?(d=function(e){t.click&&t.click.call(e.target,e,e.target)},(p=r.getCustomButtonIconClass(t))||(p=r.getIconClass(e,s))||(f=t.text)):(n=o[e])?(a.push(e),d=function(){i.changeView(e)},(f=n.buttonTextOverride)||(p=r.getIconClass(e,s))||(f=n.buttonTextDefault)):i[e]&&(d=function(){i[e]()},(f=u[e])||(p=r.getIconClass(e,s))||(f=c[e])),{buttonName:e,buttonClick:d,buttonIcon:p,buttonText:f});var t,n,d,p,f}))}))}(e,t,n,r,o,i,a)}))}function zo(e,t,n,r,o){var i=null;"GET"===(e=e.toUpperCase())?t=function(e,t){return e+(-1===e.indexOf("?")?"?":"&")+Bo(t)}(t,n):i=Bo(n);var a=new XMLHttpRequest;a.open(e,t,!0),"GET"!==e&&a.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),a.onload=function(){if(a.status>=200&&a.status<400){var e=!1,t=void 0;try{t=JSON.parse(a.responseText),e=!0}catch(e){}e?r(t,a):o("Failure parsing JSON",a)}else o("Request failed",a)},a.onerror=function(){o("Request failed",a)},a.send(i)}function Bo(e){var t=[];for(var n in e)t.push(encodeURIComponent(n)+"="+encodeURIComponent(e[n]));return t.join("&")}function jo(e,t){for(var n=We(t.getCurrentData().eventSources),r=[],o=0,i=e;o<i.length;o++){for(var a=i[o],s=!1,l=0;l<n.length;l+=1)if(n[l]._raw===a){n.splice(l,1),s=!0;break}s||r.push(a)}for(var u=0,c=n;u<c.length;u++){var d=c[u];t.dispatch({type:"REMOVE_EVENT_SOURCE",sourceId:d.sourceId})}for(var p=0,f=r;p<f.length;p++){var h=f[p];t.calendarApi.addEventSource(h)}}var Go=[lo({eventSourceDefs:[{ignoreRange:!0,parseMeta:function(e){return Array.isArray(e.events)?e.events:null},fetch:function(e,t){t({rawEvents:e.eventSource.meta})}}]}),lo({eventSourceDefs:[{parseMeta:function(e){return"function"==typeof e.events?e.events:null},fetch:function(e,t,n){var r=e.context.dateEnv;Mr(e.eventSource.meta.bind(null,Mn(e.range,r)),(function(e){t({rawEvents:e})}),n)}}]}),lo({eventSourceRefiners:{method:String,extraParams:Pt,startParam:String,endParam:String,timeZoneParam:String},eventSourceDefs:[{parseMeta:function(e){return!e.url||"json"!==e.format&&e.format?null:{url:e.url,format:"json",method:(e.method||"GET").toUpperCase(),extraParams:e.extraParams,startParam:e.startParam,endParam:e.endParam,timeZoneParam:e.timeZoneParam}},fetch:function(e,t,n){var o=e.eventSource.meta,i=function(e,t,n){var o,i,a,s,l=n.dateEnv,u=n.options,c={};null==(o=e.startParam)&&(o=u.startParam);null==(i=e.endParam)&&(i=u.endParam);null==(a=e.timeZoneParam)&&(a=u.timeZoneParam);s="function"==typeof e.extraParams?e.extraParams():e.extraParams||{};r(c,s),c[o]=l.formatIso(t.start),c[i]=l.formatIso(t.end),"local"!==l.timeZone&&(c[a]=l.timeZone);return c}(o,e.range,e.context);zo(o.method,o.url,i,(function(e,n){t({rawEvents:e,xhr:n})}),(function(e,t){n({message:e,xhr:t})}))}}]}),lo({recurringTypes:[{parse:function(e,t){if(e.daysOfWeek||e.startTime||e.endTime||e.startRecur||e.endRecur){var n={daysOfWeek:e.daysOfWeek||null,startTime:e.startTime||null,endTime:e.endTime||null,startRecur:e.startRecur?t.createMarker(e.startRecur):null,endRecur:e.endRecur?t.createMarker(e.endRecur):null},r=void 0;return e.duration&&(r=e.duration),!r&&e.startTime&&e.endTime&&(o=e.endTime,i=e.startTime,r={years:o.years-i.years,months:o.months-i.months,days:o.days-i.days,milliseconds:o.milliseconds-i.milliseconds}),{allDayGuess:Boolean(!e.startTime&&!e.endTime),duration:r,typeData:n}}var o,i;return null},expand:function(e,t,n){var r=on(t,{start:e.startRecur,end:e.endRecur});return r?function(e,t,n,r){var o=e?Le(e):null,i=we(n.start),a=n.end,s=[];for(;i<a;){var l=void 0;o&&!o[i.getUTCDay()]||(l=t?r.add(i,t):i,s.push(l)),i=me(i,1)}return s}(e.daysOfWeek,e.startTime,r,n):[]}}],eventRefiners:{daysOfWeek:Pt,startTime:Xe,endTime:Xe,duration:Xe,startRecur:Pt,endRecur:Pt}}),lo({optionChangeHandlers:{events:function(e,t){jo([e],t)},eventSources:jo}}),lo({isLoadingFuncs:[function(e){return xo(e.eventSources)}],contentTypeHandlers:{html:function(){return{render:qo}},domNodes:function(){return{render:Yo}}},propSetHandlers:{dateProfile:function(e,t){t.emitter.trigger("datesSet",r(r({},Mn(e.activeRange,t.dateEnv)),{view:t.viewApi}))},eventStore:function(e,t){var n=t.emitter;n.hasHandlers("eventsSet")&&n.trigger("eventsSet",Gn(e,t))}}})];function qo(e,t){e.innerHTML=t}function Yo(e,t){var n=Array.prototype.slice.call(e.childNodes),r=Array.prototype.slice.call(t);if(!at(n,r)){for(var o=0,i=r;o<i.length;o++){var a=i[o];e.appendChild(a)}n.forEach(F)}}var Zo=function(){function e(e){this.drainedOption=e,this.isRunning=!1,this.isDirty=!1,this.pauseDepths={},this.timeoutId=0}return e.prototype.request=function(e){this.isDirty=!0,this.isPaused()||(this.clearTimeout(),null==e?this.tryDrain():this.timeoutId=setTimeout(this.tryDrain.bind(this),e))},e.prototype.pause=function(e){void 0===e&&(e="");var t=this.pauseDepths;t[e]=(t[e]||0)+1,this.clearTimeout()},e.prototype.resume=function(e,t){void 0===e&&(e="");var n=this.pauseDepths;if(e in n){if(t)delete n[e];else n[e]-=1,n[e]<=0&&delete n[e];this.tryDrain()}},e.prototype.isPaused=function(){return Object.keys(this.pauseDepths).length},e.prototype.tryDrain=function(){if(!this.isRunning&&!this.isPaused()){for(this.isRunning=!0;this.isDirty;)this.isDirty=!1,this.drained();this.isRunning=!1}},e.prototype.clear=function(){this.clearTimeout(),this.isDirty=!1,this.pauseDepths={}},e.prototype.clearTimeout=function(){this.timeoutId&&(clearTimeout(this.timeoutId),this.timeoutId=0)},e.prototype.drained=function(){this.drainedOption&&this.drainedOption()},e}(),Xo=function(){function e(e,t){this.runTaskOption=e,this.drainedOption=t,this.queue=[],this.delayedRunner=new Zo(this.drain.bind(this))}return e.prototype.request=function(e,t){this.queue.push(e),this.delayedRunner.request(t)},e.prototype.pause=function(e){this.delayedRunner.pause(e)},e.prototype.resume=function(e,t){this.delayedRunner.resume(e,t)},e.prototype.drain=function(){for(var e=this.queue;e.length;){for(var t=[],n=void 0;n=e.shift();)this.runTask(n),t.push(n);this.drained(t)}},e.prototype.runTask=function(e){this.runTaskOption&&this.runTaskOption(e)},e.prototype.drained=function(e){this.drainedOption&&this.drainedOption(e)},e}();function Ko(e,t,n){var r;return r=/^(year|month)$/.test(e.currentRangeUnit)?e.currentRange:e.activeRange,n.formatRange(r.start,r.end,bt(t.titleFormat||function(e){var t=e.currentRangeUnit;if("year"===t)return{year:"numeric"};if("month"===t)return{year:"numeric",month:"long"};var n=Ce(e.currentRange.start,e.currentRange.end);if(null!==n&&n>1)return{year:"numeric",month:"short",day:"numeric"};return{year:"numeric",month:"long",day:"numeric"}}(e)),{isEndExclusive:e.isRangeAllDay,defaultSeparator:t.titleRangeSeparator})}var Jo=function(){function e(e){var t=this;this.computeOptionsData=st(this._computeOptionsData),this.computeCurrentViewData=st(this._computeCurrentViewData),this.organizeRawLocales=st(er),this.buildLocale=st(tr),this.buildPluginHooks=uo(),this.buildDateEnv=st($o),this.buildTheme=st(Qo),this.parseToolbars=st(Vo),this.buildViewSpecs=st(wo),this.buildDateProfileGenerator=lt(ei),this.buildViewApi=st(ti),this.buildViewUiProps=lt(oi),this.buildEventUiBySource=st(ni,Ve),this.buildEventUiBases=st(ri),this.parseContextBusinessHours=lt(ai),this.buildTitle=st(Ko),this.emitter=new xr,this.actionRunner=new Xo(this._handleAction.bind(this),this.updateData.bind(this)),this.currentCalendarOptionsInput={},this.currentCalendarOptionsRefined={},this.currentViewOptionsInput={},this.currentViewOptionsRefined={},this.currentCalendarOptionsRefiners={},this.getCurrentData=function(){return t.data},this.dispatch=function(e){t.actionRunner.request(e)},this.props=e,this.actionRunner.pause();var n={},o=this.computeOptionsData(e.optionOverrides,n,e.calendarApi),i=o.calendarOptions.initialView||o.pluginHooks.initialView,a=this.computeCurrentViewData(i,o,e.optionOverrides,n);e.calendarApi.currentDataManager=this,this.emitter.setThisContext(e.calendarApi),this.emitter.setOptions(a.options);var s,l,u,c=(s=o.calendarOptions,l=o.dateEnv,null!=(u=s.initialDate)?l.createMarker(u):Fn(s.now,l)),d=a.dateProfileGenerator.build(c);un(d.activeRange,c)||(c=d.currentRange.start);for(var p={dateEnv:o.dateEnv,options:o.calendarOptions,pluginHooks:o.pluginHooks,calendarApi:e.calendarApi,dispatch:this.dispatch,emitter:this.emitter,getCurrentData:this.getCurrentData},f=0,h=o.pluginHooks.contextInit;f<h.length;f++){(0,h[f])(p)}for(var v=ko(o.calendarOptions,d,p),g={dynamicOptionOverrides:n,currentViewType:i,currentDate:c,dateProfile:d,businessHours:this.parseContextBusinessHours(p),eventSources:v,eventUiBases:{},eventStore:{defs:{},instances:{}},renderableEventStore:{defs:{},instances:{}},dateSelection:null,eventSelection:"",eventDrag:null,eventResize:null,selectionConfig:this.buildViewUiProps(p).selectionConfig},m=r(r({},p),g),y=0,E=o.pluginHooks.reducers;y<E.length;y++){var S=E[y];r(g,S(null,null,m))}ii(g,p)&&this.emitter.trigger("loading",!0),this.state=g,this.updateData(),this.actionRunner.resume()}return e.prototype.resetOptions=function(e,t){var n=this.props;n.optionOverrides=t?r(r({},n.optionOverrides),e):e,this.actionRunner.request({type:"NOTHING"})},e.prototype._handleAction=function(e){var t=this.props,n=this.state,o=this.emitter,i=function(e,t){var n;switch(t.type){case"SET_OPTION":return r(r({},e),((n={})[t.optionName]=t.rawOptionValue,n));default:return e}}(n.dynamicOptionOverrides,e),a=this.computeOptionsData(t.optionOverrides,i,t.calendarApi),s=function(e,t){switch(t.type){case"CHANGE_VIEW_TYPE":e=t.viewType}return e}(n.currentViewType,e),l=this.computeCurrentViewData(s,a,t.optionOverrides,i);t.calendarApi.currentDataManager=this,o.setThisContext(t.calendarApi),o.setOptions(l.options);var u={dateEnv:a.dateEnv,options:a.calendarOptions,pluginHooks:a.pluginHooks,calendarApi:t.calendarApi,dispatch:this.dispatch,emitter:o,getCurrentData:this.getCurrentData},c=n.currentDate,d=n.dateProfile;this.data&&this.data.dateProfileGenerator!==l.dateProfileGenerator&&(d=l.dateProfileGenerator.build(c)),d=function(e,t,n,r){var o;switch(t.type){case"CHANGE_VIEW_TYPE":return r.build(t.dateMarker||n);case"CHANGE_DATE":if(!e.activeRange||!un(e.currentRange,t.dateMarker))return r.build(t.dateMarker);break;case"PREV":if((o=r.buildPrev(e,n)).isValid)return o;break;case"NEXT":if((o=r.buildNext(e,n)).isValid)return o}return e}(d,e,c=function(e,t){switch(t.type){case"CHANGE_DATE":return t.dateMarker;default:return e}}(c,e),l.dateProfileGenerator),un(d.currentRange,c)||(c=d.currentRange.start);for(var p=Mo(n.eventSources,e,d,u),f=Jr(n.eventStore,e,p,d,u),h=xo(p)&&!l.options.progressiveEventRendering&&n.renderableEventStore||f,v=this.buildViewUiProps(u),g=v.eventUiSingleBase,m=v.selectionConfig,y=this.buildEventUiBySource(p),E={dynamicOptionOverrides:i,currentViewType:s,currentDate:c,dateProfile:d,eventSources:p,eventStore:f,renderableEventStore:h,selectionConfig:m,eventUiBases:this.buildEventUiBases(h.defs,g,y),businessHours:this.parseContextBusinessHours(u),dateSelection:Ao(n.dateSelection,e),eventSelection:Uo(n.eventSelection,e),eventDrag:Lo(n.eventDrag,e),eventResize:Wo(n.eventResize,e)},S=r(r({},u),E),D=0,b=a.pluginHooks.reducers;D<b.length;D++){var C=b[D];r(E,C(n,e,S))}var w=ii(n,u),R=ii(E,u);!w&&R?o.trigger("loading",!0):w&&!R&&o.trigger("loading",!1),this.state=E,t.onAction&&t.onAction(e)},e.prototype.updateData=function(){var e,t,n,o,i,a,s,l,u,c=this.props,d=this.state,p=this.data,f=this.computeOptionsData(c.optionOverrides,d.dynamicOptionOverrides,c.calendarApi),h=this.computeCurrentViewData(d.currentViewType,f,c.optionOverrides,d.dynamicOptionOverrides),v=this.data=r(r(r({viewTitle:this.buildTitle(d.dateProfile,h.options,f.dateEnv),calendarApi:c.calendarApi,dispatch:this.dispatch,emitter:this.emitter,getCurrentData:this.getCurrentData},f),h),d),g=f.pluginHooks.optionChangeHandlers,m=p&&p.calendarOptions,y=f.calendarOptions;if(m&&m!==y)for(var E in m.timeZone!==y.timeZone&&(d.eventSources=v.eventSources=(a=v.eventSources,s=d.dateProfile,l=v,u=s?s.activeRange:null,Po(a,Ho(a,l),u,l)),d.eventStore=v.eventStore=(e=v.eventStore,t=p.dateEnv,n=v.dateEnv,o=e.defs,i=Ue(e.instances,(function(e){var i=o[e.defId];return i.allDay||i.recurringDef?e:r(r({},e),{range:{start:n.createMarker(t.toDate(e.range.start,e.forcedStartTzo)),end:n.createMarker(t.toDate(e.range.end,e.forcedEndTzo))},forcedStartTzo:n.canComputeOffset?null:e.forcedStartTzo,forcedEndTzo:n.canComputeOffset?null:e.forcedEndTzo})})),{defs:o,instances:i})),g)m[E]!==y[E]&&g[E](y[E],v);c.onData&&c.onData(v)},e.prototype._computeOptionsData=function(e,t,n){var r=this.processRawCalendarOptions(e,t),o=r.refinedOptions,i=r.pluginHooks,a=r.localeDefaults,s=r.availableLocaleData;si(r.extra);var l=this.buildDateEnv(o.timeZone,o.locale,o.weekNumberCalculation,o.firstDay,o.weekText,i,s,o.defaultRangeSeparator),u=this.buildViewSpecs(i.views,e,t,a),c=this.buildTheme(o,i);return{calendarOptions:o,pluginHooks:i,dateEnv:l,viewSpecs:u,theme:c,toolbarConfig:this.parseToolbars(o,e,c,u,n),localeDefaults:a,availableRawLocales:s.map}},e.prototype.processRawCalendarOptions=function(e,t){var n=_t([wt,e,t]),o=n.locales,i=n.locale,a=this.organizeRawLocales(o),s=a.map,l=this.buildLocale(i||a.defaultCode,s).options,u=this.buildPluginHooks(e.plugins||[],Go),c=this.currentCalendarOptionsRefiners=r(r(r(r(r({},Ct),Rt),Tt),u.listenerRefiners),u.optionRefiners),d={},p=_t([wt,l,e,t]),f={},h=this.currentCalendarOptionsInput,v=this.currentCalendarOptionsRefined,g=!1;for(var m in p)"plugins"!==m&&(p[m]===h[m]||kt[m]&&m in h&&kt[m](h[m],p[m])?f[m]=v[m]:c[m]?(f[m]=c[m](p[m]),g=!0):d[m]=h[m]);return g&&(this.currentCalendarOptionsInput=p,this.currentCalendarOptionsRefined=f),{rawOptions:this.currentCalendarOptionsInput,refinedOptions:this.currentCalendarOptionsRefined,pluginHooks:u,availableLocaleData:a,localeDefaults:l,extra:d}},e.prototype._computeCurrentViewData=function(e,t,n,r){var o=t.viewSpecs[e];if(!o)throw new Error('viewType "'+e+"\" is not available. Please make sure you've loaded all neccessary plugins");var i=this.processRawViewOptions(o,t.pluginHooks,t.localeDefaults,n,r),a=i.refinedOptions;return si(i.extra),{viewSpec:o,options:a,dateProfileGenerator:this.buildDateProfileGenerator({dateProfileGeneratorClass:o.optionDefaults.dateProfileGeneratorClass,duration:o.duration,durationUnit:o.durationUnit,usesMinMaxTime:o.optionDefaults.usesMinMaxTime,dateEnv:t.dateEnv,calendarApi:this.props.calendarApi,slotMinTime:a.slotMinTime,slotMaxTime:a.slotMaxTime,showNonCurrentDates:a.showNonCurrentDates,dayCount:a.dayCount,dateAlignment:a.dateAlignment,dateIncrement:a.dateIncrement,hiddenDays:a.hiddenDays,weekends:a.weekends,nowInput:a.now,validRangeInput:a.validRange,visibleRangeInput:a.visibleRange,monthMode:a.monthMode,fixedWeekCount:a.fixedWeekCount}),viewApi:this.buildViewApi(e,this.getCurrentData,t.dateEnv)}},e.prototype.processRawViewOptions=function(e,t,n,o,i){var a=_t([wt,e.optionDefaults,n,o,e.optionOverrides,i]),s=r(r(r(r(r(r({},Ct),Rt),Tt),xt),t.listenerRefiners),t.optionRefiners),l={},u=this.currentViewOptionsInput,c=this.currentViewOptionsRefined,d=!1,p={};for(var f in a)a[f]===u[f]?l[f]=c[f]:(a[f]===this.currentCalendarOptionsInput[f]?f in this.currentCalendarOptionsRefined&&(l[f]=this.currentCalendarOptionsRefined[f]):s[f]?l[f]=s[f](a[f]):p[f]=a[f],d=!0);return d&&(this.currentViewOptionsInput=a,this.currentViewOptionsRefined=l),{rawOptions:this.currentViewOptionsInput,refinedOptions:this.currentViewOptionsRefined,extra:p}},e}();function $o(e,t,n,r,o,i,a,s){var l=tr(t||a.defaultCode,a.map);return new Jn({calendarSystem:"gregory",timeZone:e,namedTimeZoneImpl:i.namedTimeZonedImpl,locale:l,weekNumberCalculation:n,firstDay:r,weekText:o,cmdFormatter:i.cmdFormatter,defaultSeparator:s})}function Qo(e,t){return new(t.themeClasses[e.themeSystem]||co)(e)}function ei(e){return new(e.dateProfileGeneratorClass||To)(e)}function ti(e,t,n){return new Un(e,t,n)}function ni(e){return Ue(e,(function(e){return e.ui}))}function ri(e,t,n){var r={"":t};for(var o in e){var i=e[o];i.sourceId&&n[i.sourceId]&&(r[o]=n[i.sourceId])}return r}function oi(e){var t=e.options;return{eventUiSingleBase:zt({display:t.eventDisplay,editable:t.editable,startEditable:t.eventStartEditable,durationEditable:t.eventDurationEditable,constraint:t.eventConstraint,overlap:"boolean"==typeof t.eventOverlap?t.eventOverlap:void 0,allow:t.eventAllow,backgroundColor:t.eventBackgroundColor,borderColor:t.eventBorderColor,textColor:t.eventTextColor,color:t.eventColor},e),selectionConfig:zt({constraint:t.selectConstraint,overlap:"boolean"==typeof t.selectOverlap?t.selectOverlap:void 0,allow:t.selectAllow},e)}}function ii(e,t){for(var n=0,r=t.pluginHooks.isLoadingFuncs;n<r.length;n++){if((0,r[n])(e))return!0}return!1}function ai(e){return ar(e.options.businessHours,e)}function si(e,t){for(var n in e)console.warn("Unknown option '"+n+"'"+(t?" for view '"+t+"'":""))}var li=function(e){function t(t){var n=e.call(this,t)||this;return n.handleData=function(e){n.dataManager?n.setState(e):n.state=e},n.dataManager=new Jo({optionOverrides:t.optionOverrides,calendarApi:t.calendarApi,onData:n.handleData}),n}return n(t,e),t.prototype.render=function(){return this.props.children(this.state)},t.prototype.componentDidUpdate=function(e){var t=this.props.optionOverrides;t!==e.optionOverrides&&this.dataManager.resetOptions(t)},t}(Or);var ui=function(e){this.timeZoneName=e},ci=function(){function e(e){this.component=e.component}return e.prototype.destroy=function(){},e}();function di(e,t){return{component:e,el:t.el,useEventCenter:null==t.useEventCenter||t.useEventCenter}}function pi(e){var t;return(t={})[e.component.uid]=e,t}var fi={},hi=function(){function e(e,t){this.emitter=new xr}return e.prototype.destroy=function(){},e.prototype.setMirrorIsVisible=function(e){},e.prototype.setMirrorNeedsRevert=function(e){},e.prototype.setAutoScrollEnabled=function(e){},e}(),vi={},gi={startTime:Xe,duration:Xe,create:Boolean,sourceId:String};function mi(e){var t=It(e,gi),n=t.refined,r=t.extra;return{startTime:n.startTime||null,duration:n.duration||null,create:null==n.create||n.create,sourceId:n.sourceId,leftoverProps:r}}var yi=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this,t=this.props.widgetGroups.map((function(t){return e.renderWidgetGroup(t)}));return Ar.apply(void 0,o(["div",{className:"fc-toolbar-chunk"}],t))},t.prototype.renderWidgetGroup=function(e){for(var t=this.props,n=this.context.theme,i=[],a=!0,s=0,l=e;s<l.length;s++){var u=l[s],c=u.buttonName,d=u.buttonClick,p=u.buttonText,f=u.buttonIcon;if("title"===c)a=!1,i.push(Ar("h2",{className:"fc-toolbar-title"},t.title));else{var h=f?{"aria-label":c}:{},v=["fc-"+c+"-button",n.getClass("button")];c===t.activeButton&&v.push(n.getClass("buttonActive"));var g=!t.isTodayEnabled&&"today"===c||!t.isPrevEnabled&&"prev"===c||!t.isNextEnabled&&"next"===c;i.push(Ar("button",r({disabled:g,className:v.join(" "),onClick:d,type:"button"},h),p||(f?Ar("span",{className:f}):"")))}}if(i.length>1){var m=a&&n.getClass("buttonGroup")||"";return Ar.apply(void 0,o(["div",{className:m}],i))}return i[0]},t}(Yr),Ei=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e,t,n=this.props,r=n.model,o=n.extraClassName,i=!1,a=r.center;return r.left?(i=!0,e=r.left):e=r.start,r.right?(i=!0,t=r.right):t=r.end,Ar("div",{className:[o||"","fc-toolbar",i?"fc-toolbar-ltr":""].join(" ")},this.renderSection("start",e||[]),this.renderSection("center",a||[]),this.renderSection("end",t||[]))},t.prototype.renderSection=function(e,t){var n=this.props;return Ar(yi,{key:e,widgetGroups:t,title:n.title,activeButton:n.activeButton,isTodayEnabled:n.isTodayEnabled,isPrevEnabled:n.isPrevEnabled,isNextEnabled:n.isNextEnabled})},t}(Yr),Si=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.state={availableWidth:null},t.handleEl=function(e){t.el=e,Kr(t.props.elRef,e),t.updateAvailableWidth()},t.handleResize=function(){t.updateAvailableWidth()},t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.state,n=e.aspectRatio,r=["fc-view-harness",n||e.liquid||e.height?"fc-view-harness-active":"fc-view-harness-passive"],o="",i="";return n?null!==t.availableWidth?o=t.availableWidth/n:i=1/n*100+"%":o=e.height||"",Ar("div",{ref:this.handleEl,onClick:e.onClick,className:r.join(" "),style:{height:o,paddingBottom:i}},e.children)},t.prototype.componentDidMount=function(){this.context.addResizeHandler(this.handleResize)},t.prototype.componentWillUnmount=function(){this.context.removeResizeHandler(this.handleResize)},t.prototype.updateAvailableWidth=function(){this.el&&this.props.aspectRatio&&this.setState({availableWidth:this.el.offsetWidth})},t}(Yr),Di=function(e){function t(t){var n=e.call(this,t)||this;return n.handleSegClick=function(e,t){var r=n.component,o=r.context,i=fn(t);if(i&&r.isValidSegDownEl(e.target)){var a=z(e.target,".fc-event-forced-url"),s=a?a.querySelector("a[href]").href:"";o.emitter.trigger("eventClick",{el:t,event:new Bn(r.context,i.eventRange.def,i.eventRange.instance),jsEvent:e,view:o.viewApi}),s&&!e.defaultPrevented&&(window.location.href=s)}},n.destroy=K(t.el,"click",".fc-event",n.handleSegClick),n}return n(t,e),t}(ci),bi=function(e){function t(t){var n,r,o,i,a,s=e.call(this,t)||this;return s.handleEventElRemove=function(e){e===s.currentSegEl&&s.handleSegLeave(null,s.currentSegEl)},s.handleSegEnter=function(e,t){fn(t)&&(s.currentSegEl=t,s.triggerEvent("eventMouseEnter",e,t))},s.handleSegLeave=function(e,t){s.currentSegEl&&(s.currentSegEl=null,s.triggerEvent("eventMouseLeave",e,t))},s.removeHoverListeners=(n=t.el,r=".fc-event",o=s.handleSegEnter,i=s.handleSegLeave,K(n,"mouseover",r,(function(e,t){if(t!==a){a=t,o(e,t);var n=function(e){a=null,i(e,t),t.removeEventListener("mouseleave",n)};t.addEventListener("mouseleave",n)}}))),s}return n(t,e),t.prototype.destroy=function(){this.removeHoverListeners()},t.prototype.triggerEvent=function(e,t,n){var r=this.component,o=r.context,i=fn(n);t&&!r.isValidSegDownEl(t.target)||o.emitter.trigger(e,{el:n,event:new Bn(o,i.eventRange.def,i.eventRange.instance),jsEvent:t,view:o.viewApi})},t}(ci),Ci=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.buildViewContext=st(Gr),t.buildViewPropTransformers=st(Ri),t.buildToolbarProps=st(wi),t.handleNavLinkClick=X("a[data-navlink]",t._handleNavLinkClick.bind(t)),t.headerRef=Lr(),t.footerRef=Lr(),t.interactionsStore={},t.registerInteractiveComponent=function(e,n){var r=di(e,n),o=[Di,bi].concat(t.props.pluginHooks.componentInteractions).map((function(e){return new e(r)}));t.interactionsStore[e.uid]=o,fi[e.uid]=r},t.unregisterInteractiveComponent=function(e){for(var n=0,r=t.interactionsStore[e.uid];n<r.length;n++){r[n].destroy()}delete t.interactionsStore[e.uid],delete fi[e.uid]},t.resizeRunner=new Zo((function(){t.props.emitter.trigger("_resize",!0),t.props.emitter.trigger("windowResize",{view:t.props.viewApi})})),t.handleWindowResize=function(e){var n=t.props.options;n.handleWindowResize&&e.target===window&&t.resizeRunner.request(n.windowResizeDelay)},t}return n(t,e),t.prototype.render=function(){var e,t=this.props,n=t.toolbarConfig,o=t.options,i=this.buildToolbarProps(t.viewSpec,t.dateProfile,t.dateProfileGenerator,t.currentDate,Fn(t.options.now,t.dateEnv),t.viewTitle),a=!1,s="";t.isHeightAuto||t.forPrint?s="":null!=o.height?a=!0:null!=o.contentHeight?s=o.contentHeight:e=Math.max(o.aspectRatio,.5);var l=this.buildViewContext(t.viewSpec,t.viewApi,t.options,t.dateProfileGenerator,t.dateEnv,t.theme,t.pluginHooks,t.dispatch,t.getCurrentData,t.emitter,t.calendarApi,this.registerInteractiveComponent,this.unregisterInteractiveComponent);return Ar(jr.Provider,{value:l},n.headerToolbar&&Ar(Ei,r({ref:this.headerRef,extraClassName:"fc-header-toolbar",model:n.headerToolbar},i)),Ar(Si,{liquid:a,height:s,aspectRatio:e,onClick:this.handleNavLinkClick},this.renderView(t),this.buildAppendContent()),n.footerToolbar&&Ar(Ei,r({ref:this.footerRef,extraClassName:"fc-footer-toolbar",model:n.footerToolbar},i)))},t.prototype.componentDidMount=function(){var e=this.props;this.calendarInteractions=e.pluginHooks.calendarInteractions.map((function(t){return new t(e)})),window.addEventListener("resize",this.handleWindowResize);var t=e.pluginHooks.propSetHandlers;for(var n in t)t[n](e[n],e)},t.prototype.componentDidUpdate=function(e){var t=this.props,n=t.pluginHooks.propSetHandlers;for(var r in n)t[r]!==e[r]&&n[r](t[r],t)},t.prototype.componentWillUnmount=function(){window.removeEventListener("resize",this.handleWindowResize),this.resizeRunner.clear();for(var e=0,t=this.calendarInteractions;e<t.length;e++){t[e].destroy()}this.props.emitter.trigger("_unmount")},t.prototype._handleNavLinkClick=function(e,t){var n=this.props,r=n.dateEnv,o=n.options,i=n.calendarApi,a=t.getAttribute("data-navlink");a=a?JSON.parse(a):{};var s=r.createMarker(a.date),l=a.type,u="day"===l?o.navLinkDayClick:"week"===l?o.navLinkWeekClick:null;"function"==typeof u?u.call(i,r.toDate(s),e):("string"==typeof u&&(l=u),i.zoomTo(s,l))},t.prototype.buildAppendContent=function(){var e=this.props,t=e.pluginHooks.viewContainerAppends.map((function(t){return t(e)}));return Ar.apply(void 0,o([Wr,{}],t))},t.prototype.renderView=function(e){for(var t=e.pluginHooks,n=e.viewSpec,o={dateProfile:e.dateProfile,businessHours:e.businessHours,eventStore:e.renderableEventStore,eventUiBases:e.eventUiBases,dateSelection:e.dateSelection,eventSelection:e.eventSelection,eventDrag:e.eventDrag,eventResize:e.eventResize,isHeightAuto:e.isHeightAuto,forPrint:e.forPrint},i=0,a=this.buildViewPropTransformers(t.viewPropsTransformers);i<a.length;i++){var s=a[i];r(o,s.transform(o,e))}var l=n.component;return Ar(l,r({},o))},t}(qr);function wi(e,t,n,r,o,i){var a=n.build(o,void 0,!1),s=n.buildPrev(t,r,!1),l=n.buildNext(t,r,!1);return{title:i,activeButton:e.type,isTodayEnabled:a.isValid&&!un(t.currentRange,o),isPrevEnabled:s.isValid,isNextEnabled:l.isValid}}function Ri(e){return e.map((function(e){return new e}))}var Ti=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.state={forPrint:!1},t.handleBeforePrint=function(){t.setState({forPrint:!0})},t.handleAfterPrint=function(){t.setState({forPrint:!1})},t}return n(t,e),t.prototype.render=function(){var e=this.props,t=e.options,n=this.state.forPrint,r=n||"auto"===t.height||"auto"===t.contentHeight,o=r||null==t.height?"":t.height,i=["fc",n?"fc-media-print":"fc-media-screen","fc-direction-"+t.direction,e.theme.getClass("root")];return pr()||i.push("fc-liquid-hack"),e.children(i,o,r,n)},t.prototype.componentDidMount=function(){var e=this.props.emitter;e.on("_beforeprint",this.handleBeforePrint),e.on("_afterprint",this.handleAfterPrint)},t.prototype.componentWillUnmount=function(){var e=this.props.emitter;e.off("_beforeprint",this.handleBeforePrint),e.off("_afterprint",this.handleAfterPrint)},t}(Yr);function ki(e,t){return bt(!e||t>10?{weekday:"short"}:t>1?{weekday:"short",month:"numeric",day:"numeric",omitCommas:!0}:{weekday:"long"})}var Mi="fc-col-header-cell";function xi(e){return e.text}var _i=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.context,t=e.dateEnv,n=e.options,o=e.theme,i=e.viewApi,a=this.props,s=a.date,l=a.dateProfile,u=gr(s,a.todayRange,null,l),c=[Mi].concat(mr(u,o)),d=t.format(s,a.dayHeaderFormat),p=n.navLinks&&!u.isDisabled&&a.colCnt>1?{"data-navlink":yr(s),tabIndex:0}:{},f=r(r(r({date:t.toDate(s),view:i},a.extraHookProps),{text:d}),u);return Ar(fo,{hookProps:f,classNames:n.dayHeaderClassNames,content:n.dayHeaderContent,defaultContent:xi,didMount:n.dayHeaderDidMount,willUnmount:n.dayHeaderWillUnmount},(function(e,t,n,o){return Ar("th",r({ref:e,className:c.concat(t).join(" "),"data-date":u.isDisabled?void 0:rt(s),colSpan:a.colSpan},a.extraDataAttrs),Ar("div",{className:"fc-scrollgrid-sync-inner"},!u.isDisabled&&Ar("a",r({ref:n,className:["fc-col-header-cell-cushion",a.isSticky?"fc-sticky":""].join(" ")},p),o)))}))},t}(Yr),Ii=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.dateEnv,o=t.theme,i=t.viewApi,a=t.options,s=me(new Date(2592e5),e.dow),l={dow:e.dow,isDisabled:!1,isFuture:!1,isPast:!1,isToday:!1,isOther:!1},u=[Mi].concat(mr(l,o),e.extraClassNames||[]),c=n.format(s,e.dayHeaderFormat),d=r(r(r(r({date:s},l),{view:i}),e.extraHookProps),{text:c});return Ar(fo,{hookProps:d,classNames:a.dayHeaderClassNames,content:a.dayHeaderContent,defaultContent:xi,didMount:a.dayHeaderDidMount,willUnmount:a.dayHeaderWillUnmount},(function(t,n,o,i){return Ar("th",r({ref:t,className:u.concat(n).join(" "),colSpan:e.colSpan},e.extraDataAttrs),Ar("div",{className:"fc-scrollgrid-sync-inner"},Ar("a",{className:["fc-col-header-cell-cushion",e.isSticky?"fc-sticky":""].join(" "),ref:o},i)))}))},t}(Yr),Pi=function(e){function t(t,n){var r=e.call(this,t,n)||this;return r.initialNowDate=Fn(n.options.now,n.dateEnv),r.initialNowQueriedMs=(new Date).valueOf(),r.state=r.computeTiming().currentState,r}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.state;return e.children(t.nowDate,t.todayRange)},t.prototype.componentDidMount=function(){this.setTimeout()},t.prototype.componentDidUpdate=function(e){e.unit!==this.props.unit&&(this.clearTimeout(),this.setTimeout())},t.prototype.componentWillUnmount=function(){this.clearTimeout()},t.prototype.computeTiming=function(){var e=this.props,t=this.context,n=ye(this.initialNowDate,(new Date).valueOf()-this.initialNowQueriedMs),r=t.dateEnv.startOf(n,e.unit),o=t.dateEnv.add(r,Xe(1,e.unit)),i=o.valueOf()-n.valueOf();return i=Math.min(864e5,i),{currentState:{nowDate:r,todayRange:Ni(r)},nextState:{nowDate:o,todayRange:Ni(o)},waitMs:i}},t.prototype.setTimeout=function(){var e=this,t=this.computeTiming(),n=t.nextState,r=t.waitMs;this.timeoutId=setTimeout((function(){e.setState(n,(function(){e.setTimeout()}))}),r)},t.prototype.clearTimeout=function(){this.timeoutId&&clearTimeout(this.timeoutId)},t.contextType=jr,t}(Or);function Ni(e){var t=we(e);return{start:t,end:me(t,1)}}var Hi=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.createDayHeaderFormatter=st(Oi),t}return n(t,e),t.prototype.render=function(){var e=this.context,t=this.props,n=t.dates,r=t.dateProfile,o=t.datesRepDistinctDays,i=t.renderIntro,a=this.createDayHeaderFormatter(e.options.dayHeaderFormat,o,n.length);return Ar(Pi,{unit:"day"},(function(e,t){return Ar("tr",null,i&&i("day"),n.map((function(e){return o?Ar(_i,{key:e.toISOString(),date:e,dateProfile:r,todayRange:t,colCnt:n.length,dayHeaderFormat:a}):Ar(Ii,{key:e.getUTCDay(),dow:e.getUTCDay(),dayHeaderFormat:a})})))}))},t}(Yr);function Oi(e,t,n){return e||ki(t,n)}var Ai=function(){function e(e,t){for(var n=e.start,r=e.end,o=[],i=[],a=-1;n<r;)t.isHiddenDay(n)?o.push(a+.5):(a+=1,o.push(a),i.push(n)),n=me(n,1);this.dates=i,this.indices=o,this.cnt=i.length}return e.prototype.sliceRange=function(e){var t=this.getDateDayIndex(e.start),n=this.getDateDayIndex(me(e.end,-1)),r=Math.max(0,t),o=Math.min(this.cnt-1,n);return(r=Math.ceil(r))<=(o=Math.floor(o))?{firstIndex:r,lastIndex:o,isStart:t===r,isEnd:n===o}:null},e.prototype.getDateDayIndex=function(e){var t=this.indices,n=Math.floor(Se(this.dates[0],e));return n<0?t[0]-1:n>=t.length?t[t.length-1]+1:t[n]},e}(),Ui=function(){function e(e,t){var n,r,o,i=e.dates;if(t){for(r=i[0].getUTCDay(),n=1;n<i.length&&i[n].getUTCDay()!==r;n+=1);o=Math.ceil(i.length/n)}else o=1,n=i.length;this.rowCnt=o,this.colCnt=n,this.daySeries=e,this.cells=this.buildCells(),this.headerDates=this.buildHeaderDates()}return e.prototype.buildCells=function(){for(var e=[],t=0;t<this.rowCnt;t+=1){for(var n=[],r=0;r<this.colCnt;r+=1)n.push(this.buildCell(t,r));e.push(n)}return e},e.prototype.buildCell=function(e,t){var n=this.daySeries.dates[e*this.colCnt+t];return{key:n.toISOString(),date:n}},e.prototype.buildHeaderDates=function(){for(var e=[],t=0;t<this.colCnt;t+=1)e.push(this.cells[0][t].date);return e},e.prototype.sliceRange=function(e){var t=this.colCnt,n=this.daySeries.sliceRange(e),r=[];if(n)for(var o=n.firstIndex,i=n.lastIndex,a=o;a<=i;){var s=Math.floor(a/t),l=Math.min((s+1)*t,i+1);r.push({row:s,firstCol:a%t,lastCol:(l-1)%t,isStart:n.isStart&&a===o,isEnd:n.isEnd&&l-1===i}),a=l}return r},e}(),Li=function(){function e(){this.sliceBusinessHours=st(this._sliceBusinessHours),this.sliceDateSelection=st(this._sliceDateSpan),this.sliceEventStore=st(this._sliceEventStore),this.sliceEventDrag=st(this._sliceInteraction),this.sliceEventResize=st(this._sliceInteraction),this.forceDayIfListItem=!1}return e.prototype.sliceProps=function(e,t,n,r){for(var i=[],a=4;a<arguments.length;a++)i[a-4]=arguments[a];var s=e.eventUiBases,l=this.sliceEventStore.apply(this,o([e.eventStore,s,t,n],i));return{dateSelectionSegs:this.sliceDateSelection.apply(this,o([e.dateSelection,s,r],i)),businessHourSegs:this.sliceBusinessHours.apply(this,o([e.businessHours,t,n,r],i)),fgEventSegs:l.fg,bgEventSegs:l.bg,eventDrag:this.sliceEventDrag.apply(this,o([e.eventDrag,s,t,n],i)),eventResize:this.sliceEventResize.apply(this,o([e.eventResize,s,t,n],i)),eventSelection:e.eventSelection}},e.prototype.sliceNowDate=function(e,t){for(var n=[],r=2;r<arguments.length;r++)n[r-2]=arguments[r];return this._sliceDateSpan.apply(this,o([{range:{start:e,end:ye(e,1)},allDay:!1},{},t],n))},e.prototype._sliceBusinessHours=function(e,t,n,r){for(var i=[],a=4;a<arguments.length;a++)i[a-4]=arguments[a];return e?this._sliceEventStore.apply(this,o([Ge(e,Wi(t,Boolean(n)),r),{},t,n],i)).bg:[]},e.prototype._sliceEventStore=function(e,t,n,r){for(var o=[],i=4;i<arguments.length;i++)o[i-4]=arguments[i];if(e){var a=cn(e,t,Wi(n,Boolean(r)),r);return{bg:this.sliceEventRanges(a.bg,o),fg:this.sliceEventRanges(a.fg,o)}}return{bg:[],fg:[]}},e.prototype._sliceInteraction=function(e,t,n,r){for(var o=[],i=4;i<arguments.length;i++)o[i-4]=arguments[i];if(!e)return null;var a=cn(e.mutatedEvents,t,Wi(n,Boolean(r)),r);return{segs:this.sliceEventRanges(a.fg,o),affectedInstances:e.affectedEvents.instances,isEvent:e.isEvent}},e.prototype._sliceDateSpan=function(e,t,n){for(var r=[],i=3;i<arguments.length;i++)r[i-3]=arguments[i];if(!e)return[];for(var a=_n(e,t,n),s=this.sliceRange.apply(this,o([e.range],r)),l=0,u=s;l<u.length;l++){var c=u[l];c.eventRange=a}return s},e.prototype.sliceEventRanges=function(e,t){for(var n=[],r=0,o=e;r<o.length;r++){var i=o[r];n.push.apply(n,this.sliceEventRange(i,t))}return n},e.prototype.sliceEventRange=function(e,t){var n=e.range;this.forceDayIfListItem&&"list-item"===e.ui.display&&(n={start:n.start,end:me(n.start,1)});for(var r=this.sliceRange.apply(this,o([n],t)),i=0,a=r;i<a.length;i++){var s=a[i];s.eventRange=e,s.isStart=e.isStart&&s.isStart,s.isEnd=e.isEnd&&s.isEnd}return r},e}();function Wi(e,t){var n=e.activeRange;return t?n:{start:ye(n.start,e.slotMinTime.milliseconds),end:ye(n.end,e.slotMaxTime.milliseconds-864e5)}}var Vi=/^(visible|hidden)$/,Fi=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.handleEl=function(e){t.el=e,Kr(t.props.elRef,e)},t}return n(t,e),t.prototype.render=function(){var e=this.props,t=e.liquid,n=e.liquidIsAbsolute,r=t&&n,o=["fc-scroller"];return t&&(n?o.push("fc-scroller-liquid-absolute"):o.push("fc-scroller-liquid")),Ar("div",{ref:this.handleEl,className:o.join(" "),style:{overflowX:e.overflowX,overflowY:e.overflowY,left:r&&-(e.overcomeLeft||0)||"",right:r&&-(e.overcomeRight||0)||"",bottom:r&&-(e.overcomeBottom||0)||"",marginLeft:!r&&-(e.overcomeLeft||0)||"",marginRight:!r&&-(e.overcomeRight||0)||"",marginBottom:!r&&-(e.overcomeBottom||0)||"",maxHeight:e.maxHeight||""}},e.children)},t.prototype.needsXScrolling=function(){if(Vi.test(this.props.overflowX))return!1;for(var e=this.el,t=this.el.getBoundingClientRect().width-this.getYScrollbarWidth(),n=e.children,r=0;r<n.length;r+=1){if(n[r].getBoundingClientRect().width>t)return!0}return!1},t.prototype.needsYScrolling=function(){if(Vi.test(this.props.overflowY))return!1;for(var e=this.el,t=this.el.getBoundingClientRect().height-this.getXScrollbarWidth(),n=e.children,r=0;r<n.length;r+=1){if(n[r].getBoundingClientRect().height>t)return!0}return!1},t.prototype.getXScrollbarWidth=function(){return Vi.test(this.props.overflowX)?0:this.el.offsetHeight-this.el.clientHeight},t.prototype.getYScrollbarWidth=function(){return Vi.test(this.props.overflowY)?0:this.el.offsetWidth-this.el.clientWidth},t}(Yr),zi=function(){function e(e){var t=this;this.masterCallback=e,this.currentMap={},this.depths={},this.callbackMap={},this.handleValue=function(e,n){var r=t,o=r.depths,i=r.currentMap,a=!1,s=!1;null!==e?(a=n in i,i[n]=e,o[n]=(o[n]||0)+1,s=!0):(o[n]-=1,o[n]||(delete i[n],delete t.callbackMap[n],a=!0)),t.masterCallback&&(a&&t.masterCallback(null,String(n)),s&&t.masterCallback(e,String(n)))}}return e.prototype.createRef=function(e){var t=this,n=this.callbackMap[e];return n||(n=this.callbackMap[e]=function(n){t.handleValue(n,String(e))}),n},e.prototype.collect=function(e,t,n){return je(this.currentMap,e,t,n)},e.prototype.getAll=function(){return We(this.currentMap)},e}();function Bi(e){for(var t=0,n=0,r=j(e,".fc-scrollgrid-shrink");n<r.length;n++){var o=r[n];t=Math.max(t,he(o))}return Math.ceil(t)}function ji(e,t){return e.liquid&&t.liquid}function Gi(e,t){return null!=t.maxHeight||ji(e,t)}function qi(e,t,n){var r=n.expandRows;return"function"==typeof t.content?t.content(n):Ar("table",{className:[t.tableClassName,e.syncRowHeights?"fc-scrollgrid-sync-table":""].join(" "),style:{minWidth:n.tableMinWidth,width:n.clientWidth,height:r?n.clientHeight:""}},n.tableColGroupNode,Ar("tbody",{},"function"==typeof t.rowContent?t.rowContent(n):t.rowContent))}function Yi(e,t){return at(e,t,Ve)}function Zi(e,t){for(var n=[],r=0,i=e;r<i.length;r++)for(var a=i[r],s=a.span||1,l=0;l<s;l+=1)n.push(Ar("col",{style:{width:"shrink"===a.width?Xi(t):a.width||"",minWidth:a.minWidth||""}}));return Ar.apply(void 0,o(["colgroup",{}],n))}function Xi(e){return null==e?4:e}function Ki(e){for(var t=0,n=e;t<n.length;t++){if("shrink"===n[t].width)return!0}return!1}function Ji(e,t){var n=["fc-scrollgrid",t.theme.getClass("table")];return e&&n.push("fc-scrollgrid-liquid"),n}function $i(e,t){var n=["fc-scrollgrid-section","fc-scrollgrid-section-"+e.type,e.className];return t&&e.liquid&&null==e.maxHeight&&n.push("fc-scrollgrid-section-liquid"),e.isSticky&&n.push("fc-scrollgrid-section-sticky"),n}function Qi(e){return Ar("div",{className:"fc-scrollgrid-sticky-shim",style:{width:e.clientWidth,minWidth:e.tableMinWidth}})}function ea(e){var t=e.stickyHeaderDates;return null!=t&&"auto"!==t||(t="auto"===e.height||"auto"===e.viewHeight),t}function ta(e){var t=e.stickyFooterScrollbar;return null!=t&&"auto"!==t||(t="auto"===e.height||"auto"===e.viewHeight),t}var na=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.processCols=st((function(e){return e}),Yi),t.renderMicroColGroup=st(Zi),t.scrollerRefs=new zi,t.scrollerElRefs=new zi(t._handleScrollerEl.bind(t)),t.state={shrinkWidth:null,forceYScrollbars:!1,scrollerClientWidths:{},scrollerClientHeights:{}},t.handleSizing=function(){t.setState(r({shrinkWidth:t.computeShrinkWidth()},t.computeScrollerDims()))},t}return n(t,e),t.prototype.render=function(){for(var e,t=this.props,n=this.state,r=this.context,i=t.sections||[],a=this.processCols(t.cols),s=this.renderMicroColGroup(a,n.shrinkWidth),l=Ji(t.liquid,r),u=i.length,c=0,d=[],p=[],f=[];c<u&&"header"===(e=i[c]).type;)d.push(this.renderSection(e,s)),c+=1;for(;c<u&&"body"===(e=i[c]).type;)p.push(this.renderSection(e,s)),c+=1;for(;c<u&&"footer"===(e=i[c]).type;)f.push(this.renderSection(e,s)),c+=1;var h=!pr();return Ar("table",{className:l.join(" "),style:{height:t.height}},Boolean(!h&&d.length)&&Ar.apply(void 0,o(["thead",{}],d)),Boolean(!h&&p.length)&&Ar.apply(void 0,o(["tbody",{}],p)),Boolean(!h&&f.length)&&Ar.apply(void 0,o(["tfoot",{}],f)),h&&Ar.apply(void 0,o(["tbody",{}],d,p,f)))},t.prototype.renderSection=function(e,t){return"outerContent"in e?Ar(Wr,{key:e.key},e.outerContent):Ar("tr",{key:e.key,className:$i(e,this.props.liquid).join(" ")},this.renderChunkTd(e,t,e.chunk))},t.prototype.renderChunkTd=function(e,t,n){if("outerContent"in n)return n.outerContent;var r=this.props,o=this.state,i=o.forceYScrollbars,a=o.scrollerClientWidths,s=o.scrollerClientHeights,l=Gi(r,e),u=ji(r,e),c=r.liquid?i?"scroll":l?"auto":"hidden":"visible",d=e.key,p=qi(e,n,{tableColGroupNode:t,tableMinWidth:"",clientWidth:void 0!==a[d]?a[d]:null,clientHeight:void 0!==s[d]?s[d]:null,expandRows:e.expandRows,syncRowHeights:!1,rowSyncHeights:[],reportRowHeightChange:function(){}});return Ar("td",{ref:n.elRef},Ar("div",{className:"fc-scroller-harness"+(u?" fc-scroller-harness-liquid":"")},Ar(Fi,{ref:this.scrollerRefs.createRef(d),elRef:this.scrollerElRefs.createRef(d),overflowY:c,overflowX:r.liquid?"hidden":"visible",maxHeight:e.maxHeight,liquid:u,liquidIsAbsolute:!0},p)))},t.prototype._handleScrollerEl=function(e,t){var n=function(e,t){for(var n=0,r=e;n<r.length;n++){var o=r[n];if(o.key===t)return o}return null}(this.props.sections,t);n&&Kr(n.chunk.scrollerElRef,e)},t.prototype.componentDidMount=function(){this.handleSizing(),this.context.addResizeHandler(this.handleSizing)},t.prototype.componentDidUpdate=function(){this.handleSizing()},t.prototype.componentWillUnmount=function(){this.context.removeResizeHandler(this.handleSizing)},t.prototype.computeShrinkWidth=function(){return Ki(this.props.cols)?Bi(this.scrollerElRefs.getAll()):0},t.prototype.computeScrollerDims=function(){var e=br(),t=this.scrollerRefs,n=this.scrollerElRefs,r=!1,o={},i={};for(var a in t.currentMap){var s=t.currentMap[a];if(s&&s.needsYScrolling()){r=!0;break}}for(var l=0,u=this.props.sections;l<u.length;l++){a=u[l].key;var c=n.currentMap[a];if(c){var d=c.parentNode;o[a]=Math.floor(d.getBoundingClientRect().width-(r?e.y:0)),i[a]=Math.floor(d.getBoundingClientRect().height)}}return{forceYScrollbars:r,scrollerClientWidths:o,scrollerClientHeights:i}},t}(Yr);na.addStateEquality({scrollerClientWidths:Ve,scrollerClientHeights:Ve});var ra=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.elRef=Lr(),t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.options,r=e.seg,o=r.eventRange,i=o.ui,a={event:new Bn(t,o.def,o.instance),view:t.viewApi,timeText:e.timeText,textColor:i.textColor,backgroundColor:i.backgroundColor,borderColor:i.borderColor,isDraggable:!e.disableDragging&&yn(r,t),isStartResizable:!e.disableResizing&&En(r,t),isEndResizable:!e.disableResizing&&Sn(r),isMirror:Boolean(e.isDragging||e.isResizing||e.isDateSelecting),isStart:Boolean(r.isStart),isEnd:Boolean(r.isEnd),isPast:Boolean(e.isPast),isFuture:Boolean(e.isFuture),isToday:Boolean(e.isToday),isSelected:Boolean(e.isSelected),isDragging:Boolean(e.isDragging),isResizing:Boolean(e.isResizing)},s=Cn(a).concat(i.classNames);return Ar(fo,{hookProps:a,classNames:n.eventClassNames,content:n.eventContent,defaultContent:e.defaultContent,didMount:n.eventDidMount,willUnmount:n.eventWillUnmount,elRef:this.elRef},(function(t,n,r,o){return e.children(t,s.concat(n),r,o,a)}))},t.prototype.componentDidMount=function(){pn(this.elRef.current,this.props.seg)},t.prototype.componentDidUpdate=function(e){var t=this.props.seg;t!==e.seg&&pn(this.elRef.current,t)},t}(Yr),oa=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=e.seg,o=t.options.eventTimeFormat||e.defaultTimeFormat,i=Dn(n,o,t,e.defaultDisplayEventTime,e.defaultDisplayEventEnd);return Ar(ra,{seg:n,timeText:i,disableDragging:e.disableDragging,disableResizing:e.disableResizing,defaultContent:e.defaultContent||ia,isDragging:e.isDragging,isResizing:e.isResizing,isDateSelecting:e.isDateSelecting,isSelected:e.isSelected,isPast:e.isPast,isFuture:e.isFuture,isToday:e.isToday},(function(t,o,i,a,s){return Ar("a",r({className:e.extraClassNames.concat(o).join(" "),style:{borderColor:s.borderColor,backgroundColor:s.backgroundColor},ref:t},function(e){var t=e.eventRange.def.url;return t?{href:t}:{}}(n)),Ar("div",{className:"fc-event-main",ref:i,style:{color:s.textColor}},a),s.isStartResizable&&Ar("div",{className:"fc-event-resizer fc-event-resizer-start"}),s.isEndResizable&&Ar("div",{className:"fc-event-resizer fc-event-resizer-end"}))}))},t}(Yr);function ia(e){return Ar("div",{className:"fc-event-main-frame"},e.timeText&&Ar("div",{className:"fc-event-time"},e.timeText),Ar("div",{className:"fc-event-title-container"},Ar("div",{className:"fc-event-title fc-sticky"},e.event.title||Ar(Wr,null," "))))}var aa=function(e){return Ar(jr.Consumer,null,(function(t){var n=t.options,r={isAxis:e.isAxis,date:t.dateEnv.toDate(e.date),view:t.viewApi};return Ar(fo,{hookProps:r,classNames:n.nowIndicatorClassNames,content:n.nowIndicatorContent,didMount:n.nowIndicatorDidMount,willUnmount:n.nowIndicatorWillUnmount},e.children)}))},sa=bt({day:"numeric"}),la=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.options,r=ua({date:e.date,dateProfile:e.dateProfile,todayRange:e.todayRange,showDayNumber:e.showDayNumber,extraProps:e.extraHookProps,viewApi:t.viewApi,dateEnv:t.dateEnv});return Ar(vo,{hookProps:r,content:n.dayCellContent,defaultContent:e.defaultContent},e.children)},t}(Yr);function ua(e){var t=e.date,n=e.dateEnv,o=gr(t,e.todayRange,null,e.dateProfile);return r(r(r({date:n.toDate(t),view:e.viewApi},o),{dayNumberText:e.showDayNumber?n.format(t,sa):""}),e.extraProps)}var ca=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.refineHookProps=lt(ua),t.normalizeClassNames=yo(),t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.options,r=this.refineHookProps({date:e.date,dateProfile:e.dateProfile,todayRange:e.todayRange,showDayNumber:e.showDayNumber,extraProps:e.extraHookProps,viewApi:t.viewApi,dateEnv:t.dateEnv}),o=mr(r,t.theme).concat(r.isDisabled?[]:this.normalizeClassNames(n.dayCellClassNames,r)),i=r.isDisabled?{}:{"data-date":rt(e.date)};return Ar(mo,{hookProps:r,didMount:n.dayCellDidMount,willUnmount:n.dayCellWillUnmount,elRef:e.elRef},(function(t){return e.children(t,o,i,r.isDisabled)}))},t}(Yr);function da(e){return Ar("div",{className:"fc-"+e})}var pa=function(e){return Ar(ra,{defaultContent:fa,seg:e.seg,timeText:"",disableDragging:!0,disableResizing:!0,isDragging:!1,isResizing:!1,isDateSelecting:!1,isSelected:!1,isPast:e.isPast,isFuture:e.isFuture,isToday:e.isToday},(function(e,t,n,r,o){return Ar("div",{ref:e,className:["fc-bg-event"].concat(t).join(" "),style:{backgroundColor:o.backgroundColor}},r)}))};function fa(e){return e.event.title&&Ar("div",{className:"fc-event-title"},e.event.title)}var ha=function(e){return Ar(jr.Consumer,null,(function(t){var n=t.dateEnv,r=t.options,o=e.date,i=r.weekNumberFormat||e.defaultFormat,a=n.computeWeekNumber(o),s=n.format(o,i);return Ar(fo,{hookProps:{num:a,text:s,date:o},classNames:r.weekNumberClassNames,content:r.weekNumberContent,defaultContent:va,didMount:r.weekNumberDidMount,willUnmount:r.weekNumberWillUnmount},e.children)}))};function va(e){return e.text}var ga=function(e){function t(t,n){void 0===n&&(n={});var o=e.call(this)||this;return o.isRendering=!1,o.isRendered=!1,o.currentClassNames=[],o.customContentRenderId=0,o.handleAction=function(e){switch(e.type){case"SET_EVENT_DRAG":case"SET_EVENT_RESIZE":o.renderRunner.tryDrain()}},o.handleData=function(e){o.currentData=e,o.renderRunner.request(e.calendarOptions.rerenderDelay)},o.handleRenderRequest=function(){if(o.isRendering){o.isRendered=!0;var e=o.currentData;Ur(Ar(Ti,{options:e.calendarOptions,theme:e.theme,emitter:e.emitter},(function(t,n,i,a){return o.setClassNames(t),o.setHeight(n),Ar(ho.Provider,{value:o.customContentRenderId},Ar(Ci,r({isHeightAuto:i,forPrint:a},e)))})),o.el)}else o.isRendered&&(o.isRendered=!1,zr(o.el),o.setClassNames([]),o.setHeight(""));Fr()},o.el=t,o.renderRunner=new Zo(o.handleRenderRequest),new Jo({optionOverrides:n,calendarApi:o,onAction:o.handleAction,onData:o.handleData}),o}return n(t,e),Object.defineProperty(t.prototype,"view",{get:function(){return this.currentData.viewApi},enumerable:!1,configurable:!0}),t.prototype.render=function(){var e=this.isRendering;e?this.customContentRenderId+=1:this.isRendering=!0,this.renderRunner.request(),e&&this.updateSize()},t.prototype.destroy=function(){this.isRendering&&(this.isRendering=!1,this.renderRunner.request())},t.prototype.updateSize=function(){e.prototype.updateSize.call(this),Fr()},t.prototype.batchRendering=function(e){this.renderRunner.pause("batchRendering"),e(),this.renderRunner.resume("batchRendering")},t.prototype.pauseRendering=function(){this.renderRunner.pause("pauseRendering")},t.prototype.resumeRendering=function(){this.renderRunner.resume("pauseRendering",!0)},t.prototype.resetOptions=function(e,t){this.currentDataManager.resetOptions(e,t)},t.prototype.setClassNames=function(e){if(!at(e,this.currentClassNames)){for(var t=this.el.classList,n=0,r=this.currentClassNames;n<r.length;n++){var o=r[n];t.remove(o)}for(var i=0,a=e;i<a.length;i++){o=a[i];t.add(o)}this.currentClassNames=e}},t.prototype.setHeight=function(e){Y(this.el,"height",e)},t}(zn);vi.touchMouseIgnoreWait=500;var ma=0,ya=0,Ea=!1,Sa=function(){function e(e){var t=this;this.subjectEl=null,this.selector="",this.handleSelector="",this.shouldIgnoreMove=!1,this.shouldWatchScroll=!0,this.isDragging=!1,this.isTouchDragging=!1,this.wasTouchScroll=!1,this.handleMouseDown=function(e){if(!t.shouldIgnoreMouse()&&function(e){return 0===e.button&&!e.ctrlKey}(e)&&t.tryStart(e)){var n=t.createEventFromMouse(e,!0);t.emitter.trigger("pointerdown",n),t.initScrollWatch(n),t.shouldIgnoreMove||document.addEventListener("mousemove",t.handleMouseMove),document.addEventListener("mouseup",t.handleMouseUp)}},this.handleMouseMove=function(e){var n=t.createEventFromMouse(e);t.recordCoords(n),t.emitter.trigger("pointermove",n)},this.handleMouseUp=function(e){document.removeEventListener("mousemove",t.handleMouseMove),document.removeEventListener("mouseup",t.handleMouseUp),t.emitter.trigger("pointerup",t.createEventFromMouse(e)),t.cleanup()},this.handleTouchStart=function(e){if(t.tryStart(e)){t.isTouchDragging=!0;var n=t.createEventFromTouch(e,!0);t.emitter.trigger("pointerdown",n),t.initScrollWatch(n);var r=e.target;t.shouldIgnoreMove||r.addEventListener("touchmove",t.handleTouchMove),r.addEventListener("touchend",t.handleTouchEnd),r.addEventListener("touchcancel",t.handleTouchEnd),window.addEventListener("scroll",t.handleTouchScroll,!0)}},this.handleTouchMove=function(e){var n=t.createEventFromTouch(e);t.recordCoords(n),t.emitter.trigger("pointermove",n)},this.handleTouchEnd=function(e){if(t.isDragging){var n=e.target;n.removeEventListener("touchmove",t.handleTouchMove),n.removeEventListener("touchend",t.handleTouchEnd),n.removeEventListener("touchcancel",t.handleTouchEnd),window.removeEventListener("scroll",t.handleTouchScroll,!0),t.emitter.trigger("pointerup",t.createEventFromTouch(e)),t.cleanup(),t.isTouchDragging=!1,ma+=1,setTimeout((function(){ma-=1}),vi.touchMouseIgnoreWait)}},this.handleTouchScroll=function(){t.wasTouchScroll=!0},this.handleScroll=function(e){if(!t.shouldIgnoreMove){var n=window.pageXOffset-t.prevScrollX+t.prevPageX,r=window.pageYOffset-t.prevScrollY+t.prevPageY;t.emitter.trigger("pointermove",{origEvent:e,isTouch:t.isTouchDragging,subjectEl:t.subjectEl,pageX:n,pageY:r,deltaX:n-t.origPageX,deltaY:r-t.origPageY})}},this.containerEl=e,this.emitter=new xr,e.addEventListener("mousedown",this.handleMouseDown),e.addEventListener("touchstart",this.handleTouchStart,{passive:!0}),1===(ya+=1)&&window.addEventListener("touchmove",Da,{passive:!1})}return e.prototype.destroy=function(){this.containerEl.removeEventListener("mousedown",this.handleMouseDown),this.containerEl.removeEventListener("touchstart",this.handleTouchStart,{passive:!0}),(ya-=1)||window.removeEventListener("touchmove",Da,{passive:!1})},e.prototype.tryStart=function(e){var t=this.querySubjectEl(e),n=e.target;return!(!t||this.handleSelector&&!z(n,this.handleSelector))&&(this.subjectEl=t,this.isDragging=!0,this.wasTouchScroll=!1,!0)},e.prototype.cleanup=function(){Ea=!1,this.isDragging=!1,this.subjectEl=null,this.destroyScrollWatch()},e.prototype.querySubjectEl=function(e){return this.selector?z(e.target,this.selector):this.containerEl},e.prototype.shouldIgnoreMouse=function(){return ma||this.isTouchDragging},e.prototype.cancelTouchScroll=function(){this.isDragging&&(Ea=!0)},e.prototype.initScrollWatch=function(e){this.shouldWatchScroll&&(this.recordCoords(e),window.addEventListener("scroll",this.handleScroll,!0))},e.prototype.recordCoords=function(e){this.shouldWatchScroll&&(this.prevPageX=e.pageX,this.prevPageY=e.pageY,this.prevScrollX=window.pageXOffset,this.prevScrollY=window.pageYOffset)},e.prototype.destroyScrollWatch=function(){this.shouldWatchScroll&&window.removeEventListener("scroll",this.handleScroll,!0)},e.prototype.createEventFromMouse=function(e,t){var n=0,r=0;return t?(this.origPageX=e.pageX,this.origPageY=e.pageY):(n=e.pageX-this.origPageX,r=e.pageY-this.origPageY),{origEvent:e,isTouch:!1,subjectEl:this.subjectEl,pageX:e.pageX,pageY:e.pageY,deltaX:n,deltaY:r}},e.prototype.createEventFromTouch=function(e,t){var n,r,o=e.touches,i=0,a=0;return o&&o.length?(n=o[0].pageX,r=o[0].pageY):(n=e.pageX,r=e.pageY),t?(this.origPageX=n,this.origPageY=r):(i=n-this.origPageX,a=r-this.origPageY),{origEvent:e,isTouch:!0,subjectEl:this.subjectEl,pageX:n,pageY:r,deltaX:i,deltaY:a}},e}();function Da(e){Ea&&e.preventDefault()}var ba=function(){function e(){this.isVisible=!1,this.sourceEl=null,this.mirrorEl=null,this.sourceElRect=null,this.parentNode=document.body,this.zIndex=9999,this.revertDuration=0}return e.prototype.start=function(e,t,n){this.sourceEl=e,this.sourceElRect=this.sourceEl.getBoundingClientRect(),this.origScreenX=t-window.pageXOffset,this.origScreenY=n-window.pageYOffset,this.deltaX=0,this.deltaY=0,this.updateElPosition()},e.prototype.handleMove=function(e,t){this.deltaX=e-window.pageXOffset-this.origScreenX,this.deltaY=t-window.pageYOffset-this.origScreenY,this.updateElPosition()},e.prototype.setIsVisible=function(e){e?this.isVisible||(this.mirrorEl&&(this.mirrorEl.style.display=""),this.isVisible=e,this.updateElPosition()):this.isVisible&&(this.mirrorEl&&(this.mirrorEl.style.display="none"),this.isVisible=e)},e.prototype.stop=function(e,t){var n=this,r=function(){n.cleanup(),t()};e&&this.mirrorEl&&this.isVisible&&this.revertDuration&&(this.deltaX||this.deltaY)?this.doRevertAnimation(r,this.revertDuration):setTimeout(r,0)},e.prototype.doRevertAnimation=function(e,t){var n=this.mirrorEl,r=this.sourceEl.getBoundingClientRect();n.style.transition="top "+t+"ms,left "+t+"ms",q(n,{left:r.left,top:r.top}),$(n,(function(){n.style.transition="",e()}))},e.prototype.cleanup=function(){this.mirrorEl&&(F(this.mirrorEl),this.mirrorEl=null),this.sourceEl=null},e.prototype.updateElPosition=function(){this.sourceEl&&this.isVisible&&q(this.getMirrorEl(),{left:this.sourceElRect.left+this.deltaX,top:this.sourceElRect.top+this.deltaY})},e.prototype.getMirrorEl=function(){var e=this.sourceElRect,t=this.mirrorEl;return t||((t=this.mirrorEl=this.sourceEl.cloneNode(!0)).classList.add("fc-unselectable"),t.classList.add("fc-event-dragging"),q(t,{position:"fixed",zIndex:this.zIndex,visibility:"",boxSizing:"border-box",width:e.right-e.left,height:e.bottom-e.top,right:"auto",bottom:"auto",margin:0}),this.parentNode.appendChild(t)),t},e}(),Ca=function(e){function t(t,n){var r=e.call(this)||this;return r.handleScroll=function(){r.scrollTop=r.scrollController.getScrollTop(),r.scrollLeft=r.scrollController.getScrollLeft(),r.handleScrollChange()},r.scrollController=t,r.doesListening=n,r.scrollTop=r.origScrollTop=t.getScrollTop(),r.scrollLeft=r.origScrollLeft=t.getScrollLeft(),r.scrollWidth=t.getScrollWidth(),r.scrollHeight=t.getScrollHeight(),r.clientWidth=t.getClientWidth(),r.clientHeight=t.getClientHeight(),r.clientRect=r.computeClientRect(),r.doesListening&&r.getEventTarget().addEventListener("scroll",r.handleScroll),r}return n(t,e),t.prototype.destroy=function(){this.doesListening&&this.getEventTarget().removeEventListener("scroll",this.handleScroll)},t.prototype.getScrollTop=function(){return this.scrollTop},t.prototype.getScrollLeft=function(){return this.scrollLeft},t.prototype.setScrollTop=function(e){this.scrollController.setScrollTop(e),this.doesListening||(this.scrollTop=Math.max(Math.min(e,this.getMaxScrollTop()),0),this.handleScrollChange())},t.prototype.setScrollLeft=function(e){this.scrollController.setScrollLeft(e),this.doesListening||(this.scrollLeft=Math.max(Math.min(e,this.getMaxScrollLeft()),0),this.handleScrollChange())},t.prototype.getClientWidth=function(){return this.clientWidth},t.prototype.getClientHeight=function(){return this.clientHeight},t.prototype.getScrollWidth=function(){return this.scrollWidth},t.prototype.getScrollHeight=function(){return this.scrollHeight},t.prototype.handleScrollChange=function(){},t}(Ir),wa=function(e){function t(t,n){return e.call(this,new Pr(t),n)||this}return n(t,e),t.prototype.getEventTarget=function(){return this.scrollController.el},t.prototype.computeClientRect=function(){return Rr(this.scrollController.el)},t}(Ca),Ra=function(e){function t(t){return e.call(this,new Nr,t)||this}return n(t,e),t.prototype.getEventTarget=function(){return window},t.prototype.computeClientRect=function(){return{left:this.scrollLeft,right:this.scrollLeft+this.clientWidth,top:this.scrollTop,bottom:this.scrollTop+this.clientHeight}},t.prototype.handleScrollChange=function(){this.clientRect=this.computeClientRect()},t}(Ca),Ta="function"==typeof performance?performance.now:Date.now,ka=function(){function e(){var e=this;this.isEnabled=!0,this.scrollQuery=[window,".fc-scroller"],this.edgeThreshold=50,this.maxVelocity=300,this.pointerScreenX=null,this.pointerScreenY=null,this.isAnimating=!1,this.scrollCaches=null,this.everMovedUp=!1,this.everMovedDown=!1,this.everMovedLeft=!1,this.everMovedRight=!1,this.animate=function(){if(e.isAnimating){var t=e.computeBestEdge(e.pointerScreenX+window.pageXOffset,e.pointerScreenY+window.pageYOffset);if(t){var n=Ta();e.handleSide(t,(n-e.msSinceRequest)/1e3),e.requestAnimation(n)}else e.isAnimating=!1}}}return e.prototype.start=function(e,t){this.isEnabled&&(this.scrollCaches=this.buildCaches(),this.pointerScreenX=null,this.pointerScreenY=null,this.everMovedUp=!1,this.everMovedDown=!1,this.everMovedLeft=!1,this.everMovedRight=!1,this.handleMove(e,t))},e.prototype.handleMove=function(e,t){if(this.isEnabled){var n=e-window.pageXOffset,r=t-window.pageYOffset,o=null===this.pointerScreenY?0:r-this.pointerScreenY,i=null===this.pointerScreenX?0:n-this.pointerScreenX;o<0?this.everMovedUp=!0:o>0&&(this.everMovedDown=!0),i<0?this.everMovedLeft=!0:i>0&&(this.everMovedRight=!0),this.pointerScreenX=n,this.pointerScreenY=r,this.isAnimating||(this.isAnimating=!0,this.requestAnimation(Ta()))}},e.prototype.stop=function(){if(this.isEnabled){this.isAnimating=!1;for(var e=0,t=this.scrollCaches;e<t.length;e++){t[e].destroy()}this.scrollCaches=null}},e.prototype.requestAnimation=function(e){this.msSinceRequest=e,requestAnimationFrame(this.animate)},e.prototype.handleSide=function(e,t){var n=e.scrollCache,r=this.edgeThreshold,o=r-e.distance,i=o*o/(r*r)*this.maxVelocity*t,a=1;switch(e.name){case"left":a=-1;case"right":n.setScrollLeft(n.getScrollLeft()+i*a);break;case"top":a=-1;case"bottom":n.setScrollTop(n.getScrollTop()+i*a)}},e.prototype.computeBestEdge=function(e,t){for(var n=this.edgeThreshold,r=null,o=0,i=this.scrollCaches;o<i.length;o++){var a=i[o],s=a.clientRect,l=e-s.left,u=s.right-e,c=t-s.top,d=s.bottom-t;l>=0&&u>=0&&c>=0&&d>=0&&(c<=n&&this.everMovedUp&&a.canScrollUp()&&(!r||r.distance>c)&&(r={scrollCache:a,name:"top",distance:c}),d<=n&&this.everMovedDown&&a.canScrollDown()&&(!r||r.distance>d)&&(r={scrollCache:a,name:"bottom",distance:d}),l<=n&&this.everMovedLeft&&a.canScrollLeft()&&(!r||r.distance>l)&&(r={scrollCache:a,name:"left",distance:l}),u<=n&&this.everMovedRight&&a.canScrollRight()&&(!r||r.distance>u)&&(r={scrollCache:a,name:"right",distance:u}))}return r},e.prototype.buildCaches=function(){return this.queryScrollEls().map((function(e){return e===window?new Ra(!1):new wa(e,!1)}))},e.prototype.queryScrollEls=function(){for(var e=[],t=0,n=this.scrollQuery;t<n.length;t++){var r=n[t];"object"==typeof r?e.push(r):e.push.apply(e,Array.prototype.slice.call(document.querySelectorAll(r)))}return e},e}(),Ma=function(e){function t(t,n){var r=e.call(this,t)||this;r.delay=null,r.minDistance=0,r.touchScrollAllowed=!0,r.mirrorNeedsRevert=!1,r.isInteracting=!1,r.isDragging=!1,r.isDelayEnded=!1,r.isDistanceSurpassed=!1,r.delayTimeoutId=null,r.onPointerDown=function(e){r.isDragging||(r.isInteracting=!0,r.isDelayEnded=!1,r.isDistanceSurpassed=!1,re(document.body),ie(document.body),e.isTouch||e.origEvent.preventDefault(),r.emitter.trigger("pointerdown",e),r.isInteracting&&!r.pointer.shouldIgnoreMove&&(r.mirror.setIsVisible(!1),r.mirror.start(e.subjectEl,e.pageX,e.pageY),r.startDelay(e),r.minDistance||r.handleDistanceSurpassed(e)))},r.onPointerMove=function(e){if(r.isInteracting){if(r.emitter.trigger("pointermove",e),!r.isDistanceSurpassed){var t=r.minDistance,n=e.deltaX,o=e.deltaY;n*n+o*o>=t*t&&r.handleDistanceSurpassed(e)}r.isDragging&&("scroll"!==e.origEvent.type&&(r.mirror.handleMove(e.pageX,e.pageY),r.autoScroller.handleMove(e.pageX,e.pageY)),r.emitter.trigger("dragmove",e))}},r.onPointerUp=function(e){r.isInteracting&&(r.isInteracting=!1,oe(document.body),ae(document.body),r.emitter.trigger("pointerup",e),r.isDragging&&(r.autoScroller.stop(),r.tryStopDrag(e)),r.delayTimeoutId&&(clearTimeout(r.delayTimeoutId),r.delayTimeoutId=null))};var o=r.pointer=new Sa(t);return o.emitter.on("pointerdown",r.onPointerDown),o.emitter.on("pointermove",r.onPointerMove),o.emitter.on("pointerup",r.onPointerUp),n&&(o.selector=n),r.mirror=new ba,r.autoScroller=new ka,r}return n(t,e),t.prototype.destroy=function(){this.pointer.destroy(),this.onPointerUp({})},t.prototype.startDelay=function(e){var t=this;"number"==typeof this.delay?this.delayTimeoutId=setTimeout((function(){t.delayTimeoutId=null,t.handleDelayEnd(e)}),this.delay):this.handleDelayEnd(e)},t.prototype.handleDelayEnd=function(e){this.isDelayEnded=!0,this.tryStartDrag(e)},t.prototype.handleDistanceSurpassed=function(e){this.isDistanceSurpassed=!0,this.tryStartDrag(e)},t.prototype.tryStartDrag=function(e){this.isDelayEnded&&this.isDistanceSurpassed&&(this.pointer.wasTouchScroll&&!this.touchScrollAllowed||(this.isDragging=!0,this.mirrorNeedsRevert=!1,this.autoScroller.start(e.pageX,e.pageY),this.emitter.trigger("dragstart",e),!1===this.touchScrollAllowed&&this.pointer.cancelTouchScroll()))},t.prototype.tryStopDrag=function(e){this.mirror.stop(this.mirrorNeedsRevert,this.stopDrag.bind(this,e))},t.prototype.stopDrag=function(e){this.isDragging=!1,this.emitter.trigger("dragend",e)},t.prototype.setIgnoreMove=function(e){this.pointer.shouldIgnoreMove=e},t.prototype.setMirrorIsVisible=function(e){this.mirror.setIsVisible(e)},t.prototype.setMirrorNeedsRevert=function(e){this.mirrorNeedsRevert=e},t.prototype.setAutoScrollEnabled=function(e){this.autoScroller.isEnabled=e},t}(hi),xa=function(){function e(e){this.origRect=Tr(e),this.scrollCaches=kr(e).map((function(e){return new wa(e,!0)}))}return e.prototype.destroy=function(){for(var e=0,t=this.scrollCaches;e<t.length;e++){t[e].destroy()}},e.prototype.computeLeft=function(){for(var e=this.origRect.left,t=0,n=this.scrollCaches;t<n.length;t++){var r=n[t];e+=r.origScrollLeft-r.getScrollLeft()}return e},e.prototype.computeTop=function(){for(var e=this.origRect.top,t=0,n=this.scrollCaches;t<n.length;t++){var r=n[t];e+=r.origScrollTop-r.getScrollTop()}return e},e.prototype.isWithinClipping=function(e,t){for(var n,r,o={left:e,top:t},i=0,a=this.scrollCaches;i<a.length;i++){var s=a[i];if(n=s.getEventTarget(),r=void 0,"HTML"!==(r=n.tagName)&&"BODY"!==r&&!sr(o,s.clientRect))return!1}return!0},e}();var _a=function(){function e(e,t){var n=this;this.useSubjectCenter=!1,this.requireInitial=!0,this.initialHit=null,this.movingHit=null,this.finalHit=null,this.handlePointerDown=function(e){var t=n.dragging;n.initialHit=null,n.movingHit=null,n.finalHit=null,n.prepareHits(),n.processFirstCoord(e),n.initialHit||!n.requireInitial?(t.setIgnoreMove(!1),n.emitter.trigger("pointerdown",e)):t.setIgnoreMove(!0)},this.handleDragStart=function(e){n.emitter.trigger("dragstart",e),n.handleMove(e,!0)},this.handleDragMove=function(e){n.emitter.trigger("dragmove",e),n.handleMove(e)},this.handlePointerUp=function(e){n.releaseHits(),n.emitter.trigger("pointerup",e)},this.handleDragEnd=function(e){n.movingHit&&n.emitter.trigger("hitupdate",null,!0,e),n.finalHit=n.movingHit,n.movingHit=null,n.emitter.trigger("dragend",e)},this.droppableStore=t,e.emitter.on("pointerdown",this.handlePointerDown),e.emitter.on("dragstart",this.handleDragStart),e.emitter.on("dragmove",this.handleDragMove),e.emitter.on("pointerup",this.handlePointerUp),e.emitter.on("dragend",this.handleDragEnd),this.dragging=e,this.emitter=new xr}return e.prototype.processFirstCoord=function(e){var t,n={left:e.pageX,top:e.pageY},r=n,o=e.subjectEl;o!==document&&(r=ur(r,t=Tr(o)));var i=this.initialHit=this.queryHitForOffset(r.left,r.top);if(i){if(this.useSubjectCenter&&t){var a=lr(t,i.rect);a&&(r=cr(a))}this.coordAdjust=dr(r,n)}else this.coordAdjust={left:0,top:0}},e.prototype.handleMove=function(e,t){var n=this.queryHitForOffset(e.pageX+this.coordAdjust.left,e.pageY+this.coordAdjust.top);!t&&Ia(this.movingHit,n)||(this.movingHit=n,this.emitter.trigger("hitupdate",n,!1,e))},e.prototype.prepareHits=function(){this.offsetTrackers=Ue(this.droppableStore,(function(e){return e.component.prepareHits(),new xa(e.el)}))},e.prototype.releaseHits=function(){var e=this.offsetTrackers;for(var t in e)e[t].destroy();this.offsetTrackers={}},e.prototype.queryHitForOffset=function(e,t){var n=this.droppableStore,r=this.offsetTrackers,o=null;for(var i in n){var a=n[i].component,s=r[i];if(s&&s.isWithinClipping(e,t)){var l=s.computeLeft(),u=s.computeTop(),c=e-l,d=t-u,p=s.origRect,f=p.right-p.left,h=p.bottom-p.top;if(c>=0&&c<f&&d>=0&&d<h){var v=a.queryHit(c,d,f,h),g=a.context.getCurrentData().dateProfile;v&&ln(g.activeRange,v.dateSpan.range)&&(!o||v.layer>o.layer)&&(v.rect.left+=l,v.rect.right+=l,v.rect.top+=u,v.rect.bottom+=u,o=v)}}}return o},e}();function Ia(e,t){return!e&&!t||Boolean(e)===Boolean(t)&&kn(e.dateSpan,t.dateSpan)}function Pa(e,t){for(var n,o,i={},a=0,s=t.pluginHooks.datePointTransforms;a<s.length;a++){var l=s[a];r(i,l(e,t))}return r(i,(n=e,{date:(o=t.dateEnv).toDate(n.range.start),dateStr:o.formatIso(n.range.start,{omitTime:n.allDay}),allDay:n.allDay})),i}var Na=function(e){function t(t){var n=e.call(this,t)||this;n.handlePointerDown=function(e){var t=n.dragging,r=e.origEvent.target;t.setIgnoreMove(!n.component.isValidDateDownEl(r))},n.handleDragEnd=function(e){var t=n.component;if(!n.dragging.pointer.wasTouchScroll){var o=n.hitDragging,i=o.initialHit,a=o.finalHit;if(i&&a&&Ia(i,a)){var s=t.context,l=r(r({},Pa(i.dateSpan,s)),{dayEl:i.dayEl,jsEvent:e.origEvent,view:s.viewApi||s.calendarApi.view});s.emitter.trigger("dateClick",l)}}},n.dragging=new Ma(t.el),n.dragging.autoScroller.isEnabled=!1;var o=n.hitDragging=new _a(n.dragging,pi(t));return o.emitter.on("pointerdown",n.handlePointerDown),o.emitter.on("dragend",n.handleDragEnd),n}return n(t,e),t.prototype.destroy=function(){this.dragging.destroy()},t}(ci),Ha=function(e){function t(t){var n=e.call(this,t)||this;n.dragSelection=null,n.handlePointerDown=function(e){var t=n,r=t.component,o=t.dragging,i=r.context.options.selectable&&r.isValidDateDownEl(e.origEvent.target);o.setIgnoreMove(!i),o.delay=e.isTouch?function(e){var t=e.context.options,n=t.selectLongPressDelay;null==n&&(n=t.longPressDelay);return n}(r):null},n.handleDragStart=function(e){n.component.context.calendarApi.unselect(e)},n.handleHitUpdate=function(e,t){var o=n.component.context,i=null,a=!1;e&&((i=function(e,t,n){var o=e.dateSpan,i=t.dateSpan,a=[o.range.start,o.range.end,i.range.start,i.range.end];a.sort(pe);for(var s={},l=0,u=n;l<u.length;l++){var c=(0,u[l])(e,t);if(!1===c)return null;c&&r(s,c)}return s.range={start:a[0],end:a[3]},s.allDay=o.allDay,s}(n.hitDragging.initialHit,e,o.pluginHooks.dateSelectionTransformers))&&n.component.isDateSelectionValid(i)||(a=!0,i=null)),i?o.dispatch({type:"SELECT_DATES",selection:i}):t||o.dispatch({type:"UNSELECT_DATES"}),a?te():ne(),t||(n.dragSelection=i)},n.handlePointerUp=function(e){n.dragSelection&&(In(n.dragSelection,e,n.component.context),n.dragSelection=null)};var o=t.component.context.options,i=n.dragging=new Ma(t.el);i.touchScrollAllowed=!1,i.minDistance=o.selectMinDistance||0,i.autoScroller.isEnabled=o.dragScroll;var a=n.hitDragging=new _a(n.dragging,pi(t));return a.emitter.on("pointerdown",n.handlePointerDown),a.emitter.on("dragstart",n.handleDragStart),a.emitter.on("hitupdate",n.handleHitUpdate),a.emitter.on("pointerup",n.handlePointerUp),n}return n(t,e),t.prototype.destroy=function(){this.dragging.destroy()},t}(ci);var Oa=function(e){function t(n){var o=e.call(this,n)||this;o.subjectEl=null,o.subjectSeg=null,o.isDragging=!1,o.eventRange=null,o.relevantEvents=null,o.receivingContext=null,o.validMutation=null,o.mutatedRelevantEvents=null,o.handlePointerDown=function(e){var t=e.origEvent.target,n=o,r=n.component,i=n.dragging,a=i.mirror,s=r.context.options,l=r.context;o.subjectEl=e.subjectEl;var u=o.subjectSeg=fn(e.subjectEl),c=(o.eventRange=u.eventRange).instance.instanceId;o.relevantEvents=Ot(l.getCurrentData().eventStore,c),i.minDistance=e.isTouch?0:s.eventDragMinDistance,i.delay=e.isTouch&&c!==r.props.eventSelection?function(e){var t=e.context.options,n=t.eventLongPressDelay;null==n&&(n=t.longPressDelay);return n}(r):null,s.fixedMirrorParent?a.parentNode=s.fixedMirrorParent:a.parentNode=z(t,".fc"),a.revertDuration=s.dragRevertDuration;var d=r.isValidSegDownEl(t)&&!z(t,".fc-event-resizer");i.setIgnoreMove(!d),o.isDragging=d&&e.subjectEl.classList.contains("fc-event-draggable")},o.handleDragStart=function(e){var t=o.component.context,n=o.eventRange,r=n.instance.instanceId;e.isTouch?r!==o.component.props.eventSelection&&t.dispatch({type:"SELECT_EVENT",eventInstanceId:r}):t.dispatch({type:"UNSELECT_EVENT"}),o.isDragging&&(t.calendarApi.unselect(e),t.emitter.trigger("eventDragStart",{el:o.subjectEl,event:new Bn(t,n.def,n.instance),jsEvent:e.origEvent,view:t.viewApi}))},o.handleHitUpdate=function(e,t){if(o.isDragging){var n=o.relevantEvents,r=o.hitDragging.initialHit,i=o.component.context,a=null,s=null,l=null,u=!1,c={affectedEvents:n,mutatedEvents:{defs:{},instances:{}},isEvent:!0};if(e){var d=e.component,p=(a=d.context).options;i===a||p.editable&&p.droppable?(s=function(e,t,n){var r=e.dateSpan,o=t.dateSpan,i=r.range.start,a=o.range.start,s={};r.allDay!==o.allDay&&(s.allDay=o.allDay,s.hasEnd=t.component.context.options.allDayMaintainDuration,o.allDay&&(i=we(i)));var l=tn(i,a,e.component.context.dateEnv,e.component===t.component?e.component.largeUnit:null);l.milliseconds&&(s.allDay=!1);for(var u={datesDelta:l,standardProps:s},c=0,d=n;c<d.length;c++){(0,d[c])(u,e,t)}return u}(r,e,a.getCurrentData().pluginHooks.eventDragMutationMassagers))&&(l=Hn(n,a.getCurrentData().eventUiBases,s,a),c.mutatedEvents=l,d.isInteractionValid(c)||(u=!0,s=null,l=null,c.mutatedEvents={defs:{},instances:{}})):a=null}o.displayDrag(a,c),u?te():ne(),t||(i===a&&Ia(r,e)&&(s=null),o.dragging.setMirrorNeedsRevert(!s),o.dragging.setMirrorIsVisible(!e||!document.querySelector(".fc-event-mirror")),o.receivingContext=a,o.validMutation=s,o.mutatedRelevantEvents=l)}},o.handlePointerUp=function(){o.isDragging||o.cleanup()},o.handleDragEnd=function(e){if(o.isDragging){var t=o.component.context,n=t.viewApi,i=o,a=i.receivingContext,s=i.validMutation,l=o.eventRange.def,u=o.eventRange.instance,c=new Bn(t,l,u),d=o.relevantEvents,p=o.mutatedRelevantEvents,f=o.hitDragging.finalHit;if(o.clearDrag(),t.emitter.trigger("eventDragStop",{el:o.subjectEl,event:c,jsEvent:e.origEvent,view:n}),s){if(a===t){var h=new Bn(t,p.defs[l.defId],u?p.instances[u.instanceId]:null);t.dispatch({type:"MERGE_EVENTS",eventStore:p});for(var v={oldEvent:c,event:h,relatedEvents:Gn(p,t,u),revert:function(){t.dispatch({type:"MERGE_EVENTS",eventStore:d})}},g={},m=0,y=t.getCurrentData().pluginHooks.eventDropTransformers;m<y.length;m++){var E=y[m];r(g,E(s,t))}t.emitter.trigger("eventDrop",r(r(r({},v),g),{el:e.subjectEl,delta:s.datesDelta,jsEvent:e.origEvent,view:n})),t.emitter.trigger("eventChange",v)}else if(a){var S={event:c,relatedEvents:Gn(d,t,u),revert:function(){t.dispatch({type:"MERGE_EVENTS",eventStore:d})}};t.emitter.trigger("eventLeave",r(r({},S),{draggedEl:e.subjectEl,view:n})),t.dispatch({type:"REMOVE_EVENTS",eventStore:d}),t.emitter.trigger("eventRemove",S);var D=p.defs[l.defId],b=p.instances[u.instanceId],C=new Bn(a,D,b);a.dispatch({type:"MERGE_EVENTS",eventStore:p});var w={event:C,relatedEvents:Gn(p,a,b),revert:function(){a.dispatch({type:"REMOVE_EVENTS",eventStore:p})}};a.emitter.trigger("eventAdd",w),e.isTouch&&a.dispatch({type:"SELECT_EVENT",eventInstanceId:u.instanceId}),a.emitter.trigger("drop",r(r({},Pa(f.dateSpan,a)),{draggedEl:e.subjectEl,jsEvent:e.origEvent,view:f.component.context.viewApi})),a.emitter.trigger("eventReceive",r(r({},w),{draggedEl:e.subjectEl,view:f.component.context.viewApi}))}}else t.emitter.trigger("_noEventDrop")}o.cleanup()};var i=o.component.context.options,a=o.dragging=new Ma(n.el);a.pointer.selector=t.SELECTOR,a.touchScrollAllowed=!1,a.autoScroller.isEnabled=i.dragScroll;var s=o.hitDragging=new _a(o.dragging,fi);return s.useSubjectCenter=n.useEventCenter,s.emitter.on("pointerdown",o.handlePointerDown),s.emitter.on("dragstart",o.handleDragStart),s.emitter.on("hitupdate",o.handleHitUpdate),s.emitter.on("pointerup",o.handlePointerUp),s.emitter.on("dragend",o.handleDragEnd),o}return n(t,e),t.prototype.destroy=function(){this.dragging.destroy()},t.prototype.displayDrag=function(e,t){var n=this.component.context,r=this.receivingContext;r&&r!==e&&(r===n?r.dispatch({type:"SET_EVENT_DRAG",state:{affectedEvents:t.affectedEvents,mutatedEvents:{defs:{},instances:{}},isEvent:!0}}):r.dispatch({type:"UNSET_EVENT_DRAG"})),e&&e.dispatch({type:"SET_EVENT_DRAG",state:t})},t.prototype.clearDrag=function(){var e=this.component.context,t=this.receivingContext;t&&t.dispatch({type:"UNSET_EVENT_DRAG"}),e!==t&&e.dispatch({type:"UNSET_EVENT_DRAG"})},t.prototype.cleanup=function(){this.subjectSeg=null,this.isDragging=!1,this.eventRange=null,this.relevantEvents=null,this.receivingContext=null,this.validMutation=null,this.mutatedRelevantEvents=null},t.SELECTOR=".fc-event-draggable, .fc-event-resizable",t}(ci);var Aa=function(e){function t(t){var n=e.call(this,t)||this;n.draggingSegEl=null,n.draggingSeg=null,n.eventRange=null,n.relevantEvents=null,n.validMutation=null,n.mutatedRelevantEvents=null,n.handlePointerDown=function(e){var t=n.component,r=fn(n.querySegEl(e)),o=n.eventRange=r.eventRange;n.dragging.minDistance=t.context.options.eventDragMinDistance,n.dragging.setIgnoreMove(!n.component.isValidSegDownEl(e.origEvent.target)||e.isTouch&&n.component.props.eventSelection!==o.instance.instanceId)},n.handleDragStart=function(e){var t=n.component.context,r=n.eventRange;n.relevantEvents=Ot(t.getCurrentData().eventStore,n.eventRange.instance.instanceId);var o=n.querySegEl(e);n.draggingSegEl=o,n.draggingSeg=fn(o),t.calendarApi.unselect(),t.emitter.trigger("eventResizeStart",{el:o,event:new Bn(t,r.def,r.instance),jsEvent:e.origEvent,view:t.viewApi})},n.handleHitUpdate=function(e,t,o){var i=n.component.context,a=n.relevantEvents,s=n.hitDragging.initialHit,l=n.eventRange.instance,u=null,c=null,d=!1,p={affectedEvents:a,mutatedEvents:{defs:{},instances:{}},isEvent:!0};e&&(u=function(e,t,n,o,i){for(var a=e.component.context.dateEnv,s=e.dateSpan.range.start,l=t.dateSpan.range.start,u=tn(s,l,a,e.component.largeUnit),c={},d=0,p=i;d<p.length;d++){var f=(0,p[d])(e,t);if(!1===f)return null;f&&r(c,f)}if(n){if(a.add(o.start,u)<o.end)return c.startDelta=u,c}else if(a.add(o.end,u)>o.start)return c.endDelta=u,c;return null}(s,e,o.subjectEl.classList.contains("fc-event-resizer-start"),l.range,i.pluginHooks.eventResizeJoinTransforms)),u&&(c=Hn(a,i.getCurrentData().eventUiBases,u,i),p.mutatedEvents=c,n.component.isInteractionValid(p)||(d=!0,u=null,c=null,p.mutatedEvents=null)),c?i.dispatch({type:"SET_EVENT_RESIZE",state:p}):i.dispatch({type:"UNSET_EVENT_RESIZE"}),d?te():ne(),t||(u&&Ia(s,e)&&(u=null),n.validMutation=u,n.mutatedRelevantEvents=c)},n.handleDragEnd=function(e){var t=n.component.context,o=n.eventRange.def,i=n.eventRange.instance,a=new Bn(t,o,i),s=n.relevantEvents,l=n.mutatedRelevantEvents;if(t.emitter.trigger("eventResizeStop",{el:n.draggingSegEl,event:a,jsEvent:e.origEvent,view:t.viewApi}),n.validMutation){var u=new Bn(t,l.defs[o.defId],i?l.instances[i.instanceId]:null);t.dispatch({type:"MERGE_EVENTS",eventStore:l});var c={oldEvent:a,event:u,relatedEvents:Gn(l,t,i),revert:function(){t.dispatch({type:"MERGE_EVENTS",eventStore:s})}};t.emitter.trigger("eventResize",r(r({},c),{el:n.draggingSegEl,startDelta:n.validMutation.startDelta||Xe(0),endDelta:n.validMutation.endDelta||Xe(0),jsEvent:e.origEvent,view:t.viewApi})),t.emitter.trigger("eventChange",c)}else t.emitter.trigger("_noEventResize");n.draggingSeg=null,n.relevantEvents=null,n.validMutation=null};var o=t.component,i=n.dragging=new Ma(t.el);i.pointer.selector=".fc-event-resizer",i.touchScrollAllowed=!1,i.autoScroller.isEnabled=o.context.options.dragScroll;var a=n.hitDragging=new _a(n.dragging,pi(t));return a.emitter.on("pointerdown",n.handlePointerDown),a.emitter.on("dragstart",n.handleDragStart),a.emitter.on("hitupdate",n.handleHitUpdate),a.emitter.on("dragend",n.handleDragEnd),n}return n(t,e),t.prototype.destroy=function(){this.dragging.destroy()},t.prototype.querySegEl=function(e){return z(e.subjectEl,".fc-event")},t}(ci);var Ua=function(){function e(e){var t=this;this.context=e,this.isRecentPointerDateSelect=!1,this.matchesCancel=!1,this.matchesEvent=!1,this.onSelect=function(e){e.jsEvent&&(t.isRecentPointerDateSelect=!0)},this.onDocumentPointerDown=function(e){var n=t.context.options.unselectCancel,r=e.origEvent.target;t.matchesCancel=!!z(r,n),t.matchesEvent=!!z(r,Oa.SELECTOR)},this.onDocumentPointerUp=function(e){var n=t.context,r=t.documentPointer,o=n.getCurrentData();if(!r.wasTouchScroll){if(o.dateSelection&&!t.isRecentPointerDateSelect){var i=n.options.unselectAuto;!i||i&&t.matchesCancel||n.calendarApi.unselect(e)}o.eventSelection&&!t.matchesEvent&&n.dispatch({type:"UNSELECT_EVENT"})}t.isRecentPointerDateSelect=!1};var n=this.documentPointer=new Sa(document);n.shouldIgnoreMove=!0,n.shouldWatchScroll=!1,n.emitter.on("pointerdown",this.onDocumentPointerDown),n.emitter.on("pointerup",this.onDocumentPointerUp),e.emitter.on("select",this.onSelect)}return e.prototype.destroy=function(){this.context.emitter.off("select",this.onSelect),this.documentPointer.destroy()},e}(),La={fixedMirrorParent:Pt},Wa={dateClick:Pt,eventDragStart:Pt,eventDragStop:Pt,eventDrop:Pt,eventResizeStart:Pt,eventResizeStop:Pt,eventResize:Pt,drop:Pt,eventReceive:Pt,eventLeave:Pt},Va=function(){function e(e,t){var n=this;this.receivingContext=null,this.droppableEvent=null,this.suppliedDragMeta=null,this.dragMeta=null,this.handleDragStart=function(e){n.dragMeta=n.buildDragMeta(e.subjectEl)},this.handleHitUpdate=function(e,t,o){var i=n.hitDragging.dragging,a=null,s=null,l=!1,u={affectedEvents:{defs:{},instances:{}},mutatedEvents:{defs:{},instances:{}},isEvent:n.dragMeta.create};e&&(a=e.component.context,n.canDropElOnCalendar(o.subjectEl,a)&&(s=function(e,t,n){for(var o=r({},t.leftoverProps),i=0,a=n.pluginHooks.externalDefTransforms;i<a.length;i++){var s=a[i];r(o,s(e,t))}var l=Xt(o,n),u=l.refined,c=l.extra,d=Jt(u,c,t.sourceId,e.allDay,n.options.forceEventDuration||Boolean(t.duration),n),p=e.range.start;e.allDay&&t.startTime&&(p=n.dateEnv.add(p,t.startTime));var f=t.duration?n.dateEnv.add(p,t.duration):Nn(e.allDay,p,n),h=Ne(d.defId,{start:p,end:f});return{def:d,instance:h}}(e.dateSpan,n.dragMeta,a),u.mutatedEvents=Ht(s),(l=!eo(u,a))&&(u.mutatedEvents={defs:{},instances:{}},s=null))),n.displayDrag(a,u),i.setMirrorIsVisible(t||!s||!document.querySelector(".fc-event-mirror")),l?te():ne(),t||(i.setMirrorNeedsRevert(!s),n.receivingContext=a,n.droppableEvent=s)},this.handleDragEnd=function(e){var t=n,o=t.receivingContext,i=t.droppableEvent;if(n.clearDrag(),o&&i){var a=n.hitDragging.finalHit,s=a.component.context.viewApi,l=n.dragMeta;if(o.emitter.trigger("drop",r(r({},Pa(a.dateSpan,o)),{draggedEl:e.subjectEl,jsEvent:e.origEvent,view:s})),l.create){var u=Ht(i);o.dispatch({type:"MERGE_EVENTS",eventStore:u}),e.isTouch&&o.dispatch({type:"SELECT_EVENT",eventInstanceId:i.instance.instanceId}),o.emitter.trigger("eventReceive",{event:new Bn(o,i.def,i.instance),relatedEvents:[],revert:function(){o.dispatch({type:"REMOVE_EVENTS",eventStore:u})},draggedEl:e.subjectEl,view:s})}}n.receivingContext=null,n.droppableEvent=null};var o=this.hitDragging=new _a(e,fi);o.requireInitial=!1,o.emitter.on("dragstart",this.handleDragStart),o.emitter.on("hitupdate",this.handleHitUpdate),o.emitter.on("dragend",this.handleDragEnd),this.suppliedDragMeta=t}return e.prototype.buildDragMeta=function(e){return"object"==typeof this.suppliedDragMeta?mi(this.suppliedDragMeta):"function"==typeof this.suppliedDragMeta?mi(this.suppliedDragMeta(e)):mi((t=function(e,t){var n=vi.dataAttrPrefix,r=(n?n+"-":"")+t;return e.getAttribute("data-"+r)||""}(e,"event"))?JSON.parse(t):{create:!1});var t},e.prototype.displayDrag=function(e,t){var n=this.receivingContext;n&&n!==e&&n.dispatch({type:"UNSET_EVENT_DRAG"}),e&&e.dispatch({type:"SET_EVENT_DRAG",state:t})},e.prototype.clearDrag=function(){this.receivingContext&&this.receivingContext.dispatch({type:"UNSET_EVENT_DRAG"})},e.prototype.canDropElOnCalendar=function(e,t){var n=t.options.dropAccept;return"function"==typeof n?n.call(t.calendarApi,e):"string"!=typeof n||!n||Boolean(B(e,n))},e}();vi.dataAttrPrefix="";var Fa=function(){function e(e,t){var n=this;void 0===t&&(t={}),this.handlePointerDown=function(e){var t=n.dragging,r=n.settings,o=r.minDistance,i=r.longPressDelay;t.minDistance=null!=o?o:e.isTouch?0:wt.eventDragMinDistance,t.delay=e.isTouch?null!=i?i:wt.longPressDelay:0},this.handleDragStart=function(e){e.isTouch&&n.dragging.delay&&e.subjectEl.classList.contains("fc-event")&&n.dragging.mirror.getMirrorEl().classList.add("fc-event-selected")},this.settings=t;var r=this.dragging=new Ma(e);r.touchScrollAllowed=!1,null!=t.itemSelector&&(r.pointer.selector=t.itemSelector),null!=t.appendTo&&(r.mirror.parentNode=t.appendTo),r.emitter.on("pointerdown",this.handlePointerDown),r.emitter.on("dragstart",this.handleDragStart),new Va(r,t.eventData)}return e.prototype.destroy=function(){this.dragging.destroy()},e}(),za=function(e){function t(t){var n=e.call(this,t)||this;n.shouldIgnoreMove=!1,n.mirrorSelector="",n.currentMirrorEl=null,n.handlePointerDown=function(e){n.emitter.trigger("pointerdown",e),n.shouldIgnoreMove||n.emitter.trigger("dragstart",e)},n.handlePointerMove=function(e){n.shouldIgnoreMove||n.emitter.trigger("dragmove",e)},n.handlePointerUp=function(e){n.emitter.trigger("pointerup",e),n.shouldIgnoreMove||n.emitter.trigger("dragend",e)};var r=n.pointer=new Sa(t);return r.emitter.on("pointerdown",n.handlePointerDown),r.emitter.on("pointermove",n.handlePointerMove),r.emitter.on("pointerup",n.handlePointerUp),n}return n(t,e),t.prototype.destroy=function(){this.pointer.destroy()},t.prototype.setIgnoreMove=function(e){this.shouldIgnoreMove=e},t.prototype.setMirrorIsVisible=function(e){if(e)this.currentMirrorEl&&(this.currentMirrorEl.style.visibility="",this.currentMirrorEl=null);else{var t=this.mirrorSelector?document.querySelector(this.mirrorSelector):null;t&&(this.currentMirrorEl=t,t.style.visibility="hidden")}},t}(hi),Ba=function(){function e(e,t){var n=document;e===document||e instanceof Element?(n=e,t=t||{}):t=e||{};var r=this.dragging=new za(n);"string"==typeof t.itemSelector?r.pointer.selector=t.itemSelector:n===document&&(r.pointer.selector="[data-event]"),"string"==typeof t.mirrorSelector&&(r.mirrorSelector=t.mirrorSelector),new Va(r,t.eventData)}return e.prototype.destroy=function(){this.dragging.destroy()},e}(),ja=lo({componentInteractions:[Na,Ha,Oa,Aa],calendarInteractions:[Ua],elementDraggingImpl:Ma,optionRefiners:La,listenerRefiners:Wa}),Ga=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.headerElRef=Lr(),t}return n(t,e),t.prototype.renderSimpleLayout=function(e,t){var n=this.props,r=this.context,o=[],i=ea(r.options);return e&&o.push({type:"header",key:"header",isSticky:i,chunk:{elRef:this.headerElRef,tableClassName:"fc-col-header",rowContent:e}}),o.push({type:"body",key:"body",liquid:!0,chunk:{content:t}}),Ar(Do,{viewSpec:r.viewSpec},(function(e,t){return Ar("div",{ref:e,className:["fc-daygrid"].concat(t).join(" ")},Ar(na,{liquid:!n.isHeightAuto&&!n.forPrint,cols:[],sections:o}))}))},t.prototype.renderHScrollLayout=function(e,t,n,r){var o=this.context.pluginHooks.scrollGridImpl;if(!o)throw new Error("No ScrollGrid implementation");var i=this.props,a=this.context,s=!i.forPrint&&ea(a.options),l=!i.forPrint&&ta(a.options),u=[];return e&&u.push({type:"header",key:"header",isSticky:s,chunks:[{key:"main",elRef:this.headerElRef,tableClassName:"fc-col-header",rowContent:e}]}),u.push({type:"body",key:"body",liquid:!0,chunks:[{key:"main",content:t}]}),l&&u.push({type:"footer",key:"footer",isSticky:!0,chunks:[{key:"main",content:Qi}]}),Ar(Do,{viewSpec:a.viewSpec},(function(e,t){return Ar("div",{ref:e,className:["fc-daygrid"].concat(t).join(" ")},Ar(o,{liquid:!i.isHeightAuto&&!i.forPrint,colGroups:[{cols:[{span:n,minWidth:r}]}],sections:u}))}))},t}(so);function qa(e,t){for(var n=[],r=0;r<t;r+=1)n[r]=[];for(var o=0,i=e;o<i.length;o++){var a=i[o];n[a.row].push(a)}return n}function Ya(e,t){for(var n=[],r=0;r<t;r+=1)n[r]=[];for(var o=0,i=e;o<i.length;o++){var a=i[o];n[a.firstCol].push(a)}return n}function Za(e,t){var n=[];if(e){for(a=0;a<t;a+=1)n[a]={affectedInstances:e.affectedInstances,isEvent:e.isEvent,segs:[]};for(var r=0,o=e.segs;r<o.length;r++){var i=o[r];n[i.row].segs.push(i)}}else for(var a=0;a<t;a+=1)n[a]=null;return n}var Xa=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context.options.navLinks?{"data-navlink":yr(e.date),tabIndex:0}:{};return Ar(la,{date:e.date,dateProfile:e.dateProfile,todayRange:e.todayRange,showDayNumber:e.showDayNumber,extraHookProps:e.extraHookProps,defaultContent:Ka},(function(n,o){return(o||e.forceDayTop)&&Ar("div",{className:"fc-daygrid-day-top",ref:n},Ar("a",r({className:"fc-daygrid-day-number"},t),o||Ar(Wr,null," ")))}))},t}(Yr);function Ka(e){return e.dayNumberText}var Ja=bt({week:"narrow"}),$a=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.handleRootEl=function(e){t.rootEl=e,Kr(t.props.elRef,e)},t.handleMoreLinkClick=function(e){var n=t.props;if(n.onMoreClick){var r=n.segsByEachCol,o=r.filter((function(e){return n.segIsHidden[e.eventRange.instance.instanceId]}));n.onMoreClick({date:n.date,allSegs:r,hiddenSegs:o,moreCnt:n.moreCnt,dayEl:t.rootEl,ev:e})}},t}return n(t,e),t.prototype.render=function(){var e=this,t=this.context,n=t.options,o=t.viewApi,i=this.props,a=i.date,s=i.dateProfile,l={num:i.moreCnt,text:i.buildMoreLinkText(i.moreCnt),view:o},u=n.navLinks?{"data-navlink":yr(a,"week"),tabIndex:0}:{};return Ar(ca,{date:a,dateProfile:s,todayRange:i.todayRange,showDayNumber:i.showDayNumber,extraHookProps:i.extraHookProps,elRef:this.handleRootEl},(function(t,o,c,d){return Ar("td",r({ref:t,className:["fc-daygrid-day"].concat(o,i.extraClassNames||[]).join(" ")},c,i.extraDataAttrs),Ar("div",{className:"fc-daygrid-day-frame fc-scrollgrid-sync-inner",ref:i.innerElRef},i.showWeekNumber&&Ar(ha,{date:a,defaultFormat:Ja},(function(e,t,n,o){return Ar("a",r({ref:e,className:["fc-daygrid-week-number"].concat(t).join(" ")},u),o)})),!d&&Ar(Xa,{date:a,dateProfile:s,showDayNumber:i.showDayNumber,forceDayTop:i.forceDayTop,todayRange:i.todayRange,extraHookProps:i.extraHookProps}),Ar("div",{className:"fc-daygrid-day-events",ref:i.fgContentElRef,style:{paddingBottom:i.fgPaddingBottom}},i.fgContent,Boolean(i.moreCnt)&&Ar("div",{className:"fc-daygrid-day-bottom",style:{marginTop:i.moreMarginTop}},Ar(fo,{hookProps:l,classNames:n.moreLinkClassNames,content:n.moreLinkContent,defaultContent:Qa,didMount:n.moreLinkDidMount,willUnmount:n.moreLinkWillUnmount},(function(t,n,r,o){return Ar("a",{ref:t,className:["fc-daygrid-more-link"].concat(n).join(" "),onClick:e.handleMoreLinkClick},o)})))),Ar("div",{className:"fc-daygrid-day-bg"},i.bgContent)))}))},t}(so);function Qa(e){return e.text}$a.addPropsEquality({onMoreClick:!0});var es=bt({hour:"numeric",minute:"2-digit",omitZeroMinute:!0,meridiem:"narrow"});function ts(e){var t=e.eventRange.ui.display;return"list-item"===t||"auto"===t&&!e.eventRange.def.allDay&&e.firstCol===e.lastCol&&e.isStart&&e.isEnd}var ns=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.options.eventTimeFormat||es,o=Dn(e.seg,n,t,!0,e.defaultDisplayEventEnd);return Ar(ra,{seg:e.seg,timeText:o,defaultContent:rs,isDragging:e.isDragging,isResizing:!1,isDateSelecting:!1,isSelected:e.isSelected,isPast:e.isPast,isFuture:e.isFuture,isToday:e.isToday},(function(t,n,o,i){return Ar("a",r({className:["fc-daygrid-event","fc-daygrid-dot-event"].concat(n).join(" "),ref:t},(a=e.seg,(s=a.eventRange.def.url)?{href:s}:{})),i);var a,s}))},t}(Yr);function rs(e){return Ar(Wr,null,Ar("div",{className:"fc-daygrid-event-dot",style:{borderColor:e.borderColor||e.backgroundColor}}),e.timeText&&Ar("div",{className:"fc-event-time"},e.timeText),Ar("div",{className:"fc-event-title"},e.event.title||Ar(Wr,null," ")))}var os=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props;return Ar(oa,r({},e,{extraClassNames:["fc-daygrid-event","fc-daygrid-block-event","fc-h-event"],defaultTimeFormat:es,defaultDisplayEventEnd:e.defaultDisplayEventEnd,disableResizing:!e.seg.eventRange.def.allDay}))},t}(Yr);function is(e,t,n,o,i,a,s,l){for(var u=[],c=[],d={},p={},f={},h={},v={},g=0;g<s;g+=1)u.push([]),c.push(0);for(var m=0,y=t=gn(t,l);m<y.length;m++){T(w=y[m],i[w.eventRange.instance.instanceId+":"+w.firstCol]||0)}!0===n||!0===o?function(e,t,n,r){ss(e,t,n,!0,(function(e){return e.bottom<=r}))}(c,d,u,a):"number"==typeof n?function(e,t,n,r){ss(e,t,n,!1,(function(e,t){return t<r}))}(c,d,u,n):"number"==typeof o&&function(e,t,n,r){ss(e,t,n,!0,(function(e,t){return t<r}))}(c,d,u,o);for(var E=0;E<s;E+=1){for(var S=0,D=0,b=0,C=u[E];b<C.length;b++){var w,R=C[b];d[(w=R.seg).eventRange.instance.instanceId]||(p[w.eventRange.instance.instanceId]=R.top,w.firstCol===w.lastCol&&w.isStart&&w.isEnd?(f[w.eventRange.instance.instanceId]=R.top-S,D=0,S=R.bottom):D=R.bottom-S)}D&&(c[E]?h[E]=D:v[E]=D)}function T(e,t){if(!k(e,t,0))for(var n=e.firstCol;n<=e.lastCol;n+=1)for(var r=0,o=u[n];r<o.length;r++){if(k(e,t,o[r].bottom))return}}function k(e,t,n){if(function(e,t,n){for(var r=e.firstCol;r<=e.lastCol;r+=1)for(var o=0,i=u[r];o<i.length;o++){var a=i[o];if(n<a.bottom&&n+t>a.top)return!1}return!0}(e,t,n)){for(var r=e.firstCol;r<=e.lastCol;r+=1){for(var o=u[r],i=0;i<o.length&&n>=o[i].top;)i+=1;o.splice(i,0,{seg:e,top:n,bottom:n+t})}return!0}return!1}for(var M in i)i[M]||(d[M.split(":")[0]]=!0);return{segsByFirstCol:u.map(as),segsByEachCol:u.map((function(t,n){var o=function(e){for(var t=[],n=0,r=e;n<r.length;n++){var o=r[n];t.push(o.seg)}return t}(t);return o=function(e,t,n){for(var o=t,i=me(o,1),a={start:o,end:i},s=[],l=0,u=e;l<u.length;l++){var c=u[l],d=c.eventRange,p=d.range,f=on(p,a);f&&s.push(r(r({},c),{firstCol:n,lastCol:n,eventRange:{def:d.def,ui:r(r({},d.ui),{durationEditable:!1}),instance:d.instance,range:f},isStart:c.isStart&&f.start.valueOf()===p.start.valueOf(),isEnd:c.isEnd&&f.end.valueOf()===p.end.valueOf()}))}return s}(o,e[n].date,n)})),segIsHidden:d,segTops:p,segMarginTops:f,moreCnts:c,moreTops:h,paddingBottoms:v}}function as(e,t){for(var n=[],r=0,o=e;r<o.length;r++){var i=o[r];i.seg.firstCol===t&&n.push(i.seg)}return n}function ss(e,t,n,r,o){for(var i=e.length,a={},s=[],l=0;l<i;l+=1)s.push([]);for(l=0;l<i;l+=1)for(var u=0,c=0,d=n[l];c<d.length;c++){var p=d[c];o(p,u)?f(p):h(p,u,r),p.top!==p.bottom&&(u+=1)}function f(e){var t=e.seg,n=t.eventRange.instance.instanceId;if(!a[n]){a[n]=!0;for(var r=t.firstCol;r<=t.lastCol;r+=1){for(var o=s[r],i=0;i<o.length&&e.top>=o[i].top;)i+=1;o.splice(i,0,e)}}}function h(n,r,o){var i=n.seg,a=i.eventRange.instance.instanceId;if(!t[a]){t[a]=!0;for(var l=i.firstCol;l<=i.lastCol;l+=1){e[l]+=1;var u=e[l];if(o&&1===u&&r>0)for(var c=r-1;s[l].length>c;)h(s[l].pop(),s[l].length,!1)}}}}var ls=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.cellElRefs=new zi,t.frameElRefs=new zi,t.fgElRefs=new zi,t.segHarnessRefs=new zi,t.rootElRef=Lr(),t.state={framePositions:null,maxContentHeight:null,segHeights:{}},t}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=this.state,o=this.context,i=t.cells.length,a=Ya(t.businessHourSegs,i),s=Ya(t.bgEventSegs,i),l=Ya(this.getHighlightSegs(),i),u=Ya(this.getMirrorSegs(),i),c=is(t.cells,t.fgEventSegs,t.dayMaxEvents,t.dayMaxEventRows,n.segHeights,n.maxContentHeight,i,o.options.eventOrder),d=c.paddingBottoms,p=c.segsByFirstCol,f=c.segsByEachCol,h=c.segIsHidden,v=c.segTops,g=c.segMarginTops,m=c.moreCnts,y=c.moreTops,E=t.eventDrag&&t.eventDrag.affectedInstances||t.eventResize&&t.eventResize.affectedInstances||{};return Ar("tr",{ref:this.rootElRef},t.renderIntro&&t.renderIntro(),t.cells.map((function(n,o){var i=e.renderFgSegs(p[o],h,v,g,E,t.todayRange),c=e.renderFgSegs(u[o],{},v,{},{},t.todayRange,Boolean(t.eventDrag),Boolean(t.eventResize),!1);return Ar($a,{key:n.key,elRef:e.cellElRefs.createRef(n.key),innerElRef:e.frameElRefs.createRef(n.key),dateProfile:t.dateProfile,date:n.date,showDayNumber:t.showDayNumbers,showWeekNumber:t.showWeekNumbers&&0===o,forceDayTop:t.showWeekNumbers,todayRange:t.todayRange,extraHookProps:n.extraHookProps,extraDataAttrs:n.extraDataAttrs,extraClassNames:n.extraClassNames,moreCnt:m[o],buildMoreLinkText:t.buildMoreLinkText,onMoreClick:function(e){t.onMoreClick(r(r({},e),{fromCol:o}))},segIsHidden:h,moreMarginTop:y[o],segsByEachCol:f[o],fgPaddingBottom:d[o],fgContentElRef:e.fgElRefs.createRef(n.key),fgContent:Ar(Wr,null,Ar(Wr,null,i),Ar(Wr,null,c)),bgContent:Ar(Wr,null,e.renderFillSegs(l[o],"highlight"),e.renderFillSegs(a[o],"non-business"),e.renderFillSegs(s[o],"bg-event"))})})))},t.prototype.componentDidMount=function(){this.updateSizing(!0)},t.prototype.componentDidUpdate=function(e,t){var n=this.props;this.updateSizing(!Ve(e,n))},t.prototype.getHighlightSegs=function(){var e=this.props;return e.eventDrag&&e.eventDrag.segs.length?e.eventDrag.segs:e.eventResize&&e.eventResize.segs.length?e.eventResize.segs:e.dateSelectionSegs},t.prototype.getMirrorSegs=function(){var e=this.props;return e.eventResize&&e.eventResize.segs.length?e.eventResize.segs:[]},t.prototype.renderFgSegs=function(e,t,n,o,i,a,s,l,u){var c=this.context,d=this.props.eventSelection,p=this.state.framePositions,f=1===this.props.cells.length,h=[];if(p)for(var v=0,g=e;v<g.length;v++){var m=g[v],y=m.eventRange.instance.instanceId,E=s||l||u,S=i[y],D=t[y]||S,b=t[y]||E||m.firstCol!==m.lastCol||!m.isStart||!m.isEnd,C=void 0,w=void 0,R=void 0,T=void 0;b?(w=n[y],c.isRtl?(T=0,R=p.lefts[m.lastCol]-p.lefts[m.firstCol]):(R=0,T=p.rights[m.firstCol]-p.rights[m.lastCol])):C=o[y],h.push(Ar("div",{className:"fc-daygrid-event-harness"+(b?" fc-daygrid-event-harness-abs":""),key:y,ref:E?null:this.segHarnessRefs.createRef(y+":"+m.firstCol),style:{visibility:D?"hidden":"",marginTop:C||"",top:w||"",left:R||"",right:T||""}},ts(m)?Ar(ns,r({seg:m,isDragging:s,isSelected:y===d,defaultDisplayEventEnd:f},bn(m,a))):Ar(os,r({seg:m,isDragging:s,isResizing:l,isDateSelecting:u,isSelected:y===d,defaultDisplayEventEnd:f},bn(m,a)))))}return h},t.prototype.renderFillSegs=function(e,t){var n=this.context.isRtl,i=this.props.todayRange,a=this.state.framePositions,s=[];if(a)for(var l=0,u=e;l<u.length;l++){var c=u[l],d=n?{right:0,left:a.lefts[c.lastCol]-a.lefts[c.firstCol]}:{left:0,right:a.rights[c.firstCol]-a.rights[c.lastCol]};s.push(Ar("div",{key:wn(c.eventRange),className:"fc-daygrid-bg-harness",style:d},"bg-event"===t?Ar(pa,r({seg:c},bn(c,i))):da(t)))}return Ar.apply(void 0,o([Wr,{}],s))},t.prototype.updateSizing=function(e){var t=this.props,n=this.frameElRefs;if(null!==t.clientWidth){if(e){var r=t.cells.map((function(e){return n.currentMap[e.key]}));if(r.length){var o=this.rootElRef.current;this.setState({framePositions:new _r(o,r,!0,!1)})}}var i=!0===t.dayMaxEvents||!0===t.dayMaxEventRows;this.setState({segHeights:this.computeSegHeights(),maxContentHeight:i?this.computeMaxContentHeight():null})}},t.prototype.computeSegHeights=function(){return Ue(this.segHarnessRefs.currentMap,(function(e){return e.getBoundingClientRect().height}))},t.prototype.computeMaxContentHeight=function(){var e=this.props.cells[0].key,t=this.cellElRefs.currentMap[e],n=this.fgElRefs.currentMap[e];return t.getBoundingClientRect().bottom-n.getBoundingClientRect().top},t.prototype.getCellEls=function(){var e=this.cellElRefs.currentMap;return this.props.cells.map((function(t){return e[t.key]}))},t}(so);ls.addPropsEquality({onMoreClick:!0}),ls.addStateEquality({segHeights:Ve});var us=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.repositioner=new Zo(t.updateSize.bind(t)),t.handleRootEl=function(e){t.rootEl=e,t.props.elRef&&Kr(t.props.elRef,e)},t.handleDocumentMousedown=function(e){var n=t.props.onClose;n&&!t.rootEl.contains(e.target)&&n()},t.handleDocumentScroll=function(){t.repositioner.request(10)},t.handleCloseClick=function(){var e=t.props.onClose;e&&e()},t}return n(t,e),t.prototype.render=function(){var e=this.context.theme,t=this.props,n=["fc-popover",e.getClass("popover")].concat(t.extraClassNames||[]);return Ar("div",r({className:n.join(" ")},t.extraAttrs,{ref:this.handleRootEl}),Ar("div",{className:"fc-popover-header "+e.getClass("popoverHeader")},Ar("span",{className:"fc-popover-title"},t.title),Ar("span",{className:"fc-popover-close "+e.getIconClass("close"),onClick:this.handleCloseClick})),Ar("div",{className:"fc-popover-body "+e.getClass("popoverContent")},t.children))},t.prototype.componentDidMount=function(){document.addEventListener("mousedown",this.handleDocumentMousedown),document.addEventListener("scroll",this.handleDocumentScroll),this.updateSize()},t.prototype.componentWillUnmount=function(){document.removeEventListener("mousedown",this.handleDocumentMousedown),document.removeEventListener("scroll",this.handleDocumentScroll)},t.prototype.updateSize=function(){var e=this.props,t=e.alignmentEl,n=e.topAlignmentEl,r=this.rootEl;if(r){var o,i=r.getBoundingClientRect(),a=t.getBoundingClientRect(),s=n?n.getBoundingClientRect().top:a.top;s=Math.min(s,window.innerHeight-i.height-10),s=Math.max(s,10),o=this.context.isRtl?a.right-i.width:a.left,o=Math.min(o,window.innerWidth-i.width-10),q(r,{top:s,left:o=Math.max(o,10)})}},t}(Yr),cs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.rootElRef=Lr(),t}return n(t,e),t.prototype.render=function(){var e=this.context,t=e.options,n=e.dateEnv,o=this.props,i=o.date,a=o.hiddenInstances,s=o.todayRange,l=o.dateProfile,u=o.selectedInstanceId,c=n.format(i,t.dayPopoverFormat);return Ar(ca,{date:i,dateProfile:l,todayRange:s,elRef:this.rootElRef},(function(e,t,n){return Ar(us,{elRef:e,title:c,extraClassNames:["fc-more-popover"].concat(t),extraAttrs:n,onClose:o.onCloseClick,alignmentEl:o.alignmentEl,topAlignmentEl:o.topAlignmentEl},Ar(la,{date:i,dateProfile:l,todayRange:s},(function(e,t){return t&&Ar("div",{className:"fc-more-popover-misc",ref:e},t)})),o.segs.map((function(e){var t=e.eventRange.instance.instanceId;return Ar("div",{className:"fc-daygrid-event-harness",key:t,style:{visibility:a[t]?"hidden":""}},ts(e)?Ar(ns,r({seg:e,isDragging:!1,isSelected:t===u,defaultDisplayEventEnd:!1},bn(e,s))):Ar(os,r({seg:e,isDragging:!1,isResizing:!1,isDateSelecting:!1,isSelected:t===u,defaultDisplayEventEnd:!1},bn(e,s))))})))}))},t.prototype.positionToHit=function(e,t,n){var r=this.rootElRef.current;if(!n||!r)return null;var o=n.getBoundingClientRect(),i=r.getBoundingClientRect(),a=i.left-o.left,s=i.top-o.top,l=e-a,u=t-s,c=this.props.date;return l>=0&&l<i.width&&u>=0&&u<i.height?{dateSpan:{allDay:!0,range:{start:c,end:me(c,1)}},dayEl:r,relativeRect:{left:a,top:s,right:i.width,bottom:i.height},layer:1}:null},t}(so),ds=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.splitBusinessHourSegs=st(qa),t.splitBgEventSegs=st(qa),t.splitFgEventSegs=st(qa),t.splitDateSelectionSegs=st(qa),t.splitEventDrag=st(Za),t.splitEventResize=st(Za),t.buildBuildMoreLinkText=st(ps),t.morePopoverRef=Lr(),t.rowRefs=new zi,t.state={morePopoverState:null},t.handleRootEl=function(e){t.rootEl=e,Kr(t.props.elRef,e)},t.handleMoreLinkClick=function(e){var n=t.context,o=n.dateEnv,i=n.options.moreLinkClick;function a(e){var t=e.eventRange,r=t.def,i=t.instance,a=t.range;return{event:new Bn(n,r,i),start:o.toDate(a.start),end:o.toDate(a.end),isStart:e.isStart,isEnd:e.isEnd}}"function"==typeof i&&(i=i({date:o.toDate(e.date),allDay:!0,allSegs:e.allSegs.map(a),hiddenSegs:e.hiddenSegs.map(a),jsEvent:e.ev,view:n.viewApi})),i&&"popover"!==i?"string"==typeof i&&n.calendarApi.zoomTo(e.date,i):t.setState({morePopoverState:r(r({},e),{currentFgEventSegs:t.props.fgEventSegs,fromRow:e.fromRow,fromCol:e.fromCol})})},t.handleMorePopoverClose=function(){t.setState({morePopoverState:null})},t}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=t.dateProfile,o=t.dayMaxEventRows,i=t.dayMaxEvents,a=t.expandRows,s=this.state.morePopoverState,l=t.cells.length,u=this.splitBusinessHourSegs(t.businessHourSegs,l),c=this.splitBgEventSegs(t.bgEventSegs,l),d=this.splitFgEventSegs(t.fgEventSegs,l),p=this.splitDateSelectionSegs(t.dateSelectionSegs,l),f=this.splitEventDrag(t.eventDrag,l),h=this.splitEventResize(t.eventResize,l),v=this.buildBuildMoreLinkText(this.context.options.moreLinkText),g=!0===i||!0===o;return g&&!a&&(g=!1,o=null,i=null),Ar("div",{className:["fc-daygrid-body",g?"fc-daygrid-body-balanced":"fc-daygrid-body-unbalanced",a?"":"fc-daygrid-body-natural"].join(" "),ref:this.handleRootEl,style:{width:t.clientWidth,minWidth:t.tableMinWidth}},Ar(Pi,{unit:"day"},(function(g,m){return Ar(Wr,null,Ar("table",{className:"fc-scrollgrid-sync-table",style:{width:t.clientWidth,minWidth:t.tableMinWidth,height:a?t.clientHeight:""}},t.colGroupNode,Ar("tbody",null,t.cells.map((function(a,s){return Ar(ls,{ref:e.rowRefs.createRef(s),key:a.length?a[0].date.toISOString():s,showDayNumbers:l>1,showWeekNumbers:t.showWeekNumbers,todayRange:m,dateProfile:n,cells:a,renderIntro:t.renderRowIntro,businessHourSegs:u[s],eventSelection:t.eventSelection,bgEventSegs:c[s].filter(fs),fgEventSegs:d[s],dateSelectionSegs:p[s],eventDrag:f[s],eventResize:h[s],dayMaxEvents:i,dayMaxEventRows:o,clientWidth:t.clientWidth,clientHeight:t.clientHeight,buildMoreLinkText:v,onMoreClick:function(t){e.handleMoreLinkClick(r(r({},t),{fromRow:s}))}})})))),!t.forPrint&&s&&s.currentFgEventSegs===t.fgEventSegs&&Ar(cs,{ref:e.morePopoverRef,date:s.date,dateProfile:n,segs:s.allSegs,alignmentEl:s.dayEl,topAlignmentEl:1===l?t.headerAlignElRef.current:null,onCloseClick:e.handleMorePopoverClose,selectedInstanceId:t.eventSelection,hiddenInstances:(t.eventDrag?t.eventDrag.affectedInstances:null)||(t.eventResize?t.eventResize.affectedInstances:null)||{},todayRange:m}))})))},t.prototype.prepareHits=function(){this.rowPositions=new _r(this.rootEl,this.rowRefs.collect().map((function(e){return e.getCellEls()[0]})),!1,!0),this.colPositions=new _r(this.rootEl,this.rowRefs.currentMap[0].getCellEls(),!0,!1)},t.prototype.positionToHit=function(e,t){var n=this.morePopoverRef.current,o=n?n.positionToHit(e,t,this.rootEl):null,i=this.state.morePopoverState;if(o)return r({row:i.fromRow,col:i.fromCol},o);var a=this.colPositions,s=this.rowPositions,l=a.leftToIndex(e),u=s.topToIndex(t);return null!=u&&null!=l?{row:u,col:l,dateSpan:{range:this.getCellRange(u,l),allDay:!0},dayEl:this.getCellEl(u,l),relativeRect:{left:a.lefts[l],right:a.rights[l],top:s.tops[u],bottom:s.bottoms[u]}}:null},t.prototype.getCellEl=function(e,t){return this.rowRefs.currentMap[e].getCellEls()[t]},t.prototype.getCellRange=function(e,t){var n=this.props.cells[e][t].date;return{start:n,end:me(n,1)}},t}(so);function ps(e){return"function"==typeof e?e:function(t){return"+"+t+" "+e}}function fs(e){return e.eventRange.def.allDay}var hs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.forceDayIfListItem=!0,t}return n(t,e),t.prototype.sliceRange=function(e,t){return t.sliceRange(e)},t}(Li),vs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.slicer=new hs,t.tableRef=Lr(),t.handleRootEl=function(e){e?t.context.registerInteractiveComponent(t,{el:e}):t.context.unregisterInteractiveComponent(t)},t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context;return Ar(ds,r({ref:this.tableRef,elRef:this.handleRootEl},this.slicer.sliceProps(e,e.dateProfile,e.nextDayThreshold,t,e.dayTableModel),{dateProfile:e.dateProfile,cells:e.dayTableModel.cells,colGroupNode:e.colGroupNode,tableMinWidth:e.tableMinWidth,renderRowIntro:e.renderRowIntro,dayMaxEvents:e.dayMaxEvents,dayMaxEventRows:e.dayMaxEventRows,showWeekNumbers:e.showWeekNumbers,expandRows:e.expandRows,headerAlignElRef:e.headerAlignElRef,clientWidth:e.clientWidth,clientHeight:e.clientHeight,forPrint:e.forPrint}))},t.prototype.prepareHits=function(){this.tableRef.current.prepareHits()},t.prototype.queryHit=function(e,t){var n=this.tableRef.current.positionToHit(e,t);return n?{component:this,dateSpan:n.dateSpan,dayEl:n.dayEl,rect:{left:n.relativeRect.left,right:n.relativeRect.right,top:n.relativeRect.top,bottom:n.relativeRect.bottom},layer:0}:null},t}(so),gs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.buildDayTableModel=st(ms),t.headerRef=Lr(),t.tableRef=Lr(),t}return n(t,e),t.prototype.render=function(){var e=this,t=this.context,n=t.options,r=t.dateProfileGenerator,o=this.props,i=this.buildDayTableModel(o.dateProfile,r),a=n.dayHeaders&&Ar(Hi,{ref:this.headerRef,dateProfile:o.dateProfile,dates:i.headerDates,datesRepDistinctDays:1===i.rowCnt}),s=function(t){return Ar(vs,{ref:e.tableRef,dateProfile:o.dateProfile,dayTableModel:i,businessHours:o.businessHours,dateSelection:o.dateSelection,eventStore:o.eventStore,eventUiBases:o.eventUiBases,eventSelection:o.eventSelection,eventDrag:o.eventDrag,eventResize:o.eventResize,nextDayThreshold:n.nextDayThreshold,colGroupNode:t.tableColGroupNode,tableMinWidth:t.tableMinWidth,dayMaxEvents:n.dayMaxEvents,dayMaxEventRows:n.dayMaxEventRows,showWeekNumbers:n.weekNumbers,expandRows:!o.isHeightAuto,headerAlignElRef:e.headerElRef,clientWidth:t.clientWidth,clientHeight:t.clientHeight,forPrint:o.forPrint})};return n.dayMinWidth?this.renderHScrollLayout(a,s,i.colCnt,n.dayMinWidth):this.renderSimpleLayout(a,s)},t}(Ga);function ms(e,t){var n=new Ai(e.renderRange,t);return new Ui(n,/year|month|week/.test(e.currentRangeUnit))}var ys=lo({initialView:"dayGridMonth",optionRefiners:{moreLinkClick:Pt,moreLinkClassNames:Pt,moreLinkContent:Pt,moreLinkDidMount:Pt,moreLinkWillUnmount:Pt},views:{dayGrid:{component:gs,dateProfileGeneratorClass:function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.buildRenderRange=function(t,n,r){var o,i=this.props.dateEnv,a=e.prototype.buildRenderRange.call(this,t,n,r),s=a.start,l=a.end;(/^(year|month)$/.test(n)&&(s=i.startOfWeek(s),(o=i.startOfWeek(l)).valueOf()!==l.valueOf()&&(l=ge(o,1))),this.props.monthMode&&this.props.fixedWeekCount)&&(l=ge(l,6-Math.ceil(Ee(s,l))));return{start:s,end:l}},t}(To)},dayGridDay:{type:"dayGrid",duration:{days:1}},dayGridWeek:{type:"dayGrid",duration:{weeks:1}},dayGridMonth:{type:"dayGrid",duration:{months:1},monthMode:!0,fixedWeekCount:!0}}}),Es=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.getKeyInfo=function(){return{allDay:{},timed:{}}},t.prototype.getKeysForDateSpan=function(e){return e.allDay?["allDay"]:["timed"]},t.prototype.getKeysForEventDef=function(e){return e.allDay?dn(e)?["timed","allDay"]:["allDay"]:["timed"]},t}(hr),Ss=bt({hour:"numeric",minute:"2-digit",omitZeroMinute:!0,meridiem:"short"});function Ds(e){var t=["fc-timegrid-slot","fc-timegrid-slot-label",e.isLabeled?"fc-scrollgrid-shrink":"fc-timegrid-slot-minor"];return Ar(jr.Consumer,null,(function(n){if(!e.isLabeled)return Ar("td",{className:t.join(" "),"data-time":e.isoTimeStr});var r=n.dateEnv,o=n.options,i=n.viewApi,a=null==o.slotLabelFormat?Ss:Array.isArray(o.slotLabelFormat)?bt(o.slotLabelFormat[0]):bt(o.slotLabelFormat),s={level:0,time:e.time,date:r.toDate(e.date),view:i,text:r.format(e.date,a)};return Ar(fo,{hookProps:s,classNames:o.slotLabelClassNames,content:o.slotLabelContent,defaultContent:bs,didMount:o.slotLabelDidMount,willUnmount:o.slotLabelWillUnmount},(function(n,r,o,i){return Ar("td",{ref:n,className:t.concat(r).join(" "),"data-time":e.isoTimeStr},Ar("div",{className:"fc-timegrid-slot-label-frame fc-scrollgrid-shrink-frame"},Ar("div",{className:"fc-timegrid-slot-label-cushion fc-scrollgrid-shrink-cushion",ref:o},i)))}))}))}function bs(e){return e.text}var Cs=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){return this.props.slatMetas.map((function(e){return Ar("tr",{key:e.key},Ar(Ds,r({},e)))}))},t}(Yr),ws=bt({week:"short"}),Rs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.allDaySplitter=new Es,t.headerElRef=Lr(),t.rootElRef=Lr(),t.scrollerElRef=Lr(),t.state={slatCoords:null},t.handleScrollTopRequest=function(e){var n=t.scrollerElRef.current;n&&(n.scrollTop=e)},t.renderHeadAxis=function(e,n){void 0===n&&(n="");var o=t.context.options,i=t.props.dateProfile.renderRange,a=Se(i.start,i.end),s=o.navLinks&&1===a?{"data-navlink":yr(i.start,"week"),tabIndex:0}:{};return o.weekNumbers&&"day"===e?Ar(ha,{date:i.start,defaultFormat:ws},(function(e,t,o,i){return Ar("th",{ref:e,className:["fc-timegrid-axis","fc-scrollgrid-shrink"].concat(t).join(" ")},Ar("div",{className:"fc-timegrid-axis-frame fc-scrollgrid-shrink-frame fc-timegrid-axis-frame-liquid",style:{height:n}},Ar("a",r({ref:o,className:"fc-timegrid-axis-cushion fc-scrollgrid-shrink-cushion fc-scrollgrid-sync-inner"},s),i)))})):Ar("th",{className:"fc-timegrid-axis"},Ar("div",{className:"fc-timegrid-axis-frame",style:{height:n}}))},t.renderTableRowAxis=function(e){var n=t.context,r=n.options,o=n.viewApi,i={text:r.allDayText,view:o};return Ar(fo,{hookProps:i,classNames:r.allDayClassNames,content:r.allDayContent,defaultContent:Ts,didMount:r.allDayDidMount,willUnmount:r.allDayWillUnmount},(function(t,n,r,o){return Ar("td",{ref:t,className:["fc-timegrid-axis","fc-scrollgrid-shrink"].concat(n).join(" ")},Ar("div",{className:"fc-timegrid-axis-frame fc-scrollgrid-shrink-frame"+(null==e?" fc-timegrid-axis-frame-liquid":""),style:{height:e}},Ar("span",{className:"fc-timegrid-axis-cushion fc-scrollgrid-shrink-cushion fc-scrollgrid-sync-inner",ref:r},o)))}))},t.handleSlatCoords=function(e){t.setState({slatCoords:e})},t}return n(t,e),t.prototype.renderSimpleLayout=function(e,t,n){var r=this.context,o=this.props,i=[],a=ea(r.options);return e&&i.push({type:"header",key:"header",isSticky:a,chunk:{elRef:this.headerElRef,tableClassName:"fc-col-header",rowContent:e}}),t&&(i.push({type:"body",key:"all-day",chunk:{content:t}}),i.push({type:"body",key:"all-day-divider",outerContent:Ar("tr",{className:"fc-scrollgrid-section"},Ar("td",{className:"fc-timegrid-divider "+r.theme.getClass("tableCellShaded")}))})),i.push({type:"body",key:"body",liquid:!0,expandRows:Boolean(r.options.expandRows),chunk:{scrollerElRef:this.scrollerElRef,content:n}}),Ar(Do,{viewSpec:r.viewSpec,elRef:this.rootElRef},(function(e,t){return Ar("div",{className:["fc-timegrid"].concat(t).join(" "),ref:e},Ar(na,{liquid:!o.isHeightAuto&&!o.forPrint,cols:[{width:"shrink"}],sections:i}))}))},t.prototype.renderHScrollLayout=function(e,t,n,r,o,i,a){var s=this,l=this.context.pluginHooks.scrollGridImpl;if(!l)throw new Error("No ScrollGrid implementation");var u=this.context,c=this.props,d=!c.forPrint&&ea(u.options),p=!c.forPrint&&ta(u.options),f=[];e&&f.push({type:"header",key:"header",isSticky:d,syncRowHeights:!0,chunks:[{key:"axis",rowContent:function(e){return Ar("tr",null,s.renderHeadAxis("day",e.rowSyncHeights[0]))}},{key:"cols",elRef:this.headerElRef,tableClassName:"fc-col-header",rowContent:e}]}),t&&(f.push({type:"body",key:"all-day",syncRowHeights:!0,chunks:[{key:"axis",rowContent:function(e){return Ar("tr",null,s.renderTableRowAxis(e.rowSyncHeights[0]))}},{key:"cols",content:t}]}),f.push({key:"all-day-divider",type:"body",outerContent:Ar("tr",{className:"fc-scrollgrid-section"},Ar("td",{colSpan:2,className:"fc-timegrid-divider "+u.theme.getClass("tableCellShaded")}))}));var h=u.options.nowIndicator;return f.push({type:"body",key:"body",liquid:!0,expandRows:Boolean(u.options.expandRows),chunks:[{key:"axis",content:function(e){return Ar("div",{className:"fc-timegrid-axis-chunk"},Ar("table",{style:{height:e.expandRows?e.clientHeight:""}},e.tableColGroupNode,Ar("tbody",null,Ar(Cs,{slatMetas:i}))),Ar("div",{className:"fc-timegrid-now-indicator-container"},Ar(Pi,{unit:h?"minute":"day"},(function(e){var t=h&&a&&a.safeComputeTop(e);return"number"==typeof t?Ar(aa,{isAxis:!0,date:e},(function(e,n,r,o){return Ar("div",{ref:e,className:["fc-timegrid-now-indicator-arrow"].concat(n).join(" "),style:{top:t}},o)})):null}))))}},{key:"cols",scrollerElRef:this.scrollerElRef,content:n}]}),p&&f.push({key:"footer",type:"footer",isSticky:!0,chunks:[{key:"axis",content:Qi},{key:"cols",content:Qi}]}),Ar(Do,{viewSpec:u.viewSpec,elRef:this.rootElRef},(function(e,t){return Ar("div",{className:["fc-timegrid"].concat(t).join(" "),ref:e},Ar(l,{liquid:!c.isHeightAuto&&!c.forPrint,colGroups:[{width:"shrink",cols:[{width:"shrink"}]},{cols:[{span:r,minWidth:o}]}],sections:f}))}))},t.prototype.getAllDayMaxEventProps=function(){var e=this.context.options,t=e.dayMaxEvents,n=e.dayMaxEventRows;return!0!==t&&!0!==n||(t=void 0,n=5),{dayMaxEvents:t,dayMaxEventRows:n}},t}(so);function Ts(e){return e.text}var ks=function(){function e(e,t,n){this.positions=e,this.dateProfile=t,this.slotDuration=n}return e.prototype.safeComputeTop=function(e){var t=this.dateProfile;if(un(t.currentRange,e)){var n=we(e),r=e.valueOf()-n.valueOf();if(r>=et(t.slotMinTime)&&r<et(t.slotMaxTime))return this.computeTimeTop(Xe(r))}return null},e.prototype.computeDateTop=function(e,t){return t||(t=we(e)),this.computeTimeTop(Xe(e.valueOf()-t.valueOf()))},e.prototype.computeTimeTop=function(e){var t,n,r=this.positions,o=this.dateProfile,i=r.els.length,a=(e.milliseconds-et(o.slotMinTime))/et(this.slotDuration);return a=Math.max(0,a),a=Math.min(i,a),t=Math.floor(a),n=a-(t=Math.min(t,i-1)),r.tops[t]+r.getHeight(t)*n},e}(),Ms=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=t.options,o=e.slatElRefs;return Ar("tbody",null,e.slatMetas.map((function(i,a){var s={time:i.time,date:t.dateEnv.toDate(i.date),view:t.viewApi},l=["fc-timegrid-slot","fc-timegrid-slot-lane",i.isLabeled?"":"fc-timegrid-slot-minor"];return Ar("tr",{key:i.key,ref:o.createRef(i.key)},e.axis&&Ar(Ds,r({},i)),Ar(fo,{hookProps:s,classNames:n.slotLaneClassNames,content:n.slotLaneContent,didMount:n.slotLaneDidMount,willUnmount:n.slotLaneWillUnmount},(function(e,t,n,r){return Ar("td",{ref:e,className:l.concat(t).join(" "),"data-time":i.isoTimeStr},r)})))})))},t}(Yr),xs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.rootElRef=Lr(),t.slatElRefs=new zi,t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context;return Ar("div",{className:"fc-timegrid-slots",ref:this.rootElRef},Ar("table",{className:t.theme.getClass("table"),style:{minWidth:e.tableMinWidth,width:e.clientWidth,height:e.minHeight}},e.tableColGroupNode,Ar(Ms,{slatElRefs:this.slatElRefs,axis:e.axis,slatMetas:e.slatMetas})))},t.prototype.componentDidMount=function(){this.updateSizing()},t.prototype.componentDidUpdate=function(){this.updateSizing()},t.prototype.componentWillUnmount=function(){this.props.onCoords&&this.props.onCoords(null)},t.prototype.updateSizing=function(){var e,t=this.context,n=this.props;n.onCoords&&null!==n.clientWidth&&(this.rootElRef.current.offsetHeight&&n.onCoords(new ks(new _r(this.rootElRef.current,(e=this.slatElRefs.currentMap,n.slatMetas.map((function(t){return e[t.key]}))),!1,!0),this.props.dateProfile,t.options.slotDuration)))},t}(Yr);function _s(e,t){var n,r=[];for(n=0;n<t;n+=1)r.push([]);if(e)for(n=0;n<e.length;n+=1)r[e[n].col].push(e[n]);return r}function Is(e,t){var n=[];if(e){for(a=0;a<t;a+=1)n[a]={affectedInstances:e.affectedInstances,isEvent:e.isEvent,segs:[]};for(var r=0,o=e.segs;r<o.length;r++){var i=o[r];n[i.col].segs.push(i)}}else for(var a=0;a<t;a+=1)n[a]=null;return n}function Ps(e,t,n,r,o){return Ns(e,t,n,r),function(e,t){for(var n=0,r=e;n<r.length;n++){(c=r[n]).level=null,c.forwardCoord=null,c.backwardCoord=null,c.forwardPressure=null}var o,i=function(e){var t,n,r,o=[];for(t=0;t<e.length;t+=1){for(n=e[t],r=0;r<o.length&&Hs(n,o[r]).length;r+=1);n.level=r,(o[r]||(o[r]=[])).push(n)}return o}(e=gn(e,t));if(function(e){var t,n,r,o,i;for(t=0;t<e.length;t+=1)for(n=e[t],r=0;r<n.length;r+=1)for((o=n[r]).forwardSegs=[],i=t+1;i<e.length;i+=1)Hs(o,e[i],o.forwardSegs)}(i),o=i[0]){for(var a=0,s=o;a<s.length;a++){Os(c=s[a])}for(var l=0,u=o;l<u.length;l++){var c;As(c=u[l],0,0,t)}}return e}(e,o)}function Ns(e,t,n,r){for(var o=0,i=e;o<i.length;o++){var a=i[o];a.top=n.computeDateTop(a.start,t),a.bottom=Math.max(a.top+(r||0),n.computeDateTop(a.end,t))}}function Hs(e,t,n){void 0===n&&(n=[]);for(var r=0;r<t.length;r+=1)o=e,i=t[r],o.bottom>i.top&&o.top<i.bottom&&n.push(t[r]);var o,i;return n}function Os(e){var t,n,r=e.forwardSegs,o=0;if(null==e.forwardPressure){for(t=0;t<r.length;t+=1)Os(n=r[t]),o=Math.max(o,1+n.forwardPressure);e.forwardPressure=o}}function As(e,t,n,r){var o,i=e.forwardSegs;if(null==e.forwardCoord)for(i.length?(!function(e,t){var n=e.map(Us),r=[{field:"forwardPressure",order:-1},{field:"backwardCoord",order:1}].concat(t);n.sort((function(e,t){return le(e,t,r)})),n.map((function(e){return e._seg}))}(i,r),As(i[0],t+1,n,r),e.forwardCoord=i[0].backwardCoord):e.forwardCoord=1,e.backwardCoord=e.forwardCoord-(e.forwardCoord-n)/(t+1),o=0;o<i.length;o+=1)As(i[o],0,e.forwardCoord,r)}function Us(e){var t=mn(e);return t.forwardPressure=e.forwardPressure,t.backwardCoord=e.backwardCoord,t}var Ls=bt({hour:"numeric",minute:"2-digit",meridiem:!1}),Ws=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=["fc-timegrid-event","fc-v-event"];return this.props.isCondensed&&e.push("fc-timegrid-event-condensed"),Ar(oa,r({},this.props,{defaultTimeFormat:Ls,extraClassNames:e}))},t}(Yr),Vs=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props;return Ar(la,{date:e.date,dateProfile:e.dateProfile,todayRange:e.todayRange,extraHookProps:e.extraHookProps},(function(e,t){return t&&Ar("div",{className:"fc-timegrid-col-misc",ref:e},t)}))},t}(Yr);vi.timeGridEventCondensedHeight=30;var Fs=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=this.context.options.selectMirror,o=t.eventDrag&&t.eventDrag.segs||t.eventResize&&t.eventResize.segs||n&&t.dateSelectionSegs||[],i=t.eventDrag&&t.eventDrag.affectedInstances||t.eventResize&&t.eventResize.affectedInstances||{};return Ar(ca,{elRef:t.elRef,date:t.date,dateProfile:t.dateProfile,todayRange:t.todayRange,extraHookProps:t.extraHookProps},(function(a,s,l){return Ar("td",r({ref:a,className:["fc-timegrid-col"].concat(s,t.extraClassNames||[]).join(" ")},l,t.extraDataAttrs),Ar("div",{className:"fc-timegrid-col-frame"},Ar("div",{className:"fc-timegrid-col-bg"},e.renderFillSegs(t.businessHourSegs,"non-business"),e.renderFillSegs(t.bgEventSegs,"bg-event"),e.renderFillSegs(t.dateSelectionSegs,"highlight")),Ar("div",{className:"fc-timegrid-col-events"},e.renderFgSegs(t.fgEventSegs,i)),Ar("div",{className:"fc-timegrid-col-events"},e.renderFgSegs(o,{},Boolean(t.eventDrag),Boolean(t.eventResize),Boolean(n))),Ar("div",{className:"fc-timegrid-now-indicator-container"},e.renderNowIndicator(t.nowIndicatorSegs)),Ar(Vs,{date:t.date,dateProfile:t.dateProfile,todayRange:t.todayRange,extraHookProps:t.extraHookProps})))}))},t.prototype.renderFgSegs=function(e,t,n,r,o){var i=this.props;return i.forPrint?this.renderPrintFgSegs(e):i.slatCoords?this.renderPositionedFgSegs(e,t,n,r,o):null},t.prototype.renderPrintFgSegs=function(e){var t=this.props;return(e=gn(e,this.context.options.eventOrder)).map((function(e){return Ar("div",{className:"fc-timegrid-event-harness",key:e.eventRange.instance.instanceId},Ar(Ws,r({seg:e,isDragging:!1,isResizing:!1,isDateSelecting:!1,isSelected:!1,isCondensed:!1},bn(e,t.todayRange,t.nowDate))))}))},t.prototype.renderPositionedFgSegs=function(e,t,n,o,i){var a=this,s=this.context,l=this.props;return(e=Ps(e,l.date,l.slatCoords,s.options.eventMinHeight,s.options.eventOrder)).map((function(e){var s=e.eventRange.instance.instanceId,u=n||o||i?r({left:0,right:0},a.computeSegTopBottomCss(e)):a.computeFgSegPositionCss(e);return Ar("div",{className:"fc-timegrid-event-harness"+(e.level>0?" fc-timegrid-event-harness-inset":""),key:s,style:r({visibility:t[s]?"hidden":""},u)},Ar(Ws,r({seg:e,isDragging:n,isResizing:o,isDateSelecting:i,isSelected:s===l.eventSelection,isCondensed:e.bottom-e.top<vi.timeGridEventCondensedHeight},bn(e,l.todayRange,l.nowDate))))}))},t.prototype.renderFillSegs=function(e,t){var n=this,o=this.context,i=this.props;if(!i.slatCoords)return null;Ns(e,i.date,i.slatCoords,o.options.eventMinHeight);var a=e.map((function(e){return Ar("div",{key:wn(e.eventRange),className:"fc-timegrid-bg-harness",style:n.computeSegTopBottomCss(e)},"bg-event"===t?Ar(pa,r({seg:e},bn(e,i.todayRange,i.nowDate))):da(t))}));return Ar(Wr,null,a)},t.prototype.renderNowIndicator=function(e){var t=this.props,n=t.slatCoords,r=t.date;return n?e.map((function(e,t){return Ar(aa,{isAxis:!1,date:r,key:t},(function(t,o,i,a){return Ar("div",{ref:t,className:["fc-timegrid-now-indicator-line"].concat(o).join(" "),style:{top:n.computeDateTop(e.start,r)}},a)}))})):null},t.prototype.computeFgSegPositionCss=function(e){var t,n,o=this.context,i=o.isRtl,a=o.options.slotEventOverlap,s=e.backwardCoord,l=e.forwardCoord;a&&(l=Math.min(1,s+2*(l-s))),i?(t=1-l,n=s):(t=s,n=1-l);var u={zIndex:e.level+1,left:100*t+"%",right:100*n+"%"};return a&&e.forwardPressure&&(u[i?"marginLeft":"marginRight"]=20),r(r({},u),this.computeSegTopBottomCss(e))},t.prototype.computeSegTopBottomCss=function(e){return{top:e.top,bottom:-e.bottom}},t}(Yr),zs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.splitFgEventSegs=st(_s),t.splitBgEventSegs=st(_s),t.splitBusinessHourSegs=st(_s),t.splitNowIndicatorSegs=st(_s),t.splitDateSelectionSegs=st(_s),t.splitEventDrag=st(Is),t.splitEventResize=st(Is),t.rootElRef=Lr(),t.cellElRefs=new zi,t}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=this.context.options.nowIndicator&&t.slatCoords&&t.slatCoords.safeComputeTop(t.nowDate),r=t.cells.length,o=this.splitFgEventSegs(t.fgEventSegs,r),i=this.splitBgEventSegs(t.bgEventSegs,r),a=this.splitBusinessHourSegs(t.businessHourSegs,r),s=this.splitNowIndicatorSegs(t.nowIndicatorSegs,r),l=this.splitDateSelectionSegs(t.dateSelectionSegs,r),u=this.splitEventDrag(t.eventDrag,r),c=this.splitEventResize(t.eventResize,r);return Ar("div",{className:"fc-timegrid-cols",ref:this.rootElRef},Ar("table",{style:{minWidth:t.tableMinWidth,width:t.clientWidth}},t.tableColGroupNode,Ar("tbody",null,Ar("tr",null,t.axis&&Ar("td",{className:"fc-timegrid-col fc-timegrid-axis"},Ar("div",{className:"fc-timegrid-col-frame"},Ar("div",{className:"fc-timegrid-now-indicator-container"},"number"==typeof n&&Ar(aa,{isAxis:!0,date:t.nowDate},(function(e,t,r,o){return Ar("div",{ref:e,className:["fc-timegrid-now-indicator-arrow"].concat(t).join(" "),style:{top:n}},o)}))))),t.cells.map((function(n,r){return Ar(Fs,{key:n.key,elRef:e.cellElRefs.createRef(n.key),dateProfile:t.dateProfile,date:n.date,nowDate:t.nowDate,todayRange:t.todayRange,extraHookProps:n.extraHookProps,extraDataAttrs:n.extraDataAttrs,extraClassNames:n.extraClassNames,fgEventSegs:o[r],bgEventSegs:i[r],businessHourSegs:a[r],nowIndicatorSegs:s[r],dateSelectionSegs:l[r],eventDrag:u[r],eventResize:c[r],slatCoords:t.slatCoords,eventSelection:t.eventSelection,forPrint:t.forPrint})}))))))},t.prototype.componentDidMount=function(){this.updateCoords()},t.prototype.componentDidUpdate=function(){this.updateCoords()},t.prototype.updateCoords=function(){var e,t=this.props;t.onColCoords&&null!==t.clientWidth&&t.onColCoords(new _r(this.rootElRef.current,(e=this.cellElRefs.currentMap,t.cells.map((function(t){return e[t.key]}))),!0,!1))},t}(Yr);var Bs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.processSlotOptions=st(js),t.state={slatCoords:null},t.handleScrollRequest=function(e){var n=t.props.onScrollTopRequest,r=t.state.slatCoords;if(n&&r){if(e.time){var o=r.computeTimeTop(e.time);(o=Math.ceil(o))&&(o+=1),n(o)}return!0}return!1},t.handleColCoords=function(e){t.colCoords=e},t.handleSlatCoords=function(e){t.setState({slatCoords:e}),t.props.onSlatCoords&&t.props.onSlatCoords(e)},t}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.state;return Ar("div",{className:"fc-timegrid-body",ref:e.rootElRef,style:{width:e.clientWidth,minWidth:e.tableMinWidth}},Ar(xs,{axis:e.axis,dateProfile:e.dateProfile,slatMetas:e.slatMetas,clientWidth:e.clientWidth,minHeight:e.expandRows?e.clientHeight:"",tableMinWidth:e.tableMinWidth,tableColGroupNode:e.axis?e.tableColGroupNode:null,onCoords:this.handleSlatCoords}),Ar(zs,{cells:e.cells,axis:e.axis,dateProfile:e.dateProfile,businessHourSegs:e.businessHourSegs,bgEventSegs:e.bgEventSegs,fgEventSegs:e.fgEventSegs,dateSelectionSegs:e.dateSelectionSegs,eventSelection:e.eventSelection,eventDrag:e.eventDrag,eventResize:e.eventResize,todayRange:e.todayRange,nowDate:e.nowDate,nowIndicatorSegs:e.nowIndicatorSegs,clientWidth:e.clientWidth,tableMinWidth:e.tableMinWidth,tableColGroupNode:e.tableColGroupNode,slatCoords:t.slatCoords,onColCoords:this.handleColCoords,forPrint:e.forPrint}))},t.prototype.componentDidMount=function(){this.scrollResponder=this.context.createScrollResponder(this.handleScrollRequest)},t.prototype.componentDidUpdate=function(e){this.scrollResponder.update(e.dateProfile!==this.props.dateProfile)},t.prototype.componentWillUnmount=function(){this.scrollResponder.detach()},t.prototype.positionToHit=function(e,t){var n=this.context,r=n.dateEnv,o=n.options,i=this.colCoords,a=this.props.dateProfile,s=this.state.slatCoords,l=this.processSlotOptions(this.props.slotDuration,o.snapDuration),u=l.snapDuration,c=l.snapsPerSlot,d=i.leftToIndex(e),p=s.positions.topToIndex(t);if(null!=d&&null!=p){var f=s.positions.tops[p],h=s.positions.getHeight(p),v=(t-f)/h,g=p*c+Math.floor(v*c),m=this.props.cells[d].date,y=Je(a.slotMinTime,$e(u,g)),E=r.add(m,y);return{col:d,dateSpan:{range:{start:E,end:r.add(E,u)},allDay:!1},dayEl:i.els[d],relativeRect:{left:i.lefts[d],right:i.rights[d],top:f,bottom:f+h}}}return null},t}(Yr);function js(e,t){var n=t||e,r=tt(e,n);return null===r&&(n=e,r=1),{snapDuration:n,snapsPerSlot:r}}var Gs=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.sliceRange=function(e,t){for(var n=[],r=0;r<t.length;r+=1){var o=on(e,t[r]);o&&n.push({start:o.start,end:o.end,isStart:o.start.valueOf()===e.start.valueOf(),isEnd:o.end.valueOf()===e.end.valueOf(),col:r})}return n},t}(Li),qs=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.buildDayRanges=st(Ys),t.slicer=new Gs,t.timeColsRef=Lr(),t.handleRootEl=function(e){e?t.context.registerInteractiveComponent(t,{el:e}):t.context.unregisterInteractiveComponent(t)},t}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=this.context,o=t.dateProfile,i=t.dayTableModel,a=n.options.nowIndicator,s=this.buildDayRanges(i,o,n.dateEnv);return Ar(Pi,{unit:a?"minute":"day"},(function(l,u){return Ar(Bs,r({ref:e.timeColsRef,rootElRef:e.handleRootEl},e.slicer.sliceProps(t,o,null,n,s),{forPrint:t.forPrint,axis:t.axis,dateProfile:o,slatMetas:t.slatMetas,slotDuration:t.slotDuration,cells:i.cells[0],tableColGroupNode:t.tableColGroupNode,tableMinWidth:t.tableMinWidth,clientWidth:t.clientWidth,clientHeight:t.clientHeight,expandRows:t.expandRows,nowDate:l,nowIndicatorSegs:a&&e.slicer.sliceNowDate(l,n,s),todayRange:u,onScrollTopRequest:t.onScrollTopRequest,onSlatCoords:t.onSlatCoords}))}))},t.prototype.queryHit=function(e,t){var n=this.timeColsRef.current.positionToHit(e,t);return n?{component:this,dateSpan:n.dateSpan,dayEl:n.dayEl,rect:{left:n.relativeRect.left,right:n.relativeRect.right,top:n.relativeRect.top,bottom:n.relativeRect.bottom},layer:0}:null},t}(so);function Ys(e,t,n){for(var r=[],o=0,i=e.headerDates;o<i.length;o++){var a=i[o];r.push({start:n.add(a,t.slotMinTime),end:n.add(a,t.slotMaxTime)})}return r}var Zs=[{hours:1},{minutes:30},{minutes:15},{seconds:30},{seconds:15}];function Xs(e,t,n,r,o){for(var i=new Date(0),a=e,s=Xe(0),l=n||function(e){var t,n,r;for(t=Zs.length-1;t>=0;t-=1)if(n=Xe(Zs[t]),null!==(r=tt(n,e))&&r>1)return n;return e}(r),u=[];et(a)<et(t);){var c=o.add(i,a),d=null!==tt(s,l);u.push({date:c,time:a,key:c.toISOString(),isoTimeStr:ot(c),isLabeled:d}),a=Je(a,r),s=Je(s,r)}return u}var Ks=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.buildTimeColsModel=st(Js),t.buildSlatMetas=st(Xs),t}return n(t,e),t.prototype.render=function(){var e=this,t=this.context,n=t.options,o=t.dateEnv,i=t.dateProfileGenerator,a=this.props,s=a.dateProfile,l=this.buildTimeColsModel(s,i),u=this.allDaySplitter.splitProps(a),c=this.buildSlatMetas(s.slotMinTime,s.slotMaxTime,n.slotLabelInterval,n.slotDuration,o),d=n.dayMinWidth,p=!d,f=d,h=n.dayHeaders&&Ar(Hi,{dates:l.headerDates,dateProfile:s,datesRepDistinctDays:!0,renderIntro:p?this.renderHeadAxis:null}),v=!1!==n.allDaySlot&&function(t){return Ar(vs,r({},u.allDay,{dateProfile:s,dayTableModel:l,nextDayThreshold:n.nextDayThreshold,tableMinWidth:t.tableMinWidth,colGroupNode:t.tableColGroupNode,renderRowIntro:p?e.renderTableRowAxis:null,showWeekNumbers:!1,expandRows:!1,headerAlignElRef:e.headerElRef,clientWidth:t.clientWidth,clientHeight:t.clientHeight,forPrint:a.forPrint},e.getAllDayMaxEventProps()))},g=function(t){return Ar(qs,r({},u.timed,{dayTableModel:l,dateProfile:s,axis:p,slotDuration:n.slotDuration,slatMetas:c,forPrint:a.forPrint,tableColGroupNode:t.tableColGroupNode,tableMinWidth:t.tableMinWidth,clientWidth:t.clientWidth,clientHeight:t.clientHeight,onSlatCoords:e.handleSlatCoords,expandRows:t.expandRows,onScrollTopRequest:e.handleScrollTopRequest}))};return f?this.renderHScrollLayout(h,v,g,l.colCnt,d,c,this.state.slatCoords):this.renderSimpleLayout(h,v,g)},t}(Rs);function Js(e,t){var n=new Ai(e.renderRange,t);return new Ui(n,!1)}var $s=lo({initialView:"timeGridWeek",optionRefiners:{allDaySlot:Boolean},views:{timeGrid:{component:Ks,usesMinMaxTime:!0,allDaySlot:!0,slotDuration:"00:30:00",slotEventOverlap:!0},timeGridDay:{type:"timeGrid",duration:{days:1}},timeGridWeek:{type:"timeGrid",duration:{weeks:1}}}}),Qs=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=e.dayDate,n=e.todayRange,o=this.context,i=o.theme,a=o.dateEnv,s=o.options,l=o.viewApi,u=gr(t,n),c=s.listDayFormat?a.format(t,s.listDayFormat):"",d=s.listDaySideFormat?a.format(t,s.listDaySideFormat):"",p=s.navLinks?yr(t):null,f=r({date:a.toDate(t),view:l,text:c,sideText:d,navLinkData:p},u),h=["fc-list-day"].concat(mr(u,i));return Ar(fo,{hookProps:f,classNames:s.dayHeaderClassNames,content:s.dayHeaderContent,defaultContent:el,didMount:s.dayHeaderDidMount,willUnmount:s.dayHeaderWillUnmount},(function(e,n,r,o){return Ar("tr",{ref:e,className:h.concat(n).join(" "),"data-date":rt(t)},Ar("th",{colSpan:3},Ar("div",{className:"fc-list-day-cushion "+i.getClass("tableCellShaded"),ref:r},o)))}))},t}(Yr);function el(e){var t=e.navLinkData?{"data-navlink":e.navLinkData,tabIndex:0}:{};return Ar(Wr,null,e.text&&Ar("a",r({className:"fc-list-day-text"},t),e.text),e.sideText&&Ar("a",r({className:"fc-list-day-side-text"},t),e.sideText))}var tl=bt({hour:"numeric",minute:"2-digit",meridiem:"short"}),nl=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t.prototype.render=function(){var e=this.props,t=this.context,n=e.seg,r=t.options.eventTimeFormat||tl;return Ar(ra,{seg:n,timeText:"",disableDragging:!0,disableResizing:!0,defaultContent:rl,isPast:e.isPast,isFuture:e.isFuture,isToday:e.isToday,isSelected:e.isSelected,isDragging:e.isDragging,isResizing:e.isResizing,isDateSelecting:e.isDateSelecting},(function(e,o,i,a,s){return Ar("tr",{className:["fc-list-event",s.event.url?"fc-event-forced-url":""].concat(o).join(" "),ref:e},function(e,t,n){var r=n.options;if(!1!==r.displayEventTime){var o=e.eventRange.def,i=e.eventRange.instance,a=!1,s=void 0;if(o.allDay?a=!0:en(e.eventRange.range)?e.isStart?s=Dn(e,t,n,null,null,i.range.start,e.end):e.isEnd?s=Dn(e,t,n,null,null,e.start,i.range.end):a=!0:s=Dn(e,t,n),a){var l={text:n.options.allDayText,view:n.viewApi};return Ar(fo,{hookProps:l,classNames:r.allDayClassNames,content:r.allDayContent,defaultContent:ol,didMount:r.allDayDidMount,willUnmount:r.allDayWillUnmount},(function(e,t,n,r){return Ar("td",{className:["fc-list-event-time"].concat(t).join(" "),ref:e},r)}))}return Ar("td",{className:"fc-list-event-time"},s)}return null}(n,r,t),Ar("td",{className:"fc-list-event-graphic"},Ar("span",{className:"fc-list-event-dot",style:{borderColor:s.borderColor||s.backgroundColor}})),Ar("td",{className:"fc-list-event-title",ref:i},a))}))},t}(Yr);function rl(e){var t=e.event,n=t.url;return Ar("a",r({},n?{href:n}:{}),t.title)}function ol(e){return e.text}var il=function(e){function t(){var t=null!==e&&e.apply(this,arguments)||this;return t.computeDateVars=st(sl),t.eventStoreToSegs=st(t._eventStoreToSegs),t.setRootEl=function(e){e?t.context.registerInteractiveComponent(t,{el:e}):t.context.unregisterInteractiveComponent(t)},t}return n(t,e),t.prototype.render=function(){var e=this,t=this.props,n=this.context,r=["fc-list",n.theme.getClass("table"),!1!==n.options.stickyHeaderDates?"fc-list-sticky":""],o=this.computeDateVars(t.dateProfile),i=o.dayDates,a=o.dayRanges,s=this.eventStoreToSegs(t.eventStore,t.eventUiBases,a);return Ar(Do,{viewSpec:n.viewSpec,elRef:this.setRootEl},(function(n,o){return Ar("div",{ref:n,className:r.concat(o).join(" ")},Ar(Fi,{liquid:!t.isHeightAuto,overflowX:t.isHeightAuto?"visible":"hidden",overflowY:t.isHeightAuto?"visible":"auto"},s.length>0?e.renderSegList(s,i):e.renderEmptyMessage()))}))},t.prototype.renderEmptyMessage=function(){var e=this.context,t=e.options,n=e.viewApi,r={text:t.noEventsText,view:n};return Ar(fo,{hookProps:r,classNames:t.noEventsClassNames,content:t.noEventsContent,defaultContent:al,didMount:t.noEventsDidMount,willUnmount:t.noEventsWillUnmount},(function(e,t,n,r){return Ar("div",{className:["fc-list-empty"].concat(t).join(" "),ref:e},Ar("div",{className:"fc-list-empty-cushion",ref:n},r))}))},t.prototype.renderSegList=function(e,t){var n=this.context,o=n.theme,i=n.options,a=function(e){var t,n,r=[];for(t=0;t<e.length;t+=1)n=e[t],(r[n.dayIndex]||(r[n.dayIndex]=[])).push(n);return r}(e);return Ar(Pi,{unit:"day"},(function(e,n){for(var s=[],l=0;l<a.length;l+=1){var u=a[l];if(u){var c=t[l].toISOString();s.push(Ar(Qs,{key:c,dayDate:t[l],todayRange:n}));for(var d=0,p=u=gn(u,i.eventOrder);d<p.length;d++){var f=p[d];s.push(Ar(nl,r({key:c+":"+f.eventRange.instance.instanceId,seg:f,isDragging:!1,isResizing:!1,isDateSelecting:!1,isSelected:!1},bn(f,n,e))))}}}return Ar("table",{className:"fc-list-table "+o.getClass("table")},Ar("tbody",null,s))}))},t.prototype._eventStoreToSegs=function(e,t,n){return this.eventRangesToSegs(cn(e,t,this.props.dateProfile.activeRange,this.context.options.nextDayThreshold).fg,n)},t.prototype.eventRangesToSegs=function(e,t){for(var n=[],r=0,o=e;r<o.length;r++){var i=o[r];n.push.apply(n,this.eventRangeToSegs(i,t))}return n},t.prototype.eventRangeToSegs=function(e,t){var n,r,o,i=this.context.dateEnv,a=this.context.options.nextDayThreshold,s=e.range,l=e.def.allDay,u=[];for(n=0;n<t.length;n+=1)if((r=on(s,t[n]))&&(o={component:this,eventRange:e,start:r.start,end:r.end,isStart:e.isStart&&r.start.valueOf()===s.start.valueOf(),isEnd:e.isEnd&&r.end.valueOf()===s.end.valueOf(),dayIndex:n},u.push(o),!o.isEnd&&!l&&n+1<t.length&&s.end<i.add(t[n+1].start,a))){o.end=s.end,o.isEnd=!0;break}return u},t}(so);function al(e){return e.text}function sl(e){for(var t=we(e.renderRange.start),n=e.renderRange.end,r=[],o=[];t<n;)r.push(t),o.push({start:t,end:me(t,1)}),t=me(t,1);return{dayDates:r,dayRanges:o}}function ll(e){return!1===e?null:bt(e)}var ul=lo({optionRefiners:{listDayFormat:ll,listDaySideFormat:ll,noEventsClassNames:Pt,noEventsContent:Pt,noEventsDidMount:Pt,noEventsWillUnmount:Pt},views:{list:{component:il,buttonTextKey:"list",listDayFormat:{month:"long",day:"numeric",year:"numeric"}},listDay:{type:"list",duration:{days:1},listDayFormat:{weekday:"long"}},listWeek:{type:"list",duration:{weeks:1},listDayFormat:{weekday:"long"},listDaySideFormat:{month:"long",day:"numeric",year:"numeric"}},listMonth:{type:"list",duration:{month:1},listDaySideFormat:{weekday:"long"}},listYear:{type:"list",duration:{year:1},listDaySideFormat:{weekday:"long"}}}}),cl=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return n(t,e),t}(Hr);cl.prototype.classes={root:"fc-theme-bootstrap",table:"table-bordered",tableCellShaded:"table-active",buttonGroup:"btn-group",button:"btn btn-primary",buttonActive:"active",popover:"popover",popoverHeader:"popover-header",popoverContent:"popover-body"},cl.prototype.baseIconClass="fa",cl.prototype.iconClasses={close:"fa-times",prev:"fa-chevron-left",next:"fa-chevron-right",prevYear:"fa-angle-double-left",nextYear:"fa-angle-double-right"},cl.prototype.rtlIconClasses={prev:"fa-chevron-right",next:"fa-chevron-left",prevYear:"fa-angle-double-right",nextYear:"fa-angle-double-left"},cl.prototype.iconOverrideOption="bootstrapFontAwesome",cl.prototype.iconOverrideCustomButtonOption="bootstrapFontAwesome",cl.prototype.iconOverridePrefix="fa-";var dl=lo({themeClasses:{bootstrap:cl}});var pl=lo({eventSourceDefs:[{parseMeta:function(e){var t=e.googleCalendarId;return!t&&e.url&&(t=function(e){var t;if(/^[^/]+@([^/.]+\.)*(google|googlemail|gmail)\.com$/.test(e))return e;if((t=/^https:\/\/www.googleapis.com\/calendar\/v3\/calendars\/([^/]*)/.exec(e))||(t=/^https?:\/\/www.google.com\/calendar\/feeds\/([^/]*)/.exec(e)))return decodeURIComponent(t[1]);return null}(e.url)),t?{googleCalendarId:t,googleCalendarApiKey:e.googleCalendarApiKey,googleCalendarApiBase:e.googleCalendarApiBase,extraParams:e.extraParams}:null},fetch:function(e,t,n){var o=e.context,i=o.dateEnv,a=o.options,s=e.eventSource.meta,l=s.googleCalendarApiKey||a.googleCalendarApiKey;if(l){var u=function(e){var t=e.googleCalendarApiBase;t||(t="https://www.googleapis.com/calendar/v3/calendars");return t+"/"+encodeURIComponent(e.googleCalendarId)+"/events"}(s),c=s.extraParams,d="function"==typeof c?c():c,p=function(e,t,n,o){var i,a,s;o.canComputeOffset?(a=o.formatIso(e.start),s=o.formatIso(e.end)):(a=me(e.start,-1).toISOString(),s=me(e.end,1).toISOString());i=r(r({},n||{}),{key:t,timeMin:a,timeMax:s,singleEvents:!0,maxResults:9999}),"local"!==o.timeZone&&(i.timeZone=o.timeZone);return i}(e.range,l,d,i);zo("GET",u,p,(function(e,r){var o,i;e.error?n({message:"Google Calendar API: "+e.error.message,errors:e.error.errors,xhr:r}):t({rawEvents:(o=e.items,i=p.timeZone,o.map((function(e){return function(e,t){var n=e.htmlLink||null;n&&t&&(n=function(e,t){return e.replace(/(\?.*?)?(#|$)/,(function(e,n,r){return(n?n+"&":"?")+t+r}))}(n,"ctz="+t));return{id:e.id,title:e.summary,start:e.start.dateTime||e.start.date,end:e.end.dateTime||e.end.date,url:n,location:e.location,description:e.description}}(e,i)}))),xhr:r})}),(function(e,t){n({message:e,xhr:t})}))}else n({message:"Specify a googleCalendarApiKey. See http://fullcalendar.io/docs/google_calendar/"})}}],optionRefiners:{googleCalendarApiKey:String},eventSourceRefiners:{googleCalendarApiKey:String,googleCalendarId:String,googleCalendarApiBase:String,extraParams:Pt}});return Go.push(ja,ys,$s,ul,dl,pl),e.BASE_OPTION_DEFAULTS=wt,e.BASE_OPTION_REFINERS=Ct,e.BaseComponent=Yr,e.BgEvent=pa,e.BootstrapTheme=cl,e.Calendar=ga,e.CalendarApi=zn,e.CalendarContent=Ci,e.CalendarDataManager=Jo,e.CalendarDataProvider=li,e.CalendarRoot=Ti,e.Component=Or,e.ContentHook=vo,e.CustomContentRenderContext=ho,e.DateComponent=so,e.DateEnv=Jn,e.DateProfileGenerator=To,e.DayCellContent=la,e.DayCellRoot=ca,e.DayGridView=gs,e.DayHeader=Hi,e.DaySeriesModel=Ai,e.DayTable=vs,e.DayTableModel=Ui,e.DayTableSlicer=hs,e.DayTimeCols=qs,e.DayTimeColsSlicer=Gs,e.DayTimeColsView=Ks,e.DelayedRunner=Zo,e.Draggable=Fa,e.ElementDragging=hi,e.ElementScrollController=Pr,e.Emitter=xr,e.EventApi=Bn,e.EventRoot=ra,e.EventSourceApi=V,e.FeaturefulElementDragging=Ma,e.Fragment=Wr,e.Interaction=ci,e.ListView=il,e.MountHook=mo,e.NamedTimeZoneImpl=ui,e.NowIndicatorRoot=aa,e.NowTimer=Pi,e.PointerDragging=Sa,e.PositionCache=_r,e.RefMap=zi,e.RenderHook=fo,e.ScrollController=Ir,e.ScrollResponder=Br,e.Scroller=Fi,e.SimpleScrollGrid=na,e.Slicer=Li,e.Splitter=hr,e.StandardEvent=oa,e.Table=ds,e.TableDateCell=_i,e.TableDowCell=Ii,e.TableView=Ga,e.Theme=Hr,e.ThirdPartyDraggable=Ba,e.TimeCols=Bs,e.TimeColsSlatsCoords=ks,e.TimeColsView=Rs,e.ViewApi=Un,e.ViewContextType=jr,e.ViewRoot=Do,e.WeekNumberRoot=ha,e.WindowScrollController=Nr,e.addDays=me,e.addDurations=Je,e.addMs=ye,e.addWeeks=ge,e.allowContextMenu=ae,e.allowSelection=oe,e.applyMutationToEventStore=Hn,e.applyStyle=q,e.applyStyleProp=Y,e.asCleanDays=function(e){return e.years||e.months||e.milliseconds?0:e.days},e.asRoughMinutes=function(e){return et(e)/6e4},e.asRoughMs=et,e.asRoughSeconds=function(e){return et(e)/1e3},e.buildClassNameNormalizer=yo,e.buildDayRanges=Ys,e.buildDayTableModel=ms,e.buildEventApis=Gn,e.buildEventRangeKey=wn,e.buildHashFromArray=function(e,t){for(var n={},r=0;r<e.length;r+=1){var o=t(e[r],r);n[o[0]]=o[1]}return n},e.buildNavLinkData=yr,e.buildSegCompareObj=mn,e.buildSegTimeText=Dn,e.buildSlatMetas=Xs,e.buildTimeColsModel=Js,e.collectFromHash=je,e.combineEventUis=Bt,e.compareByFieldSpec=ue,e.compareByFieldSpecs=le,e.compareNumbers=pe,e.compareObjs=ze,e.computeEdges=wr,e.computeFallbackHeaderFormat=ki,e.computeHeightAndMargins=function(e){return e.getBoundingClientRect().height+function(e){var t=window.getComputedStyle(e);return parseInt(t.marginTop,10)+parseInt(t.marginBottom,10)}(e)},e.computeInnerRect=Rr,e.computeRect=Tr,e.computeSegDraggable=yn,e.computeSegEndResizable=Sn,e.computeSegStartResizable=En,e.computeShrinkWidth=Bi,e.computeSmallestCellWidth=he,e.computeVisibleDayRange=Qt,e.config=vi,e.constrainPoint=ur,e.createContext=Vr,e.createDuration=Xe,e.createElement=Ar,e.createEmptyEventStore=At,e.createEventInstance=Ne,e.createEventUi=zt,e.createFormatter=bt,e.createPlugin=lo,e.createRef=Lr,e.diffDates=tn,e.diffDayAndTime=De,e.diffDays=Se,e.diffPoints=dr,e.diffWeeks=Ee,e.diffWholeDays=Ce,e.diffWholeWeeks=be,e.disableCursor=te,e.elementClosest=z,e.elementMatches=B,e.enableCursor=ne,e.eventTupleToStore=Ht,e.filterEventStoreDefs=Lt,e.filterHash=Ae,e.findDirectChildren=function(e,t){for(var n=e instanceof HTMLElement?[e]:e,r=[],o=0;o<n.length;o+=1)for(var i=n[o].children,a=0;a<i.length;a+=1){var s=i[a];t&&!B(s,t)||r.push(s)}return r},e.findElements=j,e.flexibleCompare=ce,e.flushToDom=Fr,e.formatDate=function(e,t){void 0===t&&(t={});var n=rr(t),r=bt(t),o=n.createMarkerMeta(e);return o?n.format(o.marker,r,{forcedTzo:o.forcedTzo}):""},e.formatDayString=rt,e.formatIsoTimeString=ot,e.formatRange=function(e,t,n){var r=rr("object"==typeof n&&n?n:{}),o=bt(n),i=r.createMarkerMeta(e),a=r.createMarkerMeta(t);return i&&a?r.formatRange(i.marker,a.marker,o,{forcedStartTzo:i.forcedTzo,forcedEndTzo:a.forcedTzo,isEndExclusive:n.isEndExclusive,defaultSeparator:wt.defaultRangeSeparator}):""},e.getAllowYScrolling=Gi,e.getCanVGrowWithinCell=pr,e.getClippingParents=kr,e.getDateMeta=gr,e.getDayClassNames=mr,e.getDefaultEventEnd=Nn,e.getElSeg=fn,e.getEventClassNames=Cn,e.getIsRtlScrollbarOnLeft=Dr,e.getRectCenter=cr,e.getRelevantEvents=Ot,e.getScrollGridClassNames=Ji,e.getScrollbarWidths=br,e.getSectionClassNames=$i,e.getSectionHasLiquidHeight=ji,e.getSegMeta=bn,e.getSlotClassNames=function(e,t){var n=["fc-slot","fc-slot-"+ve[e.dow]];return e.isDisabled?n.push("fc-slot-disabled"):(e.isToday&&(n.push("fc-slot-today"),n.push(t.getClass("today"))),e.isPast&&n.push("fc-slot-past"),e.isFuture&&n.push("fc-slot-future")),n},e.getStickyFooterScrollbar=ta,e.getStickyHeaderDates=ea,e.getUnequalProps=Fe,e.globalLocales=$n,e.globalPlugins=Go,e.greatestDurationDenominator=nt,e.guid=ee,e.hasBgRendering=dn,e.hasShrinkWidth=Ki,e.identity=Pt,e.interactionSettingsStore=fi,e.interactionSettingsToStore=pi,e.intersectRanges=on,e.intersectRects=lr,e.isArraysEqual=at,e.isColPropsEqual=Yi,e.isDateSpansEqual=kn,e.isInt=fe,e.isInteractionValid=eo,e.isMultiDayRange=en,e.isPropsEqual=Ve,e.isPropsValid=no,e.isValidDate=Ie,e.listenBySelector=K,e.mapHash=Ue,e.memoize=st,e.memoizeArraylike=function(e,t,n){var r=this,o=[],i=[];return function(a){for(var s=o.length,l=a.length,u=0;u<s;u+=1)if(a[u]){if(!at(o[u],a[u])){n&&n(i[u]);var c=e.apply(r,a[u]);t&&t(c,i[u])||(i[u]=c)}}else n&&n(i[u]);for(;u<l;u+=1)i[u]=e.apply(r,a[u]);return o=a,i.splice(l),i}},e.memoizeHashlike=function(e,t,n){var r=this,o={},i={};return function(a){var s={};for(var l in a)if(i[l])if(at(o[l],a[l]))s[l]=i[l];else{n&&n(i[l]);var u=e.apply(r,a[l]);s[l]=t&&t(u,i[l])?i[l]:u}else s[l]=e.apply(r,a[l]);return o=a,i=s,s}},e.memoizeObjArg=lt,e.mergeEventStores=Ut,e.multiplyDuration=$e,e.padStart=de,e.parseBusinessHours=ar,e.parseClassNames=Wt,e.parseDragMeta=mi,e.parseEventDef=Jt,e.parseFieldSpecs=se,e.parseMarker=Kn,e.pointInsideRect=sr,e.preventContextMenu=ie,e.preventDefault=Z,e.preventSelection=re,e.rangeContainsMarker=un,e.rangeContainsRange=ln,e.rangesEqual=an,e.rangesIntersect=sn,e.refineEventDef=Xt,e.refineProps=It,e.removeElement=F,e.removeExact=function(e,t){for(var n=0,r=0;r<e.length;)e[r]===t?(e.splice(r,1),n+=1):r+=1;return n},e.render=Ur,e.renderChunkContent=qi,e.renderFill=da,e.renderMicroColGroup=Zi,e.renderScrollShim=Qi,e.requestJson=zo,e.sanitizeShrinkWidth=Xi,e.setElSeg=pn,e.setRef=Kr,e.sliceEventStore=cn,e.sliceEvents=function(e,t){return cn(e.eventStore,e.eventUiBases,e.dateProfile.activeRange,t?e.nextDayThreshold:null).fg},e.sortEventSegs=gn,e.startOfDay=we,e.translateRect=function(e,t,n){return{left:e.left+t,right:e.right+t,top:e.top+n,bottom:e.bottom+n}},e.triggerDateSelect=In,e.unmountComponentAtNode=zr,e.unpromisify=Mr,e.version="5.5.1",e.whenTransitionDone=$,e.wholeDivideDurations=tt,Object.defineProperty(e,"__esModule",{value:!0}),e}({});/*!
FullCalendar v5.5.0
Docs & License: https://fullcalendar.io/
(c) 2020 Adam Shaw
*/
var FullCalendarLuxon=function(e,t,r){"use strict";var n=function(e,t){return(n=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(e,t){e.__proto__=t}||function(e,t){for(var r in t)Object.prototype.hasOwnProperty.call(t,r)&&(e[r]=t[r])})(e,t)};var o=function(){return(o=Object.assign||function(e){for(var t,r=1,n=arguments.length;r<n;r++)for(var o in t=arguments[r])Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e}).apply(this,arguments)};var a=function(e){function t(){return null!==e&&e.apply(this,arguments)||this}return function(e,t){function r(){this.constructor=e}n(e,t),e.prototype=null===t?Object.create(t):(r.prototype=t.prototype,new r)}(t,e),t.prototype.offsetForArray=function(e){return l(e,this.timeZoneName).offset},t.prototype.timestampToArray=function(e){return[(t=r.DateTime.fromMillis(e,{zone:this.timeZoneName})).year,t.month-1,t.day,t.hour,t.minute,t.second,t.millisecond];var t},t}(t.NamedTimeZoneImpl);var i=t.createPlugin({cmdFormatter:function(e,t){var r=function e(t){var r=t.match(/^(.*?)\{(.*)\}(.*)$/);if(r){var n=e(r[2]);return{head:r[1],middle:n,tail:r[3],whole:r[1]+n.whole+r[3]}}return{head:null,middle:null,tail:null,whole:t}}(e);if(t.end){var n=l(t.start.array,t.timeZone,t.localeCodes[0]),o=l(t.end.array,t.timeZone,t.localeCodes[0]);return function e(t,r,n,o){if(t.middle){var a=r(t.head),i=e(t.middle,r,n,o),l=r(t.tail),u=n(t.head),c=e(t.middle,r,n,o),d=n(t.tail);if(a===u&&l===d)return a+(i===c?i:i+o+c)+l}var f=r(t.whole),m=n(t.whole);if(f===m)return f;return f+o+m}(r,n.toFormat.bind(n),o.toFormat.bind(o),t.defaultSeparator)}return l(t.date.array,t.timeZone,t.localeCodes[0]).toFormat(r.whole)},namedTimeZonedImpl:a});function l(e,t,n){return r.DateTime.fromObject({zone:t,locale:n,year:e[0],month:e[1]+1,day:e[2],hour:e[3],minute:e[4],second:e[5],millisecond:e[6]})}return t.globalPlugins.push(i),e.default=i,e.toLuxonDateTime=function(e,n){if(!(n instanceof t.CalendarApi))throw new Error("must supply a CalendarApi instance");var o=n.getCurrentData().dateEnv;return r.DateTime.fromJSDate(e,{zone:o.timeZone,locale:o.locale.codes[0]})},e.toLuxonDuration=function(e,n){if(!(n instanceof t.CalendarApi))throw new Error("must supply a CalendarApi instance");var a=n.getCurrentData().dateEnv;return r.Duration.fromObject(o(o({},e),{locale:a.locale.codes[0]}))},Object.defineProperty(e,"__esModule",{value:!0}),e}({},FullCalendar,luxon);/* global fcom, moment, moreLinkTextLabel, FullCalendar, i, confFrontEndUrl, langLbl, userType */
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
};/* global monthNames, langLbl, fcom, VIEW_CALENDAR, VIEW_LISTING, VIEW_LISTING */
(function () {
    goToSearchPage = function (pageno) {
        var frm = document.frmSearchPaging;
        $(frm.pageno).val(pageno);
        search(frm);
    };
    searchListing = function (frm) {
        console.log(fcom.frmData(frm));
        fcom.ajax(fcom.makeUrl('Questions', 'searchquiz'), fcom.frmData(frm), function (response) {
            $("#listing").html(response);
        });
    };
    search = function (form) {
      //  var view = (form && form.view.value) ? parseInt(form.view.value) : VIEW_LISTING;
       // alert(view);
        var view=1;
        switch (view) {
            case VIEW_CALENDAR:
                getCalendarView();
                break;
            case VIEW_LISTING:
            default:
               searchListing(form);
                break;
        }
    };

  
    getCalendarView = function () {
        fcom.ajax(fcom.makeUrl('Questions', 'calendarView'), '', function (response) {
            $("#listing").html(response);
        });
    };

  /*  addgrade = function () {
      // Get all grade inputs
      const gradeInputs = document.querySelectorAll(
        '#grades-form input[type="number"]'
      );
      const gradesData = {}; // Object to store question ID and grades
      let isValid = true;
      let errorMessage = "";

      // Validate and collect grades
      gradeInputs.forEach((input) => {
        const questionId = input.name.match(/\d+/)[0]; // Extract question ID from name attribute

        const gradeValue = parseFloat(input.value);
        const maxMarks = parseFloat(input.getAttribute("data-max-marks"));

        if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > maxMarks) {
          isValid = false;
          errorMessage += `Grade for Question ${questionId} must be between 0 and ${maxMarks}.\n`;
        } else {
          alert(questionId);
          alert(gradesData[questionId]);
          gradesData[questionId] = gradeValue; // Store the question ID and grade
        }
        alert("Grades Data:", gradesData);
      });

      if (!isValid) {
        alert(errorMessage);
      } else {
        // If all validations pass, proceed with grades data
        console.log("Grades Data:", gradesData);
        alert("Grades validated successfully! Check console for details.");
      }
    };*/

    addgrade = function () {
        const gradeInputs = document.querySelectorAll(
          '#grades-form input[type="number"]'
        );
      
        const gradesData = {};
        let isValid = true;
        let errorMessage = "";

        const hiddenInput = document.querySelector('#grades-form input[name="rade_id"]');
        const radeId = hiddenInput ? hiddenInput.value : null;
        const score = document.querySelector('#grades-form input[name="score"]');
        const totalscore = score ? score.value : null;

         const  marks = document.querySelector('#grades-form input[name="totalmarks"]');
         const totalMarks = marks ? marks.value : null;

         const lecture_id = document.querySelector('#grades-form input[name="quizlectureid"]');
          const quiz_lecture_id = lecture_id ? lecture_id.value : null;

         const learner_id = document.querySelector('#grades-form input[name="quiz_learner_id"]');
         const quiz_learner_id = learner_id ? learner_id.value : null;
         const quiz_pass_percentage = document.querySelector('#grades-form input[name="quiz_pass_percentage"]');
         const Pass_percentage = quiz_pass_percentage ? quiz_pass_percentage.value : null;
          
        gradeInputs.forEach((input) => {
          // Debug: Log the name attribute
         // alert("Input Name Attribute:", input.name);
      
          // Extract question ID from the name attribute
          const questionIdMatch = input.name.match(/\d+/);
        //  alert("Question ID Match:", questionIdMatch); // Log questionIdMatch to check the regex output
      
          const questionId = questionIdMatch ? questionIdMatch[0] : null;
         // alert("Extracted Question ID:", questionId); // Debug: Log the extracted question ID
      
          if (!questionId) {
          //  console.error("Failed to extract a question ID. Skipping this input.");
            isValid = false;
            return; // Skip further processing for this input
          }
      
          const gradeValue = parseFloat(input.value);
          const maxMarks = parseFloat(input.getAttribute("data-max-marks"));
      
          if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > maxMarks) {
            isValid = false;
            errorMessage += `Grade for Question ${questionId} must be between 0 and ${maxMarks}.\n`;
          } else {
            gradesData[questionId] = gradeValue; // Store the question ID and grade
            
          }
        });
      
        if (!isValid) {
          alert(errorMessage);
        } else {
            
            fcom.ajax(fcom.makeUrl('Questions', 'submitTeacherResult'), {radeId: radeId,gradesData:gradesData,totalscore:totalscore,totalMarks:totalMarks,quiz_lecture_id:quiz_lecture_id,quiz_learner_id:quiz_learner_id,Pass_percentage:Pass_percentage}, function (response) {
                  
                setTimeout(function(){
                    location.reload();
                },1000)
                 
              });
        //     alert(`Grades Data Updated: ${JSON.stringify(gradesData)}`);
        //   alert("Grades validated successfully! Check console for details.");
        }
      };
      
    
    clearSearch = function () {
        document.frmClassSearch.reset();
        search(document.frmClassSearch);
    };
    addForm = function (classId) {
  
        fcom.ajax(fcom.makeUrl('Questions', 'addFormQuiz'), {classId: classId}, function (response) {
          //  alert(response);
            $.facebox(response, 'facebox-medium');
            bindDatetimePicker("#grpcls_start_datetime");
        });
    };
    setupClass = function (form, goToLangForm) {
        
        if (!$(form).validate()) {
            return;
        }
        var data = new FormData(form);
        fcom.ajaxMultipart(fcom.makeUrl('Questions', 'setupQuestions'), data, function (res) {
            search(document.frmClassSearch);
            if (goToLangForm && $('.lang-li').length > 0) {
                langId = $('.lang-li').first().attr('data-id');
                langForm(res.classId, langId);
                return;
            }
            $.facebox.close();
        }, {fOutMode: 'json'});
    };
    langForm = function (classId, langId) {
        fcom.ajax(fcom.makeUrl('Questions', 'langForm'), {classId: classId, langId: langId}, function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    setupLangData = function (form, goToNext) {
        if (!$(form).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Questions', 'setupLang'), fcom.frmData(form), function (res) {
            search(document.frmClassSearch);
            if (goToNext && $('.lang-list .is-active').next('li').length > 0) {
                $('.lang-list .is-active').next('li').find('a').trigger('click');
                return;
            }
            $.facebox.close();
        });
    };
    formatSlug = function (fld) {
        fcom.updateWithAjax(fcom.makeUrl('Home', 'slug'), {slug: $(fld).val()}, function (res) {
            $(fld).val(res.slug);
            if (res.slug != '') {
                checkUnique($(fld), 'tbl_group_classes', 'grpcls_slug', 'grpcls_id', $('#grpcls_id'), []);
            }
        });
    };

    getSubCategories = function (id) {
      
        id = (id == '') ? 0 : id;
        fcom.ajax(fcom.makeUrl('Courses', 'getSubcategories', [id]), '', function (res) {
            $("#subCategories").html(res);
             
        }, {process: false});
    };

    getSubCategoriessearch = function (id) {
      
        id = (id == '') ? 0 : id;
        fcom.ajax(fcom.makeUrl('Courses', 'getSubcategories', [id]), '', function (res) {
            $("#subCategoriesSearch").html(res);
             
        }, {process: false});
    };
})();