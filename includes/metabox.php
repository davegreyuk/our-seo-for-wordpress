<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add meta box for SEO fields
function seo_wp_add_seo_metabox() {
    $post_types = get_post_types( [ 'public' => true ], 'names' );
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'seo_wp_seo_metabox',
            __( 'SEO Settings', 'seo-for-wordpress' ),
            'seo_wp_render_seo_metabox',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'seo_wp_add_seo_metabox' );

// Render the meta box with modernized styles
function seo_wp_render_seo_metabox( $post ) {
    wp_nonce_field( 'seo_wp_save_seo_meta', 'seo_wp_seo_meta_nonce' );

    $seo_title       = get_post_meta( $post->ID, '_seo_wp_seo_title', true );
    $seo_description = get_post_meta( $post->ID, '_seo_wp_seo_description', true );
    $seo_keywords    = get_post_meta( $post->ID, '_seo_wp_seo_keywords', true );
    $seo_image       = get_post_meta( $post->ID, '_seo_wp_seo_image', true );
    $seo_noindex     = get_post_meta( $post->ID, '_seo_wp_noindex', true );
    $seo_nofollow    = get_post_meta( $post->ID, '_seo_wp_nofollow', true );
    $canonical_url   = get_post_meta( $post->ID, '_seo_wp_canonical_url', true );

    ?>
    <div class="seo-metabox">
        <div class="seo-field-group">
            <label for="seo_wp_seo_title" class="seo-label"><?php esc_html_e( 'SEO Title', 'seo-for-wordpress' ); ?></label>
            <input type="text" id="seo_wp_seo_title" name="seo_wp_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" class="seo-input">
        </div>

        <div class="seo-field-group">
            <label for="seo_wp_seo_description" class="seo-label"><?php esc_html_e( 'SEO Description', 'seo-for-wordpress' ); ?></label>
            <textarea id="seo_wp_seo_description" name="seo_wp_seo_description" class="seo-input"><?php echo esc_textarea( $seo_description ); ?></textarea>
        </div>

        <div class="seo-field-group">
            <label for="seo_wp_seo_keywords" class="seo-label"><?php esc_html_e( 'SEO Keywords', 'seo-for-wordpress' ); ?></label>
            <input type="text" id="seo_wp_seo_keywords" name="seo_wp_seo_keywords" value="<?php echo esc_attr( $seo_keywords ); ?>" class="seo-input">
        </div>

        <div class="seo-field-group">
            <label for="seo_wp_seo_image" class="seo-label"><?php esc_html_e( 'SEO Image', 'seo-for-wordpress' ); ?></label>
            <input type="text" id="seo_wp_seo_image" name="seo_wp_seo_image" value="<?php echo esc_url( $seo_image ); ?>" class="seo-input">
            <button type="button" class="button seo-upload-button" data-target="#seo_wp_seo_image"><?php esc_html_e( 'Upload Image', 'seo-for-wordpress' ); ?></button>
        </div>

        <div class="seo-field-group">
            <label class="seo-checkbox-label">
                <input type="checkbox" name="seo_wp_noindex" value="1" <?php checked( $seo_noindex, 1 ); ?>>
                <?php esc_html_e( 'Noindex this page', 'seo-for-wordpress' ); ?>
            </label>
        </div>

        <div class="seo-field-group">
            <label class="seo-checkbox-label">
                <input type="checkbox" name="seo_wp_nofollow" value="1" <?php checked( $seo_nofollow, 1 ); ?>>
                <?php esc_html_e( 'Nofollow links on this page', 'seo-for-wordpress' ); ?>
            </label>
        </div>

        <div class="seo-field-group">
            <label for="seo_wp_canonical_url" class="seo-label"><?php esc_html_e( 'Canonical URL', 'seo-for-wordpress' ); ?></label>
            <input type="text" id="seo_wp_canonical_url" name="seo_wp_canonical_url" value="<?php echo esc_url( $canonical_url ); ?>" class="seo-input">
            <p class="seo-description"><?php esc_html_e( 'Set a canonical URL for this page. Leave blank to use the default.', 'seo-for-wordpress' ); ?></p>
        </div>
    </div>
    <?php
}


