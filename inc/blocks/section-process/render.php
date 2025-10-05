<?php
/**
 * Section Process Block Template
 * 
 * @param array $attributes Block attributes
 * @param string $content Block content
 * @param WP_Block $block Block object
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Extract attributes with defaults and apply security filters
$section_title = $attributes['sectionTitle'] ?? 'Een gestroomlijnd proces';
$background_color = $attributes['backgroundColor'] ?? '#f9fafb';
$title_color = $attributes['titleColor'] ?? '#111827';
$columns_desktop = $attributes['columnsDesktop'] ?? '4';
$columns_tablet = $attributes['columnsTablet'] ?? '2';
$columns_mobile = $attributes['columnsMobile'] ?? '1';
$card_background_color = $attributes['cardBackgroundColor'] ?? '#ffffff';
$card_text_color = $attributes['cardTextColor'] ?? '#111827';
$card_description_color = $attributes['cardDescriptionColor'] ?? '#6b7280';
$enable_hover_effect = $attributes['enableHoverEffect'] ?? true;
$card_border_radius = $attributes['cardBorderRadius'] ?? '16';
$gap_size = $attributes['gapSize'] ?? '24';
$show_step_numbers = $attributes['showStepNumbers'] ?? true;
$step_number_style = $attributes['stepNumberStyle'] ?? 'prefix';
$use_custom_post_type = $attributes['useCustomPostType'] ?? true;
$posts_per_page = $attributes['postsPerPage'] ?? -1;

// Query Process Steps from custom post type
$process_steps = [];

if ($use_custom_post_type) {
    $args = array(
        'post_type' => 'process_step',
        'posts_per_page' => $posts_per_page,
        'post_status' => 'publish',
        'orderby' => 'meta_value_num',
        'meta_key' => 'order',
        'order' => 'ASC'
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $step_index = 1;
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Get custom field for order
            $order = get_post_meta($post_id, 'order', true);
            
            // Get featured image
            $image_id = get_post_thumbnail_id($post_id);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
            $image_alt = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : get_the_title();
            
            $process_steps[] = [
                'id' => 'step-' . $post_id,
                'stepNumber' => str_pad($step_index, 2, '0', STR_PAD_LEFT),
                'title' => get_the_title(),
                'description' => get_the_content(),
                'imageUrl' => $image_url,
                'imageAlt' => $image_alt ?: get_the_title(),
                'imageId' => $image_id,
                'order' => $order
            ];
            
            $step_index++;
        }
        wp_reset_postdata();
    }
} else {
    // Fallback to hardcoded steps from attributes
    $process_steps = $attributes['processSteps'] ?? [];
}

// Helper functions scoped to this template
$get_grid_columns_class = function($desktop, $tablet, $mobile) {
    $desktop_class = 'lg:grid-cols-' . esc_attr($desktop);
    $tablet_class = 'md:grid-cols-' . esc_attr($tablet);
    $mobile_class = 'grid-cols-' . esc_attr($mobile);
    return $mobile_class . ' ' . $tablet_class . ' ' . $desktop_class;
};

$get_gap_class = function($size) {
    $gap_map = [
        '16' => 'gap-4',
        '24' => 'gap-6',
        '32' => 'gap-8',
        '40' => 'gap-10'
    ];
    return $gap_map[$size] ?? 'gap-6';
};

$get_border_radius_class = function($radius) {
    $radius_map = [
        '0' => 'rounded-none',
        '8' => 'rounded-lg',
        '12' => 'rounded-xl',
        '16' => 'rounded-2xl',
        '24' => 'rounded-3xl'
    ];
    return $radius_map[$radius] ?? 'rounded-2xl';
};

$render_step_number = function($step_number, $style, $show) {
    if (!$show || $style === 'none') {
        return '';
    }
    
    if ($style === 'badge') {
        return '<span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold mb-2" aria-hidden="true">' . esc_html($step_number) . '</span>';
    }
    
    if ($style === 'prefix') {
        return esc_html($step_number) . '. ';
    }
    
    return '';
};

// Build CSS classes
$grid_columns_class = $get_grid_columns_class($columns_desktop, $columns_tablet, $columns_mobile);
$gap_class = $get_gap_class($gap_size);
$border_radius_class = $get_border_radius_class($card_border_radius);

$wrapper_classes = [
    'section-process-block',
    'py-12',
    'px-4',
    'sm:px-6',
    'lg:px-8'
];

// Add block attributes classes
if (!empty($attributes['className'])) {
    $wrapper_classes[] = $attributes['className'];
}

$wrapper_class = implode(' ', array_filter($wrapper_classes));

// Prepare inline styles for section
$section_styles = [];
if ($background_color) {
    $section_styles[] = 'background-color: ' . esc_attr($background_color);
}

$section_style_attr = !empty($section_styles) ? 'style="' . implode('; ', $section_styles) . '"' : '';

// Prepare inline styles for title
$title_styles = [];
if ($title_color) {
    $title_styles[] = 'color: ' . esc_attr($title_color);
}

$title_style_attr = !empty($title_styles) ? 'style="' . implode('; ', $title_styles) . '"' : '';

// Prepare inline styles for cards
$card_styles = [];
if ($card_background_color) {
    $card_styles[] = 'background-color: ' . esc_attr($card_background_color);
}

$card_style_attr = !empty($card_styles) ? 'style="' . implode('; ', $card_styles) . '"' : '';

// Prepare inline styles for card title
$card_title_styles = [];
if ($card_text_color) {
    $card_title_styles[] = 'color: ' . esc_attr($card_text_color);
}

$card_title_style_attr = !empty($card_title_styles) ? 'style="' . implode('; ', $card_title_styles) . '"' : '';

// Prepare inline styles for card description
$card_description_styles = [];
if ($card_description_color) {
    $card_description_styles[] = 'color: ' . esc_attr($card_description_color);
}

$card_description_style_attr = !empty($card_description_styles) ? 'style="' . implode('; ', $card_description_styles) . '"' : '';

// Hover effect class
$hover_class = $enable_hover_effect ? 'hover:shadow-lg' : '';

// Ensure we have steps to display
if (empty($process_steps)) {
    echo '<div class="max-w-7xl mx-auto py-12 px-4 text-center">';
    echo '<p class="text-gray-600">' . esc_html__('No process steps found. Please add some Process Steps in WordPress.', 'julianboelen') . '</p>';
    echo '</div>';
    return;
}
?>

<section class="<?php echo esc_attr($wrapper_class); ?>" 
         <?php echo $section_style_attr; ?>
         <?php if (!empty($attributes['anchor'])): ?>id="<?php echo esc_attr($attributes['anchor']); ?>"<?php endif; ?>
         role="region"
         aria-label="<?php echo esc_attr($section_title); ?>">
    
    <div class="max-w-7xl mx-auto">
        
        <!-- Section Title -->
        <?php if (!empty($section_title)): ?>
            <h2 class="text-4xl md:text-5xl font-bold mb-12" 
                <?php echo $title_style_attr; ?>>
                <?php echo wp_kses_post($section_title); ?>
            </h2>
        <?php endif; ?>
        
        <!-- Process Cards Grid -->
        <div class="grid <?php echo esc_attr($grid_columns_class); ?> <?php echo esc_attr($gap_class); ?>" 
             role="list"
             aria-label="<?php esc_attr_e('Process steps', 'julianboelen'); ?>">
            
            <?php foreach ($process_steps as $index => $step): 
                $step_id = $step['id'] ?? 'step-' . $index;
                $step_number = $step['stepNumber'] ?? str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $step_title = $step['title'] ?? '';
                $step_description = $step['description'] ?? '';
                $step_image_url = $step['imageUrl'] ?? '';
                $step_image_alt = $step['imageAlt'] ?? $step_title;
                $step_image_id = $step['imageId'] ?? null;
            ?>
            
            <div class="bg-white <?php echo esc_attr($border_radius_class); ?> shadow-md <?php echo esc_attr($hover_class); ?> transition-shadow duration-300 overflow-hidden" 
                 <?php echo $card_style_attr; ?>
                 role="listitem"
                 aria-labelledby="step-title-<?php echo esc_attr($step_id); ?>">
                
                <!-- Card Image -->
                <?php if (!empty($step_image_url)): ?>
                    <div class="aspect-[4/3] overflow-hidden">
                        <?php if ($step_image_id): 
                            echo wp_get_attachment_image(
                                $step_image_id,
                                'large',
                                false,
                                [
                                    'class' => 'w-full h-full object-cover',
                                    'alt' => esc_attr($step_image_alt),
                                    'loading' => $index > 1 ? 'lazy' : 'eager'
                                ]
                            );
                        else: ?>
                            <img src="<?php echo esc_url($step_image_url); ?>" 
                                 alt="<?php echo esc_attr($step_image_alt); ?>" 
                                 class="w-full h-full object-cover"
                                 loading="<?php echo $index > 1 ? 'lazy' : 'eager'; ?>"
                                 decoding="async">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Card Content -->
                <div class="p-6">
                    
                    <?php if ($step_number_style === 'badge' && $show_step_numbers): ?>
                        <?php echo $render_step_number($step_number, $step_number_style, $show_step_numbers); ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($step_title)): ?>
                        <h3 id="step-title-<?php echo esc_attr($step_id); ?>" 
                            class="text-xl font-bold mb-3" 
                            <?php echo $card_title_style_attr; ?>>
                            <?php if ($step_number_style === 'prefix' && $show_step_numbers): ?>
                                <?php echo $render_step_number($step_number, $step_number_style, $show_step_numbers); ?>
                            <?php endif; ?>
                            <?php echo wp_kses_post($step_title); ?>
                        </h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($step_description)): ?>
                        <div class="text-sm leading-relaxed" 
                           <?php echo $card_description_style_attr; ?>>
                            <?php echo wp_kses_post(wpautop($step_description)); ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
            
            <?php endforeach; ?>
            
        </div>
        
    </div>
    
</section>