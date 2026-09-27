@php
  Theme::set('page', $page);
@endphp

@if ($page->template == 'default')
    <div class="bgheadproject hidden-xs">
        <div class="description">
            <div class="container-fluid w90">
                <h1 class="text-center">{{ $page->name }}</h1>
                {!! Theme::partial('breadcrumb') !!}
            </div>
        </div>
    </div>
    <div class="container padtop50">
        <div class="row">
            <div class="col-sm-12">
                <div class="scontent">
                    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content), $page) !!}
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
@elseif ($page->template == 'legal')
    {{--
        Home-page-design (navy/gold) wrapper for legal/content pages
        (Terms & Conditions, Privacy Policy, FAQ, Shipping/Delivery Policy,
        Disclaimer). The hero is generic - driven by $page->name - so it
        works whether $page->content is plain admin-authored prose (Terms
        & Conditions) or a shortcode like [gem-privacy-policy] that expands
        to a fully custom section. Keeping the hero here (outside the
        clean()-purified/shortcode-expanded content below) means a
        shortcode-driven page never nests its hero inside the auto-<p>
        that clean()'s AutoFormat.AutoParagraph wraps raw shortcode text
        in - see privacy-policy.css's #app > p:has(+ .legal-hero) rule
        for the same issue when a shortcode page used to render its own
        hero inline (kept as a harmless defensive fallback).
    --}}
    <section class="legal-hero">
        <div class="container legal-hero__inner">
            <span class="legal-hero__eyebrow">{{ __('Legal') }}</span>
            {{-- Playfair Display's "&" glyph is a heavily stylized ligature -
                 swap to the plain body font for any title that contains one
                 (e.g. "Terms & Conditions") so the heading stays simple. --}}
            <h1 class="legal-hero__heading @if (str_contains($page->name, '&')) legal-hero__heading--plain @endif">{{ $page->name }}</h1>
            <p class="legal-hero__breadcrumb">
                <a href="{{ route('public.index') }}">{{ __('Home') }}</a>
                <span>/</span>
                {{ $page->name }}
            </p>
        </div>
    </section>
    <section class="legal-content">
        <div class="container legal-content__inner">
            {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content), $page) !!}
        </div>
    </section>
@else
    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content), $page) !!}
@endif
