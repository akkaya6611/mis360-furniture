/**
 * Mis360-Furniture AJAX Mini-Cart & Drawer Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    const cartDrawer = document.getElementById('emdief-cart-drawer');
    const cartTrigger = document.getElementById('emdief-cart-trigger');
    const cartClose = document.getElementById('emdief-cart-close');
    const cartOverlay = document.getElementById('emdief-cart-overlay');

    function openCartDrawer() {
        if (cartDrawer) {
            cartDrawer.classList.add('is-active');
            cartDrawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCartDrawer() {
        if (cartDrawer) {
            cartDrawer.classList.remove('is-active');
            cartDrawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    if (cartTrigger) cartTrigger.addEventListener('click', openCartDrawer);
    if (cartClose) cartClose.addEventListener('click', closeCartDrawer);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCartDrawer);

    // jQuery WooCommerce Event Listeners (WooCommerce uses jQuery triggers)
    if (window.jQuery) {
        window.jQuery(document.body).on('added_to_cart', (event, fragments, cart_hash, $button) => {
            openCartDrawer();
        });

        // �ekmece i�i �r�n ��karma
        window.jQuery(document).on('click', '.remove-cart-item', function(e) {
            e.preventDefault();
            const $btn = window.jQuery(this);
            const removeUrl = $btn.attr('href');

            $btn.closest('.emdief-drawer-item').css('opacity', '0.4');

            window.jQuery.ajax({
                type: 'GET',
                url: removeUrl,
                success: function(response) {
                    window.jQuery(document.body).trigger('wc_fragment_refresh');
                }
            });
        });
    }
});
