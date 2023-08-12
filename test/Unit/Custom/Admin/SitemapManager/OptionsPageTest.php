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

namespace WP_Media\Crawler\Tests\Unit\Custom\Admin\SitemapManager;

use Brain\Monkey\Functions;
use \PHPUnit\Framework\TestCase;
use WP_Media\Crawler\Custom\Admin\SitemapManager\OptionsPage;

/**
 * @covers \WP_Media\Crawler\Custom\Admin\SitemapManager
 * @group Admin
 */
final class OptionsPageTest extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function test_add_options_page() : void {
        Functions\expect( 'add_submenu_page' )
            ->once()
            ->with(
                'tools.php',
                'WP Media | Sitemap Manager',
                'WP Media Sitemap',
                'administrator',
                'wp-media-crawler-sitemap-manager',
                [
                    OptionsPage::class,
                    'render_options_page',
                ]
            );

        Functions\expect( '__' )
            ->twice()
            ->andReturnFirstArg();

        OptionsPage::add_options_page();

        $this->assertTrue( true );
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }
}
