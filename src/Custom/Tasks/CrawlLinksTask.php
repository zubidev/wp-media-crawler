<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Tasks;

use Exception;
use WP_Media\Crawler\Custom\Crawlers\LinksCrawler;
use WP_Media\Crawler\Custom\Crawlers\WebpageReader;
use WP_Media\Crawler\Custom\Filesystem\File;
use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;

/**
 * Class CrawlLinksTask. Responsible for crawling the links and storing them.
 */
class CrawlLinksTask extends AbstractTask {

    /**
     * The task runner.
     */
    public function run() : void {
        if ( ! wp_doing_cron() ) {
            return;
        }
        try {
            $home_file = new File( 'sitemap.html' );

            SitemapLinksStorage::delete();
            $home_file->delete();

            $page_reader      = new WebpageReader( home_url() );
            $response_content = $page_reader->get_content();

            $link_crawler = new LinksCrawler( $response_content );
            SitemapLinksStorage::store( $link_crawler->crawl() );

            // Custom step: Save the home page’s .php file as a .html file.
            $home_file->save( $response_content );
        } catch ( Exception $e ) {
            // TODO: Add a proper error logging.
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( 'The following ocurred while crawling the links: ' . $e->getMessage() );
        }
    }
}
