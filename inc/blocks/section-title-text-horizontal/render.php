<?php
/**
 * Section Title Text Horizontal Block Template
 * 
 * A sophisticated two-column layout with heading on the left and body text on the right
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
$heading = $attributes['heading'] ?? 'Wij zijn Starapple en wij willen talent laten groeien!';
$paragraph1 = $attributes['paragraph1'] ?? 'Een brug slaan tussen aanbod van en vraag naar uiterst specifieke IT-specialisten, dat is Starapple in één zin.';
$paragraph2 = $attributes['paragraph2'] ?? 'Wij helpen je graag in jou zoektocht naar een nieuwe uitdaging zodat jij jouw carrière verder kunt ontwikkelen.';
$background_color = $attributes['backgroundColor'] ?? '#ffffff';
$heading_color = $attributes['headingColor'] ?? '#111827';
$text_color = $attributes['textColor'] ?? '#111827';
$heading_size = $attributes['headingSize'] ?? 'large';
$text_size = $attributes['textSize'] ?? 'base';
$column_ratio = $attributes['columnRatio'] ?? '5-7';
$vertical_alignment = $attributes['verticalAlignment'] ?? 'start';
$column_gap = $attributes['columnGap'] ?? 'large';
$padding_top = $attributes['paddingTop'] ?? '16';
$padding_bottom = $attributes['paddingBottom'] ?? '16';
$max_width = $attributes['maxWidth'] ?? '7xl';
$show_paragraph2 = $attributes['showParagraph2'] ?? true;
$paragraph_spacing = $attributes['paragraphSpacing'] ?? '6';

// Helper functions scoped to this template
$get_heading_size_class = function($size) {
    switch($size) {
        case 'medium':
            return 'text-3xl sm:text-4xl lg:text-5xl';
        case 'large':
            return 'text-4xl sm:text-5xl lg:text-6xl';
        case 'xlarge':
            return 'text-5xl sm:text-6xl lg:text-7xl';
        default:
            return 'text-4xl sm:text-5xl lg:text-6xl';
    }
};

$get_text_size_class = function($size) {
    switch($size) {
        case 'small':
            return 'text-sm sm:text-base';
        case 'base':
            return 'text-base sm:text-lg';
        case 'large':
            return 'text-lg sm:text-xl';
        default:
            return 'text-base sm:text-lg';
    }
};

$get_column_ratio_classes = function($ratio) {
    switch($ratio) {
        case '4-8':
            return ['left' => 'lg:col-span-4', 'right' => 'lg:col-span-8'];
        case '5-7':
            return ['left' => 'lg:col-span-5', 'right' => 'lg:col-span-7'];
        case '6-6':
            return ['left' => 'lg:col-span-6', 'right' => 'lg:col-span-6'];
        default:
            return ['left' => 'lg:col-span-5', 'right' => 'lg:col-span-7'];
    }
};

$get_alignment_class = function($alignment) {
    switch($alignment) {
        case 'start':
            return 'items-start';
        case 'center':
            return 'items-center';
        case 'end':
            return 'items-end';
        default:
            return 'items-start';
    }
};

$get_gap_class = function($gap) {
    switch($gap) {
        case 'small':
            return 'gap-4 lg:gap-6';
        case 'medium':
            return 'gap-6 lg:gap-8';
        case 'large':
            return 'gap-8 lg:gap-12';
        case 'xlarge':
            return 'gap-10 lg:gap-16';
        default:
            return 'gap-8 lg:gap-12';
    }
};

$get_max_width_class = function($width) {
    switch($width) {
        case '5xl':
            return 'max-w-5xl';
        case '6xl':
            return 'max-w-6xl';
        case '7xl':
            return 'max-w-7xl';
        case 'full':
            return 'max-w-full';
        default:
            return 'max-w-7xl';
    }
};

// Calculate dynamic classes
$column_classes = $get_column_ratio_classes($column_ratio);
$heading_size_class = $get_heading_size_class($heading_size);
$text_size_class = $get_text_size_class($text_size);
$alignment_class = $get_alignment_class($vertical_alignment);
$gap_class = $get_gap_class($column_gap);
$max_width_class = $get_max_width_class($max_width);

// Build CSS classes
$section_classes = [
    'section-title-text-horizontal-block',
    'w-full',
    'px-4',
    'sm:px-6',
    'lg:px-8'
];

// Add block attributes classes
if (!empty($attributes['className'])) {
    $section_classes[] = $attributes['className'];
}

$section_class = implode(' ', array_filter($section_classes));

// Prepare inline styles
$section_styles = [];
if ($background_color) {
    $section_styles[] = 'background-color: ' . esc_attr($background_color);
}
if ($padding_top) {
    $section_styles[] = 'padding-top: ' . esc_attr($padding_top) . 'px';
}
if ($padding_bottom) {
    $section_styles[] = 'padding-bottom: ' . esc_attr($padding_bottom) . 'px';
}

$section_style_attr = !empty($section_styles) ? 'style="' . implode('; ', $section_styles) . '"' : '';

// Calculate paragraph spacing
$paragraph_spacing_px = intval($paragraph_spacing) * 4; // Convert to pixels (Tailwind scale)
?>

<section class="<?php echo esc_attr($section_class); ?>" <?php echo $section_style_attr; ?>
         <?php if (!empty($attributes['anchor'])): ?>id="<?php echo esc_attr($attributes['anchor']); ?>"<?php endif; ?>
         role="region"
         aria-label="<?php echo esc_attr(wp_strip_all_tags($heading)); ?>">
    
    <div class="<?php echo esc_attr($max_width_class); ?> mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 <?php echo esc_attr($gap_class . ' ' . $alignment_class); ?>">
            
            <!-- Left Column: Heading -->
            <div class="col-span-1 <?php echo esc_attr($column_classes['left']); ?>">
                <?php if (!empty($heading)): ?>
                    <h1 class="<?php echo esc_attr($heading_size_class); ?> font-bold leading-tight"
                        style="color: <?php echo esc_attr($heading_color); ?>;">
                        <?php echo wp_kses_post($heading); ?>
                    </h1>
                <?php endif; ?>
            </div>
            
            <!-- Right Column: Body Text -->
            <div class="col-span-1 <?php echo esc_attr($column_classes['right']); ?>" 
                 style="display: flex; flex-direction: column; gap: <?php echo esc_attr($paragraph_spacing_px); ?>px;">
                
                <?php if (!empty($paragraph1)): ?>
                    <p class="<?php echo esc_attr($text_size_class); ?> leading-relaxed"
                       style="color: <?php echo esc_attr($text_color); ?>;">
                        <?php echo wp_kses_post($paragraph1); ?>
                    </p>
                <?php endif; ?>
                
                <?php if ($show_paragraph2 && !empty($paragraph2)): ?>
                    <p class="<?php echo esc_attr($text_size_class); ?> leading-relaxed"
                       style="color: <?php echo esc_attr($text_color); ?>;">
                        <?php echo wp_kses_post($paragraph2); ?>
                    </p>
                <?php endif; ?>
                
            </div>
            
        </div>
    </div>
    
</section>