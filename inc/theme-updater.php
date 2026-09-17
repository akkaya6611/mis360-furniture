<?php
/**
 * Mis360 Mobilya - GitHub Otomatik Tema Güncelleyici
 *
 * GitHub Releases API ve Main dalı üzerinden gerçek zamanlı kontrol sağlar.
 * Hem 'mis360-mobilya' hem de 'mis360-furniture' klasör adlarını dinamik olarak destekler.
 *
 * @package Mis360-Mobilya
 * @version 1.9.21
 */

if (!defined('ABSPATH')) {
    exit;
}

class Mis360_Theme_Updater {
    private $theme_slug;
    private $github_user;
    private $github_repo;
    private $github_token;
    private $github_branch;

    public function __construct() {
        // Aktif tema klasör adını dinamik olarak al
        $active_theme        = wp_get_theme();
        $this->theme_slug    = $active_theme->exists() ? $active_theme->get_stylesheet() : 'mis360-mobilya';
        $this->github_user   = 'akkaya6611';
        $this->github_repo   = 'mis360-furniture';
        $this->github_branch = 'main';
        $this->github_token  = defined('MIS360_GITHUB_TOKEN') ? MIS360_GITHUB_TOKEN : '';

        // WordPress tema güncelleme kancaları
        add_filter('pre_set_site_transient_update_themes', [$this, 'check_theme_update']);
        add_filter('site_transient_update_themes', [$this, 'check_theme_update']);
        add_filter('http_request_args', [$this, 'authenticate_github_request'], 10, 2);
        add_filter('upgrader_source_selection', [$this, 'fix_unpacked_theme_directory'], 10, 3);
        add_filter('themes_api', [$this, 'theme_popup_details'], 10, 3);
        add_action('admin_notices', [$this, 'render_update_notice']);
        add_action('admin_init', [$this, 'force_check_listener']);
    }

    /**
     * Güncellemeyi anında zorlamak için transient ve önbellek temizleyici
     */
    public function force_check_listener() {
        if (isset($_GET['force-check']) && current_user_can('update_themes')) {
            delete_transient('mis360_github_update_data');
            delete_site_transient('update_themes');
            if (function_exists('wp_clean_themes_cache')) {
                wp_clean_themes_cache();
            }
        }
    }

    /**
     * GitHub Releases API üzerinden en güncel sürüm bilgisini çeker (0 CDN Gecikmesi)
     */
    private function get_remote_theme_data($force = false) {
        $transient_key = 'mis360_github_update_data';

        global $pagenow;
        $is_update_page = is_admin() && in_array($pagenow, ['update-core.php', 'themes.php', 'update.php']);

        if (!$force && !isset($_GET['force-check']) && !$is_update_page) {
            $cached = get_transient($transient_key);
            if ($cached !== false && is_array($cached)) {
                return $cached;
            }
        }

        $headers = [
            'User-Agent' => 'WordPress-Theme-Updater',
            'Accept'     => 'application/vnd.github.v3+json',
        ];
        if (!empty($this->github_token)) {
            $headers['Authorization'] = 'Bearer ' . $this->github_token;
        }

        // 1. Önce doğrudan GitHub Releases API'yi dene (En güncel ve kesin yol)
        $api_url = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->github_user, $this->github_repo);
        $response = wp_remote_get($api_url, [
            'headers'   => $headers,
            'timeout'   => 12,
            'sslverify' => false,
        ]);

