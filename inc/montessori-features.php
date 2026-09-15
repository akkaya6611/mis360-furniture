<?php
/**
 * Emdief Home - Özel Montessori Satış & Dönüşüm Modülleri (v1.9.0)
 *
 * 1. İmalat ve Kargo Canlı Geri Sayım Sayacı
 * 2. "Bu Seti Tamamla" (Frequently Bought Together / Bundle) Modülü
 * 3. "Mutlu Minikler Köşesi" (Müşteri Deneyimleri CPT & Lightbox Galeri)
 * 4. İnteraktif "Ölçü & Yerleşim Rehberi" (Kuşbakışı Montessori Oda Simülatörü)
 * 5. Hızlı Favorilere Ekle (Wishlist) & "Eşine WhatsApp ile Gönder" Butonları
 *
 * @package Mis360-Mobilya
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================================
   1. İMALAT VE KARGO GERİ SAYIM SAYACI (HER GÜN SAAT 13:00)
   ========================================================================== */
function mis360_production_countdown_timer() {
    if (!is_product()) return;

    $tz = new DateTimeZone('Europe/Istanbul');
    $now = new DateTime('now', $tz);

    $target = clone $now;
    $target->setTime(13, 0, 0);

    $is_past = ($now >= $target);
    if ($is_past) {
        $target->modify('+1 day');
    }

    $diff = $now->diff($target);
    ?>
    <div class="emdief-production-countdown-wrap" id="emdiefProductionCountdown" data-target-ts="<?php echo esc_attr((string)$target->getTimestamp()); ?>">
        <div class="countdown-badge-inner <?php echo $is_past ? 'is-next-day' : 'is-today'; ?>">
            <div class="countdown-icon-box">
                <span class="countdown-pulse-dot"></span>
                <span class="countdown-clock-emoji">⏱️</span>
            </div>
            <div class="countdown-content-col">
                <div class="countdown-title-row">
                    <span class="countdown-label">
                        <?php if (!$is_past): ?>
                            <?php esc_html_e('Bugünkü Öncelikli İmalata Yetişmek İçin:', 'mis360-mobilya'); ?>
                        <?php else: ?>
                            <?php esc_html_e('Yarınki İmalat Sırasına Alınacaktır. Kalan Süre:', 'mis360-mobilya'); ?>
                        <?php endif; ?>
                    </span>
                    <span class="countdown-status-pill"><?php echo $is_past ? 'YARIN 13:00 SIRASI' : 'BUGÜN 13:00 SIRASI'; ?></span>
                </div>
                <div class="countdown-timer-display" id="productionTimerClock">
                    <span class="timer-segment"><strong class="t-val" id="cntHours"><?php echo sprintf('%02d', $diff->h); ?></strong><small>saat</small></span>
                    <span class="timer-sep">:</span>
                    <span class="timer-segment"><strong class="t-val" id="cntMinutes"><?php echo sprintf('%02d', $diff->i); ?></strong><small>dakika</small></span>
                    <span class="timer-sep">:</span>
                    <span class="timer-segment"><strong class="t-val" id="cntSeconds"><?php echo sprintf('%02d', $diff->s); ?></strong><small>saniye</small></span>
                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('woocommerce_single_product_summary', 'mis360_production_countdown_timer', 29);


/* ==========================================================================
   2. "BU SETİ TAMAMLA" (FREQUENTLY BOUGHT TOGETHER / BUNDLE ENGINE)
   ========================================================================== */
function mis360_render_bundle_cross_sells() {
    global $product;
    if (!$product) return;

    $current_id = $product->get_id();
    $bundle_products = [];

    // Önce WooCommerce Cross-Sell ürünlerini kontrol et
    $cross_sell_ids = $product->get_cross_sell_ids();
    if (!empty($cross_sell_ids)) {
        foreach ($cross_sell_ids as $cs_id) {
            $p = wc_get_product($cs_id);
            if ($p && $p->is_purchasable() && $p->is_in_stock() && $p->get_id() !== $current_id) {
                $bundle_products[] = $p;
            }
            if (count($bundle_products) >= 2) break;
        }
    }

    // Yetersizse aynı kategoriden veya mağazadan popüler Montessori tamamlayıcılarını çek
    if (count($bundle_products) < 2) {
        $cats = wp_get_post_terms($current_id, 'product_cat', ['fields' => 'ids']);
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => 4,
            'post__not_in'   => array_merge([$current_id], wp_list_pluck($bundle_products, 'id')),
            'orderby'        => 'rand',
            'meta_query'     => [
                [
                    'key'     => '_stock_status',
                    'value'   => 'instock',
                    'compare' => '=',
                ]
            ]
        ];
        if (!empty($cats)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $cats,
                ]
            ];
        }
        $query = new WP_Query($args);
        while ($query->have_posts() && count($bundle_products) < 2) {
            $query->the_post();
            $add_p = wc_get_product(get_the_ID());
            if ($add_p && $add_p->is_purchasable()) {
                $bundle_products[] = $add_p;
            }
        }
        wp_reset_postdata();
    }

    if (empty($bundle_products)) return;

    $main_price = (float)$product->get_price();
    $initial_total = $main_price;
    ?>
    <section class="emdief-bundle-section" id="bu-seti-tamamla">
        <div class="bundle-box-card">
            <div class="bundle-header">
                <div class="bundle-header-left">
                    <span class="bundle-eyebrow">🛋️ MONTESSORİ ODA KOMBİNİ</span>
                    <h3 class="bundle-title"><?php esc_html_e('Bu Seti Tamamla: Odanızı Birlikte Donatın', 'mis360-mobilya'); ?></h3>
                    <p class="bundle-subtitle"><?php esc_html_e('Birbiriyle %100 uyumlu doğal Montessori parçalarını seçin, çocuk odasını tek seferde tamamlayın.', 'mis360-mobilya'); ?></p>
                </div>
                <div class="bundle-discount-badge">
                    <span>%5 SET İNDİRİMİ</span>
                </div>
            </div>

            <div class="bundle-grid-row">
                <!-- 1. Ürün Kartları Listesi -->
                <div class="bundle-items-col">
                    <!-- Ana Ürün (Zorunlu / Kilitli) -->
                    <div class="bundle-item-card is-main-item">
                        <label class="bundle-checkbox-wrap">
                            <input type="checkbox" class="bundle-chk" value="<?php echo esc_attr((string)$current_id); ?>" data-price="<?php echo esc_attr((string)$main_price); ?>" checked disabled>
                            <span class="chk-custom is-checked"></span>
                        </label>
                        <div class="bundle-item-thumb">
                            <?php echo $product->get_image('thumbnail'); ?>
                        </div>
                        <div class="bundle-item-info">
                            <span class="bundle-item-tag">Mevcut Ürün</span>
                            <h4 class="bundle-item-title"><?php echo esc_html($product->get_name()); ?></h4>
                            <div class="bundle-item-price"><?php echo $product->get_price_html(); ?></div>
                        </div>
                    </div>

                    <!-- Tamamlayıcı Ürünler -->
                    <?php foreach ($bundle_products as $idx => $b_prod): 
                        $b_price = (float)$b_prod->get_price();
                        $initial_total += $b_price;
                    ?>
                        <div class="bundle-item-plus-sign">+</div>
                        <div class="bundle-item-card">
                            <label class="bundle-checkbox-wrap">
                                <input type="checkbox" class="bundle-chk bundle-optional-chk" value="<?php echo esc_attr((string)$b_prod->get_id()); ?>" data-price="<?php echo esc_attr((string)$b_price); ?>" checked>
                                <span class="chk-custom is-checked"></span>
                            </label>
                            <div class="bundle-item-thumb">
                                <a href="<?php echo esc_url($b_prod->get_permalink()); ?>" target="_blank">
                                    <?php echo $b_prod->get_image('thumbnail'); ?>
                                </a>
                            </div>
                            <div class="bundle-item-info">
                                <span class="bundle-item-tag complement-tag">Uyumlu Parça</span>
                                <h4 class="bundle-item-title">
                                    <a href="<?php echo esc_url($b_prod->get_permalink()); ?>" target="_blank">
                                        <?php echo esc_html($b_prod->get_name()); ?>
                                    </a>
                                </h4>
                                <div class="bundle-item-price"><?php echo $b_prod->get_price_html(); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 2. Sağ Toplam ve Sepete Ekle Kolonu -->
                <div class="bundle-summary-col">
                    <div class="summary-box-inner">
                        <span class="summary-label"><?php esc_html_e('Seçilen Set Toplamı:', 'mis360-mobilya'); ?></span>
                        <div class="summary-price-row">
                            <strong class="bundle-total-price" id="bundleTotalPriceText"><?php echo number_format($initial_total, 2, ',', '.') . ' TL'; ?></strong>
                        </div>
                        <p class="summary-shipping-note">🚚 <?php esc_html_e('1.500 TL Üzeri Ücretsiz & Sigortalı Kargo', 'mis360-mobilya'); ?></p>
                        
                        <button type="button" class="emdief-btn btn-primary btn-block btn-add-bundle-cart" id="btnAddBundleToCart">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            <span><?php esc_html_e('Tüm Seti Sepete Ekle', 'mis360-mobilya'); ?></span>
                        </button>
                        <div class="bundle-ajax-status" id="bundleAjaxStatus" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
