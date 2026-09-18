<?php
/**
 * Mis360-Mobilya Ziyaretçi Yolculuğu & Sepet Takip Modülü (Visitor Journey & Cart Tracker)
 * 
 * Özellikler:
 * - Kim hangi ürünü ne zaman inceledi?
 * - Ürünü sepete ekledi mi, eklemeden mi çıktı?
 * - Terk edilen sepetler (ürünler, tutar ve kayıtlı müşteri bilgisi)
 * - Ziyaretçi zaman tüneli (baştan sona tüm gezinme ve alışveriş adımları)
 * - Ürün ilgi & sepet dönüşüm analitiği
 * - LiteSpeed Cache / Full-Page Cache uyumlu asenkron Beacon mimarisi (0 ms gecikme)
 * - Özel optimize edilmiş veritabanı tabloları ve otomatik günlük temizleme cron'u
 *
 * @package Mis360-Mobilya
 * @version 1.0.0
 * @author Serkan AKKAYA
 */

if (!defined('ABSPATH')) {
    exit;
}

// -----------------------------------------------------------------------------
// 1. VERİTABANI TABLOLARININ KURULUMU (dbDelta)
// -----------------------------------------------------------------------------
function mis360_tracker_install_tables() {
    global $wpdb;

    $installed_ver = get_option('mis360_tracker_db_version', '0');
    $current_ver   = '1.0.0';

    if ($installed_ver === $current_ver) {
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();
    $sessions_table  = $wpdb->prefix . 'mis360_visitor_sessions';
    $events_table    = $wpdb->prefix . 'mis360_visitor_events';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql_sessions = "CREATE TABLE $sessions_table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_hash VARCHAR(64) NOT NULL,
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        user_name VARCHAR(150) DEFAULT '',
        user_email VARCHAR(150) DEFAULT '',
        user_phone VARCHAR(50) DEFAULT '',
        ip_address VARCHAR(45) DEFAULT '',
        city VARCHAR(100) DEFAULT '',
        device_type VARCHAR(20) DEFAULT 'desktop',
        browser VARCHAR(50) DEFAULT '',
        referrer VARCHAR(255) DEFAULT '',
        first_seen DATETIME NOT NULL,
        last_activity DATETIME NOT NULL,
        pageviews INT UNSIGNED DEFAULT 1,
        cart_status VARCHAR(20) DEFAULT 'viewing',
        cart_items_count INT UNSIGNED DEFAULT 0,
        cart_total DECIMAL(10,2) DEFAULT 0.00,
        order_id BIGINT(20) UNSIGNED DEFAULT 0,
        PRIMARY KEY  (id),
        UNIQUE KEY idx_session_hash (session_hash),
        KEY idx_user_id (user_id),
        KEY idx_last_activity (last_activity),
        KEY idx_cart_status (cart_status)
    ) $charset_collate;";

    $sql_events = "CREATE TABLE $events_table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id BIGINT(20) UNSIGNED NOT NULL,
        event_type VARCHAR(40) NOT NULL,
        product_id BIGINT(20) UNSIGNED DEFAULT 0,
        product_name VARCHAR(255) DEFAULT '',
        product_price DECIMAL(10,2) DEFAULT 0.00,
        product_image VARCHAR(255) DEFAULT '',
        quantity INT DEFAULT 1,
        page_url VARCHAR(255) DEFAULT '',
        duration_sec INT DEFAULT 0,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY idx_session_id (session_id),
        KEY idx_event_type (event_type),
        KEY idx_product_id (product_id),
        KEY idx_created_at (created_at)
    ) $charset_collate;";

    dbDelta($sql_sessions);
    dbDelta($sql_events);

    update_option('mis360_tracker_db_version', $current_ver);
}
add_action('init', 'mis360_tracker_install_tables');

// -----------------------------------------------------------------------------
// 2. YARDIMCI FONKSİYONLAR: BOT, CİHAZ, IP & REFERRER TESPİTİ
// -----------------------------------------------------------------------------

/**
 * Arama motoru örümcekleri ve botları filtreler
 */
function mis360_tracker_is_bot() {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $bots = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'googlebot', 'bingbot', 'yandex',
        'duckduckbot', 'baiduspider', 'semrush', 'ahrefs', 'mj12bot', 'dotbot', 'petalbot',
        'lighthouse', 'gtmetrix', 'pingdom', 'headlesschrome', 'curl', 'wget', 'python'
    ];
    foreach ($bots as $bot) {
        if (strpos($ua, $bot) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Cihaz türünü tespit eder (Mobil, Tablet, Masaüstü)
 */
function mis360_tracker_detect_device() {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
        return 'tablet';
    }
    if (preg_match('/(mobi|iphone|ipod|blackberry|opera mini|iemobile|wpdesktop)/i', $ua)) {
        return 'mobile';
    }
    return 'desktop';
}

/**
 * Tarayıcı adını tespit eder
 */
function mis360_tracker_detect_browser() {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (preg_match('/Edg/i', $ua)) return 'Edge';
    if (preg_match('/Chrome/i', $ua)) return 'Chrome';
    if (preg_match('/Safari/i', $ua)) return 'Safari';
    if (preg_match('/Firefox/i', $ua)) return 'Firefox';
    if (preg_match('/Opera|OPR/i', $ua)) return 'Opera';
    return 'Diğer';
}

/**
 * KVKK Uyumlu İstemci IP Adresi (Opsiyonel Maskeli)
 */
function mis360_tracker_get_ip() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $ip = sanitize_text_field(trim($ip));

    if (get_option('mis360_tracker_mask_ip', 'yes') === 'yes') {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.***';
            }
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return substr($ip, 0, strrpos($ip, ':')) . ':****';
        }
    }
    return $ip;
}

/**
 * Referrer / Giriş Kaynağını tespit eder
 */
function mis360_tracker_detect_referrer($custom_ref = '') {
    $ref = !empty($custom_ref) ? esc_url_raw($custom_ref) : (isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '');
    if (empty($ref)) {
        return 'Direkt / Doğrudan';
    }
    $host = parse_url($ref, PHP_URL_HOST);
    if (!$host || $host === parse_url(home_url(), PHP_URL_HOST)) {
        return 'Doğrudan / Site İçi';
    }
    if (stripos($host, 'instagram') !== false) return 'Instagram';
    if (stripos($host, 'facebook') !== false || stripos($host, 'fb.com') !== false) return 'Facebook';
    if (stripos($host, 'google') !== false) return 'Google';
    if (stripos($host, 'tiktok') !== false) return 'TikTok';
    if (stripos($host, 'youtube') !== false) return 'YouTube';
    if (stripos($host, 'yandex') !== false) return 'Yandex';
    if (stripos($host, 'bing') !== false) return 'Bing';
    if (stripos($host, 'pinterest') !== false) return 'Pinterest';
    if (stripos($host, 'twitter') !== false || stripos($host, 'x.com') !== false) return 'X (Twitter)';
    if (stripos($host, 'whatsapp') !== false) return 'WhatsApp';
    return $host;
}

// -----------------------------------------------------------------------------
// 3. OTURUM YÖNETİMİ & VERİTABANI İŞLEMLERİ
// -----------------------------------------------------------------------------

/**
 * Ziyaretçinin benzersiz oturum hash'ini döndürür veya oluşturur
 */
function mis360_tracker_get_session_hash() {
    $cookie_name = 'mis360_vtr_sid';
    if (!empty($_COOKIE[$cookie_name]) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE[$cookie_name])) {
        return sanitize_text_field($_COOKIE[$cookie_name]);
    }
    
    // Yeni hash oluştur
    $new_hash = bin2hex(random_bytes(16));
    
    // Çerez başlıkları henüz gönderilmemişse çerezi ayarla (30 gün)
    if (!headers_sent()) {
        $cookie_lifetime = time() + (30 * DAY_IN_SECONDS);
        setcookie($cookie_name, $new_hash, [
            'expires'  => $cookie_lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => is_ssl(),
            'httponly' => false, // JS beacon okuyabilsin
            'samesite' => 'Lax'
        ]);
        $_COOKIE[$cookie_name] = $new_hash;
    }
    
    return $new_hash;
}

/**
 * Oturum kaydını veritabanında bulur veya oluşturup günceller
 */
