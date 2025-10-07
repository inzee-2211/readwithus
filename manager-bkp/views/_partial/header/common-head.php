<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<!doctype html>
<html class="<?php echo MyUtility::isDemoUrl() ? 'sticky-demo-header' : '' ?>">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_FAVICON, 0, Afile::SIZE_ORIGINAL], CONF_WEBROOT_FRONTEND); ?>">
        <link rel="apple-touch-icon" href="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_APPLE_TOUCH_ICON, 0, Afile::SIZE_LARGE], CONF_WEBROOT_FRONTEND); ?>">
        <?php
        if (isset($includeEditor) && $includeEditor == true) {
            $extendEditorJs = 'true';
        } else {
            $extendEditorJs = 'false';
        }
        $str = '<script type="text/javascript">
		var SITE_URL = "' . MyUtility::makeFullUrl('', '', [], CONF_WEBROOT_FRONTEND) . '" ;
		var SITE_ROOT_URL = "' . CONF_WEBROOT_URL . '" ;
		var SITE_ROOT_DASHBOARD_URL = "' . CONF_WEBROOT_DASHBOARD . '" ;
		var SITE_ROOT_FRONT_URL = "' . CONF_WEBROOT_FRONTEND . '" ;
		var langLbl = ' . json_encode(CommonHelper::htmlEntitiesDecode($jsVariables)) . ';
		var layoutDirection ="' . $layoutDirection . '";
		var CONF_AUTO_CLOSE_ALERT_TIME = ' . FatApp::getConfig("CONF_AUTO_CLOSE_ALERT_TIME", FatUtility::VAR_INT, 3) . ';
		var extendEditorJs = ' . $extendEditorJs . ';';
        /**
         * var monthNames, weekDayNames used in the fullcalendar-luxon.min.js file  
         */
        $str .= ' var monthNames =  ' . json_encode(CommonHelper::htmlEntitiesDecode(MyDate::getAllMonthName(false, $siteLangId))) . ';
                  var weekDayNames =  ' . json_encode(CommonHelper::htmlEntitiesDecode(MyDate::dayNames(false, $siteLangId))) . ';';
        $str .= '</script>' . "\r\n";
        echo $str;
        ?>
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,500i,700,700i,900,900i" rel="stylesheet">