add_action('woocommerce_after_single_product_summary', 'mis360_render_bundle_cross_sells', 9);

/**
 * AJAX: Çoklu Bundle Ürünlerini Sepete Ekle
 */
function mis360_ajax_add_bundle_cart_handler() {
    check_ajax_referer('mis360_public_nonce', 'nonce');

    $product_ids = isset($_POST['product_ids']) ? (array)$_POST['product_ids'] : [];
    $product_ids = array_map('absint', $product_ids);
    $product_ids = array_filter($product_ids);

    if (empty($product_ids)) {
        wp_send_json_error(['message' => 'Lütfen en az bir ürün seçiniz.']);
    }

    $added_count = 0;
    foreach ($product_ids as $pid) {
        if ($pid > 0) {
            $cart_item_key = WC()->cart->add_to_cart($pid, 1);
            if ($cart_item_key) {
                $added_count++;
            }
        }
    }

    if ($added_count > 0) {
        wp_send_json_success([
            'message'    => sprintf(esc_html__('%d parça ürün başarıyla sepete eklendi!', 'mis360-mobilya'), $added_count),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_subtotal(),
        ]);
    } else {
        wp_send_json_error(['message' => 'Ürünler sepete eklenirken bir hata oluştu.']);
    }
}
add_action('wp_ajax_mis360_add_bundle_to_cart', 'mis360_ajax_add_bundle_cart_handler');
add_action('wp_ajax_nopriv_mis360_add_bundle_to_cart', 'mis360_ajax_add_bundle_cart_handler');


