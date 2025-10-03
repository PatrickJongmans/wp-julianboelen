<?php
/**
 * Footer Template
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

    </main><!-- #content -->

    <footer id="colophon" class="site-footer bg-gray-900 text-white">
        <?php if (is_active_sidebar('footer-widgets')) : ?>
            <div class="footer-widgets py-12">
                <div class="container">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <?php dynamic_sidebar('footer-widgets'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-bottom border-t border-gray-800 py-6">
            <div class="container">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <!-- Copyright -->
                    <div class="footer-info">
                        <?php
                        $theme_options = isset($julianboelen_theme_options) ? $julianboelen_theme_options : null;
                        $company_name = $theme_options ? $theme_options->get_option('company_name') : '';
                        
                        if (empty($company_name)) {
                            $company_name = get_bloginfo('name');
                        }
                        ?>
                        <p class="text-sm text-gray-400">
                            &copy; <?php echo date('Y'); ?> <?php echo esc_html($company_name); ?>. 
                            <?php _e('All rights reserved.', 'julianboelen'); ?>
                        </p>
                    </div>

                    <!-- Social Links -->
                    <?php if ($theme_options && $theme_options->get_option('social_links')) : ?>
                        <div class="social-links">
                            <div class="flex space-x-4">
                                <?php
                                $social_links = $theme_options->get_option('social_links');
                                if (is_array($social_links)) :
                                    foreach ($social_links as $social_link) :
                                        if (!empty($social_link['url']) && !empty($social_link['platform'])) :
                                ?>
                                    <a href="<?php echo esc_url($social_link['url']); ?>" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="text-gray-400 hover:text-white transition-colors"
                                       aria-label="<?php echo esc_attr($social_link['platform']); ?>">
                                        <?php if (!empty($social_link['icon'])) : ?>
                                            <img src="<?php echo esc_url($social_link['icon']); ?>" 
                                                 alt="<?php echo esc_attr($social_link['platform']); ?>" 
                                                 class="w-5 h-5">
                                        <?php else : ?>
                                            <span class="text-sm"><?php echo esc_html($social_link['platform']); ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php
                                        endif;
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Footer Menu -->
                    <?php if (has_nav_menu('footer')) : ?>
                        <nav class="footer-navigation">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer',
                                'menu_id'        => 'footer-menu',
                                'container'      => false,
                                'menu_class'     => 'flex space-x-6 text-sm',
                                'depth'          => 1,
                                'fallback_cb'    => false,
                            ));
                            ?>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>