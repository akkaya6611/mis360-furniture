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
            <a href="<?php echo esc_url(home_url('/?s=kule&post_type=product')); ?>" class="story-item">
                <div class="story-ring ring-emerald">
                    <div class="story-inner">🪜</div>
                </div>
                <span class="story-name">Öğrenme Kulesi</span>
            </a>
            <a href="<?php echo esc_url(home_url('/?s=masa&post_type=product')); ?>" class="story-item">
                <div class="story-ring ring-blue">
                    <div class="story-inner">🎨</div>
                </div>
                <span class="story-name">Masa & Sandalye</span>
            </a>
            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="story-item">
                <div class="story-ring ring-purple">
                    <div class="story-inner">🧸</div>
                </div>
                <span class="story-name">Çok Satanlar</span>
            </a>
            <a href="<?php echo esc_url(home_url('/my-account/')); ?>" class="story-item">
                <div class="story-ring ring-gold">
                    <div class="story-inner">🚚</div>
                </div>
                <span class="story-name">Kargo Takip</span>
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
                            <p class="slide-lead">Kendi kitabını kendi seçen özgüvenli minikler için 1. Sınıf E1 Kalite MDF tasarımlar.</p>
                            <div class="slide-highlights">
                                <span>🛡️ E1 Belgeli MDF</span>
                                <span>🌿 360° Yuvarlak Hatlar</span>
                                <span>🔧 Kolay Kurulum</span>
                            </div>
                            <div class="slide-cta-group">
                                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="btn-hero-action">
                                    <span>Fırsatları İncele</span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                                <span class="slide-price-bubble">800 ₺'den Başlayan Fiyatlarla</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slayt 2: Öğrenme Kuleleri -->
                    <div class="hero-slide-item">
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg" alt="Öğrenme Kulesi" class="slide-bg-cover">
                        <div class="slide-overlay-gradient"></div>
                        <div class="slide-caption-box">
                            <span class="slide-tag-pill badge-emerald">✨ MİNİK ŞEFLER İŞ BAŞINDA</span>
                            <h2 class="slide-headline">Ayarlanabilir E1 MDF<br>Öğrenme Kuleleri</h2>
                            <p class="slide-lead">Mutfakta kek yapma ve tezgaha erişimde tam bağımsızlık. Kilitli güvenlik barıyla %100 emniyetli.</p>
                            <div class="slide-highlights">
                                <span>🔒 Çift Kademeli Emniyet</span>
                                <span>🪜 3 Kademeli Basamak</span>
                            </div>
                            <div class="slide-cta-group">
                                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="btn-hero-action">
                                    <span>Öğrenme Kulelerini Gör</span>
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
                            <span class="slide-tag-pill badge-blue">🚚 1.500 ₺ ÜZERİ KARGO BEDAVA</span>
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
                $flash_product_id = 189; // Carmen 3 Raflı Kitaplık
                $flash_link = get_permalink($flash_product_id) ?: home_url('/');
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
                            <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg" alt="Carmen 3 Raflı Kitaplık" class="deal-thumb">
                            <div class="deal-details">
                                <h4 class="deal-title">Carmen 3 Raflı Eğitici Kitaplık</h4>
                                <div class="deal-pricing">
                                    <del>950 ₺</del>
                                    <strong class="deal-price">800,00 ₺</strong>
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
                        <p class="coupon-desc">1. Sınıf E1 MDF Montessori ürünlerinde sepette geçerli kupon kodunuz:</p>
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
     3. BÖLÜM: GÜVEN ROZETLERİ (E1 MDF & ÜCRETSİZ KARGO)
     ========================================================================= -->
<section class="emdief-guarantee-bar">
    <div class="emdief-container">
        <div class="guarantee-grid">
            <div class="guarantee-item">
                <div class="guar-icon-box guar-mint">🛡️</div>
                <div class="guar-info">
                    <h5>1. Sınıf E1 Kalite MDF</h5>
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
                    <p>Numaralı parçalar, aletsiz pratik montaj</p>
                </div>
            </div>
            <div class="guarantee-item">
                <div class="guar-icon-box guar-coral">🚚</div>
                <div class="guar-info">
                    <h5>Ücretsiz & Sigortalı Kargo</h5>
                    <p>1.500 ₺ üzeri kapıya kadar güvenli teslimat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
