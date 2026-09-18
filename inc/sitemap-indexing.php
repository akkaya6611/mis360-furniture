<?php
/**
 * Mis360 Mobilya - Sitemap & Instant Indexing (Hızlı İndeksleme) Modülü
 *
 * Google, Bing, Yandex ve IndexNow protokolü üzerinden otomatik ve manuel indeksleme sağlar.
 * 
 * @package Mis360-Mobilya
 * @author Serkan AKKAYA
 * @since 1.9.62
 */

if (!defined('ABSPATH')) {
    exit;
}

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
function mis360_submit_to_indexnow($urls = []) {
    if (empty($urls)) {
        return ['success' => false, 'message' => 'Gönderilecek URL bulunamadı.'];
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

    $response = wp_remote_post('https://api.indexnow.org/indexnow', [
        'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
        'body'        => wp_json_encode($body),
        'timeout'     => 15,
        'httpversion' => '1.1',
    ]);

    if (is_wp_error($response)) {
        return [
            'success' => false,
            'message' => 'IndexNow API Bağlantı Hatası: ' . $response->get_error_message()
        ];
    }

    $code = wp_remote_retrieve_response_code($response);
    
    // IndexNow Yanıt Kodları: 200 (OK), 202 (Accepted)
    if ($code === 200 || $code === 202) {
        return [
            'success' => true,
            'code'    => $code,
            'message' => count($urls) . ' adet URL başarıyla IndexNow ağına (Bing, Yandex, Seznam, Naver) iletildi.'
        ];
    } else {
        $msg = wp_remote_retrieve_body($response);
        return [
            'success' => false,
            'code'    => $code,
            'message' => 'IndexNow HTTP ' . $code . ': ' . ($msg ? esc_html($msg) : 'Bilinmeyen hata')
        ];
    }
}

// 3. Arama Motoru Sitemap Ping Gönderimleri
function mis360_ping_search_engines($sitemap_url = '') {
    if (empty($sitemap_url)) {
        $sitemap_url = home_url('/sitemap.xml');
    }

    $results = [];

    // Bing Ping
    $bing_url = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url);
    $bing_res = wp_remote_get($bing_url, ['timeout' => 10]);
    $results['Bing'] = is_wp_error($bing_res) ? $bing_res->get_error_message() : ('HTTP ' . wp_remote_retrieve_response_code($bing_res));

    // Yandex Ping
    $yandex_url = 'https://webmaster.yandex.com/ping?sitemap=' . urlencode($sitemap_url);
    $yandex_res = wp_remote_get($yandex_url, ['timeout' => 10]);
    $results['Yandex'] = is_wp_error($yandex_res) ? $yandex_res->get_error_message() : ('HTTP ' . wp_remote_retrieve_response_code($yandex_res));

    // Google Ping
    $google_url = 'https://www.google.com/ping?sitemap=' . urlencode($sitemap_url);
    $google_res = wp_remote_get($google_url, ['timeout' => 10]);
    $results['Google'] = is_wp_error($google_res) ? $google_res->get_error_message() : ('HTTP ' . wp_remote_retrieve_response_code($google_res));

    // IndexNow ile Sitemap ve Ana Sayfa
    $indexnow_res = mis360_submit_to_indexnow([$sitemap_url, home_url('/')]);
    $results['IndexNow'] = $indexnow_res['success'] ? 'Başarılı (' . ($indexnow_res['code'] ?? 200) . ')' : ('Hata: ' . $indexnow_res['message']);

    return $results;
}

// 4. Yeni İçerik veya Ürün Yayınlandığında Otomatik IndexNow Gönderimi
add_action('transition_post_status', 'mis360_auto_indexnow_on_publish', 10, 3);
function mis360_auto_indexnow_on_publish($new_status, $old_status, $post) {
    if ($new_status !== 'publish' || !is_a($post, 'WP_Post')) {
        return;
    }

    // Yalnızca ürün, sayfa ve yazılarda çalıştır
    $allowed_types = ['product', 'page', 'post'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        return;
    }

    // Otomatik indeksleme açık mı kontrol et
    if (get_option('mis360_auto_indexnow', 'yes') !== 'yes') {
        return;
    }

    $permalink = get_permalink($post->ID);
    if ($permalink) {
        mis360_submit_to_indexnow([$permalink, home_url('/sitemap.xml')]);
    }
}

