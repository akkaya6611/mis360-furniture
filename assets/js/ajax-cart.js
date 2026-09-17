/**
 * Mis360-Mobilya AJAX Mini-Cart & Drawer Engine
 * Version: 1.9.19
 */

document.addEventListener('DOMContentLoaded', () => {
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
    }

    function closeCartDrawer() {
        if (!cartDrawer) return;
        cartDrawer.classList.remove('is-active');
        cartDrawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    // Global erişim
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

    window.addEventListener('pageshow', closeCartDrawer);
    window.addEventListener('popstate', closeCartDrawer);
    window.addEventListener('beforeunload', closeCartDrawer);

    // =========================================================================
    // KESİN VE ANLIK ÇALIŞAN AJAX SEPETE EKLE & SİLME MOTORU
    // =========================================================================
    if (window.jQuery) {
        const $ = window.jQuery;
        const ajaxUrl = (window.mis360Data && window.mis360Data.ajaxUrl) ? window.mis360Data.ajaxUrl : '/wp-admin/admin-ajax.php';
        const nonce   = (window.mis360Data && window.mis360Data.nonce) ? window.mis360Data.nonce : '';
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

        // Önbellekli Sayfalarda Sepet Durumunu Canlı Eşitleme
        function syncCartState() {
            // A. Önce SessionStorage'dan anında yükle (0 ms gecikme)
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

        // Sayfa açıldığında senkronize et
        syncCartState();
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) syncCartState();
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
            const isFlash = ($btn.data('flash_deal') == '1' || $btn.data('is_flash_deal') == '1' || $btn.attr('data-flash_deal') == '1') ? 1 : 0;
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
                    is_flash_deal: isFlash,
                    flash_deal: isFlash,
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

        // 2. Çekmece İçi AJAX Ürün Çıkarma
        $(document).on('click', '.remove-cart-item', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $item = $btn.closest('.emdief-drawer-item');

            let cartItemKey = $btn.data('cart_item_key');
            if (!cartItemKey) {
                const href = $btn.attr('href') || '';
                const match = href.match(/remove_item=([^&]+)/);
                if (match) {
                    cartItemKey = decodeURIComponent(match[1]);
                }
            }

            if (!cartItemKey) {
                if ($btn.attr('href')) {
                    window.location.href = $btn.attr('href');
                }
                return;
            }

            if ($item.length) {
                $item.css({
                    'opacity': '0.35',
                    'pointer-events': 'none',
                    'filter': 'grayscale(80%)',
                    'transition': 'all 0.25s ease'
                });
            }

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: {
                    action: 'mis360_remove_cart_item',
                    cart_item_key: cartItemKey,
                    nonce: nonce
                },
                success: function(response) {
                    if (response && response.success && response.data) {
                        applyFragments(response.data);

                        $(document.body).trigger('wc_fragments_refreshed');
                        $(document.body).trigger('removed_from_cart', [response.data.fragments, response.data.cart_hash, $btn]);
                    } else if ($btn.attr('href')) {
                        window.location.href = $btn.attr('href');
                        return;
                    }

                    const path = window.location.pathname.toLowerCase();
                    if (path.includes('/sepet') || path.includes('/cart') || path.includes('/odeme') || path.includes('/checkout')) {
                        window.location.reload();
                    }
                },
                error: function() {
                    if ($btn.attr('href')) {
                        window.location.href = $btn.attr('href');
                    } else {
                        window.location.reload();
                    }
                }
            });
        });

        // WooCommerce native olaylarında da fragmanları uygula
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            if (fragments) {
                applyFragments({ fragments: fragments });
            }
            openCartDrawer();
        });
    }
});
