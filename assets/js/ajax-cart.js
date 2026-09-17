/**
 * Mis360-Mobilya AJAX Mini-Cart & Drawer Engine
 * Version: 1.9.16
 */

document.addEventListener('DOMContentLoaded', () => {
    const cartDrawer = document.getElementById('emdief-cart-drawer');
    const cartTrigger = document.getElementById('emdief-cart-trigger');
    const cartClose = document.getElementById('emdief-cart-close');
    const cartOverlay = document.getElementById('emdief-cart-overlay');
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

    // Fonksiyonları global olarak sun
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

    // Otomatik Kapatma: Kategori, ürün veya başka bir sayfaya geçiş yapıldığında çekmeceyi kapat
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
    // KESİN VE GÜVENİLİR AJAX SEPETE EKLE & SİLME MOTORU
    // =========================================================================
    if (window.jQuery) {
        const $ = window.jQuery;
        const ajaxUrl = (window.mis360Data && window.mis360Data.ajaxUrl) ? window.mis360Data.ajaxUrl : '/wp-admin/admin-ajax.php';
        const nonce   = (window.mis360Data && window.mis360Data.nonce) ? window.mis360Data.nonce : '';
        const addingText = (window.mis360Data && window.mis360Data.addingText) ? window.mis360Data.addingText : 'Ekleniyor...';
        const addedText  = (window.mis360Data && window.mis360Data.addedToCartText) ? window.mis360Data.addedToCartText : '✓ Sepete Eklendi!';

        // Fragmanları DOM üzerinde güncelleme fonksiyonu
        function updateFragments(fragments) {
            if (!fragments) return;
            $.each(fragments, function(selector, html) {
                $(selector).replaceWith(html);
            });
        }

        // 1. Ürün Kartlarından (Slider / Kategori) Tıklamayla Sepete Ekleme
        $(document).on('click', '.trendyol-btn-add-cart, .emdief-btn-add-cart, .ajax_add_to_cart, .add_to_cart_button:not(.product_type_variable):not(.product_type_grouped)', function(e) {
            const $btn = $(this);
            let productId = $btn.data('product_id') || $btn.attr('data-product_id');

            // Eğer href içinde ?add-to-cart=123 varsa ayrıştır
            if (!productId) {
                const href = $btn.attr('href') || '';
                const match = href.match(/add-to-cart=([0-9]+)/);
                if (match) {
                    productId = match[1];
                }
            }

            if (!productId) {
                return; // Varyasyonlu veya harici ürünse normal linke izin ver
            }

            // Sayfanın sepet URL'sine yönlenmesini %100 durdur
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
                $span.text(addingText);
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
                    $btn.removeClass('loading');

                    if (response && response.success && response.data && response.data.fragments) {
                        updateFragments(response.data.fragments);

                        $btn.addClass('is-added');
                        if ($span.length) {
                            $span.text(addedText);
                        }

                        // Çekmeceyi anında aç
                        openCartDrawer();

                        $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash, $btn]);
                        $(document.body).trigger('wc_fragment_refresh');

                        setTimeout(() => {
                            $btn.removeClass('is-added').css('pointer-events', '');
                            $btn.html(originalHtml);
                        }, 2200);
                    } else {
                        $btn.css('pointer-events', '').html(originalHtml);
                        if (response && response.data && response.data.product_url) {
                            window.location.href = response.data.product_url;
                        }
                    }
                },
                error: function() {
                    $btn.removeClass('loading').css('pointer-events', '').html(originalHtml);
                }
            });
        });

        // 2. Tekil Ürün Sayfasındaki Formdan (veya Sticky Buy Bar) Sepete Ekleme
        $(document).on('submit', 'form.cart', function(e) {
            const $form = $(this);
            const $btn  = $form.find('.single_add_to_cart_button, button[type="submit"]');

            if ($form.closest('.product-type-external').length) {
                return;
            }

            e.preventDefault();

            if ($btn.hasClass('loading') || $btn.hasClass('is-added')) {
                return;
            }

            const originalHtml = $btn.html();
            $btn.addClass('loading').css('pointer-events', 'none');
            const $span = $btn.find('span').length ? $btn.find('span') : $btn;
            $span.text(addingText);

            let formData = $form.serializeArray();
            let dataObj = {
                action: 'mis360_ajax_add_to_cart',
                nonce: nonce
            };

            $.each(formData, function(i, field) {
                if (field.name === 'add-to-cart') {
                    dataObj['product_id'] = field.value;
                } else {
                    dataObj[field.name] = field.value;
                }
            });

            if (!dataObj['product_id']) {
                dataObj['product_id'] = $btn.val() || $btn.data('product_id');
            }

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: dataObj,
                success: function(response) {
                    $btn.removeClass('loading');

                    if (response && response.success && response.data && response.data.fragments) {
                        updateFragments(response.data.fragments);

                        $btn.addClass('is-added');
                        $span.text(addedText);

                        openCartDrawer();

                        $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash, $btn]);
                        $(document.body).trigger('wc_fragment_refresh');

                        setTimeout(() => {
                            $btn.removeClass('is-added').css('pointer-events', '');
                            $btn.html(originalHtml);
                        }, 2200);
                    } else {
                        $form.off('submit').submit();
                    }
                },
                error: function() {
                    $form.off('submit').submit();
                }
            });
        });

        // 3. Çekmece İçi AJAX Ürün Çıkarma
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
                    if (response && response.success && response.data && response.data.fragments) {
                        updateFragments(response.data.fragments);

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

        // Standart WooCommerce added_to_cart kancasında da çekmeceyi aç
        $(document.body).on('added_to_cart', () => {
            openCartDrawer();
        });
    }
});
