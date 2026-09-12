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
