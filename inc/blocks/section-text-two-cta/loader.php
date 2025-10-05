<?php
/**
 * Section Text Two CTA Block Loader
 * 
 * Handles block registration and asset enqueuing
 * 
 * @package JulianBoelen
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the Section Text Two CTA block
 * 
 * @return void
 */
function julianboelen_register_section_text_two_cta_block() {
    // Define block directory path
    $block_dir = get_template_directory() . '/inc/blocks/section-text-two-cta';
    
    // Check if block.json exists
    if (!file_exists($block_dir . '/block.json')) {
        return;
    }
    
    // Register the block editor script
    wp_register_script(
        'julianboelen-section-text-two-cta-editor',
        get_template_directory_uri() . '/inc/blocks/section-text-two-cta/editor.js',
        array(
            'wp-blocks',
            'wp-element',
            'wp-i18n',
            'wp-block-editor',
            'wp-components'
        ),
        filemtime($block_dir . '/editor.js'),
        true
    );
    
    // Register the block style
    wp_register_style(
        'julianboelen-section-text-two-cta-style',
        get_template_directory_uri() . '/inc/blocks/section-text-two-cta/style.css',
        array(),
        filemtime($block_dir . '/style.css')
    );
    
    // Register the block type
    register_block_type($block_dir, array(
        'editor_script' => 'julianboelen-section-text-two-cta-editor',
        'style' => 'julianboelen-section-text-two-cta-style',
        'render_callback' => 'julianboelen_render_section_text_two_cta_block'
    ));
}
add_action('init', 'julianboelen_register_section_text_two_cta_block');

/**
 * Render callback for the Section Text Two CTA block
 * 
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block object
 * @return string Rendered block HTML
 */
function julianboelen_render_section_text_two_cta_block($attributes, $content, $block) {
    // Start output buffering
    ob_start();
    
    // Include the render template
    $template_path = get_template_directory() . '/inc/blocks/section-text-two-cta/render.php';
    
    if (file_exists($template_path)) {
        include $template_path;
    }
    
    // Return the buffered content
    return ob_get_clean();
}

/**
 * Add block category if it doesn't exist
 * 
 * @param array $categories Existing block categories
 * @param WP_Post $post Current post object
 * @return array Modified block categories
 */
function julianboelen_add_section_text_two_cta_block_category($categories, $post) {
    // Check if category already exists
    $category_exists = false;
    foreach ($categories as $category) {
        if ($category['slug'] === 'julianboelen-blocks') {
            $category_exists = true;
            break;
        }
    }
    
    // Add category if it doesn't exist
    if (!$category_exists) {
        return array_merge(
            array(
                array(
                    'slug' => 'julianboelen-blocks',
                    'title' => __('Julian Boelen Blocks', 'julianboelen'),
                    'icon' => 'layout'
                )
            ),
            $categories
        );
    }
    
    return $categories;
}
add_filter('block_categories_all', 'julianboelen_add_section_text_two_cta_block_category', 10, 2);

/**
 * Enqueue block assets for both editor and frontend
 * 
 * @return void
 */
function julianboelen_section_text_two_cta_block_assets() {
    // Enqueue frontend styles if not in editor
    if (!is_admin()) {
        wp_enqueue_style('julianboelen-section-text-two-cta-style');
    }
}
add_action('enqueue_block_assets', 'julianboelen_section_text_two_cta_block_assets');

/**
 * Add inline styles for dynamic color support
 * 
 * @return void
 */
function julianboelen_section_text_two_cta_inline_styles() {
    // Only add if block is being used on the page
    if (has_block('julianboelen/section-text-two-cta')) {
        $custom_css = "
            .section-text-two-cta-block .cta-card {
                position: relative;
                isolation: isolate;
            }
        ";
        wp_add_inline_style('julianboelen-section-text-two-cta-style', $custom_css);
    }
}
add_action('wp_enqueue_scripts', 'julianboelen_section_text_two_cta_inline_styles');