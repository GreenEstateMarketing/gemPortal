{{--
    Layout for "legal"-style content pages (Privacy Policy, Terms, etc.)
    that should render in the home-page-new (navy/gold) design instead of
    the old bgheadproject/scontent look used by the "default" template.

    Same shell as layouts/default.blade.php (inner-header + content +
    footer) - the visual difference comes entirely from the page content
    itself, which is expected to be a shortcode (e.g. [gem-privacy-policy])
    that renders a fully self-styled partial under partials/home-page-new/.
--}}
{!! Theme::partial('inner-header') !!}
<div id="app">
    {!! Theme::content() !!}
</div>

{!! Theme::partial('footer') !!}
