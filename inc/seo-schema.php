<?php
/**
 * Schema.org JSON-LD Structured Data
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

function mis360_output_json_ld() {
    // 1. Organization & Brand Schema
    $org_schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'FurnitureStore',
        'name'     => 'Emdief Home',
        'url'      => home_url('/'),
        'logo'     => get_template_directory_uri() . '/assets/images/emdief-home-logo.webp',
        'description' => 'Montessori felsefesine uygun, 1. sınıf kaliteli MDF çocuk odası mobilyaları ve Montessori ürünleri koleksiyonu.',
        'telephone' => get_theme_mod('mis360_phone', '+90 537 477 87 66'),
        'priceRange' => 'TL',
        'address' => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Mobilya Kent Kırmızı Bloklar, Camikebir Mahallesi, 5066. Sk No:1 D:K',
            'addressLocality'  => 'Kocasinan',
            'addressRegion'    => 'Kayseri',
            'postalCode'      => '38070',
            'addressCountry'  => 'TR',
        ],
        'hasMap' => 'https://www.google.com/maps/place//data=!4m2!3m1!1s0x152b057da63cc6c7:0x45e8ad2179bc179c?sa=X&ved=1t:8290&ictx=111',
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

    // 2. Product Schema on Single Product Page
    if (class_exists('WooCommerce') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product instanceof WC_Product) {
            $product_schema = [
                '@context'    => 'https://schema.org',
                '@type'       => 'Product',
                'name'        => $product->get_name(),
                'image'       => wp_get_attachment_image_url($product->get_image_id(), 'full'),
                'description' => wp_strip_all_tags($product->get_short_description() ?: $product->get_description()),
                'sku'         => $product->get_sku() ?: (string) $product->get_id(),
                'brand'       => [
                    '@type' => 'Brand',
                    'name'  => 'Emdief Home',
                ],
                'offers'      => [
                    '@type'         => 'Offer',
                    'url'           => get_permalink($product->get_id()),
                    'priceCurrency' => get_woocommerce_currency(),
                    'price'         => $product->get_price(),
                    'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ],
            ];
            echo '<script type="application/ld+json">' . wp_json_encode($product_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'mis360_output_json_ld', 30);
