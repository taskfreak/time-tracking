<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<?php
/*
<link rel="apple-touch-icon-precomposed" href="<?php echo APP_WWW_URI.'skin/'.$GLOBALS['config']['skin'].'/iphone_icon.png'; ?>"/>
<link rel="apple-touch-startup-image" href="<?php echo APP_WWW_URI.'skin/'.$GLOBALS['config']['skin'].'/iphone_startup.png'; ?>" />
*/
?>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $this->html('title'); ?></title>
<?php
/*
if (!$this->isEmpty('description')) { 
?>
<meta name="description" content="<?php echo $this->html('description'); ?>" />
<?php
}

if (!$this->isEmpty('keywords')) { 
?>
<meta name="keywords" content="<?php echo $this->html('keywords'); ?>" />
<?php
}
*/

$this->callHelper('html_asset','headerStuff', true); 
?>
</head>