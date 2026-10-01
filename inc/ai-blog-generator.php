<?php
/**
 * Emdief Home - AI Blog & 81 İl/İlçe Yerel SEO Rehber Motoru
 *
 * - Yapay Zeka Entegrasyonları: Google Gemini, OpenAI (ChatGPT), Groq ve Nvidia AI (NIM)
 * - Anne Dilinde Sıcak, Samimi, Doğal ve Gerçekçi İçerik Üretimi
 * - 81 İl ve Tüm İlçeler İçin Otomatik Programmatic Local SEO Rehber Motoru
 * - AJAX Kademeli Parti Üretim (Sunucuyu ve Rate Limit'leri Yormayan Akıllı Kuyruk)
 * - Otomatik Kategori, Etiket, Meta ve İlgili Ürün CTA Entegrasyonu
 *
 * @package Mis360-Mobilya
 * @version 1.9.90
 * @author Serkan AKKAYA & MİS360
 */

defined('ABSPATH') || exit;

// İl & İlçe Veritabanını Yükle
require_once __DIR__ . '/turkey-locations.php';

class Emdief_AI_Blog_Generator {

    public function __construct() {
        // Admin Menüsü
        add_action('admin_menu', [$this, 'register_admin_menu']);

        // AJAX İşlemleri
        add_action('wp_ajax_mis360_ai_generate_single', [$this, 'ajax_generate_single_post']);
        add_action('wp_ajax_mis360_ai_test_api', [$this, 'ajax_test_api_connection']);
        add_action('wp_ajax_mis360_ai_save_api_settings', [$this, 'ajax_save_api_settings']);
        add_action('wp_ajax_mis360_ai_fetch_models', [$this, 'ajax_fetch_models']);

        // Blog Tekil Yazı İçine Samimi Emdief Home CTA Kartı Enjeksiyonu
        add_filter('the_content', [$this, 'inject_blog_post_cta']);
    }

    /**
     * Sağlayıcı API'sinden Canlı Model Listesini Çek
     */
    public static function fetch_available_models($provider, $api_key = null) {
        if (empty($api_key)) {
            $api_key = get_option('mis360_' . $provider . '_api_key');
        }
        $api_key = trim((string)$api_key);
        if (empty($api_key)) {
            return ['success' => false, 'message' => 'API anahtarı bulunamadı.', 'models' => []];
        }

        // 1. GOOGLE GEMINI
        if ($provider === 'gemini') {
            $urls = [
                'https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($api_key),
                'https://generativelanguage.googleapis.com/v1/models?key=' . urlencode($api_key)
            ];

            $last_err = 'Bilinmeyen hata';
            foreach ($urls as $url) {
                $response = wp_remote_get($url, [
                    'timeout' => 15,
                    'headers' => [
                        'Content-Type'   => 'application/json; charset=utf-8',
                        'x-goog-api-key' => $api_key
                    ]
                ]);

                if (is_wp_error($response)) {
                    $last_err = $response->get_error_message();
                    continue;
                }

                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (!empty($body['models']) && is_array($body['models'])) {
                    $models = [];
                    foreach ($body['models'] as $m) {
                        $name = str_replace('models/', '', $m['name'] ?? '');
                        $methods = $m['supportedGenerationMethods'] ?? [];
                        if (in_array('generateContent', $methods, true) && !empty($name)) {
                            if (strpos($name, 'embedding') === false && strpos($name, 'aqa') === false) {
                                $models[] = $name;
                            }
                        }
                    }

                    if (!empty($models)) {
                        usort($models, function($a, $b) {
                            $scoreA = (strpos($a, '2.0-flash') !== false ? 100 : (strpos($a, '2.5-flash') !== false ? 90 : (strpos($a, 'flash') !== false ? 80 : 50)));
                            $scoreB = (strpos($b, '2.0-flash') !== false ? 100 : (strpos($b, '2.5-flash') !== false ? 90 : (strpos($b, 'flash') !== false ? 80 : 50)));
                            return $scoreB <=> $scoreA;
                        });
                        return ['success' => true, 'models' => array_values(array_unique($models))];
                    }
                }

                if (!empty($body['error']['message'])) {
                    $last_err = $body['error']['message'];
                }
            }

            return ['success' => false, 'message' => 'Gemini Hatası: ' . $last_err, 'models' => []];
        }

        // 2. GROQ
        elseif ($provider === 'groq') {
            $response = wp_remote_get('https://api.groq.com/openai/v1/models', [
                'timeout' => 15,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json; charset=utf-8'
                ]
            ]);

            if (is_wp_error($response)) {
                return ['success' => false, 'message' => 'Groq Hatası: ' . $response->get_error_message(), 'models' => []];
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($body['data']) && is_array($body['data'])) {
                $models = [];
                foreach ($body['data'] as $m) {
                    $id = $m['id'] ?? '';
                    if (!empty($id) && strpos($id, 'whisper') === false && strpos($id, 'guard') === false) {
                        $models[] = $id;
                    }
                }
                if (!empty($models)) {
                    usort($models, function($a, $b) {
                        $scoreA = (strpos($a, 'instant') !== false ? 100 : (strpos($a, 'llama-3.1') !== false ? 90 : 50));
                        $scoreB = (strpos($b, 'instant') !== false ? 100 : (strpos($b, 'llama-3.1') !== false ? 90 : 50));
                        return $scoreB <=> $scoreA;
                    });
                    return ['success' => true, 'models' => array_values(array_unique($models))];
                }
            }

            $err = $body['error']['message'] ?? 'Modeller listelenemedi.';
            return ['success' => false, 'message' => 'Groq Hatası: ' . $err, 'models' => []];
        }

        // 3. NVIDIA AI (NIM)
        elseif ($provider === 'nvidia') {
            $response = wp_remote_get('https://integrate.api.nvidia.com/v1/models', [
                'timeout' => 15,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json; charset=utf-8'
                ]
            ]);

            if (is_wp_error($response)) {
                return ['success' => false, 'message' => 'Nvidia Hatası: ' . $response->get_error_message(), 'models' => []];
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($body['data']) && is_array($body['data'])) {
                $models = [];
                foreach ($body['data'] as $m) {
                    $id = $m['id'] ?? '';
                    if (!empty($id) && (strpos($id, 'nemotron') !== false || strpos($id, 'llama') !== false || strpos($id, 'instruct') !== false || strpos($id, 'mistral') !== false)) {
                        $models[] = $id;
                    }
                }
                if (!empty($models)) {
                    return ['success' => true, 'models' => array_values(array_unique($models))];
                }
            }

