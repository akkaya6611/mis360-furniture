<?php
/**
 * Mis360-Mobilya Theme Functions & Definitions
 *
 * @package Mis360-Mobilya
 * @author Serkan AKKAYA
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MIS360_MOBILYA_VERSION', '1.2.1789172600');
define('MIS360_MOBILYA_DIR', get_template_directory());
define('MIS360_MOBILYA_URI', get_template_directory_uri());

// Modüler Bileşen Yükleyici
$mis360_includes = [
    '/inc/theme-setup.php',    // Tema desteği, menüler, görsel boyutları
    '/inc/enqueue.php',        // CSS, Google Fonts ve defer scriptler
    '/inc/template-tags.php',  // SVG ikonlar, rozetler ve yardımcı fonksiyonlar
    '/inc/customizer.php',     // Tema ayarları (Duyuru çubuğu, telefon, kargo limiti)
    '/inc/seo-schema.php',     // Schema.org Product ve Organization
    '/inc/theme-updater.php',  // GitHub Otomatik Güncelleyici
    '/inc/corporate-pages.php',// Kurumsal sayfalar ve yasal metinler motoru
];

foreach ($mis360_includes as $inc_file) {
    $filepath = __DIR__ . $inc_file;
    if (file_exists($filepath)) {
        require_once $filepath;
    } elseif (defined('MIS360_MOBILYA_DIR') && file_exists(MIS360_MOBILYA_DIR . $inc_file)) {
        require_once MIS360_MOBILYA_DIR . $inc_file;
    }
}

// WooCommerce Entegrasyonu (Sadece WooCommerce aktifken yüklenir)
if (class_exists('WooCommerce')) {
    $wc_inc = __DIR__ . '/inc/woocommerce.php';
    if (file_exists($wc_inc)) {
        require_once $wc_inc;
    } elseif (defined('MIS360_MOBILYA_DIR') && file_exists(MIS360_MOBILYA_DIR . '/inc/woocommerce.php')) {
        require_once MIS360_MOBILYA_DIR . '/inc/woocommerce.php';
    }
}

/**
 * Güvenlik ve Temizlik
 */
function mis360_cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'mis360_cleanup_head');

/**
 * WooCommerce Bilgilendirme Uyarısı (WooCommerce yoksa gösterilir)
 */
function mis360_check_woocommerce_dependency() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong>Mis360-Mobilya:</strong> Bu temanın tüm e-ticaret özelliklerinin çalışması için lütfen <a href="<?php echo esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')); ?>">WooCommerce</a> eklentisini etkinleştirin.</p>
            </div>
            <?php
        });
    }
}
add_action('admin_init', 'mis360_check_woocommerce_dependency');
 
/**
 * Fail-Safe Helper Functions
 * (Temanın herhangi bir sunucu veya dosya izin probleminde dahi çökmesini %100 engeller)
 */
if (!function_exists('mis360_icon')) {
    function mis360_icon($name, $size = 20, $class = '') {
        $class_attr = $class ? ' class="mis360-svg ' . esc_attr($class) . '"' : ' class="mis360-svg"';
        $style_attr = sprintf(' style="width:%dpx;height:%dpx;"', (int)$size, (int)$size);

        $icons = [
            'search'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
            'cart'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />',
            'user'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
            'heart'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />',
            'menu'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />',
            'close'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
            'phone'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />',
            'whatsapp'    => '<path fill="currentColor" d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.17 8.17 0 012.41 5.82c0 4.54-3.7 8.24-8.24 8.24-1.45 0-2.87-.38-4.12-1.1l-.3-.18-3.12.82.83-3.04-.19-.31a8.21 8.21 0 01-1.26-4.43c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.24-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.38-.43.12-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.43.06-.66.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.13.17 1.73 2.65 4.2 3.71.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.13-.23-.2-.48-.32z"/>',
            'arrow-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />',
            'sparkles'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />',
            'help-circle' => '<circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3m.08 4h.01"/>',
            'play'        => '<polygon points="5 3 19 12 5 21 5 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="currentColor"/>',
            'video'       => '<polygon points="23 7 16 12 23 17 23 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2" stroke-width="2"/>',
            'wrench'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />',
        ];

        $path = $icons[$name] ?? ($icons['sparkles'] ?? '');
        $fill = in_array($name, ['star', 'whatsapp']) ? '' : ' fill="none" stroke="currentColor"';

        return sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"%s%s%s>%s</svg>', $fill, $class_attr, $style_attr, $path);
    }
}

if (!function_exists('mis360_breadcrumbs')) {
    function mis360_breadcrumbs() {
        // Sessiz fallback
        return '';
    }
}

if (!function_exists('mis360_render_trust_badges')) {
    function mis360_render_trust_badges() {
        return '';
    }
}

if (!function_exists('mis360_product_badges')) {
    function mis360_product_badges($product = null) {
        return '';
    }
}

if (!function_exists('mis360_get_category_url')) {
    function mis360_get_category_url($slug, $fallback_search = '') {
        if (class_exists('WooCommerce')) {
            $key = is_array($slug) ? reset($slug) : $slug;
            $term = get_term_by('slug', $key, 'product_cat');
            if ($term && !is_wp_error($term)) {
                return get_term_link($term, 'product_cat');
            }
            $shop = wc_get_page_permalink('shop');
            return $fallback_search ? add_query_arg(['s' => $fallback_search, 'post_type' => 'product'], $shop) : $shop;
        }
        return home_url('/?s=' . urlencode($fallback_search ?: (is_array($slug) ? reset($slug) : $slug)) . '&post_type=product');
    }
}

if (!function_exists('mis360_get_category_icon')) {
    function mis360_get_category_icon($term) {
        return '🏷️';
    }
}

if (!function_exists('mis360_bear_shopping_cart')) {
    function mis360_bear_shopping_cart($w = 190, $h = 105, $c = '') {
        return '<div class="bear-fallback" style="font-size:48px;">🧸🛒</div>';
    }
}

if (!function_exists('mis360_bear_empty_cart')) {
    function mis360_bear_empty_cart($w = 190, $h = 105, $c = '') {
        return '<div class="bear-fallback" style="font-size:48px;">🥺🧸</div>';
    }
}

if (!function_exists('mis360_teddy_bear_avatar')) {
    function mis360_teddy_bear_avatar($s = 68, $c = '') {
        return '<div class="bear-fallback" style="font-size:36px;">🧸</div>';
    }
}
