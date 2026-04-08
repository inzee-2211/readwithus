<?php

class CourseUtility
{
    /**
     * Format money with proper type safety
     *
     * @param mixed $value
     * @param int $currencyId
     * @param bool $addsymbol
     * @return string
     */
    public static function formatMoney($value, int $currencyId = 0, bool $addsymbol = true): string
    {
        try {
            // Type safety - ensure value is float
            $floatValue = self::safeFloat($value);
            
            if ($currencyId < 1) {
                $siteCurrency = MyUtility::getSiteCurrency();
                if (!$siteCurrency) {
                    throw new Exception('Site currency not found');
                }
            } else {
                $siteCurrency = Currency::getData($currencyId, MyUtility::getSiteLangId(), false);
                if (!$siteCurrency) {
                    throw new Exception("Currency with ID {$currencyId} not found");
                }
            }

            if ($floatValue > 0) {
                $floatValue = static::convertToCurrency($floatValue, $siteCurrency['currency_id']);
            }

            if (!$addsymbol) {
                return number_format($floatValue, 2, '.', '');
            }

            $sign = ($floatValue < 0) ? '-' : '';
            $formattedValue = number_format(abs($floatValue), 2);
            $left = $siteCurrency['currency_symbol_left'] ?? '';
            $right = $siteCurrency['currency_symbol_right'] ?? '';
            
            return $sign . $left . $formattedValue . $right;

        } catch (Exception $e) {
            // Log error and return safe default
            error_log("CourseUtility::formatMoney error: " . $e->getMessage());
            return '0.00';
        }
    }

    /**
     * Safely convert any value to float
     *
     * @param mixed $value
     * @return float
     */
    private static function safeFloat($value): float
    {
        if (is_null($value)) {
            return 0.0;
        }
        
        if (is_float($value) || is_int($value)) {
            return (float)$value;
        }
        
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return 0.0;
            }
            // Remove any currency symbols and thousands separators
            $value = preg_replace('/[^\d.-]/', '', $value);
        }
        
        $floatValue = floatval($value);
        return is_finite($floatValue) ? $floatValue : 0.0;
    }

    /**
     * Convert to system currency with validation
     *
     * @param mixed $value
     * @param int $currencyId
     * @return float
     */
    public static function convertToSystemCurrency($value, int $currencyId): float
    {
        try {
            $floatValue = self::safeFloat($value);
            
            if ($currencyId < 1) {
                throw new Exception('Invalid currency ID');
            }
            
            $currencyValue = Currency::getAttributesById($currencyId, 'currency_value');
            if (!$currencyValue || $currencyValue <= 0) {
                throw new Exception("Invalid currency value for ID: {$currencyId}");
            }
            
            return $floatValue / $currencyValue;
            
        } catch (Exception $e) {
            error_log("CourseUtility::convertToSystemCurrency error: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Convert to currency with validation
     *
     * @param mixed $value
     * @param int $currencyId
     * @return float
     */
    public static function convertToCurrency($value, int $currencyId): float
    {
        try {
            $floatValue = self::safeFloat($value);
            
            if ($currencyId < 1) {
                throw new Exception('Invalid currency ID');
            }
            
            $currencyValue = Currency::getAttributesById($currencyId, 'currency_value');
            if (!$currencyValue || $currencyValue <= 0) {
                throw new Exception("Invalid currency value for ID: {$currencyId}");
            }
            
            return $floatValue * $currencyValue;
            
        } catch (Exception $e) {
            error_log("CourseUtility::convertToCurrency error: " . $e->getMessage());
            return 0.0;
        }
    }
}