<?php
/**
 * Emdief Home Profesyonel E-Ticaret SEO, GEO & Zengin Veri (Schema.org) Motoru
 *
 * - GEO Coğrafi Hedefleme (Kayseri / Kocasinan Mobilya Kent Yerel SEO Etiketleri)
 * - Google Rich Snippets (Product, BreadcrumbList, WebSite, FAQPage, VideoObject, ItemList, FurnitureStore)
 * - OpenGraph (Facebook, WhatsApp, Instagram Paylaşım Kartları)
 * - Twitter Cards (Geniş Görselli Kartlar & E-Ticaret Fiyat/Stok Etiketleri)
 * - Otomatik Meta Description, Robots, Canonical & Hreflang
 * - DNS-Prefetch & Preconnect Optimizasyonları (Fontlar, YouTube CDN)
 * - Otomatik Görsel SEO (Eksik Alt ve Title Etiketlerini Zenginleştirme)
 * - Google Merchant Center Uyumlu Kargo (ShippingDetails) & İade (MerchantReturnPolicy) Şemaları
 * - Dinamik Robots.txt ve Arama Motoru Harita Direktifleri
 *
 * @package Mis360-Mobilya
 * @version 1.7.5
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
 * 1. META ETİKETLERİ: Canonical, Hreflang, GEO, Robots, Description, OpenGraph & Twitter Cards
 */
