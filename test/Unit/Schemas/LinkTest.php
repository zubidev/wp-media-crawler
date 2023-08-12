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

namespace WP_Media\Crawler\Tests\Unit\Schemas;

use Brain\Monkey\Functions;
use \PHPUnit\Framework\TestCase;
use WP_Media\Crawler\Custom\Sitemap\SitemapBuilder;
use WP_Media\Crawler\Schemas\Link;

/**
 * @covers \WP_Media\Crawler\Schemas\Link
 * @group Crawlers
 */
final class LinkTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function test_get_href_with_domain_when_there_is_no_domain() : void {
        $href = '/link-1';

        Functions\expect( 'wp_parse_url' )
            ->once()
            ->with( $href, PHP_URL_HOST )
            ->andReturnUsing(
                function( $href ) {
                    return parse_url( $href, PHP_URL_HOST );
                }
            );

        Functions\expect( 'home_url' )
            ->once()
            ->with( $href )
            ->andReturn( 'http://example.com/link-1' );

        $link = new Link( 'Link 1', '/link-1' );

        $this->assertEquals( 'http://example.com/link-1', $link->get_href_with_domain() );
    }

    public function test_get_href_with_domain_when_there_is_a_domain() : void {
        $href = 'http://example.com/link-1';

        Functions\expect( 'wp_parse_url' )
            ->once()
            ->with( $href, PHP_URL_HOST )
            ->andReturnUsing(
                function( $href ) {
                    return parse_url( $href, PHP_URL_HOST );
                }
            );

        $link = new Link( 'Link 1', $href );

        $this->assertEquals( $href, $link->get_href_with_domain() );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
