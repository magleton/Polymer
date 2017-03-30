<?php
date_default_timezone_set('Asia/Shanghai');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
define('ROOT_PATH', dirname(__DIR__));
define('APP_NAME', 'tests');
define('APP_PATH', __DIR__);
require ROOT_PATH . '/vendor/autoload.php';
$app = new \Polymer\Boot\Application();
$app->startConsole();