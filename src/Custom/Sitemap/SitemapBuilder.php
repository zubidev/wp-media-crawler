<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Sitemap;

use WP_Media\Crawler\Schemas\Link;

/**
 * Class SitemapBuilder. Builds the sitemap.html stricture.
 */
final class SitemapBuilder {

    /**
     * The sitemap links.
     *
     * @var Link[] $links
     */
    private $links;

    /**
     * Constructor method.
     *
     * @param Link[] $links The links to be added to the sitemap.
     */
    public function __construct( $links ) {
        $this->links = $links;
    }

    /**
     * Builds the sitemap.html structure.
     *
     * @return string The sitemap.html structure.
     */
    public function build() : string {

        if ( empty( $this->links ) ) {
            return '';
        }
        $links_list = $this->build_links_list();
        $links_list = $this->build_links_wrapper( $links_list );
        return $this->build_sitemap_html( $links_list );
    }

    /**
     * Builds the links list.
     *
     * @return string The links list.
     */
    private function build_links_list() : string {
        $links_list = '';
        foreach ( $this->links as $link ) {
            $links_list .= '<li><a href="' . $link->href . '" title="' . $link->title . '">' . $link->title . '</a></li>' . PHP_EOL;
        }
        return $links_list;
    }

    /**
     * Builds the links wrapper.
     *
     * @param string $links_list The links list.
     *
     * @return string The links wrapper.
     */
    private function build_links_wrapper( $links_list ) : string {
        return '<div class="sitemap-links-wrapper"><ul>' . $links_list . '</ul></div>';
    }

    /**
     * Builds the sitemap.html structure.
     *
     * @param string $links_wrapper The links wrapper.
     *
     * @return string The sitemap.html structure.
     */
    private function build_sitemap_html( $links_wrapper ) : string {
		$args                  = [];
        $args['links_wrapper'] = $links_wrapper;
        ob_start();
        include WP_MEDIA_CRAWLER_PATH . 'templates/public/sitemap.php';
        return ob_get_clean();
    }
}