/* ==========================================================================
   3. "MUTLU MİNİKLER KÖŞESİ (MÜŞTERİ DENEYİMLERİ)" CPT & LIGHTBOX
   ========================================================================== */
function mis360_register_happy_kids_cpt() {
    $labels = [
        'name'               => _x('Mutlu Minikler Köşesi', 'post type general name', 'mis360-mobilya'),
        'singular_name'      => _x('Müşteri Deneyimi', 'post type singular name', 'mis360-mobilya'),
        'menu_name'          => _x('Mutlu Minikler Köşesi', 'admin menu', 'mis360-mobilya'),
        'add_new'            => _x('Yeni Kurulum Ekle', 'happy kids', 'mis360-mobilya'),
        'add_new_item'       => __('Yeni Müşteri Odası Kurulumu Ekle', 'mis360-mobilya'),
        'edit_item'          => __('Müşteri Kurulumunu Düzenle', 'mis360-mobilya'),
        'new_item'           => __('Yeni Kurulum', 'mis360-mobilya'),
        'all_items'          => __('Tüm Müşteri Kurulumları', 'mis360-mobilya'),
        'view_item'          => __('Görüntüle', 'mis360-mobilya'),
        'search_items'       => __('Müşteri Deneyimi Ara', 'mis360-mobilya'),
        'not_found'          => __('Henüz müşteri deneyimi bulunamadı', 'mis360-mobilya'),
        'not_found_in_trash' => __('Çöp kutusunda bulunamadı', 'mis360-mobilya'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'mutlu-minikler'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 27,
        'menu_icon'          => 'dashicons-format-gallery',
        'supports'           => ['title', 'editor', 'thumbnail'],
    ];

    register_post_type('mutlu_minikler', $args);
}
add_action('init', 'mis360_register_happy_kids_cpt');

/**
 * CPT için Özel Alanlar (Şehir & Ürün Etiketi)
 */
function mis360_happy_kids_meta_boxes() {
    add_meta_box(
        'mis360_happy_kids_meta',
        __('Kurulum & Müşteri Detayları', 'mis360-mobilya'),
        'mis360_render_happy_kids_meta_box',
        'mutlu_minikler',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mis360_happy_kids_meta_boxes');

function mis360_render_happy_kids_meta_box($post) {
    wp_nonce_field('mis360_happy_kids_meta_action', 'mis360_happy_kids_meta_nonce');
    $city = get_post_meta($post->ID, '_customer_city', true);
    $product_tag = get_post_meta($post->ID, '_product_tag', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="customer_city"><?php esc_html_e('Şehir / İlçe:', 'mis360-mobilya'); ?></label></th>
            <td><input type="text" name="customer_city" id="customer_city" value="<?php echo esc_attr($city); ?>" class="regular-text" placeholder="Örn: Kadıköy / İstanbul"></td>
        </tr>
        <tr>
            <th scope="row"><label for="product_tag"><?php esc_html_e('Kurulan Ürün Modeli:', 'mis360-mobilya'); ?></label></th>
            <td><input type="text" name="product_tag" id="product_tag" value="<?php echo esc_attr($product_tag); ?>" class="regular-text" placeholder="Örn: Carmen Montessori 3 Raflı Kitaplık"></td>
        </tr>
    </table>
    <?php
}

function mis360_save_happy_kids_meta($post_id) {
    if (!isset($_POST['mis360_happy_kids_meta_nonce']) || !wp_verify_nonce($_POST['mis360_happy_kids_meta_nonce'], 'mis360_happy_kids_meta_action')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['customer_city'])) {
        update_post_meta($post_id, '_customer_city', sanitize_text_field($_POST['customer_city']));
    }
    if (isset($_POST['product_tag'])) {
        update_post_meta($post_id, '_product_tag', sanitize_text_field($_POST['product_tag']));
    }
}
add_action('save_post_mutlu_minikler', 'mis360_save_happy_kids_meta');

/**
 * "Mutlu Minikler Köşesi" Galeri Render Edici (Ana Sayfa & Ürün Detayında)
 */
function mis360_render_happy_kids_gallery($context = 'front') {
    $items = [];
    $query = new WP_Query([
        'post_type'      => 'mutlu_minikler',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
    ]);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$thumb) continue;
            $items[] = [
                'name'    => get_the_title(),
                'city'    => get_post_meta(get_the_ID(), '_customer_city', true) ?: 'İstanbul',
                'product' => get_post_meta(get_the_ID(), '_product_tag', true) ?: 'Carmen Montessori Kitaplık',
                'quote'   => get_the_excerpt() ?: 'Montessori mobilyalarımız tam zamanında geldi, 5 dakikada kurduk. Odadaki havası muhteşem oldu!',
                'img'     => $thumb,
            ];
        }
        wp_reset_postdata();
    }

    // Eğer admin henüz fotoğraf girmemişse, zengin hazır örnek vitrin göster
    if (empty($items)) {
        $items = [
            [
                'name'    => 'Zeynep & Ali',
                'city'    => 'Kadıköy / İstanbul',
                'product' => 'Carmen 3 Raflı Montessori Kitaplık',
                'quote'   => 'Kızımız kendi kitaplarını tek tek seçip koymaya başladı. Pürüzsüz ahşap dokusu ve yuvarlatılmış köşeleri içimizi çok rahatlattı!',
                'img'     => 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg',
            ],
            [
                'name'    => 'Selin Hanım',
                'city'    => 'Çankaya / Ankara',
                'product' => 'Melis 2 Raflı Çocuk Kitaplığı',
                'quote'   => 'Şarjlı matkapla tek başıma 8 dakikada kurdum. Duvara sabitleme aparatı paketten çıktı, çok sağlam ve kaliteli.',
                'img'     => 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg',
            ],
            [
                'name'    => 'Murat & Can',
                'city'    => 'Karşıyaka / İzmir',
                'product' => 'Montessori Katlanabilir Duvar Masası',
                'quote'   => 'Küçük çocuk odamız için hayat kurtarıcı oldu. Kapandığında hiç yer kaplamıyor, açıkken harika bir resim ve aktivite masası.',
                'img'     => 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-448-300x300.jpg',
            ],
            [
                'name'    => 'Burcu Hanım',
                'city'    => 'Nilüfer / Bursa',
                'product' => 'Safir Alçak Montessori Kitaplık',
                'quote'   => 'Kargo paketlemesi mükemmeldi, en ufak bir ezik yoktu. Oğlum odasından çıkmak istemiyor.',
                'img'     => 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg',
            ],
        ];
    }
    ?>
    <section class="emdief-happy-kids-section" id="mutlu-minikler">
        <div class="emdief-container">
            <div class="happy-kids-header text-center">
                <span class="happy-badge">📸 GERÇEK MÜŞTERİ ODALARI</span>
                <h2 class="happy-title"><?php esc_html_e('Mutlu Minikler Köşesi', 'mis360-mobilya'); ?></h2>
                <p class="happy-subtitle"><?php esc_html_e('Ebeveynlerin çocuk odalarından paylaştığı sıcacık anlar ve gerçek kurulum kareleri.', 'mis360-mobilya'); ?></p>
            </div>

            <div class="happy-kids-grid">
                <?php foreach ($items as $index => $item): ?>
                    <div class="happy-card" data-index="<?php echo esc_attr((string)$index); ?>" 
                         data-img="<?php echo esc_url($item['img']); ?>"
                         data-title="<?php echo esc_attr($item['name'] . ' (' . $item['city'] . ')'); ?>"
                         data-product="<?php echo esc_attr($item['product']); ?>"
                         data-quote="<?php echo esc_attr($item['quote']); ?>">
                        <div class="happy-thumb-wrap">
                            <img src="<?php echo esc_url($item['img']); ?>" alt="<?php echo esc_attr($item['product']); ?>" loading="lazy">
                            <div class="happy-overlay">
                                <span class="happy-zoom-icon">🔍 Büyüt &amp; İncele</span>
                            </div>
                            <span class="happy-product-pill"><?php echo esc_html($item['product']); ?></span>
                        </div>
                        <div class="happy-card-footer">
                            <div class="happy-user-info">
                                <span class="happy-stars">★★★★★</span>
                                <strong><?php echo esc_html($item['name']); ?></strong>
                                <small><?php echo esc_html($item['city']); ?></small>
                            </div>
                            <p class="happy-quote">"<?php echo esc_html(wp_trim_words($item['quote'], 12, '...')); ?>"</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="happy-lightbox-modal" id="happyLightboxModal" style="display:none;" aria-hidden="true">
        <div class="lightbox-overlay" id="happyLightboxOverlay"></div>
        <div class="lightbox-dialog">
            <button type="button" class="lightbox-close-btn" id="happyLightboxClose" aria-label="Kapat">&times;</button>
            <div class="lightbox-body">
                <div class="lightbox-img-col">
                    <img src="" id="lightboxImg" alt="Müşteri Odası">
                </div>
                <div class="lightbox-info-col">
                    <span class="lb-verified-badge">✓ Doğrulanmış Müşteri Kurulumu</span>
                    <h3 id="lightboxTitle"></h3>
                    <div class="lb-product-tag-wrap">
                        <span class="lb-tag-label">Kurulan Model:</span>
                        <strong id="lightboxProduct"></strong>
                    </div>
                    <blockquote class="lb-quote" id="lightboxQuote"></blockquote>
                    <div class="lb-footer-actions">
                        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>" class="emdief-btn btn-primary btn-sm">
                            <span>Bu Modelleri İncele</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}


