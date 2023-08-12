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
use WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage;
use WP_Media\Crawler\Schemas\Link;
use WP_Media\Crawler\Schemas\LinksRecord;

/**
 * @covers \WP_Media\Crawler\Custom\Sitemap\SitemapLinksStorage
 * @group Sitemap
 */
final class SitemapLinksStorageTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function test_store() : void {
        $timestamp = time();

        $links = [
            new Link( 'Link 1', 'http://example.com/link-1' ),
            new Link( 'Link 2', 'http://example.com/link-2' ),
        ];

        Functions\stubs( [ 'time' => $timestamp ] );

        $expect_stored_object = new LinksRecord( $links, $timestamp );

        Functions\expect( 'update_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links', $expect_stored_object->serialize() );

        SitemapLinksStorage::store( $links );

        $this->assertTrue( true );
    }

    public function test_retrieve() : void {
        $timestamp = time();

        $stored_object = [
            'timestamp' => $timestamp,
            'links'     => [
                [
                    'title' => 'Link 1',
                    'href'  => 'http://example.com/link-1',
                ],
                [
                    'title' => 'Link 2',
                    'href'  => 'http://example.com/link-2',
                ],
            ],
        ];

        Functions\expect( 'get_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links', [] )
            ->andReturn( $stored_object );

        $links = SitemapLinksStorage::retrieve();

        $expected_links = new LinksRecord(
            [
                new Link( 'Link 1', 'http://example.com/link-1' ),
                new Link( 'Link 2', 'http://example.com/link-2' ),
            ],
            $timestamp
        );

        $this->assertEquals( $expected_links, $links );
    }

    public function test_retrieve_without_links() : void {
        $timestamp     = time();
        $stored_object = [
            'timestamp' => $timestamp,
        ];

        Functions\expect( 'get_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links', [] )
            ->andReturn( $stored_object );

        $links = SitemapLinksStorage::retrieve();

        $this->assertNull( $links );
    }

    public function test_retrieve_without_timestamp() : void {
        $stored_object = [
            'links' => [
                [
                    'title' => 'Link 1',
                    'href'  => 'http://example.com/link-1',
                ],
            ],
        ];

        Functions\expect( 'get_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links', [] )
            ->andReturn( $stored_object );

        $links = SitemapLinksStorage::retrieve();

        $this->assertNull( $links );
    }

    public function test_retrieve_badly_stored_timestamp() : void {
        $stored_object = [
            'timestamp' => '2023-07-03 00:00:00',
            'links'     => [
                [
                    'title' => 'Link 1',
                    'href'  => 'http://example.com/link-1',
                ],
            ],
        ];

        Functions\expect( 'get_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links', [] )
            ->andReturn( $stored_object );

        $links = SitemapLinksStorage::retrieve();

        $this->assertNull( $links );
    }

    public function test_retrieve_badly_stored_links() : void {
        $timestamp     = time();
        $stored_object = [
            'timestamp' => $timestamp,
            'links'     => [
                [
                    'title' => 'Link 1',
                ],
                [
                    'titles' => 'Link 2',
                    'uri'    => 'http://example.com/link-2',
                ],
            ],
        ];

        Functions\expect( 'get_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links', [] )
            ->andReturn( $stored_object );

        $links = SitemapLinksStorage::retrieve();

        $this->assertEquals( new LinksRecord( [], $timestamp ), $links );
    }

    public function test_delete() : void {
        Functions\expect( 'delete_option' )
            ->once()
            ->with( 'wp_media_crawler_sitemap_links' );

        SitemapLinksStorage::delete();

        $this->assertTrue( true );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
