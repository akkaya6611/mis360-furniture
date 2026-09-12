/**
 * Mis360-Mobilya Main JavaScript
 * Vanilla ES6+ - Zero jQuery Dependency
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobil Men? Y?netimi
    const mobileTrigger = document.getElementById('emdief-mobile-menu-trigger');
    const mobileDrawer = document.getElementById('emdief-mobile-drawer');
    const mobileClose = document.getElementById('emdief-mobile-close');
    const mobileOverlay = document.getElementById('emdief-mobile-overlay');

    function openMobileMenu() {
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
});

/* ==========================================================================
   TRENDYOL HERO SLIDER & PRODUCT CAROUSEL LOGIC
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {
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
            thumb.addEventListener('click', () => {
                const fullSrc = thumb.getAttribute('data-large_image') || thumb.getAttribute('src');
                if (mainImg && fullSrc) {
                    mainImg.src = fullSrc;
                    if (mainImg.parentElement && mainImg.parentElement.tagName === 'A') {
                        mainImg.parentElement.href = fullSrc;
                    }
                }
            });
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
});