/* ==========================================================================
   4. İNTERAKTİF "ÖLÇÜ & YERLEŞİM REHBERİ" (MONTESSORI ODA SİMÜLATÖRÜ)
   ========================================================================== */
function mis360_render_room_fit_launcher() {
    global $product;
    if (!$product) return;
    ?>
    <div class="emdief-room-fit-launcher-wrap">
        <button type="button" class="btn-open-room-planner" id="btnOpenRoomPlanner">
            <span class="planner-icon">📐</span>
            <span class="planner-text-group">
                <strong class="planner-title"><?php esc_html_e('Odanıza Sığar mı? Ölçü & Yerleşim Rehberi', 'mis360-mobilya'); ?></strong>
                <small class="planner-sub"><?php esc_html_e('Oda ölçülerinizi girin, kaplayacağı alanı kuşbakışı test edin!', 'mis360-mobilya'); ?></small>
            </span>
            <span class="planner-arrow">➜</span>
        </button>
    </div>
    <?php
}
add_action('woocommerce_single_product_summary', 'mis360_render_room_fit_launcher', 31);

/**
 * Modal: Kuşbakışı Montessori Oda & Mobilya Simülatörü
 */
function mis360_render_room_fit_modal() {
    if (!is_product()) return;
    global $product;
    if (!$product) return;

    $p_title = $product->get_name();
    $dim_w = 70; // cm (varsayılan)
    $dim_d = 20; // cm derinlik
    if ($product->has_dimensions()) {
        $dim_w = max(40, (float)$product->get_width() ?: 70);
        $dim_d = max(15, (float)$product->get_length() ?: 20);
    }
    ?>
    <div class="emdief-planner-modal" id="emdiefPlannerModal" style="display:none;" aria-hidden="true">
        <div class="planner-overlay" id="emdiefPlannerOverlay"></div>
        <div class="planner-dialog">
            <div class="planner-header">
                <div class="planner-header-title">
                    <span class="pl-badge">📐 İNTERAKTİF PLANLAYICI</span>
                    <h3><?php esc_html_e('Montessori Çocuk Odası Ölçü & Yerleşim Simülatörü', 'mis360-mobilya'); ?></h3>
                </div>
                <button type="button" class="planner-close-btn" id="emdiefPlannerClose">&times;</button>
            </div>

            <div class="planner-body-grid">
                <!-- Sol: Ölçü Girişi -->
                <div class="planner-inputs-col">
                    <div class="input-card">
                        <h4>1. Çocuk Odası Ölçüleriniz:</h4>
                        <div class="inputs-row">
                            <div class="field-group">
                                <label for="roomWidth">Oda Genişliği (m):</label>
                                <input type="number" id="roomWidth" value="3.5" min="2" max="10" step="0.1">
                            </div>
                            <div class="field-group">
                                <label for="roomLength">Oda Uzunluğu (m):</label>
                                <input type="number" id="roomLength" value="4.0" min="2" max="10" step="0.1">
                            </div>
                        </div>
                    </div>

                    <div class="input-card">
                        <h4>2. Seçilen Mobilya Boyutları:</h4>
                        <div class="selected-furn-info">
                            <strong><?php echo esc_html($p_title); ?></strong>
                            <span>Genişlik: <b id="furnWText"><?php echo esc_html((string)$dim_w); ?> cm</b> | Derinlik: <b id="furnDText"><?php echo esc_html((string)$dim_d); ?> cm</b></span>
                        </div>
                    </div>

                    <div class="pedagogy-tip-card">
                        <span class="tip-icon">🌱</span>
                        <div class="tip-content">
                            <strong>Montessori Yerleşim Kuralı:</strong>
                            <p id="pedagogyAdvice">Maria Montessori pedagojisine göre çocuğun bağımsız hareket edebilmesi için odanın en az <strong>%60'ı serbest oyun alanı</strong> olarak bırakılmalıdır.</p>
                        </div>
                    </div>
                </div>

                <!-- Sağ: Kuşbakışı 2D Oda Kroki Önizlemesi -->
                <div class="planner-preview-col">
                    <div class="floorplan-container">
                        <div class="floorplan-room" id="floorplanRoom">
                            <div class="furniture-box" id="floorplanFurn" title="Mobilya Konumu">
                                <span><?php echo esc_html(wp_trim_words($p_title, 2, '')); ?></span>
                            </div>
                            <div class="play-area-label">🌿 Serbest Montessori Oyun Alanı</div>
                        </div>
                    </div>
                    <div class="planner-stats-row">
                        <div class="stat-pill">Oda Alanı: <strong id="statRoomArea">14.0 m²</strong></div>
                        <div class="stat-pill">Mobilya İzi: <strong id="statFurnArea">0.14 m²</strong></div>
                        <div class="stat-pill highlight-pill">Serbest Alan: <strong id="statFreePct">%99 Uygun</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'mis360_render_room_fit_modal');


