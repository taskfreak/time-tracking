<?php
// load config file
include './app/config/core.php';	// load up core config (paths, etc..)
include APP_CONFIG_PATH.'app.php';	// load up application specific settings (customizable)

// minimum needed classes
include APP_CLASS_PATH.'helpable.php';
include APP_HELPER_PATH.'string.php';
include APP_CLASS_PATH.'front.php';

// start session
session_start();

// initialize front controller
$fc = FrontController::getInstance();

// launch up (app) controller
$fc->run();
