<?php

/**
  * The plugin bootstrap file
  *
  * @link              https://robertdevore.com
  * @since             1.0.0
  * @package           SEO_For_WordPress
  *
  * @wordpress-plugin
  *
  * Plugin Name: SEO for WordPress®
  * Description: A modern SEO plugin for managing meta tags, breadcrumbs, and sitemaps.
  * Plugin URI:  https://github.com/robertdevore/seo-for-wordpress/
  * Version:     1.0.1
  * Author:      Robert DeVore
  * Author URI:  https://robertdevore.com/
  * License:     GPL-2.0+
  * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
  * Text Domain: seo-for-wordpress
  * Domain Path: /languages
  * Update URI:  https://github.com/robertdevore/seo-for-wordpress/
  */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/robertdevore/seo-for-wordpress/',
	__FILE__,
	'seo-for-wordpress'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );

// Check if Composer's autoloader is already registered globally.
if ( ! class_exists( 'RobertDevore\WPComCheck\WPComPluginHandler' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use RobertDevore\WPComCheck\WPComPluginHandler;

new WPComPluginHandler( plugin_basename( __FILE__ ), 'https://robertdevore.com/why-this-plugin-doesnt-support-wordpress-com-hosting/' );

/**
 * Load plugin text domain for translations
 * 
 * @since 1.1.0
 * @return void
 */
function seo_wp_load_textdomain() {
    load_plugin_textdomain( 
        'seo-for-wordpress', 
        false, 
        dirname( plugin_basename( __FILE__ ) ) . '/languages/'
    );
}
add_action( 'plugins_loaded', 'seo_wp_load_textdomain' );

// Define constants.
define( 'SEO_WP_VERSION', '1.0.1' );
define( 'SEO_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEO_WP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'SEO_WP_NETWORK_SETTINGS' ) ) {
    define( 'SEO_WP_NETWORK_SETTINGS', false );
}

// Include required files
require_once SEO_WP_PLUGIN_DIR . 'includes/settings.php';
require_once SEO_WP_PLUGIN_DIR . 'includes/metabox.php';
require_once SEO_WP_PLUGIN_DIR . 'includes/meta-output.php';
require_once SEO_WP_PLUGIN_DIR . 'includes/breadcrumbs.php';
require_once SEO_WP_PLUGIN_DIR . 'includes/sitemap.php';
require_once SEO_WP_PLUGIN_DIR . 'includes/robots.php';

/**
 * Activate the SEO plugin.
 *
 * This function initializes the plugin by creating default settings if none exist,
 * or merging new default settings with existing ones during activation.
 *
 * @since 1.0.0
 * @return void
 */
function seo_wp_activate() {
    // Get the current settings.
    $current_settings = seo_wp_get_settings();

    // Define default settings.
    $default_settings = [
        'website_name'          => '',
        'website_tagline'       => '',
        'title_separator'       => '-',
        'website_image'         => '',
        'social_title'          => '',
        'social_description'    => '',
        'social_image'          => '',
        'remove_shortlinks'     => false,
        'remove_rest_links'     => false,
        'remove_rsd_links'      => false,
        'remove_oembed_links'   => false,
        'breadcrumbs_separator' => '>',
        'breadcrumbs_home'      => 'Home',
        'breadcrumbs_prefix'    => '',
        'breadcrumbs_bold'      => false,
        'rss_before'            => '',
        'rss_after'             => '',
        'rss_featured_image'    => false,
        'enable_sitemaps'       => true,
        'custom_robots_txt'     => '',
    ];

    // If no settings exist, save the defaults.
    if ( empty( $current_settings ) ) {
        seo_wp_update_settings( $default_settings );
    } else {
        // Merge defaults with existing settings (to include new options).
        $merged_settings = array_merge( $default_settings, $current_settings );
        seo_wp_update_settings( $merged_settings );
    }
}
register_activation_hook( __FILE__, 'seo_wp_activate' );

/**
 * Enqueue media uploader scripts and admin styles.
 *
 * This function enqueues the necessary media uploader scripts and custom admin styles
 * on specific admin pages.
 *
 * @param string $hook The current admin page hook suffix.
 * 
 * @since 1.0.0
 * @return void
 */
function seo_wp_enqueue_media_uploader( $hook ) {
    // Load only on specific admin pages.
    if ( in_array( $hook, [ 'settings_page_seo-for-wordpress', 'post.php', 'edit-tags.php' ], true ) ) {
        wp_enqueue_media();
        wp_enqueue_script(
            'seo-wp-media-uploader',
            SEO_WP_PLUGIN_URL . 'js/media-uploader.js',
            [ 'jquery' ],
            SEO_WP_VERSION,
            true
        );

        wp_enqueue_style(
            'seo-wp-admin-styles',
            SEO_WP_PLUGIN_URL . 'css/admin-styles.css',
            [],
            SEO_WP_VERSION
        );
    }
}
add_action( 'admin_enqueue_scripts', 'seo_wp_enqueue_media_uploader' );

/**
 * Retrieve plugin settings.
 *
 * This function fetches the plugin settings, either from the site-wide options
 * (for multisite installations) or regular options.
 *
 * @since  1.0.0
 * @return array The plugin settings.
 */
function seo_wp_get_settings() {
    return SEO_WP_NETWORK_SETTINGS 
        ? get_site_option( 'seo_wp_settings', [] ) 
        : get_option( 'seo_wp_settings', [] );
}

/**
 * Update plugin settings.
 *
 * This function updates the plugin settings, either in the site-wide options
 * (for multisite installations) or regular options.
 *
 * @param array $settings The settings to save.
 * 
 * @since  1.0.0
 * @return void
 */
function seo_wp_update_settings( $settings ) {
    if ( SEO_WP_NETWORK_SETTINGS ) {
        update_site_option( 'seo_wp_settings', $settings );
    } else {
        update_option( 'seo_wp_settings', $settings );
    }
}
