<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'conf-common.php';
define('CONF_APPLICATION_PATH', CONF_INSTALLATION_PATH . CONF_FRONT_END_APPLICATION_DIR);
define('CONF_THEME_PATH', CONF_APPLICATION_PATH . 'views/');
define('CONF_WEBROOT_URL', CONF_WEBROOT_FRONTEND);
define('CONF_WEBROOT_FRONT_URL', CONF_WEBROOT_URL);
define('CONF_WEBROOT_URL_TRADITIONAL', CONF_WEBROOT_URL . 'public/index.php?url=');
define('SYSTEM_FRONT', true);
define('CONF_HTML_EDITOR', 'innova');
define('CONF_FAT_CACHE_DIR', CONF_INSTALLATION_PATH . 'public/cache/');
define('CONF_FAT_CACHE_URL', CONF_WEBROOT_URL . 'cache/');
// Stripe Configuration
// define('CONF_STRIPE_SECRET_KEY', 'sk_test_51SSDb7FP4feQExSlXeRFcSIyotbnP5Z2HU2qkckU5C5JFiXNuiebIpX8Lb7TZzcDnpNngAQBrnzlmOisSTiN5vGw00xh0B9QPj'); // Your test secret key
// define('CONF_STRIPE_PUBLISHABLE_KEY', 'pk_test_51SSDb7FP4feQExSlEm1iSJeLa7Gj8SNGHPuIoEzlzrRgaPWCVbXY4EFX34BcFqpJFFBLmInogIQnhVg2LGfZbXBZ00wzvs6H5m'); // Your test publishable key
// define('CONF_STRIPE_WEBHOOK_SECRET', 'whsec_905d926b1df7aef9b8c74d9312a00df1a5fc2388d6c99dbc39f56e7f0ae39c40'); // Your webhook secret