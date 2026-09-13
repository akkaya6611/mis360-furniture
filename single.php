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
                        <span class="meta-date">?? <?php echo get_the_date(); ?></span>
                        <span class="meta-author">?? <?php the_author(); ?></span>
                    </div>
                </header>

                <?php if (has_post_thumbnail()): ?>
                    <div class="single-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
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
