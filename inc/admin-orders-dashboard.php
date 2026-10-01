<?php
/**
 * Emdief Home - Admin Canlı Sipariş Yönetim Merkezi & Giriş Görünümü
 *
 * WP-Admin Başlangıç (Dashboard) ekranında doğrudan canlı siparişleri,
 * metrikleri, müşteri iletişim aksiyonlarını ve hızlı durum yönetimini sunar.
 *
 * @package Mis360-Mobilya
 * @version 1.9.85
 * @author Serkan AKKAYA & MİS360
 */

defined('ABSPATH') || exit;

class Emdief_Admin_Orders_Dashboard {

    public function __construct() {
        // Dashboard Widget Ekleme
        add_action('wp_dashboard_setup', [$this, 'register_dashboard_widget']);

        // Admin Başlangıç Sayfası Üst Paneli
        add_action('all_admin_notices', [$this, 'render_top_dashboard_view']);

        // Admin Giriş Yönlendirmesi (Opsiyonel: Doğrudan Siparişler Sayfası)
        add_filter('login_redirect', [$this, 'handle_admin_login_redirect'], 20, 3);

        // AJAX İşlemleri (Hızlı Durum Değiştirme ve Yönlendirme Ayarı)
        add_action('wp_ajax_emdief_update_order_status', [$this, 'ajax_update_order_status']);
        add_action('wp_ajax_emdief_toggle_login_redirect', [$this, 'ajax_toggle_login_redirect']);

        // Admin CSS & JS
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * WooCommerce Siparişler Yönetim Sayfası URL'i (HPOS & Klasik Uyumlu)
     */
    public static function get_orders_url() {
        if (class_exists('Automattic\WooCommerce\Utilities\OrderUtil') && 
            \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
            return admin_url('admin.php?page=wc-orders');
        }
        return admin_url('edit.php?post_type=shop_order');
    }

    /**
     * Dashboard Widget Kaydı
     */
    public function register_dashboard_widget() {
        if (!current_user_can('manage_woocommerce') && !current_user_can('edit_shop_orders')) {
            return;
        }

        wp_add_dashboard_widget(
            'emdief_admin_orders_widget',
            '📦 Emdief Home - Canlı Sipariş Yönetim Merkezi',
            [$this, 'render_widget_content'],
            null,
            null,
            'normal',
            'high'
        );
    }

    /**
     * Admin Paneli Girişinde Üst Karşılama Ekranı (wp-admin/index.php)
     */
    public function render_top_dashboard_view() {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'dashboard') {
            return;
        }

        if (!current_user_can('manage_woocommerce') && !current_user_can('edit_shop_orders')) {
            return;
        }

        // Dashboard'da üst panel olarak doğrudan göster
        ?>
        <div id="emdief-top-orders-panel" class="emdief-orders-hero-wrap">
            <?php $this->render_widget_content(true); ?>
        </div>
        <?php
    }

    /**
     * Admin oturum açtığında doğrudan Siparişlere yönlendirilsin mi kontrolü
     */
    public function handle_admin_login_redirect($redirect_to, $request, $user) {
        if (!is_wp_error($user) && is_a($user, 'WP_User')) {
            $is_admin = in_array('administrator', (array) $user->roles, true) || 
                         in_array('shop_manager', (array) $user->roles, true);

            if ($is_admin) {
                $redirect_pref = get_option('emdief_login_redirect_to_orders', 'no');
                if ($redirect_pref === 'yes') {
                    // Kullanıcı özel bir admin sayfasına gitmeye çalışmıyorsa siparişlere yönlendir
                    if (empty($request) || strpos($request, 'wp-login.php') !== false || $request === admin_url() || $request === admin_url('index.php')) {
                        return self::get_orders_url();
                    }
                }
            }
        }
        return $redirect_to;
    }

