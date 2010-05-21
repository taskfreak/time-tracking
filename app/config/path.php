<?php

// === PHP CLASS and SCRIPTS paths ============================================

$autoPath['class'] = array(
	APP_CLASS_PATH
);
$autoPath['helper'] = array(
	APP_HELPER_PATH
);
$autoPath['model'] = array(
	APP_CORE_PATH.'model/',
	APP_LIB_PATH.'model/'
);
$autoPath['controller'] = array(
	APP_CORE_PATH.'controller/',
	APP_CLASS_PATH
);
$autoPath['view'] = array(
	APP_CORE_PATH.'view/'	
);

// -TODO-
// browse plugin directory to add other classes into the searched path

// === JS and CSS paths =======================================================

$GLOBALS['config']['path']['css'] = array(
	'skin/'.$GLOBALS['config']['skin'].'/css/',
	'asset/css/'
);

$GLOBALS['config']['path']['js'] = array(
	$GLOBALS['config']['skin'].'/js/',
	'asset/js/'
);