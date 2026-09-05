{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/meet-agents.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/meet-agents') !!}
    Included from:  layouts/homepagenew.blade.php, between property-categories and the footer partials

    Shows the top 3 agents (re_accounts) by number of properties listed
    (morphMany 'properties' relation via author_id/author_type). Right now
    there's only ONE agent in the whole database with any properties
    ("Helen Keller", 3 properties) - the grid below is built to show
    however many exist (1-3), so it'll fill in on its own as more agents
    with listings are added. No code change needed for that.

    DATA GAPS (read before touching content):
    - re_accounts has no "job title" field (the design's "Senior Property
      Consultant" style line) - every card falls back to the same generic
      "Property Consultant" label. Add a real column/admin field later if
      you want per-agent titles.
    - There's no "specialty" field either. Derived instead from the actual
      categories of each agent's own properties (up to 2, most properties
      first) - e.g. "Commercial Plot & Flat" - so it's real data, just not
      hand-written copy. Falls back to "Real Estate Properties" if an
      agent somehow has properties with no category set.

    "View All Agents" and each card go to route('public.agent.list') /
    route('public.agent', $agent->username) - both already existing routes,
    not new ones.
--}}
@php
    $featuredAgents = app(\Botble\RealEstate\Repositories\Interfaces\AccountInterface::class)
        ->getModel()
        ->withCount('properties')
        ->having('properties_count', '>', -1)
        ->orderByDesc('properties_count')
        ->limit(3)
        ->get();
@endphp
@if ($featuredAgents->isNotEmpty())
    <section class="meet-agents">
        <div class="container meet-agents__inner">

            <div class="meet-agents__header">
                <div>
                    <span class="meet-agents__eyebrow">{{ __('Our Professionals') }}</span>
                    <h2 class="meet-agents__heading">
                        {{ __('Meet Our') }} <span class="meet-agents__heading--accent">{{ __('Expert Agents.') }}</span>
                    </h2>
                    <p class="meet-agents__text">{{ __('Get professional advice from experienced real estate experts.') }}</p>
                </div>

                <a href="{{ route('public.agent.list') }}" class="meet-agents__view-all">
                    {{ __('View All Agents') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            {{-- Column count matches however many cards actually exist (1-3)
                 rather than always reserving 3 - with only one real agent in
                 the database right now, a fixed 3-column grid would leave a
                 single narrow card with a lot of empty space beside it. Set
                 as a custom property (not grid-template-columns directly) so
                 the mobile media query below can still force a single column
                 regardless - an inline grid-template-columns would beat any
                 stylesheet rule, media query included, since inline styles
                 always outrank external CSS for the same property. --}}
            <div class="meet-agents__grid" style="--meet-agents-count: {{ min($featuredAgents->count(), 3) }};">
                @foreach ($featuredAgents as $agent)
                    @php
                        $agentSpecialty = $agent
                            ->properties()
                            ->with('category')
                            ->get()
                            ->pluck('category.name')
                            ->filter()
                            ->unique()
                            ->take(2)
                            ->implode(' & ');

                        $agentWhatsapp = preg_replace('/\D/', '', (string) $agent->phone);
                    @endphp
                    <div class="meet-agents__card">
                        <a href="{{ route('public.agent', $agent->username) }}" class="meet-agents__card-photo-link">
                            <img src="{{ $agent->image_path ? Storage::url($agent->image_path) : $agent->avatar_url }}"
                                alt="{{ $agent->getFullName() }}" class="meet-agents__card-photo">
                        </a>

                        <div class="meet-agents__card-body">
                            <span class="meet-agents__card-role">{{ __('Property Consultant') }}</span>

                            <h3 class="meet-agents__card-name">
                                <a href="{{ route('public.agent', $agent->username) }}">{{ $agent->getFullName() }}</a>
                            </h3>

                            <p class="meet-agents__card-specialty">
                                {{ $agentSpecialty ?: __('Real Estate Properties') }}
                            </p>

                            <div class="meet-agents__card-actions">
                                @if ($agent->phone)
                                    <a href="tel:{{ $agent->phone }}" class="meet-agents__card-action" title="{{ __('Call') }}">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                @endif
                                @if ($agent->email)
                                    <a href="mailto:{{ $agent->email }}" class="meet-agents__card-action" title="{{ __('Email') }}">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                @endif
                                @if ($agentWhatsapp)
                                    <a href="https://wa.me/{{ $agentWhatsapp }}" target="_blank" rel="noopener"
                                        class="meet-agents__card-action" title="{{ __('WhatsApp') }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
@endif
