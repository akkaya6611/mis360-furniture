/**
 * Mis360-Mobilya Main JavaScript
 * Vanilla ES6+ - Zero jQuery Dependency
 */

function mis360Init() {
    // 1. Mobil Menü Yönetimi
    const mobileTrigger = document.getElementById('emdief-mobile-menu-trigger');
    const mobileDrawer = document.getElementById('emdief-mobile-drawer');
    const mobileClose = document.getElementById('emdief-mobile-close');
    const mobileOverlay = document.getElementById('emdief-mobile-overlay');

    function openMobileMenu() {
        if (typeof window.mis360CloseCartDrawer === 'function') {
            window.mis360CloseCartDrawer();
        } else {
            const cartDrawer = document.getElementById('emdief-cart-drawer');
            if (cartDrawer && cartDrawer.classList.contains('is-active')) {
                cartDrawer.classList.remove('is-active');
                cartDrawer.setAttribute('aria-hidden', 'true');
            }
        }
        if (mobileDrawer) {
            mobileDrawer.classList.add('is-active');
            mobileDrawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileMenu() {
        if (mobileDrawer) {
            mobileDrawer.classList.remove('is-active');
            mobileDrawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    if (mobileTrigger) mobileTrigger.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);

    const allDrawerNavLinks = document.querySelectorAll('.drawer-content a');
    allDrawerNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // WhatsApp dış bağlantıları hariç menüyü kapat
            if (!link.getAttribute('href') || !link.getAttribute('href').startsWith('https://wa.me')) {
                closeMobileMenu();
            }
        });
    });

    // Mobil Menü Kategorize Akordeon Grupları
    const drawerGroupToggles = document.querySelectorAll('.drawer-group-toggle');
    drawerGroupToggles.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const group = btn.closest('.drawer-group');
            if (!group) return;
            const links = group.querySelector('.drawer-group-links');
            const icon = btn.querySelector('.group-toggle-icon');
            const isOpen = group.classList.contains('is-open');

            if (isOpen) {
                group.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
                if (links) links.style.display = 'none';
                if (icon) icon.textContent = '▾';
            } else {
                group.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
                if (links) links.style.display = 'flex';
                if (icon) icon.textContent = '▴';
            }
        });
    });

    // Mobil Menü Giriş Butonu
    const drawerLoginBtn = document.getElementById('drawer-login-trigger');
    if (drawerLoginBtn) {
        drawerLoginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeMobileMenu();
            const headerLogin = document.getElementById('emdief-login-trigger');
            if (headerLogin) {
                headerLogin.click();
            }
        });
    }

    // 1.1. Masaüstü Dropdown Menü Tıklama Desteği
    const dropdownParents = document.querySelectorAll('.emdief-nav-menu li.menu-item-has-children');
    dropdownParents.forEach(item => {
        const link = item.querySelector(':scope > a');
        if (link) {
            link.addEventListener('click', (e) => {
                // Eğer menü henüz açık değilse dropdown'ı aç
                if (!item.classList.contains('is-open')) {
                    e.preventDefault();
                    dropdownParents.forEach(other => {
                        if (other !== item) other.classList.remove('is-open');
                    });
                    item.classList.add('is-open');
                }
            });
        }
    });

    // Sayfa dışına tıklandığında açık dropdown'ı kapat
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.menu-item-has-children')) {
            dropdownParents.forEach(item => item.classList.remove('is-open'));
        }
    });

    // 2. Mobil Arama Toggle
    const searchToggle = document.getElementById('emdief-mobile-search-toggle');
    const searchBar = document.getElementById('emdief-mobile-search-bar');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', () => {
            searchBar.classList.toggle('is-open');
            if (searchBar.classList.contains('is-open')) {
                const input = searchBar.querySelector('input');
                if (input) input.focus();
            }
        });
    }

    // 3. Giri? & Kay?t Modal Popup Y?netimi
    const authTrigger = document.getElementById('emdief-login-trigger');
    const authModal = document.getElementById('emdief-auth-modal');
    const authClose = document.getElementById('emdief-auth-close');
    const authOverlay = document.getElementById('emdief-auth-overlay');

    function openAuthModal() {
        if (authModal) {
            authModal.classList.add('is-active');
            authModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            const firstInput = authModal.querySelector('input[type="text"], input[type="email"]');
            if (firstInput) setTimeout(() => firstInput.focus(), 150);
        }
    }

    function closeAuthModal() {
        if (authModal) {
            authModal.classList.remove('is-active');
            authModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    if (authTrigger) authTrigger.addEventListener('click', openAuthModal);
    if (authClose) authClose.addEventListener('click', closeAuthModal);
    if (authOverlay) authOverlay.addEventListener('click', closeAuthModal);

    // Modal ??i Tab De?i?imi (Giri? Yap / Kay?t Ol)
    const tabButtons = document.querySelectorAll('.auth-tab-btn');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            tabButtons.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            const panels = document.querySelectorAll('.auth-form-panel');
            panels.forEach(p => p.classList.remove('is-active'));

            const activePanel = document.getElementById('auth-tab-' + targetTab);
            if (activePanel) {
                activePanel.classList.add('is-active');
                const inp = activePanel.querySelector('input');
                if (inp) inp.focus();
            }
        });
    });

    // ?ifre G?ster / Gizle
    const togglePassBtn = document.getElementById('emdief-toggle-pass');
    const passInput = document.getElementById('emdief-user-pass');
    if (togglePassBtn && passInput) {
        togglePassBtn.addEventListener('click', () => {
            if (passInput.type === 'password') {
                passInput.type = 'text';
                togglePassBtn.textContent = '??';
            } else {
                passInput.type = 'password';
                togglePassBtn.textContent = '???';
            }
        });
    }

    // 4. Sayfa Ba??na D?n (Back to Top)
    const backToTopBtn = document.getElementById('emdief-back-to-top');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            if (backToTopBtn) backToTopBtn.classList.add('show');
        } else {
            if (backToTopBtn) backToTopBtn.classList.remove('show');
        }
    }, { passive: true });

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 5. ESC tu?u ile t?m ?ekmeceleri ve modallar? kapatma
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMobileMenu();
            closeAuthModal();
            const cartDrawer = document.getElementById('emdief-cart-drawer');
            if (cartDrawer && cartDrawer.classList.contains('is-active')) {
                cartDrawer.classList.remove('is-active');
                cartDrawer.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            if (searchBar && searchBar.classList.contains('is-open')) {
                searchBar.classList.remove('is-open');
            }
        }
    });

    // 6. Mobil Alt Gezinme Çubuğu (Bottom Navigation Bar)
    const bottomNavCategories = document.getElementById('bottomNavCategoriesBtn');
    if (bottomNavCategories) {
        bottomNavCategories.addEventListener('click', (e) => {
            e.preventDefault();
            openMobileMenu();
        });
    }

    const bottomNavSearch = document.getElementById('bottomNavSearchBtn');
    if (bottomNavSearch && searchBar) {
        bottomNavSearch.addEventListener('click', (e) => {
            e.preventDefault();
            searchBar.classList.toggle('is-open');
            if (searchBar.classList.contains('is-open')) {
                const input = searchBar.querySelector('input');
                if (input) input.focus();
            }
        });
    }

    const bottomNavCart = document.getElementById('bottomNavCartBtn');
    if (bottomNavCart) {
        bottomNavCart.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof window.mis360OpenCartDrawer === 'function') {
                window.mis360OpenCartDrawer();
            } else {
                const cartDrawer = document.getElementById('emdief-cart-drawer');
                if (cartDrawer) {
                    cartDrawer.classList.add('is-active');
                    cartDrawer.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }
            }
        });
    }

    const bottomNavAccount = document.getElementById('bottomNavAccountBtn');
    if (bottomNavAccount) {
        bottomNavAccount.addEventListener('click', (e) => {
            e.preventDefault();
            openAuthModal();
        });
    }

    // 7. Tekil Ürün Mobilde Sabit Satın Alma Çubuğu (Sticky Buy Bar)
    const stickyBuyBar = document.getElementById('emdiefStickyBuyBar');
    const triggerStickyAddToCart = document.getElementById('triggerStickyAddToCart');

    if (stickyBuyBar) {
        const mainAddToCartBtn = document.querySelector('form.cart .single_add_to_cart_button') || document.querySelector('button[name="add-to-cart"]');

        window.addEventListener('scroll', () => {
            if (window.innerWidth <= 768) {
                if (mainAddToCartBtn) {
                    const rect = mainAddToCartBtn.getBoundingClientRect();
                    if (rect.bottom < 0) {
                        stickyBuyBar.classList.add('is-visible');
                    } else {
                        stickyBuyBar.classList.remove('is-visible');
                    }
                } else if (window.scrollY > 350) {
                    stickyBuyBar.classList.add('is-visible');
                } else {
                    stickyBuyBar.classList.remove('is-visible');
                }
            } else {
                stickyBuyBar.classList.remove('is-visible');
            }
        }, { passive: true });

        if (triggerStickyAddToCart && mainAddToCartBtn) {
            triggerStickyAddToCart.addEventListener('click', (e) => {
                e.preventDefault();
                mainAddToCartBtn.click();
            });
        }
    }

    /* ==========================================================================
       TRENDYOL HERO SLIDER & PRODUCT CAROUSEL LOGIC
       ========================================================================== */
    // 1. Trendyol Hero Slider
    const heroSlider = document.getElementById('emdiefMainHeroSlider');
    if (heroSlider) {
        const slides = heroSlider.querySelectorAll('.hero-slide-item');
        const dots = heroSlider.querySelectorAll('.hero-dots-indicator .dot');
        const prevBtn = document.getElementById('heroPrevBtn');
        const nextBtn = document.getElementById('heroNextBtn');
        let currentIdx = 0;
        let timer = null;

        function goToSlide(idx) {
            if (idx >= slides.length) idx = 0;
            if (idx < 0) idx = slides.length - 1;
            currentIdx = idx;

            slides.forEach((s, i) => {
                s.classList.toggle('active', i === currentIdx);
            });
            dots.forEach((d, i) => {
                d.classList.toggle('active', i === currentIdx);
            });
        }

        function nextSlide() {
            goToSlide(currentIdx + 1);
        }

        function prevSlide() {
            goToSlide(currentIdx - 1);
        }

        function startTimer() {
            stopTimer();
            timer = setInterval(nextSlide, 5000);
        }

        function stopTimer() {
            if (timer) clearInterval(timer);
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                startTimer();
            });
        }
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                startTimer();
            });
        }
        dots.forEach(dot => {
            dot.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-index'));
                goToSlide(idx);
                startTimer();
            });
        });

        heroSlider.addEventListener('mouseenter', stopTimer);
        heroSlider.addEventListener('mouseleave', startTimer);

        startTimer();
    }

    // 2. Product Slider Track Arrows
    document.querySelectorAll('.btn-slider-arrow').forEach(btn => {
        btn.addEventListener('click', function () {
            const trackId = this.getAttribute('data-target');
            const track = document.getElementById(trackId);
            if (track) {
                const scrollOffset = 300;
                if (this.classList.contains('btn-prev')) {
                    track.scrollBy({ left: -scrollOffset, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: scrollOffset, behavior: 'smooth' });
                }
            }
        });
    });

    // 2.1 Trendyol Slider Track Arrows
    document.querySelectorAll('.trendyol-nav-arrow').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const track = document.getElementById(targetId);
            if (track) {
                const scrollAmount = 440;
                if (this.classList.contains('trendyol-nav-prev')) {
                    track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            }
        });
    });

    // 3. Wishlist Heart Button Toggling
    document.querySelectorAll('.btn-wishlist-heart, .trendyol-heart-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            this.classList.toggle('is-active');
            const svg = this.querySelector('svg');
            if (this.classList.contains('is-active')) {
                svg.setAttribute('fill', '#ef4444');
                svg.setAttribute('stroke', '#ef4444');
            } else {
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
            }
        });
    });

    // 3.1 Trendyol Countdown Timer
    const digitHours = document.querySelectorAll('.countdown-hours');
    const digitMins = document.querySelectorAll('.countdown-mins');
    const digitSecs = document.querySelectorAll('.countdown-secs');
    if (digitHours.length > 0) {
        let totalSecs = 5 * 3600 + 6 * 60 + 36;
        setInterval(() => {
            if (totalSecs > 0) totalSecs--;
            const h = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
            const m = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
            const s = String(totalSecs % 60).padStart(2, '0');
            digitHours.forEach(el => el.textContent = h);
            digitMins.forEach(el => el.textContent = m);
            digitSecs.forEach(el => el.textContent = s);
        }, 1000);
    }

    // 4. Kupon Kodu Kopyalama
    const btnCopy = document.getElementById('btnCopyCode');
    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            const code = document.getElementById('emdiefCouponCode').innerText;
            navigator.clipboard.writeText(code).then(() => {
                this.innerText = 'Kopyalandı!';
                this.style.background = '#047857';
                setTimeout(() => {
                    this.innerText = 'Kopyala';
                    this.style.background = '#16a34a';
                }, 2000);
            });
        });
    }

    // 5. Geri Sayım Sayacı
    const countdownEl = document.getElementById('flashDealCountdown');
    if (countdownEl) {
        let totalSeconds = 7 * 3600 + 28 * 60 + 14;
        setInterval(() => {
            if (totalSeconds > 0) {
                totalSeconds--;
                const h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const s = String(totalSeconds % 60).padStart(2, '0');
                countdownEl.innerText = `${h}:${m}:${s}`;
            }
        }, 1000);
    }

    // 6. WooCommerce Tekil Urun Galerisi Gorunurluk & Kucuk Resim Destegi
    const galleryEl = document.querySelector('.woocommerce-product-gallery');
    if (galleryEl) {
        galleryEl.style.opacity = '1';
        galleryEl.style.visibility = 'visible';

        const mainImg = galleryEl.querySelector('.woocommerce-product-gallery__image img, .wp-post-image');
        const thumbs = galleryEl.querySelectorAll('.flex-control-thumbs img, div.thumbnails a img');
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', (e) => {
                e.preventDefault();
                const fullSrc = thumb.getAttribute('data-large_image') || thumb.getAttribute('src');
                if (mainImg && fullSrc) {
                    mainImg.src = fullSrc;
                    if (mainImg.parentElement && mainImg.parentElement.tagName === 'A') {
                        mainImg.parentElement.href = fullSrc;
                    }
                }
            });
        });

        // Galeri görsellerine tıklandığında doğrudan browser'da ham görsel dosyasının (.jpg/.png) açılmasını engelle
        galleryEl.addEventListener('click', function(e) {
            const anchor = e.target.closest('.woocommerce-product-gallery__image a');
            if (anchor) {
                // Eğer PhotoSwipe açık değilse browser'ın doğrudan resim sayfasına gitmesini engelle
                if (!document.querySelector('.pswp--open')) {
                    e.preventDefault();
                }
            }
        });
    }

    // 7. Akilli Urun Slideri (Son Gezilenler & Ilgili Urunler Navigasyon)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.slider-btn');
        if (!btn) return;
        const targetId = btn.getAttribute('data-target');
        const track = document.getElementById(targetId);
        if (!track) return;

        const firstItem = track.querySelector('.emdief-slider-item');
        const itemWidth = firstItem ? (firstItem.offsetWidth + 16) : 260;
        const scrollAmount = itemWidth * 2;

        if (btn.classList.contains('prev-btn')) {
            track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    });

    // 8. Adet Arttırma & Azaltma (+/-) Butonları (Quantity Stepper)
    function showStockNotice(wrapper, maxQty) {
        const form = wrapper.closest('form.cart') || wrapper.closest('tr.cart_item') || wrapper.parentElement;
        if (!form) return;

        let notice = form.querySelector('.emdief-qty-limit-notice');
        if (!notice) {
            notice = document.createElement('div');
            notice.className = 'emdief-qty-limit-notice';
            form.appendChild(notice);
        }

        const count = parseInt(maxQty) || 1;
        const message = (count === 1) 
            ? 'Maalesef bu üründen sadece 1 adet kaldı.' 
            : `Maalesef bu üründen sadece ${count} adet kaldı.`;

        notice.innerHTML = '<span class="notice-icon">⚠️</span> ' + message;
        notice.style.display = 'flex';

        wrapper.classList.remove('is-shaking');
        void wrapper.offsetWidth;
        wrapper.classList.add('is-shaking');

        clearTimeout(wrapper._noticeTimer);
        wrapper._noticeTimer = setTimeout(() => {
            notice.style.display = 'none';
            wrapper.classList.remove('is-shaking');
        }, 3500);
    }

    function ensureQtyButtons(root = document) {
        root.querySelectorAll('.quantity').forEach(qty => {
            let input = qty.querySelector('input.qty');
            if (!input) return;

            // Eğer WooCommerce min=max durumunda gizli input bastıysa görünür number'a çevir
            if (input.type === 'hidden') {
                input.type = 'number';
            }
            if (!input.value || input.value === '0') {
                input.value = '1';
            }

            if (qty.classList.contains('emdief-qty-stepper') && qty.querySelector('.emdief-qty-btn')) return;
            qty.classList.add('emdief-qty-stepper');

            const minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'emdief-qty-btn qty-minus';
            minus.setAttribute('aria-label', 'Azalt');
            minus.setAttribute('tabindex', '-1');
            minus.textContent = '−';

            const plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'emdief-qty-btn qty-plus';
            plus.setAttribute('aria-label', 'Arttır');
            plus.setAttribute('tabindex', '-1');
            plus.textContent = '+';

            qty.insertBefore(minus, input);
            qty.appendChild(plus);
        });
    }

    ensureQtyButtons();
    document.addEventListener('updated_wc_div', () => ensureQtyButtons());
    document.addEventListener('wc_fragments_refreshed', () => ensureQtyButtons());
    document.addEventListener('wc_fragments_loaded', () => ensureQtyButtons());

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.emdief-qty-btn');
        if (!btn) return;
        e.preventDefault();

        const wrapper = btn.closest('.quantity');
        if (!wrapper) return;

        const input = wrapper.querySelector('input.qty');
        if (!input || input.disabled || input.readOnly) return;

        let currentVal = parseFloat(input.value);
        if (isNaN(currentVal) || currentVal < 1) currentVal = 1;

        const step = parseFloat(input.getAttribute('step')) || 1;
        const minAttr = input.getAttribute('min');
        const maxAttr = input.getAttribute('max');
        const min = (minAttr !== '' && minAttr !== null) ? parseFloat(minAttr) : 1;
        const max = (maxAttr !== '' && maxAttr !== null && !isNaN(parseFloat(maxAttr))) ? parseFloat(maxAttr) : Infinity;

        if (btn.classList.contains('qty-minus')) {
            let newVal = currentVal - step;
            if (newVal < min) newVal = min;
            input.value = newVal;
        } else if (btn.classList.contains('qty-plus')) {
            if (currentVal >= max) {
                showStockNotice(wrapper, max);
                return;
            }
            let newVal = currentVal + step;
            if (newVal > max) {
                newVal = max;
                showStockNotice(wrapper, max);
            }
            input.value = newVal;
        }

        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('input', { bubbles: true }));
    });

    document.addEventListener('change', function(e) {
        if (!e.target.matches('input.qty')) return;
        const input = e.target;
        const wrapper = input.closest('.quantity');
        if (!wrapper) return;

        const maxAttr = input.getAttribute('max');
        const max = (maxAttr !== '' && maxAttr !== null && !isNaN(parseFloat(maxAttr))) ? parseFloat(maxAttr) : Infinity;
        let val = parseFloat(input.value);

        if (isNaN(val) || val < 1) {
            input.value = 1;
        } else if (val > max) {
            input.value = max;
            showStockNotice(wrapper, max);
        }
    });

    // 7. Çerez Onay Bildirimi (Cookie Consent Banner)
    const cookieBanner = document.getElementById('emdief-cookie-banner');
    const cookieAcceptBtn = document.getElementById('emdiefCookieAccept');
    const cookieCloseBtn = document.getElementById('emdiefCookieClose');

    if (cookieBanner) {
        let hasConsent = false;
        try {
            hasConsent = localStorage.getItem('emdief_cookie_consent');
        } catch (err) {}

        if (!hasConsent) {
            setTimeout(() => {
                cookieBanner.style.display = 'block';
                requestAnimationFrame(() => {
                    cookieBanner.classList.add('is-visible');
                });
            }, 1200);
        }

        function dismissCookieBanner(val) {
            cookieBanner.classList.remove('is-visible');
            try {
                localStorage.setItem('emdief_cookie_consent', val || 'accepted');
            } catch (err) {}
            setTimeout(() => {
                cookieBanner.style.display = 'none';
            }, 350);
        }

        if (cookieAcceptBtn) {
            cookieAcceptBtn.addEventListener('click', () => dismissCookieBanner('accepted'));
        }
        if (cookieCloseBtn) {
            cookieCloseBtn.addEventListener('click', () => dismissCookieBanner('closed'));
        }
    }

    // 8. Tekil Ürün SSS Akordeon Etkileşimi
    const productFaqItems = document.querySelectorAll('.product-faq-accordion .product-faq-item');
    if (productFaqItems.length) {
        productFaqItems.forEach(item => {
            const toggle = item.querySelector('.product-faq-toggle');
            const answer = item.querySelector('.product-faq-answer');
            const icon = item.querySelector('.product-faq-icon');
            if (!toggle || !answer) return;

            toggle.addEventListener('click', () => {
                const isOpen = item.classList.contains('is-open');

                // Diğer açık olanları kapat
                productFaqItems.forEach(other => {
                    other.classList.remove('is-open');
                    const otherToggle = other.querySelector('.product-faq-toggle');
                    const otherAnswer = other.querySelector('.product-faq-answer');
                    const otherIcon = other.querySelector('.product-faq-icon');
                    if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
                    if (otherAnswer) otherAnswer.style.display = 'none';
                    if (otherIcon) otherIcon.textContent = '+';
                });

                if (!isOpen) {
                    item.classList.add('is-open');
                    toggle.setAttribute('aria-expanded', 'true');
                    answer.style.display = 'block';
                    if (icon) icon.textContent = '−';
                }
            });
        });
    }
}


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mis360Init);
} else {
    mis360Init();
}
