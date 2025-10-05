<?php
/**
 * Single Process Step Template
 *
 * Displays individual process step with order number and navigation
 *
 * @package JulianBoelen
 * @since 1.0.0
 */

get_header();

// Get the process step order
$step_order = get_post_meta(get_the_ID(), '_process_step_order', true);
$step_order = $step_order ? intval($step_order) : 0;

// Get adjacent steps for navigation
$args = array(
    'post_type' => 'process_step',
    'posts_per_page' => -1,
    'orderby' => 'meta_value_num',
    'meta_key' => '_process_step_order',
    'order' => 'ASC',
    'post_status' => 'publish'
);
$all_steps = get_posts($args);

$current_index = 0;
$prev_step = null;
$next_step = null;

foreach ($all_steps as $index => $step) {
    if ($step->ID === get_the_ID()) {
        $current_index = $index;
        $prev_step = isset($all_steps[$index - 1]) ? $all_steps[$index - 1] : null;
        $next_step = isset($all_steps[$index + 1]) ? $all_steps[$index + 1] : null;
        break;
    }
}

$total_steps = count($all_steps);
?>

<main id="main" class="site-main process-step-single" role="main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('process-step-article'); ?>>
                
                <!-- Breadcrumb / Progress Indicator -->
                <div class="process-step-progress">
                    <div class="step-indicator">
                        <span class="step-number">Step <?php echo esc_html($step_order); ?></span>
                        <?php if ($total_steps > 0) : ?>
                            <span class="step-total">of <?php echo esc_html($total_steps); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($total_steps > 0) : ?>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo esc_attr(($current_index + 1) / $total_steps * 100); ?>%;"></div>
                        </div>
                    <?php endif; ?>
                </div>

                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="process-step-featured-image">
                        <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'julianboelen'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="entry-footer">
                    <!-- Step Navigation -->
                    <nav class="process-step-navigation" aria-label="<?php esc_attr_e('Process Step Navigation', 'julianboelen'); ?>">
                        <div class="nav-links">
                            <?php if ($prev_step) : ?>
                                <div class="nav-previous">
                                    <a href="<?php echo esc_url(get_permalink($prev_step->ID)); ?>" rel="prev">
                                        <span class="nav-subtitle"><?php esc_html_e('Previous Step', 'julianboelen'); ?></span>
                                        <span class="nav-title">
                                            <span class="nav-step-number"><?php echo esc_html(get_post_meta($prev_step->ID, '_process_step_order', true)); ?>.</span>
                                            <?php echo esc_html($prev_step->post_title); ?>
                                        </span>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="nav-all-steps">
                                <a href="<?php echo esc_url(get_post_type_archive_link('process_step')); ?>" class="btn-view-all">
                                    <?php esc_html_e('View All Steps', 'julianboelen'); ?>
                                </a>
                            </div>

                            <?php if ($next_step) : ?>
                                <div class="nav-next">
                                    <a href="<?php echo esc_url(get_permalink($next_step->ID)); ?>" rel="next">
                                        <span class="nav-subtitle"><?php esc_html_e('Next Step', 'julianboelen'); ?></span>
                                        <span class="nav-title">
                                            <span class="nav-step-number"><?php echo esc_html(get_post_meta($next_step->ID, '_process_step_order', true)); ?>.</span>
                                            <?php echo esc_html($next_step->post_title); ?>
                                        </span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </nav>
                </footer>

            </article>

            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php
        endwhile;
        ?>
    </div>
</main>

<style>
.process-step-single {
    padding: 60px 0;
}

.process-step-progress {
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.step-indicator {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #666;
}

.step-number {
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.progress-bar {
    height: 8px;
    background-color: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background-color: #0073aa;
    transition: width 0.3s ease;
}

.process-step-article .entry-header {
    margin-bottom: 30px;
}

.process-step-article .entry-title {
    font-size: 2.5rem;
    line-height: 1.2;
    margin: 0;
}

.process-step-featured-image {
    margin-bottom: 40px;
    border-radius: 8px;
    overflow: hidden;
}

.process-step-featured-image img {
    width: 100%;
    height: auto;
    display: block;
}

.process-step-article .entry-content {
    font-size: 1.125rem;
    line-height: 1.8;
    margin-bottom: 50px;
}

.process-step-navigation {
    margin-top: 60px;
    padding-top: 40px;
    border-top: 2px solid #e0e0e0;
}

.process-step-navigation .nav-links {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 20px;
    align-items: center;
}

.nav-previous,
.nav-next {
    display: flex;
}

.nav-previous a,
.nav-next a {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    width: 100%;
}

.nav-previous a:hover,
.nav-next a:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.nav-next {
    justify-content: flex-end;
    text-align: right;
}

.nav-subtitle {
    font-size: 0.875rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #333;
}

.nav-step-number {
    color: #0073aa;
    margin-right: 5px;
}

.nav-all-steps {
    display: flex;
    justify-content: center;
}

.btn-view-all {
    display: inline-block;
    padding: 12px 30px;
    background: #0073aa;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: background 0.3s ease;
}

.btn-view-all:hover {
    background: #005a87;
    color: #fff;
}

@media (max-width: 768px) {
    .process-step-navigation .nav-links {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .nav-next {
        text-align: left;
    }

    .nav-all-steps {
        order: -1;
    }

    .process-step-article .entry-title {
        font-size: 2rem;
    }
}
</style>

<?php
get_footer();