/*
    Path in theme: platform/themes/YOUR_THEME/public/js/new-home-page/how-it-works.js
    Enqueue near the end of body (after the how-it-works section's markup):

    <script src="{{ Theme::asset()->url('js/new-home-page/how-it-works.js') }}"></script>
*/
document.addEventListener('DOMContentLoaded', function () {
    var toggleButtons = document.querySelectorAll('.how-it-works__toggle-btn');
    if (!toggleButtons.length) return;

    toggleButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var panelName = btn.getAttribute('data-panel');

            toggleButtons.forEach(function (b) {
                var isActive = b === btn;
                b.classList.toggle('active', isActive);
                b.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            document.querySelectorAll('.how-it-works__panel').forEach(function (panel) {
                panel.classList.toggle('active', panel.getAttribute('data-panel') === panelName);
            });
        });
    });
});
