<?php
/**
 * Template Helper Functions & SVG Icon System
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modern Crisp SVG Icon Helper
 */
function mis360_icon($name, $size = 20, $class = '') {
    $class_attr = $class ? ' class="mis360-svg ' . esc_attr($class) . '"' : ' class="mis360-svg"';
    $style_attr = sprintf(' style="width:%dpx;height:%dpx;"', (int)$size, (int)$size);

    $icons = [
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
        'cart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
        'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />',
        'close' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'phone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />',
        'whatsapp' => '<path fill="currentColor" d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.17 8.17 0 012.41 5.82c0 4.54-3.7 8.24-8.24 8.24-1.45 0-2.87-.38-4.12-1.1l-.3-.18-3.12.82.83-3.04-.19-.31a8.21 8.21 0 01-1.26-4.43c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.24-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.38-.43.12-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.43.06-.66.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.13.17 1.73 2.65 4.2 3.71.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.13-.23-.2-.48-.32z"/>',
        'star' => '<path fill="currentColor" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>',
        'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
        'leaf' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />',
        'truck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2m-7 0a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" />',
        'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />',
        'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />',
        'arrow-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />',
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        'package' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
        'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />',
        'ticket' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'logout' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'copy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />',
        'download' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />',
        'help-circle' => '<circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3m.08 4h.01"/>',
        'play' => '<polygon points="5 3 19 12 5 21 5 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="currentColor"/>',
        'video' => '<polygon points="23 7 16 12 23 17 23 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2" stroke-width="2"/>',
    ];

    $path = $icons[$name] ?? $icons['sparkles'];
    $fill = in_array($name, ['star', 'whatsapp']) ? '' : ' fill="none" stroke="currentColor"';

    return sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"%s%s%s>%s</svg>', $fill, $class_attr, $style_attr, $path);
}

/**
 * Montessori & Kurumsal Güven Rozetleri (Header & Footer & Ürün Sayfalarında)
 */
function mis360_render_trust_badges() {
    ?>
    <div class="emdief-trust-strip">
        <div class="emdief-container">
            <div class="emdief-trust-grid">
                <div class="emdief-trust-item">
                    <div class="emdief-trust-icon">
                        <?php echo mis360_icon('leaf', 22); ?>
                    </div>
                    <div class="emdief-trust-text">
                        <strong>1. Sınıf MDF</strong>
                        <span>Çocuk odalarına özel, pürüzsüz ve dayanıklı ahşap gövde</span>
                    </div>
                </div>
                <div class="emdief-trust-item">
                    <div class="emdief-trust-icon">
                        <?php echo mis360_icon('shield-check', 22); ?>
                    </div>
                    <div class="emdief-trust-text">
                        <strong>Yuvarlatılmış Güvenli Köşeler</strong>
                        <span>Sivri kenar barındırmayan, çocuk güvenliğine uygun emniyetli hatlar</span>
                    </div>
                </div>
                <div class="emdief-trust-item">
                    <div class="emdief-trust-icon">
                        <?php echo mis360_icon('wrench', 22); ?>
                    </div>
                    <div class="emdief-trust-text">
                        <strong>Şarjlı Matkap ile Kolay Montaj</strong>
                        <span>CNC hazır delikler, duvara güvenli sabitleme</span>
                    </div>
                </div>
                <div class="emdief-trust-item">
                    <div class="emdief-trust-icon">
                        <?php echo mis360_icon('truck', 22); ?>
                    </div>
                    <div class="emdief-trust-text">
                        <strong>Sigortalı Hızlı Kargo</strong>
                        <span>Özel korumalı strafor ambalajda kapınıza teslim</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Ekmek Kırıntısı (Breadcrumbs)
 */
function mis360_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    echo '<nav class="emdief-breadcrumbs" aria-label="' . esc_attr__('Ekmek Kırıntısı', 'mis360-mobilya') . '">';
    echo '<div class="emdief-container">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Anasayfa', 'mis360-mobilya') . '</a>';
    echo '<span class="separator">/</span>';

    if (class_exists('WooCommerce') && (is_woocommerce() || is_cart() || is_checkout())) {
        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id && !is_shop()) {
            echo '<a href="' . esc_url(get_permalink($shop_page_id)) . '">' . esc_html(get_the_title($shop_page_id)) . '</a>';
            echo '<span class="separator">/</span>';
        }
        if (is_product()) {
            $terms = get_the_terms(get_the_ID(), 'product_cat');
            if ($terms && !is_wp_error($terms)) {
                $term = current($terms);
                echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                echo '<span class="separator">/</span>';
            }
            echo '<span class="current">' . esc_html(get_the_title()) . '</span>';
        } elseif (is_product_taxonomy()) {
            echo '<span class="current">' . esc_html(single_term_title('', false)) . '</span>';
        } elseif (is_cart()) {
            echo '<span class="current">' . esc_html__('Sepet', 'mis360-mobilya') . '</span>';
        } elseif (is_checkout()) {
            echo '<span class="current">' . esc_html__('Ödeme', 'mis360-mobilya') . '</span>';
        } else {
            echo '<span class="current">' . esc_html(get_the_title($shop_page_id)) . '</span>';
        }
    } elseif (is_single()) {
        $categories = get_the_category();
        if ($categories) {
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
            echo '<span class="separator">/</span>';
        }
        echo '<span class="current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_page()) {
        echo '<span class="current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_category() || is_tag()) {
        echo '<span class="current">' . esc_html(single_cat_title('', false)) . '</span>';
    }
    echo '</div>';
    echo '</nav>';
}

