<?php
/**
 * Custom WooCommerce Shop & Product Archive Template
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

get_header('shop');
mis360_breadcrumbs();
?>

<div class="emdief-shop-header-banner">
    <div class="emdief-container">
        <div class="shop-banner-inner">
            <h1 class="shop-banner-title">
                <?php woocommerce_page_title(); ?>
            </h1>
            <?php
            $term_desc = is_product_taxonomy() ? term_description() : '';
            if (!empty($term_desc)): ?>
                <div class="shop-banner-sub"><?php echo wp_kses_post($term_desc); ?></div>
            <?php else: ?>
                <p class="shop-banner-sub">
                    <?php esc_html_e('Montessori pedagojisine uygun, 1. sınıf MDF çocuk odası ve eğitici kitaplık koleksiyonu.', 'mis360-mobilya'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="emdief-container emdief-shop-content">
    <div class="emdief-shop-wrapper">
        <!-- Üst Filtre & Sıralama Barı -->
        <div class="emdief-shop-toolbar">
            <div class="toolbar-left">
                <?php woocommerce_result_count(); ?>
            </div>
            <div class="toolbar-right">
                <?php woocommerce_catalog_ordering(); ?>
            </div>
        </div>

        <?php if (woocommerce_product_loop()): ?>
            <?php woocommerce_product_loop_start(); ?>

            <?php if (wc_get_loop_prop('total')): ?>
                <?php while (have_posts()): the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php woocommerce_product_loop_end(); ?>

            <div class="emdief-pagination-wrap">
                <?php woocommerce_pagination(); ?>
            </div>
        <?php else: ?>
            <?php do_action('woocommerce_no_products_found'); ?>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer('shop');
