<?php
/**
 * Section Wide Image Block Template
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
$image_url = $attributes['imageUrl'] ?? 'https://images.unsplash.com/photo-1575936123452-b67c3203c357?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwxfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1920';
$image_id = $attributes['imageId'] ?? 0;
$image_alt = $attributes['imageAlt'] ?? 'Collaborative workspace with laptops, tablets, notebooks and coffee on wooden table';
$image_width = $attributes['imageWidth'] ?? 1920;
$image_height = $attributes['imageHeight'] ?? 800;
$object_fit = $attributes['objectFit'] ?? 'cover';
$border_radius = $attributes['borderRadius'] ?? '2xl';
$enable_animation = $attributes['enableAnimation'] ?? true;
$animation_type = $attributes['animationType'] ?? 'fade-up';
$animation_duration = $attributes['animationDuration'] ?? 1200;
$animation_delay = $attributes['animationDelay'] ?? 0;
$animation_easing = $attributes['animationEasing'] ?? 'ease-in-out';
$aspect_ratio = $attributes['aspectRatio'] ?? 'auto';
$max_height = $attributes['maxHeight'] ?? 'none';
$overlay_enabled = $attributes['overlayEnabled'] ?? false;
$overlay_color = $attributes['overlayColor'] ?? 'rgba(0, 0, 0, 0.3)';
$overlay_opacity = $attributes['overlayOpacity'] ?? 30;
$link_url = $attributes['linkUrl'] ?? '';
$link_target = $attributes['linkTarget'] ?? '';
$link_rel = $attributes['linkRel'] ?? '';
$enable_lazy_load = $attributes['enableLazyLoad'] ?? true;
$container_padding = $attributes['containerPadding'] ?? 'none';

// Helper functions scoped to this template (avoid global namespace pollution)
$get_border_radius_class = function($radius) {
    $radius_map = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        '3xl' => 'rounded-3xl',
        'full' => 'rounded-full'
    ];
    return $radius_map[$radius] ?? 'rounded-2xl';
};

$get_object_fit_class = function($fit) {
    $fit_map = [
        'cover' => 'object-cover',
        'contain' => 'object-contain',
        'fill' => 'object-fill',
        'none' => 'object-none',
        'scale-down' => 'object-scale-down'
    ];
    return $fit_map[$fit] ?? 'object-cover';
};

$get_aspect_ratio_class = function($ratio) {
    if ($ratio === 'auto') return '';
    $ratio_map = [
        '16/9' => 'aspect-video',
        '4/3' => 'aspect-[4/3]',
        '3/2' => 'aspect-[3/2]',
        '21/9' => 'aspect-[21/9]',
        '1/1' => 'aspect-square'
    ];
    return $ratio_map[$ratio] ?? '';
};

$get_container_padding_class = function($padding) {
    $padding_map = [
        'none' => '',
        'sm' => 'px-4',
        'md' => 'px-6 md:px-8',
        'lg' => 'px-8 md:px-12',
        'xl' => 'px-12 md:px-16 lg:px-20'
    ];
    return $padding_map[$padding] ?? '';
};

// Calculate dynamic classes
$border_radius_class = $get_border_radius_class($border_radius);
$object_fit_class = $get_object_fit_class($object_fit);
$aspect_ratio_class = $get_aspect_ratio_class($aspect_ratio);
$container_padding_class = $get_container_padding_class($container_padding);

// Build CSS classes
$wrapper_classes = [
    'section-wide-image-block',
    'w-full',
    'overflow-hidden',
    $container_padding_class
];

// Add block attributes classes
if (!empty($attributes['className'])) {
    $wrapper_classes[] = $attributes['className'];
}

$wrapper_class = implode(' ', array_filter($wrapper_classes));

// Build image container classes
$image_container_classes = [
    'w-full',
    'relative'
];

if (!empty($aspect_ratio_class)) {
    $image_container_classes[] = $aspect_ratio_class;
}

$image_container_class = implode(' ', array_filter($image_container_classes));

// Build image classes
$image_classes = [
    'w-full',
    'h-auto',
    $object_fit_class,
    $border_radius_class
];

$image_class = implode(' ', array_filter($image_classes));

// Prepare inline styles
$image_styles = [];
if ($max_height !== 'none' && !empty($max_height)) {
    $image_styles[] = 'max-height: ' . esc_attr($max_height);
}

$image_style_attr = !empty($image_styles) ? 'style="' . implode('; ', $image_styles) . '"' : '';

// Prepare overlay styles
$overlay_styles = [];
if ($overlay_enabled) {
    $overlay_styles[] = 'position: absolute';
    $overlay_styles[] = 'top: 0';
    $overlay_styles[] = 'left: 0';
    $overlay_styles[] = 'right: 0';
    $overlay_styles[] = 'bottom: 0';
    $overlay_styles[] = 'background-color: ' . esc_attr($overlay_color);
    $overlay_styles[] = 'pointer-events: none';
}

$overlay_style_attr = !empty($overlay_styles) ? 'style="' . implode('; ', $overlay_styles) . '"' : '';

// Prepare animation attributes
$animation_attrs = [];
if ($enable_animation) {
    $animation_attrs[] = 'data-aos="' . esc_attr($animation_type) . '"';
    $animation_attrs[] = 'data-aos-duration="' . esc_attr($animation_duration) . '"';
    if ($animation_delay > 0) {
        $animation_attrs[] = 'data-aos-delay="' . esc_attr($animation_delay) . '"';
    }
    $animation_attrs[] = 'data-aos-easing="' . esc_attr($animation_easing) . '"';
}

$animation_attr_string = !empty($animation_attrs) ? implode(' ', $animation_attrs) : '';

// Get optimized image if using WordPress media library
$image_srcset = '';
$image_sizes = '';
if ($image_id > 0) {
    $image_srcset = wp_get_attachment_image_srcset($image_id, 'full');
    $image_sizes = wp_get_attachment_image_sizes($image_id, 'full');
    
    // Get optimized image URL
    $optimized_image = wp_get_attachment_image_url($image_id, 'full');
    if ($optimized_image) {
        $image_url = $optimized_image;
    }
}

// Prepare loading attribute
$loading_attr = $enable_lazy_load ? 'loading="lazy"' : 'loading="eager"';

// Prepare fetchpriority for above-the-fold images
$fetchpriority_attr = !$enable_lazy_load ? 'fetchpriority="high"' : '';

// Build the image element
$image_element = sprintf(
    '<img src="%s" alt="%s" class="%s" width="%d" height="%d" %s %s %s %s />',
    esc_url($image_url),
    esc_attr($image_alt),
    esc_attr($image_class),
    esc_attr($image_width),
    esc_attr($image_height),
    $image_style_attr,
    $loading_attr,
    $fetchpriority_attr,
    $image_srcset ? 'srcset="' . esc_attr($image_srcset) . '"' : ''
);

// Add sizes attribute if available
if ($image_sizes) {
    $image_element = str_replace('/>', 'sizes="' . esc_attr($image_sizes) . '" />', $image_element);
}

// Wrap image in link if URL is provided
if (!empty($link_url)) {
    $link_attrs = [];
    $link_attrs[] = 'href="' . esc_url($link_url) . '"';
    $link_attrs[] = 'class="block"';
    
    if (!empty($link_target)) {
        $link_attrs[] = 'target="' . esc_attr($link_target) . '"';
    }
    
    if (!empty($link_rel)) {
        $link_attrs[] = 'rel="' . esc_attr($link_rel) . '"';
    }
    
    $link_attr_string = implode(' ', $link_attrs);
    $image_element = sprintf(
        '<a %s aria-label="%s">%s</a>',
        $link_attr_string,
        esc_attr($image_alt),
        $image_element
    );
}
?>

<div class="<?php echo esc_attr($wrapper_class); ?>"
     <?php if (!empty($attributes['anchor'])): ?>id="<?php echo esc_attr($attributes['anchor']); ?>"<?php endif; ?>>
    
    <div class="<?php echo esc_attr($image_container_class); ?>" <?php echo $animation_attr_string; ?>>
        <div class="relative <?php echo esc_attr($aspect_ratio_class); ?>">
            <?php echo $image_element; ?>
            
            <?php if ($overlay_enabled): ?>
                <div class="<?php echo esc_attr($border_radius_class); ?>" <?php echo $overlay_style_attr; ?> aria-hidden="true"></div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<?php
// Enqueue AOS library if animation is enabled and not already enqueued
if ($enable_animation && !wp_script_is('aos', 'enqueued')) {
    // Add inline script to initialize AOS if not already initialized
    $aos_init_script = "
    <script>
        if (typeof AOS !== 'undefined' && !window.aosInitialized) {
            AOS.init({
                duration: 1200,
                once: true,
                offset: 100,
                easing: 'ease-in-out',
                disable: false,
                startEvent: 'DOMContentLoaded'
            });
            window.aosInitialized = true;
        }
    </script>
    ";
    
    // Output the script (will be moved to footer by WordPress)
    add_action('wp_footer', function() use ($aos_init_script) {
        echo $aos_init_script;
    }, 100);
}
?>