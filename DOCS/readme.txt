Requirements
------------
- apache with mod_rewrite
- PHP 5.3.x
- mySQL 4.1 or later


Setup
-----
1) Set up your virtual host (read further below if using a subfolder instead)
2) create a mySQL database
3) create tables and data with taskfreak_time.sql
4) open app/config/db.php and change database settings

You can stop here and have a look.
login is "admin" with no password


Subfolder setup
---------------
If not using a virtual host, you need to change the APP_WWW_URI constant
in app/config/core.php

eg. if running under yourdomain.tld/taskfreak
define('APP_WWW_URI', '/taskfreak/');
don't forget the trailing slash


Single user
-----------
If you don't want multi user support, open app/config/core.php
Search for APP_SETUP_USER_MODEL and set it up to false :
[code]
define('APP_SETUP_USER_MODEL',false);
[/code]

If you feel like playing so more, open app/config/app.php and look into it


Known bugs
----------
- javascript calendar (jdpicker) closes when changing month or year
- order by start, stop, or spent doesn't really make sense
- very buggy under IE8, and simply unusable with older versions of IE
