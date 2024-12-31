<?php
/**
 * SEO for WordPress - Breadcrumbs Generator
 *
 * @package SEO_WP_Breadcrumbs
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate breadcrumbs for the current page.
 *
 * @since  1.0.0
 * @return string Breadcrumbs HTML markup.
 */
function seo_wp_generate_breadcrumbs() {
    if ( is_front_page() || is_home() ) {
        return ''; // No breadcrumbs on the front page or blog home.
    }

    $settings           = get_option( 'seo_wp_advanced_settings', [] );
    $separator          = isset( $settings['breadcrumbs_separator'] ) && trim( $settings['breadcrumbs_separator'] ) !== '' 
        ? ' ' . esc_html( $settings['breadcrumbs_separator'] ) . ' ' 
        : ' - '; // Default to " - " if not set.
    $home_text          = ! empty( $settings['breadcrumbs_home'] ) 
        ? esc_html( $settings['breadcrumbs_home'] ) 
        : __( 'Home', 'seo-for-wordpress' );
    $breadcrumbs_prefix = isset( $settings['breadcrumbs_prefix'] ) ? esc_html( $settings['breadcrumbs_prefix'] ) : '';
    $bold_current       = ! empty( $settings['breadcrumbs_bold'] );

    $breadcrumbs = [];
    $breadcrumbs[] = sprintf(
        '<a href="%s">%s</a>',
        esc_url( home_url() ),
        esc_html( $home_text )
    );

    if ( is_singular() ) {
        global $post;
        $post_ancestors = array_reverse( get_post_ancestors( $post ) );

        foreach ( $post_ancestors as $ancestor_id ) {
            $breadcrumbs[] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( get_permalink( $ancestor_id ) ),
                esc_html( get_the_title( $ancestor_id ) )
            );
        }

        if ( $bold_current ) {
            $breadcrumbs[] = sprintf( '<strong>%s</strong>', esc_html( get_the_title() ) );
        } else {
            $breadcrumbs[] = esc_html( get_the_title() );
        }
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();

        if ( $term && $term->parent ) {
            $parent_terms = get_ancestors( $term->term_id, $term->taxonomy );

            foreach ( array_reverse( $parent_terms ) as $parent_id ) {
                $parent = get_term( $parent_id );
                if ( $parent && ! is_wp_error( $parent ) ) {
                    $breadcrumbs[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url( get_term_link( $parent ) ),
                        esc_html( $parent->name )
                    );
                }
            }
        }

        $breadcrumbs[] = esc_html( single_term_title( '', false ) );
    } elseif ( is_archive() ) {
        $breadcrumbs[] = esc_html( get_the_archive_title() );
    } elseif ( is_search() ) {
        $breadcrumbs[] = sprintf( 
            __( 'Search Results for: %s', 'seo-for-wordpress' ), 
            esc_html( get_search_query() ) 
        );
    } elseif ( is_404() ) {
        $breadcrumbs[] = __( '404 Not Found', 'seo-for-wordpress' );
    }

    // Generate output.
    $output = '<nav class="seo-wp-breadcrumbs" aria-label="breadcrumb">';
    if ( $breadcrumbs_prefix ) {
        $output .= '<span class="breadcrumbs-prefix">' . esc_html( $breadcrumbs_prefix ) . '</span> ';
    }
    $output .= implode( $separator, $breadcrumbs );
    $output .= '</nav>';

    return $output;
}

/**
 * Add breadcrumbs to theme locations.
 *
 * @since  1.0.0
 */
add_action( 'seo_wp_breadcrumbs_hook', 'seo_wp_generate_breadcrumbs' );

/**
 * Register the breadcrumbs shortcode.
 *
 * @since  1.0.0
 * @return string Breadcrumbs HTML markup.
 */
function seo_wp_breadcrumbs_shortcode() {
    return seo_wp_generate_breadcrumbs();
}
add_shortcode( 'seo_breadcrumbs', 'seo_wp_breadcrumbs_shortcode' );
