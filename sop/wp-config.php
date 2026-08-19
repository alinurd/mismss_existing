<?php
define( 'WP_CACHE', true );
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u464450487_rpjV6' );

/** Database username */
define( 'DB_USER', 'u464450487_I4Uzh' );

/** Database password */
define( 'DB_PASSWORD', 'viCtFtAARo' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'b1BPjWD?}Dkse|hmua@z%VR8FutS<7TWy0sC`_iK.N>Z*sx%i}AbA%0FYkVKGuyM' );
define( 'SECURE_AUTH_KEY',   'j-)FSmjn3^JA}d[,q=y^Zzz<}H-|(?>+FRrC#)D3t@v&2H)iv{U0h#bU`o#CeOM(' );
define( 'LOGGED_IN_KEY',     'ua;Ze3 1U_5JkYCGihk?d-Cv-_!p<k*n$Id[i1BkkbT2(>T}P6m]gKHR4ia 8Ju$' );
define( 'NONCE_KEY',         'U$;>DK4h{XMTv(5-lklS+4%.)8w@_.^hH5zAsaZOa kE,^y`)B^<;Z`g6/LfIBHK' );
define( 'AUTH_SALT',         '1!^_R;9O$3Q{+(SAWk@c#7;uC>}r.27X(VKG5-.Lvj&!<)[?qh74FJP)v6wok,3j' );
define( 'SECURE_AUTH_SALT',  '4u_~3<nSM,PCrs/)}Yj+Ypz,F7ASF*Xq4g6p%VDg )NdGMsBU #m=][Gb#.A4!qD' );
define( 'LOGGED_IN_SALT',    'BjH#EcY;gW;BIh[V%TW])[oT}/kba|ww7qooRWOBb]~okMN>%6R&REiA@fJ(:iB+' );
define( 'NONCE_SALT',        '1e1xE-{yaGtC{|K4gAHurlV1?=bE8a^,:]k&)5?yJ@Z>X!p4[ie`r9}0m5t0nh#`' );
define( 'WP_CACHE_KEY_SALT', 'M#>0QDa+}-(:^-bIt>2dwGiP#72/KT~KDjp&;=Ll/JL@}`1 n=#H&i8Dpc*k);Y3' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '728b281e1aff6fb8499a90b412897b2e' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
