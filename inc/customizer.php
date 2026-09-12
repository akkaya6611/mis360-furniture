<?php
/**
 * Theme Customizer Settings
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

function mis360_customize_register($wp_customize) {
    // 1. Emdief Home Genel Ayarlar Paneli
    $wp_customize->add_panel('emdief_theme_options', [
        'title'       => __('Emdief Home & Montessori Ayarları', 'mis360-mobilya'),
        'description' => __('Topbar, iletişim ve Montessori duyuru ayarları', 'mis360-mobilya'),
        'priority'    => 20,
    ]);

    // Bölüm: Üst Duyuru Çubuğu (Topbar)
    $wp_customize->add_section('emdief_topbar_section', [
        'title' => __('Üst Duyuru Çubuğu (Topbar)', 'mis360-mobilya'),
        'panel' => 'emdief_theme_options',
    ]);

    $wp_customize->add_setting('mis360_topbar_text', [
        'default'           => "13:00'a Kadar Verilen Siparişler Öncelikli İmalata Alınır! | 1500 TL Üzeri Ücretsiz Kargo",
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('mis360_topbar_text', [
        'label'    => __('Duyuru Metni', 'mis360-mobilya'),
        'section'  => 'emdief_topbar_section',
        'type'     => 'text',
    ]);

    $wp_customize->add_setting('mis360_free_shipping_limit', [
        'default'           => 1500,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('mis360_free_shipping_limit', [
        'label'    => __('Ücretsiz Kargo Barajı (TL)', 'mis360-mobilya'),
        'section'  => 'emdief_topbar_section',
        'type'     => 'number',
    ]);

    // Bölüm: Kurumsal İletişim & WhatsApp
    $wp_customize->add_section('emdief_contact_section', [
        'title' => __('İletişim & Canlı Destek', 'mis360-mobilya'),
        'panel' => 'emdief_theme_options',
    ]);

    $wp_customize->add_setting('mis360_phone', [
        'default'           => '+90 537 477 87 66',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('mis360_phone', [
        'label'   => __('Müşteri Hizmetleri Telefonu', 'mis360-mobilya'),
        'section' => 'emdief_contact_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('mis360_whatsapp', [
        'default'           => '905374778766',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('mis360_whatsapp', [
        'label'       => __('WhatsApp Numarası (Ülke kodu ile, örn: 905374778766)', 'mis360-mobilya'),
        'description' => __('Sitedeki WhatsApp hızlı sipariş butonlarında kullanılır.', 'mis360-mobilya'),
        'section'     => 'emdief_contact_section',
        'type'        => 'text',
    ]);

    // Bölüm: Montessori Renk Özelleştirmeleri
    $wp_customize->add_section('emdief_colors_section', [
        'title' => __('Montessori Renk Paleti', 'mis360-mobilya'),
        'panel' => 'emdief_theme_options',
    ]);

    $wp_customize->add_setting('mis360_color_primary', [
        'default'           => '#f59e0b',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mis360_color_primary', [
        'label'   => __('Ana Montessori Rengi (Güneş Sarısı)', 'mis360-mobilya'),
        'section' => 'emdief_colors_section',
    ]));

    $wp_customize->add_setting('mis360_color_secondary', [
        'default'           => '#0284c7',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mis360_color_secondary', [
        'label'   => __('İkincil Keşif Rengi (Mavi)', 'mis360-mobilya'),
        'section' => 'emdief_colors_section',
    ]));
}
add_action('customize_register', 'mis360_customize_register');
