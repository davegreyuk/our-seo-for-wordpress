<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the SEO Settings page in the WordPress admin.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_register_settings_page() {
    add_options_page(
        __( 'SEO Settings', 'seo-for-wordpress' ),
        __( 'SEO Settings', 'seo-for-wordpress' ),
        'manage_options',
        'seo-for-wordpress',
        'seo_wp_render_settings_page'
    );
}
add_action( 'admin_menu', 'seo_wp_register_settings_page' );

/**
 * Render the SEO Settings page in the WordPress admin.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_settings_page() {
    // Determine the active tab.
    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
    ?>
    <div class="wrap seo-for-wordpress-settings">
        <h1>
            <?php esc_html_e( 'SEO Settings', 'seo-for-wordpress' ); ?>
            <a id="seowp-support-btn" href="https://robertdevore.com/contact/" target="_blank" class="button button-alt" style="margin-left: 10px;">
                <span class="dashicons dashicons-format-chat" style="vertical-align: middle;"></span> <?php esc_html_e( 'Support', 'seo-for-wordpress' ); ?>
            </a>
            <a id="seowp-docs-btn" href="https://robertdevore.com/articles/seo-for-wordpress/" target="_blank" class="button button-alt" style="margin-left: 5px;">
                <span class="dashicons dashicons-media-document" style="vertical-align: middle;"></span> <?php esc_html_e( 'Documentation', 'seo-for-wordpress' ); ?>
            </a>
        </h1>
        <hr />
        <h2 class="nav-tab-wrapper">
            <a href="?page=seo-for-wordpress&amp;tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e( 'General', 'seo-for-wordpress' ); ?>
            </a>
            <a href="?page=seo-for-wordpress&amp;tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e( 'Advanced', 'seo-for-wordpress' ); ?>
            </a>
        </h2>
        <hr style="margin-top: 18px;" />
        <form method="post" action="options.php">
            <?php
            // Output the appropriate settings fields and sections based on the active tab.
            if ( $active_tab === 'general' ) {
                settings_fields( 'seo_wp_general_settings_group' );
                do_settings_sections( 'seo_wp_general' );
            } elseif ( $active_tab === 'advanced' ) {
                settings_fields( 'seo_wp_advanced_settings_group' );
                do_settings_sections( 'seo_wp_advanced' );
            }
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register SEO settings for the plugin.
 *
 * This function registers settings, sections, and fields for the "General" and "Advanced" tabs
 * in the SEO plugin settings page. It includes fields for website basics, social media appearance,
 * metadata removal, sitemaps, breadcrumbs, RSS customization, and robots.txt customization.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_register_settings() {
    // General Settings
    register_setting( 'seo_wp_general_settings_group', 'seo_wp_general_settings', 'seo_wp_sanitize_general_settings' );
    
    add_settings_section( 'seo_wp_general_section', __( 'Website Basics', 'seo-for-wordpress' ), null, 'seo_wp_general' );

    add_settings_field(
        'website_name',
        __( 'Website Name', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_general',
        'seo_wp_general_section',
        [ 'label_for' => 'website_name', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'website_name' ]
    );

    add_settings_field(
        'website_tagline',
        __( 'Website Tagline', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_general',
        'seo_wp_general_section',
        [ 'label_for' => 'website_tagline', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'website_tagline' ]
    );

    add_settings_field(
        'title_separator',
        __( 'Title Separator', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_general',
        'seo_wp_general_section',
        [ 'label_for' => 'title_separator', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'title_separator' ]
    );

    add_settings_field(
        'website_image',
        __( 'Website Image', 'seo-for-wordpress' ),
        'seo_wp_render_upload_field',
        'seo_wp_general',
        'seo_wp_general_section',
        [ 'label_for' => 'website_image', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'website_image' ]
    );

    add_settings_section(
        'seo_wp_social_section',
        __( 'Social Media Appearance', 'seo-for-wordpress' ),
        function () {
            echo '<p>' . esc_html__( 'Configure how your content appears when shared on social media using Open Graph meta tags.', 'seo-for-wordpress' ) . '</p>';
        },
        'seo_wp_general'
    );

    add_settings_field(
        'social_title',
        __( 'Social Media Title', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_general',
        'seo_wp_social_section',
        [ 'label_for' => 'social_title', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'social_title' ]
    );

    add_settings_field(
        'social_description',
        __( 'Social Media Description', 'seo-for-wordpress' ),
        'seo_wp_render_textarea_field',
        'seo_wp_general',
        'seo_wp_social_section',
        [ 'label_for' => 'social_description', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'social_description' ]
    );

    add_settings_field(
        'social_image',
        __( 'Social Media Image', 'seo-for-wordpress' ),
        'seo_wp_render_upload_field',
        'seo_wp_general',
        'seo_wp_social_section',
        [ 'label_for' => 'social_image', 'option_name' => 'seo_wp_general_settings', 'field_name' => 'social_image' ]
    );

    // Advanced Settings.
    register_setting( 'seo_wp_advanced_settings_group', 'seo_wp_advanced_settings', 'seo_wp_sanitize_advanced_settings' );

    add_settings_section( 'seo_wp_metadata_section', __( 'Remove Unwanted Metadata', 'seo-for-wordpress' ), null, 'seo_wp_advanced' );

    add_settings_field(
        'remove_shortlinks',
        __( 'Remove Shortlinks', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_metadata_section',
        [ 'label_for' => 'remove_shortlinks', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'remove_shortlinks' ]
    );

    add_settings_field(
        'remove_rest_links',
        __( 'Remove REST API Links', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_metadata_section',
        [ 'label_for' => 'remove_rest_links', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'remove_rest_links' ]
    );

    add_settings_field(
        'remove_rsd_links',
        __( 'Remove RSD/WLW Links', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_metadata_section',
        [ 'label_for' => 'remove_rsd_links', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'remove_rsd_links' ]
    );

    add_settings_field(
        'remove_oembed_links',
        __( 'Remove oEmbed Links', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_metadata_section',
        [ 'label_for' => 'remove_oembed_links', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'remove_oembed_links' ]
    );

    add_settings_section( 'seo_wp_sitemaps_section', __( 'Sitemaps', 'seo-for-wordpress' ), null, 'seo_wp_advanced' );

    add_settings_field(
        'enable_sitemaps',
        __( 'Enable Sitemaps', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_sitemaps_section',
        [ 'label_for' => 'enable_sitemaps', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'enable_sitemaps' ]
    );

    add_settings_section( 'seo_wp_breadcrumbs_section', __( 'Breadcrumbs', 'seo-for-wordpress' ), null, 'seo_wp_advanced' );

    add_settings_field(
        'breadcrumbs_separator',
        __( 'Separator Between Breadcrumbs', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_advanced',
        'seo_wp_breadcrumbs_section',
        [ 'label_for' => 'breadcrumbs_separator', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'breadcrumbs_separator' ]
    );

    add_settings_field(
        'breadcrumbs_home',
        __( 'Homepage Anchor Text', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_advanced',
        'seo_wp_breadcrumbs_section',
        [ 'label_for' => 'breadcrumbs_home', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'breadcrumbs_home' ]
    );

    add_settings_field(
        'breadcrumbs_prefix',
        __( 'Breadcrumbs Prefix', 'seo-for-wordpress' ),
        'seo_wp_render_text_field',
        'seo_wp_advanced',
        'seo_wp_breadcrumbs_section',
        [ 'label_for' => 'breadcrumbs_prefix', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'breadcrumbs_prefix' ]
    );

    add_settings_field(
        'breadcrumbs_bold',
        __( 'Bold Current Page', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_breadcrumbs_section',
        [ 'label_for' => 'breadcrumbs_bold', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'breadcrumbs_bold' ]
    );

    add_settings_section( 'seo_wp_rss_section', __( 'RSS', 'seo-for-wordpress' ), null, 'seo_wp_advanced' );

    add_settings_field(
        'rss_before',
        __( 'Content Before Posts in Feed', 'seo-for-wordpress' ),
        'seo_wp_render_textarea_field',
        'seo_wp_advanced',
        'seo_wp_rss_section',
        [ 'label_for' => 'rss_before', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'rss_before' ]
    );

    add_settings_field(
        'rss_after',
        __( 'Content After Posts in Feed', 'seo-for-wordpress' ),
        'seo_wp_render_textarea_field',
        'seo_wp_advanced',
        'seo_wp_rss_section',
        [ 'label_for' => 'rss_after', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'rss_after' ]
    );

    add_settings_field(
        'rss_featured_image',
        __( 'Add Featured Images to Feed', 'seo-for-wordpress' ),
        'seo_wp_render_checkbox_field',
        'seo_wp_advanced',
        'seo_wp_rss_section',
        [ 'label_for' => 'rss_featured_image', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'rss_featured_image' ]
    );
    
    add_settings_section( 'seo_wp_robots_section', __( 'Robots', 'seo-for-wordpress' ), null, 'seo_wp_advanced' );

    add_settings_field(
        'custom_robots_txt',
        __( 'Custom Robots.txt Rules', 'seo-for-wordpress' ),
        'seo_wp_render_textarea_field',
        'seo_wp_advanced',
        'seo_wp_robots_section',
        [ 'label_for' => 'custom_robots_txt', 'option_name' => 'seo_wp_advanced_settings', 'field_name' => 'custom_robots_txt' ]
    );

}
add_action( 'admin_init', 'seo_wp_register_settings' );

/**
 * Sanitize input for General SEO settings.
 *
 * This function sanitizes the input values for the general settings, ensuring all fields
 * are properly sanitized based on their expected content type.
 *
 * @since 1.0.0
 *
 * @param array $input The unsanitized input from the settings form.
 * @return array The sanitized input values.
 */
