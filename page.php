<?php
/**
 * Single Page Template
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
    <?php
    while (have_posts()):
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('emdief-page-article'); ?>>
            <header class="entry-header mb-6">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>
            <div class="entry-content typography-prose">
                <?php
                the_content();
                wp_link_pages();
                ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</div>

<?php
get_footer();
