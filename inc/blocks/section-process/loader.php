<?php
/**
 * Section Process Block Loader
 * 
 * Handles registration and asset enqueuing for the section-process block
 * 
 * @package JulianBoelen
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the section-process block
 * 
 * @return void
 */
function julianboelen_register_section_process_block() {
    // Define block directory path
    $block_dir = get_template_directory() . '/inc/blocks/section-process';
    
    // Check if block.json exists
    if (!file_exists($block_dir . '/block.json')) {
        return;
    }
    
    // Register the block editor script
    wp_register_script(
        'julianboelen-section-process-editor',
        get_template_directory_uri() . '/inc/blocks/section-process/editor.js',
        array(
            'wp-blocks',
            'wp-element',
            'wp-i18n',
            'wp-block-editor',
            'wp-components',
            'wp-data',
            'wp-compose'
        ),
        filemtime($block_dir . '/editor.js'),
        true
    );
    
    // Register the block style
    wp_register_style(
        'julianboelen-section-process-style',
        get_template_directory_uri() . '/inc/blocks/section-process/style.css',
        array(),
        filemtime($block_dir . '/style.css')
    );
    
    // Register the block using block.json
    register_block_type($block_dir, array(
        'editor_script' => 'julianboelen-section-process-editor',
        'style' => 'julianboelen-section-process-style',
        'render_callback' => 'julianboelen_render_section_process_block'
    ));
}
add_action('init', 'julianboelen_register_section_process_block');

/**
 * Render callback for the section-process block
 * 
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block instance
 * @return string Rendered block HTML
 */
function julianboelen_render_section_process_block($attributes, $content, $block) {
    // Start output buffering
    ob_start();
    
    // Include the render template
    $template_path = get_template_directory() . '/inc/blocks/section-process/render.php';
    
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        // Fallback error message for administrators
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('Section Process block template not found.', 'julianboelen');
            echo '</p></div>';
        }
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
function julianboelen_add_section_process_block_category($categories, $post) {
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
add_filter('block_categories_all', 'julianboelen_add_section_process_block_category', 10, 2);

/**
 * Enqueue block assets for both editor and frontend
 * 
 * @return void
 */
function julianboelen_section_process_block_assets() {
    // Enqueue frontend styles
    if (!is_admin()) {
        wp_enqueue_style('julianboelen-section-process-style');
    }
}
add_action('enqueue_block_assets', 'julianboelen_section_process_block_assets');

/**
 * Add inline styles for dynamic block styling
 * 
 * @return void
 */
function julianboelen_section_process_inline_styles() {
    // Only add on frontend
    if (is_admin()) {
        return;
    }
    
    // Check if block is being used on current page
    if (!has_block('julianboelen/section-process')) {
        return;
    }
    
    // Add any dynamic inline styles here if needed
    $custom_css = "
        /* Section Process Dynamic Styles */
    ";
    
    wp_add_inline_style('julianboelen-section-process-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'julianboelen_section_process_inline_styles', 20);

/**
 * Add block pattern for section-process
 * 
 * @return void
 */
function julianboelen_register_section_process_pattern() {
    // Register block pattern
    register_block_pattern(
        'julianboelen/section-process-default',
        array(
            'title' => __('Process Section - Default', 'julianboelen'),
            'description' => __('A streamlined process section with 4 steps', 'julianboelen'),
            'categories' => array('julianboelen-blocks'),
            'content' => '<!-- wp:julianboelen/section-process /-->',
            'keywords' => array('process', 'steps', 'workflow', 'timeline')
        )
    );
}
add_action('init', 'julianboelen_register_section_process_pattern');

/**
 * Add server-side rendering support for REST API
 * 
 * @param WP_REST_Response $response Response object
 * @param WP_Post $post Post object
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response Modified response
 */
function julianboelen_section_process_rest_support($response, $post, $request) {
    // Add rendered block content to REST API response
    if (has_block('julianboelen/section-process', $post)) {
        $blocks = parse_blocks($post->post_content);
        $rendered_blocks = array();
        
        foreach ($blocks as $block) {
            if ($block['blockName'] === 'julianboelen/section-process') {
                $rendered_blocks[] = render_block($block);
            }
        }
        
        $response->data['rendered_section_process'] = $rendered_blocks;
    }
    
    return $response;
}
add_filter('rest_prepare_post', 'julianboelen_section_process_rest_support', 10, 3);
add_filter('rest_prepare_page', 'julianboelen_section_process_rest_support', 10, 3);