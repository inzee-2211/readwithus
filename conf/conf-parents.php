<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'conf-common.php';
define('CONF_APPLICATION_PATH', CONF_INSTALLATION_PATH . 'parents/');
define('CONF_THEME_PATH', CONF_APPLICATION_PATH . 'views/');
define('CONF_WEBROOT_URL', CONF_WEBROOT_PARENTS);
define('CONF_WEBROOT_FRONT_URL', CONF_WEBROOT_FRONTEND);
define('CONF_WEBROOT_URL_TRADITIONAL', CONF_WEBROOT_FRONTEND . 'public/parents.php?url=');
define('SYSTEM_FRONT', true);
define('CONF_HTML_EDITOR', 'innova');
define('CONF_FAT_CACHE_DIR', CONF_INSTALLATION_PATH . 'public/cache/');
define('CONF_FAT_CACHE_URL', CONF_WEBROOT_FRONTEND . 'cache/');
if (!defined('CONF_STATIC_FILE_CONTROLLERS')) {
    define('CONF_STATIC_FILE_CONTROLLERS', []);   // ✅ ARRAY, not serialized string
}