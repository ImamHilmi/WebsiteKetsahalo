<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
/* Themify Cache Start */
define('WP_CACHE',true);
/* Themify Cache End */

define( 'DB_NAME', 'ketsahalo' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'OG 3b6RV6{[W@Q;Nt`J1|)Wv=N-GZI2B_B_gzf|4i A{t=dV>YB^N)<kXYi0vjsq' );
define( 'SECURE_AUTH_KEY',  ')4&7-rz,ng#Xm8Lb0ZKkjl&]_Rz=k}3lFLCRwEf^GnykyPb`HJsLrEvYd-xAN1v|' );
define( 'LOGGED_IN_KEY',    'jA5u#)n?pGk+u~P)W.<.ni@#`;!Ij?o5`w]POA/Eni}[x.4X:tL7/@:E/ML-:6XR' );
define( 'NONCE_KEY',        'nJGiP[ruk<bz4Z_pm3y:6+a5LhZca*3qN&oaenz<GbK:yT(X_KJ7e5EWm(+f~zfl' );
define( 'AUTH_SALT',        ':nUWVBLX.EW-eR>n_QJo=Xo[KH<wI{GtT:LqI %H`#3CsWjmFiw[{6|/`NESj &z' );
define( 'SECURE_AUTH_SALT', '7zaSHn)(l}ebxld,HHc~:8jQ]*ip|8#,2L.yv_O<@oYeCp#E S=nSqPZHv_&u=y,' );
define( 'LOGGED_IN_SALT',   '!( b!va@Ar=w/K|uU-ax5@6}3sQwY8mW1@^lT|%m^{V1nXz-34lJ7`S{`H]_#k.V' );
define( 'NONCE_SALT',       'cqux;hNP6.{9&rqBYuatGvv|!U)-6=?/vt7Gxs~jkQ)LwcW-T!WF^%]CfkQNns?d' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
