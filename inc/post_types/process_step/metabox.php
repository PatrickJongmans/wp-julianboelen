<?php
if (!defined('ABSPATH')) exit;

function tf_process_step_meta_boxes() {
    add_meta_box(
        'process_step_details',
        'Process Step Details',
        'tf_process_step_meta_callback',
        'process_step',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'tf_process_step_meta_boxes');

function tf_process_step_meta_callback($post) {
    wp_nonce_field('tf_process_step_meta', 'tf_process_step_nonce');
    
    $order = get_post_meta($post->ID, '_process_step_order', true);
    $duration = get_post_meta($post->ID, '_process_step_duration', true);
    $icon = get_post_meta($post->ID, '_process_step_icon', true);
    
    echo '<div style="margin-bottom: 15px;">';
    echo '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Step Order:</label>';
    echo '<input type="number" name="process_step_order" value="' . esc_attr($order) . '" min="1" style="width: 100px;" />';
    echo '<p class="description">Determines the display order of this step (lower numbers appear first)</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom: 15px;">';
    echo '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Duration (optional):</label>';
    echo '<input type="text" name="process_step_duration" value="' . esc_attr($duration) . '" placeholder="e.g., 2 weeks, 3 days" style="width: 100%;" />';
    echo '<p class="description">Estimated time to complete this step</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom: 15px;">';
    echo '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Icon Class (optional):</label>';
    echo '<input type="text" name="process_step_icon" value="' . esc_attr($icon) . '" placeholder="e.g., dashicons-admin-tools" style="width: 100%;" />';
    echo '<p class="description">CSS class for icon (e.g., Dashicons or Font Awesome)</p>';
    echo '</div>';
}

function tf_save_process_step_meta($post_id) {
    if (!isset($_POST['tf_process_step_nonce']) || !wp_verify_nonce($_POST['tf_process_step_nonce'], 'tf_process_step_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'process_step') return;
    
    if (isset($_POST['process_step_order'])) {
        update_post_meta($post_id, '_process_step_order', absint($_POST['process_step_order']));
    }
    
    if (isset($_POST['process_step_duration'])) {
        update_post_meta($post_id, '_process_step_duration', sanitize_text_field($_POST['process_step_duration']));
    }
    
    if (isset($_POST['process_step_icon'])) {
        update_post_meta($post_id, '_process_step_icon', sanitize_text_field($_POST['process_step_icon']));
    }
}
add_action('save_post', 'tf_save_process_step_meta');