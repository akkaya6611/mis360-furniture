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

$current_user = wp_get_current_user();
$is_demo      = isset($_GET['demo']) && $_GET['demo'] === '1';
$is_logged_in = is_user_logged_in() || $is_demo;
$display_name = is_user_logged_in() ? ($current_user->display_name ?: $current_user->user_login) : ($is_demo ? 'Serkan Bey (Önizleme)' : 'Değerli Misafirimiz');
$user_email   = is_user_logged_in() ? $current_user->user_email : ($is_demo ? 'serkan@emdiefhome.com.tr' : '');
?>

<div class="emdief-container py-8">
    <?php if ($is_logged_in): ?>
        <?php
        $cart_item_count = (class_exists('WooCommerce') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
        ?>
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
            <!-- Sol Panel: Prestij Kullanıcı Kartı & Navigasyon -->
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
                                <span>Gold Kulüp Hedefi</span>
                                <strong>%75</strong>
                            </div>
                            <div class="level-progress">
                                <div class="level-fill" style="width: 75%;"></div>
                            </div>
                            <small class="level-hint">Gold Seviye için son 250 Puan (1.000 Puan Hedefi)</small>
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
                                <span class="nav-counter">1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-wishlist" class="account-tab-trigger" data-target="tab-wishlist">
                                <span class="nav-svg"><?php echo mis360_icon('heart', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Favorilerim', 'mis360-mobilya'); ?></span>
                                <span class="nav-counter">3</span>
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
                                <span class="nav-text"><?php esc_html_e('Kuponlarım & Puan', 'mis360-mobilya'); ?></span>
                                <span class="nav-tag-badge">%10</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-details" class="account-tab-trigger" data-target="tab-details">
                                <span class="nav-svg"><?php echo mis360_icon('settings', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Hesap & Güvenlik', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item nav-logout">
                            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">
                                <span class="nav-svg"><?php echo mis360_icon('logout', 18); ?></span>
                                <span class="nav-text"><?php esc_html_e('Güvenli Çıkış', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Sağ Panel: Profesyonel İçerik Sekmeleri -->
            <main class="account-main-content">
                <!-- Tab 1: Genel Bakış (Dashboard) -->
                <div class="account-panel-tab is-active" id="tab-dashboard">
                    <!-- 4 Metrik Kartı -->
                    <div class="account-stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon-frame icon-amber">
                                <?php echo mis360_icon('package', 22); ?>
                            </div>
                            <div class="stat-meta">
                                <span class="stat-title">Toplam Sipariş</span>
                                <strong class="stat-value">1 Adet</strong>
                                <span class="stat-sub">Aktif sipariş bulunuyor</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon-frame icon-emerald">
                                <?php echo mis360_icon('truck', 22); ?>
                            </div>
                            <div class="stat-meta">
                                <span class="stat-title">Kargo Aşaması</span>
                                <strong class="stat-value color-green">Yolda / Dağıtımda</strong>
                                <span class="stat-sub">Tahmini Teslimat: Yarın</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon-frame icon-blue">
                                <?php echo mis360_icon('star', 22); ?>
                            </div>
                            <div class="stat-meta">
                                <span class="stat-title">Montessori Puanı</span>
                                <strong class="stat-value">1.000 Puan</strong>
                                <span class="stat-sub">5,00 TL Kullanılabilir Bakiye (1.000 Puan = 5 TL)</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon-frame icon-coral">
                                <?php echo mis360_icon('ticket', 22); ?>
                            </div>
                            <div class="stat-meta">
                                <span class="stat-title">Geçerli Kupon</span>
                                <strong class="stat-value color-coral">%10 İndirim</strong>
                                <span class="stat-sub">Kod: <code>EMDIEF10</code></span>
                            </div>
                        </div>
                    </div>

                    <!-- Kurumsal Canlı Sipariş Takip Kartı -->
                    <div class="order-tracker-card">
                        <div class="tracker-header">
                            <div class="tracker-header-left">
                                <span class="tracker-badge-live"><span class="radar-dot"></span> CANLI KARGO TAKİBİ</span>
                                <h3 class="tracker-order-num">Sipariş: #EMD-2026-9812</h3>
                                <span class="tracker-order-date">Sipariş Tarihi: 11 Eylül 2026 • 10:15</span>
                            </div>
                            <div class="tracker-header-right">
                                <div class="cargo-company-pill">
                                    <span class="cargo-name">Yurtiçi Kargo</span>
                                    <span class="cargo-track-num">289410951</span>
                                    <button type="button" class="btn-copy-code" data-copy="289410951" title="Takip Numarasını Kopyala">
                                        <?php echo mis360_icon('copy', 14); ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="tracker-item-row">
                            <div class="tracker-thumb-frame">
                                <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg" alt="Carmen 3 Raflı 1. Sınıf MDF Kitaplık" class="tracker-thumb">
                            </div>
                            <div class="tracker-details">
                                <div class="tracker-item-tags">
                                    <span class="badge-mdf-eco"><?php echo mis360_icon('leaf', 12); ?> 1. Sınıf MDF</span>
                                    <span class="badge-cert">1. Sınıf Kalite</span>
                                </div>
                                <h4 class="tracker-item-title">Carmen 3 Raflı Kitaplık – Çocuk Odası Eğitici Montessori</h4>
                                <div class="tracker-item-pricing">
                                    <span class="qty">1 Adet</span>
                                    <span class="dot">•</span>
                                    <span class="price">800,00 TL</span>
                                    <span class="shipping-tag">Ücretsiz Kargo</span>
                                </div>
                            </div>
                            <div class="tracker-actions">
                                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Siparis%20Takip%20No%20EMD-2026-9812%20hakkinda%20bilgi%20almak%20istiyorum." target="_blank" rel="noopener" class="emdief-btn btn-secondary btn-sm">
                                    <?php echo mis360_icon('whatsapp', 16); ?>
                                    <span>Kargo Durumu Sor</span>
                                </a>
                            </div>
                        </div>

                        <!-- 4 Kademeli Kurumsal İlerleme Çizelgesi -->
                        <div class="tracker-stepper">
                            <div class="step is-done">
                                <div class="step-indicator">
                                    <?php echo mis360_icon('check', 16); ?>
                                </div>
                                <div class="step-text">
                                    <strong>Sipariş Onaylandı</strong>
                                    <small>11 Eyl, 10:15</small>
                                </div>
                            </div>
                            <div class="step-connector is-done"></div>

                            <div class="step is-done">
                                <div class="step-indicator">
                                    <?php echo mis360_icon('check', 16); ?>
                                </div>
                                <div class="step-text">
                                    <strong>Atölyede Hazırlandı</strong>
                                    <small>11 Eyl, 16:30</small>
                                </div>
                            </div>
                            <div class="step-connector is-done"></div>

                            <div class="step is-current">
                                <div class="step-indicator">
                                    <span class="pulse-ring"></span>
                                    <?php echo mis360_icon('truck', 18); ?>
                                </div>
                                <div class="step-text">
                                    <strong class="color-amber">Kargoda / Dağıtımda</strong>
                                    <small>12 Eyl, 08:45</small>
                                </div>
                            </div>
                            <div class="step-connector"></div>

                            <div class="step is-pending">
                                <div class="step-indicator">
                                    <?php echo mis360_icon('home', 16); ?>
                                </div>
                                <div class="step-text">
                                    <strong>Teslim Edildi</strong>
                                    <small>Tahmini 13 Eyl</small>
                                </div>
                            </div>
                        </div>
                    </div>

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
                            <small class="coupon-expiry">Geçerlilik: 31 Aralık 2026</small>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Siparişlerim -->
                <div class="account-panel-tab" id="tab-orders">
                    <div class="panel-header">
                        <div>
                            <h3>Sipariş Geçmişim</h3>
                            <p>Tüm geçmiş ve aktif siparişlerinizin fatura ve kargo dökümleri.</p>
                        </div>
                        <span class="badge-total-count">Toplam 1 Sipariş</span>
                    </div>

                    <div class="orders-list">
                        <article class="order-box-card">
                            <div class="order-box-top">
                                <div class="meta-col">
                                    <span class="label">Sipariş Kodu</span>
                                    <strong>#EMD-2026-9812</strong>
                                </div>
                                <div class="meta-col">
                                    <span class="label">Tarih</span>
                                    <strong>11 Eylül 2026</strong>
                                </div>
                                <div class="meta-col">
                                    <span class="label">Ödeme Yöntemi</span>
                                    <strong>Kredi Kartı (3 Taksit)</strong>
                                </div>
                                <div class="meta-col">
                                    <span class="label">Toplam Tutar</span>
                                    <strong class="price-highlight">800,00 TL</strong>
                                </div>
                                <div class="meta-col-status">
                                    <span class="status-pill status-shipping"><?php echo mis360_icon('truck', 14); ?> Kargoda</span>
                                </div>
                            </div>
                            <div class="order-box-body">
                                <div class="order-product-item">
                                    <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-451-300x300.jpg" alt="Carmen 3 Raflı Kitaplık" class="product-mini-thumb">
                                    <div class="product-mini-info">
                                        <h4>Carmen 3 Raflı Kitaplık – Çocuk Odası Eğitici Montessori</h4>
                                        <p class="product-specs">1. Sınıf Dayanıklı MDF • Çocuk Güvenlikli Yuvarlatılmış Kenar • 1 Adet</p>
                                        <span class="product-price">800,00 TL</span>
                                    </div>
                                    <div class="order-actions-group">
                                        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Siparis%20Takip%20No%20EMD-2026-9812" target="_blank" rel="noopener" class="emdief-btn btn-secondary btn-sm">
                                            <?php echo mis360_icon('truck', 14); ?>
                                            <span>Kargo Takip</span>
                                        </a>
                                        <button type="button" class="emdief-btn btn-outline btn-sm btn-sample-action" data-msg="Faturanız hazırlanıyor. E-posta adresinize gönderildi.">
                                            <?php echo mis360_icon('download', 14); ?>
                                            <span>E-Fatura</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Tab 3: Favorilerim -->
                <div class="account-panel-tab" id="tab-wishlist">
                    <div class="panel-header">
                        <div>
                            <h3>Favori Montessori Ürünlerim</h3>
                            <p>Beğendiğiniz ve daha sonra incelemek üzere kaydettiğiniz 1. sınıf MDF Montessori koleksiyonu.</p>
                        </div>
                        <span class="badge-total-count">3 Ürün Kayıtlı</span>
                    </div>

                    <div class="wishlist-cards-grid">
                        <div class="wishlist-item-card">
                            <div class="wish-img-wrap">
                                <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-448-300x300.jpg" alt="Safir 4 Raflı Kitaplık">
                                <span class="wish-badge-wood">1. Sınıf MDF</span>
                            </div>
                            <div class="wish-meta">
                                <div class="wish-rating">
                                    <span class="stars">★★★★★</span>
                                    <small>(28 Yorum)</small>
                                </div>
                                <h4>Safir 4 Raflı 1. Sınıf MDF Kitaplık</h4>
                                <div class="wish-price-row">
                                    <span class="wish-price">1.750,00 TL</span>
                                    <span class="in-stock-label">Stokta</span>
                                </div>
                                <a href="<?php echo esc_url(home_url('/shop/?s=safir')); ?>" class="emdief-btn btn-primary btn-sm btn-block">
                                    <?php echo mis360_icon('cart', 16); ?>
                                    <span>Sepete Ekle</span>
                                </a>
                            </div>
                        </div>

                        <div class="wishlist-item-card">
                            <div class="wish-img-wrap">
                                <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-442-300x300.jpg" alt="Safir 3 Raflı Kitaplık">
                                <span class="wish-badge-wood">1. Sınıf MDF</span>
                            </div>
                            <div class="wish-meta">
                                <div class="wish-rating">
                                    <span class="stars">★★★★★</span>
                                    <small>(19 Yorum)</small>
                                </div>
                                <h4>Safir 3 Raflı 1. Sınıf MDF Kitaplık</h4>
                                <div class="wish-price-row">
                                    <span class="wish-price">1.250,00 TL</span>
                                    <span class="in-stock-label">Stokta</span>
                                </div>
                                <a href="<?php echo esc_url(home_url('/shop/?s=safir')); ?>" class="emdief-btn btn-primary btn-sm btn-block">
                                    <?php echo mis360_icon('cart', 16); ?>
                                    <span>Sepete Ekle</span>
                                </a>
                            </div>
                        </div>

                        <div class="wishlist-item-card">
                            <div class="wish-img-wrap">
                                <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-436-300x300.jpg" alt="Safir 5 Raflı 1. Sınıf MDF Kitaplık">
                                <span class="wish-badge-wood">1. Sınıf MDF</span>
                            </div>
                            <div class="wish-meta">
                                <div class="wish-rating">
                                    <span class="stars">★★★★★</span>
                                    <small>(34 Yorum)</small>
                                </div>
                                <h4>Safir 5 Raflı 1. Sınıf MDF Kitaplık</h4>
                                <div class="wish-price-row">
                                    <span class="wish-price">2.250,00 TL</span>
                                    <span class="in-stock-label">Stokta</span>
                                </div>
                                <a href="<?php echo esc_url(home_url('/shop/?s=safir')); ?>" class="emdief-btn btn-primary btn-sm btn-block">
                                    <?php echo mis360_icon('cart', 16); ?>
                                    <span>Sepete Ekle</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Adreslerim -->
                <div class="account-panel-tab" id="tab-addresses">
                    <div class="panel-header">
                        <div>
                            <h3>Kayıtlı Teslimat ve Fatura Adreslerim</h3>
                            <p>Siparişlerinizin hasarsız ve zamanında teslimatı için adreslerinizi yönetebilirsiniz.</p>
                        </div>
                    </div>

                    <div class="addresses-grid">
                        <div class="address-card is-default">
                            <div class="address-header">
                                <div class="address-title">
                                    <span class="addr-icon"><?php echo mis360_icon('home', 18); ?></span>
                                    <h4>Ev Teslimat Adresim</h4>
                                </div>
                                <span class="badge-default-addr">Varsayılan Teslimat</span>
                            </div>
                            <div class="address-body">
                                <strong><?php echo esc_html($display_name); ?></strong>
                                <p>Montessori Mahallesi, Montessori Tasarım Caddesi No: 12/4<br>Kadıköy / İstanbul, Türkiye</p>
                                <span class="addr-phone">Tel: +90 (537) 477 87 66</span>
                            </div>
                            <div class="address-footer">
                                <button type="button" class="btn-text-action btn-sample-action" data-msg="Adres düzenleme formu açılıyor.">Düzenle</button>
                            </div>
                        </div>

                        <div class="address-card">
                            <div class="address-header">
                                <div class="address-title">
                                    <span class="addr-icon"><?php echo mis360_icon('map-pin', 18); ?></span>
                                    <h4>Fatura Adresim</h4>
                                </div>
                            </div>
                            <div class="address-body">
                                <strong><?php echo esc_html($display_name); ?></strong>
                                <p>Bireysel Fatura Adresi<br>Kadıköy / İstanbul, Türkiye</p>
                                <span class="addr-phone">TC Kimlik: Kayıtlı</span>
                            </div>
                            <div class="address-footer">
                                <button type="button" class="btn-text-action btn-sample-action" data-msg="Fatura adresi düzenleme formu açılıyor.">Düzenle</button>
                            </div>
                        </div>

                        <div class="address-card add-new-address btn-sample-action" data-msg="Yeni teslimat adresi ekleme penceresi açılıyor.">
                            <div class="add-new-content">
                                <span class="plus-icon">+</span>
                                <strong>Yeni Adres Tanımla</strong>
                                <small>Farklı bir teslimat noktası ekleyin</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Kuponlarım & Puanlarım -->
                <div class="account-panel-tab" id="tab-coupons">
                    <div class="panel-header">
                        <div>
                            <h3>Montessori Puanlarım & İndirim Kuponlarım</h3>
                            <p>Alışverişlerinizden kazandığınız puanlar ve geçerli indirim kodları.</p>
                        </div>
                        <div class="points-balance-tag">
                            <span>Kullanılabilir Bakiye:</span>
                            <strong>5,00 TL (1.000 Puan)</strong>
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

                        <div class="coupon-ticket-card">
                            <div class="coupon-left-ticket ticket-emerald">
                                <span class="val">5 TL</span>
                                <span class="sub">PUAN İNDİRİMİ</span>
                            </div>
                            <div class="coupon-right-ticket">
                                <div class="coupon-tag-row">
                                    <span class="badge-club">Sadakat Puanı</span>
                                    <span class="badge-active">Aktif</span>
                                </div>
                                <h4>1.000 Puan İndirim Çeki</h4>
                                <p>1.000 Montessori Puanınız ile sepetinizde anında 5 TL nakit indirim uygulayın.</p>
                                <div class="coupon-action-row">
                                    <code class="ticket-code">PUAN5</code>
                                    <button type="button" class="btn-ticket-copy btn-copy-code" data-copy="PUAN5">
                                        <?php echo mis360_icon('copy', 14); ?>
                                        <span>Kodu Kopyala</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Puan Açıklama ve Kazanım Tablosu -->
                    <div class="points-info-card">
                        <div class="points-info-header">
                            <span class="points-star">⭐</span>
                            <h4>Montessori Puan Sistemi Nasıl Çalışır?</h4>
                        </div>
                        <ul class="points-rules-list">
                            <li><?php echo mis360_icon('check', 16); ?> <strong>1.000 Montessori Puanı = 5,00 TL</strong> nakit indirim değerindedir.</li>
                            <li><?php echo mis360_icon('check', 16); ?> Her 1.000 TL alışverişinizde <strong>1.000 Puan (5 TL)</strong> kazanırsınız.</li>
                            <li><?php echo mis360_icon('check', 16); ?> Ürün değerlendirmesi ve fotoğraf eklemelerinde ekstra <strong>200 Puan (1 TL)</strong> kazanırsınız.</li>
                            <li><?php echo mis360_icon('check', 16); ?> Puanlarınızı biriktirip sepet aşamasında 1.000 Puan ve katları (1.000 Puan = 5 TL) olarak indirim çeki şeklinde kullanabilirsiniz.</li>
                        </ul>
                    </div>
                </div>

                <!-- Tab 6: Hesap & Güvenlik -->
                <div class="account-panel-tab" id="tab-details">
                    <div class="panel-header">
                        <div>
                            <h3>Hesap Ayarları & Profil Güvenliği</h3>
                            <p>Kişisel bilgilerinizi, şifrenizi ve Montessori profil tercihlerini güncelleyin.</p>
                        </div>
                    </div>

                    <form class="account-settings-form" method="post" action="">
                        <div class="form-section-title">Kişisel Bilgiler</div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label><?php esc_html_e('Ad Soyad', 'mis360-mobilya'); ?></label>
                                <input type="text" class="form-input" value="<?php echo esc_attr($display_name); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('E-posta Adresi', 'mis360-mobilya'); ?></label>
                                <input type="email" class="form-input" value="<?php echo esc_attr($user_email); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('Telefon Numarası', 'mis360-mobilya'); ?></label>
                                <div class="input-with-badge">
                                    <input type="tel" class="form-input" value="+90 537 477 87 66">
                                    <span class="input-verified-badge">✓ Doğrulandı</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('Çocuğunuzun Doğum Yılı (Opsiyonel)', 'mis360-mobilya'); ?></label>
                                <input type="text" class="form-input" placeholder="Örn: 2023 (Yaşa özel ürün önerileri için)">
                            </div>
                        </div>

                        <div class="form-section-title mt-6">Montessori Profil Rozeti</div>
                        <div class="avatar-selection-panel">
                            <div class="avatar-preview-item is-selected">
                                <div class="avatar-ring">
                                    <?php echo mis360_teddy_bear_avatar(56); ?>
                                </div>
                                <div class="avatar-meta">
                                    <strong>Montessori Ayıcık (Özel İskandinav Tasarım)</strong>
                                    <span>Aktif Müşteri Profil Rozetiniz</span>
                                </div>
                                <span class="avatar-check-badge">✓ Seçili</span>
                            </div>
                        </div>

                        <div class="form-section-title mt-6">Şifre & Güvenlik</div>
                        <div class="security-box">
                            <div class="security-row">
                                <div>
                                    <strong>Hesap Şifresi</strong>
                                    <p>Son değişiklik: 1 ay önce. Güçlü bir şifre kullanmanız önerilir.</p>
                                </div>
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="emdief-btn btn-secondary btn-sm" target="_blank">
                                    <span>Şifremi Değiştir</span>
                                </a>
                            </div>
                        </div>

                        <div class="form-actions mt-6">
                            <button type="button" class="emdief-btn btn-primary btn-md btn-sample-action" data-msg="Değişiklikleriniz başarıyla kaydedildi!">
                                <span>Değişiklikleri Kaydet</span>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>

    <?php else: ?>
        <!-- Kullanıcı Giriş Yapmamışsa: İskandinav Kurumsal Karşılama Kartı -->
        <div class="account-guest-view">
            <div class="account-guest-card">
                <div class="guest-bear-avatar">
                    <?php echo mis360_teddy_bear_avatar(110); ?>
                </div>
                <span class="guest-club-tag">EMDIEF MONTESSORI PRIVILEGE</span>
                <h2>Montessori Ailemize Hoş Geldiniz</h2>
                <p>
                    Çocuğunuzun bağımsız dünyasını destekleyen 1. sınıf MDF Montessori tasarımlarınızı, sipariş kargo hareketlerinizi ve size özel Montessori puanlarınızı buradan kolayca yönetebilirsiniz.
                </p>

                <div class="guest-privilege-list">
                    <div class="priv-item">
                        <span class="priv-icon">✓</span>
                        <span>Canlı Kargo ve Sipariş Takip Paneli</span>
                    </div>
                    <div class="priv-item">
                        <span class="priv-icon">✓</span>
                        <span>Her Alışverişte Montessori Puan Kazanımı</span>
                    </div>
                    <div class="priv-item">
                        <span class="priv-icon">✓</span>
                        <span>%10 Hoş Geldin İndirim Kuponu</span>
                    </div>
                    <div class="priv-item">
                        <span class="priv-icon">✓</span>
                        <span>Öncelikli WhatsApp Müşteri Destek Hattı</span>
                    </div>
                </div>

                <div class="guest-actions">
                    <button type="button" class="emdief-btn btn-primary btn-lg" id="guest-login-open">
                        <span>Hesabıma Giriş Yap / Kayıt Ol</span>
                        <?php echo mis360_icon('arrow-right', 18); ?>
                    </button>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="emdief-btn btn-secondary btn-lg">
                        <span>Montessori Kataloğunu İncele</span>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('demo', '1')); ?>" class="emdief-btn btn-outline btn-md" style="margin-top:0.4rem;">
                        <span>🧸 Paneli Canlı İncele (Demo Görünüm)</span>
                    </a>
                </div>
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

    // Kopyalama Butonları (Kupon & Takip Kodu)
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

    // Örnek Aksiyon Geri Bildirimleri
    const actionBtns = document.querySelectorAll('.btn-sample-action');
    actionBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const msg = btn.getAttribute('data-msg') || 'İşleminiz kaydedildi.';
            alert(msg);
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
