<?php
/**
 * Implemented by WP Media (https://github.com/wp-media).
 *
 * Adapted by zubidev (https://github.com/zubidev/)
 *
 * @package WP_Media_Crawler
 *
 * @see https://github.com/wp-media/wp-rocket/blob/develop/tests/Unit/FilesystemTestCase.php
 *
 * @phpcs:disable Squiz.Commenting.FunctionComment
 * @phpcs:disable Generic.Commenting.DocComment
 */

namespace WP_Media\Crawler\Tests\Unit;

use WP_Media\Crawler\Tests\VirtualFilesystemTrait;
use WPMedia\PHPUnit\Unit\VirtualFilesystemTestCase;

abstract class FilesystemTestCase extends VirtualFilesystemTestCase {
    use VirtualFilesystemTrait;

    protected function setUp(): void {
        parent::setUp();

        $this->initDefaultStructure();
        $this->init();
    }
}
