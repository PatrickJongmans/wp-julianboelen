<?php
/**
 * Auto-load custom Gutenberg blocks
 * 
 * This file automatically discovers and loads all blocks that have
 * their own loader.php file in the inc/blocks/ directory.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Auto-discover and register all custom blocks
 */
function register_custom_blocks() {
    $blocks_dir = __DIR__ . '/blocks';
    
    // Check if blocks directory exists
    if (!is_dir($blocks_dir)) {
        return;
    }
    
    // Scan for block directories
    $block_dirs = array_filter(glob($blocks_dir . '/*'), 'is_dir');
    
    foreach ($block_dirs as $block_dir) {
        $loader_file = $block_dir . '/loader.php';
        
        // If the block has its own loader.php, include it
        if (file_exists($loader_file)) {
            require_once $loader_file;
        }
    }
}
add_action('init', 'register_custom_blocks');
?>