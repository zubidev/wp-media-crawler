<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Tests\Fixtures\Custom\Sitemap;

use WP_Media\Crawler\Custom\Sitemap\SitemapRouter;

/**
 * Class SitemapRouterDouble. Override the SitemapRouter class for testing.
 */
class SitemapRouterDouble extends SitemapRouter {

    /**
     * Overwrite sitemap_close() so we don't die on outputting the sitemap.
     */
    protected function sitemap_close() : void {
        remove_all_actions( 'wp_footer' );
    }
}
