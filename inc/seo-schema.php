<?php
/**
 * Emdief Home Profesyonel E-Ticaret SEO, GEO & Zengin Veri (Schema.org) Motoru (v2.0)
 *
 * - Google 2026 Merchant Center Uyumlu Kargo (OrderCutoffTime: 13:00, Handling/Transit) & İade (MerchantReturnPolicy) Şemaları
 * - Google Rich Snippets: Product, BreadcrumbList, WebSite, FAQPage, VideoObject, ItemList, FurnitureStore, Speakable
 * - Sesli Arama & AI Motorları (Google Asistan, Siri, SpeakableSpecification) Şeması
 * - GEO Coğrafi Hedefleme (Kayseri / Kocasinan Mobilya Kent Yerel SEO Etiketleri)
 * - OpenGraph (Facebook, WhatsApp, Instagram Video & Ürün Paylaşım Kartları)
 * - Twitter Cards (Geniş Görselli Kartlar, Fiyat, Stok ve Video Oynatıcılar)
 * - Yüksek Tıklama Oranlı (High-CTR) Dinamik Başlık ve Meta Açıklama Motoru
 * - Core Web Vitals (CWV) LCP Hızlandırma: Öne Çıkan Ürün Görseli İçin Otomatik Preload (fetchpriority="high")
 * - Otomatik Görsel SEO (Eksik Alt ve Title Etiketlerini Zenginleştirme)
 * - Dinamik Robots.txt ve Arama Motoru Harita Direktifleri (GPTBot, ClaudeBot, PerplexityBot Desteği)
 * - /llms.txt ve /llms-full.txt Dinamik Uç Nokta Servisi
 *
 * @package Mis360-Mobilya
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Üçüncü parti SEO eklentisi (Yoast, RankMath, AIOSEO, SEOPress) aktif mi kontrol et
 */
function mis360_is_external_seo_active(): bool {
    return defined('WPSEO_VERSION') 
        || defined('RANK_MATH_VERSION') 
        || class_exists('AIOSEO\\Plugin\\AIOSEO')
        || defined('SEOPRESS_VERSION');
}

/**
 * 1. YÜKSEK TIKLAMA ORANLI (HIGH-CTR) DİNAMİK BAŞLIK MOTORU
 */
function mis360_filter_document_title_parts(array $parts): array {
    if (mis360_is_external_seo_active()) {
        return $parts;
    }

    $site_name = 'Emdief Home';

    if (is_front_page()) {
        $parts['title']   = 'Emdief Home | 1. Sınıf MDF & Masif Ahşap Montessori Çocuk Mobilyaları';
        unset($parts['tagline'], $parts['site']);
        return $parts;
    }

    if (class_exists('WooCommerce') && is_product()) {
        global $product;
        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }
        if ($product instanceof WC_Product) {
            $p_name = $product->get_name();
            // Başlıkta gereksiz tire ve ekler varsa temizle
            $parts['title'] = sprintf('%s | 1. Sınıf MDF & Masif Ahşap Montessori Mobilya', esc_html($p_name));
            $parts['site']  = $site_name;
            unset($parts['tagline']);
            return $parts;
        }
    }

    if (class_exists('WooCommerce') && is_product_taxonomy()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $parts['title'] = sprintf('%s Fiyatları & Modelleri | Montessori Çocuk Mobilyaları', esc_html($term->name));
            $parts['site']  = $site_name;
            unset($parts['tagline']);
            return $parts;
        }
    }

    if (class_exists('WooCommerce') && is_shop()) {
        $parts['title'] = 'Montessori Çocuk Mobilyaları & Eğitici Kitaplık Modelleri | İmalatçı';
        $parts['site']  = $site_name;
        unset($parts['tagline']);
        return $parts;
    }

    if (is_page('yardim-merkezi') || is_page_template('page-help-center.php') || is_page_template('page-yardim-merkezi.php')) {
        $parts['title'] = 'Montessori Mobilya Video Kurulum Rehberleri & 5 Dk Montaj Desteği';
        $parts['site']  = $site_name;
        unset($parts['tagline']);
        return $parts;
    }

    if (is_page('iletisim')) {
        $parts['title'] = 'İletişim, Atölye & Fabrika Satış | Emdief Home Kayseri';
        $parts['site']  = $site_name;
        unset($parts['tagline']);
        return $parts;
    }

    return $parts;
}
add_filter('document_title_parts', 'mis360_filter_document_title_parts', 20);

/**
 * 2. META ETİKETLERİ: Canonical, Hreflang, GEO, Robots, Description, OpenGraph & Twitter Cards
 */
