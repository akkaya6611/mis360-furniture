<?php
/**
 * Custom Product Card Template
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('emdief-product-card', $product); ?>>
    <div class="card-inner">
        <!-- Görsel Alanı & Rozetler -->
        <div class="card-thumb-wrap">
            <?php
            // Rozetler
            if (function_exists('mis360_product_badges')) {
                mis360_product_badges();
            }
            ?>
            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="card-image-link">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('woocommerce_thumbnail', ['class' => 'product-main-img']);
                } else {
                    echo wc_placeholder_img('woocommerce_thumbnail');
                }
                ?>
            </a>
            <!-- Hızlı Ekle / Sepete Ekle Butonu -->
            <div class="card-hover-actions">
                <?php
                woocommerce_template_loop_add_to_cart([
                    'class' => 'emdief-btn-add-cart emdief-btn btn-primary btn-sm btn-block ajax_add_to_cart',
                ]);
                ?>
            </div>
        </div>

        <!-- Başlık, Yıldız ve Fiyat -->
        <div class="card-details">
            <div class="card-category">
                <?php
                $terms = get_the_terms($product->get_id(), 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    echo esc_html($terms[0]->name);
                } else {
                    echo esc_html__('Montessori Mobilya', 'mis360-mobilya');
                }
                ?>
            </div>
            <h3 class="card-product-title">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>"><?php the_title(); ?></a>
            </h3>
            <div class="card-rating">
                <span class="stars">★★★★★</span>
                <span class="rating-text">(5.0)</span>
            </div>
            <div class="card-price-row">
                <div class="price-box">
                    <?php echo $product->get_price_html(); ?>
                </div>
            </div>
        </div>
    </div>
</li>