/**
 * İskandinav Montessori Lüks Ayıcık Müşteri Profil İllüstrasyonu (Scandinavian Luxury Mascot)
 */
function mis360_teddy_bear_avatar(int $size = 72, string $class = ''): string {
    $class_attr = $class ? ' class="emdief-teddy-avatar ' . esc_attr($class) . '"' : ' class="emdief-teddy-avatar"';
    $style_attr = sprintf(' style="width:%dpx;height:%dpx;display:inline-block;flex-shrink:0;"', $size, $size);

    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"%s%s>
          <defs>
            <radialGradient id="nordicCanvas" cx="50%%" cy="35%%" r="65%%">
              <stop offset="0%%" stop-color="#FFFFFF"/>
              <stop offset="100%%" stop-color="#F5EFEB"/>
            </radialGradient>
            <linearGradient id="oakWood" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
              <stop offset="0%%" stop-color="#C2926E"/>
              <stop offset="60%%" stop-color="#A87550"/>
              <stop offset="100%%" stop-color="#8F5E3B"/>
            </linearGradient>
            <linearGradient id="innerEar" x1="0%%" y1="0%%" x2="0%%" y2="100%%">
              <stop offset="0%%" stop-color="#F2E6DA"/>
              <stop offset="100%%" stop-color="#E2D0C0"/>
            </linearGradient>
            <linearGradient id="goldRim" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
              <stop offset="0%%" stop-color="#E6C987"/>
              <stop offset="50%%" stop-color="#C5A059"/>
              <stop offset="100%%" stop-color="#9C7733"/>
            </linearGradient>
          </defs>

          <!-- Dış Prestij Çerçevesi & Hafif Gölgelendirme -->
          <circle cx="60" cy="60" r="58" fill="url(#nordicCanvas)" stroke="#E8DFD5" stroke-width="1.8"/>
          <circle cx="60" cy="60" r="53.5" fill="none" stroke="url(#goldRim)" stroke-width="1" stroke-dasharray="3.5 2.5" opacity="0.75"/>
          
          <!-- Zemin Gölgesi -->
          <ellipse cx="60" cy="107" rx="28" ry="4.5" fill="#D6C7B7" opacity="0.35"/>

          <!-- Kulaklar -->
          <circle cx="35" cy="38" r="16" fill="url(#oakWood)"/>
          <circle cx="35" cy="38" r="9.5" fill="url(#innerEar)"/>
          <circle cx="85" cy="38" r="16" fill="url(#oakWood)"/>
          <circle cx="85" cy="38" r="9.5" fill="url(#innerEar)"/>

          <!-- Ayıcık Kafa Gövdesi -->
          <ellipse cx="60" cy="65" rx="40" ry="34" fill="url(#oakWood)"/>
          
          <!-- Alın Işık Vurgusu -->
          <ellipse cx="60" cy="48" rx="18" ry="7" fill="#FFFFFF" opacity="0.16"/>

          <!-- Montessori Ağız/Burun Bölgesi -->
          <ellipse cx="60" cy="74.5" rx="20" ry="15" fill="#FAF7F2"/>

          <!-- Burun -->
          <path d="M54 69 Q60 66 66 69 Q63.5 75.5 60 76.5 Q56.5 75.5 54 69 Z" fill="#3D2619"/>
          <ellipse cx="58" cy="69" rx="1.8" ry="0.9" fill="#FFFFFF" opacity="0.6"/>

          <!-- Güven Veren Tebessüm -->
          <path d="M60 76.5 L60 80.5 M55 80 Q60 84 65 80" stroke="#3D2619" stroke-width="1.8" stroke-linecap="round" fill="none"/>

          <!-- Doğal İskandinav Gül Kurusu Yanaklar -->
          <circle cx="37" cy="73" r="5" fill="#D98A7E" opacity="0.32"/>
          <circle cx="83" cy="73" r="5" fill="#D98A7E" opacity="0.32"/>

          <!-- Gözler ve Derinlik -->
          <circle cx="45" cy="58" r="4.2" fill="#24160E"/>
          <circle cx="46.3" cy="56.7" r="1.4" fill="#FFFFFF"/>
          <circle cx="43.8" cy="59.2" r="0.6" fill="#FFFFFF"/>

          <circle cx="75" cy="58" r="4.2" fill="#24160E"/>
          <circle cx="76.3" cy="56.7" r="1.4" fill="#FFFFFF"/>
          <circle cx="73.8" cy="59.2" r="0.6" fill="#FFFFFF"/>

          <!-- Kaş İfadeleri -->
          <path d="M41 51 Q45 48.5 49 50" stroke="#7A5237" stroke-width="1.6" stroke-linecap="round" fill="none"/>
          <path d="M71 50 Q75 48.5 79 51" stroke="#7A5237" stroke-width="1.6" stroke-linecap="round" fill="none"/>

          <!-- Montessori E1 MDF & Yeşil Yaprak Yaka Rozeti -->
          <path d="M53 97.5 C47 92 49 88 56 90.5 C61 92 59 97.5 53 97.5 Z" fill="#3E6B56"/>
          <path d="M67 97.5 C73 92 71 88 64 90.5 C59 92 61 97.5 67 97.5 Z" fill="#52836C"/>
          <circle cx="60" cy="94" r="3.2" fill="url(#goldRim)"/>
        </svg>',
        $class_attr,
        $style_attr
    );
}


