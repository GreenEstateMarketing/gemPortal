/*
    Path in theme: platform/themes/YOUR_THEME/public/js/home-page-new/header.js
    Enqueue in the header partial (add before </head> or before </body>):

    <script src="{{ Theme::asset()->url('js/home-page-new/header.js') }}"></script>
*/
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('mainNavToggle');
    var menu = document.getElementById('mainNavMenu');

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            var isOpen = menu.classList.toggle('is-open');
            toggle.classList.toggle('is-active', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    // Allow tapping a parent menu item to expand its submenu on mobile
    document.querySelectorAll('.main-nav__menu li').forEach(function (li) {
        var submenu = li.querySelector('ul');
        if (!submenu) return;

        var link = li.querySelector('a');
        link.addEventListener('click', function (e) {
            if (window.innerWidth <= 860) {
                e.preventDefault();
                li.classList.toggle('is-open');
            }
        });
    });

    // NOTE: "More Filters" (.advanced-search-toggler) needs NO handler here.
    // js/app.js (loaded globally, every page) already has one:
    //   $(document).on('click', '.advanced-search-toggler', function () {
    //       // adds .active to property-advanced-search OR project-advanced-
    //       // search, whichever matches the current Buy/Rent/Projects tab,
    //       // and removes it from the other
    //   });
    // and css/theme-css.css shows/hides based on that .active class
    // (.advanced-search-content.active { display:block }). An earlier
    // version of this file reinvented that as a separate "is-open" class
    // shown via :not(.d-none) - which doesn't know about .active at all,
    // so it revealed BOTH panels regardless of which tab was selected
    // (surfacing e.g. the Projects tab's own Price widget while on Buy/
    // Rent). Removed - header.css now styles off the real .active class.

    // The real-estate plugin's search JS (js/scripts.js) binds its
    // category-popover open/close handler only to the literal
    // #propertydropdownMenuLink element, which this design keeps empty and
    // zero-size (the visible label lives in the sibling .category_id_text
    // span). Forward clicks anywhere on the trigger to that element so the
    // whole control - label, arrow, empty space - opens the popover.
    //
    // scripts.js also has a document-wide "click outside closes the
    // popover" handler - $("body").on("click", fn) with no exemption for
    // the trigger itself, only for the popover's own contents. Any click
    // that bubbles up to <body> and isn't inside .property-category-
    // search-dropdown hides it. Without stopping propagation here, the
    // real click that opens the trigger keeps bubbling past this
    // listener, reaches <body>, and immediately hides the popover again
    // in that same click - it opens and closes within one event, so a
    // real mouse click never sees it stay open (an automated `.click()`
    // in devtools can look like it "worked" because scripts.js's own
    // click handlers don't check visibility before acting, but a human
    // clicking with a mouse can't select something that's already gone).
    document.querySelectorAll('.hero-search-card__field--type .hero-search-card__dropdown-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (e.target.id === 'propertydropdownMenuLink') return;
            var toggle = document.getElementById('propertydropdownMenuLink');
            if (toggle) {
                toggle.dispatchEvent(new MouseEvent('click', { bubbles: false, cancelable: true }));
            }
        });
    });
});