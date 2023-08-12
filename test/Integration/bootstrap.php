<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

/**
 * File that will be run by PHPUnit before testings
 */

define( 'WP_MEDIA_PLUGIN_ROOT', dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR );
define( 'WP_MEDIA_PLUGIN_TESTS_ROOT', dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'Integration' . DIRECTORY_SEPARATOR );
define( 'WP_MEDIA_TESTS_FIXTURES_DIR', dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'Fixtures' );

tests_add_filter(
    'muplugins_loaded',
    function() {
        // Load the plugin.
        require WP_MEDIA_PLUGIN_ROOT . '/wp-media-crawler.php';
    }
);
