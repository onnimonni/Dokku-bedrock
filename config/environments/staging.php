<?php
/* Staging */
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASS'));
if (getenv('DB_PORT')) {
	define('DB_HOST', getenv('DB_HOST'.':'.getenv('DB_PORT'));
} else {
	define('DB_HOST', 'localhost');
}
define('WP_HOME', getenv('WP_HOME'));
define('WP_SITEURL', getenv('WP_SITEURL'));

ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);