function seo_wp_sanitize_general_settings( $input ) {
    $sanitized = [];

    // Basic website settings.
    $sanitized['website_name']     = sanitize_text_field( $input['website_name'] ?? '' );
    $sanitized['website_tagline']  = sanitize_text_field( $input['website_tagline'] ?? '' );
    $sanitized['title_separator']  = sanitize_text_field( $input['title_separator'] ?? '' );

    // Social Media settings.
    $sanitized['social_title']       = sanitize_text_field( $input['social_title'] ?? '' );
    $sanitized['social_description'] = sanitize_textarea_field( $input['social_description'] ?? '' );
    $sanitized['website_image']      = esc_url_raw( $input['website_image'] ?? '' );
    $sanitized['social_image']       = esc_url_raw( $input['social_image'] ?? '' );

    return $sanitized;
}

/**
 * Sanitize input for Advanced SEO settings.
 *
 * This function sanitizes the input values for the advanced settings, including metadata removal,
 * breadcrumbs, RSS feed settings, sitemaps, and custom robots.txt rules.
 *
 * @param array $input The unsanitized input from the settings form.
 * 
 * @since  1.0.0
 * @return array The sanitized input values.
 */
function seo_wp_sanitize_advanced_settings( $input ) {
    $sanitized = [];

    // Metadata removal settings.
    $sanitized['remove_shortlinks']   = ! empty( $input['remove_shortlinks'] ) ? 1 : 0;
    $sanitized['remove_rest_links']   = ! empty( $input['remove_rest_links'] ) ? 1 : 0;
    $sanitized['remove_rsd_links']    = ! empty( $input['remove_rsd_links'] ) ? 1 : 0;
    $sanitized['remove_oembed_links'] = ! empty( $input['remove_oembed_links'] ) ? 1 : 0;

    // Breadcrumbs settings.
    $sanitized['breadcrumbs_separator'] = sanitize_text_field( $input['breadcrumbs_separator'] ?? '' );
    $sanitized['breadcrumbs_home']      = sanitize_text_field( $input['breadcrumbs_home'] ?? '' );
    $sanitized['breadcrumbs_prefix']    = sanitize_text_field( $input['breadcrumbs_prefix'] ?? '' );
    $sanitized['breadcrumbs_bold']      = ! empty( $input['breadcrumbs_bold'] ) ? 1 : 0;

    // RSS feed settings.
    $sanitized['rss_before']         = sanitize_textarea_field( $input['rss_before'] ?? '' );
    $sanitized['rss_after']          = sanitize_textarea_field( $input['rss_after'] ?? '' );
    $sanitized['rss_featured_image'] = ! empty( $input['rss_featured_image'] ) ? 1 : 0;

    // Sitemap settings.
    $sanitized['enable_sitemaps'] = ! empty( $input['enable_sitemaps'] ) ? 1 : 0;

    // Robots settings.
    $sanitized['custom_robots_txt'] = sanitize_textarea_field( $input['custom_robots_txt'] ?? '' );

    return $sanitized;
}

