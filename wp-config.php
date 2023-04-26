<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'somashome' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'BYvVG4aUJQ51VlN80tTGAZ6VWPJ4j1r5LqMvQF4DZ6EXo5ebLSgXH3IIk1WIudvl');
define('SECURE_AUTH_KEY',  'JSJ9soFOpgeKyAgv762ToOAfluYP3BeXYkbm4n8eegTxCflmnjTLDS6qBQgX58KZ');
define('LOGGED_IN_KEY',    'aEfHywXk7Bq1j5kbwXDmatqZ1Z0dRkXZFwkibuRJfoetmd5pbg5XoSs0I9oQtQ7I');
define('NONCE_KEY',        'rCNtqhoWjjx7TasW7V49Am2ps7zUx7go1eESCG19v41srlcoXnpdMerUYVyJvVdP');
define('AUTH_SALT',        'VsuiQOjfgb6WjbMWilQbAcUUlBn5EBnse5FkN1HKiBLtH56aqgPGckuk9xiwtFDL');
define('SECURE_AUTH_SALT', 't9JbaqFbflgWm1RAY46cTNNE7zAgvp5qfjLytRtDwYPs7GPMNy96YTsc0d7fxP2u');
define('LOGGED_IN_SALT',   'qSWKIM3h00XHcLhhsJazhP1Bjey7AFxsFNKQWKrhHotgESWed3pnuAPc3FwHCDIx');
define('NONCE_SALT',       'W566j01KrsVtlW2V4U6O0C3aTPXiQekrEWpM9Vjb52BcsEFwTnMqeFxaiI1VBTkw');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
// define('CONSUMER_KEY','ck_e1cdba74d3a1ff703d295ae551e37a8daae08f0e');
// define('SECRET_KEY','cs_36a6246e498c0ff5fb0f2293e8efa647a7951be9');
// define('STORE_URL','https://somashome.be/');
/* Add any custom values between this line and the "stop editing" line. */

set_time_limit(900);
define( 'WP_MEMORY_LIMIT', '1024M' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
