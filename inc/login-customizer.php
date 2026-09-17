<?php
/**
 * Mis360 & Emdief Home Özel WordPress Giriş Sayfası (wp-login.php)
 * Markaya özel logo, renkler, tipografi ve minimalist zarif tasarım.
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
 * Dil seçici açılır kutusunu kaldır
 */
add_filter('login_display_language_dropdown', '__return_false');

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
            max-width: 400px !important;
            padding: 20px !important;
            margin: 0 auto !important;
        }

        #login h1 {
            margin-bottom: 24px !important;
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

        #loginform, #registerform, #lostpasswordform {
            background: #ffffff !important;
            border: 1px solid #f1f5f9 !important;
            border-radius: 20px !important;
            padding: 32px 28px !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.02) !important;
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
            padding: 11px 14px !important;
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
            padding: 12px 24px !important;
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

        #nav {
            text-align: center !important;
            padding: 16px 0 0 !important;
            margin: 0 !important;
            font-size: 0.84rem !important;
        }

        #nav a {
            color: #64748b !important;
            transition: color 0.2s ease !important;
            font-weight: 550 !important;
            text-decoration: none !important;
        }

        #nav a:hover {
            color: #ea580c !important;
            text-decoration: underline !important;
        }

        #backtoblog,
        .language-switcher {
            display: none !important;
        }

        .login .privacy-policy-page-link {
            text-align: center !important;
            margin: 10px 0 !important;
        }

        .login .privacy-policy-page-link a {
            color: #94a3b8 !important;
            font-size: 0.78rem !important;
            text-decoration: none !important;
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

        /* Sade ve Zarif MİS360 İmzası */
        .mis360-login-clean-credit {
            text-align: center;
            margin-top: 24px;
            font-size: 0.78rem;
            color: #94a3b8;
            letter-spacing: 0.02em;
        }

        .mis360-login-clean-credit a {
            color: #64748b;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .mis360-login-clean-credit a:hover {
            color: #ea580c;
            text-decoration: underline;
        }
    </style>
    <?php
});

/**
 * Giriş sayfası altına sade & zarif MİS360 imzası ekle
 */
add_action('login_footer', function() {
    ?>
    <div class="mis360-login-clean-credit">
        <span>Web Tasarım &amp; Yazılım: <a href="https://misteknoloji360.com.tr/" target="_blank" rel="noopener noreferrer">MİS360</a></span>
    </div>
    <?php
});