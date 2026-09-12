<?php
/**
 * 404 Error Page
 *
 * @package Mis360-Mobilya
 */


if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="emdief-container py-12 text-center">
    <div class="error-404-wrap">
        <div class="error-emoji">🧸🎈</div>
        <h1 class="error-title">404 - Sayfa Bulunamadı</h1>
        <p class="error-desc">
            Aradığınız sayfa başka bir rafa taşınmış veya henüz inşa edilmemiş olabilir. Montessori dünyamıza dönerek keşfe devam edebilirsiniz!
        </p>
        <div class="error-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="emdief-btn btn-primary btn-lg">
                <?php esc_html_e('Anasayfaya Dön', 'mis360-mobilya'); ?>
            </a>
            <?php if (class_exists('WooCommerce')): ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="emdief-btn btn-secondary btn-lg">
                    <?php esc_html_e('Ürünleri İncele', 'mis360-mobilya'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer();
