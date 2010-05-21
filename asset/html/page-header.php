<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $this->html('title'); ?></title>
<?php
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
?>
<link rel="SHORTCUT ICON" href="/favicon.ico" />
<?php $this->callHelper('html_asset','headerStuff'); ?>
</head>
<body>
