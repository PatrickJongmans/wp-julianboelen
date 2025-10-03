<?php
/**
 * TF Theme Functions
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define theme constants
 */
define('JULIANBOELEN_THEME_VERSION', '1.0.0');
define('JULIANBOELEN_THEME_DIR', get_template_directory());
define('JULIANBOELEN_THEME_URL', get_template_directory_uri());

/**
 * Theme setup
 */
function julianboelen_theme_setup() {
    // Add theme support for various features
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('custom-header');
    add_theme_support('custom-background');
    add_theme_support('post-formats', array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio'
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'julianboelen'),
        'footer'  => __('Footer Menu', 'julianboelen'),
    ));

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for wide and full alignment
    add_theme_support('align-wide');

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'julianboelen_theme_setup');

/**
 * Enqueue styles and scripts
 */
function julianboelen_theme_assets() {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'julianboelen-style',
        JULIANBOELEN_THEME_URL . '/style.css',
        array(),
        JULIANBOELEN_THEME_VERSION
    );

    // Enqueue main JavaScript file
    wp_enqueue_script(
        'julianboelen-main',
        JULIANBOELEN_THEME_URL . '/assets/js/main.js',
        array('jquery'),
        JULIANBOELEN_THEME_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('julianboelen-main', 'julianboelen_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('julianboelen_nonce')
    ));

    // Enqueue comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'julianboelen_theme_assets');

/**
 * Register widget areas
 */
function julianboelen_theme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Primary Sidebar', 'julianboelen'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'julianboelen'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widgets', 'julianboelen'),
        'id'            => 'footer-widgets',
        'description'   => __('Add widgets here to appear in your footer.', 'julianboelen'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'julianboelen_theme_widgets_init');

/**
 * Custom excerpt length
 */
function julianboelen_theme_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'julianboelen_theme_excerpt_length');

/**
 * Custom excerpt more
 */
function julianboelen_theme_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'julianboelen_theme_excerpt_more');

/**
 * Include additional theme files
 */
require_once JULIANBOELEN_THEME_DIR . '/inc/theme-options.php';
require_once JULIANBOELEN_THEME_DIR . '/inc/nav-walkers.php';
require_once JULIANBOELEN_THEME_DIR . '/inc/blocks/blocks.php';
require_once JULIANBOELEN_THEME_DIR . '/inc/enqueue.php';
require_once JULIANBOELEN_THEME_DIR . '/inc/post_types/post_types.php';

/**
 * Initialize theme options
 */
if (class_exists('JulianboelenThemeOptions')) {
    $julianboelen_theme_options = new JulianboelenThemeOptions();
}

/**
 * Add body classes
 */
function julianboelen_theme_body_classes($classes) {
    // Add class if we're viewing the front page
    if (is_front_page()) {
        $classes[] = 'front-page';
    }

    // Add class if we have a custom header
    if (has_custom_header()) {
        $classes[] = 'has-custom-header';
    }

    // Add class for sidebar
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    } else {
        $classes[] = 'no-sidebar';
    }

    return $classes;
}
add_filter('body_class', 'julianboelen_theme_body_classes');

/**
 * Remove WordPress version number
 */
function julianboelen_remove_version() {
    return '';
}
add_filter('the_generator', 'julianboelen_remove_version');

/**
 * Disable file editing in admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}