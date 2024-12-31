<?php
/**
 * Prevent direct access to the file.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamically modify the robots.txt output.
 *
 * @param string $output The existing robots.txt content.
 * @param bool   $public Whether the site is public.
 * 
 * @since  1.0.0
 * @return string Modified robots.txt content.
 */
function seo_wp_dynamic_robots_txt( $output, $public ) {
    // Respect site visibility settings; if the site is not public, do not modify.
    if ( ! $public ) {
        return $output;
    }

    // Retrieve SEO settings.
    $settings = get_option( 'seo_wp_settings', [] );

    // Add sitemap link to robots.txt.
    $sitemap_url = esc_url( home_url( '/sitemap.xml' ) );
    $output     .= "Sitemap: $sitemap_url\n";

    // Add custom robots.txt rules if defined in settings.
    if ( ! empty( $settings['custom_robots_txt'] ) ) {
        $custom_rules = sanitize_textarea_field( $settings['custom_robots_txt'] );
        $output      .= "\n" . $custom_rules . "\n";
    }

    return $output;
}
add_filter( 'robots_txt', 'seo_wp_dynamic_robots_txt', 10, 2 );