/**
 * Sepete Ürün Ekleyen Sevimli Montessori Ayıcık İllüstrasyonu
 */
function mis360_bear_shopping_cart(int $width = 200, int $height = 110, string $class = ''): string {
    $class_attr = $class ? ' class="emdief-cart-bear-svg ' . esc_attr($class) . '"' : ' class="emdief-cart-bear-svg"';
    $style_attr = sprintf(' style="width:%dpx;height:%dpx;display:inline-block;flex-shrink:0;"', $width, $height);

    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 120"%s%s>
          <defs>
            <linearGradient id="bearCoat2" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
              <stop offset="0%%" stop-color="#C2926E"/>
              <stop offset="60%%" stop-color="#A87550"/>
              <stop offset="100%%" stop-color="#8F5E3B"/>
            </linearGradient>
            <linearGradient id="cartGold2" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
              <stop offset="0%%" stop-color="#F59E0B"/>
              <stop offset="100%%" stop-color="#D97706"/>
            </linearGradient>
          </defs>

          <!-- Zemin Gölgesi -->
          <ellipse cx="110" cy="112" rx="98" ry="5.5" fill="#E5DDD3" opacity="0.7"/>

          <!-- ALISVERIS ARABASI (Sagda) -->
          <g class="cart-structure">
            <!-- Tekerlekler -->
            <circle cx="124" cy="106" r="9" fill="#FFFFFF" stroke="#64748B" stroke-width="2.5"/>
            <circle cx="124" cy="106" r="3" fill="#64748B"/>
            <circle cx="184" cy="106" r="9" fill="#FFFFFF" stroke="#64748B" stroke-width="2.5"/>
            <circle cx="184" cy="106" r="3" fill="#64748B"/>

            <!-- Araba Alt Sasesi -->
            <path d="M112 96 L192 96 L180 106 L128 106 Z" fill="#CBD5E1"/>

            <!-- Sepet Tel Kafesi -->
            <path d="M106 46 L124 90 L190 90 L202 46 Z" fill="rgba(254, 243, 199, 0.45)" stroke="url(#cartGold2)" stroke-width="2.8" stroke-linejoin="round"/>
            
            <!-- Sepet Izgaraları -->
            <line x1="130" y1="46" x2="140" y2="90" stroke="#FBBF24" stroke-width="1.8" opacity="0.65"/>
            <line x1="154" y1="46" x2="156" y2="90" stroke="#FBBF24" stroke-width="1.8" opacity="0.65"/>
            <line x1="178" y1="46" x2="172" y2="90" stroke="#FBBF24" stroke-width="1.8" opacity="0.65"/>
            <line x1="112" y1="68" x2="196" y2="68" stroke="#FBBF24" stroke-width="1.8" opacity="0.65"/>

            <!-- Tutma Kolu -->
            <path d="M106 46 L92 40 L88 46" stroke="#94A3B8" stroke-width="3.5" stroke-linecap="round" fill="none"/>
            <rect x="86" y="38" width="12" height="6" rx="3" fill="#F59E0B"/>

            <!-- Sepetteki Mini Ürünler -->
            <rect x="140" y="55" width="22" height="32" rx="3" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1.5"/>
            <rect x="145" y="62" width="12" height="18" rx="2" fill="#FEF3C7"/>
            <polygon points="174,52 182,66 166,66" fill="#10B981" opacity="0.85"/>
            <circle cx="164" cy="58" r="6.5" fill="#FF6B6B"/>
            <text x="164" y="62" font-size="8.5" text-anchor="middle" fill="#FFFFFF" font-weight="bold">♥</text>
          </g>

          <!-- SEVİMLİ AYICIK (Solda - Sepete Kitaplık Ekliyor) -->
          <g class="bear-shopper">
            <!-- Ayaklar -->
            <ellipse cx="44" cy="108" rx="10" ry="6" fill="#8F5E3B"/>
            <ellipse cx="64" cy="108" rx="10" ry="6" fill="#8F5E3B"/>

            <!-- Gövde -->
            <ellipse cx="52" cy="85" rx="22" ry="24" fill="url(#bearCoat2)"/>
            <ellipse cx="52" cy="88" rx="12" ry="14" fill="#FBF7F2" opacity="0.45"/>

            <!-- Kulaklar -->
            <circle cx="36" cy="36" r="10" fill="url(#bearCoat2)"/>
            <circle cx="36" cy="36" r="6" fill="#F2E6DA"/>

            <circle cx="68" cy="36" r="10" fill="url(#bearCoat2)"/>
            <circle cx="68" cy="36" r="6" fill="#F2E6DA"/>

            <!-- Kafa -->
            <ellipse cx="52" cy="52" rx="24" ry="20" fill="url(#bearCoat2)"/>

            <!-- Burun ve Tebessüm -->
            <ellipse cx="55" cy="57" rx="11" ry="8" fill="#FAF7F2"/>
            <path d="M52 54 Q56 52 60 54 Q58 58 56 59 Q54 58 52 54 Z" fill="#3D2619"/>
            <path d="M56 59 L56 62 M53 62 Q56 65 59 62" stroke="#3D2619" stroke-width="1.4" stroke-linecap="round" fill="none"/>

            <!-- Yanaklar -->
            <circle cx="40" cy="56" r="3.2" fill="#D98A7E" opacity="0.45"/>
            <circle cx="67" cy="56" r="3.2" fill="#D98A7E" opacity="0.45"/>

            <!-- Sevinçli Gözler -->
            <path d="M44 48 Q47 44 50 48" stroke="#24160E" stroke-width="2.2" stroke-linecap="round" fill="none"/>
            <path d="M60 48 Q63 44 66 48" stroke="#24160E" stroke-width="2.2" stroke-linecap="round" fill="none"/>

            <!-- Kollar & Eklenen Mini Carmen Kitaplık -->
            <g class="bear-adding-item">
              <path d="M40 78 Q54 70 74 68" stroke="url(#bearCoat2)" stroke-width="9" stroke-linecap="round" fill="none"/>

              <!-- Mini Montessori Kitaplık Modeli -->
              <g transform="translate(70, 36) rotate(14)">
                <rect x="0" y="0" width="26" height="34" rx="3" fill="#FFFFFF" stroke="#D4AF37" stroke-width="1.8"/>
                <line x1="2" y1="11" x2="24" y2="11" stroke="#E5DDD3" stroke-width="1.5"/>
                <line x1="2" y1="22" x2="24" y2="22" stroke="#E5DDD3" stroke-width="1.5"/>
                <!-- Mini Kitaplar -->
                <rect x="4" y="3" width="4" height="8" rx="1" fill="#F59E0B"/>
                <rect x="9" y="4" width="4" height="7" rx="1" fill="#10B981"/>
                <rect x="14" y="2" width="5" height="9" rx="1" fill="#FF6B6B"/>
                <rect x="4" y="14" width="5" height="8" rx="1" fill="#0284C7"/>
                <rect x="10" y="13" width="4" height="9" rx="1" fill="#D97706"/>
              </g>

              <!-- Patiler -->
              <circle cx="72" cy="67" r="5" fill="#8F5E3B"/>
              <circle cx="87" cy="62" r="5" fill="#8F5E3B"/>
            </g>
          </g>

          <!-- Parıltılar & Yıldızlar -->
          <g class="sparkles">
            <path d="M100 22 L102 16 L104 22 L110 24 L104 26 L102 32 L100 26 L94 24 Z" fill="#F59E0B"/>
            <path d="M182 28 L183 24 L184 28 L188 29 L184 30 L183 34 L182 30 L178 29 Z" fill="#D4AF37"/>
            <circle cx="112" cy="38" r="2.2" fill="#10B981"/>
          </g>
        </svg>',
        $class_attr,
        $style_attr
    );
}

