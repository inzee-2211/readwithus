<?php
/* ====== Load .env file manually ====== */
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue; // skip comments
        [$name, $value] = array_map('trim', explode('=', $line, 2));
        if (!getenv($name)) putenv("$name=$value");
    }
}

 
require_once dirname(__DIR__) . '/conf/conf.php';
require_once dirname(__FILE__) . '/application-top.php';

FatApp::unregisterGlobals();
if (file_exists(CONF_APPLICATION_PATH . 'utilities/prehook.php')) {
    require_once CONF_APPLICATION_PATH . 'utilities/prehook.php';
}
 
FatApplication::getInstance()->callHook();
 
 