/**
 * Yardımcı Fonksiyon: Slider Ürün Kartı Render Edici
 */
function emdief_render_slider_card(WC_Product $prod, string $badge_type = 'bestseller', ?string $custom_fav = null): void {
    $permalink = get_permalink($prod->get_id());
    $title     = $prod->get_name();
    $img_url   = wp_get_attachment_image_url($prod->get_image_id(), 'woocommerce_thumbnail');
    if (!$img_url) {
        $img_url = 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg';
    }
    $price_html = $prod->get_price_html();
    $rating_count = $prod->get_rating_count() ?: 18;
    ?>
    <div class="product-shelf-card">
        <div class="shelf-thumb-wrap">
            <?php if ($badge_type === 'bestseller'): ?>
                <span class="card-tag tag-bestseller">🔥 Çok Satan</span>
            <?php elseif ($badge_type === 'new'): ?>
                <span class="card-tag tag-new">✨ Yeni</span>
            <?php elseif ($badge_type === 'fav'): ?>
                <span class="card-tag tag-fav-count"><?php echo esc_html($custom_fav ?: '❤️ Favori'); ?></span>
            <?php endif; ?>

            <button type="button" class="btn-wishlist-heart <?php echo ($badge_type === 'fav') ? 'is-active' : ''; ?>" aria-label="<?php esc_attr_e('Favorilere Ekle', 'mis360-mobilya'); ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="<?php echo ($badge_type === 'fav') ? '#ef4444' : 'none'; ?>" stroke="<?php echo ($badge_type === 'fav') ? '#ef4444' : 'currentColor'; ?>" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </button>
            <a href="<?php echo esc_url($permalink); ?>" class="shelf-image-link" style="display:block; width:100%; height:100%;">
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
            </a>
        </div>
        <div class="shelf-content">
            <span class="mat-badge">1. Sınıf E1 MDF</span>
            <h3 class="product-title-text"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h3>
            <div class="shelf-rating">
                <span class="stars">★★★★★</span>
                <small>5.0 (<?php echo esc_html((string)$rating_count); ?> Değerlendirme)</small>
            </div>
            <div class="shelf-bottom-row">
                <div class="pricing-group">
                    <span class="price"><?php echo $price_html; ?></span>
                </div>
                <a href="<?php echo esc_url($prod->add_to_cart_url()); ?>" data-quantity="1" data-product_id="<?php echo esc_attr((string)$prod->get_id()); ?>" class="btn-add-cart-icon ajax_add_to_cart add_to_cart_button" title="<?php esc_attr_e('Sepete Ekle', 'mis360-mobilya'); ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                </a>
            </div>
        </div>
    </div>
    <?php
}
?>

<!-- =========================================================================
     4. BÖLÜM: SLIDER 1 - ÇOK SATAN MONTESSORI KİTAPLIKLAR
     ========================================================================= -->
<section class="emdief-product-slider-block" id="sliderBestsellers">
    <div class="emdief-container">
        <div class="slider-section-top">
            <div class="slider-heading-col">
                <span class="slider-mini-badge badge-flame">🔥 EN ÇOK TERCİH EDİLENLER</span>
                <h2 class="slider-title">Çok Satan Montessori Kitaplıklar</h2>
                <p class="slider-desc">Annelerin ve babaların çocuk odalarında en çok tercih ettiği 1. sınıf E1 MDF modeller.</p>
            </div>
            <div class="slider-action-controls">
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="link-view-all">Tümünü Gör</a>
                <button type="button" class="btn-slider-arrow btn-prev" data-target="bestsellersScrollTrack" aria-label="Önceki Ürünler">&#10094;</button>
                <button type="button" class="btn-slider-arrow btn-next" data-target="bestsellersScrollTrack" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>

        <div class="product-scroll-track" id="bestsellersScrollTrack">
            <?php
            // En popüler Montessori kitaplıkları (Carmen & Safir modelleri)
            $bestseller_ids = [189, 188, 169, 154, 137, 136];
            foreach ($bestseller_ids as $pid):
                $p = wc_get_product($pid);
                if ($p instanceof WC_Product):
                    emdief_render_slider_card($p, 'bestseller');
                endif;
            endforeach;
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     5. BÖLÜM: SLIDER 2 - YENİ EKLENEN ÜRÜNLER (NEW ARRIVALS)
     ========================================================================= -->
