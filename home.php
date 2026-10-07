<?php
/**
 * Blog Ana Sayfa Template (home.php)
 * WordPress bu dosyayı "Settings > Reading > Posts page" atandığında kullanır.
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

<!-- Blog Hero Bandı -->
<div class="emdief-blog-hero">
    <div class="emdief-container">
        <div class="blog-hero-inner">
            <div class="blog-hero-label">📝 <?php esc_html_e('Montessori Rehberleri', 'mis360-mobilya'); ?></div>
            <h1 class="blog-hero-title"><?php esc_html_e('Blog & Montessori İlham Köşesi', 'mis360-mobilya'); ?></h1>
            <p class="blog-hero-desc"><?php esc_html_e('Çocuğunuzun bağımsız gelişimini destekleyen Montessori yöntemleri, ürün rehberleri ve ebeveyn ipuçları burada.', 'mis360-mobilya'); ?></p>
            <!-- Kategori Linkleri -->
            <div class="blog-hero-cats">
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="blog-cat-pill active"><?php esc_html_e('Tümü', 'mis360-mobilya'); ?></a>
                <a href="<?php echo esc_url(home_url('/category/montessori-rehberleri/')); ?>" class="blog-cat-pill">🌱 <?php esc_html_e('Montessori Rehberleri', 'mis360-mobilya'); ?></a>
                <a href="<?php echo esc_url(home_url('/category/montessori-yerel-rehberler/')); ?>" class="blog-cat-pill">📍 <?php esc_html_e('Yerel Şehir Rehberleri', 'mis360-mobilya'); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="emdief-container emdief-blog-page-wrap">

    <?php if (have_posts()): ?>

        <!-- Öne Çıkan İlk Yazı -->
        <?php $first_post = true; ?>
        <div class="emdief-blog-featured-row">
        <?php while (have_posts()): the_post();
            $city  = get_post_meta(get_the_ID(), '_mis360_ai_city', true);
            $cats  = get_the_terms(get_the_ID(), 'category');
            $cat_name = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : '';
            $cat_url  = (!empty($cats) && !is_wp_error($cats)) ? get_term_link($cats[0]) : '#';

            if ($first_post): $first_post = false; ?>
            <!-- Featured Card -->
            <article id="post-<?php the_ID(); ?>" <?php post_class('emdief-blog-featured-card'); ?>>
                <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>" class="featured-card-thumb">
                        <?php the_post_thumbnail('large'); ?>
                        <?php if ($cat_name): ?>
                            <span class="featured-card-cat"><?php echo esc_html($cat_name); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <div class="featured-card-body">
                    <div class="blog-meta-row">
                        <?php if ($city): ?>
                            <span class="blog-meta-loc"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> <?php echo esc_html($city); ?></span>
                        <?php endif; ?>
                        <span class="blog-meta-date"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg> <?php echo get_the_date('d M Y'); ?></span>
                    </div>
                    <h2 class="featured-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="featured-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?></div>
                    <a href="<?php the_permalink(); ?>" class="blog-read-btn">
                        <?php esc_html_e('Yazının Devamını Oku', 'mis360-mobilya'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
            </div><!-- /.emdief-blog-featured-row -->

            <!-- Diğer Yazılar Grid -->
            <div class="emdief-blog-grid-main">

            <?php else: /* Diğer yazılar */ ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('emdief-blog-card-v2'); ?>>
                <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>" class="blog-card-thumb">
                        <?php the_post_thumbnail('medium_large'); ?>
                        <?php if ($cat_name): ?>
                            <span class="blog-card-cat"><?php echo esc_html($cat_name); ?></span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <a href="<?php the_permalink(); ?>" class="blog-card-thumb blog-card-no-thumb">
                        <span>📝</span>
                    </a>
                <?php endif; ?>
                <div class="blog-card-body-v2">
                    <div class="blog-meta-row">
                        <?php if ($city): ?>
                            <span class="blog-meta-loc">📍 <?php echo esc_html($city); ?></span>
                        <?php endif; ?>
                        <span class="blog-meta-date"><?php echo get_the_date('d M Y'); ?></span>
                    </div>
                    <h3 class="blog-card-title-v2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="blog-card-excerpt-v2"><?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?></p>
                    <a href="<?php the_permalink(); ?>" class="blog-card-link">Devamını Oku →</a>
                </div>
            </article>

            <?php endif; // End if first_post ?>
        <?php endwhile; ?>

        </div><!-- /.emdief-blog-grid-main -->

        <!-- Sayfalama -->
        <div class="emdief-blog-pagination">
            <?php
            the_posts_pagination([
                'prev_text'          => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg> ' . __('Önceki', 'mis360-mobilya'),
                'next_text'          => __('Sonraki', 'mis360-mobilya') . ' <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>',
                'before_page_number' => '',
                'mid_size'           => 2,
            ]);
            ?>
        </div>

    <?php else: ?>
        <div class="emdief-blog-empty">
            <span>📝</span>
            <h2><?php esc_html_e('Henüz blog yazısı yok', 'mis360-mobilya'); ?></h2>
            <p><?php esc_html_e('Yakında Montessori eğitim ipuçları ve ürün rehberleri burada yayınlanacak.', 'mis360-mobilya'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Blog Alt CTA -->
    <div class="emdief-blog-cta-strip">
        <div class="blog-cta-text">
            <strong><?php esc_html_e('Montessori mobilyaları incelemek ister misiniz?', 'mis360-mobilya'); ?></strong>
            <span><?php esc_html_e('Kitaplıklar, duvar masaları ve eğitici oyuncaklar sizi bekliyor.', 'mis360-mobilya'); ?></span>
        </div>
        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/magaza/')); ?>" class="blog-cta-btn">
            <?php esc_html_e('Ürünleri Keşfet', 'mis360-mobilya'); ?> →
        </a>
    </div>

</div><!-- /.emdief-container -->

<?php get_footer(); ?>
