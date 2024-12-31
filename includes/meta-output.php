<?php
/**
 * SEO for WordPress - Output Meta Tags
 *
 * @package SEO_WP_Meta
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output SEO meta tags in wp_head.
 *
 * @since  1.0.0
 * @return void
 */
function seo_wp_output_social_meta() {
    global $post;

    $meta_title       = is_singular() ? get_post_meta( $post->ID, '_seo_wp_seo_title', true ) : null;
    $meta_description = is_singular() ? get_post_meta( $post->ID, '_seo_wp_seo_description', true ) : null;
    $meta_image       = is_singular() ? get_post_meta( $post->ID, '_seo_wp_seo_image', true ) : null;

    if ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( ! is_wp_error( $term ) && $term ) {
            $meta_title       = get_term_meta( $term->term_id, 'meta_title', true );
            $meta_description = get_term_meta( $term->term_id, 'meta_description', true );
        }
    }

    // Fallback to global settings.
    $settings          = get_option( 'seo_wp_settings', [] );
    $meta_title        = $meta_title ?: ( $settings['social_title'] ?? get_bloginfo( 'name' ) );
    $meta_description  = $meta_description ?: ( $settings['social_description'] ?? get_bloginfo( 'description' ) );
    $meta_image        = $meta_image ?: ( $settings['social_image'] ?? '' );

    // Output Open Graph Tags.
    echo '<!-- Open Graph Meta Tags -->' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $meta_title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $meta_description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '">' . "\n";
    if ( $meta_image ) {
        echo '<meta property="og:image" content="' . esc_url( $meta_image ) . '">' . "\n";
    }
    echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . '">' . "\n";

    // Output Twitter Card Tags.
    echo '<!-- Twitter Card Meta Tags -->' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $meta_title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $meta_description ) . '">' . "\n";
    if ( $meta_image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $meta_image ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'seo_wp_output_social_meta', 1 );

/**
 * Modify document title parts.
 *
 * @since  1.0.0
 * @param  array $title_parts Title parts array.
 * @return array Modified title parts.
 */
function seo_wp_replace_title( $title_parts ) {
    if ( is_singular() ) {
        global $post;

        // Retrieve custom SEO title.
        $seo_title = get_post_meta( $post->ID, '_seo_wp_seo_title', true );

        // Retrieve fallback default title from settings.
        $settings        = get_option( 'seo_wp_settings', [] );
        $default_title   = $settings['website_name'] ?? '';
        $default_tagline = $settings['website_tagline'] ?? '';
        $title_separator = $settings['title_separator'] ?? '-';

        // Build the title.
        if ( $seo_title ) {
            $title_parts['title'] = $seo_title;
        } elseif ( $default_title ) {
            $title_parts['title'] = $default_title;
            if ( $default_tagline ) {
                $title_parts['tagline'] = "$title_separator $default_tagline";
            }
        }
    }

    if ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( ! is_wp_error( $term ) && $term ) {
            $meta_title = get_term_meta( $term->term_id, 'meta_title', true );
            if ( $meta_title ) {
                $title_parts['title'] = esc_html( $meta_title );
            }
        }
    }

    return $title_parts;
}
add_filter( 'document_title_parts', 'seo_wp_replace_title', 1 );

/**
 * Output robots meta tag.
 *
 * @since  1.0.0
 * @return void
 */
function seo_wp_output_robots_meta() {
    $noindex  = false;
    $nofollow = false;

    if ( is_singular() ) {
        global $post;
        $noindex  = get_post_meta( $post->ID, '_seo_wp_noindex', true );
        $nofollow = get_post_meta( $post->ID, '_seo_wp_nofollow', true );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( ! is_wp_error( $term ) && $term ) {
            $noindex  = get_term_meta( $term->term_id, 'noindex', true );
            $nofollow = get_term_meta( $term->term_id, 'nofollow', true );
        }
    }

    $robots = [];
    if ( $noindex ) {
        $robots[] = 'noindex';
    }
    if ( $nofollow ) {
        $robots[] = 'nofollow';
    }

    if ( ! empty( $robots ) ) {
        echo '<meta name="robots" content="' . esc_attr( implode( ',', $robots ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'seo_wp_output_robots_meta', 1 );

/**
 * Output canonical tag.
 *
 * @since  1.0.0
 * @return void
 */
function seo_wp_output_canonical_tag() {
    $canonical_url = '';

    if ( is_singular() ) {
        global $post;
        $canonical_url = get_post_meta( $post->ID, '_seo_wp_canonical_url', true );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( ! is_wp_error( $term ) && $term ) {
            $canonical_url = get_term_meta( $term->term_id, 'canonical_url', true );
        }
    }

    // Fallback to default canonical URL.
    if ( ! $canonical_url ) {
        $canonical_url = home_url( add_query_arg( null, null ) );
    }

    echo '<link rel="canonical" href="' . esc_url( $canonical_url ) . '">' . "\n";
}
add_action( 'wp_head', 'seo_wp_output_canonical_tag', 1 );

/**
 * Output pagination meta tags.
 *
 * @since  1.0.0
 * @return void
 */
function seo_wp_output_pagination_meta() {
    if ( ! is_singular() && ! is_archive() && ! is_paged() ) {
        return;
    }

    global $paged, $wp_query;

    if ( ! $paged ) {
        $paged = 1;
    }

    $prev_page = $paged > 1 ? $paged - 1 : null;
    $next_page = $paged < $wp_query->max_num_pages ? $paged + 1 : null;

    if ( $prev_page ) {
        echo '<link rel="prev" href="' . esc_url( get_pagenum_link( $prev_page ) ) . '">' . "\n";
    }

    if ( $next_page ) {
        echo '<link rel="next" href="' . esc_url( get_pagenum_link( $next_page ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'seo_wp_output_pagination_meta', 1 );
