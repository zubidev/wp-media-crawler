<?php
/**
 * Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

/**
 * Attributes of the template.
 *
 * @var \WP_Media\Crawler\Schemas\LinksRecord $wp_media_sitemap_links - The links Record.
 */

list(
    'sitemap_links' => $wp_media_sitemap_links,
    ) = $args;

?>

<div class="wrap">
    <h1>WP Media | Sitemap Manager</h1>
    <p><?php esc_html_e( 'This page allows you to manage and analyze the website sitemap.', 'wp-media-crawler' ); ?></p>
    <hr>
    <form method="POST" action="admin-post.php">
        <button type="submit" class="button button-primary">Crawl Sitemap Links</button>
        <input type="hidden" name="action" value="wp_media_crawler_crawl_sitemap_links">
        <?php wp_nonce_field( 'wp_media_crawler_crawl_sitemap_links' ); ?>
        <?php settings_errors( 'wp-media-crawler-sitemap-manager' ); ?>
    </form>

    <hr>

    <h2><?php esc_html_e( 'Overview:', 'wp-media-crawler' ); ?></h2>
    <p>
        <?php esc_html_e( 'The visitors can view the sitemap through the following page:', 'wp-media-crawler' ); ?>
        <a href="<?php echo esc_url( home_url( '/sitemap.html' ) ); ?>" target="_blank" title="<?php esc_attr_e( ' View sitemap', 'wp-media-crawler' ); ?>">
            <?php echo esc_html( home_url( '/sitemap.html' ) ); ?>
        </a>
    </p>

    <hr>

    <h2><?php esc_html_e( 'Sitemap Links:', 'wp-media-crawler' ); ?></h2>
    <?php if ( $wp_media_sitemap_links ) : ?>
        <p><?php esc_html_e( 'The following table shows the links found by the sitemap crawler.', 'wp-media-crawler' ); ?></p>
        <p>
            <?php
            echo esc_html(
                sprintf(
                /* translators: %s: the timestamp */
                    __( 'The last crawl happened at: %s', 'wp-media-crawler' ),
                    $wp_media_sitemap_links->get_formatted_timestamp()
                )
            );
            ?>
        </p>
        <p>
            <?php
            echo esc_html(
                sprintf(
                /* translators: %s: the total of links */
                    __( 'Total of links: %s', 'wp-media-crawler' ),
                    count( $wp_media_sitemap_links->links )
                )
            );
            ?>
        </p>
        <table class="wp-list-table widefat fixed striped" style="max-width: 1000px">
            <thead>
            <tr>
                <th><?php esc_html_e( 'Link Title', 'wp-media-crawler' ); ?></th>
                <th><?php esc_html_e( 'Link URL', 'wp-media-crawler' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $wp_media_sitemap_links->links as $wp_media_sitemap_link ) : ?>
                <tr>
                    <td><?php echo esc_html( $wp_media_sitemap_link->title ); ?></td>
                    <td>
                        <a href="<?php echo esc_url( $wp_media_sitemap_link->href ); ?>"
                           title="<?php echo esc_attr( $wp_media_sitemap_link->title ); ?>">
                            <?php echo esc_html( $wp_media_sitemap_link->get_href_with_domain() ); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e( 'There are no crawled links.', 'wp-media-crawler' ); ?></p>
        <p><?php esc_html_e( 'You can crawl the sitemap links by clicking on the button above.', 'wp-media-crawler' ); ?></p>
    <?php endif; ?>
</div>
