{{--
    Bottom CTA banner, structurally similar to
    partials/home-page-new/cta-move.blade.php but flat navy (no background
    photo) per the design - kept as its own partial/CSS rather than forking
    cta-move since the visual treatment differs (solid color vs photo+overlay).

    $contactUrl (Contact CMS page) is resolved once in
    PublicController::getProjects() and shared with spotlight-section too.
--}}
<section class="projects-cta">
    <div class="projects-cta__inner">
        <div>
            <span class="projects-cta__eyebrow">{{ __('Ready To Find Your Next Project?') }}</span>
            <h2 class="projects-cta__heading">{{ __("Let's find the right place for you.") }}</h2>
        </div>
        <a href="{{ $contactUrl }}" class="projects-cta__btn">
            {{ __('Talk to Us') }} <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>
