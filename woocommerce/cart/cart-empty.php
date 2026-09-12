<?php
/**
 * Mis360-Mobilya Boş Sepet Şablonu (Empty Cart Template)
 * Ürün yoksa ağlayan sevimli Montessori ayıcığı sergiler.
 *
 * @package Mis360_Mobilya
 */

defined('ABSPATH') || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
do_action('woocommerce_cart_is_empty');

if (wc_get_page_id('shop') > 0): ?>
    <div class="emdief-cart-empty emdief-cart-empty-page">
        <div class="empty-bear-wrap">
            <?php echo function_exists('mis360_crying_bear') ? mis360_crying_bear(160, 145, 'animated-page-crying-bear') : '<div class="empty-icon">🧸</div>'; ?>
        </div>
        <div class="empty-bear-badge">🥺 Ayıcık Çok Üzgün!</div>
        <h2 class="empty-cart-title"><?php esc_html_e('Sepetiniz Şu Anda Bomboş Kaldı...', 'mis360-mobilya'); ?></h2>
        <p class="empty-cart-desc">
            <?php esc_html_e('Miniklerin odasına neşe ve düzen katacak 1. sınıf kaliteli MDF Montessori mobilyalarımızı sepetinize ekleyin, sevimli ayıcığımızın gözyaşları dinsin!', 'mis360-mobilya'); ?>
        </p>
        <p class="return-to-shop">
            <a class="button wc-backward emdief-btn btn-primary btn-lg" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
                <span><?php echo esc_html(apply_filters('woocommerce_return_to_shop_text', __('Alışverişe Başla', 'mis360-mobilya'))); ?></span>
                <?php echo function_exists('mis360_icon') ? mis360_icon('arrow-right', 18) : ''; ?>
            </a>
        </p>
    </div>
<?php endif; ?>
