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
            $version = '1.0.0';

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
        
            $theme->asset()->container('footer')->usePath()->add('app-js', 'js/app.js', [], [], $version);
            $theme->asset()->container('footer')->usePath()->add('components-js', 'js/components.js', [], [], $version);
            $theme->asset()->container('footer')->usePath()->add('wishlist', 'js/wishlist.js', [], [], $version);

            $theme->asset()->container('footer')->add('custom-app-js', '/js/app.js');
            $theme->asset()->container('footer')->usePath()->add('jquery-js', 'js/jquery.min.js');
            $theme->asset()->container('footer')->usePath()->add('proper-js', 'js/popper.min.js');
            $theme->asset()->container('footer')->usePath()->add('bootstrap-js', 'js/bootstrap.min.js');
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

            if (Route::current() && Route::current()->getName() != "public.property.show")
                $theme->asset()->container('footer')->add('googleapis-js', "https://maps.googleapis.com/maps/api/js?key=" . setting('google_map_api_key') . "&libraries=places,drawing");


            if (Route::current() && Route::current()->getName() == "public.property.show" || Route::current() && Route::current()->getName() == "public.project.show") {
                $theme->asset()->container('footer')->add('googleapis-js', "https://maps.googleapis.com/maps/api/js?key=" . setting('google_map_api_key') . "&libraries=places,geometry&callback=initMapNeighbourhood");
            }


            $theme->asset()->container('footer')->add('show-contact-js', 'js/show-contact.js', [], []);
            if (Route::current() && Route::current()->getName() != "public.index" && Route::current() && Route::current()->getName() != "public.property.show" && Route::current() && Route::current()->getName() != "public.project.show")
                $theme->asset()->container('footer')->add('real-estate-admin-js', 'js/real-estate-admin.js', [], []);

            /* if(Route::current() && Route::current()->getName()=="general-add-property")

                $theme->asset()->add('bootstrap-css', 'custom/css/agent_style.css');*/
            /* if(Route::current() && Route::current()->getName()=="general-add-property")
             $theme->asset()->container('footer')->add('real-estate-admin-js', 'js/real-member-user.js', [], []);*/
            //if( Route::current() && Route::current()->getName()=="public.property.show" || Route::current() && Route::current()->getName()=="public.project.show" || Route::current() && Route::current()->getName()=="public.index")
            $theme->asset()->container('footer')->add('tabs-div', 'https://code.jquery.com/jquery-1.12.0.min.js');
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
