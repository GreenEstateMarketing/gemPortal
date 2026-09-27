{{--
    Split-screen auth page shell: left hero (background photo + headline +
    feature bullets), right white card. Used by every login/register/
    forgot-password/reset-password page (both member and agent) so they all
    share one visual design.

    Each auth blade @includes this at the top (via
    Theme::partial('auth-shell-open', [...])) and auth-shell-close at the
    bottom, then puts its own <form> in between - these pages don't use
    @extends, they're rendered directly via Theme::scope(...)->render()
    into layouts/default.blade.php, so a wrap-style Blade component isn't a
    natural fit here.

    Params: $heading, $headingAccent, $description, $features (array of
    ['title' => ..., 'subtitle' => ...]), $cardTitle, $cardSubtitle.

    The left panel uses a designed dark navy/gold gradient rather than a
    photo - there's no on-brand luxury-exterior photo asset in this theme
    (checked images/slide01.jpg, banner01.jpg, home-page-new/*: none match),
    and fabricating/downloading a stock photo isn't appropriate here.
--}}
@php
    $features = $features ?? [];
@endphp
<div class="gem-auth">
    <div class="gem-auth__hero">
        <div class="gem-auth__hero-content">
            <span class="gem-auth__rule"></span>
            <h1 class="gem-auth__heading">{{ $heading }} <span>{{ $headingAccent }}</span></h1>
            @if (!empty($description))
                <p class="gem-auth__description">{{ $description }}</p>
            @endif

            @if (count($features))
                <ul class="gem-auth__features">
                    @foreach ($features as $feature)
                        <li>
                            <span class="gem-auth__feature-icon">{!! $feature['icon'] ?? '' !!}</span>
                            <span>
                                <strong>{{ $feature['title'] }}</strong>
                                <small>{{ $feature['subtitle'] }}</small>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="gem-auth__panel">
        <div class="gem-auth__card">
            <a class="gem-auth__brand" href="{{ route('public.single') }}">
                @if (theme_option('logo'))
                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}">
                @endif
            </a>

            <h2 class="gem-auth__card-title">{{ $cardTitle }}</h2>
            @if (!empty($cardSubtitle))
                <p class="gem-auth__card-subtitle">{{ $cardSubtitle }}</p>
            @endif
