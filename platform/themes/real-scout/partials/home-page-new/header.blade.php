{{--
    Path in theme:  platform/themes/YOUR_THEME/partials/home-page-new/header.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/header') !!}

    This partial opens <html>, <head> and <body> since it's the first thing
    rendered on the home page. Whichever partial you render last on this page
    (e.g. a footer partial) needs to close </body></html>.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />

    {{-- Theme::header() outputs the <title>, SEO meta tags, and the theme's registered CSS/JS.
         Do not add a separate <title> tag here - it's already handled below. --}}
    {!! Theme::header() !!}

    {{-- Header-specific stylesheet for this design --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/header.css') }}">

    {{-- "Why Choose GEM" section stylesheet - relies on the color/font
         custom properties (--header-navy, --header-gold, etc.) defined
         on :root in header.css above, so header.css must load first. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/why-choose.css') }}">

    {{-- "How It Works" section stylesheet - same dependency on header.css's
         :root custom properties. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/how-it-works.css') }}">

    {{-- "About Us" section stylesheet - same dependency on header.css's
         :root custom properties. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/about-us.css') }}">

    {{-- "Search By Property Type" section stylesheet - same dependency on
         header.css's :root custom properties (and reuses --how-bg from
         how-it-works.css, with its own fallback value). --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/property-categories.css') }}">

    {{-- "Meet Our Expert Agents" section stylesheet - same dependencies. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/meet-agents.css') }}">

    {{-- "What Our Clients Say" section stylesheet - same dependencies. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/testimonials.css') }}">

    {{-- "Ready To Make Your Move" CTA section - same dependencies, also
         reuses the shared .btn-outline class defined above. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/cta-move.css') }}">

    {{-- Site footer stylesheet - same dependencies. --}}
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/home-page-new/site-footer.css') }}">

    {{-- Display fonts used in the hero headline / nav text. Swap for theme_option('primary_font')
         if you'd rather keep this on the same font system as the rest of the theme. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif class="home-page-new">

<header class="site-header">

    {{-- ============ TOP UTILITY BAR ============ --}}
    <div class="top-bar">
        <div class="container top-bar__inner">
            <div class="top-bar__left">
                <span class="top-bar__item">
                    <i class="icon-location"></i>
                    Your Trusted Real Estate Partner
                </span>
            </div>
            <div class="top-bar__right">
                @if (theme_option('hotline'))
                    <a href="tel:{{ theme_option('hotline') }}" class="top-bar__item">
                        <i class="icon-phone"></i>
                        {{ theme_option('hotline') }}
                    </a>
                @endif
                @if (theme_option('email'))
                    <a href="mailto:{{ theme_option('email') }}" class="top-bar__item">
                        <i class="icon-mail"></i>
                        {{ theme_option('email') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ MAIN NAVIGATION ============ --}}
    <div class="main-nav">
        <div class="container main-nav__inner">

            <a href="{{ route('public.single') }}" class="main-nav__logo">
                @if (theme_option('logo'))
                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" class="main-nav__logo-img"
                        height="40" alt="{{ theme_option('site_title') }}">
                @else
                    <i class="icon-logo"></i>
                    <span class="main-nav__logo-text">
                        {{ theme_option('site_title') }}
                    </span>
                @endif
            </a>

            <button class="main-nav__toggle" id="mainNavToggle" aria-label="Toggle navigation" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="main-nav__menu" id="mainNavMenu">
                {!! Menu::renderMenuLocation('main-menu', [
                    'options' => ['class' => ''],
                    'view' => 'main-menu',
                ]) !!}

                {{-- .main-nav__actions (the login/register/account controls) is
                     hidden entirely below 860px with nothing standing in for it,
                     so mobile visitors had no way to log in, register, or reach
                     their account menu at all. This is the same auth UI, shown
                     only on mobile via CSS, living inside the slide-out drawer
                     where it's actually reachable. --}}
                <div class="main-nav__mobile-actions">
                    {!! Theme::partial('home-page-new/nav-auth', ['suffix' => 'mobile']) !!}
                </div>
            </nav>

            <div class="main-nav__actions">
                {{-- js/scripts.js's scroll handler reads #login_check to decide whether
                     to reveal the main menu's "Login" item (tagged with the "sticky-
                     login" CSS class in the menu builder, rendered server-side as
                     .nav-item.sticky-login.d-none by default). Without this input the
                     check falls through to "not logged in" every time and the scroll
                     handler removes .d-none - the login link would pop up in the menu
                     on scroll even for a logged-in member. That item is fully
                     redundant here regardless (see .sticky-login in header.css,
                     forced hidden always) since this bar is never hidden on scroll
                     the way the old page's nav was, but #login_check is kept anyway
                     in case anything else in scripts.js ever reads it. --}}
                @if (auth('account')->check())
                    <input type="hidden" id="login_check" value="1" />
                @elseif (auth('member')->check())
                    <input type="hidden" id="login_check" value="1" />
                @else
                    <input type="hidden" id="login_check" value="0" />
                @endif
                {!! Theme::partial('home-page-new/nav-auth', ['suffix' => 'desktop']) !!}
            </div>

        </div>
    </div>

    {{-- ============ HERO SECTION ============ --}}
    <div class="hero">
        <div class="hero__overlay"></div>
        <div class="container hero__inner">

            <div class="hero__content">
                <span class="hero__eyebrow">
                    <i class="icon-home"></i>
                    Find Your Perfect Place
                </span>
                <h1 class="hero__heading">
                    Find a Place<br>
                    <span class="hero__heading--accent">You'll Love to Call Home.</span>
                </h1>
                <p class="hero__text">
                    Discover premium homes, apartments, commercial properties and plots with GEM
                    Properties. Your trusted partner for buying, selling and renting real estate.
                </p>
                <div class="hero__actions">
                    <a href="{{ route('public.properties') }}" class="btn-primary">
                        Explore Properties
                        <i class="icon-arrow-right"></i>
                    </a>
                    <a href="{{ route('public.agent.search') }}" class="btn-outline">Talk to an Agent</a>
                </div>
            </div>

            <div class="hero__search-wrapper">
                {!! Theme::partial('home-page-new/search-bar') !!}
            </div>

        </div>
    </div>

</header>