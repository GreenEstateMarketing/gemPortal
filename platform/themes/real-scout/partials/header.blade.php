<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />

    <!-- Fonts-->
    <link
        href="https://fonts.googleapis.com/css?family={{ urlencode(theme_option('primary_font', 'Nunito Sans')) }}:300,600,700,800"
        rel="stylesheet" type="text/css">
    <!-- CSS Library-->

    <style>
        :root {
            --primary-color:
                {{ theme_option('primary_color', '#1d5f6f') }};
            --primary-color-rgb:
                {{ hex_to_rgba(theme_option('primary_color', '#1d5f6f'), 0.8) }};
            --primary-color-hover:
                {{ theme_option('primary_color_hover', '#063a5d') }};
            --primary-font: '{{ theme_option('primary_font', 'Nunito Sans') }}';
        }

        #map-container {
            position: relative;
            height: 230px;

        }

        #map {
            position: relative;
            height: inherit;

            width: inherit;
        }

        .select2-container--default .select2-selection--single {
            border: none !important;
            text-align: left !important;
        }

        .select2-container {
            /*margin-top: 12px !important;*/
            text-align: left !important;
        }
    </style>

    {!! Theme::header() !!}
</head>

<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>


    <!-- end transition-overlay -->
    <div class="side-navigation">
        <div class="menu">
            {!! Menu::renderMenuLocation('main-menu', [
                'options' => ['class' => ''],
                'view' => 'main-menu',
            ]) !!}
        </div>
        <!-- end menu -->
        <div class="side-content">
            <figure>
                @if (theme_option('logo'))
                    <a class="navbar-brand" href="{{ route('public.single') }}">
                        <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" class="logo" height="40"
                            alt="{{ theme_option('site_name') }}">
                    </a>
                @endif
            </figure>
            <p>By aiming to take the life quality to an upper level with the whole realized Projects, GEM continues to
                be
                the address of luxury.</p>
            <ul class="gallery">
                <li><a href="images/gallery-thumb01.jpg" data-fancybox><img
                            src="{{ Theme::asset()->url('images/gallery-thumb01.jpg') }}" alt="Image"></a></li>
                <li><a href="images/gallery-thumb02.jpg" data-fancybox><img
                            src="{{ Theme::asset()->url('images/gallery-thumb02.jpg') }}" alt="Image"></a></li>
                <li><a href="images/gallery-thumb03.jpg" data-fancybox><img
                            src="{{ Theme::asset()->url('images/gallery-thumb03.jpg') }}" alt="Image"></a></li>
            </ul>
            <address>
                {{ theme_option('address') }}
            </address>
            <h6>{{ theme_option('hotline') }}</h6>
            <p><a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a></p>
            <ul class="social-media">

                <li><a href="{{ theme_option('facebook') }}"><i class="fab fa-facebook-f"></i></a></li>
                <li><a href="{{ theme_option('twitter') }}"><i class="fab fa-twitter"></i></a></li>
                <li><a href="{{ theme_option('youtube') }}"><i class="fab fa-youtube"></i></a></li>
                <li><a href="{{ theme_option('linkedin') }}"><i class="fab fa-linkedin"></i></a></li>

            </ul>
            <small>{{ theme_option('copyright') }}</small>
        </div>
        <!-- end side-content -->
    </div>
    <!-- end side-navigation -->
    <nav class="navbar sticky-top">
        <div class="container">
            <div class="upper-side">
                <div class="logo">
                    <a href="index.html">
                        @if (theme_option('logo'))
                            <a class="navbar-brand" href="{{ route('public.single') }}">
                                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" class="logo"
                                    height="40" alt="{{ theme_option('site_name') }}">
                            </a>
                        @endif
                    </a>
                </div>
                <!-- Sticky Menu -->
                <div id="menu-sticky" class="menu" style="display: none;align-items: center">
                    {!! Menu::renderMenuLocation('main-menu', [
                        'options' => ['class' => 'stick-list'],
                        'view' => 'main-menu',
                    ]) !!}
                    @if (auth('account')->check())
                        <div id="profile_link_sticky" style="display: none;">
                            <ul>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span>
                                            <img src="{{ auth('account')->user()->image_path ? 'storage/' . auth('account')->user()->image_path : auth('account')->user()->avatar_url }}"
                                                class="br-100 v-mid mr-1" style="width: 30px;">
                                            <span
                                                class="profile-label">{{ auth('account')->user()->getFullName() }}</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a class="dropdown-item"
                                            href="{{ route('public.account.dashboard') }}">Dashboard</a>
                                        <a class="dropdown-item" href="{{ route('public.account.settings') }}">Edit
                                            Profile</a>
                                        @if (auth('account')->check())
                                            <form id="logout-form" action="{{ route('public.account.logout') }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        @endif
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                            Out</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @elseif(auth('member')->check())
                        <div id="profile_link_sticky" style="display: none;">
                            <ul>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span>
                                            <img src="{{ auth('member')->user()->avatar_url }}"
                                                class="br-100 v-mid mr-1" style="width: 30px;">
                                            <span class="profile-label">{{ auth('member')->user()->full_name }}</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a class="dropdown-item" href="{{ route('member.dashboard') }}">Dashboard</a>
                                        <a class="dropdown-item" href="{{ route('member.settings') }}">Edit Profile</a>
                                        @if (auth('account')->check())
                                            <form id="logout-form" action="{{ route('public.member.logout') }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        @endif
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                            Out</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @else
                    @endif
                </div>
                <!-- end logo -->
                <div class="phone-email">
                </div>
                <!-- end phone -->
                <!--<div class="language"> <a href="#">EN</a> </div> -->
                <!-- end language -->
                <div class="hamburger"><span></span> <span></span> <span></span><span></span></div>
                <!-- end hamburger -->
                @if (auth('account')->check())
                    <input type="hidden" id="login_check" value="1" />
                    <div id="profile_link">
                        <ul>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span>
                                        <img src="{{ auth('account')->user()->image_path ? 'storage/' . auth('account')->user()->image_path : auth('account')->user()->avatar_url }}"
                                            class="br-100 v-mid mr-1" style="width: 30px;">
                                        <span
                                            class="profile-label">{{ auth('account')->user()->getFullName() }}</span>
                                    </span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item"
                                        href="{{ route('public.account.dashboard') }}">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('public.account.settings') }}">Edit
                                        Profile</a>
                                    @if (auth('account')->check())
                                        <form id="logout-form" action="{{ route('public.account.logout') }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    @endif
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                        Out</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                @elseif(auth('member')->check())
                    <input type="hidden" id="login_check" value="1" />
                    <div id="profile_link">
                        <ul>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span>
                                        <img src="{{ auth('member')->user()->avatar_url }}" class="br-100 v-mid mr-1"
                                            style="width: 30px;">
                                        <span class="profile-label">{{ auth('member')->user()->full_name }}</span>
                                    </span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="{{ route('member.dashboard') }}">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('member.settings') }}">Edit Profile</a>
                                    @if (auth('member')->check())
                                        <form id="logout-form" action="{{ route('public.member.logout') }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    @endif
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                        Out</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                @else
                    <input type="hidden" id="login_check" value="0" />
                    <button class="btn btn-login login-up ml-2" type="button"><a
                            href="{{ route('member.login') }}"><i class="fas fa-sign-in-alt"></i> Login</a></button>
                @endif

            </div>

            <!-- end upper-side -->
            <div class="menu" id="menu">
                {!! Menu::renderMenuLocation('main-menu', [
                    'options' => ['class' => ''],
                    'view' => 'main-menu',
                ]) !!}
            </div>
            <!-- end menu -->
        </div>
        <!-- end container -->
    </nav>
    <!-- end navbar -->
    @php
        $page = Theme::get('page');
    @endphp
    <input type="hidden" name="app_url" id="app_url" value="{{ config('app.url') }}">
    @if (url()->current() == route('public.single') || ($page && $page->template === 'homepage'))
        <header class="slider">
            <div class="slider-container">
                <div class="topsearch">
                    @if (theme_option('home_banner_description'))
                        <h1 class="text-center text-white mb-4 banner-text-description">
                            {{ theme_option('home_banner_description') }}</h1>
                    @endif
                    @if (is_plugin_active('real-estate'))
                        <form action="{{ route('public.projects') }}" method="GET" id="frmhomesearch">
                            <div class="typesearch" id="hometypesearch">
                                <a href="javascript:void(0)" class="active top-left-radius" rel="project"
                                    data-url="{{ route('public.projects') }}">{{ __('Projects') }}</a>
                                <a href="javascript:void(0)" rel="sale"
                                    data-url="{{ route('public.properties') }}">{{ __('Buy') }}</a>
                                <a href="javascript:void(0)" rel="rent" class="top-right-radius"
                                    data-url="{{ route('public.properties') }}">{{ __('Rent') }}</a>
                            </div>
                            <input type="hidden" id="selected-unit" name="selected-unit"
                                value="{{ getDefaultAreaByUnitForNextPage() }}" />
                            <!-- <input type="hidden" id="sub-cat" name="child_category_id" value="" >
                            <input type="hidden" id="p-cat" name="p_category_id" value="" > -->
                            <div class="input-group input-group-lg">
                                <input type="hidden" name="type" value="project" id="txttypesearch">
                                <div class="input-group-prepend">
                                    <span style="border-radius: 5px 0px 0px 5px !important"
                                        class="input-group-text"><i class="far fa-search"></i></span>
                                </div>

                                <div id="parentChipContainer" class="keyword-input">
                                    <div id="chipContainer">
                                        <div class="position-relative input-field-container"
                                            style="max-height: 32px;max-width: 60%;">
                                            <!--                                        <div class="spinner-border spinner-border-sm float-right" role="status"
                                                                                             style="display:none">
                                                                                            <span class="sr-only">Loading...</span>
                                                                                        </div>-->
                                            <input placeholder="Location" class="form-control" type="text"
                                                name="" id="autocomplete-ajax"
                                                style="position: absolute; z-index: 2; background: transparent; width: auto" />
                                            <input class="form-control" type="text" name=""
                                                id="autocomplete-ajax-x" disabled="disabled"
                                                style="color: #CCC; background: transparent; z-index: 1;" />
                                        </div>
                                        <div id="chipViewMore" class="chip" style="display:none">
                                            <div class="chip-content"></div>
                                        </div>
                                    </div>


                                    <!--                               <input type="text" class="form-control" name="k" placeholder="{{ __('Keyword') }}"  id="search_data_chosen" autocomplete="off">

                                        <select id="search_data_chosen" data-placeholder="Keyword" class="form-control  select-box" multiple name="k[]">


                                       </select>-->

                                    <!--                                <div class="spinner-icon">
                                                                            <i class="fas fa-spin fa-spinner"></i>
                                                                        </div>-->

                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-location"></i></span>
                                </div>
                                <div class="keyword-input">
                                    <input id="city-name-from-map" type="hidden"
                                        class="select-city-state form-control" placeholder="{{ __('City, State') }}"
                                        autocomplete="off" />
                                    <select class="form-control" id='city_id' name="city_id">
                                        <option value="0">Select city...</option>
                                        @foreach (app(\Botble\Location\Repositories\Interfaces\CityInterface::class)->allBy(['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED], ['state', 'country'], ['cities.name', 'cities.state_id', 'cities.country_id', 'cities.id']) as $city)
                                            <option value={{ $city->id }}>
                                                {{ $city->name . ($city->state->name ? ' (' . $city->state->name . ')' : '') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group-append search-button-wrapper">
                                    <button class="btn btn-orange" id="submitBtn"
                                        type="submit">{{ __('Search') }}</button>
                                </div>

                                <div class="advanced-search ">
                                    <a href="#" class="advanced-search-toggler">{{ __('Advanced') }} <i
                                            class="fas fa-caret-down"></i></a>
                                    <div class="advanced-search-content property-advanced-search" id="propertysearch">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-control">
                                                        <input type="hidden" name="category_id" class="category_id">
                                                        <span class="dropdown-toggle" id="propertydropdownMenuLink"
                                                            style="cursor: pointer;">Category Type</span>
                                                        <span style="display: flex"
                                                            class="category_id_text">Home</span>

                                                    </div>
                                                </div>
                                                <div class="col-md-4  bedrooms">
                                                    <div class="select--arrow">
                                                        <select name="bedroom" id="select-bedroom"
                                                            class="form-control">
                                                            <option value="">{{ __('Bedrooms') }}</option>
                                                            @for ($i = 1; $i < 5; $i++)
                                                                <option value="{{ $i }}"
                                                                    @if (request()->input('bedroom') == $i) selected @endif>
                                                                    {{ $i }}
                                                                    {{ $i == 1 ? __('room') : __('rooms') }}</option>
                                                            @endfor
                                                            <option value="5"
                                                                @if (request()->input('bedroom') == 5) selected @endif>
                                                                {{ __('5+ rooms') }}</option>
                                                        </select>
                                                        <i class="fas fa-angle-down"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 bathrooms">
                                                    <div class="select--arrow">
                                                        <select name="bathroom" id="select-bathroom"
                                                            class="form-control">
                                                            <option value="">{{ __('Bathrooms') }}</option>
                                                            @for ($i = 1; $i < 5; $i++)
                                                                <option value="{{ $i }}"
                                                                    @if (request()->input('bathroom') == $i) selected @endif>
                                                                    {{ $i }}
                                                                    {{ $i == 1 ? __('room') : __('rooms') }}</option>
                                                            @endfor
                                                            <option value="5"
                                                                @if (request()->input('bathroom') == 5) selected @endif>
                                                                {{ __('5+ rooms') }}</option>
                                                        </select>
                                                        <i class="fas fa-angle-down"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 plot-price-dp d-none">
                                                    <div class="price-dropdown">
                                                        <div class="dropdown">

                                                            <a id="min-max-price-range"
                                                                class="form-control price-select dropdown-toggle"
                                                                href="#" data-toggle="dropdown">Price<span
                                                                    class="currency">{{ CurrentCurrency()->title }}</span><strong
                                                                    class="caret"></strong>

                                                            </a>
                                                            <div class="row price-from-to">
                                                                <div class="col-md-4"><span
                                                                        class="min_price_text">0</span>
                                                                </div>
                                                                <div class="col-md-1">to</div>
                                                                <div class="col-md-4"><span
                                                                        class="max_price_text">Any</span>
                                                                </div>
                                                            </div>


                                                            <div class="dropdown-menu"
                                                                style="padding:10px;width:100%">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-6">
                                                                        <input class="form-control price-label"
                                                                            style="border:1px solid #a0a0a0 !important;"
                                                                            name="min_price" placeholder="Min"
                                                                            data-dropdown-id="price-min" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input class="form-control price-label"
                                                                            style="border:1px solid #a0a0a0 !important;"
                                                                            name="max_price" placeholder="Max"
                                                                            data-dropdown-id="price-max" />
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="row mt-2 justify-content-center">

                                                                    <div class="col-md-6">
                                                                        <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                                            style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden">

                                                                            {!! getPriceLists() !!}
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <ul class="price-range col-md-12 price-max-ul   list-unstyled"
                                                                            style="width:250px;height:150px; overflow-y: auto;overflow-x:hidden">
                                                                            {!! getPriceLists() !!}
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-reset-price"
                                                                    style="margin: 10px; height: 35px !important;">
                                                                    Reset
                                                                </button>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 floors commerical-floors d-none">
                                                    <div class="select--arrow">
                                                        <select name="floor" id="select-floor"
                                                            class="form-control">
                                                            <option value="">{{ __('Floors') }}</option>
                                                            @for ($i = 1; $i < 5; $i++)
                                                                <option value="{{ $i }}"
                                                                    @if (request()->input('floor') == $i) selected @endif>
                                                                    {{ $i }}
                                                                    {{ $i == 1 ? __('floor') : __('floors') }}</option>
                                                            @endfor
                                                            <option value="5"
                                                                @if (request()->input('floor') == 5) selected @endif>
                                                                {{ __('5+ floors') }}</option>
                                                        </select>
                                                        <i class="fas fa-angle-down"></i>
                                                    </div>
                                                </div>

                                            </div><!-- end row -->
                                            <div class="row second-row">
                                                <div class="col-md-4 floors home-floors">
                                                    <div class="select--arrow">
                                                        <select name="floor" id="select-floor"
                                                            class="form-control">
                                                            <option value="">{{ __('Floors') }}</option>
                                                            @for ($i = 1; $i < 5; $i++)
                                                                <option value="{{ $i }}"
                                                                    @if (request()->input('floor') == $i) selected @endif>
                                                                    {{ $i }}
                                                                    {{ $i == 1 ? __('floor') : __('floors') }}</option>
                                                            @endfor
                                                            <option value="5"
                                                                @if (request()->input('floor') == 5) selected @endif>
                                                                {{ __('5+ floors') }}</option>
                                                        </select>
                                                        <i class="fas fa-angle-down"></i>
                                                    </div>
                                                </div>
                                                <!-- prices -->
                                                <div class="col-md-4 home-price-dp">
                                                    <div class="price-dropdown">
                                                        <div class="dropdown">

                                                            <a id="min-max-price-range"
                                                                class="form-control dropdown-toggle" href="#"
                                                                data-toggle="dropdown">Price <span
                                                                    class="currency">{{ CurrentCurrency()->title }}</span><strong
                                                                    class="caret"></strong>

                                                            </a>
                                                            <div class="row price-from-to">
                                                                <div class="col-md-4"><span
                                                                        class="min_price_text">0</span>
                                                                </div>
                                                                <div class="col-md-2"
                                                                    style="margin-left: 9px;margin-right: -15px;">to
                                                                </div>
                                                                <div class="col-md-4"><span
                                                                        class="max_price_text">Any</span>
                                                                </div>
                                                            </div>


                                                            <div class="dropdown-menu"
                                                                style="padding:10px;width:100%">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-6">
                                                                        <input class="form-control price-label"
                                                                            style="border:1px solid #a0a0a0 !important;"
                                                                            name="min_price" placeholder="Min"
                                                                            data-dropdown-id="price-min" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input class="form-control price-label"
                                                                            style="border:1px solid #a0a0a0 !important;"
                                                                            name="max_price" placeholder="Max"
                                                                            data-dropdown-id="price-max" />
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="row mt-2 justify-content-center">

                                                                    <div class="col-md-6"
                                                                        style="padding-right: 15px !important;">
                                                                        <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                                            style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden">

                                                                            {!! getPriceLists() !!}
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6"
                                                                        style="padding-right: 15px !important;">
                                                                        <ul class="price-range col-md-12 price-max-ul   list-unstyled"
                                                                            style="width:250px;height:150px; overflow-y: auto;overflow-x:hidden">
                                                                            {!! getPriceLists() !!}
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-reset-price"
                                                                    style="margin: 10px; height: 35px !important;">
                                                                    Reset
                                                                </button>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 home-price-dp">
                                                    <div class="price-dropdown">
                                                        <div class="dropdown">

                                                            <a id="min-max-unit-range"
                                                                class="form-control dropdown-toggle" href="#"
                                                                data-toggle="dropdown">Area <span
                                                                    class="currency">({{ getDefaultAreaUnit() }})</span><strong
                                                                    class="caret"></strong>

                                                            </a>
                                                            <div class="row unit-from-to">
                                                                <div class="col-md-4"><span
                                                                        class="min_unit_text">0</span>
                                                                </div>
                                                                <div class="col-md-2">to</div>
                                                                <div class="col-md-4"><span
                                                                        class="max_unit_text">Any</span>
                                                                </div>
                                                            </div>

                                                            <div class="dropdown-menu"
                                                                style="padding:10px;width:100%">

                                                                <div class="row justify-content-center">
                                                                    <div class="col-6">
                                                                        <input class="form-control"
                                                                            style="border:1px solid #a0a0a0 !important;"
                                                                            name="min_unit" placeholder="Min"
                                                                            id="input_min_unit"
                                                                            data-dropdown-id="unit-min" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input class="form-control"
                                                                            style="border:1px solid #a0a0a0 !important;"
                                                                            name="max_unit" placeholder="Max"
                                                                            data-dropdown-id="unit-max"
                                                                            id="input_max_unit" />
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="row mt-2 justify-content-center">

                                                                    <div class="col-md-6 unit-list">
                                                                        <ul class="units-range col-md-12 unit-min-ul list-unstyled"
                                                                            style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden">
                                                                            @foreach (getAreaLists() as $unit)
                                                                                <li class="unit-li-item"
                                                                                    data-value="{{ $unit }}">
                                                                                    {{ $unit }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <ul class="units-range col-md-12 unit-max-ul  list-unstyled"
                                                                            style="width:250px;height:150px; overflow-y: auto;overflow-x:hidden">
                                                                            @foreach (getAreaLists() as $unit)
                                                                                <li class="unit-li-item"
                                                                                    data-value="{{ $unit }}">
                                                                                    {{ $unit }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn btn-reset-unit">
                                                                    Reset
                                                                </button>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- -->
                                            </div> <!--end row -->
                                        </div>
                                        <!-- Home search category dropdown popup-->
                                        <div class="property-category-search-dropdown" style="display:none">
                                            <div class="category-search-list" role="listbox">
                                                <div>
                                                    <div>
                                                        <ul class="category-ul" name="Category picker">
                                                            <div>
                                                                @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => 0]) as $categoryId => $categoryName)
                                                                    @if ($loop->index == 0)
                                                                        <li class="category-parent-active p-category"
                                                                            data-id="{{ $categoryId }}">
                                                                            {{ $categoryName }}</li>
                                                                    @else
                                                                        <li class="category-parent-inactive p-category"
                                                                            data-id="{{ $categoryId }}">
                                                                            {{ $categoryName }}</li>
                                                                    @endif
                                                                    <!-- create hiddenly list here of each categories-->
                                                                    <div class="p{{ $categoryId }}"
                                                                        style="display:none">
                                                                        <div class="row">
                                                                            @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => $categoryId]) as $subcategoryId => $subcategoryName)
                                                                                <div
                                                                                    class="@if ($loop->count == 1) col-md-12 @else col-md-6 @endif">
                                                                                    <li class="category-li-item"
                                                                                        parent-name="{{ $categoryName }}"
                                                                                        data-id="{{ $subcategoryId }}">
                                                                                        {{ $subcategoryName }}</li>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="category-li pcateory_data">

                                                            </div>

                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="ab3dd470">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- -->
                                    </div>
                                    <div class="advanced-search-content project-advanced-search" id="projectysearch">
                                        <div class="form-group">
                                            <div class="row">
                                                {{-- <div class="col-md-4">
                                                    <!--<div class="select--arrow">
                                                            <select name="category_id"  class="form-control select-category">
                                                                <option value="">{{ __('Category') }}</option>
                                                            </select>
                                                            <i class="fas fa-angle-down"></i>
                                                        </div>-->

                                                    <div class="form-control">
                                                        <input type="hidden" name="category_id" class="category_id">
                                                        <span id="projectdropdownMenuLink" class="dropdown-toggle">Category
                                                            Type</span>
                                                        <span style="display: flex" class="category_id_text">Home</span>

                                                    </div>
                                                </div> --}}
                                                <div class="col-md-5">
                                                    <div class="price-dropdown">
                                                        <div class="dropdown">
                                                            <a id="min-max-price-range"
                                                                class="form-control dropdown-toggle" href="#"
                                                                data-toggle="dropdown">Price <span
                                                                    class="currency">{{ CurrentCurrency()->title }}</span><strong
                                                                    class="caret"></strong>
                                                            </a>
                                                            <div class="row price-from-to">
                                                                <div class="col-md-4"><span
                                                                        class="min_price_text">0</span>
                                                                </div>
                                                                <div class="col-md-2">to</div>
                                                                <div class="col-md-4"><span
                                                                        class="max_price_text">Any</span>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown-menu"
                                                                style="padding:10px;width:100%">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-6">
                                                                        <input class="form-control price-label"
                                                                            style="border:1px solid #a0a0a0 !important"
                                                                            name="min_price" placeholder="Min"
                                                                            data-dropdown-id="price-min" />
                                                                    </div>

                                                                    <div class="col-6">
                                                                        <input class="form-control price-label"
                                                                            style="border:1px solid #a0a0a0 !important"
                                                                            name="max_price" placeholder="Max"
                                                                            data-dropdown-id="price-max" />
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="row mt-2 justify-content-center">
                                                                    <div class="col-md-6">
                                                                        <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                                            style="width: 250px;height: 150px; overflow-y: auto;overflow-x:hidden">

                                                                            {!! getPriceLists() !!}
                                                                        </ul>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <ul class="price-range col-md-12 price-max-ul   list-unstyled"
                                                                            style="width: 250px;height: 150px; overflow-y: auto;overflow-x:hidden">
                                                                            {!! getPriceLists() !!}
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <button type="button"
                                                                    class="btn btn-primary btn-reset-price"
                                                                    style="margin: 10px; height: 35px !important;">Reset
                                                                </button>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-4">
                                                        <input type="number" name="min_price" class="form-control"  placeholder="{{ __('Price from') }}" value="{{ request()->input('min_price') }}">
                                                    </div>-->
                                                <!--<div class="col-md-4">
                                                        <input type="number" name="max_price" class="form-control"  placeholder="{{ __('Price to') }}" value="{{ request()->input('max_price') }}">
                                                    </div>-->
                                            </div>
                                        </div>
                                        <!-- Home search category dropdown popup-->
                                        <div class="project-category-search-dropdown" style="display:none">
                                            <div class="category-search-list" role="listbox">
                                                <div>
                                                    <div>
                                                        <ul class="category-ul" name="Category picker">
                                                            <div>
                                                                @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => 0]) as $categoryId => $categoryName)
                                                                    @if ($loop->index == 0)
                                                                        <li class="category-parent-active p-category"
                                                                            data-id="{{ $categoryId }}">
                                                                            {{ $categoryName }}</li>
                                                                    @else
                                                                        <li class="category-parent-inactive p-category"
                                                                            data-id="{{ $categoryId }}">
                                                                            {{ $categoryName }}</li>
                                                                    @endif
                                                                    <!-- create hiddenly list here of each categories-->
                                                                    <div class="p{{ $categoryId }}"
                                                                        style="display:none">
                                                                        <div class="row">
                                                                            @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => $categoryId]) as $subcategoryId => $subcategoryName)
                                                                                <div
                                                                                    class="@if ($loop->count == 1) col-md-12 @else col-md-6 @endif">
                                                                                    <li class="category-li-item"
                                                                                        parent-name="{{ $categoryName }}"
                                                                                        data-id="{{ $subcategoryId }}">
                                                                                        {{ $subcategoryName }}</li>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="category-li pcateory_data">

                                                            </div>

                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="ab3dd470">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- -->
                                    </div>
                                    <span> | </span><a href="#" class="area-unit" id="changeAreaUnitlabel"
                                        data-toggle="modal" data-target="#area_modal">{{ __('Change Area Unit') }}
                                    </a>
                                    <span> | </span><a href="#" class="currency" id="changeCurrencylabel"
                                        data-toggle="modal"
                                        data-target="#currency_modal">{{ __('Change Currency') }}</a>

                                </div>

                            </div>
                            <div class="listsuggest">

                            </div>
                        </form>
                    @endif
                </div>
                <div class="swiper-wrapper">

                    <div class="swiper-slide" data-background="{{ Theme::asset()->url('images/banner01.jpg') }}"
                        data-stellar-background-ratio="1.15">

                    </div>

                </div>
                <div class="inner-elements">
                    <div class="container">

                    </div>

                </div>

            </div>

        </header>
    @endif
    <!-- Change Currency modal-->
    <div class="modal" id="currency_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Currency</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php $currencies = get_all_currencies(); @endphp

                    <select class="form-control" id="currency_val">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}">{{ $currency->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" style="height: 29px;"
                        data-dismiss="modal">Close</button>
                    <button type="button" id="update_currency" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <!-- Change Area unit modal-->
    <div class="modal" id="area_modal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Area Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select class="form-control" id="area_units-val">
                        <option value="m²">Square Meter</option>
                        <option value="ft²" selected>Square Feet</option>
                        <option value="yards">Yards</option>
                        <option value="marla">Marla</option>
                        <option value="kanal">Kanal</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" style="height: 29px;"
                        data-dismiss="modal">Close</button>
                    <button type="button" id="update_area" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <div class="modal" id="search_map_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map-container">
                        <div id="map"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="update_search" class="btn btn-primary">Go!</button>
                </div>
            </div>
        </div>
    </div>