function mis360_tracker_get_or_create_session($custom_hash = '', $referrer = '') {
    global $wpdb;

    // Takip modülü kapalıysa çık
    if (get_option('mis360_tracker_enabled', 'yes') === 'no') {
        return null;
    }

    // Botları engelle
    if (mis360_tracker_is_bot()) {
        return null;
    }

    // Admin hariç tutma ayarı aktifse ve kullanıcı yöneticiyse çık (Varsayılan: 'no')
    if (get_option('mis360_tracker_ignore_admin', 'no') === 'yes' && current_user_can('manage_options')) {
        return null;
    }

    $sessions_table = $wpdb->prefix . 'mis360_visitor_sessions';
    static $tables_checked = false;
    if (!$tables_checked) {
        $tables_checked = true;
        if ($wpdb->get_var("SHOW TABLES LIKE '$sessions_table'") !== $sessions_table) {
            mis360_tracker_install_tables();
        }
    }
    $session_hash   = !empty($custom_hash) ? sanitize_text_field($custom_hash) : mis360_tracker_get_session_hash();

    $session = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $sessions_table WHERE session_hash = %s LIMIT 1",
        $session_hash
    ));

    $now = current_time('mysql');
    $current_user_id = get_current_user_id();
    $user_name  = '';
    $user_email = '';
    $user_phone = '';

    if ($current_user_id > 0) {
        $user = get_userdata($current_user_id);
        if ($user) {
            $user_name  = trim($user->first_name . ' ' . $user->last_name);
            if (empty($user_name)) {
                $user_name = $user->display_name ?: $user->user_login;
            }
            $user_email = $user->user_email;
            $user_phone = get_user_meta($current_user_id, 'billing_phone', true) ?: '';
        }
    }

    if ($session) {
        // Oturumu güncelle
        $update_data = [
            'last_activity' => $now,
            'pageviews'     => $session->pageviews + 1
        ];
        if ($current_user_id > 0 && empty($session->user_id)) {
            $update_data['user_id']    = $current_user_id;
            $update_data['user_name']  = $user_name;
            $update_data['user_email'] = $user_email;
            if (!empty($user_phone)) {
                $update_data['user_phone'] = $user_phone;
            }
        }
        $wpdb->update($sessions_table, $update_data, ['id' => $session->id]);
        $session->last_activity = $now;
        $session->pageviews++;
        return $session;
    }

    // Yeni Oturum Oluştur
    $insert_data = [
        'session_hash'     => $session_hash,
        'user_id'          => $current_user_id,
        'user_name'        => $user_name,
        'user_email'       => $user_email,
        'user_phone'       => $user_phone,
        'ip_address'       => mis360_tracker_get_ip(),
        'city'             => '',
        'device_type'      => mis360_tracker_detect_device(),
        'browser'          => mis360_tracker_detect_browser(),
        'referrer'         => mis360_tracker_detect_referrer($referrer),
        'first_seen'       => $now,
        'last_activity'    => $now,
        'pageviews'        => 1,
        'cart_status'      => 'viewing',
        'cart_items_count' => 0,
        'cart_total'       => 0.00,
        'order_id'         => 0
    ];

    $wpdb->insert($sessions_table, $insert_data);
    $session_id = $wpdb->insert_id;

    if ($session_id) {
        $insert_data['id'] = $session_id;
        return (object) $insert_data;
    }

    return null;
}

/**
 * Yeni bir olay (event) kaydeder
 */
function mis360_tracker_log_event($session_id, $event_type, $product_id = 0, $product_name = '', $product_price = 0, $product_image = '', $quantity = 1, $page_url = '') {
    global $wpdb;

    if (!$session_id) {
        return false;
    }

    $events_table = $wpdb->prefix . 'mis360_visitor_events';
    $now = current_time('mysql');

    // Ürün bilgileri eksikse ve product_id varsa otomatik tamamla
    if ($product_id > 0) {
        if (empty($product_name)) {
            $product_name = get_the_title($product_id);
        }
        if (empty($product_price) && function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
            if ($product) {
                $product_price = (float) $product->get_price();
            }
        }
        if (empty($product_image)) {
            $img_id = get_post_thumbnail_id($product_id);
            if ($img_id) {
                $product_image = wp_get_attachment_image_url($img_id, 'thumbnail');
            }
        }
    }

    // Aynı oturumda aynı sayfanın veya ürünün son 5 saniye içinde mükerrer kaydını engelle
    if (in_array($event_type, ['view_product', 'page_view'])) {
        $recent = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $events_table 
             WHERE session_id = %d AND event_type = %s AND (product_id = %d OR page_url = %s)
             AND created_at >= %s LIMIT 1",
            $session_id,
            $event_type,
            $product_id,
            $page_url,
            date('Y-m-d H:i:s', current_time('timestamp') - 5)
        ));
        if ($recent) {
            return false;
        }
    }

    return $wpdb->insert($events_table, [
        'session_id'    => $session_id,
        'event_type'    => sanitize_key($event_type),
        'product_id'    => absint($product_id),
        'product_name'  => sanitize_text_field($product_name),
        'product_price' => (float) $product_price,
        'product_image' => esc_url_raw($product_image),
        'quantity'      => intval($quantity) ?: 1,
        'page_url'      => esc_url_raw($page_url),
        'duration_sec'  => 0,
        'created_at'    => $now
    ]);
}

// -----------------------------------------------------------------------------
// 4. LITESPEED / ÖNBELLEK DOSTU ASENKRON BEACON UÇ NOKTASI (0 MS GECİKME)
// -----------------------------------------------------------------------------

/**
 * Tarayıcıdan gönderilen navigator.sendBeacon veya AJAX isteklerini yakalar
 */
function mis360_ajax_tracker_beacon() {
    // Ham veriyi al (sendBeacon genelde json veya post body olarak gönderir)
    $input = file_get_contents('php://input');
    $data = [];
    if (!empty($input)) {
        $decoded = json_decode($input, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }

    if (empty($data) && !empty($_POST)) {
        $data = $_POST;
    }

    $event_type    = isset($data['event']) ? sanitize_key($data['event']) : 'view_product';
    $product_id    = isset($data['product_id']) ? absint($data['product_id']) : 0;
    $product_name  = isset($data['product_name']) ? sanitize_text_field($data['product_name']) : '';
    $product_price = isset($data['product_price']) ? (float) $data['product_price'] : 0.0;
    $product_image = isset($data['product_image']) ? esc_url_raw($data['product_image']) : '';
    $page_url      = isset($data['page_url']) ? esc_url_raw($data['page_url']) : '';
    $referrer      = isset($data['referrer']) ? esc_url_raw($data['referrer']) : '';
    $custom_hash   = isset($data['sid']) ? sanitize_text_field($data['sid']) : '';

    if (empty($custom_hash) && !empty($_COOKIE['mis360_vtr_sid'])) {
        $custom_hash = sanitize_text_field($_COOKIE['mis360_vtr_sid']);
    }

    $session = mis360_tracker_get_or_create_session($custom_hash, $referrer);
    if ($session) {
        mis360_tracker_log_event(
            $session->id,
            $event_type,
            $product_id,
            $product_name,
            $product_price,
            $product_image,
            1,
            $page_url
        );
    }

    // Beacon cevap beklemez, ancak REST/AJAX için başarı dönelim
    wp_send_json_success(['tracked' => true]);
}
add_action('wp_ajax_mis360_track_beacon', 'mis360_ajax_tracker_beacon');
add_action('wp_ajax_nopriv_mis360_track_beacon', 'mis360_ajax_tracker_beacon');

// -----------------------------------------------------------------------------
// 5. WOOCOMMERCE DOĞAL KANCALARI (SEPETE EKLEME, ÇIKARMA, SATIN ALMA)
// -----------------------------------------------------------------------------

/**
 * Sepete ürün eklendiğinde tetiklenir
 */
function mis360_tracker_on_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    global $wpdb;

    $session = mis360_tracker_get_or_create_session();
    if (!$session) {
        return;
    }

    $target_id = $variation_id > 0 ? $variation_id : $product_id;
    $product = wc_get_product($target_id);
    if (!$product) {
        $product = wc_get_product($product_id);
    }

    $product_name  = $product ? $product->get_name() : get_the_title($product_id);
    $product_price = $product ? (float) $product->get_price() : 0.0;
    $img_id        = $product ? $product->get_image_id() : get_post_thumbnail_id($product_id);
    $product_image = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : '';

    // Olayı kaydet
    mis360_tracker_log_event(
        $session->id,
        'add_to_cart',
        $product_id,
        $product_name,
        $product_price,
        $product_image,
        $quantity
    );

    // Oturum durumunu güncelle
    $cart = WC()->cart;
    $cart_count = $cart ? $cart->get_cart_contents_count() : 1;
    $cart_total = $cart ? (float) $cart->get_cart_contents_total() : $product_price * $quantity;

    $wpdb->update(
        $wpdb->prefix . 'mis360_visitor_sessions',
        [
            'cart_status'      => 'cart_added',
            'cart_items_count' => $cart_count,
            'cart_total'       => $cart_total,
            'last_activity'    => current_time('mysql')
        ],
        ['id' => $session->id]
    );
}
add_action('woocommerce_add_to_cart', 'mis360_tracker_on_add_to_cart', 10, 6);

