<?php
/**
 * Theme Options Class
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * TF Theme Options Class
 */
class JulianboelenThemeOptions {
    
    private $options;
    private $config;
    private $option_name = 'julianboelen_theme_options';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->load_config();
        $this->init_hooks();
        $this->load_options();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Load configuration from JSON file
     */
    private function load_config() {
        $config_file = JULIANBOELEN_THEME_DIR . '/config/theme-options.json';
        
        if (file_exists($config_file)) {
            $json_content = file_get_contents($config_file);
            $this->config = json_decode($json_content, true);
        } else {
            $this->config = array();
        }
    }
    
    /**
     * Load saved options
     */
    private function load_options() {
        $this->options = get_option($this->option_name, array());
    }
    
    /**
     * Get option value
     *
     * @param string $key Option key
     * @param mixed $default Default value
     * @return mixed Option value
     */
    public function get_option($key, $default = '') {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }
    
    /**
     * Set option value
     *
     * @param string $key Option key
     * @param mixed $value Option value
     */
    public function set_option($key, $value) {
        $this->options[$key] = $value;
        update_option($this->option_name, $this->options);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_theme_page(
            __('Theme Options', 'julianboelen'),
            __('Theme Options', 'julianboelen'),
            'manage_options',
            'julianboelen-options',
            array($this, 'options_page')
        );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting(
            'julianboelen_theme_options_group',
            $this->option_name,
            array($this, 'sanitize_options')
        );
        
        // Add settings sections and fields based on config
        foreach ($this->config as $key => $field_config) {
            add_settings_section(
                $key . '_section',
                $this->format_label($key),
                null,
                'julianboelen-options'
            );
            
            add_settings_field(
                $key,
                $this->format_label($key),
                array($this, 'render_field'),
                'julianboelen-options',
                $key . '_section',
                array('key' => $key, 'config' => $field_config)
            );
        }
    }
    
