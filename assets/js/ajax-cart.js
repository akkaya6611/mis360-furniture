/**
 * Mis360-Mobilya AJAX Mini-Cart & Drawer Engine
 * Version: 1.9.20 - Fully Synced & Bulletproof Cart System
 */

(function() {
    // 0. Çerezden ve Hafızadan ANINDA (0 ms) Sepet Sayacını Eşitleyen Yardımcı
    function quickSyncBadge() {
        try {
            var count = null;
            var match = document.cookie.match(/woocommerce_items_in_cart=([0-9]+)/);
            if (match) {
                count = parseInt(match[1], 10);
            }
            if (count === null || isNaN(count)) {
                var cached = sessionStorage.getItem('emdief_cart_data');
                if (cached) {
                    var parsed = JSON.parse(cached);
                    if (parsed && parsed.count !== undefined) {
                        count = parseInt(parsed.count, 10);
                    }
                }
            }
            if (count !== null && !isNaN(count)) {
                var b1 = document.getElementById('emdief-cart-count');
                if (b1) b1.textContent = count;
                var b2 = document.getElementById('emdief-bottom-cart-count');
                if (b2) b2.textContent = count;
                var b3 = document.getElementById('emdief-drawer-count-badge');
                if (b3) b3.textContent = count + ' ürün';
            }
        } catch (e) {}
    }

    // Script yüklendiği an ilk senkronizasyon
    quickSyncBadge();

    function initMis360CartEngine() {
        const cartDrawer    = document.getElementById('emdief-cart-drawer');
        const cartTrigger   = document.getElementById('emdief-cart-trigger');
        const cartClose     = document.getElementById('emdief-cart-close');
        const cartOverlay   = document.getElementById('emdief-cart-overlay');
        const bottomNavCart = document.getElementById('bottomNavCartBtn');

        function openCartDrawer() {
            if (!cartDrawer) return;

            // Mobil menü açıksa kapat
            const mobileDrawer = document.getElementById('emdief-mobile-drawer');
            if (mobileDrawer && mobileDrawer.classList.contains('is-active')) {
                mobileDrawer.classList.remove('is-active');
                mobileDrawer.setAttribute('aria-hidden', 'true');
            }

            cartDrawer.classList.add('is-active');
            cartDrawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            // Eğer çerezde ürün varsa ama çekmece içi boş görünüyorsa canlı güncelle
            const drawerBody = document.getElementById('emdief-drawer-cart-content');
            const match = document.cookie.match(/woocommerce_items_in_cart=([0-9]+)/);
            const cookieCount = match ? parseInt(match[1], 10) : 0;
            if (cookieCount > 0 && drawerBody && (drawerBody.querySelector('.emdief-cart-empty') || !drawerBody.children.length)) {
                if (typeof window.mis360SyncCartState === 'function') {
                    window.mis360SyncCartState();
                }
            }
        }

        function closeCartDrawer() {
            if (!cartDrawer) return;
            cartDrawer.classList.remove('is-active');
            cartDrawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        // Global fonksiyonlar
        window.mis360OpenCartDrawer = openCartDrawer;
        window.mis360CloseCartDrawer = closeCartDrawer;

        if (cartTrigger) {
            cartTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                openCartDrawer();
            });
        }

        if (bottomNavCart) {
            bottomNavCart.addEventListener('click', (e) => {
                e.preventDefault();
                openCartDrawer();
            });
        }

        if (cartClose) {
            cartClose.addEventListener('click', (e) => {
                e.preventDefault();
                closeCartDrawer();
            });
        }

        if (cartOverlay) {
            cartOverlay.addEventListener('click', closeCartDrawer);
        }

        // ESC tuşu ile çekmeceyi kapat
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCartDrawer();
            }
        });

        // Sayfa geçişlerinde çekmeceyi kapat
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            if (link.classList.contains('remove-cart-item') ||
                link.closest('.remove-cart-item') ||
                link.id === 'emdief-cart-trigger' ||
                link.id === 'bottomNavCartBtn' ||
                link.classList.contains('add_to_cart_button') ||
                link.classList.contains('trendyol-btn-add-cart') ||
                link.classList.contains('emdief-btn-add-cart') ||
                link.getAttribute('role') === 'button') {
                return;
            }

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            closeCartDrawer();
        }, { passive: true });

        // =========================================================================
        // AJAX & JQUERY SEPET ENTEGRASYONU
        // =========================================================================
        if (window.jQuery) {
            const $ = window.jQuery;
            const ajaxUrl   = (window.mis360Data && window.mis360Data.ajaxUrl) ? window.mis360Data.ajaxUrl : '/wp-admin/admin-ajax.php';
            const nonce     = (window.mis360Data && window.mis360Data.nonce) ? window.mis360Data.nonce : '';
            const addedText = (window.mis360Data && window.mis360Data.addedToCartText) ? window.mis360Data.addedToCartText : '✓ Sepete Eklendi!';

            if (typeof wc_add_to_cart_params !== 'undefined') {
                wc_add_to_cart_params.cart_redirect_after_add = 'no';
            }

            // Tüm sepet fragmanlarını ve sayaçlarını DOM üzerinde anında güncelle
            function applyFragments(data) {
                if (!data) return;

                // 1. Fragmanları değiştir
                if (data.fragments) {
                    $.each(data.fragments, function(selector, html) {
                        const $target = $(selector);
                        if ($target.length) {
                            $target.replaceWith(html);
                        }
                    });
                }

                // 2. Ürün adedini doğrudan güncelle
                if (data.count !== undefined) {
                    $('#emdief-cart-count').text(data.count);
                    $('#emdief-bottom-cart-count').text(data.count);
                    $('#emdief-drawer-count-badge').text(data.count + ' ürün');
                }

                // 3. Ara toplamı doğrudan güncelle
                if (data.subtotal !== undefined) {
                    $('.emdief-cart-total').html(data.subtotal);
                }

                // 4. SessionStorage'a kaydet (Önbellekli anasayfada sıfır gecikmeyle göstermek için)
                try {
                    sessionStorage.setItem('emdief_cart_data', JSON.stringify(data));
                } catch(err) {}
            }

            // Canlı Sepet Eşitleme Motoru
            function syncCartState() {
                // A. Hızlı çerez & sessionStorage senkronizasyonu
                quickSyncBadge();
                try {
                    const cachedData = sessionStorage.getItem('emdief_cart_data');
                    if (cachedData) {
                        const parsed = JSON.parse(cachedData);
                        applyFragments(parsed);
                    }
                } catch (err) {}

                // B. Sunucudan taze sepet verisini çekip doğrula
                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: {
                        action: 'mis360_get_cart_fragments',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response && response.success && response.data) {
                            applyFragments(response.data);
                        }
                    }
                });
            }

            window.mis360SyncCartState = syncCartState;

            // Sayfa açıldığında senkronize et
            syncCartState();

            // Sayfa geri-ileri düğmesi (bfcache), sekme değişimi ve odaklanmada sepeti daima yenile
            window.addEventListener('pageshow', () => syncCartState());
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') syncCartState();
            });
            window.addEventListener('focus', () => syncCartState());
            window.addEventListener('storage', (e) => {
                if (e.key === 'emdief_cart_data') syncCartState();
            });

            // WooCommerce standart olaylarını dinle
            $(document.body).on('added_to_cart removed_from_cart wc_fragments_refreshed wc_fragments_loaded', function(e, fragments) {
                if (fragments) {
                    applyFragments({ fragments: fragments });
                }
                syncCartState();
            });

            // 1. Ürün Kartlarından (Slider / Kategori / Anasayfa) Sepete Ekleme
            $(document).on('click', '.trendyol-btn-add-cart, .emdief-btn-add-cart, .ajax_add_to_cart', function(e) {
                const $btn = $(this);

                if ($btn.hasClass('product_type_variable') || $btn.hasClass('product_type_grouped') || $btn.hasClass('product_type_external')) {
                    return;
                }

                let productId = $btn.data('product_id') || $btn.attr('data-product_id');
                if (!productId) {
                    const href = $btn.attr('href') || '';
                    const match = href.match(/add-to-cart=([0-9]+)/);
                    if (match) productId = match[1];
                }

                if (!productId) return;

                e.preventDefault();
                e.stopPropagation();

                if ($btn.hasClass('loading') || $btn.hasClass('is-added')) {
                    return;
                }

                const qty = $btn.data('quantity') || 1;
                const originalHtml = $btn.html();

                $btn.addClass('loading').css('pointer-events', 'none');
                const $span = $btn.find('span');
                if ($span.length) {
                    $span.text('Ekleniyor...');
                }

                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: {
                        action: 'mis360_ajax_add_to_cart',
                        product_id: productId,
                        quantity: qty,
                        nonce: nonce
                    },
                    success: function(response) {
                        $btn.removeClass('loading').css('pointer-events', '');

                        if (response && response.success && response.data) {
                            applyFragments(response.data);

                            $btn.addClass('is-added');
                            if ($span.length) {
                                $span.text(addedText);
                            }

                            // Sepet çekmecesini anında aç!
                            openCartDrawer();

                            $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash, $btn]);
                            $(document.body).trigger('wc_fragment_refresh');

                            setTimeout(() => {
                                $btn.removeClass('is-added');
                                $btn.html(originalHtml);
                            }, 2200);
                        } else if ($btn.attr('href')) {
                            window.location.href = $btn.attr('href');
                        }
                    },
                    error: function() {
                        $btn.removeClass('loading').css('pointer-events', '').html(originalHtml);
                        if ($btn.attr('href')) {
                            window.location.href = $btn.attr('href');
                        }
                    }
                });
            });

            // 2. Çekmece İçinden Ürün Silme (Trash Butonu)
            $(document).on('click', '.remove-cart-item, a.remove', function(e) {
                const $btn = $(this);
                const cartItemKey = $btn.data('cart_item_key') || $btn.attr('data-cart_item_key');

                if (!cartItemKey) return;

                e.preventDefault();
                e.stopPropagation();

                const $itemRow = $btn.closest('.emdief-cart-item, .cart-item-row');
                if ($itemRow.length) {
                    $itemRow.css({ opacity: '0.4', 'pointer-events': 'none' });
                }

                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: {
                        action: 'mis360_ajax_remove_cart_item',
                        cart_item_key: cartItemKey,
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response && response.success && response.data) {
                            applyFragments(response.data);
                            $(document.body).trigger('removed_from_cart', [response.data.fragments, response.data.cart_hash, $btn]);
                            $(document.body).trigger('wc_fragment_refresh');
                        } else if ($btn.attr('href')) {
                            window.location.href = $btn.attr('href');
                        }
                    },
                    error: function() {
                        if ($itemRow.length) {
                            $itemRow.css({ opacity: '', 'pointer-events': '' });
                        }
                        if ($btn.attr('href')) {
                            window.location.href = $btn.attr('href');
                        }
                    }
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMis360CartEngine);
    } else {
        initMis360CartEngine();
    }
})();
