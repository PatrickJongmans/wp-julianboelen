<?php
/**
 * Section Text Image Block Loader
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
 * Register the Section Text Image block
 * 
 * @return void
 */
function julianboelen_register_section_text_image_block() {
    // Check if function exists (WordPress 5.8+)
    if (!function_exists('register_block_type')) {
        return;
    }

    // Get the directory path
    $block_dir = dirname(__FILE__);

    // Register the block editor script
    $editor_script_asset_path = $block_dir . '/build/index.asset.php';
    $editor_script_asset = file_exists($editor_script_asset_path)
        ? require $editor_script_asset_path
        : ['dependencies' => ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'], 'version' => filemtime($block_dir . '/editor.js')];

    wp_register_script(
        'julianboelen-section-text-image-editor',
        plugins_url('editor.js', __FILE__),
        $editor_script_asset['dependencies'],
        $editor_script_asset['version'],
        true
    );

    // Register the block style
    wp_register_style(
        'julianboelen-section-text-image-style',
        plugins_url('style.css', __FILE__),
        [],
        filemtime($block_dir . '/style.css')
    );

    // Register the block type
    register_block_type($block_dir, [
        'editor_script' => 'julianboelen-section-text-image-editor',
        'style' => 'julianboelen-section-text-image-style',
        'render_callback' => 'julianboelen_render_section_text_image_block'
    ]);
}
add_action('init', 'julianboelen_register_section_text_image_block');

/**
 * Render callback for the Section Text Image block
 * 
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block object
 * @return string Rendered block HTML
 */
function julianboelen_render_section_text_image_block($attributes, $content, $block) {
    // Start output buffering
    ob_start();
    
    // Include the render template
    include dirname(__FILE__) . '/render.php';
    
    // Return the buffered content
    return ob_get_clean();
}

/**
 * Add block category if it doesn't exist
 * 
 * @param array $categories Existing block categories
 * @param WP_Post $post Post object
 * @return array Modified block categories
 */
function julianboelen_add_section_text_image_block_category($categories, $post) {
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
            [
                [
                    'slug' => 'julianboelen-blocks',
                    'title' => __('Julian Boelen Blocks', 'julianboelen'),
                    'icon' => 'layout'
                ]
            ],
            $categories
        );
    }
    
    return $categories;
}
add_filter('block_categories_all', 'julianboelen_add_section_text_image_block_category', 10, 2);

/**
 * Enqueue block editor assets
 * 
 * @return void
 */
function julianboelen_section_text_image_editor_assets() {
    // Add inline styles for better editor experience
    $editor_styles = '
        .section-text-image-block-preview {
            position: relative;
        }
        .section-text-image-block-preview .components-placeholder {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .section-text-image-block-preview [contenteditable="true"]:focus {
            outline: 2px solid #007cba;
            outline-offset: 2px;
        }
    ';
    
    wp_add_inline_style('wp-edit-blocks', $editor_styles);
}
add_action('enqueue_block_editor_assets', 'julianboelen_section_text_image_editor_assets');

/**
 * Add server-side rendering support
 * 
 * @return void
 */
function julianboelen_section_text_image_block_init() {
    // Ensure Tailwind CSS classes are available if theme doesn't include them
    if (!wp_style_is('tailwindcss', 'enqueued')) {
        // Add fallback styles or enqueue Tailwind if needed
        // This is optional and depends on your theme setup
    }
}
add_action('wp_enqueue_scripts', 'julianboelen_section_text_image_block_init');