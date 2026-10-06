<?php
/**
 * Emdief Home - SEO, GEO & Site Haritaları (Sitemaps) Yönetim Merkezi
 *
 * - Admin Sol Menüsünde 1. Sınıf "SEO & GEO" Yönetim Merkezi
 * - XML Sitemap (sitemap.xml) Canlı İstatistikleri, Önizlemesi ve Yenilemesi
 * - GEO & Yerel Konum SEO (Kayseri, Kocasinan, Koordinatlar, Meta & Schema Yönetimi)
 * - LLMs.txt & LLMs-Full.txt (ChatGPT, Perplexity, Gemini, Claude Yapay Zeka SEO Düzenleyicisi)
 * - Canlı Robots.txt Editörü ve AI Bot İzinleri
 * - Google, Bing, Yandex, IndexNow Hızlı İndeksleme ve Doğrulama Kodları
 *
 * @package Mis360-Mobilya
 * @version 1.9.87
 * @author Serkan AKKAYA & MİS360
 */

defined('ABSPATH') || exit;

// 1. IndexNow Anahtar Yönetimi & Endpoint Servisi
function mis360_get_indexnow_key() {
    $key = get_option('mis360_indexnow_key');
    if (empty($key)) {
        $key = wp_generate_password(32, false, false);
        update_option('mis360_indexnow_key', $key);
    }
    return $key;
}

// IndexNow Key dosyasını dinamik olarak sun: /{key}.txt
add_action('init', 'mis360_serve_indexnow_key_file');
function mis360_serve_indexnow_key_file() {
    $key = mis360_get_indexnow_key();
    $request_uri = untrailingslashit(strtok($_SERVER['REQUEST_URI'] ?? '', '?'));
    
    if ($request_uri === '/' . $key . '.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Robots-Tag: noindex');
        echo $key;
        exit;
    }
}

// 2. IndexNow API Gönderim Motoru
function mis360_submit_to_indexnow($urls = [], $non_blocking = false) {
    if (empty($urls)) {
        return ['success' => false, 'message' => 'Gönderilecek URL bulunamadı.'];
    }

    // 403 / 429 hatası alındıysa sistemi kasmamak için geçici bekleme süresi
    if (get_transient('mis360_indexnow_cooldown')) {
        return ['success' => false, 'message' => 'IndexNow API geçici beklemede (Cooldown).'];
    }

    $urls = array_unique((array) $urls);
    $key = mis360_get_indexnow_key();
    $host = wp_parse_url(home_url(), PHP_URL_HOST);
    $key_location = home_url('/' . $key . '.txt');

    $body = [
        'host'        => $host,
        'key'         => $key,
        'keyLocation' => $key_location,
        'urlList'     => array_values($urls),
    ];

    // Arka planda asenkron istek (Sayfa yüklemesini 0 ms bekletir)
    if ($non_blocking) {
        wp_remote_post('https://api.indexnow.org/indexnow', [
            'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
            'body'        => wp_json_encode($body),
            'timeout'     => 2,
            'blocking'    => false,
        ]);
        return ['success' => true, 'message' => 'IndexNow arka planda asenkron gönderildi.'];
    }

    $response = wp_remote_post('https://api.indexnow.org/indexnow', [
        'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
        'body'        => wp_json_encode($body),
        'timeout'     => 3,
        'httpversion' => '1.1',
    ]);

    if (is_wp_error($response)) {
        return [
            'success' => false,
            'message' => 'IndexNow API Bağlantı Hatası: ' . $response->get_error_message()
        ];
    }

    $code = wp_remote_retrieve_response_code($response);
    
    if ($code === 200 || $code === 202) {
        delete_transient('mis360_indexnow_cooldown');
        return [
            'success' => true,
            'code'    => $code,
            'message' => count($urls) . ' adet URL başarıyla IndexNow ağına (Bing, Yandex, Seznam, Naver) iletildi.'
        ];
    } else {
        if ($code === 403 || $code === 429) {
            set_transient('mis360_indexnow_cooldown', 1, 2 * HOUR_IN_SECONDS);
        }
        $msg = wp_remote_retrieve_body($response);
        return [
            'success' => false,
            'code'    => $code,
            'message' => 'IndexNow HTTP ' . $code . ': ' . ($msg ? esc_html($msg) : 'Doğrulama hatası (2 saat mola verildi)')
        ];
    }
}

// 3. Arama Motoru Sitemap Ping Gönderimleri
function mis360_ping_search_engines($sitemap_url = '') {
    if (empty($sitemap_url)) {
        $sitemap_url = home_url('/sitemap.xml');
    }

    $results = [];

    // Bing Ping (Maks 3s)
    $bing_url = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url);
    $bing_res = wp_remote_get($bing_url, ['timeout' => 3]);
    $results['Bing'] = is_wp_error($bing_res) ? $bing_res->get_error_message() : ('HTTP ' . wp_remote_retrieve_response_code($bing_res));

    // Yandex Ping (Maks 3s)
    $yandex_url = 'https://webmaster.yandex.com/ping?sitemap=' . urlencode($sitemap_url);
    $yandex_res = wp_remote_get($yandex_url, ['timeout' => 3]);
    $results['Yandex'] = is_wp_error($yandex_res) ? $yandex_res->get_error_message() : ('HTTP ' . wp_remote_retrieve_response_code($yandex_res));

    // IndexNow ile Sitemap ve Ana Sayfa
    $indexnow_res = mis360_submit_to_indexnow([$sitemap_url, home_url('/')], true);
    $results['IndexNow'] = 'Asenkron gönderildi.';

    return $results;
}

