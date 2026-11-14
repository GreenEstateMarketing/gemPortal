{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/our-parent-companies.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/our-parent-companies') !!}
    Included from:  layouts/homepagenew.blade.php, between usp-showcase and how-it-works

    AL-HAQ Builders & Developers has no website yet, so its logo is plain
    text/image with no link. Add an href once one exists.
--}}
<section class="parent-companies">
    <div class="container parent-companies__inner">
        <span class="parent-companies__heading">{{ __('Our Parent Companies') }}</span>

        <div class="parent-companies__logos">
            <a href="https://updl.org" target="_blank" rel="noopener" class="parent-companies__logo">
                <img src="{{ Theme::asset()->url('images/home-page-new/parent-updl.jpg') }}" alt="UPDL - Urban Planning & Design (PVT) Ltd.">
            </a>

            <span class="parent-companies__divider"></span>

            <span class="parent-companies__logo parent-companies__logo--static">
                <img src="{{ Theme::asset()->url('images/home-page-new/parent-alhaq.jpg') }}" alt="AL-HAQ Builders & Developers">
            </span>
        </div>
    </div>
</section>
