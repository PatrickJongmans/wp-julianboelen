<?php
/**
 * Block Loader: section-text-two-cta
 * 
 * This file registers the section-text-two-cta block with WordPress.
 * It is automatically loaded by the main blocks.php file.
 * 
 * @package Julianboelen_Theme
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the section-text-two-cta block
 * 
 * This function registers the block type and handles all necessary
 * dependencies including scripts, styles, and server-side rendering.
 * 
 * @since 1.0.0
 * @return void
 */
function tf_register_section_text_two_cta_block() {
    // Check if block registration function exists
    if (!function_exists('register_block_type')) {
        return;
    }

    // Avoid duplicate registration
    if (class_exists('WP_Block_Type_Registry')) {
        $registry = WP_Block_Type_Registry::get_instance();
        if (method_exists($registry, 'is_registered') && $registry->is_registered('julianboelen/section-text-two-cta')) {
            return;
        }
    }

    // Register editor script with explicit dependencies
    wp_register_script(
        'julianboelen-section-text-two-cta-editor',
        get_template_directory_uri() . '/inc/blocks/section-text-two-cta/editor.js',
        array(
            'wp-blocks',
            'wp-element',
            'wp-i18n',
            'wp-components',
            'wp-block-editor',
            'wp-data',
            'wp-dom-ready'
        ),
        filemtime(get_template_directory() . '/inc/blocks/section-text-two-cta/editor.js'),
        true
    );

    // Register the block type
    register_block_type(__DIR__, [
        'render_callback' => 'tf_render_section_text_two_cta_block',
        'editor_script'   => 'julianboelen-section-text-two-cta-editor',
    ]);
}

/**
 * Server-side render callback for the section-text-two-cta block
 * 
 * @since 1.0.0
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block object
 * @return string Rendered block HTML
 */
function tf_render_section_text_two_cta_block($attributes, $content, $block) {
    // Start output buffering
    ob_start();
    
    // Include the render template
    include __DIR__ . '/render.php';
    
    // Return the buffered content
    return ob_get_clean();
}

/**
 * Enqueue additional assets for the section-text-two-cta block
 * 
 * @since 1.0.0
 * @return void
 */
function tf_section_text_two_cta_block_assets() {
    // Enqueue frontend-specific styles if needed
    if (!is_admin()) {
        wp_enqueue_style(
            'section-text-two-cta-frontend',
            get_template_directory_uri() . '/inc/blocks/section-text-two-cta/style.css',
            [],
            filemtime(get_template_directory() . '/inc/blocks/section-text-two-cta/style.css')
        );
    }
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'tf_section_text_two_cta_block_assets');
add_action('enqueue_block_editor_assets', 'tf_section_text_two_cta_block_assets');

/**
 * Add block category if it doesn't exist
 * 
 * @since 1.0.0
 * @param array $categories Existing block categories
 * @return array Modified block categories
 */
function tf_add_section_text_two_cta_block_category($categories) {
    // Check if our custom category already exists
    $category_exists = false;
    foreach ($categories as $category) {
        if ($category['slug'] === 'julianboelen-blocks') {
            $category_exists = true;
            break;
        }
    }
    
    // Add our category if it doesn't exist
    if (!$category_exists) {
        $categories = array_merge(
            [
                [
                    'slug'  => 'julianboelen-blocks',
                    'title' => __('Julianboelen Theme Blocks', 'julianboelen'),
                    'icon'  => 'block-default'
                ]
            ],
            $categories
        );
    }
    
    return $categories;
}

// Enable category registration (uncomment if needed)
// add_filter('block_categories_all', 'tf_add_section_text_two_cta_block_category', 10, 1);

/**
 * Filter block attributes for security and validation
 * 
 * @since 1.0.0
 * @param array $attributes Block attributes
 * @return array Filtered attributes
 */
function tf_filter_section_text_two_cta_block_attributes($attributes) {
    // Sanitize common text fields
    foreach (['welcomeText', 'mainHeading', 'description', 'buttonText'] as $field) {
        if (isset($attributes[$field])) {
            $attributes[$field] = wp_kses_post($attributes[$field]);
        }
    }
    
    // Sanitize URL fields
    foreach (['buttonUrl', 'imageUrl', 'linkUrl'] as $field) {
        if (isset($attributes[$field])) {
            $attributes[$field] = esc_url_raw($attributes[$field]);
        }
    }
    
    // Validate color values
    foreach (['customButtonColor', 'backgroundColor', 'textColor', 'borderColor'] as $field) {
        if (isset($attributes[$field])) {
            $attributes[$field] = sanitize_hex_color($attributes[$field]);
        }
    }
    
    return $attributes;
}