    /**
     * Canlı Sipariş İçeriğini Render Et
     */
    public function render_widget_content($is_hero = false) {
        if (!class_exists('WooCommerce')) {
            echo '<p style="padding:15px; color:#64748b;">WooCommerce aktif değil.</p>';
            return;
        }

        // Metrik Verilerini Çek
        $today_start = date('Y-m-d 00:00:00');
        
        $today_orders = wc_get_orders([
            'limit'        => -1,
            'date_created' => '>=' . $today_start,
            'status'       => ['processing', 'completed', 'on-hold', 'pending'],
            'return'       => 'ids',
        ]);
        $today_count = is_array($today_orders) ? count($today_orders) : 0;

        $today_total = 0;
        if (!empty($today_orders)) {
            foreach ($today_orders as $ord_id) {
                $ord = wc_get_order($ord_id);
                if ($ord) {
                    $today_total += (float) $ord->get_total();
                }
            }
        }

        // Bekleyen / Hazırlanan siparişler
        $processing_count = wc_orders_count('processing');
        $on_hold_count    = wc_orders_count('on-hold');
        $completed_count  = wc_orders_count('completed');

        // Son 10 Siparişi Çek
        $recent_orders = wc_get_orders([
            'limit'   => 12,
            'orderby' => 'date',
            'order'   => 'DESC',
            'status'  => ['processing', 'on-hold', 'pending', 'completed', 'cancelled'],
        ]);

        $redirect_pref = get_option('emdief_login_redirect_to_orders', 'no');
        $orders_url    = self::get_orders_url();
        ?>
        <div class="emdief-orders-dashboard <?php echo $is_hero ? 'emdief-is-hero' : 'emdief-is-widget'; ?>">

            <!-- ÜST BAR: Başlık, Hızlı Aksiyonlar ve Giriş Yönlendirme Switch'i -->
            <div class="emd-dash-header">
                <div class="emd-dash-title-group">
                    <div class="emd-dash-badge">Canlı Takip</div>
                    <h2 class="emd-dash-title">
                        <span>📦 Emdief Home</span> Sipariş Yönetim Merkezi
                    </h2>
                    <span class="emd-dash-subtitle">Son gelen siparişleri canlı izleyin, müşterilerle tek tıkla WhatsApp veya telefonla iletişime geçin.</span>
                </div>
                
                <div class="emd-dash-controls">
                    <label class="emd-toggle-label" title="Giriş yapıldığında başlangıç yerine doğrudan WooCommerce Siparişler listesini açar">
                        <input type="checkbox" id="emdief-toggle-login-redirect" <?php checked($redirect_pref, 'yes'); ?> />
                        <span class="emd-toggle-slider"></span>
                        <span class="emd-toggle-text">Girişte Doğrudan Siparişleri Aç</span>
                    </label>

                    <a href="<?php echo esc_url($orders_url); ?>" class="emd-btn emd-btn-primary">
                        <span>Tüm Siparişleri Gör (<?php echo esc_html(wc_orders_count('all')); ?>)</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- METRİK KARTLARI -->
            <div class="emd-stat-grid">
                <div class="emd-stat-card emd-stat-today">
                    <div class="emd-stat-icon">🌟</div>
                    <div class="emd-stat-body">
                        <span class="emd-stat-title">Bugünün Siparişleri</span>
                        <div class="emd-stat-val">
                            <strong><?php echo esc_html($today_count); ?></strong> Sipariş
                            <span class="emd-stat-subval"><?php echo wc_price($today_total); ?></span>
                        </div>
                    </div>
                </div>

                <div class="emd-stat-card emd-stat-processing">
                    <div class="emd-stat-icon">⏳</div>
                    <div class="emd-stat-body">
                        <span class="emd-stat-title">Hazırlanan / Onaylı</span>
                        <div class="emd-stat-val">
                            <strong><?php echo esc_html($processing_count); ?></strong> Adet
                            <a href="<?php echo esc_url(add_query_arg(['status' => 'wc-processing'], $orders_url)); ?>" class="emd-stat-link">Filtrele &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="emd-stat-card emd-stat-onhold">
                    <div class="emd-stat-icon">🏦</div>
                    <div class="emd-stat-body">
                        <span class="emd-stat-title">Havale / EFT Bekleyen</span>
                        <div class="emd-stat-val">
                            <strong><?php echo esc_html($on_hold_count); ?></strong> Adet
                            <a href="<?php echo esc_url(add_query_arg(['status' => 'wc-on-hold'], $orders_url)); ?>" class="emd-stat-link">İncele &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="emd-stat-card emd-stat-completed">
                    <div class="emd-stat-icon">🚚</div>
                    <div class="emd-stat-body">
                        <span class="emd-stat-title">Tamamlanan / Kargoda</span>
                        <div class="emd-stat-val">
                            <strong><?php echo esc_html($completed_count); ?></strong> Adet
                            <a href="<?php echo esc_url(add_query_arg(['status' => 'wc-completed'], $orders_url)); ?>" class="emd-stat-link">Görüntüle &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CANLI SİPARİŞLER TABLOSU -->
            <div class="emd-orders-table-wrapper">
                <?php if (empty($recent_orders)) : ?>
                    <div class="emd-no-orders">
                        <div class="emd-no-icon">🛒</div>
                        <h3>Henüz sipariş bulunmuyor</h3>
                        <p>Yeni bir sipariş oluşturulduğunda bu alanda anında listelenecektir.</p>
                    </div>
                <?php else : ?>
                    <table class="emd-orders-table">
                        <thead>
                            <tr>
                                <th style="width: 130px;">Sipariş</th>
                                <th style="width: 190px;">Müşteri Bilgisi</th>
                                <th>Alınan Ürünler</th>
                                <th style="width: 130px;">Tutar &amp; Ödeme</th>
                                <th style="width: 140px;">Durum</th>
                                <th style="width: 190px; text-align: right;">Hızlı Aksiyonlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order) : 
                                $order_id    = $order->get_id();
                                $status      = $order->get_status();
                                $status_name = wc_get_order_status_name($status);
                                $total_fmt   = wc_price($order->get_total());
                                $pay_method  = $order->get_payment_method_title() ?: 'Banka Havalesi / FAST';
                                $created_at  = $order->get_date_created();
                                $time_diff   = human_time_diff($created_at->getTimestamp(), current_time('timestamp')) . ' önce';

