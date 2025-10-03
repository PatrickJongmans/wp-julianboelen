<?php
/**
 * Custom Post Types Loader
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * TF Custom Post Types Manager
 */
class JulianboelenPostTypesManager {

    private $post_types_dir;

    /**
     * Constructor
     */
    public function __construct() {
        $this->post_types_dir = JULIANBOELEN_THEME_DIR . '/inc/post_types';

        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'register_post_types'));
    }

    /**
     * Register all custom post types
     */
    public function register_post_types() {
        // Get all post type directories
        $post_type_dirs = $this->get_post_type_directories();

        foreach ($post_type_dirs as $post_type_name => $post_type_path) {
            $this->register_single_post_type($post_type_name, $post_type_path);
        }
    }

    /**
     * Get all post type directories
     *
     * @return array Array of post type directories
     */
    private function get_post_type_directories() {
        $post_types = array();

        if (!is_dir($this->post_types_dir)) {
            return $post_types;
        }

        $iterator = new DirectoryIterator($this->post_types_dir);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot() || !$fileinfo->isDir()) {
                continue;
            }

            $post_type_name = $fileinfo->getFilename();
            $post_type_path = $fileinfo->getPathname();

            // Skip the current directory (post_types.php is in this directory)
            if ($post_type_name === '.' || $post_type_name === '..') {
                continue;
            }

            // Check if loader.php exists
            if (file_exists($post_type_path . '/loader.php')) {
                $post_types[$post_type_name] = $post_type_path;
            }
        }

        return $post_types;
    }

    /**
     * Register a single custom post type
     *
     * @param string $post_type_name Post type name
     * @param string $post_type_path Post type directory path
     */
    private function register_single_post_type($post_type_name, $post_type_path) {
        $loader_file = $post_type_path . '/loader.php';

        if (!file_exists($loader_file)) {
            return;
        }

        // Include the loader file
        include_once $loader_file;

        // Check if the post type registration function exists
        $function_name = 'julianboelen_register_' . str_replace('-', '_', $post_type_name) . '_post_type';

        if (function_exists($function_name)) {
            call_user_func($function_name);
        } else {
            // Log error if function doesn't exist
            if (WP_DEBUG) {
                error_log("TF Theme: Post type registration function '{$function_name}' not found in {$loader_file}");
            }
        }
    }

    /**
     * Helper method to get post type labels
     *
     * @param string $singular Singular name
     * @param string $plural Plural name
     * @return array Labels array
     */
    public static function get_post_type_labels($singular, $plural) {
        return array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $plural,
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(__('%s Archives', 'julianboelen'), $singular),
            'attributes'            => sprintf(__('%s Attributes', 'julianboelen'), $singular),
            'parent_item_colon'     => sprintf(__('Parent %s:', 'julianboelen'), $singular),
            'all_items'             => sprintf(__('All %s', 'julianboelen'), $plural),
            'add_new_item'          => sprintf(__('Add New %s', 'julianboelen'), $singular),
            'add_new'               => __('Add New', 'julianboelen'),
            'new_item'              => sprintf(__('New %s', 'julianboelen'), $singular),
            'edit_item'             => sprintf(__('Edit %s', 'julianboelen'), $singular),
            'update_item'           => sprintf(__('Update %s', 'julianboelen'), $singular),
            'view_item'             => sprintf(__('View %s', 'julianboelen'), $singular),
            'view_items'            => sprintf(__('View %s', 'julianboelen'), $plural),
            'search_items'          => sprintf(__('Search %s', 'julianboelen'), $plural),
            'not_found'             => __('Not found', 'julianboelen'),
            'not_found_in_trash'    => __('Not found in Trash', 'julianboelen'),
            'featured_image'        => __('Featured Image', 'julianboelen'),
            'set_featured_image'    => __('Set featured image', 'julianboelen'),
            'remove_featured_image' => __('Remove featured image', 'julianboelen'),
            'use_featured_image'    => __('Use as featured image', 'julianboelen'),
            'insert_into_item'      => sprintf(__('Insert into %s', 'julianboelen'), strtolower($singular)),
            'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'julianboelen'), strtolower($singular)),
            'items_list'            => sprintf(__('%s list', 'julianboelen'), $plural),
            'items_list_navigation' => sprintf(__('%s list navigation', 'julianboelen'), $plural),
            'filter_items_list'     => sprintf(__('Filter %s list', 'julianboelen'), strtolower($plural)),
        );
    }

    /**
     * Helper method to get default post type arguments
     *
     * @param array $labels Labels array
     * @param array $args Additional arguments
     * @return array Complete arguments array
     */
    public static function get_post_type_args($labels, $args = array()) {
        $defaults = array(
            'label'                 => $labels['name'],
            'description'           => '',
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
            'taxonomies'            => array(),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-admin-post',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        return wp_parse_args($args, $defaults);
    }
}

// Initialize the post types manager
new JulianboelenPostTypesManager();

/**
 * Helper function to register a custom post type
 *
 * @param string $post_type Post type key
 * @param string $singular Singular name
 * @param string $plural Plural name
 * @param array $args Additional arguments
 */
function julianboelen_register_custom_post_type($post_type, $singular, $plural, $args = array()) {
    $labels = JulianboelenPostTypesManager::get_post_type_labels($singular, $plural);
    $post_type_args = JulianboelenPostTypesManager::get_post_type_args($labels, $args);

    register_post_type($post_type, $post_type_args);
}