{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/cta-move.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/cta-move') !!}
    Included from:  layouts/homepagenew.blade.php, between testimonials and site-footer

    IMAGE: home-page-new/cta-move-bg.jpg is a placeholder (reusing an
    existing project image) - swap that file for the real photo and this
    partial needs no changes.

    "Contact Us" links to the existing "Contact" CMS page, resolved via its
    Slug row the same way about-us.blade.php resolves the About page (Page
    model has no "url" accessor - see the note there for why).
--}}
@php
    $contactPage = app(\Botble\Page\Repositories\Interfaces\PageInterface::class)->getFirstBy(['name' => 'Contact']);
    $contactSlug = $contactPage
        ? app(\Botble\Slug\Repositories\Interfaces\SlugInterface::class)->getFirstBy([
            'reference_id' => $contactPage->id,
            'reference_type' => \Botble\Page\Models\Page::class,
        ])
        : null;
    $contactUrl = $contactSlug ? url($contactSlug->key) : '#';
@endphp
<section class="cta-move" style="background-image: url('{{ Theme::asset()->url('images/home-page-new/cta-move-bg.jpg') }}')">
    <div class="cta-move__overlay"></div>
    <div class="container cta-move__inner">
        <span class="cta-move__eyebrow">{{ __('Ready To Make Your Move?') }}</span>

        <h2 class="cta-move__heading">
            {{ __('Your Next Property') }}<br>
            <span class="cta-move__heading--accent">{{ __('Starts Here.') }}</span>
        </h2>

        <p class="cta-move__text">
            {{ __("Whether you're buying, renting, selling or investing, GEM Properties is here to help.") }}
        </p>

        <div class="cta-move__actions">
            <a href="{{ route('public.properties') }}" class="cta-move__btn-primary">
                {{ __('Browse Properties') }} <i class="fas fa-arrow-right"></i>
            </a>
            {{-- .btn-outline is the shared hero-style outline button already
                 defined in header.css - reused as-is, matches this design's
                 "Contact Us" look exactly. --}}
            <a href="{{ $contactUrl }}" class="btn-outline">
                {{ __('Contact Us') }}
            </a>
        </div>
    </div>
</section>
