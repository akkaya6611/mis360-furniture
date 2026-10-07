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

<!-- Ãœst Duyuru Ã‡ubuÄŸu (Topbar) -->
<div class="emdief-topbar">
    <div class="emdief-container">
        <div class="topbar-inner">
            <div class="topbar-left">
                <span class="topbar-badge">Ã–NCELÄ°KLÄ° Ä°MALAT</span>
                <span class="topbar-text"><?php echo esc_html(get_theme_mod('mis360_topbar_text', "13:00'a Kadar Verilen SipariÅŸler Ã–ncelikli Ä°malata AlÄ±nÄ±r! | 1500 TL Ãœzeri Ãœcretsiz Kargo")); ?></span>
            </div>
            <div class="topbar-right">
                <?php if (get_theme_mod('mis360_etbis_show_topbar', true)): ?>
                    <a href="<?php echo esc_url(get_theme_mod('mis360_etbis_url', 'https://etbis.ticaret.gov.tr/tr/SiteSorgulamaSonuc?siteId=416b7951-542c-4db2-a53f-d5fca36e23fb')); ?>" target="_blank" rel="noopener noreferrer" class="topbar-link topbar-etbis" title="<?php esc_attr_e('T.C. Ticaret BakanlÄ±ÄŸÄ± ETBÄ°S KayÄ±tlÄ± DoÄŸrulanmÄ±ÅŸ MaÄŸaza', 'mis360-mobilya'); ?>">
                        <span class="etbis-dot"></span>
                        <strong>ğŸ›ï¸ ETBÄ°S</strong>
                        <span><?php esc_html_e('KayÄ±tlÄ± MaÄŸaza', 'mis360-mobilya'); ?></span>
                    </a>
                <?php endif; ?>
                <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('mis360_phone', '+90 537 477 87 66'))); ?>" class="topbar-link">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('phone', 14) : 'ğŸ“'; ?>
                    <span><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></span>
                </a>
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" target="_blank" rel="noopener" class="topbar-link topbar-wa">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 14) : 'ğŸ’¬'; ?>
                    <span>WhatsApp SipariÅŸ</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Ana Ãœst BaÅŸlÄ±k (Main Header) -->
