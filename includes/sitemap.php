<?php
/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register sitemap rewrite rules.
 *
 * This function adds rewrite rules for generating sitemaps dynamically
 * based on the plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_register_sitemap() {
    $settings = get_option( 'seo_wp_settings', [] );

    if ( empty( $settings['enable_sitemaps'] ) ) {
        return; // Sitemaps are disabled.
    }

    add_rewrite_rule( '^sitemap\.xml$', 'index.php?sitemap=1', 'top' );
    add_rewrite_rule( '^sitemap-([^/]+)\.xml$', 'index.php?sitemap=1&post_type=$matches[1]', 'top' );
}
add_action( 'init', 'seo_wp_register_sitemap' );

/**
 * Add query vars for sitemaps.
 *
 * @since 1.0.0
 *
 * @param array $vars The current query vars.
 * @return array Modified query vars with sitemap-related variables.
 */
function seo_wp_add_sitemap_query_vars( $vars ) {
    $vars[] = 'sitemap';
    $vars[] = 'post_type';
    return $vars;
}
add_filter( 'query_vars', 'seo_wp_add_sitemap_query_vars' );

/**
 * Render the sitemap.
 *
 * This function generates and outputs the XML sitemap based on the query vars.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_sitemap() {
    $settings = get_option( 'seo_wp_settings', [] );

    if ( empty( $settings['enable_sitemaps'] ) ) {
        return; // Sitemaps are disabled.
    }

    if ( get_query_var( 'sitemap' ) ) {
        header( 'Content-Type: application/xml; charset=utf-8' );

        $post_type = get_query_var( 'post_type' );
        if ( $post_type ) {
            echo seo_wp_generate_post_type_sitemap( sanitize_text_field( $post_type ) );
        } else {
            echo seo_wp_generate_main_sitemap();
        }
        exit;
    }
}
add_action( 'template_redirect', 'seo_wp_render_sitemap' );

/**
 * Generate the main sitemap.
 *
 * This function generates the main sitemap XML, including links to all post type-specific sitemaps.
 *
 * @since 1.0.0
 * @return string XML content of the main sitemap.
 */
function seo_wp_generate_main_sitemap() {
    $home_url = esc_url( home_url( '/' ) );
    $sitemap  = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Add homepage to the sitemap.
    $sitemap .= '<url>';
    $sitemap .= '<loc>' . $home_url . '</loc>';
    $sitemap .= '<lastmod>' . esc_html( date( 'c' ) ) . '</lastmod>';
    $sitemap .= '<changefreq>daily</changefreq>';
    $sitemap .= '<priority>1.0</priority>';
    $sitemap .= '</url>';

    // Add links to individual sitemaps.
    $post_types = get_post_types( [ 'public' => true ], 'objects' );
    foreach ( $post_types as $post_type ) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . esc_url( home_url( "/sitemap-{$post_type->name}.xml" ) ) . '</loc>';
        $sitemap .= '<lastmod>' . esc_html( date( 'c' ) ) . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';
    }

    $sitemap .= '</urlset>';
    return $sitemap;
}

/**
 * Generate a sitemap for a specific post type.
 *
 * This function generates the XML sitemap for a specific post type, including all published posts.
 *
 * @since 1.0.0
 *
 * @param string $post_type The post type for which the sitemap is generated.
 * @return string XML content of the post type-specific sitemap.
 */
function seo_wp_generate_post_type_sitemap( $post_type ) {
    $posts = get_posts( [
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ] );

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ( $posts as $post ) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . esc_url( get_permalink( $post ) ) . '</loc>';
        $sitemap .= '<lastmod>' . esc_html( get_the_modified_date( 'c', $post ) ) . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';
    }

    $sitemap .= '</urlset>';
    return $sitemap;
}
