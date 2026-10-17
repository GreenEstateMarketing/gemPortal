{{--
    Shared site footer, included via {!! Theme::partial('footer') !!} from every
    layout (default, homepage, homepagenew, realscouthomepage) so the whole
    public site renders one footer design from one file.

    Dynamic data used wherever it actually exists in Theme Options:
    - Logo: theme_option('logo') / site_title (same pattern as the header).
    - Description: theme_option('seo_description') - there's no separate
      "footer description" option, this is the closest real, non-empty
      piece of company copy.
    - Address / phone / email: theme_option('address') / ('hotline') / ('email').
    - Social links: theme_option('facebook') / ('linkedin') are set;
      ('instagram') is empty in Theme Options right now, so that icon is
      hidden rather than linking to "#" - add a real URL there to have it
      appear. WhatsApp isn't a theme option at all, built from the hotline
      number instead (same wa.me pattern used on the agent cards).

    DATA GAPS:
    - No "working hours" theme option exists - falls back to the design's
      static text ("Mon - Sat: 9:00 AM - 6:00 PM"). Add a real option later
      if this should be editable.
    - No newsletter plugin/route exists, so the "newsletter" input is a
      plain GET form that redirects to the Contact Us page with the
      entered address prefilled into its email field (see
      partials/short-codes/contact-form.blade.php). Swap this out for a
      real subscribe endpoint once one exists.

    "Property Types" links point to route('public.properties',
    ['category_id' => ...]) using REAL category ids looked up by name
    (House, Flat, and the COMMERCIAL/PLOTS parent categories all exist).
    "Villas" has no matching category in the database at all, so it links
    to the plain properties page instead of a fabricated category id.
--}}
{!! Theme::partial('footer-content') !!}

<script>
    window.trans = {
        "Price": "{{ __('Price') }}",
        "Number of rooms": "{{ __('Number of rooms') }}",
        "Number of rest rooms": "{{ __('Number of rest rooms') }}",
        "Square": "{{ __('Square') }}",
        "No property found": "{{ __('No property found') }}",
        "million": "{{ __('million') }}",
        "billion": "{{ __('billion') }}",
        "in": "{{ __('in') }}",
        "Added to wishlist successfully!": "{{ __('Added to wishlist successfully!') }}",
        "Removed from wishlist successfully!": "{{ __('Removed from wishlist successfully!') }}",
        "I care about this property!!!": "{{ __('I care about this property!!!') }}",
    }
    window.themeUrl = '{{ Theme::asset()->url('') }}';
    window.siteUrl = '{{ url('') }}';
    window.currentLanguage = '{{ App::getLocale() }}';
    //////////////select category change//////////
    $(".select-category").change(function () {
        var value = $(this).val();
        switch (value) {
            case '1':  //apartment
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '2': //villa
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '3': //condo
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '4': //house
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '5': //land
                $(".bedrooms").hide();
                $(".bathrooms").hide();
                $(".floors").hide();
                $(".prices").removeClass("mt-3");
                break;
            default: //commercial property
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");

        }
    });
    $("#submitBtn").click(function () {
        // executes when complete page is fully loaded, including all frames, objects and images
        var type = $("#txttypesearch").val();
        if (type == "project") {
            $(".property-advanced-search :input").prop("disabled", true);
            $('div.property-advanced-search').find('select').hide();
            $(".project-advanced-search :input").prop("disabled", false);
        } else if (type == "sale") {
            $(".property-advanced-search :input").prop("disabled", false);
            $(".project-advanced-search :input").prop("disabled", true);
            $('div.property-advanced-search').find('select').show();
        } else if (type == "rent") {
            $(".property-advanced-search :input").prop("disabled", false);
            $(".project-advanced-search :input").prop("disabled", true);
            $('div.project-advanced-search').find('select').show();
        } else {
        }
        var myForm = document.getElementById('frmhomesearch');

        chipArray.forEach(function (value) {
            var hiddenInput = document.createElement('input');

            hiddenInput.type = 'hidden';
            hiddenInput.name = 'k[]';
            hiddenInput.value = JSON.stringify(value);
            myForm.appendChild(hiddenInput);
        });
    });
    $(".typesearch a").click(function () {

        $('[data-dropdown-id="price-max"]').val("");
        $('[data-dropdown-id="price-min"]').val("");
        $('[data-dropdown-id="price-max"]').attr("placeholder", "Max");
        $('[data-dropdown-id="price-min"]').attr("placeholder", "Min");
        $('.min_price_text').html("0");
        $('.max_price_text').html("Any");

        //change for units as well
        $('[data-dropdown-id="unit-max"]').val("");
        $('[data-dropdown-id="unit-min"]').val("");
        $('[data-dropdown-id="unit-max"]').attr("placeholder", "Max");
        $('[data-dropdown-id="unit-min"]').attr("placeholder", "Min");
        $('.min_unit_text').html("0");
        $('.max_unit_text').html("Any");
    });
    window.addEventListener("pageshow", function (event) {
        var historyTraversal = event.persisted ||
            (typeof window.performance != "undefined" &&
                window.performance.navigation.type === 2);
        if (historyTraversal) {
            // Handle page restore.
            window.location.reload();
        }
    });
</script>

{!! Theme::footer() !!}
</body>

</html>