/**
 * Render a text input field.
 *
 * @param array $args Field arguments including 'label_for', 'option_name', and 'field_name'.
 * 
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_text_field( $args ) {
    $option = get_option( $args['option_name'], [] );
    $value  = $option[ $args['field_name'] ] ?? '';
    printf(
        '<input type="text" id="%1$s" name="%2$s[%3$s]" value="%4$s" class="regular-text">',
        esc_attr( $args['label_for'] ),
        esc_attr( $args['option_name'] ),
        esc_attr( $args['field_name'] ),
        esc_attr( $value )
    );
}

/**
 * Render a textarea field.
 *
 * @param array $args Field arguments including 'label_for', 'option_name', and 'field_name'.
 * 
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_textarea_field( $args ) {
    $option = get_option( $args['option_name'], [] );
    $value  = $option[ $args['field_name'] ] ?? '';
    printf(
        '<textarea id="%1$s" name="%2$s[%3$s]" class="large-text">%4$s</textarea>',
        esc_attr( $args['label_for'] ),
        esc_attr( $args['option_name'] ),
        esc_attr( $args['field_name'] ),
        esc_textarea( $value )
    );
}

/**
 * Render a checkbox field.
 *
 * @param array $args Field arguments including 'label_for', 'option_name', and 'field_name'.
 * 
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_checkbox_field( $args ) {
    $option  = get_option( $args['option_name'], [] );
    $checked = ! empty( $option[ $args['field_name'] ] ) ? 'checked' : '';
    printf(
        '<label class="toggle-switch">
            <input type="checkbox" id="%1$s" name="%2$s[%3$s]" value="1" %4$s>
            <span></span>
        </label>',
        esc_attr( $args['label_for'] ),
        esc_attr( $args['option_name'] ),
        esc_attr( $args['field_name'] ),
        esc_attr( $checked )
    );
}

/**
 * Render an upload field with a button for media selection.
 *
 * @param array $args Field arguments including 'label_for', 'option_name', and 'field_name'.
 * 
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_upload_field( $args ) {
    $option = get_option( $args['option_name'], [] );
    $value  = $option[ $args['field_name'] ] ?? '';
    printf(
        '<input type="text" id="%1$s" name="%2$s[%3$s]" value="%4$s" class="regular-text upload-url">
        <button type="button" class="button upload-button" data-target="#%1$s">%5$s</button>',
        esc_attr( $args['label_for'] ),
        esc_attr( $args['option_name'] ),
        esc_attr( $args['field_name'] ),
        esc_url( $value ),
        esc_html__( 'Add Image', 'seo-for-wordpress' )
    );
}

/**
 * Add custom robots.txt rules to the output.
 *
 * This function adds custom robots.txt rules defined in the advanced settings
 * to the robots.txt file output.
 *
 * @since  1.0.0
 * @return void
 */
