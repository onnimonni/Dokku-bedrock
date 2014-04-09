<?php
/* Development */
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
if (getenv('DB_PORT')) {
	define('DB_HOST', getenv('DB_HOST').':'.getenv('DB_PORT'));
} else {
	define('DB_HOST', 'localhost');
}

define('WP_HOME',(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST']);
define('WP_SITEURL', WP_HOME.'/wp');

define('WP_ALLOW_MULTISITE', true);
if (getenv('WP_MULTISITE_MAIN') && getenv('WP_MULTISITE_MAIN') != '' ) {
	define('MULTISITE', true);
	define('DOMAIN_CURRENT_SITE', getenv('WP_MULTISITE_MAIN'));

	define('SUBDOMAIN_INSTALL', getenv('SUBDOMAIN_INSTALL') === 'true' ? true : false);

	define('PATH_CURRENT_SITE', '/');
	define('SITE_ID_CURRENT_SITE', 1);
	define('BLOG_ID_CURRENT_SITE', 1);
}

define('SAVEQUERIES', true);
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', true);
define('SCRIPT_DEBUG', true);