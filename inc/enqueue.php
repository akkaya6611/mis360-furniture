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
    $main_css_file = file_exists($theme_dir . '/assets/css/main.min.css') ? '/assets/css/main.min.css' : '/assets/css/main.css';
    $wc_css_file   = file_exists($theme_dir . '/assets/css/woocommerce.min.css') ? '/assets/css/woocommerce.min.css' : '/assets/css/woocommerce.css';
    $main_js_file  = file_exists($theme_dir . '/assets/js/main.min.js') ? '/assets/js/main.min.js' : '/assets/js/main.js';
    $cart_js_file  = file_exists($theme_dir . '/assets/js/ajax-cart.min.js') ? '/assets/js/ajax-cart.min.js' : '/assets/js/ajax-cart.js';

    $style_ver    = file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : '1.2.0';
    $main_css_ver = file_exists($theme_dir . $main_css_file) ? filemtime($theme_dir . $main_css_file) : '1.2.0';
    $wc_css_ver   = file_exists($theme_dir . $wc_css_file) ? filemtime($theme_dir . $wc_css_file) : '1.2.0';
    $main_js_ver  = file_exists($theme_dir . $main_js_file) ? filemtime($theme_dir . $main_js_file) : '1.2.0';
    $cart_js_ver  = file_exists($theme_dir . $cart_js_file) ? filemtime($theme_dir . $cart_js_file) : '1.2.0';

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

    // 3. Ana Arayüz Stilleri (assets/css/main.min.css)
    wp_enqueue_style(
        'mis360-main',
        MIS360_MOBILYA_URI . $main_css_file,
        ['mis360-style'],
        $main_css_ver
    );

    // 4. WooCommerce Özel Stilleri (Sadece WooCommerce aktifken)
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'mis360-woocommerce',
            MIS360_MOBILYA_URI . $wc_css_file,
            ['mis360-main'],
            $wc_css_ver
        );
    }

    $free_shipping_min = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
    $mis360_data = [
        'ajaxUrl'           => admin_url('admin-ajax.php'),
        'nonce'             => wp_create_nonce('mis360_cart_nonce'),
        'freeShippingLimit' => $free_shipping_min,
        'currencySymbol'    => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'TL',
        'addedToCartText'   => __('Sepete Eklendi!', 'mis360-mobilya'),
        'addingText'        => __('Ekleniyor...', 'mis360-mobilya'),
        'isUserLoggedIn'    => is_user_logged_in(),
        'checkoutUrl'       => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/odeme/'),
        'cartUrl'           => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/sepet/'),
        'isCheckout'        => function_exists('is_checkout') ? (is_checkout() && !is_order_received_page()) : false,
        'isCart'            => function_exists('is_cart') ? is_cart() : false,
    ];

    // 5. Ana Tema Scripti (Vanilla JS)
    wp_enqueue_script(
        'mis360-main-js',
        MIS360_MOBILYA_URI . $main_js_file,
        [],
        $main_js_ver,
        true
    );
    wp_localize_script('mis360-main-js', 'mis360Data', $mis360_data);

    // 6. WooCommerce AJAX Sepet ve Çekmece Scripti
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
        wp_enqueue_script(
            'mis360-ajax-cart',
            MIS360_MOBILYA_URI . $cart_js_file,
            ['jquery', 'wc-add-to-cart', 'wc-cart-fragments', 'mis360-main-js'],
            $cart_js_ver,
            true
        );
        wp_localize_script('mis360-ajax-cart', 'mis360Data', $mis360_data);
    }
}
add_action('wp_enqueue_scripts', 'mis360_mobilya_scripts');

