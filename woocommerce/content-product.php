<?php
/**
 * Custom Product Card Template (Emdief Home Premium Catalogue Standard)
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

$id = $product->get_id();
$permalink = $product->get_permalink();
?>
<li <?php wc_product_class('emdief-product-card', $product); ?>>
    <div class="card-inner">
        <!-- Görsel Alanı & Rozetler (Ferah Beyaz Boşluk & Katalog Standardı) -->
        <div class="card-thumb-wrap">
            <?php
            // Rozetler
            if (function_exists('mis360_product_badges')) {
                mis360_product_badges();
            }
            ?>
            <a href="<?php echo esc_url($permalink); ?>" class="card-image-link">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('woocommerce_thumbnail', ['class' => 'product-main-img']);
                } else {
                    echo wc_placeholder_img('woocommerce_thumbnail');
                }
                ?>
            </a>
        </div>

        <!-- Başlık, Satış Odaklı Fayda, Yıldız ve Fiyat -->
        <div class="card-details">
            <div class="card-category">
                <?php
                $terms = get_the_terms($id, 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    echo esc_html($terms[0]->name);
                } else {
                    echo esc_html__('Montessori Mobilya', 'mis360-mobilya');
                }
                ?>
            </div>
            <h3 class="card-product-title">
                <a href="<?php echo esc_url($permalink); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
                    <strong>Emdief</strong> <?php the_title(); ?>
                </a>
            </h3>

            <!-- Satış Odaklı Fayda Açıklaması -->
            <div class="card-benefit-tagline">
                <?php echo esc_html(function_exists('mis360_get_product_benefit_tagline') ? mis360_get_product_benefit_tagline($product) : '1. Sınıf E1 MDF • CNC Hazır Delikler • Güvenli Kavisler'); ?>
            </div>

            <!-- Kullanıcı Güveni İçin Yıldız Puan Alanı -->
            <div class="card-rating">
                <span class="stars">★★★★★</span>
                <span class="rating-score">4.9</span>
                <span class="rating-text">(120+ Değerlendirme)</span>
            </div>

            <!-- Fiyat Alanı -->
            <div class="card-price-row">
                <div class="price-box">
                    <?php echo $product->get_price_html(); ?>
                </div>
            </div>

            <!-- Belirgin Satın Alma / Sepete Ekle Butonu -->
            <div class="card-action-row">
                <?php
                woocommerce_template_loop_add_to_cart([
                    'class' => 'emdief-btn-add-cart emdief-btn btn-primary btn-block ajax_add_to_cart',
                ]);
                ?>
            </div>
        </div>
    </div>
</li>
