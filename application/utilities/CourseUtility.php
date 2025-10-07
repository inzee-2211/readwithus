<?php

class CourseUtility extends FatUtility
{
    
    /**
     * Format money
     *
     * @param float   $value
     * @param int $currencyId
     * @return string
     */
    public static function formatMoney(float $value, int $currencyId = 0, bool $addsymbol = true): string
    {
        if ($currencyId < 1) {
            $siteCurrency = MyUtility::getSiteCurrency();
        } else {
            $siteCurrency = Currency::getData($currencyId, MyUtility::getSiteLangId(), false);
        }
        if ($value > 0) {
            $value = static::convertToCurrency($value, $siteCurrency['currency_id']);
        }
        if (!$addsymbol) {
            return $value;
        }
        if ($addsymbol) {
            $sign = ($value < 0) ? '-' : '';
            $value = number_format(abs($value), 2);
            $left = $siteCurrency['currency_symbol_left'];
            $right = $siteCurrency['currency_symbol_right'];
            return $sign . $left . $value . $right;
        }
    }

    public static function convertToSystemCurrency(float $value, int $currencyId): float
    {
        $value = static::float($value);
        $currencyValue = Currency::getAttributesById($currencyId, 'currency_value');
        return static::float($value / $currencyValue);
    }

    public static function convertToCurrency(float $value, int $currencyId): float
    {
        $value = static::float($value);
        $currencyValue = Currency::getAttributesById($currencyId, 'currency_value');
        return static::float($value * $currencyValue);
    }
}
