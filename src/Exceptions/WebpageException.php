<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Exceptions;

use Exception;

/**
 * Class WebpageException. Exception thrown when the web page is not accessible.
 */
class WebpageException extends Exception {

    /**
     * The webpage url
     *
     * @var string $url
     */
    private $url;

    /**
     * WebpageException constructor.
     *
     * @param string          $message The exception message.
     * @param string          $url The URL of the web page.
     * @param int             $code The exception code.
     * @param \Exception|null $previous The previous exception used for the exception chaining.
     */
    public function __construct( $message, $url, $code = 0, Exception $previous = null ) {
        $this->url = $url;
        parent::__construct( $message, $code, $previous );
    }

    /**
     * Return the error msg.
     *
     * @return string
     */
    public function get_error_message_html() : string {
        $output  = $this->getMessage();
        $output .= '<br>';
        $output .= esc_html__( 'Check the following page and try again later. Requested page: ', 'wp-media-crawler' );
        $output .= '<a href="' . esc_url( $this->url ) . '" target="_blank" title="' . esc_html__( 'Requested page', 'wp-media-crawler' ) . '">' . esc_url( $this->url ) . '</a>';
        return wp_kses(
            $output,
            [
                'br' => [],
                'a'  => [
                    'href'   => [],
                    'target' => [],
                    'title'  => [],
                ],
            ]
        );
    }
}
