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



                <!-- Sepet Güven İpuçları (v1.8.0 4 Güven Öğesi) -->



                <div class="drawer-trust-check-list">



                    <span class="drawer-trust-badge">✓ <?php esc_html_e('Güvenli Ödeme', 'mis360-mobilya'); ?></span>



                    <span class="drawer-trust-badge">✓ <?php esc_html_e('Hızlı Kargo', 'mis360-mobilya'); ?></span>



                    <span class="drawer-trust-badge">✓ <?php esc_html_e('Kolay Kurulum', 'mis360-mobilya'); ?></span>



                    <span class="drawer-trust-badge">✓ <?php esc_html_e('WhatsApp Destek', 'mis360-mobilya'); ?></span>



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



/**



 * Ürünler İçin Satış Odaklı Vurucu Fayda Açıklaması Üretici (v1.8.0)



 */



function mis360_get_product_benefit_tagline($product = null) {



    if (!$product && function_exists('wc_get_product')) {



        global $product;



    }



    if (!$product) {



        return '1. Sınıf E1 MDF • CNC Hazır Delikler • 360° Güvenli Kavisler';



    }



    $title = mb_strtolower($product->get_name(), 'UTF-8');



    if (mb_strpos($title, 'duvar') !== false && mb_strpos($title, 'masa') !== false) {



        return 'Montessori Katlanabilir Duvar Masası - Çocuk Çalışma ve Aktivite Alanı İçin Akıllı Çözüm';



    }



    if (mb_strpos($title, 'melis') !== false) {



        return 'Montessori 2 Raflı Çocuk Kitaplığı - Bağımsız Kitap Seçme ve Düzen Alışkanlığı İçin İdeal';



    }



    if (mb_strpos($title, 'carmen') !== false) {



        return 'Montessori Çok Fonksiyonlu Kitaplık - Güvenli Kavisler ve Geniş Depolama Alanı';



    }



    if (mb_strpos($title, 'safir') !== false) {



        return 'Montessori Alçak Çocuk Kitaplığı - Kolay Erişim ve Devrilme Önleyici Emniyet Sistemi';



    }



    if (mb_strpos($title, 'kitap') !== false) {



        return 'Montessori Ön Yüzlü Çocuk Kitaplığı - Bağımsız Okuma Alışkanlığı Kazandıran Ergonomik Tasarım';



    }



    return 'Montessori Çocuk Odası Mobilyası - Bağımsızlık ve Özgüven Kazandıran Doğal Ahşap Tasarım';



}



/**



 * Tekil Ürün Sayfasında Başlık Altı Vurucu Fayda Rozeti (v1.8.0)



 */



function mis360_single_product_benefit_badge() {

    global $product;

    if (!$product) return;

    $benefit = mis360_get_product_benefit_tagline($product);

    ?>

    <div class="emdief-single-benefit-block">

        <div class="emdief-single-benefit-tagline">

            <span class="benefit-highlight-icon">✨</span>

            <span class="benefit-highlight-text"><?php echo esc_html($benefit); ?></span>

        </div>

        <div class="single-product-bullet-perks">

            <span class="perk-badge">🌱 Çocuk Boyuna Uygun Ergonomi</span>

            <span class="perk-badge">🛡️ E1 Standartı 1. Sınıf MDF</span>

            <span class="perk-badge">🌿 360° Sivri Köşesiz Kavisler</span>

            <span class="perk-badge">⚡ CNC Delikli 5 Dk Montaj</span>

        </div>

    </div>

    <?php

}

add_action('woocommerce_single_product_summary', 'mis360_single_product_benefit_badge', 6);