<header id="masthead" class="emdief-header">
    <div class="emdief-container">
        <div class="header-main">
            <!-- Mobil MenÃ¼ Butonu -->
            <button type="button" class="emdief-mobile-toggle" id="emdief-mobile-menu-trigger" aria-label="<?php esc_attr_e('MenÃ¼yÃ¼ AÃ§', 'mis360-mobilya'); ?>">
                <?php echo function_exists('mis360_icon') ? mis360_icon('menu', 26) : 'â˜°'; ?>
            </button>

            <!-- Logo -->
            <div class="emdief-brand">
                <?php if (has_custom_logo()): ?>
                    <?php the_custom_logo(); ?>
                <?php else: ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-link">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/emdief-home-logo.webp'); ?>" alt="<?php bloginfo('name'); ?>" class="brand-logo" width="160" height="49" decoding="async" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                        <span class="brand-text-fallback" style="display:none; font-weight:800; font-size:1.5rem; color:var(--emd-text-main);">Emdief<span style="color:var(--emd-primary);">Home</span></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- MasaÃ¼stÃ¼ CanlÄ± ÃœrÃ¼n Arama Ã‡ubuÄŸu -->
            <div class="emdief-search-box">
                <form role="search" method="get" class="emdief-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-wrapper">
                        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Montessori kitaplÄ±k, ahÅŸap oyuncak veya Ã¼rÃ¼n adÄ± arayÄ±n...', 'mis360-mobilya'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Ara', 'mis360-mobilya'); ?>">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('search', 20) : 'ğŸ”'; ?>
                        </button>
                    </div>
                </form>
                <div class="search-quick-tags">
                    <span class="tags-label"><?php esc_html_e('Trend:', 'mis360-mobilya'); ?></span>
                    <a href="<?php echo esc_url(home_url('/?s=carmen&post_type=product')); ?>">Carmen</a>
                    <a href="<?php echo esc_url(home_url('/?s=safir&post_type=product')); ?>">Safir</a>
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplÄ±k') : home_url('/?s=kitapl%C4%B1k&post_type=product')); ?>">KitaplÄ±k</a>
                    <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/?s=oyuncak&post_type=product')); ?>">Oyuncak</a>
                </div>
            </div>

            <!-- SaÄŸ Aksiyon ButonlarÄ± (Arama, GiriÅŸ / HesabÄ±m, Sepet) -->
            <div class="emdief-header-actions">
                <!-- Mobil Arama Butonu -->
                <button type="button" class="action-btn action-search-mobile" id="emdief-mobile-search-toggle" aria-label="<?php esc_attr_e('Arama AÃ§', 'mis360-mobilya'); ?>">
                    <span class="action-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('search', 20) : 'ğŸ”'; ?></span>
                </button>

                <!-- HesabÄ±m / GiriÅŸ Butonu (GiriÅŸ YapÄ±lmamÄ±ÅŸsa Popup AÃ§ar) -->
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" class="action-btn action-account" title="<?php esc_attr_e('HesabÄ±m', 'mis360-mobilya'); ?>">
                        <span class="action-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 22) : 'ğŸ‘¤'; ?></span>
                        <span class="action-label">
                            <small><?php esc_html_e('HoÅŸ Geldiniz', 'mis360-mobilya'); ?></small>
                            <strong><?php echo esc_html(wp_get_current_user()->display_name); ?></strong>
                        </span>
                    </a>
                <?php else: ?>
                    <button type="button" class="action-btn action-account" id="emdief-login-trigger" aria-label="<?php esc_attr_e('GiriÅŸ Yap', 'mis360-mobilya'); ?>">
                        <span class="action-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 22) : 'ğŸ‘¤'; ?></span>
                        <span class="action-label">
                            <small><?php esc_html_e('GiriÅŸ YapÄ±n', 'mis360-mobilya'); ?></small>
                            <strong><?php esc_html_e('HesabÄ±m', 'mis360-mobilya'); ?></strong>
                        </span>
                    </button>
                <?php endif; ?>

                <!-- Sepet Butonu (Daima GÃ¶rÃ¼nÃ¼r) -->
                <button type="button" class="action-btn action-cart" id="emdief-cart-trigger" aria-label="<?php esc_attr_e('Sepeti AÃ§', 'mis360-mobilya'); ?>">
                    <span class="action-icon">
                        <?php echo function_exists('mis360_icon') ? mis360_icon('cart', 22) : 'ğŸ›’'; ?>
                        <span class="emdief-cart-count" id="emdief-cart-count">
                            <?php echo (class_exists('WooCommerce') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0'; ?>
                        </span>
                        <script>(function(){try{var m=document.cookie.match(/woocommerce_items_in_cart=([0-9]+)/);if(m&&parseInt(m[1],10)>0){var el=document.getElementById("emdief-cart-count");if(el)el.textContent=m[1];}}catch(e){}})();</script>
                    </span>
                    <span class="action-label">
                        <small><?php esc_html_e('Sepetim', 'mis360-mobilya'); ?></small>
                        <strong class="emdief-cart-total"><?php echo (class_exists('WooCommerce') && WC()->cart) ? WC()->cart->get_cart_subtotal() : '0,00 TL'; ?></strong>
                    </span>
                </button>
            </div>
        </div>

        <!-- Mobil HÄ±zlÄ± Arama AÃ§Ä±lÄ±r BarÄ± -->
        <div class="emdief-mobile-search-bar" id="emdief-mobile-search-bar">
            <form role="search" method="get" class="emdief-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-wrapper">
                    <input type="search" class="search-field" placeholder="<?php esc_attr_e('Montessori kitaplÄ±k, ahÅŸap oyuncak...', 'mis360-mobilya'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                    <input type="hidden" name="post_type" value="product">
                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Ara', 'mis360-mobilya'); ?>">
                        <?php echo function_exists('mis360_icon') ? mis360_icon('search', 18) : 'ğŸ”'; ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Ana MenÃ¼ BarÄ± (Desktop Navigation) -->
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
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('TÃ¼m ÃœrÃ¼nler', 'mis360-mobilya'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplÄ±k') : home_url('/shop/?s=kitapl%C4%B1k')); ?>"><?php esc_html_e('KitaplÄ±klar', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duvar-masasi', 'duvar masasÄ±') : home_url('/urun-kategori/duvar-masasi/')); ?>"><?php esc_html_e('Duvar MasalarÄ±', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/shop/?s=oyuncak')); ?>"><?php esc_html_e('AhÅŸap Oyuncak', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duzenleyiciler', 'duzenleyici') : home_url('/shop/?s=duzenleyici')); ?>"><?php esc_html_e('DÃ¼zenleyiciler', 'mis360-mobilya'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>"><?php esc_html_e('Kurulum', 'mis360-mobilya'); ?></a></li>
                    <li class="menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>" class="nav-corp-trigger" aria-haspopup="true" aria-expanded="false"><?php esc_html_e('Kurumsal', 'mis360-mobilya'); ?> <span class="nav-arrow-down">â–¾</span></a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>"><?php esc_html_e('HakkÄ±mÄ±zda & Montessori', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/teslimat-ve-iade/')); ?>"><?php esc_html_e('Teslimat ve Ä°ade KoÅŸullarÄ±', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>"><?php esc_html_e('Mesafeli SatÄ±ÅŸ SÃ¶zleÅŸmesi', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>"><?php esc_html_e('Ã‡erez PolitikasÄ±', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>"><?php esc_html_e('Gizlilik & KVKK Metni', 'mis360-mobilya'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>"><?php esc_html_e('Ä°letiÅŸim & AtÃ¶lye', 'mis360-mobilya'); ?></a></li>
                        </ul>
                    </li>
                </ul>
                <?php
            }
            ?>
            <div class="nav-extra-badge">
                <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') . '?on_sale=1' : home_url('/shop/')); ?>" class="badge-link">
                    <span><?php esc_html_e('FÄ±rsatlar', 'mis360-mobilya'); ?></span>
                </a>
            </div>
        </nav>
    </div>
</header>

<!-- Mobil MenÃ¼ Ã‡ekmecesi (Mobile Offcanvas Menu) -->
<div class="emdief-drawer" id="emdief-mobile-drawer" aria-hidden="true">
    <div class="emdief-drawer-overlay" id="emdief-mobile-overlay"></div>
    <div class="emdief-drawer-panel drawer-left">
        <div class="drawer-header">
            <div class="drawer-header-brand">
                <span class="drawer-brand-name">Emdief<strong>Home</strong></span>
                <span class="drawer-brand-sub">Montessori Ã‡ocuk OdasÄ±</span>
            </div>
            <button type="button" class="drawer-close" id="emdief-mobile-close" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
                <?php echo function_exists('mis360_icon') ? mis360_icon('close', 20) : 'âœ•'; ?>
            </button>
        </div>
        <div class="drawer-content">
            <!-- 1. Ã–ne Ã‡Ä±kan WhatsApp CanlÄ± Destek Butonu -->
            <div class="drawer-wa-card-wrap">
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=<?php echo rawurlencode('Merhaba Emdief Home, sipariÅŸim ve mobilyalar hakkÄ±nda destek almak istiyorum.'); ?>" target="_blank" rel="noopener" class="drawer-whatsapp-btn">
                    <div class="wa-icon-bubble">
                        <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 22) : 'ğŸ’¬'; ?>
                    </div>
                    <div class="wa-text-col">
                        <div class="wa-top-row">
                            <span class="wa-title">WhatsApp CanlÄ± Destek</span>
                            <span class="wa-online-pill"><span class="wa-online-dot"></span> CanlÄ±</span>
                        </div>
                        <span class="wa-sub">SipariÅŸ, Kurulum & Ã–zel Ã–lÃ§Ã¼ HattÄ±</span>
                    </div>
                    <span class="wa-arrow">âœ</span>
                </a>
            </div>

            <!-- 2. HÄ±zlÄ± Arama Kutusu -->
            <div class="drawer-mobile-search">
                <form role="search" method="get" class="emdief-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-wrapper">
                        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Montessori kitaplÄ±k, raf, oyuncak...', 'mis360-mobilya'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Ara', 'mis360-mobilya'); ?>">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('search', 16) : 'ğŸ”'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. HÄ±zlÄ± MenÃ¼ Ã‡ipleri -->
            <div class="drawer-quick-pills">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="quick-pill">
                    <span>ğŸ  Anasayfa</span>
                </a>
                <?php if (class_exists('WooCommerce')): ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="quick-pill">
                        <span>ğŸ›ï¸ TÃ¼m ÃœrÃ¼nler</span>
                    </a>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop') . '?on_sale=1'); ?>" class="quick-pill pill-sale">
                        <span>Ä°ndirimler</span>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="quick-pill pill-video">
                    <span>ğŸ¬ Kurulum</span>
                </a>
            </div>

            <!-- 4. Kategorize EdilmiÅŸ Akordeon MenÃ¼ GruplarÄ± -->
            <div class="drawer-categorized-nav">
                <!-- Grup 1: Montessori ÃœrÃ¼n Kategorileri (VarsayÄ±lan AÃ§Ä±k) -->
                <div class="drawer-group is-open">
                    <button type="button" class="drawer-group-toggle" aria-expanded="true">
                        <span class="group-title">
                            <span class="group-emoji"></span>
                            <strong>Montessori ÃœrÃ¼nleri</strong>
                        </span>
                        <span class="group-toggle-icon">â–¾</span>
                    </button>
                    <ul class="drawer-group-links">
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplÄ±k') : home_url('/shop/?s=kitapl%C4%B1k')); ?>">
                                <span class="link-bullet"></span>
                                <span>KitaplÄ±klar</span>
                                <span class="link-badge">PopÃ¼ler</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/shop/?s=oyuncak')); ?>">
                                <span class="link-bullet">ğŸ§©</span>
                                <span>EÄŸitici AhÅŸap Oyuncaklar</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duzenleyiciler', 'duzenleyici') : home_url('/shop/?s=duzenleyici')); ?>">
                                <span class="link-bullet"></span>
                                <span>Oyuncak & EÅŸya DÃ¼zenleyiciler</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duvar-rafi', 'raf') : home_url('/shop/?s=raf')); ?>">
                                <span class="link-bullet">ğŸªŸ</span>
                                <span>Duvar & Banyo RaflarÄ±</span>
                            </a>
                        </li>
                        <li class="group-all-link">
                            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>">
                                <span>TÃ¼m Montessori Koleksiyonunu GÃ¶r</span>
                                <span>âœ</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Grup 2: Kurulum & YardÄ±m Merkezi -->
                <div class="drawer-group">
                    <button type="button" class="drawer-group-toggle" aria-expanded="false">
                        <span class="group-title">
                            <span class="group-emoji">ğŸ¬</span>
                            <strong>YardÄ±m & Kurulum</strong>
                        </span>
                        <span class="group-toggle-icon">â–¾</span>
                    </button>
                    <ul class="drawer-group-links" style="display: none;">
                        <li>
                            <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>">
                                <span class="link-bullet">ğŸ¥</span>
                                <span>Montaj & Kurulum</span>
                                <span class="link-badge badge-video">Video</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/yardim-merkezi/#sikca-sorulan-sorular')); ?>">
                                <span class="link-bullet">â“</span>
                                <span>SÄ±kÃ§a Sorulan Sorular (SSS)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/teslimat-ve-iade/')); ?>">
                                <span class="link-bullet"></span>
                                <span>Teslimat & Ä°ade KoÅŸullarÄ±</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=<?php echo rawurlencode('Eksik parÃ§a / vida talebinde bulunmak istiyorum.'); ?>" target="_blank" rel="noopener">
                                <span class="link-bullet"></span>
                                <span>Eksik ParÃ§a & Garanti Talebi</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Grup 3: Kurumsal Bilgiler -->
                <div class="drawer-group">
                    <button type="button" class="drawer-group-toggle" aria-expanded="false">
                        <span class="group-title">
                            <span class="group-emoji">â„¹ï¸</span>
                            <strong>Kurumsal</strong>
                        </span>
                        <span class="group-toggle-icon">â–¾</span>
                    </button>
                    <ul class="drawer-group-links" style="display: none;">
                        <li>
                            <a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>">
                                <span class="link-bullet"></span>
                                <span>HakkÄ±mÄ±zda & Montessori Felsefesi</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>">
                                <span class="link-bullet"></span>
                                <span>Gizlilik PolitikasÄ± & KVKK</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>">
                                <span class="link-bullet"></span>
                                <span>Ã‡erez PolitikasÄ±</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>">
                                <span class="link-bullet">ğŸ“</span>
                                <span>Mesafeli SatÄ±ÅŸ SÃ¶zleÅŸmesi</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/iletisim/')); ?>">
                                <span class="link-bullet">ğŸ“</span>
                                <span><?php esc_html_e('Ä°letiÅŸim', 'mis360-mobilya'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Grup 4: Ãœyelik & Hesap AlanÄ± -->
                <div class="drawer-account-row">
                    <?php if (is_user_logged_in()): ?>
                        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/hesabim/')); ?>" class="drawer-acc-btn">
                            <span class="acc-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 18) : 'ğŸ‘¤'; ?></span>
                            <span>HesabÄ±m (<?php echo esc_html(wp_get_current_user()->display_name); ?>)</span>
                        </a>
                        <a href="<?php echo esc_url(wc_logout_url(home_url('/'))); ?>" class="drawer-logout-btn" title="<?php esc_attr_e('Ã‡Ä±kÄ±ÅŸ Yap', 'mis360-mobilya'); ?>">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('logout', 18) : 'ğŸšª'; ?>
                        </a>
                    <?php else: ?>
                        <button type="button" class="drawer-acc-btn" id="drawer-login-trigger">
                            <span class="acc-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('user', 18) : 'ğŸ‘¤'; ?></span>
                            <span>GiriÅŸ Yap / KayÄ±t Ol</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="drawer-contact-info">
                <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('mis360_phone', '+90 537 477 87 66'))); ?>" class="contact-pill">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('phone', 16) : 'ğŸ“'; ?>
                    <span><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Mini-Cart Yan Ã‡ekmecesi (Right Drawer) -->
