<?php
/**
 * Theme Header
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Üst Duyuru Çubuğu (Topbar) -->
<div class="emdief-topbar">
    <div class="emdief-container">
        <div class="topbar-inner">
            <div class="topbar-left">
                <span class="topbar-badge">ÖNCELİKLİ İMALAT</span>
                <span class="topbar-text"><?php echo esc_html(get_theme_mod('mis360_topbar_text', "13:00'a Kadar Verilen Siparişler Öncelikli İmalata Alınır! | 1500 TL Üzeri Ücretsiz Kargo")); ?></span>
            </div>
            <div class="topbar-right">
                <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('mis360_phone', '+90 537 477 87 66'))); ?>" class="topbar-link">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('phone', 14) : '📞'; ?>
                    <span><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></span>
                </a>
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" target="_blank" rel="noopener" class="topbar-link topbar-wa">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 14) : '💬'; ?>
                    <span>WhatsApp Sipariş</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Ana Üst Başlık (Main Header) -->
<header id="masthead" class="emdief-header">
    <div class="emdief-container">
        <div class="header-main">
            <!-- Mobil Menü Butonu -->
            <button type="button" class="emdief-mobile-toggle" id="emdief-mobile-menu-trigger" aria-label="<?php esc_attr_e('Menüyü Aç', 'mis360-mobilya'); ?>">
                <?php echo function_exists('mis360_icon') ? mis360_icon('menu', 26) : '☰'; ?>
            </button>

            <!-- Logo -->
            <div class="emdief-brand">
                <?php if (has_custom_logo()): ?>
                    <?php the_custom_logo(); ?>
                <?php else: ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-link">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/emdief-home-logo.webp'); ?>" alt="<?php bloginfo('name'); ?>" class="brand-logo" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                        <span class="brand-text-fallback" style="display:none; font-weight:800; font-size:1.5rem; color:var(--emd-text-main);">Emdief<span style="color:var(--emd-primary);">Home</span></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Masaüstü Canlı Ürün Arama Çubuğu -->
            <div class="emdief-search-box">
                <form role="search" method="get" class="emdief-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-wrapper">
                        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Montessori kitaplık, ahşap oyuncak veya ürün adı arayın...', 'mis360-mobilya'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Ara', 'mis360-mobilya'); ?>">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('search', 20) : '🔍'; ?>
                        </button>
                    </div>
                </form>
                <div class="search-quick-tags">
                    <span class="tags-label"><?php esc_html_e('Trend:', 'mis360-mobilya'); ?></span>
                    <a href="<?php echo esc_url(home_url('/?s=carmen&post_type=product')); ?>">Carmen</a>
                    <a href="<?php echo esc_url(home_url('/?s=safir&post_type=product')); ?>">Safir</a>
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplık') : home_url('/?s=kitapl%C4%B1k&post_type=product')); ?>">Kitaplık</a>
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/?s=oyuncak&post_type=product')); ?>">Oyuncak</a>
                </div>
            </div>

            <!-- Sağ Aksiyon Butonları (Arama, Giriş / Hesabım, Sepet) -->
            <div class="emdief-header-actions">
                <!-- Mobil Arama Butonu -->
                <button type="button" class="action-btn action-search-mobile" id="emdief-mobile-search-toggle" aria-label="<?php esc_attr_e('Arama Aç', 'mis360-mobilya'); ?>">
                    <span class="action-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('search', 20) : '🔍'; ?></span>
                </button>

                <!-- Hesabım / Giriş Butonu (Giriş Yapılmamışsa Popup Açar) -->
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" class="action-btn action-account" title="<?php esc_attr_e('Hesabım', 'mis360-mobilya'); ?>">
                        <span class="action-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 22) : '👤'; ?></span>
                        <span class="action-label">
                            <small><?php esc_html_e('Hoş Geldiniz', 'mis360-mobilya'); ?></small>
                            <strong><?php echo esc_html(wp_get_current_user()->display_name); ?></strong>
                        </span>
                    </a>
                <?php else: ?>
                    <button type="button" class="action-btn action-account" id="emdief-login-trigger" aria-label="<?php esc_attr_e('Giriş Yap', 'mis360-mobilya'); ?>">
                        <span class="action-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 22) : '👤'; ?></span>
                        <span class="action-label">
                            <small><?php esc_html_e('Giriş Yapın', 'mis360-mobilya'); ?></small>
                            <strong><?php esc_html_e('Hesabım', 'mis360-mobilya'); ?></strong>
                        </span>
                    </button>
                <?php endif; ?>

                <!-- Sepet Butonu (Daima Görünür) -->
                <button type="button" class="action-btn action-cart" id="emdief-cart-trigger" aria-label="<?php esc_attr_e('Sepeti Aç', 'mis360-mobilya'); ?>">
                    <span class="action-icon">
                        <?php echo function_exists('mis360_icon') ? mis360_icon('cart', 22) : '🛒'; ?>
                        <span class="emdief-cart-count" id="emdief-cart-count">
                            <?php echo (class_exists('WooCommerce') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0'; ?>
                        </span>
                    </span>
                    <span class="action-label">
                        <small><?php esc_html_e('Sepetim', 'mis360-mobilya'); ?></small>
                        <strong class="emdief-cart-total"><?php echo (class_exists('WooCommerce') && WC()->cart) ? WC()->cart->get_cart_subtotal() : '0,00 TL'; ?></strong>
                    </span>
                </button>
            </div>
        </div>

        <!-- Mobil Hızlı Arama Açılır Barı -->
        <div class="emdief-mobile-search-bar" id="emdief-mobile-search-bar">
            <form role="search" method="get" class="emdief-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-wrapper">
                    <input type="search" class="search-field" placeholder="<?php esc_attr_e('Montessori kitaplık, ahşap oyuncak...', 'mis360-mobilya'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                    <input type="hidden" name="post_type" value="product">
                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Ara', 'mis360-mobilya'); ?>">
                        <?php echo function_exists('mis360_icon') ? mis360_icon('search', 18) : '🔍'; ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Ana Menü Barı (Desktop Navigation) -->
        <nav class="emdief-nav-bar" aria-label="<?php esc_attr_e('Ana Gezinti', 'mis360-mobilya'); ?>">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'emdief-nav-menu',
                    'fallback_cb'    => false,
                ]);
            } else {
                ?>
                <ul class="emdief-nav-menu">
                    <li class="<?php echo is_front_page() ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Anasayfa', 'mis360-mobilya'); ?></a></li>
                    <?php if (class_exists('WooCommerce')): ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Tüm Koleksiyon', 'mis360-mobilya'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplık') : home_url('/shop/?s=kitapl%C4%B1k')); ?>">📚 <?php esc_html_e('Montessori Kitaplıklar', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/shop/?s=duvar+masas%C4%B1')); ?>">🪑 <?php esc_html_e('Katlanabilir Duvar Masaları', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/shop/?s=oyuncak')); ?>">🧸 <?php esc_html_e('Eğitici Oyuncaklar', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duzenleyiciler', 'duzenleyici') : home_url('/shop/?s=duzenleyici')); ?>">📦 <?php esc_html_e('Düzenleyiciler & Raflar', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>">🎬 <?php esc_html_e('Kurulum Videoları', 'mis360-mobilya'); ?></a></li>
                    <li class="menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>"><?php esc_html_e('Kurumsal', 'mis360-mobilya'); ?> <span class="nav-arrow-down">▾</span></a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>">🧸 <?php esc_html_e('Hakkımızda & Montessori Felsefesi', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/teslimat-ve-iade/')); ?>">📦 <?php esc_html_e('Kargo & İade Koşulları', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>">📍 <?php esc_html_e('İletişim & Atölye', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>">🛡️ <?php esc_html_e('Gizlilik & KVKK', 'mis360-mobilya'); ?></a></li>
                        </ul>
                    </li>
                </ul>
                <?php
            }
            ?>
            <div class="nav-extra-badge">
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') . '?on_sale=1' : home_url('/shop/')); ?>" class="badge-link">
                    <span class="flame-icon">🔥</span>
                    <span><?php esc_html_e('Haftanın İndirimleri', 'mis360-mobilya'); ?></span>
                </a>
            </div>
        </nav>
    </div>
</header>

<!-- Mobil Menü Çekmecesi (Mobile Offcanvas Menu) -->
<div class="emdief-drawer" id="emdief-mobile-drawer" aria-hidden="true">
    <div class="emdief-drawer-overlay" id="emdief-mobile-overlay"></div>
    <div class="emdief-drawer-panel drawer-left">
        <div class="drawer-header">
            <div class="drawer-header-brand">
                <span class="drawer-brand-name">Emdief<strong>Home</strong></span>
                <span class="drawer-brand-sub">Montessori Çocuk Odası</span>
            </div>
            <button type="button" class="drawer-close" id="emdief-mobile-close" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
                <?php echo function_exists('mis360_icon') ? mis360_icon('close', 20) : '✕'; ?>
            </button>
        </div>
        <div class="drawer-content">
            <!-- 1. Öne Çıkan WhatsApp Canlı Destek Butonu -->
            <div class="drawer-wa-card-wrap">
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=<?php echo rawurlencode('Merhaba Emdief Home, siparişim ve mobilyalar hakkında destek almak istiyorum.'); ?>" target="_blank" rel="noopener" class="drawer-whatsapp-btn">
                    <div class="wa-icon-bubble">
                        <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 22) : '💬'; ?>
                    </div>
                    <div class="wa-text-col">
                        <div class="wa-top-row">
                            <span class="wa-title">WhatsApp Canlı Destek</span>
                            <span class="wa-online-pill"><span class="wa-online-dot"></span> Canlı</span>
                        </div>
                        <span class="wa-sub">Sipariş, Kurulum & Özel Ölçü Hattı</span>
                    </div>
                    <span class="wa-arrow">➜</span>
                </a>
            </div>

            <!-- 2. Hızlı Arama Kutusu -->
            <div class="drawer-mobile-search">
                <form role="search" method="get" class="emdief-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-wrapper">
                        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Montessori kitaplık, raf, oyuncak...', 'mis360-mobilya'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Ara', 'mis360-mobilya'); ?>">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('search', 16) : '🔍'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Hızlı Menü Çipleri -->
            <div class="drawer-quick-pills">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="quick-pill">
                    <span>🏠 Anasayfa</span>
                </a>
                <?php if (class_exists('WooCommerce')): ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="quick-pill">
                        <span>🛍️ Tüm Ürünler</span>
                    </a>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop') . '?on_sale=1'); ?>" class="quick-pill pill-sale">
                        <span>🔥 İndirimler</span>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="quick-pill pill-video">
                    <span>🎬 Kurulum</span>
                </a>
            </div>

            <!-- 4. Kategorize Edilmiş Akordeon Menü Grupları -->
            <div class="drawer-categorized-nav">
                <!-- Grup 1: Montessori Ürün Kategorileri (Varsayılan Açık) -->
                <div class="drawer-group is-open">
                    <button type="button" class="drawer-group-toggle" aria-expanded="true">
                        <span class="group-title">
                            <span class="group-emoji">🧸</span>
                            <strong>Montessori Ürünleri</strong>
                        </span>
                        <span class="group-toggle-icon">▾</span>
                    </button>
                    <ul class="drawer-group-links">
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplık') : home_url('/shop/?s=kitapl%C4%B1k')); ?>">
                                <span class="link-bullet">📚</span>
                                <span>Montessori Kitaplıklar</span>
                                <span class="link-badge">Popüler</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/shop/?s=oyuncak')); ?>">
                                <span class="link-bullet">🧩</span>
                                <span>Eğitici Ahşap Oyuncaklar</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duzenleyiciler', 'duzenleyici') : home_url('/shop/?s=duzenleyici')); ?>">
                                <span class="link-bullet">📦</span>
                                <span>Oyuncak & Eşya Düzenleyiciler</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duvar-rafi', 'raf') : home_url('/shop/?s=raf')); ?>">
                                <span class="link-bullet">🪟</span>
                                <span>Duvar & Banyo Rafları</span>
                            </a>
                        </li>
                        <li class="group-all-link">
                            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>">
                                <span>Tüm Montessori Koleksiyonunu Gör</span>
                                <span>➜</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Grup 2: Kurulum & Yardım Merkezi -->
                <div class="drawer-group">
                    <button type="button" class="drawer-group-toggle" aria-expanded="false">
                        <span class="group-title">
                            <span class="group-emoji">🎬</span>
                            <strong>Yardım & Kurulum</strong>
                        </span>
                        <span class="group-toggle-icon">▾</span>
                    </button>
                    <ul class="drawer-group-links" style="display: none;">
                        <li>
                            <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>">
                                <span class="link-bullet">🎥</span>
                                <span>Montaj & Kurulum Videoları</span>
                                <span class="link-badge badge-video">Video</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/yardim-merkezi/#sikca-sorulan-sorular')); ?>">
                                <span class="link-bullet">❓</span>
                                <span>Sıkça Sorulan Sorular (SSS)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/teslimat-ve-iade/')); ?>">
                                <span class="link-bullet">🚚</span>
                                <span>Teslimat & İade Koşulları</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=<?php echo rawurlencode('Eksik parça / vida talebinde bulunmak istiyorum.'); ?>" target="_blank" rel="noopener">
                                <span class="link-bullet">🛠️</span>
                                <span>Eksik Parça & Garanti Talebi</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Grup 3: Kurumsal Bilgiler -->
                <div class="drawer-group">
                    <button type="button" class="drawer-group-toggle" aria-expanded="false">
                        <span class="group-title">
                            <span class="group-emoji">ℹ️</span>
                            <strong>Kurumsal</strong>
                        </span>
                        <span class="group-toggle-icon">▾</span>
                    </button>
                    <ul class="drawer-group-links" style="display: none;">
                        <li>
                            <a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>">
                                <span class="link-bullet">🧸</span>
                                <span>Hakkımızda & Montessori Felsefesi</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>">
                                <span class="link-bullet">🛡️</span>
                                <span>Gizlilik Politikası & KVKK</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>">
                                <span class="link-bullet">🍪</span>
                                <span>Çerez Politikası</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>">
                                <span class="link-bullet">📝</span>
                                <span>Mesafeli Satış Sözleşmesi</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/iletisim/')); ?>">
                                <span class="link-bullet">📞</span>
                                <span><?php esc_html_e('İletişim', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Grup 4: Üyelik & Hesap Alanı -->
                <div class="drawer-account-row">
                    <?php if (is_user_logged_in()): ?>
                        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/hesabim/')); ?>" class="drawer-acc-btn">
                            <span class="acc-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 18) : '👤'; ?></span>
                            <span>Hesabım (<?php echo esc_html(wp_get_current_user()->display_name); ?>)</span>
                        </a>
                        <a href="<?php echo esc_url(wc_logout_url(home_url('/'))); ?>" class="drawer-logout-btn" title="<?php esc_attr_e('Çıkış Yap', 'mis360-mobilya'); ?>">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('logout', 18) : '🚪'; ?>
                        </a>
                    <?php else: ?>
                        <button type="button" class="drawer-acc-btn" id="drawer-login-trigger">
                            <span class="acc-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 18) : '👤'; ?></span>
                            <span>Giriş Yap / Kayıt Ol</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="drawer-contact-info">
                <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('mis360_phone', '+90 537 477 87 66'))); ?>" class="contact-pill">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('phone', 16) : '📞'; ?>
                    <span><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Mini-Cart Yan Çekmecesi (Right Drawer) -->
<div class="emdief-drawer" id="emdief-cart-drawer" aria-hidden="true">
    <div class="emdief-drawer-overlay" id="emdief-cart-overlay"></div>
    <div class="emdief-drawer-panel drawer-right">
        <div class="drawer-header">
            <div class="drawer-title-group">
                <h3><?php esc_html_e('Alışveriş Sepetim', 'mis360-mobilya'); ?></h3>
                <span class="drawer-count-badge" id="emdief-drawer-count-badge"><?php echo (class_exists('WooCommerce') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0'; ?> <?php esc_html_e('ürün', 'mis360-mobilya'); ?></span>
            </div>
            <button type="button" class="drawer-close" id="emdief-cart-close" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
                <?php echo function_exists('mis360_icon') ? mis360_icon('close', 20) : '✕'; ?>
            </button>
        </div>
        <?php
        if (function_exists('mis360_render_drawer_cart_content')) {
            mis360_render_drawer_cart_content();
        } else {
            ?>
            <div class="emdief-cart-empty">
                <div class="empty-bear-wrap">
                    <?php echo function_exists('mis360_crying_bear') ? mis360_crying_bear(125, 115, 'animated-drawer-crying-bear') : '<div class="empty-icon">🧸</div>'; ?>
                </div>
                <div class="empty-bear-badge">🥺 Ayıcık Ağlıyor!</div>
                <h3><?php esc_html_e('Sepetiniz Bomboş Kaldı...', 'mis360-mobilya'); ?></h3>
                <p><?php esc_html_e('Montessori felsefesine uygun 1. sınıf kaliteli MDF ürünlerimizi ekleyin, sevimli ayıcığımızın gözyaşları dinsin!', 'mis360-mobilya'); ?></p>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- ŞIK GİRİŞ & KAYIT POPUP MODALI -->
<div class="emdief-modal" id="emdief-auth-modal" aria-hidden="true">
    <div class="emdief-modal-overlay" id="emdief-auth-overlay"></div>
    <div class="emdief-modal-dialog">
        <button type="button" class="emdief-modal-close" id="emdief-auth-close" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
            <?php echo function_exists('mis360_icon') ? mis360_icon('close', 20) : '✕'; ?>
        </button>

        <div class="auth-modal-header">
            <div class="auth-modal-icon">🧸</div>
            <h3 class="auth-modal-title"><?php esc_html_e('Emdief Home Ailesine Hoş Geldiniz', 'mis360-mobilya'); ?></h3>
            <p class="auth-modal-subtitle"><?php esc_html_e('Montessori doğal mobilya dünyasına erişin, siparişlerinizi kolayca yönetin.', 'mis360-mobilya'); ?></p>
            
            <div class="auth-tabs">
                <button type="button" class="auth-tab-btn is-active" data-tab="login"><?php esc_html_e('Giriş Yap', 'mis360-mobilya'); ?></button>
                <button type="button" class="auth-tab-btn" data-tab="register"><?php esc_html_e('Kayıt Ol', 'mis360-mobilya'); ?></button>
            </div>
        </div>

        <div class="auth-modal-body">
            <!-- Giriş Formu Paneli -->
            <div class="auth-form-panel is-active" id="auth-tab-login">
                <form method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" class="emdief-auth-form">
                    <div class="form-group">
                        <label for="emdief-user-login"><?php esc_html_e('E-posta veya Kullanıcı Adı', 'mis360-mobilya'); ?></label>
                        <input type="text" name="log" id="emdief-user-login" class="form-input" required placeholder="ornek@mail.com" autocomplete="username">
                    </div>
                    <div class="form-group">
                        <div class="d-flex-between">
                            <label for="emdief-user-pass"><?php esc_html_e('Şifre', 'mis360-mobilya'); ?></label>
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-pass-link" target="_blank"><?php esc_html_e('Şifremi Unuttum?', 'mis360-mobilya'); ?></a>
                        </div>
                        <div class="input-password-wrap">
                            <input type="password" name="pwd" id="emdief-user-pass" class="form-input" required placeholder="••••••••" autocomplete="current-password">
                            <button type="button" class="toggle-password-btn" id="emdief-toggle-pass" aria-label="<?php esc_attr_e('Şifreyi Göster', 'mis360-mobilya'); ?>">👁️</button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="remember-label">
                            <input type="checkbox" name="rememberme" value="forever" checked>
                            <span><?php esc_html_e('Beni Hatırla', 'mis360-mobilya'); ?></span>
                        </label>
                    </div>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI'] ?? home_url('/')); ?>">
                    <button type="submit" class="emdief-btn btn-primary btn-block btn-lg auth-submit-btn">
                        <span><?php esc_html_e('Giriş Yap', 'mis360-mobilya'); ?></span>
                        <?php echo function_exists('mis360_icon') ? mis360_icon('arrow-right', 18) : '→'; ?>
                    </button>
                </form>
            </div>

            <!-- Kayıt Formu Paneli -->
            <div class="auth-form-panel" id="auth-tab-register">
                <form method="post" action="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : wp_registration_url()); ?>" class="emdief-auth-form">
                    <div class="form-group">
                        <label for="emdief-reg-email"><?php esc_html_e('E-posta Adresi', 'mis360-mobilya'); ?></label>
                        <input type="email" name="<?php echo class_exists('WooCommerce') ? 'email' : 'user_email'; ?>" id="emdief-reg-email" class="form-input" required placeholder="ornek@mail.com">
                    </div>
                    <?php if (class_exists('WooCommerce')): ?>
                        <div class="form-group">
                            <label for="emdief-reg-pass"><?php esc_html_e('Şifre', 'mis360-mobilya'); ?></label>
                            <input type="password" name="password" id="emdief-reg-pass" class="form-input" required placeholder="<?php esc_attr_e('Güvenli bir şifre belirleyin', 'mis360-mobilya'); ?>">
                        </div>
                        <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                        <input type="hidden" name="register" value="1">
                    <?php else: ?>
                        <div class="form-group">
                            <label for="emdief-reg-user"><?php esc_html_e('Kullanıcı Adı', 'mis360-mobilya'); ?></label>
                            <input type="text" name="user_login" id="emdief-reg-user" class="form-input" required placeholder="<?php esc_attr_e('kullaniciadi', 'mis360-mobilya'); ?>">
                        </div>
                    <?php endif; ?>
                    <p class="form-terms-note">
                        <?php esc_html_e('Kayıt olarak Üyelik Sözleşmesini ve Kişisel Verilerin Korunması Politikasını kabul etmiş sayılırsınız.', 'mis360-mobilya'); ?>
                    </p>
                    <button type="submit" class="emdief-btn btn-primary btn-block btn-lg auth-submit-btn">
                        <span><?php esc_html_e('Ücretsiz Hesap Oluştur', 'mis360-mobilya'); ?></span>
                        <?php echo function_exists('mis360_icon') ? mis360_icon('sparkles', 18) : '✨'; ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="auth-modal-footer">
            <div class="security-badge">
                <span class="sec-icon">🔒</span>
                <span><?php esc_html_e('256-Bit SSL şifreleme ile verileriniz %100 güvende.', 'mis360-mobilya'); ?></span>
            </div>
        </div>
    </div>
</div>

<main id="primary" class="site-main">
