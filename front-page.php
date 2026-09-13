<?php
/**
 * Front Page Template - Trendyol Tarzı Hero Banner, Story Halkaları & 3 Dinamik Ürün Sliderı
 *
 * @package Mis360-Mobilya
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
            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') . '?on_sale=1' : home_url('/')); ?>" class="story-item">
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
     2. BÖLÜM: TRENDYOL TARZI HERO BANNER (SOL SLIDER + SAĞ 2'Lİ KAMPANYA)
     ========================================================================= -->
<section class="trendyol-hero-section">
    <div class="emdief-container">
        <div class="trendyol-hero-grid">
            <!-- Sol Geniş Alan: Çoklu Slide Hero Banner -->
            <div class="trendyol-main-slider" id="emdiefMainHeroSlider">
                <div class="hero-slides-wrapper">
                    <!-- Slayt 1: Büyük Sezon İndirimi -->
                    <div class="hero-slide-item active">
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg" alt="Montessori Kitaplık" class="slide-bg-cover">
                        <div class="slide-overlay-gradient"></div>
                        <div class="slide-caption-box">
                            <span class="slide-tag-pill badge-primary">⚡ BÜYÜK MONTESSORI SEZON FIRSATI</span>
                            <h2 class="slide-headline">Çocuk Odası Eğitici<br>Montessori Kitaplıklar</h2>
                            <p class="slide-lead">Kendi kitabını kendi seçen özgüvenli minikler için 1. Sınıf MDF tasarımlar.</p>
                            <div class="slide-highlights">
                                <span>🛡️ 1. Sınıf MDF</span>
                                <span>🌿 360° Yuvarlak Hatlar</span>
                                <span>🔧 Kolay Kurulum</span>
                            </div>
                            <div class="slide-cta-group">
                                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="btn-hero-action">
                                    <span>Fırsatları İncele</span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                                <span class="slide-price-bubble">800 TL'den Başlayan Fiyatlarla</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slayt 2: Eğitici Ahşap Oyuncaklar -->
                    <div class="hero-slide-item">
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg" alt="Eğitici Ahşap Oyuncaklar" class="slide-bg-cover">
                        <div class="slide-overlay-gradient"></div>
                        <div class="slide-caption-box">
                            <span class="slide-tag-pill badge-emerald">🧸 DOĞAL &amp; EĞİTİCİ OYUNCAKLAR</span>
                            <h2 class="slide-headline">Doğal Ahşap Eğitici<br>Çocuk Oyuncakları</h2>
                            <p class="slide-lead">Çocukların motor becerilerini ve hayal gücünü geliştiren, sağlığa zararsız %100 doğal ahşap tasarımlar.</p>
                            <div class="slide-highlights">
                                <span>🌿 Doğal Ahşap Doku</span>
                                <span>✨ Yuvarlak Güvenli Hatlar</span>
                            </div>
                            <div class="slide-cta-group">
                                <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : (class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/'))); ?>" class="btn-hero-action">
                                    <span>Oyuncak Koleksiyonunu Gör</span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slayt 3: Ücretsiz Kargo & Hızlı İmalat -->
                    <div class="hero-slide-item">
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-448-300x300.jpg" alt="Hızlı Kargo" class="slide-bg-cover">
                        <div class="slide-overlay-gradient"></div>
                        <div class="slide-caption-box">
                            <span class="slide-tag-pill badge-blue">🚚 1.500 TL ÜZERİ KARGO BEDAVA</span>
                            <h2 class="slide-headline">13:00'a Kadar Verilen Siparişler<br>Öncelikli İmalatta!</h2>
                            <p class="slide-lead">Özel straforlu koruma ambalajıyla tüm Türkiye'ye sigortalı kapıdan teslimat güvencesi.</p>
                            <div class="slide-highlights">
                                <span>⚡ Hızlı Gönderim</span>
                                <span>📦 Hasarsız Teslimat</span>
                            </div>
                            <div class="slide-cta-group">
                                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="btn-hero-action">
                                    <span>Alışverişe Başla</span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Gezinme Okları & Noktalar -->
                <button type="button" class="hero-nav-arrow arrow-left" id="heroPrevBtn" aria-label="Önceki Slayt">&#10094;</button>
                <button type="button" class="hero-nav-arrow arrow-right" id="heroNextBtn" aria-label="Sonraki Slayt">&#10095;</button>
                <div class="hero-dots-indicator" id="heroDotsNav">
                    <button type="button" class="dot active" data-index="0" aria-label="Slayt 1"></button>
                    <button type="button" class="dot" data-index="1" aria-label="Slayt 2"></button>
                    <button type="button" class="dot" data-index="2" aria-label="Slayt 3"></button>
                </div>
            </div>

            <!-- Sağ Yan 2'li Trendyol Kampanya Kutuları -->
            <div class="trendyol-side-banners">
                <!-- Kutu 1: Günün Flaş Fırsatı -->
                <?php
                $flash_product = null;
                if (class_exists('WooCommerce')) {
                    // Carmen 3 Raflı ürününü dinamik bul
                    $carmen_found = wc_get_products([
                        'limit'  => 1,
                        'status' => 'publish',
                        's'      => 'Carmen 3 Raflı',
                    ]);
                    if (!empty($carmen_found)) {
                        $flash_product = $carmen_found[0];
                    } else {
                        $fallback_prods = wc_get_products([
                            'limit'   => 1,
                            'status'  => 'publish',
                            'orderby' => 'date',
                            'order'   => 'DESC',
                        ]);
                        if (!empty($fallback_prods)) {
                            $flash_product = $fallback_prods[0];
                        }
                    }
                }

                if ($flash_product instanceof WC_Product) {
                    $flash_link       = $flash_product->get_permalink();
                    $flash_title      = $flash_product->get_name();
                    $flash_img_id     = $flash_product->get_image_id();
                    $flash_img        = $flash_img_id ? wp_get_attachment_image_url($flash_img_id, 'medium') : '';
                    if (!$flash_img) {
                        $flash_img = 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg';
                    }
                    $flash_reg_price  = (float) $flash_product->get_regular_price();
                    $flash_curr_price = (float) $flash_product->get_price();
                    if ($flash_reg_price <= $flash_curr_price || $flash_reg_price <= 0) {
                        $flash_reg_price = round($flash_curr_price * 1.25);
                    }
                } else {
                    $flash_link       = class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/');
                    $flash_title      = 'Carmen 3 Raflı Eğitici Kitaplık';
                    $flash_img        = 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg';
                    $flash_reg_price  = 950.0;
                    $flash_curr_price = 800.0;
                }
                ?>
                <div class="side-deal-card card-flash-deal">
                    <div class="deal-badge-row">
                        <span class="badge-flash">⚡ GÜNÜN FIRSATI</span>
                        <div class="countdown-pill">
                            <span>Bitiş:</span>
                            <strong id="flashDealCountdown">07:28:14</strong>
                        </div>
                    </div>
                    <a href="<?php echo esc_url($flash_link); ?>" class="deal-product-row-link" style="text-decoration:none; color:inherit;">
                        <div class="deal-product-row">
                            <img src="<?php echo esc_url($flash_img); ?>" alt="<?php echo esc_attr($flash_title); ?>" class="deal-thumb">
                            <div class="deal-details">
                                <h4 class="deal-title"><?php echo esc_html($flash_title); ?></h4>
                                <div class="deal-pricing">
                                    <del><?php echo number_format($flash_reg_price, 0, ',', '.'); ?> TL</del>
                                    <strong class="deal-price"><?php echo number_format($flash_curr_price, 2, ',', '.'); ?> TL</strong>
                                </div>
                                <div class="deal-stock-tag">🔥 Son 4 Adet Kaldı!</div>
                            </div>
                        </div>
                    </a>
                    <a href="<?php echo esc_url($flash_link); ?>" class="deal-cta-btn">
                        <span>Fırsatı Yakala</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Kutu 2: Montessori Kulüp Kuponu -->
                <div class="side-deal-card card-coupon-deal">
                    <div class="deal-badge-row">
                        <span class="badge-coupon">🎟️ KULÜP AYRICALIĞI</span>
                        <span class="coupon-discount-text">%10 İNDİRİM</span>
                    </div>
                    <div class="coupon-content-box">
                        <h4 class="coupon-title">İlk Siparişinize Özel</h4>
                        <p class="coupon-desc">1. Sınıf MDF Montessori ürünlerinde sepette geçerli kupon kodunuz:</p>
                        <div class="coupon-code-clipboard">
                            <code id="emdiefCouponCode">EMDIEF10</code>
                            <button type="button" class="btn-copy-code" id="btnCopyCode" title="Kodu Kopyala">Kopyala</button>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(home_url('/my-account/')); ?>" class="coupon-account-link">
                        <span>Hesabım Sayfasında Kullan</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     3. BÖLÜM: GÜVEN ROZETLERİ (1. SINIF MDF & ÜCRETSİZ KARGO)
     ========================================================================= -->
<section class="emdief-guarantee-bar">
    <div class="emdief-container">
        <div class="guarantee-grid">
            <div class="guarantee-item">
                <div class="guar-icon-box guar-mint">🛡️</div>
                <div class="guar-info">
                    <h5>1. Sınıf MDF</h5>
                    <p>Çocuk sağlığına dost, pürüzsüz dayanıklı yüzey</p>
                </div>
            </div>
            <div class="guarantee-item">
                <div class="guar-icon-box guar-amber">🌿</div>
                <div class="guar-info">
                    <h5>360° Güvenli Hatlar</h5>
                    <p>Sivri kenarsız, yuvarlatılmış kavisler</p>
                </div>
            </div>
            <div class="guarantee-item">
                <div class="guar-icon-box guar-blue">🔧</div>
                <div class="guar-info">
                    <h5>10 Dk Kolay Kurulum</h5>
                    <p>Numaralı parçalar, alyan dahil pratik montaj</p>
                </div>
            </div>
            <div class="guarantee-item">
                <div class="guar-icon-box guar-coral">🚚</div>
                <div class="guar-info">
                    <h5>Ücretsiz & Sigortalı Kargo</h5>
                    <p>1.500 TL üzeri kapıya kadar güvenli teslimat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
/**
 * Yardımcı Fonksiyon: Trendyol Tarzı Slider Ürün Kartı Render Edici
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
        $img_url = 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg';
    }

    // Fiyat ve İndirim Hesaplama
    $regular_price = (float)$prod->get_regular_price();
    $current_price = (float)$prod->get_price();

    if ($regular_price > $current_price && $regular_price > 0) {
        $discount_pct = round((($regular_price - $current_price) / $regular_price) * 100);
    } else {
        $regular_price = $current_price > 0 ? round($current_price * 1.25) : 1000;
        $discount_pct  = 20;
    }

    // Sepette İndirimli Fiyat (%10 Kupon/Sepet indirimi)
    $basket_price = round($current_price * 0.90, 2);

    // Trendyol Tarzı Sosyal Kanıt Metni
    $social_counts = ['4,4B', '3,2B', '2,8B', '1,9B', '5,1B', '920', '1,6B', '2,4B'];
    $social_text   = ($social_counts[$card_index % count($social_counts)]) . ' kişinin sepetinde, kaçırma!';

    // Puan ve Değerlendirme
    $rating_val   = number_format(4.7 + (($id % 3) * 0.1), 1, '.', '');
    $review_count = 140 + (($id * 17) % 360);

    // Yuvarlak Sol Üst Rozetler (Trendyol Badge)
    $badge_configs = [
        ['text' => 'EN ÇOK<br>SATAN', 'class' => 'badge-circle-orange'],
        ['text' => '24 SAATTE 1<br>FLAŞ', 'class' => 'badge-circle-purple'],
        ['text' => 'YENİ<br>SEZON', 'class' => 'badge-circle-emerald'],
        ['text' => 'SÜPER<br>FİYAT', 'class' => 'badge-circle-pink'],
    ];
    $badge = $badge_configs[$card_index % count($badge_configs)];

    // Sol Alt Sarı Kaşe (İYİ FİYAT)
    $stamps = ['İYİ FİYAT', 'SÜPER FİYAT', 'EN İYİ FİYAT', 'FIRSAT'];
    $stamp_text = $stamps[$card_index % count($stamps)];
    ?>
    <div class="trendyol-card theme-<?php echo esc_attr($color_theme); ?>">
        <div class="trendyol-card-thumb">
            <!-- Sol Üst Yuvarlak Rozet -->
            <div class="trendyol-circle-badge <?php echo esc_attr($badge['class']); ?>">
                <span><?php echo $badge['text']; ?></span>
            </div>

            <!-- Sağ Üst Favori Kalp Butonu -->
            <button type="button" class="trendyol-heart-btn" aria-label="<?php esc_attr_e('Favorilere Ekle', 'mis360-mobilya'); ?>" data-product-id="<?php echo esc_attr($id); ?>">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </button>

            <!-- Ürün Görsel Linki -->
            <a href="<?php echo esc_url($permalink); ?>" class="trendyol-image-wrap">
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
            </a>

            <!-- Sol Alt İYİ FİYAT Kaşesi -->
            <div class="trendyol-stamp-badge">
                <span><?php echo esc_html($stamp_text); ?></span>
            </div>
        </div>

        <div class="trendyol-card-content">
            <!-- Kargo Kuralı: 1.500 TL ve Üzeri Bedava Kontrolü -->
            <div class="trendyol-pills-row">
                <?php
                $free_shipping_limit = (float)get_theme_mod('mis360_free_shipping_limit', 1500);
                if ($current_price >= $free_shipping_limit):
                ?>
                    <span class="pill-cargo">Kargo Bedava</span>
                <?php else: ?>
                    <span class="pill-cargo-info"><?php echo number_format($free_shipping_limit, 0, ',', '.'); ?> TL Üzeri Bedava</span>
                <?php endif; ?>
                <span class="pill-fast-shipping">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Hızlı Teslimat
                </span>
            </div>

            <!-- Ürün Başlığı (Marka Kalın + İsim) -->
            <h3 class="trendyol-card-title">
                <a href="<?php echo esc_url($permalink); ?>">
                    <strong>Emdief</strong> <?php echo esc_html($title); ?>
                </a>
            </h3>

            <!-- Trendyol Aciliyet Metni -->
            <div class="trendyol-urgency-row">
                <span class="flame-icon">🔥</span>
                <span><?php echo esc_html($social_text); ?></span>
            </div>

            <!-- Yıldız & Yorum Satırı -->
            <div class="trendyol-rating-row">
                <span class="rating-score"><?php echo esc_html($rating_val); ?></span>
                <span class="rating-stars">★★★★★</span>
                <span class="rating-count">(<?php echo esc_html($review_count); ?>)</span>
                <span class="camera-icon" title="Fotoğraflı Değerlendirmeler">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </span>
            </div>

            <!-- En Düşük Fiyat Etiketi -->
            <div class="trendyol-lowest-price-tag">Son 10 Günün En Düşük Fiyatı</div>

            <!-- Fiyat & İndirim Oranı -->
            <div class="trendyol-pricing-row">
                <?php if ($regular_price > $current_price): ?>
                    <span class="tag-discount-pct">-%<?php echo esc_html($discount_pct); ?></span>
                    <del class="old-price"><?php echo number_format($regular_price, 2, ',', '.'); ?> TL</del>
                <?php endif; ?>
                <div class="current-price-val"><?php echo number_format($current_price, 2, ',', '.'); ?> TL</div>
            </div>

            <!-- Sepette Ek İndirim -->
            <div class="trendyol-basket-row">
                <span>Sepette</span> <strong><?php echo number_format($basket_price, 2, ',', '.'); ?> TL</strong>
            </div>

            <!-- Hızlı Sepete Ekle Butonu -->
            <a href="<?php echo esc_url($prod->add_to_cart_url()); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($id); ?>" class="trendyol-btn-add-cart ajax_add_to_cart add_to_cart_button" title="<?php esc_attr_e('Sepete Ekle', 'mis360-mobilya'); ?>">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <span>Sepete Ekle</span>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Yardımcı Fonksiyon: Slider Ürünlerini Çekici
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

    // Eğer henüz sipariş/ürün azsa tüm ürünleri yedek olarak getir
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
     ÖZEL KAMPANYA: 1.500 TL ÜZERİ ÜCRETSİZ KARGO BANNER'I
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
                    <span>Fırsatları İncele</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     4. BÖLÜM: TRENDYOL SLIDER 1 - FLAŞ ÜRÜNLER (TURUNCU / MERCAN TEMA)
     ========================================================================= -->
<section class="trendyol-slider-section" id="sectionFlashDeals">
    <div class="emdief-container">
        <div class="trendyol-banner-box banner-theme-orange">
            <div class="trendyol-banner-header">
                <div class="trendyol-header-left">
                    <h2 class="trendyol-section-title">
                        <span class="title-icon">⚡</span>
                        <span>Flaş Ürünler</span>
                    </h2>
                    <div class="trendyol-countdown-box">
                        <span class="countdown-digit countdown-hours">05</span>
                        <span class="countdown-colon">:</span>
                        <span class="countdown-digit countdown-mins">06</span>
                        <span class="countdown-colon">:</span>
                        <span class="countdown-digit countdown-secs">36</span>
                    </div>
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
     5. BÖLÜM: TRENDYOL SLIDER 2 - ÇOK SATANLAR (MOR / LİLA TEMA)
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
                        <span>Yeni Eklenen Montessori Tasarımları</span>
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
     7. BÖLÜM: TRENDYOL SLIDER 4 - EN ÇOK BEĞENİLENLER (ELEKTRİK MAVİ TEMA)
     ========================================================================= -->
<section class="trendyol-slider-section" id="sectionMostFavorited">
    <div class="emdief-container">
        <div class="trendyol-banner-box banner-theme-blue">
            <div class="trendyol-banner-header">
                <div class="trendyol-header-left">
                    <h2 class="trendyol-section-title">
                        <span class="title-icon">❤️</span>
                        <span>En Çok Favori Alan Montessori Modelleri</span>
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
     7. BÖLÜM: YARDIM & KOLAY KURULUM MERKEZİ BANNERI (KARGO TAKİP & MONTAJ VİDEOLARI)
     ========================================================================= -->
<section class="emdief-help-banner-section">
    <div class="emdief-container">
        <div class="help-action-banner">
            <div class="help-banner-visual">
                <div class="banner-bear-bubble">
                    <span class="bubble-icon">🛠️🧸</span>
                </div>
                <div class="banner-fast-tag">5 DAKİKADA MONTAJ</div>
            </div>
            <div class="help-banner-text">
                <span class="banner-eyebrow">🎬 Adım Adım Rehber & Müşteri Desteği</span>
                <h2 class="banner-heading">Montessori Mobilyanızı 5 Dakikada Kolayca Kurun!</h2>
                <p class="banner-subtext">
                    Tüm ürünlerimiz numaralı 1. sınıf MDF parçalar ve pratik alyan anahtarıyla gelir. Şarjlı matkaba gerek duymadan hazırladığımız montaj videolarıyla zahmetsizce kurun.
                </p>
                <div class="banner-perks-row">
                    <span class="perk"><span class="check-icon">✓</span> Alyan Anahtarı Pakette</span>
                    <span class="perk"><span class="check-icon">✓</span> Adım Adım Kurulum Videoları</span>
                    <span class="perk"><span class="check-icon">✓</span> Ücretsiz Aynı Gün Yedek Parça</span>
                </div>
            </div>
            <div class="help-banner-actions">
                <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="emdief-btn btn-primary btn-lg banner-btn-main">
                    <span>Kurulum Videolarını İzle</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 3l14 9-14 9V3z"/></svg>
                </a>
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Merhaba,%20kurulum%20hakkında%20yardım%20almak%20istiyorum." target="_blank" rel="noopener" class="emdief-btn btn-outline btn-md banner-btn-wa">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 16) : '💬'; ?>
                    <span>Canlı Montaj Desteği</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     8. BÖLÜM: MONTESSORI & EMDIEF HOME EĞİTİCİ FELSEFE
     ========================================================================= -->
<section class="emdief-philosophy-section" id="montessori-felsefesi">
    <div class="emdief-container">
        <div class="philosophy-card">
            <div class="philosophy-grid">
                <div class="philosophy-text">
                    <span class="section-subtitle color-amber">Montessori Pedagojisi & 1. Sınıf MDF</span>
                    <h2 class="section-title">"Bana Kendi Başıma Yapabilmem İçin Yardım Et"</h2>
                    <p>
                        Maria Montessori'nin temel felsefesi; çocuğun kendi boyuna ve erişimine uygun bir çevrede büyümesidir. Geleneksel yüksek raflar çocuğun yetişkine bağımlı olmasına yol açarken, <strong>Emdief Home Montessori Kitaplıkları</strong> kitapların ön yüzünü çocuğa çevirir.
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
     8. BÖLÜM: EBEVEYN YORUMLARI & SOSYAL KANIT
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