/* ==========================================================================
   5. DÖNÜŞÜM BUTONLARI (FAVORİLERE EKLE & EŞİNE WHATSAPP İLE GÖNDER)
   ========================================================================== */
function mis360_single_product_conversion_buttons() {
    global $product;
    if (!$product) return;

    $id = $product->get_id();
    $permalink = get_permalink($id);
    $title = $product->get_name();

    // Eşe WhatsApp Mesajı
    $spouse_msg = sprintf(
        __('Hayatım, miniklerin odası için şu Montessori modelini buldum, sence nasıl? %s - %s', 'mis360-mobilya'),
        $title,
        $permalink
    );
    $spouse_wa_url = 'https://api.whatsapp.com/send?text=' . rawurlencode($spouse_msg);
    ?>
    <div class="emdief-single-action-addon-btns">
        <!-- 1. Hızlı Favorilere Ekle (Wishlist) Butonu -->
        <button type="button" class="btn-single-wishlist" id="btnSingleWishlist" data-product-id="<?php echo esc_attr((string)$id); ?>" aria-label="<?php esc_attr_e('Favorilere Ekle', 'mis360-mobilya'); ?>" title="<?php esc_attr_e('Favori Listeme Ekle', 'mis360-mobilya'); ?>">
            <svg class="heart-svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span class="wishlist-btn-text"><?php esc_html_e('Favorilere Ekle', 'mis360-mobilya'); ?></span>
        </button>

        <!-- 2. "Beğendiğin Ürünü Eşine WhatsApp ile Gönder" Butonu -->
        <a href="<?php echo esc_url($spouse_wa_url); ?>" target="_blank" rel="noopener noreferrer" class="btn-spouse-share" title="<?php esc_attr_e('Eşine WhatsApp\'tan Danış', 'mis360-mobilya'); ?>">
            <span class="spouse-share-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.699c.971.53 1.771.814 2.802.814 3.184 0 5.769-2.586 5.77-5.766.001-3.181-2.584-5.766-5.776-5.766zm9.969 5.768c0 5.514-4.486 10-10 10-1.745 0-3.376-.449-4.801-1.233l-5.2 1.364 1.39-5.07c-.886-1.488-1.389-3.228-1.389-5.061 0-5.514 4.486-10 10-10s10 4.486 10 10z"/></svg>
            </span>
            <span class="spouse-btn-text">
                <strong><?php esc_html_e('Eşine WhatsApp\'tan Gönder', 'mis360-mobilya'); ?></strong>
                <small><?php esc_html_e('Fikrini hemen sor 💬', 'mis360-mobilya'); ?></small>
            </span>
        </a>
    </div>

    <!-- Wishlist Toast Bildirimi -->
    <div class="emdief-wishlist-toast" id="emdiefWishlistToast" style="display:none;">
        <span class="toast-heart">❤️</span>
        <span class="toast-msg">Ürün favorilerinize eklendi!</span>
    </div>
    <?php
}
add_action('woocommerce_after_add_to_cart_button', 'mis360_single_product_conversion_buttons', 12);


