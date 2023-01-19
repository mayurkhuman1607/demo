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
define( 'DB_NAME', 'dev' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Drc@1234' );

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
define( 'AUTH_KEY',         '0wlrr3D#8h:UnO%[ZAJQnAMEP=Lj%u::C4:p|>.B8#+KwCR+r0^pa9zN1.O7IpF`' );
define( 'SECURE_AUTH_KEY',  'CrHI0FLgd|}]OYm?B&+9;A||IKMcn4^JMm.RT0gK)b(G&N|(q0,*YE][03Jvt+<)' );
define( 'LOGGED_IN_KEY',    'y|j%COdKT&R;Qt^gf/r~#>q>#l*FuA>.}npTrjE^W2F;T9*&f_=SLTak03hV%[VM' );
define( 'NONCE_KEY',        '0(Y?JF+2D|!3B2bI%LQKgOrs 82: !5wDM9qK!ecT8|PZz3RA1[f=%cU)o6[Z=+m' );
define( 'AUTH_SALT',        'a^7(*W<n,ukbo8wx3eM9lX4_&C{vidJq=GM*lAoOcr3c*Nw)v8?C)c./?S6X4MuC' );
define( 'SECURE_AUTH_SALT', '5GD%9+m$LfZ*Z_ p&x9MmFW>,|#J(,Y_kLJ?g`?uynaz#o7iMZa@X.F:aN$l,na~' );
define( 'LOGGED_IN_SALT',   'Fh;zF))On<<Jp[`0K-8zzGi[L3,e0})v`6Nar$&CC-NyfJ0Oc:l#%)it|}Tp98=t' );
define( 'NONCE_SALT',       'l-&>J`*f:_f/`6t+WRFLpkb47mYkHzLnunNOUF*:a<D<P$D#C<5rzRogD]}]qmO2' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */
define('FS_METHOD', 'direct');
@ini_set('upload_max_size' , '25600M' );



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
