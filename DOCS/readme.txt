TaskFreak! Time Tracking
------------------------
version  : 0.4


Requirements
------------
- apache (tested) or any other webserver (not tested)
- PHP 5.3.x
- mySQL 4.1 or later


Setup
-----
1) create a mySQL database
2) create tables and data with taskfreak_time.sql
3) open app/config/db.php and change database settings

You can stop here and have a look.
login is "admin" with no password


Enabling clean URLS
-------------------
If using apache with mod_rewrite, you should enable clean URLs

1) copy the file htaccess from the DOCS folder to the application root folder
2) change its name from htaccess to .htaccess
3) open app/config/core.php, and change APP_URL_REWRITE to true


Single user
-----------
If you don't want multi user support, open app/config/core.php
Search for APP_SETUP_USER_MODEL and set it up to false :
[code]
define('APP_SETUP_USER_MODEL',false);
[/code]

More options
------------
If you feel like playing so more, open app/config/app.php and look into it

To change the application language have a look at DOCS/translation.txt


Known bugs
----------
- some errors might occur on dates depending on your time zone
- order by start, stop, or spent doesn't really make sense
- very buggy under IE8, and simply unusable with older versions of IE