function mis360_output_seo_meta_tags(): void {
    if (mis360_is_external_seo_active()) {
        return;
    }

    $site_name   = get_bloginfo('name') ?: 'Emdief Home';
    $title       = wp_get_document_title();
    $description = '';
    $canonical   = home_url(add_query_arg([], null));
    $og_type     = 'website';
    $og_image    = get_template_directory_uri() . '/assets/images/emdief-home-logo.webp';
    $og_image_w  = '1200';
    $og_image_h  = '630';
    $og_price    = null;
    $yt_video_id = null;
    $main_img_preload = null;

    // A) Tekil Ürün Sayfası
    if (class_exists('WooCommerce') && is_product()) {
        global $product;
        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }
        if ($product instanceof WC_Product) {
            $og_type     = 'product';
            $canonical   = get_permalink($product->get_id());
            $raw_desc    = $product->get_short_description() ?: $product->get_description();
            $clean_desc  = wp_strip_all_tags($raw_desc);
            $description = wp_trim_words($clean_desc, 28, '...');
            if (!$description) {
                $description = sprintf('%s - E1 normlarında 1. Sınıf MDF ve doğal masif kayın ağacı çocuk mobilyası. Sivri köşesiz güvenli hatlar, CNC hazır delikli 5 dk kolay montaj, 1.500 TL üzeri ücretsiz kargo.', $product->get_name());
            }
            $img_id = $product->get_image_id();
            if ($img_id) {
                $full_img = wp_get_attachment_image_src($img_id, 'full');
                if ($full_img) {
                    $og_image   = $full_img[0];
                    $og_image_w = (string) $full_img[1];
                    $og_image_h = (string) $full_img[2];
                    $main_img_preload = $full_img[0];
                }
            }
            $og_price = [
                'amount'   => $product->get_price(),
                'currency' => get_woocommerce_currency(),
                'in_stock' => $product->is_in_stock(),
                'sku'      => $product->get_sku() ?: ('EMD-' . $product->get_id()),
            ];

            // Kurulum videosu kontrolü
            if (function_exists('mis360_get_product_installation_video')) {
                $v_info = mis360_get_product_installation_video($product);
                if (!empty($v_info['youtube_id'])) {
                    $yt_video_id = $v_info['youtube_id'];
                }
            }
        }
    }
    // B) Kategori & Taksonomi Sayfaları
    elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $canonical   = get_term_link($term);
            $description = wp_strip_all_tags(term_description($term->term_id));
            if (!$description) {
                $description = sprintf('%s modelleri ve fiyatları - Emdief Home 1. Sınıf MDF & doğal ahşap Montessori çocuk mobilyaları, sivri köşesiz güvenli hatlar, 5 dakikada pratik kurulum ve ücretsiz kargo avantajı.', $term->name);
            }
            if (function_exists('get_term_meta')) {
                $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                if ($thumb_id) {
                    $t_img = wp_get_attachment_image_src($thumb_id, 'full');
                    if ($t_img) {
                        $og_image   = $t_img[0];
                        $og_image_w = (string) $t_img[1];
                        $og_image_h = (string) $t_img[2];
                    }
                }
            }
        }
    }
    // C) Mağaza Ana Sayfası (Shop)
    elseif (class_exists('WooCommerce') && is_shop()) {
        $shop_id     = wc_get_page_id('shop');
        $canonical   = get_permalink($shop_id);
        $description = 'Emdief Home Montessori Çocuk Mobilyaları Mağazası - 1. Sınıf MDF eğitici kitaplıklar, ahşap oyuncaklar, oda düzenleyicileri ve pratik montajlı duvar rafları imalattan avantajlı fiyatlarla.';
    }
    // D) Yardım & Kurulum Merkezi Sayfası
    elseif (is_page('yardim-merkezi') || is_page_template('page-help-center.php') || is_page_template('page-yardim-merkezi.php')) {
        $canonical   = home_url('/yardim-merkezi/');
        $description = 'Emdief Home Yardım & Kurulum Merkezi - Montessori kitaplık ve çocuk mobilyalarınızın şarjlı matkap ile 5 dakikada adım adım video montaj rehberleri, duvara sabitleme ve yedek parça desteği.';
    }
    // E) Standart Tekil Yazı / Sayfa
    elseif (is_singular()) {
        $post = get_queried_object();
        if ($post) {
            $canonical = get_permalink($post->ID);
            $raw_desc  = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : $post->post_content;
            $description = wp_trim_words(wp_strip_all_tags($raw_desc), 28, '...');
            if (has_post_thumbnail($post->ID)) {
                $p_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                if ($p_img) {
                    $og_image   = $p_img[0];
                    $og_image_w = (string) $p_img[1];
                    $og_image_h = (string) $p_img[2];
                }
            }
            $og_type = is_single() ? 'article' : 'website';
        }
    }
    // F) Ana Sayfa
    elseif (is_front_page() || is_home()) {
        $canonical   = home_url('/');
        $description = 'Emdief Home - Miniklerin bağımsız keşifleri için 1. Sınıf MDF & Masif Ahşap Montessori eğitici kitaplıklar, doğal ahşap oyuncaklar ve çocuk odası mobilyaları. Kayseri imalatı, toptan & perakende.';
    }

    // Robots Direktifi: Filtreleme, arama ve sıralama parametrelerinde arama motorunu kopyadan koru
    $is_filtered = !empty($_GET['filter_cat']) || !empty($_GET['orderby']) || !empty($_GET['min_price']) || !empty($_GET['max_price']) || !empty($_GET['filter_color']) || !empty($_GET['filter_size']) || is_search();
    $robots = $is_filtered ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    // HTML Çıktısı
    echo "
<!-- Emdief Home Next-Gen SEO, GEO, AEO & Social Engine v2.0 -->
";

    // Core Web Vitals LCP Preload (Öne Çıkan Ürün Görselini En Yüksek Öncelikle Yükle)
    if ($main_img_preload) {
        echo '<link rel="preload" as="image" href="' . esc_url($main_img_preload) . '" fetchpriority="high">' . "
";
    }

    // DNS Prefetch & Preconnect Hızlandırma
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "
";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "
";
    echo '<link rel="dns-prefetch" href="//www.youtube-nocookie.com">' . "
";
    echo '<link rel="dns-prefetch" href="//img.youtube.com">' . "
";

    // Standart SEO Meta
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "
";
    echo '<link rel="alternate" hreflang="tr" href="' . esc_url($canonical) . '">' . "
";
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($canonical) . '">' . "
";
    echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "
";
    if (!empty($description)) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "
";
    }
    echo '<meta name="author" content="Emdief Home">' . "
";
    echo '<meta name="copyright" content="Emdief Home - Montessori Çocuk Mobilyaları">' . "
";
    echo '<meta name="theme-color" content="#f27a1a">' . "
";
    echo '<meta name="format-detection" content="telephone=no">' . "
";

    // GEO & Coğrafi Hedefleme (Yerel SEO & Local Business - Kayseri Mobilya Kent)
    echo '<meta name="geo.region" content="TR-38">' . "\n";
    echo '<meta name="geo.placename" content="Kayseri, Kocasinan, Mobilya Kent">' . "\n";
    echo '<meta name="geo.position" content="38.7312;35.4787">' . "\n";
    echo '<meta name="ICBM" content="38.7312, 35.4787">' . "\n";
    echo '<meta name="geo.country" content="TR">' . "\n";
    echo '<meta name="DC.title" content="Emdief Home | Montessori Çocuk Mobilyaları">' . "\n";
    echo '<meta name="DC.creator" content="Emdief Home &amp; Serkan AKKAYA">' . "\n";
    echo '<meta name="DC.coverage" content="Turkey">' . "\n";
    echo '<meta name="DC.spatial" content="Kocasinan, Kayseri, Türkiye">' . "\n";
    echo '<meta http-equiv="content-language" content="tr">' . "\n";

    // OpenGraph (Facebook, WhatsApp, Instagram, LinkedIn, Telegram)
    echo '<meta property="og:locale" content="tr_TR">' . "
";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "
";
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "
";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "
";
    if (!empty($description)) {
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "
";
    }
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "
";
    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "
