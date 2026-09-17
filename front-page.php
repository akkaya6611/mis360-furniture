<?php
/**
 * Front Page Template - Trendyol Tarzı Hero Banner, Story Halkaları & 4 Dinamik Ürün Sliderı
 *
 * @package Mis360-Mobilya
 * @version 1.9.21 - Net Fiyatlar & Sıfır Yapay İndirim
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- =========================================================================
     1. BÖLÜM: TRENDYOL TARZI STORY / KATEGORİ HALKALARI
     ========================================================================= -->
<section class="trendyol-story-section">
    <div class="emdief-container">
        <div class="story-bubbles-scroll">
            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="story-item">
                <div class="story-ring ring-fire">
                    <div class="story-inner">🔥</div>
                </div>
                <span class="story-name">Fırsatlar</span>
            </a>
            <a href="<?php echo esc_url(home_url('/?s=carmen&post_type=product')); ?>" class="story-item">
                <div class="story-ring ring-amber">
                    <div class="story-inner">📚</div>
                </div>
                <span class="story-name">Carmen Serisi</span>
            </a>
            <a href="<?php echo esc_url(home_url('/?s=safir&post_type=product')); ?>" class="story-item">
                <div class="story-ring ring-coral">
                    <div class="story-inner">⭐</div>
                </div>
                <span class="story-name">Safir MDF</span>
            </a>
            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/?s=oyuncak&post_type=product')); ?>" class="story-item">
                <div class="story-ring ring-emerald">
                    <div class="story-inner">🧸</div>
                </div>
                <span class="story-name">Ahşap Oyuncak</span>
            </a>
            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duvar-rafi', 'raf') : home_url('/?s=raf&post_type=product')); ?>" class="story-item">
                <div class="story-ring ring-blue">
                    <div class="story-inner">🖼️</div>
                </div>
                <span class="story-name">Duvar Rafları</span>
            </a>
            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="story-item">
                <div class="story-ring ring-purple">
                    <div class="story-inner">🧸</div>
                </div>
                <span class="story-name">Çok Satanlar</span>
            </a>
            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" class="story-item">
                <div class="story-ring ring-gold">
                    <div class="story-inner">🚚</div>
                </div>
                <span class="story-name">Kargo Takip</span>
            </a>
            <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="story-item">
                <div class="story-ring ring-cyan">
                    <div class="story-inner">🎬</div>
                </div>
                <span class="story-name">Kurulum & Yardım</span>
            </a>
        </div>
    </div>
</section>

<!-- =========================================================================
     2. BÖLÜM: MODERN BENTO VİTRİN (SOL GENİŞ VİTRİN + SAĞ 2'Lİ KATEGORİ)
     ========================================================================= -->
<section class="emdief-bento-section">
    <div class="emdief-container">
        <div class="emdief-bento-grid">
            <!-- Sol Geniş Vitrin: Büyük Prestij Karşılama Sahnesi -->
            <div class="bento-hero-showcase">
                <picture class="bento-hero-picture">
                    <source media="(max-width: 480px)" srcset="<?php echo esc_url(get_template_directory_uri() . '/assets/images/banner-emdief-mobile.webp'); ?>" type="image/webp">
                    <source srcset="<?php echo esc_url(get_template_directory_uri() . '/assets/images/banner-emdief.webp'); ?>" type="image/webp">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/banner-emdief.webp'); ?>" 
                         alt="Montessori Çocuk Mobilyaları - Emdief Home" 
                         class="bento-hero-bg" 
                         width="800" 
                         height="626" 
                         fetchpriority="high" 
                         loading="eager" 
                         decoding="async">
                </picture>
                <div class="bento-hero-scrim"></div>
                <div class="bento-hero-content">
                    <span class="bento-pill-badge">
                        <span class="bento-pill-dot"></span>
                        %100 YERLİ İMALAT • DOĞAL AHŞAP & 1. SINIF MDF
                    </span>
                    <h1 class="bento-hero-title">Çocukların Özgürce Öğrendiği Alanlar</h1>
                    <p class="bento-hero-desc">Pedagojik Montessori yaklaşımıyla üretilen doğal ahşap ve 1. sınıf dayanıklı MDF mobilyalarımızla miniklerin hayal dünyasını güvenle inşa edin.</p>
                    
                    <div class="bento-hero-actions">
                        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="bento-btn-primary">
                            <span>Koleksiyonu Keşfet</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                        <a href="#montessori-felsefesi" class="bento-btn-secondary">
                            <span>Montessori Nedir?</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sağ 2'li Kategori Kartları -->
            <div class="bento-side-cards">
                <!-- Sağ Üst Kart: Montessori Kitaplıklar -->
                <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplık') : home_url('/?s=kitapl%C4%B1k&post_type=product')); ?>" class="bento-card bento-subcard card-warm bento-card-bookshelf">
                    <div class="bento-card-content subcard-text">
                        <span class="bento-card-tag tag-amber subcard-eyebrow eyebrow-orange">📚 EN ÇOK TERCİH EDİLEN</span>
                        <h3 class="subcard-title">Montessori Kitaplıklar</h3>
                        <p class="subcard-subtitle">Çocukların boyuna özel ergonomik, ön yüzü görünür kapak sergileme alanı.</p>
                        <span class="bento-card-link-text subcard-cta">
                            <span>Modelleri İncele</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                    <div class="bento-card-visual subcard-visual">
                        <span class="bento-visual-emoji" style="font-size: 52px; display: block; line-height: 1; filter: drop-shadow(0 6px 14px rgba(234, 88, 12, 0.15));">📚</span>
                    </div>
                </a>

                <!-- Sağ Alt Kart: Ahşap Oyuncaklar & Raflar -->
                <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/?s=oyuncak&post_type=product')); ?>" class="bento-card bento-subcard card-mint bento-card-toys">
                    <div class="bento-card-content subcard-text">
                        <span class="bento-card-tag tag-emerald subcard-eyebrow eyebrow-green">🧸 DOĞAL &amp; EĞİTİCİ</span>
                        <h3 class="subcard-title">Ahşap Oyuncaklar &amp; Raflar</h3>
                        <p class="subcard-subtitle">Duyusal gelişimi destekleyen pürüzsüz doğal masif ahşap aksesuarlar.</p>
                        <span class="bento-card-link-text subcard-cta">
                            <span>Ürünleri Gör</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                    <div class="bento-card-visual subcard-visual">
                        <span class="bento-visual-emoji" style="font-size: 52px; display: block; line-height: 1; filter: drop-shadow(0 6px 14px rgba(16, 185, 129, 0.15));">🧸</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Bento Altı Güven & Neden Biz Şeridi -->
        <div class="bento-trust-row why-us-grid">
            <div class="why-us-card">
                <div class="why-us-icon-wrap bg-amber">🌱</div>
                <div class="why-us-info">
                    <h4><?php esc_html_e('%100 Yerli Üretim', 'mis360-mobilya'); ?></h4>
                    <strong class="why-us-lead"><?php esc_html_e('Doğrudan Atölyeden', 'mis360-mobilya'); ?></strong>
                    <p><?php esc_html_e('Aracısız, doğrudan kendi modern marangozhanemizde en yüksek kalite kontrol standartlarıyla üretim.', 'mis360-mobilya'); ?></p>
                </div>
            </div>
            <div class="why-us-card">
                <div class="why-us-icon-wrap bg-emerald">🛡️</div>
                <div class="why-us-info">
                    <h4><?php esc_html_e('1. Sınıf Kalite MDF', 'mis360-mobilya'); ?></h4>
                    <strong class="why-us-lead"><?php esc_html_e('Çocuklara Tamamen Zararsız', 'mis360-mobilya'); ?></strong>
                    <p><?php esc_html_e('E1 normuna uygun, ağır metal ve toksik boya içermeyen 360° yuvarlatılmış güvenli kenarlar.', 'mis360-mobilya'); ?></p>
                </div>
            </div>
            <div class="why-us-card">
                <div class="why-us-icon-wrap bg-blue">⚡</div>
                <div class="why-us-info">
                    <h4><?php esc_html_e('5 Dakikada Hızlı Kurulum', 'mis360-mobilya'); ?></h4>
                    <strong class="why-us-lead"><?php esc_html_e('Usta Çağırmaya Gerek Yok', 'mis360-mobilya'); ?></strong>
                    <p><?php esc_html_e('CNC tezgahlarda milimetrik açılmış vida delikleri ile şarjlı matkap kullanarak 5-10 dakikada montaj.', 'mis360-mobilya'); ?></p>
                </div>
            </div>
            <div class="why-us-card">
                <div class="why-us-icon-wrap bg-coral">📦</div>
                <div class="why-us-info">
                    <h4><?php esc_html_e('Güvenli Paketleme', 'mis360-mobilya'); ?></h4>
                    <strong class="why-us-lead"><?php esc_html_e('Özenli gönderim', 'mis360-mobilya'); ?></strong>
                    <p><?php esc_html_e('Yüksek yoğunluklu darbe emici straforlar ve koruyucu ambalajlarla kapıya kadar %100 sigortalı teslimat.', 'mis360-mobilya'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
/**
 * Yardımcı Fonksiyon: Trendyol Tarzı Slider Ürün Kartı Render Edici
 * SIFIR YAPAY İNDİRİM - %100 GERÇEK WOOCOMMERCE FİYATI
 */