<section class="emdief-product-slider-block bg-warm-tint" id="sliderNewArrivals">
    <div class="emdief-container">
        <div class="slider-section-top">
            <div class="slider-heading-col">
                <span class="slider-mini-badge badge-emerald">✨ YENİ KOLEKSİYON</span>
                <h2 class="slider-title">Yeni Eklenen Montessori Ürünleri</h2>
                <p class="slider-desc">Emdief Home tasarım atölyesinden çıkan en güncel eğitici mobilya modelleri.</p>
            </div>
            <div class="slider-action-controls">
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="link-view-all">Tümünü Gör</a>
                <button type="button" class="btn-slider-arrow btn-prev" data-target="newArrivalsScrollTrack" aria-label="Önceki Ürünler">&#10094;</button>
                <button type="button" class="btn-slider-arrow btn-next" data-target="newArrivalsScrollTrack" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>

        <div class="product-scroll-track" id="newArrivalsScrollTrack">
            <?php
            // En son eklenen ürünler
            $new_ids = [189, 170, 155, 153, 138, 121];
            foreach ($new_ids as $pid):
                $p = wc_get_product($pid);
                if ($p instanceof WC_Product):
                    emdief_render_slider_card($p, 'new');
                endif;
            endforeach;
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     6. BÖLÜM: SLIDER 3 - EN ÇOK FAVORİ ALAN ÜRÜNLER (MOST FAVORITED)
     ========================================================================= -->
<section class="emdief-product-slider-block" id="sliderMostFavorited">
    <div class="emdief-container">
        <div class="slider-section-top">
            <div class="slider-heading-col">
                <span class="slider-mini-badge badge-coral">❤️ EN ÇOK FAVORİLENENLER</span>
                <h2 class="slider-title">En Çok Favori Alan Ürünler</h2>
                <p class="slider-desc">Binlerce annenin ve babanın favori listesine kaydettiği Montessori yıldızları.</p>
            </div>
            <div class="slider-action-controls">
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="link-view-all">Tümünü Gör</a>
                <button type="button" class="btn-slider-arrow btn-prev" data-target="favoritedScrollTrack" aria-label="Önceki Ürünler">&#10094;</button>
                <button type="button" class="btn-slider-arrow btn-next" data-target="favoritedScrollTrack" aria-label="Sonraki Ürünler">&#10095;</button>
            </div>
        </div>

        <div class="product-scroll-track" id="favoritedScrollTrack">
            <?php
            $fav_products = [
                ['id' => 189, 'fav' => '❤️ 3.8k Favori'],
                ['id' => 188, 'fav' => '❤️ 2.4k Favori'],
                ['id' => 169, 'fav' => '❤️ 1.9k Favori'],
                ['id' => 154, 'fav' => '❤️ 1.5k Favori'],
                ['id' => 137, 'fav' => '❤️ 1.2k Favori'],
                ['id' => 120, 'fav' => '❤️ 980 Favori'],
            ];
            foreach ($fav_products as $item):
                $p = wc_get_product($item['id']);
                if ($p instanceof WC_Product):
                    emdief_render_slider_card($p, 'fav', $item['fav']);
                endif;
            endforeach;
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     7. BÖLÜM: MONTESSORI & EMDIEF HOME EĞİTİCİ FELSEFE
     ========================================================================= -->
<section class="emdief-philosophy-section" id="montessori-felsefesi">
    <div class="emdief-container">
        <div class="philosophy-card">
            <div class="philosophy-grid">
                <div class="philosophy-text">
                    <span class="section-subtitle color-amber">Montessori Pedagojisi & 1. Sınıf E1 MDF</span>
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
                                <h4>1. Sınıf E1 Kalite MDF</h4>
                                <p>Avrupa standartlarında çocuk sağlığına zararsız, pürüzsüz ve uzun ömürlü 1. sınıf MDF malzeme.</p>
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