/**
 * Sepetten ürün çıkarıldığında tetiklenir
 */
function mis360_tracker_on_remove_cart_item($cart_item_key, $cart) {
    global $wpdb;

    $session = mis360_tracker_get_or_create_session();
    if (!$session || empty($cart->removed_cart_contents[$cart_item_key])) {
        return;
    }

    $item = $cart->removed_cart_contents[$cart_item_key];
    $product_id = $item['product_id'] ?? 0;
    $quantity   = $item['quantity'] ?? 1;

    mis360_tracker_log_event($session->id, 'remove_from_cart', $product_id, '', 0, '', $quantity);

    $remaining_count = $cart->get_cart_contents_count();
    $remaining_total = (float) $cart->get_cart_contents_total();
    $new_status = ($remaining_count > 0) ? 'cart_added' : 'viewing';

    $wpdb->update(
        $wpdb->prefix . 'mis360_visitor_sessions',
        [
            'cart_status'      => $new_status,
            'cart_items_count' => $remaining_count,
            'cart_total'       => $remaining_total,
            'last_activity'    => current_time('mysql')
        ],
        ['id' => $session->id]
    );
}
add_action('woocommerce_cart_item_removed', 'mis360_tracker_on_remove_cart_item', 10, 2);

/**
 * Ödeme sayfasına girildiğinde
 */
function mis360_tracker_on_checkout_page() {
    if (function_exists('is_checkout') && is_checkout() && !is_order_received_page()) {
        global $wpdb;
        $session = mis360_tracker_get_or_create_session();
        if ($session && $session->cart_status !== 'purchased') {
            mis360_tracker_log_event($session->id, 'checkout_start', 0, 'Ödeme Sayfası Başlatıldı');
            $wpdb->update(
                $wpdb->prefix . 'mis360_visitor_sessions',
                ['cart_status' => 'checkout', 'last_activity' => current_time('mysql')],
                ['id' => $session->id]
            );
        }
    }
}
add_action('template_redirect', 'mis360_tracker_on_checkout_page');

/**
 * Sipariş tamamlandığında (Satın alma)
 */
function mis360_tracker_on_purchase($order_id) {
    if (!$order_id) return;
    global $wpdb;

    $order = wc_get_order($order_id);
    if (!$order) return;

    $session = mis360_tracker_get_or_create_session();
    if (!$session) return;

    $order_total = (float) $order->get_total();
    $customer_name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
    $customer_email = $order->get_billing_email();
    $customer_phone = $order->get_billing_phone();

    // Sipariş olayını kaydet
    mis360_tracker_log_event(
        $session->id,
        'purchase',
        0,
        sprintf(__('Sipariş Tamamlandı #%s (Toplam: %s)', 'mis360-mobilya'), $order->get_order_number(), wc_price($order_total)),
        $order_total,
        '',
        1
    );

    // Oturumu "purchased" olarak mühürle
    $wpdb->update(
        $wpdb->prefix . 'mis360_visitor_sessions',
        [
            'cart_status'      => 'purchased',
            'order_id'         => $order_id,
            'user_name'        => !empty($customer_name) ? $customer_name : $session->user_name,
            'user_email'       => !empty($customer_email) ? $customer_email : $session->user_email,
            'user_phone'       => !empty($customer_phone) ? $customer_phone : $session->user_phone,
            'cart_total'       => $order_total,
            'last_activity'    => current_time('mysql')
        ],
        ['id' => $session->id]
    );
}
add_action('woocommerce_thankyou', 'mis360_tracker_on_purchase', 10, 1);

// -----------------------------------------------------------------------------
// 6. ASENKRON JS İÇİN VERİ BESLEMESİ (WP ENQUEUE HOOK)
// -----------------------------------------------------------------------------
function mis360_tracker_enqueue_script_data() {
    if (get_option('mis360_tracker_enabled', 'yes') === 'no') {
        return;
    }

    $product_data = null;
    if (function_exists('is_product') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $img_id = $product->get_image_id();
            $product_data = [
                'id'    => $product->get_id(),
                'name'  => $product->get_name(),
                'price' => (float) $product->get_price(),
                'image' => $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : ''
            ];
        }
    }

    $tracker_data = [
        'ajaxUrl'     => admin_url('admin-ajax.php'),
        'sid'         => mis360_tracker_get_session_hash(),
        'isProduct'   => !empty($product_data),
        'product'     => $product_data,
        'pageUrl'     => home_url(add_query_arg([], $GLOBALS['wp']->request ?? '')),
        'referrer'    => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : ''
    ];

    wp_localize_script('mis360-main-js', 'mis360TrackerData', $tracker_data);
}
add_action('wp_enqueue_scripts', 'mis360_tracker_enqueue_script_data', 20);

// -----------------------------------------------------------------------------
// 7. OTOMATİK VERİTABANI TEMİZLEME CRON GÖREVİ (DATABASE PURGE)
// -----------------------------------------------------------------------------
function mis360_tracker_register_cron() {
    if (!wp_next_scheduled('mis360_tracker_daily_purge_cron')) {
        wp_schedule_event(time() + 3600, 'daily', 'mis360_tracker_daily_purge_cron');
    }
}
add_action('init', 'mis360_tracker_register_cron');

function mis360_tracker_execute_purge() {
    global $wpdb;

    $days = (int) get_option('mis360_tracker_retention_days', 30);
    if ($days < 1) $days = 30;

    $sessions_table = $wpdb->prefix . 'mis360_visitor_sessions';
    $events_table   = $wpdb->prefix . 'mis360_visitor_events';

    $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    // Eski olayları sil
    $wpdb->query($wpdb->prepare(
        "DELETE FROM $events_table WHERE created_at < %s",
        $cutoff_date
    ));

    // Eski oturumları sil
    $wpdb->query($wpdb->prepare(
        "DELETE FROM $sessions_table WHERE last_activity < %s",
        $cutoff_date
    ));

    update_option('mis360_tracker_last_purge', current_time('mysql'));
}
add_action('mis360_tracker_daily_purge_cron', 'mis360_tracker_execute_purge');

// -----------------------------------------------------------------------------
// 8. WP ADMIN YÖNETİM MENÜSÜ & PANELİ
// -----------------------------------------------------------------------------
function mis360_tracker_admin_menu() {
    $parent = class_exists('WooCommerce') ? 'woocommerce' : 'tools.php';
    add_submenu_page(
        $parent,
        __('Ziyaretçi & Sepet Takibi', 'mis360-mobilya'),
        __('Ziyaretçi & Sepet Takibi', 'mis360-mobilya'),
        'manage_woocommerce',
        'mis360-visitor-tracker',
        'mis360_tracker_render_admin_page'
    );
}
add_action('admin_menu', 'mis360_tracker_admin_menu', 60);

/**
 * Admin Panel Ana Görünümü
 */
