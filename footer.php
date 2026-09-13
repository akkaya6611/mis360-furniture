<?php
/**
 * Theme Footer
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}
?>
</main><!-- #primary -->

<!-- Güven Rozetleri Şeridi (E1, 1. Sınıf MDF, Kolay Montaj, Hızlı Kargo) -->
<?php if (function_exists('mis360_render_trust_badges')) { mis360_render_trust_badges(); } ?>

<!-- Ana Footer Bölümü -->
<footer id="colophon" class="emdief-footer">
    <div class="emdief-container">
        <div class="footer-grid">
            <!-- Kolon 1: Marka & Montessori Felsefesi -->
            <div class="footer-col footer-about">
                <div class="footer-brand">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/emdief-home-logo.webp'); ?>" alt="Emdief Home" class="footer-logo" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                    <span class="footer-logo-fallback" style="display:none; font-weight:800; font-size:1.4rem; color:var(--emd-text-main);">Emdief<span style="color:var(--emd-primary);">Home</span></span>
                </div>
                <p class="footer-desc">
                    <?php esc_html_e('Emdief Home; çocukların bağımsız keşiflerini, özgüvenlerini ve yaratıcılıklarını destekleyen Montessori felsefeli 1. sınıf MDF çocuk odası mobilyaları ve eğitici ahşap ürünler üreticisidir. Montessori kitaplıklardan eğitici ahşap oyuncaklara, duvar raflarından oda düzenleyicilerine tüm ürünlerimiz sivri kenar barındırmayan yuvarlatılmış güvenli hatlarla sevgiyle üretilmektedir.', 'mis360-mobilya'); ?>
                </p>
                <div class="footer-cert-badges">
                    <span class="cert-pill">🛡️ 1. Sınıf MDF</span>
                    <span class="cert-pill">🛡️ Yuvarlatılmış Güvenli Köşeler</span>
                    <span class="cert-pill">👶 Montessori Ergonomisi</span>
                </div>
            </div>

            <!-- Kolon 2: Popüler Kategoriler -->
            <div class="footer-col">
                <h4 class="footer-heading"><?php esc_html_e('Montessori Ürünleri', 'mis360-mobilya'); ?></h4>
                <?php
                if (has_nav_menu('footer_col_2')) {
                    wp_nav_menu([
                        'theme_location' => 'footer_col_2',
                        'container'      => false,
                        'menu_class'     => 'footer-links',
                        'fallback_cb'    => false,
                    ]);
                } else {
                    ?>
                    <ul class="footer-links">
                        <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('cocuk-montessori-kitaplik', 'kitaplık') : home_url('/shop/?s=kitapl%C4%B1k')); ?>"><?php esc_html_e('Montessori Kitaplıklar', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('ahsap-oyuncak', 'oyuncak') : home_url('/shop/?s=oyuncak')); ?>"><?php esc_html_e('Eğitici Ahşap Oyuncaklar', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duzenleyiciler', 'duzenleyici') : home_url('/shop/?s=duzenleyici')); ?>"><?php esc_html_e('Oyuncak & Eşya Düzenleyiciler', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(function_exists('mis360_get_category_url') ? mis360_get_category_url('duvar-rafi', 'raf') : home_url('/shop/?s=raf')); ?>"><?php esc_html_e('Duvar & Banyo Rafları', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"><?php esc_html_e('Tüm Montessori Koleksiyonu', 'mis360-mobilya'); ?></a></li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <!-- Kolon 3: Müşteri Hizmetleri & Kurumsal -->
            <div class="footer-col">
                <h4 class="footer-heading"><?php esc_html_e('Müşteri & Kurumsal', 'mis360-mobilya'); ?></h4>
                <?php
                if (has_nav_menu('footer_col_1')) {
                    wp_nav_menu([
                        'theme_location' => 'footer_col_1',
                        'container'      => false,
                        'menu_class'     => 'footer-links',
                        'fallback_cb'    => false,
                    ]);
                } else {
                    ?>
                    <ul class="footer-links">
                        <li><a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>">🎬 <?php esc_html_e('Yardım & Kurulum Videoları', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>"><?php esc_html_e('Hakkımızda', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>"><?php esc_html_e('Çerez Politikası', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>"><?php esc_html_e('Gizlilik Politikası & KVKK', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/teslimat-ve-iade/')); ?>"><?php esc_html_e('Teslimat & İade Koşulları', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>"><?php esc_html_e('Mesafeli Satış Sözleşmesi', 'mis360-mobilya'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>"><?php esc_html_e('İletişim', 'mis360-mobilya'); ?></a></li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <!-- Kolon 4: İletişim & Hızlı Destek -->
            <div class="footer-col footer-contact-col">
                <h4 class="footer-heading"><?php esc_html_e('Bize Ulaşın', 'mis360-mobilya'); ?></h4>
                <div class="footer-contact-item">
                    <span class="contact-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('phone', 18) : '📞'; ?></span>
                    <div>
                        <small><?php esc_html_e('Müşteri Destek Hattı:', 'mis360-mobilya'); ?></small>
                        <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('mis360_phone', '+90 537 477 87 66'))); ?>">
                            <strong><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></strong>
                        </a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="contact-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 18) : '💬'; ?></span>
                    <div>
                        <small><?php esc_html_e('Doğrudan WhatsApp Hattı:', 'mis360-mobilya'); ?></small>
                        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" target="_blank" rel="noopener">
                            <strong>Hemen Mesaj Gönderin</strong>
                        </a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="contact-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('map-pin', 18) : '📍'; ?></span>
                    <div>
                        <small><?php esc_html_e('Fabrika Satış & Atölye:', 'mis360-mobilya'); ?></small>
                        <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0x152b057da63cc6c7:0x45e8ad2179bc179c?sa=X&ved=1t:8290&ictx=111" target="_blank" rel="noopener">
                            <strong>Mobilya Kent, Kocasinan / Kayseri</strong>
                        </a>
                    </div>
                </div>
                <div class="footer-payment-icons">
                    <span class="pay-text">🏦 Güvenli Banka Havalesi / EFT / FAST</span>
                    <div class="pay-badges">
                        <span class="pay-card">Banka Havalesi</span>
                        <span class="pay-card">EFT / FAST</span>
                        <span class="pay-card">Sipariş Onaylı</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alt Telif Hakkı Şeridi -->
        <div class="footer-bottom">
            <div class="footer-copy">
                &copy; <?php echo date('Y'); ?> <strong>Emdief Home</strong>. <?php esc_html_e('Tüm hakları saklıdır. Çocuklar için sevgiyle üretilmiştir.', 'mis360-mobilya'); ?>
            </div>
            <div class="footer-legal-links">
                <a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>"><?php esc_html_e('Çerezler', 'mis360-mobilya'); ?></a>
                <span class="legal-sep">&bull;</span>
                <a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>"><?php esc_html_e('Gizlilik & KVKK', 'mis360-mobilya'); ?></a>
                <span class="legal-sep">&bull;</span>
                <a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>"><?php esc_html_e('Mesafeli Satış', 'mis360-mobilya'); ?></a>
            </div>
            <div class="footer-credit">
                <span>Theme by <strong>MİS360</strong> & Serkan AKKAYA</span>
            </div>
        </div>
    </div>
</footer>

<!-- Canlı WhatsApp Butonu (Sabit Sağ Alt - Mobilde Bar Üstüne Hizalanır) -->
<a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" class="emdief-floating-wa" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('WhatsApp Sipariş ve Destek', 'mis360-mobilya'); ?>">
    <span class="wa-icon"><?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 28) : '💬'; ?></span>
    <span class="wa-tooltip">Montessori ürünleri hakkında bilgi alın! 🧸</span>
</a>

<!-- Sayfa Başına Dön Butonu -->
<button type="button" class="emdief-back-to-top" id="emdief-back-to-top" aria-label="<?php esc_attr_e('Yukarı Çık', 'mis360-mobilya'); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><polyline points="18 15 12 9 6 15"></polyline></svg>
</button>

<?php
// Tekil Ürün Sayfası Mobilde Sabit Satın Alma Çubuğu
if (class_exists('WooCommerce') && is_product()):
    global $product;
    if ($product && $product->is_purchasable() && $product->is_in_stock()):
        $sticky_img = wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail');
        ?>
        <div class="emdief-mobile-sticky-buy-bar" id="emdiefStickyBuyBar">
            <div class="sticky-buy-inner">
                <div class="sticky-buy-product">
                    <img src="<?php echo esc_url($sticky_img); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" class="sticky-thumb">
                    <div class="sticky-info">
                        <span class="sticky-price"><?php echo $product->get_price_html(); ?></span>
                        <span class="sticky-title"><?php echo esc_html(wp_trim_words($product->get_name(), 4, '...')); ?></span>
                    </div>
                </div>
                <button type="button" class="btn-sticky-add-cart" id="triggerStickyAddToCart">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <span>Sepete Ekle</span>
                </button>
            </div>
        </div>
        <?php
    endif;
endif;
?>

<!-- Mobil Sabit Alt Gezinme Çubuğu (Mobile App-style Bottom Navigation Bar) -->
<nav class="emdief-mobile-bottom-nav" id="emdief-mobile-bottom-nav" aria-label="<?php esc_attr_e('Mobil Gezinme', 'mis360-mobilya'); ?>">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="bottom-nav-item <?php echo is_front_page() ? 'is-active' : ''; ?>">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        </span>
        <span class="nav-label"><?php esc_html_e('Anasayfa', 'mis360-mobilya'); ?></span>
    </a>
    <button type="button" class="bottom-nav-item" id="bottomNavCategoriesBtn" aria-label="<?php esc_attr_e('Kategoriler', 'mis360-mobilya'); ?>">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
        </span>
        <span class="nav-label"><?php esc_html_e('Kategori', 'mis360-mobilya'); ?></span>
    </button>
    <button type="button" class="bottom-nav-item" id="bottomNavSearchBtn" aria-label="<?php esc_attr_e('Arama', 'mis360-mobilya'); ?>">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </span>
        <span class="nav-label"><?php esc_html_e('Arama', 'mis360-mobilya'); ?></span>
    </button>
    <button type="button" class="bottom-nav-item" id="bottomNavCartBtn" aria-label="<?php esc_attr_e('Sepetim', 'mis360-mobilya'); ?>">
        <span class="nav-icon has-badge">
            <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span class="bottom-cart-badge" id="emdief-bottom-cart-count"><?php echo (class_exists('WooCommerce') && WC()->cart) ? esc_html((string) WC()->cart->get_cart_contents_count()) : '0'; ?></span>
        </span>
        <span class="nav-label"><?php esc_html_e('Sepetim', 'mis360-mobilya'); ?></span>
    </button>
    <?php if (is_user_logged_in()): ?>
        <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" class="bottom-nav-item">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
            <span class="nav-label"><?php esc_html_e('Hesabım', 'mis360-mobilya'); ?></span>
        </a>
    <?php else: ?>
        <button type="button" class="bottom-nav-item" id="bottomNavAccountBtn" aria-label="<?php esc_attr_e('Giriş Yap', 'mis360-mobilya'); ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
            <span class="nav-label"><?php esc_html_e('Giriş', 'mis360-mobilya'); ?></span>
        </button>
    <?php endif; ?>
</nav>

<!-- Çerez Onay Bildirimi (Cookie Consent Banner) -->
<div class="emdief-cookie-banner" id="emdief-cookie-banner" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e('Çerez İzin Bildirimi', 'mis360-mobilya'); ?>" style="display: none;">
    <div class="cookie-banner-inner">
        <div class="cookie-icon-col">
            <span class="cookie-emoji" aria-hidden="true">🍪</span>
        </div>
        <div class="cookie-content-col">
            <div class="cookie-title">
                <strong><?php esc_html_e('Çerez Tercihleri ve Deneyiminiz', 'mis360-mobilya'); ?></strong>
            </div>
            <p class="cookie-text">
                <?php esc_html_e('Emdief Home olarak, sitemizde güvenli alışveriş yapabilmeniz, sepetinizi hatırlayabilmemiz ve deneyiminizi geliştirebilmemiz için yasal mevzuata uygun çerezler (cookies) kullanıyoruz.', 'mis360-mobilya'); ?>
                <a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>" class="cookie-policy-link"><?php esc_html_e('Çerez Politikamızı İnceleyin', 'mis360-mobilya'); ?></a>
            </p>
        </div>
        <div class="cookie-actions-col">
            <button type="button" class="emdief-btn btn-primary btn-sm cookie-accept-btn" id="emdiefCookieAccept">
                <?php esc_html_e('Kabul Ediyorum', 'mis360-mobilya'); ?>
            </button>
            <button type="button" class="cookie-close-btn" id="emdiefCookieClose" aria-label="<?php esc_attr_e('Kapat', 'mis360-mobilya'); ?>">
                &times;
            </button>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
