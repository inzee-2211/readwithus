<?php

/**
 * A Common Utility Class 
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class AppConstant
{
    /* YES|NO Flag */

    const NO = 0;
    const YES = 1;
    /* Active Status */
    const ACTIVE = 1;
    const INACTIVE = 0;
    /* Default Pagesize */
    const PAGESIZE = 12;
    /* Payment Status */
    const UNPAID = 0;
    const ISPAID = 1;
    /* Class Types */
    const CLASS_1TO1 = 1;
    const CLASS_GROUP = 2;
    /* Entity Types */
    const LESSON = 1;
    const GCLASS = 2;
    const COURSE = 3;
    /* weekdays */
    const DAY_SUNDAY = 0;
    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;
    /* Genders */
    const GEN_MALE = 1;
    const GEN_FEMALE = 2;
    /* Layouts */
    const LAYOUT_LTR = 'ltr';
    const LAYOUT_RTL = 'rtl';
    /* Sorting */
    const SORT_POPULARITY = 1;
    const SORT_PRICE_ASC = 2;
    const SORT_PRICE_DESC = 3;
    const TARGET_CURRENT_WINDOW = "_self";
    const TARGET_BLANK_WINDOW = "_blank";
    const PERCENTAGE = 1;
    const FLAT_VALUE = 2;
    const SCREEN_DESKTOP = 1;
    const SCREEN_IPAD = 2;
    const SCREEN_MOBILE = 3;
    const SMTP_TLS = 'tls';
    const SMTP_SSL = 'ssl';
    const PHONE_NO_REGEX = "^[0-9(\)-\-{\}  +-+]{4,16}$";
    const SLUG_REGEX = "^[0-9a-z-\-]{4,200}$";
    const CREDIT_CARD_NO_REGEX = "^(?:(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$";
    const CVV_NO_REGEX = "^[0-9]{3,4}$";
    const CLASS_TYPE_GROUP = 'group';
    const CLASS_TYPE_1_TO_1 = '1to1';
    const INTRODUCTION_VIDEO_LINK_REGEX = "^(?:https?:)?(?:\/\/)?(?:youtu\.be\/|(?:www\.|m\.)?youtube\.com\/(?:watch|v|embed)(?:\.php)?(?:\?.*v=|\/))([a-zA-Z0-9\_-]{7,15})(?:[\?&][a-zA-Z0-9\_-]+=[a-zA-Z0-9\_-]+)*(?:[&\/\#].*)?$";
    const DATE_TIME_REGEX = "(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})";
    const PASSWORD_REGEX = "^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%-_]{8,15}$";
    const URL_REGEX = "(?=.{5,253}$)((http|https):\/\/)(www.)?[a-zA-Z0-9@:%._\\+~#?&\/\/=-]{2,253}\\.[a-z]{2,6}\\b([-a-zA-Z0-9@:%._\\+~#?&\/\/=]*)";
    const PASSWORD_CUSTOM_ERROR_MSG = "MSG_PASSWORD_MUST_BE_EIGHT_ALPHANUMERIC";

    /* Display View */
    const VIEW_LISTING = 1;
    const VIEW_CALENDAR = 2;
    const VIEW_SHORT = 3;
    const VIEW_DASHBOARD_LISTING = 4;
    const SEARCH_SESSION = 'SEARCH_SESSION';

    /**
     * Return Array Value
     * 
     * @param array $arr
     * @param int|string $key
     * @return array
     */
    public static function returArrValue(array $arr, $key = null)
    {
        if ($key === null) {
            return $arr;
        }
        return $arr[$key] ?? Label::getLabel('LBL_NA');
    }

    /**
     * Get Yes No Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getYesNoArr(int $key = null)
    {
        $arr = [
            static::YES => Label::getLabel('LBL_YES'),
            static::NO => Label::getLabel('LBL_NO')
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Active Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getActiveArr(int $key = null, int $langId = 0)
    {
        $arr = [
            static::ACTIVE => Label::getLabel('LBL_ACTIVE', $langId),
            static::INACTIVE => Label::getLabel('LBL_INACTIVE', $langId)
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Genders
     * 
     * @param int $key
     * @return string|array
     */
    public static function getGenders(int $key = null)
    {
        $arr = [
            static::GEN_MALE => Label::getLabel('LBL_MALE'),
            static::GEN_FEMALE => Label::getLabel('LBL_FEMALE')
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Class Types
     * 
     * @param int $key
     * @return string|array
     */
    public static function getClassTypes(int $key = null)
    {
        $arr = [
            static::CLASS_1TO1 => Label::getLabel('LBL_ONE_TO_ONE'),
            static::CLASS_GROUP => Label::getLabel('LBL_GROUP_CLASS')
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Layout Directions
     * 
     * @param string $key
     * @return string|array
     */
    public static function getLayoutDirections(string $key = null)
    {
        $arr = [
            static::LAYOUT_LTR => Label::getLabel('LBL_LEFT_TO_RIGHT'),
            static::LAYOUT_RTL => Label::getLabel('LBL_RIGHT_TO_LEFT'),
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Week Days
     * 
     * @return array
     */
    public static function getWeekDays(): array
    {
        return [
            static::DAY_SUNDAY => Label::getLabel('LBL_Sun'),
            static::DAY_MONDAY => Label::getLabel('LBL_Mon'),
            static::DAY_TUESDAY => Label::getLabel('LBL_Tue'),
            static::DAY_WEDNESDAY => Label::getLabel('LBL_Wed'),
            static::DAY_THURSDAY => Label::getLabel('LBL_Thu'),
            static::DAY_FRIDAY => Label::getLabel('LBL_Fri'),
            static::DAY_SATURDAY => Label::getLabel('LBL_Sat')
        ];
    }

    /**
     * Get Sort by Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getSortbyArr(int $key = null)
    {
        $arr = [
            static::SORT_POPULARITY => Label::getLabel('LBL_BY_POPULARITY'),
            static::SORT_PRICE_ASC => Label::getLabel('LBL_BY_PRICE_LOW_TO_HIGH'),
            static::SORT_PRICE_DESC => Label::getLabel('LBL_BY_PRICE_HIGH_TO_LOW'),
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Banner Type Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function bannerTypeArr(int $key = null)
    {
        $bannerTypeArr = Language::getAllNames();
        $arr = [0 => Label::getLabel('LBL_All_Languages')] + $bannerTypeArr;
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Link Targets Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getLinkTargetsArr(int $key = null)
    {
        $arr = [
            static::TARGET_CURRENT_WINDOW => Label::getLabel('LBL_Same_Window'),
            static::TARGET_BLANK_WINDOW => Label::getLabel('LBL_New_Window')
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Percentage Flat Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getPercentageFlatArr(int $key = null)
    {
        $arr = [
            static::FLAT_VALUE => Label::getLabel('LBL_FLAT_VALUE'),
            static::PERCENTAGE => Label::getLabel('LBL_PERCENTAGE')
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Displays Array
     * 
     * @param int $key
     * @return string|array
     */
    public static function getDisplaysArr(int $key = null)
    {
        $arr = [
            static::SCREEN_DESKTOP => Label::getLabel('LBL_Desktop'),
            static::SCREEN_IPAD => Label::getLabel('LBL_Ipad'),
            static::SCREEN_MOBILE => Label::getLabel('LBL_Mobile')
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get SMTP Secure
     * 
     * @param int $key
     * @return string|array
     */
    public static function getSmtpSecureArr(int $key = null)
    {
        $arr = [
            static::SMTP_TLS => Label::getLabel('LBL_tls'),
            static::SMTP_SSL => Label::getLabel('LBL_ssl'),
        ];
        return static::returArrValue($arr, $key);
    }

    /**
     * Get Empty Day Slots
     * 
     * @return array
     */
    public static function getEmptyDaySlots(): array
    {
        return [
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
        ];
    }

    /**
     * Get Booking Slots
     * 
     * @return array
     */
    public static function getBookingSlots(): array
    {
        return [
            15 => 15, 20=> 20, 30 => 30,
            45 => 45, 60 => 60,
            90 => 90, 120 => 120
        ];
    }

    /**
     * Get Group Class Slots
     * 
     * @return array
     */
    public static function getGroupClassSlots(): array
    {
        return [
            15 => 15, 20=> 20, 30 => 30,
            45 => 45, 60 => 60,
            90 => 90, 120 => 120
        ];
    }

    /**
     * Format Class Slots
     * 
     * @param array $durations
     * @return array
     */
    public static function fromatClassSlots(array $durations = null): array
    {
        $durations = is_null($durations) ? explode(',', FatApp::getConfig('CONF_GROUP_CLASS_DURATION')) : $durations;
        $returnArray = [];
        foreach ($durations as $value) {
            $returnArray[$value] = $value . ' ' . Label::getLabel('LBL_MINUTES');
        }
        return $returnArray;
    }

    /**
     * Get Months Array
     * 
     * @return array
     */
    public static function getMonthsArr(): array
    {
        return [
            '01' => Label::getLabel('LBL_January'),
            '02' => Label::getLabel('LBL_Februry'),
            '03' => Label::getLabel('LBL_March'),
            '04' => Label::getLabel('LBL_April'),
            '05' => Label::getLabel('LBL_May'),
            '06' => Label::getLabel('LBL_June'),
            '07' => Label::getLabel('LBL_July'),
            '08' => Label::getLabel('LBL_August'),
            '09' => Label::getLabel('LBL_September'),
            '10' => Label::getLabel('LBL_October'),
            '11' => Label::getLabel('LBL_November'),
            '12' => Label::getLabel('LBL_December'),
        ];
    }

    /**
     * Rating Array
     * 
     * @return array
     */
    public static function ratingArr(): array
    {
        return ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'];
    }

    /**
     * Get Display Views
     * 
     * @return array
     */
    public static function getDisplayViews(): array
    {
        return [
            static::VIEW_LISTING => Label::getLabel('VIEW_LISTING'),
            static::VIEW_CALENDAR => Label::getLabel('VIEW_CALENDAR'),
            static::VIEW_DASHBOARD_LISTING => Label::getLabel('VIEW_DASHBOARD_LISTING'),
        ];
    }

    /**
     * Get Price Range
     * 
     * @param int $key
     * @return type
     */
    public static function getPriceRange(int $key = null)
    {
        $arr = json_decode(FatApp::getConfig('CONF_SEARCH_PRICE_OPTIONS'), true);
        return self::returArrValue($arr, $key);
    }

    /**
     * Get Price Range Options
     * 
     * @return array
     */
    public static function getPriceRangeOptions()
    {
        $arr = static::getPriceRange();
        foreach ($arr as $key => $item) {
            $arr[$key] = MyUtility::formatMoney($item[0]) . ' - ' . MyUtility::formatMoney($item[1]);
        }
        return $arr;
    }
}
