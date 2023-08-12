<?php
/**
 * Plugin Name:         WP Media Crawler
 * Plugin URI:          https://github.com/zubidev/wp-media-crawler/
 * Description:         Crawls WordPress pages (the Home page at least) and create sitemaps.
 * Author:              Muhammad Zubair Khan
 * Author URI:          https://github.com/zubidev/
 * Text Domain:         wp-media-crawler
 * Domain Path:         /languages
 * Version:             0.1.0
 * Requires at least:   5.0
 * Requires PHP:        7.2
 *
 * @package WP_Media_Crawler
 */

defined( 'ABSPATH' ) || exit();

if ( ! defined( 'WP_MEDIA_CRAWLER_VERSION' ) ) {
    define( 'WP_MEDIA_CRAWLER_VERSION', '0.1.0' );
}

if ( ! defined( 'WP_MEDIA_CRAWLER_FILE' ) ) {
    define( 'WP_MEDIA_CRAWLER_FILE', __FILE__ );
}

if ( ! defined( 'WP_MEDIA_CRAWLER_PATH' ) ) {
    define( 'WP_MEDIA_CRAWLER_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WP_MEDIA_CRAWLER_URL' ) ) {
    define( 'WP_MEDIA_CRAWLER_URL', plugin_dir_url( __FILE__ ) );
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

if ( class_exists( '\\WP_Media\\Crawler\\Init' ) ) {
    WP_Media\Crawler\Init::init();
}
