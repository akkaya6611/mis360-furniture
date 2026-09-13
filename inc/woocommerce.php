<?php
/**
 * WooCommerce Customizations & AJAX Mini Cart Engine
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sepet İkonu ve Sayacı (Header için AJAX Fragmanı)
 */
function mis360_cart_count_fragment($fragments) {
    if (!is_array($fragments)) {
        $fragments = [];
    }
    $count = (function_exists('WC') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0';
    $fragments['#emdief-cart-count'] = '<span class="emdief-cart-count" id="emdief-cart-count">' . $count . '</span>';
    $fragments['#emdief-bottom-cart-count'] = '<span class="bottom-cart-badge" id="emdief-bottom-cart-count">' . $count . '</span>';
    $fragments['#emdief-drawer-count-badge'] = '<span class="drawer-count-badge" id="emdief-drawer-count-badge">' . sprintf(esc_html__('%s ürün', 'mis360-mobilya'), $count) . '</span>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'mis360_cart_count_fragment');

/**
 * Para Birimi Simgesini ₺ yerine TL olarak ayarla
 */
function mis360_turkish_lira_currency_symbol($currency_symbol, $currency) {
    if ($currency === 'TRY') {
        return 'TL';
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'mis360_turkish_lira_currency_symbol', 10, 2);

/**
 * Mini-Cart Çekmece Fragmanı (Drawer Cart İçeriği)
 */
function mis360_drawer_cart_fragment($fragments) {
    if (!is_array($fragments)) {
        $fragments = [];
    }
    ob_start();
    mis360_render_drawer_cart_content();
    $fragments['#emdief-drawer-cart-content'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'mis360_drawer_cart_fragment');

/**
 * Çekmece Sepet İçeriği HTML Üreticisi
 */
function mis360_render_drawer_cart_content() {
    $cart = (function_exists('WC') && WC()) ? WC()->cart : null;
    $free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
    $cart_subtotal = ($cart && method_exists($cart, 'get_subtotal')) ? (float) $cart->get_subtotal() : 0.0;
    $diff = $free_shipping_limit - $cart_subtotal;
    $percent = min(100, max(0, ($cart_subtotal / ($free_shipping_limit ?: 1)) * 100));
    ?>
    <div id="emdief-drawer-cart-content" class="emdief-drawer-body">
        <!-- Maskottan Mesaj Var: Canlı Kargo Tavsiye Kutusu -->
        <div class="mascot-speech-bubble-box <?php echo ($diff <= 0 && $cart_subtotal > 0) ? 'is-free-shipping' : ''; ?>">
            <div class="mascot-bubble-avatar-col">
                <div class="mascot-avatar-wrapper">
                    <?php echo function_exists('mis360_teddy_bear_avatar') ? mis360_teddy_bear_avatar(44, 'mascot-chat-bear') : '🧸'; ?>
                    <span class="mascot-live-indicator" title="Maskot Çevrimiçi"></span>
                </div>
            </div>
            <div class="mascot-speech-body">
                <div class="mascot-speech-top">
                    <span class="mascot-label-tag">
                        <span class="tag-pulse"></span>
                        💬 MASKOTTAN MESAJ VAR!
                    </span>
                    <span class="mascot-time-tag">Canlı İpucu</span>
                </div>
                <div class="mascot-speech-msg">
                    <?php if ($diff <= 0 && $cart_subtotal > 0): ?>
                        🎉 <strong>Harika seçim!</strong> Sepetiniz <strong>ÜCRETSİZ KARGO</strong> kazandı, kargo ücreti ödemeyeceksiniz!
                    <?php elseif ($cart_subtotal > 0): ?>
                        🧸 <em>"Sepetinize <strong><?php echo function_exists('wc_price') ? wc_price(max(0, $diff)) : max(0, $diff) . ' TL'; ?></strong> değerinde daha ürün ekleyin, kargo ücreti ödemeyin!"</em>
                    <?php else: ?>
                        🧸 <em>"<strong>1.500 TL</strong> üzeri tüm siparişlerde kargo bizden hediye! Miniklerin odasını donatın, kargo ücreti ödemeyin!"</em>
                    <?php endif; ?>
                </div>
                <div class="mascot-meter-wrap">
                    <div class="mascot-meter-track">
                        <div class="mascot-meter-fill <?php echo ($diff <= 0 && $cart_subtotal > 0) ? 'full' : ''; ?>" style="width: <?php echo esc_attr((string) $percent); ?>%;"></div>
                    </div>
                    <span class="mascot-meter-status">
                        <?php if ($diff <= 0 && $cart_subtotal > 0): ?>
                            %100 Ücretsiz Kargo
                        <?php else: ?>
                            %<?php echo round($percent); ?> Tamamlandı
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Sepetteki Ürünler -->
        <?php if ($cart && !$cart->is_empty()): ?>
            <div class="emdief-drawer-items">
                <?php
                foreach ($cart->get_cart() as $cart_item_key => $cart_item):
                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)):
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                        $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                        ?>
                        <div class="emdief-drawer-item">
                            <div class="item-thumb">
                                <?php if (!empty($product_permalink)): ?>
                                    <a href="<?php echo esc_url($product_permalink); ?>"><?php echo $thumbnail; ?></a>
                                <?php else: ?>
                                    <?php echo $thumbnail; ?>
                                <?php endif; ?>
                            </div>
                            <div class="item-info">
                                <a href="<?php echo esc_url($product_permalink); ?>" class="item-title">
                                    <?php echo esc_html($product_name); ?>
                                </a>
                                <div class="item-price">
                                    <?php echo esc_html($cart_item['quantity']); ?> &times; <?php echo $product_price; ?>
                                </div>
                            </div>
                            <div class="item-remove">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove-cart-item" aria-label="%s" data-product_id="%s" data-cart_item_key="%s">&times;</a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_attr__('Bu ürünü sepetten çıkar', 'mis360-mobilya'),
                                        esc_attr((string) $product_id),
                                        esc_attr($cart_item_key)
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Sepet Alt Toplam & Butonlar -->
            <div class="emdief-drawer-footer">
                <div class="drawer-subtotal">
                    <span><?php esc_html_e('Ara Toplam:', 'mis360-mobilya'); ?></span>
                    <strong><?php echo $cart->get_cart_subtotal(); ?></strong>
                </div>
                <div class="drawer-actions">
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="emdief-btn btn-outline btn-block">
                        <?php esc_html_e('Sepeti Görüntüle', 'mis360-mobilya'); ?>
                    </a>
                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="emdief-btn btn-primary btn-block">
                        <?php esc_html_e('Siparişi Tamamla', 'mis360-mobilya'); ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="emdief-cart-empty">
                <div class="empty-bear-wrap">
                    <?php echo mis360_crying_bear(125, 115, 'animated-drawer-crying-bear'); ?>
                </div>
                <div class="empty-bear-badge">🥺 Ayıcık Ağlıyor!</div>
                <h3><?php esc_html_e('Sepetiniz Bomboş Kaldı...', 'mis360-mobilya'); ?></h3>
                <p><?php esc_html_e('Miniklerin odasına neşe ve düzen katacak 1. sınıf MDF Montessori mobilyalarımızı ekleyin, sevimli ayıcığımızın gözyaşları dinsin!', 'mis360-mobilya'); ?></p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="emdief-btn btn-primary btn-md">
                    <span><?php esc_html_e('Ürünleri Keşfet', 'mis360-mobilya'); ?></span>
                    <?php echo mis360_icon('arrow-right', 16); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * AJAX ile Çekmeceden Ürün Çıkarma (Remove Cart Item via AJAX)
 */
function mis360_ajax_remove_cart_item() {
    check_ajax_referer('mis360_cart_nonce', 'nonce');

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';

    if (!empty($cart_item_key) && function_exists('WC') && WC()->cart) {
        WC()->cart->remove_cart_item($cart_item_key);
        WC()->cart->calculate_totals();
    }

    $fragments = apply_filters('woocommerce_add_to_cart_fragments', []);
    $cart_hash = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_hash() : '';

    wp_send_json_success([
        'fragments' => $fragments,
        'cart_hash' => $cart_hash,
    ]);
}
add_action('wp_ajax_mis360_remove_cart_item', 'mis360_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_mis360_remove_cart_item', 'mis360_ajax_remove_cart_item');

/**
 * Ürün Kartlarında İndirim Yüzdesi ve Montessori Rozetleri
 */
function mis360_product_badges() {
    global $product;
    if (!$product) return;

    echo '<div class="emdief-card-badges">';

    // 1. Sınıf MDF Rozeti
    echo '<span class="badge badge-natural">1. Sınıf MDF</span>';

    // İndirim Yüzdesi
    if ($product->is_on_sale()) {
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();
        if ($regular_price > 0 && $sale_price > 0) {
            $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
            echo '<span class="badge badge-discount">%' . esc_html((string) $discount_percent) . ' İndirim</span>';
        } else {
            echo '<span class="badge badge-discount">İndirim</span>';
        }
    }

    echo '</div>';
}
add_action('woocommerce_before_shop_loop_item_title', 'mis360_product_badges', 9);

/**
 * Ürün Detay Sayfası Güven Rozetleri ve Montessori Bilgisi
 */
function mis360_single_product_trust_box() {
    ?>
    <div class="emdief-single-trust">
        <div class="trust-pill">
            <span class="pill-icon">🌿</span>
            <div class="pill-text"><strong>1. Sınıf MDF:</strong> Çocuğunuz için pürüzsüz, sağlam ve güvenli yüzey</div>
        </div>
        <div class="trust-pill">
            <span class="pill-icon">🛡️</span>
            <div class="pill-text"><strong>Yuvarlatılmış Köşeler:</strong> Çocuk güvenliği için sivri kenarsız, pürüzsüz hatlar</div>
        </div>
        <div class="trust-pill">
            <span class="pill-icon">👶</span>
            <div class="pill-text"><strong>Montessori Boyutları:</strong> Çocuğun bağımsız erişebileceği ergonomik yükseklik</div>
        </div>
        <div class="trust-pill">
            <span class="pill-icon">⚡</span>
            <div class="pill-text"><strong>Pratik Kurulum:</strong> Şarjlı matkapla dakikalar içinde kolay montaj</div>
        </div>
    </div>
    <?php
}
add_action('woocommerce_single_product_summary', 'mis360_single_product_trust_box', 35);

/**
 * Ürün Detay Sayfası - Sepete Ekle Yanında WhatsApp Soru Sor Butonu
 */
function mis360_single_product_whatsapp_button() {
    global $product;
    if (!$product) {
        return;
    }

    $phone = get_theme_mod('mis360_whatsapp', '905374778766');
    $phone = preg_replace('/[^0-9]/', '', (string) $phone);
    if (empty($phone)) {
        $phone = '905374778766';
    }

    $title = $product->get_name();
    $sku   = $product->get_sku() ? ' (Stok Kodu: ' . $product->get_sku() . ')' : '';
    $link  = get_permalink($product->get_id());

    $message = sprintf(
        __('Merhaba, "%s"%s ürünü hakkında bilgi almak ve soru sormak istiyorum: %s', 'mis360-mobilya'),
        $title,
        $sku,
        $link
    );

    $wa_url = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    ?>
    <a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer" class="emdief-single-wa-btn" aria-label="<?php esc_attr_e('WhatsApp ile Soru Sor', 'mis360-mobilya'); ?>" title="<?php esc_attr_e('WhatsApp Danışma Hattı', 'mis360-mobilya'); ?>">
        <span class="wa-btn-icon"><?php echo mis360_icon('whatsapp', 20); ?></span>
        <span class="wa-btn-text"><?php esc_html_e('WhatsApp\'tan Sor', 'mis360-mobilya'); ?></span>
    </a>
    <?php
}
add_action('woocommerce_after_add_to_cart_button', 'mis360_single_product_whatsapp_button', 10);

/**
 * Ziyaretçi Son Gezilen Ürünleri Çerezde Saklama (PHP Cookie Tracker)
 */
function mis360_track_recently_viewed_products() {
    if (!is_singular('product')) {
        return;
    }

    $product_id = get_the_ID();
    if (!$product_id) {
        return;
    }

    $viewed_raw = !empty($_COOKIE['emdief_recently_viewed']) ? sanitize_text_field($_COOKIE['emdief_recently_viewed']) : '';
    $viewed_ids = array_filter(array_map('intval', explode('|', $viewed_raw)));

    // Mevcut ürünü listeden çıkarıp en başa ekle
    $viewed_ids = array_diff($viewed_ids, [$product_id]);
    array_unshift($viewed_ids, $product_id);
    $viewed_ids = array_slice($viewed_ids, 0, 12);

    $cookie_path = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
    $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';

    @setcookie('emdief_recently_viewed', implode('|', $viewed_ids), time() + (86400 * 30), $cookie_path, $cookie_domain);
}
add_action('template_redirect', 'mis360_track_recently_viewed_products');

/**
 * Ürün Detay Altı: Akıllı Ürün Sliderı (Son Gezilenler veya Benzer Ürünler)
 */
function mis360_single_product_smart_slider() {
    global $product;
    if (!$product) {
        return;
    }

    $current_id = $product->get_id();
    $viewed_raw = !empty($_COOKIE['emdief_recently_viewed']) ? sanitize_text_field($_COOKIE['emdief_recently_viewed']) : '';
    $viewed_ids = array_filter(array_map('intval', explode('|', $viewed_raw)));

    // Mevcut ürünü son gezilenler listesinden çıkar
    $viewed_ids = array_values(array_filter($viewed_ids, function($id) use ($current_id) {
        return $id > 0 && $id !== $current_id;
    }));

    $is_recently_viewed = !empty($viewed_ids);

    if ($is_recently_viewed) {
        $section_title    = __('👀 Son Gezdiğiniz Ürünler', 'mis360-mobilya');
        $section_subtitle = __('Daha önce incelediğiniz Montessori & çocuk odası modelleri', 'mis360-mobilya');
        $slider_badge     = __('Son Gezilen', 'mis360-mobilya');

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'post__in'       => $viewed_ids,
            'orderby'        => 'post__in',
        ];
    } else {
        $section_title    = __('✨ Sizin İçin Seçtiğimiz Benzer Ürünler', 'mis360-mobilya');
        $section_subtitle = __('Bu ürünü inceleyenlerin en çok tercih ettiği 1. sınıf kaliteli MDF tasarımlar', 'mis360-mobilya');
        $slider_badge     = __('Önerilen', 'mis360-mobilya');

        $cats = wp_get_post_terms($current_id, 'product_cat', ['fields' => 'ids']);
        $tax_query = [];
        if (!empty($cats) && !is_wp_error($cats)) {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $cats,
            ];
        }

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            'post__not_in'   => [$current_id],
            'orderby'        => 'rand',
        ];

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }
    }

    $query = new WP_Query($args);

    if (!$query->have_posts() && !$is_recently_viewed) {
        unset($args['tax_query']);
        $query = new WP_Query($args);
    }

    if (!$query->have_posts()) {
        return;
    }

    $slider_id = 'emdief-slider-' . ($is_recently_viewed ? 'recent' : 'related');
    ?>
    <section class="emdief-smart-product-section" aria-label="<?php echo esc_attr($section_title); ?>">
        <div class="emdief-smart-slider-header">
            <div class="header-text">
                <span class="section-pill"><?php echo esc_html($slider_badge); ?></span>
                <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
                <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
            </div>
            <div class="slider-nav-arrows">
                <button type="button" class="slider-btn prev-btn" aria-label="<?php esc_attr_e('Önceki Ürünler', 'mis360-mobilya'); ?>" data-target="<?php echo esc_attr($slider_id); ?>">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button type="button" class="slider-btn next-btn" aria-label="<?php esc_attr_e('Sonraki Ürünler', 'mis360-mobilya'); ?>" data-target="<?php echo esc_attr($slider_id); ?>">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>

        <div class="emdief-product-slider-track" id="<?php echo esc_attr($slider_id); ?>">
            <?php
            while ($query->have_posts()):
                $query->the_post();
                $item_product = wc_get_product(get_the_ID());
                if (!$item_product) continue;
                $item_id       = $item_product->get_id();
                $item_link     = $item_product->get_permalink();
                $regular_price = (float) $item_product->get_regular_price();
                $sale_price    = (float) $item_product->get_sale_price();
                $is_sale       = $item_product->is_on_sale();
                $discount      = ($is_sale && $regular_price > 0 && $sale_price > 0) ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;
                ?>
                <div class="emdief-slider-item">
                    <div class="emdief-mini-card">
                        <div class="mini-card-thumb">
                            <a href="<?php echo esc_url($item_link); ?>" class="thumb-link">
                                <?php
                                if (has_post_thumbnail($item_id)) {
                                    echo get_the_post_thumbnail($item_id, 'woocommerce_thumbnail', ['class' => 'mini-product-img', 'alt' => esc_attr(get_the_title())]);
                                } else {
                                    echo wc_placeholder_img('woocommerce_thumbnail');
                                }
                                ?>
                            </a>
                            <div class="mini-badges">
                                <span class="badge badge-mdf">1. Sınıf MDF</span>
                                <?php if ($discount > 0): ?>
                                    <span class="badge badge-sale">-%<?php echo esc_html((string)$discount); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mini-card-content">
                            <div class="mini-rating">
                                <span class="star-icon">⭐</span>
                                <span class="rating-val"><?php echo esc_html(number_format((float)$item_product->get_average_rating() ?: 5.0, 1)); ?></span>
                                <span class="rating-cnt">(<?php echo esc_html((string)($item_product->get_review_count() ?: 18)); ?>)</span>
                            </div>
                            <h3 class="mini-title">
                                <a href="<?php echo esc_url($item_link); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php echo esc_html(get_the_title()); ?>
                                </a>
                            </h3>
                            <div class="mini-price">
                                <?php if ($is_sale && $regular_price > 0): ?>
                                    <span class="old-price"><?php echo wc_price($regular_price); ?></span>
                                <?php endif; ?>
                                <span class="current-price"><?php echo wc_price($item_product->get_price()); ?></span>
                            </div>
                            <a href="<?php echo esc_url($item_link); ?>" class="mini-action-btn">
                                <span><?php esc_html_e('Ürünü İncele', 'mis360-mobilya'); ?></span>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </section>

    <!-- Client-side Gezilen Ürün Kaydedici -->
    <script>
    (function() {
        try {
            var pid = <?php echo (int) $current_id; ?>;
            var key = 'emdief_recent_pids';
            var list = JSON.parse(localStorage.getItem(key) || '[]');
            list = list.filter(function(id) { return id !== pid; });
            list.unshift(pid);
            if (list.length > 12) list = list.slice(0, 12);
            localStorage.setItem(key, JSON.stringify(list));
            document.cookie = 'emdief_recently_viewed=' + list.join('|') + '; path=/; max-age=' + (86400 * 30) + '; SameSite=Lax';
        } catch(e) {}
    })();
    </script>
    <?php
}
// Varsayılan ilgili ürünleri kaldır, akıllı slider ekle
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
add_action('woocommerce_after_single_product_summary', 'mis360_single_product_faq_accordion', 22);
add_action('woocommerce_after_single_product_summary', 'mis360_single_product_smart_slider', 25);

