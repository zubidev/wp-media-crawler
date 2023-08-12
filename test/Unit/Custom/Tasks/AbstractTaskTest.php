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
use WP_Media\Crawler\Custom\Tasks\CrawlLinksTask;

/**
 * @covers \WP_Media\Crawler\Custom\Tasks\AbstractTask
 * @group Tasks
 */
final class AbstractTaskTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();

        if ( ! defined( 'WP_MEDIA_CRAWLER_FILE' ) ) {
            define( 'WP_MEDIA_CRAWLER_FILE', __FILE__ );
        }
    }

    public function test_the_task_instantiation() : void {
        $event_name = 'event_name';

        Functions\expect( 'register_activation_hook' )
            ->once();
        Functions\expect( 'register_deactivation_hook' )
            ->once();

        new CrawlLinksTask( $event_name, 'hourly' );

        self::assertSame( 10, has_action( $event_name, CrawlLinksTask::class . '->run()' ) );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
