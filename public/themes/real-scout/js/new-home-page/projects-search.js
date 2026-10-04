/*
    Path in theme: platform/themes/real-scout/public/js/new-home-page/projects-search.js
    Loaded via:    config.php's beforeRenderTheme, gated on route name "public.projects".

    Makes the hero search form's Location field a searchable dropdown.
    Deliberately NOT reusing #city_id (the id homechoosen.js already
    select2-ifies for the home/properties search bar) - that script also
    wires up a change handler that calls ajax/get-city-areas and expects
    #chipContainer/#autocomplete-ajax to exist, none of which this page
    has. Own id (#projects-city-select) + own minimal select2() call avoids
    pulling any of that in.
*/
(function ($) {
    $(document).ready(function () {
        var $select = $('#projects-city-select');

        if (!$select.length || typeof $select.select2 !== 'function') {
            return;
        }

        // The select already has its own "All locations" option as the
        // first entry, so no separate select2 placeholder/allowClear is
        // needed - it behaves like any other selectable option.
        $select.select2({
            width: '100%',
        });
    });
})(jQuery);
