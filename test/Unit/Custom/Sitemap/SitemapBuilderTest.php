<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 *
 * @phpcs:disable Squiz.Commenting.FunctionComment
 * @phpcs:disable Generic.Commenting.DocComment
 * @phpcs:disable WordPress.WP.AlternativeFunctions
 */

namespace WP_Media\Crawler\Tests\Unit\Custom\Sitemap;

use Brain\Monkey\Functions;
use \PHPUnit\Framework\TestCase;
use WP_Media\Crawler\Custom\Sitemap\SitemapBuilder;
use WP_Media\Crawler\Schemas\Link;

/**
 * @covers \WP_Media\Crawler\Custom\Sitemap\SitemapBuilder
 * @group Sitemap
 */
final class SitemapBuilderTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();

        if ( ! defined( 'WP_MEDIA_CRAWLER_PATH' ) ) {
            define( 'WP_MEDIA_CRAWLER_PATH', __DIR__ . '/../../../../' );
        }
    }

    public function test_build_links() : void {
        $links = [
            new Link( 'Link 1', 'http://example.com/link-1' ),
            new Link( 'Link 2', 'http://example.com/link-2' ),
        ];

        Functions\expect( 'bloginfo' );

        Functions\expect( 'esc_html_e' );

        Functions\expect( 'wp_kses' )
            ->once()
            ->andReturnUsing(
                function( $content ) {
                    return $content;
                }
            );

        $builder = new SitemapBuilder( $links );
        $result  = $builder->build();

        $this->assertStringContainsString( '<li><a href="http://example.com/link-1" title="Link 1">Link 1</a></li>', $result );
        $this->assertStringContainsString( '<li><a href="http://example.com/link-2" title="Link 2">Link 2</a></li>', $result );
    }

    public function test_build_links_without_links() : void {
        $links = [];

        $builder = new SitemapBuilder( $links );
        $result  = $builder->build();

        $this->assertEquals( '', $result );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