/**
 * Boş Sepet İçin Ağlayan Sevimli Montessori Ayıcık İllüstrasyonu (Crying Teddy Bear)
 */
function mis360_crying_bear(int $width = 130, int $height = 120, string $class = ''): string {
    $class_attr = $class ? ' class="emdief-crying-bear-svg ' . esc_attr($class) . '"' : ' class="emdief-crying-bear-svg"';
    $style_attr = sprintf(' style="width:%dpx;height:%dpx;display:inline-block;flex-shrink:0;"', $width, $height);

    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 140 130"%s%s>
          <defs>
            <linearGradient id="oakWoodSad" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
              <stop offset="0%%" stop-color="#C2926E"/>
              <stop offset="60%%" stop-color="#A87550"/>
              <stop offset="100%%" stop-color="#8F5E3B"/>
            </linearGradient>
            <linearGradient id="tearCyan" x1="0%%" y1="0%%" x2="0%%" y2="100%%">
              <stop offset="0%%" stop-color="#38BDF8"/>
              <stop offset="100%%" stop-color="#0284C7"/>
            </linearGradient>
          </defs>

          <!-- Zemin Gölgesi -->
          <ellipse cx="70" cy="124" rx="44" ry="4.5" fill="#E5DDD3" opacity="0.65"/>

          <g class="bear-sobbing-body">
            <!-- Gövde -->
            <ellipse cx="70" cy="98" rx="26" ry="24" fill="url(#oakWoodSad)"/>
            <ellipse cx="70" cy="101" rx="14" ry="14" fill="#FAF7F2" opacity="0.6"/>

            <!-- Düşük ve Üzgün Kulaklar -->
            <ellipse cx="38" cy="44" rx="13" ry="11" transform="rotate(-15 38 44)" fill="url(#oakWoodSad)"/>
            <ellipse cx="38" cy="44" rx="7" ry="6" transform="rotate(-15 38 44)" fill="#F2E6DA"/>

            <ellipse cx="102" cy="44" rx="13" ry="11" transform="rotate(15 102 44)" fill="url(#oakWoodSad)"/>
            <ellipse cx="102" cy="44" rx="7" ry="6" transform="rotate(15 102 44)" fill="#F2E6DA"/>

            <!-- Kafa -->
            <ellipse cx="70" cy="64" rx="38" ry="32" fill="url(#oakWoodSad)"/>

            <!-- Montessori Ağız Bölgesi -->
            <ellipse cx="70" cy="74" rx="19" ry="14" fill="#FAF7F2"/>

            <!-- Burun -->
            <path d="M64 69 Q70 66 76 69 Q73 74 70 75 Q67 74 64 69 Z" fill="#3D2619"/>
            <ellipse cx="68" cy="69" rx="1.5" ry="0.8" fill="#FFFFFF" opacity="0.5"/>

            <!-- Üzgün Titreyen Dudak (Down-turned quivering pout) -->
            <path d="M70 75 L70 78 M64 83 Q70 78 76 83" stroke="#3D2619" stroke-width="1.9" stroke-linecap="round" fill="none"/>

            <!-- Gül Kurusu Yanaklar -->
            <circle cx="46" cy="74" r="5.5" fill="#FDA4AF" opacity="0.55"/>
            <circle cx="94" cy="74" r="5.5" fill="#FDA4AF" opacity="0.55"/>

            <!-- Üzgün Kaşlar (Ortaya Doğru Yukarı Kıvrık) -->
            <path d="M48 48 Q54 43 60 49" stroke="#6D4327" stroke-width="2.2" stroke-linecap="round" fill="none"/>
            <path d="M80 49 Q86 43 92 48" stroke="#6D4327" stroke-width="2.2" stroke-linecap="round" fill="none"/>

            <!-- Büyük ve Sulu Islak Gözler -->
            <circle cx="54" cy="58" r="5.8" fill="#1E293B"/>
            <circle cx="56.5" cy="56" r="2.3" fill="#FFFFFF"/>
            <circle cx="52" cy="59.5" r="1.1" fill="#FFFFFF"/>
            <path d="M49 61 Q54 65 59 61" stroke="#38BDF8" stroke-width="1.8" stroke-linecap="round" fill="none"/>

            <circle cx="86" cy="58" r="5.8" fill="#1E293B"/>
            <circle cx="88.5" cy="56" r="2.3" fill="#FFFFFF"/>
            <circle cx="84" cy="59.5" r="1.1" fill="#FFFFFF"/>
            <path d="M81 61 Q86 65 91 61" stroke="#38BDF8" stroke-width="1.8" stroke-linecap="round" fill="none"/>

            <!-- Yanaklardan Süzülen Parlak Gözyaşları -->
            <path class="bear-tear-stream stream-left" d="M51 63 C46 72 48 80 50 83 C52 84 53 82 53 77 C53 71 52 64 51 63 Z" fill="url(#tearCyan)" opacity="0.95"/>
            <path class="bear-tear-stream stream-right" d="M89 63 C94 72 92 80 90 83 C88 84 87 82 87 77 C87 71 88 64 89 63 Z" fill="url(#tearCyan)" opacity="0.95"/>

            <!-- Damlayan Gözyaşı Damlaları -->
            <path class="bear-tear-drop drop-left" d="M48 88 C45 92 45 96 49 97 C52 97 53 93 49 89 Z" fill="#38BDF8"/>
            <path class="bear-tear-drop drop-right" d="M92 89 C89 93 90 97 94 97 C97 96 97 92 93 88 Z" fill="#38BDF8"/>

            <!-- Gözyaşını Silen Minik Patiler -->
            <ellipse cx="61" cy="94" rx="6.5" ry="5.5" fill="#8F5E3B"/>
            <ellipse cx="79" cy="94" rx="6.5" ry="5.5" fill="#8F5E3B"/>
          </g>
        </svg>',
        $class_attr,
        $style_attr
    );
}

