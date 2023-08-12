<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Crawlers;

use \Symfony\Component\DomCrawler\Crawler;
use WP_Media\Crawler\Exceptions\CrawlerException;
use WP_Media\Crawler\Schemas\Link;

/**
 * Class LinksCrawler. Crawls the links of a Web page.
 */
final class LinksCrawler implements ICrawler {

    /**
     * The web content to crawl
     *
     * @var string $html
     */
    private $html;

    /**
     * The links list
     *
     * @var Link[] $links
     */
    private $links;

    /**
     * The links set
     *
     * @var array $links_set
     */
    private $links_set;

    /**
     * Internal domain.
     *
     * @var string $internal_domain
     */
    private $internal_domain;

    /**
     * Constructor method.
     *
     * @param string $html - The web content to crawl.
     */
    public function __construct( $html ) {
        $this->html      = $html;
        $this->links     = [];
        $this->links_set = [];
    }

    /**
     * Crawls the links of a Webpage.
     *
     * @return Link[] The links found.
     *
     * @throws CrawlerException If the page doesn't have any link.
     */
    public function crawl() : array {
        if ( ! empty( $this->html ) ) {
            $crawler = new Crawler( $this->html );
            $crawler->filterXPath( '//a' )->each(
                function( Crawler $node ) {
                    $this->add_link( $node );
                }
            );
        }
        if ( empty( $this->links ) ) {
            throw new CrawlerException( __( 'The page doesn\'t have any internal link.', 'wp-media-crawler' ) );
        }
        return $this->links;
    }

    /**
     * Add found link to the crawled links list if it is well formed.
     *
     * @param Crawler $node - The link DOM node.
     */
    private function add_link( $node ) : void {
        $title = $node->text();
        $title = $title ? $title : $node->attr( 'title' );
        if ( empty( $title ) ) {
            return;
        }
        $href = $node->attr( 'href' );
        if ( $this->is_internal_link( $href ) && $this->is_link_unique( $href ) ) {
            $this->links[] = new Link( $title, $href );
        }
    }

    /**
     * Checks if the link is internal.
     *
     * @param string $link - The link to check.
     *
     * @return bool - True if the link is internal, false otherwise.
     */
    private function is_internal_link( $link ) : bool {
        $internal_domain = $this->get_internal_domain();
        $domain          = $this->get_domain( $link );
        // If the domain is empty, it is an internal link.
        if ( ! $domain ) {
            return true;
        }
        return $internal_domain === $this->get_domain( $link );
    }

    /**
     * Returns the internal domain.
     *
     * @return string - The internal domain.
     */
    private function get_internal_domain() : string {
        if ( ! $this->internal_domain ) {
            $this->internal_domain = $this->get_domain( get_site_url() );
        }
        return $this->internal_domain;
    }

    /**
     * Returns the domain of an url.
     *
     * @param string $url - The url.
     *
     * @return string - The domain of the url.
     */
    private function get_domain( $url ) : string {
        $domain = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $domain ) {
            return '';
        }
        return $domain;
    }

    /**
     * Checks if the link is unique.
     *
     * @param string $link - The link to check.
     *
     * @return bool - True if the link is unique, false otherwise.
     */
    private function is_link_unique( $link ) : bool {
        $host = wp_parse_url( $link, PHP_URL_HOST );
        if ( ! empty( $host ) ) {
            $splitted_link = explode( $host, $link );
            $link          = end( $splitted_link );
        }
        if ( isset( $this->links_set[ $link ] ) ) {
            return false;
        }
        $this->links_set[ $link ] = true;
        return true;
    }
}
