{{--
    The <footer> markup itself, shared between the public site's footer
    (partials/footer.blade.php, which wraps this with the trailing
    window.trans script block and the </body></html> that closes the public
    layout) and the member/agent dashboard layouts, which include this same
    markup via {!! Theme::partial('footer-content') !!} so the dashboard
    shows the identical footer design without pulling in the public footer's
    script block (that block overwrites window.trans with public-site
    strings, which would clobber the dashboard's own window.trans) or its
    closing body/html tags (the dashboard layouts close those themselves).

    See footer.blade.php for notes on the dynamic data used below.
--}}
@php
    $footerCategoryIds = \Botble\RealEstate\Models\Category::query()
        ->whereIn('name', ['House', 'Flat', 'COMMERCIAL', 'PLOTS'])
        ->pluck('id', 'name');

    $footerPageNames = ['About us', 'Contact', 'Privacy Policy', 'Terms & Conditions', 'FAQ', 'Shipping/Delivery Policy', 'Disclaimer'];
    $footerPages = \Botble\Page\Models\Page::query()->whereIn('name', $footerPageNames)->pluck('id', 'name');
    $footerSlugs = \Botble\Slug\Models\Slug::query()
        ->where('reference_type', \Botble\Page\Models\Page::class)
        ->whereIn('reference_id', $footerPages->values())
        ->pluck('key', 'reference_id');
    $footerPageUrl = function (string $name) use ($footerPages, $footerSlugs) {
        $pageId = $footerPages->get($name);
        $slug = $pageId ? $footerSlugs->get($pageId) : null;
        return $slug ? url($slug) : '#';
    };

    $footerAboutUrl = $footerPageUrl('About us');
    $footerContactUrl = $footerPageUrl('Contact');
    $footerPrivacyUrl = $footerPageUrl('Privacy Policy');
    $footerTermsUrl = $footerPageUrl('Terms & Conditions');
    $footerFaqUrl = $footerPageUrl('FAQ');
    $footerShippingUrl = $footerPageUrl('Shipping/Delivery Policy');
    $footerDisclaimerUrl = $footerPageUrl('Disclaimer');

    $footerWhatsapp = preg_replace('/\D/', '', (string) theme_option('hotline'));
@endphp
<footer class="site-footer">
    <div class="container site-footer__inner">

        <div class="site-footer__grid">
            <div class="site-footer__col site-footer__col--brand">
                <a href="{{ route('public.index') }}" class="site-footer__logo">
                    @if (theme_option('logo'))
                        <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="Greens Estate Marketing (Private) Limited">
                    @else
                        Greens Estate Marketing (Private) Limited
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
                    <li><a href="{{ route('public.index') }}#how-it-works">{{ __('How It Works') }}</a></li>
                    <li><a href="{{ $footerAboutUrl }}">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('public.blog') }}">{{ __('Blog') }}</a></li>
                    <li><a href="{{ route('public.agent.list') }}">{{ __('Our Agents') }}</a></li>
                </ul>
            </div>

            <div class="site-footer__col">
                <h4 class="site-footer__heading">{{ __('Support') }}</h4>
                <ul class="site-footer__links">
                    <li><a href="#">{{ __('Pricing') }}</a></li>
                    <li><a href="{{ $footerContactUrl }}">{{ __('Contact') }}</a></li>
                    <li><a href="{{ $footerFaqUrl }}">{{ __('FAQ') }}</a></li>
                    <li><a href="{{ $footerPrivacyUrl }}">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="{{ $footerTermsUrl }}">{{ __('Terms and Conditions') }}</a></li>
                    <li><a href="{{ $footerShippingUrl }}">{{ __('Shipping/Delivery Policy') }}</a></li>
                    <li><a href="{{ $footerDisclaimerUrl }}">{{ __('Disclaimer') }}</a></li>
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

                <form class="site-footer__newsletter" action="{{ route('public.blog.subscribe') }}" method="post">
                    @csrf
                    <input type="email" name="email" placeholder="{{ __('Your email address') }}" class="site-footer__newsletter-input" required>
                    <button type="submit" class="site-footer__newsletter-btn"><i class="fas fa-arrow-right"></i></button>
                </form>
                @if (session('newsletter_subscribed') && session('success_msg'))
                    <p class="site-footer__newsletter-message">{{ session('success_msg') }}</p>
                @endif

                <p class="site-footer__copyright">&copy; {{ now()->year }} Greens Estate Marketing (Private) Limited. {{ __('All rights reserved.') }} &middot; v{{ config('app.version') }}</p>
            </div>
        </div>

    </div>
</footer>
