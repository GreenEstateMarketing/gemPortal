{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/about-us.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/about-us') !!}
    Included from:  layouts/homepagenew.blade.php, between how-it-works and the footer partials

    IMAGE: home-page-new/about-us-bg.jpg is a placeholder (reusing an existing
    theme banner) so the section isn't broken while there's no real asset yet.
    Swap that file for the actual photo and this partial needs no changes.

    "Learn More About Us" links to the existing "About us" CMS page - looked
    up by name, then resolved to a URL via its Slug row. Page itself has no
    "url" accessor (checked platform/packages/page/src/Models/Page.php) - any
    ->url access that appears to work is a stray attribute some unrelated
    code path happened to set on a cached instance, not something to rely on.
--}}
@php
    $aboutUsPage = app(\Botble\Page\Repositories\Interfaces\PageInterface::class)->getFirstBy(['name' => 'About us']);
    $aboutUsSlug = $aboutUsPage
        ? app(\Botble\Slug\Repositories\Interfaces\SlugInterface::class)->getFirstBy([
            'reference_id' => $aboutUsPage->id,
            'reference_type' => \Botble\Page\Models\Page::class,
        ])
        : null;
    $aboutUsUrl = $aboutUsSlug ? url($aboutUsSlug->key) : '#';
@endphp

<section class="about-us" style="background-image: url('{{ Theme::asset()->url('images/home-page-new/about-us-bg.png') }}')">
    <div class="about-us__overlay"></div>
    <div class="container about-us__inner">
        <span class="about-us__eyebrow">{{ __('About GEM Properties') }}</span>

        <h2 class="about-us__heading">
            {{ __('Your Property.') }}<br>
            <span class="about-us__heading--accent">{{ __('Our Expertise.') }}</span>
        </h2>

        <p class="about-us__text">
            {{ __('We help individuals, families and investors make better property decisions through trusted listings, professional agents and local market knowledge.') }}
        </p>

        <a href="{{ $aboutUsUrl }}" class="about-us__cta">
            {{ __('Learn More About Us') }} <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>
