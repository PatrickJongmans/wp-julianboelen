<?php
if (!defined('ABSPATH')) exit;
function tf_register_process_step_post_type() {
    register_post_type('process_step', array(
        'labels' => array(
            'name' => 'Process Steps',
            'singular_name' => 'Process Step',
            'menu_name' => 'Process Steps'
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-list-view',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'process-steps')
    ));
}
add_action('init', 'tf_register_process_step_post_type', 0);
if (file_exists(__DIR__ . '/metabox.php')) {
    require_once __DIR__ . '/metabox.php';
}