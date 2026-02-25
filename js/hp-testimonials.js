(function () {
    'use strict';

    var carousel = document.querySelector('.testimonial-carousel');
    if (!carousel) return;

    var slides = carousel.querySelectorAll('.testimonial-slide');
    var dots = carousel.querySelectorAll('.testimonial-dot');
    var prevBtn = carousel.querySelector('.testimonial-prev');
    var nextBtn = carousel.querySelector('.testimonial-next');
    var current = 0;
    var total = slides.length;
    var interval = null;
    var delay = 6000;

    function goTo(index) {
        slides[current].classList.remove('is-active');
        dots[current].classList.remove('is-active');
        current = (index + total) % total;
        slides[current].classList.add('is-active');
        dots[current].classList.add('is-active');
    }

    function next() {
        goTo(current + 1);
    }

    function prev() {
        goTo(current - 1);
    }

    function startAuto() {
        interval = setInterval(next, delay);
    }

    function resetAuto() {
        clearInterval(interval);
        startAuto();
    }

    nextBtn.addEventListener('click', function () {
        next();
        resetAuto();
    });

    prevBtn.addEventListener('click', function () {
        prev();
        resetAuto();
    });

    for (var i = 0; i < dots.length; i++) {
        dots[i].addEventListener('click', function () {
            goTo(parseInt(this.getAttribute('data-index'), 10));
            resetAuto();
        });
    }

    // Pause on hover.
    carousel.addEventListener('mouseenter', function () {
        clearInterval(interval);
    });

    carousel.addEventListener('mouseleave', function () {
        startAuto();
    });

    startAuto();
})();
