# SEO for WordPress®

SEO for WordPress® is a comprehensive plugin designed to optimize your WordPress® site for search engines. 

It provides tools to manage meta tags, breadcrumbs, RSS customization, robots.txt rules, and sitemaps.

## Features

### General Settings
- **Website Basics**: Set website name, tagline, and title separators.
- **Social Media Appearance**: Customize Open Graph tags with titles, descriptions, and images.

### Advanced Settings
- **Metadata Removal**: Remove unnecessary metadata from the `<head>` section.
- **Breadcrumbs**: Configure breadcrumbs with separators, prefixes, and styling options.
- **RSS Customization**: Add custom content and featured images to RSS feeds.
- **Sitemaps**: Automatically generate XML sitemaps for all public post types.
- **Custom Robots.txt Rules**: Add tailored directives for search engine bots.

### SEO Metabox
- Manage SEO metadata for individual posts, pages, and custom post types, including:
  - SEO Title
  - Meta Description
  - Keywords
  - Canonical URL
  - Noindex/Nofollow settings

### Archive Meta Fields
- Add meta titles, descriptions, and images for taxonomy archives.
- Control indexing and linking behavior for archives.

### Breadcrumbs Generator
- Automatically generate breadcrumbs for all pages and integrate them into your theme using a shortcode or hook.

## Installation

1. **Upload the Plugin**:
   - Download the plugin ZIP file.
   - Upload it via the WordPress Admin: `Plugins > Add New > Upload Plugin`.

2. **Activate the Plugin**:
   - Go to `Plugins` in the WordPress admin dashboard and activate "SEO for WordPress."

3. **Configure Settings**:
   - Navigate to `Settings > SEO Settings` and customize according to your preferences.

## Shortcodes and Hooks

### Breadcrumbs
- **Shortcode**: `[seo_breadcrumbs]`
- **Hook**: `do_action( 'seo_wp_breadcrumbs_hook' );`

## Usage

1. **General SEO Settings**:
   - Configure basic SEO elements under `Settings > SEO Settings > General`.

2. **Advanced Features**:
   - Enable advanced SEO settings like custom robots.txt rules and metadata removal.

3. **SEO Metabox**:
   - Edit SEO settings for posts and pages in the "SEO Settings" metabox while editing content.

4. **Archive Settings**:
   - Set SEO metadata for categories, tags, and custom taxonomies under their respective editing screens.

## Development Notes

### Actions and Filters
- **`robots_txt`**: Customizes the robots.txt output.
- **`template_redirect`**: Renders the dynamic sitemap.
- **`save_post`**: Saves SEO metadata for individual posts.

### File Organization
- `/includes/`:
  - Core functionality such as settings, metaboxes, and sitemap generation.
- `/assets/`:
  - Scripts and styles for the admin interface.

### Security
- Ensures all inputs are sanitized and validated.
- Implements nonces for form submissions.

## Contribution

### Bug Reports and Feature Requests
Submit issues or feature requests via [GitHub Issues](https://github.com/robertdevore/seo-for-wordpress/issues).

## License

This project is licensed under the GPLv2 License.