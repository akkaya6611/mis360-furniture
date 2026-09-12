<?php
/**
 * Template Name: Emdief Hesabım (My Account)
 * Template Post Type: page
 *
 * @package Mis360-Mobilya
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
mis360_breadcrumbs();

$user_id      = get_current_user_id();
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
$display_name = $is_logged_in ? ($current_user->display_name ?: $current_user->user_login) : 'Değerli Misafirimiz';
$user_email   = $is_logged_in ? $current_user->user_email : '';

// Gerçek WooCommerce Verileri
$customer_orders = [];
$total_orders    = 0;
$latest_order    = null;
$total_spent     = 0.0;

if (class_exists('WooCommerce') && $is_logged_in && $user_id) {
    $customer_orders = wc_get_orders([
        'customer_id' => $user_id,
        'limit'       => 20,
        'return'      => 'objects',
        'orderby'     => 'date',
        'order'       => 'DESC',
    ]);
    $total_orders = is_array($customer_orders) ? count($customer_orders) : 0;
    $latest_order = !empty($customer_orders) ? $customer_orders[0] : null;
    $total_spent  = (float) wc_get_customer_total_spent($user_id);
}

// Gerçek Adres Verileri
$billing_first_name  = get_user_meta($user_id, 'billing_first_name', true) ?: ($current_user ? $current_user->first_name : '');
$billing_last_name   = get_user_meta($user_id, 'billing_last_name', true) ?: ($current_user ? $current_user->last_name : '');
$billing_address_1   = get_user_meta($user_id, 'billing_address_1', true);
$billing_city        = get_user_meta($user_id, 'billing_city', true);
$billing_state       = get_user_meta($user_id, 'billing_state', true);
$billing_postcode    = get_user_meta($user_id, 'billing_postcode', true);
$billing_phone       = get_user_meta($user_id, 'billing_phone', true);

$shipping_first_name = get_user_meta($user_id, 'shipping_first_name', true) ?: $billing_first_name;
$shipping_last_name  = get_user_meta($user_id, 'shipping_last_name', true) ?: $billing_last_name;
$shipping_address_1  = get_user_meta($user_id, 'shipping_address_1', true);
$shipping_city       = get_user_meta($user_id, 'shipping_city', true);
$shipping_state      = get_user_meta($user_id, 'shipping_state', true);
$shipping_postcode   = get_user_meta($user_id, 'shipping_postcode', true);

$cart_item_count = (class_exists('WooCommerce') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;

// Üyelik Seviyesi / Hedef Hesaplaması
$free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
$spend_percent = min(100, round(($total_spent / ($free_shipping_limit ?: 1)) * 100));
?>

<div class="emdief-container py-8">
    <?php if ($is_logged_in): ?>
        <?php if ($cart_item_count > 0): ?>
            <!-- Sepette Ürün Varsa: Mutlu Alışveriş Yapan Ayıcık -->
            <div class="account-cart-bear-banner is-cart-active" aria-label="<?php esc_attr_e('Aktif Sepet Alanı', 'mis360-mobilya'); ?>">
                <div class="cart-bear-visual">
                    <?php echo mis360_bear_shopping_cart(190, 105, 'animated-cart-bear'); ?>
                </div>
                <div class="cart-bear-content">
                    <div class="cart-bear-badge">
                        <span>🛒 SEPETİNİZDE <?php echo esc_html((string)$cart_item_count); ?> ÜRÜN SİZİ BEKLİYOR</span>
                    </div>
                    <h3 class="cart-bear-title">Ayıcık Sepetinizi Hazırlıyor! Miniklerin Dünyasını Büyütelim 🛒✨</h3>
                    <p class="cart-bear-sub">
                        Sepetinizde <strong><?php echo esc_html((string)$cart_item_count); ?> adet</strong> Montessori mobilya bulunuyor. 1.500 TL üzeri ücretsiz kargo avantajıyla siparişinizi güvenle tamamlayabilirsiniz.
                    </p>
                    <div class="cart-bear-perks">
                        <span class="perk-item">🚚 <strong>1.500 TL Üzeri</strong> Ücretsiz Kargo</span>
                        <span class="perk-sep">•</span>
                        <span class="perk-item">🛡️ <strong>1. Sınıf</strong> Güvenli MDF</span>
                        <span class="perk-sep">•</span>
                        <span class="perk-item">⚡ <strong>13:00'a Kadar</strong> Öncelikli İmalat</span>
                    </div>
                </div>
                <div class="cart-bear-action">
                    <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_cart_url() : home_url('/cart/')); ?>" class="emdief-btn btn-primary btn-md">
                        <span>Sepetime Git</span>
                        <?php echo mis360_icon('arrow-right', 16); ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Sepette Ürün Yoksa: Ağlayan Sevimli Ayıcık -->
            <div class="account-cart-bear-banner is-cart-empty" aria-label="<?php esc_attr_e('Boş Sepet Alanı', 'mis360-mobilya'); ?>">
                <div class="cart-bear-visual">
                    <?php echo mis360_bear_empty_cart(190, 105, 'animated-crying-bear'); ?>
                </div>
                <div class="cart-bear-content">
                    <div class="cart-bear-badge badge-crying">
                        <span>🥺 AYICIK ÇOK ÜZGÜN • SEPETİNİZ BOMBOŞ</span>
                    </div>
                    <h3 class="cart-bear-title">Sepetiniz Boş Kaldı, Ayıcığımız Ağlıyor... 🧸💔</h3>
                    <p class="cart-bear-sub">
                        Çocuğunuzun odasına düzen ve estetik katacak <strong>1. Sınıf MDF</strong> Montessori kitaplık veya dolaplarımızı sepetinize ekleyin, sevimli ayıcığımızın gözyaşları dinsin!
                    </p>
                    <div class="cart-bear-perks">
                        <span class="perk-item">🚚 <strong>1.500 TL Üzeri</strong> Ücretsiz Kargo</span>
                        <span class="perk-sep">•</span>
                        <span class="perk-item">🛡️ <strong>1. Sınıf</strong> Güvenli MDF</span>
                        <span class="perk-sep">•</span>
                        <span class="perk-item">⚡ <strong>13:00'a Kadar</strong> Öncelikli İmalat</span>
                    </div>
                </div>
                <div class="cart-bear-action">
                    <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/')); ?>" class="emdief-btn btn-primary btn-md">
                        <span>Hemen Ürünleri Keşfet</span>
                        <?php echo mis360_icon('arrow-right', 16); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="emdief-account-wrapper">
            <!-- Sol Panel: Kullanıcı Kartı & Navigasyon -->
            <aside class="account-sidebar" aria-label="<?php esc_attr_e('Hesap Gezinti Menüsü', 'mis360-mobilya'); ?>">
                <div class="account-user-card">
                    <div class="sidebar-avatar-wrap">
                        <?php echo mis360_teddy_bear_avatar(68); ?>
                    </div>
                    <div class="account-user-meta">
                        <h3 class="user-name"><?php echo esc_html($display_name); ?></h3>
                        <span class="user-email"><?php echo esc_html($user_email); ?></span>
                        <div class="user-level-wrap">
                            <div class="level-bar-label">
                                <span><?php echo $total_spent >= $free_shipping_limit ? 'Montessori Kulüp Üyesi' : 'Kulüp Avantaj Hedefi'; ?></span>
                                <strong>%<?php echo $spend_percent; ?></strong>
                            </div>
                            <div class="level-progress">
                                <div class="level-fill" style="width: <?php echo $spend_percent; ?>%;"></div>
                            </div>
                            <small class="level-hint">
                                <?php if ($total_spent >= $free_shipping_limit): ?>
                                    ⭐ Tüm siparişlerinizde ücretsiz kargo ve öncelik aktif!
                                <?php else: ?>
                                    Ücretsiz kargo kulübü için son <?php echo function_exists('wc_price') ? wc_price(max(0, $free_shipping_limit - $total_spent)) : max(0, $free_shipping_limit - $total_spent) . ' TL'; ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <nav class="account-nav-menu">
                    <ul class="account-nav-list">
                        <li class="nav-item is-active">
                            <a href="#tab-dashboard" class="account-tab-trigger" data-target="tab-dashboard">
                                <span class="nav-svg"><?php echo mis360_icon('home', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Genel Bakış', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-orders" class="account-tab-trigger" data-target="tab-orders">
                                <span class="nav-svg"><?php echo mis360_icon('package', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Siparişlerim', 'mis360-mobilya'); ?></span>
                                <?php if ($total_orders > 0): ?>
                                    <span class="nav-counter"><?php echo $total_orders; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-addresses" class="account-tab-trigger" data-target="tab-addresses">
                                <span class="nav-svg"><?php echo mis360_icon('map-pin', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Kayıtlı Adreslerim', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-coupons" class="account-tab-trigger" data-target="tab-coupons">
                                <span class="nav-svg"><?php echo mis360_icon('ticket', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Kuponlarım', 'mis360-mobilya'); ?></span>
                                <span class="nav-tag-badge">%10</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-details" class="account-tab-trigger" data-target="tab-details">
                                <span class="nav-svg"><?php echo mis360_icon('settings', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Hesap Detayları', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item nav-logout">
                            <a href="<?php echo esc_url(wc_logout_url(home_url('/'))); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('logout', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Güvenli Çıkış', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Sağ Panel: Profesyonel İçerik Sekmeleri -->
            <main class="account-main-content">
                <?php
                // Eğer WooCommerce doğrudan bir alt endpoint (örneğin view-order, edit-address vs.) çağırıyorsa:
                if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()):
                ?>
                    <div class="account-wc-endpoint-wrapper" style="background:#fff; padding:2rem; border-radius:18px; border:1px solid #f1ece7;">
                        <?php do_action('woocommerce_account_content'); ?>
                    </div>
                <?php else: ?>
                    <!-- Tab 1: Genel Bakış (Dashboard) -->
                    <div class="account-panel-tab is-active" id="tab-dashboard">
                        <!-- 4 Gerçek Metrik Kartı -->
                        <div class="account-stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon-frame icon-amber">
                                    <?php echo mis360_icon('package', 22); ?>
                                </div>
                                <div class="stat-meta">
                                    <span class="stat-title">Toplam Sipariş</span>
                                    <strong class="stat-value"><?php echo $total_orders; ?> Adet</strong>
                                    <span class="stat-sub"><?php echo $total_orders > 0 ? 'Kayıtlı siparişiniz mevcut' : 'Henüz siparişiniz yok'; ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon-frame icon-emerald">
                                    <?php echo mis360_icon('truck', 22); ?>
                                </div>
                                <div class="stat-meta">
                                    <span class="stat-title">Son Sipariş Durumu</span>
                                    <strong class="stat-value <?php echo $latest_order ? 'color-green' : ''; ?>">
                                        <?php echo $latest_order ? esc_html(wc_get_order_status_name($latest_order->get_status())) : 'Sipariş Yok'; ?>
                                    </strong>
                                    <span class="stat-sub">
                                        <?php echo $latest_order ? ('Sipariş #' . $latest_order->get_order_number()) : 'Henüz alışveriş yapılmadı'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon-frame icon-blue">
                                    <?php echo mis360_icon('star', 22); ?>
                                </div>
                                <div class="stat-meta">
                                    <span class="stat-title">Toplam Harcama</span>
                                    <strong class="stat-value"><?php echo function_exists('wc_price') ? wc_price($total_spent) : number_format($total_spent, 2, ',', '.') . ' TL'; ?></strong>
                                    <span class="stat-sub"><?php echo $total_orders > 0 ? 'Tamamlanan siparişler' : 'Alışverişe başlayın'; ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon-frame icon-coral">
                                    <?php echo mis360_icon('ticket', 22); ?>
                                </div>
                                <div class="stat-meta">
                                    <span class="stat-title">Geçerli Hoş Geldin Kuponu</span>
                                    <strong class="stat-value color-coral">%10 İndirim</strong>
                                    <span class="stat-sub">Kod: <code>EMDIEF10</code></span>
                                </div>
                            </div>
                        </div>

                        <!-- Son Sipariş Kartı (SADECE GERÇEK SİPARİŞ VARSA GÖSTERİLİR) -->
                        <?php if ($latest_order): ?>
                            <?php
                            $order_items = $latest_order->get_items();
                            $first_item = !empty($order_items) ? reset($order_items) : null;
                            $first_product = $first_item ? $first_item->get_product() : null;
                            $status = $latest_order->get_status();
                            ?>
                            <div class="order-tracker-card">
                                <div class="tracker-header">
                                    <div class="tracker-header-left">
                                        <span class="tracker-badge-live"><span class="radar-dot"></span> SON SİPARİŞİNİZ</span>
                                        <h3 class="tracker-order-num">Sipariş: #<?php echo esc_html($latest_order->get_order_number()); ?></h3>
                                        <span class="tracker-order-date">Sipariş Tarihi: <?php echo esc_html(wc_format_datetime($latest_order->get_date_created(), 'd F Y • H:i')); ?></span>
                                    </div>
                                    <div class="tracker-header-right">
                                        <span class="status-pill status-<?php echo esc_attr($status); ?>">
                                            <?php echo esc_html(wc_get_order_status_name($status)); ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if ($first_item): ?>
                                    <div class="tracker-item-row">
                                        <div class="tracker-thumb-frame">
                                            <?php if ($first_product): ?>
                                                <?php echo $first_product->get_image('thumbnail', ['class' => 'tracker-thumb']); ?>
                                            <?php else: ?>
                                                <img src="<?php echo esc_url(wc_placeholder_img_src('thumbnail')); ?>" alt="" class="tracker-thumb">
                                            <?php endif; ?>
                                        </div>
                                        <div class="tracker-details">
                                            <div class="tracker-item-tags">
                                                <span class="badge-mdf-eco">1. Sınıf MDF</span>
                                                <span class="badge-cert">Orijinal Emdief Home</span>
                                            </div>
                                            <h4 class="tracker-item-title"><?php echo esc_html($first_item->get_name()); ?></h4>
                                            <div class="tracker-item-pricing">
                                                <span class="qty"><?php echo esc_html((string)$first_item->get_quantity()); ?> Adet</span>
                                                <span class="dot">•</span>
                                                <span class="price"><?php echo $latest_order->get_formatted_line_subtotal($first_item); ?></span>
                                                <span class="dot">•</span>
                                                <strong>Toplam: <?php echo $latest_order->get_formatted_order_total(); ?></strong>
                                            </div>
                                        </div>
                                        <div class="tracker-actions">
                                            <a href="<?php echo esc_url($latest_order->get_view_order_url()); ?>" class="emdief-btn btn-secondary btn-sm">
                                                <span>Detayları İncele</span>
                                                <?php echo mis360_icon('arrow-right', 14); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Gerçek Durum Çizelgesi -->
                                <div class="tracker-stepper">
                                    <div class="step is-done">
                                        <div class="step-indicator"><?php echo mis360_icon('check', 16); ?></div>
                                        <div class="step-text"><strong>Sipariş Alındı</strong><small>Kayıt Yapıldı</small></div>
                                    </div>
                                    <div class="step-connector <?php echo in_array($status, ['processing', 'completed']) ? 'is-done' : ''; ?>"></div>

                                    <div class="step <?php echo in_array($status, ['processing', 'completed']) ? 'is-done' : 'is-pending'; ?>">
                                        <div class="step-indicator"><?php echo mis360_icon('package', 16); ?></div>
                                        <div class="step-text"><strong>Hazırlanıyor</strong><small>Atölye İmalatı</small></div>
                                    </div>
                                    <div class="step-connector <?php echo in_array($status, ['completed']) ? 'is-done' : ''; ?>"></div>

                                    <div class="step <?php echo in_array($status, ['completed']) ? 'is-done' : 'is-pending'; ?>">
                                        <div class="step-indicator"><?php echo mis360_icon('truck', 16); ?></div>
                                        <div class="step-text"><strong>Kargoda / Dağıtımda</strong><small>Taşınıyor</small></div>
                                    </div>
                                    <div class="step-connector <?php echo ($status === 'completed') ? 'is-done' : ''; ?>"></div>

                                    <div class="step <?php echo ($status === 'completed') ? 'is-done' : 'is-pending'; ?>">
                                        <div class="step-indicator"><?php echo mis360_icon('home', 16); ?></div>
                                        <div class="step-text"><strong>Teslim Edildi</strong><small>Tamamlandı</small></div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- SİPARİŞ YOKSA TEMİZ BOŞ DURUM -->
                            <div class="order-tracker-card no-orders-card" style="padding: 3rem 2rem; text-align: center; background: #fff; border-radius: 18px; border: 1px dashed #cbd5e1;">
                                <div class="no-orders-content">
                                    <div class="no-orders-icon" style="font-size: 3rem; margin-bottom: 0.75rem;">📦</div>
                                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Henüz Verilmiş Bir Siparişiniz Bulunmuyor</h3>
                                    <p style="color: #64748b; font-size: 0.92rem; max-width: 460px; margin: 0 auto 1.5rem auto; line-height: 1.5;">Miniklerin odasına düzen ve estetik katacak 1. sınıf MDF Montessori mobilyalarımızı keşfedin, siparişinizi güvenle oluşturun.</p>
                                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="emdief-btn btn-primary btn-md">
                                        <span>Kataloğu Keşfet</span>
                                        <?php echo mis360_icon('arrow-right', 16); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Kupon ve Özel Sadakat Şeridi -->
                        <div class="account-promo-strip">
                            <div class="promo-left">
                                <div class="promo-badge-tag">ÖZEL KULÜP AYRICALIĞI</div>
                                <h3 class="promo-heading">Montessori Aile Kulübü %10 İndirim Kuponunuz</h3>
                                <p class="promo-desc">1. Sınıf MDF kitaplık, öğrenme kulesi ve çocuk çalışma masalarında geçerlidir.</p>
                            </div>
                            <div class="promo-right">
                                <div class="coupon-pill-wrap">
                                    <span class="coupon-code-text">EMDIEF10</span>
                                    <button type="button" class="coupon-copy-btn btn-copy-code" data-copy="EMDIEF10">
                                        <?php echo mis360_icon('copy', 14); ?>
                                        <span>Kopyala</span>
                                    </button>
                                </div>
                                <small class="coupon-expiry">Tüm Alışverişlerde Geçerli</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Siparişlerim (Gerçek Sipariş Listesi) -->
                    <div class="account-panel-tab" id="tab-orders">
                        <div class="panel-header">
                            <div>
                                <h3>Sipariş Geçmişim</h3>
                                <p>Tüm geçmiş ve aktif siparişlerinizin dökümleri.</p>
                            </div>
                            <span class="badge-total-count">Toplam <?php echo $total_orders; ?> Sipariş</span>
                        </div>

                        <?php if ($total_orders > 0): ?>
                            <div class="orders-list">
                                <?php foreach ($customer_orders as $order): ?>
                                    <?php
                                    $order_status = $order->get_status();
                                    $order_items = $order->get_items();
                                    ?>
                                    <article class="order-box-card">
                                        <div class="order-box-top">
                                            <div class="meta-col">
                                                <span class="label">Sipariş Kodu</span>
                                                <strong>#<?php echo esc_html($order->get_order_number()); ?></strong>
                                            </div>
                                            <div class="meta-col">
                                                <span class="label">Tarih</span>
                                                <strong><?php echo esc_html(wc_format_datetime($order->get_date_created(), 'd F Y')); ?></strong>
                                            </div>
                                            <div class="meta-col">
                                                <span class="label">Ödeme Yöntemi</span>
                                                <strong><?php echo esc_html($order->get_payment_method_title() ?: 'Online Ödeme'); ?></strong>
                                            </div>
                                            <div class="meta-col">
                                                <span class="label">Toplam Tutar</span>
                                                <strong class="price-highlight"><?php echo $order->get_formatted_order_total(); ?></strong>
                                            </div>
                                            <div class="meta-col-status">
                                                <span class="status-pill status-<?php echo esc_attr($order_status); ?>">
                                                    <?php echo esc_html(wc_get_order_status_name($order_status)); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="order-box-body">
                                            <?php foreach ($order_items as $item): ?>
                                                <?php $product = $item->get_product(); ?>
                                                <div class="order-product-item">
                                                    <?php if ($product): ?>
                                                        <?php echo $product->get_image('thumbnail', ['class' => 'product-mini-thumb']); ?>
                                                    <?php else: ?>
                                                        <img src="<?php echo esc_url(wc_placeholder_img_src('thumbnail')); ?>" alt="" class="product-mini-thumb">
                                                    <?php endif; ?>
                                                    <div class="product-mini-info">
                                                        <h4><?php echo esc_html($item->get_name()); ?></h4>
                                                        <p class="product-specs"><?php echo esc_html((string)$item->get_quantity()); ?> Adet</p>
                                                        <span class="product-price"><?php echo $order->get_formatted_line_subtotal($item); ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="order-actions-group" style="margin-top: 12px; display: flex; gap: 8px;">
                                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="emdief-btn btn-secondary btn-sm">
                                                    <span>Siparişi Görüntüle</span>
                                                    <?php echo mis360_icon('arrow-right', 14); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-orders-box" style="padding: 3rem; text-align: center; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📦</div>
                                <h4>Henüz Kayıtlı Bir Siparişiniz Bulunmuyor</h4>
                                <p style="color: #64748b; margin-bottom: 1.25rem;">Verdiğiniz tüm siparişlerin durumunu ve kargo takibini buradan izleyebilirsiniz.</p>
                                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="emdief-btn btn-primary btn-md">
                                    <span>Alışverişe Başla</span>
                                    <?php echo mis360_icon('arrow-right', 16); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 3: Kayıtlı Adreslerim (Gerçek WooCommerce Adresleri) -->
                    <div class="account-panel-tab" id="tab-addresses">
                        <div class="panel-header">
                            <div>
                                <h3>Kayıtlı Teslimat ve Fatura Adreslerim</h3>
                                <p>Siparişlerinizin hasarsız ve zamanında teslimatı için adreslerinizi güncelleyebilirsiniz.</p>
                            </div>
                        </div>

                        <div class="addresses-grid">
                            <!-- Fatura Adresi -->
                            <div class="address-card">
                                <div class="address-header">
                                    <div class="address-title">
                                        <span class="addr-icon"><?php echo mis360_icon('home', 18); ?></span>
                                        <h4>Fatura Adresi</h4>
                                    </div>
                                </div>
                                <div class="address-body">
                                    <?php if (!empty($billing_address_1) || !empty($billing_city)): ?>
                                        <strong><?php echo esc_html(trim($billing_first_name . ' ' . $billing_last_name)); ?></strong>
                                        <p><?php echo esc_html($billing_address_1); ?><br><?php echo esc_html(trim($billing_city . ' ' . $billing_state . ' ' . $billing_postcode)); ?></p>
                                        <?php if (!empty($billing_phone)): ?>
                                            <span class="addr-phone">Tel: <?php echo esc_html($billing_phone); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p style="color: #94a3b8; font-style: italic;">Henüz fatura adresi eklenmedi.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="address-footer">
                                    <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', 'billing')); ?>" class="btn-text-action">
                                        <?php echo (!empty($billing_address_1)) ? 'Adresi Düzenle' : 'Adres Ekle'; ?>
                                    </a>
                                </div>
                            </div>

                            <!-- Teslimat Adresi -->
                            <div class="address-card">
                                <div class="address-header">
                                    <div class="address-title">
                                        <span class="addr-icon"><?php echo mis360_icon('truck', 18); ?></span>
                                        <h4>Teslimat Adresi</h4>
                                    </div>
                                </div>
                                <div class="address-body">
                                    <?php if (!empty($shipping_address_1) || !empty($shipping_city)): ?>
                                        <strong><?php echo esc_html(trim($shipping_first_name . ' ' . $shipping_last_name)); ?></strong>
                                        <p><?php echo esc_html($shipping_address_1); ?><br><?php echo esc_html(trim($shipping_city . ' ' . $shipping_state . ' ' . $shipping_postcode)); ?></p>
                                    <?php else: ?>
                                        <p style="color: #94a3b8; font-style: italic;">Henüz teslimat adresi eklenmedi.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="address-footer">
                                    <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', 'shipping')); ?>" class="btn-text-action">
                                        <?php echo (!empty($shipping_address_1)) ? 'Adresi Düzenle' : 'Adres Ekle'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Kuponlarım -->
                    <div class="account-panel-tab" id="tab-coupons">
                        <div class="panel-header">
                            <div>
                                <h3>İndirim Kuponlarım</h3>
                                <p>Siparişlerinizde kullanabileceğiniz geçerli indirim kuponları.</p>
                            </div>
                        </div>

                        <div class="coupon-cards-grid">
                            <div class="coupon-ticket-card">
                                <div class="coupon-left-ticket">
                                    <span class="val">%10</span>
                                    <span class="sub">İNDİRİM</span>
                                </div>
                                <div class="coupon-right-ticket">
                                    <div class="coupon-tag-row">
                                        <span class="badge-club">Montessori Club</span>
                                        <span class="badge-active">Aktif</span>
                                    </div>
                                    <h4>Montessori Aile Hoş Geldin Kuponu</h4>
                                    <p>Tüm 1. sınıf kaliteli MDF kitaplık ve mobilya siparişlerinde geçerlidir.</p>
                                    <div class="coupon-action-row">
                                        <code class="ticket-code">EMDIEF10</code>
                                        <button type="button" class="btn-ticket-copy btn-copy-code" data-copy="EMDIEF10">
                                            <?php echo mis360_icon('copy', 14); ?>
                                            <span>Kodu Kopyala</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Hesap Detayları (Gerçek Bilgiler) -->
                    <div class="account-panel-tab" id="tab-details">
                        <div class="panel-header">
                            <div>
                                <h3>Hesap Ayarları & Güvenlik</h3>
                                <p>Ad, soyad, e-posta ve şifre gibi hesap bilgilerinizi yönetin.</p>
                            </div>
                        </div>

                        <div class="account-details-card" style="background:#fff; padding: 2rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:4px;">Ad Soyad</label>
                                    <div style="font-size:16px; font-weight:800; color:#1e293b;"><?php echo esc_html($display_name); ?></div>
                                </div>
                                <div class="form-group">
                                    <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:4px;">E-Posta Adresi</label>
                                    <div style="font-size:16px; font-weight:800; color:#1e293b;"><?php echo esc_html($user_email); ?></div>
                                </div>
                            </div>

                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; flex-wrap: wrap;">
                                <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>" class="emdief-btn btn-primary btn-md">
                                    <?php echo mis360_icon('settings', 16); ?>
                                    <span>Hesap Bilgilerini Düzenle / Şifre Değiştir</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    <?php else: ?>
        <!-- Giriş Yapmamış Ziyaretçi Görünümü -->
        <div class="account-guest-wrapper" style="max-width: 540px; margin: 3rem auto; text-align: center; background: #fff; padding: 3rem 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f1ece7;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🧸</div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Emdief Home Hesabınıza Giriş Yapın</h2>
            <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 1.75rem;">Siparişlerinizi takip etmek, kayıtlı adreslerinizi yönetmek ve özel Montessori kulüp avantajlarından yararlanmak için lütfen giriş yapın.</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button type="button" class="emdief-btn btn-primary btn-md" id="guest-login-open">
                    <span>Giriş Yap / Kayıt Ol</span>
                    <?php echo mis360_icon('arrow-right', 16); ?>
                </button>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="emdief-btn btn-outline btn-md">
                    <span>Anasayfaya Dön</span>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Sekme Değişimi
    const tabTriggers = document.querySelectorAll('.account-tab-trigger');
    const panels = document.querySelectorAll('.account-panel-tab');

    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = trigger.getAttribute('data-target');
            
            document.querySelectorAll('.account-nav-list .nav-item').forEach(item => item.classList.remove('is-active'));
            trigger.closest('.nav-item').classList.add('is-active');

            panels.forEach(p => p.classList.remove('is-active'));
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.add('is-active');
            }
        });
    });

    // Kopyalama Butonları (Kupon Kodu)
    const copyBtns = document.querySelectorAll('.btn-copy-code');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const code = btn.getAttribute('data-copy');
            if (code) {
                navigator.clipboard.writeText(code).then(() => {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<span>✓ Kopyalandı!</span>';
                    btn.classList.add('is-copied');
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('is-copied');
                    }, 2000);
                });
            }
        });
    });

    // Ziyaretçi Giriş Butonu -> Header Popup Açar
    const guestLoginBtn = document.getElementById('guest-login-open');
    if (guestLoginBtn) {
        guestLoginBtn.addEventListener('click', () => {
            const authTrigger = document.getElementById('emdief-login-trigger');
            if (authTrigger) {
                authTrigger.click();
            }
        });
    }
});
</script>

<?php
get_footer();
