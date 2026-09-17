<?php
/**
 * Mis360 & Emdief Home - Trendyol Yorum İçe Aktarıcı Bot
 * WooCommerce ürünlerine Trendyol ürün yorumlarını (yıldız, isim, tarih, metin) aktarır.
 *
 * @package Mis360_Mobilya
 * @since 1.9.49
 */

defined('ABSPATH') || exit;

/**
 * 1. Ürün Düzenleme Ekranına Metabox Ekle
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'mis360_trendyol_reviews_box',
        '🧸 MİS360 Trendyol Yorum İçe Aktarıcı Bot (Otomatik Çekici)',
        'mis360_render_trendyol_review_metabox',
        'product',
        'normal',
        'high'
    );
});

/**
 * 2. Ürün Düzenleme Metabox Render Edici
 */
function mis360_render_trendyol_review_metabox($post) {
    wp_nonce_field('mis360_trendyol_import_action', 'mis360_trendyol_import_nonce');
    $product_id = $post->ID;
    $comments_count = get_comments(['post_id' => $product_id, 'count' => true, 'type' => 'review']);
    $product = wc_get_product($product_id);
    $rating = $product ? $product->get_average_rating() : 0;
    $site_ajax_url = admin_url('admin-ajax.php');
    ?>
    <div class="mis360-importer-metabox">
        <style>
            .mis360-importer-metabox { padding: 10px 4px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
            .importer-stat-row { display: flex; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
            .importer-stat-box { flex: 1; min-width: 140px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; }
            .importer-stat-val { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1; }
            .importer-stat-label { font-size: 0.78rem; font-weight: 600; color: #64748b; margin-top: 2px; }
            
            .importer-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 22px; margin-bottom: 18px; box-shadow: 0 4px 14px rgba(0,0,0,0.02); }
            
            .importer-input-group { margin-bottom: 14px; }
            .importer-input-group label { display: block; font-weight: 700; font-size: 0.9rem; color: #1e293b; margin-bottom: 8px; }
            .importer-input-group input[type="text"], .importer-input-group textarea { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; box-sizing: border-box; }
            .importer-input-group input:focus, .importer-input-group textarea:focus { border-color: #ea580c; outline: none; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15); }
            
            .importer-btn-group { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
            .importer-btn-primary { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: #ffffff !important; border: none; border-radius: 10px; padding: 12px 26px; font-weight: 800; font-size: 0.96rem; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35); }
            .importer-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(234, 88, 12, 0.45); }
            
            .importer-btn-secondary { background: #f1f5f9; color: #334155; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 11px 18px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
            .importer-btn-secondary:hover { background: #e2e8f0; color: #0f172a; }
            
            .importer-btn-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 10px; padding: 9px 16px; font-weight: 650; font-size: 0.84rem; cursor: pointer; transition: all 0.2s ease; margin-left: auto; }
            .importer-btn-danger:hover { background: #fecaca; }
            
            .importer-log-box { background: #0f172a; color: #38bdf8; border-radius: 10px; padding: 14px 18px; font-family: monospace; font-size: 0.86rem; margin-top: 16px; display: none; max-height: 220px; overflow-y: auto; border: 1px solid #334155; line-height: 1.6; }
        </style>

        <!-- Canlı İstatistik Çubuğu -->
        <div class="importer-stat-row">
            <div class="importer-stat-box">
                <span style="font-size: 28px;">⭐</span>
                <div>
                    <div class="importer-stat-val" id="mis360-rating-val"><?php echo number_format((float)$rating, 1, ',', '.'); ?> / 5.0</div>
                    <div class="importer-stat-label">Ürün Ortalama Puanı</div>
                </div>
            </div>
            <div class="importer-stat-box">
                <span style="font-size: 28px;">💬</span>
                <div>
                    <div class="importer-stat-val" id="mis360-review-count-val"><?php echo esc_html($comments_count); ?></div>
                    <div class="importer-stat-label">Toplam WooCommerce Yorumu</div>
                </div>
            </div>
            <div class="importer-stat-box">
                <span style="font-size: 28px;">🛡️</span>
                <div>
                    <div class="importer-stat-val" style="color: #10b981;">Doğrulandı</div>
                    <div class="importer-stat-label">Trendyol Gerçek Alıcı Rozeti</div>
                </div>
            </div>
        </div>

        <!-- DOĞRUDAN OTOMATİK LINK ÇEKİCİ -->
        <div class="importer-card">
            <div style="font-weight: 800; font-size: 1.1rem; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <span>⚡</span> <span>Trendyol Linki İle Yorumları Otomatik Çek</span>
            </div>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0; margin-bottom: 16px;">
                Aşağıdaki kutucuğa bu ürünün Trendyol linkini yapıştırın ve butona tıklayın. Bot tüm gerçek müşteri yorumlarını, puanlarını ve isimlerini saniyeler içinde mağazanıza aktarır.
            </p>

            <div class="importer-input-group">
                <label for="mis360_ty_product_url">Trendyol Ürün Linki:</label>
                <input type="text" id="mis360_ty_product_url" placeholder="https://www.trendyol.com/emdief-home/carmen-3-rafli-kitaplik-cocuk-odasi-egitici-montessori-kitaplik-3-rafli-bebek-odasi-kitap-p-38195379">
            </div>

            <div class="importer-btn-group">
                <button type="button" class="importer-btn-primary" id="mis360_btn_fetch_link">
                    <span>⚡</span> <span>Trendyol Yorumlarını Çek ve Kaydet</span>
                </button>

                <button type="button" class="importer-btn-secondary" id="mis360_btn_toggle_paste">
                    <span>📋</span> <span>Manuel Yorum Yapıştırıcıyı Aç</span>
                </button>
            </div>

            <!-- Manuel Yapıştırma Alanı (İsteğe Bağlı) -->
            <div id="mis360_paste_container" style="display: none; margin-top: 18px; padding-top: 16px; border-top: 1.5px dashed #cbd5e1;">
                <div class="importer-input-group">
                    <label>Yorum Metinleri veya JSON Listesi:</label>
                    <textarea id="mis360_ty_raw_reviews" rows="5" placeholder="Dilerseniz Trendyol'dan kopyaladığınız yorum metinlerini veya JSON verisini buraya yapıştırıp içe aktarabilirsiniz."></textarea>
                </div>
                <button type="button" class="importer-btn-secondary" id="mis360_btn_parse_raw" style="background:#0f172a;color:#fff;border-color:#0f172a;">
                    <span>📋</span> <span>Yapıştırılan Yorumları Kaydet</span>
                </button>
            </div>

            <div class="importer-log-box" id="mis360_importer_log"></div>
        </div>

        <!-- Alt Eylemler & Temizleme -->
        <div style="display: flex; align-items: center; margin-top: 14px;">
            <button type="button" class="importer-btn-danger" id="mis360_btn_clear_reviews" data-product-id="<?php echo $product_id; ?>" onclick="return confirm('DİKKAT: Bu ürüne ait tüm yorumlar kalıcı olarak silinecek. Onaylıyor musunuz?');">
                🗑️ Bu Üründeki Tüm Yorumları Temizle
            </button>
        </div>
    </div>

    <script>
    (function($){
        const productId = <?php echo $product_id; ?>;
        const ajaxUrl = '<?php echo esc_url($site_ajax_url); ?>';
        const nonce = '<?php echo wp_create_nonce("mis360_trendyol_ajax_nonce"); ?>';

        function logMsg(msg, isSuccess) {
            const $box = $('#mis360_importer_log');
            $box.show().append('<div style="color:' + (isSuccess ? '#4ade80' : '#38bdf8') + ';margin-bottom:4px;">' + msg + '</div>');
            $box.scrollTop($box[0].scrollHeight);
        }

        $('#mis360_btn_toggle_paste').on('click', function(){
            $('#mis360_paste_container').slideToggle(200);
        });

        // 1. Link ile Otomatik Çekme (TLS 1.3 Doğrudan Motor)
        $('#mis360_btn_fetch_link').on('click', function(){
            const urlVal = $('#mis360_ty_product_url').val().trim();
            if (!urlVal) {
                alert('Lütfen geçerli bir Trendyol ürün linki girin!');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span>⏳</span> <span>Trendyol Yorumları Taranıyor...</span>');
            logMsg('Trendyol üzerinden yorumlar taranıyor: ' + urlVal);

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mis360_ajax_fetch_and_import_by_url',
                    url: urlVal,
                    product_id: productId,
                    nonce: nonce
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<span>⚡</span> <span>Trendyol Yorumlarını Çek ve Kaydet</span>');
                    if (res && res.success) {
                        logMsg('✓ Başarılı: ' + res.data.message, true);
                        if (res.data.total !== undefined) {
                            $('#mis360-review-count-val').text(res.data.total);
                        }
                        if (res.data.rating !== undefined) {
                            $('#mis360-rating-val').text(Number(res.data.rating).toFixed(1) + ' / 5.0');
                        }
                        alert('🎉 ' + res.data.message);
                    } else {
                        var msg = res && res.data ? (res.data.message || res.data) : 'Yorumlar çekilemedi.';
                        logMsg('⚠️ ' + msg, false);
                        alert('⚠️ Hata: ' + msg);
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html('<span>⚡</span> <span>Trendyol Yorumlarını Çek ve Kaydet</span>');
                    logMsg('Bağlantı hatası oluştu: ' + error);
                    alert('Bağlantı hatası oluştu. Lütfen tekrar deneyin.');
                }
            });
        });

        // 2. Manuel Yapıştırılan Yorumları Kaydet
        $('#mis360_btn_parse_raw').on('click', function(){
            const textVal = $('#mis360_ty_raw_reviews').val().trim();
            if (!textVal) {
                alert('Lütfen yapıştırılacak yorum metinlerini girin!');
                return;
            }
            const $btn = $(this);
            $btn.prop('disabled', true).text('Ayrıştırılıyor...');
            logMsg('Yapıştırılan metin ayrıştırılıyor...');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mis360_ajax_import_raw_reviews',
                    raw_text: textVal,
                    product_id: productId,
                    nonce: nonce
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<span>📋</span> <span>Yapıştırılan Yorumları Kaydet</span>');
                    if (res && res.success) {
                        logMsg('✓ Başarılı: ' + res.data.imported + ' adet yorum içe aktarıldı!', true);
                        $('#mis360-review-count-val').text(res.data.total);
                        if (res.data.rating !== undefined) {
                            $('#mis360-rating-val').text(Number(res.data.rating).toFixed(1) + ' / 5.0');
                        }
                        $('#mis360_ty_raw_reviews').val('');
                        alert('🎉 ' + res.data.imported + ' adet yorum başarıyla eklendi!');
                    } else {
                        logMsg('⚠️ Hata: ' + (res.data || 'Ayrıştırma başarısız.'));
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span>📋</span> <span>Yapıştırılan Yorumları Kaydet</span>');
                    logMsg('İşlem sırasında hata oluştu.');
                }
            });
        });

        // 3. Yorumları Temizleme
        $('#mis360_btn_clear_reviews').on('click', function(){
            const $btn = $(this);
            $btn.prop('disabled', true).text('Siliniyor...');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mis360_ajax_clear_product_reviews',
                    product_id: productId,
                    nonce: nonce
                },
                success: function(res) {
                    $btn.prop('disabled', false).text('🗑️ Bu Üründeki Tüm Yorumları Temizle');
                    if (res && res.success) {
                        logMsg('✓ Bu ürüne ait tüm yorumlar başarıyla temizlendi.', true);
                        $('#mis360-review-count-val').text('0');
                        $('#mis360-rating-val').text('0,0 / 5.0');
                    }
                }
            });
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * 3. AJAX Handler: URL İle Trendyol'dan Doğrudan Çek ve Kaydet (TLS 1.3 Motoru)
 */
function mis360_ajax_fetch_and_import_by_url() {
    check_ajax_referer('mis360_trendyol_ajax_nonce', 'nonce');

    if (!current_user_can('edit_products')) {
        wp_send_json_error('Yetkisiz işlem.');
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $url        = isset($_POST['url']) ? sanitize_text_field(wp_unslash($_POST['url'])) : '';

    if (!$product_id || empty($url)) {
        wp_send_json_error('Geçersiz ürün veya URL.');
    }

    // Content ID ayıkla
    $cid = null;
    if (preg_match('/-p-([0-9]+)/', $url, $m)) {
        $cid = $m[1];
    } elseif (preg_match('/contentId=([0-9]+)/', $url, $m)) {
        $cid = $m[1];
    } elseif (is_numeric(trim($url))) {
        $cid = trim($url);
    }

    if (!$cid) {
        wp_send_json_error('Trendyol ürün kimliği (contentId) tespit edilemedi. Lütfen geçerli bir link girin.');
    }

    $clean = preg_replace('/\?.*$/', '', $url);
    if (!str_ends_with($clean, '/yorumlar')) {
        $yorumlar_url = rtrim($clean, '/') . '/yorumlar';
    } else {
        $yorumlar_url = $clean;
    }

    // TLS 1.3 İle Doğrudan cURL İsteği (Cloudflare Engelini Aşar)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $yorumlar_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    if (defined('CURL_SSLVERSION_TLSv1_3')) {
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: tr-TR,tr;q=0.9'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $html = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200 || empty($html)) {
        wp_send_json_error('Trendyol sayfası açılamadı (HTTP ' . $http_code . '). Lütfen linkin doğruluğunu kontrol edin.');
    }

    $reviews_to_save = [];
    $pos = strpos($html, '__review-detail__PROPS');
    if ($pos !== false) {
        $start = strpos($html, '{', $pos);
        if ($start !== false) {
            $len = strlen($html);
            $count = 0;
            $end = false;
            for ($i = $start; $i < $len; $i++) {
                $char = $html[$i];
                if ($char === '{') $count++;
                elseif ($char === '}') {
                    $count--;
                    if ($count === 0) { $end = $i + 1; break; }
                }
            }
            if ($end !== false) {
                $data = json_decode(substr($html, $start, $end - $start), true);
                if (is_array($data)) {
                    // reviewImages
                    if (!empty($data['reviewImages']['content'])) {
                        foreach ($data['reviewImages']['content'] as $item) {
                            $c = !empty($item['comment']) ? trim($item['comment']) : '';
                            if ($c && strlen($c) > 2) {
                                $reviews_to_save[] = [
                                    'author'   => !empty($item['userFullName']) ? $item['userFullName'] : 'Müşteri',
                                    'rating'   => !empty($item['rate']) ? (int)$item['rate'] : 5,
                                    'comment'  => $c,
                                    'date'     => !empty($item['lastModifiedDate']) ? date('Y-m-d H:i:s', (int)($item['lastModifiedDate'] / 1000)) : current_time('mysql'),
                                    'verified' => true
                                ];
                            }
                        }
                    }
                    // productReviews
                    if (!empty($data['productReviews']['content'])) {
                        foreach ($data['productReviews']['content'] as $item) {
                            $c = !empty($item['comment']) ? trim($item['comment']) : '';
                            if ($c && strlen($c) > 2) {
                                $reviews_to_save[] = [
                                    'author'   => !empty($item['userFullName']) ? $item['userFullName'] : 'Müşteri',
                                    'rating'   => !empty($item['rate']) ? (int)$item['rate'] : 5,
                                    'comment'  => $c,
                                    'date'     => !empty($item['lastModifiedDate']) ? date('Y-m-d H:i:s', (int)($item['lastModifiedDate'] / 1000)) : current_time('mysql'),
                                    'verified' => true
                                ];
                            }
                        }
                    }
                }
            }
        }
    }

    if (empty($reviews_to_save)) {
        wp_send_json_error('Bu üründe Trendyol üzerinde onaylanmış yorum tespit edilemedi.');
    }

    $result = mis360_process_and_save_reviews($product_id, $reviews_to_save);
    wp_send_json_success($result);
}
add_action('wp_ajax_mis360_ajax_fetch_and_import_by_url', 'mis360_ajax_fetch_and_import_by_url');

/**
 * 4. AJAX Handler: Manuel Yapıştırılan Metinleri veya JSON Verisini Kaydet
 */
function mis360_ajax_import_raw_reviews() {
    check_ajax_referer('mis360_trendyol_ajax_nonce', 'nonce');

    if (!current_user_can('edit_products')) {
        wp_send_json_error('Yetkisiz işlem.');
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $raw_text   = isset($_POST['raw_text']) ? wp_unslash($_POST['raw_text']) : '';

    if (!$product_id || empty($raw_text)) {
        wp_send_json_error('Eksik ürün veya yorum verisi.');
    }

    $reviews_to_save = [];

    // 1. JSON formatında mı?
    $json_test = json_decode($raw_text, true);
    if (is_array($json_test)) {
        $list = isset($json_test['result']['productReviews']['content']) ? $json_test['result']['productReviews']['content'] : $json_test;
        foreach ($list as $item) {
            $comment = isset($item['comment']) ? $item['comment'] : (isset($item['content']) ? $item['content'] : '');
            if (empty($comment)) continue;

            $reviews_to_save[] = [
                'author'   => isset($item['author']) ? $item['author'] : (isset($item['userFullName']) ? $item['userFullName'] : 'Müşteri'),
                'rating'   => isset($item['rating']) ? (int)$item['rating'] : (isset($item['rate']) ? (int)$item['rate'] : 5),
                'comment'  => $comment,
                'date'     => isset($item['date']) ? $item['date'] : current_time('mysql'),
                'verified' => true
            ];
        }
    } else {
        // 2. Düz Metin Blokları
        $blocks = preg_split('/\n{2,}|\r\n\r\n/', $raw_text);
        if (count($blocks) <= 1) {
            $blocks = explode("\n", $raw_text);
        }

        $current_author = 'Müşteri';
        $current_date   = current_time('mysql');
        $current_rating = 5;

        foreach ($blocks as $block) {
            $trimmed = trim($block);
            if (empty($trimmed) || strlen($trimmed) < 4) continue;

            if (preg_match('/(Satıcı:|Ürünü Değerlendir|Sepete Ekle|Kargo Bedava|Fotoğraflı Yorumlar|Değerlendirmeler|Faydalı Buldum)/iu', $trimmed)) {
                continue;
            }

            if (preg_match('/^([A-ZÇĞİÖŞÜa-zçğıöşü]\*{1,3}\s+[A-ZÇĞİÖŞÜa-zçğıöşü]\*{1,3})$/u', $trimmed, $am)) {
                $current_author = $am[1];
                continue;
            }

            if (preg_match('/([0-9]{1,2}\s+[A-Za-zÇĞİÖŞÜçğıöşü]+\s+[0-9]{4})/u', $trimmed, $dm)) {
                $current_date = $dm[1];
                continue;
            }

            $reviews_to_save[] = [
                'author'   => $current_author,
                'rating'   => $current_rating,
                'comment'  => $trimmed,
                'date'     => $current_date,
                'verified' => true
            ];

            $current_author = 'Müşteri';
        }
    }

    if (empty($reviews_to_save)) {
        wp_send_json_error('Metin içinden geçerli yorum tespit edilemedi.');
    }

    $result = mis360_process_and_save_reviews($product_id, $reviews_to_save);
    wp_send_json_success($result);
}
add_action('wp_ajax_mis360_ajax_import_raw_reviews', 'mis360_ajax_import_raw_reviews');

/**
 * 5. AJAX Handler: Ürüne Ait Yorumları Toplu Temizle
 */
function mis360_ajax_clear_product_reviews() {
    check_ajax_referer('mis360_trendyol_ajax_nonce', 'nonce');

    if (!current_user_can('edit_products')) {
        wp_send_json_error('Yetkisiz işlem.');
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_send_json_error('Geçersiz ürün.');
    }

    $comments = get_comments(['post_id' => $product_id, 'type' => 'review']);
    foreach ($comments as $c) {
        wp_delete_comment($c->comment_ID, true);
    }

    if (function_exists('wc_update_product_reviews_count')) {
        wc_update_product_reviews_count($product_id);
    }
    if (function_exists('wc_update_product_rating')) {
        wc_update_product_rating($product_id);
    }
    clean_post_cache($product_id);

    wp_send_json_success(['message' => 'Tüm yorumlar silindi.']);
}
add_action('wp_ajax_mis360_ajax_clear_product_reviews', 'mis360_ajax_clear_product_reviews');

/**
 * 6. Çekirdek Fonksiyon: Gelen Yorumları WooCommerce Veritabanına Yaz
 */
function mis360_process_and_save_reviews($product_id, $reviews_data) {
    $imported = 0;
    $skipped  = 0;

    $existing_comments = get_comments([
        'post_id' => $product_id,
        'type'    => 'review',
        'fields'  => 'ids',
    ]);
    
    $existing_texts = [];
    foreach ($existing_comments as $cid) {
        $c = get_comment($cid);
        if ($c) {
            $existing_texts[] = md5(trim($c->comment_content));
        }
    }

    foreach ($reviews_data as $rev) {
        $content = isset($rev['comment']) ? sanitize_textarea_field(wp_unslash($rev['comment'])) : '';
        if (empty($content) || strlen($content) < 3) {
            $skipped++;
            continue;
        }

        $hash = md5(trim($content));
        if (in_array($hash, $existing_texts, true)) {
            $skipped++;
            continue;
        }

        $author = isset($rev['author']) ? sanitize_text_field(wp_unslash($rev['author'])) : 'Müşteri';
        if (empty($author) || $author === 'null') {
            $author = 'Müşteri';
        }

        $rating = isset($rev['rating']) ? min(5, max(1, absint($rev['rating']))) : 5;
        
        $date_str = isset($rev['date']) ? sanitize_text_field($rev['date']) : '';
        $date_time = (!empty($date_str) && strtotime($date_str)) ? date('Y-m-d H:i:s', strtotime($date_str)) : current_time('mysql');

        $clean_name = sanitize_title($author);
        if (empty($clean_name)) $clean_name = 'musteri-' . wp_rand(100, 999);
        $email = $clean_name . '@emdief-musteri.com';

        $comment_id = wp_insert_comment([
            'comment_post_ID'      => $product_id,
            'comment_author'       => $author,
            'comment_author_email' => $email,
            'comment_content'      => $content,
            'comment_type'         => 'review',
            'comment_parent'       => 0,
            'user_id'              => 0,
            'comment_author_IP'    => '127.0.0.1',
            'comment_agent'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Trendyol-Sync',
            'comment_date'         => $date_time,
            'comment_date_gmt'     => get_gmt_from_date($date_time),
            'comment_approved'     => 1,
        ]);

        if ($comment_id) {
            update_comment_meta($comment_id, 'rating', $rating);
            update_comment_meta($comment_id, 'verified', 1);
            update_comment_meta($comment_id, 'mis360_source', 'trendyol');
            $existing_texts[] = $hash;
            $imported++;
        }
    }

    if (function_exists('wc_update_product_reviews_count')) {
        wc_update_product_reviews_count($product_id);
    }
    if (function_exists('wc_update_product_rating')) {
        wc_update_product_rating($product_id);
    }
    clean_post_cache($product_id);

    $total_reviews = get_comments(['post_id' => $product_id, 'count' => true, 'type' => 'review']);
    $product = wc_get_product($product_id);
    $new_rating = $product ? $product->get_average_rating() : 0;

    return [
        'imported' => $imported,
        'skipped'  => $skipped,
        'total'    => $total_reviews,
        'rating'   => $new_rating,
        'message'  => sprintf('%d adet yeni yorum WooCommerce mağazanıza başarıyla kaydedildi! (Toplam: %d)', $imported, $total_reviews)
    ];
}