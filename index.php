<?php
/**
 * Main Template Fallback
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

get_header();
if (function_exists('mis360_breadcrumbs')) {
    mis360_breadcrumbs();
}
?>

<div class="emdief-container py-8">
    <header class="page-header mb-6">
        <h1 class="page-title"><?php single_post_title(); ?></h1>
    </header>

    <div class="emdief-blog-grid">
        <?php
        if (have_posts()):
            while (have_posts()):
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('emdief-blog-card'); ?>>
                    <?php if (has_post_thumbnail()): ?>
                        <div class="card-media">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-meta">
                            <span><?php echo get_the_date(); ?></span>
                        </div>
                        <h2 class="card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="card-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="card-read-more">
                            <?php esc_html_e('Devam�n� Oku &rarr;', 'mis360-mobilya'); ?>
                        </a>
                    </div>
                </article>
                <?php
            endwhile;

            the_posts_pagination([
                'prev_text' => __('&laquo; �nceki', 'mis360-mobilya'),
                'next_text' => __('Sonraki &raquo;', 'mis360-mobilya'),
            ]);
        else:
            ?>
            <p><?php esc_html_e('Hen�z i�erik bulunmuyor.', 'mis360-mobilya'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
