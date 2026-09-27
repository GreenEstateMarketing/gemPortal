(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('gemHeaderToggle');
        var nav = document.getElementById('gemHeaderNav');

        if (toggle && nav) {
            toggle.addEventListener('click', function () {
                var isOpen = nav.classList.toggle('is-open');
                toggle.classList.toggle('is-open', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        var accountToggle = document.getElementById('gemHeaderAccountToggle');
        var accountMenu = document.getElementById('gemHeaderAccountMenu');

        if (accountToggle && accountMenu) {
            accountToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var isOpen = accountMenu.classList.toggle('is-open');
                accountToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', function (event) {
                if (!accountMenu.contains(event.target) && event.target !== accountToggle) {
                    accountMenu.classList.remove('is-open');
                    accountToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Show/hide toggle for password fields on the auth pages
        // (partials/auth-shell-open.blade.php). Delegated here, in an
        // externally-loaded script, rather than an inline <script> in the
        // page content - the theme's default layout wraps page content in
        // <div id="app">, which a (pre-existing, unrelated) Vue instance
        // tries to compile as a template; an inline <script> tag inside
        // that div makes Vue's compiler throw a console warning.
        document.addEventListener('click', function (event) {
            var toggle = event.target.closest('[data-toggle-password]');
            if (!toggle) {
                return;
            }
            var input = document.getElementById(toggle.getAttribute('data-toggle-password'));
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        });
    });
})();
