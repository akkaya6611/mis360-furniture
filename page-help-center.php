<?php
/**
 * Template Name: Yardım & Kurulum Merkezi (Help Center & Assembly)
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
?>

<div class="emdief-help-center-page py-8">
    <div class="emdief-container">
        
        <!-- 1. YARDIM MERKEZİ HERO BANNER -->
        <div class="help-hero-banner">
            <div class="help-hero-content">
                <span class="help-hero-tag">🛠️ Kolay Montaj & Müşteri Desteği</span>
                <h1 class="help-hero-title">Yardım Merkezi & Kurulum Videoları</h1>
                <p class="help-hero-desc">
                    Montessori felsefesine uygun 1. sınıf MDF mobilyalarınızı 5 dakikada kolayca kurabilmeniz için adım adım video anlatımlar, montaj şemaları ve teknik destek bu merkezde.
                </p>
                <div class="help-quick-search-box">
                    <span class="search-ico">🔍</span>
                    <input type="text" id="helpCenterSearch" placeholder="Model adı (Carmen, Safir, Düzenleyici) veya montaj konusu arayın..." autocomplete="off">
                </div>
            </div>
            <div class="help-hero-badge-card">
                <div class="hero-bear-icon">🧸</div>
                <div class="hero-badge-text">
                    <strong>%100 Parça Garantisi</strong>
                    <span>Eksik veya hasarlı parça durumunda aynı gün ücretsiz kargo!</span>
                </div>
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Merhaba,%20Emdief%20Home%20kurulum%20veya%20parça%20desteği%20almak%20istiyorum." target="_blank" rel="noopener" class="emdief-btn btn-sm btn-primary">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 16) : '💬'; ?>
                    <span>Canlı Montaj Desteği</span>
                </a>
            </div>
        </div>

        <!-- 2. HIZLI ERİŞİM KARTLARI (4 GRID) -->
        <div class="help-cards-grid">
            <a href="#kurulum-videolari" class="help-topic-card">
                <div class="topic-icon-wrap bg-coral">🎬</div>
                <div class="topic-text">
                    <h3>Kurulum Videoları</h3>
                    <p>Carmen, Safir ve eğitici ürünlerin adım adım video montajı.</p>
                </div>
                <span class="topic-arrow">↓</span>
            </a>
            <a href="<?php echo esc_url(class_exists('WooCommerce') ? wc_get_account_endpoint_url('orders') : home_url('/my-account/')); ?>" class="help-topic-card">
                <div class="topic-icon-wrap bg-purple">🚚</div>
                <div class="topic-text">
                    <h3>Kargo & Teslimat Takibi</h3>
                    <p>Siparişinizin üretim ve kargo durumunu anlık takip edin.</p>
                </div>
                <span class="topic-arrow">→</span>
            </a>
            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Merhaba,%20eksik/hasarlı%20parça%20talebinde%20bulunmak%20istiyorum." target="_blank" rel="noopener" class="help-topic-card">
                <div class="topic-icon-wrap bg-emerald">🔧</div>
                <div class="topic-text">
                    <h3>Yedek Parça Talebi</h3>
                    <p>Vida, dübel veya montaj aparatları için aynı gün ücretsiz gönderim.</p>
                </div>
                <span class="topic-arrow">↗</span>
            </a>
            <a href="#sikca-sorulan-sorular" class="help-topic-card">
                <div class="topic-icon-wrap bg-blue">❓</div>
                <div class="topic-text">
                    <h3>Sıkça Sorulan Sorular</h3>
                    <p>Duvara sabitleme, şarjlı matkap kullanımı ve güvenlik.</p>
                </div>
                <span class="topic-arrow">↓</span>
            </a>
        </div>

        <!-- 3. KURULUM VİDEOLARI BÖLÜMÜ -->
        <div class="help-section-box" id="kurulum-videolari">
            <div class="section-heading-wrap">
                <span class="sub-pill">🎥 Pratik Montaj Rehberleri</span>
                <h2 class="section-title">Ürün Kurulum Videoları</h2>
                <p class="section-desc">Satın aldığınız Montessori mobilyasını seçin, şarjlı matkabınızla 5 dakikada adım adım kurun.</p>
            </div>

            <div class="video-guides-grid">
                <!-- Video 1: Carmen Montessori Kitaplık -->
                <div class="video-guide-card" data-keywords="carmen kitaplık montaj montessori 3 raflı 4 raflı">
                    <div class="video-thumb-holder">
                        <div class="video-thumb-overlay">
                            <span class="play-btn-pulse">▶</span>
                        </div>
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg" alt="Carmen Kitaplık Kurulumu" class="video-cover-img">
                        <span class="video-duration-badge">⏱️ 04:45</span>
                    </div>
                    <div class="video-card-body">
                        <div class="video-meta">
                            <span class="pill-category">Montessori Kitaplık</span>
                            <span class="pill-difficulty">Zorluk: Kolay ⭐</span>
                        </div>
                        <h3 class="video-title">Carmen 3 & 4 Raflı Montessori Kitaplık Kurulumu</h3>
                        <p class="video-desc">Numaralandırılmış 1. sınıf MDF parçaların şarjlı matkap ile vidalanması ve duvara sabitleme adımları.</p>
                        <div class="video-card-footer">
                            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Carmen%20Kitaplık%20kurulum%20videosu%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" rel="noopener" class="btn-watch-modal">
                                <span>Videoyu İzle</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 3l14 9-14 9V3z"/></svg>
                            </a>
                            <span class="tag-tools">Şarjlı Matkap</span>
                        </div>
                    </div>
                </div>

                <!-- Video 2: Safir Montessori Kitaplık -->
                <div class="video-guide-card" data-keywords="safir kitaplık montessori ahşap beyaz mdf">
                    <div class="video-thumb-holder">
                        <div class="video-thumb-overlay">
                            <span class="play-btn-pulse">▶</span>
                        </div>
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg" alt="Safir Kitaplık Kurulumu" class="video-cover-img">
                        <span class="video-duration-badge">⏱️ 05:20</span>
                    </div>
                    <div class="video-card-body">
                        <div class="video-meta">
                            <span class="pill-category">Eğitici Kitaplık</span>
                            <span class="pill-difficulty">Zorluk: Kolay ⭐</span>
                        </div>
                        <h3 class="video-title">Safir Montessori Çocuk Kitaplığı Kurulumu</h3>
                        <p class="video-desc">Geniş tabanlı dengeli gövde, ön yüzü açık eğimli kitap rafları ve çocuk güvenliği için duvara sabitleme kitinin montajı.</p>
                        <div class="video-card-footer">
                            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Safir%20Kitaplık%20kurulum%20videosu%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" rel="noopener" class="btn-watch-modal">
                                <span>Videoyu İzle</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 3l14 9-14 9V3z"/></svg>
                            </a>
                            <span class="tag-tools">Şarjlı Matkap</span>
                        </div>
                    </div>
                </div>

                <!-- Video 3: Ahşap Oyuncak & Düzenleyici -->
                <div class="video-guide-card" data-keywords="oyuncak düzenleyici kutu ahşap dolap">
                    <div class="video-thumb-holder">
                        <div class="video-thumb-overlay">
                            <span class="play-btn-pulse">▶</span>
                        </div>
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/1_org_zoom-448-300x300.jpg" alt="Ahşap Oyuncak Düzenleyici" class="video-cover-img">
                        <span class="video-duration-badge">⏱️ 03:50</span>
                    </div>
                    <div class="video-card-body">
                        <div class="video-meta">
                            <span class="pill-category">Oda Düzenleyici</span>
                            <span class="pill-difficulty">Zorluk: Çok Kolay ⚡</span>
                        </div>
                        <h3 class="video-title">Montessori Ahşap Oyuncak & Eşya Düzenleyici</h3>
                        <p class="video-desc">Çocukların oyuncaklarını bağımsız toplayabilmesi için tasarlanan modüler ahşap düzenleyici ünitelerin hızlı birleştirilmesi.</p>
                        <div class="video-card-footer">
                            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Düzenleyici%20kurulum%20videosu%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" rel="noopener" class="btn-watch-modal">
                                <span>Videoyu İzle</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 3l14 9-14 9V3z"/></svg>
                            </a>
                            <span class="tag-tools">Pratik Geçmeli</span>
                        </div>
                    </div>
                </div>

                <!-- Video 4: Duvar & Banyo Rafları -->
                <div class="video-guide-card" data-keywords="duvar rafı banyo rafı montaj sabitleme">
                    <div class="video-thumb-holder">
                        <div class="video-thumb-overlay">
                            <span class="play-btn-pulse">▶</span>
                        </div>
                        <img src="https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg" alt="Duvar Rafı Kurulumu" class="video-cover-img">
                        <span class="video-duration-badge">⏱️ 03:15</span>
                    </div>
                    <div class="video-card-body">
                        <div class="video-meta">
                            <span class="pill-category">Duvar & Raf Grubu</span>
                            <span class="pill-difficulty">Zorluk: Çok Kolay ⚡</span>
                        </div>
                        <h3 class="video-title">Montessori Duvar & Banyo Rafı Montajı</h3>
                        <p class="video-desc">Gizli askı elemanları, dübel ve vida şablonuyla duvara sıfır, sallantısız ve güvenli sabitleme kılavuzu.</p>
                        <div class="video-card-footer">
                            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Duvar%20Rafı%20kurulumu%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" rel="noopener" class="btn-watch-modal">
                                <span>Videoyu İzle</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 3l14 9-14 9V3z"/></svg>
                            </a>
                            <span class="tag-tools">Matkap + Dübel + Vida</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. ADIM ADIM KOLAY MONTAJ ŞEMASI (İNFOGRAFİK) -->
        <div class="help-infographic-box">
            <h3 class="infographic-title">Montessori Mobilyanızı 4 Adımda Kolayca Kurun</h3>
            <div class="infographic-steps">
                <div class="info-step">
                    <div class="step-num">1</div>
                    <div class="step-icon">📦</div>
                    <h4>Kutuyu Açın & Parçaları Ayırın</h4>
                    <p>Kutudan çıkan parçalar etiketlidir. Ürünlerinizi temiz bir örtü veya kutu kartonu üzerinde dizin.</p>
                </div>
                <div class="info-step">
                    <div class="step-num">2</div>
                    <div class="step-icon">⚡</div>
                    <h4>Şarjlı Matkap ile Kolayca Vidalayın</h4>
                    <p>Vida delikleri CNC tezgahlarda milimetrik açılmıştır. Şarjlı matkabınızla vidaları saniyeler içinde sıkıp gövdeyi birleştirin.</p>
                </div>
                <div class="info-step">
                    <div class="step-num">3</div>
                    <div class="step-icon">🛡️</div>
                    <h4>Duvara Delik Delip Sabitleyin</h4>
                    <p>Aynı şarjlı matkap ile duvara delik delin; paketten çıkan dübel ve L-aparat ile kitaplığınızı güvenle duvara sabitleyin.</p>
                </div>
                <div class="info-step">
                    <div class="step-num">4</div>
                    <div class="step-icon">✨</div>
                    <h4>Kullanıma Hazır!</h4>
                    <p>Kitapları ve oyuncakları çocuğunuzun erişebileceği şekilde yerleştirerek keşif dünyasını başlatın.</p>
                </div>
            </div>
        </div>

        <!-- 5. SIKÇA SORULAN SORULAR (AKORDEON) -->
        <div class="help-section-box" id="sikca-sorulan-sorular">
            <div class="section-heading-wrap">
                <span class="sub-pill">❓ Merak Edilenler</span>
                <h2 class="section-title">Kurulum & Ürün Hakkında Sıkça Sorulan Sorular</h2>
            </div>

            <div class="help-faq-accordion">
                <div class="faq-item is-open">
                    <button type="button" class="faq-toggle">
                        <span>Kurulum için hangi aletlere ihtiyacım var? Paket içinde alyan var mı?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer" style="display:block;">
                        <p>Paket içerisinde alyan anahtarı gönderilmemektedir. Kurulum için ihtiyacınız olan alet <strong>şarjlı matkaptır</strong>. Parçalarımızın tüm vida delikleri CNC tezgahlarda milimetrik olarak hazır açılmıştır. Şarjlı matkabınız sayesinde hem kitaplığımızın ahşap parçalarını dakikalar içinde yorulmadan vidalayabilir, hem de duvara delik delerek paket içeriğindeki dübel ve sabitleme aparatıyla kitaplığınızı duvara güvenle monte edebilirsiniz.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-toggle">
                        <span>Eksik, hatalı veya hasarlı parça çıkarsa ne yapmalıyım?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Emdief Home olarak <strong>%100 Koşulsuz Parça Garantisi</strong> sunuyoruz. Kargo sürecinde oluşan hasarlar veya eksik vida/parça durumunda, WhatsApp hattımıza ürün ve parça görselini iletmeniz yeterlidir. Gerekli yedek parça aynı gün adresinize ücretsiz kargolanır.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-toggle">
                        <span>Montessori kitaplıkları duvara sabitlemek zorunlu mu?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Mobilyalarımızın taban dengesi ve ağırlık merkezi devrilmeye karşı dayanıklı olarak tasarlanmıştır. Ancak küçük çocukların raflara tutunup tırmanma ihtimaline karşı çocuk odası güvenliği standartları gereği paket içerisinden çıkan L-sabitleme aparatı ile duvara matkapla delik açılarak sabitlenmesini önemle tavsiye ederiz.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-toggle">
                        <span>1. Sınıf MDF malzemenin bakımı ve temizliği nasıl yapılmalı?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Ürünlerimizin yüzeyi pürüzsüz ve leke tutmayan yapıdadır. Temizlik için sadece hafif nemli bir mikrofiber bez kullanmanız yeterlidir. Alkol, çamaşır suyu veya aşındırıcı kimyasal içeren deterjanlar kullanmayınız.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-toggle">
                        <span>Siparişim kaç günde imalata alınır ve kargoya verilir?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Saat 13:00'a kadar verilen siparişler aynı gün öncelikli imalata ve kalite kontrole alınır. Özel darbe emici balonlu ambalajlarla paketlenerek 24-48 saat içerisinde kargo firmasına teslim edilir.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. DOĞRUDAN WHATSAPP & TEKNİK DESTEK KARTI -->
        <div class="help-live-support-card">
            <div class="support-card-left">
                <div class="support-icon-circle">🧸💬</div>
                <div class="support-content">
                    <h3>Kurulumda takıldığınız bir adım mı oldu?</h3>
                    <p>Montaj ustalarımız WhatsApp üzerinden anlık görüntülü ve yazılı olarak size adım adım rehberlik eder.</p>
                </div>
            </div>
            <div class="support-card-right">
                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Merhaba,%20kurulum%20esnasında%20canlı%20destek%20almak%20istiyorum." target="_blank" rel="noopener" class="emdief-btn btn-lg btn-success">
                    <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 20) : '💬'; ?>
                    <span>WhatsApp Canlı Kurulum Desteği</span>
                </a>
                <span class="support-phone">Müşteri Hattı: <strong><?php echo esc_html(get_theme_mod('mis360_phone', '+90 537 477 87 66')); ?></strong></span>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Akordeon Etkileşimi
    const faqItems = document.querySelectorAll('.help-faq-accordion .faq-item');
    faqItems.forEach(item => {
        const toggle = item.querySelector('.faq-toggle');
        const answer = item.querySelector('.faq-answer');
        if (toggle && answer) {
            toggle.addEventListener('click', () => {
                const isOpen = item.classList.contains('is-open');
                faqItems.forEach(other => {
                    other.classList.remove('is-open');
                    const otherAnswer = other.querySelector('.faq-answer');
                    if (otherAnswer) otherAnswer.style.display = 'none';
                    const otherIcon = other.querySelector('.faq-icon');
                    if (otherIcon) otherIcon.textContent = '+';
                });
                if (!isOpen) {
                    item.classList.add('is-open');
                    answer.style.display = 'block';
                    const icon = item.querySelector('.faq-icon');
                    if (icon) icon.textContent = '−';
                }
            });
        }
    });

    // Canlı Arama Filtreleme
    const searchInput = document.getElementById('helpCenterSearch');
    const videoCards = document.querySelectorAll('.video-guide-card');
    if (searchInput && videoCards.length) {
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase().trim();
            videoCards.forEach(card => {
                const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();
                const title = card.querySelector('.video-title').textContent.toLowerCase();
                if (!val || keywords.includes(val) || title.includes(val)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php
get_footer();
