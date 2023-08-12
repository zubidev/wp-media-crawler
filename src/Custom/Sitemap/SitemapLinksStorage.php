<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Sitemap;

use WP_Media\Crawler\Schemas\Link;
use WP_Media\Crawler\Schemas\LinksRecord;

/**
 * Class SitemapLinksStorage. Handle the CRUD of the sitemaps links.
 */
final class SitemapLinksStorage {

    /**
     * Stores the sitemap links.
     *
     * @param Link[] $links The links to be stored.
     */
    public static function store( $links ) : void {
        $links_record = new LinksRecord( $links, time() );
        update_option( 'wp_media_crawler_sitemap_links', $links_record->serialize() );
    }

    /**
     * Retrieves the sitemap links.
     *
     * @return LinksRecord|null The sitemap links.
     */
    public static function retrieve() : ?LinksRecord {
        $stored_links = get_option( 'wp_media_crawler_sitemap_links', [] );

        if ( ! isset( $stored_links['links'] ) || ! isset( $stored_links['timestamp'] ) ) {
            return null;
        }

        if ( ! is_numeric( $stored_links['timestamp'] ) ) {
            return null;
        }

        $links = [];
        if ( is_array( $stored_links['links'] ) ) {
            foreach ( $stored_links['links'] as $link ) {
                if ( isset( $link['title'] ) && isset( $link['href'] ) ) {
                    $links[] = new Link( $link['title'], $link['href'] );
                }
            }
        }

        return new LinksRecord( $links, $stored_links['timestamp'] );
    }

    /**
     * Deletes the sitemap links.
     */
    public static function delete() : void {
        delete_option( 'wp_media_crawler_sitemap_links' );
    }
}