                                $customer_name  = trim($order->get_formatted_billing_full_name());
                                if (empty($customer_name)) {
                                    $customer_name = 'Misafir Müşteri #' . $order_id;
                                }
                                $customer_phone = $order->get_billing_phone();
                                $customer_city  = $order->get_billing_city();
                                $customer_state = $order->get_billing_state();
                                $location       = trim($customer_city . ($customer_state ? ' / ' . $customer_state : ''));

                                $edit_url = $order->get_edit_order_url();

                                // WhatsApp Mesajı Oluştur
                                $clean_phone = preg_replace('/[^0-9]/', '', $customer_phone);
                                if (substr($clean_phone, 0, 1) === '0') {
                                    $clean_phone = '90' . substr($clean_phone, 1);
                                } elseif (strlen($clean_phone) === 10) {
                                    $clean_phone = '90' . $clean_phone;
                                }
                                
                                $wa_msg = sprintf(
                                    "Merhaba Sayın %s, Emdief Home'dan vermiş olduğunuz #%s numaralı siparişiniz için yazıyorum. Ahşap Montessori ürününüz özenle hazırlanmaktadır. Bilgi almak veya iletmek istediğiniz bir not var mıdır?",
                                    $customer_name,
                                    $order_id
                                );
                                $wa_url = 'https://wa.me/' . $clean_phone . '?text=' . rawurlencode($wa_msg);
                            ?>
                                <tr class="emd-order-row emd-status-<?php echo esc_attr($status); ?>" data-order-id="<?php echo esc_attr($order_id); ?>">
                                    
                                    <!-- Sipariş No & Tarih -->
                                    <td class="emd-col-order">
                                        <a href="<?php echo esc_url($edit_url); ?>" class="emd-order-num">
                                            #<?php echo esc_html($order_id); ?>
                                        </a>
                                        <div class="emd-order-time" title="<?php echo esc_attr($created_at->date_i18n('d.m.Y H:i')); ?>">
                                            <?php echo esc_html($time_diff); ?>
                                        </div>
                                    </td>