// 5. Doğrulama Meta Etiketleri ve Özel HTML Kodlarının Ön Yüze Enjeksiyonu
function mis360_render_verification_meta($name, $val) {
    $val = trim($val ?? '');
    if (empty($val)) {
        return;
    }
    // Kullanıcı tam <meta ...> etiketi girdiyse olduğu gibi bas, sadece içerik girdiyse etiketi oluştur
    if (stripos($val, '<meta') !== false) {
        echo $val . "\n";
    } else {
        echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($val) . '" />' . "\n";
    }
}

// <head> Çıktısı (Meta etiketleri & Özel Header HTML / Scriptler)
add_action('wp_head', 'mis360_inject_head_tags_and_scripts', 1);
function mis360_inject_head_tags_and_scripts() {
    echo "\n<!-- Emdief Home SEO & Verification Tags -->\n";
    mis360_render_verification_meta('google-site-verification', get_option('mis360_google_verification', ''));
    mis360_render_verification_meta('msvalidate.01', get_option('mis360_bing_verification', ''));
    mis360_render_verification_meta('yandex-verification', get_option('mis360_yandex_verification', ''));
    mis360_render_verification_meta('facebook-domain-verification', get_option('mis360_facebook_verification', ''));
    mis360_render_verification_meta('p:domain_verify', get_option('mis360_pinterest_verification', ''));
    echo "<!-- /Emdief Home SEO & Verification Tags -->\n";

    $custom_head = get_option('mis360_custom_header_html', '');
    if (!empty($custom_head)) {
        echo "\n<!-- Emdief Home Custom Header Scripts -->\n";
        echo $custom_head . "\n";
        echo "<!-- /Emdief Home Custom Header Scripts -->\n";
    }
}

// <body> Açılış Çıktısı (Örn: GTM <noscript>)
add_action('wp_body_open', 'mis360_inject_body_scripts', 1);
function mis360_inject_body_scripts() {
    $custom_body = get_option('mis360_custom_body_html', '');
    if (!empty($custom_body)) {
        echo "\n<!-- Emdief Home Custom Body Start Scripts -->\n";
        echo $custom_body . "\n";
        echo "<!-- /Emdief Home Custom Body Start Scripts -->\n";
    }
}

// </body> Kapanış Öncesi Çıktısı (Örn: Canlı Destek, Pixel, İstatistik)
add_action('wp_footer', 'mis360_inject_footer_scripts', 99);
function mis360_inject_footer_scripts() {
    $custom_footer = get_option('mis360_custom_footer_html', '');
    if (!empty($custom_footer)) {
        echo "\n<!-- Emdief Home Custom Footer Scripts -->\n";
        echo $custom_footer . "\n";
        echo "<!-- /Emdief Home Custom Footer Scripts -->\n";
    }
}

// 6. WP Admin Menüsü & Çok Sekmeli Arayüz
add_action('admin_menu', 'mis360_indexing_admin_menu');
function mis360_indexing_admin_menu() {
    add_submenu_page(
        'tools.php',
        'SEO, İndeksleme & HTML Etiketleri',
        'SEO & İndeksleme',
        'manage_options',
        'mis360-indexing',
        'mis360_indexing_admin_page'
    );
}

