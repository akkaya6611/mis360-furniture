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

$cart_item_count = (class_exists('WooCommerce') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;

// Üyelik Seviyesi / Hedef Hesaplaması
$free_shipping_limit = (float) get_theme_mod('mis360_free_shipping_limit', 1500);
$spend_percent = min(100, round(($total_spent / ($free_shipping_limit ?: 1)) * 100));

// Aktif Menü & Uç Nokta (Endpoint) Tespiti
$is_view_order = function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('view-order');
$is_orders     = function_exists('is_wc_endpoint_url') && (is_wc_endpoint_url('orders') || $is_view_order);
$is_address    = function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('edit-address');
$is_account    = function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('edit-account');
$is_coupons    = isset($_GET['tab']) && $_GET['tab'] === 'coupons';
$is_help       = isset($_GET['tab']) && in_array($_GET['tab'], ['yardim', 'kurulum', 'help'], true);
$is_dashboard  = !$is_orders && !$is_address && !$is_account && !$is_coupons && !$is_help;
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
            <!-- Sol Panel: Gerçek Linkli Navigasyon Menüsü -->
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
                                    ⭐ Tüm siparişlerinizde ücretsiz kargo aktif!
                                <?php else: ?>
                                    Ücretsiz kargo kulübü için son <?php echo function_exists('wc_price') ? wc_price(max(0, $free_shipping_limit - $total_spent)) : max(0, $free_shipping_limit - $total_spent) . ' TL'; ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <nav class="account-nav-menu">
                    <ul class="account-nav-list">
                        <li class="nav-item <?php echo $is_dashboard ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('home', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Genel Bakış', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $is_orders ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('package', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Siparişlerim', 'mis360-mobilya'); ?></span>
                                <?php if ($total_orders > 0): ?>
                                    <span class="nav-counter"><?php echo $total_orders; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $is_address ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('map-pin', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Kayıtlı Adreslerim', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $is_coupons ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(add_query_arg('tab', 'coupons', wc_get_page_permalink('myaccount'))); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('ticket', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Kuponlarım', 'mis360-mobilya'); ?></span>
                                <span class="nav-tag-badge">%10</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $is_help ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(add_query_arg('tab', 'yardim', wc_get_page_permalink('myaccount'))); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('video', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Kurulum & Yardım', 'mis360-mobilya'); ?></span>
                                <span class="nav-tag-badge" style="background: #fef3c7; color: #b45309;">🎬 Video</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $is_account ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>">
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

            <!-- Sağ Panel: Profesyonel İçerik Alanı -->
            <main class="account-main-content">
                <?php if ($is_coupons): ?>
                    <!-- Kuponlarım Bölümü -->
                    <div class="account-wc-endpoint-wrapper">
                        <div class="panel-header" style="margin-bottom: 1.5rem;">
                            <div>
                                <h3 style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">İndirim Kuponlarım</h3>
                                <p style="color: #64748b; font-size: 0.88rem; margin: 0;">Siparişlerinizde kullanabileceğiniz aktif indirim kuponları.</p>
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
                <?php elseif ($is_help): ?>
                    <!-- Kurulum & Yardım Bölümü -->
                    <div class="account-wc-endpoint-wrapper">
                        <div class="panel-header" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <h3 style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Kurulum Videoları & Destek Merkezi</h3>
                                <p style="color: #64748b; font-size: 0.88rem; margin: 0;">Sipariş ettiğiniz mobilyaların montaj adımlarını izleyin ve eksik parça taleplerinizi iletin.</p>
                            </div>
                            <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="emdief-btn btn-outline btn-sm">
                                <span>Tüm Kılavuzlar</span>
                                <?php echo mis360_icon('arrow-right', 14); ?>
                            </a>
                        </div>

                        <div class="account-help-grid">
                            <div class="account-video-item">
                                <div class="account-video-thumb">
                                    <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg" alt="Carmen Kitaplık Kurulumu" loading="lazy">
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Carmen%20Kitaplık%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="account-play-btn" aria-label="Videoyu Oynat">
                                        <?php echo mis360_icon('play', 20); ?>
                                    </a>
                                    <span class="video-duration">⏱️ 05:20</span>
                                </div>
                                <div class="account-video-info">
                                    <h4>Carmen Montessori 4 Raflı Kitaplık</h4>
                                    <p>Gizli vida bağlantıları, raf sıralaması ve duvara sabitleme aparatı montajı.</p>
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Carmen%20Kitaplık%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="btn-play-text">
                                        <?php echo mis360_icon('play', 14); ?>
                                        <span>Kurulum Videosunu İzle</span>
                                    </a>
                                </div>
                            </div>

                            <div class="account-video-item">
                                <div class="account-video-thumb">
                                    <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief2.jpg" alt="Safir Kitaplık Kurulumu" loading="lazy">
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Safir%20Kitaplık%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="account-play-btn" aria-label="Videoyu Oynat">
                                        <?php echo mis360_icon('play', 20); ?>
                                    </a>
                                    <span class="video-duration">⏱️ 06:45</span>
                                </div>
                                <div class="account-video-info">
                                    <h4>Safir Montessori 5 Raflı Geniş Kitaplık</h4>
                                    <p>Geniş gövde birleşimi, arka destek kuşakları ve devrilme emniyet kiti.</p>
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Safir%20Kitaplık%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="btn-play-text">
                                        <?php echo mis360_icon('play', 14); ?>
                                        <span>Kurulum Videosunu İzle</span>
                                    </a>
                                </div>
                            </div>

                            <div class="account-video-item">
                                <div class="account-video-thumb">
                                    <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg" alt="Ahşap Düzenleyici Kurulumu" loading="lazy">
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Ahşap%20Düzenleyici%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="account-play-btn" aria-label="Videoyu Oynat">
                                        <?php echo mis360_icon('play', 20); ?>
                                    </a>
                                    <span class="video-duration">⏱️ 04:10</span>
                                </div>
                                <div class="account-video-info">
                                    <h4>Montessori Kutu Düzenleyici & Oyuncaklık</h4>
                                    <p>Ahşap kanallı kutu rayları montajı ve pratik kilit mekanizmaları.</p>
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Ahşap%20Düzenleyici%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="btn-play-text">
                                        <?php echo mis360_icon('play', 14); ?>
                                        <span>Kurulum Videosunu İzle</span>
                                    </a>
                                </div>
                            </div>

                            <div class="account-video-item">
                                <div class="account-video-thumb">
                                    <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief2.jpg" alt="Duvar Rafı Kurulumu" loading="lazy">
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Duvar%20Rafı%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="account-play-btn" aria-label="Videoyu Oynat">
                                        <?php echo mis360_icon('play', 20); ?>
                                    </a>
                                    <span class="video-duration">⏱️ 03:15</span>
                                </div>
                                <div class="account-video-info">
                                    <h4>Montessori Bulut Duvar & Banyo Rafı</h4>
                                    <p>Gizli dübel montajı, su terazisiyle hizalama ve güvenli taşıma kılavuzu.</p>
                                    <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Duvar%20Rafı%20kurulum%20videosunu%20izlemek%20istiyorum." target="_blank" rel="noopener" class="btn-play-text">
                                        <?php echo mis360_icon('play', 14); ?>
                                        <span>Kurulum Videosunu İzle</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Canlı Destek & Eksik Parça Kutusu -->
                        <div class="account-support-box">
                            <div class="support-box-icon">
                                <?php echo mis360_icon('wrench', 28); ?>
                            </div>
                            <div class="support-box-content">
                                <h4>Kurulumda Bir Aksamaya mı Rastladınız?</h4>
                                <p>Paketinizden eksik vida, dübel veya hasarlı parça çıktıysa anında ücretsiz yedek parça temin ediyoruz.</p>
                            </div>
                            <div class="support-box-action">
                                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=<?php echo rawurlencode('Merhaba Emdief Home, siparişimdeki mobilyanın kurulumu / parça desteği hakkında yardım almak istiyorum.'); ?>" target="_blank" rel="noopener" class="emdief-btn btn-whatsapp btn-md">
                                    <?php echo mis360_icon('whatsapp', 18); ?>
                                    <span>WhatsApp Canlı Destek</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php elseif (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()): ?>
                    <!-- WooCommerce Doğal Alt Sayfaları (Siparişler, Sipariş Detayı, Adresler, Hesap Ayarları) -->
                    <div class="account-wc-endpoint-wrapper">
                        <?php do_action('woocommerce_account_content'); ?>
                    </div>
                <?php else: ?>
                    <!-- Genel Bakış Paneli (Dashboard) -->
                    <div class="account-dashboard-wrapper">
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

                        <!-- Kurulum & Montaj Destek Kartı -->
                        <div class="account-quick-assembly-card">
                            <div class="assembly-card-icon">
                                🎬
                            </div>
                            <div class="assembly-card-content">
                                <h4>Mobilyanızı Kurarken Yardıma mı İhtiyacınız Var?</h4>
                                <p>Tüm 1. Sınıf MDF Montessori kitaplık ve mobilyalarımız için adım adım kurulum videolarını ve kılavuzlarını izleyin.</p>
                            </div>
                            <div class="assembly-card-action">
                                <a href="<?php echo esc_url(add_query_arg('tab', 'yardim', wc_get_page_permalink('myaccount'))); ?>" class="emdief-btn btn-primary btn-sm">
                                    <span>Kurulum Videolarını Aç</span>
                                    <?php echo mis360_icon('arrow-right', 14); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Kupon ve Özel Sadakat Şeridi -->
                        <div class="account-promo-strip">
                            <div class="promo-left">
                                <div class="promo-badge-tag">ÖZEL KULÜP AYRICALIĞI</div>
                                <h3 class="promo-heading">Montessori Aile Kulübü %10 İndirim Kuponunuz</h3>
                                <p class="promo-desc">1. Sınıf MDF kitaplık, eğitici ahşap oyuncaklar ve çocuk odası düzenleyicilerinde geçerlidir.</p>
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
