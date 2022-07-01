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
define( 'DB_NAME', 'vinoroot_wp55' );

/** Database username */
define( 'DB_USER', 'vinoroot_wp55' );

/** Database password */
define( 'DB_PASSWORD', '!(7S7gk17p' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'ytqqktuwbwypqq3vlbi4rhmswexe0dyo9pswqle1ipuvsxbk9uh3g0oagijk6zn6' );
define( 'SECURE_AUTH_KEY',  'a2krrk7smufhd4vucqcofcw4wsv0qzodqg1jvmpouafpqzkdticxxbs2fp86dwhz' );
define( 'LOGGED_IN_KEY',    'wt5jc4ynoccl63f4fixjre7b05n5h2smbkxksdwwqhahazooktdyohr3v4aolgwj' );
define( 'NONCE_KEY',        'ofxcdtla8plzotp0kfc5fsig1ek6evxunjjshx8oxyoas7qklqgoklzrcflvxiiz' );
define( 'AUTH_SALT',        'jg0qhaglzrfakg7p9uuotornvqcqlaso8oucmtjfydlmcmhmdwprasnmhkdyncxy' );
define( 'SECURE_AUTH_SALT', 'eyy55grw85waiditqnjs7x4yh0hyhgrjuae3lchstamvmyfekavyus3npcbmczji' );
define( 'LOGGED_IN_SALT',   '2c9xyef0sf6qyybj0u8vbqzkepdytnmw3hlkmsncrcinubiyor8uscohsc3osqij' );
define( 'NONCE_SALT',       'ss2odmoud4stvkvlprymlsjd2aytz9owxxhhtvipp2iwawwbmjkkoahbgducmlfi' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp6i_';

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
