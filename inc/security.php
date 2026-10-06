<?php
/**
 * Emdief Home & Mis360 Security Hardening Engine
 *
 * Implements industry-standard security protections:
 * 1. HTTP Security Headers (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy)
 * 2. Information Disclosure Prevention (Remove WP version, script version tags, X-Pingback)
 * 3. XML-RPC Complete Disablement (Mitigates brute-force & DDoS)
 * 4. User Enumeration Protection (Block /?author=N and protect /wp-json/wp/v2/users)
 * 5. Obfuscated Login Error Messages (Prevents username harvesting)
 * 6. Hardened .htaccess Server Rules (Protects wp-config.php, htaccess, prevents directory indexing)
 *
 * @package Mis360-Mobilya
 * @version 1.9.53
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. HTTP Security Headers
 */
function mis360_security_send_headers() {
    if (headers_sent()) {
        return;
    }

    // Clickjacking koruması (Sitenin başka siteler içinde iframe ile açılmasını engeller)
    header('X-Frame-Options: SAMEORIGIN');

    // MIME türü koklama (MIME-sniffing) koruması
    header('X-Content-Type-Options: nosniff');

    // Cross-Site Scripting (XSS) filtre koruması
    header('X-XSS-Protection: 1; mode=block');

    // Referrer sızıntı koruması
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // İstenmeyen tarayıcı donanım erişimlerini kısıtlama
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // PHP sürüm bilgisini başlıklardan kaldır
    if (function_exists('header_remove')) {
        header_remove('X-Powered-By');
    }
}
add_action('send_headers', 'mis360_security_send_headers', 1);

/**
 * 2. Bilgi Sızıntısını Önleme (Information Disclosure)
 */
// WordPress sürüm etiketini kaldır
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// CSS ve JS dosyalarındaki WordPress sürüm parametrelerini (?ver=6.x) ön yüzde gizle
function mis360_security_remove_version_strings($src) {
    if (is_admin() || strpos($src, 'ver=') === false) {
        return $src;
    }
    return remove_query_arg('ver', $src);
}
add_filter('style_loader_src', 'mis360_security_remove_version_strings', 9999);
add_filter('script_loader_src', 'mis360_security_remove_version_strings', 9999);

// Gereksiz ve açık oluşturan head etiketlerini temizle
function mis360_security_clean_head_tags() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('template_redirect', 'rest_output_link_header', 11);
}
add_action('init', 'mis360_security_clean_head_tags');

/**
 * 3. XML-RPC Tamamen Kapatma (Brute Force & DDoS Koruması)
 */
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', '__return_empty_array');

// X-Pingback HTTP başlığını sil
function mis360_security_remove_pingback_header($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'mis360_security_remove_pingback_header');

/**
 * 4. Kullanıcı Adı Taramasını Engelleme (User Enumeration Protection)
 */
// /?author=1, /?author=2 gibi URL'lerle yönetici kullanıcı adı tespitini engelle
function mis360_security_block_author_scans() {
    if (is_admin()) {
        return;
    }
    if (isset($_REQUEST['author']) && !empty($_REQUEST['author'])) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'mis360_security_block_author_scans', 1);

// REST API üzerinden kullanıcı listesinin (/wp-json/wp/v2/users) anonim olarak çekilmesini engelle
function mis360_security_protect_rest_users($response, $handler, $request) {
    if (!is_user_logged_in()) {
        $route = $request->get_route();
        if (strpos($route, '/wp/v2/users') !== false) {
            return new WP_Error(
                'rest_cannot_access',
                __('Bu alana erişim yetkiniz bulunmamaktadır.', 'mis360-mobilya'),
                ['status' => 403]
            );
        }
    }
    return $response;
}
add_filter('rest_request_before_callbacks', 'mis360_security_protect_rest_users', 10, 3);

/**
 * 5. Güvenli Giriş Hata Mesajları (Brute-Force & Enumeration Engeli)
 */
function mis360_security_generic_login_errors() {
    return __('Girdiğiniz kullanıcı adı, e-posta veya şifre hatalıdır. Lütfen bilgilerinizi kontrol ediniz.', 'mis360-mobilya');
}
add_filter('login_errors', 'mis360_security_generic_login_errors');

/**
 * 6. Sunucu Seviyesi Güvenlik Kuralları (.htaccess Enjeksiyonu)
 */
function mis360_security_update_htaccess() {
    try {
        if (!function_exists('get_home_path')) {
            if (defined('ABSPATH') && file_exists(ABSPATH . 'wp-admin/includes/file.php')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
        }
        $home_path = function_exists('get_home_path') ? get_home_path() : (defined('ABSPATH') ? ABSPATH : '');
        if (empty($home_path)) {
            return;
        }

        $htaccess_file = $home_path . '.htaccess';
        if (!file_exists($htaccess_file) || !is_writable($htaccess_file)) {
            return;
        }

        $marker = 'MIS360_SECURITY_RULES';
        $rules = [
            '# Dizin Listelemeyi Kapat',
            'Options -Indexes',
            '',
            '# Hassas Dosyalara Erisimi Engelle',
            '<FilesMatch "^(wp-config\.php|\.htaccess|readme\.html|license\.txt|composer\.json|package\.json)">',
            '  <IfModule mod_authz_core.c>',
            '    Require all denied',
            '  </IfModule>',
            '  <IfModule !mod_authz_core.c>',
            '    Order allow,deny',
            '    Deny from all',
            '  </IfModule>',
            '</FilesMatch>',
            '',
            '# XML-RPC Korumasi',
            '<Files xmlrpc.php>',
            '  <IfModule mod_authz_core.c>',
            '    Require all denied',
            '  </IfModule>',
            '  <IfModule !mod_authz_core.c>',
            '    Order allow,deny',
            '    Deny from all',
            '  </IfModule>',
            '</Files>'
        ];

        if (!function_exists('insert_with_markers')) {
            if (defined('ABSPATH') && file_exists(ABSPATH . 'wp-admin/includes/misc.php')) {
                require_once ABSPATH . 'wp-admin/includes/misc.php';
            }
        }

        if (function_exists('insert_with_markers')) {
            insert_with_markers($htaccess_file, $marker, $rules);
        }
    } catch (Throwable $e) {
        // Sessiz hata yakalama
    }
}
add_action('after_switch_theme', 'mis360_security_update_htaccess');
add_action('admin_init', 'mis360_security_update_htaccess');
