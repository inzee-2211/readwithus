<?php

/**
 * A Common MyUtility
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class MyUtility extends FatUtility
{

    private static $userIp;
    private static $siteLangId = 1;
    private static $siteCurrId = 1;
    private static $siteLanguage;
    private static $siteCurrency;
    private static $siteTimezone;
    private static $systemTimezone;
    private static $systemLanguage;
    private static $systemCurrency;
    private static $cookieConsents;

    const FAILED = 0;
    const SUCCESS = 1;
    const AUTHREQ = 2;
    const NOTFOUND = 3;

    /**
     * Get User Agent
     * 
     * @return string
     */
    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Get User Type
     * 
     * @return int
     */
    public static function getUserType(): int
    {
        return $_SESSION['SITE_USER_TYPE'] ?? 0;
    }

    /**
     * Set User Type
     * 
     * @param int $userType
     */
    public static function setUserType(int $userType)
    {
        $_SESSION['SITE_USER_TYPE'] = $userType;
    }

    /**
     * Set Site Language
     * 
     * @param array $arr
     * @param bool $forcefully
     * @return void
     */
    public static function setSiteLanguage(array $arr, bool $forcefully = false): void
    {
        self::$siteLanguage = $arr;
        self::$siteLangId = static::int($arr['language_id']);
        if (empty($_COOKIE['CONF_SITE_LANGUAGE']) || $forcefully) {
            static::setCookie('CONF_SITE_LANGUAGE', self::$siteLangId);
        }
    }

    /**
     * Get Site Lang Id
     * 
     * @return int
     */
    public static function getSiteLangId(): int
    {
        return self::$siteLangId ?? 1;
    }

    /**
     * Get Site Language
     * 
     * @return array
     */
    public static function getSiteLanguage(): array
    {
        return self::$siteLanguage;
    }

    /**
     * Set Site Currency
     * 
     * @param array $arr
     * @return void
     */
    public static function setSiteCurrency(array $arr, bool $forcefully = false): void
    {
        self::$siteCurrency = $arr;
        self::$siteCurrId = static::int($arr['currency_id']);
        if (empty($_COOKIE['CONF_SITE_CURRENCY']) || $forcefully) {
            static::setCookie('CONF_SITE_CURRENCY', self::$siteCurrId);
        }
    }

    /**
     * Get Site Currency Id
     * 
     * @return int
     */
    public static function getSiteCurrId(): int
    {
        return self::$siteCurrId;
    }

    /**
     * Get Site Currency
     * 
     * @return array
     */
    public static function getSiteCurrency(): array
    {
        return self::$siteCurrency;
    }

    /**
     * Set Admin Timezone
     * 
     * @param string $timezone
     * @param bool $forcefully
     */
    public static function setAdminTimezone(string $timezone, bool $forcefully = false)
    {
        self::$siteTimezone = $timezone;
        if (empty($_COOKIE['CONF_ADMIN_TIMEZONE']) || $forcefully) {
            static::setCookie('CONF_ADMIN_TIMEZONE', self::$siteTimezone, 604800, CONF_WEBROOT_BACKEND, false);
        }
    }

    /**
     * Set Site Timezone
     * 
     * @param string $timezone
     * @param bool $forcefully
     */
    public static function setSiteTimezone(string $timezone, bool $forcefully = false)
    {
        self::$siteTimezone = $timezone;
        if (empty($_COOKIE['CONF_SITE_TIMEZONE']) || $forcefully) {
            static::setCookie('CONF_SITE_TIMEZONE', self::$siteTimezone, 604800, CONF_WEBROOT_FRONT_URL, false);
        }
    }

    /**
     * Get Site Timezone
     * 
     * @return string
     */
    public static function getSiteTimezone(): string
    {
        return empty(self::$siteTimezone) ? CONF_SERVER_TIMEZONE : self::$siteTimezone;
    }

    /**
     * Set Cookie Consents
     * 
     * @param array $arr
     */
    public static function setCookieConsents(array $arr, bool $forcefully = false)
    {
        self::$cookieConsents = json_encode($arr);
        if (empty($_COOKIE['CONF_SITE_CONSENTS']) || $forcefully) {
            static::setCookie('CONF_SITE_CONSENTS', self::$cookieConsents);
        }
    }

    /**
     * Get Cookie Consents
     * 
     * @return array
     */
    public static function getCookieConsents(): array
    {
        return json_decode(self::$cookieConsents, true);
    }

    /**
     * Set System Timezone
     */
    public static function setSystemTimezone()
    {
        self::$systemTimezone = CONF_SERVER_TIMEZONE;
    }

    /**
     * Get System Timezone
     * 
     * @return string
     */
    public static function getSystemTimezone(): string
    {
        return self::$systemTimezone;
    }

    /**
     * Set System Language
     */
    public static function setSystemLanguage()
    {
        self::$systemLanguage = Language::getData(CONF_DEFAULT_LANG);
    }

    /**
     * @return array
     */
    public static function getSystemLanguage()
    {
        return self::$systemLanguage;
    }

    /**
     * Set System Currency
     */
    public static function setSystemCurrency()
    {
        self::$systemCurrency = Currency::getSystemCurrency(self::$siteLangId);
    }

    /**
     * Get System Currency
     * 
     * @return type
     */
    public static function getSystemCurrency()
    {
        return self::$systemCurrency;
    }

    /**
     * Get Currency Symbol
     * 
     * @return string
     */
    public static function getCurrencySymbol(): string
    {
        return trim(self::$siteCurrency['currency_symbol_left'] . ' ' . self::$siteCurrency['currency_symbol_right']);
    }

    /**
     * Get Currency Left Symbol
     * 
     * @return string
     */
    public static function getCurrencyLeftSymbol(): string
    {
        return trim(self::$siteCurrency['currency_symbol_left']);
    }

    /**
     * Get Currency Right Symbol
     * 
     * @return string
     */
    public static function getCurrencyRightSymbol(): string
    {
        return trim(self::$siteCurrency['currency_symbol_right']);
    }

    /**
     * Get Layout Direction
     * 
     * @return string
     */
    public static function getLayoutDirection(): string
    {
        return self::$siteLanguage['language_direction'];
    }

    /**
     * Get Site Languages
     * 
     * @return array
     */
    public static function getSiteLanguages()
    {
        $srch = new SearchBase(Language::DB_TBL);
        $srch->addMultipleFields(['language_id', 'language_code', 'language_name']);
        $srch->addCondition('language_active', '=', AppConstant::YES);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get Site Currencies
     * 
     * @param int $langId
     * @return array
     */
    public static function getSiteCurrencies(int $langId = 0)
    {
        $langId = empty($langId) ? self::$siteLangId : $langId;
        $srch = new SearchBase(Currency::DB_TBL, 'currency');
        $srch->joinTable(Currency::DB_TBL_LANG, 'LEFT JOIN', 'curlang.currencylang_currency_id = '
                . 'currency.currency_id AND curlang.currencylang_lang_id = ' . $langId, 'curlang');
        $srch->addCondition('currency.currency_active', '=', AppConstant::YES);
        $srch->addMultipleFields(['currency_id', 'currency_code', 'currency_name']);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Remove User Cookies
     */
    public function removeUserCookies()
    {
        static::setCookie('CONF_SITE_TIMEZONE', '', -604800, CONF_WEBROOT_FRONT_URL, false);
        static::setCookie('CONF_SITE_CONSENTS', '', -604800);
        static::setCookie('CONF_SITE_LANGUAGE', '', -604800);
    }

    /**
     * Get Common JS Labels
     * 
     * @return array
     */
    public static function getCommonLabels(array $siteLanguages): array
    {
        $jsVariables = [
            'layoutDirection' => MyUtility::getLayoutDirection(),
            'isMandatory' => Label::getLabel('LBL_IS_MANDATORY'),
            'processing' => Label::getLabel('LBL_PROCESSING_PLEASE_WAIT'),
            'confirmRemove' => Label::getLabel('LBL_DO_YOU_WANT_TO_REMOVE'),
            'confirmCancel' => Label::getLabel('LBL_DO_YOU_WANT_TO_CANCEL'),
            'pleaseEnterValidEmailId' => Label::getLabel('VLBL_PLEASE_ENTER_VALID_EMAIL_ID_FOR'),
            'charactersSupportedFor' => Label::getLabel('VLBL_ONLY_CHARACTERS_ARE_SUPPORTED_FOR'),
            'pleaseEnterIntegerValue' => Label::getLabel('VLBL_PLEASE_ENTER_INTEGER_VALUE_FOR'),
            'pleaseEnterNumericValue' => Label::getLabel('VLBL_PLEASE_ENTER_NUMERIC_VALUE_FOR'),
            'startWithLetterOnlyAlphanumeric' => Label::getLabel('VLBL_START_WITH_LETTER_ONLY_ALPHANUMERIC'),
            'mustBeBetweenCharacters' => Label::getLabel('VLBL_LENGTH_MUST_BE_BETWEEN_6_TO_20_CHARACTERS'),
            'invalidValues' => Label::getLabel('VLBL_LENGTH_INVALID_VALUE_FOR'),
            'shouldNotBeSameAs' => Label::getLabel('VLBL_SHOULD_NOT_BE_SAME_AS'),
            'mustBeSameAs' => Label::getLabel('VLBL_MUST_BE_SAME_AS'),
            'mustBeGreaterOrEqual' => Label::getLabel('VLBL_MUST_BE_GREATER_THAN_OR_EQUAL_TO'),
            'mustBeGreaterThan' => Label::getLabel('VLBL_MUST_BE_GREATER_THAN'),
            'mustBeLessOrEqual' => Label::getLabel('VLBL_MUST_BE_LESS_THAN_OR_EQUAL_TO'),
            'mustBeLessThan' => Label::getLabel('VLBL_MUST_BE_LESS_THAN'),
            'mustBeBetween' => Label::getLabel('VLBL_MUST_BE_BETWEEN'),
            'pleaseSelect' => Label::getLabel('VLBL_PLEASE_SELECT'),
            'lengthOf' => Label::getLabel('VLBL_LENGTH_OF'),
            'valueOf' => Label::getLabel('VLBL_VALUE_OF'),
            'and' => Label::getLabel('VLBL_AND'),
            'Quit' => Label::getLabel('LBL_QUIT'),
            'Proceed' => Label::getLabel('LBL_PROCEED'),
            'Confirm' => Label::getLabel('LBL_CONFIRM'),
            'language' => Label::getLabel('Lbl_Language'),
            'timezoneString' => Label::getLabel('LBL_TIMEZONE_STRING'),
            'myTimeZoneLabel' => Label::getLabel('LBL_MY_CURRENT_TIME'),
            'requriedRescheduleMesssage' => Label::getLabel('LBL_RESCHEDULE_REASON_IS_REQURIED'),
            'gdprDeleteAccDesc' => Label::getLabel('LBL_GDPR_DELETE_ACCOUNT_REQUEST_DESCRIPTION'),
            'LessonTitle' => Label::getLabel('LBL_Lesson_Title'),
            'LessonStartTime' => Label::getLabel('LBL_Lesson_Start_Time'),
            'today' => Label::getLabel('LBL_Today'),
            'prev' => Label::getLabel('LBL_Prev'),
            'next' => Label::getLabel('LBL_Next'),
            'done' => Label::getLabel('LBL_Done'),
            'confirmActivate' => Label::getLabel('LBL_ARE_YOU_SURE_YOU_WANT_TO_ACTIVATE'),
            'invalidRequest' => Label::getLabel('LBL_INVALID_REQUEST'),
            'delete' => Label::getLabel('LBL_DELETE'),
            'addClass' => Label::getLabel('LBL_ADD_CLASS'),
            'lessonNotAvailable' => Label::getLabel('LBL_LESSON_NOT_AVAILABLE'),
            'currencyLeft' => self::getCurrencyLeftSymbol(),
            'currencyRight' => self::getCurrencyRightSymbol(),
            'courseSrchPlaceholder' => Label::getLabel('LBL_BY_COURSE_NAME,_TEACHER_NAME,_TAGS'),
            'confirmRetake' => Label::getLabel('LBL_IF_YOU_RETAKE,_THE_EXISTING_PROGRESS_WILL_BE_RESET._CONTINUE?'),
            'courseProgressPercent' => Label::getLabel('LBL_{percent}%_COMPLETED'),
            'confirmCourseSubmission' => Label::getLabel('LBL_PLEASE_CONFIRM_YOU_WANT_TO_SUBMIT_COURSE_FOR_APPROVAL?'),
            'searching' => Label::getLabel('LBL_Searching'),
        ];
        foreach ($siteLanguages as $val) {
            $jsVariables['language' . $val['language_id']] = $val['language_direction'];
        }
        return $jsVariables;
    }

    /**
     * Get User IP
     * 
     * @return string
     */
    public static function getUserIp(): string
    {
        if (!empty(self::$userIp)) {
            return self::$userIp;
        }
        if (getenv('HTTP_CLIENT_IP')) {
            self::$userIp = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            self::$userIp = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            self::$userIp = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            self::$userIp = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            self::$userIp = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            self::$userIp = getenv('REMOTE_ADDR');
        } else {
            self::$userIp = 'UNKNOWN';
        }
        return self::$userIp;
    }

    /**
     * Set Cookie
     * 
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @return bool
     */
    public static function setCookie(string $name, string $value, int $expires = 604800, string $path = CONF_WEBROOT_FRONT_URL, bool $httponly = true): bool
    {
        $secure = (bool) FatApp::getConfig('CONF_USE_SSL');
        return setCookie($name, $value, [
            'path' => $path,
            'httponly' => $httponly,
            'secure' => $secure,
            'expires' => time() + $expires,
            'domain' => $_SERVER['HTTP_HOST'],
            'samesite' => $secure ? 'none' : '',
        ]);
    }

    /**
     * Make URL
     * 
     * @param string $controller
     * @param string $action
     * @param array $queryData
     * @param string $root
     * @return string
     */
// public static function makeUrl($controller = '', $action = '', $queryData = [], $root = ''): string
// {
//     $controllerLower = strtolower((string) $controller);

//     /* 1️⃣ Technical controllers – early return, no SEO, no special roots */
//     if (in_array($controllerLower, ['image', 'js-css', 'jscss'], true)) {
//         // Root = '' lets .htaccess + index.php handle routing as before
//         return FatUtility::generateUrl($controller, $action, $queryData, '', CONF_URL_REWRITING_ENABLED);
//     }

//     /* 2️⃣ Decide base root by area (front / admin / dashboard) */
//     if ($root === '' || $root === null) {
//         if (defined('SYSTEM_ADMIN')) {
//             $root = CONF_WEBROOT_BACKEND;          // admin
//         } elseif (strpos($_SERVER['REQUEST_URI'] ?? '', '/dashboard/') !== false) {
//             $root = CONF_WEBROOT_DASHBOARD;        // learner/teacher dashboard
//         } elseif (defined('SYSTEM_FRONT')) {
//             $root = CONF_WEBROOT_FRONT_URL;        // public site
//         } else {
//             $root = CONF_WEBROOT_URL;              // fallback
//         }
//     }

//     // Raw URL from framework
//     $url = FatUtility::generateUrl($controller, $action, $queryData, $root, CONF_URL_REWRITING_ENABLED);

//     /* 3️⃣ FRONT + SEO HANDLING (for normal content controllers only) */

//     $isFront = ($root === CONF_WEBROOT_FRONT_URL);

//     // Controllers that must NEVER be SEO-rewritten
//     $skipSeo = in_array($controller, SeoUrl::staticControllers(), true);

//     if ($isFront && !$skipSeo) {
//         $langCode = '';
//         if (CONF_LANGCODE_URL && CONF_DEFAULT_LANG != self::$siteLangId) {
//             $langCode = '/' . Language::getCodes(self::$siteLangId);
//         }

//         // Normalize $url to a path (strip scheme/host if somehow present)
//         if (preg_match('#^https?://#i', $url)) {
//             $parsed = parse_url($url);
//             if (!empty($parsed['path'])) {
//                 $url = $parsed['path'];
//             }
//         }

//         // Look up SEO row based on the *path* only
//         $row = SeoUrl::getCustomUrl(self::$siteLangId, ltrim($url, '/'));

//         if (!empty($row['seourl_custom'])) {
//             $custom = $row['seourl_custom'];

//             // If someone accidentally saved a full URL in seourl_custom, strip host
//             if (preg_match('#^https?://#i', $custom)) {
//                 $parsedCustom = parse_url($custom);
//                 if (!empty($parsedCustom['path'])) {
//                     $custom = $parsedCustom['path'];
//                 }
//             }

//             // Always treat SEO value as a slug/path
//             $url = '/' . ltrim($custom, '/');
//         }

//         return urldecode($langCode . $url);
//     }

//     // Admin / dashboard / static controllers just use raw URL
//     return $url;
// }


public static function makeUrl($controller = '', $action = '', $queryData = [], $root = ''): string
{
    $controller = (string)$controller;
    $controllerLower = strtolower($controller);

    /**
     * 1️⃣ Technical controllers (image, js-css) → always use FRONT root,
     *    never /dashboard or /admin.
     */
    if (in_array($controllerLower, ['image', 'js-css', 'jscss'], true)) {
        // If caller passed a definitely-wrong root (dashboard/backend) or nothing,
        // override with front root which is where Image controller is mounted.
        if (
            $root === '' || $root === null ||
            (defined('CONF_WEBROOT_DASHBOARD') && $root === CONF_WEBROOT_DASHBOARD) ||
            (defined('CONF_WEBROOT_BACKEND')   && $root === CONF_WEBROOT_BACKEND)
        ) {
            $root = defined('CONF_WEBROOT_FRONT_URL') ? CONF_WEBROOT_FRONT_URL : CONF_WEBROOT_URL;
        }

        return FatUtility::generateUrl($controller, $action, $queryData, $root, CONF_URL_REWRITING_ENABLED);
    }

    /**
     * 2️⃣ Decide base root (front / admin / dashboard) for normal controllers
     */
    if ($root === '' || $root === null) {
        if (defined('SYSTEM_ADMIN')) {
            $root = CONF_WEBROOT_BACKEND;          // admin
        } elseif (defined('CONF_WEBROOT_DASHBOARD') &&
                  strpos($_SERVER['REQUEST_URI'] ?? '', '/dashboard/') !== false) {
            $root = CONF_WEBROOT_DASHBOARD;        // learner/teacher dashboard
        } elseif (defined('SYSTEM_FRONT')) {
            $root = CONF_WEBROOT_FRONT_URL;        // public site
        } else {
            $root = CONF_WEBROOT_URL;              // safe fallback
        }
    }

    // Raw path from framework
    $url = FatUtility::generateUrl($controller, $action, $queryData, $root, CONF_URL_REWRITING_ENABLED);

    /**
     * 3️⃣ FRONT + SEO handling (only for real front controllers, not image/js-css)
     */
    $isFront = ($root === CONF_WEBROOT_FRONT_URL);
    $skipSeo = in_array($controller, SeoUrl::staticControllers(), true);

    if ($isFront && !$skipSeo) {
        $langCode = '';
        if (CONF_LANGCODE_URL && CONF_DEFAULT_LANG != self::$siteLangId) {
            $langCode = '/' . Language::getCodes(self::$siteLangId);
        }

        // Normalize $url to a path
        if (preg_match('#^https?://#i', $url)) {
            $parsed = parse_url($url);
            if (!empty($parsed['path'])) {
                $url = $parsed['path'];
            }
        }

        // Look up SEO row based on the plain path
        $row = SeoUrl::getCustomUrl(self::$siteLangId, ltrim($url, '/'));

        if (!empty($row['seourl_custom'])) {
            $custom = $row['seourl_custom'];

            // If a full URL was stored by mistake, drop scheme+host
            if (preg_match('#^https?://#i', $custom)) {
                $parsedCustom = parse_url($custom);
                if (!empty($parsedCustom['path'])) {
                    $custom = $parsedCustom['path'];
                }
            }

            $url = '/' . ltrim($custom, '/');
        }

        return urldecode($langCode . $url);
    }

    // Admin / dashboard / static controllers
    return $url;
}



    /**
     * Make Full URL
     * 
     * @param string $controller
     * @param string $action
     * @param array $queryData
     * @param string $rootUrl
     * @return string
     */
    // public static function makeFullUrl($controller = '', $action = '', $queryData = [], $rootUrl = '')
    // {
    //     $url = static::generateUrl($controller, $action, $queryData, $rootUrl, CONF_URL_REWRITING_ENABLED);
    //     $protocol = (FatApp::getConfig('CONF_USE_SSL')) ? 'https://' : 'http://';
    //     return $protocol . $_SERVER['SERVER_NAME'] . urldecode($url);
    // }

// public static function makeFullUrl($controller = '', $action = '', $queryData = [], $rootUrl = '')
// {
//     // For full URLs we do NOT need SEO decoration – just the framework path.
//     $path = FatUtility::generateUrl($controller, $action, $queryData, $rootUrl, CONF_URL_REWRITING_ENABLED);

//     // If already absolute, return as-is
//     if (preg_match('#^https?://#i', $path)) {
//         return $path;
//     }

//     $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || FatApp::getConfig('CONF_USE_SSL');
//     $scheme = $https ? 'https' : 'http';
//     $host   = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

//     // Ensure leading slash
//     $path = '/' . ltrim($path, '/');

//     return $scheme . '://' . $host . urldecode($path);
// }
public static function makeFullUrl($controller = '', $action = '', $queryData = [], $rootUrl = '')
{
    $controllerLower = strtolower((string)$controller);

    // For technical controllers, force front root (no /dashboard, no /admin)
    if (in_array($controllerLower, ['image', 'js-css', 'jscss'], true)) {
        if (
            $rootUrl === '' || $rootUrl === null ||
            (defined('CONF_WEBROOT_DASHBOARD') && $rootUrl === CONF_WEBROOT_DASHBOARD) ||
            (defined('CONF_WEBROOT_BACKEND')   && $rootUrl === CONF_WEBROOT_BACKEND)
        ) {
            $rootUrl = defined('CONF_WEBROOT_FRONT_URL') ? CONF_WEBROOT_FRONT_URL : CONF_WEBROOT_URL;
        }
    }

    // Just get the raw path from framework
    $path = FatUtility::generateUrl($controller, $action, $queryData, $rootUrl, CONF_URL_REWRITING_ENABLED);

    // If it's already absolute, return it
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || FatApp::getConfig('CONF_USE_SSL');
    $scheme = $https ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

    // Ensure leading slash
    $path = '/' . ltrim($path, '/');

    return $scheme . '://' . $host . urldecode($path);
}


    /**
     * Format money
     * 
     * @param float $value
     * @return string
     */
    public static function formatMoney($value, bool $addsymbol = true): string
    {
        $value = static::convertToSiteCurrency(static::float($value));
        if (!$addsymbol) {
            return $value;
        }
        $sign = ($value < 0) ? '-' : '';
        $value = round(abs($value), 2);
        $value = number_format($value, 2);
        $left = self::$siteCurrency['currency_symbol_left'];
        $right = self::$siteCurrency['currency_symbol_right'];
        return str_replace(" ", "&nbsp;", $sign . $left . $value . $right);
    }

    /**
     * Convert To System Currency
     * 
     * @param float $value
     * @return float
     */
    public static function convertToSystemCurrency(float $value): float
    {
        $value = static::float($value);
        return static::float($value / static::float(self::$siteCurrency['currency_value']));
    }

    /**
     * Convert To Site Currency
     * 
     * @param float $value
     * @return float
     */
    public static function convertToSiteCurrency(float $value): float
    {
        return static::float($value) * static::float(static::$siteCurrency['currency_value']);
    }

    /**
     * Format Percent
     * 
     * @param float $value
     * @return string
     */
    public static function formatPercent(float $value): string
    {
        return $value . '%';
    }

    /**
     * Get Currency Disclaimer
     * 
     * @param float $amount
     * @return string
     */
    public static function getCurrencyDisclaimer(float $amount): string
    {
        $str = Label::getLabel('LBL_*_Note_:_charged_in_currency_disclaimer_{default-currency-symbol}');
        if ($amount) {
            $str = str_replace('{default-currency-symbol}', MyUtility::formatMoney($amount), $str);
        } else {
            $str = str_replace('{default-currency-symbol}', ' $ ', $str);
        }
        return $str;
    }

    /**
     * Get Active Slots
     * 
     * @return array
     */
    public static function getActiveSlots(): array
    {
        $defaultSlot = FatApp::getConfig('CONF_DEFAULT_PAID_LESSON_DURATION');
        $slots = FatApp::getConfig('CONF_PAID_LESSON_DURATION', FatUtility::VAR_STRING, $defaultSlot);
        return explode(',', $slots);
    }

    /**
     * Validate YouTube URL
     * 
     * @param string $link
     * @return string
     */
    public static function validateYoutubeUrl($link): string
    {
        if (empty($link)) {
            return '';
        }
        $pattern = "#" . AppConstant::INTRODUCTION_VIDEO_LINK_REGEX . "#";
        if (!preg_match($pattern, $link, $matches)) {
            return '';
        }
        if (empty($matches[1])) {
            $link = "//" . $link;
        }
        return $link;
    }

    /**
     * Mask and Disable Form Fields
     * 
     * @param Form $frm
     * @param array $fieldsToSkip
     */
    public static function maskAndDisableFormFields(Form $frm, array $fieldsToSkip)
    {
        $flds = $frm->getAllFields();
        foreach ($flds as $fld) {
            if (!in_array($fld->getName(), $fieldsToSkip) && ('submit' != $fld->fldType)) {
                $fld->addFieldTagAttribute('disabled', 'disabled');
            }
            if (!in_array($fld->getName(), $fieldsToSkip) && 'text' == $fld->fldType || $fld->fldType == "textarea") {
                $fld->value = '***********';
            }
        }
        $frm->addHTML(Label::getLabel('LBL_Note'), 'note', '<span class="spn_must_field">' . Label::getLabel('NOTE_SETTINGS_NOT_ALLOWED_TO_BE_MODIFIED_ON_DEMO_VERSION') . '</span>')->setWrapperAttribute('class', 'text--center');
    }

    /**
     * Is Demo URL
     * 
     * @return bool
     */
    public static function isDemoUrl(): bool
    {
        return (strtolower($_SERVER['SERVER_NAME']) === 'teach.yo-coach.com');
    }

    /**
     * Format Time Slot Array
     * 
     * @param array $arr
     * @return array
     */
    public static function formatTimeSlotArr(array $arr): array
    {
        $timeSlotArr = array_intersect_key(static::timeSlots(), array_flip($arr));
        $formattedArr = [];
        foreach ($timeSlotArr as $k => $timeSlot) {
            $breakTimeStrng = explode('-', $timeSlot);
            $formattedArr[$k]['startTime'] = $breakTimeStrng[0];
            $formattedArr[$k]['endTime'] = $breakTimeStrng[1];
        }
        return array_values($formattedArr);
    }

    /**
     * Validate Password
     * 
     * @param string $string
     * @return bool
     */
    public static function validatePassword(string $string = ''): bool
    {
        if (strlen($string) < 1) {
            return false;
        }
        if (!preg_match('/' . AppConstant::PASSWORD_REGEX . '/', $string)) {
            return false;
        }
        return true;
    }

    /**
     * Time Slots
     * 
     * @return array
     */
    public static function timeSlots(): array
    {
        return [
            0 => '00:00 - 04:00',
            1 => '04:00 - 08:00',
            2 => '08:00 - 12:00',
            3 => '12:00 - 16:00',
            4 => '16:00 - 20:00',
            5 => '20:00 - 24:00',
        ];
    }

    /**
     * Time Slot Array
     * 
     * @return array
     */
    public static function timeSlotArr(): array
    {
        return [
            0 => '00 - 04',
            1 => '04 - 08',
            2 => '08 - 12',
            3 => '12 - 16',
            4 => '16 - 20',
            5 => '20 - 24',
        ];
    }

    /**
     * Write File
     * 
     * @param string $name
     * @param type $data
     * @param type $response
     * @return bool
     */
    public static function writeFile(string $name, $data, &$response): bool
    {
        $fName = CONF_UPLOADS_PATH . preg_replace('/[^a-zA-Z0-9\/\-\_\.]/', '', $name);
        $dest = dirname($fName);
        if (!file_exists($dest)) {
            mkdir($dest, 0777, true);
        }
        $file = fopen($fName, 'w');
        if (!fwrite($file, $data)) {
            $response = Label::getLabel('MSG_Could_not_save_file.');
            return false;
        }
        fclose($file);
        $response = $fName;
        return true;
    }

    /**
     * Convert Bites To MBs
     * 
     * @param float $size
     * @return string
     */
    public static function convertBitesToMb(float $size): string
    {
        return number_format($size / 1048576, 2);
    }

    /**
     * Get News Letter Form
     * 
     * @return Form
     */
    public static function getNewsLetterForm()
    {
        $frm = new Form('frmNewsLetter');
        $fld1 = $frm->addEmailField('', 'email');
        $fld1->requirements()->setRequired();
        $fld1->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_WITH_NONE);
        $frm->addSubmitButton('', 'btnSubmit', Label::getLabel('LBL_SUBSCRIBE'));
        return $frm;
    }

    /**
     * Create Slug
     * 
     * @param string $title
     * @return string
     */
    public static function createSlug(string $title): string
    {
        $slug = preg_replace("/[^0-9a-zA-Z]/", "-", $title);
        return self::removeHyphens($slug);
    }

    /**
     * Remove Hyphens
     * 
     * @param string $slug
     * @return string
     */
    private static function removeHyphens(string $slug): string
    {
        $slug = str_replace('--', '-', $slug);
        if (strpos($slug, '--') !== false) {
            $slug = self::removeHyphens($slug);
        }
        return trim($slug, "-");
    }

}
