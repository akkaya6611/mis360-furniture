<?php
/**
 * Emdief Home Performance & Core Web Vitals Optimization Engine
 *
 * Provides:
 * 1. Script Deferring (defer="defer" on jQuery, WooCommerce and theme JS)
 * 2. Asynchronous Non-blocking Google Fonts
 * 3. Removal of redundant WooCommerce core CSS (layout & smallscreen)
 * 4. Responsive image sizes filter for product grids (48vw mobile)
 * 5. Native WebP image generation format (WP 5.8+)
 * 6. On-the-fly WebP conversion & delivery for product thumbnails
 * 7. Front-page LCP Hero image preload
 * 8. 1-Year Browser Caching (.htaccess & Cache-Control headers)
 *
 * @package Mis360-Mobilya
 * @version 1.9.13
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Script Deferral: Defer non-critical JavaScript to prevent render-blocking
 */
function mis360_performance_defer_scripts($tag, $handle, $src) {
    if (is_admin()) {
        return $tag;
    }

    // Do not alter if already async or deferred
    if (strpos($tag, ' defer') !== false || strpos($tag, ' async') !== false) {
        return $tag;
    }

    // Handles that should be deferred to free the main rendering thread
    $defer_handles = [
        'jquery-core',
        'jquery-migrate',
        'jquery',
        'mis360-main-js',
        'mis360-ajax-cart',
        'woocommerce',
        'wc-add-to-cart',
        'wc-cart-fragments',
        'sourcebuster-js',
        'wc-order-attribution',
        'js-cookie'
    ];

    if (in_array($handle, $defer_handles, true)) {
        return str_replace(' src=', ' defer="defer" src=', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'mis360_performance_defer_scripts', 10, 3);

/**
 * 2. Asynchronous Google Fonts: Convert render-blocking stylesheet to non-blocking preload
 */
function mis360_performance_async_google_fonts($tag, $handle, $href, $media) {
    if ($handle === 'mis360-fonts') {
        return '<link rel="preload" href="' . esc_url($href) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n" .
               '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>' . "\n";
    }
    return $tag;
}
add_filter('style_loader_tag', 'mis360_performance_async_google_fonts', 10, 4);

/**
 * 3. Preconnect for Fonts and Hero Assets
 */
function mis360_performance_head_hints() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Front-page LCP Preload
    if (is_front_page()) {
        $banner_uri = get_template_directory_uri() . '/assets/images/banner-emdief.webp';
        echo '<link rel="preload" as="image" href="' . esc_url($banner_uri) . '" type="image/webp" fetchpriority="high">' . "\n";
    }
}
add_action('wp_head', 'mis360_performance_head_hints', 1);

/**
 * 4. Remove Redundant WooCommerce Core CSS
 * Our theme provides 100% custom responsive styling in assets/css/woocommerce.css
 */
function mis360_performance_clean_wc_styles($styles) {
    if (isset($styles['woocommerce-layout'])) {
        unset($styles['woocommerce-layout']);
    }
    if (isset($styles['woocommerce-smallscreen'])) {
        unset($styles['woocommerce-smallscreen']);
    }
    return $styles;
}
add_filter('woocommerce_enqueue_styles', 'mis360_performance_clean_wc_styles');

/**
 * 5. Responsive Product Grid Image Sizes
 * Fixes: "Bu resim dosyası görüntülenen boyutlarına göre daha büyük"
 * Tells the browser on mobile (2 cols) the image is ~48vw instead of forcing 300px
 */
function mis360_performance_catalog_image_sizes($sizes, $size) {
    if (is_admin()) {
        return $sizes;
    }
    // For WooCommerce catalog loop thumbnails
    if (is_shop() || is_product_taxonomy() || is_front_page()) {
        return '(max-width: 480px) 48vw, (max-width: 768px) 33vw, (max-width: 1200px) 25vw, 300px';
    }
    return $sizes;
}
add_filter('wp_calculate_image_sizes', 'mis360_performance_catalog_image_sizes', 10, 2);

/**
 * 6. Image Attributes: decoding="async" and native lazy loading
 */
function mis360_performance_image_attributes($attr, $attachment, $size) {
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'mis360_performance_image_attributes', 10, 3);

/**
 * 7. Modern Image Formats: Generate WebP on upload (WordPress 5.8+)
 */
function mis360_performance_image_editor_output_format($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    return $formats;
}
add_filter('image_editor_output_format', 'mis360_performance_image_editor_output_format');

/**
 * 8. On-the-fly WebP Delivery Filter
 * If an uploaded image (PNG/JPG) has a .webp counterpart or can be converted via GD,
 * seamlessly serve the .webp version to cut download size by 70-90%.
 */
function mis360_performance_serve_webp_attachment($image, $attachment_id, $size, $icon) {
    if (!$image || !is_array($image) || empty($image[0])) {
        return $image;
    }

    $url = $image[0];
    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return $image;
    }

    // Resolve local file path
    $upload_dir = wp_get_upload_dir();
    $base_url   = $upload_dir['baseurl'];
    $base_dir   = $upload_dir['basedir'];

    if (strpos($url, $base_url) !== 0) {
        return $image;
    }

    $rel_path    = substr($url, strlen($base_url));
    $file_path   = $base_dir . $rel_path;
    $webp_path   = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_path);
    $webp_url    = preg_replace('/\.(jpe?g|png)$/i', '.webp', $url);

    // If WebP already exists on disk, serve it immediately
    if (file_exists($webp_path)) {
        $image[0] = $webp_url;
        return $image;
    }

    // Try on-the-fly conversion if GD / Imagick is available and source file exists
    if (file_exists($file_path) && function_exists('imagewebp') && is_readable($file_path)) {
        $created = false;
        if ($ext === 'png' && function_exists('imagecreatefrompng')) {
            $img = @imagecreatefrompng($file_path);
            if ($img) {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
                $created = @imagewebp($img, $webp_path, 80);
                imagedestroy($img);
            }
        } elseif (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('imagecreatefromjpeg')) {
            $img = @imagecreatefromjpeg($file_path);
            if ($img) {
                $created = @imagewebp($img, $webp_path, 82);
                imagedestroy($img);
            }
        }

        if ($created && file_exists($webp_path)) {
            $image[0] = $webp_url;
        }
    }

    return $image;
}
add_filter('wp_get_attachment_image_src', 'mis360_performance_serve_webp_attachment', 10, 4);

