<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Admin\SitemapManager;

use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;

/**
 * Class OptionsPage. Register the Options Page responsible for the Sitemap Management.
 */
final class OptionsPage {

    /**
     * Register the options page.
     */
    public static function register() : void {
        add_action( 'admin_menu', [ __CLASS__, 'add_options_page' ] );
    }

    /**
     * Adds the options page.
     */
    public static function add_options_page() : void {
        add_submenu_page(
            'tools.php',
            __( 'WP Media | Sitemap Manager', 'wp-media-crawler' ),
            __( 'WP Media Sitemap', 'wp-media-crawler' ),
            'administrator',
            'wp-media-crawler-sitemap-manager',
            [ __CLASS__, 'render_options_page' ]
        );
    }

    /**
     * Renders the options page.
     */
    public static function render_options_page() : void {
        $sitemap_links = SitemapLinksStorage::retrieve();

        $args                  = [];
        $args['sitemap_links'] = $sitemap_links;

        include WP_MEDIA_CRAWLER_PATH . 'templates/admin/sitemap-manager.php';
    }

}
