<?php

define('APP_DB_HOST','localhost');
define('APP_DB_USER','root');       // edit here
define('APP_DB_PASS','');           // edit here
define('APP_DB_BASE','taskfreak_timer');       // edit here
define('APP_DB_PREFIX','');
define('APP_DB_PERMANENT', true);
define('APP_DB_CONNECTOR','mysql');

define('APP_DB_CRITICAL', true); // exit on DB error

// DEBUG level :
// 0 : nothing at all
// 1 : use FC::log_error (on error only)
// 2 : show error code in browser, the rest using FC::log_error (on error only)
// 3 : show error and SQL query in browser (on error only) - useful in development
// 4 : verbose : use FC::log_message (everything) - for debugging only
// 5 : verbose : sends to browser (everything) - for heavy debugging only

define('APP_DB_DEBUG', 2);