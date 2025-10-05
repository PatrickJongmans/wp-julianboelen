<?php
/**
 * Archive Process Steps Template
 *
 * Displays all process steps in sequential order
 *
 * @package JulianBoelen
 * @since 1.0.0
 */

get_header();

// Override the default query to order by step order
global $wp_query;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
    'post_type' => 'process_step',
    'posts_per_page' => -1, // Show all steps
    'orderby' => 'meta_value_num',
    'meta_key' => '_process_step_order',
    'order' => 'ASC',
    'post_status' => 'publish'
);

$process_steps_query = new WP_Query($args);
?>

<main id="main" class="site-main process-steps-archive" role="main">
    <div class="container">
        
        <!-- Archive Header -->
        <header class="page-header process-steps-header">
            <h1 class="page-title">
                <?php
                post_type_archive_title();
                ?>
            </h1>
            
            <?php
            $post_type_obj = get_post_type_object('process_step');
            if ($post_type_obj && !empty($post_type_obj->description)) :
            ?>
                <div class="archive-description">
                    <?php echo wp_kses_post($post_type_obj->description); ?>
                </div>
            <?php endif; ?>

            <?php if ($process_steps_query->found_posts > 0) : ?>
                <div class="steps-count">
                    <span class="count-number"><?php echo esc_html($process_steps_query->found_posts); ?></span>
                    <span class="count-label"><?php echo esc_html(_n('Step', 'Steps', $process_steps_query->found_posts, 'julianboelen')); ?></span>
                </div>
            <?php endif; ?>
        </header>

        <?php if ($process_steps_query->have_posts()) : ?>

            <!-- Process Steps Timeline -->
            <div class="process-steps-timeline">
                <?php
                $step_count = 0;
                while ($process_steps_query->have_posts()) :
                    $process_steps_query->the_post();
                    $step_count++;
                    $step_order = get_post_meta(get_the_ID(), '_process_step_order', true);
                    $step_order = $step_order ? intval($step_order) : $step_count;
                ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('process-step-item'); ?>>
                        
                        <div class="step-timeline-marker">
                            <div class="step-number-circle">
                                <span class="step-number"><?php echo esc_html($step_order); ?></span>
                            </div>
                            <?php if ($step_count < $process_steps_query->found_posts) : ?>
                                <div class="step-connector"></div>
                            <?php endif; ?>
                        </div>

                        <div class="step-content-wrapper">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="step-thumbnail">
                                    <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                        <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="step-content">
                                <header class="step-header">
                                    <h2 class="step-title">
                                        <a href="<?php the_permalink(); ?>" rel="bookmark">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                </header>

                                <div class="step-excerpt">
                                    <?php
                                    if (has_excerpt()) {
                                        the_excerpt();
                                    } else {
                                        echo wp_kses_post(wp_trim_words(get_the_content(), 30, '...'));
                                    }
                                    ?>
                                </div>

                                <footer class="step-footer">
                                    <a href="<?php the_permalink(); ?>" class="btn-read-more">
                                        <?php esc_html_e('View Step Details', 'julianboelen'); ?>
                                        <span class="arrow" aria-hidden="true">→</span>
                                    </a>
                                </footer>
                            </div>
                        </div>

                    </article>

                <?php
                endwhile;
                ?>
            </div>

            <?php
            // Reset post data
            wp_reset_postdata();
            ?>

        <?php else : ?>

            <div class="no-results">
                <h2><?php esc_html_e('No Process Steps Found', 'julianboelen'); ?></h2>
                <p><?php esc_html_e('There are currently no process steps available.', 'julianboelen'); ?></p>
            </div>

        <?php endif; ?>

    </div>
</main>

<style>
.process-steps-archive {
    padding: 60px 0;
}

.process-steps-header {
    text-align: center;
    margin-bottom: 60px;
    padding-bottom: 40px;
    border-bottom: 2px solid #e0e0e0;
}

.process-steps-header .page-title {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #333;
}

.archive-description {
    font-size: 1.25rem;
    color: #666;
    max-width: 800px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

.steps-count {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 25px;
    background: #f8f9fa;
    border-radius: 50px;
    font-size: 1rem;
}

.count-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0073aa;
}

.count-label {
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.875rem;
}

.process-steps-timeline {
    max-width: 1000px;
    margin: 0 auto;
    position: relative;
}

.process-step-item {
    display: flex;
    gap: 30px;
    margin-bottom: 60px;
    position: relative;
}

.process-step-item:last-child {
    margin-bottom: 0;
}

.step-timeline-marker {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.step-number-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #0073aa;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0, 115, 170, 0.3);
    z-index: 2;
}

.step-connector {
    width: 3px;
    flex-grow: 1;
    background: linear-gradient(to bottom, #0073aa, #e0e0e0);
    margin-top: 10px;
    min-height: 80px;
}

.step-content-wrapper {
    flex-grow: 1;
    display: flex;
    gap: 30px;
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.step-content-wrapper:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.step-thumbnail {
    flex-shrink: 0;
    width: 200px;
    border-radius: 8px;
    overflow: hidden;
}

.step-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.step-thumbnail:hover img {
    transform: scale(1.05);
}

.step-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.step-header {
    margin-bottom: 15px;
}

.step-title {
    font-size: 1.75rem;
    margin: 0;
    line-height: 1.3;
}

.step-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.step-title a:hover {
    color: #0073aa;
}

.step-excerpt {
    font-size: 1rem;
    line-height: 1.6;
    color: #666;
    margin-bottom: 20px;
    flex-grow: 1;
}

.step-footer {
    margin-top: auto;
}

.btn-read-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #0073aa;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-read-more:hover {
    background: #005a87;
    color: #fff;
    gap: 12px;
}

.btn-read-more .arrow {
    transition: transform 0.3s ease;
}

.btn-read-more:hover .arrow {
    transform: translateX(4px);
}

.no-results {
    text-align: center;
    padding: 80px 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.no-results h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #333;
}

.no-results p {
    font-size: 1.125rem;
    color: #666;
}

@media (max-width: 768px) {
    .process-steps-header .page-title {
        font-size: 2rem;
    }

    .archive-description {
        font-size: 1rem;
    }

    .process-step-item {
        gap: 20px;
        margin-bottom: 40px;
    }

    .step-number-circle {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }

    .step-content-wrapper {
        flex-direction: column;
        padding: 20px;
    }

    .step-thumbnail {
        width: 100%;
        height: 200px;
    }

    .step-title {
        font-size: 1.5rem;
    }

    .step-connector {
        min-height: 60px;
    }
}

@media (max-width: 480px) {
    .process-steps-archive {
        padding: 40px 0;
    }

    .process-steps-header {
        margin-bottom: 40px;
    }

    .step-timeline-marker {
        display: none;
    }

    .process-step-item {
        display: block;
    }

    .step-content-wrapper::before {
        content: attr(data-step);
        position: absolute;
        top: 20px;
        left: 20px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #0073aa;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.125rem;
    }
}
</style>

<script>
// Add step numbers as data attributes for mobile view
document.addEventListener('DOMContentLoaded', function() {
    const stepItems = document.querySelectorAll('.process-step-item');
    stepItems.forEach((item, index) => {
        const stepNumber = item.querySelector('.step-number-circle .step-number');
        if (stepNumber) {
            item.querySelector('.step-content-wrapper').setAttribute('data-step', stepNumber.textContent);
        }
    });
});
</script>

<?php
get_footer();