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

<style>
/* Scoped Yardım Merkezi Stilleri */
.emdief-help-center-page { padding-top: 1.5rem; padding-bottom: 4rem; color: #1e293b; }
.help-hero-banner { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 45%, #fef3c7 100%); border-radius: 24px; padding: 3rem 2.5rem; border: 1px solid #fed7aa; box-shadow: 0 10px 30px rgba(249, 115, 22, 0.08); margin-bottom: 2.5rem; position: relative; overflow: hidden; display: grid; grid-template-columns: 1.6fr 1fr; gap: 2rem; align-items: center; }
@media (max-width: 991px) { .help-hero-banner { grid-template-columns: 1fr; padding: 2rem 1.5rem; gap: 1.5rem; } }
.help-hero-banner::before { content: ''; position: absolute; top: -60px; right: -60px; width: 220px; height: 220px; border-radius: 50%; background: radial-gradient(circle, rgba(249, 115, 22, 0.18) 0%, rgba(255, 255, 255, 0) 70%); pointer-events: none; }
.help-hero-content { position: relative; z-index: 2; }
.help-hero-tag { display: inline-flex; align-items: center; gap: 6px; background: #ffffff; color: #ea580c; padding: 6px 14px; border-radius: 9999px; font-size: 0.82rem; font-weight: 800; box-shadow: 0 2px 8px rgba(234, 88, 12, 0.12); margin-bottom: 1rem; }
.help-hero-title { font-size: 2.2rem; font-weight: 900; color: #0f172a; margin: 0 0 0.85rem 0; line-height: 1.25; letter-spacing: -0.5px; }
@media (max-width: 768px) { .help-hero-title { font-size: 1.7rem; } }
.help-hero-desc { font-size: 1rem; color: #475569; line-height: 1.6; margin: 0 0 1.5rem 0; max-width: 580px; }
.help-quick-search-box { position: relative; max-width: 500px; }
.help-quick-search-box .search-ico { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-size: 1.1rem; pointer-events: none; }
#helpCenterSearch { width: 100%; padding: 14px 20px 14px 48px; font-size: 0.95rem; border-radius: 9999px; border: 2px solid #fdba74; background: #ffffff; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05); outline: none; transition: all 0.25s ease; color: #0f172a; }
#helpCenterSearch:focus { border-color: #ea580c; box-shadow: 0 6px 22px rgba(234, 88, 12, 0.15); }
.help-hero-badge-card { position: relative; z-index: 2; background: #ffffff; border-radius: 20px; padding: 1.75rem; border: 1.5px solid #fed7aa; box-shadow: 0 8px 24px rgba(249, 115, 22, 0.12); display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px; }
.hero-bear-icon { font-size: 2.5rem; line-height: 1; }
.hero-badge-text strong { display: block; font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.hero-badge-text span { font-size: 0.85rem; color: #64748b; line-height: 1.4; display: block; }
.help-cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 3.5rem; }
@media (max-width: 991px) { .help-cards-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .help-cards-grid { grid-template-columns: 1fr; } }
.help-topic-card { display: flex; align-items: center; gap: 14px; background: #ffffff; padding: 1.25rem 1.35rem; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03); text-decoration: none; transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); position: relative; }
.help-topic-card:hover { transform: translateY(-3px); border-color: #fdba74; box-shadow: 0 10px 24px rgba(249, 115, 22, 0.12); }
.topic-icon-wrap { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; flex-shrink: 0; }
.bg-coral { background: #fee2e2; }
.bg-purple { background: #f3e8ff; }
.bg-emerald { background: #d1fae5; }
.bg-blue { background: #dbeafe; }
.topic-text { flex: 1; min-width: 0; }
.topic-text h3 { margin: 0 0 3px 0; font-size: 0.96rem; font-weight: 800; color: #0f172a; line-height: 1.3; }
.topic-text p { margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4; }
.topic-arrow { font-size: 1.1rem; color: #cbd5e1; font-weight: 800; transition: transform 0.2s ease, color 0.2s ease; }
.help-topic-card:hover .topic-arrow { color: #ea580c; transform: translateX(3px); }
.help-section-box { margin-bottom: 3.5rem; }
.help-section-box .section-heading-wrap { margin-bottom: 1.75rem; }
.help-section-box .sub-pill { display: inline-block; background: #ffedd5; color: #c2410c; padding: 4px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 800; margin-bottom: 0.5rem; }
.help-section-box .section-title { font-size: 1.7rem; font-weight: 900; color: #0f172a; margin: 0 0 0.4rem 0; letter-spacing: -0.4px; }
.help-section-box .section-desc { font-size: 0.95rem; color: #64748b; margin: 0; }
.video-guides-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
@media (max-width: 768px) { .video-guides-grid { grid-template-columns: 1fr; } }
.video-guide-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; transition: all 0.3s ease; }
.video-guide-card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08); border-color: #fdba74; }
.video-thumb-holder { position: relative; width: 100%; aspect-ratio: 16 / 9; background: #0f172a; overflow: hidden; }
.video-cover-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
.video-guide-card:hover .video-cover-img { transform: scale(1.04); }
.video-thumb-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.35); display: flex; align-items: center; justify-content: center; transition: background 0.3s ease; }
.video-guide-card:hover .video-thumb-overlay { background: rgba(15, 23, 42, 0.18); }
.play-btn-pulse { width: 54px; height: 54px; border-radius: 50%; background: #ea580c; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; padding-left: 3px; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.4); transition: transform 0.25s ease; }
.video-guide-card:hover .play-btn-pulse { transform: scale(1.12); }
.video-duration-badge { position: absolute; bottom: 10px; right: 10px; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(4px); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; }
.video-card-body { padding: 1.4rem 1.4rem 1.25rem 1.4rem; display: flex; flex-direction: column; flex: 1; }
.video-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 0.65rem; }
.pill-category { background: #fff7ed; color: #ea580c; font-size: 0.74rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; }
.pill-difficulty { background: #f1f5f9; color: #475569; font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; }
.video-title { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem 0; line-height: 1.35; }
.video-desc { font-size: 0.88rem; color: #64748b; margin: 0 0 1.25rem 0; line-height: 1.5; flex: 1; }
.video-card-footer { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 1rem; }
.btn-watch-modal { display: inline-flex; align-items: center; gap: 6px; background: #ea580c; color: #ffffff; font-size: 0.85rem; font-weight: 800; padding: 8px 16px; border-radius: 10px; text-decoration: none; transition: background 0.2s ease; }
.btn-watch-modal:hover { background: #c2410c; }
.tag-tools { font-size: 0.78rem; color: #64748b; font-weight: 700; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; }
.help-infographic-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 22px; padding: 2.75rem 2rem; margin-bottom: 3.5rem; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.03); }
.infographic-title { text-align: center; font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0 0 2.25rem 0; letter-spacing: -0.3px; }
.infographic-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
@media (max-width: 991px) { .infographic-steps { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .infographic-steps { grid-template-columns: 1fr; } }
.info-step { text-align: center; position: relative; padding: 1.5rem 1.15rem; background: #fafaf9; border-radius: 18px; border: 1px solid #f5f5f4; transition: transform 0.25s ease; }
.info-step:hover { transform: translateY(-3px); border-color: #fed7aa; }
.step-num { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); width: 28px; height: 28px; border-radius: 50%; background: #ea580c; color: #ffffff; font-size: 0.85rem; font-weight: 900; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.35); }
.step-icon { font-size: 2.3rem; margin-bottom: 0.75rem; margin-top: 0.5rem; }
.info-step h4 { font-size: 0.98rem; font-weight: 800; color: #0f172a; margin: 0 0 0.45rem 0; line-height: 1.35; }
.info-step p { font-size: 0.82rem; color: #64748b; margin: 0; line-height: 1.55; }
.help-faq-accordion { display: flex; flex-direction: column; gap: 0.85rem; }
.faq-item { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
.faq-item.is-open { border-color: #fdba74; box-shadow: 0 4px 14px rgba(249, 115, 22, 0.08); }
.faq-toggle { width: 100%; padding: 1.25rem 1.4rem; background: transparent; border: none; display: flex; align-items: center; justify-content: space-between; font-size: 1rem; font-weight: 800; color: #1e293b; cursor: pointer; text-align: left; }
.faq-icon { font-size: 1.4rem; font-weight: 800; color: #ea580c; margin-left: 12px; flex-shrink: 0; }
.faq-answer { display: none; padding: 0 1.4rem 1.4rem 1.4rem; color: #475569; font-size: 0.92rem; line-height: 1.65; }
.faq-answer p { margin: 0; }
.help-live-support-card { background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); border-radius: 22px; padding: 2.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 2rem; box-shadow: 0 12px 30px rgba(6, 95, 70, 0.18); margin-top: 3.5rem; }
@media (max-width: 991px) { .help-live-support-card { flex-direction: column; text-align: center; padding: 2rem 1.5rem; } }
.support-card-left { display: flex; align-items: center; gap: 1.25rem; }
@media (max-width: 991px) { .support-card-left { flex-direction: column; } }
.support-icon-circle { width: 58px; height: 58px; border-radius: 18px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; font-size: 1.7rem; flex-shrink: 0; }
.support-content h3 { font-size: 1.45rem; font-weight: 800; color: #ffffff; margin: 0 0 0.4rem 0; }
.support-content p { font-size: 0.92rem; color: #a7f3d0; margin: 0; max-width: 520px; }
.support-card-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
@media (max-width: 991px) { .support-card-right { align-items: center; } }
.support-phone { font-size: 0.82rem; color: #a7f3d0; }
.support-phone strong { color: #ffffff; }

/* YouTube Video Modal */
.help-video-modal { position: fixed; inset: 0; z-index: 99999; display: none; align-items: center; justify-content: center; padding: 1.5rem; }
.help-video-modal.is-open { display: flex; }
.video-modal-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); }
.video-modal-dialog { position: relative; z-index: 2; background: #0f172a; border-radius: 20px; width: 100%; max-width: 860px; overflow: hidden; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55); border: 1px solid rgba(255, 255, 255, 0.15); animation: modalFadeIn 0.25s ease; }
@keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.video-modal-close { position: absolute; top: 12px; right: 14px; z-index: 10; background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(255, 255, 255, 0.25); color: #ffffff; width: 38px; height: 38px; border-radius: 50%; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; line-height: 1; transition: all 0.2s ease; }
.video-modal-close:hover { background: #ea580c; border-color: #ea580c; transform: scale(1.1); }
.video-modal-frame-wrap { position: relative; width: 100%; padding-top: 56.25%; background: #000000; }
.video-modal-frame-wrap iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
.video-modal-footer { padding: 1.25rem 1.75rem; background: #1e293b; display: flex; align-items: center; justify-content: space-between; gap: 1rem; color: #ffffff; }
@media (max-width: 640px) { .video-modal-footer { flex-direction: column; align-items: flex-start; padding: 1rem 1.25rem; } }
.video-modal-footer h4 { margin: 0; font-size: 1.05rem; font-weight: 800; color: #f8fafc; }
.video-thumb-holder { cursor: pointer; }
</style>

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
        <?php
        $installation_videos = [
            [
                'youtube_id' => 'R434l8wOYBY',
                'title'      => 'Carmen Serisi Montessori Kitaplık Kurulumu',
                'desc'       => 'Carmen 3 ve 4 raflı çocuk kitaplıklarımızın CNC hazır deliklerle şarjlı matkap kullanarak kolayca birleştirilmesi ve duvara emniyetli montajı.',
                'category'   => 'Montessori Kitaplık',
                'difficulty' => 'Zorluk: Kolay ⭐',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Şarjlı Matkap',
                'keywords'   => 'carmen kitaplık montaj montessori 3 raflı 4 raflı karmen raf kurulum ahşap çocuk odası',
                'badge'      => ''
            ],
            [
                'youtube_id' => '-nYJfPdr9vw',
                'title'      => 'Askı Aparatı Duvara Nasıl Montajlanır? (Zorunlu Güvenlik)',
                'desc'       => 'Yerden olan modellerimiz hariç hemen hemen tüm Montessori mobilyalarımızda miniklerin güvenliği ve devrilmeyi önlemek için bu montaj adımı zorunludur.',
                'category'   => 'Güvenlik & Duvar Montajı',
                'difficulty' => 'Zorunlu Adım 🛡️',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Matkap + Dübel + Vida',
                'keywords'   => 'askı aparatı duvara montaj sabitleme l aparatı emniyet devrilme önleyici vida dübel sabitleme zorunlu',
                'badge'      => '⚠️ Yerden olan modeller hariç tüm ürünlerimizde zorunludur'
            ],
            [
                'youtube_id' => 'Uko45KVzhhs',
                'title'      => 'Melis 2 Raflı Montessori Kitaplık Kurulumu',
                'desc'       => 'Melis 2 raflı çocuk kitaplığımızın CNC hazır montaj delikleriyle şarjlı matkap ile zahmetsizce birleştirilmesi ve duvara sabitlenmesi.',
                'category'   => 'Montessori Kitaplık',
                'difficulty' => 'Zorluk: Kolay ⭐',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Şarjlı Matkap',
                'keywords'   => 'melis 2 raf raflı kitaplık montaj montessori çocuk kitaplığı kurulum ahşap',
                'badge'      => ''
            ],
            [
                'youtube_id' => 'J7qaETlymr0',
                'title'      => 'Carmen 3 Raflı Montessori Kitaplık Kurulumu',
                'desc'       => 'Carmen 3 raflı çocuk kitaplığımızın numaralandırılmış MDF parçalarının şarjlı matkap ile birleştirilmesi ve sağlam montajı.',
                'category'   => 'Montessori Kitaplık',
                'difficulty' => 'Zorluk: Kolay ⭐',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Şarjlı Matkap',
                'keywords'   => 'carmen 3 raf raflı kitaplık montaj montessori çocuk kitaplığı kurulum ahşap',
                'badge'      => ''
            ],
            [
                'youtube_id' => 'bpHA-jND33Q',
                'title'      => 'Safir & Carmen Tek Raflı Modellerimizin Kurulumu',
                'desc'       => 'Safir ve Carmen serisi tek raflı duvar ve banyo raflarımızın gizli askı aparatlarıyla duvara sıfır ve sallantısız montaj rehberi.',
                'category'   => 'Duvar & Raf Grubu',
                'difficulty' => 'Zorluk: Çok Kolay ⚡',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Matkap + Dübel + Vida',
                'keywords'   => 'safir carmen tek raf raflı 1 raflı duvar rafı banyo rafı montaj kurulum ahşap',
                'badge'      => ''
            ],
            [
                'youtube_id' => 'LBBww08uTcI',
                'title'      => 'Melis Serisi Montessori Kitaplık Kurulumu',
                'desc'       => 'Melis serisi çok raflı çocuk kitaplıklarımızın geniş tabanlı gövde birleşimi, raf dizilimi ve duvara emniyet sabitlemesi.',
                'category'   => 'Montessori Kitaplık',
                'difficulty' => 'Zorluk: Kolay ⭐',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Şarjlı Matkap',
                'keywords'   => 'melis serisi kitaplık montaj montessori 3 raflı 4 raflı çocuk kitaplığı kurulum ahşap',
                'badge'      => ''
            ],
            [
                'youtube_id' => '4fUzzzdXXgQ',
                'title'      => 'Safir Serisi Montessori Kitaplık Kurulumu',
                'desc'       => 'Safir serisi Montessori çocuk kitaplıklarımızın önü açık kitap sergileme kanalları, gövde montajı ve duvara sabitleme kılavuzu.',
                'category'   => 'Eğitici Kitaplık',
                'difficulty' => 'Zorluk: Kolay ⭐',
                'duration'   => '⏱️ Video Anlatım',
                'tools'      => 'Şarjlı Matkap',
                'keywords'   => 'safir serisi kitaplık montaj montessori çocuk kitaplığı kurulum ahşap beyaz mdf',
                'badge'      => ''
            ]
        ];
        ?>
        <div class="help-section-box" id="kurulum-videolari">
            <div class="section-heading-wrap">
                <span class="sub-pill">🎥 Pratik Montaj Rehberleri</span>
                <h2 class="section-title">Ürün Kurulum Videoları</h2>
                <p class="section-desc">Satın aldığınız Montessori mobilyasını seçin, şarjlı matkabınızla birkaç dakikada adım adım kurun.</p>
            </div>

            <div class="video-guides-grid">
                <?php foreach ($installation_videos as $video): 
                    $yt_id = !empty($video['youtube_id']) ? esc_attr($video['youtube_id']) : '';
                    $yt_watch = $yt_id ? 'https://www.youtube.com/watch?v=' . $yt_id : '#';
                    $yt_thumb = $yt_id ? 'https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg' : 'https://emdiefhome.com.tr/wp-content/uploads/2026/08/banner-emdief1.jpg';
                ?>
                    <div class="video-guide-card" data-keywords="<?php echo esc_attr($video['keywords']); ?>">
                        <div class="video-thumb-holder js-open-video-modal" data-youtube-id="<?php echo $yt_id; ?>" data-video-title="<?php echo esc_attr($video['title']); ?>" title="<?php esc_attr_e('Videoyu Oynat', 'mis360-mobilya'); ?>">
                            <div class="video-thumb-overlay">
                                <span class="play-btn-pulse">▶</span>
                            </div>
                            <img src="<?php echo esc_url($yt_thumb); ?>" alt="<?php echo esc_attr($video['title']); ?>" class="video-cover-img" loading="lazy">
                            <span class="video-duration-badge"><?php echo esc_html($video['duration']); ?></span>
                        </div>
                        <div class="video-card-body">
                            <div class="video-meta">
                                <span class="pill-category"><?php echo esc_html($video['category']); ?></span>
                                <span class="pill-difficulty"><?php echo esc_html($video['difficulty']); ?></span>
                            </div>
                            <?php if (!empty($video['badge'])): ?>
                                <div style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:8px; padding:6px 10px; font-size:0.78rem; font-weight:800; margin-bottom:0.75rem; display:flex; align-items:center; gap:6px;">
                                    <span><?php echo esc_html($video['badge']); ?></span>
                                </div>
                            <?php endif; ?>
                            <h3 class="video-title"><?php echo esc_html($video['title']); ?></h3>
                            <p class="video-desc"><?php echo esc_html($video['desc']); ?></p>
                            <div class="video-card-footer">
                                <button type="button" class="btn-watch-modal js-open-video-modal" data-youtube-id="<?php echo $yt_id; ?>" data-video-title="<?php echo esc_attr($video['title']); ?>">
                                    <span>Videoyu İzle</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 3l14 9-14 9V3z"/></svg>
                                </button>
                                <span class="tag-tools"><?php echo esc_html($video['tools']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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

<!-- Video Popup Modal -->
<div id="videoModal" class="help-video-modal" aria-hidden="true">
    <div class="video-modal-backdrop" id="videoModalBackdrop"></div>
    <div class="video-modal-dialog">
        <button type="button" class="video-modal-close" id="videoModalClose" aria-label="Kapat">&times;</button>
        <div class="video-modal-frame-wrap">
            <iframe id="videoModalIframe" src="" title="Kurulum Videosu" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>
        <div class="video-modal-footer">
            <h4 id="videoModalTitle">Video Başlığı</h4>
            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('mis360_whatsapp', '905374778766')); ?>?text=Kurulum%20hakkında%20sorum%20var" target="_blank" rel="noopener" class="emdief-btn btn-sm btn-primary">
                <?php echo function_exists('mis360_icon') ? mis360_icon('whatsapp', 16) : '💬'; ?>
                <span>Montaj Desteği Al</span>
            </a>
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

    // Video Modal Etkileşimi
    const videoModal = document.getElementById('videoModal');
    const videoIframe = document.getElementById('videoModalIframe');
    const videoTitle = document.getElementById('videoModalTitle');
    const modalClose = document.getElementById('videoModalClose');
    const modalBackdrop = document.getElementById('videoModalBackdrop');

    function openVideoModal(ytId, title) {
        if (!ytId || !videoModal || !videoIframe) return;
        videoIframe.src = 'https://www.youtube.com/embed/' + ytId + '?autoplay=1&rel=0';
        if (videoTitle) videoTitle.textContent = title || 'Kurulum Videosu';
        videoModal.classList.add('is-open');
        videoModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeVideoModal() {
        if (!videoModal || !videoIframe) return;
        videoModal.classList.remove('is-open');
        videoModal.setAttribute('aria-hidden', 'true');
        videoIframe.src = '';
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-open-video-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const ytId = this.getAttribute('data-youtube-id');
            const title = this.getAttribute('data-video-title');
            openVideoModal(ytId, title);
        });
    });

    if (modalClose) modalClose.addEventListener('click', closeVideoModal);
    if (modalBackdrop) modalBackdrop.addEventListener('click', closeVideoModal);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && videoModal && videoModal.classList.contains('is-open')) {
            closeVideoModal();
        }
    });
});
</script>

<?php
get_footer();
