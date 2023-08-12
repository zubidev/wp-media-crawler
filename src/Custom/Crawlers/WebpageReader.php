<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Crawlers;

use WP_Media\Crawler\Exceptions\WebpageException;

/**
 * Class WebpageReader. Reads the return a webpage content.
 */
class WebpageReader {

    /**
     * Web page url
     *
     * @var string $url
     */
    protected $url;

    /**
     * Constructor method.
     *
     * @param string $url The URL to be crawled.
     */
    public function __construct( $url ) {
        $this->url = $url;
    }

    /**
     * Get the web page content
     *
     * @return string The web page content.
     *
     * @throws WebpageException If the page request returns an error.
     * @throws WebpageException If the response body is empty.
     */
    public function get_content() : string {
        $response = wp_remote_get( $this->url );

        if ( is_wp_error( $response ) ) {
            throw new WebpageException( __( 'The page isn\'t accessible.', 'wp-media-crawler' ), $this->url );
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $response_code ) {
            throw new WebpageException( __( 'The page isn\'t accessible.', 'wp-media-crawler' ), $this->url );
        }

        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            throw new WebpageException( __( 'The page\'s body is malformed.', 'wp-media-crawler' ), $this->url );
        }
        return $body;
    }
}