// Save meta box data
function seo_wp_save_seo_meta( $post_id ) {
    if ( ! isset( $_POST['seo_wp_seo_meta_nonce'] ) || ! wp_verify_nonce( $_POST['seo_wp_seo_meta_nonce'], 'seo_wp_save_seo_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = [ 'seo_wp_seo_title', 'seo_wp_seo_description', 'seo_wp_seo_keywords', 'seo_wp_seo_image' ];
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, "_$field", sanitize_text_field( $_POST[ $field ] ) );
        } else {
            delete_post_meta( $post_id, "_$field" );
        }
    }

    if ( isset( $_POST['seo_wp_noindex'] ) ) {
        update_post_meta( $post_id, '_seo_wp_noindex', 1 );
    } else {
        delete_post_meta( $post_id, '_seo_wp_noindex' );
    }
    
    if ( isset( $_POST['seo_wp_nofollow'] ) ) {
        update_post_meta( $post_id, '_seo_wp_nofollow', 1 );
    } else {
        delete_post_meta( $post_id, '_seo_wp_nofollow' );
    }

    if ( isset( $_POST['seo_wp_canonical_url'] ) ) {
        update_post_meta( $post_id, '_seo_wp_canonical_url', esc_url_raw( $_POST['seo_wp_canonical_url'] ) );
    } else {
        delete_post_meta( $post_id, '_seo_wp_canonical_url' );
    }

}
add_action( 'save_post', 'seo_wp_save_seo_meta' );

// Add archive meta fields
function seo_wp_add_archive_meta_fields() {
    ?>
    <div class="form-field">
        <label for="meta_title"><?php esc_html_e( 'Meta Title', 'seo-for-wordpress' ); ?></label>
        <input type="text" name="meta_title" id="meta_title" value="" class="widefat">
        <p class="description"><?php esc_html_e( 'Set a meta title for this archive.', 'seo-for-wordpress' ); ?></p>
    </div>
    <div class="form-field">
        <label for="meta_description"><?php esc_html_e( 'Meta Description', 'seo-for-wordpress' ); ?></label>
        <textarea name="meta_description" id="meta_description" rows="4" class="widefat"></textarea>
        <p class="description"><?php esc_html_e( 'Set a meta description for this archive.', 'seo-for-wordpress' ); ?></p>
    </div>
    <?php
}
add_action( 'category_add_form_fields', 'seo_wp_add_archive_meta_fields' );
add_action( 'post_tag_add_form_fields', 'seo_wp_add_archive_meta_fields' );
add_action( 'edit_category_form_fields', 'seo_wp_edit_archive_meta_fields' );
add_action( 'edit_post_tag_form_fields', 'seo_wp_edit_archive_meta_fields' );

