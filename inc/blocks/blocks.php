<?php
/**
 * Gutenberg Blocks Registration
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * TF Blocks Manager
 */
class JulianboelenBlocksManager {
    
    private $blocks_dir;
    private $blocks_url;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->blocks_dir = JULIANBOELEN_THEME_DIR . '/inc/blocks';
        $this->blocks_url = JULIANBOELEN_THEME_URL . '/inc/blocks';
        
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_block_assets'));
    }
    
    /**
     * Register all blocks
     */
    public function register_blocks() {
        // Get all block directories
        $block_dirs = $this->get_block_directories();
        
        foreach ($block_dirs as $block_name => $block_path) {
            $this->register_single_block($block_name, $block_path);
        }
    }
    
    /**
     * Get all block directories
     *
     * @return array Array of block directories
     */
    private function get_block_directories() {
        $blocks = array();
        
        if (!is_dir($this->blocks_dir)) {
            return $blocks;
        }
        
        $iterator = new DirectoryIterator($this->blocks_dir);
        
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot() || !$fileinfo->isDir()) {
                continue;
            }
            
            $block_name = $fileinfo->getFilename();
            $block_path = $fileinfo->getPathname();
            
            // Check if loader.php exists
            if (file_exists($block_path . '/loader.php')) {
                $blocks[$block_name] = $block_path;
            }
        }
        
        return $blocks;
    }
    
    /**
     * Register a single block
     *
     * @param string $block_name Block name
     * @param string $block_path Block directory path
     */
    private function register_single_block($block_name, $block_path) {
        $loader_file = $block_path . '/loader.php';
        
        if (!file_exists($loader_file)) {
            return;
        }
        
        // Include the loader file
        include_once $loader_file;
        
        // Check if the block registration function exists
        $function_name = 'julianboelen_register_' . str_replace('-', '_', $block_name) . '_block';
        
        if (function_exists($function_name)) {
            call_user_func($function_name);
        } else {
            // Fallback: automatic registration based on file structure
            $this->auto_register_block($block_name, $block_path);
        }
    }
    
    /**
     * Auto-register block based on file structure
     *
     * @param string $block_name Block name
     * @param string $block_path Block directory path
     */
    private function auto_register_block($block_name, $block_path) {
        $block_args = array(
            'render_callback' => array($this, 'render_block_callback'),
        );
        
        // Check for editor script
        if (file_exists($block_path . '/editor.js')) {
            $editor_script_handle = 'tf-' . $block_name . '-editor';
            wp_register_script(
                $editor_script_handle,
                $this->blocks_url . '/' . $block_name . '/editor.js',
                array('wp-blocks', 'wp-element', 'wp-editor'),
                JULIANBOELEN_THEME_VERSION
            );
            $block_args['editor_script'] = $editor_script_handle;
        }
        
        // Check for editor style
        if (file_exists($block_path . '/editor.css')) {
            $editor_style_handle = 'tf-' . $block_name . '-editor';
            wp_register_style(
                $editor_style_handle,
                $this->blocks_url . '/' . $block_name . '/editor.css',
                array(),
                JULIANBOELEN_THEME_VERSION
            );
            $block_args['editor_style'] = $editor_style_handle;
        }
        
        // Check for frontend style
        if (file_exists($block_path . '/style.css')) {
            $style_handle = 'tf-' . $block_name;
            wp_register_style(
                $style_handle,
                $this->blocks_url . '/' . $block_name . '/style.css',
                array(),
                JULIANBOELEN_THEME_VERSION
            );
            $block_args['style'] = $style_handle;
        }
        
        // Set render callback data
        $block_args['render_callback_data'] = array(
            'block_name' => $block_name,
            'block_path' => $block_path
        );
        
        // Register the block
        register_block_type('julianboelen/' . $block_name, $block_args);
    }
    
    /**
     * Block render callback
     *
     * @param array $attributes Block attributes
     * @param string $content Block content
     * @param object $block Block object
     * @return string Rendered block HTML
     */
    public function render_block_callback($attributes, $content, $block) {
        $block_data = $block->block_type->render_callback_data ?? null;
        
        if (!$block_data) {
            return '';
        }
        
        $render_file = $block_data['block_path'] . '/render.php';
        
        if (!file_exists($render_file)) {
            return '';
        }
        
        // Start output buffering
        ob_start();
        
        // Make variables available to render file
        $block_name = $block_data['block_name'];
        $block_path = $block_data['block_path'];
        
        // Include render file
        include $render_file;
        
        // Return buffered output
        return ob_get_clean();
    }
    
    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        // Enqueue common editor styles if they exist
        $common_editor_css = JULIANBOELEN_THEME_URL . '/assets/css/blocks-editor.css';
        
        if (file_exists(JULIANBOELEN_THEME_DIR . '/assets/css/blocks-editor.css')) {
            wp_enqueue_style(
                'tf-blocks-editor',
                $common_editor_css,
                array(),
                JULIANBOELEN_THEME_VERSION
            );
        }
        
        // Enqueue common editor script if it exists
        $common_editor_js = JULIANBOELEN_THEME_URL . '/assets/js/blocks-editor.js';
        
        if (file_exists(JULIANBOELEN_THEME_DIR . '/assets/js/blocks-editor.js')) {
            wp_enqueue_script(
                'tf-blocks-editor',
                $common_editor_js,
                array('wp-blocks', 'wp-element', 'wp-editor'),
                JULIANBOELEN_THEME_VERSION
            );
        }
    }
    
    /**
     * Enqueue block assets on frontend
     */
    public function enqueue_block_assets() {
        // Enqueue common block styles if they exist
        $common_blocks_css = JULIANBOELEN_THEME_URL . '/assets/css/blocks.css';
        
        if (file_exists(JULIANBOELEN_THEME_DIR . '/assets/css/blocks.css')) {
            wp_enqueue_style(
                'tf-blocks',
                $common_blocks_css,
                array(),
                JULIANBOELEN_THEME_VERSION
            );
        }
    }
    
    /**
     * Get block attribute with default value
     *
     * @param array $attributes Block attributes
     * @param string $key Attribute key
     * @param mixed $default Default value
     * @return mixed Attribute value
     */
    public static function get_attribute($attributes, $key, $default = '') {
        return isset($attributes[$key]) ? $attributes[$key] : $default;
    }
    
    /**
     * Sanitize block classes
     *
     * @param array $attributes Block attributes
     * @return string Sanitized classes
     */
    public static function get_block_classes($attributes) {
        $classes = array();
        
        // Add custom classes
        if (!empty($attributes['className'])) {
            $classes[] = $attributes['className'];
        }
        
        // Add alignment classes
        if (!empty($attributes['align'])) {
            $classes[] = 'align' . $attributes['align'];
        }
        
        return implode(' ', $classes);
    }
}

// Initialize the blocks manager
new JulianboelenBlocksManager();

/**
 * Helper function to get theme option from blocks
 *
 * @param string $key Option key
 * @param mixed $default Default value
 * @return mixed Option value
 */
function julianboelen_get_theme_option($key, $default = '') {
    global $julianboelen_theme_options;
    
    if ($julianboelen_theme_options && method_exists($julianboelen_theme_options, 'get_option')) {
        return $julianboelen_theme_options->get_option($key, $default);
    }
    
    return $default;
}

/**
 * Block categories filter
 */
function julianboelen_block_categories($categories, $post) {
    return array_merge(
        $categories,
        array(
            array(
                'slug'  => 'tf-blocks',
                'title' => __('TF Blocks', 'julianboelen'),
                'icon'  => 'star-filled',
            ),
        )
    );
}
add_filter('block_categories_all', 'julianboelen_block_categories', 10, 2);