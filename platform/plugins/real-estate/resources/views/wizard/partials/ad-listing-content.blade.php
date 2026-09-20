<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <canvas class="wizard-confetti" data-confetti-canvas></canvas>

    <div class="wizard-celebration">
        <div class="wizard-celebration__icon">🎉</div>
        <span class="property-wizard__eyebrow">{{ $role === 'agent' ? __('Step 5 of 5') : __('Step 6 of 6') }}</span>
        <h1 class="wizard-celebration__title">{{ __('Congratulations!') }}</h1>
        <p class="wizard-celebration__property">{{ $property->name }}</p>
        <p class="wizard-celebration__message">
            {{ __('Your property is finally listed successfully! It\'s now live on GEM Listing for everyone to see. Thank you for completing every step along the way.') }}
        </p>

        <div class="wizard-celebration__link-card">
            <div class="wizard-celebration__link-label">
                <i class="fas fa-globe"></i> {{ __('Your listing is now public at') }}
            </div>
            <div class="wizard-celebration__link-row">
                <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="wizard-celebration__link">{{ $publicUrl }}</a>
                <button type="button" class="wizard-celebration__copy-btn" data-copy-link data-copy-value="{{ $publicUrl }}" data-copied-text="{{ __('Copied!') }}" title="{{ __('Copy link') }}">
                    <i class="fas fa-copy"></i> <span data-copy-label>{{ __('Copy') }}</span>
                </button>
            </div>
        </div>

        <div class="wizard-celebration__actions">
            <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="wizard-btn wizard-btn--primary">
                {{ __('View Public Listing') }} <i class="fas fa-arrow-up-right-from-square"></i>
            </a>
            <a href="{{ $dashboardUrl }}" class="wizard-btn wizard-btn--ghost">
                {{ __('Back to My Properties') }}
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

    var copyBtn = document.querySelector('[data-copy-link]');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var value = copyBtn.getAttribute('data-copy-value') || '';
            var label = copyBtn.querySelector('[data-copy-label]');
            var restore = label ? label.textContent : null;

            function showCopied() {
                if (!label) {
                    return;
                }
                label.textContent = copyBtn.getAttribute('data-copied-text') || 'Copied!';
                copyBtn.classList.add('wizard-celebration__copy-btn--copied');
                setTimeout(function () {
                    label.textContent = restore;
                    copyBtn.classList.remove('wizard-celebration__copy-btn--copied');
                }, 2000);
            }

            function fallbackCopy() {
                var temp = document.createElement('textarea');
                temp.value = value;
                temp.style.position = 'fixed';
                temp.style.opacity = '0';
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                showCopied();
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(value).then(showCopied, fallbackCopy);
            } else {
                fallbackCopy();
            }
        });
    }
})();
</script>
