<?php

// ---- LOG and DEBUGGING -----------------------------------------------------

$GLOBALS['config']['log_front'] = 0;
$GLOBALS['config']['log_debug'] = 1;
$GLOBALS['config']['log_message'] = 0;
$GLOBALS['config']['log_warn'] = 0;
$GLOBALS['config']['log_error'] = 2;
$GLOBALS['config']['log_core'] = 0;

$GLOBALS['config']['log_signature'] = '[TF]';

// --- APPLICATION CONTOLLER, ACTION and PAGES---------------------------------

$GLOBALS['config']['app'] = array(
	'default_controller'	=> 'task',
		// default controller to call (home page)
	'default_action'		=> 'main'
		// default action to call (home page)
);

$GLOBALS['config']['pages'] = array(
	'Todo'		=> 'task/main',
	'Report'	=> 'task/report',
	'Archives'	=> 'task/archives'
);

// ---- DATE / TIME FORMATS ---------------------------------------------------

// date/time timezone and formats defaults
$GLOBALS['config']['datetime'] = array(
	'timezone_server'	=> new DateTimeZone(APP_TIMEZONE_SERVER),
	'timezone_user'		=> new DateTimeZone(APP_TIMEZONE_SERVER),
	'us_format'			=> false,
);
$GLOBALS['config']['datetime']['now'] = new DateTime('now', $GLOBALS['config']['datetime']['timezone_server']);

// --- Specific DATE FORMATS -------------------------------------------------

define("APP_DATE","%d/%m");
define("APP_DATETIME","%d/%m <small>%H:%M</small>");

// --- TASKFREAK DEFAULTS ---------------------------------------------------------

$GLOBALS['config']['task'] = array(
	'date'		=> 'now', // default date is today
	'validate'	=> true	// add validation button
);

// note for default date :
// can be any PHP valid date eg. +1 days, +1 week, or false for no date

$GLOBALS['config']['task']['priority'] = array(
	'options' => array(
		1 => 'urgent',
		2 => 'important',
		3 => 'quickly',
		4 => 'soon',
		5 => 'normal',
		6 => 'after',
		7 => 'later',
		8 => 'anytime',
		9 => 'whenever'
	),
	'default'	=> 5
);

$GLOBALS['config']['task']['pagination'] = array(15=>15,30=>30,50=>50,'all'=>0);
$GLOBALS['config']['task']['pagination_default'] = 15;

// ---- DEFAULT Javascript ----------------------------------------------------

$GLOBALS['config']['header']['js'] = array(
	'jquery-1.4.2.min.js',
	// 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
);


// ---- SKINS and Templates ---------------------------------------------------

$GLOBALS['config']['skin'] = 'default';

// ---- LANGUAGE --------------------------------------------------------------

$GLOBALS['config']['lang'] = array(
	'default'		=> 'en',
	'user'			=> 'en',
	'specialchars'	=> 2
);

$GLOBALS['config']['lang']['files'] = array(
	'common.php'	=> APP_INCLUDE_PATH.'lib/lang/',
	'freak.php'		=> APP_INCLUDE_PATH.'app/lang/'
);