function seo_wp_add_custom_robots_rules() {
    $advanced_settings = get_option( 'seo_wp_advanced_settings', [] );

    if ( ! empty( $advanced_settings['custom_robots_txt'] ) ) {
        echo "\n# Custom robots.txt rules added via SEO Settings\n";
        echo esc_textarea( $advanced_settings['custom_robots_txt'] ) . "\n";
    }
}
add_action( 'do_robots', 'seo_wp_add_custom_robots_rules' );

if ( is_multisite() && SEO_WP_NETWORK_SETTINGS ) {
    add_action( 'network_admin_menu', 'seo_wp_register_network_settings_page' );
}

/**
 * Register the SEO Network Settings page in the WordPress admin.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_register_network_settings_page() {
    add_menu_page(
        __( 'SEO Network Settings', 'seo-for-wordpress' ),
        __( 'SEO Network Settings', 'seo-for-wordpress' ),
        'manage_network_options',
        'seo-for-wordpress-network',
        'seo_wp_render_network_settings_page'
    );
}
add_action( 'network_admin_menu', 'seo_wp_register_network_settings_page' );

/**
 * Render the SEO Network Settings page.
 *
 * This function displays the network settings form and handles saving data securely.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_render_network_settings_page() {
    if ( ! current_user_can( 'manage_network_options' ) ) {
        return;
    }

    // Handle form submission.
    if ( isset( $_POST['seo_wp_network_settings_nonce'] ) &&
         wp_verify_nonce( $_POST['seo_wp_network_settings_nonce'], 'seo_wp_network_settings_save' )
    ) {
        $settings = array_map( 'sanitize_text_field', $_POST['seo_wp_settings'] ?? [] );
        seo_wp_update_settings( $settings );
        echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'seo-for-wordpress' ) . '</p></div>';
    }

    $settings = seo_wp_get_settings();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'SEO Network Settings', 'seo-for-wordpress' ); ?></h1>
        <form method="post">
            <?php wp_nonce_field( 'seo_wp_network_settings_save', 'seo_wp_network_settings_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="website_name"><?php esc_html_e( 'Website Name', 'seo-for-wordpress' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="seo_wp_settings[website_name]" id="website_name"
                               value="<?php echo esc_attr( $settings['website_name'] ?? '' ); ?>" class="regular-text">
                    </td>
                </tr>
                <!-- Add other fields as needed -->
            </table>
            <?php submit_button( __( 'Save Changes', 'seo-for-wordpress' ) ); ?>
        </form>
    </div>
    <?php
}

/**
 * Flush rewrite rules when the sitemap toggle changes.
 *
 * This function flushes rewrite rules only when the sitemap toggle changes its state.
 *
 * @since 1.0.0
 *
 * @param array $old_value The old settings value.
 * @param array $value     The new settings value.
 * @return void
 */
