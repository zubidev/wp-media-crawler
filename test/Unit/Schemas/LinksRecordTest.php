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
use WP_Media\Crawler\Schemas\Link;
use WP_Media\Crawler\Schemas\LinksRecord;

/**
 * @covers \WP_Media\Crawler\Schemas\LinksRecord
 * @group Crawlers
 */
final class LinksRecordTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function test_get_formatted_timestamp() : void {
        $timestamp = time();

        Functions\expect( 'wp_date' )
            ->once()
            ->with( 'Y-m-d H:i:s', $timestamp )
            ->andReturn( '' );

        $links_record = new LinksRecord( [], time() );
        $links_record->get_formatted_timestamp();

        $this->assertTrue( true );
    }

    public function test_serialize() : void {
        $timestamp = time();

        $links_record = new LinksRecord( [ new Link( 'Link 1', 'http://exemple.com/link-1' ) ], $timestamp );
        $serialized   = $links_record->serialize();

        $expected_result = [
            'timestamp' => $timestamp,
            'links'     => [
                [
                    'title' => 'Link 1',
                    'href'  => 'http://exemple.com/link-1',
                ],
            ],
        ];
        $this->assertEquals( $serialized, $expected_result );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
