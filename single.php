<?php
/**
 * Single Post Template (Montessori Blog & Articles)
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
    <div class="emdief-article-wrapper">
        <?php
        while (have_posts()):
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('emdief-single-post'); ?>>
                <header class="single-header">
                    <div class="single-category">
                        <?php the_category(', '); ?>
                    </div>
                    <h1 class="single-title"><?php the_title(); ?></h1>
                    <div class="single-meta">
                        <?php 
                        $ai_city = get_post_meta(get_the_ID(), '_mis360_ai_city', true);
                        $ai_dist = get_post_meta(get_the_ID(), '_mis360_ai_district', true);
                        if (!empty($ai_city)) : 
                            $loc_badge = (!empty($ai_dist) ? esc_html($ai_dist) . ' / ' : '') . esc_html($ai_city);
                        ?>
                            <span class="meta-location" style="background:#fff7ed;color:#ea580c;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;border:1px solid #fed7aa;">📍 <?php echo $loc_badge; ?> Yerel Rehberi</span>
                        <?php endif; ?>
                        <span class="meta-date">📅 <?php echo get_the_date(); ?></span>
                        <span class="meta-author">✍️ <?php the_author(); ?></span>
                        <?php 
                        $word_count = str_word_count(strip_tags(get_the_content()));
                        $read_time  = max(1, ceil($word_count / 180));
                        ?>
                        <span class="meta-readtime">⏱️ <?php echo esc_html($read_time); ?> dk okuma</span>
                    </div>
                </header>

                <?php if (has_post_thumbnail()): ?>
                    <div class="single-thumbnail">
                        <?php the_post_thumbnail('large', ['title' => '', 'alt' => esc_attr(get_the_title())]); ?>
                    </div>
                <?php endif; ?>

                <div class="single-content typography-prose">
                    <?php the_content(); ?>
                </div>

                <footer class="single-footer">
                    <div class="post-tags">
                        <?php the_tags('<span class="tag-title">Etiketler:</span> ', ' '); ?>
                    </div>
                </footer>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</div>

<?php
get_footer();
