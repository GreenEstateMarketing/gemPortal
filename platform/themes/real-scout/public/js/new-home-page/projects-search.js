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

        if ($select.length && typeof $select.select2 === 'function') {
            // The select already has its own "All locations" option as the
            // first entry, so no separate select2 placeholder/allowClear is
            // needed - it behaves like any other selectable option.
            $select.select2({
                width: '100%',
            });
        }

        // Per-field "x" clear buttons (hero-section.blade.php). Each just
        // empties that one field - nothing here submits the form, so a
        // cleared field simply won't be part of the query string the next
        // time the user clicks Search.
        $(document).on('click', '.projects-search-card__clear', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var target = $(this).data('clear-target');

            switch (target) {
                case 'name':
                    $('input[name="name"]').val('');
                    break;
                case 'city_id':
                    // #projects-city-select is select2-ified above (when
                    // available) - .trigger('change') makes it redraw its
                    // visible label back to "All locations" too, not just
                    // the underlying <select>'s value.
                    $('#projects-city-select').val('').trigger('change');
                    break;
                case 'category_id':
                    $('#projects-category-select').val('').trigger('change');
                    break;
            }
        });
    });
})(jQuery);