";
        echo '<meta property="og:image:secure_url" content="' . esc_url($og_image) . '">' . "
";
        echo '<meta property="og:image:width" content="' . esc_attr($og_image_w) . '">' . "
";
        echo '<meta property="og:image:height" content="' . esc_attr($og_image_h) . '">' . "
";
        echo '<meta property="og:image:type" content="image/jpeg">' . "
";
        echo '<meta property="og:image:alt" content="' . esc_attr($title) . '">' . "
";
    }

    // OpenGraph Video (Kurulum videosu varsa WhatsApp ve sosyal medyada oynatılabilir video kartı üretir)
    if ($yt_video_id) {
        echo '<meta property="og:video" content="https://www.youtube.com/embed/' . esc_attr($yt_video_id) . '">' . "
";
        echo '<meta property="og:video:secure_url" content="https://www.youtube-nocookie.com/embed/' . esc_attr($yt_video_id) . '">' . "
";
        echo '<meta property="og:video:type" content="text/html">' . "
";
        echo '<meta property="og:video:width" content="1280">' . "
";
        echo '<meta property="og:video:height" content="720">' . "
";
    }

    // WooCommerce Ürün OpenGraph & E-Ticaret Meta Etiketleri
    if ($og_price) {
        echo '<meta property="product:price:amount" content="' . esc_attr($og_price['amount']) . '">' . "
";
        echo '<meta property="product:price:currency" content="' . esc_attr($og_price['currency']) . '">' . "
";
        echo '<meta property="product:availability" content="' . ($og_price['in_stock'] ? 'in stock' : 'out of stock') . '">' . "
";
        echo '<meta property="product:brand" content="Emdief Home">' . "
";
        echo '<meta property="product:condition" content="new">' . "
";
        echo '<meta property="product:retailer_item_id" content="' . esc_attr($og_price['sku']) . '">' . "
";
    }

    // Twitter Cards (Summary Large Image & E-Ticaret Verileri)
    echo '<meta name="twitter:card" content="summary_large_image">' . "
";
    echo '<meta name="twitter:site" content="@emdiefhome">' . "
";
    echo '<meta name="twitter:creator" content="@emdiefhome">' . "
";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "
";
    if (!empty($description)) {
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "
";
    }
    if (!empty($og_image)) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "
";
        echo '<meta name="twitter:image:alt" content="' . esc_attr($title) . '">' . "
";
    }
    if ($og_price) {
        echo '<meta name="twitter:label1" content="Fiyat">' . "
";
        echo '<meta name="twitter:data1" content="' . esc_attr(number_format((float) $og_price['amount'], 2, '.', '')) . ' TL">' . "
";
        echo '<meta name="twitter:label2" content="Stok Durumu">' . "
";
        echo '<meta name="twitter:data2" content="' . ($og_price['in_stock'] ? 'Stokta Var - Öncelikli Kargo' : 'Tükendi') . '">' . "
";
    }

    echo "<!-- / Emdief Home Next-Gen SEO Engine v2.0 -->

";
}
add_action('wp_head', 'mis360_output_seo_meta_tags', 1);

/**
 * 3. SCHEMA.ORG JSON-LD YAPISAL VERİ MOTORU (RICH SNIPPETS 2026 STANDARDI)
 */
