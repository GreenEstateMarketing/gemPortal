{{--
    Single "Featured Project" spotlight block for $spotlightProject
    (PublicController::getProjects() - first is_featured=true project,
    falling back to the newest project if none are featured). The 4 pills
    are: unit count (number_flat) + up to 3 of the project's real `features`
    (re_project_features), each using Feature::$icon the same way
    project.blade.php already does for its own features list - no new
    fields needed for "Parking/Security/Green Area" etc, whatever features
    were picked for this project in admin just show up here.
--}}
@if ($spotlightProject)
    <section class="projects-spotlight">
        <div class="projects-spotlight__inner">
            <div class="projects-spotlight__image-wrap">
                <img src="{{ RvMedia::getImageUrl($spotlightProject->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                     alt="{{ $spotlightProject->name }}" class="projects-spotlight__image" loading="lazy">
            </div>
            <div class="projects-spotlight__content">
                <span class="projects-spotlight__eyebrow">{{ __('Premium Project') }}</span>
                <h2 class="projects-spotlight__heading">{{ $spotlightProject->name }}</h2>
                @if ($spotlightProject->city && $spotlightProject->city->name)
                    <p class="projects-spotlight__location">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $spotlightProject->city->name }}@if ($spotlightProject->city->state && $spotlightProject->city->state->name && $spotlightProject->city->state->name !== $spotlightProject->city->name), {{ $spotlightProject->city->state->name }}@endif
                    </p>
                @endif
                @if ($spotlightProject->description)
                    <p class="projects-spotlight__text">{{ Str::limit(strip_tags($spotlightProject->description), 180) }}</p>
                @endif

                <div class="projects-spotlight__pills">
                    @if ($spotlightProject->number_flat)
                        <div class="projects-spotlight__pill">
                            <i class="fas fa-home"></i> {{ number_format($spotlightProject->number_flat) }} {{ __('Units') }}
                        </div>
                    @endif
                    @foreach ($spotlightProject->features->take(3) as $feature)
                        <div class="projects-spotlight__pill">
                            <i class="{{ $feature->icon ?: 'fas fa-check-circle' }}"></i> {{ $feature->name }}
                        </div>
                    @endforeach
                </div>

                <a href="{{ $contactUrl }}" class="projects-spotlight__btn">
                    {{ __('Contact Us') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
@endif