/**
 * 9. Browser Caching Rules (.htaccess & Send Headers)
 * Upgrades caching duration from 7 days to 1 year (31536000s)
 */
function mis360_performance_send_cache_headers() {
    if (is_admin()) {
        return;
    }
    // Set caching header for static requests handled by WordPress
    if (!empty($_SERVER['REQUEST_URI'])) {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        if (preg_match('/\.(?:ico|pdf|jpg|jpeg|png|gif|webp|js|css|woff2?|svg)$/i', $uri)) {
            header('Cache-Control: public, max-age=31536000, immutable');
            header_remove('Pragma');
            header_remove('Expires');
        }
    }
}
add_action('send_headers', 'mis360_performance_send_cache_headers');

/**
 * Automatic .htaccess Browser Caching Injection
 * Runs on theme switch or admin load to ensure Apache / LiteSpeed caches for 1 year
 */
function mis360_performance_update_htaccess_rules() {
    $marker = 'MIS360_BROWSER_CACHE';
    $htaccess_file = get_home_path() . '.htaccess';

    if (!file_exists($htaccess_file) || !is_writable($htaccess_file)) {
        return;
    }

    $rules = [
        '<IfModule mod_expires.c>',
        '  ExpiresActive On',
        '  ExpiresByType image/webp "access plus 1 year"',
        '  ExpiresByType image/jpeg "access plus 1 year"',
        '  ExpiresByType image/png "access plus 1 year"',
        '  ExpiresByType image/svg+xml "access plus 1 year"',
        '  ExpiresByType text/css "access plus 1 year"',
        '  ExpiresByType application/javascript "access plus 1 year"',
        '  ExpiresByType font/woff2 "access plus 1 year"',
        '</IfModule>',
        '<IfModule mod_headers.c>',
        '  <FilesMatch "\\.(ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|woff2|woff|ttf|eot)$">',
        '    Header set Cache-Control "max-age=31536000, public, immutable"',
        '  </FilesMatch>',
        '</IfModule>'
    ];

    require_once ABSPATH . 'wp-admin/includes/misc.php';
    if (function_exists('insert_with_markers')) {
        insert_with_markers($htaccess_file, $marker, $rules);
    }
}
add_action('after_switch_theme', 'mis360_performance_update_htaccess_rules');