/**
 * Boş Sepet ve Ağlayan Ayıcık Sahnesi (Hesabım & Sepet Sayfası İçin)
 */
function mis360_bear_empty_cart(int $width = 200, int $height = 110, string $class = ''): string {
    $class_attr = $class ? ' class="emdief-cart-bear-svg emdief-empty-cart-bear-svg ' . esc_attr($class) . '"' : ' class="emdief-cart-bear-svg emdief-empty-cart-bear-svg"';
    $style_attr = sprintf(' style="width:%dpx;height:%dpx;display:inline-block;flex-shrink:0;"', $width, $height);

    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 120"%s%s>
          <defs>
            <linearGradient id="bearCoatSad2" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
              <stop offset="0%%" stop-color="#C2926E"/>
              <stop offset="60%%" stop-color="#A87550"/>
              <stop offset="100%%" stop-color="#8F5E3B"/>
            </linearGradient>
            <linearGradient id="tearCyan2" x1="0%%" y1="0%%" x2="0%%" y2="100%%">
              <stop offset="0%%" stop-color="#38BDF8"/>
              <stop offset="100%%" stop-color="#0284C7"/>
            </linearGradient>
          </defs>

          <!-- Zemin Gölgesi -->
          <ellipse cx="110" cy="112" rx="98" ry="5.5" fill="#E5DDD3" opacity="0.7"/>

          <!-- BOMBOŞ ALISVERIS ARABASI (Sağda) -->
          <g class="cart-empty-structure">
            <!-- Tekerlekler -->
            <circle cx="124" cy="106" r="9" fill="#FFFFFF" stroke="#94A3B8" stroke-width="2.5"/>
            <circle cx="124" cy="106" r="3" fill="#94A3B8"/>
            <circle cx="184" cy="106" r="9" fill="#FFFFFF" stroke="#94A3B8" stroke-width="2.5"/>
            <circle cx="184" cy="106" r="3" fill="#94A3B8"/>

            <!-- Araba Alt Şasesi -->
            <path d="M112 96 L192 96 L180 106 L128 106 Z" fill="#E2E8F0"/>

            <!-- Sepet Tel Kafesi (İçi Bomboş) -->
            <path d="M106 46 L124 90 L190 90 L202 46 Z" fill="rgba(241, 245, 249, 0.4)" stroke="#CBD5E1" stroke-width="2.6" stroke-linejoin="round"/>
            
            <!-- Sepet Izgaraları -->
            <line x1="130" y1="46" x2="140" y2="90" stroke="#CBD5E1" stroke-width="1.6" opacity="0.6"/>
            <line x1="154" y1="46" x2="156" y2="90" stroke="#CBD5E1" stroke-width="1.6" opacity="0.6"/>
            <line x1="178" y1="46" x2="172" y2="90" stroke="#CBD5E1" stroke-width="1.6" opacity="0.6"/>
            <line x1="112" y1="68" x2="196" y2="68" stroke="#CBD5E1" stroke-width="1.6" opacity="0.6"/>

            <!-- Tutma Kolu -->
            <path d="M106 46 L92 40 L88 46" stroke="#94A3B8" stroke-width="3.5" stroke-linecap="round" fill="none"/>
            <rect x="86" y="38" width="12" height="6" rx="3" fill="#CBD5E1"/>

            <!-- Boş Sepet Simgesi (Kırık Kalp ve Boş İbaresi) -->
            <text x="156" y="70" font-size="16" text-anchor="middle" fill="#FDA4AF" opacity="0.9">💔</text>
            <text x="156" y="82" font-size="7.5" font-weight="bold" text-anchor="middle" fill="#94A3B8" letter-spacing="0.5">BOMBOŞ</text>
          </g>

          <!-- SEPETİN BOŞLUĞUNA AĞLAYAN AYICIK (Solda) -->
          <g class="bear-crying-shopper">
            <!-- Ayaklar -->
            <ellipse cx="44" cy="108" rx="10" ry="6" fill="#8F5E3B"/>
            <ellipse cx="64" cy="108" rx="10" ry="6" fill="#8F5E3B"/>

            <!-- Gövde -->
            <ellipse cx="52" cy="85" rx="22" ry="24" fill="url(#bearCoatSad2)"/>
            <ellipse cx="52" cy="88" rx="12" ry="14" fill="#FBF7F2" opacity="0.45"/>

            <!-- Kulaklar (Üzgün Düşük) -->
            <ellipse cx="36" cy="38" rx="10" ry="8.5" transform="rotate(-12 36 38)" fill="url(#bearCoatSad2)"/>
            <ellipse cx="36" cy="38" rx="6" ry="4.5" transform="rotate(-12 36 38)" fill="#F2E6DA"/>

            <ellipse cx="68" cy="38" rx="10" ry="8.5" transform="rotate(12 68 38)" fill="url(#bearCoatSad2)"/>
            <ellipse cx="68" cy="38" rx="6" ry="4.5" transform="rotate(12 68 38)" fill="#F2E6DA"/>

            <!-- Kafa -->
            <ellipse cx="52" cy="54" rx="24" ry="20" fill="url(#bearCoatSad2)"/>

            <!-- Burun ve Üzgün Dudak -->
            <ellipse cx="55" cy="59" rx="11" ry="8" fill="#FAF7F2"/>
            <path d="M52 56 Q56 54 60 56 Q58 60 56 61 Q54 60 52 56 Z" fill="#3D2619"/>
            <path d="M56 61 L56 63 M53 66 Q56 62 59 66" stroke="#3D2619" stroke-width="1.6" stroke-linecap="round" fill="none"/>

            <!-- Yanaklar -->
            <circle cx="41" cy="58" r="3.4" fill="#FDA4AF" opacity="0.55"/>
            <circle cx="68" cy="58" r="3.4" fill="#FDA4AF" opacity="0.55"/>

            <!-- Üzgün Kaşlar -->
            <path d="M43 47 Q47 43 50 48" stroke="#6D4327" stroke-width="1.8" stroke-linecap="round" fill="none"/>
            <path d="M60 48 Q63 43 67 47" stroke="#6D4327" stroke-width="1.8" stroke-linecap="round" fill="none"/>

            <!-- Islak Ağlayan Gözler -->
            <circle cx="47" cy="51" r="3.8" fill="#1E293B"/>
            <circle cx="48.5" cy="49.5" r="1.5" fill="#FFFFFF"/>
            <circle cx="45.5" cy="51.8" r="0.8" fill="#FFFFFF"/>
            <path d="M44 53 Q47 56 50 53" stroke="#38BDF8" stroke-width="1.3" stroke-linecap="round" fill="none"/>

            <circle cx="63" cy="51" r="3.8" fill="#1E293B"/>
            <circle cx="64.5" cy="49.5" r="1.5" fill="#FFFFFF"/>
            <circle cx="61.5" cy="51.8" r="0.8" fill="#FFFFFF"/>
            <path d="M60 53 Q63 56 66 53" stroke="#38BDF8" stroke-width="1.3" stroke-linecap="round" fill="none"/>

            <!-- Gözyaşları -->
            <path class="bear-tear-stream stream-left" d="M45 54 C42 61 43 66 45 68 C46 69 47 67 47 64 Z" fill="url(#tearCyan2)"/>
            <path class="bear-tear-stream stream-right" d="M65 54 C68 61 67 66 65 68 C64 69 63 67 63 64 Z" fill="url(#tearCyan2)"/>

            <path class="bear-tear-drop drop-left" d="M43 72 C41 75 41 78 44 79 C46 79 47 76 44 73 Z" fill="#38BDF8"/>
            <path class="bear-tear-drop drop-right" d="M67 73 C65 76 66 79 69 79 C71 78 71 75 68 72 Z" fill="#38BDF8"/>

            <!-- Patiler: Biri Gözünü Siliyor, Diğeri Boş Arabayı Tutuyor -->
            <path d="M42 78 Q50 72 58 70" stroke="url(#bearCoatSad2)" stroke-width="8" stroke-linecap="round" fill="none"/>
            <circle cx="56" cy="67" r="4.8" fill="#8F5E3B"/>

            <path d="M48 80 Q64 74 86 46" stroke="url(#bearCoatSad2)" stroke-width="7" stroke-linecap="round" fill="none"/>
            <circle cx="87" cy="45" r="5" fill="#8F5E3B"/>
          </g>
        </svg>',
        $class_attr,
        $style_attr
    );
}

