<?php
/**
 * Theme Setup & Capabilities
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

function mis360_mobilya_setup() {
    // Çeviri desteği
    load_theme_textdomain('mis360-mobilya', get_template_directory() . '/languages');

    // Başlık etiketi desteği
    add_theme_support('title-tag');

    // Öne çıkarılmış görsel desteği
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 600, true);
    add_image_size('emdief-product-thumb', 600, 600, true);
    add_image_size('emdief-hero-banner', 1400, 650, true);
    add_image_size('emdief-category-bubble', 300, 300, true);

    // Menüler
    register_nav_menus([
        'primary'       => __('Ana Menü', 'mis360-mobilya'),
        'mobile'        => __('Mobil Menü', 'mis360-mobilya'),
        'footer_col_1'  => __('Kurumsal Menü (Footer 1)', 'mis360-mobilya'),
        'footer_col_2'  => __('Montessori & Kategoriler (Footer 2)', 'mis360-mobilya'),
    ]);

    // HTML5 Desteği
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Özel Logo Desteği
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 260,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // WooCommerce Desteği ve Galeri Özellikleri
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 500,
        'single_image_width'    => 800,
        'product_grid'          => [
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 6,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ],
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Responsive embedler ve geniş hizalama desteği
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
}
add_action('after_setup_theme', 'mis360_mobilya_setup');

/**
 * WooCommerce Tekil Ürün Galeri Slaytı (Otomatik Geçiş & Gezinme Okları)
 */
function mis360_single_product_carousel_options($options) {
    $options['slideshow']      = true;  // Sayfaya girince slayt halinde otomatik değişsin
    $options['slideshowSpeed'] = 3500;  // 3.5 saniye aralıkla sonraki görsele geçsin
    $options['animationSpeed'] = 600;   // Yumuşak kayma hızı
    $options['animationLoop']  = true;  // Sürekli başa dönerek döngüye girsin
    $options['pauseOnHover']   = true;  // Müşteri görseli incelerken dursun
    $options['directionNav']   = true;  // Sağ / Sol gezinme okları görünsün
    $options['smoothHeight']   = true;
    return $options;
}
add_filter('woocommerce_single_product_carousel_options', 'mis360_single_product_carousel_options');

/**
 * /shop/ veya /shop/* İsteklerini Otomatik /magaza/ Sayfasına 301 Yönlendir (Kırık Link Engeli - v1.9.80)
 */
add_action('template_redirect', 'mis360_redirect_legacy_shop_urls', 1);
function mis360_redirect_legacy_shop_urls() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);
    if ($path === '/shop' || $path === '/shop/') {
        $shop_url = class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/magaza/');
        if (empty($shop_url) || $shop_url === home_url('/')) {
            $shop_url = home_url('/magaza/');
        }
        $query = parse_url($uri, PHP_URL_QUERY);
        $target = $query ? ($shop_url . '?' . $query) : $shop_url;
        wp_safe_redirect($target, 301);
        exit;
    }
}

