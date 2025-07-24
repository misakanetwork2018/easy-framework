<?php

use EasyFrameworkCore\App;

const APP_ROOT = __DIR__;

require_once APP_ROOT . "/core/App.php";

App::bindVendorNamespace("App", "modules");

$app = App::init();

$app->run();
