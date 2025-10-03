<?php
/**
 * Header Template
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'julianboelen'); ?></a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="flex items-center justify-between py-4">
                <!-- Site Branding -->
                <div class="site-branding">
                    <?php
                    $theme_options = isset($julianboelen_theme_options) ? $julianboelen_theme_options : null;
                    
                    if ($theme_options && $theme_options->get_option('header_logo')) {
                        $logo_url = $theme_options->get_option('header_logo');
                        $site_name = get_bloginfo('name');
                        ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="custom-logo-link">
                            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" class="custom-logo h-12 w-auto">
                        </a>
                        <?php
                    } elseif (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title text-2xl font-bold">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-primary hover:text-primary-700 transition-colors">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) {
                            ?>
                            <p class="site-description text-gray-600 text-sm mt-1"><?php echo $description; ?></p>
                            <?php
                        }
                    }
                    ?>
                </div>

                <!-- Primary Navigation -->
                <nav id="site-navigation" class="main-navigation hidden lg:block">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'menu_class'     => 'flex space-x-6 list-none m-0 p-0',
                        'link_before'    => '<span class="menu-text">',
                        'link_after'     => '</span>',
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle lg:hidden p-2 text-gray-700 hover:text-primary transition-colors" type="button" aria-label="<?php _e('Toggle navigation', 'julianboelen'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="mobile-navigation lg:hidden hidden">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'mobile-menu',
                    'container'      => false,
                    'menu_class'     => 'mobile-menu py-4 space-y-2',
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>
        </div>
    </header>

    <main id="content" class="site-main">