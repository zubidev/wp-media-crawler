<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Schemas;

/**
 * Class LinksRecord. Stores a record of links.
 */
class LinksRecord {

    /**
     * The list of links.
     *
     * @var Link[] $links
     */
    public $links;

    /**
     * The links record timestamp.
     *
     * @var int $timestamp
     */
    public $timestamp;

    /**
     * Constructor method.
     *
     * @param Link[] $links The list of links.
     * @param int    $timestamp The links record timestamp.
     */
    public function __construct( $links, $timestamp ) {
        $this->links     = $links;
        $this->timestamp = $timestamp;
    }

    /**
     * Returns the formatted timestamp.
     *
     * @param string $format The timestamp format.
     *
     * @return string
     */
    public function get_formatted_timestamp( $format = 'Y-m-d H:i:s' ) : string {
        return wp_date( $format, $this->timestamp );
    }

    /**
     * Return the object structured data as an array.
     *
     * @return array
     */
    public function serialize() : array {
        $links = array_map(
            function( $link ) {
                return (array) $link;
            },
            $this->links
        );
        return [
            'links'     => $links,
            'timestamp' => $this->timestamp,
        ];
    }
}
