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


<!-- end transition-overlay -->
<div class="side-navigation">
    <div class="menu">
        {!!
                      Menu::renderMenuLocation('main-menu', [
                          'options' => ['class' => ''],
                                    'view'    => 'main-menu',
                      ])
            !!}
    </div>
<!-- end menu -->
    <div class="side-content">
        <figure>
            @if (theme_option('logo'))
                <a class="navbar-brand" href="{{ route('public.single') }}">
                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                         class="logo" height="40" alt="{{ theme_option('site_name') }}">
                </a>
            @endif
        </figure>
        <p>By aiming to take the life quality to an upper level with the whole realized Projects, GEM continues to be the address of luxury.</p>
        <ul class="gallery">
            <li><a href="images/gallery-thumb01.jpg" data-fancybox><img src="{{ Theme::asset()->url('images/gallery-thumb01.jpg') }}" alt="Image"></a></li>
            <li><a href="images/gallery-thumb02.jpg" data-fancybox><img src="{{ Theme::asset()->url('images/gallery-thumb02.jpg') }}" alt="Image"></a></li>
            <li><a href="images/gallery-thumb03.jpg" data-fancybox><img src="{{ Theme::asset()->url('images/gallery-thumb03.jpg') }}" alt="Image"></a></li>
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
        <small>{{ theme_option('copyright') }}</small> </div>
    <!-- end side-content -->
</div>
<!-- end side-navigation -->
<nav class="navbar sticky-top inner-header">
    <div class="container">
        <!-- Sticky Menu -->

            <div id="menu-ticky-top" class="menu" style="display: flex;align-items: center">
                <a href="index.html">
                    @if (theme_option('logo'))
                        <a  href="{{ route('public.single') }}">
                            <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                 class="logo" height="40" alt="{{ theme_option('site_name') }}">
                        </a>
                    @endif
                </a>
                {!!
                          Menu::renderMenuLocation('main-menu', [
                              'options' => ['class' => 'stick-list'],
                                        'view'    => 'main-menu',
                          ])
                !!}
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
            <!-- end logo -->

            <!-- end hamburger -->

        <!-- end upper-side -->

        <!-- end menu -->
    </div>
    <!-- end container -->
</nav>
<!-- end navbar -->
@php
    $page = Theme::get('page');
@endphp

@if (url()->current() == route('public.single') || ($page && $page->template === 'homepage'))
<header class="slider">
    <div class="slider-container">
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
                            <input type="text" class="form-control" name="k" placeholder="{{ __('Enter keyword (House,Office) ') }}" id="txtkey" autocomplete="off">
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
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
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
        <div class="swiper-wrapper">

            <!-- end swiper-slide -->
            <div class="swiper-slide" data-background="{{ Theme::asset()->url('images/slide01.jpg') }}" data-stellar-background-ratio="1.15">

                <!-- end container -->
            </div>

            <!-- end swiper-slide -->

            <!-- end swiper-slide -->
           <!-- <div class="swiper-slide" data-background="{{ Theme::asset()->url('images/slide02.jpg') }}" data-stellar-background-ratio="1.15">


            </div> -->

            <!-- end swiper-slide -->
        </div>
        <!-- Add Pagination -->
        <div class="inner-elements">
            <div class="container">
               <!-- <div class="pagination"></div>

                <div class="button-prev"><i class="fas fa-angle-left"></i></div>

                <div class="button-next"><i class="fas fa-angle-right"></i></div> -->
                <!-- end button-next -->

                <!-- end social-media -->
            </div>
            <!-- end container -->
        </div>
        <!-- end inner-elements -->
    </div>
    <!-- end slider-container -->
</header>
@endif
