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
    ob_start();
    ?>
    <span class="emdief-cart-count" id="emdief-cart-count">
        <?php echo (function_exists('WC') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0'; ?>
    </span>
    <?php
    $fragments['#emdief-cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'mis360_cart_count_fragment');

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
        <!-- Kargo Hedef Barı -->
        <div class="emdief-shipping-meter">
            <?php if ($diff <= 0 && $cart_subtotal > 0): ?>
                <div class="meter-text success">
                    🎉 <strong>Tebrikler!</strong> Siparişiniz için <strong>ÜCRETSİZ KARGO</strong> kazandınız!
                </div>
                <div class="meter-bar"><div class="meter-fill full" style="width: 100%;"></div></div>
            <?php else: ?>
                <div class="meter-text">
                    🚚 Ücretsiz kargo için sepetinize <strong><?php echo function_exists('wc_price') ? wc_price(max(0, $diff)) : max(0, $diff) . ' TL'; ?></strong> değerinde ürün daha ekleyin!
                </div>
                <div class="meter-bar"><div class="meter-fill" style="width: <?php echo esc_attr((string) $percent); ?>%;"></div></div>
            <?php endif; ?>
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
                <div class="empty-icon">🧸</div>
                <h3><?php esc_html_e('Sepetiniz Henüz Boş', 'mis360-mobilya'); ?></h3>
                <p><?php esc_html_e('Montessori felsefesine uygun, 1. sınıf kaliteli MDF çocuk odası ürünlerimizi keşfetmeye başlayın!', 'mis360-mobilya'); ?></p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="emdief-btn btn-primary">
                    <?php esc_html_e('Ürünleri Keşfet', 'mis360-mobilya'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Ürün Kartlarında İndirim Yüzdesi ve Montessori Rozetleri
 */
function mis360_product_badges() {
    global $product;
    if (!$product) return;

    echo '<div class="emdief-card-badges">';

    // 1. Sınıf MDF Rozeti
    echo '<span class="badge badge-natural">1. Sınıf E1 MDF</span>';

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
            <div class="pill-text"><strong>1. Sınıf Kaliteli MDF:</strong> Çocuğunuz için pürüzsüz, sağlam ve güvenli yüzey</div>
        </div>
        <div class="trust-pill">
            <span class="pill-icon">🛡️</span>
            <div class="pill-text"><strong>E1 & EN71-3 Belgeli:</strong> Çocuk sağlığına %100 uygun su bazlı koruyucu</div>
        </div>
        <div class="trust-pill">
            <span class="pill-icon">👶</span>
            <div class="pill-text"><strong>Montessori Boyutları:</strong> Çocuğun bağımsız erişebileceği ergonomik yükseklik</div>
        </div>
        <div class="trust-pill">
            <span class="pill-icon">🔧</span>
            <div class="pill-text"><strong>Kolay Kurulum:</strong> 10 dakikada aletsiz pratik montaj</div>
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
                                <span class="badge badge-mdf">1. Sınıf E1 MDF</span>
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
add_action('woocommerce_after_single_product_summary', 'mis360_single_product_smart_slider', 25);