/**
 * Montessori Kategori veya Akıllı Arama Bağlantısı Çözücü (404 ve Yanlış Arama Hatasını Önler)
 *
 * @param string|array $slug            Kategori slug'ı, slug listesi veya terim adı
 * @param string       $fallback_search Kategori bulunamazsa kullanılacak akıllı arama terimi
 * @return string
 */
function mis360_get_category_url($slug, $fallback_search = '') {
    if (!taxonomy_exists('product_cat')) {
        return class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/');
    }

    // Kategori eşleşme haritası (Kullanıcı veritabanındaki gerçek slug'ları önceliklendirir)
    $alias_map = [
        'kitaplik'               => ['cocuk-montessori-kitaplik', 'montessori-kitapliklar', 'montessori-kitaplik', 'kitapliklar', 'kitaplik'],
        'montessori-kitapliklar' => ['cocuk-montessori-kitaplik', 'montessori-kitapliklar', 'montessori-kitaplik', 'kitapliklar', 'kitaplik'],
        'cocuk-montessori-kitaplik' => ['cocuk-montessori-kitaplik', 'montessori-kitapliklar', 'montessori-kitaplik', 'kitapliklar', 'kitaplik'],
        'oyuncak'                => ['ahsap-oyuncak', 'ahsap-oyuncaklar', 'oyuncaklar', 'oyuncak', 'egitici-oyuncaklar'],
        'oyuncaklar'             => ['ahsap-oyuncak', 'ahsap-oyuncaklar', 'oyuncaklar', 'oyuncak', 'egitici-oyuncaklar'],
        'ahsap-oyuncak'          => ['ahsap-oyuncak', 'ahsap-oyuncaklar', 'oyuncaklar', 'oyuncak'],
        'ogrenme-kulesi'         => ['ahsap-oyuncak', 'ogrenme-kulesi', 'ogrenme-kuleleri'],
        'duzenleyici'            => ['duzenleyiciler', 'duzenleyici', 'duvar-rafi', 'banyo-raflari', 'askilik', 'dekoratif-kutu'],
        'duzenleyiciler'         => ['duzenleyiciler', 'duzenleyici', 'duvar-rafi', 'banyo-raflari', 'askilik', 'dekoratif-kutu'],
        'duvar-rafi'             => ['duvar-rafi', 'duzenleyiciler', 'banyo-raflari'],
        'banyo-raflari'          => ['banyo-raflari', 'duvar-rafi', 'duzenleyiciler'],
    ];

    $candidates = [];
    if (is_array($slug)) {
        $candidates = $slug;
    } elseif (isset($alias_map[$slug])) {
        $candidates = $alias_map[$slug];
    } else {
        $candidates = [$slug];
    }

    // 1. Slug veya Ad ile doğrudan eşleşme ara
    foreach ($candidates as $candidate) {
        $term = get_term_by('slug', $candidate, 'product_cat');
        if (!$term) {
            $term = get_term_by('name', $candidate, 'product_cat');
        }
        if ($term && !is_wp_error($term)) {
            $link = get_term_link($term, 'product_cat');
            if (!is_wp_error($link)) {
                return $link;
            }
        }
    }

    // 2. Kısmi eşleşme ara (Örn: adında veya slug'ında 'kitap' veya 'oyuncak' geçen ilk kategori)
    $search_key = is_array($slug) ? reset($slug) : $slug;
    if (strpos($search_key, 'kitap') !== false) {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'name__like' => 'Kitap',
            'number'     => 1,
        ]);
        if (!empty($terms) && !is_wp_error($terms)) {
            return get_term_link($terms[0], 'product_cat');
        }
    } elseif (strpos($search_key, 'oyuncak') !== false) {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'name__like' => 'Oyuncak',
            'number'     => 1,
        ]);
        if (!empty($terms) && !is_wp_error($terms)) {
            return get_term_link($terms[0], 'product_cat');
        }
    }

    // 3. Fallback olarak Mağaza veya Türkçe Doğru Arama
    if (class_exists('WooCommerce')) {
        $shop_url = wc_get_page_permalink('shop');
        if ($fallback_search) {
            return add_query_arg(['s' => $fallback_search, 'post_type' => 'product'], $shop_url);
        }
        return $shop_url;
    }

    return home_url('/?s=' . urlencode($fallback_search ?: (is_array($slug) ? reset($slug) : $slug)) . '&post_type=product');
}

