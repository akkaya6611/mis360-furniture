<?php
/**
 * Mis360 & Emdief Home Özel WordPress Giriş Sayfası (wp-login.php)
 * Markaya özel logo, renkler, tipografi ve modern kart tasarımı.
 *
 * @package Mis360_Mobilya
 */

defined('ABSPATH') || exit;

/**
 * Giriş sayfası logosu linkini site ana sayfasına bağla
 */
add_filter('login_headerurl', function() {
    return home_url('/');
});

/**
 * Logo title metnini site adı yap
 */
add_filter('login_headertext', function() {
    return get_bloginfo('name') . ' - Montessori Çocuk Odası & Ahşap Mobilya';
});

/**
 * Giriş sayfası üstüne şık karşılama mesajı
 */
add_filter('login_message', function($message) {
    if (empty($message)) {
        return '<div class="mis360-login-badge"><span class="badge-icon">🧸</span> <strong>Emdief Home</strong> Yönetim Paneli</div>';
    }
    return $message;
});

/**
 * Giriş sayfasına özel CSS stilleri ekle
 */
add_action('login_enqueue_scripts', function() {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
    }
    if (empty($logo_url)) {
        $logo_url = get_template_directory_uri() . '/assets/images/emdief-home-logo.webp';
    }
    ?>
    <style type="text/css">
        body.login {
            background-color: #fdfbf7 !important;
            background-image: 
                radial-gradient(at 0% 0%, rgba(254, 243, 199, 0.45) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(254, 215, 170, 0.35) 0px, transparent 50%),
                radial-gradient(at 50% 50%, #ffffff 0px, #fdfbf7 100%) !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px 0;
        }

        #login {
            width: 100% !important;
            max-width: 410px !important;
            padding: 24px 20px !important;
            margin: 0 auto !important;
        }

        #login h1 {
            margin-bottom: 16px !important;
            text-align: center !important;
        }

        #login h1 a {
            background-image: url('<?php echo esc_url($logo_url); ?>') !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            width: 220px !important;
            height: 68px !important;
            margin: 0 auto !important;
            transition: transform 0.25s ease;
        }

        #login h1 a:hover {
            transform: scale(1.03);
        }

        .mis360-login-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
            font-size: 0.85rem;
            font-weight: 650;
            padding: 8px 16px;
            border-radius: 999px;
            margin: 0 auto 18px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.08);
            max-width: 320px;
        }

        .mis360-login-badge .badge-icon {
            font-size: 1.15rem;
        }

        #loginform, #registerform, #lostpasswordform {
            background: #ffffff !important;
            border: 1px solid #f1f5f9 !important;
            border-radius: 20px !important;
            padding: 30px 26px !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.06), 0 20px 25px -5px rgba(0, 0, 0, 0.03) !important;
            position: relative;
            overflow: hidden;
        }

        #loginform::before, #registerform::before, #lostpasswordform::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f97316, #ea580c, #fb923c);
        }

        #loginform label, #registerform label, #lostpasswordform label {
            font-size: 0.86rem !important;
            font-weight: 650 !important;
            color: #1e293b !important;
            margin-bottom: 6px !important;
            display: block !important;
        }

        #loginform input[type="text"],
        #loginform input[type="password"],
        #registerform input[type="text"],
        #registerform input[type="email"],
        #lostpasswordform input[type="text"] {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 10px 14px !important;
            font-size: 0.95rem !important;
            background: #fafaf9 !important;
            color: #0f172a !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
            margin-top: 4px !important;
            margin-bottom: 16px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #loginform input[type="text"]:focus,
        #loginform input[type="password"]:focus,
        #registerform input[type="text"]:focus,
        #registerform input[type="email"]:focus,
        #lostpasswordform input[type="text"]:focus {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12) !important;
            outline: none !important;
        }

        .login .button.wp-hide-pw {
            color: #64748b !important;
        }

        .login .forgetmenot {
            margin-top: 6px !important;
            margin-bottom: 16px !important;
            float: none !important;
            display: flex !important;
            align-items: center !important;
        }

        .login .forgetmenot label {
            font-weight: 500 !important;
            font-size: 0.84rem !important;
            color: #64748b !important;
            margin-bottom: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .login input[type="checkbox"] {
            border-radius: 6px !important;
            border: 1.5px solid #cbd5e1 !important;
            width: 18px !important;
            height: 18px !important;
        }

        .login input[type="checkbox"]:checked {
            background: #ea580c !important;
            border-color: #ea580c !important;
        }

        .wp-core-ui .button-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 11px 24px !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35) !important;
            text-shadow: none !important;
            height: auto !important;
            line-height: 1.4 !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            width: 100% !important;
            float: none !important;
            margin-top: 8px !important;
        }

        .wp-core-ui .button-primary:hover,
        .wp-core-ui .button-primary:focus {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.45) !important;
            transform: translateY(-1px) !important;
            color: #ffffff !important;
        }

        #nav, #backtoblog {
            text-align: center !important;
            padding: 12px 0 0 !important;
            font-size: 0.84rem !important;
        }

        #nav a, #backtoblog a {
            color: #64748b !important;
            transition: color 0.2s ease !important;
            font-weight: 550 !important;
            text-decoration: none !important;
        }

        #nav a:hover, #backtoblog a:hover {
            color: #ea580c !important;
            text-decoration: underline !important;
        }

        .login .privacy-policy-page-link {
            text-align: center !important;
            margin: 12px 0 !important;
        }

        .login .privacy-policy-page-link a {
            color: #94a3b8 !important;
            font-size: 0.78rem !important;
            text-decoration: none !important;
        }

        .language-switcher {
            margin-top: 16px !important;
            text-align: center !important;
        }

        .language-switcher select {
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 5px 12px !important;
            background: #ffffff !important;
            color: #475569 !important;
            font-size: 0.82rem !important;
        }

        .language-switcher .button {
            border-radius: 8px !important;
            font-size: 0.82rem !important;
            padding: 4px 10px !important;
        }

        /* Bildirim Kutuları */
        .login .message, .login .notice, .login #login_error {
            border-radius: 12px !important;
            border-left: 4px solid #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05) !important;
            padding: 12px 16px !important;
            font-size: 0.85rem !important;
            margin-bottom: 20px !important;
        }

        .login #login_error {
            border-left-color: #ef4444 !important;
            background: #fef2f2 !important;
            color: #991b1b !important;
        }

        .login .message {
            border-left-color: #10b981 !important;
            background: #f0fdf4 !important;
            color: #065f46 !important;
        }

        /* MİS360 Web Yazılım Banner */
        .mis360-login-agency-banner {
            width: 100%;
            max-width: 410px;
            margin: 22px auto 30px;
            box-sizing: border-box;
        }

        .agency-banner-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 12px 16px;
            text-decoration: none !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25), 0 4px 10px -2px rgba(15, 23, 42, 0.15);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .agency-banner-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
            transition: left 0.6s ease;
        }

        .agency-banner-link:hover::before {
            left: 150%;
        }

        .agency-banner-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.35);
            border-color: rgba(249, 115, 22, 0.4);
        }

        .agency-banner-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .agency-logo-badge {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 850;
            font-size: 13px;
            letter-spacing: -0.05em;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
            flex-shrink: 0;
        }

        .agency-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .agency-tag {
            font-size: 0.62rem;
            font-weight: 750;
            color: #fb923c;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .agency-brand-title {
            font-size: 0.94rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.25;
            margin: 2px 0 1px;
        }

        .agency-brand-title .agency-accent {
            color: #fdba74;
            font-weight: 600;
        }

        .agency-url {
            font-size: 0.72rem;
            color: #94a3b8;
            line-height: 1.2;
        }

        .agency-action {
            flex-shrink: 0;
            margin-left: 8px;
        }

        .agency-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-size: 0.74rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .agency-banner-link:hover .agency-btn-pill {
            background: #ea580c;
            border-color: #ea580c;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.4);
        }

        .agency-btn-pill svg {
            transition: transform 0.2s ease;
        }

        .agency-banner-link:hover .agency-btn-pill svg {
            transform: translate(2px, -2px);
        }

        .mis360-login-footer-tag {
            text-align: center;
            font-size: 0.74rem;
            color: #94a3b8;
            margin-top: 14px;
            letter-spacing: 0.02em;
        }
    </style>
    <?php
});

