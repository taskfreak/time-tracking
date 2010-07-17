<?php
error_reporting(E_ALL);

// --- APPLICATION SETUP ------------------------------------------------------

// connect to database (see /app/config/db.php for settings)
define('APP_SETUP_DATABASE', true);

// load global settings on startup (requires database)
define('APP_SETUP_GLOBAL_SETTINGS', false);

// load user settings on startup (requires database and auth)
define('APP_SETUP_USER_SETTINGS', false);

// use translation helper
define('APP_SETUP_TRANSLATOR', true);

// use (trans-sessions) messaging helper
define('APP_SETUP_MESSAGING', true);

// use navi helper
define('APP_SETUP_NAVI', true);

$GLOBALS['config'] = array();

// --- USER AUTHENTICATION SETUP ----------------------------------------------

// model class used for app users
// (will load logged in user if not false)
// see APP_AUTH_* below for other settings
define('APP_SETUP_USER_MODEL','member');

// field used for authentication (usually username or email)
define('APP_AUTH_FIELD', 'username');

// password encryption
// 1: php crypt, 2:mySQL ENCRYPT, 3: mysql ENCODE, 4: MD5, 5: MD5 with challenge
define('APP_AUTH_PASSWORD_MODE', 4);

// USER AUTHENTICATION DEFAULTS
// following settings can be modified by global settings

$GLOBALS['config']['auth'] = array(
	'remember'			=> true,
		// allow auto login (true or false)
	'password_recover'	=> true,
		// allow password recovery (true or false)
	'register'			=> 0
		// register mode
		// 0: no registration, 1: account created directly (no validation)
		// 2: user validation (from email), 3: admin validate account
);

// ---- FILE PATHs ------------------------------------------------------------

define('APP_ROOT_PATH',substr(dirname(__FILE__),0,-10));

define('APP_WWW_PATH',APP_ROOT_PATH);

define('APP_CORE_PATH',APP_ROOT_PATH.'app/');
define('APP_CONFIG_PATH',APP_CORE_PATH.'config/');
define('APP_MODEL_PATH',APP_CORE_PATH.'model/');
define('APP_CONTROLLER_PATH',APP_CORE_PATH.'controller/');
define('APP_VIEW_PATH',APP_CORE_PATH.'view/');
define('APP_CACHE_PATH',APP_ROOT_PATH.'cache/');

define('APP_INCLUDE_PATH', APP_ROOT_PATH);
define('APP_LIB_PATH',APP_INCLUDE_PATH.'lib/');
define('APP_CLASS_PATH',APP_LIB_PATH.'class/');
define('APP_HELPER_PATH',APP_LIB_PATH.'helper/');
define('APP_LANGUAGE_PATH',APP_LIB_PATH.'lang/');

define('APP_ASSET_PATH',APP_ROOT_PATH.'asset/');
define('APP_PLUGIN_PATH',APP_ROOT_PATH.'plugin/');
define('APP_SKIN_PATH',APP_ROOT_PATH.'skin/');

// ---- APPLICATION URLs ------------------------------------------------------

define('APP_URL_REWRITE', false);

$uri = trim(substr(APP_WWW_PATH,strlen($_SERVER['DOCUMENT_ROOT'])),'/');
if (empty($uri)) {
	$uri = '/';
} else {
	$uri = '/'.$uri.'/';
}
define('APP_WWW_URI', $uri);
unset($uri);

define('APP_WWW_URL','http://'.$_SERVER['SERVER_NAME'].APP_WWW_URI);

// ---- USER and PASSWORD SETTINGS --------------------------------------------

define('APP_USER_ID_LENGTH',8);		// length of user ID
define('APP_USER_SANITIZE','/^[a-z0-9_-]+$/i');
define('APP_USER_NAME_MIN',4);		// minimum length for username
define('APP_USER_NAME_MAX',16);		// maximum length for username
define('APP_USER_PASS_MIN',4);		// minimum length for password
define('APP_USER_PASS_MAX',16);		// maximum length for password
define('APP_PASSWORD_SANITIZE','/^[a-z0-9%!@#&*+:;,<>\.\?\|\{\}\(\)\[\]\$_-]+$/i');

// ---- FILE UPLOAD Settings --------------------------------------------------

define('APP_FILE_SLASH','/');
define('APP_FILE_RANDOM',false);
define('APP_FILE_FOLDER_SIZE',300); // number of files per folder
define('APP_FILE_GD_VERSION',2);
define('APP_FILE_GD_QUALITY',80);

// ---- Default DATE/TIME FORMATs ---------------------------------------------

define("APP_DATE_SQL","%Y-%m-%d");
define('APP_DATE_EUR', '%d/%m/%y');
define('APP_DATE_USA', '%m/%d/%y');
define("APP_DATE_SHT","%d %b %y"); // -TODO- %e bug
define("APP_DATE_SHX","%a %e %b %y");
define("APP_DATE_LNG","%d %B %Y");
define("APP_DATE_LNX","%A %d %B %Y");
define("APP_DATETIME_SQL","%Y-%m-%d %H:%M:%S");
define("APP_DATETIME_EUR","%d/%m/%y %H:%M");
define("APP_DATETIME_USA","%m/%d/%y %I:%M%p");
define("APP_DATETIME_SHT","%d %b %y, %H:%M");
define("APP_DATETIME_SHX","%a %d %b %y, %H:%M");
define("APP_DATETIME_LNG","%d %B %Y, %H:%M");
define("APP_DATETIME_LNX","%A %d %B %Y, %H:%M");
define("APP_DATETIME_HRS","%H:%M");

define('APP_DATE_RFC822', '%a, %d %b %Y %H:%M:%S %z');

// date_default_timezone_set('Europe/Paris');
define('APP_TIMEZONE_SERVER',date_default_timezone_get());
define('APP_TZSERVER',intval(date('Z')));
// define('APP_TZSERVER',timezone_offset_get(new DateTimeZone(APP_TIMEZONE_SERVER), new DateTime()));

define('APP_SQL_NOW', gmdate('Y-m-d H:i:s'));
define('APP_SQL_TODAY',substr(APP_SQL_NOW,0,10));
define('APP_NOW',strtotime(APP_SQL_NOW));
define('APP_TODAY',strtotime(APP_SQL_TODAY));
define('APP_YEAR',substr(APP_SQL_TODAY,0,4));

// ---- MISC MODEL PROPERTIES Settings ----------------------------------------

define('APP_KEY_LENGTH',8);
define('APP_KEY_STRING','ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

define('APP_SANITIZE_SIMPLE','/^[a-z0-9_-]+$/i');


// ----- PHP SESSION Settings -------------------------------------------------

define('TZN_TRANS_ID',0);
define('TZN_TRANS_STATUS',1);
if (@constant('APP_TRANS_ID')) {
	ini_set("session.use_trans_sid",1);
} else {
	ini_set('session.use_trans_sid', 0);
	ini_set('session.use_only_cookies', 1);
}

// ---- AGENT DETECTION -------------------------------------------------------

define('APP_ROBOT_AGENT', 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|AcioRobot|ASPSeek|'
    		.'CocoCrawler|Dumbot|FAST\-WebCrawler|GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|'
    		.'AltaVista|IDBot|eStyle|Scrubby');
    		
define('APP_IPHONE_AGENT', 'iPhone|iPod|iPad');