function mis360_single_product_trust_box() {



    ?>



    <!-- 4 Öğeli Güven Rozetleri (v1.8.0 Standart) -->



    <div class="emdief-single-trust-badges">



        <div class="trust-badge-item">



            <div class="trust-badge-check">✓</div>



            <div class="trust-badge-content">



                <span class="badge-title"><?php esc_html_e('Güvenli Ödeme', 'mis360-mobilya'); ?></span>



                <span class="badge-sub"><?php esc_html_e('256-Bit SSL & 3D Secure Güvencesi', 'mis360-mobilya'); ?></span>



            </div>



        </div>



        <div class="trust-badge-item">



            <div class="trust-badge-check">✓</div>



            <div class="trust-badge-content">



                <span class="badge-title"><?php esc_html_e('Hızlı Kargo', 'mis360-mobilya'); ?></span>



                <span class="badge-sub"><?php esc_html_e('Darbe Emici Sigortalı Ambalaj', 'mis360-mobilya'); ?></span>



            </div>



        </div>



        <div class="trust-badge-item">



            <div class="trust-badge-check">✓</div>



            <div class="trust-badge-content">



                <span class="badge-title"><?php esc_html_e('Kolay Kurulum', 'mis360-mobilya'); ?></span>



                <span class="badge-sub"><?php esc_html_e('CNC Hazır Delikler & Video Rehber', 'mis360-mobilya'); ?></span>



            </div>



        </div>



        <div class="trust-badge-item">



            <div class="trust-badge-check">✓</div>



            <div class="trust-badge-content">



                <span class="badge-title"><?php esc_html_e('WhatsApp Destek', 'mis360-mobilya'); ?></span>



                <span class="badge-sub"><?php esc_html_e('Birebir Canlı Montaj & Satış Desteği', 'mis360-mobilya'); ?></span>



            </div>



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



 * Tekil Ürün Sayfasında SSS & Montaj/Kargo Rehberi (Tam Genişlik & 2 Sütunlu Modern Tasarım)



 */



function mis360_single_product_faq_accordion() {



    global $product;



    if (!$product) return;



    $raw_name = $product->get_name();



    $split_name = preg_split('/[-–—|]/u', $raw_name);



    $short_name = trim($split_name[0]);



    if (mb_strlen($short_name) > 35) {



        $short_name = wp_trim_words($short_name, 4, '');



    }



    if (empty($short_name)) {



        $short_name = esc_html__('Bu ürün', 'mis360-mobilya');



    }



    $wa_phone = get_theme_mod('mis360_whatsapp', '905374778766');



    $wa_msg = rawurlencode("Merhaba Emdief Home, '" . $short_name . "' hakkında montaj ve teslimatla ilgili bir sorum olacaktı:");



    $wa_link = "https://wa.me/" . esc_attr($wa_phone) . "?text=" . $wa_msg;



    $faqs = [



        [



            'cat'  => esc_html__('Montaj & Kurulum', 'mis360-mobilya'),



            'icon' => '🛠️',



            'q'    => sprintf(esc_html__('%s kurulumu için hangi aletlere ihtiyacım var? Paketten alyan çıkıyor mu?', 'mis360-mobilya'), esc_html($short_name)),



            'a'    => 'Paket içerisinde alyan anahtarı gönderilmemektedir. Ürünlerimizin tüm parçalarında CNC tezgahlarda milimetrik hazır montaj delikleri ve geçme kanalları açılmıştır. Kitaplığınızı birleştirmek ve duvara güvenle sabitlemek için yalnızca bir <strong>şarjlı matkaba</strong> ihtiyacınız vardır.<div class="faq-tip-box">💡 <strong>Hızlı Kurulum:</strong> Parçaların birbirine uyumu kusursuzdur; ortalama <strong>5-10 dakika</strong> içinde tek başınıza kolayca kurabilirsiniz.</div>'



        ],



        [



            'cat'  => esc_html__('Teslimat & Kargo', 'mis360-mobilya'),



            'icon' => '🚚',



            'q'    => esc_html__('Kargo ücreti ne kadar ve siparişim ne zaman kargoya verilir?', 'mis360-mobilya'),



            'a'    => '1.500 TL ve üzeri tüm siparişlerinizde tüm Türkiye\'ye kargo <strong>%100 ücretsizdir</strong>. Ürünlerimiz atölyemizde siparişinize özel özenle üretildiği için ortalama <strong>3 iş günü</strong> içerisinde kargoya teslim edilir.<div class="faq-tip-box">⚡ <strong>Hızlı Gönderi:</strong> Siparişini verdiğiniz ürün atölye stoklarımızda hazır bulunuyorsa <strong>aynı gün / hemen</strong> kargoya sevk edilir. Kargonuz yola çıktığında SMS ve e-posta ile anlık takip numaranız iletilir.</div>'



        ],



        [



            'cat'  => esc_html__('Çocuk Sağlığı & Malzeme', 'mis360-mobilya'),



            'icon' => '🌿',



            'q'    => esc_html__('Çocuk sağlığına uygun mu? Boya, vernik veya koku var mı?', 'mis360-mobilya'),



            'a'    => '<strong>Evet, %100 çocuk dostudur ve güvenlidir.</strong> E1 Avrupa standartlarında 1. sınıf dayanıklı MDF ve doğal ahşap kullanılır. Sivri köşe ve keskin kenar barındırmaz; tüm hatlar çocuk ergonomisine uygun olarak <strong>yuvarlatılmış ve pürüzsüzleştirilmiştir</strong>. Çocuk odalarına özel, kokusuz, toksik madde içermeyen ve sağlığa zararsız su bazlı kaplama uygulanır.'



        ],



        [



            'cat'  => esc_html__('Güvenlik & Sabitleme', 'mis360-mobilya'),



            'icon' => '🔒',



            'q'    => esc_html__('Montessori kitaplığı duvara sabitlemek zorunlu mu?', 'mis360-mobilya'),



            'a'    => 'Montessori felsefesinde çocuğun kitaplarına ve eşyalarına özgürce uzanması hedeflenir. Miniklerin tırmanma veya çekme ihtimaline karşı devrilme riskini sıfıra indirmek adına, paket içerisinden çıkan <strong>özel emniyet sabitleme aparatlarıyla kitaplığın duvara delik delinerek sabitlenmesini önemle tavsiye ederiz</strong>.'



        ],



        [



            'cat'  => esc_html__('Kargo Hasar Garantisi', 'mis360-mobilya'),



            'icon' => '🛡️',



            'q'    => esc_html__('Kargoda parça kırılır veya hasar görürse ne yapmalıyım?', 'mis360-mobilya'),



            'a'    => 'Tüm ürünlerimiz darbe emici yüksek yoğunluklu straforlar ve koruyucu ambalajlarla <strong>%100 sigortalı</strong> olarak gönderilir. Taşıma sırasında oluşabilecek en ufak hasarda veya eksik parçada <strong>koşulsuz ve tamamen ücretsiz anında yeni parça temini ve değişim garantimiz</strong> vardır.<div class="faq-tip-box">📸 <strong>Nasıl Bildirilir?</strong> Hasarlı veya eksik parçanın fotoğrafını WhatsApp hattımıza iletmeniz durumunda yeni parçanız anında ücretsiz kargolanır.</div>'



        ],



        [



            'cat'  => esc_html__('Temizlik & Bakım', 'mis360-mobilya'),



            'icon' => '✨',



            'q'    => esc_html__('Temizliği ve bakımı nasıl yapılmalıdır?', 'mis360-mobilya'),



            'a'    => 'Hafif nemli ve yumuşak bir mikrofiber bezle silinmesi yeterlidir. Pürüzsüz yüzey teknolojisi leke tutmaz ve kolayca temizlenir. Ahşabın doğal dokusunu ve kaplamasını korumak amacıyla aşındırıcı çamaşır suyu veya ağır kimyasal maddeler kullanılmamalıdır.'



        ]



    ];



    ?>



    <section class="single-product-faq-section" id="product-faq-accordion">



        <div class="faq-section-inner">



            <!-- SOL SÜTUN / SIDEBAR -->



            <div class="faq-sidebar-col">



                <div class="faq-sidebar-sticky">



                    <div class="faq-sidebar-intro">



                        <span class="faq-sidebar-badge">



                            <span class="faq-badge-dot"></span>



                            <?php esc_html_e('MERAK EDİLENLER & MONTAJ REHBERİ', 'mis360-mobilya'); ?>



                        </span>



                        <h2 class="faq-sidebar-title"><?php esc_html_e('Sıkça Sorulan Sorular', 'mis360-mobilya'); ?></h2>



                        <p class="faq-sidebar-desc"><?php printf(esc_html__('%s hakkında montaj kolaylığı, kargo süreçleri, çocuk sağlığı ve malzeme kalitesiyle ilgili merak edilenler.', 'mis360-mobilya'), '<strong>' . esc_html($short_name) . '</strong>'); ?></p>



                        <div class="faq-trust-features">



                            <div class="faq-trust-card">



                                <span class="faq-trust-icon">🛠️</span>



                                <div class="faq-trust-info">



                                    <strong><?php esc_html_e('5 Dk Hızlı Montaj', 'mis360-mobilya'); ?></strong>



                                    <span><?php esc_html_e('CNC hazır delikler, şarjlı matkap yeterli', 'mis360-mobilya'); ?></span>



                                </div>



                            </div>



                            <div class="faq-trust-card">



                                <span class="faq-trust-icon">🚚</span>



                                <div class="faq-trust-info">



                                    <strong><?php esc_html_e('Ücretsiz & Sigortalı Kargo', 'mis360-mobilya'); ?></strong>



                                    <span><?php esc_html_e('1.500 TL üzeri kargo bedava, korumalı paket', 'mis360-mobilya'); ?></span>



                                </div>



                            </div>



                            <div class="faq-trust-card">



                                <span class="faq-trust-icon">🌿</span>



                                <div class="faq-trust-info">



                                    <strong><?php esc_html_e('%100 Çocuk Dostu E1', 'mis360-mobilya'); ?></strong>



                                    <span><?php esc_html_e('Toksiksiz, kokusuz, yuvarlatılmış köşeler', 'mis360-mobilya'); ?></span>



                                </div>



                            </div>



                            <div class="faq-trust-card">



                                <span class="faq-trust-icon">🛡️</span>



                                <div class="faq-trust-info">



                                    <strong><?php esc_html_e('Koşulsuz Parça Garantisi', 'mis360-mobilya'); ?></strong>



                                    <span><?php esc_html_e('Kargo hasarlarında anında yeni parça temini', 'mis360-mobilya'); ?></span>



                                </div>



                            </div>



                        </div>



                    </div>



                    <!-- WHATSAPP DESTEK KARTI -->



                    <div class="faq-whatsapp-card">



                        <div class="wa-card-badge">



                            <span class="wa-dot-live"></span>



                            <?php esc_html_e('CANLI ATÖLYE DESTEĞİ', 'mis360-mobilya'); ?>



                        </div>



                        <h3 class="wa-card-heading"><?php esc_html_e('Aklınıza Takılan Başka Bir Soru mu Var?', 'mis360-mobilya'); ?></h3>



                        <p class="wa-card-sub"><?php esc_html_e('Montaj ölçüleri, oda uyumu veya teslimatla ilgili atölye ustalarımıza danışabilirsiniz.', 'mis360-mobilya'); ?></p>



                        <a href="<?php echo esc_url($wa_link); ?>" target="_blank" rel="noopener" class="wa-card-btn">



                            <div class="wa-btn-content">



                                <svg class="wa-svg-icon" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">



                                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.17 8.17 0 0 1-5.82 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.87.85-.87 2.08s.89 2.41 1.02 2.58c.13.17 1.76 2.68 4.26 3.76.59.26 1.06.41 1.42.53.6.19 1.14.16 1.57.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.12-.22-.19-.47-.31z"/>



                                </svg>



                                <span><?php esc_html_e('WhatsApp\'tan Danışın', 'mis360-mobilya'); ?></span>



                            </div>



                            <svg class="wa-arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">



                                <line x1="5" y1="12" x2="19" y2="12"></line>



                                <polyline points="12 5 19 12 12 19"></polyline>



                            </svg>



                        </a>



                    </div>



                </div>



            </div>



            <!-- SAĞ SÜTUN / ACCORDION -->



            <div class="faq-content-col">



                <div class="product-faq-accordion">



                    <?php foreach ($faqs as $i => $item): 



                        $is_first = ($i === 0);



                    ?>



                        <div class="product-faq-item <?php echo $is_first ? 'is-open' : ''; ?>">



                            <button type="button" class="product-faq-toggle" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>">



                                <div class="faq-toggle-left">



                                    <span class="faq-cat-pill">



                                        <span class="faq-cat-emoji"><?php echo esc_html($item['icon']); ?></span>



                                        <span><?php echo esc_html($item['cat']); ?></span>



                                    </span>



                                    <span class="product-faq-q-text"><?php echo $item['q']; ?></span>



                                </div>



                                <div class="faq-chevron-wrap">



                                    <svg class="faq-chevron" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">



                                        <polyline points="6 9 12 15 18 9"></polyline>



                                    </svg>



                                </div>



                            </button>



                            <div class="product-faq-answer" style="<?php echo $is_first ? 'display:block;' : 'display:none;'; ?>">



                                <div class="faq-answer-inner">



                                    <p><?php echo $item['a']; ?></p>



                                </div>



                            </div>



                        </div>



                    <?php endforeach; ?>



                </div>



            </div>



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



/**



 * --------------------------------------------------------------------------



 * TEKİL ÜRÜN KURULUM VİDEOLARI VE DUVARA MONTAJ SİSTEMİ (v1.7.4)



 * --------------------------------------------------------------------------



 */



/**



 * YouTube URL veya ID'sinden 11 haneli video kodunu ayıkla



 */



function mis360_extract_youtube_id($url) {



    if (empty($url)) {



        return '';



    }



    $url = trim($url);



    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {



        return $match[1];



    }



    if (strlen($url) === 11 && preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {



        return $url;



    }



    return '';



}



/**



 * Ürüne uygun kurulum videosunu başlık, slug ve kategorilere göre akıllı eşleştir



 */



function mis360_get_product_installation_video($product) {



    if (!$product || !is_a($product, 'WC_Product')) {



        return null;



    }



    $product_id = $product->get_id();



    // 1. Manuel Özel Video Tanımı (Post Meta)



    $custom_video = get_post_meta($product_id, '_emdief_installation_video', true);



    if (empty($custom_video)) {



        $custom_video = get_post_meta($product_id, '_installation_video', true);



    }



    if (!empty($custom_video)) {



        $custom_yt = mis360_extract_youtube_id($custom_video);



        if ($custom_yt) {



            return [



                'youtube_id'  => $custom_yt,



                'title'       => sprintf(esc_html__('%s Kurulum Videosu', 'mis360-mobilya'), $product->get_name()),



                'series_name' => esc_html__('Özel Ürün Kurulumu', 'mis360-mobilya'),



                'tools'       => esc_html__('Şarjlı Matkap', 'mis360-mobilya'),



            ];



        }



    }



    // 2. Başlık, Slug ve Kategori İsimlerini Birleştirip Normalize Et



    $name = mb_strtolower($product->get_name(), 'UTF-8');



    $slug = strtolower((string) $product->get_slug());



    



    // Kategori isimlerini de dahil et



    $cats_str = '';



    $terms = get_the_terms($product_id, 'product_cat');



    if (!empty($terms) && !is_wp_error($terms)) {



        foreach ($terms as $term) {



            $cats_str .= ' ' . mb_strtolower($term->name, 'UTF-8');



        }



    }



    $haystack = $name . ' ' . $slug . ' ' . $cats_str;



    // Türkçe karakterleri normalize et



    $normalized = str_replace(



        ['ı', 'ç', 'ş', 'ğ', 'ü', 'ö', 'İ', 'Ç', 'Ş', 'Ğ', 'Ü', 'Ö'],



        ['i', 'c', 's', 'g', 'u', 'o', 'i', 'c', 's', 'g', 'u', 'o'],



        $haystack



    );



    // EŞLEŞTİRME KURALLARI:



    



    // A) Carmen 3 Raf (J7qaETlymr0)



    if ((strpos($normalized, 'carmen') !== false || strpos($normalized, 'karmen') !== false) &&



        (strpos($normalized, '3 raf') !== false || strpos($normalized, '3-raf') !== false || strpos($normalized, '3raf') !== false || strpos($normalized, 'uc raf') !== false)) {



        return [



            'youtube_id'  => 'J7qaETlymr0',



            'title'       => 'Carmen 3 Raflı Montessori Kitaplık Kurulumu',



            'series_name' => 'Carmen 3 Raf Serisi',



            'tools'       => 'Şarjlı Matkap',



        ];



    }



    // B) Melis 2 Raf (Uko45KVzhhs)



    if (strpos($normalized, 'melis') !== false &&



        (strpos($normalized, '2 raf') !== false || strpos($normalized, '2-raf') !== false || strpos($normalized, '2raf') !== false || strpos($normalized, 'iki raf') !== false)) {



        return [



            'youtube_id'  => 'Uko45KVzhhs',



            'title'       => 'Melis 2 Raflı Montessori Kitaplık Kurulumu',



            'series_name' => 'Melis 2 Raf Serisi',



            'tools'       => 'Şarjlı Matkap',



        ];



    }



    // C) Safir & Carmen Tek Raflı Modeller / Duvar & Banyo Rafı (bpHA-jND33Q)



    if (strpos($normalized, 'tek raf') !== false || 



        strpos($normalized, 'tek-raf') !== false || 



        strpos($normalized, 'tekraf') !== false || 



        strpos($normalized, '1 raf') !== false || 



        strpos($normalized, '1-raf') !== false || 



        strpos($normalized, 'bir raf') !== false || 



        strpos($normalized, 'duvar raf') !== false || 



        strpos($normalized, 'banyo raf') !== false) {



        return [



            'youtube_id'  => 'bpHA-jND33Q',



            'title'       => 'Safir & Carmen Tek Raflı Modellerimizin Kurulumu',



            'series_name' => 'Duvar & Banyo Rafı Grubu',



            'tools'       => 'Matkap + Dübel + Vida',



        ];



    }



    // D) Melis Serisi Kitaplıklar (LBBww08uTcI)



    if (strpos($normalized, 'melis') !== false) {



        return [



            'youtube_id'  => 'LBBww08uTcI',



            'title'       => 'Melis Serisi Montessori Kitaplık Kurulumu',



            'series_name' => 'Melis Serisi',



            'tools'       => 'Şarjlı Matkap',



        ];



    }



    // E) Safir Serisi Kitaplıklar (4fUzzzdXXgQ)



    if (strpos($normalized, 'safir') !== false) {



        return [



            'youtube_id'  => '4fUzzzdXXgQ',



            'title'       => 'Safir Serisi Montessori Kitaplık Kurulumu',



            'series_name' => 'Safir Serisi',



            'tools'       => 'Şarjlı Matkap',



        ];



    }



    // F) Carmen Serisi Kitaplıklar (R434l8wOYBY)



    if (strpos($normalized, 'carmen') !== false || strpos($normalized, 'karmen') !== false) {



        return [



            'youtube_id'  => 'R434l8wOYBY',



            'title'       => 'Carmen Serisi Montessori Kitaplık Kurulumu',



            'series_name' => 'Carmen Serisi',



            'tools'       => 'Şarjlı Matkap',



        ];



    }



    // G) Varsayılan Montessori Kurulum Rehberi (R434l8wOYBY)



    return [



        'youtube_id'  => 'R434l8wOYBY',



        'title'       => sprintf(esc_html__('%s Kurulum ve Montaj Rehberi', 'mis360-mobilya'), $product->get_name()),



        'series_name' => 'Montessori Mobilya Serisi',



        'tools'       => 'Şarjlı Matkap',



    ];



}



/**



 * WooCommerce Ürün Sayfasına "Kurulum Videosu 🎬" Sekmesi Ekle



 */



function mis360_add_installation_video_tab($tabs) {

    global $product;

    if (!$product) {

        return $tabs;

    }



    // 1. Ürün Hikayesi & Pedagojik Fayda Sekmesi

    $tabs['product_story_tab'] = [

        'title'    => esc_html__('Ürün Hikayesi', 'mis360-mobilya') . ' 📖',

        'priority' => 10,

        'callback' => 'mis360_render_product_story_tab',

    ];



    // 2. Teknik Özellikler Sekmesi

    $tabs['product_specs_tab'] = [

        'title'    => esc_html__('Özellikler', 'mis360-mobilya') . ' 🛡️',

        'priority' => 12,

        'callback' => 'mis360_render_product_specs_tab',

    ];



    // 3. Ölçüler & Boyutlar Sekmesi

    $tabs['product_dimensions_tab'] = [

        'title'    => esc_html__('Ölçüler', 'mis360-mobilya') . ' 📐',

        'priority' => 14,

        'callback' => 'mis360_render_product_dimensions_tab',

    ];



    // 4. Kullanım Alanları & Paket İçeriği Sekmesi

    $tabs['product_usage_tab'] = [

        'title'    => esc_html__('Kullanım & Paket İçeriği', 'mis360-mobilya') . ' 📦',

        'priority' => 16,

        'callback' => 'mis360_render_product_usage_tab',

    ];



    // 5. Kurulum Videosu & Montaj Sekmesi

    $video_info = mis360_get_product_installation_video($product);

    if ($video_info && !empty($video_info['youtube_id'])) {

        $tabs['installation_video'] = [

            'title'    => esc_html__('Kurulum', 'mis360-mobilya') . ' <span class="tab-video-icon">🎬</span>',

            'priority' => 18,

            'callback' => 'mis360_render_product_installation_video_tab',

        ];

    }



    // 6. Sıkça Sorulan Sorular Sekmesi

    $tabs['product_faq_tab'] = [

        'title'    => esc_html__('Sık Sorulan Sorular', 'mis360-mobilya') . ' ❓',

        'priority' => 25,

        'callback' => 'mis360_render_product_faq_tab',

    ];



    return $tabs;

}


add_filter('woocommerce_product_tabs', 'mis360_add_installation_video_tab', 20);



/**



 * "Kurulum Videosu" Sekmesi İçerik HTML'i



 */



function mis360_render_product_installation_video_tab() {



    global $product;



    if (!$product) return;



    $video_info = mis360_get_product_installation_video($product);



    if (!$video_info) return;



    $product_name = $product->get_name();



    $split_name = preg_split('/[-–—|]/u', $product_name);



    $short_name = trim($split_name[0]);



    if (mb_strlen($short_name) > 35) {



        $short_name = wp_trim_words($short_name, 4, '');



    }



    $main_yt_id = esc_attr($video_info['youtube_id']);



    $wall_yt_id = '-nYJfPdr9vw'; // Askı Aparatı Duvara Nasıl Montajlanır?



    $wa_phone = get_theme_mod('mis360_whatsapp', '905374778766');



    $wa_msg = rawurlencode("Merhaba Emdief Home, '" . $short_name . "' ürününün montajı ve kurulumu ile ilgili ustalarınızdan canlı destek almak istiyorum:");



    $wa_link = "https://wa.me/" . esc_attr($wa_phone) . "?text=" . $wa_msg;



    ?>



    <div class="product-assembly-tab-content">



        <!-- Başlık ve Giriş -->



        <div class="assembly-tab-header">



            <div class="assembly-tab-badge">



                <span class="tab-badge-dot"></span>



                <?php esc_html_e('KOLAY & PRATİK MONTAJ REHBERİ', 'mis360-mobilya'); ?>



            </div>



            <h3 class="assembly-tab-title">



                <?php printf(esc_html__('%s Kurulum ve Duvara Sabitleme Rehberi', 'mis360-mobilya'), esc_html($short_name)); ?>



            </h3>



            <p class="assembly-tab-lead">



                <?php esc_html_e('Ürünlerimizin tüm parçaları CNC tezgahlarda milimetrik montaj delikleri ve geçme kanallarıyla hazırlanmıştır. Alyan gerekmez, yalnızca bir şarjlı matkap yeterlidir! Aşağıdaki adım adım videoları izleyerek 5-10 dakika içinde tek başınıza kolayca kurabilirsiniz.', 'mis360-mobilya'); ?>



            </p>



        </div>



        <!-- 2 Sütunlu Video Kartları Izgarası -->



        <div class="product-assembly-videos-grid">



            <!-- 1. Ürünün Kendi Kurulum Videosu -->



            <div class="product-video-card main-video-card">



                <div class="p-card-header">



                    <div class="p-card-tags">



                        <span class="p-tag p-tag-orange">🎬 <?php echo esc_html($video_info['series_name']); ?></span>



                        <span class="p-tag">⏱️ 5-10 Dk Hızlı Montaj</span>



                        <span class="p-tag">⚡ <?php echo esc_html($video_info['tools']); ?></span>



                    </div>



                    <h4 class="p-card-title"><?php echo esc_html($video_info['title']); ?></h4>



                </div>



                <div class="p-video-frame-wrap">



                    <iframe 



                        src="https://www.youtube-nocookie.com/embed/<?php echo $main_yt_id; ?>?rel=0" 



                        title="<?php echo esc_attr($video_info['title']); ?>" 



                        loading="lazy" 



                        frameborder="0" 



                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 



                        allowfullscreen>



                    </iframe>



                </div>



                <div class="p-card-footer">



                    <span class="footer-tip-icon">💡</span>



                    <span class="footer-tip-text">



                        <strong>İpucu:</strong> <?php esc_html_e('Parçaları numaralarına göre düz bir zemine serin. Şarjlı matkabınızla vidaları aşırı zorlamadan CNC yuvalarına sırayla monte edin.', 'mis360-mobilya'); ?>



                    </span>



                </div>



            </div>



            <!-- 2. Askı Aparatı Duvara Montaj Videosu (Zorunlu Güvenlik) -->



            <div class="product-video-card safety-video-card">



                <div class="p-card-header">



                    <div class="p-card-tags">



                        <span class="p-tag p-tag-danger">⚠️ ÇOCUK GÜVENLİĞİ İÇİN ZORUNLUDUR</span>



                        <span class="p-tag">🛡️ Devrilme Önleyici</span>



                        <span class="p-tag">🧱 Dübel & Vida Pakette</span>



                    </div>



                    <h4 class="p-card-title"><?php esc_html_e('Askı Aparatı Duvara Nasıl Montajlanır?', 'mis360-mobilya'); ?></h4>



                </div>



                <div class="p-video-frame-wrap">



                    <iframe 



                        src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr($wall_yt_id); ?>?rel=0" 



                        title="<?php esc_attr_e('Askı Aparatı Duvara Montaj', 'mis360-mobilya'); ?>" 



                        loading="lazy" 



                        frameborder="0" 



                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 



                        allowfullscreen>



                    </iframe>



                </div>



                <div class="p-card-footer safety-footer">



                    <span class="footer-tip-icon">🔒</span>



                    <span class="footer-tip-text">



                        <strong>Önemli Emniyet Notu:</strong> <?php esc_html_e('Yerden olan modellerimiz hariç hemen hemen tüm Montessori kitaplık ve mobilyalarımızda miniklerin güvenliği için paket içerisinden çıkan askı aparatıyla duvara sabitleme yapılması zorunludur.', 'mis360-mobilya'); ?>



                    </span>



                </div>



            </div>



        </div>



        <!-- Alt Bilgi Şeridi ve Canlı WhatsApp Butonu -->



        <div class="product-assembly-features-strip">



            <div class="assembly-points-list">



                <div class="assembly-point">



                    <span class="point-icon">🎯</span>



                    <div class="point-text">



                        <strong>CNC Hazır Delikler</strong>



                        <span>Ölçü alma yok, tüm vida yuvaları açılmıştır</span>



                    </div>



                </div>



                <div class="assembly-point">



                    <span class="point-icon">🔩</span>



                    <div class="point-text">



                        <strong>Eksiksiz Montaj Kiti</strong>



                        <span>Vidalar, emniyet askı aparatları ve dübeller kutuda</span>



                    </div>



                </div>



                <div class="assembly-point">



                    <span class="point-icon">⏱️</span>



                    <div class="point-text">



                        <strong>5-10 Dakikada Hazır</strong>



                        <span>Tek başınıza şarjlı matkapla zahmetsizce kurun</span>



                    </div>



                </div>



            </div>



            <div class="assembly-action-col">



                <a href="<?php echo esc_url($wa_link); ?>" target="_blank" rel="noopener noreferrer" class="assembly-wa-live-btn" title="<?php esc_attr_e('WhatsApp Canlı Destek', 'mis360-mobilya'); ?>">



                    <span class="wa-btn-svg"><?php echo mis360_icon('whatsapp', 18); ?></span>



                    <span class="wa-btn-label">



                        <strong>Ustaya WhatsApp'tan Danış</strong>



                        <small>Canlı Montaj Desteği</small>



                    </span>



                </a>



            </div>



        </div>



    </div>



    <?php



}



/**



 * Tekil Ürün Özet Alanında (Fiyat / Sepet Yanında) Hızlı Kurulum Videosu Rozeti



 */



function mis360_single_product_video_quick_badge() {



    global $product;



    if (!$product) return;



    $video_info = mis360_get_product_installation_video($product);



    if (!$video_info || empty($video_info['youtube_id'])) return;



    ?>



    <div class="product-video-quick-badge-wrap">



        <a href="#tab-installation_video" class="product-video-quick-badge" id="btn-scroll-to-video" aria-label="<?php esc_attr_e('Kurulum Videosunu İzle', 'mis360-mobilya'); ?>">



            <span class="pv-badge-play-icon">



                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><polygon points="7 4 20 12 7 20 7 4"></polygon></svg>



            </span>



            <span class="pv-badge-content">



                <span class="pv-badge-title-row">



                    <span class="pv-title-text"><?php esc_html_e('5 Dk Kurulum Videosu', 'mis360-mobilya'); ?></span>



                    <span class="pv-tag-pulse-pill"><?php esc_html_e('VİDEOYU İZLE 🎬', 'mis360-mobilya'); ?></span>



                </span>



                <span class="pv-badge-sub">



                    <?php esc_html_e('CNC hazır delikler & şarjlı matkap ile adım adım montaj', 'mis360-mobilya'); ?>



                </span>



            </span>



            <span class="pv-badge-arrow">



                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>



            </span>



        </a>



    </div>



    <?php



}



add_action('woocommerce_single_product_summary', 'mis360_single_product_video_quick_badge', 28);



/**



 * 1. Teknik Özellikler, Ölçüler & Malzeme Tab Render (v1.8.0)



 */



function mis360_render_product_specs_tab() {



    global $product;



    if (!$product) return;



    $dimensions = function_exists('wc_format_dimensions') ? wc_format_dimensions($product->get_dimensions(false)) : '';



    $weight = $product->get_weight();



    ?>



    <div class="emdief-specs-tab-content">



        <div class="specs-intro-box">



            <h4 class="specs-title"><?php esc_html_e('Emdief Home Kalite ve Malzeme Standartları', 'mis360-mobilya'); ?></h4>



            <p><?php esc_html_e('Tüm ürünlerimiz çocuk ergonomisine uygun olarak Maria Montessori pedagojisinden ilham alınarak tasarlanmakta ve ileri teknoloji CNC tezgahlarda üretilmektedir.', 'mis360-mobilya'); ?></p>



        </div>



        <div class="specs-table-grid">



            <table class="emdief-specs-table">



                <tbody>



                    <tr>



                        <th scope="row">🌿 <?php esc_html_e('Ana Malzeme', 'mis360-mobilya'); ?></th>



                        <td><?php esc_html_e('E1 Avrupa Standartlarında 1. Sınıf Dayanıklı MDF. Çevre dostudur, formaldehit salınımı yapmaz.', 'mis360-mobilya'); ?></td>



                    </tr>



                    <tr>



                        <th scope="row">🛡️ <?php esc_html_e('Kenar & Köşe Güvenliği', 'mis360-mobilya'); ?></th>



                        <td><?php esc_html_e('360° CNC ile yuvarlatılmış kavisli kenarlar. Çarpma ve darbelere karşı sivri köşe içermez.', 'mis360-mobilya'); ?></td>



                    </tr>



                    <tr>



                        <th scope="row">🎨 <?php esc_html_e('Yüzey & Boya', 'mis360-mobilya'); ?></th>



                        <td><?php esc_html_e('Kokusuz, çocuk sağlığına uygun, pürüzsüz ve kolay temizlenebilir silinebilir melamin koruyucu yüzey.', 'mis360-mobilya'); ?></td>



                    </tr>



                    <tr>



                        <th scope="row">📐 <?php esc_html_e('Ürün Boyutları', 'mis360-mobilya'); ?></th>



                        <td>



                            <?php if (!empty($dimensions)): ?>



                                <strong><?php echo esc_html($dimensions); ?></strong>



                            <?php else: ?>



                                <?php esc_html_e('Montessori Çocuk Odası Boyutları (Detaylı teknik çizim görsellerdedir)', 'mis360-mobilya'); ?>



                            <?php endif; ?>



                            <?php if (!empty($weight)): ?>



                                <span class="spec-weight">(Ağırlık: <?php echo esc_html($weight); ?> <?php echo esc_html(get_option('woocommerce_weight_unit', 'kg')); ?>)</span>



                            <?php endif; ?>



                        </td>



                    </tr>



                    <tr>



                        <th scope="row">📦 <?php esc_html_e('Paket İçeriği', 'mis360-mobilya'); ?></th>



                        <td><?php esc_html_e('Numaralandırılmış demonte ahşap paneller, eksiksiz alyan/vida montaj seti, çelik duvara sabitleme aparatı & dübel seti, görsel montaj şeması.', 'mis360-mobilya'); ?></td>



                    </tr>



                    <tr>



                        <th scope="row">🔒 <?php esc_html_e('Duvar Emniyet Sabitlemesi', 'mis360-mobilya'); ?></th>



                        <td><?php esc_html_e('Çocuk güvenliği için devrilme önleyici askı aparatı kutu içerisindedir. Duvara monte edilerek kullanılması zorunludur.', 'mis360-mobilya'); ?></td>



                    </tr>



                </tbody>



            </table>



        </div>



    </div>



    <?php



}



/**



 * 2. Sıkça Sorulan Sorular (SSS) Tab Render (v1.8.0)



 */



function mis360_render_product_faq_tab() {



    ?>



    <div class="emdief-faq-tab-content">



        <div class="faq-accordion-wrap">



            <details class="faq-item" open>



                <summary class="faq-question">



                    <span>Kurulum için montaj ustası çağırmama gerek var mı?</span>



                    <span class="faq-chevron">▾</span>



                </summary>



                <div class="faq-answer">



                    <p>Kesinlikle hayır! Ürünlerimizin tüm vida ve kilit yuvaları CNC tezgahlarda milimetrik olarak açılmıştır. Paket içerisinden çıkan şema ve sitemizdeki adım adım kurulum videosu sayesinde şarjlı matkap ile 5-10 dakika içinde tek başınıza kolayca kurabilirsiniz.</p>



                </div>



            </details>



            <details class="faq-item">



                <summary class="faq-question">



                    <span>Duvara sabitleme aparatı neden zorunlu?</span>



                    <span class="faq-chevron">▾</span>



                </summary>



                <div class="faq-answer">



                    <p>Montessori felsefesinde çocuğun mobilyaya bağımsız erişimi esastır. Çocukların tırmanma, çekme veya tutunma risklerine karşı devrilme tehlikesini tamamen sıfırlamak için kutudan çıkan özel çelik askı aparatıyla duvara sabitlenmesi çocuk güvenliği açısından zorunludur.</p>



                </div>



            </details>



            <details class="faq-item">



                <summary class="faq-question">



                    <span>Kullanılan malzeme çocuk sağlığına uygun mudur?</span>



                    <span class="faq-chevron">▾</span>



                </summary>



                <div class="faq-answer">



                    <p>Evet. Ürünlerimizde E1 Avrupa kalite normlarına uygun, kanserojen formaldehit salınımı yapmayan 1. sınıf dayanıklı MDF ve kokusuz su bazlı kaplamalar kullanılmaktadır. Çocuk odalarında %100 güvenle kullanılabilir.</p>



                </div>



            </details>



            <details class="faq-item">



                <summary class="faq-question">



                    <span>Kargo sırasında parçalarda hasar oluşursa ne yapmalıyım?</span>



                    <span class="faq-chevron">▾</span>



                </summary>



                <div class="faq-answer">



                    <p>Tüm siparişlerimiz darbe emici yüksek yoğunluklu straforlarla %100 sigortalı paketlenir. Kargo sürecinde olası bir deformasyonda, hasarlı parçanın fotoğrafını WhatsApp destek hattımıza (0537 477 87 66) göndermeniz yeterlidir; hiçbir ücret ödemeden derhal yeni parça kargolanır.</p>



                </div>



            </details>



        </div>



    </div>



    <?php



}



/**



 * 3. Mobilde Yapışkan Sepete Ekle Çubuğu (Sticky Add to Cart Bar - v1.8.0)



 */



function mis360_mobile_sticky_product_bar() {



    if (!function_exists('is_product') || !is_product()) {



        return;



    }



    global $product;



    if (!$product || !$product->is_purchasable()) {



        return;



    }



    $price = $product->get_price_html();



    $title = $product->get_title();



    $thumb = get_the_post_thumbnail_url($product->get_id(), 'thumbnail');



    ?>



    <div class="emdief-mobile-sticky-bar" id="emdiefMobileStickyBar">



        <div class="sticky-bar-container">



            <div class="sticky-product-info">



                <?php if ($thumb): ?>



                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" class="sticky-thumb" loading="lazy" />



                <?php endif; ?>



                <div class="sticky-title-price">



                    <span class="sticky-title"><?php echo esc_html(wp_trim_words($title, 3, '...')); ?></span>



                    <span class="sticky-price"><?php echo $price; ?></span>



                </div>



            </div>



            <div class="sticky-btn-action">



                <?php if ($product->is_type('simple') && $product->is_in_stock()): ?>



                    <form class="cart sticky-cart-form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>



                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr((string) $product->get_id()); ?>" class="emdief-btn btn-primary sticky-btn-cart">



                            <span><?php esc_html_e('Sepete Ekle', 'mis360-mobilya'); ?></span>



                        </button>



                    </form>



                <?php else: ?>



                    <a href="#emdief-main-cart-area" class="emdief-btn btn-primary sticky-btn-cart">



                        <span><?php esc_html_e('İncele & Satın Al', 'mis360-mobilya'); ?></span>



                    </a>



                <?php endif; ?>



            </div>



        </div>



    </div>



    <?php



}



add_action('wp_footer', 'mis360_mobile_sticky_product_bar', 50);



/**

 * Ürün Hikayesi & Pedagojik Fayda Tab Render (v1.8.5)

 */

function mis360_render_product_story_tab() {

    global $product;

    if (!$product) return;

    $title = $product->get_name();

    ?>

    <div class="emdief-story-tab-content">

        <div class="story-tab-intro">

            <span class="story-badge">MONTESSORİ PEDAGOJİSİNDEN İLHAM ALINDI</span>

            <h3 class="story-title"><?php echo esc_html($title); ?> İle Özgürce Büyüyen Minikler</h3>

            <p class="story-desc">

                <?php esc_html_e('Maria Montessori’nin "Bana kendi başıma yapabilmem için yardım et" felsefesiyle tasarlanan bu mobilya, çocuğun boy seviyesine ve ergonomisine tam uyum sağlar. Geleneksel yüksek ve erişilemez raflar yerine, çocuğunuz kimseye bağımlı kalmadan eşyalarına ve kitaplarına dilediği an ulaşabilir, düzenleme alışkanlığı kazanır.', 'mis360-mobilya'); ?>

            </p>

        </div>

        <div class="story-values-grid">

            <div class="story-val-box">

                <span class="val-icon">🌱</span>

                <h4>Özgüven &amp; Bağımsızlık</h4>

                <p>Kendi seçimini kendi yapabilen miniklerde karar verme mekanizması güçlenir.</p>

            </div>

            <div class="story-val-box">

                <span class="val-icon">📚</span>

                <h4>Görsel Odak &amp; Düzen</h4>

                <p>Kitap kapaklarının ön yüzünün görünmesi görsel hafızayı ve okuma sevgisini artırır.</p>

            </div>

            <div class="story-val-box">

                <span class="val-icon">🛡️</span>

                <h4>Güvenli Yaşam Alanı</h4>

                <p>360° yuvarlatılmış kavisler sayesinde çocuk odalarında tam emniyet sağlanır.</p>

            </div>

        </div>

    </div>

    <?php

}



/**

 * Ölçüler & Boyutlar Tab Render (v1.8.5)

 */

function mis360_render_product_dimensions_tab() {

    global $product;

    if (!$product) return;

    $dimensions = function_exists('wc_format_dimensions') ? wc_format_dimensions($product->get_dimensions(false)) : '';

    $weight = $product->get_weight();

    ?>

    <div class="emdief-dimensions-tab-content">

        <div class="dimensions-header">

            <h4><?php esc_html_e('Milimetrik Hassasiyetle Üretilmiş Ölçüler', 'mis360-mobilya'); ?></h4>

            <p><?php esc_html_e('Çocuk odalarında yer tasarrufu sağlayan kompakt ve ergonomik hatlar.', 'mis360-mobilya'); ?></p>

        </div>

        <table class="emdief-specs-table">

            <tbody>

                <tr>

                    <th scope="row">📏 <?php esc_html_e('Dış Boyutlar (G x Y x D)', 'mis360-mobilya'); ?></th>

                    <td>

                        <?php if (!empty($dimensions)): ?>

                            <strong><?php echo esc_html($dimensions); ?></strong>

                        <?php else: ?>

                            <strong><?php esc_html_e('Standart Montessori Çocuk Ölçüleri (Detaylar ürün görsellerinde teknik çizimde mevcuttur)', 'mis360-mobilya'); ?></strong>

                        <?php endif; ?>

                    </td>

                </tr>

                <tr>

                    <th scope="row">⚖️ <?php esc_html_e('Net Ürün Ağırlığı', 'mis360-mobilya'); ?></th>

                    <td>

                        <?php if (!empty($weight)): ?>

                            <strong><?php echo esc_html($weight); ?> <?php echo esc_html(get_option('woocommerce_weight_unit', 'kg')); ?></strong>

                        <?php else: ?>

                            <?php esc_html_e('Yaklaşık 6 - 9 kg (Sağlam ve titreşimsiz gövde ağırlığı)', 'mis360-mobilya'); ?>

                        <?php endif; ?>

                    </td>

                </tr>

                <tr>

                    <th scope="row">🧱 <?php esc_html_e('Malzeme Kalınlığı', 'mis360-mobilya'); ?></th>

                    <td><?php esc_html_e('1. Sınıf 18 mm dayanıklı MDF taşıyıcı paneller ve 8 mm raf destekleri.', 'mis360-mobilya'); ?></td>

                </tr>

                <tr>

                    <th scope="row">📐 <?php esc_html_e('Raf Derinliği & Aralığı', 'mis360-mobilya'); ?></th>

                    <td><?php esc_html_e('A4 ve büyük boy çocuk masal kitaplarının rahatça sığabileceği geniş ön yüzey derinliği.', 'mis360-mobilya'); ?></td>

                </tr>

            </tbody>

        </table>

    </div>

    <?php

}



/**

 * Kullanım Alanları & Paket İçeriği Tab Render (v1.8.5)

 */

function mis360_render_product_usage_tab() {

    ?>

    <div class="emdief-usage-tab-content">

        <div class="usage-grid">

            <div class="usage-col">

                <h4 class="usage-col-title">🏡 <?php esc_html_e('Kullanım Alanları', 'mis360-mobilya'); ?></h4>

                <ul class="usage-list">

                    <li><strong>Bebek &amp; Çocuk Odaları:</strong> Bağımsız kitap okuma ve aktivite köşeleri için mükemmeldir.</li>

                    <li><strong>Montessori Oyun Alanları:</strong> Çocuğun boy seviyesinde bağımsız oyun ve düzen alanı sunar.</li>

                    <li><strong>Anaokulları &amp; Kreşler:</strong> Dayanıklı E1 MDF yapısıyla toplu kullanıma ve sık temizliğe uygundur.</li>

                    <li><strong>Çalışma &amp; Çizim Masası Yanı:</strong> Kırtasiye, boyama kitapları ve oyuncakları düzenli tutar.</li>

                </ul>

            </div>

            <div class="usage-col">

                <h4 class="usage-col-title">📦 <?php esc_html_e('Paket İçeriği', 'mis360-mobilya'); ?></h4>

                <ul class="usage-list">

                    <li><strong>Demonte MDF Ahşap Paneller:</strong> Numaralandırılmış ve kenarları pürüzsüzleştirilmiş parçalar.</li>

                    <li><strong>Eksiksiz Montaj Seti:</strong> Paslanmaz vidalar, alyan anahtar ve dübel bağlantı elemanları.</li>

                    <li><strong>Çelik Emniyet Askı Aparatı:</strong> Duvara güvenli sabitleme için çocuk emniyet kiti.</li>

                    <li><strong>Görsel Kurulum Kılavuzu:</strong> Adım adım resimli montaj şeması ve video karekodu.</li>

                </ul>

            </div>

        </div>

    </div>

    <?php

}