            return [
                'success' => true,
                'models' => [
                    'nvidia/llama-3.1-nemotron-70b-instruct',
                    'meta/llama-3.2-11b-vision-instruct',
                    'mistralai/mistral-large-2-instruct',
                    'deepseek-ai/deepseek-v4.1-flash'
                ]
            ];
        }

        // 4. OPENAI
        elseif ($provider === 'openai') {
            $response = wp_remote_get('https://api.openai.com/v1/models', [
                'timeout' => 15,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json; charset=utf-8'
                ]
            ]);

            if (is_wp_error($response)) {
                return ['success' => false, 'message' => 'OpenAI Hatası: ' . $response->get_error_message(), 'models' => []];
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($body['data']) && is_array($body['data'])) {
                $models = [];
                foreach ($body['data'] as $m) {
                    $id = $m['id'] ?? '';
                    if (strpos($id, 'gpt-') === 0 && strpos($id, 'audio') === false && strpos($id, 'realtime') === false) {
                        $models[] = $id;
                    }
                }
                if (!empty($models)) {
                    return ['success' => true, 'models' => array_values(array_unique($models))];
                }
            }

            return [
                'success' => true,
                'models' => ['gpt-4o-mini', 'gpt-4o', 'gpt-3.5-turbo']
            ];
        }

        return ['success' => false, 'message' => 'Geçersiz sağlayıcı.', 'models' => []];
    }

    /**
     * Admin Menü Kaydı
     */
    public function register_admin_menu() {
        add_menu_page(
            'Blog & AI Rehber Motoru',
            'AI Blog Motoru',
            'manage_options',
            'mis360-ai-blog',
            [$this, 'render_admin_page'],
            'dashicons-welcome-write-blog',
            57
        );
    }

    /**
     * Blog Yazısı İçine Şık, Samimi Anne Dostu Emdief Home CTA Kartı
     */
    public function inject_blog_post_cta($content) {
        if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $post_id   = get_the_ID();
        $city      = get_post_meta($post_id, '_mis360_ai_city', true);
        $district  = get_post_meta($post_id, '_mis360_ai_district', true);
        $phone     = get_theme_mod('mis360_phone', '0537 477 87 66');
        $whatsapp  = get_theme_mod('mis360_whatsapp', '905374778766');
        $clean_wa  = preg_replace('/[^0-9]/', '', (string) $whatsapp);
        $shop_url  = class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/');

        $location_text = '';
        if (!empty($city)) {
            $location_text = (!empty($district) ? esc_html($district) . ' / ' : '') . esc_html($city);
        }

        ob_start();
        ?>
        <div class="emdief-blog-cta-box" style="margin: 35px 0 25px; padding: 26px 28px; background: linear-gradient(135deg, #fdfbf7 0%, #fff7ed 100%); border: 2px dashed #fed7aa; border-radius: 16px; font-family: inherit;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom: 12px;">
                <span style="font-size: 28px;">🧸</span>
                <div>
                    <span style="display:inline-block; background:#ea580c; color:#fff; font-size:11px; font-weight:700; text-transform:uppercase; padding:3px 9px; border-radius:12px;">Emdief Home Anneler Kulübü</span>
                    <h3 style="margin:4px 0 0; font-size:18px; font-weight:800; color:#0f172a;">
                        Miniklerin Odasına Güven, Doğallık ve Neşe Katıyoruz
                    </h3>
                </div>
            </div>
            
            <p style="font-size:14px; color:#475569; line-height:1.6; margin:0 0 16px 0;">
                <?php if (!empty($location_text)) : ?>
                    <strong><?php echo $location_text; ?></strong> bölgesindeki tüm ebeveynlerimize, Kayseri Mobilya Kent'teki atölyemizden doğrudan 1. sınıf MDF ve masif kayın ağacından üretilen çocuk mobilyalarını <strong>1.500 TL üzeri ücretsiz kargo</strong> ve <strong>hasarsız teslimat garantisiyle</strong> ulaştırıyoruz.
                <?php else : ?>
                    Kayseri Mobilya Kent'teki atölyemizde sevgiyle ürettiğimiz sivri köşesiz, E1 normlarında 1. sınıf MDF Montessori kitaplık ve mobilyalarımızı <strong>tüm Türkiye'ye 1.500 TL üzeri ücretsiz kargo</strong> ve darbe emici sigortalı ambalajla kapınıza kadar getiriyoruz.
                <?php endif; ?>
            </p>

            <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                <a href="<?php echo esc_url($shop_url); ?>" class="button" style="background:#ea580c; border-color:#ea580c; color:#ffffff; font-weight:700; padding:8px 18px; border-radius:8px; text-decoration:none;">
                    📦 Montessori Modellerini İncele &rarr;
                </a>
                <a href="https://wa.me/<?php echo esc_attr($clean_wa); ?>?text=<?php echo rawurlencode('Merhaba, rehber yazınızı okudum. Montessori mobilyalarınız hakkında anne tavsiyesi ve bilgi almak istiyorum.'); ?>" target="_blank" rel="noopener noreferrer" class="button" style="background:#25d366; border-color:#25d366; color:#ffffff; font-weight:700; padding:8px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    💬 Annelerimize Özel WhatsApp Danışma
                </a>
            </div>
        </div>
        <?php
        $cta_html = ob_get_clean();

        return $content . $cta_html;
    }

    /**
     * API İstek Motoru (Gemini, OpenAI, Groq, Nvidia)
     * Akıllı Otomatik Model Geçişi & Detaylı Hata Yakalama
     */
    public static function call_ai_api($system_prompt, $user_prompt, $provider = null) {
        if (empty($provider)) {
            $provider = get_option('mis360_ai_provider', 'gemini');
        }

        // 1. GOOGLE GEMINI
        if ($provider === 'gemini') {
            $api_key = get_option('mis360_gemini_api_key');
            $chosen_model = get_option('mis360_gemini_model', 'gemini-2.5-flash');

            if (empty($api_key)) {
                return ['success' => false, 'message' => 'Google Gemini API Anahtarı girilmemiş.'];
            }

            // Otomatik model deneme sırası (2.5 Flash, 2.0 Flash, 2.5 Flash Lite, 1.5 Latest)
            $models_to_try = array_values(array_unique(array_filter([
                $chosen_model,
                'gemini-2.5-flash',
                'gemini-2.0-flash',
                'gemini-2.5-flash-lite',
                'gemini-1.5-flash-latest',
                'gemini-1.5-flash-002',
                'gemini-1.5-pro-latest'
            ])));

            $last_error = 'Bilinmeyen Gemini yanıtı.';
            foreach ($models_to_try as $model_item) {
                $endpoint = sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    urlencode($model_item),
                    urlencode($api_key)
                );

                $payload = [
                    'system_instruction' => [
                        'parts' => [['text' => $system_prompt]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $user_prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.75,
                        'maxOutputTokens' => 3000,
                    ]
                ];

                $response = wp_remote_post($endpoint, [
                    'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
                    'body'    => wp_json_encode($payload),
                    'timeout' => 60,
                ]);

                if (is_wp_error($response)) {
                    $last_error = $response->get_error_message();
                    continue;
                }

                $code = wp_remote_retrieve_response_code($response);
                $body = json_decode(wp_remote_retrieve_body($response), true);

                if (!empty($body['candidates'][0]['content']['parts'][0]['text'])) {
                    if ($model_item !== $chosen_model) {
                        update_option('mis360_gemini_model', $model_item);
                    }
                    return ['success' => true, 'text' => $body['candidates'][0]['content']['parts'][0]['text'], 'model' => $model_item];
                }

                if (!empty($body['error']['message'])) {
                    $last_error = $body['error']['message'];
                    if (strpos($last_error, 'not found') !== false || strpos($last_error, 'not supported') !== false || $code === 404) {
                        continue;
                    }
                    break;
                }
            }

            return ['success' => false, 'message' => 'Gemini Hata: ' . $last_error];
        }

        // 2. OPENAI (CHATGPT)
        elseif ($provider === 'openai') {
            $api_key = get_option('mis360_openai_api_key');
            $model   = get_option('mis360_openai_model', 'gpt-4o-mini');

            if (empty($api_key)) {
                return ['success' => false, 'message' => 'OpenAI API Anahtarı girilmemiş.'];
            }

            $endpoint = 'https://api.openai.com/v1/chat/completions';
            $payload  = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user', 'content' => $user_prompt]
                ],
                'temperature' => 0.75,
                'max_tokens'  => 3000,
            ];

            $response = wp_remote_post($endpoint, [
                'headers' => [
                    'Content-Type'  => 'application/json; charset=utf-8',
                    'Authorization' => 'Bearer ' . $api_key,
                ],
                'body'    => wp_json_encode($payload),
                'timeout' => 60,
            ]);

            if (is_wp_error($response)) {
                return ['success' => false, 'message' => 'OpenAI Hatası: ' . $response->get_error_message()];
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($body['choices'][0]['message']['content'])) {
                return ['success' => true, 'text' => $body['choices'][0]['message']['content']];
            } else {
                $err = $body['error']['message'] ?? 'Bilinmeyen OpenAI yanıtı.';
                if (strpos($err, 'no credits') !== false || strpos($err, 'insufficient_quota') !== false) {
                    $err .= ' (💡 Tavsiye: OpenAI hesabınızda bakiye kalmadığında tamamen ücretsiz çalışan Google Gemini veya Groq sağlayıcısını seçebilirsiniz.)';
                }
                return ['success' => false, 'message' => 'OpenAI Hata: ' . $err];
            }
        }

        // 3. GROQ (ULTRA HIZLI LLAMA)
        elseif ($provider === 'groq') {
            $api_key = get_option('mis360_groq_api_key');
            $chosen_model = get_option('mis360_groq_model', 'llama-3.1-8b-instant');

            if (empty($api_key)) {
                return ['success' => false, 'message' => 'Groq API Anahtarı girilmemiş.'];
            }

            // Groq güncel modelleri (versatile kaldırıldığı için instant & llama3 öncelikli)
            $models_to_try = array_values(array_unique(array_filter([
                $chosen_model,
                'llama-3.1-8b-instant',
                'llama-3.3-70b-specdec',
                'llama3-70b-8192',
                'llama3-8b-8192',
                'mixtral-8x7b-32768',
                'gemma2-9b-it'
            ])));

            $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
            $last_error = 'Bilinmeyen Groq yanıtı.';

            foreach ($models_to_try as $model_item) {
                $payload  = [
                    'model'    => $model_item,
                    'messages' => [
                        ['role' => 'system', 'content' => $system_prompt],
                        ['role' => 'user', 'content' => $user_prompt]
                    ],
                    'temperature' => 0.75,
                    'max_tokens'  => 3500,
                ];

                $response = wp_remote_post($endpoint, [
                    'headers' => [
                        'Content-Type'  => 'application/json; charset=utf-8',
                        'Authorization' => 'Bearer ' . $api_key,
                    ],
                    'body'    => wp_json_encode($payload),
                    'timeout' => 45,
                ]);

                if (is_wp_error($response)) {
                    $last_error = $response->get_error_message();
                    continue;
                }

                $code = wp_remote_retrieve_response_code($response);
                $body = json_decode(wp_remote_retrieve_body($response), true);

                if (!empty($body['choices'][0]['message']['content'])) {
                    if ($model_item !== $chosen_model) {
                        update_option('mis360_groq_model', $model_item);
                    }
                    return ['success' => true, 'text' => $body['choices'][0]['message']['content'], 'model' => $model_item];
                }

                if (!empty($body['error']['message'])) {
                    $last_error = $body['error']['message'];
                    if (strpos($last_error, 'does not exist') !== false || strpos($last_error, 'decommissioned') !== false || $code === 404) {
                        continue;
                    }
                    break;
                }
            }

            return ['success' => false, 'message' => 'Groq Hata: ' . $last_error];
        }

        // 4. NVIDIA AI (NIM)
        elseif ($provider === 'nvidia') {
            $api_key = get_option('mis360_nvidia_api_key');
            $chosen_model = get_option('mis360_nvidia_model', 'nvidia/llama-3.1-nemotron-70b-instruct');

            if (empty($api_key)) {
                return ['success' => false, 'message' => 'Nvidia AI API Anahtarı girilmemiş.'];
            }

            $models_to_try = array_values(array_unique(array_filter([
                $chosen_model,
                'nvidia/llama-3.1-nemotron-70b-instruct',
                'meta/llama-3.2-11b-vision-instruct',
                'mistralai/mistral-large-2-instruct',
                'deepseek-ai/deepseek-v4.1-flash',
                'meta/llama-3.2-90b-vision-instruct'
            ])));

            $endpoint = 'https://integrate.api.nvidia.com/v1/chat/completions';
            $last_error = 'Bilinmeyen Nvidia yanıtı.';

            foreach ($models_to_try as $model_item) {
                $payload  = [
                    'model'    => $model_item,
                    'messages' => [
                        ['role' => 'system', 'content' => $system_prompt],
                        ['role' => 'user', 'content' => $user_prompt]
                    ],
                    'temperature' => 0.75,
                    'max_tokens'  => 3500,
                ];

                $response = wp_remote_post($endpoint, [
                    'headers' => [
                        'Content-Type'  => 'application/json; charset=utf-8',
                        'Authorization' => 'Bearer ' . $api_key,
                    ],
                    'body'    => wp_json_encode($payload),
                    'timeout' => 60,
                ]);

                if (is_wp_error($response)) {
                    $last_error = $response->get_error_message();
                    continue;
                }

                $code = wp_remote_retrieve_response_code($response);
                $raw_body = wp_remote_retrieve_body($response);
                $body = json_decode($raw_body, true);

                if (!empty($body['choices'][0]['message']['content'])) {
                    if ($model_item !== $chosen_model) {
                        update_option('mis360_nvidia_model', $model_item);
                    }
                    return ['success' => true, 'text' => $body['choices'][0]['message']['content'], 'model' => $model_item];
                }

                if (!empty($body['detail'])) {
                    if (is_array($body['detail'])) {
                        $last_error = json_encode($body['detail'], JSON_UNESCAPED_UNICODE);
                    } else {
                        $last_error = (string)$body['detail'];
                    }
                } elseif (!empty($body['error']['message'])) {
                    $last_error = (string)$body['error']['message'];
                } elseif (!empty($body['message'])) {
                    $last_error = (string)$body['message'];
                } else {
                    $last_error = 'HTTP ' . $code . ': ' . substr($raw_body, 0, 200);
                }

                if ($code === 404 || strpos($last_error, 'not found') !== false || strpos($last_error, 'Model') !== false) {
                    continue;
                }
                break;
            }

            return ['success' => false, 'message' => 'Nvidia Hata: ' . $last_error];
        }

        return ['success' => false, 'message' => 'Geçersiz AI sağlayıcısı.'];
    }

    /**
     * Anne Dilinde Özel Sistem İstemi (System Prompt)
     */
    public static function get_mother_tone_system_prompt() {
        return <<<PROMPT
Sen deneyimli, şefkatli bir anne, Montessori felsefesine gönül vermiş bir ebeveyn ve çocuk mobilyası markası "Emdief Home"un samimi içerik yazarısın.

YAZIM KURALLARI & ANNE DİLİ:
1. Kesinlikle robotik, yapay zeka klişesi ("Günümüz dünyasında", "Sonuç olarak", "Bununla birlikte", "Özetlemek gerekirse") KULLANMA.
2. Yazıların kahvesini alıp diğer annelerle dertleşen, evin dağınıklığını, miniklerin güvenliğini, düşüp çarpmalarını samimiyetle anlayan bir annenin sıcak, içten "sen" ve "biz" dilinde olsun.
3. Çocukların boy hizasında kitaplara özgürce uzanmasının, kitap kapaklarını görerek kendi seçimlerini yapmasının onlara kattığı özgüveni vurgula.

EMDİEF HOME MARKA DEĞERLERİ & GERÇEKLERİ:
- İmalat Yeri: Kayseri Mobilya Kent'teki kendi yüksek teknolojili CNC entegre atölyemizde sevgi ve özenle üretiyoruz.
- Malzeme Güvencesi: E1 Avrupa standartlarında 1. sınıf dayanıklı MDF ve doğal masif kayın ağacı. Kokusuz, toksik madde içermeyen su bazlı çocuk dostu kaplama.
- Çocuk Güvenliği: Sivri köşe ASLA barındırmaz, tüm kenarlar pürüzsüz %100 yuvarlatılmış CNC kavislerle işlenmiştir.
- Kolay Montaj: Paket içinde alyanla uğraştırmaz; tüm parçalarda CNC tezgahlarda milimetrik montaj delikleri önceden açılmıştır. Yalnızca bir şarjlı matkap ile 5 dakikada tek kişi tarafından kolayca kurulur.
- Duvar Emniyeti: Çocukların tırmanma ihtimaline karşı kutudan çıkan emniyet sabitleme aparatıyla duvara sabitlenmesi tavsiye edilir.
- Kargo & Teslimat: 1.500 TL ve üzeri tüm siparişlerde tüm Türkiye'ye kargo tamamen ücretsizdir. Darbe emici özel sigortalı strafor ambalajla gönderilir. Saat 13:00'a kadar verilen siparişler aynı gün öncelikli imalat sırasına alınır.

YAZI FORMATI (HTML ÇIKTISI):
- Çıktıyı doğrudan temiz HTML olarak ver (gereksiz markdown backtickleri olmadan).
- H1 başlığı makalenin en başında <h1> ile başlasın.
- Alt başlıklar <h2> ve <h3> olsun.
- Paragraflar <p> ile ayrılsın.
- Vurgulanacak yerlerde <strong> kullan.
- Annelerin aklına takılan 2-3 pratik soru ve cevabı için samimi bir Soru-Cevap bölümü ekle.
- Sonunda bir anne tavsiyesi ve Emdief Home atölyesine selam içeren sıcacık bir kapanış yap.
PROMPT;
    }

    /**
     * Fallback Makale Motoru (API Key olmadan da anında kusursuz makale üretir)
     */
    public static function generate_fallback_article($city, $district, $template_type = 'bookcase') {
        $loc_title = (!empty($district) ? $district . ' ' : '') . $city;
        $title = sprintf('%s Montessori Çocuk Kitaplığı & Ahşap Mobilya Üreticisi | Emdief Home', $loc_title);

        $html = "<h1>" . esc_html($title) . "</h1>\n\n";
        $html .= "<p>Sevgili anneler ve miniklerine hayal gibi bir oda kurmak isteyen güzel aileler, merhaba!</p>\n\n";
        $html .= "<p>Eğer siz de <strong>" . esc_html($loc_title) . "</strong> bölgesinde oturuyor ve çocuğunuzun odasında hem onun bağımsızca kitaplarına uzanabileceği hem de gözünüz arkada kalmadan güvenle vakit geçirebileceği bir Montessori kitaplık arıyorsanız, doğru yerdesiniz. Bir anne olarak çok iyi biliyorum; piyasada o kadar çok plastik veya sivri köşeli kalitesiz sunta ürün var ki, insanın içine bir türlü sinmiyor.</p>\n\n";
        
        $html .= "<h2>Minik Ellerin Kendi Dünyasını Keşfetmesi: Neden Montessori?</h2>\n";
        $html .= "<p>Montessori felsefesi aslında çok sade bir gerçeğe dayanır: <em>'Çocuğun kendi yapabileceği bir şeyi onun yerine yapmayın.'</em> Klasik yetişkin kitaplıklarında kitaplar sırtları dönük ve yüksek raflarda durur; çocuk kitaba ulaşmak için ya sandalyeye tırmanır ya da anneden yardım ister. Oysa önden kapak sergileyen alçak bir Montessori kitaplıkta, miniğiniz o renkli kapakları görür, canı istediği kitabı kendi seçer ve işi bitince yerine koyar. Bu küçük alışkanlık, onların özgüvenini ve kitap sevgisini tahmin bile edemeyeceğiniz kadar büyütür.</p>\n\n";

        $html .= "<h2>Emdief Home Kayseri Atölyesinden " . esc_html($loc_title) . " Adresinize: Nasıl Üretiyoruz?</h2>\n";
        $html .= "<p>Biz bu işe kendi çocuklarımızın odasına mobilya ararken başladık. Kayseri Mobilya Kent'teki atölyemizde her bir parçayı titizlikle işliyoruz:</p>\n";
        $html .= "<ul>\n";
        $html .= "<li><strong>Sivri Köşesiz %100 Güvenli Kavisler:</strong> Çocuklar koşar, zıplar, düşer. Mobilyalarımızın hiçbir yerinde keskin köşe bulamazsınız. Tüm kenarlar CNC tezgahlarda pürüzsüz yuvarlatılmıştır.</li>\n";
        $html .= "<li><strong>E1 Sertifikalı 1. Sınıf MDF & Masif Ahşap:</strong> Kokusuz, sağlığa zararsız su bazlı kaplamalar kullanıyoruz. Bebeğinizin odasına girdiğinizde o ağır kimyasal vernik kokusunu asla duymazsınız.</li>\n";
        $html .= "<li><strong>5 Dakikada Zahmetsiz Kurulum:</strong> Alyanlarla saatlerce boğuşmanızı istemeyiz. Parçaların tamamında montaj delikleri milimetrik olarak hazır açılmıştır. Tek bir şarjlı matkap ile 5 dakikada tek başınıza kolayca kurabilirsiniz.</li>\n";
        $html .= "</ul>\n\n";

        $html .= "<h2>" . esc_html($city) . " Kargo ve Hasarsız Teslimat Güvencemiz</h2>\n";
        $html .= "<p><em>'" . esc_html($city) . " içine kargo sağlam gelir mi? Ya yolda kırılırsa?'</em> diye sakın endişelenmeyin. Ürünlerimizi darbe emici özel kalın straforlar ve koruyucu ambalajlarla sigortalı olarak gönderiyoruz. Taşıma sırasında oluşabilecek en ufak hasarda bile tek bir fotoğraf atmanız yeterli; sorgusuz sualsiz yeni parçayı hemen ücretsiz olarak adresinize kargoluyoruz.</p>\n";
        $html .= "<p>Ayrıca <strong>1.500 TL ve üzeri siparişlerinizde kargo tamamen ücretsizdir!</strong></p>\n\n";

        $html .= "<h2>Annelerin Merak Ettikleri (Sıkça Sorulanlar)</h2>\n";
        $html .= "<h3>Duvara sabitlemek şart mı?</h3>\n";
        $html .= "<p>Evet sevgili anneler! Minikler meraklıdır, bazen üst rafa uzanmak için tırmanmak isteyebilirler. Devrilme riskini sıfıra indirmek için kutudan çıkan emniyet sabitleme aparatıyla kitaplığı duvara sabitlemenizi önemle tavsiye ediyoruz.</p>\n";
        $html .= "<h3>Temizliği nasıl yapılmalı?</h3>\n";
        $html .= "<p>Pürüzsüz yüzeyi leke tutmaz. Nemli bir mikrofiber bez ile hafifçe silmeniz yeterlidir; ağır kimyasal deterjanlara kesinlikle gerek yoktur.</p>\n\n";

        $html .= "<p>Kayseri atölyemizden tüm <strong>" . esc_html($loc_title) . "</strong> ailelerimize ve pırıl pırıl miniklerimize kucak dolusu sevgiler gönderiyoruz. Bir sorunuz olursa WhatsApp hattımızdan bir anne sıcaklığıyla bize her zaman ulaşabilirsiniz!</p>";

        return [
            'title'   => $title,
            'content' => $html
        ];
    }

    /**
     * AJAX: Tekil Makale Üret & Kaydet
     */
    public function ajax_generate_single_post() {
        check_ajax_referer('mis360_ai_blog_nonce', 'security');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Yetkiniz bulunmuyor.']);
        }

        $city       = sanitize_text_field($_POST['city'] ?? '');
        $district   = sanitize_text_field($_POST['district'] ?? '');
        $custom_topic = sanitize_text_field($_POST['custom_topic'] ?? '');
        $provider   = sanitize_key($_POST['provider'] ?? '');
        $status     = sanitize_key($_POST['status'] ?? 'draft');
        if (!in_array($status, ['draft', 'publish'], true)) {
            $status = 'draft';
        }

        // Başlık ve Konu Belirleme
        $loc_string = '';
        if (!empty($city)) {
            $loc_string = (!empty($district) ? $district . ' / ' : '') . $city;
        }

        if (empty($custom_topic)) {
            $user_prompt = sprintf(
                "Türkiye'nin %s bölgesi için 'Montessori Çocuk Kitaplığı & Ahşap Çocuk Mobilyası Üreticisi' konulu, anne dilinde samimi ve gerçekçi bir blog rehberi yaz. Emdief Home markamızı, Kayseri atölyemizi, E1 normlarında 1. sınıf MDF malzememizi, sivri köşesiz güvenli hatlarımızı ve 1.500 TL üzeri ücretsiz kargo güvencemizi anlat.",
                $loc_string
            );
        } else {
            $user_prompt = sprintf(
                "Konu: %s. %s bölgesindeki ebeveynleri hedefleyen, anne dilinde sıcak ve samimi Emdief Home ahşap çocuk mobilyası blog rehberi yaz.",
                $custom_topic,
                $loc_string
            );
        }

        $system_prompt = self::get_mother_tone_system_prompt();
        $ai_result     = self::call_ai_api($system_prompt, $user_prompt, $provider);

        if ($ai_result['success'] && !empty($ai_result['text'])) {
            $raw_content = $ai_result['text'];
            
            // H1 veya Title'ı ayıkla
            $title = '';
            if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $raw_content, $m)) {
                $title = trim(strip_tags($m[1]));
                // H1'i gövdeden çıkar ki tema iki kez basmasın
                $raw_content = preg_replace('/<h1[^>]*>.*?<\/h1>/is', '', $raw_content, 1);
            }

            if (empty($title)) {
                $title = !empty($custom_topic) 
                    ? $custom_topic 
                    : (!empty($loc_string) ? $loc_string . ' Montessori Çocuk Kitaplığı Rehberi' : 'Montessori Çocuk Odası Rehberi');
            }

            $content = trim($raw_content);
        } else {
            // AI API yoksa veya başarısızsa akıllı fallback kullan
            $fb = self::generate_fallback_article($city ?: 'Kayseri', $district ?: 'Kocasinan');
            $title   = $fb['title'];
            $content = preg_replace('/<h1[^>]*>.*?<\/h1>/is', '', $fb['content'], 1);
        }

        // Kategori Oluştur veya Bul
        $cat_name = !empty($city) ? 'Montessori Yerel Rehberler' : 'Montessori Rehberleri';
        $cat_id = 0;
        $term = get_term_by('name', $cat_name, 'category');
        if ($term && !is_wp_error($term)) {
            $cat_id = $term->term_id;
        } else {
            $new_term = wp_insert_term($cat_name, 'category');
            if (!is_wp_error($new_term) && isset($new_term['term_id'])) {
                $cat_id = $new_term['term_id'];
            }
        }

        // WordPress Postunu Kaydet
        $post_data = [
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => $status,
            'post_type'     => 'post',
            'post_author'   => get_current_user_id(),
            'post_category' => $cat_id ? [$cat_id] : [],
        ];

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Yazı kaydedilemedi: ' . $post_id->get_error_message()]);
        }

        // Meta Verilerini Kaydet
        if (!empty($city)) {
            update_post_meta($post_id, '_mis360_ai_city', $city);
        }
        if (!empty($district)) {
            update_post_meta($post_id, '_mis360_ai_district', $district);
        }
        update_post_meta($post_id, '_mis360_is_ai_generated', '1');

        // Otomatik Görsel Atama (Eğer yazının görseli yoksa varsa ürünlerden bir görsel ata)
        if (!has_post_thumbnail($post_id) && class_exists('WooCommerce')) {
            $sample_prod = get_posts([
                'post_type'      => 'product',
                'posts_per_page' => 1,
                'meta_key'       => '_thumbnail_id',
                'orderby'        => 'rand',
            ]);
            if (!empty($sample_prod) && has_post_thumbnail($sample_prod[0]->ID)) {
                set_post_thumbnail($post_id, get_post_thumbnail_id($sample_prod[0]->ID));
            }
        }

        $edit_url = get_edit_post_link($post_id, 'raw');
        $view_url = get_permalink($post_id);

        wp_send_json_success([
            'post_id'  => $post_id,
            'title'    => $title,
            'status'   => $status,
            'city'     => $city,
            'district' => $district,
            'edit_url' => $edit_url,
            'view_url' => $view_url,
            'message'  => sprintf('"%s" başlıklı makale başarıyla oluşturuldu.', esc_html($title))
        ]);
    }

    /**
     * AJAX: Canlı Model Listesi Getir
     */
    public function ajax_fetch_models() {
        check_ajax_referer('mis360_ai_blog_nonce', 'security');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Yetkiniz bulunmuyor.']);
        }

        $provider = sanitize_key($_POST['provider'] ?? 'gemini');
        $key      = trim($_POST['key'] ?? '');

        if (empty($key)) {
            $key = get_option('mis360_' . $provider . '_api_key', '');
        }

        if (empty($key)) {
            wp_send_json_error(['message' => 'Lütfen önce geçerli bir API anahtarı girin.']);
        }

        $res = self::fetch_available_models($provider, $key);
        if ($res['success'] && !empty($res['models'])) {
            update_option('mis360_' . $provider . '_api_key', $key);
            update_option('mis360_' . $provider . '_model', $res['models'][0]);

            wp_send_json_success([
                'models'  => $res['models'],
                'current' => $res['models'][0],
                'message' => sprintf('✅ %d adet aktif uyumlu model bulundu! İlk çalışan model (%s) seçildi.', count($res['models']), $res['models'][0])
            ]);
        } else {
            wp_send_json_error([
                'message' => $res['message'] ?? 'Modeller listelenemedi. Lütfen API anahtarınızı kontrol edin.'
            ]);
        }
    }

    /**
     * AJAX: API Bağlantı Testi (Otomatik Model Eşleştirme & Canlı Doğrulama)
     */
    public function ajax_test_api_connection() {
        check_ajax_referer('mis360_ai_blog_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Yetkiniz bulunmuyor.']);
        }

        $provider = sanitize_key($_POST['provider'] ?? 'gemini');
        $key      = trim($_POST['key'] ?? '');
        $model    = sanitize_text_field($_POST['model'] ?? '');

        if (!empty($key)) {
            update_option('mis360_' . $provider . '_api_key', $key);
        } else {
            $key = get_option('mis360_' . $provider . '_api_key');
        }
        if (!empty($model)) {
            update_option('mis360_' . $provider . '_model', $model);
        }

        if (empty($key)) {
            wp_send_json_error(['message' => '❌ API anahtarı boş. Lütfen ' . strtoupper($provider) . ' API anahtarınızı girin.']);
        }

        // Önce canlı modelleri sorgula ve en uyumlu olanı garantile
        $models_check = self::fetch_available_models($provider, $key);
        if ($models_check['success'] && !empty($models_check['models'])) {
            if (empty($model) || !in_array($model, $models_check['models'], true)) {
                $model = $models_check['models'][0];
                update_option('mis360_' . $provider . '_model', $model);
            }
        }

        // Test isteği gönder
        $res = self::call_ai_api('Sen bir çocuk odası mobilya uzmanı test asistanısın.', 'Kısaca 1 cümleyle selam ver.', $provider);

        if ($res['success']) {
            $used_model = $res['model'] ?? get_option('mis360_' . $provider . '_model');
            wp_send_json_success([
                'message' => sprintf(
                    '🎉 <strong>%s API Bağlantısı Başarılı!</strong><br><span style="color:#0284c7;">✓ Aktif Model:</span> <code>%s</code><br><span style="color:#16a34a;">✓ Model Yanıtı:</span> <em>"%s"</em>',
                    strtoupper($provider),
                    esc_html($used_model),
                    esc_html($res['text'])
                )
            ]);
        } else {
            $err_msg = $res['message'];
            if (!empty($models_check['models'])) {
                $err_msg .= '<br><small>Hesabınızdaki diğer modeller: ' . implode(', ', array_slice($models_check['models'], 0, 5)) . '</small>';
            }
            wp_send_json_error(['message' => '❌ ' . $err_msg]);
        }
    }

    /**
     * AJAX: API Ayarlarını Kaydet
     */
    public function ajax_save_api_settings() {
        check_ajax_referer('mis360_ai_blog_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Yetkiniz bulunmuyor.']);
        }

        update_option('mis360_ai_provider', sanitize_key($_POST['mis360_ai_provider'] ?? 'gemini'));
        update_option('mis360_gemini_api_key', trim($_POST['mis360_gemini_api_key'] ?? ''));
        update_option('mis360_gemini_model', sanitize_text_field($_POST['mis360_gemini_model'] ?? 'gemini-2.5-flash'));
        update_option('mis360_openai_api_key', trim($_POST['mis360_openai_api_key'] ?? ''));
        update_option('mis360_openai_model', sanitize_text_field($_POST['mis360_openai_model'] ?? 'gpt-4o-mini'));
        update_option('mis360_groq_api_key', trim($_POST['mis360_groq_api_key'] ?? ''));
        update_option('mis360_groq_model', sanitize_text_field($_POST['mis360_groq_model'] ?? 'llama-3.1-8b-instant'));
        update_option('mis360_nvidia_api_key', trim($_POST['mis360_nvidia_api_key'] ?? ''));
        update_option('mis360_nvidia_model', sanitize_text_field($_POST['mis360_nvidia_model'] ?? 'nvidia/llama-3.1-nemotron-70b-instruct'));

        wp_send_json_success(['message' => 'Yapay Zeka API ayarları başarıyla kaydedildi.']);
    }

    /**
     * Admin Panel Arayüzü
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Erişim yetkiniz bulunmuyor.');
        }

        $locations    = mis360_get_turkey_locations();
        $provider     = get_option('mis360_ai_provider', 'gemini');
        $gemini_key   = get_option('mis360_gemini_api_key', '');
        $openai_key   = get_option('mis360_openai_api_key', '');
        $groq_key     = get_option('mis360_groq_api_key', '');
        $nvidia_key   = get_option('mis360_nvidia_api_key', '');

        // Üretilen son 25 yazıyı çek
        $ai_posts = get_posts([
            'post_type'      => 'post',
            'posts_per_page' => 25,
            'meta_key'       => '_mis360_is_ai_generated',
            'meta_value'     => '1',
            'post_status'    => ['publish', 'draft'],
        ]);
        ?>
        <div class="wrap mis360-ai-blog-wrap">
            <style>
                .mis360-ai-blog-wrap {
                    max-width: 1200px;
                    margin: 20px 20px 40px 0;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                }
                .mis360-hero-card {
                    background: linear-gradient(135deg, #fdfbf7 0%, #fff7ed 100%);
                    border: 1px solid #fed7aa;
                    border-radius: 16px;
                    padding: 24px 28px;
                    margin-bottom: 22px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 15px;
                }
                .mis360-hero-card h1 {
                    margin: 0 !important;
                    font-size: 24px !important;
                    font-weight: 800 !important;
                    color: #0f172a !important;
                }
                .mis360-tab-nav {
                    display: flex;
                    gap: 4px;
                    border-bottom: 2px solid #e2e8f0;
                    margin-bottom: 24px;
                    flex-wrap: wrap;
                }
                .mis360-tab-btn {
                    background: none;
                    border: none;
                    padding: 11px 20px;
                    font-size: 14px;
                    font-weight: 700;
                    color: #64748b;
                    cursor: pointer;
                    border-bottom: 2px solid transparent;
                    margin-bottom: -2px;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s ease;
                }
                .mis360-tab-btn:hover { color: #0f172a; }
                .mis360-tab-btn.active {
                    color: #ea580c;
                    border-bottom-color: #ea580c;
                }
                .mis360-panel { display: none; }
                .mis360-panel.active { display: block; }
                
                .mis360-card {
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 22px 26px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
                    margin-bottom: 22px;
                }
                .mis360-card-title {
                    margin: 0 0 16px 0;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 16px;
                    font-weight: 700;
                    color: #1e293b;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                .mis360-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                }
                @media (max-width: 782px) {
                    .mis360-grid { grid-template-columns: 1fr; }
                }
                .mis360-field { margin-bottom: 16px; }
                .mis360-field label {
                    display: block;
                    font-weight: 600;
                    font-size: 13px;
                    color: #334155;
                    margin-bottom: 6px;
                }
                .mis360-input, .mis360-select {
                    width: 100% !important;
                    max-width: 100% !important;
                    padding: 9px 12px !important;
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 8px !important;
                    font-size: 13px !important;
                    box-sizing: border-box !important;
                }
                .mis360-btn-primary {
                    background: #ea580c !important;
                    border-color: #ea580c !important;
                    color: #ffffff !important;
                    font-weight: 700 !important;
                    padding: 9px 22px !important;
                    height: auto !important;
                    border-radius: 8px !important;
                    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25) !important;
                    cursor: pointer;
                }
                .mis360-btn-primary:hover { background: #c2410c !important; }

                /* Progress Log Box */
                .mis360-log-box {
                    background: #0f172a;
                    color: #38bdf8;
                    font-family: monospace;
                    font-size: 12px;
                    padding: 15px;
                    border-radius: 8px;
                    max-height: 250px;
                    overflow-y: auto;
                    display: none;
                    margin-top: 15px;
                    line-height: 1.6;
                }
                .mis360-progress-bar-wrap {
                    background: #e2e8f0;
                    border-radius: 20px;
                    height: 14px;
                    overflow: hidden;
                    margin-top: 15px;
                    display: none;
                }
                .mis360-progress-bar {
                    background: linear-gradient(90deg, #ea580c 0%, #10b981 100%);
                    width: 0%;
                    height: 100%;
                    transition: width 0.3s ease;
                }
            </style>

            <!-- Üst Başlık Kartı -->
            <div class="mis360-hero-card">
                <div>
                    <h1>📝 Emdief Home | AI Blog &amp; 81 İl/İlçe Rehber Motoru</h1>
                    <p style="margin:6px 0 0; color:#64748b; font-size:13.5px;">
                        Anne dilinde samimi, gerçekçi ve Google/AI arama motorlarında 1. sıraya oynayan yerel SEO makale fabrikası.
                    </p>
                </div>
                <div>
                    <span style="background:#ea580c; color:#fff; font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px;">
                        Aktif AI: <?php echo esc_html(strtoupper($provider)); ?>
                    </span>
                </div>
            </div>

            <!-- Sekme Başlıkları -->
            <div class="mis360-tab-nav">
                <button type="button" class="mis360-tab-btn active" data-tab="locations">
                    📍 81 İl &amp; İlçe Yerel SEO Motoru
                </button>
                <button type="button" class="mis360-tab-btn" data-tab="single">
                    ✍️ Tekil Konu Makale Üreticisi
                </button>
                <button type="button" class="mis360-tab-btn" data-tab="api_settings">
                    🤖 Yapay Zeka API Ayarları (Gemini, Nvidia, Groq, OpenAI)
                </button>
                <button type="button" class="mis360-tab-btn" data-tab="history">
                    📋 Üretilen Makaleler (<?php echo count($ai_posts); ?>)
                </button>
            </div>

            <!-- ================= SEKME 1: 81 İL & İLÇE YEREL SEO ================= -->
            <div class="mis360-panel active" id="panel-locations">
                <div class="mis360-grid">
                    <div class="mis360-card">
                        <h2 class="mis360-card-title">
                            <span>🎯 İl &amp; İlçe Seçimi ve Toplu Üretim</span>
                        </h2>

                        <div class="mis360-field">
                            <label>Hedef İl:</label>
                            <select id="loc-city" class="mis360-select">
                                <option value="">-- Bir İl Seçin --</option>
                                <?php foreach (array_keys($locations) as $c): ?>
                                    <option value="<?php echo esc_attr($c); ?>" <?php selected($c, 'Kayseri'); ?>><?php echo esc_html($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mis360-field">
                            <label>Hedef İlçe:</label>
                            <select id="loc-district" class="mis360-select">
                                <option value="">-- Tüm İlçeler İçin Otomatik Üret --</option>
                            </select>
                            <span style="font-size:11.5px;color:#64748b;">"Tüm İlçeler" seçildiğinde seçilen ilin tüm ilçeleri için sırayla makale üretilir.</span>
                        </div>

                        <div class="mis360-field">
                            <label>Makale Başlık &amp; Odak Şablonu:</label>
                            <select id="loc-topic-tpl" class="mis360-select">
                                <option value="[ilce] [il] Montessori Çocuk Kitaplığı & Ahşap Mobilya Üreticisi">[İlçe] [İl] Montessori Çocuk Kitaplığı & Ahşap Mobilya Üreticisi (Önerilen)</option>
                                <option value="[ilce] Montessori Kitaplık Modelleri ve İmalatçı Fiyatları">[İlçe] Montessori Kitaplık Modelleri ve İmalatçı Fiyatları</option>
                                <option value="[il] [ilce] Çocuk Odası Düzenleme ve Ahşap Montessori Rehberi">[İl] [İlçe] Çocuk Odası Düzenleme ve Ahşap Montessori Rehberi</option>
                                <option value="custom">Özel Başlık Şablonu Belirle...</option>
                            </select>
                        </div>

                        <div class="mis360-field" id="loc-custom-topic-wrap" style="display:none;">
                            <label>Özel Başlık ( [il] ve [ilce] etiketleri otomatik değiştirilir ):</label>
                            <input type="text" id="loc-custom-topic" class="mis360-input" placeholder="Örn: [ilce] En Çok Tercih Edilen Ahşap Montessori Kitaplıkları" />
                        </div>

                        <div class="mis360-grid" style="margin-bottom:16px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Yayın Durumu:</label>
                                <select id="loc-status" class="mis360-select">
                                    <option value="publish">✅ Doğrudan Yayınla (Publish)</option>
                                    <option value="draft">📝 Taslak Olarak Kaydet (Draft)</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Kullanılacak AI:</label>
                                <select id="loc-provider" class="mis360-select">
                                    <option value="gemini" <?php selected($provider, 'gemini'); ?>>Google Gemini</option>
                                    <option value="groq" <?php selected($provider, 'groq'); ?>>Groq (Ultra Hızlı)</option>
                                    <option value="nvidia" <?php selected($provider, 'nvidia'); ?>>Nvidia AI</option>
                                    <option value="openai" <?php selected($provider, 'openai'); ?>>OpenAI (ChatGPT)</option>
                                </select>
                            </div>
                        </div>

                        <button type="button" id="btn-start-batch" class="button mis360-btn-primary" style="width:100%;">
                            🚀 Seçilen İl / İlçeler İçin Üretimi Başlat
                        </button>

                        <!-- İlerleme Çubuğu ve Log -->
                        <div class="mis360-progress-bar-wrap" id="loc-progress-wrap">
                            <div class="mis360-progress-bar" id="loc-progress-bar"></div>
                        </div>
                        <div class="mis360-log-box" id="loc-log-box"></div>
                    </div>

                    <div class="mis360-card">
                        <h2 class="mis360-card-title">
                            <span>💡 Anne Dili &amp; Yerel SEO Nasıl Çalışır?</span>
                        </h2>
                        <ul style="font-size:13px; color:#475569; line-height:1.7; margin:0; padding-left:18px;">
                            <li><strong>Doğal &amp; Samimi Dil:</strong> Robotik kelimeler elenir. Bir annenin diğer annelere içten tavsiyeleri gibi yazılır.</li>
                            <li><strong>İl ve İlçe Özelleştirmesi:</strong> Seçilen il ve ilçeye özel (örneğin <em>İstanbul Kadıköy</em> veya <em>Kayseri Melikgazi</em>) yerel dokunuşlar eklenir.</li>
                            <li><strong>Emdief Home İmalat Güvencesi:</strong> E1 normlarında MDF, sivri köşesiz yuvarlatılmış hatlar, 5 dakikada kolay montaj ve 1.500 TL üzeri ücretsiz kargo vurgulanır.</li>
                            <li><strong>Otomatik CTA Kartı:</strong> Üretilen tüm blog yazılarına otomatik olarak WhatsApp danışma ve kategori alışveriş linki eklenir.</li>
                            <li><strong>Güvenli Kuyruk (Batch):</strong> Sunucuyu kilitlememek için ilçeler tek tek arka planda AJAX ile sırayla üretilir.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ================= SEKME 2: TEKİL MAKALE ÜRETİCİSİ ================= -->
            <div class="mis360-panel" id="panel-single">
                <div class="mis360-card">
                    <h2 class="mis360-card-title">
                        <span>✍️ Özel Konulu Tekil Makale Üreticisi</span>
                    </h2>
                    <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
                        Belirlediğiniz herhangi bir konuda (Örn: <em>"2-6 Yaş Çocuklarda Kitap Okuma Alışkanlığı Kazandırma Yolları"</em>) anında anne dilinde SEO uyumlu makale hazırlayın.
                    </p>

                    <div class="mis360-field">
                        <label>Makale Konusu veya Başlığı:</label>
                        <input type="text" id="single-topic" class="mis360-input" placeholder="Örn: Bebek ve Çocuk Odalarında Sivri Köşe Tehlikesi ve Montessori Çözümleri" />
                    </div>

                    <div class="mis360-grid">
                        <div class="mis360-field">
                            <label>İl / İlçe (İsteğe Bağlı):</label>
                            <input type="text" id="single-loc" class="mis360-input" placeholder="Örn: Kayseri veya Ankara" />
                        </div>
                        <div class="mis360-field">
                            <label>Yayın Durumu:</label>
                            <select id="single-status" class="mis360-select">
                                <option value="publish">✅ Doğrudan Yayınla</option>
                                <option value="draft">📝 Taslak Olarak Kaydet</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" id="btn-generate-single" class="button mis360-btn-primary">
                        ✨ Makaleyi Anne Dilinde Hemen Üret
                    </button>

                    <div class="mis360-log-box" id="single-log-box"></div>
                </div>
            </div>

            <!-- ================= SEKME 3: API AYARLARI ================= -->
            <div class="mis360-panel" id="panel-api_settings">
                <div class="mis360-card">
                    <h2 class="mis360-card-title">
                        <span>🤖 Yapay Zeka Sağlayıcıları ve API Anahtarları</span>
                    </h2>
                    <p style="font-size:13px;color:#64748b;margin-bottom:20px;">
                        Kullanmak istediğiniz servisin API anahtarını girin. Dilediğiniz zaman sağlayıcılar arasında geçiş yapabilirsiniz. Sistem otomatik model geçişi (fallback) ile kesintisiz çalışır.
                    </p>

                    <?php
                    $gemini_model = get_option('mis360_gemini_model', 'gemini-2.5-flash');
                    $openai_model = get_option('mis360_openai_model', 'gpt-4o-mini');
                    $groq_model   = get_option('mis360_groq_model', 'llama-3.1-8b-instant');
                    $nvidia_model = get_option('mis360_nvidia_model', 'nvidia/llama-3.1-nemotron-70b-instruct');
                    ?>

                    <form id="form-api-settings">
                        <div class="mis360-field">
                            <label>Varsayılan Aktif AI Sağlayıcı:</label>
                            <select name="mis360_ai_provider" class="mis360-select" style="font-weight:700;">
                                <option value="gemini" <?php selected($provider, 'gemini'); ?>>🌟 Google Gemini (Ücretsiz Kota & Hızlı - Önerilen)</option>
                                <option value="groq" <?php selected($provider, 'groq'); ?>>⚡ Groq Cloud (Ultra Hızlı Llama 3.1 & Ücretsiz)</option>
                                <option value="nvidia" <?php selected($provider, 'nvidia'); ?>>🟢 Nvidia AI NIM (Nemotron 70B & Yüksek Zeka)</option>
                                <option value="openai" <?php selected($provider, 'openai'); ?>>🧠 OpenAI (ChatGPT GPT-4o Mini)</option>
                            </select>
                        </div>

                        <hr style="border:none;border-top:1px solid #f1f5f9;margin:20px 0;">

                        <!-- Google Gemini -->
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <label style="font-weight:700; color:#0f172a; margin:0;">🌟 Google Gemini API</label>
                                <div><button type="button" class="button btn-fetch-models" data-provider="gemini" style="font-size:11.5px;padding:2px 8px;margin-right:8px;font-weight:600;">🔄 Modelleri Canlı Getir</button><a href="https://aistudio.google.com/app/apikey" target="_blank" style="font-size:12px; color:#2563eb; text-decoration:none;">Ücretsiz Key Al ↗</a></div>
                            </div>
                            <div class="mis360-field" style="margin-bottom:10px;">
                                <input type="password" name="mis360_gemini_api_key" value="<?php echo esc_attr($gemini_key); ?>" class="mis360-input" placeholder="AIzaSy... (Gemini API Anahtarı)" />
                            </div>
                            <div class="mis360-field" style="margin:0;">
                                <label style="font-size:12px; color:#64748b;">Gemini Modeli:</label>
                                <select name="mis360_gemini_model" class="mis360-select">
                                    <option value="gemini-2.5-flash" <?php selected($gemini_model, 'gemini-2.5-flash'); ?>>gemini-2.5-flash (En Yeni &amp; Hızlı - Önerilen)</option>
                                    <option value="gemini-2.0-flash" <?php selected($gemini_model, 'gemini-2.0-flash'); ?>>gemini-2.0-flash (Kararlı)</option>
                                    <option value="gemini-2.5-flash-lite" <?php selected($gemini_model, 'gemini-2.5-flash-lite'); ?>>gemini-2.5-flash-lite (Hafif)</option>
                                    <option value="gemini-1.5-flash-latest" <?php selected($gemini_model, 'gemini-1.5-flash-latest'); ?>>gemini-1.5-flash-latest</option>
                                </select>
                            </div>
                        </div>

                        <!-- Groq -->
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <label style="font-weight:700; color:#0f172a; margin:0;">⚡ Groq Cloud API</label>
                                <div><button type="button" class="button btn-fetch-models" data-provider="groq" style="font-size:11.5px;padding:2px 8px;margin-right:8px;font-weight:600;">🔄 Modelleri Canlı Getir</button><a href="https://console.groq.com/keys" target="_blank" style="font-size:12px; color:#2563eb; text-decoration:none;">Ücretsiz Key Al ↗</a></div>
                            </div>
                            <div class="mis360-field" style="margin-bottom:10px;">
                                <input type="password" name="mis360_groq_api_key" value="<?php echo esc_attr($groq_key); ?>" class="mis360-input" placeholder="gsk_... (Groq API Anahtarı)" />
                            </div>
                            <div class="mis360-field" style="margin:0;">
                                <label style="font-size:12px; color:#64748b;">Groq Modeli:</label>
                                <select name="mis360_groq_model" class="mis360-select">
                                    <option value="llama-3.1-8b-instant" <?php selected($groq_model, 'llama-3.1-8b-instant'); ?>>llama-3.1-8b-instant (Ultra Hızlı &amp; Limitsiz Kota - Önerilen)</option>
                                    <option value="llama-3.3-70b-specdec" <?php selected($groq_model, 'llama-3.3-70b-specdec'); ?>>llama-3.3-70b-specdec (Yeni Hızlı 70B)</option>
                                    <option value="llama3-70b-8192" <?php selected($groq_model, 'llama3-70b-8192'); ?>>llama3-70b-8192 (Llama 3 70B)</option>
                                    <option value="llama3-8b-8192" <?php selected($groq_model, 'llama3-8b-8192'); ?>>llama3-8b-8192 (Llama 3 8B)</option>
                                    <option value="mixtral-8x7b-32768" <?php selected($groq_model, 'mixtral-8x7b-32768'); ?>>mixtral-8x7b-32768</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nvidia AI -->
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <label style="font-weight:700; color:#0f172a; margin:0;">🟢 Nvidia AI NIM API</label>
                                <div><button type="button" class="button btn-fetch-models" data-provider="nvidia" style="font-size:11.5px;padding:2px 8px;margin-right:8px;font-weight:600;">🔄 Modelleri Canlı Getir</button><a href="https://build.nvidia.com/" target="_blank" style="font-size:12px; color:#2563eb; text-decoration:none;">NIM Key Al ↗</a></div>
                            </div>
                            <div class="mis360-field" style="margin-bottom:10px;">
                                <input type="password" name="mis360_nvidia_api_key" value="<?php echo esc_attr($nvidia_key); ?>" class="mis360-input" placeholder="nvapi-... (Nvidia API Anahtarı)" />
                            </div>
                            <div class="mis360-field" style="margin:0;">
                                <label style="font-size:12px; color:#64748b;">Nvidia NIM Modeli:</label>
                                <select name="mis360_nvidia_model" class="mis360-select">
                                    <option value="nvidia/llama-3.1-nemotron-70b-instruct" <?php selected($nvidia_model, 'nvidia/llama-3.1-nemotron-70b-instruct'); ?>>nvidia/llama-3.1-nemotron-70b-instruct (Nvidia 70B - Önerilen)</option>
                                    <option value="meta/llama-3.2-11b-vision-instruct" <?php selected($nvidia_model, 'meta/llama-3.2-11b-vision-instruct'); ?>>meta/llama-3.2-11b-vision-instruct</option>
                                    <option value="mistralai/mistral-large-2-instruct" <?php selected($nvidia_model, 'mistralai/mistral-large-2-instruct'); ?>>mistralai/mistral-large-2-instruct</option>
                                    <option value="deepseek-ai/deepseek-v4.1-flash" <?php selected($nvidia_model, 'deepseek-ai/deepseek-v4.1-flash'); ?>>deepseek-ai/deepseek-v4.1-flash</option>
                                </select>
                            </div>
                        </div>

                        <!-- OpenAI -->
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <label style="font-weight:700; color:#0f172a; margin:0;">🧠 OpenAI (ChatGPT) API</label>
                                <div><button type="button" class="button btn-fetch-models" data-provider="openai" style="font-size:11.5px;padding:2px 8px;margin-right:8px;font-weight:600;">🔄 Modelleri Canlı Getir</button><a href="https://platform.openai.com/api-keys" target="_blank" style="font-size:12px; color:#2563eb; text-decoration:none;">OpenAI Platform ↗</a></div>
                            </div>
                            <div class="mis360-field" style="margin-bottom:10px;">
                                <input type="password" name="mis360_openai_api_key" value="<?php echo esc_attr($openai_key); ?>" class="mis360-input" placeholder="sk-proj-... (OpenAI API Anahtarı)" />
                            </div>
                            <div class="mis360-field" style="margin:0;">
                                <label style="font-size:12px; color:#64748b;">OpenAI Modeli:</label>
                                <select name="mis360_openai_model" class="mis360-select">
                                    <option value="gpt-4o-mini" <?php selected($openai_model, 'gpt-4o-mini'); ?>>gpt-4o-mini (Hızlı &amp; Düşük Maliyet - Önerilen)</option>
                                    <option value="gpt-4o" <?php selected($openai_model, 'gpt-4o'); ?>>gpt-4o (En Yüksek Kalite)</option>
                                    <option value="gpt-3.5-turbo" <?php selected($openai_model, 'gpt-3.5-turbo'); ?>>gpt-3.5-turbo</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:flex;gap:12px;margin-top:20px;">
                            <button type="submit" class="button mis360-btn-primary">
                                API Ayarlarını Kaydet
                            </button>
                            <button type="button" id="btn-test-api" class="button" style="padding:8px 18px; font-weight:700;">
                                🔌 Bağlantıyı Test Et
                            </button>
                        </div>
                    </form>
                    <div id="api-test-msg" style="margin-top:14px;font-size:13.5px;font-weight:600;line-height:1.5;"></div>
                </div>
            </div>

            <!-- ================= SEKME 4: ÜRETİLEN MAKALELER ================= -->
            <div class="mis360-panel" id="panel-history">
                <div class="mis360-card">
                    <h2 class="mis360-card-title">
                        <span>📋 Son Üretilen Rehber &amp; Makaleler</span>
                    </h2>
                    <?php if (empty($ai_posts)) : ?>
                        <p style="color:#64748b;font-size:13.5px;text-align:center;padding:30px 0;">Henüz AI motoru ile üretilmiş bir makale bulunmuyor.</p>
                    <?php else : ?>
                        <table class="widefat fixed striped" style="border:none;">
                            <thead>
                                <tr>
                                    <th>Başlık</th>
                                    <th style="width:180px;">Bölge / İl</th>
                                    <th style="width:110px;">Durum</th>
                                    <th style="width:130px;">Tarih</th>
                                    <th style="width:130px;text-align:right;">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ai_posts as $post) : 
                                    $p_city = get_post_meta($post->ID, '_mis360_ai_city', true);
                                    $p_dist = get_post_meta($post->ID, '_mis360_ai_district', true);
                                    $loc_label = trim(($p_dist ? $p_dist . ' / ' : '') . $p_city) ?: 'Genel';
                                ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($post->post_title); ?></strong></td>
                                        <td>📍 <?php echo esc_html($loc_label); ?></td>
                                        <td>
                                            <?php if ($post->post_status === 'publish') : ?>
                                                <span style="color:#16a34a;font-weight:700;">Yayında</span>
                                            <?php else : ?>
                                                <span style="color:#ea580c;font-weight:700;">Taslak</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo get_the_date('d.m.Y H:i', $post->ID); ?></td>
                                        <td style="text-align:right;">
                                            <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>" class="button button-small">Düzenle</a>
                                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" target="_blank" class="button button-small">Gör</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var turkeyLocations = <?php echo json_encode($locations); ?>;
                var ajaxUrl = <?php echo json_encode(admin_url('admin-ajax.php')); ?>;
                var nonce   = <?php echo json_encode(wp_create_nonce('mis360_ai_blog_nonce')); ?>;

                // Sekme Geçişleri
                $('.mis360-tab-btn').on('click', function() {
                    var tab = $(this).data('tab');
                    $('.mis360-tab-btn').removeClass('active');
                    $(this).addClass('active');

                    $('.mis360-panel').removeClass('active');
                    $('#panel-' + tab).addClass('active');
                });

                // İl Değiştiğinde İlçeleri Doldur
                $('#loc-city').on('change', function() {
                    var city = $(this).val();
                    var $district = $('#loc-district');
                    $district.empty();
                    $district.append('<option value="">-- Tüm İlçeler İçin Otomatik Üret --</option>');

                    if (city && turkeyLocations[city]) {
                        $.each(turkeyLocations[city], function(i, d) {
                            $district.append('<option value="' + d + '">' + d + '</option>');
                        });
                    }
                }).trigger('change');

                // Şablon Değişimi
                $('#loc-topic-tpl').on('change', function() {
                    if ($(this).val() === 'custom') {
                        $('#loc-custom-topic-wrap').show();
                    } else {
                        $('#loc-custom-topic-wrap').hide();
                    }
                });

                // 81 İl/İlçe Toplu Üretim Motoru (Batch AJAX)
                $('#btn-start-batch').on('click', function() {
                    var city = $('#loc-city').val();
                    if (!city) {
                        alert('Lütfen bir il seçiniz.');
                        return;
                    }

                    var selectedDistrict = $('#loc-district').val();
                    var districts = [];
                    if (selectedDistrict) {
                        districts = [selectedDistrict];
                    } else if (turkeyLocations[city]) {
                        districts = turkeyLocations[city];
                    } else {
                        districts = ['Merkez'];
                    }

                    var tpl = $('#loc-topic-tpl').val();
                    var customTopic = $('#loc-custom-topic').val();
                    var status = $('#loc-status').val();
                    var provider = $('#loc-provider').val();

                    if (!confirm(city + ' ilinde ' + districts.length + ' adet ilçe için makale üretimi başlayacak. Onaylıyor musunuz?')) {
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('⏳ Üretim Devam Ediyor...');
                    
                    var $logBox = $('#loc-log-box').show().empty();
                    var $progWrap = $('#loc-progress-wrap').show();
                    var $progBar = $('#loc-progress-bar').css('width', '0%');

                    var total = districts.length;
                    var current = 0;

                    function processNextDistrict() {
                        if (current >= total) {
                            $btn.prop('disabled', false).text('🚀 Seçilen İl / İlçeler İçin Üretimi Başlat');
                            $logBox.append('<div style="color:#4ade80;font-weight:bold;margin-top:8px;">🎉 Tüm ' + total + ' ilçe için makaleler başarıyla tamamlandı!</div>');
                            return;
                        }

                        var dist = districts[current];
                        var topicStr = '';
                        if (tpl === 'custom' && customTopic) {
                            topicStr = customTopic.replace(/\[il\]/gi, city).replace(/\[ilce\]/gi, dist);
                        } else {
                            topicStr = tpl.replace(/\[il\]/gi, city).replace(/\[ilce\]/gi, dist);
                        }

                        $logBox.append('<div>[' + (current + 1) + '/' + total + '] ' + city + ' / ' + dist + ' makalesi üretiliyor...</div>');
                        $logBox.scrollTop($logBox[0].scrollHeight);

                        $.ajax({
                            url: ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'mis360_ai_generate_single',
                                city: city,
                                district: dist,
                                custom_topic: topicStr,
                                status: status,
                                provider: provider,
                                security: nonce
                            },
                            success: function(res) {
                                current++;
                                var percent = Math.round((current / total) * 100);
                                $progBar.css('width', percent + '%');

                                if (res.success) {
                                    $logBox.append('<div style="color:#a7f3d0;">✓ ' + dist + ' tamamlandı: <a href="' + res.data.view_url + '" target="_blank" style="color:#38bdf8;">' + res.data.title + '</a></div>');
                                } else {
                                    $logBox.append('<div style="color:#f87171;">✗ ' + dist + ' hatası: ' + res.data.message + '</div>');
                                }
                                $logBox.scrollTop($logBox[0].scrollHeight);
                                setTimeout(processNextDistrict, 1000);
                            },
                            error: function() {
                                current++;
                                $logBox.append('<div style="color:#f87171;">✗ ' + dist + ' için sunucu zaman aşımı oluştu. Sonrakine geçiliyor...</div>');
                                setTimeout(processNextDistrict, 1000);
                            }
                        });
                    }

                    processNextDistrict();
                });

                // Tekil Makale Üretici
                $('#btn-generate-single').on('click', function() {
                    var topic = $('#single-topic').val();
                    var loc = $('#single-loc').val();
                    var status = $('#single-status').val();

                    if (!topic) {
                        alert('Lütfen bir makale konusu girin.');
                        return;
                    }

                    var $btn = $(this).prop('disabled', true).text('⏳ Anne Dilinde Yazılıyor...');
                    var $log = $('#single-log-box').show().html('Yapay zeka makaleyi hazırlıyor, lütfen bekleyin...');

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'mis360_ai_generate_single',
                            city: loc,
                            district: '',
                            custom_topic: topic,
                            status: status,
                            security: nonce
                        },
                        success: function(res) {
                            $btn.prop('disabled', false).text('✨ Makaleyi Anne Dilinde Hemen Üret');
                            if (res.success) {
                                $log.html('<div style="color:#a7f3d0;">🎉 Makale Hazır!<br><strong>Başlık:</strong> ' + res.data.title + '<br><a href="' + res.data.view_url + '" target="_blank" style="color:#38bdf8;text-decoration:underline;">Yazıyı Görüntüle ↗</a> | <a href="' + res.data.edit_url + '" target="_blank" style="color:#38bdf8;text-decoration:underline;">WordPress Editöründe Düzenle ↗</a></div>');
                            } else {
                                $log.html('<div style="color:#f87171;">Hata: ' + res.data.message + '</div>');
                            }
                        },
                        error: function() {
                            $btn.prop('disabled', false).text('✨ Makaleyi Anne Dilinde Hemen Üret');
                            $log.html('<div style="color:#f87171;">Sunucu bağlantı hatası oluştu.</div>');
                        }
                    });
                });

                // API Ayarlarını Kaydet
                $('#form-api-settings').on('submit', function(e) {
                    e.preventDefault();
                    var data = $(this).serialize() + '&action=mis360_ai_save_api_settings&security=' + nonce;
                    var $msg = $('#api-test-msg').text('Kaydediliyor...');

                    $.post(ajaxUrl, data, function(res) {
                        if (res.success) {
                            $msg.css('color', '#16a34a').text('✓ ' + res.data.message);
                        } else {
                            $msg.css('color', '#dc2626').text('Hata: ' + res.data.message);
                        }
                    });
                });

                // Aktif Modelleri Canlı Getir Butonları
                $(document).on('click', '.btn-fetch-models', function(e) {
                    e.preventDefault();
                    var provider = $(this).data('provider');
                    var $keyInput = $('input[name="mis360_' + provider + '_api_key"]');
                    var keyVal = $keyInput.val().trim();
                    var $select = $('select[name="mis360_' + provider + '_model"]');
                    var $msg = $('#api-test-msg');

                    if (!keyVal) {
                        alert('Lütfen önce ' + provider.toUpperCase() + ' API anahtarınızı girin.');
                        $keyInput.focus();
                        return;
                    }

                    var $btn = $(this).prop('disabled', true).text('⏳ Aranıyor...');
                    $msg.css('color', '#0284c7').html('🔍 ' + provider.toUpperCase() + ' hesabınızdaki aktif modeller taranıyor...');

                    $.post(ajaxUrl, {
                        action: 'mis360_ai_fetch_models',
                        provider: provider,
                        key: keyVal,
                        security: nonce
                    }, function(res) {
                        $btn.prop('disabled', false).text('🔄 Modelleri Canlı Getir');
                        if (res.success && res.data.models.length > 0) {
                            $select.empty();
                            $.each(res.data.models, function(i, m) {
                                var opt = $('<option></option>').attr('value', m).text(m);
                                if (m === res.data.current) opt.prop('selected', true);
                                $select.append(opt);
                            });
                            $msg.css('color', '#16a34a').html(res.data.message);
                        } else {
                            $msg.css('color', '#dc2626').html('❌ ' + (res.data.message || 'Modeller bulunamadı.'));
                        }
                    }).fail(function() {
                        $btn.prop('disabled', false).text('🔄 Modelleri Canlı Getir');
                        $msg.css('color', '#dc2626').html('❌ Sunucu zaman aşımı.');
                    });
                });

                // API Bağlantı Testi (Otomatik Model Algılama & Doğrulama)
                $('#btn-test-api').on('click', function() {
                    var provider = $('select[name="mis360_ai_provider"]').val();
                    var keyVal = $('input[name="mis360_' + provider + '_api_key"]').val().trim();
                    var modelVal = $('select[name="mis360_' + provider + '_model"]').val();
                    var $msg = $('#api-test-msg').css('color', '#0284c7').html('⏳ <strong>' + provider.toUpperCase() + '</strong> bağlantısı ve modelleri test ediliyor, lütfen bekleyin...');

                    var formData = $('#form-api-settings').serialize() + '&action=mis360_ai_save_api_settings&security=' + nonce;
                    $.post(ajaxUrl, formData, function() {
                        $.post(ajaxUrl, {
                            action: 'mis360_ai_test_api',
                            provider: provider,
                            key: keyVal,
                            model: modelVal,
                            security: nonce
                        }, function(res) {
                            if (res.success) {
                                $msg.css('color', '#16a34a').html(res.data.message);
                            } else {
                                $msg.css('color', '#dc2626').html(res.data.message);
                            }
                        }).fail(function() {
                            $msg.css('color', '#dc2626').html('❌ Sunucu yanıt vermedi veya zaman aşımına uğradı.');
                        });
                    });
                });
            });
        </script>
        <?php
    }
}

// Başlat
new Emdief_AI_Blog_Generator();
