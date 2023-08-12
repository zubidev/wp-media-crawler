<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 *
 * @phpcs:disable Generic.Commenting.DocComment
 * @phpcs:disable Squiz.Commenting.FunctionComment
 */

namespace WP_Media\Crawler\Tests\Integration;

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * @group Activation
 */
class TestPluginActivation extends TestCase {

    public function testLoadingInitClass() {
        $this->assertTrue( class_exists( 'WP_Media\Crawler\Init' ) );
    }
}
