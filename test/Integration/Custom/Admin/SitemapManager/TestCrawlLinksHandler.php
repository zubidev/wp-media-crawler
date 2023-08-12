<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 *
 * @phpcs:disable Squiz.Commenting.FunctionComment
 * @phpcs:disable Squiz.Commenting.VariableComment
 * @phpcs:disable Generic.Commenting.DocComment
 * @phpcs:disable WordPress.WP.AlternativeFunctions
 * @phpcs:disable WordPress.Security.NonceVerification
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
 */

namespace WP_Media\Crawler\Tests\Integration\Custom\Admin\SitemapManager;

use Brain\Monkey\Functions;
use WP_Media\Crawler\Custom\Filesystem\File;
use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;
use WPDieException;
use WPMedia\PHPUnit\Integration\TestCase;

/**
 * @covers WP_Media\Crawler\Custom\Admin\SitemapManager\CrawlLinksHandler
 * @covers WP_Media\Crawler\Custom\Crawlers\WebpageReader
 * @covers WP_Media\Crawler\Custom\Crawlers\LinksCrawler
 * @covers WP_Media\Crawler\Custom\Filesystem\File
 * @covers WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage
 *
 * @group Admin
 */
class TestCrawlLinksHandler extends TestCase {

    protected $user_id = 0;

    public function set_up() : void {
        parent::set_up();
        unset( $_REQUEST['_wpnonce'] );
    }

    public function test_action_blocked_by_nonce() : void {
        add_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        Functions\expect( 'current_user_can' )->never();

        $this->expectException( WPDieException::class );
        $this->expectExceptionMessage( 'The link you followed has expired.' );

        do_action( 'admin_post_wp_media_crawler_crawl_sitemap_links' );
    }

    public function test_action_blocked_by_user_capability() : void {
        $this->user_id = $this->factory->user->create( [ 'role' => 'editor' ] );
        wp_set_current_user( $this->user_id );

        $_REQUEST['_wpnonce'] = wp_create_nonce( 'wp_media_crawler_crawl_sitemap_links' );

        add_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        $this->expectException( WPDieException::class );
        $this->expectExceptionMessage( 'You are not allowed to crawl the sitemap links.' );

        do_action( 'admin_post_wp_media_crawler_crawl_sitemap_links' );
    }

    public function test_action_fails_because_of_remote_request_exception() : void {
        $this->user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
        wp_set_current_user( $this->user_id );

        $_REQUEST['_wpnonce'] = wp_create_nonce( 'wp_media_crawler_crawl_sitemap_links' );

        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andReturn( new \WP_Error( 'http_request_failed', 'Generic Error.' ) );

        add_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        $this->expectException( WPDieException::class );
        $this->expectExceptionMessageMatches( '/.*(The page isn\'t accessible.).*/' );

        do_action( 'admin_post_wp_media_crawler_crawl_sitemap_links' );
    }

    public function test_action_fails_because_there_are_no_internal_links() : void {
        $this->user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
        wp_set_current_user( $this->user_id );

        $_REQUEST['_wpnonce'] = wp_create_nonce( 'wp_media_crawler_crawl_sitemap_links' );

        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andReturn(
                [
                    'response' => [ 'code' => 200 ],
                    'body'     => '<html></html>',
                ]
            );

        add_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        $this->expectException( WPDieException::class );
        $this->expectExceptionMessageMatches( '/.*(The page doesn\'t have any internal link.).*/' );

        do_action( 'admin_post_wp_media_crawler_crawl_sitemap_links' );
    }

    public function test_action_fails_by_generic_error() : void {
        $this->user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
        wp_set_current_user( $this->user_id );

        $_REQUEST['_wpnonce'] = wp_create_nonce( 'wp_media_crawler_crawl_sitemap_links' );

        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andThrow( new \Exception( 'Generic Error.' ) );

        add_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        $this->expectException( WPDieException::class );
        $this->expectExceptionMessageMatches( '/.*(The following ocurred while crawling the links: Generic Error.).*/' );

        do_action( 'admin_post_wp_media_crawler_crawl_sitemap_links' );
    }

    public function test_action_successfully_executed() : void {
        $this->user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
        wp_set_current_user( $this->user_id );

        $_REQUEST['_wpnonce'] = wp_create_nonce( 'wp_media_crawler_crawl_sitemap_links' );

        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andReturn(
                [
                    'response' => [ 'code' => 200 ],
                    'body'     => '<!DOCTYPE html>
				<html>
					<body>
						<a href="/link-1">Link 1</a>
						<a href="http://example.org/link-2">Link 2</a>
					</body>
				</html>',
                ]
            );

        add_filter( 'wp_safe_redirect', [ $this, 'return_empty_string' ] );
        add_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        $this->expectException( WPDieException::class );

        do_action( 'admin_post_wp_media_crawler_crawl_sitemap_links' );

        $home_file = new File( 'home.html' );

        $this->assertNotEmpty( SitemapLinksStorage::retrieve() );
        $this->assertTrue( $home_file->exists() );

        SitemapLinksStorage::delete();
        $home_file->delete();
    }

    public function tear_down() : void {
        if ( $this->user_id > 0 ) {
            wp_delete_user( $this->user_id );
            $this->user_id = 0;
        }
        unset( $_REQUEST['_wpnonce'] );

        remove_filter( 'wp_safe_redirect', [ $this, 'return_empty_string' ] );
        remove_filter( 'check_admin_referer', [ $this, 'get_wp_die_handler' ] );

        parent::tear_down();
    }
}
