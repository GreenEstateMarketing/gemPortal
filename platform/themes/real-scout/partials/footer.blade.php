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
@php
    $footerCategoryIds = \Botble\RealEstate\Models\Category::query()
        ->whereIn('name', ['House', 'Flat', 'COMMERCIAL', 'PLOTS'])
        ->pluck('id', 'name');

    $footerAboutPage = app(\Botble\Page\Repositories\Interfaces\PageInterface::class)->getFirstBy(['name' => 'About us']);
    $footerAboutSlug = $footerAboutPage
        ? app(\Botble\Slug\Repositories\Interfaces\SlugInterface::class)->getFirstBy([
            'reference_id' => $footerAboutPage->id,
            'reference_type' => \Botble\Page\Models\Page::class,
        ])
        : null;
    $footerAboutUrl = $footerAboutSlug ? url($footerAboutSlug->key) : '#';

    $footerContactPage = app(\Botble\Page\Repositories\Interfaces\PageInterface::class)->getFirstBy(['name' => 'Contact']);
    $footerContactSlug = $footerContactPage
        ? app(\Botble\Slug\Repositories\Interfaces\SlugInterface::class)->getFirstBy([
            'reference_id' => $footerContactPage->id,
            'reference_type' => \Botble\Page\Models\Page::class,
        ])
        : null;
    $footerContactUrl = $footerContactSlug ? url($footerContactSlug->key) : '#';

    $footerWhatsapp = preg_replace('/\D/', '', (string) theme_option('hotline'));
@endphp
<footer class="site-footer">
    <div class="container site-footer__inner">

        <div class="site-footer__grid">
            <div class="site-footer__col site-footer__col--brand">
                <a href="{{ route('public.index') }}" class="site-footer__logo">
                    @if (theme_option('logo'))
                        <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}">
                    @else
                        {{ theme_option('site_title') }}
                    @endif
                </a>

                <p class="site-footer__description">{{ theme_option('seo_description') }}</p>

                <div class="site-footer__socials">
                    @if (theme_option('facebook'))
                        <a href="{{ theme_option('facebook') }}" target="_blank" rel="noopener" class="site-footer__social">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if (theme_option('instagram'))
                        <a href="{{ theme_option('instagram') }}" target="_blank" rel="noopener" class="site-footer__social">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                    @if (theme_option('linkedin'))
                        <a href="{{ theme_option('linkedin') }}" target="_blank" rel="noopener" class="site-footer__social">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    @endif
                    @if ($footerWhatsapp)
                        <a href="https://wa.me/{{ $footerWhatsapp }}" target="_blank" rel="noopener" class="site-footer__social">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="site-footer__col">
                <h4 class="site-footer__heading">{{ __('Quick Links') }}</h4>
                <ul class="site-footer__links">
                    <li><a href="{{ route('public.index') }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('public.properties') }}">{{ __('Properties') }}</a></li>
                    <li><a href="{{ route('public.index') }}#why-choose-gem">{{ __('Why Choose GEM') }}</a></li>
                    <li><a href="{{ route('public.index') }}#how-it-works">{{ __('How It Works') }}</a></li>
                    <li><a href="{{ $footerAboutUrl }}">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('public.agent.list') }}">{{ __('Our Agents') }}</a></li>
                </ul>
            </div>

            <div class="site-footer__col">
                <h4 class="site-footer__heading">{{ __('Property Types') }}</h4>
                <ul class="site-footer__links">
                    <li>
                        <a href="{{ route('public.properties', ['category_id' => $footerCategoryIds->get('House')]) }}">
                            {{ __('Houses') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.properties', ['category_id' => $footerCategoryIds->get('Flat')]) }}">
                            {{ __('Apartments') }}
                        </a>
                    </li>
                    <li><a href="{{ route('public.properties') }}">{{ __('Villas') }}</a></li>
                    <li>
                        <a href="{{ route('public.properties', ['category_id' => $footerCategoryIds->get('COMMERCIAL')]) }}">
                            {{ __('Commercial') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.properties', ['category_id' => $footerCategoryIds->get('PLOTS')]) }}">
                            {{ __('Plots & Land') }}
                        </a>
                    </li>
                    <li><a href="{{ route('wanted') }}">{{ __('Wanted Properties') }}</a></li>
                </ul>
            </div>

            <div class="site-footer__col">
                <h4 class="site-footer__heading">{{ __('Contact Us') }}</h4>
                <ul class="site-footer__contact">
                    @if (theme_option('address'))
                        <li><i class="fas fa-map-marker-alt"></i> {{ theme_option('address') }}</li>
                    @endif
                    @if (theme_option('hotline'))
                        <li><a href="tel:{{ theme_option('hotline') }}"><i class="fas fa-phone"></i> {{ theme_option('hotline') }}</a></li>
                    @endif
                    @if (theme_option('email'))
                        <li><a href="mailto:{{ theme_option('email') }}"><i class="fas fa-envelope"></i> {{ theme_option('email') }}</a></li>
                    @endif
                    <li><i class="fas fa-clock"></i> {{ __('Mon - Sat: 9:00 AM - 6:00 PM') }}</li>
                </ul>

                {{-- No newsletter plugin/route exists yet - redirect to Contact Us with the email prefilled instead. --}}
                <form class="site-footer__newsletter" action="{{ $footerContactUrl }}" method="get">
                    <input type="email" name="email" placeholder="{{ __('Your email address') }}" class="site-footer__newsletter-input" required>
                    <button type="submit" class="site-footer__newsletter-btn"><i class="fas fa-arrow-right"></i></button>
                </form>
            </div>
        </div>

    </div>
</footer>

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
