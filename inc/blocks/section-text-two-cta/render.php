<?php
/**
 * Section Text Two CTA Block Template
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
$about_title = $attributes['aboutTitle'] ?? 'Over ons';
$about_paragraph_1 = $attributes['aboutParagraph1'] ?? '';
$about_paragraph_2 = $attributes['aboutParagraph2'] ?? '';
$about_paragraph_3 = $attributes['aboutParagraph3'] ?? '';
$about_bg_color = $attributes['aboutBackgroundColor'] ?? '#f9fafb';
$about_text_color = $attributes['aboutTextColor'] ?? '#374151';

$card1_title = $attributes['card1Title'] ?? 'Voor IT-professionals';
$card1_description = $attributes['card1Description'] ?? '';
$card1_url = $attributes['card1Url'] ?? '#';
$card1_target = $attributes['card1Target'] ?? '';
$card1_rel = $attributes['card1Rel'] ?? '';
$card1_gradient_from = $attributes['card1GradientFrom'] ?? '#a855f7';
$card1_gradient_to = $attributes['card1GradientTo'] ?? '#9333ea';
$card1_text_color = $attributes['card1TextColor'] ?? '#ffffff';
$card1_button_color = $attributes['card1ButtonColor'] ?? '#9333ea';

$card2_title = $attributes['card2Title'] ?? 'Voor opdrachtgevers';
$card2_description = $attributes['card2Description'] ?? '';
$card2_url = $attributes['card2Url'] ?? '#';
$card2_target = $attributes['card2Target'] ?? '';
$card2_rel = $attributes['card2Rel'] ?? '';
$card2_gradient_from = $attributes['card2GradientFrom'] ?? '#4ade80';
$card2_gradient_to = $attributes['card2GradientTo'] ?? '#22c55e';
$card2_text_color = $attributes['card2TextColor'] ?? '#111827';
$card2_button_color = $attributes['card2ButtonColor'] ?? '#16a34a';

$container_max_width = $attributes['containerMaxWidth'] ?? '7xl';
$vertical_padding = $attributes['verticalPadding'] ?? 'default';
$card_gap = $attributes['cardGap'] ?? 'default';
$border_radius = $attributes['borderRadius'] ?? '3xl';

// Helper functions scoped to this template
$get_max_width_class = function($width) {
    $widths = [
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full'
    ];
    return $widths[$width] ?? 'max-w-7xl';
};

$get_padding_class = function($padding) {
    $paddings = [
        'small' => 'py-6 sm:py-8',
        'default' => 'py-8 sm:py-12',
        'large' => 'py-12 sm:py-16',
        'xlarge' => 'py-16 sm:py-20'
    ];
    return $paddings[$padding] ?? 'py-8 sm:py-12';
};

$get_gap_class = function($gap) {
    $gaps = [
        'small' => 'gap-3 lg:gap-4',
        'default' => 'gap-4 lg:gap-6',
        'large' => 'gap-6 lg:gap-8'
    ];
    return $gaps[$gap] ?? 'gap-4 lg:gap-6';
};

$get_border_radius_class = function($radius) {
    $radii = [
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        '3xl' => 'rounded-3xl'
    ];
    return $radii[$radius] ?? 'rounded-3xl';
};

// Calculate dynamic classes
$max_width_class = $get_max_width_class($container_max_width);
$padding_class = $get_padding_class($vertical_padding);
$gap_class = $get_gap_class($card_gap);
$border_radius_class = $get_border_radius_class($border_radius);

// Build CSS classes
$wrapper_classes = [
    'section-text-two-cta-block',
    'w-full',
    $max_width_class,
    'mx-auto',
    'px-4',
    $padding_class
];

// Add block attributes classes
if (!empty($attributes['className'])) {
    $wrapper_classes[] = $attributes['className'];
}

$wrapper_class = implode(' ', array_filter($wrapper_classes));

// Prepare inline styles for cards
$about_card_styles = [];
if ($about_bg_color) {
    $about_card_styles[] = 'background-color: ' . esc_attr($about_bg_color);
}
if ($about_text_color) {
    $about_card_styles[] = 'color: ' . esc_attr($about_text_color);
}
$about_card_style_attr = !empty($about_card_styles) ? 'style="' . implode('; ', $about_card_styles) . '"' : '';

$card1_styles = [];
if ($card1_gradient_from && $card1_gradient_to) {
    $card1_styles[] = 'background: linear-gradient(to bottom right, ' . esc_attr($card1_gradient_from) . ', ' . esc_attr($card1_gradient_to) . ')';
}
if ($card1_text_color) {
    $card1_styles[] = 'color: ' . esc_attr($card1_text_color);
}
$card1_style_attr = !empty($card1_styles) ? 'style="' . implode('; ', $card1_styles) . '"' : '';

$card2_styles = [];
if ($card2_gradient_from && $card2_gradient_to) {
    $card2_styles[] = 'background: linear-gradient(to bottom right, ' . esc_attr($card2_gradient_from) . ', ' . esc_attr($card2_gradient_to) . ')';
}
if ($card2_text_color) {
    $card2_styles[] = 'color: ' . esc_attr($card2_text_color);
}
$card2_style_attr = !empty($card2_styles) ? 'style="' . implode('; ', $card2_styles) . '"' : '';
?>

<div class="<?php echo esc_attr($wrapper_class); ?>"
     <?php if (!empty($attributes['anchor'])): ?>id="<?php echo esc_attr($attributes['anchor']); ?>"<?php endif; ?>>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 <?php echo esc_attr($gap_class); ?>">
        
        <!-- Left Column - About Us -->
        <div class="about-section <?php echo esc_attr($border_radius_class); ?> p-8 sm:p-10 lg:p-12 shadow-sm"
             <?php echo $about_card_style_attr; ?>>
            
            <?php if (!empty($about_title)): ?>
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                    <?php echo wp_kses_post($about_title); ?>
                </h2>
            <?php endif; ?>
            
            <div class="space-y-6 leading-relaxed">
                <?php if (!empty($about_paragraph_1)): ?>
                    <p><?php echo wp_kses_post($about_paragraph_1); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($about_paragraph_2)): ?>
                    <p><?php echo wp_kses_post($about_paragraph_2); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($about_paragraph_3)): ?>
                    <p><?php echo wp_kses_post($about_paragraph_3); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Column - Two Stacked Cards -->
        <div class="grid grid-cols-1 <?php echo esc_attr($gap_class); ?>">
            
            <!-- Card 1 - IT Professionals -->
            <div class="cta-card cta-card-1 <?php echo esc_attr($border_radius_class); ?> p-8 sm:p-10 lg:p-12 shadow-sm relative"
                 <?php echo $card1_style_attr; ?>>
                
                <?php if (!empty($card1_title)): ?>
                    <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                        <?php echo wp_kses_post($card1_title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if (!empty($card1_description)): ?>
                    <p class="text-base sm:text-lg leading-relaxed mb-4">
                        <?php echo wp_kses_post($card1_description); ?>
                    </p>
                <?php endif; ?>
                
                <?php if (!empty($card1_url)): ?>
                    <a href="<?php echo esc_url($card1_url); ?>"
                       class="cta-button absolute bottom-8 right-8 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2"
                       <?php if (!empty($card1_target)): ?>target="<?php echo esc_attr($card1_target); ?>"<?php endif; ?>
                       <?php if (!empty($card1_rel)): ?>rel="<?php echo esc_attr($card1_rel); ?>"<?php endif; ?>
                       aria-label="<?php echo esc_attr(strip_tags($card1_title)); ?>">
                        <svg class="w-6 h-6 transition-transform group-hover:translate-x-1" 
                             fill="none" 
                             stroke="<?php echo esc_attr($card1_button_color); ?>" 
                             viewBox="0 0 24 24"
                             stroke-width="2.5">
                            <path stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Card 2 - Opdrachtgevers -->
            <div class="cta-card cta-card-2 <?php echo esc_attr($border_radius_class); ?> p-8 sm:p-10 lg:p-12 shadow-sm relative"
                 <?php echo $card2_style_attr; ?>>
                
                <?php if (!empty($card2_title)): ?>
                    <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                        <?php echo wp_kses_post($card2_title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if (!empty($card2_description)): ?>
                    <p class="text-base sm:text-lg leading-relaxed mb-4">
                        <?php echo wp_kses_post($card2_description); ?>
                    </p>
                <?php endif; ?>
                
                <?php if (!empty($card2_url)): ?>
                    <a href="<?php echo esc_url($card2_url); ?>"
                       class="cta-button absolute bottom-8 right-8 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2"
                       <?php if (!empty($card2_target)): ?>target="<?php echo esc_attr($card2_target); ?>"<?php endif; ?>
                       <?php if (!empty($card2_rel)): ?>rel="<?php echo esc_attr($card2_rel); ?>"<?php endif; ?>
                       aria-label="<?php echo esc_attr(strip_tags($card2_title)); ?>">
                        <svg class="w-6 h-6 transition-transform group-hover:translate-x-1" 
                             fill="none" 
                             stroke="<?php echo esc_attr($card2_button_color); ?>" 
                             viewBox="0 0 24 24"
                             stroke-width="2.5">
                            <path stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            
        </div>
        
    </div>
    
</div>