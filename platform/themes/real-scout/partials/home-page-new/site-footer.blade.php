{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/site-footer.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/site-footer') !!}
    Included from:  layouts/homepagenew.blade.php, right before home-page-new/footer
                     (which just closes </body></html> and outputs Theme::footer()).

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
    - The newsletter input has no backend at all (no newsletter plugin is
      active, no route exists) - it's UI only for now, the button does
      nothing on submit. Wire it up once there's somewhere for it to go.

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

                {{-- No newsletter plugin/route exists yet - UI only, submit does nothing. --}}
                <form class="site-footer__newsletter" onsubmit="return false;">
                    <input type="email" placeholder="{{ __('Your email address') }}" class="site-footer__newsletter-input">
                    <button type="submit" class="site-footer__newsletter-btn"><i class="fas fa-arrow-right"></i></button>
                </form>
            </div>
        </div>

    </div>
</footer>
