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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '92XR66J-,2zGrc-&F_SRg6|rY>Mxq#zMu4eO!ef<rbk2f~.(MaLB4c$t9,DI:|m>' );
define( 'SECURE_AUTH_KEY',  'pz)LZr0KX_#aF[^ev!~uF94wW3 >>iT&y@W _u;J#c/_RC&9)#p.rwX;V8XvR(!5' );
define( 'LOGGED_IN_KEY',    '.~+HFeLJ>vfuQ)Xes*w;:J=.lb,w&r,Cqa<sPr&e1zgmK4,Y Tty-T<Y=cPpC6_x' );
define( 'NONCE_KEY',        'imK[rDe=_=VaDGyn1#f|s0|EvQMBP}~og_G}}VC3D[MO^[V14/e|mJRB0j=<Nd(x' );
define( 'AUTH_SALT',        '(;%y2NhwM>%V{MgHgy({[DM6?Z>R^psm?Ee[CoL-OEJZ@/m1X}],qnjUi@H7[ybs' );
define( 'SECURE_AUTH_SALT', '#<g1]>wz7=e47Kl9*:!wiBkbTm?b,DbQ{qh}ISWtKJ/XX-]nxw1c&vL;4U!;Px5W' );
define( 'LOGGED_IN_SALT',   'N4J)[!=5_2RPpJ]Z7)_Fo4!d>dvSpOMiqq$u:/gOKE[Vg)=za]JZ3 :c^2!Cm/DU' );
define( 'NONCE_SALT',       '>htq|HE9ut6AEm@{~&4h8Z3Rz<S)y2wJ8kDx;Zj,RFQOXRT/Pl:-mSL%(h{~8SNQ' );

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
