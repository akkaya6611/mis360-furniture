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
            const drawerContent = mobileDrawer.querySelector('.drawer-content');
            if (drawerContent) {
                drawerContent.scrollTop = 0;
            }
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

    function openAuthModal(defaultTab) {
        if (authModal) {
            if (defaultTab) {
                const targetBtn = authModal.querySelector(`.auth-tab-btn[data-tab="${defaultTab}"]`);
                if (targetBtn) {
                    targetBtn.click();
                }
            }
            authModal.classList.add('is-active');
            authModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            const activePanel = authModal.querySelector('.auth-form-panel.is-active');
            const firstInput = (activePanel || authModal).querySelector('input[type="text"], input[type="email"]');
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

    // Global erişim
    window.mis360OpenAuthModal = openAuthModal;
    window.mis360CloseAuthModal = closeAuthModal;

    if (authTrigger) authTrigger.addEventListener('click', () => openAuthModal('login'));
    if (authClose) authClose.addEventListener('click', closeAuthModal);
    if (authOverlay) authOverlay.addEventListener('click', closeAuthModal);

    // Misafir kullanıcılar için Sepet Sayfası, Bloklar ve Çekmecedeki TÜM Ödeme Butonlarını Yakala
    document.addEventListener('click', (e) => {
        // Zaten giriş yapmışsa normal devam etsin
        if (window.mis360Data && window.mis360Data.isUserLoggedIn) {
            return;
        }

        const target = e.target;
        if (!target) return;

        const checkoutBtn = target.closest(
            '.emdief-checkout-auth-btn, ' +
            '.checkout-button, ' +
            '.wc-block-cart__submit-button, ' +
            '.wc-block-components-checkout-button, ' +
            '.wc-block-cart__submit a, ' +
            'a.wc-block-cart__submit-button, ' +
            'a[href*="/odeme"], ' +
            'a[href*="/checkout"], ' +
            'button[data-auth-prompt="checkout"]'
        );

        if (!checkoutBtn) return;

        // Navigasyon veya submit işlemini KESİNLİKLE durdur!
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        // Çekmece açıksa kapat
        if (typeof window.mis360CloseCartDrawer === 'function') {
            window.mis360CloseCartDrawer();
        }

        const modal = document.getElementById('emdief-auth-modal');
        if (modal) {
            const checkoutUrl = (window.mis360Data && window.mis360Data.checkoutUrl) ? window.mis360Data.checkoutUrl : '/odeme/';
            const loginRedir = modal.querySelector('#emdief-login-redirect, input[name="redirect_to"]');
            if (loginRedir) loginRedir.value = checkoutUrl;

            const regRedir = modal.querySelector('#emdief-reg-redirect, input[name="redirect"]');
            if (regRedir) regRedir.value = checkoutUrl;

            const modalTitle = modal.querySelector('.auth-modal-title');
            if (modalTitle) {
                modalTitle.textContent = 'Ödemeye Devam Edebilmek İçin';
            }

            const modalSub = modal.querySelector('.auth-modal-subtitle');
            if (modalSub) {
                modalSub.innerHTML = '⚠️ <strong>Ödemeye devam edebilmek için hesabınıza giriş yapmalı ya da üyelik oluşturmalısınız.</strong><br><span style="font-size:12px;color:#64748b;">(Giriş yaptığınızda veya yeni üyelik oluşturduğunuzda doğrudan siparişinize devam edebilirsiniz.)</span>';
            }

            openAuthModal('login');
        } else {
            window.location.href = (window.mis360Data && window.mis360Data.checkoutUrl) ? window.mis360Data.checkoutUrl : '/odeme/';
        }
        return false;
    }, true); // useCapture = true! React ve Gutenberg event delegation öncesi en başta yakalar!

    // Ödeme Sayfasında (Checkout) Misafir Kullanıcı Kontrolü ve Zorunlu Üyelik Uyarısı
    function initCheckoutAuthGuard() {
        const isCheckout = (window.mis360Data && window.mis360Data.isCheckout) || 
                           window.location.pathname.indexOf('/odeme') !== -1 || 
                           window.location.pathname.indexOf('/checkout') !== -1;

        if (!isCheckout) return;

        const isLoggedIn = window.mis360Data ? window.mis360Data.isUserLoggedIn : false;
        if (isLoggedIn) return;

        // 1. Eğer Gutenberg Blok veya klasik checkout varsa ve uyarı kutusu henüz sayfada yoksa dinamik ekle
        function checkAndInjectGate() {
            if (document.querySelector('.emdief-checkout-auth-gate-box')) return;

            const targetContainer = document.querySelector('.wp-block-woocommerce-checkout, form.checkout, .woocommerce-checkout, main#primary .entry-content');
            if (!targetContainer) return;

            const gateBox = document.createElement('div');
            gateBox.className = 'emdief-checkout-auth-gate-box';
            gateBox.innerHTML = `
                <div class="auth-gate-badge">
                    <span class="gate-pulse"></span>
                    🔒 ÖDEME ÖNCESİ HESAP DOĞRULAMA
                </div>
                <div class="auth-gate-content">
                    <h3 class="auth-gate-title">
                        ⚠️ Ödemeye Devam Edebilmek İçin Hesabınıza Giriş Yapmalı ya da Üyelik Oluşturmalısınız
                    </h3>
                    <p class="auth-gate-desc">
                        Değerli müşterimiz; siparişinizi güvenle tamamlamak ve ödemeye devam edebilmek için lütfen <strong>hesabınıza giriş yapın ya da 10 saniyede ücretsiz üye olun.</strong> Adres ve fatura bilgileriniz hesabınıza güvenle kaydedilecektir.
                    </p>
                    <div class="auth-gate-buttons">
                        <button type="button" class="emdief-btn btn-primary auth-gate-btn-login" onclick="if(window.mis360OpenAuthModal){window.mis360OpenAuthModal('login');} return false;">
                            🔑 Giriş Yap
                        </button>
                        <button type="button" class="emdief-btn btn-warm auth-gate-btn-register" onclick="if(window.mis360OpenAuthModal){window.mis360OpenAuthModal('register');} return false;">
                            ✨ Hızlı Üye Ol (10 Saniyede Ücretsiz)
                        </button>
                    </div>
                </div>
            `;
            targetContainer.insertBefore(gateBox, targetContainer.firstChild);
        }

        checkAndInjectGate();
        setTimeout(checkAndInjectGate, 500);
        setTimeout(checkAndInjectGate, 1200);

        // 2. Ödeme sayfasına gelindiğinde kullanıcıyı hemen uyar (Popup aç)
        setTimeout(() => {
            const modal = document.getElementById('emdief-auth-modal');
            if (modal && !modal.classList.contains('is-active')) {
                const modalTitle = modal.querySelector('.auth-modal-title');
                if (modalTitle) {
                    modalTitle.textContent = 'Ödemeye Devam Edebilmek İçin';
                }
                const modalSub = modal.querySelector('.auth-modal-subtitle');
                if (modalSub) {
                    modalSub.innerHTML = '⚠️ <strong>Ödemeye devam edebilmek için hesabınıza giriş yapmalı ya da üyelik oluşturmalısınız.</strong>';
                }
                openAuthModal('login');
            }
        }, 700);

        // 3. Misafir kullanıcı adres formuna odaklanırsa popup'ı aç
        document.addEventListener('focusin', (e) => {
            if (window.mis360Data && window.mis360Data.isUserLoggedIn) return;
            const input = e.target.closest('input, select, textarea');
            if (!input) return;
            if (input.closest('.emdief-modal') || input.closest('.emdief-header')) return;
            if (input.closest('.wp-block-woocommerce-checkout, form.checkout, .woocommerce-billing-fields, .wc-block-components-address-form')) {
                openAuthModal('login');
            }
        });
    }

    initCheckoutAuthGuard();

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

    // Modal Formları AJAX ile Gönderme (Sayfa yenilenmesini ve /my-account/'a fırlatılmasını engeller)
    const authModalForms = document.querySelectorAll('#emdief-auth-modal .emdief-auth-form');
    authModalForms.forEach((form) => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const isRegister = form.closest('#auth-tab-register') !== null;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            // Mevcut bildirim mesajını temizle
            const oldFeedback = form.querySelector('.auth-feedback-msg');
            if (oldFeedback) oldFeedback.remove();

            function showFeedback(type, message) {
                const msgEl = document.createElement('div');
                msgEl.className = 'auth-feedback-msg ' + type;
                msgEl.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + message;
                form.insertBefore(msgEl, form.firstChild);
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = isRegister ? '<span>Hesap Oluşturuluyor... ⏳</span>' : '<span>Giriş Yapılıyor... ⏳</span>';
            }

            const formData = new FormData(form);
            
            // WordPress kanonik AJAX adresi
            let ajaxUrl = (window.mis360Data && window.mis360Data.ajaxUrl) ? window.mis360Data.ajaxUrl : '/wp-admin/admin-ajax.php';
            if (!ajaxUrl && window.location && window.location.origin) {
                ajaxUrl = window.location.origin + '/wp-admin/admin-ajax.php';
            }

            const nonce = (window.mis360Data && window.mis360Data.nonce) ? window.mis360Data.nonce : '';

            formData.append('action', isRegister ? 'mis360_ajax_register' : 'mis360_ajax_login');
            formData.append('security', nonce);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => {
                if (res.redirected) {
                    const fallbackUrl = (window.mis360Data && window.mis360Data.checkoutUrl) ? window.mis360Data.checkoutUrl : '/odeme/';
                    window.location.href = res.url || fallbackUrl;
                    return null;
                }
                return res.text();
            })
            .then(rawText => {
                if (!rawText) return;
                let data = null;
                try {
                    data = JSON.parse(rawText);
                } catch (jsonErr) {
                    console.warn('Auth raw response (non-json):', rawText);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                    // Eğer dönen cevap bir HTML sayfası veya uzun metin ise ASLA ekrana ham kod dökümü yapma!
                    const trimmed = rawText.trim();
                    if (trimmed.startsWith('<') || trimmed.indexOf('<!DOCTYPE') !== -1 || trimmed.indexOf('<html') !== -1 || trimmed.length > 250) {
                        // Eğer oturum açıldıysa sayfayı doğrudan ödemeye yönlendir
                        if (trimmed.indexOf('wp-login.php?action=logout') !== -1 || trimmed.indexOf('logged-in') !== -1) {
                            showFeedback('success', 'Giriş başarılı! Yönlendiriliyorsunuz...');
                            setTimeout(() => {
                                window.location.href = (window.mis360Data && window.mis360Data.checkoutUrl) ? window.mis360Data.checkoutUrl : '/odeme/';
                            }, 500);
                            return;
                        }
                        showFeedback('error', 'İşlem gerçekleştirilemedi. Lütfen bilgilerinizi kontrol edip tekrar deneyiniz.');
                    } else {
                        const cleanErr = trimmed.replace(/<[^>]*>?/gm, '').trim();
                        if (cleanErr && cleanErr !== '-1' && cleanErr !== '0') {
                            showFeedback('error', cleanErr);
                        } else {
                            showFeedback('error', 'İşlem gerçekleştirilemedi. Lütfen bilgilerinizi kontrol edip tekrar deneyiniz.');
                        }
                    }
                    return;
                }

                if (data && data.success) {
                    showFeedback('success', (data.data && data.data.message) ? data.data.message : 'Başarılı! Yönlendiriliyorsunuz...');
                    setTimeout(() => {
                        const targetUrl = (data.data && data.data.redirect) ? data.data.redirect : ((window.mis360Data && window.mis360Data.checkoutUrl) ? window.mis360Data.checkoutUrl : '/odeme/');
                        window.location.href = targetUrl;
                    }, 500);
                } else {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                    const errorMsg = (data && data.data && data.data.message) ? data.data.message : 'Bir hata oluştu. Lütfen bilgilerinizi kontrol ediniz.';
                    showFeedback('error', errorMsg);
                }
            })
            .catch(err => {
                console.error('Auth fetch error:', err);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
                showFeedback('error', 'Bağlantı hatası oluştu. Lütfen bilgilerinizi kontrol edip tekrar deneyiniz.');
            });
        });
    });

    // Şifre Göster / Gizle (Tüm formlar için evrensel)
    document.querySelectorAll('.toggle-password-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const wrap = btn.closest('.input-password-wrap');
            if (!wrap) return;
            const input = wrap.querySelector('input');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁️';
            }
        });
    });

    // Telefon Numarası Otomatik Formatlama / Maskeleme (0 (5XX) XXX XX XX)
    function formatTurkishPhone(value) {
        let digits = value.replace(/\D/g, '');
        if (digits.startsWith('90') && digits.length > 10) {
            digits = digits.substring(2);
        }
        if (!digits.startsWith('0') && digits.length > 0) {
            digits = '0' + digits;
        }
        digits = digits.substring(0, 11);

        let res = '';
        if (digits.length > 0) res += digits.substring(0, 1);
        if (digits.length > 1) res += ' (' + digits.substring(1, Math.min(4, digits.length));
        if (digits.length >= 4) res += ') ';
        if (digits.length > 4) res += digits.substring(4, Math.min(7, digits.length));
        if (digits.length >= 7) res += ' ';
        if (digits.length > 7) res += digits.substring(7, Math.min(9, digits.length));
        if (digits.length >= 9) res += ' ';
        if (digits.length > 9) res += digits.substring(9, 11);
        return res;
    }

    document.addEventListener('input', (e) => {
        const target = e.target;
        if (!target) return;
        if (target.classList.contains('emdief-phone-input') || target.name === 'billing_phone' || target.id === 'emdief-reg-phone' || target.id === 'reg_billing_phone') {
            const prevVal = target.value;
            const formatted = formatTurkishPhone(prevVal);
            if (prevVal !== formatted) {
                target.value = formatted;
            }
        }
    });

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

        if ('IntersectionObserver' in window && mainAddToCartBtn) {
            const observer = new IntersectionObserver((entries) => {
                if (window.innerWidth <= 768) {
                    entries.forEach(entry => {
                        // When main button is scrolled past viewport, reveal sticky bar
                        if (!entry.isIntersecting && entry.boundingClientRect.top < 0) {
                            stickyBuyBar.classList.add('is-visible');
                        } else {
                            stickyBuyBar.classList.remove('is-visible');
                        }
                    });
                } else {
                    stickyBuyBar.classList.remove('is-visible');
                }
            }, { threshold: 0 });

            observer.observe(mainAddToCartBtn);
        } else {
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        if (window.innerWidth <= 768) {
                            if (window.scrollY > 400) {
                                stickyBuyBar.classList.add('is-visible');
                            } else {
                                stickyBuyBar.classList.remove('is-visible');
                            }
                        } else {
                            stickyBuyBar.classList.remove('is-visible');
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        }

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

    // 6. WooCommerce Tekil Urun Galerisi Otomatik Slayt & Kucuk Resim Destegi
    const galleryEl = document.querySelector('.woocommerce-product-gallery');
    if (galleryEl) {
        galleryEl.style.opacity = '1';
        galleryEl.style.visibility = 'visible';

        const mainImg = galleryEl.querySelector('.woocommerce-product-gallery__image img, .wp-post-image');
        const thumbs = galleryEl.querySelectorAll('.flex-control-thumbs li, .flex-control-thumbs img, div.thumbnails a img');

        // Otomatik Slayt Geçişi (3.5 Saniyede Bir)
        if (thumbs.length > 1) {
            let currentSlideIdx = 0;
            let slideInterval = null;
            let isPaused = false;

            const nextSlide = () => {
                if (isPaused) return;

                // 1. FlexSlider API mevcutsa doğrudan tetikle
                if (window.jQuery && typeof jQuery.fn.flexslider === 'function') {
                    const $slider = jQuery(galleryEl).data('flexslider');
                    if ($slider && typeof $slider.flexAnimate === 'function') {
                        const target = ($slider.currentSlide + 1) % $slider.count;
                        $slider.flexAnimate(target);
                        return;
                    }
                }

                // 2. Fallback: Küçük resim tıklaması
                currentSlideIdx = (currentSlideIdx + 1) % thumbs.length;
                const targetThumb = thumbs[currentSlideIdx];
                if (targetThumb) {
                    targetThumb.click();
                }
            };

            const startSlideTimer = () => {
                if (slideInterval) clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 3500);
            };

            // Fare üzerine gelince duraklat, ayrılınca devam et
            galleryEl.addEventListener('mouseenter', () => { isPaused = true; });
            galleryEl.addEventListener('mouseleave', () => { isPaused = false; });
            galleryEl.addEventListener('touchstart', () => { isPaused = true; }, { passive: true });
            galleryEl.addEventListener('touchend', () => {
                setTimeout(() => { isPaused = false; }, 3000);
            }, { passive: true });

            // Kullanıcı bir küçük resme tıkladığında indeksi senkronize et
            thumbs.forEach((t, i) => {
                t.addEventListener('click', () => {
                    currentSlideIdx = i;
                });
            });

            startSlideTimer();
        }

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
        requestAnimationFrame(() => {
            wrapper.classList.add('is-shaking');
        });

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
            if (!toggle || !answer) return;

            toggle.addEventListener('click', () => {
                const isOpen = item.classList.contains('is-open');

                // Diğer açık olanları kapat
                productFaqItems.forEach(other => {
                    other.classList.remove('is-open');
                    const otherToggle = other.querySelector('.product-faq-toggle');
                    const otherAnswer = other.querySelector('.product-faq-answer');
                    if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
                    if (otherAnswer) otherAnswer.style.display = 'none';
                });

                if (!isOpen) {
                    item.classList.add('is-open');
                    toggle.setAttribute('aria-expanded', 'true');
                    answer.style.display = 'block';
                }
            });
        });
    }

    // 9. Tekil Ürün Hızlı Kurulum Videosu Butonu Etkileşimi
    const videoQuickBadge = document.querySelector('.product-video-quick-badge');
    if (videoQuickBadge) {
        videoQuickBadge.addEventListener('click', function(e) {
            e.preventDefault();

            // WooCommerce Kurulum Videosu sekmesini bul ve tetikle
            const tabBtn = document.querySelector('.woocommerce-tabs ul.tabs li.installation_video_tab a') || 
                           document.querySelector('.woocommerce-tabs ul.tabs a[href*="tab-installation_video"]');

            if (tabBtn) {
                tabBtn.click();
            }

            // Sekme paneline yumuşak kaydır
            const targetPanel = document.getElementById('tab-installation_video') || 
                                document.querySelector('.installation_video_tab') ||
                                document.querySelector('.woocommerce-tabs');

            if (targetPanel) {
                // Eğer sekme kapalıysa görünür yap
                targetPanel.style.display = 'block';

                const targetOffset = targetPanel.getBoundingClientRect().top + window.pageYOffset - 90;
                window.scrollTo({
                    top: targetOffset,
                    behavior: 'smooth'
                });

                targetPanel.classList.add('pv-tab-highlight');
                setTimeout(() => {
                    targetPanel.classList.remove('pv-tab-highlight');
                }, 1600);
            }
        });
    }
}


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mis360Init);
} else {
    mis360Init();
}
