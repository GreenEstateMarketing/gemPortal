/*
    Path in plugin: platform/plugins/real-estate/public/js/account-signature.js
    Served copy:    public/vendor/core/plugins/real-estate/js/account-signature.js (keep both in sync manually)

    Vanilla JS, no framework - same style as
    platform/themes/real-scout/public/js/wizard/property-wizard.js.
    Handles: Upload/Draw tab switching, a hand-written canvas signature pad
    (pointer/touch/mouse -> smoothed line -> PNG data URI), the Clear
    button, and a client-side mirror of the server's "required unless
    already on file" rule.
*/
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var widget = document.getElementById('signature-widget');
        if (!widget) {
            return;
        }

        var hasExisting = widget.getAttribute('data-existing') === '1';
        var tabs = widget.querySelectorAll('.signature-tab');
        var panes = widget.querySelectorAll('.signature-pane');
        var fileInput = document.getElementById('signature_file');
        var dataInput = document.getElementById('signature_data');
        var modeInput = document.getElementById('signature_mode');
        var errorBox = document.getElementById('signature-error');
        var canvas = document.getElementById('signature-canvas');
        var clearBtn = document.getElementById('signature-clear');
        var form = document.getElementById('setting-form');

        var ctx = canvas.getContext('2d');
        var drawing = false;
        var hasStroke = false;
        var lastX = 0;
        var lastY = 0;

        resetCanvas();

        function resetCanvas() {
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#111';
            hasStroke = false;
        }

        function pointerPos(evt) {
            var rect = canvas.getBoundingClientRect();
            var point = evt.touches && evt.touches.length ? evt.touches[0] : evt;
            return {
                x: (point.clientX - rect.left) * (canvas.width / rect.width),
                y: (point.clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function startDraw(evt) {
            evt.preventDefault();
            drawing = true;
            var p = pointerPos(evt);
            lastX = p.x;
            lastY = p.y;
        }

        function moveDraw(evt) {
            if (!drawing) {
                return;
            }
            evt.preventDefault();
            var p = pointerPos(evt);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            lastX = p.x;
            lastY = p.y;
            hasStroke = true;
        }

        function endDraw(evt) {
            if (!drawing) {
                return;
            }
            if (evt) {
                evt.preventDefault();
            }
            drawing = false;
            syncCanvasToHiddenField();
        }

        function syncCanvasToHiddenField() {
            dataInput.value = hasStroke ? canvas.toDataURL('image/png') : '';
        }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', moveDraw);
        window.addEventListener('mouseup', endDraw);

        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', moveDraw, { passive: false });
        canvas.addEventListener('touchend', endDraw);

        clearBtn.addEventListener('click', function () {
            resetCanvas();
            dataInput.value = '';
            errorBox.textContent = '';
        });

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var mode = tab.getAttribute('data-mode');

                tabs.forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');

                panes.forEach(function (pane) {
                    pane.hidden = pane.getAttribute('data-pane') !== mode;
                });

                modeInput.value = mode;

                // Only the active mode's data should be submitted.
                if (mode === 'upload') {
                    dataInput.value = '';
                } else {
                    fileInput.value = '';
                }

                errorBox.textContent = '';
            });
        });

        fileInput.addEventListener('change', function () {
            errorBox.textContent = '';
        });

        form.addEventListener('submit', function (evt) {
            var hasFile = fileInput.files && fileInput.files.length > 0;
            var hasData = !!dataInput.value;

            if (!hasFile && !hasData && !hasExisting) {
                evt.preventDefault();
                errorBox.textContent = 'Please upload or draw your signature before saving.';
                widget.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
})();