<div class="emdief-drawer" id="emdief-cart-drawer" aria-hidden="true">
    <div class="emdief-drawer-overlay" id="emdief-cart-overlay"></div>
    <div class="emdief-drawer-panel drawer-right">
        <div class="drawer-header">
            <div class="drawer-title-group">
                <h3><?php esc_html_e('AlÄ±ÅŸveriÅŸ Sepetim', 'mis360-mobilya'); ?></h3>
                <span class="drawer-count-badge" id="emdief-drawer-count-badge"><?php echo (class_exists('WooCommerce') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0'; ?> <?php esc_html_e('Ã¼rÃ¼n', 'mis360-mobilya'); ?></span>
            </div>
            <button type="button" class="drawer-close" id="emdief-cart-close" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
                <?php echo function_exists('mis360_icon') ? mis360_icon('close', 20) : 'âœ•'; ?>
            </button>
        </div>
        <?php
        if (function_exists('mis360_render_drawer_cart_content')) {
            mis360_render_drawer_cart_content();
        } else {
            ?>
            <div class="emdief-cart-empty">
                <div class="empty-bear-wrap">
                    <?php echo function_exists('mis360_crying_bear') ? mis360_crying_bear(125, 115, 'animated-drawer-crying-bear') : '<div class="empty-icon"></div>'; ?>
                </div>
                <div class="empty-bear-badge">ğŸ¥º AyÄ±cÄ±k AÄŸlÄ±yor!</div>
                <h3><?php esc_html_e('Sepetiniz BomboÅŸ KaldÄ±...', 'mis360-mobilya'); ?></h3>
                <p><?php esc_html_e('Montessori felsefesine uygun 1. sÄ±nÄ±f kaliteli MDF Ã¼rÃ¼nlerimizi ekleyin, sevimli ayÄ±cÄ±ÄŸÄ±mÄ±zÄ±n gÃ¶zyaÅŸlarÄ± dinsin!', 'mis360-mobilya'); ?></p>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- ÅIK GÄ°RÄ°Å & KAYIT POPUP MODALI -->
<div class="emdief-modal" id="emdief-auth-modal" aria-hidden="true">
    <div class="emdief-modal-overlay" id="emdief-auth-overlay"></div>
    <div class="emdief-modal-dialog">
        <button type="button" class="emdief-modal-close" id="emdief-auth-close" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
            <?php echo function_exists('mis360_icon') ? mis360_icon('close', 20) : 'âœ•'; ?>
        </button>

        <div class="auth-modal-header">
            <div class="auth-modal-icon"></div>
            <h3 class="auth-modal-title"><?php esc_html_e('Emdief Home Ailesine HoÅŸ Geldiniz', 'mis360-mobilya'); ?></h3>
            <p class="auth-modal-subtitle"><?php esc_html_e('Montessori doÄŸal mobilya dÃ¼nyasÄ±na eriÅŸin, sipariÅŸlerinizi kolayca yÃ¶netin.', 'mis360-mobilya'); ?></p>
            
            <div class="auth-tabs">
                <button type="button" class="auth-tab-btn is-active" data-tab="login"><?php esc_html_e('GiriÅŸ Yap', 'mis360-mobilya'); ?></button>
                <button type="button" class="auth-tab-btn" data-tab="register"><?php esc_html_e('KayÄ±t Ol', 'mis360-mobilya'); ?></button>
            </div>
        </div>

        <div class="auth-modal-body">
            <!-- GiriÅŸ Formu Paneli -->
            <div class="auth-form-panel is-active" id="auth-tab-login">
                <form method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" class="emdief-auth-form">
                    <div class="form-group">
                        <label for="emdief-user-login"><?php esc_html_e('E-posta veya Cep Telefonu', 'mis360-mobilya'); ?></label>
                        <input type="text" name="log" id="emdief-user-login" class="form-input" required placeholder="ornek@mail.com veya 05XX XXX XX XX" autocomplete="username">
                    </div>
                    <div class="form-group">
                        <div class="d-flex-between">
                            <label for="emdief-user-pass"><?php esc_html_e('Åifre', 'mis360-mobilya'); ?></label>
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-pass-link" target="_blank"><?php esc_html_e('Åifremi Unuttum?', 'mis360-mobilya'); ?></a>
                        </div>
                        <div class="input-password-wrap">
                            <input type="password" name="pwd" id="emdief-user-pass" class="form-input" required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" autocomplete="current-password">
                            <button type="button" class="toggle-password-btn" id="emdief-toggle-pass" aria-label="<?php esc_attr_e('Åifreyi GÃ¶ster', 'mis360-mobilya'); ?>">ğŸ‘ï¸</button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="remember-label">
                            <input type="checkbox" name="rememberme" value="forever" checked>
                            <span><?php esc_html_e('Beni HatÄ±rla', 'mis360-mobilya'); ?></span>
                        </label>
                    </div>
                    <input type="hidden" name="redirect_to" id="emdief-login-redirect" value="<?php echo esc_url($_SERVER['REQUEST_URI'] ?? home_url('/')); ?>">
                    <button type="submit" class="emdief-btn btn-primary btn-block btn-lg auth-submit-btn">
                        <span><?php esc_html_e('GiriÅŸ Yap', 'mis360-mobilya'); ?></span>
                        <?php echo function_exists('mis360_icon') ? mis360_icon('arrow-right', 18) : 'â†’'; ?>
                    </button>
                </form>
            </div>

            <!-- KayÄ±t Formu Paneli -->
            <div class="auth-form-panel" id="auth-tab-register">
                <form method="post" action="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : wp_registration_url()); ?>" class="emdief-auth-form">
                    <div class="form-group">
                        <label for="emdief-reg-email"><?php esc_html_e('E-posta Adresi', 'mis360-mobilya'); ?></label>
                        <input type="email" name="<?php echo class_exists('WooCommerce') ? 'email' : 'user_email'; ?>" id="emdief-reg-email" class="form-input" required placeholder="ornek@mail.com" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="emdief-reg-phone"><?php esc_html_e('Cep Telefonu NumarasÄ±', 'mis360-mobilya'); ?></label>
                        <input type="tel" name="billing_phone" id="emdief-reg-phone" class="form-input emdief-phone-input" required placeholder="0 (5XX) XXX XX XX" pattern="[0-9\s\(\)\-\+]{10,18}" autocomplete="tel">
                    </div>
                    <?php if (class_exists('WooCommerce')): ?>
                        <div class="form-group">
                            <label for="emdief-reg-pass"><?php esc_html_e('Åifre', 'mis360-mobilya'); ?></label>
                            <div class="input-password-wrap">
                                <input type="password" name="password" id="emdief-reg-pass" class="form-input" required placeholder="<?php esc_attr_e('GÃ¼venli bir ÅŸifre belirleyin', 'mis360-mobilya'); ?>" autocomplete="new-password">
                                <button type="button" class="toggle-password-btn" id="emdief-toggle-reg-pass" aria-label="<?php esc_attr_e('Åifreyi GÃ¶ster', 'mis360-mobilya'); ?>">ğŸ‘ï¸</button>
                            </div>
                        </div>
                        <?php wp_nonce_field('mis360_register_action', 'mis360_register_nonce'); ?>
                        <input type="hidden" name="emdief_register" value="1">
                        <input type="hidden" name="redirect" id="emdief-reg-redirect" value="<?php echo esc_url($_SERVER['REQUEST_URI'] ?? home_url('/')); ?>">
                    <?php else: ?>
                        <div class="form-group">
                            <label for="emdief-reg-user"><?php esc_html_e('KullanÄ±cÄ± AdÄ±', 'mis360-mobilya'); ?></label>
                            <input type="text" name="user_login" id="emdief-reg-user" class="form-input" required placeholder="<?php esc_attr_e('kullaniciadi', 'mis360-mobilya'); ?>">
                        </div>
                    <?php endif; ?>
                    <p class="form-terms-note">
                        <?php esc_html_e('KayÄ±t olarak Ãœyelik SÃ¶zleÅŸmesini ve KiÅŸisel Verilerin KorunmasÄ± PolitikasÄ±nÄ± kabul etmiÅŸ sayÄ±lÄ±rsÄ±nÄ±z.', 'mis360-mobilya'); ?>
                    </p>
                    <button type="submit" class="emdief-btn btn-primary btn-block btn-lg auth-submit-btn">
                        <span><?php esc_html_e('Ãœcretsiz Hesap OluÅŸtur', 'mis360-mobilya'); ?></span>
                        <?php echo function_exists('mis360_icon') ? mis360_icon('sparkles', 18) : 'âœ¨'; ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="auth-modal-footer">
            <div class="security-badge">
                <span class="sec-icon">ğŸ”’</span>
                <span><?php esc_html_e('256-Bit SSL ÅŸifreleme ile verileriniz %100 gÃ¼vende.', 'mis360-mobilya'); ?></span>
            </div>
        </div>
    </div>
</div>

<main id="primary" class="site-main">
