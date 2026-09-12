<?php
/**
 * Enqueue Styles and Scripts
 *
 * @package Mis360-Furniture
 */


if (!defined('ABSPATH')) {
    exit;
}

function mis360_furniture_scripts(): void {
    // Dequeue WooCommerce default layout floats if causing narrow columns
    // wp_dequeue_style('woocommerce-layout');
    // 1. Google Fonts: Plus Jakarta Sans (Hafif ve modern tipografi)
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
        MIS360_FURNITURE_VERSION
    );

    // 3. Ana Arayüz Stilleri (assets/css/main.css)
    wp_enqueue_style(
        'mis360-main',
        MIS360_FURNITURE_URI . '/assets/css/main.css',
        ['mis360-style'],
        MIS360_FURNITURE_VERSION
    );

    // 4. WooCommerce Özel Stilleri (Sadece WooCommerce aktifken)
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'mis360-woocommerce',
            MIS360_FURNITURE_URI . '/assets/css/woocommerce.css',
            ['mis360-main', 'woocommerce-general', 'woocommerce-layout'],
            MIS360_FURNITURE_VERSION
        );
    }

    // 5. Ana Tema Scripti (Vanilla JS, Defer)
    wp_enqueue_script(
        'mis360-main-js',
        MIS360_FURNITURE_URI . '/assets/js/main.js',
        [],
        MIS360_FURNITURE_VERSION,
        [
            'strategy'  => 'defer',
            'in_footer' => true,
        ]
    );

    // 6. WooCommerce AJAX Sepet ve Çekmece Scripti
    if (class_exists('WooCommerce')) {
        wp_enqueue_script(
            'mis360-ajax-cart',
            MIS360_FURNITURE_URI . '/assets/js/ajax-cart.js',
            ['mis360-main-js'],
            MIS360_FURNITURE_VERSION,
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ]
        );

        $free_shipping_min = (float) get_theme_mod('mis360_free_shipping_limit', 1500);

        wp_localize_script('mis360-ajax-cart', 'mis360Data', [
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            'nonce'             => wp_create_nonce('mis360_cart_nonce'),
            'freeShippingLimit' => $free_shipping_min,
            'currencySymbol'    => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'TL',
            'addedToCartText'   => __('Sepete Eklendi!', 'mis360-furniture'),
            'addingText'        => __('Ekleniyor...', 'mis360-furniture'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'mis360_furniture_scripts');
