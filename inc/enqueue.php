<?php
/**
 * Asset Enqueue Manager
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * TF Asset Enqueue Manager
 */
class JulianboelenEnqueueManager {

    private $modules_dir;
    private $modules_url;

    /**
     * Constructor
     */
    public function __construct() {
        $this->modules_dir = JULIANBOELEN_THEME_DIR . '/assets/js/modules';
        $this->modules_url = JULIANBOELEN_THEME_URL . '/assets/js/modules';

        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_js_modules'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_js_modules'));
    }

    /**
     * Enqueue JavaScript modules for frontend
     */
    public function enqueue_js_modules() {
        $this->enqueue_modules_from_directory($this->modules_dir, $this->modules_url, 'frontend');
    }

    /**
     * Enqueue JavaScript modules for admin (optional)
     */
    public function enqueue_admin_js_modules() {
        // Only enqueue admin-specific modules if they exist
        $admin_modules_dir = $this->modules_dir . '/admin';
        $admin_modules_url = $this->modules_url . '/admin';

        if (is_dir($admin_modules_dir)) {
            $this->enqueue_modules_from_directory($admin_modules_dir, $admin_modules_url, 'admin');
        }
    }

    /**
     * Enqueue modules from a specific directory
     *
     * @param string $directory Directory path
     * @param string $url Directory URL
     * @param string $context Context (frontend/admin)
     */
    private function enqueue_modules_from_directory($directory, $url, $context = 'frontend') {
        if (!is_dir($directory)) {
            return;
        }

        $js_files = $this->get_js_files($directory);

        foreach ($js_files as $file_info) {
            $this->enqueue_single_module($file_info, $url, $context);
        }
    }

    /**
     * Get all JavaScript files from directory
     *
     * @param string $directory Directory path
     * @return array Array of file information
     */
    private function get_js_files($directory) {
        $js_files = array();

        if (!is_dir($directory)) {
            return $js_files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'js') {
                $relative_path = str_replace($directory . '/', '', $file->getPathname());
                $file_name = $file->getBasename('.js');

                $js_files[] = array(
                    'path' => $file->getPathname(),
                    'relative_path' => $relative_path,
                    'name' => $file_name,
                    'handle' => $this->generate_handle($file_name, $relative_path),
                );
            }
        }

        return $js_files;
    }

    /**
     * Enqueue a single JavaScript module
     *
     * @param array $file_info File information
     * @param string $base_url Base URL for modules
     * @param string $context Context (frontend/admin)
     */
    private function enqueue_single_module($file_info, $base_url, $context) {
        $handle = $file_info['handle'];
        $file_url = $base_url . '/' . $file_info['relative_path'];

        // Determine dependencies based on file name or content
        $dependencies = $this->get_module_dependencies($file_info);

        // Check if module should be loaded in footer
        $in_footer = $this->should_load_in_footer($file_info);

        // Enqueue the script
        wp_enqueue_script(
            $handle,
            $file_url,
            $dependencies,
            JULIANBOELEN_THEME_VERSION,
            $in_footer
        );

        // Add localization if needed
        $this->maybe_localize_script($handle, $file_info, $context);

        // Log for debugging (only in WP_DEBUG mode)
        if (WP_DEBUG) {
            error_log("TF Theme: Enqueued JS module '{$handle}' from '{$file_info['relative_path']}'");
        }
    }

    /**
     * Generate unique handle for script
     *
     * @param string $file_name File name without extension
     * @param string $relative_path Relative path from modules directory
     * @return string Script handle
     */
    private function generate_handle($file_name, $relative_path) {
        // Create handle based on file path to ensure uniqueness
        $path_parts = explode('/', dirname($relative_path));
        $path_parts[] = $file_name;

        // Remove empty parts and create handle
        $path_parts = array_filter($path_parts, function($part) {
            return $part !== '.' && $part !== '';
        });

        $handle = 'tf-' . implode('-', $path_parts);

        // Sanitize handle
        $handle = sanitize_key($handle);

        return $handle;
    }

    /**
     * Get dependencies for a module based on naming conventions or file content
     *
     * @param array $file_info File information
     * @return array Dependencies array
     */
    private function get_module_dependencies($file_info) {
        $dependencies = array();

        // Default dependency on jQuery for most modules
        $dependencies[] = 'jquery';

        // Check for specific dependencies based on file name
        $file_name = strtolower($file_info['name']);

        if (strpos($file_name, 'slider') !== false || strpos($file_name, 'carousel') !== false) {
            // Slider modules might need additional dependencies
        }

        if (strpos($file_name, 'ajax') !== false) {
            // AJAX modules need jQuery
            if (!in_array('jquery', $dependencies)) {
                $dependencies[] = 'jquery';
            }
        }

        // Check if file contains specific library references
        if (file_exists($file_info['path'])) {
            $file_content = file_get_contents($file_info['path']);

            // Check for common library usage
            if (strpos($file_content, 'wp.') !== false) {
                $dependencies[] = 'wp-util';
            }

            if (strpos($file_content, 'wp.ajax') !== false) {
                $dependencies[] = 'wp-util';
            }
        }

        return array_unique($dependencies);
    }

    /**
     * Determine if script should be loaded in footer
     *
     * @param array $file_info File information
     * @return bool True if should load in footer
     */
    private function should_load_in_footer($file_info) {
        $file_name = strtolower($file_info['name']);

        // Load in footer by default for better performance
        $in_footer = true;

        // Load in header for critical scripts
        if (strpos($file_name, 'critical') !== false || 
            strpos($file_name, 'header') !== false ||
            strpos($file_name, 'inline') !== false) {
            $in_footer = false;
        }

        return $in_footer;
    }

    /**
     * Maybe localize script with data
     *
     * @param string $handle Script handle
     * @param array $file_info File information
     * @param string $context Context (frontend/admin)
     */
    private function maybe_localize_script($handle, $file_info, $context) {
        $file_name = strtolower($file_info['name']);

        // Common localization data
        $localize_data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('julianboelen_nonce'),
            'theme_url' => JULIANBOELEN_THEME_URL,
        );

        // Add context-specific data
        if ($context === 'admin') {
            $localize_data['admin_url'] = admin_url();
        }

        // Add module-specific data based on file name
        if (strpos($file_name, 'ajax') !== false) {
            wp_localize_script($handle, 'julianboelen_ajax_data', $localize_data);
        } elseif (strpos($file_name, 'form') !== false) {
            wp_localize_script($handle, 'julianboelen_form_data', $localize_data);
        } elseif (strpos($file_name, 'gallery') !== false || strpos($file_name, 'media') !== false) {
            $localize_data['media_upload_url'] = admin_url('media-upload.php');
            wp_localize_script($handle, 'julianboelen_media_data', $localize_data);
        }

        // Generic localization for modules that might need it
        if (file_exists($file_info['path'])) {
            $file_content = file_get_contents($file_info['path']);

            if (strpos($file_content, 'julianboelen_data') !== false) {
                wp_localize_script($handle, 'julianboelen_data', $localize_data);
            }
        }
    }

    /**
     * Get all enqueued modules (for debugging)
     *
     * @return array Array of enqueued module handles
     */
    public function get_enqueued_modules() {
        global $wp_scripts;

        $julianboelen_modules = array();

        if (isset($wp_scripts->registered)) {
            foreach ($wp_scripts->registered as $handle => $script) {
                if (strpos($handle, 'tf-') === 0 && strpos($script->src, '/modules/') !== false) {
                    $julianboelen_modules[] = $handle;
                }
            }
        }

        return $julianboelen_modules;
    }
}

// Initialize the enqueue manager
new JulianboelenEnqueueManager();

/**
 * Helper function to manually enqueue a specific module
 *
 * @param string $module_name Module name (without .js extension)
 * @param array $dependencies Optional dependencies
 * @param bool $in_footer Load in footer
 */
function julianboelen_enqueue_module($module_name, $dependencies = array('jquery'), $in_footer = true) {
    $module_path = JULIANBOELEN_THEME_URL . '/assets/js/modules/' . $module_name . '.js';
    $handle = 'tf-module-' . sanitize_key($module_name);

    if (file_exists(JULIANBOELEN_THEME_DIR . '/assets/js/modules/' . $module_name . '.js')) {
        wp_enqueue_script($handle, $module_path, $dependencies, JULIANBOELEN_THEME_VERSION, $in_footer);
    }
}

/**
 * Helper function to check if a module is enqueued
 *
 * @param string $module_name Module name
 * @return bool True if enqueued
 */
function julianboelen_is_module_enqueued($module_name) {
    $handle = 'tf-module-' . sanitize_key($module_name);
    return wp_script_is($handle, 'enqueued');
}