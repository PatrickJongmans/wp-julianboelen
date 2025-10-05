<?php
/**
 * Template part for displaying process step content
 *
 * Used in single-process_step.php and archive-process_step.php
 *
 * @package JulianBoelen
 * @since 1.0.0
 */

$step_order = get_post_meta(get_the_ID(), '_process_step_order', true);
$step_order = $step_order ? intval($step_order) : 0;
$is_single = is_singular('process_step');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('process-step-content'); ?>>
    
    <?php if (!$is_single && $step_order) : ?>
        <div class="step-badge">
            <span class="badge-label"><?php esc_html_e('Step', 'julianboelen'); ?></span>
            <span class="badge-number"><?php echo esc_html($step_order); ?></span>
        </div>
    <?php endif; ?>

    <?php if (has_post_thumbnail() && !$is_single) : ?>
        <div class="entry-thumbnail">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail('medium_large', array(
                    'alt' => the_title_attribute(array('echo' => false)),
                    'class' => 'img-fluid'
                ));
                ?>
            </a>
        </div>
    <?php endif; ?>

    <header class="entry-header">
        <?php
        if ($is_single) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title(
                sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())),
                '</a></h2>'
            );
        endif;
        ?>

        <?php if (!$is_single) : ?>
            <div class="entry-meta">
                <?php
                // Display post date
                printf(
                    '<span class="posted-on"><time class="entry-date published" datetime="%1$s">%2$s</time></span>',
                    esc_attr(get_the_date('c')),
                    esc_html(get_the_date())
                );

                // Display author if needed
                if (get_theme_mod('show_author', false)) {
                    printf(
                        '<span class="byline"> %s <span class="author vcard"><a class="url fn n" href="%s">%s</a></span></span>',
                        esc_html__('by', 'julianboelen'),
                        esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                        esc_html(get_the_author())
                    );
                }
                ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="entry-content">
        <?php
        if ($is_single) :
            the_content(sprintf(
                wp_kses(
                    __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'julianboelen'),
                    array('span' => array('class' => array()))
                ),
                get_the_title()
            ));

            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'julianboelen'),
                'after'  => '</div>',
            ));
        else :
            // Show excerpt for archive pages
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo '<p>' . wp_kses_post(wp_trim_words(get_the_content(), 40, '...')) . '</p>';
            }
        endif;
        ?>
    </div>

    <?php if (!$is_single) : ?>
        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more-link">
                <?php esc_html_e('View Step Details', 'julianboelen'); ?>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
        </footer>
    <?php endif; ?>

</article>

<?php if (!$is_single) : ?>
<style>
.process-step-content {
    position: relative;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    margin-bottom: 30px;
}

.process-step-content:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-4px);
}

.step-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #0073aa;
    color: #fff;
    padding: 8px 16px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 115, 170, 0.3);
}

.badge-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-number {
    font-size: 1.125rem;
    font-weight: 700;
}

.entry-thumbnail {
    position: relative;
    overflow: hidden;
    background: #f0f0f0;
}

.entry-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
}

.process-step-content:hover .entry-thumbnail img {
    transform: scale(1.05);
}

.process-step-content .entry-header {
    padding: 25px 25px 15px;
}

.process-step-content .entry-title {
    font-size: 1.5rem;
    margin: 0 0 10px;
    line-height: 1.3;
}

.process-step-content .entry-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.process-step-content .entry-title a:hover {
    color: #0073aa;
}

.entry-meta {
    font-size: 0.875rem;
    color: #666;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.entry-meta a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.entry-meta a:hover {
    color: #0073aa;
}

.process-step-content .entry-content {
    padding: 0 25px 20px;
    color: #666;
    line-height: 1.6;
}

.process-step-content .entry-footer {
    padding: 0 25px 25px;
}

.read-more-link {
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

.read-more-link:hover {
    background: #005a87;
    color: #fff;
    gap: 12px;
}

.read-more-link .arrow {
    transition: transform 0.3s ease;
}

.read-more-link:hover .arrow {
    transform: translateX(4px);
}

@media (max-width: 768px) {
    .process-step-content .entry-title {
        font-size: 1.25rem;
    }

    .process-step-content .entry-header,
    .process-step-content .entry-content,
    .process-step-content .entry-footer {
        padding-left: 20px;
        padding-right: 20px;
    }
}
</style>
<?php endif; ?>