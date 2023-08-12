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
 */

namespace WP_Media\Crawler\Tests\Unit\Custom\Filesystem;

use Brain\Monkey\Functions;
use WP_Media\Crawler\Custom\Filesystem\File;
use WP_Media\Crawler\Tests\Unit\FilesystemTestCase;

/**
 * @covers \WP_Media\Crawler\Custom\Filesystem\File
 * @group Filesystem
 */
final class FileTest extends FilesystemTestCase {

    protected $path_to_test_data = '/Custom/Filesystem/File.php';

    public function test_save() : void {
        $html = '<html><body><h1>Home</h1></body></html>';

        Functions\expect( 'wp_upload_dir' )
            ->twice()
            ->andReturn( [ 'basedir' => 'vfs://public/wp-content/uploads' ] );

        $file = new File( 'home.html', $this->filesystem );
        $file->save( $html, $this->filesystem );

        $this->assertFileExists( 'vfs://public/wp-content/uploads/wp-media/home.html' );
        $this->assertSame( $html, $this->filesystem->get_contents( 'vfs://public/wp-content/uploads/wp-media/home.html' ) );
    }

    public function test_save_without_the_base_folder() : void {
        $html = '<html><body><h1>Home</h1></body></html>';

        Functions\expect( 'wp_upload_dir' )
            ->twice()
            ->andReturn( [ 'basedir' => 'vfs://public/wp-content/uploads-no-folder-test' ] );

        $file = new File( 'home.html', $this->filesystem );
        $file->save( $html, $this->filesystem );

        $this->assertFileExists( 'vfs://public/wp-content/uploads-no-folder-test/wp-media/home.html' );
        $this->assertSame( $html, $this->filesystem->get_contents( 'vfs://public/wp-content/uploads-no-folder-test/wp-media/home.html' ) );
    }

    public function test_save_odd_path() : void {
        $html = '<html><body><h1>Home</h1></body></html>';

        Functions\expect( 'wp_upload_dir' )
            ->twice()
            ->andReturn( [ 'basedir' => 'vfs://public/wp-content/uploads' ] );

        $file = new File( '../../../wp-admin/home.html', $this->filesystem );
        $file->save( $html, $this->filesystem );

        $this->assertFileExists( 'vfs://public/wp-content/uploads/wp-media/home.html' );
        $this->assertSame( $html, $this->filesystem->get_contents( 'vfs://public/wp-content/uploads/wp-media/home.html' ) );
    }

    public function test_exists() : void {
        $basedir = 'vfs://public/wp-content/uploads-file-exists-test';

        Functions\expect( 'wp_upload_dir' )
            ->once()
            ->andReturn( [ 'basedir' => $basedir ] );

        $file   = new File( 'home.html', $this->filesystem );
        $result = $file->exists( $basedir . '/wp-media/home.html' );

        $this->assertTrue( $result );
    }

    public function test_do_not_exists() : void {
        $basedir = 'vfs://public/wp-content/uploads-file-not-exists-test';

        Functions\expect( 'wp_upload_dir' )
            ->once()
            ->andReturn( [ 'basedir' => $basedir ] );

        $file   = new File( 'home.html', $this->filesystem );
        $result = $file->exists( $basedir . '/wp-media/home.html' );

        $this->assertFalse( $result );
    }

    public function test_get_file_contents() : void {
        $basedir = 'vfs://public/wp-content/uploads-file-exists-test';

        Functions\expect( 'wp_upload_dir' )
            ->once()
            ->andReturn( [ 'basedir' => $basedir ] );

        $file   = new File( 'home.html', $this->filesystem );
        $result = $file->get( $basedir . '/wp-media/home.html' );

        $this->assertStringEqualsFile( $basedir . '/wp-media/home.html', $result );
    }

    public function test_get_file_contents_without_file() : void {
        $basedir = 'vfs://public/wp-content/uploads-file-not-exists-test';

        Functions\expect( 'wp_upload_dir' )
            ->once()
            ->andReturn( [ 'basedir' => $basedir ] );

        $file   = new File( 'home.html', $this->filesystem );
        $result = $file->get( $basedir . '/wp-media/home.html' );

        $this->assertEquals( '', $result );
    }
}
