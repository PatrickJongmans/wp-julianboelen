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
$card_background_color = $attributes['cardBackgroundColor'] ?? '#ffffff';
$card_text_color = $attributes['cardTextColor'] ?? '#374151';
$card_title_color = $attributes['cardTitleColor'] ?? '#111827';
$enable_hover_effect = $attributes['enableHoverEffect'] ?? true;
$columns_desktop = $attributes['columnsDesktop'] ?? 4;
$columns_tablet = $attributes['columnsTablet'] ?? 2;
$columns_mobile = $attributes['columnsMobile'] ?? 1;
$card_gap = $attributes['cardGap'] ?? '6';
$process_steps = $attributes['processSteps'] ?? [];
$padding_top = $attributes['paddingTop'] ?? '12';
$padding_bottom = $attributes['paddingBottom'] ?? '12';
$title_margin_bottom = $attributes['titleMarginBottom'] ?? '8';
$card_border_radius = $attributes['cardBorderRadius'] ?? 'lg';
$image_border_radius = $attributes['imageBorderRadius'] ?? 'none';
$shadow_style = $attributes['shadowStyle'] ?? 'md';
$hover_shadow_style = $attributes['hoverShadowStyle'] ?? 'lg';

// Helper functions scoped to this template
$get_grid_columns_class = function($mobile, $tablet, $desktop) {
    $mobile_class = 'grid-cols-' . absint($mobile);
    $tablet_class = 'sm:grid-cols-' . absint($tablet);
    $desktop_class = 'lg:grid-cols-' . absint($desktop);
    return "{$mobile_class} {$tablet_class} {$desktop_class}";
};

$get_gap_class = function($gap) {
    return 'gap-' . esc_attr($gap);
};

$get_padding_class = function($top, $bottom) {
    return 'py-' . esc_attr($top) . ' pb-' . esc_attr($bottom);
};

$get_title_margin_class = function($margin) {
    return 'mb-' . esc_attr($margin);
};

$get_border_radius_class = function($type) {
    if ($type === 'none') return '';
    return 'rounded-' . esc_attr($type);
};

$get_shadow_class = function($shadow) {
    if ($shadow === 'none') return '';
    return 'shadow-' . esc_attr($shadow);
};

$get_hover_shadow_class = function($enable, $shadow) {
    if (!$enable || $shadow === 'none') return '';
    return 'hover:shadow-' . esc_attr($shadow);
};

// Calculate dynamic classes
$grid_columns_class = $get_grid_columns_class($columns_mobile, $columns_tablet, $columns_desktop);
$gap_class = $get_gap_class($card_gap);
$padding_class = $get_padding_class($padding_top, $padding_bottom);
$title_margin_class = $get_title_margin_class($title_margin_bottom);
$card_border_radius_class = $get_border_radius_class($card_border_radius);
$image_border_radius_class = $get_border_radius_class($image_border_radius);
$shadow_class = $get_shadow_class($shadow_style);
$hover_shadow_class = $get_hover_shadow_class($enable_hover_effect, $hover_shadow_style);

// Build CSS classes
$wrapper_classes = [
    'section-process-block',
    'w-full',
    $padding_class,
    'px-4',
    'sm:px-6',
    'lg:px-8'
];

// Add block attributes classes
if (!empty($attributes['className'])) {
    $wrapper_classes[] = $attributes['className'];
}

$wrapper_class = implode(' ', array_filter($wrapper_classes));

// Prepare inline styles
$section_styles = [];
if ($background_color) {
    $section_styles[] = 'background-color: ' . esc_attr($background_color);
}

$section_style_attr = !empty($section_styles) ? 'style="' . implode('; ', $section_styles) . '"' : '';

// Prepare title styles
$title_styles = [];
if ($title_color) {
    $title_styles[] = 'color: ' . esc_attr($title_color);
}

$title_style_attr = !empty($title_styles) ? 'style="' . implode('; ', $title_styles) . '"' : '';

// Prepare card styles
$card_styles = [];
if ($card_background_color) {
    $card_styles[] = 'background-color: ' . esc_attr($card_background_color);
}