function mis360_tracker_render_admin_page() {
    if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
        wp_die(__('Bu sayfaya erişim yetkiniz bulunmuyor.', 'mis360-mobilya'));
    }

    global $wpdb;
    $sessions_table = $wpdb->prefix . 'mis360_visitor_sessions';
    $events_table   = $wpdb->prefix . 'mis360_visitor_events';

    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'stream';
    $notice = null;

    // Ayarları Kaydetme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('mis360_tracker_settings_action', 'mis360_tracker_nonce')) {
        if (isset($_POST['save_tracker_settings'])) {
            update_option('mis360_tracker_enabled', isset($_POST['tracker_enabled']) ? 'yes' : 'no');
            update_option('mis360_tracker_ignore_admin', isset($_POST['tracker_ignore_admin']) ? 'yes' : 'no');
            update_option('mis360_tracker_mask_ip', isset($_POST['tracker_mask_ip']) ? 'yes' : 'no');
            update_option('mis360_tracker_retention_days', absint($_POST['tracker_retention_days'] ?? 30));
            $notice = __('Ayarlar başarıyla kaydedildi.', 'mis360-mobilya');
        } elseif (isset($_POST['manual_purge_tracker'])) {
            mis360_tracker_execute_purge();
            $notice = __('Geçmiş loglar ve eski oturumlar başarıyla temizlendi.', 'mis360-mobilya');
        } elseif (isset($_POST['wipe_all_tracker'])) {
            $wpdb->query("TRUNCATE TABLE $events_table");
            $wpdb->query("TRUNCATE TABLE $sessions_table");
            $notice = __('Tüm takip verileri sıfırlandı.', 'mis360-mobilya');
        }
    }

    // Terk edilmiş sepetleri hesaplama eşiği (20 dakika öncesi ve sepetinde ürün olup sipariş vermeyenler)
    $abandon_threshold = date('Y-m-d H:i:s', current_time('timestamp') - (20 * MINUTE_IN_SECONDS));

    // KPI Metrikleri
    $today_start = date('Y-m-d 00:00:00');
    $stats_today_visitors = (int) $wpdb->get_var("SELECT COUNT(*) FROM $sessions_table WHERE last_activity >= '$today_start'");
    $stats_today_views    = (int) $wpdb->get_var("SELECT COUNT(*) FROM $events_table WHERE event_type = 'view_product' AND created_at >= '$today_start'");
    $stats_today_carts    = (int) $wpdb->get_var("SELECT COUNT(*) FROM $sessions_table WHERE cart_items_count > 0 AND last_activity >= '$today_start'");
    $stats_abandoned      = (int) $wpdb->get_var("SELECT COUNT(*) FROM $sessions_table WHERE cart_status IN ('cart_added', 'checkout') AND cart_items_count > 0 AND order_id = 0 AND last_activity <= '$abandon_threshold'");
    $stats_abandoned_val  = (float) $wpdb->get_var("SELECT SUM(cart_total) FROM $sessions_table WHERE cart_status IN ('cart_added', 'checkout') AND cart_items_count > 0 AND order_id = 0 AND last_activity <= '$abandon_threshold'");
    $stats_purchased      = (int) $wpdb->get_var("SELECT COUNT(*) FROM $sessions_table WHERE cart_status = 'purchased'");

    // Sepete Dönüşüm Oranı
    $conversion_rate = ($stats_today_visitors > 0) ? round(($stats_today_carts / $stats_today_visitors) * 100, 1) : 0;
    ?>
    <div class="wrap mis360-tracker-admin">
        <!-- Başlık ve Üst Panel -->
        <div class="tracker-header">
            <div class="tracker-header-title">
                <h1>
                    <span class="dashicons dashicons-visibility"></span> 
                    <?php esc_html_e('Emdief Home | Ziyaretçi & Sepet Takip Merkezi', 'mis360-mobilya'); ?>
                </h1>
                <p class="tracker-header-desc">
                    <?php esc_html_e('Müşterilerinizin hangi ürünleri gezdiğini, hangilerini sepete ekleyip hangilerini terk ettiğini 0 ms gecikmeyle anlık izleyin.', 'mis360-mobilya'); ?>
                </p>
            </div>
            <div class="tracker-header-badge">
                <?php if (get_option('mis360_tracker_enabled', 'yes') === 'yes'): ?>
                    <span class="tracker-live-pulse"></span>
                    <strong><?php esc_html_e('Gerçek Zamanlı Takip Devrede', 'mis360-mobilya'); ?></strong>
                <?php else: ?>
                    <span class="tracker-status-off"></span>
                    <strong><?php esc_html_e('Takip Pasif', 'mis360-mobilya'); ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($notice): ?>
            <div class="notice notice-success is-dismissible" style="margin: 15px 0 20px;"><p><?php echo esc_html($notice); ?></p></div>
        <?php endif; ?>

        <!-- KPI Kartları -->
        <div class="tracker-kpi-grid">
            <div class="tracker-kpi-card">
                <div class="kpi-icon kpi-blue"><span class="dashicons dashicons-groups"></span></div>
                <div class="kpi-info">
                    <span class="kpi-label"><?php esc_html_e('Bugünkü Ziyaretçi', 'mis360-mobilya'); ?></span>
                    <strong class="kpi-value"><?php echo number_format_i18n($stats_today_visitors); ?></strong>
                </div>
            </div>

            <div class="tracker-kpi-card">
                <div class="kpi-icon kpi-purple"><span class="dashicons dashicons-products"></span></div>
                <div class="kpi-info">
                    <span class="kpi-label"><?php esc_html_e('Bugünkü Ürün İnceleme', 'mis360-mobilya'); ?></span>
                    <strong class="kpi-value"><?php echo number_format_i18n($stats_today_views); ?></strong>
                </div>
            </div>

            <div class="tracker-kpi-card">
                <div class="kpi-icon kpi-amber"><span class="dashicons dashicons-cart"></span></div>
                <div class="kpi-info">
                    <span class="kpi-label"><?php esc_html_e('Sepete Ekleyenler (Bugün)', 'mis360-mobilya'); ?></span>
                    <strong class="kpi-value"><?php echo number_format_i18n($stats_today_carts); ?> <small style="font-size:13px; font-weight:normal;">(%<?php echo esc_html($conversion_rate); ?>)</small></strong>
                </div>
            </div>

            <div class="tracker-kpi-card">
                <div class="kpi-icon kpi-red"><span class="dashicons dashicons-warning"></span></div>
                <div class="kpi-info">
                    <span class="kpi-label"><?php esc_html_e('Terk Edilen Sepetler', 'mis360-mobilya'); ?></span>
                    <strong class="kpi-value" style="color:#d63638;"><?php echo number_format_i18n($stats_abandoned); ?> <small style="font-size:13px; color:#d63638;">(<?php echo wc_price($stats_abandoned_val); ?>)</small></strong>
                </div>
            </div>

            <div class="tracker-kpi-card">
                <div class="kpi-icon kpi-green"><span class="dashicons dashicons-yes-alt"></span></div>
                <div class="kpi-info">
                    <span class="kpi-label"><?php esc_html_e('Tamamlanan Sipariş', 'mis360-mobilya'); ?></span>
                    <strong class="kpi-value" style="color:#28a745;"><?php echo number_format_i18n($stats_purchased); ?></strong>
                </div>
            </div>
        </div>

        <!-- Çok Sekmeli Navigasyon -->
        <h2 class="nav-tab-wrapper mis360-nav-tabs">
            <a href="<?php echo esc_url(add_query_arg(['tab' => 'stream'])); ?>" class="nav-tab <?php echo $active_tab === 'stream' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-randomize"></span> <?php esc_html_e('Canlı Ziyaretçi Akışı', 'mis360-mobilya'); ?>
            </a>
            <a href="<?php echo esc_url(add_query_arg(['tab' => 'products'])); ?>" class="nav-tab <?php echo $active_tab === 'products' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-chart-area"></span> <?php esc_html_e('Ürün İlgi & Sepet Dönüşümü', 'mis360-mobilya'); ?>
            </a>
            <a href="<?php echo esc_url(add_query_arg(['tab' => 'abandoned'])); ?>" class="nav-tab <?php echo $active_tab === 'abandoned' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Terk Edilen Sepetler', 'mis360-mobilya'); ?>
                <?php if ($stats_abandoned > 0): ?>
                    <span class="tab-counter"><?php echo esc_html($stats_abandoned); ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo esc_url(add_query_arg(['tab' => 'settings'])); ?>" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Ayarlar & Bakım', 'mis360-mobilya'); ?>
            </a>
        </h2>

        <div class="tracker-tab-content">
            <?php
            switch ($active_tab) {
                case 'products':
                    mis360_tracker_render_tab_products();
                    break;
                case 'abandoned':
                    mis360_tracker_render_tab_abandoned($abandon_threshold);
                    break;
                case 'settings':
                    mis360_tracker_render_tab_settings();
                    break;
                case 'stream':
                default:
                    mis360_tracker_render_tab_stream($abandon_threshold);
                    break;
            }
            ?>
        </div>

        <!-- Ziyaretçi Zaman Tüneli Modalı (AJAX) -->
        <div id="mis360-journey-modal" class="mis360-modal-backdrop" style="display:none;">
            <div class="mis360-modal-content">
                <div class="mis360-modal-header">
                    <h3 id="mis360-modal-visitor-title">
                        <span class="dashicons dashicons-backup"></span> 
                        <?php esc_html_e('Ziyaretçi Gezinme & Alışveriş Yolculuğu', 'mis360-mobilya'); ?>
                    </h3>
                    <button type="button" class="mis360-modal-close" onclick="mis360CloseJourneyModal();">&times;</button>
                </div>
                <div class="mis360-modal-body" id="mis360-journey-modal-body">
                    <div class="mis360-loading-spinner">
                        <span class="spinner is-active"></span> <?php esc_html_e('Yolculuk adımları yükleniyor...', 'mis360-mobilya'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Panel Stilleri -->
    <style>
        .mis360-tracker-admin { margin-top: 15px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
        .tracker-header { display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 22px 26px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 20px; }
        .tracker-header-title h1 { margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .tracker-header-title h1 .dashicons { font-size: 26px; width: 26px; height: 26px; color: #6366f1; }
        .tracker-header-desc { margin: 0; color: #64748b; font-size: 13.5px; }
        .tracker-header-badge { display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 8px 14px; border-radius: 9999px; border: 1px solid #e2e8f0; font-size: 13px; color: #334155; }
        .tracker-live-pulse { width: 10px; height: 10px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); animation: mis360Pulse 2s infinite; }
        .tracker-status-off { width: 10px; height: 10px; border-radius: 50%; background: #94a3b8; }
        @keyframes mis360Pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* KPI Kartları */
        .tracker-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 22px; }
        .tracker-kpi-card { background: #fff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 14px; }
        .kpi-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .kpi-icon .dashicons { font-size: 22px; width: 22px; height: 22px; }
        .kpi-blue { background: #eff6ff; color: #2563eb; }
        .kpi-purple { background: #faf5ff; color: #9333ea; }
        .kpi-amber { background: #fffbeb; color: #d97706; }
        .kpi-red { background: #fef2f2; color: #dc2626; }
        .kpi-green { background: #f0fdf4; color: #16a34a; }
        .kpi-info { display: flex; flex-direction: column; }
        .kpi-label { font-size: 12px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.4px; }
        .kpi-value { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 2px; }

        /* Sekmeler */
        .mis360-nav-tabs { border-bottom: 2px solid #e2e8f0; margin-bottom: 18px; display: flex; gap: 4px; }
        .mis360-nav-tabs .nav-tab { font-size: 13.5px; font-weight: 600; padding: 9px 16px; border-radius: 8px 8px 0 0; display: inline-flex; align-items: center; gap: 6px; }
        .mis360-nav-tabs .nav-tab-active { border-color: #6366f1; border-bottom-color: #fff; background: #fff; color: #4f46e5; }
        .tab-counter { background: #ef4444; color: #fff; font-size: 11px; padding: 1px 7px; border-radius: 9999px; margin-left: 4px; }

        /* Tablo ve Kartlar */
        .tracker-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.02); }
        .tracker-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .tracker-table th { background: #f8fafc; color: #475569; font-weight: 600; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.3px; padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .tracker-table td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #1e293b; vertical-align: middle; }
        .tracker-table tr:hover td { background: #f8fafc; }

        /* Durum Rozetleri */
        .badge-status { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; }
        .badge-viewing { background: #f1f5f9; color: #475569; }
        .badge-cart_added { background: #fef3c7; color: #b45309; }
        .badge-abandoned { background: #fee2e2; color: #b91c1c; }
        .badge-purchased { background: #dcfce7; color: #15803d; }
        .badge-checkout { background: #e0e7ff; color: #4338ca; }

        /* Butonlar */
        .btn-view-journey { background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 5px 11px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 4px; }
        .btn-view-journey:hover { background: #6366f1; border-color: #6366f1; color: #fff; }

        /* Modal */
        .mis360-modal-backdrop { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 999999; display: flex; align-items: center; justify-content: center; }
        .mis360-modal-content { background: #fff; width: 92%; max-width: 680px; max-height: 85vh; border-radius: 12px; box-shadow: 0 20px 35px -5px rgba(0,0,0,0.2); display: flex; flex-direction: column; overflow: hidden; }
        .mis360-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .mis360-modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .mis360-modal-close { background: none; border: none; font-size: 26px; cursor: pointer; color: #64748b; line-height: 1; padding: 0 4px; }
        .mis360-modal-close:hover { color: #dc2626; }
        .mis360-modal-body { padding: 22px 26px; overflow-y: auto; }

        /* Zaman Tüneli (Timeline) */
        .journey-timeline { position: relative; padding-left: 28px; margin: 10px 0; border-left: 2px solid #e2e8f0; }
        .journey-item { position: relative; margin-bottom: 22px; }
        .journey-item:last-child { margin-bottom: 0; }
        .journey-dot { position: absolute; left: -36px; top: 2px; width: 16px; height: 16px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 1px #cbd5e1; }
        .dot-view { background: #6366f1; }
        .dot-cart { background: #f59e0b; }
        .dot-remove { background: #ef4444; }
        .dot-checkout { background: #3b82f6; }
        .dot-purchase { background: #10b981; }
        .journey-time { font-size: 11.5px; color: #64748b; font-weight: 600; margin-bottom: 3px; }
        .journey-title { font-size: 13.5px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .journey-product-thumb { width: 34px; height: 34px; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0; }
    </style>

    <!-- Modal AJAX Scripti -->
    <script>
        function mis360OpenJourneyModal(sessionId, visitorName) {
            const modal = document.getElementById('mis360-journey-modal');
            const title = document.getElementById('mis360-modal-visitor-title');
            const body = document.getElementById('mis360-journey-modal-body');
            
            if (!modal) return;
            title.innerHTML = '<span class="dashicons dashicons-backup"></span> ' + (visitorName ? visitorName + ' - ' : '') + 'Zaman Tüneli';
            body.innerHTML = '<div class="mis360-loading-spinner"><span class="spinner is-active"></span> Yükleniyor...</div>';
            modal.style.display = 'flex';

            const formData = new FormData();
            formData.append('action', 'mis360_get_visitor_journey');
            formData.append('session_id', sessionId);
            formData.append('nonce', '<?php echo wp_create_nonce("mis360_journey_nonce"); ?>');

            fetch(ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.html) {
                    body.innerHTML = data.data.html;
                } else {
                    body.innerHTML = '<p style="color:#d63638;">Bu ziyaretçi için geçmiş hareket kaydı bulunamadı.</p>';
                }
            })
            .catch(err => {
                body.innerHTML = '<p style="color:#d63638;">Veri yüklenirken hata oluştu: ' + err.message + '</p>';
            });
        }

        function mis360CloseJourneyModal() {
            const modal = document.getElementById('mis360-journey-modal');
            if (modal) modal.style.display = 'none';
        }

        window.onclick = function(e) {
            const modal = document.getElementById('mis360-journey-modal');
            if (modal && e.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>
    <?php
}

/**
 * Sekme 1: Canlı Ziyaretçi Akışı Tablosu
 */
function mis360_tracker_render_tab_stream($abandon_threshold) {
    global $wpdb;
    $sessions_table = $wpdb->prefix . 'mis360_visitor_sessions';
    $events_table   = $wpdb->prefix . 'mis360_visitor_events';

    $filter = isset($_GET['filter']) ? sanitize_key($_GET['filter']) : 'all';
    $where = "1=1";

    if ($filter === 'cart') {
        $where .= " AND cart_items_count > 0";
    } elseif ($filter === 'abandoned') {
        $where .= " AND cart_status IN ('cart_added', 'checkout') AND cart_items_count > 0 AND order_id = 0 AND last_activity <= '$abandon_threshold'";
    } elseif ($filter === 'purchased') {
        $where .= " AND cart_status = 'purchased'";
    } elseif ($filter === 'members') {
        $where .= " AND user_id > 0";
    }

    $sessions = $wpdb->get_results(
        "SELECT * FROM $sessions_table WHERE $where ORDER BY last_activity DESC LIMIT 40"
    );
    ?>
    <div class="tracker-card">
        <!-- Filtreleme Butonları -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                <a href="<?php echo esc_url(add_query_arg(['tab' => 'stream', 'filter' => 'all'])); ?>" class="button <?php echo $filter === 'all' ? 'button-primary' : ''; ?>"><?php esc_html_e('Tümü', 'mis360-mobilya'); ?></a>
                <a href="<?php echo esc_url(add_query_arg(['tab' => 'stream', 'filter' => 'cart'])); ?>" class="button <?php echo $filter === 'cart' ? 'button-primary' : ''; ?>"><?php esc_html_e('🛒 Sepete Ekleyenler', 'mis360-mobilya'); ?></a>
                <a href="<?php echo esc_url(add_query_arg(['tab' => 'stream', 'filter' => 'abandoned'])); ?>" class="button <?php echo $filter === 'abandoned' ? 'button-primary' : ''; ?>" style="color:#d63638;"><?php esc_html_e('🔴 Sepeti Terk Edenler', 'mis360-mobilya'); ?></a>
                <a href="<?php echo esc_url(add_query_arg(['tab' => 'stream', 'filter' => 'purchased'])); ?>" class="button <?php echo $filter === 'purchased' ? 'button-primary' : ''; ?>" style="color:#28a745;"><?php esc_html_e('🏆 Satın Alanlar', 'mis360-mobilya'); ?></a>
                <a href="<?php echo esc_url(add_query_arg(['tab' => 'stream', 'filter' => 'members'])); ?>" class="button <?php echo $filter === 'members' ? 'button-primary' : ''; ?>"><?php esc_html_e('👤 Kayıtlı Üyeler', 'mis360-mobilya'); ?></a>
            </div>
            <span style="font-size:12.5px; color:#64748b;">
                <?php printf(esc_html__('Son %d hareket gösteriliyor', 'mis360-mobilya'), count($sessions)); ?>
            </span>
        </div>

        <?php if (empty($sessions)): ?>
            <p style="text-align:center; padding: 40px 0; color:#64748b; font-size:14px;">
                <?php esc_html_e('Henüz filtrenize uygun bir ziyaretçi hareketi kaydedilmedi.', 'mis360-mobilya'); ?>
            </p>
        <?php else: ?>
            <table class="tracker-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Ziyaretçi / Müşteri', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Kaynak / Cihaz', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Mevcut Durum', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('İncelediği Son Sayfa / Ürün', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Sepet', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Son Hareket', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Aksiyon', 'mis360-mobilya'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $s): 
                        // Terk edilmiş sepet tespiti
                        $is_abandoned = in_array($s->cart_status, ['cart_added', 'checkout']) && $s->cart_items_count > 0 && empty($s->order_id) && $s->last_activity <= $abandon_threshold;
                        
                        // Son olayı çek (ürün veya sayfa)
                        $last_event = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM $events_table WHERE session_id = %d ORDER BY id DESC LIMIT 1",
                            $s->id
                        ));

                        $is_admin = ($s->user_id > 0 && user_can($s->user_id, 'manage_options'));
                        $is_me    = ($s->user_id > 0 && $s->user_id == get_current_user_id());
                        $visitor_display = !empty($s->user_name) ? esc_html($s->user_name) : sprintf(esc_html__('Misafir #%s', 'mis360-mobilya'), substr($s->session_hash, 0, 6));
                    ?>
                        <tr>
                            <td>
                                <strong><?php echo $visitor_display; ?></strong>
                                <?php if ($is_admin): ?>
                                    <span style="background:#ede9fe; color:#6d28d9; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:700; margin-left:4px;">👑 <?php esc_html_e('Yönetici', 'mis360-mobilya'); ?></span>
                                <?php endif; ?>
                                <?php if ($is_me): ?>
                                    <span style="background:#dbeafe; color:#1e40af; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:700; margin-left:4px;"><?php esc_html_e('(Siz)', 'mis360-mobilya'); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($s->user_email)): ?>
                                    <div style="font-size:12px; color:#64748b;"><?php echo esc_html($s->user_email); ?></div>
                                <?php endif; ?>
                                <div style="font-size:11px; color:#94a3b8;"><?php echo esc_html($s->ip_address); ?></div>
                            </td>
                            <td>
                                <span style="font-weight:500;"><?php echo esc_html($s->referrer); ?></span>
                                <div style="font-size:11.5px; color:#64748b;">
                                    <?php echo esc_html(ucfirst($s->device_type)); ?> • <?php echo esc_html($s->browser); ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($s->cart_status === 'purchased'): ?>
                                    <span class="badge-status badge-purchased">🏆 <?php esc_html_e('Satın Aldı', 'mis360-mobilya'); ?></span>
                                <?php elseif ($is_abandoned): ?>
                                    <span class="badge-status badge-abandoned">🔴 <?php esc_html_e('Sepeti Terk Etti', 'mis360-mobilya'); ?></span>
                                <?php elseif ($s->cart_status === 'checkout'): ?>
                                    <span class="badge-status badge-checkout">💳 <?php esc_html_e('Ödeme Sayfasında', 'mis360-mobilya'); ?></span>
                                <?php elseif ($s->cart_items_count > 0): ?>
                                    <span class="badge-status badge-cart_added">🛒 <?php esc_html_e('Sepete Ekledi', 'mis360-mobilya'); ?></span>
                                <?php else: ?>
                                    <span class="badge-status badge-viewing">👁️ <?php esc_html_e('Geziniyor', 'mis360-mobilya'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($last_event): ?>
                                    <?php if ($last_event->event_type === 'view_product' && !empty($last_event->product_name)): ?>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <?php if (!empty($last_event->product_image)): ?>
                                                <img src="<?php echo esc_url($last_event->product_image); ?>" class="journey-product-thumb" alt="" />
                                            <?php endif; ?>
                                            <div>
                                                <a href="<?php echo esc_url(get_permalink($last_event->product_id) ?: $last_event->page_url); ?>" target="_blank" style="text-decoration:none; font-weight:600; color:#1e293b;">
                                                    <?php echo esc_html($last_event->product_name); ?>
                                                </a>
                                                <?php if ($last_event->product_price > 0): ?>
                                                    <div style="font-size:12px; color:#16a34a; font-weight:600;">
                                                        <?php echo wc_price($last_event->product_price); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php elseif ($last_event->event_type === 'page_view'): ?>
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="font-size:16px;">📄</span>
                                            <div>
                                                <a href="<?php echo esc_url($last_event->page_url); ?>" target="_blank" style="text-decoration:none; font-weight:600; color:#1e293b;">
                                                    <?php echo esc_html($last_event->product_name ?: __('Sayfa Ziyareti', 'mis360-mobilya')); ?>
                                                </a>
                                                <div style="font-size:11px; color:#94a3b8;"><?php echo esc_html(wp_parse_url($last_event->page_url, PHP_URL_PATH) ?: '/'); ?></div>
                                            </div>
                                        </div>
                                    <?php elseif ($last_event->event_type === 'add_to_cart'): ?>
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="font-size:16px;">🛒</span>
                                            <div>
                                                <strong><?php esc_html_e('Sepete Ekledi:', 'mis360-mobilya'); ?></strong>
                                                <span style="color:#b45309;"><?php echo esc_html($last_event->product_name); ?></span>
                                            </div>
                                        </div>
                                    <?php elseif ($last_event->event_type === 'checkout_start'): ?>
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="font-size:16px;">💳</span>
                                            <strong><?php esc_html_e('Ödeme Başlatıldı', 'mis360-mobilya'); ?></strong>
                                        </div>
                                    <?php elseif ($last_event->event_type === 'purchase'): ?>
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="font-size:16px;">🎉</span>
                                            <strong style="color:#16a34a;"><?php echo esc_html($last_event->product_name); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:12.5px;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($s->cart_items_count > 0): ?>
                                    <strong><?php echo esc_html($s->cart_items_count); ?> <?php esc_html_e('ürün', 'mis360-mobilya'); ?></strong>
                                    <div style="font-size:12px; color:#d97706; font-weight:700;">
                                        <?php echo wc_price($s->cart_total); ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">0 ₺</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span title="<?php echo esc_attr($s->last_activity); ?>">
                                    <?php echo human_time_diff(strtotime($s->last_activity), current_time('timestamp')); ?> <?php esc_html_e('önce', 'mis360-mobilya'); ?>
                                </span>
                                <div style="font-size:11px; color:#94a3b8;"><?php echo esc_html($s->pageviews); ?> <?php esc_html_e('sayfa', 'mis360-mobilya'); ?></div>
                            </td>
                            <td>
                                <button type="button" class="btn-view-journey" onclick="mis360OpenJourneyModal(<?php echo (int) $s->id; ?>, '<?php echo esc_js($visitor_display); ?>');">
                                    <span class="dashicons dashicons-backup" style="font-size:15px; width:15px; height:15px;"></span>
                                    <?php esc_html_e('Yolculuğu Gör', 'mis360-mobilya'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Sekme 2: Ürün İlgi & Sepet Dönüşüm Analizi
 */
function mis360_tracker_render_tab_products() {
    global $wpdb;
    $events_table = $wpdb->prefix . 'mis360_visitor_events';

    // Ürün bazında view, cart ve purchase sayılarını grupla
    $results = $wpdb->get_results(
        "SELECT 
            product_id, 
            product_name, 
            product_price, 
            product_image,
            COUNT(CASE WHEN event_type = 'view_product' THEN 1 END) as view_count,
            COUNT(CASE WHEN event_type = 'add_to_cart' THEN 1 END) as cart_count
         FROM $events_table 
         WHERE product_id > 0
         GROUP BY product_id 
         ORDER BY view_count DESC 
         LIMIT 50"
    );
    ?>
    <div class="tracker-card">
        <div style="margin-bottom: 18px;">
            <h3 style="margin:0 0 6px 0; font-size:16px; color:#1e293b;"><?php esc_html_e('Ürün İlgi & Sepete Eklenme Dönüşüm Oranları', 'mis360-mobilya'); ?></h3>
            <p style="margin:0; font-size:13px; color:#64748b;"><?php esc_html_e('Hangi ürünlerin çok incelendiğini, hangilerinin sepete eklendiğini ve ilgi görüp satın alınmayan potansiyel ürünleri görün.', 'mis360-mobilya'); ?></p>
        </div>

        <?php if (empty($results)): ?>
            <p style="text-align:center; padding: 40px 0; color:#64748b; font-size:14px;">
                <?php esc_html_e('Henüz ürün inceleme verisi birikmedi.', 'mis360-mobilya'); ?>
            </p>
        <?php else: ?>
            <table class="tracker-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Ürün', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Fiyat', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('İnceleme Sayısı (Views)', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Sepete Eklenme', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Sepet Dönüşüm Oranı (%)', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Durum Analizi', 'mis360-mobilya'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): 
                        $views = (int) $r->view_count;
                        $carts = (int) $r->cart_count;
                        $ratio = ($views > 0) ? round(($carts / $views) * 100, 1) : 0;
                    ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if (!empty($r->product_image)): ?>
                                        <img src="<?php echo esc_url($r->product_image); ?>" class="journey-product-thumb" alt="" />
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?php echo esc_url(get_permalink($r->product_id)); ?>" target="_blank" style="text-decoration:none; font-weight:600; color:#1e293b;">
                                            <?php echo esc_html($r->product_name); ?>
                                        </a>
                                        <div style="font-size:11.5px; color:#94a3b8;">ID: #<?php echo esc_html($r->product_id); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong style="color:#16a34a;"><?php echo wc_price($r->product_price); ?></strong>
                            </td>
                            <td>
                                <strong style="font-size:15px; color:#2563eb;"><?php echo number_format_i18n($views); ?></strong>
                            </td>
                            <td>
                                <strong style="font-size:15px; color:#d97706;"><?php echo number_format_i18n($carts); ?></strong>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="flex:1; max-width:100px; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                                        <div style="width:<?php echo min(100, $ratio); ?>%; height:100%; background:#10b981; border-radius:4px;"></div>
                                    </div>
                                    <span style="font-weight:700; font-size:13px; color:#1e293b;">%<?php echo esc_html($ratio); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($views >= 10 && $carts === 0): ?>
                                    <span style="background:#fef2f2; color:#b91c1c; padding:3px 8px; border-radius:6px; font-size:11.5px; font-weight:600;">
                                        ⚠️ Yüksek İlgi / 0 Sepet (Fiyatı veya açıklamayı inceleyin)
                                    </span>
                                <?php elseif ($ratio >= 15): ?>
                                    <span style="background:#f0fdf4; color:#15803d; padding:3px 8px; border-radius:6px; font-size:11.5px; font-weight:600;">
                                        🔥 Çok Yüksek Dönüşüm
                                    </span>
                                <?php else: ?>
                                    <span style="color:#64748b; font-size:12px;">Normal Akış</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Sekme 3: Terk Edilen Sepetler (Kurtarma Listesi)
 */
function mis360_tracker_render_tab_abandoned($abandon_threshold) {
    global $wpdb;
    $sessions_table = $wpdb->prefix . 'mis360_visitor_sessions';
    $events_table   = $wpdb->prefix . 'mis360_visitor_events';

    $sessions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $sessions_table 
         WHERE cart_status IN ('cart_added', 'checkout') 
         AND cart_items_count > 0 
         AND order_id = 0 
         AND last_activity <= %s 
         ORDER BY last_activity DESC 
         LIMIT 50",
        $abandon_threshold
    ));
    ?>
    <div class="tracker-card">
        <div style="margin-bottom: 18px;">
            <h3 style="margin:0 0 6px 0; font-size:16px; color:#1e293b;"><?php esc_html_e('Terk Edilen Sepetler & Potansiyel Ciro Kaybı', 'mis360-mobilya'); ?></h3>
            <p style="margin:0; font-size:13px; color:#64748b;"><?php esc_html_e('Sepete ürün ekleyip son 20 dakikadır herhangi bir işlem yapmadan siteden ayrılan müşteriler.', 'mis360-mobilya'); ?></p>
        </div>

        <?php if (empty($sessions)): ?>
            <p style="text-align:center; padding: 40px 0; color:#16a34a; font-size:14px; font-weight:600;">
                🎉 <?php esc_html_e('Şu an terk edilmiş aktif bir sepet bulunmuyor!', 'mis360-mobilya'); ?>
            </p>
        <?php else: ?>
            <table class="tracker-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Müşteri Bilgisi', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Terk Edilen Ürünler', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Toplam Tutar', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Terk Edilme Zamanı', 'mis360-mobilya'); ?></th>
                        <th><?php esc_html_e('Aksiyon', 'mis360-mobilya'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $s): 
                        // Sepete atılan ürünleri çek
                        $cart_items = $wpdb->get_results($wpdb->prepare(
                            "SELECT DISTINCT product_id, product_name, product_price, product_image 
                             FROM $events_table 
                             WHERE session_id = %d AND event_type = 'add_to_cart'",
                            $s->id
                        ));
                        $is_member = $s->user_id > 0;
                        $visitor_title = $is_member ? esc_html($s->user_name) : sprintf(esc_html__('Misafir #%s', 'mis360-mobilya'), substr($s->session_hash, 0, 6));
                    ?>
                        <tr>
                            <td>
                                <strong><?php echo $visitor_title; ?></strong>
                                <?php if (!empty($s->user_email)): ?>
                                    <div style="font-size:12px; color:#2563eb;">✉️ <?php echo esc_html($s->user_email); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($s->user_phone)): ?>
                                    <div style="font-size:12px; color:#16a34a;">📞 <?php echo esc_html($s->user_phone); ?></div>
                                <?php endif; ?>
                                <div style="font-size:11px; color:#94a3b8;"><?php echo esc_html($s->referrer); ?></div>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <?php foreach ($cart_items as $ci): ?>
                                        <div style="display:flex; align-items:center; gap:6px; font-size:12.5px;">
                                            <?php if (!empty($ci->product_image)): ?>
                                                <img src="<?php echo esc_url($ci->product_image); ?>" style="width:24px; height:24px; border-radius:4px; object-fit:cover;" alt="" />
                                            <?php endif; ?>
                                            <span><?php echo esc_html($ci->product_name); ?></span>
                                            <strong style="color:#16a34a; font-size:11.5px;"><?php echo wc_price($ci->product_price); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <strong style="font-size:15px; color:#dc2626;"><?php echo wc_price($s->cart_total); ?></strong>
                            </td>
                            <td>
                                <span><?php echo human_time_diff(strtotime($s->last_activity), current_time('timestamp')); ?> <?php esc_html_e('önce', 'mis360-mobilya'); ?></span>
                                <div style="font-size:11px; color:#94a3b8;"><?php echo esc_html($s->last_activity); ?></div>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <button type="button" class="btn-view-journey" onclick="mis360OpenJourneyModal(<?php echo (int) $s->id; ?>, '<?php echo esc_js($visitor_title); ?>');">
                                        <?php esc_html_e('Yolculuğu Gör', 'mis360-mobilya'); ?>
                                    </button>
                                    <?php if (!empty($s->user_phone)): ?>
                                        <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $s->user_phone)); ?>" target="_blank" class="button button-small" style="background:#25d366; color:#fff; border-color:#25d366;">
                                            WhatsApp
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Sekme 4: Ayarlar & Bakım
 */
function mis360_tracker_render_tab_settings() {
    $enabled       = get_option('mis360_tracker_enabled', 'yes');
    $ignore_admin  = get_option('mis360_tracker_ignore_admin', 'no');
    $mask_ip       = get_option('mis360_tracker_mask_ip', 'yes');
    $retention     = get_option('mis360_tracker_retention_days', 30);
    $last_purge    = get_option('mis360_tracker_last_purge', __('Henüz çalışmadı', 'mis360-mobilya'));
    ?>
    <div class="tracker-card" style="max-width: 800px;">
        <form method="post" action="">
            <?php wp_nonce_field('mis360_tracker_settings_action', 'mis360_tracker_nonce'); ?>

            <h3 style="margin:0 0 16px 0; font-size:16px; color:#1e293b;"><?php esc_html_e('Takip ve Performans Ayarları', 'mis360-mobilya'); ?></h3>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Ziyaretçi Takip Motoru', 'mis360-mobilya'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="tracker_enabled" value="1" <?php checked($enabled, 'yes'); ?> />
                            <strong><?php esc_html_e('Takip motorunu aktif et', 'mis360-mobilya'); ?></strong>
                        </label>
                        <p class="description"><?php esc_html_e('İşaret kaldırılırsa arka plan ürün ve sepet izleme sinyalleri durdurulur.', 'mis360-mobilya'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Yönetici Ziyaretlerini Yoksay', 'mis360-mobilya'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="tracker_ignore_admin" value="1" <?php checked($ignore_admin, 'yes'); ?> />
                            <?php esc_html_e('Admin paneline giriş yapmış yöneticilerin hareketlerini kaydetme', 'mis360-mobilya'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Test yaparken kendi gezinmelerinizin istatistikleri bozmasını engeller.', 'mis360-mobilya'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('KVKK IP Maskeleme', 'mis360-mobilya'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="tracker_mask_ip" value="1" <?php checked($mask_ip, 'yes'); ?> />
                            <?php esc_html_e('Ziyaretçi IP adreslerini maskele (Örn: 192.168.1.***)', 'mis360-mobilya'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Kişisel Verilerin Korunması Kanunu ve GDPR standartlarına tam uyumluluk sağlar.', 'mis360-mobilya'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Veri Saklama Süresi', 'mis360-mobilya'); ?></th>
                    <td>
                        <select name="tracker_retention_days">
                            <option value="15" <?php selected($retention, 15); ?>>15 Gün</option>
                            <option value="30" <?php selected($retention, 30); ?>>30 Gün (Önerilen)</option>
                            <option value="60" <?php selected($retention, 60); ?>>60 Gün</option>
                            <option value="90" <?php selected($retention, 90); ?>>90 Gün</option>
                        </select>
                        <p class="description"><?php esc_html_e('Belirtilen süreden eski ziyaretçi olayları her gece çalışan WP Cron tarafından otomatik temizlenir.', 'mis360-mobilya'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Son Temizleme Zamanı', 'mis360-mobilya'); ?></th>
                    <td>
                        <code><?php echo esc_html($last_purge); ?></code>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="save_tracker_settings" class="button button-primary"><?php esc_html_e('Ayarları Kaydet', 'mis360-mobilya'); ?></button>
                <button type="submit" name="manual_purge_tracker" class="button button-secondary" style="margin-left: 8px;"><?php esc_html_e('Eski Logları Şimdi Temizle', 'mis360-mobilya'); ?></button>
                <button type="submit" name="wipe_all_tracker" class="button button-link-delete" style="margin-left: 14px; color:#dc2626;" onclick="return confirm('TÜM takip veritabanı sıfırlanacaktır. Emin misiniz?');">
                    <?php esc_html_e('Tüm Verileri Sıfırla', 'mis360-mobilya'); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}

// -----------------------------------------------------------------------------
// 9. AJAX: ZİYARETÇİ ZAMAN TÜNELİ DETAYI (JOURNEY MODAL)
// -----------------------------------------------------------------------------
function mis360_ajax_get_visitor_journey() {
    check_ajax_referer('mis360_journey_nonce', 'nonce');

    if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Yetkisiz erişim']);
    }

    global $wpdb;
    $session_id = absint($_POST['session_id'] ?? 0);
    if (!$session_id) {
        wp_send_json_error(['message' => 'Geçersiz oturum ID']);
    }

    $sessions_table = $wpdb->prefix . 'mis360_visitor_sessions';
    $events_table   = $wpdb->prefix . 'mis360_visitor_events';

    $session = $wpdb->get_row($wpdb->prepare("SELECT * FROM $sessions_table WHERE id = %d", $session_id));
    if (!$session) {
        wp_send_json_error(['message' => 'Oturum bulunamadı']);
    }

    $events = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $events_table WHERE session_id = %d ORDER BY created_at ASC LIMIT 100",
        $session_id
    ));

    ob_start();
    ?>
    <div style="background:#f8fafc; padding:14px 18px; border-radius:8px; margin-bottom:20px; border:1px solid #e2e8f0; font-size:13px; line-height:1.6;">
        <div><strong><?php esc_html_e('Giriş Zamanı:', 'mis360-mobilya'); ?></strong> <?php echo esc_html($session->first_seen); ?></div>
        <div><strong><?php esc_html_e('Giriş Kaynağı:', 'mis360-mobilya'); ?></strong> <?php echo esc_html($session->referrer); ?></div>
        <div><strong><?php esc_html_e('Cihaz / Tarayıcı:', 'mis360-mobilya'); ?></strong> <?php echo esc_html(ucfirst($session->device_type)); ?> • <?php echo esc_html($session->browser); ?> (<?php echo esc_html($session->ip_address); ?>)</div>
        <?php if (!empty($session->user_name)): ?>
            <div><strong><?php esc_html_e('Müşteri:', 'mis360-mobilya'); ?></strong> <?php echo esc_html($session->user_name); ?> (<?php echo esc_html($session->user_email); ?>)</div>
        <?php endif; ?>
    </div>

    <div class="journey-timeline">
        <!-- Başlangıç -->
        <div class="journey-item">
            <span class="journey-dot dot-view"></span>
            <div class="journey-time"><?php echo date_i18n('H:i:s', strtotime($session->first_seen)); ?></div>
            <div class="journey-title">
                🚀 <?php printf(esc_html__('Siteye Giriş Yaptı (%s)', 'mis360-mobilya'), esc_html($session->referrer)); ?>
            </div>
        </div>

        <?php if (!empty($events)): ?>
            <?php foreach ($events as $ev): 
                $dot_class = 'dot-view';
                $icon = '👁️';
                $action_label = __('Ürün İnceledi', 'mis360-mobilya');

                if ($ev->event_type === 'page_view') {
                    $dot_class = 'dot-view';
                    $icon = '📄';
                    $action_label = __('Sayfa Ziyareti', 'mis360-mobilya');
                } elseif ($ev->event_type === 'add_to_cart') {
                    $dot_class = 'dot-cart';
                    $icon = '🛒';
                    $action_label = __('Sepete Ekledi', 'mis360-mobilya');
                } elseif ($ev->event_type === 'remove_from_cart') {
                    $dot_class = 'dot-remove';
                    $icon = '❌';
                    $action_label = __('Sepetten Çıkardı', 'mis360-mobilya');
                } elseif ($ev->event_type === 'checkout_start') {
                    $dot_class = 'dot-checkout';
                    $icon = '💳';
                    $action_label = __('Ödeme Adımına Geçti', 'mis360-mobilya');
                } elseif ($ev->event_type === 'purchase') {
                    $dot_class = 'dot-purchase';
                    $icon = '🎉';
                    $action_label = __('Satın Alma Tamamlandı!', 'mis360-mobilya');
                }
            ?>
                <div class="journey-item">
                    <span class="journey-dot <?php echo esc_attr($dot_class); ?>"></span>
                    <div class="journey-time"><?php echo date_i18n('H:i:s', strtotime($ev->created_at)); ?></div>
                    <div class="journey-title">
                        <span><?php echo $icon; ?> <strong><?php echo esc_html($action_label); ?>:</strong></span>
                        <?php if (!empty($ev->product_image)): ?>
                            <img src="<?php echo esc_url($ev->product_image); ?>" class="journey-product-thumb" alt="" />
                        <?php endif; ?>
                        <span>
                            <?php if ($ev->product_id > 0): ?>
                                <a href="<?php echo esc_url(get_permalink($ev->product_id)); ?>" target="_blank" style="text-decoration:none; color:#1e293b;">
                                    <?php echo esc_html($ev->product_name); ?>
                                </a>
                            <?php elseif (!empty($ev->page_url)): ?>
                                <a href="<?php echo esc_url($ev->page_url); ?>" target="_blank" style="text-decoration:none; color:#1e293b;">
                                    <?php echo esc_html($ev->product_name ?: __('Sayfa', 'mis360-mobilya')); ?>
                                </a>
                            <?php else: ?>
                                <?php echo esc_html($ev->product_name); ?>
                            <?php endif; ?>
                            <?php if ($ev->product_price > 0): ?>
                                <strong style="color:#16a34a; margin-left:4px;">(<?php echo wc_price($ev->product_price); ?>)</strong>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Son Durum Bitişi -->
        <div class="journey-item">
            <?php if ($session->cart_status === 'purchased'): ?>
                <span class="journey-dot dot-purchase"></span>
                <div class="journey-time"><?php echo date_i18n('H:i:s', strtotime($session->last_activity)); ?></div>
                <div class="journey-title" style="color:#16a34a;">
                    🏆 <?php esc_html_e('Başarıyla Alışverişi Tamamladı', 'mis360-mobilya'); ?>
                </div>
            <?php elseif ($session->cart_items_count > 0): ?>
                <span class="journey-dot dot-remove"></span>
                <div class="journey-time"><?php echo date_i18n('H:i:s', strtotime($session->last_activity)); ?></div>
                <div class="journey-title" style="color:#dc2626;">
                    🔴 <?php esc_html_e('Sepeti Terk Etti (Satın almadan siteden ayrıldı)', 'mis360-mobilya'); ?>
                </div>
            <?php else: ?>
                <span class="journey-dot dot-view"></span>
                <div class="journey-time"><?php echo date_i18n('H:i:s', strtotime($session->last_activity)); ?></div>
                <div class="journey-title" style="color:#64748b;">
                    🚪 <?php esc_html_e('Sepete ürün atmadan siteden ayrıldı', 'mis360-mobilya'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_mis360_get_visitor_journey', 'mis360_ajax_get_visitor_journey');
