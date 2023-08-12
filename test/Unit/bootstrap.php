<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
 */

/**
 * File that will be run by PHPUnit before testings
 */

define( 'WP_MEDIA_PLUGIN_ROOT', dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR );
define( 'WP_MEDIA_PLUGIN_TESTS_ROOT', dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'Unit' . DIRECTORY_SEPARATOR );
define( 'WP_MEDIA_TESTS_FIXTURES_DIR', dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'Fixtures' );

// For some reason some of the classes inside the \\Tests\\Unit namespace are not being loaded by the wp
// media bootstrap.php. So we need to load them manually.
$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4( 'WP_Media\\Crawler\\Tests\\', dirname( __DIR__ ), true );
$loader->register();
