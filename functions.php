<?php
/**
 * Mis360-Furniture Theme Functions & Definitions
 *
 * @package Mis360-Furniture
 * @author Serkan AKKAYA
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MIS360_FURNITURE_VERSION', '1.2.1789172600');
define('MIS360_FURNITURE_DIR', get_template_directory());
define('MIS360_FURNITURE_URI', get_template_directory_uri());

// Modüler Bileşen Yükleyici
$mis360_includes = [
    '/inc/theme-setup.php',    // Tema desteği, menüler, görsel boyutları
    '/inc/enqueue.php',        // CSS, Google Fonts ve defer scriptler
    '/inc/template-tags.php',  // SVG ikonlar, rozetler ve yardımcı fonksiyonlar
    '/inc/customizer.php',     // Tema ayarları (Duyuru çubuğu, telefon, kargo limiti)
    '/inc/seo-schema.php',     // Schema.org Product ve Organization
];

foreach ($mis360_includes as $inc_file) {
    $filepath = MIS360_FURNITURE_DIR . $inc_file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

// WooCommerce Entegrasyonu (Sadece WooCommerce aktifken yüklenir)
if (class_exists('WooCommerce')) {
    $wc_inc = MIS360_FURNITURE_DIR . '/inc/woocommerce.php';
    if (file_exists($wc_inc)) {
        require_once $wc_inc;
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
                <p><strong>Mis360-Furniture:</strong> Bu temanın tüm e-ticaret özelliklerinin çalışması için lütfen <a href="<?php echo esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')); ?>">WooCommerce</a> eklentisini etkinleştirin.</p>
            </div>
            <?php
        });
    }
}
add_action('admin_init', 'mis360_check_woocommerce_dependency');