/**
 * Ürün Kategorisi İçin Akıllı İkon / Görsel Çözücü
 *
 * @param WP_Term $term Kategori terim nesnesi
 * @return string HTML veya Emoji
 */
function mis360_get_category_icon($term) {
    if (!is_object($term)) {
        return '🏷️';
    }

    // Varsa WooCommerce kategori görselini kontrol et
    $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
    if ($thumb_id) {
        $thumb_url = wp_get_attachment_image_url($thumb_id, [48, 48]);
        if ($thumb_url) {
            return '<img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($term->name) . '" class="cat-pill-thumb-img">';
        }
    }

    $name = function_exists('mb_strtolower') ? mb_strtolower($term->name, 'UTF-8') : strtolower($term->name);
    $slug = $term->slug;

    if (strpos($name, 'kitap') !== false || strpos($slug, 'kitap') !== false) return '📚';
    if (strpos($name, 'oyuncak') !== false || strpos($slug, 'oyuncak') !== false) return '🧸';
    if (strpos($name, 'banyo') !== false || strpos($slug, 'banyo') !== false) return '🛁';
    if (strpos($name, 'hırdavat') !== false || strpos($slug, 'hirdavat') !== false) return '🔧';
    if (strpos($name, 'duvar') !== false || strpos($name, 'raf') !== false || strpos($slug, 'raf') !== false) return '🖼️';
    if (strpos($name, 'askı') !== false || strpos($slug, 'aski') !== false) return '🧥';
    if (strpos($name, 'kutu') !== false || strpos($slug, 'kutu') !== false) return '📦';
    if (strpos($name, 'saat') !== false || strpos($slug, 'saat') !== false) return '⏰';
    if (strpos($name, 'ajanda') !== false || strpos($slug, 'ajanda') !== false) return '📓';
    if (strpos($name, 'bebek') !== false || strpos($slug, 'bebek') !== false) return '👶';
    if (strpos($name, 'dekoratif') !== false) return '✨';

    return '🏷️';
}