    /**
     * Sanitize options
     *
     * @param array $input Input data
     * @return array Sanitized data
     */
    public function sanitize_options($input) {
        $sanitized = array();
        
        foreach ($this->config as $key => $field_config) {
            if (isset($input[$key])) {
                switch ($field_config['type']) {
                    case 'string':
                        $sanitized[$key] = sanitize_text_field($input[$key]);
                        break;
                    case 'textarea':
                        $sanitized[$key] = sanitize_textarea_field($input[$key]);
                        break;
                    case 'html':
                        $sanitized[$key] = wp_kses_post($input[$key]);
                        break;
                    case 'image':
                        $sanitized[$key] = esc_url_raw($input[$key]);
                        break;
                    case 'url':
                        $sanitized[$key] = esc_url_raw($input[$key]);
                        break;
                    case 'array':
                        $sanitized[$key] = $this->sanitize_array($input[$key], $field_config);
                        break;
                    default:
                        $sanitized[$key] = sanitize_text_field($input[$key]);
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize array fields
     *
     * @param array $input Input array
     * @param array $config Field configuration
     * @return array Sanitized array
     */
    private function sanitize_array($input, $config) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        
        foreach ($input as $index => $item) {
            if (is_array($item)) {
                foreach ($item as $sub_key => $sub_value) {
                    if (isset($config['items'][$sub_key])) {
                        $sub_config = $config['items'][$sub_key];
                        
                        switch ($sub_config['type']) {
                            case 'url':
                                $sanitized[$index][$sub_key] = esc_url_raw($sub_value);
                                break;
                            case 'image':
                                $sanitized[$index][$sub_key] = esc_url_raw($sub_value);
                                break;
                            default:
                                $sanitized[$index][$sub_key] = sanitize_text_field($sub_value);
                        }
                    }
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Render field
     *
     * @param array $args Field arguments
     */
    public function render_field($args) {
        $key = $args['key'];
        $config = $args['config'];
        $value = $this->get_option($key);
        $field_name = $this->option_name . '[' . $key . ']';
        
        switch ($config['type']) {
            case 'string':
                printf(
                    '<input type="text" id="%s" name="%s" value="%s" class="regular-text" />',
                    esc_attr($key),
                    esc_attr($field_name),
                    esc_attr($value)
                );
                break;
                
            case 'textarea':
                printf(
                    '<textarea id="%s" name="%s" rows="5" cols="50" class="large-text">%s</textarea>',
                    esc_attr($key),
                    esc_attr($field_name),
                    esc_textarea($value)
                );
                break;
                
            case 'html':
                wp_editor($value, $key, array(
                    'textarea_name' => $field_name,
                    'media_buttons' => true,
                    'textarea_rows' => 10,
                ));
                break;
                
            case 'image':
                printf(
                    '<div class="image-field-container">
                        <input type="hidden" id="%s" name="%s" value="%s" />
                        <div class="image-buttons">
                            <input type="button" id="%s_button" class="button" value="%s" />
                            %s
                        </div>
                        <div id="%s_preview" class="image-preview%s">%s</div>
                    </div>',
                    esc_attr($key),
                    esc_attr($field_name),
                    esc_attr($value),
                    esc_attr($key),
                    __('Select Image', 'julianboelen'),
                    $value ? '<button type="button" class="button remove-image" data-input-id="' . esc_attr($key) . '">' . __('Remove Image', 'julianboelen') . '</button>' : '',
                    esc_attr($key),
                    $value ? ' has-image' : '',
                    $value ? '<img src="' . esc_url($value) . '" style="max-width: 200px; height: auto;" />' : '<p>' . __('No image selected', 'julianboelen') . '</p>'
                );
                break;
                
            case 'array':
                $this->render_array_field($key, $field_name, $value, $config);
                break;
        }
    }
    
    /**
     * Render array field (generic for any array type)
     *
     * @param string $key Field key from config
     * @param string $field_name Full field name for form
     * @param array $value Field value
     * @param array $config Field configuration
     */
    private function render_array_field($key, $field_name, $value, $config) {
        if (!is_array($value)) {
            $value = array();
        }
        
        $items_config = $config['items'] ?? array();
        $field_label = $this->format_label($key);
        
        echo '<div class="array-field" data-field-key="' . esc_attr($key) . '" data-field-name="' . esc_attr($field_name) . '">';
        echo '<div class="array-items-container">';
        
        // Render existing items
        foreach ($value as $index => $item_data) {
            echo '<div class="array-item">';
            echo '<div class="array-item-header">';
            echo '<span class="array-item-title">' . esc_html($field_label) . ' ' . ($index + 1) . '</span>';
            echo '<div class="array-item-actions">';
            echo '<span class="sort-handle dashicons dashicons-menu"></span>';
            echo '</div>';
            echo '</div>';
            
            // Render each sub-field based on config
            foreach ($items_config as $sub_key => $sub_config) {
                $sub_value = $item_data[$sub_key] ?? '';
                $sub_label = $this->format_label($sub_key);
                $sub_name = $field_name . '[' . $index . '][' . $sub_key . ']';
                
                echo '<label>' . esc_html($sub_label) . ':</label><br/>';
                
                switch ($sub_config['type']) {
                    case 'string':
                        printf(
                            '<input type="text" name="%s" value="%s" /><br/>',
                            esc_attr($sub_name),
                            esc_attr($sub_value)
                        );
                        break;
                    case 'url':
                        printf(
                            '<input type="url" name="%s" value="%s" /><br/>',
                            esc_attr($sub_name),
                            esc_attr($sub_value)
                        );
                        break;
                    case 'image':
                        printf(
                            '<div class="image-field-container" data-subkey="%s">
                                <input type="hidden" name="%s" value="%s" />
                                <div class="image-buttons">
                                    <button type="button" class="button image-select">%s</button>
                                    %s
                                </div>
                                <div class="image-preview%s">%s</div>
                            </div><br/>',
                            esc_attr($sub_key),
                            esc_attr($sub_name),
                            esc_attr($sub_value),
                            __('Select Image', 'julianboelen'),
                            $sub_value ? '<button type="button" class="button remove-image">' . __('Remove', 'julianboelen') . '</button>' : '',
                            $sub_value ? ' has-image' : '',
                            $sub_value ? '<img src="' . esc_url($sub_value) . '" style="max-width: 50px; height: auto;" />' : '<p>' . __('No image selected', 'julianboelen') . '</p>'
                        );
                        break;
                }
            }
            
            echo '<button type="button" class="button remove-array-item">Remove</button>';
            echo '</div>';
        }
        
        echo '</div>';
        
        // Create template for new items
        echo '<template class="array-item-template">';
        echo '<div class="array-item">';
        echo '<div class="array-item-header">';
        echo '<span class="array-item-title">' . esc_html($field_label) . ' {INDEX_PLUS_1}</span>';
        echo '<div class="array-item-actions">';
        echo '<span class="sort-handle dashicons dashicons-menu"></span>';
        echo '</div>';
        echo '</div>';
        
        foreach ($items_config as $sub_key => $sub_config) {
            $sub_label = $this->format_label($sub_key);
            $sub_name = $field_name . '[{INDEX}][' . $sub_key . ']';
            
            echo '<label>' . esc_html($sub_label) . ':</label><br/>';
            
            switch ($sub_config['type']) {
                case 'string':
                    printf(
                        '<input type="text" name="%s" value="" /><br/>',
                        esc_attr($sub_name)
                    );
                    break;
                case 'url':
                    printf(
                        '<input type="url" name="%s" value="" /><br/>',
                        esc_attr($sub_name)
                    );
                    break;
                case 'image':
                    printf(
                        '<div class="image-field-container" data-subkey="%s">
                            <input type="hidden" name="%s" value="" />
                            <div class="image-buttons">
                                <button type="button" class="button image-select">%s</button>
                            </div>
                            <div class="image-preview"><p>%s</p></div>
                        </div><br/>',
                        esc_attr($sub_key),
                        esc_attr($sub_name),
                        __('Select Image', 'julianboelen'),
                        __('No image selected', 'julianboelen')
                    );
                    break;
            }
        }
        
        echo '<button type="button" class="button remove-array-item">Remove</button>';
        echo '</div>';
        echo '</template>';
        
        echo '<button type="button" class="button add-array-item">Add ' . esc_html($field_label) . '</button>';
        echo '</div>';
    }
    
    /**
     * Format label from key
     *
     * @param string $key Option key
     * @return string Formatted label
     */
    private function format_label($key) {
        return ucwords(str_replace('_', ' ', $key));
    }
    
    /**
     * Enqueue admin assets
     *
     * @param string $hook Page hook
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'appearance_page_julianboelen-options') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('tf-admin', JULIANBOELEN_THEME_URL . '/assets/js/admin.js', array('jquery'), JULIANBOELEN_THEME_VERSION, true);
        wp_enqueue_style('tf-admin', JULIANBOELEN_THEME_URL . '/assets/css/admin.css', array(), JULIANBOELEN_THEME_VERSION);
    }
    
    /**
     * Options page HTML
     */
    public function options_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Theme Options', 'julianboelen'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('julianboelen_theme_options_group');
                do_settings_sections('julianboelen-options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}