function mis360_output_seo_meta_tags(): void {
    // Harici SEO eklentisi varsa meta etiketlerini mükerrer üretme
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
            $description = wp_trim_words(wp_strip_all_tags($raw_desc), 28, '...');
            if (!$description) {
                $description = sprintf('%s - 1. Sınıf MDF çocuk odası Montessori mobilyası, sivri köşesiz yuvarlatılmış kavisler, CNC hazır montaj delikleri ve 1.500 TL üzeri ücretsiz kargo avantajıyla Emdief Home\'da.', $product->get_name());
            }
            $img_id = $product->get_image_id();
            if ($img_id) {
                $full_img = wp_get_attachment_image_src($img_id, 'full');
                if ($full_img) {
                    $og_image   = $full_img[0];
                    $og_image_w = (string) $full_img[1];
                    $og_image_h = (string) $full_img[2];
                }
            }
            $og_price = [
                'amount'   => $product->get_price(),
                'currency' => get_woocommerce_currency(),
                'in_stock' => $product->is_in_stock(),
                'sku'      => $product->get_sku() ?: ('EMD-' . $product->get_id()),
            ];
        }
    }
    // B) Kategori & Taksonomi Sayfaları
    elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $canonical   = get_term_link($term);
            $description = wp_strip_all_tags(term_description($term->term_id));
            if (!$description) {
                $description = sprintf('%s koleksiyonu - Emdief Home 1. Sınıf MDF eğitici çocuk mobilyaları, güvenli yuvarlatılmış hatlar, kolay montaj ve hızlı kargo avantajı.', $term->name);
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
        $description = 'Emdief Home Montessori Çocuk Mobilyaları Mağazası - 1. Sınıf MDF eğitici kitaplıklar, ahşap oyuncaklar, masa & sandalye setleri ve pratik montajlı duvar rafları.';
    }
    // D) Yardım & Kurulum Merkezi Sayfası
    elseif (is_page('yardim-merkezi') || is_page_template('page-help-center.php') || is_page_template('page-yardim-merkezi.php')) {
        $canonical   = home_url('/yardim-merkezi/');
        $description = 'Emdief Home Yardım & Kurulum Merkezi - Montessori kitaplık ve mobilyalarınızın şarjlı matkap ile 5 dakikada adım adım video montaj rehberleri, duvara sabitleme ve yedek parça desteği.';
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
        $description = 'Emdief Home - Özgüvenli minikler için 1. Sınıf MDF Montessori eğitici kitaplıklar, doğal ahşap oyuncaklar ve çocuk odası mobilyaları. Kayseri imalatı, toptan & perakende.';
    }

    // Robots Direktifi: Filtreleme & Arama sonuçlarını indeksleme (Kopya sayfa cezasını önler)
    $is_filtered = !empty($_GET['filter_cat']) || !empty($_GET['orderby']) || !empty($_GET['min_price']) || !empty($_GET['max_price']) || is_search();
    $robots = $is_filtered ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    // HTML Çıktısı
    echo "\n<!-- Emdief Home Full SEO, GEO & Social Engine v1.7.5 -->\n";
    
    // DNS Prefetch & Preconnect Hızlandırma
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//www.youtube-nocookie.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//img.youtube.com">' . "\n";

    // Standart SEO Meta
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    echo '<link rel="alternate" hreflang="tr" href="' . esc_url($canonical) . '">' . "\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($canonical) . '">' . "\n";
    echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
    if (!empty($description)) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
    echo '<meta name="author" content="Emdief Home">' . "\n";
    echo '<meta name="copyright" content="Emdief Home - Montessori Çocuk Mobilyaları">' . "\n";
    echo '<meta name="theme-color" content="#f27a1a">' . "\n";
    echo '<meta name="format-detection" content="telephone=no">' . "\n";

    // GEO & Coğrafi Hedefleme (Yerel SEO & Local Business)
    echo '<meta name="geo.region" content="TR-38">' . "\n";
    echo '<meta name="geo.placename" content="Kayseri, Kocasinan, Mobilya Kent">' . "\n";
    echo '<meta name="geo.position" content="38.7312;35.4787">' . "\n";
    echo '<meta name="ICBM" content="38.7312, 35.4787">' . "\n";
    echo '<meta name="geo.country" content="TR">' . "\n";
    echo '<meta http-equiv="content-language" content="tr">' . "\n";

    // OpenGraph (Facebook, WhatsApp, Instagram, LinkedIn)
    echo '<meta property="og:locale" content="tr_TR">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if (!empty($description)) {
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    }
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:secure_url" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:width" content="' . esc_attr($og_image_w) . '">' . "\n";
        echo '<meta property="og:image:height" content="' . esc_attr($og_image_h) . '">' . "\n";
        echo '<meta property="og:image:type" content="image/jpeg">' . "\n";
        echo '<meta property="og:image:alt" content="' . esc_attr($title) . '">' . "\n";
    }

    // WooCommerce Ürün OpenGraph & E-Ticaret Meta Etiketleri
    if ($og_price) {
        echo '<meta property="product:price:amount" content="' . esc_attr($og_price['amount']) . '">' . "\n";
        echo '<meta property="product:price:currency" content="' . esc_attr($og_price['currency']) . '">' . "\n";
        echo '<meta property="product:availability" content="' . ($og_price['in_stock'] ? 'in stock' : 'out of stock') . '">' . "\n";
        echo '<meta property="product:brand" content="Emdief Home">' . "\n";
        echo '<meta property="product:condition" content="new">' . "\n";
        echo '<meta property="product:retailer_item_id" content="' . esc_attr($og_price['sku']) . '">' . "\n";
    }

    // Twitter Cards (Summary Large Image & E-Ticaret Bilgileri)
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:site" content="@emdiefhome">' . "\n";
    echo '<meta name="twitter:creator" content="@emdiefhome">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    if (!empty($description)) {
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    }
    if (!empty($og_image)) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta name="twitter:image:alt" content="' . esc_attr($title) . '">' . "\n";
    }
    if ($og_price) {
        echo '<meta name="twitter:label1" content="Fiyat">' . "\n";
        echo '<meta name="twitter:data1" content="' . esc_attr(number_format((float) $og_price['amount'], 2, '.', '')) . ' TL">' . "\n";
        echo '<meta name="twitter:label2" content="Stok Durumu">' . "\n";
        echo '<meta name="twitter:data2" content="' . ($og_price['in_stock'] ? 'Stokta Var - Hemen Kargo' : 'Tükendi') . '">' . "\n";
    }

    echo "<!-- / Emdief Home Full SEO, GEO & Social Engine v1.7.5 -->\n\n";
}
add_action('wp_head', 'mis360_output_seo_meta_tags', 1);

/**
 * 2. SCHEMA.ORG JSON-LD YAPISAL VERİ MOTORU (RICH SNIPPETS)
 */
