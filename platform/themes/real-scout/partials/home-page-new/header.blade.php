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

    {{-- "Why Choose GEMlisting" section stylesheet - relies on the color/font
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

    {{-- Site footer stylesheet is now registered globally in config.php's
         beforeRenderTheme (so the footer renders the same on every public
         page), so it's loaded via Theme::header() above - no separate
         <link> needed here. --}}

    {{-- Display fonts used in the hero headline / nav text. Swap for theme_option('primary_font')
         if you'd rather keep this on the same font system as the rest of the theme. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif class="home-page-new">

<header class="site-header">

    {!! Theme::partial('site-header') !!}

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
                    Discover premium homes, apartments, commercial properties and plots with
                    GEMlisting. Your trusted partner for buying, selling and renting real estate.
                </p>
                <div class="hero__actions">
                    <a href="{{ route('public.properties') }}" class="btn-primary">
                        Explore Properties
                        <i class="icon-arrow-right"></i>
                    </a>
                    <a href="{{ route('public.agent.list') }}" class="btn-outline">Talk to an Agent</a>
                </div>
            </div>

            <div class="hero__search-wrapper">
                {!! Theme::partial('home-page-new/search-bar') !!}
            </div>

        </div>
    </div>

</header>