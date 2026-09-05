{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/nav-auth.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/nav-auth', ['suffix' => '...']) !!}
    Included from:  home-page-new/header.blade.php, twice - once for the always-visible
                     desktop .main-nav__actions bar, once for the mobile drawer menu
                     (.main-nav__actions is display:none below 860px with nothing
                     replacing it, so mobile visitors had no way to log in/register
                     or reach their account menu at all).

    $suffix must differ between the two call sites (e.g. "desktop" / "mobile") -
    it's appended to element ids (the dropdown toggle, the logout form) so
    rendering this twice on the same page doesn't produce duplicate ids.
--}}
@if (auth('account')->check())
    <div class="dropdown user-dropdown">
        <a class="dropdown-toggle user-dropdown__toggle" href="#" id="navbarDropdownMenuLink-{{ $suffix }}"
            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="{{ auth('account')->user()->image_path ? Storage::url(auth('account')->user()->image_path) : auth('account')->user()->avatar_url }}"
                class="user-dropdown__avatar" alt="">
            <span>{{ auth('account')->user()->getFullName() }}</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink-{{ $suffix }}">
            <a class="dropdown-item" href="{{ route('public.account.dashboard') }}">Dashboard</a>
            <a class="dropdown-item" href="{{ route('public.account.settings') }}">Edit Profile</a>
            <form id="logout-form-account-{{ $suffix }}" action="{{ route('public.account.logout') }}"
                method="POST" style="display: none;">
                @csrf
            </form>
            <a class="dropdown-item" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form-account-{{ $suffix }}').submit();">Log
                Out</a>
        </div>
    </div>
@elseif (auth('member')->check())
    <div class="dropdown user-dropdown">
        <a class="dropdown-toggle user-dropdown__toggle" href="#" id="navbarDropdownMenuLink-member-{{ $suffix }}"
            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span>{{ auth('member')->user()->full_name }}</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink-member-{{ $suffix }}">
            <a class="dropdown-item" href="{{ route('member.dashboard') }}">Dashboard</a>
            <a class="dropdown-item" href="{{ route('member.settings') }}">Edit Profile</a>
            <form id="logout-form-member-{{ $suffix }}" action="{{ route('public.member.logout') }}"
                method="POST" style="display: none;">
                @csrf
            </form>
            <a class="dropdown-item" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form-member-{{ $suffix }}').submit();">Log
                Out</a>
        </div>
    </div>
@else
    <a href="{{ route('member.login') }}" class="btn-login">Login</a>
    <a href="{{ route('member.register') }}" class="btn-register">Register</a>
@endif
