/*
    Path: /var/www/gem/public/js/agent-detail.js
    Loaded via Theme::asset()->container('footer')->add() in
    FlexHomeController::getAgent(), not the default container (never
    echoed by this theme's footer partial) and not an inline <script> tag
    in agent.blade.php itself (the view's markup renders inside the
    layout's #app div, which is a Vue mount — Vue's template compiler
    silently strips any <script> tag it finds there during compilation).

    Handles the agent profile page's interactivity: favorite-heart toggling
    on the property grid (same ajax endpoint and guest-redirect behavior as
    property.blade.php's inline handler) and the "Message" button's contact
    modal. The "Share" button reuses partials/share.blade.php, which wires
    up its own dropdown via Bootstrap's data-toggle="dropdown" markup — no
    JS needed here for it.
*/
(function () {
    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    onReady(function () {
        var root = document.querySelector('.agent-detail');
        if (!root) {
            return;
        }

        // ---- Favorite heart toggle ----
        root.addEventListener('click', function (e) {
            var button = e.target.closest('.add-to-wishlist');
            if (!button || !root.contains(button)) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            if (button.dataset.loading === 'true') {
                return;
            }

            var itemId = button.getAttribute('data-id');
            var currentState = button.getAttribute('data-is-favourite') === 'true';
            var newState = !currentState;

            button.dataset.loading = 'true';

            fetch('/ajax/toggle-favourite-property/' + itemId + '?is_favourite=' + newState, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (response) {
                    if (response.error && response.message === 'UserNotAuthenticated') {
                        window.location.href = '/member-login';
                        return;
                    }

                    if (response.error) {
                        return;
                    }

                    var icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('far', !newState);
                        icon.classList.toggle('fas', newState);
                    }
                    button.setAttribute('data-is-favourite', newState ? 'true' : 'false');
                })
                .finally(function () {
                    button.dataset.loading = 'false';
                });
        });

        // ---- Message modal ----
        var messageTrigger = document.getElementById('agentMessageTrigger');
        var modal = document.getElementById('agentMessageModal');
        var modalClose = document.getElementById('agentMessageModalClose');
        var modalOverlay = document.getElementById('agentMessageModalOverlay');

        function openModal() {
            if (modal) {
                modal.classList.add('is-open');
            }
        }

        function closeModal() {
            if (modal) {
                modal.classList.remove('is-open');
            }
        }

        if (messageTrigger) {
            messageTrigger.addEventListener('click', openModal);
        }
        if (modalClose) {
            modalClose.addEventListener('click', closeModal);
        }
        if (modalOverlay) {
            modalOverlay.addEventListener('click', closeModal);
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    });
})();
