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
if (function_exists('mis360_breadcrumbs')) {
    mis360_breadcrumbs();
}
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
                    <?php esc_html_e('Montessori pedagojisine uygun, 1. sınıf MDF çocuk odası ve Montessori ürünleri koleksiyonu.', 'mis360-mobilya'); ?>
                </p>
            <?php endif; ?>

            <!-- Üst Kategori Gezinme Barı (Category Pills Bar) -->
            <?php
            $shop_categories = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'orderby'    => 'count',
                'order'      => 'DESC',
            ]);

            if (!empty($shop_categories) && !is_wp_error($shop_categories)):
                $current_cat_id = is_product_category() ? get_queried_object_id() : 0;
                $is_all_active  = (is_shop() || (is_post_type_archive('product') && !is_product_taxonomy()));

                $total_products = 0;
                if (function_exists('wp_count_posts')) {
                    $c_obj = wp_count_posts('product');
                    $total_products = isset($c_obj->publish) ? $c_obj->publish : 0;
                }
            ?>
            <div class="shop-category-bar-wrapper">
                <div class="shop-category-pills" role="navigation" aria-label="<?php esc_attr_e('Ürün Kategorileri', 'mis360-mobilya'); ?>">
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="cat-pill-item <?php echo $is_all_active ? 'is-active' : ''; ?>">
                        <span class="cat-pill-icon">✨</span>
                        <span class="cat-pill-name"><?php esc_html_e('Tüm Ürünler', 'mis360-mobilya'); ?></span>
                        <?php if ($total_products > 0): ?>
                            <span class="cat-pill-count"><?php echo esc_html($total_products); ?></span>
                        <?php endif; ?>
                    </a>
                    <?php foreach ($shop_categories as $cat): 
                        $is_active = ($current_cat_id === $cat->term_id);
                        $cat_link  = get_term_link($cat);
                        $icon      = function_exists('mis360_get_category_icon') ? mis360_get_category_icon($cat) : '🏷️';
                    ?>
                        <a href="<?php echo esc_url($cat_link); ?>" class="cat-pill-item <?php echo $is_active ? 'is-active' : ''; ?>">
                            <span class="cat-pill-icon"><?php echo $icon; ?></span>
                            <span class="cat-pill-name"><?php echo esc_html($cat->name); ?></span>
                            <span class="cat-pill-count"><?php echo esc_html($cat->count); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
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
