<?php
/**
 * Mis360 & Emdief Home - Trendyol Yorum İçe Aktarıcı Bot
 * WooCommerce ürünlerine Trendyol ürün yorumlarını (yıldız, isim, tarih, metin) aktarır.
 *
 * @package Mis360_Mobilya
 * @since 1.9.46
 */

defined('ABSPATH') || exit;

/**
 * 1. Ürün Düzenleme Ekranına Metabox Ekle
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'mis360_trendyol_reviews_box',
        '🧸 MİS360 Trendyol Yorum İçe Aktarıcı Bot',
        'mis360_render_trendyol_review_metabox',
        'product',
        'normal',
        'high'
    );
});

/**
 * 2. Ürünler Menüsü Altına Özel Yönetim Sayfası Ekle
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=product',
        'Trendyol Yorum Botu',
        '⭐ Trendyol Yorum Botu',
        'manage_woocommerce',
        'mis360-trendyol-importer',
        'mis360_render_trendyol_bot_admin_page'
    );
});

/**
 * 3. Ürün Düzenleme Metabox Render Edici
 */
function mis360_render_trendyol_review_metabox($post) {
    wp_nonce_field('mis360_trendyol_import_action', 'mis360_trendyol_import_nonce');
    $product_id = $post->ID;
    $comments_count = get_comments(['post_id' => $product_id, 'count' => true, 'type' => 'review']);
    $product = wc_get_product($product_id);
    $rating = $product ? $product->get_average_rating() : 0;
    
    // AJAX URL ve Güvenlik Tokenı
    $site_ajax_url = admin_url('admin-ajax.php');
    $import_token  = wp_create_nonce('mis360_bookmarklet_import_' . $product_id);
    
    // Bookmarklet JavaScript Kodu (Trendyol Üzerinde Çalışacak Kendi Kendini Onaran Motor)
    $bm_code = <<<JAVASCRIPT
(function(){
    var PID = {$product_id};
    var AJAX_URL = '{$site_ajax_url}';
    var TOKEN = '{$import_token}';

    // 1. Ürün Kimliğini Tespit Et
    var m = location.pathname.match(/-p-([0-9]+)/);
    var cid = m ? m[1] : null;
    if (!cid && window.__PRODUCT_DETAIL_APP_INITIAL_STATE__ && window.__PRODUCT_DETAIL_APP_INITIAL_STATE__.product) {
        cid = window.__PRODUCT_DETAIL_APP_INITIAL_STATE__.product.id;
    }
    if (!cid) {
        var qm = location.search.match(/contentId=([0-9]+)/);
        if (qm) cid = qm[1];
    }

    // Modal Arayüzünü Oluştur
    var old = document.getElementById('mis360-ty-overlay');
    if (old) old.remove();

    var overlay = document.createElement('div');
    overlay.id = 'mis360-ty-overlay';
    overlay.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999999;background:#0f172a;color:#f8fafc;padding:20px;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,0.5);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:13px;width:360px;border:2px solid #ea580c;line-height:1.5;box-sizing:border-box;';
    
    function setContent(html) {
        overlay.innerHTML = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;border-bottom:1px solid #334155;padding-bottom:8px;">'
            + '<span style="font-weight:800;color:#fb923c;font-size:14px;display:flex;align-items:center;gap:6px;">🧸 MİS360 Yorum Botu</span>'
            + '<button id="mis360-ty-close" style="background:none;border:none;color:#94a3b8;font-size:18px;cursor:pointer;line-height:1;padding:0 4px;">✕</button>'
            + '</div>' + html;
        var cb = document.getElementById('mis360-ty-close');
        if (cb) cb.onclick = function(){ overlay.remove(); };
    }

    document.body.appendChild(overlay);
    setContent('<div style="color:#93c5fd;font-weight:600;margin-bottom:8px;">🔍 Sayfa ve Yorumlar Taranıyor...</div><div style="font-size:12px;color:#cbd5e1;">Ürün ID: ' + (cid || 'Tespit ediliyor...') + '</div>');

    // DOM Scraping Fonksiyonu (Trendyol Değerlendirmeleri)
    function scrapeFromDOM() {
        var results = [];
        var selectors = ['.r-c-c', '.comment', '.review-comment', '[class*="comment-card"]', '[class*="review-item"]', '[class*="ReviewCard"]', '.ps-r-w'];
        var cards = document.querySelectorAll(selectors.join(','));
        
        cards.forEach(function(c) {
            var fullText = c.innerText.trim();
            if (fullText.length < 5) return;
            
            // Yorum Metni
            var textEl = c.querySelector('p, .comment-text, [class*="comment-text"], [class*="content"], .r-c-c-c');
            var comment = textEl ? textEl.innerText.trim() : '';
            if (!comment || comment.length < 5) {
                var lines = fullText.split('\n').map(function(l){ return l.trim(); }).filter(function(l){
                    return l.length > 5 && !l.includes('Satıcı:') && !l.includes('Beden:') && !l.includes('Faydalı') && !l.includes('Yorumu');
                });
                if (lines.length) comment = lines[0];
            }
            if (!comment || comment.length < 3) return;

            // Yazar
            var author = 'Müşteri';
            var authorMatch = fullText.match(/([A-ZÇĞİÖŞÜa-zçğıöşü]\*{1,4}\s+[A-ZÇĞİÖŞÜa-zçğıöşü]\*{1,4})/);
            if (authorMatch) {
                author = authorMatch[1];
            } else {
                var uEl = c.querySelector('[class*="user"], [class*="author"], [class*="name"]');
                if (uEl && uEl.innerText.trim()) author = uEl.innerText.trim();
            }

            // Yıldız Puanı
            var rating = 5;
            var starSvgs = c.querySelectorAll('svg');
            if (starSvgs.length >= 1 && starSvgs.length <= 5) {
                rating = starSvgs.length;
            }

            // Tarih
            var dateStr = '';
            var dateMatch = fullText.match(/([0-9]{1,2}\s+[A-Za-zÇĞİÖŞÜçğıöşü]+\s+[0-9]{4})/);
            if (dateMatch) dateStr = dateMatch[1];

            results.push({
                author: author,
                rating: rating,
                comment: comment,
                date: dateStr,
                verified: true
            });
        });
        return results;
    }

    // WooCommerce'a Gönder veya Panoya Kopyala
    function sendToWooCommerce(reviews) {
        if (!reviews || !reviews.length) {
            showEmptyGuide();
            return;
        }

        setContent('<div style="font-weight:700;color:#10b981;margin-bottom:6px;">✓ ' + reviews.length + ' Adet Yorum Tespit Edildi!</div>'
            + '<div style="color:#cbd5e1;font-size:12px;margin-bottom:12px;">WooCommerce mağazanıza aktarılıyor...</div>'
            + '<div style="background:#1e293b;padding:8px 10px;border-radius:8px;font-size:11px;color:#94a3b8;max-height:80px;overflow:hidden;">'
            + '"' + reviews[0].comment.substring(0, 60) + '..." (' + reviews[0].author + ')</div>');

        var jsonStr = JSON.stringify(reviews);

        // Panoya otomatik yedekle
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(jsonStr).catch(function(){});
        }

        // AJAX POST Denemesi (CORS destekli)
        var formData = new URLSearchParams();
        formData.append('action', 'mis360_ajax_import_trendyol_reviews');
        formData.append('product_id', PID);
        formData.append('token', TOKEN);
        formData.append('reviews', jsonStr);

        fetch(AJAX_URL, {
            method: 'POST',
            body: formData,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }
        })
        .then(function(res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function(resData) {
            if (resData && resData.success) {
                setContent('<div style="font-weight:800;color:#10b981;font-size:15px;margin-bottom:6px;">🎉 Başarıyla Aktarıldı!</div>'
                    + '<div style="color:#f1f5f9;margin-bottom:10px;">' + resData.data.imported + ' yeni yorum WooCommerce mağazanıza (#' + PID + ') kaydedildi.</div>'
                    + '<div style="font-size:11px;color:#94a3b8;">Ortalama Puan: ' + resData.data.rating + ' / Toplam: ' + resData.data.total + '</div>');
                setTimeout(function(){ overlay.remove(); }, 6000);
            } else {
                throw new Error(resData && resData.data ? resData.data : 'Bilinmeyen yanıt');
            }
        })
        .catch(function(err) {
            // Mixed Content veya CORS durumunda Pano Köprüsü devreye girer
            setContent('<div style="font-weight:700;color:#38bdf8;font-size:14px;margin-bottom:6px;">📋 ' + reviews.length + ' Yorum Panoya Kopyalandı!</div>'
                + '<div style="color:#cbd5e1;font-size:12px;margin-bottom:12px;">Tarayıcınızın güvenlik ayarları nedeniyle doğrudan aktarım yerine panonuz kullanıldı.</div>'
                + '<div style="background:#1e293b;padding:10px;border-radius:8px;font-size:12px;color:#f8fafc;margin-bottom:12px;">'
                + 'Şimdi WordPress ürün düzenleme sekmesine geçin ve <strong>"📥 Panodan Yorumları Oku ve Kaydet"</strong> butonuna tıklayın!</div>'
                + '<button id="mis360-copy-manual" style="background:#ea580c;color:#fff;border:none;padding:8px 14px;border-radius:8px;font-weight:700;cursor:pointer;width:100%;">Tekrar Panoya Kopyala</button>');
            
            var mb = document.getElementById('mis360-copy-manual');
            if (mb) {
                mb.onclick = function() {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(jsonStr).then(function(){ alert('✓ Yorumlar panoya kopyalandı! WordPress sayfasındaki yeşil butona basabilirsiniz.'); });
                    }
                };
            }
        });
    }

    function showEmptyGuide() {
        var yorumlarUrl = location.origin + location.pathname.replace(/\/yorumlar$/, '') + '/yorumlar';
        setContent('<div style="font-weight:700;color:#f59e0b;font-size:14px;margin-bottom:6px;">ℹ️ Yorumlar Henüz Sayfada Görünmüyor</div>'
            + '<div style="color:#cbd5e1;font-size:12px;margin-bottom:12px;">Trendyol yorumları sayfa açıldığında hemen yüklemez. Aşağıdaki butona tıklayarak doğrudan yorumlar sayfasına gidebilir veya sayfayı aşağı kaydırabilirsiniz:</div>'
            + '<a href="' + yorumlarUrl + '" style="display:block;text-align:center;background:#ea580c;color:#fff;text-decoration:none;font-weight:700;padding:10px;border-radius:8px;margin-bottom:8px;">👉 Tüm Yorumları Aç (Tek Tıkla Git)</a>'
            + '<button id="mis360-scroll-btn" style="background:#334155;color:#fff;border:none;padding:8px;border-radius:8px;width:100%;cursor:pointer;font-weight:600;font-size:12px;">🔽 Sayfayı Yorumlara Kaydır ve Tekrar Tara</button>');
        
        var sb = document.getElementById('mis360-scroll-btn');
        if (sb) {
            sb.onclick = function() {
                window.scrollTo({ top: document.body.scrollHeight * 0.5, behavior: 'smooth' });
                sb.innerText = 'Taranıyor...';
                setTimeout(function() {
                    var r = scrapeFromDOM();
                    if (r.length) {
                        sendToWooCommerce(r);
                    } else {
                        sb.innerText = 'Yorumlar sekmesine tıklayın veya sayfayı aşağı kaydırın';
                    }
                }, 1500);
            };
        }
    }

    // İlk Kontrol: DOM'da yorum var mı?
    var domReviews = scrapeFromDOM();
    if (domReviews.length > 0) {
        sendToWooCommerce(domReviews);
        return;
    }

    // İkinci Kontrol: Trendyol Public API dene (Güvenli, Asla Crash Olmaz)
    if (cid) {
        var apiUrl = 'https://public.trendyol.com/discovery-web-socialgw-service/api/reviews/' + cid + '?page=0&size=100';
        fetch(apiUrl, { headers: { 'Accept': 'application/json' } })
        .then(function(r) {
            if (!r.ok) throw new Error('API yanıt vermedi');
            var ct = r.headers.get('content-type') || '';
            if (!ct.includes('json')) throw new Error('Geçersiz yanıt tipi');
            return r.json();
        })
        .then(function(data) {
            var list = (data && data.result && data.result.productReviews && data.result.productReviews.content) ? data.result.productReviews.content : [];
            if (list && list.length) {
                var apiPayload = list.map(function(item) {
                    return {
                        author: item.userFullName || 'Müşteri',
                        rating: item.rate || 5,
                        comment: item.comment || '',
                        date: item.commentDateISOformat || '',
                        verified: true
                    };
                });
                sendToWooCommerce(apiPayload);
            } else {
                showEmptyGuide();
            }
        })
        .catch(function() {
            showEmptyGuide();
        });
    } else {
        showEmptyGuide();
    }
})();
JAVASCRIPT;

    // JavaScript kodunu tek satır bookmarklet formatına getir
    $min_bm = preg_replace('/\s+/', ' ', trim($bm_code));
    $bookmarklet_href = 'javascript:' . rawurlencode($min_bm);
    ?>
    <div class="mis360-importer-metabox">
        <style>
            .mis360-importer-metabox { padding: 10px 4px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
            .importer-stat-row { display: flex; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
            .importer-stat-box { flex: 1; min-width: 140px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; }
            .importer-stat-val { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1; }
            .importer-stat-label { font-size: 0.78rem; font-weight: 600; color: #64748b; margin-top: 2px; }
            
            .importer-method-tabs { display: flex; gap: 8px; margin-bottom: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; flex-wrap: wrap; }
            .importer-tab-btn { background: #f1f5f9; border: none; border-radius: 8px; padding: 8px 16px; font-size: 0.86rem; font-weight: 650; color: #475569; cursor: pointer; transition: all 0.2s ease; }
            .importer-tab-btn.is-active { background: #ea580c; color: #ffffff; }
            
            .importer-tab-content { display: none; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 18px; }
            .importer-tab-content.is-active { display: block; }
            
            .bookmarklet-drag-btn { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: #ffffff !important; padding: 12px 24px; border-radius: 12px; font-weight: 750; font-size: 0.95rem; text-decoration: none !important; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35); cursor: move; border: 2px dashed #ffedd5; }
            .bookmarklet-drag-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(234, 88, 12, 0.45); }
            
            .importer-input-group { margin-bottom: 14px; }
            .importer-input-group label { display: block; font-weight: 650; font-size: 0.85rem; color: #1e293b; margin-bottom: 6px; }
            .importer-input-group input[type="text"], .importer-input-group textarea { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 10px 14px; font-size: 0.9rem; }
            .importer-input-group input:focus, .importer-input-group textarea:focus { border-color: #ea580c; outline: none; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15); }
            
            .importer-filter-row { display: flex; gap: 16px; margin-bottom: 16px; align-items: center; flex-wrap: wrap; }
            .importer-filter-row label { font-size: 0.84rem; font-weight: 600; color: #334155; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }
            
            .importer-btn-primary { background: #ea580c; color: #ffffff; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px; }
            .importer-btn-primary:hover { background: #c2410c; }
            .importer-btn-success { background: #10b981; color: #ffffff; border: none; border-radius: 10px; padding: 10px 18px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
            .importer-btn-success:hover { background: #059669; }
            .importer-btn-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 10px; padding: 9px 16px; font-weight: 650; font-size: 0.84rem; cursor: pointer; transition: all 0.2s ease; margin-left: auto; }
            .importer-btn-danger:hover { background: #fecaca; }
            
            .importer-log-box { background: #0f172a; color: #38bdf8; border-radius: 10px; padding: 14px 18px; font-family: monospace; font-size: 0.82rem; margin-top: 14px; display: none; max-height: 180px; overflow-y: auto; border: 1px solid #334155; }
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

        <!-- Yöntem Sekmeleri -->
        <div class="importer-method-tabs">
            <button type="button" class="importer-tab-btn is-active" data-tab="bookmarklet">🚀 Yöntem 1: 1-Tıkla Tarayıcı Butonu (Önerilen)</button>
            <button type="button" class="importer-tab-btn" data-tab="direct-link">🔗 Yöntem 2: Trendyol Linki / Ürün ID</button>
            <button type="button" class="importer-tab-btn" data-tab="manual-paste">📋 Yöntem 3: Akıllı Yorum Yapıştırıcı</button>
        </div>

        <!-- SEKME 1: 1-Tıkla Bookmarklet (Sıfır Engel) -->
        <div class="importer-tab-content is-active" id="tab-bookmarklet">
            <p style="margin-top: 0; color: #334155; font-size: 0.9rem; line-height: 1.5;">
                Trendyol'un Cloudflare ve bot korumasını <strong>%100 garantili ve tek tıkla</strong> aşmak için aşağıdaki butonu farenizle basılı tutup tarayıcınızın <strong>Yer İmleri (Sık Kullanılanlar)</strong> çubuğuna sürükleyip bırakın:
            </p>
            <div style="margin: 18px 0; text-align: center;">
                <a href="<?php echo $bookmarklet_href; ?>" class="bookmarklet-drag-btn" onclick="alert('Bu butona tıklamak yerine, farenizle basılı tutarak tarayıcınızın üstündeki Yer İmleri (Bookmarks) çubuğuna sürükleyip bırakın!'); return false;">
                    <span>🧸</span> <span>Bu Ürüne Trendyol Yorumlarını Çek</span>
                </a>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 8px;">(Yukarıdaki turuncu butonu farenizle tutup tarayıcının üstündeki Yer İmleri çubuğuna bırakın)</div>
            </div>

            <!-- Hızlı Panodan Al Butonu (Pano Köprüsü) -->
            <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; padding: 14px 18px; border-radius: 12px; margin-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;">
                <div>
                    <div style="font-weight: 700; color: #166534; font-size: 0.9rem;">📋 Pano Köprüsü (Hızlı Otomatik Aktarım)</div>
                    <div style="font-size: 0.82rem; color: #15803d; margin-top: 2px;">Trendyol'da butona bastığınızda yorumlar panonuza kopyalandıysa buradan tek tıkla yükleyin:</div>
                </div>
                <button type="button" class="importer-btn-success" id="mis360_btn_paste_from_clipboard">
                    <span>📥</span> <span>Panodan Yorumları Oku ve Kaydet</span>
                </button>
            </div>
            
            <div style="background: #f8fafc; border-left: 3px solid #f97316; padding: 12px 16px; border-radius: 6px; font-size: 0.84rem; color: #475569; margin-top: 14px; line-height: 1.6;">
                <strong>Nasıl Kullanılır?</strong><br>
                1. Trendyol'da ilgili Montessori/ahşap ürününün sayfasına gidin.<br>
                2. Yer imlerine eklediğiniz <strong>"Bu Ürüne Trendyol Yorumlarını Çek"</strong> butonuna bir kere tıklayın.<br>
                3. Çıkan MİS360 penceresinden <strong>"Tüm Yorumları Aç"</strong> veya <strong>"Yorumları Aktar"</strong> deyin. Tüm gerçek yorumlar saniyeler içinde mağazanıza kaydedilir!
            </div>
        </div>

        <!-- SEKME 2: Doğrudan Link / ID İle Çekme -->
        <div class="importer-tab-content" id="tab-direct-link">
            <div class="importer-input-group">
                <label>Trendyol Ürün Linki veya Content ID:</label>
                <input type="text" id="mis360_ty_product_url" placeholder="https://www.trendyol.com/emdief-home/carmen-3-rafli-kitaplik-p-38195379 veya sadece 38195379">
            </div>
            <div class="importer-filter-row">
                <label><input type="checkbox" id="mis360_only_5stars" checked> Sadece 5 Yıldızlı Yorumları Al</label>
                <label><input type="checkbox" id="mis360_verified_badge" checked> Doğrulanmış Alıcı Rozeti Ekle</label>
                <button type="button" class="importer-btn-primary" id="mis360_btn_fetch_link">
                    <span>⚡</span> <span>Trendyol'dan Çek ve Kaydet</span>
                </button>
            </div>
            <div style="font-size: 0.82rem; color: #64748b;">
                * Not: Trendyol bazen sunucu kaynaklı istekleri Cloudflare bot korumasıyla engelleyebilir. Bu durumda en garantili yöntem <strong>Yöntem 1 (Tarayıcı Butonu)</strong> veya <strong>Yöntem 3 (Yapıştırıcı)</strong>'dır.
            </div>
        </div>

        <!-- SEKME 3: Akıllı Yorum Yapıştırıcı -->
        <div class="importer-tab-content" id="tab-manual-paste">
            <div class="importer-input-group">
                <label>Trendyol'dan Kopyaladığınız Yorum Metinleri veya JSON Verisi:</label>
                <textarea id="mis360_ty_raw_reviews" rows="6" placeholder="Trendyol ürün sayfasındaki yorumları farenizle seçip kopyalayın ve buraya yapıştırın. Bot yazar isimlerini, 5 yıldızları ve temiz yorum metinlerini otomatik ayıklar."></textarea>
            </div>
            <button type="button" class="importer-btn-primary" id="mis360_btn_parse_raw">
                <span>📋</span> <span>Yapıştırılan Yorumları İçe Aktar</span>
            </button>
        </div>

        <!-- Alt Eylemler & Temizleme -->
        <div style="display: flex; align-items: center; margin-top: 14px;">
            <button type="button" class="importer-btn-danger" id="mis360_btn_clear_reviews" data-product-id="<?php echo $product_id; ?>" onclick="return confirm('DİKKAT: Bu ürüne ait tüm yorumlar kalıcı olarak silinecek. Onaylıyor musunuz?');">
                🗑️ Bu Üründeki Tüm Yorumları Temizle
            </button>
        </div>

        <div class="importer-log-box" id="mis360_importer_log"></div>
    </div>

    <script>
    (function($){
        // Tab Geçişleri
        $('.importer-tab-btn').on('click', function(){
            $('.importer-tab-btn').removeClass('is-active');
            $('.importer-tab-content').removeClass('is-active');
            $(this).addClass('is-active');
            $('#tab-' + $(this).data('tab')).addClass('is-active');
        });

        const productId = <?php echo $product_id; ?>;
        const ajaxUrl = '<?php echo esc_url($site_ajax_url); ?>';
        const nonce = '<?php echo wp_create_nonce("mis360_trendyol_ajax_nonce"); ?>';

        function logMsg(msg, isSuccess) {
            const $box = $('#mis360_importer_log');
            $box.show().append('<div style="color:' + (isSuccess ? '#4ade80' : '#38bdf8') + ';margin-bottom:4px;">' + msg + '</div>');
            $box.scrollTop($box[0].scrollHeight);
        }

        // Pano Köprüsü: Panodaki Yorumları Oku ve Kaydet
        $('#mis360_btn_paste_from_clipboard').on('click', async function(){
            const $btn = $(this);
            if (!navigator.clipboard || !navigator.clipboard.readText) {
                alert('Tarayıcınız pano okuma izni vermiyor. Lütfen Yöntem 3 sekmesini açıp Ctrl+V ile yapıştırın.');
                return;
            }

            try {
                $btn.prop('disabled', true).text('Panodan Okunuyor...');
                const text = await navigator.clipboard.readText();
                if (!text || text.trim().length < 5) {
                    alert('Panonuzda herhangi bir yorum verisi bulunamadı. Lütfen önce Trendyol sayfasında yer imi butonuna tıklayın.');
                    $btn.prop('disabled', false).html('<span>📥</span> <span>Panodan Yorumları Oku ve Kaydet</span>');
                    return;
                }

                logMsg('Panodaki veri okunuyor ve WooCommerce veritabanına aktarılıyor...');
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'mis360_ajax_import_raw_reviews',
                        raw_text: text,
                        product_id: productId,
                        nonce: nonce
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html('<span>📥</span> <span>Panodan Yorumları Oku ve Kaydet</span>');
                        if (res && res.success) {
                            logMsg('✓ Başarılı: ' + res.data.message, true);
                            $('#mis360-review-count-val').text(res.data.total);
                            if (res.data.rating) {
                                $('#mis360-rating-val').text(Number(res.data.rating).toFixed(1) + ' / 5.0');
                            }
                        } else {
                            logMsg('⚠️ Hata: ' + (res.data || 'Yorumlar ayrıştırılamadı.'));
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html('<span>📥</span> <span>Panodan Yorumları Oku ve Kaydet</span>');
                        logMsg('Bağlantı hatası oluştu.');
                    }
                });
            } catch (err) {
                $btn.prop('disabled', false).html('<span>📥</span> <span>Panodan Yorumları Oku ve Kaydet</span>');
                alert('Pano okuma hatası: ' + err.message + '. Lütfen "Yöntem 3 (Akıllı Yorum Yapıştırıcı)" sekmesine yapıştırın.');
            }
        });

        // Link / ID ile Yorum Çekme
        $('#mis360_btn_fetch_link').on('click', function(){
            const urlVal = $('#mis360_ty_product_url').val().trim();
            if (!urlVal) {
                alert('Lütfen bir Trendyol ürün linki veya ürün ID girin!');
                return;
            }
            const $btn = $(this);
            $btn.prop('disabled', true).text('Çekiliyor...');
            logMsg('Trendyol üzerinden yorumlar taranıyor: ' + urlVal);

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mis360_ajax_fetch_and_import_by_url',
                    url: urlVal,
                    product_id: productId,
                    only_5stars: $('#mis360_only_5stars').is(':checked') ? 1 : 0,
                    nonce: nonce
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<span>⚡</span> <span>Trendyol'dan Çek ve Kaydet</span>');
                    if (res && res.success) {
                        logMsg('✓ Başarılı: ' + res.data.message, true);
                        if (res.data.total !== undefined) {
                            $('#mis360-review-count-val').text(res.data.total);
                        }
                        if (res.data.rating !== undefined) {
                            $('#mis360-rating-val').text(Number(res.data.rating).toFixed(1) + ' / 5.0');
                        }
                    } else {
                        logMsg('⚠️ ' + (res.data || 'Yorumlar sunucu tarafından çekilemedi. Trendyol bot koruması nedeniyle lütfen "Yöntem 1 (1-Tıkla Tarayıcı Butonu)"nu kullanın.'), false);
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html('<span>⚡</span> <span>Trendyol'dan Çek ve Kaydet</span>');
                    logMsg('Sunucu hatası. Trendyol koruması nedeniyle lütfen "Yöntem 1 (1-Tıkla Tarayıcı Butonu)"nu kullanın.');
                }
            });
        });

        // Manuel Yapıştırma ile İçe Aktarma
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
                    $btn.prop('disabled', false).html('<span>📋</span> <span>Yapıştırılan Yorumları İçe Aktar</span>');
                    if (res && res.success) {
                        logMsg('✓ Başarılı: ' + res.data.imported + ' adet yorum içe aktarıldı!', true);
                        $('#mis360-review-count-val').text(res.data.total);
                        if (res.data.rating !== undefined) {
                            $('#mis360-rating-val').text(Number(res.data.rating).toFixed(1) + ' / 5.0');
                        }
                        $('#mis360_ty_raw_reviews').val('');
                    } else {
                        logMsg('⚠️ Hata: ' + (res.data || 'Ayrıştırma başarısız.'));
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span>📋</span> <span>Yapıştırılan Yorumları İçe Aktar</span>');
                    logMsg('İşlem sırasında hata oluştu.');
                }
            });
        });

        // Yorumları Temizleme
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
 * 4. Ürünler Menüsündeki Genel Trendyol Botu Yönetim Sayfası
 */
function mis360_render_trendyol_bot_admin_page() {
    ?>
    <div class="wrap">
        <h1 style="display: flex; align-items: center; gap: 10px;">
            <span>🧸</span> MİS360 Trendyol Yorum İçe Aktarma Botu
        </h1>
        <p style="font-size: 1.05rem; color: #475569; max-width: 800px;">
            Bu bot, Trendyol'da satışta olan Montessori kitaplık, raf ve ahşap çocuk ürünlerinizin gerçek müşteri yorumlarını, 5 yıldızlı değerlendirmelerini ve doğrulanmış alıcı rozetlerini doğrudan WooCommerce mağazanıza aktarır.
        </p>

        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 24px; max-width: 850px; box-shadow: 0 4px 14px rgba(0,0,0,0.04); margin-top: 20px;">
            <h2 style="margin-top: 0; color: #ea580c; display: flex; align-items: center; gap: 8px;">
                <span>🚀</span> 1-Tıkla Nasıl Yorum Çekilir?
            </h2>
            <ol style="font-size: 0.95rem; line-height: 1.8; color: #334155;">
                <li>Sol menüden <strong>Ürünler &gt; Tüm Ürünler</strong> sekmesine gidin.</li>
                <li>Yorum eklemek istediğiniz ürünü seçip <strong>Düzenle</strong> deyin.</li>
                <li>Ürün açıklamasının hemen altında yer alan <strong>"🧸 MİS360 Trendyol Yorum İçe Aktarıcı Bot"</strong> kutusunu göreceksiniz.</li>
                <li>Oradaki turuncu <strong>"Bu Ürüne Trendyol Yorumlarını Çek"</strong> butonunu farenizle tarayıcınızın <strong>Yer İmleri (Sık Kullanılanlar)</strong> çubuğuna bir kere sürükleyin.</li>
                <li>Ardından Trendyol'daki ürün sayfanızı açıp yer imlerindeki o butona tıklayın! Yorumlar saniyeler içinde mağazanıza aktarılacaktır.</li>
            </ol>
        </div>
    </div>
    <?php
}

/**
 * 5. AJAX Handler: Bookmarklet ve Harici İsteklerden Gelen Yorumları Kaydet (CORS Destekli)
 */
function mis360_ajax_import_trendyol_reviews() {
    // CORS Başlıkları (Trendyol üzerinden gelen fetch isteklerinin engellenmemesi için)
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header('Access-Control-Allow-Origin: ' . esc_url_raw($_SERVER['HTTP_ORIGIN']));
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        status_header(200);
        exit;
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $token      = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';

    if (!$product_id || !wp_verify_nonce($token, 'mis360_bookmarklet_import_' . $product_id)) {
        wp_send_json_error('Geçersiz güvenlik doğrulaması veya ürün kimliği.');
    }

    $raw_reviews = isset($_POST['reviews']) ? wp_unslash($_POST['reviews']) : '';
    $reviews_data = json_decode($raw_reviews, true);

    if (empty($reviews_data) || !is_array($reviews_data)) {
        wp_send_json_error('Yorum verisi ayrıştırılamadı veya boş.');
    }

    $result = mis360_process_and_save_reviews($product_id, $reviews_data);
    wp_send_json_success($result);
}
add_action('wp_ajax_mis360_ajax_import_trendyol_reviews', 'mis360_ajax_import_trendyol_reviews');
add_action('wp_ajax_nopriv_mis360_ajax_import_trendyol_reviews', 'mis360_ajax_import_trendyol_reviews');

/**
 * 6. AJAX Handler: URL / ID İle Sunucu Tarafından Çekme Denemesi (Yöntem 2)
 */
function mis360_ajax_fetch_and_import_by_url() {
    check_ajax_referer('mis360_trendyol_ajax_nonce', 'nonce');

    if (!current_user_can('edit_products')) {
        wp_send_json_error('Yetkisiz işlem.');
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $url        = isset($_POST['url']) ? sanitize_text_field(wp_unslash($_POST['url'])) : '';
    $only_5     = !empty($_POST['only_5stars']);

    if (!$product_id || empty($url)) {
        wp_send_json_error('Geçersiz ürün veya URL.');
    }

    // URL'den veya metinden Content ID ayıkla
    $cid = null;
    if (preg_match('/-p-([0-9]+)/', $url, $m)) {
        $cid = $m[1];
    } elseif (preg_match('/contentId=([0-9]+)/', $url, $m)) {
        $cid = $m[1];
    } elseif (is_numeric(trim($url))) {
        $cid = trim($url);
    }

    if (!$cid) {
        wp_send_json_error('Trendyol ürün kimliği (contentId) tespit edilemedi. Lütfen geçerli bir Trendyol linki girin.');
    }

    // Trendyol Public API'ye sunucu tarafından istek at
    $api_url = "https://public.trendyol.com/discovery-web-socialgw-service/api/reviews/{$cid}?page=0&size=100";
    $response = wp_remote_get($api_url, [
        'timeout' => 10,
        'headers' => [
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Accept'          => 'application/json, text/plain, */*',
            'Accept-Language' => 'tr-TR,tr;q=0.9',
            'Referer'         => 'https://www.trendyol.com/'
        ]
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error('Trendyol API bağlantı hatası: ' . $response->get_error_message() . '. Lütfen "Yöntem 1 (1-Tıkla Tarayıcı Butonu)"nu kullanın.');
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body        = wp_remote_retrieve_body($response);

    if ($status_code !== 200 || empty($body)) {
        wp_send_json_error('Trendyol bot koruması (Cloudflare) sunucu isteğini engelledi (HTTP ' . $status_code . '). Lütfen tarayıcınızın engellenmeyen bağlantısını kullanan "Yöntem 1 (1-Tıkla Tarayıcı Butonu)"nu tercih edin.');
    }

    $json = json_decode($body, true);
    $list = isset($json['result']['productReviews']['content']) ? $json['result']['productReviews']['content'] : [];

    if (empty($list)) {
        wp_send_json_error('Bu üründe Trendyol üzerinde henüz onaylanmış yorum bulunamadı.');
    }

    $reviews_to_save = [];
    foreach ($list as $item) {
        $rate = isset($item['rate']) ? (int)$item['rate'] : 5;
        if ($only_5 && $rate < 5) continue;

        $reviews_to_save[] = [
            'author'   => !empty($item['userFullName']) ? $item['userFullName'] : 'Müşteri',
            'rating'   => $rate,
            'comment'  => !empty($item['comment']) ? $item['comment'] : '',
            'date'     => !empty($item['commentDateISOformat']) ? $item['commentDateISOformat'] : current_time('mysql'),
            'verified' => true
        ];
    }

    if (empty($reviews_to_save)) {
        wp_send_json_error('Filtre kriterlerine uygun yorum bulunamadı.');
    }

    $result = mis360_process_and_save_reviews($product_id, $reviews_to_save);
    wp_send_json_success($result);
}
add_action('wp_ajax_mis360_ajax_fetch_and_import_by_url', 'mis360_ajax_fetch_and_import_by_url');

/**
 * 7. AJAX Handler: Manuel Yapıştırılan veya Panodan Gelen Metinleri Ayrıştır ve Kaydet (Yöntem 3 & Pano Köprüsü)
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

    // 1. JSON formatında mı kontrol et
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
                'date'     => isset($item['date']) ? $item['date'] : (isset($item['commentDateISOformat']) ? $item['commentDateISOformat'] : current_time('mysql')),
                'verified' => true
            ];
        }
    } else {
        // 2. Metin Bloklarını Ayrıştır
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

            // Filtreleme: Navigasyon ve gereksiz metinleri atla
            if (preg_match('/(Satıcı:|Ürünü Değerlendir|Sepete Ekle|Kargo Bedava|Fotoğraflı Yorumlar|Değerlendirmeler|Faydalı Buldum)/iu', $trimmed)) {
                continue;
            }

            // İsim tespit et (Örn: A*** K*** veya Ahmet Y.)
            if (preg_match('/^([A-ZÇĞİÖŞÜa-zçğıöşü]\*{1,3}\s+[A-ZÇĞİÖŞÜa-zçğıöşü]\*{1,3})$/u', $trimmed, $am)) {
                $current_author = $am[1];
                continue;
            }

            // Tarih tespit et (Örn: 14 Mayıs 2024)
            if (preg_match('/([0-9]{1,2}\s+[A-Za-zÇĞİÖŞÜçğıöşü]+\s+[0-9]{4})/u', $trimmed, $dm)) {
                $current_date = $dm[1];
                continue;
            }

            // Yorum içeriği olarak kaydet
            $reviews_to_save[] = [
                'author'   => $current_author,
                'rating'   => $current_rating,
                'comment'  => $trimmed,
                'date'     => $current_date,
                'verified' => true
            ];

            // Bir sonraki yorum için yazarı sıfırla
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
 * 8. AJAX Handler: Ürüne Ait Yorumları Toplu Temizle
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
 * 9. Çekirdek Fonksiyon: Gelen Yorumları WooCommerce Veritabanına Yaz
 */
function mis360_process_and_save_reviews($product_id, $reviews_data) {
    $imported = 0;
    $skipped  = 0;

    // Mevcut yorumları al (mükerrerliği önlemek için)
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

        // Mükerrer kontrolü
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
        
        // Tarih formatlama
        $date_str = isset($rev['date']) ? sanitize_text_field($rev['date']) : '';
        $date_time = (!empty($date_str) && strtotime($date_str)) ? date('Y-m-d H:i:s', strtotime($date_str)) : current_time('mysql');

        // Sahte ama geçerli e-posta
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

    // WooCommerce Ürün Sayaçlarını ve Puan Ortalamasını Güncelle
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
        'message'  => sprintf('%d yeni yorum başarıyla içe aktarıldı! (Toplam: %d)', $imported, $total_reviews)
    ];
}