<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Admin\SitemapManager;

use Exception;
use WP_Media\Crawler\Custom\Crawlers\LinksCrawler;
use WP_Media\Crawler\Custom\Crawlers\WebpageReader;
use WP_Media\Crawler\Custom\Filesystem\File;
use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;
use WP_Media\Crawler\Exceptions\CrawlerException;
use WP_Media\Crawler\Exceptions\WebpageException;

/**
 * Class CrawlLinksHandler. Responsible for handling the crawl sitemap links request.
 */
class CrawlLinksHandler {

    /**
     * Register post hook
     */
    public static function init() : void {
        add_action( 'admin_post_wp_media_crawler_crawl_sitemap_links', [ __CLASS__, 'handle_crawl_sitemap_links' ] );
    }

    /**
     * Handle the crawl sitemap links request.
     */
    public static function handle_crawl_sitemap_links() : void {
        check_admin_referer( 'wp_media_crawler_crawl_sitemap_links' );

        if ( ! current_user_can( 'administrator' ) ) {
            wp_safe_redirect( wp_get_referer() );
            wp_die(
                esc_html__( 'You are not allowed to crawl the sitemap links.', 'wp-media-crawler' ),
                esc_html__( 'Permission Error', 'wp-media-crawler' )
            );
        }

        try {

            $home_file = new File( 'home.html' , 'html' );

            SitemapLinksStorage::delete();
            $home_file->delete();

            $page_reader      = new WebpageReader( home_url() );

			$response_content = $page_reader->get_content();

            $link_crawler = new LinksCrawler( $response_content );
			SitemapLinksStorage::store( $link_crawler->crawl() );
            $home_file->save( $link_crawler->crawl() );
        } catch ( WebpageException $e ) {
            self::handle_error( $e->get_error_message_html() );
        } catch ( CrawlerException $e ) {
            self::handle_error( $e->getMessage() );
        } catch ( Exception $e ) {
            self::handle_error( 'The following ocurred while crawling the links: ' . $e->getMessage() );
        }

        self::redirect();
    }

    /**
     * Handle error
     *
     * @param string $message Error message.
     */
    protected static function handle_error( $message ) : void {
        $output  = '<p><b>' . esc_html__( 'Error while crawling the sitemap links.', 'wp-media-crawler' ) . '</b></p>';
        $output .= $message;
        wp_die(
            $output, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            esc_html__( 'Sitemap Crawler Error', 'wp-media-crawler' ),
            [
                'response'  => 400,
                'back_link' => true,
            ]
        );
    }

    /**
     * Handle the request redirect
     */
    protected static function redirect() : void {
        if ( wp_safe_redirect( wp_get_referer() ) ) {
            die('Links crawled successfully.');
        }
        wp_die( 'Links crawled successfully.' );
    }
}
