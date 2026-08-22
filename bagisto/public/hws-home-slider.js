(function () {
    'use strict';

    if (document.documentElement.getAttribute('data-hws-hero-bound') === 'true') {
        return;
    }

    document.documentElement.setAttribute('data-hws-hero-bound', 'true');

    var current = 0;
    var paused = false;
    var lastChange = Date.now();
    var pointerStart = null;

    function getParts() {
        var root = document.getElementById('hwsHeroSlider');

        if (! root) {
            return null;
        }

        var track = root.querySelector('.hws-hero-slider__track');
        var slides = Array.prototype.slice.call(root.querySelectorAll('.hws-hero-slider__slide'));
        var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hws-hero-slide]'));

        if (! track || slides.length < 2 || dots.length !== slides.length) {
            return null;
        }

        return { root: root, track: track, slides: slides, dots: dots };
    }

    function render(index) {
        var parts = getParts();

        if (! parts) {
            return;
        }

        current = ((index % parts.slides.length) + parts.slides.length) % parts.slides.length;
        parts.track.style.transform = 'translate3d(-' + (current * 100) + '%, 0, 0)';
        parts.root.setAttribute('data-slider-ready', 'true');

        parts.slides.forEach(function (slide, slideIndex) {
            slide.setAttribute('aria-hidden', slideIndex === current ? 'false' : 'true');
        });

        parts.dots.forEach(function (dot, dotIndex) {
            var active = dotIndex === current;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function goTo(index) {
        lastChange = Date.now();
        render(index);
    }

    document.addEventListener('click', function (event) {
        var next = event.target.closest('[data-hws-hero-next]');
        var previous = event.target.closest('[data-hws-hero-prev]');
        var dot = event.target.closest('[data-hws-hero-slide]');

        if (next) {
            event.preventDefault();
            goTo(current + 1);
        } else if (previous) {
            event.preventDefault();
            goTo(current - 1);
        } else if (dot) {
            event.preventDefault();
            goTo(Number(dot.getAttribute('data-hws-hero-slide')));
        }
    }, true);

    document.addEventListener('pointerdown', function (event) {
        if (event.target.closest('#hwsHeroSlider')) {
            pointerStart = event.clientX;
        }
    }, true);

    document.addEventListener('pointerup', function (event) {
        if (pointerStart === null || ! event.target.closest('#hwsHeroSlider')) {
            pointerStart = null;
            return;
        }

        var movement = event.clientX - pointerStart;
        pointerStart = null;

        if (Math.abs(movement) > 45) {
            goTo(current + (movement < 0 ? 1 : -1));
        }
    }, true);

    document.addEventListener('mouseover', function (event) {
        if (event.target.closest('#hwsHeroSlider')) {
            paused = true;
        }
    });

    document.addEventListener('mouseout', function (event) {
        var root = document.getElementById('hwsHeroSlider');

        if (root && ! root.contains(event.relatedTarget)) {
            paused = false;
            lastChange = Date.now();
        }
    });

    document.addEventListener('focusin', function (event) {
        if (event.target.closest('#hwsHeroSlider')) {
            paused = true;
        }
    });

    document.addEventListener('focusout', function (event) {
        var root = document.getElementById('hwsHeroSlider');

        if (root && ! root.contains(event.relatedTarget)) {
            paused = false;
            lastChange = Date.now();
        }
    });

    window.setInterval(function () {
        if (! paused && ! document.hidden && Date.now() - lastChange >= 5000) {
            goTo(current + 1);
        }
    }, 500);

    render(0);
    window.setTimeout(function () { render(current); }, 1000);
})();
