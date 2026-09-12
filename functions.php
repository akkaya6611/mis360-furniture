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
    '/inc/enqueue.php',        // CSS, Google Fonts ve modern defer JS kuyrukları
    '/inc/template-tags.php',  // SVG ikonlar, rozetler ve yardımcı fonksiyonlar
    '/inc/customizer.php',     // Tema ayarları (Duyuru çubuğu, telefon, kargo limiti)
    '/inc/seo-schema.php',     // Schema.org Product, Organization ve Breadcrumbs
];

foreach ($mis360_includes as $inc_file) {
    $filepath = MIS360_FURNITURE_DIR . $inc_file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

// WooCommerce Entegrasyonu
if (class_exists('WooCommerce')) {
    require_once MIS360_FURNITURE_DIR . '/inc/woocommerce.php';
}

/**
 * Güvenlik ve Hız İyileştirmeleri
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
/**
 * Otomatik Hesabim (My Account) Sayfasi Olusturucu
 */
function mis360_ensure_my_account_page(): void {
    if (!function_exists('get_page_by_path')) {
        return;
    }
    if (!get_page_by_path('my-account', OBJECT, 'page')) {
        wp_insert_post([
            'post_title'     => 'Hesabım',
            'post_name'      => 'my-account',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_content'   => '<!-- wp:woocommerce/my-account -->[woocommerce_my_account]<!-- /wp:woocommerce/my-account -->',
            'comment_status' => 'closed',
        ]);
    }
}
add_action('after_switch_theme', 'mis360_ensure_my_account_page');


/**
 * WooCommerce Zorunlu Eklenti Kontrolü ve Yönetici Uyarısı
 */
function mis360_check_woocommerce_dependency(): void {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            $is_installed = file_exists(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php');
            $activate_url = wp_nonce_url(
                admin_url('plugins.php?action=activate&plugin=woocommerce/woocommerce.php'),
                'activate-plugin_woocommerce/woocommerce.php'
            );
            $install_url = wp_nonce_url(
                admin_url('update.php?action=install-plugin&plugin=woocommerce'),
                'install-plugin_woocommerce'
            );
            ?>
            <div class="notice notice-error" style="border-left-color: #f59e0b; padding: 16px 20px; background: #fffcf0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <span style="font-size: 36px; line-height: 1;">🧸</span>
                    <div>
                        <h3 style="margin: 0 0 6px 0; color: #78350f; font-size: 16px; font-weight: 800;">
                            Mis360-Furniture Teması: WooCommerce Eklentisi Zorunludur!
                        </h3>
                        <p style="margin: 0 0 10px 0; color: #92400e; font-size: 13px; max-width: 750px;">
                            Emdief Home markası için hazırlanan bu tema tam teşekküllü bir e-ticaret platformudur. Sepet, kargo takibi, canlı sipariş aşamaları ve mağaza altyapısının çalışabilmesi için <strong>WooCommerce</strong> eklentisinin kurulu ve aktif olması zorunludur.
                        </p>
                        <p style="margin: 0;">
                            <?php if ($is_installed): ?>
                                <a href="<?php echo esc_url($activate_url); ?>" class="button button-primary" style="background: #f59e0b; border-color: #d97706; font-weight: 800; padding: 4px 14px; height: auto;">
                                    ⚡ WooCommerce'i Şimdi Etkinleştir
                                </a>
                            <?php else: ?>
                                <a href="<?php echo esc_url($install_url); ?>" class="button button-primary" style="background: #f59e0b; border-color: #d97706; font-weight: 800; padding: 4px 14px; height: auto;">
                                    ⚡ WooCommerce'i Şimdi Yükle ve Etkinleştir
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        });
    }
}
add_action('admin_init', 'mis360_check_woocommerce_dependency');



