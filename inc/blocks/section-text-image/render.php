<?php
/**
 * Section Text Image Block Template
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
$small_heading = $attributes['smallHeading'] ?? 'Dit is Starapple:';
$main_heading = $attributes['mainHeading'] ?? 'De specialist achter de match';
$description = $attributes['description'] ?? 'StarApple is geen standaard bemiddelaar. Bij ons draait het om de perfecte interim match. Wij combineren inhoudelijke IT-expertise met een persoonlijke, gedreven aanpak. Of je nu zoekt naar de juiste interim opdracht of tijdelijke versterking, bij ons weet je precies wie je aan de lijn hebt en waar je aan toe bent. Leer ons kennen.';
$button_text = $attributes['buttonText'] ?? 'Over ons';
$button_url = $attributes['buttonUrl'] ?? '#';
$button_target = $attributes['buttonTarget'] ?? '';
$button_rel = $attributes['buttonRel'] ?? '';
$show_button = $attributes['showButton'] ?? true;
$button_bg_type = $attributes['buttonBackgroundType'] ?? 'primary';
$custom_button_color = $attributes['customButtonColor'] ?? '#9333ea';
$image_url = $attributes['imageUrl'] ?? 'https://images.unsplash.com/photo-1621155346337-1d19476ba7d6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHw0fHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=1200&h=800&fit=crop';
$image_alt = $attributes['imageAlt'] ?? 'Team collaboration';
$image_id = $attributes['imageId'] ?? 0;
$image_position = $attributes['imagePosition'] ?? 'right';
$background_color = $attributes['backgroundColor'] ?? '#f9fafb';
$text_color = $attributes['textColor'] ?? '#1f2937';
$small_heading_color = $attributes['smallHeadingColor'] ?? '#4b5563';
$description_color = $attributes['descriptionColor'] ?? '#374151';
$content_alignment = $attributes['contentAlignment'] ?? 'left';
$image_roundness = $attributes['imageRoundness'] ?? '3xl';
$column_gap = $attributes['columnGap'] ?? '12';
$vertical_padding = $attributes['verticalPadding'] ?? '12';
$show_shadow = $attributes['showShadow'] ?? true;

// Helper functions scoped to this template (avoid global namespace pollution)
$get_contrast_color = function($hex_color) {
    $hex = str_replace('#', '', (string) $hex_color);
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
    return $brightness > 128 ? '#1f2937' : '#ffffff';
};

$get_button_background_color = function($type, $custom_color) {
    switch($type) {
        case 'primary':
            return 'var(--wp--preset--color--primary, #9333ea)';
        case 'secondary':
            return 'var(--wp--preset--color--secondary, #84eb93)';
        case 'custom':
            return $custom_color;
        default:
            return '#9333ea';
    }
};

$get_button_text_color = function($bg_type, $custom_color) use (&$get_contrast_color) {
    if ($bg_type === 'secondary') {
        return '#1f2937';
    }
    if ($bg_type === 'custom') {
        return $get_contrast_color($custom_color);
    }
    return '#ffffff';
};

$get_roundness_class = function($roundness) {
    $roundness_map = [
        'none' => 'rounded-none',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        '3xl' => 'rounded-3xl',
        'full' => 'rounded-full'
    ];
    return $roundness_map[$roundness] ?? 'rounded-3xl';
};

$get_gap_class = function($gap) {
    return 'gap-' . $gap . ' lg:gap-' . $gap;
};

$get_padding_class = function($padding) {
    return 'py-' . $padding;
};

// Calculate dynamic styles
$button_bg_color = $get_button_background_color($button_bg_type, $custom_button_color);
$button_text_color = $get_button_text_color($button_bg_type, $custom_button_color);
$roundness_class = $get_roundness_class($image_roundness);
$gap_class = $get_gap_class($column_gap);
$padding_class = $get_padding_class($vertical_padding);

// Build CSS classes
$wrapper_classes = [
    'section-text-image-block',
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

// Prepare button styles
$button_styles = [];
if ($button_bg_color) {
    $button_styles[] = 'background-color: ' . esc_attr($button_bg_color);
}
if ($button_text_color) {
    $button_styles[] = 'color: ' . esc_attr($button_text_color);
}

$button_style_attr = !empty($button_styles) ? 'style="' . implode('; ', $button_styles) . '"' : '';

// Determine column order based on image position
$text_order_class = $image_position === 'left' ? 'order-2 lg:order-2' : 'order-2 lg:order-1';
$image_order_class = $image_position === 'left' ? 'order-1 lg:order-1' : 'order-1 lg:order-2';

// Get optimized image if WordPress image ID exists
$optimized_image_url = $image_url;
if ($image_id > 0) {
    $image_data = wp_get_attachment_image_src($image_id, 'full');
    if ($image_data) {
        $optimized_image_url = $image_data[0];
    }
}
?>

<section class="<?php echo esc_attr($wrapper_class); ?>" <?php echo $section_style_attr; ?>
         <?php if (!empty($attributes['anchor'])): ?>id="<?php echo esc_attr($attributes['anchor']); ?>"<?php endif; ?>>
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 <?php echo esc_attr($gap_class); ?> items-center">
      
      <!-- Text Column -->
      <div class="flex flex-col justify-center space-y-6 <?php echo esc_attr($text_order_class); ?>" 
           style="text-align: <?php echo esc_attr($content_alignment); ?>;">
        
        <?php if (!empty($small_heading)): ?>
          <p class="text-base sm:text-lg font-normal" 
             style="color: <?php echo esc_attr($small_heading_color); ?>;">
            <?php echo wp_kses_post($small_heading); ?>
          </p>
        <?php endif; ?>
        
        <?php if (!empty($main_heading)): ?>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight"
              style="color: <?php echo esc_attr($text_color); ?>;">
            <?php echo wp_kses_post($main_heading); ?>
          </h1>
        <?php endif; ?>
        
        <?php if (!empty($description)): ?>
          <p class="text-base sm:text-lg leading-relaxed"
             style="color: <?php echo esc_attr($description_color); ?>;">
            <?php echo wp_kses_post($description); ?>
          </p>
        <?php endif; ?>
        
        <?php if ($show_button && !empty($button_text) && !empty($button_url)): ?>
          <div class="pt-2">
            <a href="<?php echo esc_url($button_url); ?>"
               class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold px-10 py-4 rounded-full transition-colors duration-300 text-base sm:text-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
               <?php echo $button_style_attr; ?>
               <?php if (!empty($button_target)): ?>target="<?php echo esc_attr($button_target); ?>"<?php endif; ?>
               <?php if (!empty($button_rel)): ?>rel="<?php echo esc_attr($button_rel); ?>"<?php endif; ?>
               aria-label="<?php echo esc_attr($button_text); ?>">
              <?php echo esc_html($button_text); ?>
            </a>
          </div>
        <?php endif; ?>
        
      </div>
      
      <!-- Image Column -->
      <div class="flex justify-center lg:justify-end <?php echo esc_attr($image_order_class); ?>">
        <div class="w-full max-w-2xl">
          <?php if (!empty($optimized_image_url)): ?>
            <img src="<?php echo esc_url($optimized_image_url); ?>" 
                 alt="<?php echo esc_attr($image_alt); ?>" 
                 class="w-full h-auto object-cover <?php echo esc_attr($roundness_class); ?> <?php echo $show_shadow ? 'shadow-lg' : ''; ?>"
                 loading="lazy"
                 width="1200"
                 height="800" />
          <?php endif; ?>
        </div>
      </div>
      
    </div>
  </div>
</section>