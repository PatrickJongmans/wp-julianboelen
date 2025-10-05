<?php
/**
 * Section Title Text Horizontal Block Loader
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
 * Register the Section Title Text Horizontal block
 * 
 * @return void
 */
function julianboelen_register_section_title_text_horizontal_block() {
    // Define block directory path
    $block_dir = get_template_directory() . '/inc/blocks/section-title-text-horizontal';
    
    // Check if block.json exists
    if (!file_exists($block_dir . '/block.json')) {
        return;
    }
    
    // Register the block editor script
    $editor_asset_file = $block_dir . '/editor.js';
    if (file_exists($editor_asset_file)) {
        wp_register_script(
            'julianboelen-section-title-text-horizontal-editor',
            get_template_directory_uri() . '/inc/blocks/section-title-text-horizontal/editor.js',
            array(
                'wp-blocks',
                'wp-element',
                'wp-block-editor',
                'wp-components',
                'wp-i18n'
            ),
            filemtime($editor_asset_file),
            true
        );
    }
    
    // Register the block style
    $style_file = $block_dir . '/style.css';
    if (file_exists($style_file)) {
        wp_register_style(
            'julianboelen-section-title-text-horizontal-style',
            get_template_directory_uri() . '/inc/blocks/section-title-text-horizontal/style.css',
            array(),
            filemtime($style_file)
        );
    }
    
    // Register the block type
    register_block_type($block_dir, array(
        'editor_script' => 'julianboelen-section-title-text-horizontal-editor',
        'style' => 'julianboelen-section-title-text-horizontal-style',
        'render_callback' => 'julianboelen_render_section_title_text_horizontal_block'
    ));
}
add_action('init', 'julianboelen_register_section_title_text_horizontal_block');

/**
 * Render callback for the Section Title Text Horizontal block
 * 
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block instance
 * @return string Rendered block HTML
 */
function julianboelen_render_section_title_text_horizontal_block($attributes, $content, $block) {
    // Start output buffering
    ob_start();
    
    // Include the render template
    $template_path = get_template_directory() . '/inc/blocks/section-title-text-horizontal/render.php';
    
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        // Fallback if template doesn't exist
        echo '<!-- Section Title Text Horizontal block template not found -->';
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
function julianboelen_add_section_title_text_horizontal_block_category($categories, $post) {
    // Check if our custom category already exists
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
add_filter('block_categories_all', 'julianboelen_add_section_title_text_horizontal_block_category', 10, 2);

/**
 * Enqueue editor assets for better preview
 * 
 * @return void
 */
function julianboelen_section_title_text_horizontal_editor_assets() {
    // Add Tailwind CSS for editor preview (if not already loaded)
    if (!wp_style_is('tailwindcss', 'enqueued')) {
        wp_enqueue_style(
            'tailwindcss-cdn',
            'https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css',
            array(),
            '3.3.0'
        );
    }
}
add_action('enqueue_block_editor_assets', 'julianboelen_section_title_text_horizontal_editor_assets');

/**
 * Add inline styles for editor preview enhancements
 * 
 * @return void
 */
function julianboelen_section_title_text_horizontal_editor_inline_styles() {
    $custom_css = '
        .section-title-text-horizontal-preview {
            transition: all 0.3s ease;
        }
        .section-title-text-horizontal-preview:hover {
            border-color: #9ca3af;
        }
        .block-editor-rich-text__editable[data-is-placeholder-visible="true"] {
            opacity: 0.6;
        }
    ';
    
    wp_add_inline_style('julianboelen-section-title-text-horizontal-style', $custom_css);
}
add_action('enqueue_block_editor_assets', 'julianboelen_section_title_text_horizontal_editor_inline_styles');

/**
 * Register block pattern for quick insertion
 * 
 * @return void
 */
function julianboelen_register_section_title_text_horizontal_pattern() {
    if (function_exists('register_block_pattern')) {
        register_block_pattern(
            'julianboelen/section-title-text-horizontal-default',
            array(
                'title' => __('Section Title Text Horizontal - Default', 'julianboelen'),
                'description' => __('A two-column layout with heading and body text', 'julianboelen'),
                'categories' => array('julianboelen-blocks'),
                'content' => '<!-- wp:julianboelen/section-title-text-horizontal /-->'
            )
        );
    }
}
add_action('init', 'julianboelen_register_section_title_text_horizontal_pattern');