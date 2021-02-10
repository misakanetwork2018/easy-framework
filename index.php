<?php

define('APP_ROOT', __DIR__);

require_once APP_ROOT . "/include/App.php";

$app = App::init();

$app->run();
