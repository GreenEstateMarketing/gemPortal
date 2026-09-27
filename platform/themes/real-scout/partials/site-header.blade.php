{{--
    Shared site navbar, included via {!! Theme::partial('site-header') !!} from:
    - partials/header.blade.php and partials/inner-header.blade.php (every public page)
    - plugins/real-estate::member.layouts.member_skeleton (member dashboard)
    - plugins/real-estate::account.layouts.skeleton (agent dashboard)

    One markup/CSS/JS source for all three contexts (Theme::partial() resolves
    the active theme from a persisted setting, not from an active Theme::scope(),
    so it works fine from the plugin-owned dashboard skeletons too).

    Nav links are hardcoded (Projects/Properties/Agents/Add Property/Wanted) to
    match what the old member/account dashboard headers already hardcoded -
    the DB-configurable "main-menu" Menu location has different, unrelated
    seeded links (News/Careers/Contact) and no route for Agents/Wanted, so it
    isn't used here. "Add Property" keeps the existing guard-aware routing
    (member wizard / account wizard / guest wizard) copied from those headers.

    Uses the site's real logo asset (theme_option('logo'), same as every
    other header) rather than a hand-drawn mark, since that file already is
    the icon + "GEM" + tagline lockup.

    No Bootstrap-JS dependency (dropdown/hamburger use plain classList
    toggles in site-header.js) because the two dashboard skeletons don't
    reliably load Bootstrap's JS bundle the way theme pages do.
--}}
<header class="gem-header" id="gem-site-header">
    <div class="gem-header__inner">
        <a class="gem-header__brand" href="{{ route('public.single') }}">
            @if (theme_option('logo'))
                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}">
            @else
                <span class="gem-header__brand-text">{{ theme_option('site_title') }}</span>
            @endif
        </a>

        <button type="button" class="gem-header__toggle" id="gemHeaderToggle" aria-label="{{ __('Toggle navigation') }}" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="gem-header__nav" id="gemHeaderNav">
            <ul class="gem-header__links">
                <li><a href="{{ route('public.projects') }}">{{ __('Projects') }}</a></li>
                <li><a href="{{ route('public.properties') }}">{{ __('Properties') }}</a></li>
                <li><a href="/agents">{{ __('Agents') }}</a></li>
                <li>
                    <a href="{{ auth('member')->check() ? route('public.member.properties.wizard.show') : (auth('account')->check() ? route('public.account.properties.wizard.show') : route('general-property-wizard.show')) }}">
                        <svg class="gem-header__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
                        {{ __('Add Property') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('wanted') }}">
                        <svg class="gem-header__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        {{ __('Wanted') }}
                    </a>
                </li>

                @if (auth('account')->check() || auth('member')->check())
                    @php
                        $gemHeaderGuard = auth('account')->check() ? 'account' : 'member';
                        $gemHeaderUser = auth($gemHeaderGuard)->user();
                        $gemHeaderName = $gemHeaderGuard === 'member' ? $gemHeaderUser->full_name : $gemHeaderUser->getFullName();
                        $gemHeaderAvatar = $gemHeaderGuard === 'account' && $gemHeaderUser->image_path
                            ? url('storage/' . $gemHeaderUser->image_path)
                            : $gemHeaderUser->avatar_url;
                        $gemHeaderDashboard = $gemHeaderGuard === 'member' ? route('member.dashboard') : route('public.account.dashboard');
                        $gemHeaderSettings = $gemHeaderGuard === 'member' ? route('member.settings') : route('public.account.settings');
                        $gemHeaderLogout = $gemHeaderGuard === 'member' ? route('public.member.logout') : route('public.account.logout');
                    @endphp
                    <li class="gem-header__account">
                        <button type="button" class="gem-header__account-toggle" id="gemHeaderAccountToggle" aria-expanded="false">
                            <img src="{{ $gemHeaderAvatar }}" alt="{{ $gemHeaderName }}">
                            <span>{{ $gemHeaderName }}</span>
                        </button>
                        <div class="gem-header__account-menu" id="gemHeaderAccountMenu">
                            <a href="{{ $gemHeaderDashboard }}">{{ __('Dashboard') }}</a>
                            <a href="{{ $gemHeaderSettings }}">{{ __('Edit Profile') }}</a>
                            <form action="{{ $gemHeaderLogout }}" method="POST" id="gemHeaderLogoutForm">
                                @csrf
                                <button type="submit">{{ __('Log Out') }}</button>
                            </form>
                        </div>
                    </li>
                @else
                    <li>
                        <a class="gem-header__login" href="{{ route('member.login') }}">
                            <svg class="gem-header__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                            {{ __('Login') }}
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</header>
