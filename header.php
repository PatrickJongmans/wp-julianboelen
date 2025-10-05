```php
<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package ThemeForge
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'themeforge' ); ?></a>

<div id="page" class="site">

<header id="masthead" class="site-header w-full bg-white border-b border-gray-200">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 md:h-20">
      
      <!-- Logo -->
      <div class="site-branding flex items-center space-x-2">
        <?php
        if ( has_custom_logo() ) {
            the_custom_logo();
        } else {
            ?>
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                <rect x="3" y="3" width="8" height="8" rx="1"/>
                <rect x="13" y="3" width="8" height="8" rx="1"/>
                <rect x="3" y="13" width="8" height="8" rx="1"/>
                <rect x="13" y="13" width="8" height="8" rx="1"/>
              </svg>
            </div>
            <?php
        }
        ?>
        <span class="text-2xl font-semibold text-gray-900">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
        </span>
      </div>

      <!-- Desktop Navigation -->
      <nav id="site-navigation" class="main-navigation hidden lg:flex items-center space-x-8" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'themeforge' ); ?>">
        <?php
        if ( class_exists( 'TF_Primary_Nav_Walker' ) ) {
          wp_nav_menu(
            array(
              'theme_location' => 'primary',
              'menu_id'        => 'primary-menu',
              'container'      => false,
              'menu_class'     => 'nav-menu flex items-center space-x-8',
              'walker'         => new TF_Primary_Nav_Walker(),
              'fallback_cb'    => false,
            )
          );
        } else {
          wp_nav_menu(
            array(
              'theme_location' => 'primary',
              'menu_id'        => 'primary-menu',
              'container'      => false,
              'menu_class'     => 'nav-menu flex items-center space-x-8',
              'fallback_cb'    => false,
            )
          );
        }
        ?>
      </nav>

      <!-- CTA Button -->
      <div class="header-cta hidden lg:block">
        <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="inline-block px-8 py-3 bg-gradient-to-r from-green-400 to-green-500 text-gray-900 font-medium rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-sm hover:shadow-md">
          <?php esc_html_e( 'Contact', 'themeforge' ); ?>
        </a>
      </div>

      <!-- Mobile Menu Button -->
      <button id="mobile-menu-toggle" class="lg:hidden p-2 text-gray-700 hover:text-gray-900" aria-label="<?php esc_attr_e( 'Toggle menu', 'themeforge' ); ?>" aria-expanded="false" aria-controls="mobile-menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile Navigation -->
  <nav id="mobile-menu" class="mobile-navigation lg:hidden hidden bg-white border-t border-gray-200" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Menu', 'themeforge' ); ?>">
    <div class="container mx-auto px-4 py-4">
      <?php
      if ( class_exists( 'TF_Mobile_Nav_Walker' ) ) {
        wp_nav_menu(
          array(
            'theme_location' => 'primary',
            'menu_id'        => 'mobile-menu-list',
            'container'      => false,
            'menu_class'     => 'mobile-nav-menu space-y-2',
            'walker'         => new TF_Mobile_Nav_Walker(),
            'fallback_cb'    => false,
          )
        );
      } else {
        wp_nav_menu(
          array(
            'theme_location' => 'primary',
            'menu_id'        => 'mobile-menu-list',
            'container'      => false,
            'menu_class'     => 'mobile-nav-menu space-y-2',
            'fallback_cb'    => false,
          )
        );
      }
      ?>
      <div class="mt-4 pt-4 border-t border-gray-200">
        <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="block w-full text-center px-8 py-3 bg-gradient-to-r from-green-400 to-green-500 text-gray-900 font-medium rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-sm hover:shadow-md">
          <?php esc_html_e( 'Contact', 'themeforge' ); ?>
        </a>
      </div>
    </div>
  </nav>
</header>

<div id="content" class="site-content">
```