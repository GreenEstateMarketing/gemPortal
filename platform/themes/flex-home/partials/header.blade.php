<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

    <!-- Fonts-->
    <link href="https://fonts.googleapis.com/css?family={{ urlencode(theme_option('primary_font', 'Nunito Sans')) }}:300,600,700,800" rel="stylesheet" type="text/css">
    <!-- CSS Library-->

    <style>
        :root {
            --primary-color: {{ theme_option('primary_color', '#1d5f6f') }};
            --primary-color-rgb: {{ hex_to_rgba(theme_option('primary_color', '#1d5f6f'), 0.8) }};
            --primary-color-hover: {{ theme_option('primary_color_hover', '#063a5d') }};
            --primary-font: '{{ theme_option('primary_font', 'Nunito Sans') }}';
        }
    </style>

    {!! Theme::header() !!}
</head>
<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
<div id="alert-container"></div>
<div class="bravo_topbar d-none d-sm-block">
    <div class="container-fluid w90">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <div class="topbar-left">
                        <div class="top-socials">
                            <a href="{{ theme_option('facebook') }}" title="Facebook" class="fab fa-facebook-f"></a>
                            <a href="{{ theme_option('twitter') }}" title="Twitter" class="fab fa-twitter"></a>
                            <a href="{{ theme_option('youtube') }}" title="Youtube" class="fab fa-youtube"></a>
                        </div>
                        <span class="line"></span>
                        <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                    </div>
                    <div class="topbar-right">
                        @if (is_plugin_active('real-estate'))
                            <ul class="topbar-items">
                                <li><a href="{{ route('public.wishlist') }}"><i class="fas fa-heart"></i> {{ __('Wishlist') }}(<span class="wishlist-count">0</span>)</a></li>
                            </ul>
                            @php $currencies = get_all_currencies(); @endphp
                            @if (count($currencies) > 1)
                                <div class="choose-currency">
                                    <span>{{ __('Currency') }}: </span>
                                    @foreach ($currencies as $currency)
                                        <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>&nbsp;
                                    @endforeach
                                </div>
                                <div class="header-deliver">/</div>
                            @endif
                        @endif
                        {!! Theme::partial('language-switcher') !!}
                        @if (is_plugin_active('real-estate'))
                            <ul class="topbar-items">
                                @if (auth('account')->check())
                                    <li class="login-item"><a href="{{ route('public.account.dashboard') }}" rel="nofollow"><i class="fas fa-user"></i> <span>{{ auth('account')->user()->getFullName() }}</span></a></li>
                                    <li class="login-item"><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" rel="nofollow"><i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}</a></li>
                                @else
                                    <li class="login-item">
                                        <a href="{{ route('public.account.login') }}"><i class="fas fa-sign-in-alt"></i>  {{ __('Login') }}</a>
                                    </li>
                                @endif
                            </ul>
                            @if (auth('account')->check())
                                <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="topmenu bg-light">
    <div @if (theme_option('enable_sticky_header', 'yes') == 'yes') id="header-waypoint" @endif class="main-header">
        <div class="container-fluid w90">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        @if (theme_option('logo'))
                            <a class="navbar-brand" href="{{ route('public.single') }}">
                                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                     class="logo" height="40" alt="{{ theme_option('site_name') }}">
                            </a>
                        @endif
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                id="header-waypoint"                   data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-end">
                            {!!
                                Menu::renderMenuLocation('main-menu', [
                                    'options' => ['class' => 'navbar-nav justify-content-end'],
                                    'view'    => 'main-menu',
                                ])
                            !!}
                            @if (is_plugin_active('real-estate'))
                                <a class="btn btn-primary add-property" href="{{ route('public.account.properties.index') }}">
                                    <i class="fas fa-plus-circle"></i> {{ __('Add Property') }}
                                </a>
                            @endif
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse justify-content-end d-sm-none" id="navbarSupportedContent">
            {!!
                Menu::renderMenuLocation('main-menu', [
                    'options' => ['class' => 'navbar-nav justify-content-end'],
                    'view'    => 'main-menu',
                ])
            !!}

            @if (is_plugin_active('real-estate'))
                <a class="btn btn-primary add-property" href="{{ route('public.account.properties.index') }}">
                    <i class="fas fa-plus-circle"></i> {{ __('Add Property') }}
                </a>
            @endif

            <div>
                @if (is_plugin_active('real-estate'))
                    @php $currencies = get_all_currencies(); @endphp
                    @if (count($currencies) > 1)
                        <div class="choose-currency">
                            <span>{{ __('Currency') }}: </span>
                            @foreach ($currencies as $currency)
                                <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>&nbsp;
                            @endforeach
                        </div>
                    @endif
                @endif
                {!! Theme::partial('language-switcher') !!}
                @if (is_plugin_active('real-estate'))
                    <ul class="topbar-items">
                        @if (auth('account')->check())
                            <li class="login-item"><a href="{{ route('public.account.dashboard') }}" rel="nofollow"><i class="fas fa-user"></i> <span>{{ auth('account')->user()->getFullName() }}</span></a></li>
                            <li class="login-item"><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" rel="nofollow"><i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}</a></li>
                        @else
                            <li class="login-item">
                                <a href="{{ route('public.account.login') }}"><i class="fas fa-sign-in-alt"></i>  {{ __('Login') }}</a>
                            </li>
                        @endif
                    </ul>
                    @if (is_plugin_active('real-estate') && auth('account')->check())
                        <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @php
        $page = Theme::get('page');
    @endphp
    @if (url()->current() == route('public.single') || ($page && $page->template === 'homepage'))
        <div class="home_banner" style="background-image: url({{ theme_option('home_banner') ? RvMedia::getImageUrl(theme_option('home_banner')) : Theme::asset()->url('images/banner.jpg') }})">
            <div class="topsearch">
                @if (theme_option('home_banner_description'))<h1 class="text-center text-white mb-4 banner-text-description">{{ theme_option('home_banner_description') }}</h1>@endif
                @if (is_plugin_active('real-estate'))
                    <form action="{{ route('public.projects') }}" method="GET" id="frmhomesearch">
                        <div class="typesearch" id="hometypesearch">
                            <a href="javascript:void(0)" class="active" rel="project" data-url="{{ route('public.projects') }}">{{ __('Projects') }}</a>
                            <a href="javascript:void(0)" rel="sale" data-url="{{ route('public.properties') }}">{{ __('Sale') }}</a>
                            <a href="javascript:void(0)" rel="rent" data-url="{{ route('public.properties') }}">{{ __('Rent') }}</a>
                        </div>
                        <div class="input-group input-group-lg">
                            <input type="hidden" name="type" value="project" id="txttypesearch">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-search"></i></span>
                            </div>
                            <div class="keyword-input">
                                <input type="text" class="form-control" name="k" placeholder="{{ __('Enter keyword...') }}" id="txtkey" autocomplete="off">
                                <div class="spinner-icon">
                                    <i class="fas fa-spin fa-spinner"></i>
                                </div>
                            </div>
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-location"></i></span>
                            </div>
                            <div class="location-input">
                                <input type="hidden" name="city_id">
                                <input class="select-city-state form-control" name="location" value="{{ request()->input('location') }}" placeholder="{{ __('City, State') }}" autocomplete="off">
                                <div class="spinner-icon">
                                    <i class="fas fa-spin fa-spinner"></i>
                                </div>
                                <div class="suggestion">

                                </div>
                            </div>
                            <div class="input-group-append search-button-wrapper">
                                <button class="btn btn-orange" type="submit">{{ __('Search') }}</button>
                            </div>

                            <div class="advanced-search d-none d-sm-block">
                                <a href="#" class="advanced-search-toggler">{{ __('Advanced') }} <i class="fas fa-caret-down"></i></a>
                                <div class="advanced-search-content property-advanced-search">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="select--arrow">
                                                    <select name="bedroom" id="select-bedroom" class="form-control">
                                                        <option value="">{{ __('Bedrooms') }}</option>
                                                        @for($i = 1; $i < 5; $i++)
                                                            <option value="{{ $i }}" @if (request()->input('bedroom') == $i) selected @endif>{{ $i }} {{ $i == 1 ? __('room') : __('rooms') }}</option>
                                                        @endfor
                                                        <option value="5" @if (request()->input('bedroom') == 5) selected @endif>{{ __('5+ rooms') }}</option>
                                                    </select>
                                                    <i class="fas fa-angle-down"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="select--arrow">
                                                    <select name="bathroom" id="select-bathroom" class="form-control">
                                                        <option value="">{{ __('Bathrooms') }}</option>
                                                        @for($i = 1; $i < 5; $i++)
                                                            <option value="{{ $i }}" @if (request()->input('bathroom') == $i) selected @endif>{{ $i }} {{ $i == 1 ? __('room') : __('rooms') }}</option>
                                                        @endfor
                                                        <option value="5" @if (request()->input('bathroom') == 5) selected @endif>{{ __('5+ rooms') }}</option>
                                                    </select>
                                                    <i class="fas fa-angle-down"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="select--arrow">
                                                    <select name="floor" id="select-floor" class="form-control">
                                                        <option value="">{{ __('Floors') }}</option>
                                                        @for($i = 1; $i < 5; $i++)
                                                            <option value="{{ $i }}" @if (request()->input('floor') == $i) selected @endif>{{ $i }} {{ $i == 1 ? __('floor') : __('floors') }}</option>
                                                        @endfor
                                                        <option value="5" @if (request()->input('floor') == 5) selected @endif>{{ __('5+ floors') }}</option>
                                                    </select>
                                                    <i class="fas fa-angle-down"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="advanced-search-content project-advanced-search">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="select--arrow">
                                                    <select name="category_id" id="select-category" class="form-control">
                                                        <option value="">{{ __('Category') }}</option>
                                                        @foreach(app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id') as $categoryId => $categoryName)
                                                            <option value="{{ $categoryId }}" @if (request()->input('category_id') == $categoryId) selected @endif>{{ $categoryName }}</option>
                                                        @endforeach
                                                    </select>
                                                    <i class="fas fa-angle-down"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" name="min_price" class="form-control" id="min_price" placeholder="{{ __('Price from') }}" value="{{ request()->input('min_price') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" name="max_price" class="form-control" id="max_price" placeholder="{{ __('Price to') }}" value="{{ request()->input('max_price') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="listsuggest">

                        </div>
                    </form>
                @endif
            </div>
        </div>
        </div>
    @endif
</header>
