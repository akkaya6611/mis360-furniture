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
    // Bölüm: ETBİS (Ticaret Bakanlığı) Doğrulama & Güven Rozeti
    $wp_customize->add_section('emdief_etbis_section', [
        'title'       => __('ETBİS & Güven Damgası', 'mis360-mobilya'),
        'description' => __('T.C. Ticaret Bakanlığı Elektronik Ticaret Bilgi Sistemi (ETBİS) resmi kayıt ve karekod ayarları.', 'mis360-mobilya'),
        'panel'       => 'emdief_theme_options',
    ]);

    // 1. ETBİS Rozeti Gösterilsin mi?
    $wp_customize->add_setting('mis360_etbis_enabled', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('mis360_etbis_enabled', [
        'label'   => __('ETBİS Rozetini Sitede Göster', 'mis360-mobilya'),
        'section' => 'emdief_etbis_section',
        'type'    => 'checkbox',
    ]);

    // 2. ETBİS Sorgu / Doğrulama Bağlantısı
    $wp_customize->add_setting('mis360_etbis_url', [
        'default'           => 'https://etbis.eticaret.gov.tr/',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('mis360_etbis_url', [
        'label'       => __('ETBİS Doğrulama / Sorgu Bağlantısı', 'mis360-mobilya'),
        'description' => __('Kullanıcı karekoda veya rozete tıkladığında açılacak Ticaret Bakanlığı sayfası (Varsayılan: https://etbis.eticaret.gov.tr/)', 'mis360-mobilya'),
        'section'     => 'emdief_etbis_section',
        'type'        => 'url',
    ]);

    // 3. Özel ETBİS Karekod Görseli
    $wp_customize->add_setting('mis360_etbis_qr_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    if (class_exists('WP_Customize_Image_Control')) {
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mis360_etbis_qr_image', [
            'label'       => __('Özel ETBİS Karekod Görseli', 'mis360-mobilya'),
            'description' => __('Ticaret Bakanlığı ETBİS panelinizden indirdiğiniz karekod PNG/JPG görselini buradan yükleyebilirsiniz.', 'mis360-mobilya'),
            'section'     => 'emdief_etbis_section',
        ]));
    } else {
        $wp_customize->add_control('mis360_etbis_qr_image', [
            'label'   => __('Özel ETBİS Karekod Görseli URL', 'mis360-mobilya'),
            'section' => 'emdief_etbis_section',
            'type'    => 'text',
        ]);
    }

    // 4. Bakanlıktan Alınan Resmi Embed / Script Kodu
    $wp_customize->add_setting('mis360_etbis_custom_code', [
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('mis360_etbis_custom_code', [
        'label'       => __('ETBİS Resmi Embed / Script Kodu (Opsiyonel)', 'mis360-mobilya'),
        'description' => __('Ticaret Bakanlığı doğrudan HTML/JavaScript kodu verdiyse buraya yapıştırabilirsiniz. Doluysa özel kart yerine bu kod basılır.', 'mis360-mobilya'),
        'section'     => 'emdief_etbis_section',
        'type'        => 'textarea',
    ]);

    // 5. Üst Duyuru Çubuğunda (Topbar) Göster
    $wp_customize->add_setting('mis360_etbis_show_topbar', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('mis360_etbis_show_topbar', [
        'label'   => __('Üst Duyuru Çubuğunda (Topbar) Göster', 'mis360-mobilya'),
        'section' => 'emdief_etbis_section',
        'type'    => 'checkbox',
    ]);

    // 6. Ürün Detay Sayfasında Göster
    $wp_customize->add_setting('mis360_etbis_show_product', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('mis360_etbis_show_product', [
        'label'   => __('Ürün Detay Sayfası Güven Kutusunda Göster', 'mis360-mobilya'),
        'section' => 'emdief_etbis_section',
        'type'    => 'checkbox',
    ]);
}
add_action('customize_register', 'mis360_customize_register');
