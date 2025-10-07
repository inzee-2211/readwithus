<?php

/**
 * A Common MyDate Utility  
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class MyDate extends FatDate
{

    const TYPE_TODAY = 1;
    const TYPE_THIS_WEEK = 2;
    const TYPE_LAST_WEEK = 3;
    const TYPE_THIS_MONTH = 4;
    const TYPE_LAST_MONTH = 5;
    const TYPE_THIS_YEAR = 6;
    const TYPE_LAST_YEAR = 7;
    const TYPE_LAST_12_MONTH = 8;
    const TYPE_ALL = 9;
    const TYPE_28_DAYS = 10;
    /* Format Types */
    const YMDHIS = 0;
    const DATEONLY = 1;
    const DATETIME = 2;
    const TIMEONLY = 3;

    /**
     * Format Date to display
     * 
     * @param string $date
     * @param bool $time
     * @return string
     */
    public static function showDate(string $date = null, bool $time = false): string
    {
        static::setCalendarLabels();
        if (empty($date) || is_null($date) || (substr($date, 0, 10) === '0000-00-00')) {
            return Label::getLabel('LBL_NA');
        }
        $format = [0 => FatApp::getConfig('FRONTEND_DATE_FORMAT')];
        if ($time) {
            $format[1] = FatApp::getConfig('FRONTEND_TIME_FORMAT');
        }
        global $calendarLabels;
        $formattedDate = date(implode(" ", $format), strtotime($date));
        return str_replace(array_keys($calendarLabels), $calendarLabels, $formattedDate);
    }

    /**
     * Format Time to display
     * 
     * @param string $date
     * @return string
     */
    public static function showTime(string $date): string
    {
        static::setCalendarLabels();
        if (empty($date) || is_null($date) || (substr($date, 0, 10) === '0000-00-00')) {
            return Label::getLabel('LBL_NA');
        }
        global $calendarLabels;
        $formattedTime = date(FatApp::getConfig('FRONTEND_TIME_FORMAT'), strtotime($date));
        return str_replace(array_keys($calendarLabels), $calendarLabels, $formattedTime);
    }

    /**
     * Set Calendar Labels
     * 
     * @global array $calendarLabels
     */
    public static function setCalendarLabels()
    {
        global $calendarLabels;
        if (is_null($calendarLabels)) {
            $days = MyDate::dayNames(true);
            $months = MyDate::getAllMonthName(true);
            $calendarLabels = array_merge(
                    $days['shortName'],
                    $days['longName'],
                    $months['shortName'],
                    $months['longName'],
                    MyDate::meridiems(true)
            );
        }
    }

    /**
     * Convert Date to defined|site timezone
     * 
     * @param string $date
     * @param string $timezome
     * @return string
     */
    public static function convert(string $date = null, string $timezome = null): string
    {
        if (empty($date) || substr($date, 0, 10) === '0000-00-00') {
            return $date;
        }
        $timezome = is_null($timezome) ? MyUtility::getSiteTimezone() : $timezome;
        return FatDate::changeDateTimezone($date, MyUtility::getSystemTimezone(), $timezome);
    }

    /**
     * Get Date Formats
     * 
     * @return array
     */
    public static function getDateFormats(): array
    {
        $date = date('Y-m-d H:i:s');
        return [
            'Y-m-d' => date('Y-m-d', strtotime(static::convert($date))),
            'd/m/Y' => date('d/m/Y', strtotime(static::convert($date))),
            'm-d-Y' => date('m-d-Y', strtotime(static::convert($date))),
            'M d, Y' => date('M d, Y', strtotime(static::convert($date))),
            'l, F d, Y' => date('l, F d, Y', strtotime(static::convert($date))),
        ];
    }

    /**
     * Get Time Formats
     * 
     * @return array
     */
    public static function getTimeFormats(): array
    {
        $date = date('Y-m-d H:i:s');
        return [
            'H:i' => date('H:i', strtotime(static::convert($date))),
            'H:i:s' => date('H:i:s', strtotime(static::convert($date))),
            'h:i a' => date('h:i a', strtotime(static::convert($date))),
            'h:i A' => date('h:i A', strtotime(static::convert($date))),
        ];
    }

    /**
     * Format Date
     * 
     * @param string $date
     * @param string $format
     * @param string $userTimezome
     * @return string
     */
    public static function formatDate(string $date = null, string $format = 'Y-m-d H:i:s', string $userTimezome = null): string
    {
        if (is_null($date)) {
            return '--';
        }
        if (empty($date) || substr($date, 0, 10) === '0000-00-00') {
            return $date;
        }
        $userTimezome = is_null($userTimezome) ? MyUtility::getSiteTimezone() : $userTimezome;
        $date = FatDate::changeDateTimezone($date, MyUtility::getSystemTimezone(), $userTimezome);
        return date($format, strtotime($date));
    }

    /**
     * Format To System Timezone
     * 
     * @param string $date
     * @param string $format
     * @param string $userTimezome
     * @return string
     */
    public static function formatToSystemTimezone(string $date = null, string $format = 'Y-m-d H:i:s', string $userTimezome = null): string
    {
        if (is_null($date)) {
            return '--';
        }
        if (empty($date) || substr($date, 0, 10) === '0000-00-00') {
            return $date;
        }
        $userTimezome = is_null($userTimezome) ? MyUtility::getSiteTimezone() : $userTimezome;
        $date = FatDate::changeDateTimezone($date, $userTimezome, MyUtility::getSystemTimezone());
        return date($format, strtotime($date));
    }

    /**
     * Change Date Timezone
     * 
     * @param string $date
     * @param string $fromTimezone
     * @param string $toTimezone
     * @return string
     */
    public static function changeDateTimezone($date, $fromTimezone, $toTimezone): string
    {
        return parent::changeDateTimezone($date, $fromTimezone, $toTimezone);
    }

    /**
     * Get Day Number
     * 
     * @param string $date
     * @return int
     */
    public static function getDayNumber(string $date): int
    {
        $number = date('N', strtotime($date));
        return (7 == $number) ? 0 : FatUtility::int($number);
    }

    /**
     * Get Week Diff
     * 
     * @param string $date1
     * @param string $date2
     * @return type
     */
    public static function weekDiff(string $date1, string $date2)
    {
        $first = new DateTime($date1);
        $second = new DateTime($date2);
        if ($date1 > $date2) {
            return self::weekDiff($date2, $date1);
        }
        return floor($first->diff($second)->days / 7);
    }

    /**
     * Get Offset
     * 
     * @param string $timezone
     * @return string
     */
    public static function getOffset(string $timezone = 'UTC'): string
    {
        return (new DateTime("now", new DateTimeZone($timezone)))->format('P');
    }

    /**
     * Get Offset
     * 
     * @param string $timezone
     * @return string
     */
    public static function formatTimeZoneLabel(string $timezone): string
    {
        $label = Label::getLabel('LBL_UTC_{offset}_{name}');
        return str_replace(['{offset}', '{name}'], [static::getOffset($timezone), Label::getLabel('TMZ_' . $timezone)], $label);
    }

    /**
     * Time Zone Listing
     * 
     * @return array
     */
    public static function timeZoneListing(): array
    {
        $timeZoneList = DateTimeZone::listIdentifiers();
        $finalArray = [];
        foreach ($timeZoneList as $timezone) {
            $finalArray[$timezone] = static::formatTimeZoneLabel($timezone);
        }
        return $finalArray;
    }

    /**
     * Get Week Start and End Date
     * 
     * @param DateTime $dateTime
     * @param string $format
     * @param bool $midNight
     * @return array
     */
    public static function getWeekStartAndEndDate(DateTime $dateTime, string $format = 'Y-m-d', bool $midNight = false): array
    {
        $dateTime = $dateTime->modify('last saturday')->modify('+1 day');
        $weekEndModify = ($midNight) ? 'next sunday midnight' : 'next saturday';
        return [
            'weekStart' => $dateTime->format($format),
            'weekEnd' => $dateTime->modify($weekEndModify)->format($format),
        ];
    }

    /**
     * Change Week Days To Date
     * 
     * @param array $weekDays
     * @param array $timeSlotArr
     * @return array
     */
    public static function changeWeekDaysToDate(array $weekDays, array $timeSlotArr = []): array
    {
        $weekStartUnix = strtotime(Availability::GENERAL_WEEKSTART);
        $newWeekDayArray = [];
        foreach ($weekDays as $key => $day) {
            $unixDate = strtotime("+" . $day . " days", $weekStartUnix);
            $date = date("Y-m-d", $unixDate);
            if (!empty($timeSlotArr)) {
                foreach ($timeSlotArr as $timeKey => $timeSlot) {
                    $startDateTime = $date . ' ' . $timeSlot['startTime'];
                    $endDateTime = $date . ' ' . $timeSlot['endTime'];
                    $startDateTime = MyDate::formatToSystemTimezone($startDateTime);
                    $endDateTime = MyDate::formatToSystemTimezone($endDateTime);
                    $newWeekDayArray[] = [
                        'startDate' => $startDateTime,
                        'endDate' => $endDateTime
                    ];
                }
            } else {
                $dateStart = date("Y-m-d H:i:s", $unixDate);
                $dateEnd = date('Y-m-d H:i:s', strtotime(" +1 day", $unixDate));
                $dateStart = MyDate::formatToSystemTimezone($dateStart);
                $dateEnd = MyDate::formatToSystemTimezone($dateEnd);
                $newWeekDayArray[] = [
                    'startDate' => $dateStart,
                    'endDate' => $dateEnd,
                ];
            }
        }
        return $newWeekDayArray;
    }

    /**
     * Hours Difference
     * 
     * @param string $toDate
     * @param string $fromDate
     * @param int $roundUpTo
     * @return float
     */
    public static function hoursDiff(string $toDate, string $fromDate = '', int $roundUpTo = 2): float
    {
        $fromDate = $fromDate ?: date('Y-m-d H:i:s');
        return round((strtotime($toDate) - strtotime($fromDate)) / 3600, $roundUpTo);
    }

    /**
     * Is DST
     * 
     * @param string $dateTime
     * @param string $timezone
     * @return type
     */
    public static function isDST(string $dateTime = '', string $timezone = null)
    {

        $dateTime = (empty($dateTime)) ? date('Y-m-d H:i:s') : $dateTime;
        $timezone = is_null($timezone) ? MyUtility::getSiteTimezone() : $timezone;
        $tz = new DateTimeZone($timezone);
        $theTime = strtotime($dateTime);
        $transition = $tz->getTransitions($theTime, $theTime);
        $transition = current($transition);
        return $transition['isdst'];
    }

    /**
     * Get hours and minutes formatted string.
     *
     * @param integer $seconds
     * @param string  $format
     *
     * @return string
     */
    public static function getHoursMinutes(int $seconds, string $format = '%02d:%02d'): string
    {
        if (empty($seconds) || !is_numeric($seconds)) {
            return false;
        }
        $minutes = round($seconds / 60);
        $hours = floor($minutes / 60);
        $remainMinutes = ($minutes % 60);
        return sprintf($format, $hours, $remainMinutes);
    }

    /**
     * Set Month and Week Names
     * 
     * @return type
     */
    public function setMonthAndWeekNames()
    {
        $monthName = MyDate::getAllMonthName(true);
        $monthName = array_merge($monthName['longName'], $monthName['shortName']);
        $dateName = MyDate::dayNames(true);
        $dateName = array_merge($dateName['longName'], $dateName['shortName']);
        return $this->monthDateName = array_merge($monthName, $dateName, MyDate::meridiems(true));
    }

    /**
     * Convert To Local
     * 
     * @param type $dateTime
     * @return type
     */
    public function convertToLocal($dateTime)
    {
        $monthDateName = (empty($this->monthDateName)) ? $this->setMonthAndWeekNames() : $this->monthDateName;
        return str_replace(array_keys($monthDateName), $monthDateName, $dateTime);
    }

    /**
     * Get All Month Name
     * 
     * @param bool $getWithKeys
     * @return array
     */
    public static function getAllMonthName(bool $getWithKeys = false): array
    {
        $monthName = [
            'longName' => [
                'January' => Label::getLabel('LBL_JANUARY'),
                'February' => Label::getLabel('LBL_FEBRUARY'),
                'March' => Label::getLabel('LBL_MARCH'),
                'April' => Label::getLabel('LBL_APRIL'),
                'May' => Label::getLabel('LBL_MAY'),
                'June' => Label::getLabel('LBL_JUNE'),
                'July' => Label::getLabel('LBL_JULY'),
                'August' => Label::getLabel('LBL_AUGUST'),
                'September' => Label::getLabel('LBL_SEPTEMBER'),
                'October' => Label::getLabel('LBL_OCTOBER'),
                'November' => Label::getLabel('LBL_NOVEMBER'),
                'December' => Label::getLabel('LBL_DECEMBER')
            ],
            'shortName' => [
                'Jan' => Label::getLabel('LBL_JAN'),
                'Feb' => Label::getLabel('LBL_FEB'),
                'Mar' => Label::getLabel('LBL_MAR'),
                'Apr' => Label::getLabel('LBL_APR'),
                'May' => Label::getLabel('LBL_MAY'),
                'Jun' => Label::getLabel('LBL_JUN'),
                'Jul' => Label::getLabel('LBL_JUL'),
                'Aug' => Label::getLabel('LBL_AUG'),
                'Sep' => Label::getLabel('LBL_SEP'),
                'Oct' => Label::getLabel('LBL_OCT'),
                'Nov' => Label::getLabel('LBL_NOV'),
                'Dec' => Label::getLabel('LBL_DEC')
            ]
        ];
        if (!$getWithKeys) {
            return [
                'longName' => array_values($monthName['longName']),
                'shortName' => array_values($monthName['shortName']),
            ];
        }
        return $monthName;
    }

    /**
     * Day Names
     *
     * @return array
     * Note : Do not change the index of days 
     */
    public static function dayNames(bool $getWithKeys = false): array
    {
        $dayNames = [
            'longName' => [
                'Monday' => Label::getLabel('LBL_MONDAY'),
                'Tuesday' => Label::getLabel('LBL_TUESDAY'),
                'Wednesday' => Label::getLabel('LBL_WEDNESDAY'),
                'Thursday' => Label::getLabel('LBL_THURSDAY'),
                'Friday' => Label::getLabel('LBL_FRIDAY'),
                'Saturday' => Label::getLabel('LBL_SATURDAY'),
                'Sunday' => Label::getLabel('LBL_SUNDAY'),
            ],
            'shortName' => [
                'Mon' => Label::getLabel('LBL_MON'),
                'Tue' => Label::getLabel('LBL_TUE'),
                'Wed' => Label::getLabel('LBL_WED'),
                'Thu' => Label::getLabel('LBL_THU'),
                'Fri' => Label::getLabel('LBL_FRI'),
                'Sat' => Label::getLabel('LBL_SAT'),
                'Sun' => Label::getLabel('LBL_SUN'),
            ],
        ];
        if (!$getWithKeys) {
            return [
                'longName' => array_values($dayNames['longName']),
                'shortName' => array_values($dayNames['shortName']),
            ];
        }
        return $dayNames;
    }

    /**
     * Get Meridiems
     * 
     * @param bool $getWithKeys
     * @return type
     */
    public static function meridiems(bool $getWithKeys = false): array
    {
        $meridiems = [
            'AM' => Label::getLabel('LBL_AM'),
            'PM' => Label::getLabel('LBL_PM'),
        ];
        if (!$getWithKeys) {
            return array_values($meridiems);
        }
        return $meridiems;
    }

    /**
     * Get Start End Date
     * 
     * @param int $duration
     * @param string $timezone
     * @param bool $convertInSystemTimezone
     * @param string $dateFormat
     * @return array
     */
    public static function getStartEndDate(int $duration, string $timezone = NULL, bool $convertInSystemTimezone = false, string $dateFormat = 'Y-m-d H:i:s'): array
    {
        $timezone = (is_null($timezone)) ? MyUtility::getSiteTimezone() : $timezone;
        $sDateTime = new dateTime('now', new DateTimeZone($timezone));
        $eDateTime = new dateTime('now', new DateTimeZone($timezone));
        $dayNumber = $sDateTime->format('w');
        switch ($duration) {
            case static::TYPE_TODAY:
                $sDateTime->modify('today');
                $eDateTime->modify('today +1 day');
                break;
            case static::TYPE_THIS_WEEK:
                $startModif = 'this week monday -1 day';
                $endModify = 'this week sunday';
                if ($dayNumber == 0) {
                    $startModif = 'this week sunday';
                    $endModify = 'this week sunday +7 days';
                }
                $sDateTime->modify($startModif);
                $eDateTime->modify($endModify);
                break;
            case static::TYPE_LAST_WEEK:
                $startModif = 'last week monday -1 day';
                $endModify = 'last week monday +6 day';
                if ($dayNumber == 0) {
                    $startModif = 'last week sunday';
                    $endModify = 'this week sunday';
                }
                $sDateTime->modify($startModif);
                $eDateTime->modify($endModify);
                break;
            case static::TYPE_THIS_MONTH:
                $sDateTime->modify('first day of this month midnight');
                $eDateTime->modify('first day of next month midnight');
                break;
            case static::TYPE_LAST_MONTH:
                $sDateTime->modify('first day of previous month midnight');
                $eDateTime->modify('first day of this month midnight');
                break;
            case static::TYPE_THIS_YEAR:
                $sDateTime->modify('first day of January midnight');
                $eDateTime->modify('next year January 1st midnight');
                break;
            case static::TYPE_LAST_YEAR:
                $sDateTime->modify('last year January 1st midnight');
                $eDateTime->modify('first day of January midnight');
                break;
            case static::TYPE_LAST_12_MONTH:
                $sDateTime->modify('first day of this month midnight -11 months');
                $eDateTime->modify('first day of next month midnight');
                break;
            case static::TYPE_28_DAYS:
                $sDateTime->modify('today');
                $eDateTime->modify('+28 days midnight');
                break;
            case static::TYPE_ALL:
            default:
                $sDateTime->modify('first day of January 2018 midnight');
                break;
        }
        $start = $sDateTime->format($dateFormat);
        $end = $eDateTime->format($dateFormat);
        if ($convertInSystemTimezone) {
            $start = static::formatToSystemTimezone($sDateTime->format('Y-m-d H:i:s'), 'Y-m-d H:i:s', $timezone);
            $end = static::formatToSystemTimezone($eDateTime->format('Y-m-d H:i:s'), 'Y-m-d H:i:s', $timezone);
            if ($dateFormat != 'Y-m-d H:i:s') {
                $start = date($dateFormat, strtotime($start));
                $end = date($dateFormat, strtotime($end));
            }
        }
        return [
            'startDate' => $start,
            'endDate' => $end
        ];
    }

    /**
     * Get Duration Types
     * 
     * @return array
     */
    public static function getDurationTypesArr(): array
    {
        return [
            static::TYPE_TODAY => Label::getLabel('LBL_TODAY'),
            static::TYPE_THIS_WEEK => Label::getLabel('LBL_THIS_WEEK'),
            static::TYPE_LAST_WEEK => Label::getLabel('LBL_LAST_WEEK'),
            static::TYPE_THIS_MONTH => Label::getLabel('LBL_THIS_MONTH'),
            static::TYPE_LAST_MONTH => Label::getLabel('LBL_LAST_MONTH'),
            static::TYPE_THIS_YEAR => Label::getLabel('LBL_THIS_YEAR'),
            static::TYPE_LAST_YEAR => Label::getLabel('LBL_LAST_YEAR'),
            static::TYPE_LAST_12_MONTH => Label::getLabel('LBL_LAST_12_MONTH'),
            static::TYPE_ALL => Label::getLabel('LBL_ALL'),
        ];
    }

    public static function getSubscriptionDates(int $days, string $timezone = NULL): array
    {
        $timezone = (is_null($timezone)) ? MyUtility::getSiteTimezone() : $timezone;
        $sDateTime = new DateTime('now', new DateTimeZone($timezone));
        $eDateTime = new DateTime('now', new DateTimeZone($timezone));
        $sDateTime->modify('today');
        $eDateTime->modify('today +' . $days . ' days midnight');
        return [
            'startDate' => $sDateTime->format('Y-m-d H:i:s'),
            'endDate' => $eDateTime->format('Y-m-d H:i:s')
        ];
    }

}