/**
 * Tekil Ürün Sayfasında SSS & Montaj/Kargo Rehberi Akordeonu
 */
function mis360_single_product_faq_accordion() {
    global $product;
    if (!$product) return;

    $product_name = $product->get_name();
    $wa_phone = get_theme_mod('mis360_whatsapp', '905374778766');
    $wa_msg = rawurlencode("Merhaba Emdief Home, '" . $product_name . "' hakkında montaj ve teslimatla ilgili bir sorum olacaktı:");
    $wa_link = "https://wa.me/" . esc_attr($wa_phone) . "?text=" . $wa_msg;

    $faqs = [
        [
            'q' => sprintf(esc_html__('%s kurulumu için paketten alyan çıkıyor mu? Hangi aletlere ihtiyacım var?', 'mis360-mobilya'), esc_html($product_name)),
            'a' => 'Paket içerisinde alyan anahtarı gönderilmemektedir. Ürünlerimizin tüm parçalarında CNC tezgahlarda milimetrik hazır montaj delikleri açılmıştır. Kitaplığınızı birleştirmek ve duvara güvenle asmak için yalnızca bir <strong>şarjlı matkaba</strong> ihtiyacınız vardır. Ortalama 5 dakikada tek başınıza zahmetsizce kurabilirsiniz.'
        ],
        [
            'q' => esc_html__('Kargo ücreti ne kadar ve siparişim ne zaman kargoya verilir?', 'mis360-mobilya'),
            'a' => '1.500 TL ve üzeri tüm siparişlerinizde tüm Türkiye\'ye kargo <strong>tamamen ücretsizdir</strong>. Ürünlerimiz atölyemizde siparişinize özel özenle üretildiği için siparişleriniz ortalama <strong>3 iş günü</strong> içerisinde kargoya teslim edilir. Ancak siparişini verdiğiniz ürün <strong>stoklarımızda hazır bulunuyorsa aynı gün / hemen kargoya teslim edilir</strong>. Kargonuz yola çıktığında SMS ve e-posta ile anlık kargo takip numaranız iletilir.'
        ],
        [
            'q' => esc_html__('Çocuk sağlığına uygun mu? Boya, vernik veya koku var mı?', 'mis360-mobilya'),
            'a' => 'Evet, %100 çocuk dostudur. E1 Avrupa standartlarında 1. sınıf dayanıklı MDF ve sivri köşe barındırmayan pürüzsüz yuvarlatılmış güvenli hatlar kullanılır. Çocuk odalarına özel, kokusuz, toksik madde içermeyen ve sağlığa tamamen zararsız su bazlı kaplama uygulanır.'
        ],
        [
            'q' => esc_html__('Montessori kitaplığı duvara sabitlemek zorunlu mu?', 'mis360-mobilya'),
            'a' => 'Montessori felsefesinde çocuğun kitaplarına özgürce ve güvenle uzanması esastır. Miniklerin tırmanma veya çekme ihtimaline karşı devrilmeyi önlemek amacıyla, paket içerisinden çıkan emniyet sabitleme aparatlarıyla kitaplığın duvara delik delinerek sabitlenmesini önemle tavsiye ederiz.'
        ],
        [
            'q' => esc_html__('Kargoda parça kırılır veya hasar görürse ne yapmalıyım?', 'mis360-mobilya'),
            'a' => 'Tüm ürünlerimiz darbe emici özel straforlar ve koruyucu ambalajlarla sigortalı olarak gönderilir. Taşıma sırasında oluşabilecek en ufak hasarda veya eksik parçada <strong>%100 koşulsuz ve ücretsiz anında yeni parça temini ve değişim garantimiz</strong> vardır. WhatsApp destek hattımıza bir fotoğraf iletmeniz yeterlidir.'
        ],
        [
            'q' => esc_html__('Temizliği ve bakımı nasıl yapılmalıdır?', 'mis360-mobilya'),
            'a' => 'Hafif nemli ve yumuşak bir mikrofiber bezle silinmesi yeterlidir. Pürüzsüz MDF yüzeyi leke tutmaz. Ağır kimyasal ve aşındırıcı çamaşır suyu gibi temizlik maddeleri kullanılmamalıdır.'
        ]
    ];
    ?>
    <section class="single-product-faq-section" id="product-faq-accordion">
        <div class="product-faq-header">
            <span class="product-faq-badge">
                <span class="faq-badge-dot"></span>
                <?php esc_html_e('MERAK EDİLENLER & MONTAJ REHBERİ', 'mis360-mobilya'); ?>
            </span>
            <h2 class="product-faq-title"><?php esc_html_e('Sıkça Sorulan Sorular', 'mis360-mobilya'); ?></h2>
            <p class="product-faq-desc"><?php printf(esc_html__('%s hakkında en çok merak edilen montaj, malzeme güvenliği ve kargo süreçleri.', 'mis360-mobilya'), esc_html($product_name)); ?></p>
        </div>

        <div class="product-faq-accordion">
            <?php foreach ($faqs as $i => $item): 
                $is_first = ($i === 0);
            ?>
                <div class="product-faq-item <?php echo $is_first ? 'is-open' : ''; ?>">
                    <button type="button" class="product-faq-toggle" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>">
                        <span class="product-faq-q-wrap">
                            <span class="product-faq-q-num">0<?php echo $i + 1; ?></span>
                            <span class="product-faq-q-text"><?php echo $item['q']; ?></span>
                        </span>
                        <span class="product-faq-icon"><?php echo $is_first ? '−' : '+'; ?></span>
                    </button>
                    <div class="product-faq-answer" style="<?php echo $is_first ? 'display:block;' : 'display:none;'; ?>">
                        <p><?php echo $item['a']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="product-faq-support-bar">
            <div class="support-bar-left">
                <span class="support-bar-icon">💬</span>
                <div class="support-bar-text">
                    <strong><?php esc_html_e('Aklınıza takılan farklı bir soru mu var?', 'mis360-mobilya'); ?></strong>
                    <span><?php esc_html_e('Atölye ve montaj uzmanlarımıza anında WhatsApp üzerinden danışabilirsiniz.', 'mis360-mobilya'); ?></span>
                </div>
            </div>
            <a href="<?php echo esc_url($wa_link); ?>" target="_blank" rel="noopener" class="support-bar-btn">
                <span><?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 18) : '💬'; ?></span>
                <span><?php esc_html_e('WhatsApp\'tan Danışın', 'mis360-mobilya'); ?></span>
            </a>
        </div>
    </section>
    <?php
}


