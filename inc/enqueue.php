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

    $ver_prefix   = defined('MIS360_MOBILYA_VERSION') ? MIS360_MOBILYA_VERSION . '.' : '1.9.84.';
    $style_ver    = $ver_prefix . (file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : '1.0');
    $main_css_ver = $ver_prefix . (file_exists($theme_dir . $main_css_file) ? filemtime($theme_dir . $main_css_file) : '1.0');
    $wc_css_ver   = $ver_prefix . (file_exists($theme_dir . $wc_css_file) ? filemtime($theme_dir . $wc_css_file) : '1.0');
    $main_js_ver  = $ver_prefix . (file_exists($theme_dir . $main_js_file) ? filemtime($theme_dir . $main_js_file) : '1.0');
    $cart_js_ver  = $ver_prefix . (file_exists($theme_dir . $cart_js_file) ? filemtime($theme_dir . $cart_js_file) : '1.0');

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

    // 3. Ana ArayÃ¼z Stilleri (assets/css/main.min.css)
    wp_enqueue_style(
        'mis360-main',
        MIS360_MOBILYA_URI . $main_css_file,
        ['mis360-style'],
        $main_css_ver
    );

    // 4. WooCommerce Ã–zel Stilleri (Sadece WooCommerce aktifken)
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

    // 6. WooCommerce AJAX Sepet ve Ã‡ekmece Scripti
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
// Critical Mobile Cart & Checkout Layout Inline CSS (Zero Browser Cache Lag)
function mis360_inject_cart_checkout_inline_css() {
    if (class_exists('WooCommerce') && (is_cart() || is_checkout())) {
        $cart_css = "
        @media (max-width: 768px) {
            .woocommerce-cart table.shop_table.cart thead { display: none !important; }
            .woocommerce-cart table.shop_table.cart, .woocommerce-cart table.shop_table.cart tbody { display: block !important; width: 100% !important; border: none !important; background: transparent !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item { display: grid !important; grid-template-columns: 85px 1fr !important; grid-template-rows: auto auto auto !important; column-gap: 14px !important; row-gap: 6px !important; background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 16px !important; padding: 14px !important; margin-bottom: 14px !important; position: relative !important; box-shadow: 0 2px 10px rgba(0,0,0,0.03) !important; box-sizing: border-box !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-remove { position: absolute !important; top: 10px !important; right: 10px !important; padding: 0 !important; border: none !important; display: block !important; z-index: 5 !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-remove a.remove { display: flex !important; align-items: center !important; justify-content: center !important; width: 28px !important; height: 28px !important; background: #fef2f2 !important; color: #ef4444 !important; border-radius: 50% !important; font-size: 18px !important; font-weight: 700 !important; line-height: 1 !important; text-decoration: none !important; border: 1px solid #fee2e2 !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-thumbnail { grid-column: 1 !important; grid-row: 1 / span 3 !important; padding: 0 !important; border: none !important; display: flex !important; align-items: flex-start !important; justify-content: center !important; width: 85px !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-thumbnail img { width: 85px !important; height: 85px !important; min-width: 85px !important; object-fit: contain !important; border-radius: 12px !important; background: #f8fafc !important; border: 1px solid #f1f5f9 !important; padding: 4px !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-name { grid-column: 2 !important; grid-row: 1 !important; padding: 0 32px 0 0 !important; border: none !important; display: block !important; text-align: left !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-name a { font-size: 13.5px !important; font-weight: 700 !important; color: #1e293b !important; line-height: 1.4 !important; display: -webkit-box !important; -webkit-line-clamp: 2 !important; -webkit-box-orient: vertical !important; overflow: hidden !important; text-decoration: none !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-price { grid-column: 2 !important; grid-row: 2 !important; padding: 2px 0 !important; border: none !important; display: flex !important; align-items: center !important; text-align: left !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-price .amount { font-size: 15.5px !important; font-weight: 800 !important; color: #ea580c !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-quantity { grid-column: 2 !important; grid-row: 3 !important; padding: 4px 0 0 0 !important; border: none !important; display: flex !important; align-items: center !important; text-align: left !important; }
            .woocommerce-cart table.shop_table.cart tr.cart_item td.product-subtotal { display: none !important; }
            .woocommerce-cart table.shop_table.cart tr td.actions { display: block !important; width: 100% !important; padding: 12px 0 !important; border: none !important; background: transparent !important; }
            .woocommerce-cart table.shop_table.cart tr td.actions .coupon { display: flex !important; gap: 8px !important; align-items: center !important; margin-bottom: 12px !important; width: 100% !important; }
            .woocommerce-cart table.shop_table.cart tr td.actions .coupon input.input-text { flex: 1 1 auto !important; height: 44px !important; padding: 0 14px !important; border: 1.5px solid #cbd5e1 !important; border-radius: 12px !important; font-size: 13.5px !important; background: #ffffff !important; margin: 0 !important; }
            .woocommerce-cart table.shop_table.cart tr td.actions .coupon button.button { flex: 0 0 auto !important; height: 44px !important; padding: 0 18px !important; background: #1e293b !important; color: #ffffff !important; font-weight: 700 !important; font-size: 13px !important; border-radius: 12px !important; border: none !important; cursor: pointer !important; margin: 0 !important; }
            .woocommerce-cart table.shop_table.cart tr td.actions button[name='update_cart'] { width: 100% !important; height: 42px !important; background: #f8fafc !important; color: #475569 !important; font-weight: 700 !important; font-size: 13px !important; border-radius: 12px !important; border: 1px solid #e2e8f0 !important; margin-bottom: 10px !important; }
            .woocommerce-cart table.shop_table.cart tr td.actions .emdief-empty-cart-trigger { width: 100% !important; margin-top: 4px !important; }
        }
        .btn-whatsapp-order { display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; width: 100% !important; background: #25d366 !important; color: #ffffff !important; font-size: 14px !important; font-weight: 800 !important; padding: 14px 20px !important; border-radius: 14px !important; text-decoration: none !important; margin-top: 12px !important; margin-bottom: 14px !important; box-shadow: 0 4px 14px rgba(37,211,102,0.28) !important; box-sizing: border-box !important; }
        .cart-page-trust-grid, .cart-page-trust-pills { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 8px !important; margin-top: 14px !important; padding: 0 !important; background: transparent !important; border: none !important; box-sizing: border-box !important; }
        .cart-trust-item, .trust-pill-item { background: #f8fafc !important; border: 1px solid #e2e8f0 !important; border-radius: 10px !important; padding: 10px 8px !important; display: flex !important; align-items: center !important; gap: 8px !important; font-size: 11px !important; font-weight: 700 !important; color: #334155 !important; line-height: 1.3 !important; box-sizing: border-box !important; }
        .cart-trust-item svg, .trust-pill-item svg { flex-shrink: 0 !important; width: 18px !important; height: 18px !important; }
        .woocommerce-form-coupon-toggle { margin-bottom: 20px !important; }
        .woocommerce-form-coupon-toggle .woocommerce-info { background: #fffbeb !important; border: 1.5px solid #fde68a !important; border-radius: 12px !important; padding: 12px 18px !important; color: #92400e !important; font-size: 13.5px !important; font-weight: 600 !important; }
        .woocommerce-form-coupon-toggle .woocommerce-info a.showcoupon { color: #d97706 !important; font-weight: 700 !important; text-decoration: underline !important; }
        form.checkout_coupon { display: flex !important; gap: 10px !important; align-items: center !important; background: #ffffff !important; border: 1.5px solid #e2e8f0 !important; border-radius: 14px !important; padding: 16px !important; margin-bottom: 24px !important; box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important; flex-wrap: wrap !important; }
        form.checkout_coupon p { margin: 0 !important; }
        form.checkout_coupon input.input-text { height: 44px !important; padding: 0 16px !important; border: 1.5px solid #cbd5e1 !important; border-radius: 10px !important; font-size: 14px !important; flex: 1 1 200px !important; box-sizing: border-box !important; }
        form.checkout_coupon button.button { height: 44px !important; padding: 0 22px !important; background: #1e293b !important; color: #ffffff !important; font-weight: 700 !important; font-size: 13.5px !important; border-radius: 10px !important; border: none !important; cursor: pointer !important; white-space: nowrap !important; }
        ";
        wp_add_inline_style('mis360-style', $cart_css);
    }
}
add_action('wp_enqueue_scripts', 'mis360_inject_cart_checkout_inline_css', 999);
// Blog Sayfası CSS'ini Yükle
function mis360_enqueue_blog_css() {
    if (is_home() || is_category() || is_tag() || is_archive() || is_page('blog') || is_page_template('page-blog.php')) {
        wp_enqueue_style(
            'mis360-blog',
            get_template_directory_uri() . '/assets/css/blog.css',
            ['mis360-style'],
            defined('MIS360_MOBILYA_VERSION') ? MIS360_MOBILYA_VERSION : '1.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'mis360_enqueue_blog_css');
