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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'seniorbarman_devsbm0724' );

/** Database username */
define( 'DB_USER', 'seniorbarman_devsbm0724' );

/** Database password */
define( 'DB_PASSWORD', '-4Q(SpvT48' );

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
define( 'AUTH_KEY',         'gke2qjzsecwn4uiguwnshrckslvaa4hedbiceqargekrbkudcwev24y4leupwtou' );
define( 'SECURE_AUTH_KEY',  '0jqj03mrkmgtxa7ptlavclc9rlwgladdteh9wgnfpbhkxzgjyyq7ti836unhknmp' );
define( 'LOGGED_IN_KEY',    'fm4qnzrvu2ug50iqdtevw25y8aysel6zleovsmxmpe3tpceyf9611i8klj1ybps2' );
define( 'NONCE_KEY',        'ixic141fir8sjfy9widfvtzi6f5ukbmncp6f4di3xqg509s3zcm6zfzxc95fllq5' );
define( 'AUTH_SALT',        'wqhgcjcba4bmntd916g5iulvnrgemk25xrahniuwkbaa24sbeulxboc9wuiixg2q' );
define( 'SECURE_AUTH_SALT', 'ucqlublz6x8o7yuktmhokf8rekwmvlhi3hcwpisr4bkg6ochlognaeiusvm603q4' );
define( 'LOGGED_IN_SALT',   'sulq2hoiihfawo16djzzxymndba24ayex7k7gb5r6ivd7b5n2biyjyspbhhgx6mu' );
define( 'NONCE_SALT',       'l97qcbyxe2qnta3gjch8dhbfuspbvfhywl8almirwiqepyzdc9y9vq94rwabnzhd' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpsl_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