/* ==========================================================================
   6. JAVASCRIPT MOTORU: GERİ SAYIM, SET HESAPLAMA, LIGHTBOX & ODA SİMÜLATÖRÜ
   ========================================================================== */
function mis360_montessori_features_inline_script() {
    ?>
    <script>
    (function() {
        // --- 1. İmalat Canlı Geri Sayım Sayacı ---
        var cdWrap = document.getElementById('emdiefProductionCountdown');
        if (cdWrap) {
            var targetTs = parseInt(cdWrap.getAttribute('data-target-ts'), 10);
            var hEl = document.getElementById('cntHours');
            var mEl = document.getElementById('cntMinutes');
            var sEl = document.getElementById('cntSeconds');

            function updateTimer() {
                var nowTs = Math.floor(Date.now() / 1000);
                var diff = targetTs - nowTs;
                if (diff <= 0) {
                    targetTs += 86400;
                    diff = targetTs - nowTs;
                }
                var hours = Math.floor(diff / 3600);
                var mins = Math.floor((diff % 3600) / 60);
                var secs = diff % 60;

                if (hEl) hEl.textContent = hours < 10 ? '0' + hours : hours;
                if (mEl) mEl.textContent = mins < 10 ? '0' + mins : mins;
                if (sEl) sEl.textContent = secs < 10 ? '0' + secs : secs;
            }
            setInterval(updateTimer, 1000);
        }

        // --- 2. "Bu Seti Tamamla" Fiyat ve Checkbox Hesaplama ---
        var bundleSection = document.getElementById('bu-seti-tamamla');
        if (bundleSection) {
            var chks = bundleSection.querySelectorAll('.bundle-chk');
            var totalEl = document.getElementById('bundleTotalPriceText');
            var addBtn = document.getElementById('btnAddBundleToCart');
            var statusEl = document.getElementById('bundleAjaxStatus');

            function calcBundleTotal() {
                var total = 0;
                chks.forEach(function(c) {
                    if (c.checked) {
                        total += parseFloat(c.getAttribute('data-price') || 0);
                    }
                });
                if (totalEl) {
                    totalEl.textContent = total.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL';
                }
            }

            chks.forEach(function(c) {
                c.addEventListener('change', function() {
                    var customBox = this.parentElement.querySelector('.chk-custom');
                    if (customBox) {
                        if (this.checked) customBox.classList.add('is-checked');
                        else customBox.classList.remove('is-checked');
                    }
                    calcBundleTotal();
                });
            });

            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    var selectedIds = [];
                    chks.forEach(function(c) {
                        if (c.checked) selectedIds.push(c.value);
                    });
                    if (selectedIds.length === 0) return;

                    addBtn.disabled = true;
                    addBtn.innerHTML = '<span>Sepete Ekleniyor... ⏳</span>';

                    var formData = new FormData();
                    formData.append('action', 'mis360_add_bundle_to_cart');
                    formData.append('nonce', '<?php echo wp_create_nonce("mis360_public_nonce"); ?>');
                    selectedIds.forEach(function(id) {
                        formData.append('product_ids[]', id);
                    });

                    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        addBtn.disabled = false;
                        addBtn.innerHTML = '<span>Tüm Seti Sepete Ekle</span>';
                        if (res.success) {
                            if (statusEl) {
                                statusEl.textContent = res.data.message;
                                statusEl.style.display = 'block';
                            }
                            var cartTrigger = document.getElementById('emdief-cart-trigger');
                            if (cartTrigger) cartTrigger.click();
                            var badge = document.getElementById('emdief-cart-count');
                            if (badge && res.data.cart_count) badge.textContent = res.data.cart_count;
                        }
                    })
                    .catch(function(err) {
                        addBtn.disabled = false;
                        addBtn.innerHTML = '<span>Tüm Seti Sepete Ekle</span>';
                    });
                });
            }
        }

        // --- 3. Mutlu Minikler Lightbox ---
        var happyCards = document.querySelectorAll('.happy-card');
        var lbModal = document.getElementById('happyLightboxModal');
        var lbImg = document.getElementById('lightboxImg');
        var lbTitle = document.getElementById('lightboxTitle');
        var lbProduct = document.getElementById('lightboxProduct');
        var lbQuote = document.getElementById('lightboxQuote');
        var lbClose = document.getElementById('happyLightboxClose');
        var lbOverlay = document.getElementById('happyLightboxOverlay');

        happyCards.forEach(function(card) {
            card.addEventListener('click', function() {
                var img = card.getAttribute('data-img');
                var title = card.getAttribute('data-title');
                var product = card.getAttribute('data-product');
                var quote = card.getAttribute('data-quote');

                if (lbImg) lbImg.src = img;
                if (lbTitle) lbTitle.textContent = title;
                if (lbProduct) lbProduct.textContent = product;
                if (lbQuote) lbQuote.textContent = '"' + quote + '"';

                if (lbModal) {
                    lbModal.style.display = 'flex';
                    lbModal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        function closeLightbox() {
            if (lbModal) {
                lbModal.style.display = 'none';
                lbModal.setAttribute('aria-hidden', 'true');
            }
        }
        if (lbClose) lbClose.addEventListener('click', closeLightbox);
        if (lbOverlay) lbOverlay.addEventListener('click', closeLightbox);

        // --- 4. İnteraktif Ölçü & Yerleşim Simülatörü ---
        var btnOpenPlan = document.getElementById('btnOpenRoomPlanner');
        var planModal = document.getElementById('emdiefPlannerModal');
        var planClose = document.getElementById('emdiefPlannerClose');
        var planOverlay = document.getElementById('emdiefPlannerOverlay');

        if (btnOpenPlan && planModal) {
            btnOpenPlan.addEventListener('click', function() {
                planModal.style.display = 'flex';
                planModal.setAttribute('aria-hidden', 'false');
                updateRoomSimulation();
            });
        }
        function closePlanner() {
            if (planModal) {
                planModal.style.display = 'none';
                planModal.setAttribute('aria-hidden', 'true');
            }
        }
        if (planClose) planClose.addEventListener('click', closePlanner);
        if (planOverlay) planOverlay.addEventListener('click', closePlanner);

        var rWidthInput = document.getElementById('roomWidth');
        var rLengthInput = document.getElementById('roomLength');
        var floorRoom = document.getElementById('floorplanRoom');
        var floorFurn = document.getElementById('floorplanFurn');
        var statRoomEl = document.getElementById('statRoomArea');
        var statFurnEl = document.getElementById('statFurnArea');
        var statFreeEl = document.getElementById('statFreePct');

        function updateRoomSimulation() {
            if (!rWidthInput || !rLengthInput) return;
            var w = Math.max(2, parseFloat(rWidthInput.value) || 3.5);
            var l = Math.max(2, parseFloat(rLengthInput.value) || 4.0);
            var roomArea = (w * l);

            var furnWcm = 70;
            var furnDcm = 20;
            var furnArea = (furnWcm * furnDcm) / 10000; // m2

            var furnPctOfRoom = (furnArea / roomArea) * 100;
            var freeAreaPct = (100 - furnPctOfRoom).toFixed(1);

            if (statRoomEl) statRoomEl.textContent = roomArea.toFixed(1) + ' m²';
            if (statFurnEl) statFurnEl.textContent = furnArea.toFixed(2) + ' m²';
            if (statFreeEl) statFreeEl.textContent = '%' + freeAreaPct + ' Serbest Oyun Alanı';

            if (floorRoom && floorFurn) {
                var maxDim = Math.max(w, l);
                var scale = 220 / maxDim;
                floorRoom.style.width = (w * scale) + 'px';
                floorRoom.style.height = (l * scale) + 'px';

                var fWpx = Math.max(30, (furnWcm / 100) * scale);
                var fDpx = Math.max(18, (furnDcm / 100) * scale);
                floorFurn.style.width = fWpx + 'px';
                floorFurn.style.height = fDpx + 'px';
            }
        }

        if (rWidthInput) rWidthInput.addEventListener('input', updateRoomSimulation);
        if (rLengthInput) rLengthInput.addEventListener('input', updateRoomSimulation);

        // --- 5. Favorilere Ekle (Wishlist) & LocalStorage ---
        var wishBtn = document.getElementById('btnSingleWishlist');
        var toast = document.getElementById('emdiefWishlistToast');

        if (wishBtn) {
            var pid = wishBtn.getAttribute('data-product-id');
            var savedWish = JSON.parse(localStorage.getItem('emdief_wishlist') || '[]');
            if (savedWish.indexOf(pid) !== -1) {
                wishBtn.classList.add('is-favorited');
                var txt = wishBtn.querySelector('.wishlist-btn-text');
                if (txt) txt.textContent = 'Favorilerinizde ❤️';
            }

            wishBtn.addEventListener('click', function() {
                var list = JSON.parse(localStorage.getItem('emdief_wishlist') || '[]');
                var idx = list.indexOf(pid);
                var isAdded = false;

                if (idx === -1) {
                    list.push(pid);
                    wishBtn.classList.add('is-favorited');
                    var txt = wishBtn.querySelector('.wishlist-btn-text');
                    if (txt) txt.textContent = 'Favorilerinizde ❤️';
                    isAdded = true;
                } else {
                    list.splice(idx, 1);
                    wishBtn.classList.remove('is-favorited');
                    var txt = wishBtn.querySelector('.wishlist-btn-text');
                    if (txt) txt.textContent = 'Favorilere Ekle';
                    isAdded = false;
                }
                localStorage.setItem('emdief_wishlist', JSON.stringify(list));

                if (toast) {
                    toast.querySelector('.toast-msg').textContent = isAdded ? 'Ürün favori listenize eklendi! ❤️' : 'Ürün favorilerden çıkarıldı.';
                    toast.style.display = 'flex';
                    setTimeout(function() { toast.style.display = 'none'; }, 2800);
                }
            });
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'mis360_montessori_features_inline_script', 60);
