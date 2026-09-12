<?php
/**
 * Theme Footer
 *
 * @package Mis360-Furniture
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
</main><!-- #primary -->

<!-- Güven Rozetleri Şeridi (E1, 1. Sınıf MDF, Kolay Montaj, Hızlı Kargo) -->
<?php mis360_render_trust_badges(); ?>

<!-- Ana Footer Bölümü -->
<footer id="colophon" class="emdief-footer">
    <div class="emdief-container">
        <div class="footer-grid">
            <!-- Kolon 1: Marka & Montessori Felsefesi -->
            <div class="footer-col footer-about">
                <div class="footer-brand">
                    <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/emdief-home-logo-01.webp" alt="Emdief Home" class="footer-logo" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                    <span class="footer-logo-fallback" style="display:none; font-weight:800; font-size:1.4rem; color:var(--emd-text-main);">Emdief<span style="color:var(--emd-primary);">Home</span></span>
                </div>
                <p class="footer-desc">
                    <?php esc_html_e('Emdief Home; çocukların bağımsız keşiflerini, özgüvenlerini ve öğrenme heveslerini destekleyen Montessori felsefeli 1. sınıf kaliteli MDF çocuk odası mobilyaları üreticisidir. Tüm ürünlerimiz Avrupa E1 standartlarında çocuk sağlığına %100 uygun dayanıklı MDF malzemeden, özel yuvarlatılmış güvenli kenarlarla sevgiyle üretilmektedir.', 'mis360-furniture'); ?>
                </p>
                <div class="footer-cert-badges">
                    <span class="cert-pill">🛡️ 1. Sınıf E1 Kalite MDF</span>
                    <span class="cert-pill">🛡️ E1 & EN71-3 Belgeli</span>
                    <span class="cert-pill">👶 Montessori Ergonomisi</span>
                </div>
            </div>

            <!-- Kolon 2: Popüler Kategoriler -->
            <div class="footer-col">
                <h4 class="footer-heading"><?php esc_html_e('Montessori Koleksiyonu', 'mis360-furniture'); ?></h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url(home_url('/shop/?s=carmen')); ?>"><?php esc_html_e('Carmen Kitaplık Serisi', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/shop/?s=safir')); ?>"><?php esc_html_e('Safir 4 Raflı Kitaplıklar', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/shop/?s=melis')); ?>"><?php esc_html_e('Melis Eğitici Raflar', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/shop/?category=ogrenme-kulesi')); ?>"><?php esc_html_e('Mutfak Öğrenme Kuleleri', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/shop/?category=masa-sandalye')); ?>"><?php esc_html_e('Çocuk Aktivite Masası', 'mis360-furniture'); ?></a></li>
                </ul>
            </div>

            <!-- Kolon 3: Müşteri Hizmetleri & Kurumsal -->
            <div class="footer-col">
                <h4 class="footer-heading"><?php esc_html_e('Müşteri & Kurumsal', 'mis360-furniture'); ?></h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url(home_url('/about-us/')); ?>"><?php esc_html_e('Hakkımızda', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Montessori Ebeveyn Rehberi', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/teslimat-iade/')); ?>"><?php esc_html_e('Teslimat & İade Koşulları', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>"><?php esc_html_e('Mesafeli Satış Sözleşmesi', 'mis360-furniture'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>"><?php esc_html_e('İletişim & Fabrika Satış', 'mis360-furniture'); ?></a></li>
                </ul>
            </div>

            <!-- Kolon 4: İletişim & Hızlı Destek -->
            <div class="footer-col footer-contact-col">
                <h4 class="footer-heading"><?php esc_html_e('Bize Ulaşın', 'mis360-furniture'); ?></h4>
                <div class="footer-contact-item">
                    <span class="contact-icon"><?php echo mis360_icon('phone', 18); ?></span>
                    <div>
                        <small><?php esc_html_e('Müşteri Destek Hattı:', 'mis360-furniture'); ?></small>
                        <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('mis360_phone', '+90 537 477 87 66'))); ?>">
                            <strong><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></strong>
                        </a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="contact-icon"><?php echo mis360_icon('whatsapp', 18); ?></span>
                    <div>
                        <small><?php esc_html_e('Doğrudan WhatsApp Hattı:', 'mis360-furniture'); ?></small>
                        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" target="_blank" rel="noopener">
                            <strong>Hemen Mesaj Gönderin</strong>
                        </a>
                    </div>
                </div>
                <div class="footer-payment-icons">
                    <span class="pay-text">Güvenli 256-bit SSL Alışveriş</span>
                    <div class="pay-badges">
                        <span class="pay-card">Mastercard</span>
                        <span class="pay-card">Visa</span>
                        <span class="pay-card">Troy</span>
                        <span class="pay-card">Taksit İmkanı</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alt Telif Hakkı Şeridi -->
        <div class="footer-bottom">
            <div class="footer-copy">
                &copy; <?php echo date('Y'); ?> <strong>Emdief Home</strong>. <?php esc_html_e('Tüm hakları saklıdır. Çocuklar için sevgiyle üretilmiştir.', 'mis360-furniture'); ?>
            </div>
            <div class="footer-credit">
                <span>Theme by <strong>MİS360</strong> & Serkan AKKAYA</span>
            </div>
        </div>
    </div>
</footer>

<!-- Canlı WhatsApp Butonu (Sabit Sağ Alt) -->
<a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" class="emdief-floating-wa" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('WhatsApp Sipariş ve Destek', 'mis360-furniture'); ?>">
    <span class="wa-icon"><?php echo mis360_icon('whatsapp', 30); ?></span>
    <span class="wa-tooltip">Montessori ürünleri hakkında bilgi alın! 🧸</span>
</a>

<!-- Sayfa Başına Dön Butonu -->
<button type="button" class="emdief-back-to-top" id="emdief-back-to-top" aria-label="<?php esc_attr_e('Yukarı Çık', 'mis360-furniture'); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><polyline points="18 15 12 9 6 15"></polyline></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
