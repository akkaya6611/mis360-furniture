<?php
/**
 * Archive Template (Montessori Blog & Guides)
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
        <h1 class="page-title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
    </header>

    <div class="emdief-blog-grid">
        <?php
        if (have_posts()):
            while (have_posts()):
                the_post();
                $card_city = get_post_meta(get_the_ID(), '_mis360_ai_city', true);
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
                            <span>📅 <?php echo get_the_date(); ?></span>
                            <?php if (!empty($card_city)) : ?>
                                <span class="card-loc-pill" style="background:#fff7ed;color:#ea580c;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">📍 <?php echo esc_html($card_city); ?></span>
                            <?php endif; ?>
                        </div>
                        <h2 class="card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="card-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="card-read-more">
                            <?php esc_html_e('Devamını Oku &rarr;', 'mis360-mobilya'); ?>
                        </a>
                    </div>
                </article>
                <?php
            endwhile;

            the_posts_pagination();
        else:
            ?>
            <p><?php esc_html_e('Bu kategoride henüz yazı bulunamadı.', 'mis360-mobilya'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
