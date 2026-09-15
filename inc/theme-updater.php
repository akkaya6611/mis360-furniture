<?php
/**
 * Mis360 Mobilya - GitHub Otomatik Tema Güncelleyici
 *
 * Bu modül, GitHub üzerindeki 'main' dalını kontrol ederek WordPress
 * panelinde yerel güncelleme bildirimleri sunar ve tek tıkla güncelleme sağlar.
 *
 * @package Mis360-Mobilya
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
        $this->theme_slug    = 'mis360-mobilya';
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
     * Güncellemeyi anında zorlamak için transient temizleyici
     */
    public function force_check_listener() {
        if (isset($_GET['force-check']) && current_user_can('update_themes')) {
            delete_transient('mis360_github_update_data');
            delete_site_transient('update_themes');
        }
    }

    /**
     * GitHub üzerinden en güncel sürüm bilgisini çeker
     */
    private function get_remote_theme_data($force = false) {
        $transient_key = 'mis360_github_update_data';

        global $pagenow;
        $is_update_page = is_admin() && in_array($pagenow, ['update-core.php', 'themes.php', 'update.php']);

        if (!$force && !isset($_GET['force-check']) && !$is_update_page) {
            $cached = get_transient($transient_key);
            if ($cached !== false) {
                return $cached;
            }
        }

        $raw_url = sprintf(
            'https://raw.githubusercontent.com/%s/%s/%s/style.css?t=%d',
            $this->github_user,
            $this->github_repo,
            $this->github_branch,
            time()
        );

        $headers = [
            'User-Agent' => 'WordPress-Theme-Updater',
        ];
        if (!empty($this->github_token)) {
            $headers['Authorization'] = 'Bearer ' . $this->github_token;
        }

        $args = [
            'headers'   => $headers,
            'timeout'   => 15,
            'sslverify' => false,
        ];

        $response = wp_remote_get($raw_url, $args);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $style_content = wp_remote_retrieve_body($response);

        // Version bilgisini ayrıştır
        if (!preg_match('/Version:\s*([^\r\n]+)/i', $style_content, $matches)) {
            return false;
        }

        $remote_version = trim($matches[1]);

        $data = [
            'version'     => $remote_version,
            'package_url' => sprintf(
                'https://github.com/%s/%s/releases/download/v%s/mis360-mobilya.zip',
                $this->github_user,
                $this->github_repo,
                $remote_version
            ),
            'repo_url'    => sprintf('https://github.com/%s/%s', $this->github_user, $this->github_repo),
        ];

        // 10 dakika önbelleğe al
        set_transient($transient_key, $data, 10 * MINUTE_IN_SECONDS);

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

        // Hem aktif tema slug'ını hem de mis360-mobilya ve mis360-furniture'ı kontrol et
        $current_slug = function_exists('get_template') ? get_template() : $this->theme_slug;
        $target_slugs = array_unique([$this->theme_slug, $current_slug, 'mis360-mobilya', 'mis360-furniture']);

        foreach ($target_slugs as $slug) {
            $theme = wp_get_theme($slug);
            if ($theme->exists()) {
                $local_version = $theme->get('Version');
                if (version_compare($remote_data['version'], $local_version, '>')) {
                    $transient->response[$slug] = [
                        'theme'        => $slug,
                        'new_version'  => $remote_data['version'],
                        'url'          => $remote_data['repo_url'],
                        'package'      => $remote_data['package_url'],
                        'requires'     => '6.0',
                        'requires_php' => '7.4',
                    ];
                }
            }
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

        $remote_data = $this->get_remote_theme_data();
        if (!$remote_data) {
            return;
        }

        $current_slug = function_exists('get_template') ? get_template() : $this->theme_slug;
        $theme = wp_get_theme($current_slug);
        $local_ver = $theme->exists() ? $theme->get('Version') : '1.0.0';

        if (version_compare($remote_data['version'], $local_ver, '>')) {
            $update_url = wp_nonce_url(
                admin_url('update.php?action=upgrade-theme&theme=' . urlencode($current_slug)),
                'upgrade-theme_' . $current_slug
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
     * GitHub zipball açıldığında oluşan 'user-repo-sha' klasör adını 'mis360-furniture' olarak düzeltir
     */
    public function fix_unpacked_theme_directory($source, $remote_source, $upgrader) {
        global $wp_filesystem;

        $target_dir_name = $this->theme_slug;
        $is_our_theme    = false;

        if (isset($upgrader->skin->theme) && $upgrader->skin->theme === $this->theme_slug) {
            $is_our_theme = true;
        } elseif (strpos(basename($source), $this->github_repo) !== false || strpos(basename($source), $this->github_user) !== false) {
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
        if ($action !== 'theme_information' || !isset($args->slug) || $args->slug !== $this->theme_slug) {
            return $result;
        }

        $remote_data = $this->get_remote_theme_data();

        $theme = wp_get_theme($this->theme_slug);
        if (!$theme->exists()) {
            $theme = wp_get_theme();
        }

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