$card_style_attr = !empty($card_styles) ? 'style="' . implode('; ', $card_styles) . '"' : '';

// Prepare card title styles
$card_title_styles = [];
if ($card_title_color) {
    $card_title_styles[] = 'color: ' . esc_attr($card_title_color);
}

$card_title_style_attr = !empty($card_title_styles) ? 'style="' . implode('; ', $card_title_styles) . '"' : '';

// Prepare card text styles
$card_text_styles = [];
if ($card_text_color) {
    $card_text_styles[] = 'color: ' . esc_attr($card_text_color);
}

$card_text_style_attr = !empty($card_text_styles) ? 'style="' . implode('; ', $card_text_styles) . '"' : '';

// Validate process steps
if (empty($process_steps) || !is_array($process_steps)) {
    $process_steps = [];
}
?>

<section class="<?php echo esc_attr($wrapper_class); ?>" <?php echo $section_style_attr; ?>
         <?php if (!empty($attributes['anchor'])): ?>id="<?php echo esc_attr($attributes['anchor']); ?>"<?php endif; ?>
         role="region"
         aria-label="<?php echo esc_attr($section_title); ?>">
    
    <div class="max-w-7xl mx-auto">
        
        <?php if (!empty($section_title)): ?>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold <?php echo esc_attr($title_margin_class); ?>" 
                <?php echo $title_style_attr; ?>>
                <?php echo wp_kses_post($section_title); ?>
            </h2>
        <?php endif; ?>
        
        <?php if (!empty($process_steps)): ?>
            <div class="grid <?php echo esc_attr($grid_columns_class); ?> <?php echo esc_attr($gap_class); ?>">
                
                <?php foreach ($process_steps as $index => $step): 
                    // Validate step data
                    $step_number = $step['stepNumber'] ?? '';
                    $step_title = $step['title'] ?? '';
                    $step_description = $step['description'] ?? '';
                    $step_image_url = $step['imageUrl'] ?? '';
                    $step_image_alt = $step['imageAlt'] ?? $step_title;
                    $step_image_id = $step['imageId'] ?? null;
                    
                    // Skip if essential data is missing
                    if (empty($step_title)) {
                        continue;
                    }
                    
                    // Get optimized image if ID is available
                    if ($step_image_id) {
                        $image_data = wp_get_attachment_image_src($step_image_id, 'large');
                        if ($image_data) {
                            $step_image_url = $image_data[0];
                        }
                    }
                ?>
                
                <article class="process-step-card <?php echo esc_attr($card_border_radius_class); ?> <?php echo esc_attr($shadow_class); ?> <?php echo esc_attr($hover_shadow_class); ?> overflow-hidden transition-shadow duration-300" 
                         <?php echo $card_style_attr; ?>
                         itemscope 
                         itemtype="https://schema.org/HowToStep"
                         aria-labelledby="step-title-<?php echo esc_attr($index); ?>">
                    
                    <?php if (!empty($step_image_url)): ?>
                        <div class="aspect-[4/3] overflow-hidden <?php echo esc_attr($image_border_radius_class); ?>">
                            <img src="<?php echo esc_url($step_image_url); ?>" 
                                 alt="<?php echo esc_attr($step_image_alt); ?>" 
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 itemprop="image"
                                 width="800"
                                 height="600">
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h3 id="step-title-<?php echo esc_attr($index); ?>" 
                            class="text-xl font-bold mb-3" 
                            <?php echo $card_title_style_attr; ?>
                            itemprop="name">
                            <?php 
                            if (!empty($step_number)) {
                                echo esc_html($step_number) . '. ';
                            }
                            echo wp_kses_post($step_title); 
                            ?>
                        </h3>
                        
                        <?php if (!empty($step_description)): ?>
                            <p class="leading-relaxed" 
                               <?php echo $card_text_style_attr; ?>
                               itemprop="text">
                                <?php echo wp_kses_post($step_description); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                </article>
                
                <?php endforeach; ?>
                
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-500" <?php echo $card_text_style_attr; ?>>
                    <?php esc_html_e('No process steps have been added yet.', 'julianboelen'); ?>
                </p>
            </div>
        <?php endif; ?>
        
    </div>
    
</section>