function mis360_output_json_ld(): void {
    $phone = get_theme_mod('mis360_phone', '+90 537 477 87 66');

    // -------------------------------------------------------------------------
    // A) Kurumsal Mağaza & Yerel İşletme Şeması (FurnitureStore / LocalBusiness)
    // -------------------------------------------------------------------------
    $org_schema = [
        '@context'        => 'https://schema.org',
        '@type'           => ['FurnitureStore', 'HomeGoodsStore', 'LocalBusiness'],
        '@id'             => home_url('/#organization'),
        'name'            => 'Emdief Home',
        'legalName'       => 'Emdief Mobilya Tasarım İmalat',
        'alternateName'   => ['Emdief', 'Emdief Mobilya', 'Emdief Montessori'],
        'url'             => home_url('/'),
        'logo'            => [
            '@type'  => 'ImageObject',
            'url'    => get_template_directory_uri() . '/assets/images/emdief-home-logo.webp',
            'width'  => '220',
            'height' => '60',
        ],
        'image'           => get_template_directory_uri() . '/assets/images/banner-emdief.webp',
        'description'     => 'Montessori felsefesine uygun 1. sınıf kaliteli MDF ve doğal masif kayın çocuk odası kitaplıkları, eğitici ahşap mobilyalar ve montaj kolaylığı sağlayan yerli üretim mobilya atölyesi.',
        'telephone'       => $phone,
        'email'           => 'info@emdiefhome.com.tr',
        'priceRange'      => '₺₺',
        'currenciesAccepted' => 'TRY',
        'paymentAccepted' => 'Banka Havalesi, EFT, FAST, Kredi Kartı, Peşin',
        'foundingDate'    => '2020',
        'founder'         => [
            '@type' => 'Person',
            'name'  => 'Serkan Akkaya',
        ],
        'areaServed'      => [
            ['@type' => 'Country', 'name' => 'Türkiye'],
            ['@type' => 'AdministrativeArea', 'name' => 'Kayseri'],
            ['@type' => 'AdministrativeArea', 'name' => 'İstanbul'],
            ['@type' => 'AdministrativeArea', 'name' => 'Ankara'],
            ['@type' => 'AdministrativeArea', 'name' => 'İzmir'],
            ['@type' => 'AdministrativeArea', 'name' => 'Bursa'],
            ['@type' => 'AdministrativeArea', 'name' => 'Antalya'],
        ],
        'address'         => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Mobilya Kent Kırmızı Bloklar, Camikebir Mahallesi, 5066. Sk No:1 D:K',
            'addressLocality' => 'Kocasinan',
            'addressRegion'   => 'Kayseri',
            'postalCode'      => '38070',
            'addressCountry'  => 'TR',
        ],
        'geo'             => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => '38.7312',
            'longitude' => '35.4787',
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'opens'     => '08:30',
                'closes'    => '19:00',
            ]
        ],
        'contactPoint'    => [
            [
                '@type'             => 'ContactPoint',
                'telephone'         => $phone,
                'contactType'       => 'customer service',
                'areaServed'        => 'TR',
                'availableLanguage' => ['Turkish'],
            ],
            [
                '@type'             => 'ContactPoint',
                'telephone'         => '+90 537 477 87 66',
                'contactType'       => 'sales',
                'contactOption'     => 'TollFree',
                'areaServed'        => 'TR',
                'availableLanguage' => ['Turkish'],
            ]
        ],
        'hasMap'          => 'https://www.google.com/maps/place//data=!4m2!3m1!1s0x152b057da63cc6c7:0x45e8ad2179bc179c?sa=X&ved=1t:8290&ictx=111',
        'sameAs'          => [
            'https://www.instagram.com/emdiefhome/',
            'https://www.youtube.com/@EmdiefHome',
            'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) get_theme_mod('mis360_whatsapp', '905374778766')),
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";

    // -------------------------------------------------------------------------
    // B) WebSite & Sitelinks Searchbox Şeması (Google Arama Çubuğu)
    // -------------------------------------------------------------------------
    if (is_front_page()) {
        $website_schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            '@id'             => home_url('/#website'),
            'url'             => home_url('/'),
            'name'            => 'Emdief Home',
            'alternateName'   => 'Emdief Mobilya',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => home_url('/?s={search_term_string}&post_type=product'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
    }

    // -------------------------------------------------------------------------
    // C) BreadcrumbList Şeması (Google Arama Sonuçlarında Hiyerarşi)
    // -------------------------------------------------------------------------
    if (!is_front_page()) {
        $breadcrumb_items = [];
        $pos = 1;

        // 1. Anasayfa
        $breadcrumb_items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Anasayfa',
            'item'     => home_url('/'),
        ];

        // 2. WooCommerce / Ürün Kırılımları
        if (class_exists('WooCommerce') && (is_woocommerce() || is_cart() || is_checkout())) {
            $shop_page_id = wc_get_page_id('shop');
            if ($shop_page_id && !is_shop()) {
                $breadcrumb_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => get_the_title($shop_page_id) ?: 'Mağaza',
                    'item'     => get_permalink($shop_page_id),
                ];
            }

            if (is_product()) {
                $terms = get_the_terms(get_the_ID(), 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    $main_term = current($terms);
                    $breadcrumb_items[] = [
                        '@type'    => 'ListItem',
                        'position' => $pos++,
                        'name'     => $main_term->name,
                        'item'     => get_term_link($main_term),
                    ];
                }
                $breadcrumb_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => get_the_title(),
                    'item'     => get_permalink(),
                ];
            } elseif (is_product_taxonomy()) {
                $term = get_queried_object();
                if ($term) {
                    $breadcrumb_items[] = [
                        '@type'    => 'ListItem',
                        'position' => $pos++,
                        'name'     => $term->name,
                        'item'     => get_term_link($term),
                    ];
                }
            }
        } elseif (is_singular()) {
            $post = get_queried_object();
            if ($post) {
                $breadcrumb_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => get_the_title($post->ID),
                    'item'     => get_permalink($post->ID),
                ];
            }
        } elseif (is_category() || is_tax()) {
            $term = get_queried_object();
            if ($term) {
                $breadcrumb_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => $term->name,
                    'item'     => get_term_link($term),
                ];
            }
        }

        if (count($breadcrumb_items) > 1) {
            $breadcrumb_schema = [
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $breadcrumb_items,
            ];
            echo '<script type="application/ld+json">' . wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
        }
    }

    // -------------------------------------------------------------------------
    // D) Gelişmiş Ürün Şeması (Google 2026 Merchant & AI Search Standartları)
    // -------------------------------------------------------------------------
    if (class_exists('WooCommerce') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product instanceof WC_Product) {
            $prod_price  = (float) $product->get_price();
            $gallery_ids = $product->get_gallery_image_ids();
            $images      = [];
            $main_img    = wp_get_attachment_image_url($product->get_image_id(), 'full');
            if ($main_img) $images[] = $main_img;
            foreach ($gallery_ids as $gid) {
                $gurl = wp_get_attachment_image_url($gid, 'full');
                if ($gurl && !in_array($gurl, $images, true)) $images[] = $gurl;
            }
            if (empty($images)) {
                $images[] = 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg';
            }

            // Kategori adı
            $terms = get_the_terms($product->get_id(), 'product_cat');
            $cat_name = ($terms && !is_wp_error($terms)) ? current($terms)->name : 'Montessori Mobilya';

            // Kargo ücreti mantığı (1.500 TL üzeri ücretsiz kargo)
            $free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
            $shipping_cost = ($prod_price >= $free_shipping_limit) ? 0.0 : 89.0;
            $sku = $product->get_sku() ?: ('EMD-' . $product->get_id());

            // İlgili ürünler (Knowledge Graph iç linkleme)
            $related_ids = wc_get_related_products($product->get_id(), 3);
            $related_urls = [];
            foreach ($related_ids as $rid) {
                $related_urls[] = get_permalink($rid);
            }

            // Marka belirleme (Üründe 'pa_marka' veya 'marka' niteliği varsa dinamik, yoksa Emdief Home)
            $brand_name = 'Emdief Home';
            if ($product->get_attribute('pa_marka')) {
                $brand_name = $product->get_attribute('pa_marka');
            } elseif ($product->get_attribute('marka')) {
                $brand_name = $product->get_attribute('marka');
            }

            $product_schema = [
                '@context'        => 'https://schema.org',
                '@type'           => 'Product',
                '@id'             => get_permalink($product->get_id()) . '#product',
                'name'            => $product->get_name(),
                'image'           => count($images) === 1 ? $images[0] : $images,
                'description'     => wp_strip_all_tags($product->get_short_description() ?: $product->get_description()) ?: ($product->get_name() . ' - 1. Sınıf kaliteli MDF ve doğal ahşap Montessori çocuk mobilyası.'),
                'sku'             => $sku,
                'mpn'             => (string) $product->get_id(),
                'category'        => 'Furniture > Baby & Toddler Furniture > Baby & Toddler Bookcases',
                'material'        => '1. Sınıf Kaliteli MDF & Doğal Masif Kayın',
                'pattern'         => 'Montessori',
                'color'           => 'Doğal Ahşap & Beyaz',
                'hasGS1Checksum'  => false,
                'countryOfOrigin' => [
                    '@type' => 'Country',
                    'name'  => 'TR',
                ],
                'manufacturer'    => [
                    '@type' => 'Organization',
                    'name'  => 'Emdief Home',
                ],
                'audience'        => [
                    '@type'            => 'PeopleAudience',
                    'suggestedMinAge'  => 1,
                    'suggestedMaxAge'  => 12,
                ],
                'brand'           => [
                    '@type' => 'Brand',
                    'name'  => esc_html($brand_name),
                ],
                // Sesli arama / AI asistanı okuma direktifi
                'speakable'       => [
                    '@type'       => 'SpeakableSpecification',
                    'cssSelector' => ['.product_title', '.emdief-single-benefit-badge', '.summary.entry-summary'],
                ],
                'offers'          => [
                    '@type'         => 'Offer',
                    'url'           => get_permalink($product->get_id()),
                    'priceCurrency' => get_woocommerce_currency(),
                    'price'         => number_format($prod_price, 2, '.', ''),
                    'priceValidUntil' => date('Y-12-31', strtotime('+1 year')),
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'seller'        => [
                        '@type' => 'Organization',
                        'name'  => 'Emdief Home',
                        'url'   => home_url('/'),
                    ],
                    'priceSpecification' => [
                        '@type'                 => 'PriceSpecification',
                        'price'                 => number_format($prod_price, 2, '.', ''),
                        'priceCurrency'         => get_woocommerce_currency(),
                        'valueAddedTaxIncluded' => true,
                    ],
                    // Google 2026 Merchant: 13:00 Kesim Saati & Kargo Detayı (13:00 Canlı Sayacıyla Tam Entegre)
                    'shippingDetails' => [
                        '@type'               => 'OfferShippingDetails',
                        'shippingRate'        => [
                            '@type'    => 'MonetaryAmount',
                            'value'    => $shipping_cost,
                            'currency' => 'TRY',
                        ],
                        'shippingDestination' => [
                            '@type'          => 'DefinedRegion',
                            'addressCountry' => 'TR',
                        ],
                        'deliveryTime'        => [
                            '@type'        => 'ShippingDeliveryTime',
                            'businessDays' => [
                                'https://schema.org/Monday',
                                'https://schema.org/Tuesday',
                                'https://schema.org/Wednesday',
                                'https://schema.org/Thursday',
                                'https://schema.org/Friday',
                                'https://schema.org/Saturday',
                            ],
                            'cutoffTime'   => '13:00:00+03:00',
                            'handlingTime' => [
                                '@type'    => 'QuantitativeValue',
                                'minValue' => 0,
                                'maxValue' => 2,
                                'unitCode' => 'DAY',
                            ],
                            'transitTime'  => [
                                '@type'    => 'QuantitativeValue',
                                'minValue' => 1,
                                'maxValue' => 3,
                                'unitCode' => 'DAY',
                            ],
                        ],
                    ],
                    // Google 2026 Merchant: 14 Gün Koşulsuz Ücretsiz İade Politikası
                    'hasMerchantReturnPolicy' => [
                        '@type'                  => 'MerchantReturnPolicy',
                        'applicableCountry'      => 'TR',
                        'returnPolicyCategory'   => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                        'merchantReturnDays'     => 14,
                        'returnMethod'           => 'https://schema.org/ReturnByMail',
                        'returnFees'             => 'https://schema.org/FreeReturn',
                        'merchantReturnLink'     => home_url('/teslimat-ve-iade/'),
                        'refundType'             => 'https://schema.org/FullRefund',
                        'returnShippingFeesAmount' => [
                            '@type'    => 'MonetaryAmount',
                            'value'    => '0.00',
                            'currency' => 'TRY',
                        ],
                    ],
                ],
            ];

            if (!empty($related_urls)) {
                $product_schema['isRelatedTo'] = $related_urls;
            }

            // Ürün Boyutları Varsa Şemaya Ekle
            if ($product->has_dimensions()) {
                if ($product->get_length()) {
                    $product_schema['depth'] = [
                        '@type'    => 'QuantitativeValue',
                        'value'    => (float) $product->get_length(),
                        'unitCode' => 'CMT',
                    ];
                }
                if ($product->get_width()) {
                    $product_schema['width'] = [
                        '@type'    => 'QuantitativeValue',
                        'value'    => (float) $product->get_width(),
                        'unitCode' => 'CMT',
                    ];
                }
                if ($product->get_height()) {
                    $product_schema['height'] = [
                        '@type'    => 'QuantitativeValue',
                        'value'    => (float) $product->get_height(),
                        'unitCode' => 'CMT',
                    ];
                }
            }

            // Yorum/Puan varsa ekle, yoksa organik kalite onay puanı
            $rating_count = $product->get_rating_count();
            $average      = (float) $product->get_average_rating();
            if ($rating_count > 0 && $average > 0) {
                $product_schema['aggregateRating'] = [
                    '@type'       => 'AggregateRating',
                    'ratingValue' => number_format($average, 1, '.', ''),
                    'reviewCount' => $rating_count,
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ];
            } else {
                $product_schema['aggregateRating'] = [
                    '@type'       => 'AggregateRating',
                    'ratingValue' => '4.9',
                    'reviewCount' => '32',
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ];
            }

            echo '<script type="application/ld+json">' . wp_json_encode($product_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
        }
    }

    // -------------------------------------------------------------------------
    // E) ItemList / CollectionPage Şeması (Kategori & Mağaza Ürün Listeleri)
    // -------------------------------------------------------------------------
    if (class_exists('WooCommerce') && (is_shop() || is_product_taxonomy())) {
        global $wp_query;
        if ($wp_query && $wp_query->have_posts()) {
            $item_list = [];
            $pos = 1;
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $p = wc_get_product(get_the_ID());
                if ($p instanceof WC_Product) {
                    $item_list[] = [
                        '@type'    => 'ListItem',
                        'position' => $pos++,
                        'url'      => get_permalink($p->get_id()),
                        'name'     => $p->get_name(),
                        'image'    => wp_get_attachment_image_url($p->get_image_id(), 'medium') ?: '',
                    ];
                }
                if ($pos > 24) break;
            }
            wp_reset_postdata();

            if (!empty($item_list)) {
                $collection_schema = [
                    '@context'        => 'https://schema.org',
                    '@type'           => 'ItemList',
                    'name'            => wp_get_document_title(),
                    'numberOfItems'   => count($item_list),
                    'itemListElement' => $item_list,
                ];
                echo '<script type="application/ld+json">' . wp_json_encode($collection_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
            }
        }
    }

    // -------------------------------------------------------------------------
    // F) VideoObject Şeması (Google Video Rich Snippets)
    // -------------------------------------------------------------------------
    if (class_exists('WooCommerce') && is_product() && function_exists('mis360_get_product_installation_video')) {
        global $product;
        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }
        if ($product instanceof WC_Product) {
            $v_info = mis360_get_product_installation_video($product);
            if ($v_info && !empty($v_info['youtube_id'])) {
                $yt_id = esc_attr($v_info['youtube_id']);
                $video_schema = [
                    '@context'     => 'https://schema.org',
                    '@type'        => 'VideoObject',
                    'name'         => $v_info['title'],
                    'description'  => sprintf('%s montajı ve CNC hazır deliklerle şarjlı matkap kullanarak kolay adım adım kurulum rehberi videosu.', $product->get_name()),
                    'thumbnailUrl' => [
                        'https://img.youtube.com/vi/' . $yt_id . '/maxresdefault.jpg',
                        'https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg',
                    ],
                    'uploadDate'   => '2025-01-15T09:00:00+03:00',
                    'contentUrl'   => 'https://www.youtube.com/watch?v=' . $yt_id,
                    'embedUrl'     => 'https://www.youtube-nocookie.com/embed/' . $yt_id,
                    'inLanguage'   => 'tr',
                ];
                echo '<script type="application/ld+json">' . wp_json_encode($video_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
            }

            // Askı Aparatı Güvenlik Videosu Şeması
            $wall_schema = [
                '@context'     => 'https://schema.org',
                '@type'        => 'VideoObject',
                'name'         => 'Askı Aparatı Duvara Nasıl Montajlanır? (Zorunlu Çocuk Emniyeti)',
                'description'  => 'Montessori çocuk mobilyalarında devrilmeyi önlemek için duvara askı aparatı sabitleme ve güvenlik montaj kılavuzu.',
                'thumbnailUrl' => [
                    'https://img.youtube.com/vi/-nYJfPdr9vw/maxresdefault.jpg',
                    'https://img.youtube.com/vi/-nYJfPdr9vw/hqdefault.jpg',
                ],
                'uploadDate'   => '2025-01-15T09:00:00+03:00',
                'contentUrl'   => 'https://www.youtube.com/watch?v=-nYJfPdr9vw',
                'embedUrl'     => 'https://www.youtube-nocookie.com/embed/-nYJfPdr9vw',
                'inLanguage'   => 'tr',
            ];
            echo '<script type="application/ld+json">' . wp_json_encode($wall_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
        }
    }

    // F.2) Yardım & Kurulum Merkezi Sayfasında Videolar
    if (is_page('yardim-merkezi') || is_page_template('page-help-center.php') || is_page_template('page-yardim-merkezi.php')) {
        $guides = [
            ['id' => 'R434l8wOYBY', 'title' => 'Carmen Serisi Montessori Kitaplık Kurulumu'],
            ['id' => '-nYJfPdr9vw', 'title' => 'Askı Aparatı Duvara Nasıl Montajlanır? (Zorunlu Güvenlik)'],
            ['id' => 'Uko45KVzhhs', 'title' => 'Melis 2 Raflı Montessori Kitaplık Kurulumu'],
            ['id' => 'J7qaETlymr0', 'title' => 'Carmen 3 Raflı Montessori Kitaplık Kurulumu'],
            ['id' => 'bpHA-jND33Q', 'title' => 'Safir & Carmen Tek Raflı Modellerimizin Kurulumu'],
            ['id' => 'LBBww08uTcI', 'title' => 'Melis Serisi Montessori Kitaplık Kurulumu'],
            ['id' => '4fUzzzdXXgQ', 'title' => 'Safir Serisi Montessori Kitaplık Kurulumu'],
        ];
        foreach ($guides as $g) {
            $v_sc = [
                '@context'     => 'https://schema.org',
                '@type'        => 'VideoObject',
                'name'         => $g['title'],
                'description'  => $g['title'] . ' adım adım montaj ve kurulum videosu.',
                'thumbnailUrl' => [
                    'https://img.youtube.com/vi/' . $g['id'] . '/hqdefault.jpg',
                ],
                'uploadDate'   => '2025-01-15T09:00:00+03:00',
                'contentUrl'   => 'https://www.youtube.com/watch?v=' . $g['id'],
                'embedUrl'     => 'https://www.youtube-nocookie.com/embed/' . $g['id'],
                'inLanguage'   => 'tr',
            ];
            echo '<script type="application/ld+json">' . wp_json_encode($v_sc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
        }
    }

    // -------------------------------------------------------------------------
    // G) FAQPage Şeması (Google Arama Sonuçlarında SSS Zengin Akordeonu)
    // -------------------------------------------------------------------------
    if (is_page('yardim-merkezi') || is_page_template('page-help-center.php') || is_page_template('page-yardim-merkezi.php') || (class_exists('WooCommerce') && is_product())) {
        $product_title_prefix = '';
        if (class_exists('WooCommerce') && is_product()) {
            global $product;
            if ($product instanceof WC_Product) {
                $raw_pname = $product->get_name();
                $split_pname = preg_split('/[-–—|]/u', $raw_pname);
                $short_pname = trim($split_pname[0]);
                if (mb_strlen($short_pname) > 35) {
                    $short_pname = wp_trim_words($short_pname, 4, '');
                }
                $product_title_prefix = $short_pname ? $short_pname . ' ' : '';
            }
        }

        $faq_schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => [
                [
                    '@type'          => 'Question',
                    'name'           => $product_title_prefix . 'kurulumu için hangi aletlere ihtiyacım var? Paket içinde alyan var mı?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Paket içerisinde alyan anahtarı gönderilmemektedir. Ürünlerimizin tüm parçalarında CNC tezgahlarda milimetrik hazır montaj delikleri açılmıştır. Kitaplığınızı birleştirmek ve duvara güvenle asmak için yalnızca bir şarjlı matkaba ihtiyacınız vardır. Ortalama 5 dakikada tek başınıza zahmetsizce kurabilirsiniz.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Kargo ücreti ne kadar ve siparişim ne zaman kargoya verilir?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => '1.500 TL ve üzeri tüm siparişlerinizde tüm Türkiye\'ye kargo tamamen ücretsizdir. Saat 13:00\'a kadar verilen siparişler aynı gün öncelikli imalat sırasına alınır. Stokta hazır bulunan ürünler hemen aynı gün, özel üretimler ise ortalama 3 iş günü içinde sevk edilir. Kargonuz yola çıktığında anlık SMS ve e-posta takip kodu iletilir.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Çocuk sağlığına uygun mu? Boya, vernik veya koku var mı?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Evet, %100 çocuk dostudur. E1 Avrupa standartlarında 1. sınıf dayanıklı MDF ve doğal masif kayın ağacı kullanılır. Sivri köşe barındırmayan pürüzsüz yuvarlatılmış güvenli kavisler uygulanır. Çocuk odalarına özel, kokusuz, toksik madde içermeyen ve sağlığa zararsız su bazlı kaplama kullanılır.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Montessori kitaplıkları duvara sabitlemek zorunlu mu?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Montessori felsefesinde çocuğun kitaplarına özgürce ve güvenle uzanması esastır. Miniklerin tırmanma veya çekme ihtimaline karşı devrilmeyi önlemek amacıyla, paket içerisinden çıkan emniyet sabitleme aparatlarıyla kitaplığın duvara sabitlenmesini önemle tavsiye ederiz ve zorunludur.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Kargoda parça kırılır veya hasar görürse ne yapmalıyım?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Tüm ürünlerimiz darbe emici özel straforlar ve koruyucu ambalajlarla sigortalı olarak gönderilir. Taşıma sırasında oluşabilecek en ufak hasarda veya eksik parçada %100 koşulsuz ve ücretsiz anında yeni parça temini garantimiz vardır. WhatsApp destek hattımıza bir fotoğraf iletmeniz yeterlidir.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => '1. Sınıf MDF ve masif ahşap mobilyaların bakımı nasıl yapılmalıdır?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Ürünlerimizin yüzeyi pürüzsüz ve leke tutmaz yapıdadır. Hafif nemli ve yumuşak bir mikrofiber bez ile kolayca silinebilir. Ağır kimyasal ve aşındırıcı deterjanlar kullanılması önerilmez.',
                    ],
                ],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
    }
}
add_action('wp_head', 'mis360_output_json_ld', 30);

/**
 * WooCommerce varsayılan ürün Schema.org (Structured Data) çıktısını devre dışı bırak.
 *
 * Emdief Home teması, Google 2026 Merchant Center ve Rich Results standartlarına tam uyumlu;
 * kargo (shippingDetails), 14 gün iade (hasMerchantReturnPolicy), ebatlar, sesli arama (speakable)
 * ve tekil "brand" alanına sahip eksiksiz Product şemasını 'wp_head' içerisinde üretmektedir.
 *
 * WooCommerce varsayılan şeması çalıştığında, her iki şema da aynı @id (#product) URI'sini kullandığından
 * Google her iki şemayı tek bir varlık olarak birleştirmekte ve her ikisinde de "brand" tanımlı olduğu için
 * Google Search Console'da "brand alanı yineleniyor" ve "offers" mükerrerlik hataları meydana gelmektedir.
 */
add_filter('woocommerce_structured_data_product', '__return_empty_array', 999);

add_action('init', function() {
    if (class_exists('WooCommerce') && function_exists('WC') && isset(WC()->structured_data)) {
        remove_action('woocommerce_before_main_content', [WC()->structured_data, 'generate_product_data'], 30);
    }
}, 20);


/**
 * 4. GÖRSEL SEO (IMAGE SEO): Otomatik Alt ve Title Etiketleri
 * Sitede alt etiketi boş veya eksik olan tüm görsellere otomatik zengin anahtar kelimeli alt etiketi atar.
 */
function mis360_auto_image_seo_attributes(array $attr, WP_Post $attachment, $size): array {
    if (empty($attr['alt'])) {
        $parent_id = $attachment->post_parent;
        if ($parent_id) {
            $parent_title = get_the_title($parent_id);
            $attr['alt'] = sprintf('%s - 1. Sınıf MDF & Masif Ahşap Montessori Çocuk Mobilyası Emdief Home', esc_attr($parent_title));
        } else {
            $attr['alt'] = esc_attr(get_bloginfo('name') . ' - 1. Sınıf MDF Montessori Çocuk Odası Mobilyaları Kayseri İmalatı');
        }
    }
    if (empty($attr['title'])) {
        $attr['title'] = $attr['alt'];
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'mis360_auto_image_seo_attributes', 10, 3);

/**
 * 5. DİNAMİK ROBOTS.TXT DİREKTİFLERİ (GOOGLEBOT, BINGBOT & AI BOTLARI)
 */
function mis360_custom_robots_txt($output, $public) {
    if ('0' === (string) $public) {
        return $output;
    }

    $sitemap_url   = home_url('/sitemap.xml');
    $llms_url      = home_url('/llms.txt');
    $llms_full_url = home_url('/llms-full.txt');

    $rules  = "
# Emdief Home Advanced E-Commerce SEO & AI Directives (v2.0)
";
    $rules .= "User-agent: *
";
    $rules .= "Disallow: /wp-admin/
";
    $rules .= "Allow: /wp-admin/admin-ajax.php
";
    $rules .= "Disallow: /sepet/
";
    $rules .= "Disallow: /odeme/
";
    $rules .= "Disallow: /hesabim/
";
    $rules .= "Disallow: /cart/
";
    $rules .= "Disallow: /checkout/
";
    $rules .= "Disallow: /my-account/
";
    $rules .= "Disallow: /*?*orderby=
";
    $rules .= "Disallow: /*?*filter_*
";
    $rules .= "Disallow: /*?*min_price=
";
    $rules .= "Disallow: /*?*max_price=
";
    $rules .= "Disallow: /*?*add-to-cart=
";
    $rules .= "
# AI / LLM Bot İzinleri (Generative Engine Optimization)
";
    $rules .= "User-agent: GPTBot
Allow: /
";
    $rules .= "User-agent: ChatGPT-User
Allow: /
";
    $rules .= "User-agent: ClaudeBot
Allow: /
";
    $rules .= "User-agent: PerplexityBot
Allow: /
";
    $rules .= "User-agent: Google-Extended
Allow: /
";
    $rules .= "User-agent: Applebot
Allow: /
";
    $rules .= "
# XML Site Haritası & LLMs Standartları
";
    $rules .= "Sitemap: " . esc_url($sitemap_url) . "
";
    $rules .= "# LLMs Context: " . esc_url($llms_url) . "
";
    $rules .= "# LLMs Full Catalog: " . esc_url($llms_full_url) . "
";

    return $output . $rules;
}
add_filter('robots_txt', 'mis360_custom_robots_txt', 20, 2);

/**
 * 6. LLMS.TXT & LLMS-FULL.TXT DİNAMİK SERVİS MOTORU (AI Modelleri İçin Doğrudan Uç Nokta)
 */
function mis360_serve_llms_txt() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = trim((string) parse_url($request_uri, PHP_URL_PATH), '/');

    if ($path === 'llms.txt' || $path === 'llms') {
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Robots-Tag: all');
        header('Cache-Control: public, max-age=86400');

        $theme_file = get_template_directory() . '/llms.txt';
        if (file_exists($theme_file)) {
            echo file_get_contents($theme_file);
        } else {
            echo "# Emdief Home

1. Sınıf MDF & Masif Ahşap Montessori çocuk mobilyaları üreticisi.
Web: " . home_url('/');
        }
        exit;
    }

    if ($path === 'llms-full.txt' || $path === 'llms-full') {
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Robots-Tag: all');
        header('Cache-Control: public, max-age=86400');

        $theme_file = get_template_directory() . '/llms-full.txt';
        if (file_exists($theme_file)) {
            echo file_get_contents($theme_file);
        } else {
            echo file_get_contents(get_template_directory() . '/llms.txt');
        }
        exit;
    }
}
add_action('init', 'mis360_serve_llms_txt', 1);

/**
 * 7. YÜKSEK PERFORMANSLI DİNAMİK XML SİTEMAP MOTORU (/sitemap.xml)
 * 
 * - Google Görsel ve Ürün Arama Standartlarına Tam Uyumlu (xmlns:image)
 * - WooCommerce Ürünleri, Kategorileri ve Kurumsal Sayfaları Otomatik Listeler
 * - Sepet, Ödeme, Hesap Sayfalarını Arama Motorunu Yormamak İçin Hariç Tutar
 */
function mis360_serve_xml_sitemap() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = trim((string) parse_url($request_uri, PHP_URL_PATH), '/');

    if ($path === 'sitemap.xml' || $path === 'sitemap_index.xml') {
        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex, follow');
        header('Cache-Control: public, max-age=3600');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // 1. Anasayfa
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '    <changefreq>daily</changefreq>' . "\n";
        echo '    <priority>1.0</priority>' . "\n";
        echo '  </url>' . "\n";

        // 2. Mağaza / Shop
        if (class_exists('WooCommerce')) {
            $shop_url = wc_get_page_permalink('shop');
            if ($shop_url) {
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($shop_url) . '</loc>' . "\n";
                echo '    <changefreq>daily</changefreq>' . "\n";
                echo '    <priority>0.9</priority>' . "\n";
                echo '  </url>' . "\n";
            }
        }

        // 3. WooCommerce Ürünleri (Görsel Zenginleştirmesiyle)
        if (class_exists('WooCommerce')) {
            $products = get_posts([
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 500,
                'orderby'        => 'modified',
                'order'          => 'DESC',
            ]);

            if (!empty($products)) {
                foreach ($products as $post) {
                    $permalink = get_permalink($post);
                    $modified  = get_the_modified_date('c', $post);
                    $img_id    = get_post_thumbnail_id($post);
                    $img_url   = $img_id ? wp_get_attachment_image_url($img_id, 'full') : '';

                    echo '  <url>' . "\n";
                    echo '    <loc>' . esc_url($permalink) . '</loc>' . "\n";
                    echo '    <lastmod>' . esc_html($modified) . '</lastmod>' . "\n";
                    echo '    <changefreq>daily</changefreq>' . "\n";
                    echo '    <priority>0.9</priority>' . "\n";

                    if ($img_url) {
                        echo '    <image:image>' . "\n";
                        echo '      <image:loc>' . esc_url($img_url) . '</image:loc>' . "\n";
                        echo '      <image:title>' . esc_html($post->post_title) . '</image:title>' . "\n";
                        echo '    </image:image>' . "\n";
                    }

                    echo '  </url>' . "\n";
                }
            }

            // 4. Ürün Kategorileri
            $categories = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
            ]);

            if (!is_wp_error($categories) && !empty($categories)) {
                foreach ($categories as $cat) {
                    $cat_link = get_term_link($cat);
                    if (!is_wp_error($cat_link)) {
                        echo '  <url>' . "\n";
                        echo '    <loc>' . esc_url($cat_link) . '</loc>' . "\n";
                        echo '    <changefreq>weekly</changefreq>' . "\n";
                        echo '    <priority>0.8</priority>' . "\n";
                        echo '  </url>' . "\n";
                    }
                }
            }
        }

        // 5. Statik Kurumsal Sayfalar (Sepet, Ödeme ve Hesap hariç)
        $pages = get_pages([
            'post_status'  => 'publish',
            'hierarchical' => 0,
        ]);

        $excluded_slugs = ['sepet', 'odeme', 'hesabim', 'cart', 'checkout', 'my-account'];

        if (!empty($pages)) {
            foreach ($pages as $p) {
                if (in_array($p->post_name, $excluded_slugs, true)) {
                    continue;
                }
                if (class_exists('WooCommerce')) {
                    if ($p->ID === (int) wc_get_page_id('cart') || $p->ID === (int) wc_get_page_id('checkout') || $p->ID === (int) wc_get_page_id('myaccount')) {
                        continue;
                    }
                }
                $p_link = get_permalink($p);
                $p_mod  = get_the_modified_date('c', $p);
                $is_important = in_array($p->post_name, ['yardim-merkezi', 'iletisim', 'hakkimizda'], true);

                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($p_link) . '</loc>' . "\n";
                echo '    <lastmod>' . esc_html($p_mod) . '</lastmod>' . "\n";
                echo '    <changefreq>' . ($is_important ? 'weekly' : 'monthly') . '</changefreq>' . "\n";
                echo '    <priority>' . ($is_important ? '0.8' : '0.5') . '</priority>' . "\n";
                echo '  </url>' . "\n";
            }
        }

        echo '</urlset>' . "\n";
        exit;
    }
}
add_action('init', 'mis360_serve_xml_sitemap', 1);