function seo_wp_flush_rewrite_rules_on_sitemap_toggle( $old_value, $value ) {
    if ( isset( $old_value['enable_sitemaps'] ) !== isset( $value['enable_sitemaps'] ) ) {
        flush_rewrite_rules(); // Flush only when the sitemap toggle changes.
    }
}
add_action( 'update_option_seo_wp_settings', 'seo_wp_flush_rewrite_rules_on_sitemap_toggle', 10, 2 );

/**
 * Remove metadata links from wp_head based on settings.
 *
 * This function removes various metadata links (e.g., shortlinks, REST API links, etc.)
 * from the HTML head based on the plugin's advanced settings.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_remove_metadata_links() {
    $settings = get_option( 'seo_wp_advanced_settings', [] );

    // Remove shortlinks.
    if ( ! empty( $settings['remove_shortlinks'] ) ) {
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
    }

    // Remove REST API links.
    if ( ! empty( $settings['remove_rest_links'] ) ) {
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
    }

    // Remove RSD/WLW links.
    if ( ! empty( $settings['remove_rsd_links'] ) ) {
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
    }

    // Remove oEmbed links.
    if ( ! empty( $settings['remove_oembed_links'] ) ) {
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    }
}
add_action( 'init', 'seo_wp_remove_metadata_links' );

/**
 * Add custom content before and after posts in the RSS feed.
 *
 * This function appends and prepends content defined in the advanced settings
 * to the content of posts in the RSS2 feed.
 *
 * @param string $content The original post content.
 * @param string $type    The type of feed (e.g., 'rss2').
 * 
 * @since  1.0.0
 * @return string Modified post content with custom content added.
 */
function seo_wp_add_rss_custom_content( $content, $type ) {
    $settings = get_option( 'seo_wp_advanced_settings', [] );

    $before_content = ! empty( $settings['rss_before'] ) ? wp_kses_post( $settings['rss_before'] ) : '';
    $after_content  = ! empty( $settings['rss_after'] ) ? wp_kses_post( $settings['rss_after'] ) : '';

    // Append and prepend content only for RSS2 feed type.
    if ( $type === 'rss2' ) {
        $content = $before_content . $content . $after_content;
    }

    return $content;
}
add_filter( 'the_content_feed', 'seo_wp_add_rss_custom_content', 10, 2 );

/**
 * Add the featured image to the RSS feed.
 *
 * This function adds the featured image of a post as a `<media:content>` element
 * in the RSS2 feed, provided the setting is enabled and the post has a featured image.
 *
 * @since  1.0.0
 * @return void
 */
function seo_wp_add_featured_image_to_feed() {
    $settings = get_option( 'seo_wp_advanced_settings', [] );

    if ( ! empty( $settings['rss_featured_image'] ) && has_post_thumbnail() ) {
        $featured_image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        if ( $featured_image_url ) {
            echo '<media:content url="' . esc_url( $featured_image_url ) . '" type="image/jpeg" />' . "\n";
        }
    }
}
add_action( 'rss2_item', 'seo_wp_add_featured_image_to_feed' );