/**
 * Tekil Ürün Sayfasında Maskottan Canlı Kargo Tavsiyesi (Mascot Advice Pill)
 */
function mis360_single_product_mascot_advice() {
    global $product;
    if (!$product) return;

    $cart = (function_exists('WC') && WC()) ? WC()->cart : null;
    $free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
    $cart_subtotal = ($cart && method_exists($cart, 'get_subtotal')) ? (float) $cart->get_subtotal() : 0.0;
    $product_price = (float) $product->get_price();
    
    $projected_subtotal = $cart_subtotal + $product_price;
    $projected_diff = $free_shipping_limit - $projected_subtotal;
    ?>
    <div class="single-product-mascot-advice <?php echo ($projected_diff <= 0) ? 'is-qualifying' : ''; ?>">
        <div class="advice-mascot-avatar">
            <?php echo function_exists('mis360_teddy_bear_avatar') ? mis360_teddy_bear_avatar(38, 'advice-bear') : '🧸'; ?>
            <span class="advice-avatar-dot"></span>
        </div>
        <div class="advice-content">
            <div class="advice-header">
                <span class="advice-badge">
                    <span class="tag-pulse"></span>
                    💬 MASKOTTAN MESAJ VAR!
                </span>
            </div>
            <div class="advice-text">
                <?php if ($projected_diff <= 0): ?>
                    🎉 <strong>Süper Haber!</strong> Bu ürünü sepetinize eklediğinizde anında <strong>ÜCRETSİZ KARGO</strong> kazanıyorsunuz! Kargo ücreti ödemeyeceksiniz!
                <?php else: ?>
                    🧸 <em>"Bu ürünü sepete eklerseniz ücretsiz kargo fırsatına sadece <strong><?php echo function_exists('wc_price') ? wc_price($projected_diff) : $projected_diff . ' TL'; ?></strong> kalıyor!"</em>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
add_action('woocommerce_single_product_summary', 'mis360_single_product_mascot_advice', 35);

/**
 * Sepet Sayfasında Maskottan Canlı Kargo Tavsiye Kutusu
 */
function mis360_cart_page_mascot_notice() {
    $cart = (function_exists('WC') && WC()) ? WC()->cart : null;
    if (!$cart || $cart->is_empty()) return;

    $free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
    $cart_subtotal = (float) $cart->get_subtotal();
    $diff = $free_shipping_limit - $cart_subtotal;
    $percent = min(100, max(0, ($cart_subtotal / ($free_shipping_limit ?: 1)) * 100));
    ?>
    <div class="mascot-speech-bubble-box cart-page-mascot-box <?php echo ($diff <= 0) ? 'is-free-shipping' : ''; ?>">
        <div class="mascot-bubble-avatar-col">
            <div class="mascot-avatar-wrapper">
                <?php echo function_exists('mis360_teddy_bear_avatar') ? mis360_teddy_bear_avatar(46, 'mascot-chat-bear') : '🧸'; ?>
                <span class="mascot-live-indicator"></span>
            </div>
        </div>
        <div class="mascot-speech-body">
            <div class="mascot-speech-top">
                <span class="mascot-label-tag">
                    <span class="tag-pulse"></span>
                    💬 MASKOTTAN MESAJ VAR!
                </span>
                <span class="mascot-time-tag">Canlı İpucu</span>
            </div>
            <div class="mascot-speech-msg">
                <?php if ($diff <= 0): ?>
                    🎉 <strong>Tebrikler!</strong> Sepetiniz <strong>1.500 TL</strong> limitini aştı ve <strong>ÜCRETSİZ KARGO</strong> kazandınız! Kargo ücreti bizden!
                <?php else: ?>
                    🧸 <em>"Sepetinize <strong><?php echo function_exists('wc_price') ? wc_price(max(0, $diff)) : max(0, $diff) . ' TL'; ?></strong> değerinde daha ürün ekleyin, kargo ücreti ödemeyin!"</em>
                <?php endif; ?>
            </div>
            <div class="mascot-meter-wrap">
                <div class="mascot-meter-track">
                    <div class="mascot-meter-fill <?php echo ($diff <= 0) ? 'full' : ''; ?>" style="width: <?php echo esc_attr((string) $percent); ?>%;"></div>
                </div>
                <span class="mascot-meter-status">
                    <?php if ($diff <= 0): ?>
                        %100 Ücretsiz Kargo
                    <?php else: ?>
                        %<?php echo round($percent); ?> (Ücretsiz kargoya son <?php echo function_exists('wc_price') ? wc_price(max(0, $diff)) : max(0, $diff) . ' TL'; ?>)
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
    <?php
}
add_action('woocommerce_before_cart', 'mis360_cart_page_mascot_notice', 15);

/**
 * Sepet Sayfasında Ücretsiz Kargo Uyarı & Teşvik Popup Modalı (v1.4.0)
 * Gutenberg Cart Block ve Klasik Sepet ile %100 Uyumlu
 */
function mis360_cart_free_shipping_popup() {
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Sadece sepet sayfasında çalıştır
    if (!is_cart()) {
        return;
    }

    $cart = WC()->cart;
    if (!$cart) {
        return;
    }

    $free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
    $cart_subtotal = (float) $cart->get_subtotal();
    $diff = $free_shipping_limit - $cart_subtotal;
    $percent = min(100, max(0, ($cart_subtotal / ($free_shipping_limit ?: 1)) * 100));
    $shipping_fee = 150.0; // Standart kargo ücreti

    // Formatlar
    $diff_text = function_exists('wc_price') ? wc_price(max(0, $diff)) : max(0, $diff) . ' TL';
    $subtotal_text = function_exists('wc_price') ? wc_price($cart_subtotal) : $cart_subtotal . ' TL';
    $limit_text = function_exists('wc_price') ? wc_price($free_shipping_limit) : $free_shipping_limit . ' TL';
    $shipping_fee_text = function_exists('wc_price') ? wc_price($shipping_fee) : $shipping_fee . ' TL';
    $is_empty = $cart->is_empty();
    ?>
    <!-- Sepet Ücretsiz Kargo Popup Modalı -->
    <div id="emdief-cart-shipping-popup" class="emdief-shipping-popup-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="fs-popup-title">
        <div class="emdief-shipping-popup-modal">
            <!-- Kapatma Butonu (X) -->
            <button type="button" class="fs-popup-close-btn" id="fs-popup-close-x" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">&times;</button>

            <div class="fs-popup-header">
                <div class="fs-popup-mascot-wrap">
                    <?php echo function_exists('mis360_teddy_bear_avatar') ? mis360_teddy_bear_avatar(76, 'fs-mascot-bear') : '<span style="font-size: 3rem;">🧸</span>'; ?>
                    <span class="fs-mascot-badge"><?php esc_html_e('🧸 MASKOTTAN CANLI MESAJ', 'mis360-mobilya'); ?></span>
                </div>
                <div class="fs-popup-tag-row">
                    <span class="fs-popup-tag">🚚 <?php esc_html_e('ÜCRETSİZ KARGO FIRSATI', 'mis360-mobilya'); ?></span>
                    <span class="fs-popup-save-badge">💰 <?php printf(esc_html__('%s Kargo Bedava!', 'mis360-mobilya'), wp_strip_all_tags($shipping_fee_text)); ?></span>
                </div>
                <h3 id="fs-popup-title" class="fs-popup-title"><?php esc_html_e('Kargo Ücreti Ödemeyin! 🎁', 'mis360-mobilya'); ?></h3>
                <p class="fs-popup-subtitle" id="fs-popup-desc">
                    <?php if ($diff > 0): ?>
                        <?php printf(__('Sepetinize sadece <strong class="fs-highlight-diff" id="fs-popup-diff-text">%s</strong> değerinde daha ürün ekleyin, <strong class="fs-shipping-fee-val">%s</strong> kargo ücreti ödemekten anında kurtulun!', 'mis360-mobilya'), $diff_text, $shipping_fee_text); ?>
                    <?php else: ?>
                        <?php esc_html_e('🎉 Tebrikler! Sepetiniz ücretsiz kargo limitini aştı, kargo ücreti ödemeyeceksiniz!', 'mis360-mobilya'); ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- İlerleme Çubuğu ve Durum -->
            <div class="fs-popup-meter-card">
                <div class="fs-meter-labels">
                    <span class="fs-meter-current"><?php esc_html_e('Mevcut Sepet:', 'mis360-mobilya'); ?> <strong id="fs-popup-subtotal-text"><?php echo $subtotal_text; ?></strong></span>
                    <span class="fs-meter-goal"><?php esc_html_e('Hedef:', 'mis360-mobilya'); ?> <strong><?php echo $limit_text; ?></strong></span>
                </div>
                <div class="fs-meter-track">
                    <div class="fs-meter-bar <?php echo ($diff <= 0) ? 'is-complete' : ''; ?>" id="fs-popup-bar" style="width: <?php echo esc_attr((string) $percent); ?>%;">
                        <span class="fs-meter-truck" title="Kargo Aracı">🚚</span>
                    </div>
                </div>
                <div class="fs-meter-footer">
                    <span class="fs-meter-percent" id="fs-popup-percent-text"><?php printf(esc_html__('%%%d Tamamlandı', 'mis360-mobilya'), round($percent)); ?></span>
                    <span class="fs-meter-saving" id="fs-popup-saving-text">
                        <?php if ($diff > 0): ?>
                            🎯 <strong><?php echo esc_html(wp_strip_all_tags($shipping_fee_text)); ?></strong> <?php esc_html_e('Tasarruf Fırsatı', 'mis360-mobilya'); ?>
                        <?php else: ?>
                            ⭐ <?php esc_html_e('Ücretsiz Kargo Aktif', 'mis360-mobilya'); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- Hızlı Kategori Önerileri (Sepeti Tamamla) -->
            <div class="fs-popup-quick-links">
                <span class="quick-links-title"><?php esc_html_e('Sepeti Kolayca Tamamlayabileceğiniz Ürünler:', 'mis360-mobilya'); ?></span>
                <div class="quick-links-chips">
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplık') : home_url('/shop/?s=kitapl%C4%B1k')); ?>" class="chip-item">
                        <span>📚 Montessori Kitaplıklar</span>
                    </a>
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/shop/?s=oyuncak')); ?>" class="chip-item">
                        <span>🧸 Ahşap Oyuncaklar</span>
                    </a>
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duvar-rafi', 'raf') : home_url('/shop/?s=raf')); ?>" class="chip-item">
                        <span>🖼️ Duvar & Banyo Rafları</span>
                    </a>
                </div>
            </div>

            <!-- Aksiyon Butonları -->
            <div class="fs-popup-actions">
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="emdief-btn btn-primary btn-lg fs-btn-shop">
                    <span>🛒 Alışverişe Devam Et (Kargo Bedava Yap)</span>
                    <?php echo function_exists('mis360_icon') ? mis360_icon('arrow-right', 18) : '→'; ?>
                </a>
                <button type="button" class="fs-btn-dismiss" id="fs-popup-continue-btn">
                    <span>Kargo Ücreti Ödeyerek Ödemeye Geç</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Sepet Sayfası Üst Hatırlatma Çubuğu (Kapatıldığında da Görünür Kalır) -->
    <div id="emdief-cart-shipping-bar" class="emdief-cart-shipping-bar" style="display: none;">
        <div class="fs-bar-inner emdief-container">
            <div class="fs-bar-left">
                <span class="fs-bar-avatar">🧸</span>
                <div class="fs-bar-text-group">
                    <span class="fs-bar-badge"><?php esc_html_e('Kargo Tasarrufu', 'mis360-mobilya'); ?></span>
                    <span class="fs-bar-msg" id="fs-bar-msg-text">
                        <?php printf(__('Ücretsiz kargo için son <strong id="fs-bar-diff-text">%s</strong>! <strong class="fs-fee-highlight">%s</strong> kargo ücreti ödemekten kurtulun.', 'mis360-mobilya'), $diff_text, $shipping_fee_text); ?>
                    </span>
                </div>
            </div>
            <div class="fs-bar-right">
                <button type="button" class="fs-bar-trigger-btn" id="fs-bar-reopen-btn">
                    <span><?php esc_html_e('Fırsatı İncele', 'mis360-mobilya'); ?></span>
                    <?php echo function_exists('mis360_icon') ? mis360_icon('gift', 15) : '🎁'; ?>
                </button>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="fs-bar-shop-link">
                    <span><?php esc_html_e('Ürün Ekle', 'mis360-mobilya'); ?></span>
                    <?php echo function_exists('mis360_icon') ? mis360_icon('arrow-right', 14) : '→'; ?>
                </a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('emdief-cart-shipping-popup');
        const bar = document.getElementById('emdief-cart-shipping-bar');
        const closeX = document.getElementById('fs-popup-close-x');
        const continueBtn = document.getElementById('fs-popup-continue-btn');
        const reopenBtn = document.getElementById('fs-bar-reopen-btn');

        const freeShippingThreshold = <?php echo (float) $free_shipping_limit; ?>;
        const initialSubtotal = <?php echo (float) $cart_subtotal; ?>;
        const initialIsEmpty = <?php echo $is_empty ? 'true' : 'false'; ?>;

        function openModal() {
            if (!modal) return;
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('is-open'), 10);
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.remove('is-open');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
            sessionStorage.setItem('emdief_fs_popup_dismissed', '1');
        }

        // Hatırlatma çubuğunu sepet alanının en üstüne taşı
        const cartContainer = document.querySelector('.wp-block-woocommerce-cart') || document.querySelector('.woocommerce-cart-form') || document.querySelector('.entry-content');
        if (cartContainer && bar) {
            cartContainer.parentNode.insertBefore(bar, cartContainer);
        }

        // Açılış mantığı
        if (!initialIsEmpty && initialSubtotal < freeShippingThreshold && initialSubtotal > 0) {
            // Hatırlatma çubuğunu göster
            if (bar) bar.style.display = 'block';

            // Popup daha önce bu sekmede kapatılmadıysa 700ms sonra otomatik aç
            if (!sessionStorage.getItem('emdief_fs_popup_dismissed')) {
                setTimeout(openModal, 700);
            }
        }

        // Gutenberg Store API ilk yükleme kontrolü (PHP oturumu gecikmeli olsa bile garanti eder)
        setTimeout(() => {
            if (window.wp && window.wp.data && window.wp.data.select) {
                const store = window.wp.data.select('wc/store/cart');
                if (store) {
                    const data = store.getCartData();
                    if (data && data.totals && data.totals.total_items) {
                        const clientSub = parseInt(data.totals.total_items, 10) / 100;
                        if (clientSub > 0 && clientSub < freeShippingThreshold) {
                            updateFromSubtotal(clientSub);
                            if (bar) bar.style.display = 'block';
                            if (!sessionStorage.getItem('emdief_fs_popup_dismissed')) {
                                openModal();
                            }
                        }
                    }
                }
            }
        }, 500);

        if (closeX) closeX.addEventListener('click', closeModal);
        if (continueBtn) continueBtn.addEventListener('click', closeModal);
        if (reopenBtn) reopenBtn.addEventListener('click', openModal);

        // Arka plana tıklayınca kapat
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        // ESC tuşu ile kapat
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
                closeModal();
            }
        });

        // Dinamik Gutenberg Cart Block veya Klasik Sepet güncelleme dinleyicisi
        function updateFromSubtotal(subtotal) {
            if (subtotal <= 0) {
                if (modal) modal.style.display = 'none';
                if (bar) bar.style.display = 'none';
                return;
            }

            const diff = Math.max(0, freeShippingThreshold - subtotal);
            const percent = Math.min(100, Math.max(0, (subtotal / freeShippingThreshold) * 100));

            const diffEl = document.getElementById('fs-popup-diff-text');
            const subtotalEl = document.getElementById('fs-popup-subtotal-text');
            const barEl = document.getElementById('fs-popup-bar');
            const percentEl = document.getElementById('fs-popup-percent-text');
            const barDiffEl = document.getElementById('fs-bar-diff-text');

            const formattedDiff = diff.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL';
            const formattedSub = subtotal.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL';

            if (diffEl) diffEl.textContent = formattedDiff;
            if (subtotalEl) subtotalEl.textContent = formattedSub;
            if (barEl) {
                barEl.style.width = percent + '%';
                if (diff <= 0) barEl.classList.add('is-complete');
                else barEl.classList.remove('is-complete');
            }
            if (percentEl) percentEl.textContent = '%' + Math.round(percent) + ' Tamamlandı';
            if (barDiffEl) barDiffEl.textContent = formattedDiff;

            if (diff <= 0) {
                const descEl = document.getElementById('fs-popup-desc');
                if (descEl) descEl.innerHTML = '🎉 <strong>Tebrikler!</strong> Sepetiniz ücretsiz kargo limitini aştı, kargo ücreti ödemeyeceksiniz!';
                const barMsgEl = document.getElementById('fs-bar-msg-text');
                if (barMsgEl) barMsgEl.innerHTML = '🎉 <strong>Tebrikler!</strong> Sepetiniz <strong>ÜCRETSİZ KARGO</strong> kazandı!';
            }
        }

        // Gutenberg Store API dinle
        if (window.wp && window.wp.data && window.wp.data.subscribe) {
            let lastSub = initialSubtotal;
            window.wp.data.subscribe(() => {
                const store = window.wp.data.select('wc/store/cart');
                if (store) {
                    const data = store.getCartData();
                    if (data && data.totals && data.totals.total_items) {
                        const newSub = parseInt(data.totals.total_items, 10) / 100;
                        if (newSub !== lastSub) {
                            lastSub = newSub;
                            updateFromSubtotal(newSub);
                        }
                    }
                }
            });
        }

        // jQuery klasik sepet güncellemelerini dinle
        if (window.jQuery) {
            jQuery(document.body).on('updated_cart_totals updated_wc_div wc_fragments_refreshed', () => {
                // Sepet güncellendiğinde gerekiyorsa tetikle
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'mis360_cart_free_shipping_popup', 30);

/**
 * Sepet Sayfası Canlı Sipariş Bildirimi (Live Recent Order Toast) (v1.4.1)
 * Sepetteki ürün için rastgele isim ve şehirle "Az önce sipariş verildi" uyarısı gösterir, 10sn sonra kaybolur.
 */
function mis360_cart_live_order_toast() {
    if (!class_exists('WooCommerce') || !is_cart()) {
        return;
    }

    $cart = WC()->cart;
    if (!$cart || $cart->is_empty()) {
        return;
    }

    // Sepetteki ürünlerin verilerini topla
    $cart_products = [];
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
            $image_id = $_product->get_image_id();
            $img_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src('thumbnail');

            $cart_products[] = [
                'id'    => $_product->get_id(),
                'name'  => $_product->get_name(),
                'image' => $img_url,
                'link'  => $_product->get_permalink(),
            ];
        }
    }

    if (empty($cart_products)) {
        return;
    }

    $cart_products_json = wp_json_encode($cart_products);
    ?>
    <!-- Canlı Sipariş Bildirimi (Social Proof Toast) -->
    <div id="emdief-cart-order-toast" class="emdief-order-toast" style="display: none;" role="status" aria-live="polite">
        <div class="toast-progress-bar" id="toast-progress-bar"></div>
        <button type="button" class="toast-close-btn" id="toast-close-btn" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">&times;</button>
        <div class="toast-inner">
            <div class="toast-thumb-wrap">
                <img id="toast-prod-img" src="" alt="<?php esc_attr_e('Ürün Görseli', 'mis360-mobilya'); ?>" width="54" height="54" class="toast-thumb" />
            </div>
            <div class="toast-content">
                <div class="toast-header-row">
                    <span class="toast-live-dot"></span>
                    <span class="toast-badge-text"><?php esc_html_e('CANLI SİPARİŞ', 'mis360-mobilya'); ?></span>
                    <span class="toast-time-text"><?php esc_html_e('• Az önce', 'mis360-mobilya'); ?></span>
                </div>
                <div class="toast-body-text" id="toast-body-text"></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('emdief-cart-order-toast');
        if (!toast) return;

        let cartProducts = <?php echo $cart_products_json; ?>;
        const names = [
            'Ayşe S.', 'Mehmet K.', 'Zeynep T.', 'Elif D.', 'Burak A.',
            'Fatma B.', 'Emre Y.', 'Selin M.', 'Merve G.', 'Canan O.',
            'Ahmet Y.', 'Büşra K.', 'Hakan T.', 'Esra N.', 'Ömer Ç.',
            'Yasemin L.', 'Tolga B.', 'Ece R.', 'Deniz P.'
        ];
        const cities = [
            'İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya',
            'Kocaeli', 'Eskişehir', 'Adana', 'Konya', 'Mersin',
            'Gaziantep', 'Samsun', 'Trabzon', 'Denizli', 'Kayseri',
            'Muğla', 'Aydın', 'Balıkesir', 'Tekirdağ'
        ];

        let toastTimer = null;

        function getRandomItem(arr) {
            return arr[Math.floor(Math.random() * arr.length)];
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function showOrderToast() {
            if (!cartProducts || cartProducts.length === 0) return;

            const product = getRandomItem(cartProducts);
            const customerName = getRandomItem(names);
            const customerCity = getRandomItem(cities);

            const imgEl = document.getElementById('toast-prod-img');
            const bodyEl = document.getElementById('toast-body-text');
            const progressBar = document.getElementById('toast-progress-bar');

            if (imgEl) {
                imgEl.src = product.image || '';
                imgEl.alt = product.name || '';
            }

            if (bodyEl) {
                bodyEl.innerHTML = 'Sepetinizdeki <strong>' + escapeHtml(product.name) + '</strong> ürününü <strong>' + customerName + '</strong> isimli müşterimiz <strong>' + customerCity + '\'dan</strong> az önce sipariş oluşturdu.';
            }

            // Toast'ı göster
            toast.style.display = 'block';
            setTimeout(() => {
                toast.classList.add('is-active');
            }, 50);

            // 10 saniyelik ilerleme çubuğu animasyonu
            if (progressBar) {
                progressBar.style.transition = 'none';
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.style.transition = 'width 10s linear';
                    progressBar.style.width = '0%';
                }, 100);
            }

            // 10 saniye sonra otomatik kaybol
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                hideOrderToast();
            }, 10000);
        }

        function hideOrderToast() {
            if (!toast) return;
            toast.classList.remove('is-active');
            setTimeout(() => {
                toast.style.display = 'none';
            }, 400);
        }

        const closeBtn = document.getElementById('toast-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (toastTimer) clearTimeout(toastTimer);
                hideOrderToast();
            });
        }

        // Kullanıcı sepete baktıktan 3.5 saniye sonra ilk bildirimi çıkar
        setTimeout(() => {
            showOrderToast();
        }, 3500);

        // Kullanıcı sayfada kaldığı sürece her 35 saniyede bir yeni rastgele isim/şehirle tekrar göster
        setInterval(() => {
            if (!toast.classList.contains('is-active')) {
                showOrderToast();
            }
        }, 35000);

        // Gutenberg Store API ile ürünler değişirse listeyi güncelle
        if (window.wp && window.wp.data && window.wp.data.subscribe) {
            window.wp.data.subscribe(() => {
                const store = window.wp.data.select('wc/store/cart');
                if (store) {
                    const data = store.getCartData();
                    if (data && data.items && data.items.length > 0) {
                        cartProducts = data.items.map(item => ({
                            id: item.id,
                            name: item.name,
                            image: (item.images && item.images[0]) ? item.images[0].src : '',
                            link: item.permalink || '#'
                        }));
                    }
                }
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'mis360_cart_live_order_toast', 35);

/**
 * Türkçe Karakterleri Esnek Eşleştiren Akıllı Arama Geliştiricisi
 * 
 * Kullanıcı "kitaplik", "cocuk", "ahsap" gibi İngilizce harflerle arasa dahi
 * veritabanındaki "Kitaplık", "Çocuk", "Ahşap" ürünlerini de eşleştirir.
 */
function mis360_turkish_search_expansion($search, $wp_query) {
    if (empty($search) || is_admin() || !$wp_query->is_search()) {
        return $search;
    }

    $raw_s = $wp_query->get('s');
    if (empty($raw_s) || strlen($raw_s) < 2) {
        return $search;
    }

    // Türkçe karakter dönüşüm haritaları
    $tr_from = ['i', 'c', 's', 'g', 'u', 'o'];
    $tr_to   = ['ı', 'ç', 'ş', 'ğ', 'ü', 'ö'];
    $tr_variant = str_replace($tr_from, $tr_to, mb_strtolower($raw_s, 'UTF-8'));

    // Ters dönüşüm (örn: kullanıcı 'ı' yazdıysa 'i' halini de destekle)
    $en_variant = str_replace($tr_to, $tr_from, mb_strtolower($raw_s, 'UTF-8'));

    $variants = array_unique(array_filter([$tr_variant, $en_variant], function($v) use ($raw_s) {
        return $v !== mb_strtolower($raw_s, 'UTF-8');
    }));

    if (!empty($variants)) {
        global $wpdb;
        $clauses = [];
        foreach ($variants as $v) {
            $like = '%' . $wpdb->esc_like($v) . '%';
            $clauses[] = $wpdb->prepare("({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s)", $like, $like);
        }

        if (!empty($clauses)) {
            $extra_sql = ' OR ' . implode(' OR ', $clauses);
            // WordPress posts_search sonundaki kapanış parantezinden önce ekle
            $search = preg_replace('/\)\s*$/', $extra_sql . ')', $search);
        }
    }

    return $search;
}
add_filter('posts_search', 'mis360_turkish_search_expansion', 20, 2);
