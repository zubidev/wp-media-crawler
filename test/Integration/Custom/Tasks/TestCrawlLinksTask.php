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

namespace WP_Media\Crawler\Tests\Integration\Custom\Tasks;

use Brain\Monkey\Functions;
use WP_Media\Crawler\Custom\Filesystem\File;
use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;
use WP_Media\Crawler\Custom\Tasks\CrawlLinksTask;
use WPMedia\PHPUnit\Integration\TestCase;

/**
 * @covers WP_Media\Crawler\Custom\Tasks\CrawlLinksTask
 * @covers WP_Media\Crawler\Custom\Crawlers\WebpageReader
 * @covers WP_Media\Crawler\Custom\Crawlers\LinksCrawler
 * @covers WP_Media\Crawler\Custom\Filesystem\File
 * @covers WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage
 *
 * @group Tasks
 */
class TestCrawlLinksTask extends TestCase {

    public static $tmp_file;

    public static $error_log_config;

    private static $class_instance;

    public static function set_up_before_class() : void {
        parent::set_up_before_class();
        self::$class_instance = new CrawlLinksTask( 'crawl_links_task', 'hourly' );

        self::$tmp_file = tmpfile();
        // phpcs:ignore WordPress.PHP.IniSet.Risky
        self::$error_log_config = ini_set( 'error_log', stream_get_meta_data( self::$tmp_file )['uri'] );
    }

    public static function tear_down_after_class() : void {
        parent::tear_down_after_class();
        // phpcs:ignore WordPress.PHP.IniSet.Risky
        ini_set( 'error_log', self::$error_log_config );
    }

    public function set_up() : void {
        parent::set_up();
        add_filter( 'wp_doing_cron', [ $this, 'is_doing_cron' ] );
    }

    public function test_task_running_out_of_the_cron() : void {
        add_filter( 'wp_doing_cron', [ $this, 'is_not_doing_cron' ] );
        Functions\expect( 'wp_remote_get' )->never();

        self::$class_instance->run();

        $this->expectOutputString( '' );
    }

    public function test_task_fails_because_of_remote_request_exception() : void {
        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andReturn( new \WP_Error( 'http_request_failed', 'Generic Error.' ) );

        self::$class_instance->run();

        $this->assertStringContainsString( $this->get_expected_error_msg( 'The page isn\'t accessible.' ), $this->get_error_log() );
    }

    public function test_task_fails_because_there_are_no_internal_links() : void {
        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andReturn(
                [
                    'response' => [ 'code' => 200 ],
                    'body'     => '<html></html>',
                ]
            );

        self::$class_instance->run();

        $this->assertStringContainsString( $this->get_expected_error_msg( 'The page doesn\'t have any internal link.' ), $this->get_error_log() );
    }

    public function test_task_fails_by_generic_error() : void {
        Functions\expect( 'wp_remote_get' )
            ->once()
            ->andThrow( new \Exception( 'Generic Error.' ) );

        self::$class_instance->run();

        $this->assertStringContainsString( $this->get_expected_error_msg( 'Generic Error.' ), $this->get_error_log() );
    }

    public function test_task_successfully_executed() : void {
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

        self::$class_instance->run();

        $home_file = new File( 'home.html' );

        $this->assertNotEmpty( SitemapLinksStorage::retrieve() );
        $this->assertTrue( $home_file->exists() );

        SitemapLinksStorage::delete();
        $home_file->delete();
    }

    public function is_doing_cron() : bool {
        return true;
    }

    public function is_not_doing_cron() : bool {
        return false;
    }

    private function get_expected_error_msg( $msg ) : string {
        return 'The following ocurred while crawling the links: ' . $msg;
    }

    private function get_error_log() : string {
        return stream_get_contents( self::$tmp_file ) ?? '';
    }

    public function tear_down() : void {
        remove_filter( 'wp_doing_cron', [ $this, 'is_doing_cron' ] );
        remove_filter( 'wp_doing_cron', [ $this, 'is_not_doing_cron' ] );

        parent::tear_down();
    }
}