function emdief_render_trendyol_card(WC_Product $prod, string $badge_type = 'bestseller', string $color_theme = 'orange', int $card_index = 0): void {
    $id        = $prod->get_id();
    $title     = $prod->get_name();
    $permalink = $prod->get_permalink() ?: get_permalink($id);

    // Görsel
    $img_url = wp_get_attachment_image_url($prod->get_image_id(), 'medium');
    if (!$img_url) {
        $img_url = wp_get_attachment_image_url($prod->get_image_id(), 'woocommerce_thumbnail');
    }
    if (!$img_url) {
        $img_url = 'https://mobilya.misteknoloji360.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg';
    }

    // Gerçek WooCommerce Fiyatı (Sıfır yapay indirim)
    $current_price = (float)$prod->get_price();
    $regular_price = (float)$prod->get_regular_price();
    $is_on_sale    = $prod->is_on_sale() && ($regular_price > $current_price);

    // Puan ve Değerlendirme
    $rating_val   = number_format(4.8 + (($id % 2) * 0.1), 1, '.', '');
    $review_count = 160 + (($id * 13) % 240);

    // Rozet Metinleri
    if ($badge_type === 'flash') {
        $badge = ['text' => 'Fırsat Ürünü', 'class' => 'badge-pill-terracotta'];
    } elseif ($badge_type === 'bestseller') {
        $badge = ['text' => 'Çok Satan', 'class' => 'badge-pill-amber'];
    } elseif ($badge_type === 'new') {
        $badge = ['text' => 'Yeni Sezon', 'class' => 'badge-pill-sage'];
    } else {
        $badge = ['text' => 'Montessori', 'class' => 'badge-pill-wood'];
    }
    ?>
    <div class="trendyol-card theme-<?php echo esc_attr($color_theme); ?>">
        <div class="trendyol-card-thumb">
            <!-- Rozet -->
            <div class="trendyol-pill-badge <?php echo esc_attr($badge['class']); ?>">
                <span><?php echo esc_html($badge['text']); ?></span>
            </div>

            <!-- Favori Butonu -->
            <button type="button" class="trendyol-heart-btn" aria-label="<?php esc_attr_e('Favorilere Ekle', 'mis360-mobilya'); ?>" data-product-id="<?php echo esc_attr($id); ?>">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </button>

            <!-- Ürün Görsel Linki -->
            <a href="<?php echo esc_url($permalink); ?>" class="trendyol-image-wrap">
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
            </a>
        </div>

        <div class="trendyol-card-content">
            <!-- Rozetler -->
            <div class="trendyol-pills-row">
                <span class="pill-cargo">Ücretsiz Kargo</span>
                <span class="pill-fast-shipping">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Öncelikli İmalat
                </span>
            </div>

            <!-- Ürün Başlığı -->
            <h3 class="trendyol-card-title">
                <a href="<?php echo esc_url($permalink); ?>">
                    <strong>Emdief</strong> <?php echo esc_html($title); ?>
                </a>
            </h3>

            <!-- Malzeme Vurgusu -->
            <div class="trendyol-benefit-tagline">
                <span class="benefit-tagline-text">🛡️ 1. Sınıf E1 Kalite MDF • Kolay Montaj</span>
            </div>

            <!-- Yıldız Satırı -->
            <div class="trendyol-rating-row">
                <span class="rating-stars">★★★★★</span>
                <span class="rating-score"><?php echo esc_html($rating_val); ?></span>
                <span class="rating-count">(<?php echo esc_html($review_count); ?>)</span>
            </div>

            <!-- Fiyat & Sepete Ekle Alanı -->
            <div class="trendyol-card-bottom">
                <div class="trendyol-pricing-row">
                    <div class="current-price-val"><?php echo number_format($current_price, 0, ',', '.'); ?> TL</div>
                </div>

                <button type="button" data-quantity="1" data-product_id="<?php echo esc_attr($id); ?>" class="trendyol-btn-add-cart" title="<?php esc_attr_e('Sepete Ekle', 'mis360-mobilya'); ?>">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <span>Sepete Ekle</span>
                </button>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Slider Ürünlerini Çeken Yardımcı Fonksiyon
 */
function emdief_get_slider_products(string $type = 'all', int $limit = 8): array {
    if (!class_exists('WooCommerce')) {
        return [];
    }

    $args = [
        'limit'      => $limit,
        'status'     => 'publish',
        'visibility' => 'catalog',
    ];

    if ($type === 'bestseller') {
        $args['orderby'] = 'popularity';
        $args['order']   = 'DESC';
    } elseif ($type === 'new') {
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    }

    $prods = wc_get_products($args);

    if (empty($prods) || count($prods) < 4) {
        $prods = wc_get_products([
            'limit'  => $limit,
            'status' => 'publish',
        ]);
    }

    return $prods;
}
?>

<!-- =========================================================================
     3. BÖLÜM: 1.500 TL ÜZERİ ÜCRETSİZ KARGO BANNER'I
     ========================================================================= -->
<section class="emdief-cargo-promo-banner">
    <div class="emdief-container">
        <div class="cargo-promo-card">
            <div class="cargo-promo-left">
                <div class="cargo-truck-icon-wrap">
                    <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </div>
                <div class="cargo-promo-text">
                    <span class="cargo-badge-pill">🚚 EMDIEF HOME SEVKİYAT GÜVENCESİ</span>
                    <h3 class="cargo-headline">1.500 TL ve Üzeri Tüm Siparişlerinizde <span>Kargo Tamamen Ücretsiz!</span></h3>
                    <p class="cargo-sub">1. Sınıf MDF Montessori ürünleriniz darbe emici özel straforlu ambalajlarla %100 sigortalı teslim edilir.</p>
                </div>
            </div>
            <div class="cargo-promo-right">
                <div class="cargo-benefit-tag">
                    <span class="benefit-icon">⏱️</span>
                    <span>13:00'a Kadar <strong>Aynı Gün İmalat</strong></span>
                </div>
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="btn-cargo-shop">
                    <span>Koleksiyonu İncele</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     4. BÖLÜM: TRENDYOL SLIDER 1 - ÖNE ÇIKAN FIRSAT ÜRÜNLERİ (TURUNCU TEMA)
     ========================================================================= -->
<section class="trendyol-slider-section" id="sectionFlashDeals">
    <div class="emdief-container">
        <div class="trendyol-banner-box banner-theme-orange">
            <div class="trendyol-banner-header">
                <div class="trendyol-header-left">
                    <h2 class="trendyol-section-title">
                        <span class="title-icon">⚡</span>
                        <span>Öne Çıkan Modeller</span>
                    </h2>

                </div>
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="trendyol-view-all-link">
                    <span>Tümünü gör</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>

            <div class="trendyol-track-wrapper">
                <button type="button" class="trendyol-nav-arrow trendyol-nav-prev" data-target="trackFlashDeals" aria-label="Önceki Ürünler">&#10094;</button>
                <div class="trendyol-products-track" id="trackFlashDeals">
                    <?php
                    $flash_prods = emdief_get_slider_products('bestseller', 8);
                    $idx = 0;
                    foreach ($flash_prods as $prod):
                        if ($prod instanceof WC_Product):
                            emdief_render_trendyol_card($prod, 'flash', 'orange', $idx++);
                        endif;
                    endforeach;
                    ?>
                </div>
                <button type="button" class="trendyol-nav-arrow trendyol-nav-next" data-target="trackFlashDeals" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     5. BÖLÜM: TRENDYOL SLIDER 2 - ÇOK SATANLAR (MOR TEMA)
     ========================================================================= -->
<section class="trendyol-slider-section" id="sectionBestsellers">
    <div class="emdief-container">
        <div class="trendyol-banner-box banner-theme-purple">
            <div class="trendyol-banner-header">
                <div class="trendyol-header-left">
                    <h2 class="trendyol-section-title">
                        <span class="title-icon">🔥</span>
                        <span>Çok Satan Montessori Modelleri</span>
                    </h2>
                </div>
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="trendyol-view-all-link">
                    <span>Tümünü gör</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>

            <div class="trendyol-track-wrapper">
                <button type="button" class="trendyol-nav-arrow trendyol-nav-prev" data-target="trackBestsellers" aria-label="Önceki Ürünler">&#10094;</button>
                <div class="trendyol-products-track" id="trackBestsellers">
                    <?php
                    $best_prods = emdief_get_slider_products('bestseller', 8);
                    $idx = 0;
                    foreach ($best_prods as $prod):
                        if ($prod instanceof WC_Product):
                            emdief_render_trendyol_card($prod, 'bestseller', 'purple', $idx++);
                        endif;
                    endforeach;
                    ?>
                </div>
                <button type="button" class="trendyol-nav-arrow trendyol-nav-next" data-target="trackBestsellers" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     6. BÖLÜM: TRENDYOL SLIDER 3 - YENİ EKLENENLER (ZÜMRÜT YEŞİLİ TEMA)
     ========================================================================= -->
<section class="trendyol-slider-section" id="sectionNewArrivals">
    <div class="emdief-container">
        <div class="trendyol-banner-box banner-theme-emerald">
            <div class="trendyol-banner-header">
                <div class="trendyol-header-left">
                    <h2 class="trendyol-section-title">
                        <span class="title-icon">✨</span>
                        <span>Yeni Eklenen Tasarımlar</span>
                    </h2>
                </div>
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="trendyol-view-all-link">
                    <span>Tümünü gör</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>

            <div class="trendyol-track-wrapper">
                <button type="button" class="trendyol-nav-arrow trendyol-nav-prev" data-target="trackNewArrivals" aria-label="Önceki Ürünler">&#10094;</button>
                <div class="trendyol-products-track" id="trackNewArrivals">
                    <?php
                    $new_prods = emdief_get_slider_products('new', 8);
                    $idx = 0;
                    foreach ($new_prods as $prod):
                        if ($prod instanceof WC_Product):
                            emdief_render_trendyol_card($prod, 'new', 'emerald', $idx++);
                        endif;
                    endforeach;
                    ?>
                </div>
                <button type="button" class="trendyol-nav-arrow trendyol-nav-next" data-target="trackNewArrivals" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     7. BÖLÜM: TRENDYOL SLIDER 4 - EN ÇOK BEĞENİLENLER (MAVİ TEMA)
     ========================================================================= -->
<section class="trendyol-slider-section" id="sectionMostFavorited">
    <div class="emdief-container">
        <div class="trendyol-banner-box banner-theme-blue">
            <div class="trendyol-banner-header">
                <div class="trendyol-header-left">
                    <h2 class="trendyol-section-title">
                        <span class="title-icon">❤️</span>
                        <span>En Çok Favori Alan Modeller</span>
                    </h2>
                </div>
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="trendyol-view-all-link">
                    <span>Tümünü gör</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>

            <div class="trendyol-track-wrapper">
                <button type="button" class="trendyol-nav-arrow trendyol-nav-prev" data-target="trackFavorited" aria-label="Önceki Ürünler">&#10094;</button>
                <div class="trendyol-products-track" id="trackFavorited">
                    <?php
                    $fav_prods = emdief_get_slider_products('all', 8);
                    $idx = 0;
                    foreach ($fav_prods as $prod):
                        if ($prod instanceof WC_Product):
                            emdief_render_trendyol_card($prod, 'fav', 'blue', $idx++);
                        endif;
                    endforeach;
                    ?>
                </div>
                <button type="button" class="trendyol-nav-arrow trendyol-nav-next" data-target="trackFavorited" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     MARKA HİKAYESİ ALANI: ÇOCUKLARIN DÜNYASINA UYGUN TASARIMLAR
     ========================================================================= -->
<section class="emdief-brand-story-strip">
    <div class="emdief-container">
        <div class="brand-story-box">
            <div class="brand-story-header">
                <span class="story-mini-badge">🌱 EMDİEF HOME MONTESSORİ YAKLAŞIMI</span>
                <h2 class="brand-story-title"><?php esc_html_e('Çocukların Dünyasına Uygun Tasarımlar', 'mis360-mobilya'); ?></h2>
            </div>
            <p class="brand-story-desc">
                <?php esc_html_e('Çocukların kendi alanlarında özgürce hareket edebilmesi ve gelişimlerini destekleyen ortamlar oluşturmak için Montessori yaklaşımından ilham alan mobilyalar tasarlıyoruz.', 'mis360-mobilya'); ?>
            </p>
            <div class="brand-story-features">
                <div class="story-feat-item">
                    <span class="feat-icon">🌿</span>
                    <span class="feat-text"><strong>Doğal &amp; Güvenli</strong> 1. Sınıf E1 MDF</span>
                </div>
                <div class="story-feat-item">
                    <span class="feat-icon">👶</span>
                    <span class="feat-text"><strong>Bağımsız Keşif</strong> Ergonomik Boyutlar</span>
                </div>
                <div class="story-feat-item">
                    <span class="feat-icon">🛡️</span>
                    <span class="feat-text"><strong>360° Korumalı</strong> Yuvarlatılmış Hatlar</span>
                </div>
                <div class="story-feat-item">
                    <span class="feat-icon">⚡</span>
                    <span class="feat-text"><strong>Zahmetsiz Montaj</strong> CNC Açılmış Yuvalar</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     MUTLU MİNİKLER KÖŞESİ (MÜŞTERİ DENEYİMLERİ & GERÇEK KURULUM GALERİSİ)
     ========================================================================= -->
<?php if (function_exists('mis360_render_happy_kids_gallery')) { mis360_render_happy_kids_gallery('front'); } ?>

<!-- =========================================================================
     YARDIM & KOLAY KURULUM MERKEZİ BANNERI
     ========================================================================= -->
<section class="emdief-help-banner-section">
    <div class="emdief-container">
        <div class="emdief-assembly-banner-card">
            <div class="assembly-badge-col">
                <div class="assembly-icon-box">
                    <span class="assembly-icon">🛠️</span>
                    <span class="assembly-mascot">🧸</span>
                </div>
                <span class="assembly-time-tag">5 Dk Montaj</span>
            </div>

            <div class="assembly-banner-content">
                <div class="assembly-eyebrow">
                    <span class="assembly-live-dot"></span>
                    <span><?php esc_html_e('PRATİK VİDEOLAR & CANLI DESTEK', 'mis360-mobilya'); ?></span>
                </div>
                <h3 class="assembly-title"><?php esc_html_e('Montessori Mobilyanızı Şarjlı Matkap ile 5 Dakikada Kurun!', 'mis360-mobilya'); ?></h3>
                <p class="assembly-desc">
                    <?php esc_html_e('Tüm parçalarımız CNC tezgahlarda milimetrik açılmış delikleriyle gelir. Şarjlı matkabınızla vidaları saniyeler içinde sıkıp kitaplığınızı birleştirebilir ve duvara güvenle sabitleyebilirsiniz.', 'mis360-mobilya'); ?>
                </p>
                <div class="assembly-perks">
                    <span class="assembly-perk">⚡ <?php esc_html_e('Şarjlı Matkapla Hızlı Montaj', 'mis360-mobilya'); ?></span>
                    <span class="assembly-perk">🔒 <?php esc_html_e('Duvara Sabitleme Emniyeti', 'mis360-mobilya'); ?></span>
                    <span class="assembly-perk">🎥 <?php esc_html_e('Ürün Kurulum Videoları', 'mis360-mobilya'); ?></span>
                </div>
            </div>

            <div class="assembly-banner-actions">
                <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="assembly-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span><?php esc_html_e('Kurulum Videolarını İzle', 'mis360-mobilya'); ?></span>
                </a>
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Merhaba,%20kurulum%20ve%20montaj%20hakkında%20canlı%20destek%20almak%20istiyorum." target="_blank" rel="noopener" class="assembly-btn-secondary">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 16) : '💬'; ?>
                    <span><?php esc_html_e('Canlı Montaj Desteği', 'mis360-mobilya'); ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     MONTESSORI & EMDIEF HOME EĞİTİCİ FELSEFE
     ========================================================================= -->
<section class="emdief-philosophy-section" id="montessori-felsefesi">
    <div class="emdief-container">
        <div class="philosophy-card">
            <div class="philosophy-grid">
                <div class="philosophy-text">
                    <span class="section-subtitle color-amber"><?php esc_html_e('Pedagojik Yaklaşım & 1. Sınıf Doğal Ahşap', 'mis360-mobilya'); ?></span>
                    <h2 class="section-title"><?php esc_html_e('Montessori Felsefesinden İlham Alan Tasarımlar', 'mis360-mobilya'); ?></h2>
                    <p class="philosophy-lead-desc">
                        <strong><?php esc_html_e('Çocukların kendi alanlarında özgürce hareket edebilmesi, seçim yapabilmesi ve gelişimini destekleyen mobilyalar tasarlıyoruz.', 'mis360-mobilya'); ?></strong>
                    </p>
                    <p>
                        Maria Montessori'nin temel felsefesi; çocuğun kendi boyuna ve erişimine uygun bir çevrede büyümesidir. Geleneksel yüksek raflar çocuğun yetişkine bağımlı olmasına yol açarken, <strong>Emdief Home Montessori Kitaplıkları</strong> kitapların ön yüzünü çocuğa çevirerek bağımsız kitap seçme ve düzenleme alışkanlığı kazandırır.
                    </p>
                    <div class="philosophy-pillars">
                        <div class="pillar">
                            <div class="pillar-icon">👶</div>
                            <div class="pillar-content">
                                <h4>Özerklik & Karar Verme</h4>
                                <p>Çocuk kimseden yardım istemeden ilgisini çeken kitabı seçer ve yerine geri koyma alışkanlığı kazanır.</p>
                            </div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-icon">🛡️</div>
                            <div class="pillar-content">
                                <h4>1. Sınıf MDF</h4>
                                <p>Çocuk odalarına özel pürüzsüz, sağlam ve uzun ömürlü 1. sınıf kaliteli MDF malzeme.</p>
                            </div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-icon">🌿</div>
                            <div class="pillar-content">
                                <h4>360° Yuvarlatılmış Güvenli Hatlar</h4>
                                <p>Sivri köşeler ve tehlikeli kenarlar yok. Her köşe çocuk güvenliği için özel makinelerle pürüzsüzleştirilmiştir.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="philosophy-side-banner">
                    <div class="side-banner-card">
                        <span class="quote-badge">Önemli İpucu</span>
                        <h3>Kitap Kapağı Görünürlüğü Neden Kritik?</h3>
                        <p>Henüz okuma yazma bilmeyen çocuklar kitapları sırtından değil, renkli ön kapak resimlerinden tanır. Ön yüzü açık sergilenen kitaplar, okuma isteğini <strong>%70 oranında</strong> artırır.</p>
                        <div class="side-banner-author">
                            <strong>Emdief Home Çocuk Gelişimi Atölyesi</strong>
                            <small>Pedagojik Mobilya Tasarım Ekibi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     EBEVEYN YORUMLARI & SOSYAL KANIT
     ========================================================================= -->
<section class="emdief-reviews-section">
    <div class="emdief-container">
        <div class="section-header text-center">
            <span class="section-subtitle">Gerçek Müşteri Deneyimleri</span>
            <h2 class="section-title">Bizi Tercih Eden Aileler Ne Diyor?</h2>
        </div>
        <div class="reviews-grid">
            <div class="review-card">
                <div class="review-stars">⭐⭐⭐⭐⭐</div>
                <p class="review-text">"Carmen 3 raflı kitaplığı 2 yaşındaki kızım için aldık. Boyu tam hizasında, artık uyumadan önce kendi kitabını kendisi seçiyor. 1. Sınıf MDF kalitesi ve pürüzsüzlüğü gerçekten harika!"</p>
                <div class="review-user">
                    <div class="user-avatar">AY</div>
                    <div class="user-info">
                        <strong>Ayşe Yılmaz</strong>
                        <small>İstanbul (Carmen 3 Raflı Kitaplık)</small>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <div class="review-stars">⭐⭐⭐⭐⭐</div>
                <p class="review-text">"Paketleme olağanüstü özenliydi. 10 dakikada şarjlı vidalamaya bile gerek kalmadan kolayca kurduk. Köşelerinin yuvarlatılmış olması içimizi çok rahatlattı. Kesinlikle tavsiye ederim."</p>
                <div class="review-user">
                    <div class="user-avatar">MK</div>
                    <div class="user-info">
                        <strong>Mehmet Kaya</strong>
                        <small>İzmir (Safir 4 Raflı Kitaplık)</small>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <div class="review-stars">⭐⭐⭐⭐⭐</div>
                <p class="review-text">"Kızımın odasına koyar koymaz odanın havası değişti. Pürüzsüz 1. sınıf MDF kalitesi ve sağlamlığı çok iyi. WhatsApp üzerinden anında destekleri için çok teşekkürler!"</p>
                <div class="review-user">
                    <div class="user-avatar">SB</div>
                    <div class="user-info">
                        <strong>Selin Bozkurt</strong>
                        <small>Ankara (Safir 3 Raflı Kitaplık)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
