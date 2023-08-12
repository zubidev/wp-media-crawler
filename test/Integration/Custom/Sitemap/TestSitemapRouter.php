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

namespace WP_Media\Crawler\Tests\Integration\Custom\Sitemap;

use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;
use WP_Media\Crawler\Schemas\Link;
use WP_Media\Crawler\Tests\Fixtures\Custom\Sitemap\SitemapRouterDouble;
use WPMedia\PHPUnit\Integration\TestCase;

/**
 * @covers WP_Media\Crawler\Custom\Sitemap\SitemapRouter
 *
 * @group Sitemap
 */
class TestSitemapRouter extends TestCase {

    private static $class_instance;

    public static function set_up_before_class() : void {
        parent::set_up_before_class();

        require_once WP_MEDIA_TESTS_FIXTURES_DIR . '/Custom/Sitemap/SitemapRouterDouble.php';

        self::$class_instance = new SitemapRouterDouble();

        SitemapLinksStorage::delete();
    }

    public function test_redirect_with_a_generic_wp_query() : void {
        self::$class_instance->redirect( new \WP_Query() );

        // Expect an empty page, as void methods return nothing.
        $this->expectOutputString( '' );
    }

    public function test_redirect_without_query_var() : void {
        self::$class_instance->redirect( $GLOBALS['wp_the_query'] );

        // Expect an empty page, as void methods return nothing.
        $this->expectOutputString( '' );
    }

    public function test_redirect_for_not_stored_sitemap() : void {
        set_query_var( 'wp_media_sitemap', '1' );

        self::$class_instance->redirect( $GLOBALS['wp_the_query'] );

        // Expect an empty page (404) to be returned.
        $this->expectOutputString( '' );
    }

    public function test_redirect_for_empty_sitemap() : void {
        set_query_var( 'wp_media_sitemap', '1' );

        SitemapLinksStorage::store( [] );

        self::$class_instance->redirect( $GLOBALS['wp_the_query'] );

        // Expect an empty page (404) to be returned.
        $this->expectOutputString( '' );
    }

    public function test_redirect_successfully() : void {
        set_query_var( 'wp_media_sitemap', '1' );

        SitemapLinksStorage::store(
            [
                new Link( 'Link 1', 'http://example.org/link-1' ),
                new Link( 'Link 2', 'http://example.org/link-2' ),
            ]
        );

        ob_start();
        self::$class_instance->redirect( $GLOBALS['wp_the_query'] );
        $output = ob_get_clean();

        $this->assertStringContainsString( 'href="http://example.org/link-1"', $output );
        $this->assertStringContainsString( 'href="http://example.org/link-2"', $output );
    }

    public function tear_down() : void {
        set_query_var( 'wp_media_sitemap', null );

        SitemapLinksStorage::delete();

        parent::tear_down();
    }
}
