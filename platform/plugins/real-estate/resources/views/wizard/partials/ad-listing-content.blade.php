<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <canvas class="wizard-confetti" data-confetti-canvas></canvas>

    <div class="wizard-celebration">
        <div class="wizard-celebration__icon">🎉</div>
        <span class="property-wizard__eyebrow">{{ __('Step 6 of 6') }}</span>
        <h1 class="wizard-celebration__title">{{ __('Congratulations!') }}</h1>
        <p class="wizard-celebration__property">{{ $property->name }}</p>
        <p class="wizard-celebration__message">
            {{ __('Your property is finally listed successfully! It\'s now live on GEM Listing for everyone to see. Thank you for completing every step along the way.') }}
        </p>

        <div class="wizard-celebration__actions">
            <a href="{{ $dashboardUrl }}" class="wizard-btn wizard-btn--primary">
                {{ __('Back to My Properties') }} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var canvas = document.querySelector('[data-confetti-canvas]');

    if (!canvas || !canvas.getContext) {
        return;
    }

    var ctx = canvas.getContext('2d');
    var colors = ['#e0a63e', '#1a1d24', '#2f9e44', '#c0392b', '#ffffff'];
    var particles = [];
    var particleCount = 160;
    var animationFrame = null;
    var startTime = null;
    var duration = 4500;

    function resize() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }

    function createParticles() {
        particles = [];
        for (var i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: -20 - Math.random() * canvas.height * 0.5,
                size: 6 + Math.random() * 6,
                color: colors[Math.floor(Math.random() * colors.length)],
                speedY: 2 + Math.random() * 3,
                speedX: (Math.random() - 0.5) * 2,
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 10,
                shape: Math.random() > 0.5 ? 'rect' : 'circle'
            });
        }
    }

    function drawParticle(p) {
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rotation * Math.PI) / 180);
        ctx.fillStyle = p.color;
        if (p.shape === 'rect') {
            ctx.fillRect(-p.size / 2, -p.size / 4, p.size, p.size / 2);
        } else {
            ctx.beginPath();
            ctx.arc(0, 0, p.size / 2, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.restore();
    }

    function tick(timestamp) {
        if (!startTime) {
            startTime = timestamp;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        particles.forEach(function (p) {
            p.x += p.speedX;
            p.y += p.speedY;
            p.rotation += p.rotationSpeed;
            drawParticle(p);
        });

        if (timestamp - startTime < duration) {
            animationFrame = requestAnimationFrame(tick);
        } else {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    function start() {
        resize();
        createParticles();
        startTime = null;
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
        }
        animationFrame = requestAnimationFrame(tick);
    }

    window.addEventListener('resize', resize);
    start();
})();
</script>