                                    <!-- Müşteri Bilgisi -->
                                    <td class="emd-col-customer">
                                        <div class="emd-cust-name" title="<?php echo esc_attr($customer_name); ?>">
                                            <?php echo esc_html($customer_name); ?>
                                        </div>
                                        <?php if (!empty($location)) : ?>
                                            <div class="emd-cust-loc">📍 <?php echo esc_html($location); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($customer_phone)) : ?>
                                            <div class="emd-cust-phone">
                                                <a href="tel:<?php echo esc_attr($customer_phone); ?>">📞 <?php echo esc_html($customer_phone); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Alınan Ürünler -->
                                    <td class="emd-col-items">
                                        <div class="emd-order-items-list">
                                            <?php 
                                            $items = $order->get_items();
                                            $item_count = count($items);
                                            $display_items = array_slice($items, 0, 3);
                                            foreach ($display_items as $item) : 
                                                $prod = $item->get_product();
                                                $img_url = $prod && $prod->get_image_id() ? wp_get_attachment_image_url($prod->get_image_id(), [48, 48]) : wc_placeholder_img_src();
                                            ?>
                                                <div class="emd-order-item-pill" title="<?php echo esc_attr($item->get_name()); ?>">
                                                    <img src="<?php echo esc_url($img_url); ?>" alt="" class="emd-item-thumb" />
                                                    <span class="emd-item-name"><?php echo esc_html($item->get_name()); ?></span>
                                                    <span class="emd-item-qty">x<?php echo (int) $item->get_quantity(); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if ($item_count > 3) : ?>
                                                <span class="emd-more-items">+<?php echo ($item_count - 3); ?> ürün daha</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Tutar & Ödeme Yöntemi -->
                                    <td class="emd-col-total">
                                        <div class="emd-total-amount"><?php echo $total_fmt; ?></div>
                                        <div class="emd-payment-title"><?php echo esc_html($pay_method); ?></div>
                                    </td>

                                    <!-- Durum Rozeti & Hızlı Değiştirici -->
                                    <td class="emd-col-status">
                                        <select class="emd-status-select" data-order-id="<?php echo esc_attr($order_id); ?>">
                                            <option value="processing" <?php selected($status, 'processing'); ?>>⏳ Hazırlanıyor</option>
                                            <option value="on-hold" <?php selected($status, 'on-hold'); ?>>🏦 Ödeme Bekliyor</option>
                                            <option value="completed" <?php selected($status, 'completed'); ?>>✅ Tamamlandı</option>
                                            <option value="cancelled" <?php selected($status, 'cancelled'); ?>>❌ İptal Edildi</option>
                                            <option value="refunded" <?php selected($status, 'refunded'); ?>>↩️ İade Edildi</option>
                                            <option value="pending" <?php selected($status, 'pending'); ?>>⏱️ Beklemede</option>
                                        </select>
                                        <span class="emd-status-saved-msg">✓ Kaydedildi</span>
                                    </td>

                                    <!-- Hızlı Aksiyon Butonları -->
                                    <td class="emd-col-actions">
                                        <div class="emd-action-buttons">
                                            <?php if (!empty($clean_phone)) : ?>
                                                <a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer" class="emd-act-btn emd-act-wa" title="Müşteriye WhatsApp'tan Mesaj At">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.17 8.17 0 012.41 5.82c0 4.54-3.7 8.24-8.24 8.24-1.45 0-2.87-.38-4.12-1.1l-.3-.18-3.12.82.83-3.04-.19-.31a8.21 8.21 0 01-1.26-4.43c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.24-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.38-.43.12-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.43.06-.66.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.13.17 1.73 2.65 4.2 3.71.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.13-.23-.2-.48-.32z"/></svg>
                                                    <span>WhatsApp</span>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (!empty($customer_phone)) : ?>
                                                <a href="tel:<?php echo esc_attr($customer_phone); ?>" class="emd-act-btn emd-act-call" title="Müşteriyi Ara">
                                                    📞
                                                </a>
                                            <?php endif; ?>

                                            <a href="<?php echo esc_url($edit_url); ?>" class="emd-act-btn emd-act-edit" title="Siparişi İncele / Düzenle">
                                                👁️ <span>İncele</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- ALT BAR -->
            <div class="emd-dash-footer">
                <span>🔄 Canlı WooCommerce veritabanı ile senkronizedir.</span>
                <a href="<?php echo esc_url($orders_url); ?>" class="emd-view-all-link">Tüm Sipariş Listesine Git &rarr;</a>
            </div>

        </div>
        <?php
    }

    /**
     * AJAX: Sipariş Durumu Hızlı Değiştirme
     */
    public function ajax_update_order_status() {
        check_ajax_referer('emdief_orders_dash_nonce', 'security');

        if (!current_user_can('edit_shop_orders')) {
            wp_send_json_error(['message' => 'Yetkiniz bulunmuyor.']);
        }

        $order_id = absint($_POST['order_id'] ?? 0);
        $status   = sanitize_key($_POST['status'] ?? '');

        if (!$order_id || empty($status)) {
            wp_send_json_error(['message' => 'Geçersiz parametreler.']);
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            wp_send_json_error(['message' => 'Sipariş bulunamadı.']);
        }

        $order->update_status($status, 'Admin Canlı Sipariş Paneli üzerinden güncellendi.');
        wp_send_json_success(['message' => 'Sipariş durumu güncellendi.']);
    }

    /**
     * AJAX: Girişte Doğrudan Siparişlere Yönlendirme Tercihi
     */
    public function ajax_toggle_login_redirect() {
        check_ajax_referer('emdief_orders_dash_nonce', 'security');

        if (!current_user_can('manage_options') && !current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Yetkiniz bulunmuyor.']);
        }

        $enabled = isset($_POST['enabled']) && $_POST['enabled'] === 'yes' ? 'yes' : 'no';
        update_option('emdief_login_redirect_to_orders', $enabled);

        wp_send_json_success([
            'enabled' => $enabled,
            'message' => $enabled === 'yes' 
                ? 'Girişte doğrudan WooCommerce Siparişler sayfasına yönlendirileceksiniz.' 
                : 'Girişte standart Başlangıç paneline yönlendirileceksiniz.'
        ]);
    }

    /**
     * Admin CSS ve JS
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'index.php') {
            return;
        }

        ?>
        <style type="text/css">
            /* Emdief Home Admin Canlı Sipariş Paneli Stilleri */
            .emdief-orders-hero-wrap {
                margin: 20px 0 25px 0;
                clear: both;
            }
            .emdief-orders-dashboard {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
                overflow: hidden;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
            
            /* Header */
            .emd-dash-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                padding: 24px 28px;
                background: linear-gradient(135deg, #fdfbf7 0%, #fffbf0 100%);
                border-bottom: 1px solid #f1f5f9;
            }
            .emd-dash-badge {
                display: inline-block;
                background: #ea580c;
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                padding: 3px 10px;
                border-radius: 20px;
                margin-bottom: 6px;
            }
            .emd-dash-title {
                font-size: 22px !important;
                font-weight: 800 !important;
                color: #0f172a !important;
                margin: 0 !important;
                padding: 0 !important;
                line-height: 1.2;
            }
            .emd-dash-title span {
                color: #ea580c;
            }
            .emd-dash-subtitle {
                display: block;
                font-size: 13px;
                color: #64748b;
                margin-top: 4px;
            }
            .emd-dash-controls {
                display: flex;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
            }
            
            /* Toggle Switch */
            .emd-toggle-label {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                user-select: none;
                background: #ffffff;
                border: 1px solid #e2e8f0;
                padding: 7px 14px;
                border-radius: 30px;
                font-size: 12.5px;
                font-weight: 600;
                color: #334155;
                transition: all 0.2s ease;
            }
            .emd-toggle-label:hover {
                border-color: #cbd5e1;
                background: #f8fafc;
            }
            .emd-toggle-label input {
                cursor: pointer;
                margin: 0;
            }

            .emd-btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #ea580c;
                color: #ffffff !important;
                font-size: 13px;
                font-weight: 700;
                padding: 9px 18px;
                border-radius: 8px;
                text-decoration: none !important;
                box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);
                transition: all 0.2s ease;
            }
            .emd-btn-primary:hover {
                background: #c2410c;
                transform: translateY(-1px);
            }

            /* Stat Cards Grid */
            .emd-stat-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
                padding: 20px 28px;
                background: #fdfbf7;
                border-bottom: 1px solid #f1f5f9;
            }
            .emd-stat-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 16px 18px;
                display: flex;
                align-items: center;
                gap: 14px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .emd-stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
            }
            .emd-stat-icon {
                font-size: 26px;
                width: 46px;
                height: 46px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
            }
            .emd-stat-today .emd-stat-icon { background: #fef3c7; }
            .emd-stat-processing .emd-stat-icon { background: #ffedd5; }
            .emd-stat-onhold .emd-stat-icon { background: #e0f2fe; }
            .emd-stat-completed .emd-stat-icon { background: #dcfce7; }

            .emd-stat-title {
                display: block;
                font-size: 12px;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }
            .emd-stat-val {
                font-size: 18px;
                font-weight: 800;
                color: #0f172a;
                margin-top: 3px;
                display: flex;
                align-items: baseline;
                gap: 6px;
                flex-wrap: wrap;
            }
            .emd-stat-subval {
                font-size: 13px;
                font-weight: 700;
                color: #ea580c;
                background: #fff7ed;
                padding: 2px 8px;
                border-radius: 6px;
            }
            .emd-stat-link {
                font-size: 11.5px;
                color: #2563eb;
                text-decoration: none;
                margin-left: auto;
                font-weight: 600;
            }
            .emd-stat-link:hover { text-decoration: underline; }

            /* Orders Table */
            .emd-orders-table-wrapper {
                overflow-x: auto;
            }
            .emd-orders-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
                font-size: 13px;
            }
            .emd-orders-table th {
                background: #f8fafc;
                color: #475569;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                padding: 12px 18px;
                border-bottom: 1px solid #e2e8f0;
            }
            .emd-orders-table td {
                padding: 14px 18px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: middle;
            }
            .emd-order-row:hover {
                background: #fffdfa;
            }

            .emd-order-num {
                font-weight: 800;
                color: #ea580c;
                font-size: 14px;
                text-decoration: none;
            }
            .emd-order-num:hover {
                text-decoration: underline;
            }
            .emd-order-time {
                font-size: 11.5px;
                color: #94a3b8;
                margin-top: 3px;
            }

            .emd-cust-name {
                font-weight: 700;
                color: #1e293b;
                font-size: 13.5px;
            }
            .emd-cust-loc {
                font-size: 11.5px;
                color: #64748b;
                margin-top: 2px;
            }
            .emd-cust-phone a {
                font-size: 12px;
                color: #0284c7;
                text-decoration: none;
                font-weight: 600;
            }
            .emd-cust-phone a:hover {
                text-decoration: underline;
            }

            /* Product Items */
            .emd-order-items-list {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            .emd-order-item-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                padding: 4px 8px;
                border-radius: 6px;
                max-width: 320px;
            }
            .emd-item-thumb {
                width: 24px;
                height: 24px;
                object-fit: cover;
                border-radius: 4px;
                background: #ffffff;
            }
            .emd-item-name {
                font-size: 12px;
                color: #334155;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 220px;
            }
            .emd-item-qty {
                font-size: 11px;
                font-weight: 700;
                color: #ea580c;
                background: #fff7ed;
                padding: 1px 5px;
                border-radius: 4px;
            }
            .emd-more-items {
                font-size: 11px;
                color: #94a3b8;
                font-weight: 600;
                margin-left: 4px;
            }

            /* Total */
            .emd-total-amount {
                font-size: 15px;
                font-weight: 800;
                color: #0f172a;
            }
            .emd-payment-title {
                font-size: 11px;
                color: #64748b;
                margin-top: 2px;
            }

            /* Status Select */
            .emd-status-select {
                padding: 5px 10px !important;
                border-radius: 8px !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                border: 1px solid #cbd5e1 !important;
                background: #ffffff !important;
                cursor: pointer;
            }
            .emd-status-saved-msg {
                display: none;
                font-size: 11px;
                color: #16a34a;
                font-weight: 700;
                margin-left: 6px;
            }

            /* Action Buttons */
            .emd-action-buttons {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 6px;
            }
            .emd-act-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 6px 11px;
                border-radius: 7px;
                font-size: 12px;
                font-weight: 600;
                text-decoration: none !important;
                transition: all 0.15s ease;
            }
            .emd-act-wa {
                background: #25d366;
                color: #ffffff !important;
            }
            .emd-act-wa:hover {
                background: #1eb956;
                transform: translateY(-1px);
            }
            .emd-act-call {
                background: #f1f5f9;
                color: #334155 !important;
                padding: 6px 9px;
            }
            .emd-act-call:hover {
                background: #e2e8f0;
            }
            .emd-act-edit {
                background: #f8fafc;
                border: 1px solid #cbd5e1;
                color: #334155 !important;
            }
            .emd-act-edit:hover {
                background: #ffffff;
                border-color: #ea580c;
                color: #ea580c !important;
            }

            /* Empty state */
            .emd-no-orders {
                text-align: center;
                padding: 40px 20px;
                color: #64748b;
            }
            .emd-no-icon {
                font-size: 38px;
                margin-bottom: 10px;
            }

            /* Footer */
            .emd-dash-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 28px;
                background: #f8fafc;
                border-top: 1px solid #f1f5f9;
                font-size: 12px;
                color: #64748b;
            }
            .emd-view-all-link {
                color: #ea580c;
                font-weight: 700;
                text-decoration: none;
            }
            .emd-view-all-link:hover {
                text-decoration: underline;
            }

            /* Widget mode inside postbox */
            #emdief_admin_orders_widget .inside {
                padding: 0 !important;
                margin: 0 !important;
            }
            #emdief_admin_orders_widget .hndle {
                display: none !important;
            }
            #emdief_admin_orders_widget {
                border-radius: 14px !important;
                overflow: hidden !important;
            }

            @media (max-width: 782px) {
                .emd-dash-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .emd-dash-controls {
                    width: 100%;
                    justify-content: space-between;
                }
            }
        </style>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var ajaxUrl = <?php echo json_encode(admin_url('admin-ajax.php')); ?>;
                var nonce   = <?php echo json_encode(wp_create_nonce('emdief_orders_dash_nonce')); ?>;

                // 1. Durum Değiştirme AJAX
                $('.emd-status-select').on('change', function() {
                    var $select = $(this);
                    var orderId = $select.data('order-id');
                    var newStatus = $select.val();
                    var $saved = $select.siblings('.emd-status-saved-msg');

                    $select.prop('disabled', true);

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'emdief_update_order_status',
                            order_id: orderId,
                            status: newStatus,
                            security: nonce
                        },
                        success: function(res) {
                            $select.prop('disabled', false);
                            if (res.success) {
                                $saved.stop(true, true).fadeIn(200).delay(2000).fadeOut(400);
                            } else {
                                alert(res.data && res.data.message ? res.data.message : 'Güncelleme başarısız oldu.');
                            }
                        },
                        error: function() {
                            $select.prop('disabled', false);
                            alert('Sunucuyla bağlantı kurulamadı.');
                        }
                    });
                });

                // 2. Girişte Doğrudan Siparişleri Aç Toggle AJAX
                $('#emdief-toggle-login-redirect').on('change', function() {
                    var isChecked = $(this).is(':checked') ? 'yes' : 'no';

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'emdief_toggle_login_redirect',
                            enabled: isChecked,
                            security: nonce
                        },
                        success: function(res) {
                            if (res.success && res.data && res.data.message) {
                                // Bildirim mesajı
                            }
                        }
                    });
                });
            });
        </script>
        <?php
    }
}

// Başlat
if (is_admin()) {
    new Emdief_Admin_Orders_Dashboard();
}
