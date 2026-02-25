(function () {
    'use strict';

    // Mobile nav toggle
    var hamburger = document.querySelector('.hamburger');
    var overlay = document.querySelector('.nav-overlay');
    var closeBtn = document.querySelector('.nav-overlay-close');
    var body = document.body;

    function openMenu() {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        hamburger.setAttribute('aria-expanded', 'true');
        hamburger.classList.add('is-active');
        body.style.overflow = 'hidden';
    }

    function closeMenu() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.classList.remove('is-active');
        body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (overlay.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeMenu);
    }

    // Close on overlay link click
    if (overlay) {
        var links = overlay.querySelectorAll('a');
        for (var i = 0; i < links.length; i++) {
            links[i].addEventListener('click', closeMenu);
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) {
            closeMenu();
        }
    });

    // Sticky nav + back to top
    var navBar = document.querySelector('.nav-bar');
    var backToTop = document.querySelector('.back-to-top');
    var headerLogo = document.querySelector('.header-logo');

    window.addEventListener('scroll', function () {
        var scrolled = window.scrollY > 100;

        if (navBar) {
            if (scrolled) {
                navBar.classList.add('is-sticky');
            } else {
                navBar.classList.remove('is-sticky');
            }
        }

        if (backToTop) {
            if (window.scrollY > 300) {
                backToTop.classList.add('is-visible');
            } else {
                backToTop.classList.remove('is-visible');
            }
        }
    });

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
