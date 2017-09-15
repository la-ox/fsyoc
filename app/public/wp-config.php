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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'XVMbUCu0y51fciScGvi6IjWmHi9owSjIU07323KzXHUfcbVHGKe1rjY1acExe4z1oD/3jdvfZ7D9p9vQ81u9mw==');
define('SECURE_AUTH_KEY',  '3eRa8o1TWTaVPv7tEFPw3G+2Po4k0PCUPKJkGrwV3iQ7GGGqz2htJz7Kg40lcB9uK7vZMx6LAJj+wTpMw+p0Vg==');
define('LOGGED_IN_KEY',    'b2VgUgTmCoUX8MSHNom/CYE/D/EtD8fZ1r0haArIrtJQxB9+3yvIVn10j8AJ27du+nJ7TSeCXewJMCjcsrHPYg==');
define('NONCE_KEY',        '33j99et/K1uoewFFC2IbFbI9M/Y0aIBcxlSxXEcl1XtyXF040sXdUlfTIizZZsiqEoRk5OZoECXNHwAekygrsw==');
define('AUTH_SALT',        'wW1rmwVN+IrAhwTHXl6tcaqWaJkvBxdTpgrI7rAsqiyPROuNSv3umNuWQeIfZGdCk07UMNf+IGZ6oQeQ/gQn5Q==');
define('SECURE_AUTH_SALT', 'Prt7v+OF5Thhtx1QkH0wi3qJxCNhCBAXlxa69FC6Fy4ze87cckrFmd/+nQtf8X2nCfv70aKL61FIaWVTSq2TFw==');
define('LOGGED_IN_SALT',   'SkJntbItJsC2Qq+vssL8vw3GZ6A1+ft2Sxg8cOWjzRQ11K7hrgrMYE8g32k21p/m47q0Rbwy0d9rLp3uXyGzLw==');
define('NONCE_SALT',       'ZBqqHdvKIOpIPf/WUOLdQQbnabffhy5+vtGfG3kPIKq3NJnwQ/qlsEKUdM5gWQ/hoV1Zl0PEIxmXwWvAbrKc1g==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
