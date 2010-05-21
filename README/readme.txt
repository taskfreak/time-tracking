TaskFreak! Time Tracking
------------------------
version 0.2
released on 2010-05-21
------------------------

This is the first official release
status : beta

Requirements
------------
- apache with mod_rewrite
- PHP 5.3.x
- mySQL 4.1 or later
- a virtual host 

v0.2 can not run under yourdomain.com/taskfreak, it has to be yourdomain.com

Setup
-----
1) Set up your virtual host
2) create a mySQL database
3) create tables and data with taskfreak_time.sql
4) open app/config/db.php and change database settings

You can stop here and have a look.
login is "admin" with no password

If you don't want multi user support, open app/config/core.php
Search for APP_SETUP_USER_MODEL and set it up to false :
[code]
define('APP_SETUP_USER_MODEL',false);
[/code]

If you feel like playing so more, open app/config/app.php and look into it