/**
 * Giriş sayfası altına MİS360 Web Yazılım bannerı & telif notu ekle
 */
add_action('login_footer', function() {
    ?>
    <div class="mis360-login-agency-banner">
        <a href="https://misteknoloji360.com.tr/" target="_blank" rel="noopener noreferrer" class="agency-banner-link" title="<?php esc_attr_e('MİS360 Web Yazılım & E-Ticaret Altyapıları', 'mis360-mobilya'); ?>">
            <div class="agency-banner-left">
                <div class="agency-logo-badge">
                    <span>&lt;/&gt;</span>
                </div>
                <div class="agency-info">
                    <span class="agency-tag">WEB YAZILIM &bull; E-TİCARET &bull; DİJİTAL</span>
                    <div class="agency-brand-title">
                        <span>MİS360</span> <span class="agency-accent">Web Yazılım</span>
                    </div>
                    <span class="agency-url">misteknoloji360.com.tr</span>
                </div>
            </div>
            <div class="agency-action">
                <span class="agency-btn-pill">
                    <span>Ziyaret Et</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M7 7h10v10"/></svg>
                </span>
            </div>
        </a>
        <div class="mis360-login-footer-tag">&copy; <?php echo date('Y'); ?> Emdief Home &bull; Tüm Hakları Saklıdır</div>
    </div>
    <?php
});