        $remote_version = null;
        $package_url    = null;

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($body['tag_name'])) {
                $remote_version = ltrim($body['tag_name'], 'vV');
                
                // Assetlerden uygun zip'i bul
                if (!empty($body['assets']) && is_array($body['assets'])) {
                    foreach ($body['assets'] as $asset) {
                        if ($asset['name'] === $this->theme_slug . '.zip' || $asset['name'] === 'mis360-mobilya.zip') {
                            $package_url = $asset['browser_download_url'];
                            break;
                        }
                    }
                }
            }
        }

        // 2. API yanıt vermezse style.css fallback
        if (empty($remote_version)) {
            $raw_url = sprintf(
                'https://raw.githubusercontent.com/%s/%s/%s/style.css?t=%d',
                $this->github_user,
                $this->github_repo,
                $this->github_branch,
                time()
            );

            $raw_response = wp_remote_get($raw_url, [
                'headers'   => ['User-Agent' => 'WordPress-Theme-Updater'],
                'timeout'   => 12,
                'sslverify' => false,
            ]);

            if (!is_wp_error($raw_response) && wp_remote_retrieve_response_code($raw_response) === 200) {
                $style_content = wp_remote_retrieve_body($raw_response);
                if (preg_match('/Version:\s*([^
]+)/i', $style_content, $matches)) {
                    $remote_version = trim($matches[1]);
                }
            }
        }

        if (empty($remote_version)) {
            return false;
        }

        if (empty($package_url)) {
            $package_url = sprintf(
                'https://github.com/%s/%s/releases/download/v%s/%s.zip',
                $this->github_user,
                $this->github_repo,
                $remote_version,
                $this->theme_slug
            );
        }

        $data = [
            'version'     => $remote_version,
            'package_url' => $package_url,
            'repo_url'    => sprintf('https://github.com/%s/%s', $this->github_user, $this->github_repo),
        ];

        // 5 dakika önbelleğe al
        set_transient($transient_key, $data, 5 * MINUTE_IN_SECONDS);

        return $data;
    }

    /**
     * WordPress tema güncelleme listesine GitHub sürümünü enjekte eder
     */
    public function check_theme_update($transient) {
        if (empty($transient) || !is_object($transient)) {
            $transient = new stdClass();
        }

        $remote_data = $this->get_remote_theme_data();
        if (!$remote_data) {
            return $transient;
        }

        $active_theme  = wp_get_theme();
        $active_slug   = $active_theme->exists() ? $active_theme->get_stylesheet() : $this->theme_slug;
        $local_version = $active_theme->exists() ? $active_theme->get('Version') : '1.0.0';

        if (version_compare($remote_data['version'], $local_version, '>')) {
            $transient->response[$active_slug] = [
                'theme'        => $active_slug,
                'new_version'  => $remote_data['version'],
                'url'          => $remote_data['repo_url'],
                'package'      => $remote_data['package_url'],
                'requires'     => '6.0',
                'requires_php' => '7.4',
            ];
        } else {
            // Sürüm zaten güncelse eski bildirimleri tamamen temizle
            unset($transient->response[$active_slug]);
            unset($transient->response['mis360-mobilya']);
            unset($transient->response['mis360-furniture']);
        }

        return $transient;
    }

    /**
     * Yönetim panelinde bildirim ve tek tıkla güncelleme butonu sunar
     */
    public function render_update_notice() {
        if (!current_user_can('update_themes')) {
            return;
        }

        $active_theme = wp_get_theme();
        $active_slug  = $active_theme->exists() ? $active_theme->get_stylesheet() : $this->theme_slug;
        $local_ver    = $active_theme->exists() ? $active_theme->get('Version') : '1.0.0';

        $remote_data = $this->get_remote_theme_data();
        if (!$remote_data) {
            return;
        }

        // Eğer mevcut sürüm zaten uzaktaki sürüme eşit ya da daha büyükse bildirim gösterme
        if (version_compare($remote_data['version'], $local_ver, '<=')) {
            return;
        }

        $update_url = wp_nonce_url(
            admin_url('update.php?action=upgrade-theme&theme=' . urlencode($active_slug)),
            'upgrade-theme_' . $active_slug
        );
        ?>
        <div class="notice notice-warning is-dismissible" style="border-left-color: #ff6000; padding: 14px 18px; margin-top: 15px;">
            <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px;">
                🚀 Mis360-Mobilya Tema Güncellemesi Mevcut!
            </p>
            <p style="margin: 0 0 10px; color: #475569;">
                GitHub üzerinde yeni bir sürüm yayınlandı (<strong>v<?php echo esc_html($remote_data['version']); ?></strong>). Mevcut yüklü sürümünüz: <code>v<?php echo esc_html($local_ver); ?></code>.
            </p>
            <p style="margin: 0;">
                <a href="<?php echo esc_url($update_url); ?>" class="button button-primary" style="background: #ff6000; border-color: #e05300; font-weight: 700; padding: 4px 16px; height: auto;">
                    Şimdi Tek Tıkla Temayı Güncelle
                </a>
                &nbsp;
                <a href="<?php echo esc_url(admin_url('update-core.php?force-check=1')); ?>" class="button button-secondary">
                    🔄 Kontrolü Yenile
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * GitHub private repo indirmeleri için HTTP isteğine Bearer Token ekler
     */
    public function authenticate_github_request($args, $url) {
        if (!empty($this->github_token) && strpos($url, 'api.github.com/repos/' . $this->github_user . '/' . $this->github_repo) !== false) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->github_token;
            $args['headers']['Accept']        = 'application/vnd.github+json';
            $args['sslverify']                = false;
        }
        return $args;
    }

    /**
     * GitHub zip açıldığında oluşan klasör adını aktif tema dizinine ('mis360-mobilya' veya 'mis360-furniture') eşitler
     */
    public function fix_unpacked_theme_directory($source, $remote_source, $upgrader) {
        global $wp_filesystem;

        $active_theme    = wp_get_theme();
        $target_dir_name = $active_theme->exists() ? $active_theme->get_stylesheet() : $this->theme_slug;
        $is_our_theme    = false;

        if (isset($upgrader->skin->theme) && in_array($upgrader->skin->theme, ['mis360-mobilya', 'mis360-furniture', $target_dir_name], true)) {
            $is_our_theme = true;
        } elseif (isset($upgrader->skin->theme_info) && is_object($upgrader->skin->theme_info)) {
            $is_our_theme = true;
        } elseif (strpos(basename($source), 'mis360') !== false || strpos(basename($source), $this->github_repo) !== false || strpos(basename($source), $this->github_user) !== false) {
            $is_our_theme = true;
        }

        if ($is_our_theme) {
            $correct_source = trailingslashit($remote_source) . $target_dir_name . '/';
            if (trailingslashit($source) !== $correct_source) {
                if ($wp_filesystem->move($source, $correct_source)) {
                    return $correct_source;
                }
            }
        }

        return $source;
    }

    /**
     * 'Sürüm ayrıntılarını görüntüle' tıklandığında açılan popup penceresi
     */
    public function theme_popup_details($result, $action, $args) {
        if ($action !== 'theme_information' || !isset($args->slug) || !in_array($args->slug, [$this->theme_slug, 'mis360-mobilya', 'mis360-furniture'], true)) {
            return $result;
        }

        $remote_data = $this->get_remote_theme_data();
        $theme = wp_get_theme();

        $res = new stdClass();
        $res->name          = $theme->get('Name');
        $res->slug          = $this->theme_slug;
        $res->version       = $remote_data ? $remote_data['version'] : $theme->get('Version');
        $res->author        = $theme->get('Author');
        $res->homepage      = sprintf('https://github.com/%s/%s', $this->github_user, $this->github_repo);
        $res->requires      = '6.0';
        $res->requires_php  = '7.4';
        $res->download_link = $remote_data ? $remote_data['package_url'] : '';
        $res->sections      = [
            'description' => 'Emdief Home markasına özel, Montessori felsefeli 1. sınıf kaliteli MDF WooCommerce çocuk mobilyası teması.',
            'changelog'   => 'Son güncellemeler doğrudan GitHub main dalından otomatik olarak senkronize edilmektedir.',
        ];

        return $res;
    }
}

// Başlat
new Mis360_Theme_Updater();
