<?php
/**
 * Template Name: Kurumsal Sayfa (Corporate Layout)
 * Template Post Type: page
 *
 * @package Mis360-Mobilya
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
if (function_exists('mis360_breadcrumbs')) {
    mis360_breadcrumbs();
}

// Mevcut sayfa slug'ı
global $post;
$current_slug = $post ? $post->post_name : '';
?>

<div class="emdief-corporate-page-wrap py-8">
    <div class="emdief-container">
        <div class="corporate-layout-grid">
            <!-- Sol Sütun: Kurumsal Yan Menü (Sidebar) -->
            <aside class="corporate-sidebar" aria-label="<?php esc_attr_e('Kurumsal Menü', 'mis360-mobilya'); ?>">
                <div class="corporate-sidebar-card">
                    <div class="sidebar-header">
                        <span class="sidebar-badge">📜 Kurumsal & Yasal</span>
                        <h3><?php esc_html_e('Bilgi Merkezi', 'mis360-mobilya'); ?></h3>
                    </div>
                    <nav class="corporate-nav">
                        <a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'hakkimizda' || $current_slug === 'about-us') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">🧸</span>
                            <span><?php esc_html_e('Hakkımızda', 'mis360-mobilya'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/cerez-politikasi/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'cerez-politikasi') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">🍪</span>
                            <span><?php esc_html_e('Çerez Politikası', 'mis360-mobilya'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/gizlilik-ve-kvkk/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'gizlilik-ve-kvkk' || $current_slug === 'kvkk') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">🛡️</span>
                            <span><?php esc_html_e('Gizlilik & KVKK', 'mis360-mobilya'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/mesafeli-satis-sozlesmesi/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'mesafeli-satis-sozlesmesi') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">📝</span>
                            <span><?php esc_html_e('Mesafeli Satış Sözleşmesi', 'mis360-mobilya'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/teslimat-ve-iade/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'teslimat-ve-iade' || $current_slug === 'teslimat-iade') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">📦</span>
                            <span><?php esc_html_e('Teslimat ve İade Koşulları', 'mis360-mobilya'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/yardim-merkezi/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'yardim-merkezi' || $current_slug === 'help-center') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">🛠️</span>
                            <span><?php esc_html_e('Yardım & Kurulum Videoları', 'mis360-mobilya'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/iletisim/')); ?>" class="corp-nav-link <?php echo ($current_slug === 'iletisim') ? 'is-active' : ''; ?>">
                            <span class="nav-ico">📞</span>
                            <span><?php esc_html_e('İletişim & Fabrika Satış', 'mis360-mobilya'); ?></span>
                        </a>
                    </nav>

                    <!-- Hızlı Destek Kutusu -->
                    <div class="sidebar-support-box">
                        <div class="support-title">Yardıma mı ihtiyacınız var?</div>
                        <p class="support-desc">Montessori mobilyalarımız veya siparişiniz hakkında bize dilediğiniz an ulaşabilirsiniz.</p>
                        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>" target="_blank" rel="noopener" class="emdief-btn btn-outline btn-block btn-sm">
                            <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 16) : '💬'; ?>
                            <span>WhatsApp Destek</span>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Sağ Sütun: Ana Kurumsal İçerik -->
            <main class="corporate-main-content">
                <article id="post-<?php the_ID(); ?>" <?php post_class('corporate-article-card'); ?>>
                    <header class="corporate-header">
                        <div class="corp-meta-tags">
                            <span class="corp-pill">Emdief Home Resmi Belgesi</span>
                            <span class="corp-date">Son Güncelleme: <?php echo get_the_modified_date('d F Y'); ?></span>
                        </div>
                        <h1 class="corp-title"><?php the_title(); ?></h1>
                    </header>
                    <div class="corporate-body typography-prose">
                        <?php
                        while (have_posts()):
                            the_post();
                            $raw_content = get_the_content();
                            $corporate_data = function_exists('mis360_get_corporate_pages_data') ? mis360_get_corporate_pages_data() : [];
                            
                            // Eğer veritabanındaki içerikte Orhan TEBER yoksa veya eski ise doğrudan güncel şablon içeriğini göster
                            if (isset($corporate_data[$current_slug]) && (strpos($raw_content, 'Orhan TEBER') === false || strpos($raw_content, 'MİS360 Teknoloji') !== false)) {
                                echo apply_filters('the_content', $corporate_data[$current_slug]['content']);
                            } else {
                                the_content();
                            }
                        endwhile;
                        ?>
                    </div>
                </article>
            </main>
        </div>
    </div>
</div>

<?php
get_footer();