// 4. Yeni İçerik veya Ürün Yayınlandığında Otomatik IndexNow Gönderimi
add_action('transition_post_status', 'mis360_auto_indexnow_on_publish', 10, 3);
function mis360_auto_indexnow_on_publish($new_status, $old_status, $post) {
    if ($old_status === 'publish' || $new_status !== 'publish' || !is_a($post, 'WP_Post')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (get_transient('mis360_indexnow_cooldown')) {
        return;
    }

    $allowed_types = ['product', 'page', 'post'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        return;
    }

    if (get_option('mis360_auto_indexnow', 'yes') !== 'yes') {
        return;
    }

    $permalink = get_permalink($post->ID);
    if ($permalink) {
        // Kesinlikle non-blocking (0 ms gecikme)
        mis360_submit_to_indexnow([$permalink], true);
    }
}

// 5. Doğrulama Meta Etiketleri ve Özel HTML Kodlarının Ön Yüze Enjeksiyonu
function mis360_render_verification_meta($name, $val) {
    $val = trim($val ?? '');
    if (empty($val)) {
        return;
    }

    if (preg_match('/content=[\'"]([^\'"]+)[\'"]/i', $val, $matches)) {
        $clean_code = trim($matches[1]);
    } else {
        $clean_code = trim(strip_tags($val));
    }

    if (!empty($clean_code)) {
        echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($clean_code) . '">' . "\n";
    }
}

add_action('wp_head', 'mis360_output_verification_and_custom_head', 1);
function mis360_output_verification_and_custom_head() {
    mis360_render_verification_meta('google-site-verification', get_option('mis360_google_verification'));
    mis360_render_verification_meta('msvalidate.01', get_option('mis360_bing_verification'));
    mis360_render_verification_meta('yandex-verification', get_option('mis360_yandex_verification'));
    mis360_render_verification_meta('facebook-domain-verification', get_option('mis360_facebook_verification'));
    mis360_render_verification_meta('p:domain_verify', get_option('mis360_pinterest_verification'));

    $custom_head = get_option('mis360_custom_header_html');
    if (!empty($custom_head)) {
        echo "\n" . $custom_head . "\n";
    }
}

add_action('wp_body_open', 'mis360_output_custom_body_html', 1);
function mis360_output_custom_body_html() {
    $custom_body = get_option('mis360_custom_body_html');
    if (!empty($custom_body)) {
        echo "\n" . $custom_body . "\n";
    }
}

add_action('wp_footer', 'mis360_output_custom_footer_html', 99);
function mis360_output_custom_footer_html() {
    $custom_footer = get_option('mis360_custom_footer_html');
    if (!empty($custom_footer)) {
        echo "\n" . $custom_footer . "\n";
    }
}

// 6. WP ADMIN ANA MENÜSÜ: SEO & GEO YÖNETİM MERKEZİ
add_action('admin_menu', 'mis360_seo_geo_admin_menu');
function mis360_seo_geo_admin_menu() {
    // 1. Ana Menü
    add_menu_page(
        'SEO & GEO Yönetim Merkezi',
        'SEO & GEO',
        'manage_options',
        'mis360-seo-geo',
        'mis360_seo_geo_admin_page',
        'dashicons-chart-area',
        58
    );

    // Alt Menüler (Hızlı Geçiş)
    add_submenu_page(
        'mis360-seo-geo',
        'Site Haritaları & İndeksleme',
        '🗺️ Sitemaps & İndeks',
        'manage_options',
        'mis360-seo-geo&tab=sitemaps',
        'mis360_seo_geo_admin_page'
    );

    add_submenu_page(
        'mis360-seo-geo',
        'Yerel SEO & GEO Konum',
        '📍 Yerel SEO & GEO',
        'manage_options',
        'mis360-seo-geo&tab=geo',
        'mis360_seo_geo_admin_page'
    );

    add_submenu_page(
        'mis360-seo-geo',
        'Yapay Zeka SEO (LLMs.txt)',
        '🤖 GEO & LLMs.txt',
        'manage_options',
        'mis360-seo-geo&tab=llms',
        'mis360_seo_geo_admin_page'
    );

    add_submenu_page(
        'mis360-seo-geo',
        'Robots.txt Düzenleyici',
        '🛡️ Robots.txt',
        'manage_options',
        'mis360-seo-geo&tab=robots',
        'mis360_seo_geo_admin_page'
    );

    add_submenu_page(
        'mis360-seo-geo',
        'Doğrulama & HTML Kodları',
        '🏷️ Doğrulama Kodları',
        'manage_options',
        'mis360-seo-geo&tab=html_tags',
        'mis360_seo_geo_admin_page'
    );

    // Geriye dönük uyumluluk (Tools.php linki)
    add_submenu_page(
        'tools.php',
        'SEO & GEO Yönetimi',
        'SEO & GEO',
        'manage_options',
        'mis360-indexing',
        'mis360_seo_geo_admin_page'
    );
}

// 7. ADMIN PANEL SAYFASI
function mis360_seo_geo_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Bu sayfaya erişim yetkiniz bulunmuyor.', 'mis360-mobilya'));
    }

    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'sitemaps';
    if ($active_tab === 'indexing') {
        $active_tab = 'sitemaps';
    }

    $notice = null;
    $notice_type = 'info';

    // POST İŞLEMLERİ
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('mis360_seo_geo_action', 'mis360_seo_geo_nonce')) {

        // 1. Sitemap Ping
        if (isset($_POST['mis360_action_ping_all'])) {
            $sitemap_url = home_url('/sitemap.xml');
            $ping_results = mis360_ping_search_engines($sitemap_url);
            
            $log_text = 'Sitemap bildirim sonuçları:<br>';
            foreach ($ping_results as $engine => $status) {
                $log_text .= '<strong>' . esc_html($engine) . ':</strong> ' . esc_html($status) . '<br>';
            }
            $notice = $log_text;
            $notice_type = 'success';
            update_option('mis360_last_indexing_log', [
                'time' => current_time('mysql'),
                'type' => 'Sitemap Ping',
                'detail' => $ping_results
            ]);
        }

        // 2. IndexNow Toplu Gönderim
        elseif (isset($_POST['mis360_action_indexnow_bulk'])) {
            $urls = [
                home_url('/'),
                home_url('/sitemap.xml'),
                home_url('/wp-sitemap.xml'),
                home_url('/llms.txt'),
            ];

            $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 25]);
            if (!is_wp_error($categories) && !empty($categories)) {
                foreach ($categories as $cat) {
                    $cat_link = get_term_link($cat);
                    if (!is_wp_error($cat_link)) {
                        $urls[] = $cat_link;
                    }
                }
            }

            $products = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => 40]);
            foreach ($products as $p) {
                $urls[] = get_permalink($p->ID);
            }

            $res = mis360_submit_to_indexnow($urls);
            $notice = $res['message'];
            $notice_type = $res['success'] ? 'success' : 'error';
            update_option('mis360_last_indexing_log', [
                'time' => current_time('mysql'),
                'type' => 'Toplu IndexNow Gönderimi (' . count($urls) . ' URL)',
                'detail' => $res
            ]);
        }

        // 3. Tekil URL Bildir
        elseif (isset($_POST['mis360_action_custom_url'])) {
            $custom_url = esc_url_raw(trim($_POST['custom_url'] ?? ''));
            if (!empty($custom_url)) {
                $res = mis360_submit_to_indexnow([$custom_url]);
                $notice = '<strong>' . esc_html($custom_url) . '</strong> için IndexNow sonucu: ' . esc_html($res['message']);
                $notice_type = $res['success'] ? 'success' : 'error';
            } else {
                $notice = 'Lütfen geçerli bir URL giriniz.';
                $notice_type = 'error';
            }
        }

        // 4. Sitemap Ekstra URL Ayarlarını Kaydet
        elseif (isset($_POST['mis360_action_save_sitemap_settings'])) {
            update_option('mis360_sitemap_extra_urls', trim($_POST['mis360_sitemap_extra_urls'] ?? ''));
            update_option('mis360_auto_indexnow', isset($_POST['mis360_auto_indexnow']) ? 'yes' : 'no');
            $notice = 'Sitemap ayarları ve ekstra URL listesi kaydedildi.';
            $notice_type = 'success';
            $active_tab = 'sitemaps';
        }

        // 5. GEO & Yerel Konum Ayarlarını Kaydet
        elseif (isset($_POST['mis360_action_save_geo'])) {
            update_option('mis360_geo_region', sanitize_text_field($_POST['mis360_geo_region'] ?? 'TR-38'));
            update_option('mis360_geo_city', sanitize_text_field($_POST['mis360_geo_city'] ?? 'Kayseri'));
            update_option('mis360_geo_district', sanitize_text_field($_POST['mis360_geo_district'] ?? 'Kocasinan'));
            update_option('mis360_geo_area', sanitize_text_field($_POST['mis360_geo_area'] ?? 'Mobilya Kent'));
            update_option('mis360_geo_street', sanitize_text_field($_POST['mis360_geo_street'] ?? ''));
            update_option('mis360_geo_postal', sanitize_text_field($_POST['mis360_geo_postal'] ?? '38070'));
            update_option('mis360_geo_country', sanitize_text_field($_POST['mis360_geo_country'] ?? 'TR'));
            update_option('mis360_geo_lat', sanitize_text_field($_POST['mis360_geo_lat'] ?? '38.7312'));
            update_option('mis360_geo_lng', sanitize_text_field($_POST['mis360_geo_lng'] ?? '35.4787'));
            update_option('mis360_geo_hasmap', esc_url_raw($_POST['mis360_geo_hasmap'] ?? ''));

            $notice = 'GEO ve Yerel Konum SEO ayarları başarıyla kaydedildi. Meta etiketleri ve Schema.org şeması anında güncellendi.';
            $notice_type = 'success';
            $active_tab = 'geo';
        }

        // 6. LLMs.txt & Yapay Zeka SEO Ayarlarını Kaydet
        elseif (isset($_POST['mis360_action_save_llms'])) {
            update_option('mis360_custom_llms_txt', wp_unslash($_POST['mis360_custom_llms_txt'] ?? ''));
            update_option('mis360_custom_llms_full_txt', wp_unslash($_POST['mis360_custom_llms_full_txt'] ?? ''));
            
            update_option('mis360_bot_gpt', isset($_POST['mis360_bot_gpt']) ? 'yes' : 'no');
            update_option('mis360_bot_claude', isset($_POST['mis360_bot_claude']) ? 'yes' : 'no');
            update_option('mis360_bot_perplexity', isset($_POST['mis360_bot_perplexity']) ? 'yes' : 'no');
            update_option('mis360_bot_google_ext', isset($_POST['mis360_bot_google_ext']) ? 'yes' : 'no');
            update_option('mis360_bot_apple', isset($_POST['mis360_bot_apple']) ? 'yes' : 'no');

            $notice = 'LLMs.txt dosyaları ve Yapay Zeka bot izinleri başarıyla güncellendi.';
            $notice_type = 'success';
            $active_tab = 'llms';
        }

        // 7. Robots.txt Kaydet
        elseif (isset($_POST['mis360_action_save_robots'])) {
            if (isset($_POST['mis360_action_reset_robots'])) {
                delete_option('mis360_custom_robots_txt');
                $notice = 'Robots.txt varsayılan e-ticaret kurallarına sıfırlandı.';
            } else {
                update_option('mis360_custom_robots_txt', wp_unslash($_POST['mis360_custom_robots_txt'] ?? ''));
                $notice = 'Özel Robots.txt kuralları başarıyla kaydedildi.';
            }
            $notice_type = 'success';
            $active_tab = 'robots';
        }

        // 8. HTML Doğrulama & Özel Kodları Kaydet
        elseif (isset($_POST['mis360_action_save_html_tags'])) {
            update_option('mis360_google_verification', trim($_POST['mis360_google_verification'] ?? ''));
            update_option('mis360_bing_verification', trim($_POST['mis360_bing_verification'] ?? ''));
            update_option('mis360_yandex_verification', trim($_POST['mis360_yandex_verification'] ?? ''));
            update_option('mis360_facebook_verification', trim($_POST['mis360_facebook_verification'] ?? ''));
            update_option('mis360_pinterest_verification', trim($_POST['mis360_pinterest_verification'] ?? ''));

            if (current_user_can('unfiltered_html')) {
                update_option('mis360_custom_header_html', wp_unslash($_POST['mis360_custom_header_html'] ?? ''));
                update_option('mis360_custom_body_html', wp_unslash($_POST['mis360_custom_body_html'] ?? ''));
                update_option('mis360_custom_footer_html', wp_unslash($_POST['mis360_custom_footer_html'] ?? ''));
            }

            $notice = 'HTML doğrulama etiketleri ve özel kodlar başarıyla kaydedildi.';
            $notice_type = 'success';
            $active_tab = 'html_tags';
        }
    }

    // İSTATİSTİKLER & VERİLER
    $current_key = mis360_get_indexnow_key();
    $key_url     = home_url('/' . $current_key . '.txt');
    $auto_index  = get_option('mis360_auto_indexnow', 'yes');
    $last_log    = get_option('mis360_last_indexing_log');

    // Sayım verileri
    $product_count = class_exists('WooCommerce') ? (int) wp_count_posts('product')->publish : 0;
    $cat_count     = class_exists('WooCommerce') ? (int) wp_count_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]) : 0;
    $page_count    = count(get_pages(['post_status' => 'publish']));
    $total_sitemap_urls = 2 + $product_count + $cat_count + $page_count; // anasayfa + shop + diğerleri

    // Mevcut LLMs.txt içeriği
    $custom_llms = get_option('mis360_custom_llms_txt');
    if (empty($custom_llms) && file_exists(get_template_directory() . '/llms.txt')) {
        $custom_llms = file_get_contents(get_template_directory() . '/llms.txt');
    }

    $custom_llms_full = get_option('mis360_custom_llms_full_txt');
    if (empty($custom_llms_full) && file_exists(get_template_directory() . '/llms-full.txt')) {
        $custom_llms_full = file_get_contents(get_template_directory() . '/llms-full.txt');
    }

    // Mevcut Robots.txt içeriği
    $custom_robots = get_option('mis360_custom_robots_txt');
    if (empty($custom_robots)) {
        // Varsayılan kural çıktısını al
        $custom_robots = mis360_custom_robots_txt('', '1');
    }
    ?>
    <div class="wrap mis360-seo-geo-wrap">
        <style>
            .mis360-seo-geo-wrap {
                max-width: 1200px;
                margin: 20px 20px 40px 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
            .mis360-header-card {
                background: linear-gradient(135deg, #fdfbf7 0%, #fff7ed 100%);
                border: 1px solid #fed7aa;
                border-radius: 14px;
                padding: 24px 28px;
                margin-bottom: 22px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 15px;
            }
            .mis360-header-title h1 {
                margin: 0 !important;
                padding: 0 !important;
                font-size: 24px !important;
                font-weight: 800 !important;
                color: #0f172a !important;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .mis360-header-desc {
                margin: 6px 0 0 !important;
                color: #64748b;
                font-size: 13.5px;
            }
            .mis360-badge {
                background: #ea580c;
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 3px 9px;
                border-radius: 12px;
            }
            
            /* Nav Tabs */
            .mis360-tabs {
                display: flex;
                gap: 4px;
                border-bottom: 2px solid #e2e8f0;
                margin-bottom: 24px;
                flex-wrap: wrap;
            }
            .mis360-tab-link {
                padding: 11px 20px;
                font-size: 14px;
                font-weight: 700;
                color: #64748b;
                text-decoration: none !important;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s ease;
            }
            .mis360-tab-link:hover { color: #0f172a; }
            .mis360-tab-link.active {
                color: #ea580c;
                border-bottom-color: #ea580c;
            }
            
            /* Grid & Postbox */
            .mis360-box {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 22px 26px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03);
                margin-bottom: 22px;
            }
            .mis360-box-title {
                margin: 0 0 16px 0;
                padding-bottom: 12px;
                border-bottom: 1px solid #f1f5f9;
                font-size: 16px;
                font-weight: 700;
                color: #1e293b;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            /* Stat Cards */
            .mis360-stats-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 22px;
            }
            .mis360-stat-pill {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 14px 18px;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .mis360-stat-icon {
                font-size: 24px;
                width: 42px;
                height: 42px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
            }
            .mis360-stat-val { font-size: 18px; font-weight: 800; color: #0f172a; }
            .mis360-stat-lbl { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; }

            /* Forms */
            .mis360-form-row {
                margin-bottom: 18px;
            }
            .mis360-form-row label {
                display: block;
                font-weight: 600;
                font-size: 13px;
                color: #334155;
                margin-bottom: 6px;
            }
            .mis360-input-text, .mis360-textarea {
                width: 100% !important;
                max-width: 100% !important;
                padding: 9px 13px !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 8px !important;
                font-size: 13px !important;
                box-sizing: border-box !important;
            }
            .mis360-textarea-code {
                font-family: Consolas, Monaco, "Courier New", monospace !important;
                font-size: 12.5px !important;
                line-height: 1.5 !important;
                background: #f8fafc !important;
                color: #0f172a !important;
            }
            .mis360-btn-primary {
                background: #ea580c !important;
                border-color: #ea580c !important;
                color: #ffffff !important;
                font-weight: 700 !important;
                padding: 8px 20px !important;
                height: auto !important;
                border-radius: 8px !important;
                box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25) !important;
                cursor: pointer;
            }
            .mis360-btn-primary:hover {
                background: #c2410c !important;
                border-color: #c2410c !important;
            }
            .mis360-btn-secondary {
                background: #f1f5f9 !important;
                border-color: #cbd5e1 !important;
                color: #334155 !important;
                font-weight: 700 !important;
                padding: 8px 16px !important;
                height: auto !important;
                border-radius: 8px !important;
                cursor: pointer;
            }
            .mis360-btn-secondary:hover { background: #e2e8f0 !important; }

            .mis360-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
            @media (max-width: 782px) {
                .mis360-grid-2 { grid-template-columns: 1fr; }
            }
        </style>

        <!-- Üst Başlık Kartı -->
        <div class="mis360-header-card">
            <div class="mis360-header-title">
                <h1>
                    <span>🚀 Emdief Home</span>
                    <span>SEO &amp; GEO Yönetim Merkezi</span>
                    <span class="mis360-badge">v1.9.87</span>
                </h1>
                <p class="mis360-header-desc">
                    Google, Bing, Yandex ve Yapay Zeka motorları (ChatGPT, Perplexity, Gemini, Claude) için arama motoru haritalarını, yerel konum verilerini ve AI direktiflerini canlı yönetin.
                </p>
            </div>
            <div>
                <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" class="button mis360-btn-secondary" style="margin-right:8px;">
                    👁️ Canlı Harita (sitemap.xml)
                </a>
                <a href="<?php echo esc_url(home_url('/llms.txt')); ?>" target="_blank" class="button mis360-btn-secondary">
                    🤖 Yapay Zeka Özeti (llms.txt)
                </a>
            </div>
        </div>

        <!-- Çoklu Sekme Menüsü -->
        <div class="mis360-tabs">
            <a href="?page=mis360-seo-geo&tab=sitemaps" class="mis360-tab-link <?php echo $active_tab === 'sitemaps' ? 'active' : ''; ?>">
                🗺️ Site Haritaları (Sitemaps)
            </a>
            <a href="?page=mis360-seo-geo&tab=geo" class="mis360-tab-link <?php echo $active_tab === 'geo' ? 'active' : ''; ?>">
                📍 Yerel SEO &amp; GEO Konum
            </a>
            <a href="?page=mis360-seo-geo&tab=llms" class="mis360-tab-link <?php echo $active_tab === 'llms' ? 'active' : ''; ?>">
                🤖 GEO &amp; Yapay Zeka (LLMs.txt)
            </a>
            <a href="?page=mis360-seo-geo&tab=robots" class="mis360-tab-link <?php echo $active_tab === 'robots' ? 'active' : ''; ?>">
                🛡️ Robots.txt Düzenleyici
            </a>
            <a href="?page=mis360-seo-geo&tab=html_tags" class="mis360-tab-link <?php echo $active_tab === 'html_tags' ? 'active' : ''; ?>">
                🏷️ Doğrulama Kodları &amp; İndeks
            </a>
        </div>

        <?php if ($notice): ?>
            <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible" style="padding:14px 18px;border-left-width:4px;border-radius:8px;margin-bottom:22px;">
                <p style="margin:0;font-size:13.5px;"><?php echo wp_kses_post($notice); ?></p>
            </div>
        <?php endif; ?>

        <!-- ================= SEKME 1: SITEMAPS ================= -->
        <?php if ($active_tab === 'sitemaps') : ?>
            
            <!-- Canlı İstatistikler -->
            <div class="mis360-stats-row">
                <div class="mis360-stat-pill">
                    <div class="mis360-stat-icon" style="background:#fef3c7;">📦</div>
                    <div>
                        <div class="mis360-stat-val"><?php echo esc_html($product_count); ?></div>
                        <div class="mis360-stat-lbl">Yayındaki Ürün</div>
                    </div>
                </div>
                <div class="mis360-stat-pill">
                    <div class="mis360-stat-icon" style="background:#ffedd5;">🗂️</div>
                    <div>
                        <div class="mis360-stat-val"><?php echo esc_html($cat_count); ?></div>
                        <div class="mis360-stat-lbl">Ürün Kategorisi</div>
                    </div>
                </div>
                <div class="mis360-stat-pill">
                    <div class="mis360-stat-icon" style="background:#e0f2fe;">📄</div>
                    <div>
                        <div class="mis360-stat-val"><?php echo esc_html($page_count); ?></div>
                        <div class="mis360-stat-lbl">Kurumsal Sayfa</div>
                    </div>
                </div>
                <div class="mis360-stat-pill">
                    <div class="mis360-stat-icon" style="background:#dcfce7;">🌐</div>
                    <div>
                        <div class="mis360-stat-val">~<?php echo esc_html($total_sitemap_urls); ?>+</div>
                        <div class="mis360-stat-lbl">Toplam İndeks URL</div>
                    </div>
                </div>
            </div>

            <div class="mis360-grid-2">
                <!-- Sol: Hızlı İndeks Tetikleyicileri & Ekstra URL'ler -->
                <div>
                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>📡 Arama Motoru Harita Bildirimi (Ping)</span>
                        </h2>
                        <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
                            Haritanızı tek tıkla Google, Bing, Yandex ve IndexNow protokolüne bildirerek yeni ürünlerin hızlı taranmasını sağlayın.
                        </p>
                        <form method="post" action="" style="display:flex;flex-wrap:wrap;gap:10px;">
                            <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>
                            <button type="submit" name="mis360_action_ping_all" class="button mis360-btn-primary">
                                📡 Tüm Arama Motorlarına Sitemap Gönder
                            </button>
                            <button type="submit" name="mis360_action_indexnow_bulk" class="button mis360-btn-secondary">
                                ⚡ IndexNow Toplu Gönder
                            </button>
                        </form>
                    </div>

                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>🎯 Tekil URL Anında Bildir</span>
                        </h2>
                        <form method="post" action="" style="display:flex;gap:10px;">
                            <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>
                            <input type="url" name="custom_url" class="mis360-input-text" placeholder="https://emdiefhome.com.tr/urun/ornek-kitaplik" required />
                            <button type="submit" name="mis360_action_custom_url" class="button mis360-btn-primary" style="white-space:nowrap;">
                                URL Bildir
                            </button>
                        </form>
                    </div>

                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>⚙️ Sitemap Ekstra URL &amp; Otomasyon</span>
                        </h2>
                        <form method="post" action="">
                            <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>
                            
                            <div class="mis360-form-row">
                                <label>Haritaya Ekstra Eklenecek Özel URL'ler (Her satıra bir URL):</label>
                                <textarea name="mis360_sitemap_extra_urls" rows="4" class="mis360-textarea mis360-textarea-code" placeholder="https://emdiefhome.com.tr/ozel-kampanya&#10;https://emdiefhome.com.tr/montessori-rehberi"><?php echo esc_textarea(get_option('mis360_sitemap_extra_urls', '')); ?></textarea>
                                <span style="font-size:11.5px;color:#64748b;">WordPress sayfaları ve ürünleri harici eklemek istediğiniz landing page veya özel sayfaları yazabilirsiniz.</span>
                            </div>

                            <div class="mis360-form-row">
                                <label style="display:flex;align-items:center;gap:8px;">
                                    <input type="checkbox" name="mis360_auto_indexnow" value="yes" <?php checked($auto_index, 'yes'); ?> />
                                    <span>Yeni ürün/sayfa yayınlandığında arama motorlarına otomatik anında bildir</span>
                                </label>
                            </div>

                            <button type="submit" name="mis360_action_save_sitemap_settings" class="button mis360-btn-primary">
                                Sitemap Ayarlarını Kaydet
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sağ: Harita Dosyaları Bağlantıları & Son Ping Logu -->
                <div>
                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>🗺️ Canlı Harita Dosyaları</span>
                        </h2>
                        <table class="widefat fixed striped" style="border:none;">
                            <thead>
                                <tr>
                                    <th>Harita / Dosya</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Özel XML Haritası (Tema Motoru)</strong><br>
                                        <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" style="font-size:12px;word-break:break-all;">
                                            <?php echo esc_html(home_url('/sitemap.xml')); ?>
                                        </a>
                                    </td>
                                    <td><span style="color:#16a34a;font-weight:700;">✅ Aktif</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>WordPress Çekirdek Haritası</strong><br>
                                        <a href="<?php echo esc_url(home_url('/wp-sitemap.xml')); ?>" target="_blank" style="font-size:12px;word-break:break-all;">
                                            <?php echo esc_html(home_url('/wp-sitemap.xml')); ?>
                                        </a>
                                    </td>
                                    <td><span style="color:#16a34a;font-weight:700;">✅ Aktif</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Robots.txt</strong><br>
                                        <a href="<?php echo esc_url(home_url('/robots.txt')); ?>" target="_blank" style="font-size:12px;word-break:break-all;">
                                            <?php echo esc_html(home_url('/robots.txt')); ?>
                                        </a>
                                    </td>
                                    <td><span style="color:#16a34a;font-weight:700;">✅ Aktif</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>LLMs.txt (Yapay Zeka)</strong><br>
                                        <a href="<?php echo esc_url(home_url('/llms.txt')); ?>" target="_blank" style="font-size:12px;word-break:break-all;">
                                            <?php echo esc_html(home_url('/llms.txt')); ?>
                                        </a>
                                    </td>
                                    <td><span style="color:#16a34a;font-weight:700;">✅ Aktif</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>IndexNow Kimlik Dosyası</strong><br>
                                        <a href="<?php echo esc_url($key_url); ?>" target="_blank" style="font-size:12px;word-break:break-all;">
                                            <?php echo esc_html($key_url); ?>
                                        </a>
                                    </td>
                                    <td><span style="color:#16a34a;font-weight:700;">✅ Aktif</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($last_log) : ?>
                        <div class="mis360-box">
                            <h2 class="mis360-box-title">
                                <span>🕒 Son Arama Motoru Bildirimi</span>
                            </h2>
                            <p style="font-size:12.5px;color:#64748b;margin:0 0 8px 0;">
                                <strong>Tarih:</strong> <?php echo esc_html($last_log['time'] ?? '-'); ?> | 
                                <strong>Tür:</strong> <?php echo esc_html($last_log['type'] ?? '-'); ?>
                            </p>
                            <div style="background:#f8fafc;padding:12px;border-radius:8px;font-size:11.5px;max-height:180px;overflow-y:auto;border:1px solid #e2e8f0;">
                                <pre style="margin:0;font-family:monospace;"><?php echo esc_html(wp_json_encode($last_log['detail'] ?? '', JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <!-- ================= SEKME 2: GEO & YEREL KONUM SEO ================= -->
        <?php elseif ($active_tab === 'geo') : 
            $geo_region    = get_option('mis360_geo_region', 'TR-38');
            $geo_city      = get_option('mis360_geo_city', 'Kayseri');
            $geo_district  = get_option('mis360_geo_district', 'Kocasinan');
            $geo_area      = get_option('mis360_geo_area', 'Mobilya Kent');
            $geo_street    = get_option('mis360_geo_street', 'Mobilya Kent Kırmızı Bloklar, Camikebir Mahallesi, 5066. Sk No:1 D:K');
            $geo_postal    = get_option('mis360_geo_postal', '38070');
            $geo_country   = get_option('mis360_geo_country', 'TR');
            $geo_lat       = get_option('mis360_geo_lat', '38.7312');
            $geo_lng       = get_option('mis360_geo_lng', '35.4787');
            $geo_hasmap    = get_option('mis360_geo_hasmap', 'https://www.google.com/maps/place//data=!4m2!3m1!1s0x152b057da63cc6c7:0x45e8ad2179bc179c?sa=X&ved=1t:8290&ictx=111');
        ?>
            <form method="post" action="">
                <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>

                <div class="mis360-grid-2">
                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>📍 Yerel İşletme &amp; GEO Konum Bilgileri</span>
                        </h2>
                        <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
                            Bu bilgiler Google Haritalar, yerel arama sonuçları ve yapay zeka sorgularında Emdief Home'un Kayseri/Kocasinan merkezli ahşap çocuk mobilyası üreticisi olarak ön plana çıkmasını sağlar.
                        </p>

                        <div class="mis360-grid-2">
                            <div class="mis360-form-row">
                                <label>Şehir (City):</label>
                                <input type="text" name="mis360_geo_city" value="<?php echo esc_attr($geo_city); ?>" class="mis360-input-text" required />
                            </div>
                            <div class="mis360-form-row">
                                <label>İlçe (District):</label>
                                <input type="text" name="mis360_geo_district" value="<?php echo esc_attr($geo_district); ?>" class="mis360-input-text" required />
                            </div>
                        </div>

                        <div class="mis360-grid-2">
                            <div class="mis360-form-row">
                                <label>Sanayi / Bölge Adı:</label>
                                <input type="text" name="mis360_geo_area" value="<?php echo esc_attr($geo_area); ?>" class="mis360-input-text" />
                            </div>
                            <div class="mis360-form-row">
                                <label>Bölge Kodu (ISO 3166-2):</label>
                                <input type="text" name="mis360_geo_region" value="<?php echo esc_attr($geo_region); ?>" class="mis360-input-text" placeholder="TR-38" required />
                            </div>
                        </div>

                        <div class="mis360-form-row">
                            <label>Açık Fabrika / Atölye Adresi:</label>
                            <input type="text" name="mis360_geo_street" value="<?php echo esc_attr($geo_street); ?>" class="mis360-input-text" />
                        </div>

                        <div class="mis360-grid-2">
                            <div class="mis360-form-row">
                                <label>Posta Kodu:</label>
                                <input type="text" name="mis360_geo_postal" value="<?php echo esc_attr($geo_postal); ?>" class="mis360-input-text" />
                            </div>
                            <div class="mis360-form-row">
                                <label>Ülke Kodu:</label>
                                <input type="text" name="mis360_geo_country" value="<?php echo esc_attr($geo_country); ?>" class="mis360-input-text" placeholder="TR" />
                            </div>
                        </div>

                        <div class="mis360-grid-2">
                            <div class="mis360-form-row">
                                <label>Coğrafi Enlem (Latitude):</label>
                                <input type="text" name="mis360_geo_lat" value="<?php echo esc_attr($geo_lat); ?>" class="mis360-input-text" placeholder="38.7312" required />
                            </div>
                            <div class="mis360-form-row">
                                <label>Coğrafi Boylam (Longitude):</label>
                                <input type="text" name="mis360_geo_lng" value="<?php echo esc_attr($geo_lng); ?>" class="mis360-input-text" placeholder="35.4787" required />
                            </div>
                        </div>

                        <div class="mis360-form-row">
                            <label>Google Haritalar (Maps) Paylaşım Linki:</label>
                            <input type="url" name="mis360_geo_hasmap" value="<?php echo esc_attr($geo_hasmap); ?>" class="mis360-input-text" />
                        </div>

                        <button type="submit" name="mis360_action_save_geo" class="button mis360-btn-primary">
                            GEO &amp; Konum Ayarlarını Kaydet
                        </button>
                    </div>

                    <!-- Canlı GEO Meta & Schema Önizleme -->
                    <div>
                        <div class="mis360-box">
                            <h2 class="mis360-box-title">
                                <span>🌐 Sitede Üretilen Canlı GEO Meta Etiketleri</span>
                            </h2>
                            <p style="font-size:12.5px;color:#64748b;">Arama motorları sayfa kaynak kodunda bu etiketleri görür:</p>
                            <div style="background:#f8fafc;padding:14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;font-family:monospace;line-height:1.6;">
                                &lt;meta name="geo.region" content="<?php echo esc_attr($geo_region); ?>"&gt;<br>
                                &lt;meta name="geo.placename" content="<?php echo esc_attr($geo_city . ', ' . $geo_district . ', ' . $geo_area); ?>"&gt;<br>
                                &lt;meta name="geo.position" content="<?php echo esc_attr($geo_lat . ';' . $geo_lng); ?>"&gt;<br>
                                &lt;meta name="ICBM" content="<?php echo esc_attr($geo_lat . ', ' . $geo_lng); ?>"&gt;<br>
                                &lt;meta name="geo.country" content="<?php echo esc_attr($geo_country); ?>"&gt;<br>
                                &lt;meta name="DC.spatial" content="<?php echo esc_attr($geo_district . ', ' . $geo_city . ', Türkiye'); ?>"&gt;
                            </div>
                        </div>

                        <div class="mis360-box">
                            <h2 class="mis360-box-title">
                                <span>🏢 Schema.org FurnitureStore (JSON-LD)</span>
                            </h2>
                            <p style="font-size:12.5px;color:#64748b;">Google 2026 Merchant Center ve Haritalar entegrasyonu:</p>
                            <div style="background:#f8fafc;padding:14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;font-family:monospace;line-height:1.6;">
                                "@type": "FurnitureStore",<br>
                                "name": "Emdief Home",<br>
                                "address": {<br>
                                &nbsp;&nbsp;"streetAddress": "<?php echo esc_attr($geo_street); ?>",<br>
                                &nbsp;&nbsp;"addressLocality": "<?php echo esc_attr($geo_district); ?>",<br>
                                &nbsp;&nbsp;"addressRegion": "<?php echo esc_attr($geo_city); ?>",<br>
                                &nbsp;&nbsp;"addressCountry": "<?php echo esc_attr($geo_country); ?>"<br>
                                },<br>
                                "geo": {<br>
                                &nbsp;&nbsp;"latitude": "<?php echo esc_attr($geo_lat); ?>",<br>
                                &nbsp;&nbsp;"longitude": "<?php echo esc_attr($geo_lng); ?>"<br>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        <!-- ================= SEKME 3: LLMS.TXT (YAPAY ZEKA SEO) ================= -->
        <?php elseif ($active_tab === 'llms') : 
            $allow_gpt     = get_option('mis360_bot_gpt', 'yes') === 'yes';
            $allow_claude  = get_option('mis360_bot_claude', 'yes') === 'yes';
            $allow_perp    = get_option('mis360_bot_perplexity', 'yes') === 'yes';
            $allow_g_ext   = get_option('mis360_bot_google_ext', 'yes') === 'yes';
            $allow_apple   = get_option('mis360_bot_apple', 'yes') === 'yes';
        ?>
            <form method="post" action="">
                <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>

                <div class="mis360-box">
                    <h2 class="mis360-box-title">
                        <span>🤖 Yapay Zeka Arama Bot İzinleri (Generative Engine Optimization)</span>
                    </h2>
                    <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
                        Kullanıcılar ChatGPT, Claude veya Perplexity gibi yapay zeka asistanlarına *"En kaliteli Montessori çocuk kitaplığı markası hangisi?"* diye sorduğunda sitenizin taranıp önerilmesi için aşağıdaki bot izinlerini açık tutun:
                    </p>
                    <div style="display:flex;flex-wrap:wrap;gap:20px;">
                        <label style="font-weight:600;"><input type="checkbox" name="mis360_bot_gpt" value="yes" <?php checked($allow_gpt); ?> /> GPTBot &amp; ChatGPT</label>
                        <label style="font-weight:600;"><input type="checkbox" name="mis360_bot_claude" value="yes" <?php checked($allow_claude); ?> /> ClaudeBot (Anthropic)</label>
                        <label style="font-weight:600;"><input type="checkbox" name="mis360_bot_perplexity" value="yes" <?php checked($allow_perp); ?> /> PerplexityBot</label>
                        <label style="font-weight:600;"><input type="checkbox" name="mis360_bot_google_ext" value="yes" <?php checked($allow_g_ext); ?> /> Google-Extended (Gemini)</label>
                        <label style="font-weight:600;"><input type="checkbox" name="mis360_bot_apple" value="yes" <?php checked($allow_apple); ?> /> Applebot (Siri &amp; Apple Intelligence)</label>
                    </div>
                </div>

                <div class="mis360-grid-2">
                    <!-- LLMs.txt Editörü -->
                    <div class="mis360-box">
                        <div class="mis360-box-title">
                            <span>📄 llms.txt (Özet Marka &amp; Koleksiyon Dosyası)</span>
                            <a href="<?php echo esc_url(home_url('/llms.txt')); ?>" target="_blank" style="font-size:12px;font-weight:normal;text-decoration:underline;">Canlı Aç ↗</a>
                        </div>
                        <p style="font-size:12.5px;color:#64748b;margin-bottom:10px;">
                            Yapay zeka modellerinin ilk okuduğu özet tanıtım metni:
                        </p>
                        <textarea name="mis360_custom_llms_txt" rows="18" class="mis360-textarea mis360-textarea-code"><?php echo esc_textarea($custom_llms); ?></textarea>
                    </div>

                    <!-- LLMs-Full.txt Editörü -->
                    <div class="mis360-box">
                        <div class="mis360-box-title">
                            <span>📚 llms-full.txt (Genişletilmiş Katalog &amp; Detaylar)</span>
                            <a href="<?php echo esc_url(home_url('/llms-full.txt')); ?>" target="_blank" style="font-size:12px;font-weight:normal;text-decoration:underline;">Canlı Aç ↗</a>
                        </div>
                        <p style="font-size:12.5px;color:#64748b;margin-bottom:10px;">
                            Tüm ürün serilerini, ahşap malzeme detaylarını ve montaj bilgilerini içeren derin dosya:
                        </p>
                        <textarea name="mis360_custom_llms_full_txt" rows="18" class="mis360-textarea mis360-textarea-code"><?php echo esc_textarea($custom_llms_full); ?></textarea>
                    </div>
                </div>

                <div style="margin-top:10px;">
                    <button type="submit" name="mis360_action_save_llms" class="button mis360-btn-primary">
                        Yapay Zeka (LLMs.txt) Dosyalarını Kaydet
                    </button>
                </div>
            </form>

        <!-- ================= SEKME 4: ROBOTS.TXT ================= -->
        <?php elseif ($active_tab === 'robots') : ?>
            <form method="post" action="">
                <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>

                <div class="mis360-box">
                    <div class="mis360-box-title">
                        <span>🛡️ Canlı Robots.txt Düzenleyicisi</span>
                        <a href="<?php echo esc_url(home_url('/robots.txt')); ?>" target="_blank" style="font-size:12px;font-weight:normal;text-decoration:underline;">Canlı robots.txt Görüntüle ↗</a>
                    </div>
                    <p style="font-size:13px;color:#64748b;margin-bottom:14px;">
                        Arama motoru botlarının hangi sayfaları tarayıp hangilerini hariç tutacağını belirler. Sepet, ödeme ve hesap sayfaları gereksiz tarama bütçesi harcamamak için varsayılan olarak engellenmiştir.
                    </p>
                    
                    <textarea name="mis360_custom_robots_txt" rows="18" class="mis360-textarea mis360-textarea-code"><?php echo esc_textarea($custom_robots); ?></textarea>

                    <div style="display:flex;gap:12px;margin-top:16px;">
                        <button type="submit" name="mis360_action_save_robots" class="button mis360-btn-primary">
                            Robots.txt Kurallarını Kaydet
                        </button>
                        <button type="submit" name="mis360_action_reset_robots" class="button mis360-btn-secondary" onclick="return confirm('Varsayılan e-ticaret kurallarına dönmek istediğinize emin misiniz?');">
                            Varsayılan Kurallara Sıfırla
                        </button>
                    </div>
                </div>
            </form>

        <!-- ================= SEKME 5: HTML ETİKETLERİ & DOĞRULAMA ================= -->
        <?php elseif ($active_tab === 'html_tags') : ?>
            <form method="post" action="">
                <?php wp_nonce_field('mis360_seo_geo_action', 'mis360_seo_geo_nonce'); ?>

                <div class="mis360-grid-2">
                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>🔍 Arama Motoru Doğrulama Kodları</span>
                        </h2>
                        
                        <div class="mis360-form-row">
                            <label>🔴 Google Search Console Doğrulama Kodu:</label>
                            <input type="text" name="mis360_google_verification" value="<?php echo esc_attr(get_option('mis360_google_verification', '')); ?>" class="mis360-input-text" placeholder="google-site-verification kodu veya tam meta etiketi" />
                        </div>

                        <div class="mis360-form-row">
                            <label>🔵 Bing &amp; Yahoo Webmaster Doğrulama:</label>
                            <input type="text" name="mis360_bing_verification" value="<?php echo esc_attr(get_option('mis360_bing_verification', '')); ?>" class="mis360-input-text" placeholder="msvalidate.01 kodu" />
                        </div>

                        <div class="mis360-form-row">
                            <label>🟡 Yandex Webmaster Doğrulama:</label>
                            <input type="text" name="mis360_yandex_verification" value="<?php echo esc_attr(get_option('mis360_yandex_verification', '')); ?>" class="mis360-input-text" placeholder="yandex-verification kodu" />
                        </div>

                        <div class="mis360-form-row">
                            <label>🔷 Meta (Facebook) Alan Adı Doğrulama:</label>
                            <input type="text" name="mis360_facebook_verification" value="<?php echo esc_attr(get_option('mis360_facebook_verification', '')); ?>" class="mis360-input-text" placeholder="facebook-domain-verification kodu" />
                        </div>

                        <div class="mis360-form-row">
                            <label>📌 Pinterest Doğrulama:</label>
                            <input type="text" name="mis360_pinterest_verification" value="<?php echo esc_attr(get_option('mis360_pinterest_verification', '')); ?>" class="mis360-input-text" placeholder="p:domain_verify kodu" />
                        </div>

                        <button type="submit" name="mis360_action_save_html_tags" class="button mis360-btn-primary">
                            Doğrulama Kodlarını Kaydet
                        </button>
                    </div>

                    <div class="mis360-box">
                        <h2 class="mis360-box-title">
                            <span>💻 Özel HTML / JavaScript Kodları</span>
                        </h2>

                        <div class="mis360-form-row">
                            <label>Header Kodları (&lt;head&gt; içine enjekte edilir):</label>
                            <textarea name="mis360_custom_header_html" rows="4" class="mis360-textarea mis360-textarea-code" placeholder="<!-- Google Analytics / Meta Pixel vb. -->"><?php echo esc_textarea(get_option('mis360_custom_header_html', '')); ?></textarea>
                        </div>

                        <div class="mis360-form-row">
                            <label>Body Kodları (&lt;body&gt; açılışından hemen sonra):</label>
                            <textarea name="mis360_custom_body_html" rows="4" class="mis360-textarea mis360-textarea-code" placeholder="<!-- GTM NoScript vb. -->"><?php echo esc_textarea(get_option('mis360_custom_body_html', '')); ?></textarea>
                        </div>

                        <div class="mis360-form-row">
                            <label>Footer Kodları (&lt;/body&gt; kapanışından hemen önce):</label>
                            <textarea name="mis360_custom_footer_html" rows="4" class="mis360-textarea mis360-textarea-code" placeholder="<!-- Canlı Destek Widget vb. -->"><?php echo esc_textarea(get_option('mis360_custom_footer_html', '')); ?></textarea>
                        </div>

                        <button type="submit" name="mis360_action_save_html_tags" class="button mis360-btn-primary">
                            Özel Kodları Kaydet
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

    </div>
    <?php
}
