<?php
/**
 * Section Wide Image Block Loader
 * 
 * Handles block registration and asset enqueueing
 * 
 * @package JulianBoelen
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the Section Wide Image block
 * 
 * @return void
 */
function julianboelen_register_section_wide_image_block() {
    // Check if block registration function exists
    if (!function_exists('register_block_type')) {
        return;
    }

    // Define block directory path
    $block_dir = dirname(__FILE__);

    // Register block editor script
    $editor_script_asset_path = $block_dir . '/build/index.asset.php';
    $editor_script_asset = file_exists($editor_script_asset_path)
        ? require $editor_script_asset_path
        : ['dependencies' => ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'], 'version' => '1.0.0'];

    wp_register_script(
        'julianboelen-section-wide-image-editor',
        plugins_url('editor.js', __FILE__),
        $editor_script_asset['dependencies'],
        $editor_script_asset['version'],
        true
    );

    // Register block editor styles
    wp_register_style(
        'julianboelen-section-wide-image-editor-style',
        plugins_url('style.css', __FILE__),
        ['wp-edit-blocks'],
        filemtime($block_dir . '/style.css')
    );

    // Register block frontend styles
    wp_register_style(
        'julianboelen-section-wide-image-style',
        plugins_url('style.css', __FILE__),
        [],
        filemtime($block_dir . '/style.css')
    );

    // Register AOS library for animations
    wp_register_style(
        'aos-css',
        'https://unpkg.com/aos@2.3.4/dist/aos.css',
        [],
        '2.3.4'
    );

    wp_register_script(
        'aos-js',
        'https://unpkg.com/aos@2.3.4/dist/aos.js',
        [],
        '2.3.4',
        true
    );

    // Register the block
    register_block_type($block_dir, [
        'editor_script' => 'julianboelen-section-wide-image-editor',
        'editor_style' => 'julianboelen-section-wide-image-editor-style',
        'style' => 'julianboelen-section-wide-image-style',
        'render_callback' => 'julianboelen_render_section_wide_image_block'
    ]);
}

add_action('init', 'julianboelen_register_section_wide_image_block');

/**
 * Render callback for the Section Wide Image block
 * 
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block object
 * @return string Rendered block HTML
 */
function julianboelen_render_section_wide_image_block($attributes, $content, $block) {
    // Enqueue AOS library if animation is enabled
    if (isset($attributes['enableAnimation']) && $attributes['enableAnimation']) {
        wp_enqueue_style('aos-css');
        wp_enqueue_script('aos-js');
    }

    // Start output buffering
    ob_start();

    // Include the render template
    include dirname(__FILE__) . '/render.php';

    // Return the buffered content
    return ob_get_clean();
}

/**
 * Enqueue block assets for frontend
 * 
 * @return void
 */
function julianboelen_section_wide_image_enqueue_assets() {
    // Only enqueue on frontend if block is present
    if (!is_admin() && has_block('julianboelen/section-wide-image')) {
        wp_enqueue_style('julianboelen-section-wide-image-style');
        
        // Check if any block on the page has animation enabled
        global $post;
        if ($post && has_block('julianboelen/section-wide-image', $post)) {
            $blocks = parse_blocks($post->post_content);
            foreach ($blocks as $block) {
                if ($block['blockName'] === 'julianboelen/section-wide-image' && 
                    isset($block['attrs']['enableAnimation']) && 
                    $block['attrs']['enableAnimation']) {
                    wp_enqueue_style('aos-css');
                    wp_enqueue_script('aos-js');
                    break;
                }
            }
        }
    }
}

add_action('wp_enqueue_scripts', 'julianboelen_section_wide_image_enqueue_assets');

/**
 * Add block category if it doesn't exist
 * 
 * @param array $categories Existing block categories
 * @param WP_Post $post Current post object
 * @return array Modified block categories
 */
function julianboelen_section_wide_image_block_category($categories, $post) {
    // Check if our category already exists
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
            $categories,
            [
                [
                    'slug' => 'julianboelen-blocks',
                    'title' => __('Julian Boelen Blocks', 'julianboelen'),
                    'icon' => 'layout'
                ]
            ]
        );
    }

    return $categories;
}

add_filter('block_categories_all', 'julianboelen_section_wide_image_block_category', 10, 2);

/**
 * Add inline AOS initialization script
 * 
 * @return void
 */
function julianboelen_section_wide_image_aos_init() {
    // Only add on frontend if AOS is enqueued
    if (!is_admin() && wp_script_is('aos-js', 'enqueued')) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined' && !window.aosInitialized) {
                AOS.init({
                    duration: 1200,
                    once: true,
                    offset: 100,
                    easing: 'ease-in-out',
                    disable: false,
                    startEvent: 'DOMContentLoaded',
                    initClassName: 'aos-init',
                    animatedClassName: 'aos-animate',
                    useClassNames: false,
                    disableMutationObserver: false,
                    debounceDelay: 50,
                    throttleDelay: 99
                });
                window.aosInitialized = true;
            }
        });
        </script>
        <?php
    }
}

add_action('wp_footer', 'julianboelen_section_wide_image_aos_init', 100);

/**
 * Add preload hints for better performance
 * 
 * @return void
 */
function julianboelen_section_wide_image_preload_hints() {
    if (has_block('julianboelen/section-wide-image')) {
        global $post;
        if ($post && has_block('julianboelen/section-wide-image', $post)) {
            $blocks = parse_blocks($post->post_content);
            foreach ($blocks as $block) {
                if ($block['blockName'] === 'julianboelen/section-wide-image' && 
                    isset($block['attrs']['imageUrl']) && 
                    !empty($block['attrs']['imageUrl'])) {
                    // Add preload hint for the first image
                    echo '<link rel="preload" as="image" href="' . esc_url($block['attrs']['imageUrl']) . '" />' . "\n";
                    break; // Only preload the first image
                }
            }
        }
    }
}

add_action('wp_head', 'julianboelen_section_wide_image_preload_hints', 5);

/**
 * Add custom image sizes for the block
 * 
 * @return void
 */
function julianboelen_section_wide_image_custom_sizes() {
    // Add custom image size for wide images
    add_image_size('section-wide-image', 1920, 800, true);
    add_image_size('section-wide-image-medium', 1280, 533, true);
    add_image_size('section-wide-image-small', 768, 320, true);
}

add_action('after_setup_theme', 'julianboelen_section_wide_image_custom_sizes');

/**
 * Make custom image sizes selectable in media library
 * 
 * @param array $sizes Existing image sizes
 * @return array Modified image sizes
 */
function julianboelen_section_wide_image_custom_sizes_names($sizes) {
    return array_merge($sizes, [
        'section-wide-image' => __('Section Wide Image - Large', 'julianboelen'),
        'section-wide-image-medium' => __('Section Wide Image - Medium', 'julianboelen'),
        'section-wide-image-small' => __('Section Wide Image - Small', 'julianboelen')
    ]);
}

add_filter('image_size_names_choose', 'julianboelen_section_wide_image_custom_sizes_names');