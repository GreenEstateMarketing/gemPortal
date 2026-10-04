<?php

use Botble\Theme\Theme;
use Illuminate\Support\Facades\Route;

return [

    /*
    |--------------------------------------------------------------------------
    | Inherit from another theme
    |--------------------------------------------------------------------------
    |
    | Set up inherit from another if the file is not exists,
    | this is work with "layouts", "partials" and "views"
    |
    | [Notice] assets cannot inherit.
    |
    */

    'inherit' => null, //default

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    | [Notice] these event can be override by package config.
    |
    */

    'events' => [

        // Before event inherit from package config and the theme that call before,
        // you can use this event to set meta, breadcrumb template or anything
        // you want inheriting.
        'before' => function ($theme) {
            // You can remove this line anytime.
        },

        // Listen on event before render a theme,
        // this event should call to assign some assets,
        // breadcrumb template.
        'beforeRenderTheme' => function (Theme $theme) {
            $version = filemtime(public_path('js/app.js'));

            // You may use this event to set up your assets.
            $theme->asset()->usePath()->add('bootstrap-css', 'libraries/bootstrap/bootstrap.min.v4.css');
            $theme->asset()->usePath()->add('fontawesome-css', 'libraries/fontawesome/css/fontawesome.min.css');
            $theme->asset()->usePath()->add('owl-carousel-css', 'libraries/owl-carousel/owl.carousel.min.css');
            $theme->asset()->usePath()->add('owl-carousel-theme-css', 'libraries/owl-carousel/owl.theme.default.css');
            $theme->asset()->usePath()->add('style-css', 'css/style.css', [], [], $version);

            //$theme->asset()->usePath()->add('font-awesome-css', 'css/fontawesome.min.css');
            $theme->asset()->usePath()->add('animate-css', 'css/animate.min.css');
            $theme->asset()->usePath()->add('fancy-box-css', 'css/fancybox.min.css');
            $theme->asset()->usePath()->add('swiper-css', 'css/swiper.min.css');
            $theme->asset()->usePath()->add('bootstrap-css', 'css/bootstrap.min.css');
            $theme->asset()->usePath()->add('bootstrap-css', 'css/bootstrap.min.css');
            $theme->asset()->add('query-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
            $theme->asset()->add('bootstrap-typeahead-css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/tokenfield-typeahead.css');
            $theme->asset()->add('bootstrap-tokenfield-css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css');
            $theme->asset()->usePath()->add('style-css', 'css/style.css', [], [], $version);

            if (
                Route::current() && Route::current()->getName() != "public.index" &&
                Route::current() && Route::current()->getName() != "public.property.show" &&
                Route::current() && Route::current()->getName() != "public.project.show" &&
                Route::current() && Route::current()->getName() != "public.properties"
            ) {
                $theme->asset()->add('real-estate-admin', 'css/real-estate-admin.css', [], []);
            }

            $theme->asset()->add('choices-css', 'https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css', [], []);
            $theme->asset()->usePath()->add('theme-css', 'css/theme-css.css', [], [], $version);
            $theme->asset()->usePath()->add('site-footer-css', 'css/home-page-new/site-footer.css', [], [], $version);
            $theme->asset()->usePath()->add('site-header-css', 'css/site-header.css', [], [], $version);

            // "legal" template pages (Terms & Conditions, Privacy Policy, FAQ,
            // Shipping/Delivery Policy, Disclaimer) need their own stylesheet +
            // fonts in <head>. Registered here (rather than as a <link> inside
            // shortcode-rendered content) because the page module's clean()
            // helper auto-wraps a shortcode-only page's raw "[gem-*]" text in a
            // <p> before the shortcode is expanded - a <link> placed inside
            // that content would end up nested in an empty <p>, whose default
            // browser margin renders as a stray gap under the site header.
            if (request()->is('privacy-policy', 'terms-conditions', 'faq', 'shipping-delivery-policy', 'disclaimer', 'about-us')) {
                $theme->asset()->usePath()->add('legal-css', 'css/home-page-new/legal.css', [], [], $version);
                $theme->asset()->add('legal-fonts-css', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap', [], []);
            }

            // Blog listing (/blog) and single post (/blog/{slug}) pages - same
            // navy/gold design language as the "legal" pages above, but using
            // only the plain Poppins body font (no Playfair Display) for
            // headings too, intentionally kept simple rather than decorative.
            if (request()->is('blog', 'blog/*')) {
                $theme->asset()->usePath()->add('blog-css', 'css/home-page-new/blog.css', [], [], $version);
                $theme->asset()->add('blog-fonts-css', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', [], []);
            }

            // Properties listing/map page - route-name gated (not
            // request()->is('properties')) because the path prefix is
            // admin-configurable via SlugHelper::getPrefix(Property::class,
            // 'properties'); "public.properties" is the stable identifier,
            // already used this same way elsewhere in this file below.
            if (Route::current() && Route::current()->getName() === 'public.properties') {
                $theme->asset()->add('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], []);
                $theme->asset()->container('footer')->add('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], []);
                // The reused search bar (partials/home-page-new/search-bar.blade.php)
                // is styled entirely by header.css's .hero-search-card* rules.
                // header.css is normally only pulled in via an inline <link> inside
                // partials/home-page-new/header.blade.php (the homepage's hero),
                // which this page doesn't include - load it directly here instead.
                $theme->asset()->usePath()->add('home-page-header-css', 'css/home-page-new/header.css', [], [], $version);
                $theme->asset()->usePath()->add('properties-search-css', 'css/home-page-new/properties-search.css', [], [], $version);
                $theme->asset()->usePath()->add('properties-map-css', 'css/home-page-new/properties-map.css', [], [], $version);
                $theme->asset()->usePath()->add('properties-highlights-css', 'css/home-page-new/properties-highlights.css', [], [], $version);
                $theme->asset()->add('properties-fonts-css', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', [], []);
                $theme->asset()->container('footer')->usePath()->add('properties-map-js', 'js/new-home-page/properties-map.js', ['leaflet-js'], [], $version);
                // Same reason as header.css above: this page reuses the search
                // bar but not its usual parent partial (home-page-new/header.blade.php),
                // which is the only place js/new-home-page/header.js is normally
                // enqueued. That script is what forwards a click anywhere on the
                // "Property Type" trigger to the actual (zero-size, empty)
                // #propertydropdownMenuLink element scripts.js listens on -
                // without it the visible label/arrow aren't clickable, only
                // that exact empty span is. Plain vanilla JS, no jQuery
                // dependency, safe to load standalone here.
                $theme->asset()->container('footer')->usePath()->add('home-page-header-js', 'js/new-home-page/header.js', [], [], $version);
            }

            // Projects listing page - same stable-route-name gating as
            // "public.properties" above. No Leaflet/map JS needed: the map
            // section is a plain Google Maps iframe embed (see
            // partials/projects/map-section.blade.php).
            if (Route::current() && Route::current()->getName() === 'public.projects') {
                $theme->asset()->usePath()->add('home-page-header-css', 'css/home-page-new/header.css', [], [], $version);
                $theme->asset()->usePath()->add('projects-hero-css', 'css/home-page-new/projects-hero.css', [], [], $version);
                $theme->asset()->usePath()->add('projects-grid-css', 'css/home-page-new/projects-grid.css', [], [], $version);
                $theme->asset()->usePath()->add('projects-spotlight-css', 'css/home-page-new/projects-spotlight.css', [], [], $version);
                $theme->asset()->usePath()->add('projects-map-css', 'css/home-page-new/projects-map.css', [], [], $version);
                $theme->asset()->usePath()->add('projects-cta-css', 'css/home-page-new/projects-cta.css', [], [], $version);
                $theme->asset()->add('projects-fonts-css', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', [], []);
                // 'homechoosen2-js' (select2) is enqueued further below, in
                // the same route-gated block that also covers public.index
                // and public.properties - depending on its handle here
                // guarantees load order regardless of which block runs
                // first.
                $theme->asset()->container('footer')->usePath()->add('projects-search-js', 'js/new-home-page/projects-search.js', ['homechoosen2-js'], [], $version);
            }
            $theme->asset()->usePath()->add('auth-shell-css', 'css/auth-shell.css', [], [], $version);
            $theme->asset()->add('select2-css', 'css/select2-custom.min.css', [], []);
            $theme->asset()->add('choosen-css', 'css/chosen.min.css', [], []);
            /* $theme->asset()->add('leaflet-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.css');
             $theme->asset()->add('leaflet-draw-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.2.3/leaflet.draw.css');*/

            if (BaseHelper::siteLanguageDirection() == 'rtl') {
                $theme->asset()->usePath()->add('rtl-style', 'css/rtl-style.css', [], [], $version);
            }

            $theme->asset()->container('header')->usePath()->add('jquery', 'libraries/jquery.min.js');
            $theme->asset()->container('header')->usePath()->add('popper-js', 'libraries/bootstrap/popper.min.js');
            $theme->asset()->container('header')->usePath()->add('bootstrap-js', 'libraries/bootstrap/bootstrap.min.js');
            $theme->asset()->container('header')->usePath()->add('owl-carousel-js', 'libraries/owl-carousel/owl.carousel.min.js');
            $theme->asset()->container('header')->usePath()->add('equal-height-js', 'libraries/jquery.matchHeight-min.js');
            $theme->asset()->container('footer')->usePath()->add('waypoints-js', 'libraries/jquery.waypoints.min.js');
            //if(Route::current() && Route::current()->getName()!="public.property.show")
        
            $theme->asset()->container('footer')->usePath()->add('site-header-js', 'js/site-header.js', [], [], $version);
            $theme->asset()->container('footer')->usePath()->add('app-js', 'js/app.js', [], [], $version);
            $theme->asset()->container('footer')->usePath()->add('components-js', 'js/components.js', [], [], $version);
            $theme->asset()->container('footer')->usePath()->add('wishlist', 'js/wishlist.js', [], [], $version);

            // 'custom-app-js' => '/js/app.js' (no usePath() - deliberately
            // resolves from the APPLICATION root's public/js/app.js, compiled
            // from resources/js/app.js, NOT this theme's own compiled app.js
            // - handle 'app-js' above is that one, from the theme's own
            // assets/js/app.js, a different file that happens to share a
            // filename). This root bundle registers the site's OTHER Vue
            // custom elements - <projects>, <welcome>, <agent-search>,
            // <blog>, <member>, <facility>, <related>, <packages> - onto a
            // SEPARATE Vue instance mounted on #app (resources/js/app.js's
            // own `new Vue({el:'#app'})`). It does NOT touch window.jQuery
            // anywhere (confirmed: zero `window.jQuery =` / `window.$ =` in
            // the compiled output), so it was never actually part of the
            // jQuery duplication bug below - a prior pass here wrongly
            // removed it thinking it was an unrelated/duplicate bundle,
            // which broke /projects (Vue: "Unknown custom element: <projects>").
            // Restored.
            $theme->asset()->container('footer')->add('custom-app-js', '/js/app.js', [], [], $version);

            // jQuery used to be loaded THREE times total on every public page:
            // once correctly here (handle 'jquery', libraries/jquery.min.js,
            // v3.4.1, in the header container - everything below, incl.
            // owl-carousel-js above and app.js's own $(document).ready()
            // handler, is written against this one), then again here as
            // 'jquery-js' (js/jquery.min.js, v1.12.4), then again further
            // below as 'tabs-div' (CDN jquery-1.12.0.min.js). Each later load
            // replaces window.jQuery/$ with a fresh instance that never had
            // owl-carousel's plugin registered on it, which crashed app.js's
            // ready handler partway through (a $('#cityslide').owlCarousel()
            // call throws "not a function") and silently broke everything
            // bound further down in that same handler - including the search
            // bar's Buy/Rent/Projects tab switching. Removed both duplicate
            // jQuery loads (but NOT 'custom-app-js' above, see its own
            // comment); grepped scripts.js/app.js for the common
            // jQuery-1.x-only APIs
            // (.live/.die/.toggle(fn,fn)/.size()/$.browser) removed in 3.x -
            // none found, so consolidating onto the one v3.4.1 load is safe.
            $theme->asset()->container('footer')->usePath()->add('proper-js', 'js/popper.min.js');
            // 'bootstrap-js' => js/bootstrap.min.js removed here - it's the
            // exact same Bootstrap v4.3.1 already loaded above (handle
            // 'bootstrap-js', libraries/bootstrap/bootstrap.min.js, header
            // container) under the same asset name but a different
            // container, so Botble's per-container dedup never caught it.
            // Loading Bootstrap twice means its data-api delegated click
            // handler for [data-toggle="modal"] (and dropdown/collapse/tab)
            // gets bound twice - one real click fired BOTH, which call
            // .modal('toggle') in sequence: show, then immediately hide
            // again in the same click. That's why "Area Unit"/"Change
            // Currency" (both data-toggle="modal" links) looked like they
            // did nothing on click, on every page, not just this session's
            // new ones.
            $theme->asset()->container('footer')->usePath()->add('swiper-js', 'js/swiper.min.js');
            $theme->asset()->container('footer')->usePath()->add('fancybox-js', 'js/fancybox.min.js');
            $theme->asset()->container('footer')->usePath()->add('load-js', 'js/load.min.js');
            //  if(Route::current() && Route::current()->getName()!="public.property.show")
            $theme->asset()->container('footer')->usePath()->add('text-rotator-js', 'js/text-rotater.js');

            $theme->asset()->container('footer')->usePath()->add('stellar-js', 'js/jquery.stellar.js');
            $theme->asset()->container('footer')->usePath()->add('isotop-js', 'js/isotope.min.js');





            /*    $theme->asset()->container('footer')->add('leaflet-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.js');
                $theme->asset()->container('footer')->add('leaflet-draw-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js');
                $theme->asset()->container('footer')->add('leaflet-pip-js', 'https://rawgit.com/hayeswise/Leaflet.PointInPolygon/master/wise-leaflet-pip.js');*/
            // if(Route::current() && Route::current()->getName()!="public.property.show")
            $theme->asset()->container('footer')->add('choices-div', 'https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js');
            $theme->asset()->container('footer')->add('jquery-ui-js', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');
            $theme->asset()->container('footer')->add('tokenfield-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js');
            //if(Route::current() && Route::current()->getName()!="public.property.show")
            $theme->asset()->container('footer')->usePath()->add('validate-en-js', 'libraries/jquery-validation/jquery.validationEngine-vi.js');
            $theme->asset()->container('footer')->usePath()->add('validate-ens-js', 'libraries/jquery-validation/jquery.validationEngine.js');

            $theme->asset()->container('footer')->usePath()->add('scripts-js', 'js/scripts.js');

            // The property wizard's own Location step loads (and initializes) the
            // Google Maps JS API itself, on-demand, only when that step is shown -
            // adding it again here would load the API twice on the same page and
            // break both instances (the second `<script>` tag's module requests
            // silently fail once the bootstrap loader has already run once).
            if (Route::current()
                && Route::current()->getName() != "public.property.show"
                && ! str_contains(Route::current()->getName(), 'wizard'))
                $theme->asset()->container('footer')->add('googleapis-js', "https://maps.googleapis.com/maps/api/js?key=" . setting('google_map_api_key') . "&libraries=places,drawing");


            if (Route::current() && Route::current()->getName() == "public.property.show" || Route::current() && Route::current()->getName() == "public.project.show") {
                $theme->asset()->container('footer')->add('googleapis-js', "https://maps.googleapis.com/maps/api/js?key=" . setting('google_map_api_key') . "&libraries=places,geometry&callback=initMapNeighbourhood");
            }


            $theme->asset()->container('footer')->add('show-contact-js', 'js/show-contact.js', [], []);
            // real-estate-admin.js is the admin/wizard category picker - it
            // binds its own click handlers to the SAME class names as the
            // public search bar's category popover (.p-category etc.), but
            // reads a `data-category_name` attribute the public markup
            // doesn't have, so clicking a category there injects the
            // literal text "undefined". public.index/property.show/project.show
            // were already excluded for this exact reason; public.properties
            // needs the same exclusion now that it reuses the same search bar.
            if (Route::current() && Route::current()->getName() != "public.index" && Route::current() && Route::current()->getName() != "public.property.show" && Route::current() && Route::current()->getName() != "public.project.show" && Route::current() && Route::current()->getName() != "public.properties")
                $theme->asset()->container('footer')->add('real-estate-admin-js', 'js/real-estate-admin.js', [], []);

            /* if(Route::current() && Route::current()->getName()=="general-add-property")

                $theme->asset()->add('bootstrap-css', 'custom/css/agent_style.css');*/
            /* if(Route::current() && Route::current()->getName()=="general-add-property")
             $theme->asset()->container('footer')->add('real-estate-admin-js', 'js/real-member-user.js', [], []);*/
            //if( Route::current() && Route::current()->getName()=="public.property.show" || Route::current() && Route::current()->getName()=="public.project.show" || Route::current() && Route::current()->getName()=="public.index")
            // 'tabs-div' => CDN jquery-1.12.0.min.js removed here - see the
            // jQuery duplication comment above 'jquery-js' further up; this
            // was the third/final jQuery reload and the one that actually
            // "won" as window.jQuery by the time any post-load code ran.
            $theme->asset()->container('footer')->add('validate-app-js', '/js/jquery.validate.min.js');
            $theme->asset()->container('footer')->add('additional-methods-js', '/js/additional-methods.min.js');

            if (Route::current() && Route::current()->getName() == "public.member.package.subscribe" && Route::current() && Route::current()->getName() == "public.account.package.subscribe") {
                $theme->asset()->container('footer')->add('checkout-js', '/js/checkout.js');
            }
            if (Route::current() && Route::current()->getName() == "wanted") {
                $theme->asset()->add('select2-css', '/vendor/core/core/base/libraries/select2/css/select2.min.css', [], []);
                $theme->asset()->add('wanted-css', 'css/wanted.css', [], []);
                $theme->asset()->container('footer')->add('wanted-js', '/js/wanted.js');
                $theme->asset()->container('footer')->add('select2-js', '/vendor/core/core/base/libraries/select2/js/select2.min.js');
            }
            if (Route::current() && Route::current()->getName() == "public.index" || Route::current() && Route::current()->getName() == "public.properties" || Route::current() && Route::current()->getName() == "public.projects") {

                //$theme->asset()->container('footer')->add('choosen-js', '/js/chosen.jquery.min.js');
                // $theme->asset()->container('footer')->add('choosen-proto-js', '/js/chosen.proto.min.js');
                $theme->asset()->container('footer')->add('autocomplete-js', '/js/jquery.autocomplete.min.js');
                $theme->asset()->container('footer')->add('homechoosen2-js', '/vendor/core/core/base/libraries/select2/js/select2.min.js');
                $theme->asset()->container('footer')->add('homechoosen-js', '/js/homechoosen.js');

            }
            $theme->asset()->container('footer')->add('tabs-div-prop', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', null, array('integrity' => 'sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS', 'crossorigin' => 'anonymous'));
            if (function_exists('shortcode')) {
                $theme->composer([
                    'index',
                    'page',
                    'post',
                    'career.career',
                    'real-estate.project',
                    'real-estate.property',
                ], function (\Botble\Shortcode\View\View $view) {
                    $view->withShortcodes();
                });
            }
        },

        // Listen on event before render a layout,
        // this should call to assign style, script for a layout.
        'beforeRenderLayout' => [

            'default' => function ($theme) {
                // $theme->asset()->usePath()->add('ipad', 'css/layouts/ipad.css');
            }
        ]
    ]
];
