<?php
/**
 * Enqueue Styles and Scripts
 *
 * @package Mis360-Mobilya
 */

if (!defined('ABSPATH')) {
    exit;
}

function mis360_mobilya_scripts() {
    $theme_dir   = get_template_directory();
    $style_ver   = file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : '1.2.0';
    $main_css_ver = file_exists($theme_dir . '/assets/css/main.css') ? filemtime($theme_dir . '/assets/css/main.css') : '1.2.0';
    $wc_css_ver  = file_exists($theme_dir . '/assets/css/woocommerce.css') ? filemtime($theme_dir . '/assets/css/woocommerce.css') : '1.2.0';
    $main_js_ver = file_exists($theme_dir . '/assets/js/main.js') ? filemtime($theme_dir . '/assets/js/main.js') : '1.2.0';
    $cart_js_ver = file_exists($theme_dir . '/assets/js/ajax-cart.js') ? filemtime($theme_dir . '/assets/js/ajax-cart.js') : '1.2.0';

    // 1. Google Fonts: Plus Jakarta Sans
    wp_enqueue_style(
        'mis360-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    // 2. Temel Stil (style.css)
    wp_enqueue_style(
        'mis360-style',
        get_stylesheet_uri(),
        [],
        $style_ver
    );

    // 3. Ana Arayüz Stilleri (assets/css/main.css)
    wp_enqueue_style(
        'mis360-main',
        MIS360_MOBILYA_URI . '/assets/css/main.css',
        ['mis360-style'],
        $main_css_ver
    );

    // 4. WooCommerce Özel Stilleri (Sadece WooCommerce aktifken)
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'mis360-woocommerce',
            MIS360_MOBILYA_URI . '/assets/css/woocommerce.css',
            ['mis360-main'],
            $wc_css_ver
        );
    }

    // 5. Ana Tema Scripti (Vanilla JS)
    wp_enqueue_script(
        'mis360-main-js',
        MIS360_MOBILYA_URI . '/assets/js/main.js',
        [],
        $main_js_ver,
        true
    );

    // 6. WooCommerce AJAX Sepet ve Çekmece Scripti
    if (class_exists('WooCommerce')) {
        wp_enqueue_script(
            'mis360-ajax-cart',
            MIS360_MOBILYA_URI . '/assets/js/ajax-cart.js',
            ['jquery', 'mis360-main-js'],
            $cart_js_ver,
            true
        );

        $free_shipping_min = (float) get_theme_mod('mis360_free_shipping_limit', 1500);

        wp_localize_script('mis360-ajax-cart', 'mis360Data', [
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            'nonce'             => wp_create_nonce('mis360_cart_nonce'),
            'freeShippingLimit' => $free_shipping_min,
            'currencySymbol'    => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'TL',
            'addedToCartText'   => __('Sepete Eklendi!', 'mis360-mobilya'),
            'addingText'        => __('Ekleniyor...', 'mis360-mobilya'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'mis360_mobilya_scripts');