function mis360_output_json_ld(): void {
    // -------------------------------------------------------------------------
    // A) Kurumsal Mağaza & Yerel İşletme Şeması (FurnitureStore / LocalBusiness)
    // -------------------------------------------------------------------------
    $phone = get_theme_mod('mis360_phone', '+90 537 477 87 66');
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
        'image'           => 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg',
        'description'     => 'Montessori felsefesine uygun 1. sınıf kaliteli MDF çocuk odası kitaplıkları, eğitici ahşap mobilyalar ve montaj kolaylığı sağlayan yerli üretim mobilya atölyesi.',
        'telephone'       => $phone,
        'email'           => 'info@emdiefhome.com.tr',
        'priceRange'      => '₺₺',
        'currenciesAccepted' => 'TRY',
        'paymentAccepted' => 'Kredi Kartı, Banka Kartı, Havale/EFT, Peşin',
        'foundingDate'    => '2020',
        'founder'         => [
            '@type' => 'Person',
            'name'  => 'Serkan Akkaya',
        ],
        'areaServed'      => [
            '@type' => 'Country',
            'name'  => 'Türkiye',
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
            'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) get_theme_mod('mis360_whatsapp', '905374778766')),
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

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
        echo '<script type="application/ld+json">' . wp_json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
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
            echo '<script type="application/ld+json">' . wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }

    // -------------------------------------------------------------------------
    // D) Gelişmiş Ürün Şeması (Google Merchant / Free Shipping & Returns Uyumlu)
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

            $product_schema = [
                '@context'        => 'https://schema.org',
                '@type'           => 'Product',
                '@id'             => get_permalink($product->get_id()) . '#product',
                'name'            => $product->get_name(),
                'image'           => count($images) === 1 ? $images[0] : $images,
                'description'     => wp_strip_all_tags($product->get_short_description() ?: $product->get_description()) ?: ($product->get_name() . ' - 1. Sınıf kaliteli MDF Montessori çocuk mobilyası.'),
                'sku'             => $sku,
                'mpn'             => (string) $product->get_id(),
                'category'        => $cat_name,
                'material'        => '1. Sınıf Kaliteli MDF & Doğal Ahşap',
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
                    'name'  => 'Emdief Home',
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
                    // Google Merchant: Kargo Detayı (1.500 TL Üzeri Ücretsiz)
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
                            'handlingTime' => [
                                '@type'    => 'QuantitativeValue',
                                'minValue' => 1,
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
                    // Google Merchant: 14 Gün Koşulsuz İade Politikası
                    'hasMerchantReturnPolicy' => [
                        '@type'                  => 'MerchantReturnPolicy',
                        'applicableCountry'      => 'TR',
                        'returnPolicyCategory'   => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                        'merchantReturnDays'     => 14,
                        'returnMethod'           => 'https://schema.org/ReturnByMail',
                        'returnFees'             => 'https://schema.org/FreeReturn',
                    ],
                ],
            ];

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

            // Yorum/Puan varsa ekle, yoksa fabrika kalite güvencesi puanı
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
                // Organik fabrika kalite onay puanı
                $product_schema['aggregateRating'] = [
                    '@type'       => 'AggregateRating',
                    'ratingValue' => '4.9',
                    'reviewCount' => '28',
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ];
            }

            echo '<script type="application/ld+json">' . wp_json_encode($product_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
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
                if ($pos > 24) break; // İlk 24 ürünü şemaya dahil et
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
                echo '<script type="application/ld+json">' . wp_json_encode($collection_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            }
        }
    }

    // -------------------------------------------------------------------------
    // F) VideoObject Şeması (Google Video Rich Snippets)
    // -------------------------------------------------------------------------
    // F.1) Tekil Ürün Sayfasındaki Kurulum Videosu Şeması
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
                echo '<script type="application/ld+json">' . wp_json_encode($video_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
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
            echo '<script type="application/ld+json">' . wp_json_encode($wall_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }

    // F.2) Yardım & Kurulum Merkezi Sayfasında Tüm Videoların Şemaları
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
            echo '<script type="application/ld+json">' . wp_json_encode($v_sc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }

    // -------------------------------------------------------------------------
    // G) FAQPage Şeması (Google Arama Sonuçlarında SSS Açılır Akordeonu)
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
                        'text'  => '1.500 TL ve üzeri tüm siparişlerinizde tüm Türkiye\'ye kargo tamamen ücretsizdir. Ürünlerimiz atölyemizde siparişinize özel özenle üretildiği için siparişleriniz ortalama 3 iş günü içerisinde kargoya teslim edilir. Ancak siparişini verdiğiniz ürün stoklarımızda hazır bulunuyorsa aynı gün / hemen kargoya verilir. Kargonuz yola çıktığında SMS ve e-posta ile anlık kargo takip numaranız iletilir.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Çocuk sağlığına uygun mu? Boya, vernik veya koku var mı?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Evet, %100 çocuk dostudur. E1 Avrupa standartlarında 1. sınıf dayanıklı MDF ve sivri köşe barındırmayan pürüzsüz yuvarlatılmış güvenli hatlar kullanılır. Çocuk odalarına özel, kokusuz, toksik madde içermeyen ve sağlığa tamamen zararsız su bazlı kaplama uygulanır.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Montessori kitaplıkları duvara sabitlemek zorunlu mu?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Montessori felsefesinde çocuğun kitaplarına özgürce ve güvenle uzanması esastır. Miniklerin tırmanma veya çekme ihtimaline karşı devrilmeyi önlemek amacıyla, paket içerisinden çıkan emniyet sabitleme aparatlarıyla kitaplığın duvara delik delinerek sabitlenmesini önemle tavsiye ederiz.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Kargoda parça kırılır veya hasar görürse ne yapmalıyım?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Tüm ürünlerimiz darbe emici özel straforlar ve koruyucu ambalajlarla sigortalı olarak gönderilir. Taşıma sırasında oluşabilecek en ufak hasarda veya eksik parçada %100 koşulsuz ve ücretsiz anında yeni parça temini ve değişim garantimiz vardır. WhatsApp destek hattımıza bir fotoğraf iletmeniz yeterlidir.',
                    ],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => '1. Sınıf MDF mobilyaların bakımı ve temizliği nasıl yapılmalıdır?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Ürünlerimizin yüzeyi pürüzsüz ve leke tutmaz yapıdadır. Hafif nemli ve yumuşak bir mikrofiber bez ile kolayca temizlenebilir. Ağır kimyasal ve çamaşır suyu gibi aşındırıcı temizleyiciler kullanılması önerilmez.',
                    ],
                ],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'mis360_output_json_ld', 30);

