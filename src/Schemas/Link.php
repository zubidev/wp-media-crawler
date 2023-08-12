<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Schemas;

/**
 * Class Link. Stores the link data.
 */
class Link {

    /**
     * The link title.
     *
     * @var string $title
     */
    public $title;

    /**
     * The link URL.
     *
     * @var string $href
     */
    public $href;

    /**
     * Constructor method.
     *
     * @param string $title The link title.
     * @param string $href  The link URL.
     */
    public function __construct( $title, $href ) {
        $this->title = $title;
        $this->href  = $href;
    }

    /**
     * Returns the link href with the URL domain.
     *
     * @return string The link href with the URL domain.
     */
    public function get_href_with_domain() : string {
        $has_domain = wp_parse_url( $this->href, PHP_URL_HOST );
        return empty( $has_domain ) ? home_url( $this->href ) : $this->href;
    }
}
