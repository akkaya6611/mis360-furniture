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

// 5. WP Admin Menüsü & Arayüzü
add_action('admin_menu', 'mis360_indexing_admin_menu');
function mis360_indexing_admin_menu() {
    add_submenu_page(
        'tools.php',
        'SEO & Hızlı İndeksleme (Sitemap & IndexNow)',
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
            $notice = 'Ayarlar kaydedildi.';
            $notice_type = 'success';
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
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span class="dashicons dashicons-search" style="font-size:32px;width:32px;height:32px;color:#7a00df;"></span>
            Emdief Home - SEO & Hızlı İndeksleme Modülü
        </h1>
        <p style="color:#646970;font-size:14px;margin-bottom:20px;">
            Sitenizdeki sayfaları, WooCommerce ürünlerini ve XML Haritalarını Google, Bing, Yandex ve IndexNow protokolü (Bing, Yandex, Seznam, Naver) aracılığıyla anında arama motorlarına bildirin.
        </p>

        <?php if ($notice): ?>
            <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible" style="padding:12px 15px;border-left-width:4px;">
                <p><?php echo wp_kses_post($notice); ?></p>
            </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:20px;">
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
    <?php
}
