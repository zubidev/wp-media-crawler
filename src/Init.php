<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler;

use WP_Media\Crawler\Custom\Admin\SitemapManager\InitSitemapManager;
use WP_Media\Crawler\Custom\Sitemap\SitemapRouter;
use WP_Media\Crawler\Custom\Tasks\CrawlLinksTask;

/**
 * Class Init. Initializes the plugin and the sequential main flow.
 */
class Init {

    /**
     * Init method.
     */
    public static function init() : void {
        self::init_tasks();
        self::init_admin();
        self::init_custom_domains();
    }

    /**
     * Init tasks.
     */
    public static function init_tasks() : void {
        new CrawlLinksTask( 'wp_media_crawler_crawl_links', 'hourly' );
    }

    /**
     * Init admin.
     */
    public static function init_admin() : void {
        InitSitemapManager::init();
    }

    /**
     * Init custom domains.
     */
    public static function init_custom_domains() : void {
        SitemapRouter::init();
    }
}
