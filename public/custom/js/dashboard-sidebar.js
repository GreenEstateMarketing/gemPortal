(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('dashboardSidebarToggle');
        var menu = document.getElementById('dashboardSidebarMenu');

        if (toggle && menu) {
            toggle.addEventListener('click', function () {
                var isOpen = menu.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }
    });
})();