function seo_wp_edit_archive_meta_fields( $term ) {
    $meta_title = get_term_meta( $term->term_id, 'meta_title', true );
    $meta_description = get_term_meta( $term->term_id, 'meta_description', true );
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="meta_title"><?php esc_html_e( 'Meta Title', 'seo-for-wordpress' ); ?></label>
        </th>
        <td>
            <input type="text" name="meta_title" id="meta_title" value="<?php echo esc_attr( $meta_title ); ?>" class="widefat">
            <p class="description"><?php esc_html_e( 'Set a meta title for this archive.', 'seo-for-wordpress' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="meta_description"><?php esc_html_e( 'Meta Description', 'seo-for-wordpress' ); ?></label>
        </th>
        <td>
            <textarea name="meta_description" id="meta_description" rows="4" class="widefat"><?php echo esc_textarea( $meta_description ); ?></textarea>
            <p class="description"><?php esc_html_e( 'Set a meta description for this archive.', 'seo-for-wordpress' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="meta_image"><?php esc_html_e( 'Meta Image', 'seo-for-wordpress' ); ?></label>
        </th>
        <td>
            <input type="text" name="meta_image" id="meta_image" value="<?php echo esc_url( get_term_meta( $term->term_id, 'meta_image', true ) ); ?>" class="widefat">
            <button type="button" class="button seo-wp-upload-image"><?php esc_html_e( 'Upload Image', 'seo-for-wordpress' ); ?></button>
            <p class="description"><?php esc_html_e( 'Set an image for this archive (used for social media sharing).', 'seo-for-wordpress' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="noindex"><?php esc_html_e( 'Noindex', 'seo-for-wordpress' ); ?></label>
        </th>
        <td>
            <input type="checkbox" name="noindex" id="noindex" value="1" <?php checked( get_term_meta( $term->term_id, 'noindex', true ), 1 ); ?>>
            <p class="description"><?php esc_html_e( 'Prevent this archive from being indexed by search engines.', 'seo-for-wordpress' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="nofollow"><?php esc_html_e( 'Nofollow', 'seo-for-wordpress' ); ?></label>
        </th>
        <td>
            <input type="checkbox" name="nofollow" id="nofollow" value="1" <?php checked( get_term_meta( $term->term_id, 'nofollow', true ), 1 ); ?>>
            <p class="description"><?php esc_html_e( 'Add nofollow to links in this archive.', 'seo-for-wordpress' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="canonical_url"><?php esc_html_e( 'Canonical URL', 'seo-for-wordpress' ); ?></label>
        </th>
        <td>
            <input type="text" name="canonical_url" id="canonical_url" value="<?php echo esc_url( get_term_meta( $term->term_id, 'canonical_url', true ) ); ?>" class="widefat">
            <p class="description"><?php esc_html_e( 'Set a canonical URL for this archive. Leave blank to use the default.', 'seo-for-wordpress' ); ?></p>
        </td>
    </tr>

    <?php
}

function seo_wp_save_archive_meta_fields( $term_id ) {
    if ( isset( $_POST['meta_title'] ) ) {
        update_term_meta( $term_id, 'meta_title', sanitize_text_field( $_POST['meta_title'] ) );
    }
    if ( isset( $_POST['meta_description'] ) ) {
        update_term_meta( $term_id, 'meta_description', sanitize_textarea_field( $_POST['meta_description'] ) );
    }
    if ( isset( $_POST['meta_image'] ) ) {
        update_term_meta( $term_id, 'meta_image', sanitize_text_field( $_POST['meta_image'] ) );
    }

    if ( isset( $_POST['noindex'] ) ) {
        update_term_meta( $term_id, 'noindex', 1 );
    } else {
        delete_term_meta( $term_id, 'noindex' );
    }
    
    if ( isset( $_POST['nofollow'] ) ) {
        update_term_meta( $term_id, 'nofollow', 1 );
    } else {
        delete_term_meta( $term_id, 'nofollow' );
    }
    
    if ( isset( $_POST['canonical_url'] ) ) {
        update_term_meta( $term_id, 'canonical_url', esc_url_raw( $_POST['canonical_url'] ) );
    } else {
        delete_term_meta( $term_id, 'canonical_url' );
    }

}
add_action( 'edited_category', 'seo_wp_save_archive_meta_fields' );
add_action( 'create_category', 'seo_wp_save_archive_meta_fields' );
add_action( 'edited_post_tag', 'seo_wp_save_archive_meta_fields' );
add_action( 'create_post_tag', 'seo_wp_save_archive_meta_fields' );

function seo_wp_output_archive_meta() {
    if ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();

        $meta_title = get_term_meta( $term->term_id, 'meta_title', true );
        $meta_description = get_term_meta( $term->term_id, 'meta_description', true );

        if ( $meta_title ) {
            add_filter( 'pre_get_document_title', function() use ( $meta_title ) {
                return esc_html( $meta_title );
            });
        }

        if ( $meta_description ) {
            echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";
        }
    }
}
add_action( 'wp_head', 'seo_wp_output_archive_meta', 1 );

/**
 * Add archive meta fields
 * 
 * @since  1.0.0
 * @return void
 */
add_action( 'admin_init', function() {
    $taxonomies = get_taxonomies( [ 'public' => true ], 'names' );

    foreach ( $taxonomies as $taxonomy ) {
        add_action( "{$taxonomy}_add_form_fields", 'seo_wp_add_archive_meta_fields' );
        add_action( "{$taxonomy}_edit_form_fields", 'seo_wp_edit_archive_meta_fields' );
        add_action( "edited_{$taxonomy}", 'seo_wp_save_archive_meta_fields' );
        add_action( "create_{$taxonomy}", 'seo_wp_save_archive_meta_fields' );
    }
} );