function mis360_indexing_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Bu sayfaya erişim yetkiniz bulunmuyor.', 'mis360-mobilya'));
    }

    $active_tab = isset($_GET['tab']) && $_GET['tab'] === 'html_tags' ? 'html_tags' : 'indexing';
    $notice = null;
    $notice_type = 'info';

    // İşlem kontrolü (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('mis360_indexing_action', 'mis360_indexing_nonce')) {
        
        // 1. Tüm Arama Motorlarına Sitemap Ping
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

        // 2. IndexNow ile Tüm Siteyi (Sitemap + Ana Sayfa + Kategoriler + Son Ürünler) Gönder
        elseif (isset($_POST['mis360_action_indexnow_bulk'])) {
            $urls = [
                home_url('/'),
                home_url('/sitemap.xml'),
                home_url('/wp-sitemap.xml'),
                home_url('/llms.txt'),
            ];

            // Ürün kategorilerini ekle
            $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 25]);
            if (!is_wp_error($categories) && !empty($categories)) {
                foreach ($categories as $cat) {
                    $cat_link = get_term_link($cat);
                    if (!is_wp_error($cat_link)) {
                        $urls[] = $cat_link;
                    }
                }
            }

            // Son 30 ürünü ekle
            $products = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => 30]);
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

        // 3. Tekil / Özel URL İndekslet
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

        // 4. IndexNow Anahtarını Yenile
        elseif (isset($_POST['mis360_action_regen_key'])) {
            $new_key = wp_generate_password(32, false, false);
            update_option('mis360_indexnow_key', $new_key);
            $notice = 'IndexNow anahtarı başarıyla yenilendi: <code>' . esc_html($new_key) . '</code>';
            $notice_type = 'success';
        }

        // 5. Otomatik İndeksleme Ayarını Güncelle
        elseif (isset($_POST['mis360_action_save_settings'])) {
            $auto = isset($_POST['mis360_auto_indexnow']) ? 'yes' : 'no';
            update_option('mis360_auto_indexnow', $auto);
            $notice = 'İndeksleme ayarları başarıyla kaydedildi.';
            $notice_type = 'success';
        }

        // 6. HTML Doğrulama & Özel Kodları Kaydet
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

    $current_key = mis360_get_indexnow_key();
    $key_url = home_url('/' . $current_key . '.txt');
    $auto_index = get_option('mis360_auto_indexnow', 'yes');
    $last_log = get_option('mis360_last_indexing_log');

    $seo_files = [
        ['name' => 'Özel XML Haritası (Tema)', 'url' => home_url('/sitemap.xml'), 'desc' => 'Tüm ürün, kategori ve sayfaların otomatik güncellenen haritası.'],
        ['name' => 'WordPress Varsayılan Haritası', 'url' => home_url('/wp-sitemap.xml'), 'desc' => 'WordPress çekirdek sitemap haritası.'],
        ['name' => 'Robots.txt', 'url' => home_url('/robots.txt'), 'desc' => 'Arama botları için tarama direktifleri.'],
        ['name' => 'LLMs.txt (Yapay Zeka & GEO)', 'url' => home_url('/llms.txt'), 'desc' => 'ChatGPT, Perplexity, Gemini için optimize edilmiş site özeti.'],
        ['name' => 'IndexNow Doğrulama Dosyası', 'url' => $key_url, 'desc' => 'Arama motorlarının API kimliğini doğrulamak için kullanılan dosya.'],
    ];
    ?>
    <div class="wrap mis360-indexing-wrap">
        <style>
        .mis360-indexing-wrap {
            max-width: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }
        .mis360-indexing-grid-main {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .mis360-indexing-grid-tags {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 20px;
            margin-top: 10px;
        }
        @media (max-width: 1024px) {
            .mis360-indexing-grid-main,
            .mis360-indexing-grid-tags {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
        .mis360-indexing-wrap input[type="text"],
        .mis360-indexing-wrap input[type="url"],
        .mis360-indexing-wrap textarea {
            max-width: 100% !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .mis360-indexing-wrap table {
            width: 100%;
            table-layout: auto;
            word-break: break-word;
        }
        .mis360-indexing-wrap .postbox {
            box-sizing: border-box;
            overflow: hidden;
        }
        </style>

        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:15px;">
            <span class="dashicons dashicons-search" style="font-size:32px;width:32px;height:32px;color:#7a00df;"></span>
            Emdief Home - SEO, İndeksleme & HTML Doğrulama Paneli
        </h1>

        <!-- Çoklu Sekme Başlıkları -->
        <h2 class="nav-tab-wrapper" style="margin-bottom:20px;">
            <a href="?page=mis360-indexing&tab=indexing" class="nav-tab <?php echo $active_tab === 'indexing' ? 'nav-tab-active' : ''; ?>" style="font-weight:600;">
                🚀 Sitemap & Hızlı İndeksleme
            </a>
            <a href="?page=mis360-indexing&tab=html_tags" class="nav-tab <?php echo $active_tab === 'html_tags' ? 'nav-tab-active' : ''; ?>" style="font-weight:600;">
                🏷️ HTML Etiketleri & Doğrulama Kodları
            </a>
        </h2>

        <?php if ($notice): ?>
            <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible" style="padding:12px 15px;border-left-width:4px;">
                <p><?php echo wp_kses_post($notice); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($active_tab === 'indexing'): ?>
            <p style="color:#646970;font-size:14px;margin-bottom:20px;">
                Sitenizdeki sayfaları, WooCommerce ürünlerini ve XML Haritalarını Google, Bing, Yandex ve IndexNow protokolü (Bing, Yandex, Seznam, Naver) aracılığıyla anında arama motorlarına bildirin.
            </p>

        <div class="mis360-indexing-grid-main">
            <!-- Sol Kolon: İşlemler & Haritalar -->
            <div>
                <!-- Hızlı İşlemler Kartı -->
                <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:20px;">
                    <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;">🚀 Anında İndeksleme Tetikleyicileri</h2>
                    <form method="post" action="" style="margin-top:15px;display:flex;flex-wrap:wrap;gap:12px;">
                        <?php wp_nonce_field('mis360_indexing_action', 'mis360_indexing_nonce'); ?>
                        
                        <button type="submit" name="mis360_action_ping_all" class="button button-primary" style="background:#7a00df;border-color:#6500b8;padding:6px 16px;height:auto;font-weight:600;">
                            📡 Tüm Motorlara Sitemap Gönder (Google, Bing, Yandex, IndexNow)
                        </button>
                        
                        <button type="submit" name="mis360_action_indexnow_bulk" class="button button-secondary" style="padding:6px 16px;height:auto;font-weight:600;border-color:#7a00df;color:#7a00df;">
                            ⚡ IndexNow ile Tüm Ürünleri & Sayfaları Bildir
                        </button>
                    </form>
                </div>

                <!-- Özel URL Gönderim Kartı -->
                <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:20px;">
                    <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;">🎯 Tekil URL Hızlı İndekslet</h2>
                    <p style="font-size:13px;color:#555;">Yeni eklediğiniz veya güncellediğiniz bir ürün ya da sayfanın URL adresini girip tek tıkla IndexNow ağına bildirin:</p>
                    <form method="post" action="" style="display:flex;gap:10px;margin-top:10px;">
                        <?php wp_nonce_field('mis360_indexing_action', 'mis360_indexing_nonce'); ?>
                        <input type="url" name="custom_url" placeholder="https://emdiefhome.com.tr/urun/ornek-montessori-yatak" style="flex:1;padding:8px 12px;" required />
                        <button type="submit" name="mis360_action_custom_url" class="button button-primary" style="background:#2271b1;padding:6px 18px;height:auto;">
                            URL Bildir
                        </button>
                    </form>
                </div>

                <!-- Harita ve SEO Dosyaları Durumu -->
                <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;">🗺️ Aktif Harita ve SEO Dosyaları</h2>
                    <table class="widefat fixed striped" style="margin-top:12px;border:none;">
                        <thead>
                            <tr>
                                <th style="width:28%;font-weight:600;">Dosya / Kaynak</th>
                                <th style="width:47%;font-weight:600;">Bağlantı (URL)</th>
                                <th style="width:25%;font-weight:600;">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seo_files as $file): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($file['name']); ?></strong><br>
                                        <small style="color:#777;"><?php echo esc_html($file['desc']); ?></small>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url($file['url']); ?>" target="_blank" rel="noopener noreferrer" style="word-break:break-all;text-decoration:underline;">
                                            <?php echo esc_html($file['url']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="dashicons dashicons-yes" style="color:#46b450;vertical-align:middle;"></span>
                                        <span style="color:#2e7d32;font-weight:600;">Aktif & Erişilebilir</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sağ Kolon: Ayarlar & IndexNow Anahtarı -->
            <div>
                <!-- Otomasyon Ayarları -->
                <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:20px;">
                    <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;">⚙️ Otomatik İndeksleme</h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('mis360_indexing_action', 'mis360_indexing_nonce'); ?>
                        <p style="font-size:13px;color:#555;">
                            Yeni ürün veya sayfa yayınlandığında ya da güncellendiğinde IndexNow ile otomatik olarak bildir:
                        </p>
                        <label style="display:flex;align-items:center;gap:8px;font-weight:600;margin:15px 0;">
                            <input type="checkbox" name="mis360_auto_indexnow" value="yes" <?php checked($auto_index, 'yes'); ?> />
                            Otomatik Bildirimi Açık Tut (Önerilen)
                        </label>
                        <button type="submit" name="mis360_action_save_settings" class="button button-secondary">
                            Ayar Kaydet
                        </button>
                    </form>
                </div>

                <!-- IndexNow Anahtar Bilgisi -->
                <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:20px;">
                    <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;">🔑 IndexNow Kimlik Anahtarı</h2>
                    <p style="font-size:13px;color:#555;">Arama motorları sitenizin doğruluğunu bu anahtar ile teyit eder:</p>
                    <div style="background:#f0f0f1;padding:10px;border-radius:4px;font-family:monospace;font-size:12px;word-break:break-all;margin-bottom:12px;">
                        <?php echo esc_html($current_key); ?>
                    </div>
                    <form method="post" action="" onsubmit="return confirm('Yeni bir IndexNow anahtarı üretmek istediğinize emin misiniz?');">
                        <?php wp_nonce_field('mis360_indexing_action', 'mis360_indexing_nonce'); ?>
                        <button type="submit" name="mis360_action_regen_key" class="button button-link-delete" style="color:#a00;text-decoration:underline;">
                            Yeni Anahtar Üret
                        </button>
                    </form>
                </div>

                <!-- Son İşlem Geçmişi -->
                <?php if ($last_log): ?>
                <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;">🕒 Son Bildirim Geçmişi</h2>
                    <p style="font-size:12px;margin-bottom:6px;"><strong>Tarih:</strong> <?php echo esc_html($last_log['time'] ?? '-'); ?></p>
                    <p style="font-size:12px;margin-bottom:6px;"><strong>İşlem:</strong> <?php echo esc_html($last_log['type'] ?? '-'); ?></p>
                    <div style="background:#f6f7f7;padding:8px;border-radius:4px;font-size:11px;max-height:160px;overflow-y:auto;">
                        <pre style="margin:0;"><?php echo esc_html(wp_json_encode($last_log['detail'] ?? '', JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

        <?php else: ?>
            <!-- ================= SEKME 2: HTML ETİKETLERİ & DOĞRULAMA ================= -->
            <form method="post" action="">
                <?php wp_nonce_field('mis360_indexing_action', 'mis360_indexing_nonce'); ?>

                <div class="mis360-indexing-grid-tags">
                    <!-- Sol Kolon: Arama Motoru Doğrulama Kodları -->
                    <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:22px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;color:#1d2327;">
                            🔍 Arama Motoru Doğrulama Meta Etiketleri
                        </h2>
                        <p style="color:#646970;font-size:13px;line-height:1.5;">
                            Google Search Console, Bing, Yandex veya Meta (Facebook) gibi platformların sağladığı doğrulama kodlarını buraya ekleyebilirsiniz. 
                            <em>İster doğrudan <code>&lt;meta name="..." content="..." /&gt;</code> etiketinin tamamını yapıştırın, ister sadece içindeki doğrulama anahtarını yazın; sistem otomatik tanır.</em>
                        </p>

                        <!-- Google Search Console -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                🔴 Google Search Console Doğrulama
                            </label>
                            <input type="text" name="mis360_google_verification" 
                                   value="<?php echo esc_attr(get_option('mis360_google_verification', '')); ?>" 
                                   placeholder='google-site-verification veya <meta name="google-site-verification" content="..." />' 
                                   class="large-text" style="padding:7px 10px;" />
                            <p class="description" style="font-size:11px;color:#777;">
                                Örnek: <code>google-site-verification=abc123xyz</code> veya tüm <code>&lt;meta&gt;</code> etiketi.
                            </p>
                        </div>

                        <!-- Bing Webmaster Tools -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                🔵 Bing & Yahoo Webmaster Doğrulama
                            </label>
                            <input type="text" name="mis360_bing_verification" 
                                   value="<?php echo esc_attr(get_option('mis360_bing_verification', '')); ?>" 
                                   placeholder='msvalidate.01 doğrulama kodu veya tam meta etiketi' 
                                   class="large-text" style="padding:7px 10px;" />
                            <p class="description" style="font-size:11px;color:#777;">
                                Örnek: <code>&lt;meta name="msvalidate.01" content="91283019283..." /&gt;</code>
                            </p>
                        </div>

                        <!-- Yandex Webmaster -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                🟡 Yandex Webmaster Doğrulama
                            </label>
                            <input type="text" name="mis360_yandex_verification" 
                                   value="<?php echo esc_attr(get_option('mis360_yandex_verification', '')); ?>" 
                                   placeholder='yandex-verification kodu veya tam meta etiketi' 
                                   class="large-text" style="padding:7px 10px;" />
                            <p class="description" style="font-size:11px;color:#777;">
                                Örnek: <code>&lt;meta name="yandex-verification" content="abcdef012345" /&gt;</code>
                            </p>
                        </div>

                        <!-- Meta (Facebook) Domain Verification -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                🔷 Meta (Facebook) Alan Adı Doğrulama
                            </label>
                            <input type="text" name="mis360_facebook_verification" 
                                   value="<?php echo esc_attr(get_option('mis360_facebook_verification', '')); ?>" 
                                   placeholder='facebook-domain-verification kodu' 
                                   class="large-text" style="padding:7px 10px;" />
                            <p class="description" style="font-size:11px;color:#777;">
                                Facebook Business Manager alan adı doğrulama meta kodu.
                            </p>
                        </div>

                        <!-- Pinterest Domain Verification -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                📌 Pinterest Alan Doğrulama
                            </label>
                            <input type="text" name="mis360_pinterest_verification" 
                                   value="<?php echo esc_attr(get_option('mis360_pinterest_verification', '')); ?>" 
                                   placeholder='p:domain_verify kodu' 
                                   class="large-text" style="padding:7px 10px;" />
                            <p class="description" style="font-size:11px;color:#777;">
                                Pinterest işletme hesabı için site doğrulama kodu.
                            </p>
                        </div>
                    </div>

                    <!-- Sağ Kolon: Serbest HTML, Head, Body & Footer Kodları -->
                    <div class="postbox" style="background:#fff;border:1px solid #ccd0d4;padding:22px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;font-size:16px;color:#1d2327;">
                            💻 Serbest HTML & Takip Scriptleri
                        </h2>
                        <p style="color:#646970;font-size:13px;line-height:1.5;">
                            Google Tag Manager, Google Analytics (GA4), Meta Pixel, Yandex Metrica veya özel CSS/JS kodlarınızı sitenizin ilgili kısımlarına güvenle ekleyin.
                        </p>

                        <!-- Header Kodları -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                🌐 &lt;head&gt; Kodları (Header HTML & Scriptler)
                            </label>
                            <textarea name="mis360_custom_header_html" rows="5" class="large-text" 
                                      style="font-family:monospace;font-size:12px;padding:8px;" 
                                      placeholder="&lt;!-- Google Analytics, GTM, Meta Etiketleri veya Özel CSS --&gt;"><?php echo esc_textarea(get_option('mis360_custom_header_html', '')); ?></textarea>
                            <p class="description" style="font-size:11px;color:#777;">
                                <code>&lt;head&gt;</code> etiketinin hemen içine yerleştirilir (GA4, GTM ana kodu, özel meta tagler).
                            </p>
                        </div>

                        <!-- Body Açılış Kodları -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                🚪 &lt;body&gt; Başlangıç Kodları (Body Start HTML)
                            </label>
                            <textarea name="mis360_custom_body_html" rows="4" class="large-text" 
                                      style="font-family:monospace;font-size:12px;padding:8px;" 
                                      placeholder="&lt;!-- Google Tag Manager (noscript) veya açılış scriptleri --&gt;"><?php echo esc_textarea(get_option('mis360_custom_body_html', '')); ?></textarea>
                            <p class="description" style="font-size:11px;color:#777;">
                                <code>&lt;body&gt;</code> etiketinin hemen ardından çalıştırılır (Örn: GTM noscript iframe kodu).
                            </p>
                        </div>

                        <!-- Footer Kapanış Kodları -->
                        <div style="margin-top:18px;">
                            <label style="display:block;font-weight:600;margin-bottom:5px;">
                                ⚓ &lt;/body&gt; Öncesi Kodlar (Footer HTML & Scriptler)
                            </label>
                            <textarea name="mis360_custom_footer_html" rows="4" class="large-text" 
                                      style="font-family:monospace;font-size:12px;padding:8px;" 
                                      placeholder="&lt;!-- Canlı Destek Widget, WhatsApp balonu, Meta Pixel veya takip JS --&gt;"><?php echo esc_textarea(get_option('mis360_custom_footer_html', '')); ?></textarea>
                            <p class="description" style="font-size:11px;color:#777;">
                                Sayfa altındaki <code>&lt;/body&gt;</code> kapanışından hemen önce basılır (Canlı destek, piksel kodları).
                            </p>
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" name="mis360_action_save_html_tags" class="button button-primary" style="background:#7a00df;border-color:#6500b8;padding:8px 24px;font-size:14px;font-weight:600;height:auto;">
                        💾 Tüm HTML Etiketlerini ve Kodları Kaydet
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <?php
}
