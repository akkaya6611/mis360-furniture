/**
 * Mis360-Mobilya AJAX Mini-Cart & Drawer Engine
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

        // İstisnalar: Silme butonu, sepet açma tetikleyicisi, sayfa içi hash ve javascript linkleri
        if (link.classList.contains('remove-cart-item') ||
            link.closest('.remove-cart-item') ||
            link.id === 'emdief-cart-trigger' ||
            link.id === 'bottomNavCartBtn' ||
            link.getAttribute('role') === 'button') {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
            return;
        }

        // Sayfa geçişi başladı -> sepeti hemen kapat
        closeCartDrawer();
    }, { passive: true });

    // Sayfa geri/ileri (bfcache) veya geçişlerinde açık kalmasını önle
    window.addEventListener('pageshow', closeCartDrawer);
    window.addEventListener('popstate', closeCartDrawer);
    window.addEventListener('beforeunload', closeCartDrawer);

    // jQuery WooCommerce Event Listeners & AJAX Item Removal
    if (window.jQuery) {
        const $ = window.jQuery;

        // Ürün sepete eklendiğinde çekmeceyi aç
        $(document.body).on('added_to_cart', (event, fragments, cart_hash, $button) => {
            openCartDrawer();
        });

        // Çekmece içi AJAX ürün çıkarma
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

            // Görsel yükleniyor / siliniyor durumu
            if ($item.length) {
                $item.css({
                    'opacity': '0.35',
                    'pointer-events': 'none',
                    'filter': 'grayscale(80%)',
                    'transition': 'all 0.25s ease'
                });
            }

            const ajaxUrl = (window.mis360Data && window.mis360Data.ajaxUrl) ? window.mis360Data.ajaxUrl : '/wp-admin/admin-ajax.php';
            const nonce = (window.mis360Data && window.mis360Data.nonce) ? window.mis360Data.nonce : '';

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
                        const fragments = response.data.fragments;

                        // DOM üzerindeki tüm ilgili fragmanları güncelle
                        $.each(fragments, function(selector, html) {
                            $(selector).replaceWith(html);
                        });

                        $(document.body).trigger('wc_fragments_refreshed');
                        $(document.body).trigger('removed_from_cart', [fragments, response.data.cart_hash, $btn]);
                    } else if ($btn.attr('href')) {
                        window.location.href = $btn.attr('href');
                        return;
                    }

                    // Eğer kullanıcı /sepet veya /odeme sayfasındaysa sayfayı da yenile
                    const path = window.location.pathname.toLowerCase();
                    if (path.includes('/sepet') || path.includes('/cart') || path.includes('/odeme') || path.includes('/checkout')) {
                        window.location.reload();
                    }
                },
                error: function() {
                    // Bağlantı hatasında standart WooCommerce silme URL'sine yönlendir
                    if ($btn.attr('href')) {
                        window.location.href = $btn.attr('href');
                    } else {
                        window.location.reload();
                    }
                }
            });
        });
    }
});
