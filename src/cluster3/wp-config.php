<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'database' );

/** Database username */
define( 'DB_USER', 'user' );

/** Database password */
define( 'DB_PASSWORD', 'password' );

/** Database hostname */
define( 'DB_HOST', 'mariadb' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         't<JFA,t-`RC2C[#Q+BbJ@Bh/&iX84U=nY<bFfV]5?CWU$35NuBa7$4V{fbZ#{4L1' );
define( 'SECURE_AUTH_KEY',  '}hK2Ds@Sg&uacMM,~Urdi?7#Z$]}R20uisQ.%!rRjyzj;2zTPWNqK/!4uNxy.|GE' );
define( 'LOGGED_IN_KEY',    '$KbY<=*r~N_&]Yz{P8bgjR^y}voHR(-F4,t*|DFU,sQ^ .j@E5t8/J2^Rl6|M:j ' );
define( 'NONCE_KEY',        '`rc*;^{-Mu5:$_.ZVjU$63<6snuGOOjdB!HV&N_FE:*$A`Y[@wE8apA6%&Z=%uV>' );
define( 'AUTH_SALT',        'eqUK]k0rMuxqG8/k|QXqhgi5_=[ej(-!a%e:d,Lt;OI`3lB&5:St8MrpvhUyz;lr' );
define( 'SECURE_AUTH_SALT', '0kUuF+2t+CUzIhB]Xx$HhxB*z1[XBqK/]s>A|efh-; k!ln]!j=3|JJFZ&^Sw<4,' );
define( 'LOGGED_IN_SALT',   'cxwX`?R8K _)k4ZmOA(Z:?7ud{5&A{L?-ppkEI>+5g8MCJgRb2XJ8xh.d#B?P/|,' );
define( 'NONCE_SALT',       'n@BW>6+I@AphtJZfgrqCl#0w^A(g;EGRdD4N=_bqIMcl5~cY)-eX1QeK7*.cz8Lk' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
