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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',          'YLiR/j5;:c1K/L_f_oYSkFjTE%a1 @0*5U{- hv+pwA*Ur4wpL(5O]H#y`AjKn-j' );
define( 'SECURE_AUTH_KEY',   'd)E,/y}}mq8y=9H~|{ko!pl t#eBRNx!0Hf:viX(hT~/EC2HxCAi@`,7dQ:Pv^1`' );
define( 'LOGGED_IN_KEY',     '!;i<L^G6@rKu/s`T _VF2{:!^|xXvx1lf>=m!XW9se<`>l~cDIFcg;,A#0D&l3)z' );
define( 'NONCE_KEY',         'jhu!hl?^_+C!ms)!dzi)-sqvrY;-@E_1r~6:pMnJwz##!._Or f+lNCMakqUnjeA' );
define( 'AUTH_SALT',         'Nj0pTeh;;@:/tIKYqQ%i]2:)~mNQ(vQryZ|aK(O|TB31Qm*mWj$|bw/xyXnIUI8,' );
define( 'SECURE_AUTH_SALT',  '-38(SdqLP%M7Sji*]xInC`yaIEfe_|H&iV1$tGl6h!MpygFf=H;iaVW?nd.~v56V' );
define( 'LOGGED_IN_SALT',    'A)&iW|]Tr|+-{u9$]Tdp+{*0M}hb5X<KKqO&#A/qC`Y}6/7I+({CZnpH]Voe`>PM' );
define( 'NONCE_SALT',        'I!,T$&&<=:bD&P.pCz; 6j9s1Opf}`0%-qwb]K>x)GahG YpXnk,H,!#fKp{gy9R' );
define( 'WP_CACHE_KEY_SALT', '}`3^IiQ-,5Yvm?hk+Sy2(+XD#]Kv<ywx2XKt?/DJ(ux+^6`RJj-|NZGw}!Z;8[!{' );


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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