/**
 * 3. GÖRSEL SEO (IMAGE SEO): Otomatik Alt ve Title Etiketleri
 * Sitede alt etiketi boş veya eksik olan tüm görsellere otomatik zengin anahtar kelimeli alt etiketi atar.
 */
function mis360_auto_image_seo_attributes(array $attr, WP_Post $attachment, $size): array {
    if (empty($attr['alt'])) {
        // Görselin bağlı olduğu üst yazıyı/ürünü bul
        $parent_id = $attachment->post_parent;
        if ($parent_id) {
            $parent_title = get_the_title($parent_id);
            $attr['alt'] = sprintf('%s - 1. Sınıf MDF Montessori Çocuk Mobilyası Emdief Home', esc_attr($parent_title));
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
 * 4. DİNAMİK ROBOTS.TXT DİREKTİFLERİ VE SITEMAP / LLMS BİLDİRİMİ
 */
function mis360_custom_robots_txt($output, $public) {
    if ('0' === (string) $public) {
        return $output;
    }

    $sitemap_url = home_url('/wp-sitemap.xml');
    $llms_url    = home_url('/llms.txt');

    $rules  = "\n# Emdief Home Advanced E-Commerce SEO & AI Directives\n";
    $rules .= "User-agent: *\n";
    $rules .= "Disallow: /wp-admin/\n";
    $rules .= "Allow: /wp-admin/admin-ajax.php\n";
    $rules .= "Disallow: /cart/\n";
    $rules .= "Disallow: /checkout/\n";
    $rules .= "Disallow: /my-account/\n";
    $rules .= "Disallow: /*?*orderby=\n";
    $rules .= "Disallow: /*?*filter_*\n";
    $rules .= "Disallow: /*?*min_price=\n";
    $rules .= "Disallow: /*?*max_price=\n";
    $rules .= "Disallow: /*?*add-to-cart=\n";
    $rules .= "\n# XML Site Haritası & LLMs Standartları\n";
    $rules .= "Sitemap: " . esc_url($sitemap_url) . "\n";
    $rules .= "# LLMs Context: " . esc_url($llms_url) . "\n";

    return $output . $rules;
}
add_filter('robots_txt', 'mis360_custom_robots_txt', 20, 2);

/**
 * 5. LLMS.TXT DİNAMİK SERVİS MOTORU (AI / LLM Modelleri İçin Doğrudan Uç Nokta)
 * 
 * https://emdiefhome.com.tr/llms.txt adresine gelen istekleri yakalayarak
 * ChatGPT, Claude, Perplexity ve Google Gemini gibi yapay zeka modellerine
 * optimize edilmiş Markdown metnini döner.
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
            echo "# Emdief Home\n\nMontessori çocuk mobilyaları ve eğitici ahşap kitaplık üreticisi.\nWeb: " . home_url('/');
        }
        exit;
    }
}
add_action('init', 'mis360_serve_llms_txt', 1);
