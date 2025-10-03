<?php
/**
 * The main template file
 *
 * @package JulianboelenTheme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="content-area py-12">
    <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="main-content lg:col-span-2">
                <?php if (have_posts()) : ?>
                    
                    <?php if (is_home() && !is_front_page()) : ?>
                        <header class="page-header mb-8">
                            <h1 class="page-title text-3xl font-bold text-gray-900">
                                <?php single_post_title(); ?>
                            </h1>
                        </header>
                    <?php endif; ?>

                    <div class="posts-container space-y-8">
                        <?php
                        // Start the Loop
                        while (have_posts()) :
                            the_post();
                        ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-item card'); ?>>
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>" class="block">
                                            <?php the_post_thumbnail('large', array('class' => 'w-full h-64 object-cover')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <header class="entry-header mb-4">
                                        <?php
                                        if (is_singular()) :
                                            the_title('<h1 class="entry-title text-2xl font-bold text-gray-900">', '</h1>');
                                        else :
                                            the_title('<h2 class="entry-title text-xl font-bold"><a href="' . esc_url(get_permalink()) . '" rel="bookmark" class="text-gray-900 hover:text-primary transition-colors">', '</a></h2>');
                                        endif;
                                        ?>

                                        <div class="entry-meta text-sm text-gray-600 mt-2">
                                            <time class="published" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo esc_html(get_the_date()); ?>
                                            </time>
                                            
                                            <span class="byline mx-2">•</span>
                                            
                                            <span class="author vcard">
                                                <?php _e('By', 'julianboelen'); ?> 
                                                <a class="url fn n text-primary hover:text-primary-700" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                    <?php echo esc_html(get_the_author()); ?>
                                                </a>
                                            </span>

                                            <?php if (has_category()) : ?>
                                                <span class="cat-links mx-2">•</span>
                                                <span class="categories">
                                                    <?php the_category(', '); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </header>

                                    <div class="entry-content text-gray-700">
                                        <?php
                                        if (is_singular()) {
                                            the_content();
                                        } else {
                                            the_excerpt();
                                        }
                                        ?>
                                    </div>

                                    <?php if (!is_singular()) : ?>
                                        <footer class="entry-footer mt-4">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                                <?php _e('Read More', 'julianboelen'); ?>
                                            </a>
                                        </footer>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php
                        endwhile;
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper mt-12">
                        <?php
                        the_posts_pagination(array(
                            'mid_size'  => 2,
                            'prev_text' => __('Previous', 'julianboelen'),
                            'next_text' => __('Next', 'julianboelen'),
                            'class'     => 'pagination flex justify-center space-x-2'
                        ));
                        ?>
                    </div>

                <?php else : ?>
                    <!-- No posts found -->
                    <section class="no-results not-found">
                        <header class="page-header">
                            <h1 class="page-title text-2xl font-bold text-gray-900 mb-4">
                                <?php _e('Nothing here', 'julianboelen'); ?>
                            </h1>
                        </header>

                        <div class="page-content text-gray-700">
                            <?php if (is_home() && current_user_can('publish_posts')) : ?>
                                <p>
                                    <?php
                                    printf(
                                        wp_kses(
                                            __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'julianboelen'),
                                            array(
                                                'a' => array(
                                                    'href' => array(),
                                                ),
                                            )
                                        ),
                                        esc_url(admin_url('post-new.php'))
                                    );
                                    ?>
                                </p>
                            <?php elseif (is_search()) : ?>
                                <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'julianboelen'); ?></p>
                                <?php get_search_form(); ?>
                            <?php else : ?>
                                <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'julianboelen'); ?></p>
                                <?php get_search_form(); ?>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside id="secondary" class="widget-area sidebar">
                <?php if (is_active_sidebar('sidebar-1')) : ?>
                    <?php dynamic_sidebar('sidebar-1'); ?>
                <?php else : ?>
                    <div class="default-sidebar card card-body">
                        <h3 class="widget-title text-lg font-semibold mb-4"><?php _e('About', 'julianboelen'); ?></h3>
                        <p class="text-gray-700">
                            <?php _e('This is the sidebar area. Add some widgets here to make it more useful.', 'julianboelen'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>

<?php
get_footer();
?>