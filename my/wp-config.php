<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// Disable WP Cron jobs
define('DISABLE_WP_CRON', true);

// ** MySQL settings - You can get this info from your web host ** //
// Get current hostname
$current_hostname = (php_sapi_name() === 'cli') ? php_uname('n') : $_SERVER['HTTP_HOST'];

// Set database users
$hostnames = array('my.dev.saltsha.com' => 'saltshac_dev','my.qa.saltsha.com' => 'saltshac_qa','my.stage.saltsha.com' => 'saltshac_stage','my.saltsha.com' => 'saltshac_live','saltsha1.saltsha.com' => 'saltshac_live','saltsha2.saltsha.com' => 'saltshac_live','saltsha3.saltsha.com' => 'saltshac_live');

// Set database prefix based on username running this script
$db_prefix = (!array_key_exists($current_hostname, $hostnames) || stripos($current_hostname, '.local') !== false || stripos($current_hostname, 'local.') !== false) ? 'saltshac_dev' : $hostnames[$current_hostname];

// Set current hostname - If hostname not found, default to dev site, else, default to localhost
$cur_hostname = (!array_key_exists($current_hostname, $hostnames) || stripos($current_hostname, '.local') !== false || stripos($current_hostname, 'local.') !== false) ? 'dev.saltsha.com' : 'localhost';

// If production server, set hostname to cloud db
$cur_hostname = ($db_prefix == 'saltshac_live') ? 'dbs1.saltsha.com' : $cur_hostname;

/** The name of the database for WordPress */
define('DB_NAME', $db_prefix.'_my');

/** MySQL database username */
define('DB_USER', 'saltshac_Tlc0hWJ');

/** MySQL database password */
define('DB_PASSWORD', 'R,UN~v.l2p+iBQ9zud');

/** Change DB to in-house for local development **/
if( strpos($current_hostname, 'local.') !== FALSE ) {
	// $cur_hostname = 'localhost';
	$cur_hostname = '104.236.240.39';
}

/** MySQL hostname */
define('DB_HOST', $cur_hostname);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** Define website URL and home address in config instead of in database **/
/********** THIS WILL PREVENT ISSUES DEVELOPING LOCALLY WITH GIT **********/
if ($_SERVER['SERVER_PORT'] == '443' || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	define( 'WP_SITEURL', 'https://'.$_SERVER['HTTP_HOST']);
	define( 'WP_HOME', 'https://'.$_SERVER['HTTP_HOST']);
} else {
	define( 'WP_SITEURL', 'http://'.$_SERVER['HTTP_HOST']);
	define( 'WP_HOME', 'http://'.$_SERVER['HTTP_HOST']);
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'rXtMr4<+6g)preBq!|.naFiFiA}Dho1Xxkr8VzPtbHhN*9iAr-Wr{Q~H;5s@o}{E');
define('SECURE_AUTH_KEY',  '8`9p4V~,:=3<Eo.Qe|:| z,T.is4;nzpY`qZP>PRU0ku-Yj:7Nm}K@PEOnOfC?F$');
define('LOGGED_IN_KEY',    'LtVm-`A B~s*Px#f|v=r`jA#KaD$%K:PzVyE-cc/Ax[sjI<jFZ]HJDnHD#D#>=S_');
define('NONCE_KEY',        'R=x>F.JPaH,ElYs#5w6WQ]6t&q#fy#--4xHA`ox!?wY97Y!`lzwy<KwEyTqNoM-q');
define('AUTH_SALT',        '42FWiBn13p=_&lOx8sFmcPcgp&L!31N6LSn|7A9KNO*lq%lyPVL OGQTGOToNvJf');
define('SECURE_AUTH_SALT', 'QUh/-UeSQkDW_SY$x}S5tWp,t{=Clp$V;sDnY8,p$^Li|3J&<|L$sd*O%s!1~yH9');
define('LOGGED_IN_SALT',   '@<O^VHhDFF}3+~VwWzk_*l5poIq<ic@e.L8/k:-FX(YzkAp4F+u?B(cxJosum]sz');
define('NONCE_SALT',       '5jc<h^MiO70Ogkyt7`>CIhp`s[XvHs/sb.+6$.+{|x*9av_-Z)wTB-?pO<}c>z;P');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* Multisite */
/*
define( 'WP_ALLOW_MULTISITE', true );
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
*/

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
