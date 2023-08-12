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

namespace WP_Media\Crawler\Tests\Unit\Custom\Crawlers;

use Brain\Monkey\Functions;
use Mockery;
use \PHPUnit\Framework\TestCase;
use WP_Media\Crawler\Custom\Crawlers\WebpageReader;
use WP_Media\Crawler\Exceptions\WebpageException;

/**
 * @covers \WP_Media\Crawler\Custom\Crawlers\WebpageReader
 * @group Crawlers
 */
final class WebpageReaderTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function test_request_exception() : void {
        $mock_url  = 'http://example.com';
        $error_msg = 'The page isn\'t accessible.';

        $this->expectException( WebpageException::class );
        $this->expectExceptionMessage( $error_msg );

        Mockery::mock( 'overload:WP_Error' );

        Functions\expect( 'wp_remote_get' )
            ->with( $mock_url )
            ->andReturn( new \WP_Error( 'error', 'error' ) );

        Functions\expect( '__' )
            ->once()
            ->andReturn( $error_msg );

        $crawler = new WebpageReader( $mock_url );
        $crawler->get_content();
    }

    public function test_response_code_exception() : void {
        $mock_url      = 'http://example.com';
        $error_msg     = 'The page isn\'t accessible.';
        $response_body = [ 'response' => [ 'code' => 404 ] ];

        $this->expectException( WebpageException::class );
        $this->expectExceptionMessage( $error_msg );

        Functions\expect( 'wp_remote_get' )
            ->with( $mock_url )
            ->andReturn( $response_body );

        Functions\expect( 'wp_remote_retrieve_response_code' )
            ->with( $response_body )
            ->andReturn( 404 );

        Functions\expect( '__' )
            ->once()
            ->andReturn( $error_msg );

        $crawler = new WebpageReader( $mock_url );
        $crawler->get_content();
    }

    public function test_response_body_exception() : void {
        $mock_url      = 'http://example.com';
        $error_msg     = 'The page\'s body is malformed.';
        $response_body = [ 'response' => [ 'code' => 200 ] ];

        $this->expectException( WebpageException::class );
        $this->expectExceptionMessage( $error_msg );

        Functions\expect( 'wp_remote_get' )
            ->with( $mock_url )
            ->andReturn( $response_body );

        Functions\expect( 'wp_remote_retrieve_response_code' )
            ->with( $response_body )
            ->andReturn( 200 );

        Functions\expect( 'wp_remote_retrieve_body' )
            ->with( [] )
            ->andReturn( '' );

        Functions\expect( '__' )
            ->once()
            ->andReturn( $error_msg );

        $crawler = new WebpageReader( $mock_url );
        $crawler->get_content();
    }

    public function test_response_200_and_with_filled_body() : void {
        $mock_url      = 'http://example.com';
        $response_body = [
            'response' => [ 'code' => 200 ],
            'body'     => '<html></html>',
        ];

        Functions\expect( 'wp_remote_get' )
            ->with( $mock_url )
            ->andReturn( $response_body );

        Functions\expect( 'wp_remote_retrieve_response_code' )
            ->with( $response_body )
            ->andReturn( 200 );

        Functions\expect( 'wp_remote_retrieve_body' )
            ->with( [] )
            ->andReturn( '<html></html>' );

        $crawler = new WebpageReader( $mock_url );
        $result  = $crawler->get_content();

        $this->assertEquals( '<html></html>', $result );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
