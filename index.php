<?php /** @noinspection PhpUnhandledExceptionInspection */

use EasyFrameworkCore\App;

const APP_ROOT = __DIR__;

require_once APP_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "App.php";

App::bindVendorNamespace("App", "modules");
App::bindVendorNamespace("Middlewares", "middlewares");

$app = App::init();

$app->run();
